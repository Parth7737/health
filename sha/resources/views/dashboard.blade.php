@extends('layouts.admin.app',['main_li'=>'Dashboard','sub_li'=>''])

@section('title', \App\CentralLogics\Helpers::get_settings('site_title') != 'null' ? \App\CentralLogics\Helpers::get_settings('site_title') :
    '')
@section('content')

            <div class="row row-card-no-pd">
              <div class="col-12 col-sm-6 col-md-6 col-xl-3">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex justify-content-between">
                      <div>
                        <h6><b>Speciality</b></h6>
                      </div>
                      <h4 class="text-info fw-bold">110</h4>
                    </div>
                    <!-- <div class="progress progress-sm">
                      <div
                        class="progress-bar bg-info w-75"
                        role="progressbar"
                        aria-valuenow="75"
                        aria-valuemin="0"
                        aria-valuemax="100"
                      ></div>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                      <p class="text-muted mb-0">Change</p>
                      <p class="text-muted mb-0">75%</p>
                    </div> -->
                  </div>
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-6 col-xl-3">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex justify-content-between">
                      <div>
                        <h6><b>Hospital</b></h6>
                      </div>
                      <h4 class="text-success fw-bold">89</h4>
                    </div>
                    <!-- <div class="progress progress-sm">
                      <div
                        class="progress-bar bg-success w-25"
                        role="progressbar"
                        aria-valuenow="25"
                        aria-valuemin="0"
                        aria-valuemax="100"
                      ></div>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                      <p class="text-muted mb-0">Change</p>
                      <p class="text-muted mb-0">25%</p>
                    </div> -->
                  </div>
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-6 col-xl-3">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex justify-content-between">
                      <div>
                        <h6><b>Procedures</b></h6>
                      </div>
                      <h4 class="text-danger fw-bold">15</h4>
                    </div>
                    <!-- <div class="progress progress-sm">
                      <div
                        class="progress-bar bg-danger w-50"
                        role="progressbar"
                        aria-valuenow="50"
                        aria-valuemin="0"
                        aria-valuemax="100"
                      ></div>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                      <p class="text-muted mb-0">Change</p>
                      <p class="text-muted mb-0">50%</p>
                    </div> -->
                  </div>
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-6 col-xl-3">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex justify-content-between">
                      <div>
                        <h6><b>Schemes</b></h6>
                      </div>
                      <h4 class="text-secondary fw-bold">12</h4>
                    </div>
                    <!-- <div class="progress progress-sm">
                      <div
                        class="progress-bar bg-secondary w-25"
                        role="progressbar"
                        aria-valuenow="25"
                        aria-valuemin="0"
                        aria-valuemax="100"
                      ></div>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                      <p class="text-muted mb-0">Change</p>
                      <p class="text-muted mb-0">25%</p>
                    </div> -->
                  </div>
                </div>
              </div>
            </div>
@endsection
@push('scripts')
<script>
    
//Notify
$.notify({
	icon: 'icon-bell',
	title: 'Welcome',
	message: 'Admin',
},{
	type: 'secondary',
	placement: {
		from: "bottom",
		align: "right"
	},
	time: 1000,
});

</script>
@endpush