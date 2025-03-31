<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chatbot Lịch Sử</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h2>Chatbot Lịch Sử Việt Nam</h2>
    <input type="text" id="question" placeholder="Nhập câu hỏi...">
    <button onclick="sendMessage()">Gửi</button>
    <div id="chatbox"></div>

    <script>
        function sendMessage() {
            var question = $("#question").val();
            $("#chatbox").append("<p><strong>Bạn:</strong> " + question + "</p>");

            $.ajax({
                url: "/api/chatbot",
                type: "POST",
                data: { question: question },
                success: function(response) {
                    $("#chatbox").append("<p><strong>Bot:</strong> " + response.reply + "</p>");
                }
            });
        }
    </script>
</body>
</html>
