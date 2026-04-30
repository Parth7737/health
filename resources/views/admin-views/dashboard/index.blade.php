@extends('layouts.admin.app')
@section('title', \App\CentralLogics\Helpers::get_settings('site_title') != "" ? \App\CentralLogics\Helpers::get_settings('site_title') : 'Dashboard')
@section('content')
<div class="container-fluid">
  <div class="page-title">
    <div class="row">
      <div class="col-sm-6">
        <!-- <h3>Sakhuja Hospital</h3> -->
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">
              <svg class="stroke-icon">
                <use href="{{asset('public/front/assets/svg/icon-sprite.svg#stroke-home')}}"></use>
              </svg></a></li>
          <li class="breadcrumb-item">Dashboard</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<!-- Department List Grid View -->
<div class="container-fluid mt-4">
  <div class="row g-3">
    <div class="col-lg-3 col-md-4 col-sm-6">
      <div class="card d-flex flex-row align-items-center p-2 shadow-sm h-100">
        <div class="icon-square bg-warning text-white d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;font-size:1.7rem;">
          <i class="fas fa-users"></i>
        </div>
        <div>
          <div class="fw-bold">Total Hospitals</div>
          <div class="fs-4 fw-bolder text-dark">{{\App\CentralLogics\Helpers::getCountAll('all')}}</div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6">
      <div class="card d-flex flex-row align-items-center p-2 shadow-sm h-100">
        <div class="icon-square bg-warning text-white d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;font-size:1.7rem;">
          <i class="fas fa-users"></i>
        </div>
        <div>
          <div class="fw-bold">Active Hospitals</div>
          <div class="fs-4 fw-bolder text-dark">{{\App\CentralLogics\Helpers::getCountAll(1)}}</div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6">
      <div class="card d-flex flex-row align-items-center p-2 shadow-sm h-100">
        <div class="icon-square bg-warning text-white d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;font-size:1.7rem;">
          <i class="fas fa-users"></i>
        </div>
        <div>
          <div class="fw-bold">Pending Hospitals</div>
          <div class="fs-4 fw-bolder text-dark">{{\App\CentralLogics\Helpers::getCountAll(0)}}</div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6">
      <div class="card d-flex flex-row align-items-center p-2 shadow-sm h-100">
        <div class="icon-square bg-warning text-white d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;font-size:1.7rem;">
          <i class="fas fa-users"></i>
        </div>
        <div>
          <div class="fw-bold">Rejected Hospitals</div>
          <div class="fs-4 fw-bolder text-dark">{{\App\CentralLogics\Helpers::getCountAll(2)}}</div>
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
  flatpickr("#inline-calender2", {
    inline: true,
    allowInput: false,
  });
</script>
@endpush