
@extends('layouts.admin.app',['main_li'=>'Permissions','sub_li'=>''])
@section('title','Permissions')
@section('content')
<div class="container-fluid">
  <div class="page-title">
    <div class="row">
      <div class="col-sm-4">
        <h3 class="mb-3 mb-md-0">Permissions List</h3>
      </div>
      <div class="col-sm-8 d-flex flex-wrap justify-content-md-end align-items-center gap-2">
        <button class="btn btn-info adddata" data-id="">+ Add Permission</button>
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
                    <th>Permission</th>
                    <th>Module</th>
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
  @include('layouts.partials.flatpickr-css')
@endpush
@push('scripts')
  @include('layouts.partials.datatable-js')
  @include('layouts.partials.flatpickr-js')
@endpush