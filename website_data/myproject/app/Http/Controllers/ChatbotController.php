<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    public function ask(Request $request)
    {
        $response = Http::asForm()->withHeaders([
            'Authorization' => 'Bearer ' . env('API_KEY'), // Đúng chuẩn API key
        ])->post(env('FASTAPI_URL') . '/chatbot/ask/', [  // Đúng endpoint
            'question' => $request->input('message'), // Đúng tham số
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Không thể kết nối với chatbot'], 500);
        }

        return response()->json($response->json());
    }
}
