@extends('layouts.admin')

@section('title', 'إدارة الحجوزات وإعادة الجدولة')

@section('content')
<div class="card border-0 shadow-sm mb-4 d-print-none">
    <div class="card-body">
        <h5 class="fw-bold mb-3"><i class="bi bi-funnel me-1 text-teal" style="color: var(--accent-color);"></i> تصفية وشريط البحث في الحجوزات</h5>
        <form action="{{ route('admin.bookings') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="رقم المرجع، اسم المريض، الهاتف..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">جميع الحالات</option>
                    <option value="AwaitingPayment" @if(request('status') === 'AwaitingPayment') selected @endif>بانتظار الدفع</option>
                    <option value="Confirmed" @if(request('status') === 'Confirmed') selected @endif>مؤكد</option>
                    <option value="Completed" @if(request('status') === 'Completed') selected @endif>مكتمل</option>
                    <option value="CancelledByPatient" @if(request('status') === 'CancelledByPatient') selected @endif>ملغي بواسطة المريض</option>
                    <option value="CancelledByDoctor" @if(request('status') === 'CancelledByDoctor') selected @endif>ملغي بواسطة الطبيب</option>
                    <option value="NoShow" @if(request('status') === 'NoShow') selected @endif>لم يحضر</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-premium">تصفية</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold m-0"><i class="bi bi-calendar-check me-1 text-teal" style="color: var(--accent-color);"></i> قائمة الحجوزات الواردة (موبيل وويب)</h5>
        <button type="button" class="btn btn-premium btn-sm" data-bs-toggle="modal" data-bs-target="#addBookingModal">
            <i class="bi bi-calendar-plus-fill me-1"></i> إضافة حجز يدوي
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">المرجع</th>
                        <th>المريض</th>
                        <th>الخدمة ونوع الاستشارة</th>
                        <th>التاريخ والوقت</th>
                        <th>المبلغ</th>
                        <th>حالة الحجز</th>
                        <th>الدفع</th>
                        <th class="pe-4 text-end">إجراءات الموعد</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $booking->booking_reference }}</div>
                                @if($booking->reschedule_count > 0)
                                    <span class="badge bg-info text-dark small" style="font-size: 0.7rem;"><i class="bi bi-arrow-repeat me-1"></i> معدل {{ $booking->reschedule_count }} مرة</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold">{{ $booking->patient ? $booking->patient->name : ($booking->temp_user_data['name'] ?? 'زائر') }}</div>
                                <div class="text-secondary small">{{ $booking->patient ? $booking->patient->phone : ($booking->temp_user_data['phone'] ?? '-') }}</div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $booking->service->title }}</div>
                                <div class="d-flex gap-1 mt-1">
                                    <span class="badge @if($booking->booking_type === 'online') bg-info text-dark @else bg-secondary @endif">
                                        {{ $booking->booking_type_label }}
                                    </span>
                                    <span class="badge bg-light text-dark border">
                                        {{ $booking->consultation_type_label }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div><strong>{{ $booking->date->format('Y-m-d') }}</strong></div>
                                <div class="text-secondary small">{{ Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</div>
                            </td>
                            <td class="fw-bold text-success">
                                ${{ number_format($booking->price ?? $booking->service->price, 2) }}
                            </td>
                            <td>
                                @if($booking->status === 'AwaitingPayment')
                                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">بانتظار الدفع</span>
                                @elseif($booking->status === 'Confirmed')
                                    <span class="badge bg-success px-3 py-2 rounded-pill">مؤكد</span>
                                @elseif($booking->status === 'Completed')
                                    <span class="badge bg-secondary px-3 py-2 rounded-pill">مكتمل</span>
                                @elseif($booking->status === 'CancelledByPatient')
                                    <span class="badge bg-danger px-3 py-2 rounded-pill">ملغي بواسطة المريض</span>
                                @elseif($booking->status === 'CancelledByDoctor')
                                    <span class="badge bg-danger px-3 py-2 rounded-pill">ملغي بواسطة الطبيب</span>
                                @elseif($booking->status === 'NoShow')
                                    <span class="badge bg-dark px-3 py-2 rounded-pill">لم يحضر</span>
                                @endif
                            </td>
                            <td>
                                @if($booking->payment)
                                    <span class="badge @if($booking->payment->status === 'Paid') bg-success @else bg-warning text-dark @endif">
                                        {{ $booking->payment->status === 'Paid' ? 'مدفوع' : $booking->payment->status }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">غير متوفر</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <div class="btn-group gap-1">
                                    @if(in_array($booking->status, ['Confirmed', 'AwaitingPayment']))
                                        <!-- Reschedule Button -->
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2.5" data-bs-toggle="modal" data-bs-target="#rescheduleModal{{ $booking->id }}">
                                            <i class="bi bi-calendar-event me-1"></i> تغيير الموعد
                                        </button>
                                    @endif

                                    @if($booking->status === 'Confirmed')
                                        <form action="{{ route('admin.bookings.status', $booking->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="Completed">
                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-2.5">اكتمال كشف</button>
                                        </form>

                                        <form action="{{ route('admin.bookings.status', $booking->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="NoShow">
                                            <button type="submit" class="btn btn-sm btn-dark rounded-pill px-2.5">غائب</button>
                                        </form>

                                        <form action="{{ route('admin.bookings.status', $booking->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من إلغاء الحجز وإرجاع الرسوم للمريض؟')">
                                            @csrf
                                            <input type="hidden" name="status" value="CancelledByDoctor">
                                            <button type="submit" class="btn btn-sm btn-danger rounded-pill px-2.5">إلغاء وإرجاع</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Reschedule Modal -->
                        <div class="modal fade" id="rescheduleModal{{ $booking->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header bg-light">
                                        <h5 class="modal-title fw-bold">إعادة جدولة وتغيير موعد الحجز: {{ $booking->booking_reference }}</h5>
                                        <button type="button" class="btn-close ms-0" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('admin.bookings.reschedule', $booking->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-body p-4">
                                            <div class="alert alert-info border-0 small mb-3">
                                                <i class="bi bi-info-circle me-1"></i> الموعد الحالي: <strong>{{ $booking->date->format('Y-m-d') }}</strong> الساعة <strong>{{ Carbon\Carbon::parse($booking->start_time)->format('H:i') }}</strong>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">اختر التاريخ الجديد:</label>
                                                <input type="date" name="date" id="reschedule-date-{{ $booking->id }}" class="form-control" value="{{ $booking->date->format('Y-m-d') }}" min="{{ today()->format('Y-m-d') }}" onchange="loadRescheduleSlots({{ $booking->id }}, {{ $booking->service_id }})" required>
                                            </div>

                                            <div class="mb-3" id="reschedule-slots-step-{{ $booking->id }}">
                                                <label class="form-label small fw-bold">اختر الوقت المتاح:</label>
                                                <div id="reschedule-slots-container-{{ $booking->id }}" class="row row-cols-3 g-2">
                                                    <!-- JS slots -->
                                                </div>
                                                <input type="hidden" name="start_time" id="reschedule-selected-time-{{ $booking->id }}" value="{{ $booking->start_time }}" required>
                                                <div id="reschedule-slots-loader-{{ $booking->id }}" class="text-center d-none py-2">
                                                    <div class="spinner-border text-teal spinner-border-sm" role="status"></div>
                                                </div>
                                                <div id="reschedule-slots-empty-{{ $booking->id }}" class="alert alert-warning d-none text-center py-2 small">
                                                    لا توجد مواعيد متاحة في هذا اليوم.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary btn-sm rounded-pill" data-bs-dismiss="modal">إلغاء</button>
                                            <button type="submit" class="btn btn-premium btn-sm" id="reschedule-submit-btn-{{ $booking->id }}">تأكيد وحفظ الموعد الجديد</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-secondary">
                                <i class="bi bi-calendar-x fs-1 d-block mb-3"></i>
                                لم يتم العثور على أي حجوزات مطابقة للتصفية.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center py-4">
            {{ $bookings->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- Add Booking Modal -->
<div class="modal fade" id="addBookingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">إضافة حجز يدوي جديد</h5>
                <button type="button" class="btn-close ms-0" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.bookings.store') }}" method="POST" id="manualBookingForm">
                @csrf
                <div class="modal-body p-4">
                    <!-- Select Patient -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold">اختر المريض:</label>
                        <select name="patient_id" class="form-select" required>
                            <option value="" disabled selected>-- اختر المريض --</option>
                            @foreach($allPatients as $patient)
                                <option value="{{ $patient->id }}">{{ $patient->name }} ({{ $patient->phone }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Booking Type & Consultation Type -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">نوع الحجز:</label>
                            <select name="booking_type" id="modal-booking-type" class="form-select" onchange="onBookingTypeChange()">
                                <option value="clinic" selected>حجز في العيادة 🏥</option>
                                <option value="online">استشارة أونلاين 🌐</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">قناة التواصل:</label>
                            <select name="consultation_type" id="modal-consultation-type" class="form-select">
                                <option value="clinic" selected>في المقر (العيادة)</option>
                                <option value="chat">محادثة شات 💬</option>
                                <option value="voice">مكالمة صوتية 📞</option>
                                <option value="video">مكالمة فيديو 📹</option>
                            </select>
                        </div>
                    </div>

                    <!-- Select Service -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold">اختر الخدمة الطبية:</label>
                        <select name="service_id" id="modal-service-select" class="form-select" onchange="onModalServiceOrDateChange()" required>
                            <option value="" disabled selected>-- اختر الخدمة --</option>
                            @foreach($allServices as $service)
                                <option value="{{ $service->id }}">{{ $service->title }} ({{ $service->duration }} دقيقة) - ${{ $service->price }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Select Date -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold">اختر التاريخ:</label>
                        <input type="date" name="date" id="modal-date-input" class="form-control" min="{{ today()->format('Y-m-d') }}" onchange="onModalServiceOrDateChange()" required>
                    </div>

                    <!-- Select Slot -->
                    <div class="mb-3 d-none" id="modal-slots-step">
                        <label class="form-label small fw-bold">اختر الوقت المتاح:</label>
                        <div id="modal-slots-container" class="row row-cols-3 g-2">
                            <!-- JS slots -->
                        </div>
                        <input type="hidden" name="start_time" id="modal-selected-time" required>
                        <div id="modal-slots-loader" class="text-center d-none py-2">
                            <div class="spinner-border text-teal spinner-border-sm" role="status"></div>
                        </div>
                        <div id="modal-slots-empty" class="alert alert-warning d-none text-center py-2 small">
                            لا توجد مواعيد متاحة في هذا اليوم.
                        </div>
                    </div>

                    <!-- Payment Status -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold">حالة الدفع الكاش بالعيادة:</label>
                        <select name="payment_status" class="form-select" required>
                            <option value="Paid" selected>مدفوع نقداً بالكامل</option>
                            <option value="Pending">بانتظار الدفع لاحقاً</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-premium btn-sm" id="modal-submit-btn" disabled>تأكيد وتسجيل الحجز</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    function onBookingTypeChange() {
        const bookingType = document.getElementById('modal-booking-type').value;
        const consultationSelect = document.getElementById('modal-consultation-type');
        if (bookingType === 'clinic') {
            consultationSelect.value = 'clinic';
        } else {
            consultationSelect.value = 'video';
        }
    }

    function onModalServiceOrDateChange() {
        const serviceSelect = document.getElementById('modal-service-select');
        const dateInput = document.getElementById('modal-date-input');
        const slotsStep = document.getElementById('modal-slots-step');
        const submitBtn = document.getElementById('modal-submit-btn');

        const serviceId = serviceSelect.value;
        const date = dateInput.value;

        if (!serviceId || !date) {
            slotsStep.classList.add('d-none');
            submitBtn.disabled = true;
            return;
        }

        slotsStep.classList.remove('d-none');
        document.getElementById('modal-slots-container').innerHTML = '';
        document.getElementById('modal-slots-loader').classList.remove('d-none');
        document.getElementById('modal-slots-empty').classList.add('d-none');
        document.getElementById('modal-selected-time').value = '';
        submitBtn.disabled = true;

        fetch(`/api/slots?service_id=${serviceId}&date=${date}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('modal-slots-loader').classList.add('d-none');
                if (data.length === 0) {
                    document.getElementById('modal-slots-empty').classList.remove('d-none');
                    return;
                }

                data.forEach(slot => {
                    const col = document.createElement('div');
                    col.className = 'col';
                    col.innerHTML = `<div class="slot-btn" onclick="selectModalSlot('${slot.start}', this)">${slot.start}</div>`;
                    document.getElementById('modal-slots-container').appendChild(col);
                });
            })
            .catch(err => {
                console.error(err);
                document.getElementById('modal-slots-loader').classList.add('d-none');
                document.getElementById('modal-slots-empty').classList.remove('d-none');
            });
    }

    function selectModalSlot(time, element) {
        const active = document.querySelector('#modal-slots-container .slot-btn.selected');
        if (active) active.classList.remove('selected');

        element.classList.add('selected');
        document.getElementById('modal-selected-time').value = time;
        document.getElementById('modal-submit-btn').disabled = false;
    }

    function loadRescheduleSlots(bookingId, serviceId) {
        const dateInput = document.getElementById(`reschedule-date-${bookingId}`);
        const container = document.getElementById(`reschedule-slots-container-${bookingId}`);
        const loader = document.getElementById(`reschedule-slots-loader-${bookingId}`);
        const emptyAlert = document.getElementById(`reschedule-slots-empty-${bookingId}`);
        const submitBtn = document.getElementById(`reschedule-submit-btn-${bookingId}`);

        const date = dateInput.value;
        if (!date) return;

        container.innerHTML = '';
        loader.classList.remove('d-none');
        emptyAlert.classList.add('d-none');

        fetch(`/api/slots?service_id=${serviceId}&date=${date}`)
            .then(res => res.json())
            .then(data => {
                loader.classList.add('d-none');
                if (data.length === 0) {
                    emptyAlert.classList.remove('d-none');
                    return;
                }

                data.forEach(slot => {
                    const col = document.createElement('div');
                    col.className = 'col';
                    col.innerHTML = `<div class="slot-btn" onclick="selectRescheduleSlot(${bookingId}, '${slot.start}', this)">${slot.start}</div>`;
                    container.appendChild(col);
                });
            })
            .catch(err => {
                console.error(err);
                loader.classList.add('d-none');
                emptyAlert.classList.remove('d-none');
            });
    }

    function selectRescheduleSlot(bookingId, time, element) {
        const active = document.querySelector(`#reschedule-slots-container-${bookingId} .slot-btn.selected`);
        if (active) active.classList.remove('selected');

        element.classList.add('selected');
        document.getElementById(`reschedule-selected-time-${bookingId}`).value = time;
        document.getElementById(`reschedule-submit-btn-${bookingId}`).disabled = false;
    }
</script>
@endsection
@endsection
