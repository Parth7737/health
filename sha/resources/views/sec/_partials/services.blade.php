@php
   $isedit = false;
   $readonly = '';
   $disabled = '';
   $iseditsec = false;

   if($verification->status == "Physical Verification Completed") {
      $isedit = false;
      $readonly = 'readonly';
      $disabled = 'disabled';
   }

   if(@$hospital->status == 'Empanelled' || @$hospital->status == 'Query Raised by SEC' || @$hospital->status == 'Queried' || @$hospital->status == 'Rejected') {
      $iseditsec = true;
   }
@endphp


<style>
      .dinline {
         display: inline-block !important;
      }
</style>
<form  id="servicesForm" enctype="multipart/form-data">
@foreach($services as $key => $value) 
   @if(sizeof($value->subServices) > 0)
   <div class="alert alert-info mb-0 rounded-0" role="alert">{{$value->name}}</div>
   <div class="table-responsive mt-5 text-nowrap">
      <table class="table table-bordered">
         <thead class="table-dark">
            <tr>
               <th>Sr No.</th>
               <th>Hospital Input</th>
               <th>Verifier Action</th>
               <th>DEC Officer</th>
               <th>SEC Officer</th>
               <th>Remarks</th>
            </tr>
         </thead>
         <tbody
            class="table-border-bottom-0">
            @foreach($value->subServices as $k => $v)
            @php
               $isRequired = false;
               if($v->is_required) {
                  $isRequired = true;
               }
               $hosp = App\Models\Hospitals::where('id', @$hospital->main_hospitalid)->first();
               if(@$hosp && $hosp->is_upgrade_application == 1) {
                  $existData = App\CentralLogics\Helpers::getUSingleServices($hospital->id, $value->id, $v->id);
               } else {
                  $existData = App\CentralLogics\Helpers::getSingleServices($hospital->id, $value->id, $v->id);
               }
            @endphp
            @if($existData)
            <tr>
               <td>
                  {{$k+1}}
               </td>
              
               <td style="text-wrap: auto;">
                     <b>{{$v->name}}:</b><br>
                     @if(@$existData->service_value == 1)
                        Yes
                     @elseif(@$existData->service_value == 0)
                        No
                     @else
                        {{@$existData->service_value}}
                     @endif 

                     @if(@$existData->dec_verify_text_value && @$existData->dec_verify_text_value != "")
                        (<b>{{@$existData->dec_verify_text_value }}</b>)
                     @endif  

                     @if(@$existData && @$existData->image)
                        <img src="{{ asset('public/storage/'.@$existData->image) }}" style="height:100px;width:100px;" class="mb-4"><br />
                     @endif    
               </td>
               <td>
                  @if(@$existData->dec_verify_service_value == 1)
                     Yes
                  @elseif(@$existData->dec_verify_service_value == 0)
                     No
                  @else
                     {{@$existData->dec_verify_service_value}}
                  @endif 

                  @if(@$existData->text_value && @$existData->text_value != "")
                     (<b>{{@$existData->text_value }}</b>)
                  @endif  

                  @if(@$existData && @$existData->dec_verify_image)
                     <img src="{{ asset('public/storage/'.@$existData->dec_verify_image) }}" style="height:100px;width:100px;" class="mb-4"><br />
                  @endif 
               </td>
               <td style="text-wrap: auto;">
                  <b>Status:</b>{{@$existData->dec_status}}</br>
                  <b>Remark:</b>{{@$existData->dec_remark}}
               </td>
               <td>
                  <select class="select2 serviceeerror" id="sec_status_{{$existData->id}}service" name="sec_status_{{$existData->id}}" required>
                        <option value="">Select</option>
                        <option value="Valid" @if(@$existData->sec_status == "Valid") selected @endif >Valid</option>
                        <option value="InValid" @if(@$existData->sec_status == "InValid") selected @endif >InValid</option>
                  </select>
               </td>
               <td>
                  <div class="form-floating form-floating-outline">
                        <input type="text"
                           id="sec_remark_{{$existData->id}}"
                           name="sec_remark_{{$existData->id}}"
                           value="{{@$existData->sec_remark}}"
                           class="form-control serviceeerror"
                           oninput="sanitize(this, 'b');"
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
@if(!$iseditsec)
<div class="d-flex justify-content-end mt-3">
   <button class="btn btn-primary saveservices" type="button" >SAVE</button>
</div>
@endif
<form>
<script>

   function visibletextbox(id = '', name, isEnable, ids) {
      if(isEnable) {
         var input = $(`input[name=${ids}${name}]:checked`).val(); // Get the value of the checked radio button
         if(input == 1) {
            $(`.${name}`).show();
         } else {
            $(`.${name}`).hide();
         }
      } else {
         $(`.${name}`).hide();
      }      
   }

   $('.saveservices').click(function () {
      ldrshow();
      $('.error').remove();

      var step = 4;
      // Create a FormData object
      var formData = new FormData($('#servicesForm')[0]);
     
      // Send an AJAX request
      $.ajax({
         url: '{{route("sec.saveServicesReview", [base64_encode($hospital->id), base64_encode($hospital->uuid)])}}', // Replace with your server endpoint
         headers: {
               'X-CSRF-TOKEN': '{{ csrf_token() }}'
         },
         type: 'POST',
         data: formData,
         processData: false, // Prevent jQuery from automatically processing the data
         contentType: false, // Prevent jQuery from automatically setting content type
         success: function (response) {
            ldrhide();
            if(response.success) {
               successMessage(response.message);
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
                  $('.step3Icon').show();
                  loadStep(4);                  
               }, 1000);
            } else {
               errorMessage("Something went wrong!!");
            }              
         },
         error: function (xhr) {
            ldrhide();
            $('.error').remove();
            
            if (xhr.status === 422) { 
               let errors = xhr.responseJSON.errors;
               for (let field in errors) {
                  $(`[name="${field}"]`).closest('.serviceeerror').after(`<div class="error text-danger">${errors[field][0]}</div>`);
               }
            } else {
               errorMessage('Something went wrong. Please try again later.');
            }
         }
      });
   });
</script>
