@extends('layouts.hospital.app')
@section('title', 'Bed Dashboard')
@section('page_header_icon', '🛏')
@section('page_subtitle', 'Manage Bed Dashboard')
@section('page_header_actions')
<a href="{{ route('hospital.settings.beds.bed.index') }}" class="btn btn-info">Manage Beds</a>
@endsection
@section('content')
<div class="container-fluid">

    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Beds</p>
                    <h3 class="mb-0">{{ $beds->count() }}</h3>
                </div>
            </div>
        </div>
        @php
            $occupiedCount = 0;
            foreach($statuses as $status) {
                if (strtolower(str_replace([' ', '-'], '_', (string) $status->status_name)) === 'occupied') {
                    $occupiedCount = (int) ($statusCounts[$status->id] ?? 0);
                    break;
                }
            }
        @endphp
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <p class="text-muted mb-1">Occupied</p>
                        <span class="badge bg-danger">&nbsp;</span>
                    </div>
                    <h3 class="mb-0 text-danger">{{ $occupiedCount }}</h3>
                </div>
            </div>
        </div>
        @foreach($statuses as $status)
            @php
                $statusNameLower = strtolower(str_replace([' ', '-'], '_', (string) $status->status_name));
                $isAllowedStatus = in_array($statusNameLower, ['inactive', 'reserved', 'maintenance'], true);
            @endphp
            @if($isAllowedStatus)
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <p class="text-muted mb-1">{{ $status->status_name }}</p>
                                <span class="badge" style="background: {{ $status->color_code }};">&nbsp;</span>
                            </div>
                            <h3 class="mb-0">{{ (int) ($statusCounts[$status->id] ?? 0) }}</h3>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <div class="row g-3">
        <div class="col-xl-3">
            <div class="card">
                <div class="card-header bg-info ">
                    <strong>Scan Bed Barcode</strong>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <input type="text" class="form-control" id="scan_barcode" placeholder="Scan/Enter bed barcode">
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="scan_barcode_btn">Find Bed</button>
                    <div id="scan_result" class="mt-3 small"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-9">
            <div class="card">
                <div class="card-header bg-info d-flex align-items-center justify-content-between">
                    <strong>Live Bed Map</strong>
                    <!-- <small class="text-muted">Status changes are instant</small> -->
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @forelse($beds as $bed)
                            <div class="col-md-6 col-lg-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <div class="fw-semibold">Bed {{ $bed->bed_number }}</div>
                                            <div class="small text-muted">{{ $bed->bed_code }}</div>
                                        </div>
                                        <span class="badge" style="background: {{ $bed->bedStatus?->color_code ?? '#6c757d' }};">{{ $bed->bedStatus?->status_name ?? 'Unknown' }}</span>
                                    </div>
                                    <div class="small text-muted mb-2">
                                        {{ $bed->room?->ward?->ward_name }} | {{ $bed->room?->room_number }}
                                    </div>
                                    <div class="small mb-2">Type: <strong>{{ $bed->bedType?->type_name ?? '-' }}</strong></div>
                                    {{-- <!-- <img src="{{ route('hospital.settings.beds.bed.barcode', ['bed' => $bed->id]) }}" alt="Barcode" style="max-width: 170px;"> --> --}}
                                    @can('edit-bed')
                                        @php
                                            $bedStatusName = strtolower(str_replace([' ', '-'], '_', (string) ($bed->bedStatus?->status_name ?? '')));
                                            $isOccupiedOrReserved = in_array($bedStatusName, ['occupied', 'reserved_for_discharge'], true);
                                        @endphp
                                        <div class="mt-3">
                                            <select class="form-control form-control-sm bed-status-select" data-bed-id="{{ $bed->id }}" {{ $isOccupiedOrReserved ? 'disabled' : '' }}>
                                                <option value="" disabled>Select</option>
                                                @foreach($statuses as $status)
                                                    @php
                                                        $statusNameLower = strtolower(str_replace([' ', '-'], '_', (string) $status->status_name));
                                                        $isAllowedStatus = in_array($statusNameLower, ['inactive', 'reserved', 'maintenance'], true);
                                                    @endphp
                                                    @if($isAllowedStatus)
                                                        <option value="{{ $status->id }}" {{ (int) $bed->bed_status_id === (int) $status->id ? 'selected' : '' }}>{{ $status->status_name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            @if($isOccupiedOrReserved)
                                                <small class="text-muted d-block mt-1">
                                                    <i class="fa fa-lock"></i> Status locked — cannot change {{ strtolower(str_replace('_', ' ', $bedStatusName)) }} bed.
                                                </small>
                                            @endif
                                        </div>
                                    @endcan
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info mb-0">No beds found.</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).on('change', '.bed-status-select', async function () {
    const bedId = $(this).data('bed-id');
    const bedStatusId = $(this).val();
    const token = await csrftoken();

    $.post(route('status', { bed: bedId }), {
        _token: token,
        bed_status_id: bedStatusId
    }).done(function (response) {
        if (response.status) {
            sendmsg('success', response.message);
            setTimeout(function () { window.location.reload(); }, 500);
        }
    }).fail(function (xhr) {
        if (xhr.responseJSON && xhr.responseJSON.message) {
            sendmsg('error', xhr.responseJSON.message);
        } else {
            sendmsg('error', 'Failed to update bed status.');
        }
    });
});

