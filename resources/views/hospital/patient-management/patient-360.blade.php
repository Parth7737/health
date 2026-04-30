@extends('layouts.hospital.app', ['is_header_hiden' => true,'patient_360'=>true])
@section('title','Patient 360 | Paracare+')
@section('page_header_icon', '')
@section('page_subtitle', 'Manage Patient Profile')

@section('content')
@php
    $isIpdActive = ($visitContext ?? 'opd') === 'ipd' && !empty($activeIpdAllocation);
    $displayMrn = $patient->mrn ?: ($patient->patient_id ?: '-');
    $displayAbha = data_get($patient, 'abha_number')
        ?? data_get($patient, 'abha_no')
        ?? data_get($patient, 'abha_id')
        ?? data_get($patient, 'ayushman_bharat_id')
        ?? '-';

    $ageText = ($patient->age_years ?? '-') . ' Years';
    if (!empty($patient->age_months)) {
        $ageText .= ', ' . $patient->age_months . ' Months';
    }

    $patientName = $patient->name ?: 'Unknown Patient';
    $initials = collect(preg_split('/\s+/', trim((string) $patientName)))
        ->filter()
        ->take(2)
        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
        ->implode('');
    $initials = $initials ?: 'NA';

    $contextRecord = $isIpdActive ? $activeIpdAllocation : $latestOpdVisit;
    $contextDate = data_get($contextRecord, $isIpdActive ? 'admission_date' : 'appointment_date');
    $dateText = $contextDate ? \Carbon\Carbon::parse($contextDate)->format('d M Y') : '-';
    $dateLabel = $isIpdActive ? 'Admitted' : 'Visited';

    $wardName = data_get($activeIpdAllocation, 'bed.room.ward.ward_name') ?: data_get($activeIpdAllocation, 'bed.bedType.type_name') ?: 'Ward';
    $bedCode = data_get($activeIpdAllocation, 'bed.bed_code') ?: '-';

    $bpText = (filled(data_get($contextRecord, 'systolic_bp')) && filled(data_get($contextRecord, 'diastolic_bp')))
        ? data_get($contextRecord, 'systolic_bp') . '/' . data_get($contextRecord, 'diastolic_bp')
        : '-';
    $pulseText = $isIpdActive ? (data_get($contextRecord, 'pulse') ?: '-') : (data_get($contextRecord, 'pluse') ?: '-');
    $spo2Text = data_get($contextRecord, 'spo2') ?: data_get($contextRecord, 'spo2_percentage') ?: data_get($contextRecord, 'oxygen_saturation') ?: '-';
    $tempRaw = $isIpdActive ? data_get($contextRecord, 'temperature') : data_get($contextRecord, 'temperature');
    $tempText = filled($tempRaw) ? ((string) $tempRaw . '°F') : '-';
    $rbsText = $isIpdActive ? (data_get($contextRecord, 'diabetes') ?: '-') : (data_get($contextRecord, 'diabetes') ?: '-');
    $weightRaw = $isIpdActive ? data_get($contextRecord, 'weight') : data_get($contextRecord, 'weight');
    $weightText = filled($weightRaw) ? ((string) $weightRaw . ' kg') : '-';
    $bmiText = data_get($contextRecord, 'bmi') ?: '-';

    $p360VisitStatusRaw = strtolower((string) ($isIpdActive ? data_get($activeIpdAllocation, 'status') : data_get($latestOpdVisit, 'status')));
    if ($isIpdActive) {
        $p360VisitStatusLabel = $p360VisitStatusRaw !== ''
            ? ucwords(str_replace('_', ' ', $p360VisitStatusRaw))
            : 'Admitted';
        $p360VisitStatusBadgeClass = $p360VisitStatusRaw !== '' ? 'badge-secondary' : 'badge-success';
    } else {
        $p360VisitStatusLabel = $p360VisitStatusRaw !== ''
            ? ucwords(str_replace('_', ' ', $p360VisitStatusRaw))
            : '—';
        $p360VisitStatusBadgeClass = match ($p360VisitStatusRaw) {
            'completed' => 'badge-success',
            'in_room' => 'badge-primary',
            'waiting' => 'badge-warning',
            'booking' => 'badge-info',
            default => 'badge-secondary',
        };
    }
