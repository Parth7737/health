@extends('layouts.hospital.app')
@section('title','Pharmacy Sale Bills')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3" style="gap: 1rem;">
        <h3 class="mb-0">Pharmacy Sale Bills</h3>
        @can('create-pharmacy-sale')
            <button class="btn btn-info btn-sm adddata">+ New Bill</button>
        @endcan
    </div>
</div>
<div class="container-fluid mb-3">
    @include('hospital.pharmacy.partials.nav')
</div>
<div class="container-fluid">
    <div class="row g-2 mb-3">
        <div class="col-md-6 col-lg-3">
            <div class="card border-start border-3 border-primary h-100">
                <div class="card-body py-2 px-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <small class="text-muted d-block">Today Sales</small>
                            <h5 class="mb-0">{{ $saleStats['today_sales_count'] }}</h5>
                        </div>
                        <i class="fas fa-shopping-cart text-primary opacity-50"></i>
                    </div>
                    <small class="text-success d-block mt-1">Rs {{ number_format((float) $saleStats['today_net_total'], 2) }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-start border-3 border-success h-100">
                <div class="card-body py-2 px-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <small class="text-muted d-block">From Prescriptions</small>
                            <h5 class="mb-0">{{ $saleStats['today_prescription_sales'] }}</h5>
                        </div>
                        <i class="fas fa-prescription-bottle text-success opacity-50"></i>
                    </div>
                    <small class="text-muted d-block mt-1">Walk-in: {{ $saleStats['today_walk_in_sales'] }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-start border-3 border-warning h-100">
                <div class="card-body py-2 px-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <small class="text-muted d-block">Today Due</small>
                            <h5 class="mb-0">Rs {{ number_format((float) $saleStats['today_due_total'], 2) }}</h5>
                        </div>
                        <i class="fas fa-clock text-warning opacity-50"></i>
                    </div>
                    <small class="text-muted d-block mt-1">For collection</small>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-start border-3 border-danger h-100">
                <div class="card-body py-2 px-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <small class="text-muted d-block">Pending Due</small>
                            <h5 class="mb-0">Rs {{ number_format((float) $saleStats['pending_due_total'], 2) }}</h5>
                        </div>
                        <i class="fas fa-exclamation-circle text-danger opacity-50"></i>
                    </div>
                    <small class="text-muted d-block mt-1">(All pending)</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body p-3">
            <div class="row g-3 quick-bill-row">
                <div class="col-md-3 col-lg-2 quick-col">
                    <label class="form-label small fw-bold quick-label">Quick Bill Type</label>
                    <select class="form-select quick-control" id="quick_prescription_type">
                        <option value="">Select Type</option>
                        <option value="opd">OPD Prescription</option>
                        <option value="ipd">IPD Prescription</option>
                    </select>
                </div>
                <div class="col-md-6 col-lg-7 quick-col">
                    <label class="form-label small fw-bold quick-label">Find & Select Prescription</label>
                    <select class="form-select quick-control" id="quick_prescription_id">
                        <option value="">Search prescription...</option>
                    </select>
                </div>
                <div class="col-md-3 col-lg-3 quick-col">
                    <label class="form-label small fw-bold quick-label d-block invisible">x</label>
                    @can('create-pharmacy-sale')
                        <button class="btn btn-success w-100 quick-sale-open">
                            Quick Bill
                        </button>
                    @endcan
                </div>
            </div>
        </div>
    </div>

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
                                <th>Patient</th>
                                <th>Subtotal</th>
                                <th>Discount</th>
                                <th>Tax</th>
                                <th>Net Total</th>
                                <th>Paid</th>
                                <th>Due</th>
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
@include('layouts.partials.flatpickr-css')
@endpush
@push('scripts')
@include('layouts.partials.datatable-js')
@include('layouts.partials.flatpickr-js')
<script>
window.quickOpdPrescriptionOptions = @json($quickOpdPrescriptions);
window.quickIpdPrescriptionOptions = @json($quickIpdPrescriptions);
</script>
@endpush