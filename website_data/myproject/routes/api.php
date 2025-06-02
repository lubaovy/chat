<?php

use Illuminate\Support\Facades\Route;
use App\Models\ChatbotSetting;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ChatbotSettingController;
use App\Http\Controllers\Admin\UserController;

// Route::post('/chatbot/ask/', [ChatbotController::class, 'ask']);
Route::middleware('auth:sanctum')->post('/chatbot/ask', [ChatbotController::class, 'ask']);

// Route::get('/chatbot-settings', function () {
//     return ChatbotSetting::first(['openai_api_key', 'openai_model', 'google_api_key', 'google_model']);
// });
Route::get('/chatbot-settings', [ChatbotSettingController::class, 'getSetting']);

Route::middleware('auth:sanctum')->get('/remaining-questions', [UserController::class, 'getRemainingQuestions']);