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
        Schema::table('hospitals', function (Blueprint $table) {
            $table->string('propritership_document_name')->nullable();
            $table->text('propritership_document')->nullable();
            $table->text('hospital_registration_certificate')->nullable();
            $table->string('total_no_of_beds')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            $table->dropColumn(['propritership_document_name', 'propritership_document', 'hospital_registration_certificate', 'total_no_of_beds']);
        });
    }
};
