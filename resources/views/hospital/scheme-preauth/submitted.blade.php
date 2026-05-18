@extends('layouts.hospital.app', ['is_header_hiden' => true])
@section('title', 'Scheme pre-authorisation')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Pre-authorisation submitted</h5>
            <p class="mb-2">Case reference: <strong>{{ $preauthRegister->register_id }}</strong></p>
            <p class="mb-3 text-muted">Current status: {{ $preauthRegister->status_label }}. When SHA APIs are connected, updates will sync using the SHA preauth id stored on this record.</p>
            <a href="{{ $schemePreauthAfterSubmitUrl }}" class="btn btn-primary">Back to Patient 360</a>
        </div>
    </div>
</div>
@endsection
