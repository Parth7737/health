<div class="modal-header">
    <h5 class="modal-title" id="view_modal_dataModelLabel">Add New Staff Member</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="hrxAddStaffForm" data-hr-ajax-form="staff">
    <div class="modal-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">First Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="first_name" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Last Name</label>
                <input type="text" class="form-control" name="last_name">
            </div>
            <div class="col-md-6">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" name="email" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input type="text" class="form-control" name="phone" placeholder="Max 10 digits">
            </div>
            <div class="col-md-6">
                <label class="form-label">Department</label>
                <select class="form-select" name="hr_department_id">
                    <option value="">Select Department</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Designation</label>
                <input type="text" class="form-control" name="hr_designation" placeholder="Example: Senior Nurse">
            </div>
            <div class="col-md-6">
                <label class="form-label">Date of Joining</label>
                <input type="date" class="form-control" name="date_of_joining">
            </div>
            <div class="col-md-6">
                <label class="form-label">Status <span class="text-danger">*</span></label>
                <select class="form-select" name="status" required>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Staff</button>
    </div>
</form>
