@extends('layouts.app')

@section('title', 'إنشاء حساب جديد - المعالج النفسي يونس المرشد')

@section('content')
<div class="auth-page-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-6 col-xl-5">
                <div class="auth-card-luxury">
                    
                    {{-- Platform Brand Logo & Header --}}
                    <div class="text-center mb-4">
                        @php $siteLogo = \App\Models\Setting::get('site_logo', ''); @endphp
                        @if(!empty($siteLogo))
                            <img src="{{ $siteLogo }}" alt="Logo" class="mb-3" style="max-height: 55px; border-radius: 8px;">
                        @else
                            <div class="auth-logo-badge">Ψ</div>
                        @endif
                        <h4 class="fw-black mb-1" style="color: var(--primary-color);">إنشاء حساب مراجع جديد</h4>
                        <p class="text-secondary small mb-0">سجل بياناتك لإدارة مواعيدك وجلساتك النفسية بسهولة</p>
                    </div>

                    {{-- Validation Errors --}}
                    @if($errors->any())
                        <div class="alert alert-danger border-0 small py-2.5 px-3 mb-3 rounded-3 d-flex align-items-center gap-2" role="alert">
                            <i class="bi bi-exclamation-circle-fill text-danger fs-5"></i>
                            <div>{{ $errors->first() }}</div>
                        </div>
                    @endif

                    <form action="{{ route('register') }}" method="POST" id="registerForm">
                        @csrf

                        <!-- Full Name -->
                        <div class="mb-3">
                            <label for="nameInput" class="form-label small fw-bold text-dark mb-1.5">الاسم الكامل</label>
                            <div class="input-group auth-input-group @error('name') border-danger @enderror">
                                <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                <input type="text" name="name" class="form-control" id="nameInput" placeholder="أحمد محمد" value="{{ old('name') }}" required autofocus>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="emailInput" class="form-label small fw-bold text-dark mb-1.5">البريد الإلكتروني</label>
                            <div class="input-group auth-input-group @error('email') border-danger @enderror">
                                <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                                <input type="email" name="email" class="form-control" id="emailInput" placeholder="name@example.com" value="{{ old('email') }}" required>
                            </div>
                        </div>

                        <!-- Phone (WhatsApp Number) with SVG Country Flag Picker -->
                        <div class="mb-3">
                            <label for="rawPhoneInput" class="form-label small fw-bold text-dark mb-1.5">رقم الواتساب (للتواصل الطبي وتأكيد الموعد)</label>
                            <div class="input-group auth-input-group @error('phone') border-danger @enderror">
                                <span class="input-group-text bg-light border-0 pe-1 ps-2">
                                    <img id="registerCountryFlagImg" src="https://flagcdn.com/w40/iq.png" width="24" height="16" class="rounded shadow-sm" alt="العراق">
                                </span>
                                <select class="form-select border-0 bg-light fw-bold text-dark ps-1" id="registerCountryCode" style="max-width: 140px; cursor: pointer;" onchange="onRegisterCountryChanged(this)">
                                    <option value="+964" data-flag="https://flagcdn.com/w40/iq.png" selected>+964 (العراق)</option>
                                    <option value="+966" data-flag="https://flagcdn.com/w40/sa.png">+966 (السعودية)</option>
                                    <option value="+971" data-flag="https://flagcdn.com/w40/ae.png">+971 (الإمارات)</option>
                                    <option value="+965" data-flag="https://flagcdn.com/w40/kw.png">+965 (الكويت)</option>
                                    <option value="+974" data-flag="https://flagcdn.com/w40/qa.png">+974 (قطر)</option>
                                    <option value="+968" data-flag="https://flagcdn.com/w40/om.png">+968 (عُمان)</option>
                                    <option value="+973" data-flag="https://flagcdn.com/w40/bh.png">+973 (البحرين)</option>
                                    <option value="+962" data-flag="https://flagcdn.com/w40/jo.png">+962 (الأردن)</option>
                                    <option value="+20" data-flag="https://flagcdn.com/w40/eg.png">+20 (مصر)</option>
                                    <option value="+961" data-flag="https://flagcdn.com/w40/lb.png">+961 (لبنان)</option>
                                    <option value="+90" data-flag="https://flagcdn.com/w40/tr.png">+90 (تركيا)</option>
                                    <option value="+44" data-flag="https://flagcdn.com/w40/gb.png">+44 (بريطانيا)</option>
                                    <option value="+1" data-flag="https://flagcdn.com/w40/us.png">+1 (أمريكا/كندا)</option>
                                    <option value="+49" data-flag="https://flagcdn.com/w40/de.png">+49 (ألمانيا)</option>
                                    <option value="+46" data-flag="https://flagcdn.com/w40/se.png">+46 (السويد)</option>
                                </select>
                                <input type="tel" class="form-control border-start" id="rawPhoneInput" placeholder="7701234567" value="{{ old('phone') ? preg_replace('/^\+\d{1,4}/', '', old('phone')) : '' }}" required>
                                <input type="hidden" name="phone" id="phoneInput" value="{{ old('phone') }}">
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="regPasswordInput" class="form-label small fw-bold text-dark mb-1.5">كلمة المرور (8 أحرف على الأقل)</label>
                            <div class="input-group auth-input-group @error('password') border-danger @enderror">
                                <span class="input-group-text"><i class="bi bi-shield-lock-fill"></i></span>
                                <input type="password" name="password" class="form-control" id="regPasswordInput" placeholder="••••••••" required>
                                <button type="button" class="input-group-text text-muted" onclick="togglePasswordVisibility('regPasswordInput', this)" title="إظهار/إخفاء كلمة المرور">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label for="confirmPasswordInput" class="form-label small fw-bold text-dark mb-1.5">تأكيد كلمة المرور</label>
                            <div class="input-group auth-input-group">
                                <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                                <input type="password" name="password_confirmation" class="form-control" id="confirmPasswordInput" placeholder="••••••••" required>
                                <button type="button" class="input-group-text text-muted" onclick="togglePasswordVisibility('confirmPasswordInput', this)" title="إظهار/إخفاء كلمة المرور">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-royal-primary w-100 py-3 rounded-pill fw-bold shadow-sm mb-3">
                            <i class="bi bi-check2-circle me-1"></i> إنشاء الحساب والمتابعة
                        </button>

                        <!-- Login Link -->
                        <div class="text-center small pt-3 border-top">
                            <span class="text-secondary">لديك حساب بالفعل؟</span>
                            <a href="{{ route('login') }}" class="fw-bold ms-1" style="color: var(--primary-color);">تسجيل الدخول</a>
                        </div>
                    </form>
                </div>

                {{-- Security & Privacy Badge --}}
                <div class="text-center mt-3 small text-secondary">
                    <i class="bi bi-lock-fill text-success me-1"></i> جميع البيانات الطبية مشفرة ومحمية بسرية تامة
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

function onRegisterCountryChanged(selectEl) {
    const opt = selectEl.options[selectEl.selectedIndex];
    const flagImg = document.getElementById('registerCountryFlagImg');
    if (flagImg && opt && opt.getAttribute('data-flag')) {
        flagImg.src = opt.getAttribute('data-flag');
        flagImg.alt = opt.text;
    }
}

// Auto combine country code with phone on submit
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const code = document.getElementById('registerCountryCode').value;
    let raw = document.getElementById('rawPhoneInput').value.trim();
    raw = raw.replace(/^0+/, '');
    if (raw.startsWith('+')) {
        document.getElementById('phoneInput').value = raw;
    } else {
        document.getElementById('phoneInput').value = code + raw;
    }
});
</script>
@endsection
