@foreach($hrdata as $key => $value)
    <tr id="hrrow{{$value->id}}">
        <td>{{$loop->iteration}}</td>
        <td>{{$value->registration_number}}</td>
        <td>{{$value->type_of_human_resource}}</td>
        <td>{{@$value->humanResource->name}}</td>
        <td>{{$value->name}}</td>
        <td>{{$value->mobile_no}}</td>
        <td><a href="javascript:;" onclick="deleteHrData('{{$value->id}}', '{{$value->hospital_id}}');"><i class="tf-icons ri-close-fill text-danger"></i></a></td>
    </tr>
@endforeach