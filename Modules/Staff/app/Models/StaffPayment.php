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
 * A single payroll run for one staff member for one month.
 *
 * @property int $id
 * @property int $staff_details_id
 * @property int $institution_id
 * @property int|null $recorded_by
 * @property Carbon $period
 * @property string $gross_amount
 * @property string $allowances
 * @property string $deductions
 * @property string $net_amount
 * @property string $payment_method
 * @property string|null $reference
 * @property string $status
 * @property Carbon|null $paid_at
 * @property string|null $notes
 * @property-read StaffDetails|null $staff
 * @property-read Institution|null $institution
 * @property-read User|null $recordedBy
 */
class StaffPayment extends Model
{
    use HasFactory, SoftDeletes, TetherSyncable;

    /**
     * recorded_by is absent on purpose - who keyed a payslip in is decided
     * by the server from the syncing account, not claimed by the client.
     *
     * @var string[]
     */
    protected array $tetherSyncable = [
        // Present so the value TetherServiceProvider forces onto every
        // inbound mutation survives filtering - never the client's own.
        'institution_id',
        'staff_details_id',
        'period',
        'gross_amount',
        'allowances',
        'deductions',
        'net_amount',
        'payment_method',
        'reference',
        'status',
        'paid_at',
        'notes',
    ];

    protected $fillable = [
        'staff_details_id',
        'institution_id',
        'recorded_by',
        'period',
        'gross_amount',
        'allowances',
        'deductions',
        'net_amount',
        'payment_method',
        'reference',
        'status',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'period' => 'date',
        'paid_at' => 'datetime',
        'gross_amount' => 'decimal:2',
        'allowances' => 'decimal:2',
        'deductions' => 'decimal:2',
        'net_amount' => 'decimal:2',
    ];

    public function staff()
    {
        return $this->belongsTo(StaffDetails::class, 'staff_details_id');
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Take-home pay: gross plus allowances, less deductions.
     */
    public static function calculateNet(float $gross, float $allowances, float $deductions): float
    {
        return round($gross + $allowances - $deductions, 2);
    }
}
