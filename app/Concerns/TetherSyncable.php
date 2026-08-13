<?php

namespace App\Concerns;

use Illuminate\Support\Str;

/**
 * Marks a model as participating in offline sync.
 *
 * Deliberately *not* Tether's own Syncable trait: that one self-registers
 * the model under its real class name, but Tether resolves push mutations
 * through a single flat `model_namespace`. With models spread across
 * Modules\*\Models\*, the two names disagree and the per-model push guard
 * and conflict resolver silently stop firing while writes still apply.
 * TetherServiceProvider registers everything under one aliased namespace
 * instead, so both halves agree. See that provider for the detail.
 *
 * Models using this trait may declare:
 *
 *   protected array $tetherSyncable = ['title', 'amount'];
 *
 * to whitelist which fields an inbound client mutation may write. Omit it
 * and Laravel's $fillable is the only guard.
 */
trait TetherSyncable
{
    public static function bootTetherSyncable(): void
    {
        static::creating(function ($model): void {
            if (blank($model->tether_id)) {
                $model->tether_id = (string) Str::ulid();
            }
        });
    }

    /**
     * Fields an incoming mutation payload is filtered to before it's
     * written. Null means "no filtering beyond $fillable".
     *
     * @return string[]|null
     */
    public function getSyncableFields(): ?array
    {
        return property_exists($this, 'tetherSyncable') ? $this->tetherSyncable : null;
    }
}
