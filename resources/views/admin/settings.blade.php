@extends('layouts.admin')

@section('title', 'إعدادات المنصة والهوية والـ SEO')

@section('content')
<div class="card border-0 shadow-sm p-4 rounded-4">
    <div class="card-header bg-white py-3 border-0 px-0 d-flex justify-content-between align-items-center mb-2">
        <div>
            <h4 class="fw-bold m-0 text-dark"><i class="bi bi-sliders text-primary me-2"></i> إعدادات المنصة، الهوية البصرية والـ SEO</h4>
            <p class="text-secondary small mb-0 mt-1">التحكم بالشعار، ألوان الموقع، محركات البحث، التتبع وإشعارات المواعيد.</p>
        </div>
    </div>
    
    <div class="card-body px-0">
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Navigation Tabs -->
            <ul class="nav nav-pills mb-4 gap-2 bg-light p-2 rounded-4" id="settingsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold px-4 rounded-3" id="branding-tab" data-bs-toggle="tab" data-bs-target="#branding-panel" type="button" role="tab">
                        <i class="bi bi-palette-fill me-1 text-primary"></i> الشعار والألوان (Branding)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold px-4 rounded-3" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo-panel" type="button" role="tab">
                        <i class="bi bi-search me-1 text-success"></i> محركات البحث (SEO)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold px-4 rounded-3" id="analytics-tab" data-bs-toggle="tab" data-bs-target="#analytics-panel" type="button" role="tab">
                        <i class="bi bi-graph-up-arrow me-1 text-info"></i> التحليلات والتتبع (Analytics)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold px-4 rounded-3" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications-panel" type="button" role="tab">
                        <i class="bi bi-bell-fill me-1 text-warning"></i> الإشعارات (Notifications)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold px-4 rounded-3" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment-panel" type="button" role="tab">
                        <i class="bi bi-credit-card-2-front-fill me-1 text-danger"></i> إعدادات الدفع
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content pt-2" id="settingsTabsContent">
                
                <!-- 1. Branding & Colors Panel -->
                <div class="tab-pane fade show active" id="branding-panel" role="tabpanel" aria-labelledby="branding-tab">
                    <div class="row g-4 col-lg-10">
                        
                        <!-- Site Title -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">اسم المنصة / العيادة</label>
                            <input type="text" name="site_title" class="form-control form-control-lg rounded-3" placeholder="إدارة العيادة" value="{{ $settings['site_title'] }}">
                        </div>

                        <!-- Doctor Name -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">اسم الطبيب المعالج</label>
                            <input type="text" name="doctor_name" class="form-control form-control-lg rounded-3" placeholder="يونس المرشد" value="{{ $settings['doctor_name'] }}">
                        </div>

                        <!-- Header Logo Section -->
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <label class="form-label fw-bold text-dark mb-1">شعار الهيدر (Header Logo)</label>
                                <p class="text-secondary small mb-3">الشعار الظاهر في شريط التنقل العلوي وصفحات الدخول.</p>
                                
                                <div class="row align-items-center g-3">
                                    <div class="col-auto">
                                        @if(!empty($settings['site_logo']))
                                            <div class="border rounded-3 p-2 bg-white text-center shadow-sm" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                                <img src="{{ $settings['site_logo'] }}" alt="Logo" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                            </div>
                                        @else
                                            <div class="border rounded-3 p-2 bg-white text-center text-muted" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                                                Ψ
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col">
                                        <div class="mb-2">
                                            <label class="form-label small fw-bold">رفع ملف جديد:</label>
                                            <input type="file" name="logo_file" class="form-control form-control-sm rounded-3" accept="image/*">
                                        </div>
                                        <div>
                                            <label class="form-label small fw-bold">أو رابط مباشر (URL):</label>
                                            <input type="text" name="site_logo" class="form-control form-control-sm rounded-3" placeholder="https://example.com/logo.png" value="{{ $settings['site_logo'] }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Logo Section -->
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <label class="form-label fw-bold text-dark mb-1">شعار الفوتر (Footer Logo)</label>
                                <p class="text-secondary small mb-3">الشعار الظاهر بأسفل الموقع (إذا ترك فارغاً سيتم استخدام شعار الهيدر).</p>
                                
                                <div class="row align-items-center g-3">
                                    <div class="col-auto">
                                        @if(!empty($settings['footer_logo']))
                                            <div class="border rounded-3 p-2 bg-white text-center shadow-sm" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                                <img src="{{ $settings['footer_logo'] }}" alt="Footer Logo" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                            </div>
                                        @elseif(!empty($settings['site_logo']))
                                            <div class="border rounded-3 p-2 bg-white text-center shadow-sm" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;" title="افتراضي من الهيدر">
                                                <img src="{{ $settings['site_logo'] }}" alt="Default Logo" style="max-width: 100%; max-height: 100%; object-fit: contain; opacity: 0.75;">
                                            </div>
                                        @else
                                            <div class="border rounded-3 p-2 bg-white text-center text-muted" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                                                Ψ
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col">
                                        <div class="mb-2">
                                            <label class="form-label small fw-bold">رفع ملف جديد:</label>
                                            <input type="file" name="footer_logo_file" class="form-control form-control-sm rounded-3" accept="image/*">
                                        </div>
                                        <div>
                                            <label class="form-label small fw-bold">أو رابط مباشر (URL):</label>
                                            <input type="text" name="footer_logo" class="form-control form-control-sm rounded-3" placeholder="https://example.com/footer-logo.png" value="{{ $settings['footer_logo'] }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Booking Banner Image Section -->
                        <div class="col-12">
                            <div class="p-3 bg-light rounded-4 border">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div>
                                        <label class="form-label fw-bold text-dark mb-0">
                                            <i class="bi bi-image text-primary me-1"></i> صورة بانر الحجز السريع (Booking CTA Banner Image)
                                        </label>
                                        <p class="text-secondary small mb-0">الصورة الظاهرة في قسم دعوة الحجز (Booking Banner) أسفل الصفحة الرئيسية.</p>
                                    </div>
                                </div>
                                
                                <div class="row align-items-center g-3 mt-1">
                                    <div class="col-auto">
                                        @if(!empty($settings['booking_banner_image']))
                                            <div class="border rounded-3 p-1 bg-white text-center shadow-sm" style="width: 140px; height: 90px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                                <img src="{{ $settings['booking_banner_image'] }}" alt="Booking Banner" style="max-width: 100%; max-height: 100%; object-fit: cover; border-radius: 6px;">
                                            </div>
                                        @else
                                            <div class="border rounded-3 p-1 bg-white text-center shadow-sm" style="width: 140px; height: 90px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                                <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=700&q=80" alt="Default Banner" style="max-width: 100%; max-height: 100%; object-fit: cover; border-radius: 6px; opacity: 0.7;">
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col">
                                        <div class="mb-2">
                                            <label class="form-label small fw-bold">رفع صورة جديدة:</label>
                                            <input type="file" name="booking_banner_file" class="form-control form-control-sm rounded-3" accept="image/*">
                                        </div>
                                        <div>
                                            <label class="form-label small fw-bold">أو رابط صورة مباشر (URL):</label>
                                            <input type="text" name="booking_banner_image" class="form-control form-control-sm rounded-3" placeholder="https://example.com/banner-img.png" value="{{ $settings['booking_banner_image'] }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Colors Picker -->
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-4 border">
                                <label class="form-label fw-bold text-dark mb-1">اللون الرئيسي (Primary Color)</label>
                                <p class="text-secondary small mb-2">اللون الأساسي للأزرار، الهيدر والعناصر التفاعلية.</p>
                                <div class="d-flex align-items-center gap-3">
                                    <input type="color" id="primary_color_picker" class="form-control form-control-color border-0 p-1" value="{{ $settings['primary_color'] }}" style="width: 50px; height: 50px; cursor: pointer;" onchange="document.getElementById('primary_color_input').value = this.value">
                                    <input type="text" id="primary_color_input" name="primary_color" class="form-control form-control-lg rounded-3 fw-bold" value="{{ $settings['primary_color'] }}" onchange="document.getElementById('primary_color_picker').value = this.value">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-4 border">
                                <label class="form-label fw-bold text-dark mb-1">اللون الثانوي / الداكن (Secondary Color)</label>
                                <p class="text-secondary small mb-2">لون الـ Hover، الأزرار التكميلية والخلفيات المظلمة.</p>
                                <div class="d-flex align-items-center gap-3">
                                    <input type="color" id="secondary_color_picker" class="form-control form-control-color border-0 p-1" value="{{ $settings['secondary_color'] }}" style="width: 50px; height: 50px; cursor: pointer;" onchange="document.getElementById('secondary_color_input').value = this.value">
                                    <input type="text" id="secondary_color_input" name="secondary_color" class="form-control form-control-lg rounded-3 fw-bold" value="{{ $settings['secondary_color'] }}" onchange="document.getElementById('secondary_color_picker').value = this.value">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- 2. SEO Panel -->
                <div class="tab-pane fade" id="seo-panel" role="tabpanel" aria-labelledby="seo-tab">
                    <div class="row g-4 col-lg-10">
                        
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark">كود التوثيق لجوجل (Google Search Console Verification Code)</label>
                            <p class="text-secondary small">أدخل قيمة `meta google-site-verification` لإصبات ملكية الموقع في Google Console.</p>
                            <input type="text" name="google_site_verification" class="form-control form-control-lg rounded-3" placeholder="a1b2c3d4e5f6g7h8..." value="{{ $settings['google_site_verification'] }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold text-dark">الوصف الافتراضي (Meta Description)</label>
                            <p class="text-secondary small">وصف مختصر يظهر في نتائج بحث جوجل وعند مشاركة رابط المنصة.</p>
                            <textarea name="meta_description" class="form-control rounded-3" rows="3" placeholder="احجز استشارتك النفسية الآن مع المعالج يونس المرشد...">{{ $settings['meta_description'] }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold text-dark">الكلمات المفتاحية (Meta Keywords)</label>
                            <p class="text-secondary small">افصل بين الكلمات بفارزة (مثال: معالج نفسي, حجز موعد, استشارة أسرية).</p>
                            <input type="text" name="meta_keywords" class="form-control form-control-lg rounded-3" placeholder="معالج نفسي, استشارة نفسية, يونس المرشد..." value="{{ $settings['meta_keywords'] }}">
                        </div>

                        <div class="col-12">
                            <div class="p-3 bg-light rounded-4 border">
                                <label class="form-label fw-bold text-dark mb-1">صورة المعاينة لمواقع التواصل (Social Open Graph Image URL)</label>
                                <p class="text-secondary small mb-3">الصورة التي تظهر عند مشاركة رابط الموقع على WhatsApp و Facebook و Twitter.</p>
                                <div class="row align-items-center g-3">
                                    <div class="col">
                                        <input type="file" name="og_image_file" class="form-control rounded-3 mb-2" accept="image/*">
                                        <input type="text" name="og_image" class="form-control rounded-3" placeholder="https://example.com/og-preview.jpg" value="{{ $settings['og_image'] }}">
                                    </div>
                                    @if(!empty($settings['og_image']))
                                        <div class="col-auto">
                                            <img src="{{ $settings['og_image'] }}" alt="OG Preview" class="rounded-3 border shadow-sm" style="max-height: 70px; max-width: 120px; object-fit: cover;">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- 3. Analytics Panel -->
                <div class="tab-pane fade" id="analytics-panel" role="tabpanel" aria-labelledby="analytics-tab">
                    <div class="col-lg-8">
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark mb-1">معرّف تتبع جوجل أناليتكس (Google Analytics ID)</h6>
                            <p class="text-secondary small">أدخل معرف القياس (G-XXXXXXX) لتتبع زيارات المرضى وسلوكهم على الموقع.</p>
                            <input type="text" name="google_analytics_id" class="form-control form-control-lg rounded-3" placeholder="G-XXXXXXXXXX" value="{{ $settings['google_analytics_id'] }}">
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold text-dark mb-1">معرّف تتبع ميتا بكسل (Meta Pixel ID)</h6>
                            <p class="text-secondary small">أدخل معرف بكسل فيسبوك لتتبع تحويلات الحجز وقياس فاعلية الإعلانات الموجهة.</p>
                            <input type="text" name="meta_pixel_id" class="form-control form-control-lg rounded-3" placeholder="123456789012345" value="{{ $settings['meta_pixel_id'] }}">
                        </div>
                    </div>
                </div>

                <!-- 4. Notifications Panel -->
                <div class="tab-pane fade" id="notifications-panel" role="tabpanel" aria-labelledby="notifications-tab">
                    <div class="col-lg-8">
                        <h6 class="fw-bold text-dark mb-3">تفضيلات إشعارات البريد الإلكتروني</h6>
                        
                        <div class="form-check form-switch text-start mb-3 p-3 bg-light rounded-4 border d-flex justify-content-between align-items-center">
                            <div>
                                <label class="form-check-label fw-bold text-dark" for="notifyNewSwitch">إشعار عند حجز جديد</label>
                                <div class="text-secondary small">إرسال إشعار تلقائي عند نجاح عملية حجز موعد جديد.</div>
                            </div>
                            <input class="form-check-input ms-0 fs-5" type="checkbox" role="switch" name="notify_new_booking" id="notifyNewSwitch" value="1" @if($settings['notify_new_booking'] === '1') checked @endif>
                        </div>

                        <div class="form-check form-switch text-start mb-4 p-3 bg-light rounded-4 border d-flex justify-content-between align-items-center">
                            <div>
                                <label class="form-check-label fw-bold text-dark" for="notifyCancelSwitch">إشعار عند إلغاء الحجز</label>
                                <div class="text-secondary small">إرسال إشعار فوري عند إلغاء حجز أو استرداد الأموال.</div>
                            </div>
                            <input class="form-check-input ms-0 fs-5" type="checkbox" role="switch" name="notify_cancellation" id="notifyCancelSwitch" value="1" @if($settings['notify_cancellation'] === '1') checked @endif>
                        </div>
                    </div>
                </div>


                {{-- 5. Payment Settings Panel --}}
                <div class="tab-pane fade" id="payment-panel" role="tabpanel" aria-labelledby="payment-tab">
                    <div class="row g-4">

                        {{-- ─── زين كاش ─────────────────────────────────────── --}}
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-header border-0 py-3 px-4 d-flex justify-content-between align-items-center"
                                     style="background: linear-gradient(135deg, #7c3aed 0%, #4c1d95 100%);">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-3 d-flex align-items-center justify-content-center text-white"
                                             style="width:44px;height:44px;background:rgba(255,255,255,0.15);font-size:1.4rem;">
                                            💜
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-white m-0">زين كاش (ZainCash)</h6>
                                            <span class="text-white opacity-75 small">دفع محلي عبر QR Code</span>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input fs-4" type="checkbox" role="switch"
                                               name="payment_zaincash_enabled" id="zaincashSwitch"
                                               @if($settings['payment_zaincash_enabled'] === '1') checked @endif
                                               style="cursor:pointer;">
                                        <label class="form-check-label text-white fw-bold small" for="zaincashSwitch">تفعيل</label>
                                    </div>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row g-4 align-items-start">
                                        {{-- QR Upload --}}
                                        <div class="col-md-5">
                                            <label class="form-label fw-bold text-dark mb-2">
                                                <i class="bi bi-qr-code me-1 text-purple"></i> صورة رمز QR
                                            </label>
                                            <div class="border-2 border-dashed rounded-4 p-3 text-center bg-light position-relative"
                                                 id="zaincash-qr-preview-box"
                                                 style="min-height:180px;border-color:#c4b5fd !important;border-style:dashed;">
                                                @if(!empty($settings['payment_zaincash_qr']))
                                                    <img id="zaincash-qr-img" src="{{ $settings['payment_zaincash_qr'] }}"
                                                         alt="ZainCash QR"
                                                         class="img-fluid rounded-3 shadow-sm"
                                                         style="max-height:150px;object-fit:contain;">
                                                @else
                                                    <div id="zaincash-qr-img" class="text-muted py-4">
                                                        <i class="bi bi-qr-code" style="font-size:3rem;opacity:.3;"></i>
                                                        <p class="small mt-2 mb-0">لم يتم رفع صورة QR بعد</p>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="mt-3">
                                                <label class="form-label small fw-bold">رفع صورة QR جديدة:</label>
                                                <input type="file" name="payment_zaincash_qr_file" class="form-control form-control-sm rounded-3"
                                                       accept="image/*"
                                                       onchange="previewQR(this, 'zaincash-qr-img')">
                                            </div>
                                            <div class="mt-2">
                                                <label class="form-label small fw-bold">أو رابط مباشر (URL):</label>
                                                <input type="text" name="payment_zaincash_qr" class="form-control form-control-sm rounded-3"
                                                       placeholder="https://..." value="{{ $settings['payment_zaincash_qr'] }}">
                                            </div>
                                        </div>
                                        {{-- Label / Instructions --}}
                                        <div class="col-md-7">
                                            <label class="form-label fw-bold text-dark mb-2">
                                                <i class="bi bi-chat-left-text me-1"></i> تعليمات الدفع للمريض
                                            </label>
                                            <textarea name="payment_zaincash_label" class="form-control rounded-3" rows="5"
                                                      placeholder="مثال: افتح تطبيق زين كاش، اسحب الرمز، وأرسل الإيصال...">{{ $settings['payment_zaincash_label'] }}</textarea>
                                            <p class="text-muted small mt-2">
                                                <i class="bi bi-info-circle me-1"></i>
                                                هذا النص سيظهر للمريض أسفل صورة QR أثناء الحجز.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ─── SuperKi ─────────────────────────────────────── --}}
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-header border-0 py-3 px-4 d-flex justify-content-between align-items-center"
                                     style="background: linear-gradient(135deg, #0284c7 0%, #075985 100%);">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-3 d-flex align-items-center justify-content-center text-white"
                                             style="width:44px;height:44px;background:rgba(255,255,255,0.15);font-size:1.4rem;">
                                            🔵
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-white m-0">SuperKi</h6>
                                            <span class="text-white opacity-75 small">دفع محلي عبر QR Code</span>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input fs-4" type="checkbox" role="switch"
                                               name="payment_superki_enabled" id="superkiSwitch"
                                               @if($settings['payment_superki_enabled'] === '1') checked @endif
                                               style="cursor:pointer;">
                                        <label class="form-check-label text-white fw-bold small" for="superkiSwitch">تفعيل</label>
                                    </div>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row g-4 align-items-start">
                                        {{-- QR Upload --}}
                                        <div class="col-md-5">
                                            <label class="form-label fw-bold text-dark mb-2">
                                                <i class="bi bi-qr-code me-1 text-info"></i> صورة رمز QR
                                            </label>
                                            <div class="border-2 border-dashed rounded-4 p-3 text-center bg-light"
                                                 style="min-height:180px;border-color:#7dd3fc !important;border-style:dashed;">
                                                @if(!empty($settings['payment_superki_qr']))
                                                    <img id="superki-qr-img" src="{{ $settings['payment_superki_qr'] }}"
                                                         alt="SuperKi QR"
                                                         class="img-fluid rounded-3 shadow-sm"
                                                         style="max-height:150px;object-fit:contain;">
                                                @else
                                                    <div id="superki-qr-img" class="text-muted py-4">
                                                        <i class="bi bi-qr-code" style="font-size:3rem;opacity:.3;"></i>
                                                        <p class="small mt-2 mb-0">لم يتم رفع صورة QR بعد</p>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="mt-3">
                                                <label class="form-label small fw-bold">رفع صورة QR جديدة:</label>
                                                <input type="file" name="payment_superki_qr_file" class="form-control form-control-sm rounded-3"
                                                       accept="image/*"
                                                       onchange="previewQR(this, 'superki-qr-img')">
                                            </div>
                                            <div class="mt-2">
                                                <label class="form-label small fw-bold">أو رابط مباشر (URL):</label>
                                                <input type="text" name="payment_superki_qr" class="form-control form-control-sm rounded-3"
                                                       placeholder="https://..." value="{{ $settings['payment_superki_qr'] }}">
                                            </div>
                                        </div>
                                        {{-- Label / Instructions --}}
                                        <div class="col-md-7">
                                            <label class="form-label fw-bold text-dark mb-2">
                                                <i class="bi bi-chat-left-text me-1"></i> تعليمات الدفع للمريض
                                            </label>
                                            <textarea name="payment_superki_label" class="form-control rounded-3" rows="5"
                                                      placeholder="مثال: افتح تطبيق SuperKi، اسحب الرمز، وأرسل الإيصال...">{{ $settings['payment_superki_label'] }}</textarea>
                                            <p class="text-muted small mt-2">
                                                <i class="bi bi-info-circle me-1"></i>
                                                هذا النص سيظهر للمريض أسفل صورة QR أثناء الحجز.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ─── فيزا وماستر كارد (Visa & MasterCard) ─────────────────────────────── --}}
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-header border-0 py-3 px-4 d-flex justify-content-between align-items-center"
                                     style="background: linear-gradient(135deg, #1e3a8a 0%, #172554 100%);">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-3 d-flex align-items-center justify-content-center text-white bg-white p-1"
                                             style="width:52px;height:40px;border-radius:10px;">
                                            <span class="fw-black text-primary" style="font-size:0.75rem; letter-spacing: -0.5px;">VISA</span>
                                            <span class="fw-black text-danger ms-1" style="font-size:0.75rem;">MC</span>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-white m-0">فيزا وماستر كارد (Visa & MasterCard)</h6>
                                            <span class="text-white opacity-75 small">بوابة دفع البطاقات الائتمانية والخصم المباشر</span>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input fs-4" type="checkbox" role="switch"
                                               name="payment_card_enabled" id="cardSwitch"
                                               @if(($settings['payment_card_enabled'] ?? '1') === '1') checked @endif
                                               style="cursor:pointer;">
                                        <label class="form-check-label text-white fw-bold small" for="cardSwitch">تفعيل</label>
                                    </div>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-dark">
                                                <i class="bi bi-key-fill me-1 text-primary"></i>
                                                المفتاح العام (Stripe / Gateway Public Key)
                                            </label>
                                            <p class="text-muted small">المفتاح المتاح لربط بوابة الدفع (مثل Stripe Publishable Key pk_live...).</p>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0">
                                                    <i class="bi bi-shield-lock text-primary"></i>
                                                </span>
                                                <input type="text" name="payment_card_key"
                                                       class="form-control rounded-end-3 font-monospace"
                                                       placeholder="pk_live_XXXXXXXXXXXXXXXX"
                                                       value="{{ $settings['payment_card_key'] ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-dark">
                                                <i class="bi bi-link-45deg me-1 text-primary"></i>
                                                رابط الدفع المباشر للبطاقات (اختياري)
                                            </label>
                                            <p class="text-muted small">إذا كان لديك رابط دفع مباشر عبر Gumroad أو Stripe Payment Link أو بوابة أخرى.</p>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0">
                                                    <i class="bi bi-globe text-primary"></i>
                                                </span>
                                                <input type="url" name="payment_card_link"
                                                       class="form-control rounded-end-3"
                                                       placeholder="https://buy.stripe.com/..."
                                                       value="{{ $settings['payment_card_link'] ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold text-dark">
                                                <i class="bi bi-currency-dollar me-1 text-primary"></i> العملة الأساسية للدفع
                                            </label>
                                            <select name="payment_card_currency" class="form-select rounded-3">
                                                <option value="USD" @selected(($settings['payment_card_currency'] ?? 'USD') === 'USD')>USD — دولار أمريكي</option>
                                                <option value="IQD" @selected(($settings['payment_card_currency'] ?? 'USD') === 'IQD')>IQD — دينار عراقي</option>
                                                <option value="EUR" @selected(($settings['payment_card_currency'] ?? 'USD') === 'EUR')>EUR — يورو</option>
                                                <option value="SAR" @selected(($settings['payment_card_currency'] ?? 'USD') === 'SAR')>SAR — ريال سعودي</option>
                                                <option value="AED" @selected(($settings['payment_card_currency'] ?? 'USD') === 'AED')>AED — درهم إماراتي</option>
                                            </select>
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label fw-bold text-dark">
                                                <i class="bi bi-chat-left-text me-1 text-primary"></i>
                                                تعليمات وتوضيحات الدفع للمريض
                                            </label>
                                            <input type="text" name="payment_card_instructions" class="form-control rounded-3"
                                                   placeholder="تظهر للمريض عند اختيار فيزا / ماستر كارد"
                                                   value="{{ $settings['payment_card_instructions'] ?? 'يمكنك الدفع مباشرة باستخدام أي بطاقة فيزا أو ماستر كارد صادرة محلياً أو دولياً بأمان وسرية تامة.' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>{{-- end row --}}
                </div>{{-- end payment-panel --}}

            </div>{{-- end tab-content --}}

            <!-- Submit Button -->
            <div class="mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-royal-primary px-5 py-3 rounded-pill fs-5 shadow">
                    <i class="bi bi-check-circle-fill me-2"></i> حفظ الإعدادات والتطبيقات
                </button>
            </div>

        </form>
    </div>
</div>

@push('scripts')
<script>
function previewQR(input, imgId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const target = document.getElementById(imgId);
            if (target) {
                // If it was a placeholder div, replace with img
                if (target.tagName === 'DIV') {
                    const img = document.createElement('img');
                    img.id = imgId;
                    img.src = e.target.result;
                    img.className = 'img-fluid rounded-3 shadow-sm';
                    img.style.maxHeight = '150px';
                    img.style.objectFit = 'contain';
                    target.replaceWith(img);
                } else {
                    target.src = e.target.result;
                }
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
@endsection
