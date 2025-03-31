from python_rag_llm_base_public_main.chatbot.utils.llm import LLM  # noqa: I001
from python_rag_llm_base_public_main.chatbot.utils.retriever import Retriever
from python_rag_llm_base_public_main.chatbot.utils.document_grader import DocumentGrader
from python_rag_llm_base_public_main.chatbot.utils.answer_generator import AnswerGenerator
from python_rag_llm_base_public_main.chatbot.utils.no_answer_handler import NoAnswerHandler

from langgraph.graph import END, StateGraph, START
from python_rag_llm_base_public_main.chatbot.utils.graph_state import GraphState
from typing import Dict, Any

from python_rag_llm_base_public_main.app.config import settings


class FilesChatAgent:
    """
    Lớp FilesChatAgent chịu trách nhiệm quản lý quy trình chatbot,
    từ tìm kiếm tài liệu, đánh giá độ liên quan đến tạo câu trả lời và xuất kết quả HTML.
    """

    def __init__(self, path_vector_store: str) -> None:
        """
        Khởi tạo FilesChatAgent với các thành phần chính.

        Args:
            path_vector_store (str): Đường dẫn đến thư mục lưu trữ vector store.
        """
        self.retriever = Retriever(settings.LLM_NAME).set_retriever(path_vector_store)  # Khởi tạo trình tìm kiếm tài liệu
        self.llm = LLM().get_llm(settings.LLM_NAME)  # Khởi tạo mô hình ngôn ngữ
        self.document_grader = DocumentGrader(self.llm)  # Bộ đánh giá tài liệu
        self.answer_generator = AnswerGenerator(self.llm)  # Bộ tạo câu trả lời
        self.no_answer_handler = NoAnswerHandler(self.llm)  # Xử lý trường hợp không có câu trả lời

    def retrieve(self, state: GraphState) -> Dict[str, Any]:
        """
        Tìm kiếm các tài liệu liên quan đến câu hỏi.

        Args:
            state (GraphState): Trạng thái hiện tại chứa câu hỏi.

        Returns:
            dict: Chứa danh sách tài liệu và câu hỏi.
        """
        question = state["question"]
        documents = self.retriever.get_documents(question, int(settings.NUM_DOC))
        return {"documents": documents, "question": question}

    def generate(self, state: GraphState) -> Dict[str, Any]:
        """
        Tạo câu trả lời dựa trên các tài liệu liên quan.

        Args:
            state (GraphState): Trạng thái hiện tại chứa câu hỏi và tài liệu.

        Returns:
            dict: Chứa câu trả lời đã được tạo.
        """
        question = state["question"]
        documents = state["documents"]
        context = "\n\n".join(doc.page_content for doc in documents)  # Ghép nội dung các tài liệu thành một đoạn văn
        generation = self.answer_generator.get_chain().invoke({"question": question, "context": context})
        return {"generation": generation}

    def decide_to_generate(self, state: GraphState) -> str:
        """
        Xác định xem có nên tạo câu trả lời hay không dựa trên tài liệu tìm được.

        Args:
            state (GraphState): Trạng thái hiện tại chứa danh sách tài liệu.

        Returns:
            str: "no_document" nếu không có tài liệu, "generate" nếu có thể tạo câu trả lời.
        """
        filtered_documents = state["documents"]

        if not filtered_documents:
            print("---QUYẾT ĐỊNH: KHÔNG CÓ VĂN BẢN LIÊN QUAN ĐẾN CÂU HỎI, BIẾN ĐỔI TRUY VẤN---")
            return "no_document"
        else:
            print("---QUYẾT ĐỊNH: TẠO CÂU TRẢ LỜI---")
            return "generate"

    def grade_documents(self, state: GraphState) -> Dict[str, Any]:
        """
        Chấm điểm tài liệu để xác định mức độ liên quan.

        Args:
            state (GraphState): Trạng thái hiện tại chứa câu hỏi và danh sách tài liệu.

        Returns:
            dict: Chứa danh sách tài liệu đã lọc và câu hỏi.
        """
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

        return {"documents": filtered_docs, "question": question}

    def handle_no_answer(self, state: GraphState) -> Dict[str, Any]:
        """
        Xử lý trường hợp không tìm thấy câu trả lời phù hợp.

        Args:
            state (GraphState): Trạng thái hiện tại chứa câu hỏi.

        Returns:
            dict: Chứa câu trả lời mặc định hoặc phản hồi phù hợp.
        """
        question = state["question"]
        generation = self.no_answer_handler.get_chain().invoke({"question": question})
        return {"generation": generation}

    def reiterate(self, state: GraphState) -> Dict[str, Any]:
        """
        Lặp lại câu hỏi với câu trả lời đã sinh ra để tiếp tục kiểm tra độ chính xác.

        Args:
            state (GraphState): Trạng thái hiện tại chứa câu hỏi và câu trả lời.

        Returns:
            dict: Câu trả lời mới được dùng làm câu hỏi tiếp theo.
        """
        prev_answer = state["generation"]
        state["iteration"] = state.get("iteration", 0) + 1
        print("Iteration value:", state["iteration"])
        state["question"] = prev_answer
        return state

    def aggregate_results(self, state: GraphState) -> Dict[str, Any]:
        """
        Tổng hợp các kết quả sau 5 lần lặp, chọn câu trả lời tốt nhất hoặc tổng hợp thông tin.

        Args:
            state (GraphState): Trạng thái chứa danh sách các câu trả lời đã được chấm điểm.

        Returns:
            dict: Câu trả lời cuối cùng.
        """
        results = state["results"]  # Danh sách câu trả lời đã sinh ra
         # Ghép nối tất cả các câu trả lời lại với nhau, cách nhau bằng dòng mới
        combined_answers = "\n\n".join(result["answer"] for result in results)
        # Sử dụng LLM để tổng hợp lại câu trả lời từ các kết quả đã có
        aggregated_answer = self.answer_generator.get_chain().invoke({
            "question": state["question"],
            "context": combined_answers
        })
        return {"generation": aggregated_answer}

    def evaluate_final_answer(self, state: GraphState) -> Dict[str, Any]:
        """
        Đánh giá câu trả lời cuối cùng để xác định mức độ chính xác và chỉ ra lỗi nếu có.

        Args:
            state (GraphState): Trạng thái chứa câu trả lời cuối cùng.

        Returns:
            dict: Câu trả lời cuối cùng với đánh giá về độ chính xác.
        """
        final_answer = state["generation"]
        question = state["question"]

        # Sử dụng DocumentGrader để chấm điểm câu trả lời cuối cùng
        evaluation = self.document_grader.get_chain().invoke({
            "question": question,
            "document": final_answer
        })

        score = evaluation.binary_score  # "yes" nếu đúng, "no" nếu sai
        issues = evaluation.issues if "issues" in evaluation else "Không có lỗi rõ ràng."

        if score == "yes":
            confidence = "Câu trả lời chính xác với độ tin cậy cao."
        else:
            confidence = "Câu trả lời có thể chứa sai sót. Dưới đây là các vấn đề tiềm năng:\n" + issues

        return {"generation": final_answer, "confidence": confidence}

    def get_workflow(self):
        """
        Thiết lập luồng xử lý chatbot với vòng lặp 5 lần kiểm tra độ chính xác.

        Returns:
            StateGraph: Đồ thị trạng thái của quy trình chatbot.
        """
        workflow = StateGraph(GraphState)

        workflow.add_node("retrieve", self.retrieve)
        workflow.add_node("grade_documents", self.grade_documents)
        workflow.add_node("generate", self.generate)
        workflow.add_node("handle_no_answer", self.handle_no_answer)
        workflow.add_node("reiterate", self.reiterate)  # Lặp lại câu hỏi
        workflow.add_node("aggregate", self.aggregate_results)  # Tổng hợp kết quả
        workflow.add_node("evaluate_final_answer", self.evaluate_final_answer)  # Đánh giá câu trả lời cuối cùng

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

        workflow.add_conditional_edges(
            "generate",
            lambda state: "reiterate" if state["iteration"] < 5 else "aggregate",
            {
                "reiterate": "reiterate",
                "aggregate": "evaluate_final_answer",
            },
        )
        # workflow.add_edge("reiterate", "retrieve")
        
        workflow.add_edge("evaluate_final_answer", END)

        return workflow

