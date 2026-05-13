@php 
    $status = json_decode($preauth_register->medical_committee_status, true);
@endphp
<style>
    .violate {
        color: #757bff;
    }
</style>
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
                    <td class="status{{$value->id}}">@if($value->medical_committee_status != "") @if($value->medical_committee_status == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i>  @endif</td>
                    <td style="padding:0;">
                        <a href="javascript:;" onclick="clickondocument('{{$value->id}}');" class=" document{{$key}} doc-{{$value->id}}">{{@$value->investigation->name}}
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
            @if($preauth_register->preauth_query_add_doc)
                <tr class="" style="text-decoration: none; display: -webkit-inline-box">
                    <td class="statuspreauth_query_add_doc">@if(@$status['preauth_query_add_doc'] != "") @if(@$status['preauth_query_add_doc'] == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i>  @endif</td>
                    <td style="padding:0;">
                        <a href="javascript:;" onclick="clickondocument('', 'preauth_query_add_doc');" class=" document-preauth_query_add_doc">Preauth Query Supporting Other Document
                        </a>
                    </td>
                </tr>
            @endif
            @foreach($preauth_register->enhancement_docs as $key => $value)
                <tr class="" style="text-decoration: none; display: -webkit-inline-box">
                    <td class="enhancement-status{{$value->id}}">@if($value->medical_committee_status != "") @if($value->medical_committee_status == "Correct") <i class="ri-file-check-fill text-primary"></i> @else <i class="ri-file-close-fill text-danger"></i> @endif @else <i class="ri-file-info-line"></i>  @endif</td>
                    <td style="padding:0;">
                        <a href="javascript:;" onclick="clickondocument('{{$value->id}}','enhancement');" class=" enhancement-document{{$key}} enhancement--document{{$value->id}}">{{$value->name}}
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

     // Build a list of documents in the order they appear
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
            status: '{{ $value->medical_committee_status }}',
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
            status: '{{ $value->medical_committee_status }}',
            index: {{ $index++ }}
        });
    @endforeach

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

    function clickondocument(id, type = '') {
        $(".loader-overlay").show();
        $("a[class*='document-'], a[class*='doc-'], a[class*='enhancement--document']").removeClass("violate fw-bold");
        if (type === 'enhancement') {
            $(".enhancement--document" + id).addClass("violate fw-bold");
        } else if (type) {
            $(".document-" + type).addClass("violate fw-bold");
        } else {
            $(".doc-" + id).addClass("violate fw-bold");
        }

        $.ajax({
            url: '{{route("medical-committee.loadpdf", [$preauth_register_id])}}', 
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

    function verifyDocument(status, id, statustype = '') {
        $(".loader-overlay").show();
        $.ajax({
            url: '{{route("medical-committee.verifydocument", [$preauth_register_id])}}', 
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
                    } else {
                        if(statustype == 'enhancement'){
                            var classname = 'enhancement-status'+id;
                        }else{
                            var classname = 'status'+statustype;
                        }
                    }
                    if(status == "Correct") {
                        $('.'+classname).html('<i class="ri-file-check-fill text-primary"></i>')
                    } else {
                        $('.'+classname).html('<i class="ri-file-close-fill text-danger"></i>')
                    }
                    if (data.documentsinfo) {
                        $('.Documents').removeClass('pending-color').addClass('theme-color');
                    }
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