<?php
// database/migrations/XXXX_XX_XX_XXXXXX_create_performance_improvement_plans_table.php

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
        Schema::create('performance_improvement_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->integer('pip_number'); // Auto-generated: 1, 2, 3, etc.
            $table->enum('pip_type', [
                'performance_deficiency',
                'behavioral_issues',
                'attendance_problems',
                'skills_gap',
                'goal_achievement',
                'quality_standards',
                'communication_issues',
                'policy_compliance'
            ]);
            $table->enum('severity_level', ['low', 'moderate', 'high', 'critical'])->default('moderate');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('review_frequency', ['weekly', 'bi_weekly', 'monthly'])->default('weekly');
            $table->string('initiated_by'); // Name of person who initiated PIP
            $table->string('supervisor_assigned'); // Assigned supervisor/manager
            $table->string('hr_representative'); // HR person overseeing the PIP
            $table->string('title'); // Brief title of the PIP
            $table->text('performance_concerns'); // Detailed performance issues
            $table->text('root_cause_analysis')->nullable(); // Analysis of underlying causes
            $table->json('specific_objectives'); // Specific, measurable objectives
            $table->json('success_metrics'); // How success will be measured
            $table->json('required_actions'); // Actions employee must take
            $table->text('support_provided'); // Support/resources provided by company
            $table->json('training_requirements')->nullable(); // Required training/development
            $table->json('resources_allocated')->nullable(); // Resources allocated for improvement
            $table->json('milestone_dates')->nullable(); // Important milestone dates
            $table->text('consequences_of_failure'); // What happens if PIP is not successful
            $table->boolean('employee_acknowledgment')->default(false); // Has employee acknowledged
            $table->text('employee_comments')->nullable(); // Employee's response/comments
            $table->text('supervisor_notes')->nullable(); // Supervisor observations and notes
            $table->text('hr_notes')->nullable(); // HR notes and observations
            $table->enum('status', [
                'draft',
                'active',
                'under_review',
                'successful',
                'unsuccessful',
                'terminated',
                'extended'
            ])->default('draft');
            $table->date('completion_date')->nullable(); // When PIP was completed
            $table->enum('final_outcome', [
                'successful_completion',
                'unsuccessful_completion',
                'early_termination',
                'resignation_during_pip',
                'extended_pip',
                'alternative_placement'
            ])->nullable();
            $table->json('supporting_documents')->nullable(); // File paths to supporting documents
            $table->timestamps();
            $table->softDeletes();

            // Indexes for better performance
            $table->index(['employee_id', 'pip_number']);
            $table->index(['pip_type', 'severity_level']);
            $table->index('status');
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_improvement_plans');
    }
};
