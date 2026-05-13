@php
    $profileUuid = $data->uuid ?? '';
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Hospital profile setup — ParaCare+ HIMS v3.0</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Noto+Sans+Devanagari:wght@400;600;700&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('public/front/assets/css/login.css') }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('public/front/assets/vendor/libs/toastr/toastr.css') }}" />
    <link rel="stylesheet" href="{{ asset('public/front/assets/vendor/libs/select2/select2.css') }}" />
    <style>
        /* Dark shell — match facility form reference (#0b1121 / #161d31 / #1c253b) */
        .signup-profile-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: 'Inter', system-ui, sans-serif !important;
            background: #0b1121 !important
        }

        .signup-profile-page .bg-anim {
            opacity: .22;
            pointer-events: none
        }

        .signup-profile-page .main-wrap {
            flex: 1;
            flex-direction: column;
            width: 100%;
            max-width: 100%;
            min-height: 0
        }

        .signup-profile-page .right-panel {
            flex: 1;
            width: 100%;
            max-width: 100%;
            align-items: stretch;
            justify-content: flex-start;
            padding: 16px clamp(14px, 3vw, 40px) 28px;
            border-left: none;
            background: transparent
        }

        /* No outer “card” — flat section panel only */
        .signup-profile-page .profile-form-root {
            position: relative;
            width: 100%;
            max-width: 100%
        }

        .signup-profile-page .profile-form-root .login-loading {
            border-radius: 12px
        }

        .signup-profile-page .hims-form-section {
            background: #161d31;
            border-radius: 12px;
            padding: 22px clamp(18px, 2.5vw, 28px) 28px;
            border: 1px solid rgba(255, 255, 255, .04)
        }

        .signup-profile-page .hims-form-section__head {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 22px;
            padding-bottom: 18px;
            border-bottom: 1px solid rgba(255, 255, 255, .06)
        }

        .signup-profile-page .hims-form-section__icon {
            flex-shrink: 0;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(59, 130, 246, .15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #60a5fa
        }

        .signup-profile-page .hims-form-section__titles h2 {
            margin: 0 0 4px;
            font-size: 1.05rem;
            font-weight: 700;
            color: #f8fafc;
            letter-spacing: -.02em
        }

        .signup-profile-page .hims-form-section__titles p {
            margin: 0;
            font-size: 12.5px;
            font-weight: 400;
            color: #94a3b8;
            line-height: 1.45
        }

        .signup-profile-page .hims-form-section__badge {
            margin-top: 10px;
            display: inline-block;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            color: #93c5fd;
            background: rgba(37, 99, 235, .18);
            border: 1px solid rgba(59, 130, 246, .25)
        }

        .signup-profile-page .hims-form-section__body {
            max-height: none
        }

        .signup-profile-page .form-label {
            font-weight: 500;
            font-size: 13px;
            color: #f1f5f9;
            margin-bottom: 6px;
            letter-spacing: .01em
        }

        .signup-profile-page .form-label .text-danger,
        .signup-profile-page .req {
            color: #f87171;
            font-weight: 600
        }

        .signup-profile-page .form-control,
        .signup-profile-page .form-select {
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, .06) !important;
            padding: 10px 14px;
            font-size: 14px;
            font-weight: 400;
            background: #1c253b !important;
            color: #f8fafc !important;
            transition: border-color .15s, box-shadow .15s
        }

        .signup-profile-page .form-control::placeholder {
            color: #64748b;
            opacity: 1
        }

        .signup-profile-page .form-control:disabled,
        .signup-profile-page .form-select:disabled {
            background: #151c2e !important;
            color: #94a3b8 !important;
            cursor: not-allowed;
            opacity: .9
        }

        .signup-profile-page .form-control[readonly] {
            background: #182032 !important;
            color: #cbd5e1 !important
        }

        .signup-profile-page .form-control:focus,
        .signup-profile-page .form-select:focus {
            border-color: rgba(59, 130, 246, .45) !important;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, .2);
            background: #1c253b !important;
            color: #fff !important
        }

        .signup-profile-page select.form-select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: right 12px center !important;
            background-size: 11px !important;
            padding-right: 36px !important
        }

        .signup-profile-page .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #f8fafc;
            line-height: 30px
        }

        .signup-profile-page .select2-container--default.select2-container--disabled .select2-selection--single {
            background: #151c2e !important;
            opacity: .9
        }

        .signup-profile-page .input-group .btn-verify-mobile {
            border-radius: 0 8px 8px 0;
            font-weight: 700;
            font-size: 11px;
            padding: 0 14px;
            border: 1px solid rgba(59, 130, 246, .35);
            background: #2563eb;
            color: #f8fafc
        }

        .signup-profile-page .input-group .btn-verify-mobile:hover {
            background: #1d4ed8;
            color: #fff
        }

        .signup-profile-page .input-group .form-control {
            border-radius: 8px 0 0 8px;
        }

        .signup-profile-page .input-group:not(.has-verify) .form-control {
            border-radius: 8px;
        }

        .signup-profile-page .upload-box {
            width: 112px;
            height: 112px;
            border: 2px dashed rgba(148, 163, 184, .35);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            background: #1c253b
        }

        .signup-profile-page .upload-box i {
            font-size: 1.75rem;
            color: #64748b;
        }

        .signup-profile-page .upload-box input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
        }

        .signup-profile-page .error {
            color: #f87171;
        }

        .signup-profile-page .select2-container--default .select2-selection--single {
            border-radius: 8px !important;
            border: 1px solid rgba(255, 255, 255, .06) !important;
            min-height: 44px;
            padding: 6px 10px;
            background: #1c253b !important
        }

        .signup-profile-page .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #94a3b8 transparent transparent transparent !important
        }

        .signup-profile-page .btn-submit-profile {
            display: inline-block;
            width: 100%;
            max-width: 280px;
            margin-top: 24px;
            border-radius: 8px;
            padding: 12px 20px;
            font-weight: 600;
            font-size: 14px;
            font-family: inherit;
            cursor: pointer;
            background: #2563eb !important;
            border: none !important;
            color: #fff !important;
            box-shadow: 0 4px 14px rgba(37, 99, 235, .35)
        }

        .signup-profile-page .btn-submit-profile:hover:not(:disabled) {
            background: #1d4ed8 !important;
            color: #fff !important
        }

        .signup-profile-page .btn-submit-profile:disabled {
            opacity: .5;
            cursor: not-allowed
        }

        .signup-profile-page .resend-mobile {
            float: right;
            font-size: 12px;
            border: none;
            background: none;
            cursor: pointer;
            color: #60a5fa;
            font-family: inherit;
            padding: 0;
        }

        .signup-profile-page .resend-mobile:hover {
            text-decoration: underline;
            color: #93c5fd
        }

        body.signup-profile-page .select2-dropdown {
            background: #161d31 !important;
            border: 1px solid rgba(255, 255, 255, .1) !important;
            border-radius: 8px !important
        }

        body.signup-profile-page .select2-results__option {
            color: #e2e8f0
        }

        body.signup-profile-page .select2-results__option--highlighted {
            background: rgba(37, 99, 235, .45) !important;
            color: #fff !important
        }

        body.signup-profile-page .select2-search--dropdown .select2-search__field {
            background: #1c253b;
            border: 1px solid rgba(255, 255, 255, .08) !important;
            color: #f8fafc;
            border-radius: 6px
        }

        /* Footer: normal document flow (not fixed) */
        .signup-profile-page .page-footer {
            position: relative !important;
            z-index: 20
        }

        .signup-profile-page .pf-inner-row {
            display: flex;
            width: 100%;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px 16px
        }

        .signup-profile-page .pf-build {
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #5a7a8e;
            line-height: 1.35;
            margin-top: 6px
        }

        @media (max-width: 700px) {
            .signup-profile-page .pf-inner-row {
                flex-direction: column;
                text-align: center
            }
        }
    </style>
