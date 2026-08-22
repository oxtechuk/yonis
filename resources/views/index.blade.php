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
    background: linear-gradient(135deg, #0d1b4b 0%, #1e3a8a 45%, #2d5be3 100%);
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
    opacity: 0.3;
    animation: floatOrb 8s ease-in-out infinite alternate;
}
.hero-orb-1 { width: 400px; height: 400px; background: #4f93ff; top: -100px; right: -50px; }
.hero-orb-2 { width: 300px; height: 300px; background: #a78bfa; bottom: -80px; left: 10%; animation-delay: -3s; }
@keyframes floatOrb { from { transform: translateY(0) scale(1); } to { transform: translateY(-30px) scale(1.05); } }

.hero-badge {
    display: inline-flex; align-items: center; gap: 8px;
    background: rgba(255,255,255,0.12);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
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

.hero-title { font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 900; color: #fff; line-height: 1.2; }
.hero-title .highlight { color: #7dd3fc; }
.hero-subtitle { font-size: 1.15rem; color: rgba(255,255,255,0.8); max-width: 500px; line-height: 1.7; }

.hero-stats { display: flex; gap: 2rem; flex-wrap: wrap; margin-top: 2rem; }
.hero-stat { text-align: center; }
.hero-stat-num { font-size: 2rem; font-weight: 900; color: #fff; }
.hero-stat-label { font-size: 0.85rem; color: rgba(255,255,255,0.65); }

.hero-photo-frame {
    position: relative; border-radius: 24px; overflow: hidden;
    box-shadow: 0 30px 80px rgba(0,0,0,0.4);
    border: 4px solid rgba(255,255,255,0.2);
    max-height: 560px;
}
.hero-photo-frame img { width: 100%; height: 100%; object-fit: cover; object-position: top center; }
.hero-photo-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(to top, rgba(13,27,75,0.7) 0%, transparent 50%);
}
.hero-photo-caption {
    position: absolute; bottom: 1.2rem; left: 1rem; right: 1rem;
    color: #fff; text-align: center; font-weight: 700;
}
.hero-availability-badge {
    position: absolute; top: 1rem; right: 1rem;
    background: rgba(255,255,255,0.15); backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,0.25);
    color: #fff; padding: 0.4rem 0.9rem; border-radius: 50px; font-size: 0.8rem;
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

/* ═══ Reels Section ══════════════════════════════════════════ */
.reels-wrapper { background: #0f172a; padding: 5rem 0; position: relative; overflow: hidden; }
.reels-wrapper::before { content: ''; position: absolute; inset: 0; background: radial-gradient(circle at 50% 50%, rgba(59,82,164,0.15) 0%, transparent 70%); }
.reels-swiper { padding: 1rem 0.5rem 2rem !important; }
.reel-card-new {
    background: #1e293b; border-radius: 20px; overflow: hidden;
    transition: transform 0.3s ease; cursor: pointer;
    aspect-ratio: 9/16; position: relative;
    border: 1px solid rgba(255,255,255,0.06);
    max-height: 380px;
}
.reel-card-new:hover { transform: scale(1.03); }
.reel-card-new img { width: 100%; height: 100%; object-fit: cover; opacity: 0.85; transition: opacity 0.3s ease; }
.reel-card-new:hover img { opacity: 1; }
.reel-card-new .reel-gradient { position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, transparent 55%); }
.reel-card-new .reel-platform { position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.6); backdrop-filter: blur(6px); color: #fff; padding: 0.25rem 0.7rem; border-radius: 30px; font-size: 0.78rem; font-weight: 600; }
.reel-card-new .reel-play { position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); width: 52px; height: 52px; background: rgba(255,255,255,0.9); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: var(--primary-color); transition: all 0.3s ease; }
.reel-card-new:hover .reel-play { background: var(--primary-color); color: #fff; transform: translate(-50%,-50%) scale(1.12); }
.reel-card-new .reel-info { position: absolute; bottom: 0; left: 0; right: 0; padding: 1rem; color: #fff; }
.reel-card-new .reel-info .reel-title-text { font-size: 0.9rem; font-weight: 700; margin-bottom: 0.3rem; line-height: 1.3; }
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

    <div class="container py-5">
        <div class="row align-items-center gy-5">
            {{-- Text --}}
            <div class="col-lg-7 order-2 order-lg-1">
                <div class="hero-badge">
                    <span class="dot"></span>
                    متاح الآن للحجز الفوري
                </div>

                <h1 class="hero-title mb-3">
                    غيّر طريقة تفكيرك<br>
                    <span class="highlight">مع المعالج يونس المرشد</span>
                </h1>

                <p class="hero-subtitle mb-4">
                    استشارات نفسية متخصصة بخبرة أكثر من 10 سنوات. جلسات فردية وزوجية وأسرية عبر الأونلاين أو في العيادة.
                </p>

                <div class="d-flex flex-wrap gap-3 mb-4">
                    <a href="#booking-wizard" class="btn btn-royal-primary py-3 px-4">
                        <i class="bi bi-calendar-check-fill me-2"></i> احجز استشارتك الآن
                    </a>
                    <a href="#about" class="btn py-3 px-4 fw-bold" style="background:rgba(255,255,255,0.12); color:#fff; border:1px solid rgba(255,255,255,0.3); border-radius:50px; backdrop-filter:blur(6px);">
                        <i class="bi bi-person-fill me-2"></i> تعرف على يونس
                    </a>
                </div>

                <div class="hero-stats">
                    <div class="hero-stat">
                        <div class="hero-stat-num">+1200</div>
                        <div class="hero-stat-label">جلسة ناجحة</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-num">+10</div>
                        <div class="hero-stat-label">سنوات خبرة</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-num">98%</div>
                        <div class="hero-stat-label">رضا المراجعين</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-num">4</div>
                        <div class="hero-stat-label">قنوات تواصل</div>
                    </div>
                </div>
            </div>

            {{-- Photo --}}
            <div class="col-lg-5 order-1 order-lg-2">
                <div class="hero-photo-frame">
                    @if($profile && !empty($profile->hero_image))
                        <img src="{{ $profile->hero_image }}" alt="{{ $doctorName }}" loading="eager">
                    @elseif($profile && $profile->gallery && count($profile->gallery) > 0)
                        <img src="{{ $profile->gallery[0] }}" alt="{{ $doctorName }}" loading="eager">
                    @else
                        <img src="https://images.unsplash.com/photo-1614797136987-ab4b98843e29?auto=format&fit=crop&w=700&q=80" alt="{{ $doctorName }}" loading="eager">
                    @endif
                    <div class="hero-photo-overlay"></div>
                    <div class="hero-availability-badge">
                        <i class="bi bi-circle-fill text-success me-1" style="font-size:0.6rem;"></i> {{ __('messages.hero_badge') }}
                    </div>
                    <div class="hero-photo-caption">
                        <div class="fw-bold fs-6">{{ $doctorName }}</div>
                        <div style="font-size:0.8rem; opacity:0.85;">{{ $isAr ? ($profile->title ?? 'معالج نفسي ومدرب معتمد') : ($profile->title_en ?: ($profile->title ?? 'Licensed Psychological Therapist')) }}</div>
                    </div>
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
     3. SERVICES SECTION
═══════════════════════════════════════════════════════════ --}}
<section id="services" class="services-wrapper reveal-on-scroll">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-label"><i class="bi bi-layers"></i> الجلسات والأسعار</div>
            <h2 class="section-title">اختر نوع الاستشارة المناسبة</h2>
            <p class="section-subtitle">جلسات بأسعار مناسبة تبدأ من أول استشارة - بإمكانك الحجز في العيادة أو أونلاين</p>
        </div>

        <div class="row g-4 justify-content-center">
            @foreach($services as $index => $service)
                <div class="col-md-6 col-lg-4">
                    <div class="service-card-new {{ $index === 1 ? 'popular' : '' }}"
                         onclick="selectServiceAndOpenModal({{ $service->id }}, '{{ $service->title }}', {{ $service->price }}, {{ $service->duration }})">
                        @if($index === 1)
                            <span class="popular-badge"><i class="bi bi-star-fill text-warning me-1"></i> {{ $isAr ? 'الأكثر طلباً' : 'Most Popular' }}</span>
                        @endif

                        <div class="text-center mb-3">
                            <div class="my-2" style="font-size: 2.2rem; color: var(--primary-color);">
                                @if($index == 0) <i class="bi bi-chat-text-fill"></i> @elseif($index == 1) <i class="bi bi-camera-video-fill"></i> @else <i class="bi bi-hospital-fill"></i> @endif
                            </div>
                            <h4 class="fw-bold mt-2 mb-1">{{ $service->title }}</h4>
                            <p class="text-secondary small">{{ $service->description }}</p>
                            <span class="badge bg-light text-dark border fw-bold"><i class="bi bi-clock me-1"></i> {{ $service->duration }} {{ __('messages.minutes') }}</span>
                        </div>

                        <div class="channel-prices">
                            <div class="channel-price-item">
                                <span><i class="bi bi-hospital me-1 text-danger"></i> {{ $isAr ? 'عيادة' : 'Clinic' }}</span>
                                <span class="price">${{ number_format($service->clinic_price ?? $service->price, 0) }}</span>
                            </div>
                            <div class="channel-price-item">
                                <span><i class="bi bi-chat-text me-1 text-primary"></i> {{ $isAr ? 'شات' : 'Chat' }}</span>
                                <span class="price">${{ number_format($service->chat_price ?? $service->price, 0) }}</span>
                            </div>
                            <div class="channel-price-item">
                                <span><i class="bi bi-telephone me-1 text-success"></i> {{ $isAr ? 'صوت' : 'Voice' }}</span>
                                <span class="price">${{ number_format($service->voice_price ?? $service->price, 0) }}</span>
                            </div>
                            <div class="channel-price-item">
                                <span><i class="bi bi-camera-video me-1 text-info"></i> {{ $isAr ? 'فيديو' : 'Video' }}</span>
                                <span class="price">${{ number_format($service->video_price ?? $service->price, 0) }}</span>
                            </div>
                        </div>

                        <button type="button" class="btn btn-royal-outline w-100 mt-3 rounded-pill" data-bs-toggle="modal" data-bs-target="#bookingModal">
                            {{ __('messages.book_now') }}
                        </button>
                    </div>
                </div>
            @endforeach
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
                <div class="section-label" style="background:rgba(255,255,255,0.08); color:#fff;"><i class="bi bi-camera-reels-fill text-danger"></i> مقاطع توعوية</div>
                <h2 class="section-title text-white">تابعنا عبر منصات التواصل</h2>
                <p class="mb-0" style="color:rgba(255,255,255,0.65);">إرشادات نفسية ومقاطع توعوية قصيرة من أستاذ يونس</p>
            </div>
            <a href="#" target="_blank" class="btn btn-outline-light btn-sm rounded-pill px-4 d-none d-md-inline-flex align-items-center gap-2">
                <i class="bi bi-tiktok"></i> متابعة TikTok
            </a>
        </div>

        @if($reels->count() > 0)
            <div class="swiper reels-swiper">
                <div class="swiper-wrapper">
                    @foreach($reels as $reel)
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
                <div class="swiper-pagination"></div>
            </div>
        @else
            {{-- Placeholder Reels --}}
            <div class="row g-3">
                @foreach(['الكآبة وعلاجها', 'التعامل مع القلق', 'أسرار العلاقات الناجحة', 'ماذا تفعل عند الضغط'] as $i => $reelTitle)
                    <div class="col-6 col-md-3">
                        <div class="reel-card-new" onclick="">
                            <img src="https://images.unsplash.com/photo-{{ ['1544027893741-31f9c07a7688','1509842477838-1af3e1e24cf0','1591779051696-1f3f19ee63a4','1552581234-26160f608093'][$i] }}?auto=format&fit=crop&w=400&q=70&h=700" alt="{{ $reelTitle }}" loading="lazy">
                            <div class="reel-gradient"></div>
                            <div class="reel-platform"><i class="bi bi-youtube me-1"></i>YouTube</div>
                            <div class="reel-play"><i class="bi bi-play-fill"></i></div>
                            <div class="reel-info">
                                <div class="reel-title-text">{{ $reelTitle }}</div>
                                <div class="reel-views"><i class="bi bi-eye-fill"></i> {{ number_format(rand(10000,50000)) }} مشاهدة</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     5. TESTIMONIALS SECTION
═══════════════════════════════════════════════════════════ --}}
{{-- ═══════════════════════════════════════════════════════════
     5. TESTIMONIALS SECTION
═══════════════════════════════════════════════════════════ --}}
<section class="testimonials-wrapper py-5 reveal-on-scroll">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-label"><i class="bi bi-chat-quote-fill"></i> {{ __('messages.testimonials_title') }}</div>
            <h2 class="section-title">{{ __('messages.testimonials_subtitle') }}</h2>
        </div>

        <div class="row g-4">
            @if(isset($testimonials) && count($testimonials) > 0)
                @foreach($testimonials as $t)
                    <div class="col-md-4">
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
                    ];
                @endphp
                @foreach($defaultTestimonials as $t)
                    <div class="col-md-4">
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

{{-- ═══ BOOKING POPUP MODAL WITH MOBILE APP MATCHING FLOW ═══ --}}
<div class="modal fade" id="bookingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mobile-app-modal-dialog modal-dialog-scrollable">
        <div class="modal-content mobile-app-modal-content position-relative">
            
            {{-- Header --}}
            <div class="mobile-app-header">
                <button type="button" class="btn btn-sm btn-light rounded-circle p-2 d-flex align-items-center justify-content-center" data-bs-dismiss="modal" style="width:36px; height:36px;">
                    <i class="bi bi-arrow-right fs-5"></i>
                </button>
                <h5 class="mobile-app-header-title" id="app-header-title-text">{{ __('messages.immediate_session') }}</h5>
                <div style="width:36px;"></div>
            </div>

            {{-- Body --}}
            <div class="mobile-app-body">
                
                {{-- ═══ SCREEN 1: Session Details & Payment Options ═══ --}}
                <div id="app-screen-1">
                    
                    {{-- Duration Selection --}}
                    <div class="mb-4">
                        <div class="app-section-title">{{ __('messages.duration_title') }}</div>
                        <div class="app-duration-grid">
                            <div class="app-duration-item selected" onclick="selectAppDuration(15, 150, this)">
                                <div class="app-duration-icon"><i class="bi bi-hourglass-split"></i></div>
                                <div class="app-duration-time">15 {{ __('messages.minutes') }}</div>
                                <div class="app-duration-price">150 {{ __('messages.sar') }}</div>
                            </div>
                            <div class="app-duration-item" onclick="selectAppDuration(30, 250, this)">
                                <div class="app-duration-icon"><i class="bi bi-hourglass-split"></i></div>
                                <div class="app-duration-time">30 {{ __('messages.minutes') }}</div>
                                <div class="app-duration-price">250 {{ __('messages.sar') }}</div>
                            </div>
                            <div class="app-duration-item" onclick="selectAppDuration(45, 350, this)">
                                <div class="app-duration-icon"><i class="bi bi-hourglass-split"></i></div>
                                <div class="app-duration-time">45 {{ __('messages.minutes') }}</div>
                                <div class="app-duration-price">350 {{ __('messages.sar') }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Consultation Title --}}
                    <div class="mb-4">
                        <div class="app-section-title">{{ __('messages.consultation_subject') }}</div>
                        <input type="text" id="app_consultation_title" class="form-control app-input w-100" placeholder="{{ __('messages.consultation_subject_ph') }}" required>
                    </div>

                    {{-- Order Details --}}
                    <div class="mb-4">
                        <div class="app-section-title">{{ __('messages.consultation_details') }}</div>
                        <textarea id="app_consultation_details" class="form-control app-input w-100" rows="3" placeholder="{{ __('messages.consultation_details_ph') }}"></textarea>
                    </div>

                    {{-- Payment Method --}}
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
                                    <svg width="38" height="22" viewBox="0 0 36 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                      <rect width="36" height="20" rx="4" fill="#1A1F71"/>
                                      <path d="M14.5 14h-2.3l1.4-8.8h2.3l-1.4 8.8zm8.6-8.6c-.5-.2-1.3-.4-2.2-.4-2.4 0-4.1 1.3-4.1 3.1 0 1.4 1.2 2.1 2.2 2.6 1 .5 1.3.8 1.3 1.2 0 .6-.7.9-1.4.9-.9 0-1.5-.2-2-.5l-.3-.1-.3 2.1c.6.3 1.7.5 2.8.5 2.6 0 4.3-1.3 4.3-3.2 0-1.1-.6-1.9-2.1-2.6-.9-.4-1.4-.7-1.4-1.2 0-.4.5-.8 1.4-.8.8 0 1.4.2 1.9.4l.2.1.3-2.1zm5.2 0h-1.8c-.6 0-1 .2-1.2.7l-3.5 8.1h2.4l.5-1.3h3l.3 1.3h2.1l-1.8-8.8zm-2.4 5.4l1.2-3.4.7 3.4h-1.9zM10.8 5.4L8.5 11.4l-.2-1.2c-.4-1.4-1.6-3-3-3.7l2 7.5h2.4l3.6-8.6h-2.5z" fill="#FFF"/>
                                      <path d="M6.3 5.4H2.4L2.3 5.6c3.1.8 5.2 2.7 6 4.9l-.9-4.4c-.1-.5-.5-.7-1.1-.7z" fill="#F7B600"/>
                                    </svg>
                                    <svg width="38" height="22" viewBox="0 0 36 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                      <rect width="36" height="20" rx="4" fill="#005B94"/>
                                      <path d="M7 13V7h2.2l1.6 3.6L12.4 7H14.5v6h-1.8v-3.8L11.2 13h-1L8.8 9.2V13H7zm11.5 0v-1.1c-.4.8-1.2 1.2-2.1 1.2-1.5 0-2.4-1.1-2.4-2.6 0-1.5.9-2.6 2.4-2.6.9 0 1.7.4 2.1 1.2V7.9h1.8V13h-1.8zm-2.1-1.4c.8 0 1.3-.6 1.3-1.4s-.5-1.4-1.3-1.4-1.3.6-1.3 1.4.5 1.4 1.3 1.4zm7.8 1.4v-1.1c-.4.8-1.2 1.2-2.1 1.2-1.5 0-2.4-1.1-2.4-2.6 0-1.5.9-2.6 2.4-2.6.9 0 1.7.4 2.1 1.2V7H26v6h-1.8zm-2.1-1.4c.8 0 1.3-.6 1.3-1.4s-.5-1.4-1.3-1.4-1.3.6-1.3 1.4.5 1.4 1.3 1.4z" fill="#8DC63F"/>
                                    </svg>
                                </div>
                                <span class="fw-bold fs-6">ادفع باستخدام البطاقة</span>
                            </div>
                            <div class="app-payment-radio"></div>
                        </div>

                        {{-- Stripe Test Card Filler Badge --}}
                        <div class="mt-2 text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" onclick="autoFillStripeTestCard()">
                                💳 تعبئة بطاقة Stripe التجريبية (Test Card)
                            </button>
                        </div>
                    </div>

                    {{-- Summary Totals & Terms --}}
                    <div class="bg-white p-3 rounded-4 border mb-3">
                        <div class="d-flex justify-content-between mb-2 fs-6">
                            <span class="text-secondary">{{ __('messages.order_total') }}:</span>
                            <span class="fw-bold text-dark" id="app-session-price">150 {{ __('messages.sar') }}</span>
                        </div>
                        <div class="d-flex justify-content-between fs-6 border-top pt-2">
                            <span class="fw-bold text-dark">{{ __('messages.order_total') }}:</span>
                            <span class="fw-bold" style="color:var(--primary-color);" id="app-required-price">150 {{ __('messages.sar') }}</span>
                        </div>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="app_terms_check" checked>
                        <label class="form-check-label small fw-bold text-secondary" for="app_terms_check">
                            {{ $isAr ? 'أوافق على الشروط والأحكام' : 'I agree to the Terms & Conditions' }}
                        </label>
                    </div>

                    {{-- Bottom Action Bar for Screen 1 --}}
                    <div class="mobile-app-bottom-bar">
                        <div>
                            <div class="app-total-label">{{ __('messages.order_total') }}</div>
                            <div class="app-total-value" id="app-bottom-total">150 {{ __('messages.sar') }}</div>
                        </div>
                        <button type="button" class="btn-app-primary" onclick="goToAppScreen2()">{{ __('messages.next') }}</button>
                    </div>

                </div>{{-- End Screen 1 --}}

                {{-- ═══ SCREEN 2: Date, Slot & Registration ═══ --}}
                <div id="app-screen-2" class="d-none">
                    
                    {{-- Calendar View --}}
                    <div class="app-calendar-box">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <button type="button" class="btn btn-sm btn-light rounded-circle" onclick="changeAppMonth(-1)"><i class="bi bi-chevron-right"></i></button>
                            <div class="app-calendar-month mb-0" id="app-calendar-month-title">{{ $isAr ? 'أكتوبر 2026' : 'October 2026' }}</div>
                            <button type="button" class="btn btn-sm btn-light rounded-circle" onclick="changeAppMonth(1)"><i class="bi bi-chevron-left"></i></button>
                        </div>
                        <div class="app-calendar-weekdays">
                            <div>{{ $isAr ? 'أحد' : 'Sun' }}</div><div>{{ $isAr ? 'إثن' : 'Mon' }}</div><div>{{ $isAr ? 'ثلا' : 'Tue' }}</div><div>{{ $isAr ? 'أرب' : 'Wed' }}</div><div>{{ $isAr ? 'خميس' : 'Thu' }}</div><div>{{ $isAr ? 'جمع' : 'Fri' }}</div><div>{{ $isAr ? 'سبت' : 'Sat' }}</div>
                        </div>
                        <div class="app-calendar-days" id="app-calendar-days-grid">
                            {{-- Generated Dynamically --}}
                        </div>
                    </div>

                    {{-- Available Time Slots --}}
                    <div class="mb-4">
                        <div class="app-section-title"><i class="bi bi-clock me-1 text-primary"></i> {{ __('messages.select_time') }}</div>
                        <div class="app-slots-grid" id="app-slots-grid">
                            <div class="app-slot-pill selected" onclick="selectAppSlot('09:00 AM', this)">09:00 {{ $isAr ? 'ص' : 'AM' }}</div>
                            <div class="app-slot-pill" onclick="selectAppSlot('10:00 AM', this)">10:00 {{ $isAr ? 'ص' : 'AM' }}</div>
                            <div class="app-slot-pill" onclick="selectAppSlot('11:30 AM', this)">11:30 {{ $isAr ? 'ص' : 'AM' }}</div>
                            <div class="app-slot-pill" onclick="selectAppSlot('01:00 PM', this)">01:00 {{ $isAr ? 'م' : 'PM' }}</div>
                            <div class="app-slot-pill" onclick="selectAppSlot('02:30 PM', this)">02:30 {{ $isAr ? 'م' : 'PM' }}</div>
                            <div class="app-slot-pill" onclick="selectAppSlot('04:00 PM', this)">04:00 {{ $isAr ? 'م' : 'PM' }}</div>
                        </div>
                    </div>

                    {{-- Registration Section --}}
                    <div class="mb-4">
                        <div class="app-section-title fs-5 fw-black text-dark mb-3">{{ __('messages.register') }}</div>
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary mb-1">{{ __('messages.full_name') }}</label>
                            <div class="position-relative">
                                <input type="text" id="app_user_name" class="form-control app-input w-100 pe-4" placeholder="{{ __('messages.full_name') }}" required>
                                <i class="bi bi-person position-absolute top-50 translate-middle-y end-0 me-3 text-secondary"></i>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary mb-1">{{ __('messages.whatsapp_number') }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 border rounded-start-4 fw-bold text-success">
                                    <i class="bi bi-whatsapp me-1"></i> +966
                                </span>
                                <input type="tel" id="app_user_phone" class="form-control app-input border-start-0 rounded-end-4" placeholder="0512345678" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary mb-1">{{ __('messages.password') }}</label>
                            <div class="position-relative">
                                <input type="password" id="app_user_password" class="form-control app-input w-100 pe-4" placeholder="{{ __('messages.password') }}" required minlength="6">
                                <i class="bi bi-lock position-absolute top-50 translate-middle-y end-0 me-3 text-secondary"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Bottom Action Bar for Screen 2 --}}
                    <div class="mobile-app-bottom-bar">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" onclick="goToAppScreen1()"><i class="bi bi-arrow-right me-1"></i> {{ __('messages.back') }}</button>
                        <button type="button" class="btn-app-primary" id="app-submit-pay-btn" onclick="executeAppBooking()">{{ __('messages.confirm_booking') }}</button>
                    </div>

                </div>{{-- End Screen 2 --}}

                {{-- ═══ SCREEN 3: Confirmation & Success ═══ --}}
                <div id="app-screen-3" class="d-none text-center py-2">
                    
                    <div class="success-circle mx-auto mb-3" style="width:80px; height:80px; font-size:2.2rem; background:var(--primary-color); color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow: 0 10px 25px rgba(59, 82, 164, 0.35);">
                        <i class="bi bi-check-lg"></i>
                    </div>

                    <h4 class="fw-black text-dark mb-2">{{ __('messages.booking_success_title') }}</h4>
                    <p class="text-secondary small mb-4">{{ __('messages.booking_success_sub') }}</p>

                    {{-- Reference Card Matching Screenshot 3 --}}
                    <div class="app-success-card text-start">
                        <div class="app-success-row">
                            <span class="text-secondary">رقم المرجع</span>
                            <span class="fw-bold text-primary fs-6" id="app-res-ref">#REF-8492</span>
                        </div>
                        <div class="app-success-row">
                            <span class="text-secondary">الخدمة</span>
                            <span class="fw-bold text-dark" id="app-res-service">جلسة استشارة نفسية</span>
                        </div>
                        <div class="app-success-row">
                            <span class="text-secondary">الموعد</span>
                            <span class="fw-bold text-dark" id="app-res-datetime">15 أكتوبر 2026 | 04:00 مساءً</span>
                        </div>
                        <div class="app-success-row">
                            <span class="text-secondary">المستشار</span>
                            <div class="d-flex align-items-center gap-2">
                                <img src="https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&w=80&q=80" alt="Doctor" class="rounded-circle" style="width:28px; height:28px; object-fit:cover;">
                                <span class="fw-bold text-dark">د. يونس المرشد</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-2 mt-4">
                        <a id="app-start-consultation-link" href="#" target="_blank" class="btn btn-app-primary w-100 py-3 d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-calendar-check-fill"></i> ابدأ الاستشارة
                        </a>
                        <button type="button" class="btn btn-light rounded-pill py-3 fw-bold text-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-house-fill me-1"></i> العودة للرئيسية
                        </button>
                    </div>

                </div>{{-- End Screen 3 --}}

            </div>
        </div>
    </div>
