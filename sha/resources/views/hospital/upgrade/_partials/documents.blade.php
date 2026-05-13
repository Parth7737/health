@php $documents = App\CentralLogics\Helpers::getCommanData('EmpanelmentDocument'); 
    $hospitals = App\Models\Hospitals::where('id', $hospital->main_hospitalid)->first();
@endphp
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
                    $existData = App\CentralLogics\Helpers::getSingleDocument($hospital_id, $value->id);
                @endphp
                <tr>
                    <td> {{$loop->iteration}}</td>
                    <td style="text-wrap: auto;">{{$value->name}} @if($value->is_required) <span class="text-danger">*<span> @endif</td>
                    <!-- <td>
					    <div class="input-group input-group-merge docerror">
                            <input type="text" class="form-control datepicker " {{$isRequired ? 'required' : ''}} name="{{$value->id}}_dateissuedoc" id="{{$value->id}}_dateissuedoc" placeholder="YYYY-MM-DD" aria-describedby="asdasda">
                            <span class="input-group-text cursor-pointer">
                                <i class="ri-calendar-2-line text-secondary"></i>
                            </span>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-merge docerror">
                            <input type="text" class="form-control datepicker {{$value->id}}_dateexpiry" id="{{$value->id}}_dateexpirydoc" placeholder="YYYY-MM-DD" {{$isRequired ? 'required' : ''}} name="{{$value->id}}_dateexpirydoc" aria-describedby="asdasda">
                            <span class="input-group-text cursor-pointer">
                                <i class="ri-calendar-2-line text-secondary"></i>
                            </span>
                        </div>
                    </td> -->
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
                                <i class="fas fa-trash "></i>
                                <button
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
                        <input type="text" id="remark{{$value->id}}" value="{{$existData && $existData->remarks ? $existData->remarks : ''}}" name="{{$value->id}}_remarkdoc" class="form-control" placeholder="" />
                    </td>
                </tr>
                @endforeach
            
            </tbody>
        </table>
        
        @if($hospital->status != 'Empannelled')
            <div class="col-md-12 mt-2">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-primary documentsFormSave" type="button">SAVE</button>
                </div>
            </div>
        @endif
    </form>
</div>

@endif
@if($ischangeData)
    @php
        $finalsave = true
    @endphp
@else
    @php
        $finalsave = false
    @endphp
@endif

@if(@$hospitals->status == "Draft")
    <div class="card mb-6 p-0 @if($finalsave) finalsave @else d-none @endif">
        <div class="card-header">
        <label for="is_accept"><input type="checkbox" name="is_accept" id="is_accept">&nbsp;&nbsp;<strong>I hereby declare that all information provided in this empanelment form is true, accurate, and complete to the best of my knowledge. I understand that any false or missing information may lead to rejection of this application or termination of empanelment, and may be subject to legal consequenses as per applicable laws and regulations.</strong></label>
        </div>
        <div class="card-body">
            <button type="button" class="btn btn-outline-primary rounded-0 prevsubmit "  >SUBMIT</button>
        </div>
    </div>
@elseif(@$hospitals->status == 'Response Required From Facility' || @$hospitals->status == 'Queried' || @$hospitals->status == "Query On Upgradation Request From Facility")
    <div class="bg-white rounded-3 box-shadow p-5 mt-5">
        <div class="card">
            <div class="card-header"><h4>Query From DEC</h4></div>
            <div class="card-body">
                <div class="col-md-12 mb-4">
                    <div class="row">
                        @php
                            $getWorkData = App\Models\WorkFlowHistory::where('id', $hospitals->dec_work_id)->first();
                        @endphp
                      
                        @if(@$getWorkData)
                            <p><strong>Action:</strong>{{@$getWorkData->action}}</p>
                            <p><strong>Remark:</strong>{{@$getWorkData->remark}}</p>
                            <div class="infodata">
                                <a href="{{ asset('public/storage/'.@$getWorkData->attachment) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a></label>
                            </div>
                        @endif
                    </div>
                </div>
                <form id="hospitalReportFormDec" enctype="multipart/form-data">               
                    <div class="row g-5">  
                        <div class="col-sm-4">
                            <label class="mb-3">Action <span class="text-danger">*</span></label>
                            <select name="dec_action" id="dec_action" class="select2 form-select form-select-lg reporterrormesage" data-allow-clear="true" required >
                                <option value="">Select</option>
                                <option value="Query Replied">Query Replied</option>
                            </select>
                        </div>

                        <div class="col-sm-4">
                            <label class="mb-3">Upload Document <span class="text-danger">*</span></label>
                            <div class="file-upload-section reporterrormesage">
                                <div class="file-upload-wrapper">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#6200ea">
                                        <path d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                                    </svg>
                                    <p> <strong>Browse</strong> </p>
                                </div>
                                <input type="file" class="file-input d-none "  name="dec_document" id="dec_document"/>
                                <div class="uploaded-file file-upload-display d-none">
                                    <span class="file-name">Sample.pdf</span>
                                    <i class="fas fa-trash "></i>
                                    <button class="remove-file-btn bg-transparent border-0 p-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="undefined">
                                            <path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <label class="mb-3">Remark <span class="text-danger">*</span></label>
                            <textarea type="text" oninput="sanitize(this, 'b');" class="form-control reporterrormesage" id="dec_remarks"  name="dec_remarks" ></textarea>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button class="btn btn-primary saveHospitalReportFormDec" type="button" >Submit Report</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@else
    <div class="card mb-6 p-0 @if($finalsave) finalsave @else d-none @endif">
        <div class="card-header">
            <label for="is_accept">Application is {{@$hospitals->status}}!!</label>
        </div>
    </div>               
