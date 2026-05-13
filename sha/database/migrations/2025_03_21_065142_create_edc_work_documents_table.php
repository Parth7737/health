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
        Schema::create('edc_work_documents', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('action_id');
            $table->bigInteger('work_flow_id');
            $table->string('document_type')->nullable();
            $table->string('document')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('edc_work_documents');
    }
};
