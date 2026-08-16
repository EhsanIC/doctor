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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('doctor_id')->nullable()->constrained('doctor_profiles')->onDelete('set null');

            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->text('description')->nullable();

            $table->enum('status', ['pending', 'approved', 'canceled'])->default('pending');

            // Soft delete
            $table->softDeletes();

            // created_at and updated_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
