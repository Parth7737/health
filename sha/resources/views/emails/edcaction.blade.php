<!DOCTYPE html>
<html>
<head>
    <title>Application Status</title>
</head>
<body>
    <p>Hello {{@$data->userdata->name}},</p>
    <p>{{@$data->message}}</p>
    
    @php
        $workflow = $data->actionData->workflow()->orderBy('id', 'DESC')->first();
    @endphp
    <ul>
        <li><strong>Action:</strong> {{ $data->actionData->last_action }}</li>
        <li><strong>Remark:</strong> {{ @$workflow->remark }}</li>
        @if(@$workflow->action_start_date)
            <li><strong>Start Date:</strong> {{ @$workflow->action_start_date }}</li>
        @endif

        @if(@$workflow->action_start_date)
            <li><strong>End Date:</strong> {{ @$workflow->action_end_date }}</li>
        @endif
        
        @if(@$workflow->date_of_issuance)
        <li><strong>Date of Submission:</strong> {{ @$workflow->date_of_issuance }}</li>
        @endif

        @if(@$workflow->fir_case_number)
        <li><strong>Fir Case Number</strong> {{ @$workflow->fir_case_number }}</li>
        @endif

        @if(@$workflow->penalty_imposed)
        <li><strong>Penalty Imposed:</strong> {{ @$workflow->penalty_imposed }}</li>
        @endif

        @if(@$workflow->penalty_recovered)
        <li><strong>Penalty Recovered:</strong> {{ @$workflow->penalty_recovered }}</li>
        @endif
    </ul>
    <p>Please review it at the earliest.</p>
    
    <p><b>Best regards,</b></p>
    <p><b>SHA <br>
        (State Health Authority)
        </b>
    </p>
</body>
</html>
