@extends('layouts.app')

@php
    $isAr = app()->getLocale() === 'ar';
@endphp

@section('title', 'المعالج النفسي يونس المرشد - حجز استشارات نفسية وأسرية متخصصة')
@section('meta_description', 'احجز استشارتك النفسية مع المعالج يونس المرشد - جلسات فردية وزوجية وأسرية. استشارات عبر شات أو صوت أو فيديو أو في العيادة. خبرة 10 سنوات في العلاج المعرفي السلوكي.')
@section('meta_keywords', 'معالج نفسي, استشارة نفسية, يونس المرشد, حجز موعد نفسي, علاج اكتئاب, علاج قلق, استشارة زوجية, علاج أسري')

@section('styles')
<style>
/* ═══ Hero Section ═══════════════════════════════════════════════ */
.hero-section {
    position: relative;
    min-height: 92vh;
    display: flex;
    align-items: center;
    overflow: hidden;
    background: linear-gradient(135deg, #1C2752 0%, #4055A5 55%, #5F7CD4 100%);
}
.hero-section::before {
    content: '';
    position: absolute; inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Ccircle cx='30' cy='30' r='30'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    pointer-events: none;
}
.hero-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.25;
    animation: floatOrb 8s ease-in-out infinite alternate;
}
.hero-orb-1 { width: 400px; height: 400px; background: #6D8FD6; top: -100px; right: -50px; }
.hero-orb-2 { width: 300px; height: 300px; background: #D4AF37; bottom: -80px; left: 10%; animation-delay: -3s; }
@keyframes floatOrb { from { transform: translateY(0) scale(1); } to { transform: translateY(-30px) scale(1.05); } }

.hero-badge {
    display: inline-flex; align-items: center; gap: 8px;
    background: rgba(255,255,255,0.12);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.25);
    color: #fff;
    padding: 0.5rem 1.2rem;
    border-radius: 50px;
    font-size: 0.9rem; font-weight: 600;
    margin-bottom: 1.5rem;
    animation: pulse-badge 2.5s ease-in-out infinite;
}
.hero-badge .dot { width: 8px; height: 8px; background: #4ade80; border-radius: 50%; animation: blink 1s ease-in-out infinite; }
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.3} }
@keyframes pulse-badge { 0%,100%{box-shadow:0 0 0 0 rgba(74,222,128,0.3)} 50%{box-shadow:0 0 0 8px rgba(74,222,128,0)} }

.hero-title { font-size: clamp(1.85rem, 4.5vw, 3.2rem); font-weight: 900; color: #fff; line-height: 1.25; }
.hero-title .highlight { color: #F7EFE0; }
.hero-subtitle { font-size: 1.05rem; color: rgba(255,255,255,0.88); max-width: 520px; line-height: 1.7; }

.hero-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.75rem;
    margin-top: 2rem;
}
@media (max-width: 768px) {
    .hero-stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.65rem;
    }
}
.hero-stat-card {
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.16);
    border-radius: 18px;
    padding: 0.85rem 0.6rem;
    text-align: center;
    transition: all 0.25s ease;
}
.hero-stat-card:hover {
    background: rgba(255, 255, 255, 0.14);
    transform: translateY(-2px);
}
.hero-stat-card .hero-stat-num {
    font-size: 1.65rem;
    font-weight: 900;
    color: #fff;
    line-height: 1.15;
    margin-bottom: 2px;
}
.hero-stat-card .hero-stat-label {
    font-size: 0.78rem;
    color: rgba(255, 255, 255, 0.75);
    font-weight: 600;
}

.btn-hero-secondary {
    background: rgba(255, 255, 255, 0.12);
    color: #fff !important;
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 50px;
    backdrop-filter: blur(8px);
    transition: all 0.25s ease;
}
.btn-hero-secondary:hover {
    background: rgba(255, 255, 255, 0.22);
    color: #fff !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.hero-photo-frame {
    position: relative;
    display: flex;
    justify-content: center;
    align-items: flex-end;
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
    border-radius: 0 !important;
    max-height: 560px;
}
.hero-photo-frame img {
    max-width: 100%;
    max-height: 520px;
    height: auto;
    object-fit: contain;
    filter: drop-shadow(0 20px 35px rgba(0, 0, 0, 0.25));
    transition: transform 0.3s ease;
}
@media (max-width: 768px) {
    .hero-photo-frame img {
        max-height: 380px;
    }
}
.hero-photo-frame img:hover {
    transform: scale(1.02);
}
.hero-photo-overlay {
    display: none !important;
}
.hero-photo-caption {
    display: none !important;
}

