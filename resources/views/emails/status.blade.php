<!DOCTYPE html>
<html>
<head>
    <title>Application Status</title>
</head>
<body>
    <p>Hello {{$data['userdata']->name}},</p>
    <p>{{$data['message']}}</p>
    
    <p><strong>Remarks:</strong> {{ $data['remark'] }}</p>
    
    <p>Please review it at the earliest and proceed with the next steps.</p>
    
    <p><b>Best regards,</b></p>
    <p><b>SHA <br>
        (State Health Authority)
        </b>
    </p>
</body>
</html>
