@extends('layouts.admin')

@section('title', 'لوحة التحكم الرئيسية')

@section('content')
<div class="row g-4 mb-4">
    <!-- Stat 1 -->
    <div class="col-md-4 col-lg-2.4 col-sm-6">
        <div class="card border-0 shadow-sm p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-secondary mb-1">حجوزات اليوم</h6>
                    <h3 class="fw-bold m-0 text-primary">{{ $stats['today_bookings'] }}</h3>
                </div>
                <div class="fs-1 text-primary"><i class="bi bi-calendar-check"></i></div>
            </div>
        </div>
    </div>
    <!-- Stat 2 -->
    <div class="col-md-4 col-lg-2.4 col-sm-6">
        <div class="card border-0 shadow-sm p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-secondary mb-1">حجوزات قادمة</h6>
                    <h3 class="fw-bold m-0 text-success">{{ $stats['upcoming_bookings'] }}</h3>
                </div>
                <div class="fs-1 text-success"><i class="bi bi-calendar-event"></i></div>
            </div>
        </div>
    </div>
    <!-- Stat 3 -->
    <div class="col-md-4 col-lg-2.4 col-sm-6">
        <div class="card border-0 shadow-sm p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-secondary mb-1">إجمالي المرضى</h6>
                    <h3 class="fw-bold m-0 text-info">{{ $stats['total_patients'] }}</h3>
                </div>
                <div class="fs-1 text-info"><i class="bi bi-people"></i></div>
            </div>
        </div>
    </div>
    <!-- Stat 4 -->
    <div class="col-md-4 col-lg-2.4 col-sm-6">
        <div class="card border-0 shadow-sm p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-secondary mb-1">الاستشارات المكتملة</h6>
                    <h3 class="fw-bold m-0 text-secondary">{{ $stats['completed'] }}</h3>
                </div>
                <div class="fs-1 text-secondary"><i class="bi bi-patch-check"></i></div>
            </div>
        </div>
    </div>
    <!-- Stat 5 -->
    <div class="col-md-4 col-lg-2.4 col-sm-6">
        <div class="card border-0 shadow-sm p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-secondary mb-1">إجمالي الدخل</h6>
                    <h3 class="fw-bold m-0 text-dark">${{ number_format($stats['revenue'], 2) }}</h3>
                </div>
                <div class="fs-1 text-warning"><i class="bi bi-currency-dollar"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold m-0"><i class="bi bi-clock-history me-1 text-teal" style="color: var(--accent-color);"></i> جدول مواعيد كشوفات اليوم</h5>
                <span class="badge bg-secondary px-3 py-2 rounded-pill">{{ date('Y-m-d') }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">المريض</th>
                                <th>نوع الاستشارة</th>
                                <th>الوقت</th>
                                <th>الحالة</th>
                                <th>حالة الدفع</th>
                                <th class="pe-4 text-end">تغيير الحالة السريع</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($todayAppointments as $app)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ $app->patient->name }}</div>
                                        <div class="text-secondary small">{{ $app->patient->phone }}</div>
                                    </td>
                                    <td>{{ $app->service->title }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark px-2 py-1.5 fs-7 border border-secondary-subtle">
                                            {{ Carbon\Carbon::parse($app->start_time)->format('H:i') }} - {{ Carbon\Carbon::parse($app->end_time)->format('H:i') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($app->status === 'AwaitingPayment')
                                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">بانتظار الدفع</span>
                                        @elseif($app->status === 'Confirmed')
                                            <span class="badge bg-success px-3 py-2 rounded-pill">مؤكد</span>
                                        @elseif($app->status === 'Completed')
                                            <span class="badge bg-secondary px-3 py-2 rounded-pill">مكتمل</span>
                                        @elseif($app->status === 'CancelledByPatient' || $app->status === 'CancelledByDoctor')
                                            <span class="badge bg-danger px-3 py-2 rounded-pill">ملغي</span>
                                        @elseif($app->status === 'NoShow')
                                            <span class="badge bg-dark px-3 py-2 rounded-pill">لم يحضر</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($app->payment)
                                            <span class="badge @if($app->payment->status === 'Paid') bg-success @else bg-warning text-dark @endif">
                                                {{ $app->payment->status === 'Paid' ? 'تم الدفع مالي' : 'غير مدفوع' }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">لا يوجد</span>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end">
                                        @if($app->status === 'Confirmed')
                                            <div class="btn-group gap-1">
                                                <!-- Complete -->
                                                <form action="{{ route('admin.bookings.status', $app->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="Completed">
                                                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-2.5">اكتمال كشف</button>
                                                </form>

                                                <!-- No Show -->
                                                <form action="{{ route('admin.bookings.status', $app->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="NoShow">
                                                    <button type="submit" class="btn btn-sm btn-dark rounded-pill px-2.5">غائب</button>
                                                </form>

                                                <!-- Cancel (Refund) -->
                                                <form action="{{ route('admin.bookings.status', $app->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من إلغاء الحجز وإرجاع المبلغ مالياً للمريض؟')">
                                                    @csrf
                                                    <input type="hidden" name="status" value="CancelledByDoctor">
                                                    <button type="submit" class="btn btn-sm btn-danger rounded-pill px-2.5">إلغاء وإرجاع</button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-secondary small">لا توجد إجراءات متاحة</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-secondary">
                                        <i class="bi bi-calendar-x fs-1 d-block mb-3"></i>
                                        لا توجد مواعيد كشوفات مسجلة لليوم.
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
@endsection
