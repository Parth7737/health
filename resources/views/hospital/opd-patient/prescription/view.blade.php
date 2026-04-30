<div class="modal-header">
    <h5 class="modal-title">Prescription #{{ $prescription->id }}</h5>
    <div class="d-flex align-items-center gap-2 ms-auto">
        <button type="button" class="btn btn-info btn-sm edit-prescription-btn" title="Edit Prescription"
            data-form-url="{{ route('hospital.opd-patient.prescription.form', ['opdPatient' => $opdPatient->id]) }}">
            <i class="fa-solid fa-pen"></i>
            <span class="d-none d-md-inline ms-1">Edit</span>
        </button>
        <button type="button" class="btn btn-info btn-sm print-prescription-btn" title="Print Prescription"
            data-print-url="{{ route('hospital.opd-patient.prescription.print', ['opdPatient' => $opdPatient->id]) }}">
            <i class="fa-solid fa-print"></i>
            <span class="d-none d-md-inline ms-1">Print</span>
        </button>
        <button type="button" class="btn btn-info btn-sm delete-prescription-btn" title="Delete Prescription"
            data-delete-url="{{ route('hospital.opd-patient.prescription.destroy', ['opdPatient' => $opdPatient->id]) }}">
            <i class="fa-solid fa-trash-can"></i>
            <span class="d-none d-md-inline ms-1">Delete</span>
        </button>
    </div>
    <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="border rounded p-3 h-100 bg-light-subtle">
                <p class="mb-1"><strong>Patient:</strong> {{ $prescription->patient?->name ?? '-' }}</p>
                <p class="mb-1"><strong>Patient ID:</strong> {{ $prescription->patient?->patient_id ?? '-' }}</p>
                <p class="mb-1"><strong>Gender:</strong> {{ $prescription->patient?->gender ?? '-' }}</p>
                <p class="mb-0"><strong>Age:</strong> {{ $prescription->patient?->age_years ?? 0 }} Year {{ $prescription->patient?->age_months ?? 0 }} Month</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="border rounded p-3 h-100 bg-light-subtle">
                <p class="mb-1"><strong>Case No:</strong> {{ $prescription->opdPatient?->case_no ?? '-' }}</p>
                <p class="mb-1"><strong>Date:</strong> {{ optional($prescription->opdPatient?->appointment_date)->format('d-m-Y h:i A') ?? '-' }}</p>
                <p class="mb-1"><strong>Consultant:</strong> {{ $prescription->doctor?->full_name ?? '-' }}</p>
                <p class="mb-0"><strong>Symptoms:</strong> {{ $prescription->opdPatient?->symptoms_name ?? '-' }}</p>
            </div>
        </div>

        @if($prescription->header_note)
            <div class="col-12">
                <div class="border rounded p-3">
                    <h6 class="mb-2">Header Note</h6>
                    <div class="prescription-note-content">{!! $prescription->header_note !!}</div>
                </div>
            </div>
        @endif
    </div>

    <div class="table-responsive mb-3">
        <table class="table table-bordered align-middle mb-0">
            <thead class="table-light">
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
                @forelse($prescription->items as $item)
                    <tr>
                        <td>{{ $item->category?->name ?? '-' }}</td>
                        <td>{{ $item->medicine?->name ?? '-' }}</td>
                        <td>{{ $item->dosage?->dosage ?? '-' }}</td>
                        <td>{{ $item->instruction?->instruction ?? '-' }}</td>
                        <td>{{ $item->frequency?->frequency ?? '-' }}</td>
                        <td>{{ $item->no_of_day ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No medicine rows found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($prescription->footer_note)
        <div class="border rounded p-3">
            <h6 class="mb-2">Footer Note</h6>
            <div class="prescription-note-content">{!! $prescription->footer_note !!}</div>
        </div>
    @endif
</div>
