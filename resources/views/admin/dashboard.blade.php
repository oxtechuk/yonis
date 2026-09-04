@extends('layouts.admin')

@section('title', 'لوحة التحكم الرئيسية والعمليات الحية')

@section('styles')
<style>
    .kpi-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(64, 85, 165, 0.08);
        position: relative;
        overflow: hidden;
    }
    .kpi-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 28px rgba(64, 85, 165, 0.12) !important;
    }
    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--primary-dark));
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .kpi-card:hover::before {
        opacity: 1;
    }
    .chart-container {
        position: relative;
        height: 280px;
        width: 100%;
    }
    .chart-donut-container {
        position: relative;
        height: 240px;
        width: 100%;
    }
    .live-pulse {
        width: 8px;
        height: 8px;
        background-color: #10b981;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 rgba(16, 185, 129, 0.7);
        animation: pulse 1.8s infinite;
    }
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        }
        70% {
            box-shadow: 0 0 0 8px rgba(16, 185, 129, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
        }
    }
</style>
@endsection

@section('content')
<!-- 5 Key KPI Metrics Cards (Clean & Spacious Grid) -->
<div class="row row-cols-2 row-cols-md-3 row-cols-xl-5 g-3 mb-4">
    <!-- Revenue Card -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-secondary small fw-bold">إجمالي الإيرادات</span>
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(212, 175, 55, 0.12); color: var(--accent-gold);">
                    <i class="bi bi-currency-dollar fs-5"></i>
                </div>
            </div>
            <h3 class="fw-black text-dark m-0 mb-1">{{ number_format($stats['revenue'], 0) }} {{ \App\Models\Setting::currencySymbol() }}</h3>
            <div class="d-flex align-items-center justify-content-between mt-1">
                <span class="text-muted" style="font-size: 0.72rem;">معلق: {{ number_format($stats['pending_revenue'], 0) }} {{ \App\Models\Setting::currencySymbol() }}</span>
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill" style="font-size: 0.7rem;">محصل</span>
            </div>
        </div>
    </div>

    <!-- Today Bookings -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-secondary small fw-bold">مواعيد اليوم</span>
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(16, 185, 129, 0.12); color: #10b981;">
                    <i class="bi bi-calendar2-check-fill fs-5"></i>
                </div>
            </div>
            <h3 class="fw-black text-dark m-0 mb-1">{{ $stats['today_bookings'] }} <span class="text-muted fs-6 fw-normal">جلسة</span></h3>
            <div class="d-flex align-items-center justify-content-between small">
                <span class="text-muted" style="font-size: 0.72rem;">جدول اليوم</span>
                <span class="badge bg-success text-white rounded-pill px-2" style="font-size: 0.7rem;">اليوم</span>
            </div>
        </div>
    </div>

    <!-- Upcoming Bookings -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-secondary small fw-bold">حجوزات قادمة</span>
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(64, 85, 165, 0.12); color: var(--primary-color);">
                    <i class="bi bi-clock-history fs-5"></i>
                </div>
            </div>
            <h3 class="fw-black text-dark m-0 mb-1">{{ $stats['upcoming_bookings'] }} <span class="text-muted fs-6 fw-normal">حجز</span></h3>
            <div class="d-flex align-items-center justify-content-between small">
                <span class="text-muted" style="font-size: 0.72rem;">الأسبوع القادم</span>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill" style="font-size: 0.7rem;">مؤكدة</span>
            </div>
        </div>
    </div>

    <!-- Total Patients -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-secondary small fw-bold">إجمالي المرضى</span>
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(59, 130, 246, 0.12); color: #3b82f6;">
                    <i class="bi bi-people-fill fs-5"></i>
                </div>
            </div>
            <h3 class="fw-black text-dark m-0 mb-1">{{ $stats['total_patients'] }} <span class="text-muted fs-6 fw-normal">مريض</span></h3>
            <div class="d-flex align-items-center justify-content-between small">
                <span class="text-muted" style="font-size: 0.72rem;">+{{ $stats['new_patients_this_month'] }} هذا الشهر</span>
                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill" style="font-size: 0.7rem;">مسجلين</span>
            </div>
        </div>
    </div>

    <!-- Completion Rate -->
    <div class="col">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-secondary small fw-bold">نسبة الاكتمال</span>
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(13, 148, 136, 0.12); color: #0d9488;">
                    <i class="bi bi-patch-check-fill fs-5"></i>
                </div>
            </div>
            <h3 class="fw-black text-dark m-0 mb-1">{{ $stats['completion_rate'] }}%</h3>
            <div class="d-flex align-items-center justify-content-between small">
                <span class="text-muted" style="font-size: 0.72rem;">{{ $stats['completed'] }} جلسة مكتملة</span>
                <span class="badge bg-success-subtle text-success rounded-pill" style="font-size: 0.7rem;">ممتاز</span>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row: Monthly Revenue/Bookings + Channels Donut -->
