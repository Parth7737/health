<div class="modal-header">
    <h5 class="modal-title">{{ !$id ? 'Add' : 'Edit' }} Charge Master</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="savedata">
    @csrf
    <div class="modal-body">
        <input type="hidden" id="id" name="id" value="{{ $id }}">

        <div class="row g-2">
            <div class="col-md-6">
                <label class="form-label">Code <span class="text-danger">*</span></label>
                <input type="text" name="code" id="code" value="{{ old('code', $data->code ?? '') }}"
                       class="form-control text-uppercase" placeholder="e.g. OPD-CONSULT-GEN"
                       {{ $data->related_type ?? null ? 'readonly' : '' }}>
                @if($data->related_type ?? null)
                    <small class="text-muted">Auto-managed via {{ class_basename($data->related_type) }}</small>
                @endif
            </div>
            <div class="col-md-6">
                <label class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $data->name ?? '') }}" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Category <span class="text-danger">*</span></label>
                <select name="category" id="category" class="form-control">
                    @php
                        $categories = ['opd_consultation' => 'OPD Consultation', 'pathology' => 'Pathology', 'radiology' => 'Radiology', 'procedure' => 'Procedure', 'bed_charge' => 'Bed Charge', 'general' => 'General'];
                        $selected = old('category', $data->category ?? '');
                    @endphp
                    <option value="">Select Category</option>
                    @foreach($categories as $val => $label)
                        <option value="{{ $val }}" {{ $selected === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Standard Rate <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0" name="standard_rate" id="standard_rate"
                       value="{{ old('standard_rate', $data->standard_rate ?? '') }}" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Calculation Type <span class="text-danger">*</span></label>
                <select name="calculation_type" id="calculation_type" class="form-control">
                    <option value="fixed" {{ old('calculation_type', $data->calculation_type ?? 'fixed') === 'fixed' ? 'selected' : '' }}>Fixed</option>
                    <option value="daily" {{ old('calculation_type', $data->calculation_type ?? '') === 'daily' ? 'selected' : '' }}>Daily</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Billing Frequency <span class="text-danger">*</span></label>
                <select name="billing_frequency" id="billing_frequency" class="form-control">
                    <option value="one_time" {{ old('billing_frequency', $data->billing_frequency ?? 'one_time') === 'one_time' ? 'selected' : '' }}>One Time</option>
                    <option value="per_day"  {{ old('billing_frequency', $data->billing_frequency ?? '') === 'per_day' ? 'selected' : '' }}>Per Day</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="2">{{ old('description', $data->description ?? '') }}</textarea>
            </div>
            <div class="col-12 mt-1">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                           {{ old('is_active', $data->is_active ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
            </div>

            <div class="col-12 mt-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="mb-0">TPA Wise Charges</h6>
                    <button type="button" id="apply-all-tpa-rates" class="btn btn-sm btn-outline-primary">Apply Standard To All</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th>TPA Name</th>
                                <th style="width: 240px;">Charge</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($tpas ?? collect()) as $index => $tpa)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $tpa->name }}</td>
                                    <td>
                                        <input type="hidden" name="tpa_rates[{{ $index }}][tpa_id]" value="{{ $tpa->id }}">
                                        <input type="number" step="0.01" min="0"
                                            name="tpa_rates[{{ $index }}][rate]"
                                            value="{{ old('tpa_rates.' . $index . '.rate', $tpaRates[$tpa->id] ?? '') }}"
                                            class="form-control tpa-rate-input">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No TPA records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
