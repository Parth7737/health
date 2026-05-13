@php
    $humanresource = $hospital->humanResources()->where('type', 'sshr')->get();
@endphp
<div class="inside-left-info-box {{sizeof($humanresource) > 0 ? 'success' : 'pending'}} mt-4 sshrpanel">
    <h4 class="colored-verticle-title">
        Support Service Human Resource 
        <span class="status-dot">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="undefined">
                <path d="M400-304 240-464l56-56 104 104 264-264 56 56-320 320Z" />
            </svg>
        </span>
    </h4>
    <div class="addsshrform">
        <form id="sshrForm" class="mb-4" enctype="multipart/form-data">
            <div class="row g-5">
                <div class="col-md-6 col-lg-3 ">
                    <div class="input-group input-group-merge shrhprdiv sshperror">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" oninput="sanitize(this, 'b');" id="healthcare_proffessionals_registry_id_sshr" placeholder="john.doe" aria-label="Recipient's username" name="healthcare_proffessionals_registry_id" aria-describedby="healthcare_proffessionals_registry_id">
                            <label for="healthcare_proffessionals_registry_id">Healthcare Proffessionals Registry Id</label>
                        </div>
                        <button class="input-group-text btnshrinput" type="button" onclick="verifyshrhpr('healthcare_proffessionals_registry_id_sshr', 'btnshrinput');">Verify</button>
                    </div>
                    <small>Click on the verify button and first verify the HPR id first.</small>
                </div>
                <div class="col-md-6 col-lg-3 ">
                    <div class="form-floating form-floating-outline sshperror">
                        <input type="text" id="type_of_human_resource" oninput="sanitize(this, 't');" name="type_of_human_resource" required class="form-control" placeholder="Rajal Gupta" />
                        <label for="type_of_human_resource">Type Of Human Resource</label>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3 ">
                    <div class="form-floating form-floating-outline sshperror">
                        <select class="select2" id="sub_type_of_human_resource_sshr" name="sub_type_of_human_resource" required>
                            <option value="">Select</option>
                            @foreach($sshr as $key => $value)
                                <option value="{{$value->id}}">{{$value->name}}</option>
                            @endforeach
                        </select>
                        <label for="sub_type_of_human_resource">Sub-Type Of Human Resource</label>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="form-floating form-floating-outline sshperror">
                        <input type="text" id="name" name="name" oninput="sanitize(this, 't');" required class="form-control" placeholder="john" />
                        <label for="name">Name</label>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="form-floating form-floating-outline sshperror">
                        <input type="text" id="registration_number" name="registration_number" oninput="sanitize(this, 'b');" required class="form-control" placeholder="john" />
                        <label for="registration_number">Registration Number</label>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="form-floating form-floating-outline sshperror">
                        <input type="email" id="email" oninput="sanitize(this, 'email');" name="email" required class="form-control" placeholder="john@gmail.com" />
                        <label for="email">Email</label>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="form-floating form-floating-outline sshperror">
                        <input type="text" id="mobile_no" name="mobile_no" oninput="mobileinput(this);" required class="form-control" placeholder="xxxxxxx58" />
                        <label for="mobile_no">Mobile No</label>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <label for="formFile" class="form-label">Registration Certificate</label>
                    
                    <div class="file-upload-section sshperror">
                        <div class="file-upload-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                height="24px"
                                viewBox="0 -960 960 960"
                                width="24px" fill="#6200ea">
                                <path d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                            </svg>
                            <p>
                                <strong>Browse</strong>
                            </p>
                        </div>
                        <input type="file" class="file-input d-none" name="registration_certificate" id="registration_certificate" required accept=".pdf"/>
                        <div class="uploaded-file file-upload-display d-none">
                            <span class="file-name">Sample.pdf</span>
                            <i class="fas fa-trash "></i>
                            <button
                                class="remove-file-btn bg-transparent border-0 p-0">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    height="24px"
                                    viewBox="0 -960 960 960"
                                    width="24px"
                                    fill="undefined">
                                    <path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <span class="small text-muted">Supported file types: PDF (Max: 10MB)</span>
                </div>
                <div class="col-md-6 col-lg-3">
                    <label for="formFile" class="form-label">Declaration Certificate <a href="{{ asset('public/format/declaration-format.pdf') }}" download><small>Download Format</small></a></label>
                    <div class="file-upload-section sshperror">
                        <div class="file-upload-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                height="24px"
                                viewBox="0 -960 960 960"
                                width="24px" fill="#6200ea">
                                <path d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                            </svg>
                            <p>
                                <strong>Browse</strong>
                            </p>
                        </div>
                        <input type="file" class="file-input d-none" name="declaration_certificate" id="declaration_certificate" required accept=".pdf"/>
                        <div class="uploaded-file file-upload-display d-none">
                            <span class="file-name">Sample.pdf</span>
                            <i class="fas fa-trash "></i>
                            <button
                                class="remove-file-btn bg-transparent border-0 p-0">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    height="24px"
                                    viewBox="0 -960 960 960"
                                    width="24px"
                                    fill="undefined">
                                    <path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <span class="small text-muted">Supported file types: PDF (Max: 10MB)</span>
                </div>
                @if(\App\CentralLogics\Helpers::isbtnenabled(@$hospital->status))
                <div class="col-md-12">
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary" id="savessHR" disabled type="button">SAVE</button>
                    </div>
                </div>
                @endif
            </div>
        </form>
    </div>
    <div class="editsshrform d-none" >
    </div>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Sr No.</th>
                <th>Registration Number</th>
                <th>Type Of Human Resource</th>
                <th>Sub Type Of Human Resource</th>
                <th>Name</th>
                <th>Mobile Number</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody class="table-border-bottom-0 sshrtable">
            
        </tbody>
    </table>
