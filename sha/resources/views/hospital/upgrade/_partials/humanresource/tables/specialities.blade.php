@foreach($specialitiesData as $key => $value)
    <tr id="hrspecrow{{$value->id}}">
        <td>{{$loop->iteration}}</td>
        <td>{{$value->registration_no}}</td>
        <td>{{$value->designation}}</td>
        <td>{{@$value->name}}</td>
        <td>{{$value->mobile}}</td>
        <td>{{$value->speciality->name}}</td>
        <td>
            @if(\App\CentralLogics\Helpers::isbtnenablednyId($value->hospital_id))
            <a href="javascript:;" onclick="editSpecialityData('{{$value->id}}', '{{$value->hospital_id}}');"><i class="tf-icons ri-pencil-fill text-success"></i></a>
            <a href="javascript:;" onclick="deleteSpecialityData('{{$value->id}}', '{{$value->hospital_id}}');"><i class="tf-icons ri-close-fill text-danger"></i></a>
            @endif
        </td>
    </tr>
@endforeach