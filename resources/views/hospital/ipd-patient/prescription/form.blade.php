@php
    $items = $prescription?->items ?? collect([]);
    $medicineMap = $medicines->keyBy('id');
    $instructionMap = $instructions->keyBy('id');
    $routeMap = $routes->keyBy('id');
    $frequencyMap = $frequencies->keyBy('id');
@endphp

<style>
    .prescription-workspace {
        background: #f8fafc;
        border-radius: 14px;
        padding: 14px;
        overflow-x: hidden;
    }

    #ipdPrescriptionForm {
        overflow-x: hidden;
    }

    #ipdPrescriptionForm .select2-container {
        width: 100% !important;
        max-width: 100%;
    }

    #ipdPrescriptionForm .select2-selection {
        max-width: 100%;
        overflow: hidden;
    }

    #prescription_entry_dosage,
    #prescription_entry_instruction,
    #prescription_entry_route,
    #prescription_entry_frequency {
        height: 38px;
        font-size: 13px;
        padding: 6px 10px;
        border: 1px solid #ced4da;
        border-radius: 6px;
        background-color: #fff;
        color: #344054;
        width: 100%;
        appearance: auto;
        cursor: pointer;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }

    #prescription_entry_dosage:focus,
    #prescription_entry_instruction:focus,
    #prescription_entry_route:focus,
    #prescription_entry_frequency:focus {
        border-color: #4c6ef5;
        outline: none;
        box-shadow: 0 0 0 3px rgba(76, 110, 245, 0.15);
    }

    .prescription-shortcut-note {
        font-size: 0.85rem;
        color: #64748b;
        line-height: 1.4;
    }

    .prescription-meta-card {
        background: #ffffff;
        border: 1px solid #dbe4ee;
        border-radius: 14px;
        padding: 14px;
    }

    .prescription-notes-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .prescription-items-shell {
        display: grid;
        gap: 12px;
    }

    .prescription-entry-grid {
        display: grid;
        grid-template-columns: 3fr 1.6fr 2fr 2fr 2fr 1fr auto;
        gap: 10px;
    }

    .prescription-entry-grid > div {
        min-width: 0;
    }

    .prescription-entry-grid .form-label {
        margin-bottom: 6px !important;
        min-height: 22px;
        line-height: 1.2;
        display: flex;
        align-items: center;
    }

    .prescription-entry-grid .form-control,
    .prescription-entry-grid .form-select {
        height: 42px;
    }

    #prescriptionItemsTable {
        margin-bottom: 0;
        font-size: 0.88rem;
        width: 100%;
        table-layout: fixed;
    }

    #prescriptionItemsTable td,
    #prescriptionItemsTable th {
        padding: 0.45rem 0.5rem;
        vertical-align: middle;
        word-break: break-word;
    }

    #prescriptionItemsTable .prescription-item-row {
        border-bottom: 1px solid #eef2f7;
    }

    .prescription-empty-row td {
        color: #64748b;
        text-align: center;
        padding: 0.85rem;
    }

    .prescription-row-actions {
        display: inline-flex;
        gap: 6px;
        justify-content: flex-end;
    }

    .prescription-icon-btn {
        width: 30px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }

    .prescription-entry-actions {
        flex-direction: column;
        justify-content: flex-end;
    }

    .prescription-entry-actions .d-flex {
        height: 42px;
        align-items: center;
    }

    .prescription-entry-actions .prescription-icon-btn {
        width: 42px;
        height: 42px;
    }

    @media (min-width: 992px) {
        .prescription-entry-grid .btn {
            white-space: nowrap;
        }
    }

    @media (max-width: 1399.98px) and (min-width: 992px) {
        .prescription-entry-grid {
            grid-template-columns: repeat(12, minmax(0, 1fr));
        }

        .prescription-entry-grid > div:nth-child(1) {
            grid-column: span 4;
        }

        .prescription-entry-grid > div:nth-child(2) {
            grid-column: span 2;
        }

        .prescription-entry-grid > div:nth-child(3) {
            grid-column: span 3;
        }

        .prescription-entry-grid > div:nth-child(4) {
            grid-column: span 4;
        }

        .prescription-entry-grid > div:nth-child(5) {
            grid-column: span 4;
        }

        .prescription-entry-grid > div:nth-child(6) {
            grid-column: span 2;
        }

        .prescription-entry-grid > div:nth-child(7) {
            grid-column: span 2;
        }
    }

    @media (max-width: 991.98px) {
        .prescription-entry-grid {
            grid-template-columns: 1fr;
        }

        .prescription-notes-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="modal-header">
    <h5 class="modal-title">{{ $prescription ? 'Edit IPD Prescription' : 'Add IPD Prescription' }} - {{ $allocation->admission_no }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    <form id="ipdPrescriptionForm" data-store-url="{{ route('hospital.ipd-patient.prescription.store', ['allocation' => $allocation->id]) }}" data-load-dosages-url="{{ route('hospital.ipd-patient.prescription.load-dosages') }}">
        @if($prescription)
            <input type="hidden" name="prescription_id" value="{{ $prescription->id }}">
        @endif

        <div class="row g-3 prescription-workspace">
            <div class="col-12">
                <div class="prescription-meta-card">
                    <div class="row">
                        <div class="col-lg-4">
                            <label class="form-label">Prescription Valid Till</label>
                            <input type="text" class="form-control prescription-valid-till" name="valid_till" value="{{ $prescription?->valid_till ? $prescription->valid_till->format('d-m-Y') : now()->addDays(5)->format('d-m-Y') }}" placeholder="dd-mm-yyyy">
                        </div>
                        <div class="col-lg-8">
                            <div>
                                <label class="form-label mb-1">Medicine <span class="text-danger">*</span></label>
                                <select class="form-select select2-modal" id="prescription_entry_medicine" tabindex="1">
                                    <option value="">Select</option>
                                    @foreach($medicines as $medicine)
                                        <option value="{{ $medicine->id }}" data-category-id="{{ $medicine->medicine_category_id }}">{{ $medicine->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="prescription-items-shell">
                    <div class="prescription-meta-card">
                        <div class="prescription-entry-grid">
                            <div>
                                <label class="form-label mb-1">Dosage</label>
                                <select class="form-select" id="prescription_entry_dosage" tabindex="2">
                                    <option value="">Select Dosage</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label mb-1">Instruction</label>
                                <select class="form-select" id="prescription_entry_instruction" tabindex="3">
                                    <option value="">Select Instruction</option>
                                    @foreach($instructions as $instruction)
                                        <option value="{{ $instruction->id }}">{{ $instruction->instruction }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label mb-1">Route</label>
                                <select class="form-select" id="prescription_entry_route" tabindex="4">
                                    <option value="">Select Route</option>
                                    @foreach($routes as $route)
                                        <option value="{{ $route->id }}">{{ $route->route }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label mb-1">Frequency <span class="text-danger">*</span></label>
                                <select class="form-select" id="prescription_entry_frequency" tabindex="5">
                                    <option value="">Select Frequency</option>
                                    @foreach($frequencies as $frequency)
                                        <option value="{{ $frequency->id }}">{{ $frequency->frequency }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label mb-1">Days <span class="text-danger">*</span></label>
                                <input type="number" min="1" max="365" class="form-control" id="prescription_entry_days" placeholder="Days" tabindex="6">
                            </div>
                            <div class="prescription-entry-actions">
                                <label class="form-label mb-1 invisible">Action</label>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-primary btn-xs prescription-icon-btn" id="addPrescriptionItemRow" title="Add medicine" aria-label="Add medicine" tabindex="7">
                                        <i class="fa-solid fa-plus" id="prescriptionAddIcon"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-xs prescription-icon-btn d-none" id="cancelPrescriptionItemEdit" title="Cancel edit" aria-label="Cancel edit" tabindex="8">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <table class="table table-bordered align-middle mb-0" id="prescriptionItemsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Medicine</th>
                                <th>Dosage</th>
                                <th>Instruction</th>
                                <th>Route</th>
                                <th>Frequency</th>
                                <th>Days</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody id="prescriptionItemsTbody">
                            @php $rows = $items->count() ? $items : collect([]); @endphp
                            @foreach($rows as $item)
                                @php
                                    $selectedMedicineId = $item?->medicine_id;
                                    $selectedDosageId = $item?->medicine_dosage_id;
                                    $selectedCategoryId = $item?->medicine_category_id;
                                    $dosageOptions = $selectedCategoryId
                                        ? App\Models\MedicineDosage::query()->where('medicine_category_id', $selectedCategoryId)->orderBy('dosage')->get(['id', 'dosage'])
                                        : collect([]);
                                    $selectedMedicine = $medicineMap->get($selectedMedicineId);
                                    $selectedInstruction = $instructionMap->get($item?->medicine_instruction_id);
                                    $selectedRoute = $routeMap->get($item?->medicine_route_id);
                                    $selectedFrequency = $frequencyMap->get($item?->medicine_frequency_id);
                                    $selectedDosage = $dosageOptions->firstWhere('id', $selectedDosageId);
                                @endphp
                                <tr class="prescription-item-row" data-row-id="{{ $loop->iteration }}" data-medicine-id="{{ $selectedMedicineId }}" data-category-id="{{ $selectedCategoryId }}" data-dosage-id="{{ $selectedDosageId }}" data-instruction-id="{{ $item?->medicine_instruction_id }}" data-route-id="{{ $item?->medicine_route_id }}" data-frequency-id="{{ $item?->medicine_frequency_id }}" data-days="{{ $item?->no_of_day }}">
                                    <td>{{ $selectedMedicine?->name ?? '-' }}</td>
                                    <td>{{ $selectedDosage?->dosage ?? '-' }}</td>
                                    <td>{{ $selectedInstruction?->instruction ?? '-' }}</td>
                                    <td>{{ $selectedRoute?->route ?? '-' }}</td>
                                    <td>{{ $selectedFrequency?->frequency ?? '-' }}</td>
                                    <td>{{ $item?->no_of_day ?: '-' }}</td>
                                    <td class="text-end">
                                        <span class="prescription-row-actions">
                                            <button type="button" class="btn btn-primary btn-xs prescription-icon-btn edit-prescription-item-row" title="Edit medicine" aria-label="Edit medicine">
                                                <i class="fa-solid fa-pen"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-xs prescription-icon-btn remove-prescription-item-row" title="Remove medicine" aria-label="Remove medicine">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </span>
                                        <input type="hidden" name="medicine_id[]" value="{{ $selectedMedicineId }}">
                                        <input type="hidden" name="medicine_dosage_id[]" value="{{ $selectedDosageId }}">
                                        <input type="hidden" name="medicine_instruction_id[]" value="{{ $item?->medicine_instruction_id }}">
                                        <input type="hidden" name="medicine_route_id[]" value="{{ $item?->medicine_route_id }}">
                                        <input type="hidden" name="medicine_frequency_id[]" value="{{ $item?->medicine_frequency_id }}">
                                        <input type="hidden" name="no_of_day[]" value="{{ $item?->no_of_day }}">
                                    </td>
                                </tr>
                            @endforeach
                            @if($rows->isEmpty())
                                <tr class="prescription-empty-row">
                                    <td colspan="7">No medicine added yet.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-12">
                <div class="prescription-meta-card">
                    <div class="prescription-notes-grid">
                        <div>
                            <label class="form-label">Header Note</label>
                            <textarea class="form-control" name="header_note" rows="3" placeholder="Short note for patient...">{{ $prescription?->header_note }}</textarea>
                        </div>
                        <div>
                            <label class="form-label">Footer Note</label>
                            <textarea class="form-control" name="footer_note" rows="3" placeholder="Advice / follow-up note...">{{ $prescription?->footer_note }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
    <button type="submit" form="ipdPrescriptionForm" class="btn btn-primary save-ipd-prescription-btn">{{ $prescription ? 'Update Prescription' : 'Save Prescription' }}</button>
</div>
