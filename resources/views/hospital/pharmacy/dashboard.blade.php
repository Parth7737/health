@extends('layouts.hospital.app')
@section('title', '💊 Pharmacy Management')
@section('page_subtitle', 'Dispensing · Inventory · Expiry Alerts · GRN · Purchase Orders')

@section('page_header_actions')
<button class="btn btn-outline-primary btn-sm" type="button" onclick="openPrescriptionSearch()">🔍 Search Prescription</button>
<button class="btn btn-warning btn-sm" type="button" onclick="openModal('statOrderModal')">🚨 STAT Order</button>
<button class="btn btn-primary btn-sm" type="button" onclick="openWalkInDispense()">💊 Dispense</button>
<button class="btn btn-success btn-sm" type="button" onclick="openModal('grnModal')">📥 GRN / Receive</button>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('public/css/hospital/pharmacy-dashboard.css') }}">
@include('layouts.partials.datatable-css')
@include('layouts.partials.flatpickr-css')
<style>
  /* Visual focus rings for PO Modal, GRN Modal & Dispense Modal elements */
  #newPOModal .modal-footer .btn:focus,
  #newPOModal .modal-footer .btn:focus-visible,
  #grnModal .modal-footer .btn:focus,
  #grnModal .modal-footer .btn:focus-visible,
  #dispenseModal .modal-footer .btn:focus,
  #dispenseModal .modal-footer .btn:focus-visible {
    outline: 3px solid #0d47a1 !important;
    outline-offset: 3px;
    box-shadow: 0 0 0 2px #fff, 0 0 0 7px rgba(25, 118, 210, 0.55) !important;
    position: relative;
    z-index: 2;
  }
  #newPOModal .modal-footer .btn-primary:focus,
  #newPOModal .modal-footer .btn-primary:focus-visible,
  #grnModal .modal-footer .btn-success:focus,
  #grnModal .modal-footer .btn-success:focus-visible,
  #dispenseModal .modal-footer .btn-success:focus,
  #dispenseModal .modal-footer .btn-success:focus-visible,
  #dispenseModal .modal-footer .btn-primary:focus,
  #dispenseModal .modal-footer .btn-primary:focus-visible {
    outline-color: #0d47a1 !important;
  }
  #newPOModal .modal-footer .btn-secondary:focus,
  #newPOModal .modal-footer .btn-secondary:focus-visible,
  #grnModal .modal-footer .btn-secondary:focus,
  #grnModal .modal-footer .btn-secondary:focus-visible,
  #dispenseModal .modal-footer .btn-secondary:focus,
  #dispenseModal .modal-footer .btn-secondary:focus-visible {
    outline-color: #555 !important;
    box-shadow: 0 0 0 2px #fff, 0 0 0 7px rgba(100, 100, 100, 0.4) !important;
  }
  #newPOModal #poItemBody .btn-danger:focus,
  #newPOModal #poItemBody .btn-danger:focus-visible {
    outline: 2px solid #c62828 !important;
    outline-offset: 1px;
    box-shadow: 0 0 0 2px #fff, 0 0 0 5px rgba(239, 83, 80, 0.4) !important;
  }
  #newPOModal .form-control:focus,
  #newPOModal .form-control:focus-visible,
  #grnModal .form-control:focus,
  #grnModal .form-control:focus-visible,
  #dispenseModal .form-control:focus,
  #dispenseModal .form-control:focus-visible {
    border-color: #1976d2 !important;
    outline: 2px solid #1976d2 !important;
    outline-offset: 0;
    box-shadow: 0 0 0 3px rgba(25, 118, 210, 0.2) !important;
  }
  #newPOModal .select2-container--focus .select2-selection,
  #newPOModal .select2-selection:focus {
    border-color: #1976d2 !important;
    outline: 2px solid #1976d2 !important;
    box-shadow: 0 0 0 3px rgba(25, 118, 210, 0.2) !important;
  }

  /* Focus styling for checkboxes in modals */
  #newPOModal input[type="checkbox"]:focus,
  #newPOModal input[type="checkbox"]:focus-visible,
  #grnModal input[type="checkbox"]:focus,
  #grnModal input[type="checkbox"]:focus-visible,
  #dispenseModal input[type="checkbox"]:focus,
  #dispenseModal input[type="checkbox"]:focus-visible {
    outline: 2px solid #1976d2 !important;
    outline-offset: 2px;
    box-shadow: 0 0 0 3px rgba(25, 118, 210, 0.25) !important;
  }

  /* Fix modal content scrolling when there are many items */
  .modal-overlay .modal {
    max-height: 90vh !important;
  }
  .modal-overlay .modal > .modal-body {
    max-height: calc(90vh - 160px) !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
  }
