@php
    $modalServices = $services ?? \App\Models\Service::where('is_active', true)->get();
    $waRaw = \App\Models\Setting::get('whatsapp_number', '+9647700000000');
    $isArLocale = app()->getLocale() === 'ar';
    $siteLogo = \App\Models\Setting::getFileUrl('site_logo', '');
    $currencySymbol = \App\Models\Setting::currencySymbol();

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

{{-- ═══ MODERN LUXURY BOOKING MODAL (MATCHING UX/UI SPECIFICATION) ═══ --}}
<style>
/* ─── Modal Architecture & Sizing ─── */
.luxury-modal-dialog {
    max-width: 620px !important;
    margin: 1.25rem auto !important;
}
@media (min-width: 992px) {
    .luxury-modal-dialog {
        max-width: 680px !important;
    }
}
.luxury-modal-content {
    border-radius: 28px !important;
    border: 1px solid rgba(226, 232, 240, 0.8) !important;
    box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.35) !important;
    overflow: hidden !important;
    background: #ffffff !important;
}

/* ─── Top Hero Dark Header ─── */
.luxury-modal-hero {
    background: linear-gradient(135deg, #0a1628 0%, #0f2444 50%, #173767 100%);
    padding: 1.5rem 1.6rem 1.35rem 1.6rem;
    position: relative;
    color: #ffffff;
    overflow: hidden;
}
.luxury-modal-hero::after {
    content: '';
    position: absolute;
    top: -40px;
    right: -40px;
    width: 140px;
    height: 140px;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.3) 0%, rgba(59, 130, 246, 0) 70%);
    border-radius: 50%;
    pointer-events: none;
}
.hero-close-btn {
    position: absolute;
    top: 1rem;
    inset-inline-end: 1rem;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    z-index: 10;
}
.hero-close-btn:hover {
    background: rgba(255, 255, 255, 0.25);
    color: #ffffff;
    transform: scale(1.05);
}
.hero-phone-mockup {
    width: 58px;
    height: 58px;
    border-radius: 18px;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0.05));
    border: 1.5px solid rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(8px);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
    flex-shrink: 0;
}
.hero-phone-mockup img {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    object-fit: cover;
}
.hero-phone-mockup .video-badge {
    position: absolute;
    bottom: -4px;
    inset-inline-end: -4px;
    width: 22px;
    height: 22px;
    background: #2563eb;
    border: 2px solid #ffffff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.65rem;
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(37, 99, 235, 0.6);
}
.hero-title {
    font-size: 1.28rem;
    font-weight: 800;
    margin-bottom: 0.2rem;
    letter-spacing: -0.2px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.hero-subtitle {
    font-size: 0.85rem;
    color: #cbd5e1;
    margin: 0;
}

/* ─── Stepper Progress Bar ─── */
.luxury-stepper-bar {
    background: #ffffff;
    border-bottom: 1px solid #f1f5f9;
    padding: 0.85rem 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
}
.step-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    position: relative;
    z-index: 2;
    flex: 1;
    text-align: center;
}
.step-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 0.85rem;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    background: #ffffff;
    color: #94a3b8;
    border: 2px solid #e2e8f0;
}
.step-item.active .step-circle {
    background: #2563eb;
    border-color: #2563eb;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
    transform: scale(1.08);
}
.step-item.completed .step-circle {
    background: #10b981;
    border-color: #10b981;
    color: #ffffff;
}
.step-label {
    font-size: 0.78rem;
    font-weight: 700;
    color: #64748b;
    transition: color 0.3s;
}
.step-item.active .step-label {
    color: #0f172a;
    font-weight: 800;
}
.step-item.completed .step-label {
    color: #10b981;
}
.stepper-connector {
    position: absolute;
    top: 25px;
    left: 12%;
    right: 12%;
    height: 2px;
    background: #e2e8f0;
    z-index: 1;
}

/* ─── Modal Body & Scroll ─── */
.luxury-modal-body {
    padding: 1.35rem 1.6rem 5.6rem 1.6rem !important;
    max-height: calc(88vh - 120px);
    overflow-y: auto;
    background: #ffffff;
    scrollbar-width: thin;
}
.luxury-modal-body::-webkit-scrollbar {
    width: 5px;
}
.luxury-modal-body::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}

/* ─── Section Header ─── */
.section-headline {
    font-size: 0.92rem;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* ─── Booking Type Interactive Option Cards ─── */
.booking-type-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 1.15rem;
}
.type-card-option {
    border: 1.5px solid #e2e8f0;
    border-radius: 16px;
    padding: 0.9rem 1rem;
    background: #ffffff;
    cursor: pointer;
    transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    user-select: none;
}
.type-card-option:hover {
    border-color: #93c5fd;
    background: #f8fafc;
}
.type-card-option.active {
    border-color: #2563eb;
    background: #f0f7ff;
    box-shadow: 0 4px 16px rgba(37, 99, 235, 0.12);
}
.type-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.4rem;
}
.type-icon {
    font-size: 1.35rem;
    color: #2563eb;
}
.type-radio-dot {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    border: 2px solid #cbd5e1;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}
.type-card-option.active .type-radio-dot {
    border-color: #2563eb;
    background: #2563eb;
}
.type-card-option.active .type-radio-dot::after {
    content: '';
    width: 6px;
    height: 6px;
    background: #ffffff;
    border-radius: 50%;
}
.type-title {
    font-size: 0.88rem;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 0.15rem;
}
.type-subtitle {
    font-size: 0.74rem;
    color: #64748b;
    margin-bottom: 0.4rem;
}
.type-badge-selected {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
    font-size: 0.68rem;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 20px;
    align-self: flex-start;
}

/* ─── Session Details 3-Col Bar (تفاصيل الجلسة) ─── */
.session-info-strip {
    background: #f0f7ff;
    border: 1px solid #dbeafe;
    border-radius: 16px;
    padding: 0.85rem 1rem;
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 8px;
    margin-bottom: 1.25rem;
    text-align: center;
}
.session-info-col {
    position: relative;
}
.session-info-col:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 15%;
    bottom: 15%;
    inset-inline-end: 0;
    width: 1px;
    background: #bfdbfe;
}
.session-info-label {
    font-size: 0.72rem;
    font-weight: 700;
    color: #475569;
    margin-bottom: 2px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
}
.session-info-val {
    font-size: 0.88rem;
    font-weight: 800;
    color: #0f172a;
}

