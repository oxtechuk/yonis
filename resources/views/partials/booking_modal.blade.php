@php
    $modalServices = $services ?? \App\Models\Service::where('is_active', true)->get();
    $stripeActive = \App\Models\Setting::get('stripe_enabled', '0') === '1';
    $waRaw = \App\Models\Setting::get('whatsapp_number', '+9647700000000');
    $isArLocale = app()->getLocale() === 'ar';
    // ─ Payment settings ─
    $payZainEnabled   = \App\Models\Setting::get('payment_zaincash_enabled', '0') === '1';
    $payZainQr        = \App\Models\Setting::get('payment_zaincash_qr', '');
    $payZainLabel     = \App\Models\Setting::get('payment_zaincash_label', 'افتح تطبيق زين كاش وامسح الرمز لإتمام الدفع.');
    $paySuperkiEnabled = \App\Models\Setting::get('payment_superki_enabled', '0') === '1';
    $paySuperkiQr     = \App\Models\Setting::get('payment_superki_qr', '');
    $paySuperkiLabel  = \App\Models\Setting::get('payment_superki_label', 'افتح تطبيق SuperKi وامسح الرمز.');
    $payCardEnabled   = \App\Models\Setting::get('payment_card_enabled', '1') === '1';
    $payCardKey       = \App\Models\Setting::get('payment_card_key', '');
    $payCardLink      = \App\Models\Setting::get('payment_card_link', '');
    $payCardCurrency  = \App\Models\Setting::get('payment_card_currency', 'USD');
    $payCardInstructions = \App\Models\Setting::get('payment_card_instructions', 'يمكنك الدفع مباشرة باستخدام أي بطاقة فيزا أو ماستر كارد صادرة محلياً أو دولياً بأمان وسرية تامة.');
    $anyPaymentActive = $payZainEnabled || $paySuperkiEnabled || $payCardEnabled;
@endphp

