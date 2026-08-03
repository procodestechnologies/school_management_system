<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->unique()->constrained()->cascadeOnDelete();
            $table->text('opening_text')->nullable();
            $table->text('closing_text')->nullable();
            $table->string('signatory_name')->nullable();
            $table->string('signatory_title')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_templates');
    }
};
