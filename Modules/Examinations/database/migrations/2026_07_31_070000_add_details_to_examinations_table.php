<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('examinations', function (Blueprint $table) {
            $table->foreignId('institution_id')->after('id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('subject');
            $table->string('class_name');
            $table->string('term')->nullable();
            $table->date('exam_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->unsignedInteger('total_marks')->default(100);
            $table->unsignedInteger('passing_marks')->nullable();
            $table->text('notes')->nullable();

            $table->index(['institution_id', 'exam_date']);
        });
    }

    public function down(): void
    {
        Schema::table('examinations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('institution_id');
            $table->dropColumn([
                'title', 'subject', 'class_name', 'term', 'exam_date',
                'start_time', 'end_time', 'total_marks', 'passing_marks', 'notes',
            ]);
        });
    }
};