{{-- ═══ REUSABLE BOOKING POPUP MODAL WITH INTERACTIVE FLOW ═══ --}}
<div class="modal fade" id="bookingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mobile-app-modal-dialog modal-dialog-scrollable">
        <div class="modal-content mobile-app-modal-content position-relative">
            
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
                
                {{-- ═══ SCREEN 1: Type, Service, Duration & Options ═══ --}}
                <div id="app-screen-1">
                    
                    {{-- 1. Booking Type Selection (Online vs Clinic) --}}
                    <div class="mb-4">
                        <div class="app-section-title">نوع الحجز المطلوب</div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn app-type-toggle-btn flex-fill active" id="modalBtnOnline" onclick="setModalBookingType('online', this)">
                                <i class="bi bi-globe me-1"></i> استشارة أونلاين
                            </button>
                            <button type="button" class="btn app-type-toggle-btn flex-fill" id="modalBtnClinic" onclick="setModalBookingType('clinic', this)">
                                <i class="bi bi-hospital me-1"></i> كشف بالعيادة (بغداد)
                            </button>
                        </div>
                    </div>

                    {{-- 2. Service Selection Dropdown --}}
                    <div class="mb-4">
                        <div class="app-section-title">اختر الخدمة أو الجلسة</div>
                        <select class="form-select app-input w-100 fw-bold" id="app_service_select" onchange="onModalServiceChanged(this)">
                            @foreach($modalServices as $s)
                                <option value="{{ $s->id }}" 
                                        data-title="{{ $s->title }}" 
                                        data-duration="{{ $s->duration }}" 
                                        data-price="{{ $s->price }}"
                                        data-video="{{ $s->video_price ?? $s->price }}"
                                        data-clinic="{{ $s->clinic_price ?? $s->price }}"
                                        data-type="{{ $s->type }}">
                                    {{ $s->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 3. Duration Selection --}}
                    <div class="mb-4">
                        <div class="app-section-title">{{ __('messages.duration_title') }}</div>
                        <div class="app-duration-grid">
                            <div class="app-duration-item" onclick="selectAppDuration(15, 30, this)">
                                <div class="app-duration-icon"><i class="bi bi-hourglass-split"></i></div>
                                <div class="app-duration-time">15 {{ __('messages.minutes') }}</div>
                                <div class="app-duration-price">30 $</div>
                            </div>
                            <div class="app-duration-item selected" onclick="selectAppDuration(30, 50, this)">
                                <div class="app-duration-icon"><i class="bi bi-hourglass-split"></i></div>
                                <div class="app-duration-time">30 {{ __('messages.minutes') }}</div>
                                <div class="app-duration-price">50 $</div>
                            </div>
                            <div class="app-duration-item" onclick="selectAppDuration(45, 75, this)">
                                <div class="app-duration-icon"><i class="bi bi-hourglass-split"></i></div>
                                <div class="app-duration-time">45 {{ __('messages.minutes') }}</div>
                                <div class="app-duration-price">75 $</div>
                            </div>
                            <div class="app-duration-item" onclick="selectAppDuration(60, 100, this)">
                                <div class="app-duration-icon"><i class="bi bi-hourglass-split"></i></div>
                                <div class="app-duration-time">60 {{ __('messages.minutes') }}</div>
                                <div class="app-duration-price">100 $</div>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Consultation Title / Notes --}}
                    <div class="mb-4">
                        <div class="app-section-title">{{ __('messages.consultation_subject') }}</div>
                        <input type="text" id="app_consultation_title" class="form-control app-input w-100" placeholder="{{ __('messages.consultation_subject_ph') }}" value="استشارة نفسية متخصصة" required>
                    </div>

                    <div class="mb-4">
                        <div class="app-section-title">{{ __('messages.consultation_details') }}</div>
                        <textarea id="app_consultation_details" class="form-control app-input w-100" rows="2" placeholder="{{ __('messages.consultation_details_ph') }}"></textarea>
                    </div>

                    {{-- 5. Payment Method Section --}}
                    @if($stripeActive)
                        <div class="mb-4">
                            <div class="app-section-title">{{ __('messages.payment_method') }}</div>
                            
                            {{-- Apple Pay Option --}}
                            <div class="app-payment-card" onclick="selectAppPayment('apple_pay', this)">
                                <div class="d-flex align-items-center gap-3">
                                    <svg width="40" height="24" viewBox="0 0 36 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                      <rect width="36" height="20" rx="4" fill="#000000"/>
                                      <path d="M12.1 10.4c0-1.5 1.2-2.2 1.3-2.3-.7-1.1-1.8-1.2-2.2-1.2-1-.1-1.9.6-2.4.6-.5 0-1.3-.6-2.1-.6-1.1 0-2.1.6-2.6 1.6-1.1 2-.3 4.9.8 6.4.5.8 1.2 1.6 2 1.6.8 0 1.1-.5 2.1-.5 1 0 1.3.5 2.1.5.8 0 1.4-.7 1.9-1.5.6-.9.8-1.7.9-1.8-.1 0-1.8-.7-1.8-2.8zM10.8 5.6c.4-.6.7-1.4.6-2.2-.7 0-1.5.5-1.9 1-.4.5-.7 1.3-.6 2.1.8.1 1.5-.4 1.9-.9z" fill="#FFF"/>
                                      <text x="16" y="13.5" fill="#FFF" font-size="9" font-weight="bold" font-family="system-ui, sans-serif">Pay</text>
                                    </svg>
                                    <span class="fw-bold fs-6">{{ __('messages.pay_apple_pay') }}</span>
                                </div>
                                <div class="app-payment-radio"></div>
                            </div>

                            {{-- Card Option --}}
                            <div class="app-payment-card selected" onclick="selectAppPayment('stripe_card', this)">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="d-flex gap-1 align-items-center">
                                        <svg width="34" height="22" viewBox="0 0 32 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                          <rect width="32" height="20" rx="4" fill="#1A1F71"/>
                                          <circle cx="12" cy="10" r="7" fill="#EB001B"/>
                                          <circle cx="20" cy="10" r="7" fill="#F79E1B"/>
                                          <path d="M16 4.34a6.97 6.97 0 0 1 2.66 5.66c0 2.37-1.07 4.47-2.66 5.66a6.97 6.97 0 0 1-2.66-5.66c0-2.37 1.07-4.47 2.66-5.66z" fill="#FF5F00"/>
                                        </svg>
                                    </div>
                                    <span class="fw-bold fs-6">الدفع بالبطاقة الإلكترونية (Stripe)</span>
                                </div>
                                <div class="app-payment-radio"></div>
                            </div>

                            <div class="mt-2 text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" onclick="autoFillStripeTestCard()">
                                    <i class="bi bi-credit-card-2-front me-1"></i> تعبئة بطاقة Stripe التجريبية
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="mb-4">
                            <div class="app-section-title">طريقة الدفع المعتمدة</div>
                            <div class="d-flex align-items-center gap-3 p-3 rounded-4" style="background: rgba(64, 85, 165, 0.06); border: 1.5px solid rgba(64, 85, 165, 0.18);">
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; background: var(--primary-color); color: #fff; font-size: 1.25rem;">
                                    <i class="bi bi-shield-lock-fill"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark fs-6 mb-1">دفع إلكتروني آمن عبر الرابط المباشر</div>
                                    <div class="text-secondary small">سيتم تزويدك برابط الدفع المباشر الخاص بالمعالج فور تأكيد الموعد بأمان وسرية تامة.</div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Summary Totals & Terms --}}
                    <div class="bg-white p-3 rounded-4 border mb-3">
                        <div class="d-flex justify-content-between mb-2 fs-6">
                            <span class="text-secondary">{{ __('messages.order_total') }}:</span>
                            <span class="fw-bold text-dark" id="app-session-price">50 $</span>
                        </div>
                        <div class="d-flex justify-content-between fs-6 border-top pt-2">
                            <span class="fw-bold text-dark">{{ __('messages.order_total') }}:</span>
                            <span class="fw-bold" style="color:var(--primary-color);" id="app-required-price">50 $</span>
                        </div>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="app_terms_check" checked>
                        <label class="form-check-label small fw-bold text-secondary" for="app_terms_check">
                            {{ $isArLocale ? 'أوافق على الشروط والسرية الطبية التامة' : 'I agree to the Terms & Privacy Policy' }}
                        </label>
                    </div>

                    {{-- Bottom Action Bar for Screen 1 --}}
                    <div class="mobile-app-bottom-bar">
                        <div>
                            <div class="app-total-label">{{ __('messages.order_total') }}</div>
                            <div class="app-total-value" id="app-bottom-total">50 $</div>
                        </div>
                        <button type="button" class="btn-app-primary" onclick="goToAppScreen2()">{{ __('messages.next') }}</button>
                    </div>

                </div>{{-- End Screen 1 --}}

                {{-- ═══ SCREEN 2: Date, Slot & WhatsApp with Country Code ═══ --}}
                <div id="app-screen-2" class="d-none">
                    
                    {{-- Calendar View --}}
                    <div class="app-calendar-box">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <button type="button" class="btn btn-sm btn-light rounded-circle" onclick="changeAppMonth(-1)"><i class="bi bi-chevron-right"></i></button>
                            <div class="app-calendar-month mb-0" id="app-calendar-month-title">{{ date('F Y') }}</div>
                            <button type="button" class="btn btn-sm btn-light rounded-circle" onclick="changeAppMonth(1)"><i class="bi bi-chevron-left"></i></button>
                        </div>
                        <div class="app-calendar-weekdays">
                            <div>أحد</div><div>إثن</div><div>ثلا</div><div>أرب</div><div>خميس</div><div>جمع</div><div>سبت</div>
                        </div>
                        <div class="app-calendar-days" id="app-calendar-days-grid"></div>
                    </div>

                    {{-- Available Time Slots --}}
                    <div class="mb-4">
                        <div class="app-section-title"><i class="bi bi-clock me-1 text-primary"></i> {{ __('messages.select_time') }}</div>
                        <div class="app-slots-grid" id="app-slots-grid">
                            <div class="app-slot-pill selected" onclick="selectAppSlot('09:00 AM', this)">09:00 ص</div>
                            <div class="app-slot-pill" onclick="selectAppSlot('10:00 AM', this)">10:00 ص</div>
                            <div class="app-slot-pill" onclick="selectAppSlot('11:30 AM', this)">11:30 ص</div>
                            <div class="app-slot-pill" onclick="selectAppSlot('01:00 PM', this)">01:00 م</div>
                            <div class="app-slot-pill" onclick="selectAppSlot('02:30 PM', this)">02:30 م</div>
                            <div class="app-slot-pill" onclick="selectAppSlot('04:00 PM', this)">04:00 م</div>
                        </div>
                    </div>

                    {{-- Registration Section --}}
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="app-section-title fs-5 fw-black text-dark mb-0">{{ __('messages.register') }}</div>
                            <span id="app_user_status_badge" class="badge bg-light text-muted border small d-none"></span>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary mb-1">{{ __('messages.full_name') }}</label>
                            <div class="position-relative">
                                <input type="text" id="app_user_name" class="form-control app-input w-100 pe-4" placeholder="{{ __('messages.full_name') }}" value="{{ Auth::check() ? Auth::user()->name : '' }}" required>
                                <i class="bi bi-person position-absolute top-50 translate-middle-y end-0 me-3 text-secondary"></i>
                            </div>
                        </div>

                        {{-- WhatsApp with SVG Country Flag Picker (Default Iraq +964) --}}
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary mb-1">{{ __('messages.whatsapp_number') }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 pe-1 ps-2" style="border-radius: 0 16px 16px 0;">
                                    <img id="app_country_flag_img" src="https://flagcdn.com/w40/iq.png" width="24" height="16" class="rounded shadow-sm" alt="العراق">
                                </span>
                                <select class="form-select bg-light fw-bold text-dark border-start-0 border-end-0 ps-1 pe-3" id="app_country_code" style="max-width: 145px; cursor:pointer;" onchange="onModalCountryCodeChanged(this)">
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
                                <input type="tel" id="app_user_phone" class="form-control app-input rounded-start-4" placeholder="7701234567" value="{{ Auth::check() ? preg_replace('/^\+964/', '', Auth::user()->phone ?? '') : '' }}" oninput="checkUserRegistrationStatus()" required>
                            </div>
                        </div>

                        <div class="mb-3" id="app_password_wrapper" style="{{ Auth::check() ? 'display:none;' : '' }}">
                            <label class="form-label small fw-bold text-secondary mb-1" id="app_password_label">{{ __('messages.password') }}</label>
                            <div class="position-relative">
                                <input type="password" id="app_user_password" class="form-control app-input w-100 pe-4" placeholder="{{ __('messages.password') }}" minlength="6">
                                <i class="bi bi-lock position-absolute top-50 translate-middle-y end-0 me-3 text-secondary"></i>
                            </div>
                            <div class="form-text text-muted small" id="app_password_hint">يرجى تعيين كلمة مرور لإنشاء حسابك ومتابعة مواعيدك.</div>
                        </div>
                    </div>

                    {{-- Bottom Action Bar for Screen 2 --}}
                    <div class="mobile-app-bottom-bar">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" onclick="goToAppScreen1()"><i class="bi bi-arrow-right me-1"></i> {{ __('messages.back') }}</button>
                        <button type="button" class="btn-app-primary" id="app-submit-pay-btn" onclick="executeAppBooking()">
                            <i class="bi bi-credit-card-2-front-fill me-1"></i> الانتقال للدفع الآن وإتمام الحجز
                        </button>
                    </div>

                </div>{{-- End Screen 2 --}}

                {{-- ═══ SCREEN 3: اختيار طريقة الدفع ═══ --}}
                <div id="app-screen-3" class="d-none">

                    {{-- معلومات الحجز (summary) --}}
                    <div class="rounded-4 p-3 mb-4" style="background:linear-gradient(135deg,rgba(59,82,164,.07),rgba(59,82,164,.02));border:1.5px solid rgba(59,82,164,.12);">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-secondary small">رقم المرجع</span>
                            <span class="fw-black text-primary" id="app-res-ref">#REF-8492</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-secondary small">الخدمة</span>
                            <span class="fw-bold text-dark small" id="app-res-service">جلسة استشارة</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-secondary small">الموعد</span>
                            <span class="fw-bold text-dark small" id="app-res-datetime">—</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2">
                            <span class="fw-bold text-dark">إجمالي الدفع</span>
                            <span class="fw-black fs-5" style="color:var(--primary-color);" id="app-res-type">50 $</span>
                        </div>
                    </div>

                    @if($anyPaymentActive)

                        {{-- تبويبات طرق الدفع --}}
                        <div class="app-section-title mb-3">اختر طريقة الدفع</div>
                        <div class="d-flex gap-2 mb-4" id="payment-method-tabs" role="tablist">
                            @if($payZainEnabled)
                            <button type="button" class="btn pay-tab-btn flex-fill active"
                                    id="pay-tab-zaincash" onclick="switchPayTab('zaincash')"
                                    style="background:linear-gradient(135deg,#7c3aed,#4c1d95);color:#fff;border:none;border-radius:14px;padding:10px 6px;font-weight:700;font-size:.82rem;">
                                💜 زين كاش
                            </button>
                            @endif
                            @if($paySuperkiEnabled)
                            <button type="button" class="btn pay-tab-btn flex-fill @if(!$payZainEnabled) active @endif"
                                    id="pay-tab-superki" onclick="switchPayTab('superki')"
                                    style="background:@if(!$payZainEnabled)linear-gradient(135deg,#0284c7,#075985)@else#e2e8f0@endif;color:@if(!$payZainEnabled)#fff@else#475569@endif;border:none;border-radius:14px;padding:10px 6px;font-weight:700;font-size:.82rem;">
                                🔵 SuperKi
                            </button>
                            @endif
                            @if($payCardEnabled)
                            <button type="button" class="btn pay-tab-btn flex-fill @if(!$payZainEnabled && !$paySuperkiEnabled) active @endif"
                                    id="pay-tab-card" onclick="switchPayTab('card')"
                                    style="background:@if(!$payZainEnabled && !$paySuperkiEnabled)linear-gradient(135deg,#1e3a8a,#2563eb)@else#e2e8f0@endif;color:@if(!$payZainEnabled && !$paySuperkiEnabled)#fff@else#475569@endif;border:none;border-radius:14px;padding:10px 6px;font-weight:700;font-size:.82rem;">
                                💳 فيزا وماستر كارد
                            </button>
                            @endif
                        </div>

                        {{-- ─── بانل زين كاش ─── --}}
                        @if($payZainEnabled)
                        <div id="pay-panel-zaincash" class="pay-panel">
                            <div class="text-center mb-3">
                                @if(!empty($payZainQr))
                                    <div class="d-inline-block p-3 bg-white rounded-4 shadow-sm border">
                                        <img src="{{ $payZainQr }}" alt="ZainCash QR"
                                             style="max-width:210px;max-height:210px;border-radius:10px;">
                                    </div>
                                    <p class="text-secondary small mt-3 mb-0 px-2">{{ $payZainLabel }}</p>
                                @else
                                    <div class="p-4 text-muted">
                                        <i class="bi bi-qr-code" style="font-size:3rem;opacity:.3;"></i>
                                        <p class="small mt-2">لم يتم إعداد صورة QR بعد — يرجى مراجعة الإعدادات</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        {{-- ─── بانل SuperKi ─── --}}
                        @if($paySuperkiEnabled)
                        <div id="pay-panel-superki" class="pay-panel @if($payZainEnabled) d-none @endif">
                            <div class="text-center mb-3">
                                @if(!empty($paySuperkiQr))
                                    <div class="d-inline-block p-3 bg-white rounded-4 shadow-sm border">
                                        <img src="{{ $paySuperkiQr }}" alt="SuperKi QR"
                                             style="max-width:210px;max-height:210px;border-radius:10px;">
                                    </div>
                                    <p class="text-secondary small mt-3 mb-0 px-2">{{ $paySuperkiLabel }}</p>
                                @else
                                    <div class="p-4 text-muted">
                                        <i class="bi bi-qr-code" style="font-size:3rem;opacity:.3;"></i>
                                        <p class="small mt-2">لم يتم إعداد صورة QR بعد — يرجى مراجعة الإعدادات</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        {{-- ─── بانل فيزا وماستر كارد (Visa & MasterCard) ─── --}}
                        @if($payCardEnabled)
                        <div id="pay-panel-card" class="pay-panel @if($payZainEnabled || $paySuperkiEnabled) d-none @endif">
                            <div class="card border-0 rounded-4 p-3 bg-light shadow-none mb-3">
                                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                                    <div class="fw-bold text-dark small">
                                        <i class="bi bi-shield-check text-success me-1"></i> بوابة الدفع بالبطاقة الائتمانية
                                    </div>
                                    <div class="d-flex align-items-center gap-1">
                                        {{-- Visa SVG Logo --}}
                                        <span class="badge bg-white px-2 py-1 shadow-sm border text-primary fw-black" style="font-size: 0.8rem; letter-spacing: 0.5px;">VISA</span>
                                        {{-- MasterCard SVG Logo --}}
                                        <span class="badge bg-white px-2 py-1 shadow-sm border text-danger fw-black" style="font-size: 0.8rem;">MasterCard</span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-secondary mb-1">رقم البطاقة (Card Number)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0 text-secondary"><i class="bi bi-credit-card-2-front"></i></span>
                                        <input type="text" id="card_number_input" class="form-control bg-white border-start-0 font-monospace" placeholder="4242 •••• •••• 4242" maxlength="19" oninput="formatCardNumber(this)">
                                    </div>
                                </div>

                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-secondary mb-1">تاريخ الانتهاء</label>
                                        <input type="text" id="card_expiry_input" class="form-control bg-white font-monospace text-center" placeholder="MM / YY" maxlength="5" oninput="formatCardExpiry(this)">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-secondary mb-1">رمز الأمان (CVC)</label>
                                        <input type="password" id="card_cvc_input" class="form-control bg-white font-monospace text-center" placeholder="•••" maxlength="4">
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label small fw-bold text-secondary mb-1">اسم حامل البطاقة</label>
                                    <input type="text" id="card_name_input" class="form-control bg-white" placeholder="الاسم كما هو مطبوع على البطاقة">
                                </div>

                                <p class="text-secondary small mt-2 mb-0" style="font-size:0.75rem;">
                                    <i class="bi bi-lock-fill text-muted me-1"></i> {{ $payCardInstructions }}
                                </p>
                            </div>

                            @if(!empty($payCardLink))
                            <div class="mb-3">
                                <a href="{{ $payCardLink }}" target="_blank" class="btn btn-outline-primary w-100 py-2.5 rounded-pill fw-bold small d-flex align-items-center justify-content-center gap-2">
                                    <i class="bi bi-box-arrow-up-right"></i> أو الدفع عبر رابط الدفع الإلكتروني المباشر
                                </a>
                            </div>
                            @endif
                        </div>
                        @endif

                        {{-- زر "أكدت الدفع" --}}
                        <div class="mt-4" id="pay-confirm-section">
                            <button type="button" class="btn btn-royal-primary w-100 py-3 rounded-pill fw-bold shadow"
                                    id="app-confirm-payment-btn" onclick="confirmPaymentByPatient()">
                                <i class="bi bi-check-circle-fill me-2"></i> أكدت الدفع — تأكيد الحجز
                            </button>
                            <p class="text-secondary small text-center mt-2 mb-0">
                                <i class="bi bi-info-circle me-1"></i>
                                سيتم مراجعة الدفع من قبل الدكتور وتأكيد الحجز خلال 24 ساعة.
                            </p>
                        </div>

                        {{-- شاشة التأكيد بعد ضغط الزر --}}
                        <div id="pay-confirmed-screen" class="text-center py-3 d-none">
                            <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                                 style="width:70px;height:70px;border-radius:50%;background:linear-gradient(135deg,#10b981,#059669);color:#fff;font-size:2rem;box-shadow:0 10px 25px rgba(16,185,129,.35);">
                                <i class="bi bi-check2"></i>
                            </div>
                            <h5 class="fw-black text-dark mb-1">تم إرسال تأكيدك!</h5>
                            <p class="text-secondary small mb-4">سيراجع الدكتور دفعك ويؤكد الحجز خلال 24 ساعة. ستصلك رسالة تأكيد.</p>
                            <a id="app-start-consultation-link" href="#" target="_blank"
                               class="btn btn-outline-success w-100 py-3 rounded-pill fw-bold d-flex align-items-center justify-content-center gap-2 mb-2">
                                <i class="bi bi-whatsapp fs-5"></i>
                                <span>إرسال إيصال الدفع عبر واتسآب</span>
                            </a>
                            <button type="button" class="btn btn-light rounded-pill py-2 fw-bold text-secondary"
                                    data-bs-dismiss="modal" onclick="window.location.reload()">
                                <i class="bi bi-arrow-repeat me-1"></i> إغلاق
                            </button>
                        </div>

                    @else
                        {{-- لا توجد طريقة دفع مفعلة — عرض WhatsApp فقط --}}
                        <div class="text-center py-3">
                            <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                                 style="width:70px;height:70px;border-radius:50%;background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;font-size:2rem;">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <h5 class="fw-black text-dark mb-2">تم تسجيل طلب الموعد</h5>
                            <p class="text-secondary small mb-4">سيتواصل معك الدكتور لمتابعة تفاصيل الدفع.</p>
                            <a id="app-start-consultation-link" href="#" target="_blank"
                               class="btn btn-outline-success w-100 py-3 rounded-pill fw-bold d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-whatsapp fs-5"></i>
                                <span>مراسلة الدكتور عبر واتسآب</span>
                            </a>
                        </div>
                    @endif

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
    duration: 30,
    price: 50,
    bookingType: 'online',
    paymentMethod: '{{ $stripeActive ? "stripe_card" : "direct_confirmation" }}',
    title: '{{ $modalServices->first()->title ?? "استشارة نفسية متخصصة" }}',
    details: '',
    date: _initDateStr,
    slot: '',
    year: _initYear,
    month: _initMonth,
};

