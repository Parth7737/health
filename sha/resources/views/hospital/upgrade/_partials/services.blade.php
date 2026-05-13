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
               <th style="width: 45%">Action</th>
               <th>Remarks</th>
            </tr>
         </thead>
         <tbody
            class="table-border-bottom-0">
            @foreach($value->subServices()->orderBy('sort_order', 'ASC')->get() as $k => $v)
            @php
               $isRequired = false;
               if($v->is_required && empty($v->required_when)) {
                  $isRequired = true;
               } else if($v->is_required && !empty($v->required_when) && in_array(@$hospital->facility_speciality_type, explode(',',$v->required_when))) {
                  $isRequired = true;
               }  
               $existData = App\CentralLogics\Helpers::getUSingleServices($hospital_id, $value->id, $v->id);
            @endphp
            <tr>
               <td>
                  {{$k+1}}
               </td>
               <td>{{$v->name}} @if($isRequired)<span class="text-danger">*</span>@endif
               </td>
               <td>
                  @if(sizeof($v->actions) > 0)
                     @foreach($v->actions as $kk => $action)
                        @if($action->type == 'radio')
                           <div class="form-check dinline serviceerror">
                              <input 
                              class="form-check-input" 
                              onchange="visibletextbox('{{str_replace(' ', '-', strtolower($action->sublabel))}}', '{{ str_replace(' ', '-', strtolower($v->name)) }}', '{{$action->is_text_input}}', '{{$value->id}}_{{$v->id}}_')" 
                              type="radio" 
                              @if(!empty($existData) && $existData->service_value == $action->value) checked @endif
                              name="{{$value->id}}_{{$v->id}}_{{ str_replace(' ', '-', strtolower($v->name)) }}"
                              id="{{ str_replace(' ', '-', strtolower($v->name)) }}"
                              value="{{$action->value}}" {{$isRequired ? 'required' : ''}}
                              >
                              <label class="form-check-label" for="{{$action->label}}">
                                 {{$action->label}}
                              </label>
                           </div>
                        @endif

                        @if($action->type == 'text')
                        <div class="form-floating form-floating-outline serviceerror">
                           <input type="text"
                              id="{{ str_replace(' ', '-', strtolower($v->name)) }}"
                              name="{{$value->id}}_{{$v->id}}_{{ str_replace(' ', '-', strtolower($v->name)) }}"
                              value="{{!empty($existData) && $existData->service_value != '' ? $existData->service_value : '' }}"
                              @if($v->name == 'Total Bed Strength') readonly @endif
                              class="form-control {{$v->name == 'Total Bed Strength' ? 'totalbeds' : ''}}"
                              placeholder="text" {{$isRequired ? 'required' : ''}} />
                           <label for="{{ str_replace(' ', '-', strtolower($v->name)) }}">{{$action->label}}</label>
                        </div>
                        @endif

                        @if($action->is_text_input)
                        <div class="form-floating form-floating-outline serviceerror {{str_replace(' ', '-', strtolower($v->name))}}" @if(empty($existData) || $existData->text_value == '') style="display:none;" @endif>
                           <input type="{{$action->bed_count == 1 ? 'number' : 'text'}}" @if($action->bed_count == 1) onchange="bedCount(this);" @endif 
                              id="{{ str_replace(' ', '-', strtolower($v->label)) }}_text"
                              name="{{$value->id}}_{{$v->id}}_{{ str_replace(' ', '-', strtolower($v->name)) }}_text"
                                value="{{!empty($existData) && $existData->text_value != '' ? $existData->text_value : '' }}"
                              class="form-control  @if($action->bed_count == 1) countbeds @endif "
                              placeholder="text"/>
                           <label for="{{ str_replace(' ', '-', strtolower($action->sublabel)) }}">{{$action->sublabel}}</label>
                        </div>
                        @endif

                        @if($action->is_image)
                        <div class="file-upload-section serviceerror {{str_replace(' ', '-', strtolower($v->name))}} mb-4 mt-4" @if(empty($existData) || @$existData->service_value != $action->value) style="display:none;" @endif>
                           <div class="file-upload-wrapper">
                              <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#6200ea">
                                 <path d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                              </svg>
                              <p> <strong>Browse</strong> </p>
                           </div>
                           <input type="file" class="file-input d-none"  name="{{$value->id}}_{{$v->id}}_{{ str_replace(' ', '-', strtolower($v->name)) }}_image" id="{{$value->id}}_{{$v->id}}_{{ str_replace(' ', '-', strtolower($v->name)) }}_image" accept="image/*" />
                           <div class="uploaded-file file-upload-display d-none">
                              <span class="file-name">Sample.pdf</span>
                              <i class="fas fa-trash "></i>
                              <button class="remove-file-btn bg-transparent border-0 p-0">
                                 <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="undefined">
                                    <path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                                 </svg>
                              </button>
                           </div>
                           <br>
                           <small class="text-muted">Supported file types: <strong>.png, .jpg, .jpeg</strong> (Max: 2MB)</small>
                        </div>
                        
                           @if($existData && $existData->image)
                              <img src="{{ asset('public/storage/'.@$existData->image) }}" style="height:100px;width:100px;" class="mb-4"><br />
                           @endif
                        @endif
                        
                     @endforeach
                  @endif
               </td>
               <td>
                  <div class="form-floating form-floating-outline">
                     <input type="text"
                        oninput="sanitize(this, 'b');"
                        id="{{$value->id}}_{{$v->id}}_remark"
                        name="{{$value->id}}_{{$v->id}}_remark"
                        class="form-control"
                        placeholder="text"/>
                     <label for="{{$value->id}}_{{$v->id}}_remark">Remark</label>
                  </div>
               </td>
            </tr>
            @endforeach
            
         </tbody>
      </table>
   </div>
   @endif
