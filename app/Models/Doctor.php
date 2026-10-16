<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'name_en',
        'title',
        'title_en',
        'specialty',
        'specialty_en',
        'avatar',
        'bio',
        'bio_en',
        'phone',
        'email',
        'experience_years',
        'consultation_fee',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'experience_years' => 'integer',
        'consultation_fee' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $appends = [
        'avatar_url',
    ];

    /**
     * Get full avatar URL or default fallback
     */
    public function getAvatarUrlAttribute(): string
    {
        if (!empty($this->avatar)) {
            if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
                return $this->avatar;
            }
            return asset('storage/' . ltrim($this->avatar, '/'));
        }

        return asset('images/default-doctor.png');
    }

    /**
     * Scope active doctors
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->orderBy('sort_order', 'asc')
                     ->orderBy('id', 'asc');
    }

    /**
     * User account relation (if linked)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Bookings for this doctor
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Availabilities for this doctor
     */
    public function availabilities(): HasMany
    {
        return $this->hasMany(Availability::class);
    }

    /**
     * Blocked times for this doctor
     */
    public function blockedTimes(): HasMany
    {
        return $this->hasMany(BlockedTime::class);
    }
}
