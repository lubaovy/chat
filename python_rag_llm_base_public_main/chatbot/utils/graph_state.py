from typing import List, Dict
from typing_extensions import TypedDict


class GraphState(TypedDict, total=False):
    """
    Lớp GraphState đại diện cho trạng thái của đồ thị.

    Attributes:
        question (str): Câu hỏi của người dùng.
        generation (str): Kết quả sinh ra từ mô hình LLM.
        documents (List[str]): Danh sách tài liệu được truy xuất.
        iteration (int): Số lần lặp lại để kiểm tra độ chính xác.
        results (List[Dict[str, Any]]): Danh sách các câu trả lời sinh ra để tổng hợp.
        confidence (str): Đánh giá độ tin cậy của câu trả lời cuối cùng.
    """

    question: str
    generation: str
    documents: List[str]
    iteration: int
    results: List[Dict[str, any]]
    confidence: str
