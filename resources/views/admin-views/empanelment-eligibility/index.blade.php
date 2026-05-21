@extends('layouts.admin.app')
@section('title','AB Empanelment Eligibility Checklist')
@section('content')
<div class="container-fluid">
  <div class="page-title">
    <div class="row">
      <div class="col-sm-4">
        <h3 class="mb-3 mb-md-0">AB Empanelment Eligibility Checklist</h3>
      </div>
      <div class="col-sm-8 d-flex flex-wrap justify-content-md-end align-items-center gap-2">
        <button class="btn btn-info adddata" data-id="">+ Add Checklist Item</button>
      </div>
    </div>
  </div>
</div>
<!-- Container-fluid starts--> 
<div class="container-fluid">
  <div class="card">
    <div class="card-body">
      <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
          <table id="xin-table" class="display table-striped">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Subtitle</th>
                    <th>Is Required</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
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
