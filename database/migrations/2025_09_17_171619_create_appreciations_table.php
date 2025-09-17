<?php
// database/migrations/XXXX_XX_XX_XXXXXX_create_appreciations_table.php

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
        Schema::create('appreciations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->integer('appreciation_number'); // Auto-generated: 1, 2, 3, etc.
            $table->enum('appreciation_type', [
                'spot_recognition',
                'monthly_award',
                'quarterly_award',
                'annual_award',
                'peer_nomination',
                'manager_recognition',
                'customer_feedback',
                'milestone_celebration'
            ]);
            $table->enum('category', [
                'exceptional_performance',
                'innovation',
                'leadership',
                'teamwork',
                'customer_service',
                'problem_solving',
                'mentoring',
                'milestone_achievement',
                'cultural_values',
                'continuous_improvement',
                'safety_excellence',
                'other'
            ])->default('exceptional_performance');
            $table->date('achievement_date');
            $table->date('recognition_date')->default(now());
            $table->string('nominated_by'); // Name of person nominating
            $table->string('approved_by')->nullable(); // Name of person who approved
            $table->string('title'); // Title of the recognition
            $table->text('description'); // Detailed description of achievement
            $table->text('impact_description'); // Business/team impact
            $table->decimal('recognition_value', 10, 2)->nullable(); // Monetary value if applicable
            $table->boolean('public_recognition')->default(true); // Can be shared publicly
            $table->json('team_members_involved')->nullable(); // Other team members involved
            $table->json('skills_demonstrated')->nullable(); // Skills/competencies demonstrated
            $table->json('achievement_metrics')->nullable(); // Quantifiable achievements
            $table->json('peer_nominations')->nullable(); // Peer nomination details
            $table->text('employee_response')->nullable(); // Employee's response/comments
            $table->text('hr_notes')->nullable(); // Additional HR notes
            $table->enum('status', ['draft', 'pending_approval', 'approved', 'published', 'archived'])->default('draft');
            $table->date('publication_date')->nullable(); // When recognition was published/announced
            $table->json('supporting_documents')->nullable(); // File paths to supporting documents
            $table->timestamps();
            $table->softDeletes();

            // Indexes for better performance
            $table->index(['employee_id', 'appreciation_number']);
            $table->index(['appreciation_type', 'category']);
            $table->index('status');
            $table->index('recognition_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appreciations');
    }
};
