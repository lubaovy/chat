@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Cấu hình Website</h1>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form action="{{ route('admin.website_settings.update') }}" method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Site Title</label>
            <input type="text" name="site_title" class="form-control" value="{{ $setting->site_title }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Meta Description</label>
            <input type="text" name="meta_description" class="form-control" value="{{ $setting->meta_description }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Meta Keywords</label>
            <input type="text" name="meta_keywords" class="form-control" value="{{ $setting->meta_keywords }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Favicon</label>
            <input type="file" name="favicon" class="form-control">
            @if($setting->favicon)
                <div class="mt-2">
                    <img src="{{ asset($setting->favicon) }}" width="32" alt="Favicon">
                </div>
            @endif
        </div>

        <div class="mb-3">
            <label class="form-label">OG Image</label>
            <input type="file" name="og_image" class="form-control">
            @if($setting->og_image)
                <div class="mt-2">
                    <img src="{{ asset($setting->og_image) }}" width="100" alt="OG Image">
                </div>
            @endif
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật</button>
    </form>
</div>
@endsection
