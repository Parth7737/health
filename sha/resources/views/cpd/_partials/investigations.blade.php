@php 
    $cstatus = json_decode($preauth_register->cex_status, true);
    $status = json_decode($preauth_register->cpd_status, true);
@endphp
<style>
    .violate {
        color: #757bff;
    }
</style>
<div class="row">
    <div class="col-md-3">
        <table class="table">
                <tr style="text-decoration: none; display: -webkit-inline-box">
                    <td style="padding: 0;">CEX|</td>
                    <td style="padding: 0;">CPD</td>
                    <td style="padding:0;"></td>
                </tr>
            @if($preauth_register->hospital_declaration_form)
                <tr class="" style="text-decoration: none; display: -webkit-inline-box">
                    <td class="cexstatushospital_declaration_form" style="padding: 0;">@if(@$cstatus['hospital_declaration_form'] != "") @if(@$cstatus['preauth_querhospital_declaration_formy_add_doc'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else ...  @endif</td>
                    <td class="statushospital_declaration_form">@if(@$status['hospital_declaration_form'] != "") @if(@$status['hospital_declaration_form'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i>  @endif</td>
                    <td style="padding:0;">
                        <a href="javascript:;" onclick="clickondocument('', 'hospital_declaration_form');" class=" document-hospital_declaration_form">Born Baby Birth Certificate
                        </a>
                    </td>
                </tr>
            @endif
            @if($preauth_register->born_baby_birth_certificate)
                <tr class="" style="text-decoration: none; display: -webkit-inline-box">
                    <td class="cexstatusborn_baby_birth_certificate" style="padding: 0;">@if(@$cstatus['born_baby_birth_certificate'] != "") @if(@$cstatus['preauth_querborn_baby_birth_certificatey_add_doc'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else ...  @endif</td>
                    <td class="statusborn_baby_birth_certificate">@if(@$status['born_baby_birth_certificate'] != "") @if(@$status['born_baby_birth_certificate'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i>  @endif</td>
                    <td style="padding:0;">
                        <a href="javascript:;" onclick="clickondocument('', 'born_baby_birth_certificate');" class=" document-born_baby_birth_certificate">Born Baby Birth Certificate
                        </a>
                    </td>
                </tr>
            @endif
            @foreach($preauth_register->investigations as $key => $value)
                <tr class="" style="text-decoration: none; display: -webkit-inline-box">
                    <td class="cexstatus{{$value->id}}" style="padding: 0;">@if($value->cex_status != "") @if($value->cex_status == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else .. @endif</td>
                    <td class="status{{$value->id}}" >
                        @if($value->cpd_status != "") @if($value->cpd_status == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i> @endif
                    </td>
                    <td style="padding: 0;">
                        <a href="javascript:;" onclick="clickondocument('{{$value->id}}');" class=" document{{$key}} doc-{{$value->id}}">{{@$value->investigation->name}}
                        </a>
                    </td>
                </tr>
            @endforeach
            @if($preauth_register->preauth_query_supporting_doc)
                <tr class="" style="text-decoration: none; display: -webkit-inline-box">
                    <td class="cexstatuspreauth_query_supporting_doc" style="padding: 0;">@if(@$cstatus['preauth_query_supporting_doc'] != "") @if(@$cstatus['preauth_query_supporting_doc'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else ...  @endif</td>
                    <td class="statuspreauth_query_supporting_doc">@if(@$status['preauth_query_supporting_doc'] != "") @if(@$status['preauth_query_supporting_doc'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i>  @endif</td>
                    <td style="padding:0;">
                        <a href="javascript:;" onclick="clickondocument('', 'preauth_query_supporting_doc');" class=" document-preauth_query_supporting_doc">Preauth Query Supporting Document
                        </a>
                    </td>
                </tr>
            @endif
            @if($preauth_register->preauth_query_add_doc)
                <tr class="" style="text-decoration: none; display: -webkit-inline-box">
                    <td class="cexstatuspreauth_query_add_doc" style="padding: 0;">@if(@$cstatus['preauth_query_add_doc'] != "") @if(@$cstatus['preauth_query_add_doc'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else ...  @endif</td>
                    <td class="statuspreauth_query_add_doc">@if(@$status['preauth_query_add_doc'] != "") @if(@$status['preauth_query_add_doc'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i>  @endif</td>
                    <td style="padding:0;">
                        <a href="javascript:;" onclick="clickondocument('', 'preauth_query_add_doc');" class=" document-preauth_query_add_doc">Preauth Query Supporting Other Document
                        </a>
                    </td>
                </tr>
            @endif
            @if($preauth_register->committee_query_supporting_doc)
                <tr class="" style="text-decoration: none; display: -webkit-inline-box">
                    <td class="cexstatuscommittee_query_supporting_doc" style="padding: 0;">@if(@$cstatus['committee_query_supporting_doc'] != "") @if(@$cstatus['committee_query_supporting_doc'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else ...  @endif</td>
                    <td class="statuscommittee_query_supporting_doc">@if(@$status['committee_query_supporting_doc'] != "") @if(@$status['committee_query_supporting_doc'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i>  @endif</td>
                    <td style="padding:0;">
                        <a href="javascript:;" onclick="clickondocument('', 'committee_query_supporting_doc');" class=" document-committee_query_supporting_doc">Preauth Committee Query Supporting Document
                        </a>
                    </td>
                </tr>
            @endif
            @if($preauth_register->ceo_query_supporting_doc)
                <tr class="" style="text-decoration: none; display: -webkit-inline-box">
                    <td class="cexstatusceo_query_supporting_doc" style="padding: 0;">@if(@$cstatus['ceo_query_supporting_doc'] != "") @if(@$cstatus['ceo_query_supporting_doc'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else ...  @endif</td>
                    <td class="statusceo_query_supporting_doc">@if(@$status['ceo_query_supporting_doc'] != "") @if(@$status['ceo_query_supporting_doc'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i>  @endif</td>
                    <td style="padding:0;">
                        <a href="javascript:;" onclick="clickondocument('', 'ceo_query_supporting_doc');" class=" document-ceo_query_supporting_doc">Preauth CEO Query Supporting Document
                        </a>
                    </td>
                </tr>
            @endif
            @if($preauth_register->acs_query_supporting_doc)
                <tr class="" style="text-decoration: none; display: -webkit-inline-box">
                    <td class="cexstatusacs_query_supporting_doc" style="padding: 0;">@if(@$cstatus['acs_query_supporting_doc'] != "") @if(@$cstatus['ceo_query_supporting_doc'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else ...  @endif</td>
                    <td class="statusacs_query_supporting_doc">@if(@$status['acs_query_supporting_doc'] != "") @if(@$status['acs_query_supporting_doc'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i>  @endif</td>
                    <td style="padding:0;">
                        <a href="javascript:;" onclick="clickondocument('', 'acs_query_supporting_doc');" class=" document-acs_query_supporting_doc">Preauth ACS/Chairman Query Supporting Document
                        </a>
                    </td>
                </tr>
            @endif
            @foreach($preauth_register->enhancement_docs as $key => $value)
                <tr class="" style="text-decoration: none; display: -webkit-inline-box">
                    <td class="enhancement-cexstatus{{$value->id}}" style="padding: 0;">@if($value->cex_status != "") @if($value->cex_status == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else .. @endif</td>
                    <td class="enhancement-status{{$value->id}}" >
                        @if($value->cpd_status != "") @if($value->cpd_status == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i> @endif
                    </td>
                    <td style="padding: 0;">
                        <a href="javascript:;" onclick="clickondocument('{{$value->id}}','enhancement');" class="enhancement-document{{$key}} enhancement--document{{$value->id}}">{{@$value->name}}
                        </a>
                    </td>
                </tr>
            @endforeach
            @if($preauth_register->claim_query_supporting_doc)
                <tr class="" style="text-decoration: none; display: -webkit-inline-box">
                    <td class="cexstatusclaim_query_supporting_doc" style="padding: 0;">@if(@$cstatus['claim_query_supporting_doc'] != "") @if(@$cstatus['claim_query_supporting_doc'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else ...  @endif</td>
                    <td class="statusclaim_query_supporting_doc">@if(@$status['claim_query_supporting_doc'] != "") @if(@$status['claim_query_supporting_doc'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i>  @endif</td>
                    <td style="padding:0;">
                        <a href="javascript:;" onclick="clickondocument('', 'claim_query_supporting_doc');" class=" document-claim_query_supporting_doc">Claim Query Supporting Document
                        </a>
                    </td>
                </tr>
            @endif
            @if($preauth_register->claim_query_add_doc)
                <tr class="" style="text-decoration: none; display: -webkit-inline-box">
                    <td class="cexstatusclaim_query_add_doc" style="padding: 0;">@if(@$cstatus['claim_query_add_doc'] != "") @if(@$cstatus['claim_query_add_doc'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else ...  @endif</td>
                    <td class="statusclaim_query_add_doc">@if(@$status['claim_query_add_doc'] != "") @if(@$status['claim_query_add_doc'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i>  @endif</td>
                    <td style="padding:0;">
                        <a href="javascript:;" onclick="clickondocument('', 'claim_query_add_doc');" class=" document-claim_query_add_doc">Claim Query Supporting Other Document
                        </a>
                    </td>
                </tr>
            @endif
            @if($preauth_register->death_certificate)
                <tr class="" style="text-decoration: none; display: -webkit-inline-box">
                    <td class="cexstatusdeath_certificate" style="padding: 0;">@if(@$cstatus['death_certificate'] != "") @if(@$cstatus['death_certificate'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else ...  @endif</td>

                    <td class="statusdeath_certificate">@if(@$status['death_certificate'] != "") @if(@$status['death_certificate'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i>  @endif</td>

                    <td style="padding:0;">
                        <a href="javascript:;" onclick="clickondocument('', 'death_certificate');" class=" documentt document-death_summary">Death Certificate
                        </a>
                    </td>
                </tr>
            @endif

            @if($preauth_register->death_summary)
                <tr class="" style="text-decoration: none; display: -webkit-inline-box">
                    <td class="cexstatusdeath_summary" style="padding: 0;">@if(@$cstatus['death_summary'] != "") @if(@$cstatus['death_summary'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else ...  @endif</td>

                    <td class="statusdeath_summary">@if(@$status['death_summary'] != "") @if(@$status['death_summary'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i>  @endif</td>

                    <td style="padding:0;">
                        <a href="javascript:;" onclick="clickondocument('', 'death_summary');" class=" documentt document-death_summary">Death Summary
                        </a>
                    </td>
                </tr>
            @endif

            @if($preauth_register->mortality_audit_report)
                <tr class="" style="text-decoration: none; display: -webkit-inline-box">
                    <td class="cexstatusmortality_audit_report" style="padding: 0;">@if(@$cstatus['mortality_audit_report'] != "") @if(@$cstatus['mortality_audit_report'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else ...  @endif</td>

                    <td class="statusmortality_audit_report">@if(@$status['mortality_audit_report'] != "") @if(@$status['mortality_audit_report'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i>  @endif</td>

                    <td style="padding:0;">
                        <a href="javascript:;" onclick="clickondocument('', 'mortality_audit_report');" class=" documentt document-mortality_audit_report">Mortality Audit Report
                        </a>
                    </td>
                </tr>
            @endif

            @if($preauth_register->in_treatment_photo)
                <tr class="" style="text-decoration: none; display: -webkit-inline-box">
                    <td class="cexstatusin_treatment_photo" style="padding: 0;">@if(@$cstatus['in_treatment_photo'] != "") @if(@$cstatus['in_treatment_photo'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else ...  @endif</td>

                    <td class="statusin_treatment_photo">@if(@$status['in_treatment_photo'] != "") @if(@$status['in_treatment_photo'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i>  @endif</td>

                    <td style="padding:0;">
                        <a href="javascript:;" onclick="clickondocument('', 'in_treatment_photo');" class=" documentt document-in_treatment_photo">In Treatment Photo
                        </a>
                    </td>
                </tr>
            @endif

            @if($preauth_register->post_surgery_photo)
                <tr class="" style="text-decoration: none; display: -webkit-inline-box">
                    <td class="cexstatuspost_surgery_photo" style="padding: 0;">@if(@$cstatus['post_surgery_photo'] != "") @if(@$cstatus['post_surgery_photo'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else ...  @endif</td>

                    <td class="statuspost_surgery_photo">@if(@$status['post_surgery_photo'] != "") @if(@$status['post_surgery_photo'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i>  @endif</td>

                    <td style="padding:0;">
                        <a href="javascript:;" onclick="clickondocument('', 'post_surgery_photo');" class=" documentt document-post_surgery_photo">Post Surgery Photo
                        </a>
                    </td>
                </tr>
            @endif

            @if($preauth_register->discharge_summary)
                <tr class="" style="text-decoration: none; display: -webkit-inline-box">
                    <td class="cexstatusdischarge_summary" style="padding: 0;">@if(@$cstatus['discharge_summary'] != "") @if(@$cstatus['discharge_summary'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else ...  @endif</td>

                    <td class="statusdischarge_summary">@if(@$status['discharge_summary'] != "") @if(@$status['discharge_summary'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i>  @endif</td>

                    <td style="padding:0;">
                        <a href="javascript:;" onclick="clickondocument('', 'discharge_summary');" class=" documentt document-discharge_summary">Discharge Summary
                        </a>
                    </td>
                </tr>
            @endif

            @if($preauth_register->feedback_form)
                <tr class="" style="text-decoration: none; display: -webkit-inline-box">
                    <td class="cexstatusfeedback_form" style="padding: 0;">@if(@$cstatus['feedback_form'] != "") @if(@$cstatus['feedback_form'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else ...  @endif</td>

                    <td class="statusfeedback_form">@if(@$status['feedback_form'] != "") @if(@$status['feedback_form'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i>  @endif</td>
                    <td style="padding:0;">
                        <a href="javascript:;" onclick="clickondocument('', 'feedback_form');" class=" documentt document-feedback_form">Feedback Form
                        </a>
                    </td>
                </tr>
            @endif
            @if($preauth_register->beneficiary_verification_form)
                <tr class="" style="text-decoration: none; display: -webkit-inline-box">
                    <td class="cexstatusbeneficiary_verification_form" style="padding: 0;">@if(@$cstatus['beneficiary_verification_form'] != "") @if(@$cstatus['beneficiary_verification_form'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else ...  @endif</td>

                    <td class="statusbeneficiary_verification_form">@if(@$status['beneficiary_verification_form'] != "") @if(@$status['beneficiary_verification_form'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i>  @endif</td>
                    <td style="padding:0;">
                        <a href="javascript:;" onclick="clickondocument('', 'beneficiary_verification_form');" class=" documentt document-beneficiary_verification_form">Beneficiary Verification Form
                        </a>
                    </td>
                </tr>
            @endif
            @if($preauth_register->hospital_certificate)
                <tr class="" style="text-decoration: none; display: -webkit-inline-box">
                    <td class="cexstatushospital_certificate" style="padding: 0;">@if(@$cstatus['hospital_certificate'] != "") @if(@$cstatus['hospital_certificate'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else ...  @endif</td>

                    <td class="statushospital_certificate">@if(@$status['hospital_certificate'] != "") @if(@$status['hospital_certificate'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i>  @endif</td>
                    <td style="padding:0;">
                        <a href="javascript:;" onclick="clickondocument('', 'hospital_certificate');" class=" documentt document-hospital_certificate">Hospital Certificate
                        </a>
                    </td>
                </tr>
            @endif

            @if($preauth_register->hospital_bill)
                <tr class="" style="text-decoration: none; display: -webkit-inline-box">
                    <td class="cexstatushospital_bill" style="padding: 0;">@if(@$cstatus['hospital_bill'] != "") @if(@$cstatus['hospital_bill'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else ...  @endif</td>

                    <td class="statushospital_bill">@if(@$status['hospital_bill'] != "") @if(@$status['hospital_bill'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i>  @endif</td>

                    <td style="padding:0;">
                        <a href="javascript:;" onclick="clickondocument('', 'hospital_bill');" class=" documentt document-hospital_bill">Hospital Bill
                        </a>
                    </td>
                </tr>
            @endif

            @if($preauth_register->claim_other_doc)
                <tr class="" style="text-decoration: none; display: -webkit-inline-box">
                    <td class="cexstatusclaim_other_doc" style="padding: 0;">@if(@$cstatus['claim_other_doc'] != "") @if(@$cstatus['claim_other_doc'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else ...  @endif</td>

                    <td class="statusclaim_other_doc">@if(@$status['claim_other_doc'] != "") @if(@$status['claim_other_doc'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i>  @endif</td>

                    <td style="padding:0;">
                        <a href="javascript:;" onclick="clickondocument('', 'claim_other_doc');" class=" documentt document-claim_other_doc">Other Document
                        </a>
                    </td>
                </tr>
            @endif

            @foreach($preauth_register->claim_investigations as $key => $value)
                <tr class="" style="text-decoration: none; display: -webkit-inline-box">
                    <td class="claimstatus{{$value->id}}" style="padding: 0;">@if(@$value->cex_status != "") @if(@$value->cex_status == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else ...  @endif</td>
                    <td class="status{{$value->id}}claim">
                        @if($value->cpd_status != "") @if($value->cpd_status == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i>  @endif
                    </td>
                    <td style="padding:0;">
                        <a href="javascript:;" onclick="clickondocument('{{$value->id}}', 'claim');" class=" documentt{{$key}} claim-{{$value->id}}">{{$value->investigation->name}}
                        </a>
                    </td>
                </tr>
            @endforeach
            @if($preauth_register->erroneous_raise_supporting_doc)
                <tr class="" style="text-decoration: none; display: -webkit-inline-box">
                    <td class="cexstatuserroneous_raise_supporting_doc" style="padding: 0;">@if(@$cstatus['erroneous_raise_supporting_doc'] != "") @if(@$cstatus['erroneous_raise_supporting_doc'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else ...  @endif</td>
                    <td class="statuserroneous_raise_supporting_doc">@if(@$status['erroneous_raise_supporting_doc'] != "") @if(@$status['erroneous_raise_supporting_doc'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i>  @endif</td>
                    <td style="padding:0;">
                        <a href="javascript:;" onclick="clickondocument('', 'erroneous_raise_supporting_doc');" class=" document-erroneous_raise_supporting_doc">Erroneous Claim Supporting Document
                        </a>
                    </td>
                </tr>
            @endif
            @if($preauth_register->erroneous_query_supporting_doc)
                <tr class="" style="text-decoration: none; display: -webkit-inline-box">
                    <td class="cexstatuserroneous_query_supporting_doc" style="padding: 0;">@if(@$cstatus['erroneous_query_supporting_doc'] != "") @if(@$cstatus['erroneous_query_supporting_doc'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else ...  @endif</td>
                    <td class="statuserroneous_query_supporting_doc">@if(@$status['erroneous_query_supporting_doc'] != "") @if(@$status['erroneous_query_supporting_doc'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i>  @endif</td>
                    <td style="padding:0;">
                        <a href="javascript:;" onclick="clickondocument('', 'erroneous_query_supporting_doc');" class=" document-erroneous_query_supporting_doc">Erroneous Query Supporting Document
                        </a>
                    </td>
                </tr>
            @endif
        </table>
    </div>
    <div class="col-md-9 border border-primary mainpdfview">
        
    </div>
</div>


<script>


    var documentsQueue = [];
    var currentDocIndex = 0;

    @php
        $index = 0;
    @endphp

    @if($preauth_register->hospital_declaration_form)
        documentsQueue.push({
            id: '{{$preauth_register->id}}',
            type: 'hospital_declaration_form',
            status: '{{ @$status["hospital_declaration_form"] ?? "" }}',
            index: {{ $index++ }}
        });
    @endif

    @if($preauth_register->born_baby_birth_certificate)
        documentsQueue.push({
            id: '{{$preauth_register->id}}',
            type: 'born_baby_birth_certificate',
            status: '{{ @$status["born_baby_birth_certificate"] ?? "" }}',
            index: {{ $index++ }}
        });
    @endif

    @foreach($preauth_register->investigations as $key => $value)
        documentsQueue.push({
            id: '{{$value->id}}',
            type: '',
            status: '{{ $value->cpd_status }}',
            index: {{ $index++ }}
        });
    @endforeach

    @if($preauth_register->preauth_query_supporting_doc)
        documentsQueue.push({
            id: '{{$preauth_register->id}}',
            type: 'preauth_query_supporting_doc',
            status: '{{ @$status["preauth_query_supporting_doc"] ?? "" }}',
            index: {{ $index++ }}
        });
    @endif

    @if($preauth_register->committee_query_supporting_doc)
        documentsQueue.push({
            id: '{{$preauth_register->id}}',
            type: 'committee_query_supporting_doc',
            status: '{{ @$status["committee_query_supporting_doc"] ?? "" }}',
            index: {{ $index++ }}
        });
    @endif

    @if($preauth_register->ceo_query_supporting_doc)
        documentsQueue.push({
            id: '{{$preauth_register->id}}',
            type: 'ceo_query_supporting_doc',
            status: '{{ @$status["ceo_query_supporting_doc"] ?? "" }}',
            index: {{ $index++ }}
        });
    @endif

    @if($preauth_register->acs_query_supporting_doc)
        documentsQueue.push({
            id: '{{$preauth_register->id}}',
            type: 'acs_query_supporting_doc',
            status: '{{ @$status["acs_query_supporting_doc"] ?? "" }}',
            index: {{ $index++ }}
        });
    @endif

    @if($preauth_register->preauth_query_add_doc)
        documentsQueue.push({
            id: '{{$preauth_register->id}}',
            type: 'preauth_query_add_doc',
            status: '{{ @$status["preauth_query_add_doc"] ?? "" }}',
            index: {{ $index++ }}
        });
    @endif

    @foreach($preauth_register->enhancement_docs as $key => $value)
        documentsQueue.push({
            id: '{{$value->id}}',
            type: 'enhancement',
            status: '{{ $value->cpd_status }}',
            index: {{ $index++ }}
        });
    @endforeach

    @if($preauth_register->claim_query_supporting_doc)
        documentsQueue.push({
            id: '{{$preauth_register->id}}',
            type: 'claim_query_supporting_doc',
            status: '{{ @$status["claim_query_supporting_doc"] ?? "" }}',
            index: {{ $index++ }}
        });
    @endif
    @if($preauth_register->claim_query_add_doc)
        documentsQueue.push({
            id: '{{$preauth_register->id}}',
            type: 'claim_query_add_doc',
            status: '{{ @$status["claim_query_add_doc"] ?? "" }}',
            index: {{ $index++ }}
        });
    @endif
    @if($preauth_register->death_certificate)
        documentsQueue.push({
            id: '{{$preauth_register->id}}',
            type: 'death_certificate',
            status: '{{ @$status["death_certificate"] ?? "" }}',
            index: {{ $index++ }}
        });
    @endif

    @if($preauth_register->death_summary)
        documentsQueue.push({
            id: '{{$preauth_register->id}}',
            type: 'death_summary',
            status: '{{ @$status["death_summary"] ?? "" }}',
            index: {{ $index++ }}
        });
    @endif

    @if($preauth_register->feedback_form)
        documentsQueue.push({
            id: '{{$preauth_register->id}}',
            type: 'feedback_form',
            status: '{{ @$status["feedback_form"] ?? "" }}',
            index: {{ $index++ }}
        });
    @endif

    @if($preauth_register->beneficiary_verification_form)
        documentsQueue.push({
            id: '{{$preauth_register->id}}',
            type: 'beneficiary_verification_form',
            status: '{{ @$status["beneficiary_verification_form"] ?? "" }}',
            index: {{ $index++ }}
        });
    @endif
    @if($preauth_register->hospital_certificate)
        documentsQueue.push({
            id: '{{$preauth_register->id}}',
            type: 'hospital_certificate',
            status: '{{ @$status["hospital_certificate"] ?? "" }}',
            index: {{ $index++ }}
        });
    @endif
    
    @if($preauth_register->mortality_audit_report)
        documentsQueue.push({
            id: '{{$preauth_register->id}}',
            type: 'mortality_audit_report',
            status: '{{ @$status["mortality_audit_report"] ?? "" }}',
            index: {{ $index++ }}
        });
    @endif

    @if($preauth_register->in_treatment_photo)
        documentsQueue.push({
            id: '{{$preauth_register->id}}',
            type: 'in_treatment_photo',
            status: '{{ @$status["in_treatment_photo"] ?? "" }}',
            index: {{ $index++ }}
        });
    @endif

    @if($preauth_register->post_surgery_photo)
        documentsQueue.push({
            id: '{{$preauth_register->id}}',
            type: 'post_surgery_photo',
            status: '{{ @$status["post_surgery_photo"] ?? "" }}',
            index: {{ $index++ }}
        });
    @endif

    @if($preauth_register->discharge_summary)

        documentsQueue.push({
            id: '{{$preauth_register->id}}',
            type: 'discharge_summary',
            status: '{{ @$status["discharge_summary"] ?? "" }}',
            index: {{ $index++ }}
        });
    @endif

    @if($preauth_register->hospital_bill)
        documentsQueue.push({
            id: '{{$preauth_register->id}}',
            type: 'hospital_bill',
            status: '{{ @$status["hospital_bill"] ?? "" }}',
            index: {{ $index++ }}
        });
    @endif

    @if($preauth_register->claim_other_doc)
        documentsQueue.push({
            id: '{{$preauth_register->id}}',
            type: 'claim_other_doc',
            status: '{{ @$status["claim_other_doc"] ?? "" }}',
            index: {{ $index++ }}
        });
    @endif

    @foreach($preauth_register->claim_investigations as $key => $value)
        documentsQueue.push({
            id: '{{$value->id}}',
            type: 'claim',
            status: '{{ @$status["cpd_status"] ?? "" }}',
            index: {{ $index++ }}
        });
    @endforeach
    @if($preauth_register->erroneous_raise_supporting_doc)
        documentsQueue.push({
            id: '{{$preauth_register->id}}',
            type: 'erroneous_raise_supporting_doc',
            status: '{{ @$status["erroneous_raise_supporting_doc"] ?? "" }}',
            index: {{ $index++ }}
        });
    @endif
    @if($preauth_register->erroneous_query_supporting_doc)
        documentsQueue.push({
            id: '{{$preauth_register->id}}',
            type: 'erroneous_query_supporting_doc',
            status: '{{ @$status["erroneous_query_supporting_doc"] ?? "" }}',
            index: {{ $index++ }}
        });
    @endif

    // Automatically find the first unreviewed document
    function findFirstPendingIndex() {
        for(let i = 0; i < documentsQueue.length; i++) {
            if(documentsQueue[i].status === "" || documentsQueue[i].status === null || documentsQueue[i].status === "null") {
                return i;
            }
        }
        return -1; // all reviewed
    }

    function loadDocumentByIndex(index) {
        if(index >= 0 && index < documentsQueue.length) {
            currentDocIndex = index;
            let doc = documentsQueue[index];
            clickondocument(doc.id, doc.type);
        }
    }

    function nextDocument() {
        currentDocIndex++;
        if(currentDocIndex < documentsQueue.length) {
            loadDocumentByIndex(currentDocIndex);
        } else {
            successMessage("All documents reviewed.");
        }
    }

    function clickondocument(id = '', type = '') {
        $(".loader-overlay").show();

        $("a[class*='document-'], a[class*='doc-'], a[class*='claim-'], a[class*='enhancement--document']").removeClass("violate fw-bold");
        if (type === 'enhancement') {
            $(".enhancement--document" + id).addClass("violate fw-bold");
        } else if (type == 'claim') {
            $(".claim-" + id).addClass("violate fw-bold");
        } else if (type) {
            $(".document-" + type).addClass("violate fw-bold");
        } else {
            $(".doc-" + id).addClass("violate fw-bold");
        }
        $.ajax({
            url: '{{route("cpd.loadpdf", [$preauth_register_id])}}', 
            type: 'POST',
            data: {
                '_token': '{{csrf_token()}}',
                'id': id,
                'type': type
            },
            success: function (data) {
                $(".loader-overlay").hide();
                $(".mainpdfview").html(data.html || data);
            },
            error: function (xhr, status, error) {
                $(".loader-overlay").hide();
                errorMessage('Something went wrong. Please try again later.');
            }
        });
    }

    function verifyDocument(status, id, statustype = '') {
        $(".loader-overlay").show();
        $.ajax({
            url: '{{route("cpd.verifydocument", [$preauth_register_id])}}', 
            type: 'POST',
            data: {
                '_token': '{{csrf_token()}}',
                'id' : id,
                'status': status,
                'type' : statustype,
            },
            success: function (data) {
                $(".loader-overlay").hide();
                if(data.success) {
                    successMessage(data.message);
                    var classname = '';
                    if(statustype == '') {
                        var classname = 'status'+id;
                    } else if(statustype == 'claim') {
                        var classname = 'status'+id+'claim';
                    } else if(statustype == 'enhancement'){
                        var classname = 'enhancement-status'+id;
                    } else {
                        var classname = 'status'+statustype;
                    }
                    if(status == "Correct") {
                        $('.'+classname).html('<i class="ri-file-check-fill text-primary"></i>')
                    } else {
                        $('.'+classname).html('<i class="ri-file-close-fill text-danger"></i>')
                    }
                    
                    if (data.documentsinfo) {
                        $('.Documents').removeClass('pending-color').addClass('theme-color');
                    }
                    $('.documentdetails').html(data.html);
                    nextDocument();
                } else {
                    errorMessage(data.message);
                }               
            },
            error: function (xhr, status, error) {
                $(".loader-overlay").hide();
                errorMessage('Something went wrong. Please try again later.');
            }
        });
    }
</script>