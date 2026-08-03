<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('examinations', function (Blueprint $table) {
            // subject_name keeps the old free-text value around for
            // display fallback; subject_id (added below) is the new
            // source of truth, linking to the Subject module.
            $table->string('subject_name')->nullable()->after('subject');
            $table->foreignId('subject_id')->nullable()->after('subject_name')
                ->constrained('subjects')->nullOnDelete();
        });

        DB::table('examinations')->update(['subject_name' => DB::raw('subject')]);

        Schema::table('examinations', function (Blueprint $table) {
            $table->dropColumn('subject');
        });
    }

    public function down(): void
    {
        Schema::table('examinations', function (Blueprint $table) {
            $table->string('subject')->nullable()->after('title');
        });

        DB::table('examinations')->update(['subject' => DB::raw('subject_name')]);

        Schema::table('examinations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('subject_id');
            $table->dropColumn('subject_name');
        });
    }
};
