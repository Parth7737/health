@extends('layouts.hospital.app', ['is_dashbaord' => true, 'hidePageHeader' => true, 'usebillingSidebar' => true])

@section('title', 'Billing & Finance')
@section('page_subtitle', 'Billing & Finance Management')

@section('page_header_actions')
      <span class="flex" style="gap:6px;font-size:12px;color:var(--muted)"><span class="status-dot"></span> Live</span>
      <button class="btn btn-success btn-sm" onclick="switchTab('invoice')"><i class="fas fa-plus"></i> New Bill</button>
      <button class="btn btn-outline btn-sm" onclick="showToast('Daily summary exported','info')"><i class="fas fa-download"></i> Export</button>
@endsection

@push('styles')
@include('layouts.partials.flatpickr-css')
<link rel="stylesheet" type="text/css" href="{{asset('public/front/assets/css/billing.css')}}">

@endpush
@section('content')

<!-- STATS -->
<div class="stats-grid">
    <div class="stat-card green"><i class="fas fa-rupee-sign s-icon"></i>
        <div class="s-label">Today's Revenue</div>
        <div class="s-value">₹1.24L</div>
        <div class="s-meta">↑ 12% vs yesterday</div>
    </div>
    <div class="stat-card blue"><i class="fas fa-file-invoice s-icon"></i>
        <div class="s-label">Bills Generated</div>
        <div class="s-value">87</div>
        <div class="s-meta">OPD: 64 | IPD: 23</div>
    </div>
    <div class="stat-card orange"><i class="fas fa-clock s-icon"></i>
        <div class="s-label">Pending Payments</div>
        <div class="s-value">₹38K</div>
        <div class="s-meta">14 bills unpaid</div>
    </div>
    <div class="stat-card purple"><i class="fas fa-shield-alt s-icon"></i>
        <div class="s-label">Insurance Claims</div>
        <div class="s-value">₹2.1L</div>
        <div class="s-meta">8 under process</div>
    </div>
    <div class="stat-card red"><i class="fas fa-exclamation-triangle s-icon"></i>
        <div class="s-label">Overdue >30 Days</div>
        <div class="s-value">₹12K</div>
        <div class="s-meta">5 patients</div>
    </div>
</div>

<!-- TABS -->
<div class="tab-bar">
    <button class="tab-btn active" onclick="switchTab('dashboard')"><i class="fas fa-chart-pie"></i> Dashboard</button>
    <button class="tab-btn" onclick="switchTab('invoice')"><i class="fas fa-plus"></i> New Bill</button>
    <button class="tab-btn" onclick="switchTab('records')"><i class="fas fa-list"></i> Bill Records</button>
    <button class="tab-btn" onclick="switchTab('payment')"><i class="fas fa-money-bill-wave"></i> Payments</button>
    <button class="tab-btn" onclick="switchTab('insurance')"><i class="fas fa-shield-alt"></i> Insurance/TPA</button>
    <button class="tab-btn" onclick="switchTab('credit')"><i class="fas fa-university"></i> Credit/Advances</button>
    <button class="tab-btn" onclick="switchTab('refunds')"><i class="fas fa-undo"></i> Refunds</button>
    <button class="tab-btn" onclick="switchTab('revenue')"><i class="fas fa-chart-bar"></i> Revenue</button>
</div>

<!-- DASHBOARD -->
<div id="panel-dashboard" class="panel active">
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px">
        <div>
            <div class="card">
                <div class="card-header"><span class="card-title"><i class="fas fa-chart-line"
                            style="color:#2e7d32"></i> Revenue Trend (Last 14 Days)</span></div>
                <div class="card-body"><canvas id="revTrendChart" height="200"></canvas></div>
            </div>
            <div class="card">
                <div class="card-header"><span class="card-title"><i class="fas fa-list" style="color:#1565c0"></i>
                        Recent Transactions</span>
                    <button class="btn btn-outline btn-sm" onclick="switchTab('records')">View All</button>
                </div>
                <div class="card-body" style="padding:0">
                    <table>
                        <thead>
                            <tr>
                                <th>Bill No</th>
                                <th>Patient</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Time</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="recentTransBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div>
            <div class="card">
                <div class="card-header"><span class="card-title"><i class="fas fa-chart-pie" style="color:#6a1b9a"></i>
                        Revenue by Service</span></div>
                <div class="card-body" style="height:220px"><canvas id="revByServiceChart"></canvas></div>
            </div>
            <div class="card mt-4">
                <div class="card-header"><span class="card-title"><i class="fas fa-chart-bar" style="color:#e65100"></i>
                        Payment Modes</span></div>
                <div class="card-body" style="height:200px"><canvas id="payModeChart"></canvas></div>
            </div>
            <!-- KPI quick view -->
            <div class="card mt-4">
                <div class="card-header"><span class="card-title"><i class="fas fa-tachometer-alt"
                            style="color:#2e7d32"></i> Today's KPIs</span></div>
                <div class="card-body">
                    <div class="info-row"><span class="lbl">Average Bill Value</span><span class="fw-700">₹1,425</span>
                    </div>
                    <div class="info-row"><span class="lbl">Collection Rate</span><span class="fw-700"
                            style="color:#2e7d32">87.4%</span></div>
                    <div class="info-row"><span class="lbl">Concession Given</span><span class="fw-700"
                            style="color:#e65100">₹8,200</span></div>
                    <div class="info-row"><span class="lbl">Cashless Transactions</span><span class="fw-700">42</span>
                    </div>
                    <div class="info-row"><span class="lbl">New Deposits</span><span class="fw-700">₹15,000</span></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- NEW INVOICE -->
