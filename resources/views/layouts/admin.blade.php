<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - @yield('title', 'إدارة الحجوزات والعيادة')</title>
    
    <!-- Fast CDN DNS Prefetch & Preconnect -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Bootstrap 5.3.3 RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    
    @php
        $primaryColor = \App\Models\Setting::get('primary_color', '#3B52A4');
        $secondaryColor = \App\Models\Setting::get('secondary_color', '#1e3a8a');
        $siteLogo = \App\Models\Setting::get('site_logo', '');
        $siteTitle = \App\Models\Setting::get('site_title', 'إدارة العيادة');
        $currentUser = auth()->user();
    @endphp

    <style>
        :root {
            --primary-color: {{ $primaryColor }};
            --primary-dark: {{ $secondaryColor }};
            --bs-primary: {{ $primaryColor }};
            --bs-primary-rgb: 64, 85, 165;
            --primary-gradient: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }});
        }
    </style>
    
    @yield('styles')
</head>
<body class="bg-light">

    <div class="container-fluid">
        <div class="row">
            
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block admin-sidebar collapse sidebar p-3 d-print-none">
                <div class="d-flex align-items-center mb-4 px-2">
                    @if(!empty($siteLogo))
                        <img src="{{ $siteLogo }}" alt="Logo" class="me-2" style="max-height: 40px; border-radius: 8px;">
                    @else
                        <i class="bi bi-heart-pulse-fill text-danger fs-2 me-2"></i>
                    @endif
                    <h5 class="m-0 fw-bold text-white brand-text">{{ $siteTitle }}</h5>
                    <button class="btn btn-sm text-secondary ms-auto d-none d-md-block p-1" id="sidebarCollapseBtn" onclick="toggleSidebar()" style="background: transparent; border: none; font-size: 1.25rem;">
                        <i class="bi bi-chevron-bar-right text-light"></i>
                    </button>
                </div>
                <hr class="text-secondary">
                <div class="d-flex flex-column gap-1">
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-link @if(Route::is('admin.dashboard')) active @endif">
                        <i class="bi bi-speedometer2 me-2"></i> <span class="link-text">الإحصائيات</span>
                    </a>

                    @if($currentUser && $currentUser->hasPermission('manage_bookings'))
                        <a href="{{ route('admin.bookings') }}" class="sidebar-link @if(Route::is('admin.bookings')) active @endif">
                            <i class="bi bi-calendar-event me-2"></i> <span class="link-text">الحجوزات</span>
                        </a>
                        <a href="{{ route('admin.calendar') }}" class="sidebar-link @if(Route::is('admin.calendar')) active @endif">
                            <i class="bi bi-calendar-range me-2"></i> <span class="link-text">تقويم المواعيد</span>
                        </a>
                    @endif

                    @if($currentUser && $currentUser->hasPermission('manage_patients'))
                        <a href="{{ route('admin.patients') }}" class="sidebar-link @if(Route::is('admin.patients*')) active @endif">
                            <i class="bi bi-people me-2"></i> <span class="link-text">المرضى</span>
                        </a>
                    @endif

                    @if($currentUser && $currentUser->hasPermission('manage_payments'))
                        <a href="{{ route('admin.payments') }}" class="sidebar-link @if(Route::is('admin.payments')) active @endif">
                            <i class="bi bi-credit-card me-2"></i> <span class="link-text">المدفوعات والفواتير</span>
                        </a>
                        <a href="{{ route('admin.reports') }}" class="sidebar-link @if(Route::is('admin.reports*')) active @endif">
                            <i class="bi bi-bar-chart-line-fill me-2"></i> <span class="link-text">التقارير الشاملة</span>
                        </a>
                    @endif

                    @if($currentUser && $currentUser->hasPermission('manage_services'))
                        <a href="{{ route('admin.services') }}" class="sidebar-link @if(Route::is('admin.services')) active @endif">
                            <i class="bi bi-heart-pulse me-2"></i> <span class="link-text">الخدمات والأسعار</span>
                        </a>
                    @endif

                    @if($currentUser && $currentUser->hasPermission('manage_availability'))
                        <a href="{{ route('admin.availability') }}" class="sidebar-link @if(Route::is('admin.availability')) active @endif">
                            <i class="bi bi-clock me-2"></i> <span class="link-text">مواعيد العمل</span>
                        </a>
                    @endif

                    {{-- Settings & Configuration Collapsible Submenu --}}
                    @php
                        $isSettingsActive = Route::is('admin.settings*') || Route::is('admin.api-control*') || Route::is('admin.portfolio*') || Route::is('admin.staff*');
                    @endphp
                    @if($currentUser && ($currentUser->hasPermission('manage_settings') || $currentUser->hasPermission('manage_portfolio') || $currentUser->hasPermission('manage_staff')))
                        <div class="sidebar-dropdown-group">
                            <a class="sidebar-link submenu-toggle d-flex align-items-center justify-content-between @if($isSettingsActive) active @endif" 
                               data-bs-toggle="collapse" 
                               href="#settingsSubmenu" 
                               role="button" 
                               aria-expanded="{{ $isSettingsActive ? 'true' : 'false' }}" 
                               aria-controls="settingsSubmenu">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-gear-fill me-2"></i> <span class="link-text">الإعدادات والتهيئة</span>
                                </div>
                                <i class="bi bi-chevron-down chevron-icon link-text"></i>
                            </a>

                            <div class="collapse @if($isSettingsActive) show @endif" id="settingsSubmenu">
                                <div class="sidebar-submenu">
                                    @if($currentUser->hasPermission('manage_settings'))
                                        <a href="{{ route('admin.settings') }}" class="sidebar-link @if(Route::is('admin.settings')) active @endif">
                                            <i class="bi bi-sliders2 me-2"></i> <span class="link-text">إعدادات المنصة</span>
                                        </a>
                                        <a href="{{ route('admin.api-control') }}" class="sidebar-link @if(Route::is('admin.api-control')) active @endif">
                                            <i class="bi bi-code-slash me-2"></i> <span class="link-text">تحكم الـ API</span>
                                        </a>
                                    @endif

                                    @if($currentUser->hasPermission('manage_portfolio'))
                                        <a href="{{ route('admin.portfolio') }}" class="sidebar-link @if(Route::is('admin.portfolio')) active @endif">
                                            <i class="bi bi-file-person me-2"></i> <span class="link-text">الصفحة التعريفية</span>
                                        </a>
                                    @endif

                                    @if($currentUser->hasPermission('manage_staff'))
                                        <a href="{{ route('admin.staff.index') }}" class="sidebar-link @if(Route::is('admin.staff*')) active @endif">
                                            <i class="bi bi-person-gear me-2"></i> <span class="link-text">إدارة الموظفين</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <hr class="text-secondary mt-5">
                <a href="{{ route('home') }}" class="sidebar-link">
                    <i class="bi bi-house me-2"></i> <span class="link-text">عرض الموقع</span>
                </a>
                <form action="{{ route('logout') }}" method="POST" class="mt-2">
                    @csrf
                    <button type="submit" class="w-100 btn btn-outline-danger btn-sm rounded-pill py-2">
                        <i class="bi bi-box-arrow-right me-1"></i> <span>تسجيل الخروج</span>
                    </button>
                </form>
            </nav>
            
            <!-- Main Content Area -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                
                <!-- Admin Header Navbar -->
                <header class="d-flex flex-wrap justify-content-between align-items-center pb-3 mb-3 border-bottom d-print-none gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-outline-secondary btn-sm d-md-none me-1" type="button" data-bs-toggle="collapse" data-bs-target=".admin-sidebar">
                            <i class="bi bi-list fs-5"></i>
                        </button>

                        {{-- Luxury Minimal Doctor Profile Capsule Dropdown --}}
                        <div class="dropdown">
                            <button class="btn btn-sm border rounded-pill px-2.5 py-1 d-flex align-items-center gap-2 shadow-sm text-dark admin-profile-pill" 
                                    type="button" 
                                    data-bs-toggle="dropdown" 
                                    aria-expanded="false" 
                                    title="د. {{ \App\Models\Setting::get('doctor_name', 'يونس المرشد') }}">
                                <div class="position-relative">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" 
                                         style="width: 32px; height: 32px; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); font-size: 0.85rem;">
                                        {{ mb_substr(\App\Models\Setting::get('doctor_name', 'ي'), 0, 1) }}
                                    </div>
                                    <span class="position-absolute bottom-0 start-0 translate-middle p-1 bg-success border border-white rounded-circle" style="width: 8px; height: 8px;"></span>
                                </div>
                                <span class="fw-bold small d-none d-sm-inline">د. {{ \App\Models\Setting::get('doctor_name', 'يونس المرشد') }}</span>
                                <i class="bi bi-chevron-down text-muted small ms-0.5" style="font-size: 0.7rem;"></i>
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-2 mt-2" style="min-width: 220px; z-index: 1060;">
                                <li class="px-3 py-2 border-bottom mb-1">
                                    <div class="fw-bold text-dark">د. {{ \App\Models\Setting::get('doctor_name', 'يونس المرشد') }}</div>
                                    <div class="small mt-0.5">
                                        @if($currentUser && $currentUser->isAdmin())
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
                                                <i class="bi bi-shield-check me-1"></i> مسؤول النظام
                                            </span>
                                        @else
                                            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
                                                <i class="bi bi-person-check me-1"></i> موظف صلاحيات
                                            </span>
                                        @endif
                                    </div>
                                </li>
                                <li>
                                    <a class="dropdown-item rounded-3 py-2 small fw-bold text-dark d-flex align-items-center gap-2" href="{{ route('admin.portfolio') }}">
                                        <i class="bi bi-file-person text-primary"></i> الصفحة التعريفية
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item rounded-3 py-2 small fw-bold text-dark d-flex align-items-center gap-2" href="{{ route('admin.settings') }}">
                                        <i class="bi bi-gear text-primary"></i> إعدادات المنصة
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item rounded-3 py-2 small fw-bold text-danger d-flex align-items-center gap-2">
                                            <i class="bi bi-box-arrow-right"></i> تسجيل الخروج
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>

                    {{-- Quick Action Buttons --}}
                    <div class="d-flex align-items-center gap-1.5">
                        <button type="button" class="btn btn-sm btn-royal-primary rounded-pill px-3 py-1.5 fw-bold shadow-sm d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#manualBookingModal">
                            <i class="bi bi-plus-circle-fill"></i> حجز جلسة جديدة
                        </button>
                        <a href="{{ route('admin.calendar') }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1.5 fw-bold text-dark">
                            <i class="bi bi-calendar-range text-primary"></i> التقويم
                        </a>
                        <a href="{{ route('admin.reports') }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1.5 fw-bold text-dark">
                            <i class="bi bi-bar-chart-line text-primary"></i> التقارير
                        </a>
                    </div>
                </header>
                
                <!-- Floating Luxury Toast Notifications -->
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
                                    <div class="toast-title">يرجى التحقق من المدخلات</div>
                                    <div class="toast-message">
                                        <ul class="m-0 p-0 ps-3" style="list-style-type: disc;">
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

                @yield('content')
                
            </main>
            
        </div>
    </div>

    <!-- Bootstrap 5.3.3 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Sidebar Toggle Script -->
    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('.admin-sidebar');
            const mainContent = document.querySelector('main');
            const icon = document.querySelector('#sidebarCollapseBtn i');

            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');

            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebar-collapsed', isCollapsed ? 'true' : 'false');

            if (isCollapsed) {
                icon.className = 'bi bi-chevron-bar-left text-light';
            } else {
                icon.className = 'bi bi-chevron-bar-right text-light';
            }
        }

        // Apply state immediately on load
        (function() {
            const collapsed = localStorage.getItem('sidebar-collapsed');
            if (collapsed === 'true') {
                const sidebar = document.querySelector('.admin-sidebar');
                const mainContent = document.querySelector('main');
                if (sidebar) sidebar.classList.add('collapsed');
                if (mainContent) mainContent.classList.add('expanded');
                
        // Toast Dismiss Helper
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
