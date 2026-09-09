@php
    $modalServices = $services ?? \App\Models\Service::where('is_active', true)->get();
    $waRaw = \App\Models\Setting::get('whatsapp_number', '+9647700000000');
    $isArLocale = app()->getLocale() === 'ar';
    $siteLogo = \App\Models\Setting::getFileUrl('site_logo', '');
    $currencySymbol = \App\Models\Setting::currencySymbol();

    // ─ Dynamic Payment Methods from Settings ─
    $paymentMethods = \App\Models\Setting::getPaymentMethods(true);
    $anyPaymentActive = !empty($paymentMethods);
    $defaultPayMethod = $anyPaymentActive ? array_key_first($paymentMethods) : 'zaincash';
@endphp

{{-- ═══ MODERN PIXEL-PERFECT BOOKING MODAL (CLEAN MEDICAL UX/UI) ═══ --}}
<style>
/* ─── Modal Dialog & Sizing ─── */
.luxury-modal-dialog {
    max-width: 580px !important;
    margin: 1.5rem auto !important;
}
.luxury-modal-content {
    border-radius: 24px !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 20px 45px rgba(15, 23, 42, 0.22) !important;
    overflow: hidden !important;
    background: #ffffff !important;
}

/* ─── Top Header (Clean Navy) ─── */
.modal-top-header {
    background: linear-gradient(135deg, #0f1c34 0%, #162a4d 100%);
    padding: 1.25rem 1.4rem;
    position: relative;
    color: #ffffff;
}
.modal-close-btn {
    position: absolute;
    top: 1rem;
    inset-inline-end: 1rem;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.12);
    border: none;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 0.9rem;
    transition: all 0.2s ease;
}
.modal-close-btn:hover {
    background: rgba(255, 255, 255, 0.22);
    color: #ffffff;
}
.header-phone-badge {
    width: 52px;
    height: 52px;
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.1);
    border: 1.5px solid rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    flex-shrink: 0;
}
.header-phone-badge img {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    object-fit: cover;
}
.header-phone-badge .video-tag {
    position: absolute;
    bottom: -3px;
    inset-inline-end: -3px;
    width: 20px;
    height: 20px;
    background: #2563eb;
    border: 2px solid #ffffff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.6rem;
    color: #ffffff;
}
.header-title-text {
    font-size: 1.25rem;
    font-weight: 800;
    margin-bottom: 2px;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.header-sub-text {
    font-size: 0.82rem;
    color: #cbd5e1;
    margin: 0;
}

/* ─── Stepper Progress Bar (Clean & Balanced) ─── */
.stepper-nav-bar {
    background: #ffffff;
    border-bottom: 1px solid #f1f5f9;
    padding: 0.85rem 1.2rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
}
.stepper-track-line {
    position: absolute;
    top: 24px;
    left: 10%;
    right: 10%;
    height: 2px;
    background: #e2e8f0;
    z-index: 1;
}
.stepper-node {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    position: relative;
    z-index: 2;
    flex: 1;
    text-align: center;
}
.stepper-bubble {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 0.8rem;
    background: #ffffff;
    color: #94a3b8;
    border: 2px solid #e2e8f0;
    transition: all 0.25s ease;
}
.stepper-node.active .stepper-bubble {
    background: #2563eb;
    border-color: #2563eb;
    color: #ffffff;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
}
.stepper-node.completed .stepper-bubble {
    background: #10b981;
    border-color: #10b981;
    color: #ffffff;
}
.stepper-caption {
    font-size: 0.74rem;
    font-weight: 700;
    color: #64748b;
    white-space: nowrap;
}
.stepper-node.active .stepper-caption {
    color: #0f172a;
    font-weight: 800;
}
.stepper-node.completed .stepper-caption {
    color: #10b981;
}

/* ─── Modal Scroll Body with Safe Bottom Padding ─── */
.modal-body-scrollable {
    padding: 1.15rem 1.4rem 100px 1.4rem !important;
    max-height: calc(88vh - 120px);
    overflow-y: auto;
    background: #ffffff;
}

/* ─── Typography & Headlines ─── */
.card-section-label {
    font-size: 0.88rem;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 0.6rem;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* ─── Booking Type Cards ─── */
.type-choice-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 1rem;
}
.type-choice-card {
    border: 1.5px solid #e2e8f0;
    border-radius: 16px;
    padding: 0.85rem 0.95rem;
    background: #ffffff;
    cursor: pointer;
    transition: all 0.2s ease;
    user-select: none;
}
.type-choice-card:hover {
    border-color: #93c5fd;
    background: #f8fafc;
}
.type-choice-card.active {
    border-color: #2563eb;
    background: #f0f7ff;
    box-shadow: 0 2px 10px rgba(37, 99, 235, 0.08);
}
.type-card-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.35rem;
}
.type-card-radio {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    border: 2px solid #cbd5e1;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
}
.type-choice-card.active .type-card-radio {
    border-color: #2563eb;
    background: #2563eb;
}
.type-choice-card.active .type-card-radio::after {
    content: '';
    width: 6px;
    height: 6px;
    background: #ffffff;
    border-radius: 50%;
}
.type-card-name {
    font-size: 0.88rem;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 2px;
}
.type-card-desc {
    font-size: 0.73rem;
    color: #64748b;
    margin-bottom: 0.35rem;
}
.type-selected-tag {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
    font-size: 0.68rem;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 12px;
}

/* ─── Session Details Strip (3 Columns) ─── */
.session-stat-strip {
    background: #f0f7ff;
    border: 1px solid #dbeafe;
    border-radius: 14px;
    padding: 0.75rem 0.9rem;
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 6px;
    margin-bottom: 1.15rem;
    text-align: center;
}
.session-stat-item {
    position: relative;
}
.session-stat-item:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 15%;
    bottom: 15%;
    inset-inline-end: 0;
    width: 1px;
    background: #bfdbfe;
}
.session-stat-title {
    font-size: 0.72rem;
    font-weight: 700;
    color: #475569;
    margin-bottom: 2px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
}
.session-stat-data {
    font-size: 0.88rem;
    font-weight: 800;
    color: #0f172a;
}

/* ─── Form Inputs & Icons ─── */
.input-row-block {
    margin-bottom: 0.75rem;
}
.input-row-label {
    font-size: 0.78rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.3rem;
    display: block;
}
.field-box-wrap {
    position: relative;
    display: flex;
    align-items: center;
}
.field-box-wrap .form-control,
.field-box-wrap .form-select {
    height: 44px;
    border-radius: 12px;
    border: 1.5px solid #e2e8f0;
    font-size: 0.85rem;
    font-weight: 600;
    color: #0f172a;
    background: #ffffff;
    transition: border-color 0.2s;
    padding-inline-start: 0.85rem;
    padding-inline-end: 2.4rem;
}
.field-box-wrap .form-control:focus,
.field-box-wrap .form-select:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    outline: none;
}
.field-box-wrap .trailing-icon {
    position: absolute;
    inset-inline-end: 0.85rem;
    font-size: 1rem;
    color: #94a3b8;
    pointer-events: none;
}
.field-box-wrap .pwd-eye-btn {
    position: absolute;
    inset-inline-end: 2.2rem;
    border: none;
    background: transparent;
    color: #94a3b8;
    font-size: 0.95rem;
    cursor: pointer;
    padding: 0;
}

/* ─── Phone & Country Dropdown ─── */
.phone-country-duo {
    display: flex;
    align-items: center;
    gap: 8px;
}
.phone-country-duo .country-pick {
    width: 130px;
    flex-shrink: 0;
    height: 44px;
    border-radius: 12px;
    border: 1.5px solid #e2e8f0;
    background: #f8fafc;
    font-size: 0.8rem;
    font-weight: 700;
    color: #1e293b;
    padding-inline-start: 0.65rem;
    padding-inline-end: 1.25rem;
    cursor: pointer;
}
.phone-country-duo .country-pick:focus {
    border-color: #2563eb;
    outline: none;
}
.phone-country-duo .phone-field-wrap {
    flex: 1;
    position: relative;
}
.phone-country-duo .phone-field-wrap .form-control {
    height: 44px;
    border-radius: 12px;
    border: 1.5px solid #e2e8f0;
    font-weight: 700;
    font-size: 0.88rem;
    letter-spacing: 0.5px;
    padding-inline-start: 0.85rem;
    padding-inline-end: 2.4rem;
}
.phone-country-duo .phone-field-wrap .trailing-icon {
    position: absolute;
    inset-inline-end: 0.85rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1rem;
    color: #94a3b8;
    pointer-events: none;
}

