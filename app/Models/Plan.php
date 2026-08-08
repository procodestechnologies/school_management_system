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

    /**
     * Pro-tier capabilities *within* a module a plan already has - distinct
     * from MODULES, which grants a whole module or nothing.
     */
    public const FEATURES = [
        'ai_receipt_scanning' => 'AI Receipt Scanning (Fee Management)',
    ];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'billing_cycle',
        'modules',
        'features',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'modules' => 'array',
            'features' => 'array',
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

    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? [], true);
    }
}
