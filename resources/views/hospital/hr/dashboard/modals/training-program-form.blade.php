@php
    $p = $program ?? null;
    $isEdit = $p !== null;
    $cats = $trainingCategories ?? collect();
    $hasTrainingCategoryMaster = $hasTrainingCategoryMaster ?? false;
@endphp
@include('hospital.hr.dashboard.modals.partials.hrx-training-modal-styles')
<form id="hrxTrainingProgramForm">
    @csrf
    @if($isEdit)
        <input type="hidden" name="id" value="{{ $p->id }}">
    @endif
    <div class="modal-header">
        <h2>
            <i class="fa fa-graduation-cap" style="color:#4a148c"></i>
            {{ $isEdit ? 'Edit training programme' : 'Schedule training programme' }}
        </h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        @if($hasTrainingCategoryMaster)
            @if($cats->isEmpty())
                <div class="alert alert-warning mb-3" style="border-radius:12px;font-size:13px">
                    No training categories found. Please add categories under
                    @can('view-hr-training-category')
                        <a href="{{ route('hospital.settings.hr.training-category.index') }}" target="_blank" rel="noopener">Settings → HR → Training categories</a>
                    @else
                        Settings → HR → Training categories
                    @endcan
                    before scheduling.
                </div>
            @endif
        @endif

        <div class="row g-3">
            <div class="col-lg-8">
                <label class="form-label">Programme title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" value="{{ old('title', optional($p)->title) }}" required maxlength="200" placeholder="e.g. Fire safety &amp; evacuation drill">
            </div>
            <div class="col-lg-4">
                <label class="form-label">Scheduled date <span class="text-danger">*</span></label>
                <input type="date" name="schedule_date" class="form-control" value="{{ old('schedule_date', optional(optional($p)->schedule_date)->toDateString()) }}" required>
            </div>

            @if($hasTrainingCategoryMaster)
                <div class="col-lg-6">
                    <label class="form-label">Training category</label>
                    <select name="hr_training_category_id" class="form-control" {{ $cats->isEmpty() ? 'disabled' : '' }}>
                        <option value="">— Select category —</option>
                        @foreach($cats as $c)
                            <option value="{{ (int) $c->id }}" @selected((int) optional($p)->hr_training_category_id === (int) $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <div class="col-lg-6">
                    <label class="form-label">Category (legacy)</label>
                    <input type="text" name="category" class="form-control" value="{{ old('category', optional($p)->category) }}" maxlength="120" placeholder="Free text if master list not migrated">
                </div>
            @endif

            <div class="col-lg-6">
                <label class="form-label">Trainer</label>
                <input type="text" name="trainer_name" class="form-control" value="{{ old('trainer_name', optional($p)->trainer_name) }}" maxlength="150" placeholder="Name or external agency">
            </div>
            <div class="col-md-4">
                <label class="form-label">Duration (hours)</label>
                <input type="number" name="duration_hours" class="form-control" min="1" max="500" value="{{ old('duration_hours', optional($p)->duration_hours) }}" placeholder="Optional">
            </div>
            <div class="col-md-8">
                <label class="form-label">Venue / link</label>
                <input type="text" name="venue" class="form-control" value="{{ old('venue', optional($p)->venue) }}" maxlength="191" placeholder="Room, auditorium, or online meeting link">
            </div>
            <div class="col-12">
                <label class="form-label">Description &amp; learning objectives</label>
                <textarea name="description" class="form-control" rows="5" maxlength="5000" placeholder="Outline topics, materials, prerequisites, assessment…">{{ old('description', optional($p)->description) }}</textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-training-submit"><i class="fa fa-check"></i> {{ $isEdit ? 'Save changes' : 'Schedule programme' }}</button>
    </div>
</form>
