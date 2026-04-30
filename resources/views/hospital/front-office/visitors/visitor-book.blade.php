@extends('layouts.hospital.app')
@section('title','Dashboard | Hospital Engagement Module')
@section('page_header_icon', '📋')
@section('page_subtitle', 'Manage Visitor List')
@section('page_header_actions')
<button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addVisitorModal">+ Add Visitor</button>
@endsection
@section('content')
  <div class="container-fluid">
    <div class="card">
      <div class="card-body">
        <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
          <table id="visitor-table" class="display table-striped w-100">
            <thead class="table-light">
              <tr>
                <th>Purpose</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Date</th>
                <th>In Time</th>
                <th>Out Time</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Diagnostic Tests</td>
                <td>Sagar</td>
                <td>1223456252</td>
                <td>06-08-2024</td>
                <td>11:06 AM</td>
                <td>3:06 AM</td>
                <td>
                  <ul class="action mb-0">
                    <li class="view"><a href="#" data-bs-toggle="tooltip" title="Show" onclick="showVisitorDetails('Sagar')"><i class="fa-regular fa-eye"></i></a></li>
                    <li class="edit"><a href="#" data-bs-toggle="tooltip" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a></li>
                    <li class="delete"><a href="#" data-bs-toggle="tooltip" title="Delete" onclick="deleteVisitor(event)"><i class="fa-solid fa-trash-can"></i></a></li>
                  </ul>
                </td>
              </tr>
              <tr>
                <td>Treatment Procedures</td>
                <td>Parth</td>
                <td>1234567893</td>
                <td>06-08-2024</td>
                <td>11:06 AM</td>
                <td>12:15 AM</td>
                <td>
                  <ul class="action mb-0">
                    <li class="view"><a href="#" data-bs-toggle="tooltip" title="Show" onclick="showVisitorDetails('Parth')"><i class="fa-regular fa-eye"></i></a></li>
                    <li class="edit"><a href="#" data-bs-toggle="tooltip" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a></li>
                    <li class="delete"><a href="#" data-bs-toggle="tooltip" title="Delete" onclick="deleteVisitor(event)"><i class="fa-solid fa-trash-can"></i></a></li>
                  </ul>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  
   <!-- Add Visitor Modal -->
   <div class="modal fade" id="addVisitorModal" tabindex="-1" aria-labelledby="addVisitorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addVisitorModalLabel">Add Visitor</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form class="row g-3">
            <div class="col-md-3">
              @php $visitor_purposes = App\Models\VisitorPurpose::all(); @endphp 
              <label class="form-label">Purpose <span class="text-danger">*</span></label>
              <select class="form-select">
                <option>Select</option>
                @foreach($visitor_purposes as $purpose)
                  <option value="{{ $purpose->id }}">{{ $purpose->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" name="name">
            </div>
            <div class="col-md-3">
              <label class="form-label">Phone</label>
              <input type="text" class="form-control" name="phone">
            </div>
            <div class="col-md-3">
              <label class="form-label">ID Card</label>
              <input type="text" class="form-control" name="id_card">
            </div>
            <div class="col-md-3">
              <label class="form-label">Number Of Person</label>
              <input type="number" class="form-control" name="number_of_person">
            </div>
            <div class="col-md-3">
              <label class="form-label">Date</label>
              <input type="date" class="form-control" name="date">
            </div>
            <div class="col-md-3">
              <label class="form-label">In Time</label>
              <input type="time" class="form-control" name="in_time">
            </div>
            <div class="col-md-3">
              <label class="form-label">Out Time</label>
              <input type="time" class="form-control" name="out_time">
            </div>
            <div class="col-md-12">
              <label class="form-label">Note</label>
              <textarea class="form-control" rows="2" name="note"></textarea>
            </div>
            <div class="col-md-12">
              <label class="form-label">Attach Document</label>
              <input type="file" class="form-control" name="document">
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Visitor Details Modal -->
  <div class="modal fade" id="visitorDetailsModal" tabindex="-1" aria-labelledby="visitorDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="visitorDetailsModalLabel">Visitor Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-2">
            <div class="col-6"><strong>Purpose:</strong> <span id="detailPurpose"></span></div>
            <div class="col-6"><strong>Name:</strong> <span id="detailName"></span></div>
            <div class="col-6"><strong>Phone:</strong> <span id="detailPhone"></span></div>
            <div class="col-6"><strong>Number Of Person:</strong> <span id="detailNumPerson"></span></div>
            <div class="col-6"><strong>Date:</strong> <span id="detailDate"></span></div>
            <div class="col-6"><strong>In Time:</strong> <span id="detailInTime"></span></div>
            <div class="col-6"><strong>Out Time:</strong> <span id="detailOutTime"></span></div>
            <div class="col-12"><strong>Note:</strong> <span id="detailNote"></span></div>
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