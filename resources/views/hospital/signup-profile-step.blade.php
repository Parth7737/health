<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('public/front/assets/vendor/libs/toastr/toastr.css') }}" />
    <link rel="stylesheet" href="{{asset('public/front/assets/vendor/libs/select2/select2.css')}}" />
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
        .select2{
            border:1px solid #dee2e6;
            border-radius: 0.375rem !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow{
            top: 18px !important;
        }
        .select2-container .select2-selection--single{
            height: 36px;
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
                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                            <div class="input-group profileerror">
                                <input type="text" class="form-control" oninput="sanitize(this, 't');" value="{{$data->name}}" name="name" required id="name" placeholder="Name">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                            <div class="input-group profileerror">
                                <select class="form-select" name="gender" id="gender" required>
                                    <option value="" >Select</option>
                                    <option value="Male" {{$data->gender == 'Male' ? 'selected' : ''}}>Male</option>
                                    <option value="Female" {{$data->gender == 'Female' ? 'selected' : ''}}>Female</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label for="state" class="form-label">State <span class="text-danger">*</span></label>
                            <div class="input-group profileerror">
                                <select id="state" class="form-control select2" name="state" required>
                                    <option value="">Select</option>
                                    @foreach($states as $key => $value)
                                        <option value="{{$value->name}}" {{$data->state == $value->name ? 'selected': ''}}>{{$value->name}}</option>
                                    @endforeach
                                </select>                              
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-md-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <div class="input-group profileerror">
                                <input type="email" class="form-control" value="{{$data->email}}" oninput="sanitize(this, 'email');" id="email" name="email" placeholder="Type here" {{$data->email ? 'readonly' : 'required'}}>
                            </div>
                            <span class="email-error error"></span>
                        </div>
                        <div class="col-md-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <div class="input-group profileerror">
                                <input type="password" class="form-control" value="" id="password" name="password" placeholder="Type here">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="confirmation_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <div class="input-group profileerror">
                                <input type="email" class="form-control" value="" id="confirmation_password" name="confirmation_password" placeholder="Type here">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="mobileNumber" class="form-label">Mobile Number <span class="text-danger">*</span></label>
                            <div class="input-group profileerror">
                                <input type="text" class="form-control mobile_no" id="mobileNumber" name="mobile_no" placeholder="Type here" oninput="validateMobileNo(this)"; value="{{$data->mobile_no}}" {{$data->mobile_no ? 'readonly' : 'required'}}>
                                @if($data->mobile_no == "")
                                <button type="button"  class="btn btn-outline-secondary verifymobile_no" onclick="CheckMobile();">VERIFY</button>
                                @endif
                            </div>
                            <span class="mobile-error error"></span>
                        </div>

                        <div class="col-md-3" id="mobile_otp_block" style="display:none">
                            <label class="form-label">Enter Mobile OTP <span class="text-danger">*</span></label>
                            <input type="text" name="mobile_otp" id="mobile_otp" class="form-control" placeholder="Type here">
                            <a href="javascript:;" onClick="ResendOtp('Mobile')" class="resendMobileOtp" style="float:right">Resend OTP</a>
                            <span class="mobileotpverify error"></span>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="hospital_type" class="form-label">Hospital Type <span class="text-danger">*</span></label>
                            <div class="input-group profileerror">
                                <select class="form-select" name="hospital_type" id="hospital_type" required>
                                    <option value="" >Select</option>
                                    <option value="Single" {{$data->hospital_type == 'Single' ? 'selected' : ''}}>Single</option>
                                    <option value="Multi-Branch" {{$data->hospital_type == 'Multi-Branch' ? 'selected' : ''}}>Multi-Branch</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 hospital-branch {{$data->hospital_type == 'Multi-Branch' ? '' : 'd-none'}}">
                            @php $hospitals = App\Models\Hospital::where('hospital_type','Multi-Branch')->where('status','Approved')->get(); @endphp
                            <label for="hospital" class="form-label">Branch Hospital<span class="text-danger">*</span></label>
                            <div class="input-group profileerror">
                                <select class="form-select select2" name="hospital" id="hospital">
                                    <option value="0">Main</option>
                                    @foreach($hospitals as $hospital)
                                        <option value="{{ $hospital->id }}" {{$data->parent_id == $hospital->id ? 'selected' : ''}}>{{ $hospital->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- Upload Picture -->
                    <div class="mt-3">
                        <label for="uploadPicture" class="form-label">Upload Profile Picture <span class="text-danger">*</span></label>
                            
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
    <script src="{{asset('public/front/assets/vendor/libs/select2/select2.js')}}"></script>

    <script>
        $(".select2").select2();
        @if($data->mobile_no == "" || $data->email == "")
            btnenabled();
        @endif

        function btnenabled() {
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

        $("#mobile_otp").on("input",function(){
            sanitize(this, 'b',6);
            if($(this).val().length == 6){
                MobileOTPVerification();
            }
        })
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
            var formData = new FormData($('#profileForm')[0]);
            $(".loader-overlay").show();
            $.ajax({
                url: '{{route("register.updateProfile", $data->uuid)}}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    $(".loader-overlay").hide();
                    successMessage(response.message);
                    setTimeout(() => {
                        window.location.href="{{ route('hospital.dashboard') }}";
                    }, 1000);
                },
                error: function (xhr) {
                    $(".loader-overlay").hide();
                    $('.error').remove();
                    
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        for (let field in errors) {
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
        $("#hospital_type").on("change",function(){
            if($(this).val() == 'Single'){
                $(".hospital-branch").addClass("d-none");
            }else{
                $(".hospital-branch").removeClass("d-none");
            }
        });
    </script>
</body>
</html>
