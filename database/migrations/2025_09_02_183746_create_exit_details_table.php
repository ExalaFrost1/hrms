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
        Schema::create('exit_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable();
            $table->date('resignation_date')->nullable();
            $table->date('last_working_day')->nullable();
            $table->integer('notice_period_days')->nullable();
            $table->enum('resignation_type', ['voluntary', 'involuntary', 'retirement', 'termination'])->nullable();
            $table->text('resignation_reason')->nullable();
            $table->text('exit_interview_feedback')->nullable();
            $table->json('key_feedback_areas')->nullable(); // career_growth, compensation, management, etc.
            $table->boolean('assets_returned')->default(false);
            $table->boolean('accounts_closed')->default(false);
            $table->boolean('dues_settled')->default(false);
            $table->decimal('final_settlement_amount', 12, 2)->nullable();
            $table->decimal('gratuity_amount', 12, 2)->nullable();
            $table->decimal('outstanding_bonus', 12, 2)->nullable();
            $table->enum('rehire_eligibility', ['yes', 'no', 'conditional'])->nullable();
            $table->text('rehire_notes')->nullable();
            $table->string('processed_by')->nullable();
            $table->date('exit_completed_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exit_details');
    }
};
