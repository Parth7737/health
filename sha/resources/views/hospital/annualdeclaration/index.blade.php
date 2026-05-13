@extends('layouts.hospital.app')
@section('title','Dashboard | Annual Declaration')
@section('content')

@include('hospital.base.topheader')
<div class="container-xxl flex-grow-1 container-p-y">
   <div class="bg-white rounded-3 box-shadow p-5">
        @include('hospital.base.topbarmenu')
        
        <div class="row g-6">
            @include('hospital.base.basicdetails')
            <div class="bg-white rounded-3 box-shadow p-5 mt-5">
                <div class="card">
                    <!-- <div class="card-header"><h4>WorkFlow History<h4></div> -->
                    <div class="card-body">
                        <form action="" id="declarationform">
                            <div class="mt-2 mb-2"><strong >Last Submitted Date: </strong>{{@$hospital->annualdeclaration()->where('year', date('Y'))->where('status', 1)->first()->submitted_date ?? '--'}}<br/></div>
                            <label for="is_accept"><input type="checkbox" name="is_accept" id="is_accept">&nbsp;&nbsp;<strong>I hereby declare that all information provided in this empanelment form is true, accurate, and complete to the best of my knowledge. I understand that any false or missing information may lead to rejection of this application or termination of empanelment, and may be subject to legal consequenses as per applicable laws and regulations.</strong></label>

                            <div class="d-flex justify-content-end mt-3">
                                <button type="button" class="btn btn-outline-primary rounded-0 prevsubmit ">SUBMIT</button>
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
            @if(!$hospital->annualdeclaration()->where('year', date('Y'))->first())
                swal({
                    title: "Declaration Required",
                    text: "Your annual declaration is pending! Kindly submit your declaration at your earliest convenience.",
                    type: "error",
                    buttons: {
                        confirm: {
                            text: "Ok",
                            className: "btn btn-danger",
                        },
                    },
                });
            @endif
            $(".prevsubmit").on('click', function() {
                if (!$("#is_accept").is(":checked")) {
                    swal({
                        title: "Declaration Required",
                        text: "You must accept the declaration before submitting.",
                        type: "error",
                        buttons: {
                            confirm: {
                                text: "Ok",
                                className: "btn btn-danger",
                            },
                        },
                    });
                    return; // Stop execution if checkbox is not checked
                }

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

                        var formData = new FormData($('#declarationform')[0]);

                        $.ajax({
                            url: '{{route("hospital.savedeclaration", [base64_encode($hospital->uuid), base64_encode($hospital->id)])}}',
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
                                    swal({
                                        title: "Annual Declaration",
                                        text: response.message,
                                        type: "success",
                                        buttons: {
                                            confirm: {
                                                text: "Ok!",
                                                className: "btn btn-success",
                                            },
                                        },
                                    }).then((willDelete) => {
                                        if (willDelete) {
                                            location.reload();
                                        }
                                    });                                    
                                } else {
                                    errorMessage(response.message);
                                }
                            },
                            error: function (xhr) {
                                ldrhide();
                                $('.error').remove();
                                errorMessage('Something went wrong. Please try again later.');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
