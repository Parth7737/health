<?php

use App\Services\OpdTokenNoService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Token format: YYYYMMDD + band (M/A/E/N) + 3-digit seq, e.g. 20260501M007.
     * MySQL only; other drivers skip (e.g. sqlite test DBs stay on prior schema).
     */
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        Schema::table('opd_patients', function (Blueprint $table) {
            $table->string('token_no_str', 24)->nullable()->after('token_no');
        });

        DB::table('opd_patients')
            ->whereNotNull('token_no')
            ->orderBy('id')
            ->chunkById(200, function ($rows) {
                foreach ($rows as $r) {
                    $raw = $r->token_no;
                    if ($raw === null || $raw === '') {
                        continue;
                    }
                    $s = (string) $raw;
                    if ($s !== '' && ctype_digit($s)) {
                        $new = OpdTokenNoService::migrateLegacyNumericToken(
                            (string) $r->appointment_date,
                            $r->slot,
                            (int) $s
                        );
                        DB::table('opd_patients')->where('id', $r->id)->update(['token_no_str' => $new]);
                    }
                }
            });

        DB::statement('ALTER TABLE opd_patients DROP COLUMN token_no');
        DB::statement('ALTER TABLE opd_patients CHANGE token_no_str token_no VARCHAR(24) NULL');
    }

    public function down(): void
    {
        throw new \RuntimeException('opd_patients.token_no structured format migration cannot be safely reversed.');
    }
};
