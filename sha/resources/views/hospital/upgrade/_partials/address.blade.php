<form id="empanelmentaddress">
    <!-- Communication Address -->
    <div class="card mb-6 border border-primary">
        <div class="card-body">                  
            <div class="row g-5">
                <div class="addressmessage text-success"></div>
                <div class="col-sm-3">
                    <label class="mb-3">Address <span class="text-danger">*</span></label>
                    <input type="text" id="address" oninput="sanitize(this, 'm');"  value="{{$address->address}}" name="address" class="form-control aerrormesage"
                    placeholder="Address" required />
                </div>
                <div class="col-sm-3">
                    <label class="mb-3">Pincode <span class="text-danger">*</span></label>
                    <input type="text" id="pincode" value="{{$address->pincode}}" name="pincode" class="form-control aerrormesage"
                    placeholder="Pincode" required />
                </div>
                <!-- <div class="col-sm-3">
                    <label class="mb-3">Block <span class="text-danger">*</span></label>
                    <input type="text" id="block" value="{{$address->block}}" name="block" class="form-control aerrormesage"
                    placeholder="Block" required />
                </div> -->
            
                <div class="col-sm-3">
                    <label class="mb-3">City/Town <span class="text-danger">*</span></label>
                    <input type="text" id="city_town" value="{{$address->city}}" name="city" class="form-control aerrormesage"
                    placeholder="City/Town" required />
                </div>

                <div class="col-sm-3">
                    <label class="mb-3">State <span class="text-danger">*</span></label>
                    <select name="state" id="state" class="select2 form-select form-select-lg aerrormesage" data-allow-clear="true" required onchange="fetchDistrict();">
                        <option value="">Select</option>
                        @foreach($state as $key => $value)
                            <option value="{{$value->id}}" {{!empty($address) && $address->state == $value->id ? 'selected' : ''}}>{{$value->name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-3">
                    <label class="mb-3">District <span class="text-danger">*</span></label>
                    <select name="district" id="district" class="select2 form-select form-select-lg aerrormesage" data-allow-clear="true" required onchange="fetchVillage();">
                        <option value="">Select</option>
                        
                    </select>
                </div>

                <div class="col-sm-3">
                    <label class="mb-3">Block <span class="text-danger">*</span></label>
                    <select name="block" id="block" class="select2 form-select form-select-lg aerrormesage" data-allow-clear="true" required onchange="fetchVillage();">
                        <option value="">Select</option>
                        
                    </select>
                </div>

                <div class="col-sm-3">
                    <label class="mb-3">Village <span class="text-danger">*</span></label>
                    <select name="village" id="village" class="select2 form-select form-select-lg aerrormesage" data-allow-clear="true" required>
                        <option value="">Select</option>
                        
                    </select>
                </div>

                <div class="col-sm-3">
                    <label class="mb-3">Landmark </label>
                    <input type="text" id="landmark" value="{{!empty($address) ? $address->landmark : ''}}" name="landmark" class="form-control aerrormesage"
                placeholder="Landmark"  />
                </div>

                <div class="col-sm-3">
                    <label class="mb-3">Telephone With STD Code </label>
                    <div>
                        <input type="text" id="std_code" value="{{!empty($address) ? $address->std_code : ''}}" name="std_code" class="form-control aerrormesage"
                        placeholder="Std Code"  />
                        <input type="text" id="telephone" value="{{!empty($address) ? $address->telephone : ''}}" name="telephone" class="form-control aerrormesage"
                        placeholder="Telephone"  />
                    </div>
                </div>

                <div class="col-sm-3">
                    <label class="mb-3">Mobile No <span class="text-danger">*</span></label>
                    <div class="form-password-toggle aerrormesage">
                        <input type="number" id="mobile_no" value="{{$address->mobile_no}}" name="mobile_no" class="form-control" placeholder="Mobile No" required maxlength="10"/>
                    </div>
                </div>

                <div class="col-sm-3">
                    <label class="mb-3">Email Id <span class="text-danger">*</span></label>
                    <input type="text" id="email_id" oninput="sanitize(this, 'email');" value="{{!empty($address) ? $address->email : ''}}" name="email" class="form-control aerrormesage"
                    placeholder="Email" required />
                </div>

                <div class="col-sm-3">
                    <label class="mb-3">Website</label>
                    <input type="text" id="website" value="{{!empty($address) ? $address->website : ''}}" name="website" class="form-control aerrormesage"
                    placeholder="Website" />
                </div>

                <div class="col-sm-3">
                    <label class="mb-3">Local Police Station <span class="text-danger">*</span></label>
                    <input type="text" id="local_police_station" name="police_station" class="form-control aerrormesage"
                    placeholder="Local Police Station" value="{{!empty($address) ? $address->police_station : ''}}" required />
                </div>

                
                <div class="col-sm-3">
                    <label class="mb-3">Latitude <span class="text-danger">*</span></label>
                    <input type="text" id="latitude" value="{{!empty($address) ? $address->latitude : ''}}"  name="latitude" class="form-control aerrormesage"
                    placeholder="Latitude" required />
                </div>

                <div class="col-sm-3">
                    <label class="mb-3">Longitude <span class="text-danger">*</span></label>
                    <input type="text" id="longitude" value="{{!empty($address) ? $address->longitude : ''}}"  name="longitude" class="form-control aerrormesage"
                    placeholder="Longitude" required />
                </div>

                <div class="col-sm-3">
                    <label for="BMI" class="mb-2">Locality <span class="text-danger">*</span></label>
                    <div class="d-flex aerrormesage">
                        <div class="form-check">
                            <input class="form-check-input"
                                type="radio" name="locality"
                                id="option3" {{!empty($address) && $address->locality == "Rural" ? 'checked' : ''}} value="Rural">
                            <label class="form-check-label"
                                for="option3">
                                Rural
                            </label>
                        </div>
                        <div class="form-check ms-4">
                            <input class="form-check-input"
                                type="radio" name="locality"
                                id="option4" {{!empty($address) && $address->locality == "Urban" ? 'checked' : ''}}  value="Urban">
                            <label class="form-check-label"
                                for="option4">
                                Urban
                            </label>
                        </div>
                    </div>
                </div>

                @if(\App\CentralLogics\Helpers::isbtnenabled(@$hospital->status))
                <div class="col-12">
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-primary saveAddress" >Save</button>
                    </div>
                </div>      
                @endif                          
            </div>
        </div>
    </div>
</form>

<script>

    @if(!empty($address))
        fetchDistrict('$address->state', '{{$address->district}}');
        fetchblock('$address->state', '{{$address->district}}', '{{$address->block}}');
        fetchVillage('$address->state', '{{$address->district}}', '{{$address->village}}', '{{$address->block}}');
   @endif
    function fetchDistrict(state = '', district = '') {
        let state_id = $('#state').val(); // Get selected type ID
        if(!state_id) {
            state_id = state_id;
        }
        if (state_id) {
            $.ajax({
                url: '{{route("hospital.empanelmentRegistration.getDistrict")}}', 
                type: 'POST',
                data: {
                    '_token': '{{csrf_token()}}',
                    'state_id' : state_id
                },
                dataType: 'json',
                success: function (data) {
                    // Clear previous options
                    $('#district').empty().append('<option value="">Select</option>');
                    $('#district').empty().append('<option value="">Select</option>');

                    // Populate new options
                    $.each(data, function (key, subType) {
                        var selected = '';
                        var selected2 = '';
                        if(district == subType.id) {
                            selected = 'selected';
                        }
                        
                        $('#district').append(`<option value="${subType.id}" ${selected}>${subType.name}</option>`);
                    });
                },
                error: function () {
                    alert('Failed to fetch subtypes. Please try again.');
                }
            });
        } else {
            // Clear subtypes if no type is selected
            $('#district').empty().append('<option value="">Select</option>');
        }
    }

    function fetchVillage(state = '', district = '', village = '', block = '') {
        let district_id = $('#district').val(); // Get selected type ID
        let block_id = $('#block').val(); // Get selected type ID
        if(!district_id) {
            district_id = district;
        }

        if(!block_id) {
            block_id = block;
        }

        if (district_id) {
            $.ajax({
                url: '{{route("hospital.empanelmentRegistration.getVillage")}}', 
                type: 'POST',
                data: {
                    '_token': '{{csrf_token()}}',
                    'district_id' : district_id,
                    'block_id' : block_id
                },
                dataType: 'json',
                success: function (data) {
                    // Clear previous options
                    $('#village').empty().append('<option value="">Select</option>');

                    // Populate new options
                    $.each(data, function (key, subType) {
                        var selected = '';
                        var selected2 = '';
                        if(village == subType.id) {
                            selected = 'selected';
                        }
                        
                        $('#village').append(`<option value="${subType.id}" ${selected}>${subType.name}</option>`);
                    });
                },
                error: function () {
                    alert('Failed to fetch subtypes. Please try again.');
                }
            });
        } else {
            // Clear subtypes if no type is selected
            $('#village').empty().append('<option value="">Select</option>');
        }
    }

       
    function fetchblock(state = '', district = '', block = '') {
        let district_id = $('#district').val(); // Get selected type ID
        if(!district_id) {
            district_id = district;
        }
        if (district_id) {
            $.ajax({
                url: '{{route("hospital.empanelmentRegistration.getBlocks")}}', 
                type: 'POST',
                data: {
                    '_token': '{{csrf_token()}}',
                    'district_id' : district_id
                },
                dataType: 'json',
                success: function (data) {
                    // Clear previous options
                    $('#block').empty().append('<option value="">Select</option>');

                    // Populate new options
                    $.each(data, function (key, subType) {
                        var selected = '';
                        var selected2 = '';
                        if(block == subType.id) {
                            selected = 'selected';
                        }
                        
                        $('#block').append(`<option value="${subType.id}" ${selected}>${subType.name}</option>`);
                    });
                },
                error: function () {
                    alert('Failed to fetch subtypes. Please try again.');
                }
            });
        } else {
            // Clear subtypes if no type is selected
            $('#block').empty().append('<option value="">Select</option>');
        }
    }

    $('.saveAddress').click(function () {
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
                // Create a FormData object
                var formData = new FormData($('#empanelmentaddress')[0]);
                // Send an AJAX request
                $.ajax({
                    url: '{{route("hospital.upgrade-address-details", [$uuid, $hospital_id])}}', // Replace with your server endpoint
                    headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    type: 'POST',
                    data: formData,
                    processData: false, // Prevent jQuery from automatically processing the data
                    contentType: false, // Prevent jQuery from automatically setting content type
                    success: function (response) {
                        ldrhide();
                        successMessage(response.message);
                        swal({
                            title: "Confirm Submission?",
                            text: 'Do you still want to make changes in any form, or do you want to submit the application?',
                            type: "warning",
                            buttons: {
                                cancel: {
                                    visible: true,
                                    text: "No",
                                    className: "btn btn-danger",
                                },
                                confirm: {
                                    text: "Submit Application",
                                    className: "btn btn-success",
                                },
                            },
                        }).then((willSubmit) => {
                            if (willSubmit) {
                                $("#declModal").modal('show');
                            } else {
                                $('.nav-link').removeClass('active');
                                $('.tab-pane').removeClass('show active');
                                $(`.step3`).addClass('show active');
                                $(`.navstep3`).addClass('active');
                                setTimeout(() => {
                                    $(`.step3`).on('click', function(event) {
                                        if (event.target.closest('.nav-item .active')) {
                                            setSlider(event.target.closest('.nav-item'));
                                        }
                                    });
                                    // $('.step1Icon').show();
                                    $('.schemetab').removeClass('pending').addClass('success');
                                    loadStep(3);                            
                                }, 1000);
                            }
                        });
                    },
                    error: function (xhr) {
                    ldrhide();
                    $('.error').remove();
                    
                    if (xhr.status === 422) { 
                        let errors = xhr.responseJSON.errors;
                        for (let field in errors) {
                                $(`[name="${field}"]`).closest('.aerrormesage').after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                    } else {
                        alert('Something went wrong. Please try again later.');
                    }
                    }
                });
            }
        });
    });
</script>