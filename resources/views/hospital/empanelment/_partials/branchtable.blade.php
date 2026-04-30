@foreach($branches as $key => $value)
    <tr id="branchrow{{$value->id}}">
        <td>{{$loop->iteration}}</td>
        <td>{{$value->name}}</td>
        <td>{{$value->code}}</td>
        <td>{{@$value->type->name}}</td>
        <td>{{$value->email}}</td>
        <td>{{$value->phone}}</td>
        
        @if(@$hospital->status == "Draft")
            <td><a href="javascript:;" onclick="deleteBranchData('{{$value->id}}', '{{$value->uuid}}');"><i class="tf-icons ri-close-fill text-danger"></i></a></td>
        @endif
    </tr>
@endforeach