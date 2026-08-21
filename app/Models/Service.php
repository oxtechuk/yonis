<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'title',
        'description',
        'type',
        'price',
        'clinic_price',
        'chat_price',
        'voice_price',
        'video_price',
        'duration',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'clinic_price' => 'decimal:2',
        'chat_price' => 'decimal:2',
        'voice_price' => 'decimal:2',
        'video_price' => 'decimal:2',
    ];

    /**
     * Get price for a specific consultation channel
     */
    public function getPriceForChannel(string $consultationType): float
    {
        switch ($consultationType) {
            case 'clinic':
                return (float) ($this->clinic_price ?? $this->price);
            case 'chat':
                return (float) ($this->chat_price ?? $this->price);
            case 'voice':
                return (float) ($this->voice_price ?? $this->price);
            case 'video':
                return (float) ($this->video_price ?? $this->price);
            default:
                return (float) $this->price;
        }
    }

    /**
     * Bookings relation
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}

