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
        Schema::create('career_progression', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable();
            $table->date('effective_date');
            $table->enum('progression_type', ['promotion', 'transfer', 'acting_role', 'demotion']);
            $table->string('from_position');
            $table->string('to_position');
            $table->string('from_department');
            $table->string('to_department');
            $table->string('from_grade')->nullable();
            $table->string('to_grade')->nullable();
            $table->text('reason')->nullable();
            $table->string('approved_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('career_progression');
    }
};
