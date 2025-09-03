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
        Schema::create('learning_development', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable();
            $table->string('training_name')->nullable();
            $table->enum('training_type', ['mandatory', 'technical', 'soft_skills', 'certification', 'conference'])->nullable();
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['enrolled', 'in_progress', 'completed', 'cancelled'])->nullable();
            $table->string('provider')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->string('certificate_url')->nullable();
            $table->date('certificate_expiry')->nullable();
            $table->integer('rating')->nullable(); // 1-5
            $table->text('feedback')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_development');
    }
};
