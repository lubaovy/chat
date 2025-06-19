<template>
  <div class="container">
    <!-- Sidebar -->
    <div class="sidebar">
      <h3>
        <a href="/" style="text-decoration: none; color: inherit;">
          📜 CHATBOT LỊCH SỬ VIỆT NAM
        </a>
    </h3>
      <button class="new-chat" @click="newChat">+ Cuộc trò chuyện mới</button>
      <ul>
        <li 
          v-for="(chat, index) in chatHistory"
          :key="index"
          :class="{ active: index === selectedChat }"
        >
          <div class="chat-item">
            <span @click="selectChat(index)" :title="chat.name">
              🗨 {{ chat.name }}
            </span>
            <span class="chat-actions" >
              <button class="menu-btn" @click="toggleMenu(index)" title="Tùy chọn">⋮</button>
              <div v-if="menuVisible === index" class="menu-options">
                <button class="rename-btn" @click="renameChat(index)" title="Đổi tên">Đổi tên</button>
                <button class="delete-btn" @click="deleteChat(index)" title="Xoá">Xoá</button>
              </div>
            </span> 
          </div>
        </li>
      </ul>
    </div>

    <!-- Khung Chat -->
    <div class="chat-container">
      <!-- Tiêu đề khung chat -->
      <div class="chat-header">
        <h3>💬 {{ chatHistory[selectedChat]?.name }}</h3>

        <div class="user-menu" @click="toggleUser">
          {{ userName }}
          <span class="arrow-down"></span>

          <div v-if="showMenu" class="logout-menu">
            <ul>
              <li>Lượt còn lại: {{ remainingQuestions }}</li>
              <li v-if="user" @click="logout">🚪 Đăng xuất</li>
              <li v-else @click="goToLogin">🔐 Đăng nhập</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="chat-box">
        <div v-for="(msg, index) in messages" :key="index" :class="['msg', msg.sender]">
          <!-- USER -->
          <div v-if="msg.sender === 'user'">
            <p>{{ msg.text }}</p>
          </div>

          <!-- BOT -->
          <div v-else >
            <!-- Nếu phát hiện lỗi logic -->
            <div v-if="msg.issue_detected" style="margin-top: 12px;">
              <p><strong>Câu hỏi "{{ msg.original_question }}" có thể chưa chính xác hoặc chứa lỗi logic.</strong></p>

              <div v-if="msg.error_reason" style="margin-top: 4px;">
                <p><strong>Lý do sai:</strong></p>
                <p style="margin-left: 16px;">{{ msg.error_reason }}</p>
              </div>

              <div v-if="msg.enriched_question" style="margin-top: 8px;">
                <p><strong>Có thể bạn muốn hỏi:</strong></p>
                <p style="margin-left: 16px;">{{ msg.enriched_question }}</p>
              </div>
            </div>

            <!-- Trả lời chính -->
            <div v-html="renderAnswerWithLinks(msg)" @click="handleCitationClick"></div>

            <div v-if="msg.has_failed_validation" style="margin-top: 12px;">
              <p><strong>Câu hỏi có thể chưa chính xác, xem chi tiết lỗi ở Xem chi tiết các lần kiểm tra.</strong></p>
            </div>

            <div v-if="!msg.text.startsWith('❌')">
              <!-- Độ tin cậy -->
              <div v-if="msg.reliable !== undefined" style="margin-top: 8px;">
              </div>

              <!-- Sai nội dung chi tiết -->
              <div v-if="msg.false_details_summary?.length" style="margin-top: 8px;">
                <p><strong>Chi tiết sai nội dung:</strong></p>
                <ul>
                  <li v-for="(detail, i) in msg.false_details_summary" :key="i" style="margin-left: 16px;">
                    {{ detail }}
                  </li>
                </ul>
              </div>

              <!-- Tài liệu -->
              <div v-if="msg.documents?.length" style="margin-top: 8px;">
                <details :open="isOpen">
                  <summary @click="toggleOpen" style="cursor: pointer;">📂 Xem tài liệu tham khảo</summary>
                  <ul style="margin-top: 8px;">
                    <li v-for="(doc, i) in msg.documents" :key="i" style="margin-left: 16px; margin-bottom: 8px;">
                      <div v-html="doc"></div>
                    </li>
                  </ul>
                  <button @click="isOpen = false" style="margin-top: 10px;">🔽 Thu gọn</button>
                </details>
              </div>
              <div v-else style="margin-top: 8px;">
                <p><strong>Tài liệu tham khảo:</strong> Không rõ</p>
              </div>
            </div>

            <!-- Hiển thị các lần kiểm tra (validation checks) -->
            <div v-if="msg.validation_checks && msg.validation_checks.length" style="margin-top: 12px;">
              <details>
                <summary>📋 Xem chi tiết các lần kiểm tra</summary>
                <ul>
                  <li v-for="check in msg.validation_checks" :key="check.run" style="margin-bottom: 10px;">
                    <p><strong>Lần {{ check.run }}:</strong>
                      <span :style="{ color: check.passed ? 'green' : 'red' }">
                        {{ check.passed ? '✅ Pass' : '❌ Fail' }}
                      </span>
                    </p>
                    <p><strong>Prompt:</strong></p>
                    <pre style="white-space: pre-wrap; background: #f6f6f6; padding: 10px;">{{ check.prompt }}</pre>
                    <p><strong>Phản hồi:</strong></p>
                    <pre style="white-space: pre-wrap; background: #f0f0f0; padding: 10px;">{{ check.response }}</pre>
                  </li>
                </ul>
              </details>
            </div>     

          </div>
        </div>

        <!-- POPUP hiện tài liệu khi click trích dẫn -->
        <div v-if="showingCitation" class="popup-overlay">
          <div class="popup-box" style="max-width: 600px; text-align: left">
            <h3>Tài liệu tham khảo (tr. {{ activeCitation?.page || '?' }})</h3>
            <pre style="white-space: pre-wrap">{{ highlightedContent }}</pre>
            <button class="popup-close" @click="showingCitation = false">Đóng</button>
          </div>
        </div>

        <div v-if="isLoading" class="msg bot loading">
          <span class="dot"></span>
          <span class="dot"></span>
          <span class="dot"></span>
        </div>

        <!-- Popup khi hết lượt hỏi -->
        <div v-if="upgradePrompt.visible" class="popup-overlay">
          <div class="popup-box">
            <h2>{{ upgradePrompt.title }}</h2>
            <p>{{ upgradePrompt.message }}</p>
            <div class="popup-actions">
              <a :href="upgradePrompt.actionUrl" class="popup-upgrade">{{ upgradePrompt.actionText }}</a>
              <button @click="upgradePrompt.visible = false" class="popup-close">Đóng</button>
            </div>
          </div>
        </div>

      </div>

      <div class="chat-input">
        <input v-model="message" @keyup.enter="sendMessage" placeholder="Nhập câu hỏi về lịch sử..." />
        <button @click="sendMessage">Gửi</button>
      </div>
    </div>
  </div>
