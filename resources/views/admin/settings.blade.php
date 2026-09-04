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

                        <!-- Consultation Duration & Currency Settings -->
                        <div class="col-12 mt-3">
                            <div class="p-3 bg-white rounded-4 border shadow-sm">
                                <h6 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                                    <i class="bi bi-clock-history text-primary"></i>
                                    <span>مدة الاستشارة الافتراضية والعملة</span>
                                </h6>
                                <p class="text-secondary small mb-3">التحكم في المدة الافتراضية للجلسات والاستشارات والعملة المعتمدة للمنصة.</p>
                                
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-dark small">مدة الاستشارة الافتراضية (بالدقائق)</label>
                                        <div class="input-group">
                                            <input type="number" name="default_consultation_duration" class="form-control form-control-lg rounded-start-3 fw-bold" min="5" max="300" step="5" value="{{ $settings['default_consultation_duration'] ?? 45 }}" placeholder="45">
                                            <span class="input-group-text bg-light fw-bold text-secondary">دقيقة</span>
                                        </div>
                                        <div class="form-text text-muted small">المدة المعتمدة افتراضياً في نافذة الحجز وعند إضافة خدمات جديدة.</div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-dark small">كود العملة (ISO Code)</label>
                                        <input type="text" name="currency_code" class="form-control form-control-lg rounded-3 fw-bold text-uppercase" value="{{ $settings['currency_code'] ?? 'IQD' }}" placeholder="IQD">
                                        <div class="form-text text-muted small">مثال: IQD أو USD أو SAR</div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-dark small">رمز / مسمى العملة الظاهر للمستخدم</label>
                                        <input type="text" name="currency_symbol" class="form-control form-control-lg rounded-3 fw-bold" value="{{ $settings['currency_symbol'] ?? 'د.ع' }}" placeholder="د.ع">
                                        <div class="form-text text-muted small">مثال: د.ع أو $ أو ر.س</div>
                                    </div>
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
                    <div class="col-lg-9">
                        
                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                            <div>
                                <h5 class="fw-bold text-dark m-0"><i class="bi bi-envelope-check-fill text-primary me-2"></i> نظام إشعارات البريد الإلكتروني (Gmail / SMTP)</h5>
                                <p class="text-secondary small mb-0 mt-1">إرسال إشعارات فورية للطبيب عند الحجز والدفع، وإشعارات تأكيد المواعيد للمرضى.</p>
                            </div>
                        </div>

                        <!-- Master Switch -->
                        <div class="form-check form-switch text-start mb-3 p-3 bg-light rounded-4 border d-flex justify-content-between align-items-center">
                            <div>
                                <label class="form-check-label fw-bold text-dark" for="emailEnabledSwitch">تفعيل إرسال الإشعارات البريدية</label>
                                <div class="text-secondary small">تمكين نظام إرسال الإيميلات للمراجعين ولإدارة العيادة تلقائياً.</div>
                            </div>
                            <input class="form-check-input ms-0 fs-5" type="checkbox" role="switch" name="email_notifications_enabled" id="emailEnabledSwitch" value="1" @if(($settings['email_notifications_enabled'] ?? '1') === '1') checked @endif>
                        </div>

                        <!-- Doctor Recipient Email -->
                        <div class="card border-0 bg-light rounded-4 p-3 mb-3 border">
                            <label class="form-label fw-bold text-dark mb-1">
                                <i class="bi bi-person-badge text-primary me-1"></i> البريد الإلكتروني لاستقبال إشعارات الحجز (إيميل الطبيب)
                            </label>
                            <p class="text-secondary small mb-2">البريد الذي ستصل إليه تنبيهات الحجوزات الجديدة وإشعارات تحويل الأموال فورياً.</p>
                            <input type="email" name="notification_email" class="form-control form-control-lg rounded-3 bg-white" placeholder="dr.yonis@gmail.com" value="{{ $settings['notification_email'] ?? 'dr.yonis@gmail.com' }}">
                        </div>

                        <!-- Notification Events -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="form-check form-switch text-start p-3 bg-light rounded-4 border d-flex justify-content-between align-items-center h-100">
                                    <div>
                                        <label class="form-check-label fw-bold text-dark" for="notifyNewSwitch">إشعار حجز جديد</label>
                                        <div class="text-secondary small">تنبيه فوري عند تسجيل حجز جديد أو دفع محلي.</div>
                                    </div>
                                    <input class="form-check-input ms-0 fs-5" type="checkbox" role="switch" name="notify_new_booking" id="notifyNewSwitch" value="1" @if($settings['notify_new_booking'] === '1') checked @endif>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch text-start p-3 bg-light rounded-4 border d-flex justify-content-between align-items-center h-100">
                                    <div>
                                        <label class="form-check-label fw-bold text-dark" for="notifyCancelSwitch">إشعار إلغاء الحجز</label>
                                        <div class="text-secondary small">إرسال تنبيه في حال قام المريض بإلغاء موعده.</div>
                                    </div>
                                    <input class="form-check-input ms-0 fs-5" type="checkbox" role="switch" name="notify_cancellation" id="notifyCancelSwitch" value="1" @if($settings['notify_cancellation'] === '1') checked @endif>
                                </div>
                            </div>
                        </div>

                        <!-- Gmail SMTP Instructions Box -->
                        <div class="alert alert-info border-0 rounded-4 p-3 mb-4 text-start" style="background:#eff6ff;color:#1e40af;line-height:1.7;">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-google fs-4 text-primary flex-shrink-0 mt-1"></i>
                                <div>
                                    <strong class="d-block mb-1" style="font-size: 0.95rem;">ربط حساب Gmail عبر Google App Password:</strong>
                                    <span class="small">لاستخدام بريد Gmail الشخصي أو بريد العيادة للإرسال، تأكد من وضع المتغيرات التالية في ملف <code>.env</code> على الخادم:</span>
                                    <div class="bg-white p-2.5 rounded-3 border mt-2 font-monospace small text-dark" style="direction:ltr;text-align:left;">
                                        MAIL_MAILER=smtp<br>
                                        MAIL_HOST=smtp.gmail.com<br>
                                        MAIL_PORT=587<br>
                                        MAIL_USERNAME=your-email@gmail.com<br>
                                        MAIL_PASSWORD=xxxx-xxxx-xxxx-xxxx <span class="text-muted"># (Google App Password المكون من 16 حرف)</span><br>
                                        MAIL_ENCRYPTION=tls<br>
                                        MAIL_FROM_ADDRESS=your-email@gmail.com<br>
                                        MAIL_FROM_NAME="عيادة د. يونس المرشد"
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SMTP Test Email Box -->
                        <div class="card border-0 rounded-4 p-3.5 mb-2" style="background:linear-gradient(135deg,#f8fafc,#f1f5f9);border:1.5px dashed #cbd5e1 !important;">
                            <h6 class="fw-bold text-dark mb-1"><i class="bi bi-send-check text-success me-1"></i> اختبار اتصال البريد (Live SMTP Test)</h6>
                            <p class="text-secondary small mb-3">أدخل أي بريد إلكتروني تود تجربة الإرسال إليه للتأكد من أن السيرفر متصل بـ Gmail بنجاح:</p>
                            
                            <div class="input-group">
                                <input type="email" id="test-email-input" class="form-control rounded-end-3 bg-white" placeholder="test@example.com" value="{{ $settings['notification_email'] ?? 'dr.yonis@gmail.com' }}">
                                <button type="button" class="btn btn-primary rounded-start-3 px-3 fw-bold" id="send-test-btn" onclick="sendTestEmailAjax()">
                                    <i class="bi bi-paperplane me-1"></i> إرسال بريد تجريبي الآن
                                </button>
                            </div>
                            
                            <div id="test-email-feedback" class="mt-2 d-none"></div>
                        </div>

                    </div>
                </div>


                {{-- 5. Payment Settings Panel --}}
                <div class="tab-pane fade" id="payment-panel" role="tabpanel" aria-labelledby="payment-tab">
                    <div class="row g-4">

                        {{-- ─── العملة الرسمية للمنصة (Platform Currency & Symbol) ─── --}}
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-header border-0 py-3 px-4 d-flex justify-content-between align-items-center"
                                     style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-3 d-flex align-items-center justify-content-center text-white"
                                             style="width:44px;height:44px;background:rgba(255,255,255,0.15);font-size:1.4rem;">
                                            <i class="bi bi-coin text-warning"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-white m-0">العملة الرسمية للمنصة (Platform Currency)</h6>
                                            <span class="text-white opacity-75 small">العملة الافتراضية لعرض الأسعار وحسابات الدفع في الموقع، لوحة التحكم والتطبيق</span>
                                        </div>
                                    </div>
                                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold">
                                        العملة الحالية: {{ $settings['currency_code'] ?? 'IQD' }} ({{ $settings['currency_symbol'] ?? 'د.ع' }})
                                    </span>
                                </div>
                                <div class="card-body p-4 bg-light">
                                    <div class="row g-3 align-items-center">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-dark">
                                                <i class="bi bi-cash-stack text-success me-1"></i> كود العملة الأساسي (Currency Code)
                                            </label>
                                            <select name="currency_code" id="system_currency_code" class="form-select form-select-lg rounded-3 fw-bold" onchange="updateCurrencySymbolSuggestion(this.value)">
                                                <option value="IQD" @selected(($settings['currency_code'] ?? 'IQD') === 'IQD')>IQD — دينار عراقي (الافتراضي 🇮🇶)</option>
                                                <option value="USD" @selected(($settings['currency_code'] ?? '') === 'USD')>USD — دولار أمريكي ($ 🇺🇸)</option>
                                                <option value="SAR" @selected(($settings['currency_code'] ?? '') === 'SAR')>SAR — ريال سعودي (ر.س 🇸🇦)</option>
                                                <option value="AED" @selected(($settings['currency_code'] ?? '') === 'AED')>AED — درهم إماراتي (د.إ 🇦🇪)</option>
                                                <option value="EGP" @selected(($settings['currency_code'] ?? '') === 'EGP')>EGP — جنيه مصري (ج.م 🇪🇬)</option>
                                                <option value="EUR" @selected(($settings['currency_code'] ?? '') === 'EUR')>EUR — يورو (€ 🇪🇺)</option>
                                                <option value="KWD" @selected(($settings['currency_code'] ?? '') === 'KWD')>KWD — دينار كويتي (د.ك 🇰🇼)</option>
                                                <option value="QAR" @selected(($settings['currency_code'] ?? '') === 'QAR')>QAR — ريال قطري (ر.ق 🇶🇦)</option>
                                                <option value="OMR" @selected(($settings['currency_code'] ?? '') === 'OMR')>OMR — ريال عماني (ر.ع 🇴🇲)</option>
                                                <option value="BHD" @selected(($settings['currency_code'] ?? '') === 'BHD')>BHD — دينار بحريني (د.ب 🇧🇭)</option>
                                                <option value="JOD" @selected(($settings['currency_code'] ?? '') === 'JOD')>JOD — دينار أردني (د.أ 🇯🇴)</option>
                                            </select>
                                            <div class="form-text text-muted small mt-1">
                                                حدد العملة المعتمدة للمنصة بالكامل. سيتم تطبيقها فوراً على الموقع والـ API.
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-dark">
                                                <i class="bi bi-tag text-primary me-1"></i> رمز العملة المعروض للمرضى (Currency Symbol)
                                            </label>
                                            <input type="text" name="currency_symbol" id="system_currency_symbol"
                                                   class="form-control form-control-lg rounded-3 fw-bold"
                                                   placeholder="مثال: د.ع أو $ أو ر.س"
                                                   value="{{ $settings['currency_symbol'] ?? 'د.ع' }}">
                                            <div class="form-text text-muted small mt-1">
                                                هذا الرمز سيظهر بجوار الأسعار في واجهة الحجز، البطاقات، والفواتير (مثال: 50,000 د.ع).
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

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

