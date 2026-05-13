@extends('layouts.aco.app')
@section('title','Adjustment Upload')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y mb-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="javascript:void(0);">Home</a>
            </li>
            <li class="breadcrumb-item active">Adjustment Upload</li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('aco.dashboard') }}" class="btn btn-outline-primary">Back</a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title theme-color">Upload Adjustment</h6>
                </div>
                <div class="card-body">
                    <form onSubmit="return false" id="adjustmentForm">
                        <div class="row">
                            <div class="col-md-6 col-lg-3">
                                <label for="formFile" class="form-label">Upload Exel</label>
                                <div class="file-upload-section">
                                    <div class="file-upload-wrapper">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            height="24px"
                                            viewBox="0 -960 960 960"
                                            width="24px" fill="#6200ea">
                                            <path
                                                d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                                        </svg>
                                        <p>
                                            <strong>Browse</strong></p>
                                    </div>
                                    <input type="file" name="upload_excel"
                                        class="file-input d-none" />
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
                            <div class="col-md-3 mb-6 mt-7">
                                <a href="{{ asset('public/format/adjustment-format.xlsx') }}" download class="btn btn-outline-primary"><i class="ri-file-download-line"></i> Download Format</a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-start">
                                    <button id="adjustment-btn"
                                        class="btn btn-primary">Upload</button>
                                </div>
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
    $("#adjustment-btn").on("click",function(){
        swal({
            title: "Are you sure?",
            text: "upload this excel.",
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
                var formData = new FormData($('#adjustmentForm')[0]);
                
                $(".loader-overlay").show();
                $('.error').remove();
                $.ajax({
                    url: '{{route("aco.upload-excel")}}',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        $(".loader-overlay").hide();
                        if(response.success){
                            successMessage(response.message);
                            setTimeout(() => {
                                location.reload();
                            }, 5000);
                        }else{
                            errorMessage(response.message);
                        }
                    },
                    error: function (xhr) {
                        $(".loader-overlay").hide();
                        $('.error').remove();
                        
                        if (xhr.status === 422) { 
                            let errors = xhr.responseJSON.errors;
                            for (let field in errors) {
                                if($(`select[name="${field}"]`).length > 0){
                                    $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                                } else{
                                    $(`.${field}`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                                    $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                                }
                            }
                        } else {
                            errorMessage('Something went wrong. Please try again later.');
                        }
                    }
                });
            }
        });
    });
</script>
@endpush