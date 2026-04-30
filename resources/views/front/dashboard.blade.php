@extends('layouts.front.app')
@section('title','Dashboard | Paracare+')
@section('content')
<div class="container-fluid">
  <div class="page-title">
    <div class="row">
      <div class="col-sm-6">
        <h3>Sakhuja Hospital</h3>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">
              <svg class="stroke-icon">
                <use href="../assets/svg/icon-sprite.svg#stroke-home"></use>
              </svg></a></li>
          <li class="breadcrumb-item">Dashboard</li>
          <!-- <li class="breadcrumb-item active">Crypto</li> -->
        </ol>
      </div>
    </div>
  </div>
</div>
<!-- Container-fluid starts-->
<div class="container-fluid dashboard-4">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body main-title-box">
          <div class="row align-items-center mb-3">
            <!-- Filter Dropdown (Left) -->
            <div class="col-md-6 d-flex align-items-center">
              <label for="searchType" class="me-2 mb-0 fw-bold">Search Type</label>
              <select class="form-select w-auto" id="searchType" name="searchType">
                <option>All</option>
                <option>Today</option>
                <option>This Week</option>
                <option>Last Week</option>
                <option selected>This Month</option>
                <option>Last Month</option>
                <option>Last 3 Months</option>
                <option>Last 6 Months</option>
                <option>Last 12 Months</option>
                <option>This Year</option>
                <option>Last Year</option>
                <option>Period</option>
              </select>
              <button class="btn btn-primary  ms-2" type="submit">Search</button>
            </div>
            <!-- Department Dropdown (Right) -->
            <div class="col-md-6 d-flex justify-content-end align-items-center">
              <label for="department" class="me-2 mb-0 fw-bold">Department</label>
              <select class="form-select w-auto" id="department" name="department">
                <option selected>Department</option>
                <option>OT</option>
                <option>Cardiology</option>
                <option>Emergency Department (ED)</option>
                <option>Psychiatry</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xxl-4 col-md-6 box-col-6">
          <div class="card small-widget">
            <div class="card-body primary"> <span class="f-light">New Orders</span>
              <div class="d-flex align-items-end gap-1">
                <h4 class="counter" data-target="2435">2,435</h4><span class="font-primary f-12 f-w-500"><i class="icon-arrow-up"></i><span>+50%</span></span>
              </div>
              <div class="bg-gradient">
                <svg class="stroke-icon svg-fill">
                  <use href="../assets/svg/icon-sprite.svg#new-order"></use>
                </svg>
              </div>
            </div>
          </div>
          <div class="card small-widget">
            <div class="card-body warning"><span class="f-light">New Customers</span>
              <div class="d-flex align-items-end gap-1">
                <h4 class="counter" data-target="2908">2,908</h4><span class="font-warning f-12 f-w-500"><i class="icon-arrow-up"></i><span>+20%</span></span>
              </div>
              <div class="bg-gradient">
                <svg class="stroke-icon svg-fill">
                  <use href="../assets/svg/icon-sprite.svg#customers"></use>
                </svg>
              </div>
            </div>
          </div>
            <div class="card small-widget">
                <div class="card-body secondary">
                    <span class="f-light">Average Sale</span>
                    <div class="d-flex align-items-end gap-1">
                        <h4>$<span class="counter" data-target="389">389</span>k</h4><span class="font-secondary f-12 f-w-500"><i class="icon-arrow-down"></i><span>-10%</span></span>
                    </div>
                    <div class="bg-gradient">
                        <svg class="stroke-icon svg-fill">
                        <use href="../assets/svg/icon-sprite.svg#sale"></use>
                        </svg>
                    </div>
                </div>
            </div>
    </div>
    <div class="col-xxl-4 col-sm-6 box-col-6 ord-xl-i box-ord-1 mb-4">
      <div class="card h-100 mb-0">
        <div class="card-header card-no-border">
          <div class="header-top">
            <h5 class="m-0">Income</h5>
          </div>
        </div>
        <div class="card-body pt-0">
          <div class="order-wrapper">
            <div id="income-goal"></div>
          </div>
        </div>
      </div>
    </div>


    <div class="col-xxl-4 col-md-6 col-sm-6 box-col-6 mb-4">
      <div class="card h-100 mb-0">
        <div class="card-header card-no-border">
          <div class="header-top">
            <h5 class="m-0">Patient</h5>
          </div>
        </div>
        <div class="card-body pt-0">
          <div class="order-wrapper">
            <div id="patient-goal"></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xxl-4 col-sm-6 box-col-6 ord-xl-i box-ord-1 mb-4">
      <div class="card h-100 mb-0">
        <div class="card-header card-no-border">
          <div class="header-top">
            <h5 class="m-0">Patient by Gender</h5>
          </div>
        </div>
        <div class="card-body pt-0">
          <div class="order-wrapper">
            <div id="gender-chart"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xxl-4 col-sm-6 box-col-6 ord-xl-i box-ord-1 mb-4">
      <div class="card h-100 mb-0">
        <div class="card-header card-no-border">
          <div class="header-top">
            <h5 class="m-0">IPD Bed Status</h5>
          </div>
        </div>
        <div class="card-body pt-0">
          <div class="order-wrapper">
            <div id="ipd-bed-chart"></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xxl-4 col-sm-6 box-col-6 ord-xl-i box-ord-1 mb-4">
      <div class="card h-100 mb-0">
        <div class="card-header card-no-border">
          <div class="header-top">
            <h5 class="m-0">OT Bed Status</h5>
          </div>
        </div>
        <div class="card-body pt-0">
          <div class="order-wrapper">
            <div id="OT-bed-chart"></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-6 col-md-6 mb-4">
      <div class="card h-100 mb-0">
        <div class="card-header card-no-border">
          <div class="header-top">
            <h5 class="m-0">Patient by Department</h5>
          </div>
        </div>
        <div class="card-body pt-0">
          <div class="order-wrapper">
            <div id="department-chart"></div>
          </div>
        </div>
      </div>
    </div>
    <!-- Absents Total -->
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card widget-11 widget-hover">
        <div class="card-body">
          <div class="common-align justify-content-start">
            <div class="icon-circle bg-danger text-white me-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:1.5rem;">
              <i class="fas fa-user-xmark"></i>
            </div>
            <div>
              <span class="c-o-light">Absents <b class="text-danger">Total : 9</b></span>
            </div>
          </div>
          <div class="mt-3" style="max-height: 100px; overflow-y: auto;">
            <ul class="list-unstyled mb-0">
              <li><span>Dr Parth Akspanari</span> <span class="text-danger">Attendance Admin (OT)</span></li>
              <li><span>Dr yash Rank</span> <span class="text-danger">Pharmacist (OT)</span></li>
              <li><span>Dr Ishit Test</span> <span class="text-danger">Pathologist (Cardiology)</span></li>
              <li><span>Dr Ikshit</span> <span class="text-danger">Doctor (Cardiology)</span></li>
              <li><span>Dr Mohan</span> <span class="text-danger">Pharmacist (OT)</span></li>
              <li><span>Dr Mamta Rani</span> <span class="text-danger">Doctor (OT)</span></li>
              <li><span>Rohan</span> <span class="text-danger">Ambulance Driver</span></li>
            </ul>
          </div>
        </div>
      </div>
      <div class="card mb-0 widget-11 widget-hover" style="height: 150px;">
        <div class="card-body">
          <div class="common-align justify-content-start">
            <div class="icon-circle bg-primary text-white me-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:1.5rem;">
              <i class="fas fa-users"></i>
            </div>
            <div>
              <span class="c-o-light">Average Patients Per DR</span>
              <h4 class="counter mb-1" data-target="0.00">0.00</h4>
              <span class="badge badge-light-success f-12 mt-1">AVAILABLE</span>
            </div>
          </div>
          <div class="mt-3">
            <span class="f-light">Per Month</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Ambulance Status -->
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card widget-11 widget-hover" style="height: 204px;">
        <div class="card-body">
          <div class="common-align justify-content-start">
            <div class="icon-circle bg-success text-white me-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:1.5rem;">
              <i class="fas fa-truck-medical"></i>
            </div>
            <div>
              <span class="c-o-light">Ambulance Status</span>
            </div>
          </div>
          <div class="mt-3">
            <div class="d-flex align-items-center mb-2">
              <div class="me-2"><i class="fas fa-circle-check text-warning"></i></div>
              <span>Available</span>
              <span class="badge badge-light-success ms-auto">2</span>
            </div>
            <div class="d-flex align-items-center mb-2">
              <div class="me-2"><i class="fas fa-circle-xmark text-secondary"></i></div>
              <span>Not Available (On Call)</span>
              <span class="badge badge-light-danger ms-auto">0</span>
            </div>
            <div class="d-flex align-items-center">
              <div class="me-2"><i class="fas fa-circle-minus text-dark"></i></div>
              <span>Not In Use</span>
              <span class="badge badge-light-secondary ms-auto">0</span>
            </div>
          </div>
        </div>
      </div>
      <div class="card mb-0 widget-11 widget-hover" style="height: 150px;">
        <div class="card-body">
          <div class="common-align justify-content-start">
            <div class="icon-circle bg-warning text-white me-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:1.5rem;">
              <i class="fas fa-clock"></i>
            </div>
            <div>
              <span class="c-o-light">No Of Late Comes</span>
              <h4 class="counter mb-1" data-target="0">0</h4>
            </div>
          </div>
          <div class="mt-3">
            <span class="f-light">No Any Late come Found</span>
          </div>
        </div>
      </div>
    </div>


    <style>
      .icon-circle {
        border-radius: 50%;
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        transition: all 0.3s ease;
      }

      .icon-circle:hover {
        transform: scale(1.05);
      }
    </style>
  </div>
  <div class="row">
    <!-- Available Staff Per Department Chart Card -->
    <div class="col-xxl-6 col-md-12 mb-4">
      <div class="card h-100 mb-0">
        <div class="card-header card-no-border">
          <div class="header-top">
            <h5 class="m-0">Available Staff Per Department</h5>
          </div>
        </div>
        <div class="card-body pb-0 pt-0">
          <div class="order-wrapper">
            <div id="staff-department-chart"></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card mb-0 default-inline-calender">
        <div class="card-header card-no-border">
          <div class="header-top">
            <h5>Calendar</h5>
          </div>
        </div>
        <div class="card-body pt-0 school-calender">
          <div class="input-group main-inline-calender">
            <input class="form-control" id="inline-calender2" type="date">
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
  <div class="card mb-0 widget-11 widget-hover">
    <div class="card-body">
      <div class="common-align justify-content-start mb-2">
        <div class="icon-circle bg-info text-white me-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:1.5rem;">
          <i class="fas fa-camera-retro"></i>
        </div>
        <div>
          <span class="c-o-light">Selfie Attendance <b class="text-dark">Total : 7</b></span>
        </div>
      </div>
      <div style="max-height: 300px; overflow-y: auto;">
        <ul class="list-unstyled mb-0">
          <li class="d-flex align-items-center mb-2">
            <img src="path/to/selfie1.jpg" class="rounded-circle me-2" width="32" height="32" alt="Selfie">
            <div>
              <span class="fw-bold">Dr. Parth Akbari</span>
              <span class="text-muted small d-block">Attendance Admin (OT)</span>
            </div>
          </li>
          <li class="d-flex align-items-center mb-2">
            <img src="path/to/selfie2.jpg" class="rounded-circle me-2" width="32" height="32" alt="Selfie">
            <div>
              <span class="fw-bold">Dr. Yash Rank</span>
              <span class="text-muted small d-block">Pharmacist (OT)</span>
            </div>
          </li>
          <li class="d-flex align-items-center mb-2">
            <img src="path/to/selfie1.jpg" class="rounded-circle me-2" width="32" height="32" alt="Selfie">
            <div>
              <span class="fw-bold">Dr. Parth Akbari</span>
              <span class="text-muted small d-block">Attendance Admin (OT)</span>
            </div>
          </li>
          <li class="d-flex align-items-center mb-2">
            <img src="path/to/selfie2.jpg" class="rounded-circle me-2" width="32" height="32" alt="Selfie">
            <div>
              <span class="fw-bold">Dr. Yash Rank</span>
              <span class="text-muted small d-block">Pharmacist (OT)</span>
            </div>
          </li>
          <li class="d-flex align-items-center mb-2">
            <img src="path/to/selfie1.jpg" class="rounded-circle me-2" width="32" height="32" alt="Selfie">
            <div>
              <span class="fw-bold">Dr. Parth Akbari</span>
              <span class="text-muted small d-block">Attendance Admin (OT)</span>
            </div>
          </li>
          <li class="d-flex align-items-center mb-2">
            <img src="path/to/selfie2.jpg" class="rounded-circle me-2" width="32" height="32" alt="Selfie">
            <div>
              <span class="fw-bold">Dr. Yash Rank</span>
              <span class="text-muted small d-block">Pharmacist (OT)</span>
            </div>
          </li>
          <li class="d-flex align-items-center mb-2">
            <img src="path/to/selfie1.jpg" class="rounded-circle me-2" width="32" height="32" alt="Selfie">
            <div>
              <span class="fw-bold">Dr. Parth Akbari</span>
              <span class="text-muted small d-block">Attendance Admin (OT)</span>
            </div>
          </li>
          <li class="d-flex align-items-center mb-2">
            <img src="path/to/selfie2.jpg" class="rounded-circle me-2" width="32" height="32" alt="Selfie">
            <div>
              <span class="fw-bold">Dr. Yash Rank</span>
              <span class="text-muted small d-block">Pharmacist (OT)</span>
            </div>
          </li>
          <li class="d-flex align-items-center mb-2">
            <img src="path/to/selfie1.jpg" class="rounded-circle me-2" width="32" height="32" alt="Selfie">
            <div>
              <span class="fw-bold">Dr. Parth Akbari</span>
              <span class="text-muted small d-block">Attendance Admin (OT)</span>
            </div>
          </li>
          <li class="d-flex align-items-center mb-2">
            <img src="path/to/selfie2.jpg" class="rounded-circle me-2" width="32" height="32" alt="Selfie">
            <div>
              <span class="fw-bold">Dr. Yash Rank</span>
              <span class="text-muted small d-block">Pharmacist (OT)</span>
            </div>
          </li>
          <!-- More attendees... -->
        </ul>
      </div>
    </div>
  </div>
