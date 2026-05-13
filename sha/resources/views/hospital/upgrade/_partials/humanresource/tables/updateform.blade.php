<form id="edithrForm" class="mb-4" enctype="multipart/form-data">
    <div class="row g-5">
        <input type="hidden" value="{{@$hrdata->type}}" name="type">
        <input type="hidden" value="{{@$hrdata->id}}" name="id">
        <div class="col-md-6 col-lg-3 ">
            <div class="input-group input-group-merge mhrhprdiv hrerror">
                <div class="form-floating form-floating-outline">
                    <input type="text" class="form-control" oninput="sanitize(this, 'b');" value="{{@$hrdata->healthcare_proffessionals_registry_id}}" id="healthcare_proffessionals_registry_idedit" placeholder="john.doe" aria-label="Recipient's username" name="healthcare_proffessionals_registry_id" aria-describedby="healthcare_proffessionals_registry_id">
                    <label for="healthcare_proffessionals_registry_id">Healthcare Proffessionals Registry Id</label>
                </div>
                <button class="input-group-text btnhprinputedit" type="button" onclick="verifymhrhpr('healthcare_proffessionals_registry_idedit', 'btnhprinputedit');">Verify</button>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 ">
            <div class="form-floating form-floating-outline hrerror">
                <input type="text" id="type_of_human_resource" oninput="sanitize(this, 't');" name="type_of_human_resource" value="{{@$hrdata->type_of_human_resource}}" required class="form-control" placeholder="Rajal Gupta" />
                <label for="type_of_human_resource">Type Of Human Resource</label>
            </div>
        </div>

        <div class="col-md-6 col-lg-3 ">
            <div class="form-floating form-floating-outline hrerror">
                <select class="select2 hmnres" id="sub_type_of_human_resourceeedit" name="sub_type_of_human_resource" required @if(@$hrdata->humanResource->name == "Medical Superintendent") disabled @endif>
                    <option value="">Select</option>
                    @foreach($mhr as $key => $value)
                        <option value="{{$value->id}}" {{@$hrdata->sub_type_of_human_resource == $value->id ? 'selected' : ''}}>{{$value->name}}</option>
                    @endforeach
                </select>
                <label for="sub_type_of_human_resourceeedit">Sub-Type Of Human Resource</label>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="form-floating form-floating-outline hrerror">
                <input type="text" id="name" oninput="sanitize(this, 't');" name="name" value="{{@$hrdata->name}}" required class="form-control name" placeholder="" />
                <label for="name">Name</label>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="form-floating form-floating-outline hrerror">
                <input type="text" id="registration_number" oninput="sanitize(this, 'b');" value="{{@$hrdata->registration_number}}" name="registration_number" required class="form-control" placeholder="" />
                <label for="registration_number">Registration Number</label>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="form-floating form-floating-outline hrerror">
                <input type="email" id="email" oninput="sanitize(this, 'email');" name="email" value="{{@$hrdata->email}}" required class="form-control" placeholder="john@gmail.com" />
                <label for="email">Email</label>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="form-floating form-floating-outline hrerror">
                <input type="text" id="mobile_no" value="{{@$hrdata->mobile_no}}" name="mobile_no" oninput="mobileinput(this);" required class="form-control" placeholder="xxxxxxx58" />
                <label for="mobile_no">Mobile No</label>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <label for="formFile" class="form-label">Registration Certificate</label>                
            <div class="file-upload-section hrerror">
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
        <div class="col-md-6 col-lg-3">
            <label for="formFile" class="form-label">Declaration Certificate <a href="{{ asset('public/format/declaration-format.pdf') }}" download><small>Download Format</small></a></label>
            <div class="file-upload-section hrerror">
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

        @if($hospital->status != 'Empannelled' && @$hospital->status != 'Empanelment Not Recommended by DEC')
        <div class="col-md-12">
            <div class="d-flex justify-content-end">
                <button class="btn btn-primary me-2" id="editHRdata" type="button">SAVE</button>
                <button class="btn btn-primary-outline me-2" id="cancelhredit" type="button">Cancel</button>
            </div>
        </div>
        @endif
    </div>
</form>