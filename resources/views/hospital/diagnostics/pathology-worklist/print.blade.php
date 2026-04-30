<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pathology Report — {{ $item->test_name }}</title>
    <style>
        /* ─── Base ─────────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #1a2a3a; background: #ecf0f6; }

        /* ─── Screen Toolbar ────────────────────────────────────── */
        .pdf-toolbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 9999;
            background: #1565c0; color: #fff;
            display: flex; align-items: center; justify-content: space-between;
            padding: 10px 24px; box-shadow: 0 2px 8px rgba(0,0,0,.25);
        }
        .pdf-toolbar-title { font-size: 14px; font-weight: 700; letter-spacing: .3px; }
        .pdf-toolbar-meta  { font-size: 12px; opacity: .85; }
        .pdf-toolbar-actions { display: flex; gap: 10px; }
        .btn-pdf {
            background: #fff; color: #1565c0; border: none; padding: 7px 20px;
            border-radius: 4px; font-size: 13px; font-weight: 700; cursor: pointer;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-pdf:hover { background: #e3eeff; }
        .btn-close-tab {
            background: rgba(255,255,255,.15); color: #fff; border: 1px solid rgba(255,255,255,.4);
            padding: 7px 16px; border-radius: 4px; font-size: 13px; cursor: pointer;
        }

        /* ─── A4 Sheet ──────────────────────────────────────────── */
        .page-wrap { padding: 66px 24px 24px; }
        .sheet {
            width: 210mm; min-height: 297mm;
            margin: 0 auto; background: #fff;
            box-shadow: 0 0 24px rgba(0,0,0,.12);
        }

        /* ─── Header Banner (template image) ───────────────────── */
        .hdr-banner img { display: block; width: 100%; max-height: 110px; object-fit: cover; }

        /* ─── Hospital Top Bar ──────────────────────────────────── */
        .hosp-bar {
            display: flex; justify-content: space-between; align-items: center;
            padding: 12px 20px; border-bottom: 2px solid #1565c0;
            background: #f4f8ff;
        }
        .hosp-brand { display: flex; align-items: center; gap: 10px; }
        .hosp-logo { width: 52px; height: 52px; border-radius: 8px; object-fit: cover; }
        .hosp-name { font-size: 18px; font-weight: 800; color: #0f4c81; line-height: 1.2; }
        .hosp-sub  { font-size: 10px; color: #5d7285; margin-top: 2px; line-height: 1.5; }
        .hosp-right { text-align: right; font-size: 10px; color: #5d7285; line-height: 1.7; }
        .hosp-right strong { font-size: 12px; color: #1a2a3a; }

        /* ─── Report Title Band ─────────────────────────────────── */
        .report-band {
            background: linear-gradient(135deg, #0f4c81 0%, #1976d2 100%);
            color: #fff; padding: 10px 20px;
            display: flex; justify-content: space-between; align-items: center;
        }
        .report-band h1 { font-size: 16px; font-weight: 800; letter-spacing: .5px; }
        .report-band .order-info { font-size: 10px; opacity: .9; text-align: right; line-height: 1.6; }
        .status-pill {
            display: inline-block; padding: 2px 10px; border-radius: 999px;
            font-size: 10px; font-weight: 700; text-transform: uppercase; margin-top: 4px;
        }
        .status-completed  { background: #c8e6c9; color: #2e7d32; }
        .status-inprogress { background: #fff9c4; color: #f57f17; }
        .status-pending    { background: #ffe0b2; color: #e65100; }

        /* ─── Patient Details Grid ──────────────────────────────── */
        .section { padding: 12px 20px; }
        .section-title {
            font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px;
            color: #1565c0; border-bottom: 1px solid #dce8f8; padding-bottom: 4px; margin-bottom: 8px;
        }
        .info-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 6px 10px; }
        .info-label { font-size: 9px; text-transform: uppercase; letter-spacing: .6px; color: #7a93aa; display: block; }
        .info-value { font-size: 11px; font-weight: 700; color: #1a2a3a; display: block; margin-top: 1px; }

        /* ─── Critical Alert ────────────────────────────────────── */
        .critical-alert {
            background: #fff5f5; border: 1.5px solid #f44336; border-radius: 5px;
            padding: 6px 12px; margin: 8px 20px; font-size: 11px; font-weight: 700;
            color: #c62828; display: flex; align-items: center; gap: 6px;
        }

        /* ─── Results Table ─────────────────────────────────────── */
        .results-section { padding: 0 20px 12px; }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #16324f; }
        thead th { color: #fff; padding: 7px 8px; font-size: 10px; text-transform: uppercase; letter-spacing: .5px; text-align: left; }
        tbody td { padding: 6px 8px; border-bottom: 1px solid #e8eef5; font-size: 11px; vertical-align: middle; }
        tbody tr:last-child td { border-bottom: none; }
        .row-normal { background: #fff; }
        .row-low, .row-high { background: #fffbf0; }
        .row-crit { background: #fff5f5; }
        .flag-badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 9.5px; font-weight: 800; white-space: nowrap; }
        .flag-normal { background: #e8f5e9; color: #2e7d32; }
        .flag-low, .flag-high { background: #fff3e0; color: #ef6c00; }
        .flag-crit { background: #ffebee; color: #c62828; }
        .flag-none { color: #aaa; }
        .val-normal   { color: #1a2a3a; }
        .val-abnormal { color: #ef6c00; font-weight: 800; }
        .val-critical { color: #c62828; font-weight: 800; }
        .param-name { font-weight: 700; font-size: 11.5px; }

        /* ─── Remarks ───────────────────────────────────────────── */
        .remarks-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin: 8px 20px 0; }
        .remarks-box { border: 1px solid #d8e2ee; border-radius: 6px; padding: 8px 12px; background: #fbfdff; }
        .remarks-box h4 { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #0f4c81; border-bottom: 1px solid #e4ebf2; padding-bottom: 4px; margin-bottom: 6px; }
        .remarks-box p  { font-size: 11px; line-height: 1.6; white-space: pre-line; min-height: 24px; }
        .narrative { margin: 8px 20px 0; padding: 8px 12px; border: 1px solid #d8e2ee; border-radius: 6px; background: #fbfdff; }
        .narrative h3 { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #0f4c81; margin-bottom: 6px; }
        .narrative p  { font-size: 11px; line-height: 1.6; white-space: pre-line; }
        .tmpl-footer  { margin: 10px 20px 0; padding: 8px 12px; border: 1px solid #dbe4ef; border-radius: 6px; background: #f9fbff; font-size: 11px; color: #334155; line-height: 1.6; }

        /* ─── Page Footer ───────────────────────────────────────── */
        .page-footer {
            padding: 12px 20px 20px; display: flex; justify-content: space-between; align-items: flex-end;
            border-top: 1px dashed #c0d0e0; margin-top: 12px; font-size: 10px; color: #6a7f95; line-height: 1.7;
        }
        .sig-wrap { display: flex; gap: 50px; }
        .sig { text-align: center; min-width: 130px; }
        .sig-line { border-top: 1px solid #8aa0b8; padding-top: 4px; margin-top: 36px; font-size: 10px; }

        /* ─── Print ─────────────────────────────────────────────── */
        @media print {
            body { background: #fff !important; }
            .pdf-toolbar { display: none !important; }
            .page-wrap { padding: 0 !important; }
            .sheet { width: 100% !important; min-height: 0; box-shadow: none !important; border: none !important; margin: 0 !important; }
            @page { size: A4 portrait; margin: 10mm 12mm; }
        }
    </style>
</head>
<body>

@php
    $patient      = $item->order->patient;
    $order        = $item->order;
    $visit        = $order->visitable;
    $payDue       = max(0,
        (float)($item->patientCharge->amount   ?? $item->standard_charge ?? 0)
      - (float)($item->patientCharge->paid_amount ?? $item->paid_amount  ?? 0)
    );
    $logo = $hospital?->image
        ? asset('public/storage/' . $hospital->image)
        : asset('images/logo.png');
    $templateHeader = !empty($printTemplate?->header_image)
        ? asset('public/storage/' . $printTemplate->header_image)
        : null;
    $addressLine = trim(implode(', ', array_filter([
        $hospital?->address, $hospital?->city, $hospital?->pincode,
    ])));
    $phone = implode(' | ', array_filter([$hospital?->phone, $hospital?->email]));

    $patName     = $patient->name ?? '-';
    $patAge      = collect([
                       $patient->age_years  ? $patient->age_years.'y'   : null,
                       $patient->age_months ? $patient->age_months.'mo' : null,
                   ])->filter()->join(' ') ?: '-';
    $patGender   = ucfirst($patient->gender ?? '-');
    $patPhone    = $patient->phone ?? '-';
    $patMrn      = $patient->mrn ?? $patient->patient_id ?? '-';
    $patBlood    = $patient->blood_group ?? '-';
    $caseNo      = optional($visit)->case_no ?? '-';
    $refDoc      = optional($order)->referred_by ?? optional($order)->doctor?->name ?? '-';
    $collectedAt = optional($item->collected_at ?? $item->created_at)->format('d-m-Y h:i A');
    $reportedAt  = optional($item->reported_at)->format('d-m-Y h:i A') ?? '-';

    $statusClass = match($item->status) {
        'completed'   => 'status-completed',
        'in_progress' => 'status-inprogress',
        default       => 'status-pending',
    };
    $hasCritical = $item->parameters->contains(
        fn($p) => in_array($p->result_flag, ['critical_low','critical_high'])
    );
@endphp

{{-- Screen toolbar --}}
<div class="pdf-toolbar">
    <div>
        <div class="pdf-toolbar-title">📋 Pathology Report — {{ $item->test_name }}</div>
        <div class="pdf-toolbar-meta">Patient: {{ $patName }} &nbsp;|&nbsp; Report #{{ $item->id }} &nbsp;|&nbsp; {{ now()->format('d M Y') }}</div>
    </div>
    <div class="pdf-toolbar-actions">
        <button class="btn-pdf" onclick="window.print()">🖨 Print / Save as PDF</button>
        <button class="btn-close-tab" onclick="window.close()">✕ Close</button>
    </div>
</div>

<div class="page-wrap">
<div class="sheet">

    @isset($templateHeader)
        <div class="hdr-banner"><img src="{{ $templateHeader }}" alt="Header"></div>
    @endisset

    <div class="hosp-bar">
        <div class="hosp-brand">
            <img src="{{ $logo }}" class="hosp-logo" alt="Logo">
            <div>
                <div class="hosp-name">{{ $hospital?->name ?? config('app.name') }}</div>
                <div class="hosp-sub">
                    @if($addressLine) {{ $addressLine }}<br>@endif
                    @if($phone) {{ $phone }}<br>@endif
                    Pathology Department
                </div>
            </div>
        </div>
        <div class="hosp-right">
            <strong>LABORATORY REPORT</strong><br>
            Report No: <strong>#{{ $item->id }}</strong><br>
            Date: {{ optional($item->created_at)->format('d-m-Y') }}<br>
            Order: <strong>{{ $order->order_no ?? '-' }}</strong>
        </div>
    </div>

    <div class="report-band">
        <h1>{{ strtoupper($item->test_name) }}</h1>
        <div class="order-info">
            Collected: {{ $collectedAt }}<br>
            Reported: {{ $reportedAt }}<br>
            <span class="status-pill {{ $statusClass }}">{{ ucwords(str_replace('_', ' ', $item->status)) }}</span>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Patient Information</div>
        <div class="info-grid">
            <div><span class="info-label">Patient Name</span><span class="info-value">{{ $patName }}</span></div>
            <div><span class="info-label">MRN / Patient ID</span><span class="info-value">{{ $patMrn }}</span></div>
            <div><span class="info-label">Age</span><span class="info-value">{{ $patAge }}</span></div>
            <div><span class="info-label">Gender</span><span class="info-value">{{ $patGender }}</span></div>
            <div><span class="info-label">Blood Group</span><span class="info-value">{{ $patBlood }}</span></div>
            <div><span class="info-label">Phone</span><span class="info-value">{{ $patPhone }}</span></div>
            <div><span class="info-label">Case No / Visit</span><span class="info-value">{{ $caseNo }}</span></div>
            <div><span class="info-label">Referred By</span><span class="info-value">{{ $refDoc }}</span></div>
        </div>
    </div>

    @if($hasCritical)
        <div class="critical-alert">⚠ CRITICAL VALUES PRESENT — Immediate clinical attention required.</div>
    @endif

    <div class="results-section">
        <div class="section-title" style="margin: 0 0 6px;">Test Results</div>
        <table>
            <thead>
                <tr>
                    <th style="width:32%">Parameter</th>
                    <th style="width:14%">Result</th>
                    <th style="width:10%">Unit</th>
                    <th style="width:22%">Normal Range</th>
                    <th style="width:12%">Flag</th>
                    <!-- <th style="width:10%">Method</th> -->
                </tr>
            </thead>
            <tbody>
            @forelse($item->parameters as $parameter)
                @php
                    $def       = $parameter->parameterable;
                    $minVal    = $def?->min_value ?? null;
                    $maxVal    = $def?->max_value ?? null;
                    $unitName  = $def?->unit?->name ?? ($parameter->unit_name ?? '-');
                    $method    = $def?->method ?? '-';
                    $rangeText = $parameter->normal_range ?? '-';
                    if ($minVal !== null && $maxVal !== null) {
                        $rangeText = number_format((float)$minVal, 2) . ' – ' . number_format((float)$maxVal, 2);
                    }
                    $rowClass  = 'row-normal'; $valClass = 'val-normal';
                    $flagLabel = '—'; $flagClass = 'flag-none';
                    switch ($parameter->result_flag) {
                        case 'critical_low':
                            $rowClass='row-crit'; $valClass='val-critical'; $flagLabel='↓↓ Critical Low';  $flagClass='flag-badge flag-crit'; break;
                        case 'critical_high':
                            $rowClass='row-crit'; $valClass='val-critical'; $flagLabel='↑↑ Critical High'; $flagClass='flag-badge flag-crit'; break;
                        case 'low':
                            $rowClass='row-low';  $valClass='val-abnormal'; $flagLabel='↓ Low';  $flagClass='flag-badge flag-low'; break;
                        case 'high':
                            $rowClass='row-high'; $valClass='val-abnormal'; $flagLabel='↑ High'; $flagClass='flag-badge flag-high'; break;
                        case 'normal':
                            $flagLabel='Normal'; $flagClass='flag-badge flag-normal'; break;
                    }
                @endphp
                <tr class="{{ $rowClass }}">
                    <td class="param-name">{{ $parameter->parameter_name }}</td>
                    <td class="{{ $valClass }}" style="font-size:12px;">{{ filled($parameter->result_value) ? $parameter->result_value : '—' }}</td>
                    <td style="color:#666;">{{ $unitName }}</td>
                    <td style="color:#555; font-size:10.5px;">{{ $rangeText }}</td>
                    <td>
                        @if(filled($parameter->result_value))
                            <span class="{{ $flagClass }}">{{ $flagLabel }}</span>
                        @else
                            <span class="flag-none">—</span>
                        @endif
                    </td>
                  
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center; padding:16px; color:#888;">No parameters found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if(filled($item->technician_remarks) || filled($item->pathologist_comment))
        <div class="remarks-grid">
            @if(filled($item->technician_remarks))
                <div class="remarks-box">
                    <h4>🔬 Technician Remarks</h4>
                    <p>{{ $item->technician_remarks }}</p>
                </div>
            @endif
            @if(filled($item->pathologist_comment))
                <div class="remarks-box">
                    <h4>🩺 Pathologist Comment</h4>
                    <p>{{ $item->pathologist_comment }}</p>
                </div>
            @endif
        </div>
    @endif

    @if(filled($item->report_summary))
        <div class="narrative"><h3>Summary</h3><p>{{ $item->report_summary }}</p></div>
    @endif
    @if(filled($item->report_text))
        <div class="narrative"><h3>Notes</h3><p>{{ $item->report_text }}</p></div>
    @endif

    @if(!empty($printTemplate?->footer_text))
        <div class="tmpl-footer">{!! nl2br(e($printTemplate->footer_text)) !!}</div>
    @endif

    <div class="page-footer">
        <div>
            Printed on: {{ now()->format('d-m-Y h:i A') }}<br>
            Reported at: {{ $reportedAt }}<br>
            Status: <strong>{{ ucwords(str_replace('_',' ',$item->status)) }}</strong>
        </div>
        <div class="sig-wrap">
            <div class="sig"><div class="sig-line">Lab Technician</div></div>
            <div class="sig"><div class="sig-line">Pathologist / Consultant</div></div>
        </div>
    </div>

</div>
</div>

</body>
</html>
