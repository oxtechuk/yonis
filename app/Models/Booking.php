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
            'chat' => 'محادثة نصية 💬',
            'voice' => 'مكالمة صوتية 📞',
            'video' => 'مكالمة فيديو 📹',
            'clinic' => 'حجز بالعيادة 🏥',
            default => 'حجز بالعيادة 🏥',
        };
    }

    /**
     * Get Booking Type Label in Arabic
     */
    public function getBookingTypeLabelAttribute(): string
    {
        return match ($this->booking_type) {
            'online' => 'أونلاين 🌐',
            'clinic' => 'في العيادة 🏥',
            default => 'في العيادة 🏥',
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
