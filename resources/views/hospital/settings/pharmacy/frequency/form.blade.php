@php
    $doseCount = max(1, (int) (@$data->no_of_medicine ?? 1));
    $existingTimes = is_array(@$data->schedule_times) ? array_values($data->schedule_times) : [];
@endphp
<div class="modal-header">
    <h5 class="modal-title" id="view_modal_dataModelLabel">{{ !$id ? 'Add' : 'Edit'}} Frequency</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="savedata" enctype="multipart/form-data">
    <div class="modal-body">
        <input type="hidden" id="id" name="id" value="{{$id}}">
        <script type="application/json" id="marScheduleTimesData">@json($existingTimes)</script>

        <div class="col-md-12 mb-3">
            <label class="form-label">Frequency <span class="text-danger">*</span></label>
            <input type="text" name="frequency" id="frequency" value="{{ @$data->frequency }}" class="form-control" placeholder="e.g. TDS, BD, QID">
        </div>

        <div class="col-md-12 mb-3">
            <label class="form-label">Doses Per Day (MAR) <span class="text-danger">*</span></label>
            <input type="number" name="no_of_medicine" id="no_of_medicine" value="{{ $doseCount }}" class="form-control" min="1" max="12" step="1" placeholder="How many times per day">
            <div class="fs-11 text-muted mt-1">Kitni baar din me medicine deni hai — utne hi time slots neeche generate honge.</div>
        </div>

        <div class="col-md-12" id="marScheduleTimesSection">
            <label class="form-label">MAR Dose Times <span class="text-danger">*</span></label>
            <div id="marScheduleTimesList" class="mar-schedule-times-list"></div>
            <div class="fs-11 text-muted mt-2">
                Nursing MAR inhi <b>exact times</b> use karega. Meal instruction sirf label ke liye dikhegi.
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>

<style>
.mar-schedule-times-list .mar-time-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 8px;
}
.mar-schedule-times-list .mar-dose-label {
    min-width: 72px;
    font-size: 12px;
    font-weight: 600;
    color: var(--text-muted, #666);
}
.mar-schedule-times-list .mar-schedule-time-input {
    max-width: 160px;
}
</style>
