<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorProfile extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'bio',
        'education',
        'experience',
        'certificates',
        'specialties',
        'social_links',
        'gallery',
    ];

    protected $casts = [
        'education' => 'array',
        'experience' => 'array',
        'certificates' => 'array',
        'specialties' => 'array',
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
