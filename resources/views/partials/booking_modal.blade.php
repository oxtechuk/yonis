@php
    $modalServices = $services ?? \App\Models\Service::where('is_active', true)->get();
    $waRaw = \App\Models\Setting::get('whatsapp_number', '+9647700000000');
    $isArLocale = app()->getLocale() === 'ar';
    // ─ Payment settings ─
    $payZainEnabled   = \App\Models\Setting::get('payment_zaincash_enabled', '1') === '1';
    $payZainQr        = \App\Models\Setting::getFileUrl('payment_zaincash_qr', '');
    $payZainLabel     = \App\Models\Setting::get('payment_zaincash_label', 'افتح تطبيق زين كاش وامسح الرمز لإتمام الدفع، ثم أرسل لقطة شاشة الإيصال للدكتور.');
    $paySuperkiEnabled = \App\Models\Setting::get('payment_superki_enabled', '1') === '1';
    $paySuperkiQr     = \App\Models\Setting::getFileUrl('payment_superki_qr', '');
    $paySuperkiLabel  = \App\Models\Setting::get('payment_superki_label', 'افتح تطبيق SuperKi وامسح الرمز لإتمام الدفع، ثم أرسل لقطة شاشة الإيصال للدكتور.');
    $payCardEnabled   = \App\Models\Setting::get('payment_card_enabled', '0') === '1';
    $payCardKey       = \App\Models\Setting::get('payment_card_key', '');
    $payCardLink      = \App\Models\Setting::get('payment_card_link', '');
    $payCardCurrency  = \App\Models\Setting::get('payment_card_currency', 'USD');
    $payCardInstructions = \App\Models\Setting::get('payment_card_instructions', 'يمكنك الدفع مباشرة باستخدام أي بطاقة فيزا أو ماستر كارد صادرة محلياً أو دولياً بأمان وسرية تامة.');
    $anyPaymentActive = $payZainEnabled || $paySuperkiEnabled || $payCardEnabled;
    $defaultPayMethod = $payZainEnabled ? 'zaincash' : ($paySuperkiEnabled ? 'superki' : ($payCardEnabled ? 'card' : 'zaincash'));
@endphp

{{-- ═══ REUSABLE BOOKING POPUP MODAL WITH INTERACTIVE MULTI-STEP FLOW ═══ --}}
<style>
/* ─── Modal & Step 2 Streamlined Design ─── */
.mobile-app-modal-dialog {
    max-width: 500px !important;
}
.mobile-app-modal-content {
    border-radius: 24px !important;
}
.mobile-app-header {
    padding: 0.85rem 1.25rem !important;
}
.mobile-app-header-title {
    font-size: 1.05rem !important;
    font-weight: 800 !important;
}
.mobile-app-body {
    padding: 1.1rem 1.25rem 5.2rem 1.25rem !important;
}
.app-section-title {
    font-size: 0.88rem !important;
    font-weight: 800 !important;
    margin-bottom: 0.5rem !important;
    color: #0f172a !important;
}
.app-input {
    border-radius: 12px !important;
    padding: 0.65rem 0.85rem !important;
    font-size: 0.85rem !important;
}
.app-input-sm {
    border: 1.5px solid #E2E8F0;
    border-radius: 12px;
    padding: 0.5rem 0.75rem;
    font-size: 0.82rem;
    background: #ffffff;
    transition: all 0.2s ease;
}
.app-input-sm:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(59, 82, 164, 0.12);
    outline: none;
}

/* ─── Phone & Country Code Input Group ─── */
.phone-input-group {
    border-radius: 16px !important;
}
.app-country-select {
    max-width: 130px !important;
    min-width: 120px !important;
    background-color: #f8fafc !important;
    border: 1.5px solid #E2E8F0 !important;
    border-right: none !important;
    border-radius: 16px 0 0 16px !important;
    font-size: 0.82rem !important;
    font-weight: 700 !important;
    color: #1e293b !important;
    padding-left: 0.75rem !important;
    padding-right: 1.5rem !important;
    cursor: pointer;
    transition: all 0.2s ease;
}
.app-country-select:focus {
    border-color: var(--primary-color) !important;
    background-color: #ffffff !important;
    box-shadow: 0 0 0 3px rgba(59, 82, 164, 0.1) !important;
    outline: none;
}
.phone-number-input {
    border-radius: 0 16px 16px 0 !important;
    border-left: 1px solid #e2e8f0 !important;
    font-weight: 600 !important;
    font-size: 0.88rem !important;
    letter-spacing: 0.5px;
}

