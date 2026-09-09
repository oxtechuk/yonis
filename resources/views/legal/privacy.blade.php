@php
    $isAr = $isAr ?? (app()->getLocale() === 'ar');
@endphp
@extends('layouts.app')

@section('title', ($isAr ? 'سياسة الخصوصية وحماية البيانات' : 'Privacy & Data Protection Policy') . ' - ' . \App\Models\Setting::get('doctor_name', 'يونس المرشد'))

@section('meta_description', $isAr ? 'سياسة الخصوصية وحماية البيانات الطبية والشخصية لموقع وتطبيق المعالج النفسي يونس المرشد. التزام تام بالسرية وأمان البيانات.' : 'Official Privacy & Data Protection Policy for the website and mobile application of Therapist Yonis Al-Murshid.')

@section('content')
<div class="legal-page-wrapper py-5" style="background-color: #f8fafc; min-height: 85vh;">
    <div class="container py-lg-4">
        
        {{-- Document Header --}}
        <div class="text-center max-w-750 mx-auto mb-5">
            <h1 class="fw-bold text-dark mb-3" style="font-size: 2.1rem; letter-spacing: -0.5px;">
                {{ $isAr ? 'سياسة الخصوصية وحماية البيانات' : 'Privacy & Data Protection Policy' }}
            </h1>
            <p class="text-secondary mb-2" style="font-size: 1.05rem; line-height: 1.7;">
                {{ $isAr 
                    ? 'وثيقة رسمية توضح التزامات عيادة المعالج النفسي يونس المرشد بحماية الخصوصية والسرية الطبية عبر الموقع والتطبيق.' 
                    : 'Official document outlining data protection and clinical confidentiality for Therapist Yonis Al-Murshid website and mobile app.' }}
            </p>
            <div class="text-muted small mt-2">
                <span>{{ $isAr ? 'تاريخ آخر تحديث: ' . date('Y/m/d') : 'Last Updated: ' . date('F d, Y') }}</span>
            </div>
        </div>

        {{-- Main Legal Document Container --}}
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <div class="card border rounded-4 p-4 p-md-5 bg-white shadow-xs">
                    
                    {{-- Custom Admin Override Content if provided --}}
                    @php
                        $customPrivacy = \App\Models\Setting::get('privacy_policy_content', '');
                    @endphp

                    @if(!empty($customPrivacy))
                        <div class="legal-custom-body" style="font-size: 1.05rem; line-height: 1.9; color: #334155;">
                            {!! nl2br(e($customPrivacy)) !!}
                        </div>
                    @else
                        {{-- Formal Legal Articles --}}
                        <div class="legal-document-body" style="font-size: 1.05rem; line-height: 1.9; color: #334155;">

                            {{-- Article 1 --}}
                            <div class="mb-5 pb-4 border-bottom">
                                <h2 class="fw-bold text-dark mb-3" style="font-size: 1.35rem;">
                                    {{ $isAr ? 'المادة الأولى: التمهيد ونطاق التطبيق' : 'Article 1: Introduction & Scope' }}
                                </h2>
                                <p class="mb-0">
                                    {{ $isAr 
                                        ? 'نلتزم في منصة وتطبيق المعالج النفسي يونس المرشد بأعلى المعايير المهنية والأخلاقية لحماية خصوصية مراجعينا وسرية سجلاتهم الطبية. تسري هذه السياسة بصورة شاملة على كافة الخدمات المقدمة عبر الموقع الإلكتروني وتطبيقات الهواتف الذكية المتاحة على متجري Google Play و Apple App Store.' 
                                        : 'Therapist Yonis Al-Murshid Platform is committed to the highest ethical and professional standards in protecting client privacy and confidential medical records. This policy applies comprehensively to all services provided via our website and mobile applications on Google Play and Apple App Store.' }}
                                </p>
                            </div>

                            {{-- Article 2 --}}
                            <div class="mb-5 pb-4 border-bottom">
                                <h2 class="fw-bold text-dark mb-3" style="font-size: 1.35rem;">
                                    {{ $isAr ? 'المادة الثانية: البيانات التي يتم جمعها والغرض منها' : 'Article 2: Information Collected & Purpose' }}
                                </h2>
                                <p class="mb-3">
                                    {{ $isAr 
                                        ? 'يقتصر جمع البيانات على القدر الضروري اللازم لإتمام إجراءات الحجز، والتواصل المهني، وتقديم الاستشارة بكفاءة:' 
                                        : 'Data collection is strictly limited to information necessary to complete bookings, communicate, and deliver professional consultations:' }}
                                </p>
                                <ul class="mb-0" style="padding-right: 1.5rem; padding-left: 1.5rem;">
                                    <li class="mb-2">
                                        <strong>{{ $isAr ? 'البيانات الشخصية الأساسية:' : 'Basic Personal Data:' }}</strong> 
                                        {{ $isAr ? 'الاسم ورقم الهاتف والبريد الإلكتروني، وذلك بغرض توثيق ملف الاستشارة وإرسال تذاكر المواعيد وروابط الجلسات.' : 'Name, phone number, and email address for appointment confirmation and session access.' }}
                                    </li>
                                    <li class="mb-2">
                                        <strong>{{ $isAr ? 'بيانات المواعيد والخدمات:' : 'Appointment Details:' }}</strong> 
                                        {{ $isAr ? 'نوع الخدمة المختارة، وتاريخ ووقت الجلسة، وقناة التواصل المطلوبة (عيادة، فيديو، مكالمة صوتية، أو محادثة).' : 'Selected service type, date, time, and preferred medium (in-clinic, video, voice, or chat).' }}
                                    </li>
                                    <li>
                                        <strong>{{ $isAr ? 'الملاحظات الاختيارية:' : 'Optional Notes:' }}</strong> 
                                        {{ $isAr ? 'أي معلومات يفضل المراجع تزويد المعالج بها مسبقاً لتوجيه مسار الجلسة بصورة أفضل.' : 'Any introductory notes the client chooses to share prior to the session.' }}
                                    </li>
                                </ul>
                            </div>

                            {{-- Article 3 --}}
                            <div class="mb-5 pb-4 border-bottom">
                                <h2 class="fw-bold text-dark mb-3" style="font-size: 1.35rem;">
                                    {{ $isAr ? 'المادة الثالثة: السرية الطبية والمهنية' : 'Article 3: Medical & Clinical Confidentiality' }}
                                </h2>
                                <p class="mb-2">
                                    {{ $isAr 
                                        ? 'تخضع كافة المداولات والاستشارات النفسية للسرية التامة وفقاً لمواثيق الشرف المهني للعلاج النفسي والسرية الطبية. ونؤكد على المبادئ التالية:' 
                                        : 'All psychological discussions and clinical sessions are subject to strict professional confidentiality under clinical standards:' }}
                                </p>
                                <ul class="mb-0" style="padding-right: 1.5rem; padding-left: 1.5rem;">
                                    <li class="mb-2">
                                        {{ $isAr ? 'لا يتم تسجيل أو تخزين أي جلسات فيديو أو مكالمات صوتية أو محادثات نصية على أي خوادم خارجية.' : 'No video calls, audio sessions, or chat consultations are recorded or stored on external servers.' }}
                                    </li>
                                    <li class="mb-2">
                                        {{ $isAr ? 'لا يتم بيع أو تأجير أو مشاركة أي بيانات شخصية أو صحية مع أي طرف ثالث أو جهات إعلانية تحت أي ظرف.' : 'Personal and health data is never sold, leased, or shared with third parties or advertisers under any circumstances.' }}
                                    </li>
                                    <li>
                                        {{ $isAr ? 'يقتصر الاطلاع على بيانات المراجع على المعالج المختص وإدارة المواعيد المباشرة فقط.' : 'Access to client information is strictly limited to the treating therapist and direct appointment administration.' }}
                                    </li>
                                </ul>
                            </div>

                            {{-- Article 4 --}}
                            <div class="mb-5 pb-4 border-bottom">
                                <h2 class="fw-bold text-dark mb-3" style="font-size: 1.35rem;">
                                    {{ $isAr ? 'المادة الرابعة: المعاملات المالية وسياسة الدفع' : 'Article 4: Payment & Financial Transactions' }}
                                </h2>
                                <p class="mb-2">
                                    {{ $isAr 
                                        ? 'تعتمد العيادة نظام التحويل المالي المحلي المباشر لضمان أعلى مستويات الأمان المالي:' 
                                        : 'Our clinic utilizes direct local payment transfers to ensure maximum financial security:' }}
                                </p>
                                <ul class="mb-0" style="padding-right: 1.5rem; padding-left: 1.5rem;">
                                    <li class="mb-2">
                                        {{ $isAr ? 'لا تقوم المنصة أو التطبيق بطلب أو حفظ أي بيانات مصرفية حساسة مثل أرقام البطاقات الائتمانية أو الرموز السرية (CVV).' : 'The platform does not collect, process, or store sensitive credit card numbers or security codes (CVV).' }}
                                    </li>
                                    <li>
                                        {{ $isAr ? 'يقوم المراجع بسداد الرسوم عبر تطبيق محفظته المستقل (مثل زين كاش، سوبر كي، أو تحويل محلي)، ثم يرفع صورة إشعار التحويل فقط لتوثيق السداد وتأكيد الحجز.' : 'Clients transfer fees independently via their own payment apps and submit transaction receipts solely to verify payment.' }}
                                    </li>
                                </ul>
                            </div>

                            {{-- Article 5 --}}
                            <div class="mb-5 pb-4 border-bottom">
                                <h2 class="fw-bold text-dark mb-3" style="font-size: 1.35rem;">
                                    {{ $isAr ? 'المادة الخامسة: أمن وتشفير البيانات' : 'Article 5: Data Security & Encryption' }}
                                </h2>
                                <p class="mb-0">
                                    {{ $isAr 
                                        ? 'نطبق تدابير أمنية وتقنية متقدمة لحماية البيانات، تشمل استخدام بروتوكولات التشفير القياسية (SSL/TLS 256-bit) لحماية البيانات أثناء نقلها، بجانب قواعد حماية الخوادم والأنظمة ضد أي وصول غير مصرح به.' 
                                        : 'We implement advanced industry-standard security protocols, including SSL/TLS 256-bit encryption during data transmission, along with strict server protections against unauthorized access.' }}
                                </p>
                            </div>

                            {{-- Article 6 --}}
                            <div class="mb-5 pb-4 border-bottom">
                                <h2 class="fw-bold text-dark mb-3" style="font-size: 1.35rem;">
                                    {{ $isAr ? 'المادة السادسة: حقوق المستخدم وحذف الحساب والبيانات' : 'Article 6: User Rights & Data Deletion' }}
                                </h2>
                                <p class="mb-2">
                                    {{ $isAr 
                                        ? 'امتثالاً لسياسات الخصوصية الدولية ومتطلبات متجري Google Play و Apple App Store، يحق للمراجع ممارسة الحقوق الآتية في أي وقت:' 
                                        : 'In compliance with international privacy standards and Google Play & Apple App Store requirements, users have the right to:' }}
                                </p>
                                <ul class="mb-3" style="padding-right: 1.5rem; padding-left: 1.5rem;">
                                    <li class="mb-1">{{ $isAr ? 'طلب نسخة من البيانات الشخصية المسجلة في حسابه.' : 'Request a copy of their personal data registered on the system.' }}</li>
                                    <li class="mb-1">{{ $isAr ? 'طلب تصحيح أو تعديل أي بيانات غير دقيقة.' : 'Request correction of inaccurate information.' }}</li>
                                    <li>{{ $isAr ? 'طلب حذف الحساب وجميع البيانات المرتبطة به نهائياً من سجلات وخوادم العيادة.' : 'Request permanent deletion of their account and all associated data.' }}</li>
                                </ul>
                                <p class="mb-0">
                                    {{ $isAr 
                                        ? 'لتقديم طلب حذف البيانات أو الحساب، يرجى التواصل معنا عبر البريد الإلكتروني الرسمي الموضح أدناه وسيتم تنفيذ الطلب خلال مدة لا تتجاوز 48 ساعة عمل.' 
                                        : 'To request account or data deletion, please contact us via our official email below. Requests will be executed within 48 business hours.' }}
                                </p>
                            </div>

                            {{-- Article 7 --}}
                            <div class="mb-5 pb-4 border-bottom">
                                <h2 class="fw-bold text-dark mb-3" style="font-size: 1.35rem;">
                                    {{ $isAr ? 'المادة السابعة: التعديلات على هذه السياسة' : 'Article 7: Policy Updates' }}
                                </h2>
                                <p class="mb-0">
                                    {{ $isAr 
                                        ? 'تحتفظ إدارة العيادة بالحق في تحديث بنود هذه السياسة عند الحاجة لمواكبة التطورات التقنية أو التنظيمية. يتم نشر أي تعديل على هذه الصفحة مع تحديث تاريخ المراجعة في أعلى الوثيقة.' 
                                        : 'Clinic administration reserves the right to update this policy as necessary to comply with legal or operational developments. Any revisions will be published on this page with an updated revision date.' }}
                                </p>
                            </div>

                            {{-- Article 8 --}}
                            <div>
                                <h2 class="fw-bold text-dark mb-3" style="font-size: 1.35rem;">
                                    {{ $isAr ? 'المادة الثامنة: معلومات التواصل القانوني' : 'Article 8: Legal & Inquiries Contact' }}
                                </h2>
                                <p class="mb-3">
                                    {{ $isAr 
                                        ? 'لأي استفسار يتعلق بسياسة الخصوصية أو إدارة بياناتك، يمكنك التواصل مع إدارة عيادة المعالج النفسي يونس المرشد:' 
                                        : 'For questions regarding this Privacy Policy or managing your data, please contact Therapist Yonis Al-Murshid Clinic:' }}
                                </p>
                                @php
                                    $waNum = preg_replace('/[^0-9]/', '', \App\Models\Setting::get('whatsapp_number', '+9647700000000'));
                                    $contactEmail = \App\Models\Setting::get('notification_email', 'contact@younis-almurshid.com');
                                    $doctorName = \App\Models\Setting::get('doctor_name', 'يونس المرشد');
                                @endphp
                                <div class="bg-light p-3 rounded-3 border">
                                    <div class="mb-1"><strong>{{ $isAr ? 'الجهة المسؤولة:' : 'Entity:' }}</strong> {{ $isAr ? 'عيادة المعالج النفسي د. ' . $doctorName : 'Therapist ' . $doctorName . ' Clinic' }}</div>
                                    <div class="mb-1"><strong>{{ $isAr ? 'البريد الإلكتروني:' : 'Email:' }}</strong> <a href="mailto:{{ $contactEmail }}" class="text-decoration-none text-dark">{{ $contactEmail }}</a></div>
                                    @if(!empty($waNum))
                                        <div><strong>{{ $isAr ? 'هاتف / واتساب المواعيد:' : 'Phone / WhatsApp:' }}</strong> <a href="https://wa.me/{{ $waNum }}" target="_blank" class="text-decoration-none text-dark" dir="ltr">+{{ $waNum }}</a></div>
                                    @endif
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
