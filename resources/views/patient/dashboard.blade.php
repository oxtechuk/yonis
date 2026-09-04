@extends('layouts.app')

@section('title', 'ملفي الطبي ومواعيدي - د. يونس المرشدي')

@section('content')
<div class="container py-4 py-lg-5">
    
    {{-- Header Banner --}}
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border: 1px solid #e2e8f0 !important;">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1.5">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 44px; height: 44px; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); font-size: 1.25rem;">
                        <i class="bi bi-person-heart"></i>
                    </div>
                    <div>
                        <h4 class="fw-black text-dark m-0">الملف الطبي واستشاراتي</h4>
                        <div class="text-secondary small">مرحباً بك، <strong>{{ $user->name }}</strong> ({{ $user->phone }})</div>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <button type="button" class="btn btn-primary rounded-pill px-4 py-2.5 fw-bold shadow-sm d-flex align-items-center gap-2"
                        data-bs-toggle="modal" data-bs-target="#bookingModal"
                        style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); border: none;">
                    <i class="bi bi-plus-circle-fill fs-6"></i>
                    <span>حجز جلسة استشارية جديدة</span>
                </button>
                <form action="{{ route('logout') }}" method="POST" class="d-inline m-0">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger rounded-pill px-3 py-2 fw-bold d-flex align-items-center gap-1.5 shadow-sm" title="تسجيل الخروج من الحساب">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>تسجيل الخروج</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3.5 h-100 bg-white" style="border: 1px solid #e2e8f0 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small fw-bold mb-1">الجلسات القادمة والمؤكدة</div>
                        <div class="fs-2 fw-black" style="color: var(--primary-color);">{{ $upcomingBookings->count() }}</div>
                    </div>
                    <div class="rounded-4 d-flex align-items-center justify-content-center text-primary" style="width: 52px; height: 52px; background: #eff6ff; font-size: 1.5rem;">
                        <i class="bi bi-calendar-event-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3.5 h-100 bg-white" style="border: 1px solid #e2e8f0 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small fw-bold mb-1">الجلسات المكتملة</div>
                        <div class="fs-2 fw-black text-success">{{ $pastBookings->count() }}</div>
                    </div>
                    <div class="rounded-4 d-flex align-items-center justify-content-center text-success" style="width: 52px; height: 52px; background: #ecfdf5; font-size: 1.5rem;">
                        <i class="bi bi-check2-all"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3.5 h-100 bg-white" style="border: 1px solid #e2e8f0 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small fw-bold mb-1">إجمالي المواعيد المسجلة</div>
                        <div class="fs-2 fw-black text-dark">{{ $bookings->count() }}</div>
                    </div>
                    <div class="rounded-4 d-flex align-items-center justify-content-center text-secondary" style="width: 52px; height: 52px; background: #f8fafc; font-size: 1.5rem;">
                        <i class="bi bi-collection-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bookings Table --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white" style="border: 1px solid #e2e8f0 !important;">
        <div class="p-3.5 px-4 border-bottom d-flex justify-content-between align-items-center bg-light bg-opacity-50">
            <h5 class="fw-bold m-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-journal-medical text-primary fs-5"></i>
                <span>جدول المواعيد والاستشارات</span>
            </h5>
            <span class="badge bg-light text-secondary border px-3 py-1.5 rounded-pill fw-bold small">
                {{ $bookings->count() }} موعد مسجل
            </span>
        </div>
        
        <div class="table-responsive">
            <table class="table align-middle mb-0 text-nowrap" style="font-size: 0.92rem;">
                <thead class="bg-light text-secondary" style="font-size: 0.82rem; font-weight: 800; border-bottom: 2px solid #e2e8f0;">
                    <tr>
                        <th class="ps-4 py-3">رقم المرجع</th>
                        <th class="py-3">الخدمة والاستشارة</th>
                        <th class="py-3">نوع الاستشارة</th>
                        <th class="py-3">الموعد والتوقيت</th>
                        <th class="py-3 text-center">حالة الحجز</th>
                        <th class="py-3">المبلغ</th>
                        <th class="pe-4 py-3 text-end">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $b)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            {{-- 1. رقم المرجع --}}
                            <td class="ps-4 py-3.5">
                                <span class="badge bg-primary-subtle text-primary font-monospace px-2.5 py-1.5 rounded-pill fw-black" dir="ltr" style="font-size: 0.86rem; letter-spacing: 0.5px;">
                                    #{{ $b->booking_reference }}
                                </span>
                            </td>

                            {{-- 2. الخدمة والاستشارة --}}
                            <td class="py-3.5">
                                <div class="fw-bold text-dark">{{ $b->service ? $b->service->title : 'استشارة نفسية' }}</div>
                                <div class="text-secondary small mt-0.5 d-flex align-items-center gap-1">
                                    <i class="bi bi-clock-history text-primary"></i>
                                    <span>{{ $b->service ? $b->service->duration : '45' }} دقيقة</span>
                                </div>
                            </td>

                            {{-- 3. نوع الاستشارة (أونلاين / عيادة) --}}
                            <td class="py-3.5">
                                @if($b->booking_type === 'clinic' || $b->consultation_type === 'clinic')
                                    <span class="badge bg-secondary-subtle text-dark border px-2.5 py-1.5 rounded-pill fw-bold small">
                                        <i class="bi bi-hospital me-1 text-primary"></i> كشف بالعيادة (بغداد)
                                    </span>
                                @else
                                    <span class="badge bg-info-subtle text-primary border border-info-subtle px-2.5 py-1.5 rounded-pill fw-bold small">
                                        <i class="bi bi-camera-video-fill me-1"></i> استشارة أونلاين
                                    </span>
                                @endif
                            </td>

                            {{-- 4. الموعد والتوقيت --}}
                            <td class="py-3.5">
                                <div class="fw-bold text-dark font-monospace">
                                    <i class="bi bi-calendar3 text-primary me-1"></i>
                                    {{ Carbon\Carbon::parse($b->date)->format('Y-m-d') }}
                                </div>
                                <div class="text-secondary small font-monospace mt-0.5">
                                    <i class="bi bi-clock text-muted me-1"></i>
                                    {{ Carbon\Carbon::parse($b->start_time)->format('h:i A') }}
                                </div>
                            </td>

                            {{-- 5. حالة الحجز --}}
                            <td class="py-3.5 text-center">
                                @if($b->status === 'Confirmed')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill fw-bold">
                                        <i class="bi bi-check-circle-fill me-1"></i> حجز قادم مؤكد
                                    </span>
                                @elseif($b->status === 'PendingPaymentReview')
                                    <span class="badge bg-warning text-dark border border-warning px-3 py-1.5 rounded-pill fw-bold">
                                        <i class="bi bi-clock-history me-1"></i> قيد التدقيق والمراجعة
                                    </span>
                                @elseif(in_array($b->status, ['AwaitingPayment', 'Pending']))
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1.5 rounded-pill fw-bold">
                                        <i class="bi bi-hourglass-split me-1"></i> بانتظار تأكيد الدفع
                                    </span>
                                @elseif($b->status === 'Completed')
                                    <span class="badge bg-light text-secondary border px-3 py-1.5 rounded-pill fw-bold">
                                        <i class="bi bi-check2-all me-1"></i> جلسة مكتملة
                                    </span>
                                @elseif(str_contains($b->status, 'Cancelled'))
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1.5 rounded-pill fw-bold">
                                        <i class="bi bi-x-circle me-1"></i> موعد ملغي
                                    </span>
                                @else
                                    <span class="badge bg-light text-dark border px-3 py-1.5 rounded-pill fw-bold">
                                        {{ $b->status_label }}
                                    </span>
                                @endif
                            </td>

                            {{-- 6. المبلغ --}}
                            <td class="py-3.5">
                                <span class="fw-black fs-6" style="color: var(--primary-color);">
                                    {{ number_format($b->price ?? 50, 0) }} {{ \App\Models\Setting::currencySymbol() }}
                                </span>
                            </td>

                            {{-- 7. الإجراءات --}}
                            <td class="pe-4 py-3.5 text-end">
                                <div class="d-flex align-items-center justify-content-end gap-1.5">
                                    {{-- تواصل واتساب --}}
                                    @php
                                        $waNum = preg_replace('/[^0-9]/', '', \App\Models\Setting::get('whatsapp_number', '+9647700000000'));
                                        $waText = urlencode("مرحباً دكتور يونس، أستفسر بخصوص حجز الموعد رقم #{$b->booking_reference}");
                                    @endphp
                                    <a href="https://wa.me/{{ $waNum }}?text={{ $waText }}" target="_blank"
                                       class="btn btn-sm btn-outline-success rounded-pill px-2.5 py-1 fw-bold shadow-sm d-inline-flex align-items-center gap-1"
                                       title="تواصل مع الطبيب عبر واتساب">
                                        <i class="bi bi-whatsapp"></i>
                                        <span class="d-none d-md-inline">تواصل</span>
                                    </a>

                                    {{-- زر الإلغاء إذا كان الموعد قيد الانتظار أو مؤكد --}}
                                    @if(in_array($b->status, ['AwaitingPayment', 'PendingPaymentReview', 'Confirmed', 'Rescheduled']))
                                        <form action="{{ route('patient.bookings.cancel', $b->id) }}" method="POST" class="d-inline m-0"
                                              onsubmit="return confirm('هل أنت متأكد من رغبتك في إلغاء هذا الموعد؟')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 fw-bold shadow-sm d-inline-flex align-items-center gap-1"
                                                    title="إلغاء هذا الموعد">
                                                <i class="bi bi-x-circle"></i>
                                                <span class="d-none d-md-inline">إلغاء</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-secondary">
                                <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center text-muted"
                                     style="background: #f8fafc; border: 1.5px dashed #cbd5e1; width: 64px; height: 64px; font-size: 1.8rem;">
                                    <i class="bi bi-calendar-x"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">لا توجد حجوزات مسجلة حتى الآن</h6>
                                <p class="small text-muted mb-3">اختر إحدى الاستشارات المتاحة لبدء جلستك العلاجية مع د. يونس المرشدي.</p>
                                <button type="button" class="btn btn-primary rounded-pill px-4 py-2.5 fw-bold shadow-sm"
                                        data-bs-toggle="modal" data-bs-target="#bookingModal"
                                        style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); border: none;">
                                    <i class="bi bi-calendar-plus me-1"></i> حجز جلسة استشارية الآن
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('partials.booking_modal')
@endsection
