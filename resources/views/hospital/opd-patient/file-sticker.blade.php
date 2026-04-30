<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Sticker - {{ $opdPatient->case_no }}</title>
    <style>
        :root {
            --lw: 100mm;
            --lh: 40mm;
        }

        @page {
            size: var(--lw) var(--lh);
            margin: 0;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            background: #f5f7fb;
            color: #111;
        }

        .wrapper {
            width: 100%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 14px;
            gap: 12px;
        }

        /* ---- Main sticker card ---- */
        .sticker {
            width: var(--lw);
            height: var(--lh);
            border: 1.5px solid #222;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            page-break-inside: avoid;
            background: #fff;
        }

        .sticker:not(:last-child) {
            page-break-after: always;
        }

        /* TOP BAR — hospital name + date */
        .sticker-top {
            background: #1a3a6b;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5mm 3mm;
            flex-shrink: 0;
        }

        .sticker-top .hosp {
            font-size: 7.5pt;
            font-weight: bold;
            letter-spacing: 0.3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 70mm;
        }

        .sticker-top .date-text {
            font-size: 7pt;
            white-space: nowrap;
            opacity: 0.9;
        }

        /* BODY — patient info grid */
        .sticker-body {
            flex: 1;
            padding: 2mm 3mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Row: Label + Value */
        .info-row {
            display: flex;
            align-items: baseline;
            gap: 2mm;
            min-height: 6mm;
            border-bottom: 0.5px dashed #ccc;
            padding-bottom: 1mm;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-size: 6.5pt;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            white-space: nowrap;
            min-width: 18mm;
        }

        .info-value {
            font-size: 8.5pt;
            color: #111;
            font-weight: 600;
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Patient name row — larger */
        .name-row .info-value {
            font-size: 11pt;
            font-weight: bold;
        }

        /* Blank line placeholder (for manual writing) */
        .blank-line {
            flex: 1;
            border-bottom: 1px solid #aaa;
            min-width: 40mm;
            height: 3mm;
            display: inline-block;
        }

        /* ----- Print overrides ----- */
        @media print {
            body { background: #fff; }
            .no-print  { display: none !important; }
            .wrapper {
                padding: 0;
                gap: 0;
                min-height: auto;
                display: block;
            }
            .sticker {
                border: 1.5px solid #000;
                box-shadow: none;
                margin: 0;
            }
        }

        @media screen {
            .sticker { box-shadow: 0 4px 16px rgba(0,0,0,0.14); }
        }
    </style>
</head>
<body>

{{-- Preview bar (hidden on print) --}}
<div class="no-print" style="padding:10px 14px; background:#f0f4ff; display:flex; gap:10px; align-items:center;">
    <strong>File Sticker Preview (100 × 40 mm)</strong>
    <button onclick="window.print()"
            style="padding:6px 16px; background:#1a3a6b; color:#fff; border:none; border-radius:4px; cursor:pointer; font-size:13px;">
        Print
    </button>
    <button onclick="window.close()"
            style="padding:6px 14px; background:#888; color:#fff; border:none; border-radius:4px; cursor:pointer; font-size:13px;">
        Close
    </button>
</div>

<div class="wrapper">
    @for ($i = 0; $i < $copies; $i++)
    <div class="sticker">

        {{-- TOP BAR: hospital + date --}}
        <div class="sticker-top">
            <span class="hosp">{{ $hospital->name ?? config('app.name') }}</span>
            <span class="date-text">{{ $opdPatient->appointment_date?->format('d/m/Y') ?? now()->format('d/m/Y') }}</span>
        </div>

        {{-- BODY --}}
        <div class="sticker-body">

            {{-- Patient Name --}}
            <div class="info-row name-row">
                <span class="info-label">Patient</span>
                <span class="info-value">{{ $patient->name ?? '-' }}</span>
            </div>

            {{-- Patient No + Age/Gender in one row --}}
            <div class="info-row">
                <span class="info-label">Patient No</span>
                <span class="info-value" style="min-width:30mm;">
                    {{ $patient->patient_id ?? $opdPatient->case_no ?? '-' }}
                </span>
                <span class="info-label" style="">Age</span>
                <span class="info-value" style="min-width:0;">
                    @if($patient && $patient->age_years)
                        {{ $patient->age_years }}{{ $patient->age_months ? '.' . $patient->age_months : '' }} Yr
                        @if($patient->gender)/ {{ ucfirst($patient->gender) }}@endif
                    @endif
                </span>
            </div>

            {{-- Reference --}}
            <div class="info-row">
                <span class="info-label">Reference</span>

            </div>

            {{-- Doctor --}}
            <div class="info-row">
                <span class="info-label">Doctor</span>
                @if($doctor)
                    <span class="info-value">
                        Dr. {{ trim(($doctor->first_name ?? '') . ' ' . ($doctor->last_name ?? '')) }}
                    </span>
                @endif
            </div>

        </div>{{-- /sticker-body --}}
    </div>{{-- /sticker --}}
    @endfor
</div>

<script>
    const params = new URLSearchParams(window.location.search);
    if (params.get('autoprint') === '1') {
        window.addEventListener('load', () => setTimeout(() => window.print(), 300));
    }
</script>
</body>
</html>
