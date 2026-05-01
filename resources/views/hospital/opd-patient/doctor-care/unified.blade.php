<div class="doctor-care-unified-modal">
    @include('hospital.doctor-care.partials.unified-shell-styles')

    <div class="doctor-care-chip">
        <div class="doctor-care-chip-left">
            <div class="doctor-care-chip-avatar">{{ strtoupper(substr($patient?->name ?? 'P', 0, 1)) }}</div>
            <div style="min-width:0;">
                <p class="doctor-care-chip-name">{{ $patient?->name ?? '-' }} <span style="font-size:11px;font-weight:400;color:#6e86a1;">{{ $patient?->patient_id ? '- ' . $patient->patient_id : '' }}</span></p>
                <p class="doctor-care-chip-meta">
                    {{ ($patient?->age_years ?? 0) }} Yrs / {{ $patient?->gender ?? '-' }}
                    | Token: {{ str_pad((string) ($visit->token_no ?? ''), 3, '0', STR_PAD_LEFT) }}
                    | {{ $visit->department?->name ?? 'General Medicine OPD' }}
                </p>
            </div>
        </div>
        <div class="doctor-care-chip-right">
            <!-- <span class="doctor-care-chip-status">Urgent</span> -->
            <!-- <a href="{{ url('hospital/opd-patient/visits/1') }}" class="btn btn-secondary btn-xs" title="Patient History">📋</a> -->
        </div>
    </div>

    <form id="doctorUnifiedVitalsForm" data-store-url="{{ route('hospital.opd-patient.vitals-social.update', ['opdPatient' => $visit->id]) }}">
        <div class="doctor-care-grid">
            <div class="doctor-care-col-stack">
                <div class="doctor-care-card">
                    <div class="doctor-care-card-head"><span class="doctor-care-section-title">📋 SOAP Notes</span></div>
                    <div class="doctor-care-card-body">
                        <div class="doctor-care-form-group">
                            <label>Subjective - Chief Complaint</label>
                            <textarea class="form-control" name="subjective_notes" rows="2" placeholder="Patient's main complaint in their own words...">{{ $visit->subjective_notes }}</textarea>
                        </div>
                        <div class="doctor-care-form-group">
                            <label>Objective - Examination Findings</label>
                            <textarea class="form-control" name="objective_notes" rows="2" placeholder="BP, HR, Temp, O2 Sat, examination findings...">{{ $visit->objective_notes }}</textarea>
                        </div>
                        <div class="doctor-care-form-group">
                            <label>Assessment - Diagnosis</label>
                            <textarea class="form-control" name="assessment_notes" rows="2" placeholder="Primary and differential diagnosis...">{{ $visit->assessment_notes }}</textarea>
                        </div>
                        <div class="doctor-care-form-group">
                            <label>Plan - Treatment Plan</label>
                            <textarea class="form-control" name="plan_notes" rows="2" placeholder="Treatment plan, follow-up instructions, referrals...">{{ $visit->plan_notes }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="doctor-care-card">
                    <div class="doctor-care-card-head"><span class="doctor-care-section-title">❤️ Vitals</span></div>
                    <div class="doctor-care-card-body">
                        <div class="doctor-care-vitals-grid">
                            <div class="doctor-care-form-group">
                                <label>BP Systolic</label>
                                <input type="number" name="systolic_bp" min="0" max="400" class="form-control" value="{{ $visit->systolic_bp }}" placeholder="120">
                            </div>
                            <div class="doctor-care-form-group">
                                <label>BP Diastolic</label>
                                <input type="number" name="diastolic_bp" min="0" max="400" class="form-control" value="{{ $visit->diastolic_bp }}" placeholder="80">
                            </div>
                            <div class="doctor-care-form-group">
                                <label>Pulse (/min)</label>
                                <input type="number" name="pluse" min="0" max="999" class="form-control" value="{{ $visit->pluse }}" placeholder="72">
                            </div>
                            <div class="doctor-care-form-group">
                                <label>Temp</label>
                                <input type="number" name="temperature" min="0" max="200" step="0.1" class="form-control" value="{{ $visit->temperature }}" placeholder="98.6">
                            </div>
                            <div class="doctor-care-form-group">
                                <label>SpO2 (%)</label>
                                <input type="number" name="spo2" min="0" max="100" class="form-control" value="{{ $visit->spo2 }}" placeholder="99">
                            </div>
                            <div class="doctor-care-form-group">
                                <label>RR (/min)</label>
                                <input type="number" name="respiration" min="0" max="999" class="form-control" value="{{ $visit->respiration }}" placeholder="16">
                            </div>
                            <div class="doctor-care-form-group">
                                <label>Weight (kg)</label>
                                <input type="number" name="weight" min="0" max="1000" step="0.1" class="form-control" value="{{ $visit->weight }}" placeholder="65">
                            </div>
                            <div class="doctor-care-form-group">
                                <label>Height (m)</label>
                                <input type="number" name="height" min="0" max="20" step="0.01" class="form-control" value="{{ $visit->height }}" placeholder="1.70">
                            </div>
                            <div class="doctor-care-form-group">
                                <label>BMI</label>
                                <input type="number" name="bmi" min="0" max="200" step="0.1" class="form-control" value="{{ $visit->bmi }}" placeholder="22.5">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="doctor-care-col-stack">
                <div class="doctor-care-card">
                    <div class="doctor-care-card-head">
                        <span class="doctor-care-section-title">💊 e-Prescription</span>
                        <button type="button" class="btn btn-success doctor-care-head-action" id="doctorUnifiedAddDrugBtn">+ Add Drug</button>
                    </div>
                    <div class="doctor-care-card-body">
                        <div id="doctorUnifiedPrescriptionSlot" class="doctor-care-slot-note">Loading prescription form...</div>
                    </div>
                </div>

                @if ($canPathology || $canRadiology)
                    <div class="doctor-care-card">
                        <div class="doctor-care-card-head">
                            <span class="doctor-care-section-title">🧪 Lab Tests</span>
                            <button type="button" class="btn btn-outline-primary doctor-care-head-action" id="doctorUnifiedAddTestBtn">+ Add Test</button>
                        </div>
                        <div class="doctor-care-card-body">
                            <div id="doctorUnifiedTestBlock" class="doctor-care-test-block">
                                <div class="doctor-care-test-grid">
                                    <div class="doctor-care-form-group">
                                        <label>Type</label>
                                        <select id="doctorUnifiedTestType" class="form-control">
                                            <option value="">Select Type</option>
                                            @if ($canPathology)
                                                <option value="pathology">Pathology</option>
                                            @endif
                                            @if ($canRadiology)
                                                <option value="radiology">Radiology</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="doctor-care-form-group" id="doctorUnifiedPriorityWrap" style="display:none;">
                                        <label>Priority</label>
                                        <select id="doctorUnifiedTestPriority" class="form-control">
                                            <option value="Routine" selected>Routine</option>
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

                <div id="doctorUnifiedPathologySlot" class="doctor-care-hidden-slot"></div>
                <div id="doctorUnifiedRadiologySlot" class="doctor-care-hidden-slot"></div>

                <!-- <div class="doctor-care-card">
                    <div class="doctor-care-card-head"><span class="doctor-care-section-title">📋 Advice & Follow-up</span></div>
                    <div class="doctor-care-card-body">
                        <div class="doctor-care-form-group">
                            <label>Patient Instructions</label>
                            <textarea class="form-control" name="patient_instructions" rows="2" placeholder="Diet, rest, restrictions...">{{ $visit->patient_instructions }}</textarea>
                        </div>
                        <div class="doctor-care-vitals-grid" style="grid-template-columns: repeat(2, minmax(0, 1fr));">
                            <div class="doctor-care-form-group">
                                <label>Follow-up Date</label>
                                <input type="date" class="form-control" name="follow_up_date" value="{{ $visit->follow_up_date ? \Carbon\Carbon::parse($visit->follow_up_date)->format('Y-m-d') : '' }}">
                            </div>
                            <div class="doctor-care-form-group">
                                <label>Disposition</label>
                                <select class="form-control" name="disposition">
                                    <option value="">Select</option>
                                    <option value="Discharge - OPD" @selected(($visit->disposition ?? '') === 'Discharge - OPD')>Discharge - OPD</option>
                                    <option value="Admit to IPD" @selected(($visit->disposition ?? '') === 'Admit to IPD')>Admit to IPD</option>
                                    <option value="Refer to Specialist" @selected(($visit->disposition ?? '') === 'Refer to Specialist')>Refer to Specialist</option>
                                    <option value="Emergency Referral" @selected(($visit->disposition ?? '') === 'Emergency Referral')>Emergency Referral</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
    </form>

    <div class="doctor-care-actions">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-outline-primary" id="doctorCareSaveDraftBtn">Save Draft</button>
        <button type="button" class="btn btn-success" id="doctorCareSaveAllBtn">Complete &amp; Print</button>
    </div>
</div>
