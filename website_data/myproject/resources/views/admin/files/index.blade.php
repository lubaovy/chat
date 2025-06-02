@extends('admin.layout')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">📂 File Manager</h2>

    <a href="{{ route('admin.files.create') }}" class="btn btn-primary mb-3">📤 Upload New File</a>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Preview</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($files as $file)
                <tr>
                    <td>{{ $file->id }}</td>
                    <td>{{ $file->name }}</td>
                    <td>
                        @if (str_starts_with($file->type, 'image/'))
                            <img src="{{ asset('storage/' . $file->path) }}" width="100" class="img-thumbnail">
                        @else
                            <a href="{{ asset('storage/' . $file->path) }}" class="btn btn-info btn-sm" target="_blank">🔗 View</a>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('admin.files.destroy', $file->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this file?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">🗑 Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
