
<!-- Sidebar -->

<div class="sidebar" data-background-color="dark">
  <div class="sidebar-logo">
    <!-- Logo Header -->
    <div class="logo-header" data-background-color="dark">
      <a href="{{ url('admin/dashboard') }}" class="logo">
        <img
          src="{{ asset('public/images/logo.jpg') }}"
          alt="navbar brand"
          class="navbar-brand"
          height="20"
        />
      </a>
      <div class="nav-toggle">
        <button class="btn btn-toggle toggle-sidebar">
          <i class="gg-menu-right"></i>
        </button>
        <button class="btn btn-toggle sidenav-toggler">
          <i class="gg-menu-left"></i>
        </button>
      </div>
      <button class="topbar-toggler more">
        <i class="gg-more-vertical-alt"></i>
      </button>
    </div>
    <!-- End Logo Header -->
  </div>
  <div class="sidebar-wrapper scrollbar scrollbar-inner">
    <div class="sidebar-content">
      <ul class="nav nav-secondary">        
        <li class="nav-item">
          <a href="{{ url('dashboard') }}">
            <i class="fas fa-home"></i>
            <p>Dashboard</p>
          </a>
        </li>
        <li class="nav-item">
          <a data-bs-toggle="collapse" href="#base">
            <i class="fas fa-layer-group"></i>
            <p>Empanelment Masters</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="base">
            <ul class="nav nav-collapse">
              <li>
                <a href="{{ route('admin.SchemeType.index') }}">
                  <span class="sub-item">Schemes Type</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.hospital-states.index') }}">
                  <span class="sub-item">States</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.hospitalDistrict.index') }}">
                  <span class="sub-item">District</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.blocks.index') }}">
                  <span class="sub-item">Blocks</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.villages.index') }}">
                  <span class="sub-item">Villages</span>
                </a>
              </li>

              <li>
                <a href="{{ route('admin.facility-details.index') }}">
                  <span class="sub-item">Facility Details</span>
                </a>
              </li>

              <li>
                <a href="{{ route('admin.facility-types.index') }}">
                  <span class="sub-item">Facility Types</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.facility-speciality-types.index') }}">
                  <span class="sub-item">Facility Speciality Types</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.facility-ownership-types.index') }}">
                  <span class="sub-item">Facility Ownership Types</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.facility_ownership_sub_types.index') }}">
                  <span class="sub-item">Facility Ownership Sub-Types</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.facility-certificates.index') }}">
                  <span class="sub-item">Facility Certificates</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.goverment-benefits.index') }}">
                  <span class="sub-item">Goverment Benefits</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.system_medicines.index') }}">
                  <span class="sub-item">System Medicines</span>
                </a>
              </li>

              <li>
                <a href="{{ route('admin.service.index') }}">
                  <span class="sub-item">Services</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.sub-service.index') }}">
                  <span class="sub-item">Sub Services</span>
                </a>
              </li>

              <li>
                <a href="{{ route('admin.licenses.index') }}">
                  <span class="sub-item">Licenses</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.licenses_type.index') }}">
                  <span class="sub-item">Licenses Type</span>
                </a>
              </li>

              <li>
                <a href="{{ route('admin.human_resources.index') }}">
                  <span class="sub-item">Human Resources</span>
                </a>
              </li>

              <li>
                <a href="{{ route('admin.accreditations.index') }}">
                  <span class="sub-item">Accreditations</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.tds_exemptions.index') }}">
                  <span class="sub-item">TDS_exemptions</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.empanelment-documents.index') }}">
                  <span class="sub-item">Empanelment-Documents</span>
                </a>
              </li>
    
              <li>
                <a href="{{ route('admin.bank-accounts.index') }}">
                  <span class="sub-item">Bank Accounts</span>
                </a>
              </li>
            </ul>
          </div>
        </li>
        <li class="nav-item">
          <a data-bs-toggle="collapse" href="#audit-panel-masters">
            <i class="fas fa-layer-group"></i>
            <p>Quality Audit Masters</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="audit-panel-masters">
            <ul class="nav nav-collapse">   
              <li>
                <a href="{{route('admin.audit-category.index')}}">
                  <span class="sub-item">Audit Categories</span>
                </a>
              </li>
              <li>
                <a href="{{route('admin.audit-sub-category.index')}}">
                  <span class="sub-item">Audit Sub Categories</span>
                </a>
              </li>
              <li>
                <a href="{{route('admin.audit-list.index')}}">
                  <span class="sub-item">Audit List</span>
                </a>
              </li>
            </ul>
          </div>
        </li>
        <li class="nav-item">
          <a data-bs-toggle="collapse" href="#ump-panel-masters">
            <i class="fas fa-layer-group"></i>
            <p>UMP Panel Masters</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="ump-panel-masters">
            <ul class="nav nav-collapse">                         
              <li>
                <a href="{{ route('admin.entityTypes.index') }}">
                  <span class="sub-item">Entity Types</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.entities.index') }}">
                  <span class="sub-item">Entities</span>
                </a>
              </li> 
              <li>
                <a href="{{ route('admin.roles.index') }}">
                  <span class="sub-item">Roles</span>
                </a>
              </li>  
            </ul>
          </div>
        </li>
        <li class="nav-item">
          <a data-bs-toggle="collapse" href="#procedure-masters">
            <i class="fas fa-layer-group"></i>
            <p>Procedure Masters</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="procedure-masters">
            <ul class="nav nav-collapse">
              <li>
                <a href="{{ route('admin.speciality.index') }}">
                  <span class="sub-item">Speciality</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.package.index') }}">
                  <span class="sub-item">Packages</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.document.index') }}">
                  <span class="sub-item">Investigations</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.procedure-category.index') }}">
                  <span class="sub-item">Procedure Category</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.procedure.index') }}">
                  <span class="sub-item">Procedures</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.implant.index') }}">
                  <span class="sub-item">Implants</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.stratification-category.index') }}">
                  <span class="sub-item">Stratification Categories</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.stratification.index') }}">
                  <span class="sub-item">Stratifications</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.followup.index') }}">
                  <span class="sub-item">Follow Up Procedures</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.addon.index') }}">
                  <span class="sub-item">Addon Procedures</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.addon-speciality.index') }}">
                  <span class="sub-item">Addon to Speciality</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.nonaddon.index') }}">
                  <span class="sub-item">NonAddon Related Procedures</span>
                </a>
              </li>
            </ul>
          </div>
        </li>
        
        <li class="nav-item">
          <a data-bs-toggle="collapse" href="#reasons-masters">
            <i class="fas fa-layer-group"></i>
            <p>Preauth Masters</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="reasons-masters">
            <ul class="nav nav-collapse">
              <li>
                <a href="{{ route('admin.beneficiaries.index') }}">
                  <span class="sub-item">Benificiaries</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.diabetes.index') }}">
                  <span class="sub-item">Diabetes</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.hypertension.index') }}">
                  <span class="sub-item">Hypertension</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.heart_diseases.index') }}">
                  <span class="sub-item">Heart Diseases</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.strokes.index') }}">
                  <span class="sub-item">Strokes</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.cancers.index') }}">
                  <span class="sub-item">Cancer</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.tuberculosis.index') }}">
                  <span class="sub-item">Tuberculosis</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.asthmas.index') }}">
                  <span class="sub-item">Asthma</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.appetites.index') }}">
                  <span class="sub-item">Appetites</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.bowels.index') }}">
                  <span class="sub-item">Bowels</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.nutrition.index') }}">
                  <span class="sub-item">Nutrition</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.diets.index') }}">
                  <span class="sub-item">Diets</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.admission-types.index') }}">
                  <span class="sub-item">Admission Types</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.diagnoses.index') }}">
                  <span class="sub-item">Diagnoses</span>
                </a>
              </li>
            </ul>
          </div>
        </li>
        <li class="nav-item">
          <a data-bs-toggle="collapse" href="#reasons-masters">
            <i class="fas fa-layer-group"></i>
            <p>Reasons Masters</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="reasons-masters">
            <ul class="nav nav-collapse">
              <li>
                <a href="{{ route('admin.registration_cancel_reasons.index') }}">
                  <span class="sub-item">Registration Cancel Reasons</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.preauth_cancel_reasons.index') }}">
                  <span class="sub-item">Preauth Cancel Reasons</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.preauth-reject-reasons.index') }}">
                  <span class="sub-item">Preauth Reject Reasons</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.preauth_claim_reasons.index') }}">
                  <span class="sub-item">Preauth Claim Reasons</span>
                </a>
              </li>
            </ul>
          </div>
        </li>
        <li class="nav-item">
          <a data-bs-toggle="collapse" href="#users">
            <i class="icon-user-follow"></i>
            <p>Users</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="users">
            <ul class="nav nav-collapse">
             {{-- <li>
                <a href="{{ route('admin.register-requests') }}">
                  <span class="sub-item">Register Requests</span>
                </a>
              </li> --}}
              <li>
                <a href="{{ route('admin.hospitals') }}">
                  <span class="sub-item">Hospitals</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.excel.import') }}">
                  <span class="sub-item">Import</span>
                </a>
              </li>
              <li>
                <a href="{{ route('admin.users.indexUser') }}">
                  <span class="sub-item">Users</span>
                </a>
              </li>
            </ul>
          </div>
        </li>
      </ul>
    </div>
  </div>
</div>
<!-- End Sidebar -->