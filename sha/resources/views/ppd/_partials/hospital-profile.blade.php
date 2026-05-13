
<div
    class="table-responsive">
    <table class="table">
        <thead class="table-dark">
        </thead>
        <tbody
            class="table-border-bottom-0">
            <tr>
                <td>NABH Accreditation</td>
                <td>{{ @$hospital->hospitalAccreditation->accred->name??'-' }}</td>
            </tr>
            <tr>
                <td>Date of empanelment</td>
                <td>{{ @$hospital->status_update_date?date('d/m/Y H:i:s',strtotime($hospital->status_update_date)):'' }}</td>
            </tr>
            <tr>
                <td>State</td>
                <td>{{ @$hospital->hospitalAddress->states->name }}</td>
            </tr>
            <tr>
                <td>District</td>
                <td>{{ @$hospital->hospitalAddress->districts->name }}</td>
            </tr>
            <tr>
                <td>Block</td>
                <td>{{ @$hospital->hospitalAddress->blockdata->name }}</td>
            </tr>
            <tr>
                <td>Village</td>
                <td>{{ @$hospital->hospitalAddress->villages->name }}</td>
            </tr>
            <tr>
                <td>Incentive applied</td>
                <td>{{ @$hospital->hospitalAccreditation->accred->percentage?@$hospital->hospitalAccreditation->accred->percentage."%":'-' }}</td>
            </tr>
            @php
                $preauth_register = App\Models\PreauthRegister::where('hospital_id',$hospital->id)->whereNotNull('preauth_approved_date')->orderBy('preauth_approved_date','asc')->first();
            @endphp
            <tr>
                <td>Date of first preauthorization</td>
                <td>{{ @$preauth_register->preauth_approved_date?date('d/m/Y H:i:s',strtotime($preauth_register->preauth_approved_date)):'' }}</td>
            </tr>
            <tr>
                <td>Suspicious cases pending</td>
                <td>0</td>
            </tr>
            <tr>
                <td>Fraudulant cases confirmed</td>
                <td>0</td>
            </tr>
            @php
                $approved_cases = App\Models\PreauthRegister::whereNotNull('claim_approved_date')->get()->count();
            @endphp
            <tr>
                <td>No. of claims approved</td>
                <td>{{ $approved_cases }}</td>
            </tr>
            @php
                $recovery_amount = App\Models\Recovery::where('hospital_id',@$hospital->id)->sum('recovered_amount');
            @endphp
            <tr>
                <td>Recovery Amount</td>
                <td>{{ $recovery_amount }}</td>
            </tr>
        </tbody>
    </table>
</div>