<div class="doctor-care-unified-modal">
    <style>
        .doctor-care-unified-modal {
            background: #ffffff;
            min-height: 100%;
            padding: 0;
        }

        .doctor-care-chip {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            border: 1px solid #d6e4f3;
            border-radius: 10px;
            background: #f8fbff;
            padding: 10px 12px;
            margin-bottom: 14px;
        }

        .doctor-care-chip-left {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .doctor-care-chip-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1f64b8, #9ec6f2);
            color: #0b355e;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 34px;
            font-size: 13px;
        }

        .doctor-care-chip-name {
            margin: 0;
            font-size: 15px;
            font-weight: 700;
            color: #0d1b2a;
            line-height: 1.2;
        }

        .doctor-care-chip-meta {
            margin: 2px 0 0;
            font-size: 11px;
            color: #61809f;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        .doctor-care-chip-status {
            border: 1px solid #f8d4a2;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
            color: #8a4b00;
            background: #fff6e9;
            padding: 3px 9px;
            white-space: nowrap;
        }

        .doctor-care-chip-right {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .doctor-care-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            align-items: start;
        }

        .doctor-care-card {
            border: 1px solid #dbe8f5;
            border-radius: 11px;
            overflow: hidden;
            background: #ffffff;
        }

        .doctor-care-card-head {
            border-bottom: 1px solid #e3edf8;
            padding: 10px 14px;
            font-size: 14px;
            font-weight: 700;
            color: #0d1b2a;
            background: #f8fbff;
            min-height: 44px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .doctor-care-card-body {
            padding: 12px 14px;
        }

        .doctor-care-col-stack {
            display: grid;
            gap: 14px;
        }

        .doctor-care-form-group {
            margin-bottom: 10px;
        }

        .doctor-care-form-group:last-child {
            margin-bottom: 0;
        }

        .doctor-care-form-group label {
            font-size: 12px;
            color: #2c4460;
            font-weight: 600;
            margin-bottom: 4px;
            display: block;
        }

        .doctor-care-unified-modal .form-control {
            height: 34px;
            padding: 7px 10px;
            font-size: 12px;
            line-height: 1.4;
            border: 1px solid #cfdceb;
            border-radius: 5px;
            color: #0d1b2a;
            background: #ffffff;
        }

        .doctor-care-unified-modal select.form-control {
            padding-right: 28px;
        }

        .doctor-care-unified-modal textarea.form-control {
            height: auto;
            min-height: 58px;
            padding-top: 7px;
            padding-bottom: 7px;
            resize: vertical;
        }

        .doctor-care-unified-modal .btn-xs {
            height: 24px;
            min-height: 24px;
            padding: 3px 10px;
            font-size: 11px;
            border-radius: 4px;
            line-height: 1;
        }

        .doctor-care-unified-modal .btn-sm {
            height: 28px;
            min-height: 28px;
            padding: 5px 12px;
            font-size: 11.5px;
            line-height: 1;
        }

        .doctor-care-unified-modal .doctor-care-actions .btn {
            height: 34px;
            padding: 7px 14px;
            font-size: 12px;
            line-height: 1;
        }

        .doctor-care-section-title {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .doctor-care-head-action {
            font-size: 11px;
            font-weight: 700;
            border-radius: 4px;
            line-height: 1;
            padding: 5px 9px;
            height: 24px;
        }

        .doctor-care-vitals-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .doctor-care-summary {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px 12px;
            font-size: 12px;
        }

        .doctor-care-summary strong {
            color: #113a63;
            margin-right: 4px;
        }

        .doctor-care-slot-note {
            font-size: 12px;
            color: #5e7d9d;
            margin-bottom: 8px;
        }

        .doctor-care-test-block {
            border: 1px solid #d6e4f1;
            border-radius: 8px;
            background: #f8fbff;
            padding: 10px;
            margin-bottom: 10px;
            display: none;
        }

        .doctor-care-test-block.is-open {
            display: block;
        }

        .doctor-care-test-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
            align-items: end;
        }

        .doctor-care-test-grid .doctor-care-form-group {
            margin-bottom: 0;
        }

        .doctor-care-test-added {
            padding: 8px 10px;
            min-height: 40px;
            font-size: 12px;
            color: #6a84a0;
            border: 1px solid #d6e4f1;
            border-radius: 6px;
            background: #ffffff;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .doctor-care-test-added .doctor-care-empty-text {
            color: #7a93ad;
        }

        .doctor-care-test-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            background: #e7f1ff;
            color: #0f56a5;
            border: 1px solid #bfd7f5;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 10px;
        }

        .doctor-care-test-remove {
            border: 0;
            background: transparent;
            color: #0f56a5;
            font-size: 11px;
            line-height: 1;
            padding: 0;
            cursor: pointer;
            font-weight: 700;
        }

        .doctor-care-hidden-slot {
            display: none;
        }

        .doctor-care-unified-modal #doctorUnifiedPrescriptionSlot .prescription-workspace,
        .doctor-care-unified-modal #doctorUnifiedPathologySlot .prescription-workspace,
        .doctor-care-unified-modal #doctorUnifiedRadiologySlot .prescription-workspace {
            background: transparent;
            border-radius: 0;
            padding: 0;
        }

        .doctor-care-unified-modal #doctorUnifiedPrescriptionSlot .prescription-meta-card,
        .doctor-care-unified-modal #doctorUnifiedPathologySlot .prescription-meta-card,
        .doctor-care-unified-modal #doctorUnifiedRadiologySlot .prescription-meta-card {
            border: 0;
            border-radius: 0;
            padding: 0;
            background: transparent;
        }

        .doctor-care-unified-modal #doctorUnifiedPrescriptionSlot .prescription-items-shell {
            overflow-x: auto;
        }

        .doctor-care-unified-modal #doctorUnifiedPrescriptionSlot .prescription-entry-grid {
            display: none !important;
            /* grid-template-columns: minmax(180px, 2.2fr) minmax(110px, 1.2fr) minmax(120px, 1.3fr) minmax(110px, 1.2fr) minmax(70px, 0.8fr) auto !important; */
            gap: 6px !important;
            /* align-items: end !important; */
            margin-bottom: 8px;
            min-width: 740px;
            grid-auto-flow: column !important;
        }

        .doctor-care-unified-modal #doctorUnifiedPrescriptionSlot .prescription-entry-grid.is-open {
            display: grid !important;
            /* grid-template-columns: minmax(180px, 2.2fr) minmax(110px, 1.2fr) minmax(120px, 1.3fr) minmax(110px, 1.2fr) minmax(70px, 0.8fr) auto !important; */
        }

        .doctor-care-unified-modal #doctorUnifiedPrescriptionSlot .prescription-entry-grid>div {
            min-width: 0 !important;
            grid-column: auto !important;
        }

        .doctor-care-unified-modal #doctorUnifiedPrescriptionSlot .prescription-entry-grid .form-label {
            font-size: 11px;
            margin-bottom: 3px !important;
            min-height: 16px;
        }

        .doctor-care-unified-modal #doctorUnifiedPrescriptionSlot .prescription-entry-grid .form-control,
        .doctor-care-unified-modal #doctorUnifiedPrescriptionSlot .prescription-entry-grid .form-select {
            height: 32px;
            font-size: 11.5px;
            border-color: #cfdceb;
        }

        .doctor-care-unified-modal #doctorUnifiedPrescriptionSlot .prescription-entry-actions .d-flex {
            height: 32px;
            align-items: center;
        }

        .doctor-care-unified-modal #doctorUnifiedPrescriptionSlot .prescription-entry-actions .prescription-icon-btn {
            width: 32px;
            height: 32px;
        }

        .doctor-care-unified-modal #doctorUnifiedPrescriptionSlot #prescriptionItemsTable {
            border: 1px solid #d6e4f1;
            border-radius: 7px;
            overflow: hidden;
            font-size: 11px;
        }

        .doctor-care-unified-modal #doctorUnifiedPrescriptionSlot #prescriptionItemsTable thead th {
            background: #edf3f9;
            color: #56718d;
            border-color: #d6e4f1;
            text-transform: uppercase;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .02em;
            padding: 7px 8px;
        }

        .doctor-care-unified-modal #doctorUnifiedPrescriptionSlot #prescriptionItemsTable tbody td {
            border-color: #e4edf6;
            padding: 7px 8px;
            font-size: 11px;
        }

        .doctor-care-unified-modal #doctorUnifiedPrescriptionSlot .prescription-row-actions .btn {
            width: 24px;
            height: 24px;
            padding: 0;
            border-radius: 4px;
            font-size: 11px;
        }

        .doctor-care-unified-modal #doctorUnifiedPrescriptionSlot .prescription-notes-grid {
            display: none;
        }

        .doctor-care-entry-flash {
            animation: doctor-care-entry-flash .6s ease;
        }

        @keyframes doctor-care-entry-flash {
            0% {
                box-shadow: 0 0 0 0 rgba(44, 109, 182, 0.35);
            }

            100% {
                box-shadow: 0 0 0 10px rgba(44, 109, 182, 0);
            }
        }

        .doctor-care-unified-modal .doctor-unified-diagnostic-form .form-label {
            font-size: 11px;
            margin-bottom: 4px;
            color: #2c4460;
            font-weight: 600;
        }

        .doctor-care-unified-modal .doctor-unified-diagnostic-form .table th,
        .doctor-care-unified-modal .doctor-unified-diagnostic-form .table td {
            font-size: 11px;
            padding: 6px 8px;
        }

        .doctor-care-actions {
            margin-top: 16px;
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            border-top: 1px solid #e3edf8;
            padding-top: 10px;
            background: #ffffff;
        }

        @media (max-width: 991.98px) {
            .doctor-care-grid {
                grid-template-columns: 1fr;
            }

            .doctor-care-unified-modal #doctorUnifiedPrescriptionSlot .prescription-entry-grid,
            .doctor-care-unified-modal #doctorUnifiedPrescriptionSlot .prescription-entry-grid.is-open {
                min-width: 0;
                grid-template-columns: 1fr !important;
            }

            .doctor-care-vitals-grid,
            .doctor-care-summary,
            .doctor-care-test-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

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
                                    <div class="doctor-care-form-group">
                                        <label>Test</label>
                                        <select id="doctorUnifiedTestSelect" class="form-control">
                                            <option value="">Select test</option>
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

                <div class="doctor-care-card">
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
                </div>
            </div>
        </div>
    </form>

    <div class="doctor-care-actions">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-outline-primary" id="doctorCareSaveDraftBtn">Save Draft</button>
        <button type="button" class="btn btn-success" id="doctorCareSaveAllBtn">Complete &amp; Print</button>
    </div>
</div>
