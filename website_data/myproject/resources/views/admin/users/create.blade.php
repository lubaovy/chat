@extends('admin.layout')

@section('content')
    <h2>Thêm người dùng</h2>
    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        <input type="text" name="name" placeholder="Tên người dùng" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>  {{-- Thêm dòng này --}}
        <button type="submit">Thêm</button>
    </form>
@endsection
