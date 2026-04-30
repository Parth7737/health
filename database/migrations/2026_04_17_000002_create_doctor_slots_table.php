<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Doctor time slots – defines available appointment slots per doctor per date.
     * Slots are generated dynamically from work_timings but this table allows
     * overrides and tracks per-date capacity for future appointment booking.
     */
    public function up(): void
    {
        Schema::create('doctor_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained('hospitals')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('staff')->cascadeOnDelete();
            // NULL slot_date means a standing weekly slot (day_of_week applies)
            // non-NULL slot_date means a date-specific override or block
            $table->date('slot_date')->nullable()->comment('Specific date; null = recurring weekly');
            $table->tinyInteger('day_of_week')->nullable()->comment('0=Sun,1=Mon,...,6=Sat; used when slot_date is null');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('slot_label', 60)->comment('Display label e.g. 09:00 AM - 09:20 AM');
            $table->unsignedSmallInteger('max_patients')->default(1);
            $table->boolean('is_blocked')->default(false)->comment('True if slot is closed by admin');
            $table->timestamps();

            $table->index(['hospital_id', 'doctor_id', 'slot_date'], 'ds_hosp_doc_date_idx');
            $table->index(['hospital_id', 'doctor_id', 'day_of_week'], 'ds_hosp_doc_dow_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_slots');
    }
};
