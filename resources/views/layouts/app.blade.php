<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- ── SEO Core Meta ─────────────────────────────────────── --}}
    @php
        $primaryColor = \App\Models\Setting::get('primary_color', '#3B52A4');
        $secondaryColor = \App\Models\Setting::get('secondary_color', '#1e3a8a');
        $siteLogo = \App\Models\Setting::get('site_logo', '');
        $defaultMetaDesc = \App\Models\Setting::get('meta_description', 'احجز استشارتك النفسية الآن مع المعالج يونس المرشد. جلسات فردية وزوجية وأسرية بخبرة أكثر من 10 سنوات. حجز أونلاين عبر شات أو صوت أو فيديو، أو في العيادة.');
        $defaultMetaKeys = \App\Models\Setting::get('meta_keywords', 'معالج نفسي, استشارة نفسية, علاج نفسي, يونس المرشد, حجز موعد نفسي, اكتئاب, قلق, علاج زوجي');
        $isAr = app()->getLocale() === 'ar';
    @endphp

    <title>@yield('title', 'المعالج النفسي يونس المرشد - استشارات نفسية وأسرية متخصصة')</title>
    <meta name="description" content="@yield('meta_description', $defaultMetaDesc)">
    <meta name="keywords" content="@yield('meta_keywords', $defaultMetaKeys)">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- ── Google Search Console Verification ──────────────── --}}
    @php $googleVerify = \App\Models\Setting::get('google_site_verification', ''); @endphp
    @if(!empty($googleVerify))
        <meta name="google-site-verification" content="{{ $googleVerify }}">
    @endif

    {{-- ── Open Graph (Facebook / WhatsApp Preview) ─────────── --}}
    @php $ogImg = \App\Models\Setting::get('og_image', ''); @endphp
    <meta property="og:type" content="website">
    <meta property="og:locale" content="{{ $isAr ? 'ar_AR' : 'en_US' }}">
    <meta property="og:site_name" content="يونس المرشد - للعلاج النفسي">
    <meta property="og:title" content="@yield('title', 'المعالج النفسي يونس المرشد')">
    <meta property="og:description" content="@yield('meta_description', $defaultMetaDesc)">
    <meta property="og:url" content="{{ url()->current() }}">
    @if(!empty($ogImg))
        <meta property="og:image" content="{{ $ogImg }}">
    @endif

    {{-- ── Twitter Card ──────────────────────────────────────── --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'المعالج النفسي يونس المرشد')">
    <meta name="twitter:description" content="@yield('meta_description', $defaultMetaDesc)">

    {{-- ── Schema.org: Physician + MedicalClinic ─────────────── --}}
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@graph": [
            {
                "@@type": "Physician",
                "name": "{{ \App\Models\Setting::get('doctor_name', 'يونس المرشد') }}",
                "description": "معالج نفسي مرخص بخبرة أكثر من 10 سنوات في الاستشارات النفسية والعلاج المعرفي السلوكي",
                "url": "{{ url('/') }}",
                "medicalSpecialty": "Psychiatry",
                "availableService": [
                    {"@@type": "MedicalTherapy", "name": "استشارة نفسية فردية"},
                    {"@@type": "MedicalTherapy", "name": "استشارة زوجية وأسرية"},
                    {"@@type": "MedicalTherapy", "name": "علاج الاكتئاب والقلق"}
                ]
            },
            {
                "@@type": "MedicalClinic",
                "name": "عيادة يونس المرشد للاستشارات النفسية",
                "url": "{{ url('/') }}",
                "description": "عيادة متخصصة في الاستشارات النفسية الفردية والأسرية والزوجية"
            }
        ]
    }
    </script>

    {{-- ── Google Analytics ──────────────────────────────────── --}}
    @php $gaId = \App\Models\Setting::get('google_analytics_id'); @endphp
    @if(!empty($gaId) && !str_contains($gaId, 'placeholder'))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $gaId }}');
        </script>
    @endif

    {{-- ── Meta Pixel ─────────────────────────────────────────── --}}
    @php $metaId = \App\Models\Setting::get('meta_pixel_id'); @endphp
    @if(!empty($metaId) && !str_contains($metaId, 'placeholder'))
        <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{{ $metaId }}');
            fbq('track', 'PageView');
        </script>
    @endif

    {{-- ── Fonts: Tajawal & Inter ────────────────────────────── --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Inter:wght@400;600;700;800&family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">

    {{-- ── Bootstrap 5.3.3 CSS ───────────────────────────────── --}}
    @if($isAr)
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    @else
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    @endif

    {{-- ── Bootstrap Icons ────────────────────────────────────── --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- ── Swiper (for carousels) ──────────────────────────────── --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    {{-- ── Custom CSS ─────────────────────────────────────────── --}}
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    <style>
        :root {
            --primary-color: {{ $primaryColor }};
            --primary-dark: {{ $secondaryColor }};
            --bs-primary: {{ $primaryColor }};
            --bs-primary-rgb: 64, 85, 165;
            --primary-gradient: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }});
        }
    </style>

    {{-- ── Sitemap ──────────────────────────────────────────────── --}}
    <link rel="sitemap" type="application/xml" href="/sitemap.xml">

    @yield('styles')
</head>
<body class="d-flex flex-column min-vh-100 {{ $isAr ? 'rtl-mode' : 'ltr-mode' }}">

    {{-- ═══ LUXURY FLOATING GLASSMORPHIC NAVBAR ════════════════════ --}}
    <header class="navbar-floating-wrapper" id="mainHeaderWrapper">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-floating-capsule p-0">
                <a class="navbar-brand d-flex align-items-center gap-2 me-0 me-lg-2" href="{{ route('home') }}">
                    @if(!empty($siteLogo))
                        <img src="{{ $siteLogo }}" alt="Logo" style="max-height: 38px; border-radius: 6px;">
                    @else
                        <div class="navbar-logo-circle" style="width:36px; height:36px; font-size:1rem;"><i class="bi bi-heart-pulse-fill"></i></div>
                    @endif
                    <div class="d-flex flex-column text-start">
                        <span class="fw-black lh-1" style="color: var(--primary-color); font-size: 1.1rem;">{{ $isAr ? 'يونس المرشد' : 'Yonis Al-Murshid' }}</span>
                        <span class="text-secondary small" style="font-size: 0.62rem; letter-spacing: 0.3px;">{{ $isAr ? 'للعلاج النفسي والتطوير الذاتي' : 'Psychological Therapy Clinic' }}</span>
                    </div>
                </a>

                <button class="navbar-toggler border-0 shadow-none px-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    {{-- Centered Nav Links --}}
                    <ul class="navbar-nav mx-auto align-items-lg-center gap-1 my-2 my-lg-0">
                        <li class="nav-item"><a class="nav-link nav-link-luxury @if(Route::is('home')) active @endif" href="{{ Route::is('home') ? '#hero' : route('home') }}">{{ __('messages.home') }}</a></li>
                        <li class="nav-item"><a class="nav-link nav-link-luxury" href="{{ Route::is('home') ? '#about' : route('home') . '#about' }}">{{ __('messages.about') }}</a></li>
                        <li class="nav-item"><a class="nav-link nav-link-luxury" href="{{ Route::is('home') ? '#gallery' : route('home') . '#gallery' }}">{{ __('messages.gallery_label') }}</a></li>
                        <li class="nav-item"><a class="nav-link nav-link-luxury" href="{{ Route::is('home') ? '#services' : route('home') . '#services' }}">{{ __('messages.sessions') }}</a></li>
                        <li class="nav-item"><a class="nav-link nav-link-luxury" href="{{ Route::is('home') ? '#reels-section' : route('home') . '#reels-section' }}">{{ __('messages.videos') }}</a></li>
                    </ul>

                    {{-- Left Action Buttons --}}
                    <div class="d-flex align-items-center gap-2 ms-0 ms-lg-2">
                        {{-- Quick Booking Button --}}
                        <button type="button" class="btn btn-nav-booking-luxury d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#bookingModal">
                            <i class="bi bi-calendar-check"></i> {{ __('messages.book_now') }}
                        </button>

                        {{-- Language Switcher (Icon Only) --}}
                        <a href="{{ route('lang.switch', $isAr ? 'en' : 'ar') }}" class="btn-nav-icon-luxury" title="{{ $isAr ? 'English' : 'العربية' }}">
                            <i class="bi bi-globe2"></i>
                        </a>

                        {{-- Account / User Menu Dropdown --}}
                        @auth
                            <div class="dropdown d-inline-block">
                                <button class="btn-nav-icon-account border-0 shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="{{ Auth::user()->name }}">
                                    <i class="bi bi-person-check-fill text-primary"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-2 mt-2" style="min-width: 220px; z-index: 1060;">
                                    <li class="px-3 py-2 border-bottom mb-1">
                                        <div class="fw-bold text-dark small">{{ Auth::user()->name }}</div>
                                        <div class="text-secondary" style="font-size: 0.75rem;">{{ Auth::user()->phone ?? Auth::user()->email }}</div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item rounded-3 py-2 small fw-bold d-flex align-items-center gap-2" href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('patient.dashboard') }}">
                                            <i class="bi bi-person-workspace text-primary"></i>
                                            <span>{{ Auth::user()->isAdmin() ? 'لوحة تحكم الإدارة' : 'ملفي الطبي ومواعيدي' }}</span>
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider my-1">
                                    </li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="dropdown-item rounded-3 py-2 small fw-bold text-danger d-flex align-items-center gap-2">
                                                <i class="bi bi-box-arrow-right"></i>
                                                <span>تسجيل الخروج</span>
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="btn-nav-icon-account" title="{{ __('messages.login') }}">
                                <i class="bi bi-person-fill"></i>
                            </a>
                        @endauth
                    </div>
                </div>
            </nav>
        </div>
    </header>

    {{-- ═══ Floating Luxury Toast Notifications ═══════════════ --}}
    @if(session('success') || session('error') || session('warning') || session('info') || $errors->any())
        <div class="luxury-toast-container" id="luxuryToastContainer">
            @if(session('success'))
                <div class="luxury-toast toast-success" role="alert">
                    <div class="toast-icon-wrapper">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="toast-content">
                        <div class="toast-title">تمت العملية بنجاح</div>
                        <div class="toast-message">{{ session('success') }}</div>
                    </div>
                    <button type="button" class="toast-close-btn" onclick="dismissToast(this.closest('.luxury-toast'))" aria-label="إغلاق">
                        <i class="bi bi-x-lg"></i>
                    </button>
                    <div class="toast-progress"><div class="toast-progress-bar"></div></div>
                </div>
            @endif

            @if(session('error'))
                <div class="luxury-toast toast-danger" role="alert">
                    <div class="toast-icon-wrapper">
                        <i class="bi bi-exclamation-octagon-fill"></i>
                    </div>
                    <div class="toast-content">
                        <div class="toast-title">حدث خطأ</div>
                        <div class="toast-message">{{ session('error') }}</div>
                    </div>
                    <button type="button" class="toast-close-btn" onclick="dismissToast(this.closest('.luxury-toast'))" aria-label="إغلاق">
                        <i class="bi bi-x-lg"></i>
                    </button>
                    <div class="toast-progress"><div class="toast-progress-bar"></div></div>
                </div>
            @endif

            @if(session('warning'))
                <div class="luxury-toast toast-warning" role="alert">
                    <div class="toast-icon-wrapper">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div class="toast-content">
                        <div class="toast-title">تنبيه</div>
                        <div class="toast-message">{{ session('warning') }}</div>
                    </div>
                    <button type="button" class="toast-close-btn" onclick="dismissToast(this.closest('.luxury-toast'))" aria-label="إغلاق">
                        <i class="bi bi-x-lg"></i>
                    </button>
                    <div class="toast-progress"><div class="toast-progress-bar"></div></div>
                </div>
            @endif

            @if($errors->any())
                <div class="luxury-toast toast-danger" role="alert">
                    <div class="toast-icon-wrapper">
                        <i class="bi bi-shield-fill-x"></i>
                    </div>
                    <div class="toast-content">
                        <div class="toast-title">يرجى مراجعة البيانات</div>
                        <div class="toast-message">
                            <ul class="m-0 p-0 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="toast-close-btn" onclick="dismissToast(this.closest('.luxury-toast'))" aria-label="إغلاق">
                        <i class="bi bi-x-lg"></i>
                    </button>
                    <div class="toast-progress"><div class="toast-progress-bar"></div></div>
                </div>
            @endif
        </div>
    @endif

    {{-- ═══ Main Content ════════════════════════════════════════ --}}
    <main class="flex-grow-1">
        @yield('content')
    </main>

    {{-- ═══ Footer ═════════════════════════════════════════════ --}}
    {{-- ═══ Footer ═════════════════════════════════════════════ --}}
    <footer class="site-footer">
        <div class="container">
            <div class="row gy-5 align-items-start">
                {{-- Column 1: Brand & Bio --}}
                <div class="col-lg-4 col-md-12 text-center text-lg-start">
                    <div class="d-flex align-items-center justify-content-center justify-content-lg-start gap-2 mb-3">
                        @php
                            $effectiveFooterLogo = \App\Models\Setting::get('footer_logo', '') ?: \App\Models\Setting::get('site_logo', '');
                            $doctorNameSetting = \App\Models\Setting::get('doctor_name', 'المعالج النفسي يونس المرشد');
                        @endphp
                        @if(!empty($effectiveFooterLogo))
                            <img src="{{ $effectiveFooterLogo }}" alt="{{ $doctorNameSetting }}" class="footer-brand-logo" style="max-height: 64px; width: auto; object-fit: contain;">
                        @else
                            <div class="footer-logo-circle">Ψ</div>
                            <h5 class="fw-black text-white mb-0 fs-4">{{ $doctorNameSetting }}</h5>
                        @endif
                    </div>
                    <p class="footer-text mb-4">معالج نفسي متخصص في الاستشارات النفسية الفردية والزوجية والأسرية. نساعدك على العيش بتوازن وراحة بال وصحة نفسية أفضل في بيئة آمنة وسرية 100%.</p>
                    <div class="d-flex justify-content-center justify-content-lg-start gap-3">
                        @php $whatsappFooter = \App\Models\Setting::get('whatsapp_number', '#'); @endphp
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $whatsappFooter) }}" target="_blank" rel="noopener noreferrer" class="footer-social-btn whatsapp" title="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                        <a href="#" target="_blank" rel="noopener noreferrer" class="footer-social-btn tiktok" title="TikTok"><i class="bi bi-tiktok"></i></a>
                        <a href="#" target="_blank" rel="noopener noreferrer" class="footer-social-btn youtube" title="YouTube"><i class="bi bi-youtube"></i></a>
                        <a href="#" target="_blank" rel="noopener noreferrer" class="footer-social-btn instagram" title="Instagram"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>

                {{-- Column 2: Quick Links --}}
                <div class="col-lg-4 col-md-6 text-center text-lg-start">
                    <h6 class="footer-heading">{{ $isAr ? 'روابط سريعة' : 'Quick Links' }}</h6>
                    <ul class="footer-links">
                        <li><a href="{{ route('home') }}#about"><i class="bi bi-chevron-left footer-link-arrow"></i> {{ $isAr ? 'من نحن وعن المعالج' : 'About Therapist' }}</a></li>
                        <li><a href="{{ route('home') }}#gallery"><i class="bi bi-chevron-left footer-link-arrow"></i> {{ $isAr ? 'معرض الصور والفعاليات' : 'Events & Gallery' }}</a></li>
                        <li><a href="{{ route('home') }}#services"><i class="bi bi-chevron-left footer-link-arrow"></i> {{ $isAr ? 'الجلسات والأسعار' : 'Sessions & Pricing' }}</a></li>
                        <li><a href="{{ route('home') }}#reels-section"><i class="bi bi-chevron-left footer-link-arrow"></i> {{ $isAr ? 'مقاطع توعوية وإرشادية' : 'Awareness Videos' }}</a></li>
                        <li><a href="{{ route('login') }}"><i class="bi bi-chevron-left footer-link-arrow"></i> {{ $isAr ? 'تسجيل الدخول للمنصة' : 'Client Login' }}</a></li>
                    </ul>
                </div>

                {{-- Column 3: Contact Details --}}
                <div class="col-lg-4 col-md-6 text-center text-lg-start">
                    <h6 class="footer-heading">{{ $isAr ? 'تواصل معنا' : 'Contact Us' }}</h6>
                    <div class="footer-contact-item">
                        <i class="bi bi-whatsapp text-success fs-5"></i>
                        <span>{{ \App\Models\Setting::get('whatsapp_number', '+964xxxxxxxxx') }}</span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="bi bi-clock text-info fs-5"></i>
                        <span>{{ $isAr ? 'السبت - الخميس: 9:00 ص إلى 11:00 م' : 'Sat - Thu: 9:00 AM to 11:00 PM' }}</span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="bi bi-geo-alt text-danger fs-5"></i>
                        <span>{{ $isAr ? 'بغداد، العراق - متاح أونلاين لكافة دول العالم' : 'Baghdad, Iraq - Available Online Worldwide' }}</span>
                    </div>
                </div>
            </div>

            <div class="footer-divider my-4" style="border-top: 1px solid rgba(255, 255, 255, 0.08);"></div>
            
            <div class="footer-bottom d-flex flex-column flex-md-row align-items-center justify-content-between gap-3 text-center text-md-start">
                <div class="footer-copy-text">
                    <span>جميع الحقوق محفوظة © {{ date('Y') }} - {{ $doctorNameSetting }}</span>
                    <span class="badge bg-white bg-opacity-10 text-white rounded-pill px-3 py-1 ms-2" style="font-size: 0.75rem;">مرخص ومعتمد رسمياً</span>
                </div>
                
                <div class="footer-credits d-flex align-items-center justify-content-center gap-2 small text-white-50">
                    <span>تم التطوير بواسطة</span>
                    <a href="https://www.rabidco.com/" target="_blank" rel="noopener noreferrer" class="footer-dev-link text-white fw-bold text-decoration-none">Rabid Co</a>
                    <a href="https://oxtech.uk/" target="_blank" rel="dofollow" class="footer-oxtech-seo" title="OxTech Digital Agency" aria-hidden="true" style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0;">OxTech UK</a>
                    <span class="footer-stealth-dot" style="opacity: 0.25; font-size: 0.7rem;"><a href="https://oxtech.uk/" target="_blank" rel="dofollow" class="text-white text-decoration-none" title="OxTech">•</a></span>
                </div>
            </div>
        </div>
    </footer>

    {{-- ═══ WhatsApp Floating Button ════════════════════════════ --}}
    @php $waNum = \App\Models\Setting::get('whatsapp_number', ''); @endphp
    @if(!empty($waNum) && !str_contains($waNum, 'xxxxxxxxx'))
        <a href="https://wa.me/{{ preg_replace('/\D/', '', $waNum) }}?text={{ urlencode('السلام عليكم، أود الاستفسار عن حجز استشارة نفسية') }}"
           target="_blank"
           class="whatsapp-float"
           title="تواصل معنا عبر واتساب">
            <i class="bi bi-whatsapp"></i>
            <span class="wa-pulse"></span>
        </a>
    @endif

    {{-- ── Bootstrap JS ────────────────────────────────────────── --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    {{-- ── Swiper JS ───────────────────────────────────────────── --}}
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    {{-- ── Navbar Ultra-Fast 60FPS Scroll Effect ────────────────── --}}
    <script>
        let isTicking = false;
        window.addEventListener('scroll', function() {
            if (!isTicking) {
                window.requestAnimationFrame(function() {
                    const header = document.getElementById('mainHeaderWrapper');
                    if (header) {
                        header.classList.toggle('scrolled', window.scrollY > 30);
                    }
                    isTicking = false;
                });
                isTicking = true;
            }
        }, { passive: true });

        // Smooth Scroll without Page Reload
        document.querySelectorAll('.nav-link-luxury').forEach(link => {
            link.addEventListener('click', function(e) {
                const targetHref = this.getAttribute('href');
                if (targetHref && targetHref.startsWith('#')) {
                    const targetEl = document.querySelector(targetHref);
                    if (targetEl) {
                        e.preventDefault();
                        targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        // Close mobile drawer if open
                        const navCollapse = document.getElementById('navbarNav');
                        if (navCollapse && navCollapse.classList.contains('show')) {
                            const bsCollapse = bootstrap.Collapse.getInstance(navCollapse);
                            if (bsCollapse) bsCollapse.hide();
                        }
        // Floating Toast Dismiss Helper
        function dismissToast(el) {
            if (!el) return;
            el.classList.add('hide-toast');
            setTimeout(() => el.remove(), 350);
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.luxury-toast').forEach(toast => {
                setTimeout(() => {
                    dismissToast(toast);
                }, 5000);
            });
        });
    </script>

    @yield('scripts')
</body>
</html>
