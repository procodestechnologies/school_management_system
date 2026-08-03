<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->unsignedInteger('min_electives')->default(0)->after('education_level');
            $table->unsignedInteger('max_electives')->nullable()->after('min_electives');
        });
    }

    public function down(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->dropColumn(['min_electives', 'max_electives']);
        });
    }
};
