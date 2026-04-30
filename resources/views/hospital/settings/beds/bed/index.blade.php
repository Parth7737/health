@extends('layouts.hospital.app')
@section('title', 'Beds')
@section('page_header_icon', '📋')
@section('page_subtitle', 'Manage Beds')
@section('page_header_actions')
@can('create-bed')
    <button class="btn btn-info adddata" data-id="">+ Add Bed</button>
    <button class="btn btn-info adddata" data-id="" data-bulk="1">+ Bulk Create Beds</button>
@endcan
<a href="{{ route('hospital.settings.beds.bed-dashboard') }}" class="btn btn-info">Bed Dashboard</a>
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
                                    <th>Bed Number</th>
                                    <th>Bed Code</th>
                                    <th>Barcode</th>
                                    <th>Ward</th>
                                    <th>Room</th>
                                    <th>Type</th>
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
@include('layouts.partials.datatable-js')
@endpush
