@php
    $isedit = false;
    $readonly = '';
   $iseditsec = false;
@endphp
@if($verification->status == "Physical Verification Completed")
    @php($isedit = true)
    @php($readonly = 'readonly')
@endif

@if(@$hospital->status == 'Empanelled' || @$hospital->status == 'Query Raised by SEC' || @$hospital->status == 'Queried' || @$hospital->status == 'Rejected')
    @php($iseditsec = true)
@endif
<div class="card mb-6 p-0">
    <div class="card-header">
        Establishment Details
    </div>                                
    <div class="card-body">
        <div class="row row-cols-4">
            <div class="col">
                <div class="infodata">
                    <label>Name of the Facility</label>
                    <p><strong>{{ @$hospital->facility_name }}</strong></p>
                    <label>Facility Type</label>
                    <p><strong>{{ @$hospital->facilityType->name }}</strong></p>
                    <label>Facility Speciality Type</label>
                    <p><strong>{{ @$hospital->facilitySpecialityType->name }}</strong></p>
                    <label>Facility Ownership Type</label>
                    <p><strong>{{ @$hospital->facilityOwnershipType->name }}</strong></p>    
                    <label class="mb-3">Government Benefits/Concessions</label>
                    <p><strong>{{ @$hospital->govermentBenefits->name }}</strong></p>    
                                  
                </div>
            </div>
            <div class="col">
                <div class="infodata">                            
                    <label>Facility Ownership Sub Type - 1</label>
                    <p><strong>{{ @$hospital->facilityOwnershipSubType1->name }}</strong></p>                           
                    <label>Facility Ownership Sub Type - 2</label>
                    <p><strong>{{ @$hospital->facilityOwnershipSubType2->name }}</strong></p>
                    <label>Facility Registration Certificate</label>
                    <p><strong>{{ @$hospital->facilityRegistrationCertificate->name }}</strong></p>
                    <label>System(s) of Medicine</label>
                    <p><strong>{{ @$hospital->systemMedicine->name }}</strong></p>                                                
                </div>
            </div>
            <div class="col">
                <div class="infodata">
                    
                    <label>Does this facility has PG/DNB?</label>
                    <p><strong>{{ @$hospital->pg_dnb ? 'Yes' : 'No' }}</strong></p>
                    <label>Facility Registration Number</label>
                    <p><strong>{{ @$hospital->facility_registration_number }}</strong></p>
                    <label>Registration Certificate Expiry Date</label>
                    <p><strong>{{ @$hospital->registration_certificate_expiry }}</strong></p>
                    <label>Establishment Year</label>
                    <p><strong>{{ @$hospital->date_of_establishment }}</strong></p>                            
                </div>
            </div>
            <div class="col">
                <div class="infodata">
                    @if(@$hospital->sub_type_certificate_name && @$hospital->sub_type_certificate)
                        <label class="mt-2"><strong>{{@$hospital->sub_type_certificate_name}}</strong>&nbsp; <br><a href="{{ asset('public/storage/'.@$hospital->sub_type_certificate) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a></label> <br>
                    @endif
                    @if(@$hospital->facilityOwnershipSubType3->name)
                        <label>Facility Ownership Sub Type - 3</label>
                        <p><strong>{{ @$hospital->facilityOwnershipSubType3->name }}</strong></p>    
                    @endif      
                    @if(@$hospital->rohini_id)
                        <label>ROHINI ID</label>
                        <p><strong>{{ @$hospital->rohini_id }}</strong></p>
                    @endif  
                    @if(@$hospital->name_od_group)
                        <label>Group Name</label>
                        <p><strong>{{ @$hospital->name_od_group }}</strong></p>
                    @endif
                    @if(@$hospital->group_id)
                        <label>Group ID</label>
                        <p><strong>{{ @$hospital->group_id }}</strong></p>
                    @endif
                </div>
            </div>
        </div>
        <div class="row mb-2 ">
            <div class="col-md-2">
                <div class="infodata">
                    <label class="mb-3">Scheme</label>
                    <p><strong> {{@$hospital->schemeType->name}}</strong></p>   
                </div>
            </div>
            <h5 class="theme-color mt-3">Hospital Gallery</h5>
            @if(@$hospital->image)
                <div class="col-md-1">
                    <div class="infodata">
                        <a href="{{ asset('public/storage/'.@$hospital->image) }}" target="_blank" class="btn btn-outline-primary btn-sm">View</a></label>
                    </div>
                </div>
            @endif   
            @foreach(@$hospital->images as $key => $value)
                <div class="col-md-1">
                    <div class="infodata">
                        <a href="{{ asset('public/storage/'.@$value->image) }}" target="_blank" class="btn btn-outline-primary btn-sm">View</a></label>
                    </div>
                </div>
            @endforeach 
            @if(@$hospital->hospital_ppt)
                <div class="col-md-2">
                    <div class="infodata">
                        <a href="{{ asset('public/storage/'.@$hospital->hospital_ppt) }}" target="_blank" class="btn btn-outline-primary btn-sm">Hospital PPT</a></label>
                    </div>
                </div>
            @endif    
        </div>
        <div class="row"> 
            <div class=" mt-5 ">
                <form id="establishmentForm">
                    <table class="table table-responsive table-bordered">
                        <thead class="table-dark">
                            <tr>    
                                <td>Physical Verifier</td>
                                <td>Remark</td>
                                <td>Dec Officer</td>
                                <td>Dec Remark</td>
                                <td>Sec Officer</td>
                                <td>Sec Remark</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="form-floating form-floating-outline">
                                        <select class="select2 eerror" id="dec_verify_statussestablish" name="dec_verify_status" disabled required>
                                            <option value="">Select</option>
                                            <option value="Valid" @if(@$hospital->dec_verify_status == "Valid") selected @endif >Valid</option>
                                            <option value="InValid"  @if(@$hospital->dec_verify_status == "InValid") selected @endif >InValid</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-floating form-floating-outline">
                                        <input type="text"
                                            id="dec_verify_remark"
                                            oninput="sanitize(this, 'b');"
                                            readonly
                                            name="dec_verify_remark"
                                            value="{{@$hospital->dec_verify_remark}}"
                                            class="form-control eerror"
                                            placeholder="text"/>
                                    </div>
                                </td>

                                <td>
                                    <div class="form-floating form-floating-outline">
                                        <select  class="select2 eerror" disabled id="dec_statussestablish" disabled name="dec_status" required>
                                            <option value="">Select</option>
                                            <option value="Valid" @if(@$hospital->dec_status == "Valid") selected @endif >Valid</option>
                                            <option value="InValid"  @if(@$hospital->dec_status == "InValid") selected @endif >InValid</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-floating form-floating-outline">
                                        <input type="text"
                                            readonly
                                            id="dec_remark"
                                            oninput="sanitize(this, 'b');"
                                            name="dec_remark"
                                            value="{{@$hospital->dec_remark}}"
                                            class="form-control eerror"
                                            placeholder="text"/>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-floating form-floating-outline">
                                        <select  class="select2 eerror" id="sec_statussestablish" name="sec_status" required>
                                            <option value="">Select</option>
                                            <option value="Valid" @if(@$hospital->sec_status == "Valid") selected @endif >Valid</option>
                                            <option value="InValid"  @if(@$hospital->sec_status == "InValid") selected @endif >InValid</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-floating form-floating-outline">
                                        <input type="text"
                                            id="sec_remark"
                                            oninput="sanitize(this, 'b');"
                                            name="sec_remark"
                                            value="{{@$hospital->sec_remark}}"
                                            class="form-control eerror"
                                            placeholder="text"/>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    @if(!$iseditsec)
                    <div class="d-flex justify-content-end mt-3">
                    <button type="button" class="btn mt-2 btn-primary rounded-0 saveestablishmentForm" >Save</button>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