</style>
@endpush

@section('content')

<div class="pharmacy-dashboard-scope">
    <div class="stats-grid pharmacy-stats-grid">
        <div class="stat-card stat-blue">
            <div class="stat-icon">📋</div>
            <div class="stat-info">
                <div class="stat-value" id="phQueuePendingCount">0</div>
                <div class="stat-label">Queue Pending</div>
                <div class="stat-change neutral"><span id="phStatOrdersSubCount">0</span> STAT orders</div>
            </div>
        </div>
        <div class="stat-card stat-green">
            <div class="stat-icon">✅</div>
            <div class="stat-info">
                <div class="stat-value" id="phTodayDispensedCount">0</div>
                <div class="stat-label">Dispensed Today</div>
                <div class="stat-change neutral" id="phTodayDispensedTrend">0% vs yesterday</div>
            </div>
        </div>
        <div class="stat-card stat-orange">
            <div class="stat-icon">⚠️</div>
            <div class="stat-info">
                <div class="stat-value" id="phExpiryAlertsCount">0</div>
                <div class="stat-label">Expiry Alerts</div>
                <div class="stat-change neutral">Within 90 days</div>
            </div>
        </div>
        <div class="stat-card stat-red">
            <div class="stat-icon">📉</div>
            <div class="stat-info">
                <div class="stat-value" id="phLowStockItemsCount">0</div>
                <div class="stat-label">Low Stock Items</div>
                <div class="stat-change neutral">Reorder needed</div>
            </div>
        </div>
        <div class="stat-card stat-purple">
            <div class="stat-icon">💰</div>
            <div class="stat-info">
                <div class="stat-value" id="phTodaySalesAmount">₹0.00</div>
                <div class="stat-label">Today Sales</div>
                <div class="stat-change neutral" id="phTodaySalesTrend">0% vs yesterday</div>
            </div>
        </div>
        <div class="stat-card stat-teal">
            <div class="stat-icon">📦</div>
            <div class="stat-info">
                <div class="stat-value" id="phDrugItemsCount">0</div>
                <div class="stat-label">Drug Items</div>
                <div class="stat-change neutral">In inventory</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="tabs-bar-wrapper">
                <button class="tabs-scroll-btn scroll-left" type="button" onclick="scrollPharmacyTabs('left')">&lsaquo;</button>
                <div class="tabs-bar pharmacy-tabs-bar" id="pharmacyTabsBar">
                    <button class="tab-btn active" type="button" onclick="switchPhTab('dispenseQueuePane', this)">💊 Dispense Queue <span class="tab-count" id="phQueuePendingTabCount">0</span></button>
                    <button class="tab-btn" type="button" onclick="switchPhTab('statPane', this)">🚨 STAT Orders <span class="tab-count" id="phStatOrdersTabCount">0</span></button>
                    <button class="tab-btn" type="button" onclick="switchPhTab('allBillsPane', this)">📃 All Bills</button>
                    <button class="tab-btn" type="button" onclick="switchPhTab('rxValidatePane', this)">✅ Rx Validation</button>
                    <button class="tab-btn" type="button" onclick="switchPhTab('inventoryPane', this)">📦 Drug Inventory</button>
                    <button class="tab-btn" type="button" onclick="switchPhTab('quarantinePane', this)">📦 Quarantine Inventory</button>
                    <button class="tab-btn" type="button" onclick="switchPhTab('expiryPane', this)">⚠️ Expiry Alerts <span class="tab-count tab-count-danger" id="phExpiryAlertsTabCount">0</span></button>
                    <button class="tab-btn" type="button" onclick="switchPhTab('grnListPane', this)">📥 GRN Log</button>
                    <button class="tab-btn" type="button" onclick="switchPhTab('poPane', this)">📤 Purchase Orders</button>
                    <button class="tab-btn" type="button" onclick="switchPhTab('marPane', this)">📋 MAR</button>
                </div>
                <button class="tabs-scroll-btn scroll-right" type="button" onclick="scrollPharmacyTabs('right')">&rsaquo;</button>
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
                        <thead><tr><th>Drug Name</th><th>Category</th><th>Form</th><th>Batch</th><th>Expiry</th><th>Stock</th><th>Min Level</th><th>MRP ₹</th><th>Sale Price ₹</th><th>Status</th><th>Action</th></tr></thead>
                        <tbody id="drugInventoryBody"></tbody>
                    </table>
                </div>
            </div>
            <div id="quarantinePane" class="ph-pane ph-hidden">
                <div class="ph-toolbar inventory-toolbar">
                    <div class="inventory-filter-row">
                        <div class="input-group ph-search"><span class="input-addon">🔍</span><input type="text" class="form-control" id="quarantineDrugSearch" placeholder="Drug name / category..."></div>
                        <select class="form-control ph-small-select" id="quarantineDrugCategoryFilter">
                            <option value="">All Categories</option>
                            @foreach($medicineCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <select class="form-control ph-small-select" id="quarantineDrugExpiryFilter">
                            <option value="all">All Expiry</option>
                            <option value="exp_30">Expiring in 30 Days</option>
                            <option value="exp_90">Expiring in 90 Days</option>
                            <option value="expired">Already Expired</option>
                        </select>
                    </div>
                    <div class="inventory-action-row">
                        <button class="btn btn-secondary btn-sm" type="button" onclick="exportQuarantineDrugInventory()">🖨️ Export</button>
                    </div>
                </div>
                <div class="table-wrap">
                    <table class="hims-table display table-striped" id="quarantineDrugInventoryTable">
                        <thead><tr><th>Drug Name</th><th>Category</th><th>Form</th><th>Batch</th><th>Expiry</th><th>Quarantine Stock</th><th>Min Level</th><th>MRP ₹</th><th>Action</th></tr></thead>
                        <tbody id="quarantineDrugInventoryBody"></tbody>
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
                        <thead><tr><th>#</th><th>GRN No.</th><th>PO Ref</th><th>Supplier</th><th>Invoice No.</th><th>Items</th><th>Total Value</th><th>Received By</th><th>Date</th><th>Action</th></tr></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <div id="poPane" class="ph-pane ph-hidden">
                <div class="ph-toolbar mb-12">
                    <div class="fw-700 fs-14">Purchase Orders</div>
                    <button class="btn btn-primary btn-sm" type="button" onclick="window.openNewPOModal()">+ New PO</button>
                </div>
                <div class="table-wrap">
                    <table class="hims-table display table-striped" id="purchaseOrdersTable">
                        <thead><tr><th>#</th><th>PO No.</th><th>Date</th><th>Supplier</th><th>Items</th><th>Created By</th><th>Net Total</th><th>Status</th><th>Action</th></tr></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <div id="marPane" class="ph-pane ph-hidden">
                <div class="fw-700 fs-14 mb-12">📋 Medication Administration Record — Active IPD Patients</div>
                <div id="marContent"></div>
            </div>

            <div id="allBillsPane" class="ph-pane ph-hidden">
                <div class="ph-toolbar mb-12">
                    <div class="fw-700 fs-14">📃 All Pharmacy Bills</div>
                    <div class="d-flex gap-8 align-center">
                        <div class="input-group ph-search"><span class="input-addon">🔍</span><input type="text" class="form-control" id="allBillsSearch" placeholder="Search bill no / patient..."></div>
                        <div class="input-group" style="width: 270px; position: relative;">
                            <span class="input-addon">📅</span>
                            <input type="text" class="form-control all-bills-date-input" id="allBillsDateRange" placeholder="Select Date Range" readonly>
                            <button type="button" class="btn-clear-date" id="clearAllBillsDate" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; font-size: 14px; cursor: pointer; color: var(--text-light); display: none; z-index: 5;">✕</button>
                        </div>
                        <button class="btn btn-secondary btn-xs" type="button" onclick="refreshAllBills()">🔄 Refresh</button>
                    </div>
                </div>
                <div class="table-wrap">
                    <table class="hims-table display table-striped" id="allBillsTable">
                        <thead><tr><th>Bill No</th><th>Date</th><th>Patient</th><th>Items</th><th>Subtotal ₹</th><th>Discount ₹</th><th>Net Total ₹</th><th>Paid ₹</th><th>Actions</th></tr></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay hidden" id="dispenseModal" onclick="if(event.target===this) closeModal('dispenseModal')">
    <div class="modal modal-fullscreen">
        <div class="modal-header">
            <div class="modal-title" id="dispenseModalTitle">💊 Dispense Medicine</div>
            <button class="modal-close" type="button" onclick="closeModal('dispenseModal')">✕</button>
        </div>
        <div class="modal-body">
            <div id="dispenseKeyboardHints" style="margin:0 0 12px;padding:10px 12px;border:1px dashed var(--border-light);border-radius:10px;background:var(--surface-2);font-size:12px;color:var(--text-muted)">
                Keyboard: <b>Tab</b> cycles inside modal · <b>Enter</b> advances to next input in row (or next row) · <b>Alt+S</b> / <b>Ctrl+S</b> Dispense & Bill · <b>Alt+P</b> / <b>Ctrl+P</b> Dispense & Print · <b>Alt+H</b> / <b>Ctrl+H</b> Hold · <b>Alt+B</b> / <b>Esc</b> Close
            </div>
            <form id="dispenseForm">
                <input type="hidden" id="dispensePrescriptionType" name="prescription_type">
                <input type="hidden" id="dispensePrescriptionId" name="prescription_id">
                <input type="hidden" id="dispensePatientId" name="patient_id">

                {{-- Prescription Search Bar --}}
                <div class="ph-toolbar mb-8" id="dispensePrescriptionSearchBar">
                    <div class="input-group ph-search" style="max-width:400px; position:relative;">
                        <span class="input-addon">🔍</span>
                        <input type="text" class="form-control" id="dispensePrescriptionSearch" placeholder="Search Rx No / Patient name..." autocomplete="off">
                        <div id="dispensePrescriptionSearchResults" class="ph-autocomplete-results" style="display:none"></div>
                    </div>
                    <div class="text-muted fs-11">Search a prescription to dispense remaining medicines</div>
                </div>

                <div class="form-row cols-2">
                    <div class="ph-panel">
                        <div class="d-flex justify-content-between align-center mb-8">
                            <div class="fw-700 fs-13">Patient / Rx</div>
                            <span class="badge badge-gray" id="dispenseModeBadge">Walk-in</span>
                        </div>
                        {{-- Walk-in patient search --}}
                        <div id="dispensePatientSearchWrap" class="mb-8">
                            <div class="input-group ph-search" style="position:relative">
                                <span class="input-addon">👤</span>
                                <input type="text" class="form-control" id="dispensePatientSearch" placeholder="Search patient by name/UHID/phone..." autocomplete="off">
                                <div id="dispensePatientSearchResults" class="ph-autocomplete-results" style="display:none"></div>
                            </div>
                        </div>
                        <div id="dispensePatientCard">
                            <div class="patient-chip">
                                <div class="patient-chip-avatar">W</div>
                                <div class="patient-chip-info">
                                    <div class="patient-chip-name">Walk-in Customer</div>
                                    <div class="patient-chip-meta">No prescription selected</div>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-orange mt-8 mb-0" id="dispenseAllergyAlert" style="display:none;padding:8px 12px">
                            <span class="alert-icon">⚠️</span><div></div>
                        </div>
                    </div>

                    <div class="ph-panel">
                        <div class="form-group" style="position:relative">
                            <label class="form-label fs-11">Add Medicine</label>
                            <input class="form-control" id="walkInMedicineSearch" placeholder="Type 2+ chars to search medicine..." autocomplete="off">
                            <div id="walkInMedicineResults" class="ph-autocomplete-results" style="display:none"></div>
                        </div>
                    </div>
                </div>

                <div class="ph-toolbar mb-8 mt-12">
                    <div class="fw-700 fs-13">Dispense Items</div>
                    <div class="text-muted fs-11" id="dispenseStockHint">FEFO batch selection is applied by default. Reduce quantity to create a partial dispense.</div>
                </div>
                <div class="table-wrap">
                    <table class="hims-table ph-table-small" id="dispenseItemsTable">
                        <thead>
                            <tr>
                                <th>Medicine</th>
                                <th>Rx Detail</th>
                                <th>Prescribed</th>
                                <th>Days</th>
                                <th>Available</th>
                                <th>Batch</th>
                                <th>Dispense Qty</th>
                                <th>Rate ₹</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="dispenseItemsBody">
                            <tr><td colspan="10" class="text-muted text-center">Select a prescription or add medicine for walk-in dispense.</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="form-row cols-2 mt-12">
                    <div>
                        <div class="form-group">
                            <label class="form-label">Dispense Remarks</label>
                            <textarea class="form-control" rows="2" name="notes" id="dispenseNotes" placeholder="Any dispensing notes..."></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Counselling Given</label>
                            <div class="d-flex gap-12">
                                <label class="form-check"><input type="checkbox"><span class="form-check-label">Dosage instructions</span></label>
                                <label class="form-check"><input type="checkbox"><span class="form-check-label">Side effects</span></label>
                                <label class="form-check"><input type="checkbox"><span class="form-check-label">Storage</span></label>
                            </div>
                        </div>
                    </div>
                    <div class="ph-bill-preview">
                        <div class="fw-700 fs-12 mb-8 text-primary">💳 Pharmacy Bill</div>
                        <div class="ph-bill-row"><span>Drugs Total:</span><span class="fw-700" id="dispenseSubtotal">₹0.00</span></div>
                        <div class="ph-bill-row mt-4 d-flex justify-content-between align-center">
                            <span>Discount:</span>
                            <div class="d-flex gap-4 align-center">
                                <input class="form-control ph-grid-input" type="number" min="0" max="100" step="0.1" id="dispenseDiscountPercent" placeholder="%" style="width: 48px; text-align: right;" value="0">
                                <span class="fs-10 text-muted">%</span>
                                <input class="form-control ph-grid-input" type="number" min="0" step="0.01" name="discount_amount" id="dispenseDiscount" placeholder="₹" style="width: 65px; text-align: right;" value="0">
                            </div>
                        </div>
                        <div class="ph-bill-row mt-4 d-flex justify-content-between align-center">
                            <span>Round Off:</span>
                            <input class="form-control ph-grid-input" type="number" step="0.01" name="round_off" id="dispenseRoundOff" placeholder="₹" style="width: 65px; text-align: right; margin-left: auto;" value="0">
                        </div>
                        <div class="ph-bill-total"><span>Net Payable:</span><span class="fw-700 text-primary" id="dispenseNet">₹0.00</span></div>
                        <div class="ph-bill-row mt-8 d-flex justify-content-between align-center">
                            <span>Payment Mode: <span class="req">*</span></span>
                            <select class="form-control ph-grid-input" name="payment_mode" id="dispensePaymentMode" style="width: 130px;" required>
                                <option value="Cash">Cash</option>
                                <option value="UPI">UPI</option>
                                <option value="Card">Card</option>
                                <option value="Online">Online</option>
                            </select>
                        </div>
                        <div class="ph-bill-row mt-4 d-flex justify-content-between align-center" id="dispensePaymentRefRow" style="display: none;">
                            <span>Payment Ref:</span>
                            <input class="form-control ph-grid-input" type="text" name="payment_reference" id="dispensePaymentRef" placeholder="Txn Ref No" style="width: 130px;">
                        </div>
                        <input type="hidden" name="paid_amount" id="dispensePaid" value="0">
                        <input type="hidden" id="dispenseTax" value="0">
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" type="button" id="dispenseCancelBtn" onclick="closeModal('dispenseModal')">Cancel</button>
            <button class="btn btn-warning" type="button" id="dispenseHoldBtn" onclick="holdDispense()">⏸ Hold</button>
            <button class="btn btn-success" type="button" id="dispenseSaveBtn" onclick="confirmDispense(false)">✅ Dispense & Create Bill</button>
            <button class="btn btn-primary" type="button" id="dispensePrintBtn" onclick="confirmDispense(true)">🖨️ Dispense & Print</button>
        </div>
    </div>
</div>

{{-- ─── GRN Modal (PO-linked dynamic) ─── --}}
<div class="modal-overlay hidden" id="grnModal" onclick="if(event.target===this) closeModal('grnModal')">
    <div class="modal modal-fullscreen">
        <div class="modal-header">
            <div class="modal-title">📥 Goods Receipt Note (GRN)</div>
            <button class="modal-close" type="button" onclick="closeModal('grnModal')">✕</button>
        </div>
        <div class="modal-body">
            <div id="grnKeyboardHints" style="margin:0 0 12px;padding:10px 12px;border:1px dashed var(--border-light);border-radius:10px;background:var(--surface-2);font-size:12px;color:var(--text-muted)">
                Keyboard: <b>Tab</b> cycles inside modal · <b>Enter</b> advances to next input in row (or next row) · <b>Alt+S</b> / <b>Ctrl+S</b> Submit GRN · <b>Alt+B</b> / <b>Esc</b> Close
            </div>
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
                        <input type="text" class="form-control" id="grn_invoice_date" name="invoice_date" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Vehicle No.</label>
                        <input class="form-control" name="vehicle_no" placeholder="UK-XX-XXXX">
                    </div>
                </div>
                <div class="form-row cols-3">
                    <div class="form-group">
                        <label class="form-label">Temperature (Cold Chain)</label>
                        <select class="form-control" name="temperature_status">
                            <option value="Room Temp (N/A)">Room Temp (N/A)</option>
                            <option value="2-8°C (Cold Chain OK)">2-8°C (Cold Chain OK)</option>
                            <option value="Cold Chain Broken">Cold Chain Broken</option>
                        </select>
                    </div>
                    <div class="form-group" style="display: none;">
                        <label class="form-label">GST Type</label>
                        <select class="form-control" name="gst_type" id="grn_gst_type">
                            <option value="local">Local (CGST + SGST)</option>
                            <option value="interstate">Interstate (IGST)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Notes</label>
                        <input class="form-control" name="notes" placeholder="Optional remarks">
                    </div>
                </div>
                <div class="form-row cols-3">
                    <div class="form-group">
                        <label class="form-label">Default Profit %</label>
                        <input type="number" class="form-control" id="grn_profit_percent" value="30" min="0" placeholder="e.g. 30">
                    </div>
                    <div class="form-group d-flex align-items-center" style="padding-top: 25px;">
                        <label class="form-check" style="margin-left: 0;"><input type="checkbox" id="grn_sale_is_mrp" checked><span class="form-check-label text-success fw-700">Is Sale Price MRP?</span></label>
                    </div>
                    <div class="form-group">
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
                                <th>Rem. (Units)</th>
                                <th>Pack Size <span class="req">*</span></th>
                                <th>Recd. Qty (Packs) <span class="req">*</span></th>
                                <th>Free Qty (Packs)</th>
                                <th>Rej. Qty (Packs)</th>
                                <th>Batch No. <span class="req">*</span></th>
                                <th>Expiry</th>
                                <th>Pack Pur. Rate ₹ <span class="req">*</span></th>
                                <th>Pack Sale Rate ₹</th>
                                <th>Pack MRP ₹</th>
                                <th>GST %</th>
                                <th>GST Amt.</th>
                                <th>Line Total</th>
                                <th>Accd. Qty (Units)</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody id="grnItemBody"></tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <div class="ph-bill-preview" style="width: 320px; background: #fafafa; border: 1.5px solid #e0e0e0; border-radius: 8px; padding: 12px; margin-right: 15px;">
                        <div class="fw-700 fs-13 mb-2 text-success" style="border-bottom: 1.5px solid #e0e0e0; padding-bottom: 4px;">📥 Invoice Summary</div>
                        <div class="ph-bill-row d-flex justify-content-between fs-12 mb-1">
                            <span>Taxable Value (Subtotal):</span>
                            <span class="fw-700" id="grnSubtotal">₹0.00</span>
                        </div>
                        <div class="ph-bill-row d-flex justify-content-between fs-12 mb-1" id="grn_cgst_row">
                            <span>CGST:</span>
                            <span class="fw-700" id="grnCgstTotal">₹0.00</span>
                        </div>
                        <div class="ph-bill-row d-flex justify-content-between fs-12 mb-1" id="grn_sgst_row">
                            <span>SGST:</span>
                            <span class="fw-700" id="grnSgstTotal">₹0.00</span>
                        </div>
                        <div class="ph-bill-row d-flex justify-content-between fs-12 mb-1" id="grn_igst_row" style="display:none;">
                            <span>IGST:</span>
                            <span class="fw-700" id="grnIgstTotal">₹0.00</span>
                        </div>
                        <div class="ph-bill-row d-flex justify-content-between fs-12 mb-1" style="border-bottom: 1.5px dashed #ccc; padding-bottom: 6px;">
                            <span>Total GST / Tax:</span>
                            <span class="fw-700" id="grnTaxTotal">₹0.00</span>
                        </div>
                        <div class="ph-bill-total d-flex justify-content-between fs-14 fw-700 text-success mt-2">
                            <span>Gross Total (Payable):</span>
                            <span id="grnTotal">₹0.00</span>
                        </div>
                    </div>
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
            <div id="poKeyboardHints" style="margin:0 0 12px;padding:10px 12px;border:1px dashed var(--border-light);border-radius:10px;background:var(--surface-2);font-size:12px;color:var(--text-muted)">
                Keyboard: <b>Tab</b> cycles inside modal · <b>Alt+A</b> Add Item · <b>Enter</b> on Price or <b>Ctrl+Enter</b> adds row & focuses Medicine · <b>Alt+S</b> / <b>Ctrl+S</b> Create PO · <b>Alt+B</b> / <b>Esc</b> Close
            </div>
            <form id="newPOForm">
                <div class="form-row cols-3">
                    <div class="form-group">
                        <label class="form-label">Supplier <span class="req">*</span></label>
                        <select class="form-control select2" name="supplier_id" id="po_supplier_id">
                            <option value="">— Select —</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}{{ $s->phone ? ' ('.$s->phone.')' : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Order Date <span class="req">*</span></label>
                        <input type="text" class="form-control" id="bill_date" name="bill_date">
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
                        <thead><tr><th>Drug Name <span class="req">*</span></th><th>Pack Size</th><th>Packs <span class="req">*</span></th><th>Units</th><th>Pack Rate</th><th>Est. Amount</th><th></th></tr></thead>
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

{{-- ─── View Bill Modal ─── --}}
<div class="modal-overlay hidden" id="viewBillModal" onclick="if(event.target===this) closeModal('viewBillModal')">
    <div class="modal modal-md">
        <div class="modal-header">
            <div class="modal-title" id="viewBillModalTitle">📃 Bill Details</div>
            <button class="modal-close" type="button" onclick="closeModal('viewBillModal')">✕</button>
        </div>
        <div class="modal-body" id="viewBillModalBody">
            <div class="text-muted text-center">Loading...</div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" type="button" onclick="closeModal('viewBillModal')">Close</button>
            <button class="btn btn-primary" type="button" id="viewBillPrintBtn" onclick="printViewedBill()">🖨️ Print Bill</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@include('layouts.partials.datatable-js')
@include('layouts.partials.flatpickr-js')
<script>
window.poMedicines = @json($medicines);
window.hospitalStateId = {{ $hospitalStateId ?? 'null' }};
</script>
@endpush
