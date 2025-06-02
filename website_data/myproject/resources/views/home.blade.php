<!DOCTYPE html>
<html lang="vi">
<head>
    @php
        $settings = \App\Models\WebsiteSetting::first() ?? new \App\Models\WebsiteSetting;
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="title" content="Chatbot Lịch Sử Việt Nam">
    <meta name="description" content="{{ $settings->meta_description ?? 'Trợ lý lịch sử chính xác, xác thực' }}">
    <meta name="keywords" content="{{ $settings->meta_keywords ?? 'chatbot lịch sử, lịch sử Việt Nam, AI giáo dục' }}">
    <meta name="author" content="Tên bạn hoặc tổ chức">
    <link rel="icon" type="image/x-icon" href="{{ asset($settings->favicon ?? 'favicon.ico') }}" />
    <title>{{ $settings->site_title ?? 'Chatbot Lịch Sử Việt Nam' }}</title>

    <!-- Open Graph & Twitter Card -->
    <meta property="og:title" content="Chatbot Lịch Sử Việt Nam">
    <meta property="og:description" content="Khám phá các sự kiện, nhân vật lịch sử Việt Nam với chatbot thông minh.">
    <meta property="og:image" content="{{ asset($settings->og_image ?? 'images/share.jpg') }}" />
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Chatbot Lịch Sử Việt Nam">
    <meta name="twitter:description" content="Khám phá các sự kiện, nhân vật lịch sử Việt Nam với chatbot thông minh.">
    <meta name="twitter:image" content="{{ asset('images/chatbot-thumbnail.jpg') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f2f6ff;
            margin: 0;
            padding: 0;
            overflow-x: hidden; /* Giúp tránh trượt ngang */
        }

        /* Phần đăng nhập với hình nền đẹp mắt */
        .login-section {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            min-height: 50vh;
            background: linear-gradient(to right, rgba(255, 204, 0, 0.6), rgba(255, 87, 34, 0.6)), url('{{ asset('assets/images/others/Trống_đồng_Đông_Sơn.svg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        /* Lớp phủ mờ cho hình nền */
        .login-section::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: rgba(0, 0, 0, 0.3); /* Để thêm hiệu ứng tối cho nền */
            z-index: -1;
        }

        /* Phần đăng nhập */
        .login-box {
            background: rgba(255, 255, 255, 0.85); /* Màu nền hộp login trong suốt */
            border-radius: 1rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 450px;
            width: 100%;
            z-index: 1; /* Đảm bảo nội dung luôn ở trên cùng */
            transition: transform 0.3s ease-in-out; /* Hiệu ứng khi di chuột */
            animation: fadeInUp 1s ease forwards;
        }

        /* Hiệu ứng hover cho form login */
        .login-box:hover {
            transform: translateY(-5px);
        }

        /* Hiệu ứng hover cho các nút */
        .btn-primary, .btn-outline-secondary {
            transition: background-color 0.3s ease-in-out, color 0.3s ease-in-out;
        }

        .btn-primary:hover, .btn-outline-secondary:hover {
            background-color: #f76c5e;
            color: white;
        }

        /* Card min họa AI */
        .feature-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease-in-out;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .intro-section {
            background-color: #ffffff;
            padding: 4rem 0;
        }

        .intro-section h2 {
            color: #c8102e;
            font-weight: bold;
            margin-bottom: 1.5rem;
        }

        .intro-section p {
            font-size: 1.05rem;
            margin-bottom: 1rem;
            line-height: 1.6;
            color: #333333;
        }

        .intro-section ul {
            padding-left: 1.2rem;
            margin-bottom: 2rem;
        }

        .intro-section ul li {
            margin-bottom: 0.5rem;
        }

        .intro-section .intro-image {
            max-height: 360px;
            object-fit: cover;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }

        .intro-section .btn-primary-custom {
            background-color: #c8102e;
            color: white;
            border: none;
        }

        .intro-section .btn-primary-custom:hover {
            background-color: #a10c24;
            color: white;
        }

        .features-section {
            position: relative;
            background: url('{{ asset('assets/images/others/dinh.jpg') }}') center/cover no-repeat;
            background-attachment: fixed;
            padding: 80px 20px;
            z-index: 1;
        }

        .features-section::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.4); /* lớp phủ trắng trong suốt */
            z-index: 2;
        }

        .features-section .container {
            position: relative;
            z-index: 3; /* để nội dung nằm trên lớp phủ */
        }

        .section-title {
            text-align: center;
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 50px;
            color: #b20000;
        }

        .features-grid {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }

        .feature-card {
            background: linear-gradient(135deg, #ffe4e1, #fff1cc);
            border: 2px solid #da251d;
            border-radius: 24px;
            padding: 30px 25px;
            width: 300px;
            text-align: center;
            box-shadow: 0 8px 24px rgba(218, 37, 29, 0.15);
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-6px);
            background: linear-gradient(135deg, #ffd6d6, #fff3aa);
        }

        .feature-card h5 {
            font-size: 1.2rem;
            font-weight: 600;
            color: #b20000;
            margin-bottom: 12px;
        }

        .feature-card p {
            font-size: 0.95rem;
            color: #333;
            line-height: 1.6;
        }

        .pricing-section {
            background: linear-gradient(to right, #f8bcbc, #fff5b7); /* đỏ nhạt -> vàng nhạt */
            padding: 80px 20px;
            border-radius: 12px;
        }

        .pricing-card {
            border-radius: 16px;
            transition: transform 0.3s ease;
        }

        .pricing-card:hover {
            transform: translateY(-5px);
        }

        @keyframes gentle-shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-2px); }
            75% { transform: translateX(2px); }
        }

        .flash-highlight {
            animation: gentle-shake 1s ease-in-out 2;
        }

        /* Footer */
        .footer {
            background-color: #f8f9fa;
            padding: 40px 0;
            text-align: center;
            font-size: 0.9rem;
            color: #666;
            position: relative;
            z-index: 1;
        }
        .full-screen-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
    </style>