</template>


<script>
import { ref, computed } from 'vue';
import axios from 'axios';
import { marked } from "marked";

export default {
  props: {
    user: {
      type: Object,
      default: null
    }
  },

  data() {
    return {
      message: "",
      isLoading: false,
      showMenu: false,
      menuVisible: null,
      remainingQuestions: null,
      chatHistory: [
        { name: "Lịch sử Việt Nam", messages: [] },
        { name: "Các cuộc chiến tranh", messages: [] }
      ],
      selectedChat: 0,
      upgradePrompt: {
        visible: false,
        title: "",
        message: "",
        actionText: "",
        actionUrl: "",
      },
      showingCitation: false,
      activeCitation: null,
      highlightedContent: "",
    };
  },

  computed: {
    userName() {
      return this.user?.name || 'Khách';
    },
    messages() {
      return this.chatHistory[this.selectedChat]?.messages || [];
    }
  },

  mounted() {
    this.fetchRemainingQuestions();
  },  

  methods: {
    // 1️⃣ RENDER LINK TRONG CÂU TRẢ LỜI
    renderAnswerWithLinks(msg) {
      let html = msg.text;
      if (msg.citations && msg.citations.length) {
        msg.citations.forEach((citation, idx) => {
          const pattern = citation.matched_text?.slice(0, 30); // dùng phần đầu đoạn trích
          if (!pattern) return;
          const escaped = pattern.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
          const regex = new RegExp(escaped, "i");
          html = html.replace(regex, match => {
            return `<a href="#" class="citation-link" data-docid="${citation.doc_id}" data-index="${idx}">${match}</a>`;
          });
        });
      }
      return html;
    },

    // 2️⃣ XỬ LÝ CLICK LINK
    handleCitationClick(e) {
      const el = e.target;
      if (!el.classList.contains("citation-link")) return;

      const docId = el.getAttribute("data-docid");
      const idx = el.getAttribute("data-index");
      const msg = this.messages.find(m => m.citations);
      const citation = msg?.citations?.[idx];

      const matchedDoc = msg?.documents?.find(doc => doc.metadata?.doc_id === docId);

      if (matchedDoc) {
        this.activeCitation = citation;
        const fullText = matchedDoc.page_content || "";
        const highlight = citation.matched_text || "";

        const escaped = highlight.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const reg = new RegExp(`(${escaped})`, "gi");
        this.highlightedContent = fullText.replace(reg, '<mark>$1</mark>');
        this.showingCitation = true;
      } else {
        alert("Không tìm thấy tài liệu cho trích dẫn này.");
      }
    },

    toggleMenu(index) {
      this.menuVisible = this.menuVisible === index ? null : index; // Toggle menu cho cuộc trò chuyện
    },

    toggleUser() {
      this.showMenu = !this.showMenu;
    },

    async logout() {
      try {
        await axios.post('/logout');
        window.location.href = '/'; // hoặc đường dẫn trang đăng nhập
      } catch (error) {
        console.error("Lỗi đăng xuất:", error);
      }
    },

    goToLogin() {
      window.location.href = '/login';
    },

    async sendMessage() {
      if (!this.message.trim()) return;

      const userMsg = this.message;
      this.message = "";

      this.chatHistory[this.selectedChat].messages.push({
        text: userMsg,
        sender: "user"
      });

      this.isLoading = true;

      try {
        const headers = {
          "Content-Type": "application/json",
          "API-Key": "Lk13bVFH1eyy0pz1LBpgmt4iUNDYQAY6"
        };

        // Nếu có token người dùng đăng nhập → thêm Authorization
        if (this.user?.token) {
          headers["Authorization"] = `Bearer ${this.user.token}`;
        }

        const response = await axios.post(  
          "/chatbot/ask/",
          { question: userMsg },
          {
            withCredentials: true,
            headers,
            timeout: 90000
          }
        );
        this.remainingQuestions = response.data.remaining;
        // Nếu hết lượt thì hiện popup nâng cấp
        if (this.remainingQuestions === 0) {
          this.upgradePrompt = {
            visible: true,
            title: "Hết lượt hỏi",
            message: "Bạn đã sử dụng hết lượt hỏi miễn phí.",
            actionText: "Nâng cấp",
            actionUrl: "/pricing", // hoặc /login nếu cần
          };
        }

        const {
          generation,
          documents,
          reliable,
          validation_checks,
          false_details_summary,
          error_reason,
          issue_detected,
          original_question,
          enriched_question,
          has_failed_validation
        } = response.data.answer;

        const botMsg = {
          text: typeof generation === 'string'
            ? marked.parse(generation)
            : "❌ Lỗi: Không thể hiển thị câu trả lời do dữ liệu không hợp lệ.",
          documents: documents.map(doc => doc.page_content),
          reliable,
          validation_checks,
          false_details_summary,
          error_reason,
          issue_detected,
          original_question,
          enriched_question,
          has_failed_validation,
          sender: "bot"
        };

        this.chatHistory[this.selectedChat].messages.push(botMsg);
      } catch (err) {
        if (err.response && (err.response.status === 403 || err.response.status === 429)) {
          const data = err.response.data;
          console.log('Thông báo từ backend:', data);
          this.upgradePrompt = {
            visible: true,
            title: data.title || "Thông báo",
            message: data.message || "Bạn đã hết lượt hỏi.",
            actionText: data.action?.text || "Đăng nhập",
            actionUrl: data.action?.url || "/login",
          };
        } else {
          const errorMessage = err?.message || 'Đã xảy ra lỗi không xác định.';
          console.error('Chi tiết lỗi:', err);

          this.chatHistory[this.selectedChat].messages.push({
            text: `❌ Lỗi: không thể gửi câu hỏi. (${errorMessage})`,
            sender: "bot",
          });
        }
      } finally {
        this.isLoading = false;
      }
    },

    async fetchRemainingQuestions() {
      try {
        const res = await axios.get('/remaining-questions', {
          withCredentials: true,
        });
        this.remainingQuestions = res.data.remaining;
      } catch (error) {
        console.error("Lỗi khi lấy số lượt còn lại:", error);
      }
    },

    newChat() {
      const newName = "Cuộc trò chuyện " + (this.chatHistory.length + 1);
      this.chatHistory.push({ name: newName, messages: [] });
      this.selectedChat = this.chatHistory.length - 1;
    },

    selectChat(index) {
      this.selectedChat = index;
    },

    renameChat(index) {
      const newName = prompt("Đổi tên cuộc trò chuyện:", this.chatHistory[index].name);
      if (newName?.trim()) {
        this.chatHistory[index].name = newName.trim();
      }
    },

    deleteChat(index) {
      if (confirm("Bạn có chắc muốn xóa cuộc trò chuyện này?")) {
        this.chatHistory.splice(index, 1);
        this.selectedChat = Math.max(0, this.selectedChat - 1);
      }
    }
  }
  
};
</script>

