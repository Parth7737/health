<div class="row g-6">
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
        <div class="card card-border-shadow-warning h-100 filter-card" data-status="Empanelment Recommended by DEC">
                <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar me-4"><span class="avatar-initial rounded-3 bg-label-warning"><i class="ri-calendar-schedule-fill ri-24px"></i></span></div>
                    <h4 class="mb-0">{{$submitted}}</h4>
                </div>
                <h6 class="mb-0 fw-normal">Pending SEC</h6>
                </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-warning h-100 filter-card" data-status="Query Raised by SEC">
                <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar me-4"><span class="avatar-initial rounded-3 bg-label-warning"><i class="ri-file-history-fill ri-24px"></i></span></div>

                    <h4 class="mb-0">{{$decquery}}</h4>
                </div>
                <h6 class="mb-0 fw-normal">DEC Query</h6>
                </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-primary h-100 filter-card" data-status="Approved Upgradation Request">
                <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar me-4"><span class="avatar-initial rounded-3 bg-label-primary"><i class="ri-refresh-fill ri-24px"></i></span></div>

                    <h4 class="mb-0">{{$upgradationRequest}}</h4>
                </div>
                <h6 class="mb-0 fw-normal">Upgradation Request</h6>
                </div>
        </div>
    </div>
</div>
<div class="row mt-3 g-6 toggle-div" style="display: none;">
    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-info h-100 filter-card" data-status="Withdrawn">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar me-4"><span class="avatar-initial rounded-3 bg-label-info"><i class="ri-mail-send-line ri-24px"></i></span></div>
                    <h4 class="mb-0">{{$Withdrawn}}</h4>
                </div>
                <h6 class="mb-0 fw-normal">Withdrawn</h6>
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


<!-- <div class="col-sm-6 col-lg-3">
    <div class="card card-border-shadow-danger h-100 filter-card" data-status="Queried">
            <div class="card-body">
            <div class="d-flex align-items-center mb-2">
                <h4 class="mb-0">{{--$facilityQuery--}}</h4>
            </div>
            <h6 class="mb-0 fw-normal">Facility Query</h6>
            </div>
    </div>
</div> -->

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