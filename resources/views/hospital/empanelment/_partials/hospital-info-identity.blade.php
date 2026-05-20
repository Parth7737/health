@php
    $om = [];
    if (old('onboarding_meta')) {
        $om = (array) old('onboarding_meta');
    } elseif ($hospital && $hospital->onboarding_meta) {
        $om = (array) $hospital->onboarding_meta;
    }
    $chairmanUser = null;
    if ($hospital) {
        $chairmanUser = \App\Models\User::where('hospital_id', $hospital->id)->whereHas('roles', fn ($q) => $q->where('name', 'Chairman'))->first();
    }
@endphp

<form id="hospitalinfoForm">
    @php
        $wizT = (array) (auth()->user()->wizard_onboarding ?? []);
        $hiddenTypeId = ($hospital && !empty($hospital->type_id)) ? (int) $hospital->type_id : (!empty($wizT['type_id']) ? (int) $wizT['type_id'] : null);
    @endphp
    @if ($hiddenTypeId)
        <input type="hidden" name="type_id" value="{{ $hiddenTypeId }}" />
    @endif
    <div class="eo-panel-title"><i class="fas fa-info-circle" style="color:#60a5fa"></i> Basic information</div>
    <p class="eo-panel-sub">Official facility details as per government records. Fields marked <span class="eo-req">*</span> are required.</p>

    <div class="eo-card">
        <div class="eo-card-hdr">
            <h3 class="eo-card-title"><i class="fas fa-id-card"></i> Facility identity</h3>
        </div>
        <div class="eo-card-body">
            <div class="eo-grid-2">
                <div class="eo-form-group">
                    <label for="name">Official facility name <span class="eo-req">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" oninput="sanitize(this, 'b');"
                        placeholder="e.g. Government District Hospital" value="{{ old('name', $hospital->name ?? '') }}" />
                </div>
                <div class="eo-form-group">
                    <label for="onboarding_meta_local_name">Local / common name</label>
                    <input type="text" id="onboarding_meta_local_name" name="onboarding_meta[local_name]" class="form-control"
                        placeholder="Commonly known as…" value="{{ old('onboarding_meta.local_name', $om['local_name'] ?? '') }}" />
                </div>
            </div>
            <div class="eo-grid-3">
                <div class="eo-form-group">
                    <label for="code">Facility / hospital code <span class="eo-req">*</span></label>
                    <input type="text" id="code" name="code" class="form-control" oninput="sanitize(this, 'b');"
                        placeholder="Registry or internal code" value="{{ old('code', $hospital->code ?? '') }}" />
                    <div class="eo-form-hint">Use the same code you use for reporting and empanelment.</div>
                </div>
                <div class="eo-form-group">
                    <label for="onboarding_meta_establishment_year">Establishment year</label>
                    <input type="number" id="onboarding_meta_establishment_year" name="onboarding_meta[establishment_year]"
                        class="form-control" placeholder="e.g. 1987" min="1800" max="{{ (int) date('Y') }}"
                        value="{{ old('onboarding_meta.establishment_year', $om['establishment_year'] ?? '') }}" />
                </div>
                <div class="eo-form-group">
                    <label for="onboarding_meta_registration_no">Govt. order / registration no.</label>
                    <input type="text" id="onboarding_meta_registration_no" name="onboarding_meta[registration_no]"
                        class="form-control" placeholder="GO / registration number"
                        value="{{ old('onboarding_meta.registration_no', $om['registration_no'] ?? '') }}" />
                </div>
            </div>
            <div class="eo-grid-2">
                <div class="eo-form-group">
                    <label for="onboarding_meta_ownership">Ownership</label>
                    <select class="form-select" id="onboarding_meta_ownership" name="onboarding_meta[ownership]">
                        @php $own = old('onboarding_meta.ownership', $om['ownership'] ?? ''); @endphp
                        <option value="">Select</option>
                        <option value="Government (State)" @selected($own === 'Government (State)')>Government (State)</option>
                        <option value="Government (Central)" @selected($own === 'Government (Central)')>Government (Central)</option>
                        <option value="Local Body" @selected($own === 'Local Body')>Local body / municipal</option>
                        <option value="Private" @selected($own === 'Private')>Private</option>
                        <option value="Trust/NGO" @selected($own === 'Trust/NGO')>Trust / NGO</option>
                        <option value="PPP" @selected($own === 'PPP')>PPP</option>
                    </select>
                </div>
                <div class="eo-form-group">
                    <label for="onboarding_meta_sub_category">Sub-category</label>
                    <select class="form-select" id="onboarding_meta_sub_category" name="onboarding_meta[sub_category]">
                        @php $sub = old('onboarding_meta.sub_category', $om['sub_category'] ?? ''); @endphp
                        <option value="">Select</option>
                        <option value="Urban" @selected($sub === 'Urban')>Urban</option>
                        <option value="Rural" @selected($sub === 'Rural')>Rural</option>
                        <option value="Tribal" @selected($sub === 'Tribal')>Tribal</option>
                        <option value="Hilly/Remote" @selected($sub === 'Hilly/Remote')>Hilly / remote</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="eo-card">
        <div class="eo-card-hdr">
            <h3 class="eo-card-title"><i class="fas fa-map-marker-alt"></i> Location &amp; address</h3>
        </div>
        <div class="eo-card-body">
            <div class="eo-grid-3">
                <div class="eo-form-group">
                    <label for="onboarding_meta_district">District</label>
                    <input type="text" id="onboarding_meta_district" name="onboarding_meta[district]" class="form-control"
                        placeholder="District name" value="{{ old('onboarding_meta.district', $om['district'] ?? '') }}" />
                </div>
                <div class="eo-form-group">
                    <label for="onboarding_meta_block">Block / tehsil</label>
                    <input type="text" id="onboarding_meta_block" name="onboarding_meta[block]" class="form-control"
                        placeholder="Block or tehsil" value="{{ old('onboarding_meta.block', $om['block'] ?? '') }}" />
                </div>
                <div class="eo-form-group">
                    <label for="onboarding_meta_village">Village / town</label>
                    <input type="text" id="onboarding_meta_village" name="onboarding_meta[village]" class="form-control"
                        placeholder="Village or town" value="{{ old('onboarding_meta.village', $om['village'] ?? '') }}" />
                </div>
            </div>
            <div class="eo-form-group">
                <label for="address">Full address <span class="eo-req">*</span></label>
                <textarea class="form-control" id="address" name="address" rows="3"
                    placeholder="Complete postal address including landmark">{{ old('address', $hospital->address ?? '') }}</textarea>
            </div>
            <div class="eo-grid-3">
                <div class="eo-form-group">
                    <label for="city">City <span class="eo-req">*</span></label>
                    <input type="text" id="city" name="city" class="form-control" oninput="sanitize(this, 'b');"
                        placeholder="City" value="{{ old('city', $hospital->city ?? '') }}" />
                </div>
                <div class="eo-form-group">
                    <label for="pincode">PIN code <span class="eo-req">*</span></label>
                    <input type="text" id="pincode" name="pincode" class="form-control" oninput="sanitize(this, 'n');"
                        placeholder="6-digit PIN" maxlength="10" value="{{ old('pincode', $hospital->pincode ?? '') }}" />
                </div>
                <div class="eo-form-group">
                    <label for="landmark">Landmark</label>
                    <input type="text" id="landmark" name="landmark" class="form-control" oninput="sanitize(this, 'b');"
                        placeholder="Nearby landmark" value="{{ old('landmark', $hospital->landmark ?? '') }}" />
                </div>
            </div>
            <div class="eo-grid-3">
                <div class="eo-form-group">
                    <label for="onboarding_meta_latitude">GPS latitude</label>
                    <input type="text" id="onboarding_meta_latitude" name="onboarding_meta[latitude]" class="form-control"
                        placeholder="e.g. 29.5970" value="{{ old('onboarding_meta.latitude', $om['latitude'] ?? '') }}" />
                </div>
                <div class="eo-form-group">
                    <label for="onboarding_meta_longitude">GPS longitude</label>
                    <input type="text" id="onboarding_meta_longitude" name="onboarding_meta[longitude]" class="form-control"
                        placeholder="e.g. 79.6590" value="{{ old('onboarding_meta.longitude', $om['longitude'] ?? '') }}" />
                </div>
                <div class="eo-form-group" style="align-self:flex-end">
                    <button type="button" class="eo-tb-btn" id="eoGpsBtn" onclick="eoFillGps()"><i class="fas fa-crosshairs"></i>
                        Auto-detect GPS</button>
                </div>
            </div>
        </div>
    </div>

    <div class="eo-card">
        <div class="eo-card-hdr">
            <h3 class="eo-card-title"><i class="fas fa-phone-alt"></i> Contact information</h3>
        </div>
        <div class="eo-card-body">
            <div class="eo-grid-3">
                <div class="eo-form-group">
                    <label for="onboarding_meta_ms_name">Medical superintendent / nodal officer name</label>
                    <input type="text" id="onboarding_meta_ms_name" name="onboarding_meta[ms_name]" class="form-control"
                        placeholder="Full name" value="{{ old('onboarding_meta.ms_name', $om['ms_name'] ?? '') }}" />
                </div>
                <div class="eo-form-group">
                    <label for="onboarding_meta_ms_mobile">Official mobile</label>
                    <input type="text" id="onboarding_meta_ms_mobile" name="onboarding_meta[ms_mobile]" class="form-control"
                        maxlength="15" placeholder="10-digit mobile"
                        value="{{ old('onboarding_meta.ms_mobile', $om['ms_mobile'] ?? '') }}" />
                </div>
                <div class="eo-form-group">
                    <label for="hospital_email">Official email <span class="eo-req">*</span></label>
                    <input type="email" id="hospital_email" name="hospital_email" class="form-control"
                        oninput="sanitize(this, 'email');" placeholder="official@gov.in"
                        value="{{ old('hospital_email', $hospital->email ?? '') }}" />
                </div>
            </div>
            <div class="eo-grid-3">
                <div class="eo-form-group">
                    <label for="hospital_phone">Primary phone <span class="eo-req">*</span></label>
                    <input type="text" id="hospital_phone" name="hospital_phone" class="form-control"
                        oninput="sanitize(this, 'n','13');" placeholder="Hospital phone"
                        value="{{ old('hospital_phone', $hospital->phone ?? '') }}" />
                </div>
                <div class="eo-form-group">
                    <label for="onboarding_meta_landline">Landline / STD</label>
                    <input type="text" id="onboarding_meta_landline" name="onboarding_meta[landline]" class="form-control"
                        placeholder="STD number" value="{{ old('onboarding_meta.landline', $om['landline'] ?? '') }}" />
                </div>
                <div class="eo-form-group">
                    <label for="onboarding_meta_helpline">Emergency helpline</label>
                    <input type="text" id="onboarding_meta_helpline" name="onboarding_meta[helpline]" class="form-control"
                        placeholder="Emergency contact" value="{{ old('onboarding_meta.helpline', $om['helpline'] ?? '') }}" />
                </div>
            </div>
            <div class="eo-form-group">
                <label for="onboarding_meta_website">Website (if any)</label>
                <input type="url" id="onboarding_meta_website" name="onboarding_meta[website]" class="form-control"
                    placeholder="https://" value="{{ old('onboarding_meta.website', $om['website'] ?? '') }}" />
            </div>
        </div>
    </div>

    @if (auth()->user()->hospital_type == 'Multi-Branch' && auth()->user()->parent_id == 0)
        <div class="eo-card">
            <div class="eo-card-hdr">
                <h3 class="eo-card-title"><i class="fas fa-user-tie"></i> Chairman / head (multi-branch main)</h3>
            </div>
            <div class="eo-card-body">
                <div class="eo-grid-3">
                    <div class="eo-form-group">
                        <label for="chairman_name">Name <span class="eo-req">*</span></label>
                        <input type="text" id="chairman_name" name="chairman_name" class="form-control"
                            oninput="sanitize(this, 'b');" placeholder="Chairman / head name"
                            value="{{ old('chairman_name', $chairmanUser->name ?? '') }}" />
                    </div>
                    <div class="eo-form-group">
                        <label for="chairman_email">Email <span class="eo-req">*</span></label>
                        <input type="email" id="chairman_email" name="chairman_email" class="form-control"
                            oninput="sanitize(this, 'email');" placeholder="Chairman email"
                            value="{{ old('chairman_email', $chairmanUser->email ?? '') }}" />
                    </div>
                    <div class="eo-form-group">
                        <label for="password">Password @if (!($chairmanUser?->id))
                                <span class="eo-req">*</span>
                            @endif </label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Leave blank to keep current" />
                    </div>
                    <div class="eo-form-group">
                        <label for="confirmation_password">Confirm password @if (!($chairmanUser?->id))
                                <span class="eo-req">*</span>
                            @endif </label>
                        <input type="password" id="confirmation_password" name="confirmation_password" class="form-control" />
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (@$hospital->status == 'Draft' || !@$hospital || @$hospital->status == 'Rejected')
        <div class="eo-step-nav">
            <button type="button" class="eo-tb-btn" onclick="if(typeof loadStep==='function')loadStep(1)"><i class="fas fa-arrow-left"></i> Back</button>
            <span class="eo-nav-info">Save to continue to infrastructure.</span>
            <button type="button" class="eo-tb-btn primary savehospitalinfo"><i class="fas fa-save"></i> Save &amp;
                continue</button>
        </div>
    @endif
