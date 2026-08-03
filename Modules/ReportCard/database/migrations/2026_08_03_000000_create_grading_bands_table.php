<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grading_bands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->decimal('min_percentage', 5, 2);
            $table->decimal('max_percentage', 5, 2);
            $table->string('grade', 10);
            $table->string('remark')->nullable();
            $table->timestamps();

            $table->index(['institution_id', 'min_percentage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grading_bands');
    }
};
