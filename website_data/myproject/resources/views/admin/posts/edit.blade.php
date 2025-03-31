@extends('admin.layout')

@section('content')
    <h2>Chỉnh sửa bài viết</h2>

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

    <form action="{{ route('admin.posts.update', $post->id) }}" method="POST">
        @csrf
        @method('PUT')

        <label>Tiêu đề:</label>
        <input type="text" name="title" value="{{ $post->title }}" required>

        <label>Nội dung:</label>
        <textarea name="content" rows="5" required>{{ $post->content }}</textarea>

        <button type="submit">Cập nhật</button>
    </form>

    <a href="{{ route('admin.posts.index') }}">Quay lại danh sách</a>
@endsection