@endphp
<div class="container-fluid px-0">
    <div class="opd-content-wrap">
        <!-- Patient Header -->
        <div style="background:linear-gradient(135deg,#071221,#0a1628);padding:16px 24px;border-bottom:1px solid rgba(255,255,255,0.08)">
            <div class="flex items-center gap-4 flex-wrap">
                <div style="width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#1565c0,#00695c);color:white;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:800;flex-shrink:0;border:2px solid rgba(255,255,255,.2)">{{ $initials }}</div>
                <div class="flex-1">
                    <div style="font-size:18px;font-weight:800;color:#e8f2fb;letter-spacing:-.02em">{{ $patientName }}</div>
                    <div style="font-size:11.5px;color:#6a8fa8;margin-top:2px">
                        MRN: {{ $displayMrn }} &nbsp;·&nbsp;
                        ABHA: {{ $displayAbha }} &nbsp;·&nbsp;
                        {{ $ageText }}, {{ $patient->gender ?? '-' }} &nbsp;·&nbsp;
                        Blood Group: {{ $patient->blood_group ?: '-' }} &nbsp;·&nbsp;
                        {{ $dateLabel }}: {{ $dateText }}
                    </div>
                </div>
                <div class="flex gap-2 flex-wrap">
                    @if($isIpdActive)
                        <span class="badge badge-danger" style="font-size:11px">IPD - {{ $wardName }} Bed {{ $bedCode }}</span>
                        <span class="badge badge-primary">Admission: {{ $activeIpdAllocation->admission_no ?: '-' }}</span>
                        <span class="badge {{ $p360VisitStatusBadgeClass }}" style="font-size:11px">Status: {{ $p360VisitStatusLabel }}</span>
                    @else
                        <span class="badge badge-primary" style="font-size:11px">OPD - Visit {{ data_get($latestOpdVisit, 'case_no') ?: '-' }}</span>
                        <span class="badge badge-warning">Token: {{ filled(data_get($latestOpdVisit, 'token_no')) ? str_pad((int) data_get($latestOpdVisit, 'token_no'), 3, '0', STR_PAD_LEFT) : '-' }}</span>
                        <span class="badge {{ $p360VisitStatusBadgeClass }}" style="font-size:11px">Status: {{ $p360VisitStatusLabel }}</span>
                    @endif
                    <button class="btn btn-ghost btn-sm" style="color:#d0e8fb;border-color:rgba(255,255,255,.15)" onclick="window.print()">Print</button>
                    <button
                        type="button"
                        class="btn btn-primary btn-sm"
                        id="patient360NewOrderBtn"
                        data-mode="{{ $isIpdActive ? 'ipd' : 'opd' }}"
                        data-opd-id="{{ data_get($latestOpdVisit, 'id', '') }}"
                        data-allocation-id="{{ data_get($activeIpdAllocation, 'id', '') }}"
                        data-can-new-order="{{ ($canPatient360NewOrder ?? true) ? '1' : '0' }}"
                        data-block-reason="{{ e($patient360NewOrderBlockedReason ?? '') }}"
                        @if(!($canPatient360NewOrder ?? true))
                            disabled
                            aria-disabled="true"
                            style="opacity:.55;cursor:not-allowed"
                            title="{{ e($patient360NewOrderBlockedReason ?? 'New orders are not allowed.') }}"
                        @endif
                    >+ New Order</button>
                </div>
            </div>

            <!-- Quick vitals strip -->
            <div class="flex gap-4 mt-3 flex-wrap" style="border-top:1px solid rgba(255,255,255,.07);padding-top:12px">
                <div style="text-align:center"><div style="font-size:10px;color:#4a6880;text-transform:uppercase;letter-spacing:.05em">BP</div><div style="font-size:15px;font-weight:700;color:#ef5350">{{ $bpText }}</div></div>
                <div style="width:1px;background:rgba(255,255,255,.07)"></div>
                <div style="text-align:center"><div style="font-size:10px;color:#4a6880;text-transform:uppercase">Pulse</div><div style="font-size:15px;font-weight:700;color:#e8f2fb">{{ $pulseText }}</div></div>
                <div style="width:1px;background:rgba(255,255,255,.07)"></div>
                <div style="text-align:center"><div style="font-size:10px;color:#4a6880;text-transform:uppercase">SpO2</div><div style="font-size:15px;font-weight:700;color:#66bb6a">{{ $spo2Text }}</div></div>
                <div style="width:1px;background:rgba(255,255,255,.07)"></div>
                <div style="text-align:center"><div style="font-size:10px;color:#4a6880;text-transform:uppercase">Temp</div><div style="font-size:15px;font-weight:700;color:#fb8c00">{{ $tempText }}</div></div>
                <div style="width:1px;background:rgba(255,255,255,.07)"></div>
                <div style="text-align:center"><div style="font-size:10px;color:#4a6880;text-transform:uppercase">RBS</div><div style="font-size:15px;font-weight:700;color:#ef5350">{{ $rbsText }}</div></div>
                <div style="width:1px;background:rgba(255,255,255,.07)"></div>
                <div style="text-align:center"><div style="font-size:10px;color:#4a6880;text-transform:uppercase">Weight</div><div style="font-size:15px;font-weight:700;color:#e8f2fb">{{ $weightText }}</div></div>
                <div style="width:1px;background:rgba(255,255,255,.07)"></div>
                <div style="text-align:center"><div style="font-size:10px;color:#4a6880;text-transform:uppercase">BMI</div><div style="font-size:15px;font-weight:700;color:#fb8c00">{{ $bmiText }}</div></div>
            </div>
        </div>

        <!-- Tab navigation -->
        <div style="background:#fff;border-bottom:2px solid #e2ecf4;padding:0 24px">
            <div class="tab-bar" style="border:none;margin:0">
                <button class="tab-btn active" data-tab="tabTimeline" onclick="switchEMRTab('tabTimeline',this)">Timeline</button>
                <button class="tab-btn" data-tab="tabOrders" onclick="switchEMRTab('tabOrders',this)">Orders</button>
                <button class="tab-btn" data-tab="tabMeds" onclick="switchEMRTab('tabMeds',this)">Medications</button>
                <button class="tab-btn" data-tab="tabNotes" onclick="switchEMRTab('tabNotes',this)">Clinical Notes</button>
                <button class="tab-btn" data-tab="tabLab" onclick="switchEMRTab('tabLab',this)">Lab Results</button>
                <button class="tab-btn" data-tab="tabVitals" onclick="switchEMRTab('tabVitals',this)">Vitals Chart</button>
                <button class="tab-btn" data-tab="tabHistory" onclick="switchEMRTab('tabHistory',this)">History</button>
                <button class="tab-btn" data-tab="tabBilling" onclick="switchEMRTab('tabBilling',this)">Billing</button>
            </div>
        </div>

        <div class="content-area">
            <!-- TIMELINE -->
            <div class="tab-pane active" id="tabTimeline">
                <div class="grid-21">
                    <div class="card">
                        <div class="card-header"><div class="card-title">Clinical Timeline</div></div>
                        <div class="card-body">
                            <div class="timeline">
                                @forelse(($timelineEntries ?? collect()) as $timelineEntry)
                                    @php
                                        $eventKey = strtolower((string) data_get($timelineEntry, 'event_key', ''));
                                        $dotColor = 'gray';
                                        if (str_contains($eventKey, 'delete') || str_contains($eventKey, 'refund') || str_contains($eventKey, 'discount') || str_contains($eventKey, 'payment')) {
                                            $dotColor = 'orange';
                                        } elseif (str_contains($eventKey, 'diagnosis') || str_contains($eventKey, 'status')) {
                                            $dotColor = 'red';
                                        } elseif (str_contains($eventKey, 'prescription') || str_contains($eventKey, 'medicine') || str_contains($eventKey, 'vitals')) {
                                            $dotColor = 'green';
                                        } elseif (str_contains($eventKey, 'lab') || str_contains($eventKey, 'pathology') || str_contains($eventKey, 'radiology') || str_contains($eventKey, 'visit') || str_contains($eventKey, 'opd') || str_contains($eventKey, 'ipd')) {
                                            $dotColor = 'blue';
                                        }

                                        $loggedAt = data_get($timelineEntry, 'logged_at') ?: data_get($timelineEntry, 'created_at');
                                        $timeLabel = '-';
                                        if ($loggedAt) {
                                            $logAt = \Carbon\Carbon::parse($loggedAt);
                                            if ($logAt->isToday()) {
                                                $timeLabel = 'Today, ' . $logAt->format('h:i A');
                                            } elseif ($logAt->isYesterday()) {
                                                $timeLabel = 'Yesterday, ' . $logAt->format('h:i A');
                                            } else {
                                                $timeLabel = $logAt->format('d M Y, h:i A');
                                            }
                                        }

                                        $title = data_get($timelineEntry, 'title')
                                            ?: data_get($timelineEntry, 'event_name')
                                            ?: data_get($timelineEntry, 'event_key')
                                            ?: 'Clinical Event';
                                        $detail = data_get($timelineEntry, 'description')
                                            ?: data_get($timelineEntry, 'notes')
                                            ?: data_get($timelineEntry, 'event_detail');
                                    @endphp
                                    <div class="tl-item">
                                        <div class="tl-dot {{ $dotColor }}"></div>
                                        <div class="tl-time">{{ $timeLabel }}</div>
                                        <div class="tl-title">{{ $title }}</div>
                                        @if(!empty($detail))
                                            <div class="tl-detail">{{ $detail }}</div>
                                        @endif
                                    </div>
                                @empty
                                    <div class="tl-item">
                                        <div class="tl-dot gray"></div>
                                        <div class="tl-time">No activity</div>
                                        <div class="tl-title">No clinical timeline found for this patient.</div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-4">
                        <!-- Problem List -->
                        <div class="card">
                            <div class="card-header"><div class="card-title">Active Problem List</div></div>
                            <div class="card-body-sm">
                                <div class="list-item"><div class="list-item-icon" style="background:#ffebee"></div><div class="list-item-body"><div class="li-title">Type 2 Diabetes Mellitus</div><div class="li-sub">ICD-10: E11.9 · Uncontrolled · Since 2018</div></div><span class="badge badge-danger">Active</span></div>
                                <div class="list-item"><div class="list-item-icon" style="background:#fff3e0"></div><div class="list-item-body"><div class="li-title">Essential Hypertension</div><div class="li-sub">ICD-10: I10 · Poorly controlled · Since 2020</div></div><span class="badge badge-warning">Active</span></div>
                                <div class="list-item"><div class="list-item-icon" style="background:#e3f2fd"></div><div class="list-item-body"><div class="li-title">Dyslipidaemia</div><div class="li-sub">ICD-10: E78.5 · On statin · LDL 142</div></div><span class="badge badge-primary">Active</span></div>
                            </div>
                        </div>
                        <!-- AI -->
                        <div class="ai-insight-block">
                            <div class="ai-header">AI Clinical Copilot</div>
                            <div class="ai-body">
                                <div class="ai-item"><div class="ai-dot" style="background:#e65100"></div><div>HbA1c trend worsening (7.9 to 8.4%). Consider dual therapy - add Sitagliptin or refer for endocrinology review.</div></div>
                                <div class="ai-item"><div class="ai-dot"></div><div>BP target for DM+CKD is &lt;130/80 per ACC/AHA. Current 148/94 is above target - ARB preferred.</div></div>
                                <div class="ai-item"><div class="ai-dot" style="background:#2e7d32"></div><div>Kidney function stable (Cr 1.1). Continue Metformin with monitoring. eGFR estimation recommended.</div></div>
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
                        $linkedVisitId = (int) data_get($pathologyVisit, 'order.visitable_id');
                        $linkedVisit = ($opdVisitsById ?? collect())->get($linkedVisitId);
                        $consultantName = trim(
                            data_get($linkedVisit, 'consultant.first_name', '') . ' ' . data_get($linkedVisit, 'consultant.last_name', '')
                        );

                        $statusKey = strtolower(str_replace([' ', '-'], '_', (string) data_get($pathologyVisit, 'status', 'pending')));
                        $statusLabel = ucfirst(str_replace('_', ' ', $statusKey));
                        $statusClass = $statusKey === 'completed'
                            ? 'badge-success'
                            : ($statusKey === 'in_progress'
                                ? 'badge-warning'
                                : (in_array($statusKey, ['cancelled', 'rejected'], true) ? 'badge-danger' : 'badge-secondary'));

                        $orderRows->push([
                            'type' => 'Pathology',
                            'type_badge' => 'badge-primary',
                            'description' => data_get($pathologyVisit, 'test_name', '-'),
                            'ordered_by' => $consultantName !== '' ? $consultantName : '-',
                            'date' => data_get($pathologyVisit, 'created_at'),
                            'status_label' => $statusLabel,
                            'status_class' => $statusClass,
                            'result_label' => $statusKey === 'completed' ? 'View Report' : '-',
                            'result_url' => ($statusKey === 'completed' && Route::has('hospital.pathology.worklist.print'))
                                ? route('hospital.pathology.worklist.print', ['item' => $pathologyVisit->id])
                                : null,
                        ]);
                    }

                    foreach (($radiologyVisits ?? collect()) as $radiologyVisit) {
                        $linkedVisitId = (int) data_get($radiologyVisit, 'order.visitable_id');
                        $linkedVisit = ($opdVisitsById ?? collect())->get($linkedVisitId);
                        $consultantName = trim(
                            data_get($linkedVisit, 'consultant.first_name', '') . ' ' . data_get($linkedVisit, 'consultant.last_name', '')
                        );

                        $statusKey = strtolower(str_replace([' ', '-'], '_', (string) data_get($radiologyVisit, 'status', 'pending')));
                        $statusLabel = ucfirst(str_replace('_', ' ', $statusKey));
                        $statusClass = $statusKey === 'completed'
                            ? 'badge-success'
                            : ($statusKey === 'in_progress'
                                ? 'badge-warning'
                                : (in_array($statusKey, ['cancelled', 'rejected'], true) ? 'badge-danger' : 'badge-secondary'));

                        $orderRows->push([
                            'type' => 'Radiology',
                            'type_badge' => 'badge-purple',
                            'description' => data_get($radiologyVisit, 'test_name', '-'),
                            'ordered_by' => $consultantName !== '' ? $consultantName : '-',
                            'date' => data_get($radiologyVisit, 'created_at'),
                            'status_label' => $statusLabel,
                            'status_class' => $statusClass,
                            'result_label' => $statusKey === 'completed' ? 'View Report' : '-',
                            'result_url' => ($statusKey === 'completed' && Route::has('hospital.radiology.worklist.print'))
                                ? route('hospital.radiology.worklist.print', ['item' => $radiologyVisit->id])
                                : null,
                        ]);
                    }

                    foreach (($prescriptionVisits ?? collect()) as $prescriptionVisit) {
                        $doctorName = trim(
                            data_get($prescriptionVisit, 'doctor.first_name', '') . ' ' . data_get($prescriptionVisit, 'doctor.last_name', '')
                        );

                        $orderRows->push([
                            'type' => 'Prescription',
                            'type_badge' => 'badge-info',
                            'description' => 'Prescription - Case ' . (data_get($prescriptionVisit, 'opdPatient.case_no') ?: '-'),
                            'ordered_by' => $doctorName !== '' ? $doctorName : '-',
                            'date' => data_get($prescriptionVisit, 'created_at'),
                            'status_label' => 'Issued',
                            'status_class' => 'badge-success',
                            'result_label' => 'View Prescription',
                            'result_url' => (Route::has('hospital.opd-patient.prescription.print') && !empty($prescriptionVisit->opd_patient_id))
                                ? route('hospital.opd-patient.prescription.print', ['opdPatient' => $prescriptionVisit->opd_patient_id])
                                : null,
                        ]);
                    }

                    foreach (($ipdPrescriptionVisits ?? collect()) as $ipdPrescriptionVisit) {
                        $doctorName = trim(
                            data_get($ipdPrescriptionVisit, 'doctor.first_name', '') . ' ' . data_get($ipdPrescriptionVisit, 'doctor.last_name', '')
                        );

                        $orderRows->push([
                            'type' => 'Prescription',
                            'type_badge' => 'badge-info',
                            'description' => 'IPD Prescription - Admission ' . (data_get($ipdPrescriptionVisit, 'allocation.admission_no') ?: '-'),
                            'ordered_by' => $doctorName !== '' ? $doctorName : '-',
                            'date' => data_get($ipdPrescriptionVisit, 'created_at'),
                            'status_label' => 'Issued',
                            'status_class' => 'badge-success',
                            'result_label' => 'View Prescription',
                            'result_url' => (Route::has('hospital.ipd-patient.prescription.print')
                                && !empty($ipdPrescriptionVisit->bed_allocation_id)
                                && !empty($ipdPrescriptionVisit->id))
                                ? route('hospital.ipd-patient.prescription.print', [
                                    'allocation' => $ipdPrescriptionVisit->bed_allocation_id,
                                    'prescription' => $ipdPrescriptionVisit->id,
                                ])
                                : null,
                        ]);
                    }

                    $orderRows = $orderRows
                        ->sortByDesc(function ($row) {
                            return data_get($row, 'date') ? \Carbon\Carbon::parse(data_get($row, 'date'))->timestamp : 0;
                        })
                        ->values();
                @endphp
                <div class="card">
                    <div class="card-header"><div class="card-title">Active Orders</div></div>
                    <div class="table-wrap">
                        <table>
                            <thead><tr><th>Order Type</th><th>Description</th><th>Ordered By</th><th>Date/Time</th><th>Status</th><th>Result</th></tr></thead>
                            <tbody>
                                @forelse($orderRows as $order)
                                    <tr>
                                        <td><span class="badge {{ $order['type_badge'] }}">{{ $order['type'] }}</span></td>
                                        <td>{{ $order['description'] }}</td>
                                        <td>{{ $order['ordered_by'] }}</td>
                                        <td>{{ $order['date'] ? \Carbon\Carbon::parse($order['date'])->format('d M Y, h:i A') : '-' }}</td>
                                        <td><span class="badge {{ $order['status_class'] }}">{{ $order['status_label'] }}</span></td>
                                        <td>
                                            @if(!empty($order['result_url']))
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
                    <div class="card-header"><div class="card-title">Current Medication Sheet</div><button class="btn btn-primary btn-sm">+ Prescribe</button></div>
                    <div class="table-wrap">
                        <table>
                            <thead><tr><th>Drug</th><th>Dose</th><th>Route</th><th>Frequency</th><th>Duration</th><th>Prescribed By</th><th>Status</th></tr></thead>
                            <tbody>
                                <tr><td class="font-600">Metformin</td><td>500mg</td><td>Oral</td><td>Twice daily (after food)</td><td>Ongoing</td><td>Dr. Sharma</td><td><span class="badge badge-success">Active</span></td></tr>
                                <tr><td class="font-600">Amlodipine</td><td>10mg</td><td>Oral</td><td>Once daily (morning)</td><td>Ongoing</td><td>Dr. Sharma</td><td><span class="badge badge-success">Active</span></td></tr>
                                <tr><td class="font-600">Atorvastatin</td><td>40mg</td><td>Oral</td><td>Once daily (night)</td><td>Ongoing</td><td>Dr. Sharma</td><td><span class="badge badge-success">Active</span></td></tr>
                                <tr><td class="font-600">Inj. Normal Saline</td><td>500mL</td><td>IV</td><td>Q12h</td><td>48 hours</td><td>Dr. Sharma</td><td><span class="badge badge-warning">Running</span></td></tr>
                                <tr><td class="font-600">Aspirin</td><td>75mg</td><td>Oral</td><td>Once daily</td><td>Ongoing</td><td>Dr. Sharma</td><td><span class="badge badge-success">Active</span></td></tr>
                                <tr style="background:#fff3e0"><td class="font-600" style="color:#e65100">Inj. Labetalol</td><td>20mg</td><td>IV</td><td>STAT (given)</td><td>Single dose</td><td>Dr. Sharma</td><td><span class="badge badge-gray">Completed</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- LAB RESULTS — pathology: abnormal / flagged / summary only (in-range normal lines hidden) -->
            <div class="tab-pane" id="tabLab">
                @php
                    $labRows = $pathologyLabResultRows ?? collect();
                    $abnormalLabCount = (int) ($pathologyAbnormalCount ?? 0);
                    $radiologySorted = ($radiologyVisits ?? collect())->sortByDesc(function ($r) {
                        return optional($r->reported_at ?? $r->created_at)->timestamp ?? 0;
                    })->values();
                @endphp
                <div class="grid-2">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Lab Results (Pathology)</div>
                            @if($labRows->isNotEmpty() && $abnormalLabCount > 0)
                                <span class="badge badge-warning">{{ $abnormalLabCount }} abnormal</span>
                            @endif
                        </div>
                        <div class="table-wrap">
                            <table>
                                <thead><tr><th>Test</th><th>Result</th><th>Ref. Range</th><th>Status</th><th>Date</th><th></th></tr></thead>
                                <tbody>
                                    @forelse($labRows as $labRow)
                                        @php
                                            $flag = $labRow['result_flag'] ?? null;
                                            $statusLabel = match ($flag) {
                                                'low' => 'Low',
                                                'high' => 'High',
                                                'critical_low' => 'Critical low',
                                                'critical_high' => 'Critical high',
                                                'normal' => 'Normal',
                                                default => '—',
                                            };
                                            $statusBadge = match ($flag) {
                                                'low', 'high' => 'badge-warning',
                                                'critical_low', 'critical_high' => 'badge-danger',
                                                'normal' => 'badge-success',
                                                default => 'badge-secondary',
                                            };
                                            $resultStyle = match ($flag) {
                                                'critical_low', 'critical_high', 'high' => 'color:#c62828',
                                                'low' => 'color:#e65100',
                                                'normal' => 'color:#2e7d32',
                                                default => '',
                                            };
                                            $labDate = !empty($labRow['dated_at']) ? \Carbon\Carbon::parse($labRow['dated_at']) : null;
                                            $labStatusKey = strtolower(str_replace([' ', '-'], '_', (string) ($labRow['item_status'] ?? '')));
                                            $printUrl = ($labStatusKey === 'completed' && \Illuminate\Support\Facades\Route::has('hospital.pathology.worklist.print'))
                                                ? route('hospital.pathology.worklist.print', ['item' => $labRow['item_id']])
                                                : null;
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="font-600">{{ $labRow['test_label'] }}</div>
                                                <div class="fs-11 text-muted">{{ $labRow['context_line'] }}</div>
                                            </td>
                                            <td class="font-600" @if($resultStyle !== '') style="{{ $resultStyle }}" @endif>{{ $labRow['result'] }}</td>
                                            <td>{{ $labRow['ref_range'] }}</td>
                                            <td>
                                                @if($flag)
                                                    <span class="badge {{ $statusBadge }}">{{ $statusLabel }}</span>
                                                @else
                                                    <span class="badge badge-secondary">Summary</span>
                                                @endif
                                            </td>
                                            <td>{{ $labDate ? $labDate->format('d M Y') : '—' }}</td>
                                            <td>
                                                @if($printUrl)
                                                    <a href="{{ $printUrl }}" target="_blank" class="btn btn-ghost btn-xs">Print</a>
                                                @else
                                                    —
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No abnormal pathology results to show. In-range (normal) results are hidden.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header"><div class="card-title">Radiology Reports</div></div>
                        <div class="card-body-sm">
                            @forelse($radiologySorted as $radItem)
                                @php
                                    $radStatusKey = strtolower(str_replace([' ', '-'], '_', (string) ($radItem->status ?? 'pending')));
                                    $radDate = $radItem->reported_at ?? $radItem->created_at;
                                    $radDateLabel = $radDate ? \Carbon\Carbon::parse($radDate)->format('d M Y') : '—';
                                    $radSub = $radItem->report_summary ?: strip_tags((string) ($radItem->report_text ?? ''));
                                    $radSubShort = \Illuminate\Support\Str::limit(trim($radSub ?: '—'), 180);
                                    $radPrint = ($radStatusKey === 'completed' && \Illuminate\Support\Facades\Route::has('hospital.radiology.worklist.print'))
                                        ? route('hospital.radiology.worklist.print', ['item' => $radItem->id])
                                        : null;
                                @endphp
                                <div class="list-item" @if($radStatusKey !== 'completed') style="opacity:.75" @endif>
                                    <div class="list-item-icon" style="background:#e3f2fd"></div>
                                    <div class="list-item-body">
                                        <div class="li-title">{{ $radItem->test_name }} — {{ $radDateLabel }}</div>
                                        <div class="li-sub">{{ $radSubShort }}</div>
                                    </div>
                                    @if($radPrint)
                                        <a href="{{ $radPrint }}" target="_blank" class="btn btn-ghost btn-xs">View</a>
                                    @else
                                        <span class="fs-11 text-muted">{{ ucfirst(str_replace('_', ' ', $radStatusKey)) }}</span>
                                    @endif
                                </div>
                            @empty
                                <div class="fs-12 text-muted" style="padding:12px 16px">No radiology reports found for this patient.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- VITALS CHART -->
            <div class="tab-pane" id="tabVitals">
                <div class="grid-2">
                    <div class="card"><div class="card-header"><div class="card-title">BP Trend</div></div><div class="card-body"><div class="chart-container" style="height:220px"><canvas id="bpChart"></canvas></div></div></div>
                    <div class="card"><div class="card-header"><div class="card-title">Blood Sugar Trend</div></div><div class="card-body"><div class="chart-container" style="height:220px"><canvas id="bsChart"></canvas></div></div></div>
                </div>
            </div>

            <!-- HISTORY -->
            <div class="tab-pane" id="tabHistory">
                <div class="grid-2">
                    <div class="card">
                        <div class="card-header"><div class="card-title">Past Encounters</div></div>
                        <div class="table-wrap">
                            <table>
                                <thead><tr><th>Date</th><th>Type</th><th>Diagnosis</th><th>Doctor</th></tr></thead>
                                <tbody>
                                    @forelse($visits as $visit)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($visit->appointment_date ?? $visit->admission_date)->format('d M Y') }}</td>
                                            <td>
                                                @if(!empty($visit->is_ipd) || isset($visit->admission_date))
                                                    <span class="badge badge-primary">IPD</span>
                                                @else
                                                    <span class="badge badge-success">OPD</span>
                                                @endif
                                            </td>
                                            <td>{{ $visit->symptoms_name ?? $visit->symptoms_description ?? $visit->admission_reason ?? '-' }}</td>
                                            <td>{{ $visit->consultant->full_name ?? $visit->doctor_name ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No past encounters found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header"><div class="card-title">Allergy &amp; Immunisation</div></div>
                        <div class="card-body-sm">
                            <div class="alert alert-danger mb-3"><span class="alert-icon">! </span><div><div class="alert-title">Drug Allergy</div>Sulfonamides - Rash (confirmed)</div></div>
                            <div class="section-title">Vaccinations</div>
                            <div class="list-item"><div class="list-item-body"><div class="li-title">COVID-19 - Covishield</div><div class="li-sub">2 doses + Booster · 2022</div></div><span class="badge badge-success">Complete</span></div>
                            <div class="list-item"><div class="list-item-body"><div class="li-title">Influenza</div><div class="li-sub">Annual · Last: Oct 2023</div></div><span class="badge badge-success">Current</span></div>
                            <div class="list-item"><div class="list-item-body"><div class="li-title">Hepatitis B</div><div class="li-sub">3 dose series</div></div><span class="badge badge-success">Complete</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BILLING -->
            <div class="tab-pane" id="tabBilling">
                @php
                    $fullBillUrl = null;
                    if ($isIpdActive && !empty(data_get($activeIpdAllocation, 'id')) && Route::has('hospital.ipd-patient.final-bill.print')) {
                        $fullBillUrl = route('hospital.ipd-patient.final-bill.print', ['allocation' => data_get($activeIpdAllocation, 'id')]);
                    } elseif (Route::has('hospital.opd-patient.charges.final-bill.print')) {
                        $fullBillUrl = route('hospital.opd-patient.charges.final-bill.print', ['patient' => $patient->id]);
                    }

                    $billingRows = collect($patientCharges ?? collect())->values();
                @endphp
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Billing Summary</div>
                        @if($fullBillUrl)
                            <a href="{{ $fullBillUrl }}" target="_blank" class="btn btn-primary btn-sm">View Full Bill</a>
                        @endif
                    </div>
                    <div class="card-body-sm">
                        <div class="stat-row mb-3">
                            <div class="stat-item"><div class="s-label">Total Charges</div><div class="s-value" style="color:#1565c0">Rs {{ number_format((float) ($totalCharges ?? 0), 2) }}</div></div>
                            <div class="stat-item"><div class="s-label">Paid Amount</div><div class="s-value" style="color:#2e7d32">Rs {{ number_format((float) ($totalPaid ?? 0), 2) }}</div></div>
                            <div class="stat-item"><div class="s-label">Balance Due</div><div class="s-value" style="color:#c62828">Rs {{ number_format((float) ($totalDue ?? 0), 2) }}</div></div>
                        </div>
                        <div class="table-wrap">
                            <table>
                                <thead><tr><th>Service</th><th>Amount</th><th>Status</th></tr></thead>
                                <tbody>
                                    @forelse($billingRows as $charge)
                                        @php
                                            $chargeAmount = (float) data_get($charge, 'amount', 0);
                                            $chargePaid = (float) data_get($charge, 'paid_amount', 0);
                                            $dueAmount = max(0, $chargeAmount - $chargePaid);
                                            $statusKey = strtolower((string) data_get($charge, 'payment_status', ''));

                                            if ($statusKey === 'paid' || $dueAmount <= 0) {
                                                $statusLabel = 'Paid';
                                                $statusClass = 'badge-success';
                                            } elseif ($statusKey === 'partial' || ($chargePaid > 0 && $dueAmount > 0)) {
                                                $statusLabel = 'Partial';
                                                $statusClass = 'badge-primary';
                                            } else {
                                                $statusLabel = 'Pending';
                                                $statusClass = 'badge-warning';
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ data_get($charge, 'particular') ?: ('Charge #' . data_get($charge, 'id')) }}</td>
                                            <td>Rs {{ number_format($chargeAmount, 2) }}</td>
                                            <td><span class="badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
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
                    <div class="card-header"><div class="card-title">Clinical Notes</div><button class="btn btn-primary btn-sm">+ Add Note</button></div>
                    <div class="card-body">
                        <div style="border:1.5px solid #e2ecf4;border-radius:10px;padding:16px;margin-bottom:16px">
                            <div class="flex justify-between mb-2">
                                <div><strong>Progress Note</strong> - Dr. Rajesh Sharma</div>
                                <div class="text-muted text-sm">Today, 10:15 AM</div>
                            </div>
                            <p style="font-size:13px;line-height:1.6;color:#344a5e"><strong>S:</strong> Patient c/o headache improved. BP remains elevated. No chest pain. Diet: poor compliance.<br><strong>O:</strong> BP 148/94, Pulse 88, Temp 99.2°F, RBS 212. JVP normal. No ankle oedema.<br><strong>A:</strong> Hypertensive crisis - improving. Uncontrolled T2DM - medication review needed.<br><strong>P:</strong> Increase Amlodipine to 10mg. Review Metformin dose. HbA1c result awaited. Continue IV fluids. Repeat BP in 4h.</p>
                        </div>
                        <div style="border:1.5px solid #e2ecf4;border-radius:10px;padding:16px;opacity:.7">
                            <div class="flex justify-between mb-2">
                                <div><strong>Admission Note</strong> - Dr. Rajesh Sharma</div>
                                <div class="text-muted text-sm">Yesterday, 6:00 PM</div>
                            </div>
                            <p style="font-size:13px;line-height:1.6;color:#344a5e">62M known T2DM, HTN, Dyslipidaemia. Presented with severe headache, BP 196/110, RBS 340. IV Labetalol given. IV access established. Admitted for monitoring and stabilisation.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Patient 360 — New Order Modal --}}