const monthNamesAr = [
    'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو',
    'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'
];

const waNumber = '{{ preg_replace("/\D/", "", $waRaw) }}';
let appUserIsRegistered = {{ Auth::check() ? 'true' : 'false' }};
let checkPhoneTimeout = null;

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
    const dur = parseInt(opt.getAttribute('data-duration')) || 30;
    appState.duration = dur;
    
    const p = appState.bookingType === 'clinic' ? (opt.getAttribute('data-clinic') || opt.getAttribute('data-price')) : (opt.getAttribute('data-video') || opt.getAttribute('data-price'));
    if (p) updateModalPrice(parseFloat(p));
    
    const titleInput = document.getElementById('app_consultation_title');
    if (titleInput) titleInput.value = appState.title;
    
    // Update duration pills
    document.querySelectorAll('.app-duration-item').forEach(item => {
        const text = item.querySelector('.app-duration-time').textContent;
        item.classList.toggle('selected', text.includes(dur.toString()));
    });

    // Refresh slots if screen 2 is visible
    if (!document.getElementById('app-screen-2').classList.contains('d-none')) {
        fetchModalSlots(appState.date);
    }
}

function selectAppDuration(duration, price, el) {
    appState.duration = duration;
    updateModalPrice(price);
    document.querySelectorAll('.app-duration-item').forEach(i => i.classList.remove('selected'));
    if (el) el.classList.add('selected');
}

