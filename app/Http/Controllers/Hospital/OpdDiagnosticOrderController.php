<?php

namespace App\Http\Controllers\Hospital;

use App\CentralLogics\Helpers;
use App\Http\Controllers\BaseHospitalController;
use App\Models\DiagnosticOrder;
use App\Models\DiagnosticOrderItem;
use App\Models\OpdPatient;
use App\Models\PatientCharge;
use App\Models\PathologyTest;
use App\Models\RadiologyTest;
use App\Services\ChargeLedgerService;
use App\Services\PatientTimelineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OpdDiagnosticOrderController extends BaseHospitalController
{
    public function showform(Request $request, OpdPatient $opdPatient)
    {
        if ($opdPatient->hospital_id !== $this->hospital_id) {
            abort(403, 'Unauthorized OPD patient record.');
        }

        $orderType = $request->get('order_type');
        if (!in_array($orderType, ['pathology', 'radiology'], true)) {
            abort(422, 'Invalid diagnostic order type.');
        }

        $this->authorizeOrderType($orderType);

        $tests = $orderType === 'pathology'
            ? PathologyTest::with(['category:id,name', 'parameters:id,name', 'chargeMaster.tpaRates'])->orderBy('test_name')->get()
            : RadiologyTest::with(['category:id,name', 'parameters:id,name', 'chargeMaster.tpaRates'])->orderBy('test_name')->get();

        $tests = $tests->map(function ($test) use ($opdPatient) {
            $test->resolved_charge = $this->resolveTestCharge($test, $opdPatient->tpa_id ? (int) $opdPatient->tpa_id : null);
            return $test;
        });

        $existingDiagnosticItems = DiagnosticOrderItem::query()
            ->select(['id', 'testable_id', 'status'])
            ->where('department', $orderType)
            ->whereHas('order', function ($query) use ($opdPatient, $orderType) {
                $query->where('visitable_type', OpdPatient::class)
                    ->where('visitable_id', $opdPatient->id)
                    ->where('order_type', $orderType);
            })
            ->get()
            ->map(function ($item) {
                return [
                    'item_id' => (int) $item->id,
                    'test_id' => (int) $item->testable_id,
                    'status' => (string) ($item->status ?? ''),
                ];
            })
            ->values();

        return view('hospital.opd-patient.partials.diagnostic_order_form', [
            'opdPatient' => $opdPatient,
            'orderType' => $orderType,
            'tests' => $tests,
            'existingDiagnosticItems' => $existingDiagnosticItems,
        ]);
    }

    public function store(Request $request, OpdPatient $opdPatient, ChargeLedgerService $chargeLedger, PatientTimelineService $timelineService)
    {
        if ($opdPatient->hospital_id !== $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized OPD patient record.'], 403);
        }

        $orderType = $request->order_type;
        if (!in_array($orderType, ['pathology', 'radiology'], true)) {
            return response()->json(['status' => false, 'message' => 'Invalid diagnostic order type.'], 422);
        }

        $this->authorizeOrderType($orderType);

        $priorityValue = (string) $request->input('priority', 'Routine');

        $validator = Validator::make($request->all(), [
            'order_type' => 'required|in:pathology,radiology',
            'test_ids' => 'required|array|min:1',
            'test_ids.*' => 'required|integer',
            'priority' => 'required|in:Routine,Urgent,STAT',
            'notes' => 'nullable|string',
        ]);

        $validator->after(function ($validator) use ($request, $orderType) {
            $testIds = collect($request->test_ids ?? [])->filter()->map(fn ($id) => (int) $id)->unique()->values();
            if ($testIds->isEmpty()) {
                $validator->errors()->add('test_ids', 'Please select at least one test.');
                return;
            }

            $query = $orderType === 'pathology' ? PathologyTest::query() : RadiologyTest::query();
            $validCount = $query->whereIn('id', $testIds)->count();
            if ($validCount !== $testIds->count()) {
                $validator->errors()->add('test_ids', 'One or more selected tests are invalid.');
            }

            $missingChargeMasterTests = $query->whereIn('id', $testIds)
                ->whereNull('charge_master_id')
                ->pluck('test_name')
                ->all();
            if (!empty($missingChargeMasterTests)) {
                $validator->errors()->add('test_ids', 'Charge master not mapped for: ' . implode(', ', $missingChargeMasterTests));
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $testIds = collect($request->test_ids)->map(fn ($id) => (int) $id)->unique()->values();
        $tests = $orderType === 'pathology'
            ? PathologyTest::with(['category:id,name', 'parameters.unit:id,name', 'chargeMaster.tpaRates'])->whereIn('id', $testIds)->get()
            : RadiologyTest::with(['category:id,name', 'parameters.unit:id,name', 'chargeMaster.tpaRates'])->whereIn('id', $testIds)->get();

        $order = DB::transaction(function () use ($opdPatient, $request, $orderType, $tests, $chargeLedger, $priorityValue) {
            $order = DiagnosticOrder::create([
                'hospital_id' => $this->hospital_id,
                'patient_id' => $opdPatient->patient_id,
                'visitable_type' => OpdPatient::class,
                'visitable_id' => $opdPatient->id,
                'order_type' => $orderType,
                'order_no' => $this->generateOrderNo($orderType),
                'ordered_by' => auth()->id(),
                'notes' => $request->notes,
                'status' => 'ordered',
            ]);

            foreach ($tests as $test) {
                $resolvedCharge = $this->resolveTestCharge($test, $opdPatient->tpa_id ? (int) $opdPatient->tpa_id : null);

                $item = $order->items()->create([
                    'department' => $orderType,
                    'testable_type' => get_class($test),
                    'testable_id' => $test->id,
                    'test_name' => $test->test_name,
                    'test_code' => $test->test_code,
                    'category_name' => optional($test->category)->name,
                    'priority' => $priorityValue,
                    'sample_type' => $test->sample_type ?? null,
                    'method' => $test->method,
                    'expected_report_days' => $test->report_days,
                    'standard_charge' => $resolvedCharge,
                    'status' => 'ordered',
                ]);

                $chargeLedger->upsertCharge([
                    'hospital_id' => $this->hospital_id,
                    'patient_id' => $opdPatient->patient_id,
                    'visitable_type' => OpdPatient::class,
                    'visitable_id' => $opdPatient->id,
                    'source_type' => DiagnosticOrderItem::class,
                    'source_id' => $item->id,
                    'module' => $orderType,
                    'particular' => strtoupper($orderType) . ' - ' . $test->test_name,
                    'charge_master_id' => $test->charge_master_id,
                    'charge_category' => $orderType,
                    'calculation_type' => 'fixed',
                    'billing_frequency' => 'one_time',
                    'quantity' => 1,
                    'unit_rate' => $resolvedCharge,
                    'net_amount' => $resolvedCharge,
                    'payer_type' => $opdPatient->tpa_id ? 'tpa' : 'self',
                    'tpa_id' => $opdPatient->tpa_id,
                    'charged_at' => now(),
                ]);

                foreach ($test->parameters as $index => $parameter) {
                    $item->parameters()->create([
                        'parameterable_type' => get_class($parameter),
                        'parameterable_id' => $parameter->id,
                        'parameter_name' => $parameter->name,
                        'unit_name' => optional($parameter->unit)->name,
                        'normal_range' => $parameter->range,
                        'sort_order' => $index + 1,
                    ]);
                }
            }

            return $order;
        });

        $timelineService->logForOpdVisit($opdPatient, [
            'event_key' => 'opd.diagnostic_order.created',
            'title' => ucfirst($orderType) . ' Order Placed',
            'description' => ucfirst($orderType) . ' order ' . $order->order_no . ' created with ' . $tests->count() . ' test(s).',
            'meta' => [
                'order_type' => $orderType,
                'order_no' => $order->order_no,
                'priority' => $priorityValue,
                'test_count' => $tests->count(),
            ],
        ]);

        return response()->json([
            'status' => true,
            'message' => ucfirst($orderType) . ' test order created successfully.',
            'order_no' => $order->order_no,
        ]);
    }

    public function destroy(OpdPatient $opdPatient, DiagnosticOrderItem $item, ChargeLedgerService $chargeLedger, PatientTimelineService $timelineService)
    {
        if ($opdPatient->hospital_id !== $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized OPD patient record.'], 403);
        }

        if ((int) ($item->order->visitable_id ?? 0) !== (int) $opdPatient->id || (string) ($item->order->visitable_type ?? '') !== OpdPatient::class) {
            return response()->json(['status' => false, 'message' => 'Diagnostic item does not belong to this OPD visit.'], 422);
        }

        $this->authorizeDeleteOrderType((string) $item->department);

        $statusKey = strtolower(str_replace([' ', '-'], '_', (string) $item->status));
        if (in_array($statusKey, ['in_progress', 'completed'], true)) {
            return response()->json([
                'status' => false,
                'message' => 'In-progress or completed tests cannot be deleted.',
            ], 422);
        }

        $deletedItemName = $item->test_name;
        $deletedDepartment = $item->department;

        $reversalSummary = DB::transaction(function () use ($item, $chargeLedger) {
            $charge = PatientCharge::query()
                ->where('source_type', DiagnosticOrderItem::class)
                ->where('source_id', $item->id)
                ->first();

            $summary = [
                'released_amount' => 0.0,
                'unallocated_credit' => 0.0,
            ];

            if ($charge) {
                $summary = $chargeLedger->removeChargeAndRebalance($charge);
            }

            $order = $item->order;
            $item->parameters()->delete();
            $item->delete();

            if ($order && !$order->items()->exists()) {
                $order->delete();
            }

            return $summary;
        });

        $timelineService->logForOpdVisit($opdPatient, [
            'event_key' => 'opd.diagnostic_order.deleted',
            'title' => ucfirst((string) $deletedDepartment) . ' Test Removed',
            'description' => ($deletedItemName ?: 'Diagnostic test') . ' has been removed from this visit.',
            'meta' => [
                'order_type' => $deletedDepartment,
                'test_name' => $deletedItemName,
                'released_paid_amount' => (float) ($reversalSummary['released_amount'] ?? 0),
                'available_credit_after_delete' => (float) ($reversalSummary['unallocated_credit'] ?? 0),
            ],
        ]);

        return response()->json(['status' => true, 'message' => 'Diagnostic test deleted successfully.']);
    }

    protected function authorizeOrderType(string $orderType): void
    {
        $permission = $orderType === 'pathology' ? 'create-pathology-order' : 'create-radiology-order';
        abort_unless(auth()->user()->can($permission), 403, 'Unauthorized action.');
    }

    protected function generateOrderNo(string $orderType): string
    {
        $prefix = $orderType === 'pathology' ? 'PAT' : 'RAD';
        $date = now()->format('Ymd');
        $sequence = DiagnosticOrder::withoutGlobalScopes()
            ->where('hospital_id', $this->hospital_id)
            ->where('order_type', $orderType)
            ->whereDate('created_at', now()->toDateString())
            ->count() + 1;

        return sprintf('%s-%s-%04d', $prefix, $date, $sequence);
    }

    protected function authorizeDeleteOrderType(string $orderType): void
    {
        $permission = $orderType === 'pathology' ? 'delete-pathology-order' : 'delete-radiology-order';
        abort_unless(auth()->user()->can($permission), 403, 'Unauthorized action.');
    }

    protected function resolveTestCharge(object $test, ?int $tpaId = null): float
    {
        $chargeMaster = $test->chargeMaster ?? null;
        if (!$chargeMaster) {
            return (float) ($test->standard_charge ?? 0);
        }

        if ($tpaId) {
            $tpaRate = collect($chargeMaster->tpaRates ?? [])->firstWhere('tpa_id', $tpaId);
            if ($tpaRate && isset($tpaRate->rate)) {
                return (float) $tpaRate->rate;
            }
        }

        return (float) $chargeMaster->standard_rate;
    }
}
