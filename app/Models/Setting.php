<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Throwable;

class Setting extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    public const DEFAULTS = [
        'instagram_handle' => 'kinksandqueens',
        'tiktok_handle' => 'kinksandqueens',
        'whatsapp_number' => '',
        'notify_email' => '',
        'announcement' => 'Pre-order only · We do not carry ready stock · Orders arrive within 14 business days or less',
        'footer_note' => 'Jesus is Lord.',
        'accepting_orders' => '1',
    ];

    public static function get(string $key, ?string $default = null): ?string
    {
        $all = static::all_cached();
        if (array_key_exists($key, $all)) {
            // A saved value wins, including one the owner deliberately cleared.
            return $all[$key] ?? $default;
        }

        return $default ?? (self::DEFAULTS[$key] ?? null);
    }

    /** @return array<string, string|null> */
    public static function all_cached(): array
    {
        try {
            return Cache::rememberForever('kq.settings', fn () => static::query()->pluck('value', 'key')->all());
        } catch (Throwable) {
            return [];
        }
    }

    public static function put(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('kq.settings');
    }

    public static function whatsappLink(?string $text = null): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) static::get('whatsapp_number'));
        if ($digits === '') {
            return null;
        }
        $digits = self::normalisePhone($digits);

        return 'https://wa.me/'.$digits.($text ? '?text='.rawurlencode($text) : '');
    }

    /** Trinidad & Tobago numbers: 7 digits get +1 868, 10-digit NANP numbers get the leading 1. */
    public static function normalisePhone(string $digits): string
    {
        $digits = preg_replace('/\D+/', '', $digits);

        return match (strlen($digits)) {
            7 => '1868'.$digits,
            10 => '1'.$digits,
            default => $digits,
        };
    }

    public static function instagramDm(): string
    {
        return 'https://ig.me/m/'.static::get('instagram_handle');
    }

    public static function instagramUrl(): string
    {
        return 'https://www.instagram.com/'.static::get('instagram_handle').'/';
    }

    public static function tiktokUrl(): ?string
    {
        $handle = static::get('tiktok_handle');

        return $handle ? 'https://www.tiktok.com/@'.$handle : null;
    }
}