function updateModalPrice(price) {
    appState.price = price;
    const pText = price + ' $';
    if (document.getElementById('app-session-price')) document.getElementById('app-session-price').textContent = pText;
    if (document.getElementById('app-required-price')) document.getElementById('app-required-price').textContent = pText;
    if (document.getElementById('app-bottom-total')) document.getElementById('app-bottom-total').textContent = pText;
}

function selectAppPayment(method, el) {
    appState.paymentMethod = method;
    document.querySelectorAll('.app-payment-card').forEach(c => c.classList.remove('selected'));
    if (el) el.classList.add('selected');
}

// ─── Payment Tab Switcher (Screen 3) ───────────────────────────────────────
const payTabGradients = {
    zaincash:   'linear-gradient(135deg,#7c3aed,#4c1d95)',
    superki:    'linear-gradient(135deg,#0284c7,#075985)',
    card:       'linear-gradient(135deg,#1e3a8a,#2563eb)',
};

function switchPayTab(method) {
    // Hide all panels
    document.querySelectorAll('.pay-panel').forEach(p => p.classList.add('d-none'));
    // Show selected panel
    const panel = document.getElementById('pay-panel-' + method);
    if (panel) panel.classList.remove('d-none');

    // Reset all tab buttons
    document.querySelectorAll('.pay-tab-btn').forEach(btn => {
        btn.style.background = '#e2e8f0';
        btn.style.color = '#475569';
    });
    // Highlight active tab
    const activeBtn = document.getElementById('pay-tab-' + method);
    if (activeBtn && payTabGradients[method]) {
        activeBtn.style.background = payTabGradients[method];
        activeBtn.style.color = '#fff';
    }
}

