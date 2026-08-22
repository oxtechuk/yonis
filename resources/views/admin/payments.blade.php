@extends('layouts.admin')

@section('title', 'سجل المدفوعات والتقارير المالية')

@section('content')

<!-- Total Revenue Card & Filter Bar -->
<div class="row g-3 mb-4 d-print-none">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-primary text-white h-100 d-flex flex-column justify-content-center">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-white-50 fw-bold small"><i class="bi bi-wallet2 me-1"></i> إجمالي الإيرادات المحصلة</span>
                <span class="badge bg-white text-primary rounded-pill px-3 py-1 fw-bold">إيرادات دقيقة</span>
            </div>
            <h2 class="fw-bold mb-0">${{ number_format($totalRevenue ?? 0, 2) }}</h2>
            <div class="text-white-50 small mt-1" style="font-size:0.75rem;">تتضمن جميع المدفوعات الناجحة أونلاين وفي العيادة</div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold m-0 text-primary d-flex align-items-center gap-2">
                    <i class="bi bi-funnel-fill fs-5"></i> تصفية واستخراج تقرير المدفوعات
                </h5>
                <a href="{{ route('admin.payments.export', request()->query()) }}" class="btn btn-outline-success rounded-pill px-3 py-1.5 small fw-bold">
                    <i class="bi bi-file-earmark-spreadsheet-fill me-1"></i> تصدير تقرير مالى CSV
                </a>
            </div>

            <form action="{{ route('admin.payments') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">حالة العملية</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">جميع المعاملات</option>
                        <option value="Paid" @if(request('status') === 'Paid') selected @endif>ناجح (مدفوع)</option>
                        <option value="Pending" @if(request('status') === 'Pending') selected @endif>معلق</option>
                        <option value="Failed" @if(request('status') === 'Failed') selected @endif>فشل الدفع</option>
                        <option value="Refunded" @if(request('status') === 'Refunded') selected @endif>مسترد بالكامل</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">من تاريخ</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">إلى تاريخ</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm w-100 rounded-pill fw-bold">بحث وتصفية</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Payments Table Card -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white py-3 border-0">
        <h5 class="fw-bold m-0"><i class="bi bi-credit-card-fill text-primary me-2"></i> سجل المعاملات والمدفوعات الإلكترونية والكاش</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">معرف العملية</th>
                        <th>المريض</th>
                        <th>الخدمة المحجوزة</th>
                        <th>المبلغ</th>
                        <th>العملة</th>
                        <th>الحالة</th>
                        <th>المبلغ المسترد</th>
                        <th class="pe-4">تاريخ المعاملة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td class="ps-4 fw-bold text-dark font-monospace small">{{ $payment->payment_intent_id }}</td>
                            <td>
                                @if($payment->booking && $payment->booking->patient)
                                    <div class="fw-bold">{{ $payment->booking->patient->name }}</div>
                                    <div class="text-secondary small">{{ $payment->booking->patient->phone }}</div>
                                @else
                                    <span class="text-secondary small">غير متوفر</span>
                                @endif
                            </td>
                            <td>
                                @if($payment->booking && $payment->booking->service)
                                    {{ $payment->booking->service->title }}
                                @else
                                    <span class="text-secondary small">غير متوفر</span>
                                @endif
                            </td>
                            <td class="fw-bold text-success">${{ number_format($payment->amount, 2) }}</td>
                            <td>{{ strtoupper($payment->currency) }}</td>
                            <td>
                                @if($payment->status === 'Pending')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1 rounded-pill">معلق</span>
                                @elseif($payment->status === 'Paid')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">ناجح</span>
                                @elseif($payment->status === 'Failed')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill">فاشل</span>
                                @elseif($payment->status === 'Refunded')
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1 rounded-pill">تم الاسترداد</span>
                                @else
                                    <span class="badge bg-dark-subtle text-dark border border-dark-subtle px-3 py-1 rounded-pill">{{ $payment->status }}</span>
                                @endif
                            </td>
                            <td class="text-danger fw-bold">
                                @if($payment->refunded_amount > 0)
                                    -${{ number_format($payment->refunded_amount, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="pe-4 text-secondary small">{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-secondary">
                                <i class="bi bi-wallet2 fs-1 d-block mb-3 text-secondary"></i>
                                لم يتم العثور على أي معاملات مالية مطابقة.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center py-4">
            {{ $payments->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
