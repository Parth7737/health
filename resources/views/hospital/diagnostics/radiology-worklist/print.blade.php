<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Radiology Report</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: DejaVu Sans, Arial, sans-serif; color: #1d3557; background: #edf4f7; }
        .sheet { max-width: 960px; margin: 24px auto; background: #ffffff; border: 1px solid #d7e3eb; box-shadow: 0 18px 45px rgba(17, 59, 89, 0.08); }
        .print-header-banner { width: 100%; border-bottom: 1px solid #d6e6ea; overflow: hidden; background: #fff; }
        .print-header-banner img { display: block; width: 100%; max-height: 150px; object-fit: cover; }
        .hospital-top { padding: 18px 32px 10px; display: flex; justify-content: space-between; gap: 16px; border-bottom: 1px solid #e2edf3; }
        .brand-wrap { display: flex; gap: 12px; align-items: center; }
        .brand-logo { width: 60px; height: 60px; border-radius: 10px; object-fit: cover; border: 1px solid #d6e6ea; background: #fff; }
        .brand-name { margin: 0; font-size: 22px; color: #13547a; }
        .brand-subtitle { margin-top: 4px; font-size: 12px; color: #5f7687; line-height: 1.5; }
        .hospital-meta { text-align: right; font-size: 12px; color: #5f7687; }
        .header { padding: 24px 32px 20px; background: linear-gradient(135deg, #13547a, #80d0c7); color: #ffffff; }
        .header h1 { margin: 0; font-size: 28px; letter-spacing: 1px; }
        .header p { margin: 6px 0 0; font-size: 13px; opacity: 0.95; }
        .section { padding: 22px 32px; }
        .grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
        .card { background: #f7fbfc; border: 1px solid #d6e6ea; border-radius: 10px; padding: 12px 14px; }
        .label { display: block; font-size: 11px; text-transform: uppercase; letter-spacing: 0.8px; color: #5f7687; margin-bottom: 6px; }
        .value { font-size: 15px; font-weight: 600; color: #16324f; }
        .report-block { margin-top: 18px; padding: 18px 20px; border-radius: 14px; border: 1px solid #d6e6ea; background: linear-gradient(180deg, #fbfeff, #f4f9fb); }
        .report-block h3 { margin: 0 0 10px; font-size: 15px; color: #13547a; text-transform: uppercase; letter-spacing: 0.5px; }
        .report-block p { margin: 0; line-height: 1.7; white-space: pre-line; }
        .html-body { line-height: 1.65; font-size: 14px; color: #16324f; }
        .html-body p { margin: 0 0 8px; white-space: normal; }
        .html-body ul, .html-body ol { margin: 6px 0 10px; padding-left: 22px; }
        .html-body table { border-collapse: collapse; width: 100%; margin: 8px 0; font-size: 13px; }
        .html-body th, .html-body td { border: 1px solid #cfd8e6; padding: 6px 8px; }
        .summary-strip { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-top: 18px; }
        .summary-box { padding: 14px 16px; border-radius: 12px; background: #f4f8fb; border: 1px solid #d6e6ea; }
        .summary-box strong { display: block; font-size: 12px; color: #60748a; margin-bottom: 6px; }
        .summary-box span { font-size: 16px; font-weight: 700; }
        .footer { padding: 20px 32px 30px; display: flex; justify-content: space-between; align-items: flex-end; color: #5d7285; font-size: 12px; }
        .template-footer { margin: 12px 32px 0; border: 1px solid #dbe4ef; border-radius: 8px; background: #f9fbff; padding: 10px 12px; font-size: 13px; color: #334155; line-height: 1.6; }
        .signature { min-width: 220px; text-align: center; }
        .signature-line { margin-top: 42px; border-top: 1px solid #8aa0b8; padding-top: 8px; }
        @media print {
            body { background: #ffffff; }
            .sheet { margin: 0; box-shadow: none; border: none; }
        }
    </style>
</head>
<body>
    @php
        $paymentStatus = ucfirst($item->patientCharge->payment_status ?? $item->payment_status ?? 'unpaid');
    @endphp
    @php
        $paymentDue = max(0, (float) ($item->patientCharge->amount ?? $item->net_amount ?? $item->standard_charge ?? 0) - (float) ($item->patientCharge->paid_amount ?? $item->paid_amount ?? 0));
    @endphp
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
        @isset($templateHeader)
            <div class="print-header-banner">
                <img src="{{ $templateHeader }}" alt="Radiology Header">
            </div>
        @endisset

        <div class="hospital-top">
            <div class="brand-wrap">
                <img src="{{ $logo }}" alt="Hospital Logo" class="brand-logo">
                <div>
                    <h2 class="brand-name">{{ $hospital?->name ?? config('app.name') }}</h2>
                    <div class="brand-subtitle">
                        @if($addressLine !== '')
                            {{ $addressLine }}<br>
                        @endif
                        Radiology Department
                    </div>
                </div>
            </div>
            <div class="hospital-meta">
                <div><strong>Report #{{ $item->id }}</strong></div>
                <div>Date: {{ optional($item->created_at)->format('d-m-Y') ?? '-' }}</div>
            </div>
        </div>

        <div class="header">
            <h1>{{ $item->test_name }}</h1>
            <p>Order {{ $item->order->order_no ?? '-' }}</p>
        </div>

        <div class="section">
            <div class="grid">
                <div class="card"><span class="label">Patient</span><span class="value">{{ $item->order->patient->name ?? '-' }}</span></div>
                <div class="card"><span class="label">Visit No</span><span class="value">{{ optional($item->order->visitable)->case_no ?? '-' }}</span></div>
                <div class="card"><span class="label">Ordered On</span><span class="value">{{ optional($item->created_at)->format('d-m-Y h:i A') ?? '-' }}</span></div>
                <div class="card"><span class="label">Reported On</span><span class="value">{{ optional($item->reported_at)->format('d-m-Y h:i A') ?? '-' }}</span></div>
            </div>

            <div class="summary-strip">
                <div class="summary-box"><strong>Workflow Status</strong><span>{{ ucwords(str_replace('_', ' ', $item->status)) }}</span></div>
                <div class="summary-box"><strong>Payment Status</strong><span>{{ $paymentStatus }}</span></div>
                <div class="summary-box"><strong>Pending Amount</strong><span>{{ number_format($paymentDue, 2) }}</span></div>
            </div>

            <div class="report-block">
                <h3>Clinical indication</h3>
                <div class="html-body">
                    @php $c = \App\Support\SafeReportHtml::sanitize($item->clinical_indication); @endphp
                    @if($c === '')
                        <p>—</p>
                    @else
                        {!! $c !!}
                    @endif
                </div>
            </div>

            <div class="report-block">
                <h3>Technique</h3>
                <p>{{ filled($item->report_technique) ? $item->report_technique : '—' }}</p>
            </div>

            @if($item->parameters->isNotEmpty())
                <div class="report-block">
                    <h3>Parameters / measurements</h3>
                    <table style="width:100%; border-collapse:collapse; font-size:12px;">
                        <thead>
                            <tr style="background:#eef4fb;">
                                <th style="border:1px solid #cfd8e6; padding:6px 8px; text-align:left;">Parameter</th>
                                <th style="border:1px solid #cfd8e6; padding:6px 8px;">Result</th>
                                <th style="border:1px solid #cfd8e6; padding:6px 8px;">Unit</th>
                                <th style="border:1px solid #cfd8e6; padding:6px 8px;">Normal range</th>
                                <th style="border:1px solid #cfd8e6; padding:6px 8px;">Flag</th>
                                <th style="border:1px solid #cfd8e6; padding:6px 8px;">Remarks</th>
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
                                    <td style="border:1px solid #cfd8e6; padding:6px 8px;">{{ $parameter->parameter_name }}</td>
                                    <td style="border:1px solid #cfd8e6; padding:6px 8px;">{{ filled($parameter->result_value) ? $parameter->result_value : '—' }}</td>
                                    <td style="border:1px solid #cfd8e6; padding:6px 8px;">{{ $unitName }}</td>
                                    <td style="border:1px solid #cfd8e6; padding:6px 8px;">{{ $rangeText }}</td>
                                    <td style="border:1px solid #cfd8e6; padding:6px 8px;">{{ $flagLabel }}</td>
                                    <td style="border:1px solid #cfd8e6; padding:6px 8px;">{{ filled($parameter->remarks) ? $parameter->remarks : '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="report-block">
                <h3>Findings</h3>
                <div class="html-body">
                    @php $f = \App\Support\SafeReportHtml::sanitize($item->report_text); @endphp
                    @if($f === '')
                        <p>—</p>
                    @else
                        {!! $f !!}
                    @endif
                </div>
            </div>

            <div class="report-block">
                <h3>Impression</h3>
                <div class="html-body">
                    @php $im = \App\Support\SafeReportHtml::sanitize($item->report_impression); @endphp
                    @if($im === '')
                        <p>—</p>
                    @else
                        {!! $im !!}
                    @endif
                </div>
            </div>

            @if(filled($item->report_summary))
                <div class="report-block">
                    <h3>Summary</h3>
                    <div class="html-body">{!! \App\Support\SafeReportHtml::sanitize($item->report_summary) !!}</div>
                </div>
            @endif
        </div>

        @if(!empty($printTemplate?->footer_text))
            <div class="template-footer">
                {!! nl2br(e($printTemplate->footer_text)) !!}
            </div>
        @endif

        <div class="footer">
            <div>
                Printed on {{ now()->format('d-m-Y h:i A') }}<br>
                Payment Due {{ number_format($paymentDue, 2) }}
            </div>
            <div class="signature">
                <div class="signature-line">Authorized Signature</div>
            </div>
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
      })();
    </script>
</body>
</html>
