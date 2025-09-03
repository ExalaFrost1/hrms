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
        Schema::create('career_progression', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable();
            $table->date('effective_date')->nullable();
            $table->enum('progression_type', ['promotion', 'transfer', 'acting_role', 'demotion'])->nullable();
            $table->string('from_position')->nullable();
            $table->string('to_position')->nullable();
            $table->string('from_department')->nullable();
            $table->string('to_department')->nullable();
            $table->string('from_grade')->nullable();
            $table->string('to_grade')->nullable();
            $table->text('reason')->nullable();
            $table->string('approved_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('career_progression');
    }
};
