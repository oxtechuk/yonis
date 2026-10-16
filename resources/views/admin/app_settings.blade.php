@extends('layouts.admin')

@section('title', 'إعدادات شاشة البداية (Splash Screen) وتطبيق الموبايل')

@section('styles')
<style>
    .mobile-mockup {
        width: 290px;
        height: 580px;
        background: #000;
        border-radius: 40px;
        padding: 12px;
        box-shadow: 0 20px 45px rgba(0,0,0,0.22);
        margin: 0 auto;
        position: relative;
    }
    .mobile-screen {
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        border-radius: 30px;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 24px;
        color: #fff;
    }
    .mobile-screen::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.78) 100%);
    }
    .mobile-notch {
        width: 110px;
        height: 20px;
        background: #000;
        border-radius: 0 0 14px 14px;
        position: absolute;
        top: 12px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 10;
    }
    .mobile-content {
        position: relative;
        z-index: 2;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1 text-dark">
                <i class="bi bi-phone text-primary me-2"></i> إعدادات شاشة البداية (Splash Screen) وتطبيق الموبايل
            </h3>
            <p class="text-muted small mb-0">التحكم في الصورة والنصوص الترحيبية التي تظهر للمريض عند فتح تطبيق الموبايل</p>
        </div>
    </div>

    <div class="row g-4 align-items-start">
        {{-- Settings Form --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold text-dark mb-3 border-bottom pb-3">
                    <i class="bi bi-sliders text-primary me-2"></i> بيانات شاشة البداية (Splash / Onboarding)
                </h5>

                <form action="{{ route('admin.app-settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">عنوان شاشة البداية الرئيسي <span class="text-danger">*</span></label>
                        <input type="text" name="app_splash_title" id="inputSplashTitle" class="form-control rounded-3" value="{{ old('app_splash_title', $splashSettings['title']) }}" required>
                        <span class="text-muted small">النص الترحيبي العريض الذي يظهر فور فتح التطبيق</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">النص الفرعي / الوصف</label>
                        <textarea name="app_splash_subtitle" id="inputSplashSubtitle" rows="3" class="form-control rounded-3">{{ old('app_splash_subtitle', $splashSettings['subtitle']) }}</textarea>
                        <span class="text-muted small">شرح مختصر أو شعار العيادة الترحيبي</span>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">مدة بقاء الشاشة (بالثواني)</label>
                            <input type="number" name="app_splash_duration" class="form-control rounded-3" value="{{ old('app_splash_duration', $splashSettings['duration']) }}" min="1" max="10">
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" name="app_splash_enabled" value="1" id="splashEnabled" {{ $splashSettings['enabled'] ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="splashEnabled">تفعيل شاشة البداية في التطبيق</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">صورة خلفية شاشة البداية (Splash Image)</label>
                        <input type="file" name="app_splash_image" id="inputSplashImage" class="form-control rounded-3" accept="image/*">
                        <span class="text-muted small">يفضل استخدام صورة رأسية بدقة عالية (أبعاد مثل 1080x1920 أو 9:16)</span>
                    </div>

                    <div class="pt-3 border-top d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-5 py-2 rounded-3 shadow-sm">
                            <i class="bi bi-save me-1"></i> حفظ وتحديث الإعدادات
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Live Mockup Preview --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center">
                <h6 class="fw-bold text-dark mb-3">
                    <i class="bi bi-eye text-primary me-2"></i> معاينة حية لشاشة الموبايل (Live Preview)
                </h6>
                <div class="mobile-mockup">
                    <div class="mobile-notch"></div>
                    <div class="mobile-screen" id="mockupScreen" style="background-image: url('{{ $splashSettings['image'] ? (str_starts_with($splashSettings['image'], 'http') ? $splashSettings['image'] : asset($splashSettings['image'])) : 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=1000&q=80' }}');">
                        <div class="mobile-content text-start">
                            <span class="badge bg-primary px-3 py-1 mb-2 rounded-pill small">تطبيق العيادة</span>
                            <h5 class="fw-bold mb-2 text-white" id="mockupTitle">{{ $splashSettings['title'] }}</h5>
                            <p class="small text-white-50 mb-4" id="mockupSubtitle">{{ $splashSettings['subtitle'] }}</p>
                            <div class="d-grid">
                                <button class="btn btn-primary btn-sm rounded-pill py-2 shadow" disabled>
                                    دخول التطبيق <i class="bi bi-arrow-left ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-muted small mt-3">
                    <i class="bi bi-info-circle me-1"></i> يتم إرسال هذه البيانات عبر <code>GET /api/splash</code> لتغذية تطبيق Flutter / React Native مباشرة.
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const titleInput = document.getElementById('inputSplashTitle');
        const subtitleInput = document.getElementById('inputSplashSubtitle');
        const imageInput = document.getElementById('inputSplashImage');
        const mockupTitle = document.getElementById('mockupTitle');
        const mockupSubtitle = document.getElementById('mockupSubtitle');
        const mockupScreen = document.getElementById('mockupScreen');

        if (titleInput) {
            titleInput.addEventListener('input', function() {
                mockupTitle.innerText = this.value || 'عنوان شاشة البداية';
            });
        }

        if (subtitleInput) {
            subtitleInput.addEventListener('input', function() {
                mockupSubtitle.innerText = this.value || 'النص الفرعي';
            });
        }

        if (imageInput) {
            imageInput.addEventListener('change', function(e) {
                if (e.target.files && e.target.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        mockupScreen.style.backgroundImage = 'url(' + event.target.result + ')';
                    }
                    reader.readAsDataURL(e.target.files[0]);
                }
            });
        }
    });
</script>
@endsection
