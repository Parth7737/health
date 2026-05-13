@extends('layouts.admin.app',['main_li'=>'Register Requests','sub_li'=>''])
@section('title','User Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-lg">
            <!-- Header Section -->
            <div class="card-header bg-gradient-primary text-white d-flex align-items-center">
                <div class="profile-header me-3">
                    <img src="{{ asset('public/storage/'.$user->avatar) }}" 
                         alt="{{ $user->name }}" 
                         class="rounded-circle shadow-lg border border-light" 
                         width="70" height="70">
                </div>
                <div>
                    <h4 class="mb-0">{{ $user->name }}</h4>
                    <small>{{ ucfirst($user->designation) }} | {{ ucfirst($user->entity_type) }}</small>
                </div>
            </div>

            <!-- Body Content -->
            <div class="card-body bg-light">
                <div class="row g-4">
                    <!-- Personal Information -->
                    <div class="col-md-6">
                        <div class="card shadow-sm border-0 p-3">
                            <h5 class="text-primary fw-bold"><i class="fas fa-user me-2"></i> Personal Information</h5>
                            <hr>
                            <ul class="list-unstyled">
                                <li><strong>Email:</strong> {{ $user->email }}</li>
                                <li><strong>User ID:</strong> {{ $user->userid }}</li>
                                <li><strong>Mobile No:</strong> {{ $user->mobile_no }}</li>
                                <li><strong>Gender:</strong> {{ ucfirst($user->gender) }}</li>
                                <li><strong>Age:</strong> {{ $user->age }}</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Employment Details -->
                    <div class="col-md-6">
                        <div class="card shadow-sm border-0 p-3">
                            <h5 class="text-primary fw-bold"><i class="fas fa-briefcase me-2"></i> Employment Details</h5>
                            <hr>
                            <ul class="list-unstyled">
                                <li><strong>Nature of Employment:</strong> {{ $user->nature_of_employment }}</li>
                                <li><strong>Designation:</strong> {{ $user->designation }}</li>
                                <li><strong>Entity Type:</strong> {{ $user->entity_type }}</li>
                                <li><strong>Entity Name:</strong> {{ $user->entity_name }}</li>
                                <li><strong>Parent Entity:</strong> {{ $user->parent_entity }}</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Location Details -->
                    <div class="col-md-6">
                        <div class="card shadow-sm border-0 p-3">
                            <h5 class="text-primary fw-bold"><i class="fas fa-map-marker-alt me-2"></i> Location Details</h5>
                            <hr>
                            <ul class="list-unstyled">
                                <li><strong>State:</strong> {{ $user->state }}</li>
                                <li><strong>District:</strong> {{ $user->district }}</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Registration Details -->
                    <div class="col-md-6">
                        <div class="card shadow-sm border-0 p-3">
                            <h5 class="text-primary fw-bold"><i class="fas fa-user-check me-2"></i> Registration Details</h5>
                            <hr>
                            <ul class="list-unstyled">
                                <li><strong>Register Status:</strong> {{ $user->register_status }}</li>
                                <li><strong>Is Entity Updated:</strong> {{ $user->is_entity_update ? 'Yes' : 'No' }}</li>
                                <li><strong>Is User Updated:</strong> {{ $user->is_user_update ? 'Yes' : 'No' }}</li>
                                <li><strong>Created At:</strong> {{ $user->created_at->format('d-m-Y H:i') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="text-center mt-4">
                    <a href="{{ route('admin.register-requests') }}" class="btn btn-secondary px-4">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
