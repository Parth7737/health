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
        Schema::create('initiate_verifications', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('hospital_id');
            $table->string('verification_authority');
            $table->string('uuid');
            $table->bigInteger('physical_verifier');
            $table->string('verification_type');
            $table->date('date_of_assignment');
            $table->date('due_date_of_physical_verification');
            $table->string('status')->default('Physical Verification Pending');
            $table->tinyInteger('is_approve')->default(0);
            $table->bigInteger('assigned_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('initiate_verifications');
    }
};
