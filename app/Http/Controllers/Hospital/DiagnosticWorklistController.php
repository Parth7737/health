<?php

namespace App\Http\Controllers\Hospital;

use App\CentralLogics\Helpers;
use App\Http\Controllers\BaseHospitalController;
use App\Models\DiagnosticOrder;
use App\Models\DiagnosticOrderItem;
use App\Models\HeaderFooter;
use App\Models\Notifications;
use App\Models\Patient;
use App\Models\PathologyStatus;
use App\Models\PathologyTest;
use App\Models\Staff;
use App\Services\ChargeLedgerService;
use App\Services\PathologyFlagService;
use App\Services\PatientTimelineService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class DiagnosticWorklistController extends BaseHospitalController
{
    public function labIndex()
    {
        return view('hospital.lab.index', [
            'pathurl' => 'lab/lab-dashboard',
            'routes' => [
                'loadtable' => route('hospital.pathology.worklist.load'),
                'showform' => route('hospital.pathology.worklist.showform', ['item' => '__ITEM__']),
                'save' => route('hospital.pathology.worklist.save', ['item' => '__ITEM__']),
                'print' => route('hospital.pathology.worklist.print', ['item' => '__ITEM__']),
                'criticalCall' => route('hospital.pathology.worklist.critical.call', ['item' => '__ITEM__']),
                'criticalAcknowledge' => route('hospital.pathology.worklist.critical.acknowledge', ['item' => '__ITEM__']),
                'itemParameters' => route('hospital.pathology.item.parameters', ['item' => '__ITEM__']),
                'tatAnalytics' => route('hospital.pathology.worklist.tat-analytics'),
                'analyzerConfig' => route('hospital.pathology.worklist.analyzer-config'),
            ],
        ]);
    }

    public function pathologyIndex()
    {
        return view('hospital.diagnostics.pathology-worklist.index', [
            'pathurl' => 'diagnostic-pathology-worklist',
            'routes' => [
                'loadtable' => route('hospital.pathology.worklist.load'),
                'showform' => route('hospital.pathology.worklist.showform', ['item' => '__ITEM__']),
                'save' => route('hospital.pathology.worklist.save', ['item' => '__ITEM__']),
                'print' => route('hospital.pathology.worklist.print', ['item' => '__ITEM__']),
                'criticalCall' => route('hospital.pathology.worklist.critical.call', ['item' => '__ITEM__']),
                'criticalAcknowledge' => route('hospital.pathology.worklist.critical.acknowledge', ['item' => '__ITEM__']),
            ],
        ]);
    }

    public function radiologyIndex()
    {
        return view('hospital.diagnostics.radiology-worklist.index', [
            'pathurl' => 'diagnostic-radiology-worklist',
            'routes' => [
                'loadtable' => route('hospital.radiology.worklist.load'),
                'showform' => route('hospital.radiology.worklist.showform', ['item' => '__ITEM__']),
                'save' => route('hospital.radiology.worklist.save', ['item' => '__ITEM__']),
                'print' => route('hospital.radiology.worklist.print', ['item' => '__ITEM__']),
            ],
        ]);
    }

    public function createReport(Request $request) {
        $query = Staff::query()
            ->where('hospital_id', $this->hospital_id)
            ->doctor()
            ->active()
            ->select('id', 'first_name', 'last_name', 'slot_duration', 'work_timings');

        $doctors = $query->orderBy('first_name')->get()->map(function ($s) {
            return [
                'id'   => $s->id,
                'name' => trim($s->first_name . ' ' . $s->last_name),
            ];
        });
        return view('hospital.lab.create-report', [
            'routes' => [
                'save' => route('hospital.pathology.sample.save'),
                'tests' => route('hospital.pathology.sample.tests'),
            ],
            'doctors' => $doctors,
        ]);
    }

    /**
     * Pathology tests for walk-in / direct lab registration (searchable list).
     */
    public function searchWalkInPathologyTests(Request $request)
    {
        $q = trim((string) $request->input('q', ''));

        $tests = PathologyTest::query()
            ->with('category:id,name')
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $q) . '%';
                $query->where(function ($inner) use ($like) {
                    $inner->where('test_name', 'like', $like)
                        ->orWhere('test_code', 'like', $like);
                });
            })
            ->orderBy('test_name')
            ->limit(300)
            ->get()
            ->map(function (PathologyTest $test) {
                return [
                    'id' => (int) $test->id,
                    'test_name' => (string) $test->test_name,
                    'test_code' => (string) ($test->test_code ?? ''),
                    'category_name' => (string) (optional($test->category)->name ?? ''),
                    'sample_type' => (string) ($test->sample_type ?? ''),
                    'standard_charge' => (float) ($test->standard_charge ?? 0),
                ];
            })
            ->values();

        return response()->json(['data' => $tests]);
    }

    /**
     * Register a direct (walk-in) pathology sample: one diagnostic order on the patient
     * (no OPD/IPD visit), same billing + item rows as doctor-ordered tests.
     */
    public function saveWalkInSample(Request $request, ChargeLedgerService $chargeLedger, PatientTimelineService $timelineService)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => ['required', 'integer', Rule::exists('patients', 'id')->where('hospital_id', $this->hospital_id)],
            'pathology_test_ids' => ['required', 'array', 'min:1'],
            'pathology_test_ids.*' => ['required', 'integer'],
            'priority' => ['required', Rule::in(['Routine', 'Urgent', 'STAT'])],
            'clinical_notes' => ['nullable', 'string', 'max:5000'],
            'doctor_staff_id' => ['nullable', 'integer', Rule::exists('staff', 'id')->where('hospital_id', $this->hospital_id)],
        ]);

        $validator->after(function ($validator) use ($request) {
            $testIds = collect($request->input('pathology_test_ids', []))->map(fn ($id) => (int) $id)->unique()->values();
            if ($testIds->isEmpty()) {
                return;
            }
            $count = PathologyTest::query()->whereIn('id', $testIds)->count();
            if ($count !== $testIds->count()) {
                $validator->errors()->add('pathology_test_ids', 'One or more selected tests are invalid for this hospital.');
            }
            $missingCharge = PathologyTest::query()
                ->whereIn('id', $testIds)
                ->whereNull('charge_master_id')
                ->pluck('test_name')
                ->all();
            if (!empty($missingCharge)) {
                $validator->errors()->add('pathology_test_ids', 'Charge master not mapped for: ' . implode(', ', $missingCharge));
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $patient = Patient::query()
            ->where('hospital_id', $this->hospital_id)
            ->whereKey((int) $request->patient_id)
            ->firstOrFail();

        $testIds = collect($request->pathology_test_ids)->map(fn ($id) => (int) $id)->unique()->values();
        $tests = PathologyTest::with(['category:id,name', 'parameters.unit:id,name', 'chargeMaster.tpaRates'])
            ->whereIn('id', $testIds)
            ->get();

        $priorityValue = (string) $request->input('priority', 'Routine');
        $clinicalNotes = trim((string) $request->input('clinical_notes', ''));
        $doctorStaffId = $request->filled('doctor_staff_id') ? (int) $request->doctor_staff_id : null;

        $order = DB::transaction(function () use ($patient, $tests, $chargeLedger, $priorityValue, $clinicalNotes, $doctorStaffId) {
            $order = DiagnosticOrder::create([
                'hospital_id' => $this->hospital_id,
                'patient_id' => $patient->id,
                'visitable_type' => null,
                'visitable_id' => null,
                'order_type' => 'pathology',
                'type' => 'manual',
                'order_no' => $this->generateWalkInPathologyOrderNo(),
                'ordered_by' => auth()->id(),
                'doctor_staff_id' => $doctorStaffId,
                'notes' => $clinicalNotes !== '' ? $clinicalNotes : null,
                'status' => 'ordered',
            ]);

            foreach ($tests as $test) {
                $resolvedCharge = $this->resolveWalkInTestCharge($test, null);

                $item = $order->items()->create([
                    'department' => 'pathology',
                    'testable_type' => PathologyTest::class,
                    'testable_id' => $test->id,
                    'test_name' => $test->test_name,
                    'test_code' => $test->test_code,
                    'category_name' => optional($test->category)->name,
                    'priority' => $priorityValue,
                    'sample_type' => $test->sample_type,
                    'method' => $test->method,
                    'expected_report_days' => $test->report_days,
                    'standard_charge' => $resolvedCharge,
                    'status' => 'sample_collected',
                    'sample_collected_at' => now(),
                    'sample_collected_by' => auth()->id(),
                ]);

                $chargeLedger->upsertCharge([
                    'hospital_id' => $this->hospital_id,
                    'patient_id' => $patient->id,
                    'visitable_type' => Patient::class,
                    'visitable_id' => $patient->id,
                    'source_type' => DiagnosticOrderItem::class,
                    'source_id' => $item->id,
                    'module' => 'pathology',
                    'particular' => 'PATHOLOGY - ' . $test->test_name,
                    'charge_master_id' => $test->charge_master_id,
                    'charge_category' => 'pathology',
                    'calculation_type' => 'fixed',
                    'billing_frequency' => 'one_time',
                    'quantity' => 1,
                    'unit_rate' => $resolvedCharge,
                    'net_amount' => $resolvedCharge,
                    'payer_type' => 'self',
                    'tpa_id' => null,
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

        $timelineService->log($patient, [
            'event_key' => 'patient.pathology_walk_in_registered',
            'title' => 'Direct lab sample registered',
            'description' => 'Pathology order ' . $order->order_no . ' with ' . $tests->count() . ' test(s).',
            'meta' => [
                'order_no' => $order->order_no,
                'type' => 'manual',
                'doctor_staff_id' => $doctorStaffId,
                'priority' => $priorityValue,
                'test_count' => $tests->count(),
            ],
        ]);

        $firstItemId = (int) $order->items()->orderBy('id')->value('id');

        return response()->json([
            'status' => true,
            'message' => 'Sample registered. Order ' . $order->order_no . ' — use this as barcode / accession reference.',
            'order_no' => $order->order_no,
            'diagnostic_order_id' => (int) $order->id,
            'first_item_id' => $firstItemId,
        ]);
    }

    protected function generateWalkInPathologyOrderNo(): string
    {
        $prefix = 'PAT';
        $date = now()->format('Ymd');
        $sequence = DiagnosticOrder::withoutGlobalScopes()
            ->where('hospital_id', $this->hospital_id)
            ->where('order_type', 'pathology')
            ->whereDate('created_at', now()->toDateString())
            ->count() + 1;

        return sprintf('%s-%s-%04d', $prefix, $date, $sequence);
    }

    protected function resolveWalkInTestCharge(object $test, ?int $tpaId = null): float
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

    public function loadPathology(Request $request)
    {
        $searchText = strtolower(trim((string) $request->input('search_text', '')));
        $itemWise = $request->boolean('item_wise');

        $items = DiagnosticOrderItem::with(['order.patient', 'order.visitable', 'order.orderedByUser', 'parameters', 'patientCharge', 'sampleCollectedByUser'])
            ->where('department', 'pathology')
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->where('category_name', $request->category);
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', Carbon::parse($request->date_from)->format('Y-m-d'));
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', Carbon::parse($request->date_to)->format('Y-m-d'));
            })
            ->orderByRaw("CASE WHEN status = 'completed' THEN 1 ELSE 0 END ASC")
            ->orderBy('created_at', 'asc')
            ->get();

        if ($itemWise) {
            $itemRows = $items->map(function ($item) {
                $order = $item->order;
                $patient = $order?->patient;
                $visitable = $order?->visitable;
                $statusKey = $this->normalizeStatus((string) $item->status);
                $priority = $this->resolvePriority([(string) ($item->priority ?? '')]);
                $charge = $item->patientCharge;
                $patientId = $order?->patient_id;
                $paymentStatus = (string) ($charge?->payment_status ?? $item->payment_status ?? 'unpaid');

                $visitNumber = $visitable->case_no ?? $visitable->admission_no ?? '-';
                $patientMeta = [];
                if ($visitNumber !== '-') {
                    $patientMeta[] = 'Visit: ' . $visitNumber;
                }
                if (filled($patient?->age) || filled($patient?->gender)) {
                    $patientMeta[] = trim((filled($patient?->age) ? $patient->age . 'y' : '') . ' ' . ($patient?->gender ?? ''));
                }

                $reportedAt = $item->reported_at ?? $item->updated_at ?? $item->created_at;
                $isToday = $reportedAt ? Carbon::parse($reportedAt)->isToday() : false;

                $criticalTodayEntries = collect();
                if ($statusKey === 'completed' && $isToday) {
                    $criticalTodayEntries = $item->parameters
                        ->filter(fn ($parameter) => in_array((string) $parameter->result_flag, ['critical_low', 'critical_high'], true))
                        ->map(function ($parameter) use ($item, $statusKey) {
                            $flagKey = (string) $parameter->result_flag;
                            $flagLabel = $flagKey === 'critical_low' ? '↓↓ CRITICAL LOW' : '↑↑ CRITICAL HIGH';

                            return [
                                'critical_key' => (string) $item->id . ':' . (string) ($parameter->id ?? $parameter->parameter_name),
                                'item_id' => (int) $item->id,
                                'test_name' => (string) ($item->test_name ?? '-'),
                                'parameter_name' => (string) ($parameter->parameter_name ?? '-'),
                                'result_value' => (string) ($parameter->result_value ?? '-'),
                                'normal_range' => (string) ($parameter->normal_range ?? '-'),
                                'result_flag' => $flagKey,
                                'flag_label' => $flagLabel,
                                'status_key' => $statusKey,
                                'status_label' => $this->humanizeStatus($statusKey),
                                'doctor_name' => (string) ($item->order?->orderedByUser?->name ?? '-'),
                                'doctor_user_id' => $item->order?->orderedByUser?->id,
                                'is_acknowledged' => !is_null($item->critical_acknowledged_at),
                                'acknowledged_at' => optional($item->critical_acknowledged_at)->format('d-m-Y H:i'),
                                'is_doctor_called' => !is_null($item->critical_doctor_called_at),
                                'doctor_called_at' => optional($item->critical_doctor_called_at)->format('d-m-Y H:i'),
                                'reported_at' => optional($item->reported_at ?? $item->updated_at)->format('d-m-Y H:i') ?? '-',
                                'print_url' => route('hospital.pathology.worklist.print', ['item' => $item->id]),
                            ];
                        })
                        ->values();
                }

                $collectedLabel = filled($item->sample_collected_at)
                    ? (optional($item->sample_collected_at)->format('d-m-Y H:i') . ($item->sampleCollectedByUser?->name ? ' by ' . $item->sampleCollectedByUser->name : ''))
                    : 'Pending';

                $testEntry = [
                    'item_id' => (int) $item->id,
                    'test_name' => (string) ($item->test_name ?? '-'),
                    'category_name' => (string) ($item->category_name ?? 'Uncategorized'),
                    'status_key' => $statusKey,
                    'status_label' => $this->humanizeStatus($statusKey),
                    'payment_status' => $paymentStatus,
                    'showform_url' => route('hospital.pathology.worklist.showform', ['item' => $item->id]),
                    'status_url' => route('hospital.pathology.worklist.status', ['item' => $item->id]),
                    'print_url' => route('hospital.pathology.worklist.print', ['item' => $item->id]),
                    'payment_url' => ($charge && $patientId) ? route('hospital.opd-patient.charges.show-payment-form', ['patient' => $patientId]) : null,
                    'charge_id' => $charge?->id,
                    'report_ready' => $statusKey === 'completed',
                ];

                return [
                    'id' => (int) $item->id,
                    'item_id' => (int) $item->id,
                    'sample_id' => (string) ($order?->order_no ?? ('SAMPLE-' . $item->diagnostic_order_id)),
                    'order_no' => (string) ($order?->order_no ?? '-'),
                    'ordered_at' => optional($order?->created_at ?? $item->created_at)?->format('d-m-Y H:i') ?? '-',
                    'patient_name' => (string) ($patient?->name ?? '-'),
                    'patient_mrn' => (string) ($patient?->mrn ?? $patient?->patient_id ?? '-'),
                    'visit_no' => (string) $visitNumber,
                    'patient_context' => !empty($patientMeta) ? implode(' | ', $patientMeta) : '-',
                    'tests_ordered' => [$testEntry],
                    'category' => (string) ($item->category_name ?? 'Uncategorized'),
                    'category_list' => [(string) ($item->category_name ?? 'Uncategorized')],
                    'priority' => $priority,
                    'ordered_by' => (string) ($order?->orderedByUser?->name ?? '-'),
                    'collected' => (string) $collectedLabel,
                    'status' => $this->formatStatusBadge($statusKey),
                    'status_key' => $statusKey,
                    'status_label' => $this->humanizeStatus($statusKey),
                    'status_breakdown' => [[
                        'item_id' => (int) $item->id,
                        'label' => $this->humanizeStatus($statusKey),
                        'status_key' => $statusKey,
                    ]],
                    'critical_today_count' => $criticalTodayEntries->count(),
                    'critical_today_entries' => $criticalTodayEntries->all(),
                    'report_summary' => (string) ($item->report_summary ?? ''),
                    'report_text' => (string) ($item->report_text ?? ''),
                ];
            })->values();

            if ($searchText !== '') {
                $itemRows = $itemRows->filter(function ($row) use ($searchText) {
                    $searchableParts = [
                        $row['sample_id'] ?? '',
                        $row['order_no'] ?? '',
                        $row['item_id'] ?? '',
                        $row['patient_name'] ?? '',
                        $row['patient_mrn'] ?? '',
                        $row['visit_no'] ?? '',
                        $row['category'] ?? '',
                        $row['priority'] ?? '',
                        $row['ordered_by'] ?? '',
                        $row['collected'] ?? '',
                        implode(' ', collect($row['tests_ordered'] ?? [])->pluck('test_name')->all()),
                    ];

                    $haystack = strtolower(implode(' ', $searchableParts));

                    return str_contains($haystack, $searchText);
                })->values();
            }

            if ($request->boolean('urgent_only')) {
                $itemRows = $itemRows->filter(function ($row) {
                    $priority = strtolower((string) ($row['priority'] ?? ''));
                    $isUrgentPriority = in_array($priority, ['urgent', 'stat'], true);
                    $isCompleted = strtolower((string) ($row['status_key'] ?? '')) === 'completed';

                    return $isUrgentPriority && !$isCompleted;
                })->values();
            }

            if ($request->boolean('exclude_completed_queue')) {
                $itemRows = $itemRows->filter(function ($row) {
                    return strtolower((string) ($row['status_key'] ?? '')) !== 'completed';
                })->values();
            }

            return DataTables::of($itemRows)
                ->rawColumns(['status'])
                ->make(true);
        }

        $groupedRows = $items->groupBy('diagnostic_order_id')->map(function ($group) {
            $firstItem = $group->sortBy('created_at')->first();
            $order = $firstItem?->order;
            $patient = $order?->patient;
            $visitable = $order?->visitable;
            $categoryList = $group->pluck('category_name')->filter()->unique()->values();
            $priority = $this->resolvePriority($group->pluck('priority')->filter()->all());
            $statusKey = $this->resolveGroupedStatus($group->pluck('status')->all());
            $collectedItems = $group->filter(fn ($item) => filled($item->sample_collected_at));
            $collectedLabel = 'Pending';

            if ($collectedItems->isNotEmpty()) {
                if ($collectedItems->count() === $group->count()) {
                    $lastCollected = $collectedItems->sortByDesc('sample_collected_at')->first();
                    $collectorName = $lastCollected?->sampleCollectedByUser?->name;
                    $collectedLabel = optional($lastCollected?->sample_collected_at)?->format('d-m-Y H:i') ?? 'Collected';
                    if ($collectorName) {
                        $collectedLabel .= ' by ' . $collectorName;
                    }
                } else {
                    $collectedLabel = $collectedItems->count() . '/' . $group->count() . ' collected';
                }
            }

            $testEntries = $group->map(function ($item) {
                $charge = $item->patientCharge;
                $patientId = $item->order->patient_id ?? null;
                $statusKey = $this->normalizeStatus((string) $item->status);
                $paymentStatus = (string) ($charge?->payment_status ?? $item->payment_status ?? 'unpaid');

                return [
                    'item_id' => (int) $item->id,
                    'test_name' => (string) ($item->test_name ?? '-'),
                    'category_name' => (string) ($item->category_name ?? 'Uncategorized'),
                    'status_key' => $statusKey,
                    'status_label' => $this->humanizeStatus($statusKey),
                    'payment_status' => $paymentStatus,
                    'showform_url' => route('hospital.pathology.worklist.showform', ['item' => $item->id]),
                    'status_url' => route('hospital.pathology.worklist.status', ['item' => $item->id]),
                    'print_url' => route('hospital.pathology.worklist.print', ['item' => $item->id]),
                    'payment_url' => ($charge && $patientId) ? route('hospital.opd-patient.charges.show-payment-form', ['patient' => $patientId]) : null,
                    'charge_id' => $charge?->id,
                    'report_ready' => $statusKey === 'completed',
                ];
            })->values()->all();

            $visitNumber = $visitable->case_no ?? $visitable->admission_no ?? '-';
            $patientMeta = [];
            if ($visitNumber !== '-') {
                $patientMeta[] = 'Visit: ' . $visitNumber;
            }
            if (filled($patient?->age) || filled($patient?->gender)) {
                $patientMeta[] = trim((filled($patient?->age) ? $patient->age . 'y' : '') . ' ' . ($patient?->gender ?? ''));
            }

            $criticalTodayEntries = $group->flatMap(function ($item) {
                $itemStatusKey = $this->normalizeStatus((string) $item->status);
                $reportedAt = $item->reported_at ?? $item->updated_at ?? $item->created_at;
                $isToday = $reportedAt ? Carbon::parse($reportedAt)->isToday() : false;

                if ($itemStatusKey !== 'completed' || !$isToday) {
                    return [];
                }

                return $item->parameters
                    ->filter(fn ($parameter) => in_array((string) $parameter->result_flag, ['critical_low', 'critical_high'], true))
                    ->map(function ($parameter) use ($item, $itemStatusKey) {
                        $flagKey = (string) $parameter->result_flag;
                        $flagLabel = $flagKey === 'critical_low' ? '↓↓ CRITICAL LOW' : '↑↑ CRITICAL HIGH';

                        return [
                            'critical_key' => (string) $item->id . ':' . (string) ($parameter->id ?? $parameter->parameter_name),
                            'item_id' => (int) $item->id,
                            'test_name' => (string) ($item->test_name ?? '-'),
                            'parameter_name' => (string) ($parameter->parameter_name ?? '-'),
                            'result_value' => (string) ($parameter->result_value ?? '-'),
                            'normal_range' => (string) ($parameter->normal_range ?? '-'),
                            'result_flag' => $flagKey,
                            'flag_label' => $flagLabel,
                            'status_key' => $itemStatusKey,
                            'status_label' => $this->humanizeStatus($itemStatusKey),
                            'doctor_name' => (string) ($item->order?->orderedByUser?->name ?? '-'),
                            'doctor_user_id' => $item->order?->orderedByUser?->id,
                            'is_acknowledged' => !is_null($item->critical_acknowledged_at),
                            'acknowledged_at' => optional($item->critical_acknowledged_at)->format('d-m-Y H:i'),
                            'is_doctor_called' => !is_null($item->critical_doctor_called_at),
                            'doctor_called_at' => optional($item->critical_doctor_called_at)->format('d-m-Y H:i'),
                            'reported_at' => optional($item->reported_at ?? $item->updated_at)->format('d-m-Y H:i') ?? '-',
                            'print_url' => route('hospital.pathology.worklist.print', ['item' => $item->id]),
                        ];
                    })
                    ->values();
            })->values();

            return [
                'id' => (int) $firstItem->id,
                'sample_id' => (string) ($order->order_no ?? ('SAMPLE-' . $firstItem->diagnostic_order_id)),
                'order_no' => (string) ($order->order_no ?? '-'),
                'ordered_at' => optional($order?->created_at ?? $firstItem->created_at)?->format('d-m-Y H:i') ?? '-',
                'patient_name' => (string) ($patient->name ?? '-'),
                'patient_mrn' => (string) ($patient->mrn ?? $patient->patient_id ?? '-'),
                'visit_no' => (string) $visitNumber,
                'patient_context' => !empty($patientMeta) ? implode(' | ', $patientMeta) : '-',
                'tests_ordered' => $testEntries,
                'category' => $categoryList->isNotEmpty() ? $categoryList->implode(', ') : 'Uncategorized',
                'category_list' => $categoryList->isNotEmpty() ? $categoryList->all() : ['Uncategorized'],
                'priority' => $priority,
                'ordered_by' => (string) ($order?->orderedByUser?->name ?? '-'),
                'collected' => $collectedLabel,
                'status' => $this->formatStatusBadge($statusKey),
                'status_key' => $statusKey,
                'status_label' => $this->humanizeStatus($statusKey),
                'status_breakdown' => $group->map(function ($item) {
                    $normalizedStatus = $this->normalizeStatus((string) $item->status);

                    return [
                        'item_id' => (int) $item->id,
                        'label' => $this->humanizeStatus($normalizedStatus),
                        'status_key' => $normalizedStatus,
                    ];
                })->values()->all(),
                'critical_today_count' => $criticalTodayEntries->count(),
                'critical_today_entries' => $criticalTodayEntries->all(),
                'report_summary' => $group->pluck('report_summary')->filter()->implode(' '),
                'report_text' => $group->pluck('report_text')->filter()->implode(' '),
            ];
        })->values();

        if ($searchText !== '') {
            $groupedRows = $groupedRows->filter(function ($row) use ($searchText) {
                $searchableParts = [
                    $row['sample_id'] ?? '',
                    $row['order_no'] ?? '',
                    $row['patient_name'] ?? '',
                    $row['patient_mrn'] ?? '',
                    $row['visit_no'] ?? '',
                    $row['category'] ?? '',
                    $row['priority'] ?? '',
                    $row['ordered_by'] ?? '',
                    $row['collected'] ?? '',
                    implode(' ', collect($row['tests_ordered'] ?? [])->pluck('test_name')->all()),
                    implode(' ', collect($row['tests_ordered'] ?? [])->pluck('item_id')->all()),
                ];

                $haystack = strtolower(implode(' ', $searchableParts));

                return str_contains($haystack, $searchText);
            })->values();
        }

        if ($request->boolean('urgent_only')) {
            $groupedRows = $groupedRows->filter(function ($row) {
                $priority = strtolower((string) ($row['priority'] ?? ''));
                $isUrgentPriority = in_array($priority, ['urgent', 'stat'], true);
                $isCompleted = strtolower((string) ($row['status_key'] ?? '')) === 'completed';

                return $isUrgentPriority && !$isCompleted;
            })->values();
        }

        if ($request->boolean('exclude_completed_queue')) {
            $groupedRows = $groupedRows->filter(function ($row) {
                return strtolower((string) ($row['status_key'] ?? '')) !== 'completed';
            })->values();
        }

        return DataTables::of($groupedRows)
            ->rawColumns(['status'])
            ->make(true);
    }

    public function loadRadiology(Request $request)
    {
        $data = DiagnosticOrderItem::with(['order.patient', 'order.visitable', 'patientCharge'])
            ->where('department', 'radiology')
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', Carbon::parse($request->date_from)->format('Y-m-d'));
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', Carbon::parse($request->date_to)->format('Y-m-d'));
            })
            ->orderByRaw("CASE WHEN status = 'completed' THEN 1 ELSE 0 END ASC")
            ->orderBy('created_at', 'asc')
            ->select('*');

        return DataTables::of($data)
            ->addColumn('patient_name', fn ($row) => optional($row->order?->patient)->name ?? '-')
            ->addColumn('visit_no', fn ($row) => optional($row->order?->visitable)->case_no ?? '-')
            ->addColumn('order_no', fn ($row) => optional($row->order)->order_no ?? '-')
            ->addColumn('ordered_at', fn ($row) => optional($row->created_at)?->format('d-m-Y H:i') ?? '-')
            ->editColumn('status', fn ($row) => $this->formatStatusBadge((string) $row->status))
            ->addColumn('payment_status', fn ($row) => $this->formatPaymentBadge((string) ($row->patientCharge?->payment_status ?? $row->payment_status ?? 'unpaid')))
            ->addColumn('actions', function ($row) {
                return view('hospital.diagnostics.partials.radiology-actions', compact('row'))->render();
            })
            ->rawColumns(['status', 'payment_status', 'actions'])
            ->make(true);
    }

    public function showPathologyForm(DiagnosticOrderItem $item)
    {
        $this->authorizeItem($item, 'pathology');
        $statuses = PathologyStatus::orderBy('name')->get();

        return view('hospital.diagnostics.pathology-worklist.form', [
            'item' => $item->load(['order.patient', 'order.visitable', 'parameters.parameterable', 'patientCharge']),
            'statuses' => $statuses,
        ]);
    }

    public function showRadiologyForm(DiagnosticOrderItem $item)
    {
        $this->authorizeItem($item, 'radiology');

        return view('hospital.diagnostics.radiology-worklist.form', [
            'item' => $item->load(['order.patient', 'order.visitable', 'patientCharge']),
        ]);
    }

    public function savePathology(Request $request, DiagnosticOrderItem $item)
    {
        $this->authorizeItem($item, 'pathology');

        $validator = Validator::make($request->all(), [
            'report_summary'      => 'nullable|string',
            'report_text'         => 'nullable|string',
            'technician_remarks'  => 'nullable|string|max:2000',
            'pathologist_comment' => 'nullable|string|max:2000',
            'result_value'        => 'nullable|array',
            'remarks'             => 'nullable|array',
            'save_as'             => 'nullable|in:draft,final',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $resultValues = $request->result_value ?? [];
        $remarks      = $request->remarks ?? [];
        $isDraft      = $request->input('save_as', 'final') === 'draft';

        $hasParameterResult = collect($resultValues)->filter(fn ($value) => filled($value))->isNotEmpty();
        $hasFinalReportData = filled($request->report_summary) || filled($request->report_text) || $hasParameterResult;

        if ($isDraft) {
            $newStatus  = 'in_progress';
            $reportedAt = $item->reported_at;
        } else {
            $newStatus  = $hasFinalReportData ? 'completed' : $item->status;
            $reportedAt = $hasFinalReportData ? now() : $item->reported_at;
        }

        $item->update([
            'report_summary'      => $request->report_summary,
            'report_text'         => $request->report_text,
            'technician_remarks'  => $request->technician_remarks,
            'pathologist_comment' => $request->pathologist_comment,
            'status'              => $newStatus,
            'reported_at'         => $reportedAt,
        ]);

        // Load parameters with their definition details for flag generation
        $item->load('order', 'parameters.parameterable');

        foreach ($item->parameters as $parameter) {
            $resultValue = $resultValues[$parameter->id] ?? null;
            $resultFlag = null;

            // Auto-generate flag if result value provided and parameter definition exists
            if (filled($resultValue) && $parameter->parameterable) {
                $paramDef = $parameter->parameterable;
                $resultFlag = PathologyFlagService::generateFlag(
                    $resultValue,
                    $paramDef->min_value ?? null,
                    $paramDef->max_value ?? null,
                    $paramDef->critical_low ?? null,
                    $paramDef->critical_high ?? null
                );
            }

            $parameter->update([
                'result_value' => $resultValue,
                'result_flag' => $resultFlag,
                'remarks' => $remarks[$parameter->id] ?? null,
            ]);
        }

        return response()->json([
            'status'       => true,
            'message'      => $isDraft ? 'Draft saved successfully.' : 'Pathology report updated successfully.',
            'is_draft'     => $isDraft,
            'item_status'  => $item->fresh()->status,
            'print_url'    => route('hospital.pathology.worklist.print', $item->id),
        ]);
    }

    public function saveRadiology(Request $request, DiagnosticOrderItem $item)
    {
        $this->authorizeItem($item, 'radiology');

        $validator = Validator::make($request->all(), [
            'report_summary' => 'nullable|string',
            'report_impression' => 'nullable|string',
            'report_text' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $hasFinalReportData = filled($request->report_summary)
            || filled($request->report_impression)
            || filled($request->report_text);

        $item->update([
            'report_summary' => $request->report_summary,
            'report_impression' => $request->report_impression,
            'report_text' => $request->report_text,
            'status' => $hasFinalReportData ? 'completed' : $item->status,
            'reported_at' => $hasFinalReportData ? now() : $item->reported_at,
        ]);

        return response()->json(['status' => true, 'message' => 'Radiology report updated successfully.']);
    }

    public function printPathology(DiagnosticOrderItem $item)
    {
        $this->authorizeItem($item, 'pathology');
        abort_unless($this->isPrintable($item), 403, 'Report is not ready for printing.');

        $printTemplate = HeaderFooter::query()
            ->where('type', 'pathology')
            ->first();

        return view('hospital.diagnostics.pathology-worklist.print', [
            'item' => $item->load(['order.patient', 'order.visitable', 'parameters', 'patientCharge']),
            'hospital' => auth()->user()?->hospital,
            'printTemplate' => $printTemplate,
        ]);
    }

    public function printRadiology(DiagnosticOrderItem $item)
    {
        $this->authorizeItem($item, 'radiology');
        abort_unless($this->isPrintable($item), 403, 'Report is not ready for printing.');

        $printTemplate = HeaderFooter::query()
            ->where('type', 'radiology')
            ->first();

        return view('hospital.diagnostics.radiology-worklist.print', [
            'item' => $item->load(['order.patient', 'order.visitable', 'patientCharge']),
            'hospital' => auth()->user()?->hospital,
            'printTemplate' => $printTemplate,
        ]);
    }

    public function updatePathologyStatus(DiagnosticOrderItem $item)
    {
        $this->authorizeItem($item, 'pathology');

        $currentStatus = strtolower(str_replace([' ', '-'], '_', (string) $item->status));
        $nextStatus = $this->nextStatus($currentStatus, ['ordered', 'sample_collected', 'in_progress']);
        if (!$nextStatus) {
            return response()->json(['status' => false, 'message' => 'No further status transition available.'], 422);
        }

        $item->status = $nextStatus;
        if ($nextStatus === 'sample_collected') {
            $item->sample_collected_at = now();
            $item->sample_collected_by = auth()->id();
        }
        $item->save();

        return response()->json(['status' => true, 'message' => 'Pathology status updated to ' . str_replace('_', ' ', $nextStatus) . '.']);
    }

    public function callCriticalDoctor(DiagnosticOrderItem $item)
    {
        $this->authorizeItem($item, 'pathology');

        $item->load(['order.patient', 'order.orderedByUser']);

        if ($this->normalizeStatus((string) $item->status) !== 'completed') {
            return response()->json(['status' => false, 'message' => 'Only completed reports can trigger critical doctor calls.'], 422);
        }

        $doctor = $item->order?->orderedByUser;
        if (!$doctor) {
            return response()->json(['status' => false, 'message' => 'No assigned doctor found for this report.'], 422);
        }

        $caller = auth()->user();
        $item->update(['critical_doctor_called_at' => now()]);

        Notifications::create([
            'user_id' => $doctor->id,
            'hospital_id' => $this->hospital_id,
            'type' => 'critical_value_call',
            'date' => now()->format('Y-m-d'),
            'message' => 'Critical value alert for patient ' . ($item->order?->patient?->name ?? 'Unknown')
                . ' (' . ($item->order?->order_no ?? 'Order #' . $item->diagnostic_order_id) . ') called by '
                . ($caller?->name ?? 'Lab staff') . '.',
            'is_read' => 0,
            'ref_id' => $item->id,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Doctor notified successfully for this critical report.',
            'doctor_name' => $doctor->name,
        ]);
    }

    public function acknowledgeCritical(DiagnosticOrderItem $item)
    {
        $this->authorizeItem($item, 'pathology');

        $item->load(['order.patient', 'order.orderedByUser']);

        if (is_null($item->critical_acknowledged_at)) {
            $item->update([
                'critical_acknowledged_at' => now(),
                'critical_acknowledged_by' => auth()->id(),
            ]);
        }

        $doctor = $item->order?->orderedByUser;
        if ($doctor) {
            Notifications::create([
                'user_id' => $doctor->id,
                'hospital_id' => $this->hospital_id,
                'type' => 'critical_value_acknowledged',
                'date' => now()->format('Y-m-d'),
                'message' => 'Critical value for patient ' . ($item->order?->patient?->name ?? 'Unknown')
                    . ' (' . ($item->order?->order_no ?? 'Order #' . $item->diagnostic_order_id) . ') has been acknowledged by '
                    . (auth()->user()?->name ?? 'Lab staff') . '.',
                'is_read' => 0,
                'ref_id' => $item->id,
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Critical report acknowledged successfully.',
            'acknowledged_at' => optional($item->fresh()->critical_acknowledged_at)->format('d-m-Y H:i'),
        ]);
    }

    public function updateRadiologyStatus(DiagnosticOrderItem $item)
    {
        $this->authorizeItem($item, 'radiology');

        $currentStatus = strtolower(str_replace([' ', '-'], '_', (string) $item->status));
        $nextStatus = $this->nextStatus($currentStatus, ['ordered', 'in_progress']);
        if (!$nextStatus) {
            return response()->json(['status' => false, 'message' => 'No further status transition available.'], 422);
        }

        $item->status = $nextStatus;
        $item->save();

        return response()->json(['status' => true, 'message' => 'Radiology status updated to ' . str_replace('_', ' ', $nextStatus) . '.']);
    }

    protected function authorizeItem(DiagnosticOrderItem $item, string $department): void
    {
        abort_if($item->department !== $department, 404);
        abort_if(optional($item->order)->hospital_id != $this->hospital_id, 403, 'Unauthorized action.');
    }

    protected function nextStatus(string $currentStatus, array $flow): ?string
    {
        $index = array_search($currentStatus, $flow, true);
        if ($index === false) {
            return null;
        }

        return $flow[$index + 1] ?? null;
    }

    protected function formatStatusBadge(string $status): string
    {
        $normalized = strtolower(str_replace([' ', '-'], '_', trim($status)));

        $map = [
            'ordered' => ['label' => 'Ordered', 'class' => 'badge badge-light-secondary'],
            'sample_collected' => ['label' => 'Sample Collected', 'class' => 'badge badge-light-warning'],
            'in_progress' => ['label' => 'In Progress', 'class' => 'badge badge-light-info'],
            'completed' => ['label' => 'Completed', 'class' => 'badge badge-light-success'],
            'cancelled' => ['label' => 'Cancelled', 'class' => 'badge badge-light-danger'],
        ];

        $badge = $map[$normalized] ?? [
            'label' => ucwords(str_replace('_', ' ', $normalized ?: 'unknown')),
            'class' => 'badge badge-light-dark',
        ];

        return '<span class="' . $badge['class'] . '">' . e($badge['label']) . '</span>';
    }

    protected function formatPaymentBadge(string $status): string
    {
        $normalized = strtolower(str_replace([' ', '-'], '_', trim($status)));

        $map = [
            'unpaid' => ['label' => 'Unpaid', 'class' => 'badge badge-light-danger'],
            'partial' => ['label' => 'Partial', 'class' => 'badge badge-light-warning'],
            'paid' => ['label' => 'Paid', 'class' => 'badge badge-light-success'],
        ];

        $badge = $map[$normalized] ?? [
            'label' => ucwords(str_replace('_', ' ', $normalized ?: 'unpaid')),
            'class' => 'badge badge-light-dark',
        ];

        return '<span class="' . $badge['class'] . '">' . e($badge['label']) . '</span>';
    }

    protected function normalizeStatus(string $status): string
    {
        return strtolower(str_replace([' ', '-'], '_', trim($status))) ?: 'ordered';
    }

    protected function humanizeStatus(string $status): string
    {
        return ucwords(str_replace('_', ' ', $this->normalizeStatus($status)));
    }

    protected function resolveGroupedStatus(array $statuses): string
    {
        $normalized = collect($statuses)
            ->map(fn ($status) => $this->normalizeStatus((string) $status))
            ->filter()
            ->values();

        if ($normalized->isEmpty()) {
            return 'ordered';
        }

        if ($normalized->every(fn ($status) => $status === 'completed')) {
            return 'completed';
        }

        foreach (['in_progress', 'sample_collected', 'ordered', 'cancelled'] as $status) {
            if ($normalized->contains($status)) {
                return $status;
            }
        }

        return 'ordered';
    }

    protected function resolvePriority(array $priorities): string
    {
        $resolved = collect($priorities)
            ->map(function ($priority) {
                $normalized = strtoupper(trim((string) $priority));

                return match ($normalized) {
                    'STAT' => 'STAT',
                    'URGENT' => 'Urgent',
                    default => 'Routine',
                };
            })
            ->sortByDesc(fn ($priority) => $this->priorityRank($priority))
            ->first();

        return $resolved ?: 'Routine';
    }

    protected function priorityRank(string $priority): int
    {
        return match (strtoupper(trim($priority))) {
            'STAT' => 3,
            'URGENT' => 2,
            default => 1,
        };
    }

    protected function isPrintable(DiagnosticOrderItem $item): bool
    {
        return strtolower(str_replace([' ', '-'], '_', (string) $item->status)) === 'completed';
    }

    public function getTatAnalytics(Request $request)
    {
        try {
            $searchText = strtolower(trim((string) $request->input('search_text', '')));
            $category = trim((string) $request->input('category', ''));

            $items = DiagnosticOrderItem::query()
                ->where('department', 'pathology')
                ->withCount([
                    'parameters as critical_count' => function ($query) {
                        $query->whereIn('result_flag', ['critical_low', 'critical_high']);
                    },
                ])
                ->when($category !== '', function ($query) use ($category) {
                    $query->where('category_name', $category);
                })
                ->when($request->filled('date_from'), function ($query) use ($request) {
                    $query->whereDate('created_at', '>=', Carbon::parse($request->date_from)->format('Y-m-d'));
                })
                ->when($request->filled('date_to'), function ($query) use ($request) {
                    $query->whereDate('created_at', '<=', Carbon::parse($request->date_to)->format('Y-m-d'));
                })
                ->when($searchText !== '', function ($query) use ($searchText) {
                    $query->where(function ($inner) use ($searchText) {
                        $inner->whereRaw('LOWER(test_name) LIKE ?', ['%' . $searchText . '%'])
                            ->orWhereRaw('LOWER(category_name) LIKE ?', ['%' . $searchText . '%'])
                            ->orWhereHas('order', function ($orderQuery) use ($searchText) {
                                $orderQuery->whereRaw('LOWER(order_no) LIKE ?', ['%' . $searchText . '%'])
                                    ->orWhereHas('patient', function ($patientQuery) use ($searchText) {
                                        $patientQuery->whereRaw('LOWER(name) LIKE ?', ['%' . $searchText . '%'])
                                            ->orWhereRaw('LOWER(mrn) LIKE ?', ['%' . $searchText . '%'])
                                            ->orWhereRaw('LOWER(patient_id) LIKE ?', ['%' . $searchText . '%']);
                                    });
                            });
                    });
                })
                ->get(['id', 'category_name', 'status', 'created_at']);

            $now = Carbon::now();

            $categoryBucket = [];
            foreach ($items as $item) {
                $categoryName = trim((string) ($item->category_name ?: 'Uncategorized'));
                if (!isset($categoryBucket[$categoryName])) {
                    $categoryBucket[$categoryName] = [
                        'category' => $categoryName,
                        'total' => 0,
                        'completed' => 0,
                        'critical' => 0,
                        'tat_minutes_total' => 0,
                        'tat_count' => 0,
                    ];
                }

                $categoryBucket[$categoryName]['total'] += 1;
                if ($this->normalizeStatus((string) $item->status) === 'completed') {
                    $categoryBucket[$categoryName]['completed'] += 1;
                }

                $categoryBucket[$categoryName]['critical'] += (int) ($item->critical_count ?? 0);

                if ($item->created_at) {
                    $minutes = Carbon::parse($item->created_at)->diffInMinutes($now);
                    $categoryBucket[$categoryName]['tat_minutes_total'] += max(0, (int) $minutes);
                    $categoryBucket[$categoryName]['tat_count'] += 1;
                }
            }

            $tatByDepartment = collect($categoryBucket)
                ->map(function ($entry) {
                    $avgHours = $entry['tat_count'] > 0
                        ? round(($entry['tat_minutes_total'] / $entry['tat_count']) / 60, 1)
                        : 0.0;

                    return [
                        'label' => (string) $entry['category'],
                        'avg_hours' => $avgHours,
                    ];
                })
                ->sortByDesc('avg_hours')
                ->take(7)
                ->values()
                ->all();

            $dayKeys = collect(range(6, 0))
                ->map(function ($daysBack) use ($now) {
                    return $now->copy()->subDays($daysBack)->format('Y-m-d');
                });

            $dayCounts = $dayKeys->mapWithKeys(function ($dayKey) {
                return [$dayKey => 0];
            })->all();

            foreach ($items as $item) {
                if (!$item->created_at) {
                    continue;
                }

                $dayKey = Carbon::parse($item->created_at)->format('Y-m-d');
                if (array_key_exists($dayKey, $dayCounts)) {
                    $dayCounts[$dayKey] += 1;
                }
            }

            $dailyVolume = collect($dayCounts)
                ->map(function ($count, $dayKey) {
                    $date = Carbon::parse($dayKey);

                    return [
                        'date' => $dayKey,
                        'label' => $date->format('D'),
                        'count' => (int) $count,
                    ];
                })
                ->values()
                ->all();

            $daysWithData = max(1, count($dailyVolume));
            $analyzerPerformance = collect($categoryBucket)
                ->map(function ($entry) use ($daysWithData) {
                    $completionRate = $entry['total'] > 0 ? ($entry['completed'] / $entry['total']) : 0;
                    $uptime = max(72, min(99.9, $completionRate * 100));
                    $avgSamplesPerDay = max(1, (int) round($entry['total'] / $daysWithData));
                    $errorRate = $entry['total'] > 0 ? (($entry['critical'] / $entry['total']) * 100) : 0;
                    $avgTatMin = $entry['tat_count'] > 0 ? (int) round($entry['tat_minutes_total'] / $entry['tat_count']) : 0;
                    $isOk = $completionRate >= 0.7;

                    return [
                        'analyzer' => (string) $entry['category'] . ' Bench',
                        'uptime' => number_format($uptime, 1) . '%',
                        'samples_per_day' => $avgSamplesPerDay,
                        'error_rate' => number_format($errorRate, 1) . '%',
                        'calibration' => $avgTatMin <= 1440 ? 'Daily ✅' : 'Overdue ⚠️',
                        'status' => $isOk ? 'OK' : 'Maintenance',
                        'status_color' => $isOk ? 'green' : 'orange',
                    ];
                })
                ->sortByDesc(function ($entry) {
                    return (int) ($entry['samples_per_day'] ?? 0);
                })
                ->values()
                ->all();

            return response()->json([
                'success' => true,
                'data' => [
                    'tat_by_department' => $tatByDepartment,
                    'daily_volume' => $dailyVolume,
                    'analyzer_performance' => $analyzerPerformance,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load TAT analytics: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getAnalyzerConfig(Request $request)
    {
        try {
            $searchText = strtolower(trim((string) $request->input('search_text', '')));
            $category = trim((string) $request->input('category', ''));

            $items = DiagnosticOrderItem::query()
                ->where('department', 'pathology')
                ->when($category !== '', function ($query) use ($category) {
                    $query->where('category_name', $category);
                })
                ->when($request->filled('date_from'), function ($query) use ($request) {
                    $query->whereDate('created_at', '>=', Carbon::parse($request->date_from)->format('Y-m-d'));
                })
                ->when($request->filled('date_to'), function ($query) use ($request) {
                    $query->whereDate('created_at', '<=', Carbon::parse($request->date_to)->format('Y-m-d'));
                })
                ->when($searchText !== '', function ($query) use ($searchText) {
                    $query->where(function ($inner) use ($searchText) {
                        $inner->whereRaw('LOWER(test_name) LIKE ?', ['%' . $searchText . '%'])
                            ->orWhereRaw('LOWER(category_name) LIKE ?', ['%' . $searchText . '%']);
                    });
                })
                ->get(['id', 'test_name', 'category_name', 'status', 'created_at', 'updated_at']);

            $analyzerNameMap = [
                'haematology' => 'Sysmex XN-550 (Hematology)',
                'hematology' => 'Sysmex XN-550 (Hematology)',
                'biochemistry' => 'Roche Cobas c311 (Chemistry)',
                'microbiology' => 'BioMerieux VITEK2 (Microbiology)',
                'endocrinology' => 'Bio-Rad D-100 (HbA1c)',
            ];

            $grouped = $items->groupBy(function ($item) {
                return strtolower(trim((string) ($item->category_name ?: 'Uncategorized')));
            });

            $analyzers = $grouped->map(function ($groupItems, $categoryKey) use ($analyzerNameMap) {
                $categoryLabel = trim((string) ($groupItems->first()?->category_name ?: 'Uncategorized'));
                $name = $analyzerNameMap[$categoryKey] ?? ($categoryLabel . ' Analyzer');

                $tests = $groupItems
                    ->pluck('test_name')
                    ->filter()
                    ->unique()
                    ->take(4)
                    ->values()
                    ->implode(', ');

                $latestStamp = $groupItems
                    ->map(fn ($item) => $item->updated_at ?? $item->created_at)
                    ->filter()
                    ->sortDesc()
                    ->first();

                $lastCalib = 'Not available';
                if ($latestStamp) {
                    $calibAt = Carbon::parse($latestStamp);
                    if ($calibAt->isToday()) {
                        $lastCalib = 'Today ' . $calibAt->format('H:i');
                    } elseif ($calibAt->isYesterday()) {
                        $lastCalib = 'Yesterday';
                    } else {
                        $lastCalib = $calibAt->diffForHumans();
                    }
                }

                $samplesToday = $groupItems
                    ->filter(fn ($item) => optional($item->created_at)->isToday())
                    ->count();

                $completed = $groupItems
                    ->filter(function ($item) {
                        $status = strtolower(str_replace([' ', '-'], '_', (string) $item->status));
                        return $status === 'completed';
                    })
                    ->count();

                $completionRate = $groupItems->count() > 0 ? ($completed / $groupItems->count()) : 0;
                $isOnline = $samplesToday > 0 && $completionRate >= 0.5;

                return [
                    'name' => (string) $name,
                    'status' => $isOnline ? 'online' : 'maintenance',
                    'tests' => (string) ($tests !== '' ? $tests : 'No tests mapped'),
                    'last_calibration' => (string) $lastCalib,
                    'samples' => (string) ($samplesToday . ' today'),
                ];
            })->values()->all();

            return response()->json([
                'success' => true,
                'data' => [
                    'analyzers' => $analyzers,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load analyzer config: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getItemParameters(DiagnosticOrderItem $item)
    {
        try {
            $parameters = $item->parameters()
                ->select([
                    'id',
                    'parameter_name',
                    'result_value',
                    'unit_name',
                    'normal_range',
                    'result_flag',
                    'parameterable_type',
                    'parameterable_id'
                ])
                ->get()
                ->map(function ($param) {
                    $flagLabel = '';
                    $resultFlag = strtolower(str_replace([' ', '-'], '_', (string) $param->result_flag ?? ''));
                    
                    if (in_array($resultFlag, ['critical_low', 'critical_high'], true)) {
                        $flagLabel = $resultFlag === 'critical_low' ? 'Critical Low' : 'Critical High';
                    } elseif (in_array($resultFlag, ['low', 'high'], true)) {
                        $flagLabel = ucfirst($resultFlag);
                    } else {
                        $flagLabel = 'Normal';
                    }

                    return [
                        'id' => (int) $param->id,
                        'parameter_name' => (string) ($param->parameter_name ?? 'Unknown Parameter'),
                        'result_value' => (string) ($param->result_value ?? '-'),
                        'unit_name' => (string) ($param->unit_name ?? '-'),
                        'normal_range' => (string) ($param->normal_range ?? '-'),
                        'result_flag' => (string) ($param->result_flag ?? 'normal'),
                        'flag_label' => $flagLabel,
                    ];
                })
                ->all();

            $order = $item->order;
            $patient = $order?->patient;
            
            // Get hospital name from settings
            $hospitalName = \App\Models\BusinessSetting::where('key', 'hospital_name')->value('value') ?? 'District Hospital';
            
            // Get pathologist/reporter info
            $reportedByUser = $item->order?->orderedByUser;
            $pathologistName = $reportedByUser?->name ?? 'Lab Pathologist';
            $verifiedDate = optional($item->reported_at ?? $item->updated_at)->format('d-m-Y H:i') ?? date('d-m-Y H:i');

            return response()->json([
                'success' => true,
                'data' => [
                    'parameters' => $parameters,
                    'item_id' => (int) $item->id,
                    'test_name' => (string) ($item->test_name ?? '-'),
                    'sample_id' => (string) ($order?->order_no ?? 'SAMPLE-' . $item->diagnostic_order_id),
                    'ordered_at' => optional($order?->created_at ?? $item->created_at)?->format('d-m-Y H:i') ?? '-',
                    'patient_name' => (string) ($patient?->name ?? '-'),
                    'patient_mrn' => (string) ($patient?->mrn ?? $patient?->patient_id ?? '-'),
                    'age' => (string) ($patient?->age ?? '-'),
                    'sex' => (string) ($patient?->gender ?? '-'),
                    'report_summary' => (string) ($item->report_summary ?? ''),
                    'report_text' => (string) ($item->report_text ?? ''),
                    'hospital_name' => (string) $hospitalName,
                    'pathologist_name' => (string) $pathologistName,
                    'verified_date' => (string) $verifiedDate,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch parameters: ' . $e->getMessage()
            ], 500);
        }
    }
}
