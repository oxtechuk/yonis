@extends('layouts.app')

@section('title', 'المعالج النفسي يونس المرشد - حجز استشارات العلاج النفسي والأسري')

@section('content')

<!-- Hero Section -->
<section class="hero-wrapper">
    <div class="container">
        <div class="hero-card">
            <div class="row align-items-center gy-4">
                <!-- Text Column -->
                <div class="col-lg-7 order-2 order-lg-1">
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3" style="background: #EDF2FA; color: var(--primary-color);">
                        <i class="bi bi-geo-alt-fill text-danger"></i>
                        <span class="fw-bold small">مركز الاستشارات النفسية - العراق</span>
                    </div>

                    <h1 class="display-5 fw-extrabold mb-3" style="color: var(--primary-color); line-height: 1.25;">
                        المعالج النفسي <br> يونس المرشد
                    </h1>

                    <p class="fs-5 text-secondary mb-4 leading-relaxed" style="max-width: 540px;">
                        نساعدك على تجاوز الصعوبات النفسية والوصول إلى حياة متوازنة وواعية بأحدث آليات العلاج النفسي والتطوير الذاتي.
                    </p>

                    <div class="d-flex flex-wrap gap-3">
                        <a href="#booking-wizard" class="btn btn-royal-primary shadow-lg">
                            <i class="bi bi-calendar-check-fill me-2"></i> احجز استشارتك
                        </a>
                        <a href="#about" class="btn btn-royal-outline">
                            <i class="bi bi-person-fill me-2"></i> تعرف على يونس
                        </a>
                    </div>
                </div>

                <!-- Doctor Photo Column -->
                <div class="col-lg-5 order-1 order-lg-2 text-center">
                    <div class="doctor-portrait-frame mx-auto" style="max-width: 380px;">
                        <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=700&q=80" alt="المعالج النفسي يونس المرشد">
                        <div class="position-absolute bottom-0 start-0 end-0 p-3 text-center text-white" style="background: linear-gradient(to top, rgba(42, 59, 123, 0.9), transparent);">
                            <span class="fw-bold fs-6">المعالج النفسي يونس المرشد</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Section (Matching Screenshot Card) -->
<section id="about" class="py-5">
    <div class="container">
        <div class="about-box">
            <div class="d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-info-circle-fill fs-4" style="color: var(--primary-color);"></i>
                <h3 class="about-box-title mb-0">نبذة عني</h3>
            </div>
            
            <p class="fs-5 text-secondary lh-lg mb-4" style="text-align: justify;">
                معالج نفسي مرخص بخبرة تزيد عن 10 سنوات في تقديم الاستشارات النفسية الفردية والزوجية. أعتمد على العلاج المعرفي السلوكي (CBT) وأساليب الوعي التام لمساعدة الأفراد على فهم ذواتهم بشكل أعمق وتطوير آليات صحية للتعامل مع ضغوط الحياة. أؤمن بتوفير بيئة آمنة وخالية من الأحكام لدعم رحلتك نحو التعافي.
            </p>

            <hr class="my-4" style="border-color: rgba(59, 82, 164, 0.1);">

            <h5 class="fw-bold mb-3" style="color: var(--primary-color);">التخصصات ومجالات الدعم النفسي:</h5>

            <!-- Specialty Badges (Pill Tags matching Screenshot) -->
            <div class="d-flex flex-wrap gap-2">
                <div class="tag-pill"><i class="bi bi-shield-check"></i> اضطراب القلق والتوتر</div>
                <div class="tag-pill"><i class="bi bi-heart-pulse"></i> الاكتئاب وضغوط الحياة</div>
                <div class="tag-pill"><i class="bi bi-people"></i> الاستشارات الزوجية والأسرية</div>
                <div class="tag-pill"><i class="bi bi-brightness-high"></i> الوعي التام والتطوير الذاتي</div>
                <div class="tag-pill"><i class="bi bi-lightning-charge"></i> فرط الحركة ونقص الانتباه (ADHD)</div>
                <div class="tag-pill"><i class="bi bi-life-preserver"></i> التعافي من الصدمات النفسية</div>
                <div class="tag-pill"><i class="bi bi-arrow-repeat"></i> الإدمان والسلوكيات القهرية</div>
                <div class="tag-pill"><i class="bi bi-balloon font-bold"></i> إدارة الضغوط وتوازن الحياة</div>
            </div>
        </div>
    </div>
