@extends('layouts.preauth.app')
@section('title','Search Benificiary')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y mb-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="javascript:void(0);">Home</a>
            </li>
            <li class="breadcrumb-item active">Search Beneficiary</li>
        </ol>
    </nav>
    <div class="row">
        <div class="bs-stepper-content mt-4">
            <form onSubmit="return false">
                <!-- Communication Address -->
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <label class="mb-3">Search Beneficiary</label>
                        <div class="mb-4 d-flex beni-merger">
                            <select id="scheme_id" class="form-select" name="scheme_id">
                                @foreach(App\Models\SchemeType::get()->all() as $scheme)
                                    <option value="{{ $scheme->id }}">{{ $scheme->name }}</option>
                                @endforeach
                            </select>
                            <div class="input-group">
                                <input type="search" class="form-control" name="card_number"
                                    placeholder="Search by Ayushman id/Mobile Number/ABHA Number"
                                    aria-label="Recipient's username" oninput="sanitize(this, 'm','30');"
                                    aria-describedby="button-addon2" 
                                    id="search-val"/>
                                <button class="btn btn-outline-primary" id="search-beneficiary" type="button"
                                    id="button-addon2">Search</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center mt-4 mb-5 card-data" style="display:none;">
                    <div class="col-md-8 col-lg-8 col-xl-8">
                        <div class="gov-pationt-card mb-5">
                            <img src="{{ asset('public/front/assets/img/n_logo-removebg-preview.png') }}" alt="avatar" class="pmjay-overlay" />
                            <div class="grid-wrapper-inside">
                                <div class="left-part text-center">
                                    <div class="position-relative image-overlay">
                                        <img src="" id="image" alt="avatar" class="rounded-circle" />
                                    </div>
                                    <span class="number-1 mb-2 fw-bold text-black" id="name"></span>
                                    <span class="number-3" id="card_id"></span>
                                </div>
                                <div class="right-part">
                                    <ul class="list-unstyled">
                                        <li><label>Age: </label> <span id="age"></span> Yr</li>
                                        <li><label>Gender: </label><span id="gender"></span> </li>
                                        <li><label>Mobile: </label><span id="mobile_no"></span> </li>
                                        <li><label>Address: </label><span id="address"></span> </li>
                                        <li><label>District:</label><span id="district"></span> </li>
                                        <li><label>State:</label> <span id="state"></span></li>
                                        <li><label>Scheme Type</label><span id="care_plan"></span> </li>
                                        <li><label>ABHA Number</label> <span id="aabha_id"></span></li>
                                    </ul>
                                </div>
                            </div>
                            <button class="btn btn-info ml-auto" type="button" id="verify">Verify</button>
                        </div>
                    </div>
                </div>
                <div class="card mb-6 border border-primary verify-data" style="display:none;">
                    <div class="card-body">
                        <div class="card-title header-elements mb-5">
                            <h5 class="m-0 me-2 theme-color">Verify Beneficiaries</h5>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="d-flex flex-wrap">
                                    <div class="form-check ms-4">
                                        <input class="form-check-input" type="radio" name="kyc_type"
                                            id="fingerprint" value="fingerprint">
                                        <label class="form-check-label" for="fingerprint">
                                            Finger Print
                                        </label>
                                    </div>
                                    <div class="form-check ms-4">
                                        <input class="form-check-input" type="radio" name="kyc_type"
                                            id="iris" value="iris">
                                        <label class="form-check-label" for="iris">
                                            IRIS
                                        </label>
                                    </div>
                                    <div class="form-check ms-4">
                                        <input class="form-check-input" type="radio" name="kyc_type"
                                            id="aadhar_otp" value="aadhar_otp">
                                        <label class="form-check-label" for="aadhar_otp">
                                            Aadhar OTP
                                        </label>
                                    </div>
                                    <div class="form-check  ms-4">
                                        <input class="form-check-input" type="radio" name="kyc_type" value="without_auth"
                                            id="without_auth" checked />
                                        <label class="form-check-label fw-semibold" for="without_auth"> Proceed Without Aadhar Authentication </label>
                                    </div>
                                </div>
                                <div class="row m-2 aadhar-otp-field d-none">
                                    <div class="col-sm-4">
                                        <div class="form-password-toggle">
                                            <div class="input-group input-group-merge">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="number" class="form-control"
                                                        id="aadhar_no" name="aadhar_no" oninput="sanitize(this, 'n', '12');"
                                                        placeholder="">
                                                    <input type="hidden" name="reference_id" id="reference_id">

                                                    <label for="basic-default-password12">Aadhar
                                                        Number</label>
                                                </div>
                                                <button class="input-group-text cursor-pointer theme-color" id="send-otp" onclick="SendOTPOnAadhar()">SEND</button>
                                                <!-- <i class="ri-checkbox-fill theme-color"></i> -->
                                            </div>
                                            <span id="aadhar-error" class="text-danger"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 otp-field d-none">
                                        <div class="form-password-toggle">
                                            <div class="input-group input-group-merge">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="number" class="form-control" oninput="sanitize(this, 'n','8');" id="aadhar_otp_no" name="aadhar_otp_no" placeholder="123456">
                                                    <label for="basic-default-password12">Enter OTP</label>
                                                </div>
                                                <button class="input-group-text cursor-pointer theme-color" disabled id="resend-otp" onclick="ReSendOTPOnAadhar()">RESEND OTP</button>
                                            </div>
                                            <span id="otp-error" class="text-danger"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 otp-field d-none">
                                        <button class="otp-btn" id="verify-otp" disabled onclick="VerifyOtp()">VERIFY OTP</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 consent-field d-none">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="terms" value="1" id="terms"
                                        />
                                    <label class="form-check-label" for="terms">I hereby declare that I am voluntarily sharing my identity information / Aadhaar Number / Virtual ID issued by UIDAI with National Health Authority (NHA) for the purpose of creating and linking my PM-JAY ID with ABHA number. I understand that upon successfully creation and linking both cards, I stand to avail benefits under Ayushman Bharat Pradhan Mantri Jan Aarogya Yojana (AB PM-JAY) and related benefits under Ayushman Bharat Digital Mission. I also authorize NHA to use my Aadhaar number / Virtual ID for performing Aadhaar based authentication with UIDAI and store my e-KYC information as per the provisions of Aadhaar Act, 2016 read with Regulations and Amendments as may be updated from time to time only for the above purpose. I understand that UIDAI will share my e-KYC (Name, Address, Age, DoB, Gender and Photograph) details with NHA on successful authentication. I also understand that my e-KYC information excluding Aadhaar number / Virtual ID / UID Token will be made available to empanelled hospitals, insurers, insurance agencies (ISAs), State Health Agencies (SHA) and entities (such as Healthcare Information Providers (HIPs), Healthcare Information Users (HIUs), Consent Managers, Healthcare Repository Providers (HRPs), and Health Lockers as specified under National Digital Health Ecosystem (NDHE)), post my consent and authentication. Further, I give my consent to use my e-KYC details for following: • Enrollment into the AB PM-JAY Scheme, ABHA, Golden record generation. • Availing benefits under AB PM-JAY and ABDM scheme at any of the empanelled hospital. • The purpose of Data Analytics by NHA. • Enabling the healthcare services across NDHE • Verifying eligibility under other schemes of Government of India. I have been duly informed about the option of creation of AB PM-JAY ID and ABHA No. without using my Aadhaar details. However, I have consciously taken the decision to use Aadhaar number (UID) / Virtual ID (VID) for the purpose of availing benefits under AB PM-JAY and ABDM. I also understand that my Aadhaar number may be used to verify my information available with SECC, RSBY and other databases as required by the scheme. I reserve the right to revoke the given consent at any point of time from National Health Authority.
                                    पीएम-जय आईडी को हेल्थ आईडी से जोड़ने के लिए आधार सहमति टेक्स्ट मैं एतदद्वारा घोषणा करता हूं कि मैं अपनी पीएम-जेएवाई आईडी को हेल्थ आईडी के साथ बनाने और लिंक करने के उद्देश्य से राष्ट्रीय स्वास्थ्य प्राधिकरण (एनएचए) के साथ यूआईडीएआई द्वारा जारी अपनी पहचान की जानकारी/आधार संख्या/वर्चुअल आईडी स्वेच्छा से साझा कर रहा हूं। मैं समझता हूं कि दोनों कार्डों को सफलतापूर्वक बनाने और लिंक करने पर, मैं आयुष्मान भारत प्रधानमंत्री जन आरोग्य योजना (एबी-पीएमजेएवाई) और आयुष्मान भारत डिजिटल मिशन के तहत संबंधित लाभों का लाभ उठाने के लिए खड़ा हूं। मैं एनएचए को यूआईडीएआई के साथ आधार आधारित प्रमाणीकरण करने के लिए मेरे आधार नंबर/वर्चुअल आईडी का उपयोग करने के लिए भी अधिकृत करता हूं और आधार अधिनियम, 2016 के प्रावधानों के अनुसार मेरी ई-केवाईसी जानकारी को समय-समय पर अद्यतन किए जा सकने वाले विनियमों और संशोधनों के साथ पढ़ता हूं। उपरोक्त उद्देश्य। मैं समझता हूं कि सफल प्रमाणीकरण पर यूआईडीएआई एनएचए के साथ मेरा ई-केवाईसी (नाम, पता, आयु, जन्मतिथि, लिंग और फोटोग्राफ) विवरण साझा करेगा। मैं यह भी समझता हूं कि आधार संख्या/वर्चुअल आईडी/यूआईडी टोकन को छोड़कर मेरी ई-केवाईसी जानकारी सूचीबद्ध अस्पतालों, बीमाकर्ताओं, बीमा एजेंसियों (आईएसए), राज्य स्वास्थ्य एजेंसियों (एसएचए) और संस्थाओं (जैसे स्वास्थ्य सेवा सूचना प्रदाता (एचआईपी)) को उपलब्ध कराई जाएगी। ), हेल्थकेयर सूचना उपयोगकर्ता (एचआईयू), सहमति प्रबंधक, हेल्थकेयर रिपॉजिटरी प्रोवाइडर (एचआरपी), और नेशनल डिजिटल हेल्थ इकोसिस्टम (एनडीएचई) के तहत निर्दिष्ट स्वास्थ्य लॉकर्स, मेरी सहमति और प्रमाणीकरण के बाद। इसके अलावा, मैं निम्नलिखित के लिए अपने ई-केवाईसी विवरण का उपयोग करने के लिए अपनी सहमति देता हूं: • AB PM-JAY योजना में नामांकन (ABHA/गोल्डन रिकॉर्ड जनरेशन)। • किसी भी सूचीबद्ध अस्पताल में AB PM-JAY और ABDM योजना के तहत लाभ प्राप्त करना। • एनएचए द्वारा डेटा एनालिटिक्स का उद्देश्य। • एनडीएचई में स्वास्थ्य सेवाओं को सक्षम बनाना • भारत सरकार की अन्य योजनाओं के तहत पात्रता का सत्यापन। मुझे मेरे आधार विवरण का उपयोग किए बिना एबी-पीएमजेएवाई आईडी और आभा संख्या के निर्माण के विकल्प के बारे में विधिवत सूचित किया गया है। हालांकि, मैंने एबी-पीएमजेएवाई और एबीडीएम के तहत लाभ प्राप्त करने के उद्देश्य से आधार संख्या (यूआईडी)/वर्चुअल आईडी (वीआईडी) का उपयोग करने का निर्णय जानबूझकर लिया है। मैं यह भी समझता हूं कि मेरी आधार संख्या का उपयोग SECC, RSBY और योजना द्वारा आवश्यक अन्य डेटाबेस के साथ उपलब्ध मेरी जानकारी को सत्यापित करने के लिए किया जा सकता है। मैं किसी भी समय राष्ट्रीय स्वास्थ्य प्राधिकरण से दी गई सहमति को रद्द करने का अधिकार सुरक्षित रखता हूं।</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-primary" type="button" id="proceed">Proceed</button>
                                    <button class="btn btn-outline-primarbtn-primary ms-3" type="button">Back</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    $("#search-beneficiary").on("click",function(){
        let search = $('#search-val').val();
        let scheme_id = $('#scheme_id').val();
        $(".loader-overlay").show();
        fetch('{{route("preauth.fetch-card")}}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ search,scheme_id })
        })
        .then(response => response.json())
        .then(data => {
            
            $(".loader-overlay").hide();
            if (data.success) {
                $(".card-data").show();
                $('#search-val').attr("disabled",true);
                $('#scheme_id').attr("disabled",true);
                $('#name').html(data.benificiary.name);
                $('#card_id').html(data.benificiary.card_id);
                $('#age').html(data.benificiary.age);
                $('#gender').html(data.benificiary.gender);
                $('#mobile_no').html(data.benificiary.mobile_no);
                $('#address').html(data.benificiary.address);
                $('#district').html(data.benificiary.dist_name);
                $('#state').html(data.benificiary.state_name);
                $('#care_plan').html(data.benificiary.care_plan);
                $('#aabha_id').html(data.benificiary.aabha_id);
                if(data.benificiary.image != ''){
                    $("#image").attr("src", data.benificiary.image_url);
                }else{
                    $("#image").attr("src","{{ asset('public/front/assets/img/avatars/1.png') }}");
                }
            } else {
                $(".card-data").hide();
                $(".verify-data").hide();
                errorMessage(data.msg);
            }
        });
    })
    $("#verify").on("click",function(){
        let search = $('#search-val').val();
        let scheme_id = $('#scheme_id').val();
        $(".loader-overlay").show();
        fetch('{{route("preauth.verify-card")}}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ search,scheme_id })
        })
        .then(response => response.json())
        .then(data => {
            
            $(".loader-overlay").hide();
            if (data.success) {
                $(".verify-data").show();
            } else {
                $(".verify-data").hide();
                errorMessage(data.msg);
            }
        });
    });
    $("#proceed").click(function(){
        search = $('#search-val').val();
        scheme_id = $('#scheme_id').val();
        kyc_type = $('input[name="kyc_type"]:checked').val();
        terms = $('input[name="terms"]:checked').val();
        aadhar_no = '';
        $(".loader-overlay").show();
        fetch('{{route("preauth.register-patient-ses")}}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ search,scheme_id,kyc_type,aadhar_no,terms })
        })
        .then(response => response.json())
        .then(data => {
            
            $(".loader-overlay").hide();
            if (data.success) {
                window.location.href="{{ route('preauth.register-patient') }}";
            } else {
                errorMessage(data.message);
            }
        });
    })
    $("input[name='kyc_type']").on("change",function(){
        if($("input[name='kyc_type']:checked").val() == 'aadhar_otp'){
            $("#proceed").attr('disabled',true);
            $(".aadhar-otp-field").removeClass('d-none');
        }else{
            $(".aadhar-otp-field").addClass('d-none');
            $("#proceed").attr('disabled',false);
        }
        if($("input[name='kyc_type']:checked").val() != 'without_auth'){
            $(".consent-field").removeClass('d-none');
        }else{
            $(".consent-field").addClass('d-none');
        }
    })
    $("#aadhar_no").on("change",function(){
        $("#send-otp").attr("disabled",false);
        $("#resend-otp").attr("disabled",true);
        $("#verify-otp").attr("disabled",true);
        
    })
    // Send OTP to Aadhar
    function SendOTPOnAadhar() {
        let aadhar_no = $('#aadhar_no').val();
        if(aadhar_no != ''){
            ldrshow();
            $("#aadhar-error").text("");
            fetch('{{route("preauth.send-aadhar-otp")}}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ aadhar_no })
            })
            .then(response => response.json())
            .then(data => {
                ldrhide();
                if (data.success) {
                    successMessage(data.message);
                    $(".otp-field").removeClass('d-none');
                    // $("#aadhar_otp_no").val(data.otp);
                    $('#aadhar_no').attr('readonly', true);
                    $('#reference_id').val(data.reference_id);
                    $("#send-otp").attr('disabled',true);
                    $("#resend-otp").attr("disabled",false);
                    $("#verify-otp").attr("disabled",false);
                } else {
                    $('#aadhar_no').removeAttr('readonly');
                    $("#aadhar-error").text(data.message);
                }
            });
        }else{
            $("#aadhar-error").text("Please enter aadhar number");
        }
    }
    // Re-Send OTP to Aadhar
    function ReSendOTPOnAadhar() {
        let aadhar_no = $('#aadhar_no').val();
        if(aadhar_no != ''){
            ldrshow();
            $("#aadhar-error").text("");
            fetch('{{route("preauth.resend-aadhar-otp")}}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ aadhar_no })
            })
            .then(response => response.json())
            .then(data => {
                ldrhide();
                if (data.success) {
                    successMessage(data.message);
                    $('#aadhar_no').attr('readonly', true);
                    $('#reference_id').val(data.reference_id);
                    // $("#aadhar_otp_no").val(data.otp);
                } else {
                    $('#aadhar_no').removeAttr('readonly');
                    $("#aadhar-error").text(data.message);
                }
            });
        }else{
            $("#aadhar-error").text("Please enter aadhar number");
        }
    }
    function VerifyOtp(){
        
        let aadhar_no = $('#aadhar_no').val();
        let otp = $('#aadhar_otp_no').val();
        let reference_id = $('#reference_id').val();
        if(otp != ''){
            ldrshow();
            $("#otp-error").text("");
            fetch('{{route("preauth.verify-aadhar-otp")}}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ aadhar_no,otp,reference_id })
            })
            .then(response => response.json())
            .then(data => {
                ldrhide();
                if (data.success) {
                    $("#aadhar_no").attr("disabled",true);
                    $("#aadhar_otp").attr("disabled",true);
                    $("#send-otp").attr("disabled",true);
                    $("#send-otp").html("<i class='ri-checkbox-fill theme-color'></i>");
                    $("#resend-otp").html("<i class='ri-checkbox-fill theme-color'></i>");
                    $("#resend-otp").attr("disabled",true);
                    $("#verify-otp").attr("disabled",true);
                    $(".otp-field").addClass("d-none");
                    successMessage('OTP Verifed');
                    $("#proceed").attr('disabled',false);
                } else {
                    $("#otp-error").text(data.message);
                }
            });
        }else{
            $("#otp-error").text("Please enter OTP");
        }
    }
</script>
@endpush