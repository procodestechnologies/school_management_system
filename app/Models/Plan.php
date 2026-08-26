<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
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
        'Staff',
        'Expenditure',
    ];

    /**
     * Pro-tier capabilities *within* a module a plan already has - distinct
     * from MODULES, which grants a whole module or nothing.
     */
    public const FEATURES = [
        'ai_receipt_scanning' => 'AI Receipt Scanning (Fee Management)',
    ];

    /**
     * Prices are held as a bare number, so the currency they are quoted in
     * lives here rather than being repeated by every caller that has to
     * render one. Stated as an assumption in one place so a school billed
     * in something else is a single edit, not a search.
     */
    public const CURRENCY = 'KES';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'billing_cycle',
        'modules',
        'features',
        'is_active',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'modules' => 'array',
            'features' => 'array',
            'price' => 'decimal:2',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
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

    /**
     * The plans a prospective customer may be shown. A plan switched off is
     * withdrawn from sale, not merely hidden from the pricing page, so it
     * has to be absent from the public API too.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * A module key rendered for someone who doesn't work here:
     * "FeeManagement" is what the code calls it, "Fee Management" is what a
     * head teacher reads on a pricing page.
     */
    public static function moduleLabel(string $module): string
    {
        return Str::headline($module);
    }

    public static function featureLabel(string $feature): string
    {
        return self::FEATURES[$feature] ?? Str::headline($feature);
    }

    /**
     * The price as a pricing page shows it. A plan costing nothing reads
     * "Free" rather than "KES 0", which looks like a missing value.
     */
    public function priceLabel(): string
    {
        return (float) $this->price === 0.0
            ? 'Free'
            : self::CURRENCY.' '.number_format((float) $this->price);
    }

    /**
     * What the price is per, for the small print beside it. Null on a
     * one-off charge, where "/lifetime" would read strangely.
     */
    public function periodLabel(): ?string
    {
        return match ($this->billing_cycle) {
            'monthly' => '/month',
            'yearly' => '/year',
            default => null,
        };
    }

    /**
     * Everything the plan grants, as one list of printable lines - the
     * modules it opens up followed by the extras within them.
     *
     * @return array<int, string>
     */
    public function inclusions(): array
    {
        return collect($this->modules ?? [])
            ->filter(fn ($key) => is_string($key) && $key !== '')
            ->map(fn (string $key) => self::moduleLabel($key))
            ->merge(
                collect($this->features ?? [])
                    ->filter(fn ($key) => is_string($key) && $key !== '')
                    ->map(fn (string $key) => self::featureLabel($key))
            )
            ->values()
            ->all();
    }
}
