<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LimitQuestions
{
    public function handle(Request $request, Closure $next)
    {
        // // Nếu chưa đăng nhập (user khách) → chỉ 1 câu
        // if (!Auth::check()) {
        //     $guestCount = Session::get('guest_questions', 0);
        //     if ($guestCount >= 1) {
        //         return response()->json([
        //             'message' => 'Bạn chỉ được hỏi 1 câu khi dùng thử. Vui lòng đăng nhập để tiếp tục.',
        //             'require_login' => true
        //         ], 403);
        //     }
        //     Session::put('guest_questions', $guestCount + 1);
        // } else {
        //     // Nếu đã đăng nhập → tối đa 5 câu mỗi ngày
        //     $user = Auth::user();
        //     $key = 'user_questions_' . $user->id;
        //     $data = cache()->get($key, ['count' => 0, 'date' => now()->toDateString()]);

        //     if ($data['date'] !== now()->toDateString()) {
        //         // Reset nếu sang ngày mới
        //         $data = ['count' => 0, 'date' => now()->toDateString()];
        //     }

        //     if ($data['count'] >= 5 && $user->plan !== 'premium') {
        //         return response()->json([
        //             'message' => 'Bạn đã dùng hết 5 lượt hỏi hôm nay. Vui lòng nâng cấp gói để tiếp tục.',
        //             'require_upgrade' => true
        //         ], 403);
        //     }

        //     // Tăng số câu đã hỏi
        //     $data['count']++;
        //     cache()->put($key, $data, now()->addDay());
        // }

        // return $next($request);
            $user = $request->user(); // đảm bảo đã đăng nhập

        if ($user->remaining_questions <= 0) {
            return response()->json(['message' => 'Bạn đã hết lượt hỏi'], 429);
        }

        $user->decrement('remaining_questions');

        return $next($request);
    }
}
