<?php

namespace App\Services;

use App\Models\OpdPatient;
use Carbon\Carbon;

/**
 * OPD queue token (stored + display).
 * Stored format: {MOR|AFT|EVE|NGT}-{seq3}  e.g. EVE-003.
 * Prefix follows slot time (morning/afternoon/evening/night); sequence number is one running counter per hospital per calendar day (all bands share the same sequence).
 */
class OpdTokenNoService
{
    public const BAND_MORNING = 'M';

    public const BAND_AFTERNOON = 'A';

    public const BAND_EVENING = 'E';

    public const BAND_NIGHT = 'N';

    public static function bandTriplet(string $bandLetter): string
    {
        return match (strtoupper($bandLetter)) {
            self::BAND_MORNING => 'MOR',
            self::BAND_AFTERNOON => 'AFT',
            self::BAND_EVENING => 'EVE',
            self::BAND_NIGHT => 'NGT',
            default => throw new \InvalidArgumentException('Unknown OPD slot band letter: ' . $bandLetter),
        };
    }

    public static function tripletToLetter(string $triplet): ?string
    {
        return match (strtoupper($triplet)) {
            'MOR' => self::BAND_MORNING,
            'AFT' => self::BAND_AFTERNOON,
            'EVE' => self::BAND_EVENING,
            'NGT' => self::BAND_NIGHT,
            default => null,
        };
    }

