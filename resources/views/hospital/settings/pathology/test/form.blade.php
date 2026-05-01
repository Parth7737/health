
@php
    $selectedParameterIds = isset($data) && $data ? $data->parameters->pluck('id')->map(function ($id) {
        return (int) $id;
    })->all() : [];
    $selectedSampleTypeIds = isset($data) && $data ? $data->sampleTypes->pluck('id')->map(function ($id) {
        return (int) $id;
    })->all() : [];
@endphp

<div class="modal-header">
    <h5 class="modal-title" id="view_modal_dataModelLabel">{{ !$id ? 'Add' : 'Edit'}} Pathology Test</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="savedata" enctype="multipart/form-data">
    <div class="modal-body">
        <input type="hidden" id="id" name="id" value="{{ $id }}">

        <div class="row g-2">
            <div class="col-md-6">
                <label class="form-label">Test Name</label>
                <input type="text" name="test_name" id="test_name" value="{{ @$data->test_name }}" class="form-control">
            </div>

            <div class="col-md-6">
                <label class="form-label">Test Code</label>
                <input type="text" name="test_code" id="test_code" value="{{ @$data->test_code }}" class="form-control">
            </div>

            <div class="col-md-6">
                <label class="form-label">Category</label>
                <select name="pathology_category_id" id="pathology_category_id" class="form-control select2-modal">
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ (isset($data) && (int) @$data->pathology_category_id === (int) $category->id) ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Charge Master <span class="text-danger">*</span></label>
                <select name="charge_master_id" id="charge_master_id" class="form-control select2-modal">
                    <option value="">Select Charge Master</option>
                    @foreach($chargeMasters as $chargeMaster)
                        <option value="{{ $chargeMaster->id }}"
                            data-standard-rate="{{ number_format((float) $chargeMaster->standard_rate, 2, '.', '') }}"
                            data-tpa-count="{{ (int) ($chargeMaster->tpa_rates_count ?? 0) }}"
                            {{ (isset($data) && (int) ($data->charge_master_id ?? 0) === (int) $chargeMaster->id) ? 'selected' : '' }}>
                            {{ $chargeMaster->name }} ({{ $chargeMaster->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Method</label>
                <input type="text" name="method" id="method" value="{{ @$data->method }}" class="form-control">
            </div>
            
            <div class="col-md-6">
                <label class="form-label">Sample Types</label>
                <select name="sample_type_ids[]" id="sample_type_ids" class="form-control select2-modal" multiple>
                    @foreach($sampleTypes as $sampleType)
                        <option value="{{ $sampleType->id }}" {{ in_array((int) $sampleType->id, $selectedSampleTypeIds, true) ? 'selected' : '' }}>
                            {{ $sampleType->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Report Days</label>
                <textarea name="report_days" id="report_days" class="form-control" rows="2">{{ @$data->report_days }}</textarea>
            </div>

            <div class="col-md-6">
                <label class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control" rows="2">{{ @$data->description }}</textarea>
            </div>

            <div class="col-md-12 mt-1">
                <label class="form-label">Parameters</label>
                <select name="pathology_parameter_ids[]" id="pathology_parameter_ids" class="form-control select2-modal" multiple>
                    @foreach($parameters as $parameter)
                        <option
                            value="{{ $parameter->id }}"
                            data-unit="{{ optional($parameter->unit)->name ?? 'N/A' }}"
                            data-range="{{ $parameter->range ?? 'N/A' }}"
                            {{ in_array((int) $parameter->id, $selectedParameterIds, true) ? 'selected' : '' }}
                        >
                            {{ $parameter->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-12">
                <div class="table-responsive mt-2">
                    <table class="table table-bordered align-middle mb-0" id="parameter-preview-table">
                        <thead class="table-light">
                            <tr>
                                <th>Parameter</th>
                                <th>Unit</th>
                                <th>Range</th>
                            </tr>
                        </thead>
                        <tbody id="parameter-preview-body">
                            @if(!empty($selectedParameterIds))
                                @foreach($parameters->whereIn('id', $selectedParameterIds) as $parameter)
                                    <tr>
                                        <td>{{ $parameter->name }}</td>
                                        <td>{{ optional($parameter->unit)->name ?? 'N/A' }}</td>
                                        <td>{{ $parameter->range ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr class="empty-parameter-row">
                                    <td colspan="3" class="text-muted text-center">No parameter selected.</td>
                                </tr>
                            @endif
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
