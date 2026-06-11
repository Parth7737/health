<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medicine_frequencies', function (Blueprint $table) {
            if (!Schema::hasColumn('medicine_frequencies', 'schedule_times')) {
                $table->json('schedule_times')->nullable()->after('no_of_medicine');
            }
        });
  
        Schema::table('medicine_instructions', function (Blueprint $table) {
            if (!Schema::hasColumn('medicine_instructions', 'meal_relation')) {
                $table->enum('meal_relation', [
                    'none',
                    'before_food',
                    'after_food',
                    'with_food',
                    'empty_stomach',
                ])->default('none')->after('instruction');
            }
        });

        Schema::table('hospitals', function (Blueprint $table) {
            if (!Schema::hasColumn('hospitals', 'mar_breakfast_time')) {
                $table->time('mar_breakfast_time')->default('08:00:00');
            }
            if (!Schema::hasColumn('hospitals', 'mar_lunch_time')) {
                $table->time('mar_lunch_time')->default('13:00:00');
            }
            if (!Schema::hasColumn('hospitals', 'mar_dinner_time')) {
                $table->time('mar_dinner_time')->default('20:00:00');
            }
            if (!Schema::hasColumn('hospitals', 'mar_meal_offset_minutes')) {
                $table->unsignedSmallInteger('mar_meal_offset_minutes')->default(30);
            }
        });
    }

    public function down(): void
    {
        Schema::table('medicine_frequencies', function (Blueprint $table) {
            if (Schema::hasColumn('medicine_frequencies', 'schedule_times')) {
                $table->dropColumn('schedule_times');
            }
        });

        Schema::table('medicine_instructions', function (Blueprint $table) {
            if (Schema::hasColumn('medicine_instructions', 'meal_relation')) {
                $table->dropColumn('meal_relation');
            }
        });

        Schema::table('hospitals', function (Blueprint $table) {
            $columns = ['mar_breakfast_time', 'mar_lunch_time', 'mar_dinner_time', 'mar_meal_offset_minutes'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('hospitals', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
