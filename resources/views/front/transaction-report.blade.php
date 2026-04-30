@extends('layouts.front.app')
@section('title', 'Transaction Report')
@section('content')
<div class="container-fluid">
  <div class="page-title">
    <div class="row">
      <div class="col-sm-4">
        <h3 class="mb-3 mb-md-0">Transaction Report</h3>
      </div>
    </div>
  </div>
</div>
<div class="container-fluid py-3">
    <div class="card mb-3 shadow-sm border-0">
        <div class="card-body p-3">
            <form class="row g-2 align-items-center flex-wrap flex-md-nowrap" onsubmit="return false;">
                <div class="col-12 col-md-4">
                    <select class="form-select" id="roleFilter">
                        <option value="">This Month</option>
                        <option>This Year</option>
                        <option>Last Month</option>
                        <option>Last Year</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-grid">
                    <button class="btn btn-danger w-100" id="searchBtn" type="button"><i class="fa fa-search me-1"></i>Search</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="card mb-3 shadow-sm border-0">
        <div class="card-body p-3">
            <div class="bg-navbar arrow-tabs">
                <ul class="nav nav-pills mb-3" id="staffTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#allView" type="button" role="tab">All</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="opd-tab" data-bs-toggle="tab" data-bs-target="#opdView" type="button" role="tab">OPD Patient</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="ipd-tab" data-bs-toggle="tab" data-bs-target="#ipdView" type="button" role="tab">IPD Patient</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pharmacy-tab" data-bs-toggle="tab" data-bs-target="#pharmacyView" type="button" role="tab">Pharmacy Bill</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pathology-tab" data-bs-toggle="tab" data-bs-target="#pathologyView" type="button" role="tab">Pathology Test</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="radiology-tab" data-bs-toggle="tab" data-bs-target="#radiologyView" type="button" role="tab">Radiology Test</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="ot-tab" data-bs-toggle="tab" data-bs-target="#otView" type="button" role="tab">OT Patient</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="blood-tab" data-bs-toggle="tab" data-bs-target="#bloodView" type="button" role="tab">Blood Issue</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="ambulance-tab" data-bs-toggle="tab" data-bs-target="#ambulanceView" type="button" role="tab">Ambulance Call</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="income-tab" data-bs-toggle="tab" data-bs-target="#incomeView" type="button" role="tab">Income</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="expense-tab" data-bs-toggle="tab" data-bs-target="#expenseView" type="button" role="tab">Expense</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="payroll-tab" data-bs-toggle="tab" data-bs-target="#payrollView" type="button" role="tab">Payroll Report</button>
                    </li>
                </ul>
            </div>
            <div class="tab-content" id="staffTabContent">
                <div class="tab-pane fade show active" id="allView" role="tabpanel">
                <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                        <table id="allview-table" class="display table-striped w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Hospital</th>
                                    <th>Mobile</th>
                                    <th>Role</th>
                                    <th>Tags</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/1.jpg" class="rounded-circle staff-avatar" alt="Dr Saurabh Sakhuja" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Saurabh Sakhuja</td>
                                    <td>Sakhuja Hospital</td>
                                    <td>938657083, 7070707070</td>
                                    <td><span class="badge bg-info">Super Admin</span></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/2.jpg" class="rounded-circle staff-avatar" alt="Dr Parth Akbari" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Parth Akbari</td>
                                    <td>Sakhuja Hospital</td>
                                    <td>9425689523</td>
                                    <td><span class="badge bg-info">Attendance Admin</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/3.jpg" class="rounded-circle staff-avatar" alt="Dr yash Rank" width="48" height="48"></td>
                                    <td class="fw-bold">Dr yash Rank</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/4.jpg" class="rounded-circle staff-avatar" alt="Dr Ishit Test" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Ishit Test</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pathologist</span></td>
                                    <td><span class="badge bg-secondary">Cardiology</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/5.jpg" class="rounded-circle staff-avatar" alt="Dr Ikshit" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Ikshit</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">Cardiology</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/6.jpg" class="rounded-circle staff-avatar" alt="Dr Mohan" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Mohan</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/women/1.jpg" class="rounded-circle staff-avatar" alt="Dr Mamta Rani" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Mamta Rani</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/7.jpg" class="rounded-circle staff-avatar" alt="Rohan" width="48" height="48"></td>
                                    <td class="fw-bold">Rohan</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Ambulance Driver</span></td>
                                    <td><span class="badge bg-secondary">OT</span> <span class="badge bg-primary">Physician Assistants</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/8.jpg" class="rounded-circle staff-avatar" alt="Ramesh" width="48" height="48"></td>
                                    <td class="fw-bold">Ramesh</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">Emergency Department (ED)</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/9.jpg" class="rounded-circle staff-avatar" alt="Manish" width="48" height="48"></td>
                                    <td class="fw-bold">Manish</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">Psychiatry</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="opdView" role="tabpanel">
                    <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                        <table id="opd-table" class="display table-striped w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Hospital</th>
                                    <th>Mobile</th>
                                    <th>Role</th>
                                    <th>Tags</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/1.jpg" class="rounded-circle staff-avatar" alt="Dr Saurabh Sakhuja" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Saurabh Sakhuja</td>
                                    <td>Sakhuja Hospital</td>
                                    <td>938657083, 7070707070</td>
                                    <td><span class="badge bg-info">Super Admin</span></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/2.jpg" class="rounded-circle staff-avatar" alt="Dr Parth Akbari" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Parth Akbari</td>
                                    <td>Sakhuja Hospital</td>
                                    <td>9425689523</td>
                                    <td><span class="badge bg-info">Attendance Admin</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/3.jpg" class="rounded-circle staff-avatar" alt="Dr yash Rank" width="48" height="48"></td>
                                    <td class="fw-bold">Dr yash Rank</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/4.jpg" class="rounded-circle staff-avatar" alt="Dr Ishit Test" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Ishit Test</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pathologist</span></td>
                                    <td><span class="badge bg-secondary">Cardiology</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/5.jpg" class="rounded-circle staff-avatar" alt="Dr Ikshit" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Ikshit</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">Cardiology</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/6.jpg" class="rounded-circle staff-avatar" alt="Dr Mohan" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Mohan</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/women/1.jpg" class="rounded-circle staff-avatar" alt="Dr Mamta Rani" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Mamta Rani</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/7.jpg" class="rounded-circle staff-avatar" alt="Rohan" width="48" height="48"></td>
                                    <td class="fw-bold">Rohan</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Ambulance Driver</span></td>
                                    <td><span class="badge bg-secondary">OT</span> <span class="badge bg-primary">Physician Assistants</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/8.jpg" class="rounded-circle staff-avatar" alt="Ramesh" width="48" height="48"></td>
                                    <td class="fw-bold">Ramesh</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">Emergency Department (ED)</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/9.jpg" class="rounded-circle staff-avatar" alt="Manish" width="48" height="48"></td>
                                    <td class="fw-bold">Manish</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">Psychiatry</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="ipdView" role="tabpanel">
                    <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                        <table id="ipd-table" class="display table-striped w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Hospital</th>
                                    <th>Mobile</th>
                                    <th>Role</th>
                                    <th>Tags</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/1.jpg" class="rounded-circle staff-avatar" alt="Dr Saurabh Sakhuja" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Saurabh Sakhuja</td>
                                    <td>Sakhuja Hospital</td>
                                    <td>938657083, 7070707070</td>
                                    <td><span class="badge bg-info">Super Admin</span></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/2.jpg" class="rounded-circle staff-avatar" alt="Dr Parth Akbari" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Parth Akbari</td>
                                    <td>Sakhuja Hospital</td>
                                    <td>9425689523</td>
                                    <td><span class="badge bg-info">Attendance Admin</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/3.jpg" class="rounded-circle staff-avatar" alt="Dr yash Rank" width="48" height="48"></td>
                                    <td class="fw-bold">Dr yash Rank</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/4.jpg" class="rounded-circle staff-avatar" alt="Dr Ishit Test" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Ishit Test</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pathologist</span></td>
                                    <td><span class="badge bg-secondary">Cardiology</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/5.jpg" class="rounded-circle staff-avatar" alt="Dr Ikshit" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Ikshit</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">Cardiology</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/6.jpg" class="rounded-circle staff-avatar" alt="Dr Mohan" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Mohan</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/women/1.jpg" class="rounded-circle staff-avatar" alt="Dr Mamta Rani" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Mamta Rani</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/7.jpg" class="rounded-circle staff-avatar" alt="Rohan" width="48" height="48"></td>
                                    <td class="fw-bold">Rohan</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Ambulance Driver</span></td>
                                    <td><span class="badge bg-secondary">OT</span> <span class="badge bg-primary">Physician Assistants</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/8.jpg" class="rounded-circle staff-avatar" alt="Ramesh" width="48" height="48"></td>
                                    <td class="fw-bold">Ramesh</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">Emergency Department (ED)</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/9.jpg" class="rounded-circle staff-avatar" alt="Manish" width="48" height="48"></td>
                                    <td class="fw-bold">Manish</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">Psychiatry</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="pharmacyView" role="tabpanel">
                    <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                        <table id="pharmacy-table" class="display table-striped w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Hospital</th>
                                    <th>Mobile</th>
                                    <th>Role</th>
                                    <th>Tags</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/1.jpg" class="rounded-circle staff-avatar" alt="Dr Saurabh Sakhuja" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Saurabh Sakhuja</td>
                                    <td>Sakhuja Hospital</td>
                                    <td>938657083, 7070707070</td>
                                    <td><span class="badge bg-info">Super Admin</span></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/2.jpg" class="rounded-circle staff-avatar" alt="Dr Parth Akbari" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Parth Akbari</td>
                                    <td>Sakhuja Hospital</td>
                                    <td>9425689523</td>
                                    <td><span class="badge bg-info">Attendance Admin</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/3.jpg" class="rounded-circle staff-avatar" alt="Dr yash Rank" width="48" height="48"></td>
                                    <td class="fw-bold">Dr yash Rank</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/4.jpg" class="rounded-circle staff-avatar" alt="Dr Ishit Test" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Ishit Test</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pathologist</span></td>
                                    <td><span class="badge bg-secondary">Cardiology</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/5.jpg" class="rounded-circle staff-avatar" alt="Dr Ikshit" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Ikshit</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">Cardiology</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/6.jpg" class="rounded-circle staff-avatar" alt="Dr Mohan" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Mohan</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/women/1.jpg" class="rounded-circle staff-avatar" alt="Dr Mamta Rani" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Mamta Rani</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/7.jpg" class="rounded-circle staff-avatar" alt="Rohan" width="48" height="48"></td>
                                    <td class="fw-bold">Rohan</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Ambulance Driver</span></td>
                                    <td><span class="badge bg-secondary">OT</span> <span class="badge bg-primary">Physician Assistants</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/8.jpg" class="rounded-circle staff-avatar" alt="Ramesh" width="48" height="48"></td>
                                    <td class="fw-bold">Ramesh</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">Emergency Department (ED)</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/9.jpg" class="rounded-circle staff-avatar" alt="Manish" width="48" height="48"></td>
                                    <td class="fw-bold">Manish</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">Psychiatry</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="pathologyView" role="tabpanel">
                    <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                        <table id="pathology-table" class="display table-striped w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Hospital</th>
                                    <th>Mobile</th>
                                    <th>Role</th>
                                    <th>Tags</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/1.jpg" class="rounded-circle staff-avatar" alt="Dr Saurabh Sakhuja" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Saurabh Sakhuja</td>
                                    <td>Sakhuja Hospital</td>
                                    <td>938657083, 7070707070</td>
                                    <td><span class="badge bg-info">Super Admin</span></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/2.jpg" class="rounded-circle staff-avatar" alt="Dr Parth Akbari" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Parth Akbari</td>
                                    <td>Sakhuja Hospital</td>
                                    <td>9425689523</td>
                                    <td><span class="badge bg-info">Attendance Admin</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/3.jpg" class="rounded-circle staff-avatar" alt="Dr yash Rank" width="48" height="48"></td>
                                    <td class="fw-bold">Dr yash Rank</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/4.jpg" class="rounded-circle staff-avatar" alt="Dr Ishit Test" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Ishit Test</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pathologist</span></td>
                                    <td><span class="badge bg-secondary">Cardiology</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/5.jpg" class="rounded-circle staff-avatar" alt="Dr Ikshit" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Ikshit</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">Cardiology</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/6.jpg" class="rounded-circle staff-avatar" alt="Dr Mohan" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Mohan</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/women/1.jpg" class="rounded-circle staff-avatar" alt="Dr Mamta Rani" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Mamta Rani</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/7.jpg" class="rounded-circle staff-avatar" alt="Rohan" width="48" height="48"></td>
                                    <td class="fw-bold">Rohan</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Ambulance Driver</span></td>
                                    <td><span class="badge bg-secondary">OT</span> <span class="badge bg-primary">Physician Assistants</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/8.jpg" class="rounded-circle staff-avatar" alt="Ramesh" width="48" height="48"></td>
                                    <td class="fw-bold">Ramesh</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">Emergency Department (ED)</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/9.jpg" class="rounded-circle staff-avatar" alt="Manish" width="48" height="48"></td>
                                    <td class="fw-bold">Manish</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">Psychiatry</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="radiologyView" role="tabpanel">
                    <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                        <table id="radiology-table" class="display table-striped w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Hospital</th>
                                    <th>Mobile</th>
                                    <th>Role</th>
                                    <th>Tags</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/1.jpg" class="rounded-circle staff-avatar" alt="Dr Saurabh Sakhuja" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Saurabh Sakhuja</td>
                                    <td>Sakhuja Hospital</td>
                                    <td>938657083, 7070707070</td>
                                    <td><span class="badge bg-info">Super Admin</span></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/2.jpg" class="rounded-circle staff-avatar" alt="Dr Parth Akbari" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Parth Akbari</td>
                                    <td>Sakhuja Hospital</td>
                                    <td>9425689523</td>
                                    <td><span class="badge bg-info">Attendance Admin</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/3.jpg" class="rounded-circle staff-avatar" alt="Dr yash Rank" width="48" height="48"></td>
                                    <td class="fw-bold">Dr yash Rank</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/4.jpg" class="rounded-circle staff-avatar" alt="Dr Ishit Test" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Ishit Test</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pathologist</span></td>
                                    <td><span class="badge bg-secondary">Cardiology</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/5.jpg" class="rounded-circle staff-avatar" alt="Dr Ikshit" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Ikshit</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">Cardiology</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/6.jpg" class="rounded-circle staff-avatar" alt="Dr Mohan" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Mohan</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/women/1.jpg" class="rounded-circle staff-avatar" alt="Dr Mamta Rani" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Mamta Rani</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/7.jpg" class="rounded-circle staff-avatar" alt="Rohan" width="48" height="48"></td>
                                    <td class="fw-bold">Rohan</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Ambulance Driver</span></td>
                                    <td><span class="badge bg-secondary">OT</span> <span class="badge bg-primary">Physician Assistants</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/8.jpg" class="rounded-circle staff-avatar" alt="Ramesh" width="48" height="48"></td>
                                    <td class="fw-bold">Ramesh</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">Emergency Department (ED)</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/9.jpg" class="rounded-circle staff-avatar" alt="Manish" width="48" height="48"></td>
                                    <td class="fw-bold">Manish</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">Psychiatry</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="otView" role="tabpanel">
                    <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                        <table id="ot-table" class="display table-striped w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Hospital</th>
                                    <th>Mobile</th>
                                    <th>Role</th>
                                    <th>Tags</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/1.jpg" class="rounded-circle staff-avatar" alt="Dr Saurabh Sakhuja" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Saurabh Sakhuja</td>
                                    <td>Sakhuja Hospital</td>
                                    <td>938657083, 7070707070</td>
                                    <td><span class="badge bg-info">Super Admin</span></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/2.jpg" class="rounded-circle staff-avatar" alt="Dr Parth Akbari" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Parth Akbari</td>
                                    <td>Sakhuja Hospital</td>
                                    <td>9425689523</td>
                                    <td><span class="badge bg-info">Attendance Admin</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/3.jpg" class="rounded-circle staff-avatar" alt="Dr yash Rank" width="48" height="48"></td>
                                    <td class="fw-bold">Dr yash Rank</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/4.jpg" class="rounded-circle staff-avatar" alt="Dr Ishit Test" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Ishit Test</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pathologist</span></td>
                                    <td><span class="badge bg-secondary">Cardiology</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/5.jpg" class="rounded-circle staff-avatar" alt="Dr Ikshit" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Ikshit</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">Cardiology</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/6.jpg" class="rounded-circle staff-avatar" alt="Dr Mohan" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Mohan</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/women/1.jpg" class="rounded-circle staff-avatar" alt="Dr Mamta Rani" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Mamta Rani</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/7.jpg" class="rounded-circle staff-avatar" alt="Rohan" width="48" height="48"></td>
                                    <td class="fw-bold">Rohan</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Ambulance Driver</span></td>
                                    <td><span class="badge bg-secondary">OT</span> <span class="badge bg-primary">Physician Assistants</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/8.jpg" class="rounded-circle staff-avatar" alt="Ramesh" width="48" height="48"></td>
                                    <td class="fw-bold">Ramesh</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">Emergency Department (ED)</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/9.jpg" class="rounded-circle staff-avatar" alt="Manish" width="48" height="48"></td>
                                    <td class="fw-bold">Manish</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">Psychiatry</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="bloodView" role="tabpanel">
                    <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                        <table id="blood-table" class="display table-striped w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Hospital</th>
                                    <th>Mobile</th>
                                    <th>Role</th>
                                    <th>Tags</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/1.jpg" class="rounded-circle staff-avatar" alt="Dr Saurabh Sakhuja" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Saurabh Sakhuja</td>
                                    <td>Sakhuja Hospital</td>
                                    <td>938657083, 7070707070</td>
                                    <td><span class="badge bg-info">Super Admin</span></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/2.jpg" class="rounded-circle staff-avatar" alt="Dr Parth Akbari" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Parth Akbari</td>
                                    <td>Sakhuja Hospital</td>
                                    <td>9425689523</td>
                                    <td><span class="badge bg-info">Attendance Admin</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/3.jpg" class="rounded-circle staff-avatar" alt="Dr yash Rank" width="48" height="48"></td>
                                    <td class="fw-bold">Dr yash Rank</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/4.jpg" class="rounded-circle staff-avatar" alt="Dr Ishit Test" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Ishit Test</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pathologist</span></td>
                                    <td><span class="badge bg-secondary">Cardiology</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/5.jpg" class="rounded-circle staff-avatar" alt="Dr Ikshit" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Ikshit</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">Cardiology</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/6.jpg" class="rounded-circle staff-avatar" alt="Dr Mohan" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Mohan</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/women/1.jpg" class="rounded-circle staff-avatar" alt="Dr Mamta Rani" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Mamta Rani</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/7.jpg" class="rounded-circle staff-avatar" alt="Rohan" width="48" height="48"></td>
                                    <td class="fw-bold">Rohan</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Ambulance Driver</span></td>
                                    <td><span class="badge bg-secondary">OT</span> <span class="badge bg-primary">Physician Assistants</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/8.jpg" class="rounded-circle staff-avatar" alt="Ramesh" width="48" height="48"></td>
                                    <td class="fw-bold">Ramesh</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">Emergency Department (ED)</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/9.jpg" class="rounded-circle staff-avatar" alt="Manish" width="48" height="48"></td>
                                    <td class="fw-bold">Manish</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">Psychiatry</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="ambulanceView" role="tabpanel">
                    <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                        <table id="ambulance-table" class="display table-striped w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Hospital</th>
                                    <th>Mobile</th>
                                    <th>Role</th>
                                    <th>Tags</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/1.jpg" class="rounded-circle staff-avatar" alt="Dr Saurabh Sakhuja" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Saurabh Sakhuja</td>
                                    <td>Sakhuja Hospital</td>
                                    <td>938657083, 7070707070</td>
                                    <td><span class="badge bg-info">Super Admin</span></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/2.jpg" class="rounded-circle staff-avatar" alt="Dr Parth Akbari" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Parth Akbari</td>
                                    <td>Sakhuja Hospital</td>
                                    <td>9425689523</td>
                                    <td><span class="badge bg-info">Attendance Admin</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/3.jpg" class="rounded-circle staff-avatar" alt="Dr yash Rank" width="48" height="48"></td>
                                    <td class="fw-bold">Dr yash Rank</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/4.jpg" class="rounded-circle staff-avatar" alt="Dr Ishit Test" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Ishit Test</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pathologist</span></td>
                                    <td><span class="badge bg-secondary">Cardiology</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/5.jpg" class="rounded-circle staff-avatar" alt="Dr Ikshit" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Ikshit</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">Cardiology</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/6.jpg" class="rounded-circle staff-avatar" alt="Dr Mohan" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Mohan</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/women/1.jpg" class="rounded-circle staff-avatar" alt="Dr Mamta Rani" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Mamta Rani</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/7.jpg" class="rounded-circle staff-avatar" alt="Rohan" width="48" height="48"></td>
                                    <td class="fw-bold">Rohan</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Ambulance Driver</span></td>
                                    <td><span class="badge bg-secondary">OT</span> <span class="badge bg-primary">Physician Assistants</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/8.jpg" class="rounded-circle staff-avatar" alt="Ramesh" width="48" height="48"></td>
                                    <td class="fw-bold">Ramesh</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">Emergency Department (ED)</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/9.jpg" class="rounded-circle staff-avatar" alt="Manish" width="48" height="48"></td>
                                    <td class="fw-bold">Manish</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">Psychiatry</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="incomeView" role="tabpanel">
                    <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                        <table id="income-table" class="display table-striped w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Hospital</th>
                                    <th>Mobile</th>
                                    <th>Role</th>
                                    <th>Tags</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/1.jpg" class="rounded-circle staff-avatar" alt="Dr Saurabh Sakhuja" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Saurabh Sakhuja</td>
                                    <td>Sakhuja Hospital</td>
                                    <td>938657083, 7070707070</td>
                                    <td><span class="badge bg-info">Super Admin</span></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/2.jpg" class="rounded-circle staff-avatar" alt="Dr Parth Akbari" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Parth Akbari</td>
                                    <td>Sakhuja Hospital</td>
                                    <td>9425689523</td>
                                    <td><span class="badge bg-info">Attendance Admin</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/3.jpg" class="rounded-circle staff-avatar" alt="Dr yash Rank" width="48" height="48"></td>
                                    <td class="fw-bold">Dr yash Rank</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/4.jpg" class="rounded-circle staff-avatar" alt="Dr Ishit Test" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Ishit Test</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pathologist</span></td>
                                    <td><span class="badge bg-secondary">Cardiology</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/5.jpg" class="rounded-circle staff-avatar" alt="Dr Ikshit" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Ikshit</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">Cardiology</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/6.jpg" class="rounded-circle staff-avatar" alt="Dr Mohan" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Mohan</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/women/1.jpg" class="rounded-circle staff-avatar" alt="Dr Mamta Rani" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Mamta Rani</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/7.jpg" class="rounded-circle staff-avatar" alt="Rohan" width="48" height="48"></td>
                                    <td class="fw-bold">Rohan</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Ambulance Driver</span></td>
                                    <td><span class="badge bg-secondary">OT</span> <span class="badge bg-primary">Physician Assistants</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/8.jpg" class="rounded-circle staff-avatar" alt="Ramesh" width="48" height="48"></td>
                                    <td class="fw-bold">Ramesh</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">Emergency Department (ED)</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/9.jpg" class="rounded-circle staff-avatar" alt="Manish" width="48" height="48"></td>
                                    <td class="fw-bold">Manish</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">Psychiatry</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="expenseView" role="tabpanel">
                    <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                        <table id="expense-table" class="display table-striped w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Hospital</th>
                                    <th>Mobile</th>
                                    <th>Role</th>
                                    <th>Tags</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/1.jpg" class="rounded-circle staff-avatar" alt="Dr Saurabh Sakhuja" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Saurabh Sakhuja</td>
                                    <td>Sakhuja Hospital</td>
                                    <td>938657083, 7070707070</td>
                                    <td><span class="badge bg-info">Super Admin</span></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/2.jpg" class="rounded-circle staff-avatar" alt="Dr Parth Akbari" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Parth Akbari</td>
                                    <td>Sakhuja Hospital</td>
                                    <td>9425689523</td>
                                    <td><span class="badge bg-info">Attendance Admin</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/3.jpg" class="rounded-circle staff-avatar" alt="Dr yash Rank" width="48" height="48"></td>
                                    <td class="fw-bold">Dr yash Rank</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/4.jpg" class="rounded-circle staff-avatar" alt="Dr Ishit Test" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Ishit Test</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pathologist</span></td>
                                    <td><span class="badge bg-secondary">Cardiology</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/5.jpg" class="rounded-circle staff-avatar" alt="Dr Ikshit" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Ikshit</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">Cardiology</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/6.jpg" class="rounded-circle staff-avatar" alt="Dr Mohan" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Mohan</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/women/1.jpg" class="rounded-circle staff-avatar" alt="Dr Mamta Rani" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Mamta Rani</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/7.jpg" class="rounded-circle staff-avatar" alt="Rohan" width="48" height="48"></td>
                                    <td class="fw-bold">Rohan</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Ambulance Driver</span></td>
                                    <td><span class="badge bg-secondary">OT</span> <span class="badge bg-primary">Physician Assistants</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/8.jpg" class="rounded-circle staff-avatar" alt="Ramesh" width="48" height="48"></td>
                                    <td class="fw-bold">Ramesh</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">Emergency Department (ED)</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/9.jpg" class="rounded-circle staff-avatar" alt="Manish" width="48" height="48"></td>
                                    <td class="fw-bold">Manish</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">Psychiatry</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="payrollView" role="tabpanel">
                    <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                        <table id="payroll-table" class="display table-striped w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Hospital</th>
                                    <th>Mobile</th>
                                    <th>Role</th>
                                    <th>Tags</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/1.jpg" class="rounded-circle staff-avatar" alt="Dr Saurabh Sakhuja" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Saurabh Sakhuja</td>
                                    <td>Sakhuja Hospital</td>
                                    <td>938657083, 7070707070</td>
                                    <td><span class="badge bg-info">Super Admin</span></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/2.jpg" class="rounded-circle staff-avatar" alt="Dr Parth Akbari" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Parth Akbari</td>
                                    <td>Sakhuja Hospital</td>
                                    <td>9425689523</td>
                                    <td><span class="badge bg-info">Attendance Admin</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/3.jpg" class="rounded-circle staff-avatar" alt="Dr yash Rank" width="48" height="48"></td>
                                    <td class="fw-bold">Dr yash Rank</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/4.jpg" class="rounded-circle staff-avatar" alt="Dr Ishit Test" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Ishit Test</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pathologist</span></td>
                                    <td><span class="badge bg-secondary">Cardiology</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/5.jpg" class="rounded-circle staff-avatar" alt="Dr Ikshit" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Ikshit</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">Cardiology</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/6.jpg" class="rounded-circle staff-avatar" alt="Dr Mohan" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Mohan</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/women/1.jpg" class="rounded-circle staff-avatar" alt="Dr Mamta Rani" width="48" height="48"></td>
                                    <td class="fw-bold">Dr Mamta Rani</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">OT</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/7.jpg" class="rounded-circle staff-avatar" alt="Rohan" width="48" height="48"></td>
                                    <td class="fw-bold">Rohan</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Ambulance Driver</span></td>
                                    <td><span class="badge bg-secondary">OT</span> <span class="badge bg-primary">Physician Assistants</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/8.jpg" class="rounded-circle staff-avatar" alt="Ramesh" width="48" height="48"></td>
                                    <td class="fw-bold">Ramesh</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Doctor</span></td>
                                    <td><span class="badge bg-secondary">Emergency Department (ED)</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://randomuser.me/api/portraits/men/9.jpg" class="rounded-circle staff-avatar" alt="Manish" width="48" height="48"></td>
                                    <td class="fw-bold">Manish</td>
                                    <td>Sakhuja Hospital</td>
                                    <td></td>
                                    <td><span class="badge bg-info">Pharmacist</span></td>
                                    <td><span class="badge bg-secondary">Psychiatry</span> <span class="badge bg-primary">Surgeons</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('public/front/assets/css/vendors/jquery.dataTables.css') }}">
