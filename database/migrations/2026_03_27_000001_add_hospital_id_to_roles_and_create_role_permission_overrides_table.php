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
        Schema::table('roles', function (Blueprint $table) {
            if (!Schema::hasColumn('roles', 'hospital_id')) {
                $table->unsignedBigInteger('hospital_id')->nullable()->after('is_custom');
                $table->index('hospital_id');
            }
        });

        Schema::create('hospital_role_permission_overrides', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->unsignedBigInteger('role_id');
            $table->string('permission_name');
            $table->boolean('is_allowed')->default(false);
            $table->timestamps();

            $table->unique(['hospital_id', 'role_id', 'permission_name'], 'hosp_role_perm_override_unique');
            $table->index(['hospital_id', 'role_id'], 'hosp_role_perm_override_lookup');

            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospital_role_permission_overrides');

        Schema::table('roles', function (Blueprint $table) {
            if (Schema::hasColumn('roles', 'hospital_id')) {
                $table->dropIndex(['hospital_id']);
                $table->dropColumn('hospital_id');
            }
        });
    }
};
