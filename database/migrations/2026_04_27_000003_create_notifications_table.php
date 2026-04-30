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
        if (Schema::hasTable('notifications')) {
            return;
        }

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('hospital_id')->nullable();
            $table->string('type')->nullable();
            $table->date('date')->nullable();
            $table->text('message')->nullable();
            $table->boolean('is_read')->default(false);
            $table->unsignedBigInteger('ref_id')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_read'], 'notif_user_read_idx');
            $table->index('hospital_id', 'notif_hospital_idx');
            $table->index('type', 'notif_type_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
