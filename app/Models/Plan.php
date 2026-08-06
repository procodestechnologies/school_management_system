<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Institution\Models\Institution;

class Plan extends Model
{
    use HasFactory;

    /**
     * The full set of module keys a plan can grant. Matches the module
     * names in modules_statuses.json / Module::getName(), which is what
     * the sidebar checks a plan's modules list against.
     */
    public const MODULES = [
        'Institution',
        'Student',
        'Parent',
        'Teacher',
        'Attendance',
        'FeeManagement',
        'Examinations',
        'Timetable',
        'Curriculum',
        'Report',
        'Classes',
        'Lesson',
        'Result',
        'Subject',
        'Selections',
        'ReportCard',
    ];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'billing_cycle',
        'modules',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'modules' => 'array',
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function institutions()
    {
        return $this->hasMany(Institution::class, 'subscription_plan');
    }

    public function hasModule(string $module): bool
    {
        return in_array($module, $this->modules ?? [], true);
    }
}
