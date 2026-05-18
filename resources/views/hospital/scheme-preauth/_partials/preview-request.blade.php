<div class="spa-preview-content">
    <div class="row g-2 spa-preview-columns">
        <div class="col-lg-6">
            <section class="spa-preview-section">
                <h6 class="spa-preview-section-title">General Information</h6>
                <div class="spa-preview-kv-grid spa-preview-kv-grid--2">
                    <div class="spa-preview-kv"><span class="spa-preview-kv-label">Temp (°F)</span><span class="spa-preview-kv-value">{{ @$general_info->temprature ?? '—' }}</span></div>
                    <div class="spa-preview-kv"><span class="spa-preview-kv-label">Pulse</span><span class="spa-preview-kv-value">{{ @$general_info->pulserate ?? '—' }}</span></div>
                    <div class="spa-preview-kv"><span class="spa-preview-kv-label">Height</span><span class="spa-preview-kv-value">{{ @$general_info->height ?? '—' }} cm</span></div>
                    <div class="spa-preview-kv"><span class="spa-preview-kv-label">Weight</span><span class="spa-preview-kv-value">{{ @$general_info->weight ?? '—' }} kg</span></div>
                    <div class="spa-preview-kv"><span class="spa-preview-kv-label">BMI</span><span class="spa-preview-kv-value">{{ @$general_info->bmi ?? '—' }}</span></div>
                    <div class="spa-preview-kv"><span class="spa-preview-kv-label">Cyanosis</span><span class="spa-preview-kv-value">{{ @$general_info->cyanosis ?? '—' }}</span></div>
                    <div class="spa-preview-kv"><span class="spa-preview-kv-label">Pallor</span><span class="spa-preview-kv-value">{{ @$general_info->pallor ?? '—' }}</span></div>
                    <div class="spa-preview-kv"><span class="spa-preview-kv-label">Malnutrition</span><span class="spa-preview-kv-value">{{ @$general_info->malnutration ?? '—' }}</span></div>
                    <div class="spa-preview-kv"><span class="spa-preview-kv-label">Oedema</span><span class="spa-preview-kv-value">{{ @$general_info->oedema ?? '—' }}</span></div>
                </div>
            </section>

            <section class="spa-preview-section">
                <h6 class="spa-preview-section-title">Family History</h6>
                <div class="spa-preview-kv-grid spa-preview-kv-grid--2">
                    <div class="spa-preview-kv"><span class="spa-preview-kv-label">Diabetes</span><span class="spa-preview-kv-value">{{ @$family_history->diabetes->name ?? '—' }}</span></div>
                    <div class="spa-preview-kv"><span class="spa-preview-kv-label">Hypertension</span><span class="spa-preview-kv-value">{{ @$family_history->hypertension->name ?? '—' }}</span></div>
                    <div class="spa-preview-kv"><span class="spa-preview-kv-label">Heart</span><span class="spa-preview-kv-value">{{ @$family_history->heartdisease->name ?? '—' }}</span></div>
                    <div class="spa-preview-kv"><span class="spa-preview-kv-label">Stroke</span><span class="spa-preview-kv-value">{{ @$family_history->stroke->name ?? '—' }}</span></div>
                    <div class="spa-preview-kv"><span class="spa-preview-kv-label">Cancer</span><span class="spa-preview-kv-value">{{ @$family_history->cancer->name ?? '—' }}</span></div>
                    <div class="spa-preview-kv"><span class="spa-preview-kv-label">TB</span><span class="spa-preview-kv-value">{{ @$family_history->tuberculosis->name ?? '—' }}</span></div>
                    <div class="spa-preview-kv"><span class="spa-preview-kv-label">Asthma</span><span class="spa-preview-kv-value">{{ @$family_history->asthma->name ?? '—' }}</span></div>
                </div>
            </section>
        </div>

        <div class="col-lg-6">
            <section class="spa-preview-section">
                <h6 class="spa-preview-section-title">Personal History</h6>
                <div class="spa-preview-kv-grid spa-preview-kv-grid--2">
                    <div class="spa-preview-kv"><span class="spa-preview-kv-label">Appetite</span><span class="spa-preview-kv-value">{{ @$personal_history->appetite->name ?? '—' }}</span></div>
                    <div class="spa-preview-kv"><span class="spa-preview-kv-label">Bowels</span><span class="spa-preview-kv-value">{{ @$personal_history->bowels->name ?? @$personal_history->bowel->name ?? '—' }}</span></div>
                    <div class="spa-preview-kv"><span class="spa-preview-kv-label">Nutrition</span><span class="spa-preview-kv-value">{{ @$personal_history->nutrition->name ?? '—' }}</span></div>
                    <div class="spa-preview-kv"><span class="spa-preview-kv-label">Diet</span><span class="spa-preview-kv-value">{{ @$personal_history->diet->name ?? '—' }}</span></div>
                    <div class="spa-preview-kv spa-preview-kv--wide"><span class="spa-preview-kv-label">Allergies</span><span class="spa-preview-kv-value">{{ @$personal_history->known_allergies ?? '—' }}</span></div>
                    <div class="spa-preview-kv spa-preview-kv--wide"><span class="spa-preview-kv-label">Allergy detail</span><span class="spa-preview-kv-value">{{ @$personal_history->allergy_detail ?? '—' }}</span></div>
                    <div class="spa-preview-kv"><span class="spa-preview-kv-label">Habits</span><span class="spa-preview-kv-value">{{ @$personal_history->habits ?? '—' }}</span></div>
                    <div class="spa-preview-kv spa-preview-kv--wide"><span class="spa-preview-kv-label">Habit detail</span><span class="spa-preview-kv-value">{{ @$personal_history->habits_detail ?? '—' }}</span></div>
                </div>
            </section>

            <section class="spa-preview-section">
                <h6 class="spa-preview-section-title">Authentication &amp; Admission</h6>
                <div class="spa-preview-kv-grid spa-preview-kv-grid--2">
                    @if(@$authentication_consent->hospital_declaration_form)
                    <div class="spa-preview-kv spa-preview-kv--wide">
                        <span class="spa-preview-kv-label">Declaration</span>
                        <span class="spa-preview-kv-value"><a href="{{ asset('public/storage/'.@$authentication_consent->hospital_declaration_form) }}" target="_blank" class="btn btn-outline-primary btn-sm py-0 px-2">View</a></span>
                    </div>
                    @endif
                    <div class="spa-preview-kv spa-preview-kv--wide"><span class="spa-preview-kv-label">Remarks</span><span class="spa-preview-kv-value">{{ @$authentication_consent->remarks ?? '—' }}</span></div>
                    <div class="spa-preview-kv"><span class="spa-preview-kv-label">Admit date</span><span class="spa-preview-kv-value">{{ @$admission_details->admission_date ?? '—' }}</span></div>
                    <div class="spa-preview-kv"><span class="spa-preview-kv-label">Surgery date</span><span class="spa-preview-kv-value">{{ @$admission_details->surgery_date ?? '—' }}</span></div>
                    <div class="spa-preview-kv"><span class="spa-preview-kv-label">Admit type</span><span class="spa-preview-kv-value">{{ @$admission_details->admission_type->name ?? '—' }}</span></div>
                    <div class="spa-preview-kv"><span class="spa-preview-kv-label">MLC</span><span class="spa-preview-kv-value">{{ @$admission_details->legal_case ?? '—' }}</span></div>
                    @if(@$admission_details->fir_doc)
                    <div class="spa-preview-kv"><span class="spa-preview-kv-label">FIR</span><span class="spa-preview-kv-value"><a href="{{ asset('public/storage/'.@$admission_details->fir_doc) }}" target="_blank" class="btn btn-outline-primary btn-sm py-0 px-2">View</a></span></div>
                    @endif
                </div>
            </section>
        </div>
    </div>

    <section class="spa-preview-section">
        <h6 class="spa-preview-section-title">Diagnosis</h6>
        <div class="table-responsive spa-preauth-compact-table-wrap">
            <table class="table table-sm spa-preauth-compact-table mb-0">
                <thead class="table-dark">
                    <tr><th>No.</th><th>Code</th><th>Description</th><th>Type</th></tr>
                </thead>
                <tbody>
                    @include('hospital.scheme-preauth._partials.diagnosis', ['preauth_diagnosis' => @$preauth_diagnosis, 'is_action_hide' => 1])
                </tbody>
            </table>
        </div>
    </section>

    <section class="spa-preview-section">
        <h6 class="spa-preview-section-title">Treatment Plan</h6>
        <div class="table-responsive spa-procedures-table-wrap">
            <table class="table table-sm spa-procedures-table mb-0">
                <thead class="table-dark">
                    <tr><th>No.</th><th>Spec.</th><th>Procedure</th><th>Strat.</th><th>Days</th><th>Amt</th><th>ICHI</th></tr>
                </thead>
                <tbody>
                    @include('hospital.scheme-preauth._partials.procedures', ['procedures' => @$procedures, 'is_action_hide' => 1])
                </tbody>
            </table>
        </div>
    </section>

    <section class="spa-preview-section">
        <h6 class="spa-preview-section-title">Investigations</h6>
        @if(@$investigations && $investigations->count() > 0)
        <div class="spa-preview-kv-grid spa-preview-kv-grid--3">
            @foreach(@$investigations as $investigation)
            <div class="spa-preview-kv">
                <span class="spa-preview-kv-label">{{ \Illuminate\Support\Str::limit(@$investigation->investigation->name ?? 'Doc', 36) }}</span>
                <span class="spa-preview-kv-value">
                    @if(@$investigation->file)
                    <a href="{{ asset('public/storage/'.@$investigation->file) }}" target="_blank" class="btn btn-outline-primary btn-sm py-0 px-2">View</a>
                    @else
                    <span class="text-muted">—</span>
                    @endif
                </span>
            </div>
            @endforeach
        </div>
        @else
        <p class="spa-preview-empty mb-0">No investigations.</p>
        @endif
    </section>

    @if($bed_side_photo || $clinical_notes || $any_other_doc)
    <section class="spa-preview-section">
        <h6 class="spa-preview-section-title">Enhancement docs</h6>
        <div class="spa-preview-kv-grid spa-preview-kv-grid--3">
            @if($bed_side_photo)
            <div class="spa-preview-kv"><span class="spa-preview-kv-label">{{ $bed_side_photo->name }}</span><span class="spa-preview-kv-value"><a href="{{ asset('public/storage/'.$bed_side_photo->file) }}" target="_blank" class="btn btn-outline-primary btn-sm py-0 px-2">View</a></span></div>
            @endif
            @if($clinical_notes)
            <div class="spa-preview-kv"><span class="spa-preview-kv-label">{{ $clinical_notes->name }}</span><span class="spa-preview-kv-value"><a href="{{ asset('public/storage/'.$clinical_notes->file) }}" target="_blank" class="btn btn-outline-primary btn-sm py-0 px-2">View</a></span></div>
            @endif
            @if($any_other_doc)
            <div class="spa-preview-kv"><span class="spa-preview-kv-label">{{ $any_other_doc->name }}</span><span class="spa-preview-kv-value"><a href="{{ asset('public/storage/'.$any_other_doc->file) }}" target="_blank" class="btn btn-outline-primary btn-sm py-0 px-2">View</a></span></div>
            @endif
        </div>
    </section>
    @endif

    <section class="spa-preview-section">
        <h6 class="spa-preview-section-title">Care Team</h6>
        <div class="table-responsive spa-preauth-compact-table-wrap">
            <table class="table table-sm spa-preauth-compact-table mb-0">
                <thead class="table-dark">
                    <tr><th>No.</th><th>Doctor</th><th>Reg. ID</th><th>Spec.</th><th>Contact</th></tr>
                </thead>
                <tbody>
                    @include('hospital.scheme-preauth._partials.teams', ['preauth_teams' => @$preauth_teams, 'is_action_hide' => 1])
                </tbody>
            </table>
        </div>
    </section>

    <section class="spa-preview-section spa-preview-section--last">
        <h6 class="spa-preview-section-title">Finance</h6>
        <div class="table-responsive spa-preauth-compact-table-wrap">
            <table class="table table-sm spa-preauth-compact-table mb-0">
                <thead class="table-dark">
                    <tr><th>#</th><th>Pkg</th><th>Type</th><th>Strat</th><th>Qty</th><th>Cost</th><th>Adj</th><th>Inc.</th><th>Total</th></tr>
                </thead>
                <tbody>
                    @include('hospital.scheme-preauth._partials.finance', ['procedures' => @$procedures])
                </tbody>
            </table>
        </div>
        <ul class="spa-preview-finance-totals list-unstyled mb-0">
            @include('hospital.scheme-preauth._partials.finance-total', ['procedures' => @$procedures])
        </ul>
    </section>
</div>