<style scoped>
/* Font và màu sắc */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Inter:wght@400;600&display=swap');

* {
  font-family: 'Poppins', sans-serif;
  box-sizing: border-box;
}

html, body {
  height: 100%;
  margin: 0;
  padding: 0;
}

.container {
  display: flex;
  height: 100vh;
  background: #f4f6f9; /* Nền nhẹ nhàng, dễ nhìn */
  font-family: sans-serif;
}

/* Sidebar */
.sidebar {
  width: 280px;
  background: rgb(255, 255, 255);
  border-right: 1px solid #e0dcdc;  
  padding: 20px 15px;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  transition: all 0.3s ease-in-out;
  transform-origin: left center;
  animation: slideIn 0.5s ease-out;
}

.sidebar:hover {
  transform: scale(1.01);
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

.sidebar h3 {
  font-size: 1.3rem;
  padding: 0 10px;
  font-weight: 600;
  margin-bottom: 20px;
  text-align: center;
  color: #b22222;
  letter-spacing: 0.5px;
}

@keyframes slideIn {
  0% {
    transform: translateX(-100px);
    opacity: 0;
  }
  100% {
    transform: translateX(0);
    opacity: 1;
  }
}

.new-chat {
  width: 100%;
  padding: 10px;
  background: #b22222;
  color: #fff;
  border: none;
  border-radius: 8px;
  margin: 0 0 20px 0;
  cursor: pointer;
  font-size: 1rem;
  transition: background-color 0.3s ease;
}

.new-chat:hover {
  background-color: #8b1a1a;
}

.sidebar ul {
  list-style: none;
  padding: 0;
  flex-grow: 1;
  overflow-y: auto;
}

.sidebar li {
  padding: 12px;
  margin-bottom: 10px;
  cursor: pointer;
  border-radius: 8px;
  transition: background-color 0.3s ease;
}

.sidebar li.active {
  background:rgb(255, 233, 124);
}

.sidebar li:hover {
  background: #ffeaaa;
  transform: translateX(5px);
}

.chat-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.chat-actions button {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1rem;
  transition: color 0.3s ease;
}

.chat-actions button:hover {
  color: #007bff;
}

/* Chat container */
.chat-container {
  flex: 1;
  display: flex;
  flex-direction: column;
  background: #fff;
  position: relative;
  overflow: hidden;
}

/* Chat header */
.chat-header {
  position: relative;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #ffd70033;
  max-width: 100%; /* Đảm bảo chat-header không rộng quá */
  box-sizing: border-box;
}

.chat-header h3 {
  font-weight: 600;
  color: #333;
  color: #b30000; /* đỏ đậm nhưng không chói */
  font-size: 1.2rem;
  display: flex;
  align-items: center;
  gap: 8px;
}

.chat-header .user-name {
  font-size: 1rem;
  font-weight: bold;
  cursor: pointer;
  color:rgb(247, 191, 60); 
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
}

.chat-header .user-name:hover {
  text-decoration: underline;
}

.chat-box {
  display: flex;
  flex-direction: column; 
  flex: 1;
  overflow-y: auto;
  padding: 20px 25px;
  padding-bottom: 80px; /* Chừa chỗ cho chat-input */
}

.logout-menu {
  position: absolute;
  top: 48px;
  right: 20px;
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  z-index: 1000;
  min-width: 140px;
  padding: 8px 0;
  font-size: 13px;
}

.logout-menu ul {
  list-style: none;
  margin: 0;
  padding: 0;
}

.logout-menu li {
  padding: 12px 20px;
  cursor: pointer;
  transition: background 0.3s ease;
}

.logout-menu li:hover {
  background: #f1f1f1;
}

.user-menu {
  position: relative;
  font-weight: bold;
  color: #f6ad55; /* màu vàng cam */
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
}

.arrow-down {
  display: inline-block;
  width: 0;
  height: 0;
  margin-top: 2px;
  border-left: 5px solid transparent;
  border-right: 5px solid transparent;
  border-top: 5px solid #f6ad55;
  transition: transform 0.3s ease;
}

.user-menu:hover .arrow-down {
  transform: rotate(180deg);
}

/* Tin nhắn */
.msg {
  max-width: 75%; /* Tăng từ 80% lên 90% */
  width: 100%;
  padding: 16px 20px;
  border-radius: 12px;
  font-size: 16px;
  line-height: 1.6;
  margin-bottom: 16px;
  background: #f3f4f6;
}

.msg.user {
  align-self: flex-end;
  background: rgb(247, 246, 246);
  max-width: 70%;
}

.msg.bot {
  align-self: flex-start;
  background: transparent;
  max-width: 70%;
  margin: 12px auto;
  padding: 0;
}

/* Loading dots */
.msg.loading {
  display: flex;
  gap: 8px;
}

.dot {
  width: 10px;
  height: 10px;
  background: #999;
  border-radius: 50%;
  animation: wave 1.2s infinite ease-in-out;
}

@keyframes wave {
  0%, 100% {
    transform: translateY(0);
    opacity: 0.5;
  }
  50% {
    transform: translateY(-6px);
    opacity: 1;
  }
}

/* Chat input */
.chat-input {
  display: flex;
  padding: 15px 25px;
  background: #fff;
}

.chat-input input {
  flex: 1;
  padding: 12px 15px;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 1rem;
  transition: border-color 0.3s ease;
}

.chat-input input:focus {
  border-color: #cc0000;
}

.chat-input button {
  margin-left: 12px;
  padding: 12px 20px;
  background:rgb(255, 233, 124);
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 1.1rem;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.chat-input button:hover {
  background-color:rgb(255, 225, 73) ;
}

/* Thêm style cho menu 3 chấm và các nút hành động */
.chat-actions {
  position: relative;
}

.menu-btn {
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
  color: #333;
}

.menu-options {
  width: 90px;
  position: absolute;
  top: 100%;
  right: 0;
  background: #fff;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  border-radius: 8px;
  display: flex;
  flex-direction: column;
  padding: 10px;
  z-index: 20;
  transform: translateX(0);
}

.menu-options button {
  background: none;
  border: none;
  padding: 8px 12px;
  font-size: 13px;
  cursor: pointer;
  text-align: left;
}

.menu-options button:hover {
  background-color: #f0f0f0;
}

.menu-btn:hover {
  color: #000;
}

/* Nền mờ phủ toàn màn hình */
.popup-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.5); /* nền mờ */
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

/* Khung nội dung popup */
.popup-box {
  background-color: #ffffff;
  border-radius: 12px;
  padding: 24px 32px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
  max-width: 400px;
  width: 90%;
  text-align: center;
  animation: fadeIn 0.3s ease-out;
}

/* Tiêu đề popup */
.popup-box h2 {
  font-size: 22px;
  margin-bottom: 12px;
  color: #c53030; /* đỏ nhẹ */
}

/* Nội dung mô tả */
.popup-box p {
  font-size: 16px;
  color: #444;
  margin-bottom: 20px;
}

/* Vùng chứa nút */
.popup-actions {
  display: flex;
  justify-content: center;
  gap: 16px;
}

/* Nút nâng cấp */
.popup-upgrade {
  background-color: #f6ad55; /* cam nhạt */
  color: #fff;
  text-decoration: none;
  padding: 10px 20px;
  border-radius: 6px;
  font-weight: bold;
  transition: background-color 0.2s;
}

.popup-upgrade:hover {
  background-color: #dd6b20; /* cam đậm */
}

/* Nút đóng */
.popup-close {
  background: none;
  border: none;
  color: #666;
  font-size: 14px;
  cursor: pointer;
}

.popup-close:hover {
  text-decoration: underline;
}

/* Hiệu ứng mờ vào */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.citation-link {
  color: #cc0000;
  text-decoration: underline;
  cursor: pointer;
}
mark {
  background: yellow;
  font-weight: bold;
}

</style>
