<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'المعالج النفسي يونس المرشد - عيادة الاستشارات النفسية والأسرية')</title>

    @php
        $gaId = \App\Models\Setting::get('google_analytics_id');
        $metaId = \App\Models\Setting::get('meta_pixel_id');
    @endphp
    @if(!empty($gaId) && !str_contains($gaId, 'placeholder'))
        <!-- Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $gaId }}');
        </script>
    @endif
    @if(!empty($metaId) && !str_contains($metaId, 'placeholder'))
        <!-- Meta Pixel Code -->
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
    
    <!-- Google Fonts: Tajawal -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3.3 RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom Royal Indigo CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    
    @yield('styles')
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Premium Navbar -->
    <nav class="navbar navbar-expand-lg navbar-premium sticky-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 44px; height: 44px; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); font-size: 1.3rem;">
                    Ψ
                </div>
                <div class="d-flex flex-column">
                    <span class="fw-black lh-1" style="color: var(--primary-color); font-weight: 900; font-size: 1.25rem;">يونس المرشد</span>
                    <span class="text-secondary small fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">للعلاج النفسي والتطوير الذاتي</span>
                </div>
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-1 my-3 my-lg-0">
                    <li class="nav-item">
                        <a class="nav-link @if(Route::is('home')) active @endif" href="{{ route('home') }}">الرئيسية</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}#about">عن المعالج</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}#services">أنواع الجلسات</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}#reels-section">فيديوهات وتوعية</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}#booking-wizard">احجز استشارتك</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center me-lg-3 gap-2">
                    @auth
                        <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('patient.dashboard') }}" class="btn btn-royal-primary btn-sm px-4">
                            <i class="bi bi-person-circle me-1"></i> حسابي وجلساتي
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary btn-sm rounded-pill px-3">تسجيل خروج</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-royal-outline btn-sm px-4">تسجيل الدخول</a>
                        <a href="{{ route('home') }}#booking-wizard" class="btn btn-royal-primary btn-sm px-4">احجز موعدك</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5 mt-5 border-top border-secondary">
        <div class="container">
            <div class="row gy-4 align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2 mb-3">
                        <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; font-size: 1.1rem; color: var(--primary-color) !important;">
                            Ψ
                        </div>
                        <h5 class="fw-bold mb-0 text-white">المعالج النفسي يونس المرشد</h5>
                    </div>
                    <p class="text-secondary small mb-0">نساعدك على تجاوز الصعوبات النفسية والوصول إلى حياة متوازنة وواعية. استشارات تخصصية فردية وزوجية في العراق وخارجه.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <h6 class="fw-bold mb-3 text-gold" style="color: var(--accent-gold);">تابعنا على منصات التواصل</h6>
                    <div class="d-flex justify-content-center justify-content-md-end gap-3 fs-4 mb-3">
                        <a href="#" class="text-white-50 text-hover-white"><i class="bi bi-tiktok"></i></a>
                        <a href="#" class="text-white-50 text-hover-white"><i class="bi bi-youtube"></i></a>
                        <a href="#" class="text-white-50 text-hover-white"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-white-50 text-hover-white"><i class="bi bi-whatsapp"></i></a>
                    </div>
                    <p class="text-secondary small mb-0">جميع الحقوق محفوظة © {{ date('Y') }} - المعالج النفسي يونس المرشد</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5.3.3 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    @yield('scripts')
</body>
</html>