function formatCardNumber(input) {
    let v = input.value.replace(/\D/g, '').slice(0, 16);
    let formatted = v.match(/.{1,4}/g)?.join(' ') || v;
    input.value = formatted;
}

function formatCardExpiry(input) {
    let v = input.value.replace(/\D/g, '').slice(0, 4);
    if (v.length >= 3) {
        input.value = v.slice(0, 2) + '/' + v.slice(2);
    } else {
        input.value = v;
    }
}

// ─── Patient confirms payment manually ─────────────────────────────────────
function confirmPaymentByPatient() {
    const btn = document.getElementById('app-confirm-payment-btn');
    if (!btn) return;
    if (!appState.bookingRef) {
        alert('لم يتم تسجيل رقم الحجز بعد. يرجى المحاولة لاحقاً.');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> جارٍ الإرسال...';

    fetch(`/booking/${appState.bookingRef}/confirm-payment`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ booking_ref: appState.bookingRef }),
    })
    .then(r => r.json())
    .then(data => {
        // Show confirmation screen
        document.getElementById('pay-confirm-section')?.classList.add('d-none');
        document.querySelectorAll('.pay-panel').forEach(p => p.classList.add('d-none'));
        document.getElementById('payment-method-tabs')?.classList.add('d-none');
        document.getElementById('pay-confirmed-screen')?.classList.remove('d-none');
    })
    .catch(() => {
        // Even on error, show confirmation (optimistic UX)
        document.getElementById('pay-confirm-section')?.classList.add('d-none');
        document.getElementById('pay-confirmed-screen')?.classList.remove('d-none');
    });
}