<div class="modal fade" id="p360Modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="p360ModalTitle">New Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="p360ModalBody">
                <div class="p-4 text-center text-muted">Loading...</div>
            </div>
            <div class="modal-footer p360-modal-footer">
                <button type="button" id="p360SaveBtn" class="btn btn-primary px-5" style="display:none">Save</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal CSS in body so it loads AFTER bootstrap.css and wins on specificity --}}
<style>
#p360Modal { padding-right: 0 !important; }
#p360Modal .modal-dialog {
    max-width: none !important;
    width: calc(100vw - 16px) !important;
    height: calc(100vh - 48px) !important;
    margin: 24px 8px !important;
}
#p360Modal .modal-content {
    border-radius: 12px !important;
    border: 2px solid #2c6db6 !important;
    background: #fff !important;
    display: flex !important;
    flex-direction: column !important;
    height: 100% !important;
    max-height: calc(100vh - 48px) !important;
    overflow: hidden !important;
    box-shadow: 0 18px 48px rgba(18,49,80,.28) !important;
}
#p360Modal .modal-header {
    flex-shrink: 0;
    border-bottom: 1px solid #e3edf8;
    padding: 12px 16px;
    background: #fff;
    color: #0d1b2a;
}
#p360Modal .modal-body {
    flex: 1 1 auto !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
    min-height: 0 !important;
    padding: 16px !important;
    background: #fff !important;
}
#p360Modal .p360-modal-footer {
    flex-shrink: 0;
    border-top: 1px solid #e3edf8;
    padding: 10px 16px;
    display: flex;
    justify-content: flex-end;
    background: #f7fafd;
}
</style>
@endsection

