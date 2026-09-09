<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'title',
        'title_ar',
        'title_en',
        'description',
        'description_ar',
        'description_en',
        'icon',
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

    protected $appends = [
        'icon_url',
        'icon_name',
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'channel_type',
        'channel_label',
        'display_price',
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
     * Get Arabic Title
     */
    public function getTitleArAttribute(): string
    {
        return $this->attributes['title_ar'] ?? ($this->attributes['title'] ?? '');
    }

    /**
     * Get English Title
     */
    public function getTitleEnAttribute(): ?string
    {
        return $this->attributes['title_en'] ?? null;
    }

    /**
     * Get Arabic Description
     */
    public function getDescriptionArAttribute(): ?string
    {
        return $this->attributes['description_ar'] ?? ($this->attributes['description'] ?? null);
    }

    /**
     * Get English Description
     */
    public function getDescriptionEnAttribute(): ?string
    {
        return $this->attributes['description_en'] ?? null;
    }

    /**
     * Localized Title based on current locale or given locale
     */
    public function getLocalizedTitle(?string $locale = null): string
    {
        $loc = $locale ?: app()->getLocale();
        if ($loc === 'en' && !empty($this->title_en)) {
            return $this->title_en;
        }
        return $this->title_ar ?: ($this->title ?? '');
    }

    /**
     * Localized Description based on current locale or given locale
     */
    public function getLocalizedDescription(?string $locale = null): ?string
    {
        $loc = $locale ?: app()->getLocale();
        if ($loc === 'en' && !empty($this->description_en)) {
            return $this->description_en;
        }
        return $this->description_ar ?: ($this->description ?? '');
    }

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
     * Accessor for channel_type
     */
    public function getChannelTypeAttribute(): string
    {
        return $this->getChannelType();
    }

    /**
     * Accessor for channel_label
     */
    public function getChannelLabelAttribute(): string
    {
        return $this->getChannelLabel();
    }

    /**
     * Accessor for display_price
     */
    public function getDisplayPriceAttribute(): float
    {
        return $this->getDisplayPrice();
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
     * Get English label for the channel
     */
    public function getChannelLabelEn(): string
    {
        $channel = $this->getChannelType();
        switch ($channel) {
            case 'clinic':
                return 'Clinic In-Person';
            case 'video':
                return 'Video Only';
            case 'voice':
                return 'Voice Only';
            case 'chat':
                return 'Chat Only';
            default:
                return 'Multi-Channel';
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

    /**
     * Get resolved Icon URL if an image was uploaded or full URL provided
     */
    public function getIconUrlAttribute(): ?string
    {
        if (empty($this->icon)) {
            return null;
        }

        if (preg_match('/^https?:\/\//i', $this->icon)) {
            return $this->icon;
        }

        if (str_starts_with($this->icon, 'services/') || str_starts_with($this->icon, 'uploads/') || str_starts_with($this->icon, 'icons/')) {
            return asset('storage/' . $this->icon);
        }

        return null;
    }

    /**
     * Get icon name / bootstrap icon class / icon identifier
     */
    public function getIconNameAttribute(): string
    {
        return $this->getChannelIcon();
    }

    /**
     * Get icon class for the consultation channel
     */
    public function getChannelIcon(): string
    {
        if (!empty($this->icon) && !preg_match('/^https?:\/\//i', $this->icon) && !str_starts_with($this->icon, 'services/') && !str_starts_with($this->icon, 'uploads/')) {
            return str_starts_with($this->icon, 'bi-') ? $this->icon : ('bi-' . $this->icon);
        }

        $channel = $this->getChannelType();
        switch ($channel) {
            case 'clinic':
                return 'bi-hospital-fill';
            case 'video':
                return 'bi-camera-video-fill';
            case 'voice':
                return 'bi-telephone-fill';
            case 'chat':
                return 'bi-chat-dots-fill';
            default:
                return 'bi-heart-pulse-fill';
        }
    }

    /**
     * Get formatted display price with active currency symbol
     */
    public function getFormattedPrice(): string
    {
        return Setting::formatPrice($this->getDisplayPrice());
    }
}
