from python_rag_llm_base_public_main.chatbot.utils.llm import LLM  # noqa: I001
from python_rag_llm_base_public_main.chatbot.utils.retriever import Retriever
from python_rag_llm_base_public_main.chatbot.utils.document_grader import DocumentGrader
from python_rag_llm_base_public_main.chatbot.utils.answer_generator import AnswerGenerator
from python_rag_llm_base_public_main.chatbot.utils.no_answer_handler import NoAnswerHandler
from python_rag_llm_base_public_main.chatbot.utils.smart_question_handler import SmartQuestionHandler

from langgraph.graph import END, StateGraph, START
from python_rag_llm_base_public_main.chatbot.utils.graph_state import GraphState
from typing import Dict, Any
from langchain.schema import AIMessage

from python_rag_llm_base_public_main.app.config import settings


class FilesChatAgent:
    def __init__(self, path_vector_store: str) -> None:
        self.retriever = Retriever(settings.LLM_NAME).set_retriever(path_vector_store)
        self.llm = LLM().get_llm(settings.LLM_NAME)
        self.document_grader = DocumentGrader(self.llm)
        self.answer_generator = AnswerGenerator(self.llm)
        self.no_answer_handler = NoAnswerHandler(self.llm)
        self.question_handler = SmartQuestionHandler(self.llm)

    def retrieve(self, state: GraphState) -> Dict[str, Any]:
        original_question = state["question"]
        processed = self.question_handler.process(original_question)
        print(f"[PROCESSED RESULT]: {processed}")
        enriched_question = processed["enriched_question"]
        analysis = processed["analysis"]
        insights = processed["insights"]
        error_reason = processed.get("error_reason", "")
        issue_detected = processed.get("issue_detected", False)
        
        dynamic_k = self.question_handler.analyzer.infer_top_k(analysis)
        print(f"[RETRIEVER] ✅ Dynamic top-k = {dynamic_k} cho câu hỏi: {original_question}")


        documents = self.retriever.get_documents(enriched_question, dynamic_k)

        return {
            "documents": documents,
            "question": enriched_question,
            "original_question": original_question,
            "insights": insights,
            "error_reason": error_reason,            # ✅ thêm dòng này
            "issue_detected": issue_detected 
        }

    def generate(self, state: GraphState) -> Dict[str, Any]:
        question = state.get("original_question", state["question"])
        documents = state["documents"]
        context = "\n\n".join(doc.page_content for doc in documents)
        generation = self.answer_generator.get_chain().invoke({"question": question, "context": context})
        return {"generation": generation}

    def decide_to_generate(self, state: GraphState) -> str:
        filtered_documents = state["documents"]

        if not filtered_documents:
            print("---QUYẾT ĐỊNH: KHÔNG CÓ VĂN BẢN LIÊN QUAN ĐẾN CÂU HỎI, BIẾN ĐỔI TRUY VẤN---")
            return "no_document"
        else:
            print("---TẠO CÂU TRẢ LỜI---")
            return "generate"

    def grade_documents(self, state: GraphState) -> Dict[str, Any]:
        question = state["question"]
        documents = state["documents"]

        filtered_docs = []
        for d in documents:
            score = self.document_grader.get_chain().invoke({"question": question, "document": d.page_content})
            grade = score.binary_score
            if grade == "yes":
                print("---CHẤM ĐIỂM: TÀI LIỆU LIÊN QUAN---")
                filtered_docs.append(d)
            else:
                print("---CHẤM ĐIỂM: TÀI LIỆU KHÔNG LIÊN QUAN---")

        return {"documents": filtered_docs, "question": question, "original_question": state.get("original_question"), "insights": state.get("insights", [])}

    def handle_no_answer(self, state: GraphState) -> Dict[str, Any]:
        return {
            "generation": "❌ Không tìm thấy tài liệu liên quan để trả lời câu hỏi này. Vui lòng đặt lại câu hỏi cụ thể hơn.",
            # "reliable": False,
            # "validation_checks": []
        }

    def evaluate_final_answer(self, state: GraphState) -> Dict[str, Any]:
        import re
        from copy import deepcopy

        original_answer = state["generation"]
        current_answer = original_answer
        max_iterations = 3

        all_validation_checks = []
        all_false_details = set()

        for iteration in range(max_iterations):
            re_docs = self.retriever.get_documents(current_answer, int(settings.NUM_DOC))
            re_context = "\n\n".join(doc.page_content for doc in re_docs)

            prompt = f"""
    [KIỂM ĐỊNH HẬU SINH - LẦN {iteration + 1}]

    Nhiệm vụ của bạn là kiểm tra xem tất cả các thông tin trong CÂU TRẢ LỜI có thực sự nằm trong TÀI LIỆU hay không.

    Bạn phải trả lời đúng định dạng dưới đây:

    ---
    TRẢ LỜI:
    TRUE
    HOẶC
    FALSE
    - Chi tiết sai 1: <ghi rõ>
    - Chi tiết sai 2: <ghi rõ>
    - ...
    - Chi tiết sai n: <ghi rõ>
    ---

    TÀI LIỆU:
    {re_context}

    CÂU TRẢ LỜI:
    {current_answer}
    """
            response = self.llm.invoke(prompt)
            response_text = response.content.strip()

            lines = response_text.lower().splitlines()
            answer_line = next((lines[i + 1].strip() for i, l in enumerate(lines) if "trả lời" in l and i + 1 < len(lines)), "")
            passed = answer_line == "true"
            false_details = re.findall(r"- Chi tiết sai \d+: (.+)", response_text)

            all_validation_checks.append({
                "run": iteration + 1,
                "prompt": prompt.strip(),
                "response": response_text,
                "passed": passed,
                "false_details": false_details,
            })

            all_false_details.update(false_details)

            if passed or not false_details:
                break

            fix_prompt = f"""
    Bạn là một trợ lý AI. Dưới đây là một câu trả lời sai vì có các chi tiết không đúng với tài liệu.

    CÂU TRẢ LỜI GỐC:
    {current_answer}

    LỖI CẦN SỬA:
    {chr(10).join(f"- {d}" for d in false_details)}

    Yêu cầu: Viết lại câu trả lời sao cho loại bỏ hoặc điều chỉnh các lỗi sai trên. Giữ nguyên nội dung đúng.

    TRẢ LỜI MỚI:
    """
            fixed = self.llm.invoke(fix_prompt).content.strip()
            current_answer = fixed

        return {
            "generation": current_answer,
            "reliable": all_validation_checks[-1]["passed"],
            "validation_checks": all_validation_checks,
            "false_details_summary": list(all_false_details),
        }

    def get_workflow(self):
        workflow = StateGraph(GraphState)

        workflow.add_node("retrieve", self.retrieve)
        workflow.add_node("grade_documents", self.grade_documents)
        workflow.add_node("generate", self.generate)
        workflow.add_node("handle_no_answer", self.handle_no_answer)
        workflow.add_node("evaluate_final_answer", self.evaluate_final_answer)

        workflow.add_edge(START, "retrieve")
        workflow.add_edge("retrieve", "grade_documents")

        workflow.add_conditional_edges(
            "grade_documents",
            self.decide_to_generate,
            {
                "no_document": "handle_no_answer",
                "generate": "generate",
            },
        )

        workflow.add_edge("generate", "evaluate_final_answer")
        workflow.add_edge("handle_no_answer", END)
        workflow.add_edge("evaluate_final_answer", END)

        return workflow