<div id="panel-invoice" class="panel">
    <div class="invoice-layout">
        <div>
            <!-- Patient Info -->
            <div class="card">
                <div class="card-header"><span class="card-title"><i class="fas fa-user" style="color:#1565c0"></i>
                        Patient Information</span>
                    <button class="btn btn-outline btn-sm" onclick="searchPatient()"><i class="fas fa-search"></i>
                        Search Patient</button>
                </div>
                <div class="card-body">
                    <div class="grid-3">
                        <div class="form-group"><label>UHID / MRD No</label><input type="text" id="billUhid"
                                placeholder="Enter UHID..."></div>
                        <div class="form-group"><label>Patient Name</label><input type="text" id="billPatient"
                                value="Ramesh Kumar"></div>
                        <div class="form-group"><label>Age / Sex</label><input type="text" value="42 / M"></div>
                        <div class="form-group"><label>Visit Type</label>
                            <select id="billType" onchange="updateBillType()">
                                <option>OPD</option>
                                <option>IPD</option>
                                <option>Emergency</option>
                                <option>Day Care</option>
                            </select>
                        </div>
                        <div class="form-group"><label>Department</label>
                            <select>
                                <option>General Medicine</option>
                                <option>Surgery</option>
                                <option>Orthopaedics</option>
                                <option>Gynaecology</option>
                                <option>Paediatrics</option>
                                <option>ENT</option>
                                <option>Ophthalmology</option>
                                <option>Cardiology</option>
                                <option>Neurology</option>
                            </select>
                        </div>
                        <div class="form-group"><label>Consulting Doctor</label>
                            <select>
                                <option>Dr. Priya Sharma</option>
                                <option>Dr. Rajesh Verma</option>
                                <option>Dr. Anita Rawat</option>
                                <option>Dr. Suresh Pant</option>
                            </select>
                        </div>
                        <div class="form-group"><label>Mobile</label><input type="text" value="98765-43210"></div>
                        <div class="form-group"><label>Payment Type</label>
                            <select id="payTypeSelect" onchange="updatePayType()">
                                <option>Cash</option>
                                <option>Insurance / TPA</option>
                                <option>Government Scheme</option>
                                <option>Credit</option>
                            </select>
                        </div>
                        <div class="form-group"><label>Date</label><input type="date" value="2025-04-12"></div>
                    </div>
                </div>
            </div>

            <!-- Services -->
            <div class="card">
                <div class="card-header"><span class="card-title"><i class="fas fa-list-ul" style="color:#2e7d32"></i>
                        Services & Items</span>
                    <button class="btn btn-success btn-sm" onclick="addServiceRow()"><i class="fas fa-plus"></i> Add
                        Service</button>
                </div>
                <div class="card-body">
                    <div class="service-add-row"
                        style="background:#e8f5e9;font-size:11px;font-weight:700;color:var(--muted)">
                        <span>Service / Item</span><span>Category</span><span>Qty</span><span>Rate
                            (₹)</span><span>Action</span>
                    </div>
                    <div id="serviceRows"></div>
                    <button class="btn btn-outline btn-sm mt-4" onclick="openModal('serviceModal')"><i
                            class="fas fa-search"></i> Browse Service Catalogue</button>
                </div>
            </div>

            <!-- Discounts / Schemes -->
            <div class="card">
                <div class="card-header"><span class="card-title"><i class="fas fa-tag" style="color:#e65100"></i>
                        Discount & Scheme</span></div>
                <div class="card-body">
                    <div class="grid-3">
                        <div class="form-group"><label>Discount Type</label>
                            <select id="discountType" onchange="calcTotal()">
                                <option value="">None</option>
                                <option value="percent">Percentage</option>
                                <option value="flat">Flat Amount</option>
                                <option value="scheme">Govt. Scheme</option>
                            </select>
                        </div>
                        <div class="form-group"><label>Value</label><input type="number" id="discountVal"
                                placeholder="0" oninput="calcTotal()"></div>
                        <div class="form-group"><label>Approved By</label><input type="text"
                                placeholder="Doctor / Admin name"></div>
                    </div>
                    <div class="grid-2">
                        <div class="form-group"><label>Govt. Scheme</label>
                            <select>
                                <option>None</option>
                                <option>Ayushman Bharat</option>
                                <option>PMJAY</option>
                                <option>Uttarakhand State Scheme</option>
                                <option>ESI</option>
                                <option>CGHS</option>
                                <option>Armed Forces</option>
                            </select>
                        </div>
                        <div class="form-group"><label>Scheme ID / Card No</label><input type="text"
                                placeholder="Scheme card / policy number"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SUMMARY PANEL -->
        <div>
            <div class="inv-summary" id="invoiceSummary">
                <h3 class="text-white"><i class="fas fa-receipt"></i> Bill Summary</h3>
                <div class="inv-line"><span class="lbl">Consultation</span><span id="sumConsult">₹0</span></div>
                <div class="inv-line"><span class="lbl">Investigation</span><span id="sumInv">₹0</span></div>
                <div class="inv-line"><span class="lbl">Procedures</span><span id="sumProc">₹0</span></div>
                <div class="inv-line"><span class="lbl">Medicine / Pharmacy</span><span id="sumPharm">₹0</span></div>
                <div class="inv-line"><span class="lbl">Room / Bed Charges</span><span id="sumBed">₹0</span></div>
                <div class="inv-line"><span class="lbl">Subtotal</span><span id="sumSubtotal">₹0</span></div>
                <div class="inv-line"><span class="lbl">Discount</span><span id="sumDiscount" style="color:#ffcc80">-
                        ₹0</span></div>
                <div class="inv-line"><span class="lbl">CGST (5%)</span><span id="sumCGST">₹0</span></div>
                <div class="inv-line"><span class="lbl">SGST (5%)</span><span id="sumSGST">₹0</span></div>
                <div class="inv-line total"><span>GRAND TOTAL</span><span id="sumTotal" style="font-size:20px">₹0</span>
                </div>
                <div style="margin-top:14px">
                    <div class="form-group" style="margin-bottom:10px">
                        <label style="color:rgba(255,255,255,.7);font-size:12px">Amount Received (₹)</label>
                        <input type="number" id="amtReceived" placeholder="0" oninput="calcBalance()"
                            style="background:rgba(255,255,255,.15);border-color:rgba(255,255,255,.25);color:#fff;border-radius:8px;padding:8px 12px;width:100%;font-size:14px;font-weight:700;outline:none">
                    </div>
                    <div class="inv-line" style="border:none"><span class="lbl">Balance Due</span><span id="balanceDue"
                            style="color:#ffcc80;font-weight:700">₹0</span></div>
                </div>
                <div style="margin-top:16px;display:flex;flex-direction:column;gap:8px">
                    <button class="btn btn-success" style="width:100%;justify-content:center"
                        onclick="generateBill()"><i class="fas fa-check"></i> Generate Bill & Print</button>
                    <button class="btn"
                        style="width:100%;justify-content:center;background:rgba(255,255,255,.15);color:#fff"
                        onclick="saveDraftBill()"><i class="fas fa-save"></i> Save Draft</button>
                    <button class="btn"
                        style="width:100%;justify-content:center;background:rgba(255,255,255,.1);color:#fff"
                        onclick="clearBill()"><i class="fas fa-trash"></i> Clear</button>
                </div>
            </div>
            <!-- Payment Method -->
            <div class="card mt-4">
                <div class="card-header"><span class="card-title"><i class="fas fa-credit-card"
                            style="color:#1565c0"></i> Payment Method</span></div>
                <div class="card-body">
                    <div class="payment-methods">
                        <div class="pay-method selected" onclick="selectPayMethod(this,'cash')"><i
                                class="fas fa-money-bill-wave"></i>
                            <div class="pm-name">Cash</div>
                        </div>
                        <div class="pay-method" onclick="selectPayMethod(this,'upi')"><i class="fab fa-google-pay"
                                style="font-size:24px;color:#2e7d32;display:block;margin-bottom:6px"></i>
                            <div class="pm-name">UPI</div>
                        </div>
                        <div class="pay-method" onclick="selectPayMethod(this,'card')"><i
                                class="fas fa-credit-card"></i>
                            <div class="pm-name">Card</div>
                        </div>
                        <div class="pay-method" onclick="selectPayMethod(this,'neft')"><i class="fas fa-university"></i>
                            <div class="pm-name">NEFT</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Transaction / Reference No</label>
                        <input type="text" placeholder="UTR / Txn Reference (if any)">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- BILL RECORDS -->
