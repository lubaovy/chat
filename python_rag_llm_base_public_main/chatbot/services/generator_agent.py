from python_rag_llm_base_public_main.chatbot.utils.retriever import Retriever
from python_rag_llm_base_public_main.chatbot.utils.answer_generator import AnswerGenerator
from python_rag_llm_base_public_main.chatbot.utils.smart_question_handler import SmartQuestionHandler
from python_rag_llm_base_public_main.app.config import settings

class GeneratorAgent:
    def __init__(self, path_vector_store: str, llm):
        # Sinh câu trả lời đầu tiên
        self.retriever = Retriever(settings.LLM_NAME).set_retriever(path_vector_store)
        self.answer_generator = AnswerGenerator(llm)
        self.question_handler = SmartQuestionHandler(llm)

    async def run(self, question: str):
        processed = self.question_handler.process(question)
        enriched_question = processed["enriched_question"]
        analysis = processed["analysis"]
        insights = processed["insights"]
        # dynamic_k = self.question_handler.analyzer.infer_top_k(analysis)
        
        documents = self.retriever.get_documents(enriched_question, num_doc=30)
        if not documents:
            return {"generation": None,
                    "documents": [],
                    "error": "No relevant documents found.",
                    "enriched_question": enriched_question,
                    "error_reason": processed.get("error_reason", None),
                    "issue_detected": processed.get("issue_detected", False)
                }

        context = "\n\n".join(doc.page_content for doc in documents)
        generation = await self.answer_generator.get_chain().ainvoke({"question": enriched_question, "context": context})

        return {
            "generation": generation,
            "documents": [{"page_content": doc.page_content} for doc in documents],
            "enriched_question": enriched_question,
            "insights": insights,
            "analysis": analysis,
            "error_reason": processed.get("error_reason", None),     # ✅ thêm
            "issue_detected": processed.get("issue_detected", False)
        }
