<div id="specialityCard">
    <div class="eo-card-hdr">
        <h3 class="eo-card-title"><i class="fas fa-stethoscope" style="color:#4db6ac"></i> Specialities</h3>
        <p class="eo-panel-sub">Mark which clinical specialities are available at this facility. Save to continue.</p>
    </div>
    <div class="eo-card-body">
        <div class="table-responsive">
            <table class="table eo-staff-table mb-0">
                <thead>
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
        </div>
    </div>
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
</script>