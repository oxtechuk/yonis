@extends('layouts.app')

@section('title', 'ملفي الطبي ومواعيدي - د. يونس المرشدي')

@section('content')
<div class="container py-4">
    {{-- Header Banner --}}
    <div class="saas-card mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h3 class="fw-bold m-0" style="color: var(--brand-navy);">الملف الطبي للمريض</h3>
                </div>
                <p class="text-secondary m-0">مرحباً بك، <strong>{{ $user->name }}</strong> ({{ $user->phone }}). يمكنك استعراض كافة تفاصيل ومواعيد استشاراتك وحالتها هنا.</p>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <button type="button" class="btn-saas-primary" data-bs-toggle="modal" data-bs-target="#bookingModal">
                    <i class="bi bi-plus-circle me-1"></i> حجز جلسة استشارية جديدة
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
            <div class="saas-card p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small fw-bold">الجلسات القادمة والمؤكدة</div>
                        <div class="fs-3 fw-bold" style="color: var(--brand-primary);">{{ $upcomingBookings->count() }}</div>
                    </div>
                    <div class="brand-icon-box" style="background: var(--brand-primary-light); color: var(--brand-primary);">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="saas-card p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small fw-bold">الجلسات المكتملة</div>
                        <div class="fs-3 fw-bold" style="color: var(--brand-emerald);">{{ $pastBookings->count() }}</div>
                    </div>
                    <div class="brand-icon-box" style="background: var(--brand-emerald-light); color: var(--brand-emerald);">
                        <i class="bi bi-check2-all"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="saas-card p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small fw-bold">إجمالي المواعيد المسجلة</div>
                        <div class="fs-3 fw-bold" style="color: var(--brand-navy);">{{ $bookings->count() }}</div>
                    </div>
                    <div class="brand-icon-box" style="background: var(--surface-subtle); color: var(--text-secondary);">
                        <i class="bi bi-collection"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bookings Table --}}
    <div class="saas-card p-0 overflow-hidden">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold m-0" style="color: var(--brand-navy);"><i class="bi bi-journal-medical me-1"></i> جدول المواعيد والاستشارات</h5>
        </div>
        
        <div class="table-responsive">
            <table class="table-saas mb-0">
                <thead>
                    <tr>
                        <th>رقم المرجع</th>
                        <th>نوع الجلسة</th>
                        <th>القناة</th>
                        <th>التاريخ والوقت</th>
                        <th>حالة الحجز</th>
                        <th>حالة الدفع</th>
                        <th>الرسوم</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $b)
                        <tr>
                            <td>
                                <span class="fw-bold text-dark font-monospace">{{ $b->booking_reference }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $b->service ? $b->service->title : 'استشارة نفسية' }}</div>
                                <div class="text-muted small">{{ $b->service ? $b->service->duration . ' دقيقة' : '' }}</div>
                            </td>
                            <td>
                                <div class="fw-bold">{{ Carbon\Carbon::parse($b->date)->format('Y-m-d') }}</div>
                                <div class="text-secondary small">{{ Carbon\Carbon::parse($b->start_time)->format('H:i') }} - {{ Carbon\Carbon::parse($b->end_time)->format('H:i') }}</div>
                            </td>
                            <td>
                                @if($b->status === 'Confirmed')
                                    <span class="saas-badge saas-badge-green"><i class="bi bi-calendar-check-fill"></i> حجز قادم مؤكد</span>
                                @elseif($b->status === 'PendingPaymentReview')
                                    <span class="saas-badge saas-badge-amber"><i class="bi bi-clock-history"></i> قيد التدقيق والمراجعة</span>
                                @elseif(in_array($b->status, ['AwaitingPayment', 'Pending']))
                                    <span class="saas-badge saas-badge-amber"><i class="bi bi-hourglass-split"></i> بانتظار تحويل الدفع</span>
                                @elseif($b->status === 'Completed')
                                    <span class="saas-badge" style="background: #F1F5F9; color: #475569;"><i class="bi bi-check2-all"></i> مكتمل</span>
                                @elseif(str_contains($b->status, 'Cancelled'))
                                    <span class="saas-badge" style="background: #FFE4E6; color: var(--brand-rose);"><i class="bi bi-x-circle"></i> ملغي</span>
                                @else
                                    <span class="saas-badge">{{ $b->status_label }}</span>
                                @endif
                            </td>
                            <td>
                                @if($b->payment && $b->payment->status === 'Paid')
                                    <span class="saas-badge saas-badge-green"><i class="bi bi-credit-card-2-front"></i> مدفوع</span>
                                @else
                                    <span class="saas-badge saas-badge-amber">غير مدفوع</span>
                                @endif
                            </td>
                            <td class="fw-bold">{{ number_format($b->price ?? 50, 0) }} {{ \App\Models\Setting::currencySymbol() }}</td>
                            <td class="text-end">
                                @if(in_array($b->status, ['AwaitingPayment', 'Confirmed', 'Rescheduled']))
                                    @if($b->status === 'AwaitingPayment' && $b->service && !empty($b->service->payment_url))
                                        <a href="{{ $b->service->payment_url }}" target="_blank" class="btn-saas-secondary py-1 px-2.5 small me-1">
                                            <i class="bi bi-credit-card"></i> دفع Gumroad
                                        </a>
                                    @endif
                                    
                                    <form action="{{ route('patient.bookings.cancel', $b->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من رغبتك في إلغاء هذا الموعد؟')">
                                        @csrf
                                        <button type="submit" class="btn-saas-ghost text-danger py-1 px-2 small">
                                            <i class="bi bi-x-circle"></i> إلغاء
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-secondary">
                                <div class="brand-icon-box mx-auto mb-3" style="background: var(--surface-subtle); color: var(--text-muted); width: 50px; height: 50px; font-size: 1.5rem;">
                                    <i class="bi bi-calendar-x"></i>
                                </div>
                                <h6 class="fw-bold mb-1">لا توجد حجوزات مسجلة حتى الآن</h6>
                                <p class="small text-muted mb-3">اختر إحدى الاستشارات المتاحة لبدء جلستك العلاجية مع د. يونس المرشدي.</p>
                                <button type="button" class="btn-saas-primary" data-bs-toggle="modal" data-bs-target="#bookingModal">
                                    <i class="bi bi-calendar-plus"></i> حجز جلسة الآن
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
