<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $fillable = [
        'booking_reference',
        'patient_id',
        'service_id',
        'booking_type',
        'consultation_type',
        'price',
        'date',
        'start_time',
        'end_time',
        'title',
        'notes',
        'temp_user_data',
        'status',
        'rescheduled_at',
        'reschedule_count',
    ];

    protected $casts = [
        'date' => 'date',
        'temp_user_data' => 'array',
        'price' => 'decimal:2',
        'rescheduled_at' => 'datetime',
    ];

    /**
     * Get Consultation Type Label in Arabic
     */
    public function getConsultationTypeLabelAttribute(): string
    {
        return match ($this->consultation_type) {
            'chat' => 'محادثة نصية (شات)',
            'voice' => 'مكالمة صوتية',
            'video' => 'مكالمة فيديو',
            'clinic' => 'حجز بالعيادة',
            default => 'حجز بالعيادة',
        };
    }

    /**
     * Get Booking Type Label in Arabic
     */
    public function getBookingTypeLabelAttribute(): string
    {
        return match ($this->booking_type) {
            'online' => 'أونلاين',
            'clinic' => 'في العيادة',
            default => 'في العيادة',
        };
    }

    /**
     * Group status into 4 primary clean categories:
     * - 'pending_payment': بانتظار تأكيد الدفع
     * - 'upcoming': حجز قادم مؤكد
     * - 'completed': مكتمل
     * - 'cancelled': ملغي
     */
    public function getStatusCategoryAttribute(): string
    {
        return match ($this->status) {
            'AwaitingPayment', 'PendingPaymentReview', 'Pending' => 'pending_payment',
            'Confirmed' => 'upcoming',
            'Completed' => 'completed',
            'CancelledByPatient', 'CancelledByDoctor', 'Cancelled' => 'cancelled',
            'NoShow' => 'noshow',
            default => 'other'
        };
    }

    /**
     * Get clean Arabic status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'PendingPaymentReview' => 'بانتظار تأكيد الدفع',
            'AwaitingPayment' => 'بانتظار تأكيد الدفع',
            'Pending' => 'بانتظار المراجعة',
            'Confirmed' => 'حجز قادم مؤكد',
            'Completed' => 'مكتمل',
            'CancelledByPatient' => 'ملغي (بواسطة المريض)',
            'CancelledByDoctor' => 'ملغي (بواسطة الطبيب)',
            'Cancelled' => 'ملغي',
            'NoShow' => 'لم يحضر',
            default => $this->status
        };
    }

    /**
     * Get badge configuration for views
     */
    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status_category) {
            'pending_payment' => [
                'class' => 'bg-warning-subtle text-warning-emphasis border border-warning',
                'icon' => 'bi-hourglass-split',
                'label' => 'بانتظار تأكيد الدفع',
            ],
            'upcoming' => [
                'class' => 'bg-success-subtle text-success border border-success',
                'icon' => 'bi-calendar-check-fill',
                'label' => 'حجز قادم مؤكد',
            ],
            'completed' => [
                'class' => 'bg-info-subtle text-info border border-info',
                'icon' => 'bi-check2-all',
                'label' => 'مكتمل',
            ],
            'cancelled' => [
                'class' => 'bg-danger-subtle text-danger border border-danger',
                'icon' => 'bi-x-circle-fill',
                'label' => str_contains($this->status, 'Patient') ? 'ملغي بواسطة المريض' : (str_contains($this->status, 'Doctor') ? 'ملغي بواسطة الطبيب' : 'ملغي'),
            ],
            default => [
                'class' => 'bg-secondary-subtle text-secondary border border-secondary',
                'icon' => 'bi-dash-circle',
                'label' => 'لم يحضر',
            ]
        };
    }


    /**
     * Patient relation
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Service relation
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Payment relation
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}
