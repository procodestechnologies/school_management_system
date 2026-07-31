<?php

namespace Modules\Classes\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Classes\Models\SchoolClass;
use Modules\Institution\Models\Institution;

class ClassesDatabaseSeeder extends Seeder
{
    /**
     * A handful of classes per institution, covering a few grade levels.
     */
    private const CLASSES = [
        ['name' => 'Grade 7 East', 'level' => 'Grade 7', 'capacity' => 35],
        ['name' => 'Grade 8 East', 'level' => 'Grade 8', 'capacity' => 35],
        ['name' => 'Grade 9 East', 'level' => 'Grade 9', 'capacity' => 30],
        ['name' => 'Grade 9 West', 'level' => 'Grade 9', 'capacity' => 30],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Institution::all()->each(function (Institution $institution) {
            foreach (self::CLASSES as $class) {
                SchoolClass::firstOrCreate(
                    ['institution_id' => $institution->id, 'name' => $class['name']],
                    ['level' => $class['level'], 'capacity' => $class['capacity']]
                );
            }
        });
    }
}
