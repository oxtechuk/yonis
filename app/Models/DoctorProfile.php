<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorProfile extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'title_en',
        'bio',
        'bio_en',
        'hero_image',
        'hero_image_mobile',
        'about_image',
        'education',
        'experience',
        'certificates',
        'specialties',
        'specialties_en',
        'social_links',
        'gallery',
    ];

    protected $appends = [
        'hero_image_web',
        'mobile_hero_image',
    ];

    protected $casts = [
        'education' => 'array',
        'experience' => 'array',
        'certificates' => 'array',
        'specialties' => 'array',
        'specialties_en' => 'array',
        'social_links' => 'array',
        'gallery' => 'array',
    ];

    /**
     * Get web hero image attribute (alias for hero_image)
     */
    public function getHeroImageWebAttribute(): ?string
    {
        return $this->hero_image;
    }

    /**
     * Get mobile hero image attribute (falls back to hero_image if not specifically set)
     */
    public function getMobileHeroImageAttribute(): ?string
    {
        return !empty($this->hero_image_mobile) ? $this->hero_image_mobile : $this->hero_image;
    }

    /**
     * User relation
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
