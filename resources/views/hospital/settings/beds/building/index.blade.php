@extends('layouts.hospital.app')
@section('title', 'Buildings')
@section('page_header_icon', '🏢')
@section('page_subtitle', 'Manage Buildings')
@section('page_header_actions')
@can('create-building')
    <button class="btn btn-info adddata" data-id="">+ Add Building</button>
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
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Floors</th>
                                    <th>Description</th>
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
<script src="{{ asset('modules/sa/building.js') }}"></script>
@include('layouts.partials.datatable-js')
@endpush
