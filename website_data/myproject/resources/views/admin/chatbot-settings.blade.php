@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h2>Cài đặt Mô hình Chatbot</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form action="{{ route('chatbot.settings.update') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="model_name">Chọn Mô hình</label>
            <select name="model_name" id="model_name" class="form-control" onchange="toggleAPIKeyInput()">
                <option value="openai" {{ old('model_name', $setting->model_name ?? '') == 'openai' ? 'selected' : '' }}>OpenAI</option>
                <option value="gemini" {{ old('model_name', $setting->model_name ?? '') == 'gemini' ? 'selected' : '' }}>Gemini</option>
            </select>
        </div>

        <div class="mb-3" id="openai-key" style="display: none;">
            <label for="openai_api_key" class="form-label">OpenAI API Key:</label>
            <input type="text" name="openai_api_key" class="form-control" value="{{ old('openai_api_key', $setting->openai_api_key ?? '') }}">
        </div>

        <div class="mb-3" id="gemini-key" style="display: none;">
            <label for="gemini_api_key" class="form-label">Gemini API Key:</label>
            <input type="text" name="gemini_api_key" class="form-control" value="{{ old('gemini_api_key', $setting->gemini_api_key ?? '') }}">
        </div>

        <button type="submit" class="btn btn-primary mt-4">Lưu Cài đặt</button>
    </form>

    <script>
        function toggleAPIKeyInput() {
            const selected = document.getElementById('model_name').value;
            document.getElementById('openai-key').style.display = selected === 'openai' ? 'block' : 'none';
            document.getElementById('gemini-key').style.display = selected === 'gemini' ? 'block' : 'none';
        }
        toggleAPIKeyInput(); // Gọi lần đầu khi load
    </script>
</div>
@endsection
