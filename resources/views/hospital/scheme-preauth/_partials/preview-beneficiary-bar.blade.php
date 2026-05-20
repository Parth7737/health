<div class="card mb-2 border-0 shadow-none spa-preview-beneficiary-card">
    <div class="card-body py-2 px-3">
        <div class="row g-3 align-items-start">
            <div class="col-12 col-sm-4 col-lg-3 text-center text-sm-start border-sm-end">
                @if($preauthBeneficiary->image_url ?? false)
                <img src="{{ $preauthBeneficiary->image_url }}" width="72" height="72" alt="Patient"
                    class="rounded-circle mb-2 object-fit-cover" />
                @endif
                <div class="fw-semibold spa-preview-beneficiary-name">{{ @$preauthBeneficiary->name }}</div>
                <div class="text-muted small">{{ @$preauthBeneficiary->age }} Yr / {{ @$preauthBeneficiary->gender }}</div>
            </div>
            <div class="col-6 col-sm-4 col-lg-3">
                <div class="infodata">
                    <label>Care plan</label>
                    <p><strong>{{ @$preauthBeneficiary->care_plan }}</strong></p>
                    <label>SGHS ID</label>
                    <p>{{ @$preauthBeneficiary->card_id }}</p>
                    <label>ABHA</label>
                    <p>{{ @$preauthBeneficiary->aabha_id ?: '—' }}</p>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-lg-4">
                <div class="infodata">
                    <label>Mobile</label>
                    <p>{{ @$preauthBeneficiary->mobile_no }}</p>
                    <label>Address</label>
                    <p class="mb-0">
                        @if(filled($preauthBeneficiary->address ?? null)){{ $preauthBeneficiary->address }} @endif
                        @php
                            $locationLine = collect([
                                $preauthRegister->city ?? null,
                                $preauthRegister->district_name ?? null,
                                $preauthRegister->state_name ?? null,
                            ])->filter()->implode(', ');
                        @endphp
                        @if($locationLine || filled($preauthRegister->pincode ?? null))
                            @if(filled($preauthBeneficiary->address ?? null))<br>@endif
                            {{ $locationLine }}@if(filled($preauthRegister->pincode ?? null)) — {{ $preauthRegister->pincode }}@endif
                        @endif
                    </p>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-lg-2">
                <div class="infodata">
                    <label>Reg. ID</label>
                    <p>{{ $preauthRegister->register_id ?: 'Draft' }}</p>
                    <label>Reg. date</label>
                    <p class="mb-0"><strong>{{ date('d/m/Y h:i A', strtotime($preauthRegister->created_at)) }}</strong></p>
                </div>
            </div>
        </div>
    </div>
</div>
