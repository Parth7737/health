@php
    $s = strtolower(str_replace([' ', '-'], '_', (string) $row->status));
    if ($s === 'in_progress') {
        $s = 'examination';
    }
@endphp
@if(!empty($completedOnly))
    <a href="{{ route('hospital.radiology.ris.completed-pdf', $row) }}" class=" btn btn-sm btn-warning" target="_blank" rel="noopener">
        <i class="fas fa-print"></i>
    </a>
@else
    @if($s === 'ordered')
        <button type="button" class="rad-ris-btn rad-ris-btn-primary rad-ris-btn-sm rad-ris-wf-to-exam" data-item-id="{{ $row->id }}">
            <i class="fa-solid fa-arrow-right"></i> Start examination
        </button>
    @elseif($s === 'examination')
        <button type="button" class="rad-ris-btn rad-ris-btn-outline rad-ris-btn-sm rad-ris-wf-open-reporting" data-item-id="{{ $row->id }}"
            data-order-no="{{ e($row->order->order_no ?? '') }}"
            data-patient-id="{{ e(optional($row->order?->patient)->patient_id ?? optional($row->order?->patient)->mrn ?? '') }}"
            data-patient="{{ e(optional($row->order?->patient)->name ?? '') }}"
            data-study="{{ e($row->test_name ?? '') }}">
            <i class="fas fa-file-medical"></i>
        </button>
    @else
        <span class="rad-ris-text-muted rad-ris-text-sm">—</span>
    @endif
@endif
