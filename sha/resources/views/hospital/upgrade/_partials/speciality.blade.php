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
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @foreach($specialities as $key => $value)
                @php
                    $availableSpeciality = App\CentralLogics\Helpers::getUSingleSpecialities($hospital_id, $value->id)
                @endphp
                <tr>
                    <td> {{$loop->iteration}}</td>
                    <td>{{$value->name}}</td>
                    <td> {{$value->code}} <input type="hidden" value="{{$value->id}}" name="speciality_id[]"><input type="hidden" value="@if($availableSpeciality){{$availableSpeciality->id}}@endif" name="tableid[]"></td>
                    <td>
                        <div class="form-check mt-4">
                            <input class="form-check-input" @if($availableSpeciality && $availableSpeciality->available == 1) checked @endif type="checkbox" id="available{{$value->id}}" name="available_{{$value->id}}" value="1" onclick="visibleOfferedCheckbox('{{$value->id}}');" />
                        </div>
                    </td>
                    <td>
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" onclick="visibleTextCheckbox('{{$value->id}}')" @if($availableSpeciality && $availableSpeciality->offered == 1) checked @endif @if($availableSpeciality && $availableSpeciality->available != 1) disabled @elseif(!$availableSpeciality) disabled @endif value="1" id="offered{{$value->id}}" name="offered_{{$value->id}}" />
                        </div>
                    </td>
                    <td>
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="not_offered_reason{{$value->id}}" oninput="sanitize(this, 'b');" @if($availableSpeciality && $availableSpeciality->not_offered_reason == "") disabled @elseif($availableSpeciality && $availableSpeciality->offered == 0)  @elseif(!$availableSpeciality) disabled @endif class="form-control" value="{{$availableSpeciality && $availableSpeciality->not_offered_reason != '' ? $availableSpeciality->not_offered_reason : ''}}" placeholder="" name="not_offered_reason_{{$value->id}}" />
                        </div>
                    </td>
                    <td>
                        <input type="text" id="remark{{$value->id}}" value="{{$availableSpeciality && $availableSpeciality->remark != '' ? $availableSpeciality->remark : ''}}" name="remark_{{$value->id}}" class="form-control" oninput="sanitize(this, 'b');" placeholder="" />
                    </td>
                </tr>
                @endforeach
            
            </tbody>
        </table>
        
        @if(\App\CentralLogics\Helpers::isbtnenabled(@$hospital->status))
            <div class="col-md-12 mt-2">
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
     $(document).ready(function () {
        $('.itemName').text('Speciality');
    });
    function visibleOfferedCheckbox(id) {
        const availableCheckbox = $(`#available${id}`);
        const offeredCheckbox = $(`#offered${id}`);
        const notOfferedReason = $(`#not_offered_reason${id}`);

        if (availableCheckbox.is(':checked')) {
            offeredCheckbox.prop('disabled', false); // Enable "Offered"
            notOfferedReason.prop('disabled', false); // Enable "Reason for not offering"
        } else {
            offeredCheckbox.prop('disabled', true).prop('checked', false); // Disable and uncheck "Offered"
            notOfferedReason.prop('disabled', true).val(''); // Disable and clear "Reason for not offering"
        }
    }

    function visibleTextCheckbox(id) {
        const offeredCheckbox = $(`#offered${id}`);
        const notOfferedReason = $(`#not_offered_reason${id}`);

        if (offeredCheckbox.is(':checked')) {
            notOfferedReason.prop('disabled', true).val('');
        } else {
            notOfferedReason.prop('disabled', false);
        }
    }

    $('.savespecialities').click(function () {
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

                var step = 5;
                // Create a FormData object
                var formData = new FormData($('#specialitiesForm')[0]);
                console.log(formData);
                $('#specialitiesForm input[type="checkbox"]').each(function () {
                    if (!this.checked) {
                        formData.append(this.name, 0); // Append 0 for unchecked
                    }
                });
                // Send an AJAX request
                $.ajax({
                    url: '{{route("hospital.upgradeSpecialities", [$uuid, $hospital_id])}}', // Replace with your server endpoint
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
                                        // $('.step4Icon').show();
                                        // Populate the content of the step
                                        // $(`.step${step}`).html(data.html || data);
                                        loadStep(5);
                                        
                                    }, 1000);
                                }
                            });
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
            }
        });
    });
</script>