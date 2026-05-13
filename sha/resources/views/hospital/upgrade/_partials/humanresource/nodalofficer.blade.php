<div class="inside-left-info-box @if(auth()->user()->name != '') success @else pending @endif mt-4">
    <h4 class="colored-verticle-title">
        Hospital Admin/Nodal Officer 
        <span class="status-dot">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="undefined">
                <path d="M400-304 240-464l56-56 104 104 264-264 56 56-320 320Z" />
            </svg>
        </span>
    </h4>
    <form id="nodalForm">
        <div class="row g-5">
            <div class="col-md-6 col-lg-3">
                <div
                    class="form-floating form-floating-outline">
                    <input type="text" id="name" name="name" value="{{auth()->user()->name}}" class="form-control" readonly placeholder="Rajal Gupta" />
                    <label for="name">Hospital Admin Name</label>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div
                    class="form-floating form-floating-outline">
                    <input type="text" id="mobile_no" name="mobile_no" value="{{auth()->user()->mobile_no}}" readonly class="form-control" placeholder="CEO" />
                    <label for="mobile_no">Mobile No</label>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div
                    class="input-group input-group-merge">
                    <div
                        class="form-floating form-floating-outline">
                        <input type="text" class="form-control" id="email" placeholder="john.doe" value="{{auth()->user()->email}}" readonly aria-label="Recipient's username" aria-describedby="email">
                        <label for="email">Email ID</label>
                    </div>
                    <!-- <button class="input-group-text">Verify</button> -->
                </div>
            </div>
            <!-- <div class="col-md-12">
                <div class="d-flex">
                    <label for="BMI" class="mb-2 me-3">Does the faculty has additional nodal officer <span class="text-danger">*</span></label>
                    <div class="form-check">
                        <input class="form-check-input"
                            type="radio" name="options"
                            id="option1" value="option1">
                        <label class="form-check-label"
                            for="option1">
                        Yes
                        </label>
                    </div>
                    <div class="form-check ms-4">
                        <input class="form-check-input"
                            type="radio" name="options"
                            id="option2" value="option2">
                        <label class="form-check-label"
                            for="option2">
                        No
                        </label>
                    </div>
                </div>
            </div> -->
            <div class="col-md-12">
                <div class="d-flex justify-content-end">
                    <!-- <button class="btn btn-primary">SAVE</button> -->
                </div>
            </div>
        </div>
    </form>
</div>