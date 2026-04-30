<div class="modal-header">
    <h5 class="modal-title">Visit Summary #{{ $visit->id }}</h5>
    <div class="d-flex align-items-center gap-2 ms-auto">
        <a href="{{ route('hospital.opd-patient.visit-summary.print', ['opdPatient' => $visit->id]) }}"
            class="btn btn-info btn-sm" target="_blank" title="Print Visit Summary">
            <i class="fa-solid fa-print"></i>
            <span class="d-none d-md-inline ms-1">Print</span>
        </a>
    </div>
    <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="border rounded p-3 h-100 bg-light-subtle">
                <h6 class="mb-3">Patient Details</h6>
                <p class="mb-1"><strong>Patient Name:</strong> {{ $patient?->name ?? '-' }}</p>
                <p class="mb-1"><strong>Patient ID:</strong> {{ $patient?->patient_id ?? '-' }}</p>
                <p class="mb-1"><strong>Guardian Name:</strong> {{ $patient?->guardian_name ?? '-' }}</p>
                <p class="mb-1"><strong>Gender:</strong> {{ $patient?->gender ?? '-' }}</p>
                <p class="mb-1"><strong>Age:</strong> {{ $patient?->age_years ?? 0 }} Year {{ $patient?->age_months ?? 0 }} Month</p>
                <p class="mb-1"><strong>DOB:</strong> {{ $patient?->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth)->format('d-m-Y') : '-' }}</p>
                <p class="mb-1"><strong>Phone:</strong> {{ $patient?->phone ?? '-' }}</p>
                <p class="mb-1"><strong>Email:</strong> {{ $patient?->email ?? '-' }}</p>
                <p class="mb-0"><strong>Address:</strong> {{ $patient?->address ?? '-' }}</p>
            </div>
        </div>

        <div class="col-md-6">
            <div class="border rounded p-3 h-100 bg-light-subtle">
                <h6 class="mb-3">Visit Details</h6>
                <p class="mb-1"><strong>Case No:</strong> {{ $visit->case_no ?? '-' }}</p>
                <p class="mb-1"><strong>Appointment Date:</strong> {{ optional($visit->appointment_date)->format('d-m-Y h:i A') ?? '-' }}</p>
                <p class="mb-1"><strong>Consultant:</strong> {{ $visit->consultant?->full_name ?? '-' }}</p>
                <p class="mb-1"><strong>Symptoms:</strong> {{ $visit->symptoms_name ?? '-' }}</p>
                <p class="mb-1"><strong>Reference No:</strong> {{ $visit->tpa_reference_no ?? '-' }}</p>
                <p class="mb-1"><strong>TPA:</strong> {{ $tpaName ?: '-' }}</p>
                <p class="mb-1"><strong>Payment Mode:</strong> {{ $visit->payment_mode ?? '-' }}</p>
                <p class="mb-0"><strong>Marital Status:</strong> {{ $patient?->marital_status ?? '-' }}</p>
            </div>
        </div>

        <div class="col-md-6">
            <div class="border rounded p-3 h-100">
                <h6 class="mb-3">Vitals</h6>
                <p class="mb-1"><strong>Height:</strong> {{ $visit->height ?? '-' }}</p>
                <p class="mb-1"><strong>Weight:</strong> {{ $visit->weight ?? '-' }}</p>
                <p class="mb-1"><strong>BP:</strong> {{ $visit->bp ?? '-' }}</p>
                <p class="mb-1"><strong>Systolic BP:</strong> {{ $visit->systolic_bp ?? '-' }}</p>
                <p class="mb-1"><strong>Diastolic BP:</strong> {{ $visit->diastolic_bp ?? '-' }}</p>
                <p class="mb-1"><strong>Pulse:</strong> {{ $visit->pluse ?? '-' }}</p>
                <p class="mb-1"><strong>Respiration:</strong> {{ $visit->respiration ?? '-' }}</p>
                <p class="mb-1"><strong>Diabetes:</strong> {{ $visit->diabetes ?? '-' }}</p>
                <p class="mb-1"><strong>Temperature:</strong> {{ $visit->temperature ?? '-' }}</p>
                <p class="mb-0"><strong>BMI:</strong> {{ $visit->bmi ?? '-' }}</p>
            </div>
        </div>

        <div class="col-md-6">
            <div class="border rounded p-3 h-100">
                <h6 class="mb-3">Charge Details</h6>
                <p class="mb-1"><strong>Standard Charge:</strong> Rs. {{ number_format((float) ($visit->standard_charge ?? 0), 2) }}</p>
                <p class="mb-1"><strong>Applied Charge:</strong> Rs. {{ number_format((float) ($visit->applied_charge ?? 0), 2) }}</p>
            </div>
        </div>

        <div class="col-md-6">
            <div class="border rounded p-3 h-100">
                <h6 class="mb-3">Disease & Allergy Profile</h6>
                <p class="mb-1"><strong>Disease Types:</strong> {{ !empty($patientDiseaseTypeNames) ? implode(', ', $patientDiseaseTypeNames) : '-' }}</p>
                <p class="mb-1"><strong>Diseases:</strong> {{ !empty($patientDiseaseNames) ? implode(', ', $patientDiseaseNames) : '-' }}</p>
                <p class="mb-1"><strong>Known Allergies (Patient Master):</strong> {{ !empty($patientAllergyNames) ? implode(', ', $patientAllergyNames) : '-' }}</p>
                <p class="mb-1"><strong>Clinical Allergy Notes:</strong> {{ $patient?->known_allergies ?? '-' }}</p>
                <p class="mb-1"><strong>Known Allergies (Social):</strong> {{ !empty($socialKnownAllergyNames) ? implode(', ', $socialKnownAllergyNames) : '-' }}</p>
                <p class="mb-1"><strong>Allergic Reactions:</strong> {{ !empty($socialAllergicReactionNames) ? implode(', ', $socialAllergicReactionNames) : '-' }}</p>
                <p class="mb-0"><strong>Body Area (Marked):</strong> {{ $visit->body_area ?? '-' }}</p>
            </div>
        </div>

        <div class="col-md-6">
            <div class="border rounded p-3 h-100">
                <h6 class="mb-3">Social History</h6>
                <p class="mb-1"><strong>Occupation:</strong> {{ $visit->occupation ?? '-' }}</p>
                <p class="mb-1"><strong>Marital Status (Social):</strong> {{ $visit->social_marital_status ?? '-' }}</p>
                <p class="mb-1"><strong>Place of Birth:</strong> {{ $visit->place_of_birth ?? '-' }}</p>
                <p class="mb-1"><strong>Current Location:</strong> {{ $visit->current_location ?? '-' }}</p>
                <p class="mb-1"><strong>Years in Current Location:</strong> {{ $visit->years_in_current_location ?? '-' }}</p>
                <p class="mb-1"><strong>Habits:</strong></p>
                @if(!empty($socialHabits))
                    @foreach($socialHabits as $habit)
                        <p class="mb-1">
                            - {{ $habit['name'] ?? '-' }}
                            @if(!empty($habit['status']))
                                ({{ $habit['status'] }})
                            @endif
                        </p>
                    @endforeach
                @else
                    <p class="mb-0">-</p>
                @endif
            </div>
        </div>

        <div class="col-12">
            <div class="border rounded p-3 h-100">
                <h6 class="mb-3">Family History</h6>
                @if(!empty($familyHistory))
                    @foreach($familyHistory as $family)
                        <p class="mb-1">
                            <strong>Disease:</strong> {{ $family['disease'] ?? '-' }},
                            <strong>Relation:</strong> {{ $family['relation'] ?? '-' }},
                            <strong>Age:</strong> {{ $family['age'] ?? '-' }},
                            <strong>Comments:</strong> {{ $family['comments'] ?? '-' }}
                        </p>
                    @endforeach
                @else
                    <p class="mb-0">-</p>
                @endif
            </div>
        </div>
    </div>
</div>
