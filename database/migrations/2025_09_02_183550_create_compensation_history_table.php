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
        Schema::create('compensation_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable();
            $table->date('effective_date')->nullable();
            $table->enum('action_type', ['joining', 'increment', 'promotion', 'bonus', 'adjustment'])->nullable();
            $table->decimal('new_salary', 12, 2)->nullable();
            $table->decimal('previous_salary', 12, 2)->nullable();
            $table->decimal('bonus_amount', 12, 2)->nullable();
            $table->decimal('incentive_amount', 12, 2)->nullable();
            $table->decimal('adjustment_amount', 12, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->string('approved_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compensation_history');
    }
};
