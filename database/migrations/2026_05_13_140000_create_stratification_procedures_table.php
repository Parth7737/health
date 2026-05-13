<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * SHA-style: one stratification linked to many procedures (procedure_id[]).
     */
    public function up(): void
    {
        Schema::create('stratification_procedures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stratification_id');
            $table->unsignedBigInteger('procedure_id');
            $table->timestamps();

            $table->unique(['stratification_id', 'procedure_id'], 'uq_strat_proc_pair');

            $table->foreign('stratification_id', 'fk_sp_strat')->references('id')->on('stratifications')->cascadeOnDelete();
            $table->foreign('procedure_id', 'fk_sp_proc')->references('id')->on('procedures')->cascadeOnDelete();
        });

        if (Schema::hasTable('stratifications')) {
            DB::table('stratifications')
                ->whereNotNull('procedure_id')
                ->orderBy('id')
                ->chunk(500, function ($rows) {
                    $now = now();
                    foreach ($rows as $row) {
                        DB::table('stratification_procedures')->insertOrIgnore([
                            'stratification_id' => $row->id,
                            'procedure_id' => $row->procedure_id,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('stratification_procedures');
    }
};
