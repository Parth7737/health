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
        Schema::create('edc_actions', function (Blueprint $table) {
            $table->id();
            $table->biginteger('hospital_id');
            $table->string('order_id');
            $table->string('last_action')->nmullable();
            $table->string('status')->nmullable();
            $table->string('next_status')->nmullable();
            $table->string('main_status')->nmullable();
            $table->date('submission_date')->nmullable();
            $table->bigInteger('added_by')->nmullable();
            $table->tinyInteger('is_close_action')->default(0);
            $table->tinyInteger('is_stop_payment')->default(0);
            $table->tinyInteger('is_stop_preauth')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('edc_actions');
    }
};
