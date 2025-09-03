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
            $table->date('joining_date');
            $table->date('probation_end_date');
            $table->string('initial_department');
            $table->string('initial_role');
            $table->string('initial_grade');
            $table->string('reporting_manager');
            $table->string('current_department');
            $table->string('current_role');
            $table->string('current_grade');
            $table->string('current_manager');
            $table->decimal('initial_salary', 12, 2);
            $table->decimal('current_salary', 12, 2);
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'intern']);
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
