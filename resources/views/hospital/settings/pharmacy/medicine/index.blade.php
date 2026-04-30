@extends('layouts.hospital.app')
@section('title','Medicines')
@section('page_header_icon', '💊')
@section('page_subtitle', 'Manage Medicines')
@section('page_header_actions')
@can('create-medicine')
    <button class="btn btn-info adddata" data-id="">+ Add Medicine</button>
@endcan
@endsection
@section('content')
<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-3">
          @include('hospital.settings.pharmacy.submenu')
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
                                    <th>Category</th>
                                    <th>Generic Name</th>
                                    <th>Company</th>
                                    <th>Unit</th>
                                    <th>Min Level</th>
                                    <th>Reorder Level</th>
                                    <th>VAT</th>
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