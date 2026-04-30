@extends('layouts.hospital.app')
@section('title','Pharmacy Purchase Bills')
@section('page_header_icon', '💊')
@section('page_subtitle', 'Manage Pharmacy Purchase Bills')
@section('page_header_actions')
@can('create-pharmacy-purchase')
    <button class="btn btn-info btn-sm adddata" data-id="">+ New Purchase Bill</button>
@endcan
@endsection
@section('content')
<div class="container-fluid">
    @include('hospital.pharmacy.partials.nav')
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                        <table id="xin-table" class="display table-striped">
                            <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Bill No</th>
                                <th>Date</th>
                                <th>Supplier</th>
                                <th>Subtotal</th>
                                <th>Discount</th>
                                <th>Tax</th>
                                <th>Net Total</th>
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