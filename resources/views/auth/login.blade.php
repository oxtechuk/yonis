@extends('layouts.app')

@section('title', 'تسجيل الدخول - المعالج النفسي يونس المرشد')

@section('content')
<div class="auth-page-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5 col-xl-4">
                <div class="auth-card-luxury">
                    
                    {{-- Platform Brand Logo & Header --}}
                    <div class="text-center mb-4">
                        @php $siteLogo = \App\Models\Setting::get('site_logo', ''); @endphp
                        @if(!empty($siteLogo))
                            <img src="{{ $siteLogo }}" alt="Logo" class="mb-3" style="max-height: 55px; border-radius: 8px;">
                        @else
                            <div class="auth-logo-badge">Ψ</div>
                        @endif
                        <h4 class="fw-black mb-1" style="color: var(--primary-color);">يونس المرشد</h4>
                        <p class="text-secondary small mb-0">بوابة تسجيل الدخول الآمنة للاستشارات النفسية</p>
                    </div>

                    {{-- Flash Errors & Validation Messages --}}
                    @if(session('error'))
                        <div class="alert alert-danger border-0 small py-2.5 px-3 mb-3 rounded-3 d-flex align-items-center gap-2" role="alert">
                            <i class="bi bi-exclamation-circle-fill text-danger fs-5"></i>
                            <div>{{ session('error') }}</div>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger border-0 small py-2.5 px-3 mb-3 rounded-3 d-flex align-items-center gap-2" role="alert">
                            <i class="bi bi-exclamation-circle-fill text-danger fs-5"></i>
                            <div>{{ $errors->first() }}</div>
                        </div>
                    @endif

                    <form action="{{ route('login') }}" method="POST" id="loginForm">
                        @csrf

                        <!-- Identifier: Email or Phone -->
                        <div class="mb-3">
                            <label for="loginInput" class="form-label small fw-bold text-dark mb-1.5">البريد الإلكتروني أو رقم الهاتف</label>
                            <div class="input-group auth-input-group @error('login') border-danger @enderror">
                                <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                <input type="text" name="login" class="form-control" id="loginInput" placeholder="name@example.com أو +964xxxxxxxxx" value="{{ old('login') }}" required autofocus>
                            </div>
                        </div>

                        <!-- Password with Visibility Toggle -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1.5">
                                <label for="passwordInput" class="form-label small fw-bold text-dark mb-0">كلمة المرور</label>
                            </div>
                            <div class="input-group auth-input-group @error('password') border-danger @enderror">
                                <span class="input-group-text"><i class="bi bi-shield-lock-fill"></i></span>
                                <input type="password" name="password" class="form-control" id="passwordInput" placeholder="••••••••" required>
                                <button type="button" class="input-group-text text-muted" onclick="togglePasswordVisibility('passwordInput', this)" title="إظهار/إخفاء كلمة المرور">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Remember Me -->
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" name="remember" id="rememberSwitch" checked>
                            <label class="form-check-label text-secondary small fw-bold" for="rememberSwitch">تذكر تسجيل دخولي</label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-royal-primary w-100 py-3 rounded-pill fw-bold shadow-sm mb-3" id="submitBtn">
                            <i class="bi bi-box-arrow-in-right me-1"></i> تسجيل الدخول
                        </button>

                        <!-- Registration Link -->
                        <div class="text-center small pt-3 border-top">
                            <span class="text-secondary">ليس لديك حساب مراجع حتى الآن؟</span>
                            <a href="{{ route('register') }}" class="fw-bold ms-1" style="color: var(--primary-color);">إنشاء حساب جديد</a>
                        </div>
                    </form>
                </div>

                {{-- Security & Privacy Badge --}}
                <div class="text-center mt-3 small text-secondary">
                    <i class="bi bi-shield-check text-success me-1"></i> جلسة مشفرة وخاضعة للسرية الطبية التامة
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>
@endsection
