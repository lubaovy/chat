<!DOCTYPE html>
<html lang="vi">
@if(Auth::check())
    <script>
        window.user = @json(Auth::user());
    </script>
@else
    <script>
        window.user = null;
    </script>
@endif
<head>
    @php
        $settings = \App\Models\WebsiteSetting::first();
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

    

    @vite(['resources/js/app.js'])
</head>
<body>
    <div id="app">
        <chatbot :user='@json(Auth::user())'></chatbot>
    </div>
</body>
</html>
