@php 
    $status = json_decode($preauth_register->ppd_status, true);
@endphp
<div class="row">
    <div class="col-md-3">
        <table class="table">
            @if($preauth_register->hospital_declaration_form)
                <tr class="" style="text-decoration: none; display: -webkit-inline-box">
                    <td class="statushospital_declaration_form">@if(@$status['hospital_declaration_form'] != "") @if(@$status['hospital_declaration_form'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i>  @endif</td>
                    <td style="padding:0;">
                        <a href="javascript:;" onclick="clickondocument('', 'hospital_declaration_form');" class=" document-hospital_declaration_form">Hospital Declaration Form (During Admission)</a>
                    </td>
                </tr>
            @endif

            @if($preauth_register->born_baby_birth_certificate)
                <tr class="" style="text-decoration: none; display: -webkit-inline-box">
                    <td class="statusborn_baby_birth_certificate">@if(@$status['born_baby_birth_certificate'] != "") @if(@$status['born_baby_birth_certificate'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i>  @endif</td>
                    <td style="padding:0;">
                        <a href="javascript:;" onclick="clickondocument('', 'born_baby_birth_certificate');" class=" document-born_baby_birth_certificate">Born Baby Birth Certificate
                        </a>
                    </td>
                </tr>
            @endif
            @foreach($preauth_register->investigations as $key => $value)
                <tr class="" style="text-decoration: none; display: -webkit-inline-box">
                    <td class="status{{$value->id}}">@if($value->ppd_status != "") @if($value->ppd_status == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i>  @endif</td>
                    <td style="padding:0;">
                        <a href="javascript:;" onclick="clickondocument('{{$value->id}}');" class=" document{{$key}}">{{@$value->investigation->name}}
                        </a>
                    </td>
                </tr>
            @endforeach
            @if($preauth_register->preauth_query_supporting_doc)
                <tr class="" style="text-decoration: none; display: -webkit-inline-box">
                    <td class="statuspreauth_query_supporting_doc">@if(@$status['preauth_query_supporting_doc'] != "") @if(@$status['preauth_query_supporting_doc'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i>  @endif</td>
                    <td style="padding:0;">
                        <a href="javascript:;" onclick="clickondocument('', 'preauth_query_supporting_doc');" class=" document-preauth_query_supporting_doc">Preauth Query Supporting Document
                        </a>
                    </td>
                </tr>
            @endif
            @if($preauth_register->preauth_query_add_doc)
                <tr class="" style="text-decoration: none; display: -webkit-inline-box">
                    <td class="statuspreauth_query_add_doc">@if(@$status['preauth_query_add_doc'] != "") @if(@$status['preauth_query_add_doc'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i>  @endif</td>
                    <td style="padding:0;">
                        <a href="javascript:;" onclick="clickondocument('', 'preauth_query_add_doc');" class=" document-preauth_query_add_doc">Preauth Query Supporting Other Document
                        </a>
                    </td>
                </tr>
            @endif
            @if($preauth_register->committee_query_supporting_doc)
                <tr class="" style="text-decoration: none; display: -webkit-inline-box">
                    <td class="statuscommittee_query_supporting_doc">@if(@$status['committee_query_supporting_doc'] != "") @if(@$status['committee_query_supporting_doc'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i>  @endif</td>
                    <td style="padding:0;">
                        <a href="javascript:;" onclick="clickondocument('', 'committee_query_supporting_doc');" class=" document-committee_query_supporting_doc">Preauth Committee Query Supporting Document
                        </a>
                    </td>
                </tr>
            @endif
            @if($preauth_register->ceo_query_supporting_doc)
                <tr class="" style="text-decoration: none; display: -webkit-inline-box">
                    <td class="statusceo_query_supporting_doc">@if(@$status['ceo_query_supporting_doc'] != "") @if(@$status['ceo_query_supporting_doc'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i>  @endif</td>
                    <td style="padding:0;">
                        <a href="javascript:;" onclick="clickondocument('', 'ceo_query_supporting_doc');" class=" document-ceo_query_supporting_doc">Preauth CEO Query Supporting Document
                        </a>
                    </td>
                </tr>
            @endif
            @if($preauth_register->acs_query_supporting_doc)
                <tr class="" style="text-decoration: none; display: -webkit-inline-box">
                    <td class="statusacs_query_supporting_doc">@if(@$status['acs_query_supporting_doc'] != "") @if(@$status['acs_query_supporting_doc'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i>  @endif</td>
                    <td style="padding:0;">
                        <a href="javascript:;" onclick="clickondocument('', 'acs_query_supporting_doc');" class=" document-acs_query_supporting_doc">Preauth ACS/Chairman Query Supporting Document
                        </a>
                    </td>
                </tr>
            @endif
            @foreach($preauth_register->enhancement_docs as $key => $value)
                <tr class="" style="text-decoration: none; display: -webkit-inline-box">
                    <td class="enhancement-status{{$value->id}}">@if($value->ppd_status != "") @if($value->ppd_status == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i>  @endif</td>
                    <td style="padding:0;">
                        <a href="javascript:;" onclick="clickondocument('{{$value->id}}','enhancement');" class=" enhancement-document{{$key}}">{{$value->name}}
                        </a>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
    <div class="col-md-9 border border-primary mainpdfview">
        
    </div>
</div>


<script>

    function clickondocument(id, type = '') {
        $(".loader-overlay").show();
        $.ajax({
            url: '{{route("aco.loadpdf", [$preauth_register_id])}}', 
            type: 'POST',
            data: {
                '_token': '{{csrf_token()}}',
                'id' : id,
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

</script>