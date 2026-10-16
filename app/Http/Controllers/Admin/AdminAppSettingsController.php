<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminAppSettingsController extends Controller
{
    /**
     * Show App & Splash Screen Settings page.
     */
    public function index()
    {
        $splashSettings = [
            'title' => Setting::get('app_splash_title', 'مرحباً بك في تطبيق عيادة د. يونس المرشدي'),
            'subtitle' => Setting::get('app_splash_subtitle', 'احجز استشارتك الطبية والنفسية بكل سهولة وسرية تامة'),
            'image' => Setting::get('app_splash_image', 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=1000&q=80'),
            'enabled' => Setting::get('app_splash_enabled', '1') === '1',
            'duration' => Setting::get('app_splash_duration', '3'),
        ];

        return view('admin.app_settings', compact('splashSettings'));
    }

    /**
     * Update App & Splash Screen Settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'app_splash_title' => 'required|string|max:191',
            'app_splash_subtitle' => 'nullable|string|max:255',
            'app_splash_duration' => 'nullable|integer|min:1|max:10',
            'app_splash_enabled' => 'nullable|boolean',
            'app_splash_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'app_splash_title.required' => 'عنوان شاشة البداية (Splash Screen) مطلوب.',
        ]);

        Setting::set('app_splash_title', $validated['app_splash_title']);
        Setting::set('app_splash_subtitle', $validated['app_splash_subtitle'] ?? '');
        Setting::set('app_splash_duration', (string) ($validated['app_splash_duration'] ?? '3'));
        Setting::set('app_splash_enabled', $request->has('app_splash_enabled') ? '1' : '0');

        if ($request->hasFile('app_splash_image')) {
            $oldImage = Setting::get('app_splash_image');
            if ($oldImage && !str_starts_with($oldImage, 'http')) {
                Storage::disk('public')->delete(str_replace('storage/', '', $oldImage));
            }

            $file = $request->file('app_splash_image');
            $filename = 'splash_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('app', $filename, 'public');
            Setting::set('app_splash_image', 'storage/' . $path);
        }

        return redirect()->route('admin.app-settings.index')->with('success', 'تم حفظ إعدادات شاشة البداية (Splash Screen) والتطبيق بنجاح.');
    }
}
