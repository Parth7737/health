<style>
      .dinline {
         display: inline-block !important;
      }
</style>
@php
   $isedit = false;
   $readonly = '';
   $disabled = '';

   if($verification->status == "Physical Verification Completed") {
      $isedit = true;
      $readonly = 'readonly';
      $disabled = 'disabled';
   }
@endphp
<form  id="licenseForm" enctype="multipart/form-data">
@foreach($licenses as $key => $value) 
   @if(sizeof($value->licenseType) > 0)
   <div class="alert alert-info mb-0 rounded-0" role="alert">{{$value->name}}</div>
   <div class="table-responsive mt-5 text-nowrap">
      <table class="table table-bordered">
         <thead class="table-dark">
            <tr>
               <th>Sr No.</th>
               <th>Name</th>
               <th>Hospital Input</th>
               <th>Verifier Action</th>
               <th>Remarks</th>
            </tr>
         </thead>
         <tbody
            class="table-border-bottom-0">
            @foreach($value->licenseType as $k => $v)
               @php
                  $isRequired = false;
                  if($v->is_required) {
                     $isRequired = true;
                  }
                  $hosp = App\Models\Hospitals::where('id', @$hospital->main_hospitalid)->first();
                  if(@$hosp && $hosp->is_upgrade_application == 1) {
                     $existData = App\CentralLogics\Helpers::getUSingleLicense($hospital->id, $value->id, $v->id);
                  } else {
                     $existData = App\CentralLogics\Helpers::getSingleLicense($hospital->id, $value->id, $v->id);
                  }
               @endphp

               @if(@$existData)
               <tr>
                  <td>{{$k+1}}</td>
                  <td>{{$v->name}}</td>
                  <td>
                     From. {{date('d/m/Y', strtotime(@$existData->issue_date))}} To. {{date('d/m/Y', strtotime(@$existData->expiry_date))}} <br>

                     @if(@$existData->document)
                        <label class="mt-2"><strong>Preview</strong>&nbsp; <a href="{{ asset('public/storage/'.@$existData->document) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a></label>
                     @endif
                  </td>
                  <td>
                     <select {{$disabled}} class="select2 aerror" id="dec_verify_status_{{$existData->id}}" name="dec_verify_status_{{$existData->id}}" required>
                           <option value="">Select</option>
                           <option value="Valid" @if(@$existData->dec_verify_status == "Valid") selected @endif >Valid</option>
                           <option value="InValid" @if(@$existData->dec_verify_status == "InValid") selected @endif >InValid</option>
                     </select>
                  </td>                  
                  <td>
                     <div class="form-floating form-floating-outline">
                        <input type="text"
                           {{$readonly}}
                           id="dec_verify_remark_{{$existData->id}}"
                           oninput="sanitize(this, 'b');"
                           name="dec_verify_remark_{{$existData->id}}"
                           value="{{@$existData->dec_verify_remark}}"
                           class="form-control aerror"
                           placeholder="text"/>
                     </div>
                  </td>
               </tr>
               @endif
            @endforeach            
         </tbody>
      </table>
   </div>
   @endif
@endforeach

@if(!$isedit)
<div class="d-flex justify-content-end mt-3">
   <button class="btn btn-primary savelicense" type="button" >SAVE</button>
</div>
@endif
<form>
<script>
   $('.savelicense').click(function () {
      ldrshow();
      $('.error').remove();
      var step = 5;
      // Create a FormData object
      var formData = new FormData($('#licenseForm')[0]);
     
      // Send an AJAX request
      $.ajax({
         url: '{{route("decverifier.saveLicensesReview", [base64_encode($hospital->id), base64_encode($hospital->uuid)])}}', // Replace with your server endpoint
         headers: {
               'X-CSRF-TOKEN': '{{ csrf_token() }}'
         },
         type: 'POST',
         data: formData,
         processData: false, // Prevent jQuery from automatically processing the data
         contentType: false, // Prevent jQuery from automatically setting content type
         success: function (response) {
            ldrhide();
            successMessage(response.message);
               // $('#licenseForm input[type="file"]').val('');
               // $('.remove-file-btn').click();

               $('.nav-link').removeClass('active');
               $('.tab-pane').removeClass('show active');
               $(`.step${step}`).addClass('show active');
               $(`.navstep${step}`).addClass('active');
               setTimeout(() => {
                  $(`.step${step}`).on('click', function(event) {
                     if (event.target.closest('.nav-item .active')) {
                           setSlider(event.target.closest('.nav-item'));
                     }
                  });
                  $('.step4Icon').show();
                  loadStep(5);                  
               }, 1000);
         },
         error: function (xhr) {
            ldrhide();
            $('.error').remove();
            
            if (xhr.status === 422) { 
               let errors = xhr.responseJSON.errors;
               for (let field in errors) {
                  $(`[name="${field}"]`).closest('.aerror').after(`<div class="error text-danger">${errors[field][0]}</div>`);
               }
            } else {
               alert('Something went wrong. Please try again later.');
            }
         }
      });
   });
</script>
