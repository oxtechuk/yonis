<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * In-memory request cache for instant O(1) resolution
     */
    protected static ?array $settingsCache = null;

    /**
     * Get all settings cached in memory & Redis/File cache
     */
    public static function allCached(): array
    {
        if (static::$settingsCache !== null) {
            return static::$settingsCache;
        }

        static::$settingsCache = Cache::rememberForever('global_app_settings_keyval', function () {
            try {
                return self::query()->pluck('value', 'key')->toArray();
            } catch (\Throwable $e) {
                return [];
            }
        });

        return static::$settingsCache ?? [];
    }

    /**
     * Get a setting value by key with zero database queries on hot path.
     */
    public static function get(string $key, $default = null): ?string
    {
        $all = static::allCached();
        if (array_key_exists($key, $all)) {
            return $all[$key] !== null ? (string)$all[$key] : $default;
        }
        return $default;
    }

    /**
     * Set/Update a setting value by key and automatically invalidate caches.
     */
    public static function set(string $key, ?string $value): self
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        // Invalidate both cache layers
        static::$settingsCache = null;
        Cache::forget('global_app_settings_keyval');

        return $setting;
    }

    /**
     * Clear all cached settings manually
     */
    public static function clearCache(): void
    {
        static::$settingsCache = null;
        Cache::forget('global_app_settings_keyval');
    }
}
