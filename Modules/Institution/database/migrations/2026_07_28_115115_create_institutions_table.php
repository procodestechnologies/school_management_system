<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('institutions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Basic Information
            $table->string('name');
            $table->string('code')->unique();
            $table->string('type')->default('School');

            // Contact
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('alternate_phone')->nullable();
            $table->string('website')->nullable();

            // Address
            $table->string('country')->default('Kenya');
            $table->string('county')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_address')->nullable();
            $table->text('physical_address')->nullable();

            // Branding
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();

            // Administration
            $table->string('principal_name')->nullable();
            $table->string('principal_phone')->nullable();

            // Academic
            $table->foreignId('curriculum')->constrained('curricula')->cascadeOnDelete();
            $table->string('education_level')->nullable();

            // Attendance
            $table->string('timezone')->default('Africa/Nairobi');

            // Status
            $table->boolean('is_active')->default(true);
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');

            // Notes
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institutions');
    }
};
