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
        Schema::create('hospital_licenses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('hospital_id');
            $table->string('uuid');
            $table->bigInteger('license_id');
            $table->bigInteger('license_type_id');
            $table->text('document');
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('remark')->nullable();
            $table->string('admin_verify_status')->nullable();
            $table->text('admin_verify_remark')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospital_licenses');
    }
};
