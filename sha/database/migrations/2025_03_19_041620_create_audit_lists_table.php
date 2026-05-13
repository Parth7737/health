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
        Schema::create('audit_lists', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('category_id')->nullable();
            $table->bigInteger('sub_category_id')->nullable();
            $table->text('name')->nullable();
            $table->tinyInteger('is_required')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_lists');
    }
};
