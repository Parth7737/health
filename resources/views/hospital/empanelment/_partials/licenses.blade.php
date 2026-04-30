<style>
      .dinline {
         display: inline-block !important;
      }

      .licenseerror {
         text-wrap: auto;
      }
</style>
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
               <th>Date Of Issue</th>
               <th>Date Of Expiry</th>
               <th>Document</th>
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
			   $existData = App\CentralLogics\Helpers::getSingleLicense($hospital->id, $value->id, $v->id);
            @endphp
            <tr>
               <td>
                  {{$k+1}}
               </td>
               <td>{{$v->name}} @if($isRequired)<span class="text-danger">*</span>@endif
               </td>
               <td>
					<div class="input-group input-group-merge lierror">
						<input type="text" value="{{!empty($existData) && $existData->issue_date ? date('Y-m-d', strtotime($existData->issue_date)) : ''}}" class="form-control datepicker " name="{{$value->id}}_{{$v->id}}_dateissue" id="{{$value->id}}_{{$v->id}}_dateissue" placeholder="YYYY-MM-DD" aria-describedby="asdasda">
						<span class="input-group-text cursor-pointer">
							<i class="ri-calendar-2-line text-secondary"></i>
						</span>
					</div>
               </td>
			      <td>
			   		<div class="input-group input-group-merge lierror">
                     <input type="text" class="form-control datepicker {{$value->id}}_{{$v->id}}_dateexpiry" value="{{!empty($existData) && $existData->expiry_date ? date('Y-m-d', strtotime($existData->expiry_date)) : ''}}" id="{{$value->id}}_{{$v->id}}_dateexpiry" placeholder="YYYY-MM-DD" name="{{$value->id}}_{{$v->id}}_dateexpiry" aria-describedby="asdasda">
                     <span class="input-group-text cursor-pointer">
                        <i class="ri-calendar-2-line text-secondary"></i>
                     </span>
                  </div>
			      </td>
               <td>
                  <div class="file-upload-section lierror">
                     <div class="file-upload-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg"
                           height="24px"
                           viewBox="0 -960 960 960"
                           width="24px" fill="#6200ea">
                           <path d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                        </svg>
                        <p>
                           <strong>Browse</strong>
                        </p>
                     </div>
                     <input type="file" class="file-input d-none" {{ (empty($is_admin_edit) && empty($existData)) ? 'required' : '' }} name="document_{{$value->id}}_{{$v->id}}" id="document_{{$value->id}}_{{$v->id}}" />
                     <div class="uploaded-file file-upload-display d-none">
                        <span class="file-name">Sample.pdf</span>
                        <button type="button"
                           class="remove-file-btn bg-transparent border-0 p-0">
                           <svg xmlns="http://www.w3.org/2000/svg"
                              height="24px"
                              viewBox="0 -960 960 960"
                              width="24px"
                              fill="undefined">
                              <path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                           </svg>
                        </button>
                     </div>
                  </div>
                  <small>Upload a only pdf format file</small>
                  @if(@$existData->document)
                  <br>
                     <label class="mt-2"><strong>Preview</strong>&nbsp; <a href="{{ asset('public/storage/'.@$existData->document) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a></label>
                  @endif
               </td>
               
               <td>
                  <div class="form-floating form-floating-outline">
                     <input type="text"
                        id="{{$value->id}}_{{$v->id}}_remark"
                        name="{{$value->id}}_{{$v->id}}_remark"
						value="{{!empty($existData) && $existData->remark ? $existData->remark : ''}}"
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

@if($hospital->status == "Draft" || !@$hospital || $hospital->status == "Rejected" || !empty($is_admin_edit))
   <div class="d-flex justify-content-end mt-3">
      <button class="btn btn-primary savelicense" type="button" >SAVE</button>
   </div>
@endif
</form>
<script>

	$(document).ready(function() {
		$('.datepicker[name$="_dateissue"]').daterangepicker({
			singleDatePicker: true,
         autoUpdateInput: false,
         showDropdowns: true,
			maxDate: moment().subtract(1, 'days'), // Restrict to past dates
			locale: {
				format: 'YYYY-MM-DD'
			},
			opens: 'right'
		});

		// Initialize Date of Expiry with future dates only
		$('.datepicker[name$="_dateexpiry"]').daterangepicker({
			singleDatePicker: true,
         autoUpdateInput: false,
         showDropdowns: true,
			minDate: moment().add(1, 'days'), // Restrict to future dates
			locale: {
				format: 'YYYY-MM-DD'
			},
			opens: 'right'
		});

      @if(!$hospital->licenses()->count() > 0)
         $('.datepicker[name$="_dateissue"]').val('');
         $('.datepicker[name$="_dateexpiry"]').val('');
      @endif

		// // Apply selected date to input for Date of Issue
		$('.datepicker[name$="_dateissue"]').on('apply.daterangepicker', function (ev, picker) {
			$(this).val(picker.startDate.format('YYYY-MM-DD'));
		});

		// Clear Date of Issue if canceled
		$('.datepicker[name$="_dateissue"]').on('cancel.daterangepicker', function (ev, picker) {
			$(this).val('');
		});

		// // Apply selected date to input for Date of Expiry
		$('.datepicker[name$="_dateexpiry"]').on('apply.daterangepicker', function (ev, picker) {
			$(this).val(picker.startDate.format('YYYY-MM-DD'));
		});

		// Clear Date of Expiry if canceled
		$('.datepicker[name$="_dateexpiry"]').on('cancel.daterangepicker', function (ev, picker) {
			$(this).val('');
		});
	});
	$(document).ready(function () {
		$('.itemName').text('Statutory Licenses');
	});

   $('.savelicense').click(function () {
      ldrshow();
      $('.error').remove();
      var step = 5;
      // Create a FormData object
      var formData = new FormData($('#licenseForm')[0]);
     
      // Send an AJAX request
      $.ajax({
         url: '{{ !empty($is_admin_edit) ? route("admin.hospitals.update.licenses", $hospital->id) : route("hospital.empanelmentRegistration.saveLicenses", [$uuid ?? '', $hospital->id]) }}',
         headers: {
               'X-CSRF-TOKEN': '{{ csrf_token() }}'
         },
         type: 'POST',
         data: formData,
         processData: false, // Prevent jQuery from automatically processing the data
         contentType: false, // Prevent jQuery from automatically setting content type
         success: function (response) {
            ldrhide();
            $('#licenseForm input[type="file"]').val('');
               $('.remove-file-btn').click();
               @if(empty($is_admin_edit))
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
                     $('.step5Icon').show();
                     loadStep(6);
                  }, 1000);
               @else
                  if (response.success) (typeof sendmsg === 'function' ? sendmsg('success', response.message) : alert(response.message));
                  setTimeout(() => {
                     loadAdminStep(6);
                  }, 1000);
               @endif
         },
         error: function (xhr) {
            ldrhide();
            $('.error').remove();
            
            if (xhr.status === 422) { 
               let errors = xhr.responseJSON.errors;
               for (let field in errors) {
                  $(`[name="${field}"]`).closest('.lierror').after(`<div class="error text-danger m-0 licenseerror">${errors[field][0]}</div>`);
               }
            } else {
               errorMessage("Something went wrong!!");
            }
         }
      });
   });
</script>
