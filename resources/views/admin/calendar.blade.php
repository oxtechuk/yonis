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
        <div class="card border-0 shadow-sm p-4 mb-4">
            <h5 class="fw-bold mb-4"><i class="bi bi-calendar-range me-1 text-teal" style="color: var(--accent-color);"></i> تقويم الحجوزات المجدولة</h5>
            <div id="calendar"></div>
        </div>
    </div>
</div>

<!-- Event Details Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="modal-title">تفاصيل موعد الكشف</h5>
                <button type="button" class="btn-close ms-0" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <i class="bi bi-person-circle fs-1 text-teal" style="color: var(--accent-color);"></i>
                    <div>
                        <h4 class="fw-bold m-0" id="modal-patient-name">-</h4>
                        <p class="text-secondary m-0" id="modal-patient-phone">-</p>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <span class="text-secondary small d-block">الخدمة الاستشارية</span>
                        <strong class="text-dark" id="modal-service">-</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-secondary small d-block">رقم مرجع الحجز</span>
                        <strong class="text-dark" id="modal-reference">-</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-secondary small d-block">التوقيت</span>
                        <strong class="text-dark" id="modal-time">-</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-secondary small d-block">رسوم الكشف</span>
                        <strong class="text-success" id="modal-price">-</strong>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded-3">
                    <div>
                        <span class="text-secondary small d-block">حالة الحجز</span>
                        <span id="modal-status-badge">-</span>
                    </div>
                    <a href="#" id="modal-history-link" class="btn btn-premium btn-sm rounded-pill">عرض السجل الطبي</a>
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
                document.getElementById('modal-reference').innerText = props.reference;
                
                // Format time
                const startTime = info.event.start.toLocaleTimeString('ar', {hour: '2-digit', minute:'2-digit'});
                const dateStr = info.event.start.toLocaleDateString('ar', {year: 'numeric', month: 'short', day: 'numeric'});
                document.getElementById('modal-time').innerText = `${dateStr} في ${startTime}`;
                document.getElementById('modal-price').innerText = `$${props.price}`;

                // Status Badge
                let badgeClass = 'bg-success';
                let statusText = 'مؤكد';
                if (props.status === 'Completed') {
                    badgeClass = 'bg-secondary';
                    statusText = 'مكتمل';
                } else if (props.status === 'AwaitingPayment') {
                    badgeClass = 'bg-warning text-dark';
                    statusText = 'بانتظار الدفع';
                }
                document.getElementById('modal-status-badge').innerHTML = `<span class="badge ${badgeClass} px-3 py-2 rounded-pill">${statusText}</span>`;

                // History link mapping
                document.getElementById('modal-history-link').href = `/admin/patients/${props.patient_id}`;

                eventModal.show();
            }
        });

        calendar.render();
    });
</script>
@endsection
