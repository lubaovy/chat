<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteSetting extends Model
{
    protected $fillable = [
        'site_title',
        'meta_description',
        'meta_keywords',
        'favicon',
        'og_image',
    ];
}
