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
        Schema::create('compliance_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable();
            $table->enum('document_type', [
                'offer_letter',
                'employment_contract',
                'handbook_acknowledgment',
                'tax_documents',
                'bank_details',
                'emergency_contact_form',
                'background_check',
                'reference_letters'
            ])->nullable();
            $table->string('document_name')->nullable();
            $table->string('file_path')->nullable();
            $table->date('submission_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->enum('status', ['pending', 'submitted', 'verified', 'expired', 'rejected']);
            $table->text('notes')->nullable();
            $table->string('verified_by')->nullable();
            $table->date('verified_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance_documents');
    }
};
