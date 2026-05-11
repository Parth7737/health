<div class="modal-header" style="padding:20px 24px;border-bottom:1px solid #ccd8e8;position:sticky;top:0;background:#fff;z-index:1">
    <h5 class="modal-title" style="font-size:16px;font-weight:700;display:flex;align-items:center;gap:8px">
        <i class="fa fa-calendar-times-o" style="color:#e65100"></i> Apply for Leave
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="hrxLeaveRequestForm" data-hr-ajax-form="leave">
    <div class="modal-body" style="padding:24px">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">

            {{-- Employee --}}
            <div style="display:flex;flex-direction:column;gap:5px">
                <label style="font-size:12px;font-weight:600;color:#5a7894">Employee <span style="color:#c62828">*</span></label>
                <select class="form-select" name="staff_id" required
                        style="height:38px;border:1.5px solid #ccd8e8;border-radius:8px;font-size:13px;padding:0 10px;transition:border-color .2s;font-family:inherit">
                    <option value="">Employee name / ID</option>
                    @foreach($staffOptions as $staff)
                        <option value="{{ $staff->id }}">{{ $staff->staff_id }} – {{ trim($staff->first_name . ' ' . $staff->last_name) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Leave Type --}}
            <div style="display:flex;flex-direction:column;gap:5px">
                <label style="font-size:12px;font-weight:600;color:#5a7894">Leave Type</label>
                <select class="form-select" name="hr_leave_type_id"
                        style="height:38px;border:1.5px solid #ccd8e8;border-radius:8px;font-size:13px;padding:0 10px;transition:border-color .2s;font-family:inherit">
                    <option value="">Select type</option>
                    @foreach($leaveTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- From Date --}}
            <div style="display:flex;flex-direction:column;gap:5px">
                <label style="font-size:12px;font-weight:600;color:#5a7894">From Date <span style="color:#c62828">*</span></label>
                <input type="date" class="form-control" name="from_date" required
                       style="height:38px;border:1.5px solid #ccd8e8;border-radius:8px;font-size:13px;padding:0 12px;transition:border-color .2s;font-family:inherit">
            </div>

            {{-- To Date --}}
            <div style="display:flex;flex-direction:column;gap:5px">
                <label style="font-size:12px;font-weight:600;color:#5a7894">To Date <span style="color:#c62828">*</span></label>
                <input type="date" class="form-control" name="to_date" required
                       style="height:38px;border:1.5px solid #ccd8e8;border-radius:8px;font-size:13px;padding:0 12px;transition:border-color .2s;font-family:inherit">
            </div>

            {{-- Reason – full width --}}
            <div style="grid-column:1/-1;display:flex;flex-direction:column;gap:5px">
                <label style="font-size:12px;font-weight:600;color:#5a7894">Reason <span style="color:#c62828">*</span></label>
                <textarea class="form-control" name="reason" rows="2" maxlength="500" required
                          placeholder="Reason for leave..."
                          style="border:1.5px solid #ccd8e8;border-radius:8px;font-size:13px;padding:9px 12px;resize:vertical;min-height:68px;transition:border-color .2s;font-family:inherit"></textarea>
            </div>

            {{-- Contact During Leave --}}
            <div style="display:flex;flex-direction:column;gap:5px">
                <label style="font-size:12px;font-weight:600;color:#5a7894">Contact During Leave</label>
                <input type="text" class="form-control" name="contact_during_leave" maxlength="20"
                       placeholder="Mobile number"
                       style="height:38px;border:1.5px solid #ccd8e8;border-radius:8px;font-size:13px;padding:0 12px;transition:border-color .2s;font-family:inherit">
            </div>

            {{-- Covering Staff --}}
            <div style="display:flex;flex-direction:column;gap:5px">
                <label style="font-size:12px;font-weight:600;color:#5a7894">Covering Staff</label>
                <input type="text" class="form-control" name="covering_staff" maxlength="150"
                       placeholder="Who covers your duties"
                       style="height:38px;border:1.5px solid #ccd8e8;border-radius:8px;font-size:13px;padding:0 12px;transition:border-color .2s;font-family:inherit">
            </div>

        </div>
    </div>
    <div class="modal-footer" style="padding:16px 24px;border-top:1px solid #ccd8e8;display:flex;justify-content:flex-end;gap:10px">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                style="background:#fff;color:#4a148c;border:1.5px solid #4a148c;border-radius:8px;padding:8px 16px;font-size:13px;font-weight:600">
            Cancel
        </button>
        <button type="submit" class="btn btn-warning"
                style="background:#e65100;color:#fff;border:none;border-radius:8px;padding:8px 18px;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:6px">
            <i class="fa fa-check"></i> Submit Leave Application
        </button>
    </div>
</form>
