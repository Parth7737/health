@foreach($hrdata as $key => $value)
    <tr id="hrrow{{$value->id}}">
        <td>{{$loop->iteration}}</td>
        <td>{{$value->registration_number}}</td>
        <td>{{$value->type_of_human_resource}}</td>
        <td>{{@$value->humanResource->name}}</td>
        <td>{{$value->name}}</td>
        <td>{{$value->mobile_no}}</td>
        <td>
            @if(\App\CentralLogics\Helpers::isbtnenablednyId($value->hospital_id))
                <a href="javascript:;" @if($value->type == "mhr") onclick="editHrData('{{$value->id}}', '{{$value->hospital_id}}', '{{$value->type}}');" @else onclick="editSSHRData('{{$value->id}}', '{{$value->hospital_id}}', '{{$value->type}}');" @endif><i class="tf-icons ri-pencil-fill text-success"></i></a>
                @if(@$value->humanResource->name != "Medical Superintendent")
                    <a href="javascript:;" onclick="deleteHrData('{{$value->id}}', '{{$value->hospital_id}}');"><i class="tf-icons ri-close-fill text-danger"></i></a>
                @endif
            @endif
        </td>
    </tr>
@endforeach