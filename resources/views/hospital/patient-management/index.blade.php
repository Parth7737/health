@extends('layouts.hospital.app')

@section('title', 'Patient Registration & ADT')
@section('page_subtitle', 'OPD / IPD Registration · Bed Allocation · Transfers · Discharge')

@section('page_header_actions')
<button class="btn btn-success btn-sm" onclick="openModal('newPatientModal')">➕ New Registration</button>
<button class="btn btn-primary btn-sm" onclick="openModal('opdTokenModal')">🩺 OPD Token</button>
<button class="btn btn-purple btn-sm" onclick="openModal('ipdAdmitModal')">🛏️ IPD Admit</button>
@endsection

@push('styles')
@include('layouts.partials.flatpickr-css')

<style>
  #newPatientModal .modal {
    display: flex;
    flex-direction: column;
    max-height: calc(100vh - 48px);
  }

  #newPatientModal #patientRegistrationForm {
    display: flex;
    flex: 1;
    flex-direction: column;
    min-height: 0;
  }

  #newPatientModal .modal-body {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    padding-bottom: 20px;
  }

  #newPatientModal .modal-footer {
    flex-shrink: 0;
    background: rgba(255, 255, 255, 0.96);
  }

  #newPatientModal #reg-bed-summary {
    margin-bottom: 12px;
  }

  #opdTokenModal .modal {
    display: flex;
    flex-direction: column;
    max-height: calc(100vh - 48px);
  }

  #opdTokenModal #opdTokenForm {
    display: flex;
    flex: 1;
    flex-direction: column;
    min-height: 0;
  }

  #opdTokenModal .modal-body {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    padding-bottom: 16px;
  }

  #opdTokenModal .modal-footer {
    flex-shrink: 0;
    background: rgba(255, 255, 255, 0.96);
  }

  #ipdAdmitModal .modal {
    display: flex;
    flex-direction: column;
    max-height: calc(100vh - 48px);
  }

  #ipdAdmitModal #ipdAdmitForm {
    display: flex;
    flex: 1;
    flex-direction: column;
    min-height: 0;
  }

  #ipdAdmitModal .modal-body {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    padding-bottom: 16px;
  }

  #ipdAdmitModal .modal-footer {
    flex-shrink: 0;
    background: rgba(255, 255, 255, 0.96);
  }

  #admit_search_results {
    margin-top: 8px;
    max-height: 220px;
    overflow-y: auto;
  }

  #admit_search_results:empty {
    display: none;
  }

  .bed-preview-groups {
    display: grid;
    gap: 10px;
    max-height: 260px;
    overflow-y: auto;
  }

  .bed-preview-group {
    background: #fff;
    border: 1px solid var(--border-light);
    border-radius: 10px;
    padding: 10px;
  }

  .bed-preview-group-head {
    display: flex;
    justify-content: space-between;
    gap: 8px;
    align-items: center;
    margin-bottom: 8px;
    font-size: 12px;
    font-weight: 700;
    color: var(--text);
  }

  .bed-preview-count {
    color: var(--success-dark);
    background: var(--success-light);
    border-radius: 999px;
    padding: 2px 8px;
    font-size: 11px;
    font-weight: 700;
  }

  .bed-preview-list {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
  }

  .bed-preview-chip {
    border: 1px solid rgba(46, 125, 50, 0.18);
    background: var(--success-light);
    color: var(--success-dark);
    border-radius: 999px;
    padding: 5px 9px;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
  }

  .bed-preview-chip.active {
    border-color: rgba(21, 101, 192, 0.32);
    background: var(--primary-light);
    color: var(--primary);
  }

  .bed-preview-meta {
    display: block;
    font-size: 10px;
    font-weight: 600;
    opacity: 0.8;
  }

  #tok_search_results {
    margin-top: 8px;
    max-height: 240px;
    overflow-y: auto;
    border: 1px solid var(--border-light);
    border-radius: 10px;
    background: #fff;
  }

  #tok_search_results:empty {
    display: none;
  }

  .tok-search-item {
    width: 100%;
    border: 0;
    background: transparent;
    text-align: left;
    padding: 10px 12px;
    border-bottom: 1px solid var(--border-light);
    cursor: pointer;
  }

  .tok-search-item:last-child {
    border-bottom: 0;
  }

  .tok-search-item:hover,
  .tok-search-item:focus {
    background: var(--surface-2);
    outline: none;
  }

  .tok-search-name {
    font-weight: 700;
    font-size: 13px;
    color: var(--text);
    line-height: 1.2;
  }

  .tok-search-meta {
    margin-top: 3px;
    font-size: 11px;
    color: var(--text-muted);
    line-height: 1.2;
  }

  .tok-search-empty {
    padding: 10px 12px;
    font-size: 12px;
    color: var(--text-muted);
  }

  @media (max-width: 991.98px) {
    #newPatientModal .modal {
      max-height: calc(100vh - 20px);
    }

    #opdTokenModal .modal {
      max-height: calc(100vh - 20px);
    }

    #ipdAdmitModal .modal {
      max-height: calc(100vh - 20px);
    }

    .bed-preview-groups {
      max-height: none;
    }
  }
