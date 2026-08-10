<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Term labels ("Second Term") repeat every year with nothing else to
     * distinguish them, so result-matching for a report card would blend
     * every year's "Second Term" exams together. This column lets that
     * matching be scoped per year.
     */
    public function up(): void
    {
        Schema::table('examinations', function (Blueprint $table) {
            $table->unsignedSmallInteger('academic_year')->nullable()->after('term');
        });

        // YEAR() is MySQL-only; SQLite (used in tests) has no such
        // function and extracts the year via strftime() instead.
        $yearExpression = DB::connection()->getDriverName() === 'sqlite'
            ? "CAST(strftime('%Y', exam_date) AS INTEGER)"
            : 'YEAR(exam_date)';

        DB::statement("UPDATE examinations SET academic_year = {$yearExpression} WHERE academic_year IS NULL");
    }

    public function down(): void
    {
        Schema::table('examinations', function (Blueprint $table) {
            $table->dropColumn('academic_year');
        });
    }
};