    /**
     * Next token for this hospital and appointment day. Sequence increments once per day across all slot bands; prefix reflects this visit’s slot band.
     * Call only inside a DB transaction (uses lockForUpdate).
     */
    public function nextSequentialToken(int $hospitalId, Carbon $appointmentDate, ?string $slot): string
    {
        $day = $appointmentDate->copy()->startOfDay();
        $bandLetter = $this->slotBandLetter($day, $slot);
        $prefix = $this->storagePrefix($day, $bandLetter);

        $dayStart = $day->copy()->startOfDay();
        $dayEnd = $day->copy()->endOfDay();

        $tokens = OpdPatient::query()
            ->lockForUpdate()
            ->where('hospital_id', $hospitalId)
            ->whereBetween('appointment_date', [$dayStart, $dayEnd])
            ->whereNotNull('token_no')
            ->pluck('token_no');

        $maxSeq = 0;
        foreach ($tokens as $raw) {
            $seq = $this->parseDayWideSequence((string) $raw, $day);
            if ($seq !== null && $seq > $maxSeq) {
                $maxSeq = $seq;
            }
        }

        $next = $maxSeq + 1;
        if ($next > 999) {
            throw new \RuntimeException('Maximum 999 OPD tokens reached for this day.');
        }

        return $prefix . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Prefix for new tokens: "MOR-"
     */
    public function storagePrefix(Carbon $appointmentDay, string $bandLetter): string
    {
        return self::bandTriplet($bandLetter) . '-';
    }

    /**
     * Extract numeric sequence from any stored token for the same calendar day (any band counts toward the daily max).
     */
    private function parseDayWideSequence(string $token, Carbon $appointmentDay): ?int
    {
        $ym = $appointmentDay->format('Ym');
        $ymd = $appointmentDay->format('Ymd');

        if (preg_match('/^(MOR|AFT|EVE|NGT)-(\d{3})$/i', $token, $m)) {
            return (int) $m[2];
        }

        if (preg_match('/^(Mor|Aft|Eve|Ngt|MOR|AFT|EVE|NGT)-(\d{6})-(\d{3})$/', $token, $m)) {
            if ($m[2] !== $ym) {
                return null;
            }

            return (int) $m[3];
        }

        if (preg_match('/^([MAEN])-(\d{2})-(\d{2})-(\d{4})-(\d{3})$/', $token, $m)) {
            $rowYmd = $m[4] . $m[3] . $m[2];
            if ($rowYmd !== $ymd) {
                return null;
            }

            return (int) $m[5];
        }

        if (preg_match('/^(\d{4})(\d{2})(\d{2})([MAEN])(\d{3})$/', $token, $m)) {
            if ($m[1] . $m[2] . $m[3] !== $ymd) {
                return null;
            }

            return (int) $m[5];
        }

        return null;
    }

    public function slotBandLetter(Carbon $appointmentDate, ?string $slot): string
    {
        $start = $this->resolveSlotStartDateTime($appointmentDate, $slot);
        $hour = (int) $start->format('H');
        $minute = (int) $start->format('i');
        $t = $hour * 60 + $minute;

        if ($t < 12 * 60) {
            return self::BAND_MORNING;
        }
        if ($t < 17 * 60) {
            return self::BAND_AFTERNOON;
        }
        if ($t < 21 * 60) {
            return self::BAND_EVENING;
        }

        return self::BAND_NIGHT;
    }

    public function resolveSlotStartDateTime(Carbon $appointmentDate, ?string $slot): Carbon
    {
        if (! empty($slot)) {
            $startPart = trim(explode('-', (string) $slot)[0] ?? '');

            if ($startPart !== '') {
                try {
                    return Carbon::createFromFormat('h:i A', $startPart)
                        ->setDate($appointmentDate->year, $appointmentDate->month, $appointmentDate->day);
                } catch (\Throwable) {
                    // fall through
                }
            }
        }

        return $appointmentDate->copy();
    }

    public static function formatForDisplay(?string $token): string
    {
        if ($token === null || $token === '') {
            return '-';
        }

        if (preg_match('/^(MOR|AFT|EVE|NGT)-(\d{3})$/i', $token, $m)) {
            return strtoupper($m[1]) . '-' . $m[2];
        }

        if (preg_match('/^(Mor|Aft|Eve|Ngt|MOR|AFT|EVE|NGT)-(\d{6})-(\d{3})$/', $token, $m)) {
            $label = match (strtoupper($m[1])) {
                'MOR' => 'Morning',
                'AFT' => 'Afternoon',
                'EVE' => 'Evening',
                'NGT' => 'Night',
                default => $m[1],
            };
            $monthName = Carbon::createFromFormat('Ym', $m[2])->translatedFormat('M Y');

            return $label . ' · ' . $monthName . ' · #' . $m[3];
        }

        if (preg_match('/^([MAEN])-(\d{2})-(\d{2})-(\d{4})-(\d{3})$/', $token, $m)) {
            $band = match ($m[1]) {
                self::BAND_MORNING => 'Morning',
                self::BAND_AFTERNOON => 'Afternoon',
                self::BAND_EVENING => 'Evening',
                self::BAND_NIGHT => 'Night',
                default => 'Band ' . $m[1],
            };

            return $band . ' · ' . $m[2] . '-' . $m[3] . '-' . $m[4] . ' · #' . $m[5];
        }

        if (preg_match('/^(\d{4})(\d{2})(\d{2})([MAENU])(\d{3})$/', $token, $m)) {
            $dateStr = $m[3] . '-' . $m[2] . '-' . $m[1];
            $band = match ($m[4]) {
                self::BAND_MORNING => 'Morning',
                self::BAND_AFTERNOON => 'Afternoon',
                self::BAND_EVENING => 'Evening',
                self::BAND_NIGHT => 'Night',
                'U' => 'Unbanded',
                default => 'Band ' . $m[4],
            };

            return $dateStr . ' · ' . $band . ' · #' . $m[5];
        }

        if (preg_match('/^(\d{4})(\d{2})(\d{2})L(\d{1,4})$/', $token, $m)) {
            return $m[3] . '-' . $m[2] . '-' . $m[1] . ' · Legacy · #' . ltrim($m[4], '0');
        }

        return $token;
    }

    public static function formatShort(?string $token): string
    {
        if ($token === null || $token === '') {
            return '-';
        }

        $compressed = self::compressTokenToShort((string) $token);

        return $compressed ?? (string) $token;
    }

    /**
     * Normalize any supported legacy token to stored short form, or null if unrecognized.
     */
    public static function compressTokenToShort(string $token): ?string
    {
        $t = trim($token);
        if ($t === '') {
            return null;
        }

        if (preg_match('/^(MOR|AFT|EVE|NGT)-(\d{3})$/i', $t, $m)) {
            return strtoupper($m[1]) . '-' . $m[2];
        }

        if (preg_match('/^(Mor|Aft|Eve|Ngt|MOR|AFT|EVE|NGT)-(\d{6})-(\d{3})$/', $t, $m)) {
            return strtoupper($m[1]) . '-' . $m[3];
        }

        if (preg_match('/^([MAEN])-(\d{2})-(\d{2})-(\d{4})-(\d{3})$/', $t, $m)) {
            return self::bandTriplet($m[1]) . '-' . $m[5];
        }

        if (preg_match('/^(\d{4})(\d{2})(\d{2})([MAEN])(\d{3})$/', $t, $m)) {
            return self::bandTriplet($m[4]) . '-' . $m[5];
        }

        return null;
    }

    public static function migrateLegacyNumericToken(string $appointmentDate, ?string $slot, int|string $legacyToken): string
    {
        $day = Carbon::parse($appointmentDate)->startOfDay();
        $svc = new self;
        $band = $svc->slotBandLetter($day, $slot);

        return $svc->storagePrefix($day, $band) . str_pad((string) (int) $legacyToken, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Convert Ymd+band+seq (12 chars) to MOR-###.
     */
    public static function hyphenateCompactToken(string $compact): ?string
    {
        if (! preg_match('/^(\d{4})(\d{2})(\d{2})([MAEN])(\d{3})$/', $compact, $m)) {
            return null;
        }

        return self::bandTriplet($m[4]) . '-' . $m[5];
    }

    /**
     * Convert M-dd-mm-yyyy-seq to MOR-###.
     */
    public static function hyphenateDayFormatToMonthTriplet(string $token): ?string
    {
        if (! preg_match('/^([MAEN])-(\d{2})-(\d{2})-(\d{4})-(\d{3})$/', $token, $m)) {
            return null;
        }

        return self::bandTriplet($m[1]) . '-' . $m[5];
    }
}
