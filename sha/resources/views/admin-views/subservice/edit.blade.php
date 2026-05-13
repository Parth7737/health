@extends('layouts.admin.app',['main_li'=>'Sub Service','sub_li'=>'Edit Sub Service'])
@section('title','Edit Sub Service')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Edit Sub Service</h4>
            </div>
            <form onSubmit="return false" id="implantForm">
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">Select Service<span class="text-danger"> *</span></label>
                                <select name="service_id" required id="service_id" class="form-control">
                                    <option value="">Select</option>
                                    @foreach($service as $key => $value)
                                        <option value="{{$value->id}}" @if($subservice->service_id == $value->id) selected @endif>{{$value->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">Name<span class="text-danger"> *</span></label>
                                <input type="text" value="{{$subservice->name}}" required class="form-control" name="name" id="name">
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="is_required">Is Required <input type="checkbox" class="" name="is_required" id="is_required" @if($subservice->is_required == 1) checked @endif value="1"></label>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4 req_when" @if($subservice->is_required != 1) style="display:none;" @endif>
                            <div class="form-group">
                                <label for="">Required When</label>
                                <select class="form-control select2" name="required_when[]" multiple id="required_when" >
                                    @foreach($types as $type)
                                        <option value="{{ $type->id }}" {{ in_array($type->id,explode(",",$subservice->required_when))?'selected':'' }}>{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <h5>Sub Service Action</h5>
                        <table class="table table-responsive">
                            <thead>
                                <tr>
                                    <td>Type</td>
                                    <td>Label</td>
                                    <td>Value</td>
                                    <td>Is Text Input</td>
                                    <td>Is Image</td>
                                    <td>SubLabel</td>
                                    <td>Is Bed Count?</td>
                                    <td></td>
                                </tr>
                            </thead>
                            <tbody class="body">
                                @foreach($subservice->actions as $key => $value)
                                <tr>
                                    <td>
                                        <select name="type[]" required id="type0" class="form-control">
                                            <option value="">Select</option>
                                            <option value="radio" @if($value->type == 'radio') selected @endif>Radio</option>
                                            <option value="text" @if($value->type == 'text') selected @endif>Text</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" value="{{$value->label}}" required name="label[]" id="label0" class="form-control">
                                    </td>
                                    <td>
                                        <input type="text" value="{{$value->value}}" name="value[]" id="value0" class="form-control">
                                    </td>
                                    <td>
                                        <input type="checkbox" @if($value->is_text_input == 1) checked @endif name="is_text_input[]" id="is_text_input0" class="">
                                    </td>
                                    <td>
                                        <input type="checkbox" @if($value->is_image == 1) checked @endif name="is_image[]" id="is_image0" class="">
                                    </td>
                                    <td>
                                        <input type="text" value="{{$value->sublabel}}" name="sublabel[]" id="sublabel0" class="form-control">
                                    </td>
                                    <td>
                                        <input type="checkbox" @if($value->bed_count == 1) checked @endif name="bed_count[]" id="bed_count0" class="">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="7">
                                        <button type="button" id="addmore" class="btn btn-info"><i class="fa fa-plus"></i>Add More</button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>                        
                    </div>
                </div>

                <div class="card-action">
                    <button class="btn btn-success" id="update-implant-btn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@push('scripts')

<script>
$(document).ready(function() {
    var rowIndex = '{{count($subservice->actions) + 1}}'; // Start from 1 since the first row has index 0

    $('#is_required').change(function() {
        if ($(this).is(':checked')) {
            $('.req_when').show(); // Show the div
        } else {
            $('.req_when').hide(); // Hide the div
        }
    });

    $('#addmore').click(function() {
        var newRow = `
            <tr>
                <td>
                    <select name="type[]" id="type${rowIndex}" required class="form-control">
                        <option value="">Select</option>
                        <option value="radio">Radio</option>
                        <option value="text">Text</option>
                    </select>
                </td>
                <td>
                    <input type="text" name="label[]" required id="label${rowIndex}" class="form-control">
                </td>
                <td>
                    <input type="text" name="value[]" id="value${rowIndex}" class="form-control">
                </td>
                <td>
                    <input type="checkbox" name="is_text_input[]" id="is_text_input${rowIndex}" class="">
                </td>
                <td>
                    <input type="checkbox" name="is_image[]" id="is_image${rowIndex}" class="">
                </td>
                <td>
                    <input type="text" name="sublabel[]" id="sublabel${rowIndex}" class="form-control">
                </td>
                <td>
                    <input type="checkbox" name="bed_count[]" id="bed_count${rowIndex}" >
                </td>
                <td>
                    <button type="button" class="btn btn-danger delete-row"><i class="fa fa-trash"></i></button>
                </td>
            </tr>
        `;
        
        // Append the new row to the table body
        $('.body').append(newRow);

        // Increment the row index
        rowIndex++;
    });

    // Delegate click event to delete-row button to remove the row
    $('body').on('click', '.delete-row', function() {
        $(this).closest('tr').remove();
    });
});
</script>

<script>
    $(document).ready(function () {
        $("#basic-datatables").DataTable({});
    });
    $("#update-implant-btn").on("click",function(){
        var formData = new FormData($('#implantForm')[0]);
        
        $('.error').remove();
        $.ajax({
            url: '{{route("admin.sub-service.update", [$subservice->id])}}',
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
                        window.location.href="{{ route('admin.sub-service.index') }}";
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