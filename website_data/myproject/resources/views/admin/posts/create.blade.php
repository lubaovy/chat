@extends('admin.layout')

@section('content')
    <h2>Thêm bài viết mới</h2>

    <!-- Hiển thị lỗi nếu có -->
    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.posts.store') }}" method="POST">
        @csrf
        <label>Tiêu đề:</label>
        <input type="text" name="title" required>

        <label>Nội dung:</label>
        <textarea name="content" rows="5" required></textarea>

        <button type="submit">Lưu</button>
    </form>

    <a href="{{ route('admin.posts.index') }}">Quay lại danh sách</a>
@endsection
