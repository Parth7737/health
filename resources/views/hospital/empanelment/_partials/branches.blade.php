

<form id="branchForm">
    @if(@$hospital->status == "Draft")
   <div id="branches">
    <div class="inside-left-info-box mb-3">
         <h4 class="colored-verticle-title">
            Branch Hospital Information
         </h4>
         <div class="row">
               <div class="col-md-4">
                  <div class="form-floating form-floating-outline mb-6">
                     <input type="text" id="name" oninput="sanitize(this, 'b');" name="name" class="form-control" placeholder="Hospital Name" value="" />
                     <label for="name">Hospital Name<span class="text-danger">*</span></label>
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="form-floating form-floating-outline mb-6">
                     <input type="text" id="code" oninput="sanitize(this, 'b');" name="code" class="form-control" placeholder="Hospital Code" value="" />
                     <label for="code">Hospital Code<span class="text-danger">*</span></label>
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="form-floating form-floating-outline mb-6">
                     @php $types = App\CentralLogics\Helpers::getCommanData('HospitalType'); @endphp
                     <select class="form-select select2"
                           id="type_id"
                           name="type_id">
                           <option value="">Select</option>
                           @foreach($types as $type)
                              <option value="{{ $type->id }}">{{ $type->name }}</option>
                           @endforeach
                     </select>
                     <label for="type_id">Type<span class="text-danger">*</label>
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="form-floating form-floating-outline mb-6">
                     <input type="text" id="hospital_phone" oninput="sanitize(this, 'n','13');" name="hospital_phone" class="form-control" placeholder="Hospital Phone" value="" />
                     <label for="hospital_phone">Hospital Phone<span class="text-danger">*</span></label>
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="form-floating form-floating-outline mb-6">
                     <input type="email" id="hospital_email" oninput="sanitize(this, 'email');" name="hospital_email" class="form-control" placeholder="Hospital Email" value="" />
                     <label for="hospital_email">Hospital Email<span class="text-danger">*</span></label>
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="form-floating form-floating-outline mb-6">
                     <input type="number" id="pincode" oninput="sanitize(this, 'n');" name="pincode" class="form-control" placeholder="Pincode" value="" />
                     <label for="pincode">Pincode<span class="text-danger">*</span></label>
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="form-floating form-floating-outline mb-6">
                     <input type="text" id="city" oninput="sanitize(this, 'b');" name="city" class="form-control" placeholder="City" value="" />
                     <label for="city">City<span class="text-danger">*</span></label>
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="form-floating form-floating-outline mb-6">
                     <input type="text" id="landmark" oninput="sanitize(this, 'b');" name="landmark" class="form-control" placeholder="Landmark" value="" />
                     <label for="landmark">Landmark</label>
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="form-floating form-floating-outline mb-6">
                     <textarea class="form-control h-px-100" id="address" name="address" placeholder=""></textarea>
                     <label for="address">Hospital Address<span class="text-danger">*</span></label>
                  </div>
               </div>
         </div>
         <h4 class="colored-verticle-title">
            Hospital Admin Information
         </h4>
         
         <div class="row">
               <div class="col-md-3">
                  <div class="form-floating form-floating-outline mb-6">
                     <input type="text" id="admin_name" oninput="sanitize(this, 'b');" name="admin_name" class="form-control" placeholder="Admin Name" value="" />
                     <label for="admin_name">Admin<span class="text-danger">*</span></label>
                  </div>
               </div>
               <div class="col-md-3">
                  <div class="form-floating form-floating-outline mb-6">
                     <input type="email" id="admin_email" oninput="sanitize(this, 'email');" name="admin_email" class="form-control" placeholder="Admin Email" value="" />
                     <label for="admin_email">Admin/Head Email<span class="text-danger">*</span></label>
                  </div>
               </div>
               <div class="col-md-3">
                  <div class="form-floating form-floating-outline mb-6">
                     <input type="password" id="password" oninput="sanitize(this, 'b');" name="password" class="form-control" placeholder="" value="" />
                     <label for="password">Password<span class="text-danger">*</span></label>
                  </div>
               </div>
               <div class="col-md-3">
                  <div class="form-floating form-floating-outline mb-6">
                     <input type="password" id="confirmation_password" oninput="sanitize(this, 'b');" name="confirmation_password" class="form-control" placeholder="" value="" />
                     <label for="confirmation_password">Confirm Password<span class="text-danger">*</span></label>
                  </div>
               </div>
         </div>
    </div>
   </div>
        <div class="col-md-12 mt-2">
            <div
                class="d-flex justify-content-end">
                <button type="button"
                class="btn btn-primary branchesFormSave">SAVE BRANCH</button>
            </div>
        </div>
    @endif
</form>
@if($allStepCompleted)
    @php
        $finalsave = true
    @endphp
@else
    @php
        $finalsave = false
    @endphp
