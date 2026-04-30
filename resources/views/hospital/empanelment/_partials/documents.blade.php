@php $documents = App\CentralLogics\Helpers::getCommanData('EmpanelmentDocument'); @endphp
@if(sizeof($documents) > 0)
<div  class="table-responsive mt-5 text-nowrap">
    <form id="documentsForm">
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th style="width: 5%">Sr No.</th>
                    <th style="width: 35%">Name</th>
                    <!-- <th style="width: 20%">Issue Date</th>
                    <th style="width: 20%">Expiry Date</th> -->
                    <th style="width: 35%">Action</th>
                    <th style="width: 25%">Remarks</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
           
                @foreach($documents as $key => $value)
                @php
                    $isRequired = false;
                    if($value->is_required) {
                        $isRequired = true;
                    }
                    $existData = App\CentralLogics\Helpers::getSingleDocument(@$hospital->id, $value->id);
                @endphp
                <tr>
                    <td> {{$loop->iteration}}</td>
                    <td style="text-wrap: auto;">{{$value->name}} @if($value->is_required) <span class="text-danger">*<span> @endif</td>
                    <td>
                        <div class="file-upload-section docerror">
                            <div class="file-upload-wrapper">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    height="24px"
                                    viewBox="0 -960 960 960"
                                    width="24px" fill="#6200ea">
                                    <path
                                        d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                                </svg>
                                <p><strong>Browse</strong></p>
                            </div>
                            <input type="file" class="file-input d-none" required name="document_{{$value->id}}_doc" {{$isRequired ? 'required' : ''}}  id="document_{{$value->id}}_doc" />
                            <div
                                class="uploaded-file file-upload-display d-none">
                                <span
                                    class="file-name">Sample.pdf</span>
                                <button type="button"
                                    class="remove-file-btn bg-transparent border-0 p-0">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        height="24px"
                                        viewBox="0 -960 960 960"
                                        width="24px"
                                        fill="undefined">
                                        <path
                                            d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <small class="small text-muted">Supported file types: PDF (Max: 10MB)</small>
                        @if(@$existData->document)
                            <label class="mt-2"><strong>Preview</strong>&nbsp; <a href="{{ asset('public/storage/'.@$existData->document) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a></label>
                        @endif
                    </td>
                    <td>
                        <input type="text" id="remark{{$value->id}}" oninput="sanitize(this, 'b');" value="{{$existData && $existData->remarks ? $existData->remarks : ''}}" name="{{$value->id}}_remarkdoc" class="form-control" placeholder="" />
                    </td>
                </tr>
                @endforeach
            
            </tbody>
        </table>
        
        @if(@$hospital->status == "Draft" || @$hospital->status == "Rejected" || !empty($is_admin_edit))
            <div class="col-md-12 mt-2">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-primary documentsFormSave" type="button">SAVE</button>
                </div>
            </div>
        @endif
    </form>
</div>

@endif
@if($allStepCompleted)
    @php
        $finalsave = true
    @endphp
@else
    @php
        $finalsave = false
    @endphp
@endif

@if((@$hospital->status == "Draft" || @$hospital->status == "Rejected"))
    <div class="card mb-6 mt-3 p-0 finalsave @if($finalsave) @else d-none @endif">
        @if(empty($is_admin_edit))
            <div class="card-header">
                <label for="is_accept"><input type="checkbox" name="is_accept" id="is_accept">&nbsp;&nbsp;<strong>I hereby declare that all information provided in this empanelment form is true, accurate, and complete to the best of my knowledge. I understand that any false or missing information may lead to rejection of this application or termination of empanelment, and may be subject to legal consequenses as per applicable laws and regulations.</strong></label>
            </div>
        @else
            <div class="card-header">
                <label class="text-primary">
                    Are you sure you want to approve this hospital? 
                    Once approved, the hospital details will be visible and accessible to users. 
                    Please ensure that all information has been verified before proceeding with the approval process. 
                    You can always revisit the details later if needed.
                </label>
            </div>
        @endif
        <div class="card-body">
            <button type="button" class="btn btn-outline-primary rounded-0 prevsubmit">{{ @$hospital->status == "Rejected" ? "RESUBMIT" : "SUBMIT" }}</button>
        </div>
    </div>
