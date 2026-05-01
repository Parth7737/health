<?php

use App\Services\OpdTokenNoService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Stored token: MOR-007 (band + 3 digits). Compresses Mor-YYYYMM-### and other legacy shapes.
     */
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement('ALTER TABLE opd_patients MODIFY token_no VARCHAR(24) NULL');

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
        throw new \RuntimeException('OPD token MOR-### short format migration cannot be safely reversed.');
    }
};
