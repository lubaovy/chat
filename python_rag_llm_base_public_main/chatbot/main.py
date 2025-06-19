from chatbot.services.llm_generator import LLMGenerator
from chatbot.services.llm_verifier import LLMVerifier
from chatbot.services.llm_composer import LLMComposer
from utils.retriever import Retriever
from your_llm_wrapper import YourLLM

# Khởi tạo
retriever = Retriever()
llm1 = YourLLM("gpt-4")  # generator
llm2 = YourLLM("gpt-4")  # verifier
llm3 = YourLLM("gpt-4")  # composer

generator = GeneratorAgent(llm1, retriever)
verifier = VerifierAgent(llm2, retriever, max_rounds=3)
composer = ComposerAgent(llm3)

# Chạy hệ thống
question = "Tại sao nhà Tây Sơn suy yếu nhanh chóng sau khi Quang Trung mất?"

init_answer, init_docs = generator.generate(question)
history = verifier.verify_and_improve(question, init_answer, init_docs)
final_docs = history[-1]["docs"]
final_answer = composer.compose(question, final_docs)

# In kết quả
print("===== CÂU TRẢ LỜI CUỐI CÙNG =====")
print(final_answer)
print("\n===== LỊCH SỬ KIỂM TRA =====")
for i, step in enumerate(history):
    print(f"[Vòng {i+1}] Passed: {step['passed']}")
    print("Answer:", step["answer"])
    print("False Details:", step["false_details"])
    print("----")
