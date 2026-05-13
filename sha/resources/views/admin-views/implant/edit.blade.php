@extends('layouts.admin.app',['main_li'=>'Procedures','sub_li'=>'Edit Procedure'])
@section('title','Edit Procedure')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Edit Procedure</h4>
            </div>
            <form onSubmit="return false" id="implantForm">
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">Implant / High End Consumable Name<span class="text-danger"> *</span></label>
                                <input type="text" class="form-control" name="name" id="name" value="{{ $implant->name }}">
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">Implant Code<span class="text-danger"> *</span></label>
                                <input type="text" class="form-control" name="code" id="code" value="{{ $implant->code }}">
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">Maximum Permissible Multiplier</label>
                                <input type="text" class="form-control" name="no_of_multiplier" id="no_of_multiplier" value="{{ $implant->no_of_multiplier }}">
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">Speciality<span class="text-danger"> *</span></label>
                                <select class="form-control select2" name="speciality_id" id="speciality_id" >
                                    <option value="">Select Speciality</option>
                                    @foreach($specialities as $speciality)
                                        <option value="{{ $speciality->id }}" {{ $implant->speciality_id == $speciality->id?'selected':'' }}>{{ $speciality->name." (".$speciality->code.")" }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label for="">Procedure Code<span class="text-danger"> *</span></label>
                                <select class="form-control select2" name="procedure_id[]" multiple id="procedure_id" >
                                    <option value="">Select Procedure</option>
                                    @foreach($procedures as $procedure)
                                        <option value="{{ $procedure->id }}" {{ in_array($procedure->id,explode(",",$implant->procedure_id))?'selected':'' }}>{{ $procedure->procedure_code_2 }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">Implant Price</label>
                                <input type="number" class="form-control" name="price" id="price" value="{{ $implant->price }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-action">
                    <button class="btn btn-success" id="update-implant-btn">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
    $(document).ready(function () {
        $("#basic-datatables").DataTable({});
    });
    $("#update-implant-btn").on("click",function(){
        var formData = new FormData($('#implantForm')[0]);
        
        $('.error').remove();
        $.ajax({
            url: '{{route("admin.implant.update",[$implant->id])}}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if(response.success){
                    successMessage(response.message);
                    setTimeout(() => {
                        window.location.href="{{ route('admin.implant.index') }}";
                    }, 1000);
                }
            },
            error: function (xhr) {
                $('.error').remove();
                
                if (xhr.status === 422) { 
                    let errors = xhr.responseJSON.errors;
                    for (let field in errors) {
                        if(field == 'procedure_id'){
                            $(`[name="procedure_id[]"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                        if($(`select[name="${field}"]`).length > 0){
                            $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }else{
                            $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                    }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });
    })
</script>
@endpush