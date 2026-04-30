<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discharge Summary - {{ $allocation->admission_no }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 20px;
            font-family: "Times New Roman", serif;
            color: #111;
            background: #f5f7fa;
        }

        .print-action {
            margin-bottom: 12px;
        }

        .print-action button {
            border: 1px solid #0c4a6e;
            background: #0c4a6e;
            color: #fff;
            padding: 8px 14px;
            border-radius: 4px;
            font-size: 13px;
            cursor: pointer;
        }

        .sheet {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #cdd6df;
            box-shadow: 0 6px 16px rgba(20, 34, 46, 0.08);
            padding: 18px;
        }

        .hospital-header {
            text-align: center;
            border-bottom: 2px solid #1f2937;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }

        .hospital-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .hospital-header .subtitle {
            margin-top: 3px;
            font-size: 14px;
            letter-spacing: 2px;
            font-weight: 600;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 10px;
            font-size: 13px;
        }

        .meta-box {
            flex: 1;
            border: 1px solid #d4d4d4;
            padding: 8px;
            min-height: 36px;
        }

        .section {
            margin-top: 10px;
            border: 1px solid #d4d4d4;
        }

        .section-title {
            background: #eef2f7;
            border-bottom: 1px solid #d4d4d4;
            padding: 6px 8px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .section-body {
            padding: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .details-table td {
            border: 1px solid #e3e3e3;
            padding: 6px;
            vertical-align: top;
        }

        .label {
            width: 24%;
            font-weight: 700;
            background: #fafafa;
        }

        .notes-table th,
        .notes-table td {
            border: 1px solid #e3e3e3;
            padding: 6px;
            vertical-align: top;
        }

        .notes-table th {
            text-align: left;
            background: #f5f5f5;
            font-weight: 700;
        }

        .multiline {
            min-height: 54px;
            white-space: pre-line;
            line-height: 1.4;
        }

        .signatures {
            margin-top: 16px;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .sign-box {
            text-align: center;
            padding-top: 28px;
            border-top: 1px solid #111;
            font-size: 12px;
        }

        .footer-note {
            margin-top: 10px;
            font-size: 11px;
            color: #444;
            text-align: center;
        }

        .muted {
            color: #5f6368;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .no-print {
                display: none;
            }

            .sheet {
                border: none;
                box-shadow: none;
                max-width: 100%;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="print-action no-print">
        <button onclick="window.print()">Print Summary</button>
    </div>

    <div class="sheet">
        <div class="hospital-header">
            <h1>{{ $hospital?->name ?? 'Hospital' }}</h1>
            <div class="subtitle">DISCHARGE SUMMARY</div>
            <div class="muted" style="font-size:12px; margin-top:2px;">IPD Patient Final Clinical Document</div>
        </div>

        <div class="meta-row">
            <div class="meta-box"><strong>Admission No:</strong> {{ $allocation->admission_no ?: ('IPD-' . str_pad((string) $allocation->id, 6, '0', STR_PAD_LEFT)) }}</div>
            <div class="meta-box"><strong>Date of Admission:</strong> {{ optional($allocation->admission_date)->format('d-m-Y H:i') ?: '-' }}</div>
            <div class="meta-box"><strong>Date of Discharge:</strong> {{ optional($allocation->discharge_date)->format('d-m-Y H:i') ?: '-' }}</div>
        </div>

        <div class="section">
            <div class="section-title">Patient and Admission Details</div>
            <div class="section-body" style="padding:0;">
                <table class="details-table">
                    <tr>
                        <td class="label">Patient Name</td>
                        <td>{{ $allocation->patient?->name ?: '-' }}</td>
                        <td class="label">UHID</td>
                        <td>{{ $allocation->patient?->patient_id ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Age / Gender</td>
                        <td>{{ ($allocation->patient?->age_years ?? '-') . ' years / ' . ($allocation->patient?->gender ?? '-') }}</td>
                        <td class="label">Phone</td>
                        <td>{{ $allocation->patient?->phone ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Consultant</td>
                        <td>{{ $allocation->consultantDoctor?->full_name ?: '-' }}</td>
                        <td class="label">Department</td>
                        <td>{{ $allocation->department?->name ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Admission Type</td>
                        <td>{{ ucfirst($allocation->admission_type ?: 'N/A') }}</td>
                        <td class="label">Admission Source</td>
                        <td>{{ strtoupper($allocation->admission_source ?: 'DIRECT') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Discharge Status</td>
                        <td colspan="3">{{ ucfirst($allocation->discharge_status ?: 'N/A') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Clinical Summary</div>
            <div class="section-body" style="padding:0;">
                <table class="details-table">
                    <tr>
                        <td class="label">Reason for Admission</td>
                        <td class="multiline">{{ $allocation->admission_reason ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Provisional Diagnosis</td>
                        <td class="multiline">{{ $allocation->provisional_diagnosis ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Admission Notes</td>
                        <td class="multiline">{{ $allocation->admission_notes ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Condition at Discharge / Advice</td>
                        <td class="multiline">{{ $allocation->discharge_notes ?: '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Hospital Course and Progress Notes</div>
            <div class="section-body" style="padding:0;">
                <table class="notes-table">
                    <thead>
                        <tr>
                            <th style="width:6%;">#</th>
                            <th style="width:20%;">Date & Time</th>
                            <th style="width:18%;">Note Type</th>
                            <th>Clinical Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notes as $note)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ optional($note->noted_at)->format('d-m-Y H:i') ?: '-' }}</td>
                                <td>{{ strtoupper($note->note_type ?: '-') }}</td>
                                <td class="multiline" style="min-height:0;">{{ $note->note ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="muted" style="text-align:center;">No progress notes available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="signatures">
            <div class="sign-box">
                Treating Consultant Signature<br>
                <span class="muted">Name & Registration No.</span>
            </div>
            <div class="sign-box">
                Patient / Relative Signature<br>
                <span class="muted">Name & Relation</span>
            </div>
        </div>

        <div class="footer-note">
            This is a computer-generated discharge summary.
        </div>
    </div>

    <script>
        @if(!empty($autoprint))
            window.addEventListener('load', function () {
                window.print();
            });
        @endif
    </script>
</body>
</html>
