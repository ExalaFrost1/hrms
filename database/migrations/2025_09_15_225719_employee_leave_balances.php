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
        Schema::create('employee_leave_balances', function (Blueprint $table) {
            $table->id();
            $table->string('employee_name');
            $table->string('discord_user_id')->unique();
            $table->date('date_of_joining');
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'intern', 'consultant']);

            // Annual Leave
            $table->decimal('annual_entitled', 5, 2)->default(25);
            $table->decimal('annual_taken', 5, 2)->default(0);
            $table->decimal('annual_balance', 5, 2)->default(25);

            // Sick Leave
            $table->decimal('sick_entitled', 5, 2)->default(12);
            $table->decimal('sick_taken', 5, 2)->default(0);
            $table->decimal('sick_balance', 5, 2)->default(12);

            // Bereavement Leave
            $table->decimal('bereavement_entitled', 5, 2)->default(5);
            $table->decimal('bereavement_taken', 5, 2)->default(0);
            $table->decimal('bereavement_balance', 5, 2)->default(5);

            $table->year('year')->default(date('Y'));
            $table->timestamps();

            $table->unique(['discord_user_id', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_leave_balances');
    }
};
