@php
   $isedit = false;
   $readonly = '';
   $disabled = '';
   $iseditsec = false;

   if($verification->status == "Physical Verification Completed") {
      $isedit = false;
      $readonly = 'readonly';
      $disabled = 'disabled';
   }

    if(@$hospital->status == 'Empanelled' || @$hospital->status == 'Query Raised by SEC' || @$hospital->status == 'Queried' || @$hospital->status == 'Rejected') {
        $iseditsec = true;
    }
@endphp
<div class="card p-0 shadow-none rounded-0  border-bottom">
    <h5 class="theme-color mt-3">Reports</h5><hr>
    <div class="row">
        <div class="table-responsive text-nowrap">
            @if($hospital->hospitalReport && @$hospital->status != 'Empanelled' && @$hospital->status != 'Query Raised by SEC' && @$hospital->status != 'Queried' && @$hospital->status != 'Rejected')
                <div class="bg-white rounded-3 box-shadow p-5 ">
                    <div class="card">                        
                        <div class="card-body">
                            <form id="hospitalReportForm" enctype="multipart/form-data">               
                                <div class="row g-5">  
                                    @if(@$hospital->hospitalReport->remark && @$hospital->hospitalReport->document_type && @$hospital->hospitalReport->document)
                                        <h5>Physicial Verifier Report</h5>
                                        <div class="col-sm-12">
                                            <label class="mb-3"><strong>Remark</strong></label>
                                            <textarea class="form-control reporterrormesage" id="remark"  name="remark" readonly disabled>{{@$hospital->hospitalReport->remark}}</textarea>
                                        </div>                  
                                        <div class="col-sm-4">
                                            <label class="mb-3"><strong>Document Type</strong></label><br>
                                            <label for="">{{@$hospital->hospitalReport->document_type}}</label>
                                        </div>
                                        <div class="col-sm-4">
                                            <label class="mb-3"><strong>{{@$hospital->hospitalReport->document_type}}</strong></label><br>
                                            <label class="mt-2"> <a href="{{ asset('public/storage/'.@$hospital->hospitalReport->document) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a></label>
                                        </div>
                                        <div class="col-sm-4">
                                            <label class="mb-3"><strong>Docuemnt Description</strong></label><br>
                                            <label for="">{{@$hospital->hospitalReport->description}}</label>
                                        </div>
                                        <div class="col-sm-4">
                                            <label class="mb-3"><strong>Latitude</strong></label><br>
                                            <label for="">{{@$hospital->hospitalReport->latitude}}</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <label class="mb-3"><strong>Longitude</strong></label><br>
                                            <label for="">{{@$hospital->hospitalReport->longitude}}</label>
                                        </div>
                                        <hr>
                                    @endif
                                    <h5>Dec Verifier Report</h5>
                                    <div class="col-sm-6">
                                        <label class="mb-3"><strong>Action</strong></label><br>
                                        <label class="mt-2">{{@$hospital->hospitalReport->dec_action}}</label>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="mb-3"><strong>Uploaded Document</strong></label><br>
                                        <label class="mt-2"> <a href="{{ asset('public/storage/'.@$hospital->hospitalReport->dec_document) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a></label>
                                    </div>
                                    <div class="col-sm-12">
                                        <label class="mb-3"><strong>Remark</strong></label>
                                        <textarea class="form-control reporterrormesage" id="dec_remark"  name="dec_remark" readonly disabled>{{@$hospital->hospitalReport->dec_remarks}}</textarea>
                                    </div>  
                                    <hr>
                                    @if($allstepcomplete)
                                        <h5><strong>SEC Officer Action</strong></h5>
                                        <div class="col-sm-4">
                                            <label class="mb-3">Action <span class="text-danger">*</span></label>
                                            <select name="sec_action" id="sec_action" class="select2 form-select form-select-lg reporterrormesage" data-allow-clear="true" required >
                                                <option value="">Select</option>
                                                @if($hospital->is_upgrade_application == 1)
                                                    <option value="Upgrade Request Approved by SEC" {{@$hospital->hospitalReport->sec_action == 'Upgrade Request Approved by SEC' ? 'selected' : ''}}>Upgrade Request Approved by SEC</option>
                                                    <option value="Upgrade Request Rejected by SEC" {{@$hospital->hospitalReport->sec_action == 'Upgrade Request Rejected by SEC' ? 'selected' : ''}}>Upgrade Request Rejected by SEC</option>
                                                    <option value="Query Raised by SEC" {{@$hospital->hospitalReport->sec_action == 'Query Raised by SEC' ? 'selected' : ''}}>Query Raised by SEC</option>
                                                @else
                                                    <option value="Empanelment Approved by SEC" {{@$hospital->hospitalReport->sec_action == 'Empanelment Approved by SEC' ? 'selected' : ''}}>Empanelment Approved by SEC</option>
                                                    <option value="Empanelment Rejected by SEC" {{@$hospital->hospitalReport->sec_action == 'Empanelment Rejected by SEC' ? 'selected' : ''}}>Empanelment Rejected by SEC</option>
                                                    <option value="Query Raised by SEC" {{@$hospital->hospitalReport->sec_action == 'Query Raised by SEC' ? 'selected' : ''}}>Query Raised by SEC</option>
                                                @endif
                                                
                                                <!-- <option value="Query Requested by SEC from Facility" {{@$hospital->hospitalReport->sec_action == 'Query Requested by SEC from Facility' ? 'selected' : ''}}>Query Requested by SEC from Facility</option> -->
                                            </select>
                                        </div>

                                        <div class="col-sm-4">
                                            <label class="mb-3">Upload Physical Verification Report <span class="text-danger">*</span></label>
                                            <div class="file-upload-section reporterrormesage">
                                                <div class="file-upload-wrapper">
                                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#6200ea">
                                                        <path d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                                                    </svg>
                                                    <p> <strong>Browse</strong> </p>
                                                </div>
                                                <input type="file" class="file-input d-none "  name="sec_document" id="sec_document"/>
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
                                            @if(@$hospital->hospitalReport->sec_document)
                                                <a href="{{ asset('public/storage/'.@$hospital->hospitalReport->sec_document) }}" target="_blank">Preview <i class="tf-icons ri-eye-fill"></i></a>
                                            @endif
                                        </div>

                                        <div class="col-sm-12">
                                            <label class="mb-3">Remark <span class="text-danger">*</span></label>
                                            <textarea class="form-control reporterrormesage" id="sec_remarks"  name="sec_remarks" >{{@$hospital->hospitalReport->sec_remarks}}</textarea>
                                        </div>

                                        <div class="d-flex justify-content-end mt-3">
                                            <button class="btn btn-primary saveHospitalReportForm" type="button" >Submit Report</button>
                                        </div>
                                    @else   
                                        <h4 class="text-center"><strong>Please Check all form then you will submit the report!!</strong></h4>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            {{-- <form id="documentForm">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Sr No.</th>
                            <th>Hospital Input</th>
                            <th>Verifier Action</th>
                            <th>DEC Officer</th>
                            <th>SEC Officer</th>
                            <th>SEC Remarks</th>
                        </tr>
                    <thead>
                    <tbody>
                        @foreach(@$hospital->documents as $key => $value)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td style="text-wrap: auto;">
                                    {{@$value->doc->name}} <br>
                                    <a href="{{ asset('public/storage/'.@$value->document) }}" target="_blank">Preview <i class="tf-icons ri-eye-fill"></i></a>
                                </td>
                                <td style="text-wrap: auto;">
                                    <b>Status:</b>{{@$value->dec_verify_status}}</br>
                                    <b>Remark:</b>{{@$value->dec_verify_remark}}
                                </td> 
                                <td style="text-wrap: auto;">
                                    <b>Status:</b>{{@$value->dec_status}}</br>
                                    <b>Remark:</b>{{@$value->dec_remark}}
                                </td> 
                                
                                <td>
                                    <div class="form-floating form-floating-outline">
                                        <select class="select2 docerror" id="sec_status{{@$value->id}}" name="sec_status_{{@$value->id}}" required>
                                            <option value="">Select</option>
                                            <option value="Valid" @if(@$value->sec_status == "Valid") selected @endif >Valid</option>
                                            <option value="InValid" @if(@$value->sec_status == "InValid") selected @endif >InValid</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-floating form-floating-outline">
                                        <input type="text"
                                            id="sec_remark_{{@$value->id}}"
                                            name="sec_remark_{{@$value->id}}"
                                            value="{{@$value->sec_remark}}"
                                            oninput="sanitize(this, 'b');"
                                            class="form-control docerror"
                                            placeholder="text"/>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if(!$iseditsec)
                <div class="d-flex justify-content-end mt-3">
                    <button type="button" class="btn btn-primary mt-2 rounded-0 saveDocumentForm">Save</button>
                </div>
                @endif
            </form> --}}
        </div>
    </div>
</div>

<script>
    
    $('.saveDocumentForm').on('click', function() {
        ldrshow();
        $('.error').remove();
       
        var formData = new FormData($('#documentForm')[0]);
        
        $.ajax({
            url: '{{route("sec.saveDocumentReview", [base64_encode($hospital->id), base64_encode($hospital->uuid)])}}',
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
            },
            error: function (xhr) {
                ldrhide();
                $('.error').remove();
                
                if (xhr.status === 422) { 
                let errors = xhr.responseJSON.errors;
                for (let field in errors) {
                    $(`[name="${field}"]`).closest('.docerror').after(`<div class="error text-danger">${errors[field][0]}</div>`);
                }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });    
    });

    $('.saveHospitalReportForm').click(function () {
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

                var formData = new FormData($('#hospitalReportForm')[0]);
                $.ajax({
                    url: '{{route("sec.submitVerifierReport", [base64_encode($hospital->id), base64_encode($verification->id), base64_encode($hospital->uuid)])}}',
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
                                title: "SEC Officer Request",
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
                                $(`[name="${field}"]`).closest('.reporterrormesage').after(`<div class="error text-danger">${errors[field][0]}</div>`);
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