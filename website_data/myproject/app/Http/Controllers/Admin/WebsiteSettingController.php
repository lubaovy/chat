<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class WebsiteSettingController extends Controller
{
    public function edit()
    {
        $setting = WebsiteSetting::first();
        return view('admin.website_settings.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = WebsiteSetting::firstOrCreate([]);
        $setting->update($request->only([
            'site_title', 'meta_description', 'meta_keywords'
        ]));

        if ($request->hasFile('favicon')) {
            // Tạo thư mục nếu chưa có
            Storage::makeDirectory('public/images');

            $faviconPath = $request->file('favicon')->store('images', 'public');
            $setting->favicon = 'storage/images/' . basename($faviconPath);
        }

        if ($request->hasFile('og_image')) {
            // Tạo thư mục nếu chưa có
            Storage::makeDirectory('public/images');

            $ogImagePath = $request->file('og_image')->store('images', 'public');
            $setting->og_image = 'storage/images/' . basename($ogImagePath);
        }

        $setting->save();
        return redirect()->back()->with('success', 'Cập nhật thành công!');
    }
}
