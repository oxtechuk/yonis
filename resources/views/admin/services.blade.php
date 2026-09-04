@extends('layouts.admin')

@section('title', 'إدارة وتصنيف الخدمات والأسعار')

@section('styles')
<style>
    /* Modern Category Segment Capsule */
    .category-segment-capsule {
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 30px;
        padding: 4px;
        display: inline-flex;
        gap: 4px;
    }
    .category-segment-capsule .service-filter-btn {
        color: #64748b;
        border: none;
        background: transparent;
        border-radius: 25px;
        padding: 6px 18px;
        font-size: 0.85rem;
        font-weight: 700;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .category-segment-capsule .service-filter-btn:hover {
        color: var(--primary-color);
        background: rgba(64, 85, 165, 0.08);
    }
    .category-segment-capsule .service-filter-btn.active {
        color: #ffffff !important;
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)) !important;
        box-shadow: 0 4px 12px rgba(64, 85, 165, 0.25);
    }

    /* Distinct Type Badges */
    .badge-service-type {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 700;
        white-space: nowrap;
    }
    .badge-service-type.clinic {
        background: #fff1f2;
        color: #be123c;
        border: 1px solid #fecdd3;
    }
    .badge-service-type.online {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    /* Distinct Price Chips */
    .price-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 11px;
        border-radius: 8px;
        font-size: 0.82rem;
        font-weight: 700;
        white-space: nowrap;
        border: 1px solid transparent;
        transition: transform 0.15s ease;
    }
    .price-chip:hover {
        transform: translateY(-1px);
    }
    .price-chip.clinic {
        background: #fff1f2;
        border-color: #fecdd3;
        color: #be123c;
    }
    .price-chip.chat {
        background: #fffbeb;
        border-color: #fde68a;
        color: #b45309;
    }
    .price-chip.voice {
        background: #ecfdf5;
        border-color: #a7f3d0;
        color: #047857;
    }
    .price-chip.video {
        background: #f5f3ff;
        border-color: #ddd6fe;
        color: #6d28d9;
    }

    /* Status Pill */
    .badge-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        white-space: nowrap;
    }
    .badge-status-pill.active {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }
    .badge-status-pill.inactive {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }
    .badge-status-pill .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        display: inline-block;
    }
    .badge-status-pill.active .dot { background-color: #10b981; }
    .badge-status-pill.inactive .dot { background-color: #ef4444; }

    /* Action Buttons */
    .btn-action-icon {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 0.82rem;
        font-weight: 600;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #334155;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-action-icon.edit:hover {
        background: #f8fafc;
        color: var(--primary-color);
        border-color: var(--primary-color);
    }
    .btn-action-icon.delete {
        padding: 5px 8px;
        color: #dc2626;
    }
    .btn-action-icon.delete:hover {
        background: #fef2f2;
        border-color: #ef4444;
        color: #b91c1c;
    }
</style>
@endsection

@section('content')
<div class="row g-4">
    <!-- Services List Table -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
            <div class="card-header bg-white py-3 border-0">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h5 class="fw-bold m-0 text-primary d-flex align-items-center gap-2">
                            <i class="bi bi-grid-fill fs-5"></i> إدارة وتصنيف الخدمات الطبية
                        </h5>
                        <p class="text-secondary small m-0">تنظيم الخدمات وتحديد أسعارها (كشف العيادة بسعر محدد، أو استشارة أونلاين بأسعار الشات والصوت والفيديو).</p>
                    </div>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1.5 fw-bold">
                        إجمالي {{ count($services) }} خدمات
                    </span>
                </div>

                {{-- Segmented Filter Capsule --}}
                <div class="mt-3 pt-3 border-top">
                    <div class="category-segment-capsule">
                        <button type="button" class="service-filter-btn active" onclick="filterServicesTable('all', this)">
                            <i class="bi bi-grid-3x3-gap-fill"></i> جميع الخدمات ({{ count($services) }})
                        </button>
                        <button type="button" class="service-filter-btn" onclick="filterServicesTable('online', this)">
                            <i class="bi bi-laptop"></i> استشارات أونلاين ({{ $services->whereIn('type', ['online', 'both'])->count() }})
                        </button>
                        <button type="button" class="service-filter-btn" onclick="filterServicesTable('clinic', this)">
                            <i class="bi bi-hospital"></i> كشوفات العيادة ({{ $services->whereIn('type', ['clinic', 'both'])->count() }})
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">اسم الخدمة والتصنيف</th>
                                <th>المدة</th>
                                <th>الأسعار والتسعير ($)</th>
                                <th>الحالة</th>
                                <th class="pe-4 text-end">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($services as $service)
                                <tr class="service-row" data-type="{{ $service->type }}">
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="fw-bold text-dark fs-6">{{ $service->title }}</span>
                                            @if($service->type === 'clinic')
                                                <span class="badge-service-type clinic">
                                                    <i class="bi bi-hospital"></i> كشف في العيادة
                                                </span>
                                            @else
                                                <span class="badge-service-type online">
                                                    <i class="bi bi-laptop"></i> استشارة أونلاين
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-secondary small">{{ Str::limit($service->description, 75) }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1">
                                            <i class="bi bi-clock-history me-1 text-primary"></i> {{ $service->duration }} دقيقة
                                        </span>
                                    </td>
                                    <td>
                                        @if($service->type === 'clinic')
                                            {{-- كشف العيادة بسعر واحد فقط --}}
                                            <div class="d-flex align-items-center">
                                                <span class="price-chip clinic">
                                                    <i class="bi bi-hospital"></i> كشف العيادة: <strong>${{ number_format($service->clinic_price ?? $service->price, 2) }}</strong>
                                                </span>
                                            </div>
                                        @else
                                            {{-- استشارة أونلاين: 3 قنوات بأسعار منفصلة ومميزة --}}
                                            <div class="d-flex flex-wrap gap-1.5 align-items-center">
                                                <span class="price-chip chat" title="سعر استشارة الشات">
                                                    <i class="bi bi-chat-dots"></i> شات: <strong>${{ number_format($service->chat_price ?? $service->price, 2) }}</strong>
                                                </span>
                                                <span class="price-chip voice" title="سعر استشارة الصوت">
                                                    <i class="bi bi-telephone"></i> صوت: <strong>${{ number_format($service->voice_price ?? $service->price, 2) }}</strong>
                                                </span>
                                                <span class="price-chip video" title="سعر استشارة الفيديو">
                                                    <i class="bi bi-camera-video"></i> فيديو: <strong>${{ number_format($service->video_price ?? $service->price, 2) }}</strong>
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($service->is_active)
                                            <span class="badge-status-pill active"><span class="dot"></span> نشط</span>
                                        @else
                                            <span class="badge-status-pill inactive"><span class="dot"></span> معطل</span>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-flex align-items-center justify-content-end gap-1.5">
                                            <button type="button" class="btn-action-icon edit" data-bs-toggle="modal" data-bs-target="#editModal{{ $service->id }}" title="تعديل الخدمة">
                                                <i class="bi bi-pencil-square"></i>
                                                <span>تعديل</span>
                                            </button>

                                            <button type="button" class="btn-action-icon delete" data-bs-toggle="modal" data-bs-target="#deleteServiceModal{{ $service->id }}" title="حذف الخدمة">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editModal{{ $service->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                            <div class="modal-header bg-light border-bottom">
                                                <h5 class="modal-title fw-bold text-dark fs-6">
                                                    <i class="bi bi-pencil-square text-primary me-1"></i> تعديل الخدمة: {{ $service->title }}
                                                </h5>
                                                <button type="button" class="btn-close ms-0" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('admin.services.update', $service->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body p-4">
                                                    {{-- Service Category Selection (Only 2 types) --}}
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-primary"><i class="bi bi-tags-fill me-1"></i> تصنيف ونوع الخدمة</label>
                                                        <select name="type" class="form-select rounded-3 fw-bold" id="editServiceType{{ $service->id }}" onchange="toggleEditServiceFields({{ $service->id }})" required>
                                                            <option value="online" @if($service->type !== 'clinic') selected @endif>💻 استشارة أونلاين</option>
                                                            <option value="clinic" @if($service->type === 'clinic') selected @endif>🏥 كشف في العيادة</option>
                                                        </select>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">اسم الخدمة</label>
                                                        <input type="text" name="title" class="form-control rounded-3" value="{{ $service->title }}" required>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">مدة الجلسة (بالدقائق)</label>
                                                        <input type="number" name="duration" class="form-control rounded-3" value="{{ $service->duration }}" required min="5">
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">شرح وتفاصيل الخدمة</label>
                                                        <textarea name="description" class="form-control rounded-3" rows="2">{{ $service->description }}</textarea>
                                                    </div>

                                                    {{-- Clinic Price Box (Only 1 price) --}}
                                                    <div class="p-3 rounded-4 border mb-3" id="editClinicPriceBox{{ $service->id }}" style="background: #fff1f2; border-color: #fecdd3 !important; display: {{ $service->type === 'clinic' ? 'block' : 'none' }};">
                                                        <h6 class="fw-bold small mb-2 text-danger">
                                                            <i class="bi bi-hospital me-1"></i> سعر كشف العيادة ($)
                                                        </h6>
                                                        <p class="text-secondary small mb-2">كشف العيادة له سعر موحد للكشف والفحص المباشر في مقر العيادة.</p>
                                                        <input type="number" step="0.01" name="clinic_price" class="form-control rounded-3 bg-white" value="{{ $service->clinic_price ?? $service->price }}" placeholder="50.00">
                                                    </div>

                                                    {{-- Online Channels Pricing Box (3 distinct channel prices) --}}
                                                    <div class="p-3 rounded-4 border mb-3 bg-light" id="editOnlineChannelsBox{{ $service->id }}" style="display: {{ $service->type !== 'clinic' ? 'block' : 'none' }};">
                                                        <h6 class="fw-bold text-primary small mb-2">
                                                            <i class="bi bi-cash-coin me-1"></i> أسعار قنوات الاستشارة الأونلاين ($)
                                                        </h6>
                                                        <p class="text-secondary small mb-3">حدد سعر كل قناة تواصل بحسب وسيلة الاستشارة المختارة.</p>
                                                        <div class="row g-3">
                                                            <div class="col-md-4">
                                                                <label class="form-label small fw-bold" style="color: #b45309;"><i class="bi bi-chat-dots me-1"></i> استشارة شات ($)</label>
                                                                <input type="number" step="0.01" name="chat_price" class="form-control rounded-3 bg-white" value="{{ $service->chat_price ?? $service->price }}" placeholder="30.00">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label small fw-bold" style="color: #047857;"><i class="bi bi-telephone me-1"></i> استشارة صوت ($)</label>
                                                                <input type="number" step="0.01" name="voice_price" class="form-control rounded-3 bg-white" value="{{ $service->voice_price ?? $service->price }}" placeholder="40.00">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label small fw-bold" style="color: #6d28d9;"><i class="bi bi-camera-video me-1"></i> استشارة فيديو ($)</label>
                                                                <input type="number" step="0.01" name="video_price" class="form-control rounded-3 bg-white" value="{{ $service->video_price ?? $service->price }}" placeholder="50.00">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-check form-switch text-start">
                                                        <input class="form-check-input float-end ms-2" type="checkbox" role="switch" name="is_active" id="editActive{{ $service->id }}" @if($service->is_active) checked @endif>
                                                        <label class="form-check-label fw-bold small text-dark" for="editActive{{ $service->id }}">تفعيل الخدمة وإظهارها للمرضى في صفحة الحجز</label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light border-top">
                                                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                                                    <button type="submit" class="btn btn-royal-primary btn-sm rounded-pill px-4 fw-bold">حفظ التعديلات</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delete Confirmation Modal -->
                                <div class="modal fade" id="deleteServiceModal{{ $service->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title fw-bold fs-6"><i class="bi bi-exclamation-triangle-fill me-1"></i> تأكيد حذف الخدمة</h5>
                                                <button type="button" class="btn-close btn-close-white ms-0" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4 text-center">
                                                <div class="rounded-circle bg-danger bg-opacity-10 text-danger mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 1.75rem;">
                                                    <i class="bi bi-trash3-fill"></i>
                                                </div>
                                                <h5 class="fw-bold text-dark mb-2">هل أنت متأكد من حذف هذه الخدمة؟</h5>
                                                <p class="text-secondary small mb-3">
                                                    سيتم حذف الخدمة <strong>«{{ $service->title }}»</strong> ولن تظهر بعد الآن في خيارات الحجز.
                                                </p>
                                            </div>
                                            <div class="modal-footer bg-light border-top justify-content-center gap-2">
                                                <button type="button" class="btn btn-secondary rounded-pill px-4 btn-sm" data-bs-dismiss="modal">تراجع</button>
                                                <form action="{{ route('admin.services.delete', $service->id) }}" method="POST" class="d-inline m-0">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger rounded-pill px-4 btn-sm fw-bold">
                                                        <i class="bi bi-trash3 me-1"></i> نعم، حذف الخدمة
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-secondary">لا توجد خدمات مضافة حالياً.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Service Form -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold m-0 text-primary d-flex align-items-center gap-2">
                    <i class="bi bi-plus-circle-fill fs-5"></i> إضافة خدمة وتحديد تصنيفها
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.services.store') }}" method="POST">
                    @csrf
                    
                    {{-- Category Selection (2 Types Only) --}}
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-primary"><i class="bi bi-tags-fill me-1"></i> تصنيف ونوع الخدمة</label>
                        <select name="type" class="form-select rounded-3 fw-bold" id="addServiceType" onchange="toggleAddServiceFields()" required>
                            <option value="online" selected>💻 استشارة أونلاين</option>
                            <option value="clinic">🏥 كشف في العيادة</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">اسم الخدمة</label>
                        <input type="text" name="title" class="form-control rounded-3" placeholder="مثال: استشارة نفسية - 45 دقيقة" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">المدة (بالدقائق)</label>
                        <input type="number" name="duration" class="form-control rounded-3" placeholder="45" required min="5" value="{{ \App\Models\Setting::get('default_consultation_duration', '45') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">الوصف والتفاصيل</label>
                        <textarea name="description" class="form-control rounded-3" rows="2" placeholder="اكتب هنا تفاصيل الجلسة ومميزاتها..."></textarea>
                    </div>

                    {{-- Clinic Pricing (Single Price) --}}
                    <div class="p-3 rounded-4 border mb-3" id="addClinicPriceBox" style="background: #fff1f2; border-color: #fecdd3 !important; display: none;">
                        <h6 class="fw-bold small mb-2 text-danger">
                            <i class="bi bi-hospital me-1"></i> سعر كشف العيادة ($)
                        </h6>
                        <p class="text-secondary small mb-2">سعر موحد للكشف والفحص المباشر في مقر العيادة.</p>
                        <input type="number" step="0.01" name="clinic_price" class="form-control rounded-3 bg-white" placeholder="مثال: 50.00">
                    </div>

                    {{-- Online Channels Pricing (3 Distinct Channel Prices) --}}
                    <div class="p-3 rounded-4 border mb-3 bg-light" id="addOnlineChannelsBox" style="display: block;">
                        <h6 class="fw-bold text-primary small mb-2">
                            <i class="bi bi-cash-coin me-1"></i> أسعار قنوات الاستشارة الأونلاين ($)
                        </h6>
                        <p class="text-secondary small mb-3">حدد سعر كل قناة تواصل بحسب وسيلة الاستشارة:</p>

                        <div class="mb-2.5">
                            <label class="form-label small fw-bold mb-1" style="color: #b45309;">
                                <i class="bi bi-chat-dots me-1"></i> سعر استشارة الشات ($)
                            </label>
                            <input type="number" step="0.01" name="chat_price" class="form-control form-control-sm rounded-3 bg-white" placeholder="مثال: 30.00">
                        </div>

                        <div class="mb-2.5">
                            <label class="form-label small fw-bold mb-1" style="color: #047857;">
                                <i class="bi bi-telephone me-1"></i> سعر استشارة الصوت ($)
                            </label>
                            <input type="number" step="0.01" name="voice_price" class="form-control form-control-sm rounded-3 bg-white" placeholder="مثال: 40.00">
                        </div>

                        <div class="mb-2">
                            <label class="form-label small fw-bold mb-1" style="color: #6d28d9;">
                                <i class="bi bi-camera-video me-1"></i> سعر استشارة الفيديو ($)
                            </label>
                            <input type="number" step="0.01" name="video_price" class="form-control form-control-sm rounded-3 bg-white" placeholder="مثال: 50.00">
                        </div>
                    </div>

                    <div class="form-check form-switch mb-4 text-start">
                        <input class="form-check-input float-end ms-2" type="checkbox" role="switch" name="is_active" id="activeSwitch" checked>
                        <label class="form-check-label fw-bold small text-dark" for="activeSwitch">تفعيل الخدمة فورياً للحجز</label>
                    </div>

                    <button type="submit" class="btn btn-royal-primary w-100 py-3 rounded-pill fw-bold shadow-sm">
                        <i class="bi bi-plus-circle me-1"></i> إضافة الخدمة وحفظ الأسعار
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Filter Services Table by Category
function filterServicesTable(category, btn) {
    document.querySelectorAll('.service-filter-btn').forEach(b => {
        b.classList.remove('active');
    });

    btn.classList.add('active');

    const rows = document.querySelectorAll('.service-row');
    rows.forEach(row => {
        const type = row.getAttribute('data-type');
        if (category === 'all') {
            row.style.display = '';
        } else if (category === 'online') {
            row.style.display = (type === 'online' || type === 'both') ? '' : 'none';
        } else if (category === 'clinic') {
            row.style.display = (type === 'clinic' || type === 'both') ? '' : 'none';
        }
    });
}

// Toggle Add Form Fields between Clinic vs Online
function toggleAddServiceFields() {
    const type = document.getElementById('addServiceType').value;
    const clinicBox = document.getElementById('addClinicPriceBox');
    const onlineBox = document.getElementById('addOnlineChannelsBox');

    if (type === 'clinic') {
        clinicBox.style.display = 'block';
        onlineBox.style.display = 'none';
    } else {
        clinicBox.style.display = 'none';
        onlineBox.style.display = 'block';
    }
}

// Toggle Edit Modal Fields between Clinic vs Online
function toggleEditServiceFields(id) {
    const type = document.getElementById('editServiceType' + id).value;
    const clinicBox = document.getElementById('editClinicPriceBox' + id);
    const onlineBox = document.getElementById('editOnlineChannelsBox' + id);

    if (type === 'clinic') {
        clinicBox.style.display = 'block';
        onlineBox.style.display = 'none';
    } else {
        clinicBox.style.display = 'none';
        onlineBox.style.display = 'block';
    }
}
</script>
@endsection
