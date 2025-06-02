<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng Giá - Chatbot Lịch Sử Việt Nam</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 text-gray-800">

    <div class="max-w-4xl mx-auto px-4 py-12">
        <h1 class="text-4xl font-bold text-center mb-10">Bảng Giá Sử Dụng Chatbot</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- GÓI MIỄN PHÍ -->
            <div class="bg-white shadow rounded-lg p-6 text-center">
                <h2 class="text-xl font-bold mb-4">Miễn phí</h2>
                <p class="text-3xl font-extrabold mb-4">0₫</p>
                <ul class="mb-6 space-y-2 text-gray-600">
                    <li>✔ 5 câu hỏi/ngày</li>
                    <li>✘ Không lưu lịch sử</li>
                    <li>✘ Không truy cập tài liệu nâng cao</li>
                </ul>
                <a href="{{ route('register') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Dùng thử</a>
            </div>

            <!-- GÓI NÂNG CAO -->
            <div class="bg-yellow-100 border-2 border-yellow-500 shadow-lg rounded-lg p-6 text-center">
                <h2 class="text-xl font-bold mb-4">Gói Nâng cao</h2>
                <p class="text-3xl font-extrabold mb-4">49.000₫ / tháng</p>
                <ul class="mb-6 space-y-2 text-gray-700">
                    <li>✔ Không giới hạn câu hỏi</li>
                    <li>✔ Truy cập tài liệu nâng cao</li>
                    <li>✔ Lưu lịch sử trò chuyện</li>
                    <li>✔ Hỗ trợ ưu tiên</li>
                </ul>
                <a href="{{ route('login') }}" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Nâng cấp</a>
            </div>
        </div>
    </div>

</body>
</html>
