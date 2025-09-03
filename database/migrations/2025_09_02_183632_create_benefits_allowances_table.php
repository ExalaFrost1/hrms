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
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->decimal('internet_allowance', 10, 2)->default(0);
            $table->decimal('medical_allowance', 10, 2)->default(0);
            $table->decimal('home_office_setup', 10, 2)->default(1000.00);
            $table->boolean('home_office_setup_claimed')->default(false);
            $table->date('laptop_issued_date')->nullable();
            $table->string('laptop_model')->nullable();
            $table->string('laptop_serial')->nullable();
            $table->boolean('birthday_allowance_claimed')->default(false);
            $table->year('year')->default(now()->year);
            $table->timestamps();
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
