<div class="card mb-6 ps-0 border border-primary">
    <div class="card-body">
        <div class="row row-cols-5">
            <div class="col">
                <div class="d-flex text-center justify-content-center flex-column border-end border-secondary">
                    @if(@$hospital->image)
                    <div class="position-relative image-overlay">
                        <img src="{{asset('public/storage/'.@$hospital->image)}}" width="80" alt="{{@$hospital->facility_name}}" class="mb-3 rounded-circle">
                    </div>
                    @endif
                <span class="number-3 mb-2">{{@$hospital->facility_name}}</span>
                <span class="number-2">{{@$hospital->facilityOwnershipType->name}}</span>
                </div>
            </div>
            <div class="col">
                <div class="infodata">
                <label>Facility/Reference Id</label>
                <p><strong>{{@$hospital->hospital_id}}</strong></p>
                <label>Facility Contact</label>
                <p><strong>{{@$hospital->hospitalAddress->mobile_no}}</strong></p>
                <label>Status</label>
                <p><strong>{{@$hospital->status}}</strong></p>
                </div>
            </div>
            <div class="col">
                <div class="infodata">
                <label>Facility Name</label>
                <p>{{$hospital->facility_name}}</p>
                <label>Specialities Selected</label>
                <p>
                    @php
                        $specialities = $hospital->specialities()->where('available', 1)->get()->pluck('speciality.name')->toArray();
                    @endphp
                    {{ implode(', ', $specialities) }}
                </p>
                <!-- <label>Health Facility Registry ID</label>
                <p> </p> -->
                </div>
            </div>
            <div class="col">
                <div class="infodata">
                <label>State</label>
                <p>{{@$hospital->hospitalAddress->states->name}}</p>
                <label>Submission Date</label>
                <p><strong>{{date('d/m/Y', strtotime($hospital->created_at))}}</strong></p>
            
                </div>
            </div>
            <div class="col">
                <div class="infodata">
                <label>District</label>
                <p class="">{{@$hospital->hospitalAddress->districts->name}}</p>
                <label>Status Updated Date</label>
                <p class="">{{date('d/m/Y g:i:A', strtotime(@$hospital->status_update_date))}}</p>
                </div>
            </div>
            
        </div>
    </div>
</div>