/* ─── Security Shield Alert ─── */
.security-alert-card {
    background: #f0f7ff;
    border: 1px solid #dbeafe;
    border-radius: 12px;
    padding: 0.7rem 0.9rem;
    display: flex;
    align-items: center;
    gap: 9px;
    margin-top: 0.75rem;
    margin-bottom: 1rem;
}
.security-alert-card i {
    font-size: 1.3rem;
    color: #2563eb;
    flex-shrink: 0;
}
.security-alert-card div {
    font-size: 0.75rem;
    color: #334155;
    line-height: 1.4;
}

/* ─── Calendar & Slots ─── */
.calendar-card-frame {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 0.9rem;
    margin-bottom: 0.85rem;
}
.app-calendar-box {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 8px 10px;
    margin-bottom: 0.75rem;
}
.app-calendar-month {
    font-size: 0.84rem;
    font-weight: 800;
    color: #0f172a;
}
.app-calendar-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    text-align: center;
    font-size: 0.68rem;
    font-weight: 800;
    color: #94a3b8;
    margin-bottom: 5px;
}
.app-calendar-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 3px;
}
.app-cal-day {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.78rem;
    font-weight: 700;
    border-radius: 8px;
    cursor: pointer;
    color: #1e293b;
    border: 1px solid transparent;
    transition: all 0.15s;
}
.app-cal-day:hover:not(.disabled) {
    background: #eff6ff;
    color: #2563eb;
}
.app-cal-day.selected {
    background: #2563eb !important;
    color: #ffffff !important;
    font-weight: 800;
}
.app-cal-day.today {
    border-color: #2563eb;
    color: #2563eb;
    font-weight: 800;
}
.app-cal-day.disabled {
    color: #cbd5e1;
    cursor: not-allowed;
    pointer-events: none;
}
.app-slots-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 6px;
}
@media (min-width: 576px) {
    .app-slots-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}
.app-slot-pill {
    padding: 7px 4px;
    font-size: 0.76rem;
    font-weight: 700;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    text-align: center;
    background: #ffffff;
    color: #334155;
    cursor: pointer;
    transition: all 0.15s;
    user-select: none;
}
.app-slot-pill:hover {
    border-color: #93c5fd;
    background: #eff6ff;
    color: #2563eb;
}
.app-slot-pill.selected {
    border-color: #2563eb;
    background: #2563eb;
    color: #ffffff;
}

/* ─── Screen 2: Payment & Order Summary ─── */
.order-summary-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 0.9rem 1rem;
    margin-bottom: 1rem;
}
.summary-top-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.35rem;
}
.summary-heading {
    font-size: 0.84rem;
    font-weight: 800;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 5px;
}
.summary-price-badge {
    font-size: 1.35rem;
    font-weight: 900;
    color: #0f172a;
}
.summary-feature-bullets {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 0.4rem;
    padding-top: 0.4rem;
    border-top: 1px dashed #cbd5e1;
    font-size: 0.74rem;
    font-weight: 700;
    color: #334155;
}

/* ─── Payment Methods Segmented Switcher ─── */
.pay-method-switcher {
    display: flex;
    background: #f1f5f9;
    padding: 4px;
    border-radius: 12px;
    gap: 4px;
    border: 1px solid #e2e8f0;
    margin-bottom: 0.75rem;
}
.pay-method-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    padding: 7px 4px;
    background: transparent;
    border: none;
    border-radius: 8px;
    color: #64748b;
    font-weight: 700;
    font-size: 0.78rem;
    cursor: pointer;
    transition: all 0.18s;
}
.pay-method-btn.active {
    background: #ffffff;
    color: #2563eb;
    box-shadow: 0 1px 4px rgba(15, 23, 42, 0.08);
}

.pay-qr-display-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 0.85rem;
    text-align: center;
    margin-bottom: 0.75rem;
}
.pay-qr-wrapper {
    display: inline-block;
    padding: 6px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    margin-bottom: 0.5rem;
}
.pay-qr-wrapper img {
    max-width: 120px;
    height: auto;
    display: block;
    border-radius: 6px;
}
.pay-instruction-note {
    font-size: 0.76rem;
    color: #475569;
    line-height: 1.4;
    margin: 0;
}

.proof-upload-box {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 0.85rem;
    margin-bottom: 0.75rem;
}
.receipt-drop-target {
    border: 1.5px dashed #cbd5e1;
    border-radius: 10px;
    padding: 8px;
    background: #f8fafc;
    cursor: pointer;
    text-align: center;
    transition: all 0.2s;
}
.receipt-drop-target:hover {
    border-color: #2563eb;
    background: #f0f7ff;
}

/* ─── Sticky Bottom Action Bar ─── */
.modal-bottom-bar-fixed {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: #ffffff;
    border-top: 1px solid #f1f5f9;
    padding: 0.75rem 1.4rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.05);
    z-index: 30;
    border-bottom-left-radius: 24px;
    border-bottom-right-radius: 24px;
}
.bottom-price-info {
    display: flex;
    flex-direction: column;
}
.bottom-price-val {
    font-size: 1.3rem;
    font-weight: 900;
    color: #0f172a;
    line-height: 1.1;
}
.bottom-price-lbl {
    font-size: 0.7rem;
    font-weight: 700;
    color: #64748b;
}
.btn-action-submit {
    background: #2563eb;
    color: #ffffff;
    border: none;
    border-radius: 12px;
    padding: 0.75rem 1.6rem;
    font-weight: 800;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    transition: all 0.18s;
    flex: 1;
    max-width: 280px;
}
.btn-action-submit:hover {
    background: #1d4ed8;
    color: #ffffff;
}
.btn-action-back {
    background: #f1f5f9;
    color: #475569;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 0.75rem 1.1rem;
    font-weight: 700;
    font-size: 0.88rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    cursor: pointer;
    transition: all 0.18s;
    flex-shrink: 0;
}
.btn-action-back:hover {
    background: #e2e8f0;
    color: #0f172a;
}
.step-summary-banner {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 0.75rem 0.95rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
}

/* ─── Screen 3: Voucher & Success ─── */
.success-check-bubble {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #10b981;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 1.8rem;
    margin: 0.5rem auto 0.8rem auto;
    box-shadow: 0 8px 20px rgba(16, 185, 129, 0.25);
}
.ticket-voucher-wrap {
    background: #f8fafc;
    border: 1.5px dashed #cbd5e1;
    border-radius: 16px;
    padding: 0.9rem 1.1rem;
    margin: 0.8rem 0;
    text-align: start;
}
.ticket-row-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.35rem 0;
    font-size: 0.82rem;
    border-bottom: 1px solid #f1f5f9;
}
.ticket-row-item:last-child {
    border-bottom: none;
}
</style>