</style>

@endpush
@section('content')
<div class="stats-grid">
  <div class="stat-card stat-blue"><div class="stat-icon">👥</div><div class="stat-info"><div class="stat-value" id="kpiOpdToday">0</div><div class="stat-label">OPD Today</div></div></div>
  <div class="stat-card stat-teal"><div class="stat-icon">🛏️</div><div class="stat-info"><div class="stat-value" id="kpiIpdActive">0</div><div class="stat-label">IPD Admitted</div></div></div>
  <div class="stat-card stat-red"><div class="stat-icon">🚨</div><div class="stat-info"><div class="stat-value" id="kpiEmergency">0</div><div class="stat-label">Emergency</div></div></div>
  <div class="stat-card stat-green"><div class="stat-icon">✅</div><div class="stat-info"><div class="stat-value" id="kpiDischargedToday">0</div><div class="stat-label">Discharged Today</div></div></div>
  <div class="stat-card stat-orange"><div class="stat-icon">🔄</div><div class="stat-info"><div class="stat-value" id="kpiTransferred">0</div><div class="stat-label">Transferred</div></div></div>
  <div class="stat-card stat-purple"><div class="stat-icon">📋</div><div class="stat-info"><div class="stat-value" id="kpiTotalActive">0</div><div class="stat-label">Total Active</div></div></div>
</div>

