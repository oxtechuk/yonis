@extends('layouts.admin')

@section('title', 'إدارة الخدمات والأسعار')

@section('content')
<div class="row g-4">
    <!-- Services List -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold m-0"><i class="bi bi-heart-pulse-fill me-1 text-teal" style="color: var(--accent-color);"></i> قائمة الخدمات الطبية الحالية</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">اسم الخدمة</th>
                                <th>مدة الجلسة</th>
                                <th>السعر الرسوم</th>
                                <th>الحالة</th>
                                <th class="pe-4 text-end">تعديل</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($services as $service)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ $service->title }}</div>
                                        <div class="text-secondary small">{{ Str::limit($service->description, 80) }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><i class="bi bi-clock me-1"></i> {{ $service->duration }} دقيقة</span>
                                    </td>
                                    <td class="fw-bold text-success">${{ number_format($service->price, 2) }}</td>
                                    <td>
                                        <span class="badge @if($service->is_active) bg-success @else bg-danger @endif">
                                            {{ $service->is_active ? 'نشط' : 'معطل مؤقتاً' }}
                                        </span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editModal{{ $service->id }}">تعديل البيانات</button>
                                    </td>
                                </tr>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editModal{{ $service->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-light">
                                                <h5 class="modal-title fw-bold">تعديل الخدمة: {{ $service->title }}</h5>
                                                <button type="button" class="btn-close ms-0" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('admin.services.update', $service->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">اسم الخدمة</label>
                                                        <input type="text" name="title" class="form-control" value="{{ $service->title }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">شرح ووصف الخدمة</label>
                                                        <textarea name="description" class="form-control" rows="3">{{ $service->description }}</textarea>
                                                    </div>
                                                    <div class="row g-3 mb-3">
                                                        <div class="col-6">
                                                            <label class="form-label small fw-bold">السعر ($)</label>
                                                            <input type="number" step="0.01" name="price" class="form-control" value="{{ $service->price }}" required>
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="form-label small fw-bold">المدة بالدقائق</label>
                                                            <input type="number" name="duration" class="form-control" value="{{ $service->duration }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-check form-switch text-start">
                                                        <input class="form-check-input float-end ms-2" type="checkbox" role="switch" name="is_active" id="editActive{{ $service->id }}" @if($service->is_active) checked @endif>
                                                        <label class="form-check-label" for="editActive{{ $service->id }}">تفعيل الاستشارة للحجز السريع</label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm rounded-pill" data-bs-dismiss="modal">إلغاء</button>
                                                    <button type="submit" class="btn btn-premium btn-sm">حفظ التغييرات</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Service Form -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold m-0"><i class="bi bi-plus-circle me-1 text-teal" style="color: var(--accent-color);"></i> إضافة استشارة جديدة</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.services.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold">اسم الخدمة</label>
                        <input type="text" name="title" class="form-control" placeholder="مثال: استشارة عيادة أولية" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">الوصف والتفاصيل</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="اكتب هنا ما يشتمل عليه الكشف..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">السعر بالدولار ($)</label>
                        <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold">مدة الجلسة (بالدقائق)</label>
                        <input type="number" name="duration" class="form-control" placeholder="مثال: 30" required>
                    </div>
                    <div class="form-check form-switch mb-4 text-start">
                        <input class="form-check-input float-end ms-2" type="checkbox" role="switch" name="is_active" id="activeSwitch" checked>
                        <label class="form-check-label" for="activeSwitch">تفعيل الخدمة فورياً</label>
                    </div>
                    <button type="submit" class="btn btn-premium w-100 py-2.5">إضافة الخدمة</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
