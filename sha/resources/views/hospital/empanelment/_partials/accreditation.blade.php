@php @$hospital_accreditation=$hospital->hospitalAccreditation; @endphp
<div class="inside-left-info-box {{ @$hospital_accreditation?'success':'pending' }} mt-4">
	<h4 class="colored-verticle-title">Accreditation
		<span class="status-dot">
			<svg xmlns="http://www.w3.org/2000/svg"
				height="24px" viewBox="0 -960 960 960"
				width="24px" fill="undefined">
				<path d="M400-304 240-464l56-56 104 104 264-264 56 56-320 320Z" />
			</svg>
		</span>
	</h4>
	<form onSubmit="return false" id="accreditationForm">
		<div class="row g-5">
			<div class="col-md-12 col-lg-12">
				<label for="cyanosis" class="mb-2">Does your organization have any accreditation/certification <span class="text-danger">*</span></label>
				<div class="d-flex">
					<div class="form-check">
						<input class="form-check-input"
							type="radio" name="accreditation"
							id="accreditation_yes" value="Yes" {{ @$hospital_accreditation->accreditation && $hospital_accreditation->accreditation=='Yes'?'checked':'' }}>
						<label class="form-check-label"
							for="accreditation_yes">
							Yes
						</label>
					</div>
					<div class="form-check ms-4">
						<input class="form-check-input"
							type="radio" name="accreditation"
							id="accreditation_no" value="No" {{ @$hospital_accreditation->accreditation && $hospital_accreditation->accreditation=='No'?'checked':'' }}>
						<label class="form-check-label"
							for="accreditation_no">
							No
						</label>
					</div>
				</div>
			</div>
		</div>
		<div class="row mt-3 g-5 accreditation-field {{ @$hospital_accreditation->accreditation && $hospital_accreditation->accreditation=='Yes'?'':'d-none' }}">
			<div class="col-md-6 col-lg-3">
				<div
					class="form-floating form-floating-outline">
					@php $accreditations = App\CentralLogics\Helpers::getCommanData('Accreditation'); @endphp
					<select class="form-select select2"
						id="accreditation_id"
						name="accreditation_id">
						<option value=""></option>
						@foreach($accreditations as $accreditation)
							<option value="{{ $accreditation->id }}" {{ @$hospital_accreditation->accreditation_id == $accreditation->id?'selected':'' }}>{{ $accreditation->name }}</option>
						@endforeach
					</select>
					<label for="accreditation_id">Name of Accreditation Board <span class="text-danger">*</label>
				</div>
			</div>
			<div class="col-md-6 col-lg-3">
				<div class="form-floating form-floating-outline">
					<input type="text" id="certificate_no" oninput="sanitize(this, 'b');" name="certificate_no" class="form-control" placeholder="Certificate Number" value="{{ @$hospital_accreditation->certificate_no }}" />
					<label for="certificate_no">Certificate Number <span class="text-danger">*</span></label>
				</div>
			</div>
			<div class="col-md-6 col-lg-3">
				<div class="form-floating form-floating-outline">
					<input type="text" id="bs-rangepicker-singlee" name="valid_from" class="form-control" placeholder="Valid From" value="{{ @$hospital_accreditation->valid_from }}" />
					<label for="bs-rangepicker-singlee">Valid From <span class="text-danger">*</span></label>
				</div>
			</div>
			<div class="col-md-6 col-lg-3">
				<div class="form-floating form-floating-outline">
					<input type="text" id="bs-rangepicker-singlee-2" name="valid_till" class="form-control" placeholder="Valid Till" value="{{ @$hospital_accreditation->valid_till }}" />
					<label for="bs-rangepicker-singlee-2">Valid Till <span class="text-danger">*</span></label>
				</div>
			</div>
			<div class="col-md-6 col-lg-3">
				<label for="formFile" class="form-label">Certificate <span class="text-danger">*</span></label>
				<div class="file-upload-section">
					<div class="file-upload-wrapper">
						<svg xmlns="http://www.w3.org/2000/svg"
							height="24px"
							viewBox="0 -960 960 960"
							width="24px" fill="#6200ea">
							<path
								d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
						</svg>
						<p><strong>Browse</strong></p>
					</div>
					<input type="file"
						class="file-input d-none" name="certificate" accept=".pdf"/>
					<div
						class="uploaded-file file-upload-display d-none">
						<span
							class="file-name">Sample.pdf</span>
						<i class="fas fa-trash "></i>
						<button
							class="remove-file-btn bg-transparent border-0 p-0">
							<svg xmlns="http://www.w3.org/2000/svg"
								height="24px"
								viewBox="0 -960 960 960"
								width="24px"
								fill="undefined">
								<path
									d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
							</svg>
						</button>
					</div>
				</div>
				<span class="small text-muted">Supported file types: PDF (Max: 10MB)</span>
				<div class="preivew-certificate">
					@if(@$hospital_accreditation->certificate)
						<label class="mt-2"><strong>Preview</strong>&nbsp; <a href="{{ asset('public/storage/'.@$hospital_accreditation->certificate) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a></label>
					@endif
				</div>
			</div>
			<div class="col-md-6 col-lg-3 mt-5">
				<div
					class="form-floating form-floating-outline">
					@php $specialities = $hospital->specialities()->where('available', 1)->get();
					$selected_ids = @$hospital_accreditation->speciality_ids?json_decode(@$hospital_accreditation->speciality_ids,true):array();
					@endphp
					<select class="form-select select2" multiple
						id="speciality_ids"
						name="speciality_ids[]">
						<option value="" disabled></option>
						@foreach($specialities as $speciality)
							<option value="{{ $speciality->id }}" {{ in_array($speciality->id,$selected_ids)?'selected':'' }}>{{ @$speciality->speciality->name }}</option>
						@endforeach
					</select>
					<label for="speciality_ids">Specialization <span class="text-danger">*</label>
				</div>
			</div>
		</div>
		@if(@$hospital->status != 'Empanelment Not Recommended by DEC' && @$hospital->status != 'Empannelled')
		<div class="col-md-12">
			<div
				class="d-flex justify-content-end">
				<button
					class="btn btn-primary saveaccreditation">SAVE</button>
			</div>
		</div>
		@endif
	</form>