<div class="modal fade" id="bookingModal" tabindex="-1" aria-hidden="true" dir="{{ $isArLocale ? 'rtl' : 'ltr' }}">
    <div class="modal-dialog modal-dialog-centered luxury-modal-dialog">
        <div class="modal-content luxury-modal-content position-relative {{ $isArLocale ? 'text-end' : 'text-start' }}">
            
            {{-- 1. Top Navy Header --}}
            <div class="modal-top-header">
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal" aria-label="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
                <div class="d-flex align-items-center gap-3">
                    <div class="header-phone-badge">
                        @if(!empty($siteLogo))
                            <img src="{{ $siteLogo }}" alt="Logo">
                        @else
                            <span style="font-size: 1.5rem; font-weight: 900; color: #60a5fa;">Ψ</span>
                        @endif
                        <span class="video-tag"><i class="bi bi-camera-video-fill"></i></span>
                    </div>
                    <div>
                        <div class="header-title-text">
                            <span>جلسة فورية</span>
                            <span style="color: #facc15;">⚡</span>
                        </div>
                        <p class="header-sub-text">{{ $isArLocale ? 'احجز استشارة مع الطبيب خلال دقائق' : 'Book a consultation with the doctor in minutes' }}</p>
                    </div>
                </div>
            </div>

            {{-- 2. Stepper Progress Bar (3 Clean Steps) --}}
            <div class="stepper-nav-bar">
                <div class="stepper-track-line"></div>
                <div class="stepper-node active" id="modal-step-1">
                    <div class="stepper-bubble">1</div>
                    <div class="stepper-caption">{{ $isArLocale ? 'الخدمة والموعد' : 'Service & Time' }}</div>
                </div>
                <div class="stepper-node" id="modal-step-2">
                    <div class="stepper-bubble">2</div>
                    <div class="stepper-caption">{{ $isArLocale ? 'بيانات المريض' : 'Patient Info' }}</div>
                </div>
                <div class="stepper-node" id="modal-step-3">
                    <div class="stepper-bubble">3</div>
                    <div class="stepper-caption">{{ $isArLocale ? 'الدفع والتأكيد' : 'Payment' }}</div>
                </div>
            </div>

            {{-- 3. Modal Body Content (Safe scroll padding) --}}
            <div class="modal-body-scrollable">
                
                {{-- ═══════════════════════════════════════════════════════════
                     الخطوة الأولى (Step 1): الخدمة وتحديد التاريخ والوقت
                     ═══════════════════════════════════════════════════════════ --}}
                <div id="app-screen-1">
                    
                    {{-- 1.1 نوع الحجز المطلوب --}}
                    <div class="card-section-label">
                        <i class="bi bi-grid-fill text-primary"></i>
                        <span>{{ $isArLocale ? 'نوع الحجز المطلوب' : 'Booking Type' }}</span>
                    </div>
                    <div class="type-choice-grid">
                        {{-- Online --}}
                        <div class="type-choice-card active" id="typeOptionOnline" onclick="setBookingCategory('online')">
                            <div class="type-card-top">
                                <i class="bi bi-camera-video text-primary fs-5"></i>
                                <div class="type-card-radio"></div>
                            </div>
                            <div class="type-card-name">{{ $isArLocale ? 'استشارة أونلاين' : 'Online Consultation' }}</div>
                            <div class="type-card-desc">{{ $isArLocale ? 'تم عبر مكالمة فيديو' : 'Via secure video call' }}</div>
                            <span class="type-selected-tag" id="badgeSelectedOnline">
                                <i class="bi bi-check2"></i> {{ $isArLocale ? 'محدد' : 'Selected' }}
                            </span>
                        </div>

                        {{-- Clinic --}}
                        <div class="type-choice-card" id="typeOptionClinic" onclick="setBookingCategory('clinic')">
                            <div class="type-card-top">
                                <i class="bi bi-hospital text-secondary fs-5"></i>
                                <div class="type-card-radio"></div>
                            </div>
                            <div class="type-card-name">{{ $isArLocale ? 'كشف بالعيادة' : 'Clinic Visit' }}</div>
                            <div class="type-card-desc">{{ $isArLocale ? 'بغداد / مقر العيادة' : 'Baghdad Clinic' }}</div>
                            <span class="type-selected-tag d-none" id="badgeSelectedClinic">
                                <i class="bi bi-check2"></i> {{ $isArLocale ? 'محدد' : 'Selected' }}
                            </span>
                        </div>
                    </div>

                    {{-- 1.2 تفاصيل الجلسة (3 Columns) --}}
                    <div class="session-stat-strip">
                        <div class="session-stat-item">
                            <div class="session-stat-title">
                                <i class="bi bi-tag text-primary"></i>
                                <span>{{ $isArLocale ? 'سعر الجلسة' : 'Price' }}</span>
                            </div>
                            <div class="session-stat-data text-primary" id="strip_session_price">
                                {{ $modalServices->first()->price ?? 50 }} {{ $currencySymbol }}
                            </div>
                        </div>
                        <div class="session-stat-item">
                            <div class="session-stat-title">
                                <i class="bi bi-clock text-primary"></i>
                                <span>{{ $isArLocale ? 'مدة الجلسة' : 'Duration' }}</span>
                            </div>
                            <div class="session-stat-data" id="strip_session_duration">
                                {{ $modalServices->first()->duration ?? 45 }} {{ $isArLocale ? 'دقيقة' : 'min' }}
                            </div>
                        </div>
                        <div class="session-stat-item">
                            <div class="session-stat-title">
                                <i class="bi bi-calendar-check text-primary"></i>
                                <span>{{ $isArLocale ? 'الموعد' : 'Date' }}</span>
                            </div>
                            <div class="session-stat-data text-success" id="strip_session_date">
                                {{ $isArLocale ? 'متاح اليوم' : 'Today' }}
                            </div>
                        </div>
                    </div>

                    {{-- 1.3 اختيار نوع الخدمة التخصصية --}}
                    <div class="input-row-block">
                        <label class="input-row-label">{{ $isArLocale ? 'اختر الخدمة أو الاستشارة' : 'Select Service' }}</label>
                        <div class="field-box-wrap">
                            <select class="form-select" id="app_service_select" onchange="onModalServiceChanged(this)">
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
                                        {{ $isArLocale ? ($s->title_ar ?: $s->title) : ($s->title_en ?: $s->title) }} ({{ $s->duration }} {{ $isArLocale ? 'دقيقة' : 'min' }})
                                    </option>
                                @endforeach
                            </select>
                            <i class="bi bi-chevron-down trailing-icon"></i>
                        </div>
                    </div>

                    {{-- 1.4 موضوع الاستشارة --}}
                    <div class="input-row-block">
                        <label class="input-row-label">{{ $isArLocale ? 'عنوان وموضوع الاستشارة' : 'Consultation Subject' }}</label>
                        <div class="field-box-wrap">
                            <input type="text" id="app_consultation_title" class="form-control" placeholder="{{ $isArLocale ? 'اكتب موضوعاً مختصراً' : 'Short subject' }}" value="{{ $isArLocale ? 'استشارة نفسية متخصصة' : 'Specialized Consultation' }}" required>
                            <i class="bi bi-chat-dots trailing-icon"></i>
                        </div>
                    </div>

                    {{-- 1.5 التاريخ والمواعيد المتاحة --}}
                    <div class="calendar-card-frame">
                        <div class="card-section-label mb-2">
                            <i class="bi bi-calendar3 text-primary"></i>
                            <span>{{ $isArLocale ? 'تحديد الموعد والتاريخ' : 'Select Date & Time' }}</span>
                        </div>
                        
                        {{-- تقويم الشهر التفاعلي --}}
                        <div class="app-calendar-box">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <button type="button" class="btn btn-sm btn-light rounded-circle p-1" onclick="changeAppMonth(-1)"><i class="bi bi-chevron-right"></i></button>
                                <div class="app-calendar-month" id="app-calendar-month-title">{{ date('F Y') }}</div>
                                <button type="button" class="btn btn-sm btn-light rounded-circle p-1" onclick="changeAppMonth(1)"><i class="bi bi-chevron-left"></i></button>
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

                        {{-- شبكة الأوقات --}}
                        <div>
                            <label class="input-row-label mb-1.5"><i class="bi bi-clock me-1 text-primary"></i> {{ $isArLocale ? 'الأوقات المتاحة' : 'Available Slots' }}</label>
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

                </div>{{-- End Screen 1 --}}

                {{-- ═══════════════════════════════════════════════════════════
                     الخطوة الثانية (Step 2): بيانات المريض وحسابه
                     ═══════════════════════════════════════════════════════════ --}}
                <div id="app-screen-2" class="d-none">
                    
                    {{-- 2.1 شريط ملخص الخدمة والموعد المختار --}}
                    <div class="step-summary-banner">
                        <div class="d-flex align-items-center gap-2 overflow-hidden">
                            <div class="p-2 bg-primary-subtle text-primary rounded-3">
                                <i class="bi bi-calendar-check-fill fs-5"></i>
                            </div>
                            <div class="overflow-hidden">
                                <div class="fw-bold text-dark text-truncate small" id="step2-summary-service">{{ $modalServices->first()->title ?? 'استشارة نفسية متخصصة' }}</div>
                                <div class="text-muted" style="font-size: 0.76rem;" id="step2-summary-datetime">—</div>
                            </div>
                        </div>
                        <span class="badge bg-primary text-white px-2.5 py-1.5 rounded-pill fw-bold" id="step2-summary-price">
                            {{ $modalServices->first()->price ?? 50 }} {{ $currencySymbol }}
                        </span>
                    </div>

                    {{-- 2.2 بيانات المريض --}}
                    <div class="card-section-label">
                        <i class="bi bi-person-fill text-primary"></i>
                        <span>{{ $isArLocale ? 'بيانات المريض للتواصل' : 'Patient Information' }}</span>
                        <span id="app_user_status_badge" class="badge bg-light text-muted border small ms-auto d-none"></span>
                    </div>

                    {{-- الاسم بالكامل --}}
                    <div class="input-row-block">
                        <label class="input-row-label">{{ $isArLocale ? 'الاسم بالكامل' : 'Full Name' }} <span class="text-danger">*</span></label>
                        <div class="field-box-wrap">
                            <input type="text" id="app_user_name" class="form-control" 
                                   placeholder="{{ $isArLocale ? 'أدخل الاسم بالكامل' : 'Enter full name' }}" 
                                   value="{{ Auth::check() ? Auth::user()->name : '' }}" 
                                   oninput="savePatientBookingToStorage()" required>
                            <i class="bi bi-person trailing-icon"></i>
                        </div>
                    </div>

                    {{-- رقم الواتساب مع كود البلد --}}
                    <div class="input-row-block">
                        <label class="input-row-label">{{ $isArLocale ? 'رقم الواتساب' : 'WhatsApp Number' }} <span class="text-danger">*</span></label>
                        <div class="phone-country-duo" dir="ltr">
                            <select class="country-pick" id="app_country_code" onchange="onModalCountryCodeChanged(this)">
                                <option value="+964" selected>🇮🇶 +964</option>
                                <option value="+966">🇸🇦 +966</option>
                                <option value="+971">🇦🇪 +971</option>
                                <option value="+965">🇰🇼 +965</option>
                                <option value="+974">🇶🇦 +974</option>
                                <option value="+968">🇴🇲 +968</option>
                                <option value="+973">🇧🇭 +973</option>
                                <option value="+962">🇯🇴 +962</option>
                                <option value="+20">🇪🇬 +20</option>
                                <option value="+961">🇱🇧 +961</option>
                                <option value="+963">🇸🇾 +963</option>
                                <option value="+970">🇵🇸 +970</option>
                                <option value="+967">🇾🇪 +967</option>
                                <option value="+218">🇱🇾 +218</option>
                                <option value="+249">🇸🇩 +249</option>
                                <option value="+213">🇩🇿 +213</option>
                                <option value="+212">🇲🇦 +212</option>
                                <option value="+216">🇹🇳 +216</option>
                                <option value="+90">🇹🇷 +90</option>
                                <option value="+44">🇬🇧 +44</option>
                                <option value="+1">🇺🇸 +1</option>
                                <option value="+49">🇩🇪 +49</option>
                                <option value="+46">🇸🇪 +46</option>
                                <option value="+33">🇫🇷 +33</option>
                                <option value="+31">🇳🇱 +31</option>
                                <option value="+61">🇦🇺 +61</option>
                                <option value="+41">🇨🇭 +41</option>
                                <option value="+43">🇦🇹 +43</option>
                                <option value="+47">🇳🇴 +47</option>
                                <option value="+45">🇩🇰 +45</option>
                                <option value="+32">🇧🇪 +32</option>
                                <option value="+39">🇮🇹 +39</option>
                                <option value="+34">🇪🇸 +34</option>
                            </select>
                            <div class="phone-field-wrap">
                                <input type="tel" id="app_user_phone" class="form-control" placeholder="7701234567" value="{{ Auth::check() ? preg_replace('/^\+964/', '', Auth::user()->phone ?? '') : '' }}" oninput="savePatientBookingToStorage(); checkUserRegistrationStatus();" required>
                                <i class="bi bi-telephone trailing-icon"></i>
                            </div>
                        </div>
                    </div>

                    {{-- البريد الإلكتروني --}}
                    <div class="input-row-block">
                        <label class="input-row-label">{{ $isArLocale ? 'البريد الإلكتروني (لاستلام تفاصيل الموعد)' : 'Email' }}</label>
                        <div class="field-box-wrap">
                            <input type="email" id="app_user_email" class="form-control" 
                                   placeholder="name@example.com" 
                                   value="{{ Auth::check() ? Auth::user()->email : '' }}" 
                                   oninput="savePatientBookingToStorage()">
                            <i class="bi bi-envelope trailing-icon"></i>
                        </div>
                    </div>

                    {{-- كلمة المرور للمستخدم الجديد --}}
                    <div class="input-row-block" id="app_password_wrapper" style="{{ Auth::check() ? 'display:none;' : '' }}">
                        <label class="input-row-label" id="app_password_label">{{ $isArLocale ? 'كلمة المرور' : 'Password' }} <span class="text-danger">*</span></label>
                        <div class="field-box-wrap">
                            <input type="password" id="app_user_password" class="form-control" 
                                   placeholder="{{ $isArLocale ? 'أدخل كلمة المرور' : 'Enter password' }}" minlength="6">
                            <button type="button" class="pwd-eye-btn" onclick="togglePasswordVisibility('app_user_password')">
                                <i class="bi bi-eye-slash" id="app_user_password_eye"></i>
                            </button>
                            <i class="bi bi-lock trailing-icon"></i>
                        </div>
                        <div class="form-text text-muted small" id="app_password_hint">{{ $isArLocale ? 'يرجى تعيين كلمة مرور لإنشاء حسابك ومتابعة مواعيدك.' : 'Create password for your patient dashboard.' }}</div>
                    </div>

                    {{-- أمان البيانات --}}
                    <div class="security-alert-card mt-3">
                        <i class="bi bi-shield-check"></i>
                        <div>
                            <strong class="d-block mb-0.5 text-dark">{{ $isArLocale ? 'معلوماتك آمنة ومحمية' : 'Your data is safe' }}</strong>
                            <span>{{ $isArLocale ? 'نستخدم أحدث تقنيات التشفير لحماية بياناتك الشخصية وسرية الجلسة.' : 'We use end-to-end encryption to protect your privacy.' }}</span>
                        </div>
                    </div>

                </div>{{-- End Screen 2 --}}

                {{-- ═══════════════════════════════════════════════════════════
                     الخطوة الثالثة (Step 3): الملخص وطرق الدفع والإيصال
                     ═══════════════════════════════════════════════════════════ --}}
                <div id="app-screen-3" class="d-none">
                    
                    {{-- بطاقة إجمالي المبلغ --}}
                    <div class="order-summary-card">
                        <div class="summary-top-row">
                            <span class="summary-heading">
                                <i class="bi bi-cart3 text-primary"></i>
                                <span>{{ $isArLocale ? 'إجمالي المبلغ' : 'Order Total' }}</span>
                            </span>
                            <span class="summary-price-badge" id="app-screen3-total">
                                {{ $modalServices->first()->price ?? 50 }} {{ $currencySymbol }}
                            </span>
                        </div>
                        <div class="summary-feature-bullets">
                            <span id="screen3_feature_type"><i class="bi bi-check-circle-fill text-success"></i> {{ $isArLocale ? 'استشارة أونلاين' : 'Online Consultation' }}</span>
                            <span id="screen3_feature_dur"><i class="bi bi-check-circle-fill text-success"></i> {{ $modalServices->first()->duration ?? 45 }} {{ $isArLocale ? 'دقيقة' : 'min' }}</span>
                            <span><i class="bi bi-check-circle-fill text-success"></i> {{ $isArLocale ? 'حجز فوري ومباشر' : 'Direct Booking' }}</span>
                        </div>
                    </div>

                    {{-- طريقة الدفع --}}
                    @if($anyPaymentActive)
                    <div class="mb-3">
                        <div class="card-section-label mb-2">
                            <i class="bi bi-wallet2 text-primary"></i>
                            <span>{{ $isArLocale ? 'طريقة الدفع' : 'Payment Method' }}</span>
                        </div>
                        
                        {{-- Segmented Switcher --}}
                        <div class="pay-method-switcher" id="payment-method-tabs" role="tablist">
                            @foreach($paymentMethods as $mId => $pm)
                            <button type="button" class="pay-method-btn {{ $defaultPayMethod === $mId ? 'active' : '' }}"
                                    id="pay-tab-{{ $mId }}" onclick="switchPayTab('{{ $mId }}')">
                                @if(!empty($pm['logo']))
                                    <img src="{{ $pm['logo'] }}" alt="{{ $pm['name'] }}" style="width:20px;height:20px;object-fit:contain;border-radius:4px;" class="me-1">
                                @else
                                    <i class="bi {{ $pm['icon_class'] ?? 'bi-wallet2' }}"></i>
                                @endif
                                <span>{{ $pm['name'] }}</span>
                            </button>
                            @endforeach
                        </div>

                        {{-- Display Panels for each Payment Method --}}
                        @foreach($paymentMethods as $mId => $pm)
                        <div id="pay-panel-{{ $mId }}" class="pay-qr-display-card {{ $defaultPayMethod !== $mId ? 'd-none' : '' }}">
                            @if($mId === 'card')
                                <div class="d-flex align-items-center justify-content-between mb-2 border-bottom pb-2">
                                    <span class="fw-bold text-dark small"><i class="bi bi-shield-check text-success me-1"></i> {{ $pm['badge'] ?? ($isArLocale ? 'دفع إلكتروني آمن' : 'Secure Card Payment') }}</span>
                                    <div class="d-flex gap-1" dir="ltr">
                                        @if(!empty($pm['logo']))
                                            <img src="{{ $pm['logo'] }}" alt="Card" style="height:22px;object-fit:contain;">
                                        @else
                                            <span class="badge bg-white text-primary border shadow-sm">VISA</span>
                                            <span class="badge bg-white text-danger border shadow-sm">MasterCard</span>
                                        @endif
                                    </div>
                                </div>
                                <p class="pay-instruction-note text-secondary mb-2">{{ $pm['instructions'] }}</p>
                                @if(!empty($pm['link']))
                                <a href="{{ $pm['link'] }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill fw-bold w-100 py-1.5">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> {{ $isArLocale ? 'فتح رابط الدفع الإلكتروني' : 'Open Payment Link' }}
                                </a>
                                @endif
                            @else
                                @if(!empty($pm['qr_image']))
                                    <div class="pay-qr-wrapper">
                                        <img src="{{ $pm['qr_image'] }}" alt="{{ $pm['name'] }} QR">
                                    </div>
                                    <p class="pay-instruction-note">{{ $pm['instructions'] }}</p>
                                @else
                                    <div class="py-2 text-center">
                                        @if(!empty($pm['logo']))
                                            <img src="{{ $pm['logo'] }}" alt="{{ $pm['name'] }}" class="mb-2" style="max-height:48px;object-fit:contain;">
                                        @else
                                            <i class="bi {{ $pm['icon_class'] ?? 'bi-qr-code' }} text-primary fs-2"></i>
                                        @endif
                                        <p class="pay-instruction-note mt-1">{{ $pm['instructions'] }}</p>
                                    </div>
                                @endif
                            @endif
                        </div>
                        @endforeach

                        {{-- رقم هاتف المحول والإيصال --}}
                        <div class="proof-upload-box mt-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="input-row-label mb-0">
                                    <i class="bi bi-phone text-primary me-1"></i> {{ $isArLocale ? 'رقم هاتف المحوّل / رقم التحويل' : 'Sender Phone / Transfer #' }}
                                </label>
                                <span class="badge bg-light text-muted border small">{{ $isArLocale ? 'اختياري' : 'Optional' }}</span>
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-hash"></i></span>
                                <input type="tel" id="app_transfer_number" class="form-control" placeholder="{{ $isArLocale ? 'رقم الهاتف / المحفظة التي تم التحويل منها' : 'Sender wallet / phone number' }}">
                            </div>

                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="input-row-label mb-0">
                                    <i class="bi bi-image text-success me-1"></i> {{ $isArLocale ? 'إرفاق سكرين شوت الإيصال' : 'Attach Receipt Screenshot' }}
                                </label>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle small">{{ $isArLocale ? 'يسرّع التأكيد' : 'Fast Confirm' }}</span>
                            </div>

                            <div id="receiptUploadBox" class="receipt-drop-target" onclick="document.getElementById('app_receipt_file').click()">
                                <input type="file" id="app_receipt_file" class="d-none" accept="image/*" onchange="onReceiptImageSelected(this)">
                                
                                <div id="receiptPlaceholderView" class="py-1">
                                    <i class="bi bi-cloud-arrow-up text-primary fs-3 d-block mb-1"></i>
                                    <div class="fw-bold text-dark small">{{ $isArLocale ? 'انقر لرفع صورة الإيصال' : 'Click to upload receipt screenshot' }}</div>
                                    <div class="text-muted" style="font-size: 0.72rem;">PNG, JPG, WEBP</div>
                                </div>

                                <div id="receiptPreviewView" class="d-none align-items-center justify-content-between gap-2 text-start">
                                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                                        <img id="receiptPreviewImg" src="" alt="Preview" class="rounded border" style="width: 40px; height: 40px; object-fit: cover;">
                                        <div class="overflow-hidden {{ $isArLocale ? 'text-end' : 'text-start' }}">
                                            <div class="fw-bold text-dark text-truncate small" id="receiptFileName">receipt.png</div>
                                            <div class="text-success" style="font-size: 0.72rem;"><i class="bi bi-check-circle-fill me-1"></i> {{ $isArLocale ? 'تم إرفاق الإيصال' : 'Receipt attached' }}</div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-circle p-1" onclick="event.stopPropagation(); removeReceiptImage();" style="width:26px;height:26px; display:flex; align-items:center; justify-content:center;">
                                        <i class="bi bi-trash3-fill" style="font-size:0.75rem;"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                    @endif

                    {{-- الشروط والأحكام --}}
                    <div class="form-check my-2">
                        <input class="form-check-input" type="checkbox" id="app_terms_check" checked style="cursor:pointer;">
                        <label class="form-check-label fw-bold text-secondary small" for="app_terms_check" style="cursor:pointer;">
                            {{ $isArLocale ? 'أوافق على الشروط وسياسة الخصوصية الخاصة بالحجز' : 'I agree to the terms and booking policy' }}
                        </label>
                    </div>

                </div>{{-- End Screen 3 --}}

                {{-- ═══════════════════════════════════════════════════════════
                     شاشة النجاح النهائية: بطاقة التأكيد وتذكرة الموعد
                     ═══════════════════════════════════════════════════════════ --}}
                <div id="app-screen-success" class="d-none">
                    <div class="text-center py-1">
                        <div class="success-check-bubble">
                            <i class="bi bi-check-lg"></i>
                        </div>
                        <h5 class="fw-black text-dark mb-1">{{ $isArLocale ? 'تم استلام طلب الحجز بنجاح' : 'Booking Request Received' }}</h5>
                        <p class="text-muted small mb-3">{{ $isArLocale ? 'طلبك قيد المتابعة وسيتم إشعارك فور التأكيد' : 'Your request is being processed' }}</p>

                        {{-- بطاقة التذكرة --}}
                        <div class="ticket-voucher-wrap">
                            <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                                <span class="fw-bold text-dark small"><i class="bi bi-ticket-perforated-fill text-primary me-1"></i> {{ $isArLocale ? 'تذكرة الحجز الإلكترونية' : 'E-Ticket' }}</span>
                                <span class="badge bg-primary rounded-pill px-2 py-0.5" id="app-res-ref">#BK-REF</span>
                            </div>
                            <div class="ticket-row-item">
                                <span class="text-muted">{{ $isArLocale ? 'الخدمة' : 'Service' }}</span>
                                <span class="fw-bold text-dark" id="app-res-service">{{ $modalServices->first()->title ?? 'جلسة استشارة' }}</span>
                            </div>
                            <div class="ticket-row-item">
                                <span class="text-muted">{{ $isArLocale ? 'الموعد' : 'Date & Time' }}</span>
                                <span class="fw-bold text-dark font-monospace" id="app-res-datetime">—</span>
                            </div>
                            <div class="ticket-row-item">
                                <span class="text-muted">{{ $isArLocale ? 'طريقة الدفع' : 'Payment' }}</span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill fw-bold" id="app-res-paymethod">زين كاش</span>
                            </div>
                            <div class="ticket-row-item pt-2">
                                <span class="fw-bold text-dark">{{ $isArLocale ? 'المبلغ' : 'Amount' }}</span>
                                <span class="fw-black text-primary fs-5" id="app-res-type">50 {{ $currencySymbol }}</span>
                            </div>
                        </div>

                        {{-- أزرار الإجراءات --}}
                        <div class="d-flex flex-column gap-2 mt-3">
                            <a id="app-dashboard-link" href="{{ route('patient.dashboard') }}" class="btn btn-primary rounded-3 py-2 fw-bold w-100">
                                <i class="bi bi-speedometer2 me-1"></i>
                                <span>{{ $isArLocale ? 'الانتقال للوحة التحكم ومتابعة الموعد' : 'Go to Dashboard' }}</span>
                            </a>
                            <button type="button" class="btn btn-light rounded-3 py-2 fw-bold text-secondary" data-bs-dismiss="modal" onclick="window.location.reload()">
                                <span>{{ $isArLocale ? 'إغلاق والعودة للرئيسية' : 'Close' }}</span>
                            </button>
                        </div>
                    </div>
                </div>{{-- End Screen Success --}}

            </div>

            {{-- 4. Sticky Bottom Action Bar --}}
            <div class="modal-bottom-bar-fixed" id="app-bottom-bar">
                <div class="bottom-price-info">
                    <div class="bottom-price-val" id="app-bottom-total">{{ $modalServices->first()->price ?? 50 }} {{ $currencySymbol }}</div>
                    <div class="bottom-price-lbl">{{ $isArLocale ? 'إجمالي المبلغ' : 'Total' }}</div>
                </div>
                
                {{-- أزرار الخطوة 1 --}}
                <div id="bar-actions-step-1" class="d-flex align-items-center gap-2 flex-grow-1 justify-content-end">
                    <button type="button" class="btn-action-submit" onclick="goToAppScreen2()">
                        <span>{{ $isArLocale ? 'التالي: بيانات المريض' : 'Next: Patient Info' }}</span>
                        <i class="bi {{ $isArLocale ? 'bi-arrow-left' : 'bi-arrow-right' }}"></i>
                    </button>
                </div>

                {{-- أزرار الخطوة 2 --}}
                <div id="bar-actions-step-2" class="d-none align-items-center gap-2 flex-grow-1 justify-content-end">
                    <button type="button" class="btn-action-back" onclick="goToAppScreen1()">
                        <i class="bi {{ $isArLocale ? 'bi-arrow-right' : 'bi-arrow-left' }}"></i>
                        <span>{{ $isArLocale ? 'رجوع' : 'Back' }}</span>
                    </button>
                    <button type="button" class="btn-action-submit" onclick="goToAppScreen3()">
                        <span>{{ $isArLocale ? 'التالي: الدفع' : 'Next: Payment' }}</span>
                        <i class="bi {{ $isArLocale ? 'bi-arrow-left' : 'bi-arrow-right' }}"></i>
                    </button>
                </div>

                {{-- أزرار الخطوة 3 --}}
                <div id="bar-actions-step-3" class="d-none align-items-center gap-2 flex-grow-1 justify-content-end">
                    <button type="button" class="btn-action-back" onclick="goToAppScreen2()">
                        <i class="bi {{ $isArLocale ? 'bi-arrow-right' : 'bi-arrow-left' }}"></i>
                        <span>{{ $isArLocale ? 'رجوع' : 'Back' }}</span>
                    </button>
                    <button type="button" class="btn-action-submit" id="btn-flow-submit" onclick="executeAppBooking()">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>{{ $isArLocale ? 'تأكيد الحجز النهائي' : 'Confirm Booking' }}</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

