@include('hospital.hr.dashboard.modals.partials.hrx-training-modal-styles')
@php
    $program = $program ?? null;
    $canEdit = $trainingCanEdit ?? false;
    $staffOptions = $staffOptions ?? collect();
    $enrolledIds = $program ? $program->trainingParticipants->pluck('staff_id')->all() : [];
    $stLower = $program ? strtolower((string) $program->status) : '';
    $logEventClass = function (string $type): string {
        return match ($type) {
            'status_changed', 'program_created', 'program_updated' => 'ev-status',
            'participant_added', 'participant_removed' => 'ev-participant',
            'certificates_issued', 'certificate_generated', 'certificate_regenerated' => 'ev-cert',
            default => '',
        };
    };
    $logEventLabel = function (string $type): string {
        return match ($type) {
            'program_created' => 'Created',
            'program_updated' => 'Updated',
            'status_changed' => 'Status',
            'participant_added' => 'Participant',
            'participant_removed' => 'Participant',
            'certificates_issued' => 'Certificates',
            'certificate_generated' => 'Certificate',
            'certificate_regenerated' => 'Certificate',
            default => 'Activity',
        };
    };
@endphp
@if(!$program)
    <div class="modal-header">
        <h2><i class="fa fa-graduation-cap" style="color:#4a148c"></i> Training</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <p class="text-danger mb-0">Programme not found.</p>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
    </div>
