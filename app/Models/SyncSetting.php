<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Runtime configuration for a paired device.
 *
 * Read during service-provider boot, which can happen before the database
 * exists at all - a freshly installed client, or an artisan command run
 * against an unmigrated database. Every read therefore fails soft to the
 * config default rather than taking the application down.
 */
class SyncSetting extends Model
{
    protected $primaryKey = 'key';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['key', 'value'];

    public static function get(string $key, ?string $default = null): ?string
    {
        try {
            if (! Schema::hasTable('sync_settings')) {
                return $default;
            }

            return static::find($key)?->value ?? $default;
        } catch (Throwable) {
            return $default;
        }
    }

    public static function put(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
