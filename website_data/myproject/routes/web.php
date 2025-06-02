<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ChatbotSettingController;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\FileController;
use App\Http\Controllers\Admin\WebsiteSettingController;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Auth;


Route::get('/', function () {
    return view('home');
});
// Route::get('/login', function () {
//     return view('login'); // Trang đăng nhập
// });

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
});
// Auth::routes();

Route::get('/test-fastapi', function () {
    $response = Http::get('http://127.0.0.1:8000/');  // Gọi FastAPI

    return $response->json();  // Trả về kết quả từ FastAPI
});

// Route::get('/dashboard', function () {
//     return view('users'); // Tải giao diện users.blade.php
// });

Route::prefix('admin')->middleware(['auth'])->name('admin.')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::middleware([IsAdmin::class])->group(function () {
        Route::resource('users', UserController::class);
    });
    Route::resource('posts', PostController::class);
    Route::resource('pages', PageController::class);
    Route::resource('files', FileController::class);

    Route::get('/website-settings/edit', [WebsiteSettingController::class, 'edit'])->name('website_settings.edit');
    Route::put('/website-settings/update', [WebsiteSettingController::class, 'update'])->name('website_settings.update');
});
// Auth::routes();

Route::post('/chatbot/ask/', [ChatbotController::class, 'ask']);
// Route::middleware(['auth:sanctum'])->group(function () {
//     Route::post('/chatbot/ask/', [ChatbotController::class, 'ask']);
//     Route::get('/api/me/', function (Request $request) {
//         return response()->json([
//             'user' => $request->user()
//         ]);
//     });
// });
// Route::middleware(['check.question.limit'])->group(function () {
//     Route::post('/chatbot/ask/', [ChatbotController::class, 'ask']);
// });

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/chatbot-settings', [ChatbotSettingController::class, 'edit'])->name('chatbot.settings');
    Route::post('/admin/chatbot-settings', [ChatbotSettingController::class, 'update'])->name('chatbot.settings.update');
});

Route::get('/chatbot', function () {
    return view('chatbot');
});

Route::get('/chatbot-settings', [ChatbotSettingController::class, 'getSetting']);

// Route để lấy cookie CSRF (có sẵn trong Sanctum, hoặc tự tạo nếu muốn)
Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
});

Route::get('/pricing', function () {
    return view('pricing');
})->name('pricing');

Route::get('/remaining-questions', function () {
    $user = Auth::user();

    if (!$user) {
        return response()->json(['error' => 'Chưa đăng nhập'], 401);
    }

    return response()->json([
        'remaining' => $user->remaining_questions,
    ]);
});