// ─── SpaceRemit success callback ───────────────────────────────────────────
function confirmPaymentAfterSpaceRemit(code) {
    if (appState.bookingRef) {
        fetch(`/booking/${appState.bookingRef}/confirm-payment`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ booking_ref: appState.bookingRef, spaceremit_code: code }),
        }).catch(() => {});
    }
    document.getElementById('pay-confirm-section')?.classList.add('d-none');
    document.getElementById('pay-confirmed-screen')?.classList.remove('d-none');
}



function autoFillStripeTestCard() {
    const cards = document.querySelectorAll('.app-payment-card');
    if (cards.length > 1) selectAppPayment('stripe_card', cards[1]);
    alert('تم تحديد دفع Stripe بالبطاقة التجريبية (Test Card: 4242 4242 4242 4242 | CVC: 123 | Exp: 12/34)');
}

function selectServiceAndOpenModal(id, title, price, duration, categoryType) {
    const selectedType = categoryType || 'online';
    setModalBookingType(selectedType);
    
    appState.serviceId = id;
    appState.duration = duration || 30;
    appState.title = title || (selectedType === 'clinic' ? 'كشف واستشارة بالعيادة' : 'استشارة نفسية أونلاين');
    
    const select = document.getElementById('app_service_select');
    if (select) {
        select.value = id;
    }
    
    const titleInput = document.getElementById('app_consultation_title');
    if (titleInput) titleInput.value = appState.title;
    
    if (price) updateModalPrice(price);
    
    const modalEl = document.getElementById('bookingModal');
    if (modalEl) {
        new bootstrap.Modal(modalEl).show();
    }
}

