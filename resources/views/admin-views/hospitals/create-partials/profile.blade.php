<form id="adminCreateProfileForm" enctype="multipart/form-data" class="row g-3">
    @csrf
    <div class="inside-left-info-box mb-3">
        <h4 class="colored-verticle-title mb-2 text-primary">Profile Information</h4>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group mb-3">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" required placeholder="Full name">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-3">
                    <label class="form-label">Gender <span class="text-danger">*</span></label>
                    <select class="form-select" name="gender" required>
                            <option value="">Select</option>
                            <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-3">
                    <label class="form-label">State <span class="text-danger">*</span></label>
                    <select class="form-select select2" name="state" required>
                        <option value="">Select</option>
                        @foreach($states as $s)
                        <option value="{{ $s->name }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-3">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" name="email" required placeholder="Email">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-3">
                    <label class="form-label">Mobile <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="mobile_no" maxlength="10" pattern="[0-9]{10}" required placeholder="10 digits">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-3">
                    <label class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" name="password" required minlength="6" placeholder="Min 6 characters">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-3">
                    <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" name="confirmation_password" required placeholder="Same as password">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-3">
                    <label class="form-label">Hospital Type <span class="text-danger">*</span></label>
                    <select class="form-select" name="hospital_type" id="createHospitalType" required>
                        <option value="">Select</option>
                        <option value="Single">Single</option>
                        <option value="Multi-Branch">Multi-Branch</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 create-branch-wrap d-none">
                <div class="form-group mb-3">
                    <label class="form-label">Parent Hospital <span class="text-danger">*</span></label>
                    <select class="form-select select2" name="hospital" id="createParentHospital">
                        <option value="0">Main</option>
                        @php $parentHospitals = \App\Models\Hospital::where('hospital_type','Multi-Branch')->where('status','Approved')->where('parent_id',0)->get(); @endphp
                        @foreach($parentHospitals as $ph)
                        <option value="{{ $ph->id }}">{{ $ph->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                
                <label class="form-label">Profile Picture</label>
                <div class="file-upload-section lierror">
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
                    <input type="file" name="avatar" class="file-input d-none"/>
                    <div class="uploaded-file file-upload-display d-none">
                    <span class="file-name">Sample.pdf</span>
                    <button type="button"
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
            </div>
            <div class="col-md-12 mt-2">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-primary" id="adminCreateProfileSubmit">SAVE</button>
                </div>
            </div>
        </div>
    </div>
</form>
