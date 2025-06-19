<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}" defer></script>
    <style>
        /* CSS tùy chỉnh để fix lỗi */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        
        .wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        .content-wrapper {
            flex: 1;
            padding-bottom: 0 !important; /* Loại bỏ padding dưới */
        }
        
        .content {
            padding: 20px;
            min-height: calc(100vh - (navbar height + footer height)); 
            /* Thay thế bằng giá trị thực tế */
        }
        
        /* Ẩn footer nếu không cần thiết */
        .main-footer {
            display: none;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        @include('admin.sidebar') <!-- Thanh menu bên trái -->
        <div class="content-wrapper">
            @yield('content') <!-- Nội dung động -->
        </div>
    </div>
</body>
</html>
