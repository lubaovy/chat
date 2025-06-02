<?php

namespace App\Http\Controllers;

use App\Models\ChatbotSetting;
use Illuminate\Http\Request;

class ChatbotSettingController extends Controller
{
    public function edit()
    {
        $setting = ChatbotSetting::first();
        return view('admin.chatbot-settings', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'model_name' => 'required|string',
            'openai_api_key' => 'nullable|string',
            'gemini_api_key' => 'nullable|string',
        ]);
    
        $data = [
            'model_name' => $request->model_name,
            'openai_api_key' => $request->openai_api_key,
            'gemini_api_key' => $request->gemini_api_key,
        ];
    
        ChatbotSetting::updateOrCreate(['id' => 1], $data);
    
        return redirect()->route('chatbot.settings')->with('success', 'Cập nhật thành công!');
    }

    public function getSetting()
    {
        // Lấy thông tin cấu hình chatbot từ cơ sở dữ liệu
        $settings = ChatbotSetting::first(['openai_api_key', 'gemini_api_key', 'model_name']); // Lấy đúng các trường cần thiết
    
        // Kiểm tra nếu không có cấu hình
        if (!$settings) {
            return response()->json(['error' => 'Cấu hình không tìm thấy.'], 404);
        }
    
        // Kiểm tra loại model đã chọn và trả về api_key tương ứng
        if ($settings->model_name == 'openai') {
            $api_key = $settings->openai_api_key;
        } elseif ($settings->model_name == 'gemini') {
            $api_key = $settings->gemini_api_key;
        } else {
            return response()->json(['error' => 'Không xác định được loại model.'], 400);
        }
    
        // Trả về API key dưới tên `api_key`
        return response()->json([
            'model_name' => $settings->model_name,
            'api_key' => $api_key,  // Trả về api_key tương ứng
        ]);
    }
    
}
