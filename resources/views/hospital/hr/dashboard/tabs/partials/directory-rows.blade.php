@forelse($staff as $row)
    @php
        $fullName = trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? ''));
        $departmentName = $row->department->name ?? 'Unassigned';
        $designationName = $row->designation->name ?? 'Unassigned';
        $staffId = $row->staff_id ?: 'N/A';
    @endphp
    <tr class="hrx-directory-row" data-name="{{ strtolower($fullName) }}" data-email="{{ strtolower($row->email ?? '') }}" data-staff-id="{{ strtolower($staffId) }}" data-department="{{ strtolower($departmentName) }}" data-status="{{ strtolower($row->status ?? '') }}">
        <td>{{ $staffId }}</td>
        <td>
            <div>{{ $fullName !== '' ? $fullName : 'Unnamed Staff' }}</div>
            <small style="color:#8fa4ba">{{ $designationName }} - {{ $departmentName }}</small>
        </td>
        <td>{{ $departmentName }}</td>
        <td>{{ $row->email ?: '-' }}</td>
        <td>{{ $row->phone ?: '-' }}</td>
        <td>{{ optional($row->date_of_joining)->format('d M Y') }}</td>
        <td><span class="hrx-badge {{ ($row->status ?? '') === 'Active' ? 'green' : 'red' }}">{{ $row->status ?: 'Unknown' }}</span></td>
        <td>
            <div class="hrx-actions">
                @can('view-staff')
                    <button type="button" class="hrx-btn-lite hrx-staff-view" data-id="{{ $row->id }}" title="View"><i class="fa fa-eye"></i></button>
                @endcan
                @can('edit-staff')
                    <button type="button" class="hrx-btn-lite hrx-staff-edit" data-id="{{ $row->id }}" title="Edit"><i class="fa fa-pen"></i></button>
                @endcan
                @can('delete-staff')
                    <button type="button" class="hrx-btn-lite hrx-staff-delete" data-id="{{ $row->id }}" title="Delete"><i class="fa fa-trash"></i></button>
                @endcan
            </div>
        </td>
    </tr>
@empty
    @if(!empty($showEmpty))
        <tr><td colspan="8">No staff data found.</td></tr>
    @endif
@endforelse
