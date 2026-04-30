<?php

namespace App\Http\Controllers\Hospital;

use App\CentralLogics\Helpers;
use App\Http\Controllers\BaseHospitalController;
use App\Models\BedAllocation;
use App\Models\HeaderFooter;
use App\Models\IpdPrescription;
use App\Models\Medicine;
use App\Models\MedicineDosage;
use App\Models\MedicineFrequency;
use App\Models\MedicineInstruction;
use App\Models\MedicineRoute;
use App\Services\PatientTimelineService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class IpdPrescriptionController extends BaseHospitalController
{
    public function form(BedAllocation $allocation)
    {
        $allocation = $this->resolveAllocation($allocation);

        return $this->renderForm($allocation, null);
    }

    public function editForm(BedAllocation $allocation, IpdPrescription $prescription)
    {
        $allocation = $this->resolveAllocation($allocation);
        $prescription = $this->resolvePrescription($allocation, $prescription);

        return $this->renderForm($allocation, $prescription->load(['items']));
    }

    public function store(Request $request, BedAllocation $allocation, PatientTimelineService $timelineService)
    {
        $allocation = $this->resolveAllocation($allocation);

        if ($allocation->discharge_date) {
            return response()->json([
                'status' => false,
                'message' => 'Discharged admission cannot be modified.',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'prescription_id' => 'nullable|integer|exists:ipd_prescriptions,id',
            'valid_till' => 'nullable|date_format:d-m-Y',
            'header_note' => 'nullable|string',
            'footer_note' => 'nullable|string',
            'medicine_id' => 'required|array|min:1',
            'medicine_id.*' => 'required|integer|exists:medicines,id',
            'medicine_dosage_id' => 'nullable|array',
            'medicine_dosage_id.*' => 'nullable|integer|exists:medicine_dosages,id',
            'medicine_instruction_id' => 'nullable|array',
            'medicine_instruction_id.*' => 'nullable|integer|exists:medicine_instructions,id',
            'medicine_route_id' => 'nullable|array',
            'medicine_route_id.*' => 'nullable|integer|exists:medicine_routes,id',
            'medicine_frequency_id' => 'nullable|array',
            'medicine_frequency_id.*' => 'nullable|integer|exists:medicine_frequencies,id',
            'no_of_day' => 'nullable|array',
            'no_of_day.*' => 'nullable|integer|min:1|max:365',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $medicineIds = collect($request->input('medicine_id', []))->values();
        $dosageIdsInput = collect($request->input('medicine_dosage_id', []))->values();
        $instructionIdsInput = collect($request->input('medicine_instruction_id', []))->values();
        $routeIdsInput = collect($request->input('medicine_route_id', []))->values();
        $frequencyIdsInput = collect($request->input('medicine_frequency_id', []))->values();
        $daysInput = collect($request->input('no_of_day', []))->values();

        $rowCount = $medicineIds->count();
        if ($rowCount === 0) {
            return response()->json([
                'errors' => [
                    ['code' => 'medicine_id', 'message' => 'Prescription rows are incomplete. Please fill all medicine rows properly.'],
                ],
            ], 422);
        }

        $normalizeNullableInt = function ($value) {
            if ($value === null || $value === '') {
                return null;
            }
            return (int) $value;
        };

        $dosageIds = collect(range(0, max($rowCount - 1, 0)))->map(fn ($index) => $normalizeNullableInt($dosageIdsInput->get($index)))->values();
        $instructionIds = collect(range(0, max($rowCount - 1, 0)))->map(fn ($index) => $normalizeNullableInt($instructionIdsInput->get($index)))->values();
        $routeIds = collect(range(0, max($rowCount - 1, 0)))->map(fn ($index) => $normalizeNullableInt($routeIdsInput->get($index)))->values();
        $frequencyIds = collect(range(0, max($rowCount - 1, 0)))->map(fn ($index) => $normalizeNullableInt($frequencyIdsInput->get($index)))->values();
        $days = collect(range(0, max($rowCount - 1, 0)))->map(fn ($index) => $normalizeNullableInt($daysInput->get($index)))->values();

        $medicineMap = Medicine::query()
            ->whereIn('id', $medicineIds->unique()->all())
            ->get(['id', 'medicine_category_id'])
            ->keyBy('id');

        $dosageMap = MedicineDosage::query()
            ->whereIn('id', $dosageIds->filter()->unique()->all())
            ->get(['id', 'medicine_category_id'])
            ->keyBy('id');

        $instructionMap = MedicineInstruction::query()
            ->whereIn('id', $instructionIds->filter()->unique()->all())
            ->pluck('id')
            ->flip();

        $routeMap = MedicineRoute::query()
            ->whereIn('id', $routeIds->filter()->unique()->all())
            ->pluck('id')
            ->flip();

        $frequencyMap = MedicineFrequency::query()
            ->whereIn('id', $frequencyIds->filter()->unique()->all())
            ->pluck('id')
            ->flip();

        if (
            $medicineMap->count() !== $medicineIds->unique()->count() ||
            $dosageMap->count() !== $dosageIds->filter()->unique()->count() ||
            $instructionMap->count() !== $instructionIds->filter()->unique()->count() ||
            $routeMap->count() !== $routeIds->filter()->unique()->count() ||
            $frequencyMap->count() !== $frequencyIds->filter()->unique()->count()
        ) {
            return response()->json([
                'errors' => [
                    ['code' => 'medicine_id', 'message' => 'Invalid medicine/instruction/frequency selection for this hospital.'],
                ],
            ], 422);
        }

        for ($index = 0; $index < $rowCount; $index++) {
            $medicineId = (int) $medicineIds[$index];
            $dosageId = $dosageIds[$index];

            $medicine = $medicineMap->get($medicineId);
            if (!$medicine) {
                return response()->json([
                    'errors' => [
                        ['code' => 'medicine_id', 'message' => 'Invalid medicine selection.'],
                    ],
                ], 422);
            }

            if ($dosageId !== null) {
                $dosage = $dosageMap->get($dosageId);
                if (!$dosage || (int) $medicine->medicine_category_id !== (int) $dosage->medicine_category_id) {
                    return response()->json([
                        'errors' => [
                            ['code' => 'medicine_dosage_id', 'message' => 'Selected dosage does not belong to selected medicine category.'],
                        ],
                    ], 422);
                }
            }

            if ($days[$index] !== null && (int) $days[$index] < 1) {
                return response()->json([
                    'errors' => [
                        ['code' => 'no_of_day', 'message' => 'Number of days must be at least 1.'],
                    ],
                ], 422);
            }
        }

        try {
            $editingPrescription = null;
            if ($request->filled('prescription_id')) {
                $editingPrescription = IpdPrescription::query()
                    ->where('id', (int) $request->input('prescription_id'))
                    ->first();

                if (!$editingPrescription) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Prescription not found.',
                    ], 404);
                }

                $editingPrescription = $this->resolvePrescription($allocation, $editingPrescription);
            }

            $context = DB::transaction(function () use ($request, $allocation, $medicineIds, $dosageIds, $instructionIds, $routeIds, $frequencyIds, $days, $rowCount, $medicineMap, $editingPrescription) {
                $prescription = $editingPrescription ?: new IpdPrescription();

                $isUpdate = $prescription->exists;

                $prescription->hospital_id = $this->hospital_id;
                $prescription->patient_id = $allocation->patient_id;
                $prescription->bed_allocation_id = $allocation->id;
                $prescription->doctor_id = $allocation->consultant_doctor_id;
                $prescription->header_note = $request->input('header_note');
                $prescription->footer_note = $request->input('footer_note');
                $prescription->valid_till = $request->filled('valid_till')
                    ? Carbon::createFromFormat('d-m-Y', $request->input('valid_till'))->format('Y-m-d')
                    : null;
                $prescription->save();

                if (empty($prescription->prescription_no)) {
                    $dateCode = optional($prescription->created_at)->format('ym') ?: now()->format('ym');
                    $prescription->prescription_no = 'IPD-RX-' . $dateCode . '-' . str_pad((string) $prescription->id, 5, '0', STR_PAD_LEFT);
                    $prescription->save();
                }

                $prescription->items()->delete();

                for ($index = 0; $index < $rowCount; $index++) {
                    $medicineId = (int) $medicineIds[$index];

                    $prescription->items()->create([
                        'medicine_id' => $medicineId,
                        'medicine_category_id' => $medicineMap->get($medicineId)?->medicine_category_id,
                        'medicine_dosage_id' => $dosageIds[$index],
                        'medicine_instruction_id' => $instructionIds[$index],
                        'medicine_route_id' => $routeIds[$index],
                        'medicine_frequency_id' => $frequencyIds[$index],
                        'no_of_day' => $days[$index],
                    ]);
                }

                return [
                    'prescription_id' => $prescription->id,
                    'is_update' => $isUpdate,
                    'item_count' => $rowCount,
                ];
            });

            $timelineService->logForIpdAdmission($allocation, [
                'event_key' => 'ipd.prescription.saved',
                'title' => ($context['is_update'] ?? false) ? 'Prescription Updated' : 'Prescription Added',
                'description' => 'Prescription has been saved with ' . ($context['item_count'] ?? 0) . ' medicine item(s).',
                'meta' => [
                    'prescription_id' => (int) ($context['prescription_id'] ?? 0),
                    'is_update' => (bool) ($context['is_update'] ?? false),
                    'item_count' => (int) ($context['item_count'] ?? 0),
                ],
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Prescription saved successfully.',
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'status' => false,
                'message' => 'Unable to save prescription right now. Please try again.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function view(BedAllocation $allocation, IpdPrescription $prescription)
    {
        $allocation = $this->resolveAllocation($allocation);
        $prescription = $this->resolvePrescription($allocation, $prescription)
            ->load([
                'items.medicine:id,name,medicine_category_id',
                'items.category:id,name',
                'items.dosage:id,dosage',
                'items.instruction:id,instruction',
                'items.route:id,route',
                'items.frequency:id,frequency',
                'doctor:id,first_name,last_name',
                'patient:id,name,gender,age_years,age_months,phone,email,patient_id',
                'allocation:id,admission_no,admission_date,bp,weight',
            ]);

        return view('hospital.ipd-patient.prescription.view', [
            'allocation' => $allocation,
            'prescription' => $prescription,
        ]);
    }

    public function destroy(BedAllocation $allocation, IpdPrescription $prescription, PatientTimelineService $timelineService)
    {
        $allocation = $this->resolveAllocation($allocation);

        if ($allocation->discharge_date) {
            return response()->json([
                'status' => false,
                'message' => 'Discharged admission cannot be modified.',
            ], 422);
        }

        $prescription = $this->resolvePrescription($allocation, $prescription);

        $deletedPrescriptionId = $prescription->id;
        $prescription->delete();

        $timelineService->logForIpdAdmission($allocation, [
            'event_key' => 'ipd.prescription.deleted',
            'title' => 'Prescription Deleted',
            'description' => 'Prescription was removed from this IPD admission.',
            'meta' => [
                'prescription_id' => $deletedPrescriptionId,
            ],
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Prescription deleted successfully.',
        ]);
    }

    public function print(BedAllocation $allocation, IpdPrescription $prescription)
    {
        $allocation = $this->resolveAllocation($allocation);
        $prescription = $this->resolvePrescription($allocation, $prescription)
            ->load([
                'items.medicine:id,name,medicine_category_id',
                'items.category:id,name',
                'items.dosage:id,dosage',
                'items.instruction:id,instruction',
                'items.route:id,route',
                'items.frequency:id,frequency',
                'doctor:id,first_name,last_name',
                'patient:id,name,gender,age_years,age_months,phone,email,patient_id',
                'allocation:id,admission_no,admission_date,bp,weight',
            ]);

        $printTemplate = HeaderFooter::query()
            ->whereIn('type', ['ipd_prescription', 'opd_prescription'])
            ->orderByRaw("CASE WHEN type = 'ipd_prescription' THEN 0 ELSE 1 END")
            ->first();

        return view('hospital.ipd-patient.prescription.print', [
            'allocation' => $allocation,
            'prescription' => $prescription,
            'hospital' => auth()->user()?->hospital,
            'printTemplate' => $printTemplate,
        ]);
    }

    public function loadDosages(Request $request)
    {
        $medicineCategoryIds = collect($request->input('medicine_category_ids', []))
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->map(fn ($value) => (int) $value)
            ->unique()
            ->values();

        $medicineIds = collect($request->input('medicine_ids', []))
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->map(fn ($value) => (int) $value)
            ->unique()
            ->values();

        if ($request->filled('medicine_category_id')) {
            $medicineCategoryIds->push((int) $request->input('medicine_category_id'));
            $medicineCategoryIds = $medicineCategoryIds->unique()->values();
        }

        if ($request->filled('medicine_id')) {
            $medicineIds->push((int) $request->input('medicine_id'));
            $medicineIds = $medicineIds->unique()->values();
        }

        if ($medicineCategoryIds->isEmpty() && $medicineIds->isNotEmpty()) {
            $resolvedCategoryIds = Medicine::query()
                ->whereIn('id', $medicineIds->all())
                ->whereNotNull('medicine_category_id')
                ->pluck('medicine_category_id')
                ->map(fn ($value) => (int) $value)
                ->unique()
                ->values();

            $medicineCategoryIds = $medicineCategoryIds->merge($resolvedCategoryIds)->unique()->values();
        }

        if ($medicineCategoryIds->isEmpty()) {
            return response()->json([]);
        }

        $dosages = MedicineDosage::query()
            ->whereIn('medicine_category_id', $medicineCategoryIds->all())
            ->orderBy('dosage')
            ->get(['id', 'medicine_category_id', 'dosage']);

        return response()->json($dosages);
    }

    protected function renderForm(BedAllocation $allocation, ?IpdPrescription $prescription)
    {
        $medicines = Medicine::query()
            ->select('id', 'medicine_category_id', 'name')
            ->orderBy('name')
            ->get();

        $instructions = MedicineInstruction::query()
            ->select('id', 'instruction')
            ->orderBy('instruction')
            ->get();

        $frequencies = MedicineFrequency::query()
            ->select('id', 'frequency')
            ->orderBy('frequency')
            ->get();

        $routes = MedicineRoute::query()
            ->select('id', 'route')
            ->orderBy('route')
            ->get();

        return view('hospital.ipd-patient.prescription.form', [
            'allocation' => $allocation,
            'prescription' => $prescription,
            'medicines' => $medicines,
            'instructions' => $instructions,
            'routes' => $routes,
            'frequencies' => $frequencies,
        ]);
    }

    protected function resolveAllocation(BedAllocation $allocation): BedAllocation
    {
        if ((int) $allocation->hospital_id !== (int) $this->hospital_id) {
            abort(403, 'Unauthorized IPD admission record.');
        }

        return $allocation;
    }

    protected function resolvePrescription(BedAllocation $allocation, IpdPrescription $prescription): IpdPrescription
    {
        if ((int) $prescription->hospital_id !== (int) $this->hospital_id || (int) $prescription->bed_allocation_id !== (int) $allocation->id) {
            abort(403, 'Unauthorized IPD prescription record.');
        }

        return $prescription;
    }
}
