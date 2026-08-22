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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">

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

                        {{-- Account (Icon Only) --}}
                        @auth
                            <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('patient.dashboard') }}" class="btn-nav-icon-account" title="{{ __('messages.my_account') }}">
                                <i class="bi bi-person-fill"></i>
                            </a>
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

    {{-- ═══ Main Content ════════════════════════════════════════ --}}
    <main class="flex-grow-1">
        @yield('content')
    </main>

    {{-- ═══ Footer ═════════════════════════════════════════════ --}}
    <footer class="site-footer">
        <div class="container">
            <div class="row gy-5 align-items-start">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="footer-logo-circle">Ψ</div>
                        <h5 class="fw-black text-white mb-0">يونس المرشد</h5>
                    </div>
                    <p class="footer-text mb-4">معالج نفسي متخصص في الاستشارات النفسية الفردية والزوجية والأسرية. نساعدك على العيش بتوازن وصحة نفسية أفضل.</p>
                    <div class="d-flex gap-3">
                        @php $whatsappFooter = \App\Models\Setting::get('whatsapp_number', '#'); @endphp
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $whatsappFooter) }}" target="_blank" class="footer-social-btn whatsapp"><i class="bi bi-whatsapp"></i></a>
                        <a href="#" class="footer-social-btn"><i class="bi bi-tiktok"></i></a>
                        <a href="#" class="footer-social-btn"><i class="bi bi-youtube"></i></a>
                        <a href="#" class="footer-social-btn"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>

                <div class="col-lg-4">
                    <h6 class="footer-heading">روابط سريعة</h6>
                    <ul class="footer-links">
                        <li><a href="{{ route('home') }}#about">نبذة عن المعالج</a></li>
                        <li><a href="{{ route('home') }}#services">أنواع الجلسات والأسعار</a></li>
                        <li><a href="{{ route('home') }}#reels-section">مقاطع توعوية</a></li>
                        <li><a href="{{ route('home') }}#booking-wizard">احجز استشارتك الآن</a></li>
                        <li><a href="{{ route('login') }}">تسجيل الدخول لحسابي</a></li>
                    </ul>
                </div>

                <div class="col-lg-4">
                    <h6 class="footer-heading">تواصل معنا</h6>
                    <div class="footer-contact-item">
                        <i class="bi bi-whatsapp text-success"></i>
                        <span>{{ \App\Models\Setting::get('whatsapp_number', '+964xxxxxxxxx') }}</span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="bi bi-clock text-info"></i>
                        <span>السبت - الخميس: 9 ص إلى 9 م</span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="bi bi-geo-alt text-danger"></i>
                        <span>العراق - متاح أونلاين لجميع الدول</span>
                    </div>
                </div>
            </div>

            <div class="footer-divider"></div>
            <div class="footer-bottom">
                <span>جميع الحقوق محفوظة © {{ date('Y') }} - المعالج النفسي يونس المرشد</span>
                <span class="footer-badge">مرخص ومعتمد رسمياً</span>
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
                    }
                }
            });
        });
    </script>

    @yield('scripts')
</body>
</html>
