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
        Schema::create('hospital_quality_audits', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('hospital_id');
            $table->bigInteger('category_id');
            $table->bigInteger('sub_category_id');
            $table->bigInteger('audit_id');
            $table->string('action');
            $table->string('year');
            $table->string('month');
            $table->date('submitted_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospital_quality_audits');
    }
};