</form>

<script>
    function eoFillGps() {
        if (!navigator.geolocation) {
            alert('Geolocation is not supported by this browser.');
            return;
        }
        navigator.geolocation.getCurrentPosition(function(pos) {
            document.getElementById('onboarding_meta_latitude').value = pos.coords.latitude.toFixed(6);
            document.getElementById('onboarding_meta_longitude').value = pos.coords.longitude.toFixed(6);
        }, function() {
            alert('Unable to read location. Enter coordinates manually.');
        });
    }

    $('.savehospitalinfo').off('click').on('click', function() {
        ldrshow();
        $('.error').remove();
        var formData = new FormData($('#hospitalinfoForm')[0]);
        $.ajax({
            url: "{{ route('hospital.empanelmentRegistration.hospitalinfo', [$uuid]) }}",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                ldrhide();
                if (response.success) {
                    successMessage(response.message);
                    var ws = response.wizard_step || response.step;
                    if (typeof window.eoSetUnlockedMax === 'function') {
                        window.eoSetUnlockedMax(ws);
                    }
                    $('.nav-link').removeClass('active');
                    $('.tab-pane').removeClass('show active');
                    $(`.step${ws}`).addClass('show active');
                    $(`.navstep${ws}`).addClass('active');
                    if (typeof window.eoUpdateStepper === 'function') {
                        window.eoUpdateStepper(ws);
                    }
                    setTimeout(function() {
                        loadStep(ws);
                    }, 600);
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            },
            error: function(xhr) {
                ldrhide();
                $('.error').remove();
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = [];
                    for (let field in errors) {
                        let bracketName = field.split('.').reduce(function(acc, part, i) {
                            if (i === 0) {
                                return part;
                            }
                            return acc + '[' + part + ']';
                        });
                        let $el = $('input, select, textarea').filter(function() {
                            return this.name === bracketName || this.name === field;
                        }).first();
                        $el.closest('.eo-form-group').append(
                            `<div class="error text-danger small mt-1">${errors[field][0]}</div>`);
                        errorMessages.push(errors[field][0]);
                    }
                    if (errorMessages.length > 0) {
                        errorMessage(errorMessages.join('<br>'));
                    }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });
    });
</script>
