<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\BedAllocation;
use App\Models\DiagnosticOrderItem;
use App\Models\OpdPatient;
use App\Models\HeaderFooter;
use App\Models\RadiologyCategory;
use App\Models\RadiologyPacsStudy;
use App\Models\RadiologyTest;
use App\Models\Staff;
use App\Models\User;
use App\Support\SafeReportHtml;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class RadiologyRisController extends BaseHospitalController
{
    public function index()
    {
        $modalities = DiagnosticOrderItem::query()
            ->where('department', 'radiology')
            ->whereNotNull('category_name')
            ->where('category_name', '!=', '')
            ->distinct()
            ->orderBy('category_name')
            ->pluck('category_name')
            ->values()
            ->all();

        return view('hospital.radiology.ris.index', [
            'modalities' => $modalities,
            'pacs_viewer_url_template' => (string) config('radiology.pacs_web_viewer_url_template', ''),
            'routes' => [
                'loadtable' => route('hospital.radiology.ris.worklist-load'),
                'showform' => route('hospital.radiology.worklist.showform', ['item' => '__ITEM__']),
                'save' => route('hospital.radiology.worklist.save', ['item' => '__ITEM__']),
                'print' => route('hospital.radiology.worklist.print', ['item' => '__ITEM__']),
                'status' => route('hospital.radiology.worklist.status', ['item' => '__ITEM__']),
                'risSummary' => route('hospital.radiology.ris.summary'),
                'risModalitiesBoard' => route('hospital.radiology.ris.modalities-board'),
                'risAnalytics' => route('hospital.radiology.ris.analytics'),
                'risPendingQueue' => route('hospital.radiology.ris.pending-queue'),
                'risProtocols' => route('hospital.radiology.ris.protocols'),
                'risSchedule' => route('hospital.radiology.ris.schedule'),
                'reportItem' => route('hospital.radiology.ris.report-item', ['item' => '__ITEM__']),
                'workflowAdvance' => route('hospital.radiology.ris.workflow-advance', ['item' => '__ITEM__']),
                'completedPdf' => route('hospital.radiology.ris.completed-pdf', ['item' => '__ITEM__']),
                'pacsResolve' => route('hospital.radiology.ris.pacs-resolve', ['item' => '__ITEM__']),
            ],
        ]);
    }

    public function summary(Request $request)
    {
        $date = $request->filled('date')
            ? Carbon::parse($request->input('date'))->startOfDay()
            : Carbon::today();

        $base = DiagnosticOrderItem::query()
            ->where('department', 'radiology')
            ->whereHas('order', fn ($q) => $q->where('hospital_id', $this->hospital_id))
            ->whereDate('created_at', $date->toDateString());

        $total = (clone $base)->count();
        $completed = (clone $base)->where('status', 'completed')->count();
        $ordered = (clone $base)->where('status', 'ordered')->count();
        $inProgress = (clone $base)->whereIn('status', ['in_progress', 'examination'])->count();
        $urgentOpen = (clone $base)
            ->whereRaw("UPPER(TRIM(IFNULL(priority, ''))) IN ('STAT','URGENT')")
            ->where('status', '!=', 'completed')
            ->count();
        $pendingReport = (clone $base)->whereIn('status', ['in_progress', 'examination'])->count();

        $completionPct = $total > 0 ? round(($completed / $total) * 100, 1) : 0.0;

        return response()->json([
            'date' => $date->toDateString(),
            'total_orders' => $total,
            'completed' => $completed,
            'ordered' => $ordered,
            'in_progress' => $inProgress,
            'urgent_stat_open' => $urgentOpen,
            'reports_pending' => $pendingReport,
            'completion_pct' => $completionPct,
            'ai_flagged' => 0,
        ]);
    }

    public function modalitiesBoard(Request $request)
    {
        $date = $request->filled('date')
            ? Carbon::parse($request->input('date'))->startOfDay()
            : Carbon::today();

        $modExpr = 'COALESCE(NULLIF(TRIM(category_name), ""), "Uncategorized")';

        $rows = DiagnosticOrderItem::query()
            ->select([
                DB::raw($modExpr . ' as modality'),
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as done"),
                DB::raw("SUM(CASE WHEN status != 'completed' AND status != 'cancelled' THEN 1 ELSE 0 END) as pending"),
            ])
            ->where('department', 'radiology')
            ->whereHas('order', fn ($q) => $q->where('hospital_id', $this->hospital_id))
            ->whereDate('created_at', $date->toDateString())
            ->groupBy(DB::raw($modExpr))
            ->orderBy(DB::raw($modExpr))
            ->get()
            ->keyBy(fn ($r) => (string) $r->modality);

        $names = $this->radiologyModalityNamesForBoard();
        if ($names === []) {
            $names = $rows->keys()->values()->all();
            if ($names === []) {
                $names = ['Uncategorized'];
            }
        }

        $palette = ['#1565c0', '#0d47a1', '#6a1b9a', '#00695c', '#e65100', '#880e4f', '#2e7d32', '#4527a0'];
        $icons = ['fa-x-ray', 'fa-atom', 'fa-magnet', 'fa-wave-square', 'fa-female', 'fa-brain', 'fa-lungs', 'fa-x-ray'];

        $items = collect($names)->values()->map(function ($name, $i) use ($rows, $palette, $icons) {
            $r = $rows->get($name);
            $total = $r ? (int) $r->total : 0;
            $pending = $r ? (int) $r->pending : 0;
            $done = $r ? (int) $r->done : 0;
            $util = $total > 0 ? (int) round(($done / $total) * 100) : 0;
            $color = $palette[$i % count($palette)];
            $icon = $icons[$i % count($icons)];

            return [
                'name' => (string) $name,
                'location' => 'Imaging — ' . (string) $name,
                'icon' => $icon,
                'color' => $color,
                'today' => $total,
                'pending' => $pending,
                'util' => min(100, max(0, $util)),
            ];
        });

        return response()->json(['data' => $items->all()]);
    }

    public function worklistLoad(Request $request)
    {
        $searchKeyword = $this->worklistSearchKeyword($request);
        $completedOnly = $request->boolean('completed_only');

        $query = DiagnosticOrderItem::query()
            ->with(['order.patient', 'order.visitable', 'order.orderedByUser', 'patientCharge', 'testable'])
            ->where('department', 'radiology')
            ->whereHas('order', fn ($q) => $q->where('hospital_id', $this->hospital_id))
            ->when($completedOnly, fn ($q) => $q->where('status', 'completed'))
            ->when(! $completedOnly, fn ($q) => $q->where('status', '!=', 'completed'))
            ->when($request->filled('status'), function ($q) use ($request) {
                $st = strtolower(trim((string) $request->status));
                if ($st === 'examination') {
                    $q->whereIn('status', ['examination', 'in_progress']);
                } else {
                    $q->where('status', $request->status);
                }
            })
            ->when($request->filled('category'), function ($q) use ($request) {
                $cat = trim((string) $request->category);
                if ($cat !== '') {
                    $q->where('category_name', $cat);
                }
            })
            ->when($request->filled('priority'), function ($q) use ($request) {
                $p = strtoupper(trim((string) $request->priority));
                if ($p === 'STAT') {
                    $q->whereRaw("UPPER(TRIM(IFNULL(priority, ''))) = 'STAT'");
                } elseif ($p === 'URGENT') {
                    $q->whereRaw("UPPER(TRIM(IFNULL(priority, ''))) = 'URGENT'");
                } elseif ($p === 'ROUTINE') {
                    $q->where(function ($inner) {
                        $inner->whereNull('priority')
                            ->orWhereRaw("UPPER(TRIM(priority)) NOT IN ('STAT','URGENT')");
                    });
                }
            })
            ->when($request->filled('date'), function ($q) use ($request) {
                $q->whereDate('created_at', Carbon::parse($request->date)->format('Y-m-d'));
            })
            ->when($request->filled('date_from'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', Carbon::parse($request->date_from)->format('Y-m-d'));
            })
            ->when($request->filled('date_to'), function ($q) use ($request) {
                $q->whereDate('created_at', '<=', Carbon::parse($request->date_to)->format('Y-m-d'));
            })
            ->when($searchKeyword !== '', function ($q) use ($searchKeyword) {
                $term = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $searchKeyword) . '%';
                $q->where(function ($inner) use ($term) {
                    $inner->where('diagnostic_order_items.test_name', 'like', $term)
                        ->orWhere('diagnostic_order_items.category_name', 'like', $term)
                        ->orWhereHas('order', function ($oq) use ($term) {
                            $oq->where('order_no', 'like', $term)
                                ->orWhereHas('patient', function ($pq) use ($term) {
                                    $pq->where('name', 'like', $term)
                                        ->orWhere('mrn', 'like', $term)
                                        ->orWhere('patient_id', 'like', $term);
                                });
                        });
                });
            })
            ->when($completedOnly, function ($q) {
                $q->reorder()->orderByDesc('reported_at')->orderByDesc('diagnostic_order_items.id');
            }, function ($q) {
                $q->orderBy('created_at', 'asc');
            });

        return DataTables::of($query)
            ->addColumn('accession', fn ($row) => e(optional($row->order)->order_no ?? ('RAD-' . $row->id)))
            ->addColumn('patient_name', fn ($row) => e(optional($row->order?->patient)->name ?? '-'))
            ->addColumn('patient_age_sex', function ($row) {
                $p = $row->order?->patient;
                $age = filled($p?->age) ? (string) $p->age : '-';
                $g = filled($p?->gender) ? (string) $p->gender : '-';

                return e(trim($age . '/' . $g));
            })
            ->addColumn('modality', function ($row) {
                $m = trim((string) ($row->category_name ?? ''));
                if ($m === '') {
                    $m = 'Uncategorized';
                }

                return '<span class="rad-ris-badge rad-ris-badge-purple">' . e($m) . '</span>';
            })
            ->addColumn('examination', fn ($row) => e((string) ($row->test_name ?? '-')))
            ->addColumn('ordered_by', fn ($row) => e(optional($row->order?->orderedByUser)->name ?? '-'))
            ->addColumn('ward_opd', function ($row) {
                return '<span class="rad-ris-badge rad-ris-badge-gray">' . e($this->resolveVisitLabel($row)) . '</span>';
            })
            ->addColumn('priority', function ($row) {
                return $this->formatPriorityBadge((string) ($row->priority ?? 'Routine'));
            })
            ->addColumn('status', fn ($row) => $this->formatRisStatusBadge((string) $row->status))
            ->addColumn('time_slot', function ($row) use ($request) {
                if ($request->boolean('completed_only') && $row->reported_at) {
                    return e($row->reported_at->format('d-m-Y H:i'));
                }

                return e(optional($row->created_at)?->format('H:i') ?? '-');
            })
            ->addColumn('workflow', function ($row) use ($request) {
                return view('hospital.radiology.ris.partials.worklist-workflow', [
                    'row' => $row,
                    'completedOnly' => $request->boolean('completed_only'),
                ])->render();
            })
            ->rawColumns(['modality', 'ward_opd', 'priority', 'status', 'workflow'])
            ->make(true);
    }

    public function reportItemJson(DiagnosticOrderItem $item)
    {
        abort_if($item->department !== 'radiology', 404);
        abort_if(optional($item->order)->hospital_id != $this->hospital_id, 403);

        $item->load(['order.patient', 'order.orderedByUser', 'order.visitable', 'testable', 'reportRadiologist', 'parameters.parameterable.unit']);

        $order = $item->order;
        $patient = $order?->patient;
        $testName = (string) ($item->test_name ?? '');

        $methodFromTest = ($item->testable instanceof RadiologyTest)
            ? trim((string) ($item->testable->method ?? ''))
            : '';
        $clinical = trim((string) ($item->clinical_indication ?? ''));
        if ($clinical === '') {
            $clinical = trim((string) ($order?->notes ?? ''));
        }

        $technique = trim((string) ($item->report_technique ?? ''));
        if ($technique === '') {
            $technique = trim((string) ($item->method ?? '')) ?: $methodFromTest;
        }

        $findings = trim((string) ($item->report_text ?? ''));
        if ($findings === '') {
            $sentence = 'Examination performed: ' . $testName . '. Key images reviewed. Clinical correlation is recommended where applicable.';
            $findings = '<p>' . htmlspecialchars($sentence, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '</p>';
        }

        $impression = trim((string) ($item->report_impression ?? ''));
        $summary = trim((string) ($item->report_summary ?? ''));

        $radiologists = Staff::query()
            ->where('hospital_id', $this->hospital_id)
            ->whereNotNull('user_id')
            ->doctor()
            ->active()
            ->with('user:id,name')
            ->orderBy('first_name')
            ->get()
            ->map(function (Staff $s) {
                $uid = $s->user_id;
                $name = trim(($s->first_name ?? '') . ' ' . ($s->last_name ?? ''));

                return [
                    'id' => (int) $uid,
                    'name' => $name !== '' ? $name : (optional($s->user)->name ?? 'Staff #' . $s->id),
                ];
            })
            ->filter(fn ($r) => $r['id'] > 0)
            ->values();

        if ($radiologists->isEmpty()) {
            $radiologists = User::query()
                ->where('hospital_id', $this->hospital_id)
                ->orderBy('name')
                ->limit(40)
                ->get(['id', 'name'])
                ->map(fn (User $u) => ['id' => (int) $u->id, 'name' => (string) $u->name])
                ->values();
        }

        $parameterRows = $item->parameters->sortBy('sort_order')->values()->map(function ($p) {
            $def = $p->parameterable;
            $minVal = $def->min_value ?? null;
            $maxVal = $def->max_value ?? null;
            $critLow = $def->critical_low ?? null;
            $critHigh = $def->critical_high ?? null;
            $unitName = $def?->unit?->name ?? ($p->unit_name ?? '');
            $rangeText = $p->normal_range ?? '';
            if ($minVal !== null && $maxVal !== null) {
                $rangeText = number_format((float) $minVal, 2) . ' - ' . number_format((float) $maxVal, 2);
            }

            return [
                'id' => (int) $p->id,
                'parameter_name' => (string) $p->parameter_name,
                'unit_name' => (string) ($unitName !== '' ? $unitName : '-'),
                'normal_range' => (string) ($rangeText !== '' ? $rangeText : '-'),
                'result_value' => (string) ($p->result_value ?? ''),
                'remarks' => (string) ($p->remarks ?? ''),
                'result_flag' => $p->result_flag,
                'min_value' => $minVal !== null ? (string) $minVal : '',
                'max_value' => $maxVal !== null ? (string) $maxVal : '',
                'critical_low' => $critLow !== null ? (string) $critLow : '',
                'critical_high' => $critHigh !== null ? (string) $critHigh : '',
            ];
        })->all();

        $norm = strtolower(str_replace([' ', '-'], '_', (string) $item->status));
        if ($norm === 'in_progress') {
            $norm = 'examination';
        }
        $statusLabel = match ($norm) {
            'examination' => 'Pending report',
            'ordered' => 'Ordered',
            'completed' => 'Completed',
            default => ucwords(str_replace('_', ' ', $norm)),
        };

        return response()->json([
            'id' => (int) $item->id,
            'patient' => (string) ($patient->name ?? '-'),
            'patient_age_sex' => trim((filled($patient?->age) ? (string) $patient->age : '-') . ' / ' . (filled($patient?->gender) ? (string) $patient->gender : '-')),
            'accession' => (string) ($order->order_no ?? ('RAD-' . $item->id)),
            'study' => $testName,
            'referred_by' => (string) (optional($order?->orderedByUser)->name ?? '-'),
            'status' => (string) $item->status,
            'status_norm' => $norm,
            'status_label' => $statusLabel,
            'clinical_indication' => SafeReportHtml::sanitize($clinical),
            'report_technique' => $technique,
            'report_text' => SafeReportHtml::sanitize($findings),
            'report_impression' => SafeReportHtml::sanitize($impression),
            'report_summary' => SafeReportHtml::sanitize($summary),
            'report_category' => (string) ($item->report_category ?? 'Normal'),
            'report_radiologist_id' => $item->report_radiologist_id ? (int) $item->report_radiologist_id : null,
            'report_is_draft' => (bool) ($item->report_is_draft ?? false),
            'radiologists' => $radiologists->all(),
            'parameters' => $parameterRows,
        ]);
    }

    public function advanceWorkflow(Request $request, DiagnosticOrderItem $item)
    {
        abort_if($item->department !== 'radiology', 404);
        abort_if(optional($item->order)->hospital_id != $this->hospital_id, 403);

        $current = strtolower(str_replace([' ', '-'], '_', (string) $item->status));
        if ($current === 'in_progress') {
            $current = 'examination';
        }

        if ($current !== 'ordered') {
            return response()->json(['status' => false, 'message' => 'Only ordered studies can move to examination.'], 422);
        }

        $item->status = 'examination';
        $item->save();

        return response()->json(['status' => true, 'message' => 'Study moved to examination.', 'new_status' => 'examination']);
    }

    public function completedPdf(DiagnosticOrderItem $item)
    {
        abort_if($item->department !== 'radiology', 404);
        abort_if(optional($item->order)->hospital_id != $this->hospital_id, 403);
        abort_unless(strtolower((string) $item->status) === 'completed', 404);

        $item->load(['order.patient', 'order.orderedByUser', 'order.visitable', 'reportRadiologist', 'patientCharge', 'parameters.parameterable.unit']);
        $printTemplate = HeaderFooter::query()
            ->where('type', 'radiology')
            ->first();

        $html = view('hospital.radiology.ris.pdf.report-summary', [
            'item' => $item,
            'hospital' => auth()->user()?->hospital,
            'printTemplate' => $printTemplate,
        ])->render();

        if (! class_exists(\Dompdf\Dompdf::class)) {
            return response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
        }

        try {
            $options = new \Dompdf\Options();
            $options->set('isRemoteEnabled', false);
            $options->set('defaultFont', 'DejaVu Sans');
            $chroot = realpath(public_path());
            if ($chroot) {
                $options->setChroot($chroot);
            }
            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $filename = 'Radiology-' . preg_replace('/[^A-Za-z0-9_-]+/', '_', (string) ($item->order?->order_no ?? $item->id)) . '.pdf';

            return response($dompdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } catch (\Throwable $e) {
            return response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
        }
    }

    public function analytics(Request $request)
    {
        $to = Carbon::today()->endOfDay();
        $from = Carbon::today()->subDays(6)->startOfDay();

        $modExpr = 'COALESCE(NULLIF(TRIM(category_name), ""), "Uncategorized")';

        $dayKeys = collect(range(0, 6))->map(fn ($i) => $from->copy()->addDays($i)->format('Y-m-d'))->all();

        $perDayMod = DiagnosticOrderItem::query()
            ->where('department', 'radiology')
            ->whereHas('order', fn ($q) => $q->where('hospital_id', $this->hospital_id))
            ->whereBetween('created_at', [$from, $to])
            ->select([
                DB::raw('DATE(created_at) as d'),
                DB::raw($modExpr . ' as modality'),
                DB::raw('COUNT(*) as c'),
            ])
            ->groupBy(DB::raw('DATE(created_at)'), DB::raw($modExpr))
            ->get();

        $modTotals = [];
        foreach ($perDayMod as $row) {
            $m = (string) $row->modality;
            $modTotals[$m] = ($modTotals[$m] ?? 0) + (int) $row->c;
        }
        arsort($modTotals);
        $orderedMods = array_keys($modTotals);
        $topMain = array_slice($orderedMods, 0, 7);
        if ($topMain === []) {
            $topMain = ['Uncategorized'];
        }
        $useOther = count($orderedMods) > 7;
        $topMods = $useOther ? array_merge($topMain, ['Other']) : $topMain;

        $counts = [];
        foreach ($dayKeys as $dk) {
            $counts[$dk] = array_fill_keys($topMods, 0);
        }
        foreach ($perDayMod as $row) {
            $d = (string) $row->d;
            $m = (string) $row->modality;
            $c = (int) $row->c;
            if (! isset($counts[$d])) {
                continue;
            }
            if ($useOther && ! in_array($m, $topMain, true)) {
                $counts[$d]['Other'] += $c;
            } elseif (in_array($m, $topMods, true)) {
                $counts[$d][$m] += $c;
            }
        }

        $chartLabels = [];
        foreach ($dayKeys as $dk) {
            $chartLabels[] = [
                'label' => Carbon::parse($dk)->format('D'),
                'date' => $dk,
            ];
        }

        $modalitySeries = [];
        foreach ($topMods as $mod) {
            $modalitySeries[] = [
                'label' => $mod,
                'data' => array_map(fn ($dk) => (int) ($counts[$dk][$mod] ?? 0), $dayKeys),
            ];
        }

        $dailyCounts = DiagnosticOrderItem::query()
            ->where('department', 'radiology')
            ->whereHas('order', fn ($q) => $q->where('hospital_id', $this->hospital_id))
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->groupBy('d')
            ->pluck('c', 'd');

        $dailyVolume = collect($dayKeys)->map(function ($d) use ($dailyCounts) {
            $date = Carbon::parse($d);

            return [
                'label' => $date->format('D'),
                'date' => $d,
                'count' => (int) ($dailyCounts[$d] ?? 0),
            ];
        })->values()->all();

        $byModality = collect($modTotals)->map(fn ($c, $label) => ['label' => (string) $label, 'count' => (int) $c])
            ->sortByDesc('count')
            ->values()
            ->all();

        $tatByModality = DiagnosticOrderItem::query()
            ->where('department', 'radiology')
            ->where('status', 'completed')
            ->whereNotNull('reported_at')
            ->whereHas('order', fn ($q) => $q->where('hospital_id', $this->hospital_id))
            ->whereBetween('created_at', [$from, $to])
            ->get(['category_name', 'created_at', 'reported_at'])
            ->groupBy(fn ($item) => trim((string) ($item->category_name ?? '')) ?: 'Uncategorized')
            ->map(function ($group, $label) {
                $hours = [];
                foreach ($group as $item) {
                    if ($item->created_at && $item->reported_at) {
                        $hours[] = max(0, $item->created_at->diffInMinutes($item->reported_at) / 60);
                    }
                }
                $avg = count($hours) > 0 ? round(array_sum($hours) / count($hours), 1) : 0.0;

                return ['label' => (string) $label, 'avg_hours' => $avg];
            })
            ->values()
            ->sortByDesc('avg_hours')
            ->values()
            ->all();

        $completedWeek = DiagnosticOrderItem::query()
            ->where('department', 'radiology')
            ->where('status', 'completed')
            ->whereNotNull('reported_at')
            ->whereHas('order', fn ($q) => $q->where('hospital_id', $this->hospital_id))
            ->whereBetween('reported_at', [$from, $to])
            ->get(['created_at', 'reported_at']);

        $tatWeekly = [];
        foreach ($dayKeys as $dk) {
            $hours = [];
            foreach ($completedWeek as $item) {
                if ($item->reported_at && $item->reported_at->format('Y-m-d') === $dk && $item->created_at) {
                    $hours[] = max(0, $item->created_at->diffInMinutes($item->reported_at) / 60);
                }
            }
            $tatWeekly[] = [
                'label' => Carbon::parse($dk)->format('D'),
                'date' => $dk,
                'avg_hours' => count($hours) > 0 ? round(array_sum($hours) / count($hours), 1) : 0.0,
                'count' => count($hours),
            ];
        }

        $sources = DiagnosticOrderItem::query()
            ->with('order')
            ->where('department', 'radiology')
            ->whereHas('order', fn ($q) => $q->where('hospital_id', $this->hospital_id))
            ->whereBetween('created_at', [$from, $to])
            ->get()
            ->groupBy(fn ($item) => $this->resolveVisitLabel($item))
            ->map(fn ($g, $k) => ['label' => (string) $k, 'count' => $g->count()])
            ->sortByDesc('count')
            ->values()
            ->all();

        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $monthlyRows = DiagnosticOrderItem::query()
            ->select([
                DB::raw($modExpr . ' as modality'),
                DB::raw('COUNT(*) as orders'),
                DB::raw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed"),
                DB::raw("SUM(CASE WHEN status != 'completed' AND status != 'cancelled' THEN 1 ELSE 0 END) as pending"),
            ])
            ->where('department', 'radiology')
            ->whereHas('order', fn ($q) => $q->where('hospital_id', $this->hospital_id))
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->groupBy(DB::raw($modExpr))
            ->orderBy(DB::raw($modExpr))
            ->get();

        $tatMonth = DiagnosticOrderItem::query()
            ->where('department', 'radiology')
            ->where('status', 'completed')
            ->whereNotNull('reported_at')
            ->whereHas('order', fn ($q) => $q->where('hospital_id', $this->hospital_id))
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->get(['category_name', 'created_at', 'reported_at'])
            ->groupBy(fn ($item) => trim((string) ($item->category_name ?? '')) ?: 'Uncategorized');

        $monthly = $monthlyRows->map(function ($r) use ($tatMonth) {
            $label = (string) $r->modality;
            $g = $tatMonth->get($label, collect());
            $hours = [];
            foreach ($g as $item) {
                if ($item->created_at && $item->reported_at) {
                    $hours[] = max(0, $item->created_at->diffInMinutes($item->reported_at) / 60);
                }
            }
            $avgTat = count($hours) > 0 ? round(array_sum($hours) / count($hours), 1) : null;

            return [
                'modality' => $label,
                'orders' => (int) $r->orders,
                'completed' => (int) $r->completed,
                'pending' => (int) $r->pending,
                'tat' => $avgTat !== null ? (string) $avgTat . ' h' : '—',
                'revenue' => '—',
            ];
        })->values()->all();

        return response()->json([
            'range' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
            ],
            'daily_volume' => $dailyVolume,
            'by_modality' => $byModality,
            'tat_by_modality' => $tatByModality,
            'tat_weekly' => $tatWeekly,
            'chart_labels' => $chartLabels,
            'modality_series' => $modalitySeries,
            'sources' => $sources,
            'monthly_summary' => $monthly,
            'monthly_period' => $monthStart->format('M Y'),
        ]);
    }

    public function pendingQueue(Request $request)
    {
        $limit = min(50, max(5, (int) $request->input('limit', 20)));

        $items = DiagnosticOrderItem::query()
            ->with(['order.patient'])
            ->where('department', 'radiology')
            ->whereIn('status', ['examination', 'in_progress'])
            ->whereHas('order', fn ($q) => $q->where('hospital_id', $this->hospital_id))
            ->orderByRaw("FIELD(UPPER(TRIM(IFNULL(priority,''))), 'STAT', 'URGENT') DESC")
            ->orderBy('created_at', 'asc')
            ->limit($limit)
            ->get();

        $rows = $items->map(function ($item) {
            $wait = $item->created_at ? $item->created_at->diffForHumans(null, true) : '-';
            $order = $item->order;
            $patient = $order?->patient;

            return [
                'patient' => (string) (optional($patient)->name ?? '-'),
                'study' => (string) ($item->test_name ?? '-'),
                'priority' => (string) ($item->priority ?? 'Routine'),
                'age' => $wait,
                'item_id' => (int) $item->id,
                'order_no' => (string) ($order->order_no ?? ''),
                'patient_id' => (string) (optional($patient)->patient_id ?? optional($patient)->mrn ?? ''),
            ];
        });

        return response()->json(['data' => $rows]);
    }

    public function protocols(Request $request)
    {
        $q = trim((string) $request->input('q', ''));

        $tests = RadiologyTest::query()
            ->with('category:id,name')
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $q) . '%';
                $query->where(function ($inner) use ($like) {
                    $inner->where('test_name', 'like', $like)
                        ->orWhere('test_code', 'like', $like);
                });
            })
            ->orderBy('test_name')
            ->limit(60)
            ->get()
            ->map(function (RadiologyTest $t) {
                $cat = optional($t->category)->name ?? 'General';

                return [
                    'name' => (string) $t->test_name,
                    'modality' => (string) $cat,
                    'code' => (string) ($t->test_code ?? ''),
                    'desc' => trim((string) ($t->method ?? '') . (filled($t->expected_report_days) ? ' · Report in ' . $t->expected_report_days . 'd' : '')),
                ];
            })
            ->values();

        return response()->json(['data' => $tests]);
    }

    public function schedule(Request $request)
    {
        $start = $request->filled('week_start')
            ? Carbon::parse($request->week_start)->startOfWeek(Carbon::MONDAY)
            : Carbon::now()->startOfWeek(Carbon::MONDAY);
        $end = $start->copy()->endOfWeek(Carbon::SUNDAY);

        $days = collect(range(0, 6))->map(function ($i) use ($start) {
            $d = $start->copy()->addDays($i);

            return [
                'key' => $d->format('Y-m-d'),
                'label' => $d->format('D') . ' ' . $d->format('M j'),
            ];
        });

        $slots = ['08:00', '09:00', '10:00', '11:00', '12:00'];

        $items = DiagnosticOrderItem::query()
            ->with(['order.patient'])
            ->where('department', 'radiology')
            ->whereHas('order', fn ($q) => $q->where('hospital_id', $this->hospital_id))
            ->whereBetween('created_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
            ->orderBy('created_at')
            ->limit(200)
            ->get();

        $cells = [];
        foreach ($items as $item) {
            if (!$item->created_at) {
                continue;
            }
            $dayKey = $item->created_at->format('Y-m-d');
            $hour = (int) $item->created_at->format('G');
            $slotIdx = match (true) {
                $hour < 9 => 0,
                $hour < 10 => 1,
                $hour < 11 => 2,
                $hour < 12 => 3,
                default => 4,
            };
            $p = mb_substr((string) (optional($item->order?->patient)->name ?? '?'), 0, 14);
            $study = mb_substr((string) ($item->test_name ?? 'Study'), 0, 22);
            $pri = strtoupper(trim((string) ($item->priority ?? '')));
            $cls = $pri === 'STAT' ? 'urgent' : ($pri === 'URGENT' ? 'urgent' : '');
            $cells[$dayKey][$slotIdx][] = [
                'text' => $study . ' — ' . $p,
                'class' => $cls,
            ];
        }

        return response()->json([
            'week_label' => $start->format('M j') . '–' . $end->format('M j, Y'),
            'days' => $days,
            'slots' => $slots,
            'cells' => $cells,
        ]);
    }

    /**
     * Resolve PACS viewer URL from indexed PACS studies first, then fallback template.
     */
    public function pacsResolve(DiagnosticOrderItem $item): JsonResponse
    {
        abort_if($item->department !== 'radiology', 404);
        abort_if(optional($item->order)->hospital_id != $this->hospital_id, 403);

        $item->loadMissing('order.patient');
        $orderNo = (string) ($item->order?->order_no ?? '');
        $patientId = (string) (optional($item->order?->patient)->patient_id ?? optional($item->order?->patient)->mrn ?? '');

        $study = RadiologyPacsStudy::query()
            ->where('hospital_id', $this->hospital_id)
            ->where(function ($q) use ($item, $orderNo) {
                $q->where('diagnostic_order_item_id', $item->id);
                if ($orderNo !== '') {
                    $q->orWhere('accession_no', $orderNo);
                }
            })
            ->latest('received_at')
            ->latest('id')
            ->first();

        $template = trim((string) config('radiology.pacs_web_viewer_url_template', ''));
        $url = '';

        if ($study && filled($study->viewer_url)) {
            $url = (string) $study->viewer_url;
        } elseif ($template !== '') {
            $studyUid = (string) ($study?->study_instance_uid ?? '');
            $url = str_replace(
                ['{accession}', '{order_no}', '{patient_id}', '{study_uid}'],
                [rawurlencode($orderNo), rawurlencode($orderNo), rawurlencode($patientId), rawurlencode($studyUid)],
                $template
            );
        }

        return response()->json([
            'status' => $url !== '',
            'url' => $url,
            'source' => $study?->source ?? ($template !== '' ? 'template' : null),
            'study_uid' => $study?->study_instance_uid,
            'message' => $url !== '' ? null : 'No PACS study mapped yet for this order.',
        ]);
    }

    /**
     * REST endpoint for PACS/modality bridge to ingest study metadata after C-STORE.
     */
    public function pacsIngest(Request $request): JsonResponse
    {
        if (! (bool) config('radiology.pacs_ingest_enabled', false)) {
            return response()->json(['status' => false, 'message' => 'PACS ingest is disabled.'], 403);
        }
        if (! $this->hasValidPacsSecret($request)) {
            return response()->json(['status' => false, 'message' => 'Unauthorized PACS ingest request.'], 401);
        }

        $data = $request->validate([
            'hospital_id' => 'required|integer|min:1',
            'accession_no' => 'required|string|max:120',
            'study_instance_uid' => 'required|string|max:128',
            'patient_identifier' => 'nullable|string|max:120',
            'modality' => 'nullable|string|max:32',
            'viewer_url' => 'nullable|string|max:2000',
            'source' => 'nullable|string|max:40',
            'status' => 'nullable|string|max:32',
            'payload' => 'nullable|array',
        ]);

        $item = DiagnosticOrderItem::query()
            ->where('department', 'radiology')
            ->whereHas('order', function ($q) use ($data) {
                $q->where('hospital_id', (int) $data['hospital_id'])
                    ->where('order_no', (string) $data['accession_no']);
            })
            ->latest('id')
            ->first();

        $study = RadiologyPacsStudy::query()->updateOrCreate(
            ['study_instance_uid' => (string) $data['study_instance_uid']],
            [
                'hospital_id' => (int) $data['hospital_id'],
                'diagnostic_order_item_id' => $item?->id,
                'accession_no' => (string) $data['accession_no'],
                'patient_identifier' => isset($data['patient_identifier']) ? (string) $data['patient_identifier'] : null,
                'modality' => isset($data['modality']) ? strtoupper((string) $data['modality']) : null,
                'status' => isset($data['status']) ? strtolower((string) $data['status']) : 'received',
                'source' => isset($data['source']) ? strtolower((string) $data['source']) : 'modality',
                'viewer_url' => isset($data['viewer_url']) ? (string) $data['viewer_url'] : null,
                'payload' => $data['payload'] ?? null,
                'received_at' => now(),
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'PACS study ingested.',
            'study_id' => $study->id,
            'item_id' => $item?->id,
        ]);
    }

    /**
     * Worklist feed for MWL bridge services (JSON feed to convert into DICOM MWL).
     */
    public function pacsWorklistFeed(Request $request): JsonResponse
    {
        if (! (bool) config('radiology.pacs_ingest_enabled', false)) {
            return response()->json(['status' => false, 'message' => 'PACS worklist feed is disabled.'], 403);
        }
        if (! $this->hasValidPacsSecret($request)) {
            return response()->json(['status' => false, 'message' => 'Unauthorized PACS worklist request.'], 401);
        }

        $hospitalId = (int) $request->input('hospital_id', 0);
        abort_unless($hospitalId > 0, 422, 'hospital_id is required.');

        $limit = min(300, max(10, (int) $request->input('limit', 120)));
        $items = DiagnosticOrderItem::query()
            ->with(['order.patient'])
            ->where('department', 'radiology')
            ->whereIn('status', ['ordered', 'in_progress', 'examination'])
            ->whereHas('order', fn ($q) => $q->where('hospital_id', $hospitalId))
            ->orderBy('created_at')
            ->limit($limit)
            ->get();

        $rows = $items->map(function (DiagnosticOrderItem $item) {
            $patient = $item->order?->patient;
            return [
                'item_id' => (int) $item->id,
                'accession_no' => (string) ($item->order?->order_no ?? ''),
                'modality' => (string) ($item->category_name ?? ''),
                'study_description' => (string) ($item->test_name ?? ''),
                'priority' => strtoupper(trim((string) ($item->priority ?? 'ROUTINE'))),
                'scheduled_at' => optional($item->created_at)?->toIso8601String(),
                'patient' => [
                    'id' => (string) ($patient?->patient_id ?? $patient?->mrn ?? ''),
                    'name' => (string) ($patient?->name ?? ''),
                    'gender' => strtoupper(substr((string) ($patient?->gender ?? 'U'), 0, 1)),
                    'age' => filled($patient?->age) ? (string) $patient->age : null,
                    'dob' => null,
                ],
            ];
        })->values();

        return response()->json([
            'status' => true,
            'count' => $rows->count(),
            'data' => $rows,
        ]);
    }

    protected function hasValidPacsSecret(Request $request): bool
    {
        $expected = trim((string) config('radiology.pacs_shared_secret', ''));
        if ($expected === '') {
            return false;
        }
        $provided = (string) $request->header('X-PACS-SECRET', $request->input('secret', ''));
        return hash_equals($expected, trim($provided));
    }

    protected function resolveVisitLabel(DiagnosticOrderItem $item): string
    {
        $order = $item->order;
        $v = $order?->visitable;
        $type = (string) ($order?->visitable_type ?? '');

        if ($v === null) {
            return 'Direct';
        }

        if ($type === OpdPatient::class) {
            return 'OPD';
        }

        if ($type === BedAllocation::class) {
            return 'IPD';
        }

        return 'Clinical';
    }

    protected function formatRisStatusBadge(string $status): string
    {
        $normalized = strtolower(str_replace([' ', '-'], '_', trim($status)));

        $map = [
            'ordered' => ['label' => 'Ordered', 'class' => 'rad-ris-badge-gray'],
            'sample_collected' => ['label' => 'Sample Collected', 'class' => 'rad-ris-badge-orange'],
            'in_progress' => ['label' => 'Examination', 'class' => 'rad-ris-badge-blue'],
            'examination' => ['label' => 'Examination', 'class' => 'rad-ris-badge-blue'],
            'completed' => ['label' => 'Completed', 'class' => 'rad-ris-badge-green'],
            'cancelled' => ['label' => 'Cancelled', 'class' => 'rad-ris-badge-red'],
        ];

        $badge = $map[$normalized] ?? [
            'label' => ucwords(str_replace('_', ' ', $normalized ?: 'unknown')),
            'class' => 'rad-ris-badge-gray',
        ];

        return '<span class="rad-ris-badge ' . $badge['class'] . '">' . e($badge['label']) . '</span>';
    }

    protected function formatPriorityBadge(string $priority): string
    {
        $p = strtoupper(trim($priority));
        if ($p === 'STAT') {
            return '<span class="rad-ris-badge rad-ris-badge-red"><span class="rad-ris-prio-dot rad-ris-prio-stat"></span>' . e('STAT') . '</span>';
        }
        if ($p === 'URGENT') {
            return '<span class="rad-ris-badge rad-ris-badge-orange"><span class="rad-ris-prio-dot rad-ris-prio-urg"></span>' . e('Urgent') . '</span>';
        }

        return '<span class="rad-ris-badge rad-ris-badge-blue"><span class="rad-ris-prio-dot rad-ris-prio-rou"></span>' . e('Routine') . '</span>';
    }

    protected function worklistSearchKeyword(Request $request): string
    {
        $search = $request->input('search');

        if (is_array($search)) {
            return trim((string) ($search['value'] ?? ''));
        }

        return trim((string) ($search ?? ''));
    }

    /**
     * @return list<string>
     */
    protected function radiologyModalityNamesForBoard(): array
    {
        $fromCategories = RadiologyCategory::query()
            ->orderBy('name')
            ->pluck('name')
            ->map(fn ($n) => trim((string) $n))
            ->filter()
            ->unique()
            ->values();

        $fromOrders = DiagnosticOrderItem::query()
            ->where('department', 'radiology')
            ->whereHas('order', fn ($q) => $q->where('hospital_id', $this->hospital_id))
            ->whereNotNull('category_name')
            ->where('category_name', '!=', '')
            ->distinct()
            ->orderBy('category_name')
            ->pluck('category_name')
            ->map(fn ($n) => trim((string) $n))
            ->filter()
            ->unique()
            ->values();

        return $fromCategories->merge($fromOrders)->unique()->sort()->values()->all();
    }
}
