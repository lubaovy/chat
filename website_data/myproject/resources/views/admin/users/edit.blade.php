@extends('admin.layout')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">✏️ Chỉnh sửa Người Dùng</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card p-4 shadow-sm">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Tên người dùng:</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ $user->name }}" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ $user->email }}" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu mới:</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Nhập mật khẩu mới (bỏ trống nếu không đổi)">
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Xác nhận mật khẩu:</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Nhập lại mật khẩu mới">
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Chọn quyền:</label>
                <select id="role" name="role" class="form-select" required>
                    <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">💾 Cập nhật</button>
        </form>
    </div>
</div>
@endsection