@endif

@if(@$hospital->status == "Draft")
    <div class="card mb-6 mt-3 p-0 finalsave @if($finalsave)  @else d-none @endif">
        <div class="card-header">
        <label for="is_accept"><input type="checkbox" name="is_accept" id="is_accept">&nbsp;&nbsp;<strong>I hereby declare that all information provided in this empanelment form is true, accurate, and complete to the best of my knowledge. I understand that any false or missing information may lead to rejection of this application or termination of empanelment, and may be subject to legal consequenses as per applicable laws and regulations.</strong></label>
        </div>
        <div class="card-body">
            <button type="button" class="btn btn-outline-primary rounded-0 prevsubmit "  >SUBMIT</button>
        </div>
    </div>
@else
    <div class="card mb-6 mt-3 p-0 finalsave @if($finalsave) @else d-none @endif">
        <div class="card-header">
        <label for="is_accept">Application is submitted!!</label>
        </div>
       
    </div>               
@endif
<table class="table table-bordered mt-4">
    <thead class="table-dark">
        <tr>
            <th>Sr No.</th>
            <th>Hospital Name</th>
            <th>Hospital Code</th>
            <th>Type</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody class="table-border-bottom-0 branchtable">
        
    </tbody>
</table>


<script>
   
    $(document).ready(function() {
        loadBrachTable();
    });

	$(document).ready(function() {

        $(".prevsubmit").on('click', function() {
            if (!$("#is_accept").is(":checked")) {
                swal({
                    title: "Declaration Required",
                    text: "You must accept the declaration before submitting.",
                    type: "error",
                    buttons: {
                        confirm: {
                            text: "Ok",
                            className: "btn btn-danger",
                        },
                    },
                });
                return; // Stop execution if checkbox is not checked
            }

            swal({
                title: "Confirm Submission?",
                text: 'Are you sure you want to submit and proceed with the payment?',
                type: "warning",
                buttons: {
                    cancel: {
                        visible: true,
                        text: "No, cancel!",
                        className: "btn btn-danger",
                    },
                    confirm: {
                        text: "Yes!",
                        className: "btn btn-success",
                    },
                },
            }).then((willDelete) => {
                if (willDelete) {
                    ldrshow();
                    $.ajax({
                        url: '{{route("hospital.empanelmentRegistration.hospitalSubmit", [$uuid, @$hospital->id])}}',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        type: 'POST',
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            ldrhide();
                            if(response.success) {
                                swal({
                                    title: "Hospital Submitted!!",
                                    text: response.message,
                                    type: "success",
                                    buttons: {
                                    confirm: {
                                        text: "Ok!",
                                        className: "btn btn-success",
                                    },
                                    },
                                }).then((willDelete) => {
                                    if (willDelete) {
                                        successMessage(response.message);
                                        setTimeout(() => {
                                            window.location.href = response.url;
                                        }, 1000);
                                    }
                                });
                                
                            } else {
                                errorMessage(response.message);
                            }
                        },
                        error: function (xhr) {
                            ldrhide();
                            $('.error').remove();
                            errorMessage('Something went wrong. Please try again later.');
                        }
                    });
                }
            });
        });

	});

   $('.branchesFormSave').click(function () {
      ldrshow();
      $('.error').remove();
      var formData = new FormData($('#branchForm')[0]);
     
      $.ajax({
         url: '{{route("hospital.empanelmentRegistration.saveBranch", [$uuid, @$hospital->id])}}', 
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
                $('#branchForm')[0].reset();
                $('.step4Icon').show();
                if(response.is_complete) {
                    $('.finalsave').removeClass('d-none');
                }
                loadBrachTable();
         },
         error: function (xhr) {
            ldrhide();
            $('.error').remove();
            
            if (xhr.status === 422) { 
                let errors = xhr.responseJSON.errors;
                let errorMessages = [];
                for (let field in errors) {
                    $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
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
    function loadBrachTable() {
        ldrshow();
        $.ajax({
            url: '{{route("hospital.empanelmentRegistration.loadBranchTable", [$uuid, @$hospital->id])}}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: {},
            processData: false,
            contentType: false, 
            success: function (response) {
                ldrhide();
                $('.branchtable').html(response.html || response);
            },
        });
    }

    function deleteBranchData(id, uuid) {
        ldrshow();       
        var type = 'mhr';
        fetch('{{route("hospital.empanelmentRegistration.deleteBranch")}}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ id, uuid })
        })
        .then(response => response.json())
        .then(data => {
            ldrhide();
            if(data.success) {
                successMessage(data.message);
                $('#branchrow'+id).remove();
                if(response.is_complete) {
                    $('.finalsave').removeClass('d-none');
                }else{
                    $('.finalsave').addClass('d-none');
                }
            } else {
                errorMessage(data.message);
            }
        });
    }
</script>