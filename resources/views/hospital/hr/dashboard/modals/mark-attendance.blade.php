<style>
.hrx-att-modal-header {
    background: linear-gradient(135deg, #fdf6ff 0%, #f5e6ff 100%);
    border-bottom: 1px solid #e1bee7;
    padding: 12px 14px;
}
.hrx-att-modal-header .modal-title {
    color: #3b0070;
    font-size: 16px;
    font-weight: 700;
    letter-spacing: 0.2px;
}
.hrx-att-title-icon {
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
.hrx-att-modal .modal-body {
    padding: 12px 14px;
}
.hrx-att-modal .modal-footer {
    padding: 8px 14px;
    border-top: 1px solid #e9d5f5;
    background: linear-gradient(180deg, #fdf6ff 0%, #f5e6ff 100%);
}
.hrx-att-modal .form-label {
    font-size: 11px;
    color: #5b738d;
    font-weight: 600;
    margin-bottom: 4px;
}
.hrx-att-modal .form-control,
.hrx-att-modal .form-select {
    min-height: 34px;
    font-size: 12px;
    border: 1px solid #d5b8e8;
    border-radius: 8px;
}
.hrx-att-modal .form-control:focus,
.hrx-att-modal .form-select:focus {
    border-color: #ba68c8;
    box-shadow: 0 0 0 3px rgba(74, 20, 140, 0.12);
}
.hrx-att-modal .modal-footer .btn-secondary {
    border: 1px solid #d8b4e8;
    background: #fff;
    color: #44556b;
    font-weight: 600;
    border-radius: 9px;
    padding: 7px 16px;
}
.hrx-att-modal .modal-footer .btn-secondary:hover {
    border-color: #c27dd6;
    background: #f7fbff;
}
.hrx-att-modal .modal-footer .btn-primary {
    border: none;
    background: linear-gradient(135deg, #4a148c, #7b1fa2);
    color: #fff;
    font-weight: 700;
    border-radius: 9px;
    padding: 7px 18px;
    box-shadow: 0 8px 18px rgba(74, 20, 140, 0.28);
}
.hrx-att-modal .modal-footer .btn-primary:hover {
    background: linear-gradient(135deg, #38006b, #6a1b9a);
    box-shadow: 0 10px 20px rgba(74, 20, 140, 0.34);
}
.hrx-att-modal .select2-container--default .select2-selection--single {
    border: 1px solid #d5b8e8;
    border-radius: 8px;
    min-height: 34px;
}
.hrx-att-modal .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 32px;
    font-size: 12px;
}
.hrx-att-modal .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 32px;
}
.hrx-att-modal .hrx-bs-switch {
    margin-top: 3px;
    display: flex;
    align-items: center;
    gap: 8px;
    padding-left: 0;
}
.hrx-att-modal .hrx-bs-switch .form-check-input {
    cursor: pointer;
    width: 2.25rem !important;
    height: 1.2rem !important;
    margin: 0 !important;
    border: 1px solid #bfc7d6 !important;
    border-radius: 2rem !important;
    background-color: #d7dde8 !important;
    background-image: radial-gradient(circle, #ffffff 54%, rgba(255,255,255,0) 56%) !important;
    background-repeat: no-repeat !important;
    background-size: 0.92rem 0.92rem !important;
    background-position: left 2px center !important;
    appearance: none;
    -webkit-appearance: none;
    transition: background-color .2s ease, background-position .2s ease, border-color .2s ease;
    box-shadow: none !important;
}
.hrx-att-modal .hrx-bs-switch .form-check-input:checked {
    background-color: #6f3fc7 !important;
    border-color: #6f3fc7 !important;
    background-position: right 2px center !important;
}
.hrx-att-modal .hrx-bs-switch .form-check-input:focus {
    box-shadow: 0 0 0 2px rgba(111, 63, 199, 0.2) !important;
}
.hrx-att-modal .hrx-bs-switch .form-check-label {
    font-size: 11px;
    color: #4f6780;
    font-weight: 600;
}
.hrx-att-modal .hrx-preview-box {
    background: #f4f8ff;
    font-weight: 700;
    color: #244a74;
}
</style>

@php
    $record = $existingRecord ?? null;
    $selectedStaff = $selectedStaffId ?? ($record->staff_id ?? null);
    $selectedDate = $attendanceDate ?? ($record?->attendance_date?->toDateString() ?? now()->toDateString());
    $statusValue = $record->status ?? 'Present';
    $dayTypeValue = $record->day_type ?? '';
    $lateCountValue = (int) ($record->late_count ?? 0);
    $lateToggle = $lateCountValue > 0;
    $isMissPunch = (bool) ($record->is_miss_punch ?? false);
    $isOvertime = (bool) ($record->is_overtime ?? false);
    $otHoursValue = (float) ($record->overtime_hours ?? 0);
    $inTimeValue = $record && $record->in_time ? \Carbon\Carbon::parse($record->in_time)->format('H:i') : '';
    $outTimeValue = $record && $record->out_time ? \Carbon\Carbon::parse($record->out_time)->format('H:i') : '';
    $notesValue = $record->notes ?? '';
    $combinedPreview = $record->combined_status_label ?? 'P/FD';
@endphp

<div class="modal-header hrx-att-modal-header">
    <h5 class="modal-title d-flex align-items-center gap-2" id="view_modal_dataModelLabel">
        <span class="hrx-att-title-icon"><i class="fa fa-calendar-check-o"></i></span>
        <span>Add Attendance</span>
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="hrxAttendanceForm" data-hr-ajax-form="attendance" class="hrx-att-modal">
    <div class="modal-body">
        <div class="row g-2">
            <div class="col-md-6">
                <label class="form-label">Staff <span class="text-danger">*</span></label>
                <select class="form-select select2-modal" id="hrxAttendanceStaff" name="staff_id" required>
                    <option value="">Select Staff</option>
                    @foreach($staffOptions as $staff)
                        <option value="{{ $staff->id }}" {{ (string) $selectedStaff === (string) $staff->id ? 'selected' : '' }}>{{ $staff->staff_id }} - {{ trim($staff->first_name . ' ' . $staff->last_name) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Attendance Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="attendance_date" max="{{ now()->toDateString() }}" value="{{ $selectedDate }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Status <span class="text-danger">*</span></label>
                <select class="form-select" name="status" id="hrxAttendanceStatus" required>
                    <option value="Present" {{ $statusValue === 'Present' ? 'selected' : '' }}>Present</option>
                    <option value="Absent" {{ $statusValue === 'Absent' ? 'selected' : '' }}>Absent</option>
                    <option value="Leave" {{ $statusValue === 'Leave' ? 'selected' : '' }}>Leave</option>
                    <option value="Holiday" {{ $statusValue === 'Holiday' ? 'selected' : '' }}>Holiday</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">In Time</label>
                <input type="time" class="form-control" name="in_time" value="{{ $inTimeValue }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Out Time</label>
                <input type="time" class="form-control" name="out_time" value="{{ $outTimeValue }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">Day Type</label>
                <select class="form-select" name="day_type" id="hrxAttendanceDayType">
                    <option value="">Select Day Type</option>
                    <option value="Full Day" {{ $dayTypeValue === 'Full Day' ? 'selected' : '' }}>Full Day</option>
                    <option value="Half Day" {{ $dayTypeValue === 'Half Day' ? 'selected' : '' }}>Half Day</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label d-block">Late Count</label>
                <div class="form-check form-switch hrx-bs-switch">
                    <input class="form-check-input" type="checkbox" id="hrxAttendanceLateToggle" {{ $lateToggle ? 'checked' : '' }}>
                    <label class="form-check-label" for="hrxAttendanceLateToggle">Enable Late Count</label>
                </div>
                <input type="hidden" name="late_count" id="hrxAttendanceLateCount" value="{{ $lateToggle ? 1 : 0 }}">
            </div>

            <div class="col-md-6">
                <label class="form-label d-block">Miss Punch</label>
                <div class="form-check form-switch hrx-bs-switch">
                    <input class="form-check-input" type="checkbox" name="is_miss_punch" value="1" id="hrxAttendanceMissPunch" {{ $isMissPunch ? 'checked' : '' }}>
                    <label class="form-check-label" for="hrxAttendanceMissPunch">Enable Miss Punch</label>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label d-block">Overtime</label>
                <div class="form-check form-switch hrx-bs-switch">
                    <input class="form-check-input" type="checkbox" name="is_overtime" value="1" id="hrxAttendanceIsOvertime" {{ $isOvertime ? 'checked' : '' }}>
                    <label class="form-check-label" for="hrxAttendanceIsOvertime">Enable Overtime</label>
                </div>
            </div>

            <div class="col-md-6" id="hrxAttendanceOtHoursWrap" style="display:{{ $isOvertime ? 'block' : 'none' }};">
                <label class="form-label">OT Hours</label>
                <input type="number" class="form-control" name="overtime_hours" id="hrxAttendanceOtHours" min="0" max="24" step="0.5" value="{{ $otHoursValue }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Combined Status (Preview)</label>
                <input type="text" class="form-control hrx-preview-box" id="hrxAttendanceCombinedPreview" value="{{ $combinedPreview }}" readonly>
            </div>

            <div class="col-12">
                <label class="form-label">Notes</label>
                <textarea class="form-control" name="notes" rows="2" maxlength="255">{{ $notesValue }}</textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Attendance</button>
    </div>
</form>

<script>
(function () {
    var staffEl = document.getElementById('hrxAttendanceStaff');
    var statusEl = document.getElementById('hrxAttendanceStatus');
    var dayTypeEl = document.getElementById('hrxAttendanceDayType');
    var lateCountEl = document.getElementById('hrxAttendanceLateCount');
    var lateToggleEl = document.getElementById('hrxAttendanceLateToggle');
    var missPunchEl = document.getElementById('hrxAttendanceMissPunch');
    var overtimeEl = document.getElementById('hrxAttendanceIsOvertime');
    var overtimeHoursWrap = document.getElementById('hrxAttendanceOtHoursWrap');
    var overtimeHoursEl = document.getElementById('hrxAttendanceOtHours');
    var previewEl = document.getElementById('hrxAttendanceCombinedPreview');

    if (window.jQuery && window.jQuery.fn && typeof window.jQuery.fn.select2 === 'function' && staffEl) {
        window.jQuery(staffEl).select2({
            width: '100%',
            dropdownParent: window.jQuery('.add-datamodal')
        });
    }

    if (!statusEl || !previewEl) {
        return;
    }

    function updateVisibility() {
        var isOvertime = !!(overtimeEl && overtimeEl.checked);
        if (overtimeHoursWrap) {
            overtimeHoursWrap.style.display = isOvertime ? '' : 'none';
        }
        if (overtimeHoursEl && !isOvertime) {
            overtimeHoursEl.value = '0';
        }
    }

    function updateCombinedPreview() {
        var status = statusEl.value || 'Present';

        if (status === 'Absent') {
            previewEl.value = 'A';
            return;
        }
        if (status === 'Leave') {
            previewEl.value = 'OnLeave';
            return;
        }
        if (status === 'Holiday') {
            previewEl.value = 'H';
            return;
        }

        var parts = ['P'];
        var lateCount = parseInt((lateCountEl && lateCountEl.value) || '0', 10) || 0;
        var dayType = dayTypeEl ? dayTypeEl.value : '';
        var isMissPunch = !!(missPunchEl && missPunchEl.checked);
        var isOvertime = !!(overtimeEl && overtimeEl.checked);
        var overtimeHours = parseFloat((overtimeHoursEl && overtimeHoursEl.value) || '0') || 0;

        if (lateCount > 0) {
            parts.push('L');
        }
        if (dayType === 'Half Day') {
            parts.push('HD');
        } else if (dayType === 'Full Day') {
            parts.push('FD');
        }
        if (isMissPunch) {
            parts.push('MP');
        }
        if (isOvertime && overtimeHours > 0) {
            parts.push('OT');
        }

        previewEl.value = parts.join('/');
    }

    [statusEl, dayTypeEl, lateToggleEl, missPunchEl, overtimeEl, overtimeHoursEl].forEach(function (el) {
        if (!el) return;
        el.addEventListener('change', function () {
            if (lateToggleEl && lateCountEl) {
                lateCountEl.value = lateToggleEl.checked ? '1' : '0';
            }
            updateVisibility();
            updateCombinedPreview();
        });
        el.addEventListener('input', function () {
            if (lateToggleEl && lateCountEl) {
                lateCountEl.value = lateToggleEl.checked ? '1' : '0';
            }
            updateVisibility();
            updateCombinedPreview();
        });
    });

    if (lateToggleEl && lateCountEl) {
        lateCountEl.value = lateToggleEl.checked ? '1' : '0';
    }

    updateVisibility();
    updateCombinedPreview();
})();
</script>
