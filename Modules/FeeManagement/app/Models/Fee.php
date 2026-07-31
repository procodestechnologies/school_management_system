<?php

namespace Modules\FeeManagement\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Institution\Models\Institution;

class Fee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'institution_id',
        'student_id',
        'parent_id',
        'title',
        'fee_type',
        'amount',
        'amount_paid',
        'due_date',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'due_date' => 'date',
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function getBalanceAttribute(): float
    {
        return round((float) $this->amount - (float) $this->amount_paid, 2);
    }

    public function getStatusAttribute(): string
    {
        if ($this->balance <= 0) {
            return 'paid';
        }

        if ((float) $this->amount_paid > 0) {
            return 'partial';
        }

        if ($this->due_date && $this->due_date->isPast()) {
            return 'overdue';
        }

        return 'pending';
    }
}
