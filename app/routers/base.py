from fastapi import APIRouter, Form, Depends  
from app.security.security import get_api_key
from python_rag_llm_base_public_main.run import get_bot_response  # Import hàm xử lý chatbot

router = APIRouter(prefix="/chatbot", tags=["chatbot"])

@router.post("/ask")
async def ask_question(
    question: str = Form(...), 
    api_key: str = Depends(get_api_key)
):
    response = get_bot_response(question)  # Gọi chatbot
    return {"answer": response}  # Trả về kết quả