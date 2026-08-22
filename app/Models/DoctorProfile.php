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
        'about_image',
        'education',
        'experience',
        'certificates',
        'specialties',
        'specialties_en',
        'social_links',
        'gallery',
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
     * User relation
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