</head>

<body class="signup-profile-page">

    <div class="bg-anim" aria-hidden="true">
        <div class="bg-layer1"></div>
        <div class="bg-lines"></div>
        <div class="bg-glow"></div>
        <div class="bg-glow2"></div>
        <div class="particles" id="particles"></div>
    </div>

    <div class="gov-ribbon">
        <div class="gr-left">
            <div class="gr-seal">
                <svg viewBox="0 0 52 52" width="52" height="52">
                    <circle cx="26" cy="26" r="24" fill="none" stroke="#c8a84b" stroke-width="2" />
                    <circle cx="26" cy="26" r="20" fill="none" stroke="#c8a84b" stroke-width=".7"
                        stroke-dasharray="3 2" />
                    <circle cx="26" cy="26" r="17" fill="rgba(11,78,168,.7)" />
                    <text x="26" y="31" font-size="15" fill="#f9a825" text-anchor="middle" font-weight="900"
                        font-family="serif">अ</text>
                    <path d="M8 44 Q26 50 44 44" fill="none" stroke="#c8a84b" stroke-width="1.2" />
                </svg>
            </div>
            <div class="gr-div"></div>
            <div class="gr-info">
                <div class="line1">Government of Uttarakhand - Department of Health &amp; Family Welfare</div>
                <div class="hindi" lang="en" style="font-family:'Inter',sans-serif;color:#a0c0d8">Uttarakhand Government · Health &amp; Family Welfare</div>
                <div class="line2">State Health Information Management System (SHIMS) | ABDM Compliant</div>
                <div class="line3">Designed &amp; Developed by NIC Uttarakhand | v3.0.0 | ISO 27001 Certified</div>
            </div>
        </div>
        <div class="gr-right">
            <div class="gr-badge green"><span class="live-dot"></span> All Systems Online</div>
            <div class="gr-badge blue">🔒 256-bit SSL</div>
            <div class="gr-badge amber">⏰ <span id="clockRibbon">--:--:--</span></div>
        </div>
    </div>

    <div class="announce-strip">
        <span class="ann-icon">📢</span>
        <div class="ann-marquee">
            <span class="ann-text">
                🏥 Hospital onboarding — complete your administrator profile to access the dashboard &nbsp;|&nbsp;
                ParaCare+ HIMS v3.0 - Fully Integrated Hospital Information Management System &nbsp;|&nbsp;
                🔐 Keep passwords strong and unique | Do not share OTPs &nbsp;|&nbsp;
                ⚕️ ABDM compliant workflows | PHR linked records
            </span>
        </div>
        <span class="ann-badge">LIVE</span>
    </div>

    <div class="main-wrap">
        <div class="right-panel">
            <div class="profile-form-root" id="profileCard">
                <div class="login-loading" id="profileLoading" aria-live="polite" aria-busy="false">
                    <div class="ld-spinner"></div>
                    <div class="ld-text" id="profileLoadingText">Please wait…</div>
                </div>

                <section class="hims-form-section">
                    <div class="hims-form-section__head">
                        <div class="hims-form-section__icon" aria-hidden="true">🏥</div>
                        <div class="hims-form-section__titles">
                            <h2>Hospital administrator profile</h2>
                            <p>Update your details and submit to finish onboarding.</p>
                            <div class="hims-form-section__badge">Registration ID ·
                                {{ strlen((string) $profileUuid) > 18 ? substr((string) $profileUuid, 0, 18) . '…' : $profileUuid }}
                            </div>
                        </div>
                    </div>

                    <div class="hims-form-section__body">
                    <form id="profileForm" enctype="multipart/form-data">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <div class="input-group profileerror">
                                    <input type="text" class="form-control" oninput="sanitize(this, 't');"
                                        value="{{ $data->name }}" name="name" required id="name" placeholder="Name">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                                <div class="input-group profileerror">
                                    <select class="form-select" name="gender" id="gender" required>
                                        <option value="">Select</option>
                                        <option value="Male" {{ $data->gender == 'Male' ? 'selected' : '' }}>Male
                                        </option>
                                        <option value="Female" {{ $data->gender == 'Female' ? 'selected' : '' }}>Female
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label for="state" class="form-label">State <span class="text-danger">*</span></label>
                                <div class="input-group profileerror">
                                    <select id="state" class="form-control select2" name="state" required>
                                        <option value="">Select</option>
                                        @foreach ($states as $key => $value)
                                            <option value="{{ $value->name }}"
                                                {{ $data->state == $value->name ? 'selected' : '' }}>{{ $value->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <div class="input-group profileerror">
                                    <input type="email" class="form-control" value="{{ $data->email }}"
                                        oninput="sanitize(this, 'email');" id="email" name="email" placeholder="Type here"
                                        {{ $data->email ? 'readonly' : 'required' }}>
                                </div>
                                <span class="email-error error"></span>
                            </div>
                            <div class="col-md-3">
                                <label for="password" class="form-label">Password <span
                                        class="text-danger">*</span></label>
                                <div class="input-group profileerror">
                                    <input type="password" class="form-control" value="" id="password" name="password"
                                        placeholder="Type here" autocomplete="new-password">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="confirmation_password" class="form-label">Confirm Password <span
                                        class="text-danger">*</span></label>
                                <div class="input-group profileerror">
                                    <input type="password" class="form-control" value="" id="confirmation_password"
                                        name="confirmation_password" placeholder="Type here" autocomplete="new-password">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="mobileNumber" class="form-label">Mobile Number <span
                                        class="text-danger">*</span></label>
                                <div
                                    class="input-group profileerror {{ $data->mobile_no == '' ? 'has-verify' : '' }}">
                                    <input type="text" class="form-control mobile_no" id="mobileNumber" name="mobile_no"
                                        placeholder="Type here" oninput="validateMobileNo(this)"
                                        value="{{ $data->mobile_no }}" {{ $data->mobile_no ? 'readonly' : 'required' }}>
                                    @if ($data->mobile_no == '')
                                        <button type="button" class="btn-verify-mobile verifymobile_no"
                                            onclick="CheckMobile();">VERIFY</button>
                                    @endif
                                </div>
                                <span class="mobile-error error"></span>
                            </div>

                            <div class="col-md-3" id="mobile_otp_block" style="display:none">
                                <label class="form-label">Enter Mobile OTP <span class="text-danger">*</span></label>
                                <input type="text" name="mobile_otp" id="mobile_otp" class="form-control"
                                    placeholder="Type here">
                                <div class="mt-1">
                                    <button type="button" class="resend-mobile resendMobileOtp"
                                        onclick="ResendOtp('Mobile')">Resend OTP</button>
                                </div>
                                <span class="mobileotpverify error"></span>
                            </div>
                        </div>
                        <input type="hidden" name="hospital_type" value="Single">
                        <!-- <div class="row g-3 ">
                            <div class="col-md-3">
                                <label for="hospital_type" class="form-label">Hospital Type <span
                                        class="text-danger">*</span></label>
                                <div class="input-group profileerror">
                                    <select class="form-select" name="hospital_type" id="hospital_type" required>
                                        <option value="">Select</option>
                                        <option value="Single" {{ $data->hospital_type == 'Single' ? 'selected' : '' }}>
                                            Single</option>
                                        <option value="Multi-Branch"
                                            {{ $data->hospital_type == 'Multi-Branch' ? 'selected' : '' }}>Multi-Branch
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div
                                class="col-md-3 hospital-branch {{ $data->hospital_type == 'Multi-Branch' ? '' : 'd-none' }}">
                                @php
                                    $hospitals = App\Models\Hospital::where('hospital_type', 'Multi-Branch')
                                        ->where('status', 'Approved')
                                        ->get();
                                @endphp
                                <label for="hospital" class="form-label">Branch Hospital<span
                                        class="text-danger">*</span></label>
                                <div class="input-group profileerror">
                                    <select class="form-select select2" name="hospital" id="hospital">
                                        <option value="0">Main</option>
                                        @foreach ($hospitals as $hospital)
                                            <option value="{{ $hospital->id }}"
                                                {{ $data->parent_id == $hospital->id ? 'selected' : '' }}>
                                                {{ $hospital->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div> -->

                        <div class="mt-3">
                            <label for="uploadPicture" class="form-label">Upload Profile Picture <span
                                    class="text-danger">*</span></label>
                            <div class="d-flex flex-wrap align-items-start gap-3">
                                <div class="upload-box">
                                    <i class="fas fa-upload"></i>
                                    <input type="file" name="avatar" id="uploadPicture"
                                        accept="image/jpg,image/jpeg,image/png">
                                </div>
                                <div class="d-flex flex-column gap-2">
                                    @if ($data->avatar)
                                        <img src="{{ asset('public/storage/' . $data->avatar) }}" alt="Current photo"
                                            style="max-width: 100px; border: 1px solid rgba(255,255,255,.2); border-radius: 12px; padding: 4px;">
                                    @endif
                                    <img id="imagePreview" src="" alt="Preview"
                                        style="max-width: 96px; max-height: 96px; display: none; border: 1px solid rgba(255,255,255,.2); border-radius: 12px; padding: 4px;">
                                </div>
                            </div>
                        </div>

                        <button type="button" id="submitForm" class="btn-submit-profile">Submit</button>
                    </form>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <div class="page-footer">
        <div class="pf-inner-row">
            <div class="pf-left">© 2025 Government of Uttarakhand, Department of Health &amp; Family Welfare. All rights
                reserved.</div>
            <div class="pf-right">
                <a class="pf-link" href="{{ route('hospital.login') }}">Back to Login</a>
                <a class="pf-link" href="#">Privacy Policy</a>
                <a class="pf-link" href="#">Terms of Use</a>
                <a class="pf-link" href="#">Accessibility</a>
                <a class="pf-link" href="#">Contact</a>
                <a class="pf-link" href="#">Sitemap</a>
                <span style="opacity:.75;font-size:10px;color:#6a8a9e">AES-256</span>
            </div>
        </div>
        <div class="pf-build">ParaCare+ HIMS v3.0.0 · Build 2024.12 · ABDM Compliant · ISO 27001 · NIC Uttarakhand</div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/js/all.min.js"></script>
    <script src="{{ asset('public/front/assets/vendor/libs/toastr/toastr.js') }}"></script>
    <script src="{{ asset('public/front/assets/js/sanitize.js') }}"></script>
    <script src="{{ asset('public/front/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('public/js/plugin/sweetalert/sweetalert.min.js') }}"></script>

    <script>
        function updateClock() {
            const now = new Date();
            const el = document.getElementById('clockRibbon');
            if (el) {
                el.textContent = now.toLocaleTimeString('en-IN', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                }) + ' IST';
            }
        }
        updateClock();
        setInterval(updateClock, 1000);

        (function() {
            const c = document.getElementById('particles');
            if (!c) return;
            for (let i = 0; i < 28; i++) {
                const d = document.createElement('div');
                d.className = 'p-dot';
                const sz = Math.random() * 4 + 2;
                d.style.cssText =
                    `width:${sz}px;height:${sz}px;left:${Math.random()*100}%;bottom:-${sz}px;animation-duration:${Math.random()*14+8}s;animation-delay:${Math.random()*10}s;background:rgba(${Math.random()>0.5?'21,101,192':'0,105,92'},.4)`;
                c.appendChild(d);
            }
        })();

        function profileLoader(show, msg) {
            const el = document.getElementById('profileLoading');
            const tx = document.getElementById('profileLoadingText');
            if (tx && msg) tx.textContent = msg;
            if (el) {
                el.classList.toggle('show', !!show);
                el.setAttribute('aria-busy', show ? 'true' : 'false');
            }
        }

        $(".select2").select2();
        @if ($data->mobile_no == '' || $data->email == '')
            btnenabled();
        @endif

        function btnenabled() {
            var mobile_otp = @if ($data->mobile_no == '')
                $("#mobile_otp").val()
            @else
                1
            @endif ;
            var email_otp = @if ($data->email == '')
                $("#email_otp").val()
            @else
                1
            @endif ;

            if (mobile_otp != "" && email_otp != "") {
                $("#submitForm").removeAttr('disabled');
            } else {
                $("#submitForm").attr('disabled', true);
            }
        }

        function errorMessage(msg) {
            var shortCutFunction = 'error',
                title = 'Error';
            toastr.options.showDuration = 300;
            toastr.options = {
                maxOpened: 1,
                autoDismiss: true,
                closeButton: true,
                newestOnTop: true,
                progressBar: true,
                positionClass: 'toast-top-right',
                onclick: null,
            };
            toastr[shortCutFunction](msg, title);
        }

        function successMessage(msg) {
            var shortCutFunction = 'success',
                title = 'Success';
            toastr.options.showDuration = 300;
            toastr.options = {
                maxOpened: 1,
                autoDismiss: true,
                closeButton: true,
                newestOnTop: true,
                progressBar: true,
                positionClass: 'toast-top-right',
                onclick: null,
            };
            toastr[shortCutFunction](msg, title);
        }

        function getDistrict() {
            var entity_name = $("#entity_name").val();
            if (entity_name == "District Empanelment Committee") {
                fetchDistrict();
            } else {
                $(".district_block").hide();
            }
        }

        function fetchDistrict(state = '', district = '') {
            const dataId = $("#parent_entity").find(':selected').data('id');
            let state_id = dataId;
            if (!state_id) {
                state_id = state_id;
            }
            if (state_id) {
                $.ajax({
                    url: '{{ route("hospital.getDistrict") }}',
                    type: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'state_id': state_id
                    },
                    dataType: 'json',
                    success: function(data) {
                        $('#district').empty().append('<option value="">Select</option>');
                        $.each(data, function(key, subType) {
                            var selected = '';
                            if (district == subType.id) {
                                selected = 'selected';
                            }
                            $(".district_block").show();
                            $('#district').append(
                                `<option value="${subType.id}" ${selected}>${subType.name}</option>`);
                        });
                    },
                    error: function() {
                        alert('Failed to fetch subtypes. Please try again.');
                    }
                });
            } else {
                $('#district').empty().append('<option value="">Select</option>');
            }
        }

        function validateMobileNo(input) {
            input.value = input.value.replace(/[^0-9]/g, '');
            if (input.value.length > 10) {
                input.value = input.value.slice(0, 10);
            }
        }

        function CheckEmail() {
            var uuid = '{{ $data->uuid }}';
            profileLoader(true, 'Sending…');
            let email = $('#email').val();
            $(".email-error").text("");
            fetch('{{ route("sendEmailMail") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        email,
                        uuid
                    })
                })
                .then(response => response.json())
                .then(data => {
                    profileLoader(false);
                    if (data.success) {
                        $('#email_otp_block').show();
                        $('#email_otp').removeAttr('disabled');
                        $("#email").attr('disabled', true);
                        $(".emailButton").attr('disabled', true);
                    } else {
                        $(".email-error").text(data.message);
                    }
                })
                .catch(function() {
                    profileLoader(false);
                    errorMessage('Network error. Please try again.');
                });
        }

        function OTPVerification() {
            let otp = $("#email_otp").val();
            let email = $('#email').val();
            $(".emailotpverify").text("");
            VerifyOTP(otp, 'Email', email, '', '', function(data) {
                console.log(data);
                if (data.success) {
                    btnenabled();
                    $("#email_otp").attr('disabled', true);
                    $("#email").attr('readonly', true);
                    $(".emailbutton").removeClass('btn-default').addClass('btn-success');
                    $(".resendEmailOtp").hide();
                } else {
                    $(".emailotpverify").text(data.message);
                }
            });
        }

        function VerifyOTP(otp, type, email = '', aadhaar = '', mobile_no = '', callback) {
            profileLoader(true, 'Verifying…');
            fetch('{{ route("verifyEmailOtp") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        otp,
                        type,
                        email,
                        mobile_no,
                        aadhaar
                    })
                })
                .then(response => response.json())
                .then(data => {
                    profileLoader(false);
                    if (callback && typeof callback === 'function') {
                        callback(data);
                    }
                })
                .catch(function() {
                    profileLoader(false);
                    errorMessage('Network error. Please try again.');
                });
        }

        function CheckMobile() {
            var uuid = '{{ $data->uuid }}';
            profileLoader(true, 'Sending OTP…');
            let mobile_no = $('.mobile_no').val();
            let email = $('#email').val();
            $(".mobile-error").text("");
            fetch('{{ route("sendMobileMail") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        email,
                        mobile_no,
                        uuid
                    })
                })
                .then(response => response.json())
                .then(data => {
                    profileLoader(false);
                    if (data.success) {
                        $('#mobile_otp_block').show();
                        $('#mobile_otp').removeAttr('disabled');
                        $("#mobileNumber").attr('disabled', true);
                        $(".mobile_no").attr('required', true);
                        $('.mobilebutton').attr('disabled', true);
                    } else {
                        $(".mobile-error").text(data.message);
                    }
                })
                .catch(function() {
                    profileLoader(false);
                    errorMessage('Network error. Please try again.');
                });
        }

        function ResendOtp(type) {
            var uuid = '{{ $data->uuid }}';
            profileLoader(true, 'Resending…');
            let email = $('#email').val();
            let mobile = $('#mobileNumber').val();

            fetch('{{ route("resendOTP") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        email,
                        type,
                        uuid
                    })
                })
                .then(response => response.json())
                .then(data => {
                    profileLoader(false);
                    if (type == "Mobile") {
                        if (data.success) {
                            $('#mobile_otp_block').show();
                            $('#mobile_otp').removeAttr('disabled');
                        } else {
                            $(".mobile-error").text(data.message);
                        }
                    } else {
                        if (data.success) {
                            $('#email_otp_block').show();
                            $('#email_otp').removeAttr('disabled');
                        } else {
                            $(".email-error").text(data.message);
                        }
                    }
                })
                .catch(function() {
                    profileLoader(false);
                    errorMessage('Network error. Please try again.');
                });
        }

        $("#mobile_otp").on("input", function() {
            sanitize(this, 'b', 6);
            if ($(this).val().length == 6) {
                MobileOTPVerification();
            }
        });

        function MobileOTPVerification() {
            let otp = $("#mobile_otp").val();
            let mobile_no = $('.mobile_no').val();
            $(".mobileotpverify").text("");
            VerifyOTP(otp, 'Mobile', '', '', mobile_no, function(data) {
                console.log(data);
                if (data.success) {
                    console.log('hello');
                    $("#mobile_otp").attr('disabled', true);
                    $(".verifymobile_no").attr('disabled', true);
                    $(".resendMobileOtp").hide();
                    $(".mobile_no").attr('readonly', true);
                    btnenabled();
                } else {
                    $(".mobileotpverify").text(data.message);
                }
            });
        }

        $('#submitForm').click(function() {
            $('.error').remove();
            var formData = new FormData($('#profileForm')[0]);
            profileLoader(true, 'Saving profile…');
            $.ajax({
                url: '{{ route("register.updateProfile", $data->uuid) }}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    profileLoader(false);
                    successMessage(response.message);
                    setTimeout(() => {
                        window.location.href = "{{ route('hospital.dashboard') }}";
                    }, 1000);
                },
                error: function(xhr) {
                    profileLoader(false);
                    $('.error').remove();

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        for (let field in errors) {
                            $(`[name="${field}"]`).closest('.profileerror').after(
                                `<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                    } else {
                        errorMessage('Something went wrong. Please try again later.');
                    }
                }
            });
        });

        document.getElementById('uploadPicture').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('imagePreview');
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
        $("#hospital_type").on("change", function() {
            if ($(this).val() == 'Single') {
                $(".hospital-branch").addClass("d-none");
            } else {
                $(".hospital-branch").removeClass("d-none");
            }
        });
    </script>
</body>

</html>
