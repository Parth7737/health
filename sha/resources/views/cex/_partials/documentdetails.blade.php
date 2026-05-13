@php
    $status = \App\CentralLogics\Helpers::checkStatus($preauth_register->id, 'cex');

    $datetype = 'No';

    if(date('d/m/Y', strtotime(@$preauth_register->preauth_submission_date)) == date('d/m/Y', strtotime(@$preauth_register->cex_admission_date)) && date('d/m/Y', strtotime(@$preauth_register->discharge_date)) == date('d/m/Y', strtotime(@$preauth_register->cex_discharge_date)) && date('d/m/Y', strtotime(@$preauth_register->bill_date)) == date('d/m/Y', strtotime(@$preauth_register->cex_hospital_bill_date))) {
        $datetype = 'Yes';
    }
@endphp

<div class="row g-5">
    <div class="col-12">
        <div class="table-responsive mt-5 text-nowrap">
            <table class="table">
                <thead class="table-dark">
                    <tr>
                        <th>Verification Parameter</th>
                        <th>Date Entered By Hospital</th>
                        <th>Date As Per Document</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0 finance-body">
                    <tr>
                        <td>Admission Date</td>
                        <td>{{date('d/m/Y', strtotime(@$preauth_register->preauth_submission_date))}}</td>
                        <td>@if($preauth_register->cex_admission_date){{date('d/m/Y', strtotime(@$preauth_register->cex_admission_date))}} @endif</td>
                        <td>@if(date('d/m/Y', strtotime(@$preauth_register->preauth_submission_date)) == date('d/m/Y', strtotime(@$preauth_register->cex_admission_date))) <span class="text-primary">Matched</span> @else <span class="text-danger">Unmatched</span>@endif</td>
                    </tr>
                    <tr>
                        <td>Discharge Date</td>
                        <td>{{date('d/m/Y', strtotime(@$preauth_register->discharge_date))}}</td>
                        <td>@if($preauth_register->cex_discharge_date){{date('d/m/Y', strtotime(@$preauth_register->cex_discharge_date))}} @endif</td>
                        <td>@if(date('d/m/Y', strtotime(@$preauth_register->discharge_date)) == date('d/m/Y', strtotime(@$preauth_register->cex_discharge_date))) <span class="text-primary">Matched</span> @else <span class="text-danger">Unmatched</span> @endif</td>
                    </tr>
                    <tr>
                        <td>Hospital Bill Date</td>
                        <td>{{date('d/m/Y', strtotime(@$preauth_register->bill_date))}}</td>
                        <td>@if($preauth_register->cex_hospital_bill_date){{date('d/m/Y', strtotime(@$preauth_register->cex_hospital_bill_date))}} @endif</td>
                        <td>@if(date('d/m/Y', strtotime(@$preauth_register->bill_date)) == date('d/m/Y', strtotime(@$preauth_register->cex_hospital_bill_date))) <span class="text-primary">Matched</span> @else <span class="text-danger">Unmatched</span>@endif</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <table class="table">
            <thead class="table-white">
                <tr>
                    <th class="text-primary p-2">Overall observations on the documents by CEX</th>
                    <th class="float-left p-2">@if($status) <span class="text-primary"><strong>Correct</strong></span> @else <span class="text-danger"><strong>Incorrect</strong></span> @endif</th>
                </tr>
                <tr>
                    <th class="text-primary p-2">Los Matching with approved treatment plan:</th>
                    <th class="float-left p-2">@if($datetype == 'Yes' && $status) <span class="text-primary"><strong>Yes</strong></span> @else <span class="text-danger"><strong>No</strong></span> @endif</th>
                </tr>
            </thead>
        </table>
    </div>
</div>