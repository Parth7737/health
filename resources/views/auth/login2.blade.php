<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ParaCare+ HMIS | Secure Login — Govt. of Uttarakhand</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Noto+Sans+Devanagari:wght@400;600;700&display=swap" rel="stylesheet"/>
  <style>
    /* ── Variables ─────────────────────────────────────── */
    :root{
      --navy:#071221; --navy2:#0a1628; --blue:#003580;
      --blue-mid:#0b4ea8; --blue-l:#1565c0;
      --teal:#00695c; --teal-l:#00897b;
      --saffron:#e65100; --saffron-l:#f57c00;
      --gold:#f9a825;
      --success:#2e7d32; --success-l:#e8f5e9;
      --danger:#c62828;  --danger-l:#ffebee;
      --warning:#e65100; --warning-l:#fff3e0;
      --surface:#fff; --border:#d0dce8;
      --text:#0d1b2a; --muted:#5a7894; --light:#8aa4bc;
      --card-radius:20px; --input-radius:9px;
      --transition:.18s ease;
    }
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    html,body{height:100%;font-family:'Inter',sans-serif;scroll-behavior:smooth}

    body{
      background:var(--navy);
      display:flex;flex-direction:column;min-height:100vh;
      position:relative;overflow-x:hidden;
    }

    /* ── BACKGROUND ─────────────────────────────────────── */
    .bg-banner{
      position:fixed;inset:0;z-index:0;
      background:url('{{ asset('public/front/assets/images/login-banner.jpg') }}') center/cover no-repeat;
    }
    .bg-overlay{
      position:fixed;inset:0;z-index:1;
      background:
        linear-gradient(to right,
          rgba(7,18,33,.96) 0%,
          rgba(7,18,33,.88) 35%,
          rgba(7,18,33,.55) 65%,
          rgba(7,18,33,.40) 100%),
        linear-gradient(to bottom,
          rgba(7,18,33,.5) 0%,
          rgba(7,18,33,.1) 40%,
          rgba(7,18,33,.5) 100%);
    }
    .bg-grid{
      position:fixed;inset:0;z-index:2;
      background-image:
        linear-gradient(rgba(255,255,255,.018) 1px,transparent 1px),
        linear-gradient(90deg,rgba(255,255,255,.018) 1px,transparent 1px);
      background-size:48px 48px;
    }

    /* floating particles */
    .particles{position:fixed;inset:0;z-index:2;overflow:hidden;pointer-events:none}
    .particle{
      position:absolute;border-radius:50%;
      animation:floatUp linear infinite;
      opacity:0;
    }
    @keyframes floatUp{
      0%  {transform:translateY(0) scale(0);opacity:0}
      10% {opacity:.6}
      90% {opacity:.2}
      100%{transform:translateY(-100vh) scale(1.5);opacity:0}
    }

    /* ── TOP GOV BAR ─────────────────────────────────────── */
    .login-topbar{
      position:relative;z-index:20;
      background:rgba(7,18,33,.92);
      backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);
      border-bottom:3px solid var(--saffron);
      padding:9px 28px;
      display:flex;align-items:center;justify-content:space-between;
      box-shadow:0 2px 24px rgba(0,0,0,.5);
    }
    .topbar-left{display:flex;align-items:center;gap:14px}
    .gov-seal{width:48px;height:48px;flex-shrink:0;filter:drop-shadow(0 2px 6px rgba(0,0,0,.5))}
    .gov-seal svg{width:100%;height:100%}
    .tb-block .t1{font-size:11px;font-weight:700;color:#c8dff0;letter-spacing:.07em;text-transform:uppercase}
    .tb-block .t2{font-size:11.5px;font-weight:600;color:#e2eff8}
    .tb-block .t3{font-size:9.5px;color:#6a8a9e;font-style:italic}
    .tb-block .hindi{font-family:'Noto Sans Devanagari',sans-serif;font-size:11px;color:#a0c0d8;font-weight:600}
    .tb-div{width:1px;height:36px;background:rgba(255,255,255,.1);flex-shrink:0}
    .topbar-right{display:flex;align-items:center;gap:10px}
    .tb-badge{
      padding:4px 10px;border-radius:20px;font-size:10.5px;font-weight:600;
      display:flex;align-items:center;gap:6px;
    }
    .tb-badge.green{background:rgba(46,125,50,.18);border:1px solid rgba(46,125,50,.3);color:#66bb6a}
    .tb-badge.blue {background:rgba(21,101,192,.18);border:1px solid rgba(21,101,192,.3);color:#7ab0e0}
    .tb-badge.gray {background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);color:#8aa8c0}
    .live-dot{width:6px;height:6px;border-radius:50%;background:#43a047;animation:ldPulse 1.8s infinite}
    @keyframes ldPulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.4;transform:scale(1.4)}}

    /* datetime strip */
    .dt-strip{
      background:rgba(7,18,33,.7);
      border-bottom:1px solid rgba(255,255,255,.05);
      padding:4px 28px;
      display:flex;align-items:center;justify-content:space-between;
      font-size:10px;color:#4a6880;position:relative;z-index:20;
    }
    .dt-strip span{display:flex;align-items:center;gap:6px}

    /* ── MAIN LAYOUT ─────────────────────────────────────── */
    .login-main{
      flex:1;position:relative;z-index:10;
      display:flex;align-items:center;justify-content:center;
      padding:24px 20px;
    }
    .login-wrapper{
      display:grid;grid-template-columns:1fr 420px;
      gap:52px;max-width:960px;width:100%;
      align-items:center;
    }

    /* ── LEFT INFO PANEL ─────────────────────────────────── */
    .info-panel{color:white}
    .portal-badge{
      display:inline-flex;align-items:center;gap:8px;
      background:rgba(230,81,0,.15);border:1px solid rgba(230,81,0,.3);
      border-radius:20px;padding:5px 14px;
      font-size:10.5px;font-weight:700;color:#ffab40;
      letter-spacing:.06em;text-transform:uppercase;margin-bottom:16px;
    }
    .info-panel h1{
      font-size:38px;font-weight:900;letter-spacing:-.04em;
      line-height:1.05;color:#e8f2fb;margin-bottom:6px;
    }
    .info-panel h1 .hl{
      background:linear-gradient(90deg,#1e88e5,#00897b);
      -webkit-background-clip:text;-webkit-text-fill-color:transparent;
      background-clip:text;
    }
    .info-panel .tagline{
      font-size:13px;color:#5a80a0;font-weight:400;
      margin-bottom:24px;line-height:1.6;
    }

    /* Stats row */
    .stats-row{
      display:grid;grid-template-columns:repeat(3,1fr);gap:10px;
      margin-bottom:24px;
    }
    .stat-box{
      background:rgba(255,255,255,.05);
      border:1px solid rgba(255,255,255,.08);
      border-radius:10px;padding:10px 12px;
      text-align:center;
    }
    .stat-box .sv{font-size:20px;font-weight:800;color:#d0e8fb;letter-spacing:-.03em;line-height:1}
    .stat-box .sl{font-size:9.5px;color:#4a6880;font-weight:600;text-transform:uppercase;letter-spacing:.04em;margin-top:3px}

    /* Feature list */
    .feature-list{display:flex;flex-direction:column;gap:8px;margin-bottom:24px}
    .feature-item{
      display:flex;align-items:flex-start;gap:10px;
      font-size:12.5px;color:#7eaac8;line-height:1.4;
    }
    .f-check{
      width:20px;height:20px;border-radius:50%;flex-shrink:0;
      background:rgba(46,125,50,.2);border:1px solid rgba(46,125,50,.35);
      display:flex;align-items:center;justify-content:center;
      font-size:10px;color:#66bb6a;margin-top:1px;
    }

    /* Cert chips */
    .cert-row{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:20px}
    .cert-chip{
      padding:3px 10px;border-radius:6px;
      background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.09);
      font-size:10px;color:#5a7894;font-weight:500;
    }

    /* Module icons strip */
    .modules-strip{
      display:flex;align-items:center;gap:8px;flex-wrap:wrap;
    }
    .mod-pill{
      display:flex;align-items:center;gap:5px;
      padding:4px 10px;border-radius:20px;
      background:rgba(21,101,192,.12);border:1px solid rgba(21,101,192,.2);
      font-size:10.5px;color:#6a9cbd;
    }

    /* ── LOGIN CARD ──────────────────────────────────────── */
    .login-card{
      background:rgba(255,255,255,.98);
      border-radius:var(--card-radius);
      box-shadow:0 32px 80px rgba(0,0,0,.45),0 0 0 1px rgba(255,255,255,.12);
      overflow:hidden;
      animation:cardSlide .5s ease both;
    }
    @keyframes cardSlide{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:none}}
    .card-accent-bar{
      height:4px;
      background:linear-gradient(90deg,var(--saffron) 0%,var(--gold) 40%,var(--teal-l) 80%,var(--blue-l) 100%);
    }
    .card-inner{padding:26px 28px 28px}

    /* Card logo */
    .card-logo{display:flex;align-items:center;gap:10px;margin-bottom:18px}
    .card-logo-mark{
      width:40px;height:40px;border-radius:10px;
      background:linear-gradient(135deg,#1565c0,#00695c);
      color:white;font-size:22px;font-weight:900;
      display:flex;align-items:center;justify-content:center;
      box-shadow:0 4px 14px rgba(21,101,192,.35);flex-shrink:0;
    }
    .card-logo-text .n1{font-size:16px;font-weight:800;color:#0d1b2a;letter-spacing:-.02em}
    .card-logo-text .n2{font-size:10px;color:#5a7894;font-weight:500;margin-top:1px}

    /* Panel switcher */
    .panel{display:none}
    .panel.active{display:block}

    /* Headings */
    .c-heading{font-size:18px;font-weight:700;color:#0d1b2a;letter-spacing:-.02em;margin-bottom:3px}
    .c-sub{font-size:11.5px;color:#8aa0b4;margin-bottom:18px;line-height:1.4}

    /* Role selector */
    .role-label{font-size:10.5px;font-weight:700;color:#344a5e;letter-spacing:.05em;text-transform:uppercase;margin-bottom:7px;display:block}
    .role-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:5px;margin-bottom:14px}
    .role-chip{
      border:1.5px solid #d8e4f0;border-radius:8px;
      padding:7px 4px;text-align:center;cursor:pointer;
      transition:all var(--transition);background:#f7fafd;
    }
    .role-chip:hover{border-color:#1565c0;background:#e8f2fd;transform:translateY(-1px)}
    .role-chip.selected{
      border-color:#1565c0;background:linear-gradient(135deg,#e3f2fd,#e8f5e9);
      box-shadow:0 0 0 2px rgba(21,101,192,.18);
    }
    .role-chip .rc-icon{font-size:17px;margin-bottom:2px}
    .role-chip .rc-label{font-size:9.5px;font-weight:600;color:#344a5e}
    .role-chip.selected .rc-label{color:#1565c0}

    /* Form elements */
    .form-group{margin-bottom:13px}
    .form-label{display:flex;align-items:center;justify-content:space-between;font-size:11px;font-weight:600;color:#344a5e;margin-bottom:5px;letter-spacing:.02em}
    .input-wrap{position:relative}
    .inp-icon{position:absolute;left:11px;top:50%;transform:translateY(-50%);font-size:13px;color:#94afc4;pointer-events:none;z-index:1}
    .form-input{
      width:100%;padding:10px 38px 10px 36px;
      border:1.5px solid #d0dce8;border-radius:var(--input-radius);
      font-size:13px;color:#0d1b2a;background:#fff;
      transition:border-color var(--transition),box-shadow var(--transition);
      outline:none;font-family:'Inter',sans-serif;
    }
    .form-input:focus{border-color:#1565c0;box-shadow:0 0 0 3px rgba(21,101,192,.12)}
    .form-input.error{border-color:var(--danger);box-shadow:0 0 0 3px rgba(198,40,40,.1)}
    .form-input.valid{border-color:var(--success);box-shadow:0 0 0 3px rgba(46,125,50,.08)}
    .form-input::placeholder{color:#c0d0de}
    .input-action{position:absolute;right:10px;top:50%;transform:translateY(-50%);cursor:pointer;color:#8aa0b4;font-size:13px;user-select:none;transition:color .15s;z-index:1;background:none;border:none;padding:2px}
    .input-action:hover{color:#1565c0}

    /* Error / hint messages */
    .field-msg{font-size:10.5px;margin-top:4px;display:flex;align-items:center;gap:4px;line-height:1.3}
    .field-msg.err{color:var(--danger)}
    .field-msg.ok {color:var(--success)}
    .field-msg.hint{color:#8aa4bc}

    /* Password strength */
    .pw-strength-bar{
      display:grid;grid-template-columns:repeat(4,1fr);gap:3px;
      margin-top:5px;
    }
    .pw-seg{height:3px;border-radius:2px;background:#e0eaf2;transition:background .2s}
    .pw-seg.weak  {background:#c62828}
    .pw-seg.fair  {background:#f57c00}
    .pw-seg.good  {background:#f9a825}
    .pw-seg.strong{background:#2e7d32}
    .pw-label{font-size:10px;color:#8aa4bc;margin-top:3px}

    /* Remember me & forgot */
    .form-options{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;font-size:11.5px}
    .checkbox-wrap{display:flex;align-items:center;gap:6px;cursor:pointer;color:#5a7894;user-select:none}
    .checkbox-wrap input{width:14px;height:14px;accent-color:#1565c0;cursor:pointer}
    .forgot-link{color:#1565c0;font-weight:500;font-size:11.5px;text-decoration:none;cursor:pointer;background:none;border:none;padding:0;font-family:inherit}
    .forgot-link:hover{text-decoration:underline}

    /* Session timer badge */
    .session-info{
      display:flex;align-items:center;gap:5px;
      font-size:10px;color:#6a90a8;margin-bottom:4px;
    }

    /* Login button */
    .btn-login{
      width:100%;padding:11px;border-radius:var(--input-radius);border:none;
      background:linear-gradient(135deg,#1565c0 0%,#0b4ea8 100%);
      color:white;font-size:13.5px;font-weight:700;
      cursor:pointer;transition:all var(--transition);
      display:flex;align-items:center;justify-content:center;gap:8px;
      box-shadow:0 4px 16px rgba(21,101,192,.35);
      font-family:'Inter',sans-serif;letter-spacing:.01em;
      position:relative;overflow:hidden;
    }
    .btn-login::after{
      content:'';position:absolute;inset:0;
      background:linear-gradient(135deg,rgba(255,255,255,.08),transparent);
    }
    .btn-login:hover{transform:translateY(-1px);box-shadow:0 8px 28px rgba(21,101,192,.45)}
    .btn-login:active{transform:none;box-shadow:0 2px 8px rgba(21,101,192,.3)}
    .btn-login:disabled{opacity:.6;cursor:not-allowed;transform:none}

    /* Secondary button */
    .btn-secondary{
      width:100%;padding:10px;border-radius:var(--input-radius);
      background:transparent;border:1.5px solid #d0dce8;
      color:#344a5e;font-size:13px;font-weight:600;
      cursor:pointer;transition:all var(--transition);
      display:flex;align-items:center;justify-content:center;gap:7px;
      font-family:'Inter',sans-serif;margin-top:8px;
    }
    .btn-secondary:hover{background:#f0f6ff;border-color:#1565c0;color:#1565c0}

    /* OTP inputs */
    .otp-row{display:flex;gap:8px;justify-content:center;margin:16px 0}
    .otp-box{
      width:44px;height:52px;border:1.5px solid #d0dce8;border-radius:8px;
      text-align:center;font-size:20px;font-weight:700;color:#0d1b2a;
      outline:none;transition:border-color var(--transition),box-shadow var(--transition);
      font-family:'Inter',sans-serif;background:#fff;
    }
    .otp-box:focus{border-color:#1565c0;box-shadow:0 0 0 3px rgba(21,101,192,.12)}
    .otp-box.filled{border-color:#1565c0;background:#f0f6ff}
    .otp-timer{text-align:center;font-size:11.5px;color:#8aa0b4;margin-bottom:12px}
    .otp-timer .countdown{color:#1565c0;font-weight:600}
    .otp-resend{background:none;border:none;color:#1565c0;font-size:11.5px;font-weight:600;cursor:pointer;font-family:inherit;padding:0}
    .otp-resend:disabled{color:#b0c4d8;cursor:not-allowed}
    .otp-resend:not(:disabled):hover{text-decoration:underline}

    /* Forgot password flow */
    .reset-method-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:16px}
    .reset-method-btn{
      border:1.5px solid #d0dce8;border-radius:9px;
      padding:10px 8px;text-align:center;cursor:pointer;
      transition:all var(--transition);background:#f7fafd;
    }
    .reset-method-btn:hover,.reset-method-btn.selected{border-color:#1565c0;background:#e3f2fd}
    .reset-method-btn .rm-icon{font-size:22px;margin-bottom:3px}
    .reset-method-btn .rm-label{font-size:11px;font-weight:600;color:#344a5e}
    .reset-method-btn .rm-val{font-size:9.5px;color:#8aa0b4}

    /* Success state */
    .success-state{text-align:center;padding:10px 0 6px}
    .success-icon{font-size:48px;margin-bottom:12px;animation:popIn .4s ease}
    @keyframes popIn{from{transform:scale(0)}to{transform:scale(1)}}
    .success-title{font-size:18px;font-weight:700;color:#0d1b2a;margin-bottom:6px}
    .success-sub{font-size:12.5px;color:#5a7894;line-height:1.5;margin-bottom:20px}

    /* Demo section */
    .demo-divider{
      display:flex;align-items:center;gap:8px;margin:12px 0 10px;
      font-size:10.5px;color:#b0c4d4;
    }
    .demo-divider::before,.demo-divider::after{content:'';flex:1;height:1px;background:#e2ecf4}
    .demo-creds{display:grid;grid-template-columns:1fr 1fr 1fr;gap:5px}
    .demo-cred{
      padding:6px 5px;border-radius:7px;border:1px solid #e2ecf4;
      background:#f7fafd;cursor:pointer;text-align:center;
      transition:all .15s;
    }
    .demo-cred:hover{border-color:#1565c0;background:#e3f2fd;transform:translateY(-1px)}
    .demo-cred .dc-role{font-size:10px;font-weight:700;color:#344a5e}
    .demo-cred .dc-user{font-size:9px;color:#8aa0b4}

    /* Security note */
    .security-note{
      margin-top:14px;padding:10px 12px;
      background:#f7fafd;border:1px solid #e2ecf4;border-radius:8px;
      font-size:10px;color:#8aa0b4;
      display:flex;align-items:flex-start;gap:7px;line-height:1.5;
    }

    /* ── TOAST ─────────────────────────────────────────── */
    #toastArea{
      position:fixed;bottom:24px;right:24px;z-index:9998;
      display:flex;flex-direction:column;gap:8px;align-items:flex-end;
    }
    .toast{
      background:#fff;border:1px solid #e0e8f0;border-radius:10px;
      padding:11px 16px;display:flex;align-items:center;gap:10px;
      font-size:12.5px;color:#1a2a3a;font-family:'Inter',sans-serif;
      box-shadow:0 8px 32px rgba(0,0,0,.15);max-width:320px;
      animation:toastIn .25s ease;
    }
    .toast.success{border-left:4px solid #2e7d32}
    .toast.error  {border-left:4px solid #c62828}
    .toast.info   {border-left:4px solid #1565c0}
    .toast.warning{border-left:4px solid #e65100}
    @keyframes toastIn{from{opacity:0;transform:translateX(20px)}to{opacity:1;transform:none}}

    /* ── LOADING OVERLAY ──────────────────────────────── */
    #loginLoading{
      display:none;position:fixed;inset:0;z-index:9999;
      background:rgba(7,18,33,.88);backdrop-filter:blur(8px);
      flex-direction:column;align-items:center;justify-content:center;gap:20px;
    }
    #loginLoading.show{display:flex}
    .ld-spinner{
      width:52px;height:52px;
      border:3px solid rgba(255,255,255,.12);
      border-top-color:#1565c0;border-radius:50%;
      animation:spin .7s linear infinite;
    }
    @keyframes spin{to{transform:rotate(360deg)}}
    .ld-text p{font-size:13.5px;color:#7ab0d0;font-family:'Inter',sans-serif;text-align:center}
    .ld-text small{font-size:10.5px;color:#3a5a7a;text-align:center;display:block;margin-top:4px}
    .ld-progress{
      width:200px;height:3px;background:rgba(255,255,255,.08);
      border-radius:2px;overflow:hidden;
    }
    .ld-bar{
      height:100%;width:0%;
      background:linear-gradient(90deg,#1565c0,#00897b);
      border-radius:2px;transition:width .4s ease;
    }

    /* ── FOOTER ────────────────────────────────────────── */
    .login-footer{
      position:relative;z-index:20;
      background:rgba(7,18,33,.85);
      border-top:1px solid rgba(255,255,255,.05);
      padding:9px 28px;
      display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;
    }
    .footer-links{display:flex;gap:14px;flex-wrap:wrap}
    .footer-links a{font-size:10px;color:#3a5268;text-decoration:none;transition:color .15s}
    .footer-links a:hover{color:#6a9cbc}
    .footer-right{font-size:10px;color:#2c4055}

    /* ── RESPONSIVE ────────────────────────────────────── */
    @media(max-width:860px){
      .login-wrapper{grid-template-columns:1fr;gap:28px;max-width:460px}
      .info-panel{text-align:center}
      .cert-row,.modules-strip{justify-content:center}
      .feature-list{align-items:flex-start;max-width:400px;margin:0 auto 24px}
      .stats-row{grid-template-columns:repeat(3,1fr)}
    }
    @media(max-width:500px){
      .login-topbar{padding:8px 14px}
      .gov-seal{width:40px;height:40px}
      .tb-block .t2,.tb-block .t3{display:none}
      .topbar-right .tb-badge:not(:first-child){display:none}
      .info-panel h1{font-size:28px}
      .stats-row{grid-template-columns:1fr 1fr}
      .card-inner{padding:20px 18px 22px}
      .dt-strip{padding:3px 14px;font-size:9.5px}
    }
  </style>
</head>
<body>

<!-- ── BACKGROUND LAYERS ─────────────────────────────── -->
<div class="bg-banner" aria-hidden="true"></div>
<div class="bg-overlay" aria-hidden="true"></div>
<div class="bg-grid" aria-hidden="true"></div>
<div class="particles" id="particles" aria-hidden="true"></div>

<!-- ── LOADING OVERLAY ───────────────────────────────── -->
<div id="loginLoading" role="status" aria-live="polite">
  <div class="ld-spinner"></div>
  <div class="ld-text">
    <p id="loadingMsg">Authenticating… Please wait</p>
    <small id="loadingSub">Connecting to State HMIS</small>
  </div>
  <div class="ld-progress"><div class="ld-bar" id="ldBar"></div></div>
</div>

<!-- ── TOAST AREA ────────────────────────────────────── -->
<div id="toastArea" aria-live="polite"></div>

<!-- ── TOP GOVERNMENT BAR ────────────────────────────── -->
<header class="login-topbar">
  <div class="topbar-left">
    <div class="gov-seal">
      <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="50" cy="50" r="46" fill="#fff8ec" stroke="#003580" stroke-width="3"/>
        <circle cx="50" cy="50" r="34" fill="#e6f0fb" stroke="#003580" stroke-width="1.5"/>
        <path d="M50 16 L57 35 L78 35 L62 47 L68 66 L50 54 L32 66 L38 47 L22 35 L43 35 Z" fill="#003580" opacity=".9"/>
        <circle cx="50" cy="50" r="9" fill="#e65100"/>
        <circle cx="50" cy="50" r="5" fill="#f9a825"/>
        <text x="50" y="89" text-anchor="middle" font-size="7.5" fill="#003580" font-family="Arial" font-weight="700">उत्तराखण्ड</text>
      </svg>
    </div>
    <div class="tb-block">
      <div class="t1">Government of Uttarakhand</div>
      <div class="t2">Department of Health &amp; Family Welfare</div>
      <div class="t3">National Health Mission | NIC Hosted</div>
    </div>
    <div class="tb-div"></div>
    <div class="tb-block">
      <div class="hindi">उत्तराखण्ड सरकार</div>
      <div class="hindi" style="font-size:10px;color:#4a6880">स्वास्थ्य एवं परिवार कल्याण विभाग</div>
    </div>
  </div>
  <div class="topbar-right">
    <div class="tb-badge green"><span class="live-dot"></span> System Live</div>
    <div class="tb-badge blue">🔒 SSL Secured</div>
    <div class="tb-badge gray" id="topTime">--:-- --</div>
  </div>
</header>

<!-- datetime strip -->
<div class="dt-strip">
  <span>📅 <span id="fullDate">Loading…</span></span>
  <span>🏥 ParaCare+ HMIS v2.4.1 &nbsp;·&nbsp; ABDM Integrated &nbsp;·&nbsp; NHM Portal</span>
</div>

<!-- ── MAIN ───────────────────────────────────────────── -->
<main class="login-main">
  <div class="login-wrapper">

    <!-- ── LEFT: Info Panel ──────────────────────────── -->
    <div class="info-panel">
      <div class="portal-badge">🏥 HMIS Portal — Govt. of Uttarakhand</div>
      <h1>ParaCare+ <span class="hl">HMIS</span></h1>
      <p class="tagline">
        Integrated Hospital Management Information System for<br>
        Uttarakhand State — Powered by AI &amp; ABDM
      </p>

      <!-- Stats -->
      <div class="stats-row">
        <div class="stat-box">
          <div class="sv">186</div>
          <div class="sl">Facilities</div>
        </div>
        <div class="stat-box">
          <div class="sv">4,200+</div>
          <div class="sl">Daily OPDs</div>
        </div>
        <div class="stat-box">
          <div class="sv">13</div>
          <div class="sl">Modules</div>
        </div>
      </div>

      <div class="feature-list">
        <div class="feature-item"><div class="f-check">✓</div>Unified clinical modules — OPD, IPD, Lab, Radiology, Blood Bank &amp; Pharmacy</div>
        <div class="feature-item"><div class="f-check">✓</div>Role-based access for Doctors, Nurses, Admin, Pharmacy &amp; HR staff</div>
        <div class="feature-item"><div class="f-check">✓</div>Real-time KPI dashboards with district &amp; state-level analytics</div>
        <div class="feature-item"><div class="f-check">✓</div>AI clinical decision support, drug interaction &amp; epidemiology alerts</div>
        <div class="feature-item"><div class="f-check">✓</div>ABDM / ABHA patient ID integration — Ayushman Bharat Digital Mission</div>
        <div class="feature-item"><div class="f-check">✓</div>108 Ambulance dispatch &amp; real-time fleet tracking system</div>
      </div>

      <div class="cert-row">
        <div class="cert-chip">✅ NIC Hosted</div>
        <div class="cert-chip">🔐 ISO 27001</div>
        <div class="cert-chip">🏥 NABH Aligned</div>
        <div class="cert-chip">🇮🇳 ABDM Ready</div>
        <div class="cert-chip">♿ WCAG 2.1 AA</div>
      </div>

      <div class="modules-strip">
        <div class="mod-pill">🩺 OPD/IPD</div>
        <div class="mod-pill">🧪 Lab/LIS</div>
        <div class="mod-pill">🩻 Radiology</div>
        <div class="mod-pill">💊 Pharmacy</div>
        <div class="mod-pill">🩸 BloodBank</div>
        <div class="mod-pill">🚑 Ambulance</div>
        <div class="mod-pill">💳 Billing</div>
        <div class="mod-pill">👔 HR</div>
      </div>
    </div>

    <!-- ── RIGHT: Login Card ─────────────────────────── -->
    <div class="login-card" role="main" aria-label="Login form">
      <div class="card-accent-bar"></div>
      <div class="card-inner">

        <!-- Card logo -->
        <div class="card-logo">
          <div class="card-logo-mark" aria-hidden="true">+</div>
          <div class="card-logo-text">
            <div class="n1">ParaCare+ HMIS</div>
            <div class="n2">State Health Information Management System</div>
          </div>
        </div>

        <!-- ══ PANEL 1: LOGIN ══════════════════════════ -->
        <div class="panel active" id="panelLogin">
          <div class="c-heading">Sign In to Portal</div>
          <p class="c-sub">Select your role, enter your credentials and sign in securely</p>

          <!-- Role selector -->
          <span class="role-label">Select Your Role</span>
          <div class="role-grid" id="roleGrid">
            <div class="role-chip selected" data-role="admin"     data-dest="admin.html"     onclick="selectRole(this)" tabindex="0" role="button" aria-pressed="true">
              <div class="rc-icon">👔</div><div class="rc-label">State Admin</div>
            </div>
            <div class="role-chip" data-role="doctor"    data-dest="doctor.html"    onclick="selectRole(this)" tabindex="0" role="button" aria-pressed="false">
              <div class="rc-icon">🩺</div><div class="rc-label">Doctor</div>
            </div>
            <div class="role-chip" data-role="nurse"     data-dest="nurse.html"     onclick="selectRole(this)" tabindex="0" role="button" aria-pressed="false">
              <div class="rc-icon">👩‍⚕️</div><div class="rc-label">Nurse</div>
            </div>
            <div class="role-chip" data-role="billing"   data-dest="billing.html"   onclick="selectRole(this)" tabindex="0" role="button" aria-pressed="false">
              <div class="rc-icon">💳</div><div class="rc-label">Billing</div>
            </div>
            <div class="role-chip" data-role="pharmacy"  data-dest="pharmacy.html"  onclick="selectRole(this)" tabindex="0" role="button" aria-pressed="false">
              <div class="rc-icon">💊</div><div class="rc-label">Pharmacy</div>
            </div>
            <div class="role-chip" data-role="lab"       data-dest="lab.html"       onclick="selectRole(this)" tabindex="0" role="button" aria-pressed="false">
              <div class="rc-icon">🧪</div><div class="rc-label">Lab</div>
            </div>
            <div class="role-chip" data-role="radiology" data-dest="radiology.html" onclick="selectRole(this)" tabindex="0" role="button" aria-pressed="false">
              <div class="rc-icon">🩻</div><div class="rc-label">Radiology</div>
            </div>
            <div class="role-chip" data-role="ambulance" data-dest="ambulance.html" onclick="selectRole(this)" tabindex="0" role="button" aria-pressed="false">
              <div class="rc-icon">🚑</div><div class="rc-label">Ambulance</div>
            </div>
            <div class="role-chip" data-role="hr"        data-dest="hr.html"        onclick="selectRole(this)" tabindex="0" role="button" aria-pressed="false">
              <div class="rc-icon">👨‍💼</div><div class="rc-label">HR</div>
            </div>
          </div>

          <!-- Login form -->
          <form id="loginForm" onsubmit="handleLogin(event)" novalidate>
            <!-- Username -->
            <div class="form-group">
              <label class="form-label" for="username">
                <span>Employee ID / Username</span>
                <span id="userBadge" style="font-size:9.5px;font-weight:600;color:#1565c0;background:#e3f2fd;padding:1px 7px;border-radius:10px">State Admin</span>
              </label>
              <div class="input-wrap">
                <span class="inp-icon" aria-hidden="true">👤</span>
                <input id="username" class="form-input" type="text"
                  placeholder="e.g. admin@uk.gov.in"
                  value="admin@uk.gov.in"
                  autocomplete="username"
                  aria-required="true"
                  aria-describedby="usernameMsg"
                  oninput="validateUsername()"/>
                <button type="button" class="input-action" onclick="clearField('username')" title="Clear" tabindex="-1" id="userClear" style="display:none">✕</button>
              </div>
              <div class="field-msg hint" id="usernameMsg">Use your official Gov. employee ID or email</div>
            </div>

            <!-- Password -->
            <div class="form-group">
              <label class="form-label" for="password">
                <span>Password</span>
                <span id="pwStrengthLabel" class="pw-label"></span>
              </label>
              <div class="input-wrap">
                <span class="inp-icon" aria-hidden="true">🔒</span>
                <input id="password" class="form-input" type="password"
                  placeholder="Enter your password"
                  value="Demo@1234"
                  autocomplete="current-password"
                  aria-required="true"
                  aria-describedby="passwordMsg"
                  oninput="checkPasswordStrength(this.value)"/>
                <button type="button" class="input-action" id="passToggle" onclick="togglePass()" title="Show/hide password">👁</button>
              </div>
              <!-- Strength bar (shown only in new-password context; hidden in login) -->
              <div class="pw-strength-bar" id="pwStrengthBar" style="display:none">
                <div class="pw-seg" id="ps1"></div>
                <div class="pw-seg" id="ps2"></div>
                <div class="pw-seg" id="ps3"></div>
                <div class="pw-seg" id="ps4"></div>
              </div>
              <div class="field-msg hint" id="passwordMsg">Your session will expire after 8 hours of inactivity</div>
            </div>

            <!-- Options row -->
            <div class="form-options">
              <label class="checkbox-wrap" for="rememberMe">
                <input type="checkbox" id="rememberMe" checked/>
                Remember me (8 hrs)
              </label>
              <button type="button" class="forgot-link" onclick="showForgot()" aria-label="Forgot password">Forgot password?</button>
            </div>

            <!-- Session note -->
            <div class="session-info">
              <span>🔐</span>
              <span id="rememberNote">Session will persist for 8 hours on this device</span>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn-login" id="btnLogin">
              <span id="btnLoginText">🔐 Sign In Securely</span>
              <span id="btnLoginArrow">→</span>
            </button>
          </form>

          <!-- Demo quick access -->
          <div class="demo-divider">Quick Demo Access</div>
          <div class="demo-creds">
            <div class="demo-cred" onclick="quickLogin('admin.html','State Admin')" title="Admin Demo">
              <div class="dc-role">👔 Admin</div><div class="dc-user">State Level</div>
            </div>
            <div class="demo-cred" onclick="quickLogin('doctor.html','Doctor')" title="Doctor Demo">
              <div class="dc-role">🩺 Doctor</div><div class="dc-user">OPD / IPD</div>
            </div>
            <div class="demo-cred" onclick="quickLogin('nurse.html','Nurse')" title="Nurse Demo">
              <div class="dc-role">👩‍⚕️ Nurse</div><div class="dc-user">Ward 4</div>
            </div>
            <div class="demo-cred" onclick="quickLogin('lab.html','Lab Tech')" title="Lab Demo">
              <div class="dc-role">🧪 Lab</div><div class="dc-user">Pathology</div>
            </div>
            <div class="demo-cred" onclick="quickLogin('radiology.html','Radiographer')" title="Radiology Demo">
              <div class="dc-role">🩻 Radiology</div><div class="dc-user">RIS/PACS</div>
            </div>
            <div class="demo-cred" onclick="quickLogin('bloodbank.html','Blood Bank')" title="Blood Bank Demo">
              <div class="dc-role">🩸 BloodBank</div><div class="dc-user">Transfusion</div>
            </div>
          </div>

          <!-- Security note -->
          <div class="security-note" role="note">
            <span>🛡️</span>
            <span>Secured by NIC Cloud Infrastructure &amp; MeitY guidelines. All access is logged, monitored &amp; audited. Use of this portal implies acceptance of the IT Act 2000 &amp; Govt. data privacy policies.</span>
          </div>
        </div><!-- /panelLogin -->

        <!-- ══ PANEL 2: OTP / 2FA ════════════════════════ -->
        <div class="panel" id="panelOTP">
          <div class="c-heading">Two-Factor Verification</div>
          <p class="c-sub" id="otpSubtitle">A 6-digit OTP has been sent to your registered mobile number ending in <strong>••••87</strong></p>

          <div class="otp-row" id="otpRow" role="group" aria-label="Enter OTP">
            <input class="otp-box" type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" aria-label="OTP digit 1" autocomplete="one-time-code"/>
            <input class="otp-box" type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" aria-label="OTP digit 2"/>
            <input class="otp-box" type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" aria-label="OTP digit 3"/>
            <input class="otp-box" type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" aria-label="OTP digit 4"/>
            <input class="otp-box" type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" aria-label="OTP digit 5"/>
            <input class="otp-box" type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" aria-label="OTP digit 6"/>
          </div>

          <div class="otp-timer">
            OTP expires in <span class="countdown" id="otpCountdown">02:00</span>
            &nbsp;·&nbsp;
            <button class="otp-resend" id="btnResend" onclick="resendOTP()" disabled>Resend OTP</button>
          </div>

          <button class="btn-login" onclick="verifyOTP()" id="btnVerifyOTP">✅ Verify &amp; Sign In</button>
          <button class="btn-secondary" onclick="showPanel('panelLogin')">← Back to Login</button>

          <div class="security-note" style="margin-top:12px" role="note">
            <span>💡</span>
            <span>Demo: Enter any 6 digits to proceed. In production, OTP is sent via NIC SMS Gateway to registered mobile.</span>
          </div>
        </div><!-- /panelOTP -->

        <!-- ══ PANEL 3: FORGOT PASSWORD ══════════════════ -->
        <div class="panel" id="panelForgot">
          <div class="c-heading">Reset Password</div>
          <p class="c-sub">Choose how you'd like to receive your password reset link</p>

          <div class="reset-method-grid">
            <div class="reset-method-btn selected" id="rmEmail" onclick="selectResetMethod('email')" role="button" tabindex="0">
              <div class="rm-icon">📧</div>
              <div class="rm-label">Email OTP</div>
              <div class="rm-val" id="rmEmailVal">••••@uk.gov.in</div>
            </div>
            <div class="reset-method-btn" id="rmSMS" onclick="selectResetMethod('sms')" role="button" tabindex="0">
              <div class="rm-icon">📱</div>
              <div class="rm-label">SMS OTP</div>
              <div class="rm-val" id="rmSMSVal">+91 ••••••87</div>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label" for="resetId">Employee ID / Registered Email</label>
            <div class="input-wrap">
              <span class="inp-icon" aria-hidden="true">👤</span>
              <input id="resetId" class="form-input" type="text" placeholder="Enter your Employee ID or email" autocomplete="username"/>
            </div>
          </div>

          <button class="btn-login" onclick="sendResetOTP()">📤 Send Reset OTP</button>
          <button class="btn-secondary" onclick="showPanel('panelLogin')">← Back to Login</button>

          <div class="security-note" style="margin-top:12px" role="note">
            <span>🔒</span>
            <span>For security, the OTP link is valid for 10 minutes only. If you don't receive it, contact your facility IT administrator or NIC helpdesk at <strong>1800-111-555</strong>.</span>
          </div>
        </div><!-- /panelForgot -->

        <!-- ══ PANEL 4: RESET OTP ═════════════════════════ -->
        <div class="panel" id="panelResetOTP">
          <div class="c-heading">Enter Reset OTP</div>
          <p class="c-sub" id="resetOtpSub">Enter the 6-digit OTP sent to your registered contact</p>

          <div class="otp-row" id="resetOtpRow" role="group" aria-label="Reset OTP">
            <input class="otp-box" type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="one-time-code"/>
            <input class="otp-box" type="text" maxlength="1" pattern="[0-9]" inputmode="numeric"/>
            <input class="otp-box" type="text" maxlength="1" pattern="[0-9]" inputmode="numeric"/>
            <input class="otp-box" type="text" maxlength="1" pattern="[0-9]" inputmode="numeric"/>
            <input class="otp-box" type="text" maxlength="1" pattern="[0-9]" inputmode="numeric"/>
            <input class="otp-box" type="text" maxlength="1" pattern="[0-9]" inputmode="numeric"/>
          </div>

          <button class="btn-login" onclick="verifyResetOTP()">✅ Verify OTP</button>
          <button class="btn-secondary" onclick="showPanel('panelForgot')">← Back</button>
        </div><!-- /panelResetOTP -->

        <!-- ══ PANEL 5: NEW PASSWORD ══════════════════════ -->
        <div class="panel" id="panelNewPassword">
          <div class="c-heading">Set New Password</div>
          <p class="c-sub">Your identity is verified. Please set a strong new password.</p>

          <form id="newPasswordForm" onsubmit="event.preventDefault();submitNewPassword()" autocomplete="off">
          <!-- Hidden username for password manager accessibility -->
          <input type="text" name="username" autocomplete="username" style="position:absolute;left:-9999px;width:1px;opacity:0" tabindex="-1" aria-hidden="true"/>
          <div class="form-group">
            <label class="form-label" for="newPass">
              <span>New Password</span>
              <span id="newPwStrengthLabel" class="pw-label"></span>
            </label>
            <div class="input-wrap">
              <span class="inp-icon" aria-hidden="true">🔒</span>
              <input id="newPass" class="form-input" type="password"
                placeholder="Min. 8 chars, uppercase, number, symbol"
                autocomplete="new-password"
                oninput="checkPasswordStrength(this.value, true)"/>
              <button type="button" class="input-action" onclick="toggleField('newPass')" title="Show/hide">👁</button>
            </div>
            <div class="pw-strength-bar" id="newPwStrengthBar">
              <div class="pw-seg" id="nps1"></div>
              <div class="pw-seg" id="nps2"></div>
              <div class="pw-seg" id="nps3"></div>
              <div class="pw-seg" id="nps4"></div>
            </div>
            <ul style="font-size:10px;color:#8aa4bc;margin-top:5px;padding-left:14px;line-height:1.7" id="pwRules">
              <li id="rule-len">At least 8 characters</li>
              <li id="rule-upper">At least 1 uppercase letter</li>
              <li id="rule-num">At least 1 number</li>
              <li id="rule-sym">At least 1 symbol (!@#$%)</li>
            </ul>
          </div>
          <div class="form-group">
            <label class="form-label" for="confirmPass">Confirm New Password</label>
            <div class="input-wrap">
              <span class="inp-icon" aria-hidden="true">🔒</span>
              <input id="confirmPass" class="form-input" type="password"
                placeholder="Re-enter new password"
                autocomplete="new-password"
                oninput="checkConfirmPass()"/>
              <button type="button" class="input-action" onclick="toggleField('confirmPass')" title="Show/hide">👁</button>
            </div>
            <div class="field-msg hint" id="confirmMsg"></div>
          </div>

          <button type="submit" class="btn-login">✅ Save New Password</button>
          <button type="button" class="btn-secondary" onclick="showPanel('panelLogin')">← Back to Login</button>
          </form>
        </div><!-- /panelNewPassword -->

        <!-- ══ PANEL 6: SUCCESS ═══════════════════════════ -->
        <div class="panel" id="panelSuccess">
          <div class="success-state">
            <div class="success-icon" id="successIcon">✅</div>
            <div class="success-title" id="successTitle">Password Reset Successful!</div>
            <p class="success-sub" id="successSub">Your password has been updated. You will be redirected to the login page shortly.</p>
            <button class="btn-login" style="max-width:280px;margin:0 auto" onclick="showPanel('panelLogin')">🔐 Back to Login</button>
          </div>
        </div><!-- /panelSuccess -->

      </div><!-- /card-inner -->
    </div><!-- /login-card -->

  </div><!-- /login-wrapper -->
</main>

<!-- ── FOOTER ─────────────────────────────────────────── -->
<footer class="login-footer">
  <div class="footer-links">
    <a href="#" onclick="showToast('Help Desk: 1800-111-555 (Toll Free)','info');return false">Help Desk</a>
    <a href="#" onclick="showToast('User manual download coming soon','info');return false">User Manual</a>
    <a href="#" onclick="showToast('Privacy policy document loading…','info');return false">Privacy Policy</a>
    <a href="#" onclick="showToast('Accessibility statement available','info');return false">Accessibility</a>
    <a href="#" onclick="showToast('RTI portal: uk.gov.in/rti','info');return false">RTI</a>
    <a href="#" onclick="showToast('NIC Helpdesk: 1800-111-4000','info');return false">NIC Helpdesk</a>
    <a href="#" onclick="showToast('Terms of service applicable','info');return false">Terms of Use</a>
  </div>
  <div class="footer-right">
    © 2024 Govt. of Uttarakhand &nbsp;·&nbsp; ParaCare+ HMIS v2.4.1 &nbsp;·&nbsp; NIC Hosted &nbsp;·&nbsp; ABDM Integrated
  </div>
</footer>

<script>
/* ══════════════════════════════════════════════════════════
   STATE
   ══════════════════════════════════════════════════════════ */
let selectedDest     = 'admin.html';
let selectedRole     = 'admin';
let otpTimerInterval = null;
let otpSeconds       = 120;
let resetMethod      = 'email';
let loginAttempts    = 0;
const MAX_ATTEMPTS   = 5;

/* ══════════════════════════════════════════════════════════
   ROLE MAP
   ══════════════════════════════════════════════════════════ */
const ROLE_MAP = {
  admin:     { email:'admin@uk.gov.in',         label:'State Admin',    mobile:'••••87' },
  doctor:    { email:'dr.sharma@aiims.uk',       label:'Doctor',         mobile:'••••23' },
  nurse:     { email:'nurse.priya@dh.uk',        label:'Nurse',          mobile:'••••61' },
  billing:   { email:'billing.counter3@dh.uk',   label:'Billing Operator',mobile:'••••44' },
  pharmacy:  { email:'pharma.central@dh.uk',     label:'Pharmacist',     mobile:'••••90' },
  lab:       { email:'lab.tech@dh.uk',           label:'Lab Technician', mobile:'••••15' },
  radiology: { email:'radio.tech@dh.uk',         label:'Radiographer',   mobile:'••••73' },
  ambulance: { email:'dispatch.108@uk.gov.in',   label:'Ambulance Dispatcher', mobile:'••••56' },
  hr:        { email:'hr.officer@dh.uk',         label:'HR Officer',     mobile:'••••38' },
};

/* ══════════════════════════════════════════════════════════
   PANEL SWITCHING
   ══════════════════════════════════════════════════════════ */
function showPanel(id) {
  document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
  const panel = document.getElementById(id);
  if (panel) {
    panel.classList.add('active');
    // Scroll card into view
    panel.closest('.login-card').scrollIntoView({ behavior:'smooth', block:'nearest' });
  }
  if (id !== 'panelOTP') clearOTPTimer();
}

/* ══════════════════════════════════════════════════════════
   ROLE SELECTION
   ══════════════════════════════════════════════════════════ */
function selectRole(el) {
  document.querySelectorAll('.role-chip').forEach(c => {
    c.classList.remove('selected');
    c.setAttribute('aria-pressed','false');
  });
  el.classList.add('selected');
  el.setAttribute('aria-pressed','true');
  selectedDest = el.dataset.dest;
  selectedRole = el.dataset.role;
  const info = ROLE_MAP[selectedRole] || {};
  document.getElementById('username').value = info.email || '';
  document.getElementById('password').value = 'Demo@1234';
  document.getElementById('userBadge').textContent = info.label || selectedRole;
  validateUsername();
  updateRememberNote();
  clearFieldErrors();
  showToast(`Role selected: ${info.label || selectedRole}`, 'info', 1800);
}

/* keyboard support for role chips */
document.querySelectorAll('.role-chip').forEach(chip => {
  chip.addEventListener('keydown', e => {
    if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); selectRole(chip); }
  });
});

/* ══════════════════════════════════════════════════════════
   FORM VALIDATION
   ══════════════════════════════════════════════════════════ */
function validateUsername() {
  const val = document.getElementById('username').value.trim();
  const msg = document.getElementById('usernameMsg');
  const inp = document.getElementById('username');
  const clearBtn = document.getElementById('userClear');
  clearBtn.style.display = val.length > 0 ? 'block' : 'none';
  if (!val) {
    setFieldState(inp, msg, '', 'hint', 'Use your official Gov. employee ID or email');
    return false;
  }
  if (val.length < 5) {
    setFieldState(inp, msg, 'error', 'err', '⚠ Username must be at least 5 characters');
    return false;
  }
  if (!val.includes('@') && !val.match(/^[a-zA-Z0-9_\-]+$/)) {
    setFieldState(inp, msg, 'error', 'err', '⚠ Invalid username format');
    return false;
  }
  setFieldState(inp, msg, 'valid', 'ok', '✓ Username looks valid');
  return true;
}

function setFieldState(inp, msg, inputClass, msgClass, text) {
  inp.classList.remove('error','valid');
  msg.className = 'field-msg ' + msgClass;
  msg.textContent = text;
  if (inputClass) inp.classList.add(inputClass);
}

function clearField(id) {
  document.getElementById(id).value = '';
  if (id === 'username') {
    document.getElementById('userClear').style.display = 'none';
    validateUsername();
  }
}

function clearFieldErrors() {
  ['username','password'].forEach(id => {
    const el = document.getElementById(id);
    if (el) { el.classList.remove('error','valid'); }
  });
}

/* ══════════════════════════════════════════════════════════
   PASSWORD STRENGTH
   ══════════════════════════════════════════════════════════ */
function checkPasswordStrength(pw, isNew = false) {
  if (!isNew) return; // skip meter on login panel
  const segs = ['nps1','nps2','nps3','nps4'];
  const classes = ['weak','fair','good','strong'];
  const labels  = ['Weak','Fair','Good','Strong 💪'];
  const rules = {
    len:   pw.length >= 8,
    upper: /[A-Z]/.test(pw),
    num:   /[0-9]/.test(pw),
    sym:   /[!@#$%^&*()_+\-=[\]{};':"\\|,.<>/?]/.test(pw),
  };
  const score = Object.values(rules).filter(Boolean).length;
  segs.forEach((id, i) => {
    const el = document.getElementById(id);
    if (!el) return;
    el.className = 'pw-seg';
    if (i < score) el.classList.add(classes[score - 1]);
  });
  const lbl = document.getElementById('newPwStrengthLabel');
  if (lbl) lbl.textContent = pw.length > 0 ? labels[score - 1] || '' : '';
  // Update rules visual
  Object.entries(rules).forEach(([key, ok]) => {
    const el = document.getElementById('rule-' + key);
    if (el) el.style.color = ok ? '#2e7d32' : '#8aa4bc';
  });
}

function checkConfirmPass() {
  const pw1 = document.getElementById('newPass').value;
  const pw2 = document.getElementById('confirmPass').value;
  const msg = document.getElementById('confirmMsg');
  const inp = document.getElementById('confirmPass');
  if (!pw2) { msg.textContent = ''; inp.classList.remove('error','valid'); return; }
  if (pw1 === pw2) {
    setFieldState(inp, msg, 'valid', 'ok', '✓ Passwords match');
  } else {
    setFieldState(inp, msg, 'error', 'err', '⚠ Passwords do not match');
  }
}

/* ══════════════════════════════════════════════════════════
   TOGGLE PASSWORD VISIBILITY
   ══════════════════════════════════════════════════════════ */
function togglePass() {
  const pw  = document.getElementById('password');
  const btn = document.getElementById('passToggle');
  pw.type = pw.type === 'password' ? 'text' : 'password';
  btn.textContent = pw.type === 'password' ? '👁' : '🙈';
}
function toggleField(id) {
  const el = document.getElementById(id);
  el.type = el.type === 'password' ? 'text' : 'password';
}

/* ══════════════════════════════════════════════════════════
   REMEMBER ME
   ══════════════════════════════════════════════════════════ */
function updateRememberNote() {
  const cb   = document.getElementById('rememberMe');
  const note = document.getElementById('rememberNote');
  note.textContent = cb.checked
    ? 'Session will persist for 8 hours on this device'
    : 'Session will expire when you close the browser';
}
document.getElementById('rememberMe').addEventListener('change', updateRememberNote);

/* ══════════════════════════════════════════════════════════
   LOGIN FLOW
   ══════════════════════════════════════════════════════════ */
function handleLogin(e) {
  e.preventDefault();
  if (loginAttempts >= MAX_ATTEMPTS) {
    showToast('Account locked after 5 failed attempts. Contact IT Admin.','error',5000);
    return;
  }
  const user = document.getElementById('username').value.trim();
  const pass = document.getElementById('password').value;
  if (!user) {
    document.getElementById('username').classList.add('error');
    document.getElementById('usernameMsg').className = 'field-msg err';
    document.getElementById('usernameMsg').textContent = '⚠ Please enter your username';
    document.getElementById('username').focus();
    return;
  }
  if (!pass || pass.length < 4) {
    document.getElementById('password').classList.add('error');
    document.getElementById('passwordMsg').className = 'field-msg err';
    document.getElementById('passwordMsg').textContent = '⚠ Please enter your password';
    document.getElementById('password').focus();
    return;
  }
  // Simulate 2FA check (in demo: always proceed to OTP)
  const info = ROLE_MAP[selectedRole] || {};
  document.getElementById('otpSubtitle').innerHTML =
    `A 6-digit OTP has been sent to <strong>${user.includes('@') ? user.replace(/(.{3}).+(@.+)/, '$1•••$2') : '••••••'}</strong> and mobile <strong>+91 ••••••${info.mobile || '87'}</strong>`;
  showPanel('panelOTP');
  startOTPTimer();
  showToast('OTP sent! (Demo: enter any 6 digits)', 'info', 3000);
  initOTPBoxes('otpRow');
}

function quickLogin(dest, roleLabel) {
  showToast(`Quick access: ${roleLabel || dest.replace('.html','')}`, 'info', 1500);
  startLoadingAndRedirect(dest, roleLabel || dest.replace('.html',''));
}

/* ══════════════════════════════════════════════════════════
   OTP LOGIC
   ══════════════════════════════════════════════════════════ */
function initOTPBoxes(rowId) {
  const boxes = document.getElementById(rowId).querySelectorAll('.otp-box');
  boxes.forEach((box, i) => {
    box.value = '';
    box.classList.remove('filled');
    box.addEventListener('input', () => {
      box.classList.toggle('filled', box.value.length > 0);
      if (box.value && i < boxes.length - 1) boxes[i + 1].focus();
    });
    box.addEventListener('keydown', e => {
      if (e.key === 'Backspace' && !box.value && i > 0) boxes[i - 1].focus();
    });
    box.addEventListener('paste', e => {
      const data = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g,'');
      boxes.forEach((b, j) => {
        b.value = data[j] || '';
        b.classList.toggle('filled', !!b.value);
      });
      e.preventDefault();
    });
  });
  if (boxes[0]) boxes[0].focus();
}

function startOTPTimer() {
  otpSeconds = 120;
  clearOTPTimer();
  const resendBtn = document.getElementById('btnResend');
  if (resendBtn) resendBtn.disabled = true;
  updateOTPDisplay();
  otpTimerInterval = setInterval(() => {
    otpSeconds--;
    updateOTPDisplay();
    if (otpSeconds <= 0) {
      clearOTPTimer();
      if (resendBtn) resendBtn.disabled = false;
    }
  }, 1000);
}

function updateOTPDisplay() {
  const el = document.getElementById('otpCountdown');
  if (!el) return;
  const m = Math.floor(otpSeconds / 60), s = otpSeconds % 60;
  el.textContent = `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
  el.style.color = otpSeconds < 30 ? '#c62828' : '#1565c0';
}

function clearOTPTimer() {
  if (otpTimerInterval) { clearInterval(otpTimerInterval); otpTimerInterval = null; }
}

function resendOTP() {
  showToast('New OTP sent! (Demo: any 6 digits)', 'info', 2500);
  startOTPTimer();
  initOTPBoxes('otpRow');
  document.getElementById('btnResend').disabled = true;
}

function verifyOTP() {
  const boxes = document.getElementById('otpRow').querySelectorAll('.otp-box');
  const otp   = Array.from(boxes).map(b => b.value).join('');
  if (otp.length < 6 || !/^\d{6}$/.test(otp)) {
    showToast('Please enter all 6 OTP digits','warning');
    boxes[0].focus();
    return;
  }
  // Demo: any 6-digit OTP is valid
  clearOTPTimer();
  startLoadingAndRedirect(selectedDest, ROLE_MAP[selectedRole]?.label || selectedRole);
}

/* ══════════════════════════════════════════════════════════
   FORGOT PASSWORD FLOW
   ══════════════════════════════════════════════════════════ */
function showForgot() {
  const user = document.getElementById('username').value.trim();
  if (user) document.getElementById('resetId').value = user;
  const info = ROLE_MAP[selectedRole] || {};
  document.getElementById('rmEmailVal').textContent = info.email ? info.email.replace(/(.{3}).+(@.+)/, '$1•••$2') : '••••@uk.gov.in';
  document.getElementById('rmSMSVal').textContent   = '+91 ••••••' + (info.mobile || '87');
  showPanel('panelForgot');
}

function selectResetMethod(method) {
  resetMethod = method;
  document.getElementById('rmEmail').classList.toggle('selected', method === 'email');
  document.getElementById('rmSMS').classList.toggle('selected', method === 'sms');
}

function sendResetOTP() {
  const id = document.getElementById('resetId').value.trim();
  if (!id) {
    showToast('Please enter your Employee ID or registered email','warning');
    document.getElementById('resetId').focus();
    return;
  }
  const sub = resetMethod === 'email'
    ? `OTP sent to ${id.includes('@') ? id.replace(/(.{3}).+(@.+)/, '$1•••$2') : '••••@uk.gov.in'}`
    : `OTP sent to your registered mobile number`;
  document.getElementById('resetOtpSub').textContent = sub;
  showPanel('panelResetOTP');
  initOTPBoxes('resetOtpRow');
  showToast(`Reset OTP sent via ${resetMethod === 'email' ? 'Email' : 'SMS'}! (Demo: any 6 digits)`, 'info', 3000);
}

function verifyResetOTP() {
  const boxes = document.getElementById('resetOtpRow').querySelectorAll('.otp-box');
  const otp   = Array.from(boxes).map(b => b.value).join('');
  if (otp.length < 6 || !/^\d{6}$/.test(otp)) {
    showToast('Please enter all 6 OTP digits','warning');
    return;
  }
  showPanel('panelNewPassword');
  document.getElementById('newPass').focus();
}

function submitNewPassword() {
  const pw1 = document.getElementById('newPass').value;
  const pw2 = document.getElementById('confirmPass').value;
  if (!pw1 || pw1.length < 8) {
    showToast('Password must be at least 8 characters','warning');
    document.getElementById('newPass').focus();
    return;
  }
  if (pw1 !== pw2) {
    showToast('Passwords do not match!','error');
    document.getElementById('confirmPass').focus();
    return;
  }
  const rules = [/[A-Z]/, /[0-9]/, /[!@#$%^&*()_+\-=[\]{};':"\\|,.<>/?]/];
  if (!rules.every(r => r.test(pw1))) {
    showToast('Password must contain uppercase, number and symbol','warning');
    return;
  }
  document.getElementById('successIcon').textContent  = '✅';
  document.getElementById('successTitle').textContent = 'Password Reset Successful!';
  document.getElementById('successSub').textContent   = 'Your password has been updated securely. You can now log in with your new password.';
  showPanel('panelSuccess');
  showToast('Password changed successfully!','success');
}

/* ══════════════════════════════════════════════════════════
   LOADING & REDIRECT
   ══════════════════════════════════════════════════════════ */
function startLoadingAndRedirect(dest, roleLabel) {
  const overlay = document.getElementById('loginLoading');
  const msgEl   = document.getElementById('loadingMsg');
  const subEl   = document.getElementById('loadingSub');
  const bar     = document.getElementById('ldBar');
  const msgs = [
    'Authenticating credentials…',
    'Verifying role permissions…',
    'Loading your dashboard…',
    'Connecting to State HMIS…',
  ];
  let i = 0, progress = 0;
  overlay.classList.add('show');
  msgEl.textContent = msgs[0];
  subEl.textContent = `Logging in as: ${roleLabel}`;
  bar.style.width = '0%';
  const msgInterval = setInterval(() => {
    i++;
    if (i < msgs.length) msgEl.textContent = msgs[i];
  }, 480);
  const barInterval = setInterval(() => {
    progress = Math.min(progress + 4, 95);
    bar.style.width = progress + '%';
  }, 80);
  setTimeout(() => {
    clearInterval(msgInterval);
    clearInterval(barInterval);
    bar.style.width = '100%';
    window.location.href = dest;
  }, 2200);
}

/* Alias for old quick demo calls */
function startLogin(dest) { startLoadingAndRedirect(dest, 'Portal User'); }

/* ══════════════════════════════════════════════════════════
   TOAST
   ══════════════════════════════════════════════════════════ */
function showToast(msg, type = 'info', duration = 3000) {
  const area  = document.getElementById('toastArea');
  const icons = { success:'✅', error:'❌', warning:'⚠️', info:'ℹ️' };
  const t = document.createElement('div');
  t.className = `toast ${type}`;
  t.innerHTML = `<span style="font-size:15px;flex-shrink:0">${icons[type]||'ℹ️'}</span><span>${msg}</span>`;
  area.appendChild(t);
  setTimeout(() => {
    t.style.cssText += 'opacity:0;transform:translateX(16px);transition:all .25s ease';
    setTimeout(() => t.remove(), 280);
  }, duration);
}

/* ══════════════════════════════════════════════════════════
   CLOCK & DATE
   ══════════════════════════════════════════════════════════ */
function updateClock() {
  const now  = new Date();
  const time = now.toLocaleTimeString('en-IN', { hour:'2-digit', minute:'2-digit', hour12:true });
  const date = now.toLocaleDateString('en-IN', { weekday:'long', day:'2-digit', month:'long', year:'numeric' });
  const el = document.getElementById('topTime');
  const dt = document.getElementById('fullDate');
  if (el) el.textContent = time;
  if (dt) dt.textContent = date;
}
updateClock();
setInterval(updateClock, 1000);

/* ══════════════════════════════════════════════════════════
   ANIMATED PARTICLES
   ══════════════════════════════════════════════════════════ */
(function spawnParticles() {
  const container = document.getElementById('particles');
  if (!container) return;
  const colors = ['rgba(21,101,192,.35)','rgba(0,105,92,.3)','rgba(230,81,0,.2)','rgba(249,168,37,.25)'];
  for (let i = 0; i < 22; i++) {
    const p = document.createElement('div');
    p.className = 'particle';
    const size = 3 + Math.random() * 6;
    p.style.cssText = `
      width:${size}px;height:${size}px;
      left:${Math.random()*100}%;
      bottom:${Math.random()*-10}%;
      background:${colors[Math.floor(Math.random()*colors.length)]};
      animation-duration:${8+Math.random()*14}s;
      animation-delay:${Math.random()*10}s;
    `;
    container.appendChild(p);
  }
})();

/* ══════════════════════════════════════════════════════════
   REMEMBER ME — Restore saved session
   ══════════════════════════════════════════════════════════ */
(function restoreSession() {
  try {
    const saved = localStorage.getItem('paracare_session');
    if (!saved) return;
    const data = JSON.parse(saved);
    const now  = Date.now();
    if (data.expiry && now < data.expiry && data.dest) {
      // Show a restore banner instead of auto-redirecting
      showToast(`Welcome back, ${data.role || 'User'}! Your session is still active.`, 'success', 4000);
    }
  } catch(e) {}
})();

function saveSession(dest, role) {
  try {
    const cb = document.getElementById('rememberMe');
    if (!cb || !cb.checked) return;
    localStorage.setItem('paracare_session', JSON.stringify({
      dest, role,
      expiry: Date.now() + 8 * 60 * 60 * 1000,
    }));
  } catch(e) {}
}

/* Override redirect to also save session */
const _orig = startLoadingAndRedirect;
// patch to save before redirect
window.addEventListener('beforeunload', () => {
  saveSession(selectedDest, ROLE_MAP[selectedRole]?.label || selectedRole);
});

/* ══════════════════════════════════════════════════════════
   ACCOUNT LOCKOUT WARNING
   ══════════════════════════════════════════════════════════ */
document.getElementById('loginForm').addEventListener('submit', () => {
  loginAttempts++;
  if (loginAttempts >= MAX_ATTEMPTS - 1) {
    showToast(`Warning: ${MAX_ATTEMPTS - loginAttempts} attempt(s) remaining before lockout`, 'warning', 4000);
  }
});

/* ══════════════════════════════════════════════════════════
   KEYBOARD TRAP WITHIN OTP PANEL
   ══════════════════════════════════════════════════════════ */
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') {
    const active = document.querySelector('.panel.active');
    if (active && active.id !== 'panelLogin') showPanel('panelLogin');
  }
});
</script>
</body>
</html>
