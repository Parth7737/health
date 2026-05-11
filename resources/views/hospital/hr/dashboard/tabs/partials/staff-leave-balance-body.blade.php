@php
    $empCode = $staff->staff_id ?? '—';
@endphp
<div class="mb-2" style="font-size:13px;color:#5a7894;">
    <strong style="color:#0d1b2a">{{ trim(($staff->first_name ?? '') . ' ' . ($staff->last_name ?? '')) }}</strong>
    · Emp ID: {{ $empCode }}
    · Year: <strong>{{ (int) $year }}</strong>
</div>
@if($rows->isEmpty())
    <p class="mb-0" style="color:#5a7894;font-size:13px;">No paid-leave balance rows for this year. Configure leave types (paid time off + annual days) and run yearly provisioning, or pick another year.</p>
@else
    <div class="hrx-table-wrap">
        <table class="hrx-table" style="width:100%;">
            <thead>
            <tr>
                <th>Leave type</th>
                <th>Entitled (days)</th>
                <th>Used (days)</th>
                <th>Available (days)</th>
            </tr>
            </thead>
            <tbody>
            @foreach($rows as $b)
                @php
                    $avail = max(0, (float) $b->entitled_days - (float) $b->used_days);
                @endphp
                <tr>
                    <td>{{ $b->leaveType->name ?? ('Type #' . $b->hr_leave_type_id) }}</td>
                    <td>{{ number_format((float) $b->entitled_days, 1) }}</td>
                    <td>{{ number_format((float) $b->used_days, 1) }}</td>
                    <td>{{ number_format($avail, 1) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif
