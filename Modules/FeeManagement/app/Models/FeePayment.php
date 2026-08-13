<?php

namespace Modules\FeeManagement\Models;

use App\Concerns\TetherSyncable;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Modules\Institution\Models\Institution;

class FeePayment extends Model
{
    use TetherSyncable;

    protected $fillable = [
        'fee_id',
        'institution_id',
        'student_id',
        'amount',
        'reference',
        'payment_method',
        'paid_at',
        'receipt_path',
        'source',
        'extraction_raw_response',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'date',
            'extraction_raw_response' => 'array',
        ];
    }

    public function fee()
    {
        return $this->belongsTo(Fee::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
