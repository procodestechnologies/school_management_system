<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Institution\Models\Institution;

class Payment extends Model
{
    protected $fillable = [
        'institution_id',
        'plan_id',
        'initiated_by',
        'reference',
        'gateway_reference',
        'amount',
        'currency',
        'channel',
        'status',
        'paid_at',
        'gateway_response',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'gateway_response' => 'array',
        ];
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function initiatedBy()
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    public function isSuccessful(): bool
    {
        return $this->status === 'successful';
    }
}
