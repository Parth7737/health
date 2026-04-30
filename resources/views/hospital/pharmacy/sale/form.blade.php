<style>
    #savedata {
        font-size: 12px;
    }

    #savedata .modal-body {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 280px;
        gap: 10px;
        max-height: calc(100vh - 170px);
        overflow: auto;
    }

    #savedata .pos-left {
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    #savedata .table-shell {
        overflow-y: auto;
        overflow-x: hidden;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        flex: 1;
        min-height: 220px;
    }

    #sale-items-table {
        width: 100%;
        table-layout: fixed;
        margin-bottom: 0;
        font-size: 12px;
    }

    #sale-items-table th,
    #sale-items-table td {
        padding: 4px 5px;
        vertical-align: middle;
    }

    #sale-items-table th {
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    #sale-items-table .form-control,
    #sale-items-table .form-select {
        height: 30px;
        min-height: 30px;
        padding: 2px 6px;
        font-size: 12px;
    }

    #savedata .select2-container {
        width: 100% !important;
    }

    #savedata .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #ced4da;
    }

    #savedata .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
        padding-left: 10px;
    }

    #savedata .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }

    #sale-items-table .select2-container--default .select2-selection--single {
        height: 30px;
    }

    #sale-items-table .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px;
        padding-left: 6px;
    }

    #sale-items-table .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 28px;
    }

    #sale-items-table .item-medicine,
    #sale-items-table .item-batch,
    #sale-items-table .item-note {
        width: 100%;
    }

    #sale-items-table .line-total {
        font-size: 12px;
        font-weight: 700;
        line-height: 1.2;
        display: inline-block;
        min-width: 70px;
        text-align: right;
    }

    #sale-items-table td:last-child {
        text-align: center;
        overflow: visible;
    }

    #sale-items-table .remove-item {
        min-width: 28px;
        height: 28px;
        line-height: 1;
        padding: 0;
    }

    #savedata .summary-panel {
        border-left: 1px solid #dee2e6;
        background: #f8f9fa;
        padding: 10px;
        overflow-y: auto;
    }

    #savedata .summary-title {
        font-size: 11px;
        letter-spacing: 0.5px;
        margin-bottom: 10px;
    }

    #savedata .summary-line {
        display: flex;
        justify-content: space-between;
        margin-bottom: 6px;
        font-size: 12px;
    }

    #savedata .summary-strong {
        font-weight: 700;
    }

    @media (max-width: 1100px) {
        #savedata .modal-body {
            grid-template-columns: 1fr;
            height: auto;
            max-height: calc(100vh - 180px);
            overflow-y: auto;
        }

        #savedata .summary-panel {
            border-left: 0;
            border-top: 1px solid #dee2e6;
        }
    }
</style>

