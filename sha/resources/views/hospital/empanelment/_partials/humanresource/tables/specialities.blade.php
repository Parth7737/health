@foreach($specialitiesData as $key => $value)
    <tr id="hrrow{{$value->id}}">
        <td>{{$loop->iteration}}</td>
        <td>{{$value->registration_no}}</td>
        <td>{{$value->designation}}</td>
        <td>{{@$value->name}}</td>
        <td>{{$value->mobile}}</td>
        <td>{{$value->speciality->name}}</td>
        <td><a href="javascript:;" onclick="deleteSpecialityData('{{$value->id}}', '{{$value->hospital_id}}');"><i class="tf-icons ri-close-fill text-danger"></i></a></td>
    </tr>
@endforeach