<div id="panel-records" class="panel">
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-list" style="color:#1565c0"></i> Bill Records</span>
            <div class="flex" style="gap:8px">
                <input type="text" placeholder="Search patient, bill no..."
                    style="padding:7px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;outline:none;width:220px">
                <select
                    style="padding:7px 10px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;outline:none">
                    <option>All Status</option>
                    <option>Paid</option>
                    <option>Pending</option>
                    <option>Partial</option>
                    <option>Credit</option>
                </select>
                <button class="btn btn-outline btn-sm" onclick="showToast('Exporting bill records...','info')"><i
                        class="fas fa-download"></i> Export</button>
            </div>
        </div>
        <div class="card-body" style="padding:0">
            <table>
                <thead>
                    <tr>
                        <th>Bill No</th>
                        <th>Patient</th>
                        <th>UHID</th>
                        <th>Visit</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="billRecordsBody"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- PAYMENT COLLECTION -->
<div id="panel-payment" class="panel">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
        <div class="card">
            <div class="card-header"><span class="card-title"><i class="fas fa-hand-holding-usd"
                        style="color:#2e7d32"></i> Collect Payment</span></div>
            <div class="card-body">
                <div class="form-group"><label>Bill Number / Patient UHID</label><input type="text"
                        placeholder="Enter bill number..."></div>
                <div style="background:#f9fbff;border-radius:10px;padding:14px;border:1px solid var(--border);margin-bottom:14px"
                    id="paymentBillInfo">
                    <div class="info-row"><span class="lbl">Patient</span><span class="fw-700">Kishore Negi</span></div>
                    <div class="info-row"><span class="lbl">Bill No</span><span>BL-2025-0412-018</span></div>
                    <div class="info-row"><span class="lbl">Total Amount</span><span class="fw-700">₹4,500</span></div>
                    <div class="info-row"><span class="lbl">Already Paid</span><span
                            style="color:#2e7d32;font-weight:700">₹2,000</span></div>
                    <div class="info-row"><span class="lbl">Balance Due</span><span
                            style="color:#c62828;font-weight:700;font-size:16px">₹2,500</span></div>
                </div>
                <div class="form-group"><label>Payment Mode</label>
                    <select>
                        <option>Cash</option>
                        <option>UPI</option>
                        <option>Card</option>
                        <option>NEFT</option>
                        <option>Cheque</option>
                    </select>
                </div>
                <div class="form-group"><label>Amount Collecting (₹)</label><input type="number" placeholder="2500"
                        value="2500"></div>
                <div class="form-group"><label>Reference / UTR</label><input type="text"
                        placeholder="Transaction reference"></div>
                <div class="form-group"><label>Remarks</label><input type="text" placeholder="Optional remarks"></div>
                <button class="btn btn-success" style="width:100%;justify-content:center" onclick="collectPayment()"><i
                        class="fas fa-check"></i> Collect & Print Receipt</button>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><span class="card-title"><i class="fas fa-history" style="color:#1565c0"></i>
                    Today's Collections</span>
                <span class="badge badge-green fw-700">₹1,24,800</span>
            </div>
            <div class="card-body" style="padding:0">
                <table>
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Patient</th>
                            <th>Amount</th>
                            <th>Mode</th>
                            <th>By</th>
                        </tr>
                    </thead>
                    <tbody id="collectionBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- INSURANCE / TPA -->
<div id="panel-insurance" class="panel">
    <div class="ins-card">
        <h4><i class="fas fa-shield-alt" style="margin-right:8px"></i> Active Insurance Verification Panel</h4>
        <div class="grid-2" style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
            <div class="ins-field"><span class="lbl">Scheme</span><span>Ayushman Bharat PMJAY</span></div>
            <div class="ins-field"><span class="lbl">Patient</span><span>Ramesh Kumar</span></div>
            <div class="ins-field"><span class="lbl">Card No</span><span>PMJAY-UK-2025-88741</span></div>
            <div class="ins-field"><span class="lbl">Eligibility</span><span
                    style="color:#a5d6a7;font-weight:700">Eligible</span></div>
            <div class="ins-field"><span class="lbl">Limit</span><span>₹5,00,000 / year</span></div>
            <div class="ins-field"><span class="lbl">Used</span><span>₹1,20,000</span></div>
            <div class="ins-field"><span class="lbl">Balance</span><span
                    style="color:#a5d6a7;font-weight:700">₹3,80,000</span></div>
            <div class="ins-field"><span class="lbl">TPA Name</span><span>MD India Health Insurance</span></div>
        </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
        <div class="card">
            <div class="card-header"><span class="card-title"><i class="fas fa-clipboard-list"
                        style="color:#1565c0"></i> Pre-Auth Requests</span>
                <button class="btn btn-primary btn-sm" onclick="openModal('preAuthModal')"><i class="fas fa-plus"></i>
                    New Pre-Auth</button>
            </div>
            <div class="card-body" style="padding:0">
                <table>
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Procedure</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="preAuthBody"></tbody>
                </table>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><span class="card-title"><i class="fas fa-file-invoice" style="color:#6a1b9a"></i>
                    Claims Submitted</span>
                <button class="btn btn-purple btn-sm" style="background:#6a1b9a;color:#fff"
                    onclick="showToast('Submitting batch claims...','info')"><i class="fas fa-paper-plane"></i> Submit
                    Batch</button>
            </div>
            <div class="card-body" style="padding:0">
                <table>
                    <thead>
                        <tr>
                            <th>Claim No</th>
                            <th>Patient</th>
                            <th>Amount</th>
                            <th>Submitted</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="claimsBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- CREDIT / ADVANCES -->
<div id="panel-credit" class="panel">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
        <div class="card">
            <div class="card-header"><span class="card-title"><i class="fas fa-university" style="color:#1565c0"></i>
                    Patient Deposits</span>
                <button class="btn btn-primary btn-sm" onclick="openModal('depositModal')"><i class="fas fa-plus"></i>
                    New Deposit</button>
            </div>
            <div class="card-body" style="padding:0">
                <table>
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Date</th>
                            <th>Deposited</th>
                            <th>Used</span></th>
                            <th>Balance</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="depositsBody"></tbody>
                </table>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><span class="card-title"><i class="fas fa-building" style="color:#2e7d32"></i>
                    Credit Organisations</span></div>
            <div class="card-body" style="padding:0">
                <table>
                    <thead>
                        <tr>
                            <th>Organisation</th>
                            <th>Limit</th>
                            <th>Used</th>
                            <th>Balance</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="creditOrgBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- REFUNDS -->
