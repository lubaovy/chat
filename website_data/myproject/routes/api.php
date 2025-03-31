<?php

use App\Http\Controllers\ChatbotController;

Route::post('/chatbot/ask', [ChatbotController::class, 'ask']);

