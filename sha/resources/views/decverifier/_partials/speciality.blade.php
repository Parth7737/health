@php
    $isedit = false;
    $readonly = '';
    $disabled = '';
@endphp
@if($verification->status == "Physical Verification Completed")
    @php($isedit = true)
    @php($readonly = 'readonly')
    @php($disabled = 'disabled')
@endif
<div  class="table-responsive mt-5 text-nowrap">
    <form id="specialitiesForm">
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Sr No.</th>
                    <th>Speciality Name</th>
                    <th>Code</th>
                    <th>Avaliable</th>
                    <th>Offered</th>
                    <th>Reason For not
                        offering
                    </th>
                    <th>Status</th>
                    <th>Remark</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @foreach($hospital->specialities as $key => $value)
               
                <tr>
                    <td> {{$loop->iteration}}</td>
                    <td>{{@$value->speciality->name}}</td>
                    <td> {{@$value->speciality->code}} <input type="hidden" value="{{$value->id}}" name="speciality_id[]"></td>
                    <td>
                        <div class="form-check mt-4">
                            <input class="form-check-input" @if($value && $value->available == 1) checked @endif type="checkbox" id="available{{$value->id}}" name="available_{{$value->id}}" value="1" disabled />
                        </div>
                    </td>
                    <td>
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox"  @if($value && $value->offered == 1) checked @endif disabled />
                        </div>
                    </td>
                    <td>
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="not_offered_reason{{$value->id}}" readonly class="form-control" value="{{$value && $value->not_offered_reason != '' ? $value->not_offered_reason : ''}}" placeholder="" name="not_offered_reason_{{$value->id}}" />
                        </div>
                    </td>
                    <td>
                        <select class="select2 aerror" id="dec_verify_status_{{$value->id}}verifier" name="dec_verify_status_{{$value->id}}" {{$disabled}} required>
                            <option value="">Select</option>
                            <option value="Valid" @if(@$value->dec_verify_status == "Valid") selected @endif >Valid</option>
                            <option value="InValid" @if(@$value->dec_verify_status == "InValid") selected @endif >InValid</option>
                        </select>
                    </td>
                    <td>
                        <div class="form-floating form-floating-outline">
                            <input type="text"
                                {{$readonly}}
                                oninput="sanitize(this, 'b');"
                                id="dec_verify_remark_{{$value->id}}"
                                name="dec_verify_remark_{{$value->id}}"
                                value="{{@$value->dec_verify_remark}}"
                                class="form-control aerror"
                                placeholder="text"/>
                        </div>
                    </td>
                </tr>
                @endforeach
            
            </tbody>
        </table>
        @if(!$isedit)
        <div class="col-md-12">
            <div
                class="d-flex justify-content-end">
                <button type="button"
                class="btn btn-primary savespecialities">SAVE</button>
            </div>
        </div>
        @endif
    </form>
</div>

<script>
    $('.savespecialities').click(function () {
        ldrshow();
      $('.error').remove();

        var step = 3;
        // Create a FormData object
        var formData = new FormData($('#specialitiesForm')[0]);
        console.log(formData);
        // Send an AJAX request
        // saveSpecialityReview
        $.ajax({
            url: '{{route("decverifier.saveSpecialityReview", [base64_encode($hospital->id), base64_encode($hospital->uuid)])}}', // Replace with your server endpoint
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
                        $('.step2Icon').show();
                        loadStep(3);
                    }, 1000);
                } else {
                    errorMessage('Please Select One Speciality!!');
                }              
            },
            error: function (xhr) {
               ldrhide();
                $('.error').remove();
                
                if (xhr.status === 422) { 
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = [];
                    for (let field in errors) {
                        $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        errorMessages.push(errors[field][0]);
                    }
                    if (errorMessages.length > 0) {
                        errorMessage(errorMessages.join('<br>'));
                    }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });
    });
</script>