/* ─── Segmented Payment Method Toggle Switch ─── */
.pay-toggle-switcher {
    display: flex;
    background: #f1f5f9;
    padding: 4px;
    border-radius: 14px;
    gap: 4px;
    border: 1px solid #e2e8f0;
}
.pay-toggle-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 7px 6px;
    background: transparent;
    border: none;
    border-radius: 10px;
    color: #64748b;
    font-weight: 700;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    outline: none;
    user-select: none;
}
.pay-toggle-btn:hover:not(.active) {
    background: rgba(255, 255, 255, 0.65);
    color: #1e293b;
}
.pay-toggle-btn.active {
    background: #ffffff;
    box-shadow: 0 3px 10px rgba(15, 23, 42, 0.08);
}
.pay-toggle-btn.active.zain {
    color: #6d28d9;
    box-shadow: 0 2px 10px rgba(109, 40, 217, 0.16), 0 0 0 1.5px #7c3aed;
}
.pay-toggle-btn.active.superki {
    color: #0369a1;
    box-shadow: 0 2px 10px rgba(2, 132, 199, 0.16), 0 0 0 1.5px #0284c7;
}
.pay-toggle-btn.active.card {
    color: #1e40af;
    box-shadow: 0 2px 10px rgba(37, 99, 235, 0.16), 0 0 0 1.5px #2563eb;
}
.pay-toggle-icon {
    width: 22px;
    height: 22px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    color: #fff;
    flex-shrink: 0;
}
.pay-toggle-icon.zain { background: linear-gradient(135deg, #7c3aed, #4c1d95); }
.pay-toggle-icon.superki { background: linear-gradient(135deg, #0284c7, #075985); }
.pay-toggle-icon.card { background: linear-gradient(135deg, #1e3a8a, #2563eb); }

/* ─── Compact QR Box & Panels ─── */
.pay-qr-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 10px 12px;
    text-align: center;
}
.pay-qr-img-box {
    display: inline-block;
    padding: 6px;
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    margin-bottom: 6px;
}
.pay-qr-img-box img {
    max-width: 120px;
    max-height: 120px;
    border-radius: 8px;
    object-fit: contain;
    display: block;
}
.pay-qr-label {
    font-size: 0.77rem;
    color: #475569;
    line-height: 1.45;
    margin: 0;
    font-weight: 600;
}

/* ─── Proof & Upload Card ─── */
.pay-proof-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 10px 12px;
    margin-top: 8px;
    box-shadow: 0 1px 4px rgba(15, 23, 42, 0.02);
}
.pay-upload-dropzone {
    border: 1.5px dashed #cbd5e1;
    border-radius: 10px;
    padding: 8px 10px;
    background: #f8fafc;
    cursor: pointer;
    text-align: center;
    transition: all 0.2s ease;
}
.pay-upload-dropzone:hover {
    border-color: var(--primary-color);
    background: #f0f4ff;
}

/* ─── Bottom Bar ─── */
.mobile-app-bottom-bar {
    padding: 0.7rem 1.25rem !important;
    gap: 8px !important;
}
</style>

<div class="modal fade" id="bookingModal" tabindex="-1" aria-hidden="true" dir="{{ $isArLocale ? 'rtl' : 'ltr' }}">
    <div class="modal-dialog modal-dialog-centered mobile-app-modal-dialog">
        <div class="modal-content mobile-app-modal-content position-relative {{ $isArLocale ? 'text-end' : 'text-start' }}">
            
            {{-- Header --}}
            <div class="mobile-app-header">
                <button type="button" class="btn btn-sm btn-light rounded-circle p-2 d-flex align-items-center justify-content-center" data-bs-dismiss="modal" style="width:36px; height:36px;">
                    <i class="bi bi-x-lg fs-6"></i>
                </button>
                <h5 class="mobile-app-header-title" id="app-header-title-text">{{ __('messages.immediate_session') }}</h5>
                <div style="width:36px;"></div>
            </div>

            {{-- Body --}}
            <div class="mobile-app-body">
                
                {{-- ═══════════════════════════════════════════════════════════
                     الخطوة الأولى (Step 1): العنوان والاسم والتاريخ والوقت
                     ═══════════════════════════════════════════════════════════ --}}
                <div id="app-screen-1">
                    
                    {{-- 1. نوع الحجز المطلوب --}}
                    <div class="mb-3">
                        <div class="app-section-title">{{ __('messages.booking_type') }}</div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn app-type-toggle-btn flex-fill active" id="modalBtnOnline" onclick="setModalBookingType('online', this)">
                                {{ __('messages.online_consultation') }}
                            </button>
                            <button type="button" class="btn app-type-toggle-btn flex-fill" id="modalBtnClinic" onclick="setModalBookingType('clinic', this)">
                                {{ __('messages.clinic_consultation') }}
                            </button>
                        </div>
                    </div>

                    {{-- 2. اختيار الخدمة أو الجلسة --}}
                    <div class="mb-3">
                        <div class="app-section-title">{{ __('messages.select_service') }}</div>
                        <select class="form-select app-input w-100 fw-bold {{ $isArLocale ? 'text-end' : 'text-start' }}" id="app_service_select" onchange="onModalServiceChanged(this)">
                            @foreach($modalServices as $s)
                                <option value="{{ $s->id }}" 
                                        data-title="{{ $isArLocale ? ($s->title_ar ?: $s->title) : ($s->title_en ?: $s->title) }}" 
                                        data-duration="{{ $s->duration }}" 
                                        data-price="{{ $s->getDisplayPrice() }}"
                                        data-video="{{ $s->video_price }}"
                                        data-voice="{{ $s->voice_price }}"
                                        data-chat="{{ $s->chat_price }}"
                                        data-clinic="{{ $s->clinic_price ?? $s->price }}"
                                        data-channel="{{ $s->getChannelType() }}"
                                        data-type="{{ $s->type }}">
                                    {{ $isArLocale ? ($s->title_ar ?: $s->title) : ($s->title_en ?: $s->title) }} • {{ $isArLocale ? $s->getChannelLabel() : $s->getChannelLabelEn() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 3. مدة الاستشارة --}}
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1.5">
                            <div class="app-section-title mb-0">
                                <i class="bi bi-clock-history text-primary me-1"></i> {{ __('messages.duration_title') }}
                            </div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fw-bold" id="app_modal_duration_badge" style="font-size: 0.82rem;">
                                {{ $modalServices->first()->duration ?? 45 }} {{ __('messages.minutes') }}
                            </span>
                        </div>
                    </div>

                    {{-- 4. عنوان وموضوع الاستشارة --}}
                    <div class="mb-3">
                        <div class="app-section-title">{{ __('messages.consultation_subject') }}</div>
                        <input type="text" id="app_consultation_title" class="form-control app-input w-100 {{ $isArLocale ? 'text-end' : 'text-start' }}" placeholder="{{ __('messages.consultation_subject_ph') }}" value="{{ __('messages.consultation_subject_default') }}" required>
                    </div>

                    {{-- 5. بيانات المريض للتواصل والتأكيد --}}
                    <div class="mb-3 pt-2 border-top">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="app-section-title fs-6 fw-black text-dark mb-0">
                                <i class="bi bi-person-badge text-primary me-1"></i> {{ __('messages.patient_details') }}
                            </div>
                            <span id="app_user_status_badge" class="badge bg-light text-muted border small d-none"></span>
                        </div>
                        
                        {{-- الاسم الكامل --}}
                        <div class="mb-2.5">
                            <label class="form-label small fw-bold text-secondary mb-1">{{ __('messages.full_name') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3 pe-2" style="{{ $isArLocale ? 'border-radius: 0 16px 16px 0;' : 'border-radius: 16px 0 0 16px;' }}">
                                    <i class="bi bi-person fs-5"></i>
                                </span>
                                <input type="text" id="app_user_name" class="form-control app-input border-start-0 {{ $isArLocale ? 'text-end ps-3' : 'text-start pe-3' }}" 
                                       style="{{ $isArLocale ? 'border-radius: 16px 0 0 16px;' : 'border-radius: 0 16px 16px 0;' }}" 
                                       placeholder="{{ __('messages.full_name') }}" 
                                       value="{{ Auth::check() ? Auth::user()->name : '' }}" 
                                       oninput="savePatientBookingToStorage()" required>
                            </div>
                        </div>

                        {{-- رقم واتساب مع اختيار الدولة --}}
                        <div class="mb-2.5">
                            <label class="form-label small fw-bold text-secondary mb-1">{{ __('messages.whatsapp_number') }} <span class="text-danger">*</span></label>
                            <div class="input-group phone-input-group" dir="ltr">
                                <select class="form-select app-country-select" id="app_country_code" onchange="onModalCountryCodeChanged(this)">
                                    <option value="+964" selected>+964 ({{ $isArLocale ? 'العراق' : 'Iraq' }})</option>
                                    <option value="+966">+966 ({{ $isArLocale ? 'السعودية' : 'KSA' }})</option>
                                    <option value="+971">+971 ({{ $isArLocale ? 'الإمارات' : 'UAE' }})</option>
                                    <option value="+965">+965 ({{ $isArLocale ? 'الكويت' : 'Kuwait' }})</option>
                                    <option value="+974">+974 ({{ $isArLocale ? 'قطر' : 'Qatar' }})</option>
                                    <option value="+968">+968 ({{ $isArLocale ? 'عُمان' : 'Oman' }})</option>
                                    <option value="+973">+973 ({{ $isArLocale ? 'البحرين' : 'Bahrain' }})</option>
                                    <option value="+962">+962 ({{ $isArLocale ? 'الأردن' : 'Jordan' }})</option>
                                    <option value="+20">+20 ({{ $isArLocale ? 'مصر' : 'Egypt' }})</option>
                                    <option value="+961">+961 ({{ $isArLocale ? 'لبنان' : 'Lebanon' }})</option>
                                    <option value="+963">+963 ({{ $isArLocale ? 'سوريا' : 'Syria' }})</option>
                                    <option value="+970">+970 ({{ $isArLocale ? 'فلسطين' : 'Palestine' }})</option>
                                    <option value="+967">+967 ({{ $isArLocale ? 'اليمن' : 'Yemen' }})</option>
                                    <option value="+218">+218 ({{ $isArLocale ? 'ليبيا' : 'Libya' }})</option>
                                    <option value="+249">+249 ({{ $isArLocale ? 'السودان' : 'Sudan' }})</option>
                                    <option value="+213">+213 ({{ $isArLocale ? 'الجزائر' : 'Algeria' }})</option>
                                    <option value="+212">+212 ({{ $isArLocale ? 'المغرب' : 'Morocco' }})</option>
                                    <option value="+216">+216 ({{ $isArLocale ? 'تونس' : 'Tunisia' }})</option>
                                    <option value="+90">+90 ({{ $isArLocale ? 'تركيا' : 'Turkey' }})</option>
                                    <option value="+44">+44 ({{ $isArLocale ? 'بريطانيا' : 'UK' }})</option>
                                    <option value="+1">+1 ({{ $isArLocale ? 'أمريكا / كندا' : 'USA / Canada' }})</option>
                                    <option value="+49">+49 ({{ $isArLocale ? 'ألمانيا' : 'Germany' }})</option>
                                    <option value="+46">+46 ({{ $isArLocale ? 'السويد' : 'Sweden' }})</option>
                                    <option value="+33">+33 ({{ $isArLocale ? 'فرنسا' : 'France' }})</option>
                                    <option value="+31">+31 ({{ $isArLocale ? 'هولندا' : 'Netherlands' }})</option>
                                    <option value="+61">+61 ({{ $isArLocale ? 'أستراليا' : 'Australia' }})</option>
                                    <option value="+41">+41 ({{ $isArLocale ? 'سويسرا' : 'Switzerland' }})</option>
                                    <option value="+43">+43 ({{ $isArLocale ? 'النمسا' : 'Austria' }})</option>
                                    <option value="+47">+47 ({{ $isArLocale ? 'النرويج' : 'Norway' }})</option>
                                    <option value="+45">+45 ({{ $isArLocale ? 'الدنمارك' : 'Denmark' }})</option>
                                    <option value="+32">+32 ({{ $isArLocale ? 'بلجيكا' : 'Belgium' }})</option>
                                    <option value="+39">+39 ({{ $isArLocale ? 'إيطاليا' : 'Italy' }})</option>
                                    <option value="+34">+34 ({{ $isArLocale ? 'إسبانيا' : 'Spain' }})</option>
                                </select>
                                <input type="tel" id="app_user_phone" class="form-control app-input phone-number-input" placeholder="7700000000" value="{{ Auth::check() ? preg_replace('/^\+964/', '', Auth::user()->phone ?? '') : '' }}" oninput="savePatientBookingToStorage(); checkUserRegistrationStatus();" required>
                            </div>
                        </div>

                        {{-- البريد الإلكتروني --}}
                        <div class="mb-2.5">
                            <label class="form-label small fw-bold text-secondary mb-1">{{ __('messages.email_label') }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3 pe-2" style="{{ $isArLocale ? 'border-radius: 0 16px 16px 0;' : 'border-radius: 16px 0 0 16px;' }}">
                                    <i class="bi bi-envelope fs-5"></i>
                                </span>
                                <input type="email" id="app_user_email" class="form-control app-input border-start-0 {{ $isArLocale ? 'text-end ps-3' : 'text-start pe-3' }}" 
                                       style="{{ $isArLocale ? 'border-radius: 16px 0 0 16px;' : 'border-radius: 0 16px 16px 0;' }}" 
                                       placeholder="name@example.com" 
                                       value="{{ Auth::check() ? Auth::user()->email : '' }}" 
                                       oninput="savePatientBookingToStorage()">
                            </div>
                        </div>

                        {{-- كلمة المرور للمستخدم الجديد --}}
                        <div class="mb-2.5" id="app_password_wrapper" style="{{ Auth::check() ? 'display:none;' : '' }}">
                            <label class="form-label small fw-bold text-secondary mb-1" id="app_password_label">{{ __('messages.password') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3 pe-2" style="{{ $isArLocale ? 'border-radius: 0 16px 16px 0;' : 'border-radius: 16px 0 0 16px;' }}">
                                    <i class="bi bi-lock fs-5"></i>
                                </span>
                                <input type="password" id="app_user_password" class="form-control app-input border-start-0 {{ $isArLocale ? 'text-end ps-3' : 'text-start pe-3' }}" 
                                       style="{{ $isArLocale ? 'border-radius: 16px 0 0 16px;' : 'border-radius: 0 16px 16px 0;' }}" 
                                       placeholder="{{ __('messages.password') }}" minlength="6">
                            </div>
                            <div class="form-text text-muted small {{ $isArLocale ? 'text-end' : 'text-start' }}" id="app_password_hint">{{ __('messages.password_hint') }}</div>
                        </div>
                    </div>

                    {{-- 6. اختيار التاريخ والوقت --}}
                    <div class="mb-3 pt-2 border-top">
                        <div class="app-section-title fs-6 fw-black text-dark mb-2">
                            <i class="bi bi-calendar3 text-primary me-1"></i> {{ __('messages.date_time_title') }}
                        </div>

                        {{-- صندوق التقويم --}}
                        <div class="app-calendar-box">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <button type="button" class="btn btn-sm btn-light rounded-circle" onclick="changeAppMonth(-1)"><i class="bi bi-chevron-right"></i></button>
                                <div class="app-calendar-month mb-0" id="app-calendar-month-title">{{ date('F Y') }}</div>
                                <button type="button" class="btn btn-sm btn-light rounded-circle" onclick="changeAppMonth(1)"><i class="bi bi-chevron-left"></i></button>
                            </div>
                            <div class="app-calendar-weekdays">
                                @if($isArLocale)
                                    <div>أحد</div><div>إثن</div><div>ثلا</div><div>أرب</div><div>خميس</div><div>جمع</div><div>سبت</div>
                                @else
                                    <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
                                @endif
                            </div>
                            <div class="app-calendar-days" id="app-calendar-days-grid"></div>
                        </div>

                        {{-- الأوقات المتاحة --}}
                        <div class="mb-3">
                            <div class="app-section-title"><i class="bi bi-clock me-1 text-primary"></i> {{ __('messages.select_time') }}</div>
                            <div class="app-slots-grid" id="app-slots-grid">
                                <div class="app-slot-pill selected" onclick="selectAppSlot('09:00 AM', this)">{{ $isArLocale ? '09:00 ص' : '09:00 AM' }}</div>
                                <div class="app-slot-pill" onclick="selectAppSlot('10:00 AM', this)">{{ $isArLocale ? '10:00 ص' : '10:00 AM' }}</div>
                                <div class="app-slot-pill" onclick="selectAppSlot('11:30 AM', this)">{{ $isArLocale ? '11:30 ص' : '11:30 AM' }}</div>
                                <div class="app-slot-pill" onclick="selectAppSlot('01:00 PM', this)">{{ $isArLocale ? '01:00 م' : '01:00 PM' }}</div>
                                <div class="app-slot-pill" onclick="selectAppSlot('02:30 PM', this)">{{ $isArLocale ? '02:30 م' : '02:30 PM' }}</div>
                                <div class="app-slot-pill" onclick="selectAppSlot('04:00 PM', this)">{{ $isArLocale ? '04:00 م' : '04:00 PM' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Bottom Action Bar for Screen 1 --}}
                    <div class="mobile-app-bottom-bar">
                        <div>
                            <div class="app-total-label">{{ __('messages.order_total') }}</div>
                            <div class="app-total-value" id="app-bottom-total">{{ $modalServices->first()->price ?? 50 }} {{ \App\Models\Setting::currencySymbol() }}</div>
                        </div>
                        <button type="button" class="btn-app-primary d-flex align-items-center gap-2" onclick="goToAppScreen2()">
                            <span>{{ __('messages.next_to_payment') }}</span>
                            <i class="bi {{ $isArLocale ? 'bi-arrow-left' : 'bi-arrow-right' }}"></i>
                        </button>
                    </div>

                </div>{{-- End Screen 1 --}}

                {{-- ═══════════════════════════════════════════════════════════
                     الخطوة الثانية (Step 2): الملخص وتفاصيل الاستشارة والدفع
                     ═══════════════════════════════════════════════════════════ --}}
                <div id="app-screen-2" class="d-none">
                    
                    {{-- ═══ اختيار طريقة الدفع بتصميم Toggle Switch الفاخر ═══ --}}
                    @if($anyPaymentActive)
                    <div class="mb-2.5 pt-1" dir="{{ $isArLocale ? 'rtl' : 'ltr' }}">
                        <div class="d-flex justify-content-between align-items-center mb-1.5">
                            <div class="app-section-title fw-black text-dark mb-0" style="font-size:0.85rem;">
                                <i class="bi bi-wallet2 text-primary me-1"></i> {{ __('messages.payment_method') }}
                            </div>
                            <span class="text-muted" style="font-size:0.72rem;">{{ __('messages.select_payment_sub') }}</span>
                        </div>
                        
                        {{-- Segmented Toggle Switcher --}}
                        <div class="pay-toggle-switcher mb-2.5" id="payment-method-tabs" role="tablist">
                            @if($payZainEnabled)
                            <button type="button" class="pay-toggle-btn {{ $defaultPayMethod === 'zaincash' ? 'active zain' : '' }}"
                                    id="pay-tab-zaincash" onclick="switchPayTab('zaincash')">
                                <span class="pay-toggle-icon zain"><i class="bi bi-wallet2"></i></span>
                                <span>{{ __('messages.pay_zaincash') }}</span>
                            </button>
                            @endif
                            @if($paySuperkiEnabled)
                            <button type="button" class="pay-toggle-btn {{ $defaultPayMethod === 'superki' ? 'active superki' : '' }}"
                                    id="pay-tab-superki" onclick="switchPayTab('superki')">
                                <span class="pay-toggle-icon superki"><i class="bi bi-qr-code-scan"></i></span>
                                <span>{{ __('messages.pay_superki') }}</span>
                            </button>
                            @endif
                            @if($payCardEnabled)
                            <button type="button" class="pay-toggle-btn {{ $defaultPayMethod === 'card' ? 'active card' : '' }}"
                                    id="pay-tab-card" onclick="switchPayTab('card')">
                                <span class="pay-toggle-icon card"><i class="bi bi-credit-card-2-front"></i></span>
                                <span>{{ __('messages.pay_card') }}</span>
                            </button>
                            @endif
                        </div>

                        {{-- ─── بانل زين كاش ─── --}}
                        @if($payZainEnabled)
                        <div id="pay-panel-zaincash" class="pay-panel {{ $defaultPayMethod !== 'zaincash' ? 'd-none' : '' }}">
                            <div class="pay-qr-card">
                                @if(!empty($payZainQr))
                                    <div class="pay-qr-img-box">
                                        <img src="{{ $payZainQr }}" alt="ZainCash QR">
                                    </div>
                                    <p class="pay-qr-label" dir="{{ $isArLocale ? 'rtl' : 'ltr' }}">{{ $payZainLabel }}</p>
                                @else
                                    <div class="py-2 text-muted text-center">
                                        <i class="bi bi-qr-code" style="font-size:2.2rem;color:#7c3aed;"></i>
                                        <p class="small mt-1 mb-0 fw-bold text-dark">{{ __('messages.pay_zaincash') }} QR</p>
                                        <p class="pay-qr-label">{{ $isArLocale ? 'افتح تطبيق زين كاش وامسح الرمز لإتمام الدفع، ثم أرسل لقطة شاشة الإيصال.' : 'Open ZainCash app, scan the code to pay, then upload the receipt screenshot.' }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        {{-- ─── بانل SuperKi ─── --}}
                        @if($paySuperkiEnabled)
                        <div id="pay-panel-superki" class="pay-panel {{ $defaultPayMethod !== 'superki' ? 'd-none' : '' }}">
                            <div class="pay-qr-card">
                                @if(!empty($paySuperkiQr))
                                    <div class="pay-qr-img-box">
                                        <img src="{{ $paySuperkiQr }}" alt="SuperKi QR">
                                    </div>
                                    <p class="pay-qr-label" dir="{{ $isArLocale ? 'rtl' : 'ltr' }}">{{ $paySuperkiLabel }}</p>
                                @else
                                    <div class="py-2 text-muted text-center">
                                        <i class="bi bi-qr-code" style="font-size:2.2rem;color:#0284c7;"></i>
                                        <p class="small mt-1 mb-0 fw-bold text-dark">SuperKi QR</p>
                                        <p class="pay-qr-label">{{ $isArLocale ? 'افتح تطبيق SuperKi وامسح الرمز لإتمام الدفع، ثم أرسل لقطة شاشة الإيصال.' : 'Open SuperKi app, scan the code to pay, then upload the receipt screenshot.' }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        {{-- ─── بانل فيزا وماستر كارد ─── --}}
                        @if($payCardEnabled)
                        <div id="pay-panel-card" class="pay-panel {{ $defaultPayMethod !== 'card' ? 'd-none' : '' }}">
                            <div class="pay-qr-card {{ $isArLocale ? 'text-start' : 'text-end' }}" dir="{{ $isArLocale ? 'rtl' : 'ltr' }}">
                                <div class="d-flex align-items-center justify-content-between mb-1.5 border-bottom pb-1.5">
                                    <div class="fw-bold text-dark" style="font-size:0.8rem;">
                                        <i class="bi bi-shield-check text-success me-1"></i> {{ __('messages.pay_card') }}
                                    </div>
                                    <div class="d-flex align-items-center gap-1" dir="ltr">
                                        <span class="badge bg-white px-2 py-0.5 shadow-sm border text-primary fw-bold" style="font-size: 0.72rem;">VISA</span>
                                        <span class="badge bg-white px-2 py-0.5 shadow-sm border text-danger fw-bold" style="font-size: 0.72rem;">MasterCard</span>
                                    </div>
                                </div>
                                <p class="pay-qr-label text-secondary mb-2" style="{{ $isArLocale ? 'text-align:right;' : 'text-align:left;' }}">{{ $payCardInstructions }}</p>
                                @if(!empty($payCardLink))
                                <a href="{{ $payCardLink }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill fw-bold w-100 py-1.5" style="font-size:0.82rem;">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> {{ $isArLocale ? 'فتح رابط الدفع الإلكتروني المباشر' : 'Open Direct Payment Link' }}
                                </a>
                                @endif
                            </div>
                        </div>
                        @endif

                        {{-- ═══ كارت إثبات التحويل المالي وسكرين شوت الإيصال ═══ --}}
                        <div class="pay-proof-card" dir="{{ $isArLocale ? 'rtl' : 'ltr' }}">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="form-label fw-bold text-dark mb-0" style="font-size:0.78rem;">
                                    <i class="bi bi-phone-vibrate text-primary me-1"></i> {{ __('messages.transfer_number_label') }}
                                </label>
                                <span class="badge bg-light text-muted border" style="font-size: 0.68rem;">{{ __('messages.optional') }}</span>
                            </div>
                            <div class="input-group mb-1">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-2.5 pe-2" style="{{ $isArLocale ? 'border-radius: 0 12px 12px 0;' : 'border-radius: 12px 0 0 12px;' }}">
                                    <i class="bi bi-hash fs-6"></i>
                                </span>
                                <input type="tel" id="app_transfer_number" class="form-control app-input-sm border-start-0 {{ $isArLocale ? 'text-end ps-2.5' : 'text-start pe-2.5' }}" 
                                       style="{{ $isArLocale ? 'border-radius: 12px 0 0 12px;' : 'border-radius: 0 12px 12px 0;' }}"
                                       placeholder="{{ __('messages.transfer_number_ph') }}">
                            </div>
                            <div class="form-text text-muted mb-2.5 {{ $isArLocale ? 'text-end' : 'text-start' }}" style="font-size: 0.72rem;">
                                <i class="bi bi-info-circle text-primary me-1"></i> {{ __('messages.transfer_number_helper') }}
                            </div>

                            {{-- Upload Screenshot Area --}}
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="form-label fw-bold text-dark mb-0" style="font-size:0.78rem;">
                                    <i class="bi bi-image text-success me-1"></i> {{ __('messages.receipt_upload_label') }}
                                </label>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25" style="font-size: 0.68rem;">{{ __('messages.speeds_confirmation') }}</span>
                            </div>

                            {{-- Custom Upload Box --}}
                            <div id="receiptUploadBox" class="pay-upload-dropzone" onclick="document.getElementById('app_receipt_file').click()">
                                <input type="file" id="app_receipt_file" class="d-none" accept="image/*" onchange="onReceiptImageSelected(this)">
                                
                                {{-- Placeholder View --}}
                                <div id="receiptPlaceholderView" class="py-1">
                                    <i class="bi bi-cloud-arrow-up-fill text-primary fs-4 d-block mb-0.5"></i>
                                    <div class="fw-bold text-dark" style="font-size:0.8rem;">{{ __('messages.receipt_dropzone_title') }}</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">
                                        {{ __('messages.receipt_dropzone_sub') }}
                                    </div>
                                </div>

                                {{-- Preview View --}}
                                <div id="receiptPreviewView" class="d-none align-items-center justify-content-between gap-2 text-start">
                                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                                        <img id="receiptPreviewImg" src="" alt="Receipt Preview" class="rounded-2 border" style="width: 40px; height: 40px; object-fit: cover;">
                                        <div class="overflow-hidden {{ $isArLocale ? 'text-end' : 'text-start' }}">
                                            <div class="fw-bold text-dark text-truncate" id="receiptFileName" style="font-size:0.78rem;">receipt.png</div>
                                            <div class="text-success" style="font-size: 0.7rem;"><i class="bi bi-check-circle-fill me-1"></i> {{ __('messages.receipt_attached_success') }}</div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-circle p-1" onclick="event.stopPropagation(); removeReceiptImage();" title="Remove" style="width:26px;height:26px; display:flex; align-items:center; justify-content:center;">
                                        <i class="bi bi-trash3-fill" style="font-size:0.75rem;"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Terms check --}}
                    <div class="form-check my-2" dir="{{ $isArLocale ? 'rtl' : 'ltr' }}">
                        <input class="form-check-input" type="checkbox" id="app_terms_check" checked style="cursor:pointer;">
                        <label class="form-check-label fw-bold text-secondary" for="app_terms_check" style="font-size:0.78rem; cursor:pointer;">
                            {{ __('messages.terms_agree') }}
                        </label>
                    </div>

                    {{-- Bottom Action Bar for Screen 2 --}}
                    <div class="mobile-app-bottom-bar">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-1.5 fw-bold d-flex align-items-center gap-1" onclick="goToAppScreen1()" style="font-size:0.85rem;">
                            <i class="bi {{ $isArLocale ? 'bi-arrow-right' : 'bi-arrow-left' }}"></i>
                            <span>{{ __('messages.previous') }}</span>
                        </button>
                        <button type="button" class="btn-app-primary flex-fill d-flex align-items-center justify-content-center gap-2" id="app-submit-pay-btn" onclick="executeAppBooking()" style="white-space:nowrap; font-size:0.86rem; padding:0.65rem 0.9rem;">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>{{ __('messages.confirm_and_send_receipt') }}</span>
                            <span class="badge bg-white bg-opacity-25 rounded-pill px-2 py-0.5 fw-bold ms-1" id="app-btn-price-display">{{ $modalServices->first()->price ?? 50 }} {{ \App\Models\Setting::currencySymbol() }}</span>
                        </button>
                    </div>

                </div>{{-- End Screen 2 --}}

                {{-- ═══════════════════════════════════════════════════════════
                     الخطوة الثالثة (Screen 3): تأكيد تسجيل الحجز وبطاقة الموعد الفاخرة
                     ═══════════════════════════════════════════════════════════ --}}
                <div id="app-screen-3" class="d-none">
                    <div class="text-center py-1">
                        {{-- Top Glowing Animated Status Icon --}}
                        <div class="luxury-status-circle">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold mb-1" style="font-size:0.78rem;">
                            <i class="bi bi-clock-history me-1"></i> {{ __('messages.booking_under_review') }}
                        </span>
                        <h5 class="fw-black text-dark mb-2" style="font-size: 1.15rem;">{{ __('messages.booking_success_title') }}</h5>
                        
                        {{-- إشعار المراجعة والتأكيد الأنيق --}}
                        <div class="luxury-notice-box" dir="{{ $isArLocale ? 'rtl' : 'ltr' }}">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-info-circle-fill text-warning fs-5 flex-shrink-0 mt-0.5"></i>
                                <div style="line-height:1.55;font-size:0.83rem;flex-grow:1;{{ $isArLocale ? 'text-align:right;' : 'text-align:left;' }}">
                                    <div class="fw-bold" style="color:#b45309;">{{ __('messages.review_alert_title') }}</div>
                                    <div style="color:#78350f;">
                                        {!! __('messages.review_alert_text') !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- بطاقة تذكرة الموعد الإلكترونية الفاخرة --}}
                        <div class="luxury-voucher-card" dir="{{ $isArLocale ? 'rtl' : 'ltr' }}">
                            {{-- رأس التذكرة --}}
                            <div class="voucher-header">
                                <div class="d-flex align-items-center gap-1.5">
                                    <i class="bi bi-ticket-perforated-fill text-primary fs-6"></i>
                                    <span class="fw-bold text-secondary" style="font-size: 0.82rem;">{{ __('messages.e_ticket_title') }}</span>
                                </div>
                                <span class="voucher-ref-badge" id="app-res-ref">#BK-REF</span>
                            </div>

                            {{-- محتوى التذكرة --}}
                            <div class="voucher-body">
                                <div class="voucher-row">
                                    <span class="voucher-label">
                                        <i class="bi bi-heart-pulse-fill"></i> {{ __('messages.ticket_service') }}
                                    </span>
                                    <span class="voucher-val" id="app-res-service">{{ $modalServices->first()->title ?? 'جلسة استشارة' }}</span>
                                </div>

                                <div class="voucher-row">
                                    <span class="voucher-label">
                                        <i class="bi bi-calendar-check-fill"></i> {{ __('messages.ticket_datetime') }}
                                    </span>
                                    <span class="voucher-val font-monospace" id="app-res-datetime">—</span>
                                </div>

                                <div class="voucher-row">
                                    <span class="voucher-label">
                                        <i class="bi bi-credit-card-2-front-fill"></i> {{ __('messages.ticket_paymethod') }}
                                    </span>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill fw-bold" id="app-res-paymethod">{{ __('messages.pay_zaincash') }}</span>
                                </div>

                                {{-- شريط الإجمالي المميز --}}
                                <div class="voucher-total-box">
                                    <span class="voucher-total-label">{{ __('messages.ticket_amount') }}</span>
                                    <span class="voucher-total-amount" id="app-res-type">50 {{ \App\Models\Setting::currencySymbol() }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- إشعار الحساب والمتابعة --}}
                        <div class="rounded-pill px-3 py-1.5 mb-2.5 d-flex align-items-center justify-content-center gap-2 text-success small fw-bold" style="background:#ecfdf5; border: 1px solid #a7f3d0; font-size:0.8rem;">
                            <i class="bi bi-check-circle-fill fs-6"></i>
                            <span>{{ __('messages.account_auto_created') }}</span>
                        </div>

                        {{-- أزرار الإجراءات المتناسقة الفاخرة --}}
                        <div class="d-flex flex-column gap-2">
                            {{-- زر الانتقال المباشر للداشبورد --}}
                            <a id="app-dashboard-link" href="{{ route('patient.dashboard') }}" class="btn-voucher-primary">
                                <i class="bi bi-speedometer2 fs-5"></i>
                                <span>{{ __('messages.go_to_dashboard') }}</span>
                                <i class="bi {{ $isArLocale ? 'bi-arrow-left' : 'bi-arrow-right' }} ms-auto"></i>
                            </a>

                            {{-- زر إغلاق البوب اب والعودة للرئيسية --}}
                            <button type="button" class="btn-voucher-secondary" data-bs-dismiss="modal" onclick="window.location.reload()">
                                <span>{{ __('messages.close_and_return_home') }}</span>
                            </button>
                        </div>
                    </div>
                </div>{{-- End Screen 3 --}}

            </div>
        </div>
    </div>
</div>

<script>
// ════ Shared Booking Modal JS Engine ════
const _initDate = new Date();
const _initYear = _initDate.getFullYear();
const _initMonth = _initDate.getMonth();
const _initDay = _initDate.getDate();
const _initDateStr = `${_initYear}-${String(_initMonth + 1).padStart(2, '0')}-${String(_initDay).padStart(2, '0')}`;

let appState = {
    serviceId: {{ $modalServices->first()->id ?? 1 }},
    duration: {{ $modalServices->first()->duration ?? 45 }},
    price: {{ $modalServices->first()->price ?? 50 }},
    bookingType: 'online',
    paymentMethod: '{{ $defaultPayMethod }}',
    title: '{{ $modalServices->first()->title ?? "استشارة نفسية متخصصة" }}',
    details: '',
    date: _initDateStr,
    slot: '',
    year: _initYear,
    month: _initMonth,
};

const appMonthNames = @json($isArLocale ? [
    'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو',
    'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'
] : [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
]);

const waNumber = '{{ preg_replace("/\D/", "", $waRaw) }}';
let appUserIsRegistered = {{ Auth::check() ? 'true' : 'false' }};
let checkPhoneTimeout = null;
const appCurrencySymbol = '{{ \App\Models\Setting::currencySymbol() }}';

function setModalBookingType(type, btn) {
    appState.bookingType = type;
    document.getElementById('modalBtnOnline').classList.toggle('active', type === 'online');
    document.getElementById('modalBtnClinic').classList.toggle('active', type === 'clinic');
    
    // Filter services dropdown based on category type
    const select = document.getElementById('app_service_select');
    if (select) {
        let firstMatch = null;
        for (let i = 0; i < select.options.length; i++) {
            const opt = select.options[i];
            const optType = opt.getAttribute('data-type') || 'both';
            const matches = (type === 'online' && (optType === 'online' || optType === 'both')) ||
                            (type === 'clinic' && (optType === 'clinic' || optType === 'both'));
            
            opt.style.display = matches ? '' : 'none';
            opt.disabled = !matches;

            if (matches && !firstMatch) {
                firstMatch = opt;
            }
        }

        if (firstMatch) {
            select.value = firstMatch.value;
            onModalServiceChanged(select);
        }
    }
}

function onModalServiceChanged(selectEl) {
    const opt = selectEl.options[selectEl.selectedIndex];
    if (!opt) return;
    
    appState.serviceId = selectEl.value;
    appState.title = opt.getAttribute('data-title') || opt.text;
    const dur = parseInt(opt.getAttribute('data-duration')) || 45;
    appState.duration = dur;

    // Update dynamic duration badge
    const badge = document.getElementById('app_modal_duration_badge');
    if (badge) badge.textContent = dur + ' ' + '{{ __("messages.minutes") }}';
    
    const p = appState.bookingType === 'clinic'
        ? (opt.getAttribute('data-clinic') || opt.getAttribute('data-price'))
        : (opt.getAttribute('data-price') || opt.getAttribute('data-video') || opt.getAttribute('data-voice') || opt.getAttribute('data-chat'));
    if (p) updateModalPrice(parseFloat(p));
    
    const titleInput = document.getElementById('app_consultation_title');
    if (titleInput) titleInput.value = appState.title;

    // Refresh slots
    fetchModalSlots(appState.date);
}

function updateModalPrice(price) {
    appState.price = price;
    const pText = price + ' ' + appCurrencySymbol;
    if (document.getElementById('app-required-price')) document.getElementById('app-required-price').textContent = pText;
    if (document.getElementById('app-bottom-total')) document.getElementById('app-bottom-total').textContent = pText;
    if (document.getElementById('app-btn-price-display')) document.getElementById('app-btn-price-display').textContent = pText;
    if (document.getElementById('app-res-type')) document.getElementById('app-res-type').textContent = pText;
}

// ─── Payment Tab Switcher ───────────────────────────────────────
function switchPayTab(method) {
    appState.paymentMethod = method;
    // Hide all panels
    document.querySelectorAll('.pay-panel').forEach(p => p.classList.add('d-none'));
    // Show selected panel
    const panel = document.getElementById('pay-panel-' + method);
    if (panel) panel.classList.remove('d-none');

    // Reset all toggle buttons
    document.querySelectorAll('.pay-toggle-btn').forEach(btn => {
        btn.classList.remove('active', 'zain', 'superki', 'card');
    });
    // Activate clicked button
    const activeBtn = document.getElementById('pay-tab-' + method);
    if (activeBtn) {
        activeBtn.classList.add('active', method);
    }
}

function selectServiceAndOpenModal(id, title, price, duration, categoryType) {
    const selectedType = categoryType || 'online';
    setModalBookingType(selectedType);
    
    appState.serviceId = id;
    const dur = duration || 45;
    appState.duration = dur;
    appState.title = title || (selectedType === 'clinic' ? '{{ $isArLocale ? "كشف واستشارة بالعيادة" : "In-Clinic Consultation" }}' : '{{ $isArLocale ? "استشارة نفسية أونلاين" : "Online Consultation" }}');

    const badge = document.getElementById('app_modal_duration_badge');
    if (badge) badge.textContent = dur + ' ' + '{{ __("messages.minutes") }}';
    
    const select = document.getElementById('app_service_select');
    if (select) {
        select.value = id;
    }
    
    const titleInput = document.getElementById('app_consultation_title');
    if (titleInput) titleInput.value = appState.title;
    
    if (price) updateModalPrice(price);
    
    // Ensure screen 1 is active and restore patient info
    document.getElementById('app-screen-1')?.classList.remove('d-none');
    document.getElementById('app-screen-2')?.classList.add('d-none');
    document.getElementById('app-screen-3')?.classList.add('d-none');
    const modalBody = document.querySelector('.mobile-app-body');
    if (modalBody) {
        modalBody.classList.remove('screen-3-active');
        modalBody.scrollTop = 0;
    }
    restorePatientBookingData();
    renderAppCalendar();
    fetchModalSlots(appState.date);

    const modalEl = document.getElementById('bookingModal');
    if (modalEl) {
        const bsModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        bsModal.show();
    }
}

function goToAppScreen2() {
    const titleInput = document.getElementById('app_consultation_title');
    const nameInput = document.getElementById('app_user_name');
    const phoneInput = document.getElementById('app_user_phone');
    const passInput = document.getElementById('app_user_password');
    const countryCodeSelect = document.getElementById('app_country_code');
    const countryCode = countryCodeSelect ? countryCodeSelect.value : '+964';

    const title = titleInput ? titleInput.value.trim() : '';
    if (!title) {
        alert(@json(__('messages.enter_subject_warning')));
        if (titleInput) titleInput.focus();
        return;
    }
    appState.title = title;

    const name = nameInput ? nameInput.value.trim() : '';
    if (!name) {
        alert(@json(__('messages.enter_name_warning')));
        if (nameInput) nameInput.focus();
        return;
    }

    const rawPhone = phoneInput ? phoneInput.value.trim().replace(/^0+/, '') : '';
    if (!rawPhone || rawPhone.length < 6) {
        alert(@json(__('messages.enter_phone_warning')));
        if (phoneInput) phoneInput.focus();
        return;
    }

    if (!appState.slot) {
        alert(@json(__('messages.select_slot_warning')));
        return;
    }

    const password = passInput ? passInput.value.trim() : '';
    if (!appUserIsRegistered && (!password || password.length < 6)) {
        alert(@json(__('messages.enter_password_warning')));
        if (passInput) passInput.focus();
        return;
    }

    savePatientBookingToStorage();

    // Populate Screen 2 summary
    const summaryService = document.getElementById('step2_summary_service');
    if (summaryService) summaryService.textContent = appState.title;

    const summaryDatetime = document.getElementById('step2_summary_datetime');
    if (summaryDatetime) summaryDatetime.textContent = `${appState.date} • ${appState.slot}`;

    const summaryPatient = document.getElementById('step2_summary_patient');
    if (summaryPatient) summaryPatient.textContent = `${name} (${countryCode}${rawPhone})`;

    // Transition to Screen 2
    document.getElementById('app-screen-1').classList.add('d-none');
    document.getElementById('app-screen-2').classList.remove('d-none');
    const modalBody = document.querySelector('.mobile-app-body');
    if (modalBody) {
        modalBody.classList.remove('screen-3-active');
        modalBody.scrollTop = 0;
    }
}

function goToAppScreen1() {
    document.getElementById('app-screen-2').classList.add('d-none');
    document.getElementById('app-screen-1').classList.remove('d-none');
    const modalBody = document.querySelector('.mobile-app-body');
    if (modalBody) {
        modalBody.classList.remove('screen-3-active');
        modalBody.scrollTop = 0;
    }
}

function changeAppMonth(offset) {
    const today = new Date();
    const targetMonth = appState.month + offset;
    const targetYear = appState.year + (targetMonth > 11 ? 1 : (targetMonth < 0 ? -1 : 0));
    const normalizedMonth = (targetMonth + 12) % 12;

    // Prevent navigating before current year and month
    if (targetYear < today.getFullYear() || (targetYear === today.getFullYear() && normalizedMonth < today.getMonth())) {
        return;
    }

    appState.month = normalizedMonth;
    appState.year = targetYear;
    renderAppCalendar();
}

function renderAppCalendar() {
    const titleEl = document.getElementById('app-calendar-month-title');
    if (titleEl) titleEl.textContent = appMonthNames[appState.month] + ' ' + appState.year;
    const daysGrid = document.getElementById('app-calendar-days-grid');
    if (!daysGrid) return;
    daysGrid.innerHTML = '';

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const firstDay = new Date(appState.year, appState.month, 1).getDay();
    const daysInMonth = new Date(appState.year, appState.month + 1, 0).getDate();

    for (let i = 0; i < firstDay; i++) {
        const empty = document.createElement('div');
        daysGrid.appendChild(empty);
    }

    for (let d = 1; d <= daysInMonth; d++) {
        const thisDate = new Date(appState.year, appState.month, d);
        thisDate.setHours(0, 0, 0, 0);

        const isPast = thisDate < today;
        const isToday = thisDate.getTime() === today.getTime();

        const mm = (appState.month + 1).toString().padStart(2, '0');
        const dd = d.toString().padStart(2, '0');
        const formattedDate = `${appState.year}-${mm}-${dd}`;

        const dayEl = document.createElement('div');
        dayEl.textContent = d;

        if (isPast) {
            dayEl.className = 'app-calendar-day disabled-day';
            dayEl.title = @json($isArLocale ? 'تاريخ سابق غير متاح' : 'Past date unavailable');
        } else {
            const isSelected = (formattedDate === appState.date);
            dayEl.className = 'app-calendar-day' + (isSelected ? ' selected' : '') + (isToday ? ' today-day' : '');

            dayEl.onclick = function() {
                document.querySelectorAll('.app-calendar-day').forEach(el => el.classList.remove('selected'));
                dayEl.classList.add('selected');
                appState.date = formattedDate;
                fetchModalSlots(appState.date);
            };
        }
        daysGrid.appendChild(dayEl);
    }
}

function fetchModalSlots(dateStr) {
    const container = document.getElementById('app-slots-grid');
    if (!container) return;

    container.innerHTML = `<div class="text-center py-3 w-100" style="grid-column: 1 / -1;"><div class="spinner-border spinner-border-sm text-primary me-2"></div><span class="text-muted small">${@json(__('messages.loading_slots'))}</span></div>`;

    fetch(`{{ url('/api/slots') }}?service_id=${appState.serviceId}&date=${dateStr}`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        container.innerHTML = '';
        const slots = data?.slots || (Array.isArray(data) ? data : []);

        if (!slots || slots.length === 0) {
            container.innerHTML = `<div class="alert alert-warning border-0 small py-2.5 px-3 mb-0 w-100 text-center" style="grid-column: 1 / -1;"><i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> ${@json(__('messages.no_slots_warning'))}</div>`;
            appState.slot = '';
            return;
        }

        slots.forEach((s, idx) => {
            const rawTime = typeof s === 'string' ? s : (s.start || s.formatted || '');
            if (!rawTime) return;

            // Format to 12-hour format with localization
            let displayTime = rawTime;
            const parts = rawTime.split(':');
            if (parts.length >= 2) {
                let hour = parseInt(parts[0], 10);
                const min = parts[1];
                const period = hour >= 12 ? '{{ $isArLocale ? "م" : "PM" }}' : '{{ $isArLocale ? "ص" : "AM" }}';
                hour = hour % 12 || 12;
                displayTime = `${hour.toString().padStart(2, '0')}:${min} ${period}`;
            }

            const pill = document.createElement('div');
            pill.className = 'app-slot-pill' + (idx === 0 ? ' selected' : '');
            pill.textContent = displayTime;
            pill.onclick = function() {
                selectAppSlot(displayTime, pill);
            };

            container.appendChild(pill);

            if (idx === 0) {
                appState.slot = displayTime;
            }
        });
    })
    .catch(() => {
        container.innerHTML = `<div class="text-center text-muted small py-2 w-100" style="grid-column: 1 / -1;">${@json(__('messages.failed_slots'))}</div>`;
    });
}

function selectAppSlot(time, el) {
    appState.slot = time;
    document.querySelectorAll('.app-slot-pill').forEach(p => p.classList.remove('selected'));
    if (el) el.classList.add('selected');
}

function onModalCountryCodeChanged(selectEl) {
    const opt = selectEl.options[selectEl.selectedIndex];
    const flagBadge = document.getElementById('app_country_flag_badge');
    const flag = opt ? (opt.getAttribute('data-flag') || '🇮🇶') : '🇮🇶';
    if (flagBadge) {
        flagBadge.textContent = flag;
    }
    localStorage.setItem('yonis_country_code', selectEl.value);
    localStorage.setItem('yonis_country_flag', flag);
    checkUserRegistrationStatus();
}

function savePatientBookingToStorage() {
    const nameInput = document.getElementById('app_user_name');
    const phoneInput = document.getElementById('app_user_phone');
    const emailInput = document.getElementById('app_user_email');
    const codeSelect = document.getElementById('app_country_code');
    const opt = codeSelect ? codeSelect.options[codeSelect.selectedIndex] : null;

    if (nameInput && nameInput.value.trim()) {
        localStorage.setItem('yonis_patient_name', nameInput.value.trim());
    }
    if (phoneInput && phoneInput.value.trim()) {
        localStorage.setItem('yonis_patient_phone', phoneInput.value.trim());
    }
    if (emailInput && emailInput.value.trim()) {
        localStorage.setItem('yonis_patient_email', emailInput.value.trim());
    }
    if (codeSelect && codeSelect.value) {
        localStorage.setItem('yonis_country_code', codeSelect.value);
        if (opt && opt.getAttribute('data-flag')) {
            localStorage.setItem('yonis_country_flag', opt.getAttribute('data-flag'));
        }
    }
}

function restorePatientBookingData() {
    const savedName = localStorage.getItem('yonis_patient_name');
    const savedPhone = localStorage.getItem('yonis_patient_phone');
    const savedCode = localStorage.getItem('yonis_country_code');
    const savedFlag = localStorage.getItem('yonis_country_flag');
    const savedEmail = localStorage.getItem('yonis_patient_email');

    const nameInput = document.getElementById('app_user_name');
    const phoneInput = document.getElementById('app_user_phone');
    const codeSelect = document.getElementById('app_country_code');
    const emailInput = document.getElementById('app_user_email');
    const flagBadge = document.getElementById('app_country_flag_badge');

    if (nameInput && (!nameInput.value || nameInput.value.trim() === '') && savedName) {
        nameInput.value = savedName;
    }
    if (phoneInput && (!phoneInput.value || phoneInput.value.trim() === '') && savedPhone) {
        phoneInput.value = savedPhone;
    }
    if (emailInput && (!emailInput.value || emailInput.value.trim() === '') && savedEmail) {
        emailInput.value = savedEmail;
    }
    if (codeSelect && savedCode) {
        codeSelect.value = savedCode;
        if (flagBadge) flagBadge.textContent = savedFlag || '🇮🇶';
    }

    if (phoneInput && phoneInput.value.trim().length >= 6) {
        checkUserRegistrationStatus();
    }
}

function checkUserRegistrationStatus() {
    const countryCode = document.getElementById('app_country_code') ? document.getElementById('app_country_code').value : '+964';
    const phoneInput = document.getElementById('app_user_phone');
    if (!phoneInput) return;
    let rawPhone = phoneInput.value.trim();
    rawPhone = rawPhone.replace(/^0+/, '');
    
    if (!rawPhone || rawPhone.length < 6) {
        const badge = document.getElementById('app_user_status_badge');
        if (badge) badge.classList.add('d-none');
        return;
    }

    clearTimeout(checkPhoneTimeout);
    checkPhoneTimeout = setTimeout(() => {
        const fullPhone = rawPhone.startsWith('+') ? rawPhone : (countryCode + rawPhone);
        
        fetch("{{ url('/api/checkout/check-user') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ phone: fullPhone })
        })
        .then(r => r.json())
        .then(res => {
            const badge = document.getElementById('app_user_status_badge');
            const passWrapper = document.getElementById('app_password_wrapper');
            const passInput = document.getElementById('app_user_password');
            const nameInput = document.getElementById('app_user_name');
            const emailInput = document.getElementById('app_user_email');

            if (badge) badge.classList.remove('d-none');
            if (res.is_registered) {
                appUserIsRegistered = true;
                if (badge) {
                    badge.className = 'badge bg-success-subtle text-success border border-success-subtle small';
                    badge.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> ' + @json(__('messages.existing_user_badge'));
                }
                if (passWrapper) passWrapper.style.display = 'none';
                if (passInput) passInput.value = '';
                
                // Populate name and email if available
                if (res.user) {
                    if (res.user.name && nameInput && (!nameInput.value || nameInput.value.trim() === '')) {
                        nameInput.value = res.user.name;
                    }
                    if (res.user.email && emailInput && (!emailInput.value || emailInput.value.trim() === '')) {
                        emailInput.value = res.user.email;
                    }
                }
                savePatientBookingToStorage();
            } else {
                appUserIsRegistered = false;
                if (badge) {
                    badge.className = 'badge bg-info-subtle text-primary border border-info-subtle small';
                    badge.innerHTML = '<i class="bi bi-person-plus-fill me-1"></i> ' + @json(__('messages.new_user_badge'));
                }
                if (passWrapper) passWrapper.style.display = 'block';
            }
        })
        .catch(() => {});
    }, 400);
}

function onReceiptImageSelected(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewImg = document.getElementById('receiptPreviewImg');
            const previewView = document.getElementById('receiptPreviewView');
            const placeholderView = document.getElementById('receiptPlaceholderView');
            const fileNameEl = document.getElementById('receiptFileName');
            
            if (previewImg) previewImg.src = e.target.result;
            if (fileNameEl) fileNameEl.textContent = file.name;
            if (placeholderView) placeholderView.classList.add('d-none');
            if (previewView) {
                previewView.classList.remove('d-none');
                previewView.classList.add('d-flex');
            }
        };
        reader.readAsDataURL(file);
    }
}

function removeReceiptImage() {
    const input = document.getElementById('app_receipt_file');
    if (input) input.value = '';
    const previewImg = document.getElementById('receiptPreviewImg');
    const previewView = document.getElementById('receiptPreviewView');
    const placeholderView = document.getElementById('receiptPlaceholderView');
    if (previewImg) previewImg.src = '';
    if (previewView) {
        previewView.classList.add('d-none');
        previewView.classList.remove('d-flex');
    }
    if (placeholderView) placeholderView.classList.remove('d-none');
}

function executeAppBooking() {
    const nameInput = document.getElementById('app_user_name');
    const phoneInput = document.getElementById('app_user_phone');
    const emailInput = document.getElementById('app_user_email');
    const passInput = document.getElementById('app_user_password');
    const detailsInput = document.getElementById('app_consultation_details');
    const transferNumInput = document.getElementById('app_transfer_number');
    const receiptFileInput = document.getElementById('app_receipt_file');
    const countryCode = document.getElementById('app_country_code') ? document.getElementById('app_country_code').value : '+964';
    const termsCheck = document.getElementById('app_terms_check');

    if (termsCheck && !termsCheck.checked) {
        alert(@json(__('messages.terms_required_warning')));
        return;
    }

    const name = nameInput ? nameInput.value.trim() : '';
    let rawPhone = phoneInput ? phoneInput.value.trim().replace(/^0+/, '') : '';
    const email = emailInput ? emailInput.value.trim() : '';
    const password = passInput ? passInput.value.trim() : '';
    const details = detailsInput ? detailsInput.value.trim() : '';

    if (!name || !rawPhone) {
        alert(@json(__('messages.enter_name_warning')));
        return;
    }

    if (!appState.slot) {
        alert(@json(__('messages.select_slot_warning')));
        return;
    }

    if (!appUserIsRegistered && (!password || password.length < 6)) {
        alert(@json(__('messages.enter_password_warning')));
        if (passInput) passInput.focus();
        return;
    }

    // Persist details for any future booking
    savePatientBookingToStorage();

    const btn = document.getElementById('app-submit-pay-btn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> ' + @json(__('messages.submitting_booking'));
    }

    const fullPhone = rawPhone.startsWith('+') ? rawPhone : (countryCode + rawPhone);
    const transferNumber = transferNumInput && transferNumInput.value.trim() ? transferNumInput.value.trim() : fullPhone;

    const selServiceOpt = document.getElementById('app_service_select') ? document.getElementById('app_service_select').selectedOptions[0] : null;
    const serviceChannel = selServiceOpt ? (selServiceOpt.getAttribute('data-channel') || 'video') : 'video';
    const consultationChannel = (serviceChannel === 'clinic') ? 'clinic' : (serviceChannel === 'all' ? 'video' : serviceChannel);

    const formData = new FormData();
    formData.append('service_id', appState.serviceId || 1);
    formData.append('booking_type', appState.bookingType || 'online');
    formData.append('consultation_type', consultationChannel);
    formData.append('date', appState.date);
    formData.append('start_time', appState.slot);
    formData.append('name', name);
    formData.append('phone', fullPhone);
    if (email) formData.append('email', email);
    if (password) formData.append('password', password);
    if (appState.title) formData.append('title', appState.title);
    if (details) formData.append('notes', details);
    formData.append('payment_method', appState.paymentMethod || 'zaincash');
    formData.append('transfer_number', transferNumber);

    if (receiptFileInput && receiptFileInput.files && receiptFileInput.files[0]) {
        formData.append('receipt_image', receiptFileInput.files[0]);
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '';
    const headers = {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
    };

    fetch("{{ url('/api/checkout/initialize') }}", { method: 'POST', headers, body: formData })
        .then(r => r.json())
        .then(data => {
            if (!data.success) throw new Error(data.message || @json(__('messages.request_failed')));
            
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> ' + @json(__('messages.confirm_and_send_receipt')) + ' (' + (appState.price || 50) + ' ' + appCurrencySymbol + ')';
            }

            const ref = data?.booking_reference || 'REF-' + Math.floor(1000 + Math.random() * 9000);

            // Store booking reference for payment confirmation
            appState.bookingRef = ref;

            // Transition UI to Screen 3 (Confirmation & WhatsApp)
            document.getElementById('app-screen-2').classList.add('d-none');
            document.getElementById('app-screen-3').classList.remove('d-none');
            const modalBody = document.querySelector('.mobile-app-body');
            if (modalBody) {
                modalBody.classList.add('screen-3-active');
                modalBody.scrollTop = 0;
            }

            if (document.getElementById('app-res-ref')) document.getElementById('app-res-ref').textContent = '#' + ref;
            if (document.getElementById('app-res-service')) document.getElementById('app-res-service').textContent = appState.title || '{{ $isArLocale ? "جلسة استشارة" : "Consultation Session" }}';
            if (document.getElementById('app-res-datetime')) document.getElementById('app-res-datetime').textContent = appState.date + ' | ' + appState.slot;
            if (document.getElementById('app-res-type')) document.getElementById('app-res-type').textContent = (appState.price || 50) + ' ' + appCurrencySymbol;

            const payMethodLabels = {
                zaincash: @json(__('messages.pay_zaincash')),
                superki: @json(__('messages.pay_superki')),
                card: @json(__('messages.pay_card'))
            };
            const methodLabel = payMethodLabels[appState.paymentMethod] || @json(__('messages.pay_zaincash'));
            if (document.getElementById('app-res-paymethod')) document.getElementById('app-res-paymethod').textContent = methodLabel;

            const waMsg = @json($isArLocale)
                ? `السلام عليكم دكتور يونس، تم تسجيل طلب حجز موعد مؤكد\nرقم المرجع: #${ref}\nالاسم: ${name}\nالخدمة: ${appState.title}\nالموعد: ${appState.date} (${appState.slot})\nطريقة الدفع: ${methodLabel}\nالمبلغ: ${appState.price || 50} ${appCurrencySymbol}\nمرفق لكم لقطة شاشة إيصال الدفع.`
                : `Hello Dr. Yonis, my booking request has been submitted\nBooking Reference: #${ref}\nName: ${name}\nService: ${appState.title}\nDate: ${appState.date} (${appState.slot})\nPayment Method: ${methodLabel}\nAmount: ${appState.price || 50} ${appCurrencySymbol}\nPayment receipt screenshot attached.`;
                
            const waUrl = waNumber ? `https://wa.me/${waNumber}?text=${encodeURIComponent(waMsg)}` : `https://wa.me/?text=${encodeURIComponent(waMsg)}`;
            if (document.getElementById('app-start-consultation-link')) document.getElementById('app-start-consultation-link').href = waUrl;

            // Dynamically bind patient dashboard link
            const dashLink = document.getElementById('app-dashboard-link');
            if (dashLink) {
                dashLink.href = `/booking/${ref}/view-dashboard`;
            }

            // Auto trigger confirmation in backend
            fetch(`/booking/${ref}/confirm-payment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ booking_ref: ref, payment_method: appState.paymentMethod }),
            }).catch(() => {});
        })
        .catch(err => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> ' + @json(__('messages.confirm_and_send_receipt')) + ' (' + (appState.price || 50) + ' ' + appCurrencySymbol + ')';
            }
            alert(err.message || @json(__('messages.request_failed')));
        });
}

// Modal lifecycle and auto-restore
document.addEventListener('DOMContentLoaded', function() {
    restorePatientBookingData();
    renderAppCalendar();
    fetchModalSlots(appState.date);

    const modalEl = document.getElementById('bookingModal');
    if (modalEl) {
        modalEl.addEventListener('show.bs.modal', function() {
            // Always return to screen 1 on opening modal
            document.getElementById('app-screen-1')?.classList.remove('d-none');
            document.getElementById('app-screen-2')?.classList.add('d-none');
            document.getElementById('app-screen-3')?.classList.add('d-none');
            const modalBody = document.querySelector('.mobile-app-body');
            if (modalBody) {
                modalBody.classList.remove('screen-3-active');
                modalBody.scrollTop = 0;
            }
            restorePatientBookingData();
            renderAppCalendar();
            fetchModalSlots(appState.date);
        });
    }
});
</script>
