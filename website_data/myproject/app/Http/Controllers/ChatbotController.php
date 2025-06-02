<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class ChatbotController extends Controller
{
    public function ask(Request $request)
    {
        $user = $request->user();

        // Nếu chưa đăng nhập → lỗi
        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập để sử dụng chatbot.',
                'require_login' => true,
                'action' => [
                    'text' => 'Đăng nhập',
                    'url' => route('login'),
                ]
            ], 403);
        }

        // ✅ Reset nếu chưa reset hôm nay
        if (!$user->last_reset_at || !Carbon::parse($user->last_reset_at)->isToday()) {
            $user->remaining_questions = 5;
            $user->last_reset_at = now();
            $user->save();
        }

        \Log::info("User {$user->id} has remaining_questions before: {$user->remaining_questions}");

        // Kiểm tra lượt còn lại
        if ($user->remaining_questions <= 0) {
            return response()->json([
                'message' => 'Bạn đã hết lượt hỏi. Vui lòng nâng cấp tài khoản.',
                'require_upgrade' => true,
                'title' => 'Hết lượt truy vấn',
                'action' => [
                    'text' => 'Nâng cấp tài khoản',
                    'url' => route('pricing'),
                ]
            ], 403);
        }

        $question = $request->input('question');

        // Gửi tới API chatbot
        $response = $this->sendToChatbot($question);

        // Trừ lượt
        $user->decrement('remaining_questions');
        $user->refresh();
        \Log::info("User {$user->id} has remaining_questions after: {$user->remaining_questions}"); 

        return response()->json([
            'answer' => $response['answer'] ?? 'Không có câu trả lời.',
            'remaining' => $user->remaining_questions,
        ]);
    }

    private function sendToChatbot($question)
    {
        try {
            $response = Http::timeout(360) // tăng timeout lên 60 giây
                ->withHeaders([
                    'API-Key' => env('API_KEY', 'Lk13bVFH1eyy0pz1LBpgmt4iUNDYQAY6'),
                ])
                ->post('http://127.0.0.1:55013/chatbot/ask/', [
                    'question' => $question,
                ]);

            return $response->json(); // Trả về luôn JSON từ FastAPI

        } catch (\Exception $e) {
            return [
                'answer' => [
                    'generation' => '❌ Lỗi khi gọi chatbot. Vui lòng thử lại sau.',
                    'documents' => [],
                    'reliable' => false,
                    'validation_checks' => [],
                    'false_details_summary' => [],
                    'error_reason' => $e->getMessage(),
                    'issue_detected' => true,
                    'original_question' => $question,
                    'enriched_question' => null,
                ]
            ];
        }
    }
}
