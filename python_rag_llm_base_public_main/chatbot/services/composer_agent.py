from python_rag_llm_base_public_main.chatbot.utils.answer_generator import AnswerGenerator

class ComposerAgent:
    def __init__(self, llm):
        """Khởi tạo agent tổng hợp câu trả lời cuối cùng"""
        self.answer_generator = AnswerGenerator(llm)

    def _get_document_content(self, doc):
        """Hàm helper để lấy nội dung document từ nhiều định dạng"""
        if isinstance(doc, dict):
            return doc.get("page_content", "")
        elif hasattr(doc, 'page_content'):
            return doc.page_content
        else:
            raise ValueError(f"Định dạng document không hỗ trợ: {type(doc)}")

    async def run(self, question: str, verified_documents):
        """
        Tạo câu trả lời cuối cùng từ các tài liệu đã xác minh
        
        Args:
            question: Câu hỏi của người dùng
            verified_documents: Danh sách tài liệu đã được kiểm định
                              (có thể là dict hoặc Document object)
        
        Returns:
            Câu trả lời được tạo bởi LLM
        """
        # Xử lý trường hợp không có tài liệu hợp lệ
        if not verified_documents:
            return {"error": "Không có tài liệu hợp lệ để tổng hợp câu trả lời"}
        
        try:
            # Tạo context từ các document (xử lý đa định dạng)
            context = "\n\n".join(
                f"[Nguồn: {doc.get('metadata', {}).get('source', 'Không rõ')}]\n"
                f"{self._get_document_content(doc)}"
                for doc in verified_documents
            )
            
            # Debug (có thể bỏ sau khi kiểm tra)
            print(f"Context length: {len(context)} characters")
            print(f"[Composer] Nhận được {len(verified_documents)} tài liệu để tổng hợp.")
            
            # Tạo câu trả lời cuối cùng
            generation = await self.answer_generator.get_chain().ainvoke({
                "question": question,
                "context": context
            })
            print(f"[DEBUG] Composer context length: {len(context)} characters")

            return {
                "answer": generation,
                "sources": [doc.get("metadata", {}).get("source", "") 
                           for doc in verified_documents]
            }
            
        except Exception as e:
            return {
                "error": f"Lỗi khi tạo câu trả lời: {str(e)}",
                "exception_type": type(e).__name__
            }