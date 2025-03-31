from fastapi import FastAPI
from pydantic import BaseModel
from chatbot.services.files_chat_agent import FilesChatAgent
from app.config import settings

# Cấu hình mô hình
settings.LLM_NAME = "phobert"

# Khởi tạo FastAPI
app = FastAPI()

# Khai báo dữ liệu đầu vào
class ChatRequest(BaseModel):
    question: str

@app.post("/chatbot")
def chat_with_bot(request: ChatRequest):
    chat_agent = FilesChatAgent("demo/data_vector").get_workflow().compile()
    response = chat_agent.invoke(
        input={
            "question": request.question,
            "iteration": 0,
        }
    )

    return {
        "answer": response["generation"],
        "sources": response["documents"]
    }
