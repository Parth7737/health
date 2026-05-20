<?php

namespace App\Http\Controllers\Integration;

use App\Http\Controllers\Controller;
use App\Services\PreauthReferenceOptionImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use RuntimeException;

class PreauthReferenceOptionImportController extends Controller
{
    public function __construct(
        protected PreauthReferenceOptionImportService $importService
    ) {}

    /**
     * GET /integration/scheme-preauth/reference-options
     */
    public function index(): JsonResponse
    {
        $grouped = $this->importService->groupedIndex();
        $payload = [];
        foreach ($grouped as $category => $items) {
            $payload[$category] = $items->map(static fn ($row) => [
                'id' => $row->id,
                'label' => $row->label,
                'sort_order' => (int) $row->sort_order,
            ])->values()->all();
        }

        return response()->json([
            'status' => true,
            'categories' => array_keys($payload),
            'data' => $payload,
            'sha_table_map' => PreauthReferenceOptionImportService::SHA_TABLE_CATEGORY_MAP,
        ]);
    }

    /**
     * POST /integration/scheme-preauth/reference-options/import
     *
     * Body:
     * - source: payload | sha_database (default payload)
     * - mode: merge | replace_category | replace_all
     * - categories: { "Diabetes": [{ "label": "Yes", "sort_order": 0 }, ...] }  (required when source=payload)
     */
    public function import(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'source' => 'nullable|in:payload,sha_database',
            'mode' => 'nullable|in:merge,replace_category,replace_all',
            'categories' => 'required_if:source,payload|array',
            'categories.*' => 'array',
            'categories.*.*.label' => 'required_with:categories.*|string|max:255',
            'categories.*.*.name' => 'nullable|string|max:255',
            'categories.*.*.sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $source = (string) $request->input('source', 'payload');
        $mode = (string) $request->input('mode', 'merge');

        try {
            $summary = $source === 'sha_database'
                ? $this->importService->importFromShaDatabase($mode)
                : $this->importService->importFromPayload((array) $request->input('categories', []), $mode);

            return response()->json([
                'status' => true,
                'message' => 'Preauth reference options imported successfully.',
                'source' => $source,
                'summary' => $summary,
            ]);
        } catch (RuntimeException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Import failed: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /integration/scheme-preauth/reference-options/sha-map
     */
    public function shaMap(): JsonResponse
    {
        return response()->json([
            'status' => true,
            'map' => PreauthReferenceOptionImportService::SHA_TABLE_CATEGORY_MAP,
        ]);
    }
}
