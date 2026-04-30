
<form id="hospitalinfoForm">
    <div class="inside-left-info-box mb-3">
        <h4 class="colored-verticle-title">
        Hospital Information
        </h4>
        <div class="row">
            <div class="col-md-4">
                <div class="form-floating form-floating-outline mb-6">
                    <input type="text" id="name" oninput="sanitize(this, 'b');" name="name" class="form-control" placeholder="Hospital Name" value="{{ @$hospital->name }}" />
                    <label for="name">Hospital Name<span class="text-danger">*</span></label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-floating form-floating-outline mb-6">
                    <input type="text" id="code" oninput="sanitize(this, 'b');" name="code" class="form-control" placeholder="Hospital Code" value="{{ @$hospital->code }}" />
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
                            <option value="{{ $type->id }}" {{ @$hospital->type_id == $type->id?'selected':'' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                    <label for="type_id">Type<span class="text-danger">*</label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-floating form-floating-outline mb-6">
                    <input type="text" id="hospital_phone" oninput="sanitize(this, 'n','13');" name="hospital_phone" class="form-control" placeholder="Hospital Phone" value="{{ @$hospital->phone }}" />
                    <label for="hospital_phone">Hospital Phone<span class="text-danger">*</span></label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-floating form-floating-outline mb-6">
                    <input type="email" id="hospital_email" oninput="sanitize(this, 'email');" name="hospital_email" class="form-control" placeholder="Hospital Email" value="{{ @$hospital->email }}" />
                    <label for="hospital_email">Hospital Email<span class="text-danger">*</span></label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-floating form-floating-outline mb-6">
                    <input type="number" id="pincode" oninput="sanitize(this, 'n');" name="pincode" class="form-control" placeholder="Pincode" value="{{ @$hospital->pincode }}" />
                    <label for="pincode">Pincode<span class="text-danger">*</span></label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-floating form-floating-outline mb-6">
                    <input type="text" id="city" oninput="sanitize(this, 'b');" name="city" class="form-control" placeholder="City" value="{{ @$hospital->city }}" />
                    <label for="city">City<span class="text-danger">*</span></label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-floating form-floating-outline mb-6">
                    <input type="text" id="landmark" oninput="sanitize(this, 'b');" name="landmark" class="form-control" placeholder="Landmark" value="{{ @$hospital->landmark }}" />
                    <label for="landmark">Landmark</label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-floating form-floating-outline mb-6">
                    <textarea class="form-control h-px-100" id="address" name="address" placeholder="">{{ @$hospital->address }}</textarea>
                    <label for="address">Hospital Address<span class="text-danger">*</span></label>
                </div>
            </div>
        </div>
    </div>
    @if(auth()->user()->hospital_type == 'Multi-Branch' && auth()->user()->parent_id== 0)
        <div class="inside-left-info-box mb-3">
            <h4 class="colored-verticle-title">
            Chairman Details
            </h4>
            
            <div class="row">
                <div class="col-md-3">
                    <div class="form-floating form-floating-outline mb-6">
                        <input type="text" id="chairman_name" oninput="sanitize(this, 'b');" name="chairman_name" class="form-control" placeholder="Chairman/Head Name" value="{{ @$hospital->chairman->name }}" />
                        <label for="chairman_name">Chairman/Head<span class="text-danger">*</span></label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating form-floating-outline mb-6">
                        <input type="email" id="chairman_email" oninput="sanitize(this, 'email');" name="chairman_email" class="form-control" placeholder="Chairman/Head Email" value="{{ @$hospital->chairman->email }}" />
                        <label for="chairman_email">Chairman/Head Email<span class="text-danger">*</span></label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating form-floating-outline mb-6">
                        <input type="password" id="password" oninput="sanitize(this, 'b');" name="password" class="form-control" placeholder="" value="" />
                        <label for="password">Password @if(@$hospital->chairman->name == '')<span class="text-danger">*</span>@endif</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating form-floating-outline mb-6">
                        <input type="password" id="confirmation_password" oninput="sanitize(this, 'b');" name="confirmation_password" class="form-control" placeholder="" value="" />
                        <label for="confirmation_password">Confirm Password @if(@$hospital->chairman->name == '')<span class="text-danger">*</span>@endif</label>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if(@$hospital->status == "Draft" || !@$hospital || @$hospital->status == "Rejected")
        <div class="col-md-12 mt-2">
            <div
                class="d-flex justify-content-end">
                <button type="button"
                class="btn btn-primary savehospitalinfo">SAVE</button>
            </div>
        </div>
    @endif
</form>

<script>
    $('.savehospitalinfo').click(function () {
        ldrshow();
      $('.error').remove();

        var step = 3;
        // Create a FormData object
        var formData = new FormData($('#hospitalinfoForm')[0]);
        $.ajax({
            url: '{{route("hospital.empanelmentRegistration.hospitalinfo", [$uuid])}}',
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
                    successMessage(response.message);
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
                        $('.step2Icon').show();
                        loadStep(response.step);     
                        
                    }, 1000);
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }              
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
</script>