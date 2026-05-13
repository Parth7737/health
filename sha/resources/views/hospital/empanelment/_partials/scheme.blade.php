<div class="inside-left-info-box schemetab @if($hospital && $hospital->scheme != '') success @else pending @endif">
    <h4 class="colored-verticle-title">
    Scheme Details 
    <span class="status-dot">
        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="undefined">
            <path d="M400-304 240-464l56-56 104 104 264-264 56 56-320 320Z" />
        </svg>
    </span>
    </h4>
    <form id="schemeForm" enctype="multipart/form-data">
        <div class="row g-5">
            <div class="col-md-12 col-lg-4">
                <div class="form-floating form-floating-outline schemeerror">
                    <select class="select2" id="scheme" name="scheme" required>
                        <option value="">Select</option>
                        @foreach($schemes as $key => $value)
                            <option value="{{$value->id}}" @if($hospital && $hospital->scheme == $value->id) selected @endif>{{$value->name}}</option>
                        @endforeach
                    </select>
                    <label for="scheme">Scheme</label>
                </div>
            </div>
            <h5 class="colored-verticle-title">
                Hospital Main Image
            </h5>
            <div class="col-md-12 col-lg-4">
                <div class="file-upload-section schemeerror">
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
                    <input type="file" class="file-input d-none" required name="image" id="imagemain" accept="image/*"/>
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
                <span class="small">Supported file types: JPG, PNG, JPEG. Max image dimensions: 1920x1080 pixels</span>

                @if(@$hospital->image)
                    <img src="{{asset('public/storage/'.@$hospital->image)}}" class="img img-thumbnail img-responsive border-1 mt-2" style="width: 100px;height: 100px;" alt="">
                @endif
            </div>
            <hr>
            <h5 class="colored-verticle-title">
                Hospital Gallery Images
            </h5>
            <div class="col-md-12 col-lg-12">
                <div class="file-upload-section schemeerror multiimage">
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
                    <input type="file" class="file-input d-none" required name="images[]" id="imagesmain" accept="image/*" multiple />
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
                <span class="small">Supported file types:JPG, PNG, JPEG. Max image dimensions: 1920x1080 pixels</span>               
            </div>
            @foreach(@$hospital->images as $key => $value)
                <div class="col-md-6 col-lg-2">
                    @if(@$value->image)
                        <img src="{{asset('public/storage/'.@$value->image)}}" class="img img-thumbnail img-responsive border-1 mt-2" style="width: 100px;height: 100px;" alt="">
                    @endif
                </div>
            @endforeach
            
            <hr />
            <h5 class="colored-verticle-title">
                Hospital PPT
            </h5>
            <div class="col-md-12 col-lg-12">
                <div class="file-upload-section schemeerror">
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
                    <input type="file" class="file-input d-none" required name="hospital_ppt" id="hospital_ppt" accept=".pdf"  />
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
                <span class="small text-muted">Supported file types: PDF (Max: 10MB)</span>
               
            </div>
            
            @if(@$hospital->hospital_ppt)
                <div class="col-md-6 col-lg-2">
                    <div class="preivew-certificate">
                        @if(@$hospital->hospital_ppt)
                            <label class="mt-2"><a href="{{ asset('public/storage/'.@$hospital->hospital_ppt) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Hospital PPT</a></label>
                        @endif
                    </div>
                </div>
            @endif

            @if($hospital->status != 'Empannelled' && @$hospital->status != 'Empanelment Not Recommended by DEC')
                <div class="col-md-12">
                    <div
                        class="d-flex justify-content-end">
                        <button type="button"
                        class="btn btn-primary savescheme">SAVE</button>
                    </div>
                </div>
            @endif
        </div>
    </form>
</div>

<script>
    document.getElementById('imagesmain').addEventListener('change', function () {
        if (this.files.length > 5) {
            alert('You can only upload a maximum of 5 images.');
            this.value = ''; // Clear the input
        }
    });

    $(document).ready(function () {
        $('.itemName').text('Scheme');
    });
    
    $('.savescheme').click(function () {
       ldrshow();
      $('.error').remove();

        var step = 2;
        // Create a FormData object
        var formData = new FormData($('#schemeForm')[0]);
        // Send an AJAX request
        $.ajax({
            url: '{{route("hospital.empanelmentRegistration.saveScheme", [$uuid, $hospital_id])}}', // Replace with your server endpoint
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
                    $('.step1Icon').show();
                    // Populate the content of the step
                    // $(`.step${step}`).html(data.html || data);
                    $('.schemetab').removeClass('pending').addClass('success');
                    loadStep(2);
                    
                }, 1000);
            },
            error: function (xhr) {
               ldrhide();
                $('.error').remove();
                
                if (xhr.status === 422) { 
                    let errors = xhr.responseJSON.errors;
                    for (let field in errors) {
                        let errorMessage = errors[field][0]; // Get the first error message
                        if (field.startsWith("images")) {
                            // If the error is related to images, display it near the file upload section
                            $('.multiimage').after(`<div class="error text-danger">${errorMessage}</div>`);
                        } else {
                            // For other fields, find the closest element and display the error
                            $(`[name="${field}"]`).closest('.schemeerror').after(`<div class="error text-danger">${errorMessage}</div>`);
                        }
                    }
                } else {
                    alert('Something went wrong. Please try again later.');
                }
            }
        });
    });
</script>