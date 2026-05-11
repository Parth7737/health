<div id="hrx-panel-training">
    <div class="hrx-toolbar">
        <div class="hrx-filters">
            <input type="text" id="hrxTrainingSearch" class="hrx-input" placeholder="Search programme">
            <select id="hrxTrainingStatus" class="hrx-select">
                <option value="">All Status</option>
                <option value="scheduled">Scheduled</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
        <div class="hrx-actions">
            <button type="button" class="hrx-btn-lite" id="hrxTrainingSchedule"><i class="fa fa-calendar-plus-o"></i>Schedule</button>
            <button type="button" class="hrx-btn-lite" id="hrxTrainingExport"><i class="fa fa-download"></i>Export</button>
        </div>
    </div>
    <div class="hrx-card">
        <div class="hrx-card-header">
            <div class="hrx-card-title"><i class="fa fa-graduation-cap" style="color:#4a148c"></i>Training & CPD Programmes</div>
        </div>
        <div class="hrx-table-wrap">
            <table class="hrx-table">
                <thead>
                <tr>
                    <th>Programme</th>
                    <th>Category</th>
                    <th>Date</th>
                    <th>Trainer</th>
                    <th>Participants</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @forelse($programs as $row)
                    <tr class="hrx-training-row" data-title="{{ strtolower($row->title) }}" data-status="{{ strtolower($row->status) }}">
                        <td>{{ $row->title }}</td>
                        <td>{{ $row->category ?: '-' }}</td>
                        <td>{{ optional($row->schedule_date)->format('d M Y') }}</td>
                        <td>{{ $row->trainer_name ?: '-' }}</td>
                        <td>{{ $row->participants }}</td>
                        <td><span class="hrx-badge {{ $row->status === 'Completed' ? 'green' : ($row->status === 'Cancelled' ? 'red' : 'blue') }}">{{ $row->status }}</span></td>
                        <td><button type="button" class="hrx-btn-lite hrx-training-view" data-title="{{ $row->title }}"><i class="fa fa-eye"></i></button></td>
                    </tr>
                @empty
                    <tr><td colspan="7">No training programmes found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
