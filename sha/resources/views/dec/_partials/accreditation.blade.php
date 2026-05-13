@php
   $isedit = false;
   $readonly = '';
   $disabled = '';
   $iseditdec = false;

   if($verification->status == "Physical Verification Completed") {
      $isedit = false;
      $readonly = 'readonly';
      $disabled = 'disabled';
   }

    if(@$hospital->status == 'Empanelment Recommended by DEC' || @$hospital->status == 'Response Required From Facility' || @$hospital->status == 'Empanelment Not Recommended by DEC' || @$hospital->status == 'Approved Upgradation Request' || @$hospital->status == 'Query On Upgradation Request From Facility' || @$hospital->status == 'Rejected Upgradation Request' || @$hospital->status == 'Empanelled') {
        $iseditdec = true; 
    }
@endphp
<div class="card p-0 shadow-none rounded-0  border-bottom">
    <div class="row">
        <h5 class="theme-color mt-3">Accreditation</h5>
        <div class="row"> 
            <div class="table-responsive mt-5 text-nowrap">
                <form id="accreditationform">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Sr No.</th>
                                <th>Hospital Input</th>
                                <th>Verifier Action</th>
                                <th>Remark</th>
                                <th>DEC Recommanded</th>
                                <th>DEC Remark</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0 procedure-body">
                            <tr>
                                <td>1</td>
                                <td>
                                    <b>Accreditation Name:</b> {{@$hospital->hospitalAccreditation->accred->name}} <br>
                                    <b>Valid From: </b> {{@$hospital->hospitalAccreditation->valid_from}} <b>Valid Till: </b> {{@$hospital->hospitalAccreditation->valid_till}}
                                    <br>
                                    <b>Certificate No:</b> {{@$hospital->hospitalAccreditation->certificate_no}} <br>
                                    @php
                                        $hosp = App\Models\Hospitals::where('id', @$hospital->main_hospitalid)->first();
                                        $selected_ids = optional($hospital->hospitalAccreditation)->speciality_ids;
                                        $selected_ids = $selected_ids ? json_decode($selected_ids, true) : [];
                                        if (!empty($selected_ids)) {
                                            if(@$hosp && $hosp->is_upgrade_application == 1) {
                                                $specialities = App\Models\UHospitalSpeciality::join('specialities', 'u_hospital_specialities.speciality_id', '=', 'specialities.id')
                                                    ->whereIn('u_hospital_specialities.id', $selected_ids)
                                                    ->pluck('specialities.name')
                                                    ->toArray();
                                            } else {
                                                $specialities = App\Models\HospitalSpeciality::join('specialities', 'hospital_specialities.speciality_id', '=', 'specialities.id')
                                                    ->whereIn('hospital_specialities.id', $selected_ids)
                                                    ->pluck('specialities.name')
                                                    ->toArray();
                                            }
                                            echo implode(', ', $specialities);
                                        }
                                    @endphp
                                    <br>
                                    <a href="{{ asset('public/storage/'.@$hospital->hospitalAccreditation->certificate) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a>
                                </td>
                                <td>
                                    <div class="form-floating form-floating-outline">
                                        <select disabled class="select2 accreerror" id="dec_verify_status{{@$hospital->hospitalAccreditation->id}}accr" name="dec_verify_status_{{@$hospital->hospitalAccreditation->id}}" required>
                                            <option value="">Select</option>
                                            <option value="Valid" @if(@$hospital->hospitalAccreditation->dec_verify_status == "Valid") selected @endif >Valid</option>
                                            <option value="InValid" @if(@$hospital->hospitalAccreditation->dec_verify_status == "InValid") selected @endif >InValid</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-floating form-floating-outline">
                                        <input type="text"
                                            readonly
                                            id="dec_verify_remark_{{@$hospital->hospitalAccreditation->id}}"
                                            name="dec_verify_remark_{{@$hospital->hospitalAccreditation->id}}"
                                            value="{{@$hospital->hospitalAccreditation->dec_verify_remark}}"
                                            class="form-control accreerror"
                                            oninput="sanitize(this, 'b');" 
                                            placeholder="text"/>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-floating form-floating-outline">
                                        <select class="select2 accreerror" id="dec_status{{@$hospital->hospitalAccreditation->id}}accr" name="dec_status_{{@$hospital->hospitalAccreditation->id}}" required>
                                            <option value="">Select</option>
                                            <option value="Valid" @if(@$hospital->hospitalAccreditation->dec_status == "Valid") selected @endif >Valid</option>
                                            <option value="InValid" @if(@$hospital->hospitalAccreditation->dec_status == "InValid") selected @endif >InValid</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-floating form-floating-outline">
                                        <input type="text"
                                            id="dec_remark_{{@$hospital->hospitalAccreditation->id}}"
                                            name="dec_remark_{{@$hospital->hospitalAccreditation->id}}"
                                            value="{{@$hospital->hospitalAccreditation->dec_remark}}"
                                            class="form-control accreerror"
                                            oninput="sanitize(this, 'b');" 
                                            placeholder="text"/>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    @if(!$iseditdec)
                        <div class="d-flex justify-content-end mt-3">
                            <button type="button" class="btn btn-primary mt-2 rounded-0 saveaccreditationform">Save</button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    
    $('.saveaccreditationform').on('click', function() {
        ldrshow();
        $('.error').remove();
        var step = 7;
        var formData = new FormData($('#accreditationform')[0]);
        
        $.ajax({
            url: '{{route("dec.saveAccreditationReview", [base64_encode($hospital->id), base64_encode($hospital->uuid)])}}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                ldrhide();
                successMessage(response.message);
                $('.nav-link').removeClass('active');
                $('.tab-pane').removeClass('show active');
                $(`.step${step}`).addClass('show active');
                $(`.navstep${step}`).addClass('active');
                setTimeout(() => {
                    $(`.step${step}`).on('click', function(event) {
                        if (event.target.closest('.nav-item .active')) {
                            setSlider(event.target.closest('.nav-item'));
                        }
                    });
                    $('.step6Icon').show();
                    loadStep(7);                  
                }, 1000);
            },
            error: function (xhr) {
                ldrhide();
                $('.error').remove();
                
                if (xhr.status === 422) { 
                let errors = xhr.responseJSON.errors;
                for (let field in errors) {
                    $(`[name="${field}"]`).closest('.accreerror').after(`<div class="error text-danger">${errors[field][0]}</div>`);
                }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });    
    });
</script>