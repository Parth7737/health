<form id="editspecialitiesform" class="mb-4">
        <div class="row g-5">
            <input type="hidden" value="{{@$hrdata->id}}" name="id">

            <div class="col-md-6 col-lg-3 ">
                <div class="input-group input-group-merge sperror">
                    <div class="form-floating form-floating-outline">
                        <input type="text" class="form-control" oninput="sanitize(this, 'b');" value="{{@$hrdata->hpr_id}}" id="hpr_idedit" aria-label="Recipient's username" name="hpr_id" required aria-describedby="hpr_id">
                        <label for="hpr_id">Healthcare Proffessionals Registry Id</label>
                    </div>
                    <button class="input-group-text btnspecialitiesedit" onclick="verifyspecialitieshpr('hpr_idedit', 'btnspecialitiesedit');" type="button">Verify</button>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 ">
                <div class="form-floating form-floating-outline sperror">
                    <input type="text" id="designation" name="designation" oninput="sanitize(this, 't');" value="{{@$hrdata->designation}}" required class="form-control" />
                    <label for="designation">Type Of Human Resource</label>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 ">
                <div class="form-floating form-floating-outline sperror">
                    <select class="select2" id="speciality_idedit" name="speciality_id" required>
                        <option value="">Select</option>
                        @foreach($specialities as $key => $value)
                            <option value="{{$value->speciality->id}}" {{@$hrdata->speciality_id == $value->speciality->id ? 'selected' : ''}}>{{$value->speciality->name.' ('.$value->speciality->code.')'}}</option>
                        @endforeach
                        
                    </select>
                    <label for="speciality_id">Specialization</label>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 ">
                <div class="form-floating form-floating-outline sperror">
                    <select class="select2" id="employement_typeedit" name="employement_type" required>
                        <option value="">Select</option>
                        <option value="FullTime Consultant" {{@$hrdata->employement_type == "FullTime Consultant" ? 'selected' : ''}}>FullTime Consultant</option>
                        <option value="Visiting Consultant" {{@$hrdata->employement_type == "Visiting Consultant" ? 'selected' : ''}}>Visiting Consultant</option>
                    </select>
                    <label for="employement_type">Employment Type</label>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="form-floating form-floating-outline sperror">
                    <input type="text" id="name" oninput="sanitize(this, 't');" value="{{@$hrdata->name}}" name="name" required class="form-control" placeholder="john" />
                    <label for="name">Name</label>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="form-floating form-floating-outline sperror">
                    <input type="text" id="registration_no" oninput="sanitize(this, 'b');" value="{{@$hrdata->registration_no}}" name="registration_no" required class="form-control" placeholder="xxxxxx56" />
                    <label for="registration_no">Registration Number</label>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="form-floating form-floating-outline sperror">
                    <input type="email" id="email" oninput="sanitize(this, 'email');" name="email" value="{{@$hrdata->email}}" required class="form-control" placeholder="john@gmail.com" />
                    <label for="email">Email</label>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="form-floating form-floating-outline sperror">
                    <input type="text" id="mobile" name="mobile" value="{{@$hrdata->mobile}}" oninput="mobileinput(this);" required class="form-control" placeholder="xxxxxxx58" />
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
                    <input type="file" class="file-input d-none" name="registration_certificate" id="registration_certificate" required />
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
                <div class="preivew-certificate">
                    @if(@$hrdata->registration_certificate)
                        <label class="mt-2"><a href="{{ asset('public/storage/'.@$hrdata->registration_certificate) }}" target="_blank" class="btn btn-outline-primary btn-sm">View</a></label>
                    @endif
                </div>
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
                    <input type="file" class="file-input d-none" name="declaration_certificate" id="declaration_certificate" required />
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
                <div class="preivew-certificate">
                    @if(@$hrdata->declaration_certificate)
                        <label class="mt-2"><a href="{{ asset('public/storage/'.@$hrdata->declaration_certificate) }}" target="_blank" class="btn btn-outline-primary btn-sm">View</a></label>
                    @endif
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="form-floating form-floating-outline sperror">
                    <input type="text" id="registration_certificate_expiry" value="{{@$hrdata->registration_certificate_expiry}}" name="registration_certificate_expiry" required class="form-control datepicker" placeholder="YYYY-MM-DD" />
                    <label for="registration_certificate_expiry">Registration Certificate Expiry</label>
                </div>
            </div>

            <!-- <div class="col-md-6 col-lg-3">
                <div class="form-floating form-floating-outline sperror">
                    <input type="text" id="declaration_certificate_expiry" name="declaration_certificate_expiry" required class="form-control datepicker" placeholder="YYYY-MM-DD" />
                    <label for="declaration_certificate_expiry">Declaration Certificate Expiry</label>
                </div>
            </div> -->
            @if($hospital->status != 'Empannelled' && @$hospital->status != 'Empanelment Not Recommended by DEC')
            <div class="col-md-12">
                <div class="d-flex justify-content-end">
                    <button class="btn btn-primary editspecialities me-2" id="editspecialities" type="button">SAVE</button>
                    <button class="btn btn-primary-outline me-2" id="cancelspecialitiesedit" type="button">Cancel</button>
                </div>
            </div>
            @endif
        </div>
    </form>