@extends('layouts.hospital.app')
@section('title', 'Bed Types')
@section('page_header_icon', '🛏')
@section('page_subtitle', 'Manage Bed Types')
@section('page_header_actions')
@can('create-bed-type')
    <button class="btn btn-info adddata" data-id="">+ Add Bed Type</button>
@endcan
@endsection
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-xl-3">
            @include('hospital.settings.beds.submenu')
        </div>
        <div class="col-xl-9">
            <div class="card">
                <div class="card-body">
                    <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                        <table id="xin-table" class="display table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Type</th>
                                    <th>Charge Master</th>
                                    <th>Base Charge</th>
                                    <th>Beds</th>
                                    <th>Status</th>
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
@endpush
@push('scripts')
<script src="{{ asset('modules/sa/bed-type.js') }}"></script>
@include('layouts.partials.datatable-js')
@endpush