<div id="panel-refunds" class="panel">
    <div class="card">
        <div class="card-header"><span class="card-title"><i class="fas fa-undo" style="color:#e65100"></i> Refund
                Requests</span>
            <button class="btn btn-warning btn-sm" onclick="openModal('refundModal')"><i class="fas fa-plus"></i> New
                Refund</button>
        </div>
        <div class="card-body" style="padding:0">
            <table>
                <thead>
                    <tr>
                        <th>Refund No</th>
                        <th>Bill No</th>
                        <th>Patient</th>
                        <th>Reason</th>
                        <th>Amount</th>
                        <th>Requested By</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="refundsBody"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- REVENUE REPORTS -->
<div id="panel-revenue" class="panel">
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:16px">
        <div class="card">
            <div class="card-body" style="text-align:center;padding:20px">
                <div style="font-size:28px;font-weight:800;color:#2e7d32">₹38.6L</div>
                <div style="font-size:12px;color:var(--muted);margin-top:4px">Monthly Revenue</div>
                <div style="font-size:11px;color:#2e7d32;margin-top:4px">↑ 8.4% vs last month</div>
            </div>
        </div>
        <div class="card">
            <div class="card-body" style="text-align:center;padding:20px">
                <div style="font-size:28px;font-weight:800;color:#1565c0">₹4,44,500</div>
                <div style="font-size:12px;color:var(--muted);margin-top:4px">Annual (YTD)</div>
                <div style="font-size:11px;color:#1565c0;margin-top:4px">Target: ₹5.4Cr</div>
            </div>
        </div>
        <div class="card">
            <div class="card-body" style="text-align:center;padding:20px">
                <div style="font-size:28px;font-weight:800;color:#e65100">₹2.1L</div>
                <div style="font-size:12px;color:var(--muted);margin-top:4px">Outstanding Dues</div>
                <div style="font-size:11px;color:#e65100;margin-top:4px">22 bills pending</div>
            </div>
        </div>
    </div>
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px">
        <div class="card">
            <div class="card-header"><span class="card-title"><i class="fas fa-chart-bar" style="color:#1565c0"></i>
                    Monthly Revenue Breakdown</span>
                <div class="flex" style="gap:8px">
                    <select
                        style="padding:6px 10px;border:1.5px solid var(--border);border-radius:7px;font-size:12px;outline:none">
                        <option>April 2025</option>
                        <option>March 2025</option>
                        <option>February 2025</option>
                    </select>
                    <button class="btn btn-outline btn-sm" onclick="showToast('Downloading revenue report','info')"><i
                            class="fas fa-download"></i></button>
                </div>
            </div>
            <div class="card-body"><canvas id="monthlyRevChart" height="250"></canvas></div>
        </div>
        <div>
            <div class="card">
                <div class="card-header"><span class="card-title"><i class="fas fa-table" style="color:#2e7d32"></i>
                        Dept-wise Revenue</span></div>
                <div class="card-body" style="padding:0">
                    <table>
                        <thead>
                            <tr>
                                <th>Department</th>
                                <th>Revenue</th>
                                <th>%</th>
                            </tr>
                        </thead>
                        <tbody id="deptRevBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- SERVICE CATALOGUE MODAL -->
<div class="modal-overlay hidden" id="serviceModal">
    <div class="modal">
        <div class="modal-header">
            <h2><i class="fas fa-search" style="color:#1565c0;margin-right:8px"></i> Service Catalogue</h2>
            <button class="modal-close" onclick="closeModal('serviceModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <input type="text" placeholder="Search services..."
                style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;outline:none;margin-bottom:14px">
            <div style="overflow-y:auto;max-height:400px">
                <table>
                    <thead>
                        <tr>
                            <th>Service Name</th>
                            <th>Category</th>
                            <th>Rate (₹)</th>
                            <th>Add</th>
                        </tr>
                    </thead>
                    <tbody id="catalogueBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- PRE-AUTH MODAL -->
<div class="modal-overlay hidden" id="preAuthModal">
    <div class="modal">
        <div class="modal-header">
            <h2><i class="fas fa-clipboard-check" style="color:#6a1b9a;margin-right:8px"></i> New Pre-Auth Request</h2>
            <button class="modal-close" onclick="closeModal('preAuthModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div class="grid-2" style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div class="form-group"><label>Patient</label><input type="text" placeholder="Patient name / UHID">
                </div>
                <div class="form-group"><label>Insurance Scheme</label><select>
                        <option>Ayushman Bharat</option>
                        <option>PMJAY</option>
                        <option>CGHS</option>
                        <option>ESI</option>
                    </select></div>
                <div class="form-group"><label>Procedure</label><input type="text" placeholder="Procedure name"></div>
                <div class="form-group"><label>Estimated Amount</label><input type="number" placeholder="0"></div>
                <div class="form-group" style="grid-column:1/-1"><label>Clinical Justification</label><textarea rows="3"
                        placeholder="Clinical need and justification..."></textarea></div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('preAuthModal')">Cancel</button>
            <button class="btn btn-primary" onclick="submitPreAuth()"><i class="fas fa-paper-plane"></i> Submit</button>
        </div>
    </div>
</div>

<!-- DEPOSIT MODAL -->
<div class="modal-overlay hidden" id="depositModal">
    <div class="modal">
        <div class="modal-header">
            <h2><i class="fas fa-university" style="color:#2e7d32;margin-right:8px"></i> New Patient Deposit</h2>
            <button class="modal-close" onclick="closeModal('depositModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div class="grid-2" style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div class="form-group"><label>Patient UHID</label><input type="text" placeholder="UHID"></div>
                <div class="form-group"><label>Patient Name</label><input type="text" placeholder="Name"></div>
                <div class="form-group"><label>Deposit Amount</label><input type="number" placeholder="0"></div>
                <div class="form-group"><label>Payment Mode</label><select>
                        <option>Cash</option>
                        <option>UPI</option>
                        <option>Card</option>
                    </select></div>
                <div class="form-group" style="grid-column:1/-1"><label>Remarks</label><input type="text"
                        placeholder="Deposit purpose..."></div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('depositModal')">Cancel</button>
            <button class="btn btn-success" onclick="saveDeposit()"><i class="fas fa-check"></i> Save Deposit</button>
        </div>
    </div>
</div>