$(document).on('click', '#scan_barcode_btn', async function () {
    const barcode = $('#scan_barcode').val();
    const token = await csrftoken();

    $.post(route('scan'), {
        _token: token,
        barcode: barcode
    }).done(function (response) {
        if (!response.status) {
            $('#scan_result').html('<div class="text-danger">Bed not found.</div>');
            return;
        }

        const bed = response.bed;
        let resultHtml = '<div class="p-3 border rounded" style="background: #fafbfc;">'
            + '<div class="mb-2"><strong style="font-size: 16px;">' + (bed.bed_code || '') + '</strong></div>'
            + '<div class="small mb-1">Bed: ' + (bed.bed_number || '-') + '</div>'
            + '<div class="small mb-1">Ward/Room: ' + (bed.ward || '-') + ' / ' + (bed.room || '-') + '</div>'
            + '<div class="small mb-2">Type: <strong>' + (bed.type || '-') + '</strong></div>'
            + '<div class="mb-2">Status: <span class="badge" style="background:' + (bed.status_color || '#6c757d') + '; padding: 0.4em 0.6em;">' + (bed.status || 'Unknown') + '</span></div>';

        // Show patient details if bed is occupied
        if (bed.patient && bed.patient.name) {
            resultHtml += '<hr style="margin: 0.75rem 0;">'
                + '<div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 12px; border-radius: 4px;">'
                + '<div class="fw-semibold mb-1" style="color: #223;">Occupant Information</div>'
                + '<div class="small mb-1"><strong>Patient:</strong> ' + (bed.patient.name || '-') + '</div>'
                + '<div class="small mb-1"><strong>UHID:</strong> ' + (bed.patient.uhid || '-') + '</div>'
                + '<div class="small mb-1"><strong>Admission No:</strong> ' + (bed.patient.admission_no || '-') + '</div>'
                + '<div class="small mb-1"><strong>Admitted:</strong> ' + (bed.patient.admission_date || '-') + '</div>'
                + '<div class="small mb-1"><strong>Consultant:</strong> ' + (bed.patient.consultant || '-') + '</div>'
                + '<div class="small"><strong>Department:</strong> ' + (bed.patient.department || '-') + '</div>'
                + '</div>';
        }

        resultHtml += '</div>';
        $('#scan_result').html(resultHtml);
    }).fail(function (xhr) {
        const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Scan failed.';
        $('#scan_result').html('<div class="text-danger">' + msg + '</div>');
    });
});
</script>
@endpush