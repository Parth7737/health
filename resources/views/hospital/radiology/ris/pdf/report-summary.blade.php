@php
    $patient = $item->order?->patient;
    $order = $item->order;
    $visit = $order?->visitable;
    $radiologist = $item->reportRadiologist ?? null;

    $logoData = null;
    if ($hospital?->image) {
        $logoPath = public_path('storage/' . ltrim($hospital->image, '/'));
        if (is_file($logoPath)) {
            $mime = @mime_content_type($logoPath) ?: 'image/png';
            $logoData = 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($logoPath));
        }
    }

    $headerData = null;
    if (! empty($printTemplate?->header_image)) {
        $hdrPath = public_path('storage/' . ltrim($printTemplate->header_image, '/'));
        if (is_file($hdrPath)) {
            $mime = @mime_content_type($hdrPath) ?: 'image/png';
            $headerData = 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($hdrPath));
        }
    }

    $addressLine = trim(implode(', ', array_filter([
        $hospital?->address,
        $hospital?->city,
        $hospital?->pincode,
    ])));
    $phone = implode(' | ', array_filter([$hospital?->phone, $hospital?->email]));

    $patName = $patient->name ?? '—';
    $patMrn = $patient->mrn ?? $patient->patient_id ?? '—';
    $patAge = filled($patient?->age) ? (string) $patient->age : '—';
    $patGender = filled($patient?->gender) ? ucfirst((string) $patient->gender) : '—';
    $caseNo = optional($visit)->case_no ?? '—';
    $refDoc = optional($order?->orderedByUser)->name ?? '—';
    $reportedAt = optional($item->reported_at)->format('d-m-Y H:i') ?? '—';
    $accession = (string) ($order?->order_no ?? ('RAD-' . $item->id));

    $clinicalHtml = \App\Support\SafeReportHtml::sanitize($item->clinical_indication);
    $findingsHtml = \App\Support\SafeReportHtml::sanitize($item->report_text);
    $impressionHtml = \App\Support\SafeReportHtml::sanitize($item->report_impression);
    $summaryHtml = \App\Support\SafeReportHtml::sanitize($item->report_summary);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Radiology report — {{ $accession }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, Helvetica, Arial, sans-serif; font-size: 11px; color: #1a2a3a; margin: 0; padding: 0; }
        .hdr-banner img { display: block; width: 100%; max-height: 100px; object-fit: cover; }
        .hosp-bar { width: 100%; border-bottom: 2px solid #1565c0; background: #f4f8ff; padding: 10px 14px; }
        .hosp-bar-inner { width: 100%; border-collapse: collapse; }
        .hosp-bar-inner td { vertical-align: middle; padding: 4px 6px; }
        .hosp-logo { width: 48px; height: 48px; border-radius: 6px; }
        .hosp-name { font-size: 15px; font-weight: bold; color: #0f4c81; }
        .hosp-sub { font-size: 9px; color: #5d7285; line-height: 1.45; margin-top: 2px; }
        .hosp-right { font-size: 9px; color: #5d7285; text-align: right; line-height: 1.5; }
        .hosp-right strong { font-size: 10px; color: #1a2a3a; }
        .report-band { width: 100%; background: #1565c0; color: #fff; padding: 8px 14px; }
        .report-band-inner { width: 100%; border-collapse: collapse; }
        .report-band-inner td { vertical-align: middle; padding: 2px 0; }
        .report-band h1 { font-size: 14px; margin: 0; font-weight: bold; letter-spacing: 0.3px; }
        .report-band .meta { font-size: 9px; opacity: 0.95; text-align: right; line-height: 1.5; }
        .pill { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 8px; font-weight: bold; background: #c8e6c9; color: #2e7d32; margin-top: 4px; }
        .section { padding: 10px 14px 6px; }
        .section-title { font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.6px; color: #1565c0; border-bottom: 1px solid #dce8f8; padding-bottom: 3px; margin-bottom: 6px; }
        .info-grid { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        .info-grid td { width: 25%; vertical-align: top; padding: 4px 6px; border: 1px solid #e4ebf2; background: #fbfdff; }
        .info-label { font-size: 8px; text-transform: uppercase; color: #7a93aa; display: block; margin-bottom: 2px; }
        .info-value { font-size: 10px; font-weight: bold; color: #1a2a3a; }
        .narrative { margin: 6px 14px 0; padding: 8px 10px; border: 1px solid #d8e2ee; border-radius: 4px; background: #fbfdff; }
        .narrative h3 { font-size: 9px; font-weight: bold; text-transform: uppercase; color: #0f4c81; margin: 0 0 6px; padding-bottom: 3px; border-bottom: 1px solid #e4ebf2; }
        .html-body { font-size: 10px; line-height: 1.55; color: #1a2a3a; }
        .html-body p { margin: 0 0 5px; }
        .html-body ul, .html-body ol { margin: 4px 0 6px; padding-left: 18px; }
        .html-body table { border-collapse: collapse; width: 100%; margin: 6px 0; font-size: 9px; }
        .html-body th, .html-body td { border: 1px solid #cfd8e6; padding: 4px 6px; vertical-align: top; }
        .html-body th { background: #eef4fb; font-weight: bold; }
        .plain { font-size: 10px; line-height: 1.55; white-space: pre-wrap; margin: 0; }
        .tmpl-footer { margin: 8px 14px 0; padding: 8px 10px; border: 1px solid #dbe4ef; border-radius: 4px; background: #f9fbff; font-size: 10px; color: #334155; line-height: 1.5; }
        .page-footer { width: 100%; border-top: 1px dashed #c0d0e0; margin-top: 12px; padding: 10px 14px 14px; font-size: 9px; color: #6a7f95; }
        .page-footer-inner { width: 100%; border-collapse: collapse; }
        .page-footer-inner td { vertical-align: bottom; }
        .sig-line { border-top: 1px solid #8aa0b8; padding-top: 4px; margin-top: 32px; font-size: 9px; text-align: center; min-width: 140px; }
        .muted { color: #888; font-style: italic; }
    </style>
</head>
<body>

@if($headerData)
    <div class="hdr-banner"><img src="{{ $headerData }}" alt="Header"></div>
@endif

<table class="hosp-bar" cellpadding="0" cellspacing="0"><tr><td>
    <table class="hosp-bar-inner" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width:56px;">
                @if($logoData)
                    <img src="{{ $logoData }}" class="hosp-logo" alt="">
                @endif
            </td>
            <td>
                <div class="hosp-name">{{ $hospital?->name ?? config('app.name') }}</div>
                <div class="hosp-sub">
                    @if($addressLine !== ''){{ $addressLine }}<br>@endif
                    @if($phone !== ''){{ $phone }}<br>@endif
                    Radiology Department
                </div>
            </td>
            <td class="hosp-right" style="width:38%;">
                <strong>IMAGING REPORT</strong><br>
                Report ID: <strong>#{{ $item->id }}</strong><br>
                Accession: <strong>{{ $accession }}</strong><br>
                Reported: <strong>{{ $reportedAt }}</strong><br>
                <span class="pill">COMPLETED</span>
            </td>
        </tr>
    </table>
</td></tr></table>

<table class="report-band" cellpadding="0" cellspacing="0"><tr><td>
    <table class="report-band-inner" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width:62%;"><h1>{{ strtoupper($item->test_name ?? 'Radiology study') }}</h1></td>
            <td class="meta">
                Modality: {{ $item->category_name ?? '—' }}<br>
                Category: {{ $item->report_category ?? '—' }}<br>
                Ordered: {{ $item->created_at?->format('d-m-Y H:i') ?? '—' }}
            </td>
        </tr>
    </table>
</td></tr></table>

<div class="section">
    <div class="section-title">Patient information</div>
    <table class="info-grid" cellspacing="0" cellpadding="0">
        <tr>
            <td><span class="info-label">Patient</span><span class="info-value">{{ $patName }}</span></td>
            <td><span class="info-label">MRN / ID</span><span class="info-value">{{ $patMrn }}</span></td>
            <td><span class="info-label">Age</span><span class="info-value">{{ $patAge }}</span></td>
            <td><span class="info-label">Gender</span><span class="info-value">{{ $patGender }}</span></td>
        </tr>
        <tr>
            <td><span class="info-label">Visit / Case</span><span class="info-value">{{ $caseNo }}</span></td>
            <td colspan="2"><span class="info-label">Referred by</span><span class="info-value">{{ $refDoc }}</span></td>
            <td><span class="info-label">Radiologist</span><span class="info-value">{{ $radiologist?->name ?? '—' }}</span></td>
        </tr>
    </table>
</div>

<div class="narrative">
    <h3>Clinical indication</h3>
    <div class="html-body">
        @if($clinicalHtml === '')
            <span class="muted">—</span>
        @else
            {!! $clinicalHtml !!}
        @endif
    </div>
</div>

<div class="narrative">
    <h3>Technique</h3>
    @if(filled($item->report_technique))
        <p class="plain">{{ $item->report_technique }}</p>
    @else
        <span class="muted">—</span>
    @endif
</div>

@if($item->parameters->isNotEmpty())
    <div class="narrative">
        <h3>Parameters / measurements</h3>
        <table class="html-body" style="width:100%; font-size:9px;">
            <thead>
                <tr>
                    <th style="width:30%;">Parameter</th>
                    <th style="width:12%;">Result</th>
                    <th style="width:10%;">Unit</th>
                    <th style="width:20%;">Normal range</th>
                    <th style="width:12%;">Flag</th>
                    <th style="width:16%;">Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($item->parameters->sortBy('sort_order') as $parameter)
                    @php
                        $def = $parameter->parameterable;
                        $minVal = $def?->min_value ?? null;
                        $maxVal = $def?->max_value ?? null;
                        $unitName = $def?->unit?->name ?? ($parameter->unit_name ?? '—');
                        $rangeText = $parameter->normal_range ?? '—';
                        if ($minVal !== null && $maxVal !== null) {
                            $rangeText = number_format((float) $minVal, 2) . ' – ' . number_format((float) $maxVal, 2);
                        }
                        $flagLabel = '—';
                        if ($parameter->result_flag) {
                            $fc = [
                                'normal' => 'Normal',
                                'low' => 'Low',
                                'high' => 'High',
                                'critical_low' => 'Critical low',
                                'critical_high' => 'Critical high',
                            ];
                            $flagLabel = $fc[$parameter->result_flag] ?? $parameter->result_flag;
                        }
                    @endphp
                    <tr>
                        <td>{{ $parameter->parameter_name }}</td>
                        <td>{{ filled($parameter->result_value) ? $parameter->result_value : '—' }}</td>
                        <td>{{ $unitName }}</td>
                        <td>{{ $rangeText }}</td>
                        <td>{{ $flagLabel }}</td>
                        <td>{{ filled($parameter->remarks) ? $parameter->remarks : '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

<div class="narrative">
    <h3>Findings</h3>
    <div class="html-body">
        @if($findingsHtml === '')
            <span class="muted">—</span>
        @else
            {!! $findingsHtml !!}
        @endif
    </div>
</div>

<div class="narrative">
    <h3>Impression / diagnosis</h3>
    <div class="html-body">
        @if($impressionHtml === '')
            <span class="muted">—</span>
        @else
            {!! $impressionHtml !!}
        @endif
    </div>
</div>

@if($summaryHtml !== '')
    <div class="narrative">
        <h3>Summary</h3>
        <div class="html-body">{!! $summaryHtml !!}</div>
    </div>
@endif

@if(!empty($printTemplate?->footer_text))
    <div class="tmpl-footer">{!! nl2br(e($printTemplate->footer_text)) !!}</div>
@endif

<table class="page-footer" cellpadding="0" cellspacing="0"><tr><td>
    <table class="page-footer-inner" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width:48%;">
                Printed: {{ now()->format('d-m-Y H:i') }}<br>
                Status: <strong>Completed</strong><br>
                {{ $hospital?->name ?? '' }}
            </td>
            <td style="width:26%;">
                <div class="sig-line">Radiologist</div>
                <div style="text-align:center;font-size:9px;margin-top:2px;">{{ $radiologist?->name ?? '' }}</div>
            </td>
            <td style="width:26%;">
                <div class="sig-line">Authorized signature</div>
            </td>
        </tr>
    </table>
</td></tr></table>

</body>
</html>