<!-- REFUND MODAL -->
<div class="modal-overlay hidden" id="refundModal">
    <div class="modal">
        <div class="modal-header">
            <h2><i class="fas fa-undo" style="color:#e65100;margin-right:8px"></i> New Refund Request</h2>
            <button class="modal-close" onclick="closeModal('refundModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div class="grid-2" style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div class="form-group"><label>Bill Number</label><input type="text" placeholder="BL-2025-..."></div>
                <div class="form-group"><label>Refund Amount</label><input type="number" placeholder="0"></div>
                <div class="form-group"><label>Reason</label><select>
                        <option>Service Not Rendered</option>
                        <option>Duplicate Payment</option>
                        <option>Overpayment</option>
                        <option>Cancelled Admission</option>
                    </select></div>
                <div class="form-group"><label>Approved By</label><input type="text" placeholder="Approving authority">
                </div>
                <div class="form-group" style="grid-column:1/-1"><label>Remarks</label><textarea rows="2"
                        placeholder="Detailed reason..."></textarea></div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('refundModal')">Cancel</button>
            <button class="btn btn-warning" onclick="saveRefund()"><i class="fas fa-check"></i> Submit Refund</button>
        </div>
    </div>
</div>

<div class="toast-container" id="toastContainer"></div>

@endsection
@push('scripts')
@include('layouts.partials.flatpickr-js')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ── DATA ──────────────────────────────────────────────────────────────────
const billRecords = [{
        no: 'BL-001',
        patient: 'Ramesh Kumar',
        uhid: 'UK-00421',
        visit: 'IPD',
        date: '12/04',
        amount: 12500,
        paid: 12500,
        balance: 0,
        status: 'Paid'
    },
    {
        no: 'BL-002',
        patient: 'Savita Devi',
        uhid: 'UK-00318',
        visit: 'OPD',
        date: '12/04',
        amount: 850,
        paid: 850,
        balance: 0,
        status: 'Paid'
    },
    {
        no: 'BL-003',
        patient: 'Kishore Negi',
        uhid: 'UK-00512',
        visit: 'IPD',
        date: '12/04',
        amount: 4500,
        paid: 2000,
        balance: 2500,
        status: 'Partial'
    },
    {
        no: 'BL-004',
        patient: 'Meena Bisht',
        uhid: 'UK-00290',
        visit: 'OPD',
        date: '12/04',
        amount: 1200,
        paid: 0,
        balance: 1200,
        status: 'Pending'
    },
    {
        no: 'BL-005',
        patient: 'Arjun Singh',
        uhid: 'UK-00601',
        visit: 'Emergency',
        date: '12/04',
        amount: 3200,
        paid: 3200,
        balance: 0,
        status: 'Paid'
    },
    {
        no: 'BL-006',
        patient: 'Pushpa Karki',
        uhid: 'UK-00445',
        visit: 'OPD',
        date: '12/04',
        amount: 650,
        paid: 650,
        balance: 0,
        status: 'Paid'
    },
    {
        no: 'BL-007',
        patient: 'Mohan Lal',
        uhid: 'UK-00389',
        visit: 'IPD',
        date: '11/04',
        amount: 8900,
        paid: 0,
        balance: 8900,
        status: 'Credit'
    },
    {
        no: 'BL-008',
        patient: 'Anita Thapa',
        uhid: 'UK-00271',
        visit: 'OPD',
        date: '11/04',
        amount: 720,
        paid: 720,
        balance: 0,
        status: 'Paid'
    },
];

const collections = [{
        time: '08:22',
        patient: 'Savita Devi',
        amount: '₹850',
        mode: 'UPI',
        by: 'Cashier 1'
    },
    {
        time: '08:45',
        patient: 'Ramesh Kumar',
        amount: '₹12,500',
        mode: 'Card',
        by: 'Cashier 1'
    },
    {
        time: '09:10',
        patient: 'Arjun Singh',
        amount: '₹3,200',
        mode: 'Cash',
        by: 'Cashier 2'
    },
    {
        time: '09:35',
        patient: 'Pushpa Karki',
        amount: '₹650',
        mode: 'Cash',
        by: 'Cashier 1'
    },
    {
        time: '10:05',
        patient: 'Anita Thapa',
        amount: '₹720',
        mode: 'UPI',
        by: 'Cashier 2'
    },
    {
        time: '10:22',
        patient: 'Bhupesh Pandey',
        amount: '₹1,100',
        mode: 'Cash',
        by: 'Cashier 1'
    },
];

const preAuthData = [{
        patient: 'Mohan Lal',
        procedure: 'CABG Surgery',
        amount: '₹2,50,000',
        status: 'Approved'
    },
    {
        patient: 'Meena Bisht',
        procedure: 'MRI Brain',
        amount: '₹8,500',
        status: 'Pending'
    },
    {
        patient: 'Kishore Negi',
        procedure: 'Lap Cholecystectomy',
        amount: '₹65,000',
        status: 'Under Review'
    },
];

const claimsData = [{
        claim: 'CLM-2025-001',
        patient: 'Ramesh Kumar',
        amount: '₹45,000',
        date: '08/04',
        status: 'Submitted'
    },
    {
        claim: 'CLM-2025-002',
        patient: 'Savita Devi',
        amount: '₹12,000',
        date: '05/04',
        status: 'Approved'
    },
    {
        claim: 'CLM-2025-003',
        patient: 'Pushpa Karki',
        amount: '₹8,500',
        date: '01/04',
        status: 'Settled'
    },
    {
        claim: 'CLM-2025-004',
        patient: 'Mohan Lal',
        amount: '₹1,80,000',
        date: '10/04',
        status: 'Under Review'
    },
];

const depositsData = [{
        patient: 'Kishore Negi',
        date: '10/04',
        deposited: '₹10,000',
        used: '₹5,000',
        balance: '₹5,000'
    },
    {
        patient: 'Meena Bisht',
        date: '12/04',
        deposited: '₹5,000',
        used: '₹0',
        balance: '₹5,000'
    },
    {
        patient: 'Anita Thapa',
        date: '11/04',
        deposited: '₹2,000',
        used: '₹720',
        balance: '₹1,280'
    },
];

const creditOrgs = [{
        name: 'Uttarakhand Police Welfare',
        limit: '₹2,00,000',
        used: '₹45,000',
        balance: '₹1,55,000',
        status: 'Active'
    },
    {
        name: 'ONGC Employees',
        limit: '₹5,00,000',
        used: '₹1,20,000',
        balance: '₹3,80,000',
        status: 'Active'
    },
    {
        name: 'Army Unit 18 RR',
        limit: '₹3,00,000',
        used: '₹2,80,000',
        balance: '₹20,000',
        status: 'Near Limit'
    },
];