</head>

<script>
    function scrollToLogin() {
        const loginForm = document.getElementById('login-form');
        if (loginForm) {
            loginForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
            loginForm.classList.add('flash-highlight');
            setTimeout(() => {
                loginForm.classList.remove('flash-highlight');
            }, 2000); // Xóa hiệu ứng sau 2s
        }
    }
</script>

<body>
<!-- PHẦN 1: Đăng nhập -->
<section class="login-section">
  <div class="container">
    <div class="row align-items-center">
      <!-- Form đăng nhập -->
      <div class="col-lg-6 mb-4 mb-lg-0">
        <div class="login-box p-4 rounded-4 shadow bg-white">
          <div class="text-center mb-4">
            <img src="{{ asset('assets/images/logo/history-book.png') }}" alt="Logo" height="60" class="mb-2" />
            <h4 class="fw-bold text-primary">Trợ lý AI Lịch sử Việt Nam</h4>
            <p class="text-muted">Đăng nhập để khám phá kho kiến thức lịch sử!</p>
          </div>

          <!-- Form đăng nhập -->
          <form id="login-form" method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
              <input type="email" name="email" class="form-control" placeholder="Nhập địa chỉ email" required>
            </div>
            <div class="mb-3">
              <input type="password" name="password" class="form-control" placeholder="Mật khẩu" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
          </form>

          <div class="text-center mt-3">
            <a href="{{ route('register') }}">Chưa có tài khoản? Đăng ký</a>
          </div>
          <!-- <div class="text-center mt-2">
            <a href="/chatbot" class="btn btn-outline-secondary btn-sm">Dùng thử không cần tài khoản</a>
          </div> -->
        </div>
      </div>

      <!-- Minh họa AI trả lời -->
      <div class="col-lg-6 d-none d-lg-block">
        <div class="bg-gradient p-4 rounded-4 shadow-sm" style="background-color: rgba(255,255,255,0.6);">
          <p class="fw-semibold">Ví dụ:</p>
          <div class="chat-box border p-3 rounded-3 bg-white shadow-sm">
            <div class="user-question mb-2">
              <strong>👤 Người dùng:</strong> Ai là người chỉ huy trận Ngọc Hồi - Đống Đa?
            </div>
            <div class="ai-answer">
              <strong>🤖 AI Lịch sử:</strong> Người chỉ huy trận Ngọc Hồi - Đống Đa là vua Quang Trung (Nguyễn Huệ), ông đánh bại quân Thanh vào năm 1789.
            </div>
          </div>
          <div class="mt-3 text-muted small">✦ Kiến thức trích từ sách giáo khoa và sử liệu chính thống.</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- PHẦN 2: Giới thiệu -->
