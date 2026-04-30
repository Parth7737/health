<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Indian States master
        Schema::create('indian_states', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('code', 5)->nullable();
            $table->boolean('is_active')->default(true);
        });

        // Indian Districts master
        Schema::create('indian_districts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('state_id')->constrained('indian_states')->cascadeOnDelete();
            $table->string('name', 100);
            $table->boolean('is_active')->default(true);
            $table->index(['state_id', 'name'], 'districts_state_name_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indian_districts');
        Schema::dropIfExists('indian_states');
    }
};
