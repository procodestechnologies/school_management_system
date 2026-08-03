<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('examination_id')->constrained('examinations')->cascadeOnDelete();
            $table->decimal('marks_obtained', 6, 2);
            $table->string('grade', 10)->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // One result per student per examination.
            $table->unique(['examination_id', 'student_id']);
            $table->index(['institution_id', 'class_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
