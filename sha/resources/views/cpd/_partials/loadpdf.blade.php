@php($type = '')
@if($investigation->type)
    @php($type = $investigation->type)
@endif
<div class="pdfview">
    <iframe src="{{asset('public/storage/'.@$investigation->file) }}" width="100%" height="500px" ></iframe>
</div>

@if($type == "hospital_bill")
    <div class="row mt-2">
        <div class="col-md-6 col-lg-3 lama-field dama-field normal-field ">
            <div
                class="form-floating form-floating-outline">
                <input type="text" disabled readonly id="bs-rangepicker-third" name="cex_hospital_bill_date" @if(@$preauth_register->cex_hospital_bill_date) value="{{date('Y-m-d', strtotime(@$preauth_register->cex_hospital_bill_date))}}" @endif
                    class="form-control datepicker" />
                <label for="bs-rangepicker-third">Bill Date <span class="text-danger">*</span></label>
            </div>
        </div>
    </div>
@endif
@if($type == "discharge_summary")
    <div class="row mt-2">
        <div class="col-md-6 col-lg-3 lama-field dama-field normal-field ">
            <div
                class="form-floating form-floating-outline">
                <input type="text" disabled readonly
                    id="bs-rangepicker-third" name="cex_admission_date" @if(@$preauth_register->cex_admission_date) value="{{date('Y-m-d', strtotime(@$preauth_register->cex_admission_date))}}" @endif
                    class="form-control datepicker" />
                <label for="bs-rangepicker-third">Admission Date <span class="text-danger">*</span></label>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 lama-field dama-field normal-field ">
            <div
                class="form-floating form-floating-outline">
                <input type="text" disabled readonly
                    id="bs-rangepicker-forth" name="cex_discharge_date" @if(@$preauth_register->cex_discharge_date) value="{{date('Y-m-d', strtotime(@$preauth_register->cex_discharge_date))}}" @endif
                    class="form-control datepicker" />
                <label for="bs-rangepicker-third">Discharge Date <span class="text-danger">*</span></label>
            </div>
        </div>
    </div>
@endif

@if($preauth_register->status == \App\Models\PreauthRegister::STATUS_CPD_CLAIM_PENDING || $preauth_register->status == \App\Models\PreauthRegister::STATUS_ERRONEOUS_CLAIM_PENDING)
<div class="d-flex justify-content-end mt-3 mb-3">
    <button class="btn btn-outline-primary btn-lg" type="button" onclick="verifyDocument('Correct', '{{$investigation->id}}', '{{$type}}');" id="preview">Correct</button>
    <button type="button" class="btn btn-outline-danger ms-2" onclick="verifyDocument('InCorrect', '{{$investigation->id}}', '{{$type}}');" >In Correct</button>
</div>
@endif