/* ─── Order Summary Floating Card (إجمالي الطلب) ─── */
.order-summary-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 1rem 1.15rem;
    margin-bottom: 1.25rem;
}
.order-summary-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.5rem;
}
.order-summary-title {
    font-size: 0.85rem;
    font-weight: 800;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 5px;
}
.order-summary-price {
    font-size: 1.45rem;
    font-weight: 900;
    color: #0f172a;
}
.order-summary-features {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 0.5rem;
    padding-top: 0.5rem;
    border-top: 1px dashed #cbd5e1;
    font-size: 0.76rem;
    font-weight: 700;
    color: #334155;
}
.order-summary-features span {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* ─── Form Inputs & Icons ─── */
.form-group-custom {
    margin-bottom: 0.85rem;
}
.form-label-custom {
    font-size: 0.8rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.35rem;
    display: block;
}
.input-icon-wrap {
    position: relative;
    display: flex;
    align-items: center;
}
.input-icon-wrap .form-control,
.input-icon-wrap .form-select {
    height: 46px;
    border-radius: 14px;
    border: 1.5px solid #e2e8f0;
    font-size: 0.86rem;
    font-weight: 600;
    color: #0f172a;
    background: #ffffff;
    transition: all 0.2s ease;
    padding-inline-start: 1rem;
    padding-inline-end: 2.5rem;
}
.input-icon-wrap .form-control:focus,
.input-icon-wrap .form-select:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3.5px rgba(37, 99, 235, 0.12);
    outline: none;
}
.input-icon-wrap .field-icon {
    position: absolute;
    inset-inline-end: 1rem;
    font-size: 1.1rem;
    color: #94a3b8;
    pointer-events: none;
}
.input-icon-wrap .toggle-password-btn {
    position: absolute;
    inset-inline-end: 2.4rem;
    border: none;
    background: transparent;
    color: #94a3b8;
    font-size: 1rem;
    cursor: pointer;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ─── Phone & Country Select Combo ─── */
.phone-select-combo {
    display: flex;
    align-items: center;
    gap: 8px;
}
.phone-select-combo .country-dropdown {
    width: 140px;
    flex-shrink: 0;
    height: 46px;
    border-radius: 14px;
    border: 1.5px solid #e2e8f0;
    background: #f8fafc;
    font-size: 0.82rem;
    font-weight: 700;
    color: #1e293b;
    padding-inline-start: 0.75rem;
    padding-inline-end: 1.5rem;
    cursor: pointer;
}
.phone-select-combo .country-dropdown:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    outline: none;
}
.phone-select-combo .phone-input-wrap {
    flex: 1;
    position: relative;
}
.phone-select-combo .phone-input-wrap .form-control {
    height: 46px;
    border-radius: 14px;
    border: 1.5px solid #e2e8f0;
    font-weight: 700;
    font-size: 0.9rem;
    letter-spacing: 0.5px;
    padding-inline-start: 0.9rem;
    padding-inline-end: 2.5rem;
}
.phone-select-combo .phone-input-wrap .field-icon {
    position: absolute;
    inset-inline-end: 1rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.05rem;
    color: #94a3b8;
    pointer-events: none;
}

/* ─── Security Shield Alert ─── */
.security-note-box {
    background: #f0f7ff;
    border: 1px solid #dbeafe;
    border-radius: 14px;
    padding: 0.75rem 1rem;
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 0.9rem;
    margin-bottom: 1.15rem;
}
.security-note-icon {
    font-size: 1.4rem;
    color: #2563eb;
    flex-shrink: 0;
}
.security-note-text {
    font-size: 0.76rem;
    color: #334155;
    line-height: 1.4;
}
.security-note-text strong {
    color: #0f172a;
    display: block;
    margin-bottom: 1px;
}

/* ─── Calendar & Time Slots ─── */
.calendar-collapsible-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    padding: 1rem;
    margin-bottom: 1rem;
}
.app-calendar-box {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 10px 12px;
    margin-bottom: 0.85rem;
}
.app-calendar-month {
    font-size: 0.86rem;
    font-weight: 800;
    color: #0f172a;
}
.app-calendar-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    text-align: center;
    font-size: 0.7rem;
    font-weight: 800;
    color: #94a3b8;
    margin-bottom: 6px;
}
.app-calendar-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 4px;
}
.app-cal-day {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: 700;
    border-radius: 10px;
    cursor: pointer;
    color: #1e293b;
    border: 1px solid transparent;
    transition: all 0.18s ease;
}
.app-cal-day:hover:not(.disabled) {
    background: #eff6ff;
    border-color: #bfdbfe;
    color: #2563eb;
}
.app-cal-day.selected {
    background: #2563eb !important;
    color: #ffffff !important;
    box-shadow: 0 4px 10px rgba(37, 99, 235, 0.35);
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
    background: #f8fafc;
}
.app-slots-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
}
@media (min-width: 576px) {
    .app-slots-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}
.app-slot-pill {
    padding: 8px 6px;
    font-size: 0.78rem;
    font-weight: 700;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    text-align: center;
    background: #ffffff;
    color: #334155;
    cursor: pointer;
    transition: all 0.18s ease;
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
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
}

