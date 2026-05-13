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
        Schema::create('work_flow_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('hospital_id');
            $table->string('uuid');
            $table->string('action');
            $table->text('remark')->nullable();
            $table->text('attachment')->nullable();
            $table->bigInteger('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_flow_histories');
    }
};
