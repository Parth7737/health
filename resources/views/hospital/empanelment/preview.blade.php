<div class="card p-0 shadow-none rounded-0  border-bottom">
    <h5 class="theme-color mt-3">Scheme Details</h5>
    <div class="row">
        <div class="col-md-2">
            <div class="infodata">
                <label><strong>Scheme:</strong></label>
                <p>{{ @$hospital->schemeType->name??'' }}</p>
            </div>
        </div>        
    </div>
</div>

<div class="card p-0 shadow-none rounded-0  border-bottom">
    <h5 class="theme-color mt-3">Specilities</h5>
    @if(sizeof($hospital->specialities) > 0)
    <div class="row">
        <div class="col-md-12">
            <div class="infodata">
                <div class="table-responsive mt-5 text-nowrap">
                    <table class="table">
                        <thead class="table-dark">
                            <tr>
                                <th>Sr No.</th>
                                <th>Name</th>
                                <th>Avaiable</th>
                                <th>Offered</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>  
                        @foreach(@$hospital->specialities as $key => $value)
                            <tr>
                                <td>
                                    {{$key + 1}}
                                </td>
                                <td>{{@$value->speciality->name}}</td>  
                                <td>{{$value->available?'Yes':'No'}}</td>  
                                <td>{{$value->offered?'Yes':'No'}}</td>  
                                <td>{{$value->not_offered_reason?$value->not_offered_reason:'N/A'}}</td>  
                            </tr> 
                        @endforeach                  
                        </tbody>
                    </table>
                </div>
            </div>
        </div>        
    </div>
    @endif
</div>


@if(sizeof($hospital->services) > 0)
<div class="card p-0 shadow-none rounded-0  border-bottom">
    <h5 class="theme-color mt-3">General Services</h5>
    <div class="row">
        <div class="col-md-12">
            <div class="infodata">
                @php 
                    $services = App\CentralLogics\Helpers::getCommanData('Service'); 
                @endphp
                @foreach($services as $key => $value) 
                    @if(sizeof($value->subServices) > 0)
                    <label class="theme-color"><strong>{{@$value->name}}:</strong></label>
                    <div class="table-responsive mt-5 text-nowrap">
                        <table class="table">
                            <thead class="table-dark">
                                <tr>
                                    <th>Sr No.</th>
                                    <th>Name</th>
                                    <th style="width: 45%">Value</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>  
                            @foreach($value->subServices as $k => $v)
                                @php
                                    $existData = App\CentralLogics\Helpers::getSingleServices($hospital->id, $value->id, $v->id);
                                @endphp     
                                @if($existData)
                                <tr>
                                    <td>
                                        {{$k+1}}
                                    </td>
                                    <td>{{$v->name}}</td>  
                                    <td>
                                        <div class="infodata">
                                        @if(@$existData->text_value && @$existData->text_value != "" && @$existData->service_value == 1)
                                            Yes
                                        @elseif(@$existData->text_value && @$existData->text_value != "" && @$existData->service_value == 0)
                                            No
                                        @else
                                            {{@$existData->service_value}}
                                        @endif   
                                        @if(@$existData->text_value && @$existData->text_value != "")
                                            (<b>{{@$existData->text_value }}</b>)
                                        @endif  
                                        @if(@$existData && @$existData->image)                                            
                                            <a href="{{ asset('public/storage/'.@$existData->image) }}" target="_blank" class="btn btn-outline-primary btn-sm">View</a></label>                                            
                                        @endif  
                                        </div>                                 
                                    </td>
                                    <td>
                                        {{@$existData->remark}}
                                    </td>
                                </tr> 
                                @endif
                            @endforeach                  
                            </tbody>
                        </table>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>        
    </div>
</div>
@endif

<div class="card p-0 shadow-none rounded-0  border-bottom">
    <h5 class="theme-color mt-3">Statutory Licenses</h5>
    <div class="row">
        @php 
            $Licenses = App\CentralLogics\Helpers::getCommanData('Licenses'); 
        @endphp
        @foreach($Licenses as $key => $value) 
            @if(sizeof($value->licenseType) > 0)
            <h6 class="theme-color mt-3">{{$value->name}}</h6>
            <div class="table-responsive mt-5 text-nowrap">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                        <th>Sr No.</th>
                        <th>Name</th>
                        <th>Valid Upto</th>
                        <th>Document</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($value->licenseType as $k => $v)
                            @php
                                $existData = App\CentralLogics\Helpers::getSingleLicense(@$hospital->id, $value->id, $v->id);
                            @endphp
                            @if(@$existData)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>
                                        <label>{{ @$existData->licenseType->name }}</label>
                                        <p><strong>{{ date('d/m/Y', strtotime(@$existData->issue_date)) }}</strong></p>
                                    </td>
                                    <td>
                                        {{ date('d/m/Y', strtotime(@$existData->expiry_date)) }}
                                    </td>
                                    <td>
                                        <a href="{{ asset('public/storage/'.@$existData->document) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        @endforeach
    </div>
</div>

