@extends('layouts.hospital.app', ['is_header_hiden' => true])

@php
		$canDischargeNow = ((float) $outstandingAmount) <= 0;
@endphp

@section('title', 'IPD Profile | Paracare+')
@section('page_header_icon', '')
@section('page_subtitle', '')

@section('page_header_actions')
<a href="{{ route('hospital.ipd-patient.index') }}" class="btn btn-info">Back To IPD</a>
<a href="{{ route('hospital.settings.beds.bed-dashboard') }}" class="btn btn-info">Bed Dashboard</a>
@if($allocation->discharge_date)
		<a href="{{ route('hospital.ipd-patient.discharge-summary.print', ['allocation' => $allocation->id, 'autoprint' => 1]) }}" target="_blank" class="btn btn-info">Print Discharge Summary</a>
@endif
@if(!$allocation->discharge_date)
		@can('edit-ipd-patient')
				<button type="button" class="btn btn-warning transfer-ipd-btn" data-id="{{ $allocation->id }}" data-url="{{ route('hospital.ipd-patient.transfer.showform', ['allocation' => $allocation->id]) }}">Transfer Bed</button>
				<button
						type="button"
						class="btn {{ $canDischargeNow ? 'btn-success discharge-ipd-btn' : 'btn-secondary' }}"
						data-id="{{ $allocation->id }}"
						data-url="{{ route('hospital.ipd-patient.discharge.showform', ['allocation' => $allocation->id]) }}"
						{{ $canDischargeNow ? '' : 'disabled' }}
						title="{{ $canDischargeNow ? 'Discharge Patient' : 'Clear outstanding bill before discharge' }}"
				>
						{{ $canDischargeNow ? 'Discharge' : 'Clear Bill To Discharge' }}
				</button>
		@endcan
@endif
@endsection

