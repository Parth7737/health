<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * SHA implant/create: procedure_id[] — many procedures per implant.
     */
    public function up(): void
    {
        Schema::create('implant_procedures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('implant_id')->constrained('implants')->cascadeOnDelete();
            $table->foreignId('procedure_id')->constrained('procedures')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['implant_id', 'procedure_id'], 'uq_impl_proc_pair');
        });

        if (Schema::hasTable('implants')) {
            DB::table('implants')
                ->whereNotNull('procedure_id')
                ->orderBy('id')
                ->chunk(500, function ($rows) {
                    $now = now();
                    foreach ($rows as $row) {
                        DB::table('implant_procedures')->insertOrIgnore([
                            'implant_id' => $row->id,
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
        Schema::dropIfExists('implant_procedures');
    }
};