/* ═══ Section Headers ══════════════════════════════════════════ */
.section-label { display: inline-flex; align-items: center; gap: 0.5rem; color: var(--primary-color); font-weight: 700; font-size: 0.9rem; background: rgba(59,82,164,0.08); padding: 0.4rem 1rem; border-radius: 30px; margin-bottom: 0.8rem; }
.section-title { font-size: clamp(1.6rem, 3vw, 2.5rem); font-weight: 900; color: #1e293b; line-height: 1.3; }
.section-subtitle { color: #64748b; font-size: 1.1rem; max-width: 560px; margin: 0 auto; }

/* ═══ About Section ════════════════════════════════════════════ */
.about-wrapper { background: linear-gradient(to bottom, #f8fafc, #ffffff); padding: 5rem 0; }
.about-card {
    background: #fff; border-radius: 28px; padding: 2.5rem;
    box-shadow: 0 20px 50px rgba(59,82,164,0.08);
    border: 1px solid rgba(59,82,164,0.1);
}
.about-doctor-img {
    width: 120px; height: 120px; border-radius: 50%;
    object-fit: cover; object-position: top;
    border: 4px solid var(--primary-color);
    box-shadow: 0 8px 20px rgba(59,82,164,0.25);
}
.credentials-list { list-style: none; padding: 0; margin: 0; }
.credentials-list li { display: flex; align-items: flex-start; gap: 0.8rem; padding: 0.5rem 0; border-bottom: 1px solid #f1f5f9; }
.credentials-list li:last-child { border-bottom: none; }
.credentials-list li i { color: var(--primary-color); font-size: 1.1rem; margin-top: 2px; flex-shrink: 0; }

/* ═══ Services Cards ══════════════════════════════════════════ */
.services-wrapper { background: #f0f4fb; padding: 5rem 0; }
.service-card-new {
    background: #fff; border-radius: 24px; padding: 2rem;
    box-shadow: 0 8px 30px rgba(59,82,164,0.08);
    border: 2px solid transparent;
    transition: all 0.3s ease; cursor: pointer;
    position: relative; overflow: hidden;
    height: 100%;
}
.service-card-new::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px;
    background: linear-gradient(90deg, var(--primary-color), #7dd3fc);
    opacity: 0; transition: opacity 0.3s ease;
}
.service-card-new:hover { border-color: var(--primary-color); transform: translateY(-6px); box-shadow: 0 20px 50px rgba(59,82,164,0.15); }
.service-card-new:hover::before { opacity: 1; }
.service-card-new.popular { border-color: var(--primary-color); }
.service-card-new.popular::before { opacity: 1; }
.popular-badge { position: absolute; top: 1rem; left: 1rem; background: linear-gradient(135deg, var(--primary-color), #5b72c7); color: #fff; font-size: 0.75rem; font-weight: 700; padding: 0.25rem 0.7rem; border-radius: 30px; }

.channel-prices { display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-top: 1rem; }
.channel-price-item { display: flex; align-items: center; gap: 0.4rem; background: #f8fafc; padding: 0.4rem 0.6rem; border-radius: 8px; font-size: 0.82rem; }
.channel-price-item .price { font-weight: 800; color: var(--primary-color); margin-right: auto; }

/* ═══ Reels Section Dual Loop Marquee ═════════════════════════ */
.reels-wrapper { background: #0f172a; padding: 5rem 0; position: relative; overflow: hidden; }
.reels-wrapper::before { content: ''; position: absolute; inset: 0; background: radial-gradient(circle at 50% 50%, rgba(59,82,164,0.15) 0%, transparent 70%); pointer-events: none; }
.reels-swiper-row-1, .reels-swiper-row-2 { padding: 0.5rem 0 !important; overflow: hidden; }
.reels-swiper-row-1 .swiper-wrapper, .reels-swiper-row-2 .swiper-wrapper { transition-timing-function: linear !important; }
.reel-card-new {
    background: #1e293b; border-radius: 20px; overflow: hidden;
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease; cursor: pointer;
    aspect-ratio: 9/16; position: relative;
    border: 1px solid rgba(255,255,255,0.08);
    width: 220px; height: 350px;
}
.reel-card-new:hover { transform: translateY(-6px) scale(1.03); box-shadow: 0 16px 36px rgba(0,0,0,0.5); }
.reel-card-new img { width: 100%; height: 100%; object-fit: cover; opacity: 0.85; transition: opacity 0.3s ease; }
.reel-card-new:hover img { opacity: 1; }
.reel-card-new .reel-gradient { position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, transparent 55%); }
.reel-card-new .reel-platform { position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.65); backdrop-filter: blur(6px); color: #fff; padding: 0.25rem 0.7rem; border-radius: 30px; font-size: 0.78rem; font-weight: 700; z-index: 2; }
.reel-card-new .reel-play { position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); width: 48px; height: 48px; background: rgba(255,255,255,0.92); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; color: var(--primary-color); transition: all 0.3s ease; z-index: 2; }
.reel-card-new:hover .reel-play { background: var(--primary-color); color: #fff; transform: translate(-50%,-50%) scale(1.12); }
.reel-card-new .reel-info { position: absolute; bottom: 0; left: 0; right: 0; padding: 1rem; color: #fff; z-index: 2; }
.reel-card-new .reel-info .reel-title-text { font-size: 0.9rem; font-weight: 700; margin-bottom: 0.3rem; line-height: 1.35; }
.reel-card-new .reel-views { font-size: 0.75rem; color: rgba(255,255,255,0.7); display: flex; align-items: center; gap: 0.3rem; }

/* ═══ Testimonials ══════════════════════════════════════════ */
.testimonials-wrapper { background: #fff; padding: 5rem 0; }
.testimonial-card { background: #f8fafc; border-radius: 20px; padding: 2rem; border: 1px solid #e2e8f0; height: 100%; transition: box-shadow 0.3s; }
.testimonial-card:hover { box-shadow: 0 15px 40px rgba(59,82,164,0.1); }
.stars { color: #f59e0b; font-size: 1.1rem; }
.testimonial-avatar { width: 42px; height: 42px; background: linear-gradient(135deg, var(--primary-color), #5b72c7); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }

/* ═══ Booking Wizard ════════════════════════════════════════ */
.booking-wrapper { background: linear-gradient(135deg, #f0f4fb 0%, #e8edf8 100%); padding: 5rem 0; }
.wizard-card {
    background: #fff; border-radius: 32px;
    box-shadow: 0 25px 60px rgba(59,82,164,0.12);
    overflow: hidden; max-width: 800px; margin: 0 auto;
}
.wizard-header { background: linear-gradient(135deg, var(--primary-color), #2d5be3); padding: 2rem 2.5rem; color: #fff; }
.wizard-step-bar { display: flex; align-items: center; gap: 0; margin-top: 1.5rem; }
.wizard-step-item { display: flex; align-items: center; flex: 1; }
.wizard-step-circle { width: 36px; height: 36px; border-radius: 50%; background: rgba(255,255,255,0.2); border: 2px solid rgba(255,255,255,0.4); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.9rem; flex-shrink: 0; transition: all 0.4s ease; }
.wizard-step-circle.active { background: #fff; color: var(--primary-color); border-color: #fff; }
.wizard-step-circle.done { background: #4ade80; border-color: #4ade80; }
.wizard-step-label { font-size: 0.8rem; color: rgba(255,255,255,0.8); margin-right: 0.5rem; }
.wizard-step-label.active { color: #fff; font-weight: 700; }
.wizard-step-line { flex: 1; height: 2px; background: rgba(255,255,255,0.3); margin: 0 0.5rem; }
.wizard-step-line.done { background: #4ade80; }

.wizard-body { padding: 2.5rem; }

/* Channel Select */
.channel-select-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; }
.channel-option { border: 2px solid #e2e8f0; border-radius: 14px; padding: 1rem; cursor: pointer; transition: all 0.2s ease; text-align: center; }
.channel-option:hover { border-color: var(--primary-color); background: #f0f4fb; }
.channel-option.selected { border-color: var(--primary-color); background: linear-gradient(135deg, rgba(59,82,164,0.08), rgba(59,82,164,0.04)); }
.channel-option .channel-icon { font-size: 2rem; margin-bottom: 0.4rem; }
.channel-option .channel-name { font-weight: 700; font-size: 0.95rem; color: #1e293b; }
.channel-option .channel-price { font-size: 0.85rem; color: var(--primary-color); font-weight: 700; margin-top: 0.2rem; }

/* Service Buttons */
.service-btns { display: flex; gap: 0.6rem; flex-wrap: wrap; }
.service-btn { border: 2px solid #e2e8f0; border-radius: 12px; padding: 0.6rem 1rem; cursor: pointer; font-weight: 700; font-size: 0.9rem; background: #fff; color: #475569; transition: all 0.2s ease; }
.service-btn.selected { border-color: var(--primary-color); background: var(--primary-color); color: #fff; }

/* Slots */
.slots-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.6rem; }
@media(max-width: 480px) { .slots-grid { grid-template-columns: repeat(2, 1fr); } }
.slot-item { border: 2px solid #e2e8f0; border-radius: 10px; padding: 0.6rem; text-align: center; font-weight: 700; font-size: 0.9rem; cursor: pointer; transition: all 0.2s ease; background: #fff; }
.slot-item:hover { border-color: var(--primary-color); color: var(--primary-color); }
.slot-item.selected { background: var(--primary-color); border-color: var(--primary-color); color: #fff; }

/* Booking Summary */
.summary-card { background: #f0f4fb; border-radius: 16px; padding: 1.5rem; border: 1px solid rgba(59,82,164,0.1); }
.summary-row { display: flex; justify-content: space-between; align-items: center; padding: 0.6rem 0; border-bottom: 1px solid rgba(59,82,164,0.08); }
.summary-row:last-child { border-bottom: none; }
.summary-total { font-size: 1.5rem; font-weight: 900; color: var(--primary-color); }

/* Payment Method */
.payment-method { border: 2px solid #e2e8f0; border-radius: 14px; padding: 1rem 1.2rem; cursor: pointer; display: flex; align-items: center; gap: 0.8rem; transition: all 0.2s ease; margin-bottom: 0.5rem; }
.payment-method:hover, .payment-method.selected { border-color: var(--primary-color); background: rgba(59,82,164,0.04); }

/* Success */
.success-circle { width: 90px; height: 90px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 2.5rem; margin: 0 auto 1.5rem; animation: pop-in 0.6s cubic-bezier(0.175,0.885,0.32,1.275); }
@keyframes pop-in { from{transform:scale(0);opacity:0} to{transform:scale(1);opacity:1} }

.whatsapp-btn-cta { background: linear-gradient(135deg, #25d366, #128c7e); color: #fff !important; border: none; border-radius: 50px; padding: 1rem 2rem; font-weight: 700; font-size: 1.1rem; display: inline-flex; align-items: center; gap: 0.6rem; text-decoration: none; transition: all 0.3s ease; box-shadow: 0 8px 25px rgba(37,211,102,0.35); }
.whatsapp-btn-cta:hover { transform: translateY(-3px); box-shadow: 0 12px 35px rgba(37,211,102,0.45); }
</style>
@endsection

@section('content')

{{-- ═══════════════════════════════════════════════════════════
     1. HERO SECTION
═══════════════════════════════════════════════════════════ --}}
<section class="hero-section">
    <div class="hero-orb hero-orb-1"></div>
    <div class="hero-orb hero-orb-2"></div>

    <div class="container py-4 py-lg-5">
        <div class="row align-items-center gy-4 gy-lg-5">
            {{-- Text Column --}}
            <div class="col-lg-7 text-center text-lg-start">
                <div class="hero-badge mb-3">
                    <span class="dot"></span>
                    <span>{{ __('messages.hero_badge') }}</span>
                </div>

                <h1 class="hero-title mb-3">
                    غيّر طريقة تفكيرك<br>
                    <span class="highlight">مع المعالج يونس المرشد</span>
                </h1>

                <p class="hero-subtitle mb-4 mx-auto mx-lg-0">
                    استشارات نفسية متخصصة بخبرة أكثر من 10 سنوات. جلسات فردية وزوجية وأسرية عبر الأونلاين أو في العيادة.
                </p>

                <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-3 mb-4">
                    <button type="button" class="btn btn-royal-primary py-3 px-4 fw-bold shadow-lg" data-bs-toggle="modal" data-bs-target="#bookingModal">
                        <i class="bi bi-calendar-check-fill me-2"></i> احجز استشارتك الآن
                    </button>
                    <a href="#about" class="btn btn-hero-secondary py-3 px-4 fw-bold">
                        <i class="bi bi-person-fill me-2"></i> تعرف على يونس
                    </a>
                </div>

                <div class="hero-stats-grid">
                    <div class="hero-stat-card">
                        <div class="hero-stat-num">+1200</div>
                        <div class="hero-stat-label">جلسة ناجحة</div>
                    </div>
                    <div class="hero-stat-card">
                        <div class="hero-stat-num">+10</div>
                        <div class="hero-stat-label">سنوات خبرة</div>
                    </div>
                    <div class="hero-stat-card">
                        <div class="hero-stat-num">98%</div>
                        <div class="hero-stat-label">رضا المراجعين</div>
                    </div>
                    <div class="hero-stat-card">
                        <div class="hero-stat-num">4</div>
                        <div class="hero-stat-label">قنوات تواصل</div>
                    </div>
                </div>
            </div>

            {{-- Photo Column --}}
            <div class="col-lg-5 text-center position-relative">
                <div class="hero-photo-frame">
                    @if($profile && !empty($profile->hero_image))
                        <img src="{{ $profile->hero_image }}" alt="{{ $doctorName }}" loading="eager">
                    @elseif($profile && $profile->gallery && count($profile->gallery) > 0)
                        <img src="{{ $profile->gallery[0] }}" alt="{{ $doctorName }}" loading="eager">
                    @else
                        <img src="https://images.unsplash.com/photo-1614797136987-ab4b98843e29?auto=format&fit=crop&w=700&q=80" alt="{{ $doctorName }}" loading="eager">
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     2. ABOUT SECTION
═══════════════════════════════════════════════════════════ --}}
<section id="about" class="about-wrapper reveal-on-scroll">
    <div class="container">

        <div class="about-card">
            <div class="row gy-4 align-items-start">
                <div class="col-lg-4 text-center">
                    @if($profile && !empty($profile->about_image))
                        <img src="{{ $profile->about_image }}" alt="{{ $doctorName }}" class="about-doctor-img mb-3">
                    @elseif($profile && !empty($profile->hero_image))
                        <img src="{{ $profile->hero_image }}" alt="{{ $doctorName }}" class="about-doctor-img mb-3">
                    @elseif($profile && $profile->gallery && count($profile->gallery) > 0)
                        <img src="{{ $profile->gallery[0] }}" alt="{{ $doctorName }}" class="about-doctor-img mb-3">
                    @else
                        <div class="about-doctor-img d-flex align-items-center justify-content-center mx-auto mb-3" style="background: linear-gradient(135deg, var(--primary-color), #5b72c7); color:#fff; font-size:3rem;">Ψ</div>
                    @endif
                    <h4 class="fw-bold text-dark mb-1">{{ $doctorName }}</h4>
                    <p class="text-secondary small">{{ $isAr ? ($profile->title ?? 'معالج نفسي ومدرب معتمد') : ($profile->title_en ?: ($profile->title ?? 'Licensed Psychological Therapist')) }}</p>
                    <ul class="credentials-list text-start mt-3">
                        @if($profile && $profile->education)
                            @foreach(array_slice($profile->education, 0, 3) as $edu)
                                <li><i class="bi bi-mortarboard-fill"></i><span class="small">{{ $edu }}</span></li>
                            @endforeach
                        @else
                            <li><i class="bi bi-mortarboard-fill"></i><span class="small">{{ $isAr ? 'بكالوريوس علم النفس الإكلينيكي' : 'B.Sc. Clinical Psychology' }}</span></li>
                            <li><i class="bi bi-award-fill"></i><span class="small">{{ $isAr ? 'شهادة العلاج المعرفي السلوكي CBT' : 'CBT Certified Therapist' }}</span></li>
                            <li><i class="bi bi-patch-check-fill"></i><span class="small">{{ $isAr ? 'معتمد في العلاج الأسري والزوجي' : 'Certified Family & Couples Therapist' }}</span></li>
                        @endif
                    </ul>
                </div>

                <div class="col-lg-8">
                    <p class="fs-5 text-secondary lh-lg mb-4">
                        {{ $isAr ? ($profile->bio ?? 'معالج نفسي مرخص بخبرة تزيد عن 10 سنوات في تقديم الاستشارات النفسية الفردية والزوجية والأسرية.') : ($profile->bio_en ?: ($profile->bio ?? 'Licensed psychological therapist with over 10 years of experience in providing individual and family counseling.')) }}
                    </p>

                    <h5 class="fw-bold mb-3" style="color: var(--primary-color);">{{ $isAr ? 'مجالات التخصص:' : 'Specialties & Focus Areas:' }}</h5>
                    <div class="d-flex flex-wrap gap-2">
                        @php
                            $defaultSpecs = $isAr ? ['اضطرابات القلق والتوتر', 'الاكتئاب وضغوط الحياة', 'الاستشارات الزوجية', 'العلاج الأسري', 'نقص الانتباه ADHD', 'الصدمات النفسية', 'الإدمان', 'التطوير الذاتي'] : ['Anxiety & Stress', 'Depression & Pressure', 'Couples Counseling', 'Family Therapy', 'ADHD', 'Psychological Trauma', 'Addiction Recovery', 'Self Development'];
                            $specialties = $isAr ? ($profile->specialties ?? $defaultSpecs) : ($profile->specialties_en ?: ($profile->specialties ?? $defaultSpecs));
                        @endphp
                        @foreach($specialties as $spec)
                            <div class="tag-pill"><i class="bi bi-check-circle-fill" style="color: var(--primary-color);"></i> {{ $spec }}</div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     2.5. GALLERY COLLAGE SECTION
═══════════════════════════════════════════════════════════ --}}
<section id="gallery" class="gallery-wrapper py-5 reveal-on-scroll">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title text-dark">{{ __('messages.gallery_title') }}</h2>
            <p class="section-subtitle">{{ __('messages.gallery_subtitle') }}</p>
        </div>

        @php
            $galleryImages = ($profile && !empty($profile->gallery) && count($profile->gallery) > 0)
                ? $profile->gallery
                : [
                    'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=1000&q=80',
                    'https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=600&q=80',
                    'https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?auto=format&fit=crop&w=600&q=80',
                    'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=600&q=80',
                    'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=600&q=80',
                    'https://images.unsplash.com/photo-1551836022-d5d88e9218df?auto=format&fit=crop&w=600&q=80',
                ];
            $totalGalleryCount = count($galleryImages);
            $remainingGalleryCount = max(0, $totalGalleryCount - 5);
        @endphp

        <div class="gallery-grid-collage">
            <!-- Large Image 1 -->
            <div class="gallery-item item-large" onclick="openLightbox(0)">
                <img src="{{ $galleryImages[0] }}" alt="Gallery Image 1" loading="lazy">
                <div class="gallery-overlay"><i class="bi bi-arrows-angle-expand"></i></div>
            </div>

            <!-- Stacked Images Col 1 -->
            <div class="gallery-col-stacked">
                @if(isset($galleryImages[1]))
                    <div class="gallery-item" onclick="openLightbox(1)">
                        <img src="{{ $galleryImages[1] }}" alt="Gallery Image 2" loading="lazy">
                        <div class="gallery-overlay"><i class="bi bi-arrows-angle-expand"></i></div>
                    </div>
                @endif
                @if(isset($galleryImages[2]))
                    <div class="gallery-item" onclick="openLightbox(2)">
                        <img src="{{ $galleryImages[2] }}" alt="Gallery Image 3" loading="lazy">
                        <div class="gallery-overlay"><i class="bi bi-arrows-angle-expand"></i></div>
                    </div>
                @endif
            </div>

            <!-- Stacked Images Col 2 with +X -->
            <div class="gallery-col-stacked">
                @if(isset($galleryImages[3]))
                    <div class="gallery-item" onclick="openLightbox(3)">
                        <img src="{{ $galleryImages[3] }}" alt="Gallery Image 4" loading="lazy">
                        <div class="gallery-overlay"><i class="bi bi-arrows-angle-expand"></i></div>
                    </div>
                @endif
                @if(isset($galleryImages[4]))
                    <div class="gallery-item item-more" onclick="openLightbox(4)">
                        <img src="{{ $galleryImages[4] }}" alt="Gallery Image 5" loading="lazy">
                        <div class="more-overlay">
                            <span class="more-count">+{{ $remainingGalleryCount > 0 ? $remainingGalleryCount : $totalGalleryCount }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Fullscreen Lightbox Modal -->
<div id="galleryLightboxModal" class="lightbox-modal" tabindex="-1">
    <div class="lightbox-backdrop" onclick="closeLightbox()"></div>
    <div class="lightbox-content">
        <button type="button" class="btn-lightbox-close" onclick="closeLightbox()">&times;</button>
        <div class="lightbox-counter"><span id="lightboxCurrentIndex">1</span> / <span id="lightboxTotalIndex">1</span></div>
        <button type="button" class="btn-lightbox-arrow btn-prev" onclick="navigateLightbox(-1)"><i class="bi bi-chevron-right"></i></button>
        <div class="lightbox-image-container">
            <img id="lightboxActiveImg" src="" alt="Gallery Preview">
        </div>
        <button type="button" class="btn-lightbox-arrow btn-next" onclick="navigateLightbox(1)"><i class="bi bi-chevron-left"></i></button>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     3. SERVICES SECTION WITH DEDICATED ONLINE & CLINIC CATEGORIES
═══════════════════════════════════════════════════════════ --}}
<section id="services" class="services-wrapper reveal-on-scroll">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="section-title">اختر نوع الاستشارة المناسبة</h2>
            <p class="section-subtitle">جلسات واستشارات متخصصة تضمن لك أقصى درجات الراحة والسرية التامة</p>
        </div>

        {{-- ── Category Tabs: Online vs Clinic ─────────────────── --}}
        <div class="d-flex justify-content-center mb-5">
            <div class="booking-type-toggle-container">
                <button type="button" class="booking-type-toggle-btn active" id="btnCategoryOnline" onclick="showServiceCategory('online')">
                   {{ $isAr ? 'الاستشارات الأونلاين (عن بُعد)' : 'Online Consultations' }}
                    <span class="badge badge-default-tag bg-primary text-white ms-2" style="font-size: 0.72rem; padding: 0.25rem 0.6rem; border-radius: 50px;"></span>
                </button>
                <button type="button" class="booking-type-toggle-btn" id="btnCategoryClinic" onclick="showServiceCategory('clinic')">
                   {{ $isAr ? 'حجوزات العيادة (حضورياً)' : 'In-Clinic Appointments' }}
                </button>
            </div>
        </div>

        {{-- ═════════════════════════════════════════════════════════
             CATEGORY 1: ONLINE SERVICES (RESPONSIVE SLIDER)
        ═════════════════════════════════════════════════════════ --}}
        <div id="onlineCategoryGrid" class="services-swiper-container">
            <div class="swiper services-swiper-online">
                <div class="swiper-wrapper">
                    @php
                        $onlineServices = $services->filter(fn($s) => in_array($s->type, ['online', 'both']));
                    @endphp
                    @foreach($onlineServices as $index => $service)
                        <div class="swiper-slide h-auto">
                            <div class="service-card-new h-100 d-flex flex-column justify-content-between {{ $index === 1 ? 'popular' : '' }}"
                                 onclick="selectServiceAndOpenModal({{ $service->id }}, '{{ $service->title }}', {{ $service->video_price ?? $service->price }}, {{ $service->duration }}, 'online')">
                                
                                @if($index === 1)
                                    <div class="luxury-popular-tag">
                                        <i class="bi bi-stars text-warning me-1"></i> {{ $isAr ? 'الأكثر طلباً واختياراً' : 'Most Popular' }}
                                    </div>
                                @endif

                                <div>
                                    {{-- Header: Icon + Badges --}}
                                    <div class="d-flex justify-content-between align-items-start mb-2 pt-2">
                                        <div class="pricing-icon-bubble">
                                            @if($index == 0) <i class="bi bi-camera-video-fill"></i> @elseif($index == 1) <i class="bi bi-chat-dots-fill"></i> @else <i class="bi bi-telephone-fill"></i> @endif
                                        </div>
                                        <div class="d-flex flex-column align-items-end gap-1">
                                            <span class="badge bg-light text-secondary border rounded-pill px-2.5 py-1" style="font-size: 0.74rem;">
                                                <i class="bi bi-clock me-1 text-primary"></i> {{ $service->duration }} دقيقة
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Title & Description --}}
                                    <h4 class="fw-black text-dark mb-2" style="font-size: 1.25rem; line-height: 1.4;">{{ $service->title }}</h4>
                                    <p class="text-secondary small mb-3" style="line-height: 1.6; min-height: 48px;">{{ $service->description }}</p>

                                    {{-- Main Price Box --}}
                                    <div class="pricing-amount-box">
                                        <div>
                                            <span class="text-secondary small fw-bold d-block mb-1">الرسوم تبدأ من</span>
                                            <div class="pricing-main-price">
                                                ${{ number_format($service->chat_price ?? $service->price, 0) }}
                                                <small>/ للجلسة</small>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 small fw-bold">
                                                <i class="bi bi-shield-check me-1"></i> سرية تامة 100%
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Features Included List --}}
                                    <ul class="pricing-features-list">
                                        <li>
                                            <i class="bi bi-check2-circle text-success me-2"></i>
                                            <span>جلسة خاصة وآمنة عبر المنصة</span>
                                        </li>
                                        <li>
                                            <i class="bi bi-check2-circle text-success me-2"></i>
                                            <span>تقييم سريري وإرشادي للحالة</span>
                                        </li>
                                        <li>
                                            <i class="bi bi-check2-circle text-success me-2"></i>
                                            <span>خطة علاجية معرفية سلوكية مخصصة</span>
                                        </li>
                                    </ul>
                                </div>

                                {{-- Action CTA Button --}}
                                <div class="mt-2">
                                    <button type="button" class="btn {{ $index === 1 ? 'btn-royal-primary' : 'btn-outline-primary' }} w-100 py-3 rounded-pill fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#bookingModal">
                                        <span>احجز استشارتك أونلاين الآن</span>
                                        <i class="bi bi-arrow-left fs-6"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="swiper-pagination services-pagination-online mt-4"></div>
            </div>
        </div>

        {{-- ═════════════════════════════════════════════════════════
             CATEGORY 2: CLINIC SERVICES (RESPONSIVE SLIDER)
        ═════════════════════════════════════════════════════════ --}}
        <div id="clinicCategoryGrid" class="services-swiper-container d-none">
            <div class="swiper services-swiper-clinic">
                <div class="swiper-wrapper">
                    @php
                        $clinicServices = $services->filter(fn($s) => in_array($s->type, ['clinic', 'both']));
                    @endphp
                    @foreach($clinicServices as $index => $service)
                        <div class="swiper-slide h-auto">
                            <div class="service-card-new clinic-card-luxury h-100 d-flex flex-column justify-content-between {{ $index === 0 ? 'popular' : '' }}"
                                 onclick="selectServiceAndOpenModal({{ $service->id }}, '{{ $service->title }}', {{ $service->clinic_price ?? $service->price }}, {{ $service->duration }}, 'clinic')">
                                
                                <div>
                                    {{-- Header: Icon + Badges --}}
                                    <div class="d-flex justify-content-between align-items-start mb-2 pt-2">
                                        <div class="pricing-icon-bubble">
                                            <i class="bi bi-hospital-fill"></i>
                                        </div>
                                        <div class="d-flex flex-column align-items-end gap-1">
                                            <span class="badge bg-danger bg-opacity-10 text-danger fw-bold rounded-pill px-3 py-1.5" style="font-size: 0.78rem;">
                                                <i class="bi bi-geo-alt-fill me-1"></i> كشف وحضور بالعيادة
                                            </span>
                                            <span class="badge bg-light text-secondary border rounded-pill px-2.5 py-1" style="font-size: 0.74rem;">
                                                <i class="bi bi-clock me-1 text-danger"></i> {{ $service->duration }} دقيقة
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Title & Description --}}
                                    <h4 class="fw-black text-dark mb-2" style="font-size: 1.25rem; line-height: 1.4;">{{ $service->title }}</h4>
                                    <p class="text-secondary small mb-3" style="line-height: 1.6; min-height: 48px;">{{ $service->description }}</p>

                                    {{-- Main Price Box --}}
                                    <div class="pricing-amount-box">
                                        <div>
                                            <span class="text-secondary small fw-bold d-block mb-1">رسوم الكشف السريري</span>
                                            <div class="pricing-main-price" style="color: #881337;">
                                                ${{ number_format($service->clinic_price ?? $service->price, 0) }}
                                                <small>/ للجلسة</small>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 small fw-bold">
                                                <i class="bi bi-patch-check-fill me-1"></i> فحص مباشر
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Location Pill --}}
                                    <div class="clinic-location-pill">
                                        <i class="bi bi-geo-alt-fill text-danger fs-5 flex-shrink-0"></i>
                                        <div>
                                            <div class="fw-bold">مقر عيادة د. يونس المرشد - بغداد</div>
                                            <div class="text-secondary small fw-normal">جلسة تشخيص وكشف سريري متكامل في بيئة مريحة</div>
                                        </div>
                                    </div>

                                    {{-- Features Included List --}}
                                    <ul class="pricing-features-list">
                                        <li>
                                            <i class="bi bi-check2-circle text-danger me-2"></i>
                                            <span>جلسة كشف وتشخيص سريري مباشر مع المعالج</span>
                                        </li>
                                        <li>
                                            <i class="bi bi-check2-circle text-danger me-2"></i>
                                            <span>وضع الخطة العلاجية السلوكية والدوائية</span>
                                        </li>
                                        <li>
                                            <i class="bi bi-check2-circle text-danger me-2"></i>
                                            <span>خصوصية تامة وغرفة استشارة هادئة ومجهزة</span>
                                        </li>
                                    </ul>
                                </div>

                                {{-- Action CTA Button --}}
                                <div class="mt-2">
                                    <button type="button" class="btn btn-outline-danger w-100 py-3 rounded-pill fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#bookingModal">
                                        <span>احجز موعدك بالعيادة الآن</span>
                                        <i class="bi bi-arrow-left fs-6"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="swiper-pagination services-pagination-clinic mt-4"></div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     4. REELS SECTION
═══════════════════════════════════════════════════════════ --}}
<section id="reels-section" class="reels-wrapper reveal-on-scroll">
    <div class="container position-relative">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="section-title text-white">تابعنا عبر منصات التواصل</h2>
                <p class="mb-0" style="color:rgba(255,255,255,0.65);">إرشادات نفسية ومقاطع توعوية قصيرة من أستاذ يونس</p>
            </div>
            <a href="#" target="_blank" class="btn btn-outline-light btn-sm rounded-pill px-4 d-none d-md-inline-flex align-items-center gap-2">
                <i class="bi bi-tiktok"></i> متابعة TikTok
            </a>
        </div>

        @php
            $reelsList = ($reels && $reels->count() > 0) ? $reels : collect([
                (object)['title' => 'الكآبة وعلاجها المعرفي السلوكي', 'video_url' => 'https://youtube.com', 'platform' => 'youtube', 'thumbnail_url' => 'https://images.unsplash.com/photo-1544027893741-31f9c07a7688?auto=format&fit=crop&w=400&q=70&h=700'],
                (object)['title' => 'كيف تتعامل مع القلق ونوبات الهلع', 'video_url' => 'https://youtube.com', 'platform' => 'tiktok', 'thumbnail_url' => 'https://images.unsplash.com/photo-1509842477838-1af3e1e24cf0?auto=format&fit=crop&w=400&q=70&h=700'],
                (object)['title' => 'أسرار التفاهم في العلاقات الزوجية', 'video_url' => 'https://youtube.com', 'platform' => 'youtube', 'thumbnail_url' => 'https://images.unsplash.com/photo-1591779051696-1f3f19ee63a4?auto=format&fit=crop&w=400&q=70&h=700'],
                (object)['title' => 'إدارة ضغوط العمل والتفكير الزائد', 'video_url' => 'https://youtube.com', 'platform' => 'tiktok', 'thumbnail_url' => 'https://images.unsplash.com/photo-1552581234-26160f608093?auto=format&fit=crop&w=400&q=70&h=700'],
            ]);
            // Replicate items to ensure endless smooth marquee loops
            $row1Reels = $reelsList->concat($reelsList)->concat($reelsList);
            $row2Reels = $reelsList->reverse()->concat($reelsList->reverse())->concat($reelsList->reverse());
        @endphp

        {{-- ── ROW 1: Forward Marquee Swiper ───────────────────── --}}
        <div class="swiper reels-swiper-row-1 mb-3">
            <div class="swiper-wrapper">
                @foreach($row1Reels as $reel)
                    <div class="swiper-slide" style="width: 220px;">
                        <div class="reel-card-new" onclick="openReelModal('{{ $reel->title }}', '{{ $reel->video_url }}', '{{ $reel->platform }}')">
                            <img src="{{ $reel->thumbnail_url }}" alt="{{ $reel->title }}" loading="lazy">
                            <div class="reel-gradient"></div>
                            <div class="reel-platform">
                                <i class="bi bi-{{ $reel->platform === 'tiktok' ? 'tiktok' : 'youtube' }} me-1"></i>
                                {{ ucfirst($reel->platform) }}
                            </div>
                            <div class="reel-play">
                                <i class="bi bi-play-fill"></i>
                            </div>
                            <div class="reel-info">
                                <div class="reel-title-text">{{ $reel->title }}</div>
                                <div class="reel-views">
                                    <i class="bi bi-eye-fill"></i>
                                    {{ number_format(rand(5000, 80000)) }} مشاهدة
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ── ROW 2: Reverse Direction Marquee Swiper ─────────── --}}
        <div class="swiper reels-swiper-row-2" dir="ltr">
            <div class="swiper-wrapper">
                @foreach($row2Reels as $reel)
                    <div class="swiper-slide" style="width: 220px;">
                        <div class="reel-card-new" onclick="openReelModal('{{ $reel->title }}', '{{ $reel->video_url }}', '{{ $reel->platform }}')">
                            <img src="{{ $reel->thumbnail_url }}" alt="{{ $reel->title }}" loading="lazy">
                            <div class="reel-gradient"></div>
                            <div class="reel-platform">
                                <i class="bi bi-{{ $reel->platform === 'tiktok' ? 'tiktok' : 'youtube' }} me-1"></i>
                                {{ ucfirst($reel->platform) }}
                            </div>
                            <div class="reel-play">
                                <i class="bi bi-play-fill"></i>
                            </div>
                            <div class="reel-info">
                                <div class="reel-title-text">{{ $reel->title }}</div>
                                <div class="reel-views">
                                    <i class="bi bi-eye-fill"></i>
                                    {{ number_format(rand(5000, 80000)) }} مشاهدة
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     5. TESTIMONIALS SECTION (SWIPER SLIDER)
═══════════════════════════════════════════════════════════ --}}
<section class="testimonials-wrapper py-5 reveal-on-scroll">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">{{ __('messages.testimonials_subtitle') }}</h2>
        </div>

        <div class="testimonials-swiper-container">
            <div class="swiper testimonials-swiper">
                <div class="swiper-wrapper">
                    @if(isset($testimonials) && count($testimonials) > 0)
                        @foreach($testimonials as $t)
                            <div class="swiper-slide h-auto">
                                <div class="testimonial-card h-100 d-flex flex-column">
                                    <div class="stars mb-3">
                                        @for($i = 0; $i < ($t->rating ?? 5); $i++)
                                            <i class="bi bi-star-fill text-warning me-1"></i>
                                        @endfor
                                    </div>
                                    <p class="text-secondary lh-lg mb-4 flex-grow-1">"{{ $isAr ? $t->content_ar : ($t->content_en ?: $t->content_ar) }}"</p>
                                    <div class="d-flex align-items-center gap-3 mt-auto">
                                        @if(!empty($t->client_avatar))
                                            <img src="{{ $t->client_avatar }}" alt="Avatar" class="rounded-circle" style="width:48px; height:48px; object-fit:cover;">
                                        @else
                                            <div class="testimonial-avatar"><i class="bi bi-person-heart"></i></div>
                                        @endif
                                        <div>
                                            <div class="fw-bold text-dark">{{ $isAr ? $t->client_name_ar : ($t->client_name_en ?: $t->client_name_ar) }}</div>
                                            <div class="text-secondary small">{{ $isAr ? 'عميل مؤكد' : 'Verified Client' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        {{-- Default Testimonials --}}
                        @php
                            $defaultTestimonials = [
                                ['text' => $isAr ? 'خدمة متميزة جداً، بعد الجلسة الثانية مع أستاذ يونس شعرت بفرق كبير واختفت نوبات التوتر والقلق تماماً.' : 'Excellent service! After the second session with Mr. Yonis, I felt a huge difference and anxiety attacks disappeared.', 'name' => $isAr ? 'مراجع من الرياض' : 'Client from Riyadh', 'session' => $isAr ? 'جلسة أونلاين فيديو' : 'Online Video Session', 'icon' => 'bi-person-heart'],
                                ['text' => $isAr ? 'شكراً جزيلاً دكتور يونس، التعامل أحدث تحولاً كبيراً في علاقتي الزوجية. المعالجة احترافية جداً وبيئة آمنة.' : 'Thank you Dr. Yonis! Very professional therapy in a safe environment without judgment.', 'name' => $isAr ? 'مراجعة من جدة' : 'Client from Jeddah', 'session' => $isAr ? 'استشارة زوجية' : 'Couples Therapy', 'icon' => 'bi-people-fill'],
                                ['text' => $isAr ? 'أفضل تجربة علاج نفسي مررت بها. الجلسات عبر الشات مريحة جداً وتناسب وقتي. أنصح كل شخص يعاني بالحجز.' : 'Best psychological therapy experience ever. Chat sessions are super convenient and flexible.', 'name' => $isAr ? 'مراجع من دبي' : 'Client from Dubai', 'session' => $isAr ? 'جلسة شات 30 دقيقة' : '30 Min Chat Session', 'icon' => 'bi-briefcase-fill'],
                                ['text' => $isAr ? 'أسلوب راقي جداً واحتواء نفسي عالي، ساعدني في تجاوز مرحلة صعبة من الاكتئاب وفقدان الشغف.' : 'High level of psychological empathy, helped me overcome severe depression and regain passion.', 'name' => $isAr ? 'مراجعة من بغداد' : 'Client from Baghdad', 'session' => $isAr ? 'استشارة فردية' : 'Individual Session', 'icon' => 'bi-heart-pulse-fill'],
                            ];
                        @endphp
                        @foreach($defaultTestimonials as $t)
                            <div class="swiper-slide h-auto">
                                <div class="testimonial-card h-100 d-flex flex-column">
                                    <div class="stars mb-3"><i class="bi bi-star-fill text-warning me-1"></i><i class="bi bi-star-fill text-warning me-1"></i><i class="bi bi-star-fill text-warning me-1"></i><i class="bi bi-star-fill text-warning me-1"></i><i class="bi bi-star-fill text-warning"></i></div>
                                    <p class="text-secondary lh-lg mb-4 flex-grow-1">"{{ $t['text'] }}"</p>
                                    <div class="d-flex align-items-center gap-3 mt-auto">
                                        <div class="testimonial-avatar"><i class="bi {{ $t['icon'] }}"></i></div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $t['name'] }}</div>
                                            <div class="text-secondary small">{{ $t['session'] }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="swiper-pagination testimonials-pagination mt-4"></div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     5.5. BOOKING BANNER CTA SECTION
═══════════════════════════════════════════════════════════ --}}
<section class="booking-banner-wrapper reveal-on-scroll">
    <div class="container">
        <div class="booking-banner-card">
            <div class="row align-items-center gy-4">
                <div class="col-lg-7">
                    <span class="badge bg-primary px-3 py-2 rounded-pill mb-3 fw-bold"><i class="bi bi-lightning-charge-fill text-warning me-1"></i> {{ __('messages.banner_tag') }}</span>
                    <h2 class="fw-black fs-2 text-white mb-3">{{ __('messages.banner_title') }}</h2>
                    <p class="fs-5 text-white-50 mb-4">{{ __('messages.banner_subtitle') }}</p>
                    <button type="button" class="btn btn-royal-primary btn-lg px-5 py-3 rounded-pill shadow" data-bs-toggle="modal" data-bs-target="#bookingModal">
                        <i class="bi bi-calendar-check-fill me-2"></i> {{ __('messages.banner_btn') }}
                    </button>
                </div>
                <div class="col-lg-5 text-center">
                    <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=700&q=80" alt="Booking Banner" class="booking-banner-img">
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══ REUSABLE BOOKING POPUP MODAL ═══ --}}
@include('partials.booking_modal')

{{-- ═══ Reel Video Modal ═══ --}}
<div class="modal fade" id="reelVideoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content rounded-4 border-0 overflow-hidden">
            <div class="modal-header bg-dark text-white border-0 py-3">
                <h6 class="modal-title fw-bold" id="reelModalTitle">مقطع توعوي</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 bg-black text-center" style="min-height:400px; display:flex; align-items:center; justify-content:center;">
                <div id="reelModalPlayer" class="w-100"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// ═══ Dual Counter-Rotating Reels Swipers ═══
if (document.querySelector('.reels-swiper-row-1')) {
    new Swiper('.reels-swiper-row-1', {
        slidesPerView: 'auto',
        spaceBetween: 18,
        loop: true,
        speed: 4500,
        autoplay: {
            delay: 0,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
        },
        allowTouchMove: true,
        freeMode: {
            enabled: true,
            momentum: false,
        },
    });
}

if (document.querySelector('.reels-swiper-row-2')) {
    new Swiper('.reels-swiper-row-2', {
        slidesPerView: 'auto',
        spaceBetween: 18,
        loop: true,
        speed: 5200,
        autoplay: {
            delay: 0,
            disableOnInteraction: false,
            reverseDirection: true,
            pauseOnMouseEnter: true,
        },
        allowTouchMove: true,
        freeMode: {
            enabled: true,
            momentum: false,
        },
    });
}

// ═══ Reel Modal ═══
function openReelModal(title, url, platform) {
    document.getElementById('reelModalTitle').textContent = title;
    const player = document.getElementById('reelModalPlayer');
    if (platform === 'youtube' || url.includes('youtube') || url.includes('youtu.be')) {
        const videoId = url.includes('youtu.be') ? url.split('/').pop() : new URL(url).searchParams.get('v');
        player.innerHTML = `<iframe width="100%" height="450" src="https://www.youtube.com/embed/${videoId || 'dQw4w9WgXcQ'}?autoplay=1" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>`;
    } else {
        player.innerHTML = `<div class="p-5 text-white"><i class="bi bi-tiktok" style="font-size:4rem;color:#ff0050;"></i><h5 class="mt-3">${title}</h5><a href="${url}" target="_blank" class="btn btn-danger btn-lg rounded-pill px-5 mt-3"><i class="bi bi-play-fill me-2"></i>مشاهدة على TikTok</a></div>`;
    }
    new bootstrap.Modal(document.getElementById('reelVideoModal')).show();
    document.getElementById('reelVideoModal').addEventListener('hidden.bs.modal', () => { player.innerHTML = ''; }, { once: true });
}

// ════ Services Responsive Swipers (Online & Clinic) ════
let onlineSwiper = null;
let clinicSwiper = null;

const defaultServicesSwiperOptions = {
    slidesPerView: 1.15,
    spaceBetween: 16,
    grabCursor: true,
    observer: true,
    observeParents: true,
    observeSlideChildren: true,
    breakpoints: {
        576: {
            slidesPerView: 1.6,
            spaceBetween: 18,
        },
        768: {
            slidesPerView: 2.15,
            spaceBetween: 20,
        },
        992: {
            slidesPerView: 3,
            spaceBetween: 24,
        },
        1200: {
            slidesPerView: 3,
            spaceBetween: 28,
        }
    }
};

if (document.querySelector('.services-swiper-online')) {
    onlineSwiper = new Swiper('.services-swiper-online', {
        ...defaultServicesSwiperOptions,
        pagination: {
            el: '.services-pagination-online',
            clickable: true,
        }
    });
}

if (document.querySelector('.services-swiper-clinic')) {
    clinicSwiper = new Swiper('.services-swiper-clinic', {
        ...defaultServicesSwiperOptions,
        pagination: {
            el: '.services-pagination-clinic',
            clickable: true,
        }
    });
}

// ════ Testimonials Responsive Swiper ════
if (document.querySelector('.testimonials-swiper')) {
    new Swiper('.testimonials-swiper', {
        slidesPerView: 1.15,
        spaceBetween: 16,
        grabCursor: true,
        loop: true,
        autoplay: {
            delay: 4500,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
        },
        pagination: {
            el: '.testimonials-pagination',
            clickable: true,
        },
        breakpoints: {
            576: {
                slidesPerView: 1.6,
                spaceBetween: 18,
            },
            768: {
                slidesPerView: 2.15,
                spaceBetween: 22,
            },
            992: {
                slidesPerView: 3,
                spaceBetween: 28,
            }
        }
    });
}

// ════ Services Category Switcher (Online vs Clinic) ════
let currentCategory = 'online';

function showServiceCategory(category) {
    currentCategory = category;
    const onlineGrid = document.getElementById('onlineCategoryGrid');
    const clinicGrid = document.getElementById('clinicCategoryGrid');
    const onlineBtn = document.getElementById('btnCategoryOnline');
    const clinicBtn = document.getElementById('btnCategoryClinic');

    if (category === 'clinic') {
        if (onlineGrid) onlineGrid.classList.add('d-none');
        if (clinicGrid) clinicGrid.classList.remove('d-none');
        if (onlineBtn) onlineBtn.classList.remove('active');
        if (clinicBtn) clinicBtn.classList.add('active');
        setTimeout(() => {
            if (clinicSwiper) clinicSwiper.update();
        }, 50);
    } else {
        if (clinicGrid) clinicGrid.classList.add('d-none');
        if (onlineGrid) onlineGrid.classList.remove('d-none');
        if (clinicBtn) clinicBtn.classList.remove('active');
        if (onlineBtn) onlineBtn.classList.add('active');
        setTimeout(() => {
            if (onlineSwiper) onlineSwiper.update();
        }, 50);
    }
}

// ═══ Lightbox Gallery Modal ═══
const galleryImagesList = @json($galleryImages ?? []);
let currentLightboxIndex = 0;

function openLightbox(index) {
    if (!galleryImagesList || galleryImagesList.length === 0) return;
    currentLightboxIndex = index;
    updateLightboxView();
    const modal = document.getElementById('galleryLightboxModal');
    if (modal) modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    const modal = document.getElementById('galleryLightboxModal');
    if (modal) modal.classList.remove('active');
    document.body.style.overflow = '';
}

function navigateLightbox(direction) {
    if (!galleryImagesList || galleryImagesList.length === 0) return;
    currentLightboxIndex = (currentLightboxIndex + direction + galleryImagesList.length) % galleryImagesList.length;
    updateLightboxView();
}

function updateLightboxView() {
    const imgEl = document.getElementById('lightboxActiveImg');
    const counterEl = document.getElementById('lightboxCurrentIndex');
    const totalEl = document.getElementById('lightboxTotalIndex');

    if (imgEl) {
        imgEl.style.opacity = '0.3';
        imgEl.style.transform = 'scale(0.96)';
        setTimeout(() => {
            imgEl.src = galleryImagesList[currentLightboxIndex];
            imgEl.style.opacity = '1';
            imgEl.style.transform = 'scale(1)';
        }, 120);
    }
    if (counterEl) counterEl.textContent = currentLightboxIndex + 1;
    if (totalEl) totalEl.textContent = galleryImagesList.length;
}

document.addEventListener('keydown', function(e) {
    const modal = document.getElementById('galleryLightboxModal');
    if (modal && modal.classList.contains('active')) {
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowRight' || e.key === 'ArrowUp') navigateLightbox(-1);
        if (e.key === 'ArrowLeft' || e.key === 'ArrowDown') navigateLightbox(1);
    }
});

// ═══ Init & Scroll Reveal Observer ═══
document.addEventListener('DOMContentLoaded', function() {
    renderAppCalendar();

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
            }
        });
    }, { threshold: 0.12 });

    document.querySelectorAll('.reveal-on-scroll').forEach(el => observer.observe(el));
});
</script>
@endsection
