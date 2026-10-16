@extends('layouts.admin')

@section('title', 'إدارة الدكاترة والأطباء - تطبيق العيادة')

@section('styles')
<style>
    .doctor-avatar-circle {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #E2E8F0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    .kpi-card-doc {
        background: #FFFFFF;
        border: 1px solid #E8EEF8;
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 4px 16px rgba(64, 85, 165, 0.05);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .kpi-card-doc .icon-box {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
    }
    .badge-auto-select {
        background: #ECFDF5;
        color: #059669;
        border: 1px solid #A7F3D0;
        border-radius: 9999px;
        padding: 0.35rem 0.85rem;
        font-size: 0.82rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1 text-dark">
                <i class="bi bi-person-badge text-primary me-2"></i> إدارة الدكاترة والاستشاريين
            </h3>
            <p class="text-muted small mb-0">إضافة وتعديل بيانات الأطباء وصورهم وتخصصاتهم لظهورهم في تطبيق الموبايل وبوابة الحجوزات</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addDoctorModal">
                <i class="bi bi-plus-lg"></i> إضافة طبيب جديد
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="kpi-card-doc">
                <div>
                    <span class="text-muted d-block small mb-1">إجمالي الدكاترة</span>
                    <h3 class="fw-bold mb-0 text-dark">{{ $totalDoctors }}</h3>
                </div>
                <div class="icon-box bg-primary-subtle text-primary">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card-doc">
                <div>
                    <span class="text-muted d-block small mb-1">الدكاترة المتاحين للتطبيق</span>
                    <h3 class="fw-bold mb-0 text-success">{{ $activeDoctors }}</h3>
                </div>
                <div class="icon-box bg-success-subtle text-success">
                    <i class="bi bi-check2-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card-doc">
                <div>
                    <span class="text-muted d-block small mb-1">وضعية الحجز في التطبيق</span>
                    @if($activeDoctors === 1)
                        <div class="badge-auto-select mt-1">
                            <i class="bi bi-lightning-charge-fill text-warning"></i> اختيار تلقائي (Auto-Select)
                        </div>
                    @else
                        <span class="badge bg-primary-subtle text-primary border border-primary px-3 py-2 rounded-pill small mt-1">
                            <i class="bi bi-ui-radios-grid me-1"></i> قائمة اختيار متعدد
                        </span>
                    @endif
                </div>
                <div class="icon-box bg-warning-subtle text-warning">
                    <i class="bi bi-phone"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Doctors Table Card --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">قائمة الدكاترة المعروضين في النظام</h5>
                <span class="text-muted small">يعرض التطبيق الدكاترة المفعلين فقط</span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">الطبيب</th>
                        <th>المسمى والتخصص</th>
                        <th>الخبرة</th>
                        <th>سعر الكشف</th>
                        <th>حجوزات مسجلة</th>
                        <th>حالة الظهور</th>
                        <th class="text-end pe-4">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($doctors as $doc)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $doc->avatar_url }}" alt="{{ $doc->name }}" class="doctor-avatar-circle">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $doc->name }}</div>
                                        @if($doc->name_en)
                                            <div class="text-muted small">{{ $doc->name_en }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold text-primary">{{ $doc->title }}</div>
                                <span class="badge bg-secondary-subtle text-secondary small border">
                                    {{ $doc->specialty ?? 'طب عام' }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted">{{ $doc->experience_years }} سنوات</span>
                            </td>
                            <td>
                                @if($doc->consultation_fee)
                                    <span class="fw-bold text-success">{{ number_format($doc->consultation_fee, 2) }} $</span>
                                @else
                                    <span class="text-muted small">حسب الخدمة</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-info border border-info rounded-pill px-3">
                                    {{ $doc->bookings_count }} حجز
                                </span>
                            </td>
                            <td>
                                @if($doc->is_active)
                                    <span class="badge bg-success-subtle text-success border border-success rounded-pill px-3 py-2">
                                        <i class="bi bi-check-circle-fill me-1"></i> مفعل بالتطبيق
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger rounded-pill px-3 py-2">
                                        <i class="bi bi-x-circle-fill me-1"></i> معطل
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary rounded-2 me-1" data-bs-toggle="modal" data-bs-target="#editDoctorModal{{ $doc->id }}" title="تعديل">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form action="{{ route('admin.doctors.toggle', $doc->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $doc->is_active ? 'btn-outline-warning' : 'btn-outline-success' }} rounded-2 me-1" title="{{ $doc->is_active ? 'تعطيل' : 'تفعيل' }}">
                                            <i class="bi {{ $doc->is_active ? 'bi-pause-circle' : 'bi-play-circle' }}"></i>
                                        </button>
                                    </form>
                                    @if($totalDoctors > 1)
                                        <form action="{{ route('admin.doctors.destroy', $doc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الطبيب؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-2" title="حذف">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- Edit Doctor Modal --}}
                        <div class="modal fade" id="editDoctorModal{{ $doc->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg rounded-4">
                                    <div class="modal-header border-bottom py-3 px-4">
                                        <h5 class="modal-title fw-bold">
                                            <i class="bi bi-pencil-square text-primary me-2"></i> تعديل بيانات الطبيب: {{ $doc->name }}
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('admin.doctors.update', $doc->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body p-4">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">اسم الطبيب (بالعربية) <span class="text-danger">*</span></label>
                                                    <input type="text" name="name" class="form-control rounded-3" value="{{ old('name', $doc->name) }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">اسم الطبيب (بالانجليزية)</label>
                                                    <input type="text" name="name_en" class="form-control rounded-3" value="{{ old('name_en', $doc->name_en) }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">المسمى الوظيفي (مثلاً: استشاري طب وجراحة) <span class="text-danger">*</span></label>
                                                    <input type="text" name="title" class="form-control rounded-3" value="{{ old('title', $doc->title) }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">التخصص الطبي <span class="text-danger">*</span></label>
                                                    <input type="text" name="specialty" class="form-control rounded-3" value="{{ old('specialty', $doc->specialty) }}" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-semibold">سنوات الخبرة</label>
                                                    <input type="number" name="experience_years" class="form-control rounded-3" value="{{ old('experience_years', $doc->experience_years) }}" min="0">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-semibold">سعر الكشف التقديري ($)</label>
                                                    <input type="number" step="0.01" name="consultation_fee" class="form-control rounded-3" value="{{ old('consultation_fee', $doc->consultation_fee) }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-semibold">ترتيب الظهور</label>
                                                    <input type="number" name="sort_order" class="form-control rounded-3" value="{{ old('sort_order', $doc->sort_order) }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">الهاتف / واتساب</label>
                                                    <input type="text" name="phone" class="form-control rounded-3" value="{{ old('phone', $doc->phone) }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">البريد الإلكتروني</label>
                                                    <input type="email" name="email" class="form-control rounded-3" value="{{ old('email', $doc->email) }}">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">نبذة تعريفية عن الطبيب</label>
                                                    <textarea name="bio" rows="3" class="form-control rounded-3">{{ old('bio', $doc->bio) }}</textarea>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">صورة الطبيب الشخصية (Avatar)</label>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <img src="{{ $doc->avatar_url }}" alt="Preview" class="doctor-avatar-circle">
                                                        <input type="file" name="avatar" class="form-control rounded-3" accept="image/*">
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-check form-switch mt-2">
                                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActiveEdit{{ $doc->id }}" {{ $doc->is_active ? 'checked' : '' }}>
                                                        <label class="form-check-label fw-semibold" for="isActiveEdit{{ $doc->id }}">تفعيل الطبيب للظهور في التطبيق والحجوزات</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top py-3 px-4">
                                            <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">إلغاء</button>
                                            <button type="submit" class="btn btn-primary rounded-3 px-4">حفظ التغييرات</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-person-x fs-1 d-block mb-2"></i>
                                لا يوجد دكاترة مضافين حالياً.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($doctors->hasPages())
            <div class="card-footer bg-white border-top py-3 px-4">
                {{ $doctors->links() }}
            </div>
        @endif
    </div>

</div>

{{-- Add Doctor Modal --}}
<div class="modal fade" id="addDoctorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-person-plus text-primary me-2"></i> إضافة طبيب جديد للتطبيق
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.doctors.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">اسم الطبيب (بالعربية) <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control rounded-3" placeholder="مثال: د. أحمد خالد" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">اسم الطبيب (بالانجليزية)</label>
                            <input type="text" name="name_en" class="form-control rounded-3" placeholder="Dr. Ahmed Khaled">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">المسمى الوظيفي <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control rounded-3" placeholder="مثال: أخصائي طب وجراحة العيون" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">التخصص الطبي <span class="text-danger">*</span></label>
                            <input type="text" name="specialty" class="form-control rounded-3" placeholder="مثال: طب العيون" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">سنوات الخبرة</label>
                            <input type="number" name="experience_years" class="form-control rounded-3" value="5" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">سعر الكشف التقديري ($)</label>
                            <input type="number" step="0.01" name="consultation_fee" class="form-control rounded-3" placeholder="مثال: 40.00">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">ترتيب الظهور</label>
                            <input type="number" name="sort_order" class="form-control rounded-3" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">الهاتف / واتساب</label>
                            <input type="text" name="phone" class="form-control rounded-3" placeholder="+9647800000000">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">البريد الإلكتروني</label>
                            <input type="email" name="email" class="form-control rounded-3" placeholder="doctor@example.com">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">نبذة تعريفية عن الطبيب</label>
                            <textarea name="bio" rows="3" class="form-control rounded-3" placeholder="اكتب نبذة مختصرة عن مؤهلات وخبرات الطبيب..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">صورة الطبيب الشخصية (Avatar)</label>
                            <input type="file" name="avatar" class="form-control rounded-3" accept="image/*">
                            <span class="text-muted small">يفضل صورة مربعة بحجم لا يتجاوز 5 ميجابايت</span>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActiveAdd" checked>
                                <label class="form-check-label fw-semibold" for="isActiveAdd">تفعيل الطبيب مباشرة للظهور في التطبيق والحجوزات</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4">إضافة الطبيب</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
