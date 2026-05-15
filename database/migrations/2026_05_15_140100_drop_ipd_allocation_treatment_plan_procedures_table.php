<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('ipd_allocation_treatment_plan_procedures');
    }

    public function down(): void
    {
        // Restored only if needed; original structure is in 2026_05_13_120000 migration.
    }
};
