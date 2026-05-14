<div class="eo-panel-title"><i class="fas fa-stethoscope" style="color:#4db6ac"></i> Specialities</div>
<p class="eo-panel-sub">Mark which clinical specialities are available at this facility. Save to continue.</p>

<div class="eo-table-panel">
<div  class="table-responsive mt-0 text-nowrap">
    <form id="specialitiesForm">
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Sr No.</th>
                    <th>Speciality Name</th>
                    <th>Code</th>
                    <th>Avaliable</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @foreach($specialities as $key => $value)
                @php
                    $availableSpeciality = App\CentralLogics\Helpers::getSingleSpecialities($hospital->id, $value->id)
                @endphp
                <tr>
                    <td> {{$loop->iteration}}</td>
                    <td>{{$value->name}}</td>
                    <td> {{$value->code}} <input type="hidden" value="{{$value->id}}" name="speciality_id[]"></td>
                    <td>
                        <div class="form-check mt-4">
                            <input class="form-check-input" @if($availableSpeciality && $availableSpeciality->available == 1) checked @endif type="checkbox" id="available{{$value->id}}" name="available_{{$value->id}}" value="1" onclick="visibleOfferedCheckbox('{{$value->id}}');" />
                        </div>
                    </td>
                    <td>
                        <input type="text" id="remark{{$value->id}}" value="{{$availableSpeciality && $availableSpeciality->remark != '' ? $availableSpeciality->remark : ''}}" name="remark_{{$value->id}}" class="form-control" placeholder="" />
                    </td>
                </tr>
                @endforeach
            
            </tbody>
        </table>
        
        @if($hospital->status == "Draft" || !@$hospital || $hospital->status == "Rejected")
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
</div>

<div class="eo-step-nav">
    <button type="button" class="eo-tb-btn" onclick="var p=window.eoWizardPrev && window.eoWizardPrev[4]; if(p) loadStep(p);"><i class="fas fa-arrow-left"></i> Back</button>
    <span class="eo-nav-info">Select at least one speciality where “Available” is checked, then save.</span>
    <span></span>
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
        ldrshow();
      $('.error').remove();

        var formData = new FormData($('#specialitiesForm')[0]);
        $('#specialitiesForm input[type="checkbox"]').each(function () {
            if (!this.checked) {
                formData.append(this.name, 0); // Append 0 for unchecked
            }
        });
        // Send an AJAX request
        $.ajax({
            url: '{{route("hospital.empanelmentRegistration.saveSpecialities", [$uuid, $hospital->id])}}', // Replace with your server endpoint
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
                    var ws = response.wizard_step || response.step;
                    if (typeof window.eoSetUnlockedMax === 'function') {
                        window.eoSetUnlockedMax(ws);
                    }
                    if (typeof window.eoUpdateStepper === 'function') {
                        window.eoUpdateStepper(ws);
                    }
                    $('.nav-link').removeClass('active');
                    $('.tab-pane').removeClass('show active');
                    $(`.step${ws}`).addClass('show active');
                    $(`.navstep${ws}`).addClass('active');
                    setTimeout(() => {
                        loadStep(ws);
                    }, 600);
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