/* ─── Sticky Bottom Action Bar ─── */
.luxury-bottom-bar {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: #ffffff;
    border-top: 1px solid #f1f5f9;
    padding: 0.85rem 1.6rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.05);
    z-index: 20;
    border-bottom-left-radius: 28px;
    border-bottom-right-radius: 28px;
}
.bottom-total-col {
    display: flex;
    flex-direction: column;
}
.bottom-total-amount {
    font-size: 1.35rem;
    font-weight: 900;
    color: #0f172a;
    line-height: 1.1;
}
.bottom-total-sub {
    font-size: 0.72rem;
    font-weight: 700;
    color: #64748b;
}
.btn-primary-flow {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    color: #ffffff;
    border: none;
    border-radius: 14px;
    padding: 0.8rem 1.75rem;
    font-weight: 800;
    font-size: 0.92rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.35);
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    flex: 1;
    max-width: 320px;
}
.btn-primary-flow:hover {
    background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
    transform: translateY(-1px);
    box-shadow: 0 8px 25px rgba(37, 99, 235, 0.45);
    color: #ffffff;
}

/* ─── Payment Methods Segmented Switcher (Screen 2) ─── */
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
    padding: 8px 6px;
    background: transparent;
    border: none;
    border-radius: 10px;
    color: #64748b;
    font-weight: 700;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.2s ease;
}
.pay-toggle-btn.active.zain {
    background: #ffffff;
    color: #6d28d9;
    box-shadow: 0 2px 10px rgba(109, 40, 217, 0.16), 0 0 0 1.5px #7c3aed;
}
.pay-toggle-btn.active.superki {
    background: #ffffff;
    color: #0369a1;
    box-shadow: 0 2px 10px rgba(2, 132, 199, 0.16), 0 0 0 1.5px #0284c7;
}
.pay-toggle-btn.active.card {
    background: #ffffff;
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
    font-size: 0.8rem;
}
.pay-toggle-icon.zain { background: rgba(124, 58, 237, 0.12); color: #7c3aed; }
.pay-toggle-icon.superki { background: rgba(2, 132, 199, 0.12); color: #0284c7; }
.pay-toggle-icon.card { background: rgba(37, 99, 235, 0.12); color: #2563eb; }

.pay-qr-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 1rem;
    text-align: center;
    margin-top: 0.75rem;
}
.pay-qr-img-box {
    display: inline-block;
    padding: 8px;
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.05);
    margin-bottom: 0.65rem;
}
.pay-qr-img-box img {
    max-width: 130px;
    height: auto;
    display: block;
    border-radius: 8px;
}
.pay-qr-label {
    font-size: 0.78rem;
    color: #475569;
    line-height: 1.45;
    margin-bottom: 0;
}
.pay-proof-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 1rem;
    margin-top: 0.85rem;
}
.pay-upload-dropzone {
    border: 1.5px dashed #cbd5e1;
    border-radius: 12px;
    padding: 10px;
    background: #f8fafc;
    cursor: pointer;
    text-align: center;
    transition: all 0.2s ease;
}
.pay-upload-dropzone:hover {
    border-color: #2563eb;
    background: #f0f7ff;
}

/* ─── Screen 3: Voucher & Success ─── */
.luxury-status-circle {
    width: 68px;
    height: 68px;
    border-radius: 50%;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 2rem;
    margin: 0.5rem auto 1rem auto;
    box-shadow: 0 10px 25px rgba(16, 185, 129, 0.35);
}
.luxury-voucher-card {
    background: #f8fafc;
    border: 1.5px dashed #cbd5e1;
    border-radius: 18px;
    padding: 1rem 1.25rem;
    margin: 1rem 0;
    text-align: start;
}
.voucher-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.4rem 0;
    font-size: 0.84rem;
    border-bottom: 1px solid #f1f5f9;
}
.voucher-row:last-child {
    border-bottom: none;
}
</style>

