<style>
    .add-datamodal .modal-dialog.hrx-leave-modal-dialog {
        max-width: 680px;
        width: 90%;
    }
    .add-datamodal .modal-content.hrx-leave-modal-content {
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #ccd8e8;
        box-shadow: 0 20px 60px rgba(0,0,0,.2);
    }
    .hrx-leave-modal-content .modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid #ccd8e8;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        background: #fff;
        z-index: 1;
    }
    .hrx-leave-modal-content .modal-header h2 {
        font-size: 16px;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
    }
    .hrx-leave-modal-content .btn-close {
        background: none;
        border: none;
        color: #5a7894;
        opacity: 1;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        transition: all .22s cubic-bezier(.4,0,.2,1);
        box-shadow: none;
    }
    .hrx-leave-modal-content .btn-close:hover {
        background: #f3e5f5;
        color: #4a148c;
        transform: rotate(90deg);
    }
    .hrx-leave-modal-content .btn-close:focus {
        box-shadow: 0 0 0 3px rgba(74,20,140,.16);
    }
    .hrx-leave-modal-content .modal-body { padding: 24px; }
    .hrx-leave-modal-content .modal-footer {
        padding: 16px 24px;
        border-top: 1px solid #ccd8e8;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    .hrx-leave-modal-content .form-group { display: flex; flex-direction: column; gap: 5px; margin-bottom: 12px; }
    .hrx-leave-modal-content .form-group label { font-size: 12px; font-weight: 600; color: #5a7894; }
    .hrx-leave-modal-content .form-group input,
    .hrx-leave-modal-content .form-group select,
    .hrx-leave-modal-content .form-group textarea {
        padding: 9px 12px;
        border: 1.5px solid #ccd8e8;
        border-radius: 8px;
        font-size: 13px;
        font-family: 'Inter', sans-serif;
        outline: none;
        transition: border-color .22s cubic-bezier(.4,0,.2,1), box-shadow .22s cubic-bezier(.4,0,.2,1), background-color .22s cubic-bezier(.4,0,.2,1);
        background: #fff;
    }
    .hrx-leave-modal-content .form-group input,
    .hrx-leave-modal-content .form-group select { height: 40px; }
    .hrx-leave-modal-content .form-group input:focus,
    .hrx-leave-modal-content .form-group select:focus,
    .hrx-leave-modal-content .form-group textarea:focus {
        border-color: #4a148c;
        box-shadow: 0 0 0 3px rgba(74,20,140,.10);
    }
    .hrx-leave-modal-content .form-group input:hover,
    .hrx-leave-modal-content .form-group select:hover,
    .hrx-leave-modal-content .form-group textarea:hover {
        border-color: #b7c9de;
    }
    .hrx-leave-modal-content .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .hrx-leave-modal-content .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all .22s cubic-bezier(.4,0,.2,1);
        text-decoration: none;
    }
    .hrx-leave-modal-content .btn-outline {
        background: #fff;
        color: #4a148c;
        border: 1.5px solid #4a148c;
    }
    .hrx-leave-modal-content .btn-outline:hover {
        background: #f3e5f5;
        color: #3b0070;
        transform: translateY(-1px);
    }
    .hrx-leave-modal-content .btn-warning {
        background: #e65100;
        color: #fff;
    }
    .hrx-leave-modal-content .btn-warning:hover {
        background: #bf360c;
        color: #fff;
        transform: translateY(-1px);
    }
    .hrx-leave-modal-content .btn:focus {
        box-shadow: 0 0 0 3px rgba(74,20,140,.16);
    }
    .hrx-leave-modal-content .btn:active {
        transform: translateY(0);
    }

    .hrx-leave-modal-content .select2-container {
        width: 100% !important;
    }
    .hrx-leave-modal-content .select2-container--default .select2-selection--single {
        height: 40px;
        border: 1.5px solid #ccd8e8;
        border-radius: 8px;
        font-size: 13px;
        font-family: 'Inter', sans-serif;
        transition: border-color .22s cubic-bezier(.4,0,.2,1), box-shadow .22s cubic-bezier(.4,0,.2,1);
        background: #fff;
    }
    .hrx-leave-modal-content .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
        color: #1f2937;
        padding-left: 12px;
        padding-right: 34px;
    }
    .hrx-leave-modal-content .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #6b7280;
    }
    .hrx-leave-modal-content .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px;
        right: 8px;
    }
    .hrx-leave-modal-content .select2-container--default.select2-container--focus .select2-selection--single,
    .hrx-leave-modal-content .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #4a148c;
        box-shadow: 0 0 0 3px rgba(74,20,140,.10);
    }
    .hrx-leave-modal-content .select2-container--default:not(.select2-container--open) .select2-selection--single:hover {
        border-color: #b7c9de;
    }
    .hrx-leave-select-dropdown .select2-results__option {
        font-size: 13px;
        padding: 8px 12px;
    }
    .hrx-leave-select-dropdown .select2-results__option--highlighted[aria-selected] {
        background: #4a148c;
        color: #fff;
    }
    @media (max-width: 767px) {
        .hrx-leave-modal-content .grid-2 { grid-template-columns: 1fr; }
    }
</style>

<div class="modal-header">
    <h2><i class="fa fa-calendar-times" style="color:#e65100;margin-right:8px"></i> Apply for Leave</h2>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form id="hrxLeaveRequestForm" data-hr-ajax-form="leave">
    <div class="modal-body">
        <div class="grid-2">
            <div class="form-group">
                <label>Employee <span style="color:#c62828">*</span></label>
                <select name="staff_id" class="select2-modal" required>
                    <option value="">Employee name / ID</option>
                    @foreach($staffOptions as $staff)
                        <option value="{{ $staff->id }}">{{ $staff->staff_id }} - {{ trim($staff->first_name . ' ' . $staff->last_name) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Leave Type</label>
                <select name="hr_leave_type_id" class="select2-modal">
                    <option value="">Select Leave Type</option>
                    @foreach($leaveTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>From Date <span style="color:#c62828">*</span></label>
                <input type="date" name="from_date" required>
            </div>
            <div class="form-group">
                <label>To Date <span style="color:#c62828">*</span></label>
                <input type="date" name="to_date" required>
            </div>
            <div class="form-group" style="grid-column:1/-1">
                <label>Reason <span style="color:#c62828">*</span></label>
                <textarea name="reason" rows="2" maxlength="500" placeholder="Reason for leave..." required></textarea>
            </div>
            <div class="form-group">
                <label>Contact During Leave</label>
                <input type="text" name="contact_during_leave" maxlength="20" placeholder="Mobile number">
            </div>
            <div class="form-group">
                <label>Covering Staff</label>
                <input type="text" name="covering_staff" maxlength="150" placeholder="Who covers your duties">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-warning"><i class="fa fa-check"></i> Submit Leave Application</button>
    </div>
</form>