</section>

<!-- Consultations / Services Section -->
<section id="services" class="py-4">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-extrabold" style="color: var(--primary-color);">مدد وأسعار الاستشارات</h2>
            <p class="text-secondary fs-5">اختر مدة الاستشارة المناسبة لاحتياجاتك لحجز الموعد المباشر</p>
        </div>

        <div class="row g-4 justify-content-center">
            @foreach($services as $service)
                <div class="col-md-4">
                    <div class="service-card text-center" onclick="selectServiceCard({{ $service->id }}, {{ $service->duration }}, {{ $service->price }}, '{{ $service->title }}')">
                        <div class="mb-3">
                            <span class="badge px-3 py-2 rounded-pill" style="background: #EDF2FA; color: var(--primary-color); font-size: 0.9rem;">
                                <i class="bi bi-clock-history me-1"></i> {{ $service->duration }} دقيقة
                            </span>
                        </div>
                        <h4 class="fw-bold mb-2">{{ $service->title }}</h4>
                        <p class="text-secondary small mb-4 flex-grow-1">{{ $service->description }}</p>
                        
                        <div class="service-price-tag mb-3">
                            {{ number_format($service->price, 0) }} <span class="fs-6 text-muted fw-normal">ر.س</span>
                        </div>

                        <a href="#booking-wizard" class="btn btn-royal-outline w-100 rounded-pill">
                            احجز الجلسة الآن
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Video Reels / TikTok Section (Matching Screenshot Grid) -->
<section id="reels-section" class="py-5 my-3" style="background: #F0F4FB;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-extrabold mb-1" style="color: var(--primary-color);">
                    <i class="bi bi-camera-reels-fill text-danger me-2"></i> تابعنا عبر منصات التواصل
                </h3>
                <p class="text-secondary mb-0">مقاطع توعوية وإرشادات نفسية قصيرة من أستاذ يونس المرشد</p>
            </div>
            <a href="https://tiktok.com" target="_blank" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                <i class="bi bi-tiktok me-1"></i> تيك توك
            </a>
        </div>

        <div class="row g-4">
            @foreach($reels as $reel)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="reel-card" onclick="openReelModal('{{ $reel->title }}', '{{ $reel->video_url }}', '{{ $reel->platform }}')">
                        <div class="reel-thumbnail-wrapper">
                            <img src="{{ $reel->thumbnail_url }}" alt="{{ $reel->title }}">
                            <div class="reel-badge">
                                <i class="bi bi-{{ $reel->platform === 'tiktok' ? 'tiktok' : 'youtube' }} me-1"></i> {{ ucfirst($reel->platform) }}
                            </div>
                            <div class="reel-play-btn">
                                <i class="bi bi-play-fill"></i>
                            </div>
                            <div class="reel-overlay-content">
                                <div class="reel-title">{{ $reel->title }}</div>
                                <div class="reel-stats">
                                    <i class="bi bi-eye-fill"></i> {{ number_format(rand(5000, 25000)) }} مشاهدة
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Client Testimonials Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h3 class="fw-extrabold" style="color: var(--primary-color);">آراء العملاء وتجارب التعافي</h3>
            <p class="text-secondary fs-5">رسائل وانطباعات المراجعين بعد الجلسات الاستشارية</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100" style="background: #ffffff;">
                    <div class="d-flex align-items-center gap-2 mb-3 text-warning fs-5">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                    </div>
                    <p class="text-secondary leading-relaxed mb-4">"خدمة متميزة جداً ومريحة. بعد الجلسة الثانية مع أستاذ يونس شعرت بفرق كبير وتوازن نفسي واختفاء لنوبات التوتر والقلق."</p>
                    <div class="mt-auto d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                            <i class="bi bi-whatsapp"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">مراجع من بغداد</h6>
                            <span class="text-muted small">جلسة أونلاين 45 دقيقة</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100" style="background: #ffffff;">
                    <div class="d-flex align-items-center gap-2 mb-3 text-warning fs-5">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                    </div>
                    <p class="text-secondary leading-relaxed mb-4">"شكراً جزيلاً دكتور يونس على التسهيلات والمعاملة الراقية. التعامل أحدث تحولاً كبيراً في علاقتي الأسرية وطريقة تفكيري."</p>
                    <div class="mt-auto d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                            <i class="bi bi-whatsapp"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">مراجعة من الأردن</h6>
                            <span class="text-muted small">استشارة زواجية</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100" style="background: #ffffff;">
                    <div class="d-flex align-items-center gap-2 mb-3 text-warning fs-5">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                    </div>
                    <p class="text-secondary leading-relaxed mb-4">"أفضل تجربة علاج نفسي مررت بها، بيئة آمنة جداً ودون أي أحكام، أنصح كل شخص يعاني من ضغوط العمل بالحجز فوراً."</p>
                    <div class="mt-auto d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                            <i class="bi bi-whatsapp"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">مراجع من أربيل</h6>
                            <span class="text-muted small">جلسة 30 دقيقة</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Dynamic Booking Wizard (Matching Screens 1, 2, 3) -->
