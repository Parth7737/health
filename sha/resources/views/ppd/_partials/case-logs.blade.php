

@foreach($case_logs as $case_log)
<strong>Status - {{ $case_log->status }}</strong>
<div class="mb-6 ps-0">
    <div class="row row-cols-5">
        <div class="col">
            <div class="infodata">
                <p><strong>Role - {{ $case_log->role->name }}</strong></p>
            </div>
        </div>
        <div class="col">
            <div class="infodata">
                <p><strong>Stage - {{ $case_log->stage }}</strong></p>
            </div>
        </div>
        <div class="col">
            <div class="infodata">
                <p><strong>Type - {{ $case_log->type }}</strong></p>
            </div>
        </div>
        <div class="col">
            <div class="infodata">
                <p><strong>Remarks - {{ $case_log->remarks }}</strong></p>
            </div>
        </div>
        <div class="col">
            <div class="infodata">
                <p><strong>Amount - {{ $case_log->amount }}</strong></pp>
            </div>
        </div>
        <div class="col">
            <div class="infodata">
                <p><strong>{{ date("d/m/Y | h:i A",strtotime($case_log->created_at)) }}</strong></p>
            </div>
        </div>
        <div class="col">
            <div class="infodata">
                <p><strong>Name - {{ $case_log->user->name }}</strong></p>
            </div>
        </div>
    </div>
</div>
<div
    class="table-responsive">
    <table class="table">
        <thead class="table-dark">
            <tr>
                <th>No.</th>
                <th>Package Code</th>
                <th>Quantity</th>
                <th>Amount</th>
                <th>Action</th>
                <th>Reason</th>
            </tr>
        </thead>
        <tbody class="table-border-bottom-0">
            @php $procedures = json_decode($case_log->procedures); @endphp
            @php $i=1; @endphp
            @foreach($procedures as $procedure)
                @php $procedure_details = App\Models\Procedure::where('id',$procedure->procedure_id)->first(); @endphp
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $procedure_details->procedure_code_2 }}</td>
                    @php 
                        $total = 0;
                        $total +=@$procedure->procedure_price;
                        $total +=@$procedure->stratification_price;
                        if(@$procedure->procedure_price == 0 && $procedure->stratification_price != 0 && $procedure->no_of_days > 1){
                            $total +=$procedure->stratification_price*intval($procedure->no_of_days ?$procedure->no_of_days-1: 0);;
                        }
                        $total +=@$procedure->implant_price*$procedure->implant_qty;
                        $total -=@$procedure->deducted_amount;

                        $total_incentive =@$procedure->incentive;
                    @endphp
                    <td>{{ $procedure->no_of_days }}</td>
                    <td>
                        <span>₹{{ number_format($total+$total_incentive, 2) }}</span>
                    </td>
                    <td>
                        @if($case_log->role_id == 4 || $case_log->role_id == 13)
                            <span>{{ $procedure->preauth_status??'Requested' }}</span>
                        @elseif($case_log->role_id == 14)
                            <span>{{ $procedure->preauth_claim_status??'Requested' }}</span>
                        @elseif($case_log->role_id == 15 || $case_log->role_id == 16 || $case_log->role_id == 17)
                            <span>{{ 'Approved' }}</span>
                        @endif
                    </td>
                    <td>
                        @if($case_log->role_id == 13)
                            <span>{{ $procedure->preauth_reason??'' }}</span>
                        @elseif($case_log->role_id == 14)
                            <span>{{ $procedure->preauth_claim_reason??'' }}</span>
                        @endif
                    </td>
                </tr>
                
                @if($procedure->implant_id)
                @php $implant = App\Models\Implant::where('id',$procedure->implant_id)->first(); @endphp
                    <tr>
                        <td>{{ $i++ }}</td>
                        <td>{{ $implant->code }}</td>
                        @php $sub_total = (@$procedure->implant_price*@$procedure->implant_qty) @endphp
                        <td>{{ 'N/A' }}</td>
                        <td>
                            <span>₹{{ number_format($sub_total, 2) }}</span>
                        </td>
                        <td>
                            @if($case_log->role_id == 4 || $case_log->role_id == 13)
                                <span>{{ $procedure->preauth_implant_status??'Requested' }}</span>
                            @elseif($case_log->role_id == 14)
                                <span>{{ $procedure->preauth_claim_implant_status??'Requested' }}</span>
                            @elseif($case_log->role_id == 15 || $case_log->role_id == 16 || $case_log->role_id == 17)
                                <span>{{ 'Approved' }}</span>
                            @endif
                        </td>
                        <td>
                            @if($case_log->role_id == 13)
                                <span>{{ $procedure->preauth_implant_reason??'' }}</span>
                            @elseif($case_log->role_id == 14)
                                <span>{{ $procedure->preauth_claim_implant_reason??'' }}</span>
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</div>
<div class="border-top border-3 border-primary p-1"></div>
@endforeach