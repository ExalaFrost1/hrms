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
        Schema::create('conduct_compliance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable();
            $table->date('incident_date')->nullable();
            $table->enum('type', ['warning', 'show_cause', 'pip', 'disciplinary_action', 'appreciation', 'recognition', 'reward'])->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->text('action_taken')->nullable();
            $table->date('resolution_date')->nullable();
            $table->string('issued_by')->nullable();
            $table->json('documents')->nullable(); // Store file paths
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->nullable();
            $table->enum('status', ['pending', 'in_progress', 'resolved', 'closed'])->default('pending')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conduct_compliance');
    }
};
