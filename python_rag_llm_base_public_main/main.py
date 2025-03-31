from ingestion.ingestion import Ingestion

Ingestion("openai").ingestion_folder(
    path_input_folder="demo\data_in",
    path_vector_store="demo\data_vector",
)