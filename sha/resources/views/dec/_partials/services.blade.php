@php
   $isedit = false;
   $readonly = '';
   $disabled = '';
   $iseditdec = false;

   if($verification->status == "Physical Verification Completed") {
      $isedit = false;
      $readonly = 'readonly';
      $disabled = 'disabled';
   }
   if(@$hospital->status == 'Empanelment Recommended by DEC' || @$hospital->status == 'Response Required From Facility' || @$hospital->status == 'Empanelment Not Recommended by DEC' || @$hospital->status == 'Approved Upgradation Request' || @$hospital->status == 'Query On Upgradation Request From Facility' || @$hospital->status == 'Rejected Upgradation Request' || @$hospital->status == 'Empanelled') {
      $iseditdec = true; 
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
               <th>Name</th>
               <th>Hospital Input</th>
               <th>Verifier Action</th>
               <th>DEC Recommanded</th>
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
               <td style="text-wrap: auto;">{{$v->name}} @if($isRequired)<span class="text-danger">*</span>@endif
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

                     @if(@$existData->text_value && @$existData->text_value != "")
                        (<b>{{@$existData->text_value }}</b>)
                     @endif  

                     @if(@$existData && @$existData->image)
                        <img src="{{ asset('public/storage/'.@$existData->image) }}" style="height:100px;width:100px;" class="mb-4"><br />
                     @endif    
               </td>
               <td>
                  @if(sizeof($v->actions) > 0)
                     @foreach($v->actions as $kk => $action)
                        @if($action->type == 'radio')
                           <div class="form-check dinline serviceerror">
                              <input class="form-check-input" disabled onchange="visibletextbox('{{str_replace(' ', '-', strtolower($action->sublabel))}}', '{{ str_replace(' ', '-', strtolower($v->name)) }}', '{{$action->is_text_input}}', '{{$value->id}}_{{$v->id}}_')" type="radio" 
                              @if(!empty($existData) && $existData->service_value == $action->value) checked @endif
                              name="{{$value->id}}_{{$v->id}}_{{ str_replace(' ', '-', strtolower($v->name)) }}" id="{{ str_replace(' ', '-', strtolower($v->name)) }}" value="{{$action->value}}" {{$isRequired ? 'required' : ''}}>
                              <label class="form-check-label" for="{{$action->label}}">
                                 {{$action->label}}
                              </label>
                           </div>
                        @endif

                        @if($action->type == 'text')
                        <div class="form-floating form-floating-outline serviceerror">
                           <input type="text"
                              readonly
                              id="{{ str_replace(' ', '-', strtolower($v->name)) }}"
                              name="{{$value->id}}_{{$v->id}}_{{ str_replace(' ', '-', strtolower($v->name)) }}"
                              value="{{!empty($existData) && $existData->service_value != '' ? $existData->service_value : '' }}"
                              class="form-control"
                              placeholder="text" {{$isRequired ? 'required' : ''}} />
                           <label for="{{ str_replace(' ', '-', strtolower($v->name)) }}">{{$action->label}}</label>
                        </div>
                        @endif
                        
                        @if($action->is_text_input)
                        <div class="form-floating form-floating-outline serviceerror {{str_replace(' ', '-', strtolower($v->name))}}" @if(empty($existData) || $existData->service_value != $action->value) style="display:none;" @endif>
                           <input type="text"
                              readonly
                              id="{{ str_replace(' ', '-', strtolower($v->label)) }}_text"
                              name="{{$value->id}}_{{$v->id}}_{{ str_replace(' ', '-', strtolower($v->name)) }}_text"
                                value="{{!empty($existData) && $existData->text_value != '' ? $existData->text_value : '' }}"
                              class="form-control"
                              placeholder="text"/>
                           <label for="{{ str_replace(' ', '-', strtolower($action->sublabel)) }}">{{$action->sublabel}}</label>
                        </div>
                        @endif

                        @if($action->is_image && !$isedit)
                        <div class="file-upload-section serviceerror {{str_replace(' ', '-', strtolower($v->name))}} mb-4 mt-4" @if(empty($existData) || @$existData->service_value != $action->value) style="display:none;" @endif>
                           <div class="file-upload-wrapper">
                              <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#6200ea">
                                 <path d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                              </svg>
                              <p> <strong>Browse</strong> </p>
                           </div>
                           <input type="file" {} class="file-input d-none"  name="{{$value->id}}_{{$v->id}}_{{ str_replace(' ', '-', strtolower($v->name)) }}_image" id="{{$value->id}}_{{$v->id}}_{{ str_replace(' ', '-', strtolower($v->name)) }}_image" accept="image/*" />
                           <div class="uploaded-file file-upload-display d-none">
                              <span class="file-name">Sample.pdf</span>
                              <i class="fas fa-trash "></i>
                              <button class="remove-file-btn bg-transparent border-0 p-0">
                                 <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="undefined">
                                    <path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                                 </svg>
                              </button>
                           </div>
                        </div>
                        @endif
                        
                     @endforeach
                  @endif
               </td>
               <td>
                  <select class="select2 serviceeerror" id="dec_status_{{$existData->id}}service" name="dec_status_{{$existData->id}}" required>
                        <option value="">Select</option>
                        <option value="Valid" @if(@$existData->dec_status == "Valid") selected @endif >Valid</option>
                        <option value="InValid" @if(@$existData->dec_status == "InValid") selected @endif >InValid</option>
                  </select>
               </td>
               <td>
                  <div class="form-floating form-floating-outline">
                        <input type="text"
                           id="dec_remark_{{$existData->id}}"
                           oninput="sanitize(this, 'b');"
                           name="dec_remark_{{$existData->id}}"
                           value="{{@$existData->dec_remark}}"
                           class="form-control serviceeerror"
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
@if(!$iseditdec)
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
         url: '{{route("dec.saveServicesReview", [base64_encode($hospital->id), base64_encode($hospital->uuid)])}}', // Replace with your server endpoint
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
