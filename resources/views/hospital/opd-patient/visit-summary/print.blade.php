<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Visit Summary #{{ $visit->id }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            background: #eef3f8;
            color: #1f2937;
        }

        .sheet {
            max-width: 980px;
            margin: 20px auto;
            background: #fff;
            border: 1px solid #dbe4ef;
            border-radius: 10px;
            padding: 20px 22px;
        }

        .page {
            min-height: 1360px;
        }

        .page + .page {
            page-break-before: always;
            border-top: 2px dashed #cbd5e1;
            margin-top: 22px;
            padding-top: 22px;
        }

        .top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #dbe4ef;
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

        .meta {
            text-align: right;
            font-size: 14px;
            line-height: 1.6;
        }

        .section-title {
            margin: 16px 0 10px;
            font-size: 15px;
            font-weight: 700;
            color: #0f4c81;
            text-transform: uppercase;
            letter-spacing: 0.4px;
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

        .box-tight {
            padding: 10px 12px;
        }

        .box-muted {
            background: #fbfdff;
        }

        .write-line {
            display: flex;
            align-items: center;
            gap: 8px;
            min-height: 26px;
            margin-bottom: 4px;
        }

        .write-line .label {
            min-width: 150px;
            font-weight: 700;
            color: #334155;
        }

        .write-line .line {
            flex: 1;
            min-height: 22px;
            border-bottom: 1px solid #94a3b8;
            display: inline-flex;
            align-items: center;
            padding: 0 6px;
        }

        .write-line .line.multiline {
            min-height: 54px;
            align-items: flex-start;
            padding-top: 6px;
        }

        .subgrid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px 16px;
        }

        .mini-line {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .mini-line strong {
            min-width: 88px;
            font-size: 13px;
        }

        .mini-line span {
            flex: 1;
            min-height: 20px;
            border-bottom: 1px solid #94a3b8;
            padding: 0 4px;
        }

        .history-table,
        .charge-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .history-table th,
        .history-table td,
        .charge-table th,
        .charge-table td {
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            vertical-align: top;
        }

        .history-table th,
        .charge-table th {
            background: #f1f5f9;
            color: #0f172a;
            font-weight: 700;
        }

        .blank-row td {
            height: 34px;
        }

        .notes-box {
            min-height: 90px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 10px 12px;
            line-height: 1.8;
        }

        .bill-head {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 12px 14px;
            background: #f8fbff;
            margin-bottom: 14px;
        }

        .bill-head h4 {
            margin: 0;
            font-size: 20px;
            color: #0f4c81;
        }

        .bill-head small {
            color: #475569;
            font-size: 12px;
        }

        .bill-meta {
            text-align: right;
            font-size: 13px;
            line-height: 1.7;
        }

        .bill-parties {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 14px;
        }

        .party-box {
            border: 1px solid #dbe4ef;
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 13px;
            line-height: 1.7;
        }

        .party-box .party-title {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #64748b;
            margin-bottom: 4px;
        }

        .bill-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-bottom: 12px;
        }

        .bill-table th,
        .bill-table td {
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            vertical-align: top;
        }

        .bill-table thead th {
            background: #eef4fb;
            color: #0f172a;
            font-weight: 700;
        }

        .text-end {
            text-align: right;
        }

        .bill-totals {
            margin-left: auto;
            width: 320px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            overflow: hidden;
            font-size: 13px;
            margin-bottom: 12px;
        }

        .bill-totals .row {
            display: flex;
            justify-content: space-between;
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
        }

        .bill-totals .row:last-child {
            border-bottom: 0;
            background: #eef4fb;
            font-weight: 700;
            color: #0f4c81;
        }

        .signature-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 28px;
            margin-top: 26px;
        }

        .signature-box {
            padding-top: 30px;
            border-top: 1px solid #64748b;
            text-align: center;
            font-size: 13px;
            color: #475569;
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

        .footer {
            margin-top: 16px;
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: #4b5563;
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

            .page {
                min-height: auto;
            }

            .page + .page {
                border-top: none;
                margin-top: 0;
                padding-top: 0;
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
        $familyRows = is_array($familyHistory ?? null) ? $familyHistory : [];
        $minimumFamilyRows = 5;
        $blankFamilyRows = max(0, $minimumFamilyRows - count($familyRows));
        $visitChargeLabel = $visitCharge?->particular ?: 'Consultation Charge';
        $consultationRule = $visit->consultation_case_label ?: 'New Case';
        $consultationSource = $visit->consultation_charge_source ?: ($visitCharge?->payer_type === 'tpa' ? 'TPA OPD Charge' : 'Doctor Consultation Charge');
        $consultationValidity = $visit->consultation_valid_until ? \Carbon\Carbon::parse($visit->consultation_valid_until)->format('d-m-Y') : '-';
        $doctorFullName = $visit->consultant?->full_name ?? '-';
        $consultationQty = (float) ($visitCharge?->quantity ?? 1);
        $consultationRate = (float) ($visitCharge?->unit_rate ?? $visit->applied_charge ?? 0);
        $consultationAmount = (float) ($visitCharge?->net_amount ?? $visit->applied_charge ?? 0);
        $invoiceNo = 'OPD-BILL-' . str_pad((string) $visit->id, 6, '0', STR_PAD_LEFT);
        $invoiceDate = optional($visit->appointment_date)->format('d-m-Y h:i A') ?? '-';
        $paymentStatus = ucfirst((string) ($visitCharge?->payment_status ?? ($consultationAmount > 0 ? 'unpaid' : 'paid')));
    @endphp

    <div class="sheet">
        <div class="page">
            <div class="top">
                <div class="brand-wrap">
                    <img src="{{ $logo }}" alt="Hospital Logo" class="brand-logo">
                    <div>
                        <div class="brand">{{ $hospital?->name ?? config('app.name') }}</div>
                        <div class="brand-subtitle">
                            @if($addressLine !== '')
                                {{ $addressLine }}<br>
                            @endif
                            OPD Registration Slip / Clinical Intake Sheet
                        </div>
                    </div>
                </div>
                <div class="meta">
                    <div><strong>Visit #{{ $visit->id }}</strong></div>
                    <div>Date: {{ optional($visit->appointment_date)->format('d-m-Y h:i A') ?? '-' }}</div>
                    <div>Token: {{ $visit->token_no ? \App\Services\OpdTokenNoService::formatForDisplay($visit->token_no) : ($visit->case_no ?? '-') }}</div>
                    <div>Doctor: {{ $doctorFullName }}</div>
                </div>
            </div>

            <div class="section-title">Patient Identification</div>
            <div class="grid">
                <div class="box box-tight">
                    <div class="write-line"><span class="label">Patient Name</span><span class="line">{{ $patient?->name ?? '' }}</span></div>
                    <div class="write-line"><span class="label">Patient ID</span><span class="line">{{ $patient?->patient_id ?? '' }}</span></div>
                    <div class="write-line"><span class="label">Guardian Name</span><span class="line">{{ $patient?->guardian_name ?? '' }}</span></div>
                    <div class="write-line"><span class="label">Phone</span><span class="line">{{ $patient?->phone ?? '' }}</span></div>
                    <div class="write-line"><span class="label">Address</span><span class="line multiline">{{ $patient?->address ?? '' }}</span></div>
                </div>
                <div class="box box-tight">
                    <div class="write-line"><span class="label">Gender</span><span class="line">{{ $patient?->gender ?? '' }}</span></div>
                    <div class="write-line"><span class="label">Age / DOB</span><span class="line">{{ ($patient?->age_years ?? 0) . ' Year ' . ($patient?->age_months ?? 0) . ' Month' }} @if($patient?->date_of_birth) / {{ \Carbon\Carbon::parse($patient->date_of_birth)->format('d-m-Y') }} @endif</span></div>
                    <div class="write-line"><span class="label">Marital Status</span><span class="line">{{ $patient?->marital_status ?? '' }}</span></div>
                    <div class="write-line"><span class="label">TPA / Ref.</span><span class="line">{{ $tpaName ?: '' }}{{ $visit->tpa_reference_no ? ' / ' . $visit->tpa_reference_no : '' }}</span></div>
                    <div class="write-line"><span class="label">Doctor</span><span class="line">{{ $doctorFullName }}</span></div>
                </div>
            </div>

            <div class="section-title">Vitals / Examination</div>
            <div class="box box-muted">
                <div class="subgrid">
                    <div class="mini-line"><strong>Height</strong><span>{{ $visit->height ?? '' }}</span></div>
                    <div class="mini-line"><strong>Weight</strong><span>{{ $visit->weight ?? '' }}</span></div>
                    <div class="mini-line"><strong>BP</strong><span>{{ $visit->bp ?? '' }}</span></div>
                    <div class="mini-line"><strong>Pulse</strong><span>{{ $visit->pluse ?? '' }}</span></div>
                    <div class="mini-line"><strong>Temperature</strong><span>{{ $visit->temperature ?? '' }}</span></div>
                    <div class="mini-line"><strong>Respiration</strong><span>{{ $visit->respiration ?? '' }}</span></div>
                    <div class="mini-line"><strong>Sugar</strong><span>{{ $visit->diabetes ?? '' }}</span></div>
                    <div class="mini-line"><strong>BMI</strong><span>{{ $visit->bmi ?? '' }}</span></div>
                    <div class="mini-line"><strong>Body Area</strong><span>{{ $visit->body_area ?? '' }}</span></div>
                </div>
            </div>

            <div class="section-title">Clinical Notes</div>
            <div class="grid">
                <div class="box box-tight">
                    <div class="write-line"><span class="label">Symptoms</span><span class="line multiline">{{ $visit->symptoms_name ?? '' }}</span></div>
                    <div class="write-line"><span class="label">Known Allergies</span><span class="line multiline">{{ !empty($patientAllergyNames) ? implode(', ', $patientAllergyNames) : ($patient?->known_allergies ?? '') }}</span></div>
                    <div class="write-line"><span class="label">Disease History</span><span class="line multiline">{{ !empty($patientDiseaseNames) ? implode(', ', $patientDiseaseNames) : '' }}</span></div>
                </div>
                <div class="box box-tight">
                    <div class="write-line"><span class="label">Chief Complaint</span><span class="line multiline"></span></div>
                    <div class="write-line"><span class="label">Examination</span><span class="line multiline"></span></div>
                    <div class="write-line"><span class="label">Advice / Notes</span><span class="line multiline">{{ $visit->symptoms_description ?? '' }}</span></div>
                </div>
            </div>
        </div>

        <div class="page">
            <div class="section-title">Social / Lifestyle History</div>
            <div class="grid">
                <div class="box box-tight">
                    <div class="write-line"><span class="label">Occupation</span><span class="line">{{ $visit->occupation ?? '' }}</span></div>
                    <div class="write-line"><span class="label">Place of Birth</span><span class="line">{{ $visit->place_of_birth ?? '' }}</span></div>
                    <div class="write-line"><span class="label">Current Location</span><span class="line">{{ $visit->current_location ?? '' }}</span></div>
                    <div class="write-line"><span class="label">Years in Current City</span><span class="line">{{ $visit->years_in_current_location ?? '' }}</span></div>
                </div>
                <div class="box box-tight">
                    <div><strong>Habits</strong></div>
                    <div class="notes-box">
                        @if(!empty($socialHabits))
                            @foreach($socialHabits as $habit)
                                <div>
                                    {{ $habit['name'] ?? '-' }}@if(!empty($habit['status'])) ({{ $habit['status'] }}) @endif
                                </div>
                            @endforeach
                        @else
                            <div>&nbsp;</div>
                            <div>&nbsp;</div>
                            <div>&nbsp;</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="section-title">Family History</div>
            <table class="history-table">
                <thead>
                    <tr>
                        <th style="width: 26%;">Relation</th>
                        <th style="width: 22%;">Disease / Condition</th>
                        <th style="width: 14%;">Age</th>
                        <th>Comments</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($familyRows as $family)
                        <tr>
                            <td>{{ $family['relation'] ?? '' }}</td>
                            <td>{{ $family['disease'] ?? '' }}</td>
                            <td>{{ $family['age'] ?? '' }}</td>
                            <td>{{ $family['comments'] ?? '' }}</td>
                        </tr>
                    @endforeach
                    @for($i = 0; $i < $blankFamilyRows; $i++)
                        <tr class="blank-row">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endfor
                </tbody>
            </table>

            <div class="signature-row">
                <div class="signature-box">Reception / Registration</div>
                <div class="signature-box">Nurse / Vitals</div>
                <div class="signature-box">Consultant Doctor</div>
            </div>

            <div class="footer">
                <div>Generated by {{ auth()->user()->name }}</div>
            </div>
        </div>

        <div class="page">
            <div class="top">
                <div class="brand-wrap">
                    <img src="{{ $logo }}" alt="Hospital Logo" class="brand-logo">
                    <div>
                        <div class="brand">{{ $hospital?->name ?? config('app.name') }}</div>
                        <div class="brand-subtitle">OPD Consultation Bill</div>
                    </div>
                </div>
                <div class="meta">
                    <div><strong>Visit #{{ $visit->id }}</strong></div>
                    <div>Token: {{ $visit->token_no ? \App\Services\OpdTokenNoService::formatForDisplay($visit->token_no) : ($visit->case_no ?? '-') }}</div>
                    <div>Patient: {{ $patient?->name ?? '-' }}</div>
                    <div>Doctor: {{ $doctorFullName }}</div>
                </div>
            </div>

            <div class="bill-head">
                <div>
                    <h4>Tax Invoice</h4>
                    <small>Consultation billing for OPD visit</small>
                </div>
                <div class="bill-meta">
                    <div><strong>Invoice No:</strong> {{ $invoiceNo }}</div>
                    <div><strong>Invoice Date:</strong> {{ $invoiceDate }}</div>
                    <div><strong>Case No:</strong> {{ $visit->case_no ?? '-' }}</div>
                    <div><strong>Status:</strong> {{ $paymentStatus }}</div>
                </div>
            </div>

            <div class="bill-parties">
                <div class="party-box">
                    <div class="party-title">Bill To</div>
                    <div><strong>{{ $patient?->name ?? '-' }}</strong></div>
                    <div>UHID: {{ $patient?->patient_id ?? '-' }}</div>
                    <div>Phone: {{ $patient?->phone ?? '-' }}</div>
                    <div>TPA: {{ $tpaName ?: 'Self Payer' }}</div>
                    @if(!empty($visit->tpa_reference_no))
                        <div>TPA Ref: {{ $visit->tpa_reference_no }}</div>
                    @endif
                </div>
                <div class="party-box">
                    <div class="party-title">Consultation Details</div>
                    <div><strong>Doctor:</strong> {{ $doctorFullName }}</div>
                    <div><strong>Visit Type:</strong> {{ $consultationRule }}</div>
                    <div><strong>Charge Basis:</strong> {{ $consultationSource }}</div>
                    <div><strong>Validity:</strong> {{ $consultationValidity }}</div>
                    <div><strong>Payment Mode:</strong> {{ $visit->payment_mode ?? '-' }}</div>
                </div>
            </div>

            <table class="bill-table">
                <thead>
                    <tr>
                        <th style="width: 7%;">SN</th>
                        <th style="width: 40%;">Particular</th>
                        <th style="width: 19%;">Visit Type</th>
                        <th style="width: 10%;" class="text-end">Qty</th>
                        <th style="width: 12%;" class="text-end">Rate</th>
                        <th style="width: 12%;" class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>
                            <strong>{{ $visitChargeLabel }}</strong><br>
                            <small>Doctor: {{ $doctorFullName }}</small>
                        </td>
                        <td>{{ $consultationRule }}</td>
                        <td class="text-end">{{ number_format($consultationQty, 2) }}</td>
                        <td class="text-end">{{ number_format($consultationRate, 2) }}</td>
                        <td class="text-end">{{ number_format($consultationAmount, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- <div class="bill-totals">
                <div class="row"><span>Sub Total</span><span>Rs. {{ number_format($consultationAmount, 2) }}</span></div>
                <div class="row"><span>Discount</span><span>Rs. 0.00</span></div>
                <div class="row"><span>Tax</span><span>Rs. 0.00</span></div>
                <div class="row"><span>Net Payable</span><span>Rs. {{ number_format($consultationAmount, 2) }}</span></div>
            </div> -->

            <div class="section-title">Bill Notes</div>
            <div class="notes-box">
                <!-- <div><strong>Charge Item:</strong> Only OPD consultation charge for this visit is included.</div> -->
                <div><strong>Visit Date:</strong> {{ optional($visit->appointment_date)->format('d-m-Y h:i A') ?? '-' }}</div>
                <div><strong>Consultation:</strong> {{ $consultationRule }} ({{ $consultationSource }})</div>
                <div><strong>Payment Mode:</strong> {{ $visit->payment_mode ?? '-' }} | <strong>Status:</strong> {{ $paymentStatus }}</div>
            </div>

            <div class="signature-row">
                <div class="signature-box">Billing Executive</div>
                <div class="signature-box">Patient / Attendant</div>
                <div class="signature-box">Authorized Signature</div>
            </div>

            <div class="footer">
                <div>Generated by {{ auth()->user()->name }}</div>
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