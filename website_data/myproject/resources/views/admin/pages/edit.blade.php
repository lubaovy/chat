@extends('admin.layout')

@section('content')
    <div class="container">
        <h2 class="my-3">✏️ Chỉnh sửa trang</h2>

        <!-- Hiển thị lỗi nếu có -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.pages.update', $page->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="title">📌 Tiêu đề:</label>
                <input type="text" id="title" name="title" class="form-control" value="{{ $page->title }}" required>
            </div>

            <div class="form-group">
                <label for="content">📝 Nội dung:</label>
                <textarea id="content" name="content" class="form-control" rows="5" required>{{ $page->content }}</textarea>
            </div>

            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Cập nhật
            </button>
            <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </form>
    </div>
@endsection