<div class="modal fade" id="bookingModal" tabindex="-1" aria-hidden="true" dir="{{ $isArLocale ? 'rtl' : 'ltr' }}">
    <div class="modal-dialog modal-dialog-centered luxury-modal-dialog">
        <div class="modal-content luxury-modal-content position-relative {{ $isArLocale ? 'text-end' : 'text-start' }}">
            
            {{-- 1. Hero Dark Banner (جلسة فورية مع الطبيب) --}}
            <div class="luxury-modal-hero">
                <button type="button" class="hero-close-btn" data-bs-dismiss="modal" aria-label="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
                <div class="d-flex align-items-center gap-3">
                    <div class="hero-phone-mockup">
                        @if(!empty($siteLogo))
                            <img src="{{ $siteLogo }}" alt="Clinic Logo">
                        @else
                            <span style="font-size: 1.7rem; font-weight: 900; color: #60a5fa;">Ψ</span>
                        @endif
                        <span class="video-badge"><i class="bi bi-camera-video-fill"></i></span>
                    </div>
                    <div>
                        <div class="hero-title">
                            <span class="text-primary-light" style="color: #60a5fa;">⚡</span>
                            <span>{{ __('messages.immediate_session') }}</span>
                        </div>
                        <p class="hero-subtitle">{{ $isArLocale ? 'احجز استشارة مع الطبيب خلال دقائق' : 'Book a consultation with the doctor in minutes' }}</p>
                    </div>
                </div>
            </div>

            {{-- 2. Stepper Bar (الخدمة - بيانات المريض - الدفع - التأكيد) --}}
            <div class="luxury-stepper-bar">
                <div class="stepper-connector"></div>
                <div class="step-item active" id="modal-step-1">
                    <div class="step-circle">1</div>
                    <div class="step-label">{{ __('messages.service_title') ?? 'الخدمة' }}</div>
                </div>
                <div class="step-item" id="modal-step-2">
                    <div class="step-circle">2</div>
                    <div class="step-label">{{ __('messages.patient_details') ?? 'بيانات المريض' }}</div>
                </div>
                <div class="step-item" id="modal-step-3">
                    <div class="step-circle">3</div>
                    <div class="step-label">{{ __('messages.payment_step') ?? 'الدفع' }}</div>
                </div>
                <div class="step-item" id="modal-step-4">
                    <div class="step-circle">4</div>
                    <div class="step-label">{{ __('messages.confirmation_step') ?? 'التأكيد' }}</div>
                </div>
            </div>

            {{-- 3. Modal Body Content --}}
            <div class="luxury-modal-body">
                
                {{-- ═══════════════════════════════════════════════════════════
                     الخطوة الأولى (Step 1): الاختيار والبيانات والتاريخ
                     ═══════════════════════════════════════════════════════════ --}}
                <div id="app-screen-1">
                    
                    {{-- 1.1 نوع الحجز المطلوب (Interactive Cards) --}}
                    <div class="section-headline">
                        <i class="bi bi-grid-fill text-primary"></i>
                        <span>{{ __('messages.booking_type') }}</span>
                    </div>
                    <div class="booking-type-grid">
                        {{-- Option Online --}}
                        <div class="type-card-option active" id="typeOptionOnline" onclick="setBookingCategory('online')">
                            <div class="type-card-header">
                                <i class="bi bi-camera-video-fill type-icon"></i>
                                <div class="type-radio-dot"></div>
                            </div>
                            <div class="type-title">{{ __('messages.online_consultation') }}</div>
                            <div class="type-subtitle">{{ $isArLocale ? 'تم عبر مكالمة فيديو' : 'Via secure video call' }}</div>
                            <span class="type-badge-selected" id="badgeSelectedOnline">
                                <i class="bi bi-check2"></i> {{ $isArLocale ? 'محدد' : 'Selected' }}
                            </span>
                        </div>

                        {{-- Option Clinic --}}
                        <div class="type-card-option" id="typeOptionClinic" onclick="setBookingCategory('clinic')">
                            <div class="type-card-header">
                                <i class="bi bi-hospital type-icon text-secondary"></i>
                                <div class="type-radio-dot"></div>
                            </div>
                            <div class="type-title">{{ __('messages.clinic_consultation') }}</div>
                            <div class="type-subtitle">{{ $isArLocale ? 'بغداد / مقر العيادة' : 'In Clinic / Baghdad' }}</div>
                            <span class="type-badge-selected d-none" id="badgeSelectedClinic">
                                <i class="bi bi-check2"></i> {{ $isArLocale ? 'محدد' : 'Selected' }}
                            </span>
                        </div>
                    </div>

                    {{-- 1.2 تفاصيل الجلسة الشريطية (Session Details Strip) --}}
                    <div class="section-headline">
                        <i class="bi bi-clock-history text-primary"></i>
                        <span>{{ $isArLocale ? 'تفاصيل الجلسة' : 'Session Details' }}</span>
                    </div>
                    <div class="session-info-strip">
                        <div class="session-info-col">
                            <div class="session-info-label">
                                <i class="bi bi-tag-fill text-primary"></i>
                                <span>{{ $isArLocale ? 'سعر الجلسة' : 'Session Price' }}</span>
                            </div>
                            <div class="session-info-val text-primary" id="strip_session_price">
                                {{ $modalServices->first()->price ?? 50 }} {{ $currencySymbol }}
                            </div>
                        </div>
                        <div class="session-info-col">
                            <div class="session-info-label">
                                <i class="bi bi-stopwatch-fill text-primary"></i>
                                <span>{{ $isArLocale ? 'مدة الجلسة' : 'Duration' }}</span>
                            </div>
                            <div class="session-info-val" id="strip_session_duration">
                                {{ $modalServices->first()->duration ?? 45 }} {{ __('messages.minutes') }}
                            </div>
                        </div>
                        <div class="session-info-col">
                            <div class="session-info-label">
                                <i class="bi bi-calendar-event-fill text-primary"></i>
                                <span>{{ $isArLocale ? 'الموعد' : 'Date' }}</span>
                            </div>
                            <div class="session-info-val text-success" id="strip_session_date">
                                {{ $isArLocale ? 'متاح اليوم' : 'Available Today' }}
                            </div>
                        </div>
                    </div>

                    {{-- 1.3 اختيار نوع الخدمة التخصصية --}}
                    <div class="form-group-custom">
                        <label class="form-label-custom">{{ __('messages.select_service') }}</label>
                        <div class="input-icon-wrap">
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
                                        {{ $isArLocale ? ($s->title_ar ?: $s->title) : ($s->title_en ?: $s->title) }} ({{ $s->duration }} {{ __('messages.minutes') }})
                                    </option>
                                @endforeach
                            </select>
                            <i class="bi bi-chevron-down field-icon"></i>
                        </div>
                    </div>

                    {{-- 1.4 عنوان أو موضوع الاستشارة --}}
                    <div class="form-group-custom">
                        <label class="form-label-custom">{{ __('messages.consultation_subject') }}</label>
                        <div class="input-icon-wrap">
                            <input type="text" id="app_consultation_title" class="form-control" placeholder="{{ __('messages.consultation_subject_ph') }}" value="{{ __('messages.consultation_subject_default') }}" required>
                            <i class="bi bi-chat-left-dots field-icon"></i>
                        </div>
                    </div>

                    {{-- 1.5 بيانات المريض (Patient Information) --}}
                    <div class="pt-2">
                        <div class="section-headline">
                            <i class="bi bi-person-badge-fill text-primary"></i>
                            <span>{{ __('messages.patient_details') }}</span>
                            <span id="app_user_status_badge" class="badge bg-light text-muted border small ms-auto d-none"></span>
                        </div>

                        {{-- الاسم بالكامل --}}
                        <div class="form-group-custom">
                            <label class="form-label-custom">{{ __('messages.full_name') }} <span class="text-danger">*</span></label>
                            <div class="input-icon-wrap">
                                <input type="text" id="app_user_name" class="form-control" 
                                       placeholder="{{ $isArLocale ? 'أدخل الاسم بالكامل' : 'Enter full name' }}" 
                                       value="{{ Auth::check() ? Auth::user()->name : '' }}" 
                                       oninput="savePatientBookingToStorage()" required>
                                <i class="bi bi-person field-icon"></i>
                            </div>
                        </div>

                        {{-- رقم الواتساب مع كود الدولة --}}
                        <div class="form-group-custom">
                            <label class="form-label-custom">{{ __('messages.whatsapp_number') }} <span class="text-danger">*</span></label>
                            <div class="phone-select-combo" dir="ltr">
                                <select class="country-dropdown" id="app_country_code" onchange="onModalCountryCodeChanged(this)">
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
                                <div class="phone-input-wrap">
                                    <input type="tel" id="app_user_phone" class="form-control" placeholder="7701234567" value="{{ Auth::check() ? preg_replace('/^\+964/', '', Auth::user()->phone ?? '') : '' }}" oninput="savePatientBookingToStorage(); checkUserRegistrationStatus();" required>
                                    <i class="bi bi-telephone field-icon"></i>
                                </div>
                            </div>
                        </div>

                        {{-- البريد الإلكتروني --}}
                        <div class="form-group-custom">
                            <label class="form-label-custom">{{ __('messages.email_label') }} ({{ $isArLocale ? 'يمكنك الموعد واستلام التفاصيل' : 'To receive appointment confirmation' }}) <span class="text-danger">*</span></label>
                            <div class="input-icon-wrap">
                                <input type="email" id="app_user_email" class="form-control" 
                                       placeholder="name@example.com" 
                                       value="{{ Auth::check() ? Auth::user()->email : '' }}" 
                                       oninput="savePatientBookingToStorage()">
                                <i class="bi bi-envelope field-icon"></i>
                            </div>
                        </div>

                        {{-- كلمة المرور للمستخدم الجديد --}}
                        <div class="form-group-custom" id="app_password_wrapper" style="{{ Auth::check() ? 'display:none;' : '' }}">
                            <label class="form-label-custom" id="app_password_label">{{ __('messages.password') }} <span class="text-danger">*</span></label>
                            <div class="input-icon-wrap">
                                <input type="password" id="app_user_password" class="form-control" 
                                       placeholder="{{ $isArLocale ? 'أدخل كلمة المرور' : 'Enter password' }}" minlength="6">
                                <button type="button" class="toggle-password-btn" onclick="togglePasswordVisibility('app_user_password')">
                                    <i class="bi bi-eye-slash" id="app_user_password_eye"></i>
                                </button>
                                <i class="bi bi-lock field-icon"></i>
                            </div>
                            <div class="form-text text-muted small" id="app_password_hint">{{ __('messages.password_hint') }}</div>
                        </div>

                        {{-- شريط أمان البيانات المعتمد --}}
                        <div class="security-note-box">
                            <i class="bi bi-shield-check security-note-icon"></i>
                            <div class="security-note-text">
                                <strong>{{ $isArLocale ? 'معلوماتك آمنة ومحمية' : 'Your data is safe and encrypted' }}</strong>
                                <span>{{ $isArLocale ? 'نستخدم أحدث تقنيات التشفير لحماية بياناتك الشخصية وسرية الجلسة.' : 'We use modern end-to-end encryption to protect your privacy and consultation details.' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- 1.6 التاريخ والمواعيد المتاحة (Calendar & Time Slots) --}}
                    <div class="calendar-collapsible-box">
                        <div class="section-headline mb-2">
                            <i class="bi bi-calendar3 text-primary"></i>
                            <span>{{ __('messages.date_time_title') }}</span>
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

                        {{-- شبكة الأوقات المتاحة --}}
                        <div>
                            <label class="form-label-custom mb-1.5"><i class="bi bi-clock me-1 text-primary"></i> {{ __('messages.select_time') }}</label>
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
                     الخطوة الثانية (Step 2): الملخص وطرق الدفع والإيصال
                     ═══════════════════════════════════════════════════════════ --}}
                <div id="app-screen-2" class="d-none">
                    
                    {{-- بطاقة إجمالي الطلب --}}
                    <div class="order-summary-box">
                        <div class="order-summary-header">
                            <span class="order-summary-title">
                                <i class="bi bi-cart3 text-primary"></i>
                                <span>{{ __('messages.order_total') }}</span>
                            </span>
                            <span class="order-summary-price" id="app-screen2-total">
                                {{ $modalServices->first()->price ?? 50 }} {{ $currencySymbol }}
                            </span>
                        </div>
                        <div class="order-summary-features">
                            <span id="screen2_feature_type"><i class="bi bi-check-circle-fill text-success"></i> {{ $isArLocale ? 'استشارة أونلاين' : 'Online Consultation' }}</span>
                            <span id="screen2_feature_dur"><i class="bi bi-check-circle-fill text-success"></i> {{ $modalServices->first()->duration ?? 45 }} {{ __('messages.minutes') }}</span>
                            <span><i class="bi bi-check-circle-fill text-success"></i> {{ $isArLocale ? 'حجز فوري ومباشر' : 'Direct Booking' }}</span>
                        </div>
                    </div>

                    {{-- خيارات الدفع --}}
                    @if($anyPaymentActive)
                    <div class="mb-3">
                        <div class="section-headline mb-2">
                            <i class="bi bi-wallet2 text-primary"></i>
                            <span>{{ __('messages.payment_method') }}</span>
                        </div>
                        
                        {{-- Segmented Toggle Switcher --}}
                        <div class="pay-toggle-switcher mb-2" id="payment-method-tabs" role="tablist">
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
                                    <p class="pay-qr-label">{{ $payZainLabel }}</p>
                                @else
                                    <div class="py-2 text-center">
                                        <i class="bi bi-qr-code" style="font-size:2.4rem;color:#7c3aed;"></i>
                                        <p class="small mt-1 mb-0 fw-bold text-dark">{{ __('messages.pay_zaincash') }} QR</p>
                                        <p class="pay-qr-label">{{ $isArLocale ? 'افتح تطبيق زين كاش وامسح الرمز لإتمام الدفع، ثم أرسل لقطة شاشة الإيصال.' : 'Open ZainCash app, scan QR code, and upload the screenshot.' }}</p>
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
                                    <p class="pay-qr-label">{{ $paySuperkiLabel }}</p>
                                @else
                                    <div class="py-2 text-center">
                                        <i class="bi bi-qr-code" style="font-size:2.4rem;color:#0284c7;"></i>
                                        <p class="small mt-1 mb-0 fw-bold text-dark">SuperKi QR</p>
                                        <p class="pay-qr-label">{{ $isArLocale ? 'افتح تطبيق SuperKi وامسح الرمز لإتمام الدفع، ثم أرسل لقطة شاشة الإيصال.' : 'Open SuperKi app, scan QR code, and upload the screenshot.' }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        {{-- ─── بانل فيزا وماستر كارد ─── --}}
                        @if($payCardEnabled)
                        <div id="pay-panel-card" class="pay-panel {{ $defaultPayMethod !== 'card' ? 'd-none' : '' }}">
                            <div class="pay-qr-card">
                                <div class="d-flex align-items-center justify-content-between mb-2 border-bottom pb-2">
                                    <div class="fw-bold text-dark" style="font-size:0.85rem;">
                                        <i class="bi bi-shield-check text-success me-1"></i> {{ __('messages.pay_card') }}
                                    </div>
                                    <div class="d-flex align-items-center gap-1" dir="ltr">
                                        <span class="badge bg-white px-2 py-0.5 shadow-sm border text-primary fw-bold">VISA</span>
                                        <span class="badge bg-white px-2 py-0.5 shadow-sm border text-danger fw-bold">MasterCard</span>
                                    </div>
                                </div>
                                <p class="pay-qr-label text-secondary mb-2">{{ $payCardInstructions }}</p>
                                @if(!empty($payCardLink))
                                <a href="{{ $payCardLink }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill fw-bold w-100 py-1.5">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> {{ $isArLocale ? 'فتح رابط الدفع الإلكتروني المباشر' : 'Open Direct Payment Link' }}
                                </a>
                                @endif
                            </div>
                        </div>
                        @endif

                        {{-- ═══ كارت إثبات التحويل المالي وسكرين شوت الإيصال ═══ --}}
                        <div class="pay-proof-card">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="form-label fw-bold text-dark mb-0" style="font-size:0.82rem;">
                                    <i class="bi bi-phone-vibrate text-primary me-1"></i> {{ __('messages.transfer_number_label') }}
                                </label>
                                <span class="badge bg-light text-muted border" style="font-size: 0.68rem;">{{ __('messages.optional') }}</span>
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-hash"></i></span>
                                <input type="tel" id="app_transfer_number" class="form-control" placeholder="{{ __('messages.transfer_number_ph') }}">
                            </div>

                            {{-- Upload Screenshot Dropzone --}}
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="form-label fw-bold text-dark mb-0" style="font-size:0.82rem;">
                                    <i class="bi bi-image text-success me-1"></i> {{ __('messages.receipt_upload_label') }}
                                </label>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25" style="font-size: 0.68rem;">{{ __('messages.speeds_confirmation') }}</span>
                            </div>

                            <div id="receiptUploadBox" class="pay-upload-dropzone" onclick="document.getElementById('app_receipt_file').click()">
                                <input type="file" id="app_receipt_file" class="d-none" accept="image/*" onchange="onReceiptImageSelected(this)">
                                
                                {{-- Placeholder View --}}
                                <div id="receiptPlaceholderView" class="py-1">
                                    <i class="bi bi-cloud-arrow-up-fill text-primary fs-3 d-block mb-1"></i>
                                    <div class="fw-bold text-dark" style="font-size:0.82rem;">{{ __('messages.receipt_dropzone_title') }}</div>
                                    <div class="text-muted" style="font-size: 0.72rem;">
                                        {{ __('messages.receipt_dropzone_sub') }}
                                    </div>
                                </div>

                                {{-- Preview View --}}
                                <div id="receiptPreviewView" class="d-none align-items-center justify-content-between gap-2 text-start">
                                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                                        <img id="receiptPreviewImg" src="" alt="Receipt Preview" class="rounded-2 border" style="width: 44px; height: 44px; object-fit: cover;">
                                        <div class="overflow-hidden {{ $isArLocale ? 'text-end' : 'text-start' }}">
                                            <div class="fw-bold text-dark text-truncate" id="receiptFileName" style="font-size:0.8rem;">receipt.png</div>
                                            <div class="text-success" style="font-size: 0.72rem;"><i class="bi bi-check-circle-fill me-1"></i> {{ __('messages.receipt_attached_success') }}</div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-circle p-1" onclick="event.stopPropagation(); removeReceiptImage();" title="Remove" style="width:28px;height:28px; display:flex; align-items:center; justify-content:center;">
                                        <i class="bi bi-trash3-fill" style="font-size:0.8rem;"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Terms check --}}
                    <div class="form-check my-2">
                        <input class="form-check-input" type="checkbox" id="app_terms_check" checked style="cursor:pointer;">
                        <label class="form-check-label fw-bold text-secondary" for="app_terms_check" style="font-size:0.8rem; cursor:pointer;">
                            {{ __('messages.terms_agree') }}
                        </label>
                    </div>

                </div>{{-- End Screen 2 --}}

                {{-- ═══════════════════════════════════════════════════════════
                     الخطوة الثالثة (Screen 3): بطاقة التأكيد وتذكرة الموعد
                     ═══════════════════════════════════════════════════════════ --}}
                <div id="app-screen-3" class="d-none">
                    <div class="text-center py-1">
                        <div class="luxury-status-circle">
                            <i class="bi bi-check-lg"></i>
                        </div>
                        <h4 class="fw-black text-dark mb-1">{{ __('messages.booking_success_title') }}</h4>
                        <p class="text-muted small mb-3">{{ $isArLocale ? 'تم استلام طلبك بنجاح وهو قيد التأكيد والمتابعة' : 'Your request has been received and is being processed' }}</p>

                        {{-- بطاقة تذكرة الموعد الإلكترونية --}}
                        <div class="luxury-voucher-card">
                            <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                                <span class="fw-bold text-dark"><i class="bi bi-ticket-perforated-fill text-primary me-1"></i> {{ __('messages.e_ticket_title') }}</span>
                                <span class="badge bg-primary rounded-pill px-2.5 py-1" id="app-res-ref">#BK-REF</span>
                            </div>
                            <div class="voucher-row">
                                <span class="text-muted"><i class="bi bi-heart-pulse-fill text-primary me-1"></i> {{ __('messages.ticket_service') }}</span>
                                <span class="fw-bold text-dark" id="app-res-service">{{ $modalServices->first()->title ?? 'جلسة استشارة' }}</span>
                            </div>
                            <div class="voucher-row">
                                <span class="text-muted"><i class="bi bi-calendar-check-fill text-primary me-1"></i> {{ __('messages.ticket_datetime') }}</span>
                                <span class="fw-bold text-dark font-monospace" id="app-res-datetime">—</span>
                            </div>
                            <div class="voucher-row">
                                <span class="text-muted"><i class="bi bi-credit-card-2-front-fill text-primary me-1"></i> {{ __('messages.ticket_paymethod') }}</span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill fw-bold" id="app-res-paymethod">{{ __('messages.pay_zaincash') }}</span>
                            </div>
                            <div class="voucher-row pt-2">
                                <span class="fw-bold text-dark">{{ __('messages.ticket_amount') }}</span>
                                <span class="fw-black text-primary fs-5" id="app-res-type">50 {{ $currencySymbol }}</span>
                            </div>
                        </div>

                        {{-- أزرار الإجراءات --}}
                        <div class="d-flex flex-column gap-2 mt-3">
                            <a id="app-dashboard-link" href="{{ route('patient.dashboard') }}" class="btn-primary-flow w-100 max-w-100" style="max-width:100%;">
                                <i class="bi bi-speedometer2"></i>
                                <span>{{ __('messages.go_to_dashboard') }}</span>
                            </a>
                            <button type="button" class="btn btn-light rounded-3 py-2 fw-bold text-secondary" data-bs-dismiss="modal" onclick="window.location.reload()">
                                <span>{{ __('messages.close_and_return_home') }}</span>
                            </button>
                        </div>
                    </div>
                </div>{{-- End Screen 3 --}}

            </div>

            {{-- 4. Sticky Bottom Action Bar (Screen 1 & 2 Controls) --}}
            <div class="luxury-bottom-bar" id="app-bottom-bar">
                <div class="bottom-total-col">
                    <div class="bottom-total-amount" id="app-bottom-total">{{ $modalServices->first()->price ?? 50 }} {{ $currencySymbol }}</div>
                    <div class="bottom-total-sub">{{ __('messages.order_total') }}</div>
                </div>
                
                {{-- Button for Screen 1 --}}
                <button type="button" class="btn-primary-flow" id="btn-flow-next" onclick="goToAppScreen2()">
                    <span>{{ $isArLocale ? 'متابعة الدفع' : 'Proceed to Payment' }}</span>
                    <i class="bi {{ $isArLocale ? 'bi-arrow-left' : 'bi-arrow-right' }}"></i>
                </button>

                {{-- Button for Screen 2 --}}
                <button type="button" class="btn-primary-flow d-none" id="btn-flow-submit" onclick="executeAppBooking()">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ __('messages.confirm_and_send_receipt') }}</span>
                </button>
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
        'minutes' => __('messages.minutes'),
        'enter_subject' => __('messages.enter_subject_warning'),
        'enter_name' => __('messages.enter_name_warning'),
        'enter_phone' => __('messages.enter_phone_warning'),
        'select_slot' => __('messages.select_slot_warning'),
        'enter_password' => __('messages.enter_password_warning'),
        'terms_required' => __('messages.terms_required_warning'),
        'loading_slots' => __('messages.loading_slots'),
        'no_slots' => __('messages.no_slots_warning'),
        'failed_slots' => __('messages.failed_slots'),
        'existing_user' => __('messages.existing_user_badge'),
        'new_user' => __('messages.new_user_badge'),
        'submitting' => __('messages.submitting_booking'),
        'request_failed' => __('messages.request_failed'),
        'confirm_btn' => __('messages.confirm_and_send_receipt'),
        'pay_zain' => __('messages.pay_zaincash'),
        'pay_superki' => __('messages.pay_superki'),
        'pay_card' => __('messages.pay_card'),
        'past_date' => $isArLocale ? 'تاريخ سابق غير متاح' : 'Past date unavailable',
        'default_service' => $isArLocale ? 'جلسة استشارة' : 'Consultation Session',
        'is_ar' => $isArLocale,
        'slot_am' => $isArLocale ? 'ص' : 'AM',
        'slot_pm' => $isArLocale ? 'م' : 'PM',
    ];
