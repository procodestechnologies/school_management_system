<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('examinations', function (Blueprint $table) {
            $table->foreignId('class_id')->nullable()->after('class_name')
                ->constrained('classes')->nullOnDelete();
        });

        // class_name was the old free-text field; class_id is now the
        // source of truth, so it's no longer required going forward.
        Schema::table('examinations', function (Blueprint $table) {
            $table->string('class_name')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('examinations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('class_id');
            $table->string('class_name')->nullable(false)->change();
        });
    }
};
