@extends('layouts.hospital.app')
@section('title','Dashboard | Hospital Withdraw')
@section('content')

@include('hospital.base.topheader')
<div class="container-xxl flex-grow-1 container-p-y">
   <div class="bg-white rounded-3 box-shadow p-5">
        @include('hospital.base.topbarmenu')
        
        <div class="row g-6">
            @include('hospital.base.basicdetails')
            <div class="bg-white rounded-3 box-shadow p-5 mt-5">
                <div class="card">
                    <div class="card-header"><h4 class="theme-color">Hospital Withdraw<h4></div>
                    <div class="card-body">
                        <form action="" id="declarationform">
                            <label for="remark">Please click on submit to initiate withdrawal process</label>
                            <textarea name="remark" id="remark" class="form-control"></textarea>
                            <div class="d-flex justify-content-end mt-3">
                                <button type="button" class="btn btn-primary rounded-0 prevsubmit ">INITIATE</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
   </div>
</div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $(".prevsubmit").on('click', function() {
                swal({
                    title: "Confirm Submission?",
                    text: 'Are you sure you want to submit?',
                    type: "warning",
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

                        // var formData = new FormData($('#declarationform')[0]);

                        // $.ajax({
                        //     url: '{{route("hospital.savedeclaration", [base64_encode($hospital->uuid), base64_encode($hospital->id)])}}',
                        //     headers: {
                        //         'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        //     },
                        //     type: 'POST',
                        //     data: formData,
                        //     processData: false,
                        //     contentType: false,
                        //     success: function (response) {
                        //         ldrhide();
                        //         if(response.success) {
                        //             swal({
                        //                 title: "Annual Declaration",
                        //                 text: response.message,
                        //                 type: "success",
                        //                 buttons: {
                        //                     confirm: {
                        //                         text: "Ok!",
                        //                         className: "btn btn-success",
                        //                     },
                        //                 },
                        //             }).then((willDelete) => {
                        //                 if (willDelete) {
                        //                     location.reload();
                        //                 }
                        //             });                                    
                        //         } else {
                        //             errorMessage(response.message);
                        //         }
                        //     },
                        //     error: function (xhr) {
                        //         ldrhide();
                        //         $('.error').remove();
                        //         errorMessage('Something went wrong. Please try again later.');
                        //     }
                        // });
                    }
                });
            });
        });
    </script>
@endpush
