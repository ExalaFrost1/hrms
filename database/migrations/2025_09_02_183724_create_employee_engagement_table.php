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
        Schema::create('employee_engagement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('activity_type'); // event_participation, recognition, content_contribution
            $table->string('activity_name');
            $table->text('description');
            $table->date('activity_date');
            $table->enum('participation_level', ['attended', 'actively_participated', 'organized', 'led']);
            $table->text('recognition_details')->nullable();
            $table->json('documents')->nullable(); // certificates, photos, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_engagement');
    }
};
