/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import { createApp } from 'vue';
import Chatbot from './components/Chatbot.vue';

/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */

const app = createApp({});

import ExampleComponent from './components/ExampleComponent.vue';
app.component('example-component', ExampleComponent);
app.component('chatbot', Chatbot);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// Object.entries(import.meta.glob('./**/*.vue', { eager: true })).forEach(([path, definition]) => {
//     app.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
// });

/**
 * Finally, we will attach the application instance to a HTML element with
 * an "id" attribute of "app". This element is included with the "auth"
 * scaffolding. Otherwise, you will need to add an element yourself.
 */

app.mount('#app');

// document.addEventListener("DOMContentLoaded", () => {
//     const chatHistory = [];
//     let selectedChat = 0;

//     const newChatBtn = document.getElementById("newChatBtn");
//     const sendBtn = document.getElementById("sendBtn");
//     const messageInput = document.getElementById("messageInput");
//     const chatBox = document.getElementById("chatBox");
//     const chatTitle = document.getElementById("chatTitle");
//     const chatHistoryList = document.getElementById("chatHistory");

//     // Thêm cuộc trò chuyện mới
//     newChatBtn.addEventListener("click", () => {
//         const newName = "Cuộc trò chuyện " + (chatHistory.length + 1);
//         chatHistory.push({ name: newName, messages: [] });
//         selectedChat = chatHistory.length - 1;
//         renderChatHistory();
//         renderChat();
//     });

//     // Gửi tin nhắn
//     sendBtn.addEventListener("click", () => {
//         const userMsg = messageInput.value.trim();
//         if (!userMsg) return;

//         messageInput.value = "";
//         chatHistory[selectedChat].messages.push({
//             text: userMsg,
//             sender: "user"
//         });

//         renderChat();

//         // Gửi yêu cầu đến API Laravel
//         fetch('/api/chatbot/ask', {
//             method: 'POST',
//             headers: {
//                 'Content-Type': 'application/json',
//             },
//             body: JSON.stringify({ question: userMsg }),
//         })
//         .then(response => response.json())
//         .then(data => {
//             const botMsg = {
//                 text: data.answer.generation,
//                 documents: data.answer.documents,
//                 reliable: data.answer.reliable,
//                 validation_checks: data.answer.validation_checks,
//                 false_details_summary: data.answer.false_details_summary,
//                 error_reason: data.answer.error_reason,
//                 issue_detected: data.answer.issue_detected,
//                 original_question: data.answer.original_question,
//                 enriched_question: data.answer.enriched_question,
//                 sender: "bot"
//             };

//             chatHistory[selectedChat].messages.push(botMsg);
//             renderChat();
//         })
//         .catch(err => {
//             console.error('Lỗi gửi:', err);
//             chatHistory[selectedChat].messages.push({
//                 text: "❌ Không thể kết nối đến chatbot!",
//                 sender: "bot"
//             });
//             renderChat();
//         });
//     });

//     // Render danh sách cuộc trò chuyện
//     function renderChatHistory() {
//         chatHistoryList.innerHTML = '';
//         chatHistory.forEach((chat, index) => {
//             const li = document.createElement('li');
//             li.textContent = chat.name;
//             li.addEventListener("click", () => {
//                 selectedChat = index;
//                 renderChat();
//             });
//             chatHistoryList.appendChild(li);
//         });
//     }

//     // Render khung chat
//     function renderChat() {
//         const messages = chatHistory[selectedChat]?.messages || [];
//         chatBox.innerHTML = '';

//         messages.forEach(msg => {
//             const messageDiv = document.createElement('div');
//             messageDiv.classList.add(msg.sender === 'user' ? 'msg user' : 'msg bot');
//             messageDiv.innerHTML = `
//                 <p>${msg.text}</p>
//                 ${msg.sender === 'bot' ? `
//                     ${msg.issue_detected ? `<p><strong>Câu hỏi "${msg.original_question}" có thể chưa chính xác hoặc chứa lỗi logic.</strong></p>` : ''}
//                     ${msg.reliable !== undefined ? `<p><strong>Độ tin cậy:</strong> <span style="color:${msg.reliable ? 'green' : 'red'}">${msg.reliable ? 'ĐÁNG TIN CẬY' : 'KHÔNG ĐÁNG TIN'}</span></p>` : ''}
//                     ${msg.false_details_summary?.length ? `<ul>${msg.false_details_summary.map(detail => `<li>${detail}</li>`).join('')}</ul>` : ''}
//                     ${msg.documents?.length ? `<ul>${msg.documents.map(doc => `<li>${doc}</li>`).join('')}</ul>` : ''}
//                 ` : ''}
//             `;
//             chatBox.appendChild(messageDiv);
//         });

//         chatTitle.textContent = chatHistory[selectedChat]?.name || 'Cuộc trò chuyện mới';
//     }
// });
