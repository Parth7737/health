@extends('layouts.hospital.app')
@section('title', '💊 Pharmacy Management')
@section('page_subtitle', 'Dispensing · Inventory · Expiry Alerts · GRN · Purchase Orders')

@section('page_header_actions')
<button class="btn btn-warning btn-sm" type="button" onclick="openModal('statOrderModal')">🚨 STAT Order</button>
<button class="btn btn-primary btn-sm" type="button" onclick="openModal('dispenseModal')">💊 Dispense</button>
<button class="btn btn-success btn-sm" type="button" onclick="openModal('grnModal')">📥 GRN / Receive</button>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('public/css/hospital/pharmacy-dashboard.css') }}">
@include('layouts.partials.datatable-css')
@endpush

@section('content')

<div class="pharmacy-dashboard-scope">
    <div class="stats-grid pharmacy-stats-grid">
        <div class="stat-card stat-blue">
            <div class="stat-icon">📋</div>
            <div class="stat-info">
                <div class="stat-value">12</div>
                <div class="stat-label">Queue Pending</div>
                <div class="stat-change neutral">3 STAT orders</div>
            </div>
        </div>
        <div class="stat-card stat-green">
            <div class="stat-icon">✅</div>
            <div class="stat-info">
                <div class="stat-value">89</div>
                <div class="stat-label">Dispensed Today</div>
                <div class="stat-change up">↑ 15% vs yesterday</div>
            </div>
        </div>
        <div class="stat-card stat-orange">
            <div class="stat-icon">⚠️</div>
            <div class="stat-info">
                <div class="stat-value">7</div>
                <div class="stat-label">Expiry Alerts</div>
                <div class="stat-change neutral">Within 30 days</div>
            </div>
        </div>
        <div class="stat-card stat-red">
            <div class="stat-icon">📉</div>
            <div class="stat-info">
                <div class="stat-value">14</div>
                <div class="stat-label">Low Stock Items</div>
                <div class="stat-change neutral">Reorder needed</div>
            </div>
        </div>
        <div class="stat-card stat-purple">
            <div class="stat-icon">💰</div>
            <div class="stat-info">
                <div class="stat-value">₹42K</div>
                <div class="stat-label">Today Sales</div>
                <div class="stat-change up">↑ Good</div>
            </div>
        </div>
        <div class="stat-card stat-teal">
            <div class="stat-icon">📦</div>
            <div class="stat-info">
                <div class="stat-value">1,842</div>
                <div class="stat-label">Drug Items</div>
                <div class="stat-change neutral">In inventory</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="tabs-bar pharmacy-tabs-bar">
                <button class="tab-btn active" type="button" onclick="switchPhTab('dispenseQueuePane', this)">💊 Dispense Queue <span class="tab-count">12</span></button>
                <button class="tab-btn" type="button" onclick="switchPhTab('statPane', this)">🚨 STAT Orders <span class="tab-count">3</span></button>
                <button class="tab-btn" type="button" onclick="switchPhTab('rxValidatePane', this)">✅ Rx Validation</button>
                <button class="tab-btn" type="button" onclick="switchPhTab('inventoryPane', this)">📦 Drug Inventory</button>
                <button class="tab-btn" type="button" onclick="switchPhTab('expiryPane', this)">⚠️ Expiry Alerts <span class="tab-count tab-count-danger">7</span></button>
                <button class="tab-btn" type="button" onclick="switchPhTab('grnListPane', this)">📥 GRN Log</button>
                <button class="tab-btn" type="button" onclick="switchPhTab('poPane', this)">📤 Purchase Orders</button>
                <button class="tab-btn" type="button" onclick="switchPhTab('marPane', this)">📋 MAR</button>
            </div>

            <div id="dispenseQueuePane" class="ph-pane">
                <div class="ph-toolbar">
                    <div class="input-group ph-search"><span class="input-addon">🔍</span><input type="text" class="form-control" id="dispenseSearch" placeholder="Search patient/prescription..." oninput="filterDispenseQueue(this.value)"></div>
                    <div class="d-flex gap-8">
                        <select class="form-control ph-small-select" id="dispenseTypeFilter"><option value="all">All Types</option><option value="opd">OPD</option><option value="ipd">IPD</option><option value="emergency">Emergency</option></select>
                        <button class="btn btn-secondary btn-xs" type="button" onclick="refreshQueue()">🔄 Refresh</button>
                    </div>
                </div>
                <div class="table-wrap">
                    <table class="hims-table display table-striped" id="dispenseQueueTable">
                        <thead><tr><th>Rx No.</th><th>Patient</th><th>Ward/Type</th><th>Doctor</th><th>Drugs</th><th>Priority</th><th>Time</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <div id="statPane" class="ph-pane ph-hidden">
                <div class="alert alert-red mb-12"><span class="alert-icon">🚨</span><div><b>STAT Orders</b> — These require IMMEDIATE dispensing. Target time: &lt;15 minutes.</div></div>
                <div class="ph-toolbar mb-12">
                    <button class="btn btn-secondary btn-xs" type="button" onclick="refreshStatOrders()">🔄 Refresh STAT Orders</button>
                </div>
                <div id="statOrdersList"></div>
            </div>

            <div id="rxValidatePane" class="ph-pane ph-hidden">
                <div id="rxValidateList"></div>
            </div>

            <div id="inventoryPane" class="ph-pane ph-hidden">
                <div class="ph-toolbar inventory-toolbar">
                    <div class="inventory-filter-row">
                        <div class="input-group ph-search"><span class="input-addon">🔍</span><input type="text" class="form-control" id="drugSearch" placeholder="Drug name / category..."></div>
                        <select class="form-control ph-small-select" id="drugCategoryFilter">
                            <option value="">All Categories</option>
                            @foreach($medicineCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <select class="form-control ph-small-select" id="drugStockFilter">
                            <option value="all">All Stock</option>
                            <option value="in_stock">In Stock</option>
                            <option value="low_stock">Low Stock</option>
                            <option value="out_of_stock">Out of Stock</option>
                            <option value="expired">Expired</option>
                        </select>
                        <select class="form-control ph-small-select" id="drugExpiryFilter">
                            <option value="all">All Expiry</option>
                            <option value="exp_30">Expiring in 30 Days</option>
                            <option value="exp_90">Expiring in 90 Days</option>
                            <option value="expired">Already Expired</option>
                        </select>
                    </div>
                    <div class="inventory-action-row">
                        <button class="btn btn-outline-primary btn-sm" type="button" onclick="openModal('grnModal')">📥 Add Stock</button>
                        <button class="btn btn-secondary btn-sm" type="button" onclick="exportDrugInventory()">🖨️ Export</button>
                    </div>
                </div>
                <div class="table-wrap">
                    <table class="hims-table display table-striped" id="drugInventoryTable">
                        <thead><tr><th>Drug Name</th><th>Category</th><th>Form</th><th>Batch</th><th>Expiry</th><th>Stock</th><th>Min Level</th><th>MRP ₹</th><th>Status</th><th>Action</th></tr></thead>
                        <tbody id="drugInventoryBody"></tbody>
                    </table>
                </div>
            </div>

            <div id="expiryPane" class="ph-pane ph-hidden">
                <div class="ph-toolbar inventory-toolbar mb-12">
                    <div class="inventory-filter-row">
                        <div class="input-group ph-search"><span class="input-addon">🔍</span><input type="text" class="form-control" id="expirySearch" placeholder="Drug name / batch..."></div>
                        <select class="form-control ph-small-select" id="expiryRangeFilter">
                            <option value="all_alerts">All Alerts (90 days)</option>
                            <option value="expired">Already Expired</option>
                            <option value="exp_30">Expiring in 30 Days</option>
                            <option value="exp_90">Expiring in 90 Days</option>
                        </select>
                    </div>
                    <div class="inventory-action-row">
                        <button class="btn btn-danger btn-sm" type="button" onclick="processExpiredBatches()">⚙️ Process Expired</button>
                    </div>
                </div>
                <div id="expiryAlertBanner" class="alert alert-orange mb-12"><span class="alert-icon">⚠️</span><div id="expiryAlertMsg"><b>Loading expiry alerts...</b></div></div>
                <div class="table-wrap">
                    <table class="hims-table display table-striped" id="expiryAlertsTable">
                        <thead><tr><th>Drug</th><th>Batch</th><th>Expiry Date</th><th>Days Left</th><th>Stock Qty</th><th>Recommended Action</th><th>Actions</th></tr></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <div id="grnListPane" class="ph-pane ph-hidden">
                <div class="ph-toolbar mb-12">
                    <div class="fw-700 fs-14">GRN Log</div>
                    <button class="btn btn-success btn-sm" type="button" onclick="openModal('grnModal')">📥 New GRN</button>
                </div>
                <div class="table-wrap">
                    <table class="hims-table display table-striped" id="grnLogTable">
                        <thead><tr><th>#</th><th>GRN No.</th><th>PO Ref</th><th>Supplier</th><th>Invoice No.</th><th>Items</th><th>Total Value</th><th>Received By</th><th>Date</th></tr></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <div id="poPane" class="ph-pane ph-hidden">
                <div class="ph-toolbar mb-12">
                    <div class="fw-700 fs-14">Purchase Orders</div>
                    <button class="btn btn-primary btn-sm" type="button" onclick="openModal('newPOModal')">+ New PO</button>
                </div>
                <div class="table-wrap">
                    <table class="hims-table display table-striped" id="purchaseOrdersTable">
                        <thead><tr><th>#</th><th>PO No.</th><th>Date</th><th>Supplier</th><th>Items</th><th>Net Total</th><th>Status</th><th>Action</th></tr></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <div id="marPane" class="ph-pane ph-hidden">
                <div class="fw-700 fs-14 mb-12">📋 Medication Administration Record — Active IPD Patients</div>
                <div id="marContent"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay hidden" id="dispenseModal" onclick="if(event.target===this) closeModal('dispenseModal')">
    <div class="modal modal-lg">
        <div class="modal-header">
            <div class="modal-title">💊 Dispense Prescription</div>
            <button class="modal-close" type="button" onclick="closeModal('dispenseModal')">✕</button>
        </div>
        <div class="modal-body">
            <div class="form-row cols-2">
                <div>
                    <div class="form-group"><label class="form-label">Prescription / Patient Search</label>
                        <div class="input-group"><span class="input-addon">🔍</span><input class="form-control" placeholder="Rx No / Patient MRN / Name..." oninput="loadRxPreview()"></div>
                    </div>
                    <div class="ph-panel" id="rxPreviewCard">
                        <div class="fw-700 fs-12 mb-8">📋 Prescription Preview</div>
                        <div class="patient-chip mb-8">
                            <div class="patient-chip-avatar">R</div>
                            <div class="patient-chip-info">
                                <div class="patient-chip-name">Ramesh Kumar Singh</div>
                                <div class="patient-chip-meta">MRN-10021 | 45M | B+ | OPD — Gen. Medicine</div>
                            </div>
                        </div>
                        <div class="alert alert-red mb-8" style="padding:8px 12px">
                            <span class="alert-icon">⚠️</span><div><b>Drug Allergy Alert:</b> Patient allergic to Penicillin. Verify current Rx for interactions.</div>
                        </div>
                        <table class="hims-table ph-table-small">
                            <thead><tr><th>Drug</th><th>Dose</th><th>Freq</th><th>Days</th><th>Qty</th><th>✓</th></tr></thead>
                            <tbody id="rxPreviewBody"></tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <div class="form-group"><label class="form-label">Dispense Remarks</label><textarea class="form-control" rows="2" placeholder="Any dispensing notes..."></textarea></div>
                    <div class="form-group"><label class="form-label">Substitution</label>
                        <select class="form-control"><option>No Substitution</option><option>Generic Substitution (with consent)</option><option>Formulary Item Used</option></select>
                    </div>
                    <div class="form-group"><label class="form-label">Counselling Given</label>
                        <div class="d-flex gap-12">
                            <label class="form-check"><input type="checkbox" checked><span class="form-check-label">Dosage instructions</span></label>
                            <label class="form-check"><input type="checkbox" checked><span class="form-check-label">Side effects</span></label>
                            <label class="form-check"><input type="checkbox"><span class="form-check-label">Storage</span></label>
                        </div>
                    </div>
                    <div class="ph-bill-preview">
                        <div class="fw-700 fs-12 mb-8 text-primary">💳 Pharmacy Bill</div>
                        <div class="ph-bill-row"><span>Drugs Total:</span><span class="fw-700">₹485.00</span></div>
                        <div class="ph-bill-row mt-4"><span>GST (12%):</span><span>₹58.20</span></div>
                        <div class="ph-bill-total"><span>Net Payable:</span><span class="fw-700 text-primary">₹543.20</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" type="button" onclick="closeModal('dispenseModal')">Cancel</button>
            <button class="btn btn-warning" type="button" onclick="holdDispense()">⏸ Hold</button>
            <button class="btn btn-success" type="button" onclick="confirmDispense()">✅ Dispense & Print Label</button>
        </div>
    </div>
</div>

{{-- ─── GRN Modal (PO-linked dynamic) ─── --}}
<div class="modal-overlay hidden" id="grnModal" onclick="if(event.target===this) closeModal('grnModal')">
    <div class="modal modal-lg">
        <div class="modal-header">
            <div class="modal-title">📥 Goods Receipt Note (GRN)</div>
            <button class="modal-close" type="button" onclick="closeModal('grnModal')">✕</button>
        </div>
        <div class="modal-body">
            <form id="grnForm">
                <div class="form-row cols-2">
                    <div class="form-group">
                        <label class="form-label">Select Approved PO <span class="req">*</span></label>
                        <select class="form-control" name="purchase_bill_id" id="grn_po_select">
                            <option value="">— Select —</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Supplier</label>
                        <input class="form-control" id="grn_supplier_display" readonly placeholder="Auto from PO">
                    </div>
                </div>
                <div class="form-row cols-3">
                    <div class="form-group">
                        <label class="form-label">Invoice No.</label>
                        <input class="form-control" name="invoice_no" placeholder="INV-XXXXX">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Invoice Date</label>
                        <input type="date" class="form-control" name="invoice_date" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Vehicle No.</label>
                        <input class="form-control" name="vehicle_no" placeholder="UK-XX-XXXX">
                    </div>
                </div>
                <div class="form-row cols-2">
                    <div class="form-group">
                        <label class="form-label">Temperature (Cold Chain)</label>
                        <select class="form-control" name="temperature_status">
                            <option value="Room Temp (N/A)">Room Temp (N/A)</option>
                            <option value="2-8°C (Cold Chain OK)">2-8°C (Cold Chain OK)</option>
                            <option value="Cold Chain Broken">Cold Chain Broken</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Notes</label>
                        <input class="form-control" name="notes" placeholder="Optional remarks">
                    </div>
                </div>

                <div class="ph-toolbar mb-8 mt-12">
                    <div class="fw-700 fs-13">Receive Items — Inspect & Enter Details</div>
                </div>
                <div class="alert alert-orange mb-8" id="grnNoPoAlert" style="padding:8px 12px">
                    <span class="alert-icon">ℹ️</span><div>Select an approved PO above to load items.</div>
                </div>
                <div class="table-wrap" id="grnItemsWrap" style="display:none">
                    <table class="hims-table">
                        <thead>
                            <tr>
                                <th>Drug Name</th>
                                <th>Ordered</th>
                                <th>Remaining</th>
                                <th>Batch No. <span class="req">*</span></th>
                                <th>Expiry</th>
                                <th>Recd. Qty <span class="req">*</span></th>
                                <th>Free Qty</th>
                                <th>Rejected</th>
                                <th>Pur. Price ₹</th>
                                <th>Sale Price ₹</th>
                                <th>MRP ₹</th>
                                <th>Tax%</th>
                                <th>Accepted</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody id="grnItemBody"></tbody>
                    </table>
                </div>
                <div class="ph-total-wrap">
                    <span class="fw-700 fs-14">Accepted Stock Value: <span id="grnTotal" class="text-primary">₹0.00</span></span>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" type="button" onclick="closeModal('grnModal')">Cancel</button>
            <button class="btn btn-success" type="button" id="submitGRNBtn">✅ Submit GRN & Inward Stock</button>
        </div>
    </div>
</div>

<div class="modal-overlay hidden" id="statOrderModal" onclick="if(event.target===this) closeModal('statOrderModal')">
    <div class="modal modal-md">
        <div class="modal-header">
            <div class="modal-title">🚨 STAT Drug Order</div>
            <button class="modal-close" type="button" onclick="closeModal('statOrderModal')">✕</button>
        </div>
        <div class="modal-body">
            <div class="alert alert-red"><span class="alert-icon">🚨</span><div>STAT orders must be dispensed within <b>15 minutes</b>. All STAT orders are auto-escalated to senior pharmacist.</div></div>
            <div class="form-row cols-2">
                <div class="form-group"><label class="form-label">Patient / Ward</label><input class="form-control" placeholder="ICU Bed 3 — Mohan Gupta"></div>
                <div class="form-group"><label class="form-label">Ordered By</label><input class="form-control" value="Dr. Rajesh Negi"></div>
            </div>
            <div class="form-group"><label class="form-label">Drug Required</label><input class="form-control" placeholder="e.g. Inj. Meropenem 1g IV"></div>
            <div class="form-row cols-2">
                <div class="form-group"><label class="form-label">Dose</label><input class="form-control" placeholder="1g"></div>
                <div class="form-group"><label class="form-label">Route</label><select class="form-control"><option>IV</option><option>IM</option><option>Oral</option><option>SC</option></select></div>
            </div>
            <div class="form-group"><label class="form-label">Clinical Urgency</label><textarea class="form-control" rows="2" placeholder="Reason for STAT..."></textarea></div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" type="button" onclick="closeModal('statOrderModal')">Cancel</button>
            <button class="btn btn-danger" type="button" onclick="processSTAT()">🚨 Process STAT — Urgent</button>
        </div>
    </div>
</div>
{{-- ─── New Purchase Order Modal (simplified) ─── --}}
<div class="modal-overlay hidden" id="newPOModal" onclick="if(event.target===this) closeModal('newPOModal')">
    <div class="modal modal-md">
        <div class="modal-header">
            <div class="modal-title">📤 New Purchase Order</div>
            <button class="modal-close" type="button" onclick="closeModal('newPOModal')">✕</button>
        </div>
        <div class="modal-body">
            <form id="newPOForm">
                <div class="form-row cols-3">
                    <div class="form-group">
                        <label class="form-label">Supplier <span class="req">*</span></label>
                        <select class="form-control" name="supplier_id" id="po_supplier_id">
                            <option value="">— Select —</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}{{ $s->phone ? ' ('.$s->phone.')' : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Order Date <span class="req">*</span></label>
                        <input type="date" class="form-control" name="bill_date" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Notes</label>
                        <input class="form-control" name="notes" placeholder="Optional remarks">
                    </div>
                </div>
                <div class="ph-toolbar mb-8 mt-12">
                    <div class="fw-700 fs-13">Order Items</div>
                    <button class="btn btn-outline-primary btn-xs" type="button" id="addPOItemRow">+ Add Item</button>
                </div>
                <div class="table-wrap">
                    <table class="hims-table">
                        <thead><tr><th>Drug Name <span class="req">*</span></th><th>Qty <span class="req">*</span></th><th>Est. Rate ₹</th><th>Est. Amount</th><th></th></tr></thead>
                        <tbody id="poItemBody"></tbody>
                    </table>
                </div>
                <div class="ph-total-wrap">
                    <span class="fw-700 fs-14">Estimated Total: <span id="poTotal" class="text-primary">₹0.00</span></span>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" type="button" onclick="closeModal('newPOModal')">Cancel</button>
            <button class="btn btn-primary" type="button" id="submitNewPO">📤 Create Purchase Order</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@include('layouts.partials.datatable-js')
<script>
window.poMedicines = @json($medicines);
</script>
@endpush