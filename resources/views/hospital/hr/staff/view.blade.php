<div class="modal-header hrx-staff-modal-header">
    <h5 class="modal-title d-flex align-items-center gap-2">
        <span class="hrx-staff-title-icon"><i class="fa fa-user"></i></span>
        <span>Staff Profile</span>
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

@php
    $fullName = trim(($staff->first_name ?? '') . ' ' . ($staff->last_name ?? ''));
    $departmentName = $staff->department->name ?? 'Unassigned';
    $designationName = $staff->designation->name ?? 'Unassigned';
    $specialistName = $staff->specialist->name ?? '-';
    $roleName = $staff->role->name ?? '-';
    $statusLabel = $staff->status ?: 'Unknown';
    $workTimings = is_array($staff->work_timings) ? $staff->work_timings : [];
@endphp

<div class="modal-body">
    <div class="hrx-view-section">
        <div class="hrx-view-title">Basic Information</div>
        <div class="row g-2">
            <div class="col-md-3"><div class="hrx-view-item"><span>Employee ID</span><strong>{{ $staff->staff_id ?: 'N/A' }}</strong></div></div>
            <div class="col-md-5"><div class="hrx-view-item"><span>Name</span><strong>{{ $fullName !== '' ? $fullName : 'Unnamed Staff' }}</strong></div></div>
            <div class="col-md-2"><div class="hrx-view-item"><span>Status</span><strong>{{ $statusLabel }}</strong></div></div>
            <div class="col-md-2"><div class="hrx-view-item"><span>Gender</span><strong>{{ $staff->gender ?: '-' }}</strong></div></div>
        </div>
    </div>

    <div class="hrx-view-section">
        <div class="hrx-view-title">Professional Details</div>
        <div class="row g-2">
            <div class="col-md-3"><div class="hrx-view-item"><span>Role</span><strong>{{ $roleName }}</strong></div></div>
            <div class="col-md-3"><div class="hrx-view-item"><span>Department</span><strong>{{ $departmentName }}</strong></div></div>
            <div class="col-md-3"><div class="hrx-view-item"><span>Designation</span><strong>{{ $designationName }}</strong></div></div>
            <div class="col-md-3"><div class="hrx-view-item"><span>Specialist</span><strong>{{ $specialistName }}</strong></div></div>
            <div class="col-md-3"><div class="hrx-view-item"><span>Category</span><strong>{{ $staff->employment_category ?: '-' }}</strong></div></div>
            <div class="col-md-3"><div class="hrx-view-item"><span>Basic Pay</span><strong>{{ $staff->basic_pay !== null ? $staff->basic_pay : '-' }}</strong></div></div>
            <div class="col-md-3"><div class="hrx-view-item"><span>Shift</span><strong>{{ $staff->shift_timing ?: '-' }}</strong></div></div>
            <div class="col-md-3"><div class="hrx-view-item"><span>Slot Duration</span><strong>{{ $staff->slot_duration ?: '-' }}</strong></div></div>
        </div>
    </div>

    <div class="hrx-view-section">
        <div class="hrx-view-title">Contact & Dates</div>
        <div class="row g-2">
            <div class="col-md-4"><div class="hrx-view-item"><span>Email</span><strong>{{ $staff->email ?: '-' }}</strong></div></div>
            <div class="col-md-4"><div class="hrx-view-item"><span>Phone</span><strong>{{ $staff->phone ?: '-' }}</strong></div></div>
            <div class="col-md-4"><div class="hrx-view-item"><span>Date of Joining</span><strong>{{ optional($staff->date_of_joining)->format('d M Y') ?: '-' }}</strong></div></div>
            <div class="col-md-4"><div class="hrx-view-item"><span>Date of Birth</span><strong>{{ optional($staff->date_of_birth)->format('d M Y') ?: '-' }}</strong></div></div>
            <div class="col-md-4"><div class="hrx-view-item"><span>Blood Group</span><strong>{{ $staff->blood_group ?: '-' }}</strong></div></div>
            <div class="col-md-4"><div class="hrx-view-item"><span>Marital Status</span><strong>{{ $staff->marital_status ?: '-' }}</strong></div></div>
        </div>
    </div>

    <div class="hrx-view-section">
        <div class="hrx-view-title">Address & Family</div>
        <div class="row g-2">
            <div class="col-md-6"><div class="hrx-view-item"><span>Current Address</span><strong>{{ $staff->current_address ?: '-' }}</strong></div></div>
            <div class="col-md-6"><div class="hrx-view-item"><span>Permanent Address</span><strong>{{ $staff->permanent_address ?: '-' }}</strong></div></div>
            <div class="col-md-6"><div class="hrx-view-item"><span>Father Name</span><strong>{{ $staff->father_name ?: '-' }}</strong></div></div>
            <div class="col-md-6"><div class="hrx-view-item"><span>Mother Name</span><strong>{{ $staff->mother_name ?: '-' }}</strong></div></div>
        </div>
    </div>

    <div class="hrx-view-section">
        <div class="hrx-view-title">Professional Notes</div>
        <div class="row g-2">
            <div class="col-md-4"><div class="hrx-view-item"><span>Qualifications</span><strong>{{ $staff->qualifications ?: '-' }}</strong></div></div>
            <div class="col-md-4"><div class="hrx-view-item"><span>Work Experience</span><strong>{{ $staff->work_experience ?: '-' }}</strong></div></div>
            <div class="col-md-4"><div class="hrx-view-item"><span>Specialization</span><strong>{{ $staff->specialization ?: '-' }}</strong></div></div>
            <div class="col-md-12"><div class="hrx-view-item"><span>Notes</span><strong>{{ $staff->note ?: '-' }}</strong></div></div>
        </div>
    </div>

    <div class="hrx-view-section">
        <div class="hrx-view-title">Leaves & Bank</div>
        <div class="row g-2">
            <div class="col-md-3"><div class="hrx-view-item"><span>PTO</span><strong>{{ $staff->pto ?? 0 }}</strong></div></div>
            <div class="col-md-3"><div class="hrx-view-item"><span>Vacation</span><strong>{{ $staff->vacation ?? 0 }}</strong></div></div>
            <div class="col-md-3"><div class="hrx-view-item"><span>FMLA</span><strong>{{ $staff->fmla ?? 0 }}</strong></div></div>
            <div class="col-md-3"><div class="hrx-view-item"><span>Education Leave</span><strong>{{ $staff->education_leave ?? 0 }}</strong></div></div>

            <div class="col-md-3"><div class="hrx-view-item"><span>Bank Name</span><strong>{{ $staff->bank_name ?: '-' }}</strong></div></div>
            <div class="col-md-3"><div class="hrx-view-item"><span>Account Holder</span><strong>{{ $staff->bank_account_holder_name ?: '-' }}</strong></div></div>
            <div class="col-md-3"><div class="hrx-view-item"><span>Account Number</span><strong>{{ $staff->bank_account_number ?: '-' }}</strong></div></div>
            <div class="col-md-3"><div class="hrx-view-item"><span>IFSC</span><strong>{{ $staff->bank_ifsc_code ?: '-' }}</strong></div></div>
        </div>
    </div>

    <div class="hrx-view-section">
        <div class="hrx-view-title">Work Timings</div>
        @if(!empty($workTimings))
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($workTimings as $day => $timing)
                            <tr>
                                <td>{{ ucfirst($day) }}</td>
                                <td>{{ $timing['start_time'] ?? '-' }}</td>
                                <td>{{ $timing['end_time'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="hrx-view-item"><span>Work Timings</span><strong>-</strong></div>
        @endif
    </div>

    <div class="hrx-view-section">
        <div class="hrx-view-title">Profile Photo</div>
        @if($staff->image)
            <img src="{{ url('public/storage/' . $staff->image) }}" alt="Staff Photo" style="width:90px;height:90px;border-radius:10px;border:1px solid #ddd;object-fit:cover;">
        @else
            <div class="hrx-view-item"><span>Photo</span><strong>Not uploaded</strong></div>
        @endif
    </div>
</div>

<div class="modal-footer justify-content-between">
    <div class="d-flex align-items-center gap-2">
        @can('edit-staff')
            <button type="button" class="btn hrx-view-action-btn hrx-view-action-edit hrx-staff-edit" data-id="{{ $staff->id }}">
                <i class="fa fa-pen"></i>Edit
            </button>
        @endcan
        @can('delete-staff')
            <button type="button" class="btn hrx-view-action-btn hrx-view-action-delete hrx-staff-delete" data-id="{{ $staff->id }}">
                <i class="fa fa-trash"></i>Delete
            </button>
        @endcan
    </div>
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>

<style>
    .hrx-view-section {
        border: 1px solid #e6d5f5;
        border-radius: 10px;
        background: #fcf7ff;
        padding: 12px;
        margin-bottom: 10px;
    }

    .hrx-view-title {
        font-size: 12px;
        font-weight: 700;
        color: #4a148c;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .hrx-view-item {
        border: 1px solid #efe5f8;
        background: #fff;
        border-radius: 8px;
        min-height: 58px;
        padding: 7px 10px;
    }

    .hrx-view-item span {
        display: block;
        font-size: 11px;
        color: #7a6a88;
        margin-bottom: 2px;
    }

    .hrx-view-item strong {
        display: block;
        font-size: 12.5px;
        color: #221833;
        font-weight: 600;
        word-break: break-word;
    }

    .hrx-view-action-btn {
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        padding: 6px 12px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .hrx-view-action-edit {
        border: 1px solid #fde68a;
        background: #fffbeb;
        color: #b45309;
    }

    .hrx-view-action-edit:hover {
        background: #fef3c7;
        color: #92400e;
        border-color: #fcd34d;
    }

    .hrx-view-action-delete {
        border: 1px solid #fecaca;
        background: #fef2f2;
        color: #dc2626;
    }

    .hrx-view-action-delete:hover {
        background: #fee2e2;
        color: #b91c1c;
        border-color: #fca5a5;
    }
</style>