const refundsData = [{
        no: 'REF-001',
        bill: 'BL-2024-890',
        patient: 'Ram Prasad',
        reason: 'Cancelled Test',
        amount: '₹2,200',
        by: 'Dr. Sharma',
        status: 'Approved'
    },
    {
        no: 'REF-002',
        bill: 'BL-2025-012',
        patient: 'Kamala Devi',
        reason: 'Overpayment',
        amount: '₹500',
        by: 'Admin',
        status: 'Pending'
    },
];

const deptRevData = [{
        dept: 'General Medicine',
        rev: '₹8,20,000',
        pct: '21%'
    },
    {
        dept: 'Surgery',
        rev: '₹7,40,000',
        pct: '19%'
    },
    {
        dept: 'Orthopaedics',
        rev: '₹5,60,000',
        pct: '15%'
    },
    {
        dept: 'Radiology',
        rev: '₹5,44,000',
        pct: '14%'
    },
    {
        dept: 'Gynaecology',
        rev: '₹4,20,000',
        pct: '11%'
    },
    {
        dept: 'Paediatrics',
        rev: '₹3,10,000',
        pct: '8%'
    },
    {
        dept: 'Others',
        rev: '₹4,62,000',
        pct: '12%'
    },
];

const catalogue = [{
        name: 'OPD Consultation',
        cat: 'Consultation',
        rate: 300
    },
    {
        name: 'Emergency Consultation',
        cat: 'Consultation',
        rate: 500
    },
    {
        name: 'CBC',
        cat: 'Pathology',
        rate: 250
    },
    {
        name: 'LFT',
        cat: 'Pathology',
        rate: 600
    },
    {
        name: 'KFT',
        cat: 'Pathology',
        rate: 600
    },
    {
        name: 'CXR PA',
        cat: 'Radiology',
        rate: 250
    },
    {
        name: 'CT Head Plain',
        cat: 'Radiology',
        rate: 3500
    },
    {
        name: 'MRI Brain',
        cat: 'Radiology',
        rate: 7500
    },
    {
        name: 'USG Abdomen',
        cat: 'Radiology',
        rate: 800
    },
    {
        name: 'General Ward (per day)',
        cat: 'Bed',
        rate: 500
    },
    {
        name: 'Private Room (per day)',
        cat: 'Bed',
        rate: 1500
    },
    {
        name: 'ICU (per day)',
        cat: 'Bed',
        rate: 3000
    },
    {
        name: 'Dressing',
        cat: 'Procedure',
        rate: 150
    },
    {
        name: 'Nebulisation',
        cat: 'Procedure',
        rate: 100
    },
    {
        name: 'IV Cannula + Infusion',
        cat: 'Procedure',
        rate: 200
    },
    {
        name: 'Sutures',
        cat: 'Procedure',
        rate: 300
    },
];

let serviceItems = [];
let selectedPayMethod = 'cash';

// ── RENDER ────────────────────────────────────────────────────────────────
function renderBillRecords() {
    const tbody = document.getElementById('billRecordsBody');
    tbody.innerHTML = billRecords.map(r => {
        const cls = r.status === 'Paid' ? 'badge-green' : r.status === 'Partial' ? 'badge-orange' : r.status ===
            'Credit' ? 'badge-purple' : 'badge-red';
        return `<tr>
      <td style="font-family:monospace;font-weight:700;font-size:12px">${r.no}</td>
      <td class="fw-700">${r.patient}</td>
      <td class="text-muted text-sm">${r.uhid}</td>
      <td><span class="badge badge-blue">${r.visit}</span></td>
      <td class="text-muted text-sm">${r.date}</td>
      <td class="fw-700">₹${r.amount.toLocaleString()}</td>
      <td style="color:#2e7d32;font-weight:700">₹${r.paid.toLocaleString()}</td>
      <td style="color:${r.balance>0?'#c62828':'#2e7d32'};font-weight:700">₹${r.balance.toLocaleString()}</td>
      <td><span class="badge ${cls}">${r.status}</span></td>
      <td>
        <div class="flex" style="gap:4px">
          <button class="btn btn-sm btn-outline" onclick="viewBill('${r.no}')"><i class="fas fa-eye"></i></button>
          <button class="btn btn-sm btn-primary" onclick="printBill('${r.no}')"><i class="fas fa-print"></i></button>
          ${r.balance>0?`<button class="btn btn-sm btn-success" onclick="switchTab('payment')"><i class="fas fa-rupee-sign"></i></button>`:''}
        </div>
      </td>
    </tr>`;
    }).join('');
}

function renderCollections() {
    const tbody = document.getElementById('collectionBody');
    tbody.innerHTML = collections.map(c => `
    <tr><td class="text-muted text-sm">${c.time}</td><td class="fw-700">${c.patient}</td><td class="fw-700" style="color:#2e7d32">${c.amount}</td><td><span class="badge badge-blue">${c.mode}</span></td><td class="text-muted text-sm">${c.by}</td></tr>
  `).join('');
}

function renderPreAuth() {
    const tbody = document.getElementById('preAuthBody');
    tbody.innerHTML = preAuthData.map(p => {
        const cls = p.status === 'Approved' ? 'badge-green' : p.status === 'Pending' ? 'badge-orange' :
            'badge-blue';
        return `<tr><td class="fw-700">${p.patient}</td><td>${p.procedure}</td><td class="fw-700">${p.amount}</td><td><span class="badge ${cls}">${p.status}</span></td><td><button class="btn btn-sm btn-outline" onclick="showToast('Pre-auth details opened','info')"><i class="fas fa-eye"></i></button></td></tr>`;
    }).join('');
}

function renderClaims() {
    const tbody = document.getElementById('claimsBody');
    tbody.innerHTML = claimsData.map(c => {
        const cls = c.status === 'Settled' ? 'badge-green' : c.status === 'Approved' ? 'badge-teal' : c
            .status === 'Submitted' ? 'badge-blue' : 'badge-orange';
        return `<tr><td style="font-family:monospace;font-size:12px">${c.claim}</td><td class="fw-700">${c.patient}</td><td class="fw-700">${c.amount}</td><td class="text-muted text-sm">${c.date}</td><td><span class="badge ${cls}">${c.status}</span></td></tr>`;
    }).join('');
}

function renderDeposits() {
    const tbody = document.getElementById('depositsBody');
    tbody.innerHTML = depositsData.map(d => `
    <tr><td class="fw-700">${d.patient}</td><td class="text-muted text-sm">${d.date}</td><td style="color:#1565c0;font-weight:700">${d.deposited}</td><td style="color:#e65100;font-weight:700">${d.used}</td><td style="color:#2e7d32;font-weight:700">${d.balance}</td><td><button class="btn btn-sm btn-outline" onclick="showToast('Deposit ledger opened','info')"><i class="fas fa-eye"></i></button></td></tr>
  `).join('');
}

