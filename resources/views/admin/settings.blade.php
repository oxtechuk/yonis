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

            </div>

            <!-- Submit Button -->
            <div class="mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-royal-primary px-5 py-3 rounded-pill fs-5 shadow">
                    <i class="bi bi-check-circle-fill me-2"></i> حفظ الإعدادات والتطبيقات
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
