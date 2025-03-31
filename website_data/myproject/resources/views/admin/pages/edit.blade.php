@extends('admin.layout')

@section('content')
    <h2>Chỉnh sửa trang</h2>

    @if ($errors->any())
        <div style="color: red;">
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

        <label>Tiêu đề:</label>
        <input type="text" name="title" value="{{ $page->title }}" required>

        <label>Nội dung:</label>
        <textarea name="content" rows="5" required>{{ $page->content }}</textarea>

        <button type="submit">Cập nhật</button>
    </form>

    <a href="{{ route('admin.pages.index') }}">Quay lại danh sách</a>
@endsection
