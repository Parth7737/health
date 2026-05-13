@extends('layouts.shaadmin.app')
@section('title','ISA Form | SHA')
@section('content')
    <div class="container mt-2">
        <div class="card p-2">
            <div class="text-left text-white mb-4 card-header bg-primary">ISA User Info</div>
            <div class="card-body">
                <form id="profileForm" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                            <div class="input-group profileerror">
                                <input type="text" class="form-control vidt" oninput="sanitize(this, 't');" name="name" required id="name" placeholder="Name">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label for="age" class="form-label">Age <span class="text-danger">*</span></label>
                            <div class="input-group profileerror">
                                <input type="text" class="form-control vidt" name="age" oninput="sanitize(this, 'n');" required required id="age" placeholder="Age">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                            <div class="input-group profileerror">
                                <select class="form-select" name="gender" id="gender" required>
                                    <option value="" >Select</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label for="state" class="form-label">State <span class="text-danger">*</span></label>
                            <div class="input-group profileerror">
                                <select id="state" class="form-select" name="state" required>
                                    <option value="">Select</option>
                                    @foreach($states as $key => $value)
                                        <option value="{{$value->name}}" >{{$value->name}}</option>
                                    @endforeach
                                </select>                              
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <div class="input-group profileerror">
                                <input type="email" class="form-control" oninput="sanitize(this, 'email');" id="email" name="email" placeholder="Type here" required>
                            </div>
                            <span class="email-error"></span>
                        </div>

                        <div class="col-md-3">
                            <label for="mobileNumber" class="form-label">Mobile Number <span class="text-danger">*</span></label>
                            <div class="input-group profileerror">
                                <input type="text" class="form-control mobile_no" id="mobileNumber" name="mobile_no" placeholder="Type here" oninput="mobileinput(this);" required>
                            </div>
                            <span class="mobile-error"></span>
                        </div>

                        <div class="col-sm-3 profileerror">
                            <label for="formFile" id="propcertificateName" class="form-label">Profile Picture</label>
                            <div class="file-upload-section imgerror">
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
                                <input type="file"
                                    class="file-input d-none" name="avatar"/>
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
                        </div>
                              
                        <div class="col-md-3 ">
                            <label for="mobileNumber" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select profileerror" name="role_id" id="role_id" required>
                                <option value="">Select</option>
                                @foreach($roles as $key => $value)
                                    <option value="{{$value->id}}" >{{$value->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 ">
                            <label for="mobileNumber" class="form-label">Nature Of Employment <span class="text-danger">*</span></label>
                            <select class="form-select profileerror" name="nature_of_employment" id="nature_of_employment" required>
                                <option value="">Select</option>
                                <option value="Contractual">Contractual</option>
                                <option value="Permanent">Permanent</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="designation" class="form-label">Designation <span class="text-danger">*</span></label>
                            <div class="input-group profileerror">
                                <input type="text"oninput="sanitize(this, 't');" class="form-control vidt" name="designation" required id="designation" placeholder="Designation">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label for="userid" class="form-label">User Id <span class="text-danger">*</span></label>
                            <div class="input-group profileerror">
                                <input type="text" oninput="sanitize(this, 'm');" class="form-control vidt" name="userid" required id="userid" placeholder="User id">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <div class="input-group profileerror">
                                <input type="password" class="form-control" name="password" required id="password" placeholder="Password">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label for="confirm-password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <div class="input-group profileerror">
                                <input type="password" class="form-control" name="confirmation_password" required id="confirmation_password" placeholder="Confirm Password">
                            </div>
                        </div>     
                    <div>

                    <button type="button" class="btn btn-primary btn-sm mt-4 submitEntity">Submit</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $('.submitEntity').click(function () {
        $('.error').remove();
       ldrshow();
        var formData = new FormData($('#profileForm')[0]);
        $.ajax({
            url: @if(auth()->user()->role->name == 'ISA Admin') '{{route("isaadmin.registerIsaUser")}}' @else '{{route("shaadmin.registerIsaUser")}}' @endif,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                ldrhide();
                $('#profileForm')[0].reset();
                $('#profileForm input[type="file"]').val('');
                $('.remove-file-btn').click();

                if(response.success) {
                    successMessage(response.message);
                    window.location = '{{route("shaadmin.dashboard")}}';
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
                        $(`[name="${field}"]`).closest('.profileerror').after(`<div class="error text-danger">${errors[field][0]}</div>`);
                    }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });
    });

</script>
@endpush