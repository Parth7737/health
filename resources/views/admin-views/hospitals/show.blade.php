@extends('layouts.admin.app')
@section('title', 'Hospital Details')
@section('content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>Patient Profile</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">
                            <svg class="stroke-icon">
                            <use href="{{ asset('public/front/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.hospitals.index') }}" class="text-white">Hospitals</a></li>
                        <li class="breadcrumb-item active"><a href="{{ route('admin.hospitals.show', $hospital->id) }}" class="text-white">{{$hospital->name}}</a></li>
                    </ol>
                </div>
            </div>
            <div class="col-sm-12 d-flex flex-wrap justify-content-md-end align-items-center gap-2 mt-2">
                @if($hospital->status == 'Submitted')
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveConfirmModal">
                        <i class="fa-solid fa-check me-1"></i> Approve
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="fa-solid fa-times me-1"></i> Reject
                    </button>
                @endif
                <a href="{{ route('admin.hospitals.index') }}" class="btn btn-info">Back</a>
            </div>
        </div>
    </div>
  <!-- Container-fluid starts-->
  <div class="container-fluid">
    <div class="user-profile">
      <div class="row">
        <!-- hospital details menu start-->
        <div class="col-12">
          <div class="row scope-bottom-wrapper user-profile-wrapper">
            <div class="col-xxl-3 user-xl-25 col-xl-4 box-col-4">
              <div class="card">
                <div class="card-body">
                  <ul class="sidebar-left-icons nav nav-pills" id="add-product-pills-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="hospital-info-tab" data-bs-toggle="pill" href="#hospital-info" role="tab" aria-controls="hospital-info" aria-selected="false">
                            <div class="nav-rounded">
                                <div class="product-icons"><i class="fa-regular fa-hospital"></i></div>
                            </div>
                            <div class="product-tab-content">
                                <h6>Basic Info</h6>
                            </div>
                        </a>
                    </li>
                    @php
                        $empanelment_step_status = @$hospital->user->enable_step ?json_decode($hospital->user->enable_step):json_decode(\App\CentralLogics\Helpers::get_settings('empanelment_step_status'));
                    @endphp
                    
                    @if($empanelment_step_status && $empanelment_step_status->speciality_status == 1)
                        <li class="nav-item"> <a class="nav-link" id="specialities-tab" data-bs-toggle="pill" href="#specialities" role="tab" aria-controls="specialities" aria-selected="false">
                            <div class="nav-rounded">
                            <div class="product-icons"><i class="fa-solid fa-medkit"></i></div>
                            </div>
                            <div class="product-tab-content">
                            <h6>Specialities</h6>
                            </div></a>
                        </li>
                    @endif
                    @if($empanelment_step_status && $empanelment_step_status->service_status == 1)
                        <li class="nav-item"> <a class="nav-link" id="services-tab" data-bs-toggle="pill" href="#services" role="tab" aria-controls="services" aria-selected="false">
                            <div class="nav-rounded">
                            <div class="product-icons"><i class="fa-solid fa-ambulance"></i></div>
                            </div>
                            <div class="product-tab-content">
                            <h6>Services</h6>
                            </div></a>
                        </li>
                    @endif
                    @if($empanelment_step_status && $empanelment_step_status->licenses_status == 1)
                        <li class="nav-item"> <a class="nav-link" id="licenses-tab" data-bs-toggle="pill" href="#licenses" role="tab" aria-controls="licenses" aria-selected="false">
                            <div class="nav-rounded">
                            <div class="product-icons"><i class="icofont icofont-list"></i></div>
                            </div>
                            <div class="product-tab-content">
                            <h6>Licenses</h6>
                            </div></a>
                        </li>
                    @endif
                    <li class="nav-item"> <a class="nav-link" id="documents-tab" data-bs-toggle="pill" href="#documents" role="tab" aria-controls="documents" aria-selected="false">
                        <div class="nav-rounded">
                          <div class="product-icons"><i class="icofont icofont-file-pdf"></i></div>
                        </div>
                        <div class="product-tab-content">
                          <h6>Documents</h6>
                        </div></a>
                    </li>
                    @if($hospital->hospital_type == 'Multi-Branch' && $hospital->user->parent_id == 0)
                        <li class="nav-item"> <a class="nav-link " id="branches-tab" data-bs-toggle="pill" href="#branches" role="tab" aria-controls="branches" aria-selected="false">
                            <div class="nav-rounded">
                            <div class="product-icons"><i class="fa-solid fa-timeline"></i></div>
                            </div>
                            <div class="product-tab-content">
                            <h6>Branches</h6>
                            </div></a>
                        </li>
                    @endif
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-xxl-9 user-xl-75 col-xl-8 box-col-8e">
              <div class="row">
                <div class="col-12">
                  <div class="tab-content" id="add-product-pills-tabContent">
                    <div class="tab-pane fade show active" id="hospital-info" role="tabpanel" aria-labelledby="hospital-info-tab">
                        <div class="card">
                            <div class="card-header">
                                <h5>Basic Information</h5>
                            </div>
                            <div class="card-body dark-timeline">
                                
                                <div class="row g-3">
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <div class="ttl-info text-start">
                                            <h6>Name : </h6><span>{{ @$hospital->hospital_admin->name }}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <div class="ttl-info text-start">
                                            <h6>Email : </h6><span>{{ @$hospital->hospital_admin->email }}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <div class="ttl-info text-start">
                                            <h6>Phone : </h6><span>{{ @$hospital->hospital_admin->mobile_no }}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <div class="ttl-info text-start">
                                            <h6>Gender : </h6><span>{{ @$hospital->hospital_admin->gender }}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <div class="ttl-info text-start">
                                            <h6>State : </h6><span>{{ @$hospital->hospital_admin->state }}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <div class="ttl-info text-start">
                                            <h6>Hospital Type : </h6><span>{{ $hospital->hospital_type }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h5>Hospital Information</h5>
                            </div>
                            <div class="card-body dark-timeline">
                                
                                <div class="row g-3">
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <div class="ttl-info text-start">
                                            <h6>Hospital Name : </h6><span>{{ @$hospital->name }}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <div class="ttl-info text-start">
                                            <h6>Hospital Code : </h6><span>{{ @$hospital->code }}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <div class="ttl-info text-start">
                                            <h6>Type : </h6><span>{{ @$hospital->type->name }}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <div class="ttl-info text-start">
                                            <h6>Phone  : </h6><span>{{ @$hospital->phone }}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <div class="ttl-info text-start">
                                            <h6>Email : </h6><span>{{ @$hospital->email }}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <div class="ttl-info text-start">
                                            <h6>Address : </h6><span>{!! $hospital->address !!}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <div class="ttl-info text-start">
                                            <h6>Landmark : </h6><span>{{ $hospital->lankmark }}</span>
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
                            <div class="card-body ">
                                <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                                    <table id="specialities-table" class="display table w-100">
                                        <thead class="table-light">
                                        <tr>
                                            <th>Speciality</th>
                                            <th>Code</th>
                                            <th>Remarks</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($hospital->specialities as $speciality)
                                            <tr>
                                                <td>{{ @$speciality->speciality->name }}</td>
                                                <td>{{ @$speciality->speciality->code }}</td>
                                                <td>{{ $speciality->remark }}</td>
                                            </tr>
                                        @endforeach

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
                            <div class="card-body ">
                                <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                                    <table id="services-table" class="display table w-100">
                                        <thead class="table-light">
                                        <tr>
                                            <th>Service</th>
                                            <th>Values</th>
                                            <th>Attachment</th>
                                            <th>Remarks</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($hospital->services as $service)
                                            <tr>
                                                <td>{{ @$service->subService->name }}</td>
                                                <td>
                                                    {{ @$service->action->type == 'radio'?$service->action->label:@$service->action->label." : ".@$service->service_value }}
                                                    @if(@$service->action->is_text_input == 1)
                                                    </br>
                                                    {{ $service->action->sublabel." : ".$service->text_value }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($service->image)
                                                        <ul class="action mb-0">
                                                            @php $image_url =  asset('public/storage/'.$service->image); @endphp
                                                            <li class="delete"><a href="#" data-bs-toggle="tooltip" title="View Image" onclick="loadImage('{{ $image_url }}')"><i class="fa-solid fa-eye"></i></a></li>
                                                        </ul>
                                                    @endif
                                                </td>
                                                <td>{{ $service->remark }}</td>
                                            </tr>
                                        @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div><div class="tab-pane fade" id="licenses" role="tabpanel" aria-labelledby="licenses-tab">
                        <div class="card">
                            <div class="card-header">
                                <h5>Hospital Licenses</h5>
                            </div>
                            <div class="card-body ">
                                <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                                    <table id="services-table" class="display table w-100">
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
                                        @foreach($hospital->licenses as $license)
                                            <tr>
                                                <td>{{ @$license->licenseType->name }}</td>
                                                <td>{{ @$license->issue_date }}</td>
                                                <td>{{ @$license->expiry_date }}</td>
                                                <td>
                                                    <ul class="action mb-0">
                                                        <li class="delete"><a href="#" data-bs-toggle="tooltip" title="View Doc" onclick="loadPDF('{{ $license->doc_url }}')"><i class="fa-solid fa-eye"></i></a></li>
                                                    </ul>
                                                </td>
                                                <td>{{ $speciality->remarks }}</td>
                                            </tr>
                                        @endforeach

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
                            <div class="card-body ">
                                <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                                    <table id="documents-table" class="display table w-100">
                                        <thead class="table-light">
                                        <tr>
                                            <th>Document</th>
                                            <th>Upload Date</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($hospital->documents as $document)
                                            <tr>
                                                <td>{{ @$document->doc->name }}</td>
                                                <td>{{ \Carbon\Carbon::parse($document->created_at)->format('d/m/Y') }}</td>
                                                <td>
                                                    <ul class="action mb-0">
                                                        <li class="delete"><a href="#" data-bs-toggle="tooltip" title="View Doc" onclick="loadPDF('{{ $document->doc_url }}')"><i class="fa-solid fa-eye"></i></a></li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        @endforeach

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
                            <div class="card-body ">
                                <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                                    <table id="branches-table" class="display table w-100">
                                        <thead class="table-light">
                                        <tr>
                                            <th>Hospital Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Address</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($hospital->branches as $branch)
                                            <tr>
                                                <td>{{ $branch->name }}</td>
                                                <td>{{ $branch->email }}</td>
                                                <td>{{ $branch->phone }}</td>
                                                <td>{{ $branch->address.", ".$branch->landmark.", ".$branch->city."-".$branch->pincode }}</td>
                                                <td>
                                                @if($branch->status == 'Draft')
                                                    <!-- // loadmodal" data-targetid="' . $branch->id . ' data-status="0" style="cursor:pointer;" -->
                                                    <span class="badge badge-warning " >Draft</span>
                                                @elseif($branch->status == 'Submitted')
                                                    <span class="badge badge-info " >Submitted</span>
                                                @elseif($branch->status == 'Approved')
                                                    <span class="badge badge-success">Approved</span>
                                                @elseif($branch->status == 'Rejected')
                                                    <span class="badge badge-danger">Rejected</span>
                                                @else
                                                    <span class="badge badge-danger">{{ $branch->status }}</span>
                                                @endif
                                                </td>
                                                <td>
                                                    <ul class="action mb-0">
                                                        <li class="delete"><a href="{{ route('admin.hospitals.show', $branch->id) }}" target="_blank" data-bs-toggle="tooltip" title="View Hospital"><i class="fa-solid fa-eye"></i></a></li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        @endforeach

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
        <!-- hospital details menu end-->
      </div>
    </div>
  </div>

  
    <!-- The Modal -->
    
    <!-- Approve Confirmation Modal -->
    <div class="modal fade" id="approveConfirmModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white" id="approveModalLabel">Confirm Approve</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to approve this hospital?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmApproveBtn">
                        <i class="fa-solid fa-check me-1"></i> Yes, Approve
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white" id="rejectModalLabel">Reject Hospital</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="rejectForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="reject_reason" class="form-label">Reject Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="reject_reason" name="reject_reason" rows="4" required placeholder="Enter reason for rejection..."></textarea>
                            <div id="reject_reason_error" class="text-danger small mt-1" style="display:none;"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger" id="submitRejectBtn">
                            <i class="fa-solid fa-times me-1"></i> Reject
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal (Bootstrap 5) -->
    <div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="pdfModalLabel">Document Viewer</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div id="pdfViewer" style="height: 600px; overflow:auto;"></div>
            <img id="imagePreview" src="" alt="Image Preview" class="img-fluid" />
        </div>
        </div>
    </div>
    </div>
@endsection
@push('scripts')
    <!-- Add PDF.js CDN -->
    <script src="{{ asset('public/front/assets/js/pdf.min.js') }}"></script>
    <script>
        // Function to load the PDF using PDF.js
        function loadPDF(url) {
            var pdfViewer = document.getElementById('pdfViewer');
            var imagePreview = document.getElementById('imagePreview');
            $(imagePreview).hide();
            $(pdfViewer).show();
            pdfjsLib.getDocument(url).promise.then(function(pdfDoc_) {
                var pdfDoc = pdfDoc_;
                var pageNum = 1;

                // Fetch the first page
                pdfDoc.getPage(pageNum).then(function(page) {
                    var scale = 1.5;
                    var viewport = page.getViewport({ scale: scale });

                    // Prepare the canvas element
                    var canvas = document.createElement('canvas');
                    var ctx = canvas.getContext('2d');
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;

                    // Append canvas to the viewer
                    pdfViewer.innerHTML = ''; // Clear previous content
                    pdfViewer.appendChild(canvas);

                    // Render the page on the canvas
                    var renderContext = {
                        canvasContext: ctx,
                        viewport: viewport
                    };
                    page.render(renderContext);
                });
                $("#pdfModal").modal("show");
            });
        }
        
        function loadImage(url) {
            var pdfViewer = document.getElementById('pdfViewer');
            var imagePreview = document.getElementById('imagePreview');
            imagePreview.src = url;
            $(imagePreview).show();
            $(pdfViewer).hide();
            $("#pdfModal").modal("show");
        }

        // Approve Hospital
        var approveUrl = '{{ route("admin.hospitals.approve", $hospital->id) }}';
        $('#confirmApproveBtn').on('click', function() {
            var btn = $(this);
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Processing...');
            $.ajax({
                url: approveUrl,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status) {
                        $('#approveConfirmModal').modal('hide');
                        location.reload();
                    } else {
                        alert(response.message || 'Something went wrong');
                    }
                },
                error: function(xhr) {
                    var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Something went wrong';
                    alert(msg);
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fa-solid fa-check me-1"></i> Yes, Approve');
                }
            });
        });

        // Reject Hospital
        var rejectUrl = '{{ route("admin.hospitals.reject", $hospital->id) }}';
        $('#rejectForm').on('submit', function(e) {
            e.preventDefault();
            var reason = $('#reject_reason').val().trim();
            if (!reason) {
                $('#reject_reason_error').text('Reject reason is required.').show();
                return;
            }
            $('#reject_reason_error').hide();
            var btn = $('#submitRejectBtn');
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Processing...');
            $.ajax({
                url: rejectUrl,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    reject_reason: reason
                },
                success: function(response) {
                    if (response.status) {
                        $('#rejectModal').modal('hide');
                        $('#reject_reason').val('');
                        location.reload();
                    } else {
                        alert(response.message || 'Something went wrong');
                    }
                },
                error: function(xhr) {
                    var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Something went wrong';
                    if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.reject_reason) {
                        $('#reject_reason_error').text(xhr.responseJSON.errors.reject_reason[0]).show();
                    } else {
                        alert(msg);
                    }
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fa-solid fa-times me-1"></i> Reject');
                }
            });
        });

        $('#rejectModal').on('hidden.bs.modal', function () {
            $('#reject_reason').val('');
            $('#reject_reason_error').hide();
        });
    </script>
@endpush
