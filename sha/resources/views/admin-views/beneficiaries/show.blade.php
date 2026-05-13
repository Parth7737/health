@extends('layouts.admin.app')

@section('title', 'Beneficiary Details')

@section('content')
<div class="container mt-4">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-user-circle me-2"></i>Beneficiary Details</h4>
            <a href="{{ route('admin.beneficiaries.index') }}" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>
        </div>
        <div class="card-body">
            <!-- Personal Information -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Personal Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-user me-1"></i>Name:</strong> {{ $beneficiary->name }}</p>
                            <p><strong><i class="fas fa-male me-1"></i>Father's Name:</strong> {{ $beneficiary->father_name }}</p>
                            <p><strong><i class="fas fa-venus-mars me-1"></i>Gender:</strong> {{ $beneficiary->gender }}</p>
                            <p><strong><i class="fas fa-birthday-cake me-1"></i>Year of Birth:</strong> {{ $beneficiary->year_of_birth }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-mobile-alt me-1"></i>Mobile No:</strong> {{ $beneficiary->mobile_no }}</p>
                            <p><strong><i class="fas fa-envelope me-1"></i>Email:</strong> {{ $beneficiary->ben_email_id }}</p>
                            <p><strong><i class="fas fa-calendar-alt me-1"></i>Age:</strong> {{ $beneficiary->age }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Details -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Address Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-flag me-1"></i>State:</strong> {{ $beneficiary->state_name }} ({{ $beneficiary->state_cd }})</p>
                            <p><strong><i class="fas fa-map-pin me-1"></i>District:</strong> {{ $beneficiary->dist_name }} ({{ $beneficiary->dist_cd }})</p>
                            <p><strong><i class="fas fa-city me-1"></i>City:</strong> {{ $beneficiary->city }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-home me-1"></i>House No:</strong> {{ $beneficiary->house_no }}</p>
                            <p><strong><i class="fas fa-map-marked-alt me-1"></i>Pincode:</strong> {{ $beneficiary->pincode }}</p>
                            <p><strong><i class="fas fa-road me-1"></i>Address:</strong> {{ $beneficiary->address }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Identification Details -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-id-badge me-2"></i>Identification Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-id-card me-1"></i>Scheme ID:</strong> {{ $beneficiary->scheme_id }}</p>
                            <p><strong><i class="fas fa-credit-card me-1"></i>Card No:</strong> {{ $beneficiary->card_id }}</p>
                            <p><strong><i class="fas fa-fingerprint me-1"></i>Abha ID:</strong> {{ $beneficiary->aabha_id }}</p>
                            <p><strong><i class="fas fa-user-tag me-1"></i>Ben ID:</strong> {{ $beneficiary->ben_id }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-users me-1"></i>Family ID:</strong> {{ $beneficiary->family_id }}</p>
                            <p><strong><i class="fas fa-user-friends me-1"></i>Member ID:</strong> {{ $beneficiary->member_id }}</p>
                            <p><strong><i class="fas fa-sitemap me-1"></i>BIS Family ID:</strong> {{ $beneficiary->bis_family_id }}</p>
                            <p><strong><i class="fas fa-sitemap me-1"></i>BIS Member ID:</strong> {{ $beneficiary->bis_member_id }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enrollment & Status -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Enrollment & Status</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-check-circle me-1"></i>Active Status:</strong> 
                                <span class="badge bg-{{ $beneficiary->active_status === 'Active' ? 'success' : 'danger' }}">
                                    {{ $beneficiary->active_status }}
                                </span>
                            </p>
                            <p><strong><i class="fas fa-clipboard-list me-1"></i>Enrollment Status:</strong> {{ $beneficiary->enrl_status }}</p>
                            <p><strong><i class="fas fa-calendar-day me-1"></i>Enrollment Date:</strong> {{ $beneficiary->enrol_date }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-calendar-check me-1"></i>Approval Date:</strong> {{ $beneficiary->approve_date }}</p>
                            <p><strong><i class="fas fa-calendar-times me-1"></i>Reject Date:</strong> {{ $beneficiary->reject_date }}</p>
                            <p><strong><i class="fas fa-birthday-cake me-1"></i>Date of Birth:</strong> {{ $beneficiary->date_of_birth }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Photo Section -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-camera me-2"></i>Photo</h5>
                </div>
                <div class="card-body text-center">
                    @if($beneficiary->photo)
                        <img src="{{ asset('storage/' . $beneficiary->photo) }}" alt="Beneficiary Photo" class="img-thumbnail rounded-circle" width="150">
                    @else
                        <p class="text-muted"><i class="fas fa-image me-1"></i>No photo available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom CSS -->
<style>
    .card-header {
        font-weight: 600;
    }
    .card-body p {
        margin-bottom: 0.75rem;
    }
    .badge {
        font-size: 0.9em;
    }
    .img-thumbnail {
        border: 2px solid #dee2e6;
    }
</style>
@endsection