function renderCreditOrgs() {
    const tbody = document.getElementById('creditOrgBody');
    tbody.innerHTML = creditOrgs.map(o => {
        const cls = o.status === 'Active' ? 'badge-green' : 'badge-orange';
        return `<tr><td class="fw-700">${o.name}</td><td>${o.limit}</td><td style="color:#e65100;font-weight:700">${o.used}</td><td style="color:#2e7d32;font-weight:700">${o.balance}</td><td><span class="badge ${cls}">${o.status}</span></td></tr>`;
    }).join('');
}

function renderRefunds() {
    const tbody = document.getElementById('refundsBody');
    tbody.innerHTML = refundsData.map(r => {
        const cls = r.status === 'Approved' ? 'badge-green' : 'badge-orange';
        return `<tr><td style="font-family:monospace;font-size:12px">${r.no}</td><td>${r.bill}</td><td class="fw-700">${r.patient}</td><td>${r.reason}</td><td class="fw-700">${r.amount}</td><td class="text-muted text-sm">${r.by}</td><td><span class="badge ${cls}">${r.status}</span></td><td><button class="btn btn-sm btn-outline" onclick="showToast('Refund processed','success')"><i class="fas fa-check"></i></button></td></tr>`;
    }).join('');
}

function renderDeptRev() {
    const tbody = document.getElementById('deptRevBody');
    tbody.innerHTML = deptRevData.map(d => `
    <tr><td class="fw-700">${d.dept}</td><td style="color:#2e7d32;font-weight:700">${d.rev}</td><td class="text-muted">${d.pct}</td></tr>
  `).join('');
}

function renderCatalogue() {
    const tbody = document.getElementById('catalogueBody');
    tbody.innerHTML = catalogue.map(c => `
    <tr><td class="fw-700">${c.name}</td><td><span class="badge badge-blue">${c.cat}</span></td><td class="fw-700">₹${c.rate}</td><td><button class="btn btn-sm btn-success" onclick="addFromCatalogue('${c.name}','${c.cat}',${c.rate})"><i class="fas fa-plus"></i> Add</button></td></tr>
  `).join('');
}

function renderRecentTrans() {
    const tbody = document.getElementById('recentTransBody');
    tbody.innerHTML = billRecords.slice(0, 6).map(r => {
        const cls = r.status === 'Paid' ? 'badge-green' : r.status === 'Partial' ? 'badge-orange' : r.status ===
            'Credit' ? 'badge-purple' : 'badge-red';
        return `<tr><td style="font-family:monospace;font-size:12px">${r.no}</td><td class="fw-700">${r.patient}</td><td><span class="badge badge-blue">${r.visit}</span></td><td class="fw-700">₹${r.amount.toLocaleString()}</td><td><span class="badge ${cls}">${r.status}</span></td><td class="text-muted text-sm">${r.date}/04</td><td><button class="btn btn-sm btn-outline" onclick="viewBill('${r.no}')"><i class="fas fa-eye"></i></button></td></tr>`;
    }).join('');
}

// SERVICE ROWS
function initServiceRows() {
    serviceItems = [{
            name: 'OPD Consultation',
            cat: 'Consultation',
            qty: 1,
            rate: 300
        },
        {
            name: 'CBC',
            cat: 'Pathology',
            qty: 1,
            rate: 250
        },
    ];
    renderServiceRows();
}

function renderServiceRows() {
    const c = document.getElementById('serviceRows');
    c.innerHTML = serviceItems.map((s, i) => `
    <div class="service-add-row">
      <input value="${s.name}" onchange="serviceItems[${i}].name=this.value;calcTotal()">
      <select onchange="serviceItems[${i}].cat=this.value"><option ${s.cat==='Consultation'?'selected':''}>Consultation</option><option ${s.cat==='Pathology'?'selected':''}>Pathology</option><option ${s.cat==='Radiology'?'selected':''}>Radiology</option><option ${s.cat==='Procedure'?'selected':''}>Procedure</option><option ${s.cat==='Pharmacy'?'selected':''}>Pharmacy</option><option ${s.cat==='Bed'?'selected':''}>Bed</option></select>
      <input type="number" value="${s.qty}" min="1" onchange="serviceItems[${i}].qty=parseInt(this.value)||1;calcTotal()">
      <input type="number" value="${s.rate}" onchange="serviceItems[${i}].rate=parseFloat(this.value)||0;calcTotal()">
      <button class="btn btn-sm btn-danger" onclick="removeService(${i})"><i class="fas fa-trash"></i></button>
    </div>
  `).join('');
    calcTotal();
}

function addServiceRow() {
    serviceItems.push({
        name: 'New Service',
        cat: 'Consultation',
        qty: 1,
        rate: 0
    });
    renderServiceRows();
}

function removeService(i) {
    serviceItems.splice(i, 1);
    renderServiceRows();
}

function addFromCatalogue(name, cat, rate) {
    serviceItems.push({
        name,
        cat,
        qty: 1,
        rate
    });
    renderServiceRows();
    closeModal('serviceModal');
    showToast(`Added: ${name}  ₹${rate}`, 'success');
}

function calcTotal() {
    let consult = 0,
        inv = 0,
        proc = 0,
        pharm = 0,
        bed = 0;
    serviceItems.forEach(s => {
        const amt = s.qty * s.rate;
        if (s.cat === 'Consultation') consult += amt;
        else if (s.cat === 'Pathology' || s.cat === 'Radiology') inv += amt;
        else if (s.cat === 'Procedure') proc += amt;
        else if (s.cat === 'Pharmacy') pharm += amt;
        else if (s.cat === 'Bed') bed += amt;
    });
    const sub = consult + inv + proc + pharm + bed;
    const dtype = document.getElementById('discountType').value;
    const dval = parseFloat(document.getElementById('discountVal').value) || 0;
    let disc = 0;
    if (dtype === 'percent') disc = sub * dval / 100;
    else if (dtype === 'flat') disc = dval;
    else if (dtype === 'scheme') disc = dval;
    const afterDisc = Math.max(0, sub - disc);
    const cgst = afterDisc * 0.05;
    const sgst = afterDisc * 0.05;
    const total = afterDisc + cgst + sgst;
    document.getElementById('sumConsult').textContent = '₹' + consult.toLocaleString();
    document.getElementById('sumInv').textContent = '₹' + inv.toLocaleString();
    document.getElementById('sumProc').textContent = '₹' + proc.toLocaleString();
    document.getElementById('sumPharm').textContent = '₹' + pharm.toLocaleString();
    document.getElementById('sumBed').textContent = '₹' + bed.toLocaleString();
    document.getElementById('sumSubtotal').textContent = '₹' + sub.toLocaleString();
    document.getElementById('sumDiscount').textContent = '- ₹' + disc.toFixed(2);
    document.getElementById('sumCGST').textContent = '₹' + cgst.toFixed(2);
    document.getElementById('sumSGST').textContent = '₹' + sgst.toFixed(2);
    document.getElementById('sumTotal').textContent = '₹' + Math.round(total).toLocaleString();
    calcBalance(Math.round(total));
}

