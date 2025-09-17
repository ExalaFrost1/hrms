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
        Schema::create('warnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->integer('warning_number'); // Auto-generated: 1, 2, 3, etc.
            $table->enum('warning_type', [
                'attendance',
                'performance',
                'conduct',
                'policy_violation',
                'safety',
                'harassment',
                'insubordination',
                'other'
            ]);
            $table->enum('severity_level', ['minor', 'moderate', 'major', 'critical'])->default('minor');
            $table->date('incident_date');
            $table->date('warning_date')->default(now());
            $table->string('issued_by'); // Name of HR/Manager who issued the warning
            $table->string('subject'); // Brief subject of the warning
            $table->text('description'); // Detailed description of the incident
            $table->string('incident_location')->nullable(); // Where the incident occurred
            $table->text('witnesses')->nullable(); // Names of witnesses if any
            $table->text('previous_discussions')->nullable(); // Any previous verbal warnings or discussions
            $table->text('expected_improvement'); // What improvement is expected
            $table->text('consequences_if_repeated'); // Consequences if behavior repeats
            $table->date('follow_up_date')->nullable(); // When to follow up on improvement
            $table->boolean('employee_acknowledgment')->default(false); // Has employee acknowledged
            $table->text('employee_comments')->nullable(); // Employee's response/comments
            $table->text('hr_notes')->nullable(); // Additional HR notes
            $table->enum('status', ['active', 'acknowledged', 'resolved', 'escalated'])->default('active');
            $table->date('resolution_date')->nullable(); // When the warning was resolved
            $table->json('supporting_documents')->nullable(); // File paths to supporting documents
            $table->timestamps();
            $table->softDeletes();

            // Indexes for better performance
            $table->index(['employee_id', 'warning_number']);
            $table->index(['warning_type', 'severity_level']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warnings');
    }
};
