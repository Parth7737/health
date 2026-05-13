@extends('layouts.admin.app',['main_li'=>'Stratifications','sub_li'=>'Create Stratification'])
@section('title','Create Stratification')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Create Stratification</h4>
            </div>
            <form onSubmit="return false" id="stratificationForm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">Category<span class="text-danger"> *</span></label>
                                <select class="form-control select2" name="stratification_category_id" id="stratification_category_id" >
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">Stratification Name<span class="text-danger"> *</span></label>
                                <input type="text" class="form-control" name="name" id="name">
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">Rule<span class="text-danger"> *</span></label>
                                <select class="form-control select2" name="rule" id="rule">
                                    <?php foreach (range('A', 'Z') as $letter): ?>
                                        <option value="<?= strtolower($letter) ?>"><?= $letter ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">Stratification Code<span class="text-danger"> *</span></label>
                                <input type="text" class="form-control" name="code" id="code">
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">Stratification Code2<span class="text-danger"> *</span></label>
                                <input type="text" class="form-control" name="code2" id="code2" readonly>
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label for="">Procedure Code<span class="text-danger"> *</span></label>
                                <select class="form-control select2" name="procedure_id[]" multiple id="procedure_id" >
                                    <option value="">Select Procedure</option>
                                    @foreach($procedures as $procedure)
                                        <option value="{{ $procedure->id }}">{{ $procedure->procedure_code_2 }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="">Stratification Price</label>
                                <input type="number" class="form-control" name="price" id="price">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-action">
                    <button class="btn btn-success" id="add-stratification-btn">Save</button>
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
    $("#rule,#code").on("change",function(){
        rule = $("#rule").val();
        code = $("#code").val();
        if(code != ''){
            $("#code2").val(code+rule);
        }
    })
    $("#add-stratification-btn").on("click",function(){
        var formData = new FormData($('#stratificationForm')[0]);
        
        $('.error').remove();
        $.ajax({
            url: '{{route("admin.stratification.store")}}',
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
                        window.location.href="{{ route('admin.stratification.index') }}";
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