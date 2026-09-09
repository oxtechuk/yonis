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

    /**
     * Get active system currency code (e.g. IQD, USD, SAR, AED). Default: IQD (الدينار العراقي)
     */
    public static function currencyCode(): string
    {
        return static::get('currency_code', static::get('currency', 'IQD'));
    }

    /**
     * Get active system currency display symbol (e.g. د.ع, $, ر.س). Default: د.ع
     */
    public static function currencySymbol(): string
    {
        return static::get('currency_symbol', 'د.ع');
    }

    /**
     * Format a price with the active currency symbol
     */
    public static function formatPrice($amount): string
    {
        $sym = static::currencySymbol();
        $formatted = number_format((float)$amount, 0);
        return "{$formatted} {$sym}";
    }

    /**
     * Safely resolve image/file URL from settings, preventing nested or duplicate URL prefixes.
     */
    public static function getFileUrl(string $key, ?string $default = null): ?string
    {
        $val = static::get($key, $default);
        if (empty($val)) {
            return $default;
        }

        // Remove any recursive/duplicated prefixes like https://domain.com/storage/https://...
        while (preg_match('/https?:\/\/[^\/]+\/storage\/(https?:\/\/.*)$/i', $val, $m)) {
            $val = $m[1];
        }

        if (preg_match('/^https?:\/\//i', $val)) {
            return $val;
        }

        $cleanPath = ltrim(preg_replace('/^storage\//i', '', ltrim($val, '/')), '/');
        return asset('storage/' . $cleanPath);
    }

    /**
     * Get dynamic list of payment methods with ID, Name, Logo/Image, QR, and Instructions
     *
     * @param bool $onlyEnabled Filter only active payment methods
     * @return array
     */
    public static function getPaymentMethods(bool $onlyEnabled = false): array
    {
        $isAr = app()->getLocale() === 'ar';

        $methods = [
            'zaincash' => [
                'id'           => 'zaincash',
                'name'         => static::get('payment_zaincash_name', $isAr ? 'زين كاش' : 'ZainCash'),
                'name_ar'      => static::get('payment_zaincash_name_ar', 'زين كاش'),
                'name_en'      => static::get('payment_zaincash_name_en', 'ZainCash'),
                'logo'         => static::getFileUrl('payment_zaincash_logo', ''),
                'image'        => static::getFileUrl('payment_zaincash_logo', ''),
                'qr_image'     => static::getFileUrl('payment_zaincash_qr', ''),
                'instructions' => static::get('payment_zaincash_label', $isAr ? 'افتح تطبيق زين كاش وامسح الرمز لإتمام الدفع، ثم أرسل لقطة شاشة الإيصال للدكتور.' : 'Scan QR code via ZainCash app to complete payment, then upload receipt.'),
                'badge'        => $isAr ? 'محفظة إلكترونية' : 'E-Wallet',
                'icon_class'   => 'bi-wallet2',
                'color'        => '#7c3aed',
                'is_enabled'   => static::get('payment_zaincash_enabled', '1') === '1',
            ],
            'superki' => [
                'id'           => 'superki',
                'name'         => static::get('payment_superki_name', 'SuperKi'),
                'name_ar'      => static::get('payment_superki_name_ar', 'SuperKi'),
                'name_en'      => static::get('payment_superki_name_en', 'SuperKi'),
                'logo'         => static::getFileUrl('payment_superki_logo', ''),
                'image'        => static::getFileUrl('payment_superki_logo', ''),
                'qr_image'     => static::getFileUrl('payment_superki_qr', ''),
                'instructions' => static::get('payment_superki_label', $isAr ? 'افتح تطبيق SuperKi وامسح الرمز لإتمام الدفع، ثم أرسل لقطة شاشة الإيصال للدكتور.' : 'Scan QR code via SuperKi app to complete payment, then upload receipt.'),
                'badge'        => $isAr ? 'محفظة إلكترونية' : 'E-Wallet',
                'icon_class'   => 'bi-qr-code-scan',
                'color'        => '#0284c7',
                'is_enabled'   => static::get('payment_superki_enabled', '1') === '1',
            ],
            'card' => [
                'id'           => 'card',
                'name'         => static::get('payment_card_name', $isAr ? 'بطاقة دفع (فيزا / ماستر كارد)' : 'Card (Visa / MasterCard)'),
                'name_ar'      => static::get('payment_card_name_ar', 'بطاقة دفع (فيزا / ماستر كارد)'),
                'name_en'      => static::get('payment_card_name_en', 'Card (Visa / MasterCard)'),
                'logo'         => static::getFileUrl('payment_card_logo', ''),
                'image'        => static::getFileUrl('payment_card_logo', ''),
                'qr_image'     => '',
                'link'         => static::get('payment_card_link', ''),
                'key'          => static::get('payment_card_key', ''),
                'currency'     => static::get('payment_card_currency', 'USD'),
                'instructions' => static::get('payment_card_instructions', $isAr ? 'يمكنك الدفع مباشرة باستخدام أي بطاقة فيزا أو ماستر كارد صادرة محلياً أو دولياً بأمان وسرية تامة.' : 'Pay securely using any local or international Visa / MasterCard.'),
                'badge'        => $isAr ? 'دفع إلكتروني آمن' : 'Secure Online',
                'icon_class'   => 'bi-credit-card-2-front',
                'color'        => '#1e3a8a',
                'is_enabled'   => static::get('payment_card_enabled', '0') === '1',
            ],
            'spaceremit' => [
                'id'           => 'spaceremit',
                'name'         => static::get('payment_spaceremit_name', 'SpaceRemit'),
                'name_ar'      => static::get('payment_spaceremit_name_ar', 'SpaceRemit'),
                'name_en'      => static::get('payment_spaceremit_name_en', 'SpaceRemit'),
                'logo'         => static::getFileUrl('payment_spaceremit_logo', ''),
                'image'        => static::getFileUrl('payment_spaceremit_logo', ''),
                'qr_image'     => '',
                'link'         => '',
                'key'          => static::get('payment_spaceremit_key', ''),
                'currency'     => static::get('payment_spaceremit_currency', 'USD'),
                'instructions' => static::get('payment_spaceremit_instructions', $isAr ? 'بوابة SpaceRemit للدفع السريع والتحويلات المالية.' : 'SpaceRemit payment gateway.'),
                'badge'        => 'SpaceRemit',
                'icon_class'   => 'bi-send-check',
                'color'        => '#10b981',
                'is_enabled'   => static::get('payment_spaceremit_enabled', '0') === '1',
            ],
        ];

        if ($onlyEnabled) {
            $methods = array_filter($methods, fn($m) => !empty($m['is_enabled']));
        }

        return $methods;
    }
}

