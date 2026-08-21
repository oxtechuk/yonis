@extends('layouts.app')

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
                    @if($profile && $profile->gallery && count($profile->gallery) > 0)
                        <img src="{{ $profile->gallery[0] }}" alt="المعالج النفسي يونس المرشد" loading="eager">
                    @else
                        <img src="https://images.unsplash.com/photo-1614797136987-ab4b98843e29?auto=format&fit=crop&w=700&q=80" alt="المعالج النفسي يونس المرشد" loading="eager">
                    @endif
                    <div class="hero-photo-overlay"></div>
                    <div class="hero-availability-badge">
                        <i class="bi bi-circle-fill text-success me-1" style="font-size:0.6rem;"></i> متاح للجلسات
                    </div>
                    <div class="hero-photo-caption">
                        <div class="fw-bold fs-6">{{ $doctorName }}</div>
                        <div style="font-size:0.8rem; opacity:0.85;">{{ $profile->title ?? 'معالج نفسي ومدرب معتمد' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     2. ABOUT SECTION
═══════════════════════════════════════════════════════════ --}}
<section id="about" class="about-wrapper">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-label"><i class="bi bi-person-badge"></i> عن المعالج</div>
            <h2 class="section-title">المعالج النفسي<br>يونس المرشد</h2>
        </div>

        <div class="about-card">
            <div class="row gy-4 align-items-start">
                <div class="col-lg-4 text-center">
                    @if($profile && $profile->gallery && count($profile->gallery) > 0)
                        <img src="{{ $profile->gallery[0] }}" alt="د. يونس المرشد" class="about-doctor-img mb-3">
                    @else
                        <div class="about-doctor-img d-flex align-items-center justify-content-center mx-auto mb-3" style="background: linear-gradient(135deg, var(--primary-color), #5b72c7); color:#fff; font-size:3rem;">Ψ</div>
                    @endif
                    <h4 class="fw-bold text-dark mb-1">{{ $doctorName }}</h4>
                    <p class="text-secondary small">{{ $profile->title ?? 'معالج نفسي ومدرب معتمد' }}</p>
                    <ul class="credentials-list text-start mt-3">
                        @if($profile && $profile->education)
                            @foreach(array_slice($profile->education, 0, 3) as $edu)
                                <li><i class="bi bi-mortarboard-fill"></i><span class="small">{{ $edu }}</span></li>
                            @endforeach
                        @else
                            <li><i class="bi bi-mortarboard-fill"></i><span class="small">بكالوريوس علم النفس الإكلينيكي</span></li>
                            <li><i class="bi bi-award-fill"></i><span class="small">شهادة العلاج المعرفي السلوكي CBT</span></li>
                            <li><i class="bi bi-patch-check-fill"></i><span class="small">معتمد في العلاج الأسري والزوجي</span></li>
                        @endif
                    </ul>
                </div>

                <div class="col-lg-8">
                    <p class="fs-5 text-secondary lh-lg mb-4">
                        {{ $profile->bio ?? 'معالج نفسي مرخص بخبرة تزيد عن 10 سنوات في تقديم الاستشارات النفسية الفردية والزوجية والأسرية. أعتمد على العلاج المعرفي السلوكي وأساليب الوعي التام لمساعدة الأفراد على تجاوز صعوباتهم النفسية.' }}
                    </p>

                    <h5 class="fw-bold mb-3" style="color: var(--primary-color);">مجالات التخصص:</h5>
                    <div class="d-flex flex-wrap gap-2">
                        @php
                            $specialties = $profile->specialties ?? ['اضطرابات القلق والتوتر', 'الاكتئاب وضغوط الحياة', 'الاستشارات الزوجية', 'العلاج الأسري', 'نقص الانتباه ADHD', 'الصدمات النفسية', 'الإدمان', 'التطوير الذاتي'];
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
<section id="services" class="services-wrapper">
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
                         onclick="selectServiceAndScroll({{ $service->id }}, '{{ $service->title }}', {{ $service->price }}, {{ $service->duration }})">
                        @if($index === 1)
                            <span class="popular-badge">⭐ الأكثر طلباً</span>
                        @endif

                        <div class="text-center mb-3">
                            <div style="font-size: 2.5rem;">
                                @if($index == 0) 💬 @elseif($index == 1) 📹 @else 🏥 @endif
                            </div>
                            <h4 class="fw-bold mt-2 mb-1">{{ $service->title }}</h4>
                            <p class="text-secondary small">{{ $service->description }}</p>
                            <span class="badge bg-light text-dark border fw-bold"><i class="bi bi-clock me-1"></i> {{ $service->duration }} دقيقة</span>
                        </div>

                        <div class="channel-prices">
                            <div class="channel-price-item">
                                <span>🏥 عيادة</span>
                                <span class="price">${{ number_format($service->clinic_price ?? $service->price, 0) }}</span>
                            </div>
                            <div class="channel-price-item">
                                <span>💬 شات</span>
                                <span class="price">${{ number_format($service->chat_price ?? $service->price, 0) }}</span>
                            </div>
                            <div class="channel-price-item">
                                <span>📞 صوت</span>
                                <span class="price">${{ number_format($service->voice_price ?? $service->price, 0) }}</span>
                            </div>
                            <div class="channel-price-item">
                                <span>📹 فيديو</span>
                                <span class="price">${{ number_format($service->video_price ?? $service->price, 0) }}</span>
                            </div>
                        </div>

                        <button class="btn btn-royal-outline w-100 mt-3 rounded-pill">
                            احجز هذه الجلسة الآن
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
<section id="reels-section" class="reels-wrapper">
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
<section class="testimonials-wrapper">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-label"><i class="bi bi-chat-quote-fill"></i> آراء المراجعين</div>
            <h2 class="section-title">تجارب حقيقية من مراجعينا</h2>
        </div>

        <div class="row g-4">
            @php
                $testimonials = [
                    ['text' => 'خدمة متميزة جداً، بعد الجلسة الثانية مع أستاذ يونس شعرت بفرق كبير واختفت نوبات التوتر والقلق تماماً. أنصح كل شخص يعاني.', 'name' => 'مراجع من بغداد', 'session' => 'جلسة أونلاين فيديو', 'icon' => '🧠'],
                    ['text' => 'شكراً جزيلاً دكتور يونس، التعامل أحدث تحولاً كبيراً في علاقتي الزوجية. المعالجة احترافية جداً وبيئة آمنة ودون أي أحكام.', 'name' => 'مراجعة من الأردن', 'session' => 'استشارة زوجية', 'icon' => '💑'],
                    ['text' => 'أفضل تجربة علاج نفسي مررت بها. الجلسات عبر الشات مريحة جداً وتناسب وقتي. أنصح كل شخص يعاني من ضغوط العمل بالحجز فوراً.', 'name' => 'مراجع من أربيل', 'session' => 'جلسة شات 30 دقيقة', 'icon' => '💼'],
                ];
            @endphp
            @foreach($testimonials as $t)
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="stars mb-3">★★★★★</div>
                        <p class="text-secondary lh-lg mb-4 flex-grow-1">"{{ $t['text'] }}"</p>
                        <div class="d-flex align-items-center gap-3 mt-auto">
                            <div class="testimonial-avatar">{{ $t['icon'] }}</div>
                            <div>
                                <div class="fw-bold text-dark">{{ $t['name'] }}</div>
                                <div class="text-secondary small">{{ $t['session'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     6. BOOKING WIZARD (2 Steps)
═══════════════════════════════════════════════════════════ --}}
<section id="booking-wizard" class="booking-wrapper">
    <div class="container">
        <div class="text-center mb-4">
            <div class="section-label"><i class="bi bi-calendar-check-fill"></i> احجز موعدك</div>
            <h2 class="section-title">جلسة فورية - حجز آمن في دقيقتين</h2>
        </div>

        <div class="wizard-card">
            {{-- Wizard Header --}}
            <div class="wizard-header">
                <h4 class="fw-bold mb-1 text-white" id="wizard-header-title">احجز استشارتك الآن</h4>
                <p class="mb-0" style="color:rgba(255,255,255,0.75); font-size:0.9rem;" id="wizard-header-sub">ادخل بياناتك واختر موعدك المناسب</p>

                {{-- Step Bar --}}
                <div class="wizard-step-bar">
                    <div class="wizard-step-item">
                        <div class="wizard-step-circle active" id="step-circle-1">1</div>
                        <span class="wizard-step-label active" id="step-label-1">البيانات والموعد</span>
                    </div>
                    <div class="wizard-step-line" id="step-line-1"></div>
                    <div class="wizard-step-item">
                        <div class="wizard-step-circle" id="step-circle-2">2</div>
                        <span class="wizard-step-label" id="step-label-2">مراجعة ودفع</span>
                    </div>
                    <div class="wizard-step-line" id="step-line-2"></div>
                    <div class="wizard-step-item">
                        <div class="wizard-step-circle" id="step-circle-3">✓</div>
                        <span class="wizard-step-label" id="step-label-3">تأكيد الحجز</span>
                    </div>
                </div>
            </div>

            {{-- Wizard Body --}}
            <div class="wizard-body">

                {{-- ─── STEP 1: Details & Slot ─── --}}
                <div id="wizard-step-1">

                    {{-- Channel Selection --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark mb-2">1. اختر قناة الاستشارة:</label>
                        <div class="channel-select-grid">
                            <div class="channel-option" onclick="selectChannel('clinic', this)">
                                <div class="channel-icon">🏥</div>
                                <div class="channel-name">في العيادة</div>
                                <div class="channel-price" id="price-clinic">يُحدد بعد الخدمة</div>
                            </div>
                            <div class="channel-option selected" onclick="selectChannel('video', this)">
                                <div class="channel-icon">📹</div>
                                <div class="channel-name">فيديو أونلاين</div>
                                <div class="channel-price" id="price-video">يُحدد بعد الخدمة</div>
                            </div>
                            <div class="channel-option" onclick="selectChannel('voice', this)">
                                <div class="channel-icon">📞</div>
                                <div class="channel-name">مكالمة صوتية</div>
                                <div class="channel-price" id="price-voice">يُحدد بعد الخدمة</div>
                            </div>
                            <div class="channel-option" onclick="selectChannel('chat', this)">
                                <div class="channel-icon">💬</div>
                                <div class="channel-name">محادثة شات</div>
                                <div class="channel-price" id="price-chat">يُحدد بعد الخدمة</div>
                            </div>
                        </div>
                        <input type="hidden" id="selected_channel" value="video">
                        <input type="hidden" id="selected_booking_type" value="online">
                    </div>

                    {{-- Service Selection --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark mb-2">2. اختر مدة الجلسة:</label>
                        <div class="service-btns" id="service-btns-container">
                            @foreach($services as $s)
                                <button type="button"
                                    class="service-btn {{ $loop->first ? 'selected' : '' }}"
                                    onclick="selectServiceWizard(this, {{ $s->id }}, {{ $s->duration }}, '{{ $s->title }}', {{ $s->price }}, {{ $s->clinic_price ?? $s->price }}, {{ $s->chat_price ?? $s->price }}, {{ $s->voice_price ?? $s->price }}, {{ $s->video_price ?? $s->price }})"
                                    data-service-id="{{ $s->id }}"
                                    data-price="{{ $s->price }}"
                                    data-clinic="{{ $s->clinic_price ?? $s->price }}"
                                    data-chat="{{ $s->chat_price ?? $s->price }}"
                                    data-voice="{{ $s->voice_price ?? $s->price }}"
                                    data-video="{{ $s->video_price ?? $s->price }}">
                                    {{ $s->duration }} دقيقة<br>
                                    <small class="opacity-75">${{ number_format($s->video_price ?? $s->price, 0) }}</small>
                                </button>
                            @endforeach
                        </div>
                        <input type="hidden" id="selected_service_id" value="{{ $services->first()->id ?? 1 }}">
                        <input type="hidden" id="selected_service_title" value="{{ $services->first()->title ?? '' }}">
                        <input type="hidden" id="selected_price" value="{{ $services->first()->price ?? 0 }}">
                    </div>

                    {{-- Date Selection --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark mb-2">3. اختر تاريخ الموعد:</label>
                        <input type="date" id="booking_date_input" class="form-control form-control-lg rounded-3"
                               value="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               min="{{ date('Y-m-d') }}"
                               onchange="fetchSlots()">
                    </div>

                    {{-- Slots --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark mb-2">4. اختر الوقت المتاح:</label>
                        <div class="slots-grid" id="slots-grid">
                            <div class="col-span-3 text-center text-muted py-3">
                                <div class="spinner-border spinner-border-sm me-1"></div> جاري تحميل الأوقات...
                            </div>
                        </div>
                        <input type="hidden" id="selected_slot" value="">
                    </div>

                    {{-- Patient Info --}}
                    <div class="mb-4 p-4 rounded-4" style="background:#f0f4fb; border:1px solid rgba(59,82,164,0.12);">
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-person-fill me-1" style="color:var(--primary-color)"></i> 5. بياناتك الشخصية</h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-bold">الاسم الكامل <span class="text-danger">*</span></label>
                                <input type="text" id="guest_name" class="form-control rounded-3" placeholder="أدخل اسمك بالكامل" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">رقم الواتساب <span class="text-danger">*</span></label>
                                <input type="tel" id="guest_phone" class="form-control rounded-3" placeholder="+964xxxxxxxxx" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">كلمة المرور <span class="text-danger">*</span></label>
                                <input type="password" id="guest_password" class="form-control rounded-3" placeholder="6 أحرف على الأقل" required minlength="6">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">عنوان الاستشارة</label>
                                <input type="text" id="consultation_title" class="form-control rounded-3" placeholder="موضوع مختصر للاستشارة">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">تفاصيل إضافية (اختياري)</label>
                                <textarea id="consultation_notes" class="form-control rounded-3" rows="2" placeholder="أي تفاصيل إضافية..."></textarea>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-royal-primary w-100 py-3 fs-5" onclick="goToStep2()">
                        التالي: مراجعة وتأكيد الدفع <i class="bi bi-arrow-left ms-2"></i>
                    </button>
                </div>

                {{-- ─── STEP 2: Summary + Payment ─── --}}
                <div id="wizard-step-2" class="d-none">
                    <h5 class="fw-bold mb-3 text-dark">ملخص الحجز:</h5>
                    <div class="summary-card mb-4">
                        <div class="summary-row">
                            <span class="text-muted">الخدمة</span>
                            <span class="fw-bold" id="s-service">—</span>
                        </div>
                        <div class="summary-row">
                            <span class="text-muted">نوع الاستشارة</span>
                            <span class="fw-bold" id="s-channel">—</span>
                        </div>
                        <div class="summary-row">
                            <span class="text-muted">التاريخ</span>
                            <span class="fw-bold" id="s-date">—</span>
                        </div>
                        <div class="summary-row">
                            <span class="text-muted">الوقت</span>
                            <span class="fw-bold" id="s-time">—</span>
                        </div>
                        <div class="summary-row">
                            <span class="text-muted">الاسم</span>
                            <span class="fw-bold" id="s-name">—</span>
                        </div>
                        <div class="summary-row border-top pt-2 mt-1">
                            <span class="fw-bold fs-5">المبلغ الإجمالي</span>
                            <span class="summary-total" id="s-price">—</span>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-3 text-dark">طريقة الدفع:</h5>
                    <label class="payment-method selected" id="pm-card" onclick="selectPayment('stripe_card', this)">
                        <input type="radio" name="payment_method" value="stripe_card" checked style="display:none;">
                        <i class="bi bi-credit-card-2-front fs-4 text-primary"></i>
                        <div>
                            <div class="fw-bold">بطاقة ائتمانية</div>
                            <div class="text-secondary small">VISA / Mastercard / مدى عبر Stripe</div>
                        </div>
                    </label>
                    <label class="payment-method" id="pm-apple" onclick="selectPayment('apple_pay', this)">
                        <input type="radio" name="payment_method" value="apple_pay" style="display:none;">
                        <i class="bi bi-apple fs-4"></i>
                        <div>
                            <div class="fw-bold">Apple Pay</div>
                            <div class="text-secondary small">ادفع بلمسة واحدة</div>
                        </div>
                    </label>

                    <div class="d-flex gap-3 mt-4">
                        <button type="button" class="btn btn-outline-secondary px-4 py-3 rounded-pill" onclick="goBackToStep1()">
                            <i class="bi bi-arrow-right me-1"></i> تعديل
                        </button>
                        <button type="button" class="btn btn-royal-primary flex-grow-1 py-3 fs-5" id="pay-btn" onclick="submitBooking()">
                            <i class="bi bi-shield-lock-fill me-2"></i> الدفع وتأكيد الحجز
                        </button>
                    </div>
                </div>

                {{-- ─── SUCCESS SCREEN ─── --}}
                <div id="wizard-success" class="d-none text-center py-3">
                    <div class="success-circle">✓</div>
                    <h3 class="fw-black text-dark mb-2">تم تأكيد حجزك بنجاح! 🎉</h3>
                    <p class="text-secondary mb-4">تم إنشاء حسابك وحجز موعدك. اضغط على زر واتساب لتصلك تفاصيل الموعد.</p>

                    <div class="summary-card text-start mb-4">
                        <div class="summary-row">
                            <span class="text-muted">رقم المرجع</span>
                            <span class="fw-bold text-dark" id="res-ref">—</span>
                        </div>
                        <div class="summary-row">
                            <span class="text-muted">الخدمة</span>
                            <span class="fw-bold" id="res-service">—</span>
                        </div>
                        <div class="summary-row">
                            <span class="text-muted">الموعد</span>
                            <span class="fw-bold" id="res-datetime">—</span>
                        </div>
                        <div class="summary-row">
                            <span class="text-muted">نوع الاستشارة</span>
                            <span class="fw-bold" id="res-channel">—</span>
                        </div>
                        <div class="summary-row">
                            <span class="text-muted">المبلغ</span>
                            <span class="fw-bold" id="res-price">—</span>
                        </div>
                    </div>

                    <a id="whatsapp-confirm-link" href="#" target="_blank" class="whatsapp-btn-cta d-inline-flex w-100 justify-content-center mb-3">
                        <i class="bi bi-whatsapp"></i> افتح واتساب - تفاصيل حجزك
                    </a>
                    <div class="d-flex gap-2">
                        <a href="{{ route('patient.dashboard') }}" class="btn btn-royal-outline flex-grow-1 py-2">
                            <i class="bi bi-person me-1"></i> حسابي وجلساتي
                        </a>
                        <button type="button" class="btn btn-outline-secondary px-4 py-2" onclick="location.reload()">حجز جديد</button>
                    </div>
                </div>

            </div>{{-- end wizard-body --}}
        </div>{{-- end wizard-card --}}
    </div>
</section>

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

    fetch(`/api/slots?service_id=${state.serviceId}&date=${date}`)
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

    fetch('/api/checkout/initialize', { method: 'POST', headers, body: JSON.stringify(payload) })
        .then(r => r.json())
        .then(data => {
            if (!data.success) throw new Error(data.message || 'حدث خطأ في الطلب.');
            return fetch('/api/checkout/confirm', {
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

    // Build WhatsApp message
    const msg = `السلام عليكم ورحمة الله 🌿

تم تأكيد حجزك بنجاح ✅

📋 *تفاصيل موعدك:*
━━━━━━━━━━━━━━━━━━━━━━
🆔 رقم المرجع: #${booking.booking_reference}
👤 الاسم: ${state.name}
🩺 الخدمة: ${state.serviceTitle}
📞 القناة: ${channelLabels[state.channel]}
📅 التاريخ: ${state.date}
⏰ الوقت: ${state.slot}
💰 المبلغ: $${state.price}
━━━━━━━━━━━━━━━━━━━━━━

يسعدنا خدمتك 🤍
المعالج النفسي يونس المرشد`;

    const waUrl = waNumber
        ? `https://wa.me/${waNumber}?text=${encodeURIComponent(msg)}`
        : `https://wa.me/?text=${encodeURIComponent(msg)}`;

    document.getElementById('whatsapp-confirm-link').href = waUrl;

    // Transition to success
    document.getElementById('wizard-step-2').classList.add('d-none');
    document.getElementById('wizard-success').classList.remove('d-none');

    document.getElementById('step-circle-2').classList.remove('active'); document.getElementById('step-circle-2').classList.add('done'); document.getElementById('step-circle-2').textContent = '✓';
    document.getElementById('step-circle-3').classList.add('active');
    document.getElementById('step-line-2').classList.add('done');
    document.getElementById('step-label-2').classList.remove('active');
    document.getElementById('step-label-3').classList.add('active');

    document.getElementById('wizard-header-title').textContent = '🎉 تم التأكيد!';
    document.getElementById('wizard-header-sub').textContent = 'تم تأكيد حجزك وإنشاء حسابك بنجاح';

    // Auto-open WhatsApp after 1.5s
    setTimeout(() => { if (waNumber) window.open(waUrl, '_blank'); }, 1500);

    window.scrollTo({ top: document.getElementById('booking-wizard').offsetTop - 80, behavior: 'smooth' });
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

// ═══ Init ═══
document.addEventListener('DOMContentLoaded', function() {
    updateCurrentPrice();
    fetchSlots();
});
</script>
@endsection
