@php
   $isedit = false;
   $readonly = '';
   $disabled = '';

   if($verification->status == "Physical Verification Completed") {
      $isedit = true;
      $readonly = 'readonly';
      $disabled = 'disabled';
   }
@endphp
<div class="card p-0 shadow-none rounded-0  border-bottom">
    <div class="row">       
        <div class="alert alert-info mb-0 rounded-0">General Service Human Resource</div>
        <div class="row">       
            <h6 class="theme-color mt-3">Head Of the Organization/CEO</h6>
            <div class="row">
                <div class="col-md-3">
                    <div class="infodata">
                        <label><strong>Name</strong></label>
                        <p>{{ @$hospital->ceo->name }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="infodata">
                        <label><strong>Designation</strong></label>
                        <p>{{ @$hospital->ceo->designation }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="infodata">
                        <label><strong>Email ID</strong></label>
                        <p>{{ @$hospital->ceo->email }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="infodata">
                        <label><strong>Mobile No</strong></label>
                        <p>{{ @$hospital->ceo->mobile_no }}</p>
                    </div>
                </div>
                <div class=" mt-5 ">
                    <form id="ceoForm">
                        <table class="table table-responsive table-bordered">
                            <thead class="table-dark">
                                <tr>    
                                    <td>Physical Verifier</td>
                                    <td>Remark</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="form-floating form-floating-outline">
                                            <select {{$disabled}} class="select2 ceoerror" id="dec_verify_statusceo" name="dec_verify_status" required>
                                                <option value="">Select</option>
                                                <option value="Valid" @if(@$hospital->ceo->dec_verify_status == "Valid") selected @endif >Valid</option>
                                                <option value="InValid" @if(@$hospital->ceo->dec_verify_status == "InValid") selected @endif >InValid</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-floating form-floating-outline">
                                            <input type="text"
                                                {{$readonly}}
                                                id="dec_verify_remark"
                                                oninput="sanitize(this, 'b');"
                                                name="dec_verify_remark"
                                                value="{{@$hospital->ceo->dec_verify_remark}}"
                                                class="form-control ceoerror"
                                                placeholder="text"/>
                                        </div>
                                    </td>
                                </tr>
                               
                            </tbody>
                        </table>
                        @if(!$isedit)
                            <div class="d-flex justify-content-end mt-3">
                                <button type="button" class="btn btn-primary rounded-0 saveceoForm">Save</button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
            <hr>
            <h6 class="theme-color mt-3">Hospital Admin/Nodal Officer</h6>
            <div class="row">
                <div class="col-md-4">
                    <div class="infodata">
                        <label><strong>Name</strong></label>
                        <p>{{ @$hospital->user->name }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="infodata">
                        <label><strong>Mobile No</strong></label>
                        <p>{{ @$hospital->user->mobile_no }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="infodata">
                        <label><strong>Email ID</strong></label>
                        <p>{{ @$hospital->user->email }}</p>
                    </div>
                </div>
            </div>
            <hr>
            <h6 class="theme-color mt-3">Medical Human Resource</h6>
            <div class="row">
                <div class="table-responsive mt-5 text-nowrap">
                    <form id="mhrform">
                        <table class="table table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Sr No.</th>
                                    <th>Hospital Input</th>
                                    <th>Verifier Action</th>
                                    <th>Remark</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0 procedure-body">
                                @foreach($hospital->humanResources()->where('type', 'mhr')->get() as $key => $value)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>
                                        <b>Type of humanresource</b>: {{@$value->type_of_human_resource}}</b> <br>  
                                        <b>Sub Type of humanresource</b>: {{@$value->humanResource->name}}</b> <br> 
                                        <b>Registration Number</b>: {{@$value->registration_number}} <br>
                                        <b>Name</b>: {{@$value->name}} <br>
                                        <b>Mobile No</b>: {{@$value->mobile_no}} <br>
                                        @if(@$value->registration_certificate)
                                            <a href="{{ asset('public/storage/'.@$value->registration_certificate) }}" target="_blank" class="btn btn-outline-primary btn-sm me-2">Registration Certificate</a>
                                        @endif
                                        @if(@$value->declaration_certificate)
                                            <a href="{{ asset('public/storage/'.@$value->declaration_certificate) }}" target="_blank" class="btn btn-outline-primary btn-sm">Declaration Certificate</a>
                                        @endif
                                        </td>
                                        <td>
                                            <div class="form-floating form-floating-outline">
                                                <select {{$disabled}} class="select2 mhrerror" id="dec_verify_status{{@$value->id}}medical" name="dec_verify_status_{{@$value->id}}" required>
                                                    <option value="">Select</option>
                                                    <option value="Valid" @if(@$value->dec_verify_status == "Valid") selected @endif >Valid</option>
                                                    <option value="InValid" @if(@$value->dec_verify_status == "InValid") selected @endif >InValid</option>
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-floating form-floating-outline">
                                                <input type="text"
                                                    {{ $readonly }}
                                                    id="dec_verify_remark_{{@$value->id}}"
                                                    oninput="sanitize(this, 'b');"
                                                    name="dec_verify_remark_{{@$value->id}}"
                                                    value="{{@$value->dec_verify_remark}}"
                                                    class="form-control mhrerror"
                                                    placeholder="text"/>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if(!$isedit)
                            <div class="d-flex justify-content-end mt-3">
                              <button type="button" class="btn btn-primary mt-2 rounded-0 savemhrForm">Save</button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
            <hr>
            <h6 class="theme-color mt-3">Non Medical Human Resource</h6>
            <div class="row">
                <div class="table-responsive mt-5 text-nowrap">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Sr No.</th>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0 procedure-body">
                            <tr>
                                <td>1</td>
                                <td>Medico</td>
                                <td>{{@$hospital->medico_count}}</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>House Keepingg</td>
                                <td>{{@$hospital->house_keeping}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <hr>
        <div class="alert alert-info mb-0 rounded-0">Support Service Human Resource</div>
        <div class="row"> 
            <div class="table-responsive mt-5 text-nowrap">
                <form id="sshrform">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Sr No.</th>
                                <th>Hospital Input</th>
                                <th>Verifier Action</th>
                                <th>Remark</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0 procedure-body">
                            @foreach($hospital->humanResources()->where('type', 'sshr')->get() as $key => $value)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>
                                        <b>Type of humanresource</b>: {{@$value->type_of_human_resource}}</b> <br>  
                                        <b>Sub Type of humanresource</b>: {{@$value->humanResource->name}}</b> <br> 
                                        <b>Registration Number</b>: {{@$value->registration_number}} <br>
                                        <b>Name</b>: {{@$value->name}} <br>
                                        <b>Mobile No</b>: {{@$value->mobile_no}}  <br>
                                      
                                        @if(@$value->registration_certificate)
                                            <a href="{{ asset('public/storage/'.@$value->registration_certificate) }}" target="_blank" class="btn btn-outline-primary btn-sm me-2">Registration Certificate</a>
                                        @endif
                                        @if(@$value->declaration_certificate)
                                            <a href="{{ asset('public/storage/'.@$value->declaration_certificate) }}" target="_blank" class="btn btn-outline-primary btn-sm">Declaration Certificate</a>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="form-floating form-floating-outline">
                                            <select {{$disabled}} class="select2 sshrerror" id="dec_verify_status{{@$value->id}}supportservice" name="dec_verify_status_{{@$value->id}}" required>
                                                <option value="">Select</option>
                                                <option value="Valid" @if(@$value->dec_verify_status == "Valid") selected @endif >Valid</option>
                                                <option value="InValid" @if(@$value->dec_verify_status == "InValid") selected @endif >InValid</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-floating form-floating-outline">
                                            <input {{$readonly}} type="text"
                                                id="dec_verify_remark_{{@$value->id}}"
                                                oninput="sanitize(this, 'b');"
                                                name="dec_verify_remark_{{@$value->id}}"
                                                value="{{@$value->dec_verify_remark}}"
                                                class="form-control sshrerror"
                                                placeholder="text"/>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if(!$isedit)
                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn btn-primary mt-2 rounded-0 savesshrForm">Save</button>
                    </div>
                    @endif
                </form>
            </div>
        </div>
        <hr>
        <div class="alert alert-info mb-0 rounded-0">Specialist</div>
        <div class="row"> 
            <div class="table-responsive mt-5 text-nowrap">
                <form id="speccform">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Sr No.</th>
                                <th>Hospital Input</th>                         
                                <th>Verifier Action</th>
                                <th>Remark</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0 procedure-body">
                            @foreach($hospital->hospitalTeam as $key => $value)
                                <tr id="hrrow{{$value->id}}">
                                    <td>{{$loop->iteration}}</td>
                                    <td>
                                        <b>Designation:</b> {{@$value->designation}} <br>
                                        <b>Name:</b> {{@$value->name}} <br>
                                        <b>Mobile:</b> {{@$value->mobile}} <br>
                                        <b>Speiality Name:</b> {{@$value->speciality->name}} <br>
                                        <b>Registration Certificate Expiry</b>: {{@$value->registration_certificate_expiry}} <br>
                                        @if(@$value->registration_certificate)
                                            <a href="{{ asset('public/storage/'.@$value->registration_certificate) }}" target="_blank" class="btn btn-outline-primary btn-sm me-2">Registration Certificate</a>
                                        @endif
                                        @if(@$value->declaration_certificate)
                                            <a href="{{ asset('public/storage/'.@$value->declaration_certificate) }}" target="_blank" class="btn btn-outline-primary btn-sm">Declaration Certificate</a>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="form-floating form-floating-outline">
                                            <select {{$disabled}} class="select2 specerror" id="dec_verify_status{{@$value->id}}specility" name="dec_verify_status_{{@$value->id}}" required>
                                                <option value="">Select</option>
                                                <option value="Valid" @if(@$value->dec_verify_status == "Valid") selected @endif >Valid</option>
                                                <option value="InValid" @if(@$value->dec_verify_status == "InValid") selected @endif >InValid</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-floating form-floating-outline">
                                            <input {{$readonly}} type="text"
                                                id="dec_verify_remark_{{@$value->id}}"
                                                oninput="sanitize(this, 'b');"
                                                name="dec_verify_remark_{{@$value->id}}"
                                                value="{{@$value->dec_verify_remark}}"
                                                class="form-control specerror"
                                                placeholder="text"/>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if(!$isedit)
                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn btn-primary mt-2 rounded-0 savespecForm">Save</button>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $('.saveceoForm').on('click', function() {        
        ldrshow();
        $('.error').remove();
       
        var formData = new FormData($('#ceoForm')[0]);
        
        $.ajax({
            url: '{{route("decverifier.saveCEOReview", [base64_encode($hospital->id), base64_encode($hospital->uuid)])}}',
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
                    CheckHumanResourceCompleteOrNot(6, true);
                } else {
                    CheckHumanResourceCompleteOrNot(5, false);
                }
            },
            error: function (xhr) {
                ldrhide();
                $('.error').remove();
                
                if (xhr.status === 422) { 
                let errors = xhr.responseJSON.errors;
                for (let field in errors) {
                    $(`[name="${field}"]`).closest('.ceoerror').after(`<div class="error text-danger">${errors[field][0]}</div>`);
                }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });    
    });

    $('.savemhrForm').on('click', function() {        
        ldrshow();
        $('.error').remove();
       
        var formData = new FormData($('#mhrform')[0]);
        
        $.ajax({
            url: '{{route("decverifier.saveMHRReview", [base64_encode($hospital->id), base64_encode($hospital->uuid)])}}',
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
                    CheckHumanResourceCompleteOrNot(6, true);
                } else {
                    CheckHumanResourceCompleteOrNot(5, false);
                }
            },
            error: function (xhr) {
                ldrhide();
                $('.error').remove();
                
                if (xhr.status === 422) { 
                let errors = xhr.responseJSON.errors;
                for (let field in errors) {
                    $(`[name="${field}"]`).closest('.mhrerror').after(`<div class="error text-danger">${errors[field][0]}</div>`);
                }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });    
    });

    $('.savesshrForm').on('click', function() {        
        ldrshow();
        $('.error').remove();
       
        var formData = new FormData($('#sshrform')[0]);
        
        $.ajax({
            url: '{{route("decverifier.saveSSHRReview", [base64_encode($hospital->id), base64_encode($hospital->uuid)])}}',
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
                    CheckHumanResourceCompleteOrNot(6, true);
                } else {
                    CheckHumanResourceCompleteOrNot(5, false);
                }
            },
            error: function (xhr) {
                ldrhide();
                $('.error').remove();
                
                if (xhr.status === 422) { 
                let errors = xhr.responseJSON.errors;
                for (let field in errors) {
                    $(`[name="${field}"]`).closest('.sshrerror').after(`<div class="error text-danger">${errors[field][0]}</div>`);
                }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });    
    });

    $('.savespecForm').on('click', function() {
        ldrshow();
        $('.error').remove();
       
        var formData = new FormData($('#speccform')[0]);
        
        $.ajax({
            url: '{{route("decverifier.saveSPECReview", [base64_encode($hospital->id), base64_encode($hospital->uuid)])}}',
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
                    CheckHumanResourceCompleteOrNot(6, true);
                } else {
                    CheckHumanResourceCompleteOrNot(5, false);
                }
            },
            error: function (xhr) {
                ldrhide();
                $('.error').remove();
                
                if (xhr.status === 422) { 
                let errors = xhr.responseJSON.errors;
                for (let field in errors) {
                    $(`[name="${field}"]`).closest('.specerror').after(`<div class="error text-danger">${errors[field][0]}</div>`);
                }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });    
    });

    function CheckHumanResourceCompleteOrNot(step, isLoad = 0) {
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