</div>

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
// ═══ Swiper Init ═══
if (document.querySelector('.reels-swiper')) {
    new Swiper('.reels-swiper', {
        slidesPerView: 'auto', spaceBetween: 16, centeredSlides: false,
        grabCursor: true, freeMode: true,
        pagination: { el: '.swiper-pagination', clickable: true },
        breakpoints: { 320: { slidesPerView: 1.5 }, 480: { slidesPerView: 2.2 }, 768: { slidesPerView: 3.5 }, 1024: { slidesPerView: 4.5 } }
    });
}

// ═══ State ═══
let state = {
    serviceId: {{ $services->first()->id ?? 1 }},
    serviceTitle: '{{ $services->first()->title ?? '' }}',
    basePriceClinic: {{ $services->first()->clinic_price ?? $services->first()->price ?? 0 }},
    basePriceChat: {{ $services->first()->chat_price ?? $services->first()->price ?? 0 }},
    basePriceVoice: {{ $services->first()->voice_price ?? $services->first()->price ?? 0 }},
    basePriceVideo: {{ $services->first()->video_price ?? $services->first()->price ?? 0 }},
    channel: 'video',
    bookingType: 'online',
    price: {{ $services->first()->video_price ?? $services->first()->price ?? 0 }},
    date: '{{ date("Y-m-d", strtotime("+1 day")) }}',
    slot: '',
    name: '',
    phone: '',
    bookingRef: '',
    paymentMethod: 'stripe_card',
};

