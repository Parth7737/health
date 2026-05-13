@extends('layouts.dec.app')
@section('title','Dashboard | DEC Approver')
@section('content')
<aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu bg-menu-theme flex-grow-0">
   <div class="w-100 h-100">
         <div class="row g-0">
            <div class="col-md-5">
               <div class="d-flex align-items-center bg-theme-color arrow">
                     <ul class="menu-list mb-0 py-2  d-flex">
                        <li class="menu-item">
                           <a href="{{route('dec.dashboard')}}" class="menu-link bottom-menu-icons">
                                 <i class="ri-home-4-line"></i>
                           </a>
                        </li>
                        <li class="menu-item">
                           <a href="javascript:void(0)" onclick="location.reload();" class="menu-link bottom-menu-icons">
                                 <i class="ri-restart-line"></i>
                           </a>
                        </li>
                     </ul>
               </div>
            </div>
            <div class="col-md-7">
                {{$hospital->facility_name}}
            </div>
         </div>
   </div>
</aside>
<div class="container-xxl flex-grow-1 container-p-y">
   <div class="bg-white rounded-3 box-shadow p-5">
    <div class="row">
        <div class="card mb-6 border border-primary">
            <div class="card-header">New verification</div>
            <div class="card-body">   
                <form id="verificationForm">               
                    <div class="row g-5">                    
                        <div class="col-sm-3">
                            <label class="mb-3">Verification Authority <span class="text-danger">*</span></label>
                            <select name="verification_authority" id="verification_authority" class="select2 form-select form-select-lg aerrormesage" data-allow-clear="true" required >
                                <option value="">Select</option>
                                <option value="District Empanelment Committee">District Empanelment Committee</option>
                                <!-- <option value="Empanelment TPA">Empanelment TPA</option> -->
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label class="mb-3">Physical Verifier <span class="text-danger">*</span></label>
                            <select name="physical_verifier" id="physical_verifier" class="select2 form-select form-select-lg aerrormesage" data-allow-clear="true" required >
                                <option value="">Select</option>
                                @foreach($users as $key => $value)
                                    <option value="{{$value->id}}">{{$value->mobile_no}} - {{$value->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label class="mb-3">Verification Type <span class="text-danger">*</span></label>
                            <input type="text" id="verification_type" readonly value="{{$hospital->is_upgrade_application == 1 ? 'Enhancement' : 'Empanelment'}}" name="verification_type" oninput="sanitize(this, 't');" class="form-control aerrormesage" placeholder="Verification Type" required />
                        </div>

                        <div class="col-sm-3">
                            <label class="mb-3">Date Of Assignment <span class="text-danger">*</span></label>
                            <input type="text" class="form-control datepicker aerrormesage" id="date_of_assignment" placeholder="YYYY-MM-DD" value="{{date('Y-m-d')}}" disabled name="date_of_assignment" >
                        </div>

                        <div class="col-sm-3">
                            <label class="mb-3">Due Date Of Physical Verification <span class="text-danger">*</span></label>
                            <input type="text" class="form-control datepicker aerrormesage" id="due_date_of_physical_verification" disabled value="{{ date('Y-m-d', strtotime('+9 days')) }}" placeholder="YYYY-MM-DD" name="due_date_of_physical_verification" >
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <button class="btn btn-primary saveVerification" type="button" >INITIATE VERIFICATION</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
   </div>
</div>

@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
		    $('.datepicker').daterangepicker({
                singleDatePicker: true,
                autoApply: false,
                autoUpdateInput: false,
                maxDate: moment(), // Restrict to past dates
                locale: {
                    format: 'YYYY-MM-DD'
                },
                opens: 'right'
            });

            $('.datepicker').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD'));
            });
        });
       
        $('.saveVerification').click(function () {
            ldrshow();
            $('.error').remove();
            // Create a FormData object
            $('#date_of_assignment, #due_date_of_physical_verification').prop('disabled', false);

            var formData = new FormData($('#verificationForm')[0]);
            
            $('#date_of_assignment, #due_date_of_physical_verification').prop('disabled', true);
            // Send an AJAX request
            $.ajax({
                url: '{{route("dec.initiate.verification", [base64_encode($hospital->id), base64_encode($hospital->uuid)])}}',
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
                        $('#verificationForm')[0].reset();
                        setTimeout(() => {
                            window.location.href = '{{route("dec.gethospital", [base64_encode($hospital->id), base64_encode($hospital->uuid)])}}';
                        }, 1000);
                    } else {
                        errorMessage("Something wen't wrong!!");
                    }
                },
                error: function (xhr) {
                    ldrhide();
                    $('.error').remove();
                    
                    if (xhr.status === 422) { 
                    let errors = xhr.responseJSON.errors;
                    for (let field in errors) {
                        $(`[name="${field}"]`).closest('.aerrormesage').after(`<div class="error text-danger">${errors[field][0]}</div>`);
                    }
                    } else {
                        errorMessage('Something went wrong. Please try again later.');
                    }
                }
            });
        });
    </script>
@endpush
