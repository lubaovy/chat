from python_rag_llm_base_public_main.chatbot.utils.llm import LLM
from python_rag_llm_base_public_main.chatbot.services.generator_agent import GeneratorAgent
from python_rag_llm_base_public_main.chatbot.services.verifier_agent import VerifierAgent
# from python_rag_llm_base_public_main.chatbot.services.composer_agent import ComposerAgent
from python_rag_llm_base_public_main.app.config import settings

import asyncio

class ChatOrchestrator:
    def __init__(self, path_vector_store: str):
        # Điều phối tổng thể
        self.llm = LLM().get_llm(settings.LLM_NAME)
        self.generator = GeneratorAgent(path_vector_store, self.llm)
        self.verifier = VerifierAgent(path_vector_store, self.llm)
        # self.composer = ComposerAgent(self.llm)

    async def run(self, question: str):
        initial = await self.generator.run(question)
        if not initial["generation"]:
            return {"answer": "Không tìm thấy tài liệu phù hợp.",
                    "sources": [],
                    "original_question": question,
                    "error_reason": initial.get("error_reason", None),
                    "issue_detected": initial.get("issue_detected", False),
                    "documents": [],  # thêm cho nhất quán schema trả về
                    "enriched_question": None
                }
        
        enriched_question = initial["enriched_question"]

        verification = await self.verifier.run(
            enriched_question=enriched_question,
            initial_answer=initial["generation"],
            initial_docs=initial["documents"],
            
        )
        
        # Kiểm tra nếu có vòng kiểm định nào failed
        has_failed_validation = any(not check["passed"] for check in verification["validation_checks"])

        # Lấy tài liệu từ các lần passed=True
        # verified_docs = []
        # for check in verification["validation_checks"]:
        #     if check["passed"]:
        #         verified_docs.extend(check["documents"])

        # # Composer sinh câu trả lời cuối cùng
        # final_answer = await self.composer.run(enriched_question, verified_docs)
        return {
            # "answer": final_answer.get("answer", "Không tạo được câu trả lời."),
            # "sources": final_answer.get("sources", []),
            "answer": initial["generation"],
            "sources": [doc.get("metadata", {}).get("source", "Không rõ") for doc in initial["documents"]],
            "documents": initial["documents"],
            "validation_checks": verification["validation_checks"],
            "original_question": question,
            "enriched_question": enriched_question,
            "error_reason": initial.get("error_reason", None),
            "issue_detected": initial.get("issue_detected", False),
            "has_failed_validation": has_failed_validation
        }
    # async def run(self, question: str):
    #     # Truy vấn tài liệu trực tiếp bằng câu hỏi gốc
    #     documents = self.generator.retriever.get_documents(question, num_doc=5)  # hoặc dynamic k

    #     if not documents:
    #         return {
    #             "answer": "Không tìm thấy tài liệu phù hợp.",
    #             "sources": [],
    #             "original_question": question
    #         }

    #     # Tạo context đơn giản
    #     context = "\n\n".join(
    #         doc.page_content if hasattr(doc, "page_content") else doc.get("page_content", "")
    #         for doc in documents
    #     )

    #     # Gọi LLM sinh câu trả lời
    #     generation = await self.generator.answer_generator.get_chain().ainvoke({
    #         "question": question,
    #         "context": context
    #     })

    #     # Trả kết quả
    #     return {
    #         "answer": generation,
    #         "documents": [
    #             {
    #                 "page_content": doc.page_content,
    #                 "metadata": doc.metadata if hasattr(doc, "metadata") else doc.get("metadata", {})
    #             }
    #             for doc in documents
    #         ],
    #         "sources": [
    #             doc.metadata.get("source", "Không rõ") if hasattr(doc, "metadata") else doc.get("metadata", {}).get("source", "Không rõ")
    #             for doc in documents
    #         ],
    #         "original_question": question
    #     }

        # passed_check = verification["validation_checks"][0] if verification["validation_checks"] else None

        # if passed_check and passed_check["passed"]:
        #     return {
        #         "answer": initial["generation"],
        #         "sources": [
        #             doc.get("metadata", {}).get("source", "")
        #             for doc in passed_check["documents"]
        #         ],
        #         "validation_checks": verification["validation_checks"],
        #         "original_question": question,
        #         "error_reason": initial.get("error_reason", None),
        #         "issue_detected": initial.get("issue_detected", False)
        #     }

        # # Nếu không passed
        # return {
        #     "answer": "Không có câu trả lời đáng tin cậy.",
        #     "sources": [],
        #     "validation_checks": verification["validation_checks"],
        #     "original_question": question,
        #     "error_reason": initial.get("error_reason", None),
        #     "issue_detected": initial.get("issue_detected", False)
        # }
    # async def run(self, question: str):
    #     initial = await self.generator.run(question)
    #     if not initial["generation"]:
    #         return {"answer": "Không tìm thấy tài liệu phù hợp.", "sources": [], "original_question": question}
        
    #     enriched_question = initial["enriched_question"]
    #     initial_answer = initial["generation"]
    #     documents = initial["documents"]

    #     verification = await self.verifier.run(
    #         question=enriched_question,
    #         answer=initial_answer,
    #         documents=documents,
    #         num_checks=5
    #     )

    #     return {
    #         "answer": initial_answer,
    #         "enriched_question": enriched_question,
    #         "validation_checks": verification["validation_checks"],
    #         "verified_ratio": f"{sum(1 for c in verification['validation_checks'] if c['passed'])}/5",
    #         "sources": [
    #             doc.get("metadata", {}).get("source", "") for doc in documents
    #         ]
    #     }