</div>

<script>
   
    loadTable();   

    function verifyshrhpr(id = '', classname = '') {
        var healthcare_proffessionals_registry_id = $('#'+id).val();
        if(healthcare_proffessionals_registry_id != "") {
            $.ajax({
                url: '{{route("hospital.verifyHPRId", [$uuid, $hospital_id])}}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                type: 'POST',
                data: {healthcare_proffessionals_registry_id: healthcare_proffessionals_registry_id},
                success: function (response) {
                    ldrhide();
                    if(response.success) {
                        successMessage('HPR Verified Successfully!!');
                        $('#'+id).prop('readonly', true);
                        $("."+classname).attr('disabled', true);
                        $("."+classname).html('<i class="tf-icons ri-check-fill text-green"></i>');
                        $("#savessHR").removeAttr('disabled');
                    } else {
                        errorMessage('HPR Invalid!!');
                    }
                   
                },
                error: function (xhr) {
                    ldrhide();
                    $('.error').remove();
                    
                    if (xhr.status === 422) { 
                    let errors = xhr.responseJSON.errors;
                    for (let field in errors) {
                        $(`[name="${field}"]`).closest('.hrerror').after(`<div class="error text-danger">${errors[field][0]}</div>`);
                    }
                    } else {
                    alert('Something went wrong. Please try again later.');
                    }
                }
            });
        } else {
            $("#savessHR").attr('disabled', true);
            $("#editHRdata").attr('disabled', true);
            errorMessage('Please first type a HPR ID');
        }
    }

    $('#savessHR').click(function () {
        swal({
            title: "Confirm Submission?",
            text: 'Are you sure you want to resubmit this application? It will be moved to draft and you will need to submit it again.',
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
            if(willDelete) {  
                $('.error').remove();     
                ldrshow();
                // Create a FormData object
                var formData = new FormData($('#sshrForm')[0]);
                formData.append('type', 'sshr'); // Append 0 for unchecked
                // Send an AJAX request
                $.ajax({
                    url: '{{route("hospital.saveUHR", [$uuid, $hospital_id])}}',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false, 
                    success: function (response) {
                        ldrhide();
                        $('.sshrpanel').removeClass('pending').addClass('success');
                        
                        successMessage(response.message);
                        loadTable();  
                        $('#sshrForm')[0].reset();
                        $('#sshrForm input[type="file"]').val('');
                        $('#sub_type_of_human_resource_sshr').val('').trigger('change'); 
                        $("#savessHR").attr('disabled', true);
                        $('#healthcare_proffessionals_registry_id_sshr').prop('readonly', false);
                        $(".btnshrinput").removeAttr('disabled', true);
                        $(".btnshrinput").text('Verify');
                        $('.remove-file-btn').click();         
                    },
                    error: function (xhr) {
                        ldrhide();
                        $('.error').remove();
                        
                        if (xhr.status === 422) { 
                        let errors = xhr.responseJSON.errors;
                        for (let field in errors) {
                            $(`[name="${field}"]`).closest('.sshperror').after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                        } else {
                        alert('Something went wrong. Please try again later.');
                        }
                    }
                });
            }
        });
    });

        
    $(document).on("click", "#editHRdata", function() {
        swal({
            title: "Confirm Submission?",
            text: 'Are you sure you want to resubmit this application? It will be moved to draft and you will need to submit it again.',
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
            if(willDelete) {  
                ldrshow();
                $('.error').remove();
                var formData = new FormData($('#edithrForm')[0]);
                $.ajax({
                    url: '{{route("hospital.saveUHR", [$uuid, $hospital_id])}}',
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
                        $('.editsshrform').addClass('d-none').html("");
                        $('.addsshrform').removeClass('d-none');
                        loadTable();            
                    },
                    error: function (xhr) {
                        ldrhide();
                        $('.error').remove();
                        
                        if (xhr.status === 422) { 
                        let errors = xhr.responseJSON.errors;
                        for (let field in errors) {
                            $(`[name="${field}"]`).closest('.hrerror').after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                        } else {
                        alert('Something went wrong. Please try again later.');
                        }
                    }
                });
            }
        });
    });

    $(document).on("click", "#cancelhredit", function() {
        $('.editsshrform').addClass('d-none').html("");
        $('.addsshrform').removeClass('d-none');
    });

    function editSSHRData(id, hospital_id, type) {
        ldrshow();
        var type = type;
        $.ajax({
            url: '{{route("hospital.loadUHRSingleData")}}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: JSON.stringify({ type: type, id : id, hospital_id: hospital_id }),
            processData: false,
            contentType: false, 
            success: function (response) {
                ldrhide();
                $('.editsshrform').removeClass('d-none').html(response.html || response);
                loadSelect2();
                $('.addsshrform').addClass('d-none');
            },
            error: function (xhr) {
                ldrhide();
            }
        });
    }


    function loadTable() {
        ldrshow();
        var type = "sshr";
        $.ajax({
            url: '{{route("hospital.loadUHrTable", [$uuid, $hospital_id])}}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: JSON.stringify({ type: type }),
            processData: false,
            contentType: false, 
            success: function (response) {
                ldrhide();
                $('.sshrtable').html(response.html || response);
            },
        });
    }

    function deleteHrData(id, hospital_id) {
        ldrshow();       
        var type = 'sshr';
        fetch('{{route("hospital.deleteUHR")}}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ id, hospital_id, type })
        })
        .then(response => response.json())
        .then(data => {
            ldrhide();
            if(data.success) {
                successMessage(data.message);
                $('#hrrow'+id).remove();
                if(!data.is_data) {
                    $('.sshrpanel').addClass('pending').removeClass('success');
                }
             
            } else {
                errorMessage(data.message);
            }
        });
    }

</script>