<div class="row g-4 mb-4">
    <!-- Revenue & Bookings Trend Area Chart -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
                <div>
                    <h5 class="fw-bold m-0 text-primary d-flex align-items-center gap-2">
                        <i class="bi bi-graph-up-arrow fs-5"></i> مؤشر نمو الإيرادات والحجوزات (آخر 6 أشهر)
                    </h5>
                    <p class="text-secondary small m-0">تتبع تدفق المبيعات وعدد الجلسات المنجزة شهرياً.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge rounded-pill px-3 py-1.5" style="background: rgba(64, 85, 165, 0.1); color: var(--primary-color); font-weight: 700;">
                        <i class="bi bi-cash me-1"></i> إجمالي: {{ number_format(array_sum($monthlyRevenues), 0) }} {{ \App\Models\Setting::currencySymbol() }}
                    </span>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="monthlyTrendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Channels Breakdown Donut Chart -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
            <div class="mb-3">
                <h5 class="fw-bold m-0 text-primary d-flex align-items-center gap-2">
                    <i class="bi bi-pie-chart-fill fs-5"></i> توزيع قنوات الاستشارة
                </h5>
                <p class="text-secondary small m-0">نسبة الإقبال بين العيادة والفيديو والصوت والشات.</p>
            </div>
            <div class="chart-donut-container mb-3">
                <canvas id="channelsDonutChart"></canvas>
            </div>
            <div class="row g-2 pt-2 border-top small text-center">
                <div class="col-6">
                    <div class="p-2 rounded-3 bg-light">
                        <span class="text-danger fw-bold d-block"><i class="bi bi-hospital me-1"></i> العيادة</span>
                        <strong class="fs-6 text-dark">{{ $clinicCount }}</strong>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-2 rounded-3 bg-light">
                        <span class="text-info fw-bold d-block"><i class="bi bi-camera-video me-1"></i> فيديو</span>
                        <strong class="fs-6 text-dark">{{ $videoCount }}</strong>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-2 rounded-3 bg-light">
                        <span class="text-success fw-bold d-block"><i class="bi bi-telephone me-1"></i> صوت</span>
                        <strong class="fs-6 text-dark">{{ $voiceCount }}</strong>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-2 rounded-3 bg-light">
                        <span class="text-warning-emphasis fw-bold d-block"><i class="bi bi-chat-dots me-1"></i> شات</span>
                        <strong class="fs-6 text-dark">{{ $chatCount }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Operations: Today's Live Schedule + Recent Stream & Top Services -->
<div class="row g-4 mb-4">
    <!-- Today's Live Schedule -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold m-0 text-primary d-flex align-items-center gap-2">
                        <span class="live-pulse"></span> جدول مواعيد كشوفات اليوم الحية
                    </h5>
                    <p class="text-secondary small m-0">المواعيد المسجلة لتاريخ اليوم ({{ date('Y-m-d') }}).</p>
                </div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1.5 fw-bold">
                    {{ count($todayAppointments) }} مواعيد اليوم
                </span>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">المريض</th>
                                <th>نوع الاستشارة والقناة</th>
                                <th>الوقت</th>
                                <th>الحالة</th>
                                <th>تواصل</th>
                                <th class="pe-4 text-end">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($todayAppointments as $app)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px; background: rgba(64, 85, 165, 0.1); color: var(--primary-color);">
                                                <i class="bi bi-person-fill fs-5"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $app->patient ? $app->patient->name : ($app->temp_user_data['name'] ?? 'زائر') }}</div>
                                                <div class="text-secondary small">{{ $app->patient ? $app->patient->phone : ($app->temp_user_data['phone'] ?? '-') }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark small">{{ $app->service?->title ?? 'جلسة استشارة' }}</div>
                                        <span class="badge @if($app->booking_type === 'clinic') bg-danger-subtle text-danger border border-danger-subtle @else bg-info-subtle text-info-emphasis border border-info-subtle @endif rounded-pill px-2 py-0.5 mt-1" style="font-size: 0.7rem;">
                                            {{ $app->booking_type_label }} - {{ $app->consultation_type_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 fw-bold">
                                            <i class="bi bi-clock me-1 text-primary"></i>
                                            {{ Carbon\Carbon::parse($app->start_time)->format('H:i') }} - {{ Carbon\Carbon::parse($app->end_time)->format('H:i') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($app->status === 'AwaitingPayment')
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1 rounded-pill small fw-bold">بانتظار الدفع</span>
                                        @elseif($app->status === 'Confirmed')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill small fw-bold">مؤكد</span>
                                        @elseif($app->status === 'Completed')
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2.5 py-1 rounded-pill small fw-bold">مكتمل</span>
                                        @elseif(str_contains($app->status, 'Cancelled'))
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill small fw-bold">ملغي</span>
                                        @elseif($app->status === 'NoShow')
                                            <span class="badge bg-dark-subtle text-dark border border-dark-subtle px-2.5 py-1 rounded-pill small fw-bold">لم يحضر</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $rawPhone = $app->patient ? $app->patient->phone : ($app->temp_user_data['phone'] ?? '');
                                            $cleanPhone = preg_replace('/\D/', '', $rawPhone);
                                        @endphp
                                        @if(!empty($cleanPhone))
                                            <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-2.5 py-1 small fw-bold d-inline-flex align-items-center gap-1" title="مراسلة واتساب">
                                                <i class="bi bi-whatsapp"></i> واتساب
                                            </a>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 fw-bold shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-2 small" style="min-width: 180px; z-index: 1050;">
                                                @if($app->status === 'AwaitingPayment')
                                                    <li>
                                                        <form action="{{ route('admin.bookings.status', $app->id) }}" method="POST" class="m-0">
                                                            @csrf
                                                            <input type="hidden" name="status" value="Confirmed">
                                                            <button type="submit" class="dropdown-item rounded-3 py-2 fw-bold text-success d-flex align-items-center gap-2">
                                                                <i class="bi bi-check-circle-fill"></i> تأكيد استلام الدفع
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                                @if($app->status === 'Confirmed')
                                                    <li>
                                                        <form action="{{ route('admin.bookings.status', $app->id) }}" method="POST" class="m-0">
                                                            @csrf
                                                            <input type="hidden" name="status" value="Completed">
                                                            <button type="submit" class="dropdown-item rounded-3 py-2 fw-bold text-info d-flex align-items-center gap-2">
                                                                <i class="bi bi-check2-all"></i> اكتمال الجلسة
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('admin.bookings.status', $app->id) }}" method="POST" class="m-0">
                                                            @csrf
                                                            <input type="hidden" name="status" value="NoShow">
                                                            <button type="submit" class="dropdown-item rounded-3 py-2 fw-bold text-secondary d-flex align-items-center gap-2">
                                                                <i class="bi bi-person-x-fill"></i> تسجيل غياب
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                                @if($app->patient)
                                                    <li>
                                                        <a class="dropdown-item rounded-3 py-2 fw-bold text-dark d-flex align-items-center gap-2" href="{{ route('admin.patients.details', $app->patient->id) }}">
                                                            <i class="bi bi-file-medical-fill text-primary"></i> السجل الطبي
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-secondary">
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-2 text-muted" style="width: 54px; height: 54px; font-size: 1.5rem;">
                                            <i class="bi bi-calendar2-check"></i>
                                        </div>
                                        <div class="fw-bold text-dark">لا توجد مواعيد متبقية لليوم!</div>
                                        <div class="small text-muted">يمكنك مراجعة الحجوزات القادمة من جدول المواعيد أو التقويم.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Services & Recent Activity Stream -->
    <div class="col-lg-4">
        <!-- Top Services -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold m-0 text-primary d-flex align-items-center gap-2">
                    <i class="bi bi-trophy-fill text-warning fs-5"></i> الخدمات الأكثر طلباً
                </h5>
                <a href="{{ route('admin.services') }}" class="small fw-bold text-decoration-none">إدارة</a>
            </div>
            <div class="d-flex flex-column gap-2.5">
                @foreach($topServices as $srv)
                    <div class="p-3 bg-light rounded-3 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="fw-bold text-dark small mb-0.5">{{ Str::limit($srv->title, 28) }}</div>
                            <span class="text-secondary" style="font-size: 0.75rem;"><i class="bi bi-clock me-1"></i> {{ $srv->duration }} دقيقة</span>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fw-bold">
                                {{ $srv->bookings_count }} حجز
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Quick Manual Booking Trigger Card -->
        <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-light border">
            <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 50px; height: 50px; background: rgba(64, 85, 165, 0.12); color: var(--primary-color); font-size: 1.5rem;">
                <i class="bi bi-calendar-plus-fill"></i>
            </div>
            <h6 class="fw-bold text-dark mb-1">تسجيل حجز يدوي مباشر</h6>
            <p class="text-secondary small mb-3">إضافة كشف عيادة أو جلسة استشارية مباشرة لمريض من لوحة الإدارة.</p>
            <button type="button" class="btn btn-royal-primary rounded-pill px-4 py-2 small fw-bold w-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#manualBookingModal">
                <i class="bi bi-plus-lg me-1"></i> فتح نموذج الحجز اليدوي
            </button>
        </div>
    </div>
</div>

<!-- Manual Booking Modal -->
<div class="modal fade" id="manualBookingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold text-dark fs-6"><i class="bi bi-calendar-plus text-primary me-2"></i> إضافة حجز يدوي جديد</h5>
                <button type="button" class="btn-close ms-0" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.bookings.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">اختر المريض (أو مسجل مسبقاً)</label>
                            <select name="patient_id" class="form-select rounded-3" required>
                                <option value="">-- اختر المريض --</option>
                                @foreach($allPatients as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->phone }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">الخدمة الطبية</label>
                            <select name="service_id" class="form-select rounded-3" required>
                                @foreach($allServices as $s)
                                    <option value="{{ $s->id }}">{{ $s->title }} (${{ $s->price }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">نوع الحجز</label>
                            <select name="booking_type" class="form-select rounded-3" required>
                                <option value="online">استشارة أونلاين</option>
                                <option value="clinic">كشف في مقر العيادة</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">قناة الاستشارة</label>
                            <select name="consultation_type" class="form-select rounded-3">
                                <option value="video">مكالمة فيديو</option>
                                <option value="voice">مكالمة صوتية</option>
                                <option value="chat">محادثة نصية (شات)</option>
                                <option value="clinic">عيادة</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">تاريخ الموعد</label>
                            <input type="date" name="date" class="form-control rounded-3" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">وقت البدء</label>
                            <input type="time" name="start_time" class="form-control rounded-3" value="16:00" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">وقت الانتهاء</label>
                            <input type="time" name="end_time" class="form-control rounded-3" value="16:30" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">حالة الحجز المبدئية</label>
                        <select name="status" class="form-select rounded-3">
                            <option value="Confirmed" selected>مؤكد مباشرة</option>
                            <option value="AwaitingPayment">بانتظار الدفع</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-royal-primary btn-sm rounded-pill px-4 fw-bold">حفظ وتأكيد الحجز</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Chart.js 4.4 CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Monthly Trend Area Chart
    const trendCtx = document.getElementById('monthlyTrendChart');
    if (trendCtx) {
        const monthLabels = @json($monthLabels);
        const monthlyBookings = @json($monthlyBookings);
        const monthlyRevenues = @json($monthlyRevenues);

        const gradient = trendCtx.getContext('2d').createLinearGradient(0, 0, 0, 260);
        gradient.addColorStop(0, 'rgba(64, 85, 165, 0.35)');
        gradient.addColorStop(1, 'rgba(64, 85, 165, 0.00)');

        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: monthLabels,
                datasets: [
                    {
                        label: 'الإيرادات ($)',
                        data: monthlyRevenues,
                        borderColor: '#4055A5',
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointBackgroundColor: '#1C2752',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        yAxisID: 'y'
                    },
                    {
                        label: 'عدد الحجوزات',
                        data: monthlyBookings,
                        borderColor: '#10b981',
                        borderDash: [5, 5],
                        backgroundColor: 'transparent',
                        tension: 0.4,
                        borderWidth: 2,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        rtl: true,
                        labels: {
                            font: { family: 'Cairo', size: 12, weight: 'bold' },
                            usePointStyle: true,
                            boxWidth: 8
                        }
                    },
                    tooltip: {
                        rtl: true,
                        titleFont: { family: 'Cairo', size: 13, weight: 'bold' },
                        bodyFont: { family: 'Cairo', size: 12 },
                        padding: 12,
                        cornerRadius: 10,
                        backgroundColor: '#1C2752'
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Cairo', size: 11 } }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: { color: 'rgba(0, 0, 0, 0.05)' },
                        ticks: {
                            callback: function(value) { return '$' + value; },
                            font: { family: 'Cairo', size: 11 }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        grid: { drawOnChartArea: false },
                        ticks: {
                            stepSize: 1,
                            font: { family: 'Cairo', size: 11 }
                        }
                    }
                }
            }
        });
    }

    // 2. Channels Donut Chart
    const channelsCtx = document.getElementById('channelsDonutChart');
    if (channelsCtx) {
        new Chart(channelsCtx, {
            type: 'doughnut',
            data: {
                labels: ['كشف العيادة', 'استشارة فيديو', 'استشارة صوت', 'محادثة شات'],
                datasets: [{
                    data: [{{ $clinicCount }}, {{ $videoCount }}, {{ $voiceCount }}, {{ $chatCount }}],
                    backgroundColor: [
                        '#ef4444', // Clinic - Red
                        '#3b82f6', // Video - Blue
                        '#10b981', // Voice - Green
                        '#f59e0b'  // Chat - Amber
                    ],
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        rtl: true,
                        titleFont: { family: 'Cairo', size: 13, weight: 'bold' },
                        bodyFont: { family: 'Cairo', size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        backgroundColor: '#1C2752'
                    }
                }
            }
        });
    }
});
</script>
@endsection
