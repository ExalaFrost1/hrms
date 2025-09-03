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
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('review_period'); // e.g., "Q1 2024", "Annual 2024"
            $table->date('review_date');
            $table->decimal('goal_completion_rate', 5, 2); // percentage
            $table->decimal('overall_rating', 3, 1); // 1.0 to 5.0
            $table->text('manager_feedback')->nullable();
            $table->text('peer_feedback')->nullable();
            $table->text('self_assessment')->nullable();
            $table->text('areas_of_strength')->nullable();
            $table->text('areas_for_improvement')->nullable();
            $table->text('development_goals')->nullable();
            $table->string('reviewed_by');
            $table->enum('status', ['draft', 'submitted', 'approved'])->default('draft');
            $table->timestamps();
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