@endif

<script>
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
                text: 'Are you sure you want to submit?',
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
                        url: '{{route("hospital.hospitalReSubmit", [$uuid, $hospital_id])}}',
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
                                    title: "Hospital Upgradation Request Sent Successfully!!",
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

		// $('.datepicker[name$="_dateissuedoc"]').daterangepicker({
		// 	singleDatePicker: true,
		// 	autoUpdateInput: false,
		// 	maxDate: moment().subtract(1, 'days'), // Restrict to past dates
		// 	locale: {
		// 		format: 'YYYY-MM-DD'
		// 	},
		// 	opens: 'right'
		// });

		// // Initialize Date of Expiry with future dates only
		// $('.datepicker[name$="_dateexpirydoc"]').daterangepicker({
		// 	singleDatePicker: true,
		// 	autoUpdateInput: false,
		// 	minDate: moment().add(1, 'days'), // Restrict to future dates
		// 	locale: {
		// 		format: 'YYYY-MM-DD'
		// 	},
		// 	opens: 'right'
		// });

		// // Apply selected date to input for Date of Issue
		// $('.datepicker[name$="_dateissuedoc"]').on('apply.daterangepicker', function (ev, picker) {
		// 	$(this).val(picker.startDate.format('YYYY-MM-DD'));
		// });

		// // Clear Date of Issue if canceled
		// $('.datepicker[name$="_dateissuedoc"]').on('cancel.daterangepicker', function (ev, picker) {
		// 	$(this).val('');
		// });

		// // Apply selected date to input for Date of Expiry
		// $('.datepicker[name$="_dateexpirydoc"]').on('apply.daterangepicker', function (ev, picker) {
		// 	$(this).val(picker.startDate.format('YYYY-MM-DD'));
		// });

		// // Clear Date of Expiry if canceled
		// $('.datepicker[name$="_dateexpirydoc"]').on('cancel.daterangepicker', function (ev, picker) {
		// 	$(this).val('');
		// });
	});
	$(document).ready(function () {
		$('.itemName').text('Documents');
	});

   $('.documentsFormSave').click(function () {
      ldrshow();
      $('.error').remove();
      var step = 8;
      var formData = new FormData($('#documentsForm')[0]);
     
      $.ajax({
         url: '{{route("hospital.empanelmentRegistration.saveDocuments", [$uuid, $hospital_id])}}', 
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
                $('#documentsForm input[type="file"]').val('');
                $('.remove-file-btn').click();
                $('.step8Icon').show();
                if(response.is_complete) {
                    // $("#previewsubmit").show();
                    $('.finalsave').removeClass('d-none');
                }
         },
         error: function (xhr) {
            ldrhide();
            $('.error').remove();
            
            // $('#specialitiesform input[type="file"]').val('');
            // $('.remove-file-btn').click();
            if (xhr.status === 422) { 
               let errors = xhr.responseJSON.errors;
               for (let field in errors) {
                  $(`[name="${field}"]`).closest('.docerror').after(`<div class="error text-danger">${errors[field][0]}</div>`);
               }
            } else {
               alert('Something went wrong. Please try again later.');
            }
         }
      });
   });

   $('.saveHospitalReportFormDec').click(function () {
        swal({
            title: "Confirm Submission?",
            text: 'Are you sure you want to proceed?',
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
                $('.error').remove();
                var formData = new FormData($('#hospitalReportFormDec')[0]);
                $.ajax({
                    url: '{{route("hospital.submitResponse", [base64_encode($hospital->id), base64_encode($hospital->uuid)])}}',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        ldrhide();
                        if(response.success) {
                            swal({
                                title: "Hospital Update",
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
                                    $('#hospitalReportFormDec')[0].reset();
                                    $('#hospitalReportFormDec input[type="file"]').val('');
                                    $('#decaction').val('').trigger('change');
                                    setTimeout(() => {
                                        window.location.href = response.url;
                                    }, 1000);
                                }
                            });
                        } else {
                            errorMessage('Something is wrong!!');
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
                                    $(`[name="${field}"]`).closest('.reporterrormesage').after(`<div class="error text-danger">${errors[field][0]}</div>`);
                                }
                            }
                        } else {
                            errorMessage('Something went wrong. Please try again later.');
                        }
                    }
                });
            }
        });

    });
</script>