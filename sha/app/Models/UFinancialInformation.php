<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UFinancialInformation extends Model
{
    protected $fillable = ['hospital_id','uuid','account_holder','account_no','ifsc_code','bank_name','bank_branch_name','bank_address','micr','account_type','authorised_signatory_name','bank_email','neft_enabled','bsr_code','cancelled_cheque', 'dec_verify_status', 'dec_verify_remark', 'dec_verify_id', 'dec_status', 'dec_remark', 'dec_id', 'sec_status', 'sec_remark', 'sec_id', 'main_hospitalid', 'old_id'];
}
