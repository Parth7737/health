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
