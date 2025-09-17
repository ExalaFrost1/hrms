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
        Schema::create('performance_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable();
            $table->string('review_period')->nullable(); // Remove nullable, make required
            $table->date('review_date');
            $table->decimal('goal_completion_rate', 5, 2)->nullable(); // percentage
            $table->decimal('overall_rating', 3, 1)->nullable(); // 1.0 to 5.0
            $table->text('manager_feedback')->nullable();
            $table->text('peer_feedback')->nullable();
            $table->text('self_assessment')->nullable();
            $table->text('areas_of_strength')->nullable();
            $table->text('areas_for_improvement')->nullable();
            $table->text('development_goals')->nullable();
            $table->json('key_achievements')->nullable(); // Add missing field
            $table->json('skills_demonstrated')->nullable(); // Add missing field
            $table->json('supporting_documents')->nullable(); // Add missing field
            $table->string('reviewed_by');
            $table->enum('status', ['draft', 'submitted', 'approved', 'completed'])->default('draft');
            $table->timestamps();

            // Add indexes for better performance
            $table->index(['employee_id', 'review_date']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_reviews');
    }
};
