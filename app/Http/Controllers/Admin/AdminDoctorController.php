<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminDoctorController extends Controller
{
    /**
     * Display a listing of doctors.
     */
    public function index()
    {
        $doctors = Doctor::withCount('bookings')
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->paginate(15);

        $totalDoctors = Doctor::count();
        $activeDoctors = Doctor::where('is_active', true)->count();

        return view('admin.doctors', compact('doctors', 'totalDoctors', 'activeDoctors'));
    }

    /**
     * Store a newly created doctor.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'name_en' => 'nullable|string|max:191',
            'title' => 'required|string|max:191',
            'title_en' => 'nullable|string|max:191',
            'specialty' => 'required|string|max:191',
            'specialty_en' => 'nullable|string|max:191',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:191',
            'experience_years' => 'nullable|integer|min:0|max:80',
            'consultation_fee' => 'nullable|numeric|min:0',
            'bio' => 'nullable|string',
            'bio_en' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ], [
            'name.required' => 'اسم الطبيب مطلوب.',
            'title.required' => 'المسمى الوظيفي للطبيب مطلوب.',
            'specialty.required' => 'التخصص الطبي مطلوب.',
            'avatar.image' => 'يجب أن يكون الملف المرفوع صورة صالحة.',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = 'doctor_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('doctors', $filename, 'public');
            $avatarPath = 'doctors/' . $filename;
        }

        Doctor::create([
            'name' => $validated['name'],
            'name_en' => $validated['name_en'] ?? null,
            'title' => $validated['title'],
            'title_en' => $validated['title_en'] ?? null,
            'specialty' => $validated['specialty'],
            'specialty_en' => $validated['specialty_en'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'experience_years' => $validated['experience_years'] ?? 5,
            'consultation_fee' => $validated['consultation_fee'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'bio_en' => $validated['bio_en'] ?? null,
            'avatar' => $avatarPath,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->has('is_active') ? (bool)$request->input('is_active') : true,
        ]);

        return redirect()->route('admin.doctors.index')->with('success', 'تم إضافة الطبيب بنجاح وهو متاح الآن في النظام والتطبيق.');
    }

    /**
     * Update the specified doctor.
     */
    public function update(Request $request, $id)
    {
        $doctor = Doctor::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'name_en' => 'nullable|string|max:191',
            'title' => 'required|string|max:191',
            'title_en' => 'nullable|string|max:191',
            'specialty' => 'required|string|max:191',
            'specialty_en' => 'nullable|string|max:191',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:191',
            'experience_years' => 'nullable|integer|min:0|max:80',
            'consultation_fee' => 'nullable|numeric|min:0',
            'bio' => 'nullable|string',
            'bio_en' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('avatar')) {
            if ($doctor->avatar && !str_starts_with($doctor->avatar, 'http')) {
                Storage::disk('public')->delete($doctor->avatar);
            }
            $file = $request->file('avatar');
            $filename = 'doctor_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('doctors', $filename, 'public');
            $doctor->avatar = 'doctors/' . $filename;
        }

        $doctor->name = $validated['name'];
        $doctor->name_en = $validated['name_en'] ?? null;
        $doctor->title = $validated['title'];
        $doctor->title_en = $validated['title_en'] ?? null;
        $doctor->specialty = $validated['specialty'];
        $doctor->specialty_en = $validated['specialty_en'] ?? null;
        $doctor->phone = $validated['phone'] ?? null;
        $doctor->email = $validated['email'] ?? null;
        $doctor->experience_years = $validated['experience_years'] ?? 5;
        $doctor->consultation_fee = $validated['consultation_fee'] ?? null;
        $doctor->bio = $validated['bio'] ?? null;
        $doctor->bio_en = $validated['bio_en'] ?? null;
        $doctor->sort_order = $validated['sort_order'] ?? 0;
        $doctor->is_active = $request->has('is_active') ? (bool)$request->input('is_active') : false;

        $doctor->save();

        return redirect()->route('admin.doctors.index')->with('success', 'تم تحديث بيانات الطبيب بنجاح.');
    }

    /**
     * Remove the specified doctor.
     */
    public function destroy($id)
    {
        $doctor = Doctor::findOrFail($id);

        if (Doctor::count() <= 1) {
            return redirect()->route('admin.doctors.index')->with('error', 'لا يمكن حذف الطبيب الأخير في النظام. يمكنك تعديل بياناته أو تعطيل نشاطه.');
        }

        if ($doctor->avatar && !str_starts_with($doctor->avatar, 'http')) {
            Storage::disk('public')->delete($doctor->avatar);
        }

        $doctor->delete();

        return redirect()->route('admin.doctors.index')->with('success', 'تم حذف الطبيب بنجاح.');
    }

    /**
     * Quick toggle status.
     */
    public function toggleStatus($id)
    {
        $doctor = Doctor::findOrFail($id);
        $doctor->is_active = !$doctor->is_active;
        $doctor->save();

        return redirect()->route('admin.doctors.index')->with('success', 'تم تغيير حالة الطبيب إلى ' . ($doctor->is_active ? 'مفعل' : 'معطل'));
    }
}
