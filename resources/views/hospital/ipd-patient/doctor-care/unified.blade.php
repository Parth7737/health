<div class="doctor-care-unified-modal">
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
                                    <label>Test</label>
                                    <select id="doctorUnifiedTestSelect" class="form-control">
                                        <option value="">Select test</option>
                                    </select>
                                </div>
                                <div class="doctor-care-form-group">
                                    <label>Priority <span class="text-danger">*</span></label>
                                    <select id="doctorUnifiedTestPriority" class="form-control">
                                        <option value="Routine">Routine</option>
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
                                <label>Pulse</label>
                                <input type="text" name="pulse" class="form-control" value="{{ data_get($allocation, 'pulse', '') }}" placeholder="e.g. 88">
                            </div>
                            <div class="doctor-care-form-group">
                                <label>Temperature</label>
                                <input type="text" name="temperature" class="form-control" value="{{ data_get($allocation, 'temperature', '') }}" placeholder="e.g. 98.6">
                            </div>
                            <div class="doctor-care-form-group">
                                <label>Weight</label>
                                <input type="text" name="weight" class="form-control" value="{{ data_get($allocation, 'weight', '') }}" placeholder="e.g. 74">
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