</div>
  </div>
</div>
<!-- Container-fluid Ends-->

<!-- Department List Grid View -->
<div class="container-fluid mt-4">
  <div class="row g-3">
    <div class="col-lg-3 col-md-4 col-sm-6">
      <div class="card d-flex flex-row align-items-center p-2 shadow-sm h-100">
        <div class="icon-square bg-warning text-white d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;font-size:1.7rem;">
          <i class="fas fa-users"></i>
        </div>
        <div>
          <div class="fw-bold">Accountant</div>
          <div class="fs-4 fw-bolder text-dark">0</div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6">
      <div class="card d-flex flex-row align-items-center p-2 shadow-sm h-100">
        <div class="icon-square bg-warning text-white d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;font-size:1.7rem;">
          <i class="fas fa-users"></i>
        </div>
        <div>
          <div class="fw-bold">Doctor</div>
          <div class="fs-4 fw-bolder text-dark">3</div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6">
      <div class="card d-flex flex-row align-items-center p-2 shadow-sm h-100">
        <div class="icon-square bg-warning text-white d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;font-size:1.7rem;">
          <i class="fas fa-users"></i>
        </div>
        <div>
          <div class="fw-bold">Pharmacist</div>
          <div class="fs-4 fw-bolder text-dark">3</div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6">
      <div class="card d-flex flex-row align-items-center p-2 shadow-sm h-100">
        <div class="icon-square bg-warning text-white d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;font-size:1.7rem;">
          <i class="fas fa-users"></i>
        </div>
        <div>
          <div class="fw-bold">Pathologist</div>
          <div class="fs-4 fw-bolder text-dark">1</div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6">
      <div class="card d-flex flex-row align-items-center p-2 shadow-sm h-100">
        <div class="icon-square bg-warning text-white d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;font-size:1.7rem;">
          <i class="fas fa-users"></i>
        </div>
        <div>
          <div class="fw-bold">Radiologist</div>
          <div class="fs-4 fw-bolder text-dark">0</div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6">
      <div class="card d-flex flex-row align-items-center p-2 shadow-sm h-100">
        <div class="icon-square bg-warning text-white d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;font-size:1.7rem;">
          <i class="fas fa-users"></i>
        </div>
        <div>
          <div class="fw-bold">Super Admin</div>
          <div class="fs-4 fw-bolder text-dark">1</div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6">
      <div class="card d-flex flex-row align-items-center p-2 shadow-sm h-100">
        <div class="icon-square bg-warning text-white d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;font-size:1.7rem;">
          <i class="fas fa-users"></i>
        </div>
        <div>
          <div class="fw-bold">Receptionist</div>
          <div class="fs-4 fw-bolder text-dark">0</div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6">
      <div class="card d-flex flex-row align-items-center p-2 shadow-sm h-100">
        <div class="icon-square bg-warning text-white d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;font-size:1.7rem;">
          <i class="fas fa-users"></i>
        </div>
        <div>
          <div class="fw-bold">Master Admin</div>
          <div class="fs-4 fw-bolder text-dark">0</div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6">
      <div class="card d-flex flex-row align-items-center p-2 shadow-sm h-100">
        <div class="icon-square bg-warning text-white d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;font-size:1.7rem;">
          <i class="fas fa-users"></i>
        </div>
        <div>
          <div class="fw-bold">Department Staff</div>
          <div class="fs-4 fw-bolder text-dark">0</div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6">
      <div class="card d-flex flex-row align-items-center p-2 shadow-sm h-100">
        <div class="icon-square bg-warning text-white d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;font-size:1.7rem;">
          <i class="fas fa-users"></i>
        </div>
        <div>
          <div class="fw-bold">Ambulance Driver</div>
          <div class="fs-4 fw-bolder text-dark">1</div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6">
      <div class="card d-flex flex-row align-items-center p-2 shadow-sm h-100">
        <div class="icon-square bg-warning text-white d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;font-size:1.7rem;">
          <i class="fas fa-users"></i>
        </div>
        <div>
          <div class="fw-bold">Attendance Admin</div>
          <div class="fs-4 fw-bolder text-dark">1</div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6">
      <div class="card d-flex flex-row align-items-center p-2 shadow-sm h-100">
        <div class="icon-square bg-warning text-white d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;font-size:1.7rem;">
          <i class="fas fa-users"></i>
        </div>
        <div>
          <div class="fw-bold">Chairman</div>
          <div class="fs-4 fw-bolder text-dark">0</div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6">
      <div class="card d-flex flex-row align-items-center p-2 shadow-sm h-100">
        <div class="icon-square bg-warning text-white d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;font-size:1.7rem;">
          <i class="fas fa-users"></i>
        </div>
        <div>
          <div class="fw-bold">TEST</div>
          <div class="fs-4 fw-bolder text-dark">0</div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6">
      <div class="card d-flex flex-row align-items-center p-2 shadow-sm h-100">
        <div class="icon-square bg-warning text-white d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;font-size:1.7rem;">
          <i class="fas fa-users"></i>
        </div>
        <div>
          <div class="fw-bold">Claim Admin</div>
          <div class="fs-4 fw-bolder text-dark">0</div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection


@push('styles')
<link rel="stylesheet" type="text/css" href="{{asset('public/front/assets/css/vendors/flatpickr/flatpickr.min.css') }}">
@endpush
@push('scripts')
<script src="{{ asset('public/front/assets/js/flat-pickr/flatpickr.js') }}"></script>
<script src="{{ asset('public/front/assets/js/chart/apex-chart/apex-chart.js') }}"></script>
<script>
  function formatRupeeShort(val) {
    if (val >= 10000000) return (val / 10000000).toFixed(2) + ' Cr';
    if (val >= 100000) return (val / 100000).toFixed(2) + ' L';
    if (val >= 1000) return (val / 1000).toFixed(2) + ' K';
    return val;
  }

  var options = {
    series: [{
      name: 'Income',
      data: [8000000, 7000000, 600000, 500000, 40000, 30000, 20000, 10000, 2000, 300]
    }],
    chart: {
      type: 'bar',
      height: 300,
      toolbar: {
        show: false
      }
    },
    plotOptions: {
      bar: {
        horizontal: true,
        borderRadius: 4,
        barHeight: '60%',
        distributed: true
      }
    },
    fill: {
      type: 'gradient',
      gradient: {
        shade: 'light',
        type: 'horizontal',
        shadeIntensity: 0.5,
        gradientToColors: ['#1e90ff'],
        inverseColors: false,
        opacityFrom: 0.9,
        opacityTo: 1,
        stops: [0, 100]
      }
    },
    dataLabels: {
      enabled: false,
      formatter: function(val) {
        return '₹' + formatRupeeShort(val);
      },
      style: {
        fontSize: '14px',
        colors: ['#333']
      }
    },
    xaxis: {
      categories: [
        'IPD', 'OPD', 'Expenses', 'General', 'Ambulance',
        'Blood Bank', 'OT', 'Radiology', 'Pathology', 'Pharmacy'
      ],
      title: {
        text: 'Income'
      },
      labels: {
        formatter: function(val) {
          return '₹' + formatRupeeShort(val);
        }
      },
      tickAmount: 4
    },
    yaxis: {
      title: {
        text: undefined
      }
    },
    grid: {
      borderColor: "#e7e7e7",
      xaxis: {
        lines: {
          show: true
        }
      },
      yaxis: {
        lines: {
          show: false
        }
      }
    },
    tooltip: {
      enabled: true,
      y: {
        formatter: function(val) {
          return '₹' + formatRupeeShort(val);
        }
      }
    },
    legend: {
      show: false
    }
  };

  var chart = new ApexCharts(document.querySelector("#income-goal"), options);
  chart.render();

  // Patient Donut Chart
  var patientOptions = {
    series: [120, 80, 45, 30, 20, 10, 5, 3, 2, 1], // Example patient counts
    chart: {
      type: 'pie',
      height: 300,
      toolbar: {
        show: false
      }
    },
    labels: [
      'IPD', 'OPD', 'Expenses', 'General', 'Ambulance',
      'Blood Bank', 'OT', 'Radiology', 'Pathology', 'Pharmacy'
    ],
    legend: {
      show: false
    },
    colors: ['#ffb347', '#ff6961', '#77dd77', '#aec6cf', '#f49ac2', '#cfcfc4', '#b39eb5', '#779ecb', '#966fd6', '#fdfd96'],
    dataLabels: {
      enabled: true,
      formatter: function(val, opts) {
        return opts.w.config.series[opts.seriesIndex];
      }
    },
    tooltip: {
      y: {
        formatter: function(val) {
          return val + ' Patients';
        }
      }
    }
  };

  var patientChart = new ApexCharts(document.querySelector("#patient-goal"), patientOptions);
  patientChart.render();

  // Patients by Gender Chart
  var genderOptions = {
    series: [{
      name: 'Patients',
      data: [62, 34]
    }],
    chart: {
      type: 'bar',
      height: 300,
      toolbar: {
        show: false
      }
    },
    plotOptions: {
      bar: {
        horizontal: false,
        columnWidth: '40%',
        endingShape: 'rounded',
        distributed: true
      }
    },
    colors: ['#6c63ff', '#2ec4b6'],
    dataLabels: {
      enabled: true,
      offsetY: -10,
      style: {
        fontSize: '14px',
        colors: ['#333']
      }
    },
    xaxis: {
      categories: ['Male', 'Female'],
      labels: {
        style: {
          fontSize: '14px'
        }
      }
    },
    yaxis: {
      title: {
        text: undefined
      },
      min: 0,
      forceNiceScale: true
    },
    legend: {
      show: false
    },
    tooltip: {
      y: {
        formatter: function(val) {
          return val + ' Patients';
        }
      }
    }
  };

  var genderChart = new ApexCharts(document.querySelector("#gender-chart"), genderOptions);
  genderChart.render();

  // Patients by Department Horizontal Bar Chart
  var departmentOptions = {
    series: [{
      name: 'Patients',
      data: [18, 25, 3, 7] // Example: OT, Cardiology, Emergency Department (ED), Psychiatry
    }],
    chart: {
      type: 'bar',
      height: 300,
      toolbar: {
        show: false
      }
    },
    plotOptions: {
      bar: {
        horizontal: true,
        barHeight: '60%',
        borderRadius: 4,
        distributed: true
      }
    },
    colors: ['#00b894', '#0984e3', '#fdcb6e', '#d63031'],
    dataLabels: {
      enabled: true,
      style: {
        fontSize: '14px',
        colors: ['#333']
      }
    },
    xaxis: {
      categories: ['OT', 'Cardiology', 'Emergency(ED)', 'Psychiatry'],
      title: {
        text: 'Patients'
      }
    },
    yaxis: {
      title: {
        text: undefined
      }
    },
    legend: {
      show: false
    },
    tooltip: {
      y: {
        formatter: function(val) {
          return val + ' Patients';
        }
      }
    }
  };

  var departmentChart = new ApexCharts(document.querySelector("#department-chart"), departmentOptions);
  departmentChart.render();

  // IPD Bed Status Donut Chart
  var ipdBedDetails = [{
      status: 'Available',
      beds: [{
          floor: 'Floor 1',
          bed: 'B101'
        },
        {
          floor: 'Floor 1',
          bed: 'B102'
        },
        {
          floor: 'Floor 2',
          bed: 'B201'
        }
      ]
    },
    {
      status: 'Occupied',
      beds: [{
          floor: 'Floor 1',
          bed: 'B103'
        },
        {
          floor: 'Floor 2',
          bed: 'B202'
        }
      ]
    }
  ];

  var ipdBedOptions = {
    series: [3, 2], // Example: 3 available, 2 occupied
    chart: {
      type: 'donut',
      height: 300,
      toolbar: {
        show: false
      }
    },
    labels: ['Available', 'Occupied'],
    legend: {
      show: false
    },
    colors: ['#00b894', '#d63031'],
    dataLabels: {
      enabled: true,
      formatter: function(val, opts) {
        return opts.w.config.series[opts.seriesIndex];
      }
    },

    tooltip: {
      custom: function({
        series,
        seriesIndex,
        w
      }) {
        var details = ipdBedDetails[seriesIndex];
        var html = `
        <div style="background:#fff; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.12); padding:14px 18px; min-width:180px; border:1px solid #e0e0e0;">
          <div style="font-weight:700; font-size:16px; color:#222; margin-bottom:8px;">${details.status} Beds</div>
          <ul style="padding-left:18px; margin:0 0 8px 0; color:#444; font-size:15px;">
            ${details.beds.map(bed => `<li style='margin-bottom:2px;'><span style='color:#666;'>${bed.floor}:</span> <b>${bed.bed}</b></li>`).join('')}
          </ul>
          <div style="font-size:13px; color:#888; border-top:1px solid #eee; padding-top:6px; margin-top:6px;">Total: <b>${details.beds.length}</b></div>
        </div>
      `;
        return html;
      }
    }
  };

  var ipdBedChart = new ApexCharts(document.querySelector("#ipd-bed-chart"), ipdBedOptions);
  ipdBedChart.render();

  // OT Bed Status Donut Chart
  var otBedDetails = [{
      status: 'Available',
      beds: [{
          floor: 'Floor 1',
          bed: 'OT101'
        },
        {
          floor: 'Floor 1',
          bed: 'OT102'
        },
        {
          floor: 'Floor 2',
          bed: 'OT201'
        }
      ]
    },
    {
      status: 'Occupied',
      beds: [{
        floor: 'Floor 2',
        bed: 'OT202'
      }]
    }
  ];

  var otBedOptions = {
    series: [3, 1], // Example: 3 available, 1 occupied
    chart: {
      type: 'donut',
      height: 300,
      toolbar: {
        show: false
      }
    },
    labels: ['Available', 'Occupied'],
    legend: {
      show: false
    },
    colors: ['#0984e3', '#fdcb6e'],
    dataLabels: {
      enabled: true,
      formatter: function(val, opts) {
        return opts.w.config.series[opts.seriesIndex];
      }
    },
    tooltip: {
      custom: function({
        series,
        seriesIndex,
        w
      }) {
        var details = otBedDetails[seriesIndex];
        var html = `
        <div style="background:#fff; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.12); padding:14px 18px; min-width:180px; border:1px solid #e0e0e0;">
          <div style="font-weight:700; font-size:16px; color:#222; margin-bottom:8px;">${details.status} OT Beds</div>
          <ul style="padding-left:18px; margin:0 0 8px 0; color:#444; font-size:15px;">
            ${details.beds.map(bed => `<li style='margin-bottom:2px;'><span style='color:#666;'>${bed.floor}:</span> <b>${bed.bed}</b></li>`).join('')}
          </ul>
          <div style="font-size:13px; color:#888; border-top:1px solid #eee; padding-top:6px; margin-top:6px;">Total: <b>${details.beds.length}</b></div>
        </div>
      `;
        return html;
      }
    }
  };

  var otBedChart = new ApexCharts(document.querySelector("#OT-bed-chart"), otBedOptions);
  otBedChart.render();

  // Patients by Age Pie Chart (was Donut)
  var ageOptions = {
    series: [8, 15, 30, 25, 10, 5],
    chart: {
      type: 'pie', // changed from 'donut' to 'pie'
      height: 300,
      toolbar: {
        show: false
      }
    },
    labels: ['0-10', '10-20', '21-30', '31-50', '51-60', '61+'],
    legend: {
      show: false
    },
    colors: ['#00b894', '#00cec9', '#0984e3', '#6c5ce7', '#fdcb6e', '#d63031'],
    dataLabels: {
      enabled: true,
      formatter: function(val, opts) {
        return opts.w.config.series[opts.seriesIndex];
      }
    },
    tooltip: {
      y: {
        formatter: function(val, opts) {
          var total = opts.w.globals.seriesTotals.reduce((a, b) => a + b, 0);
          var percent = ((val / total) * 100).toFixed(2);
          return val + ' Patients (' + percent + '%)';
        }
      }
    }
  };

  var ageChart = new ApexCharts(document.querySelector("#age-chart"), ageOptions);
  ageChart.render();

  // Improved IPD Bed Status Donut Chart Tooltip

  // Generate random staff counts for each department
  var staffDepartments = [
    'IPD', 'OPD', 'Expenses', 'General', 'Ambulance',
    'Blood Bank', 'OT', 'Radiology', 'Pathology', 'Pharmacy'
  ];
  var staffCounts = staffDepartments.map(() => Math.floor(Math.random() * 20) + 5); // 5-24 staff

  var staffDeptOptions = {
    series: [{
      name: 'Available Staff',
      data: staffCounts
    }],
    chart: {
      type: 'bar',
      height: 300,
      toolbar: {
        show: false
      }
    },
    plotOptions: {
      bar: {
        horizontal: true,
        borderRadius: 6,
        barHeight: '60%',
        distributed: true
      }
    },
    colors: ['#1e90ff', '#00b894', '#fdcb6e', '#d63031', '#6c5ce7', '#00cec9', '#ffb347', '#ff6961', '#b39eb5', '#aec6cf'],
    dataLabels: {
      enabled: true,
      style: {
        fontSize: '14px',
        colors: ['#333']
      }
    },
    xaxis: {
      categories: staffDepartments,
      title: {
        text: 'Department'
      },
      labels: {
        style: {
          fontSize: '14px'
        }
      }
    },
    yaxis: {
      title: {
        text: 'Available Staff'
      },
      min: 0
    },
    grid: {
      borderColor: '#e7e7e7',
      xaxis: {
        lines: {
          show: true
        }
      },
      yaxis: {
        lines: {
          show: false
        }
      }
    },
    tooltip: {
      y: {
        formatter: function(val) {
          return val + ' Staff';
        }
      }
    },
    legend: {
      show: false
    }
  };

  var staffDeptChart = new ApexCharts(document.querySelector("#staff-department-chart"), staffDeptOptions);
  staffDeptChart.render();


  flatpickr("#inline-calender2", {
    inline: true,
    allowInput: false,
  });
</script>
@endpush