@endphp

<script>
// ════ Shared Booking Modal JS Engine ════
const _i18n = {!! json_encode($modalI18n, JSON_UNESCAPED_UNICODE) !!};
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
    
    // Toggle active classes on cards
    const cardOnline = document.getElementById('typeOptionOnline');
    const cardClinic = document.getElementById('typeOptionClinic');
    const badgeOnline = document.getElementById('badgeSelectedOnline');
    const badgeClinic = document.getElementById('badgeSelectedClinic');
    
    if (cardOnline) cardOnline.classList.toggle('active', isOnline);
    if (cardClinic) cardClinic.classList.toggle('active', !isOnline);
    if (badgeOnline) badgeOnline.classList.toggle('d-none', !isOnline);
    if (badgeClinic) badgeClinic.classList.toggle('d-none', isOnline);
    
    // Filter services dropdown
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

    // Update strip duration
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

    // Refresh slots
    fetchModalSlots(appState.date);
}

function updateModalPrice(price) {
    appState.price = price;
    const pText = price + ' ' + appCurrencySymbol;
    if (document.getElementById('strip_session_price')) document.getElementById('strip_session_price').textContent = pText;
    if (document.getElementById('app-bottom-total')) document.getElementById('app-bottom-total').textContent = pText;
    if (document.getElementById('app-screen2-total')) document.getElementById('app-screen2-total').textContent = pText;
    if (document.getElementById('app-res-type')) document.getElementById('app-res-type').textContent = pText;
}