@else
    <div class="hrx-training-program-view-root" data-training-id="{{ (int) $program->id }}">
        <div class="modal-header">
            <h2><i class="fa fa-graduation-cap" style="color:#4a148c"></i> Programme overview</h2>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="hrx-training-hero">
                <div>
                    <div class="hrx-training-hero-title">{{ $program->title }}</div>
                    <div class="hrx-training-hero-meta">
                        <span class="hrx-badge {{ $program->status === 'Completed' ? 'green' : ($program->status === 'Cancelled' ? 'red' : 'blue') }}" style="margin-right:8px">{{ $program->status }}</span>
                        {{ optional($program->schedule_date)->format('l, d M Y') ?: '—' }}
                        @if($program->duration_hours)
                            · {{ $program->duration_hours }} h
                        @endif
                    </div>
                </div>
            </div>

            <div class="hrx-training-summary-grid">
                <div class="hrx-training-summary-card">
                    <div class="lbl">Category</div>
                    <div class="val">{{ $program->categoryDisplay() ?: '—' }}</div>
                </div>
                <div class="hrx-training-summary-card">
                    <div class="lbl">Trainer</div>
                    <div class="val">{{ $program->trainer_name ?: '—' }}</div>
                </div>
                <div class="hrx-training-summary-card">
                    <div class="lbl">Participants</div>
                    <div class="val">{{ (int) ($program->participants ?? 0) }}</div>
                </div>
                <div class="hrx-training-summary-card">
                    <div class="lbl">Venue</div>
                    <div class="val">{{ $program->venue ?: '—' }}</div>
                </div>
            </div>

            @if(!empty($program->description))
                <div class="mb-2">
                    <div class="lbl" style="font-size:10px;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px">Description</div>
                    <div style="white-space:pre-wrap;font-size:14px;color:#1e293b;line-height:1.55;border:1px solid #e8e0f0;border-radius:12px;padding:14px 16px;background:#fff">{{ $program->description }}</div>
                </div>
            @endif

            <div class="hrx-training-section">
                <h6 class="hrx-training-section-title"><i class="fa fa-users"></i> Participants</h6>
                @if($canEdit && $stLower === 'scheduled')
                    <div class="hrx-training-add-row">
                        <div class="flex-grow-1" style="min-width:220px">
                            <label class="form-label">Add staff</label>
                            <select id="hrxTrainingAddStaff" class="form-control">
                                <option value="">Select active staff…</option>
                                @foreach($staffOptions as $s)
                                    @if(!in_array((int) $s->id, $enrolledIds, true))
                                        <option value="{{ (int) $s->id }}">{{ $s->full_name }} ({{ $s->staff_id }})</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <button type="button" class="btn btn-training-submit hrx-training-add-participant-btn" style="margin-bottom:2px"><i class="fa fa-user-plus"></i> Add to roster</button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="hrx-training-table">
                        <thead>
                        <tr>
                            <th>Participant</th>
                            <th>Employee ID</th>
                            <th>Certificate</th>
                            <th style="min-width:200px">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($program->trainingParticipants as $row)
                            <tr>
                                <td><strong>{{ $row->staff?->full_name ?? '—' }}</strong></td>
                                <td>{{ $row->staff?->staff_id ?? '—' }}</td>
                                <td>
                                    @if($row->certificate_path && $row->certificate_number)
                                        <span style="font-size:12px;font-weight:700;color:#2e7d32"><i class="fa fa-certificate"></i> {{ $row->certificate_number }}</span>
                                    @else
                                        <span class="text-muted" style="font-size:12px">Not generated</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="hrx-training-cert-actions">
                                        @if($stLower === 'completed' && $canEdit)
                                            <button type="button" class="hrx-btn-lite hrx-training-generate-cert-btn" data-participant-id="{{ (int) $row->id }}" data-regenerate="0" title="Generate PDF">
                                                <i class="fa fa-file-pdf-o"></i> Generate PDF
                                            </button>
                                            @if($row->certificate_path)
                                                <button type="button" class="hrx-btn-lite hrx-training-generate-cert-btn" data-participant-id="{{ (int) $row->id }}" data-regenerate="1" title="Replace PDF">
                                                    <i class="fa fa-refresh"></i> Regenerate
                                                </button>
                                            @endif
                                        @endif
                                        @if($row->certificate_path)
                                            <a href="{{ route('hospital.hr.dashboard.training-certificate', ['participantId' => $row->id]) }}" class="hrx-btn-lite" style="text-decoration:none" target="_blank" rel="noopener"><i class="fa fa-download"></i> Download</a>
                                        @endif
                                        @if($canEdit && $stLower === 'scheduled')
                                            <button type="button" class="hrx-btn-lite text-danger hrx-training-remove-participant-btn" data-participant-id="{{ (int) $row->id }}"><i class="fa fa-times"></i> Remove</button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-muted text-center py-4">No participants enrolled yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($canEdit && $stLower === 'scheduled')
                <div class="hrx-training-section">
                    <h6 class="hrx-training-section-title"><i class="fa fa-flag-checkered"></i> Close programme</h6>
                    <p class="text-muted" style="font-size:12px;margin-bottom:12px">Mark as completed when the programme has finished. Certificate PDFs are not auto-generated—use Generate PDF on each participant when you are ready. Cancellation is logged with your note.</p>
                    <div class="hrx-training-status-row">
                        <div>
                            <label class="form-label">Next status</label>
                            <select id="hrxTrainingStatusNext" class="form-control">
                                <option value="">— Select —</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="flex-grow-1" style="min-width:240px">
                            <label class="form-label">Note (optional)</label>
                            <input type="text" id="hrxTrainingStatusNote" class="form-control" maxlength="1000" placeholder="Visible in activity log">
                        </div>
                        <button type="button" class="btn btn-training-submit hrx-training-update-status-btn"><i class="fa fa-save"></i> Update status</button>
                    </div>
                </div>
            @endif

            <div class="hrx-training-section">
                <h6 class="hrx-training-section-title"><i class="fa fa-history"></i> Activity log</h6>
                @if(($program->logs ?? collect())->isEmpty())
                    <p class="text-muted" style="font-size:13px">No activity recorded yet.</p>
                @else
                    <ul class="hrx-training-log-list">
                        @foreach($program->logs as $log)
                            <li class="hrx-training-log-item {{ $logEventClass($log->event_type) }}">
                                <span class="hrx-training-log-badge">{{ $logEventLabel($log->event_type) }}</span>
                                <div class="hrx-training-log-when">{{ optional($log->created_at)->format('d M Y, h:i A') }}</div>
                                <div class="hrx-training-log-msg">{{ $log->message }}</div>
                                @if(!empty($log->note))
                                    <div class="hrx-training-log-note">{{ $log->note }}</div>
                                @endif
                                <div class="hrx-training-log-by">
                                    @if($log->created_by && $log->creator)
                                        Recorded by {{ $log->creator->name }}
                                    @else
                                        System / careers
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
        </div>
    </div>
@endif
