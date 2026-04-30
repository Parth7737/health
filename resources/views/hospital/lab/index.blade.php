@extends('layouts.hospital.app')
@section('title','Pathology')
@section('page_header_icon', '🧪')
@section('page_subtitle', 'Manage Pathology')

@section('page_header_actions')
<button class="btn btn-warning btn-sm" type="button" onclick="switchLabTab('urgentPane', null)">🚨 Urgent Test</button>
<button class="btn btn-primary btn-sm" type="button" onclick="openModal();">🧫 New Sample</button>
<button class="btn btn-success btn-sm" type="button" onclick="switchLabTab('resultEntryPane', null)">📊 Enter Result</button>
@endsection

@section('content')
<div class="container-fluid">
    @include('hospital.lab.partials.stats')

    <div class="card">
        <div class="card-body p-0">
            @include('hospital.lab.partials.tabs')
            @include('hospital.lab.partials.sample-queue')
            @include('hospital.lab.partials.urgent')
            @include('hospital.lab.partials.result-entry')
            @include('hospital.lab.partials.critical')
            @include('hospital.lab.partials.reports')
            @include('hospital.lab.partials.tat')
            @include('hospital.lab.partials.analyzer')
        </div>
    </div>
</div>

<!-- NEW SAMPLE MODAL -->
<div class="modal-overlay hidden" id="newSampleModal">
    <div class="modal modal-lg">
        <div class="body">

        </div>
    </div>
</div>
@endsection

@push('styles')
@include('layouts.partials.datatable-css')
@include('layouts.partials.flatpickr-css')
@include('hospital.lab.partials.custom-css')
@endpush

@push('scripts')
@include('layouts.partials.datatable-js')
@include('layouts.partials.flatpickr-js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    function openModal() {
        loader('show');
        $.ajax({
            url: "{{ route('hospital.pathology.sample.create') }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                loader('hide');
                $('#ajaxdata').html(response);
                $('.add-datamodal .modal-dialog').addClass('modal-xl');
                $('.add-datamodal').modal('show');
            },
            error: function() {
                loader('hide');
                alert('Failed to load sample form.');
            }
        });
    }
    $(document).ready(function() {
    
    });
</script>
@endpush
