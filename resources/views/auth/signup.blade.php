@php
    $logo = App\Models\BusinessSetting::where('key', 'front_logo')->value('value');
    if ($logo) {
        $logo = asset('public/storage/' . $logo);
    } else {
        $logo = asset('public/front/assets/img/paracare-logo.png');
    }
    $siteTitle = App\Models\BusinessSetting::where('key', 'site_title')->value('value') ?: 'HIMS';
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="description" content="Hospital signup — {{ $siteTitle }}" />
    <link rel="icon" href="{{ asset('public/front/assets/images/favicon.png') }}" type="image/x-icon" />
    <link rel="shortcut icon" href="{{ $logo }}" type="image/x-icon" />
    <title>Hospital Signup — ParaCare+ HIMS v3.0 | {{ $siteTitle }}</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Noto+Sans+Devanagari:wght@400;600;700&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('public/front/assets/css/login.css') }}" />
    <link rel="stylesheet" href="{{ asset('public/front/assets/vendor/libs/toastr/toastr.css') }}" />
    <style>
        .signup-step {
            font-size: 11px;
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 999px;
            border: 1px solid var(--border);
            color: var(--muted);
            background: #f4f7fb;
            letter-spacing: .02em
        }

        .signup-step-active {
            border-color: var(--blue-l);
            color: var(--blue-l);
            background: rgba(21, 101, 192, .08)
        }

        .signup-step-done {
            border-color: var(--teal);
            color: var(--teal);
            background: rgba(0, 105, 92, .08)
        }

        .signup-step-todo {
            opacity: .75
        }

        .signup-hint {
            font-size: 11.5px;
            color: var(--muted);
            margin-top: 6px;
            line-height: 1.45
        }
    </style>
</head>

