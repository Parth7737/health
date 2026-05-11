@forelse($staff as $row)
    @php
        $fullName = trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? ''));
        $departmentName = $row->department->name ?? 'Unassigned';
        $designationName = $row->designation->name ?? 'Unassigned';
        $staffId = $row->staff_id ?: 'N/A';
        $statusLabel = $row->status ?: 'Unknown';
    @endphp
    <div class="hrx-staff-card hrx-directory-card" data-name="{{ strtolower($fullName) }}" data-email="{{ strtolower($row->email ?? '') }}" data-staff-id="{{ strtolower($staffId) }}" data-department="{{ strtolower($departmentName) }}" data-status="{{ strtolower($statusLabel) }}">
        <div class="hrx-avatar">{{ strtoupper(substr($row->first_name ?? 'X', 0, 1) . substr($row->last_name ?? 'X', 0, 1)) }}</div>
        <div style="font-weight:700">{{ $fullName !== '' ? $fullName : 'Unnamed Staff' }}</div>
        <div style="font-size:11px;color:#5a7894">{{ $designationName }} - {{ $departmentName }}</div>
        <div style="font-size:10px;color:#8fa4ba;margin-top:2px">ID: {{ $staffId }}</div>
        <div style="margin-top:6px">
            <span class="hrx-badge {{ strtolower($statusLabel) === 'active' ? 'green' : 'red' }}">{{ $statusLabel }}</span>
        </div>
        <div class="hrx-actions" style="justify-content:center; gap:6px; margin-top:8px;">
            @can('view-staff')
                <button type="button" class="hrx-btn-lite hrx-staff-view" data-id="{{ $row->id }}" title="View">
                    <i class="fa fa-eye"></i>
                </button>
            @endcan
            @can('edit-staff')
                <button type="button" class="hrx-btn-lite hrx-staff-edit" data-id="{{ $row->id }}" title="Edit">
                    <i class="fa fa-pen"></i>
                </button>
            @endcan
            @can('delete-staff')
                <button type="button" class="hrx-btn-lite hrx-staff-delete" data-id="{{ $row->id }}" title="Delete">
                    <i class="fa fa-trash"></i>
                </button>
            @endcan
        </div>
    </div>
@empty
    @if(!empty($showEmpty))
        <div class="hrx-loading">No staff records found.</div>
    @endif
@endforelse
