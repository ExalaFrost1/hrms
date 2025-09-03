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
        Schema::create('asset_management', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable();
            $table->string('asset_type')->nullable(); // laptop, desktop, phone, etc.
            $table->string('asset_name')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->unique();
            $table->date('issued_date')->nullable();
            $table->date('return_date')->nullable();
            $table->enum('condition_when_issued', ['new', 'good', 'fair', 'poor'])->nullable();
            $table->enum('condition_when_returned', ['good', 'fair', 'poor', 'damaged'])->nullable();
            $table->decimal('purchase_value', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['issued', 'returned', 'damaged', 'lost'])->default('issued');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_management');
    }
};
