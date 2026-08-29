@extends('layouts.admin')

@section('title', 'التقارير والإحصائيات الشاملة')

@section('styles')
<style>
    .report-stat-card {
        border: 1px solid rgba(64, 85, 165, 0.08);
        border-radius: 12px;
        transition: all 0.2s ease;
    }
    .report-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(64, 85, 165, 0.08) !important;
    }
    .chart-report-container {
        position: relative;
        height: 220px;
        width: 100%;
    }
    @media print {
        body {
            background-color: #ffffff !important;
        }
        .admin-sidebar, .card-header button, .btn, .d-print-none, form {
            display: none !important;
        }
        .card {
            border: 1px solid #ddd !important;
            box-shadow: none !important;
            break-inside: avoid;
        }
        .print-only-header {
            display: block !important;
        }
    }
    .print-only-header {
        display: none;
    }
</style>
@endsection

@section('content')
<!-- Printable Header (Visible Only on Print) -->
<div class="print-only-header text-center mb-3 pb-2 border-bottom">
    <h4 class="fw-bold m-0">مركز د. {{ \App\Models\Setting::get('doctor_name', 'يونس المرشد') }} للاستشارات النفسية</h4>
    <h6 class="text-secondary m-0 mt-1">تقرير العمليات والأداء المالي الشامل</h6>
    <p class="small text-muted m-0 mt-0.5">الفترة من: {{ $metrics['start_date'] }} إلى: {{ $metrics['end_date'] }} | تاريخ الاستخراج: {{ date('Y-m-d H:i') }}</p>
</div>

<!-- Page Top Header & Action Controls (Compact) -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 d-print-none">
    <div>
        <h5 class="fw-bold m-0 text-primary d-flex align-items-center gap-2">
            <i class="bi bi-bar-chart-line-fill fs-5"></i> مركز التقارير والإحصائيات الشاملة
        </h5>
        <span class="text-secondary small">تحليل الأداء المالي، وحركة الحجوزات، وأداء الخدمات الطبية.</span>
    </div>
    <div class="d-flex align-items-center gap-1.5">
        <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1.5 fw-bold text-dark shadow-sm d-flex align-items-center gap-1" onclick="window.print()">
            <i class="bi bi-printer text-primary"></i> طباعة
        </button>
        <a href="{{ route('admin.reports.export', request()->all()) }}" class="btn btn-sm btn-royal-primary rounded-pill px-3 py-1.5 fw-bold shadow-sm d-flex align-items-center gap-1">
            <i class="bi bi-file-earmark-excel"></i> تصدير Excel
        </a>
    </div>
</div>

