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
        Schema::create('annual_declarations', function (Blueprint $table) {
            $table->id();
            $table->string('hospital_id');
            $table->string('year');
            $table->date('submitted_date');
            $table->string('status')->default(0)->comment('1=Done,0=Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annual_declarations');
    }
};