@elseif(empty($is_admin_edit) && @$hospital->status != "Approved" && @$hospital->status != "Rejected")
    <div class="card mb-6 mt-3 finalsave p-0 @if($finalsave) @else d-none @endif">
        <div class="card-header">
        <label for="is_accept">Application is submitted!!</label>
        </div>
    
    </div>
@endif

<script>

	$(document).ready(function() {

        $(".prevsubmit").on('click', function() {
            
            @if(empty($is_admin_edit))
                if (!$("#is_accept").is(":checked")) {
                    swal({
                        title: "Declaration Required",
                        text: "You must accept the declaration before submitting.",
                        icon: "error",
                        buttons: {
                            confirm: {
                                text: "Ok",
                                className: "btn btn-danger",
                            },
                        },
                    });
                    return;
                }
                swal({
                    title: "Confirm Submission?",
                    text: 'Are you sure you want to submit and proceed with the payment?',
                    icon: "warning",
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
                            url: '{{ route("hospital.empanelmentRegistration.hospitalSubmit", [$uuid, @$hospital->id]) }}',
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
                                        icon: "success",
                                        buttons: {
                                        confirm: {
                                            text: "Ok!",
                                            className: "btn btn-success",
                                        },
                                        },
                                    }).then((willDelete) => {
                                        successMessage(response.message);
                                        setTimeout(() => {
                                            location.reload();
                                        }, 1000);
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
            @else
            
                swal.fire({
                    title: "Confirm Submission?",
                    text: 'Are you sure you want to Approve this hospital?',
                    icon: "warning",
                    showCancelButton: true, // Replaces the 'buttons' structure
                    cancelButtonText: "No, cancel!",
                    confirmButtonText: "Yes!",
                    customClass: {
                        cancelButton: "btn btn-danger",  // Apply custom class for cancel button
                        confirmButton: "btn btn-success" // Apply custom class for confirm button
                    }
                }).then((result) => {
                    if (result.isConfirmed) {  // Use result.isConfirmed instead of 'willDelete'
                        ldrshow();
                        $.ajax({
                            url: '{{ route("admin.hospitals.hospitalSubmit", $hospital->id) }}',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            type: 'POST',
                            processData: false,
                            contentType: false,
                            success: function (response) {
                                ldrhide();
                                if (response.success) {
                                    swal.fire({
                                        title: "Hospital Submitted!!",
                                        text: response.message,
                                        icon: "success",
                                        confirmButtonText: "Ok!",
                                        confirmButtonClass: "btn btn-success",
                                    }).then(() => {
                                        successMessage(response.message);
                                        setTimeout(() => {
                                            window.location.href="{{ route('admin.hospitals.index') }}";
                                        }, 1000);
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

            @endif
        });

	});

   $('.documentsFormSave').click(function () {
      ldrshow();
      $('.error').remove();
      var formData = new FormData($('#documentsForm')[0]);
     
      $.ajax({
         url: '{{ !empty($is_admin_edit) ? route("admin.hospitals.update.documents", $hospital->id) : route("hospital.empanelmentRegistration.saveDocuments", [$uuid ?? '', $hospital->id]) }}', 
         headers: {
               'X-CSRF-TOKEN': '{{ csrf_token() }}'
         },
         type: 'POST',
         data: formData,
         processData: false,
         contentType: false,
         success: function (response) {
            ldrhide();
            $('#documentsForm input[type="file"]').val('');
                $('.remove-file-btn').click();
                @if(empty($is_admin_edit))
                    successMessage(response.message);
                    $('.step6Icon').show();
                    if(response.is_complete){
                        $('.finalsave').removeClass('d-none');
                    }
                    loadStep(6);
                @else
                    if (response.success) (typeof sendmsg === 'function' ? sendmsg('success', response.message) : alert(response.message));
                    
                    if(response.is_complete){
                        $('.finalsave').removeClass('d-none');
                    }
                    setTimeout(() => {
                        loadAdminStep(6);
                    }, 1000);
                @endif
         },
         error: function (xhr) {
            ldrhide();
            $('.error').remove();
            
            // $('#specialitiesform input[type="file"]').val('');
            // $('.remove-file-btn').click();
            if (xhr.status === 422) { 
               let errors = xhr.responseJSON.errors;
               for (let field in errors) {
                  $(`[name="${field}"]`).closest('.docerror').after(`<div class="error m-0 text-danger">${errors[field][0]}</div>`);
               }
            } else {
               errorMessage('Something went wrong. Please try again later.');
            }
         }
      });
   });
</script>