<div class="modal-header bg-primary px-3 py-2">
    <h5 class="modal-title text-white mb-0">Pharmacy Sale Bill</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="POST" id="savedata">
    <div class="modal-body">
        
        <!-- Left side: Bill details & Items -->
        <div class="pos-left">
            <!-- Header Section -->
            <div class="border-bottom pb-3 mb-3" style="flex-shrink: 0;">
                <!-- Row 1: Date & Patient -->
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label mb-2 small fw-bold">Bill Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="bill_date" value="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="col-md-9">
                        <label class="form-label mb-2 small fw-bold">Patient</label>
                        <select class="form-control select2-modal" name="patient_id" id="sale_patient_id">
                            <option value="">Walk-in Customer</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}">{{ $patient->name }} ({{ $patient->patient_id }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Row 2: Prescription Type & ID -->
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label mb-2 small fw-bold">Rx Type</label>
                        <select class="form-control" name="prescription_type" id="prescription_type">
                            <option value="">None</option>
                            <option value="opd">OPD</option>
                            <option value="ipd">IPD</option>
                        </select>
                    </div>
                    <div class="col-md-9">
                        <label class="form-label mb-2 small fw-bold">Prescription</label>
                        <select class="form-control select2-modal" name="prescription_id" id="prescription_id">
                            <option value="">Select</option>
                        </select>
                    </div>
                </div>
                
                <small class="text-muted d-block mt-2">
                    <strong>Keyboard:</strong> Tab/Enter navigate. Last row note field par Enter = new row auto-add.
                </small>
            </div>

            <!-- Items Table -->
            <div class="table-shell">
                <table class="table table-sm align-middle" id="sale-items-table">
                    <colgroup>
                        <col style="width:24%;">
                        <col style="width:14%;">
                        <col style="width:7%;">
                        <col style="width:8%;">
                        <col style="width:8%;">
                        <col style="width:7%;">
                        <col style="width:10%;">
                        <col style="width:8%;">
                        <col style="width:10%;">
                        <col style="width:4%;">
                    </colgroup>
                    <thead class="table-light sticky-top">
                        <tr>
                            <th>Medicine</th>
                            <th>Batch</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>MRP</th>
                            <th>Tax%</th>
                            <th>Total</th>
                            <th>Subst</th>
                            <th>Note</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div class="pt-3 border-top d-flex gap-2 align-items-end" style="flex-shrink: 0;">
                <button type="button" class="btn btn-sm btn-outline-primary" id="add-sale-item">+ Add Row</button>
                <div class="flex-grow-1">
                    <label class="form-label mb-2 small fw-bold">Notes</label>
                    <textarea class="form-control form-control-sm" name="notes" rows="2" placeholder="Optional notes..."></textarea>
                </div>
            </div>
        </div>

        <!-- Right side: Summary (Sticky) -->
        <div class="summary-panel">
            <div>
                <h6 class="text-muted text-uppercase summary-title">Bill Summary</h6>
                
                <!-- Items Subtotal -->
                <div class="summary-line">
                    <span class="text-muted">Items</span>
                    <span class="summary-strong" id="sum-subtotal">Rs 0.00</span>
                </div>

                <!-- Item Discount -->
                <div class="summary-line">
                    <span class="text-muted">Item Disc</span>
                    <span id="sum-item-discount" class="text-danger">Rs 0.00</span>
                </div>

                <!-- Tax -->
                <div class="summary-line" style="padding-bottom: 8px; border-bottom: 1px solid #dee2e6; margin-bottom: 8px;">
                    <span class="text-muted">Tax</span>
                    <span class="summary-strong" id="sum-tax">Rs 0.00</span>
                </div>

                <!-- Net Before Discount -->
                <div class="summary-line" style="font-size: 11px; color: #6c757d;">
                    <small class="text-muted">Net (Before)</small>
                    <small id="sum-before-discount">Rs 0.00</small>
                </div>

                <!-- Header Discount Input -->
                <div class="mb-3">
                    <label class="form-label mb-2 small fw-bold">Header Discount</label>
                    <input type="number" step="0.01" min="0" class="form-control form-control-sm text-end" name="discount_amount" value="0" id="header_discount_amount">
                </div>

                <!-- Net Total (Highlighted) -->
                <div class="bg-primary text-white p-2 rounded mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">Net Total</span>
                        <strong id="sum-net-total" style="font-size: 1.1rem;">Rs 0.00</strong>
                    </div>
                </div>

                <!-- Paid Amount Input -->
                <div class="mb-3">
                    <label class="form-label mb-2 small fw-bold">Paid Amount</label>
                    <input type="number" step="0.01" min="0" class="form-control form-control-sm text-end" name="paid_amount" value="0" id="paid_amount">
                </div>

                <!-- Due Amount -->
                <div class="bg-info bg-opacity-10 p-2 rounded">
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold text-white">Due</span>
                        <strong id="sum-due" class="text-white" style="font-size: 1.1rem;">Rs 0.00</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer py-2 px-3 border-top">
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-sm btn-success">Save Bill</button>
    </div>
</form>

@php
    $opdPrescriptionPayload = $opdPrescriptions->map(function ($p) {
        return [
            'id' => $p->id,
            'patient_id' => $p->patient_id,
            'label' => ($p->prescription_no ?: ('OPD-RX-' . now()->format('ym') . '-' . str_pad((string) $p->id, 5, '0', STR_PAD_LEFT)))
                . ' | #' . $p->id
                . ' - ' . ($p->patient?->name ?? 'Unknown')
                . ' | Valid: ' . ($p->valid_till ? $p->valid_till->format('d-m-Y') : 'NA'),
        ];
    })->values();

    $ipdPrescriptionPayload = $ipdPrescriptions->map(function ($p) {
        return [
            'id' => $p->id,
            'patient_id' => $p->patient_id,
            'label' => ($p->prescription_no ?: ('IPD-RX-' . now()->format('ym') . '-' . str_pad((string) $p->id, 5, '0', STR_PAD_LEFT)))
                . ' | #' . $p->id
                . ' - ' . ($p->patient?->name ?? 'Unknown')
                . ' | Valid: ' . ($p->valid_till ? $p->valid_till->format('d-m-Y') : 'NA'),
        ];
    })->values();
@endphp

<script>
window.saleMedicines = @json($medicines);
window.opdPrescriptionOptions = @json($opdPrescriptionPayload);
window.ipdPrescriptionOptions = @json($ipdPrescriptionPayload);
window.initialSalePrescription = {
    type: @json($initialPrescriptionType),
    id: @json($initialPrescriptionId),
    patientId: @json($initialPatientId),
};
</script>