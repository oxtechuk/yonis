@extends('layouts.admin')

@section('title', 'إدارة تصنيفات الخدمات والأسعار وقنوات الاستشارة')

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
        padding: 6px 16px;
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

    /* Gumroad Link Pill */
    .btn-gumroad-link {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        border-radius: 20px;
        background: rgba(64, 85, 165, 0.08);
        color: var(--primary-color);
        font-size: 0.8rem;
        font-weight: 700;
        border: 1px solid rgba(64, 85, 165, 0.2);
        white-space: nowrap;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .btn-gumroad-link:hover {
        background: var(--primary-color);
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(64, 85, 165, 0.2);
    }

    /* Status Dot Pill */
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
    <!-- Services List -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
            <div class="card-header bg-white py-3 border-0">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h5 class="fw-bold m-0 text-primary d-flex align-items-center gap-2">
                            <i class="bi bi-grid-fill fs-5"></i> إدارة وتصنيف الخدمات الطبية
                        </h5>
                        <p class="text-secondary small m-0">تنظيم الخدمات بحسب التصنيف (أونلاين، عيادة، كلاهما) وتخصيص الأسعار وقنوات الحجز.</p>
                    </div>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1.5 fw-bold">
                        إجمالي {{ count($services) }} خدمات
                    </span>
                </div>

                {{-- Modern Segmented Filter Capsule --}}
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
                                <th>أسعار القنوات ($)</th>
                                <th>رابط الدفع</th>
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
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0.5" style="font-size: 0.75rem;">
                                                    <i class="bi bi-hospital me-1"></i> عيادة فقط
                                                </span>
                                            @elseif($service->type === 'online')
                                                <span class="badge bg-info-subtle text-primary border border-info-subtle rounded-pill px-2 py-0.5" style="font-size: 0.75rem;">
                                                    <i class="bi bi-laptop me-1"></i> أونلاين فقط
                                                </span>
                                            @else
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5" style="font-size: 0.75rem;">
                                                    <i class="bi bi-arrow-left-right me-1"></i> أونلاين وعيادة
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-secondary small">{{ Str::limit($service->description, 65) }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1">
                                            <i class="bi bi-clock-history me-1 text-primary"></i> {{ $service->duration }} دقيقة
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1 small">
                                            @if($service->type === 'clinic' || $service->type === 'both')
                                                <div><span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5"><i class="bi bi-hospital me-1"></i>العيادة:</span> <strong class="text-dark">${{ number_format($service->clinic_price ?? $service->price, 2) }}</strong></div>
                                            @endif
                                            @if($service->type === 'online' || $service->type === 'both')
                                                <div><span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-0.5"><i class="bi bi-chat-dots me-1"></i>شات:</span> <strong class="text-dark">${{ number_format($service->chat_price ?? $service->price, 2) }}</strong></div>
                                                <div><span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5"><i class="bi bi-telephone me-1"></i>صوت:</span> <strong class="text-dark">${{ number_format($service->voice_price ?? $service->price, 2) }}</strong></div>
                                                <div><span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2 py-0.5"><i class="bi bi-camera-video me-1"></i>فيديو:</span> <strong class="text-dark">${{ number_format($service->video_price ?? $service->price, 2) }}</strong></div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if(!empty($service->payment_url))
                                            <a href="{{ $service->payment_url }}" target="_blank" class="btn-gumroad-link" title="{{ $service->payment_url }}">
                                                <i class="bi bi-box-arrow-up-right"></i> رابط Gumroad
                                            </a>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1">رابط افتراضي</span>
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
                                                <h5 class="modal-title fw-bold text-dark fs-6">تعديل الخدمة والتصنيف: {{ $service->title }}</h5>
                                                <button type="button" class="btn-close ms-0" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('admin.services.update', $service->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body p-4">
                                                    {{-- Service Category Selection --}}
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-primary"><i class="bi bi-tags-fill me-1"></i> تصنيف ونوع الحجز للخدمة</label>
                                                        <select name="type" class="form-select rounded-3 fw-bold" id="editServiceType{{ $service->id }}" onchange="toggleEditServiceFields({{ $service->id }})" required>
                                                            <option value="both" @if($service->type === 'both') selected @endif>🔄 كلاهما متاح (أونلاين وعيادة)</option>
                                                            <option value="online" @if($service->type === 'online') selected @endif>💻 استشارة أونلاين فقط</option>
                                                            <option value="clinic" @if($service->type === 'clinic') selected @endif>🏥 كشف في مقر العيادة فقط (بغداد)</option>
                                                        </select>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">اسم الخدمة</label>
                                                        <input type="text" name="title" class="form-control rounded-3" value="{{ $service->title }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">شرح ووصف الخدمة</label>
                                                        <textarea name="description" class="form-control rounded-3" rows="2">{{ $service->description }}</textarea>
                                                    </div>
                                                    <div class="row g-3 mb-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold">السعر الأساسي القياسي ($)</label>
                                                            <input type="number" step="0.01" name="price" class="form-control rounded-3" value="{{ $service->price }}" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold">مدة الجلسة (بالدقائق)</label>
                                                            <input type="number" name="duration" class="form-control rounded-3" value="{{ $service->duration }}" required>
                                                        </div>
                                                    </div>

                                                    <div class="mb-3" id="editPaymentUrlBox{{ $service->id }}">
                                                        <label class="form-label small fw-bold text-primary"><i class="bi bi-link-45deg me-1"></i> رابط الدفع الخارجي الخاص بالخدمة (Gumroad / رابط مباشر)</label>
                                                        <input type="url" name="payment_url" class="form-control rounded-3" value="{{ $service->payment_url }}" placeholder="https://younisalmurshed.gumroad.com/l/...">
                                                        <div class="form-text small">رابط صفحة الدفع الخاصة بهذه الخدمة لنقل العميل إليها مباشرة.</div>
                                                    </div>

                                                    <div class="p-3 bg-light rounded-4 border mb-3">
                                                        <h6 class="fw-bold text-primary small mb-3"><i class="bi bi-cash-coin me-1"></i> تسعير قنوات التواصل ($)</h6>
                                                        <div class="row g-3">
                                                            <div class="col-md-6" id="editClinicPriceBox{{ $service->id }}">
                                                                <label class="form-label small fw-bold text-danger"><i class="bi bi-hospital me-1"></i> سعر كشف العيادة ($)</label>
                                                                <input type="number" step="0.01" name="clinic_price" class="form-control rounded-3" value="{{ $service->clinic_price ?? $service->price }}" placeholder="0.00">
                                                            </div>
                                                            <div class="col-md-6 edit-online-channel-{{ $service->id }}">
                                                                <label class="form-label small fw-bold text-warning-emphasis"><i class="bi bi-chat-dots me-1"></i> سعر استشارة الشات ($)</label>
                                                                <input type="number" step="0.01" name="chat_price" class="form-control rounded-3" value="{{ $service->chat_price ?? $service->price }}" placeholder="0.00">
                                                            </div>
                                                            <div class="col-md-6 edit-online-channel-{{ $service->id }}">
                                                                <label class="form-label small fw-bold text-success"><i class="bi bi-telephone me-1"></i> سعر استشارة الصوت ($)</label>
                                                                <input type="number" step="0.01" name="voice_price" class="form-control rounded-3" value="{{ $service->voice_price ?? $service->price }}" placeholder="0.00">
                                                            </div>
                                                            <div class="col-md-6 edit-online-channel-{{ $service->id }}">
                                                                <label class="form-label small fw-bold text-info-emphasis"><i class="bi bi-camera-video me-1"></i> سعر استشارة الفيديو ($)</label>
                                                                <input type="number" step="0.01" name="video_price" class="form-control rounded-3" value="{{ $service->video_price ?? $service->price }}" placeholder="0.00">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-check form-switch text-start">
                                                        <input class="form-check-input float-end ms-2" type="checkbox" role="switch" name="is_active" id="editActive{{ $service->id }}" @if($service->is_active) checked @endif>
                                                        <label class="form-check-label fw-bold small text-dark" for="editActive{{ $service->id }}">تفعيل الخدمة وإظهارها في قائمة الحجز</label>
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
                                    <td colspan="6" class="text-center py-4 text-secondary">لا توجد خدمات مضافة حالياً.</td>
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
                    
                    {{-- Category Selection --}}
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-primary"><i class="bi bi-tags-fill me-1"></i> تصنيف ونوع الحجز للخدمة</label>
                        <select name="type" class="form-select rounded-3 fw-bold" id="addServiceType" onchange="toggleAddServiceFields()" required>
                            <option value="both" selected>🔄 كلاهما متاح (أونلاين وعيادة)</option>
                            <option value="online">💻 استشارة أونلاين فقط</option>
                            <option value="clinic">🏥 كشف في مقر العيادة فقط (بغداد)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">اسم الخدمة</label>
                        <input type="text" name="title" class="form-control rounded-3" placeholder="مثال: استشارة زوجية وأسرية - 45 دقيقة" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">الوصف والتفاصيل</label>
                        <textarea name="description" class="form-control rounded-3" rows="2" placeholder="اكتب هنا تفاصيل الجلسة ومميزاتها..."></textarea>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">السعر الأساسي ($)</label>
                            <input type="number" step="0.01" name="price" class="form-control rounded-3" placeholder="50.00" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">المدة (دقائق)</label>
                            <input type="number" name="duration" class="form-control rounded-3" placeholder="30" required>
                        </div>
                    </div>

                    <div class="mb-3" id="addPaymentUrlBox">
                        <label class="form-label small fw-bold text-primary"><i class="bi bi-link-45deg me-1"></i> رابط الدفع الخارجي (Gumroad)</label>
                        <input type="url" name="payment_url" class="form-control rounded-3" placeholder="https://younisalmurshed.gumroad.com/l/srjlvw?wanted=true">
                        <div class="form-text small">رابط بوابة Gumroad أو رابط الدفع المباشر الخاص بهذه الخدمة.</div>
                    </div>

                    <div class="border rounded-4 p-3 bg-light mb-3">
                        <h6 class="fw-bold text-primary small mb-3"><i class="bi bi-cash-coin me-1"></i> تسعير قنوات التواصل المباشر ($)</h6>
                        
                        <div class="mb-2.5" id="addClinicPriceBox">
                            <label class="form-label small fw-bold text-danger mb-1"><i class="bi bi-hospital me-1"></i> سعر كشف العيادة ($)</label>
                            <input type="number" step="0.01" name="clinic_price" class="form-control form-control-sm rounded-3" placeholder="اختياري (أو نفس الأساسي)">
                        </div>

                        <div class="add-online-channels">
                            <div class="mb-2.5">
                                <label class="form-label small fw-bold text-warning-emphasis mb-1"><i class="bi bi-chat-dots me-1"></i> سعر استشارة الشات ($)</label>
                                <input type="number" step="0.01" name="chat_price" class="form-control form-control-sm rounded-3" placeholder="اختياري">
                            </div>
                            <div class="mb-2.5">
                                <label class="form-label small fw-bold text-success mb-1"><i class="bi bi-telephone me-1"></i> سعر استشارة الصوت ($)</label>
                                <input type="number" step="0.01" name="voice_price" class="form-control form-control-sm rounded-3" placeholder="اختياري">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-bold text-info-emphasis mb-1"><i class="bi bi-camera-video me-1"></i> سعر استشارة الفيديو ($)</label>
                                <input type="number" step="0.01" name="video_price" class="form-control form-control-sm rounded-3" placeholder="اختياري">
                            </div>
                        </div>
                    </div>

                    <div class="form-check form-switch mb-4 text-start">
                        <input class="form-check-input float-end ms-2" type="checkbox" role="switch" name="is_active" id="activeSwitch" checked>
                        <label class="form-check-label fw-bold small text-dark" for="activeSwitch">تفعيل الخدمة فورياً للحجز</label>
                    </div>
                    <button type="submit" class="btn btn-royal-primary w-100 py-3 rounded-pill fw-bold shadow-sm">
                        <i class="bi bi-plus-circle me-1"></i> إضافة الخدمة والأسعار
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

function toggleAddServiceFields() {
    const type = document.getElementById('addServiceType').value;
    const clinicBox = document.getElementById('addClinicPriceBox');
    const onlineChannels = document.querySelectorAll('.add-online-channels');
    
    if (type === 'clinic') {
        clinicBox.style.display = 'block';
        onlineChannels.forEach(el => el.style.display = 'none');
    } else if (type === 'online') {
        clinicBox.style.display = 'none';
        onlineChannels.forEach(el => el.style.display = 'block');
    } else {
        clinicBox.style.display = 'block';
        onlineChannels.forEach(el => el.style.display = 'block');
    }
}

function toggleEditServiceFields(id) {
    const type = document.getElementById('editServiceType' + id).value;
    const clinicBox = document.getElementById('editClinicPriceBox' + id);
    const onlineChannels = document.querySelectorAll('.edit-online-channel-' + id);

    if (type === 'clinic') {
        clinicBox.style.display = 'block';
        onlineChannels.forEach(el => el.style.display = 'none');
    } else if (type === 'online') {
        clinicBox.style.display = 'none';
        onlineChannels.forEach(el => el.style.display = 'block');
    } else {
        clinicBox.style.display = 'block';
        onlineChannels.forEach(el => el.style.display = 'block');
    }
}
</script>
@endsection
