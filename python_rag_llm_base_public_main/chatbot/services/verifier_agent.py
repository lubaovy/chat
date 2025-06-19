from python_rag_llm_base_public_main.app.config import settings
from python_rag_llm_base_public_main.chatbot.utils.retriever import Retriever

class VerifierAgent:
    def __init__(self, path_vector_store: str, llm):
        # Lặp lại kiểm định và sinh câu trả lời mới (5 lần)
        self.retriever = Retriever(settings.LLM_NAME).set_retriever(path_vector_store)
        self.llm = llm

    async def run(self, enriched_question: str, initial_answer: str, initial_docs: list, max_iterations: int = 5):
        import re

        validation_checks = []
        # current_answer = initial_answer
        # current_docs = initial_docs

        for i in range(max_iterations):
            def get_doc_content(doc):
                if hasattr(doc, 'page_content'):
                    return doc.page_content
                elif isinstance(doc, dict) and 'page_content' in doc:
                    return doc['page_content']
                else:
                    raise ValueError(f"Định dạng document không hợp lệ: {type(doc)}")
            context = "\n\n".join(get_doc_content(doc) for doc in initial_docs)

            # 🔍 1. Kiểm định
#             check_prompt = f"""
# Bạn là hệ thống kiểm định câu trả lời lịch sử Việt Nam. Kiểm tra xem CÂU TRẢ LỜI có đúng và đầy đủ nội dung theo TÀI LIỆU cho CÂU HỎI không?

# ---CÂU HỎI---
# {enriched_question}

# ---TÀI LIỆU---
# {context}

# ---CÂU TRẢ LỜI---
# {initial_answer}

# Nếu CÂU TRẢ LỜI phù hợp và không có lỗi, hãy trả lời TRUE.
# Nếu có lỗi, trả lời FALSE và liệt kê lỗi theo dạng:
# - Chi tiết sai 1: ...
# - Chi tiết sai 2: ...
# """.strip()
            check_prompt = f"""
            Bạn là hệ thống đánh giá tính chính xác của câu trả lời lịch sử Việt Nam.

            🧠 Nhiệm vụ:
            - Đối chiếu từng chi tiết trong CÂU TRẢ LỜI với TÀI LIỆU để xác định xem thông tin đó có đúng không.
            - Nếu câu trả lời chứa **thông tin sai với tài liệu**, đánh giá là `FALSE`.
            - Đánh giá dựa hoàn toàn trên TÀI LIỆU đã cho. Không suy luận thêm từ kiến thức bên ngoài.

            ---CÂU HỎI---
            {enriched_question}

            ---TÀI LIỆU---
            {context}

            ---CÂU TRẢ LỜI---
            {initial_answer}

            📌 Kết luận:
            - Nếu TẤT CẢ các thông tin trong CÂU TRẢ LỜI đều xuất hiện rõ ràng, đúng với TÀI LIỆU → trả lời: `TRUE`
            - Nếu có bất kỳ chi tiết nào không đúng với TÀI LIỆU hoặc không có trong TÀI LIỆU → trả lời: `FALSE`

            📌 Nếu FALSE, hãy liệt kê từng lỗi cụ thể theo định dạng:
            - Chi tiết sai 1: ...
            - Chi tiết sai 2: ...
            """.strip()

            response = (await self.llm.ainvoke(check_prompt)).content.strip()
            passed = "true" in response.lower()
            false_details = re.findall(r"- Chi tiết sai \d+: (.+)", response)

            # 📝 2. Ghi lại kết quả kiểm định
            validation_checks.append({
                "iteration": i + 1,
                "passed": passed,
                "answer": initial_answer,
                "documents": [{
                    "page_content": get_doc_content(doc),
                    "metadata": doc.metadata if hasattr(doc, 'metadata') else doc.get('metadata', {})
                } for doc in initial_docs],
                "response": response,
                "false_details": false_details,
            })

#             # 🔁 3. Dù passed hay failed, luôn tạo tài liệu và câu trả lời mới
#             current_docs = self.retriever.get_documents(enriched_question, int(settings.NUM_DOC))
#             new_context = "\n\n".join(get_doc_content(doc) for doc in current_docs)

#             regenerate_prompt = f"""
# Bạn là hệ thống sinh câu trả lời chính xác.

# ---CÂU HỎI---
# {enriched_question}

# ---TÀI LIỆU---
# {new_context}

# Hãy viết một câu trả lời ngắn gọn, đúng với tài liệu và dễ hiểu.
# """.strip()

#         current_answer = (await self.llm.ainvoke(regenerate_prompt)).content.strip()

        return {
            "validation_checks": validation_checks
        }