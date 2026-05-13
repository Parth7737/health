@extends('layouts.admin.app',['main_li'=>'Procedures','sub_li'=>'Edit Procedure'])
@section('title','Edit Procedure')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Edit Procedure</h4>
            </div>
            <form onSubmit="return false" id="procedureForm">
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">Scheme Type<span class="text-danger"> *</span></label>
                                @php $scheme_types = App\Models\SchemeType::get() @endphp
                                <select class="form-control select2" name="scheme_type_id" id="scheme_type_id" >
                                    <option value="">Select Scheme Type</option>
                                    @foreach($scheme_types as $scheme_type)
                                        <option value="{{ $scheme_type->id }}" {{ $procedure->scheme_type_id == $scheme_type->id?'selected':'' }}>{{ $scheme_type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4 sghs-field  {{ $procedure->scheme_type_id == 1?'':'d-none' }}">
                            <div class="form-group">
                                <label for="">Category<span class="text-danger"> *</span></label>
                                @php $procedure_categories = App\Models\ProcedureCategory::get() @endphp
                                <select class="form-control select2" name="procedure_category_id" id="procedure_category_id" >
                                    <option value="">Select Category</option>
                                    @foreach($procedure_categories as $procedure_category)
                                        <option value="{{ $procedure_category->id }}" {{ $procedure->procedure_category_id == $procedure_category->id?'selected':'' }}>{{ $procedure_category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">Speciality<span class="text-danger"> *</span></label>
                                <select class="form-control select2" name="speciality_id" id="speciality_id" >
                                    <option value="">Select Speciality</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">Package<span class="text-danger"> *</span></label>
                                <select class="form-control select2" name="package_id" id="package_id" >
                                    <option value="">Select Package</option>
                                    @foreach($packages as $package)
                                        <option value="{{ $package->id }}" {{ $procedure->package_id == $package->id?'selected':'' }}>{{ $package->name." (".$package->code.")" }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">Procedure Code 1<span class="text-danger"> *</span></label>
                                <input type="text" class="form-control" name="procedure_code_1" value="{{ $procedure->procedure_code_1 }}" id="procedure_code_1">
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">Procedure Code 2<span class="text-danger"> *</span></label>
                                <input type="text" class="form-control" name="procedure_code_2" value="{{ $procedure->procedure_code_2 }}" id="procedure_code_2">
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">Is Multiple Procedures<span class="text-danger"> *</span></label>
                                <select class="form-control select2" name="is_multiple_procedure" id="is_multiple_procedure" >
                                    <option value="">Select </option>
                                    <option value="Yes" {{ $procedure->is_multiple_procedure == 'Yes'?'selected':'' }}>Yes</option>
                                    <option value="No" {{ $procedure->is_multiple_procedure == 'No'?'selected':'' }}>No</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">ICHI Procedure / ICD Code</label>
                                <input type="text" class="form-control" name="icd_code" id="icd_code" value="{{ $procedure->icd_code }}">
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">Procedure Type<span class="text-danger"> *</span></label>
                                <input type="text" class="form-control" name="procedure_type" id="procedure_type" value="{{ $procedure->procedure_type }}">
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label for="">Procedure <span class="text-danger"> *</span></label>
                                <input type="text" class="form-control" name="procedure_name" id="procedure_name" value="{{ $procedure->procedure_name }}">
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">Procedure Price<span class="text-danger"> *</span></label>
                                <input type="number" class="form-control" name="price" id="price" value="{{ $procedure->price }}">
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4 sghs-field  {{ $procedure->scheme_type_id == 1?'':'d-none' }}">
                            <div class="form-group">
                                <label for="">Procedure Non NABH Price<span class="text-danger"> *</span></label>
                                <input type="number" class="form-control" name="non_nabh_price" id="non_nabh_price" value="{{ $procedure->non_nabh_price }}">
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">Stratification Criteria<span class="text-danger"> *</span></label>
                                <select class="form-control select2" name="stratification_criteria" id="stratification_criteria" >
                                    <option value="">Select</option>
                                    <option value="Yes" {{ $procedure->stratification_criteria == 'Yes'?'selected':'' }}>Yes</option>
                                    <option value="No" {{ $procedure->stratification_criteria == 'No'?'selected':'' }}>No</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4 {{ $procedure->stratification_criteria == 'Yes'?'':'d-none' }} stratification-field">
                            <div class="form-group">
                                <label for="">No. of Stratification Criteria Book<span class="text-danger"> *</span></label>
                                <input type="number" class="form-control" name="no_of_stratification" id="no_of_stratification" value="{{ $procedure->no_of_stratification }}">
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">Implants / High End Consumables<span class="text-danger"> *</span></label>
                                <select class="form-control select2" name="implants_high_end_consumables" id="implants_high_end_consumables" >
                                    <option value="">Select</option>
                                    <option value="Yes" {{ $procedure->implants_high_end_consumables == 'Yes'?'selected':'' }}>Yes</option>
                                    <option value="No" {{ $procedure->implants_high_end_consumables == 'No'?'selected':'' }}>No</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4 {{ $procedure->implants_high_end_consumables == 'Yes'?'':'d-none' }} implant-field">
                            <div class="form-group">
                                <label for="">No. of Implants High End Consumables Book<span class="text-danger"> *</span></label>
                                <input type="number" class="form-control" name="more_than_one_implant" id="more_than_one_implant" value="{{ $procedure->more_than_one_implant }}">
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">Special Conditions<span class="text-danger"> *</span></label>
                                <select class="form-control select2" name="special_conditions" id="special_conditions" >
                                    <option value="">Select</option>
                                    <option value="Yes" {{ $procedure->special_conditions == 'Yes'?'selected':'' }}>Yes</option>
                                    <option value="No" {{ $procedure->special_conditions == 'No'?'selected':'' }}>No</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">Reservation Public Hospitals<span class="text-danger"> *</span></label>
                                <select class="form-control select2" name="reservation_public_hospitals" id="reservation_public_hospitals" >
                                    <option value="">Select</option>
                                    <option value="Yes" {{ $procedure->reservation_public_hospitals == 'Yes'?'selected':'' }}>Yes</option>
                                    <option value="No" {{ $procedure->reservation_public_hospitals == 'No'?'selected':'' }}>No</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">Reservation Tertiary Hospitals<span class="text-danger"> *</span></label>
                                <select class="form-control select2" name="reservation_tertiary_hospitals" id="reservation_tertiary_hospitals" >
                                    <option value="">Select</option>
                                    <option value="Yes" {{ $procedure->reservation_tertiary_hospitals == 'Yes'?'selected':'' }}>Yes</option>
                                    <option value="No" {{ $procedure->reservation_tertiary_hospitals == 'No'?'selected':'' }}>No</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">Level of Care<span class="text-danger"> *</span></label>
                                <select class="form-control select2" name="level_of_care" id="level_of_care" >
                                    <option value="NA">NA</option>
                                    <option value="Secondary" {{ $procedure->level_of_care == 'Secondary'?'selected':'' }}>Secondary</option>
                                    <option value="Tertiary" {{ $procedure->level_of_care == 'Tertiary'?'selected':'' }}>Tertiary</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">LOS<span class="text-danger"> *</span></label>
                                <input type="number" class="form-control" name="los" id="los" value="{{ $procedure->los }}">
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">Auto Approved<span class="text-danger"> *</span></label>
                                <select class="form-control select2" name="auto_approved" id="auto_approved" >
                                    <option value="No" {{ $procedure->auto_approved == 'No'?'selected':'' }}>No</option>
                                    <option value="Yes" {{ $procedure->auto_approved == 'Yes'?'selected':'' }}>Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">Procedure Label<span class="text-danger"> *</span></label>
                                <select class="form-control select2" name="procedure_label" id="procedure_label" >
                                    <option value="Regular Procedure" {{ $procedure->procedure_label == 'Regular Procedure'?'selected':'' }}>Regular Procedure</option>
                                    <option value="Add-on Procedure" {{ $procedure->procedure_label == 'Add-on Procedure'?'selected':'' }}>Add-on Procedure</option>
                                    <option value="Follow-up Procedure" {{ $procedure->procedure_label == 'Follow-up Procedure'?'selected':'' }}>Follow-up Procedure</option>
                                    <option value="Stand Alone Procedure" {{ $procedure->procedure_label == 'Stand Alone Procedure'?'selected':'' }}>Stand Alone Procedure</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label for="">Mandatory Documents - Pre Authorization<span class="text-danger"> *</span></label>
                                <select class="form-control select2" name="mandatory_documents_pre_auth[]" multiple id="mandatory_documents_pre_auth" >
                                    <option value="">Select Investigation</option>
                                    @foreach($investigations as $investigation)
                                        <option value="{{ $investigation->id }}" {{ in_array($investigation->id,explode(",",$procedure->mandatory_documents_pre_auth))?'selected':'' }}>{{ $investigation->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label for="">Mandatory Documents - Claim Processing<span class="text-danger"> *</span></label>
                                <select class="form-control select2" name="mandatory_documents_claim_processing[]" multiple id="mandatory_documents_claim_processing" >
                                    <option value="">Select Investigation</option>
                                    @foreach($investigations as $investigation)
                                        <option value="{{ $investigation->id }}" {{ in_array($investigation->id,explode(",",$procedure->mandatory_documents_claim_processing))?'selected':'' }}>{{ $investigation->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">Special Condition - Pop Up<span class="text-danger"> *</span></label>
                                <select class="form-control select2" name="special_condition_pop_up" id="special_condition_pop_up" >
                                    <option value="No" {{ $procedure->special_condition_pop_up == 'No'?'selected':'' }}>No</option>
                                    <option value="Yes" {{ $procedure->special_condition_pop_up == 'Yes'?'selected':'' }}>Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-8 {{ $procedure->special_condition_pop_up == 'Yes'?'':'d-none' }} popup-field">
                            <div class="form-group">
                                <label for="">Special Condition - Pop Up Message<span class="text-danger"> *</span></label>
                                <textarea class="form-control" name="special_condition_pop_up_message">{{ $procedure->special_condition_pop_up_message }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">Special Condition - Rule<span class="text-danger"> *</span></label>
                                <select class="form-control select2" name="special_conditions_rule" id="special_conditions_rule" >
                                    <option value="No" {{ $procedure->special_conditions_rule == 'No'?'selected':'' }}>No</option>
                                    <option value="Yes" {{ $procedure->special_conditions_rule == 'Yes'?'selected':'' }}>Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-8 {{ $procedure->special_conditions_rule == 'Yes'?'':'d-none' }} rule-field">
                            <div class="form-group">
                                <label for="">Special Condition Rule Message<span class="text-danger"> *</span></label>
                                <textarea class="form-control" name="special_conditions_rule_message">{{ $procedure->special_conditions_rule_message }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">Enhancement applicable Or Not<span class="text-danger"> *</span></label>
                                <select class="form-control select2" name="enhancement_applicable" id="enhancement_applicable" >
                                    <option value="No" {{ $procedure->enhancement_applicable == 'No'?'selected':'' }}>No</option>
                                    <option value="Yes" {{ $procedure->enhancement_applicable == 'Yes'?'selected':'' }}>Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">Medical or Surgical<span class="text-danger"> *</span></label>
                                <select class="form-control select2" name="medical_or_surgical" id="medical_or_surgical" >
                                    <option value="Medical" {{ $procedure->medical_or_surgical=='Medical'?'selected':'' }}>Medical</option>
                                    <option value="Surgical" {{ $procedure->medical_or_surgical=='Surgical'?'selected':'' }}>Surgical</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">Day Care Procedure<span class="text-danger"> *</span></label>
                                <select class="form-control select2" name="day_care_procedure" id="day_care_procedure" >
                                    <option value="No" {{ $procedure->day_care_procedure == 'No'?'selected':'' }}>No</option>
                                    <option value="Yes" {{ $procedure->day_care_procedure == 'Yes'?'selected':'' }}>Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-action">
                    <button class="btn btn-success" id="update-procedure-btn">Update</button>
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
    $('#stratification_criteria').on("change",function(){
        if($(this).val() == 'Yes'){
            $(".stratification-field").removeClass('d-none');
        }else{
            $(".stratification-field").addClass('d-none');
        }
    })
    $('#implants_high_end_consumables').on("change",function(){
        if($(this).val() == 'Yes'){
            $(".implant-field").removeClass('d-none');
        }else{
            $(".implant-field").addClass('d-none');
        }
    })
    $('#special_condition_pop_up').on("change",function(){
        if($(this).val() == 'Yes'){
            $(".popup-field").removeClass('d-none');
        }else{
            $(".popup-field").addClass('d-none');
        }
    })
    $('#special_conditions_rule').on("change",function(){
        if($(this).val() == 'Yes'){
            $(".rule-field").removeClass('d-none');
        }else{
            $(".rule-field").addClass('d-none');
        }
    })
    $("#update-procedure-btn").on("click",function(){
        var formData = new FormData($('#procedureForm')[0]);
        
        $('.error').remove();
        $.ajax({
            url: '{{route("admin.procedure.update",[$procedure->id])}}',
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
                        window.location.href="{{ route('admin.procedure.index') }}";
                    }, 1000);
                }
            },
            error: function (xhr) {
                $('.error').remove();
                
                if (xhr.status === 422) { 
                    let errors = xhr.responseJSON.errors;
                    for (let field in errors) {
                        if(field == 'mandatory_documents_pre_auth'){
                            $(`[name="mandatory_documents_pre_auth[]"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                        if(field == 'mandatory_documents_claim_processing'){
                            $(`[name="mandatory_documents_claim_processing[]"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
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
    $(document).ready(function(){
        scheme_type_id = $("#scheme_type_id").val();
        if(scheme_type_id){
            $("#scheme_type_id").trigger("change");
        }
    })
    $("#scheme_type_id").on("change",function(){
        if($(this).val() == 1){
            $(".sghs-field").removeClass("d-none");
        }else{
            $(".sghs-field").addClass("d-none");
        }
        scheme_type_id = $("#scheme_type_id").val();
        $.ajax({
            url: '{{route("admin.procedure.get-specialities")}}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: {scheme_type_id:scheme_type_id},
            success: function (response) {
                var html = '<option value="">Select Speciality</option>';
                var selected = '{{ $procedure->speciality_id }}';
                $(response.specialities).each(function(index,speciality){
                    if(selected == speciality.id){
                        html += '<option value="'+speciality.id+'" selected>'+speciality.name+'</option>';
                    }else{
                        html += '<option value="'+speciality.id+'">'+speciality.name+'</option>';
                    }
                });
                $("#speciality_id").html(html).change();
            },
            error: function (xhr) {
                $('.error').remove();
                errorMessage('Something went wrong. Please try again later.');
            }
        });
    })
</script>
@endpush