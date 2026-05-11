@include('hospital.hr.dashboard.modals.partials.hrx-recruitment-modal-styles')
@if(!$vacancy)
    <div class="modal-header">
        <h2><i class="fa fa-user-plus" style="color:#2e7d32"></i> Vacancy Details</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <p class="text-danger mb-0">Vacancy not found.</p>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Close</button>
    </div>
@else
    <div class="modal-header">
        <h2><i class="fa fa-user-plus" style="color:#2e7d32"></i> {{ $vacancy->designation->name ?? $vacancy->title }}</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="row mb-3">
            <div class="col-md-6"><strong>Department:</strong> {{ $vacancy->department->name ?? 'General' }}</div>
            <div class="col-md-6"><strong>Status:</strong> {{ $vacancy->status }}</div>
            <div class="col-md-6"><strong>Required:</strong> {{ $vacancy->required_positions }}</div>
            <div class="col-md-6"><strong>Applicants:</strong> {{ $vacancy->applicants }}</div>
            <div class="col-md-6"><strong>Shortlisted:</strong> {{ $vacancy->shortlisted }}</div>
            <div class="col-md-6"><strong>Open:</strong> {{ optional($vacancy->open_from)->format('d M Y') ?: '-' }} to {{ optional($vacancy->open_till)->format('d M Y') ?: '-' }}</div>
        </div>
        @if(!empty($vacancy->description))
            <div class="mb-3">
                <strong>Description:</strong>
                <div style="white-space:pre-wrap">{{ $vacancy->description }}</div>
            </div>
        @endif
        <hr>
        <h6 class="mb-2">Applicants</h6>
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Resume</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(($vacancy->applications ?? collect()) as $app)
                        <tr>
                            <td>{{ $app->full_name }}</td>
                            <td>{{ $app->email }}</td>
                            <td>{{ $app->phone ?: '-' }}</td>
                            <td>
                                @if($app->resume_path)
                                    <a href="{{ asset('storage/' . $app->resume_path) }}" target="_blank">View</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $app->status }}</td>
                            <td style="min-width:220px">
                                @can('edit-hr-recruitment')
                                <form class="hrxRecruitmentAppStatusForm d-flex" style="gap:6px;align-items:center;">
                                    @csrf
                                    <input type="hidden" name="application_id" value="{{ $app->id }}">
                                    <select name="status" class="form-control form-control-sm" required>
                                        @foreach(['Applied','Screening','Shortlisted','Interview','Selected','Rejected','Hired'] as $status)
                                            <option value="{{ $status }}" @selected($app->status === $status)>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="status_note" class="form-control form-control-sm" value="{{ $app->status_note }}" placeholder="Note">
                                    <button class="btn btn-sm btn-recruit-save" type="submit">Save</button>
                                </form>
                                @else
                                <span class="text-muted" style="font-size:12px">View only</span>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">No applicants yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Close</button>
    </div>
@endif
