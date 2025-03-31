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

def get_bot_response(question: str) -> dict:
    """
    Nhận câu hỏi, gọi chatbot xử lý và trả về kết quả
    """

    chat_agent = FilesChatAgent("python_rag_llm_base_public_main/demo/data_vector")
    response = chat_agent.get_workflow().compile().invoke(
        input={"question": question, "iteration": 0}
    )

    return {
        "generation": response["generation"],  # Câu trả lời của chatbot
        "documents": response["documents"]     # Các tài liệu tham khảo
    }