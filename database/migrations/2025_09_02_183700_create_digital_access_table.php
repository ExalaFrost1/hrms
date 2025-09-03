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
        Schema::create('digital_access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('platform'); // email, slack, discord, etc.
            $table->string('username_or_email');
            $table->date('access_granted_date');
            $table->date('access_revoked_date')->nullable();
            $table->json('permissions')->nullable(); // store array of permissions
            $table->enum('status', ['active', 'suspended', 'revoked'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digital_access');
    }
};