<div class="card mb-20">
  <div class="card-body" style="padding:14px">
    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
      <div style="flex:1;min-width:200px">
        <div class="input-group">
          <span class="input-addon">🔍</span>
          <input type="text" class="form-control" id="ptSearch" placeholder="Search by Name, MRN, Phone, Aadhaar..."/>
        </div>
      </div>
      <select class="form-control" style="width:140px" id="ptFilter">
        <option value="">All Patients</option>
        <option value="opd">OPD</option>
        <option value="ipd">IPD</option>
        <option value="emergency">Emergency</option>
        <option value="discharged">Discharged</option>
      </select>
      <select class="form-control" style="width:160px" id="deptFilter">
        <option value="">All Departments</option>
        <option>General Medicine</option>
        <option>Surgery</option>
        <option>Orthopaedics</option>
        <option>Gynaecology</option>
        <option>Paediatrics</option>
        <option>ENT</option>
        <option>Cardiology</option>
        <option>Emergency</option>
      </select>
      <button class="btn btn-secondary btn-sm" type="button" id="ptResetBtn">🔄 Reset</button>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body p-0">
    <div class="tabs-bar" style="padding:0 18px;margin-bottom:0" id="ptTabBar">
      <button class="tab-btn active" onclick="switchPtTab('ptListPane',this)">📋 All Patients <span class="tab-count" id="tc-all">0</span></button>
      <button class="tab-btn" onclick="switchPtTab('opdPane',this)">🩺 OPD Queue <span class="tab-count" id="tc-opd">0</span></button>
      <button class="tab-btn" onclick="switchPtTab('bookingPane',this)">📆 Booking Appointment <span class="tab-count" id="tc-book">0</span></button>
      <button class="tab-btn" onclick="switchPtTab('ipdPane',this)">🛏️ IPD Admissions <span class="tab-count" id="tc-ipd">0</span></button>
      <button class="tab-btn" onclick="switchPtTab('emergencyPane',this)">🚨 Emergency <span class="tab-count" id="tc-em">0</span></button>
      <button class="tab-btn" onclick="switchPtTab('dischargePane',this)">✅ Discharged Today <span class="tab-count" id="tc-dis">0</span></button>
    </div>

    <div id="ptListPane" style="padding:0">
      <div class="table-wrap">
        <table class="hims-table">
          <thead>
            <tr>
              <th>MRN</th><th>Patient Name</th><th>Age/Sex</th><th>Contact</th>
              <th>Blood Grp</th><th>Visit Type</th><th>Department</th><th>Status</th><th>Registered</th><th>Actions</th>
            </tr>
          </thead>
          <tbody id="patientTableBody"></tbody>
        </table>
      </div>
      <div class="table-pagination">
        <span id="ptPagInfo">Showing 0-0 of 0 patients</span>
        <div class="pagination-btns" id="ptPagBtns"></div>
      </div>
    </div>

    <div id="opdPane" style="padding:16px;display:none">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
        <div class="fw-700 fs-14">🩺 Today's OPD Queue - Real-time</div>
        <button class="btn btn-primary btn-sm" onclick="openModal('opdTokenModal')">🎫 Issue Token</button>
      </div>
      <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-bottom:12px">
        <div style="flex:1;min-width:220px">
          <div class="input-group">
            <span class="input-addon">🔍</span>
            <input type="text" class="form-control" id="opdSearch" placeholder="Search by Token, MRN, Name, Phone..."/>
          </div>
        </div>
        <select class="form-control" style="width:180px" id="opdDeptFilter">
          <option value="">All Departments</option>
        </select>
      </div>
      <div id="opdQueueCards"></div>
      <div class="table-pagination">
        <span id="opdPagInfo">Showing 0-0 of 0 records</span>
        <div class="pagination-btns" id="opdPagBtns"></div>
      </div>
    </div>

    <div id="bookingPane" style="padding:16px;display:none">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
        <div class="fw-700 fs-14">📆 Future Booking Appointments</div>
      </div>
      <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-bottom:12px">
        <div style="flex:1;min-width:220px">
          <div class="input-group">
            <span class="input-addon">🔍</span>
            <input type="text" class="form-control" id="bookingSearch" placeholder="Search by Booking No, MRN, Name, Phone..."/>
          </div>
        </div>
        <select class="form-control" style="width:180px" id="bookingDeptFilter">
          <option value="">All Departments</option>
        </select>
      </div>
      <div id="bookingQueueCards"></div>
      <div class="table-pagination">
        <span id="bookingPagInfo">Showing 0-0 of 0 records</span>
        <div class="pagination-btns" id="bookingPagBtns"></div>
      </div>
    </div>

    <div id="ipdPane" style="padding:16px;display:none">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
        <div class="fw-700 fs-14">🛏️ Current IPD Admissions</div>
        <button class="btn btn-purple btn-sm" onclick="openModal('ipdAdmitModal')">🛏️ New Admission</button>
      </div>
      <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-bottom:12px">
        <div style="flex:1;min-width:220px">
          <div class="input-group">
            <span class="input-addon">🔍</span>
            <input type="text" class="form-control" id="ipdSearch" placeholder="Search by Admission No, MRN, Name..."/>
          </div>
        </div>
        <select class="form-control" style="width:180px" id="ipdDeptFilter">
          <option value="">All Departments</option>
        </select>
      </div>
      <div id="ipdAdmissionsList"></div>
      <div class="table-pagination">
        <span id="ipdPagInfo">Showing 0-0 of 0 records</span>
        <div class="pagination-btns" id="ipdPagBtns"></div>
      </div>
    </div>

    <div id="emergencyPane" style="padding:16px;display:none">
      <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-bottom:12px">
        <div style="flex:1;min-width:220px">
          <div class="input-group">
            <span class="input-addon">🔍</span>
            <input type="text" class="form-control" id="emSearch" placeholder="Search emergency patients..."/>
          </div>
        </div>
        <select class="form-control" style="width:180px" id="emDeptFilter">
          <option value="">All Departments</option>
        </select>
      </div>
      <div id="emergencyList"></div>
      <div class="table-pagination">
        <span id="emPagInfo">Showing 0-0 of 0 records</span>
        <div class="pagination-btns" id="emPagBtns"></div>
      </div>
    </div>

    <div id="dischargePane" style="padding:16px;display:none">
      <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-bottom:12px">
        <div style="flex:1;min-width:220px">
          <div class="input-group">
            <span class="input-addon">🔍</span>
            <input type="text" class="form-control" id="disSearch" placeholder="Search discharged patients..."/>
          </div>
        </div>
        <select class="form-control" style="width:180px" id="disDeptFilter">
          <option value="">All Departments</option>
        </select>
      </div>
      <div id="dischargedList"></div>
      <div class="table-pagination">
        <span id="disPagInfo">Showing 0-0 of 0 records</span>
        <div class="pagination-btns" id="disPagBtns"></div>
      </div>
    </div>
  </div>
</div>

