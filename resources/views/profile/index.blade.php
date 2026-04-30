@extends('layouts.hospital.app')
@section('title','User Profile')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-6">
                <div class="user-profile-header-banner">
                    <img src="{{asset('public/front/assets/img/pages/profile-banner.png')}}" alt="Banner image" class="rounded-top" />
                </div>
                <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-5">
                    <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
                        <img
                            src="{{$user->profile_image}}"
                            alt="user image"
                            class="d-block h-auto ms-0 ms-sm-5 rounded-4 user-profile-img" />
                    </div>
                    <div class="flex-grow-1 mt-4 mt-sm-12">
                        <div
                            class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-5 flex-md-row flex-column gap-6">
                            <div class="user-profile-info">
                                <h4 class="mb-2">{{@$user->name}}</h4>
                                <ul class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-4">                                    
                                    <li class="list-inline-item">
                                        <i class="ri-calendar-line me-2 ri-24px"></i><span class="fw-medium"> Joined {{date('F Y', strtotime(@$user->created_at))}}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/ Header -->

    <!-- User Profile Content -->
    <div class="row">
        <div class="col-xl-4 col-lg-5 col-md-5">
            <!-- About User -->
            <div class="card mb-6">
                <div class="card-body">
                    <small class="card-text text-uppercase text-muted small">About</small>
                    <ul class="list-unstyled my-3 py-1">
                        <li class="d-flex align-items-center mb-4">
                            <i class="ri-user-3-line ri-24px"></i><span class="fw-medium mx-2">Full Name:</span>
                            <span>{{ $user->name }}</span>
                        </li>
                        <li class="d-flex align-items-center mb-4">
                            <i class="ri-check-line ri-24px"></i><span class="fw-medium mx-2">Status:</span>
                            <span>Active</span>
                        </li>
                        <li class="d-flex align-items-center mb-4">
                            <i class="ri-star-smile-line ri-24px"></i><span class="fw-medium mx-2">Role:</span>
                            <span>{{ $user->getRoleNames()->first() }}</span>
                        </li>
                        @if(@$user->state)
                        <li class="d-flex align-items-center mb-4">
                            <i class="ri-flag-2-line ri-24px"></i><span class="fw-medium mx-2">Country:</span>
                            <span>{{@$user->state}}</span>
                        </li>
                        @endif
                        @if(@$user->gender)
                        <li class="d-flex align-items-center mb-4">
                            <i class="{{$user->gender == 'Male' ? 'ri-men-line' : 'ri-women-line' }} ri-24px"></i><span class="fw-medium mx-2">Gender:</span>
                            <span>{{@$user->gender}}</span>
                        </li>
                        @endif
                    </ul>
                    <small class="card-text text-uppercase text-muted small">Contacts</small>
                    <ul class="list-unstyled my-3 py-1">
                        <li class="d-flex align-items-center mb-4">
                            <i class="ri-phone-line ri-24px"></i><span class="fw-medium mx-2">Contact:</span>
                            <span>{{@$user->mobile_no}}</span>
                        </li>
                        <li class="d-flex align-items-center mb-2">
                            <i class="ri-mail-open-line ri-24px"></i><span class="fw-medium mx-2">Email:</span>
                            <span>{{@$user->email}}</span>
                        </li>
                    </ul>
                </div>
            </div>
            <!--/ About User -->
        
        </div>
        <div class="col-xl-8 col-lg-7 col-md-7">
            <div class="card card-action mb-6">
                <div class="card-header align-items-center">
                    <h5 class="card-action-title mb-0">
                        <i class="ri-shield-user-fill ri-24px text-body me-4"></i>User Update
                    </h5>
                </div>
                <div class="card-body pt-2">
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ __("Update your account's profile information and email address.") }}
                    </p>
                    <form onSubmit="return false" id="updateform" enctype="multipart/form-data">
						<div class="row mt-3 g-5">

                            <div class="col-md-6 col-lg-6">
								<div class="form-floating form-floating-outline profileerror">
									<input type="text" id="userid" name="userid" class="form-control" placeholder="UserId" value="{{ @$user->userid }}" readonly />
									<label for="userid">UserId <span class="text-danger">*</span></label>
								</div>
							</div>

							<div class="col-md-6 col-lg-6">
								<div class="form-floating form-floating-outline profileerror">
									<input type="text" id="name" name="name" oninput="sanitize(this, 't');"  class="form-control" placeholder="Name" value="{{ @$user->name }}" />
									<label for="name">Name <span class="text-danger">*</span></label>
								</div>
							</div>
							
							<div class="col-md-6 col-lg-6">
								<div class="form-floating form-floating-outline profileerror">
									<input type="email" id="email" name="email" oninput="sanitize(this, 'email');"  class="form-control" placeholder="Email" value="{{ @$user->email }}" />
									<label for="email">Email <span class="text-danger">*</span></label>
								</div>
							</div>
                            
                            <div class="col-md-6 col-lg-6">                                                                
                                <div class="form-floating form-floating-outline profileerror">
                                    <select class="form-select" name="gender" id="gender" required>
                                        <option value="" >Select</option>
                                        <option value="Male" {{$user->gender == 'Male' ? 'selected' : ''}}>Male</option>
                                        <option value="Female" {{$user->gender == 'Female' ? 'selected' : ''}}>Female</option>
                                    </select>
                                    <label for="gender" class="form-label">Gender</label>
                                </div>                                
                            </div>

                            <div class="col-md-6 col-lg-6">
								<div class="form-floating form-floating-outline profileerror">
									<input type="text" id="mobile_no" name="mobile_no" oninput="mobileinput(this)";  class="form-control" placeholder="Mobile No" value="{{ @$user->mobile_no }}" />
									<label for="mobile_no">Mobile Number <span class="text-danger">*</span></label>
								</div>
							</div>

                            <div class="col-md-6 col-lg-6">
                                <div class="file-upload-section profileerror">
                                    <div class="file-upload-wrapper">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#6200ea">
                                            <path d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                                        </svg>
                                        <p> <strong>Change Profile Image</strong> </p>
                                    </div>
                                    <input type="file" class="file-input d-none " name="avatar" id="avatar"/>
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

                            <div class="d-flex justify-content-end mt-3">
                                <button type="button" id="submitForm" class="btn mt-2 btn-primary justify-content-end rounded-0" >Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-5 col-md-5"></div>
        <div class="col-xl-8 col-lg-7 col-md-7">
            <div class="card card-action mb-6">
                <div class="card-header align-items-center">
                    <h5 class="card-action-title mb-0">
                        <i class="ri-lock-2-fill ri-24px text-body me-4"></i>Change Password
                    </h5>
                </div>
                <div class="card-body pt-2">
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Ensure your account is using a long, random password to stay secure.') }}
                    </p>
                    <form onSubmit="return false" id="updatepassword" enctype="multipart/form-data">
						<div class="row mt-3 g-5">                         

                            <div class="row">
                                <div class="mb-5 col-md-6 form-password-toggle">
                                    <div class="input-group input-group-merge passworderror">
                                        <div class="form-floating form-floating-outline">
                                            <input
                                            class="form-control"
                                            type="password"
                                            name="current_password"
                                            id="currentPassword"
                                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                                            <label for="currentPassword">Current Password</label>
                                        </div>
                                        <span class="input-group-text eyeicon cursor-pointer"><i class="ri-eye-off-line"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row ">
                                <div class="col-md-6 form-password-toggle">
                                    <div class="input-group input-group-merge passworderror">
                                        <div class="form-floating form-floating-outline">
                                            <input
                                            class="form-control"
                                            type="password"
                                            id="newPassword"
                                            name="password"
                                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                                            <label for="newPassword">New Password</label>
                                        </div>
                                        <span class="input-group-text eyeicon cursor-pointer"><i class="ri-eye-off-line"></i></span>
                                    </div>
                                </div>
                                <div class="col-md-6 form-password-toggle">
                                    <div class="input-group input-group-merge passworderror">
                                        <div class="form-floating form-floating-outline">
                                            <input
                                            class="form-control"
                                            type="password"
                                            name="password_confirmation"
                                            id="confirmPassword"
                                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                                            <label for="confirmPassword">Confirm New Password</label>
                                        </div>
                                        <span class="input-group-text eyeicon cursor-pointer"><i class="ri-eye-off-line"></i></span>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-3">
                                <button type="button" id="submitpassword" class="btn mt-2 btn-primary justify-content-end rounded-0" >Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--/ User Profile Content -->
