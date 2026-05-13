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
    <!-- <h5 class="theme-color mt-3">Documents</h5><hr> -->
    <div class="row">
        <div class="table-responsive  text-nowrap">
            @if(@$verification->status == "Physical Verification Pending" || @$verification->status == "Pending")
                <div class="bg-white rounded-3 box-shadow p-5 mt-2">
                    <div class="card">
                        <div class="card-header"><h4>Report<h4></div>
                        <div class="card-body">
                            @if($allstepcomplete)
                            <form id="hospitalReportForm" onSubmit="return false" enctype="multipart/form-data">               
                                <div class="row g-5">                    
                                    <div class="col-sm-3">
                                        <label class="mb-3">Document Type <span class="text-danger">*</span></label>
                                        <select name="document_type" id="document_type" class="select2 form-select form-select-lg reporterrormesage" data-allow-clear="true" required >
                                            <option value="">Select</option>
                                            <option value="Audio">Audio</option>
                                            <option value="Document">Document</option>
                                            <option value="Video">Video</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="mb-3">Upload Support Document <span class="text-danger">*</span></label>
                                        <div class="file-upload-section reporterrormesage">
                                            <div class="file-upload-wrapper">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#6200ea">
                                                    <path d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                                                </svg>
                                                <p> <strong>Browse</strong> </p>
                                            </div>
                                            <input type="file" class="file-input d-none "  name="document" id="document"/>
                                            <div class="uploaded-file file-upload-display d-none">
                                                <span class="file-name">Sample.pdf</span>
                                                <i class="fas fa-trash "></i>
                                                <button type="button" class="remove-file-btn bg-transparent border-0 p-0">
                                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="undefined">
                                                        <path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="mb-3">Document Description</label>
                                        <input type="text" id="document_description" oninput="sanitize(this, 'b');" name="description" class="form-control reporterrormesage" />
                                    </div>

                                    <div class="col-sm-12">
                                        <label class="mb-3">Remark <span class="text-danger">*</span></label>
                                        <textarea class="form-control reporterrormesage" id="remark"  name="remark" ></textarea>
                                    </div>

                                    <div class="col-sm-3">
                                        <label class="mb-3">Latitude</label>
                                        <input type="text" id="latitude" name="latitude" class="form-control reporterrormesage" required />
                                    </div>

                                    <div class="col-sm-3">
                                        <label class="mb-3">Longitude</label>
                                        <input type="text" id="longitude" name="longitude" class="form-control reporterrormesage" required />
                                    </div>
                                
                                    <div class="d-flex justify-content-end mt-3">
                                        <button class="btn btn-primary saveHospitalReportForm" type="button" >Submit Report</button>
                                    </div>
                                </div>
                            </form>
                            @else   
                                <h4 class="text-center"><strong>Please Check all form then you will submit the report!!</strong></h4>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <h4 class="text-center"><strong>Physical Verification Completed!!</strong></h4>
            @endif
            <!-- <form id="documentForm">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Sr No.</th>
                            <th>Hospital Input</th>
                            <th>Verifier Comment</th>
                            <th>Remark</th>
                        </tr>
                    <thead>
                    <tbody>
                        @foreach(@$hospital->documents as $key => $value)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>
                                    {{@$value->doc->name}} <br>
                                    <a href="{{ asset('public/storage/'.@$value->document) }}" target="_blank">Preview <i class="tf-icons ri-eye-fill"></i></a>
                                </td>
                                <td>
                                    <div class="form-floating form-floating-outline">
                                        <select {{$disabled}} class="select2 docerror" id="dec_verify_status{{@$value->id}}" name="dec_verify_status_{{@$value->id}}" required>
                                            <option value="">Select</option>
                                            <option value="Valid" @if(@$value->dec_verify_status == "Valid") selected @endif >Valid</option>
                                            <option value="InValid" @if(@$value->dec_verify_status == "InValid") selected @endif >InValid</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-floating form-floating-outline">
                                        <input type="text"
                                            {{$readonly}}
                                            id="dec_verify_remark_{{@$value->id}}"
                                            name="dec_verify_remark_{{@$value->id}}"
                                            value="{{@$value->dec_verify_remark}}"
                                            class="form-control docerror"
                                            placeholder="text"/>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if(!$isedit)
                <button type="button" class="btn btn-primary mt-2 rounded-0 saveDocumentForm">Save</button>
                @endif
            </form> -->
        </div>
    </div>
</div>

<script>

    // getLocation();

    // function getLocation() {
    //     if (navigator.geolocation) {
    //         navigator.geolocation.getCurrentPosition(sendPosition);
    //     } else {
    //         alert("Geolocation is not supported by this browser.");
    //     }
    // }

    // function sendPosition(position) {
    //     var lat = position.coords.latitude;
    //     var long = position.coords.longitude;
    //     alert(lat, long);
    //     $("#latitude").val(lat);
    //     $("#longitude").val(long);
    // }
    
    $('.saveDocumentForm').on('click', function() {
        ldrshow();
        $('.error').remove();
       
        var formData = new FormData($('#documentForm')[0]);
        
        $.ajax({
            url: '{{route("decverifier.saveDocumentReview", [base64_encode($hospital->id), base64_encode($hospital->uuid)])}}',
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
                console.log(formData);
                $.ajax({
                    url: '{{route("decverifier.submitVerifierReport", [base64_encode($hospital->id), base64_encode($verification->id), base64_encode($hospital->uuid)])}}',
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
                                title: "Physical Verifier",
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
        })
    });
</script>