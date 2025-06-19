import httpx

class OpenRouterWrapper:
    def __init__(self, api_key, model="deepseek-ai/deepseek-llm-67b-chat"):
        self.api_key = api_key
        self.model = model
        self.api_url = "https://openrouter.ai/api/v1/chat/completions"

    async def ainvoke(self, prompt: str) -> str:
        headers = {
            "Authorization": f"Bearer {self.api_key}",
            "HTTP-Referer": "http://ai-lichsu.com/",  # required by OpenRouter
            "X-Title": "Chatbot-Lich-Su"
        }
        data = {
            "model": self.model,
            "messages": [
                {"role": "system", "content": "Bạn là một trợ lý lịch sử Việt Nam, trả lời bằng tiếng Việt."},
                {"role": "user", "content": prompt}
            ]
        }
        async with httpx.AsyncClient(timeout=20.0) as client:
            resp = await client.post(self.api_url, json=data, headers=headers)
            result = resp.json()
            if "choices" in result:
                return result["choices"][0]["message"]["content"]
            else:
                raise ValueError(f"❌ Lỗi khi gọi OpenRouter: {result.get('error', {}).get('message', 'Không rõ lỗi')}")
