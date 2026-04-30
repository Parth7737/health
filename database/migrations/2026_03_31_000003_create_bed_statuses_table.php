<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bed_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('status_name')->unique();
            $table->string('color_code')->default('#999999');
            $table->text('description')->nullable();
        });

        // Insert default statuses
        DB::table('bed_statuses')->insert([
            ['id' => 1, 'status_name' => 'Available', 'color_code' => '#28a745', 'description' => 'उपलब्ध / Available'],
            ['id' => 2, 'status_name' => 'Occupied', 'color_code' => '#dc3545', 'description' => 'व्यस्त / Occupied'],
            ['id' => 3, 'status_name' => 'Maintenance', 'color_code' => '#ffc107', 'description' => 'रखरखाव / Maintenance'],
            ['id' => 4, 'status_name' => 'Reserved', 'color_code' => '#17a2b8', 'description' => 'आरक्षित / Reserved'],
            ['id' => 5, 'status_name' => 'Reserved for Discharge', 'color_code' => '#6c757d', 'description' => 'डिस्चार्ज के लिए / Discharge'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bed_statuses');
    }
};
