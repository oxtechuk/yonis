<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- ── SEO Core Meta ─────────────────────────────────────── --}}
    <title>@yield('title', 'المعالج النفسي يونس المرشد - استشارات نفسية وأسرية متخصصة')</title>
    <meta name="description" content="@yield('meta_description', 'احجز استشارتك النفسية الآن مع المعالج يونس المرشد. جلسات فردية وزوجية وأسرية بخبرة أكثر من 10 سنوات. حجز أونلاين عبر شات أو صوت أو فيديو، أو في العيادة.')">
    <meta name="keywords" content="@yield('meta_keywords', 'معالج نفسي, استشارة نفسية, علاج نفسي, يونس المرشد, حجز موعد نفسي, اكتئاب, قلق, علاج زوجي')">
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
    <meta property="og:locale" content="ar_AR">
    <meta property="og:site_name" content="يونس المرشد - للعلاج النفسي">
    <meta property="og:title" content="@yield('title', 'المعالج النفسي يونس المرشد')">
    <meta property="og:description" content="@yield('meta_description', 'استشارات نفسية متخصصة - احجز موعدك الآن')">
    <meta property="og:url" content="{{ url()->current() }}">
    @if(!empty($ogImg))
        <meta property="og:image" content="{{ $ogImg }}">
    @endif

    {{-- ── Twitter Card ──────────────────────────────────────── --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'المعالج النفسي يونس المرشد')">
    <meta name="twitter:description" content="@yield('meta_description', 'استشارات نفسية متخصصة - احجز موعدك الآن')">

    {{-- ── Schema.org: Physician + MedicalClinic ─────────────── --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@graph": [
            {
                "@type": "Physician",
                "name": "{{ \App\Models\Setting::get('doctor_name', 'يونس المرشد') }}",
                "description": "معالج نفسي مرخص بخبرة أكثر من 10 سنوات في الاستشارات النفسية والعلاج المعرفي السلوكي",
                "url": "{{ url('/') }}",
                "medicalSpecialty": "Psychiatry",
                "availableService": [
                    {"@type": "MedicalTherapy", "name": "استشارة نفسية فردية"},
                    {"@type": "MedicalTherapy", "name": "استشارة زوجية وأسرية"},
                    {"@type": "MedicalTherapy", "name": "علاج الاكتئاب والقلق"}
                ]
            },
            {
                "@type": "MedicalClinic",
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

    {{-- ── Fonts: Tajawal ────────────────────────────────────── --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">

    {{-- ── Bootstrap 5.3.3 RTL ───────────────────────────────── --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">

    {{-- ── Bootstrap Icons ────────────────────────────────────── --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- ── Swiper (for carousels) ──────────────────────────────── --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    {{-- ── Custom CSS ─────────────────────────────────────────── --}}
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    {{-- ── Sitemap ──────────────────────────────────────────────── --}}
    <link rel="sitemap" type="application/xml" href="/sitemap.xml">

    @yield('styles')
</head>
<body class="d-flex flex-column min-vh-100">

    {{-- ═══ Navbar ════════════════════════════════════════════ --}}
    <nav class="navbar navbar-expand-lg navbar-premium sticky-top py-2" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                <div class="navbar-logo-circle">Ψ</div>
                <div class="d-flex flex-column">
                    <span class="fw-black lh-1" style="color: var(--primary-color); font-size: 1.2rem;">يونس المرشد</span>
                    <span class="text-secondary small" style="font-size: 0.68rem; letter-spacing: 0.4px;">للعلاج النفسي والتطوير الذاتي</span>
                </div>
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-1 my-3 my-lg-0">
                    <li class="nav-item"><a class="nav-link @if(Route::is('home')) active @endif" href="{{ route('home') }}">الرئيسية</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#about">عن المعالج</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#services">الجلسات</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#reels-section">فيديوهات</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#booking-wizard">احجز موعدك</a></li>
                </ul>

                <div class="d-flex align-items-center me-lg-3 gap-2">
                    @auth
                        <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('patient.dashboard') }}" class="btn btn-royal-primary btn-sm px-4">
                            <i class="bi bi-person-circle me-1"></i> حسابي
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-royal-outline btn-sm px-4">دخول</a>
                        <a href="{{ route('home') }}#booking-wizard" class="btn btn-royal-primary btn-sm px-4">
                            <i class="bi bi-calendar-check me-1"></i> احجز الآن
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

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

    {{-- ── Navbar Scroll Effect ────────────────────────────────── --}}
    <script>
        window.addEventListener('scroll', function() {
            const nav = document.getElementById('mainNavbar');
            if (nav) {
                nav.classList.toggle('scrolled', window.scrollY > 50);
            }
        });
    </script>

    @yield('scripts')
</body>
</html>
