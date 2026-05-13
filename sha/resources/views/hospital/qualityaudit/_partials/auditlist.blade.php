<style>
    .dinline {
        display: inline-block !important;
    }

    .licenseerror {
        text-wrap: auto;
    }
</style>
<form  id="auditform" enctype="multipart/form-data">
    <input type="hidden" name="categoryid" value="{{@$auditcategory->id}}">
   <div class="alert alert-info mb-0 rounded-0" role="alert">{{@$auditcategory->name}}</div>
    @foreach(@$auditcategory->auditSubCategories as $key => $value)
    <div class="table-responsive mt-5 text-nowrap">
        <table class="table table-bordered">
            <thead class="">
                <tr>
                    <th colspan="2" class="text-primary">{{$value->name}}</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @foreach($value->auditlist as $k => $v)
                    @php
                        $isRequired = false;
                        if($v->is_required) {
                            $isRequired = true;
                        }

                        $existdata = App\CentralLogics\Helpers::existaudit($hospital->id, $v->category_id, $v->sub_category_id, $v->id);
                    @endphp
                    <tr>
                        <td style="width: 80%; text-wrap: auto;">{{$v->name}} @if($isRequired)<span class="text-danger">*</span>@endif</td>
                        <td style="width: 20%;">
                            <select class="select2 auditerror" id="audit_{{$v->category_id}}_{{$v->sub_category_id}}_{{$v->id}}service" name="audit_{{$value->category_id}}_{{$v->sub_category_id}}_{{$v->id}}" @if($isRequired) required @endif>
                                <option value="">Select</option>
                                <option value="Compliance" {{$existdata && $existdata->action == "Compliance" ? 'selected' : ''}}>Compliance</option>
                                <option value="Non-Compliance" {{$existdata && $existdata->action == "Non-Compliance" ? 'selected' : ''}}>Non-Compliance</option>
                            </select>
                        </td>
                    </tr>
                @endforeach                
            </tbody>
        </table>
    </div>
    @endforeach
    <div class="d-flex justify-content-end mt-3">
        <button class="btn btn-primary saveaudit" type="button" >SUBMIT</button>
    </div>
<form>
<script>

		
   $('.saveaudit').click(function () {
      ldrshow();
      $('.error').remove();
      var formData = new FormData($('#auditform')[0]);
      $.ajax({
         url: '{{route("hospital.quality-audit-save", [$uuid, $hospital_id])}}',
         headers: {
               'X-CSRF-TOKEN': '{{ csrf_token() }}'
         },
         type: 'POST',
         data: formData,
         processData: false,
         contentType: false,
         success: function (response) {
            ldrhide();
            successMessage(response.message);
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
                        $(`[name="${field}"]`).closest('.auditerror').after(`<div class="error text-danger licenseerror">${errors[field][0]}</div>`);
                    }
               }
            } else {
               alert('Something went wrong. Please try again later.');
            }
         }
      });
   });
</script>