<div class="container intro-section">
    <div class="row align-items-center">
        <div class="col-md-4 mb-4 mb-md-0 d-flex justify-content-center">
            <img src="{{ asset('assets/images/others/download (1).jpg') }}"
                 alt="Chatbot Lịch sử Việt Nam"
                 class="img-fluid intro-image">
        </div>
        <div class="col-md-8">
            <h2>Giới thiệu về Chatbot Lịch Sử Việt Nam</h2>

            <p>
                Trong thời đại số, việc tiếp cận lịch sử không chỉ còn bó hẹp trong sách giáo khoa. Chatbot Lịch Sử Việt Nam mang đến một phương tiện học tập mới mẻ, gần gũi và dễ sử dụng cho học sinh, giáo viên và những ai yêu thích lịch sử dân tộc.
            </p>

            <p>
                Hệ thống được xây dựng trên nền tảng trí tuệ nhân tạo RAG kết hợp với công nghệ kiểm định thông tin. Chatbot không chỉ cung cấp câu trả lời từ các nguồn chính thống mà còn có khả năng tự phát hiện và điều chỉnh khi gặp câu hỏi sai hoặc mâu thuẫn.
            </p>

            <p>Một số điểm nổi bật của hệ thống:</p>
            <ul>
                <li>Giao diện đơn giản, thân thiện với người dùng</li>
                <li>Câu trả lời nhanh, trích dẫn rõ nguồn gốc</li>
                <li>Phát hiện và xử lý các câu hỏi sai logic để đảm bảo độ tin cậy</li>
            </ul>

            <div class="mt-4">
                <a class="btn btn-primary-custom me-2" onclick="scrollToLogin()">Bắt đầu khám phá</a>
                <!-- <a href="/chatbot" class="btn btn-outline-secondary">Dùng thử ngay</a> -->
            </div>
        </div>
    </div>
</div>

<!-- PHẦN 3: Tính năng nổi bật -->
<section class="features-section">
  <div class="container">
    <h2 class="section-title">Tính Năng Nổi Bật</h2>
    <div class="features-grid">
      <div class="feature-card">
        <h5>Tra Cứu Sự Kiện Lịch Sử</h5>
        <p>Hiển thị các sự kiện lịch sử quan trọng theo thời gian và bối cảnh cụ thể.</p>
      </div>
      <div class="feature-card">
        <h5>Phân Tích Nhân Vật Lịch Sử</h5>
        <p>Trình bày tiểu sử, đóng góp và vai trò của nhân vật trong dòng chảy lịch sử Việt Nam.</p>
      </div>
      <div class="feature-card">
        <h5>Kiểm Định Thông Tin Tự Động</h5>
        <p>Mỗi câu trả lời đều được so sánh với tài liệu chuẩn để đảm bảo độ chính xác tối đa.</p>
      </div>
      <div class="feature-card">
        <h5>Phát Hiện Câu Hỏi Sai Logic</h5>
        <p>Hệ thống nhận diện các câu hỏi sai lệch như “Lê Lợi đánh Lê Lai” và đưa ra gợi ý sửa phù hợp.</p>
      </div>
      <div class="feature-card">
        <h5>Trích Dẫn Nguồn Rõ Ràng</h5>
        <p>Các thông tin luôn đi kèm tài liệu dẫn chứng để người dùng dễ kiểm tra và đối chiếu.</p>
      </div>
    </div>
  </div>
</section>

<!-- PHẦN 4: Báo giá -->
<div class="container my-5 full-screen-section pricing-section text-white">
    <h2 class="text-center mb-4" style="font-weight: 700; font-size: 2rem;">Bảng Giá Sử Dụng Chatbot</h2>
    <div class="row justify-content-center g-4">
        <!-- GÓI MIỄN PHÍ -->
        <div class="col-md-6">
            <div class="card pricing-card bg-danger text-white h-100 shadow-lg border-0 rounded-4">
                <div class="card-body text-center p-5">
                    <h5 class="card-title mb-3" style="font-weight: 700; font-size: 1.5rem;">Dùng Thử</h5>
                    <p class="card-text mb-2">Sử dụng tối đa <strong>5 lượt/ngày</strong> khi đăng nhập.</p>
                    <p class="card-text mb-4">Phù hợp với học sinh, người học cơ bản.</p>
                    <a class="btn btn-light px-4 py-2 shadow-sm text-danger fw-bold rounded" onclick="scrollToLogin()">
                        Dùng thử
                    </a>
                </div>
            </div>
        </div>
        <!-- GÓI NÂNG CAO -->
        <div class="col-md-6">
            <div class="card pricing-card bg-warning text-dark h-100 shadow-lg border-0 rounded-4">
                <div class="card-body text-center p-5">
                    <h5 class="card-title mb-3" style="font-weight: 700; font-size: 1.5rem;">Gói Nâng Cao</h5>
                    <p class="card-text mb-2">Truy cập <strong>không giới hạn</strong> chỉ với <strong>2 USD/tháng</strong>.</p>
                    <p class="card-text mb-4">Phù hợp với giáo viên, nhà nghiên cứu, người yêu sử.</p>
                    <a href="{{ route('pricing') }}" class="btn btn-dark px-4 py-2 shadow-sm text-warning fw-bold rounded">
                        Nâng cấp
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="footer">
    <div>© 2025 Chatbot Lịch Sử Việt Nam. Phát triển bởi nhóm Nghiên cứu & AI.</div>
</footer>

</body>
</html>
