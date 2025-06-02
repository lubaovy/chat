@extends('admin.layout')

@section('content')
    <div class="container">
        <h2 class="my-3">✏️ Chỉnh sửa bài viết</h2>

        <!-- Hiển thị lỗi nếu có -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.posts.update', $post->id) }}" method="POST" class="bg-white p-4 shadow rounded">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="title">📌 Tiêu đề:</label>
                <input type="text" name="title" id="title" class="form-control" value="{{ $post->title }}" placeholder="Nhập tiêu đề bài viết" required>
            </div>

            <div class="form-group">
                <label for="content">📖 Nội dung:</label>
                <textarea name="content" id="content" class="form-control" rows="5" placeholder="Nhập nội dung bài viết" required>{{ $post->content }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Cập nhật bài viết
            </button>
            <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </form>
    </div>
@endsection
