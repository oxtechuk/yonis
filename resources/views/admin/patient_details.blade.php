@extends('layouts.admin')

@section('title', 'السجل الطبي للمريض')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm p-4">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-person-bounding-box text-teal fs-1" style="color: var(--accent-color);"></i>
                <div>
                    <h3 class="fw-bold m-0 text-dark">{{ $patient->name }}</h3>
                    <p class="text-secondary m-0">رقم الهاتف: {{ $patient->phone }} | البريد: {{ $patient->email }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-0">
        <h5 class="fw-bold m-0"><i class="bi bi-journal-medical me-1 text-teal" style="color: var(--accent-color);"></i> كشف الحجوزات والاستشارات السابقة لهذا المريض</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">المرجع</th>
                        <th>الاستشارة الطبية</th>
                        <th>التاريخ والوقت</th>
                        <th>الحالة</th>
                        <th>قيمة الكشف</th>
                        <th>المعاملة المالية</th>
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
                                @endif
                            </td>
                            <td class="fw-bold">${{ number_format($booking->service->price, 2) }}</td>
                            <td>
                                @if($booking->payment)
                                    <div><span class="badge @if($booking->payment->status === 'Paid') bg-success @else bg-warning text-dark @endif">{{ $booking->payment->status }}</span></div>
                                    <div class="text-secondary small">{{ $booking->payment->payment_intent_id }}</div>
                                @else
                                    <span class="text-secondary small">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-secondary">
                                <i class="bi bi-calendar-x fs-1 d-block mb-3"></i>
                                لا توجد سجلات كشوفات سابقة مسجلة للمريض.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