@php
    $modalI18n = [
        'months' => $isArLocale ? [
            'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو',
            'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'
        ] : [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ],
        'minutes' => $isArLocale ? 'دقيقة' : 'min',
        'enter_subject' => $isArLocale ? 'يرجى إدخال عنوان أو موضوع الاستشارة' : 'Please enter subject',
        'enter_name' => $isArLocale ? 'يرجى إدخال الاسم بالكامل' : 'Please enter full name',
        'enter_phone' => $isArLocale ? 'يرجى إدخال رقم الهاتف' : 'Please enter phone number',
        'select_slot' => $isArLocale ? 'يرجى اختيار الوقت المناسب للجلسة' : 'Please select a time slot',
        'enter_password' => $isArLocale ? 'يرجى إدخال كلمة المرور' : 'Please enter password',
        'terms_required' => $isArLocale ? 'يرجى الموافقة على الشروط وسياسة الحجز' : 'Please accept terms',
        'loading_slots' => $isArLocale ? 'جاري تحميل الأوقات المتاحة...' : 'Loading slots...',
        'no_slots' => $isArLocale ? 'لا توجد أوقات متاحة' : 'No available slots',
        'failed_slots' => $isArLocale ? 'تعذر جلب الأوقات' : 'Failed to load slots',
        'existing_user' => $isArLocale ? 'عميل مسجل مسبقاً' : 'Registered Client',
        'new_user' => $isArLocale ? 'عميل جديد' : 'New Client',
        'submitting' => $isArLocale ? 'جاري إرسال الطلب...' : 'Submitting...',
        'request_failed' => $isArLocale ? 'حدث خطأ أثناء تنفيذ الطلب' : 'Request failed',
        'confirm_btn' => $isArLocale ? 'تأكيد الحجز وإرسال الإيصال' : 'Confirm Booking',
        'pay_zain' => $isArLocale ? 'زين كاش' : 'ZainCash',
        'pay_superki' => 'SuperKi',
        'pay_card' => $isArLocale ? 'بطاقة دفع' : 'Card',
        'past_date' => $isArLocale ? 'تاريخ سابق غير متاح' : 'Past date unavailable',
        'default_service' => $isArLocale ? 'استشارة نفسية متخصصة' : 'Specialized Consultation',
        'is_ar' => $isArLocale,
        'slot_am' => $isArLocale ? 'ص' : 'AM',
        'slot_pm' => $isArLocale ? 'م' : 'PM',
    ];
