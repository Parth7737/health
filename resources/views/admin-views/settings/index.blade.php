@extends('layouts.admin.app')
@section('title','Settings')
@section('content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
            <div class="col-sm-6">
                <h3>Settings</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('admin.dashboard.index')}}">
                    <svg class="stroke-icon">
                        <use href="{{asset('public/front/assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                    </svg></a></li>
                <li class="breadcrumb-item active">Settings</li>
                </ol>
            </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid starts-->
    <div class="container-fluid main-setting">
        <div class="row">
            <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Master Settings</h5>
                </div>
                <div class="card-body">
                    <form method="POST" id="savedata" method="POST" enctype="multipart/form-data">
                    <div class="row g-lg-3 g-4">
                        <div class="col-lg-3 col-12">
                            <div class="nav flex-lg-column nav-pills nav-primary" id="ver-pills-tab" role="tablist" aria-orientation="vertical">
                                <a class="nav-link active" id="ver-pills-general-tab" data-bs-toggle="pill" href="#ver-pills-general">
                                <svg class="stroke-icon">
                                    <use href="{{asset('public/front/assets/svg/icon-sprite.svg#general-setting')}}"></use>
                                </svg>General</a>

                                <a class="nav-link" id="ver-pills-empanelment-tab" data-bs-toggle="pill" href="#ver-pills-empanelment">
                                <svg class="stroke-icon">
                                    <use href="{{asset('public/front/assets/svg/icon-sprite.svg#stroke-form')}}"></use>
                                </svg>Empanelment Settings</a>

                                <a class="nav-link" id="ver-pills-payment-tab" data-bs-toggle="pill" href="#ver-pills-payment">
                                <svg class="stroke-icon">
                                    <use href="{{asset('public/front/assets/svg/icon-sprite.svg#setting-payment')}}"></use>
                                </svg>Payment Method</a>
                            </div>
                        </div>
                        <div class="col-lg-9 col-12">
                            <div class="tab-content" id="ver-pills-tabContent">
                                <div class="tab-pane fade show active" id="ver-pills-general">

                                    <div class="row">
                                        <label class="col-md-3 form-label">Site Title</label>
                                        <div class="col-md-9">
                                            <input class="form-control" type="text" id="site_title" value="{{\App\CentralLogics\Helpers::get_settings('site_title')}}" placeholder="Site title" name="site_title" required="">
                                        </div>
                                    </div>                                    

                                    <div class="row">
                                        <label class="col-md-3 form-label">Logo</label>
                                        <div class="col-md-9">
                                            <img src="{{ asset('public/storage/' . \App\CentralLogics\Helpers::get_settings('front_logo')) }}" class="w-50 img img-responsive img-thumbnail" alt="">
                                            <input class="form-control" type="file" id="front_logo" name="front_logo">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-md-3 form-label">Background Image</label>
                                        <div class="col-md-9">
                                            <img src="{{ asset('public/storage/' . \App\CentralLogics\Helpers::get_settings('background_image')) }}" class="w-50 img img-responsive img-thumbnail" alt="">
                                            <input class="form-control" type="file" id="background_image" name="background_image">
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade show" id="ver-pills-empanelment">
                                    
                                    <div class="row">
                                        <label class="col-md-3 form-label">Hospital Registration Fee</label>
                                        <div class="col-md-9">
                                            <div class="input-group">
                                                <input class="form-control" type="number" id="registration_fee" name="registration_fee" value="{{\App\CentralLogics\Helpers::get_settings('registration_fee')}}" placeholder="Enter amount">
                                            </div>
                                        </div>
                                    </div>
                                    @php
                                        $empanelment_step_status = json_decode(\App\CentralLogics\Helpers::get_settings('empanelment_step_status'));
                                    @endphp
                                    <div class="row">
                                        <div class="col-md-3 col-auto">
                                            <label class="form-label mb-0">Specialities Step</label>
                                        </div>
                                        <div class="col-md-9 col-auto">
                                            <x-form.toggle-switch 
                                                name="speciality_status" 
                                                value="1" 
                                                id="speciality_status" 
                                                :checked="$empanelment_step_status && $empanelment_step_status->speciality_status == 1" 
                                            />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3 col-auto">
                                            <label class="form-label mb-0">Services Step</label>
                                        </div>
                                        <div class="col-md-9 col-auto">
                                            <x-form.toggle-switch 
                                                name="service_status" 
                                                value="1" 
                                                id="service_status" 
                                                :checked="$empanelment_step_status && $empanelment_step_status->service_status == 1" 
                                            />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3 col-auto">
                                            <label class="form-label mb-0">Licenses Step</label>
                                        </div>
                                        <div class="col-md-9 col-auto">
                                            <x-form.toggle-switch 
                                                name="licenses_status" 
                                                value="1" 
                                                id="licenses_status" 
                                                :checked="$empanelment_step_status && $empanelment_step_status->licenses_status == 1" 
                                            />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="tab-pane fade" id="ver-pills-payment">
                                    <div class="advance-options">
                                        <ul class="nav nav-tabs border-tab" id="advance-option-tab" role="tablist">
                                            <li class="nav-item"><a class="nav-link active" id="ccavenue-tab" data-bs-toggle="tab" href="#ccavenue">CCAvenue</a></li>
                                        </ul>
                                        <div class="tab-content" id="advance-option-tabContent">
                                            @php
                                                $ccavenue = json_decode(\App\CentralLogics\Helpers::get_settings('ccavenue'));
                                            @endphp
                                            <div class="tab-pane fade show active" id="ccavenue">
                                                <div class="row">
                                                    <div class="col-md-3 col-auto">
                                                        <label class="form-label mb-0">Status</label>
                                                    </div>
                                                    <div class="col-md-9 col-auto">
                                                        <x-form.toggle-switch 
                                                            name="ccavenue_status" 
                                                            value="1" 
                                                            id="ccavenue_status" 
                                                            :checked="$ccavenue && $ccavenue->status == 1" 
                                                        />
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <label class="col-md-3 form-label">Client ID</label>
                                                    <div class="col-md-9">
                                                        <input class="form-control" name="client_id" id="client_id" value="{{$ccavenue ? $ccavenue->client_id : ''}}" type="text" placeholder="Enter client Id">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <label class="col-md-3 form-label">Secret</label>
                                                    <div class="col-md-9">
                                                        <input class="form-control" type="text" name="secret_id" id="secret_id" value="{{$ccavenue ? $ccavenue->client_id : ''}}" placeholder="Enter secret">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary ms-auto d-block">Save</button>
                    </form>
                </div>
            </div>
            </div>
        </div>
    </div>
@endsection
@push('styles')
  @include('layouts.partials.datatable-css')
  @include('layouts.partials.flatpickr-css')
@endpush
@push('scripts')
  @include('layouts.partials.datatable-js')
  @include('layouts.partials.flatpickr-js')
@endpush