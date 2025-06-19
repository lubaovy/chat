import json

class QuestionAnalyzer:
    def __init__(self, llm):
        self.llm = llm

    def analyze(self, question: str) -> dict:
        prompt = f"""
Bạn là trợ lý AI chuyên phân tích truy vấn người dùng về lịch sử Việt Nam.

Hãy đánh giá tính hợp lý của câu hỏi dưới đây, bao gồm:
- Cú pháp và ngữ nghĩa.
- **Tính chính xác về mặt kiến thức lịch sử**: Nhân vật, thời gian, bối cảnh, sự kiện có đúng không?
- **Loại câu hỏi**: câu hỏi đơn, câu hỏi so sánh, câu hỏi nhiều bước (multi-hop), hay câu hỏi mơ hồ.

Câu hỏi: "{question}"

Chỉ trả về `"valid": false"` nếu câu hỏi chứa thông tin sai sự thật lịch sử rõ ràng.
Ví dụ: nếu nhân vật, thời gian, hoặc sự kiện trong câu hỏi **không đúng**, mới được xem là `valid: false`.
Nếu câu hỏi đúng nhưng thiếu bối cảnh, chỉ cần thêm `insights`, KHÔNG đánh `valid: false`.
và liệt kê **tất cả lỗi** rõ ràng trong mảng `"error_reason"`.

Ví dụ lỗi:
- "Ngô Quyền đánh quân Tống" ❌ (thực tế là quân Nam Hán)
- "Hồ Chí Minh sinh năm 1892" ❌ (sai năm sinh)
- "Chiến tranh thế giới thứ nhất xảy ra vào thế kỷ 18" ❌ (sai thời điểm)
- "Ngô Quyền đánh quân Tống và giành độc lập năm 1010" ❌
  → lỗi:
    - Ngô Quyền đánh quân Nam Hán, không phải quân Tống.
    - Ông giành độc lập năm 938, không phải 1010.

Trả về đúng định dạng JSON sau:

{{
  "valid": true | false,
  "error_reason": "Giải thích nếu có lỗi",
  "inferred_intent": "Ý định suy đoán nếu có",
  "insights": ["Gợi ý phân tích hoặc đặc điểm câu hỏi"]
}}
"""
        response = self.llm.invoke(prompt).content.strip()
        try:
            result = json.loads(response)
        except Exception as e:
            print(f"[ANALYZE PARSE ERROR]: {e} | RAW: {response}")
            result = {
                "valid": False,
                "error_reason": "Không phân tích được phản hồi từ LLM. Có thể đầu ra không đúng định dạng JSON.",
                "inferred_intent": question,
                "insights": ["Phản hồi từ LLM không đúng định dạng, cần kiểm tra prompt hoặc kết quả trả về."]
            }
        return result
    def infer_top_k(self, analysis: dict) -> int:
        """
        Suy luận số lượng top-k cần lấy (dynamic) dựa trên kết quả phân tích câu hỏi.
        """
        inferred_intent = analysis.get("inferred_intent", "").lower()
        insights = [i.lower() for i in analysis.get("insights", [])]

        # Nếu là câu hỏi so sánh, phân biệt => cần nhiều tài liệu hơn
        if any(kw in inferred_intent for kw in ["so sánh", "khác nhau", "phân biệt", "giống nhau", "điểm giống", "điểm khác"]):
            return 7
        elif any("nhiều bước" in i for i in insights):
            return 6
        elif any("câu hỏi đơn" in i for i in insights):
            return 3
        else:
            return 5  # fallback mặc định

class QuestionEnricher:
    def __init__(self, llm):
        self.llm = llm

    def enrich(self, question: str) -> str:
        prompt = f"""
Bạn là chuyên gia cải thiện truy vấn cho chatbot lịch sử Việt Nam.  
Nhiệm vụ của bạn là giúp người dùng viết lại câu hỏi sau rõ ràng và đầy đủ hơn để tìm kiếm thông tin tốt hơn từ tài liệu lịch sử.

Lưu ý:
- Giữ nguyên ý nghĩa gốc.
- Bổ sung ngữ cảnh (nếu cần) để truy vấn dễ match với tài liệu lịch sử.
- Không chỉ lặp lại câu hỏi gốc.
- **Nếu câu hỏi có dạng so sánh hoặc nhiều bước, hãy tách thành các tiểu truy vấn hoặc làm rõ từng đối tượng.**

Câu hỏi gốc: "{question}"

Câu hỏi sau khi làm rõ:
"""
        enriched = self.llm.invoke(prompt).content.strip()
        print(f"[LLM ENRICHED]: {enriched}")
        return enriched


class SmartQuestionHandler:
    def __init__(self, llm):
        self.llm = llm
        self.analyzer = QuestionAnalyzer(llm)
        self.enricher = QuestionEnricher(llm)

    def process(self, question: str) -> dict:
        analysis = self.analyzer.analyze(question)
        error_reason = analysis.get('error_reason', 'Câu hỏi không hợp lệ.')
        top_k = self.analyzer.infer_top_k(analysis)
        if not analysis.get("valid", True):
            inferred_intent = analysis.get("inferred_intent") or question
            prompt = f"""
        Bạn là chuyên gia xử lý câu hỏi lịch sử. Dưới đây là một câu hỏi từ người dùng bị xác định là chưa hợp lý.

        Câu hỏi gốc: "{question}"

        ⛔ Vấn đề phát hiện: {analysis.get('error_reason', '')}

        🔍 Ý định suy đoán từ người dùng: "{analysis.get('inferred_intent') or question}"

        🎯 Nhiệm vụ:
        - Viết lại câu hỏi sao cho hợp lý và đúng sự thật lịch sử.
        - **Giữ lại tất cả các yếu tố cốt lõi từ ý định**, bao gồm tên các nhân vật và bối cảnh được nhắc đến.
        - Sửa lỗi nêu trên mà vẫn giữ được mục tiêu ban đầu của người dùng.
        - Có thể thêm chi tiết lịch sử nếu cần làm rõ.

        📌 Câu hỏi viết lại:
        """

            rewritten = self.llm.invoke(prompt).content.strip()
            print(f"[AUTO-FIXED]: {rewritten}")
            return {
                "enriched_question": rewritten,
                "issue_detected": True,
                "issues": analysis.get("error_reason",[]),
                "insights": analysis.get("insights", []),
                "error_reason": error_reason,
                "analysis": analysis,
                "top_k": top_k  
            }

        enriched = self.enricher.enrich(question)
        return {
            "enriched_question": enriched,
            "issue_detected": False,
            "issues": [],
            "insights": analysis.get("insights", []),
            "analysis": analysis,
            "top_k": top_k
        }

