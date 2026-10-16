<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSlider extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'image_path',
        'action_type',
        'action_value',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $appends = [
        'image_url',
    ];

    /**
     * Get full image URL
     */
    public function getImageUrlAttribute(): string
    {
        if (!empty($this->image_path)) {
            if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
                return $this->image_path;
            }
            return asset('storage/' . ltrim($this->image_path, '/'));
        }

        return '';
    }

    /**
     * Scope active sliders
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->orderBy('sort_order', 'asc')
                     ->orderBy('id', 'asc');
    }
}
