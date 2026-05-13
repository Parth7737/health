<div class="row g-6 ">
    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-success h-100 filter-card" data-status="Empanelled">
                <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar me-4"><span class="avatar-initial rounded-3 bg-label-success"><i class="ri-emphasis ri-24px"></i></span></div>

                    <h4 class="mb-0">{{$empanelled}}</h4>
                </div>
                <h6 class="mb-0 fw-normal">Empanelled</h6>

                </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-warning h-100 filter-card" data-status="Submitted">
                <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                   <div class="avatar me-4"><span class="avatar-initial rounded-3 bg-label-warning"><i class="ri-calendar-schedule-fill ri-24px"></i></span></div>

                    <h4 class="mb-0">{{$submitted}}</h4>
                </div>
                <h6 class="mb-0 fw-normal">Pending DEC</h6>
                </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-primary h-100 filter-card" data-status="Upgradation Request">
                <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar me-4"><span class="avatar-initial rounded-3 bg-label-primary"><i class="ri-refresh-fill ri-24px"></i></span></div>

                    <h4 class="mb-0">{{$upgradation}}</h4>
                </div>
                <h6 class="mb-0 fw-normal">Upgradation Request</h6>
                </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-info h-100 filter-card" data-status="Queried">
                <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar me-4"><span class="avatar-initial rounded-3 bg-label-info"><i class="ri-file-history-fill ri-24px"></i></span></div>

                    <h4 class="mb-0">{{$queried}}</h4>
                </div>
                <h6 class="mb-0 fw-normal">Queried</h6>
                </div>
        </div>
    </div>
</div>

<div class="row mt-3 g-6 toggle-div" style="display: none;">
    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-info h-100 filter-card" data-status="Query Replied">
                <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar me-4"><span class="avatar-initial rounded-3 bg-label-info"><i class="ri-reply-fill ri-24px"></i></span></div>

                    <h4 class="mb-0">{{$qryreplied}}</h4>
                </div>
                <h6 class="mb-0 fw-normal">Query Replied</h6>
                </div>
        </div>
    </div>           
    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-success h-100 filter-card" data-status="Empanelment Recommended by DEC">
                <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar me-4"><span class="avatar-initial rounded-3 bg-label-success"><i class="ri-emphasis ri-24px"></i></span></div>

                    <h4 class="mb-0">{{$recommendedbydec}}</h4>
                </div>
                <h6 class="mb-0 fw-normal">Empanelment Recommended by DEC</h6>
                </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-danger h-100 filter-card" data-status="Empanelment Not Recommended by DEC">
                <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar me-4"><span class="avatar-initial rounded-3 bg-label-danger"><i class="ri-file-close-fill ri-24px"></i></span></div>

                    <h4 class="mb-0">{{$rejected}}</h4>
                </div>
                <h6 class="mb-0 fw-normal">Empanelment Not Recommended by DEC</h6>
                </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-success h-100 filter-card" data-status="Approved Upgradation Request">
                <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar me-4"><span class="avatar-initial rounded-3 bg-label-success"><i class="ri-check-double-fill ri-24px"></i></span></div>

                    <h4 class="mb-0">{{$approveupgradationrequest}}</h4>
                </div>
                <h6 class="mb-0 fw-normal">Approved Upgradation Request</h6>
                </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-warning h-100 filter-card" data-status="Query On Upgradation Request From Facility">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar me-4"><span class="avatar-initial rounded-3 bg-label-warning"><i class="ri-file-history-fill ri-24px"></i></span></div>

                    <h4 class="mb-0">{{$queryupgradationrequest}}</h4>
                </div>
                <h6 class="mb-0 fw-normal">Query On Upgradation Request From Facility</h6>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-warning h-100 filter-card" data-status="Query Raised by SEC">
                <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar me-4"><span class="avatar-initial rounded-3 bg-label-warning"><i class="ri-file-history-fill ri-24px"></i></span></div>

                    <h4 class="mb-0">{{$queriedbysec}}</h4>
                </div>
                <h6 class="mb-0 fw-normal">Query Raised by SEC</h6>
                </div>
        </div>
    </div>
</div>

<div class="row justify-content-end mt-2">
    <div class="col-sm-6 col-lg-3">
        <div class="d-flex justify-content-end">
            <div class="btn-group">
                <button type="button"
                    class="btn btn-outline-primary border-0 dropdown-toggle toggle-boxes waves-effect"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    View More
                </button>
            </div>
        </div>
    </div>
</div>

<script>
     $('.toggle-boxes').click(function () {
        const toggleDiv = $('.toggle-div');
        const button = $(this);

        toggleDiv.slideToggle(300, function () {
            if (toggleDiv.is(':visible')) {
                button.text('Show Less');
            } else {
                button.text('View More');
            }
        });
    });
</script>