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
        Schema::create('insurance_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable();
            $table->string('claim_number')->unique();
            $table->date('claim_date')->nullable();
            $table->string('claim_type')->nullable(); // consultation, procedure, medication, etc.
            $table->decimal('claim_amount', 10, 2)->nullable();
            $table->decimal('approved_amount', 10, 2)->nullable();
            $table->enum('status', ['submitted', 'under_review', 'approved', 'rejected', 'paid'])->nullable();
            $table->text('description')->nullable();
            $table->json('documents')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_claims');
    }
};