<!-- Compact Filter & Date Range Bar -->
<div class="card border-0 shadow-sm rounded-4 p-3 mb-3 bg-white d-print-none">
    <form action="{{ route('admin.reports') }}" method="GET">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2.5 pb-2 border-bottom">
            <div class="small fw-bold text-dark d-flex align-items-center gap-1.5">
                <i class="bi bi-funnel-fill text-primary"></i> النطاق الزمني:
            </div>
            {{-- Quick Presets Compact Pills --}}
            <div class="btn-group btn-group-sm rounded-pill overflow-hidden border p-0.5 bg-light" role="group">
                <a href="{{ route('admin.reports', array_merge(request()->except(['period', 'start_date', 'end_date']), ['period' => 'today'])) }}" class="btn btn-sm {{ $period === 'today' ? 'btn-royal-primary' : 'btn-light' }} rounded-pill px-2.5 py-0.5 fw-bold" style="font-size: 0.75rem;">اليوم</a>
                <a href="{{ route('admin.reports', array_merge(request()->except(['period', 'start_date', 'end_date']), ['period' => 'week'])) }}" class="btn btn-sm {{ $period === 'week' ? 'btn-royal-primary' : 'btn-light' }} rounded-pill px-2.5 py-0.5 fw-bold" style="font-size: 0.75rem;">هذا الأسبوع</a>
                <a href="{{ route('admin.reports', array_merge(request()->except(['period', 'start_date', 'end_date']), ['period' => 'month'])) }}" class="btn btn-sm {{ $period === 'month' ? 'btn-royal-primary' : 'btn-light' }} rounded-pill px-2.5 py-0.5 fw-bold" style="font-size: 0.75rem;">هذا الشهر</a>
                <a href="{{ route('admin.reports', array_merge(request()->except(['period', 'start_date', 'end_date']), ['period' => 'year'])) }}" class="btn btn-sm {{ $period === 'year' ? 'btn-royal-primary' : 'btn-light' }} rounded-pill px-2.5 py-0.5 fw-bold" style="font-size: 0.75rem;">هذا العام</a>
                <a href="{{ route('admin.reports', array_merge(request()->except(['period', 'start_date', 'end_date']), ['period' => 'all'])) }}" class="btn btn-sm {{ $period === 'all' ? 'btn-royal-primary' : 'btn-light' }} rounded-pill px-2.5 py-0.5 fw-bold" style="font-size: 0.75rem;">الكل</a>
            </div>
        </div>

        <div class="row g-2 align-items-end">
            <div class="col-lg-2 col-md-3 col-6">
                <label class="form-label text-secondary mb-1" style="font-size: 0.75rem;">من تاريخ:</label>
                <input type="date" name="start_date" class="form-control form-control-sm rounded-3" value="{{ $startDate }}">
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <label class="form-label text-secondary mb-1" style="font-size: 0.75rem;">إلى تاريخ:</label>
                <input type="date" name="end_date" class="form-control form-control-sm rounded-3" value="{{ $endDate }}">
            </div>
            <div class="col-lg-3 col-md-3 col-6">
                <label class="form-label text-secondary mb-1" style="font-size: 0.75rem;">الخدمة الطبية:</label>
                <select name="service_id" class="form-select form-select-sm rounded-3">
                    <option value="">جميع الخدمات</option>
                    @foreach($allServices as $s)
                        <option value="{{ $s->id }}" @if(request('service_id') == $s->id) selected @endif>{{ $s->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <label class="form-label text-secondary mb-1" style="font-size: 0.75rem;">نوع الحجز:</label>
                <select name="booking_type" class="form-select form-select-sm rounded-3">
                    <option value="">الكل</option>
                    <option value="clinic" @if(request('booking_type') === 'clinic') selected @endif>عيادة</option>
                    <option value="online" @if(request('booking_type') === 'online') selected @endif>أونلاين</option>
                </select>
            </div>
            <div class="col-lg-3 col-md-12 d-flex gap-1.5">
                <button type="submit" class="btn btn-sm btn-royal-primary rounded-pill px-3 py-1.5 fw-bold flex-grow-1 shadow-sm">
                    <i class="bi bi-search me-1"></i> تصفية
                </button>
                <a href="{{ route('admin.reports') }}" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1.5 text-secondary" title="إعادة تعيين">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Compact KPI Summary Cards -->
<div class="row g-2.5 mb-3">
    <!-- Paid Revenue -->
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm rounded-3 p-2.5 bg-white report-stat-card h-100">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <span class="text-secondary small fw-bold" style="font-size: 0.75rem;">الإيرادات المحصلة</span>
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(16, 185, 129, 0.12); color: #10b981;">
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>
            <h4 class="fw-black text-success m-0 mb-0.5">${{ number_format($metrics['paid_revenue'], 2) }}</h4>
            <span class="text-muted" style="font-size: 0.7rem;">إجمالي المدفوع فعلياً</span>
        </div>
    </div>

    <!-- Total Bookings -->
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm rounded-3 p-2.5 bg-white report-stat-card h-100">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <span class="text-secondary small fw-bold" style="font-size: 0.75rem;">إجمالي الحجوزات</span>
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(64, 85, 165, 0.12); color: var(--primary-color);">
                    <i class="bi bi-calendar-range"></i>
                </div>
            </div>
            <h4 class="fw-black text-dark m-0 mb-0.5">{{ $metrics['total_bookings'] }} <span class="text-muted fs-7 fw-normal">جلسة</span></h4>
            <span class="text-muted" style="font-size: 0.7rem;">مكتمل: {{ $metrics['completed_count'] }} | ملغي: {{ $metrics['cancelled_count'] }}</span>
        </div>
    </div>

    <!-- Pending Amount -->
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm rounded-3 p-2.5 bg-white report-stat-card h-100">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <span class="text-secondary small fw-bold" style="font-size: 0.75rem;">المبالغ بانتظار الدفع</span>
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>
            <h4 class="fw-black text-warning-emphasis m-0 mb-0.5">${{ number_format($metrics['pending_revenue'], 2) }}</h4>
            <span class="text-muted" style="font-size: 0.7rem;">حجوزات قيد إتمام السداد</span>
        </div>
    </div>

    <!-- Average Ticket Value -->
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm rounded-3 p-2.5 bg-white report-stat-card h-100">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <span class="text-secondary small fw-bold" style="font-size: 0.75rem;">متوسط سعر الجلسة</span>
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(59, 130, 246, 0.12); color: #3b82f6;">
                    <i class="bi bi-receipt"></i>
                </div>
            </div>
            <h4 class="fw-black text-primary m-0 mb-0.5">${{ number_format($metrics['avg_booking_value'], 2) }}</h4>
            <span class="text-muted" style="font-size: 0.7rem;">معدل الدخل لكل استشارة</span>
        </div>
    </div>
</div>

<!-- Performance Trend Graph (Compact) -->
<div class="card border-0 shadow-sm rounded-4 p-3 mb-3 bg-white">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="fw-bold m-0 text-primary d-flex align-items-center gap-1.5">
            <i class="bi bi-graph-up"></i> المخطط الزمني لحركة المبيعات والحجوزات
        </h6>
        <span class="text-muted" style="font-size: 0.75rem;">توزيع الإيرادات وعدد الحجوزات</span>
    </div>
    <div class="chart-report-container">
        <canvas id="periodReportChart"></canvas>
    </div>
</div>

<!-- Analytical Tables Row: Services Performance + Channels Breakdown -->
<div class="row g-3 mb-3">
    <!-- Services Performance Table -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
            <div class="card-header bg-white py-2.5 px-3 border-0">
                <h6 class="fw-bold m-0 text-primary d-flex align-items-center gap-1.5">
                    <i class="bi bi-trophy-fill text-warning"></i> تقرير أداء الخدمات الطبية
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 py-2">الخدمة</th>
                                <th class="py-2">المدة</th>
                                <th class="py-2">الحجوزات</th>
                                <th class="py-2">الإيراد</th>
                                <th class="pe-3 py-2">نسبة الطلب</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($serviceStats as $srv)
                                <tr>
                                    <td class="ps-3 py-2">
                                        <div class="fw-bold text-dark">{{ $srv['title'] }}</div>
                                    </td>
                                    <td class="py-2"><span class="badge bg-light text-dark border px-2 py-0.5">{{ $srv['duration'] }} د</span></td>
                                    <td class="py-2"><strong>{{ $srv['count'] }}</strong></td>
                                    <td class="py-2 fw-bold text-success">${{ number_format($srv['revenue'], 2) }}</td>
                                    <td class="pe-3 py-2">
                                        <div class="d-flex align-items-center gap-1.5">
                                            <div class="progress flex-grow-1" style="height: 5px;">
                                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $srv['percentage'] }}%;"></div>
                                            </div>
                                            <span class="fw-bold text-secondary" style="font-size: 0.75rem;">{{ $srv['percentage'] }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-3 text-secondary">لا توجد بيانات خدمات في هذه الفترة.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Channels Breakdown -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <h6 class="fw-bold m-0 text-primary d-flex align-items-center gap-1.5 mb-2.5">
                <i class="bi bi-diagram-3-fill"></i> تحليل قنوات الاستشارة والدخل
            </h6>
            <div class="d-flex flex-column gap-2">
                @foreach($channelStats as $key => $ch)
                    <div class="p-2.5 bg-light rounded-3 border">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong class="text-dark small">
                                @if($key === 'clinic') <i class="bi bi-hospital text-danger me-1"></i> @endif
                                @if($key === 'video') <i class="bi bi-camera-video text-info me-1"></i> @endif
                                @if($key === 'voice') <i class="bi bi-telephone text-success me-1"></i> @endif
                                @if($key === 'chat') <i class="bi bi-chat-dots text-warning-emphasis me-1"></i> @endif
                                {{ $ch['title'] }}
                            </strong>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5 fw-bold" style="font-size: 0.7rem;">
                                {{ $ch['count'] }} حجز
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center text-secondary" style="font-size: 0.75rem;">
                            <span>الدخل المحصل:</span>
                            <strong class="text-success fs-7">${{ number_format($ch['revenue'], 2) }}</strong>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Detailed Filtered Bookings Table -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3">
    <div class="card-header bg-white py-2.5 px-3 border-0 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold m-0 text-primary d-flex align-items-center gap-1.5">
            <i class="bi bi-list-check"></i> سجل الحجوزات التفصيلي للفترة
        </h6>
        <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1" style="font-size: 0.75rem;">
            {{ $detailedBookings->total() }} سجل
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0" style="font-size: 0.85rem;">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-2">المرجع</th>
                        <th class="py-2">المريض</th>
                        <th class="py-2">الخدمة</th>
                        <th class="py-2">النوع والقناة</th>
                        <th class="py-2">التاريخ والوقت</th>
                        <th class="py-2">المبلغ</th>
                        <th class="py-2">الحالة</th>
                        <th class="pe-3 py-2">الدفع</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($detailedBookings as $b)
                        <tr>
                            <td class="ps-3 py-2 fw-bold text-dark">#{{ $b->booking_reference }}</td>
                            <td class="py-2">
                                <div class="fw-bold text-dark">{{ $b->patient ? $b->patient->name : ($b->temp_user_data['name'] ?? 'زائر') }}</div>
                                <div class="text-secondary" style="font-size: 0.75rem;">{{ $b->patient ? $b->patient->phone : ($b->temp_user_data['phone'] ?? '-') }}</div>
                            </td>
                            <td class="py-2">{{ $b->service?->title ?? '-' }}</td>
                            <td class="py-2">
                                <span class="badge @if($b->booking_type === 'clinic') bg-danger-subtle text-danger border border-danger-subtle @else bg-info-subtle text-info-emphasis border border-info-subtle @endif rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
                                    {{ $b->booking_type_label }} - {{ $b->consultation_type_label }}
                                </span>
                            </td>
                            <td class="py-2">
                                <div><strong>{{ $b->date instanceof \DateTimeInterface ? $b->date->format('Y-m-d') : substr((string)$b->date, 0, 10) }}</strong></div>
                                <div class="text-secondary" style="font-size: 0.75rem;">{{ Carbon\Carbon::parse($b->start_time)->format('H:i') }} - {{ Carbon\Carbon::parse($b->end_time)->format('H:i') }}</div>
                            </td>
                            <td class="py-2 fw-bold text-success">${{ number_format($b->price ?? $b->service?->price ?? 0, 2) }}</td>
                            <td class="py-2">
                                @if($b->status === 'AwaitingPayment')
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;">بانتظار الدفع</span>
                                @elseif($b->status === 'Confirmed')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;">مؤكد</span>
                                @elseif($b->status === 'Completed')
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;">مكتمل</span>
                                @elseif(str_contains($b->status, 'Cancelled'))
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;">ملغي</span>
                                @elseif($b->status === 'NoShow')
                                    <span class="badge bg-dark-subtle text-dark border border-dark-subtle px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;">لم يحضر</span>
                                @endif
                            </td>
                            <td class="pe-3 py-2">
                                @if($b->payment)
                                    <span class="badge @if($b->payment->status === 'Paid') bg-success-subtle text-success border border-success-subtle @else bg-warning-subtle text-warning-emphasis border border-warning-subtle @endif rounded-pill px-2 py-0.5 fw-bold" style="font-size: 0.7rem;">
                                        {{ $b->payment->status === 'Paid' ? 'مدفوع' : $b->payment->status }}
                                    </span>
                                @else
                                    <span class="badge @if($b->status === 'Confirmed') bg-success-subtle text-success border border-success-subtle @else bg-secondary-subtle text-secondary @endif rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
                                        {{ $b->status === 'Confirmed' ? 'مدفوع' : 'غير متوفر' }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-secondary">لا توجد حجوزات مطابقة لمعايير الفلترة المحددة.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($detailedBookings->hasPages())
            <div class="p-2.5 bg-light border-top d-flex justify-content-center d-print-none">
                {{ $detailedBookings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<!-- Chart.js 4.4 CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const reportCtx = document.getElementById('periodReportChart');
    if (reportCtx) {
        const trendLabels = @json($trendLabels);
        const trendBookings = @json($trendBookings);
        const trendRevenue = @json($trendRevenue);

        const gradient = reportCtx.getContext('2d').createLinearGradient(0, 0, 0, 200);
        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.35)');
        gradient.addColorStop(1, 'rgba(16, 185, 129, 0.00)');

        new Chart(reportCtx, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [
                    {
                        label: 'الإيرادات المحصلة ($)',
                        data: trendRevenue,
                        borderColor: '#10b981',
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2.5,
                        pointBackgroundColor: '#059669',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 3.5,
                        yAxisID: 'y'
                    },
                    {
                        label: 'عدد الحجوزات',
                        data: trendBookings,
                        borderColor: '#4055A5',
                        borderDash: [4, 4],
                        backgroundColor: 'transparent',
                        tension: 0.35,
                        borderWidth: 2,
                        pointBackgroundColor: '#1C2752',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 3.5,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'top',
                        rtl: true,
                        labels: { font: { family: 'Cairo', size: 11, weight: 'bold' }, boxWidth: 8 }
                    },
                    tooltip: {
                        rtl: true,
                        titleFont: { family: 'Cairo', size: 12, weight: 'bold' },
                        bodyFont: { family: 'Cairo', size: 11 },
                        backgroundColor: '#1C2752',
                        cornerRadius: 8,
                        padding: 8
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { family: 'Cairo', size: 10 } } },
                    y: {
                        type: 'linear',
                        position: 'right',
                        grid: { color: 'rgba(0, 0, 0, 0.04)' },
                        ticks: {
                            callback: function(v) { return '$' + v; },
                            font: { family: 'Cairo', size: 10 }
                        }
                    },
                    y1: {
                        type: 'linear',
                        position: 'left',
                        grid: { drawOnChartArea: false },
                        ticks: { stepSize: 1, font: { family: 'Cairo', size: 10 } }
                    }
                }
            }
        });
    }
});
</script>
@endsection
