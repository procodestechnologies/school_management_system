<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\ReportCard\Support\TermParser;

return new class extends Migration
{
    /**
     * academic_year distinguishes report cards across years (the old
     * unique(student_id, term) constraint would otherwise treat next
     * year's "Second Term" as a duplicate of this year's and silently
     * refuse to create it). term_number is a parsed 1/2/3 used to order
     * and compare terms within a year for the performance trend section.
     */
    public function up(): void
    {
        Schema::table('report_cards', function (Blueprint $table) {
            $table->unsignedSmallInteger('academic_year')->nullable()->after('term');
            $table->unsignedTinyInteger('term_number')->nullable()->after('academic_year');
        });

        foreach (DB::table('report_cards')->select('id', 'term', 'completed_at')->get() as $reportCard) {
            DB::table('report_cards')->where('id', $reportCard->id)->update([
                'academic_year' => \Carbon\Carbon::parse($reportCard->completed_at)->year,
                'term_number' => TermParser::number($reportCard->term),
            ]);
        }

        Schema::table('report_cards', function (Blueprint $table) {
            // Add the replacement unique index before dropping the old one:
            // student_id's foreign key relies on a supporting index, and
            // the old composite unique is currently the only one covering
            // it, so MySQL refuses to drop it until another one exists.
            $table->unique(['student_id', 'term', 'academic_year']);
            $table->dropUnique(['student_id', 'term']);
        });
    }

    public function down(): void
    {
        Schema::table('report_cards', function (Blueprint $table) {
            $table->unique(['student_id', 'term']);
            $table->dropUnique(['student_id', 'term', 'academic_year']);
            $table->dropColumn(['academic_year', 'term_number']);
        });
    }
};
