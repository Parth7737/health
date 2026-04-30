@php
$roleList = collect($loginRoles ?? [])->values();
if ($roleList->isEmpty()) {
$roleList = \App\Models\Role::query()->orderBy('name')->get(['name']);
}

$roleEmojiMap = [
'state super admin' => '🏛️',
'admin' => '👨‍💼',
'administrator' => '👨‍💼',
'doctor' => '🩺',
'nurse' => '👩‍⚕️',
'billing staff' => '💳',
'pharmacy' => '💊',
'pharmacist' => '💊',
'lab' => '🧪',
'laboratory' => '🧪',
'radiology' => '🩻',
'ambulance' => '🚑',
'bloodbank' => '🩸',
'inventory' => '📦',
'hr' => '👥',
];
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>ParaCare+ HIMS v3.0 - Secure Login | Govt. of Uttarakhand</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Noto+Sans+Devanagari:wght@400;600;700&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('public/front/assets/css/login.css') }}" />
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
                    <text x="26" y="31" font-size="15" fill="#f9a825" text-anchor="middle" font-weight="900"
                        font-family="serif">अ</text>
                    <path d="M8 44 Q26 50 44 44" fill="none" stroke="#c8a84b" stroke-width="1.2" />
                </svg>
            </div>
            <div class="gr-div"></div>
            <div class="gr-info">
                <div class="line1">Government of Uttarakhand - Department of Health &amp; Family Welfare</div>
                <div class="hindi">उत्तराखण्ड शासन · स्वास्थ्य एवं परिवार कल्याण विभाग</div>
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
                🏥 ParaCare+ HIMS v3.0 - Fully Integrated Hospital Information Management System &nbsp;|&nbsp;
                💉 National Vaccination Drive ongoing - Update records in the Certificate module &nbsp;|&nbsp;
                📋 Ayushman Bharat PMJAY integration active - Verify beneficiary status in Billing &nbsp;|&nbsp;
                🩸 Blood Bank Alert: O- stock critically low - Please arrange donations &nbsp;|&nbsp;
                📞 System Support: 1800-XXX-XXXX (Toll Free) | helpdesk@hims.uk.gov.in &nbsp;|&nbsp;
                ⚕️ All clinical workflows now ABDM compliant | PHR linked records active
            </span>
        </div>
        <span class="ann-badge">LIVE</span>
    </div>

    <div class="main-wrap">
        <div class="left-panel">
            <div class="hero-logo">🏥</div>
            <div class="hero-version">● ParaCare+ HIMS v3.0 - Enterprise Edition</div>
            <h1 class="hero-title">World-Class<br />Healthcare <span>Management</span></h1>
            <p class="hero-sub">An end-to-end, AI-enabled Hospital Information System for the Government of Uttarakhand
                - integrating all clinical, financial, and administrative workflows into a single unified platform.</p>

            <div class="feature-grid">
                <div class="feature-card"><span class="fc-icon">🩺</span>
                    <div>
                        <div class="fc-title">Complete OPD/IPD</div>
                        <div class="fc-sub">Registration, tokens, bed management, ADT workflows</div>
                    </div>
                </div>
                <div class="feature-card"><span class="fc-icon">💊</span>
                    <div>
                        <div class="fc-title">Integrated Pharmacy</div>
                        <div class="fc-sub">Dispensing, inventory, expiry alerts, MAR</div>
                    </div>
                </div>
                <div class="feature-card"><span class="fc-icon">🧪</span>
                    <div>
                        <div class="fc-title">LIS / Pathology</div>
                        <div class="fc-sub">Sample lifecycle, auto-flagging, critical alerts</div>
                    </div>
                </div>
                <div class="feature-card"><span class="fc-icon">🩻</span>
                    <div>
                        <div class="fc-title">Radiology RIS/PACS</div>
                        <div class="fc-sub">Worklist, reporting, AI findings, DICOM</div>
                    </div>
                </div>
                <div class="feature-card"><span class="fc-icon">💳</span>
                    <div>
                        <div class="fc-title">Financial Management</div>
                        <div class="fc-sub">Billing, insurance, AB-PMJAY, revenue analytics</div>
                    </div>
                </div>
                <div class="feature-card"><span class="fc-icon">🚑</span>
                    <div>
                        <div class="fc-title">Live Ambulance Map</div>
                        <div class="fc-sub">GPS tracking, dispatch, ETA, fleet management</div>
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
                        <div class="fc-title">HR & Payroll</div>
                        <div class="fc-sub">Staff, attendance, rosters, salary, leave</div>
                    </div>
                </div>
            </div>

            <div class="stats-ticker">
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
            </div>


            <!-- System Status -->
            <div class="status-panel">
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
                        <div class="sp-dot ok"></div><span class="sp-label">Lab Interface</span><span
                            class="sp-val">Connected</span>
                    </div>
                    <div class="sp-item">
                        <div class="sp-dot ok"></div><span class="sp-label">Pharmacy LIS</span><span
                            class="sp-val">Active</span>
                    </div>
                    <div class="sp-item">
                        <div class="sp-dot warn"></div><span class="sp-label">RIS/PACS</span><span
                            class="sp-val">Partial</span>
                    </div>
                    <div class="sp-item">
                        <div class="sp-dot ok"></div><span class="sp-label">Ambulance GPS</span><span
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
                </div>
            </div>
        </div>

        <div class="right-panel">
            <div class="login-card" style="position:relative">
                <div class="login-loading" id="loginLoading">
                    <div class="ld-spinner"></div>
                    <div class="ld-text">Authenticating... Please wait</div>
                </div>

                <div class="card-top">
                    <div style="font-size:32px">🏥</div>
                    <div class="card-top-title">ParaCare+ HIMS - Secure Login</div>
                    <div class="card-top-sub">Hospital Information Management System</div>
                    <div class="facility-badge">🏥 District Hospital Dehradun | ID: DHD-01</div>
                </div>

                <div class="card-body">
                    @if(session('status'))
                    <div class="form-alert success">✅ <span>{{ session('status') }}</span></div>
                    @endif

                    @if($errors->any())
                    <div class="form-alert error" id="loginErrorAlert">
                        ⚠️
                        <span id="loginErrorText">{{ $errors->first('role') ?: $errors->first('email') ?: $errors->first('password') ?: $errors->first() }}</span>
                    </div>
                    @else
                    <div class="form-alert error" id="loginErrorAlert" style="display:none">
                        ⚠️
                        <span id="loginErrorText"></span>
                    </div>
                    @endif

                    <div class="role-section-title">Select Your Role</div>
                    <div class="role-grid" id="roleGrid">
                        @forelse($roleList as $index => $role)
                        @php
                        $roleName = (string) $role->name;
                        $roleKey = strtolower($roleName);
                        $icon = $roleEmojiMap[$roleKey] ?? '👤';
                        $desc = $roleName . ' Access';
                        @endphp
                        <button class="role-btn {{ $index === 0 ? 'selected' : '' }}" type="button"
                            data-role="{{ $roleName }}" onclick="selectRole('{{ addslashes($roleName) }}', this)">
                            <span class="role-btn-icon">{{ $icon }}</span>
                            <span>
                                <span class="role-btn-name">{{ $roleName }}</span>
                                <span class="role-btn-desc">{{ $desc }}</span>
                            </span>
                        </button>
                        @empty
                        <button class="role-btn selected" type="button" data-role="Administrator"
                            onclick="selectRole('Administrator', this)">
                            <span class="role-btn-icon">👨‍💼</span>
                            <span>
                                <span class="role-btn-name">Administrator</span>
                                <span class="role-btn-desc">Full system access</span>
                            </span>
                        </button>
                        @endforelse
                    </div>

                    <form method="POST" action="{{ route('portal.login') }}" id="loginForm" novalidate>
                        @csrf
                        <input type="hidden" name="role" id="selectedRole" value="{{ old('role') }}">

                        <div class="form-group">
                            <label class="form-label" for="email">Employee ID / Email <span class="req">*</span></label>
                            <div class="input-wrap">
                                <span class="input-prefix">👤</span>
                                <input type="text"
                                    class="form-input has-prefix {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                    id="email" name="email" placeholder="e.g. admin@uk.gov.in"
                                    value="{{ old('email') }}" autocomplete="username" required />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="password">Password <span class="req">*</span></label>
                            <div class="input-wrap">
                                <span class="input-prefix">🔒</span>
                                <input type="password"
                                    class="form-input has-prefix {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                    id="password" name="password" placeholder="Enter your password"
                                    autocomplete="current-password" style="padding-right:44px" required />
                                <button class="pass-toggle" id="passToggle" onclick="togglePass()"
                                    type="button">👁️</button>
                            </div>
                        </div>

                        <div
                            style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;gap:8px;flex-wrap:wrap">
                            <label style="display:flex;align-items:center;gap:6px;cursor:pointer">
                                <input type="checkbox" id="remember" name="remember"
                                    {{ old('remember') ? 'checked' : '' }}
                                    style="accent-color:var(--blue-l);width:14px;height:14px" />
                                <span style="font-size:12px;color:var(--muted)">Remember me</span>
                            </label>
                            <button type="button" class="cfa-link" style="font-size:12px"
                                onclick="toggleForgotPanel()">Forgot Password?</button>
                        </div>

                        <button class="login-btn" id="loginBtn" type="submit">🔐 Secure Login to HIMS</button>
                    </form>

                    <div class="forgot-panel" id="forgotPanel">
                        <div class="forgot-panel-title">Reset Password Link</div>
                        <form method="POST" action="{{ route('password.email') }}" id="forgotForm">
                            @csrf
                            <div class="form-group" style="margin-bottom:10px">
                                <label class="form-label" for="forgot_email">Registered Email <span
                                        class="req">*</span></label>
                                <input type="email" class="form-input" id="forgot_email" name="email"
                                    value="{{ old('email') }}" placeholder="Enter registered email" required />
                            </div>
                            <button type="submit" class="login-btn" style="padding:10px;font-size:12px">📧 Send Reset
                                Link</button>
                        </form>
                    </div>

                    <div class="divider">
                        <hr /><span>Quick Demo Fill</span>
                        <hr />
                    </div>

                    <div class="demo-title">Fill Demo Credentials</div>
                    <div class="demo-btns">
                        <button class="demo-btn" type="button" onclick="demoFill('admin@uk.gov.in')">👨‍💼
                            Admin</button>
                        <button class="demo-btn" type="button" onclick="demoFill('doctor@uk.gov.in')">🩺 Doctor</button>
                        <button class="demo-btn" type="button" onclick="demoFill('nurse@uk.gov.in')">👩‍⚕️
                            Nurse</button>
                        <button class="demo-btn" type="button" onclick="demoFill('billing@uk.gov.in')">💳
                            Billing</button>
                        <button class="demo-btn" type="button" onclick="demoFill('pharmacy@uk.gov.in')">💊
                            Pharmacy</button>
                        <button class="demo-btn" type="button" onclick="demoFill('lab@uk.gov.in')">🧪 Lab</button>
                    </div>
                </div>

                <div class="card-footer-bar">
                    <div class="cfa-links">
                        <a class="cfa-link" href="{{ route('password.request') }}">🔑 Reset Password</a>
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

    function togglePass() {
        const inp = document.getElementById('password');
        const btn = document.getElementById('passToggle');
        if (!inp || !btn) return;
        if (inp.type === 'password') {
            inp.type = 'text';
            btn.textContent = '🙈';
        } else {
            inp.type = 'password';
            btn.textContent = '👁️';
        }
    }

    function selectRole(role, btn) {
        document.querySelectorAll('.role-btn').forEach((b) => b.classList.remove('selected'));
        if (btn) btn.classList.add('selected');
        const hidden = document.getElementById('selectedRole');
        if (hidden) hidden.value = role || '';
    }

    function demoFill(email) {
        const emailInput = document.getElementById('email');
        const passInput = document.getElementById('password');
        if (emailInput) emailInput.value = email;
        if (passInput) passInput.value = 'admin123';
    }

    function toggleForgotPanel() {
        const panel = document.getElementById('forgotPanel');
        if (!panel) return;
        panel.classList.toggle('show');
        if (panel.classList.contains('show')) {
            const forgotInput = document.getElementById('forgot_email');
            const loginEmail = document.getElementById('email');
            if (forgotInput && loginEmail && loginEmail.value) {
                forgotInput.value = loginEmail.value;
            }
        }
    }

    (function preselectRole() {
        const selected = document.querySelector('.role-btn.selected');
        if (selected) {
            const role = selected.getAttribute('data-role') || '';
            const hidden = document.getElementById('selectedRole');
            if (hidden && !hidden.value) hidden.value = role;
        }
    })();

    (function ajaxLoginSubmit() {
        const form = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');
        const loadingOverlay = document.getElementById('loginLoading');
        const errorBox = document.getElementById('loginErrorAlert');
        const errorText = document.getElementById('loginErrorText');
        const endpoint = @json(route('portal.login'));
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (!form || !loginBtn || !endpoint || !csrfToken) return;

        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            if (errorBox && errorText) {
                errorBox.style.display = 'none';
                errorText.textContent = '';
            }

            const role = document.getElementById('selectedRole')?.value?.trim();
            const email = document.getElementById('email')?.value?.trim();
            const password = document.getElementById('password')?.value || '';
            const remember = document.getElementById('remember')?.checked;

            if (!role) {
                if (errorBox && errorText) {
                    errorText.textContent = 'Please select a role before login.';
                    errorBox.style.display = 'flex';
                }
                return;
            }

            loginBtn.disabled = true;
            if (loadingOverlay) loadingOverlay.classList.add('show');

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        email,
                        password,
                        role,
                        remember: !!remember,
                    }),
                });

                const data = await response.json();

                if (!response.ok || !data?.success) {
                    const roleErr = data?.errors?.role?.[0];
                    const emailErr = data?.errors?.email?.[0];
                    const passErr = data?.errors?.password?.[0];
                    const genericErr = data?.message || 'Unable to login. Please check credentials.';
                    const message = roleErr || emailErr || passErr || genericErr;
                    if (errorBox && errorText) {
                        errorText.textContent = message;
                        errorBox.style.display = 'flex';
                    }
                    return;
                }

                if (data?.url) {
                    window.location.href = data.url;
                    return;
                }

                window.location.reload();
            } catch (err) {
                if (errorBox && errorText) {
                    errorText.textContent = 'Network error. Please try again.';
                    errorBox.style.display = 'flex';
                }
            } finally {
                if (loadingOverlay) loadingOverlay.classList.remove('show');
                loginBtn.disabled = false;
            }
        });
    })();

    setInterval(() => {
        const t1 = document.getElementById('t1');
        if (t1) t1.textContent = Math.floor(240 + Math.random() * 15);
        const t2 = document.getElementById('t2');
        if (t2) t2.textContent = Math.floor(44 + Math.random() * 10);
    }, 5000);
    </script>
</body>

</html>