<div class="card mb-6 p-0">
    <div class="card-header">Address Details</div>                                
    <div class="card-body">
        <div class="row row-cols-5">
            <div class="col">
                <div class="infodata">
                    <label>Address</label>
                    <p><strong>{{ @$hospital->hospitalAddress->address }}</strong></p>
                    <label>City/Town</label>
                    <p><strong>{{ @$hospital->hospitalAddress->city }}</strong></p>
                    <label>District</label>
                    <p><strong>{{ @$hospital->hospitalAddress->districts->name }}</strong></p>
                    <label>Mobile No</label>
                    <p><strong>{{ @$hospital->hospitalAddress->mobile_no }}</strong></p>                                           
                </div>
            </div>
            <div class="col">
                <div class="infodata">
                    <label>Pincode</label>
                    <p><strong>{{ @$hospital->hospitalAddress->pincode }}</strong></p>
                    <label>Block</label>
                    <p><strong>{{ @$hospital->hospitalAddress->blockdata->name }}</strong></p>
                    <label>Telephone with STD Code</label>
                    <p><strong>{{ @$hospital->hospitalAddress->std_code }}{{ @$hospital->hospitalAddress->telephone }}</strong></p>
                    
                </div>
            </div>
            <div class="col">
                <div class="infodata">
                    <label>Village</label>
                    <p><strong>{{@$hospital->hospitalAddress->villages->name }}</strong></p>
                    <label>State</label>
                    <p><strong>{{ @$hospital->hospitalAddress->states->name }}</strong></p>
                    <label>Email ID</label>
                    <p><strong>{{ @$hospital->hospitalAddress->email  }}</strong></p>
                </div>
            </div>
            <div class="col">
                <div class="infodata">
                    <label>LandMark</label>
                    <p><strong>{{ @$hospital->hospitalAddress->landmark }}</strong></p>
                    <label>Website</label>
                    <p><strong>{{ @$hospital->hospitalAddress->website }}</strong></p>
                    <label>Local Police Station</label>
                    <p><strong>{{ @$hospital->hospitalAddress->police_station }}</strong></p>
                    <label>Locality</label>
                    <p><strong>{{ @$hospital->hospitalAddress->locality }}</strong></p>
                </div>
            </div>
            <div class="col">
                <div class="infodata">
                    <label>Latitude</label>
                    <p><strong>{{ @$hospital->hospitalAddress->latitude }}</strong></p>
                    <label>Longitude</label>
                    <p><strong>{{ @$hospital->hospitalAddress->longitude }}</strong></p>
                </div>
            </div>
        </div>

        <div class="row"> 
            <div class=" mt-5 ">
                <form id="addressForm">
                    <table class="table table-responsive table-bordered">
                        <thead class="table-dark">
                            <tr>    
                                <td>Physical Verifier</td>
                                <td>Remark</td>
                                <td>Dec Officer</td>
                                <td>Dec Remark</td>
                                <td>Sec Officer</td>
                                <td>Sec Remark</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="form-floating form-floating-outline">
                                        <select class="select2 aerror" disabled id="dec_verify_statusaddress" name="dec_verify_status"  required>
                                            <option value="">Select</option>
                                            <option value="Valid" @if(@$hospital->hospitalAddress->dec_verify_status == "Valid") selected @endif >Valid</option>
                                            <option value="InValid" @if(@$hospital->hospitalAddress->dec_verify_status == "InValid") selected @endif >InValid</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-floating form-floating-outline">
                                        <input type="text"
                                            id="dec_verify_remark"
                                            readonly
                                            name="dec_verify_remark"
                                            value="{{@$hospital->hospitalAddress->dec_verify_remark}}"
                                            class="form-control aerror"
                                            placeholder="text"/>
                                    </div>
                                </td>

                                <td>
                                    <div class="form-floating form-floating-outline">
                                        <select class="select2 eerror" disabled id="dec_statussaddress" name="dec_status" required>
                                            <option value="">Select</option>
                                            <option value="Valid" @if(@$hospital->hospitalAddress->dec_status == "Valid") selected @endif >Valid</option>
                                            <option value="InValid"  @if(@$hospital->hospitalAddress->dec_status == "InValid") selected @endif >InValid</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-floating form-floating-outline">
                                        <input type="text"
                                            readonly
                                            id="dec_remark"
                                            name="dec_remark"
                                            value="{{@$hospital->hospitalAddress->dec_remark}}"
                                            class="form-control eerror"
                                            placeholder="text"/>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-floating form-floating-outline">
                                        <select  class="select2 aerror" id="sec_statusaddress" name="sec_status" required>
                                            <option value="">Select</option>
                                            <option value="Valid" @if(@$hospital->hospitalAddress->sec_status == "Valid") selected @endif >Valid</option>
                                            <option value="InValid"  @if(@$hospital->hospitalAddress->sec_status == "InValid") selected @endif >InValid</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-floating form-floating-outline">
                                        <input type="text"
                                            id="sec_remark"
                                            oninput="sanitize(this, 'b');"
                                            name="sec_remark"
                                            value="{{@$hospital->hospitalAddress->sec_remark}}"
                                            class="form-control aerror"
                                            placeholder="text"/>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    @if(!$iseditsec)
                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn mt-2 btn-primary rounded-0 saveaddressForm">Save</button>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    $('.saveestablishmentForm').on('click', function() {
        
        ldrshow();
        $('.error').remove();
       
        var formData = new FormData($('#establishmentForm')[0]);
        
        $.ajax({
            url: '{{route("sec.saveEstablishmentReview", [base64_encode($hospital->id), base64_encode($hospital->uuid)])}}',
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
                if(response.isComplete) {
                    CheckbasicStepCompleteOrNot(2, true);
                } else {
                    CheckbasicStepCompleteOrNot(1, false);
                }
            },
            error: function (xhr) {
                ldrhide();
                $('.error').remove();
                
                if (xhr.status === 422) { 
                let errors = xhr.responseJSON.errors;
                for (let field in errors) {
                    if ($(`select[name="${field}"]`).length > 0) {
                        $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                    } else {
                        $(`[name="${field}"]`).closest('.eerror').after(`<div class="error text-danger">${errors[field][0]}</div>`);
                    }
                }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });    
    });

    $('.saveaddressForm').on('click', function() {
        
        ldrshow();
        $('.error').remove();
       
        var formData = new FormData($('#addressForm')[0]);
        
        $.ajax({
            url: '{{route("sec.saveAddressReview", [base64_encode($hospital->id), base64_encode($hospital->uuid)])}}',
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
                if(response.isComplete) {
                    CheckbasicStepCompleteOrNot(2, true);
                } else {
                    CheckbasicStepCompleteOrNot(1, false);
                }
            },
            error: function (xhr) {
                ldrhide();
                $('.error').remove();
                
                if (xhr.status === 422) { 
                let errors = xhr.responseJSON.errors;
                for (let field in errors) {
                    if ($(`select[name="${field}"]`).length > 0) {
                        $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                    } else {
                        $(`[name="${field}"]`).closest('.aerror').after(`<div class="error text-danger">${errors[field][0]}</div>`);
                    }
                }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });    
    });

    function CheckbasicStepCompleteOrNot(step, isLoad = 0) {
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
			$(`.step${(step-1)}Icon`).show();
			if(isLoad) {
				$(`.step${step}Icon`).hide();
				loadStep(step);
			}
		}, 1000);
	}
</script>