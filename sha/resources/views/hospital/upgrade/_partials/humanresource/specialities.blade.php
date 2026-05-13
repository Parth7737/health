@php
    $hospitalTeam = $hospital->hospitalTeam()->get();
@endphp
<div class="inside-left-info-box {{sizeof($hospitalTeam) > 0 ? 'success' : 'pending'}} mt-4 sppanel">
    <h4 class="colored-verticle-title">
        Specialities
        <span class="status-dot">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="undefined">
                <path d="M400-304 240-464l56-56 104 104 264-264 56 56-320 320Z" />
            </svg>
        </span>
    </h4>
    <div class="addspecialitiesform">
        <form id="specialitiesform" class="mb-4">
            <div class="row g-5">
                <div class="col-md-6 col-lg-3 ">
                    <div class="input-group input-group-merge sperror">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" id="hpr_id" oninput="sanitize(this, 'b');" aria-label="Recipient's username" name="hpr_id" required aria-describedby="hpr_id">
                            <label for="hpr_id">Healthcare Proffessionals Registry Id</label>
                        </div>
                        <button class="input-group-text btnspecialities" onclick="verifyspecialitieshpr('hpr_id', 'btnspecialities');" type="button">Verify</button>
                    </div>
                    <small>Click on the verify button and first verify the HPR id first.</small>
                </div>
                <div class="col-md-6 col-lg-3 ">
                    <div class="form-floating form-floating-outline sperror">
                        <input type="text" id="designation" oninput="sanitize(this, 't');" name="designation" required class="form-control" />
                        <label for="designation">Type Of Human Resource</label>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 ">
                    <div class="form-floating form-floating-outline sperror">
                        <select class="select2" id="speciality_id" name="speciality_id" required>
                            <option value="">Select</option>
                            @foreach($specialities as $key => $value)
                                <option value="{{$value->speciality->id}}">{{$value->speciality->name.' ('.$value->speciality->code.')'}}</option>
                            @endforeach
                            
                        </select>
                        <label for="speciality_id">Specialization</label>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 ">
                    <div class="form-floating form-floating-outline sperror">
                        <select class="select2" id="employement_type" name="employement_type" required>
                            <option value="">Select</option>
                            <option value="FullTime Consultant">FullTime Consultant</option>
                            <option value="Visiting Consultant">Visiting Consultant</option>
                        </select>
                        <label for="employement_type">Employment Type</label>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="form-floating form-floating-outline sperror">
                        <input type="text" id="name" oninput="sanitize(this, 't');" name="name" required class="form-control" placeholder="john" />
                        <label for="name">Name</label>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="form-floating form-floating-outline sperror">
                        <input type="text" id="registration_no" oninput="sanitize(this, 'b');" name="registration_no" required class="form-control" placeholder="xxxxxx56" />
                        <label for="registration_no">Registration Number</label>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="form-floating form-floating-outline sperror">
                        <input type="email" id="email" oninput="sanitize(this, 'email');" name="email" required class="form-control" placeholder="john@gmail.com" />
                        <label for="email">Email</label>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="form-floating form-floating-outline sperror">
                        <input type="text" id="mobile" name="mobile" oninput="mobileinput(this);" required class="form-control" placeholder="xxxxxxx58" />
                        <label for="mobile">Mobile No</label>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <label for="formFile" class="form-label">Registration Certificate</label>
                    
                    <div class="file-upload-section sperror">
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
                
                <div class="col-md-6 col-lg-9">
                    <label for="formFile" class="form-label">Declaration Certificate <a href="{{ asset('public/format/declaration-format.pdf') }}" download><small>Download Format</small></a></label>
                    <div class="file-upload-section sperror">
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
                <div class="col-md-6 col-lg-3">
                    <div class="form-floating form-floating-outline sperror">
                        <input type="text" id="registration_certificate_expiry" name="registration_certificate_expiry" required class="form-control datepicker" placeholder="YYYY-MM-DD" />
                        <label for="registration_certificate_expiry">Registration Certificate Expiry</label>
                    </div>
                </div>

                <!-- <div class="col-md-6 col-lg-3">
                    <div class="form-floating form-floating-outline sperror">
                        <input type="text" id="declaration_certificate_expiry" name="declaration_certificate_expiry" required class="form-control datepicker" placeholder="YYYY-MM-DD" />
                        <label for="declaration_certificate_expiry">Declaration Certificate Expiry</label>
                    </div>
                </div> -->
                @if(\App\CentralLogics\Helpers::isbtnenabled(@$hospital->status))
                    <div class="col-md-12">
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-primary savespecialities" disabled type="button">SAVE</button>
                        </div>
                    </div>
                @endif
            </div>
        </form>
    </div>
    <div class="editspecialitiesform d-none"></div>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Sr No.</th>
                <th>Registration Number</th>
                <th>Type of Human Resource</th>
                <th>Name</th>
                <th>Mobile Number</th>
                <th>Specialization</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody class="table-border-bottom-0 specialitiestbl">
            
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        loadSpecialitiesTable();
		$('.datepicker').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD',
                cancelLabel: 'Clear'
            },
            singleDatePicker: true,
            minDate: moment().add(1, 'days'),
            autoUpdateInput: true,
            showDropdowns: true,
			opens: 'right'
		});

        $('.datepicker').val('');

        $('.datepicker').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    });

    function verifyspecialitieshpr(id = '', classname = '') {
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
                        $("."+ classname).attr('disabled', true);
                        $("."+ classname).html('<i class="tf-icons ri-check-fill text-green"></i>');
                        $(".savespecialities").removeAttr('disabled');
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
            $(".savespecialities").attr('disabled', true);
            $("#editspecialities").attr('disabled', true);
            errorMessage('Please first type a HPR ID');
        }
    }

    $('.savespecialities').click(function () {
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
                var formData = new FormData($('#specialitiesform')[0]);
                // Send an AJAX request
                $.ajax({
                    url: '{{route("hospital.saveUHumanSpecialities", [$uuid, $hospital_id])}}',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false, 
                    success: function (response) {
                        ldrhide();
                        $('.sppanel').removeClass('pending').addClass('success');
                        successMessage(response.message);
                        $('#specialitiesform')[0].reset();
                        $('#specialitiesform input[type="file"]').val('');
                        $(".savespecialities").attr('disabled', true);
                        $('#speciality_id').val('').trigger('change'); 
                        $('#employement_type').val('').trigger('change'); 
                        $('#hpr_id').prop('readonly', false);
                        $(".btnspecialities").removeAttr('disabled', true);
                        $(".btnspecialities").text('Verify');
                        $('.remove-file-btn').click();
                    
                        loadSpecialitiesTable();  
                                
                    },
                    error: function (xhr) {
                    ldrhide();
                    $('.error').remove();
                    
                    if (xhr.status === 422) { 
                        let errors = xhr.responseJSON.errors;
                        for (let field in errors) {
                            $(`[name="${field}"]`).closest('.sperror').after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                    } else {
                        alert('Something went wrong. Please try again later.');
                    }
                    }
                });
            }
        });
   });

    $(document).on("click", "#editspecialities", function() {
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
                var formData = new FormData($('#editspecialitiesform')[0]);
                $.ajax({
                    url: '{{route("hospital.saveUHumanSpecialities", [$uuid, $hospital_id])}}',
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
                        $('.editspecialitiesform').addClass('d-none').html("");
                        $('.addspecialitiesform').removeClass('d-none');
                        loadSpecialitiesTable();            
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

    $(document).on("click", "#cancelspecialitiesedit", function() {
        $('.editspecialitiesform').addClass('d-none').html("");
        $('.addspecialitiesform').removeClass('d-none');
    });

    function editSpecialityData(id, hospital_id) {
        ldrshow();
        var type = "sshr";
        $.ajax({
            url: '{{route("hospital.loadUSpecialitiesSingleData")}}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: JSON.stringify({ type: type, id : id, hospital_id: hospital_id }),
            processData: false,
            contentType: false, 
            success: function (response) {
                ldrhide();
                $('.editspecialitiesform').removeClass('d-none').html(response.html || response);
                loadSelect2();
                $('.addspecialitiesform').addClass('d-none');
            },
            error: function (xhr) {
                ldrhide();
            }
        });
    }
   
   function loadSpecialitiesTable() {
        ldrshow();
        var type = "mhr";
        $.ajax({
            url: '{{route("hospital.loadUSpecialitiesTable", [$uuid, $hospital_id])}}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: JSON.stringify({ type: type }),
            processData: false,
            contentType: false, 
            success: function (response) {
                ldrhide();
                $('.specialitiestbl').html(response.html || response);
            },
        });
    }

   function deleteSpecialityData(id, hospital_id) {
        ldrshow();    
        fetch('{{route("hospital.deleteUSpecialitiesHR")}}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ id, hospital_id })
        })
        .then(response => response.json())
        .then(data => {
            ldrhide();
            if(data.success) {
                successMessage(data.message);
                $('#hrspecrow'+id).remove();
                if(!data.is_data) {
                    $('.sppanel').addClass('pending').removeClass('success');
                }
                loadSpecialitiesTable
            } else {
                errorMessage(data.message);
            }
        });
   }
</script>