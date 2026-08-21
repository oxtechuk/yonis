@extends('layouts.admin')

@section('title', 'مركز التحكم في الـ API والخدمات')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold m-0"><i class="bi bi-code-slash text-teal me-2" style="color: var(--accent-color);"></i> مركز التحكم في الـ API والخدمات</h3>
        <p class="text-secondary small m-0">إدارة مفاتيح التشغيل، التحكم بقنوات الاستشارة (شات، صوت، فيديو)، وضوابط إعادة جدولة المواعيد لتطبيق الموبايل.</p>
    </div>
</div>

<!-- Stats Overview -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3 text-primary">
                    <i class="bi bi-key-fill fs-3"></i>
                </div>
                <div>
                    <h6 class="text-secondary small m-0">رموز الـ Tokens النشطة</h6>
                    <h4 class="fw-bold m-0 mt-1">{{ $stats['total_tokens'] }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3 text-success">
                    <i class="bi bi-house-door-fill fs-3"></i>
                </div>
                <div>
                    <h6 class="text-secondary small m-0">حجوزات العيادة</h6>
                    <h4 class="fw-bold m-0 mt-1">{{ $stats['clinic_bookings'] }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3 text-info">
                    <i class="bi bi-globe fs-3"></i>
                </div>
                <div>
                    <h6 class="text-secondary small m-0">الحجوزات الأونلاين</h6>
                    <h4 class="fw-bold m-0 mt-1">{{ $stats['online_bookings'] }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3 text-warning">
                    <i class="bi bi-chat-dots-fill fs-3"></i>
                </div>
                <div>
                    <h6 class="text-secondary small m-0">استشارات أونلاين (شات/صوت/فيديو)</h6>
                    <h4 class="fw-bold m-0 mt-1">{{ $stats['chat_bookings'] + $stats['voice_bookings'] + $stats['video_bookings'] }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Controls Form -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold m-0"><i class="bi bi-sliders me-1 text-teal" style="color: var(--accent-color);"></i> مفاتيح التحكم والتشغيل (Feature Toggles)</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.api-control.update') }}" method="POST">
                    @csrf
                    
                    <!-- Global API Switch -->
                    <div class="p-3 bg-light rounded mb-4 border">
                        <div class="form-check form-switch d-flex align-items-center justify-content-between p-0">
                            <div>
                                <label class="form-check-label fw-bold text-dark fs-6" for="apiEnabled">تفعيل خدمة الـ API لتطبيق الموبايل</label>
                                <div class="text-secondary small">عند التعطيل، سيدخل التطبيق في حالة صيانة فورية ويرفض أي طلبات حجز جديدة.</div>
                            </div>
                            <input class="form-check-input ms-3 fs-4" type="checkbox" role="switch" name="api_enabled" id="apiEnabled" @if($settings['api_enabled'] == '1') checked @endif>
                        </div>
                    </div>

                    <h6 class="fw-bold text-teal border-bottom pb-2 mb-3">أنواع الحجوزات المتاحة عبر الـ API</h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="border rounded p-3 bg-white">
                                <div class="form-check form-switch d-flex justify-content-between p-0">
                                    <div>
                                        <div class="fw-bold text-dark"><i class="bi bi-hospital text-danger me-1"></i> حجوزات العيادة (In-Clinic)</div>
                                        <div class="text-secondary small">السماح بالحجز الحضوري لمقر العيادة.</div>
                                    </div>
                                    <input class="form-check-input ms-2" type="checkbox" role="switch" name="clinic_booking_enabled" id="clinicEnabled" @if($settings['clinic_booking_enabled'] == '1') checked @endif>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="border rounded p-3 bg-white">
                                <div class="form-check form-switch d-flex justify-content-between p-0">
                                    <div>
                                        <div class="fw-bold text-dark"><i class="bi bi-laptop text-primary me-1"></i> الحجوزات الأونلاين (Online)</div>
                                        <div class="text-secondary small">السماح بطلب الاستشارات الإلكترونية.</div>
                                    </div>
                                    <input class="form-check-input ms-2" type="checkbox" role="switch" name="online_booking_enabled" id="onlineEnabled" @if($settings['online_booking_enabled'] == '1') checked @endif>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-teal border-bottom pb-2 mb-3">قنوات الاستشارة الأونلاين (Online Consultation Channels)</h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="border rounded p-3 bg-white text-center">
                                <i class="bi bi-chat-text-fill fs-2 text-warning mb-2 d-block"></i>
                                <div class="fw-bold mb-1">استشارة شات 💬</div>
                                <div class="form-check form-switch d-flex justify-content-center p-0">
                                    <input class="form-check-input ms-0" type="checkbox" role="switch" name="chat_enabled" id="chatEnabled" @if($settings['chat_enabled'] == '1') checked @endif>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="border rounded p-3 bg-white text-center">
                                <i class="bi bi-telephone-fill fs-2 text-success mb-2 d-block"></i>
                                <div class="fw-bold mb-1">استشارة صوتية 📞</div>
                                <div class="form-check form-switch d-flex justify-content-center p-0">
                                    <input class="form-check-input ms-0" type="checkbox" role="switch" name="voice_enabled" id="voiceEnabled" @if($settings['voice_enabled'] == '1') checked @endif>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="border rounded p-3 bg-white text-center">
                                <i class="bi bi-camera-video-fill fs-2 text-info mb-2 d-block"></i>
                                <div class="fw-bold mb-1">استشارة فيديو 📹</div>
                                <div class="form-check form-switch d-flex justify-content-center p-0">
                                    <input class="form-check-input ms-0" type="checkbox" role="switch" name="video_enabled" id="videoEnabled" @if($settings['video_enabled'] == '1') checked @endif>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-teal border-bottom pb-2 mb-3">سياسات ضوابط تغيير وإعادة جدولة المواعيد (Rescheduling Rules)</h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">الحد الأقصى لمرات التغيير لكل حجز</label>
                            <div class="input-group">
                                <input type="number" name="max_reschedule_allowed" class="form-control" value="{{ $settings['max_reschedule_allowed'] }}" min="0" required>
                                <span class="input-group-text">مرات</span>
                            </div>
                            <div class="form-text small">عدد المرات التي يُسمح للمريض فيها بتغيير موعده عبر التطبيق.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">الحد الأدنى للإشعار المسبق بالتعديل</label>
                            <div class="input-group">
                                <input type="number" name="min_reschedule_notice_hours" class="form-control" value="{{ $settings['min_reschedule_notice_hours'] }}" min="0" required>
                                <span class="input-group-text">ساعة</span>
                            </div>
                            <div class="form-text small">أقل مهلة زمنية (بالساعات) قبل الموعد الأصلي لتمكين تعديله.</div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-premium w-100 py-2.5">
                        <i class="bi bi-save me-1"></i> حفظ وتطبيق كافة الإعدادات
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- API Documentation & Endpoints Info -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold m-0"><i class="bi bi-journal-code me-1 text-teal" style="color: var(--accent-color);"></i> وثائق اتصالات الـ API الحالية</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item px-0">
                        <span class="badge bg-success me-2">GET</span> <code class="fw-bold text-dark">/api/config</code>
                        <div class="text-secondary small mt-1">إرجاع حالة السيرفر والإمكانيات المفعلة (عيادة، أونلاين، شات، صوت، فيديو).</div>
                    </div>
                    <div class="list-group-item px-0">
                        <span class="badge bg-success me-2">GET</span> <code class="fw-bold text-dark">/api/services</code>
                        <div class="text-secondary small mt-1">قائمة الخدمات والأسعار المخصصة لكل قناة (عيادة، شات، صوت، فيديو).</div>
                    </div>
                    <div class="list-group-item px-0">
                        <span class="badge bg-success me-2">GET</span> <code class="fw-bold text-dark">/api/slots?service_id={id}&date={Y-m-d}</code>
                        <div class="text-secondary small mt-1">جلب الفترات والمواعيد الشاغرة المحسوبة تلقائياً.</div>
                    </div>
                    <div class="list-group-item px-0">
                        <span class="badge bg-primary me-2">POST</span> <code class="fw-bold text-dark">/api/checkout/initialize</code>
                        <div class="text-secondary small mt-1">بدء الحجز وتوليد Stripe intent بحسب القناة والسعر المخصص.</div>
                    </div>
                    <div class="list-group-item px-0">
                        <span class="badge bg-primary me-2">POST</span> <code class="fw-bold text-dark">/api/booking/{id}/reschedule</code>
                        <div class="text-secondary small mt-1">إعادة جدولة وتغيير موعد حجز قائم وفق ضوابط الأدمن.</div>
                    </div>
                </div>

                <div class="alert alert-info border-0 mt-4 mb-0 small">
                    <i class="bi bi-info-circle-fill me-1"></i>
                    <strong>ملاحظة مطور:</strong> تُطبق هذه القيود ديناميكياً على استجابات تطبيقات الموبايل وفحوصات الـ Back-end فور تغيير أي من المفاتيح أعلاه.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Active Personal Access Tokens Table -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold m-0"><i class="bi bi-shield-lock me-1 text-teal" style="color: var(--accent-color);"></i> رمـوز الأمان والـ Tokens المسجلة للأجهزة المحمولة</h5>
        <span class="badge bg-light text-dark border">آخر {{ count($tokens) }} جهاز يتصل بالتطبيق</span>
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
                            <td class="ps-4 fw-bold text-dark">{{ $token->user_name }}</td>
                            <td>
                                <div><i class="bi bi-telephone me-1 text-secondary"></i> {{ $token->user_phone }}</div>
                                <div class="small text-secondary"><i class="bi bi-envelope me-1"></i> {{ $token->user_email }}</div>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $token->name }}</span></td>
                            <td class="small text-secondary">{{ \Carbon\Carbon::parse($token->created_at)->diffForHumans() }}</td>
                            <td class="small text-secondary">
                                {{ $token->last_used_at ? \Carbon\Carbon::parse($token->last_used_at)->diffForHumans() : 'لم يُستخدم بعد' }}
                            </td>
                            <td class="pe-4 text-end">
                                <form action="{{ route('admin.api-control.token.revoke', $token->id) }}" method="POST" onsubmit="return confirm('هل أنت تأكد من إلغاء هذا الـ Token؟ سيتم تسجيل خروج المريض فوراً.');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                        <i class="bi bi-trash me-1"></i> إلغاء
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