</div>
@endsection

@push('scripts')
    <script>
        $('#submitForm').on('click', function() {
            swal({
                title: "Confirm Submission?",
                text: 'Are you sure you want to proceed?',
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
                    $('.error').remove();
                
                    var formData = new FormData($('#updateform')[0]);
                    formData.append('_method', 'PATCH');
                    $.ajax({
                        url: '{{route("profile.update")}}',
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
                                location.reload();
                            } else {
                                errorMessage(response.message);
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
                                        $(`[name="${field}"]`).closest('.profileerror').after(`<div class="error text-danger">${errors[field][0]}</div>`);
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

        $('#submitpassword').on('click', function() {
            swal({
                title: "Confirm Submission?",
                text: 'Are you sure you want to change old password?',
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
                    $('.error').remove();
                
                    var formData = new FormData($('#updatepassword')[0]);
                    formData.append('_method', 'put');
                    $.ajax({
                        url: '{{route("password.update")}}',
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
                                location.reload();
                            } else {
                                errorMessage(response.message);
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
                                        $(`[name="${field}"]`).closest('.passworderror').after(`<div class="error text-danger">${errors[field][0]}</div>`);
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

        $(document).ready(function() {
            $('.eyeicon').on('click', function () {
                const $icon = $(this).find('i');
                const $input = $(this).siblings('.form-floating').find('input');

                if ($input.attr('type') === 'password') {
                    $input.attr('type', 'text');
                    $icon.removeClass('ri-eye-off-line').addClass('ri-eye-line');
                } else {
                    $input.attr('type', 'password');
                    $icon.removeClass('ri-eye-line').addClass('ri-eye-off-line');
                }
            });
        });
    </script>
@endpush