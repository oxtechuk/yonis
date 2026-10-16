<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSlider;
use App\Models\Doctor;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminSliderController extends Controller
{
    /**
     * Display a listing of app sliders.
     */
    public function index()
    {
        $sliders = AppSlider::orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        $doctors = Doctor::active()->get();
        $services = Service::where('is_active', true)->get();

        return view('admin.sliders', compact('sliders', 'doctors', 'services'));
    }

    /**
     * Store a newly created slider.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'title' => 'nullable|string|max:191',
            'subtitle' => 'nullable|string|max:191',
            'action_type' => 'required|in:none,doctor,service,url',
            'action_value' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ], [
            'image.required' => 'صورة السلايدر مطلوبة.',
            'image.image' => 'يجب أن يكون الملف صورة صالحة.',
        ]);

        $file = $request->file('image');
        $filename = 'slider_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('sliders', $filename, 'public');

        AppSlider::create([
            'image_path' => 'sliders/' . $filename,
            'title' => $validated['title'] ?? null,
            'subtitle' => $validated['subtitle'] ?? null,
            'action_type' => $validated['action_type'],
            'action_value' => $validated['action_value'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->has('is_active') ? (bool)$request->input('is_active') : true,
        ]);

        return redirect()->route('admin.sliders.index')->with('success', 'تم إضافة شريحة السلايدر بنجاح وستظهر في الصفحة الرئيسية للتطبيق.');
    }

    /**
     * Update the specified slider.
     */
    public function update(Request $request, $id)
    {
        $slider = AppSlider::findOrFail($id);

        $validated = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'title' => 'nullable|string|max:191',
            'subtitle' => 'nullable|string|max:191',
            'action_type' => 'required|in:none,doctor,service,url',
            'action_value' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($slider->image_path && !str_starts_with($slider->image_path, 'http')) {
                Storage::disk('public')->delete($slider->image_path);
            }
            $file = $request->file('image');
            $filename = 'slider_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('sliders', $filename, 'public');
            $slider->image_path = 'sliders/' . $filename;
        }

        $slider->title = $validated['title'] ?? null;
        $slider->subtitle = $validated['subtitle'] ?? null;
        $slider->action_type = $validated['action_type'];
        $slider->action_value = $validated['action_value'] ?? null;
        $slider->sort_order = $validated['sort_order'] ?? 0;
        $slider->is_active = $request->has('is_active') ? (bool)$request->input('is_active') : false;

        $slider->save();

        return redirect()->route('admin.sliders.index')->with('success', 'تم تحديث شريحة السلايدر بنجاح.');
    }

    /**
     * Remove the specified slider.
     */
    public function destroy($id)
    {
        $slider = AppSlider::findOrFail($id);

        if ($slider->image_path && !str_starts_with($slider->image_path, 'http')) {
            Storage::disk('public')->delete($slider->image_path);
        }

        $slider->delete();

        return redirect()->route('admin.sliders.index')->with('success', 'تم حذف شريحة السلايدر بنجاح.');
    }

    /**
     * Quick toggle status.
     */
    public function toggleStatus($id)
    {
        $slider = AppSlider::findOrFail($id);
        $slider->is_active = !$slider->is_active;
        $slider->save();

        return redirect()->route('admin.sliders.index')->with('success', 'تم تغيير حالة الشريحة بنجاح.');
    }
}
