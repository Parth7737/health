<div class="modal-header">
    <h5 class="modal-title">{{ $id ? 'Edit' : 'Add' }} Supplier</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="POST" id="savedata">
    @if($id)<input type="hidden" name="id" value="{{ $id }}">@endif
    <div class="modal-body">
        <div class="row g-2">
            <div class="col-md-6">
                <label class="form-label">Supplier Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="name" value="{{ $data?->name }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Contact Person</label>
                <input type="text" class="form-control" name="contact_person" value="{{ $data?->contact_person }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input type="text" class="form-control" name="phone" value="{{ $data?->phone }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" value="{{ $data?->email }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">GST No</label>
                <input type="text" class="form-control" name="gstin" value="{{ $data?->gstin }}" maxlength="20">
            </div>
            <div class="col-md-12">
                <label class="form-label">Address</label>
                <textarea class="form-control" name="address" rows="2">{{ $data?->address }}</textarea>
            </div>
            <div class="col-md-12">
                <label class="form-label">Notes</label>
                <textarea class="form-control" name="notes" rows="2">{{ $data?->notes }}</textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Supplier</button>
    </div>
</form>