</div>
<script>

	bsRangePickerSingle = $('#bs-rangepicker-singlee'),
   	bsRangePickerSingle2 = $('#bs-rangepicker-singlee-2');
	if (bsRangePickerSingle.length) {
      bsRangePickerSingle.daterangepicker({
		locale: {
		format: 'YYYY-MM-DD',
		cancelLabel: 'Clear'
		},
		singleDatePicker: true,
        maxDate: moment(),
		autoUpdateInput: true,
		showDropdowns: true,
		opens: 'left'
      });
      @if(!@$hospital_accreditation->valid_from)
		bsRangePickerSingle.val('');
      @endif

      bsRangePickerSingle.on('cancel.daterangepicker', function(ev, picker) {
         $(this).val('');
      });
	}

   if (bsRangePickerSingle2.length) {
      bsRangePickerSingle2.daterangepicker({
         locale: {
            format: 'YYYY-MM-DD',
            cancelLabel: 'Clear'
         },
         singleDatePicker: true,
         minDate: moment().add(1, 'days'), 
         autoUpdateInput: true,
         showDropdowns: true,
         opens: 'left'
      });
      
      @if(!@$hospital_accreditation->valid_till)
         bsRangePickerSingle2.val('');
      @endif

      bsRangePickerSingle2.on('cancel.daterangepicker', function(ev, picker) {
         $(this).val('');
      });
	}

	$("input[name='accreditation']").on("change",function(){
		if($("input[name='accreditation']:checked").val() == 'Yes'){
			$(".accreditation-field").removeClass('d-none');
		}else{
			$(".accreditation-field").addClass('d-none');
		}
	})
	$('.saveaccreditation').click(function () {
        ldrshow();
		$('.error').remove();
        var step = 6;
        // Create a FormData object
        var formData = new FormData($('#accreditationForm')[0]);
        $.ajax({
            url: '{{route("hospital.empanelmentRegistration.accreditationForm", [$uuid, $hospital_id])}}', // Replace with your server endpoint
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
                        $('.step6Icon').show();
                        // Populate the content of the step
                        // $(`.step${step}`).html(data.html || data);
                        loadStep(7);
                        
                    }, 1000);
                } else {
                    errorMessage(response.message);
                }              
            },
            error: function (xhr) {
               ldrhide();
                $('.error').remove();
                
                if (xhr.status === 422) { 
                    let errors = xhr.responseJSON.errors;
                    for (let field in errors) {
						if( field =='speciality_ids'){
							$(`[name="speciality_ids[]"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
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
    });
</script>