<div class="card p-0 shadow-none rounded-0  border-bottom">
    <h5 class="theme-color mt-3">Human Resources</h5><hr>
    <div class="row">       
        <h5 class="theme-color mt-3">General Service Human Resource</h5>
        <div class="row">       
            <h6 class="theme-color mt-3">Head Of the Organization/CEO</h6>
            <div class="row">
                <div class="col-md-3">
                    <div class="infodata">
                        <label><strong>Name</strong></label>
                        <p>{{ @$hospital->ceo->name }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="infodata">
                        <label><strong>Designation</strong></label>
                        <p>{{ @$hospital->ceo->designation }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="infodata">
                        <label><strong>Email ID</strong></label>
                        <p>{{ @$hospital->ceo->email }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="infodata">
                        <label><strong>Mobile No</strong></label>
                        <p>{{ @$hospital->ceo->mobile_no }}</p>
                    </div>
                </div>
            </div>
            <hr>
            <h6 class="theme-color mt-3">Hospital Admin/Nodal Officer</h6>
            <div class="row">
                <div class="col-md-4">
                    <div class="infodata">
                        <label><strong>Name</strong></label>
                        <p>{{ auth()->user()->name }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="infodata">
                        <label><strong>Mobile No</strong></label>
                        <p>{{ auth()->user()->mobile_no }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="infodata">
                        <label><strong>Email ID</strong></label>
                        <p>{{ auth()->user()->email }}</p>
                    </div>
                </div>
            </div>
            <hr>
            <h6 class="theme-color mt-3">Medical Human Resource</h6>
            <div class="row">
                <div class="table-responsive mt-5 text-nowrap">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Sr No.</th>
                                <th>HPR</th>
                                <th>Registration Number</th>
                                <th>Type Of Human Resource</th>
                                <th>Sub Type Of Human Resource</th>
                                <th>Name</th>
                                <th>Mobile Number</th>
                                <th>Email</th>
                                <th>Regi. Certificate</th>
                                <th>Declaration Certificate</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0 procedure-body">
                            @foreach($hospital->humanResources()->where('type', 'mhr')->get() as $key => $value)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{@$value->healthcare_proffessionals_registry_id}}</td>
                                    <td>{{@$value->registration_number}}</td>
                                    <td>{{@$value->type_of_human_resource}}</td>
                                    <td>{{@$value->humanResource->name}}</td>
                                    <td>{{@$value->name}}</td>
                                    <td>{{@$value->mobile_no}}</td>
                                    <td>{{@$value->email}}</td>
                                    <td>
                                        @if(@$value->registration_certificate)
                                            <a href="{{ asset('public/storage/'.@$value->registration_certificate) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a>
                                        @endif
                                    </td>
                                    <td>
                                        @if(@$value->declaration_certificate)
                                            <a href="{{ asset('public/storage/'.@$value->declaration_certificate) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <hr>
            <h6 class="theme-color mt-3">Non Medical Human Resource</h6>
            <div class="row">
                <div class="table-responsive mt-5 text-nowrap">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Sr No.</th>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0 procedure-body">
                            <tr>
                                <td>1</td>
                                <td>Medico</td>
                                <td>{{@$hospital->medico_count}}</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>House Keeping</td>
                                <td>{{@$hospital->house_keeping}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <hr>
        <h5 class="theme-color mt-3">Support Service Human Resource</h5>
        <div class="row"> 
            <div class="table-responsive mt-5 text-nowrap">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Sr No.</th>
                            <th>HPR</th>
                            <th>Registration Number</th>
                            <th>Type Of Human Resource</th>
                            <th>Sub Type Of Human Resource</th>
                            <th>Name</th>
                            <th>Mobile Number</th>
                            <th>Email</th>
                            <th>Regi. Certificate</th>
                            <th>Declaration Certificate</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0 procedure-body">
                        @foreach($hospital->humanResources()->where('type', 'sshr')->get() as $key => $value)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{@$value->healthcare_proffessionals_registry_id}}</td>
                                <td>{{@$value->registration_number}}</td>
                                <td>{{@$value->type_of_human_resource}}</td>
                                <td>{{@$value->humanResource->name}}</td>
                                <td>{{@$value->name}}</td>
                                <td>{{@$value->mobile_no}}</td>
                                <td>{{@$value->email}}</td>
                                <td>
                                    @if(@$value->registration_certificate)
                                        <a href="{{ asset('public/storage/'.@$value->registration_certificate) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a>
                                    @endif
                                </td>
                                <td>
                                    @if(@$value->declaration_certificate)
                                        <a href="{{ asset('public/storage/'.@$value->declaration_certificate) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <h5 class="theme-color mt-3">Specialist</h5>
        <div class="row"> 
            <div class="table-responsive mt-5 text-nowrap">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Sr No.</th>
                            <th>HPR</th>
                            <th>Registration Number</th>
                            <th>Type of Human Resource</th>
                            <th>Employment Type</th>
                            <th>Name</th>
                            <th>Mobile Number</th>
                            <th>Email</th>
                            <th>Specialization</th>
                            <th>Regi. Certificate</th>
                            <th>Registration Certificate Expiry</th>
                            <th>Declaration Certificate</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0 procedure-body">
                        @foreach($hospital->hospitalTeam as $key => $value)
                            <tr id="hrrow{{$value->id}}">
                                <td>{{$loop->iteration}}</td>
                                <td>{{@$value->hpr_id}}</td>
                                <td>{{@$value->registration_no}}</td>
                                <td>{{@$value->designation}}</td>
                                <td>{{@$value->employement_type}}</td>
                                <td>{{@$value->name}}</td>
                                <td>{{@$value->mobile}}</td>
                                <td>{{@$value->email}}</td>
                                <td>{{@$value->speciality->name}}</td>
                                <td>
                                    @if(@$value->registration_certificate)
                                        <a href="{{ asset('public/storage/'.@$value->registration_certificate) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a>
                                    @endif
                                </td>
                                <td>{{@$value->registration_certificate_expiry}}</td>
                                <td>
                                    @if(@$value->declaration_certificate)
                                        <a href="{{ asset('public/storage/'.@$value->declaration_certificate) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card p-0 shadow-none rounded-0  border-bottom">
    <h5 class="theme-color mt-3">Quality & Accreditation</h5><hr>
    <div class="row">
        <h5 class="theme-color mt-3">Accreditation</h5>
        <div class="row"> 
            <div class="table-responsive mt-5 text-nowrap">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Sr No.</th>
                            <th>Accreditation Name</th>
                            <th>Valid From</th>
                            <th>Valid Till</th>
                            <th>Certificate Number</th>
                            <th>Certificate</th>
                            <th>Specialization</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0 procedure-body">
                        @if(@$hospital->hospitalAccreditation)
                        <tr>
                            <td>1</td>
                            <td>{{@$hospital->hospitalAccreditation->accred->name}}</td>
                            <td>{{@$hospital->hospitalAccreditation->valid_from}}</td>
                            <td>{{@$hospital->hospitalAccreditation->valid_till}}</td>
                            <td>{{@$hospital->hospitalAccreditation->certificate_no}}</td>
                            <td><a href="{{ asset('public/storage/'.@$hospital->hospitalAccreditation->certificate) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a></td>
                            <td>
                                @php
                                    $selected_ids = optional($hospital->hospitalAccreditation)->speciality_ids;
                                    $selected_ids = $selected_ids ? json_decode($selected_ids, true) : [];
                                    if (!empty($selected_ids)) {
                                        $specialities = App\Models\HospitalSpeciality::join('specialities', 'hospital_specialities.speciality_id', '=', 'specialities.id')
                                                    ->whereIn('hospital_specialities.id', $selected_ids)
                                                    ->pluck('specialities.name')
                                                    ->toArray();
                                        echo implode(', ', $specialities);
                                    }
                                @endphp
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
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
                    @if(@$hospital->financialInformation->cancelled_cheque)
                        <a href="{{ asset('public/storage/'.@$hospital->financialInformation->cancelled_cheque) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Cancelled Cheque</a></label>
                    @endif
                </div>
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
            @if(@$hospital->taxDetails->tds_exemption == 'Yes')
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
            @endif
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
        </div>
    </div>
</div>
@php $documents = App\CentralLogics\Helpers::getCommanData('EmpanelmentDocument'); @endphp
@if(sizeof($documents) > 0)
<div class="card p-0 shadow-none rounded-0  border-bottom">
    <h5 class="theme-color mt-3">Documents</h5><hr>
    <div class="row">
        <div class="table-responsive mt-5 text-nowrap">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Sr No.</th>
                        <th>Name</th>
                        <th>Attachment</th>
                    </tr>
                <thead>
                <tbody>
                    @foreach(@$hospital->documents as $key => $value)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{@$value->doc->name}}</td>
                            <td><a href="{{ asset('public/storage/'.@$value->document) }}" target="_blank">Preview <i class="tf-icons ri-eye-fill"></i></a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<div class="card p-0 shadow-none rounded-0  border-bottom ">
    <h5 class="theme-color mt-3">Hospital Gallery</h5>
    <div class="row mb-2 ">
        @if(@$hospital->image)
            <div class="col-md-1">
                <div class="infodata">
                    <a href="{{ asset('public/storage/'.@$hospital->image) }}" target="_blank" class="btn btn-outline-primary btn-sm">View</a></label>
                </div>
            </div>
        @endif   
        @foreach(@$hospital->images as $key => $value)
            <div class="col-md-1">
                <div class="infodata">
                    <a href="{{ asset('public/storage/'.@$value->image) }}" target="_blank" class="btn btn-outline-primary btn-sm">View</a></label>
                </div>
            </div>
        @endforeach 
        @if(@$hospital->hospital_ppt)
            <div class="col-md-1">
                <div class="infodata">
                    <a href="{{ asset('public/storage/'.@$hospital->hospital_ppt) }}" target="_blank" class="btn btn-outline-primary btn-sm">Hospital PPT</a></label>
                </div>
            </div>
        @endif    
    </div>
</div>