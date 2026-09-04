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
        'payment_url',
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
     * Determine the single channel or channels for this service
     * Returns: 'clinic', 'video', 'voice', 'chat', or 'all'
     */
    public function getChannelType(): string
    {
        if ($this->type === 'clinic') {
            return 'clinic';
        }

        $hasVideo = !is_null($this->video_price) && (float)$this->video_price > 0;
        $hasVoice = !is_null($this->voice_price) && (float)$this->voice_price > 0;
        $hasChat  = !is_null($this->chat_price) && (float)$this->chat_price > 0;

        $count = ($hasVideo ? 1 : 0) + ($hasVoice ? 1 : 0) + ($hasChat ? 1 : 0);

        if ($count === 1) {
            if ($hasVideo) return 'video';
            if ($hasVoice) return 'voice';
            if ($hasChat) return 'chat';
        }

        return 'all';
    }

    /**
     * Get Arabic label for the channel
     */
    public function getChannelLabel(): string
    {
        $channel = $this->getChannelType();
        switch ($channel) {
            case 'clinic':
                return 'كشف في العيادة';
            case 'video':
                return 'فيديو فقط';
            case 'voice':
                return 'صوت فقط';
            case 'chat':
                return 'شات فقط';
            default:
                return 'متعدد القنوات';
        }
    }

    /**
     * Get the single primary price for display
     */
    public function getDisplayPrice(): float
    {
        $channel = $this->getChannelType();
        switch ($channel) {
            case 'clinic':
                return (float) ($this->clinic_price ?? $this->price);
            case 'video':
                return (float) ($this->video_price ?? $this->price);
            case 'voice':
                return (float) ($this->voice_price ?? $this->price);
            case 'chat':
                return (float) ($this->chat_price ?? $this->price);
            default:
                return (float) ($this->video_price ?? ($this->voice_price ?? ($this->chat_price ?? $this->price)));
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

