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
        Schema::create('preauth_enhancement_docs', function (Blueprint $table) {
            $table->id();
            $table->integer('preauth_register_id')->nullable();
            $table->string('temp_enhancement_id')->nullable();
            $table->string('name')->nullable();
            $table->string('file')->nullable();
            $table->integer('is_draft')->default(1);
            $table->text('ppd_status')->nullable();
            $table->datetime('ppd_status_verify_date')->nullable();
            $table->text('cex_status')->nullable();
            $table->datetime('cex_status_verify_date')->nullable();
            $table->text('cpd_status')->nullable();
            $table->datetime('cpd_status_verify_date')->nullable();
            $table->text('sha_status')->nullable();
            $table->datetime('sha_status_verify_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preath_enhancement_docs');
    }
};
