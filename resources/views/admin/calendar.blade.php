@extends('layouts.admin')

@section('title', 'تقويم الحجوزات')

@section('styles')
<!-- FullCalendar CSS CDN -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet" />
<style>
    .fc-event {
        cursor: pointer;
        padding: 2px 5px;
        font-weight: 500;
    }
    .fc {
        background: #ffffff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.03);
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm p-4 mb-4 rounded-4">
           

            <div id="calendar"></div>
        </div>
    </div>
</div>

<!-- Event Details Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold text-dark fs-6" id="modal-title">تفاصيل موعد الاستشارة</h5>
                <button type="button" class="btn-close ms-0" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 52px; height: 52px; background: rgba(59, 82, 164, 0.1); color: var(--primary-color); font-size: 1.5rem;">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="fw-bold m-0 text-dark" id="modal-patient-name">-</h5>
                        <p class="text-secondary small m-0" id="modal-patient-phone">-</p>
                    </div>
                    <a id="modal-whatsapp-btn" href="#" target="_blank" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-bold d-flex align-items-center gap-1">
                        <i class="bi bi-whatsapp"></i> واتساب
                    </a>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <span class="text-secondary small d-block mb-1">الخدمة الاستشارية</span>
                        <strong class="text-dark small" id="modal-service">-</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-secondary small d-block mb-1">رقم مرجع الحجز</span>
                        <strong class="text-primary small" id="modal-reference">-</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-secondary small d-block mb-1">التوقيت والتاريخ</span>
                        <strong class="text-dark small" id="modal-time">-</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-secondary small d-block mb-1">رسوم الجلسة</span>
                        <strong class="text-success small" id="modal-price">-</strong>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded-4 mb-3">
                    <div>
                        <span class="text-secondary small d-block">حالة الحجز</span>
                        <span id="modal-status-badge">-</span>
                    </div>
                    <div id="modal-notes-box" class="text-end small text-muted"></div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="#" id="modal-history-link" class="btn btn-royal-primary btn-sm rounded-pill px-3 py-2 fw-bold">
                        <i class="bi bi-file-medical me-1"></i> عرض سجل الحجز والمريض
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- FullCalendar JS CDN -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<!-- FullCalendar Arabic Locale -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/locales/ar.global.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));

        const calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'ar',
            direction: 'rtl',
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: '{{ route('admin.api.calendar-events') }}',
            eventClick: function(info) {
                const props = info.event.extendedProps;
                
                document.getElementById('modal-patient-name').innerText = props.patient_name;
                document.getElementById('modal-patient-phone').innerText = props.patient_phone;
                document.getElementById('modal-service').innerText = props.service_title;
                document.getElementById('modal-reference').innerText = '#' + props.reference;
                
                // WhatsApp Button
                const cleanPhone = (props.patient_phone || '').replace(/\D/g, '');
                const waBtn = document.getElementById('modal-whatsapp-btn');
                if (waBtn) {
                    if (cleanPhone) {
                        waBtn.href = `https://wa.me/${cleanPhone}`;
                        waBtn.style.display = 'inline-flex';
                    } else {
                        waBtn.style.display = 'none';
                    }
                }

                // Format time
                let timeText = '-';
                if (info.event.start) {
                    const startTime = info.event.start.toLocaleTimeString('ar', {hour: '2-digit', minute:'2-digit'});
                    const dateStr = info.event.start.toLocaleDateString('ar', {year: 'numeric', month: 'short', day: 'numeric'});
                    timeText = `${dateStr} في ${startTime}`;
                }
                document.getElementById('modal-time').innerText = timeText;
                document.getElementById('modal-price').innerText = `$${props.price}`;

                // Status Badge
                let badgeClass = 'bg-success';
                let statusText = props.status_label || 'مؤكد';
                if (props.status === 'Completed') {
                    badgeClass = 'bg-primary';
                } else if (props.status === 'AwaitingPayment' || props.status === 'Pending') {
                    badgeClass = 'bg-warning text-dark';
                } else if (props.status && props.status.includes('Cancelled')) {
                    badgeClass = 'bg-danger';
                }
                document.getElementById('modal-status-badge').innerHTML = `<span class="badge ${badgeClass} px-3 py-1.5 rounded-pill small fw-bold">${statusText}</span>`;

                // History link mapping
                const historyLink = document.getElementById('modal-history-link');
                if (props.patient_id) {
                    historyLink.href = `/admin/patients/${props.patient_id}`;
                    historyLink.innerHTML = '<i class="bi bi-file-medical me-1"></i> السجل الطبي للمريض';
                } else {
                    historyLink.href = `/admin/bookings?search=${props.reference}`;
                    historyLink.innerHTML = '<i class="bi bi-calendar-check me-1"></i> تفاصيل الحجز';
                }

                eventModal.show();
            }
        });

        calendar.render();
    });
</script>
@endsection
