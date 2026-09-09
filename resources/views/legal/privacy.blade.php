@php
    $isAr = $isAr ?? (app()->getLocale() === 'ar');
@endphp
@extends('layouts.app')

@section('title', ($isAr ? 'سياسة الخصوصية وحماية البيانات' : 'Privacy & Data Protection Policy') . ' - ' . \App\Models\Setting::get('doctor_name', 'يونس المرشد'))

@section('meta_description', $isAr ? 'تعرف على سياسة الخصوصية وحماية البيانات الطبية والشخصية لموقع وتطبيق المعالج النفسي يونس المرشد. التزام تام بالسرية وأمان المعاملات.' : 'Learn about the Privacy & Data Protection Policy for the website and mobile app of Therapist Yonis Al-Murshid.')

@section('content')
<div class="legal-page-wrapper py-5" style="background: linear-gradient(180deg, #f8faff 0%, #ffffff 100%); min-height: 85vh;">
    <div class="container py-lg-4">
        
        {{-- Hero Header --}}
        <div class="text-center max-w-750 mx-auto mb-5">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill bg-primary bg-opacity-10 text-primary fw-bold small mb-3 border border-primary border-opacity-20 shadow-sm">
                <i class="bi bi-shield-check-fill fs-6"></i>
                <span>{{ $isAr ? 'وثيقة قانونية معتمدة ومتوافقة مع معايير المتاجر' : 'Official Legal Policy & Store Compliant' }}</span>
            </div>
            <h1 class="fw-black text-dark mb-3 display-6" style="letter-spacing: -0.5px;">
                {{ $isAr ? 'سياسة الخصوصية وحماية البيانات' : 'Privacy & Data Protection Policy' }}
            </h1>
            <p class="text-secondary lead fs-6 mb-2">
                {{ $isAr ? 'تسري هذه السياسة على الموقع الإلكتروني وتطبيق الهاتف المحمول (Android & iOS) الخاص بعيادة المعالج النفسي يونس المرشد.' : 'This policy applies to the official website and mobile applications (Android & iOS) of Therapist Yonis Al-Murshid Clinic.' }}
            </p>
            <div class="text-muted small d-flex align-items-center justify-content-center gap-3 mt-2">
                <span><i class="bi bi-calendar-check me-1 text-primary"></i> {{ $isAr ? 'آخر تحديث: ' . date('Y/m/d') : 'Last Updated: ' . date('F Y') }}</span>
                <span>•</span>
                <span><i class="bi bi-globe2 me-1 text-success"></i> {{ $isAr ? 'الموقع والتطبيق' : 'Website & Mobile App' }}</span>
            </div>
        </div>

        {{-- Main Content Container --}}
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white position-relative overflow-hidden">
                    
                    {{-- Decorative Top Line --}}
                    <div class="position-absolute top-0 start-0 w-100" style="height: 5px; background: linear-gradient(90deg, #3B52A4, #22c55e, #3B52A4);"></div>

                    {{-- Custom Admin Content if provided --}}
                    @php
                        $customPrivacy = \App\Models\Setting::get('privacy_policy_content', '');
                    @endphp

                    @if(!empty($customPrivacy))
                        <div class="legal-custom-body mb-4">
                            {!! nl2br(e($customPrivacy)) !!}
                        </div>
                    @else
                        {{-- Structured Default Legal Policy --}}
                        <div class="legal-content">

                            {{-- 1. Introduction --}}
                            <div class="legal-section mb-5">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="legal-icon-box bg-primary bg-opacity-10 text-primary rounded-3 p-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                        <i class="bi bi-file-earmark-lock2-fill fs-5"></i>
                                    </div>
                                    <h3 class="fw-bold text-dark h5 mb-0">{{ $isAr ? '1. مقدمة ونطاق التطبيق' : '1. Introduction & Scope' }}</h3>
                                </div>
                                <p class="text-secondary leading-relaxed mb-0">
                                    {{ $isAr 
                                        ? 'نحن في منصة وتطبيق المعالج النفسي يونس المرشد نلتزم التزاماً مطلقاً بحماية خصوصيتك وسرية بياناتك الشخصية والصحية. توضح هذه السياسة كيف نجمع ونستخدم ونحمي المعلومات عند استخدامك لموقعنا الإلكتروني أو تطبيق الهاتف المحمول المتاح على متجري (Google Play و Apple App Store).' 
                                        : 'At Therapist Yonis Al-Murshid Platform and Mobile App, we are strictly committed to protecting your privacy and confidential data. This policy explains how we collect, use, and safeguard information when you use our website or mobile apps.' }}
                                </p>
                            </div>

                            {{-- 2. Data We Collect --}}
                            <div class="legal-section mb-5">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="legal-icon-box bg-info bg-opacity-10 text-info rounded-3 p-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                        <i class="bi bi-person-lines-fill fs-5"></i>
                                    </div>
                                    <h3 class="fw-bold text-dark h5 mb-0">{{ $isAr ? '2. البيانات التي نقوم بجمعها' : '2. Information We Collect' }}</h3>
                                </div>
                                <p class="text-secondary mb-3">
                                    {{ $isAr 
                                        ? 'نحن نجمع فقط الحد الأدنى الضروري من البيانات لتمكينك من حجز الجلسات الاستشارية والتواصل مع المعالج:' 
                                        : 'We only collect the minimal essential data required to facilitate booking consultations and connecting with the therapist:' }}
                                </p>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded-3 border h-100">
                                            <div class="d-flex align-items-center gap-2 mb-1 text-primary fw-bold">
                                                <i class="bi bi-person-circle"></i>
                                                <span>{{ $isAr ? 'الاسم الكامل' : 'Full Name' }}</span>
                                            </div>
                                            <p class="text-muted small mb-0">{{ $isAr ? 'للتعرف على صاحب الحجز وتوثيق ملف الاستشارة.' : 'To identify the appointment holder and consultation file.' }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded-3 border h-100">
                                            <div class="d-flex align-items-center gap-2 mb-1 text-success fw-bold">
                                                <i class="bi bi-telephone-fill"></i>
                                                <span>{{ $isAr ? 'رقم الهاتف / الواتساب' : 'Phone / WhatsApp' }}</span>
                                            </div>
                                            <p class="text-muted small mb-0">{{ $isAr ? 'لتأكيد الموعد وإرسال رابط الجلسة والتذكير بموعدها.' : 'For booking confirmations, session links, and reminders.' }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded-3 border h-100">
                                            <div class="d-flex align-items-center gap-2 mb-1 text-warning fw-bold">
                                                <i class="bi bi-envelope-fill"></i>
                                                <span>{{ $isAr ? 'البريد الإلكتروني' : 'Email Address' }}</span>
                                            </div>
                                            <p class="text-muted small mb-0">{{ $isAr ? 'لتسجيل الدخول وإرسال إشعارات الحجز وتفاصيل الفاتورة.' : 'For account authentication, invoice details, and receipts.' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- 3. Local Payment Processing Policy --}}
                            <div class="legal-section mb-5 p-4 rounded-4" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="legal-icon-box bg-success text-white rounded-3 p-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                        <i class="bi bi-wallet2 fs-5"></i>
                                    </div>
                                    <div>
                                        <h3 class="fw-bold text-success h5 mb-0">{{ $isAr ? '3. سياسة المعاملات المالية والدفع المحلي' : '3. Payment & Financial Transactions Policy' }}</h3>
                                        <span class="badge bg-success bg-opacity-25 text-success small mt-1">{{ $isAr ? 'أمان مالي 100% - تحويلات محلية مباشرة' : '100% Safe Local Transfers' }}</span>
                                    </div>
                                </div>
                                <p class="text-dark leading-relaxed mb-2">
                                    {{ $isAr 
                                        ? 'تعتمد المنصة والتطبيق آلية سداد آمنة ومباشرة تعتمد حصراً على التحويلات المحلية الرسمية (مثل محفظة زين كاش ZainCash، سوبر كي SuperKi، أو التحويلات المحلية المباشرة).' 
                                        : 'Our platform and app operate strictly via direct local transfer methods (such as ZainCash, SuperKi, or local peer-to-peer wallets).' }}
                                </p>
                                <ul class="text-secondary small mb-0 ps-3">
                                    <li class="mb-1"><strong>{{ $isAr ? 'عدم تخزين بيانات بنكية:' : 'No Financial Data Stored:' }}</strong> {{ $isAr ? 'نحن لا نطلب ولا نخزن ولا نعالج أي أرقام بطاقات ائتمانية أو أرقام حسابات بنكية سرية على خوادمنا نهائياً.' : 'We do not collect, process, or store credit card numbers, CVVs, or bank account credentials.' }}</li>
                                    <li><strong>{{ $isAr ? 'إثبات التحويل:' : 'Transfer Verification:' }}</strong> {{ $isAr ? 'يقوم العميل بالتحويل من تطبيقه الخاص بشكل مستقل ثم يرفع إشعار التحويل لتأكيد الحجز.' : 'Clients transfer funds securely via their independent payment apps and submit the transaction receipt for confirmation.' }}</li>
                                </ul>
                            </div>

                            {{-- 4. Medical & Psychological Confidentiality --}}
                            <div class="legal-section mb-5">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="legal-icon-box bg-purple bg-opacity-10 text-primary rounded-3 p-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background-color: #f3e8ff; color: #7e22ce !important;">
                                        <i class="bi bi-heart-pulse-fill fs-5"></i>
                                    </div>
                                    <h3 class="fw-bold text-dark h5 mb-0">{{ $isAr ? '4. السرية الطبية والمهنية التامة' : '4. Medical & Psychological Confidentiality' }}</h3>
                                </div>
                                <p class="text-secondary leading-relaxed mb-0">
                                    {{ $isAr 
                                        ? 'جميع المعلومات والاستشارات النفسية التي تدور بين المراجع والمعالج تخضع لأعلى درجات السرية المهنية والأخلاقية والطبية. لا يتم تسجيل الجلسات الصوتية أو المرئية أو مشاركة أي بيانات حساسة مع أي طرف ثالث أو معلن تحت أي ظرف.' 
                                        : 'All psychological consultations and clinical discussions between the patient and therapist are held under strict professional, ethical, and medical confidentiality. Sessions are never recorded, and sensitive data is never shared with third parties.' }}
                                </p>
                            </div>

                            {{-- 5. Data Security & Storage --}}
                            <div class="legal-section mb-5">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="legal-icon-box bg-warning bg-opacity-10 text-warning rounded-3 p-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                        <i class="bi bi-lock-fill fs-5"></i>
                                    </div>
                                    <h3 class="fw-bold text-dark h5 mb-0">{{ $isAr ? '5. أمان وحماية البيانات' : '5. Data Security & Storage' }}</h3>
                                </div>
                                <p class="text-secondary leading-relaxed mb-0">
                                    {{ $isAr 
                                        ? 'نستخدم بروتوكولات تشفير متقدمة (SSL/TLS 256-bit) لحماية البيانات المنقولة بين جهازك وخوادمنا، وتطبيق إجراءات حماية تقنية متطورة لمنع الوصول غير المصرح به أو التعديل أو الإفصاح عن البيانات.' 
                                        : 'We implement advanced encryption protocols (SSL/TLS 256-bit) to protect data transmitted between your device and our servers, applying modern security measures to prevent unauthorized access.' }}
                                </p>
                            </div>

                            {{-- 6. User Rights & Account Deletion (Google Play & App Store Compliance) --}}
                            <div class="legal-section mb-5">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="legal-icon-box bg-danger bg-opacity-10 text-danger rounded-3 p-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                        <i class="bi bi-trash3-fill fs-5"></i>
                                    </div>
                                    <h3 class="fw-bold text-dark h5 mb-0">{{ $isAr ? '6. حقوق المستخدم وحذف الحساب والبيانات' : '6. User Rights & Account/Data Deletion' }}</h3>
                                </div>
                                <p class="text-secondary leading-relaxed mb-2">
                                    {{ $isAr 
                                        ? 'يحق للمستخدم في أي وقت طلب الوصول إلى بياناته المسجلة أو تصحيحها أو طلب حذف حسابه وكافة بياناته الشخصية نهائياً من أنظمتنا وفقاً لمتطلبات متجر Google Play و Apple App Store.' 
                                        : 'Users have the right to request access, correction, or permanent deletion of their account and personal data from our systems at any time in compliance with Google Play and Apple App Store requirements.' }}
                                </p>
                                <p class="text-muted small mb-0">
                                    {{ $isAr 
                                        ? 'لطلب حذف البيانات أو إغلاق الحساب، يمكنك التواصل معنا مباشرة عبر البريد الإلكتروني أو الواتساب الموضحين أدناه.' 
                                        : 'To request account deletion or data removal, please contact us via email or WhatsApp below.' }}
                                </p>
                            </div>

                            {{-- 7. Contact Us --}}
                            <div class="legal-section p-4 bg-light rounded-4 border">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="legal-icon-box bg-dark text-white rounded-3 p-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                        <i class="bi bi-chat-dots-fill fs-5"></i>
                                    </div>
                                    <h3 class="fw-bold text-dark h5 mb-0">{{ $isAr ? '7. التواصل بشأن الخصوصية' : '7. Contact Us Regarding Privacy' }}</h3>
                                </div>
                                <p class="text-secondary small mb-3">
                                    {{ $isAr 
                                        ? 'إذا كانت لديك أي استفسارات أو ملاحظات بخصوص سياسة الخصوصية أو معالجة البيانات، يمكنك التواصل مع إدارة العيادة عبر:' 
                                        : 'If you have any questions or feedback regarding this Privacy Policy, please contact our clinic management:' }}
                                </p>
                                <div class="d-flex flex-wrap gap-3">
                                    @php
                                        $waNum = preg_replace('/[^0-9]/', '', \App\Models\Setting::get('whatsapp_number', '+9647700000000'));
                                        $contactEmail = \App\Models\Setting::get('notification_email', 'dr.yonis@example.com');
                                    @endphp
                                    <a href="https://wa.me/{{ $waNum }}" target="_blank" class="btn btn-outline-success btn-sm rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center gap-2">
                                        <i class="bi bi-whatsapp"></i>
                                        <span>{{ $isAr ? 'تواصل عبر الواتساب' : 'WhatsApp Support' }}</span>
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
