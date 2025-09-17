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
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable();
            $table->string('request_id')->unique()->index();
            $table->string('employee_name'); // Discord username
            $table->string('full_name');
            $table->string('email');
            $table->string('department');
            $table->enum('leave_type', ['Full Day', 'Half Day', 'Emergency']);
            $table->enum('half_day_period', ['First Half', 'Second Half'])->nullable();
            $table->text('reason');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('approver_username')->nullable();
            $table->string('thread_id')->nullable(); // Discord thread ID
            $table->text('rejection_reason')->nullable();
            $table->string('attachment_filename')->nullable();
            $table->string('attachment_path')->nullable();
            $table->decimal('calculated_days', 5, 2)->default(0); // Calculated leave days
            $table->string('leave_category')->default('annual'); // annual, sick, bereavement
            $table->timestamps();

            $table->index(['employee_name', 'status']);
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
