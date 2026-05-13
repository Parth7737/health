@extends('layouts.dec.app')
@section('title','Dashboard | Initiate EDC')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="bg-white rounded-3 box-shadow p-5 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row row-cols-5">
                    <div class="col">
                        <div class="d-flex text-center justify-content-center flex-column border-end border-secondary">
                        @if(@$hospital->image)
                            <div class="position-relative image-overlay">
                                <img src="{{asset('public/storage/'.@$hospital->image)}}" width="80" alt="{{@$hospital->facility_name}}" class="mb-3 rounded-circle">
                            </div>
                        @endif
                        <span class="number-3 mb-2">{{@$hospital->facility_name}}</span>
                        <span class="number-2">{{@$hospital->facilityOwnershipType->name}}</span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="infodata">
                            <label>Facility/Reference Id</label>
                            <p><strong>{{@$hospital->hospital_id}}</strong></p>
                            <label>Facility Contact</label>
                            <p><strong>{{@$hospital->hospitalAddress->mobile_no}}</strong></p>
                            <label>Status</label>
                            <p><strong>{{@$hospital->status}}</strong></p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="infodata">
                        <label>Facility Name</label>
                        <p>{{$hospital->facility_name}}</p>
                        <label>Specialities Selected</label>
                        <p>
                            @php
                                $specialities = $hospital->specialities()->where('available', 1)->get()->pluck('speciality.name')->toArray();
                            @endphp
                            <b>{{ implode(', ', $specialities) }}</b>
                        </p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="infodata">
                        <label>State</label>
                        <p>{{@$hospital->hospitalAddress->states->name}}</p>
                        <label>Submission Date</label>
                        <p><strong>{{date('d/m/Y', strtotime($hospital->created_at))}}</strong></p>
                    
                        </div>
                    </div>
                    <div class="col">
                        <div class="infodata">
                        <label>District</label>
                        <p class="">{{@$hospital->hospitalAddress->districts->name}}</p>
                        <label>Status Updated Date</label>
                        <p class="">{{date('d/m/Y g:i:A', strtotime(@$hospital->status_update_date))}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header"><h4 class="theme-color">Workflow Details</h4></div>
        <div class="card-body">
            <div class="">
                <form id="establishmentForm">
                    <table class="table table-responsive table-bordered">
                        <thead class="table-default">
                            <tr>
                                <th>Sr.No</th>
                                <th>Authority</th>
                                <th>Action Type</th>
                                <th>Date Of Submission</th>
                                <th>Documents/Attachments</th>
                                <th>Remarks</th>
                            </tr>
                        <thead>
                        <tbody>
                            @foreach($action->workflow()->orderBy('id', 'ASC')->get() as $key => $value)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{@$value->authority}}</td>
                                    <td>{{@$value->action}}</td>
                                    <td>{{@$value->submission_date ?? 'N/A'}}</td>
                                    <td>@if(@$value->documents()->orderBy('id', 'DESC')->first()->document)<a href="{{ asset('public/storage/'.@$value->documents()->orderBy('id', 'DESC')->first()->document) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a>@endif</td>
                                    <td>{{@$value->remark ?? "N/A"}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
    
    @if(@$action->is_close_action == 0)
        <div class="card mt-4">
            <div class="card-header"><h4 class="theme-color">Action Details</h4></div>
            <div class="card-body">
                <form id="edcactionform" enctype="multipart/form-data">               
                    <div class="row g-5">
                        <div class="col-sm-3">
                            <label class="mb-3">Action <span class="text-danger">*</span></label>
                            <select name="edc_action" id="edc_action" class="select2 form-select form-select-lg reporterrormesage" data-allow-clear="true" required >
                                <option value="">Select</option>
                                @foreach(App\CentralLogics\Helpers::getNextStatuses($action->main_status, $action->last_action, 'sec') as $key => $value)
                                    <option value="{{$value}}" data-block="{{$value}}">{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Communication -->
                        <!-- <div class="col-sm-3 dateblock d-none blocks">
                            <label class="mb-3">Days <span class="text-danger">*</span></label>
                            <input type="number" name="days" id="days" onchange="changedate(this);" class="form-control reporterrormesage" />
                        </div>
                        <div class="col-sm-3 dateblock d-none blocks">
                            <label class="mb-3">Action Start Date <span class="text-danger">*</span></label>
                            <input type="text" disabled readonly name="action_start_date" id="action_start_date" class="form-control reporterrormesage" />
                        </div>
                        <div class="col-sm-3 dateblock d-none blocks">
                            <label class="mb-3">Action End Date <span class="text-danger">*</span></label>
                            <input type="text" disabled readonly name="action_end_date" id="action_end_date" class="form-control reporterrormesage" />
                        </div> -->

                        <div class="col-sm-12">
                            <label for="remark" class="control-label">Remarks <span class="text-danger">*</span></label>
                            <textarea name="remark" id="remark" class="form-control reporterrormesage"></textarea>
                        </div>
                        <div class="col-sm-12">
                            <label for="is_stop_payment" class="control-label"><input type="checkbox" name="is_stop_payment" id="is_stop_payment" class="reporterrormesage" value="1" @if($action->is_stop_payment == 1) checked @endif /> Is Stop Payment?</label>
                        </div>
                        <div class="col-sm-12">
                            <label for="is_stop_preauth" class="control-label"><input type="checkbox" name="is_stop_preauth" id="is_stop_preauth" class="reporterrormesage" value="1" @if($action->is_stop_preauth == 1) checked @endif /> Is Stop Preauth?</label>
                        </div>

                        <div class="col-sm-3 revoke">
                            <label class="mb-3">Document Type <span class="text-danger">*</span></label>
                            <select name="document_type" id="document_type" class="select2 form-select form-select-lg reporterrormesage" data-allow-clear="true" required >
                                <option value="">Select</option>
                                <option value="Audio">Audio</option>
                                <option value="Document">Document</option>
                                <option value="Video">Video</option>
                            </select>
                        </div>
                        <div class="col-sm-3 revoke">
                            <label class="mb-3">Upload Support Document <span class="text-danger">*</span></label>
                            <div class="file-upload-section reporterrormesage">
                                <div class="file-upload-wrapper">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#6200ea">
                                        <path d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                                    </svg>
                                    <p> <strong>Browse</strong> </p>
                                </div>
                                <input type="file" class="file-input d-none "  name="document" id="document"/>
                                <div class="uploaded-file file-upload-display d-none">
                                    <span class="file-name">Sample.pdf</span>
                                    <i class="fas fa-trash "></i>
                                    <button type="button" class="remove-file-btn bg-transparent border-0 p-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="undefined">
                                            <path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div> 
                        <div class="col-sm-3 revoke">
                            <label class="mb-3">Document Description</label>
                            <input type="text" id="document_description" name="description" class="form-control reporterrormesage" />
                        </div>
                        
                        <div class="col-sm-12">
                            <label for="is_close_action" class="control-label"><input type="checkbox" name="is_close_action" id="is_close_action" value="1" class="reporterrormesage" /> Is Close Action?</label>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <button class="btn btn-primary initiateaction" type="button" >Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection


@push('scripts')
<script>
    $(document).ready(function () {
        $('#edc_action').change(function () {
            $(".blocks").addClass('d-none');
            var selectedOption = $(this).val();

            
            if (selectedOption == 'Close The Matter' || selectedOption == 'Revoke Blacklist' || selectedOption == "Revoke Suspension") {
                $('.revoke').addClass('d-none');
            } else {
                $('.revoke').removeClass('d-none');
            }
        });
    });


    $('.initiateaction').on("click", function() {
        ldrshow();
        $('.error').remove();
        // $("#action_start_date").removeAttr('disabled', true);
        // $("#action_end_date").removeAttr('disabled', true);
        var formData = new FormData($('#edcactionform')[0]);
        $.ajax({
            url: '{{route("sec.updateinitiate-action", [base64_encode($action->id), base64_encode($hospital->uuid)])}}',
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
                    window.location = "{{route('sec.edcindex')}}";
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
                            $(`[name="${field}"]`).closest('.reporterrormesage').after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                    }
                    // $("#action_start_date").attr('disabled', true);
                    // $("#action_end_date").attr('disabled', true);
                } else {
                    // $("#action_start_date").attr('disabled', true);
                    // $("#action_end_date").attr('disabled', true);
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });
    });
</script>
@endpush