@extends('layouts.front.app')
@section('title', 'Add Page')
@section('content')
<div class="container-fluid py-3">
    <div class="row g-3" >
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0">Add Page</h5></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="pageTitle" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="pageTitle" placeholder="Enter page title" required>
                    </div>
                    <div class="mb-3">
                          <label>Page Type</label>
                          <div class="m-checkbox-inline">
                            <label for="edo-ani">
                              <input class="radio_animated" id="edo-ani" type="radio" name="rdo-ani" checked="">Standard
                            </label>
                            <label for="edo-ani1">
                              <input class="radio_animated" id="edo-ani1" type="radio" name="rdo-ani">Events
                            </label>
                            <label for="edo-ani2">
                              <input class="radio_animated" id="edo-ani2" type="radio" name="rdo-ani" checked="">News
                            </label>
                            <label for="edo-ani3">
                              <input class="radio_animated" id="edo-ani3" type="radio" name="rdo-ani">Gallery
                            </label>
                          </div>
                        </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <div class="d-flex justify-content-end mb-2">
                            <button type="button" class="btn btn-sm btn-primary"><i class="fa fa-plus me-1"></i>Add Media</button>
                        </div>
                            <div class="toolbar-box">
                                <textarea id="editor1" name="description"></textarea>
                            </div>
                    </div>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-header"><h6 class="mb-0">SEO Detail</h6></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="metaTitle" class="form-label">Meta Title</label>
                        <input type="text" class="form-control" id="metaTitle" placeholder="Meta Title">
                    </div>
                    <div class="mb-3">
                        <label for="metaKeyword" class="form-label">Meta Keyword</label>
                        <input type="text" class="form-control" id="metaKeyword" placeholder="Meta Keyword">
                    </div>
                    <div class="mb-3">
                        <label for="metaDescription" class="form-label">Meta Description</label>
                        <textarea class="form-control" id="metaDescription" rows="2" placeholder="Meta Description"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h6>Sidebar Setting</h6>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" id="sidebarToggle">
                        <label class="form-check-label" for="sidebarToggle">Sidebar</label>
                    </div>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-header"><h6>Featured Image</h6></div>
                <div class="card-body">
                <form class="dropzone dropzone-secondary" id="multiFileUpload" action="/upload.php">
                      <div class="dz-message needsclick"><i class="fa-solid fa-cloud-arrow-up fa-fade"></i>
                        <h6>Drop files here or click to upload.</h6><span class="note needsclick">(This is just a demo dropzone. Selected files are <strong>not</strong> actually uploaded.)</span>
                      </div>
                    </form>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body">
                    <button type="submit" class="btn btn-lg btn-success w-100"><i class="fa fa-save me-1"></i>Save</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Media Gallery Modal -->
