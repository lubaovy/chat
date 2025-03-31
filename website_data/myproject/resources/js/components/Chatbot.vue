<template>
  <div class="chat-container">
    <div class="chat-box">
      <div v-for="(msg, index) in messages" :key="index" :class="['msg', msg.sender]">
        <p>{{ msg.text }}</p>
      </div>
    </div>
    <div class="chat-input">
      <input v-model="message" @keyup.enter="sendMessage" placeholder="Nhập tin nhắn..." />
      <button @click="sendMessage">Gửi</button>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      message: "",
      messages: [],
    };
  },
  methods: {
    async sendMessage() {
      if (!this.message.trim()) return;

      // Hiển thị tin nhắn người dùng
      this.messages.push({ text: this.message, sender: "user" });
      let userMessage = this.message;
      this.message = "";

      try {
        // Gửi tin nhắn đến API Laravel
        const response = await axios.post("http://127.0.0.1:8000/chatbot/ask", { question: userMessage },{
        headers: { "X-API-Key": "Lk13bVFH1eyy0pz1LBpgmt4iUNDYQAY6" } // Thêm API Key
      });

        // Hiển thị câu trả lời từ bot
        this.messages.push({ text: response.data.answer, sender: "bot" });

      } catch (error) {
        console.error("Lỗi gửi tin nhắn:", error);
        this.messages.push({ text: "❌ Lỗi: Không thể kết nối với chatbot!", sender: "bot" });
      }
    }
  }
};
</script>

<style scoped>
.chat-container {
  width: 400px;
  margin: 50px auto;
  border-radius: 10px;
  box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  background: white;
}

.chat-box {
  height: 300px;
  padding: 10px;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.msg {
  max-width: 80%;
  padding: 8px 12px;
  border-radius: 15px;
  word-wrap: break-word;
}

.msg.user {
  background: pink;
  color: white;
  align-self: flex-end;
}

.msg.bot {
  background: #f1f1f1;
  color: black;
  align-self: flex-start;
}

.chat-input {
  display: flex;
  padding: 10px;
  border-top: 1px solid #ddd;
  background: white;
}

.chat-input input {
  flex: 1;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 5px;
  outline: none;
}

.chat-input button {
  margin-left: 10px;
  padding: 8px 12px;
  background: pink;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}
</style>
