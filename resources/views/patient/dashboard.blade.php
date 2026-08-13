@extends('layouts.app')

@section('title', 'لوحة تحكم المريض - عيادة د. يونس أحمد')

@section('content')
<div class="container my-4">
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm p-4 card-glass">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h2 class="fw-bold m-0"><i class="bi bi-speedometer2 text-teal me-1" style="color: var(--accent-color);"></i> ملفك الطبي والحجوزات</h2>
                        <p class="text-secondary m-0">مرحباً بك، {{ Auth::user()->name }}. هنا يمكنك متابعة مواعيدك الطبية ومعاملاتك المالية.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('home') }}#booking-section" class="btn btn-premium">حجز موعد استشارة جديد</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-exclamation-octagon-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold m-0">سجل الاستشارات والمواعيد</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">رقم المرجع</th>
                                    <th>نوع الاستشارة</th>
                                    <th>التاريخ والوقت</th>
                                    <th>حالة الحجز</th>
                                    <th>حالة الدفع</th>
                                    <th>الرسوم</th>
                                    <th class="pe-4 text-end">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bookings as $booking)
                                    <tr>
                                        <td class="ps-4 fw-bold text-dark">{{ $booking->booking_reference }}</td>
                                        <td>{{ $booking->service->title }}</td>
                                        <td>
                                            <div><strong>{{ $booking->date->format('Y-m-d') }}</strong></div>
                                            <div class="text-secondary small">{{ Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</div>
                                        </td>
                                        <td>
                                            @if($booking->status === 'AwaitingPayment')
                                                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">بانتظار الدفع</span>
                                            @elseif($booking->status === 'Confirmed')
                                                <span class="badge bg-success px-3 py-2 rounded-pill">مؤكد</span>
                                            @elseif($booking->status === 'Completed')
                                                <span class="badge bg-secondary px-3 py-2 rounded-pill">مكتمل</span>
                                            @elseif($booking->status === 'CancelledByPatient' || $booking->status === 'CancelledByDoctor')
                                                <span class="badge bg-danger px-3 py-2 rounded-pill">ملغي</span>
                                            @elseif($booking->status === 'NoShow')
                                                <span class="badge bg-dark px-3 py-2 rounded-pill">لم يحضر</span>
                                            @elseif($booking->status === 'Expired')
                                                <span class="badge bg-warning px-3 py-2 rounded-pill">منتهي الصلاحية</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($booking->payment)
                                                @if($booking->payment->status === 'Pending')
                                                    <span class="badge bg-warning text-dark">معلق</span>
                                                @elseif($booking->payment->status === 'Paid')
                                                    <span class="badge bg-success">تم الدفع</span>
                                                @elseif($booking->payment->status === 'Failed')
                                                    <span class="badge bg-danger">فشل الدفع</span>
                                                @elseif($booking->payment->status === 'RefundPending')
                                                    <span class="badge bg-info text-dark">طلب استرداد معلق</span>
                                                @elseif($booking->payment->status === 'Refunded')
                                                    <span class="badge bg-secondary">تم الاسترداد</span>
                                                @else
                                                    <span class="badge bg-dark">{{ $booking->payment->status }}</span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">غير متوفر</span>
                                            @endif
                                        </td>
                                        <td class="fw-bold">${{ number_format($booking->service->price, 2) }}</td>
                                        <td class="pe-4 text-end">
                                            @if(in_array($booking->status, ['AwaitingPayment', 'Confirmed']))
                                                <!-- Cancel Button -->
                                                <form action="{{ route('patient.booking.cancel', $booking->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من رغبتك في إلغاء هذا الحجز؟ (سيتم استرداد الرسوم تلقائياً إذا كان الحجز قبل أكثر من 24 ساعة من الموعد)')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">إلغاء الموعد</button>
                                                </form>
                                            @else
                                                <span class="text-secondary small">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-secondary">
                                            <i class="bi bi-calendar-x fs-1 d-block mb-3"></i>
                                            لم تقم بأي حجوزات بعد.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
