@extends('layouts.hospital.app')
@section('title','OPD Patient | Paracare+')
@section('page_header_icon', '🩺')
@section('page_subtitle', 'Manage Patient Profile')
@section('content')
<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="user-profile">
        <div class="row">
            <!-- user profile first-style start-->
            <div class="col-sm-12">
                <div class="card hovercard text-center common-user-image bg-white">
                    @if($patient->image)
                        <div class="cardheader h-auto" style="background-image:none;">
                            <div class="user-image">
                                <div class="avatar">
                                    <div class="common-align">
                                        <div>
                                            <img id="output" src="{{ url('public/storage/'.$patient->image) }}" class="rounded-circle"
                                                alt="Profile Image">
                                        </div>
                                        <div class="user-designation text-danger">
                                            {{ $patient->name }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="ttl-info text-start">
                                    <h6><i class="fa-solid fa-user-shield pe-2"></i>Guardian Name</h6>
                                    <span>{{ $patient->guardian_name }}</span>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="ttl-info text-start">
                                    <h6><i class="fa-solid fa-mars pe-2"></i>Gender</h6>
                                    <span>{{ $patient->gender }}</span>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="ttl-info text-start">
                                    <h6><i class="fa-solid fa-hourglass-half pe-2"></i>Age</h6><span>{{ $patient->age_years }} Years</span> <span>{{ $patient->age_months }} Months</span>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="ttl-info text-start">
                                    <h6><i class="fa-solid fa-phone pe-2"></i>Phone</h6>
                                    <span>{{ $patient->phone }}</span>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="ttl-info text-start">
                                    <h6><i class="fa-solid fa-envelope pe-2"></i>Email</h6>
                                    <span>{{ $patient->email }}</span>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="ttl-info text-start">
                                    <h6><i class="fa-solid fa-id-card pe-2"></i>Patient Id</h6>
                                    <span>{{ $patient->patient_id }}</span>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="ttl-info text-start">
                                    <h6><i class="fa-solid fa-location-dot pe-2"></i>Address</h6>
                                    <span>{{ $patient->address }}</span>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="ttl-info text-start">
                                    <h6><i class="fa-solid fa-cake-candles pe-2"></i>Date of Birth</h6>
                                    <span>{{ \Carbon\Carbon::parse($patient->date_of_birth)->format('d-m-Y') }}</span>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="ttl-info text-start">
                                    <h6><i class="fa-solid fa-ring pe-2"></i>Married Status</h6>
                                    <span>{{ $patient->marital_status }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- user profile first-style end-->
            <div class="col-12">
                <!-- user profile menu start-->
                <div class="col-12">
                    <div class="row scope-bottom-wrapper user-profile-wrapper">
                        <div class="col-xxl-3 user-xl-25 col-xl-4 box-col-4">
                            <div class="card">
                                <div class="card-body">
                                    <ul class="sidebar-left-icons nav nav-pills" id="add-product-pills-tab"
                                        role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="visits-tab" data-bs-toggle="pill"
                                                href="#visits" role="tab" aria-controls="visits"
                                                aria-selected="false">
                                                <div class="nav-rounded">
                                                    <div class="product-icons"><i class="fa-solid fa-user"></i></div>
                                                </div>
                                                <div class="product-tab-content">
                                                    <h6>Visits</h6>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="nav-item"> <a class="nav-link " id="patient-timeline-tab"
                                                data-bs-toggle="pill" href="#timeline-patient" role="tab"
                                                aria-controls="timeline-patient" aria-selected="false">
                                                <div class="nav-rounded">
                                                    <div class="product-icons"><i class="fa-solid fa-timeline"></i>
                                                    </div>
                                                </div>
                                                <div class="product-tab-content">
                                                    <h6>Timeline</h6>
                                                </div>
                                            </a>
                                        </li>
                                        
                                        @if(auth()->user()->can('view-diagnosis'))
                                            <li class="nav-item"> <a class="nav-link" id="diagnosis-tab"
                                                    data-bs-toggle="pill" href="#diagnosis" role="tab"
                                                    aria-controls="diagnosis" aria-selected="false">
                                                    <div class="nav-rounded">
                                                        <div class="product-icons"><i class="fa-solid fa-list-check"></i>
                                                        </div>
                                                    </div>
                                                    <div class="product-tab-content">
                                                        <h6>Diagnosis</h6>
                                                    </div>
                                                </a>
                                            </li>
                                        @endif

                                        <li class="nav-item"> <a class="nav-link" id="live-consultation-project-tab"
                                                data-bs-toggle="pill" href="#live-consultation-project" role="tab"
                                                aria-controls="live-consultation-project" aria-selected="false">
                                                <div class="nav-rounded">
                                                    <div class="product-icons"><i class="fa-solid fa-list-check"></i>
                                                    </div>
                                                </div>
                                                <div class="product-tab-content">
                                                    <h6>Live Consultation</h6>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="nav-item"> <a class="nav-link" id="consolidated-tab"
                                                data-bs-toggle="pill" href="#consolidated" role="tab"
                                                aria-controls="consolidated" aria-selected="false">
                                                <div class="nav-rounded">
                                                    <div class="product-icons"><i class="fa-solid fa-list-check"></i>
                                                    </div>
                                                </div>
                                                <div class="product-tab-content">
                                                    <h6>Consolidated</h6>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="nav-item"> <a class="nav-link" id="charges-tab"
                                                data-bs-toggle="pill" href="#charges" role="tab"
                                                aria-controls="charges" aria-selected="false">
                                                <div class="nav-rounded">
                                                    <div class="product-icons"><i class="fa-solid fa-wallet"></i>
                                                    </div>
                                                </div>
                                                <div class="product-tab-content">
                                                    <h6>Charges</h6>
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-9 user-xl-75 col-xl-8 box-col-8e">
                            <div class="row">
                                <div class="col-12">
                                    <div class="tab-content" id="add-product-pills-tabContent">
                                        <div class="tab-pane fade show active" id="visits" role="tabpanel"
                                            aria-labelledby="visits-tab">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>Visits</h5>
                                                </div>
                                                <div class="card-body dark-timeline">
                                                    <div
                                                        class="dt-ext table-responsive custom-scrollbar html-expert-table">
                                                        <table id="visits-table" class="display table-striped w-100">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>OPD No</th>
                                                                    <th>Appointment Date</th>
                                                                    <th>Consultant</th>
                                                                    <th>Reference</th>
                                                                    <th>Symptoms</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="opd-visits-table-body">
                                                                @foreach($visits as $visit)
                                                                    @php
                                                                        $visitPayload = [
                                                                            'id' => $visit->id,
                                                                            'case_no' => $visit->case_no,
                                                                            'appointment_date' => $visit->appointment_date
                                                                                ? \Carbon\Carbon::parse($visit->appointment_date)->format('d-m-Y h:i A')
                                                                                : null,
                                                                            'respiration' => $visit->respiration,
                                                                            'diabetes' => $visit->diabetes,
                                                                            'pluse' => $visit->pluse,
                                                                            'systolic_bp' => $visit->systolic_bp,
                                                                            'diastolic_bp' => $visit->diastolic_bp,
                                                                            'temperature' => $visit->temperature,
                                                                            'height' => $visit->height,
                                                                            'weight' => $visit->weight,
                                                                            'bmi' => $visit->bmi,
                                                                            'body_area' => $visit->body_area,
                                                                            'social_known_allergies' => $visit->social_known_allergies,
                                                                            'social_allergic_reactions' => $visit->social_allergic_reactions,
                                                                            'occupation' => $visit->occupation,
                                                                            'social_marital_status' => $visit->social_marital_status,
                                                                            'place_of_birth' => $visit->place_of_birth,
                                                                            'current_location' => $visit->current_location,
                                                                            'years_in_current_location' => $visit->years_in_current_location,
                                                                            'social_habits' => $visit->social_habits,
                                                                            'family_history' => $visit->family_history,
                                                                        ];
                                                                    @endphp
                                                                    <tr>
                                                                        <td>{{ $visit->case_no }}</td>
                                                                        <td>{{ \Carbon\Carbon::parse($visit->appointment_date)->format('d-m-Y h:i A') }}</td>
                                                                        <td>{{ $visit->consultant?->full_name }}</td>
                                                                        <td>{{ $visit->reference }}</td>
                                                                        <td>{{ $visit->symptoms_name }}</td>
                                                                        <td>
                                                                            <ul class="action mb-0">
                                                                                <li class="view me-2" data-bs-toggle="tooltip" title="View Visit">
                                                                                    <a href="#" class="open-visit-summary" data-view-url="{{ route('hospital.opd-patient.visit-summary.view', ['opdPatient' => $visit->id]) }}">
                                                                                        <i class="text-danger fa-solid fa-eye"></i>
                                                                                    </a>
                                                                                </li>
                                                                                <li class="view me-2" data-bs-toggle="tooltip"
                                                                                    title="Vital & Social History"><a href="#"
                                                                                    class="open-vitals-social"
                                                                                    data-bs-toggle="modal"
                                                                                    data-bs-target="#vitalsSocialModal"
                                                                                    data-save-url-template="{{ route('hospital.opd-patient.vitals-social.update', ['opdPatient' => '__OPD_PATIENT__']) }}"
                                                                                    data-visit='@json($visitPayload)'><i
                                                                                        class="text-danger fa-solid fa-heart"></i></a>
                                                                                </li>
                                                                                
                                                                                <li class="view me-2" data-bs-toggle="tooltip" title="Print Visit Summary">
                                                                                    <a href="{{ route('hospital.opd-patient.visit-summary.print', ['opdPatient' => $visit->id]) }}" target="_blank">
                                                                                        <i class="text-danger fa-solid fa-print"></i>
                                                                                    </a>
                                                                                </li>
                                                                                <li class="view me-2" data-bs-toggle="tooltip" title="Print Visit Bill">
                                                                                    <a href="{{ route('hospital.opd-patient.charges.visit-bill.print', ['patient' => $patient->id, 'opdPatient' => $visit->id]) }}" target="_blank">
                                                                                        <i class="text-danger fa-solid fa-file-invoice-dollar"></i>
                                                                                    </a>
                                                                                </li>
                                                                                @if($visit->prescription)
                                                                                    <li class="view me-2" data-bs-toggle="tooltip" title="View Prescription">
                                                                                        <a href="#" class="open-prescription-view" data-view-url="{{ route('hospital.opd-patient.prescription.view', ['opdPatient' => $visit->id]) }}">
                                                                                            <i class="text-danger fa-solid fa-file-prescription"></i>
                                                                                        </a>
                                                                                    </li>
                                                                                @else
                                                                                    <li class="view me-2" data-bs-toggle="tooltip" title="Add Prescription">
                                                                                        <a href="#" class="open-prescription-form" data-form-url="{{ route('hospital.opd-patient.prescription.form', ['opdPatient' => $visit->id]) }}">
                                                                                            <i class="text-danger fa-solid fa-prescription"></i>
                                                                                        </a>
                                                                                    </li>
                                                                                @endif
                                                                                @can('create-pathology-order')
                                                                                    <li class="view me-2" data-bs-toggle="tooltip" title="Order Pathology Tests">
                                                                                        <a href="#" class="open-diagnostic-order"
                                                                                            data-order-type="pathology"
                                                                                            data-show-url="{{ route('hospital.opd-patient.diagnostics.showform', ['opdPatient' => $visit->id]) }}"
                                                                                            data-store-url="{{ route('hospital.opd-patient.diagnostics.store', ['opdPatient' => $visit->id]) }}">
                                                                                            <i class="text-danger fa-solid fa-vial-circle-check"></i>
                                                                                        </a>
                                                                                    </li>
                                                                                @endcan
                                                                                @can('create-radiology-order')
                                                                                    <li class="view me-2" data-bs-toggle="tooltip" title="Order Radiology Tests">
                                                                                        <a href="#" class="open-diagnostic-order"
                                                                                            data-order-type="radiology"
                                                                                            data-show-url="{{ route('hospital.opd-patient.diagnostics.showform', ['opdPatient' => $visit->id]) }}"
                                                                                            data-store-url="{{ route('hospital.opd-patient.diagnostics.store', ['opdPatient' => $visit->id]) }}">
                                                                                            <i class="text-danger fa-solid fa-x-ray"></i>
                                                                                        </a>
                                                                                    </li>
                                                                                @endcan
                                                                            </ul>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="timeline-patient" role="tabpanel"
                                            aria-labelledby="patient-timeline-tab">
                                            <div class="notification">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5>Patient Timeline</h5>
                                                    </div>
                                                    <div class="card-body dark-timeline">
                                                        <ul>
                                                            @forelse($timelineEntries as $timelineEntry)
                                                                @php
                                                                    $eventKey = (string) ($timelineEntry->event_key ?? '');
                                                                    $dotClass = str_contains($eventKey, 'deleted')
                                                                        ? 'activity-dot-warning'
                                                                        : 'activity-dot-primary';
                                                                    $logTime = $timelineEntry->logged_at ?? $timelineEntry->created_at;
                                                                @endphp
                                                                <li class="d-flex">
                                                                    <div class="{{ $dotClass }}"></div>
                                                                    <div class="w-100 ms-3">
                                                                        <p class="d-flex justify-content-between mb-2">
                                                                            <span class="date-content light-background">{{ optional($logTime)->format('d M, Y') }}</span>
                                                                            <span>{{ optional($logTime)->format('h:i A') }}</span>
                                                                        </p>
                                                                        <h6>{{ $timelineEntry->title }}<span class="dot-notification"></span></h6>
                                                                        @if($timelineEntry->description)
                                                                            <span class="c-o-light d-block">{{ $timelineEntry->description }}</span>
                                                                        @endif
                                                                        <span class="badge badge-light-info mt-1 text-uppercase">{{ $timelineEntry->encounter_type }}</span>
                                                                    </div>
                                                                </li>
                                                            @empty
                                                                <li class="text-center text-muted">No timeline activity found for this patient.</li>
                                                            @endforelse
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @if(auth()->user()->can('view-diagnosis'))
                                            <div class="tab-pane fade" id="diagnosis" role="tabpanel"
                                                aria-labelledby="diagnosis-tab">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5>Diagnosis</h5>

                                                        <!-- add diagnosis button -->
                                                        <div class="card-header-right">
                                                            @can('create-diagnosis')
                                                                <button class="btn btn-info add-diagnosis-btn" data-patient-id="{{ $patient->id }}" data-bs-toggle="tooltip" title="Add Diagnosis">
                                                                    +
                                                                </button>
                                                            @endcan
                                                        </div>
                                                    </div>
                                                    <div class="card-body ">
                                                        <div
                                                            class="dt-ext table-responsive custom-scrollbar html-expert-table">
                                                            <table id="diagnosis-table" data-patient-id="{{ $patient->id }}" class="display table-striped w-100">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th>Report Type</th>
                                                                        <th>Report Date</th>
                                                                        <th>Description</th>
                                                                        <th>Action</th>
                                                                    </tr>
                                                                </thead>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="tab-pane fade" id="live-consultation-project" role="tabpanel"
                                            aria-labelledby="live-consultation-project-tab">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>Live Consultation</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div
                                                        class="dt-ext table-responsive custom-scrollbar html-expert-table">
                                                        <table id="live-consultation-table"
                                                            class="display table-striped w-100">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Consultation Title</th>
                                                                    <th>Date</th>
                                                                    <th>Created By</th>
                                                                    <th>Created For</th>
                                                                    <th>Patient</th>
                                                                    <th>Status</th>
                                                                    <th class="text-end">Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="patient-prescription-list-body">
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="consolidated" role="tabpanel"
                                            aria-labelledby="consolidated-tab">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>User Logs</h5>
                                                </div>
                                                <div class="card-body">
                                                    <h4 class="mb-3 text-info">Vitals</h4>
                                                    <div class="table-responsive custom-scrollbar mb-4">
                                                        <table class="table border-bottom-table">
                                                            <thead>
                                                                <tr class="border-bottom-primary">
                                                                    <th>SN.</th>
                                                                    <th>Date</th>
                                                                    <th>Systolic BP (mmhg)</th>
                                                                    <th>Diastolic BP (mmhg)</th>
                                                                    <th>Respiration</th>
                                                                    <th>Temperature (®F)</th>
                                                                    <th>Pluse (BPL)</th>
                                                                    <th>Diabetes (mmol/l)</th>
                                                                    <th>Height (Feet)</th>
                                                                    <th>Weight (Kg)</th>
                                                                    <th>BMI</th>
                                                                    <th>By</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse($vitalsVisits as $vitalVisit)
                                                                    <tr>
                                                                        <td>{{ $loop->iteration }}</td>
                                                                        <td>{{ optional($vitalVisit->appointment_date)->format('d-m-Y h:i A') }}</td>
                                                                        <td>{{ $vitalVisit->systolic_bp ?? '-' }}</td>
                                                                        <td>{{ $vitalVisit->diastolic_bp ?? '-' }}</td>
                                                                        <td>{{ $vitalVisit->respiration ?? '-' }}</td>
                                                                        <td>{{ $vitalVisit->temperature ?? '-' }}</td>
                                                                        <td>{{ $vitalVisit->pluse ?? '-' }}</td>
                                                                        <td>{{ $vitalVisit->diabetes ?? '-' }}</td>
                                                                        <td>{{ $vitalVisit->height ?? '-' }}</td>
                                                                        <td>{{ $vitalVisit->weight ?? '-' }}</td>
                                                                        <td>{{ $vitalVisit->bmi ?? '-' }}</td>
                                                                        <td>{{ $vitalVisit->consultant?->full_name ?? '-' }}</td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="12" class="text-center">No Data Found.</td>
                                                                    </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <h4 class="mb-3 text-info">Prescription</h4>
                                                    <div class="table-responsive custom-scrollbar mb-4">
                                                        <table class="table border-bottom-table">
                                                            <thead>
                                                                <tr class="border-bottom-primary">
                                                                    <th scope="col">SN.</th>
                                                                    <th scope="col">Date</th>
                                                                    <th scope="col">OPD</th>
                                                                    <th scope="col">Consultant</th>
                                                                    <th scope="col">Reference</th>
                                                                    <th scope="col">Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse($prescriptionVisits as $prescriptionVisit)
                                                                    <tr>
                                                                        <td>{{ $loop->iteration }}</td>
                                                                        <td>{{ optional($prescriptionVisit->created_at)->format('d-m-Y h:i A') }}</td>
                                                                        <td>{{ $prescriptionVisit->opdPatient?->case_no ?? '-' }}</td>
                                                                        <td>{{ $prescriptionVisit->doctor?->full_name ?? '-' }}</td>
                                                                        <td>{{ $prescriptionVisit->opdPatient?->tpa_reference_no ?? '-' }}</td>
                                                                        <td>
                                                                            <ul class="action mb-0">
                                                                                <li class="view" data-bs-toggle="tooltip" title="View Prescription">
                                                                                    <a href="#" class="open-prescription-view" data-view-url="{{ route('hospital.opd-patient.prescription.view', ['opdPatient' => $prescriptionVisit->opd_patient_id]) }}">
                                                                                        <i class="text-primary fa-solid fa-eye"></i>
                                                                                    </a>
                                                                                </li>
                                                                                <li class="view" data-bs-toggle="tooltip" title="Print Prescription">
                                                                                    <a href="{{ route('hospital.opd-patient.prescription.print', ['opdPatient' => $prescriptionVisit->opd_patient_id]) }}" target="_blank">
                                                                                        <i class="text-success fa-solid fa-print"></i>
                                                                                    </a>
                                                                                </li>
                                                                            </ul>
                                                                        </td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="6" class="text-center">No prescription data found.</td>
                                                                    </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <h4 class="mb-3 text-info">Pathology</h4>
                                                    <div class="table-responsive custom-scrollbar mb-4">
                                                        <table class="table border-bottom-table">
                                                            <thead>
                                                                <tr class="border-bottom-primary">
                                                                    <th scope="col">SN.</th>
                                                                    <th scope="col">Date</th>
                                                                    <th scope="col">OPD</th>
                                                                    <th scope="col">Consultant</th>
                                                                    <th scope="col">Test Report</th>
                                                                    <th scope="col">Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse($pathologyVisits as $pathologyVisit)
                                                                    @php
                                                                        $linkedVisit = $visitsById->get($pathologyVisit->order->visitable_id);
                                                                        $pathologyStatusKey = strtolower(str_replace([' ', '-'], '_', (string) $pathologyVisit->status));
                                                                        $canDeletePathology = !in_array($pathologyStatusKey, ['in_progress', 'completed'], true);
                                                                        $canPrintPathology = $pathologyStatusKey === 'completed';
                                                                    @endphp
                                                                    <tr>
                                                                        <td>{{ $loop->iteration }}</td>
                                                                        <td>{{ optional($pathologyVisit->created_at)->format('d-m-Y h:i A') }}</td>
                                                                        <td>{{ $linkedVisit?->case_no ?? '-' }}</td>
                                                                        <td>{{ $linkedVisit?->consultant?->full_name ?? '-' }}</td>
                                                                        <td>
                                                                            <div>{{ $pathologyVisit->test_name }}</div>
                                                                            @php
                                                                                $pathologyBadgeClass = $pathologyStatusKey === 'completed'
                                                                                    ? 'bg-success'
                                                                                    : ($pathologyStatusKey === 'in_progress'
                                                                                        ? 'bg-warning text-dark'
                                                                                        : (in_array($pathologyStatusKey, ['cancelled', 'rejected'], true) ? 'bg-danger' : 'bg-secondary'));
                                                                            @endphp
                                                                            <small class="text-muted d-flex align-items-center gap-1 flex-wrap">
                                                                                <span class="badge {{ $pathologyBadgeClass }}">{{ ucfirst(str_replace('_', ' ', $pathologyStatusKey)) }}</span>
                                                                                <span>| {{ $pathologyVisit->order->order_no ?? '-' }}</span>
                                                                            </small>
                                                                        </td>
                                                                        <td>
                                                                            @can('view-pathology-report')
                                                                                @if($canPrintPathology)
                                                                                    <a href="{{ route('hospital.pathology.worklist.print', ['item' => $pathologyVisit->id]) }}" target="_blank" class="me-2" data-bs-toggle="tooltip" title="Print Pathology Report">
                                                                                        <i class="text-success fa-solid fa-print"></i>
                                                                                    </a>
                                                                                @endif
                                                                            @endcan
                                                                            @can('delete-pathology-order')
                                                                                @if($canDeletePathology)
                                                                                    <a href="javascript:;" class="delete-diagnostic-item" data-delete-url="{{ route('hospital.opd-patient.diagnostics.destroy', ['opdPatient' => $pathologyVisit->order->visitable_id, 'item' => $pathologyVisit->id]) }}" data-title="Delete Pathology Test" data-test-name="{{ $pathologyVisit->test_name }}">
                                                                                        <i class="text-danger fa-solid fa-trash-can"></i>
                                                                                    </a>
                                                                                @else
                                                                                    <span class="text-muted" data-bs-toggle="tooltip" title="In-progress or completed test cannot be deleted.">
                                                                                        <i class="fa-solid fa-lock"></i>
                                                                                    </span>
                                                                                @endif
                                                                            @endcan
                                                                        </td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="6" class="text-center">No pathology data found.</td>
                                                                    </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <h4 class="mb-3 text-info">Radiology</h4>
                                                    <div class="table-responsive custom-scrollbar mb-4">
                                                        <table class="table border-bottom-table">
                                                            <thead>
                                                                <tr class="border-bottom-primary">
                                                                    <th scope="col">SN.</th>
                                                                    <th scope="col">Date</th>
                                                                    <th scope="col">OPD</th>
                                                                    <th scope="col">Consultant</th>
                                                                    <th scope="col">Test Report</th>
                                                                    <th scope="col">Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse($radiologyVisits as $radiologyVisit)
                                                                    @php
                                                                        $linkedVisit = $visitsById->get($radiologyVisit->order->visitable_id);
                                                                        $radiologyStatusKey = strtolower(str_replace([' ', '-'], '_', (string) $radiologyVisit->status));
                                                                        $canDeleteRadiology = !in_array($radiologyStatusKey, ['in_progress', 'completed'], true);
                                                                        $canPrintRadiology = $radiologyStatusKey === 'completed';
                                                                    @endphp
                                                                    <tr>
                                                                        <td>{{ $loop->iteration }}</td>
                                                                        <td>{{ optional($radiologyVisit->created_at)->format('d-m-Y h:i A') }}</td>
                                                                        <td>{{ $linkedVisit?->case_no ?? '-' }}</td>
                                                                        <td>{{ $linkedVisit?->consultant?->full_name ?? '-' }}</td>
                                                                        <td>
                                                                            <div>{{ $radiologyVisit->test_name }}</div>
                                                                            @php
                                                                                $radiologyBadgeClass = $radiologyStatusKey === 'completed'
                                                                                    ? 'bg-success'
                                                                                    : ($radiologyStatusKey === 'in_progress'
                                                                                        ? 'bg-warning text-dark'
                                                                                        : (in_array($radiologyStatusKey, ['cancelled', 'rejected'], true) ? 'bg-danger' : 'bg-secondary'));
                                                                            @endphp
                                                                            <small class="text-muted d-flex align-items-center gap-1 flex-wrap">
                                                                                <span class="badge {{ $radiologyBadgeClass }}">{{ ucfirst(str_replace('_', ' ', $radiologyStatusKey)) }}</span>
                                                                                <span>| {{ $radiologyVisit->order->order_no ?? '-' }}</span>
                                                                            </small>
                                                                        </td>
                                                                        <td>
                                                                            @can('view-radiology-report')
                                                                                @if($canPrintRadiology)
                                                                                    <a href="{{ route('hospital.radiology.worklist.print', ['item' => $radiologyVisit->id]) }}" target="_blank" class="me-2" data-bs-toggle="tooltip" title="Print Radiology Report">
                                                                                        <i class="text-success fa-solid fa-print"></i>
                                                                                    </a>
                                                                                @endif
                                                                            @endcan
                                                                            @can('delete-radiology-order')
                                                                                @if($canDeleteRadiology)
                                                                                    <a href="javascript:;" class="delete-diagnostic-item" data-delete-url="{{ route('hospital.opd-patient.diagnostics.destroy', ['opdPatient' => $radiologyVisit->order->visitable_id, 'item' => $radiologyVisit->id]) }}" data-title="Delete Radiology Test" data-test-name="{{ $radiologyVisit->test_name }}">
                                                                                        <i class="text-danger fa-solid fa-trash-can"></i>
                                                                                    </a>
                                                                                @else
                                                                                    <span class="text-muted" data-bs-toggle="tooltip" title="In-progress or completed test cannot be deleted.">
                                                                                        <i class="fa-solid fa-lock"></i>
                                                                                    </span>
                                                                                @endif
                                                                            @endcan
                                                                        </td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="6" class="text-center">No radiology data found.</td>
                                                                    </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="charges" role="tabpanel" aria-labelledby="charges-tab">
                                            <div class="card">
                                                <div class="card-header d-flex justify-content-between align-items-center">
                                                    <h5 class="mb-0">Patient Charges</h5>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-success open-charge-payment-form" data-url="{{ route('hospital.opd-patient.charges.show-payment-form', ['patient' => $patient->id]) }}">Payment / Discount</button>
                                                        <button type="button" class="btn btn-warning open-charge-payment-form" data-url="{{ route('hospital.opd-patient.charges.show-refund-form', ['patient' => $patient->id]) }}">Refund Advance</button>
                                                        <a href="{{ route('hospital.opd-patient.charges.final-bill.print', ['patient' => $patient->id]) }}" target="_blank" class="btn btn-primary">Print Final Bill</a>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-2 mb-3">
                                                        <div class="col-md-3">
                                                            <div class="alert alert-light border mb-0"><strong>Total Charges:</strong> {{ number_format($totalCharges, 2) }}</div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="alert alert-light border mb-0"><strong>Paid (Adjusted):</strong> {{ number_format($totalPaid, 2) }}</div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="alert alert-light border mb-0"><strong>Total Due:</strong> {{ number_format($totalDue, 2) }}</div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="alert alert-warning border mb-0"><strong>Advance/Credit:</strong> {{ number_format((float) ($advanceCredit ?? 0), 2) }}</div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="alert alert-light border mb-0"><strong>Discount:</strong> {{ number_format((float) ($totalDiscount ?? 0), 2) }}</div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="alert alert-light border mb-0"><strong>Tax:</strong> {{ number_format((float) ($totalTax ?? 0), 2) }}</div>
                                                        </div>
                                                    </div>

                                                    <ul class="nav nav-tabs mb-3" id="charges-ledger-tabs" role="tablist">
                                                        <li class="nav-item" role="presentation">
                                                            <button class="nav-link active" id="charges-ledger-tab" data-bs-toggle="tab" data-bs-target="#charges-ledger-panel" type="button" role="tab" aria-controls="charges-ledger-panel" aria-selected="true">Charge Ledger</button>
                                                        </li>
                                                        <li class="nav-item" role="presentation">
                                                            <button class="nav-link" id="payments-ledger-tab" data-bs-toggle="tab" data-bs-target="#payments-ledger-panel" type="button" role="tab" aria-controls="payments-ledger-panel" aria-selected="false">Payment Ledger</button>
                                                        </li>
                                                    </ul>

                                                    <div class="tab-content" id="charges-ledger-tab-content">
                                                        <div class="tab-pane fade show active" id="charges-ledger-panel" role="tabpanel" aria-labelledby="charges-ledger-tab">
                                                            <div class="table-responsive custom-scrollbar mb-1">
                                                                <table id="charges-ledger-table" class="table border-bottom-table w-100">
                                                                    <thead>
                                                                        <tr class="border-bottom-primary">
                                                                            <th>SN.</th>
                                                                            <th>Date</th>
                                                                            <th>Module</th>
                                                                            <th>Code</th>
                                                                            <th>Particular</th>
                                                                            <th>Amount</th>
                                                                            <th>Paid</th>
                                                                            <th>Due</th>
                                                                            <th>Payer</th>
                                                                            <th>Status</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @forelse($patientCharges as $charge)
                                                                            <tr>
                                                                                <td>{{ $loop->iteration }}</td>
                                                                                <td>{{ optional($charge->charged_at)->format('d-m-Y h:i A') ?? optional($charge->created_at)->format('d-m-Y h:i A') }}</td>
                                                                                <td>{{ strtoupper($charge->module ?? $charge->charge_category ?? '-') }}</td>
                                                                                <td>{{ $charge->charge_code ?? '-' }}</td>
                                                                                <td>{{ $charge->particular }}</td>
                                                                                <td>{{ number_format((float) $charge->amount, 2) }}</td>
                                                                                <td>{{ number_format((float) $charge->paid_amount, 2) }}</td>
                                                                                <td>{{ number_format(max(0, (float) $charge->amount - (float) $charge->paid_amount), 2) }}</td>
                                                                                <td>{{ strtoupper($charge->payer_type ?? 'self') }}</td>
                                                                                <td>
                                                                                    @php
                                                                                        $statusKey = strtolower((string) ($charge->payment_status ?? 'unpaid'));
                                                                                        $statusClass = $statusKey === 'paid'
                                                                                            ? 'bg-success'
                                                                                            : ($statusKey === 'partial' ? 'bg-warning text-dark' : 'bg-danger');
                                                                                    @endphp
                                                                                    <span class="badge {{ $statusClass }}">{{ ucfirst($statusKey) }}</span>
                                                                                </td>
                                                                            </tr>
                                                                        @empty
                                                                            <tr>
                                                                                <td colspan="10" class="text-center">No charge data found.</td>
                                                                            </tr>
                                                                        @endforelse
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>

                                                        <div class="tab-pane fade" id="payments-ledger-panel" role="tabpanel" aria-labelledby="payments-ledger-tab">
                                                            <div class="table-responsive custom-scrollbar mb-1">
                                                                <table id="payments-ledger-table" class="table border-bottom-table w-100">
                                                                    <thead>
                                                                        <tr class="border-bottom-primary">
                                                                            <th>SN.</th>
                                                                            <th>Date</th>
                                                                            <th>Type</th>
                                                                            <th>Mode</th>
                                                                            <th>Reference</th>
                                                                            <th>Amount</th>
                                                                            @can('delete-opd-payment')
                                                                                <th>Action</th>
                                                                            @endcan
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @forelse($patientPayments as $payment)
                                                                            @php
                                                                                $isRefund = (float) $payment->amount < 0;
                                                                                $displayAmount = abs((float) $payment->amount);
                                                                            @endphp
                                                                            <tr>
                                                                                <td>{{ $loop->iteration }}</td>
                                                                                <td>{{ optional($payment->paid_at)->format('d-m-Y h:i A') ?? optional($payment->created_at)->format('d-m-Y h:i A') }}</td>
                                                                                <td>
                                                                                    @if($isRefund)
                                                                                        <span class="badge bg-warning text-dark">REFUND</span>
                                                                                    @else
                                                                                        <span class="badge bg-success">PAYMENT</span>
                                                                                    @endif
                                                                                </td>
                                                                                <td>{{ $payment->payment_mode ?? '-' }}</td>
                                                                                <td>{{ $payment->reference ?? '-' }}</td>
                                                                                <td class="{{ $isRefund ? 'text-danger' : 'text-success' }}">{{ $isRefund ? '-' : '' }}{{ number_format($displayAmount, 2) }}</td>
                                                                                @can('delete-opd-payment')
                                                                                    <td>
                                                                                        <a href="javascript:;" class="delete-patient-payment" data-delete-url="{{ route('hospital.opd-patient.charges.payments.destroy', ['patient' => $patient->id, 'payment' => $payment->id]) }}" data-amount="{{ number_format((float) $payment->amount, 2) }}" data-bs-toggle="tooltip" title="Delete Payment">
                                                                                            <i class="text-danger fa-solid fa-trash-can"></i>
                                                                                        </a>
                                                                                    </td>
                                                                                @endcan
                                                                            </tr>
                                                                        @empty
                                                                            <tr>
                                                                                <td colspan="{{ auth()->user()->can('delete-opd-payment') ? 7 : 6 }}" class="text-center">No payment data found.</td>
                                                                            </tr>
                                                                        @endforelse
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('hospital.opd-patient.partials.vitals_social_history')

    <div class="modal fade" id="opdPrescriptionModal" tabindex="-1" aria-hidden="true" data-bs-focus="false">
        <div class="modal-dialog modal-xl">
            <div class="modal-content" id="opdPrescriptionContent"></div>
        </div>
    </div>

    <div class="modal fade" id="opdVisitSummaryModal" tabindex="-1" aria-hidden="true" data-bs-focus="false">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content" id="opdVisitSummaryContent"></div>
        </div>
    </div>

    <div class="modal fade" id="diagnosticOrderModal" tabindex="-1" aria-hidden="true" data-bs-focus="false">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content" id="diagnosticOrderContent"></div>
        </div>
    </div>

    <div class="modal fade" id="chargePaymentModal" tabindex="-1" aria-hidden="true" data-bs-focus="false">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content" id="chargePaymentContent"></div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <style>
        .vital-box {
            border-radius: 12px;
            padding: 18px 20px 10px 20px;
            margin-bottom: 18px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            background: #fff;
            min-width: 180px;
            min-height: 90px;
            font-weight: 500;
            font-size: 1.1rem;
        }

        .vital-orange {
            background: linear-gradient(90deg, #fff7e6 0%, #fbb040 100%);
        }

        .vital-red {
            background: linear-gradient(90deg, #fff0f0 0%, #f77c7c 100%);
        }

        .vital-green {
            background: linear-gradient(90deg, #f0fff0 0%, #7cf77c 100%);
        }

        .vital-blue {
            background: linear-gradient(90deg, #e6f0ff 0%, #40a1fb 100%);
        }

        .vital-input {
            font-size: 1.2rem;
            font-weight: 600;
            border: none;
            background: transparent;
            border-bottom: 1px dashed #bbb;
            border-radius: 0;
            text-align: right;
        }

        .vital-input:focus {
            outline: none;
            box-shadow: none;
            border-color: #007bff;
            background: #fff;
        }

        .vital-box input.form-control.vital-input {
            color: #00000085;
            background: transparent;
            border: none;
            border-bottom: 2px dashed;
            display: inline-block;
            flex: 1;
            border-radius: 0;
        }

        .human-body {
            width: 207px;
            position: relative;
            padding-top: 500px;
            height: 500px;
            display: block;
            margin: 20px auto;
        }

        .human-body svg:hover {
            cursor: pointer;
        }

        .human-body svg:hover path {
            fill: #ff7d16;
        }

        .human-body svg {
            position: absolute;
            left: 50%;
            fill: #57c9d5;
        }

        .human-body svg.head {
            margin-left: -28.5px;
            top: -6px;
        }

        .human-body svg.shoulder {
            margin-left: -53.5px;
            top: 69px;
        }

        .human-body svg.arm {
            margin-left: -78px;
            top: 112px;
        }

        .human-body svg.cheast {
            margin-left: -43.5px;
            top: 88px;
        }

        .human-body svg.stomach {
            margin-left: -37.5px;
            top: 130px;
        }

        .human-body svg.legs {
            margin-left: -46.5px;
            top: 205px;
            z-index: 9999;
        }

        .human-body svg.hands {
            margin-left: -102.5px;
            top: 224px;
        }

        #area {
            display: block;
            width: 100%;
            clear: both;
            padding: 10px;
            text-align: center;
            font-size: 25px;
            font-family: Courier New;
            color: #a5a5a5;
        }

        #area #data {
            color: black;
        }
    </style>
@endpush
@push('styles')
@include('layouts.partials.datatable-css')
@include('layouts.partials.flatpickr-css')
@endpush
@push('scripts')
@include('layouts.partials.datatable-js')
@include('layouts.partials.flatpickr-js')
    <script src="{{ asset('public/modules/sa/opd-care-shared.js') }}"></script>
    <script src="{{ asset('public/front/assets/js/editor/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('public/modules/sa/opd-visits.js') }}"></script>
@endpush