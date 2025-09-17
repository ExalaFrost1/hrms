<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employee_daily_attendance', function (Blueprint $table) {
            $table->id();
            $table->string('discord_user_id')->index(); // From Discord bot
            $table->date('attendance_date')->index();
            $table->string('employee_name');
            $table->string('display_name');
            $table->enum('status', ['checked_in', 'checked_out', 'on_break', 'screen_sharing', 'offline'])->default('offline');
            $table->timestamp('last_update')->nullable();
            $table->time('total_work_time')->default('00:00:00');
            $table->time('total_break_time')->default('00:00:00');
            $table->time('screen_time')->default('00:00:00');
            $table->timestamp('check_in_time')->nullable();
            $table->timestamp('check_out_time')->nullable();
            $table->timestamp('break_start_time')->nullable();
            $table->timestamp('screen_share_start')->nullable();
            $table->timestamps();

            // Unique constraint to prevent duplicate entries per user per date
            $table->unique(['discord_user_id', 'attendance_date'], 'unique_daily_attendance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_daily_attendance');
    }
};
