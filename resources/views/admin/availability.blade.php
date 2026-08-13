@extends('layouts.admin')

@section('title', 'إدارة مواعيد العمل والتوفر')

@section('content')
<div class="row g-4">
    <!-- Weekly general availability -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold m-0"><i class="bi bi-calendar-range-fill me-1 text-teal" style="color: var(--accent-color);"></i> مواعيد العمل الأسبوعية العامة</h5>
                <p class="text-secondary small m-0 mt-1">حدد أيام العمل بالعيادة وساعات البدء والانتهاء لتوليد المواعيد ديناميكياً.</p>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.availability.update') }}" method="POST">
                    @csrf
                    
                    @php
                        $daysNames = [
                            0 => 'الأحد',
                            1 => 'الاثنين',
                            2 => 'الثلاثاء',
                            3 => 'الأربعاء',
                            4 => 'الخميس',
                            5 => 'الجمعة',
                            6 => 'السبت'
                        ];
                    @endphp

                    <div class="d-flex flex-column gap-3 mb-4">
                        @for($d = 0; $d <= 6; $d++)
                            @php
                                $avail = $availabilities->firstWhere('day_of_week', $d);
                                $isActive = !empty($avail);
                                $start = $isActive ? Carbon\Carbon::parse($avail->start_time)->format('H:i') : '14:00';
                                $end = $isActive ? Carbon\Carbon::parse($avail->end_time)->format('H:i') : '20:00';
                            @endphp
                            
                            <div class="row align-items-center bg-light p-3 rounded-3 g-2">
                                <div class="col-md-3">
                                    <div class="form-check form-switch text-start m-0">
                                        <input class="form-check-input float-end ms-2" type="checkbox" role="switch" name="days[{{ $d }}][active]" value="1" id="day{{ $d }}" @if($isActive) checked @endif>
                                        <label class="form-check-label fw-bold text-dark" for="day{{ $d }}">{{ $daysNames[$d] }}</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">من</span>
                                        <input type="time" name="days[{{ $d }}][start_time]" class="form-control" value="{{ $start }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">إلى</span>
                                        <input type="time" name="days[{{ $d }}][end_time]" class="form-control" value="{{ $end }}">
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>

                    <button type="submit" class="btn btn-premium w-100 py-2.5">حفظ مواعيد التوفر الأسبوعية</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Off days and Blocked times -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold m-0"><i class="bi bi-calendar-x-fill me-1 text-danger"></i> إضافة إجازة استثنائية أو حظر مؤقت</h5>
                <p class="text-secondary small m-0 mt-1">لحظر استقبال أي حجوزات في تاريخ محدد أو ساعات محددة.</p>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.availability.block.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold">التاريخ</label>
                        <input type="date" name="date" class="form-control" min="{{ today()->format('Y-m-d') }}" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">وقت البدء (اختياري)</label>
                            <input type="time" name="start_time" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">وقت الانتهاء (اختياري)</label>
                            <input type="time" name="end_time" class="form-control">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold">السبب أو الملاحظة</label>
                        <input type="text" name="reason" class="form-control" placeholder="مثال: مؤتمر علمي أو إجازة رسمية">
                    </div>
                    <button type="submit" class="btn btn-danger w-100 py-2.5">تسجيل حظر الحجوزات</button>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold m-0">قائمة الإجازات والحظر النشطة</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">التاريخ</th>
                                <th>الفترة</th>
                                <th>الملاحظة</th>
                                <th class="pe-3 text-end">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($blockedTimes as $block)
                                <tr>
                                    <td class="ps-3 fw-bold text-dark">{{ $block->date->format('Y-m-d') }}</td>
                                    <td class="small">
                                        @if($block->start_time && $block->end_time)
                                            {{ Carbon\Carbon::parse($block->start_time)->format('H:i') }} - {{ Carbon\Carbon::parse($block->end_time)->format('H:i') }}
                                        @else
                                            <span class="badge bg-danger">طوال اليوم</span>
                                        @endif
                                    </td>
                                    <td class="text-secondary small">{{ $block->reason ?: '-' }}</td>
                                    <td class="pe-3 text-end">
                                        <form action="{{ route('admin.availability.block.delete', $block->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger border-0"><i class="bi bi-trash-fill"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-secondary small">لا توجد إجازات استثنائية مسجلة.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