const channelLabels = { clinic: 'كشف في العيادة 🏥', chat: 'محادثة شات 💬', voice: 'مكالمة صوتية 📞', video: 'مكالمة فيديو 📹' };
const waNumber = '{{ preg_replace("/\D/", "", App\Models\Setting::get("whatsapp_number", "")) }}';

// ═══ Channel Select ═══
function selectChannel(channel, el) {
    document.querySelectorAll('.channel-option').forEach(e => e.classList.remove('selected'));
    el.classList.add('selected');
    state.channel = channel;
    state.bookingType = channel === 'clinic' ? 'clinic' : 'online';
    document.getElementById('selected_channel').value = channel;
    document.getElementById('selected_booking_type').value = state.bookingType;
    updateCurrentPrice();
}

function updateCurrentPrice() {
    const priceMap = { clinic: state.basePriceClinic, chat: state.basePriceChat, voice: state.basePriceVoice, video: state.basePriceVideo };
    state.price = priceMap[state.channel] || state.basePriceClinic;
    document.getElementById('selected_price').value = state.price;
    ['clinic','chat','voice','video'].forEach(c => {
        const el = document.getElementById(`price-${c}`);
        if (el) el.textContent = '$' + priceMap[c];
    });
}

// ═══ Service Select ═══
function selectServiceWizard(btn, id, duration, title, price, clinic, chat, voice, video) {
    document.querySelectorAll('.service-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
    state.serviceId = id; state.serviceTitle = title;
    state.basePriceClinic = clinic; state.basePriceChat = chat;
    state.basePriceVoice = voice; state.basePriceVideo = video;
    document.getElementById('selected_service_id').value = id;
    document.getElementById('selected_service_title').value = title;
    updateCurrentPrice();
    fetchSlots();
}

function selectServiceAndScroll(id, title, price, duration) {
    document.getElementById('booking-wizard').scrollIntoView({ behavior: 'smooth' });
    setTimeout(() => {
        const btn = document.querySelector(`.service-btn[data-service-id="${id}"]`);
        if (btn) btn.click();
    }, 600);
}

// ═══ Slots ═══
function fetchSlots() {
    const date = document.getElementById('booking_date_input').value;
    state.date = date;
    const grid = document.getElementById('slots-grid');
    grid.innerHTML = '<div class="text-center text-muted py-3 col-span-3"><div class="spinner-border spinner-border-sm me-1"></div> جاري تحميل الأوقات...</div>';
    document.getElementById('selected_slot').value = '';

    fetch("{{ url('/api/slots') }}?service_id=" + state.serviceId + "&date=" + date)
        .then(r => r.json())
        .then(data => {
            grid.innerHTML = '';
            const slots = Array.isArray(data) ? data : (data.slots || []);
            if (!slots.length) {
                grid.innerHTML = '<div class="text-center text-danger py-2" style="grid-column:1/-1">لا تتوفر مواعيد في هذا اليوم. اختر تاريخاً آخر.</div>';
                return;
            }
            slots.forEach((s, i) => {
                const div = document.createElement('div');
                div.className = `slot-item${i === 0 ? ' selected' : ''}`;
                div.textContent = s.formatted || s.start || s;
                div.onclick = function() {
                    document.querySelectorAll('.slot-item').forEach(x => x.classList.remove('selected'));
                    div.classList.add('selected');
                    state.slot = div.textContent;
                    document.getElementById('selected_slot').value = state.slot;
                };
                if (i === 0) { state.slot = div.textContent; document.getElementById('selected_slot').value = state.slot; }
                grid.appendChild(div);
            });
        })
        .catch(() => {
            grid.innerHTML = '<div class="text-center text-muted py-2" style="grid-column:1/-1">تعذّر تحميل الأوقات. يرجى المحاولة لاحقاً.</div>';
        });
}

// ═══ Payment Method Select ═══
function selectPayment(method, label) {
    state.paymentMethod = method;
    document.querySelectorAll('.payment-method').forEach(e => e.classList.remove('selected'));
    label.classList.add('selected');
}

// ═══ Step 1 → Step 2 ═══
function goToStep2() {
    const name = document.getElementById('guest_name').value.trim();
    const phone = document.getElementById('guest_phone').value.trim();
    const pass = document.getElementById('guest_password').value.trim();
    const slot = state.slot;

    if (!name) { alert('الرجاء إدخال الاسم الكامل.'); document.getElementById('guest_name').focus(); return; }
    if (!phone) { alert('الرجاء إدخال رقم الواتساب.'); document.getElementById('guest_phone').focus(); return; }
    if (!pass || pass.length < 6) { alert('الرجاء إدخال كلمة مرور (6 أحرف على الأقل).'); document.getElementById('guest_password').focus(); return; }
    if (!slot) { alert('الرجاء اختيار وقت متاح للموعد.'); return; }

    state.name = name; state.phone = phone;

    // Fill summary
    document.getElementById('s-service').textContent = state.serviceTitle;
    document.getElementById('s-channel').textContent = channelLabels[state.channel];
    document.getElementById('s-date').textContent = state.date;
    document.getElementById('s-time').textContent = slot;
    document.getElementById('s-name').textContent = name;
    document.getElementById('s-price').textContent = '$' + state.price;

    // Update wizard UI
    document.getElementById('wizard-step-1').classList.add('d-none');
    document.getElementById('wizard-step-2').classList.remove('d-none');

    document.getElementById('step-circle-1').classList.remove('active'); document.getElementById('step-circle-1').classList.add('done'); document.getElementById('step-circle-1').textContent = '✓';
    document.getElementById('step-circle-2').classList.add('active');
    document.getElementById('step-line-1').classList.add('done');
    document.getElementById('step-label-1').classList.remove('active');
    document.getElementById('step-label-2').classList.add('active');

    document.getElementById('wizard-header-title').textContent = 'مراجعة ودفع';
    document.getElementById('wizard-header-sub').textContent = 'راجع بيانات موعدك ثم أكد الدفع';

    window.scrollTo({ top: document.getElementById('booking-wizard').offsetTop - 80, behavior: 'smooth' });
}

function goBackToStep1() {
    document.getElementById('wizard-step-2').classList.add('d-none');
    document.getElementById('wizard-step-1').classList.remove('d-none');

    document.getElementById('step-circle-1').classList.remove('done'); document.getElementById('step-circle-1').classList.add('active'); document.getElementById('step-circle-1').textContent = '1';
    document.getElementById('step-circle-2').classList.remove('active');
    document.getElementById('step-line-1').classList.remove('done');
    document.getElementById('step-label-1').classList.add('active');
    document.getElementById('step-label-2').classList.remove('active');

    document.getElementById('wizard-header-title').textContent = 'احجز استشارتك الآن';
    document.getElementById('wizard-header-sub').textContent = 'ادخل بياناتك واختر موعدك المناسب';
}

// ═══ Submit Booking ═══
function submitBooking() {
    const btn = document.getElementById('pay-btn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> جاري معالجة الحجز والدفع...';

    const payload = {
        service_id: state.serviceId,
        booking_type: state.bookingType,
        consultation_type: state.channel,
        date: state.date,
        start_time: state.slot,
        name: state.name,
        phone: state.phone,
        password: document.getElementById('guest_password').value,
        title: document.getElementById('consultation_title').value || state.serviceTitle,
        notes: document.getElementById('consultation_notes').value,
    };

    const headers = {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json',
    };

    fetch("{{ url('/api/checkout/initialize') }}", { method: 'POST', headers, body: JSON.stringify(payload) })
        .then(r => r.json())
        .then(data => {
            if (!data.success) throw new Error(data.message || 'حدث خطأ في الطلب.');
            return fetch("{{ url('/api/checkout/confirm') }}", {
                method: 'POST', headers,
                body: JSON.stringify({ booking_reference: data.booking_reference })
            }).then(r => r.json());
        })
        .then(res => {
            if (!res || !res.success) throw new Error(res?.message || 'لم يتم تأكيد الحجز.');
            showSuccess(res);
        })
        .catch(err => {
            alert(err.message || 'تعذّر إكمال الحجز. يرجى المحاولة مرة أخرى.');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-shield-lock-fill me-2"></i> الدفع وتأكيد الحجز';
        });
}

// ═══ Show Success + WhatsApp ═══
function showSuccess(res) {
    const booking = res.booking;
    state.bookingRef = booking.booking_reference;

    document.getElementById('res-ref').textContent = '#' + booking.booking_reference;
    document.getElementById('res-service').textContent = booking.title || state.serviceTitle;
    document.getElementById('res-datetime').textContent = (booking.date || state.date) + ' | ' + (booking.start_time || state.slot);
    document.getElementById('res-channel').textContent = channelLabels[state.channel];
    document.getElementById('res-price').textContent = '$' + state.price;

    // Build WhatsApp message (clean text without emojis)
    const msg = `السلام عليكم ورحمة الله
تم تأكيد حجزك بنجاح

تفاصيل موعدك:
------------------------------------
رقم المرجع: #${booking.booking_reference}
الاسم: ${state.name}
الخدمة: ${state.serviceTitle}
القناة: ${channelLabels[state.channel]}
التاريخ: ${state.date}
الوقت: ${state.slot}
المبلغ: $${state.price}
------------------------------------

يسعدنا خدمتك
المعالج النفسي يونس المرشد`;

    const waUrl = waNumber
        ? `https://wa.me/${waNumber}?text=${encodeURIComponent(msg)}`
        : `https://wa.me/?text=${encodeURIComponent(msg)}`;

    document.getElementById('whatsapp-confirm-link').href = waUrl;

    // Transition to success
    document.getElementById('wizard-step-2').classList.add('d-none');
    document.getElementById('wizard-success').classList.remove('d-none');

    document.getElementById('step-circle-2').classList.remove('active'); document.getElementById('step-circle-2').classList.add('done'); document.getElementById('step-circle-2').innerHTML = '<i class="bi bi-check-lg"></i>';
    document.getElementById('step-circle-3').classList.add('active');
    document.getElementById('step-line-2').classList.add('done');
    document.getElementById('step-label-2').classList.remove('active');
    document.getElementById('step-label-3').classList.add('active');

    document.getElementById('wizard-header-title').textContent = 'تم التأكيد بنجاح';
    document.getElementById('wizard-header-sub').textContent = 'تم تأكيد حجزك وإنشاء حسابك بنجاح';

    // Auto-open WhatsApp after 1.5s
    setTimeout(() => { if (waNumber) window.open(waUrl, '_blank'); }, 1500);
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

// ════ Modal Switch Logic (Clinic vs Online) ═══
let currentModalType = 'clinic';

function switchModalBookingType(type, el) {
    currentModalType = type;
    document.querySelectorAll('.popup-switch-btn').forEach(b => b.classList.remove('active'));
    if (el) el.classList.add('active');
}

function selectServiceAndOpenModal(id, title, price, duration) {
    const modalEl = document.getElementById('bookingModal');
    if (modalEl) {
        new bootstrap.Modal(modalEl).show();
    }
}

// ════ Mobile App Flow JS Logic ════
let appState = {
    duration: 15,
    price: 150,
    paymentMethod: 'stripe_card',
    title: '',
    details: '',
    date: '2026-10-15',
    slot: '04:00 م',
    year: 2026,
    month: 9, // October
};

const monthNamesAr = [
    'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو',
    'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'
];

function selectAppDuration(duration, price, el) {
    appState.duration = duration;
    appState.price = price;
    document.querySelectorAll('.app-duration-item').forEach(i => i.classList.remove('selected'));
    if (el) el.classList.add('selected');
    
    document.getElementById('app-session-price').textContent = price + ' ر.س';
    document.getElementById('app-required-price').textContent = price + ' ر.س';
    document.getElementById('app-bottom-total').textContent = price + ' ر.س';
}

function selectAppPayment(method, el) {
    appState.paymentMethod = method;
    document.querySelectorAll('.app-payment-card').forEach(c => c.classList.remove('selected'));
    if (el) el.classList.add('selected');
}

function autoFillStripeTestCard() {
    selectAppPayment('stripe_card', document.querySelectorAll('.app-payment-card')[1]);
    alert('💳 تم تحديد دفع Stripe بالبطاقة التجريبية (Test Card: 4242 4242 4242 4242 | CVC: 123 | Exp: 12/34)');
}

function goToAppScreen2() {
    const titleInput = document.getElementById('app_consultation_title');
    const title = titleInput.value.trim();
    if (!title) {
        alert('يرجى كتابة عنوان الاستشارة للاستمرار.');
        titleInput.focus();
        return;
    }
    appState.title = title;
    appState.details = document.getElementById('app_consultation_details').value;

    document.getElementById('app-screen-1').classList.add('d-none');
    document.getElementById('app-screen-2').classList.remove('d-none');
    renderAppCalendar();
}

function goToAppScreen1() {
    document.getElementById('app-screen-2').classList.add('d-none');
    document.getElementById('app-screen-1').classList.remove('d-none');
}

function changeAppMonth(offset) {
    appState.month += offset;
    if (appState.month > 11) { appState.month = 0; appState.year++; }
    if (appState.month < 0) { appState.month = 11; appState.year--; }
    renderAppCalendar();
}

function renderAppCalendar() {
    document.getElementById('app-calendar-month-title').textContent = monthNamesAr[appState.month] + ' ' + appState.year;
    const daysGrid = document.getElementById('app-calendar-days-grid');
    daysGrid.innerHTML = '';

    const firstDay = new Date(appState.year, appState.month, 1).getDay();
    const daysInMonth = new Date(appState.year, appState.month + 1, 0).getDate();

    for (let i = 0; i < firstDay; i++) {
        const empty = document.createElement('div');
        daysGrid.appendChild(empty);
    }

    for (let d = 1; d <= daysInMonth; d++) {
        const dayEl = document.createElement('div');
        dayEl.className = 'app-calendar-day' + (d === 15 ? ' selected' : '');
        dayEl.textContent = d;
        dayEl.onclick = function() {
            document.querySelectorAll('.app-calendar-day').forEach(el => el.classList.remove('selected'));
            dayEl.classList.add('selected');
            const mm = (appState.month + 1).toString().padStart(2, '0');
            const dd = d.toString().padStart(2, '0');
            appState.date = `${appState.year}-${mm}-${dd}`;
        };
        daysGrid.appendChild(dayEl);
    }
}

function selectAppSlot(time, el) {
    appState.slot = time;
    document.querySelectorAll('.app-slot-pill').forEach(p => p.classList.remove('selected'));
    if (el) el.classList.add('selected');
}

function executeAppBooking() {
    const name = document.getElementById('app_user_name').value.trim();
    const phone = document.getElementById('app_user_phone').value.trim();
    const password = document.getElementById('app_user_password').value.trim();

    if (!name || !phone || !password) {
        alert('يرجى تعبئة جميع بيانات إنشاء الحساب (الاسم، الواتساب، وكلمة المرور).');
        return;
    }

    const btn = document.getElementById('app-submit-pay-btn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> جاري معالجة الدفع بـ Stripe...';

    const payload = {
        service_id: 1,
        booking_type: 'online',
        consultation_type: 'video',
        date: appState.date,
        start_time: appState.slot,
        name: name,
        phone: '+966' + phone,
        password: password,
        title: appState.title,
        notes: appState.details,
    };

    const headers = {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json',
    };

    fetch("{{ url('/api/checkout/initialize') }}", { method: 'POST', headers, body: JSON.stringify(payload) })
        .then(r => r.json())
        .then(data => {
            if (!data.success) throw new Error(data.message || 'حدث خطأ في طلب الحجز.');
            return fetch("{{ url('/api/checkout/confirm') }}", {
                method: 'POST', headers,
                body: JSON.stringify({ booking_reference: data.booking_reference, payment_method: appState.paymentMethod })
            }).then(r => r.json());
        })
        .then(res => {
            btn.disabled = false;
            btn.textContent = 'التالي';

            // Show Screen 3 Success
            document.getElementById('app-screen-2').classList.add('d-none');
            document.getElementById('app-screen-3').classList.remove('d-none');

            const ref = res?.booking?.booking_reference || 'REF-' + Math.floor(1000 + Math.random() * 9000);
            document.getElementById('app-res-ref').textContent = '#' + ref;
            document.getElementById('app-res-service').textContent = appState.title || 'جلسة استشارة نفسية';
            document.getElementById('app-res-datetime').textContent = appState.date + ' | ' + appState.slot;
            
            const waMsg = `السلام عليكم دكتور يونس، تم الحجز بنجاح عبر Stripe Test Mode\nرقم المرجع: #${ref}\nالاسم: ${name}\nالموعد: ${appState.date} ${appState.slot}`;
            const waUrl = waNumber ? `https://wa.me/${waNumber}?text=${encodeURIComponent(waMsg)}` : `https://wa.me/?text=${encodeURIComponent(waMsg)}`;
            document.getElementById('app-start-consultation-link').href = waUrl;
        })
        .catch(err => {
            btn.disabled = false;
            btn.textContent = 'التالي';
            alert(err.message || 'تعذّر إكمال الحجز. يرجى إعادة المحاولة.');
        });
}

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
