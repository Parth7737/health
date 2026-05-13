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
        Schema::create('hospital_reports', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('hospital_id');
            $table->string('document_type')->nullable();
            $table->string('document')->nullable();
            $table->string('description')->nullable();
            $table->text('remark')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->bigInteger('verifier_id')->nullable();
            $table->string('dec_action')->nullable();
            $table->string('dec_document')->nullable();
            $table->string('dec_remarks')->nullable();
            $table->bigInteger('dec_verifier_id')->nullable();
            $table->string('sec_action')->nullable();
            $table->string('sec_document')->nullable();
            $table->string('sec_remarks')->nullable();
            $table->bigInteger('sec_verifier_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospital_reports');
    }
};
