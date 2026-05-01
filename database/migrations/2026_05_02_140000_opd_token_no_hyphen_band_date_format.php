<?php

use App\Services\OpdTokenNoService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Stored token: M-01-05-2026-007 (band-DD-MM-YYYY-3digit). Widen column; convert YmdBand### rows.
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
                    $hy = OpdTokenNoService::hyphenateCompactToken($tok);
                    if ($hy !== null && $hy !== $tok) {
                        DB::table('opd_patients')->where('id', $r->id)->update(['token_no' => $hy]);
                    }
                }
            });
    }

    public function down(): void
    {
        throw new \RuntimeException('OPD token hyphen format migration cannot be safely reversed.');
    }
};
