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
        Schema::create('health_insurance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable();
            $table->string('provider_name')->nullable();
            $table->string('policy_number')->unique();
            $table->json('coverage_details')->nullable(); // employee, spouse, children, parents
            $table->date('policy_start_date')->nullable();
            $table->date('policy_end_date')->nullable();
            $table->decimal('annual_premium', 10, 2)->nullable();
            $table->boolean('annual_checkup_used')->default(false);
            $table->date('last_checkup_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_insurance');
    }
};
