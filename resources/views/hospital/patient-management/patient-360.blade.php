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

    $patientName = $patient->full_name ?: 'Unknown Patient';
    $initials = collect(preg_split('/\s+/', trim((string) $patientName)))
        ->filter()
        ->take(2)
        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
        ->implode('');
    $initials = $initials ?: 'NA';

    $contextRecord = $isIpdActive ? $activeIpdAllocation : $latestOpdVisit;
    $consultantName = $isIpdActive ? data_get($activeIpdAllocation, 'consultantDoctor.first_name') . ' ' . data_get($activeIpdAllocation, 'consultantDoctor.last_name') : data_get($latestOpdVisit, 'consultant.first_name') . ' ' . data_get($latestOpdVisit, 'consultant.last_name');
    $contextDate = data_get($contextRecord, $isIpdActive ? 'admission_date' : 'appointment_date');
    $dateText = $contextDate ? \Carbon\Carbon::parse($contextDate)->format('d M Y') : '-';
    $dateLabel = $isIpdActive ? 'Admitted' : 'Visited';
    if($contextDate > now()->startOfDay()) {
        $dateLabel = 'Scheduled';
    }
    $wardName = data_get($activeIpdAllocation, 'bed.room.ward.ward_name') ?: data_get($activeIpdAllocation, 'bed.bedType.type_name') ?: 'Ward';
    $bedCode = data_get($activeIpdAllocation, 'bed.bed_code') ?: '-';
    $stayDayText = '-';
    if ($isIpdActive && !empty(data_get($activeIpdAllocation, 'admission_date'))) {
        $admissionAt = \Carbon\Carbon::parse(data_get($activeIpdAllocation, 'admission_date'))->startOfDay();
        $stayDays = $admissionAt->diffInDays(now()->startOfDay()) + 1;
        $stayDayText = 'Day ' . max(1, $stayDays);
    }

    $p360HasSchemePayer = (bool) ($patient360HasSchemePayer ?? false);

    $p360PayerBadgeText = null;
    if ($isIpdActive && ! empty($activeIpdAllocation)) {
        $lbl = trim((string) data_get($activeIpdAllocation, 'payment_mode_label', ''));
        if ($lbl !== '' && strcasecmp($lbl, 'Cash') !== 0) {
            $p360PayerBadgeText = $lbl;
        } elseif (filled(data_get($activeIpdAllocation, 'tpa_id'))) {
            $tpaNm = trim((string) data_get($activeIpdAllocation, 'tpa.name', ''));
            $p360PayerBadgeText = $tpaNm !== '' ? ('Payor: ' . $tpaNm) : 'TPA / Insurance';
        } elseif ($p360HasSchemePayer) {
            $sn = trim((string) data_get($activeIpdAllocation, 'schemeType.name', ''));
            $p360PayerBadgeText = $sn !== '' ? ('Payor: ' . $sn) : 'Government scheme';
        }
    }

    $p360OpdPayerBadgeText = null;
    if (! $isIpdActive && ! empty($latestOpdVisit)) {
        $om = trim((string) data_get($latestOpdVisit, 'payment_mode', ''));
        if ($om !== '' && strcasecmp($om, 'Cash') !== 0) {
            $p360OpdPayerBadgeText = $om;
        } elseif (filled(data_get($latestOpdVisit, 'tpa_id'))) {
            $tpaNm = trim((string) data_get($latestOpdVisit, 'tpa.name', ''));
            $p360OpdPayerBadgeText = $tpaNm !== '' ? ('Payor: ' . $tpaNm) : 'TPA / Insurance';
        }
    }

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
    $bmiText = data_get($contextRecord, 'bmi');
    if (! filled($bmiText)) {
        $hM = (float) (data_get($contextRecord, 'height') ?: 0);
        $wKg = (float) (preg_replace('/[^\d.]/', '', (string) ($weightRaw ?? '')) ?: 0);
        if ($hM >= 0.5 && $hM <= 3.0 && $wKg >= 20 && $wKg <= 400) {
            $calcBmi = $wKg / ($hM * $hM);
            if ($calcBmi >= 5 && $calcBmi <= 80) {
                $bmiText = number_format($calcBmi, 1);
            }
        }
    }
    $bmiText = filled($bmiText) ? (string) $bmiText : '-';

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
        <div class="p360-patient-banner">
            <div class="p360-banner-row1">
                <div class="p360-banner-avatar">{{ $initials }}</div>
                <div class="p360-banner-patient">
                    <div class="p360-banner-name">{{ $patientName }}</div>
                    <div class="p360-banner-meta">
                        <div class="p360-meta-line">
                            <span>MRN: <strong class="p360-meta-strong">{{ $displayMrn }}</strong></span>
                            <span class="p360-meta-sep" aria-hidden="true">·</span>
                            <span>ABHA: <strong class="p360-meta-strong">{{ $displayAbha }}</strong></span>
                        </div>
                        <div class="p360-meta-line">
                            <span>{{ $ageText }}, {{ $patient->gender ?? '-' }}</span>
                            <span class="p360-meta-sep" aria-hidden="true">·</span>
                            <span>Blood: <strong class="p360-meta-strong">{{ $patient->blood_group ?: '—' }}</strong></span>
                            <span class="p360-meta-sep" aria-hidden="true">·</span>
                            <span>{{ $dateLabel }}: <strong class="p360-meta-strong">{{ $dateText }}</strong></span>
                            <span class="p360-meta-sep" aria-hidden="true">·</span>
                            <span>Consultant: <strong class="p360-meta-strong">{{ $consultantName ?: '—' }}</strong></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p360-banner-row2">
                <div class="p360-banner-badges" aria-label="Visit context">
                    @if($isIpdActive)
                        <span class="badge badge-danger p360-badge-chip">IPD — {{ $wardName }} · Bed {{ $bedCode }}</span>
                        <span class="badge badge-primary p360-badge-chip" title="{{ e($activeIpdAllocation->admission_no ?: '') }}">Adm: {{ \Illuminate\Support\Str::limit($activeIpdAllocation->admission_no ?: '—', 20, '…') }}</span>
                        <span class="badge {{ $p360VisitStatusBadgeClass }} p360-badge-chip">Status: {{ $p360VisitStatusLabel }}</span>
                        <span class="badge badge-info p360-badge-chip">LOS: {{ $stayDayText }}</span>
                        @if(filled($p360PayerBadgeText))
                            <span class="badge badge-secondary p360-badge-chip" title="Payment / payor">{{ $p360PayerBadgeText }}</span>
                        @endif
                    @else
                        <span class="badge badge-primary p360-badge-chip">OPD — Visit {{ data_get($latestOpdVisit, 'case_no') ?: '—' }}</span>
                        <span class="badge badge-warning p360-badge-chip">Token: {{ filled(data_get($latestOpdVisit, 'token_no')) ? \App\Services\OpdTokenNoService::formatForDisplay(data_get($latestOpdVisit, 'token_no')) : '—' }}</span>
                        <span class="badge {{ $p360VisitStatusBadgeClass }} p360-badge-chip">Status: {{ $p360VisitStatusLabel }}</span>
                        @if(filled($p360OpdPayerBadgeText))
                            <span class="badge badge-secondary p360-badge-chip" title="Payment / payor">{{ $p360OpdPayerBadgeText }}</span>
                        @endif
                    @endif
                </div>
                <div class="p360-banner-actions">
                    <button
                        type="button"
                        class="btn btn-primary btn-sm"
                        id="patient360NewOrderBtn"
                        data-mode="{{ $isIpdActive ? 'ipd' : 'opd' }}"
                        data-opd-id="{{ data_get($latestOpdVisit, 'id', '') }}"
                        data-allocation-id="{{ data_get($activeIpdAllocation, 'id', '') }}"
                        data-can-new-order="{{ ($canPatient360NewOrder ?? true) ? '1' : '0' }}"
                        data-block-reason="{{ e($patient360NewOrderBlockedReason ?? '') }}"
                        @if($canPatient360NewOrder ?? true)
                            data-bs-toggle="tooltip"
                            data-bs-placement="bottom"
                            data-bs-title="New order — keyboard: Alt+Shift+O"
                            aria-keyshortcuts="Alt+Shift+O"
                        @else
                            disabled
                            aria-disabled="true"
                            title="{{ e($patient360NewOrderBlockedReason ?? 'New orders are not allowed.') }}"
                        @endif
                    >+ New Order</button>
                    @if($isIpdActive && $activeIpdAllocation)
                        @can('edit-patient-management')
                            @if($p360HasSchemePayer)
                            <a
                                href="{{ route('hospital.patient-management.scheme-preauth.start', ['patient_id' => $patient->id, 'bed_allocation_id' => $activeIpdAllocation->id]) }}"
                                class="btn btn-primary btn-sm"
                            >Preauth Details</a>
                            @endif
                            <button
                                type="button"
                                class="btn btn-warning btn-sm p360-transfer-ipd-btn"
                                data-url="{{ route('hospital.ipd-patient.transfer.showform', ['allocation' => $activeIpdAllocation->id]) }}"
                            >Transfer Bed</button>
                            <button
                                type="button"
                                class="btn btn-sm p360-discharge-ipd-btn {{ ($patient360CanIpdDischarge ?? false) ? 'btn-success' : 'btn-secondary' }}"
                                data-url="{{ route('hospital.ipd-patient.discharge.showform', ['allocation' => $activeIpdAllocation->id]) }}"
                                @if(!($patient360CanIpdDischarge ?? false))
                                    disabled
                                    aria-disabled="true"
                                @endif
                                title="{{ ($patient360CanIpdDischarge ?? false) ? 'Discharge patient' : 'Clear outstanding bill before discharge' }}"
                            >{{ ($patient360CanIpdDischarge ?? false) ? 'Discharge' : 'Clear Bill To Discharge' }}</button>
                        @endcan
                    @endif
                </div>
            </div>

            <!-- Quick vitals strip -->
            <div class="flex gap-4 mt-3 flex-wrap p360-vitals-strip">
                <div class="p360-vital-cell"><div class="p360-vital-label">BP</div><div class="p360-vital-value p360-vital-value--bp">{{ $bpText }}</div></div>
                <div class="p360-vitals-rule" aria-hidden="true"></div>
                <div class="p360-vital-cell"><div class="p360-vital-label">Pulse</div><div class="p360-vital-value p360-vital-value--pulse">{{ $pulseText }}</div></div>
                <div class="p360-vitals-rule" aria-hidden="true"></div>
                <div class="p360-vital-cell"><div class="p360-vital-label">SpO2</div><div class="p360-vital-value p360-vital-value--spo2">{{ $spo2Text }}</div></div>
                <div class="p360-vitals-rule" aria-hidden="true"></div>
                <div class="p360-vital-cell"><div class="p360-vital-label">Temp</div><div class="p360-vital-value p360-vital-value--temp">{{ $tempText }}</div></div>
                <div class="p360-vitals-rule" aria-hidden="true"></div>
                <div class="p360-vital-cell"><div class="p360-vital-label">RBS</div><div class="p360-vital-value p360-vital-value--rbs">{{ $rbsText }}</div></div>
                <div class="p360-vitals-rule" aria-hidden="true"></div>
                <div class="p360-vital-cell"><div class="p360-vital-label">Weight</div><div class="p360-vital-value p360-vital-value--weight">{{ $weightText }}</div></div>
                <div class="p360-vitals-rule" aria-hidden="true"></div>
                <div class="p360-vital-cell"><div class="p360-vital-label">BMI</div><div class="p360-vital-value p360-vital-value--bmi">{{ $bmiText }}</div></div>
            </div>
        </div>

        <!-- Tab navigation -->
        <div class="p360-tab-shell">
            <div class="tab-bar p360-tab-bar">
                <button class="tab-btn active" data-tab="tabTimeline" onclick="switchEMRTab('tabTimeline',this)">📅 Timeline</button>
                <button class="tab-btn" data-tab="tabProfile" onclick="switchEMRTab('tabProfile',this)">🧾 Details</button>
                <button class="tab-btn" data-tab="tabOrders" onclick="switchEMRTab('tabOrders',this)">📋 Orders</button>
                @if($isIpdActive && $activeIpdAllocation && $p360HasSchemePayer)
                    <button class="tab-btn" data-tab="tabProcedure" onclick="switchEMRTab('tabProcedure',this)">📋Preauth Procedures</button>
                @endif
                <button class="tab-btn" data-tab="tabMeds" onclick="switchEMRTab('tabMeds',this)">💊 Medications</button>
                <button class="tab-btn" data-tab="tabNotes" onclick="switchEMRTab('tabNotes',this)">📝 Clinical Notes</button>
                <button class="tab-btn" data-tab="tabLab" onclick="switchEMRTab('tabLab',this)">🧪 Lab Results</button>
                <button class="tab-btn" data-tab="tabVitals" onclick="switchEMRTab('tabVitals',this)">📈 Vitals Chart</button>
                <button class="tab-btn" data-tab="tabHistory" onclick="switchEMRTab('tabHistory',this)">📁 History</button>
                <button class="tab-btn" data-tab="tabBilling" onclick="switchEMRTab('tabBilling',this)">💳 Billing</button>
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
                                <div class="list-item"><div class="list-item-icon p360-li-icon p360-li-icon--danger"></div><div class="list-item-body"><div class="li-title">Type 2 Diabetes Mellitus</div><div class="li-sub">ICD-10: E11.9 · Uncontrolled · Since 2018</div></div><span class="badge badge-danger">Active</span></div>
                                <div class="list-item"><div class="list-item-icon p360-li-icon p360-li-icon--warning"></div><div class="list-item-body"><div class="li-title">Essential Hypertension</div><div class="li-sub">ICD-10: I10 · Poorly controlled · Since 2020</div></div><span class="badge badge-warning">Active</span></div>
                                <div class="list-item"><div class="list-item-icon p360-li-icon p360-li-icon--primary"></div><div class="list-item-body"><div class="li-title">Dyslipidaemia</div><div class="li-sub">ICD-10: E78.5 · On statin · LDL 142</div></div><span class="badge badge-primary">Active</span></div>
                            </div>
                        </div>
                        <!-- AI -->
                        <div class="ai-insight-block">
                            <div class="ai-header">AI Clinical Copilot</div>
                            <div class="ai-body">
                                <div class="ai-item"><div class="ai-dot p360-ai-dot p360-ai-dot--alert"></div><div>HbA1c trend worsening (7.9 to 8.4%). Consider dual therapy - add Sitagliptin or refer for endocrinology review.</div></div>
                                <div class="ai-item"><div class="ai-dot"></div><div>BP target for DM+CKD is &lt;130/80 per ACC/AHA. Current 148/94 is above target - ARB preferred.</div></div>
                                <div class="ai-item"><div class="ai-dot p360-ai-dot p360-ai-dot--ok"></div><div>Kidney function stable (Cr 1.1). Continue Metformin with monitoring. eGFR estimation recommended.</div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- REGISTRATION DETAILS -->
            <div class="tab-pane" id="tabProfile">
                @php
                    $profileChronicConditions = collect($patient->chronic_conditions ?? [])->filter()->values();
                    $profileAddressParts = collect([
                        $patient->address,
                        $patient->district,
                        $patient->state,
                        $patient->pin_code,
                    ])->filter(function ($v) {
                        return filled($v);
                    })->values();

                    $profileCategoryLabel = data_get($patient, 'patientCategory.name')
                        ?: (filled($patient->category) ? $patient->category : '-');
                    $profileReligionLabel = data_get($patient, 'religion.name') ?: '-';
                @endphp
                <div class="grid-2">
                    <div class="card">
                        <div class="card-header"><div class="card-title">Identity & Demographics</div></div>
                        <div class="card-body-sm">
                            <div class="stat-row mb-3">
                                <div class="stat-item"><div class="s-label">Gender</div><div class="s-value">{{ $patient->gender ?: '-' }}</div></div>
                                <div class="stat-item"><div class="s-label">DOB</div><div class="s-value">{{ !empty($patient->date_of_birth) ? \Carbon\Carbon::parse($patient->date_of_birth)->format('d M Y') : '-' }}</div></div>
                            </div>
                            <div class="table-wrap">
                                <table>
                                    <tbody>
                                        <tr><td class="p360-profile-label-col">MRN</td><td>{{ $displayMrn }}</td></tr>
                                        <tr><td>ABHA / Ayushman Bharat ID</td><td>{{ $displayAbha }}</td></tr>
                                        <tr><td>Aadhaar Number</td><td>{{ $patient->aadhar_no ?: '-' }}</td></tr>
                                        <tr><td>Blood Group</td><td>{{ $patient->blood_group ?: '-' }}</td></tr>
                                        <tr><td>Marital Status</td><td>{{ $patient->marital_status ?: '-' }}</td></tr>
                                        <tr><td>Religion</td><td>{{ $profileReligionLabel }}</td></tr>
                                        <tr><td>Category</td><td>{{ $profileCategoryLabel }}</td></tr>
                                        <tr><td>Occupation</td><td>{{ $patient->occupation ?: '-' }}</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header"><div class="card-title">Contact & Address</div></div>
                        <div class="card-body-sm">
                            <div class="table-wrap">
                                <table>
                                    <tbody>
                                        <tr><td class="p360-profile-label-col">Primary Phone</td><td>{{ $patient->phone ?: '-' }}</td></tr>
                                        <tr><td>Alternate Phone</td><td>{{ $patient->alternate_phone ?: '-' }}</td></tr>
                                        <tr><td>Email</td><td>{{ $patient->email ?: '-' }}</td></tr>
                                        <tr><td>Address</td><td>{{ $profileAddressParts->isNotEmpty() ? $profileAddressParts->implode(', ') : '-' }}</td></tr>
                                        <tr><td>Pin Code</td><td>{{ $patient->pin_code ?: '-' }}</td></tr>
                                        <tr><td>District</td><td>{{ $patient->district ?: '-' }}</td></tr>
                                        <tr><td>State</td><td>{{ $patient->state ?: '-' }}</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header"><div class="card-title">Emergency Contact</div></div>
                        <div class="card-body-sm">
                            <div class="table-wrap">
                                <table>
                                    <tbody>
                                        <tr><td class="p360-profile-label-col">Contact Name</td><td>{{ $patient->emergency_contact_name ?: '-' }}</td></tr>
                                        <tr><td>Relation</td><td>{{ $patient->emergency_contact_relation ?: '-' }}</td></tr>
                                        <tr><td>Phone</td><td>{{ $patient->emergency_contact_phone ?: '-' }}</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header"><div class="card-title">Medical Background (Registration)</div></div>
                        <div class="card-body-sm">
                            <div class="mb-3">
                                <div class="fw-600 fs-12 mb-2">Known Allergies</div>
                                <div class="fs-12 text-muted">{{ $patient->known_allergies ?: '-' }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="fw-600 fs-12 mb-2">Chronic Conditions</div>
                                @if($profileChronicConditions->isNotEmpty())
                                    <div class="p360-allergy-tags">
                                        @foreach($profileChronicConditions as $profileCondition)
                                            <span class="badge badge-warning">{{ $profileCondition }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="fs-12 text-muted">-</div>
                                @endif
                            </div>
                            <div class="table-wrap">
                                <table>
                                    <tbody>
                                        <tr><td class="p360-profile-label-col">Past Surgical History</td><td>{{ $patient->past_surgical_history ?: '-' }}</td></tr>
                                        <tr><td>Current Medications</td><td>{{ $patient->current_medications ?: '-' }}</td></tr>
                                        <tr><td>Family History</td><td>{{ $patient->family_history ?: '-' }}</td></tr>
                                        <tr><td>Smoking Status</td><td>{{ $patient->smoking_status ?: '-' }}</td></tr>
                                        <tr><td>Alcohol Status</td><td>{{ $patient->alcohol_status ?: '-' }}</td></tr>
                                        <tr><td>Vaccination Status</td><td>{{ $patient->vaccination_status ?: '-' }}</td></tr>
                                    </tbody>
                                </table>
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
                            ? 'badge-gray'
                            : ($statusKey === 'in_progress'
                                ? 'badge-warning'
                                : (in_array($statusKey, ['cancelled', 'rejected'], true) ? 'badge-danger' : 'badge-warning'));

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
                                ? 'badge-gray'
                                : (in_array($statusKey, ['cancelled', 'rejected'], true) ? 'badge-danger' : 'badge-warning'));

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

            @if($isIpdActive && $activeIpdAllocation && $p360HasSchemePayer)
            <div class="tab-pane" id="tabProcedure">
                <div class="card">
                    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                        @php
                            $p360Preauth = $schemePreauthRegister ?? null;
                            $p360PreauthDraft = $p360Preauth && (int) $p360Preauth->status === \App\Models\PreauthRegister::STATUS_REGISTER;
                        @endphp
                        <div>
                            <div class="card-title mb-0">Scheme preauth procedures</div>
                            @if($p360Preauth)
                                <div class="text-muted small mt-1">
                                    Status: {{ $p360Preauth->status_label }}
                                    @if($p360Preauth->register_id)
                                        · Register ID: {{ $p360Preauth->register_id }}
                                    @endif
                                </div>
                            @endif
                        </div>
                        @can('edit-patient-management')
                            @if($p360Preauth && $p360PreauthDraft)
                                <a href="{{ route('hospital.patient-management.scheme-preauth.show', ['preauthRegister' => $p360Preauth->id]) }}" class="btn btn-primary btn-sm">Continue preauth</a>
                            @elseif(!$p360Preauth)
                                <a href="{{ route('hospital.patient-management.scheme-preauth.start', ['patient_id' => $patient->id, 'bed_allocation_id' => $activeIpdAllocation->id]) }}" class="btn btn-primary btn-sm">Open scheme preauth</a>
                            @endif
                        @endcan
                    </div>
                    <div class="card-body">
                        <div class="p360-tp-table-wrap">
                            <div class="table-responsive rounded-2 p360-tp-table-border">
                                <table class="table table-hover mb-0" aria-label="Scheme preauth procedures">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Speciality</th>
                                            <th>Procedure</th>
                                            <th>Stratification</th>
                                            <th>Days</th>
                                            <th>Amount</th>
                                            <th>ICD</th>
                                        </tr>
                                    </thead>
                                    <tbody id="p360ProcedureTabTableBody">
                                        @forelse(($schemePreauthProcedures ?? collect()) as $preauthProc)
                                            @php
                                                $lineTotal = (float) ($preauthProc->procedure_price ?? 0) + (float) ($preauthProc->stratification_price ?? 0);
                                            @endphp
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $preauthProc->speciality->name ?? '—' }}</td>
                                                <td>{{ $preauthProc->procedure->procedure_name ?? '—' }}</td>
                                                <td>{{ (float) ($preauthProc->stratification_price ?? 0) > 0 ? '₹' . number_format((float) $preauthProc->stratification_price, 2) : '—' }}</td>
                                                <td>{{ $preauthProc->no_of_days ?: '—' }}</td>
                                                <td>₹{{ number_format($lineTotal, 2) }}</td>
                                                <td>{{ $preauthProc->procedure->icd_code ?? '—' }}</td>
                                            </tr>
                                            @if($preauthProc->implant_id)
                                            <tr>
                                                <td>{{ $loop->iteration }}.1</td>
                                                <td>{{ $preauthProc->speciality->name ?? '—' }}</td>
                                                <td>{{ $preauthProc->implant->name ?? 'Implant' }}</td>
                                                <td>—</td>
                                                <td>Qty {{ $preauthProc->implant_qty ?? '—' }}</td>
                                                <td>₹{{ number_format((float) ($preauthProc->implant_price ?? 0), 2) }}</td>
                                                <td>—</td>
                                            </tr>
                                            @endif
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">
                                                    @if(($p360Preauth ?? null) && !($p360PreauthDraft ?? false))
                                                        No procedures on this preauth record yet, or preauth has already been submitted.
                                                    @else
                                                        No procedures added yet. Use <strong>Scheme preauth</strong> to add procedures.
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- MEDICATIONS -->
            <div class="tab-pane" id="tabMeds">
                <div class="card">
                    <div class="card-header"><div class="card-title">💊 Current Medication Sheet</div><button type="button" class="btn btn-primary btn-sm" id="patient360PrescribeBtn" data-mode="{{ $isIpdActive ? 'ipd' : 'opd' }}" data-opd-id="{{ data_get($latestOpdVisit, 'id', '') }}" data-allocation-id="{{ data_get($activeIpdAllocation, 'id', '') }}" data-can-new-order="{{ ($canPatient360NewOrder ?? true) ? '1' : '0' }}" data-block-reason="{{ e($patient360NewOrderBlockedReason ?? '') }}" @if($canPatient360NewOrder ?? true) data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Alt+Shift+P — open prescription workspace from this page. Inside modal: Alt+S / Ctrl+S Save · Alt+B Close · Alt+D Add drug · Ctrl+Enter commit row · Alt+T lab test · Alt+1–9 sections" aria-keyshortcuts="Alt+Shift+P Alt+S Alt+B Alt+D Alt+T" @else disabled aria-disabled="true" title="{{ e($patient360NewOrderBlockedReason ?? 'New orders are not allowed.') }}" @endif>+ Prescribe</button></div>
                    <div class="table-wrap">
                        <table>
                            <thead><tr><th>Drug</th><th>Dose</th><th>Route</th><th>Frequency</th><th>Duration</th><th>Prescribed By</th><th>Visit</th><th>Start Date</th><th>Status</th></tr></thead>
                            <tbody>
                                @forelse(($medicationRows ?? collect()) as $medRow)
                                    <tr>
                                        <td class="font-600">{{ $medRow['drug'] }}</td>
                                        <td>{{ $medRow['dose'] }}</td>
                                        <td>{{ $medRow['route'] }}</td>
                                        <td>{{ $medRow['frequency'] }}</td>
                                        <td>{{ $medRow['duration'] }}</td>
                                        <td>{{ $medRow['prescribed_by'] }}</td>
                                        <td>{{ $medRow['reference'] }}</td>
                                        <td>{{ !empty($medRow['started_at']) ? \Carbon\Carbon::parse($medRow['started_at'])->format('d M Y') : '-' }}</td>
                                        <td><span class="badge {{ $medRow['status_class'] }}">{{ $medRow['status_label'] }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">No prescribed medicines found for this patient.</td>
                                    </tr>
                                @endforelse
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
                            <div class="card-title">Lab Results</div>
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
                                            $resultClass = match ($flag) {
                                                'critical_low', 'critical_high', 'high' => 'p360-lab-result--critical',
                                                'low' => 'p360-lab-result--low',
                                                'normal' => 'p360-lab-result--normal',
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
                                            <td class="font-600{{ $resultClass !== '' ? ' ' . $resultClass : '' }}">{{ $labRow['result'] }}</td>
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
                                <div class="list-item{{ $radStatusKey !== 'completed' ? ' p360-rad-row-muted' : '' }}">
                                    <div class="list-item-icon p360-li-icon p360-li-icon--rad"></div>
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
                                <div class="fs-12 text-muted p360-rad-empty">No radiology reports found for this patient.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- VITALS CHART -->
            <div class="tab-pane" id="tabVitals">
                <div class="grid-2">
                    <div class="card"><div class="card-header"><div class="card-title">BP Trend</div></div><div class="card-body"><div class="chart-container p360-chart-container"><canvas id="bpChart"></canvas></div></div></div>
                    <div class="card"><div class="card-header"><div class="card-title">Blood Sugar Trend</div></div><div class="card-body"><div class="chart-container p360-chart-container"><canvas id="bsChart"></canvas></div></div></div>
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
                            <div class="stat-item"><div class="s-label">Total Charges</div><div class="s-value p360-stat-charge">Rs {{ number_format((float) ($totalCharges ?? 0), 2) }}</div></div>
                            <div class="stat-item"><div class="s-label">Paid Amount</div><div class="s-value p360-stat-paid">Rs {{ number_format((float) ($totalPaid ?? 0), 2) }}</div></div>
                            <div class="stat-item"><div class="s-label">Balance Due</div><div class="s-value p360-stat-due">Rs {{ number_format((float) ($totalDue ?? 0), 2) }}</div></div>
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
                    <div class="card-header"><div class="card-title">Clinical Notes</div><button type="button" class="btn btn-primary btn-sm" id="patient360AddNoteBtn" data-open-context="note" data-mode="{{ $isIpdActive ? 'ipd' : 'opd' }}" data-opd-id="{{ data_get($latestOpdVisit, 'id', '') }}" data-allocation-id="{{ data_get($activeIpdAllocation, 'id', '') }}" data-can-new-order="{{ ($canPatient360NewOrder ?? true) ? '1' : '0' }}" data-block-reason="{{ e($patient360NewOrderBlockedReason ?? '') }}" @if($canPatient360NewOrder ?? true) data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Alt+Shift+N — open notes (SOAP) workspace from this page. Inside modal: Alt+S / Ctrl+S Save · Alt+B Close · Alt+D / Alt+T / Ctrl+Enter · Alt+1–9" aria-keyshortcuts="Alt+Shift+N Alt+S Alt+B Alt+D Alt+T" @else disabled aria-disabled="true" title="{{ e($patient360NewOrderBlockedReason ?? 'New orders are not allowed.') }}" @endif>+ Add Note</button></div>
                    <div class="card-body">
                        @forelse(($clinicalNotes ?? collect()) as $note)
                            @php
                                $noteAt = !empty($note['noted_at']) ? \Carbon\Carbon::parse($note['noted_at']) : null;
                                $noteAtLabel = $noteAt
                                    ? ($noteAt->isToday()
                                        ? 'Today, ' . $noteAt->format('h:i A')
                                        : ($noteAt->isYesterday()
                                            ? 'Yesterday, ' . $noteAt->format('h:i A')
                                            : $noteAt->format('d M Y, h:i A')))
                                    : '-';
                            @endphp
                            <div class="p360-note-card">
                                <div class="flex justify-between mb-2">
                                    <div>
                                        <strong>{{ $note['title'] }}</strong> - {{ $note['author'] }}
                                        <span class="badge {{ $note['note_badge'] }} ms-2">{{ $note['author_role'] }}</span>
                                    </div>
                                    <div class="text-muted text-sm">{{ $noteAtLabel }}</div>
                                </div>
                                <div class="text-muted text-sm mb-2">{{ $note['context'] }}</div>
                                <p class="p360-note-body">{{ $note['body'] ?: '-' }}</p>
                            </div>
                        @empty
                            <div class="text-center text-muted p360-empty-state-pad">No clinical notes found for this patient.</div>
                        @endforelse
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
            <div class="modal-footer p360-modal-footer d-flex flex-wrap align-items-center gap-2 justify-content-end">
                <button type="button" id="p360SaveAndCompleteBtn" class="btn btn-primary px-5 d-none">Save and Complete</button>
                <button type="button" id="p360SaveBtn" class="btn btn-primary px-5 d-none" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Alt+S or Ctrl+S to save · Enter when Save is focused" aria-keyshortcuts="Alt+S Ctrl+S">Save</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('public/front/assets/css/gov.css') }}">
@include('layouts.partials.flatpickr-css')
@endpush

@push('scripts')
@include('layouts.partials.flatpickr-js')
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

function applyRequestedTabFromUrl() {
    var params = new URLSearchParams(window.location.search || '');
    var fromQuery = String(params.get('tab') || '').trim();
    var fromHash = String(window.location.hash || '').replace(/^#/, '').trim();
    var requestedTab = fromQuery || fromHash;
    if (!requestedTab) { return; }

    var pane = document.getElementById(requestedTab);
    if (!pane) { return; }

    var tabButton = document.querySelector('.tab-btn[data-tab="' + requestedTab + '"]');
    switchEMRTab(requestedTab, tabButton || null);
}

document.addEventListener('DOMContentLoaded', function () {
    applyRequestedTabFromUrl();
});

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
            careUnifiedForm:         @json(route('hospital.ipd-patient.doctor-care.unified',    ['allocation' => '__ALLOCATION__'])),
            prescriptionForm:        @json(route('hospital.ipd-patient.prescription.form',      ['allocation' => '__ALLOCATION__'])),
            prescriptionStore:       @json(route('hospital.ipd-patient.prescription.store',     ['allocation' => '__ALLOCATION__'])),
            prescriptionLoadDosages: @json(route('hospital.ipd-patient.prescription.load-dosages')),
            diagnosticShow:          @json(route('hospital.ipd-patient.diagnostics.showform',   ['allocation' => '__ALLOCATION__'])),
            diagnosticStore:         @json(route('hospital.ipd-patient.diagnostics.store',      ['allocation' => '__ALLOCATION__'])),
            notesStore:              @json(route('hospital.ipd-patient.notes.store',            ['allocation' => '__ALLOCATION__'])),
            clinicalUpdate:          @json(route('hospital.ipd-patient.clinical.update',        ['allocation' => '__ALLOCATION__']))
        }
    },
    csrf: @json(csrf_token()),
    opdVisitComplete: {
        eligible: @json(!empty($patient360OpdCanCompleteVisit)),
        url: @json($patient360OpdMarkCompletedUrl ?? ''),
    },
    ipdAllocationId: @json(($isIpdActive && $activeIpdAllocation) ? $activeIpdAllocation->id : null),
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
        bmi:          @json(data_get($contextRecord, 'bmi')),
        rbs:          @json(data_get($contextRecord, 'diabetes'))
    }
};
</script>
<script src="{{ asset('public/modules/sa/patient-360.js') }}"></script>
@endpush