<div class="modal-overlay hidden" id="newPatientModal" onclick="if(event.target===this)closeModal('newPatientModal')">
  <div class="modal modal-xl">
    <div class="modal-header">
      <div class="modal-title">👤 New Patient Registration</div>
      <button class="modal-close" onclick="closeModal('newPatientModal')">✕</button>
    </div>
    <form id="patientRegistrationForm" novalidate>
    <div class="modal-body">
      <div class="steps-bar" id="regSteps">
        <div class="step-item active" id="step1"><div class="step-circle">1</div><div class="step-info"><div class="step-name">Personal Info</div></div></div>
        <div class="step-line"></div>
        <div class="step-item" id="step2"><div class="step-circle">2</div><div class="step-info"><div class="step-name">Contact & Address</div></div></div>
        <div class="step-line"></div>
        <div class="step-item" id="step3"><div class="step-circle">3</div><div class="step-info"><div class="step-name">Medical History</div></div></div>
        <div class="step-line"></div>
        <div class="step-item" id="step4"><div class="step-circle">4</div><div class="step-info"><div class="step-name">Visit Type</div></div></div>
        <div class="step-line"></div>
        <div class="step-item" id="step5"><div class="step-circle">5</div><div class="step-info"><div class="step-name">Confirm & Print</div></div></div>
      </div>

      <div id="regPane1">
        <div class="form-row cols-3">
          <div class="form-group">
            <label class="form-label">Title</label>
            <select class="form-control" id="reg_title"><option>Mr.</option><option>Mrs.</option><option>Ms.</option><option>Dr.</option><option>Baby</option></select>
          </div>
          <div class="form-group" style="grid-column:span 2">
            <label class="form-label">Full Name <span class="req">*</span></label>
            <input type="text" class="form-control" id="reg_name" placeholder="Patient's full name"/>
          </div>
        </div>
        <div class="form-row cols-4">
          <div class="form-group">
            <label class="form-label">Date of Birth <span class="req">*</span></label>
            <input type="text" class="form-control" id="reg_dob"/>
          </div>
          <div class="form-group">
            <label class="form-label">Age (Auto)</label>
            <input type="text" class="form-control" id="reg_age" placeholder="Auto-calculated" readonly/>
          </div>
          <div class="form-group">
            <label class="form-label">Gender <span class="req">*</span></label>
            <select class="form-control" id="reg_gender"><option value="">Select</option><option>Male</option><option>Female</option><option>Other</option></select>
          </div>
          <div class="form-group">
            <label class="form-label">Blood Group</label>
            <select class="form-control" id="reg_blood"><option value="">Unknown</option><option>A+</option><option>A−</option><option>B+</option><option>B−</option><option>AB+</option><option>AB−</option><option>O+</option><option>O−</option></select>
          </div>
        </div>
        <div class="form-row cols-3">
          <div class="form-group">
            <label class="form-label">Aadhaar Number</label>
            <input type="text" class="form-control" id="reg_aadhaar" placeholder="XXXX XXXX XXXX" maxlength="14"/>
          </div>
          <div class="form-group">
            <label class="form-label">Ayushman Bharat ID</label>
            <input type="text" class="form-control" id="reg_ab" placeholder="AB-PMJAY ID"/>
          </div>
          <div class="form-group">
            <label class="form-label">Marital Status</label>
            <select class="form-control" id="reg_marital_status"><option>Single</option><option>Married</option><option>Widowed</option><option>Divorced</option></select>
          </div>
        </div>
        <div class="form-row cols-3">
          @php $religions = App\Models\Religion::get(); @endphp
          <div class="form-group">
            <label class="form-label">Religion</label>
            <select class="form-control" id="reg_religion">
              <option value="">Select Religion</option>
              @foreach($religions as $religion)
              <option value="{{ $religion->id }}">{{ $religion->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Occupation</label>
            <input type="text" class="form-control" id="reg_occupation" placeholder="Farmer / Govt. Employee etc."/>
          </div>
          @php $categories = App\Models\PatientCategory::get(); @endphp
          <div class="form-group">
            <label class="form-label">Category</label>
            <select class="form-control select2" id="reg_category">
              <option value="">Select Category</option>
              @foreach($categories as $category)
              <option value="{{ $category->id }}">{{ $category->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>

      <div id="regPane2" style="display:none">
        <div class="form-row cols-3">
          <div class="form-group">
            <label class="form-label">Mobile Number <span class="req">*</span></label>
            <input type="tel" class="form-control" id="reg_phone" placeholder="10-digit mobile"/>
          </div>
          <div class="form-group">
            <label class="form-label">Alternate Phone</label>
            <input type="tel" class="form-control" id="reg_alt_phone" placeholder="Alternate contact"/>
          </div>
          <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" id="reg_email" placeholder="email@example.com"/>
          </div>
        </div>
        <div class="form-row cols-2-1">
          <div class="form-group">
            <label class="form-label">Address <span class="req">*</span></label>
            <textarea class="form-control" id="reg_address" rows="2" placeholder="House No, Street, Village/Colony..."></textarea>
          </div>
          <div>
            <div class="form-group">
              <label class="form-label">Pin Code</label>
              <input type="text" class="form-control" id="reg_pin" placeholder="248001"/>
            </div>
          </div>
        </div>
        <div class="form-row cols-3">
          
          <div class="form-group">
            <label class="form-label">State</label>
            <select class="form-control select2" id="reg_state" data-district-url="{{ route('hospital.patient-management.load-districts') }}">
              <option value="">Select State</option>
              @foreach(($states ?? []) as $state)
              <option value="{{ $state->id }}">{{ $state->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">District</label>
            <select class="form-control select2" id="reg_district"><option value="">Select District</option></select>
          </div>
          <div class="form-group">
            <label class="form-label">Nationality</label>
            <select class="form-control select2" id="reg_nationality">
              <option value="">Select Nationality</option>
              <option value="Indian">Indian</option>
              <option value="Other">Other</option>
            </select>
          </div>
        </div>
        <div style="border-top:1px solid var(--border-light);padding-top:14px;margin-top:4px">
          <div class="fw-700 fs-13 mb-12">👨‍👩‍👦 Emergency Contact</div>
          <div class="form-row cols-3">
            <div class="form-group">
              <label class="form-label">Name</label>
              <input type="text" class="form-control" id="reg_emergency_name" placeholder="Guardian/Relative name"/>
            </div>
            <div class="form-group">
              <label class="form-label">Relation</label>
              <select class="form-control" id="reg_emergency_relation">
                <option value="">Select</option>
                <option value="Father">Father</option>
                <option value="Mother">Mother</option>
                <option value="Spouse">Spouse</option>
                <option value="Son">Son</option>
                <option value="Daughter">Daughter</option>
                <option value="Other">Other</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Phone</label>
              <input type="tel" class="form-control" id="reg_emergency_phone" placeholder="Emergency contact"/>
            </div>
          </div>
        </div>
      </div>

      <div id="regPane3" style="display:none">
        <div class="form-row cols-2">
          <div>
            <div class="form-group">
              <label class="form-label">Known Allergies</label>
              <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:8px" id="allergyChips">
              </div>
              <div class="input-group">
                <input type="text" class="form-control" id="allergyInput" placeholder="Type allergy and press Enter"/>
                <button class="btn btn-secondary" id="allergyAddBtn" type="button">+Add</button>
              </div>
            </div>
            <div class="form-group">
              @php $diseases = App\Models\Disease::get(); @endphp
              <label class="form-label">Chronic Conditions</label>
              <div id="reg_chronic_conditions" style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:6px">
                @foreach($diseases as $disease)
                <label style="display:flex;align-items:center;gap:5px;cursor:pointer"><input type="checkbox" name="diseases[]" value="{{ $disease->name }}"> {{ $disease->name }}</label>
                @endforeach
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Past Surgical History</label>
              <textarea class="form-control" id="reg_past_surgery" rows="2" placeholder="Previous surgeries / procedures..."></textarea>
            </div>
          </div>
          <div>
            <div class="form-group">
              <label class="form-label">Current Medications</label>
              <textarea class="form-control" id="reg_current_medications" rows="3" placeholder="List ongoing medications..."></textarea>
            </div>
            <div class="form-row cols-2">
              <div class="form-group">
                <label class="form-label">Smoking</label>
                <select class="form-control" id="reg_smoking"><option>Never</option><option>Current</option><option>Past</option></select>
              </div>
              <div class="form-group">
                <label class="form-label">Alcohol</label>
                <select class="form-control" id="reg_alcohol"><option>Never</option><option>Occasional</option><option>Regular</option></select>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Family History</label>
              <textarea class="form-control" id="reg_family_history" rows="2" placeholder="Family history of relevant diseases..."></textarea>
            </div>
            <div class="form-group">
              <label class="form-label">Vaccination History</label>
              <select class="form-control" id="reg_vaccination"><option>Up to date</option><option>Partial</option><option>Unknown</option><option>None</option></select>
            </div>
          </div>
        </div>
      </div>

      <div id="regPane4" style="display:none">
        <div class="form-row cols-2">
          <div>
            <div class="form-group">
              <label class="form-label">Visit Type <span class="req">*</span></label>
              <div id="reg_visit_type" style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                <label style="display:flex;align-items:center;gap:8px;padding:12px;border:2px solid var(--border);border-radius:8px;cursor:pointer" id="vtOPD">
                  <input type="radio" name="visitType" value="OPD" checked/> <span>🩺 OPD</span>
                </label>
                <label style="display:flex;align-items:center;gap:8px;padding:12px;border:2px solid var(--border);border-radius:8px;cursor:pointer" id="vtIPD">
                  <input type="radio" name="visitType" value="IPD"/> <span>🛏️ IPD Admission</span>
                </label>
                <label style="display:flex;align-items:center;gap:8px;padding:12px;border:2px solid var(--border);border-radius:8px;cursor:pointer" id="vtEM">
                  <input type="radio" name="visitType" value="Emergency"/> <span>🚨 Emergency</span>
                </label>
                <label style="display:flex;align-items:center;gap:8px;padding:12px;border:2px solid var(--border);border-radius:8px;cursor:pointer" id="vtDaycare">
                  <input type="radio" name="visitType" value="Daycare"/> <span>☀️ Daycare</span>
                </label>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Department <span class="req">*</span></label>
              <select class="form-control select2" id="reg_dept">
                <option value="">Select Department</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Consulting Doctor</label>
              <select class="form-control select2" id="reg_doctor"><option value="">Select Doctor</option></select>
            </div>
            <div class="form-row cols-2">
              <div class="form-group">
                <label class="form-label">Appointment Date</label>
                <input type="text" class="form-control" id="reg_appointment_date"/>
              </div>
              <div class="form-group">
                <label class="form-label">Appointment Slot</label>
                <select class="form-control select2" id="reg_slot"><option value="">Select Slot</option></select>
              </div>
            </div>
          </div>
          <div>
            <div class="form-group">
              <label class="form-label">Chief Complaint</label>
              <textarea class="form-control" rows="3" id="reg_complaint" placeholder="Patient's main complaint..."></textarea>
            </div>
            <div class="form-group">
              <label class="form-label">Payment Mode</label>
              <select class="form-control" id="reg_payment">
                <option>Cash</option>
                <option>AB-PMJAY (Ayushman Bharat)</option>
                <option>CGHS</option><option>ECHS</option>
                <option>State Health Scheme</option>
                <option>ESI</option><option>Private Insurance</option>
              </select>
            </div>
            <div class="form-group" id="regFeeGroup">
              <label class="form-label">Registration Fee (₹)</label>
              <div class="input-group">
                <span class="input-addon">₹</span>
                <input type="number" class="form-control" value="0" id="reg_fee"/>
              </div>
            </div>
            <div id="regIpdFields" style="display:none">
              <div class="form-group">
                <label class="form-label">Advance Deposit (₹)</label>
                <div class="input-group">
                  <span class="input-addon">₹</span>
                  <input type="number" class="form-control" value="0" id="reg_advance_deposit" placeholder="5000"/>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Available Bed</label>
                <select class="form-control select2" id="reg_bed"><option value="">Select Bed</option></select>
              </div>
              <div class="ipd-bed-hint" id="reg-bed-summary">
                Select a bed to see its location, type and standard base charge.
              </div>
              <div class="form-group" style="margin-top:12px">
                <label class="form-label">Admission Reason</label>
                <textarea class="form-control" rows="2" id="reg_admission_reason" placeholder="Admission reason / provisional diagnosis..."></textarea>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div id="regPane5" style="display:none">
        <div class="alert alert-green"><span class="alert-icon">✅</span><div><b>Ready to Register!</b> Please verify patient details before printing the registration slip.</div></div>
        <div style="background:var(--surface-2);border:1px solid var(--border-light);border-radius:12px;padding:16px" id="regSummary">
          <div class="fw-700 fs-14 mb-12">📋 Registration Summary</div>
          <div class="form-row cols-2">
            <div id="summLeft"></div>
            <div id="summRight"></div>
          </div>
        </div>
        <div class="form-row cols-3 mt-16">
          <div style="text-align:center;padding:14px;background:var(--primary-light);border-radius:10px;border:1px solid rgba(21,101,192,.2)">
            <div style="font-size:24px">🎫</div>
            <div class="fw-700 fs-12 text-primary" id="genToken">—</div>
            <div class="fs-11 text-muted">Token Number</div>
          </div>
          <div style="text-align:center;padding:14px;background:var(--success-light);border-radius:10px;border:1px solid rgba(46,125,50,.2)">
            <div style="font-size:24px">🆔</div>
            <div class="fw-700 fs-12 text-success" id="genMRN">—</div>
            <div class="fs-11 text-muted">MRN Number</div>
          </div>
          <div style="text-align:center;padding:14px;background:var(--warning-light);border-radius:10px;border:1px solid rgba(245,124,0,.2)">
            <div style="font-size:24px">💳</div>
            <div class="fw-700 fs-12" style="color:var(--warning-dark)" id="genFee">₹100</div>
            <div class="fs-11 text-muted">Registration Fee</div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" id="regPrevBtn" type="button" style="display:none">‹ Back</button>
      <div style="flex:1"></div>
      <button class="btn btn-secondary" type="button" onclick="closeModal('newPatientModal')">Cancel</button>
        <button class="btn btn-primary" id="regNextBtn" type="button">Next ›</button>
        <button class="btn btn-success" id="regSubmitBtn" type="submit" style="display:none">✅ Register & Print Slip</button>
    </div>
    </form>
  </div>
</div>

<div class="modal-overlay hidden" id="opdTokenModal" onclick="if(event.target===this)closeModal('opdTokenModal')">
  <div class="modal modal-md">
    <div class="modal-header">
      <div class="modal-title">🎫 Issue OPD Token</div>
      <button class="modal-close" onclick="closeModal('opdTokenModal')">✕</button>
    </div>
    <form id="opdTokenForm" novalidate>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Search Existing Patient</label>
        <div class="input-group"><span class="input-addon">🔍</span><input type="text" class="form-control" id="tok_patient_search" placeholder="MRN / Name / Phone"/></div>
        <input type="hidden" id="tok_patient_id"/>
        <div id="tok_search_results"></div>
      </div>
      <div class="form-row cols-2">
        <div class="form-group"><label class="form-label">Patient Name <span class="req">*</span></label><input class="form-control" placeholder="Full name" id="tok_name" readonly/></div>
        <div class="form-group">
          <label class="form-label">Age / Sex</label>
          <div style="display:flex;gap:6px">
            <input class="form-control" placeholder="Age" style="width:70px" id="tok_age" readonly/>
            <select class="form-control" id="tok_gender" data-no-select2="1">
              <option value="">Select</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
              <option value="Other">Other</option>
            </select>
          </div>
        </div>
      </div>
      <div class="form-row cols-2">
        <div class="form-group"><label class="form-label">Department <span class="req">*</span></label>
          <select class="form-control" id="tok_dept">
            <option value="">Select Dept</option>
          </select>
        </div>
        <div class="form-group"><label class="form-label">Doctor</label>
          <select class="form-control" id="tok_doctor"><option value="">Select Doctor</option></select>
        </div>
      </div>
      <div class="form-row cols-2">
        <div class="form-group"><label class="form-label">Appointment Date</label><input type="text" class="form-control" id="tok_appointment_date"/></div>
        <div class="form-group"><label class="form-label">Slot</label><select class="form-control" id="tok_slot"><option value="">Select Slot</option></select></div>
      </div>
      <div class="form-group"><label class="form-label">Chief Complaint</label><textarea class="form-control" rows="2" id="tok_complaint" placeholder="Main reason for visit..."></textarea></div>
      <div class="form-group"><label class="form-label">Visit Type</label>
        <select class="form-control" id="tok_visit_type">
          <option value="OPD">OPD</option>
          <option value="Emergency">Emergency</option>
          <option value="Daycare">Daycare</option>
        </select>
      </div>
      <div class="form-group"><label class="form-label">Payment</label>
        <select class="form-control" id="tok_payment"><option value="Cash">Cash</option><option value="AB-PMJAY">AB-PMJAY</option><option value="CGHS">CGHS</option><option value="ECHS">ECHS</option><option value="Private Insurance">Private Insurance</option></select>
      </div>
      <div class="form-group">
        <label class="form-label">Applied Charge (₹)</label>
        <div class="input-group"><span class="input-addon">₹</span><input type="number" class="form-control" id="tok_charge" readonly/></div>
      </div>
      <div style="background:linear-gradient(135deg,var(--primary),var(--blue-mid));border-radius:12px;padding:16px;text-align:center;color:white;margin-top:8px" id="tokenPreview">
        <!-- <div style="font-size:11px;opacity:.8;text-transform:uppercase;letter-spacing:.06em">OPD Token</div> -->
        <div style="font-size:36px;font-weight:900;letter-spacing:2px;display:none" id="tokenDisplayNo">---</div>
        <div style="font-size:12px;opacity:.85" id="tokenDisplayDept">Select Department</div>
        <div style="font-size:11px;opacity:.7;margin-top:4px" id="tokenDisplayTime">—</div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" type="button" onclick="closeModal('opdTokenModal')">Cancel</button>
      <button class="btn btn-success" type="submit" id="tokSubmitBtn">🎫 Issue Token & Print</button>
    </div>
    </form>
  </div>
</div>

<div class="modal-overlay hidden" id="ipdAdmitModal" onclick="if(event.target===this)closeModal('ipdAdmitModal')">
  <div class="modal modal-lg">
    <div class="modal-header">
      <div class="modal-title">🛏️ IPD Admission</div>
      <button class="modal-close" onclick="closeModal('ipdAdmitModal')">✕</button>
    </div>
    <form id="ipdAdmitForm" novalidate>
    <div class="modal-body">
      <div class="form-row cols-2">
        <div>
          <div class="form-group"><label class="form-label">Patient (Search MRN)</label><div class="input-group"><span class="input-addon">🔍</span><input class="form-control" id="admit_patient_search" placeholder="MRN-XXXXX or Name"/></div><input type="hidden" id="admit_patient_id"/><div id="admit_search_results" class="mt-8"></div></div>
          <div class="patient-chip" id="admitPatientChip" style="display:none"></div>
          <div class="form-group"><label class="form-label">Department</label><select class="form-control" id="admit_dept"><option value="">Select Department</option></select></div>
          <div class="form-group"><label class="form-label">Admission Reason / Diagnosis</label><textarea class="form-control" id="admit_reason" rows="2" placeholder="Primary diagnosis or admission reason..."></textarea></div>
          <div class="form-row cols-2">
            <div class="form-group"><label class="form-label">Ward</label>
              <select class="form-control" id="admit_ward">
                <option value="">All Wards</option>
              </select>
            </div>
            <div class="form-group"><label class="form-label">Bed</label>
              <select class="form-control" id="admit_bed"><option value="">Select Bed</option></select>
            </div>
          </div>
          <div class="form-group"><label class="form-label">Admitting Doctor</label>
            <select class="form-control" id="admit_doctor"><option value="">Select Doctor</option></select>
          </div>
          <div class="form-group"><label class="form-label">Expected Duration</label>
            <select class="form-control"><option>1-3 days</option><option>3-7 days</option><option>7-14 days</option><option>14+ days</option></select>
          </div>
        </div>
        <div>
          <div class="form-group"><label class="form-label">Payment / Insurance</label>
            <select class="form-control" id="admit_payment"><option>Cash</option><option>AB-PMJAY</option><option>CGHS</option><option>Private Insurance</option></select>
          </div>
          <div class="form-group"><label class="form-label">Advance Deposit (₹)</label>
            <div class="input-group"><span class="input-addon">₹</span><input type="number" class="form-control" id="admit_advance" placeholder="5000"/></div>
          </div>
          <div class="form-group"><label class="form-label">Special Instructions</label>
            <textarea class="form-control" rows="3" placeholder="Any special nursing or dietary instructions..."></textarea>
          </div>
          <div style="background:var(--surface-2);border:1px solid var(--border-light);border-radius:10px;padding:12px">
            <div class="fw-600 fs-12 mb-8">🛏️ Bed Availability Preview</div>
            <div style="display:flex;gap:6px;flex-wrap:wrap" id="bedPreview">
              <span style="font-size:11px;color:var(--text-muted)">Select a ward to see available beds</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" type="button" onclick="closeModal('ipdAdmitModal')">Cancel</button>
      <button class="btn btn-purple" type="submit" id="admitSubmitBtn">🛏️ Admit Patient & Allocate Bed</button>
    </div>
    </form>
  </div>
</div>

<div class="modal-overlay hidden" id="patient360Modal" onclick="if(event.target===this)closeModal('patient360Modal')">
  <div class="modal modal-xl">
    <div class="modal-header">
      <div class="modal-title">👁️ Patient 360° View - Complete Medical Record</div>
      <button class="modal-close" onclick="closeModal('patient360Modal')">✕</button>
    </div>
    <div class="modal-body">
      <div id="p360Content"></div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
@php
  $pmOpdBindPlaceholder = 999999999;
  $pmOpdQueueSkipUrl = str_replace((string) $pmOpdBindPlaceholder, '__ID__', route('hospital.patient-management.opd-queue-skip', ['opdPatient' => $pmOpdBindPlaceholder]));
  $pmOpdQueueUndoSkipUrl = str_replace((string) $pmOpdBindPlaceholder, '__ID__', route('hospital.patient-management.opd-queue-undo-skip', ['opdPatient' => $pmOpdBindPlaceholder]));
@endphp
<script>
window.PM_ROUTES = {
  stats: @json(route('hospital.patient-management.stats')),
  patients: @json(route('hospital.patient-management.patients')),
  opdQueue: @json(route('hospital.patient-management.opd-queue')),
  bookingAppointments: @json(route('hospital.patient-management.booking-appointments')),
  ipdAdmissions: @json(route('hospital.patient-management.ipd-admissions')),
  searchPatients: @json(route('hospital.patient-management.search-patients')),
  patient360: @json(route('hospital.patient-management.patient-360')),
  patientDetails: @json(route('hospital.patient-management.patient-details')),
  loadDoctors: @json(route('hospital.patient-management.load-doctors')),
  loadDoctorSlots: @json(route('hospital.patient-management.load-doctor-slots')),
  availableBeds: @json(route('hospital.patient-management.available-beds')),
  loadDistricts: @json(route('hospital.patient-management.load-districts')),
  mrnPreview: @json(route('hospital.patient-management.mrn-preview')),
  getOpdCharge: @json(route('hospital.patient-management.get-opd-charge')),
  register: @json(route('hospital.patient-management.register')),
  issueToken: @json(route('hospital.patient-management.issue-token')),
  issueNextToken: @json(route('hospital.patient-management.issue-next-token')),
  cancelBookingAppointment: @json(route('hospital.patient-management.cancel-booking-appointment')),
  ipdAdmit: @json(route('hospital.patient-management.ipd-admit')),
  opdQueueSkip: @json($pmOpdQueueSkipUrl),
  opdQueueUndoSkip: @json($pmOpdQueueUndoSkipUrl),
};

window.PM_BOOT = {
  departments: @json($departments ?? []),
  states: @json($states ?? []),
};
</script>
<script src="{{ asset('public/modules/sa/patient-management.js') }}?v={{ time() }}"></script>
<script src="{{ asset('public/modules/sa/patient-registration-form.js') }}"></script>
<script src="{{ asset('public/modules/sa/patient-visit-modals.js') }}"></script>
@include('layouts.partials.flatpickr-js')
@endpush