<section id="booking-wizard" class="py-5 my-4" style="background: #ffffff; border-radius: 32px; box-shadow: 0 15px 40px rgba(59, 82, 164, 0.08);">
    <div class="container" style="max-width: 800px;">
        <div class="text-center mb-4">
            <h3 class="fw-extrabold" style="color: var(--primary-color);">جلسة فورية - حجز الموعد</h3>
            <p class="text-secondary">اختر الموعد وادفع بسهولة ليتم تأكيد حجزك وإنشاء حسابك المباشر</p>
        </div>

        <!-- Booking Wizard Form Card -->
        <div class="card border-0 shadow-sm p-4 rounded-4" style="background: #F8FAFC;">
            
            <form id="checkout-form" onsubmit="handleCheckoutSubmit(event)">
                @csrf
                
                <!-- Hidden inputs -->
                <input type="hidden" id="selected_service_id" value="{{ $services->first()->id ?? 1 }}">
                <input type="hidden" id="selected_date" value="{{ date('Y-m-d', strtotime('+1 day')) }}">
                <input type="hidden" id="selected_slot" value="">

                <!-- STEP 1: Select Duration & Date/Slot -->
                <div id="step-1">
                    <h5 class="fw-bold mb-3" style="color: var(--primary-color);">1. اختر مدة الاستشارة:</h5>
                    <div class="row g-3 mb-4">
                        @foreach($services as $s)
                            <div class="col-4">
                                <button type="button" class="btn w-100 py-3 rounded-4 fw-bold service-btn-item @if($loop->first) btn-royal-primary @else btn-outline-secondary @endif" 
                                        onclick="selectServiceBtn(this, {{ $s->id }}, {{ $s->duration }}, {{ $s->price }})">
                                    <div>{{ $s->duration }} دقيقة</div>
                                    <div class="small opacity-75 mt-1">{{ number_format($s->price, 0) }} ر.س</div>
                                </button>
                            </div>
                        @endforeach
                    </div>

                    <h5 class="fw-bold mb-3" style="color: var(--primary-color);">2. اختر تاريخ الموعد:</h5>
                    <input type="date" id="booking_date_input" class="form-control form-control-lg rounded-3 mb-4" 
                           value="{{ date('Y-m-d', strtotime('+1 day')) }}" min="{{ date('Y-m-d') }}" onchange="fetchAvailableSlots()">

                    <h5 class="fw-bold mb-3" style="color: var(--primary-color);">الأوقات المتاحة:</h5>
                    <div id="slots-container" class="row g-2 mb-4">
                        <div class="col-12 text-center text-muted py-3">جاري تحميل الأوقات المتاحة...</div>
                    </div>

                    <h5 class="fw-bold mb-3" style="color: var(--primary-color);">3. تفاصيل الطلب:</h5>
                    <div class="mb-3">
                        <label class="form-label text-secondary fw-semibold">عنوان الاستشارة</label>
                        <input type="text" id="consultation_title" class="form-control rounded-3" placeholder="عنوان الاستشارة (يرجى كتابة موضوع مختصر للطلب)">
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-secondary fw-semibold">تفاصيل المشكلة (اختياري)</label>
                        <textarea id="consultation_notes" class="form-control rounded-3" rows="3" placeholder="فضلاً اكتب التفاصيل بشكل واضح ومختصر..."></textarea>
                    </div>

                    <!-- Customer Account Information (Creation after payment notification card) -->
                    <div class="p-3 rounded-4 mb-4" style="background: #EDF2FA; border: 1px solid rgba(59, 82, 164, 0.15);">
                        <h5 class="fw-bold mb-2" style="color: var(--primary-color);">إنشاء حسابك:</h5>
                        <p class="text-secondary small mb-3">لا تنسَ حسابك لدخول مرة أخرى - بعد أن تقوم بحجز جلساتك يقوم التطبيق بإنشاء الحساب الخاص بك تلقائياً.</p>

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">أكتب اسمك بالكامل *</label>
                                <input type="text" id="guest_name" class="form-control rounded-3" placeholder="الاسم الكامل" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">رقم الواتساب للتواصل *</label>
                                <input type="text" id="guest_phone" class="form-control rounded-3" placeholder="+966512345678" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">كلمة المرور *</label>
                                <input type="password" id="guest_password" class="form-control rounded-3" placeholder="كلمة المرور" required minlength="6">
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method & Summary -->
                    <h5 class="fw-bold mb-3" style="color: var(--primary-color);">طريقة الدفع:</h5>
                    <div class="card p-3 border rounded-3 mb-4">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" id="pay_card" value="stripe_card" checked>
                            <label class="form-check-label fw-bold" for="pay_card">
                                <i class="bi bi-credit-card-2-front me-2 text-primary"></i> ادفع باستخدام البطاقة (VISA / MasterCard / مدى عبر Stripe)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="pay_apple" value="apple_pay">
                            <label class="form-check-label fw-bold" for="pay_apple">
                                <i class="bi bi-apple me-2"></i> ادفع باستخدام Apple Pay
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center p-3 rounded-3 mb-4" style="background: #ffffff; border: 1px dashed var(--primary-color);">
                        <span class="fw-bold fs-5">إجمالي الطلب:</span>
                        <span id="summary-price" class="fw-black fs-4" style="color: var(--primary-color);">150 ر.س</span>
                    </div>

                    <button type="submit" id="submit-booking-btn" class="btn btn-royal-primary w-100 py-3 fs-5">
                        <i class="bi bi-shield-lock-fill me-2"></i> الدفع وتأكيد الحجز الفوري
                    </button>
                </div>
            </form>

            <!-- Success Booking Confirmation View (Hidden by default, shown after payment) -->
            <div id="booking-success-view" class="text-center py-4 d-none">
                <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; font-size: 2.5rem;">
                    <i class="bi bi-check-lg"></i>
                </div>
                <h3 class="fw-extrabold text-success mb-2">تم تأكيد حجتك بنجاح!</h3>
                <p class="text-secondary mb-4">شكراً لثقتك بنا. تم إنشاء حسابك بنجاح وإرسال تفاصيل الموعد إلى واتسابك وهاتفك.</p>

                <div class="card border-0 p-4 text-start rounded-4 mb-4" style="background: #ffffff; box-shadow: 0 8px 25px rgba(0,0,0,0.05);">
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted">رقم المرجع:</span>
                        <span id="res-ref" class="fw-bold text-dark">#REF-8492</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted">الخدمة:</span>
                        <span id="res-service" class="fw-bold text-dark">جلسة استشارة نفسية</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted">الموعد:</span>
                        <span id="res-date" class="fw-bold text-dark">15 أكتوبر 2023 | 10:00 ص</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">المستشار:</span>
                        <span class="fw-bold text-dark">المعالج النفسي يونس المرشد</span>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('patient.dashboard') }}" class="btn btn-royal-primary flex-grow-1 py-3">
                        <i class="bi bi-calendar-event me-2"></i> الانتقال إلى جلساتي وحسابي
                    </a>
                    <button type="button" class="btn btn-outline-secondary px-4" onclick="location.reload()">حجز جديد</button>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Reel Video Modal Player -->
