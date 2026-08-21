@extends('layouts.admin')

@section('title', 'إدارة الخدمات وأسعار قنوات الاستشارة')

@section('content')
<div class="row g-4">
    <!-- Services List -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold m-0"><i class="bi bi-heart-pulse-fill me-1 text-teal" style="color: var(--accent-color);"></i> قائمة الخدمات والأسعار الحالية</h5>
                <span class="badge bg-light text-dark border">إجمالي {{ count($services) }} خدمات</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">اسم الخدمة والوصف</th>
                                <th>المدة</th>
                                <th>أسعار القنوات ($)</th>
                                <th>الحالة</th>
                                <th class="pe-4 text-end">تعديل</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($services as $service)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ $service->title }}</div>
                                        <div class="text-secondary small">{{ Str::limit($service->description, 70) }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><i class="bi bi-clock me-1"></i> {{ $service->duration }} دقيقة</span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1 small">
                                            <div><span class="text-danger fw-bold"><i class="bi bi-hospital me-1"></i>العيادة:</span> ${{ number_format($service->clinic_price ?? $service->price, 2) }}</div>
                                            <div><span class="text-warning fw-bold"><i class="bi bi-chat-dots me-1"></i>شات:</span> ${{ number_format($service->chat_price ?? $service->price, 2) }}</div>
                                            <div><span class="text-success fw-bold"><i class="bi bi-telephone me-1"></i>صوت:</span> ${{ number_format($service->voice_price ?? $service->price, 2) }}</div>
                                            <div><span class="text-info fw-bold"><i class="bi bi-camera-video me-1"></i>فيديو:</span> ${{ number_format($service->video_price ?? $service->price, 2) }}</div>
                                        </div>
                                    </td>
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
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-light">
                                                <h5 class="modal-title fw-bold">تعديل الخدمة والأسعار: {{ $service->title }}</h5>
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
                                                        <textarea name="description" class="form-control" rows="2">{{ $service->description }}</textarea>
                                                    </div>
                                                    <div class="row g-3 mb-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold">السعر الأساسي القياسي ($)</label>
                                                            <input type="number" step="0.01" name="price" class="form-control" value="{{ $service->price }}" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold">مدة الجلسة (بالدقائق)</label>
                                                            <input type="number" name="duration" class="form-control" value="{{ $service->duration }}" required>
                                                        </div>
                                                    </div>

                                                    <h6 class="fw-bold text-teal border-bottom pb-2 my-3"><i class="bi bi-tags me-1"></i> تحديد السعر الخاص لكل نوع حجز وقناة تواصل ($)</h6>
                                                    <div class="row g-3 mb-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold text-danger"><i class="bi bi-hospital me-1"></i> سعر حجز العيادة ($)</label>
                                                            <input type="number" step="0.01" name="clinic_price" class="form-control" value="{{ $service->clinic_price ?? $service->price }}" placeholder="0.00">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold text-warning"><i class="bi bi-chat-dots me-1"></i> سعر استشارة الشات ($)</label>
                                                            <input type="number" step="0.01" name="chat_price" class="form-control" value="{{ $service->chat_price ?? $service->price }}" placeholder="0.00">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold text-success"><i class="bi bi-telephone me-1"></i> سعر استشارة الصوت ($)</label>
                                                            <input type="number" step="0.01" name="voice_price" class="form-control" value="{{ $service->voice_price ?? $service->price }}" placeholder="0.00">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold text-info"><i class="bi bi-camera-video me-1"></i> سعر استشارة الفيديو ($)</label>
                                                            <input type="number" step="0.01" name="video_price" class="form-control" value="{{ $service->video_price ?? $service->price }}" placeholder="0.00">
                                                        </div>
                                                    </div>

                                                    <div class="form-check form-switch text-start">
                                                        <input class="form-check-input float-end ms-2" type="checkbox" role="switch" name="is_active" id="editActive{{ $service->id }}" @if($service->is_active) checked @endif>
                                                        <label class="form-check-label" for="editActive{{ $service->id }}">تفعيل الاستشارة للحجز والموبايل</label>
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
                <h5 class="fw-bold m-0"><i class="bi bi-plus-circle me-1 text-teal" style="color: var(--accent-color);"></i> إضافة خدمة طبية جديدة</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.services.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold">اسم الخدمة</label>
                        <input type="text" name="title" class="form-control" placeholder="مثال: استشارة قلب وأوعية دموية" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">الوصف والتفاصيل</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="اكتب هنا تفاصيل الخدمة والتشخيص..."></textarea>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">السعر الأساسي ($)</label>
                            <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">المدة (دقائق)</label>
                            <input type="number" name="duration" class="form-control" placeholder="30" required>
                        </div>
                    </div>

                    <div class="border rounded p-3 bg-light mb-3">
                        <h6 class="fw-bold text-dark small mb-2"><i class="bi bi-cash-stack text-teal me-1"></i> أسعار قنوات التواصل ($)</h6>
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-danger mb-1">سعر كشف العيادة ($)</label>
                            <input type="number" step="0.01" name="clinic_price" class="form-control form-control-sm" placeholder="اختياري (أو نفس الأساسي)">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-warning mb-1">سعر استشارة الشات ($)</label>
                            <input type="number" step="0.01" name="chat_price" class="form-control form-control-sm" placeholder="اختياري">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-success mb-1">سعر استشارة الصوت ($)</label>
                            <input type="number" step="0.01" name="voice_price" class="form-control form-control-sm" placeholder="اختياري">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-info mb-1">سعر استشارة الفيديو ($)</label>
                            <input type="number" step="0.01" name="video_price" class="form-control form-control-sm" placeholder="اختياري">
                        </div>
                    </div>

                    <div class="form-check form-switch mb-4 text-start">
                        <input class="form-check-input float-end ms-2" type="checkbox" role="switch" name="is_active" id="activeSwitch" checked>
                        <label class="form-check-label" for="activeSwitch">تفعيل الخدمة فورياً</label>
                    </div>
                    <button type="submit" class="btn btn-premium w-100 py-2.5">إضافة الخدمة والأسعار</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
