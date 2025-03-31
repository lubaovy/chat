@extends('admin.layout')

@section('content')
<h2>File Manager</h2>

<a href="{{ route('admin.files.create') }}">Upload New File</a>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Preview</th>
        <th>Action</th>
    </tr>
    @foreach ($files as $file)
    <tr>
        <td>{{ $file->id }}</td>
        <td>{{ $file->name }}</td>
        <td>
            @if (str_starts_with($file->type, 'image/'))
                <img src="{{ asset('storage/' . $file->path) }}" width="100">
            @else
                <a href="{{ asset('storage/' . $file->path) }}" target="_blank">View</a>
            @endif
        </td>
        <td>
            <form action="{{ route('admin.files.destroy', $file->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit">Delete</button>
            </form>
        </td>
    </tr>
    @endforeach
</table>
@endsection
