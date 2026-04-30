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
        Schema::create('bed_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained('hospitals')->onDelete('cascade');
            $table->string('type_name');
            $table->text('description')->nullable();
            $table->foreignId('charge_master_id')->nullable()->constrained('charge_masters')->nullOnDelete();
            $table->decimal('base_charge', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);

            $table->unique(['hospital_id', 'type_name'], 'bed_types_hospital_type_name_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bed_types');
    }
};