@endphp

<script>
// ════ Shared Booking Modal JS Engine ════
const _i18n = {!! json_encode($modalI18n, JSON_UNESCAPED_UNICODE) !!};
const _paymentMethods = {!! json_encode($paymentMethods, JSON_UNESCAPED_UNICODE) !!};
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
    title: '{{ $modalServices->first()->title ?? ($isArLocale ? "استشارة نفسية متخصصة" : "Consultation") }}',
    details: '',
    date: _initDateStr,
    slot: '09:00 AM',
    year: _initYear,
    month: _initMonth,
};

const waNumber = '{{ preg_replace("/\D/", "", $waRaw) }}';
let appUserIsRegistered = {{ Auth::check() ? 'true' : 'false' }};
let checkPhoneTimeout = null;
const appCurrencySymbol = '{{ $currencySymbol }}';

function setBookingCategory(type) {
    appState.bookingType = type;
    const isOnline = type === 'online';
    
    const cardOnline = document.getElementById('typeOptionOnline');
    const cardClinic = document.getElementById('typeOptionClinic');
    const badgeOnline = document.getElementById('badgeSelectedOnline');
    const badgeClinic = document.getElementById('badgeSelectedClinic');
    
    if (cardOnline) cardOnline.classList.toggle('active', isOnline);
    if (cardClinic) cardClinic.classList.toggle('active', !isOnline);
    if (badgeOnline) badgeOnline.classList.toggle('d-none', !isOnline);
    if (badgeClinic) badgeClinic.classList.toggle('d-none', isOnline);
    
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

    const durStrip = document.getElementById('strip_session_duration');
    if (durStrip) durStrip.textContent = dur + ' ' + _i18n.minutes;
    const durFeature = document.getElementById('screen2_feature_dur');
    if (durFeature) durFeature.innerHTML = '<i class="bi bi-check-circle-fill text-success"></i> ' + dur + ' ' + _i18n.minutes;
    
    const p = appState.bookingType === 'clinic'
        ? (opt.getAttribute('data-clinic') || opt.getAttribute('data-price'))
        : (opt.getAttribute('data-price') || opt.getAttribute('data-video') || opt.getAttribute('data-voice') || opt.getAttribute('data-chat'));
    if (p) updateModalPrice(parseFloat(p));
    
    const titleInput = document.getElementById('app_consultation_title');
    if (titleInput) titleInput.value = appState.title;

    fetchModalSlots(appState.date);
}