<div class="modal fade" id="mediaGalleryModal" tabindex="-1" aria-labelledby="mediaGalleryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="mediaGalleryModalLabel">Media Gallery</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <form class="dropzone dropzone-secondary" id="mediaUploadDropzone" action="/upload.php">
            <div class="dz-message needsclick"><i class="fa-solid fa-cloud-arrow-up fa-fade"></i>
              <h6>Drop files here or click to upload.</h6><span class="note needsclick">(This is just a demo dropzone. Selected files are <strong>not</strong> actually uploaded.)</span>
            </div>
          </form>
        </div>
        <div class="row g-3" id="mediaGallery">
          <!-- Example static media thumbnails -->
          <div class="col-6 col-sm-4 col-md-3">
            <div class="card h-100 border-primary media-thumb" data-media-url="https://via.placeholder.com/300x200.png?text=Image+1">
              <img src="https://via.placeholder.com/300x200.png?text=Image+1" class="card-img-top" alt="Media 1">
              <div class="card-body p-2 text-center">
                <input type="checkbox" class="form-check-input media-select">
              </div>
            </div>
          </div>
          <div class="col-6 col-sm-4 col-md-3">
            <div class="card h-100 border-primary media-thumb" data-media-url="https://via.placeholder.com/300x200.png?text=Image+2">
              <img src="https://via.placeholder.com/300x200.png?text=Image+2" class="card-img-top" alt="Media 2">
              <div class="card-body p-2 text-center">
                <input type="checkbox" class="form-check-input media-select">
              </div>
            </div>
          </div>
          <div class="col-6 col-sm-4 col-md-3">
            <div class="card h-100 border-primary media-thumb" data-media-url="https://via.placeholder.com/300x200.png?text=Image+3">
              <img src="https://via.placeholder.com/300x200.png?text=Image+3" class="card-img-top" alt="Media 3">
              <div class="card-body p-2 text-center">
                <input type="checkbox" class="form-check-input media-select">
              </div>
            </div>
          </div>
          <div class="col-6 col-sm-4 col-md-3">
            <div class="card h-100 border-primary media-thumb" data-media-url="https://via.placeholder.com/300x200.png?text=Image+4">
              <img src="https://via.placeholder.com/300x200.png?text=Image+4" class="card-img-top" alt="Media 4">
              <div class="card-body p-2 text-center">
                <input type="checkbox" class="form-check-input media-select">
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="insertSelectedMedia">Insert Selected</button>
      </div>
    </div>
  </div>
</div>
@push('styles')
<link rel="stylesheet" href="{{ asset('public/front/assets/css/vendors/icofont.css') }}">
<link rel="stylesheet" href="{{ asset('public/front/assets/css/vendors/themify.css') }}">
<link rel="stylesheet" href="{{ asset('public/front/assets/css/vendors/fontawesome.css') }}">
<link rel="stylesheet" href="{{ asset('public/front/assets/css/vendors/bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('public/front/assets/css/style.css') }}">
<link rel="stylesheet" href="{{ asset('public/front/assets/css/responsive.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('public/front/assets/css/vendors/dropzone.min.css') }}">
@endpush
@push('scripts')
<script src="{{ asset('public/front/assets/js/editor/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('public/front/assets/js/dropzone/dropzone.min.js') }}"></script>
<script src="{{ asset('public/front/assets/js/dropzone/dropzone-script.js') }}"></script>
<script>
    CKEDITOR.replace('editor1');

    // Add Media Modal logic
    document.querySelector('.btn-primary:has(i.fa-plus)').addEventListener('click', function() {
        var modal = new bootstrap.Modal(document.getElementById('mediaGalleryModal'));
        modal.show();
    });

    // Visual feedback for selected media
    document.querySelectorAll('.media-thumb').forEach(function(card) {
        card.addEventListener('click', function(e) {
            if (e.target.classList.contains('media-select')) return;
            var checkbox = card.querySelector('.media-select');
            checkbox.checked = !checkbox.checked;
            card.classList.toggle('border-success', checkbox.checked);
        });
    });
    document.querySelectorAll('.media-select').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            this.closest('.media-thumb').classList.toggle('border-success', this.checked);
        });
    });

    // Insert selected media into CKEditor
    document.getElementById('insertSelectedMedia').addEventListener('click', function() {
        var selected = document.querySelectorAll('.media-select:checked');
        var html = '';
        selected.forEach(function(cb) {
            var url = cb.closest('.media-thumb').getAttribute('data-media-url');
            html += '<img src="' + url + '" style="max-width:100%;margin:10px 0;" />';
        });
        CKEDITOR.instances['editor1'].insertHtml(html);
        var modal = bootstrap.Modal.getInstance(document.getElementById('mediaGalleryModal'));
        modal.hide();
    });

    // Dropzone init (demo only)
    if (window.Dropzone) {
        Dropzone.autoDiscover = false;
        new Dropzone('#mediaUploadDropzone', {
            url: '/upload.php',
            autoProcessQueue: false,
            addRemoveLinks: true
        });
    }
</script>
@endpush
@endsection
