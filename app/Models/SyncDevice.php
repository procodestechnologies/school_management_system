<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\PersonalAccessToken;
use Modules\Institution\Models\Institution;

/**
 * @property int $id
 * @property int $institution_id
 * @property int $user_id
 * @property string $name
 * @property string $platform
 * @property string $client_id
 * @property int|null $token_id
 * @property Carbon|null $last_synced_at
 * @property string|null $last_seen_ip
 * @property Carbon|null $revoked_at
 * @property-read User|null $user
 * @property-read Institution|null $institution
 */
class SyncDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'user_id',
        'name',
        'platform',
        'client_id',
        'token_id',
        'last_synced_at',
        'last_seen_ip',
        'revoked_at',
    ];

    protected function casts(): array
    {
        return [
            'last_synced_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function token()
    {
        return $this->belongsTo(PersonalAccessToken::class, 'token_id');
    }

    /**
     * A device can only sync while it still holds a token. Revoking
     * deletes the token and leaves the record behind.
     */
    public function isActive(): bool
    {
        return $this->revoked_at === null && $this->token_id !== null;
    }
}
