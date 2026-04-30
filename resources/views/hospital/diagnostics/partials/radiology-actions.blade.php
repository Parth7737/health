<ul class="action">
    @php($statusKey = strtolower(str_replace([' ', '-'], '_', (string) $row->status)))
    @php($charge = $row->patientCharge)
    @php($patientId = $row->order->patient_id ?? null)
    @php($isReportReady = $statusKey === 'completed')
    @if(!in_array($statusKey, ['completed', 'cancelled'], true))
        <li class="view">
            <a href="javascript:;" class="update-item-status" data-id="{{ $row->id }}" data-url="{{ route('hospital.radiology.worklist.status', ['item' => $row->id]) }}" data-bs-toggle="tooltip" title="Move to Next Status">
                <i class="text-warning fa-solid fa-forward-step"></i>
            </a>
        </li>
    @endif
    <li class="view">
        <a href="javascript:;" data-id="{{ $row->id }}" class="open-report-form" data-bs-toggle="tooltip" title="Update Report"><i class="text-primary fa-solid fa-file-medical"></i></a>
    </li>
    @if($charge && $patientId && $charge->payment_status !== 'paid')
        <li class="view">
            <a href="javascript:;" class="open-charge-payment-form" data-url="{{ route('hospital.opd-patient.charges.show-payment-form', ['patient' => $patientId]) }}" data-charge-id="{{ $charge->id }}" data-title="Collect Payment | {{ $row->test_name }}" data-context-note="Radiology worklist payment will update the patient ledger as well." data-bs-toggle="tooltip" title="Collect Payment">
                <i class="text-info fa-solid fa-wallet"></i>
            </a>
        </li>
    @endif
    @if($isReportReady)
        <li class="view">
            <a href="{{ route('hospital.radiology.worklist.print', ['item' => $row->id]) }}" target="_blank" data-bs-toggle="tooltip" title="Print Report"><i class="text-success fa-solid fa-print"></i></a>
        </li>
    @endif
</ul>
