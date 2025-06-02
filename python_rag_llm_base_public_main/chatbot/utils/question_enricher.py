import re

class QuestionEnricher:
    def __init__(self, llm):
        self.llm = llm

    def enrich(self, question: str) -> str:
        question = question.strip()

        # Nếu câu hỏi là kiểu định nghĩa ngắn, enrich nhẹ nhàng
        if re.match(r"^(ai|cái gì|gì|là ai|là gì)\b", question.lower()) or len(question.split()) < 6:
            # Gợi ý enrich thông minh theo ngữ nghĩa
            prompt = f"""
Bạn là trợ lý AI thông minh chuyên cải thiện câu hỏi người dùng.  
Nhiệm vụ của bạn là viết lại câu hỏi bên dưới rõ ràng hơn để tìm kiếm thông tin tốt hơn từ tài liệu lịch sử Việt Nam.

Lưu ý:
- Giữ nguyên ý nghĩa gốc.
- Bổ sung ngữ cảnh (nếu cần) để truy vấn dễ match với tài liệu lịch sử.
- Không chỉ lặp lại câu hỏi gốc.

Câu hỏi gốc: "{question}"

Câu hỏi sau khi làm rõ:
"""
            enriched = self.llm.invoke(prompt).content.strip()
            print(f"[LLM ENRICHED]: {enriched}")
            return enriched

        # Nếu đã đủ dài và rõ, giữ nguyên
        print(f"[SKIP ENRICH]: {question}")
        return question
