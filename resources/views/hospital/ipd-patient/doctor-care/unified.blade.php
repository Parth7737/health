<div class="doctor-care-unified-modal">
    @include('hospital.doctor-care.partials.unified-shell-styles')

    @php
        $ipdPatient = $patient ?? null;
        $ipdBed = $allocation->bed ?? null;
        $ipdBedMeta = $ipdBed
            ? ($ipdBed->bed_code . ' | ' . ($ipdBed->room?->ward?->ward_name ?? '-') . ' / ' . ($ipdBed->room?->room_number ?? '-'))
            : 'Bed not assigned';
        $ipdDept = $allocation->department?->name ?? '—';
    @endphp

    <div class="doctor-care-chip">
        <div class="doctor-care-chip-left">
            <div class="doctor-care-chip-avatar">{{ strtoupper(substr($ipdPatient?->name ?? 'P', 0, 1)) }}</div>
            <div style="min-width:0;">
                <p class="doctor-care-chip-name">{{ $ipdPatient?->name ?? '-' }} <span style="font-size:11px;font-weight:400;color:#6e86a1;">{{ $ipdPatient?->patient_id ? '- ' . $ipdPatient->patient_id : '' }}</span></p>
                <p class="doctor-care-chip-meta">
                    {{ ($ipdPatient?->age_years ?? 0) }} Yrs / {{ $ipdPatient?->gender ?? '-' }}
                    | Admission: {{ $allocation->admission_no ?? '—' }}
                    | {{ $ipdDept }}
                    | {{ $ipdBedMeta }}
                </p>
            </div>
        </div>
        <div class="doctor-care-chip-right"></div>
    </div>

    <div class="doctor-care-grid">
        <div class="doctor-care-col-stack">
            <div class="doctor-care-card">
                <div class="doctor-care-card-head">
                    <span class="doctor-care-section-title">&#x1F4DD; Clinical SOAP Note</span>
                </div>
                <div class="doctor-care-card-body">
                    <div class="doctor-care-form-group">
                        <label>Note Type</label>
                        <select id="doctorUnifiedIpdNoteType" class="form-control">
                            <option value="doctor">Doctor</option>
                            <option value="nursing">Nursing</option>
                            <option value="progress">Progress</option>
                            <option value="discharge_plan">Discharge Plan</option>
                        </select>
                    </div>
                    <div class="doctor-care-form-group">
                        <label>S — Subjective</label>
                        <textarea id="doctorUnifiedIpdSoapS" class="form-control" rows="2" placeholder="Patient complaints / subjective details"></textarea>
                    </div>
                    <div class="doctor-care-form-group">
                        <label>O — Objective</label>
                        <textarea id="doctorUnifiedIpdSoapO" class="form-control" rows="2" placeholder="Vitals / clinical findings"></textarea>
                    </div>
                    <div class="doctor-care-form-group">
                        <label>A — Assessment</label>
                        <textarea id="doctorUnifiedIpdSoapA" class="form-control" rows="2" placeholder="Clinical assessment"></textarea>
                    </div>
                    <div class="doctor-care-form-group">
                        <label>P — Plan</label>
                        <textarea id="doctorUnifiedIpdSoapP" class="form-control" rows="2" placeholder="Treatment / plan"></textarea>
                    </div>
                </div>
            </div>

        </div>

        <div class="doctor-care-col-stack">
            <div class="doctor-care-card">
                <div class="doctor-care-card-head">
                    <span class="doctor-care-section-title">&#x1F48A; e-Prescription</span>
                    <button type="button" class="btn btn-success doctor-care-head-action" id="doctorUnifiedAddDrugBtn">+ Add Drug</button>
                </div>
                <div class="doctor-care-card-body">
                    <div id="doctorUnifiedPrescriptionSlot">Loading...</div>
                </div>
            </div>

            @if($canPathology || $canRadiology)
                <div class="doctor-care-card">
                    <div class="doctor-care-card-head">
                        <span class="doctor-care-section-title">&#x1F9EA; Lab Tests</span>
                        <button type="button" class="btn btn-outline-primary doctor-care-head-action" id="doctorUnifiedAddTestBtn">+ Add Test</button>
                    </div>
                    <div class="doctor-care-card-body">
                        <div id="doctorUnifiedTestBlock" class="doctor-care-test-block">
                            <div class="doctor-care-test-grid">
                                <div class="doctor-care-form-group">
                                    <label>Type</label>
                                    <select id="doctorUnifiedTestType" class="form-control">
                                        <option value="">Select Type</option>
                                        @if($canPathology)
                                            <option value="pathology">Pathology</option>
                                        @endif
                                        @if($canRadiology)
                                            <option value="radiology">Radiology</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="doctor-care-form-group">
                                    <label>Priority</label>
                                    <select id="doctorUnifiedTestPriority" class="form-control">
                                        <option value="Routine">Routine</option>
                                        <option value="Urgent">Urgent</option>
                                        <option value="STAT">STAT</option>
                                    </select>
                                </div>
                                <div class="doctor-care-form-group">
                                    <label>Test</label>
                                    <select id="doctorUnifiedTestSelect" class="form-control">
                                        <option value="">Select test</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div id="labTestsAdded" class="doctor-care-test-added">
                            <span class="doctor-care-empty-text">No tests added</span>
                        </div>
                    </div>
                </div>
            @endif

            <div class="doctor-care-card">
                <div class="doctor-care-card-head">
                    <span class="doctor-care-section-title">&#x1FA7A; Vitals</span>
                </div>
                <div class="doctor-care-card-body">
                    <form id="doctorUnifiedIpdVitalsForm">
                        <div class="doctor-care-test-grid">
                            <div class="doctor-care-form-group">
                                <label>Systolic BP</label>
                                <input type="text" name="systolic_bp" class="form-control" value="{{ data_get($allocation, 'systolic_bp', '') }}" placeholder="e.g. 120">
                            </div>
                            <div class="doctor-care-form-group">
                                <label>Diastolic BP</label>
                                <input type="text" name="diastolic_bp" class="form-control" value="{{ data_get($allocation, 'diastolic_bp', '') }}" placeholder="e.g. 80">
                            </div>
                            <div class="doctor-care-form-group">
                                <label>SpO2 (%)</label>
                                <input type="text" name="spo2" class="form-control" value="{{ data_get($allocation, 'spo2', '') }}" placeholder="e.g. 98">
                            </div>
                            <div class="doctor-care-form-group">
                                <label>Pulse (/min)</label>
                                <input type="text" name="pulse" class="form-control" value="{{ data_get($allocation, 'pulse', '') }}" placeholder="e.g. 88">
                            </div>
                            <div class="doctor-care-form-group">
                                <label>RR (/min)</label>
                                <input type="text" name="respiration" class="form-control" value="{{ data_get($allocation, 'respiration', '') }}" placeholder="e.g. 16">
                            </div>
                            <div class="doctor-care-form-group">
                                <label>Temperature (°F)</label>
                                <input type="text" name="temperature" class="form-control" value="{{ data_get($allocation, 'temperature', '') }}" placeholder="e.g. 98.6">
                            </div>
                            <div class="doctor-care-form-group">
                                <label>Weight (kg)</label>
                                <input type="text" name="weight" class="form-control" value="{{ data_get($allocation, 'weight', '') }}" placeholder="e.g. 74">
                            </div>
                            <div class="doctor-care-form-group">
                                <label>Height</label>
                                <input type="text" name="height" class="form-control" value="{{ data_get($allocation, 'height', '') }}" placeholder="cm e.g. 165 or m e.g. 1.65" title="Enter height in centimetres (50–300) or metres (0.5–2.5). BMI updates from height and weight.">
                                <small class="text-muted" style="font-size:10px">BMI fills automatically from weight and height.</small>
                            </div>
                            <div class="doctor-care-form-group">
                                <label>BMI</label>
                                <input type="text" name="bmi" class="form-control" value="{{ data_get($allocation, 'bmi', '') }}" placeholder="e.g. 24.5">
                            </div>
                            <div class="doctor-care-form-group">
                                <label>RBS / Diabetes</label>
                                <input type="text" name="diabetes" class="form-control" value="{{ data_get($allocation, 'diabetes', '') }}" placeholder="e.g. 212">
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div id="doctorUnifiedPathologySlot" class="doctor-care-hidden-slot"></div>
            <div id="doctorUnifiedRadiologySlot" class="doctor-care-hidden-slot"></div>
        </div>
    </div>
</div>