<link rel="stylesheet" href="{{ asset('public/front/assets/css/vendors/dataTables.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('public/front/assets/css/vendors/autoFill.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('public/front/assets/css/vendors/keyTable.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('public/front/assets/css/vendors/buttons.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('public/front/assets/css/vendors/fixedHeader.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('public/front/assets/css/vendors/responsive.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('public/front/assets/css/vendors/rowReorder.bootstrap5.css') }}">
<style>
.staff-card { transition: box-shadow .2s; }
.staff-card:hover { box-shadow: 0 0 0 4px #e3f2fd; }
.staff-avatar { object-fit: cover; border: 2px solid #e3e3e3; }
</style>
@endpush

@push('scripts')

<script src="{{ asset('public/front/assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatables/dataTables1.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatables/dataTables.bootstrap5.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/dataTables.autoFill.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/autoFill.bootstrap5.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/dataTables.keyTable.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/keyTable.bootstrap5.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/dataTables.buttons.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/buttons.bootstrap5.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/buttons.colVis.min.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/dataTables.fixedHeader.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/fixedHeader.bootstrap5.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/jszip.min.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/pdfmake.min.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/vfs_fonts.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/buttons.html5.min.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/buttons.print.min.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/dataTables.responsive.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/responsive.bootstrap5.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/dataTables.rowReorder.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/rowReorder.bootstrap5.js') }}"></script>
<script>
var liveconsultationTable = $('#allview-table,#opd-table,#ipd-table,#pharmacy-table,#pathology-table,#radiology-table,#ot-table,#blood-table,#ambulance-table,#income-table,#expense-table,#payroll-table').DataTable({
      dom: "fBrtip",
      buttons: [
        { extend: 'copy', className: 'buttons-copy btn btn-light', text: '<i class=\"fa fa-copy\"></i>', titleAttr: 'Copy' },
        { extend: 'csv', className: 'buttons-csv btn btn-info', text: '<i class=\"fa fa-file-csv\"></i>', titleAttr: 'Export as CSV' },
        { extend: 'excel', className: 'buttons-excel btn btn-success', text: '<i class=\"fa fa-file-excel\"></i>', titleAttr: 'Export as Excel' },
        { extend: 'pdf', className: 'buttons-pdf btn btn-danger', text: '<i class=\"fa fa-file-pdf\"></i>', titleAttr: 'Export as PDF' },
        { extend: 'print', className: 'buttons-print btn btn-primary', text: '<i class=\"fa fa-print\"></i>', titleAttr: 'Print Table' },
        { extend: 'colvis', className: 'buttons-colvis btn btn-dark', text: '<i class=\"fa fa-columns\"></i>', titleAttr: 'Column Visibility' }
      ],
      language: {
        search: '',
        searchPlaceholder: 'Search Leaves...'
      },
      lengthChange: true,
      paging: true,
      info: true,
      ordering: true,
      scrollX: true,
      autoWidth: true,
      responsive: true
    });
    $(document).ready(function() {
        // Trigger adjustment after tab is shown
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (event) {
            if (event.target.id === 'allView') {
                setTimeout(() => {
                    $('#allview-table').DataTable().columns.adjust().responsive.recalc();
                }, 200);
            } else if (event.target.id === 'opdView') {
                setTimeout(() => {
                    $('#opd-table').DataTable().columns.adjust().responsive.recalc();
                }, 200);
            } else if (event.target.id === 'ipdView') {
                setTimeout(() => {
                    $('#ipd-table').DataTable().columns.adjust().responsive.recalc();
                }, 200);
            } else if (event.target.id === 'pharmacyView') {
                setTimeout(() => {
                    $('#pharmacy-table').DataTable().columns.adjust().responsive.recalc();
                }, 200);
            } else if (event.target.id === 'pathologyView') {
                setTimeout(() => {
                    $('#pathology-table').DataTable().columns.adjust().responsive.recalc();
                }, 200);
            } else if (event.target.id === 'radiologyView') {
                setTimeout(() => {
                    $('#radiology-table').DataTable().columns.adjust().responsive.recalc();
                }, 200);
            } else if (event.target.id === 'otView') {
                setTimeout(() => {
                    $('#ot-table').DataTable().columns.adjust().responsive.recalc();
                }, 200);
            } else if (event.target.id === 'bloodView') {
                setTimeout(() => {
                    $('#blood-table').DataTable().columns.adjust().responsive.recalc();
                }, 200);
            } else if (event.target.id === 'ambulanceView') {
                setTimeout(() => {
                    $('#ambulance-table').DataTable().columns.adjust().responsive.recalc();
                }, 200);
            } else if (event.target.id === 'incomeView') {
                setTimeout(() => {
                    $('#income-table').DataTable().columns.adjust().responsive.recalc();
                }, 200);
            } else if (event.target.id === 'expenseView') {
                setTimeout(() => {
                    $('#expense-table').DataTable().columns.adjust().responsive.recalc();
                }, 200);
            } else if (event.target.id === 'payrollView') {
                setTimeout(() => {
                    $('#payroll-table').DataTable().columns.adjust().responsive.recalc();
                }, 200);
            }
        });
    });
</script>
    @endpush
