@extends('layouts.hospital.app')
@section('title','Department Units')
@section('page_header_icon', '🏢')
@section('page_subtitle', 'Manage Department Units')
@section('page_header_actions')
@can('create-hr-department-unit')
    <button class="btn btn-info adddata" data-id="">+ Add Unit</button>
@endcan
@endsection
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-xl-3">
          @include('hospital.settings.hr.submenu')
        </div>
        <div class="col-xl-9">
            <div class="card">
                <div class="card-body">
                    <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                        <table id="xin-table" class="display table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Floor</th>
                                    <th>Unit Incharge</th>
                                    <th>Video Consultation</th>
                                    <th>Daily Capacity</th>
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