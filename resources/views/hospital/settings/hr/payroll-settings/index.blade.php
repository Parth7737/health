@extends('layouts.hospital.app')
@section('title','Payroll Settings')
@section('page_header_icon', '$')
@section('page_subtitle', 'Manage allowances and deductions')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-3">
            @include('hospital.settings.hr.submenu')
        </div>
        <div class="col-xl-9">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0">General Payroll Rules</h5></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('hospital.settings.hr.payroll-settings.general.save') }}" class="row g-3">
                        @csrf
                        <div class="col-md-6">
                            <label class="form-label">Standard Working Days / Month</label>
                            <input type="number" min="1" max="31" class="form-control" name="standard_working_days" value="{{ old('standard_working_days', $settings->standard_working_days ?? 30) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Leave Deduction Per Day (INR)</label>
                            <input type="number" min="0" step="0.01" class="form-control" name="leave_deduction_per_day" value="{{ old('leave_deduction_per_day', $settings->leave_deduction_per_day ?? 0) }}">
                            <small class="text-muted">If 0, system auto-calculates leave deduction from basic pay / working days.</small>
                        </div>
                        <div class="col-12"><button type="submit" class="btn btn-primary">Save General Settings</button></div>
                    </form>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0">Payroll Components (Allowances and Deductions)</h5></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('hospital.settings.hr.payroll-settings.component.store') }}" class="row g-3 mb-3">
                        @csrf
                        <input type="hidden" name="id" id="component_id">
                        <div class="col-md-3"><label class="form-label">Name</label><input type="text" class="form-control" name="name" id="component_name" required></div>
                        <div class="col-md-2"><label class="form-label">Type</label><select class="form-select" name="component_type" id="component_type" required><option value="Allowance">Allowance</option><option value="Deduction">Deduction</option></select></div>
                        <div class="col-md-2"><label class="form-label">Value Type</label><select class="form-select" name="value_type" id="component_value_type" required><option value="Fixed">Fixed</option><option value="Percentage">Percentage</option></select></div>
                        <div class="col-md-2"><label class="form-label">Value</label><input type="number" min="0" step="0.01" class="form-control" name="value" id="component_value" required></div>
                        <div class="col-md-1"><label class="form-label">Order</label><input type="number" min="0" class="form-control" name="sort_order" id="component_sort_order" value="0"></div>
                        <div class="col-md-2 d-flex align-items-center"><div class="form-check mt-4"><input type="hidden" name="is_active" value="0"><input class="form-check-input" type="checkbox" name="is_active" id="component_is_active" value="1" checked><label class="form-check-label" for="component_is_active">Active</label></div></div>
                        <div class="col-12"><button type="submit" class="btn btn-primary">Save Component</button><button type="button" class="btn btn-light" id="component_reset_btn">Reset</button></div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped"><thead><tr><th>Name</th><th>Type</th><th>Value Type</th><th>Value</th><th>Order</th><th>Status</th><th>Action</th></tr></thead><tbody>
                            @forelse($components as $component)
                                <tr>
                                    <td>{{ $component->name }}</td><td>{{ $component->component_type }}</td><td>{{ $component->value_type }}</td><td>{{ number_format($component->value, 2) }}</td><td>{{ $component->sort_order }}</td>
                                    <td><span class="badge {{ $component->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $component->is_active ? 'Active' : 'Inactive' }}</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-warning payroll-component-edit" data-id="{{ $component->id }}" data-name="{{ $component->name }}" data-component-type="{{ $component->component_type }}" data-value-type="{{ $component->value_type }}" data-value="{{ number_format($component->value, 2, '.', '') }}" data-sort-order="{{ $component->sort_order }}" data-is-active="{{ $component->is_active ? 1 : 0 }}">Edit</button>
                                        <form method="POST" action="{{ route('hospital.settings.hr.payroll-settings.component.destroy', ['component' => $component->id]) }}" class="d-inline">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this component?')">Delete</button></form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted">No payroll components configured yet.</td></tr>
                            @endforelse
                        </tbody></table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    function resetComponentForm() {
        document.getElementById('component_id').value = '';
        document.getElementById('component_name').value = '';
        document.getElementById('component_type').value = 'Allowance';
        document.getElementById('component_value_type').value = 'Fixed';
        document.getElementById('component_value').value = '';
        document.getElementById('component_sort_order').value = '0';
        document.getElementById('component_is_active').checked = true;
    }
    document.querySelectorAll('.payroll-component-edit').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById('component_id').value = this.dataset.id || '';
            document.getElementById('component_name').value = this.dataset.name || '';
            document.getElementById('component_type').value = this.dataset.componentType || 'Allowance';
            document.getElementById('component_value_type').value = this.dataset.valueType || 'Fixed';
            document.getElementById('component_value').value = this.dataset.value || '';
            document.getElementById('component_sort_order').value = this.dataset.sortOrder || '0';
            document.getElementById('component_is_active').checked = String(this.dataset.isActive || '1') === '1';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });
    var componentReset = document.getElementById('component_reset_btn');
    if (componentReset) componentReset.addEventListener('click', resetComponentForm);
})();
</script>
@endpush
