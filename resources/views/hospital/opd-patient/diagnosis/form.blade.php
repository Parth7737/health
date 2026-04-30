<div class="modal-header">
    <h5 class="modal-title">{{ !$id ? 'Add' : 'Edit' }} Diagnosis</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form id="diagnosis-form" method="POST" enctype="multipart/form-data">
    <div class="modal-body">
        <input type="hidden" name="id" value="{{ $id }}">

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Report Type <span class="text-danger">*</span></label>
                <input type="text" name="report_type" value="{{ old('report_type', $data->report_type ?? '') }}" class="form-control" placeholder="Enter report type">
            </div>

            <div class="col-md-6">
                <label class="form-label">Report Date <span class="text-danger">*</span></label>
                <input type="text" name="report_date" value="{{ old('report_date', isset($data->report_date) ? \Carbon\Carbon::parse($data->report_date)->format('d-m-Y') : date('d-m-Y')) }}" class="form-control diagnosis-date" placeholder="DD-MM-YYYY">
            </div>

            <div class="col-md-12">
                <label class="form-label">Upload Report</label>
                <input type="file" name="report_file" class="form-control" accept=".pdf,.jpeg,.jpg,.png,.gif,.svg">
                @if(!empty($data?->report_file))
                    <small class="text-muted d-block mt-1">
                        Current File:
                        <a href="{{ asset('public/storage/'.$data->report_file) }}" target="_blank">View</a>
                    </small>
                @endif
            </div>

            <div class="col-md-12">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Enter description">{{ old('description', $data->description ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
