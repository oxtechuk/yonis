<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    /**
     * Available permission keys & human readable labels
     */
    public static array $availablePermissions = [
        'manage_bookings' => 'إدارة الحجوزات وتقويم المواعيد',
        'manage_patients' => 'إدارة سجلات المرضى والمراجعين',
        'manage_payments' => 'إدارة المدفوعات والتقارير المالية',
        'manage_services' => 'إدارة الخدمات والأسعار',
        'manage_availability' => 'إدارة مواعيد العمل والإجازات',
        'manage_portfolio' => 'إدارة محتوى الموقع والوسائط',
        'manage_settings' => 'إدارة إعدادات المنصة والهوية',
        'manage_staff' => 'إدارة الموظفين وتحديد الصلاحيات',
    ];

    /**
     * Display list of staff members
     */
    public function index()
    {
        $staffMembers = User::where('role', 'staff')->latest()->get();
        $availablePermissions = self::$availablePermissions;
        return view('admin.staff.index', compact('staffMembers', 'availablePermissions'));
    }

    /**
     * Store new staff member
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:30',
            'password' => 'required|string|min:8',
            'permissions' => 'nullable|array',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => 'staff',
            'permissions' => $request->permissions ?? [],
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'تم إضافة الموظف وتحديد صلاحياته بنجاح.');
    }

    /**
     * Update staff member & permissions
     */
    public function update(Request $request, $id)
    {
        $staff = User::where('role', 'staff')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:30',
            'password' => 'nullable|string|min:8',
            'permissions' => 'nullable|array',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'permissions' => $request->permissions ?? [],
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $staff->update($data);

        return redirect()->back()->with('success', 'تم تحديث بيانات وصلاحيات الموظف بنجاح.');
    }

    /**
     * Delete staff member
     */
    public function destroy($id)
    {
        $staff = User::where('role', 'staff')->findOrFail($id);
        $staff->delete();

        return redirect()->back()->with('success', 'تم حذف حساب الموظف بنجاح.');
    }
}
