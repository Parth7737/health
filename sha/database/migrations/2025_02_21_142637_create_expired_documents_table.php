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
        Schema::create('expired_documents', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('hospital_id')->nullable();
            $table->bigInteger('notification_id')->nullable();
            $table->bigInteger('document_id')->nullable();
            $table->string('document_ref_table')->nullable();
            $table->string('document_name')->nullable();
            $table->date('expiry_date')->nullable();
            $table->tinyInteger('is_updated')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expired_documents');
    }
};
