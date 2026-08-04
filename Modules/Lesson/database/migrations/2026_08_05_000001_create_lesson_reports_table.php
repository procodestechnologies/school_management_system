<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->enum('type', ['daily', 'weekly']);
            $table->date('period_start');
            $table->date('period_end');
            $table->unsignedInteger('total_lessons')->default(0);
            $table->unsignedInteger('attended_count')->default(0);
            $table->unsignedInteger('not_attended_count')->default(0);
            $table->unsignedInteger('recovered_count')->default(0);
            $table->string('pdf_path')->nullable();
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();

            $table->unique(['class_id', 'type', 'period_start']);
            $table->index(['institution_id', 'class_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_reports');
    }
};