function setModalStep(step) {
    for (let i = 1; i <= 4; i++) {
        const item = document.getElementById(`modal-step-${i}`);
        if (!item) continue;
        item.classList.remove('active', 'completed');
        if (i < step) {
            item.classList.add('completed');
            item.querySelector('.step-circle').innerHTML = '<i class="bi bi-check-lg"></i>';
        } else if (i === step) {
            item.classList.add('active');
            item.querySelector('.step-circle').textContent = i;
        } else {
            item.querySelector('.step-circle').textContent = i;
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
                    if (passLabel) passLabel.innerHTML = '{{ __("messages.login_password") }} <span class="text-danger">*</span>';
                    if (passHint) passHint.textContent = '{{ __("messages.enter_existing_password") }}';
                } else {
                    appUserIsRegistered = false;
                    badge.className = 'badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 ms-auto';
                    badge.innerHTML = '<i class="bi bi-person-plus-fill me-1"></i> ' + _i18n.new_user;
                    if (passWrapper) passWrapper.style.display = 'block';
                    if (passLabel) passLabel.innerHTML = '{{ __("messages.create_password") }} <span class="text-danger">*</span>';
                    if (passHint) passHint.textContent = '{{ __("messages.password_hint") }}';
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
    
    // Update strip date label
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
            if (data.success && data.slots && data.slots.length > 0) {
                data.slots.forEach((slot, idx) => {
                    const pill = document.createElement('div');
                    pill.className = `app-slot-pill ${idx === 0 ? 'selected' : ''}`;
                    pill.textContent = slot;
                    pill.onclick = () => selectAppSlot(slot, pill);
                    grid.appendChild(pill);
                    if (idx === 0) appState.slot = slot;
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
    document.querySelectorAll('.app-slot-pill.selected').forEach(p => p.classList.remove('selected'));
    el.classList.add('selected');
    appState.slot = slot;
}

function goToAppScreen2() {
    const title = (document.getElementById('app_consultation_title')?.value || '').trim();
    const name = (document.getElementById('app_user_name')?.value || '').trim();
    const phone = (document.getElementById('app_user_phone')?.value || '').trim();

    if (!title) { alert(_i18n.enter_subject); return; }
    if (!name) { alert(_i18n.enter_name); return; }
    if (!phone) { alert(_i18n.enter_phone); return; }
    if (!appState.slot) { alert(_i18n.select_slot); return; }

    @if(!Auth::check())
    const password = document.getElementById('app_user_password')?.value || '';
    if (!password) { alert(_i18n.enter_password); return; }
    @endif

    savePatientBookingToStorage();

    document.getElementById('app-screen-1')?.classList.add('d-none');
    document.getElementById('app-screen-2')?.classList.remove('d-none');
    document.getElementById('btn-flow-next')?.classList.add('d-none');
    document.getElementById('btn-flow-submit')?.classList.remove('d-none');
    
    // Set Stepper to Step 3 (الدفع)
    setModalStep(3);

    const modalBody = document.querySelector('.luxury-modal-body');
    if (modalBody) modalBody.scrollTop = 0;
}

function goToAppScreen1() {
    document.getElementById('app-screen-1')?.classList.remove('d-none');
    document.getElementById('app-screen-2')?.classList.add('d-none');
    document.getElementById('btn-flow-next')?.classList.remove('d-none');
    document.getElementById('btn-flow-submit')?.classList.add('d-none');
    
    // Set Stepper back to Step 1/2
    setModalStep(1);

    const modalBody = document.querySelector('.luxury-modal-body');
    if (modalBody) modalBody.scrollTop = 0;
}

function switchPayTab(method) {
    appState.paymentMethod = method;
    document.querySelectorAll('.pay-toggle-btn').forEach(btn => btn.classList.remove('active', 'zain', 'superki', 'card'));
    document.querySelectorAll('.pay-panel').forEach(panel => panel.classList.add('d-none'));

    const tabBtn = document.getElementById('pay-tab-' + method);
    if (tabBtn) tabBtn.classList.add('active', method);

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

    const formData = new FormData();
    formData.append('service_id', appState.serviceId);
    formData.append('booking_type', appState.bookingType);
    formData.append('name', name);
    formData.append('phone', fullPhone);
    formData.append('email', email);
    formData.append('title', title);
    formData.append('date', appState.date);
    formData.append('slot', appState.slot);
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
        // Set Stepper to Step 4 (التأكيد)
        setModalStep(4);

        // Hide screen 1 & 2, show screen 3
        document.getElementById('app-screen-1')?.classList.add('d-none');
        document.getElementById('app-screen-2')?.classList.add('d-none');
        document.getElementById('app-screen-3')?.classList.remove('d-none');
        document.getElementById('app-bottom-bar')?.classList.add('d-none');

        const ref = resData.booking_ref || resData.reference || `BK-${Math.floor(100000 + Math.random() * 900000)}`;
        if (document.getElementById('app-res-ref')) document.getElementById('app-res-ref').textContent = '#' + ref;
        if (document.getElementById('app-res-service')) document.getElementById('app-res-service').textContent = appState.title;
        if (document.getElementById('app-res-datetime')) document.getElementById('app-res-datetime').textContent = `${appState.date} | ${appState.slot}`;
        
        let payLabel = _i18n.pay_zain;
        if (appState.paymentMethod === 'superki') payLabel = _i18n.pay_superki;
        if (appState.paymentMethod === 'card') payLabel = _i18n.pay_card;
        if (document.getElementById('app-res-paymethod')) document.getElementById('app-res-paymethod').textContent = payLabel;

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
