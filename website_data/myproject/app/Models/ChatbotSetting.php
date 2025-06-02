<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotSetting extends Model
{
    protected $fillable = [
        'id',          // ✅ Thêm dòng này
        'model_name',
        'openai_api_key',
        'gemini_api_key',
    ];
}
