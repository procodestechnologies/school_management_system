<?php

namespace Modules\Staff\Models;

use App\Concerns\TetherSyncable;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Modules\Institution\Models\Institution;

/**
 * @property int $id
 * @property int|null $user_id
 * @property int $institution_id
 * @property string $name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $staff_number
 * @property string|null $job_title
 * @property string|null $department
 * @property string $employment_type
 * @property Carbon|null $hire_date
 * @property string|null $salary
 * @property string|null $address
 * @property bool $is_active
 * @property string $status
 * @property string|null $notes
 * @property-read User|null $user
 * @property-read Institution|null $institution
 */
class StaffDetails extends Model
{
    use HasFactory, SoftDeletes, TetherSyncable;

    /**
     * user_id is absent on purpose - granting a staff member a login is a
     * server-side act with a role attached, never something an offline
     * device can assign.
     *
     * @var string[]
     */
    protected array $tetherSyncable = [
        // Present so the value TetherServiceProvider forces onto every
        // inbound mutation survives filtering - never the client's own.
        'institution_id',
        'name',
        'email',
        'phone',
        'staff_number',
        'job_title',
        'department',
        'employment_type',
        'hire_date',
        'salary',
        'address',
        'is_active',
        'status',
        'notes',
    ];

    protected $table = 'staff_details';

    protected $fillable = [
        'user_id',
        'institution_id',
        'name',
        'email',
        'phone',
        'staff_number',
        'job_title',
        'department',
        'employment_type',
        'hire_date',
        'salary',
        'address',
        'is_active',
        'status',
        'notes',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'salary' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * The login account attached to this staff member, if they were given
     * one - support staff without system access have none.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function payments()
    {
        return $this->hasMany(StaffPayment::class, 'staff_details_id');
    }

    public function hasSystemAccess(): bool
    {
        return (bool) $this->user_id;
    }
}
