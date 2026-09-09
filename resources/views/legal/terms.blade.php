@php
    $isAr = $isAr ?? (app()->getLocale() === 'ar');
@endphp
@extends('layouts.app')

@section('title', ($isAr ? 'الشروط والأحكام وسياسة الاستخدام' : 'Terms & Conditions of Service') . ' - ' . \App\Models\Setting::get('doctor_name', 'يونس المرشد'))

@section('meta_description', $isAr ? 'الشروط والأحكام الرسمية وسياسة حجز وإلغاء الاستشارات النفسية لموقع وتطبيق المعالج النفسي يونس المرشد.' : 'Official Terms & Conditions and Booking Policies for Therapist Yonis Al-Murshid Platform.')

@section('content')
<div class="legal-page-wrapper py-5" style="background-color: #f8fafc; min-height: 85vh;">
    <div class="container py-lg-4">
        
        {{-- Document Header --}}
        <div class="text-center max-w-750 mx-auto mb-5">
            <h1 class="fw-bold text-dark mb-3" style="font-size: 2.1rem; letter-spacing: -0.5px;">
                {{ $isAr ? 'الشروط والأحكام وسياسة الاستخدام' : 'Terms & Conditions of Service' }}
            </h1>
            <p class="text-secondary mb-2" style="font-size: 1.05rem; line-height: 1.7;">
                {{ $isAr 
                    ? 'اتفاقية تقديم الخدمات الاستشارية النفسية وضوابط الحجز والإلغاء المعتمدة عبر الموقع والتطبيق.' 
                    : 'Agreement governing consultation services, booking procedures, and cancellation policies.' }}
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
                        $customTerms = \App\Models\Setting::get('terms_conditions_content', '');
                    @endphp

                    @if(!empty($customTerms))
                        <div class="legal-custom-body" style="font-size: 1.05rem; line-height: 1.9; color: #334155;">
                            {!! nl2br(e($customTerms)) !!}
                        </div>
                    @else
                        {{-- Formal Legal Articles --}}
                        <div class="legal-document-body" style="font-size: 1.05rem; line-height: 1.9; color: #334155;">

                            {{-- Article 1 --}}
                            <div class="mb-5 pb-4 border-bottom">
                                <h2 class="fw-bold text-dark mb-3" style="font-size: 1.35rem;">
                                    {{ $isAr ? 'المادة الأولى: الموافقة والأهلية القانونية' : 'Article 1: Acceptance & Eligibility' }}
                                </h2>
                                <p class="mb-0">
                                    {{ $isAr 
                                        ? 'يعد استخدامك للموقع الإلكتروني أو تطبيق الهاتف المحمول أو حجز أي موعد استشاري إقراراً صريحاً وموافقة تامة وغير مشروطة على الالتزام بكافة بنود هذه الاتفاقية وسياسة الخصوصية التابعة لها. إذا كنت لا توافق على أي بند من هذه الشروط، يرجى الامتناع عن استخدام المنصة.' 
                                        : 'By accessing the website, mobile app, or booking an appointment, you explicitly agree to comply with all terms of this Agreement and the accompanying Privacy Policy. If you do not agree with any provision, please refrain from using the platform.' }}
                                </p>
                            </div>

                            {{-- Article 2 --}}
                            <div class="mb-5 pb-4 border-bottom">
                                <h2 class="fw-bold text-dark mb-3" style="font-size: 1.35rem;">
                                    {{ $isAr ? 'المادة الثانية: طبيعة الخدمات والاستشارات النفسية' : 'Article 2: Nature of Consultation Services' }}
                                </h2>
                                <p class="mb-2">
                                    {{ $isAr 
                                        ? 'يقدم المعالج النفسي يونس المرشد جلسات استشارية متخصصة في الإرشاد النفسي، وتطوير الذات، والعلاج المعرفي السلوكي (CBT)، والجلسات الزوجية والأسرية وفق الضوابط التالية:' 
                                        : 'Therapist Yonis Al-Murshid provides specialized consultations in psychological counseling, personal development, and CBT under the following guidelines:' }}
                                </p>
                                <ul class="mb-0" style="padding-right: 1.5rem; padding-left: 1.5rem;">
                                    <li class="mb-2">
                                        {{ $isAr ? 'تقدم الاستشارات إما حضورياً في مقر العيادة أو عن بُعد عبر القنوات الرقمية المعتمدة (فيديو، مكالمة صوتية، أو محادثة كتابية).' : 'Consultations are conducted in-clinic or remotely via authorized digital channels (video, voice, or chat).' }}
                                    </li>
                                    <li>
                                        <strong>{{ $isAr ? 'تنويه الطوارئ:' : 'Emergency Disclaimer:' }}</strong> 
                                        {{ $isAr ? 'الخدمات المقدمة عبر المنصة ليست بديلاً عن الرعاية الطبية الطارئة أو الحالات الإسعافية الحادة أو التفكير في إيذاء النفس. في هذه الحالات الحرجة، يرجى التوجه فوراً لأقرب مستشفى أو الاتصال بخدمات الطوارئ الطبية المحلية.' : 'Our services do not constitute emergency psychiatric intervention. In acute crisis situations or thoughts of self-harm, please visit the nearest hospital or contact local emergency services immediately.' }}
                                    </li>
                                </ul>
                            </div>

                            {{-- Article 3 --}}
                            <div class="mb-5 pb-4 border-bottom">
                                <h2 class="fw-bold text-dark mb-3" style="font-size: 1.35rem;">
                                    {{ $isAr ? 'المادة الثالثة: إجراءات الحجز وسداد الرسوم' : 'Article 3: Booking & Payment Procedures' }}
                                </h2>
                                <p class="mb-2">
                                    {{ $isAr 
                                        ? 'تتم عملية حجز وتأكيد المواعيد وفق الخطوات التنظيمية التالية:' 
                                        : 'Appointment reservations and confirmations follow these established procedures:' }}
                                </p>
                                <ul class="mb-0" style="padding-right: 1.5rem; padding-left: 1.5rem;">
                                    <li class="mb-2">
                                        <strong>{{ $isAr ? 'اختيار الموعد والخدمة:' : 'Selection:' }}</strong> 
                                        {{ $isAr ? 'يقوم المراجع باختيار نوع الخدمة والوقت المناسب له من خلال جدول المواعيد المتاح.' : 'The client selects the desired service and available timeslot from the clinic schedule.' }}
                                    </li>
                                    <li class="mb-2">
                                        <strong>{{ $isAr ? 'السداد المالي المحلي:' : 'Payment Transfer:' }}</strong> 
                                        {{ $isAr ? 'يتم سداد تكلفة الجلسة عبر المحافظ المالية المحلية المعتمدة (زين كاش، سوبر كي، أو تحويل محلي مباشر).' : 'Session fees are paid via authorized local wallets (ZainCash, SuperKi, or local transfers).' }}
                                    </li>
                                    <li>
                                        <strong>{{ $isAr ? 'التأكيد والاعتماد:' : 'Confirmation:' }}</strong> 
                                        {{ $isAr ? 'يصبح الحجز نهائياً بمجرد إرفاق إشعار التحويل والتحقق منه من قبل إدارة العيادة، حيث يتم إرسال رسالة التأكيد وتفاصيل الرابط تلقائياً.' : 'The appointment is finalized upon verification of the transfer receipt by clinic administration, triggering automated confirmation and session link delivery.' }}
                                    </li>
                                </ul>
                            </div>

                            {{-- Article 4 --}}
                            <div class="mb-5 pb-4 border-bottom">
                                <h2 class="fw-bold text-dark mb-3" style="font-size: 1.35rem;">
                                    {{ $isAr ? 'المادة الرابعة: سياسة إعادة الجدولة والإلغاء' : 'Article 4: Rescheduling & Cancellation Policy' }}
                                </h2>
                                @php
                                    $minNotice = \App\Models\Setting::get('min_reschedule_notice_hours', '24');
                                    $maxResched = \App\Models\Setting::get('max_reschedule_allowed', '2');
                                @endphp
                                <p class="mb-2">
                                    {{ $isAr 
                                        ? 'حرصاً على تنظيم جدول مواعيد العيادة وحقوق المراجعين الآخرين، تخضع إعادة الجدولة والإلغاء للضوابط الآتية:' 
                                        : 'To ensure smooth scheduling and respect for other clients, the following rescheduling guidelines apply:' }}
                                </p>
                                <ul class="mb-0" style="padding-right: 1.5rem; padding-left: 1.5rem;">
                                    <li class="mb-2">
                                        <strong>{{ $isAr ? 'مهلة إعادة الجدولة:' : 'Notice Period:' }}</strong> 
                                        {{ $isAr ? "يحق للمراجع طلب تأجيل الموعد قبل بدء الجلسة بما لا يقل عن ($minNotice ساعة)، بحد أقصى ($maxResched مرات) لكل حجز." : "Clients may reschedule up to ($minNotice hours) prior to session start time, up to ($maxResched times) per booking." }}
                                    </li>
                                    <li>
                                        <strong>{{ $isAr ? 'الغياب دون إشعار مسبق:' : 'No-Show Policy:' }}</strong> 
                                        {{ $isAr ? 'في حال عدم حضور الجلسة في الموعد المحدد أو الإلغاء في وقت يقل عن المهلة المقررة، تعتبر الجلسة منفذة ولا يحق للمراجع المطالبة باسترداد الرسوم لتعويض حجز وقت المعالج.' : 'Failure to attend at the scheduled time without prior notice or cancellation below the required notice period renders the session non-refundable.' }}
                                    </li>
                                </ul>
                            </div>

                            {{-- Article 5 --}}
                            <div class="mb-5 pb-4 border-bottom">
                                <h2 class="fw-bold text-dark mb-3" style="font-size: 1.35rem;">
                                    {{ $isAr ? 'المادة الخامسة: الالتزام بالمواعيد وحضور الجلسات' : 'Article 5: Punctuality & Attendance' }}
                                </h2>
                                <p class="mb-0">
                                    {{ $isAr 
                                        ? 'يتعين على المراجع الحضور في الوقت المحدد تماماً لبدء الجلسة. يبدأ احتساب وقت الجلسة من التوقيت المحجوز، ولن يتم تمديد مدة الجلسة في حال تأخر المراجع عن الحضور لضمان عدم الإخلال بمواعيد المراجعين اللاحقين.' 
                                        : 'Clients are required to be present promptly at the scheduled time. Sessions start at the booked time and cannot be extended due to client tardiness to maintain timely service for subsequent appointments.' }}
                                </p>
                            </div>

                            {{-- Article 6 --}}
                            <div class="mb-5 pb-4 border-bottom">
                                <h2 class="fw-bold text-dark mb-3" style="font-size: 1.35rem;">
                                    {{ $isAr ? 'المادة السادسة: الملكية الفكرية وحظر التسجيل' : 'Article 6: Intellectual Property & Non-Recording' }}
                                </h2>
                                <p class="mb-2">
                                    {{ $isAr 
                                        ? 'حفاظاً على حقوق الملكية الفكرية والأخلاقيات المهنية، يلتزم الطرفان بالآتي:' 
                                        : 'In protection of intellectual property and professional ethics, all parties adhere to:' }}
                                </p>
                                <ul class="mb-0" style="padding-right: 1.5rem; padding-left: 1.5rem;">
                                    <li class="mb-2">
                                        {{ $isAr ? 'يُحظر تماماً على المراجع تسجيل جلسات الاستشارة (صوتياً أو مرئياً) أو التقاط صور شاشة أو نشر أي جزء منها دون إذن خطي مسبق ومعتمد من المعالج.' : 'Recording consultation sessions (audio or video), screen capturing, or publishing session excerpts without prior written consent is strictly prohibited.' }}
                                    </li>
                                    <li>
                                        {{ $isAr ? 'كافة المواد المكتوبة والتسجيلات الإرشادية والمقاطع التوعوية المنشورة على المنصة محمية بحقوق الملكية الفكرية الخاصة بالمعالج يونس المرشد.' : 'All educational materials, articles, and media on the platform are protected intellectual property of Therapist Yonis Al-Murshid.' }}
                                    </li>
                                </ul>
                            </div>

                            {{-- Article 7 --}}
                            <div class="mb-5 pb-4 border-bottom">
                                <h2 class="fw-bold text-dark mb-3" style="font-size: 1.35rem;">
                                    {{ $isAr ? 'المادة السابعة: القانون واجب التطبيق والاختصاص القضائي' : 'Article 7: Governing Law & Jurisdiction' }}
                                </h2>
                                <p class="mb-0">
                                    {{ $isAr 
                                        ? 'تخضع هذه الشروط والأحكام وتفسر وفقاً للأنظمة والقوانين المهنية المعمول بها، ويتم السعي لحل أي نزاع بشكل ودي ومهني في إطار أخلاقيات العمل الطبي والنفسي.' 
                                        : 'These Terms & Conditions are governed by and construed in accordance with applicable professional regulations, and any disputes shall be addressed through amicable and professional mediation.' }}
                                </p>
                            </div>

                            {{-- Article 8 --}}
                            <div>
                                <h2 class="fw-bold text-dark mb-3" style="font-size: 1.35rem;">
                                    {{ $isAr ? 'المادة الثامنة: خدمة المراجعين والتواصل' : 'Article 8: Client Support & Inquiries' }}
                                </h2>
                                <p class="mb-3">
                                    {{ $isAr 
                                        ? 'لأي استفسارات بخصوص حجزك أو شروط تقديم الخدمة، يسعدنا تواصلك مع إدارة العيادة عبر القنوات الرسمية التالية:' 
                                        : 'For any inquiries regarding your booking or terms of service, please contact our clinic administration:' }}
                                </p>
                                @php
                                    $waNum = preg_replace('/[^0-9]/', '', \App\Models\Setting::get('whatsapp_number', '+9647700000000'));
                                    $contactEmail = \App\Models\Setting::get('notification_email', 'contact@younis-almurshid.com');
                                    $doctorName = \App\Models\Setting::get('doctor_name', 'يونس المرشد');
                                @endphp
                                <div class="bg-light p-3 rounded-3 border">
                                    <div class="mb-1"><strong>{{ $isAr ? 'العيادة:' : 'Clinic:' }}</strong> {{ $isAr ? 'عيادة المعالج النفسي د. ' . $doctorName : 'Therapist ' . $doctorName . ' Clinic' }}</div>
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
