@extends('layouts.app')

@section('title', 'تسجيل الدخول - عيادة د. يونس أحمد')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card card-glass p-4 border-0 shadow">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="bi bi-person-circle text-teal fs-1" style="color: var(--accent-color);"></i>
                        <h3 class="fw-bold mt-2">تسجيل الدخول</h3>
                        <p class="text-secondary small">مرحباً بك مجدداً، يرجى إدخال بياناتك للمتابعة</p>
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger border-0 small py-2 mb-3">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('login') }}" method="POST">
                        @csrf

                        <!-- Email -->
                        <div class="form-floating mb-3">
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="floatingInput" placeholder="name@example.com" value="{{ old('email') }}" required>
                            <label for="floatingInput">البريد الإلكتروني</label>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="form-floating mb-3">
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="floatingPassword" placeholder="Password" required>
                            <label for="floatingPassword">كلمة المرور</label>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="form-check form-switch mb-4 text-start">
                            <input class="form-check-input float-end ms-2" type="checkbox" role="switch" name="remember" id="rememberSwitch">
                            <label class="form-check-label" for="rememberSwitch">تذكرني على هذا الجهاز</label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-premium w-100 py-2.5 mb-3">
                            <i class="bi bi-box-arrow-in-left me-1"></i> دخول
                        </button>

                        <div class="text-center small mt-3">
                            <span class="text-secondary">ليس لديك حساب؟</span>
                            <a href="{{ route('register') }}" class="text-decoration-none fw-bold ms-1" style="color: var(--accent-color);">إنشاء حساب جديد</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
