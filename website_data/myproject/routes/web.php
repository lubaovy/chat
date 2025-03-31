<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChatbotController;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\FileController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
});
Auth::routes();

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

    Route::resource('users', UserController::class);
    Route::resource('posts', PostController::class);
    Route::resource('pages', PageController::class);
    Route::resource('files', FileController::class);
});
Auth::routes();

Route::post('/chatbot/ask', [ChatbotController::class, 'ask']);
