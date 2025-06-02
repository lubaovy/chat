# # chuẩn bị dữ liệu
# from ingestion.ingestion import Ingestion

# Ingestion("openai").ingestion_folder(
#     path_input_folder="demo\data_in",
#     path_vector_store="demo\data_vector",
# )

# chatbot
from python_rag_llm_base_public_main.chatbot.services.files_chat_agent import FilesChatAgent  # noqa: E402
from python_rag_llm_base_public_main.app.config import settings

settings.LLM_NAME = "phobert"
CHAT_AGENT = FilesChatAgent("python_rag_llm_base_public_main/demo/data_vector")

def get_bot_response(question: str) -> dict:
    """
    Nhận câu hỏi, gọi chatbot xử lý và trả về kết quả
    """

    # chat_agent = FilesChatAgent("python_rag_llm_base_public_main/demo/data_vector")
    response = CHAT_AGENT.get_workflow().compile().invoke(
        input={"question": question, "iteration": 0}
    )
    
    # print(">>> Response từ workflow:")
    # print(response)

    return {
        "generation": response["generation"],  # Câu trả lời của chatbot
        "documents": response["documents"],     # Các tài liệu tham khảo
        "validation_checks": response.get("validation_checks", []),  # Kiểm tra hậu sinh
        "reliable": response.get("reliable", False),    # Có đáng tin hay không
        
         # ✅ Thêm thông tin lỗi logic (nếu có)
        "issue_detected": response.get("issue_detected", False),
        "error_reason": response.get("error_reason", ""),
        "original_question": response.get("original_question", ""),
        "enriched_question": response.get("question", ""),
    }