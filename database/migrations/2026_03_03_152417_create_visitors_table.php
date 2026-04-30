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
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id')->nullable();
            $table->unsignedBigInteger('visitor_purpose_id')->nullable();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('id_card')->nullable();
            $table->integer('number_of_persons')->default(1);
            $table->date('visit_date');
            $table->time('in_time');
            $table->time('out_time')->nullable();
            $table->text('note')->nullable();
            $table->string('document')->nullable();
            $table->timestamps();

            $table->unique(['hospital_id', 'name']);
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreign('visitor_purpose_id')->references('id')->on('visitor_purposes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