@section('content')
		<div style="background:linear-gradient(135deg,#071221,#0a1628);padding:16px 24px;border-bottom:1px solid rgba(255,255,255,0.08)">
			<div class="flex items-center gap-4 flex-wrap">
				<div style="width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#1565c0,#00695c);color:white;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:800;flex-shrink:0;border:2px solid rgba(255,255,255,.2)">HD</div>
				<div class="flex-1">
					<div style="font-size:18px;font-weight:800;color:#e8f2fb;letter-spacing:-.02em">Hari Das Gupta</div>
					<div style="font-size:11.5px;color:#6a8fa8;margin-top:2px">MRN: UK-IPD-4812 &nbsp;·&nbsp; ABHA: UK-22041-D &nbsp;·&nbsp; 62 Years, Male &nbsp;·&nbsp; Blood Group: B+ &nbsp;·&nbsp; Admitted: 24 Mar 2024</div>
				</div>
				<div class="flex gap-2 flex-wrap">
					<span class="badge badge-danger" style="font-size:11px">IPD — Ward 4 Bed 12</span>
					<span class="badge badge-primary">T2DM + HTN</span>
					<span class="badge badge-warning">Drug Alert: Metformin</span>
					<button class="btn btn-ghost btn-sm" style="color:#d0e8fb;border-color:rgba(255,255,255,.15)" onclick="window.print()">🖨 Print</button>
					<button class="btn btn-primary btn-sm">+ New Order</button>
				</div>
			</div>

			<div class="flex gap-4 mt-3 flex-wrap" style="border-top:1px solid rgba(255,255,255,.07);padding-top:12px">
				<div style="text-align:center"><div style="font-size:10px;color:#4a6880;text-transform:uppercase;letter-spacing:.05em">BP</div><div style="font-size:15px;font-weight:700;color:#ef5350">148/94</div></div>
				<div style="width:1px;background:rgba(255,255,255,.07)"></div>
				<div style="text-align:center"><div style="font-size:10px;color:#4a6880;text-transform:uppercase">Pulse</div><div style="font-size:15px;font-weight:700;color:#e8f2fb">88</div></div>
				<div style="width:1px;background:rgba(255,255,255,.07)"></div>
				<div style="text-align:center"><div style="font-size:10px;color:#4a6880;text-transform:uppercase">SpO2</div><div style="font-size:15px;font-weight:700;color:#66bb6a">97%</div></div>
				<div style="width:1px;background:rgba(255,255,255,.07)"></div>
				<div style="text-align:center"><div style="font-size:10px;color:#4a6880;text-transform:uppercase">Temp</div><div style="font-size:15px;font-weight:700;color:#fb8c00">99.2°F</div></div>
				<div style="width:1px;background:rgba(255,255,255,.07)"></div>
				<div style="text-align:center"><div style="font-size:10px;color:#4a6880;text-transform:uppercase">RBS</div><div style="font-size:15px;font-weight:700;color:#ef5350">212</div></div>
				<div style="width:1px;background:rgba(255,255,255,.07)"></div>
				<div style="text-align:center"><div style="font-size:10px;color:#4a6880;text-transform:uppercase">Weight</div><div style="font-size:15px;font-weight:700;color:#e8f2fb">74 kg</div></div>
				<div style="width:1px;background:rgba(255,255,255,.07)"></div>
				<div style="text-align:center"><div style="font-size:10px;color:#4a6880;text-transform:uppercase">BMI</div><div style="font-size:15px;font-weight:700;color:#fb8c00">27.8</div></div>
			</div>
		</div>

		<div style="background:#fff;border-bottom:2px solid #e2ecf4;padding:0 24px">
			<div class="tab-bar" style="border:none;margin:0">
				<button class="tab-btn active" data-tab="tabTimeline" onclick="switchEMRTab('tabTimeline',this)">📅 Timeline</button>
				<button class="tab-btn" data-tab="tabOrders" onclick="switchEMRTab('tabOrders',this)">📋 Orders</button>
				<button class="tab-btn" data-tab="tabMeds" onclick="switchEMRTab('tabMeds',this)">💊 Medications</button>
				<button class="tab-btn" data-tab="tabNotes" onclick="switchEMRTab('tabNotes',this)">📝 Clinical Notes</button>
				<button class="tab-btn" data-tab="tabLab" onclick="switchEMRTab('tabLab',this)">🧪 Lab Results</button>
				<button class="tab-btn" data-tab="tabVitals" onclick="switchEMRTab('tabVitals',this)">📈 Vitals Chart</button>
				<button class="tab-btn" data-tab="tabHistory" onclick="switchEMRTab('tabHistory',this)">📁 History</button>
				<button class="tab-btn" data-tab="tabBilling" onclick="switchEMRTab('tabBilling',this)">💳 Billing</button>
			</div>
		</div>

		<div class="content-area">
			<div class="tab-pane active" id="tabTimeline">
				<div class="grid-21">
					<div class="card">
						<div class="card-header"><div class="card-title">📅 Clinical Timeline</div></div>
						<div class="card-body">
							<div class="timeline">
								<div class="tl-item"><div class="tl-dot orange"></div><div class="tl-time">Today, 10:15 AM</div><div class="tl-title">Vitals Review — Dr. Sharma</div><div class="tl-detail">BP 148/94 — increased Amlodipine to 10mg. RBS 212 — Metformin dose review pending.</div></div>
								<div class="tl-item"><div class="tl-dot blue"></div><div class="tl-time">Today, 8:00 AM</div><div class="tl-title">Lab Orders Sent</div><div class="tl-detail">CBC, KFT, LFT, HbA1c, Fasting glucose — STAT priority. Blood culture × 2.</div></div>
								<div class="tl-item"><div class="tl-dot green"></div><div class="tl-time">Yesterday, 6:00 PM</div><div class="tl-title">IPD Admission</div><div class="tl-detail">Admitted via Emergency — Uncontrolled T2DM, hypertensive crisis. Ward 4 Bed 12 assigned.</div></div>
								<div class="tl-item"><div class="tl-dot red"></div><div class="tl-time">Yesterday, 4:30 PM</div><div class="tl-title">Emergency OPD Visit</div><div class="tl-detail">c/o severe headache, BP 196/110, RBS 340. IV Labetalol given. IV access established.</div></div>
								<div class="tl-item"><div class="tl-dot gray"></div><div class="tl-time">15 Mar 2024</div><div class="tl-title">OPD Visit — Follow-up</div><div class="tl-detail">Routine DM review. HbA1c 8.4%. Diet counselling done. Metformin 1g BD continued.</div></div>
								<div class="tl-item"><div class="tl-dot gray"></div><div class="tl-time">1 Mar 2024</div><div class="tl-title">Lab Results — HbA1c</div><div class="tl-detail">HbA1c: 8.4% (elevated). Creatinine: 1.1 (normal). Lipid profile: LDL 142 (borderline).</div></div>
							</div>
						</div>
					</div>
					<div class="flex flex-col gap-4">
						<div class="card">
							<div class="card-header"><div class="card-title">⚕️ Active Problem List</div></div>
							<div class="card-body-sm">
								<div class="list-item"><div class="list-item-icon" style="background:#ffebee">🔴</div><div class="list-item-body"><div class="li-title">Type 2 Diabetes Mellitus</div><div class="li-sub">ICD-10: E11.9 · Uncontrolled · Since 2018</div></div><span class="badge badge-danger">Active</span></div>
								<div class="list-item"><div class="list-item-icon" style="background:#fff3e0">🟠</div><div class="list-item-body"><div class="li-title">Essential Hypertension</div><div class="li-sub">ICD-10: I10 · Poorly controlled · Since 2020</div></div><span class="badge badge-warning">Active</span></div>
								<div class="list-item"><div class="list-item-icon" style="background:#e3f2fd">🔵</div><div class="list-item-body"><div class="li-title">Dyslipidaemia</div><div class="li-sub">ICD-10: E78.5 · On statin · LDL 142</div></div><span class="badge badge-primary">Active</span></div>
							</div>
						</div>
						<div class="ai-insight-block">
							<div class="ai-header">🤖 AI Clinical Copilot</div>
							<div class="ai-body">
								<div class="ai-item"><div class="ai-dot" style="background:#e65100"></div><div>HbA1c trend worsening (7.9→8.4%). Consider dual therapy — add Sitagliptin or refer for endocrinology review.</div></div>
								<div class="ai-item"><div class="ai-dot"></div><div>BP target for DM+CKD is &lt;130/80 per ACC/AHA. Current 148/94 is above target — ARB preferred.</div></div>
								<div class="ai-item"><div class="ai-dot" style="background:#2e7d32"></div><div>Kidney function stable (Cr 1.1). Continue Metformin with monitoring. eGFR estimation recommended.</div></div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="tab-pane" id="tabOrders">
				<div class="card">
					<div class="card-header"><div class="card-title">📋 Active Orders</div><button class="btn btn-primary btn-sm">+ New Order</button></div>
					<div class="table-wrap">
						<table>
							<thead><tr><th>Order Type</th><th>Description</th><th>Ordered By</th><th>Date/Time</th><th>Status</th><th>Result</th></tr></thead>
							<tbody>
								<tr><td><span class="badge badge-primary">Lab</span></td><td>CBC, KFT, LFT, HbA1c</td><td>Dr. Sharma</td><td>Today 8:00 AM</td><td><span class="badge badge-warning">Pending</span></td><td>—</td></tr>
								<tr><td><span class="badge badge-purple">Radiology</span></td><td>Chest X-Ray PA view</td><td>Dr. Sharma</td><td>Today 8:05 AM</td><td><span class="badge badge-success">Completed</span></td><td><button class="btn btn-ghost btn-xs">View Report</button></td></tr>
								<tr><td><span class="badge badge-accent">Pharmacy</span></td><td>Inj. Labetalol 20mg IV (STAT)</td><td>Dr. Sharma</td><td>Yesterday 4:35 PM</td><td><span class="badge badge-success">Dispensed</span></td><td>—</td></tr>
								<tr><td><span class="badge badge-success">Diet</span></td><td>Diabetic diet — 1800 kcal, low sodium</td><td>Dr. Sharma</td><td>Yesterday 6:00 PM</td><td><span class="badge badge-success">Active</span></td><td>—</td></tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<div class="tab-pane" id="tabMeds">
				<div class="card">
					<div class="card-header"><div class="card-title">💊 Current Medication Sheet</div><button class="btn btn-primary btn-sm">+ Prescribe</button></div>
					<div class="table-wrap">
						<table>
							<thead><tr><th>Drug</th><th>Dose</th><th>Route</th><th>Frequency</th><th>Duration</th><th>Prescribed By</th><th>Status</th></tr></thead>
							<tbody>
								<tr><td class="font-600">Metformin</td><td>500mg</td><td>Oral</td><td>Twice daily (after food)</td><td>Ongoing</td><td>Dr. Sharma</td><td><span class="badge badge-success">Active</span></td></tr>
								<tr><td class="font-600">Amlodipine</td><td>10mg</td><td>Oral</td><td>Once daily (morning)</td><td>Ongoing</td><td>Dr. Sharma</td><td><span class="badge badge-success">Active</span></td></tr>
								<tr><td class="font-600">Atorvastatin</td><td>40mg</td><td>Oral</td><td>Once daily (night)</td><td>Ongoing</td><td>Dr. Sharma</td><td><span class="badge badge-success">Active</span></td></tr>
								<tr><td class="font-600">Inj. Normal Saline</td><td>500mL</td><td>IV</td><td>Q12h</td><td>48 hours</td><td>Dr. Sharma</td><td><span class="badge badge-warning">Running</span></td></tr>
								<tr><td class="font-600">Aspirin</td><td>75mg</td><td>Oral</td><td>Once daily</td><td>Ongoing</td><td>Dr. Sharma</td><td><span class="badge badge-success">Active</span></td></tr>
								<tr style="background:#fff3e0"><td class="font-600" style="color:#e65100">Inj. Labetalol</td><td>20mg</td><td>IV</td><td>STAT (given)</td><td>Single dose</td><td>Dr. Sharma</td><td><span class="badge badge-gray">Completed</span></td></tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<div class="tab-pane" id="tabLab">
				<div class="grid-2">
					<div class="card">
						<div class="card-header"><div class="card-title">🧪 Lab Results</div><span class="badge badge-warning">2 Abnormal</span></div>
						<div class="table-wrap">
							<table>
								<thead><tr><th>Test</th><th>Result</th><th>Ref. Range</th><th>Status</th><th>Date</th></tr></thead>
								<tbody>
									<tr><td>Haemoglobin</td><td class="font-600">11.8 g/dL</td><td>13–17</td><td><span class="badge badge-warning">Low</span></td><td>Today</td></tr>
									<tr><td>Fasting Glucose</td><td class="font-600" style="color:#c62828">212 mg/dL</td><td>70–100</td><td><span class="badge badge-danger">High</span></td><td>Today</td></tr>
									<tr><td>HbA1c</td><td class="font-600">8.4%</td><td>&lt;5.7%</td><td><span class="badge badge-danger">High</span></td><td>15 Mar</td></tr>
									<tr><td>Serum Creatinine</td><td class="font-600" style="color:#2e7d32">1.1 mg/dL</td><td>0.7–1.2</td><td><span class="badge badge-success">Normal</span></td><td>Today</td></tr>
									<tr><td>Total Cholesterol</td><td class="font-600">198 mg/dL</td><td>&lt;200</td><td><span class="badge badge-success">Normal</span></td><td>15 Mar</td></tr>
									<tr><td>LDL Cholesterol</td><td class="font-600" style="color:#e65100">142 mg/dL</td><td>&lt;100</td><td><span class="badge badge-warning">High</span></td><td>15 Mar</td></tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="card">
						<div class="card-header"><div class="card-title">📋 Radiology Reports</div></div>
						<div class="card-body-sm">
							<div class="list-item"><div class="list-item-icon" style="background:#e3f2fd">🩻</div><div class="list-item-body"><div class="li-title">Chest X-Ray PA — Today</div><div class="li-sub">Mild cardiomegaly. No consolidation. No pleural effusion.</div></div><button class="btn btn-ghost btn-xs">View</button></div>
							<div class="list-item" style="opacity:.6"><div class="list-item-icon" style="background:#e8f5e9">🔬</div><div class="list-item-body"><div class="li-title">2D Echo — 10 Mar 2024</div><div class="li-sub">EF 55%. Concentric LVH. Grade I diastolic dysfunction.</div></div><button class="btn btn-ghost btn-xs">View</button></div>
						</div>
					</div>
				</div>
			</div>

			<div class="tab-pane" id="tabVitals">
				<div class="grid-2">
					<div class="card"><div class="card-header"><div class="card-title">📈 BP Trend</div></div><div class="card-body"><div class="chart-container" style="height:220px"><canvas id="bpChart"></canvas></div></div></div>
					<div class="card"><div class="card-header"><div class="card-title">📈 Blood Sugar Trend</div></div><div class="card-body"><div class="chart-container" style="height:220px"><canvas id="bsChart"></canvas></div></div></div>
				</div>
			</div>

			<div class="tab-pane" id="tabHistory">
				<div class="grid-2">
					<div class="card">
						<div class="card-header"><div class="card-title">📁 Past Encounters</div></div>
						<div class="table-wrap">
							<table>
								<thead><tr><th>Date</th><th>Type</th><th>Diagnosis</th><th>Doctor</th></tr></thead>
								<tbody>
									<tr><td>24 Mar 2024</td><td><span class="badge badge-primary">IPD</span></td><td>Hypertensive Crisis + T2DM</td><td>Dr. Sharma</td></tr>
									<tr><td>15 Mar 2024</td><td><span class="badge badge-success">OPD</span></td><td>DM Follow-up</td><td>Dr. Sharma</td></tr>
									<tr><td>1 Feb 2024</td><td><span class="badge badge-success">OPD</span></td><td>Routine check</td><td>Dr. Rawat</td></tr>
									<tr><td>5 Nov 2023</td><td><span class="badge badge-warning">Emergency</span></td><td>RBS 390 — DKA borderline</td><td>Dr. Pande</td></tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="card">
						<div class="card-header"><div class="card-title">💉 Allergy &amp; Immunisation</div></div>
						<div class="card-body-sm">
							<div class="alert alert-danger mb-3"><span class="alert-icon">⚠️</span><div><div class="alert-title">Drug Allergy</div>Sulfonamides — Rash (confirmed)</div></div>
							<div class="section-title">Vaccinations</div>
							<div class="list-item"><div class="list-item-body"><div class="li-title">COVID-19 — Covishield</div><div class="li-sub">2 doses + Booster · 2022</div></div><span class="badge badge-success">Complete</span></div>
							<div class="list-item"><div class="list-item-body"><div class="li-title">Influenza</div><div class="li-sub">Annual · Last: Oct 2023</div></div><span class="badge badge-success">Current</span></div>
							<div class="list-item"><div class="list-item-body"><div class="li-title">Hepatitis B</div><div class="li-sub">3 dose series</div></div><span class="badge badge-success">Complete</span></div>
						</div>
					</div>
				</div>
			</div>

			<div class="tab-pane" id="tabBilling">
				<div class="card">
					<div class="card-header"><div class="card-title">💳 Billing Summary</div><a href="billing.html" class="btn btn-primary btn-sm">View Full Bill →</a></div>
					<div class="card-body-sm">
						<div class="stat-row mb-3">
							<div class="stat-item"><div class="s-label">Total Charges</div><div class="s-value" style="color:#1565c0">₹28,400</div></div>
							<div class="stat-item"><div class="s-label">Paid (PMJAY)</div><div class="s-value" style="color:#2e7d32">₹15,000</div></div>
							<div class="stat-item"><div class="s-label">Balance Due</div><div class="s-value" style="color:#c62828">₹13,400</div></div>
						</div>
						<div class="table-wrap">
							<table>
								<thead><tr><th>Service</th><th>Amount</th><th>Status</th></tr></thead>
								<tbody>
									<tr><td>IPD Room Charges (Ward 4 × 1 day)</td><td>₹1,500</td><td><span class="badge badge-success">Paid</span></td></tr>
									<tr><td>Physician Consultation × 2</td><td>₹400</td><td><span class="badge badge-success">Paid</span></td></tr>
									<tr><td>Emergency OPD Charges</td><td>₹500</td><td><span class="badge badge-success">Paid</span></td></tr>
									<tr><td>Lab Tests (CBC, KFT, LFT, HbA1c)</td><td>₹2,800</td><td><span class="badge badge-warning">Pending</span></td></tr>
									<tr><td>X-Ray Chest</td><td>₹400</td><td><span class="badge badge-warning">Pending</span></td></tr>
									<tr><td>IV Medications &amp; Consumables</td><td>₹3,400</td><td><span class="badge badge-warning">Pending</span></td></tr>
									<tr><td>AB-PMJAY Coverage</td><td style="color:#2e7d32">-₹15,000</td><td><span class="badge badge-primary">Applied</span></td></tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>

			<div class="tab-pane" id="tabNotes">
				<div class="card">
					<div class="card-header"><div class="card-title">📝 Clinical Notes</div><button class="btn btn-primary btn-sm">+ Add Note</button></div>
					<div class="card-body">
						<div style="border:1.5px solid #e2ecf4;border-radius:10px;padding:16px;margin-bottom:16px">
							<div class="flex justify-between mb-2">
								<div><strong>Progress Note</strong> — Dr. Rajesh Sharma</div>
								<div class="text-muted text-sm">Today, 10:15 AM</div>
							</div>
							<p style="font-size:13px;line-height:1.6;color:#344a5e"><strong>S:</strong> Patient c/o headache improved. BP remains elevated. No chest pain. Diet: poor compliance.<br><strong>O:</strong> BP 148/94, Pulse 88, Temp 99.2°F, RBS 212. JVP normal. No ankle oedema.<br><strong>A:</strong> Hypertensive crisis — improving. Uncontrolled T2DM — medication review needed.<br><strong>P:</strong> Increase Amlodipine to 10mg. Review Metformin dose. HbA1c result awaited. Continue IV fluids. Repeat BP in 4h.</p>
						</div>
						<div style="border:1.5px solid #e2ecf4;border-radius:10px;padding:16px;opacity:.7">
							<div class="flex justify-between mb-2">
								<div><strong>Admission Note</strong> — Dr. Rajesh Sharma</div>
								<div class="text-muted text-sm">Yesterday, 6:00 PM</div>
							</div>
							<p style="font-size:13px;line-height:1.6;color:#344a5e">62M known T2DM, HTN, Dyslipidaemia. Presented with severe headache, BP 196/110, RBS 340. IV Labetalol given. IV access established. Admitted for monitoring and stabilisation.</p>
						</div>
					</div>
				</div>
			</div>
		</div>
@endsection
