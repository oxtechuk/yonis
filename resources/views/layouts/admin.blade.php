<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - @yield('title', 'إدارة الحجوزات والعيادة')</title>
    
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
                            <i class="bi bi-credit-card me-2"></i> <span class="link-text">المدفوعات والتقارير</span>
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

                    @if($currentUser && $currentUser->hasPermission('manage_portfolio'))
                        <a href="{{ route('admin.portfolio') }}" class="sidebar-link @if(Route::is('admin.portfolio')) active @endif">
                            <i class="bi bi-file-person me-2"></i> <span class="link-text">الصفحة التعريفية</span>
                        </a>
                    @endif

                    @if($currentUser && $currentUser->hasPermission('manage_staff'))
                        <a href="{{ route('admin.staff.index') }}" class="sidebar-link @if(Route::is('admin.staff*')) active @endif">
                            <i class="bi bi-person-gear me-2"></i> <span class="link-text">إدارة الموظفين</span>
                        </a>
                    @endif

                    @if($currentUser && $currentUser->hasPermission('manage_settings'))
                        <a href="{{ route('admin.settings') }}" class="sidebar-link @if(Route::is('admin.settings')) active @endif">
                            <i class="bi bi-gear me-2"></i> <span class="link-text">إعدادات المنصة</span>
                        </a>
                        <a href="{{ route('admin.api-control') }}" class="sidebar-link @if(Route::is('admin.api-control')) active @endif">
                            <i class="bi bi-code-slash me-2"></i> <span class="link-text">تحكم الـ API</span>
                        </a>
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
                
                <!-- Admin Header -->
                <header class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom d-print-none">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-outline-secondary d-md-none me-2" type="button" data-bs-toggle="collapse" data-bs-target=".admin-sidebar">
                            <i class="bi bi-list"></i>
                        </button>
                        <h4 class="m-0 fw-bold text-dark">أهلاً بك، {{ $currentUser ? $currentUser->name : 'المستخدم' }}</h4>
                    </div>
                    <div>
                        @if($currentUser && $currentUser->isAdmin())
                            <span class="badge bg-primary px-3 py-2 fs-6 rounded-pill"><i class="bi bi-shield-lock-fill me-1"></i> مسؤول النظام</span>
                        @else
                            <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2 fs-6 rounded-pill"><i class="bi bi-person-badge-fill me-1"></i> موظف صلاحيات</span>
                        @endif
                    </div>
                </header>
                
                <!-- Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <i class="bi bi-exclamation-octagon-fill me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
                
                window.addEventListener('DOMContentLoaded', () => {
                    const icon = document.querySelector('#sidebarCollapseBtn i');
                    if (icon) icon.className = 'bi bi-chevron-bar-left text-light';
                });
            }
        })();
    </script>

    @yield('scripts')
</body>
</html>
