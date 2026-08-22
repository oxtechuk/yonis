@extends('layouts.admin')

@section('title', 'سجلات المرضى والمراجعين')

@section('content')
<div class="card border-0 shadow-sm mb-4 rounded-4 d-print-none">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-3 text-primary d-flex align-items-center gap-2">
            <i class="bi bi-search fs-5"></i> البحث في سجلات المرضى والمراجعين
        </h5>
        <form action="{{ route('admin.patients') }}" method="GET" class="row g-3">
            <div class="col-md-9">
                <input type="text" name="search" class="form-control" placeholder="ابحث باسم المريض، الجوال، البريد الإلكتروني..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3 d-grid">
                <button type="submit" class="btn btn-primary rounded-pill fw-bold">بحث وتصفية</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold m-0"><i class="bi bi-people-fill text-primary me-2"></i> سجل المرضى والمراجعين المسجلين</h5>
        <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addPatientModal">
            <i class="bi bi-person-plus-fill me-1"></i> إضافة مريض يدوي
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">الاسم الكامل</th>
                        <th>رقم الجوال</th>
                        <th>البريد الإلكتروني</th>
                        <th>تاريخ التسجيل</th>
                        <th>عدد الحجوزات</th>
                        <th class="pe-4 text-end">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $patient)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $patient->name }}</div>
                            </td>
                            <td>{{ $patient->phone }}</td>
                            <td class="font-monospace text-secondary">{{ $patient->email }}</td>
                            <td>{{ $patient->created_at->format('Y-m-d') }}</td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1.5 rounded-pill">{{ $patient->bookings_count }} حجوزات</span>
                            </td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('admin.patients.details', $patient->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="bi bi-folder2-open me-1"></i> عرض السجل الطبي
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-secondary">
                                <i class="bi bi-people fs-1 d-block mb-3 text-secondary"></i>
                                لم يتم العثور على أي مرضى مسجلين.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center py-4">
            {{ $patients->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- Add Patient Modal -->
<div class="modal fade" id="addPatientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-person-plus-fill text-primary me-2"></i> إضافة حساب مريض جديد</h5>
                <button type="button" class="btn-close ms-0" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.patients.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">الاسم الكامل للمريض</label>
                        <input type="text" name="name" class="form-control" placeholder="مثال: محمد أحمد العلي" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">رقم الجوال</label>
                        <input type="tel" name="phone" class="form-control" placeholder="01234567890" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">البريد الإلكتروني</label>
                        <input type="email" name="email" class="form-control" placeholder="patient@example.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">كلمة المرور (الافتراضية للمتابعة)</label>
                        <input type="text" name="password" class="form-control" value="patient123" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold">حفظ وتأكيد المريض</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