<body>

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
                    <text x="26" y="31" font-size="11" fill="#f9a825" text-anchor="middle" font-weight="900"
                        font-family="Inter,sans-serif">UK</text>
                    <path d="M8 44 Q26 50 44 44" fill="none" stroke="#c8a84b" stroke-width="1.2" />
                </svg>
            </div>
            <div class="gr-div"></div>
            <div class="gr-info">
                <div class="line1">Government of Uttarakhand - Department of Health &amp; Family Welfare</div>
                <div class="hindi" lang="en" style="font-family:'Inter',sans-serif">उत्तराखण्ड शासन · स्वास्थ्य एवं परिवार कल्याण विभाग</div>
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
                🏥 New hospital onboarding — complete email verification to continue registration &nbsp;|&nbsp;
                ParaCare+ HIMS v3.0 - Fully Integrated Hospital Information Management System &nbsp;|&nbsp;
                📋 Ayushman Bharat PMJAY integration active &nbsp;|&nbsp;
                ⚕️ ABDM compliant workflows | PHR linked records
            </span>
        </div>
        <span class="ann-badge">LIVE</span>
    </div>

    <div class="main-wrap">
        <div class="left-panel">
            <div class="hero-logo">🏥</div>
            <div class="hero-version">● ParaCare+ HIMS v3.0 - Enterprise Edition</div>
            <h1 class="hero-title">Onboard Your<br />Hospital on <span>HIMS</span></h1>
            <p class="hero-sub">Register your facility on the state platform with verified email and mobile — then
                complete hospital profile, departments, and user access in a guided flow.</p>

            <!-- <div class="feature-grid">
                <div class="feature-card"><span class="fc-icon">✉️</span>
                    <div>
                        <div class="fc-title">Verified Email</div>
                        <div class="fc-sub">OTP-based validation before account creation</div>
                    </div>
                </div>
                <div class="feature-card"><span class="fc-icon">📱</span>
                    <div>
                        <div class="fc-title">Mobile Linked</div>
                        <div class="fc-sub">Primary contact for alerts and recovery</div>
                    </div>
                </div>
                <div class="feature-card"><span class="fc-icon">🏛️</span>
                    <div>
                        <div class="fc-title">Govt. Platform</div>
                        <div class="fc-sub">Aligned with Uttarakhand SHIMS policies</div>
                    </div>
                </div>
                <div class="feature-card"><span class="fc-icon">🔐</span>
                    <div>
                        <div class="fc-title">Secure Pipeline</div>
                        <div class="fc-sub">Encrypted transport and audited access</div>
                    </div>
                </div>
                <div class="feature-card"><span class="fc-icon">🩺</span>
                    <div>
                        <div class="fc-title">Clinical Ready</div>
                        <div class="fc-sub">OPD/IPD, billing, lab, pharmacy modules</div>
                    </div>
                </div>
                <div class="feature-card"><span class="fc-icon">📊</span>
                    <div>
                        <div class="fc-title">Dashboard Next</div>
                        <div class="fc-sub">Continue setup after quick verification</div>
                    </div>
                </div>
                <div class="feature-card"><span class="fc-icon">🩸</span>
                    <div>
                        <div class="fc-title">Blood Bank</div>
                        <div class="fc-sub">8-group inventory, cross-match, donor registry</div>
                    </div>
                </div>
                <div class="feature-card"><span class="fc-icon">👥</span>
                    <div>
                        <div class="fc-title">HR &amp; Payroll</div>
                        <div class="fc-sub">Staff, attendance, rosters, salary, leave</div>
                    </div>
                </div>
            </div> -->

            <!-- <div class="stats-ticker">
                <div class="ticker-item"><span class="ticker-val" id="t1">247</span><span class="ticker-lab">Live
                        Patients</span></div>
                <div class="ticker-item"><span class="ticker-val" id="t2">48</span><span class="ticker-lab">Today
                        OPD</span></div>
                <div class="ticker-item"><span class="ticker-val" id="t3">12</span><span class="ticker-lab">Lab
                        Pending</span></div>
                <div class="ticker-item"><span class="ticker-val" id="t4">3</span><span class="ticker-lab">Ambulances
                        Active</span></div>
                <div class="ticker-item"><span class="ticker-val" id="t5">86%</span><span class="ticker-lab">Bed
                        Occupancy</span></div>
                <div class="ticker-item"><span class="ticker-val" id="t6">🟢</span><span class="ticker-lab">System
                        Health</span></div>
            </div> -->

            <!-- <div class="status-panel">
                <div class="sp-title">🟢 System Status</div>
                <div class="sp-grid">
                    <div class="sp-item">
                        <div class="sp-dot ok"></div><span class="sp-label">Core HIMS</span><span
                            class="sp-val">Operational</span>
                    </div>
                    <div class="sp-item">
                        <div class="sp-dot ok"></div><span class="sp-label">ABDM Gateway</span><span
                            class="sp-val">Synced</span>
                    </div>
                    <div class="sp-item">
                        <div class="sp-dot ok"></div><span class="sp-label">Onboarding API</span><span
                            class="sp-val">Active</span>
                    </div>
                    <div class="sp-item">
                        <div class="sp-dot ok"></div><span class="sp-label">Email OTP</span><span
                            class="sp-val">Live</span>
                    </div>
                    <div class="sp-item">
                        <div class="sp-dot ok"></div><span class="sp-label">Billing Engine</span><span
                            class="sp-val">Active</span>
                    </div>
                    <div class="sp-item">
                        <div class="sp-dot ok"></div><span class="sp-label">Backup (Last)</span><span
                            class="sp-val">02:00 AM</span>
                    </div>
                    <div class="sp-item">
                        <div class="sp-dot ok"></div><span class="sp-label">Ambulance GPS</span><span
                            class="sp-val">Live</span>
                    </div>
                    <div class="sp-item">
                        <div class="sp-dot ok"></div><span class="sp-label">Registration</span><span
                            class="sp-val">Open</span>
                    </div>
                </div>
            </div> -->
        </div>

        <div class="right-panel">
            <div class="login-card" style="position:relative">
                <div class="login-loading" id="signupLoading" aria-live="polite" aria-busy="false">
                    <div class="ld-spinner"></div>
                    <div class="ld-text" id="signupLoadingText">Please wait…</div>
                </div>

                <div class="card-top">
                    <div style="font-size:32px">🏥</div>
                    <div class="card-top-title">ParaCare+ HIMS — Hospital Signup</div>
                    <div class="card-top-sub">Enter your email, verify the OTP we send, then add your mobile number. Each step unlocks the next.</div>
                    <div class="facility-badge">🏥 New facility onboarding | Govt. of Uttarakhand</div>
                </div>

                <div class="card-body" style="max-height:min(720px,calc(100vh - 280px));overflow-y:auto">
                    <form id="loginForm" method="POST" class="theme-form" onsubmit="return false;">
                        @csrf
                        <input type="hidden" id="hospital_name" name="hospital_name" value="" />

                        <div id="signupStepStrip" style="display:flex;gap:8px;margin-bottom:18px;flex-wrap:wrap;align-items:center">
                            <div id="stepPill1" class="signup-step signup-step-active">1 · Email</div>
                            <div id="stepPill2" class="signup-step signup-step-todo">2 · OTP</div>
                            <div id="stepPill3" class="signup-step signup-step-todo">3 · Mobile</div>
                        </div>

                        <div class="form-group submiterror" id="emailStepWrap">
                            <label class="form-label" for="emailInput">Work email <span class="req">*</span></label>
                            <div class="input-wrap">
                                <span class="input-prefix">✉️</span>
                                <input type="text" name="email" id="emailInput"
                                    oninput="sanitize(this, 'email'); syncSignupProgressiveUI();"
                                    class="form-input has-prefix email" placeholder="name@hospital.org"
                                    autocomplete="email" />
                            </div>
                            <p class="signup-hint" id="emailStepHint">Enter your official work email address. </p>
                            <div id="emailSendWrap" style="display:none;margin-top:12px">
                                <button class="login-btn emailbutton" type="button" onclick="CheckEmail();"
                                    style="width:100%;font-size:14px">📧 Send Email OTP</button>
                            </div>
                            <span class="email-error" style="display:block;margin-top:6px;font-size:12px;color:#e57373"></span>
                        </div>

                        <div class="form-group submiterror" id="email_otp_block" style="display:none">
                            <label class="form-label" for="email_otp">Email OTP <span class="req">*</span></label>
                            <p class="signup-hint" style="margin-bottom:8px">Check your inbox and spam folder. Enter all 6 digits.</p>
                            <div class="input-wrap">
                                <span class="input-prefix">🔢</span>
                                <input type="text" name="email_otp" id="email_otp" class="form-input has-prefix"
                                    placeholder="6-digit OTP" maxlength="6" inputmode="numeric" autocomplete="one-time-code" />
                            </div>
                            <div style="margin-top:8px;text-align:right">
                                <button type="button" class="cfa-link resendEmailOtp" style="font-size:12px;border:none;background:none;cursor:pointer;padding:0;font-family:inherit"
                                    onclick="ResendOtp('Email')">Resend OTP</button>
                            </div>
                            <span class="emailotpverify" style="display:block;margin-top:6px;font-size:12px;color:#e57373"></span>
                        </div>

                        <div class="form-group submiterror" id="mobileFieldWrap" style="display:none">
                            <label class="form-label" for="mobileInput">Mobile <span class="req">*</span></label>
                            <p class="signup-hint" style="margin-bottom:8px">Email verified. Enter your 10-digit mobile number for alerts and account recovery.</p>
                            <div class="input-wrap">
                                <span class="input-prefix">📱</span>
                                <input type="text" name="mobile_no" id="mobileInput" class="form-input has-prefix mobile_no"
                                    oninput="validateMobileNo(this); syncSignupProgressiveUI();" placeholder="10-digit mobile" maxlength="10"
                                    inputmode="numeric" autocomplete="tel" disabled />
                            </div>
                            <span class="mobile-error" style="display:block;margin-top:6px;font-size:12px;color:#e57373"></span>
                        </div>

                        <div id="signupActionWrap" style="display:none;margin-top:4px">
                            <p class="signup-hint" style="margin-bottom:10px"><strong>Complete Signup</strong> to continue.</p>
                            <button class="login-btn submitbtn" disabled onclick="submitData();" type="button"
                                style="width:100%;opacity:.65">✅ Complete Signup</button>
                        </div>

                        <div style="text-align:center;margin-top:16px">
                            <span style="font-size:12px;color:var(--muted)">Already have an account?</span>
                            <a href="{{ route('hospital.login') }}" class="cfa-link" style="font-size:13px;margin-left:6px">Secure
                                Login</a>
                        </div>
                    </form>
                </div>

                <div class="card-footer-bar">
                    <div class="cfa-links">
                        <a class="cfa-link" href="{{ route('hospital.login') }}">🔑 Back to Login</a>
                        <button class="cfa-link" type="button">📞 IT Support</button>
                        <button class="cfa-link" type="button">📋 User Manual</button>
                    </div>
                    <div class="security-badge">🔒 AES-256 Encrypted</div>
                </div>
            </div>

            <div style="margin-top:16px;text-align:center">
                <div style="font-size:11px;color:#5a7a8e">ParaCare+ HIMS v3.0.0 - Build 2024.12 | ABDM Compliant | ISO
                    27001</div>
                <div style="font-size:10.5px;color:#3d5a6e;margin-top:3px">NIC Uttarakhand | Last Updated: April 2025
                </div>
            </div>
        </div>
    </div>

    <div class="page-footer">
        <div class="pf-left">© 2025 Government of Uttarakhand, Department of Health &amp; Family Welfare. All rights
            reserved.</div>
        <div class="pf-right">
            <a class="pf-link" href="#">Privacy Policy</a>
            <a class="pf-link" href="#">Terms of Use</a>
            <a class="pf-link" href="#">Accessibility</a>
            <a class="pf-link" href="#">Contact</a>
            <a class="pf-link" href="#">Sitemap</a>
        </div>
    </div>

    <script src="{{ asset('public/front/assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('public/front/assets/vendor/libs/toastr/toastr.js') }}"></script>
    <script src="{{ asset('public/front/assets/js/sanitize.js') }}"></script>

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

        setInterval(() => {
            const t1 = document.getElementById('t1');
            if (t1) t1.textContent = Math.floor(240 + Math.random() * 15);
            const t2 = document.getElementById('t2');
            if (t2) t2.textContent = Math.floor(44 + Math.random() * 10);
        }, 5000);

        function signupLoaderShow(msg) {
            const el = document.getElementById('signupLoading');
            const tx = document.getElementById('signupLoadingText');
            if (tx && msg) tx.textContent = msg;
            if (el) {
                el.classList.add('show');
                el.setAttribute('aria-busy', 'true');
            }
        }

        function signupLoaderHide() {
            const el = document.getElementById('signupLoading');
            if (el) {
                el.classList.remove('show');
                el.setAttribute('aria-busy', 'false');
            }
        }

        $(document).ready(function() {
            $("#emailInput").focus();
            setSignupStepPills(1);
            syncSignupProgressiveUI();
        });

        function setSignupStepPills(active) {
            [1, 2, 3].forEach(function(n) {
                const el = document.getElementById('stepPill' + n);
                if (!el) return;
                el.classList.remove('signup-step-active', 'signup-step-done', 'signup-step-todo');
                if (n < active) el.classList.add('signup-step-done');
                else if (n === active) el.classList.add('signup-step-active');
                else el.classList.add('signup-step-todo');
            });
        }

        function syncSignupProgressiveUI() {
            const $inp = $('#emailInput');
            const emailLocked = $inp.prop('disabled');
            const hasEmailText = $.trim($inp.val()).length > 0;
            if (!emailLocked && hasEmailText) {
                $('#emailSendWrap').stop(true, true).fadeIn(150);
            } else {
                $('#emailSendWrap').hide();
            }
            syncSubmitEnabled();
        }

        function syncSubmitEnabled() {
            if (!$('#signupActionWrap').is(':visible')) return;
            const digits = $.trim($('#mobileInput').val()).replace(/\D/g, '');
            const ok = digits.length === 10;
            $('.submitbtn').prop('disabled', !ok);
            $('.submitbtn').css('opacity', ok ? 1 : 0.65);
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

        $("#email_otp").on("input", function() {
            sanitize(this, 'b', 6);
            if ($(this).val().length == 6) {
                OTPVerification();
            }
        });

        $("#mobileInput").on("input", function() {
            sanitize(this, 'b', 10);
            if ($(this).val().length == 10) {
                $(".submitbtn").focus();
            }
            syncSubmitEnabled();
        });

        function validateMobileNo(input) {
            input.value = input.value.replace(/[^0-9]/g, '');
            if (input.value.length > 10) {
                input.value = input.value.slice(0, 10);
            }
        }

        function CheckEmail() {
            signupLoaderShow('Sending email OTP…');
            let email = $('#emailInput').val();
            $(".email-error").text("");
            fetch('{{ route("sendEmailMail") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        email
                    })
                })
                .then(response => response.json())
                .then(data => {
                    signupLoaderHide();
                    if (data.success) {
                        $('#email_otp_block').show();
                        $('#email_otp').removeAttr('disabled');
                        $("#emailInput").attr('disabled', true);
                        $('.emailbutton').attr('disabled', true);
                        $('#emailSendWrap').hide();
                        $('#emailStepHint').text('We have sent an OTP to this email. Enter the 6-digit code below.');
                        setSignupStepPills(2);
                        $("#email_otp").focus();
                        successMessage(data.message);
                        syncSignupProgressiveUI();
                    } else {
                        $(".email-error").text(data.message);
                        errorMessage(data.message);
                        syncSignupProgressiveUI();
                    }
                })
                .catch(function() {
                    signupLoaderHide();
                    errorMessage('Network error. Please try again.');
                    syncSignupProgressiveUI();
                });
        }

        function ResendOtp(type) {
            signupLoaderShow('Resending OTP…');
            let email = $('#emailInput').val();
            if (type == "Aadhaar") {
                email = $('#aadhaar_no').val();
            }

            fetch('{{ route("resendOTP") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        email,
                        type
                    })
                })
                .then(response => response.json())
                .then(data => {
                    signupLoaderHide();
                    if (data.success) {
                        $('#email_otp_block').show();
                        $('#email_otp').removeAttr('disabled');
                        if (type == "Aadhaar") {
                            $('#reference_id').val(data.reference_id);
                        }
                        successMessage(data.message);
                    } else {
                        $(".email-error").text(data.message);
                        errorMessage(data.message);
                    }
                })
                .catch(function() {
                    signupLoaderHide();
                    errorMessage('Network error. Please try again.');
                });
        }

        function VerifyOTP(otp, type, email = '', callback) {
            signupLoaderShow('Verifying OTP…');
            fetch('{{ route("verifyEmailOtp") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        otp,
                        type,
                        email
                    })
                })
                .then(response => response.json())
                .then(data => {
                    signupLoaderHide();
                    if (callback && typeof callback === 'function') {
                        callback(data);
                    }
                })
                .catch(function() {
                    signupLoaderHide();
                    errorMessage('Network error. Please try again.');
                });
        }

        function OTPVerification() {
            let otp = $("#email_otp").val();
            let email = $('#emailInput').val();
            $(".emailotpverify").text("");
            VerifyOTP(otp, 'Email', email, function(data) {
                if (data.success) {
                    $("#email_otp").attr('disabled', true);
                    $(".emailbutton").removeClass('btn-default').addClass('btn-success');
                    $(".resendEmailOtp").hide();
                    $(".mobile_no").removeAttr('disabled');
                    $(".mobilebutton").removeAttr('disabled');
                    $('#mobileFieldWrap').slideDown(200);
                    $('#signupActionWrap').slideDown(200);
                    $('#emailStepHint').text('Your email is verified.');
                    setSignupStepPills(3);
                    $(".submitbtn").prop('disabled', true);
                    $(".submitbtn").removeClass('btn-success');
                    $(".submitbtn").removeClass('btn-secondary');
                    $(".submitbtn").css({
                        opacity: 0.65
                    });
                    $("#mobileInput").focus();
                    successMessage(data.message);
                    syncSubmitEnabled();
                } else {
                    $(".emailotpverify").text(data.message);
                    errorMessage(data.message);
                }
            });
        }

        function submitData() {
            signupLoaderShow('Submitting registration…');
            const formData = {
                hospital_name: $('#hospital_name').val(),
                email: $('#emailInput').val(),
                email_otp: $('#email_otp').val(),
                mobile_no: $('.mobile_no').val(),
            };

            $.ajax({
                url: '{{ route("register.store") }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                contentType: 'application/json',
                data: JSON.stringify(formData),
                success: function(response) {
                    signupLoaderHide();
                    if (response.success) {
                        window.location.href = response.route;
                    } else {
                        alert("Registration failed: " + response.message);
                    }
                },
                error: function(xhr) {
                    signupLoaderHide();
                    $('.error').remove();

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        for (let field in errors) {
                            $(`[name="${field}"]`).closest('.submiterror').after(
                                `<div class="error text-danger" style="font-size:12px;margin-top:4px">${errors[field][0]}</div>`
                            );
                        }
                    } else {
                        alert('Something went wrong. Please try again later.');
                    }
                }
            });
        }
    </script>
</body>

</html>
