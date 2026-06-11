@extends('layouts.hospital.app')
@section('title', '👩‍⚕️ Nursing MAR')
@section('page_subtitle', 'Medication Administration Record — Active IPD Patients')

@section('page_header_actions')
<button class="btn btn-secondary btn-sm" type="button" onclick="loadNursingMar()">🔄 Refresh</button>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('public/css/hospital/pharmacy-dashboard.css') }}">
@include('layouts.partials.flatpickr-css')
<style>
.nursing-mar-scope { margin: 0 16px 16px; }
.mar-summary-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-bottom: 16px; }
.mar-summary-card { background: var(--surface-1); border: 1px solid var(--border-light); border-radius: 10px; padding: 12px 14px; }
.mar-summary-card .label { font-size: 11px; color: var(--text-muted); }
.mar-summary-card .value { font-size: 22px; font-weight: 800; margin-top: 4px; }
.mar-patient-card { background: var(--surface-1); border: 1px solid var(--border-light); border-radius: 12px; margin-bottom: 14px; overflow: hidden; }
.mar-patient-head { padding: 12px 16px; background: var(--surface-2); border-bottom: 1px solid var(--border-light); display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
.mar-dose-row { display: grid; grid-template-columns: 90px 1.4fr 1fr 1.2fr auto; gap: 10px; align-items: center; padding: 10px 16px; border-bottom: 1px dashed var(--border-light); }
.mar-dose-row:last-child { border-bottom: none; }
.mar-meal-badge { font-size: 10px; }
.mar-actions { display: flex; gap: 4px; flex-wrap: wrap; justify-content: flex-end; }
@media (max-width: 992px) {
    .mar-summary-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .mar-dose-row { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')
<div class="nursing-mar-scope">
    <div class="card mb-12">
        <div class="card-body">
            <div class="ph-toolbar ph-wrap">
                <div class="input-group ph-search">
                    <span class="input-addon">🔍</span>
                    <input type="text" class="form-control" id="marSearch" placeholder="Search patient / UHID / bed..." autocomplete="off">
                </div>
                <select class="form-control ph-small-select" id="marWardFilter" style="width: 180px;">
                    <option value="">All Wards</option>
                </select>
                <div class="input-group" style="width: 180px;">
                    <span class="input-addon">📅</span>
                    <input type="text" class="form-control" id="marDate" placeholder="Select date" readonly>
                </div>
            </div>
            <div class="fs-11 text-muted mt-8">
                Meal reference times: Breakfast <b id="marBreakfastTime">08:00</b> · Lunch <b id="marLunchTime">13:00</b> · Dinner <b id="marDinnerTime">20:00</b>
                · <b>Frequency MAR times</b> override meal-based calculation when configured in Settings → Frequency.
            </div>
        </div>
    </div>

    <div class="mar-summary-grid">
        <div class="mar-summary-card"><div class="label">Total Doses Today</div><div class="value" id="marSummaryTotal">0</div></div>
        <div class="mar-summary-card"><div class="label">Pending</div><div class="value text-warning" id="marSummaryPending">0</div></div>
        <div class="mar-summary-card"><div class="label">Given</div><div class="value text-success" id="marSummaryGiven">0</div></div>
        <div class="mar-summary-card"><div class="label">Missed / Held / Refused</div><div class="value text-danger" id="marSummaryOther">0</div></div>
    </div>

    <div id="marContent">
        <div class="text-muted text-center" style="padding: 40px;">Loading medication administration records...</div>
    </div>
</div>
@endsection

@push('scripts')
@include('layouts.partials.flatpickr-js')
@endpush
