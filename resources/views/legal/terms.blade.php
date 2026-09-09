@php
    $isAr = $isAr ?? (app()->getLocale() === 'ar');
@endphp
@extends('layouts.app')

@section('title', ($isAr ? 'الشروط والأحكام وسياسة الاستخدام' : 'Terms & Conditions of Service') . ' - ' . \App\Models\Setting::get('doctor_name', 'يونس المرشد'))

@section('meta_description', $isAr ? 'تعرف على الشروط والأحكام وسياسة حجز وإلغاء المواعيد لموقع وتطبيق المعالج النفسي يونس المرشد.' : 'Terms of Service and Appointment Cancellation Policy for Therapist Yonis Al-Murshid Platform.')

@section('content')
<div class="legal-page-wrapper py-5" style="background: linear-gradient(180deg, #f8faff 0%, #ffffff 100%); min-height: 85vh;">
    <div class="container py-lg-4">
        
        {{-- Hero Header --}}
        <div class="text-center max-w-750 mx-auto mb-5">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill bg-primary bg-opacity-10 text-primary fw-bold small mb-3 border border-primary border-opacity-20 shadow-sm">
                <i class="bi bi-file-earmark-ruled-fill fs-6"></i>
                <span>{{ $isAr ? 'شروط الخدمة وسياسة الحجوزات الرسمية' : 'Official Terms & Booking Policies' }}</span>
            </div>
            <h1 class="fw-black text-dark mb-3 display-6" style="letter-spacing: -0.5px;">
                {{ $isAr ? 'الشروط والأحكام وسياسة الاستخدام' : 'Terms & Conditions of Service' }}
            </h1>
            <p class="text-secondary lead fs-6 mb-2">
                {{ $isAr ? 'تحدد هذه الاتفاقية شروط استخدام الموقع الإلكتروني وتطبيق الهاتف المحمول وآلية حجز وإلغاء المواعيد الاستشارية.' : 'This agreement governs your use of the website and mobile app, including booking, payment, and cancellation policies.' }}
            </p>
            <div class="text-muted small d-flex align-items-center justify-content-center gap-3 mt-2">
                <span><i class="bi bi-calendar-check me-1 text-primary"></i> {{ $isAr ? 'آخر تحديث: ' . date('Y/m/d') : 'Last Updated: ' . date('F Y') }}</span>
                <span>•</span>
                <span><i class="bi bi-patch-check-fill me-1 text-success"></i> {{ $isAr ? 'سارية على جميع المراجعين' : 'Applies to all clients' }}</span>
            </div>
        </div>

        {{-- Main Content Container --}}
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white position-relative overflow-hidden">
                    
                    {{-- Decorative Top Line --}}
                    <div class="position-absolute top-0 start-0 w-100" style="height: 5px; background: linear-gradient(90deg, #3B52A4, #0284c7, #3B52A4);"></div>

                    {{-- Custom Admin Content if provided --}}
                    @php
                        $customTerms = \App\Models\Setting::get('terms_conditions_content', '');
                    @endphp

                    @if(!empty($customTerms))
                        <div class="legal-custom-body mb-4">
                            {!! nl2br(e($customTerms)) !!}
                        </div>
                    @else
                        {{-- Structured Default Legal Policy --}}
                        <div class="legal-content">

                            {{-- 1. Acceptance of Terms --}}
                            <div class="legal-section mb-5">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="legal-icon-box bg-primary bg-opacity-10 text-primary rounded-3 p-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                        <i class="bi bi-check2-square fs-5"></i>
                                    </div>
                                    <h3 class="fw-bold text-dark h5 mb-0">{{ $isAr ? '1. الموافقة على الشروط' : '1. Acceptance of Terms' }}</h3>
                                </div>
                                <p class="text-secondary leading-relaxed mb-0">
                                    {{ $isAr 
                                        ? 'باستخدامك للموقع أو تسجيل الدخول إلى تطبيق الهاتف المحمول أو حجز أي جلسة استشارية، فإنك تقر وتوافق على الالتزام الكامل بهذه الشروط والأحكام وسياسة الخصوصية المرتبطة بها.' 
                                        : 'By accessing the website, logging into our mobile app, or booking any consultation session, you agree to be bound by these Terms & Conditions and the accompanying Privacy Policy.' }}
                                </p>
                            </div>

                            {{-- 2. Consultation Nature & Scope --}}
                            <div class="legal-section mb-5">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="legal-icon-box bg-info bg-opacity-10 text-info rounded-3 p-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                        <i class="bi bi-chat-heart-fill fs-5"></i>
                                    </div>
                                    <h3 class="fw-bold text-dark h5 mb-0">{{ $isAr ? '2. طبيعة الاستشارات والجلسات' : '2. Consultation Services' }}</h3>
                                </div>
                                <p class="text-secondary leading-relaxed mb-0">
                                    {{ $isAr 
                                        ? 'يقدم المعالج النفسي يونس المرشد جلسات إرشاد وتوجيه وعلاج نفسي معرفي سلوكي عبر الإنترنت (أونلاين عبر الصوت أو الفيديو أو المحادثة) أو حضورياً في العيادة. هذه الخدمات لا تغني عن الطوارئ الطبية العاجلة أو الحالات الإسعافية الحادة.' 
                                        : 'Therapist Yonis Al-Murshid provides psychological counseling and CBT sessions online (audio, video, or chat) or in-clinic. These services are not a substitute for emergency psychiatric crisis intervention.' }}
                                </p>
                            </div>

                            {{-- 3. Booking & Local Payment Flow --}}
                            <div class="legal-section mb-5 p-4 rounded-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="legal-icon-box bg-primary text-white rounded-3 p-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                        <i class="bi bi-wallet2 fs-5"></i>
                                    </div>
                                    <div>
                                        <h3 class="fw-bold text-dark h5 mb-0">{{ $isAr ? '3. آلية الحجز والتحويل المحلي' : '3. Booking & Local Payment Procedures' }}</h3>
                                        <span class="badge bg-primary bg-opacity-10 text-primary small mt-1">{{ $isAr ? 'تأكيد الموعد بعد مراجعة الإشعار' : 'Confirmed Upon Receipt Review' }}</span>
                                    </div>
                                </div>
                                <ul class="text-secondary small mb-0 ps-3">
                                    <li class="mb-2"><strong>{{ $isAr ? 'التحويل المحلي المباشر:' : 'Direct Local Transfers:' }}</strong> {{ $isAr ? 'يتم سداد رسوم الاستشارة عبر محافظ الدفع المحلية المعتمدة (زين كاش، سوبر كي، أو تحويل مالي محلي) وفق البيانات الظاهرة عند إتمام الحجز.' : 'Consultation fees are paid via local wallets (ZainCash, SuperKi, or local transfers) as specified during checkout.' }}</li>
                                    <li class="mb-2"><strong>{{ $isAr ? 'تأكيد الحجز:' : 'Confirmation:' }}</strong> {{ $isAr ? 'يعتبر الحجز مؤكداً ونهائياً بمجرد إرفاق إشعار التحويل وتأكيده من قبل إدارة العيادة، ويتم إرسال تذكرة الموعد وتفاصيل الرابط تلقائياً.' : 'Bookings are finalized once transfer confirmation is verified by the clinic, after which session details and links are automatically sent.' }}</li>
                                    <li><strong>{{ $isAr ? 'الالتزام بالموعد المحدد:' : 'Punctuality:' }}</strong> {{ $isAr ? 'يرجى التواجد في الموعد المحدد للجلسة بدقة لضمان الاستفادة الكاملة من الوقت المخصص.' : 'Clients are requested to attend promptly at the scheduled appointment time.' }}</li>
                                </ul>
                            </div>

                            {{-- 4. Cancellation & Rescheduling Policy --}}
                            <div class="legal-section mb-5">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="legal-icon-box bg-warning bg-opacity-10 text-warning rounded-3 p-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                        <i class="bi bi-arrow-repeat fs-5"></i>
                                    </div>
                                    <h3 class="fw-bold text-dark h5 mb-0">{{ $isAr ? '4. سياسة الإلغاء وإعادة الجدولة' : '4. Cancellation & Rescheduling Policy' }}</h3>
                                </div>
                                @php
                                    $minNotice = \App\Models\Setting::get('min_reschedule_notice_hours', '24');
                                    $maxResched = \App\Models\Setting::get('max_reschedule_allowed', '2');
                                @endphp
                                <p class="text-secondary leading-relaxed mb-2">
                                    {{ $isAr 
                                        ? "يمكن للمراجع طلب إعادة جدولة الموعد إلى وقت آخر متاح قبل موعد الجلسة بما لا يقل عن ($minNotice ساعة)، ويحق للمراجع إعادة الجدولة بحد أقصى ($maxResched مرات) لكل حجز لضمان تنظيم جداول العيادة." 
                                        : "Clients may request appointment rescheduling at least ($minNotice hours) before session start time, up to a maximum of ($maxResched times) per booking." }}
                                </p>
                                <p class="text-muted small mb-0">
                                    {{ $isAr 
                                        ? 'في حال عدم الحضور دون إشعار مسبق أو الإلغاء في اللحظات الأخيرة، قد يتعذر استرداد الرسوم لتعويض حجز الموعد المخصص.' 
                                        : 'No-shows without prior notice or last-minute cancellations may forfeit session fees.' }}
                                </p>
                            </div>

                            {{-- 5. User Conduct & Code of Ethics --}}
                            <div class="legal-section mb-5">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="legal-icon-box bg-danger bg-opacity-10 text-danger rounded-3 p-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                        <i class="bi bi-shield-exclamation fs-5"></i>
                                    </div>
                                    <h3 class="fw-bold text-dark h5 mb-0">{{ $isAr ? '5. قواعد الاستخدام والسلوك المهني' : '5. Code of Conduct & Intellectual Property' }}</h3>
                                </div>
                                <p class="text-secondary leading-relaxed mb-0">
                                    {{ $isAr 
                                        ? 'يمنع منعاً باتاً تسجيل جلسات الاستشارة الصوتية أو المرئية دون إذن خطي مسبق، كما يمنع استخدام محتوى الموقع ومقاطع الفيديو التوعوية لأغراض تجارية دون موافقة رسمية من المعالج.' 
                                        : 'Recording consultation sessions without prior written consent is strictly prohibited. All materials, videos, and platform content are protected by copyright.' }}
                                </p>
                            </div>

                            {{-- 6. Contact Us --}}
                            <div class="legal-section p-4 bg-light rounded-4 border">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="legal-icon-box bg-dark text-white rounded-3 p-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                        <i class="bi bi-headset fs-5"></i>
                                    </div>
                                    <h3 class="fw-bold text-dark h5 mb-0">{{ $isAr ? '6. خدمة المراجعين والدعم الفني' : '6. Client Support & Inquiries' }}</h3>
                                </div>
                                <p class="text-secondary small mb-3">
                                    {{ $isAr 
                                        ? 'فريق الدعم وإدارة العيادة متواجدون لمساعدتك في أي استفسار يتعلق بحجزك أو شروط الخدمة:' 
                                        : 'Our clinic management team is available to assist you with any questions regarding bookings or terms of service:' }}
                                </p>
                                <div class="d-flex flex-wrap gap-3">
                                    @php
                                        $waNum = preg_replace('/[^0-9]/', '', \App\Models\Setting::get('whatsapp_number', '+9647700000000'));
                                        $contactEmail = \App\Models\Setting::get('notification_email', 'dr.yonis@example.com');
                                    @endphp
                                    <a href="https://wa.me/{{ $waNum }}" target="_blank" class="btn btn-outline-success btn-sm rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center gap-2">
                                        <i class="bi bi-whatsapp"></i>
                                        <span>{{ $isAr ? 'دعم الواتساب' : 'WhatsApp Support' }}</span>
                                    </a>
                                    <a href="mailto:{{ $contactEmail }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center gap-2">
                                        <i class="bi bi-envelope"></i>
                                        <span>{{ $contactEmail }}</span>
                                    </a>
                                </div>
                            </div>

                        </div>
                    @endif

                </div>
            </div>
        </div>

    </div>
</div>
@endsection
