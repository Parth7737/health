<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('hr_training_categories')) {
            Schema::create('hr_training_categories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('hospital_id')->constrained('hospitals')->onDelete('cascade');
                $table->string('name', 150);
                $table->string('description', 500)->nullable();
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->unique(['hospital_id', 'name'], 'hr_training_cat_hosp_name_unique');
                $table->index(['hospital_id', 'is_active'], 'hr_training_cat_hosp_active_idx');
            });
        }

        if (Schema::hasTable('hr_training_programs') && !Schema::hasColumn('hr_training_programs', 'hr_training_category_id')) {
            Schema::table('hr_training_programs', function (Blueprint $table) {
                $table->foreignId('hr_training_category_id')->nullable()->after('hospital_id')->constrained('hr_training_categories')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('hr_training_programs') && Schema::hasColumn('hr_training_programs', 'hr_training_category_id')) {
            Schema::table('hr_training_programs', function (Blueprint $table) {
                $table->dropConstrainedForeignId('hr_training_category_id');
            });
        }

        Schema::dropIfExists('hr_training_categories');
    }
};
