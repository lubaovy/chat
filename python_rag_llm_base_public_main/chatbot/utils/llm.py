from langchain_openai import ChatOpenAI  # Import API của OpenAI
from langchain_google_genai import ChatGoogleGenerativeAI  # Import API của Google Gemini
from python_rag_llm_base_public_main.app.config import settings  # Import cấu hình API từ file settings
import requests


def fetch_setting_from_laravel():
    """
    Gọi API từ Laravel để lấy model_name và api_key.
    """
    try:
        response = requests.get("http://localhost:8000/api/chatbot-settings")
        data = response.json()

        model_name = data.get("model_name")
        api_key = data.get("api_key")

        # Kiểm tra nếu không có model_name hoặc api_key thì trả về None
        if not model_name or not api_key:
            print("Lỗi: Không có model_name hoặc api_key.")
            return None, None

        return model_name, api_key
    except Exception as e:
        print(f"Lỗi khi lấy setting từ Laravel: {e}")
        return None, None


class LLM:
    """
    Lớp LLM (Large Language Model) hỗ trợ gọi API của OpenAI và Google Gemini.

    Attributes:
        temperature (float): Độ sáng tạo của mô hình.
        max_tokens (int): Số token tối đa trong một lần gọi API.
        n_ctx (int): Ngữ cảnh tối đa trong một lần gọi API.
    """

    def __init__(self, temperature: float = 0.01, max_tokens: int = 4096, n_ctx: int = 4096) -> None:
        """
        Khởi tạo lớp LLM với các tham số điều chỉnh mô hình.

        Args:
            temperature (float, optional): Độ sáng tạo của mô hình. Mặc định là 0.01.
            max_tokens (int, optional): Số lượng token tối đa. Mặc định là 4096.
            n_ctx (int, optional): Ngữ cảnh tối đa. Mặc định là 4096.
        """
        self.temperature = temperature
        self.n_ctx = n_ctx
        self.max_tokens = max_tokens
        # self.model = ""  # Biến model để lưu mô hình đang sử dụng (nếu cần)
        setting = fetch_setting_from_laravel()
         # Kiểm tra xem setting có hợp lệ hay không
        if setting is None or len(setting) < 2:
            raise ValueError("Không thể lấy thông tin cấu hình từ Laravel.")

        self.model_name = setting[0]  # Lấy model_name từ tuple
        self.api_key = setting[1]     # Lấy api_key từ tuple
    def open_ai(self):
        """
        Khởi tạo mô hình OpenAI sử dụng API Key từ settings.

        Returns:
            ChatOpenAI: Đối tượng mô hình OpenAI.
        """
        # llm = ChatOpenAI(
        #     # openai_api_key=settings.KEY_API_GPT,  # API Key OpenAI
        #     # model=settings.OPENAI_LLM,  # Mô hình OpenAI (ví dụ: 'gpt-4')
        #     # temperature=self.temperature,
        # )
        # return llm
        return ChatOpenAI(
            openai_api_key=self.api_key,
            model=settings.OPENAI_LLM,
            temperature=self.temperature,
        )

    def gemini(self):
        """
        Khởi tạo mô hình Google Gemini sử dụng API Key từ settings.

        Returns:
            ChatGoogleGenerativeAI: Đối tượng mô hình Google Gemini.
        """
        # llm = ChatGoogleGenerativeAI(
        #     google_api_key=settings.KEY_API,  # API Key Google Gemini
        #     model=settings.GOOGLE_LLM,  # Mô hình Google Gemini (ví dụ: 'gemini-pro')
        #     temperature=self.temperature,
        # )
        # return llm
        return ChatGoogleGenerativeAI(
            google_api_key=self.api_key,
            model=settings.GOOGLE_LLM,
            temperature=self.temperature,
        )

    def get_llm(self, llm_name: str):
        """
        Trả về mô hình LLM tương ứng dựa trên tên được cung cấp.

        Args:
            llm_name (str): Tên mô hình ('openai' hoặc 'gemini').

        Returns:
            ChatOpenAI hoặc ChatGoogleGenerativeAI: Đối tượng mô hình tương ứng.
        """
        if llm_name == "openai":
            return self.open_ai()
        elif llm_name == "gemini":
            return self.gemini()
        else:
            return self.open_ai()  # Mặc định sử dụng OpenAI nếu không có tên hợp lệ
