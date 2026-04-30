<div class="modal-header">
    <h5 class="modal-title" id="view_modal_dataModelLabel">{{ !$id ? 'Add New' : 'Edit'}} Staff Member</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="savedata" enctype="multipart/form-data">
    <div class="modal-body">   
        <input type="hidden" id="id" name="id" value="{{ $id ?? '' }}">
        
        <!-- Professional Information Section -->
        <div class="section-title mb-3 mt-4">
            <h6 class="fw-bold text-primary">Professional Information</h6>
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
            <h6 class="fw-bold text-primary">Personal Information</h6>
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
            <h6 class="fw-bold text-primary">Address Information</h6>
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
            <h6 class="fw-bold text-primary">Professional Details</h6>
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
        <!-- Leaves Section -->
        <div class="section-title mb-3 mt-4">
            <h6 class="fw-bold text-primary">Leaves</h6>
        </div>
        <div class="row g-2 mt-2">
            <div class="col-md-3">
                <label class="form-label">Paid Time Off (PTO) </label>
                <input type="number" name="pto" id="pto" value="{{ @$data->pto }}" class="form-control" placeholder="PTO days per year">
            </div>
            <div class="col-md-3">
                <label class="form-label">Vacation </label>
                <input type="number" name="vacation" id="vacation" value="{{ @$data->vacation }}" class="form-control" placeholder="Vacation days per year">
            </div>
            <div class="col-md-3">
                <label class="form-label">Family and Medical Leaves (FMLA) </label>
                <input type="number" name="fmla" id="fmla" value="{{ @$data->fmla }}" class="form-control" placeholder="FMLA days per year">
            </div>
            <div class="col-md-3">
                <label class="form-label">Continuing Education Leaves </label>
                <input type="number" name="education_leave" id="education_leave" value="{{ @$data->education_leave }}" class="form-control" placeholder="Education leave days per year">
            </div>
        </div>
            
        <!-- Leaves Section -->
        <div class="section-title mb-3 mt-4">
            <h6 class="fw-bold text-primary">Bank Details</h6>
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
        <div class="section-title mb-3 mt-4">
            <h6 class="fw-bold text-primary">Work Timings</h6>
        </div>

        <div class="row g-2">
            <div class="col-md-12">
                <div class="work-timings-container">
                    
                    <div class="col-md-2">
                        <label class="form-label">Slot Duration (Minutes)</label>
                        <input type="number" name="slot_duration" id="slot_duration" value="{{ @$data->slot_duration }}" class="form-control" placeholder="In Minutes">
                    </div>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-md-3">
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
                        <div class="col-md-3">
                            <label class="form-label">Start Time</label>
                            <input type="time" class="form-control" id="start_time">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">End Time</label>
                            <input type="time" class="form-control" id="end_time">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-success me-2" id="add_timing">
                                <i class="fa fa-plus me-1"></i>Add
                            </button>
                            <button type="button" class="btn btn-warning" id="clear_timings">
                                <i class="fa fa-trash me-1"></i>Clear All
                            </button>
                        </div>
                    </div>

                    <div class="timings-list" id="timings_list">
                        @if(@$data->work_timings)
                            @php $timings = json_decode($data->work_timings, true); @endphp
                            @if(is_array($timings))
                                @foreach($timings as $day => $timing)
                                    <div class="timing-item card p-3 mb-2" data-day="{{ $day }}">
                                        <div class="row align-items-center">
                                            <div class="col-md-3">
                                                <strong class="text-capitalize">{{ $day }}</strong>
                                            </div>
                                            <div class="col-md-3">
                                                <span class="badge bg-primary">{{ $timing['start_time'] ?? 'N/A' }}</span>
                                            </div>
                                            <div class="col-md-3">
                                                <span class="badge bg-success">{{ $timing['end_time'] ?? 'N/A' }}</span>
                                            </div>
                                            <div class="col-md-3">
                                                <button type="button" class="btn btn-sm btn-danger remove-timing" data-day="{{ $day }}">
                                                    <i class="fa fa-times"></i> Remove
                                                </button>
                                            </div>
                                        </div>
                                        <input type="hidden" name="work_timings[{{ $day }}][start_time]" value="{{ $timing['start_time'] ?? '' }}">
                                        <input type="hidden" name="work_timings[{{ $day }}][end_time]" value="{{ $timing['end_time'] ?? '' }}">
                                    </div>
                                @endforeach
                            @endif
                        @endif
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr('input[type="date"]', { dateFormat: 'd-m-Y' });

        // Work Timings functionality
        const addTimingBtn = document.getElementById('add_timing');
        const clearTimingsBtn = document.getElementById('clear_timings');
        const workDaySelect = document.getElementById('work_day');
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        const timingsList = document.getElementById('timings_list');

        // Add timing
        addTimingBtn.addEventListener('click', function() {
            const day = workDaySelect.value;
            const startTime = startTimeInput.value;
            const endTime = endTimeInput.value;

            if (!day) {
                alert('Please select a day');
                return;
            }

            if (!startTime || !endTime) {
                alert('Please enter both start and end time');
                return;
            }

            if (startTime >= endTime) {
                alert('End time must be after start time');
                return;
            }

            // Check if day already exists
            if (document.querySelector(`.timing-item[data-day="${day}"]`)) {
                alert('Timing for this day already exists. Remove it first to update.');
                return;
            }

            addTimingItem(day, startTime, endTime);

            // Clear inputs
            workDaySelect.value = '';
            startTimeInput.value = '';
            endTimeInput.value = '';
        });

        // Clear all timings
        clearTimingsBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to clear all work timings?')) {
                timingsList.innerHTML = '';
            }
        });

        // Remove timing (event delegation)
        timingsList.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-timing') || e.target.closest('.remove-timing')) {
                const button = e.target.classList.contains('remove-timing') ? e.target : e.target.closest('.remove-timing');
                const day = button.getAttribute('data-day');
                if (confirm(`Remove timing for ${day.charAt(0).toUpperCase() + day.slice(1)}?`)) {
                    document.querySelector(`.timing-item[data-day="${day}"]`).remove();
                }
            }
        });

        function addTimingItem(day, startTime, endTime) {
            const timingItem = document.createElement('div');
            timingItem.className = 'timing-item card p-3 mb-2';
            timingItem.setAttribute('data-day', day);

            timingItem.innerHTML = `
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <strong class="text-capitalize">${day}</strong>
                    </div>
                    <div class="col-md-3">
                        <span class="badge bg-primary">${startTime}</span>
                    </div>
                    <div class="col-md-3">
                        <span class="badge bg-success">${endTime}</span>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-sm btn-danger remove-timing" data-day="${day}">
                            <i class="fa fa-times"></i> Remove
                        </button>
                    </div>
                </div>
                <input type="hidden" name="work_timings[${day}][start_time]" value="${startTime}">
                <input type="hidden" name="work_timings[${day}][end_time]" value="${endTime}">
            `;

            timingsList.appendChild(timingItem);
        }

        // Pre-populate timings if editing
        @if(@$data->work_timings)
            @php $timings = json_decode($data->work_timings, true); @endphp
            @if(is_array($timings))
                // Timings are already rendered in the blade template
            @endif
        @endif
    });
</script>
