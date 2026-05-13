<div class="card">
    <div class="card-body">
        
        <form id="documentUpdateForm" enctype="multipart/form-data">
            <input type="hidden" id="document_expired_id" name="document_expired_id" value="{{@$data['expired_doc_id']}}" class="form-control" />
            <input type="hidden" id="hospital_id" name="hospital_id" value="{{@$data['hospital_id']}}" class="form-control" />
            <input type="hidden" id="document_id" name="document_id" value="{{@$data['document_id']}}" class="form-control" />

            <div class="row mt-2">
                <div class="col-md-12 col-lg-6 lama-field dama-field normal-field ">
                    <div
                        class="form-floating form-floating-outline documenterror">
                        <input type="text" id="start_date" name="start_date" class="form-control datepicker" />
                        <label for="start_date">Valid From <span class="text-danger">*</span></label>
                    </div>
                </div>
                <div class="col-md-12 col-lg-6 lama-field dama-field normal-field ">
                    <div
                        class="form-floating form-floating-outline documenterror">
                        <input type="text" id="expiry_date" value="{{@$data['document_expire_date']}}" name="expiry_date" class="form-control datepicker" />
                        <label for="expiry_date">Expiry Date <span class="text-danger">*</span></label>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12 col-lg-6 lama-field dama-field normal-field ">
                    <div class="file-upload-section documenterror">
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
                        <input type="file" class="file-input d-none" required name="document" id="document" />
                        <div class="uploaded-file file-upload-display d-none">
                            <span class="file-name">Sample.pdf</span>
                            <i class="fas fa-trash "></i>
                            <button
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
                </div>
            </div>

            <div class=" row mt-2 pdfview">
                <iframe src="{{asset('public/storage/'.@$data['file']) }}" width="100%" height="500px" ></iframe>
            </div>

            <div class="row mt-2">
                <div class="d-flex justify-content-end mt-3">
                    <button class="btn btn-primary updatedocument" type="button" >SAVE</button>
                </div>
            </div>
        </form>
        
    </div>
    
</div>

<script>
    $(document).ready(function() {
        $('.datepicker[name$="start_date"]').daterangepicker({
            singleDatePicker: true,
            autoUpdateInput: true,
            minDate: moment(), // Restrict to past dates
            locale: {
                format: 'YYYY-MM-DD'
            },
            opens: 'right'
        });

        $('.datepicker[name$="start_date"]').on('apply.daterangepicker', function (ev, picker) {
			$(this).val(picker.startDate.format('YYYY-MM-DD'));
		});

		$('.datepicker[name$="start_date"]').on('cancel.daterangepicker', function (ev, picker) {
			$(this).val('');
		});

        $('.datepicker[name$="expiry_date"]').daterangepicker({
            singleDatePicker: true,
            autoUpdateInput: true,
            minDate: moment(), // Restrict to past dates
            locale: {
                format: 'YYYY-MM-DD'
            },
            opens: 'right'
        });

        $('.datepicker[name$="expiry_date"]').on('apply.daterangepicker', function (ev, picker) {
			$(this).val(picker.startDate.format('YYYY-MM-DD'));
		});

		$('.datepicker[name$="expiry_date"]').on('cancel.daterangepicker', function (ev, picker) {
			$(this).val('');
		});

        $('.updatedocument').click(function () {
            ldrshow();
            $('.error').remove();
            var step = 5;
            // Create a FormData object
            var formData = new FormData($('#documentUpdateForm')[0]);
            var document_expired_id = $("#document_expired_id").val();
            // Send an AJAX request
            $.ajax({
                url: '{{route("hospital.updateDocument")}}', // Replace with your server endpoint
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
                    $('#licenseForm input[type="file"]').val('');
                    $("#exdocid"+document_expired_id).remove();
                    $('#documentModal').modal('hide');
                },
                error: function (xhr) {
                    ldrhide();
                    $('.error').remove();
                    
                    if (xhr.status === 422) { 
                        let errors = xhr.responseJSON.errors;
                        for (let field in errors) {
                            $(`[name="${field}"]`).closest('.documenterror').after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                    } else {
                        alert('Something went wrong. Please try again later.');
                    }
                }
            });
        });
    });
</script>