function goToAppScreen2() {
    const titleInput = document.getElementById('app_consultation_title');
    const title = titleInput ? titleInput.value.trim() : 'استشارة نفسية';
    if (!title) {
        alert('يرجى كتابة موضوع الاستشارة للمتابعة.');
        if (titleInput) titleInput.focus();
        return;
    }
    appState.title = title;
    const detailsInput = document.getElementById('app_consultation_details');
    if (detailsInput) appState.details = detailsInput.value;

    document.getElementById('app-screen-1').classList.add('d-none');
    document.getElementById('app-screen-2').classList.remove('d-none');
    renderAppCalendar();
    fetchModalSlots(appState.date);
}

function goToAppScreen1() {
    document.getElementById('app-screen-2').classList.add('d-none');
    document.getElementById('app-screen-1').classList.remove('d-none');
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
    if (titleEl) titleEl.textContent = monthNamesAr[appState.month] + ' ' + appState.year;
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
            dayEl.title = 'تاريخ سابق غير متاح';
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

    container.innerHTML = '<div class="text-center py-3 w-100" style="grid-column: 1 / -1;"><div class="spinner-border spinner-border-sm text-primary me-2"></div><span class="text-muted small">جاري جلب الأوقات المتاحة...</span></div>';

    fetch(`{{ url('/api/slots') }}?service_id=${appState.serviceId}&date=${dateStr}`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        container.innerHTML = '';
        const slots = data?.slots || (Array.isArray(data) ? data : []);

        if (!slots || slots.length === 0) {
            container.innerHTML = '<div class="alert alert-warning border-0 small py-2.5 px-3 mb-0 w-100 text-center" style="grid-column: 1 / -1;"><i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> لا تتوفر مواعيد متاحة في هذا اليوم، يرجى اختيار تاريخ آخر من التقويم.</div>';
            appState.slot = '';
            return;
        }

        slots.forEach((s, idx) => {
            const rawTime = typeof s === 'string' ? s : (s.start || s.formatted || '');
            if (!rawTime) return;

            // Format to Arabic 12-hour format
            let displayTime = rawTime;
            const parts = rawTime.split(':');
            if (parts.length >= 2) {
                let hour = parseInt(parts[0], 10);
                const min = parts[1];
                const period = hour >= 12 ? 'م' : 'ص';
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
        container.innerHTML = '<div class="text-center text-muted small py-2 w-100" style="grid-column: 1 / -1;">تعذّر تحميل الأوقات، يرجى اختيار تاريخ آخر.</div>';
    });
}

function selectAppSlot(time, el) {
    appState.slot = time;
    document.querySelectorAll('.app-slot-pill').forEach(p => p.classList.remove('selected'));
    if (el) el.classList.add('selected');
}

function onModalCountryCodeChanged(selectEl) {
    const opt = selectEl.options[selectEl.selectedIndex];
    const flagImg = document.getElementById('app_country_flag_img');
    if (flagImg && opt && opt.getAttribute('data-flag')) {
        flagImg.src = opt.getAttribute('data-flag');
        flagImg.alt = opt.text;
    }
    checkUserRegistrationStatus();
}

function checkUserRegistrationStatus() {
    const countryCode = document.getElementById('app_country_code') ? document.getElementById('app_country_code').value : '+964';
    let rawPhone = document.getElementById('app_user_phone').value.trim();
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

            if (badge) badge.classList.remove('d-none');
            if (res.is_registered) {
                appUserIsRegistered = true;
                if (badge) {
                    badge.className = 'badge bg-success-subtle text-success border border-success-subtle small';
                    badge.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> عميل مسجل مسبقاً';
                }
                if (passWrapper) passWrapper.style.display = 'none';
                if (passInput) passInput.value = '';
                if (res.user && res.user.name && nameInput && !nameInput.value) {
                    nameInput.value = res.user.name;
                }
            } else {
                appUserIsRegistered = false;
                if (badge) {
                    badge.className = 'badge bg-info-subtle text-primary border border-info-subtle small';
                    badge.innerHTML = '<i class="bi bi-person-plus-fill me-1"></i> عميل جديد (يتطلب كلمة مرور)';
                }
                if (passWrapper) passWrapper.style.display = 'block';
            }
        })
        .catch(() => {});
    }, 400);
}

