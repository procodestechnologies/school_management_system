<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    /**
     * The full set of toggleable feature keys, mapped to their admin-facing
     * label and description. Single source of truth for both the site
     * settings page and SettingSeeder's defaults.
     */
    public const FEATURES = [
        'sms' => [
            'label' => 'SMS notifications',
            'description' => 'Send SMS messages (e.g. fee payment reminders) via the configured SMS provider.',
        ],
        'email_notifications' => [
            'label' => 'Email notifications',
            'description' => 'Send notification emails (e.g. fee payment reminders) to parents.',
        ],
    ];

    protected $fillable = [
        'key',
        'enabled',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
        ];
    }

    /**
     * Whether the given feature is enabled. Cached indefinitely and busted
     * on write by set(), so callers can check this on every request without
     * hitting the database each time.
     */
    public static function isEnabled(string $key): bool
    {
        return Cache::rememberForever(
            "settings.{$key}",
            fn () => static::query()->where('key', $key)->value('enabled') ?? true,
        );
    }

    /**
     * Enable/disable a feature and bust its cached value immediately, so the
     * change is reflected on the very next request.
     */
    public static function set(string $key, bool $enabled): void
    {
        static::query()->updateOrCreate(['key' => $key], ['enabled' => $enabled]);

        Cache::forget("settings.{$key}");
    }
}
