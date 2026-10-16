@extends('layouts.admin')

@section('title', 'إدارة سلايدر وبنرات التطبيق (Home Sliders)')

@section('styles')
<style>
    .slider-banner-card {
        background: #FFFFFF;
        border: 1px solid #E8EEF8;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(64, 85, 165, 0.05);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .slider-banner-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(64, 85, 165, 0.1);
    }
    .slider-banner-img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        background: #F1F5F9;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    {{-- Alerts --}}
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
                <i class="bi bi-images text-primary me-2"></i> سلايدر وبنرات التطبيق (Home Sliders)
            </h3>
            <p class="text-muted small mb-0">إدارة البنرات الإعلانية والتعريفية التي تظهر في أعلى الصفحة الرئيسية لتطبيق الموبايل</p>
        </div>
        <div>
            <button class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addSliderModal">
                <i class="bi bi-plus-lg"></i> إضافة شريحة جديدة
            </button>
        </div>
    </div>

    {{-- Sliders Grid --}}
    <div class="row g-4">
        @forelse($sliders as $slider)
            <div class="col-md-6 col-lg-4">
                <div class="slider-banner-card h-100 d-flex flex-column">
                    <div class="position-relative">
                        <img src="{{ $slider->image_url }}" alt="{{ $slider->title ?? 'Slider' }}" class="slider-banner-img">
                        <div class="position-absolute top-0 end-0 p-2">
                            @if($slider->is_active)
                                <span class="badge bg-success shadow-sm rounded-pill px-3 py-1">مفعل</span>
                            @else
                                <span class="badge bg-danger shadow-sm rounded-pill px-3 py-1">معطل</span>
                            @endif
                        </div>
                        <div class="position-absolute bottom-0 start-0 p-2">
                            <span class="badge bg-dark bg-opacity-75 rounded-pill px-2 py-1 small">
                                ترتيب: {{ $slider->sort_order }}
                            </span>
                        </div>
                    </div>
                    <div class="p-3 flex-grow-1 d-flex flex-column justify-content-between">
                        <div>
                            <h6 class="fw-bold text-dark mb-1">{{ $slider->title ?: 'بدون عنوان رئيسي' }}</h6>
                            @if($slider->subtitle)
                                <p class="text-muted small mb-2">{{ $slider->subtitle }}</p>
                            @endif
                            <div class="small text-secondary mb-3">
                                <i class="bi bi-link-45deg me-1"></i>
                                <strong>الوجهة:</strong>
                                @if($slider->action_type === 'doctor')
                                    <span class="badge bg-primary-subtle text-primary border border-primary">حجز طبيب (ID: {{ $slider->action_value }})</span>
                                @elseif($slider->action_type === 'service')
                                    <span class="badge bg-info-subtle text-info border border-info">خدمة معينة (ID: {{ $slider->action_value }})</span>
                                @elseif($slider->action_type === 'url')
                                    <span class="badge bg-secondary-subtle text-secondary border">رابط خارجي: {{ Str::limit($slider->action_value, 20) }}</span>
                                @else
                                    <span class="badge bg-light text-muted border">عرض فقط بدون رابط</span>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <form action="{{ route('admin.sliders.toggle', $slider->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $slider->is_active ? 'btn-outline-warning' : 'btn-outline-success' }} rounded-2">
                                    <i class="bi {{ $slider->is_active ? 'bi-pause-fill' : 'bi-play-fill' }}"></i>
                                    {{ $slider->is_active ? 'تعطيل' : 'تفعيل' }}
                                </button>
                            </form>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-primary rounded-2 me-1" data-bs-toggle="modal" data-bs-target="#editSliderModal{{ $slider->id }}" title="تعديل">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('admin.sliders.destroy', $slider->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل تريد حذف هذه الشريحة؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-2" title="حذف">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Edit Slider Modal --}}
            <div class="modal fade" id="editSliderModal{{ $slider->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4">
                        <div class="modal-header border-bottom py-3 px-4">
                            <h5 class="modal-title fw-bold">
                                <i class="bi bi-pencil-square text-primary me-2"></i> تعديل شريحة السلايدر
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('admin.sliders.update', $slider->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="modal-body p-4">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">صورة البانر الحالية</label>
                                    <div class="mb-2">
                                        <img src="{{ $slider->image_url }}" alt="Preview" class="img-fluid rounded-3" style="max-height: 120px; width: 100%; object-fit: cover;">
                                    </div>
                                    <label class="form-label small text-muted">تغيير الصورة (اختياري)</label>
                                    <input type="file" name="image" class="form-control rounded-3" accept="image/*">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">العنوان الرئيسي</label>
                                    <input type="text" name="title" class="form-control rounded-3" value="{{ old('title', $slider->title) }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">النص الفرعي</label>
                                    <input type="text" name="subtitle" class="form-control rounded-3" value="{{ old('subtitle', $slider->subtitle) }}">
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">نوع التوجيه عند الضغط</label>
                                        <select name="action_type" class="form-select rounded-3">
                                            <option value="none" {{ $slider->action_type === 'none' ? 'selected' : '' }}>عرض فقط</option>
                                            <option value="doctor" {{ $slider->action_type === 'doctor' ? 'selected' : '' }}>حجز طبيب معين</option>
                                            <option value="service" {{ $slider->action_type === 'service' ? 'selected' : '' }}>فتح خدمة معينة</option>
                                            <option value="url" {{ $slider->action_type === 'url' ? 'selected' : '' }}>رابط خارجي</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">القيمة / المعرف / الرابط</label>
                                        <input type="text" name="action_value" class="form-control rounded-3" value="{{ old('action_value', $slider->action_value) }}" placeholder="ID الطبيب أو الرابط">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">ترتيب الظهور</label>
                                    <input type="number" name="sort_order" class="form-control rounded-3" value="{{ old('sort_order', $slider->sort_order) }}">
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="activeSlider{{ $slider->id }}" {{ $slider->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="activeSlider{{ $slider->id }}">تفعيل ظهور الشريحة في التطبيق</label>
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
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 p-5 text-center text-muted">
                    <i class="bi bi-images fs-1 d-block mb-3 text-secondary"></i>
                    <h5 class="fw-bold text-dark">لا توجد شرائح سلايدر حتى الآن</h5>
                    <p class="mb-3">أضف شرائح وبنرات لعرضها في أعلى واجهة تطبيق الموبايل لشد انتباه المرضى</p>
                    <div>
                        <button class="btn btn-primary px-4 py-2 rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addSliderModal">
                            <i class="bi bi-plus-lg me-1"></i> إضافة أول شريحة الآن
                        </button>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

</div>

{{-- Add Slider Modal --}}
<div class="modal fade" id="addSliderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-plus-circle text-primary me-2"></i> إضافة شريحة سلايدر جديدة
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.sliders.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">صورة البانر <span class="text-danger">*</span></label>
                        <input type="file" name="image" class="form-control rounded-3" accept="image/*" required>
                        <span class="text-muted small">يفضل أبعاد عرضية مناسبة للموبايل (مثال: 1200x600 بكسل)</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">العنوان الرئيسي (اختياري)</label>
                        <input type="text" name="title" class="form-control rounded-3" placeholder="مثال: خصم 20% على الاستشارات الأونلاين">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">النص الفرعي (اختياري)</label>
                        <input type="text" name="subtitle" class="form-control rounded-3" placeholder="مثال: لفترة محدودة خلال هذا الشهر">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">نوع التوجيه عند الضغط</label>
                            <select name="action_type" class="form-select rounded-3">
                                <option value="none">عرض فقط (بدون نقر)</option>
                                <option value="doctor">فتح حجز دكتور معين</option>
                                <option value="service">فتح خدمة معينة</option>
                                <option value="url">رابط خارجي</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">القيمة / المعرف / الرابط</label>
                            <input type="text" name="action_value" class="form-control rounded-3" placeholder="رقم الطبيب أو الرابط">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">ترتيب الظهور</label>
                        <input type="number" name="sort_order" class="form-control rounded-3" value="0">
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="activeSliderAdd" checked>
                        <label class="form-check-label fw-semibold" for="activeSliderAdd">تفعيل ظهور الشريحة فوراً في التطبيق</label>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4">إضافة الشريحة</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
