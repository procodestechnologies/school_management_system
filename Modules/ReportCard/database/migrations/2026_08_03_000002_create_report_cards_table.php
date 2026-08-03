<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->string('term');
            $table->decimal('mean_percentage', 5, 2)->nullable();
            $table->string('mean_grade', 10)->nullable();
            $table->enum('status', ['ready', 'sent'])->default('ready');
            $table->string('pdf_path')->nullable();
            $table->timestamp('completed_at');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'term']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_cards');
    }
};