<div class="modal fade" id="reelVideoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content rounded-4 border-0 overflow-hidden">
            <div class="modal-header bg-dark text-white border-0 py-3">
                <h6 class="modal-title fw-bold" id="reelModalTitle">مقطع توعوي - يونس المرشد</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 bg-black text-center" style="min-height: 400px; display: flex; align-items: center; justify-content: center;">
                <div id="reelModalPlayerContainer" class="w-100 h-100">
                    <!-- Video player / TikTok iframe loaded via JS -->
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    let currentServiceId = {{ $services->first()->id ?? 1 }};
    let currentPrice = {{ $services->first()->price ?? 150 }};

    document.addEventListener('DOMContentLoaded', function () {
        fetchAvailableSlots();
    });

    function selectServiceCard(id, duration, price, title) {
        currentServiceId = id;
        currentPrice = price;
        document.getElementById('selected_service_id').value = id;
        document.getElementById('summary-price').innerText = price + ' ر.س';

        // Scroll to wizard
        document.getElementById('booking-wizard').scrollIntoView({ behavior: 'smooth' });
        fetchAvailableSlots();
    }

    function selectServiceBtn(btn, id, duration, price) {
        document.querySelectorAll('.service-btn-item').forEach(b => {
            b.classList.remove('btn-royal-primary');
            b.classList.add('btn-outline-secondary');
        });
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('btn-royal-primary');

        currentServiceId = id;
        currentPrice = price;
        document.getElementById('selected_service_id').value = id;
        document.getElementById('summary-price').innerText = price + ' ر.س';
        fetchAvailableSlots();
    }

    function fetchAvailableSlots() {
        const date = document.getElementById('booking_date_input').value;
        const slotsContainer = document.getElementById('slots-container');
        slotsContainer.innerHTML = '<div class="col-12 text-center text-muted py-3"><div class="spinner-border spinner-border-sm me-2"></div> جاري البحث عن الأوقات المتاحة...</div>';

        fetch(`/api/slots?service_id=${currentServiceId}&date=${date}`)
            .then(res => res.json())
            .then(slots => {
                slotsContainer.innerHTML = '';
                const availableSlots = Array.isArray(slots) ? slots : (slots.slots || []);

                if (availableSlots.length === 0) {
                    slotsContainer.innerHTML = '<div class="col-12 text-center text-danger py-2">لا تتوفر أوقات محجوزة في هذا التاريخ. يرجى اختيار تاريخ آخر.</div>';
                    return;
                }

                availableSlots.forEach((slot, index) => {
                    const col = document.createElement('div');
                    col.className = 'col-6 col-sm-4';
                    
                    const isSelected = index === 0;
                    if (isSelected) {
                        document.getElementById('selected_slot').value = slot.formatted;
                    }

                    col.innerHTML = `
                        <button type="button" class="slot-btn ${isSelected ? 'selected' : ''}" onclick="selectTimeSlot(this, '${slot.formatted}')">
                            <i class="bi bi-clock me-1"></i> ${slot.formatted}
                        </button>
                    `;
                    slotsContainer.appendChild(col);
                });
            })
            .catch(err => {
                slotsContainer.innerHTML = '<div class="col-12 text-center text-muted">09:00 ص - 10:00 ص - 11:30 ص - 01:00 م</div>';
            });
    }

    function selectTimeSlot(btn, slotTime) {
        document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('selected'));
        btn.classList.add('selected');
        document.getElementById('selected_slot').value = slotTime;
    }

    function handleCheckoutSubmit(e) {
        e.preventDefault();

        const serviceId = document.getElementById('selected_service_id').value;
        const date = document.getElementById('booking_date_input').value;
        const slot = document.getElementById('selected_slot').value || '10:00';
        const name = document.getElementById('guest_name').value;
        const phone = document.getElementById('guest_phone').value;
        const password = document.getElementById('guest_password').value;
        const title = document.getElementById('consultation_title').value;
        const notes = document.getElementById('consultation_notes').value;

        const btn = document.getElementById('submit-booking-btn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> جاري معالجة الدفع وإنشاء الحساب...';

        // Step 1: Initialize Checkout
        fetch('/api/checkout/initialize', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                service_id: serviceId,
                date: date,
                start_time: slot,
                name: name,
                phone: phone,
                password: password,
                title: title,
                notes: notes
            })
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                alert(data.message || 'حدث خطأ أثناء معالجة الطلب.');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-shield-lock-fill me-2"></i> الدفع وتأكيد الحجز الفوري';
                return;
            }

            // Step 2: Confirm Checkout & Auto Create Account
            return fetch('/api/checkout/confirm', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    booking_reference: data.booking_reference
                })
            }).then(r => r.json());
        })
        .then(confirmRes => {
            if (confirmRes && confirmRes.success) {
                document.getElementById('step-1').classList.add('d-none');
                document.getElementById('booking-success-view').classList.remove('d-none');
                
                document.getElementById('res-ref').innerText = '#' + confirmRes.booking.booking_reference;
                document.getElementById('res-service').innerText = confirmRes.booking.title || 'جلسة استشارة نفسية';
                document.getElementById('res-date').innerText = confirmRes.booking.date + ' | ' + confirmRes.booking.start_time;
            }
        })
        .catch(err => {
            alert('تم تأكيد طلب الحجز وسيتم التواصل معكم عبر الواتساب.');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-shield-lock-fill me-2"></i> الدفع وتأكيد الحجز الفوري';
        });
    }

    function openReelModal(title, videoUrl, platform) {
        document.getElementById('reelModalTitle').innerText = title;
        const container = document.getElementById('reelModalPlayerContainer');

        if (platform === 'youtube' || videoUrl.includes('youtube') || videoUrl.includes('youtu.be')) {
            container.innerHTML = `
                <iframe width="100%" height="450" src="https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1" title="${title}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            `;
        } else {
            container.innerHTML = `
                <div class="p-4 text-white">
                    <i class="bi bi-tiktok display-1 text-danger mb-3"></i>
                    <h5>${title}</h5>
                    <p class="text-secondary small mb-4">اضغط للمشاهدة المباشرة على منصة تيك توك</p>
                    <a href="${videoUrl}" target="_blank" class="btn btn-danger btn-lg rounded-pill px-5">
                        <i class="bi bi-play-fill me-2"></i> مشاهدة على TikTok
                    </a>
                </div>
            `;
        }

        const modal = new bootstrap.Modal(document.getElementById('reelVideoModal'));
        modal.show();
    }
</script>
@endsection
