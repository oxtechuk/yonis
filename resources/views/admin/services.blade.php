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

    /* Icon Picker Grid & Items */
    .icon-picker-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 6px;
    }
    .icon-picker-item {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 8px 4px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 2px;
    }
    .icon-picker-item:hover {
        border-color: var(--primary-color, #3B52A4);
        background: rgba(59, 82, 164, 0.08);
        transform: translateY(-2px);
    }
    .icon-picker-item.active {
        border-color: var(--primary-color, #3B52A4);
        background: var(--primary-color, #3B52A4);
        color: #fff !important;
        box-shadow: 0 4px 10px rgba(59, 82, 164, 0.3);
    }
    .icon-picker-item.active i,
    .icon-picker-item.active span {
        color: #fff !important;
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
                                <th>الأسعار والتسعير ({{ \App\Models\Setting::currencySymbol() }})</th>
                                <th>الحالة</th>
                                <th class="pe-4 text-end">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($services as $service)
                                @php
                                    $chType = $service->getChannelType();
                                @endphp
                                <tr class="service-row" data-type="{{ $service->type }}">
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary" style="width:40px; height:40px; min-width:40px;">
                                                @if($service->icon_url)
                                                    <img src="{{ $service->icon_url }}" alt="icon" style="width:26px; height:26px; object-fit:contain; border-radius:6px;">
                                                @else
                                                    <i class="bi {{ $service->icon_name }} fs-5"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark fs-6">{{ $service->title }}</div>
                                                <div class="text-secondary small">{{ Str::limit($service->description, 75) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1">
                                            <i class="bi bi-clock-history me-1 text-primary"></i> {{ $service->duration }} دقيقة
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1.5 align-items-center">
                                            @if($service->type === 'clinic' || (!is_null($service->clinic_price) && (float)$service->clinic_price > 0))
                                                <span class="price-chip clinic">
                                                    <i class="bi bi-hospital"></i> كشف العيادة: <strong>{{ number_format($service->clinic_price ?? $service->price, 2) }} {{ \App\Models\Setting::currencySymbol() }}</strong>
                                                </span>
                                            @endif

                                            @if($service->type !== 'clinic')
                                                @if($chType === 'video' || (!is_null($service->video_price) && (float)$service->video_price > 0))
                                                    <span class="price-chip video" title="استشارة فيديو">
                                                        <i class="bi bi-camera-video"></i> فيديو: <strong>{{ number_format($service->video_price ?? $service->price, 2) }} {{ \App\Models\Setting::currencySymbol() }}</strong>
                                                    </span>
                                                @endif
                                                @if($chType === 'voice' || (!is_null($service->voice_price) && (float)$service->voice_price > 0))
                                                    <span class="price-chip voice" title="استشارة صوت">
                                                        <i class="bi bi-telephone"></i> صوت: <strong>{{ number_format($service->voice_price ?? $service->price, 2) }} {{ \App\Models\Setting::currencySymbol() }}</strong>
                                                    </span>
                                                @endif
                                                @if($chType === 'chat' || (!is_null($service->chat_price) && (float)$service->chat_price > 0))
                                                    <span class="price-chip chat" title="استشارة شات">
                                                        <i class="bi bi-chat-dots"></i> شات: <strong>{{ number_format($service->chat_price ?? $service->price, 2) }} {{ \App\Models\Setting::currencySymbol() }}</strong>
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
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
                                            <form action="{{ route('admin.services.update', $service->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @php
                                                    $currCh = $service->type === 'clinic' ? 'clinic' : $service->getChannelType();
                                                    $currIcon = $service->icon ?? 'bi-heart-pulse';
                                                @endphp
                                                <div class="modal-body p-4">
                                                    {{-- Service Channel Selection --}}
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-primary"><i class="bi bi-tags-fill me-1"></i> قناة / وسيلة تقديم الخدمة</label>
                                                        <input type="hidden" name="type" id="editServiceType{{ $service->id }}" value="{{ $service->type === 'clinic' ? 'clinic' : 'online' }}">
                                                        <select name="channel" class="form-select rounded-3 fw-bold" id="editServiceChannel{{ $service->id }}" onchange="toggleEditServiceFields({{ $service->id }})" required>
                                                            <option value="video" @if($currCh === 'video') selected @endif>🎥 استشارة أونلاين - فيديو فقط (Video Call)</option>
                                                            <option value="voice" @if($currCh === 'voice') selected @endif>📞 استشارة أونلاين - مكالمة صوتية فقط (Voice Call)</option>
                                                            <option value="chat" @if($currCh === 'chat') selected @endif>💬 استشارة أونلاين - محادثة شات فقط (Chat Only)</option>
                                                            <option value="all" @if($currCh === 'all') selected @endif>🌐 استشارة أونلاين - متعددة القنوات (شات + صوت + فيديو)</option>
                                                            <option value="clinic" @if($currCh === 'clinic') selected @endif>🏥 كشف في مقر العيادة (In-Clinic)</option>
                                                        </select>
                                                    </div>

                                                    {{-- Icon Selection --}}
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-dark d-flex align-items-center justify-content-between">
                                                            <span><i class="bi bi-palette-fill me-1 text-primary"></i> أيقونة الخدمة (تظهر في التطبيق والموقع)</span>
                                                        </label>
                                                        <input type="hidden" name="icon" id="editServiceIcon{{ $service->id }}" value="{{ $currIcon }}">
                                                        <div class="icon-picker-grid mb-2" id="editIconPicker{{ $service->id }}">
                                                            @php
                                                                $availableIcons = [
                                                                    ['name' => 'bi-heart-pulse', 'label' => 'صحة نفسية'],
                                                                    ['name' => 'bi-camera-video', 'label' => 'فيديو'],
                                                                    ['name' => 'bi-telephone', 'label' => 'مكالمة'],
                                                                    ['name' => 'bi-chat-dots', 'label' => 'شات'],
                                                                    ['name' => 'bi-hospital', 'label' => 'عيادة'],
                                                                    ['name' => 'bi-person-heart', 'label' => 'فردي'],
                                                                    ['name' => 'bi-people', 'label' => 'زوجي/أسري'],
                                                                    ['name' => 'bi-emoji-smile', 'label' => 'دعم نفسي'],
                                                                    ['name' => 'bi-lightbulb', 'label' => 'تطوير'],
                                                                    ['name' => 'bi-shield-check', 'label' => 'سرية'],
                                                                    ['name' => 'bi-stars', 'label' => 'مميز'],
                                                                    ['name' => 'bi-flower1', 'label' => 'استرخاء'],
                                                                ];
                                                            @endphp
                                                            @foreach($availableIcons as $ico)
                                                                <div class="icon-picker-item {{ $currIcon === $ico['name'] ? 'active' : '' }}" onclick="selectServiceIcon('{{ $ico['name'] }}', 'editServiceIcon{{ $service->id }}', this)">
                                                                    <i class="bi {{ $ico['name'] }} fs-5"></i>
                                                                    <span style="font-size:0.7rem;">{{ $ico['label'] }}</span>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <div class="row g-2 align-items-center mt-1">
                                                            <div class="col-md-6">
                                                                <label class="form-label small text-muted mb-1">أو ارفع صورة/أيقونة مخصصة (PNG/SVG):</label>
                                                                <input type="file" name="icon_file" class="form-control form-control-sm rounded-3" accept="image/*">
                                                            </div>
                                                            @if($service->icon_url)
                                                                <div class="col-md-6 d-flex align-items-center gap-2 mt-3 mt-md-0">
                                                                    <img src="{{ $service->icon_url }}" alt="current icon" style="height:32px; width:32px; object-fit:contain; border-radius:6px; border:1px solid #ddd;">
                                                                    <span class="small text-success fw-bold">أيقونة مخصصة مرفوعة</span>
                                                                </div>
                                                            @endif
                                                        </div>
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

                                                    {{-- Clinic Price Box --}}
                                                    <div class="p-3 rounded-4 border mb-3" id="editClinicPriceBox{{ $service->id }}" style="background: #fff1f2; border-color: #fecdd3 !important; display: {{ $currCh === 'clinic' ? 'block' : 'none' }};">
                                                        <h6 class="fw-bold small mb-2 text-danger">
                                                            <i class="bi bi-hospital me-1"></i> سعر كشف العيادة ({{ \App\Models\Setting::currencySymbol() }})
                                                        </h6>
                                                        <p class="text-secondary small mb-2">كشف العيادة له سعر موحد للكشف والفحص المباشر في مقر العيادة.</p>
                                                        <input type="number" step="0.01" name="clinic_price" class="form-control rounded-3 bg-white" value="{{ $service->clinic_price ?? $service->price }}" placeholder="50.00">
                                                    </div>

                                                    {{-- Video Price Box --}}
                                                    <div class="p-3 rounded-4 border mb-3" id="editVideoPriceBox{{ $service->id }}" style="background: #f5f3ff; border-color: #ddd6fe !important; display: {{ in_array($currCh, ['video', 'all']) ? 'block' : 'none' }};">
                                                        <h6 class="fw-bold small mb-2" style="color:#6d28d9;">
                                                            <i class="bi bi-camera-video me-1"></i> سعر استشارة الفيديو ({{ \App\Models\Setting::currencySymbol() }})
                                                        </h6>
                                                        <input type="number" step="0.01" name="video_price" class="form-control rounded-3 bg-white" value="{{ $service->video_price ?? $service->price }}" placeholder="40.00">
                                                    </div>

                                                    {{-- Voice Price Box --}}
                                                    <div class="p-3 rounded-4 border mb-3" id="editVoicePriceBox{{ $service->id }}" style="background: #ecfdf5; border-color: #a7f3d0 !important; display: {{ in_array($currCh, ['voice', 'all']) ? 'block' : 'none' }};">
                                                        <h6 class="fw-bold small mb-2 text-success">
                                                            <i class="bi bi-telephone me-1"></i> سعر استشارة الصوت ({{ \App\Models\Setting::currencySymbol() }})
                                                        </h6>
                                                        <input type="number" step="0.01" name="voice_price" class="form-control rounded-3 bg-white" value="{{ $service->voice_price ?? $service->price }}" placeholder="30.00">
                                                    </div>

                                                    {{-- Chat Price Box --}}
                                                    <div class="p-3 rounded-4 border mb-3" id="editChatPriceBox{{ $service->id }}" style="background: #fffbeb; border-color: #fde68a !important; display: {{ in_array($currCh, ['chat', 'all']) ? 'block' : 'none' }};">
                                                        <h6 class="fw-bold small mb-2" style="color:#b45309;">
                                                            <i class="bi bi-chat-dots me-1"></i> سعر استشارة الشات ({{ \App\Models\Setting::currencySymbol() }})
                                                        </h6>
                                                        <input type="number" step="0.01" name="chat_price" class="form-control rounded-3 bg-white" value="{{ $service->chat_price ?? $service->price }}" placeholder="20.00">
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
                <form action="{{ route('admin.services.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    {{-- Channel / Type Selection --}}
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-primary"><i class="bi bi-tags-fill me-1"></i> قناة / وسيلة تقديم الخدمة</label>
                        <input type="hidden" name="type" id="addServiceType" value="online">
                        <select name="channel" class="form-select rounded-3 fw-bold" id="addServiceChannel" onchange="toggleAddServiceFields()" required>
                            <option value="video" selected>🎥 استشارة أونلاين - فيديو فقط (Video Call)</option>
                            <option value="voice">📞 استشارة أونلاين - مكالمة صوتية فقط (Voice Call)</option>
                            <option value="chat">💬 استشارة أونلاين - محادثة شات فقط (Chat Only)</option>
                            <option value="all">🌐 استشارة أونلاين - متعددة القنوات (شات + صوت + فيديو)</option>
                            <option value="clinic">🏥 كشف في مقر العيادة (In-Clinic)</option>
                        </select>
                    </div>

                    {{-- Icon Selection --}}
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark d-flex align-items-center justify-content-between">
                            <span><i class="bi bi-palette-fill me-1 text-primary"></i> أيقونة الخدمة (تظهر في التطبيق والموقع)</span>
                        </label>
                        <input type="hidden" name="icon" id="addServiceIcon" value="bi-heart-pulse">
                        <div class="icon-picker-grid mb-2" id="addIconPicker">
                            <div class="icon-picker-item active" onclick="selectServiceIcon('bi-heart-pulse', 'addServiceIcon', this)">
                                <i class="bi bi-heart-pulse fs-5"></i>
                                <span style="font-size:0.7rem;">صحة نفسية</span>
                            </div>
                            <div class="icon-picker-item" onclick="selectServiceIcon('bi-camera-video', 'addServiceIcon', this)">
                                <i class="bi bi-camera-video fs-5"></i>
                                <span style="font-size:0.7rem;">فيديو</span>
                            </div>
                            <div class="icon-picker-item" onclick="selectServiceIcon('bi-telephone', 'addServiceIcon', this)">
                                <i class="bi bi-telephone fs-5"></i>
                                <span style="font-size:0.7rem;">مكالمة</span>
                            </div>
                            <div class="icon-picker-item" onclick="selectServiceIcon('bi-chat-dots', 'addServiceIcon', this)">
                                <i class="bi bi-chat-dots fs-5"></i>
                                <span style="font-size:0.7rem;">شات</span>
                            </div>
                            <div class="icon-picker-item" onclick="selectServiceIcon('bi-hospital', 'addServiceIcon', this)">
                                <i class="bi bi-hospital fs-5"></i>
                                <span style="font-size:0.7rem;">عيادة</span>
                            </div>
                            <div class="icon-picker-item" onclick="selectServiceIcon('bi-person-heart', 'addServiceIcon', this)">
                                <i class="bi bi-person-heart fs-5"></i>
                                <span style="font-size:0.7rem;">فردي</span>
                            </div>
                            <div class="icon-picker-item" onclick="selectServiceIcon('bi-people', 'addServiceIcon', this)">
                                <i class="bi bi-people fs-5"></i>
                                <span style="font-size:0.7rem;">زوجي/أسري</span>
                            </div>
                            <div class="icon-picker-item" onclick="selectServiceIcon('bi-emoji-smile', 'addServiceIcon', this)">
                                <i class="bi bi-emoji-smile fs-5"></i>
                                <span style="font-size:0.7rem;">دعم نفسي</span>
                            </div>
                            <div class="icon-picker-item" onclick="selectServiceIcon('bi-lightbulb', 'addServiceIcon', this)">
                                <i class="bi bi-lightbulb fs-5"></i>
                                <span style="font-size:0.7rem;">تطوير</span>
                            </div>
                            <div class="icon-picker-item" onclick="selectServiceIcon('bi-shield-check', 'addServiceIcon', this)">
                                <i class="bi bi-shield-check fs-5"></i>
                                <span style="font-size:0.7rem;">سرية</span>
                            </div>
                            <div class="icon-picker-item" onclick="selectServiceIcon('bi-stars', 'addServiceIcon', this)">
                                <i class="bi bi-stars fs-5"></i>
                                <span style="font-size:0.7rem;">مميز</span>
                            </div>
                            <div class="icon-picker-item" onclick="selectServiceIcon('bi-flower1', 'addServiceIcon', this)">
                                <i class="bi bi-flower1 fs-5"></i>
                                <span style="font-size:0.7rem;">استرخاء</span>
                            </div>
                        </div>
                        <div class="mt-2">
                            <label class="form-label small text-muted mb-1">أو رفع أيقونة/صورة مخصصة (PNG/SVG):</label>
                            <input type="file" name="icon_file" class="form-control form-control-sm rounded-3" accept="image/*">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">اسم الخدمة</label>
                        <input type="text" name="title" class="form-control rounded-3" placeholder="مثال: استشارة مرئية أونلاين - فيديو" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">المدة (بالدقائق)</label>
                        <input type="number" name="duration" class="form-control rounded-3" placeholder="45" required min="5" value="{{ \App\Models\Setting::get('default_consultation_duration', '45') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">الوصف والتفاصيل</label>
                        <textarea name="description" class="form-control rounded-3" rows="2" placeholder="اكتب هنا تفاصيل الجلسة ومميزاتها..."></textarea>
                    </div>

                    {{-- Clinic Pricing Box --}}
                    <div class="p-3 rounded-4 border mb-3" id="addClinicPriceBox" style="background: #fff1f2; border-color: #fecdd3 !important; display: none;">
                        <h6 class="fw-bold small mb-2 text-danger">
                            <i class="bi bi-hospital me-1"></i> سعر كشف العيادة ({{ \App\Models\Setting::currencySymbol() }})
                        </h6>
                        <p class="text-secondary small mb-2">سعر موحد للكشف والفحص المباشر في مقر العيادة.</p>
                        <input type="number" step="0.01" name="clinic_price" class="form-control rounded-3 bg-white" placeholder="مثال: 50.00">
                    </div>

                    {{-- Video Price Box --}}
                    <div class="p-3 rounded-4 border mb-3" id="addVideoPriceBox" style="background: #f5f3ff; border-color: #ddd6fe !important; display: block;">
                        <h6 class="fw-bold small mb-2" style="color:#6d28d9;">
                            <i class="bi bi-camera-video me-1"></i> سعر استشارة الفيديو ({{ \App\Models\Setting::currencySymbol() }})
                        </h6>
                        <p class="text-secondary small mb-2">سعر جلسة الاستشارة المرئية بالفيديو أونلاين.</p>
                        <input type="number" step="0.01" name="video_price" class="form-control rounded-3 bg-white" placeholder="مثال: 40.00">
                    </div>

                    {{-- Voice Price Box --}}
                    <div class="p-3 rounded-4 border mb-3" id="addVoicePriceBox" style="background: #ecfdf5; border-color: #a7f3d0 !important; display: none;">
                        <h6 class="fw-bold small mb-2 text-success">
                            <i class="bi bi-telephone me-1"></i> سعر استشارة الصوت ({{ \App\Models\Setting::currencySymbol() }})
                        </h6>
                        <p class="text-secondary small mb-2">سعر المكالمة الصوتية الاستشارية عبر المنصة.</p>
                        <input type="number" step="0.01" name="voice_price" class="form-control rounded-3 bg-white" placeholder="مثال: 30.00">
                    </div>

                    {{-- Chat Price Box --}}
                    <div class="p-3 rounded-4 border mb-3" id="addChatPriceBox" style="background: #fffbeb; border-color: #fde68a !important; display: none;">
                        <h6 class="fw-bold small mb-2" style="color:#b45309;">
                            <i class="bi bi-chat-dots me-1"></i> سعر استشارة الشات ({{ \App\Models\Setting::currencySymbol() }})
                        </h6>
                        <p class="text-secondary small mb-2">سعر المحادثة والاستشارة النصية عبر الشات.</p>
                        <input type="number" step="0.01" name="chat_price" class="form-control rounded-3 bg-white" placeholder="مثال: 20.00">
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
// Select Icon in Add / Edit form
function selectServiceIcon(iconName, inputId, element) {
    const input = document.getElementById(inputId);
    if (input) {
        input.value = iconName;
    }
    const parent = element.parentElement;
    if (parent) {
        parent.querySelectorAll('.icon-picker-item').forEach(item => {
            item.classList.remove('active');
        });
        element.classList.add('active');
    }
}

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

// Toggle Add Form Fields between Channels
function toggleAddServiceFields() {
    const ch = document.getElementById('addServiceChannel').value;
    const typeInput = document.getElementById('addServiceType');
    
    typeInput.value = (ch === 'clinic') ? 'clinic' : 'online';

    const clinicBox = document.getElementById('addClinicPriceBox');
    const videoBox  = document.getElementById('addVideoPriceBox');
    const voiceBox  = document.getElementById('addVoicePriceBox');
    const chatBox   = document.getElementById('addChatPriceBox');

    clinicBox.style.display = (ch === 'clinic') ? 'block' : 'none';
    videoBox.style.display  = (ch === 'video' || ch === 'all') ? 'block' : 'none';
    voiceBox.style.display  = (ch === 'voice' || ch === 'all') ? 'block' : 'none';
    chatBox.style.display   = (ch === 'chat'  || ch === 'all') ? 'block' : 'none';
}

// Toggle Edit Modal Fields between Channels
function toggleEditServiceFields(id) {
    const ch = document.getElementById('editServiceChannel' + id).value;
    const typeInput = document.getElementById('editServiceType' + id);

    typeInput.value = (ch === 'clinic') ? 'clinic' : 'online';

    const clinicBox = document.getElementById('editClinicPriceBox' + id);
    const videoBox  = document.getElementById('editVideoPriceBox' + id);
    const voiceBox  = document.getElementById('editVoicePriceBox' + id);
    const chatBox   = document.getElementById('editChatPriceBox' + id);

    clinicBox.style.display = (ch === 'clinic') ? 'block' : 'none';
    videoBox.style.display  = (ch === 'video' || ch === 'all') ? 'block' : 'none';
    voiceBox.style.display  = (ch === 'voice' || ch === 'all') ? 'block' : 'none';
    chatBox.style.display   = (ch === 'chat'  || ch === 'all') ? 'block' : 'none';
}
</script>
@endsection
