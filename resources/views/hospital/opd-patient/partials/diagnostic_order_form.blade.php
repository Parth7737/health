<div class="modal-header">
    <h5 class="modal-title">{{ ucfirst($orderType) }} Test</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="saveDiagnosticOrderForm">
    <div class="modal-body">
        <input type="hidden" name="order_type" value="{{ $orderType }}">
        <input type="hidden" name="opd_patient_id" value="{{ $opdPatient->id }}">
        @php
            $existingItemByTestId = collect($existingDiagnosticItems ?? [])->keyBy('test_id');
        @endphp

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Visit / OPD No</label>
                <input type="text" class="form-control" value="{{ $opdPatient->case_no }}" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label">Appointment Date</label>
                <input type="text" class="form-control" value="{{ optional($opdPatient->appointment_date)->format('d-m-Y h:i A') }}" readonly>
            </div>
            @if($orderType === 'pathology')
                <div class="col-md-6">
                    <label class="form-label">Priority</label>
                    <select name="priority" class="form-control">
                        <option value="Routine" selected>Routine</option>
                        <option value="Urgent">Urgent</option>
                        <option value="STAT">STAT</option>
                    </select>
                </div>
            @endif
            <div class="col-md-12">
                <label class="form-label">Select {{ ucfirst($orderType) }} Tests</label>
                <select name="test_ids[]" id="diagnostic_test_ids" class="form-control select2-modal" multiple>
                    @foreach($tests as $test)
                        @php
                            $existingItem = $existingItemByTestId->get((int) $test->id);
                        @endphp
                        <option
                            value="{{ $test->id }}"
                            data-category="{{ optional($test->category)->name ?? 'N/A' }}"
                            data-code="{{ $test->test_code ?? 'N/A' }}"
                            data-parameters="{{ $test->parameters->pluck('name')->implode(', ') ?: 'No parameters' }}"
                            data-charge="{{ number_format((float) ($test->resolved_charge ?? $test->standard_charge ?? 0), 2, '.', '') }}"
                            data-item-id="{{ $existingItem['item_id'] ?? '' }}"
                            @selected(!empty($existingItem))
                        >
                            {{ $test->test_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-12">
                <label class="form-label">Order Notes</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes for lab/radiology team"></textarea>
            </div>
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Test</th>
                                <th>Category</th>
                                <th>Code</th>
                                <th>Parameters</th>
                                <th>Charge</th>
                            </tr>
                        </thead>
                        <tbody id="diagnostic-test-preview-body">
                            <tr class="empty-diagnostic-test-row">
                                <td colspan="5" class="text-muted text-center">No test selected.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Create {{ ucfirst($orderType) }} Test</button>
    </div>
</form>
