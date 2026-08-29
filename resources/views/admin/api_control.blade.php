@extends('layouts.admin')

@section('title', 'مركز التحكم في الـ API والخدمات')

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold m-0 text-primary d-flex align-items-center gap-2">
            <i class="bi bi-code-slash fs-4"></i> مركز التحكم في الـ API ومفاتيح التشغيل
        </h4>
        <p class="text-secondary small m-0">إدارة مفاتيح التشغيل (Feature Toggles)، وقنوات الاستشارة، وضوابط إعادة الجدولة لتطبيق الموبايل.</p>
    </div>
    <div>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1.5 fw-bold">
            <i class="bi bi-shield-check me-1"></i> وضع الحماية والـ API نشط
        </span>
    </div>
</div>

<!-- Stats Overview -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-bold d-block mb-1">رموز الـ Tokens النشطة</span>
                    <h3 class="fw-black text-dark m-0">{{ $stats['total_tokens'] }}</h3>
                </div>
                <div class="rounded-4 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(64, 85, 165, 0.1); color: var(--primary-color); font-size: 1.5rem;">
                    <i class="bi bi-key-fill"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-bold d-block mb-1">حجوزات العيادة</span>
                    <h3 class="fw-black text-dark m-0">{{ $stats['clinic_bookings'] }}</h3>
                </div>
                <div class="rounded-4 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(16, 185, 129, 0.1); color: #10b981; font-size: 1.5rem;">
                    <i class="bi bi-hospital-fill"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-bold d-block mb-1">الحجوزات الأونلاين</span>
                    <h3 class="fw-black text-dark m-0">{{ $stats['online_bookings'] }}</h3>
                </div>
                <div class="rounded-4 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(59, 130, 246, 0.1); color: #3b82f6; font-size: 1.5rem;">
                    <i class="bi bi-globe"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-bold d-block mb-1">استشارات القنوات (شات/صوت/فيديو)</span>
                    <h3 class="fw-black text-dark m-0">{{ $stats['chat_bookings'] + $stats['voice_bookings'] + $stats['video_bookings'] }}</h3>
                </div>
                <div class="rounded-4 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(245, 158, 11, 0.1); color: #f59e0b; font-size: 1.5rem;">
                    <i class="bi bi-chat-dots-fill"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Controls Form -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold m-0 text-primary d-flex align-items-center gap-2">
                    <i class="bi bi-sliders fs-5"></i> مفاتيح التحكم والتشغيل (Feature Toggles)
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.api-control.update') }}" method="POST">
                    @csrf
                    
                    <!-- Global API Switch -->
                    <div class="p-3 bg-light rounded-4 mb-3 border">
                        <div class="form-check form-switch d-flex align-items-center justify-content-between p-0">
                            <div>
                                <label class="form-check-label fw-bold text-dark fs-6" for="apiEnabled">تفعيل خدمة الـ API لتطبيق الموبايل</label>
                                <div class="text-secondary small">عند التعطيل، سيدخل التطبيق في وضع الصيانة الفورية ويرفض أي طلبات حجز جديدة.</div>
                            </div>
                            <input class="form-check-input ms-3 fs-4" type="checkbox" role="switch" name="api_enabled" id="apiEnabled" @if($settings['api_enabled'] == '1') checked @endif>
                        </div>
                    </div>

                    <!-- Stripe Gateway Switch -->
                    <div class="p-3 bg-light rounded-4 mb-4 border">
                        <div class="form-check form-switch d-flex align-items-center justify-content-between p-0">
                            <div>
                                <label class="form-check-label fw-bold text-dark fs-6" for="stripeEnabled">
                                    <i class="bi bi-credit-card-2-front-fill text-primary me-1"></i> تفعيل بوابة الدفع المباشر (Stripe Gateway)
                                </label>
                                <div class="text-secondary small">عند التعطيل (موصى به حالياً)، سيعتمد النظام على روابط الدفع المباشرة (Gumroad).</div>
                            </div>
                            <input class="form-check-input ms-3 fs-4" type="checkbox" role="switch" name="stripe_enabled" id="stripeEnabled" @if(($settings['stripe_enabled'] ?? '0') == '1') checked @endif>
                        </div>
                    </div>

                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3"><i class="bi bi-calendar2-range-fill me-1"></i> أنواع الحجوزات المتاحة عبر الـ API</h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="border rounded-4 p-3 bg-white shadow-none">
                                <div class="form-check form-switch d-flex justify-content-between p-0">
                                    <div>
                                        <div class="fw-bold text-dark"><i class="bi bi-hospital text-danger me-1"></i> حجوزات العيادة (In-Clinic)</div>
                                        <div class="text-secondary small">السماح بالحجز الحضوري للعيادة.</div>
                                    </div>
                                    <input class="form-check-input ms-2" type="checkbox" role="switch" name="clinic_booking_enabled" id="clinicEnabled" @if($settings['clinic_booking_enabled'] == '1') checked @endif>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="border rounded-4 p-3 bg-white shadow-none">
                                <div class="form-check form-switch d-flex justify-content-between p-0">
                                    <div>
                                        <div class="fw-bold text-dark"><i class="bi bi-laptop text-primary me-1"></i> الحجوزات الأونلاين (Online)</div>
                                        <div class="text-secondary small">السماح بطلب الجلسات الإلكترونية.</div>
                                    </div>
                                    <input class="form-check-input ms-2" type="checkbox" role="switch" name="online_booking_enabled" id="onlineEnabled" @if($settings['online_booking_enabled'] == '1') checked @endif>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3"><i class="bi bi-broadcast-pin me-1"></i> قنوات الاستشارة الأونلاين (Online Consultation Channels)</h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="border rounded-4 p-3 bg-white text-center">
                                <i class="bi bi-chat-dots-fill fs-2 text-warning mb-2 d-block"></i>
                                <div class="fw-bold small mb-2 text-dark">استشارة محادثة (شات)</div>
                                <div class="form-check form-switch d-flex justify-content-center p-0">
                                    <input class="form-check-input ms-0" type="checkbox" role="switch" name="chat_enabled" id="chatEnabled" @if($settings['chat_enabled'] == '1') checked @endif>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="border rounded-4 p-3 bg-white text-center">
                                <i class="bi bi-telephone-fill fs-2 text-success mb-2 d-block"></i>
                                <div class="fw-bold small mb-2 text-dark">استشارة مكالمة صوتية</div>
                                <div class="form-check form-switch d-flex justify-content-center p-0">
                                    <input class="form-check-input ms-0" type="checkbox" role="switch" name="voice_enabled" id="voiceEnabled" @if($settings['voice_enabled'] == '1') checked @endif>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="border rounded-4 p-3 bg-white text-center">
                                <i class="bi bi-camera-video-fill fs-2 text-info mb-2 d-block"></i>
                                <div class="fw-bold small mb-2 text-dark">استشارة مكالمة فيديو</div>
                                <div class="form-check form-switch d-flex justify-content-center p-0">
                                    <input class="form-check-input ms-0" type="checkbox" role="switch" name="video_enabled" id="videoEnabled" @if($settings['video_enabled'] == '1') checked @endif>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3"><i class="bi bi-arrow-repeat me-1"></i> سياسات ضوابط إعادة جدولة المواعيد (Rescheduling Rules)</h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">الحد الأقصى لمرات التغيير لكل حجز</label>
                            <div class="input-group">
                                <input type="number" name="max_reschedule_allowed" class="form-control rounded-start-3" value="{{ $settings['max_reschedule_allowed'] }}" min="0" required>
                                <span class="input-group-text bg-light rounded-end-3">مرات</span>
                            </div>
                            <div class="form-text small text-secondary">عدد المرات المسموح بها للمريض لإعادة جدولة موعده.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">الحد الأدنى للإشعار المسبق بالتعديل</label>
                            <div class="input-group">
                                <input type="number" name="min_reschedule_notice_hours" class="form-control rounded-start-3" value="{{ $settings['min_reschedule_notice_hours'] }}" min="0" required>
                                <span class="input-group-text bg-light rounded-end-3">ساعة</span>
                            </div>
                            <div class="form-text small text-secondary">أقل مهلة زمنية (بالساعات) قبل الموعد الأصلي لإتاحة تعديله.</div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-royal-primary w-100 py-3 rounded-pill fw-bold shadow-sm">
                        <i class="bi bi-check2-circle me-1"></i> حفظ وتطبيق كافة إعدادات الـ API
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- API Documentation & Endpoints Info -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold m-0 text-primary d-flex align-items-center gap-2">
                    <i class="bi bi-journal-code fs-5"></i> وثائق اتصالات الـ API الحالية
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="d-flex flex-column gap-3">
                    <div class="p-3 bg-light rounded-3 border">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <code class="fw-bold text-dark">/api/config</code>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5">GET</span>
                        </div>
                        <div class="text-secondary small">إرجاع حالة السيرفر والإمكانيات المفعلة (عيادة، أونلاين، شات، صوت، فيديو).</div>
                    </div>

                    <div class="p-3 bg-light rounded-3 border">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <code class="fw-bold text-dark">/api/services</code>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5">GET</span>
                        </div>
                        <div class="text-secondary small">قائمة الخدمات والأسعار المخصصة لكل قناة (عيادة، شات، صوت، فيديو).</div>
                    </div>

                    <div class="p-3 bg-light rounded-3 border">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <code class="fw-bold text-dark">/api/slots</code>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5">GET</span>
                        </div>
                        <div class="text-secondary small">جلب الفترات والمواعيد الشاغرة المحسوبة تلقائياً مع المعاملات.</div>
                    </div>

                    <div class="p-3 bg-light rounded-3 border">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <code class="fw-bold text-dark">/api/checkout/initialize</code>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-0.5">POST</span>
                        </div>
                        <div class="text-secondary small">بدء طلب الحجز ورابط الدفع المباشر بحسب القناة والسعر.</div>
                    </div>

                    <div class="p-3 bg-light rounded-3 border">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <code class="fw-bold text-dark">/api/booking/{id}/reschedule</code>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-0.5">POST</span>
                        </div>
                        <div class="text-secondary small">إعادة جدولة وتغيير موعد حجز قائم وفق ضوابط الأدمن.</div>
                    </div>
                </div>

                <div class="alert alert-info border-0 rounded-4 mt-4 mb-0 small" style="background: rgba(64, 85, 165, 0.08); color: var(--primary-color);">
                    <i class="bi bi-info-circle-fill me-1"></i>
                    <strong>ملاحظة تقنية:</strong> تُطبق هذه القيود ديناميكياً وفورياً على استجابات الـ Back-end وتطبيقات الموبايل عند تغيير أي مفتاح.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Active Personal Access Tokens Table -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold m-0 text-primary d-flex align-items-center gap-2">
                <i class="bi bi-shield-lock-fill fs-5"></i> رمـوز الأمان والـ Tokens المسجلة للأجهزة المحمولة
            </h5>
            <p class="text-secondary small m-0">سجل الجلسات والأجهزة المتصلة بالتطبيق مع صلاحية إلغاء الوصول.</p>
        </div>
        <span class="badge bg-light text-dark border rounded-pill px-3 py-1.5">
            آخر {{ count($tokens) }} أجهزة نشطة
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">اسم المريض/الحساب</th>
                        <th>بيانات التواصل</th>
                        <th>اسم الـ Token</th>
                        <th>تاريخ الإنشاء</th>
                        <th>آخر استخدام</th>
                        <th class="pe-4 text-end">إلغاء الوصول</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tokens as $token)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(64, 85, 165, 0.1); color: var(--primary-color);">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <span>{{ $token->user_name }}</span>
                                </div>
                            </td>
                            <td>
                                <div><i class="bi bi-telephone me-1 text-secondary"></i> {{ $token->user_phone }}</div>
                                <div class="small text-secondary"><i class="bi bi-envelope me-1"></i> {{ $token->user_email }}</div>
                            </td>
                            <td><span class="badge bg-light text-dark border rounded-pill px-2.5 py-1">{{ $token->name }}</span></td>
                            <td class="small text-secondary">{{ \Carbon\Carbon::parse($token->created_at)->diffForHumans() }}</td>
                            <td class="small text-secondary">
                                {{ $token->last_used_at ? \Carbon\Carbon::parse($token->last_used_at)->diffForHumans() : 'لم يُستخدم بعد' }}
                            </td>
                            <td class="pe-4 text-end">
                                <form action="{{ route('admin.api-control.token.revoke', $token->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من إلغاء هذا الـ Token؟ سيتم تسجيل خروج المريض فوراً.');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 fw-bold">
                                        <i class="bi bi-trash3 me-1"></i> إلغاء
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-4">لا توجد رموز Tokens مسجلة حالياً.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