function sendTestEmailAjax() {
    const input = document.getElementById('test-email-input');
    const btn = document.getElementById('send-test-btn');
    const feedback = document.getElementById('test-email-feedback');
    const email = input.value.trim();

    if (!email) {
        alert('يرجى كتابة بريد إلكتروني صالح للاختبار');
        return;
    }

    btn.disabled = true;
    const oldHtml = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> جارٍ الإرسال...';
    feedback.className = 'mt-2 d-none';

    fetch('{{ route("admin.settings.test-email") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ email: email })
    })
    .then(r => r.json().then(data => ({ status: r.status, body: data })))
    .then(({ status, body }) => {
        btn.disabled = false;
        btn.innerHTML = oldHtml;
        feedback.classList.remove('d-none');
        if (status === 200 && body.success) {
            feedback.className = 'alert alert-success border-0 rounded-3 p-2.5 small mt-2';
            feedback.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> ' + body.message;
        } else {
            feedback.className = 'alert alert-danger border-0 rounded-3 p-2.5 small mt-2';
            feedback.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i> ' + (body.message || 'فشل إرسال البريد');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = oldHtml;
        feedback.classList.remove('d-none');
    feedback.className = 'alert alert-danger border-0 rounded-3 p-2.5 small mt-2';
        feedback.innerHTML = '<i class="bi bi-x-circle-fill me-1"></i> حدث خطأ أثناء محاولة الاتصال بالسيرفر.';
    });
}

const defaultCurrencySymbols = {
    'IQD': 'د.ع',
    'USD': '$',
    'SAR': 'ر.س',
    'AED': 'د.إ',
    'EGP': 'ج.م',
    'EUR': '€',
    'KWD': 'د.ك',
    'QAR': 'ر.ق',
    'OMR': 'ر.ع',
    'BHD': 'د.ب',
    'JOD': 'د.أ'
};

function updateCurrencySymbolSuggestion(code) {
    const symInput = document.getElementById('system_currency_symbol');
    if (symInput && defaultCurrencySymbols[code]) {
        symInput.value = defaultCurrencySymbols[code];
    }
}
</script>
@endpush
@endsection
