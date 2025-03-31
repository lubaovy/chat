import faiss

# Load FAISS index
index = faiss.read_index("python_rag_llm_base_public_main/demo/data_vector/index.faiss")

# Kiểm tra số chiều
print("Số chiều của FAISS index:", index.d)
