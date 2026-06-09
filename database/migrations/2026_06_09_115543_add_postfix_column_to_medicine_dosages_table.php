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
        Schema::table('medicine_dosages', function (Blueprint $table) {
            $table->string('postfix', 50)->nullable()->after('dosage');
            
            // Drop old unique constraint
            $table->dropUnique(['medicine_unit_id', 'dosage']);
            
            // Add new unique constraint including postfix
            $table->unique(['medicine_unit_id', 'dosage', 'postfix']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicine_dosages', function (Blueprint $table) {
            $table->dropUnique(['medicine_unit_id', 'dosage', 'postfix']);
            $table->unique(['medicine_unit_id', 'dosage']);
            $table->dropColumn('postfix');
        });
    }
};
