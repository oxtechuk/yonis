@extends('layouts.app')

@section('title', 'إنشاء حساب جديد - عيادة د. يونس أحمد')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card card-glass p-4 border-0 shadow">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="bi bi-person-plus-fill text-teal fs-1" style="color: var(--accent-color);"></i>
                        <h3 class="fw-bold mt-2">إنشاء حساب جديد</h3>
                        <p class="text-secondary small">يرجى ملء البيانات التالية لإنشاء ملفك الطبي والتمكن من الحجز</p>
                    </div>

                    <form action="{{ route('register') }}" method="POST">
                        @csrf

                        <!-- Name -->
                        <div class="form-floating mb-3">
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="floatingName" placeholder="الاسم الكامل" value="{{ old('name') }}" required>
                            <label for="floatingName">الاسم الكامل</label>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="form-floating mb-3">
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="floatingEmail" placeholder="name@example.com" value="{{ old('email') }}" required>
                            <label for="floatingEmail">البريد الإلكتروني</label>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="form-floating mb-3">
                            <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" id="floatingPhone" placeholder="رقم الهاتف" value="{{ old('phone') }}" required>
                            <label for="floatingPhone">رقم الجوال (الرقم الطبي للتواصل)</label>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="form-floating mb-3">
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="floatingPassword" placeholder="كلمة المرور" required>
                            <label for="floatingPassword">كلمة المرور</label>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="form-floating mb-4">
                            <input type="password" name="password_confirmation" class="form-control" id="floatingConfirmPassword" placeholder="تأكيد كلمة المرور" required>
                            <label for="floatingConfirmPassword">تأكيد كلمة المرور</label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-premium w-100 py-2.5 mb-3">
                            <i class="bi bi-check-circle me-1"></i> تسجيل الحساب
                        </button>

                        <div class="text-center small mt-3">
                            <span class="text-secondary">لديك حساب بالفعل؟</span>
                            <a href="{{ route('login') }}" class="text-decoration-none fw-bold ms-1" style="color: var(--accent-color);">تسجيل الدخول</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
