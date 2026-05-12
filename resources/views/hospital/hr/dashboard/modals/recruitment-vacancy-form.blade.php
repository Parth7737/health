@php
    $v = $vacancy ?? null;
    $isEdit = $v !== null;
@endphp
@include('hospital.hr.dashboard.modals.partials.hrx-recruitment-modal-styles')
<form id="hrxRecruitmentVacancyForm">
    @csrf
    @if($isEdit)
        <input type="hidden" name="id" value="{{ $v->id }}">
    @endif
    <div class="modal-header">
        <h2>
            <i class="fa fa-user-plus" style="color:#2e7d32"></i>
            {{ $isEdit ? 'Edit Vacancy' : 'Post Vacancy' }}
        </h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Position (Designation)</label>
                <select name="hr_designation_id" class="form-control">
                    <option value="">Select designation</option>
                    @foreach(($designations ?? collect()) as $designation)
                        <option value="{{ $designation->id }}" @selected(optional($v)->hr_designation_id == $designation->id)>{{ $designation->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Department</label>
                <select name="department_id" class="form-control">
                    <option value="">Select department</option>
                    @foreach(($departments ?? collect()) as $department)
                        <option value="{{ $department->id }}" @selected(optional($v)->department_id == $department->id)>{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Required Positions</label>
                <input type="number" min="1" name="required_positions" class="form-control" value="{{ optional($v)->required_positions ?? 1 }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-control" required>
                    @php $st = optional($v)->status ?? 'Open'; @endphp
                    <option value="Open" @selected($st === 'Open')>Open</option>
                    <option value="On Hold" @selected($st === 'On Hold')>On Hold</option>
                    <option value="Closed" @selected($st === 'Closed')>Closed</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Open From</label>
                <input type="date" name="open_from" class="form-control" value="{{ optional(optional($v)->open_from)->toDateString() }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Open Till</label>
                <input type="date" name="open_till" class="form-control" value="{{ optional(optional($v)->open_till)->toDateString() ?: optional(optional($v)->last_date)->toDateString() }}">
            </div>
            <div class="col-12 mb-3">
                <label class="form-label">Title (optional)</label>
                <input type="text" name="title" class="form-control" value="{{ optional($v)->title ?? '' }}" placeholder="If blank, designation name will be used">
            </div>
            <div class="col-12 mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Role requirements, experience, skills...">{{ optional($v)->description ?? '' }}</textarea>
            </div>
            <div class="col-12">
                <label class="form-check-label" style="font-size:13px">
                    <input type="checkbox" name="is_published" value="1" @checked(optional($v)->is_published ?? true)> Publish on careers page
                </label>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-recruit-submit"><i class="fa fa-check"></i> {{ $isEdit ? 'Update Vacancy' : 'Post Vacancy' }}</button>
    </div>
</form>
