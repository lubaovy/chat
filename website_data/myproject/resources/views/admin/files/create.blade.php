@extends('admin.layout')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">📤 Upload File</h2>

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
        <form action="{{ route('admin.files.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="file" class="form-label">Chọn file:</label>
                <input type="file" name="file" id="file" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">⬆ Upload</button>
        </form>
    </div>
</div>
@endsection
