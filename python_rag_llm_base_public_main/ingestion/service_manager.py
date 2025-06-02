from langchain_openai import OpenAIEmbeddings
from langchain_huggingface import HuggingFaceEmbeddings
from langchain_community.embeddings import HuggingFaceEmbeddings
from python_rag_llm_base_public_main.app.config import settings

class ServiceManager:
    """
    Quản lý các dịch vụ liên quan đến embeddings.
    """

    def __init__(self) -> None:
        """
        Khởi tạo ServiceManager.
        """
        pass

    def get_embedding_model(self, embedding_model_name: str):
        """
        Trả về mô hình embeddings tương ứng dựa trên tên mô hình.

        Args:
            embedding_model_name (str): Tên của mô hình embeddings.

        Returns:
            OpenAIEmbeddings | None: Đối tượng OpenAIEmbeddings nếu tìm thấy, ngược lại trả về None.
        """
        embeddings = None
        if embedding_model_name == "openai":
            embeddings = OpenAIEmbeddings(model="text-embedding-ada-002", openai_api_key=settings.KEY_API_GPT)
        elif embedding_model_name == "phobert":
            embeddings = HuggingFaceEmbeddings(model_name="./python_rag_llm_base_public_main/sup-SimCSE-VietNamese-phobert-base")
        return embeddings