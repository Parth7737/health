@php
    $risNormStatus = strtolower(str_replace([' ', '-'], '_', (string) $item->status));
@endphp
<div class="modal-header">
    <h5 class="modal-title">Radiology Report | {{ $item->test_name }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="saveReportForm">
    <input type="hidden" name="item_id" value="{{ $item->id }}">
    <input type="hidden" name="save_action" id="radRisSaveAction" value="save">
    <div class="modal-body">
        <div class="row g-2">
            <div class="col-md-4">
                <label class="form-label">Order No</label>
                <input type="text" class="form-control" value="{{ $item->order->order_no ?? '-' }}" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label">Patient</label>
                <input type="text" class="form-control" value="{{ $item->order->patient->name ?? '-' }}" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label">Visit</label>
                <input type="text" class="form-control" value="{{ optional($item->order->visitable)->case_no ?? '-' }}" readonly>
            </div>

            <div class="col-md-4">
                <label class="form-label">Workflow Status</label>
                <input type="text" class="form-control" value="{{ ucwords(str_replace('_', ' ', $item->status)) }}" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label">Payment Status</label>
                <input type="text" class="form-control" value="{{ ucfirst($item->patientCharge->payment_status ?? $item->payment_status ?? 'unpaid') }}" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label">Pending Amount</label>
                <input type="text" class="form-control" value="{{ number_format(max(0, (float) ($item->patientCharge->amount ?? $item->net_amount ?? $item->standard_charge ?? 0) - (float) ($item->patientCharge->paid_amount ?? $item->paid_amount ?? 0)), 2) }}" readonly>
            </div>
            <div class="col-md-8">
                <label class="form-label">Report Summary</label>
                <input type="text" class="form-control" name="report_summary" value="{{ $item->report_summary }}">
            </div>

            @if($item->parameters->isNotEmpty())
                <div class="col-md-12">
                    <label class="form-label">Study parameters / measurements</label>
                    <div class="table-responsive border rounded">
                        <table class="table table-sm mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Parameter</th>
                                    <th class="text-center" style="width:80px;">Unit</th>
                                    <th class="text-center" style="width:120px;">Normal range</th>
                                    <th class="text-center" style="width:110px;">Result</th>
                                    <th class="text-center" style="width:120px;">Flag</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($item->parameters->sortBy('sort_order') as $parameter)
                                    @php
                                        $paramDef = $parameter->parameterable;
                                        $minVal = $paramDef?->min_value ?? null;
                                        $maxVal = $paramDef?->max_value ?? null;
                                        $critLow = $paramDef?->critical_low ?? null;
                                        $critHigh = $paramDef?->critical_high ?? null;
                                        $unitName = $paramDef?->unit?->name ?? ($parameter->unit_name ?? '—');
                                        $rangeText = $parameter->normal_range ?? '—';
                                        if ($minVal !== null && $maxVal !== null) {
                                            $rangeText = number_format((float) $minVal, 2) . ' - ' . number_format((float) $maxVal, 2);
                                        }
                                    @endphp
                                    <tr>
                                        <td>
                                            {{ $parameter->parameter_name }}
                                            <input type="hidden" class="param-min" value="{{ $minVal }}">
                                            <input type="hidden" class="param-max" value="{{ $maxVal }}">
                                            <input type="hidden" class="param-crit-low" value="{{ $critLow }}">
                                            <input type="hidden" class="param-crit-high" value="{{ $critHigh }}">
                                        </td>
                                        <td class="text-center small">{{ $unitName }}</td>
                                        <td class="text-center small text-muted">{{ $rangeText }}</td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm rad-ris-legacy-result-value" name="result_value[{{ $parameter->id }}]" value="{{ $parameter->result_value }}" data-param-id="{{ $parameter->id }}" autocomplete="off">
                                        </td>
                                        <td class="text-center">
                                            <div class="result-flag-display rad-ris-legacy-flag" data-param-id="{{ $parameter->id }}">
                                                @if($parameter->result_flag)
                                                    @php
                                                        $flagConfig = [
                                                            'normal' => ['✓ Normal', '#4caf50', '#e8f5e9'],
                                                            'low' => ['↓ Low', '#ff9800', '#fff3e0'],
                                                            'high' => ['↑ High', '#ff9800', '#fff3e0'],
                                                            'critical_low' => ['↓↓ Critical Low', '#f44336', '#ffebee'],
                                                            'critical_high' => ['↑↑ Critical High', '#f44336', '#ffebee'],
                                                        ];
                                                        $cfg = $flagConfig[$parameter->result_flag] ?? ['—', '#999', '#f5f5f5'];
                                                    @endphp
                                                    <span class="badge rounded-pill" style="background:{{ $cfg[2] }};color:{{ $cfg[1] }};border:1px solid {{ $cfg[1] }}">{{ $cfg[0] }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="remarks[{{ $parameter->id }}]" value="{{ $parameter->remarks }}" autocomplete="off">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <div class="col-md-6">
                <label class="form-label">Narrative Report</label>
                <textarea class="form-control" name="report_text" rows="4">{{ $item->report_text }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Impression</label>
                <textarea class="form-control" name="report_impression" rows="4">{{ $item->report_impression }}</textarea>
            </div>

            @if($item->order->notes)
                <div class="col-md-12">
                    <div class="alert alert-light border mb-0">
                        {{ $item->order->notes ?? '' }}
                    </div>
                </div>
            @endif

            @if($risNormStatus === 'completed')
                <div class="col-md-12">
                    <label class="form-label">Addendum (appends to narrative after finalization)</label>
                    <textarea class="form-control" name="addendum_text" rows="2" placeholder="Additional note — saved only when you use Save addendum"></textarea>
                </div>
            @endif
        </div>
    </div>
    <div class="modal-footer flex-wrap gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <a href="{{ route('hospital.radiology.worklist.print', $item) }}" target="_blank" rel="noopener" class="btn btn-outline-success"><i class="fa-solid fa-print"></i> Print</a>
        <button type="button" class="btn btn-light border" id="radRisReportDraftBtn"><i class="fa-solid fa-floppy-disk"></i> Save draft</button>
        @if($risNormStatus === 'completed')
            <button type="button" class="btn btn-warning text-dark" id="radRisReportAddendumBtn"><i class="fa-solid fa-plus"></i> Save addendum</button>
        @endif
        <button type="button" class="btn btn-primary" id="radRisReportFinalizeBtn"><i class="fa-solid fa-circle-check"></i> Finalize report</button>
        <button type="submit" class="btn btn-outline-primary" id="radRisReportLegacySaveBtn">Save</button>
    </div>
</form>

<script>
(function () {
    function generateRadLegacyFlag(value, minVal, maxVal, critLow, critHigh) {
        if (value === '' || value === null) return null;
        var val = parseFloat(String(value).replace(/,/g, ''));
        if (isNaN(val)) return null;
        var n = function (s) { return (s === '' || s == null) ? null : parseFloat(s); };
        var cLo = n(critLow), cHi = n(critHigh), mn = n(minVal), mx = n(maxVal);
        var cfg = {
            normal: { label: '✓ Normal', color: '#4caf50', bg: '#e8f5e9' },
            low: { label: '↓ Low', color: '#ff9800', bg: '#fff3e0' },
            high: { label: '↑ High', color: '#ff9800', bg: '#fff3e0' },
            critical_low: { label: '↓↓ Critical Low', color: '#f44336', bg: '#ffebee' },
            critical_high: { label: '↑↑ Critical High', color: '#f44336', bg: '#ffebee' }
        };
        var flag = 'normal';
        if (cLo !== null && !isNaN(cLo) && val < cLo) flag = 'critical_low';
        else if (cHi !== null && !isNaN(cHi) && val > cHi) flag = 'critical_high';
        else if (mn !== null && mx !== null && !isNaN(mn) && !isNaN(mx)) {
            if (val < mn) flag = 'low';
            else if (val > mx) flag = 'high';
            else flag = 'normal';
        } else if (mn !== null && !isNaN(mn) && val < mn) flag = 'low';
        else if (mx !== null && !isNaN(mx) && val > mx) flag = 'high';
        return Object.assign({ flag: flag }, cfg[flag] || cfg.normal);
    }
    var form = document.getElementById('saveReportForm');
    if (!form) return;
    form.querySelectorAll('.rad-ris-legacy-result-value').forEach(function (input) {
        input.addEventListener('input', function () {
            var row = input.closest('tr');
            if (!row) return;
            var id = input.getAttribute('data-param-id');
            var box = form.querySelector('.rad-ris-legacy-flag[data-param-id="' + id + '"]');
            if (!box) return;
            var info = generateRadLegacyFlag(
                input.value,
                row.querySelector('.param-min') && row.querySelector('.param-min').value,
                row.querySelector('.param-max') && row.querySelector('.param-max').value,
                row.querySelector('.param-crit-low') && row.querySelector('.param-crit-low').value,
                row.querySelector('.param-crit-high') && row.querySelector('.param-crit-high').value
            );
            if (info) {
                box.innerHTML = '<span class="badge rounded-pill" style="background:' + info.bg + ';color:' + info.color + ';border:1px solid ' + info.color + '">' + info.label + '</span>';
            } else {
                box.innerHTML = '';
            }
        });
    });
})();
</script>
