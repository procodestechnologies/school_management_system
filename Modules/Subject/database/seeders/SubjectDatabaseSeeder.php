<?php

namespace Modules\Subject\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Institution\Models\Institution;
use Modules\Subject\Models\Subject;

class SubjectDatabaseSeeder extends Seeder
{
    /**
     * The full standard 8-4-4 secondary curriculum: name => [code, is_compulsory].
     * Mathematics/English/Kiswahili and Physical/Life Skills Education are
     * compulsory for every student; the rest are elective/optional subjects.
     */
    private const SUBJECTS = [
        'Mathematics' => ['MATH', true],
        'English' => ['ENG', true],
        'Kiswahili' => ['KIS', true],
        'Biology' => ['BIO', false],
        'Chemistry' => ['CHEM', false],
        'Physics' => ['PHY', false],
        'History and Government' => ['HIST', false],
        'Geography' => ['GEO', false],
        'Christian Religious Education' => ['CRE', false],
        'Islamic Religious Education' => ['IRE', false],
        'Business Studies' => ['BST', false],
        'Agriculture' => ['AGR', false],
        'Computer Studies' => ['COMP', false],
        'Home Science' => ['HSC', false],
        'Art and Design' => ['ART', false],
        'Woodwork' => ['WOOD', false],
        'Metalwork' => ['MTW', false],
        'Building Construction' => ['BC', false],
        'French' => ['FRE', false],
        'German' => ['GER', false],
        'Music' => ['MUS', false],
        'Physical Education' => ['PE', true],
        'Life Skills Education' => ['LSE', true],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $institution = Institution::find(1);

        if (! $institution) {
            return;
        }

        foreach (self::SUBJECTS as $name => [$code, $isCompulsory]) {
            // firstOrCreate: (institution_id, name) is unique, and a few of
            // these already exist as real, manually-entered records - leave
            // those untouched rather than overwrite their code/flags.
            Subject::firstOrCreate(
                ['institution_id' => $institution->id, 'name' => $name],
                ['code' => $code, 'is_compulsory' => $isCompulsory, 'is_active' => true]
            );
        }
    }
}
