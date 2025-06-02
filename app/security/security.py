from fastapi import Security  # noqa: E402
from fastapi import FastAPI, File, UploadFile, Header, HTTPException, Request, Form  # noqa: E402, F401
from fastapi.security import APIKeyHeader  # noqa: E402

from app.config import settings

# Định nghĩa header cho API key
api_key_header = APIKeyHeader(name="API-Key", auto_error=False)


async def get_api_key(api_key_header_value: str = Security(api_key_header)):  
    print(f"Received API Key: {api_key_header_value}")  # Debugging
    print(f"Expected API Key: {settings.API_KEY}")  # Debugging

    if api_key_header_value is None:
        raise HTTPException(status_code=403, detail="API Key is missing")

    if api_key_header_value == settings.API_KEY:
        return api_key_header_value

    raise HTTPException(status_code=403, detail="Could not validate API Key")


# print(f"Received API Key: {api_key_header}")
# print(f"Expected API Key: {settings.API_KEY}")
