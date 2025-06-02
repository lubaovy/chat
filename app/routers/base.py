from fastapi import APIRouter, Body, Depends  
from fastapi.concurrency import run_in_threadpool
from app.security.security import get_api_key
from python_rag_llm_base_public_main.run import get_bot_response  # Import hàm xử lý chatbot
from pydantic import BaseModel

router = APIRouter(prefix="/chatbot", tags=["chatbot"])

class Base(BaseModel):
    question: str

@router.post("/ask/")
async def ask_question(
    request: Base,
    api_key: str = Depends(get_api_key)
):
    question = request.question
    # response = get_bot_response(question)  # Gọi chatbot
    response = await run_in_threadpool(get_bot_response, question)
    return {"answer": response}  # Trả về kết quả