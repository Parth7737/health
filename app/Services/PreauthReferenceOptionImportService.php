<?php

namespace App\Services;

use App\Models\PreauthReferenceOption;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class PreauthReferenceOptionImportService
{
    /**
     * SHA master tables → HIMS preauth_reference_options.category
     *
     * @var array<string, string>
     */
    public const SHA_TABLE_CATEGORY_MAP = [
        'diabetes' => 'Diabetes',
        'hypertensions' => 'Hypertension',
        'heart_diseases' => 'HeartDisease',
        'strokes' => 'Stroke',
        'cancers' => 'Cancer',
        'tuberculoses' => 'Tuberculosis',
        'asthmas' => 'Asthma',
        'appetites' => 'Appetite',
        'bowels' => 'Bowels',
        'nutrition' => 'Nutrition',
        'diets' => 'Diet',
        'admission_types' => 'AdmissionType',
    ];

    /**
     * @param  array<string, array<int, array{label: string, sort_order?: int}>>  $categories
     * @return array<string, mixed>
     */
    public function importFromPayload(array $categories, string $mode = 'merge'): array
    {
        $normalized = $this->normalizeCategoriesPayload($categories);

        return DB::transaction(function () use ($normalized, $mode) {
            if ($mode === 'replace_all') {
                PreauthReferenceOption::query()->delete();
            }

            return $this->persistCategories($normalized, $mode);
        });
    }

    /**
     * Pull rows from the configured SHA database connection.
     *
     * @return array<string, mixed>
     */
    public function importFromShaDatabase(string $mode = 'merge'): array
    {
        $connection = (string) config('scheme_preauth.sha_db_connection', 'sha');

        try {
            DB::connection($connection)->getPdo();
        } catch (\Throwable $e) {
            throw new RuntimeException(
                'Cannot connect to SHA database. Configure SHA_DB_* in .env and config/database.php connections.sha.',
                0,
                $e
            );
        }

        $categories = [];
        foreach (self::SHA_TABLE_CATEGORY_MAP as $table => $category) {
            if (! Schema::connection($connection)->hasTable($table)) {
                continue;
            }
            $rows = DB::connection($connection)
                ->table($table)
                ->orderBy('id')
                ->get(['id', 'name']);
            $options = [];
            $sort = 0;
            foreach ($rows as $row) {
                $label = trim((string) ($row->name ?? ''));
                if ($label === '') {
                    continue;
                }
                $options[] = [
                    'label' => $label,
                    'sort_order' => $sort++,
                ];
            }
            if ($options !== []) {
                $categories[$category] = $options;
            }
        }

        if ($categories === []) {
            throw new RuntimeException('No reference rows found in SHA database tables.');
        }

        return $this->importFromPayload($categories, $mode);
    }

    /**
     * @return array<string, Collection<int, PreauthReferenceOption>>
     */
    public function groupedIndex(): array
    {
        $grouped = [];
        PreauthReferenceOption::query()
            ->orderBy('category')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->groupBy('category')
            ->each(function (Collection $items, string $category) use (&$grouped) {
                $grouped[$category] = $items->values();
            });

        ksort($grouped);

        return $grouped;
    }

    /**
     * @param  array<string, array<int, array{label: string, sort_order: int}>>  $categories
     * @return array<string, mixed>
     */
    protected function persistCategories(array $categories, string $mode): array
    {
        $summary = [
            'mode' => $mode,
            'categories' => count($categories),
            'created' => 0,
            'updated' => 0,
            'deleted' => 0,
            'per_category' => [],
        ];

        foreach ($categories as $category => $options) {
            $catSummary = ['imported' => 0, 'created' => 0, 'updated' => 0, 'deleted' => 0];

            if ($mode === 'replace_category') {
                $deleted = PreauthReferenceOption::query()->where('category', $category)->delete();
                $catSummary['deleted'] = $deleted;
                $summary['deleted'] += $deleted;
            }

            foreach ($options as $option) {
                $label = $option['label'];

                $existing = PreauthReferenceOption::query()
                    ->where('category', $category)
                    ->where('label', $label)
                    ->first();

                if ($existing) {
                    $existing->sort_order = $option['sort_order'];
                    $existing->save();
                    $catSummary['updated']++;
                    $summary['updated']++;
                } else {
                    PreauthReferenceOption::query()->create([
                        'category' => $category,
                        'label' => $label,
                        'sort_order' => $option['sort_order'],
                    ]);
                    $catSummary['created']++;
                    $summary['created']++;
                }
                $catSummary['imported']++;
            }

            $summary['per_category'][$category] = $catSummary;
        }

        return $summary;
    }

    /**
     * @param  array<string, mixed>  $categories
     * @return array<string, array<int, array{label: string, sort_order: int}>>
     */
    protected function normalizeCategoriesPayload(array $categories): array
    {
        $normalized = [];
        foreach ($categories as $category => $options) {
            $categoryKey = trim((string) $category);
            if ($categoryKey === '' || ! is_array($options)) {
                continue;
            }
            $rows = [];
            $sort = 0;
            foreach ($options as $option) {
                if (is_string($option)) {
                    $label = trim($option);
                } elseif (is_array($option)) {
                    $label = trim((string) ($option['label'] ?? $option['name'] ?? ''));
                    $sort = isset($option['sort_order']) ? (int) $option['sort_order'] : $sort;
                } else {
                    continue;
                }
                if ($label === '') {
                    continue;
                }
                $rows[] = [
                    'label' => $label,
                    'sort_order' => $sort,
                ];
                $sort++;
            }
            if ($rows !== []) {
                $normalized[$categoryKey] = $rows;
            }
        }

        if ($normalized === []) {
            throw new RuntimeException('No valid categories supplied.');
        }

        return $normalized;
    }
}
