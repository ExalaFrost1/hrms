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
        Schema::create('benefits_allowances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable();
            $table->year('year')->default(now()->year);
            $table->tinyInteger('month')->unsigned(); // 1-12 for months
            $table->decimal('internet_allowance', 10, 2)->default(0)->nullable();
            $table->decimal('medical_allowance', 10, 2)->default(0)->nullable();
            $table->decimal('home_office_setup', 10, 2)->default(1000.00)->nullable();
            $table->boolean('home_office_setup_claimed')->default(false)->nullable();
            $table->boolean('birthday_allowance_claimed')->default(false)->nullable();
            $table->json('other_benefits')->nullable(); // Store array of custom benefits
            $table->timestamps();

            // Ensure unique combination of employee_id, year, and month
            $table->unique(['employee_id', 'year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('benefits_allowances');
    }
};
