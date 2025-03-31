<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}" defer></script>
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        @include('admin.sidebar') <!-- Sidebar luôn hiển thị -->

        <div class="content-wrapper">
            <section class="content">
                @yield('content') <!-- Nội dung động sẽ thay đổi -->
            </section>
        </div>
    </div>
</body>
</html>
