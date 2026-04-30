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
        Schema::create('beds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained('hospitals')->onDelete('cascade');
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->foreignId('bed_type_id')->constrained('bed_types')->onDelete('restrict');
            $table->foreignId('bed_status_id')->default(1)->constrained('bed_statuses')->onDelete('restrict');
            $table->string('bed_number');
            $table->string('bed_code')->unique();
            $table->text('notes')->nullable();
            $table->timestamp('last_cleaned_at')->nullable();
            $table->timestamp('last_maintained_at')->nullable();
            $table->timestamps();

            $table->index(['hospital_id', 'room_id']);
            $table->index('bed_status_id');
            $table->index('bed_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beds');
    }
};
