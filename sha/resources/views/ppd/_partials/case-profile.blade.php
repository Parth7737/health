
<div
    class="table-responsive">
    <table class="table">
        <thead class="table-dark">
        </thead>
        <tbody
            class="table-border-bottom-0">
            <tr>
                <td>Request Type</td>
                <td>{{ $preauth_register->status_label }}</td>
            </tr>
            <tr>
                @php 
                $admission_details = App\Models\AdmissionDetails::where('preauth_register_id',$preauth_register->id)->first();
                $admission_type = App\Models\AdmissionType::where('id',$admission_details->admission_type)->first();
                @endphp
                <td>Admission Type</td>
                <td>{{ @$admission_details->admission_type->name }}</td>
            </tr>
            <tr>
                <td>Suspicion Level</td>
                <td>Not Suspicious</td>
            </tr>
            <tr>
                <td>Authentication Status</td>
                <td>{{ $preauth_register->kyc_type == 'without_auth'?'Without Authentication':'Aadhar Verified' }}</td>
            </tr>
            <tr>
                <td>Hospital Type</td>
                <td>{{ @$preauth_register->hospital->facilityOwnershipType->name }}</td>
            </tr>
            <tr>
                <td>Hospital State</td>
                <td>{{ @$preauth_register->hospital->hospitalAddress->states->name }}</td>
            </tr>
            <tr>
                <td>Hospital Name</td>
                <td>{{ @$preauth_register->hospital->facility_name."(".@$preauth_register->hospital->hospital_id.")-".@$preauth_register->hospital->hospitalAddress->villages->name }}</td>
            </tr>
            <tr>
                <td>Case Type</td>
                <td>{{ @$preauth_register->patient_type }}</td>
            </tr>
        </tbody>
    </table>
</div>