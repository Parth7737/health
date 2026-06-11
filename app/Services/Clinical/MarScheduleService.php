<?php

namespace App\Services\Clinical;

use App\Models\IpdPrescriptionItem;
use App\Models\MedicineFrequency;
use Carbon\Carbon;

class MarScheduleService
{
    public const MEAL_LABELS = [
        'none' => 'Any Time',
        'before_food' => 'Before Food',
        'after_food' => 'After Food',
        'with_food' => 'With Food',
        'empty_stomach' => 'Empty Stomach',
    ];

    /**
     * Build daily administration slots for a prescription item.
     *
     * Priority:
     * 1. Frequency MAR schedule_times (explicit) — used as-is; meal instruction is informational only.
     * 2. No explicit times + meal instruction — derive slots from hospital meal times.
     * 3. No explicit times + no meal rule — auto slots from no_of_medicine count.
     *
     * @return array<int, array{scheduled_time: string, meal_relation: string, meal_label: string, time_source: string}>
     */
    public function buildDailySlots(IpdPrescriptionItem $item, Carbon $date, array $mealSettings): array
    {
        $frequency = $item->frequency;
        $mealRelation = $item->instruction?->meal_relation ?? 'none';
        $explicitTimes = $this->extractScheduleTimes($frequency);

        if (!empty($explicitTimes)) {
            $times = $explicitTimes;

            if ($mealRelation === 'empty_stomach') {
                $times = $this->applyEmptyStomachRule($times, $mealSettings);
            }

            $timeSource = 'frequency';
        } else {
            $count = max(1, (int) ($frequency?->no_of_medicine ?? 1));

            if (in_array($mealRelation, ['before_food', 'after_food', 'with_food'], true)) {
                $times = $this->mealBasedTimes($count, $mealRelation, $mealSettings);
                $timeSource = 'meal';
            } else {
                $times = $this->defaultTimesForCount($count);

                if ($mealRelation === 'empty_stomach') {
                    $times = $this->applyEmptyStomachRule($times, $mealSettings);
                }

                $timeSource = 'auto';
            }
        }

        return collect($times)
            ->unique()
            ->sort()
            ->values()
            ->map(fn (string $time) => [
                'scheduled_time' => $time,
                'meal_relation' => $mealRelation,
                'meal_label' => self::MEAL_LABELS[$mealRelation] ?? 'Any Time',
                'time_source' => $timeSource,
            ])
            ->all();
    }

    /**
     * @return array<int, string> HH:MM
     */
    public function extractScheduleTimes(?MedicineFrequency $frequency): array
    {
        if (!$frequency || empty($frequency->schedule_times)) {
            return [];
        }

        $times = is_array($frequency->schedule_times)
            ? $frequency->schedule_times
            : json_decode((string) $frequency->schedule_times, true);

        if (!is_array($times)) {
            return [];
        }

        return $this->normalizeTimes($times);
    }

    /**
     * Empty stomach doses should be scheduled before breakfast when possible.
     *
     * @param array<int, string> $times
     * @return array<int, string>
     */
    private function applyEmptyStomachRule(array $times, array $mealSettings): array
    {
        if (empty($times)) {
            return ['06:00'];
        }

        $breakfast = $this->timeToMinutes($mealSettings['breakfast'] ?? '08:00');
        $beforeBreakfast = collect($times)
            ->filter(fn (string $time) => $this->timeToMinutes($time) < $breakfast)
            ->values()
            ->all();

        if (!empty($beforeBreakfast)) {
            return $beforeBreakfast;
        }

        $first = $times[0];

        if ($this->timeToMinutes($first) >= $breakfast) {
            return ['06:00'];
        }

        return [$first];
    }

    /**
     * @return array<int, string>
     */
    private function mealBasedTimes(int $count, string $mealRelation, array $mealSettings): array
    {
        $meals = [
            $mealSettings['breakfast'] ?? '08:00',
            $mealSettings['lunch'] ?? '13:00',
            $mealSettings['dinner'] ?? '20:00',
        ];

        $offset = (int) ($mealSettings['offset_minutes'] ?? 30);
        $selectedMeals = array_slice($meals, 0, min($count, count($meals)));

        if ($count > count($meals)) {
            $selectedMeals = array_merge(
                $selectedMeals,
                $this->defaultTimesForCount($count - count($meals))
            );
        }

        if ($mealRelation === 'with_food') {
            return $this->normalizeTimes($selectedMeals);
        }

        $delta = $mealRelation === 'before_food' ? -$offset : $offset;

        return collect($selectedMeals)
            ->map(fn (string $meal) => $this->minutesToTime($this->timeToMinutes($meal) + $delta))
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function defaultTimesForCount(int $count): array
    {
        $presets = [
            1 => ['08:00'],
            2 => ['08:00', '20:00'],
            3 => ['08:00', '14:00', '22:00'],
            4 => ['08:00', '12:00', '18:00', '22:00'],
        ];

        if (isset($presets[$count])) {
            return $presets[$count];
        }

        $start = 8 * 60;
        $end = 22 * 60;
        $step = (int) floor(($end - $start) / max(1, $count - 1));

        $times = [];
        for ($i = 0; $i < $count; $i++) {
            $times[] = $this->minutesToTime($start + ($step * $i));
        }

        return $this->normalizeTimes($times);
    }

    /**
     * @param array<int, mixed> $times
     * @return array<int, string>
     */
    private function normalizeTimes(array $times): array
    {
        return collect($times)
            ->map(function ($time) {
                $time = trim((string) $time);
                if (preg_match('/^\d{1,2}:\d{2}$/', $time)) {
                    [$hour, $minute] = array_map('intval', explode(':', $time));

                    return sprintf('%02d:%02d', $hour, $minute);
                }

                return null;
            })
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    private function timeToMinutes(string $time): int
    {
        [$hour, $minute] = array_map('intval', explode(':', substr($time, 0, 5)));

        return ($hour * 60) + $minute;
    }

    private function minutesToTime(int $minutes): string
    {
        $minutes = max(0, min((24 * 60) - 1, $minutes));

        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }
}