@endforeach
<input type="hidden" name="total_no_of_beds" id="total_no_of_beds">

@if(\App\CentralLogics\Helpers::isbtnenabled(@$hospital->status))
   <div class="d-flex justify-content-end mt-3">
      <button class="btn btn-primary saveservices" type="button" >SAVE</button>
   </div>
@endif
<form>
<script>
      $(document).ready(function () {
         $('.itemName').text('Services');
         var total = 0;
         $(".countbeds").each(function() {
            var value = parseInt($(this).val()) || 0; // Convert value to number, default to 0 if empty
            total += value;
         });
         $('.totalbeds').val(total)
         $('#total_no_of_beds').val(total)
      });

   function bedCount(input) {
      // var totalbeds = parseInt($('.totalbeds').val()) || 0; 
      // var inputdata = parseInt($(input).val()) || 0;

      // var updatedTotal = totalbeds + inputdata;
      // $('.totalbeds').val(updatedTotal)
      // $('#total_no_of_beds').val(updatedTotal)

      var total = 0;
      $(".countbeds").each(function() {
         var value = parseInt($(this).val()) || 0; // Convert value to number, default to 0 if empty
         total += value;
      });
      $('.totalbeds').val(total)
      $('#total_no_of_beds').val(total);
   }

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
      swal({
         title: "Confirm Submission?",
         text: 'Are you sure you want to resubmit this application? It will be moved to draft and you will need to submit it again.',
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
         if(willDelete) {
            ldrshow();
            $('.error').remove();

            var step = 6;
            // Create a FormData object
            var formData = new FormData($('#servicesForm')[0]);
         
            // Send an AJAX request
            $.ajax({
               url: '{{route("hospital.upgrade-service-details", [$uuid, $hospital_id])}}', // Replace with your server endpoint
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
                     swal({
                        title: "Confirm Submission?",
                        text: 'Do you still want to make changes in any form, or do you want to submit the application?',
                        type: "warning",
                        buttons: {
                           cancel: {
                                 visible: true,
                                 text: "No",
                                 className: "btn btn-danger",
                           },
                           confirm: {
                                 text: "Submit Application",
                                 className: "btn btn-success",
                           },
                        },
                     }).then((willSubmit) => {
                        if (willSubmit) {
                           $("#declModal").modal('show');
                        } else {
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
                              // $('.step6Icon').show();
                              loadStep(6);                  
                           }, 1000);
                        }
                     });
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
                        $(`[name="${field}"]`).closest('.serviceerror').after(`<div class="error text-danger">${errors[field][0]}</div>`);
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
