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
        Schema::create('succession_planning', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('current_role');
            $table->string('potential_future_role');
            $table->enum('readiness_level', ['ready_now', '1_2_years', '2_3_years', 'not_ready']);
            $table->text('development_plan')->nullable();
            $table->string('mentor_assigned')->nullable();
            $table->json('required_skills')->nullable();
            $table->json('completed_training')->nullable();
            $table->date('assessment_date');
            $table->string('assessed_by');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('succession_planning');
    }
};
