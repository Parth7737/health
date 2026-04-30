@extends('layouts.hospital.app')
@section('title','Doctor OPD Charges')
@section('page_header_icon', '🩺')
@section('page_subtitle', 'Manage Doctor OPD Charges')
@section('page_header_actions')
@can('create-doctor-opd-charges')
    <button class="btn btn-info adddata" data-id="">+ Add Doctor Charge</button>
@endcan
@endsection
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-xl-3">
          @include('hospital.charges.submenu')
        </div>
        <div class="col-xl-9">
            <div class="card">
                <div class="card-body">
                    <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                        <table id="xin-table" class="display table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Doctor</th>
                                    <th>New Case Charge</th>
                                    <th>Follow-up Charge</th>
                                    <th>Emergency Charge</th>
                                    <th>Follow-up Window</th>
                                    <th>TPA Charges Set</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
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
