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
        Schema::create('employment_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable();
            $table->date('joining_date')->nullable();
            $table->date('probation_end_date')->nullable();
            $table->string('initial_department')->nullable();
            $table->string('initial_role')->nullable();
            $table->string('initial_grade')->nullable();
            $table->string('reporting_manager')->nullable();
            $table->string('current_department')->nullable();
            $table->string('current_role')->nullable();
            $table->string('current_grade')->nullable();
            $table->string('current_manager')->nullable();
            $table->decimal('initial_salary', 12, 2)->nullable();
            $table->decimal('current_salary', 12, 2)->nullable();
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'intern'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employment_history');
    }
};
