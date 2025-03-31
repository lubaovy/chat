@extends('admin.layout')

@section('content')
    <h2>Chỉnh sửa người dùng</h2>
    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <input type="text" name="name" value="{{ $user->name }}" required>
        <input type="email" name="email" value="{{ $user->email }}" required>

        <input type="password" name="password" placeholder="Nhập mật khẩu mới (bỏ trống nếu không đổi)">
        
        <button type="submit">Cập nhật</button>
    </form>
@endsection