@push('styles')
<link rel="stylesheet" type="text/css" href="{{asset('public/front/assets/css/gov.css')}}">
@endpush

@push('scripts')
<script src="{{ asset('public/modules/sa/opd-care-shared.js') }}"></script>
<script>
/* ── Page tab switching ─────────────────────────────────────── */
function switchEMRTab(id, btn) {
    document.querySelectorAll('.tab-pane').forEach(function(pane) { pane.classList.remove('active'); });
    document.querySelectorAll('.tab-btn').forEach(function(tabBtn) { tabBtn.classList.remove('active'); });
    var el = document.getElementById(id);
    if (el) { el.classList.add('active'); }
    if (btn) { btn.classList.add('active'); }
    if (id === 'tabVitals') { initVitalCharts(); }
}

/* ── Vitals chart (static demo data in chart tab) ───────────── */
var vitalChartsInited = false;
function initVitalCharts() {
    if (vitalChartsInited || typeof Chart === 'undefined') { return; }
    vitalChartsInited = true;
    new Chart(document.getElementById('bpChart'), {
        type: 'line',
        data: {
            labels: ['Adm', '4h', '8h', '12h', '16h', '20h', '24h'],
            datasets: [
                { label: 'Systolic',  data: [196,180,168,158,152,148,148], borderColor: '#c62828', backgroundColor: 'rgba(198,40,40,.07)', fill: true, tension: 0.4 },
                { label: 'Diastolic', data: [110,104, 98, 96, 94, 92, 94], borderColor: '#1565c0', backgroundColor: 'rgba(21,101,192,.05)', fill: true, tension: 0.4 }
            ]
        },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: false } } }
    });
    new Chart(document.getElementById('bsChart'), {
        type: 'line',
        data: {
            labels: ['Adm', '4h', '8h', '12h', '16h', '20h'],
            datasets: [
                { label: 'RBS (mg/dL)', data: [340,298,260,234,218,212], borderColor: '#e65100', backgroundColor: 'rgba(230,81,0,.07)', fill: true, tension: 0.4 }
            ]
        },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: false } }, plugins: { legend: { display: false } } }
    });
}
</script>

