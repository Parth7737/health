<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Sticker - {{ $opdPatient->case_no }}</title>
    <style>
        :root {
            --label-width: {{ $labelWidthMm ?? 90 }}mm;
            --label-height: {{ $labelHeightMm ?? 45 }}mm;
        }

        @page {
            size: var(--label-width) var(--label-height);
            margin: 0;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fb;
            color: #111;
        }

        .sticker-wrapper {
            width: 100%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 12px;
            gap: 10px;
        }

        .sticker {
            width: var(--label-width);
            height: var(--label-height);
            border: 1px solid #333;
            padding: 4mm 5mm;
            page-break-inside: avoid;
            background: #fff;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sticker:not(:last-child) {
            page-break-after: always;
        }

        .sticker-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 1px dashed #aaa;
            padding-bottom: 2mm;
            margin-bottom: 2mm;
        }

        .hospital-name {
            font-size: 9pt;
            font-weight: bold;
            color: #1a3a6b;
            max-width: 55mm;
            line-height: 1.2;
        }

        .token-box {
            text-align: center;
            border: 1.5px solid #1a3a6b;
            border-radius: 4px;
            padding: 1mm 3mm;
            min-width: 18mm;
        }

        .token-label {
            font-size: 6pt;
            color: #555;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .token-number {
            font-size: 18pt;
            font-weight: bold;
            color: #1a3a6b;
            line-height: 1;
        }

        .patient-name {
            font-size: 12pt;
            font-weight: bold;
            color: #111;
            margin-bottom: 1mm;
        }

        .patient-meta {
            font-size: 8pt;
            color: #333;
            line-height: 1.6;
        }

        .patient-meta span {
            margin-right: 4mm;
        }

        .ref-row {
            margin-top: 2mm;
            font-size: 8pt;
            color: #444;
        }

        .sticker-footer {
            margin-top: 2mm;
            padding-top: 2mm;
            border-top: 1px dashed #aaa;
            font-size: 7.5pt;
            color: #555;
            display: flex;
            justify-content: space-between;
            gap: 2mm;
        }

        .sticker-footer span {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        @media print {
            body { background: #fff; }
            .no-print { display: none !important; }
            .sticker-wrapper {
                padding: 0;
                gap: 0;
                min-height: auto;
                display: block;
            }
            .sticker {
                margin: 0;
                border: 1px solid #000;
                box-shadow: none;
            }
        }

        @media screen {
            .sticker {
                box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
            }
        }
    </style>
</head>
<body>

<div class="no-print" style="padding:10px; background:#f0f4ff; display:flex; gap:8px; align-items:center;">
    <strong>Patient Sticker Preview ({{ $labelWidthMm ?? 90 }} x {{ $labelHeightMm ?? 45 }} mm)</strong>
    <button onclick="window.print()" style="padding:6px 16px; background:#1a3a6b; color:#fff; border:none; border-radius:4px; cursor:pointer; font-size:13px;">
        Print Sticker
    </button>
    <button onclick="window.close()" style="padding:6px 14px; background:#888; color:#fff; border:none; border-radius:4px; cursor:pointer; font-size:13px;">
        Close
    </button>
</div>

<div class="sticker-wrapper">
    @for($i = 0; $i < $copies; $i++)
    <div class="sticker">
        <div class="sticker-header">
            <div class="hospital-name">{{ $hospital->name ?? config('app.name') }}</div>
            <div class="token-box">
                <div class="token-label">Token</div>
                <div class="token-number">{{ $opdPatient->token_no ? \App\Services\OpdTokenNoService::formatShort($opdPatient->token_no) : '-' }}</div>
            </div>
        </div>

        <div class="patient-name">{{ $patient->name ?? '-' }}</div>

        <div class="patient-meta">
            <span>
                @if($patient->age_years)
                    Age: {{ $patient->age_years }}{{ $patient->age_months ? '.' . $patient->age_months : '' }} Yr
                @endif
            </span>
            <span>
                @if($patient->gender)
                    {{ $patient->gender }}
                @endif
            </span>
            @if($patient->patient_id)
                <span>ID: {{ $patient->patient_id }}</span>
            @endif
        </div>

        @if($patient->guardian_name)
            <div class="ref-row">D/o, S/o, W/o: <strong>{{ $patient->guardian_name }}</strong></div>
        @endif

        <div class="sticker-footer">
            <span>Case: {{ $opdPatient->case_no }}</span>
            <span>{{ \Carbon\Carbon::parse($opdPatient->appointment_date)->format('d-m-Y') }}</span>
            @if($doctor)
                <span>Dr. {{ trim($doctor->first_name . ' ' . $doctor->last_name) }}</span>
            @endif
        </div>
    </div>
    @endfor
</div>

<script>
    // Auto-print on load if URL has ?autoprint=1
    const params = new URLSearchParams(window.location.search);
    if (params.get('autoprint') === '1') {
        window.addEventListener('load', () => { setTimeout(() => window.print(), 400); });
    }
</script>
</body>
</html>