function updateModalPrice(price) {
    appState.price = price;
    const pText = price + ' ' + appCurrencySymbol;
    if (document.getElementById('strip_session_price')) document.getElementById('strip_session_price').textContent = pText;
    if (document.getElementById('app-bottom-total')) document.getElementById('app-bottom-total').textContent = pText;
    if (document.getElementById('step2-summary-price')) document.getElementById('step2-summary-price').textContent = pText;
    if (document.getElementById('app-screen3-total')) document.getElementById('app-screen3-total').textContent = pText;
    if (document.getElementById('app-res-type')) document.getElementById('app-res-type').textContent = pText;
}

function setModalStep(step) {
    for (let i = 1; i <= 3; i++) {
        const item = document.getElementById(`modal-step-${i}`);
        if (!item) continue;
        item.classList.remove('active', 'completed');
        const bubble = item.querySelector('.stepper-bubble');
        if (i < step) {
            item.classList.add('completed');
            if (bubble) bubble.innerHTML = '<i class="bi bi-check-lg"></i>';
        } else if (i === step) {
            item.classList.add('active');
            if (bubble) bubble.textContent = i;
        } else {
            if (bubble) bubble.textContent = i;
        }
    }
}

function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);
    const eye = document.getElementById(inputId + '_eye');
    if (!input) return;
    if (input.type === 'password') {
        input.type = 'text';
        if (eye) eye.className = 'bi bi-eye';
    } else {
        input.type = 'password';
        if (eye) eye.className = 'bi bi-eye-slash';
    }
}

function onModalCountryCodeChanged(selectEl) {
    savePatientBookingToStorage();
    checkUserRegistrationStatus();
}

function getFullPhoneNumber() {
    const countryCode = document.getElementById('app_country_code')?.value || '+964';
    let phone = (document.getElementById('app_user_phone')?.value || '').trim();
    phone = phone.replace(/^0+/, '');
    return countryCode + phone;
}

function checkUserRegistrationStatus() {
    clearTimeout(checkPhoneTimeout);
    const phoneInput = document.getElementById('app_user_phone');
    const badge = document.getElementById('app_user_status_badge');
    const passWrapper = document.getElementById('app_password_wrapper');
    const passLabel = document.getElementById('app_password_label');
    const passHint = document.getElementById('app_password_hint');
    
    if (!phoneInput || !badge) return;
    const phone = phoneInput.value.trim();
    if (phone.length < 7) {
        badge.classList.add('d-none');
        return;
    }

    checkPhoneTimeout = setTimeout(() => {
        const fullPhone = getFullPhoneNumber();
        fetch(`/booking/check-user?phone=${encodeURIComponent(fullPhone)}`)
            .then(res => res.json())
            .then(data => {
                badge.classList.remove('d-none');
                if (data.exists) {
                    appUserIsRegistered = true;
                    badge.className = 'badge bg-success-subtle text-success border border-success-subtle px-2 py-1 ms-auto';
                    badge.innerHTML = '<i class="bi bi-person-check-fill me-1"></i> ' + _i18n.existing_user;
                    if (data.name && !document.getElementById('app_user_name').value) {
                        document.getElementById('app_user_name').value = data.name;
                    }
                    if (data.email && !document.getElementById('app_user_email').value) {
                        document.getElementById('app_user_email').value = data.email;
                    }
                    if (passWrapper) passWrapper.style.display = 'block';
                    if (passLabel) passLabel.innerHTML = '{{ $isArLocale ? "كلمة المرور الخاصة بحسابك" : "Account Password" }} <span class="text-danger">*</span>';
                    if (passHint) passHint.textContent = '{{ $isArLocale ? "أدخل كلمة المرور الخاصة بحسابك لتأكيد الحجز." : "Enter your account password." }}';
                } else {
                    appUserIsRegistered = false;
                    badge.className = 'badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 ms-auto';
                    badge.innerHTML = '<i class="bi bi-person-plus-fill me-1"></i> ' + _i18n.new_user;
                    if (passWrapper) passWrapper.style.display = 'block';
                    if (passLabel) passLabel.innerHTML = '{{ $isArLocale ? "كلمة المرور" : "Password" }} <span class="text-danger">*</span>';
                    if (passHint) passHint.textContent = '{{ $isArLocale ? "يرجى تعيين كلمة مرور لإنشاء حسابك ومتابعة مواعيدك." : "Set a password for your account." }}';
                }
            })
            .catch(() => {});
    }, 450);
}

