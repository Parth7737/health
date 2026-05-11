<div class="modal-header hrx-staff-modal-header">
    <h5 class="modal-title d-flex align-items-center gap-2" id="view_modal_dataModelLabel">
        <span class="hrx-staff-title-icon"><i class="fa fa-user-plus"></i></span>
        <span>{{ !$id ? 'Add New' : 'Edit'}} Staff Member</span>
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="savedata" enctype="multipart/form-data">
    <div class="modal-body">   
        <input type="hidden" id="id" name="id" value="{{ $id ?? '' }}">

        <div class="row g-2">
            <div class="col-md-3">
                <label class="form-label">Employee ID</label>
                <input type="text" class="form-control" value="{{ @$data->staff_id ?: 'Auto-generated' }}" readonly>
            </div>
            <div class="col-md-3">
                <label class="form-label">Category</label>
                <select name="employment_category" id="employment_category" class="form-select">
                    <option value="">Select Category</option>
                    <option value="Permanent" {{ @$data->employment_category === 'Permanent' ? 'selected' : '' }}>Permanent</option>
                    <option value="Contract" {{ @$data->employment_category === 'Contract' ? 'selected' : '' }}>Contract</option>
                    <option value="Daily Wage" {{ @$data->employment_category === 'Daily Wage' ? 'selected' : '' }}>Daily Wage</option>
                    <option value="Outsource" {{ @$data->employment_category === 'Outsource' ? 'selected' : '' }}>Outsource</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Basic Pay</label>
                <input type="number" name="basic_pay" id="basic_pay" value="{{ @$data->basic_pay }}" class="form-control" placeholder="Monthly basic salary">
            </div>
            <div class="col-md-3">
                <label class="form-label">Shift</label>
                <select name="shift_timing" id="shift_timing" class="form-select">
                    <option value="">Select Shift</option>
                    <option value="Day (8am-4pm)" {{ @$data->shift_timing === 'Day (8am-4pm)' ? 'selected' : '' }}>Day (8am-4pm)</option>
                    <option value="Evening (2pm-10pm)" {{ @$data->shift_timing === 'Evening (2pm-10pm)' ? 'selected' : '' }}>Evening (2pm-10pm)</option>
                    <option value="Night (10pm-8am)" {{ @$data->shift_timing === 'Night (10pm-8am)' ? 'selected' : '' }}>Night (10pm-8am)</option>
                    <option value="General" {{ @$data->shift_timing === 'General' ? 'selected' : '' }}>General</option>
                </select>
            </div>
        </div>
        
        <!-- Professional Information Section -->
        <div class="section-title mb-3 mt-4">
            <h6 class="fw-bold text-default">Professional Information</h6>
        </div>

        <div class="row g-2">
            <div class="col-md-3">
                <label class="form-label">Role <span class="text-danger">*</span></label>
                <select name="role_id" id="role" class="form-select select2-modal">
                    <option value="">Select Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ @$data->role_id === $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Designation</label>
                <select name="hr_designation_id" id="hr_designation_id" class="form-select select2-modal">
                    <option value="">Select Designation</option>
                    @foreach($designations as $designation)
                        <option value="{{ $designation->id }}" {{ @$data->hr_designation_id === $designation->id ? 'selected' : '' }}>{{ $designation->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Department</label>
                <select name="hr_department_id" id="hr_department_id" class="form-select select2-modal">
                    <option value="">Select Department <span class="text-danger">*</span></option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ @$data->hr_department_id === $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                @php $departmentunits = @$data->hr_department_id ? App\Models\HrDepartmentUnit::where('hr_department_id',$data->hr_department_id)->get():[]; @endphp
                <label class="form-label">Department Unit</label>
                <select name="hr_department_unit_id" id="hr_department_unit_id" class="form-select select2-modal">
                    <option value="">Select Unit</option>
                    @foreach($departmentunits as $departmentunit)
                        <option value="{{ $departmentunit->id }}" {{ @$data->hr_department_unit_id === $departmentunit->id ? 'selected' : '' }}>{{ $departmentunit->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Specialist</label>
                <select name="hr_specialist_id" id="hr_specialist_id" class="form-select select2-modal">
                    <option value="">Select Specialist</option>
                    @foreach($specialists as $specialist)
                        <option value="{{ $specialist->id }}" {{ @$data->hr_specialist_id === $specialist->id ? 'selected' : '' }}>{{ $specialist->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Personal Information Section -->
        <div class="section-title mb-3 mt-4">
            <h6 class="fw-bold text-default">Personal Information</h6>
        </div>

        <div class="row g-2">
            <div class="col-md-3">
                <label class="form-label">First Name <span class="text-danger">*</span></label>
                <input type="text" name="first_name" id="first_name" value="{{ @$data->first_name }}" class="form-control" placeholder="First Name">
            </div>
            <div class="col-md-3">
                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                <input type="text" name="last_name" id="last_name" value="{{ @$data->last_name }}" class="form-control" placeholder="Last Name">
            </div>
            <div class="col-md-3">
                <label class="form-label">Father Name</label>
                <input type="text" name="father_name" id="father_name" value="{{ @$data->father_name }}" class="form-control" placeholder="Father Name">
            </div>
            <div class="col-md-3">
                <label class="form-label">Mother Name</label>
                <input type="text" name="mother_name" id="mother_name" value="{{ @$data->mother_name }}" class="form-control" placeholder="Mother Name">
            </div>
        </div>

        <div class="row g-2 mt-2">
            <div class="col-md-3">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" id="email" value="{{ @$data->email }}" class="form-control" placeholder="Email address">
            </div>
            <div class="col-md-3">
                <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                <input type="number" name="phone" id="phone" value="{{ @$data->phone }}" class="form-control" placeholder="Phone number">
            </div>
            <div class="col-md-3">
                <label class="form-label">Date of Joining <span class="text-danger">*</span></label>
                <input type="text" name="date_of_joining" id="date_of_joining" value="{{ @$data->date_of_joining?date('d-m-Y', strtotime(@$data->date_of_joining)):date('d-m-Y') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                <input type="text" name="date_of_birth" id="date_of_birth" value="{{ @$data->date_of_birth?date('d-m-Y', strtotime(@$data->date_of_birth)):date('d-m-Y') }}" class="form-control">
            </div>
        </div>

        <div class="row g-2 mt-2">
            <div class="col-md-4">
                <label class="form-label">Gender <span class="text-danger">*</span></label>
                <select name="gender" id="gender" class="form-select">
                    <option value="">Select Gender</option>
                    <option value="Male" {{ @$data->gender === 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ @$data->gender === 'Female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Blood Group</label>
                <select name="blood_group" id="blood_group" class="form-select">
                    <option value="">Select Blood Group</option>
                    <option value="A+" {{ @$data->blood_group === 'A+' ? 'selected' : '' }}>A+</option>
                    <option value="A-" {{ @$data->blood_group === 'A-' ? 'selected' : '' }}>A-</option>
                    <option value="B+" {{ @$data->blood_group === 'B+' ? 'selected' : '' }}>B+</option>
                    <option value="B-" {{ @$data->blood_group === 'B-' ? 'selected' : '' }}>B-</option>
                    <option value="AB+" {{ @$data->blood_group === 'AB+' ? 'selected' : '' }}>AB+</option>
                    <option value="AB-" {{ @$data->blood_group === 'AB-' ? 'selected' : '' }}>AB-</option>
                    <option value="O+" {{ @$data->blood_group === 'O+' ? 'selected' : '' }}>O+</option>
                    <option value="O-" {{ @$data->blood_group === 'O-' ? 'selected' : '' }}>O-</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Marital Status</label>
                <select name="marital_status" id="marital_status" class="form-select">
                    <option value="">Select Status</option>
                    <option value="Single" {{ @$data->marital_status === 'Single' ? 'selected' : '' }}>Single</option>
                    <option value="Married" {{ @$data->marital_status === 'Married' ? 'selected' : '' }}>Married</option>
                    <option value="Divorced" {{ @$data->marital_status === 'Divorced' ? 'selected' : '' }}>Divorced</option>
                    <option value="Not Specified" {{ @$data->marital_status === 'Not Specified' ? 'selected' : '' }}>Not Specified</option>
                </select>
            </div>
        </div>
        <!-- Address Section -->
        <div class="section-title mb-3 mt-4">
            <h6 class="fw-bold text-default">Address Information</h6>
        </div>

        <div class="row g-2">
            <div class="col-md-6">
                <label class="form-label">Current Address <span class="text-danger">*</span></label>
                <textarea name="current_address" id="current_address" class="form-control" rows="3" placeholder="Current Address">{{ @$data->current_address }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Permanent Address</label>
                <textarea name="permanent_address" id="permanent_address" class="form-control" rows="3" placeholder="Permanent Address">{{ @$data->permanent_address }}</textarea>
            </div>
        </div>

        <!-- Professional Details Section -->
        <div class="section-title mb-3 mt-4">
            <h6 class="fw-bold text-default">Professional Details</h6>
        </div>

        <div class="row g-2">
            <div class="col-md-6">
                <label class="form-label">Qualifications</label>
                <textarea name="qualifications" id="qualifications" class="form-control" rows="3" placeholder="List qualifications">{{ @$data->qualifications }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Work Experience</label>
                <textarea name="work_experience" id="work_experience" class="form-control" rows="3" placeholder="List work experience">{{ @$data->work_experience }}</textarea>
            </div>
        </div>
        <div class="row g-2">
            <div class="col-md-6">
                <label class="form-label">Specialization</label>
                <textarea name="specialization" id="specialization" class="form-control" rows="3" placeholder="List specializations">{{ @$data->specialization }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Notes</label>
                <textarea name="note" id="note" class="form-control" rows="3" placeholder="Additional note">{{ @$data->note }}</textarea>
            </div>
        </div>

        <div class="section-title mb-3 mt-4">
            <h6 class="fw-bold text-default">Bank Details</h6>
        </div>
        <div class="row g-2 mt-2">
            <div class="col-md-2">
                <label class="form-label">Account Holder Name </label>
                <input type="text" name="bank_account_holder_name" id="bank_account_holder_name" value="{{ @$data->bank_account_holder_name }}" class="form-control" placeholder="Account holder name">
            </div>
            <div class="col-md-2">
                <label class="form-label">Bank Name </label>
                <input type="text" name="bank_name" id="bank_name" value="{{ @$data->bank_name }}" class="form-control" placeholder="Bank name">
            </div>
            <div class="col-md-2">
                <label class="form-label">Account Number </label>
                <input type="number" name="bank_account_number" id="bank_account_number" value="{{ @$data->bank_account_number }}" class="form-control" placeholder="Account number">
            </div>
            <div class="col-md-2">
                <label class="form-label">Bank Branch </label>
                <input type="text" name="bank_branch" id="bank_branch" value="{{ @$data->bank_branch }}" class="form-control" placeholder="Bank branch">
            </div>
            <div class="col-md-2">
                <label class="form-label">Bank IFSC Code </label>
                <input type="text" name="bank_ifsc_code" id="bank_ifsc_code" value="{{ @$data->bank_ifsc_code }}" class="form-control" placeholder="Bank IFSC code">
            </div>
        </div>

        <!-- Work Timings Section -->
        <div class="hrx-wt-card mt-4">
            <div class="hrx-wt-header">
                <span class="hrx-wt-header-icon"><i class="fa fa-clock-o"></i></span>
                <span class="hrx-wt-header-title">Work Timings</span>
            </div>

            <div class="hrx-wt-body">
                <!-- Slot Duration (Doctor only) -->
                <div class="hrx-wt-slot-row mb-3" id="slot_duration_wrapper">
                    <div class="hrx-wt-slot-inner">
                        <span class="hrx-wt-slot-icon"><i class="fa fa-hourglass-half"></i></span>
                        <div>
                            <label class="form-label mb-1">Slot Duration (Minutes)</label>
                            <input type="number" name="slot_duration" id="slot_duration" value="{{ @$data->slot_duration }}" class="form-control hrx-wt-slot-input" placeholder="e.g. 15">
                        </div>
                    </div>
                </div>

                <!-- Add Timing Row -->
                <div class="hrx-wt-add-row">
                    <div class="hrx-wt-add-field">
                        <label class="form-label">Day</label>
                        <select class="form-select" id="work_day">
                            <option value="">Select Day</option>
                            <option value="monday">Monday</option>
                            <option value="tuesday">Tuesday</option>
                            <option value="wednesday">Wednesday</option>
                            <option value="thursday">Thursday</option>
                            <option value="friday">Friday</option>
                            <option value="saturday">Saturday</option>
                            <option value="sunday">Sunday</option>
                        </select>
                    </div>
                    <div class="hrx-wt-add-field">
                        <label class="form-label">Start Time</label>
                        <input type="time" class="form-control" id="start_time">
                    </div>
                    <div class="hrx-wt-add-field">
                        <label class="form-label">End Time</label>
                        <input type="time" class="form-control" id="end_time">
                    </div>
                    <div class="hrx-wt-add-actions">
                        <button type="button" class="hrx-btn-add" id="add_timing">
                            <i class="fa fa-plus"></i> Add
                        </button>
                        <button type="button" class="hrx-btn-clear" id="clear_timings">
                            <i class="fa fa-trash"></i> Clear All
                        </button>
                    </div>
                </div>

                <!-- Timings Table -->
                <div class="hrx-wt-table-wrap" id="timings_list_wrap">
                    <table class="hrx-wt-table" id="timings_table">
                        <thead>
                            <tr>
                                <th><i class="fa fa-calendar me-1"></i>Day</th>
                                <th><i class="fa fa-sign-in me-1"></i>Start</th>
                                <th><i class="fa fa-sign-out me-1"></i>End</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="timings_list">
                            @if(@$data->work_timings)
                                @php $timings = json_decode($data->work_timings, true); @endphp
                                @if(is_array($timings))
                                    @foreach($timings as $day => $timing)
                                        <tr class="timing-item" data-day="{{ $day }}">
                                            <td>
                                                <span class="hrx-day-pill">{{ ucfirst($day) }}</span>
                                            </td>
                                            <td>
                                                <span class="hrx-time-chip hrx-time-start"><i class="fa fa-clock-o me-1"></i>{{ $timing['start_time'] ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <span class="hrx-time-chip hrx-time-end"><i class="fa fa-clock-o me-1"></i>{{ $timing['end_time'] ?? 'N/A' }}</span>
                                            </td>
                                            <td class="text-end">
                                                <button type="button" class="hrx-btn-remove remove-timing" data-day="{{ $day }}" title="Remove">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </td>
                                            <input type="hidden" name="work_timings[{{ $day }}][start_time]" value="{{ $timing['start_time'] ?? '' }}">
                                            <input type="hidden" name="work_timings[{{ $day }}][end_time]" value="{{ $timing['end_time'] ?? '' }}">
                                        </tr>
                                    @endforeach
                                @endif
                            @endif
                        </tbody>
                    </table>
                    <div class="hrx-wt-empty" id="timings_empty" style="display:none">
                        <i class="fa fa-calendar-times-o"></i>
                        <span>No timings added yet. Select a day and add timings above.</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-2">
            <div class="col-md-6">
                <label class="form-label">Profile Photo</label>
                <input type="file" name="image" id="image" class="form-control" accept="image/*">
                @if(@$data->image)
                    <small class="text-muted d-block mt-2">Current image: <img src="{{ url('public/storage/' . $data->image) }}" width="50" class="rounded mt-1"></small>
                @endif
            </div>
            <div class="col-md-6">
                <label class="form-label">Status <span class="text-danger">*</span></label>
                <select name="status" id="status" class="form-select">
                    <option value="Active" {{ @$data->status === 'Active' ? 'selected' : 'selected' }}>Active</option>
                    <option value="Inactive" {{ @$data->status === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">{{ $id ? 'Update' : 'Create'}} Staff</button>
    </div>
</form>

<style>
    .hrx-staff-modal-header {
        background: linear-gradient(135deg, #fdf6ff 0%, #f5e6ff 100%);
        border-bottom: 1px solid #e1bee7;
        padding-top: 14px;
        padding-bottom: 14px;
    }

    .hrx-staff-modal-header .modal-title {
        color: #3b0070;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: 0.2px;
    }

    .hrx-staff-title-icon {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        background: linear-gradient(135deg, #4a148c, #8e24aa);
        box-shadow: 0 6px 14px rgba(74, 20, 140, 0.25);
        font-size: 13px;
    }

    #savedata + .modal-footer,
    #savedata .modal-footer {
        border-top: 1px solid #e9d5f5;
        background: linear-gradient(180deg, #fdf6ff 0%, #f5e6ff 100%);
    }

    #savedata .modal-footer .btn-secondary {
        border: 1px solid #d8b4e8;
        background: #fff;
        color: #44556b;
        font-weight: 600;
        border-radius: 9px;
        padding: 7px 16px;
    }

    #savedata .modal-footer .btn-secondary:hover {
        border-color: #c27dd6;
        background: #f7fbff;
    }

    #savedata .modal-footer .btn-primary {
        border: none;
        background: linear-gradient(135deg, #4a148c, #7b1fa2);
        color: #fff;
        font-weight: 700;
        border-radius: 9px;
        padding: 7px 18px;
        box-shadow: 0 8px 18px rgba(74, 20, 140, 0.28);
    }

    #savedata .modal-footer .btn-primary:hover {
        background: linear-gradient(135deg, #38006b, #6a1b9a);
        box-shadow: 0 10px 20px rgba(74, 20, 140, 0.34);
    }

    #savedata .form-label {
        color: #6b7280;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.2px;
        margin-bottom: 5px;
    }

    #savedata .form-control,
    #savedata .form-select {
        border: 1px solid #d5b8e8;
        border-radius: 10px;
        font-size: 13px;
        color: #1f2937;
        min-height: 38px;
        background: #fff;
        box-shadow: none;
    }

    #savedata .form-control:focus,
    #savedata .form-select:focus {
        border-color: #ba68c8;
        box-shadow: 0 0 0 3px rgba(74, 20, 140, 0.12);
    }

    #savedata textarea.form-control {
        min-height: 92px;
    }

    #savedata .section-title {
        border-bottom: 1px solid #e9d5f5;
        padding-bottom: 8px;
        margin-top: 22px !important;
    }

    #savedata .section-title h6 {
        color: #334155;
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 0;
    }

    /* ── Work Timings Card ─────────────────────────────── */
    .hrx-wt-card {
        border: 1px solid #ddb8f0;
        border-radius: 14px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 2px 10px rgba(74, 20, 140,0.06);
    }

    .hrx-wt-header {
        display: flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(135deg, #4a148c 0%, #7b1fa2 100%);
        padding: 11px 18px;
    }

    .hrx-wt-header-icon {
        width: 28px;
        height: 28px;
        border-radius: 7px;
        background: rgba(255,255,255,0.18);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 13px;
    }

    .hrx-wt-header-title {
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.3px;
    }

    .hrx-wt-body {
        padding: 18px;
    }

    /* Slot Duration Row */
    .hrx-wt-slot-row {
        background: #f8f0ff;
        border: 1px dashed #ce93d8;
        border-radius: 10px;
        padding: 10px 14px;
        display: inline-block;
    }

    .hrx-wt-slot-inner {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .hrx-wt-slot-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: linear-gradient(135deg, #4a148c, #8e24aa);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        flex-shrink: 0;
    }

    .hrx-wt-slot-input {
        width: 140px !important;
        min-height: 34px !important;
    }

    /* Add Row */
    .hrx-wt-add-row {
        display: flex;
        align-items: flex-end;
        gap: 10px;
        flex-wrap: wrap;
        background: #fdf6ff;
        border: 1px solid #e8d5f5;
        border-radius: 10px;
        padding: 14px;
        margin-bottom: 16px;
    }

    .hrx-wt-add-field {
        flex: 1;
        min-width: 120px;
    }

    .hrx-wt-add-actions {
        display: flex;
        gap: 8px;
        flex-shrink: 0;
    }

    .hrx-btn-add {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: linear-gradient(135deg, #16a34a, #22c55e);
        color: #fff;
        border: none;
        border-radius: 9px;
        padding: 8px 16px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(22,163,74,0.25);
        transition: all .18s ease;
        white-space: nowrap;
    }

    .hrx-btn-add:hover {
        background: linear-gradient(135deg, #15803d, #16a34a);
        box-shadow: 0 6px 16px rgba(22,163,74,0.32);
        transform: translateY(-1px);
    }

    .hrx-btn-clear {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #fff;
        color: #dc2626;
        border: 1.5px solid #fca5a5;
        border-radius: 9px;
        padding: 8px 14px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all .18s ease;
        white-space: nowrap;
    }

    .hrx-btn-clear:hover {
        background: #fef2f2;
        border-color: #ef4444;
    }

    /* Table */
    .hrx-wt-table-wrap {
        border: 1px solid #ddb8f0;
        border-radius: 10px;
        overflow: hidden;
    }

    .hrx-wt-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .hrx-wt-table thead tr {
        background: linear-gradient(135deg, #f8f0ff 0%, #ede0f7 100%);
    }

    .hrx-wt-table thead th {
        padding: 10px 14px;
        color: #4a148c;
        font-weight: 700;
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #e1bee7;
    }

    .hrx-wt-table tbody .timing-item:nth-child(even) {
        background: #fdf6ff;
    }

    .hrx-wt-table tbody .timing-item:nth-child(odd) {
        background: #fff;
    }

    .hrx-wt-table tbody .timing-item:hover {
        background: #f3e5f5;
    }

    .hrx-wt-table tbody .timing-item td {
        padding: 10px 14px;
        border-bottom: 1px solid #f3e5f5;
        vertical-align: middle;
    }

    .hrx-wt-table tbody .timing-item:last-child td {
        border-bottom: none;
    }

    .hrx-day-pill {
        display: inline-block;
        background: linear-gradient(135deg, #4a148c, #8e24aa);
        color: #fff;
        border-radius: 20px;
        padding: 3px 12px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.3px;
    }

    .hrx-time-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        border-radius: 8px;
        padding: 4px 11px;
        font-size: 12px;
        font-weight: 700;
    }

    .hrx-time-start {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .hrx-time-end {
        background: #dcfce7;
        color: #15803d;
    }

    .hrx-btn-remove {
        background: #fff0f0;
        border: 1.5px solid #fca5a5;
        color: #dc2626;
        border-radius: 7px;
        width: 30px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 12px;
        transition: all .15s ease;
    }

    .hrx-btn-remove:hover {
        background: #fef2f2;
        border-color: #ef4444;
        transform: scale(1.05);
    }

    .hrx-wt-empty {
        display: flex;
        align-items: center;
        gap: 8px;
        justify-content: center;
        padding: 20px;
        color: #94a3b8;
        font-size: 13px;
    }

    .hrx-wt-empty i {
        font-size: 18px;
    }
</style>

<script>
    (function () {
        const addTimingBtn = document.getElementById('add_timing');
        const clearTimingsBtn = document.getElementById('clear_timings');
        const workDaySelect = document.getElementById('work_day');
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        const timingsList = document.getElementById('timings_list');
        const roleSelect = document.getElementById('role');
        const shiftSelect = document.getElementById('shift_timing');
        const slotDurationWrapper = document.getElementById('slot_duration_wrapper');
        const slotDurationInput = document.getElementById('slot_duration');

        if (!addTimingBtn || !clearTimingsBtn || !workDaySelect || !startTimeInput || !endTimeInput || !timingsList) {
            return;
        }

        if (typeof flatpickr === 'function') {
            flatpickr('#date_of_joining, #date_of_birth', { dateFormat: 'd-m-Y' });
        }

        const weekDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        const shiftMap = {
            'Day (8am-4pm)': { start: '08:00', end: '16:00' },
            'Evening (2pm-10pm)': { start: '14:00', end: '22:00' },
            'Night (10pm-8am)': { start: '22:00', end: '08:00' },
            'General': { start: '09:00', end: '18:00' }
        };

        const timingsEmpty = document.getElementById('timings_empty');

        function syncEmptyState() {
            if (!timingsEmpty) return;
            timingsEmpty.style.display = timingsList.querySelectorAll('.timing-item').length === 0 ? 'flex' : 'none';
        }

        function timingItemTemplate(day, startTime, endTime) {
            const dayLabel = day.charAt(0).toUpperCase() + day.slice(1);
            return `
                <td><span class="hrx-day-pill">${dayLabel}</span></td>
                <td><span class="hrx-time-chip hrx-time-start"><i class="fa fa-clock-o me-1"></i>${startTime}</span></td>
                <td><span class="hrx-time-chip hrx-time-end"><i class="fa fa-clock-o me-1"></i>${endTime}</span></td>
                <td class="text-end">
                    <button type="button" class="hrx-btn-remove remove-timing" data-day="${day}" title="Remove">
                        <i class="fa fa-times"></i>
                    </button>
                </td>
                <input type="hidden" name="work_timings[${day}][start_time]" value="${startTime}">
                <input type="hidden" name="work_timings[${day}][end_time]" value="${endTime}">
            `;
        }

        function upsertTimingItem(day, startTime, endTime) {
            let timingItem = timingsList.querySelector(`.timing-item[data-day="${day}"]`);
            if (!timingItem) {
                timingItem = document.createElement('tr');
                timingItem.className = 'timing-item';
                timingItem.setAttribute('data-day', day);
                timingsList.appendChild(timingItem);
            }

            timingItem.innerHTML = timingItemTemplate(day, startTime, endTime);
            syncEmptyState();
        }

        function applyShiftToAllDays(shiftValue) {
            const selectedShift = shiftMap[shiftValue];
            if (!selectedShift) {
                return;
            }

            weekDays.forEach(function (day) {
                upsertTimingItem(day, selectedShift.start, selectedShift.end);
            });

            workDaySelect.value = '';
            startTimeInput.value = '';
            endTimeInput.value = '';
        }

        function toggleDoctorSlotDuration() {
            if (!roleSelect || !slotDurationWrapper || !slotDurationInput) {
                return;
            }

            const selectedText = roleSelect.options[roleSelect.selectedIndex]
                ? roleSelect.options[roleSelect.selectedIndex].text.toLowerCase()
                : '';
            const isDoctor = selectedText.indexOf('doctor') !== -1;

            slotDurationWrapper.style.display = isDoctor ? '' : 'none';
            slotDurationInput.disabled = !isDoctor;
        }

        addTimingBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            const day = workDaySelect.value;
            const startTime = startTimeInput.value;
            const endTime = endTimeInput.value;
            const isNightShift = shiftSelect && shiftSelect.value === 'Night (10pm-8am)';

            if (!day) {
                alert('Please select a day');
                return;
            }

            if (!startTime || !endTime) {
                alert('Please enter both start and end time');
                return;
            }

            if (startTime === endTime) {
                alert('Start and end time cannot be same');
                return;
            }

            if (!isNightShift && startTime > endTime) {
                alert('End time must be after start time');
                return;
            }

            upsertTimingItem(day, startTime, endTime);
            workDaySelect.value = '';
            startTimeInput.value = '';
            endTimeInput.value = '';
        });

        clearTimingsBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            if (confirm('Are you sure you want to clear all work timings?')) {
                timingsList.innerHTML = '';
                syncEmptyState();
            }
        });

        timingsList.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-timing') || e.target.closest('.remove-timing')) {
            e.stopPropagation();
                const button = e.target.classList.contains('remove-timing') ? e.target : e.target.closest('.remove-timing');
                const day = button.getAttribute('data-day');
                if (confirm(`Remove timing for ${day.charAt(0).toUpperCase() + day.slice(1)}?`)) {
                    const item = timingsList.querySelector(`.timing-item[data-day="${day}"]`);
                    if (item) {
                        item.remove();
                        syncEmptyState();
                    }
                }
            }
        });

        if (shiftSelect) {
            shiftSelect.addEventListener('change', function () {
                applyShiftToAllDays(this.value);
            });
        }

        if (roleSelect) {
            // Select2 fires jQuery change events, not native DOM events.
            // Use jQuery listener so both select2 and native changes are caught.
            if (typeof $ !== 'undefined' && $.fn && $.fn.select2) {
                $(roleSelect).on('change', toggleDoctorSlotDuration);
            } else {
                roleSelect.addEventListener('change', toggleDoctorSlotDuration);
            }
            toggleDoctorSlotDuration();
        }

        syncEmptyState();
    })();
</script>
