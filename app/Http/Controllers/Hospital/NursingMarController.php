<?php

namespace App\Http\Controllers\Hospital;

use App\CentralLogics\Helpers;
use App\Http\Controllers\BaseHospitalController;
use App\Models\BedAllocation;
use App\Models\Hospital;
use App\Models\IpdPrescription;
use App\Models\MedicationAdministrationLog;
use App\Models\Ward;
use App\Services\Clinical\MarScheduleService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NursingMarController extends BaseHospitalController
{
    public array $routes = [];

    public function __construct(protected MarScheduleService $marScheduleService)
    {
        parent::__construct();
        $this->routes = [
            'marLoad' => route('hospital.nursing.mar.load'),
            'marAdminister' => route('hospital.nursing.mar.administer'),
            'wards' => route('hospital.nursing.mar.wards'),
        ];
    }

    public function index()
    {
        return view('hospital.nursing.mar.index', [
            'pathurl' => 'nursing-mar',
            'routes' => $this->routes,
        ]);
    }

    public function wards()
    {
        $wards = Ward::query()
            ->where('hospital_id', $this->hospital_id)
            ->orderBy('ward_name')
            ->get(['id', 'ward_name']);

        return response()->json(['items' => $wards]);
    }

    public function load(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'nullable|date_format:Y-m-d',
            'ward_id' => 'nullable|integer',
            'search' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $date = Carbon::parse($request->input('date', now()->toDateString()))->startOfDay();
        $search = trim((string) $request->input('search', ''));
        $wardId = $request->input('ward_id');

        $mealSettings = $this->mealSettings();

        $allocationsQuery = BedAllocation::query()
            ->with([
                'patient:id,patient_id,name',
                'bed.room.ward:id,ward_name',
            ])
            ->whereNull('discharge_date')
            ->whereHas('patient');

        if ($wardId) {
            $allocationsQuery->whereHas('bed.room', fn ($q) => $q->where('ward_id', $wardId));
        }

        if ($search !== '') {
            $like = '%' . $search . '%';
            $allocationsQuery->where(function ($q) use ($like) {
                $q->whereHas('patient', function ($pq) use ($like) {
                    $pq->where('name', 'like', $like)
                        ->orWhere('patient_id', 'like', $like);
                })->orWhereHas('bed', function ($bq) use ($like) {
                    $bq->where('bed_number', 'like', $like);
                });
            });
        }

        $allocations = $allocationsQuery
            ->latest('admission_date')
            ->get();

        if ($allocations->isEmpty()) {
            return response()->json([
                'date' => $date->format('Y-m-d'),
                'meal_times' => $mealSettings,
                'patients' => [],
                'summary' => ['total' => 0, 'pending' => 0, 'given' => 0, 'other' => 0],
            ]);
        }

        $allocationIds = $allocations->pluck('id');

        $prescriptions = IpdPrescription::query()
            ->with([
                'items.medicine:id,name',
                'items.dosage:id,dosage',
                'items.frequency:id,frequency,no_of_medicine,schedule_times',
                'items.instruction:id,instruction,meal_relation',
                'items.route:id,route',
            ])
            ->whereIn('bed_allocation_id', $allocationIds)
            ->where(function ($q) use ($date) {
                $q->whereNull('valid_till')
                    ->orWhereDate('valid_till', '>=', $date->toDateString());
            })
            ->whereDate('created_at', '<=', $date->toDateString())
            ->get()
            ->groupBy('bed_allocation_id');

        $existingLogs = MedicationAdministrationLog::query()
            ->with('administeredByUser:id,name')
            ->whereDate('scheduled_date', $date->toDateString())
            ->whereIn('bed_allocation_id', $allocationIds)
            ->get()
            ->keyBy(fn ($log) => $this->slotKey(
                (int) $log->ipd_prescription_item_id,
                $date->toDateString(),
                substr((string) $log->scheduled_time, 0, 5)
            ));

        $summary = ['total' => 0, 'pending' => 0, 'given' => 0, 'other' => 0];
        $patients = [];

        foreach ($allocations as $allocation) {
            $rxList = $prescriptions->get($allocation->id, collect());
            if ($rxList->isEmpty()) {
                continue;
            }

            $medications = [];
            foreach ($rxList as $prescription) {
                foreach ($prescription->items as $item) {
                    if (!$item->medicine_id) {
                        continue;
                    }

                    $startDate = Carbon::parse($prescription->created_at)->startOfDay();
                    $dayIndex = $startDate->diffInDays($date) + 1;
                    if ($dayIndex > max(1, (int) ($item->no_of_day ?? 1))) {
                        continue;
                    }

                    $slots = $this->marScheduleService->buildDailySlots($item, $date, $mealSettings);
                    $slotPayload = [];

                    foreach ($slots as $slot) {
                        $key = $this->slotKey((int) $item->id, $date->toDateString(), $slot['scheduled_time']);
                        $log = $existingLogs->get($key);
                        $status = $log?->status ?? 'pending';

                        $summary['total']++;
                        if ($status === 'pending') {
                            $summary['pending']++;
                        } elseif ($status === 'given') {
                            $summary['given']++;
                        } else {
                            $summary['other']++;
                        }

                        $slotPayload[] = [
                            'log_id' => $log?->id,
                            'prescription_item_id' => (int) $item->id,
                            'prescription_id' => (int) $prescription->id,
                            'medicine_id' => (int) $item->medicine_id,
                            'scheduled_time' => $slot['scheduled_time'],
                            'meal_relation' => $slot['meal_relation'],
                            'meal_label' => $slot['meal_label'],
                            'time_source' => $slot['time_source'] ?? 'auto',
                            'status' => $status,
                            'administered_at' => $log?->administered_at?->format('d-m-Y H:i'),
                            'administered_by' => $log?->administeredByUser?->name,
                            'notes' => $log?->notes,
                            'can_manage' => auth()->user()?->can('edit-nursing-mar') ?? false,
                        ];
                    }

                    if (empty($slotPayload)) {
                        continue;
                    }

                    $medications[] = [
                        'prescription_item_id' => (int) $item->id,
                        'medicine_name' => $item->medicine?->name ?? '-',
                        'dosage' => $item->dosage?->dosage ?? '-',
                        'frequency' => $item->frequency?->frequency ?? '-',
                        'route' => $item->route?->route ?? '-',
                        'instruction' => $item->instruction?->instruction ?? '-',
                        'meal_label' => MarScheduleService::MEAL_LABELS[$item->instruction?->meal_relation ?? 'none'] ?? 'Any Time',
                        'day' => $dayIndex,
                        'total_days' => (int) ($item->no_of_day ?? 1),
                        'slots' => $slotPayload,
                    ];
                }
            }

            if (empty($medications)) {
                continue;
            }

            $wardName = $allocation->bed?->room?->ward?->ward_name ?? '-';
            $bedNumber = $allocation->bed?->bed_number ?? '-';

            $patients[] = [
                'bed_allocation_id' => (int) $allocation->id,
                'patient_id' => (int) $allocation->patient_id,
                'patient_name' => $allocation->patient?->name ?? '-',
                'patient_uhid' => $allocation->patient?->patient_id ?? '-',
                'ward_name' => $wardName,
                'bed_number' => $bedNumber,
                'location' => trim($wardName . ' · Bed ' . $bedNumber),
                'medications' => $medications,
            ];
        }

        return response()->json([
            'date' => $date->format('Y-m-d'),
            'meal_times' => $mealSettings,
            'patients' => $patients,
            'summary' => $summary,
        ]);
    }

    public function administer(Request $request)
    {
        if (!auth()->user()?->can('edit-nursing-mar')) {
            return response()->json(['status' => false, 'message' => 'You do not have permission to manage MAR.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'bed_allocation_id' => 'required|integer|exists:bed_allocations,id',
            'ipd_prescription_id' => 'required|integer|exists:ipd_prescriptions,id',
            'ipd_prescription_item_id' => 'required|integer|exists:ipd_prescription_items,id',
            'medicine_id' => 'required|integer|exists:medicines,id',
            'scheduled_date' => 'required|date_format:Y-m-d',
            'scheduled_time' => 'required|date_format:H:i',
            'status' => 'required|in:given,missed,held,refused',
            'notes' => 'nullable|string|max:1000',
            'meal_relation' => 'nullable|in:none,before_food,after_food,with_food,empty_stomach',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $allocation = BedAllocation::query()
            ->where('hospital_id', $this->hospital_id)
            ->whereNull('discharge_date')
            ->find($request->input('bed_allocation_id'));

        if (!$allocation) {
            return response()->json(['status' => false, 'message' => 'Active admission not found.'], 404);
        }

        $scheduledTime = substr((string) $request->input('scheduled_time'), 0, 5) . ':00';

        $log = MedicationAdministrationLog::query()->updateOrCreate(
            [
                'ipd_prescription_item_id' => (int) $request->input('ipd_prescription_item_id'),
                'scheduled_date' => $request->input('scheduled_date'),
                'scheduled_time' => $scheduledTime,
            ],
            [
                'hospital_id' => $this->hospital_id,
                'patient_id' => $allocation->patient_id,
                'bed_allocation_id' => $allocation->id,
                'ipd_prescription_id' => (int) $request->input('ipd_prescription_id'),
                'medicine_id' => (int) $request->input('medicine_id'),
                'meal_relation' => $request->input('meal_relation', 'none'),
                'status' => $request->input('status'),
                'administered_at' => now(),
                'administered_by' => auth()->id(),
                'notes' => $request->input('notes'),
            ]
        );

        $log->load('administeredByUser:id,name');

        return response()->json([
            'status' => true,
            'message' => 'Medication administration updated successfully.',
            'log' => [
                'id' => $log->id,
                'status' => $log->status,
                'administered_at' => $log->administered_at?->format('d-m-Y H:i'),
                'administered_by' => $log->administeredByUser?->name,
                'notes' => $log->notes,
            ],
        ]);
    }

    private function mealSettings(): array
    {
        $hospital = Hospital::query()->find($this->hospital_id);

        return [
            'breakfast' => substr((string) ($hospital?->mar_breakfast_time ?? '08:00:00'), 0, 5),
            'lunch' => substr((string) ($hospital?->mar_lunch_time ?? '13:00:00'), 0, 5),
            'dinner' => substr((string) ($hospital?->mar_dinner_time ?? '20:00:00'), 0, 5),
            'offset_minutes' => (int) ($hospital?->mar_meal_offset_minutes ?? 30),
        ];
    }

    private function slotKey(int $itemId, string $date, string $time): string
    {
        return $itemId . '_' . $date . '_' . substr($time, 0, 5);
    }
}
