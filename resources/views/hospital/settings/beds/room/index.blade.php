@extends('layouts.hospital.app')
@section('title', 'Rooms')
@section('page_header_icon', '🏢')
@section('page_subtitle', 'Manage Rooms')
@section('page_header_actions')
@can('create-room')
    <button class="btn btn-info adddata" data-id="">+ Add Room</button>
    <button class="btn btn-info adddata" data-id="" data-bulk="1">+ Bulk Create Rooms</button>
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
                                    <th>Room Number</th>
                                    <th>Room Code</th>
                                    <th>Ward</th>
                                    <th>Floor</th>
                                    <th>Bed Capacity</th>
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
