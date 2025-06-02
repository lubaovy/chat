# File: services/answer_validator.py

from typing import Dict, Any
from nltk.tokenize import sent_tokenize
import re
import nltk


nltk.download('punkt', quiet=True)

class AnswerValidator:
    def __init__(self, llm, data_path: str):
        
        from python_rag_llm_base_public_main.chatbot.services.files_chat_agent import FilesChatAgent
        """
        :param llm: LLM bạn đang dùng (ví dụ: self.llm trong chatbot)
        :param data_path: Đường dẫn tới FAISS vector store gốc (data_vector)
        """
        self.llm = llm
        self.agent = FilesChatAgent("python_rag_llm_base_public_main/demo/data_vector")

    def validate(self, final_answer: str) -> Dict[str, Any]:
        """
        Kiểm tra từng câu trong câu trả lời có đáng tin không bằng FAISS gốc.
        """
        sentences = sent_tokenize(final_answer)
        validation_checks = []
        all_passed = True

        for i, sentence in enumerate(sentences):
            result = self.agent.get_workflow().compile().invoke(
                input={"question": sentence, "iteration": 0}
            )

            docs = result["documents"]
            context = "\n\n".join(doc.page_content for doc in docs)

            prompt = f"""
Bạn hãy kiểm tra xem câu sau có đúng hoàn toàn theo tài liệu không.

Hãy trả lời đúng định dạng:

---
TRẢ LỜI: 
TRUE 
- Trích dẫn: <ghi rõ đoạn xác thực từ tài liệu>

HOẶC

FALSE 
- Lý do: <câu này không thấy trong tài liệu hoặc sai lệch>
---

CÂU:
{sentence}

TÀI LIỆU:
{context}
            """

            llm_response = self.llm.invoke(prompt)
            response_text = llm_response.content.strip()

            match = re.search(r"trả lời:\s*(true|false)", response_text, re.IGNORECASE)
            passed = match and match.group(1).strip().lower() == "true"

            if not passed:
                all_passed = False

            validation_checks.append({
                "sentence": sentence,
                "passed": passed,
                "llm_check_response": response_text,
                "retrieved_documents": [doc.page_content[:200] + "..." for doc in docs]
            })

        return {
            "generation": final_answer,
            "reliable": all_passed,
            "validation_checks": validation_checks
        }
