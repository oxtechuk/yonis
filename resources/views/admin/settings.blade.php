@extends('layouts.admin')

@section('title', 'إعدادات المنصة')

@section('content')
<div class="card border-0 shadow-sm p-4">
    <div class="card-header bg-white py-3 border-0 px-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold m-0"><i class="bi bi-gear-fill text-teal me-1" style="color: var(--accent-color);"></i> إدارة إعدادات المنصة وتتبع الزيارات</h5>
    </div>
    
    <div class="card-body px-0">
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf

            <!-- Navigation Tabs -->
            <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold" id="analytics-tab" data-bs-toggle="tab" data-bs-target="#analytics-panel" type="button" role="tab" aria-selected="true">
                        <i class="bi bi-graph-up-arrow me-1"></i> التحليلات والتتبع (Analytics)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications-panel" type="button" role="tab" aria-selected="false">
                        <i class="bi bi-bell-fill me-1"></i> إشعارات النظام (Notifications)
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="settingsTabsContent">
                
                <!-- Analytics Panel -->
                <div class="tab-pane fade show active" id="analytics-panel" role="tabpanel" aria-labelledby="analytics-tab">
                    <div class="col-md-8">
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark mb-1">معرّف تتبع جوجل أناليتكس (Google Analytics ID)</h6>
                            <p class="text-secondary small">أدخل معرف القياس (G-XXXXXXX) لتتبع زيارات المرضى وسلوكهم على الموقع.</p>
                            <input type="text" name="google_analytics_id" class="form-control" placeholder="G-XXXXXXXXXX" value="{{ $settings['google_analytics_id'] }}">
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold text-dark mb-1">معرّف تتبع ميتا بكسل (Meta Pixel ID)</h6>
                            <p class="text-secondary small">أدخل معرف بكسل فيسبوك لتتبع تحويلات الحجز وقياس فاعلية الإعلانات الموجهة.</p>
                            <input type="text" name="meta_pixel_id" class="form-control" placeholder="123456789012345" value="{{ $settings['meta_pixel_id'] }}">
                        </div>
                    </div>
                </div>

                <!-- Notifications Panel -->
                <div class="tab-pane fade" id="notifications-panel" role="tabpanel" aria-labelledby="notifications-tab">
                    <div class="col-md-8">
                        <h6 class="fw-bold text-dark mb-3">تفضيلات إشعارات البريد الإلكتروني (محاكية محلياً في Log)</h6>
                        
                        <div class="form-check form-switch text-start mb-3 p-3 bg-light rounded-3 d-flex justify-content-between align-items-center">
                            <div>
                                <label class="form-check-label fw-bold text-dark" for="notifyNewSwitch">إشعار عند حجز جديد</label>
                                <div class="text-secondary small">إرسال بريد إلكتروني تلقائي للطبيب والمريض عند نجاح عملية الحجز.</div>
                            </div>
                            <input class="form-check-input ms-0" type="checkbox" role="switch" name="notify_new_booking" id="notifyNewSwitch" value="1" @if($settings['notify_new_booking'] === '1') checked @endif>
                        </div>

                        <div class="form-check form-switch text-start mb-4 p-3 bg-light rounded-3 d-flex justify-content-between align-items-center">
                            <div>
                                <label class="form-check-label fw-bold text-dark" for="notifyCancelSwitch">إشعار عند إلغاء الحجز</label>
                                <div class="text-secondary small">إرسال بريد إلكتروني فوري للمريض عند إلغاء الحجز أو إرجاع الأموال.</div>
                            </div>
                            <input class="form-check-input ms-0" type="checkbox" role="switch" name="notify_cancellation" id="notifyCancelSwitch" value="1" @if($settings['notify_cancellation'] === '1') checked @endif>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Submit Button -->
            <div class="mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-premium px-5 py-2.5 shadow">
                    <i class="bi bi-save me-1"></i> حفظ الإعدادات وتطبيقها
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
