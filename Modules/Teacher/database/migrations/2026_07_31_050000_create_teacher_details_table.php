<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->string('phone')->nullable();
            $table->string('employee_number')->nullable()->unique();
            $table->string('department')->nullable();
            $table->string('qualification')->nullable();
            $table->date('hire_date')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->enum('status', ['active', 'on_leave', 'suspended', 'resigned', 'terminated'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['institution_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_details');
    }
};
