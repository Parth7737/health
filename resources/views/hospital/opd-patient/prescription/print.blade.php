<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Prescription #{{ $prescription->id }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            background: #f5f7fb;
            color: #1f2937;
        }

        .sheet {
            max-width: 1100px;
            margin: 20px auto;
            background: #fff;
            border: 1px solid #dbe4ef;
            border-radius: 10px;
            padding: 18px 22px;
        }

        .top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 10px;
            margin-bottom: 14px;
            gap: 20px;
        }

        .brand-wrap {
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .brand-logo {
            width: 72px;
            height: 72px;
            border-radius: 14px;
            object-fit: cover;
            border: 1px solid #dbe4ef;
            background: #fff;
        }

        .brand {
            font-size: 28px;
            font-weight: 700;
            color: #0f4c81;
            letter-spacing: 0.6px;
        }

        .brand-subtitle {
            margin-top: 4px;
            font-size: 13px;
            color: #6b7280;
            line-height: 1.5;
        }

        .print-header-banner {
            width: 100%;
            border: 1px solid #dbe4ef;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 14px;
            background: #fff;
        }

        .print-header-banner img {
            display: block;
            width: 100%;
            max-height: 150px;
            object-fit: cover;
        }

        .meta {
            text-align: right;
            font-size: 14px;
            line-height: 1.5;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 12px;
        }

        .box {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            font-size: 14px;
            line-height: 1.7;
        }

        .note {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px 12px;
            margin-bottom: 12px;
            font-size: 14px;
            line-height: 1.6;
        }

        .note p {
            margin: 0 0 8px;
        }

        .note p:last-child {
            margin-bottom: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th, td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f0f6ff;
        }

        .footer {
            margin-top: 16px;
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: #4b5563;
        }

        .template-footer {
            margin-top: 12px;
            border: 1px solid #dbe4ef;
            border-radius: 8px;
            background: #f9fbff;
            padding: 10px 12px;
            font-size: 13px;
            color: #334155;
            line-height: 1.6;
        }

        @media print {
            body {
                background: #fff;
            }

            .sheet {
                border: none;
                margin: 0;
                max-width: 100%;
                border-radius: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    @php
        $logo = $hospital?->image ? asset('public/storage/' . $hospital->image) : asset('images/logo.png');
        $templateHeader = !empty($printTemplate?->header_image)
            ? asset('public/storage/' . $printTemplate->header_image)
            : null;
        $addressLine = trim(implode(', ', array_filter([
            $hospital?->address,
            $hospital?->city,
            $hospital?->pincode,
        ])));
    @endphp
    <div class="sheet">
        @if($templateHeader)
            <div class="print-header-banner">
                <img src="{{ $templateHeader }}" alt="Prescription Header">
            </div>
        @endif

        <div class="top">
            <div class="brand-wrap">
                <img src="{{ $logo }}" alt="Hospital Logo" class="brand-logo">
                <div>
                    <div class="brand">{{ $hospital?->name ?? config('app.name') }}</div>
                    <div class="brand-subtitle">
                        @if($addressLine !== '')
                            {{ $addressLine }}<br>
                        @endif
                    </div>
                </div>
            </div>
            <div class="meta">
                <div><strong>Prescription #{{ $prescription->id }}</strong></div>
                <div>Date: {{ optional($prescription->opdPatient?->appointment_date)->format('d-m-Y') ?? '-' }}</div>
                <div>Case No: {{ $prescription->opdPatient?->case_no ?? '-' }}</div>
            </div>
        </div>

        <div class="grid">
            <div class="box">
                <div><strong>Patient:</strong> {{ $prescription->patient?->name ?? '-' }}</div>
                <div><strong>Patient ID:</strong> {{ $prescription->patient?->patient_id ?? '-' }}</div>
                <div><strong>Gender:</strong> {{ $prescription->patient?->gender ?? '-' }}</div>
                <div><strong>Age:</strong> {{ $prescription->patient?->age_years ?? 0 }} Year {{ $prescription->patient?->age_months ?? 0 }} Month</div>
                <div><strong>Phone:</strong> {{ $prescription->patient?->phone ?? '-' }}</div>
                <div><strong>Email:</strong> {{ $prescription->patient?->email ?? '-' }}</div>
            </div>
            <div class="box">
                <div><strong>Consultant:</strong> {{ $prescription->doctor?->full_name ?? '-' }}</div>
                <div><strong>Symptoms:</strong> {{ $prescription->opdPatient?->symptoms_name ?? '-' }}</div>
                <div><strong>Weight:</strong> {{ $prescription->opdPatient?->weight ?? '-' }}</div>
                <div><strong>BP:</strong> {{ $prescription->opdPatient?->bp ?? '-' }}</div>
                <div><strong>Valid Till:</strong> {{ $prescription->valid_till ? $prescription->valid_till->format('d-m-Y') : '-' }}</div>
            </div>
        </div>

        @if($prescription->header_note)
            <div class="note">
                <strong>Header Note</strong><br>
                {!! $prescription->header_note !!}
            </div>
        @endif

        <table>
            <thead>
                <tr>
                    <th>Medicine Category</th>
                    <th>Medicine</th>
                    <th>Dosage</th>
                    <th>Instruction</th>
                    <th>Frequency</th>
                    <th>No Of Day</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prescription->items as $item)
                    <tr>
                        <td>{{ $item->category?->name ?? '-' }}</td>
                        <td>{{ $item->medicine?->name ?? '-' }}</td>
                        <td>{{ $item->dosage?->dosage ?? '-' }}</td>
                        <td>{{ $item->instruction?->instruction ?? '-' }}</td>
                        <td>{{ $item->frequency?->frequency ?? '-' }}</td>
                        <td>{{ $item->no_of_day ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($prescription->footer_note)
            <div class="note" style="margin-top: 12px;">
                <strong>Footer Note</strong><br>
                {!! $prescription->footer_note !!}
            </div>
        @endif

        @if(!empty($printTemplate?->footer_text))
            <div class="template-footer">
                {!! nl2br(e($printTemplate->footer_text)) !!}
            </div>
        @endif

        <div class="footer">
            <div>Generated by {{ auth()->user()->name }}</div>
            <div>Doctor Signature: ____________________</div>
        </div>
    </div>

    <script>
      (function () {
        let closeHandled = false;

        function closeAfterPrint() {
          if (closeHandled) {
            return;
          }

          closeHandled = true;
          window.close();
        }

        window.addEventListener('afterprint', closeAfterPrint);

        if (window.matchMedia) {
          const mediaQueryList = window.matchMedia('print');
          const mediaListener = function (event) {
            if (!event.matches) {
              closeAfterPrint();
            }
          };

          if (typeof mediaQueryList.addEventListener === 'function') {
            mediaQueryList.addEventListener('change', mediaListener);
          } else if (typeof mediaQueryList.addListener === 'function') {
            mediaQueryList.addListener(mediaListener);
          }
        }

        window.addEventListener('load', function () {
          window.print();
        });

        window.addEventListener('afterprint', function () {
          if (document.referrer) {
            window.location.href = document.referrer;
          } else {
            window.history.back();
          }
        });
      })();</script>
</body>
</html>
