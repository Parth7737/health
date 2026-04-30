@extends('layouts.hospital.app')

@section('title', 'Hospital Data')

@section('page_header_icon', '⚙')
@section('page_subtitle', 'Manage Hospital Data')
@section('content')

    <div class="container-fluid">
        <div class="user-profile">
            <div class="row">
                <div class="col-12">
                    <div class="row scope-bottom-wrapper user-profile-wrapper">
                        <div class="col-xxl-3 user-xl-25 col-xl-4 box-col-4">
                            <div class="card">
                                <div class="card-body">
                                    <ul class="sidebar-left-icons nav nav-pills" id="hospital-data-tab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="hospital-info-tab" data-bs-toggle="pill" href="#hospital-info" role="tab" aria-controls="hospital-info" aria-selected="true">
                                                <div class="nav-rounded">
                                                    <div class="product-icons"><i class="fa-regular fa-hospital"></i></div>
                                                </div>
                                                <div class="product-tab-content">
                                                    <h6>Basic Info</h6>
                                                </div>
                                            </a>
                                        </li>
                                        @if($empanelmentStepStatus && $empanelmentStepStatus->speciality_status == 1)
                                            <li class="nav-item">
                                                <a class="nav-link" id="specialities-tab" data-bs-toggle="pill" href="#specialities" role="tab" aria-controls="specialities" aria-selected="false">
                                                    <div class="nav-rounded">
                                                        <div class="product-icons"><i class="fa-solid fa-medkit"></i></div>
                                                    </div>
                                                    <div class="product-tab-content">
                                                        <h6>Specialities</h6>
                                                    </div>
                                                </a>
                                            </li>
                                        @endif
                                        @if($empanelmentStepStatus && $empanelmentStepStatus->service_status == 1)
                                            <li class="nav-item">
                                                <a class="nav-link" id="services-tab" data-bs-toggle="pill" href="#services" role="tab" aria-controls="services" aria-selected="false">
                                                    <div class="nav-rounded">
                                                        <div class="product-icons"><i class="fa-solid fa-ambulance"></i></div>
                                                    </div>
                                                    <div class="product-tab-content">
                                                        <h6>Services</h6>
                                                    </div>
                                                </a>
                                            </li>
                                        @endif
                                        @if($empanelmentStepStatus && $empanelmentStepStatus->licenses_status == 1)
                                            <li class="nav-item">
                                                <a class="nav-link" id="licenses-tab" data-bs-toggle="pill" href="#licenses" role="tab" aria-controls="licenses" aria-selected="false">
                                                    <div class="nav-rounded">
                                                        <div class="product-icons"><i class="icofont icofont-list"></i></div>
                                                    </div>
                                                    <div class="product-tab-content">
                                                        <h6>Licenses</h6>
                                                    </div>
                                                </a>
                                            </li>
                                        @endif
                                        <li class="nav-item">
                                            <a class="nav-link" id="documents-tab" data-bs-toggle="pill" href="#documents" role="tab" aria-controls="documents" aria-selected="false">
                                                <div class="nav-rounded">
                                                    <div class="product-icons"><i class="icofont icofont-file-pdf"></i></div>
                                                </div>
                                                <div class="product-tab-content">
                                                    <h6>Documents</h6>
                                                </div>
                                            </a>
                                        </li>
                                        @if($hospital->hospital_type == 'Multi-Branch' && optional($hospital->user)->parent_id == 0)
                                            <li class="nav-item">
                                                <a class="nav-link" id="branches-tab" data-bs-toggle="pill" href="#branches" role="tab" aria-controls="branches" aria-selected="false">
                                                    <div class="nav-rounded">
                                                        <div class="product-icons"><i class="fa-solid fa-timeline"></i></div>
                                                    </div>
                                                    <div class="product-tab-content">
                                                        <h6>Branches</h6>
                                                    </div>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-xxl-9 user-xl-75 col-xl-8 box-col-8e">
                            <div class="row">
                                <div class="col-12">
                                    <div class="tab-content" id="hospital-data-tab-content">
                                        <div class="tab-pane fade show active" id="hospital-info" role="tabpanel" aria-labelledby="hospital-info-tab">
                                            <div class="card">
                                                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                                                    <h5 class="mb-0">General Information</h5>
                                                    <span class="badge text-white badge-{{ $hospital->status == 'Approved' ? 'success' : ($hospital->status == 'Submitted' ? 'info' : ($hospital->status == 'Rejected' ? 'danger' : 'warning')) }}">{{ $hospital->status }}</span>
                                                </div>
                                                <div class="card-body dark-timeline">
                                                    <form id="hospitalBasicInfoForm" enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="row g-3">
                                                            <div class="col-lg-4 col-md-5">
                                                                <label for="image" class="form-label">Hospital Logo</label>
                                                                <div class="border rounded p-3 text-center h-100 bg-light-subtle">
                                                                    <img id="hospitalLogoPreview" src="{{ $hospitalLogoUrl }}" alt="Hospital Logo" class="img-fluid rounded border bg-white p-2" style="max-height: 180px; width: auto; object-fit: contain;">
                                                                    @can('edit-hospital-data')
                                                                        <input type="file" class="form-control mt-3" id="image" name="image" accept=".jpg,.jpeg,.png,.webp">
                                                                        <small class="text-muted d-block mt-2">Allowed: JPG, JPEG, PNG, WEBP. Max 2MB.</small>
                                                                        <div class="text-danger small mt-1 field-error" id="error_image"></div>
                                                                    @endcan
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-8 col-md-7">
                                                                <div class="row g-3">
                                                                    <div class="col-lg-6 col-md-6">
                                                                        <label for="name" class="form-label">Hospital Name <span class="text-danger">*</span></label>
                                                                        <input type="text" class="form-control" id="name" name="name" value="{{ $hospital->name }}" @cannot('edit-hospital-data') readonly @endcannot>
                                                                        <div class="text-danger small mt-1 field-error" id="error_name"></div>
                                                                    </div>
                                                                    <div class="col-lg-6 col-md-6">
                                                                        <label for="code" class="form-label">Hospital Code <span class="text-danger">*</span></label>
                                                                        <input type="text" class="form-control" id="code" name="code" value="{{ $hospital->code }}" @cannot('edit-hospital-data') readonly @endcannot>
                                                                        <div class="text-danger small mt-1 field-error" id="error_code"></div>
                                                                    </div>
                                                                    <div class="col-lg-6 col-md-6">
                                                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                                                        <input type="email" class="form-control" id="email" name="email" value="{{ $hospital->email }}" @cannot('edit-hospital-data') readonly @endcannot>
                                                                        <div class="text-danger small mt-1 field-error" id="error_email"></div>
                                                                    </div>
                                                                    <div class="col-lg-6 col-md-6">
                                                                        <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                                                                        <input type="text" class="form-control" id="phone" name="phone" value="{{ $hospital->phone }}" @cannot('edit-hospital-data') readonly @endcannot>
                                                                        <div class="text-danger small mt-1 field-error" id="error_phone"></div>
                                                                    </div>
                                                                    <div class="col-lg-6 col-md-6">
                                                                        <label for="hospital_type" class="form-label">Hospital Type</label>
                                                                        <input type="text" class="form-control" id="hospital_type" value="{{ $hospital->hospital_type ?: 'N/A' }}" readonly>
                                                                    </div>
                                                                    <div class="col-lg-6 col-md-6">
                                                                        <label for="type_name" class="form-label">Hospital Category</label>
                                                                        <input type="text" class="form-control" id="type_name" value="{{ optional($hospital->type)->name ?: 'N/A' }}" readonly>
                                                                    </div>
                                                                    <div class="col-lg-4 col-md-6">
                                                                        <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                                                        <input type="text" class="form-control" id="city" name="city" value="{{ $hospital->city }}" @cannot('edit-hospital-data') readonly @endcannot>
                                                                        <div class="text-danger small mt-1 field-error" id="error_city"></div>
                                                                    </div>
                                                                    <div class="col-lg-4 col-md-6">
                                                                        <label for="pincode" class="form-label">Pincode <span class="text-danger">*</span></label>
                                                                        <input type="text" class="form-control" id="pincode" name="pincode" value="{{ $hospital->pincode }}" @cannot('edit-hospital-data') readonly @endcannot>
                                                                        <div class="text-danger small mt-1 field-error" id="error_pincode"></div>
                                                                    </div>
                                                                    <div class="col-lg-4 col-md-12">
                                                                        <label for="landmark" class="form-label">Landmark</label>
                                                                        <input type="text" class="form-control" id="landmark" name="landmark" value="{{ $hospital->landmark }}" @cannot('edit-hospital-data') readonly @endcannot>
                                                                        <div class="text-danger small mt-1 field-error" id="error_landmark"></div>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                                                        <textarea class="form-control" id="address" name="address" rows="3" @cannot('edit-hospital-data') readonly @endcannot>{{ $hospital->address }}</textarea>
                                                                        <div class="text-danger small mt-1 field-error" id="error_address"></div>
                                                                    </div>
                                                                    <div class="col-lg-6 col-md-6">
                                                                        <label class="form-label">Status Updated</label>
                                                                        <input type="text" class="form-control" value="{{ $hospital->status_update_date ? \Carbon\Carbon::parse($hospital->status_update_date)->format('d/m/Y h:i A') : 'N/A' }}" readonly>
                                                                    </div>
                                                                    <div class="col-12 mt-2">
                                                                        <div class="border rounded p-3 bg-light-subtle">
                                                                            <h6 class="mb-3">OPD Sticker Print Size (Hospital-wise)</h6>
                                                                            <div class="row g-3">
                                                                                <div class="col-lg-4 col-md-6">
                                                                                    <label for="sticker_width_mm" class="form-label">Sticker Width (mm) <span class="text-danger">*</span></label>
                                                                                    <input type="number" step="0.1" min="20" max="150" class="form-control" id="sticker_width_mm" name="sticker_width_mm" value="{{ $stickerWidthMm ?? '90' }}" @cannot('edit-hospital-data') readonly @endcannot>
                                                                                    <div class="text-danger small mt-1 field-error" id="error_sticker_width_mm"></div>
                                                                                </div>
                                                                                <div class="col-lg-4 col-md-6">
                                                                                    <label for="sticker_height_mm" class="form-label">Sticker Height (mm) <span class="text-danger">*</span></label>
                                                                                    <input type="number" step="0.1" min="15" max="120" class="form-control" id="sticker_height_mm" name="sticker_height_mm" value="{{ $stickerHeightMm ?? '45' }}" @cannot('edit-hospital-data') readonly @endcannot>
                                                                                    <div class="text-danger small mt-1 field-error" id="error_sticker_height_mm"></div>
                                                                                </div>
                                                                                <div class="col-lg-4 col-md-12 d-flex align-items-end">
                                                                                    <small class="text-muted">Example: 90 x 45 mm (common OPD sticker)</small>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 mt-2">
                                                                        <div class="border rounded p-3 bg-light-subtle">
                                                                            <h6 class="mb-3">MRN Number Format</h6>
                                                                            <div class="row g-3">
                                                                                <div class="col-lg-8 col-md-12">
                                                                                    <label for="mrn_format" class="form-label">Format <span class="text-danger">*</span></label>
                                                                                    <input type="text" class="form-control" id="mrn_format" name="mrn_format" value="{{ $mrnFormat ?? 'MRN-{sequence:05}' }}" @cannot('edit-hospital-data') readonly @endcannot>
                                                                                    <div class="text-danger small mt-1 field-error" id="error_mrn_format"></div>
                                                                                    <small class="text-muted d-block mt-2">Default: MRN-{sequence:05}. Available placeholders: {sequence}, {sequence:05}, {Y}, {y}, {m}, {d}</small>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    @if($hospital->reject_reason)
                                                                        <div class="col-12">
                                                                            <label class="form-label">Reject Reason</label>
                                                                            <textarea class="form-control" rows="2" readonly>{{ $hospital->reject_reason }}</textarea>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            @can('edit-hospital-data')
                                                                <div class="col-12 d-flex justify-content-end">
                                                                    <button type="submit" class="btn btn-primary" id="saveHospitalBasicInfoBtn">
                                                                        <i class="fa-solid fa-floppy-disk me-1"></i> Update Basic Info
                                                                    </button>
                                                                </div>
                                                            @endcan
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>

                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>Hospital Admin Information</h5>
                                                </div>
                                                <div class="card-body dark-timeline">
                                                    <div class="row g-3">
                                                        <div class="col-lg-3 col-md-4 col-sm-6">
                                                            <div class="ttl-info text-start">
                                                                <h6>Name :</h6><span>{{ optional($hospital->hospital_admin)->name ?: 'N/A' }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 col-md-4 col-sm-6">
                                                            <div class="ttl-info text-start">
                                                                <h6>Email :</h6><span>{{ optional($hospital->hospital_admin)->email ?: 'N/A' }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 col-md-4 col-sm-6">
                                                            <div class="ttl-info text-start">
                                                                <h6>Phone :</h6><span>{{ optional($hospital->hospital_admin)->mobile_no ?: 'N/A' }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 col-md-4 col-sm-6">
                                                            <div class="ttl-info text-start">
                                                                <h6>Gender :</h6><span>{{ optional($hospital->hospital_admin)->gender ?: 'N/A' }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 col-md-4 col-sm-6">
                                                            <div class="ttl-info text-start">
                                                                <h6>State :</h6><span>{{ optional($hospital->hospital_admin)->state ?: 'N/A' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="specialities" role="tabpanel" aria-labelledby="specialities-tab">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>Available Specialities</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                                                        <table class="display table w-100">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Speciality</th>
                                                                    <th>Code</th>
                                                                    <th>Remarks</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse($hospital->specialities as $speciality)
                                                                    <tr>
                                                                        <td>{{ optional($speciality->speciality)->name ?: 'N/A' }}</td>
                                                                        <td>{{ optional($speciality->speciality)->code ?: 'N/A' }}</td>
                                                                        <td>{{ $speciality->remark ?: 'N/A' }}</td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="3" class="text-center">No specialities found.</td>
                                                                    </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="services" role="tabpanel" aria-labelledby="services-tab">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>Hospital Services</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                                                        <table class="display table w-100">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Service</th>
                                                                    <th>Values</th>
                                                                    <th>Attachment</th>
                                                                    <th>Remarks</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse($hospital->services as $service)
                                                                    <tr>
                                                                        <td>{{ optional($service->subService)->name ?: 'N/A' }}</td>
                                                                        <td>
                                                                            {{ optional($service->action)->type == 'radio' ? (optional($service->action)->label ?: 'N/A') : (trim((optional($service->action)->label ?: '') . (optional($service->action)->label ? ' : ' : '') . ($service->service_value ?: '')) ?: 'N/A') }}
                                                                            @if(optional($service->action)->is_text_input == 1)
                                                                                <br>
                                                                                {{ (optional($service->action)->sublabel ?: 'Details') . ' : ' . ($service->text_value ?: 'N/A') }}
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @if($service->image)
                                                                                @php $imageUrl = asset('public/storage/' . $service->image); @endphp
                                                                                <ul class="action mb-0">
                                                                                    <li class="delete"><a href="#" data-bs-toggle="tooltip" title="View Image" onclick="loadImage('{{ $imageUrl }}')"><i class="fa-solid fa-eye"></i></a></li>
                                                                                </ul>
                                                                            @else
                                                                                N/A
                                                                            @endif
                                                                        </td>
                                                                        <td>{{ $service->remark ?: 'N/A' }}</td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="4" class="text-center">No services found.</td>
                                                                    </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="licenses" role="tabpanel" aria-labelledby="licenses-tab">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>Hospital Licenses</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                                                        <table class="display table w-100">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>License</th>
                                                                    <th>Date of Issue</th>
                                                                    <th>Date of Expiry</th>
                                                                    <th>Document</th>
                                                                    <th>Remarks</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse($hospital->licenses as $license)
                                                                    <tr>
                                                                        <td>{{ optional($license->licenseType)->name ?: 'N/A' }}</td>
                                                                        <td>{{ $license->issue_date ? \Carbon\Carbon::parse($license->issue_date)->format('d/m/Y') : 'N/A' }}</td>
                                                                        <td>{{ $license->expiry_date ? \Carbon\Carbon::parse($license->expiry_date)->format('d/m/Y') : 'N/A' }}</td>
                                                                        <td>
                                                                            @if($license->document)
                                                                                <ul class="action mb-0">
                                                                                    <li class="delete"><a href="#" data-bs-toggle="tooltip" title="View Doc" onclick="loadPDF('{{ $license->doc_url }}')"><i class="fa-solid fa-eye"></i></a></li>
                                                                                </ul>
                                                                            @else
                                                                                N/A
                                                                            @endif
                                                                        </td>
                                                                        <td>{{ $license->remark ?: 'N/A' }}</td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="5" class="text-center">No licenses found.</td>
                                                                    </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="documents-tab">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>Documents</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                                                        <table class="display table w-100">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Document</th>
                                                                    <th>Upload Date</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse($hospital->documents as $document)
                                                                    <tr>
                                                                        <td>{{ optional($document->doc)->name ?: 'N/A' }}</td>
                                                                        <td>{{ $document->created_at ? \Carbon\Carbon::parse($document->created_at)->format('d/m/Y') : 'N/A' }}</td>
                                                                        <td>
                                                                            @if($document->document)
                                                                                <ul class="action mb-0">
                                                                                    <li class="delete"><a href="#" data-bs-toggle="tooltip" title="View Doc" onclick="loadPDF('{{ $document->doc_url }}')"><i class="fa-solid fa-eye"></i></a></li>
                                                                                </ul>
                                                                            @else
                                                                                N/A
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="3" class="text-center">No documents found.</td>
                                                                    </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="branches" role="tabpanel" aria-labelledby="branches-tab">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>Branches</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                                                        <table class="display table w-100">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Hospital Name</th>
                                                                    <th>Email</th>
                                                                    <th>Phone</th>
                                                                    <th>Address</th>
                                                                    <th>Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse($hospital->branches as $branch)
                                                                    <tr>
                                                                        <td>{{ $branch->name ?: 'N/A' }}</td>
                                                                        <td>{{ $branch->email ?: 'N/A' }}</td>
                                                                        <td>{{ $branch->phone ?: 'N/A' }}</td>
                                                                        <td>{{ collect([$branch->address, $branch->landmark, $branch->city, $branch->pincode])->filter()->implode(', ') ?: 'N/A' }}</td>
                                                                        <td>
                                                                            <span class="badge badge-{{ $branch->status == 'Approved' ? 'success' : ($branch->status == 'Submitted' ? 'info' : ($branch->status == 'Rejected' ? 'danger' : 'warning')) }}">{{ $branch->status }}</span>
                                                                        </td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="5" class="text-center">No branches found.</td>
                                                                    </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfModalLabel">Document Viewer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="pdfViewer" style="height: 600px; overflow: auto;"></div>
                    <img id="imagePreview" src="" alt="Image Preview" class="img-fluid" />
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('public/front/assets/js/pdf.min.js') }}"></script>
    <script>
        var hospitalDataUpdateUrl = '{{ $routes['update'] }}';

        function loadPDF(url) {
            var pdfViewer = document.getElementById('pdfViewer');
            var imagePreview = document.getElementById('imagePreview');
            $(imagePreview).hide();
            $(pdfViewer).show();

            pdfjsLib.getDocument(url).promise.then(function(pdfDoc) {
                pdfDoc.getPage(1).then(function(page) {
                    var viewport = page.getViewport({ scale: 1.5 });
                    var canvas = document.createElement('canvas');
                    var ctx = canvas.getContext('2d');

                    canvas.height = viewport.height;
                    canvas.width = viewport.width;

                    pdfViewer.innerHTML = '';
                    pdfViewer.appendChild(canvas);

                    page.render({
                        canvasContext: ctx,
                        viewport: viewport
                    });
                });

                $('#pdfModal').modal('show');
            });
        }

        function loadImage(url) {
            var pdfViewer = document.getElementById('pdfViewer');
            var imagePreview = document.getElementById('imagePreview');

            imagePreview.src = url;
            $(imagePreview).show();
            $(pdfViewer).hide();
            $('#pdfModal').modal('show');
        }

        $('#image').on('change', function(event) {
            var file = event.target.files[0];

            if (!file) {
                return;
            }

            var reader = new FileReader();
            reader.onload = function(e) {
                $('#hospitalLogoPreview').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        });

        $('#hospitalBasicInfoForm').on('submit', function(e) {
            e.preventDefault();

            $('.field-error').text('');

            var btn = $('#saveHospitalBasicInfoBtn');
            var originalBtnHtml = btn.html();
            var formData = new FormData(this);

            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Updating...');

            $.ajax({
                url: hospitalDataUpdateUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status) {
                        sendmsg('success',response.message);
                        window.location.reload();
                    } else {
                        sendmsg('error',response.message);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        var errors = xhr.responseJSON.errors;
                        var messages = [];

                        errors.forEach(function(errorItem) {
                            $('#error_' + errorItem.code).text(errorItem.message);
                            messages.push(errorItem.message);
                        });

                        if (messages.length) {
                            sendmsg('error', messages.join('<br>'));
                        }

                        return;
                    }

                    var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Something went wrong.';
                    sendmsg('error', message);
                },
                complete: function() {
                    btn.prop('disabled', false).html(originalBtnHtml);
                }
            });
        });
    </script>
@endpush