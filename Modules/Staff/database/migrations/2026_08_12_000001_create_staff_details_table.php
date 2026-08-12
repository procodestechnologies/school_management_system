<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_details', function (Blueprint $table) {
            $table->id();
            // Most support staff (cleaners, cooks, security) never sign in,
            // so a login is optional - only staff given a system role
            // (currently Accountant) get a user account attached.
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('staff_number')->nullable()->unique();
            $table->string('job_title')->nullable();
            $table->string('department')->nullable();
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'volunteer'])->default('full_time');
            $table->date('hire_date')->nullable();
            $table->decimal('salary', 12, 2)->nullable();
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
        Schema::dropIfExists('staff_details');
    }
};