function calcBalance(total) {
    const t = total || parseFloat(document.getElementById('sumTotal').textContent.replace(/[₹,]/g, '')) || 0;
    const paid = parseFloat(document.getElementById('amtReceived').value) || 0;
    document.getElementById('balanceDue').textContent = '₹' + Math.max(0, t - paid).toLocaleString();
}

// ── CHARTS ────────────────────────────────────────────────────────────────
function initCharts() {
    const days = ['Mar30', 'Mar31', 'Apr1', 'Apr2', 'Apr3', 'Apr4', 'Apr5', 'Apr6', 'Apr7', 'Apr8', 'Apr9', 'Apr10',
        'Apr11', 'Apr12'
    ];
    const rev = [88000, 92000, 95000, 78000, 105000, 112000, 98000, 87000, 102000, 115000, 108000, 120000, 118000,
        124000
    ];
    new Chart(document.getElementById('revTrendChart'), {
        type: 'line',
        data: {
            labels: days,
            datasets: [{
                label: 'Revenue (₹)',
                data: rev,
                borderColor: '#2e7d32',
                backgroundColor: 'rgba(46,125,50,.08)',
                fill: true,
                tension: .4,
                pointBackgroundColor: '#2e7d32'
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    grid: {
                        color: '#f0f3f8'
                    },
                    ticks: {
                        callback: v => '₹' + (v / 1000) + 'K'
                    }
                }
            },
            maintainAspectRatio: true
        }
    });
    new Chart(document.getElementById('revByServiceChart'), {
        type: 'doughnut',
        data: {
            labels: ['Consultation', 'Lab/Path', 'Radiology', 'IPD/Bed', 'Pharmacy', 'Procedures'],
            datasets: [{
                data: [22, 18, 14, 25, 12, 9],
                backgroundColor: ['#1565c0', '#6a1b9a', '#00695c', '#2e7d32', '#e65100', '#c62828'],
                borderWidth: 2
            }]
        },
        options: {
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            maintainAspectRatio: false
        }
    });
    new Chart(document.getElementById('payModeChart'), {
        type: 'bar',
        data: {
            labels: ['Cash', 'UPI', 'Card', 'NEFT', 'Cheque', 'Insurance'],
            datasets: [{
                label: 'Amount (₹K)',
                data: [38, 32, 22, 15, 8, 42],
                backgroundColor: ['#1565c0', '#2e7d32', '#e65100', '#6a1b9a', '#c62828', '#00695c']
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    grid: {
                        color: '#f0f3f8'
                    }
                }
            },
            maintainAspectRatio: false,
            indexAxis: 'y'
        }
    });
    new Chart(document.getElementById('monthlyRevChart'), {
        type: 'bar',
        data: {
            labels: ['Apr 25', 'Mar 25', 'Feb 25', 'Jan 25', 'Dec 24', 'Nov 24'],
            datasets: [{
                    label: 'OPD',
                    data: [1240000, 1180000, 1050000, 980000, 1120000, 1060000],
                    backgroundColor: '#1565c0'
                },
                {
                    label: 'IPD',
                    data: [1840000, 1720000, 1560000, 1490000, 1680000, 1540000],
                    backgroundColor: '#2e7d32'
                },
                {
                    label: 'Radiology',
                    data: [544000, 512000, 480000, 460000, 520000, 490000],
                    backgroundColor: '#6a1b9a'
                },
                {
                    label: 'Pharmacy',
                    data: [380000, 360000, 330000, 310000, 370000, 340000],
                    backgroundColor: '#e65100'
                },
            ]
        },
        options: {
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                x: {
                    stacked: true
                },
                y: {
                    stacked: true,
                    grid: {
                        color: '#f0f3f8'
                    },
                    ticks: {
                        callback: v => '₹' + (v / 100000).toFixed(1) + 'L'
                    }
                }
            },
            maintainAspectRatio: false
        }
    });
}

// ── ACTIONS ───────────────────────────────────────────────────────────────
function switchTab(tab) {
    document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('panel-' + tab).classList.add('active');
    const tabs = ['dashboard', 'invoice', 'records', 'payment', 'insurance', 'credit', 'refunds', 'revenue'];
    const idx = tabs.indexOf(tab);
    if (idx >= 0) document.querySelectorAll('.tab-btn')[idx].classList.add('active');
}

function openModal(id) {
    document.getElementById(id).classList.add('open')
}

function closeModal(id) {
    document.getElementById(id).classList.remove('open')
}

function selectPayMethod(el, m) {
    document.querySelectorAll('.pay-method').forEach(p => p.classList.remove('selected'));
    el.classList.add('selected');
    selectedPayMethod = m;
}

function searchPatient() {
    showToast('Patient search popup opened', 'info')
}

function updateBillType() {
    showToast('Bill type updated', 'info')
}

function updatePayType() {
    showToast('Payment type updated', 'info')
}

function generateBill() {
    showToast('Bill generated! BL-2025-0412-' + Math.floor(Math.random() * 900 + 100), 'success')
}

function saveDraftBill() {
    showToast('Draft saved', 'info')
}

function clearBill() {
    serviceItems = [];
    initServiceRows();
    showToast('Bill cleared', 'info')
}

function viewBill(no) {
    showToast('Viewing bill: ' + no, 'info')
}

function printBill(no) {
    showToast('Printing bill: ' + no, 'info')
}

function collectPayment() {
    showToast('Payment collected & receipt printed', 'success')
}

function submitPreAuth() {
    closeModal('preAuthModal');
    showToast('Pre-authorization request submitted', 'success')
}

function saveDeposit() {
    closeModal('depositModal');
    showToast('Deposit recorded', 'success')
}

function saveRefund() {
    closeModal('refundModal');
    showToast('Refund request submitted for approval', 'info')
}

function showToast(msg, type = 'info') {
    const c = document.getElementById('toastContainer');
    const t = document.createElement('div');
    t.className = `toast ${type}`;
    const icons = {
        success: 'fa-check-circle',
        info: 'fa-info-circle',
        warning: 'fa-exclamation-triangle'
    };
    t.innerHTML = `<i class="fas ${icons[type]||'fa-info-circle'}"></i> ${msg}`;
    c.appendChild(t);
    setTimeout(() => t.remove(), 3500);
}

// ── INIT ──────────────────────────────────────────────────────────────────
window.addEventListener('DOMContentLoaded', () => {
    renderBillRecords();
    renderCollections();
    renderPreAuth();
    renderClaims();
    renderDeposits();
    renderCreditOrgs();
    renderRefunds();
    renderDeptRev();
    renderCatalogue();
    renderRecentTrans();
    initServiceRows();
    initCharts();
});
</script>
@endpush