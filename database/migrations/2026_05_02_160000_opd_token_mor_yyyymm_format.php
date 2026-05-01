<?php

use App\Services\OpdTokenNoService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Normalizes legacy token strings toward short MOR-### form (via compressTokenToShort).
     */
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement('ALTER TABLE opd_patients MODIFY token_no VARCHAR(40) NULL');

        DB::table('opd_patients')
            ->whereNotNull('token_no')
            ->orderBy('id')
            ->chunkById(200, function ($rows) {
                foreach ($rows as $r) {
                    $tok = (string) $r->token_no;
                    $new = OpdTokenNoService::compressTokenToShort($tok);
                    if ($new !== null && $new !== $tok) {
                        DB::table('opd_patients')->where('id', $r->id)->update(['token_no' => $new]);
                    }
                }
            });
    }

    public function down(): void
    {
        throw new \RuntimeException('OPD token Mor-YYYYMM-### migration cannot be safely reversed.');
    }
};
