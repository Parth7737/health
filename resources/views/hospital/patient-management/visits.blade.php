@extends('layouts.hospital.app', ['is_header_hiden' => true])
@section('title','Patient Details | Paracare+')
@section('page_header_icon', '')
@section('page_subtitle', 'Manage Patient Profile')

@section('content')
<div class="container-fluid px-0">
    <div class="opd-content-wrap">
        @php
            $selectedVisitId = (int) request('visit_id');
            $latestVisit = isset($visits)
                ? ($visits->firstWhere('id', $selectedVisitId) ?: $visits->first())
                : null;
            $visitMode = $visitContext ?? (data_get($latestVisit, 'is_ipd') ? 'ipd' : 'opd');
            $patientName = $patient->name ?? '-';
            $initials = collect(preg_split('/\s+/', trim((string) $patientName)))
                ->filter()
                ->take(2)
                ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
                ->implode('');
            $initials = $initials ?: 'NA';
            $ageText = trim(((string) ($patient->age_years ?? '-')) . ' Years');
            if (!empty($patient->age_months)) {
                $ageText .= ', ' . $patient->age_months . ' Months';
            }
            $abhaNumber = data_get($patient, 'abha_number') ?? data_get($patient, 'abha_no') ?? data_get($patient, 'abha_id') ?? '-';
            $bloodGroup = data_get($patient, 'blood_group') ?? '-';
            $visitedOn = '-';
            if (!empty($latestVisit?->appointment_date)) {
                $visitedOn = \Carbon\Carbon::parse($latestVisit->appointment_date)->format('d M Y');
            }
            $bloodPressure = ($latestVisit?->systolic_bp && $latestVisit?->diastolic_bp)
                ? $latestVisit->systolic_bp . '/' . $latestVisit->diastolic_bp
                : '-';
            $pulseValue = $latestVisit?->pluse ?? '-';
            $spo2Value = data_get($latestVisit, 'spo2')
                ?? data_get($latestVisit, 'spo2_percentage')
                ?? data_get($latestVisit, 'oxygen_saturation')
                ?? '-';
            $temperatureValue = $latestVisit?->temperature ? $latestVisit->temperature . 'F' : '-';
            $rbsValue = $latestVisit?->diabetes ?? '-';
            $weightValue = $latestVisit?->weight ? $latestVisit->weight . ' kg' : '-';
            $bmiValue = $latestVisit?->bmi ?? '-';
        @endphp
        <!-- Patient Header -->
        <div
            style="background:linear-gradient(135deg,#071221,#0a1628);padding:16px 24px;border-bottom:1px solid rgba(255,255,255,0.08)">
            <div class="flex items-center gap-4 flex-wrap">
                <div
                    style="width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#1565c0,#00695c);color:white;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:800;flex-shrink:0;border:2px solid rgba(255,255,255,.2)">
                    {{ $initials }}</div>
                <div class="flex-1">
                    <div style="font-size:18px;font-weight:800;color:#e8f2fb;letter-spacing:-.02em">{{ $patientName }}</div>
                    <div style="font-size:11.5px;color:#6a8fa8;margin-top:2px">MRN: {{ $patient->patient_id ?? '-' }} &nbsp;&nbsp; ABHA:
                        {{ $abhaNumber }} &nbsp;&nbsp; {{ $ageText }}, {{ $patient->gender ?? '-' }} &nbsp;&nbsp; Blood Group:
                        {{ $bloodGroup }} &nbsp;&nbsp; Visited: {{ $visitedOn }}</div>
                </div>
                <div class="flex gap-2 flex-wrap">
                    <span class="badge badge-danger" style="font-size:11px">{{ $latestVisit?->case_no ? (($visitMode === 'ipd' ? 'Admission ' : 'Case ') . $latestVisit->case_no) : 'Patient Record' }}</span>
                    @if($latestVisit?->id && $visitMode === 'opd')
                        <a href="{{ route('hospital.opd-patient.visit-summary.print', ['opdPatient' => $latestVisit->id]) }}" target="_blank" class="btn btn-ghost btn-sm" style="color:#d0e8fb;border-color:rgba(255,255,255,.15)">Print</a>
                    @else
                        @if(!empty($activeIpdAllocation?->id))
                            <a href="{{ route('hospital.ipd-patient.final-bill.print', ['allocation' => $activeIpdAllocation->id]) }}" target="_blank" class="btn btn-ghost btn-sm" style="color:#d0e8fb;border-color:rgba(255,255,255,.15)">Print</a>
                        @else
                            <button type="button" class="btn btn-ghost btn-sm" style="color:#d0e8fb;border-color:rgba(255,255,255,.15)" disabled>Print</button>
                        @endif
                    @endif
                    <button class="btn btn-primary btn-sm">+ New Order</button>
                </div>
            </div>

            <!-- Quick vitals strip -->
            <div class="flex gap-4 mt-3 flex-wrap" style="border-top:1px solid rgba(255,255,255,.07);padding-top:12px">
                <div style="text-align:center">
                    <div style="font-size:10px;color:#4a6880;text-transform:uppercase;letter-spacing:.05em">BP</div>
                    <div style="font-size:15px;font-weight:700;color:#ef5350">{{ $bloodPressure }}</div>
                </div>
                <div style="width:1px;background:rgba(255,255,255,.07)"></div>
                <div style="text-align:center">
                    <div style="font-size:10px;color:#4a6880;text-transform:uppercase">Pulse</div>
                    <div style="font-size:15px;font-weight:700;color:#e8f2fb">{{ $pulseValue }}</div>
                </div>
                <div style="width:1px;background:rgba(255,255,255,.07)"></div>
                <div style="text-align:center">
                    <div style="font-size:10px;color:#4a6880;text-transform:uppercase">SpO2</div>
                    <div style="font-size:15px;font-weight:700;color:#66bb6a">{{ $spo2Value }}</div>
                </div>
                <div style="width:1px;background:rgba(255,255,255,.07)"></div>
                <div style="text-align:center">
                    <div style="font-size:10px;color:#4a6880;text-transform:uppercase">Temp</div>
                    <div style="font-size:15px;font-weight:700;color:#fb8c00">{{ $temperatureValue }}</div>
                </div>
                <div style="width:1px;background:rgba(255,255,255,.07)"></div>
                <div style="text-align:center">
                    <div style="font-size:10px;color:#4a6880;text-transform:uppercase">RBS</div>
                    <div style="font-size:15px;font-weight:700;color:#ef5350">{{ $rbsValue }}</div>
                </div>
                <div style="width:1px;background:rgba(255,255,255,.07)"></div>
                <div style="text-align:center">
                    <div style="font-size:10px;color:#4a6880;text-transform:uppercase">Weight</div>
                    <div style="font-size:15px;font-weight:700;color:#e8f2fb">{{ $weightValue }}</div>
                </div>
                <div style="width:1px;background:rgba(255,255,255,.07)"></div>
                <div style="text-align:center">
                    <div style="font-size:10px;color:#4a6880;text-transform:uppercase">BMI</div>
                    <div style="font-size:15px;font-weight:700;color:#fb8c00">{{ $bmiValue }}</div>
                </div>
            </div>
        </div>

        <!-- Tab navigation -->
        <div style="background:#fff;border-bottom:2px solid #e2ecf4;padding:0 24px">
            <div class="tab-bar" style="border:none;margin:0">
                <button class="tab-btn active" data-tab="tabVisits" onclick="switchEMRTab('tabVisits',this)">
                    Visits</button>
                <button class="tab-btn" data-tab="tabTimeline" onclick="switchEMRTab('tabTimeline',this)">
                    Timeline</button>
                <button class="tab-btn" data-tab="tabOrders" onclick="switchEMRTab('tabOrders',this)"> Orders</button>
                <button class="tab-btn" data-tab="tabMeds" onclick="switchEMRTab('tabMeds',this)"> Medications</button>
                <button class="tab-btn" data-tab="tabNotes" onclick="switchEMRTab('tabNotes',this)"> Clinical
                    Notes</button>
                <button class="tab-btn" data-tab="tabLab" onclick="switchEMRTab('tabLab',this)"> Lab Results</button>
                <button class="tab-btn" data-tab="tabVitals" onclick="switchEMRTab('tabVitals',this)"> Vitals
                    Chart</button>
                <button class="tab-btn" data-tab="tabHistory" onclick="switchEMRTab('tabHistory',this)">
                    History</button>
                <button class="tab-btn" data-tab="tabBilling" onclick="switchEMRTab('tabBilling',this)">
                    Billing</button>
            </div>
        </div>

        <div class="content-area">

            <!-- VISITS -->
            <div class="tab-pane active" id="tabVisits">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title"> Visits List</div>
                    </div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date / Time</th>
                                    <th>Case No</th>
                                    <th>Consultant</th>
                                    <th>Reference</th>
                                    <th>Symptoms</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($visits ?? [] as $visit)
                                    @php
                                        $isSelectedVisit = (int) $visit->id === (int) ($latestVisit->id ?? 0);
                                        $visitSlot = data_get($visit, 'slot')
                                            ?? data_get($visit, 'slot_name')
                                            ?? data_get($visit, 'time_slot');
                                        if (empty($visitSlot) && !empty($visit->appointment_date)) {
                                            $visitSlot = \Carbon\Carbon::parse($visit->appointment_date)->format('h:i A');
                                        }
                                    @endphp
                                    <tr @if($isSelectedVisit) style="background:#eef6ff;border-left:3px solid #1565c0" @endif>
                                        <td>{{ $visit->appointment_date ? \Carbon\Carbon::parse($visit->appointment_date)->format('d M Y') : '-' }} {{ $visitSlot ?: '' }}</td>
                                        <td>
                                            @if($visit->case_no)
                                                <a href="{{ request()->fullUrlWithQuery(['visit_id' => $visit->id]) }}" style="color:#1565c0;font-weight:600">{{ $visit->case_no }}</a>
                                                
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $visit->consultant?->full_name ?? '-' }}</td>
                                        <td>{{ $visit->reference ?? '-' }}</td>
                                        <td>{{ $visit->symptoms_name ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No visits found for this patient.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TIMELINE -->
            <div class="tab-pane" id="tabTimeline">
                <div class="grid-21">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title"> Clinical Timeline</div>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                @forelse($timelineEntries ?? [] as $timelineEntry)
                                    @php
                                        $eventKey = strtolower((string) ($timelineEntry->event_key ?? ''));
                                        $logAt = ($timelineEntry->logged_at ?? $timelineEntry->created_at)
                                            ? \Carbon\Carbon::parse($timelineEntry->logged_at ?? $timelineEntry->created_at)
                                            : null;
                                        $dotColor = 'gray';

                                        if (str_contains($eventKey, 'delete') || str_contains($eventKey, 'refund') || str_contains($eventKey, 'discount') || str_contains($eventKey, 'payment')) {
                                            $dotColor = 'orange';
                                        } elseif (str_contains($eventKey, 'diagnosis') || str_contains($eventKey, 'status')) {
                                            $dotColor = 'red';
                                        } elseif (str_contains($eventKey, 'prescription') || str_contains($eventKey, 'medicine') || str_contains($eventKey, 'vitals')) {
                                            $dotColor = 'green';
                                        } elseif (str_contains($eventKey, 'lab') || str_contains($eventKey, 'pathology') || str_contains($eventKey, 'radiology') || str_contains($eventKey, 'visit') || str_contains($eventKey, 'opd')) {
                                            $dotColor = 'blue';
                                        }

                                        if ($logAt?->isToday()) {
                                            $timeLabel = 'Today, ' . $logAt->format('h:i A');
                                        } elseif ($logAt?->isYesterday()) {
                                            $timeLabel = 'Yesterday, ' . $logAt->format('h:i A');
                                        } else {
                                            $timeLabel = $logAt?->format('d M Y, h:i A') ?? '-';
                                        }
                                    @endphp
                                    <div class="tl-item">
                                        <div class="tl-dot {{ $dotColor }}"></div>
                                        <div class="tl-time">{{ $timeLabel }}</div>
                                        <div class="tl-title">{{ $timelineEntry->title ?? 'Timeline Event' }}</div>
                                        @if($timelineEntry->description)
                                            <div class="tl-detail">{{ $timelineEntry->description }}</div>
                                        @endif
                                        @if($timelineEntry->encounter_type)
                                            <div class="mt-2">
                                                <span class="badge badge-primary">{{ strtoupper($timelineEntry->encounter_type) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <div class="tl-item">
                                        <div class="tl-dot gray"></div>
                                        <div class="tl-time">No activity</div>
                                        <div class="tl-title">No timeline activity found for this patient.</div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-4">
                        <!-- Problem List -->
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title"> Active Problem List</div>
                            </div>
                            <div class="card-body-sm">
                                <div class="list-item">
                                    <div class="list-item-icon" style="background:#ffebee"></div>
                                    <div class="list-item-body">
                                        <div class="li-title">Type 2 Diabetes Mellitus</div>
                                        <div class="li-sub">ICD-10: E11.9 Uncontrolled Since 2018</div>
                                    </div><span class="badge badge-danger">Active</span>
                                </div>
                                <div class="list-item">
                                    <div class="list-item-icon" style="background:#fff3e0"></div>
                                    <div class="list-item-body">
                                        <div class="li-title">Essential Hypertension</div>
                                        <div class="li-sub">ICD-10: I10 Poorly controlled Since 2020</div>
                                    </div><span class="badge badge-warning">Active</span>
                                </div>
                                <div class="list-item">
                                    <div class="list-item-icon" style="background:#e3f2fd"></div>
                                    <div class="list-item-body">
                                        <div class="li-title">Dyslipidaemia</div>
                                        <div class="li-sub">ICD-10: E78.5 On statin LDL 142</div>
                                    </div><span class="badge badge-primary">Active</span>
                                </div>
                            </div>
                        </div>
                        <!-- AI -->
                        <div class="ai-insight-block">
                            <div class="ai-header"> AI Clinical Copilot</div>
                            <div class="ai-body">
                                <div class="ai-item">
                                    <div class="ai-dot" style="background:#e65100"></div>
                                    <div>HbA1c trend worsening (7.98.4%). Consider dual therapy add Sitagliptin or refer
                                        for endocrinology review.</div>
                                </div>
                                <div class="ai-item">
                                    <div class="ai-dot"></div>
                                    <div>BP target for DM+CKD is &lt;130/80 per ACC/AHA. Current 148/94 is above target
                                        ARB preferred.</div>
                                </div>
                                <div class="ai-item">
                                    <div class="ai-dot" style="background:#2e7d32"></div>
                                    <div>Kidney function stable (Cr 1.1). Continue Metformin with monitoring. eGFR
                                        estimation recommended.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ORDERS -->
            <div class="tab-pane" id="tabOrders">
                @php
                    $orderRows = collect();

                    foreach (($pathologyVisits ?? collect()) as $pathologyVisit) {
                        $linkedVisit = ($visitsById ?? collect())->get($pathologyVisit->order->visitable_id ?? null);
                        $statusKey = strtolower(str_replace([' ', '-'], '_', (string) ($pathologyVisit->status ?? 'pending')));
                        $statusLabel = ucfirst(str_replace('_', ' ', $statusKey));
                        $statusClass = $statusKey === 'completed'
                            ? 'badge-success'
                            : ($statusKey === 'in_progress' ? 'badge-warning' : (in_array($statusKey, ['cancelled', 'rejected'], true) ? 'badge-danger' : 'badge-secondary'));

                        $orderRows->push([
                            'type' => 'Pathology',
                            'type_badge' => 'badge-primary',
                            'description' => $pathologyVisit->test_name ?? '-',
                            'ordered_by' => $linkedVisit?->consultant?->full_name ?? '-',
                            'date' => $pathologyVisit->created_at,
                            'status_label' => $statusLabel,
                            'status_class' => $statusClass,
                            'result_label' => $statusKey === 'completed' ? 'View Report' : '-',
                            'result_url' => $statusKey === 'completed' ? route('hospital.pathology.worklist.print', ['item' => $pathologyVisit->id]) : null,
                        ]);
                    }

                    foreach (($radiologyVisits ?? collect()) as $radiologyVisit) {
                        $linkedVisit = ($visitsById ?? collect())->get($radiologyVisit->order->visitable_id ?? null);
                        $statusKey = strtolower(str_replace([' ', '-'], '_', (string) ($radiologyVisit->status ?? 'pending')));
                        $statusLabel = ucfirst(str_replace('_', ' ', $statusKey));
                        $statusClass = $statusKey === 'completed'
                            ? 'badge-success'
                            : ($statusKey === 'in_progress' ? 'badge-warning' : (in_array($statusKey, ['cancelled', 'rejected'], true) ? 'badge-danger' : 'badge-secondary'));

                        $orderRows->push([
                            'type' => 'Radiology',
                            'type_badge' => 'badge-purple',
                            'description' => $radiologyVisit->test_name ?? '-',
                            'ordered_by' => $linkedVisit?->consultant?->full_name ?? '-',
                            'date' => $radiologyVisit->created_at,
                            'status_label' => $statusLabel,
                            'status_class' => $statusClass,
                            'result_label' => $statusKey === 'completed' ? 'View Report' : '-',
                            'result_url' => $statusKey === 'completed' ? route('hospital.radiology.worklist.print', ['item' => $radiologyVisit->id]) : null,
                        ]);
                    }

                    foreach (($prescriptionVisits ?? collect()) as $prescriptionVisit) {
                        $orderRows->push([
                            'type' => 'Prescription',
                            'type_badge' => 'badge-accent',
                            'description' => 'Prescription - Case ' . ($prescriptionVisit->opdPatient?->case_no ?? '-'),
                            'ordered_by' => $prescriptionVisit->doctor?->full_name ?? '-',
                            'date' => $prescriptionVisit->created_at,
                            'status_label' => 'Issued',
                            'status_class' => 'badge-success',
                            'result_label' => 'View Prescription',
                            'result_url' => route('hospital.opd-patient.prescription.print', ['opdPatient' => $prescriptionVisit->opd_patient_id]),
                        ]);
                    }

                    $orderRows = $orderRows->sortByDesc(function ($row) {
                        return optional($row['date'])->timestamp ?? 0;
                    })->values();
                @endphp
                <div class="card">
                    <div class="card-header">
                        <div class="card-title"> Active Orders</div><button class="btn btn-primary btn-sm">+ New
                            Order</button>
                    </div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Order Type</th>
                                    <th>Description</th>
                                    <th>Ordered By</th>
                                    <th>Date/Time</th>
                                    <th>Status</th>
                                    <th>Result</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orderRows as $order)
                                    <tr>
                                        <td><span class="badge {{ $order['type_badge'] }}">{{ $order['type'] }}</span></td>
                                        <td>{{ $order['description'] }}</td>
                                        <td>{{ $order['ordered_by'] }}</td>
                                        <td>{{ $order['date'] ? \Carbon\Carbon::parse($order['date'])->format('d-m-Y h:i A') : '-' }}</td>
                                        <td><span class="badge {{ $order['status_class'] }}">{{ $order['status_label'] }}</span></td>
                                        <td>
                                            @if($order['result_url'])
                                                <a href="{{ $order['result_url'] }}" target="_blank" class="btn btn-ghost btn-xs">{{ $order['result_label'] }}</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No orders found for this patient.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- MEDICATIONS -->
            <div class="tab-pane" id="tabMeds">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title"> Current Medication Sheet</div><button class="btn btn-primary btn-sm">+
                            Prescribe</button>
                    </div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Drug</th>
                                    <th>Dose</th>
                                    <th>Route</th>
                                    <th>Frequency</th>
                                    <th>Duration</th>
                                    <th>Prescribed By</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="font-600">Metformin</td>
                                    <td>500mg</td>
                                    <td>Oral</td>
                                    <td>Twice daily (after food)</td>
                                    <td>Ongoing</td>
                                    <td>Dr. Sharma</td>
                                    <td><span class="badge badge-success">Active</span></td>
                                </tr>
                                <tr>
                                    <td class="font-600">Amlodipine</td>
                                    <td>10mg</td>
                                    <td>Oral</td>
                                    <td>Once daily (morning)</td>
                                    <td>Ongoing</td>
                                    <td>Dr. Sharma</td>
                                    <td><span class="badge badge-success">Active</span></td>
                                </tr>
                                <tr>
                                    <td class="font-600">Atorvastatin</td>
                                    <td>40mg</td>
                                    <td>Oral</td>
                                    <td>Once daily (night)</td>
                                    <td>Ongoing</td>
                                    <td>Dr. Sharma</td>
                                    <td><span class="badge badge-success">Active</span></td>
                                </tr>
                                <tr>
                                    <td class="font-600">Inj. Normal Saline</td>
                                    <td>500mL</td>
                                    <td>IV</td>
                                    <td>Q12h</td>
                                    <td>48 hours</td>
                                    <td>Dr. Sharma</td>
                                    <td><span class="badge badge-warning">Running</span></td>
                                </tr>
                                <tr>
                                    <td class="font-600">Aspirin</td>
                                    <td>75mg</td>
                                    <td>Oral</td>
                                    <td>Once daily</td>
                                    <td>Ongoing</td>
                                    <td>Dr. Sharma</td>
                                    <td><span class="badge badge-success">Active</span></td>
                                </tr>
                                <tr style="background:#fff3e0">
                                    <td class="font-600" style="color:#e65100">Inj. Labetalol</td>
                                    <td>20mg</td>
                                    <td>IV</td>
                                    <td>STAT (given)</td>
                                    <td>Single dose</td>
                                    <td>Dr. Sharma</td>
                                    <td><span class="badge badge-gray">Completed</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- LAB RESULTS -->
            <div class="tab-pane" id="tabLab">
                <div class="grid-2">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title"> Lab Results</div><span class="badge badge-warning">2
                                Abnormal</span>
                        </div>
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Test</th>
                                        <th>Result</th>
                                        <th>Ref. Range</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Haemoglobin</td>
                                        <td class="font-600">11.8 g/dL</td>
                                        <td>1317</td>
                                        <td><span class="badge badge-warning">Low</span></td>
                                        <td>Today</td>
                                    </tr>
                                    <tr>
                                        <td>Fasting Glucose</td>
                                        <td class="font-600" style="color:#c62828">212 mg/dL</td>
                                        <td>70100</td>
                                        <td><span class="badge badge-danger">High</span></td>
                                        <td>Today</td>
                                    </tr>
                                    <tr>
                                        <td>HbA1c</td>
                                        <td class="font-600">8.4%</td>
                                        <td>&lt;5.7%</td>
                                        <td><span class="badge badge-danger">High</span></td>
                                        <td>15 Mar</td>
                                    </tr>
                                    <tr>
                                        <td>Serum Creatinine</td>
                                        <td class="font-600" style="color:#2e7d32">1.1 mg/dL</td>
                                        <td>0.71.2</td>
                                        <td><span class="badge badge-success">Normal</span></td>
                                        <td>Today</td>
                                    </tr>
                                    <tr>
                                        <td>Total Cholesterol</td>
                                        <td class="font-600">198 mg/dL</td>
                                        <td>&lt;200</td>
                                        <td><span class="badge badge-success">Normal</span></td>
                                        <td>15 Mar</td>
                                    </tr>
                                    <tr>
                                        <td>LDL Cholesterol</td>
                                        <td class="font-600" style="color:#e65100">142 mg/dL</td>
                                        <td>&lt;100</td>
                                        <td><span class="badge badge-warning">High</span></td>
                                        <td>15 Mar</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title"> Radiology Reports</div>
                        </div>
                        <div class="card-body-sm">
                            <div class="list-item">
                                <div class="list-item-icon" style="background:#e3f2fd"></div>
                                <div class="list-item-body">
                                    <div class="li-title">Chest X-Ray PA Today</div>
                                    <div class="li-sub">Mild cardiomegaly. No consolidation. No pleural effusion.</div>
                                </div><button class="btn btn-ghost btn-xs">View</button>
                            </div>
                            <div class="list-item" style="opacity:.6">
                                <div class="list-item-icon" style="background:#e8f5e9"></div>
                                <div class="list-item-body">
                                    <div class="li-title">2D Echo 10 Mar 2024</div>
                                    <div class="li-sub">EF 55%. Concentric LVH. Grade I diastolic dysfunction.</div>
                                </div><button class="btn btn-ghost btn-xs">View</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- VITALS CHART -->
            <div class="tab-pane" id="tabVitals">
                <div class="grid-2">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title"> BP Trend</div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container" style="height:220px"><canvas id="bpChart"></canvas></div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title"> Blood Sugar Trend</div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container" style="height:220px"><canvas id="bsChart"></canvas></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- HISTORY -->
            <div class="tab-pane" id="tabHistory">
                <div class="grid-2">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title"> Past Encounters</div>
                        </div>
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Diagnosis</th>
                                        <th>Doctor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>24 Mar 2024</td>
                                        <td><span class="badge badge-primary">IPD</span></td>
                                        <td>Hypertensive Crisis + T2DM</td>
                                        <td>Dr. Sharma</td>
                                    </tr>
                                    <tr>
                                        <td>15 Mar 2024</td>
                                        <td><span class="badge badge-success">OPD</span></td>
                                        <td>DM Follow-up</td>
                                        <td>Dr. Sharma</td>
                                    </tr>
                                    <tr>
                                        <td>1 Feb 2024</td>
                                        <td><span class="badge badge-success">OPD</span></td>
                                        <td>Routine check</td>
                                        <td>Dr. Rawat</td>
                                    </tr>
                                    <tr>
                                        <td>5 Nov 2023</td>
                                        <td><span class="badge badge-warning">Emergency</span></td>
                                        <td>RBS 390 DKA borderline</td>
                                        <td>Dr. Pande</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title"> Allergy &amp; Immunisation</div>
                        </div>
                        <div class="card-body-sm">
                            <div class="alert alert-danger mb-3"><span class="alert-icon"></span>
                                <div>
                                    <div class="alert-title">Drug Allergy</div>Sulfonamides Rash (confirmed)
                                </div>
                            </div>
                            <div class="section-title">Vaccinations</div>
                            <div class="list-item">
                                <div class="list-item-body">
                                    <div class="li-title">COVID-19 Covishield</div>
                                    <div class="li-sub">2 doses + Booster 2022</div>
                                </div><span class="badge badge-success">Complete</span>
                            </div>
                            <div class="list-item">
                                <div class="list-item-body">
                                    <div class="li-title">Influenza</div>
                                    <div class="li-sub">Annual Last: Oct 2023</div>
                                </div><span class="badge badge-success">Current</span>
                            </div>
                            <div class="list-item">
                                <div class="list-item-body">
                                    <div class="li-title">Hepatitis B</div>
                                    <div class="li-sub">3 dose series</div>
                                </div><span class="badge badge-success">Complete</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BILLING -->
            <div class="tab-pane" id="tabBilling">
                @php
                    $billingRows = collect($patientCharges ?? [])->map(function ($charge) {
                        $statusKey = strtolower((string) ($charge->payment_status ?? 'unpaid'));
                        $statusClass = $statusKey === 'paid'
                            ? 'badge-success'
                            : ($statusKey === 'partial' ? 'badge-warning' : 'badge-danger');

                        return [
                            'service' => $charge->particular ?? '-',
                            'amount' => (float) ($charge->amount ?? 0),
                            'status_label' => ucfirst($statusKey),
                            'status_class' => $statusClass,
                        ];
                    });
                @endphp
                <div class="card">
                    <div class="card-header">
                        <div class="card-title"> Billing Summary</div>
                        <div class="d-flex gap-2 flex-wrap">
                            @if($visitMode === 'opd')
                                <button type="button" class="btn btn-success btn-sm open-charge-payment-form" data-url="{{ route('hospital.opd-patient.charges.show-payment-form', ['patient' => $patient->id]) }}">Payment / Discount</button>
                                <button type="button" class="btn btn-warning btn-sm open-charge-payment-form" data-url="{{ route('hospital.opd-patient.charges.show-refund-form', ['patient' => $patient->id]) }}">Refund Advance</button>
                                <a href="{{ route('hospital.opd-patient.charges.final-bill.print', ['patient' => $patient->id]) }}" target="_blank" class="btn btn-primary btn-sm">View Full Bill</a>
                            @elseif(!empty($activeIpdAllocation?->id))
                                <a href="{{ route('hospital.ipd-patient.final-bill.print', ['allocation' => $activeIpdAllocation->id]) }}" target="_blank" class="btn btn-primary btn-sm">View Full Bill</a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body-sm">
                        <div class="stat-row mb-3">
                            <div class="stat-item">
                                <div class="s-label">Total Charges</div>
                                <div class="s-value" style="color:#1565c0">{{ number_format((float) ($totalCharges ?? 0), 2) }}</div>
                            </div>
                            <div class="stat-item">
                                <div class="s-label">Paid (Adjusted)</div>
                                <div class="s-value" style="color:#2e7d32">{{ number_format((float) ($totalPaid ?? 0), 2) }}</div>
                            </div>
                            <div class="stat-item">
                                <div class="s-label">Balance Due</div>
                                <div class="s-value" style="color:#c62828">{{ number_format((float) ($totalDue ?? 0), 2) }}</div>
                            </div>
                        </div>
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Service</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($billingRows as $row)
                                        <tr>
                                            <td>{{ $row['service'] }}</td>
                                            <td>{{ number_format($row['amount'], 2) }}</td>
                                            <td><span class="badge {{ $row['status_class'] }}">{{ $row['status_label'] }}</span></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No billing records found for this patient.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- NOTES -->
            <div class="tab-pane" id="tabNotes">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title"> Clinical Notes</div><button class="btn btn-primary btn-sm">+ Add
                            Note</button>
                    </div>
                    <div class="card-body">
                        <div style="border:1.5px solid #e2ecf4;border-radius:10px;padding:16px;margin-bottom:16px">
                            <div class="flex justify-between mb-2">
                                <div><strong>Progress Note</strong> Dr. Rajesh Sharma</div>
                                <div class="text-muted text-sm">Today, 10:15 AM</div>
                            </div>
                            <p style="font-size:13px;line-height:1.6;color:#344a5e"><strong>S:</strong> Patient c/o
                                headache improved. BP remains elevated. No chest pain. Diet: poor
                                compliance.<br /><strong>O:</strong> BP 148/94, Pulse 88, Temp 99.2F, RBS 212. JVP
                                normal. No ankle oedema.<br /><strong>A:</strong> Hypertensive crisis improving.
                                Uncontrolled T2DM medication review needed.<br /><strong>P:</strong> Increase Amlodipine
                                to 10mg. Review Metformin dose. HbA1c result awaited. Continue IV fluids. Repeat BP in
                                4h.</p>
                        </div>
                        <div style="border:1.5px solid #e2ecf4;border-radius:10px;padding:16px;opacity:.7">
                            <div class="flex justify-between mb-2">
                                <div><strong>Admission Note</strong> Dr. Rajesh Sharma</div>
                                <div class="text-muted text-sm">Yesterday, 6:00 PM</div>
                            </div>
                            <p style="font-size:13px;line-height:1.6;color:#344a5e">62M known T2DM, HTN, Dyslipidaemia.
                                Presented with severe headache, BP 196/110, RBS 340. IV Labetalol given. IV access
                                established. Admitted for monitoring and stabilisation.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="chargePaymentModal" tabindex="-1" aria-hidden="true" data-bs-focus="false">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content" id="chargePaymentContent"></div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{asset('public/front/assets/css/gov.css')}}">
<style>
.opd-content-wrap {
    background: #f3f7fb;
    border-radius: 0;
    overflow: hidden;
}

.opd-content-wrap .content-area {
    padding-bottom: 24px;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function switchEMRTab(id, btn) {
    document.querySelectorAll('.tab-pane').forEach(function(pane) {
        pane.classList.remove('active');
    });
    document.querySelectorAll('.tab-btn').forEach(function(tabBtn) {
        tabBtn.classList.remove('active');
    });
    var el = document.getElementById(id);
    if (el) {
        el.classList.add('active');
    }
    if (btn) {
        btn.classList.add('active');
    }
    if (id === 'tabVitals') {
        initVitalCharts();
    }
}

var vitalChartsInited = false;

function initVitalCharts() {
    if (vitalChartsInited) return;
    vitalChartsInited = true;

    new Chart(document.getElementById('bpChart'), {
        type: 'line',
        data: {
            labels: ['Adm', '4h', '8h', '12h', '16h', '20h', '24h'],
            datasets: [{
                    label: 'Systolic',
                    data: [196, 180, 168, 158, 152, 148, 148],
                    borderColor: '#c62828',
                    backgroundColor: 'rgba(198,40,40,.07)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Diastolic',
                    data: [110, 104, 98, 96, 94, 92, 94],
                    borderColor: '#1565c0',
                    backgroundColor: 'rgba(21,101,192,.05)',
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: false
                }
            }
        }
    });

    new Chart(document.getElementById('bsChart'), {
        type: 'line',
        data: {
            labels: ['Adm', '4h', '8h', '12h', '16h', '20h'],
            datasets: [{
                label: 'RBS (mg/dL)',
                data: [340, 298, 260, 234, 218, 212],
                borderColor: '#e65100',
                backgroundColor: 'rgba(230,81,0,.07)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: false
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

(function () {
    if (typeof window.jQuery === 'undefined') {
        return;
    }

    function getCsrfToken() {
        return $('meta[name="csrf-token"]').attr('content');
    }

    $(document).on('click', '.open-charge-payment-form', function (event) {
        event.preventDefault();

        var url = $(this).data('url');
        var chargeIdRaw = parseInt($(this).data('charge-id'), 10);
        var chargeIdsRaw = ($(this).data('charge-ids') || '').toString().trim();
        var chargeIds = chargeIdsRaw
            ? chargeIdsRaw.split(',').map(function (id) { return parseInt(id, 10); }).filter(function (id) { return !isNaN(id); })
            : [];

        if (!isNaN(chargeIdRaw)) {
            chargeIds.push(chargeIdRaw);
        }

        chargeIds = Array.from(new Set(chargeIds));

        var title = ($(this).data('title') || '').toString();
        var contextNote = ($(this).data('context-note') || '').toString();

        if (!url) {
            if (typeof sendmsg === 'function') {
                sendmsg('error', 'Payment form URL missing.');
            }
            return;
        }

        if (typeof loader === 'function') {
            loader('show');
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: getCsrfToken(),
                charge_ids: chargeIds,
                title: title,
                context_note: contextNote
            },
            success: function (response) {
                if (typeof loader === 'function') {
                    loader('hide');
                }

                $('#chargePaymentContent').html(response);
                $('#chargePaymentModal').modal('show');
            },
            error: function (xhr) {
                if (typeof loader === 'function') {
                    loader('hide');
                }

                if (typeof sendmsg === 'function') {
                    sendmsg('error', xhr?.responseJSON?.message || 'Unable to open payment form.');
                }
            }
        });
    });

    $(document).on('hidden.bs.modal', '#chargePaymentModal', function () {
        $('#chargePaymentContent').empty();
    });
})();
</script>
@endpush