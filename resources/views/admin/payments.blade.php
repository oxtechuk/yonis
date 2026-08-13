@extends('layouts.admin')

@section('title', 'سجل المدفوعات والمالية')

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h5 class="fw-bold mb-3"><i class="bi bi-funnel me-1 text-teal" style="color: var(--accent-color);"></i> تصفية المعاملات المالية</h5>
        <form action="{{ route('admin.payments') }}" method="GET" class="row g-3">
            <div class="col-md-9">
                <select name="status" class="form-select">
                    <option value="">جميع المعاملات</option>
                    <option value="Pending" @if(request('status') === 'Pending') selected @endif>معلق</option>
                    <option value="Paid" @if(request('status') === 'Paid') selected @endif>مدفوع ناجح</option>
                    <option value="Failed" @if(request('status') === 'Failed') selected @endif>فشل الدفع</option>
                    <option value="RefundPending" @if(request('status') === 'RefundPending') selected @endif>طلب استرداد معلق</option>
                    <option value="Refunded" @if(request('status') === 'Refunded') selected @endif>مسترد بالكامل</option>
                </select>
            </div>
            <div class="col-md-3 d-grid">
                <button type="submit" class="btn btn-premium">تصفية</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-0">
        <h5 class="fw-bold m-0">سجل المعاملات والمدفوعات الإلكترونية عبر Stripe</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">المعرف (Stripe Payment ID)</th>
                        <th>المريض</th>
                        <th>الخدمة المحجوزة</th>
                        <th>قيمة المعاملة</th>
                        <th>العملة</th>
                        <th>الحالة</th>
                        <th>المبلغ المسترد</th>
                        <th class="pe-4">تاريخ المعاملة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td class="ps-4 fw-bold text-dark small">{{ $payment->payment_intent_id }}</td>
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
                                    <span class="badge bg-warning text-dark">معلق</span>
                                @elseif($payment->status === 'Paid')
                                    <span class="badge bg-success">ناجح</span>
                                @elseif($payment->status === 'Failed')
                                    <span class="badge bg-danger">فاشل</span>
                                @elseif($payment->status === 'RefundPending')
                                    <span class="badge bg-info text-dark">طلب استرداد معلق</span>
                                @elseif($payment->status === 'Refunded')
                                    <span class="badge bg-secondary">تم الاسترداد</span>
                                @else
                                    <span class="badge bg-dark">{{ $payment->status }}</span>
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
                                <i class="bi bi-wallet2 fs-1 d-block mb-3"></i>
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