function executeAppBooking() {
    const nameInput = document.getElementById('app_user_name');
    const phoneInput = document.getElementById('app_user_phone');
    const passInput = document.getElementById('app_user_password');
    const countryCode = document.getElementById('app_country_code') ? document.getElementById('app_country_code').value : '+964';

    const name = nameInput ? nameInput.value.trim() : '';
    let rawPhone = phoneInput ? phoneInput.value.trim().replace(/^0+/, '') : '';
    const password = passInput ? passInput.value.trim() : '';

    if (!name || !rawPhone) {
        alert('يرجى كتابة الاسم ورقم الهاتف.');
        return;
    }

    if (!appState.slot) {
        alert('يرجى اختيار وقت متاح للموعد.');
        return;
    }

    if (!appUserIsRegistered && (!password || password.length < 6)) {
        alert('يرجى إدخال كلمة مرور الحساب (6 أحرف على الأقل).');
        if (passInput) passInput.focus();
        return;
    }

    const btn = document.getElementById('app-submit-pay-btn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> جاري تجهيز رابط الدفع...';
    }

    const fullPhone = rawPhone.startsWith('+') ? rawPhone : (countryCode + rawPhone);

    const payload = {
        service_id: appState.serviceId || 1,
        booking_type: appState.bookingType || 'online',
        consultation_type: 'video',
        date: appState.date,
        start_time: appState.slot,
        name: name,
        phone: fullPhone,
        password: password || null,
        title: appState.title,
        notes: appState.details,
    };

    const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '';
    const headers = {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
    };

    fetch("{{ url('/api/checkout/initialize') }}", { method: 'POST', headers, body: JSON.stringify(payload) })
        .then(r => r.json())
        .then(data => {
            if (!data.success) throw new Error(data.message || 'حدث خطأ في طلب الحجز.');
            
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-credit-card-2-front-fill me-1"></i> الانتقال للدفع الآن وإتمام الحجز';
            }

            const ref = data?.booking_reference || 'REF-' + Math.floor(1000 + Math.random() * 9000);
            const paymentUrl = data?.payment_url || 'https://younisalmurshed.gumroad.com/l/srjlvw?wanted=true';

            // Store booking reference for payment confirmation
            appState.bookingRef = ref;

            // 1. Immediately open external direct payment URL in a new tab
            try {
                window.open(paymentUrl, '_blank');
            } catch (e) {
                console.log('Popup blocked, using fallback button');
            }

            // 2. Transition UI to Screen 3 (Payment Selection)
            document.getElementById('app-screen-2').classList.add('d-none');
            document.getElementById('app-screen-3').classList.remove('d-none');

            if (document.getElementById('app-res-ref')) document.getElementById('app-res-ref').textContent = '#' + ref;
            if (document.getElementById('app-res-service')) document.getElementById('app-res-service').textContent = appState.title || 'جلسة استشارة نفسية';
            if (document.getElementById('app-res-datetime')) document.getElementById('app-res-datetime').textContent = appState.date + ' | ' + appState.slot;
            if (document.getElementById('app-res-type')) document.getElementById('app-res-type').textContent = (appState.price || 50) + ' $';
            

            // Direct Service Payment Link
            const payBtn = document.getElementById('app-direct-payment-link');
            if (payBtn) {
                payBtn.href = paymentUrl;
            }

            const waMsg = `السلام عليكم دكتور يونس، تم تسجيل طلب حجز موعد\nرقم المرجع: #${ref}\nالاسم: ${name}\nالموعد المطلوب: ${appState.date} ${appState.slot}\nالنوع: ${appState.bookingType === 'clinic' ? 'كشف بالعيادة' : 'استشارة أونلاين'}\nحالة الدفع: جاري إتمام الدفع عبر الرابط المباشر\nرابط الدفع: ${paymentUrl}`;
            const waUrl = waNumber ? `https://wa.me/${waNumber}?text=${encodeURIComponent(waMsg)}` : `https://wa.me/?text=${encodeURIComponent(waMsg)}`;
            if (document.getElementById('app-start-consultation-link')) document.getElementById('app-start-consultation-link').href = waUrl;
        })
        .catch(err => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-credit-card-2-front-fill me-1"></i> الانتقال للدفع الآن وإتمام الحجز';
            }
            alert(err.message || 'تعذّر إكمال الطلب. يرجى إعادة المحاولة.');
        });
}
</script>
