@php
   $isedit = false;
   $readonly = '';
   $disabled = '';

   if($verification->status == "Physical Verification Completed") {
      $isedit = true;
      $readonly = 'readonly';
      $disabled = 'disabled';
   }
@endphp
<div class="card p-0 shadow-none rounded-0  border-bottom">
    <div class="row">      
        <h5 class="theme-color mt-3">Financial Information</h5>
        <div class="row">

            <div class="col-md-3">
                <div class="infodata">
                    <label><strong>Account Holder's Name</strong></label>
                    <p>{{ @$hospital->financialInformation->account_holder }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="infodata">
                    <label><strong>Bank Account Number</strong></label>
                    <p>{{ @$hospital->financialInformation->account_no }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="infodata">
                    <label><strong>IFSC Code</strong></label>
                    <p>{{ @$hospital->financialInformation->ifsc_code }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="infodata">
                    <label><strong>Bank Name</strong></label>
                    <p>{{ @$hospital->financialInformation->bank_name }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="infodata">
                    <label><strong>Bank Branch Name</strong></label>
                    <p>{{ @$hospital->financialInformation->bank_branch_name }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="infodata">
                    <label><strong>Bank Address</strong></label>
                    <p>{{ @$hospital->financialInformation->bank_address }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="infodata">
                    <label><strong>MICR</strong></label>
                    <p>{{ @$hospital->financialInformation->micr }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="infodata">
                    <label><strong>Authorised signatory Name</strong></label>
                    <p>{{ @$hospital->financialInformation->authorised_signatory_name }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="infodata">
                    <label><strong>Account Type</strong></label>
                    <p>{{ @$hospital->financialInformation->account_type }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="infodata">
                    <label><strong>Bank Email ID</strong></label>
                    <p>{{ @$hospital->financialInformation->bank_email }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="infodata">
                    <label><strong>NEFT Enabled</strong></label>
                    <p>{{ @$hospital->financialInformation->neft_enabled }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="infodata">
                    <label><strong>BSR Code</strong></label>
                    <p>{{ @$hospital->financialInformation->bsr_code }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="infodata">
                    <label><strong>Cancelled Cheque</strong></label><br>
                    @if(@$hospital->financialInformation->cancelled_cheque)
                        <label class="mt-2"><strong>Preview</strong>&nbsp; <a href="{{ asset('public/storage/'.@$hospital->financialInformation->cancelled_cheque) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a></label>
                    @endif
                </div>
            </div>


            <div class=" mt-5 ">
                <form id="financialInfo">
                    <table class="table table-responsive table-bordered">
                        <thead class="table-dark">
                            <tr>    
                                <td>Physical Verifier</td>
                                <td>Remark</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="form-floating form-floating-outline">
                                        <select {{$disabled}} class="select2 financeerror" id="dec_verify_statussfinance" name="dec_verify_status" required>
                                            <option value="">Select</option>
                                            <option value="Valid" @if(@$hospital->financialInformation->dec_verify_status == "Valid") selected @endif >Valid</option>
                                            <option value="InValid"  @if(@$hospital->financialInformation->dec_verify_status == "InValid") selected @endif >InValid</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-floating form-floating-outline">
                                        <input type="text"
                                            {{$readonly}}
                                            id="dec_verify_remark"
                                            oninput="sanitize(this, 'b');"
                                            name="dec_verify_remark"
                                            value="{{@$hospital->financialInformation->dec_verify_remark}}"
                                            class="form-control financeerror"
                                            placeholder="text"/>
                                    </div>
                                </td>
                            </tr>
                            
                        </tbody>
                    </table>
                    @if(!$isedit)
                        <div class="d-flex justify-content-end mt-3">
                            <button type="button" class="btn btn-primary rounded-0 savefinancialInfo mt-2" >Save</button>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <hr>
        <h5 class="theme-color mt-3">Taxation Details</h5>
        <div class="row">
            <div class="col-md-3">
                <div class="infodata">
                    <label><strong>Pan Number</strong></label>
                    <p>{{ @$hospital->taxDetails->pan_no }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="infodata">
                    <label><strong>Name On Pan Card</strong></label>
                    <p>{{ @$hospital->taxDetails->pan_name }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="infodata">
                    <label><strong>TAN Number</strong></label>
                    <p>{{ @$hospital->taxDetails->tan_no }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="infodata">
                    <label><strong>TAN Holder Name</strong></label>
                    <p>{{ @$hospital->taxDetails->batan_holder_namenk_name }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="infodata">
                    <label><strong>GST NUMBER</strong></label>
                    <p>{{ @$hospital->taxDetails->gst_no }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="infodata">
                    <label><strong>Name on GST Certificate</strong></label>
                    <p>{{ @$hospital->taxDetails->gst_name }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="infodata">
                    <label><strong>TDS Exemption Application</strong></label>
                    <p>{{ @$hospital->taxDetails->tdsexemption->name }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="infodata">
                    <label><strong>Original TDS Rate</strong></label>
                    <p>{{ @$hospital->taxDetails->tds_rate }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="infodata">
                    <label><strong>TDS Exemption Certificate Number</strong></label>
                    <p>{{ @$hospital->taxDetails->tds_exemption_certificate_no }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="infodata">
                    <label><strong>After Exemption Applicable TDS Rate</strong></label>
                    <p>{{ @$hospital->taxDetails->after_tds_rate }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="infodata">
                    <label><strong>TDS Exemption Valid From</strong></label>
                    <p>{{ @$hospital->taxDetails->tds_exemption_valid_from }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="infodata">
                    <label><strong>TDS Exemption Valid Till</strong></label>
                    <p>{{ @$hospital->taxDetails->tds_exemption_valid_till }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="infodata">
                    <label><strong>TDS Exemption Amount</strong></label>
                    <p>{{ @$hospital->taxDetails->tds_exemption_amount }}</p>
                </div>
            </div>
            @if(@$hospital->taxDetails->pan_certificate)
            <div class="col-md-3">
                <div class="infodata">
                    @if(@$hospital->taxDetails->pan_certificate)
                        <a href="{{ asset('public/storage/'.@$hospital->taxDetails->pan_certificate) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Pan Certificate</a></label>
                    @endif
                </div>
            </div>
            @endif
            @if(@$hospital->taxDetails->gst_certificate)
            <div class="col-md-3">
                <div class="infodata">
                    @if(@$hospital->taxDetails->gst_certificate)
                        <a href="{{ asset('public/storage/'.@$hospital->taxDetails->gst_certificate) }}" target="_blank" class="btn btn-outline-primary btn-sm">View GST Certificate</a></label>
                    @endif
                </div>
            </div>
            @endif
            @if(@$hospital->taxDetails->tds_exemption_certificate)
            <div class="col-md-3">
                <div class="infodata">
                    @if(@$hospital->taxDetails->tds_exemption_certificate)
                       <a href="{{ asset('public/storage/'.@$hospital->taxDetails->tds_exemption_certificate) }}" target="_blank" class="btn btn-outline-primary btn-sm">View TDS Excemption Certificate</a></label>
                    @endif
                </div>
            </div>
            @endif
            <div class=" mt-5 ">
                <form id="taxationForm">
                    <table class="table table-responsive table-bordered">
                        <thead class="table-dark">
                            <tr>    
                                <td>Physical Verifier</td>
                                <td>Remark</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="form-floating form-floating-outline">
                                        <select {{$disabled}}  class="select2 taxationerror" id="dec_verify_statusstaxation" name="dec_verify_status" required>
                                            <option value="">Select</option>
                                            <option value="Valid" @if(@$hospital->taxDetails->dec_verify_status == "Valid") selected @endif >Valid</option>
                                            <option value="InValid"  @if(@$hospital->taxDetails->dec_verify_status == "InValid") selected @endif >InValid</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-floating form-floating-outline">
                                        <input type="text"
                                            {{$readonly}}
                                            id="dec_verify_remark"
                                            oninput="sanitize(this, 'b');"
                                            name="dec_verify_remark"
                                            value="{{@$hospital->taxDetails->dec_verify_remark}}"
                                            class="form-control taxationerror"
                                            placeholder="text"/>
                                    </div>
                                </td>
                            </tr>
                            
                        </tbody>
                    </table>
                    @if(!$isedit)
                        <div class="d-flex justify-content-end mt-3">
                            <button type="button" class="btn btn-primary rounded-0 savetaxationFormInfo mt-2" >Save</button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

<script>

$('.savefinancialInfo').on('click', function() {
    ldrshow();
    $('.error').remove();
    
    var formData = new FormData($('#financialInfo')[0]);
    
    $.ajax({
        url: '{{route("decverifier.saveFinancialReview", [base64_encode($hospital->id), base64_encode($hospital->uuid)])}}',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            ldrhide();
            successMessage(response.message);
            if(response.isComplete) {
                CheckFinanceCompleteOrNot(8, true);
            } else {
                CheckFinanceCompleteOrNot(7, false)
            }
        },
        error: function (xhr) {
            ldrhide();
            $('.error').remove();
            
            if (xhr.status === 422) { 
            let errors = xhr.responseJSON.errors;
            for (let field in errors) {
                $(`[name="${field}"]`).closest('.financeerror').after(`<div class="error text-danger">${errors[field][0]}</div>`);
            }
            } else {
                errorMessage('Something went wrong. Please try again later.');
            }
        }
    });    
});

$('.savetaxationFormInfo').on('click', function() {
    ldrshow();
    $('.error').remove();
    
    var formData = new FormData($('#taxationForm')[0]);
    
    $.ajax({
        url: '{{route("decverifier.saveTaxdetailsReview", [base64_encode($hospital->id), base64_encode($hospital->uuid)])}}',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            ldrhide();
            successMessage(response.message);
            if(response.isComplete) {
                CheckFinanceCompleteOrNot(8, true);
            } else {
                CheckFinanceCompleteOrNot(7, false)
            }
        },
        error: function (xhr) {
            ldrhide();
            $('.error').remove();
            
            if (xhr.status === 422) { 
            let errors = xhr.responseJSON.errors;
            for (let field in errors) {
                $(`[name="${field}"]`).closest('.taxationerror').after(`<div class="error text-danger">${errors[field][0]}</div>`);
            }
            } else {
                errorMessage('Something went wrong. Please try again later.');
            }
        }
    });    
});

function CheckFinanceCompleteOrNot(step, isLoad = 0) {
    $('.nav-link').removeClass('active');
    $('.tab-pane').removeClass('show active');
    $(`.step${step}`).addClass('show active');
    $(`.navstep${step}`).addClass('active');
    setTimeout(() => {
        $(`.step${step}`).on('click', function(event) {
            if (event.target.closest('.nav-item .active')) {
                setSlider(event.target.closest('.nav-item'));
            }
        });
        $(`.step${(step-1)}Icon`).show();
        if(isLoad) {
            $(`.step${step}Icon`).hide();
            loadStep(step);
        }
    }, 1000);
}

</script>