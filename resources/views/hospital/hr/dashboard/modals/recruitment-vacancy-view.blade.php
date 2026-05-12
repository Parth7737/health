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
    @php
        $statusBadge = function (string $st): string {
            $k = strtolower(trim($st));
            return match ($k) {
                'applied' => 'app-applied',
                'screening' => 'app-screening',
                'shortlisted' => 'app-shortlisted',
                'interview' => 'app-interview',
                'selected' => 'app-selected',
                'rejected' => 'app-rejected',
                'hired' => 'app-hired',
                default => 'app-applied',
            };
        };
    @endphp
    <div class="hrx-recruitment-vacancy-view-root" data-vacancy-id="{{ (int) $vacancy->id }}">
        <div class="modal-header">
            <h2><i class="fa fa-user-plus" style="color:#2e7d32"></i> {{ $vacancy->designation->name ?? $vacancy->title }}</h2>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="hrx-rv-summary">
                <div class="hrx-rv-summary-card">
                    <span class="hrx-rv-label">Department</span>
                    <span class="hrx-rv-value">{{ $vacancy->department->name ?? 'General' }}</span>
                </div>
                <div class="hrx-rv-summary-card">
                    <span class="hrx-rv-label">Vacancy status</span>
                    <span class="hrx-rv-value">{{ $vacancy->status }}</span>
                </div>
                <div class="hrx-rv-summary-card">
                    <span class="hrx-rv-label">Required</span>
                    <span class="hrx-rv-value">{{ $vacancy->required_positions }}</span>
                </div>
                <div class="hrx-rv-summary-card">
                    <span class="hrx-rv-label">Applicants</span>
                    <span class="hrx-rv-value">{{ $vacancy->applicants }}</span>
                </div>
                <div class="hrx-rv-summary-card">
                    <span class="hrx-rv-label">Shortlisted</span>
                    <span class="hrx-rv-value">{{ $vacancy->shortlisted }}</span>
                </div>
                <div class="hrx-rv-summary-card">
                    <span class="hrx-rv-label">Open period</span>
                    <span class="hrx-rv-value">{{ optional($vacancy->open_from)->format('d M Y') ?: '—' }} – {{ optional($vacancy->open_till)->format('d M Y') ?: '—' }}</span>
                </div>
            </div>

            @if(!empty($vacancy->description))
                <div class="mb-4">
                    <div class="hrx-rv-label" style="margin-bottom:6px">Description</div>
                    <div style="white-space:pre-wrap;font-size:14px;color:#1e293b;line-height:1.55;border:1px solid #e2e8f0;border-radius:10px;padding:14px 16px;background:#fafbfd">{{ $vacancy->description }}</div>
                </div>
            @endif

            <h6 class="hrx-recruitment-applicants-heading"><i class="fa fa-users"></i> Applicants</h6>
            <div class="hrx-recruitment-applicants-wrap">
                <table class="hrx-recruitment-applicants-table">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Resume</th>
                        <th>Status</th>
                        <th style="min-width:140px">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse(($vacancy->applications ?? collect()) as $app)
                        @php $sb = $statusBadge($app->status); @endphp
                        <tr class="hrx-app-main-row">
                            <td><strong>{{ $app->full_name }}</strong></td>
                            <td>{{ $app->email }}</td>
                            <td>{{ $app->phone ?: '—' }}</td>
                            <td>
                                @if($app->resume_path)
                                    <a href="{{ asset('storage/' . $app->resume_path) }}" target="_blank" rel="noopener" class="hrx-recruitment-resume-link"><i class="fa fa-file-pdf-o"></i> Resume</a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td><span class="hrx-badge {{ $sb }}">{{ $app->status }}</span></td>
                            <td>
                                <div class="hrx-recruitment-applicant-actions">
                                    <button type="button"
                                            class="hrx-recruitment-applicant-detail-btn hrx-recruitment-applicant-view-icon-btn"
                                            title="View applicant details &amp; status history"
                                            data-application-id="{{ (int) $app->id }}"
                                            aria-label="View applicant details and history">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </button>
                                    @can('edit-hr-recruitment')
                                        <button type="button"
                                                class="hrx-recruitment-status-popup-btn hrx-recruitment-applicant-status-btn"
                                                data-application-id="{{ (int) $app->id }}"
                                                data-applicant-name="{{ e($app->full_name) }}"
                                                data-current-status="{{ e($app->status) }}"
                                                data-status-note="{{ e($app->status_note ?? '') }}">
                                            <i class="fa fa-exchange"></i> Change status
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No applicants yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Close</button>
        </div>
    </div>
@endif
