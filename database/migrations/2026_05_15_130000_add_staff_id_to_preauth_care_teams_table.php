<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('preauth_care_teams', function (Blueprint $table) {
            if (! Schema::hasColumn('preauth_care_teams', 'staff_id')) {
                $table->unsignedBigInteger('staff_id')->nullable()->after('hospital_team_id')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('preauth_care_teams', function (Blueprint $table) {
            if (Schema::hasColumn('preauth_care_teams', 'staff_id')) {
                $table->dropColumn('staff_id');
            }
        });
    }
};