{{-- Patient 360 config — injected for patient-360.js --}}
<script>
window.Patient360Config = {
    routes: {
        opd: {
            careUnifiedForm:         @json(route('hospital.opd-patient.doctor-care.unified',    ['opdPatient' => '__ID__'])),
            prescriptionForm:        @json(route('hospital.opd-patient.prescription.form',      ['opdPatient' => '__ID__'])),
            prescriptionStore:       @json(route('hospital.opd-patient.prescription.store',     ['opdPatient' => '__ID__'])),
            prescriptionDestroy:     @json(route('hospital.opd-patient.prescription.destroy',   ['opdPatient' => '__ID__'])),
            prescriptionLoadDosages: @json(route('hospital.opd-patient.prescription.load-dosages')),
            diagnosticShow:          @json(route('hospital.opd-patient.diagnostics.showform',   ['opdPatient' => '__ID__'])),
            diagnosticStore:         @json(route('hospital.opd-patient.diagnostics.store',      ['opdPatient' => '__ID__'])),
            updateVitalsSocial:      @json(route('hospital.opd-patient.vitals-social.update',   ['opdPatient' => '__ID__']))
        },
        ipd: {
            prescriptionForm:        @json(route('hospital.ipd-patient.prescription.form',      ['allocation' => '__ALLOCATION__'])),
            prescriptionStore:       @json(route('hospital.ipd-patient.prescription.store',     ['allocation' => '__ALLOCATION__'])),
            prescriptionLoadDosages: @json(route('hospital.ipd-patient.prescription.load-dosages')),
            diagnosticShow:          @json(route('hospital.ipd-patient.diagnostics.showform',   ['allocation' => '__ALLOCATION__'])),
            diagnosticStore:         @json(route('hospital.ipd-patient.diagnostics.store',      ['allocation' => '__ALLOCATION__']))
        }
    },
    csrf: @json(csrf_token()),
    permissions: {
        canPathology: @json(auth()->user()->can('create-pathology-order')),
        canRadiology: @json(auth()->user()->can('create-radiology-order'))
    },
    vitals: {
        systolic_bp:  @json(data_get($contextRecord, 'systolic_bp')),
        diastolic_bp: @json(data_get($contextRecord, 'diastolic_bp')),
        pulse:        @json($isIpdActive ? data_get($contextRecord, 'pulse') : data_get($contextRecord, 'pluse')),
        spo2:         @json(data_get($contextRecord, 'spo2') ?? data_get($contextRecord, 'spo2_percentage')),
        temperature:  @json(data_get($contextRecord, 'temperature')),
        weight:       @json(data_get($contextRecord, 'weight')),
        rbs:          @json(data_get($contextRecord, 'diabetes'))
    }
};
</script>
<script src="{{ asset('public/modules/sa/patient-360.js') }}"></script>
@endpush
