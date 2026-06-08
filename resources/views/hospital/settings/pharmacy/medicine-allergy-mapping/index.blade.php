@extends('layouts.hospital.app')
@section('title','Medicine Allergy Mappings')
@section('page_header_icon', '🛡️')
@section('page_subtitle', 'Manage Medicine Allergy Contraindications')
@section('page_header_actions')
@can('create-medicine')
    <button class="btn btn-info adddata" data-id="">+ Add Allergy Mapping</button>
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
                        <table id="xin-table" class="display table-striped text-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Medicine</th>
                                    <th>Allergy / Allergen Class</th>
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
