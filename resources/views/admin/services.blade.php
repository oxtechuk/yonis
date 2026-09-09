@extends('layouts.admin')

@section('title', 'إدارة وتصنيف الخدمات الطبية والأسعار')

@section('styles')
<style>
    /* ════ Luxury Admin Services Design System ════ */
    :root {
        --srv-primary: #4055A5;
        --srv-primary-dark: #1C2752;
        --srv-accent: #6D8FD6;
        --srv-bg-card: #FFFFFF;
        --srv-border: #E8EEF8;
    }

    /* KPI Stats Bar */
    .kpi-card {
        background: #FFFFFF;
        border: 1px solid var(--srv-border);
        border-radius: 18px;
        padding: 1.15rem 1.3rem;
        box-shadow: 0 4px 18px rgba(64, 85, 165, 0.04);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        position: relative;
        overflow: hidden;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 22px rgba(64, 85, 165, 0.08);
    }
    .kpi-card::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--srv-primary), var(--srv-accent));
        opacity: 0.8;
    }
    .kpi-icon-bubble {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }

    /* Channel Selector Visual Cards */
    .channel-selector-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(95px, 1fr));
        gap: 6px;
    }
    .channel-select-card {
        border: 1.5px solid #E2E8F0;
        background: #F8FAFC;
        border-radius: 12px;
        padding: 8px 4px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 3px;
    }
    .channel-select-card:hover {
        border-color: var(--srv-primary);
        background: #F0F4FB;
        transform: translateY(-2px);
    }
    .channel-select-card.active {
        border-color: var(--srv-primary);
        background: linear-gradient(135deg, rgba(64, 85, 165, 0.08), rgba(109, 143, 214, 0.12));
        color: var(--srv-primary-dark);
        box-shadow: 0 4px 12px rgba(64, 85, 165, 0.15);
    }
    .channel-select-card .channel-icon {
        font-size: 1.25rem;
        transition: transform 0.2s ease;
    }
    .channel-select-card.active .channel-icon {
        transform: scale(1.12);
    }
    .channel-select-card .channel-title {
        font-size: 0.74rem;
        font-weight: 800;
        margin: 0;
        line-height: 1.2;
    }
    .channel-select-card .channel-sub {
        font-size: 0.62rem;
        color: #64748B;
        margin: 0;
    }

    /* Icon Picker Luxury */
    .icon-picker-nav {
        background: #F1F5F9;
        border-radius: 10px;
        padding: 2px;
        display: inline-flex;
        gap: 3px;
        margin-bottom: 8px;
    }
    .icon-picker-nav-btn {
        border: none;
        background: transparent;
        border-radius: 8px;
        padding: 3px 12px;
        font-size: 0.76rem;
        font-weight: 700;
        color: #64748B;
        transition: all 0.2s ease;
    }
    .icon-picker-nav-btn.active {
        background: #FFFFFF;
        color: var(--srv-primary);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
    }
    .icon-picker-grid-luxury {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 5px;
    }
    .icon-box-item {
        background: #FFFFFF;
        border: 1.5px solid #E2E8F0;
        border-radius: 10px;
        padding: 6px 2px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 2px;
    }
    .icon-box-item:hover {
        border-color: var(--srv-primary);
        background: #F0F4FB;
        transform: translateY(-2px);
    }
    .icon-box-item.active {
        border-color: var(--srv-primary);
        background: linear-gradient(135deg, var(--srv-primary), var(--srv-primary-dark));
        color: #FFFFFF !important;
        box-shadow: 0 4px 10px rgba(64, 85, 165, 0.3);
    }
    .icon-box-item.active i,
    .icon-box-item.active span {
        color: #FFFFFF !important;
    }

    /* Modern Category Segment Capsule */
    .category-segment-capsule {
        background: #F1F5F9;
        border: 1px solid #E2E8F0;
        border-radius: 30px;
        padding: 3px;
        display: inline-flex;
        gap: 3px;
    }
    .category-segment-capsule .service-filter-btn {
        color: #64748B;
        border: none;
        background: transparent;
        border-radius: 25px;
        padding: 5px 14px;
        font-size: 0.8rem;
        font-weight: 700;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .category-segment-capsule .service-filter-btn:hover {
        color: var(--srv-primary);
        background: rgba(64, 85, 165, 0.08);
    }
    .category-segment-capsule .service-filter-btn.active {
        color: #FFFFFF !important;
        background: linear-gradient(135deg, var(--srv-primary), var(--srv-primary-dark)) !important;
        box-shadow: 0 4px 10px rgba(64, 85, 165, 0.25);
    }

    /* Price Chip Luxury */
    .price-chip-luxury {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 700;
        white-space: nowrap;
        border: 1px solid transparent;
        transition: transform 0.15s ease;
    }
    .price-chip-luxury:hover {
        transform: translateY(-1px);
    }
    .price-chip-luxury.clinic { background: #FFF1F2; border-color: #FECDD3; color: #BE123C; }
    .price-chip-luxury.chat   { background: #FFFBEB; border-color: #FDE68A; color: #B45309; }
    .price-chip-luxury.voice  { background: #ECFDF5; border-color: #A7F3D0; color: #047857; }
    .price-chip-luxury.video  { background: #F5F3FF; border-color: #DDD6FE; color: #6D28D9; }

    /* Action Buttons */
    .btn-action-luxury {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 700;
        border: 1px solid #E2E8F0;
        background: #FFFFFF;
        color: #334155;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-action-luxury.edit:hover {
        background: #F0F4FB;
        color: var(--srv-primary);
        border-color: var(--srv-primary);
        box-shadow: 0 2px 8px rgba(64, 85, 165, 0.15);
    }
    .btn-action-luxury.delete {
        color: #DC2626;
        padding: 5px 8px;
    }
    .btn-action-luxury.delete:hover {
        background: #FEF2F2;
        border-color: #EF4444;
        color: #B91C1C;
        box-shadow: 0 2px 8px rgba(220, 38, 38, 0.15);
    }

    /* Status Pill */
    .badge-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.74rem;
        font-weight: 700;
        white-space: nowrap;
    }
    .badge-status-pill.active { background: #ECFDF5; color: #059669; border: 1px solid #A7F3D0; }
    .badge-status-pill.inactive { background: #FEF2F2; color: #DC2626; border: 1px solid #FECACA; }
    .badge-status-pill .dot { width: 6px; height: 6px; border-radius: 50%; display: inline-block; }
    .badge-status-pill.active .dot { background-color: #10B981; }
    .badge-status-pill.inactive .dot { background-color: #EF4444; }

    /* Custom Upload Dropzone */
    .custom-icon-upload-box {
        border: 2px dashed #CBD5E1;
        background: #F8FAFC;
        border-radius: 12px;
        padding: 12px;
        text-align: center;
        transition: all 0.25s ease;
        cursor: pointer;
    }
    .custom-icon-upload-box:hover {
        border-color: var(--srv-primary);
        background: #F0F4FB;
    }

    /* Price Section Card */
    .pricing-tier-card {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 10px 12px;
        margin-bottom: 8px;
        transition: all 0.2s ease;
    }
    .pricing-tier-card:hover {
        border-color: #CBD5E1;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    }

    /* Bilingual Pill Tab */
    .lang-badge-pill {
        font-size: 0.7rem;
        font-weight: 800;
        padding: 2px 7px;
        border-radius: 6px;
        letter-spacing: 0.3px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-0">

    {{-- ═══ Top Summary KPI Stats ═══ --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="kpi-card d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-secondary small fw-bold mb-1">إجمالي الخدمات</div>
                    <div class="fs-4 fw-black text-dark">{{ count($services) }}</div>
                </div>
                <div class="kpi-icon-bubble" style="background: rgba(64, 85, 165, 0.1); color: var(--srv-primary);">
                    <i class="bi bi-grid-fill"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="kpi-card d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-secondary small fw-bold mb-1">استشارات أونلاين</div>
                    <div class="fs-4 fw-black text-primary">{{ $services->whereIn('type', ['online', 'both'])->count() }}</div>
                </div>
                <div class="kpi-icon-bubble" style="background: rgba(14, 165, 233, 0.1); color: #0284C7;">
                    <i class="bi bi-camera-video-fill"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="kpi-card d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-secondary small fw-bold mb-1">كشوفات العيادة</div>
                    <div class="fs-4 fw-black" style="color: #BE123C;">{{ $services->whereIn('type', ['clinic', 'both'])->count() }}</div>
                </div>
                <div class="kpi-icon-bubble" style="background: rgba(225, 29, 72, 0.1); color: #BE123C;">
                    <i class="bi bi-hospital-fill"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="kpi-card d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-secondary small fw-bold mb-1">الخدمات النشطة</div>
                    <div class="fs-4 fw-black text-success">{{ $services->where('is_active', true)->count() }}</div>
                </div>
                <div class="kpi-icon-bubble" style="background: rgba(16, 185, 129, 0.1); color: #059669;">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ Main Content Grid ═══ --}}
    <div class="row g-4 align-items-start">

        {{-- ── Left Column: Add Service Panel ───────────────────────── --}}
        <div class="col-xl-5 col-lg-5 col-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 px-4 border-bottom border-light">
                    <h5 class="fw-black m-0 text-dark d-flex align-items-center gap-2" style="font-size: 1.05rem;">
                        <span class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width:28px; height:28px; background: linear-gradient(135deg, var(--srv-primary), var(--srv-primary-dark)); font-size:0.85rem;">
                            <i class="bi bi-plus-lg"></i>
                        </span>
                        <span>إضافة خدمة جديدة (عربي / English)</span>
                    </h5>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('admin.services.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        {{-- 1. Visual Channel / Medium Selector --}}
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark mb-2 d-flex align-items-center gap-1.5">
                                <i class="bi bi-broadcast-pin text-primary"></i>
                                <span>قناة ووسيلة تقديم الخدمة:</span>
                            </label>
                            
                            <input type="hidden" name="type" id="addServiceType" value="online">
                            <input type="hidden" name="channel" id="addServiceChannel" value="video">

                            <div class="channel-selector-grid">
                                <div class="channel-select-card active" onclick="setAddChannel('video', this)">
                                    <i class="bi bi-camera-video-fill channel-icon" style="color: #6D28D9;"></i>
                                    <p class="channel-title">فيديو أونلاين</p>
                                    <p class="channel-sub">Video Call</p>
                                </div>
                                <div class="channel-select-card" onclick="setAddChannel('voice', this)">
                                    <i class="bi bi-telephone-fill channel-icon" style="color: #059669;"></i>
                                    <p class="channel-title">مكالمة صوتية</p>
                                    <p class="channel-sub">Voice Call</p>
                                </div>
                                <div class="channel-select-card" onclick="setAddChannel('chat', this)">
                                    <i class="bi bi-chat-dots-fill channel-icon" style="color: #D97706;"></i>
                                    <p class="channel-title">محادثة شات</p>
                                    <p class="channel-sub">Chat Only</p>
                                </div>
                                <div class="channel-select-card" onclick="setAddChannel('all', this)">
                                    <i class="bi bi-laptop-fill channel-icon" style="color: #2563EB;"></i>
                                    <p class="channel-title">متعدد القنوات</p>
                                    <p class="channel-sub">شات+صوت+فيديو</p>
                                </div>
                                <div class="channel-select-card" onclick="setAddChannel('clinic', this)">
                                    <i class="bi bi-hospital-fill channel-icon" style="color: #BE123C;"></i>
                                    <p class="channel-title">كشف العيادة</p>
                                    <p class="channel-sub">حضور مباشر</p>
                                </div>
                            </div>
                        </div>

                        {{-- 2. Luxury Dual-Mode Icon Selection --}}
                        <div class="mb-3 pt-2 border-top">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label small fw-bold text-dark m-0 d-flex align-items-center gap-1.5">
                                    <i class="bi bi-palette-fill text-primary"></i>
                                    <span>أيقونة الخدمة (تطبيق وموقع):</span>
                                </label>
                                <div class="icon-picker-nav">
                                    <button type="button" class="icon-picker-nav-btn active" id="btnIconTabPresets" onclick="switchAddIconTab('presets')">المكتبة</button>
                                    <button type="button" class="icon-picker-nav-btn" id="btnIconTabUpload" onclick="switchAddIconTab('upload')">رفع صورة</button>
                                </div>
                            </div>

                            <input type="hidden" name="icon" id="addServiceIcon" value="bi-heart-pulse">

                            {{-- Tab 1: Icon Presets --}}
                            <div id="addIconPresetsView">
                                <div class="icon-picker-grid-luxury" id="addIconPicker">
                                    <div class="icon-box-item active" onclick="selectServiceIcon('bi-heart-pulse', 'addServiceIcon', this)">
                                        <i class="bi bi-heart-pulse fs-5"></i>
                                        <span style="font-size:0.65rem;">صحة نفسية</span>
                                    </div>
                                    <div class="icon-box-item" onclick="selectServiceIcon('bi-camera-video', 'addServiceIcon', this)">
                                        <i class="bi bi-camera-video fs-5"></i>
                                        <span style="font-size:0.65rem;">فيديو</span>
                                    </div>
                                    <div class="icon-box-item" onclick="selectServiceIcon('bi-telephone', 'addServiceIcon', this)">
                                        <i class="bi bi-telephone fs-5"></i>
                                        <span style="font-size:0.65rem;">مكالمة</span>
                                    </div>
                                    <div class="icon-box-item" onclick="selectServiceIcon('bi-chat-dots', 'addServiceIcon', this)">
                                        <i class="bi bi-chat-dots fs-5"></i>
                                        <span style="font-size:0.65rem;">شات</span>
                                    </div>
                                    <div class="icon-box-item" onclick="selectServiceIcon('bi-hospital', 'addServiceIcon', this)">
                                        <i class="bi bi-hospital fs-5"></i>
                                        <span style="font-size:0.65rem;">عيادة</span>
                                    </div>
                                    <div class="icon-box-item" onclick="selectServiceIcon('bi-person-heart', 'addServiceIcon', this)">
                                        <i class="bi bi-person-heart fs-5"></i>
                                        <span style="font-size:0.65rem;">فردي</span>
                                    </div>
                                    <div class="icon-box-item" onclick="selectServiceIcon('bi-people', 'addServiceIcon', this)">
                                        <i class="bi bi-people fs-5"></i>
                                        <span style="font-size:0.65rem;">زوجي/أسري</span>
                                    </div>
                                    <div class="icon-box-item" onclick="selectServiceIcon('bi-emoji-smile', 'addServiceIcon', this)">
                                        <i class="bi bi-emoji-smile fs-5"></i>
                                        <span style="font-size:0.65rem;">دعم نفسي</span>
                                    </div>
                                    <div class="icon-box-item" onclick="selectServiceIcon('bi-lightbulb', 'addServiceIcon', this)">
                                        <i class="bi bi-lightbulb fs-5"></i>
                                        <span style="font-size:0.65rem;">تطوير</span>
                                    </div>
                                    <div class="icon-box-item" onclick="selectServiceIcon('bi-shield-check', 'addServiceIcon', this)">
                                        <i class="bi bi-shield-check fs-5"></i>
                                        <span style="font-size:0.65rem;">سرية</span>
                                    </div>
                                    <div class="icon-box-item" onclick="selectServiceIcon('bi-stars', 'addServiceIcon', this)">
                                        <i class="bi bi-stars fs-5"></i>
                                        <span style="font-size:0.65rem;">مميز</span>
                                    </div>
                                    <div class="icon-box-item" onclick="selectServiceIcon('bi-flower1', 'addServiceIcon', this)">
                                        <i class="bi bi-flower1 fs-5"></i>
                                        <span style="font-size:0.65rem;">استرخاء</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Tab 2: Upload Custom Image --}}
                            <div id="addIconUploadView" class="d-none">
                                <label class="custom-icon-upload-box d-block mb-0" for="addIconFileInput">
                                    <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                                        <i class="bi bi-cloud-arrow-up-fill fs-4 text-primary"></i>
                                        <span class="small fw-bold text-dark" id="addIconFileName">اختر صورة أو أيقونة مخصصة</span>
                                    </div>
                                    <p class="text-secondary mb-0" style="font-size: 0.72rem;">يدعم PNG أو SVG الشفافة (128x128)</p>
                                    <input type="file" id="addIconFileInput" name="icon_file" class="d-none" accept="image/*" onchange="previewAddIconFile(this)">
                                </label>
                                <div id="addIconFilePreviewContainer" class="d-none align-items-center gap-2 mt-2 p-2 bg-light rounded-3">
                                    <img id="addIconFilePreview" src="" alt="preview" style="width:32px; height:32px; object-fit:contain; border-radius:6px;">
                                    <span class="small text-success fw-bold">تم اختيار الأيقونة بنجاح</span>
                                </div>
                            </div>
                        </div>

                        {{-- 3. Bilingual Titles & Duration --}}
                        <div class="mb-3 pt-2 border-top">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="form-label small fw-bold text-dark mb-0">اسم الخدمة (عربي) <span class="text-danger">*</span></label>
                                <span class="badge bg-primary-subtle text-primary lang-badge-pill">AR</span>
                            </div>
                            <input type="text" name="title_ar" class="form-control rounded-3 py-2 text-end" placeholder="مثال: استشارة فردية - فيديو أونلاين" required>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="form-label small fw-bold text-dark mb-0">Service Title (English)</label>
                                <span class="badge bg-secondary-subtle text-secondary lang-badge-pill">EN</span>
                            </div>
                            <input type="text" name="title_en" class="form-control rounded-3 py-2" dir="ltr" placeholder="e.g. Individual Consultation - Online Video">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark mb-1">مدة الجلسة (بالدقائق) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="duration" class="form-control rounded-start-3 py-2 text-end" placeholder="45" required min="5" value="{{ \App\Models\Setting::get('default_consultation_duration', '45') }}">
                                <span class="input-group-text bg-light text-secondary small fw-bold rounded-end-3">دقيقة</span>
                            </div>
                        </div>

                        {{-- Bilingual Descriptions --}}
                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="form-label small fw-bold text-dark mb-0">الوصف والتفاصيل (عربي)</label>
                                <span class="badge bg-primary-subtle text-primary lang-badge-pill">AR</span>
                            </div>
                            <textarea name="description_ar" class="form-control rounded-3 text-end" rows="2" placeholder="اكتب هنا تفاصيل ومميزات الجلسة بالعربية..."></textarea>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="form-label small fw-bold text-dark mb-0">Description & Details (English)</label>
                                <span class="badge bg-secondary-subtle text-secondary lang-badge-pill">EN</span>
                            </div>
                            <textarea name="description_en" class="form-control rounded-3" dir="ltr" rows="2" placeholder="Write consultation details in English..."></textarea>
                        </div>

                        {{-- 4. Pricing Tiers (Dynamic based on selected channels) --}}
                        <div class="mb-3 pt-2 border-top">
                            <label class="form-label small fw-bold text-dark mb-2 d-flex align-items-center gap-1.5">
                                <i class="bi bi-cash-stack text-primary"></i>
                                <span>الأسعار والتسعير ({{ \App\Models\Setting::currencySymbol() }}):</span>
                            </label>

                            {{-- Clinic Price --}}
                            <div class="pricing-tier-card" id="addClinicPriceBox" style="display: none; border-left: 4px solid #BE123C;">
                                <div class="d-flex align-items-center justify-content-between mb-1.5">
                                    <span class="small fw-bold" style="color: #BE123C;"><i class="bi bi-hospital me-1"></i> سعر كشف العيادة:</span>
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-0.5" style="font-size:0.7rem;">حضور العيادة</span>
                                </div>
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.01" name="clinic_price" class="form-control rounded-start-3" placeholder="50.00">
                                    <span class="input-group-text bg-light fw-bold">{{ \App\Models\Setting::currencySymbol() }}</span>
                                </div>
                            </div>

                            {{-- Video Price --}}
                            <div class="pricing-tier-card" id="addVideoPriceBox" style="display: block; border-left: 4px solid #6D28D9;">
                                <div class="d-flex align-items-center justify-content-between mb-1.5">
                                    <span class="small fw-bold" style="color: #6D28D9;"><i class="bi bi-camera-video me-1"></i> سعر استشارة الفيديو:</span>
                                    <span class="badge bg-purple bg-opacity-10 text-purple rounded-pill px-2 py-0.5" style="font-size:0.7rem; color:#6D28D9; background:#F5F3FF;">Video Call</span>
                                </div>
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.01" name="video_price" class="form-control rounded-start-3" placeholder="40.00">
                                    <span class="input-group-text bg-light fw-bold">{{ \App\Models\Setting::currencySymbol() }}</span>
                                </div>
                            </div>

                            {{-- Voice Price --}}
                            <div class="pricing-tier-card" id="addVoicePriceBox" style="display: none; border-left: 4px solid #059669;">
                                <div class="d-flex align-items-center justify-content-between mb-1.5">
                                    <span class="small fw-bold text-success"><i class="bi bi-telephone me-1"></i> سعر استشارة الصوت:</span>
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-0.5" style="font-size:0.7rem;">Voice Call</span>
                                </div>
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.01" name="voice_price" class="form-control rounded-start-3" placeholder="30.00">
                                    <span class="input-group-text bg-light fw-bold">{{ \App\Models\Setting::currencySymbol() }}</span>
                                </div>
                            </div>

                            {{-- Chat Price --}}
                            <div class="pricing-tier-card" id="addChatPriceBox" style="display: none; border-left: 4px solid #D97706;">
                                <div class="d-flex align-items-center justify-content-between mb-1.5">
                                    <span class="small fw-bold" style="color: #D97706;"><i class="bi bi-chat-dots me-1"></i> سعر استشارة الشات:</span>
                                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2 py-0.5" style="font-size:0.7rem; color:#B45309; background:#FFFBEB;">Chat Only</span>
                                </div>
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.01" name="chat_price" class="form-control rounded-start-3" placeholder="20.00">
                                    <span class="input-group-text bg-light fw-bold">{{ \App\Models\Setting::currencySymbol() }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- 5. Active Status Switch --}}
                        <div class="form-check form-switch mb-4 p-0 d-flex align-items-center justify-content-between border-top pt-3">
                            <label class="form-check-label fw-bold small text-dark m-0" for="activeSwitch">تفعيل الخدمة وإظهارها فورياً للحجز</label>
                            <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" name="is_active" id="activeSwitch" checked style="width: 2.4em; height: 1.25em;">
                        </div>

                        {{-- Submit Button --}}
                        <button type="submit" class="btn btn-royal-primary w-100 py-3 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-check2-circle fs-5"></i>
                            <span>حفظ وإضافة الخدمة الجديدة</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ── Right Column: Services List & Management Table ───────── --}}
        <div class="col-xl-7 col-lg-7 col-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 px-4 border-bottom border-light">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <h5 class="fw-black m-0 text-dark d-flex align-items-center gap-2" style="font-size: 1.1rem;">
                                <i class="bi bi-collection-fill text-primary"></i>
                                <span>قائمة الخدمات الطبية المعتمدة</span>
                            </h5>
                            <p class="text-secondary small m-0 mt-0.5">تدعم اللغتين العربية والإنجليزية وتتزامن تلقائياً مع التطبيق والموقع.</p>
                        </div>
                        
                        {{-- Segmented Category Filter Capsule --}}
                        <div class="category-segment-capsule">
                            <button type="button" class="service-filter-btn active" onclick="filterServicesTable('all', this)">
                                <i class="bi bi-grid-fill"></i> الجميع ({{ count($services) }})
                            </button>
                            <button type="button" class="service-filter-btn" onclick="filterServicesTable('online', this)">
                                <i class="bi bi-laptop"></i> أونلاين ({{ $services->whereIn('type', ['online', 'both'])->count() }})
                            </button>
                            <button type="button" class="service-filter-btn" onclick="filterServicesTable('clinic', this)">
                                <i class="bi bi-hospital"></i> عيادة ({{ $services->whereIn('type', ['clinic', 'both'])->count() }})
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-secondary" style="font-size: 0.8rem; letter-spacing: 0.3px;">
                                <tr>
                                    <th class="ps-4 py-3">الخدمة (عربي / EN)</th>
                                    <th class="py-3">المدة</th>
                                    <th class="py-3">الأسعار والقنوات</th>
                                    <th class="py-3">الحالة</th>
                                    <th class="pe-4 py-3 text-end">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($services as $service)
                                    @php
                                        $chType = $service->getChannelType();
                                    @endphp
                                    <tr class="service-row" data-type="{{ $service->type }}">
                                        {{-- 1. Service Title + Icon --}}
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="rounded-3 d-flex align-items-center justify-content-center shadow-xs" 
                                                     style="width:44px; height:44px; min-width:44px; background: linear-gradient(135deg, rgba(64, 85, 165, 0.1), rgba(109, 143, 214, 0.15)); border: 1px solid rgba(64, 85, 165, 0.18);">
                                                    @if($service->icon_url)
                                                        <img src="{{ $service->icon_url }}" alt="icon" style="width:26px; height:26px; object-fit:contain; border-radius:6px;">
                                                    @else
                                                        <i class="bi {{ $service->icon_name }} fs-4 text-primary"></i>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="fw-black text-dark fs-6 mb-0.5">{{ $service->title_ar ?: $service->title }}</div>
                                                    @if($service->title_en)
                                                        <div class="text-primary small fw-bold mb-0.5" dir="ltr" style="font-size:0.75rem;">
                                                            <i class="bi bi-translate me-1"></i> {{ $service->title_en }}
                                                        </div>
                                                    @endif
                                                    <div class="text-secondary small text-truncate" style="max-width: 220px;">{{ $service->description_ar ?: ($service->description ?: 'استشارة نفسية معتمدة') }}</div>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- 2. Duration --}}
                                        <td class="py-3">
                                            <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 fw-bold" style="font-size:0.76rem;">
                                                <i class="bi bi-clock-history me-1 text-primary"></i> {{ $service->duration }} دقيقة
                                            </span>
                                        </td>

                                        {{-- 3. Channel Pricing Chips --}}
                                        <td class="py-3">
                                            <div class="d-flex flex-wrap gap-1 align-items-center">
                                                @if($service->type === 'clinic' || (!is_null($service->clinic_price) && (float)$service->clinic_price > 0))
                                                    <span class="price-chip-luxury clinic" title="كشف في العيادة">
                                                        <i class="bi bi-hospital"></i> {{ number_format($service->clinic_price ?? $service->price, 0) }} {{ \App\Models\Setting::currencySymbol() }}
                                                    </span>
                                                @endif

                                                @if($service->type !== 'clinic')
                                                    @if($chType === 'video' || (!is_null($service->video_price) && (float)$service->video_price > 0))
                                                        <span class="price-chip-luxury video" title="استشارة فيديو أونلاين">
                                                            <i class="bi bi-camera-video"></i> {{ number_format($service->video_price ?? $service->price, 0) }} {{ \App\Models\Setting::currencySymbol() }}
                                                        </span>
                                                    @endif
                                                    @if($chType === 'voice' || (!is_null($service->voice_price) && (float)$service->voice_price > 0))
                                                        <span class="price-chip-luxury voice" title="استشارة صوت أونلاين">
                                                            <i class="bi bi-telephone"></i> {{ number_format($service->voice_price ?? $service->price, 0) }} {{ \App\Models\Setting::currencySymbol() }}
                                                        </span>
                                                    @endif
                                                    @if($chType === 'chat' || (!is_null($service->chat_price) && (float)$service->chat_price > 0))
                                                        <span class="price-chip-luxury chat" title="استشارة محادثة شات">
                                                            <i class="bi bi-chat-dots"></i> {{ number_format($service->chat_price ?? $service->price, 0) }} {{ \App\Models\Setting::currencySymbol() }}
                                                        </span>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>

                                        {{-- 4. Status --}}
                                        <td class="py-3">
                                            @if($service->is_active)
                                                <span class="badge-status-pill active"><span class="dot"></span> مفعّلة</span>
                                            @else
                                                <span class="badge-status-pill inactive"><span class="dot"></span> معطلة</span>
                                            @endif
                                        </td>

                                        {{-- 5. Actions --}}
                                        <td class="pe-4 py-3 text-end">
                                            <div class="d-flex align-items-center justify-content-end gap-1.5">
                                                <button type="button" class="btn-action-luxury edit" data-bs-toggle="modal" data-bs-target="#editModal{{ $service->id }}" title="تعديل بيانات وأسعار الخدمة">
                                                    <i class="bi bi-pencil-square"></i>
                                                    <span>تعديل</span>
                                                </button>

                                                <button type="button" class="btn-action-luxury delete" data-bs-toggle="modal" data-bs-target="#deleteServiceModal{{ $service->id }}" title="حذف الخدمة">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-secondary">
                                            <i class="bi bi-inbox fs-2 d-block text-muted mb-2"></i>
                                            <span>لا توجد خدمات مضافة حالياً. أضف خدمتك الأولى من النموذج الجانبي.</span>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         MODALS CONTAINER (Placed cleanly outside of table)
         ═══════════════════════════════════════════════════════════════ --}}
    @foreach($services as $service)
        @php
            $currCh = $service->type === 'clinic' ? 'clinic' : $service->getChannelType();
            $currIcon = $service->icon ?? 'bi-heart-pulse';
        @endphp

        {{-- ═══ Edit Modal for Service ═══ --}}
        <div class="modal fade" id="editModal{{ $service->id }}" tabindex="-1" aria-hidden="true" dir="rtl">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden text-end">
                    <div class="modal-header bg-white border-bottom py-3 px-4">
                        <h5 class="modal-title fw-black text-dark fs-6 d-flex align-items-center gap-2">
                            <i class="bi bi-pencil-square text-primary"></i>
                            <span>تعديل الخدمة: {{ $service->title_ar ?: $service->title }}</span>
                        </h5>
                        <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('admin.services.update', $service->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body p-4">
                            {{-- Channel Selector in Modal --}}
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark mb-2 d-flex align-items-center gap-1.5">
                                    <i class="bi bi-broadcast-pin text-primary"></i>
                                    <span>قناة ووسيلة تقديم الخدمة:</span>
                                </label>

                                <input type="hidden" name="type" id="editServiceType{{ $service->id }}" value="{{ $service->type === 'clinic' ? 'clinic' : 'online' }}">
                                <input type="hidden" name="channel" id="editServiceChannel{{ $service->id }}" value="{{ $currCh }}">

                                <div class="channel-selector-grid" id="editChannelGrid{{ $service->id }}">
                                    <div class="channel-select-card {{ $currCh === 'video' ? 'active' : '' }}" onclick="setEditChannel({{ $service->id }}, 'video', this)">
                                        <i class="bi bi-camera-video-fill channel-icon" style="color: #6D28D9;"></i>
                                        <p class="channel-title">فيديو أونلاين</p>
                                    </div>
                                    <div class="channel-select-card {{ $currCh === 'voice' ? 'active' : '' }}" onclick="setEditChannel({{ $service->id }}, 'voice', this)">
                                        <i class="bi bi-telephone-fill channel-icon" style="color: #059669;"></i>
                                        <p class="channel-title">مكالمة صوتية</p>
                                    </div>
                                    <div class="channel-select-card {{ $currCh === 'chat' ? 'active' : '' }}" onclick="setEditChannel({{ $service->id }}, 'chat', this)">
                                        <i class="bi bi-chat-dots-fill channel-icon" style="color: #D97706;"></i>
                                        <p class="channel-title">محادثة شات</p>
                                    </div>
                                    <div class="channel-select-card {{ $currCh === 'all' ? 'active' : '' }}" onclick="setEditChannel({{ $service->id }}, 'all', this)">
                                        <i class="bi bi-laptop-fill channel-icon" style="color: #2563EB;"></i>
                                        <p class="channel-title">متعدد القنوات</p>
                                    </div>
                                    <div class="channel-select-card {{ $currCh === 'clinic' ? 'active' : '' }}" onclick="setEditChannel({{ $service->id }}, 'clinic', this)">
                                        <i class="bi bi-hospital-fill channel-icon" style="color: #BE123C;"></i>
                                        <p class="channel-title">كشف العيادة</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Icon Picker in Modal --}}
                            <div class="mb-3 pt-2 border-top">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <label class="form-label small fw-bold text-dark m-0 d-flex align-items-center gap-1.5">
                                        <i class="bi bi-palette-fill text-primary"></i>
                                        <span>أيقونة الخدمة:</span>
                                    </label>
                                    <div class="icon-picker-nav">
                                        <button type="button" class="icon-picker-nav-btn active" id="btnEditIconTabPresets{{ $service->id }}" onclick="switchEditIconTab({{ $service->id }}, 'presets')">المكتبة</button>
                                        <button type="button" class="icon-picker-nav-btn" id="btnEditIconTabUpload{{ $service->id }}" onclick="switchEditIconTab({{ $service->id }}, 'upload')">رفع صورة</button>
                                    </div>
                                </div>

                                <input type="hidden" name="icon" id="editServiceIcon{{ $service->id }}" value="{{ $currIcon }}">

                                <div id="editIconPresetsView{{ $service->id }}">
                                    <div class="icon-picker-grid-luxury" id="editIconPicker{{ $service->id }}">
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
                                            <div class="icon-box-item {{ $currIcon === $ico['name'] ? 'active' : '' }}" onclick="selectServiceIcon('{{ $ico['name'] }}', 'editServiceIcon{{ $service->id }}', this)">
                                                <i class="bi {{ $ico['name'] }} fs-5"></i>
                                                <span style="font-size:0.65rem;">{{ $ico['label'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div id="editIconUploadView{{ $service->id }}" class="d-none">
                                    <label class="custom-icon-upload-box d-block mb-0" for="editIconFileInput{{ $service->id }}">
                                        <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                                            <i class="bi bi-cloud-arrow-up-fill fs-4 text-primary"></i>
                                            <span class="small fw-bold text-dark" id="editIconFileName{{ $service->id }}">رفع صورة/أيقونة جديدة</span>
                                        </div>
                                        <input type="file" id="editIconFileInput{{ $service->id }}" name="icon_file" class="d-none" accept="image/*" onchange="previewEditIconFile({{ $service->id }}, this)">
                                    </label>
                                    @if($service->icon_url)
                                        <div class="d-flex align-items-center gap-2 mt-2 p-2 bg-light rounded-3">
                                            <img src="{{ $service->icon_url }}" alt="current icon" style="height:32px; width:32px; object-fit:contain; border-radius:6px;">
                                            <span class="small text-success fw-bold">الأيقونة المرفوعة الحالية مفعلة</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Bilingual Inputs in Modal --}}
                            <div class="row g-3 mb-3 pt-2 border-top">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <label class="form-label small fw-bold mb-0">اسم الخدمة (عربي)</label>
                                        <span class="badge bg-primary-subtle text-primary lang-badge-pill">AR</span>
                                    </div>
                                    <input type="text" name="title_ar" class="form-control rounded-3 py-2 text-end" value="{{ $service->title_ar ?: $service->title }}" required>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <label class="form-label small fw-bold mb-0">Service Title (English)</label>
                                        <span class="badge bg-secondary-subtle text-secondary lang-badge-pill">EN</span>
                                    </div>
                                    <input type="text" name="title_en" class="form-control rounded-3 py-2" dir="ltr" value="{{ $service->title_en }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">المدة (بالدقائق)</label>
                                <div class="input-group">
                                    <input type="number" name="duration" class="form-control rounded-start-3 py-2 text-end" value="{{ $service->duration }}" required min="5">
                                    <span class="input-group-text bg-light fw-bold rounded-end-3">دقيقة</span>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <label class="form-label small fw-bold mb-0">شرح وتفاصيل الخدمة (عربي)</label>
                                        <span class="badge bg-primary-subtle text-primary lang-badge-pill">AR</span>
                                    </div>
                                    <textarea name="description_ar" class="form-control rounded-3 text-end" rows="2">{{ $service->description_ar ?: $service->description }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <label class="form-label small fw-bold mb-0">Description & Details (English)</label>
                                        <span class="badge bg-secondary-subtle text-secondary lang-badge-pill">EN</span>
                                    </div>
                                    <textarea name="description_en" class="form-control rounded-3" dir="ltr" rows="2">{{ $service->description_en }}</textarea>
                                </div>
                            </div>

                            {{-- Pricing Section --}}
                            <div class="mb-3 pt-2 border-top">
                                <label class="form-label small fw-bold text-dark mb-2">تحديث الأسعار ({{ \App\Models\Setting::currencySymbol() }}):</label>

                                <div class="pricing-tier-card" id="editClinicPriceBox{{ $service->id }}" style="display: {{ $currCh === 'clinic' ? 'block' : 'none' }}; border-left: 4px solid #BE123C;">
                                    <span class="small fw-bold d-block mb-1" style="color: #BE123C;"><i class="bi bi-hospital me-1"></i> سعر كشف العيادة:</span>
                                    <input type="number" step="0.01" name="clinic_price" class="form-control form-control-sm rounded-3 bg-white" value="{{ $service->clinic_price ?? $service->price }}" placeholder="50.00">
                                </div>

                                <div class="pricing-tier-card" id="editVideoPriceBox{{ $service->id }}" style="display: {{ in_array($currCh, ['video', 'all']) ? 'block' : 'none' }}; border-left: 4px solid #6D28D9;">
                                    <span class="small fw-bold d-block mb-1" style="color:#6D28D9;"><i class="bi bi-camera-video me-1"></i> سعر استشارة الفيديو:</span>
                                    <input type="number" step="0.01" name="video_price" class="form-control form-control-sm rounded-3 bg-white" value="{{ $service->video_price ?? $service->price }}" placeholder="40.00">
                                </div>

                                <div class="pricing-tier-card" id="editVoicePriceBox{{ $service->id }}" style="display: {{ in_array($currCh, ['voice', 'all']) ? 'block' : 'none' }}; border-left: 4px solid #059669;">
                                    <span class="small fw-bold d-block mb-1 text-success"><i class="bi bi-telephone me-1"></i> سعر استشارة الصوت:</span>
                                    <input type="number" step="0.01" name="voice_price" class="form-control form-control-sm rounded-3 bg-white" value="{{ $service->voice_price ?? $service->price }}" placeholder="30.00">
                                </div>

                                <div class="pricing-tier-card" id="editChatPriceBox{{ $service->id }}" style="display: {{ in_array($currCh, ['chat', 'all']) ? 'block' : 'none' }}; border-left: 4px solid #D97706;">
                                    <span class="small fw-bold d-block mb-1" style="color:#D97706;"><i class="bi bi-chat-dots me-1"></i> سعر استشارة الشات:</span>
                                    <input type="number" step="0.01" name="chat_price" class="form-control form-control-sm rounded-3 bg-white" value="{{ $service->chat_price ?? $service->price }}" placeholder="20.00">
                                </div>
                            </div>

                            <div class="form-check form-switch p-0 d-flex align-items-center justify-content-between border-top pt-3">
                                <label class="form-check-label fw-bold small text-dark m-0" for="editActive{{ $service->id }}">تفعيل الخدمة وإظهارها للمرضى في صفحة الحجز</label>
                                <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" name="is_active" id="editActive{{ $service->id }}" @if($service->is_active) checked @endif style="width: 2.4em; height: 1.25em;">
                            </div>
                        </div>

                        <div class="modal-footer bg-light border-top py-3 px-4">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-royal-primary rounded-pill px-4 fw-bold">حفظ التعديلات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ═══ Delete Confirmation Modal ═══ --}}
        <div class="modal fade" id="deleteServiceModal{{ $service->id }}" tabindex="-1" aria-hidden="true" dir="rtl">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden text-end">
                    <div class="modal-header bg-danger text-white py-3 px-4">
                        <h5 class="modal-title fw-bold fs-6"><i class="bi bi-exclamation-triangle-fill me-1"></i> تأكيد حذف الخدمة</h5>
                        <button type="button" class="btn-close btn-close-white ms-0 me-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        <div class="rounded-circle bg-danger bg-opacity-10 text-danger mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 1.75rem;">
                            <i class="bi bi-trash3-fill"></i>
                        </div>
                        <h5 class="fw-black text-dark mb-2">هل أنت متأكد من حذف هذه الخدمة؟</h5>
                        <p class="text-secondary small mb-3">
                            سيتم حذف الخدمة <strong>«{{ $service->title_ar ?: $service->title }}»</strong> ولن تظهر بعد الآن في خيارات الحجز.
                        </p>
                    </div>
                    <div class="modal-footer bg-light border-top justify-content-center gap-2 py-3 px-4">
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
    @endforeach

</div>
@endsection

@section('scripts')
<script>
// ════ Channel Selector Switching in Add Form ════
function setAddChannel(channel, element) {
    document.querySelectorAll('.channel-select-card').forEach(el => el.classList.remove('active'));
    element.classList.add('active');

    document.getElementById('addServiceChannel').value = channel;
    document.getElementById('addServiceType').value = (channel === 'clinic') ? 'clinic' : 'online';

    const clinicBox = document.getElementById('addClinicPriceBox');
    const videoBox  = document.getElementById('addVideoPriceBox');
    const voiceBox  = document.getElementById('addVoicePriceBox');
    const chatBox   = document.getElementById('addChatPriceBox');

    if (clinicBox) clinicBox.style.display = (channel === 'clinic') ? 'block' : 'none';
    if (videoBox)  videoBox.style.display  = (channel === 'video' || channel === 'all') ? 'block' : 'none';
    if (voiceBox)  voiceBox.style.display  = (channel === 'voice' || channel === 'all') ? 'block' : 'none';
    if (chatBox)   chatBox.style.display   = (channel === 'chat'  || channel === 'all') ? 'block' : 'none';
}

// ════ Channel Selector in Edit Modal ════
function setEditChannel(id, channel, element) {
    const parent = element.parentElement;
    parent.querySelectorAll('.channel-select-card').forEach(el => el.classList.remove('active'));
    element.classList.add('active');

    document.getElementById('editServiceChannel' + id).value = channel;
    document.getElementById('editServiceType' + id).value = (channel === 'clinic') ? 'clinic' : 'online';

    const clinicBox = document.getElementById('editClinicPriceBox' + id);
    const videoBox  = document.getElementById('editVideoPriceBox' + id);
    const voiceBox  = document.getElementById('editVoicePriceBox' + id);
    const chatBox   = document.getElementById('editChatPriceBox' + id);

    if (clinicBox) clinicBox.style.display = (channel === 'clinic') ? 'block' : 'none';
    if (videoBox)  videoBox.style.display  = (channel === 'video' || channel === 'all') ? 'block' : 'none';
    if (voiceBox)  voiceBox.style.display  = (channel === 'voice' || channel === 'all') ? 'block' : 'none';
    if (chatBox)   chatBox.style.display   = (channel === 'chat'  || channel === 'all') ? 'block' : 'none';
}

// ════ Icon Picker Tab Switcher in Add Form ════
function switchAddIconTab(tab) {
    const btnPresets = document.getElementById('btnIconTabPresets');
    const btnUpload = document.getElementById('btnIconTabUpload');
    const presetsView = document.getElementById('addIconPresetsView');
    const uploadView = document.getElementById('addIconUploadView');

    if (tab === 'presets') {
        btnPresets.classList.add('active');
        btnUpload.classList.remove('active');
        presetsView.classList.remove('d-none');
        uploadView.classList.add('d-none');
    } else {
        btnUpload.classList.add('active');
        btnPresets.classList.remove('active');
        uploadView.classList.remove('d-none');
        presetsView.classList.add('d-none');
    }
}

// ════ Icon Picker Tab Switcher in Edit Modal ════
function switchEditIconTab(id, tab) {
    const btnPresets = document.getElementById('btnEditIconTabPresets' + id);
    const btnUpload = document.getElementById('btnEditIconTabUpload' + id);
    const presetsView = document.getElementById('editIconPresetsView' + id);
    const uploadView = document.getElementById('editIconUploadView' + id);

    if (tab === 'presets') {
        btnPresets.classList.add('active');
        btnUpload.classList.remove('active');
        presetsView.classList.remove('d-none');
        uploadView.classList.add('d-none');
    } else {
        btnUpload.classList.add('active');
        btnPresets.classList.remove('active');
        uploadView.classList.remove('d-none');
        presetsView.classList.add('d-none');
    }
}

// ════ Select Icon from Library ════
function selectServiceIcon(iconName, inputId, element) {
    const input = document.getElementById(inputId);
    if (input) {
        input.value = iconName;
    }
    const parent = element.parentElement;
    if (parent) {
        parent.querySelectorAll('.icon-box-item').forEach(item => item.classList.remove('active'));
        element.classList.add('active');
    }
}

// ════ Preview Custom Icon File ════
function previewAddIconFile(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('addIconFilePreview');
            const container = document.getElementById('addIconFilePreviewContainer');
            const nameEl = document.getElementById('addIconFileName');
            preview.src = e.target.result;
            container.classList.remove('d-none');
            container.classList.add('d-flex');
            nameEl.textContent = input.files[0].name;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function previewEditIconFile(id, input) {
    if (input.files && input.files[0]) {
        const nameEl = document.getElementById('editIconFileName' + id);
        if (nameEl) {
            nameEl.textContent = 'تم اختيار: ' + input.files[0].name;
        }
    }
}

// ════ Filter Services Table by Category ════
function filterServicesTable(category, btn) {
    document.querySelectorAll('.service-filter-btn').forEach(b => b.classList.remove('active'));
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
</script>
@endsection