function savePatientBookingToStorage() {
    try {
        const data = {
            name: document.getElementById('app_user_name')?.value || '',
            phone: document.getElementById('app_user_phone')?.value || '',
            countryCode: document.getElementById('app_country_code')?.value || '+964',
            email: document.getElementById('app_user_email')?.value || '',
            subject: document.getElementById('app_consultation_title')?.value || '',
        };
        localStorage.setItem('yonis_patient_booking_draft', JSON.stringify(data));
    } catch (e) {}
}

function restorePatientBookingData() {
    try {
        const raw = localStorage.getItem('yonis_patient_booking_draft');
        if (!raw) return;
        const data = JSON.parse(raw);
        if (data.name && !document.getElementById('app_user_name').value) document.getElementById('app_user_name').value = data.name;
        if (data.phone && !document.getElementById('app_user_phone').value) document.getElementById('app_user_phone').value = data.phone;
        if (data.countryCode && document.getElementById('app_country_code')) document.getElementById('app_country_code').value = data.countryCode;
        if (data.email && !document.getElementById('app_user_email').value) document.getElementById('app_user_email').value = data.email;
        if (data.subject && !document.getElementById('app_consultation_title').value) document.getElementById('app_consultation_title').value = data.subject;
    } catch (e) {}
}

function renderAppCalendar() {
    const titleEl = document.getElementById('app-calendar-month-title');
    const gridEl = document.getElementById('app-calendar-days-grid');
    if (!titleEl || !gridEl) return;

    titleEl.textContent = `${_i18n.months[appState.month]} ${appState.year}`;
    gridEl.innerHTML = '';

    const firstDay = new Date(appState.year, appState.month, 1).getDay();
    const totalDays = new Date(appState.year, appState.month + 1, 0).getDate();
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    for (let i = 0; i < firstDay; i++) {
        const empty = document.createElement('div');
        empty.className = 'app-cal-day disabled';
        gridEl.appendChild(empty);
    }

    for (let d = 1; d <= totalDays; d++) {
        const dayEl = document.createElement('div');
        dayEl.className = 'app-cal-day';
        dayEl.textContent = d;

        const dateStr = `${appState.year}-${String(appState.month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
        const checkDate = new Date(appState.year, appState.month, d);

        if (checkDate < today) {
            dayEl.classList.add('disabled');
        } else {
            if (checkDate.getTime() === today.getTime()) dayEl.classList.add('today');
            if (dateStr === appState.date) dayEl.classList.add('selected');

            dayEl.onclick = () => selectAppDate(dateStr, dayEl);
        }
        gridEl.appendChild(dayEl);
    }
}

function changeAppMonth(delta) {
    appState.month += delta;
    if (appState.month < 0) {
        appState.month = 11;
        appState.year--;
    } else if (appState.month > 11) {
        appState.month = 0;
        appState.year++;
    }
    renderAppCalendar();
}

function selectAppDate(dateStr, el) {
    document.querySelectorAll('.app-cal-day.selected').forEach(d => d.classList.remove('selected'));
    el.classList.add('selected');
    appState.date = dateStr;
    
    const stripDate = document.getElementById('strip_session_date');
    if (stripDate) stripDate.textContent = dateStr;
    
    fetchModalSlots(dateStr);
}

function fetchModalSlots(dateStr) {
    const grid = document.getElementById('app-slots-grid');
    if (!grid) return;
    grid.innerHTML = `<div class="col-12 text-center text-muted py-2 small"><div class="spinner-border spinner-border-sm text-primary me-1"></div> ${_i18n.loading_slots}</div>`;

    fetch(`/booking/available-slots?date=${encodeURIComponent(dateStr)}&service_id=${encodeURIComponent(appState.serviceId)}`)
        .then(res => res.json())
        .then(data => {
            grid.innerHTML = '';
            const rawSlots = data.available_slots || data.slots || (Array.isArray(data) ? data : []);
            if (rawSlots && rawSlots.length > 0) {
                rawSlots.forEach((slot, idx) => {
                    const slotValue = (typeof slot === 'object' && slot !== null) ? (slot.start || slot.time_formatted || '') : String(slot || '');
                    const slotDisplay = (typeof slot === 'object' && slot !== null) ? (slot.time_formatted || slot.start || '') : (_i18n.is_ar ? String(slot).replace('AM', _i18n.slot_am).replace('PM', _i18n.slot_pm) : String(slot));
                    
                    const pill = document.createElement('div');
                    pill.className = `app-slot-pill ${idx === 0 ? 'selected' : ''}`;
                    pill.textContent = slotDisplay;
                    pill.onclick = () => selectAppSlot(slotValue, pill);
                    grid.appendChild(pill);
                    if (idx === 0) appState.slot = slotValue;
                });
            } else {
                const defaultSlots = ['09:00 AM', '10:00 AM', '11:30 AM', '01:00 PM', '02:30 PM', '04:00 PM'];
                defaultSlots.forEach((slot, idx) => {
                    const pill = document.createElement('div');
                    pill.className = `app-slot-pill ${idx === 0 ? 'selected' : ''}`;
                    pill.textContent = _i18n.is_ar ? slot.replace('AM', _i18n.slot_am).replace('PM', _i18n.slot_pm) : slot;
                    pill.onclick = () => selectAppSlot(slot, pill);
                    grid.appendChild(pill);
                    if (idx === 0) appState.slot = slot;
                });
            }
        })
        .catch(() => {
            const fallbackSlots = ['10:00 AM', '11:00 AM', '12:30 PM', '02:00 PM', '03:30 PM'];
            grid.innerHTML = '';
            fallbackSlots.forEach((slot, idx) => {
                const pill = document.createElement('div');
                pill.className = `app-slot-pill ${idx === 0 ? 'selected' : ''}`;
                pill.textContent = _i18n.is_ar ? slot.replace('AM', _i18n.slot_am).replace('PM', _i18n.slot_pm) : slot;
                pill.onclick = () => selectAppSlot(slot, pill);
                grid.appendChild(pill);
                if (idx === 0) appState.slot = slot;
            });
        });
}

function selectAppSlot(slot, el) {
    const slotValue = (typeof slot === 'object' && slot !== null) ? (slot.start || slot.time_formatted || '') : String(slot || '');
    document.querySelectorAll('.app-slot-pill.selected').forEach(p => p.classList.remove('selected'));
    if (el) el.classList.add('selected');
    appState.slot = slotValue;
}

// ═════ SCREEN TRANSITIONS (3 STEPS) ═════

function goToAppScreen1() {
    document.getElementById('app-screen-1')?.classList.remove('d-none');
    document.getElementById('app-screen-2')?.classList.add('d-none');
    document.getElementById('app-screen-3')?.classList.add('d-none');
    document.getElementById('app-screen-success')?.classList.add('d-none');
    
    document.getElementById('app-bottom-bar')?.classList.remove('d-none');
    
    const bar1 = document.getElementById('bar-actions-step-1');
    const bar2 = document.getElementById('bar-actions-step-2');
    const bar3 = document.getElementById('bar-actions-step-3');
    if (bar1) { bar1.classList.remove('d-none'); bar1.classList.add('d-flex'); }
    if (bar2) { bar2.classList.add('d-none'); bar2.classList.remove('d-flex'); }
    if (bar3) { bar3.classList.add('d-none'); bar3.classList.remove('d-flex'); }
    
    setModalStep(1);

    const modalBody = document.querySelector('.modal-body-scrollable');
    if (modalBody) modalBody.scrollTop = 0;
}

function goToAppScreen2() {
    const title = (document.getElementById('app_consultation_title')?.value || '').trim();
    if (!title) { 
        document.getElementById('app_consultation_title').value = appState.title;
    }
    if (!appState.slot) { 
        alert(_i18n.select_slot); 
        return; 
    }

    // تحديث بانر ملخص الحجز في الخطوة 2
    if (document.getElementById('step2-summary-service')) {
        document.getElementById('step2-summary-service').textContent = appState.title;
    }
    if (document.getElementById('step2-summary-datetime')) {
        const slotDisplay = _i18n.is_ar ? String(appState.slot).replace('AM', _i18n.slot_am).replace('PM', _i18n.slot_pm) : appState.slot;
        document.getElementById('step2-summary-datetime').textContent = `${appState.date} | ${slotDisplay}`;
    }
    if (document.getElementById('step2-summary-price')) {
        document.getElementById('step2-summary-price').textContent = appState.price + ' ' + appCurrencySymbol;
    }

    document.getElementById('app-screen-1')?.classList.add('d-none');
    document.getElementById('app-screen-2')?.classList.remove('d-none');
    document.getElementById('app-screen-3')?.classList.add('d-none');
    document.getElementById('app-screen-success')?.classList.add('d-none');

    document.getElementById('app-bottom-bar')?.classList.remove('d-none');

    const bar1 = document.getElementById('bar-actions-step-1');
    const bar2 = document.getElementById('bar-actions-step-2');
    const bar3 = document.getElementById('bar-actions-step-3');
    if (bar1) { bar1.classList.add('d-none'); bar1.classList.remove('d-flex'); }
    if (bar2) { bar2.classList.remove('d-none'); bar2.classList.add('d-flex'); }
    if (bar3) { bar3.classList.add('d-none'); bar3.classList.remove('d-flex'); }

    setModalStep(2);

    const modalBody = document.querySelector('.modal-body-scrollable');
    if (modalBody) modalBody.scrollTop = 0;
}

function goToAppScreen3() {
    const name = (document.getElementById('app_user_name')?.value || '').trim();
    const phone = (document.getElementById('app_user_phone')?.value || '').trim();

    if (!name) { alert(_i18n.enter_name); return; }
    if (!phone) { alert(_i18n.enter_phone); return; }

    @if(!Auth::check())
    const password = document.getElementById('app_user_password')?.value || '';
    if (!password) { alert(_i18n.enter_password); return; }
    @endif

    savePatientBookingToStorage();

    // تحديث بيانات بطاقة الدفع في الخطوة 3
    if (document.getElementById('app-screen3-total')) {
        document.getElementById('app-screen3-total').textContent = appState.price + ' ' + appCurrencySymbol;
    }
    if (document.getElementById('screen3_feature_dur')) {
        document.getElementById('screen3_feature_dur').innerHTML = `<i class="bi bi-check-circle-fill text-success"></i> ${appState.duration} ${_i18n.minutes}`;
    }
    if (document.getElementById('screen3_feature_type')) {
        document.getElementById('screen3_feature_type').innerHTML = `<i class="bi bi-check-circle-fill text-success"></i> ${appState.bookingType === 'clinic' ? '{{ $isArLocale ? "كشف بالعيادة" : "Clinic Visit" }}' : '{{ $isArLocale ? "استشارة أونلاين" : "Online Consultation" }}'}`;
    }

    document.getElementById('app-screen-1')?.classList.add('d-none');
    document.getElementById('app-screen-2')?.classList.add('d-none');
    document.getElementById('app-screen-3')?.classList.remove('d-none');
    document.getElementById('app-screen-success')?.classList.add('d-none');

    document.getElementById('app-bottom-bar')?.classList.remove('d-none');

    const bar1 = document.getElementById('bar-actions-step-1');
    const bar2 = document.getElementById('bar-actions-step-2');
    const bar3 = document.getElementById('bar-actions-step-3');
    if (bar1) { bar1.classList.add('d-none'); bar1.classList.remove('d-flex'); }
    if (bar2) { bar2.classList.add('d-none'); bar2.classList.remove('d-flex'); }
    if (bar3) { bar3.classList.remove('d-none'); bar3.classList.add('d-flex'); }

    setModalStep(3);

    const modalBody = document.querySelector('.modal-body-scrollable');
    if (modalBody) modalBody.scrollTop = 0;
}

function switchPayTab(method) {
    appState.paymentMethod = method;
    document.querySelectorAll('.pay-method-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.pay-qr-display-card').forEach(panel => panel.classList.add('d-none'));

    const tabBtn = document.getElementById('pay-tab-' + method);
    if (tabBtn) tabBtn.classList.add('active');

    const targetPanel = document.getElementById('pay-panel-' + method);
    if (targetPanel) targetPanel.classList.remove('d-none');
}

function onReceiptImageSelected(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('receiptPreviewImg').src = e.target.result;
            document.getElementById('receiptFileName').textContent = file.name;
            document.getElementById('receiptPlaceholderView').classList.add('d-none');
            const preview = document.getElementById('receiptPreviewView');
            preview.classList.remove('d-none');
            preview.classList.add('d-flex');
        };
        reader.readAsDataURL(file);
    }
}

function removeReceiptImage() {
    const input = document.getElementById('app_receipt_file');
    if (input) input.value = '';
    document.getElementById('receiptPlaceholderView').classList.remove('d-none');
    const preview = document.getElementById('receiptPreviewView');
    preview.classList.add('d-none');
    preview.classList.remove('d-flex');
}

function executeAppBooking() {
    const termsCheck = document.getElementById('app_terms_check');
    if (termsCheck && !termsCheck.checked) {
        alert(_i18n.terms_required);
        return;
    }

    const btn = document.getElementById('btn-flow-submit');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> ' + _i18n.submitting;
    }

    const name = document.getElementById('app_user_name')?.value.trim() || '';
    const fullPhone = getFullPhoneNumber();
    const email = document.getElementById('app_user_email')?.value.trim() || '';
    const password = document.getElementById('app_user_password')?.value || '';
    const title = document.getElementById('app_consultation_title')?.value.trim() || _i18n.default_service;
    const transferNumber = document.getElementById('app_transfer_number')?.value.trim() || '';
    const receiptFile = document.getElementById('app_receipt_file')?.files[0];

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    const finalDate = typeof appState.date === 'string' ? appState.date : (appState.date?.date || '');
    const finalSlot = typeof appState.slot === 'string' ? appState.slot : (appState.slot?.start || appState.slot?.time_formatted || '');

    const formData = new FormData();
    formData.append('service_id', appState.serviceId);
    formData.append('booking_type', appState.bookingType);
    formData.append('name', name);
    formData.append('phone', fullPhone);
    formData.append('email', email);
    formData.append('title', title);
    formData.append('date', finalDate);
    formData.append('slot', finalSlot);
    formData.append('payment_method', appState.paymentMethod);
    formData.append('transfer_number', transferNumber);
    if (password) formData.append('password', password);
    if (receiptFile) formData.append('receipt_image', receiptFile);

    fetch('/booking/request', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(async res => {
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || _i18n.request_failed);
        return data;
    })
    .then(resData => {
        setModalStep(3);

        document.getElementById('app-screen-1')?.classList.add('d-none');
        document.getElementById('app-screen-2')?.classList.add('d-none');
        document.getElementById('app-screen-3')?.classList.add('d-none');
        document.getElementById('app-screen-success')?.classList.remove('d-none');
        document.getElementById('app-bottom-bar')?.classList.add('d-none');

        const ref = resData.booking_ref || resData.reference || `BK-${Math.floor(100000 + Math.random() * 900000)}`;
        if (document.getElementById('app-res-ref')) document.getElementById('app-res-ref').textContent = '#' + ref;
        if (document.getElementById('app-res-service')) document.getElementById('app-res-service').textContent = appState.title;
        const slotDisplay = _i18n.is_ar ? String(appState.slot).replace('AM', _i18n.slot_am).replace('PM', _i18n.slot_pm) : appState.slot;
        if (document.getElementById('app-res-datetime')) document.getElementById('app-res-datetime').textContent = `${appState.date} | ${slotDisplay}`;
        
        let payLabel = _paymentMethods[appState.paymentMethod]?.name || appState.paymentMethod;
        if (document.getElementById('app-res-paymethod')) document.getElementById('app-res-paymethod').textContent = payLabel;
        if (document.getElementById('app-res-type')) document.getElementById('app-res-type').textContent = appState.price + ' ' + appCurrencySymbol;

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
            btn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> ' + _i18n.confirm_btn;
        }
        alert(err.message || _i18n.request_failed);
    });
}

// Lifecycle
document.addEventListener('DOMContentLoaded', function() {
    restorePatientBookingData();
    renderAppCalendar();
    fetchModalSlots(appState.date);

    const modalEl = document.getElementById('bookingModal');
    if (modalEl) {
        modalEl.addEventListener('show.bs.modal', function() {
            goToAppScreen1();
            restorePatientBookingData();
            renderAppCalendar();
            fetchModalSlots(appState.date);
        });
    }
});
</script>
