<div class="modal-header">
    <h5 class="modal-title" id="view_modal_dataModelLabel">{{ $id ? 'Edit' : 'Manage' }} {{ $typeLabel }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="savedata" enctype="multipart/form-data">
    <div class="modal-body">
        <input type="hidden" name="id" id="id" value="{{ $id }}">
        <input type="hidden" name="type" id="type" value="{{ $type }}">

        <div class="mb-3">
            <label class="form-label">Print Type</label>
            <input type="text" class="form-control" value="{{ $typeLabel }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Header Image</label>
            <input type="file" name="header_image" id="header_image" class="form-control" accept="image/*">
            <small class="text-muted d-block mt-1">Recommended banner image. JPG, PNG or WEBP up to 2MB.</small>
            <span class="text-danger err_header_image"></span>
        </div>

        <div class="mb-3 header-image-preview-wrapper {{ empty($data?->header_image) ? 'd-none' : '' }}">
            <label class="form-label d-block">Current Header</label>
            <img
                src="{{ !empty($data?->header_image) ? asset('public/storage/' . $data->header_image) : '' }}"
                alt="Current Header"
                class="img-fluid rounded border header-image-preview"
                style="max-height: 110px; object-fit: contain;"
            >

            @if(!empty($data?->header_image))
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" value="1" id="remove_header_image" name="remove_header_image">
                    <label class="form-check-label" for="remove_header_image">
                        Remove current header image
                    </label>
                </div>
            @endif
        </div>

        <div class="mb-0">
            <label class="form-label">Footer Text</label>
            <textarea name="footer_text" id="footer_text" rows="6" class="form-control" placeholder="Add footer text, disclaimers, address, contact details, or any print note.">{{ $data?->footer_text }}</textarea>
            <small class="text-muted d-block mt-1">You can leave this empty if the type only needs a header image.</small>
            <span class="text-danger err_footer_text"></span>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>