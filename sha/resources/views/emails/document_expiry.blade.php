<!DOCTYPE html>
<html>
    <head>
        <title>Document Expiry Notification</title>
    </head>
    <body>
        <p>Dear <strong>{{$data['userdata']->name}}</strong>,</p>
        <p>Your Hospital <strong>{{ $data['facility_name'] }}</strong> document <strong>{{ $data['document_name'] }}</strong> will expire in <strong>{{ $data['daysLeft'] }} days</strong>.</p>
        <p>Please renew it at the earliest to avoid deactivation.</p>
        <p>Best regards,<br>
        SHA <br>
        (State Health Authority)
    </p>
    </body>
</html>
