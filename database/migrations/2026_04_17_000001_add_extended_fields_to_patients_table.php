<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // MRN – unique per hospital, format MRN-HHHH-XXXXX
            if (!Schema::hasColumn('patients', 'mrn')) {
                $table->string('mrn', 30)->nullable()->after('patient_id');
                $table->index(['hospital_id', 'mrn'], 'patients_hospital_mrn_idx');
            }

            // Title prefix
            if (!Schema::hasColumn('patients', 'title')) {
                $table->string('title', 10)->nullable()->after('mrn');
            }

            // Occupation
            if (!Schema::hasColumn('patients', 'occupation')) {
                $table->string('occupation', 100)->nullable()->after('is_staff');
            }

            // Alternate phone
            if (!Schema::hasColumn('patients', 'alternate_phone')) {
                $table->string('alternate_phone', 20)->nullable()->after('phone');
            }

            // Address extras
            if (!Schema::hasColumn('patients', 'pin_code')) {
                $table->string('pin_code', 10)->nullable()->after('address');
            }
            if (!Schema::hasColumn('patients', 'district')) {
                $table->string('district', 100)->nullable()->after('pin_code');
            }
            if (!Schema::hasColumn('patients', 'state')) {
                $table->string('state', 100)->nullable()->after('district');
            }

            // Emergency contact
            if (!Schema::hasColumn('patients', 'emergency_contact_name')) {
                $table->string('emergency_contact_name', 150)->nullable()->after('state');
            }
            if (!Schema::hasColumn('patients', 'emergency_contact_relation')) {
                $table->string('emergency_contact_relation', 50)->nullable()->after('emergency_contact_name');
            }
            if (!Schema::hasColumn('patients', 'emergency_contact_phone')) {
                $table->string('emergency_contact_phone', 20)->nullable()->after('emergency_contact_relation');
            }

            // Government IDs
            if (!Schema::hasColumn('patients', 'ayushman_bharat_id')) {
                $table->string('ayushman_bharat_id', 100)->nullable()->after('aadhar_no');
            }

            // Medical history
            if (!Schema::hasColumn('patients', 'chronic_conditions')) {
                $table->json('chronic_conditions')->nullable()->after('known_allergies');
            }
            if (!Schema::hasColumn('patients', 'past_surgical_history')) {
                $table->text('past_surgical_history')->nullable()->after('chronic_conditions');
            }
            if (!Schema::hasColumn('patients', 'current_medications')) {
                $table->text('current_medications')->nullable()->after('past_surgical_history');
            }
            if (!Schema::hasColumn('patients', 'family_history')) {
                $table->text('family_history')->nullable()->after('current_medications');
            }

            // Habits / lifestyle
            if (!Schema::hasColumn('patients', 'smoking_status')) {
                $table->enum('smoking_status', ['Never', 'Current', 'Past'])->default('Never')->after('family_history');
            }
            if (!Schema::hasColumn('patients', 'alcohol_status')) {
                $table->enum('alcohol_status', ['Never', 'Occasional', 'Regular'])->default('Never')->after('smoking_status');
            }

            // Vaccination
            if (!Schema::hasColumn('patients', 'vaccination_status')) {
                $table->enum('vaccination_status', ['Up to date', 'Partial', 'Unknown', 'None'])->default('Unknown')->after('alcohol_status');
            }

            // Social category
            if (!Schema::hasColumn('patients', 'category')) {
                $table->enum('category', ['General', 'OBC', 'SC', 'ST', 'EWS'])->default('General')->after('vaccination_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $columns = [
                'mrn','title','occupation','alternate_phone','pin_code','district','state',
                'emergency_contact_name','emergency_contact_relation','emergency_contact_phone',
                'ayushman_bharat_id','chronic_conditions','past_surgical_history',
                'current_medications','family_history','smoking_status','alcohol_status',
                'vaccination_status','category',
            ];
            foreach ($columns as $col) {
                if (Schema::hasColumn('patients', $col)) {
                    $table->dropColumn($col);
                }
            }
            if (Schema::hasIndex('patients', 'patients_hospital_mrn_idx')) {
                $table->dropIndex('patients_hospital_mrn_idx');
            }
        });
    }
};
