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
        Schema::create('leave_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable();
            $table->integer('annual_leave_quota')->default(21);
            $table->integer('annual_leave_used')->default(0);
            $table->integer('sick_leave_used')->default(0);
            $table->integer('casual_leave_used')->default(0);
            $table->integer('unpaid_leave_used')->default(0);
            $table->decimal('average_login_hours', 5, 2)->nullable();
            $table->decimal('on_time_attendance_rate', 5, 2)->nullable();
            $table->year('year')->default(now()->year);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_attendance');
    }
};
