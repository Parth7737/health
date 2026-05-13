<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('public/front/assets/vendor/libs/toastr/toastr.css') }}" />
    <style>
        .loader-overlay {
            position: fixed;
            width: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99999999999;
            top: 0;
            left: 0;
            height: 100vh;
        }
        body {
            background-color: #f8f9fa;
        }
        .update-profile-container, .role-container {
            margin: 2rem auto;
            background-color: white;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-label {
            font-weight: bold;
        }
        .upload-box {
            width: 100px;
            height: 100px;
            border: 2px dashed #6c757d;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
        }
        .upload-box i {
            font-size: 2rem;
            color: #6c757d;
        }
        .upload-box input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body class="main ">
    <div class="container-fluide">
        <div class="card p-2">
            <div class="text-left text-white mb-4 card-header bg-success">Update Profile</div>
            <div class="card-body">
                <form id="profileForm" enctype="multipart/form-data">
                    <div class="row g-3">
                        <!-- Mobile Number -->
                        <div class="col-md-3">
                            <label for="name" class="form-label">Name</label>
                            <div class="input-group profileerror">
                                <input type="text" class="form-control vidt" oninput="sanitize(this, 't');" value="{{$data->name}}" name="name" required id="name" placeholder="Name">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label for="age" class="form-label">Age</label>
                            <div class="input-group profileerror">
                                <input type="text" class="form-control vidt" name="age" oninput="sanitize(this, 'n');" value="{{$data->age}}"  required required id="age" placeholder="Age">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label for="gender" class="form-label">Gender</label>
                            <div class="input-group profileerror">
                                <select class="form-select" name="gender" id="gender" required>
                                    <option value="" >Select</option>
                                    <option value="Male" {{$data->gender == 'Male' ? 'selected' : ''}}>Male</option>
                                    <option value="Female" {{$data->gender == 'Female' ? 'selected' : ''}}>Female</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label for="state" class="form-label">State</label>
                            <div class="input-group profileerror">
                                <select id="state" class="form-select" name="state" required>
                                    <option value="">Select</option>
                                    @foreach($states as $key => $value)
                                        <option value="{{$value->name}}" {{$data->state == $value->name ? 'selected': ''}}>{{$value->name}}</option>
                                    @endforeach
                                </select>                              
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-md-3">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group profileerror">
                                <input type="email" class="form-control" value="{{$data->email}}" oninput="sanitize(this, 'email');" id="email" name="email" placeholder="Type here" {{$data->email ? 'readonly' : 'required'}}>
                                @if($data->email == "")
                                <button type="button" class="btn btn-outline-secondary emailButton"  onclick="CheckEmail();" {{$data->email ?'disabled' : ''}}>VERIFY</button>
                                @endif
                            </div>
                            <span class="email-error error"></span>
                        </div>

                        <div class="col-md-3 " id="email_otp_block" style="display:none">
                            <label class="form-label">Enter Email OTP*</label>
                            <input type="text" name="email_otp" id="email_otp" class="form-control" onchange="OTPVerification();" placeholder="Type here">
                            <a href="javascript:;" onClick="ResendOtp('Email')" class="resendEmailOtp" style="float:right;">Resend OTP</a>
                            <span class="emailotpverify error"></span>
                        </div>

                        <div class="col-md-3">
                            <label for="mobileNumber" class="form-label">Mobile Number</label>
                            <div class="input-group profileerror">
                                <input type="text" class="form-control mobile_no" id="mobileNumber" name="mobile_no" placeholder="Type here" oninput="validateMobileNo(this)"; value="{{$data->mobile_no}}" {{$data->mobile_no ? 'readonly' : 'required'}}>
                                @if($data->mobile_no == "")
                                <button type="button"  class="btn btn-outline-secondary verifymobile_no" onclick="CheckMobile();">VERIFY</button>
                                @endif
                            </div>
                            <span class="mobile-error error"></span>
                        </div>

                        <div class="col-md-3" id="mobile_otp_block" style="display:none">
                            <label class="form-label">Enter Mobile OTP*</label>
                            <input type="text" name="mobile_otp" id="mobile_otp" class="form-control" onChange="MobileOTPVerification();" placeholder="Type here">
                            <a href="javascript:;" onClick="ResendOtp('Mobile')" class="resendMobileOtp" style="float:right">Resend OTP</a>
                            <span class="mobileotpverify error"></span>
                        </div>
                    </div>

                    <!-- Upload Picture -->
                    <div class="mt-3">
                        <label for="uploadPicture" class="form-label">Upload Peofile Picture</label>
                            
                        <div class="upload-box">
                            
                            <i class="fas fa-upload"></i>
                            <input type="file" name="avatar" id="uploadPicture" accept="image/jpg,image/jpeg,image/png">
                            @if($data->avatar)
                                <img id="" src="{{asset('public/storage/'.$data->avatar)}}" alt="Image Preview" class="mt-3" style="max-width: 100px;  border: 1px solid #ccc; padding: 5px;">

                            @endif
                            <img id="imagePreview" src="" alt="Image Preview" class="mt-3" style="max-width: 96px; max-height:96px; display: none; border: 1px solid #ccc; padding: 5px;">

                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="button" id="submitForm" class="btn btn-success btn-sm w-20 mt-4">Submit</utton>
                </form>
            </div>
        </div>

        <div class="card stpe2  mt-4 p-2" @if($data->is_user_update == 0) style="display:none;" @endif>
            <div class="text-left text-white mb-4 card-header bg-success">Add Role Details</div>
            <form id="step2Form">
                <div class="row g-3">
                    <!-- Mobile Number -->
                    <div class="col-md-3 ">
                        <label for="mobileNumber" class="form-label">Parent Entity</label>
                        <select class="form-select entityerror" name="parent_entity" id="parent_entity" required>
                            <option value="">Select</option>
                            @foreach($states as $key => $value)
                                <option value="{{$value->name}}" data-id="{{$value->id}}" {{$data->parent_entity == $value->name ? 'selected' : ''}}>SHA {{$value->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 ">
                        <label for="mobileNumber" class="form-label">Entity Type</label>
                        <select class="form-select entityerror" name="entity_type" id="entity_type" required>
                            <option value="">Select</option>
                            @foreach($entityTypes as $key => $value) 
                                <option value="{{$value->name}}" {{$data->entity_type == $value->name ? 'selected' : ''}}>{{$value->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 ">
                        <label for="mobileNumber" class="form-label">Entity Name</label>
                        <select class="form-select entityerror" onchange="getDistrict();" name="entity_name" id="entity_name" required>
                            <option value="">Select</option>
                            @foreach($entities as $key => $value)
                                <option value="{{$value->name}}" @if($data->entity_name == $value->name) selected @endif data-type="{{$value->type}}">{{$value->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 district_block " style="display:none;">
                        <label for="district" class="form-label">District</label>
                        <select class="form-select entityerror" name="district" id="district" required>
                            <option value="">Select</option>
                        </select>
                    </div>

                    <div class="col-md-3 ">
                        <label for="mobileNumber" class="form-label">Role</label>
                        <select class="form-select entityerror" name="user_role" id="user_role" required>
                            <option value="">Select</option>
                            @foreach($roles as $key => $value)
                                <option value="{{$value->id}}" data-entity="{{$value->entity}}" {{$data->user_role == $value->id ? 'selected' : ''}} >{{$value->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="button" class="btn btn-success btn-sm mt-4 submitEntity">Submit</button>
            </form>
        </div>

        <div class="card step3 mt-4 p-2 " @if($data->is_entity_update == 0) style="display:none;" @endif>
            <div class="text-left text-white mb-4 card-header bg-success">User Data</div>
            <form id="step3Form">
                <div class="row g-3">
                    <!-- Mobile Number -->
                    <div class="col-md-3 ">
                        <label for="mobileNumber" class="form-label">Nature Of Employment</label>
                        <select class="form-select crederror" name="nature_of_employment" id="nature_of_employment" required>
                            <option selected>Select</option>
                            <option value="Contractual" {{$data->nature_of_employment == 'Contractual' ? 'selected' : ''}}>Contractual</option>
                            <option value="Permanent">Permanent</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="designation" class="form-label">Designation</label>
                        <div class="input-group crederror">
                            <input type="text" value="{{$data->designation}}" oninput="sanitize(this, 't');" class="form-control vidt" name="designation" required id="designation" placeholder="Designation">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="userid" class="form-label">User Id</label>
                        <div class="input-group crederror">
                            <input type="text" value="{{$data->userid}}" oninput="sanitize(this, 'm');" class="form-control vidt" name="userid" required id="userid" placeholder="User id">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group crederror">
                            <input type="password" class="form-control" name="password" required id="password" placeholder="Password">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="confirm-password" class="form-label">Confirm Password</label>
                        <div class="input-group crederror">
                            <input type="password" class="form-control" name="confirmation_password" required id="confirmation_password" placeholder="Confirm Password">
                        </div>
                    </div>                   
                </div>

                <!-- Submit Button -->
                <button type="button" class="btn btn-success btn-sm mt-4 submitUserData">Submit</button>
            </form>
        </div>
        
    </div>
    <div class="loader-overlay" style="display:none">
        <div class="spinner-grow text-success" style="width: 5rem; height: 5rem;"  role="status">
        </div>
    </div>
   
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/js/all.min.js"></script>
    <script src="{{asset('public/front/assets/vendor/libs/toastr/toastr.js') }}"></script>
    <script src="{{asset('public/front/assets/js/ui-toasts.js') }}"></script>
    <script src="{{ asset('public/js/plugin/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{asset('public/front/assets/js/sanitize.js') }}"></script>

    <script>
        @if($data->mobile_no == "" || $data->email == "")
            btnenabled();
        @endif

        function btnenabled() {
            console.log('hello');
            var mobile_otp =  @if($data->mobile_no == "") $("#mobile_otp").val() @else 1 @endif;
            var email_otp = @if($data->email  == "") $("#email_otp").val() @else 1 @endif;
            
            
            if(mobile_otp != "" && email_otp != "") {
                $("#submitForm").removeAttr('disabled');
            } else {
                $("#submitForm").attr('disabled', true);
            }
        }
        
        
        function errorMessage(msg){            
            var shortCutFunction = 'error',
            title = 'Error',
            
            prePositionClass =
            typeof toastr.options.positionClass === 'undefined' ? 'toast-top-right' : toastr.options.positionClass;
            toastr.options.showDuration = 300;
            toastr.options = {
                maxOpened: 1,
                autoDismiss: true,
                closeButton: true,
                newestOnTop: true,
                progressBar:true,
                positionClass: 'toast-top-right',
                onclick: null,
            };    
            var $toast = toastr[shortCutFunction](msg, title); // Wire up an event handler to a button in the toast, if it exists
            $toastlast = $toast;
        }
        function successMessage(msg){
            var shortCutFunction = 'success',
            title = 'Success',
            
            prePositionClass =
            typeof toastr.options.positionClass === 'undefined' ? 'toast-top-right' : toastr.options.positionClass;
            toastr.options.showDuration = 300;
            toastr.options = {
                maxOpened: 1,
                autoDismiss: true,
                closeButton: true,
                newestOnTop: true,
                progressBar:true,
                positionClass: 'toast-top-right',
                onclick: null,
            };    
            var $toast = toastr[shortCutFunction](msg, title); // Wire up an event handler to a button in the toast, if it exists
            $toastlast = $toast;
        }

        function getDistrict() {
            var entity_name = $("#entity_name").val();
            if(entity_name == "District Empanelment Committee") {
                fetchDistrict();
            } else {
                $(".district_block").hide();
            }
        }
    function fetchDistrict(state = '', district = '') {
        const dataId = $("#parent_entity").find(':selected').data('id'); 
      let state_id = dataId; // Get selected type ID
      if(!state_id) {
         state_id = state_id;
      }
      if (state_id) {
         $.ajax({
               url: '{{route("hospital.getDistrict")}}', 
               type: 'POST',
               data: {
                  '_token': '{{csrf_token()}}',
                  'state_id' : state_id
               },
               dataType: 'json',
               success: function (data) {
                  // Clear previous options
                  $('#district').empty().append('<option value="">Select</option>');

                  // Populate new options
                  $.each(data, function (key, subType) {
                     var selected = '';
                     var selected2 = '';
                     if(district == subType.id) {
                        selected = 'selected';
                     }
                     $(".district_block").show();
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
   
    document.getElementById('entity_type').addEventListener('change', function () {
        const selectedType = this.value; // Get the selected entity type
        const entityNameDropdown = document.getElementById('entity_name');
        
        // Reset entity_name dropdown
        Array.from(entityNameDropdown.options).forEach(option => {
            if (option.value === "Select") {
                option.style.display = ''; // Keep the default "Select" option
                option.selected = true; // Reset selection to default
            } else if (option.dataset.type === selectedType) {
                option.style.display = ''; // Show options that match the selected type
            } else {
                option.style.display = 'none'; // Hide options that do not match
            }
        });
    });

    document.getElementById('entity_name').addEventListener('change', function () {
        const selectedType = this.value; // Get the selected entity type
        const entityNameDropdown = document.getElementById('user_role');
        
        // Reset entity_name dropdown
        Array.from(entityNameDropdown.options).forEach(option => {
            if (option.value === "Select") {
                option.style.display = ''; // Keep the default "Select" option
                option.selected = true; // Reset selection to default
            } else if (option.dataset.entity === selectedType) {
                option.style.display = ''; // Show options that match the selected type
            } else {
                option.style.display = 'none'; // Hide options that do not match
            }
        });
    });

        function validateMobileNo(input) {
            // Remove any non-numeric characters
            input.value = input.value.replace(/[^0-9]/g, '');

            // Limit the length to 10 digits
            if (input.value.length > 10) {
                input.value = input.value.slice(0, 10);
            }
        }
        function CheckEmail() {
            var uuid = '{{$data->uuid}}';
            $(".loader-overlay").show();
            let email = $('#email').val();
            $(".email-error").text("");
            fetch('{{route("sendEmailMail")}}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ email, uuid })
            })
            .then(response => response.json())
            .then(data => {
                $(".loader-overlay").hide();
                if (data.success) {
                    $('#email_otp_block').show();
                    $('#email_otp').removeAttr('disabled');
                    $("#email").attr('disabled', true);
                    $(".emailButton").attr('disabled', true);
                } else {
                    $(".email-error").text(data.message);
                }
            });
        }

        function OTPVerification() {
            let otp = $("#email_otp").val();
            let email = $('#email').val();
            $(".emailotpverify").text("");
            VerifyOTP(otp, 'Email', email, '', '', function(data) {
                console.log(data);
                if(data.success) {
                    btnenabled();
                    $("#email_otp").attr('disabled', true);
                    $("#email").attr('readonly', true);
                    $(".emailbutton").removeClass('btn-default').addClass('btn-success');
                    $(".resendEmailOtp").hide();
                } else {
                    $(".emailotpverify").text(data.message);
                }
            });
        };

        function VerifyOTP(otp, type, email='', aadhaar='', mobile_no = '', callback) {
            $(".loader-overlay").show();
            fetch('{{route("verifyEmailOtp")}}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ otp, type, email, mobile_no, aadhaar })
            })
            .then(response => response.json())
            .then(data => {
                $(".loader-overlay").hide();
                if (callback && typeof callback === 'function') {
                    callback(data);
                }
            });
        }

        function CheckMobile() {
            var uuid = '{{$data->uuid}}';
            $(".loader-overlay").show();
            let mobile_no = $('.mobile_no').val();
            let email = $('#email').val();
            $(".mobile-error").text("");
            fetch('{{route("sendMobileMail")}}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ email, mobile_no, uuid })
            })
            .then(response => response.json())
            .then(data => {
                $(".loader-overlay").hide();
                if (data.success) {
                    $('#mobile_otp_block').show();
                    $('#mobile_otp').removeAttr('disabled');
                    $("#mobileInput").attr('disabled', true);
                    $(".mobile_no").attr('required', true);
                    $('.mobilebutton').attr('disabled', true);
                } else {
                    $(".mobile-error").text(data.message);
                }
            });
        }

        function ResendOtp(type) {
            var uuid = '{{$data->uuid}}';
            $(".loader-overlay").show();
            let email = $('#email').val();
            let mobile = $('#mobileNumber').val();

            fetch('{{route("resendOTP")}}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ email, type, uuid })
            })
            .then(response => response.json())
            .then(data => {
                $(".loader-overlay").hide();
                if(type == "Mobile") {
                    if (data.success) {
                        $('#mobile_otp_block').show();
                        $('#mobile_otp').removeAttr('disabled');
                    } else {
                        $(".mobile-error").text(data.message);
                    }
                } else {
                    if (data.success) {
                        $('#email_otp_block').show();
                        $('#email_otp').removeAttr('disabled');
                    } else {
                        $(".email-error").text(data.message);
                    }
                }
            });
        }

        function MobileOTPVerification() {
            let otp = $("#mobile_otp").val();
            let mobile_no = $('.mobile_no').val();
            $(".mobileotpverify").text("");
            VerifyOTP(otp, 'Mobile', '', '', mobile_no, function(data) {
                console.log(data);
                if(data.success) {
                    console.log('hello');
                    $("#mobile_otp").attr('disabled', true);
                    $(".verifymobile_no").attr('disabled', true);
                    $(".resendMobileOtp").hide();
                    $(".mobile_no").attr('readonly', true);
                    btnenabled();
                } else {
                    $(".mobileotpverify").text(data.message);
                }
            });
        };


        $('#submitForm').click(function () {
            $('.error').remove();
            // Create a FormData object
            var formData = new FormData($('#profileForm')[0]);
            $(".loader-overlay").show();
            // Send an AJAX request
            $.ajax({
                url: '{{route("register.updateProfile", $data->uuid)}}', // Replace with your server endpoint
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                type: 'POST',
                data: formData,
                processData: false, // Prevent jQuery from automatically processing the data
                contentType: false, // Prevent jQuery from automatically setting content type
                success: function (response) {
                    $(".loader-overlay").hide();
                    // alert('Form submitted successfully!');
                    $('.stpe2').show();
                    successMessage(response.message);
                    console.log(response); // Handle server response
                },
                error: function (xhr) {
                    $(".loader-overlay").hide();
                    // Clear previous errors
                    $('.error').remove();
                    
                    if (xhr.status === 422) { // Laravel validation error
                        let errors = xhr.responseJSON.errors;
                        for (let field in errors) {
                            // Append error message below the respective input
                            $(`[name="${field}"]`).closest('.profileerror').after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                    } else {
                        errorMessage('Something went wrong. Please try again later.');
                    }
                }
            });
        });

        document.getElementById('uploadPicture').addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const preview = document.getElementById('imagePreview');
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });

        $('.submitEntity').click(function () {
            $('.error').remove();
            // Create a FormData object
            $(".loader-overlay").show();
            var formData = new FormData($('#step2Form')[0]);

            // Send an AJAX request
            $.ajax({
                url: '{{route("register.updateentity", $data->uuid)}}', // Replace with your server endpoint
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                type: 'POST',
                data: formData,
                processData: false, // Prevent jQuery from automatically processing the data
                contentType: false, // Prevent jQuery from automatically setting content type
                success: function (response) {
                    $(".loader-overlay").hide();
                    // alert('Form submitted successfully!');
                    $('.step3').show();
                    successMessage(response.message);
                    console.log(response); // Handle server response
                },
                error: function (xhr) {
                    $(".loader-overlay").hide();
                    // Clear previous errors
                    $('.error').remove();
                    
                    if (xhr.status === 422) { // Laravel validation error
                        let errors = xhr.responseJSON.errors;
                        for (let field in errors) {
                            // Append error message below the respective input
                            $(`[name="${field}"]`).closest('.entityerror').after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                    } else {
                        errorMessage('Something went wrong. Please try again later.');
                    }
                }
            });
        });

        $('.submitUserData').click(function () {
            $('.error').remove();
            // Create a FormData object
            $(".loader-overlay").show();
            var formData = new FormData($('#step3Form')[0]);

            // Send an AJAX request
            $.ajax({
                url: '{{route("register.updateUserData", $data->uuid)}}', // Replace with your server endpoint
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                type: 'POST',
                data: formData,
                processData: false, // Prevent jQuery from automatically processing the data
                contentType: false, // Prevent jQuery from automatically setting content type
                success: function (response) {
                    // alert('Form submitted successfully!');
                    $(".loader-overlay").hide();
                    successMessage(response.message);
                    
                    window.location.href = '{{route("hospital.login")}}';
                    $('.step3').show();
                    console.log(response); // Handle server response
                },
                error: function (xhr) {
                    $(".loader-overlay").hide();
                    $('.error').remove();
                    
                    if (xhr.status === 422) { 
                        let errors = xhr.responseJSON.errors;
                        for (let field in errors) {
                            $(`[name="${field}"]`).closest('.crederror').after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                    } else {
                        errorMessage('Something went wrong. Please try again later.');
                    }
                }
            });
        });
    </script>
</body>
</html>
