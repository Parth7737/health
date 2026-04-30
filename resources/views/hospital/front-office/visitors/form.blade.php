<div class="modal-header">
    <h5 class="modal-title" id="view_modal_dataModelLabel">{{ !$id ? 'Add' : 'Edit'}} Visitor</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="POST" id="savedata" enctype="multipart/form-data">
    <div class="modal-body">
        <input type="hidden" id="id" name="id" value="{{$id}}">
            <div class="row g-3">
                <div class="col-md-3">
                    @php $visitor_purposes = App\Models\VisitorPurpose::all(); @endphp 
                    <label class="form-label">Purpose <span class="text-danger">*</span></label>
                    <select class="form-select" name="visitor_purpose_id">
                    <option value="">Select</option>
                    @foreach($visitor_purposes as $purpose)
                        <option value="{{ $purpose->id }}" {{ $data && $data->visitor_purpose_id == $purpose->id ? 'selected' : '' }}>{{ $purpose->name }}</option>
                    @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" value="{{ $data ? $data->name : '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Phone</label>
                    <input type="text" class="form-control" name="phone" value="{{ $data ? $data->phone : '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">ID Card</label>
                    <input type="text" class="form-control" name="id_card" value="{{ $data ? $data->id_card : '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Number Of Person</label>
                    <input type="number" class="form-control" name="number_of_persons" value="{{ $data ? $data->number_of_persons : 1 }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date</label>
                    <input type="text" class="form-control" name="visit_date" value="{{ $data ? Carbon\Carbon::parse($data->visit_date)->format('d-m-Y') : date('d-m-Y') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">In Time</label>
                    <input type="time" class="form-control" name="in_time" value="{{ $data ? $data->in_time : date('H:i') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Out Time</label>
                    <input type="time" class="form-control" name="out_time" value="{{ $data ? $data->out_time : '' }}">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Note</label>
                    <textarea class="form-control" rows="2" name="note">{{ $data ? $data->note : '' }}</textarea>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Attach Document</label>
                    <input type="file" class="form-control" name="document">
                    @if($data && $data->document)
                        <a href="{{ url('public/storage/' . $data->document) }}" target="_blank" class="btn btn-sm btn-info mt-2">View Existing Document</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>