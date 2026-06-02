(function () {
    'use strict';

    function toast(title, message, type, duration) {
        if (typeof window.sendmsg === 'function') {
            window.sendmsg(type || 'info', message || title);
            return;
        }
        console.log(title + ': ' + message);
    }

    function fallbackOpenModal(id) {
        var modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    function fallbackCloseModal(id) {
        var modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    window.openModal = window.openModal || fallbackOpenModal;
    window.closeModal = window.closeModal || fallbackCloseModal;

    // Wrap the global openModal to hook into PO/GRN modal opens
    var _origOpenModal = window.openModal;
    window.openModal = function (id) {
        _origOpenModal(id);
        if (id === 'newPOModal') {
            initNewPOModal();
        }
        if (id === 'grnModal') {
            loadApprovedPOs();
        }
        if (id === 'dispenseModal') {
            initDispenseModal();
        }
    };

    function initBillDatePicker() {
        var billDateInput = document.getElementById('bill_date');
        if (!billDateInput) {
            return;
        }
        if (typeof flatpickr !== 'function') {
            setTimeout(initBillDatePicker, 50);
            return;
        }
        if (billDateInput._flatpickr) {
            return;
        }
        flatpickr(billDateInput, {
            enableTime: false,
            dateFormat: 'd-m-Y',
            defaultDate: new Date(),
            maxDate: new Date()
        });
    }

    function initNewPOModal() {
        initBillDatePicker();

        var body = document.getElementById('poItemBody');
        if (body && body.children.length === 0) {
            body.innerHTML = buildPORow();
            initPOMedicineSelect(body);
            recalcPOTotal();
        }
    }

    document.addEventListener('DOMContentLoaded', initBillDatePicker);

    var dispenseTable = null;
    var inventoryTable = null;
    var quarantineInventoryTable = null;
    var expiryTable = null;
    var poTable = null;
    var statOrdersLoaded = false;
    var grnRowCount = 1;

    function getDispenseQueueLoadUrl() {
        if (typeof window.route === 'function') {
            try {
                return window.route('dispenseQueueLoad');
            } catch (e) {
                return '';
            }
        }
        return '';
    }

    function getDispensePreviewUrl() {
        if (typeof window.route === 'function') {
            try { return window.route('dispensePreview'); } catch (e) { return ''; }
        }
        return '';
    }

    function getDispenseMedicineSearchUrl() {
        if (typeof window.route === 'function') {
            try { return window.route('dispenseMedicineSearch'); } catch (e) { return ''; }
        }
        return '';
    }

    function getDispenseStoreUrl() {
        if (typeof window.route === 'function') {
            try { return window.route('dispenseStore'); } catch (e) { return ''; }
        }
        return '';
    }

    function loadDispenseQueue() {
        var tableNode = document.getElementById('dispenseQueueTable');
        if (!tableNode) {
            return;
        }

        var dispenseLoadUrl = getDispenseQueueLoadUrl();
        if (!dispenseLoadUrl || typeof window.$ === 'undefined' || typeof window.$.fn.DataTable === 'undefined') {
            return;
        }

        if (dispenseTable) {
            return;
        }

        dispenseTable = window.$('#dispenseQueueTable').DataTable({
            processing: true,
            serverSide: true,
            paging: true,
            info: true,
            searching: true,
            ordering: true,
            responsive: true,
            autoWidth: false,
            ajax: {
                url: dispenseLoadUrl,
                type: 'POST',
                data: function (d) {
                    var searchEl = document.getElementById('dispenseSearch');
                    var typeEl = document.getElementById('dispenseTypeFilter');
                    d._token = getCsrfToken();
                    d.queue_type = typeEl ? String(typeEl.value || 'all') : 'all';
                    d.search.value = searchEl ? String(searchEl.value || '') : '';
                }
            },
            columns: [
                { data: 'rx_no', name: 'rx_no' },
                { data: 'patient_name', name: 'patient_name' },
                { data: 'ward_type', name: 'ward_type' },
                { data: 'doctor_name', name: 'doctor_name' },
                { data: 'drugs', name: 'drugs' },
                { data: 'priority', name: 'priority' },
                { data: 'queue_time', name: 'queue_time' },
                { data: 'status', name: 'status' },
                { data: 'row_id', orderable: false, searchable: false }
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function (data, type) {
                        if (type !== 'display') { return data; }
                        return '<span class="fw-700 text-primary" style="font-size:12px">' + escapeHtml(data || '-') + '</span>';
                    }
                },
                {
                    targets: 1,
                    render: function (data, type) {
                        if (type !== 'display') { return data; }
                        return '<div class="fw-700 fs-12">' + escapeHtml(data || '-') + '</div>';
                    }
                },
                {
                    targets: 2,
                    render: function (data, type) {
                        if (type !== 'display') { return data; }
                        var ward = String(data || 'OPD');
                        var cls = ward.toLowerCase() === 'ipd' ? 'red' : 'gray';
                        return '<span class="badge badge-' + cls + ' fs-10">' + escapeHtml(ward) + '</span>';
                    }
                },
                {
                    targets: 3,
                    render: function (data, type) {
                        if (type !== 'display') { return data; }
                        return '<span class="fs-11">' + escapeHtml(data || '-') + '</span>';
                    }
                },
                {
                    targets: 4,
                    render: function (data, type) {
                        if (type !== 'display') { return data; }
                        return '<span class="fs-11" style="max-width:160px;white-space:normal;display:inline-block">' + escapeHtml(data || '-') + '</span>';
                    }
                },
                {
                    targets: 5,
                    render: function (data, type) {
                        var priority = String(data || 'normal').toLowerCase();
                        if (type !== 'display') { return priority; }
                        var cls = priority === 'emergency' ? 'red' : (priority === 'urgent' ? 'orange' : 'gray');
                        return '<span class="badge badge-' + cls + '">' + escapeHtml(priority.toUpperCase()) + '</span>';
                    }
                },
                {
                    targets: 6,
                    render: function (data, type) {
                        if (type !== 'display') { return data; }
                        return '<span class="text-muted fs-11">' + escapeHtml(data || '-') + '</span>';
                    }
                },
                {
                    targets: 7,
                    render: function (data, type) {
                        var status = String(data || 'pending').toLowerCase().replace('_', ' ');
                        if (type !== 'display') { return status; }
                        var cls = status === 'pending' ? 'orange' : (status === 'on hold' ? 'gray' : 'green');
                        return '<span class="badge badge-' + cls + '">' + escapeHtml(status.charAt(0).toUpperCase() + status.slice(1)) + '</span>';
                    }
                },
                {
                    targets: 8,
                    render: function (data, type, row) {
                        if (type !== 'display') { return data; }

                        var sourceType = escapeJs(row.source_type || '');
                        var sourceId = parseInt(row.source_id, 10) || 0;
                        var priority = String(row.priority || '').toLowerCase();
                        return '<div style="display:flex;gap:3px">' +
                            '<button class="btn btn-success btn-xs" onclick="openPrescriptionDispense(\'' + sourceType + '\',' + sourceId + ')">✅ Dispense</button>' +
                            '<button class="btn btn-secondary btn-xs" onclick="openPrescriptionDispense(\'' + sourceType + '\',' + sourceId + ')">👁️ View</button>' +
                            (priority !== 'stat' ? '<button class="btn btn-warning btn-xs" onclick="holdRx(this)">⏸</button>' : '') +
                            '</div>';
                    }
                }
            ],
            language: {
                search: '',
                searchPlaceholder: 'Search queue...'
            }
        });

        var queueReload = function () {
            if (dispenseTable) {
                dispenseTable.ajax.reload();
            }
        };

        var debounceTimer = null;
        var searchEl = document.getElementById('dispenseSearch');
        if (searchEl) {
            searchEl.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(queueReload, 300);
            });
        }

        var typeEl = document.getElementById('dispenseTypeFilter');
        if (typeEl) {
            typeEl.addEventListener('change', queueReload);
        }
    }

    function getStatOrdersLoadUrl() {
        if (typeof window.route === 'function') {
            try {
                return window.route('statOrdersLoad');
            } catch (e) {
                return '';
            }
        }
        return '';
    }

    function getDashboardCountsUrl() {
        if (typeof window.route === 'function') {
            try {
                return window.route('dashboardCounts');
            } catch (e) {
                return '';
            }
        }
        return '';
    }

    function formatCount(value) {
        var number = parseInt(value, 10);
        if (isNaN(number)) {
            number = 0;
        }
        return number.toLocaleString('en-IN');
    }

    function setCountText(id, value) {
        var el = document.getElementById(id);
        if (el) {
            el.textContent = formatCount(value);
        }
    }

    function applyDashboardCounts(counts) {
        counts = counts || {};
        setCountText('phQueuePendingCount', counts.queue_pending);
        setCountText('phQueuePendingTabCount', counts.queue_pending);
        setCountText('phStatOrdersSubCount', counts.stat_orders);
        setCountText('phStatOrdersTabCount', counts.stat_orders);
        setCountText('phExpiryAlertsCount', counts.expiry_alerts);
        setCountText('phExpiryAlertsTabCount', counts.expiry_alerts);
        setCountText('phLowStockItemsCount', counts.low_stock_items);
        setCountText('phDrugItemsCount', counts.drug_items);
        setCountText('phTodayDispensedCount', counts.today_dispensed || 0);
        var salesText = '\u20B9' + parseFloat(counts.today_sales || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        var salesEl = document.getElementById('phTodaySalesAmount');
        if (salesEl) {
            salesEl.textContent = salesText;
        }
    }

    function loadDashboardCounts() {
        var url = getDashboardCountsUrl();
        if (!url || typeof window.fetch !== 'function') {
            return;
        }

        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken() || ''
            }
        })
            .then(function (res) { return res.ok ? res.json() : Promise.reject(res); })
            .then(applyDashboardCounts)
            .catch(function () {});
    }

    function renderStatOrders(listEl, stats) {
        listEl.innerHTML = (stats || [])
            .map(function (s) {
                var elapsed = parseInt(String(s.elapsed || '0'), 10) || 0;
                var elapsedText = s.elapsed || (elapsed + ' min');
                return '' +
                    '<div style="background:#fff5f5;border:1.5px solid rgba(198,40,40,.2);border-radius:10px;padding:14px;margin-bottom:10px">' +
                    '<div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px">' +
                    '<div><div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">' +
                    '<span class="badge badge-red">🚨 STAT</span><span class="fw-700 fs-13">' + escapeHtml(s.patient || s.patient_name || '-') + '</span></div>' +
                    '<div class="fs-12 text-muted mb-4"><b>Drug:</b> ' + escapeHtml(s.drug || s.drugs || '-') + '</div>' +
                    '<div class="fs-12 text-muted"><b>Ordered by:</b> ' + escapeHtml(s.doctor || s.ordered_by || '-') + ' | <b>Time:</b> ' + escapeHtml(s.time || '-') + '</div></div>' +
                    '<div style="text-align:right;flex-shrink:0"><div style="font-size:20px;font-weight:900;color:' + (elapsed > 15 ? 'var(--danger)' : 'var(--warning)') + '">' + escapeHtml(elapsedText) + '</div>' +
                    '<div class="fs-10 text-muted">elapsed</div>' +
                    '<button class="btn btn-danger btn-xs mt-8" onclick="dispenseSTAT(this, \'' + (escapeHtml(s.rx || s.rx_no || '')) + '\')">🚨 Dispense NOW</button></div></div></div>';
            })
            .join('');
    }

    function loadStatOrders() {
        var list = document.getElementById('statOrdersList');
        if (!list) {
            return;
        }

        var sampleStats = [
            { rx: 'RX-STAT-001', patient: 'Mohan Lal Gupta - ICU Bed 3', drug: 'Inj. Noradrenaline 4mg + Dopamine 200mg', time: '10:05', elapsed: '8 min', doctor: 'Dr. Negi' },
            { rx: 'RX-STAT-002', patient: 'Deepak Rawat - HDU Bed 1', drug: 'Inj. Furosemide 80mg IV Push + O2 Supply', time: '10:12', elapsed: '1 min', doctor: 'Dr. Bisht' },
            { rx: 'RX-STAT-003', patient: 'Baby Renu - NICU Bed 2', drug: 'Inj. Ampicillin 250mg + Gentamicin 20mg', time: '09:55', elapsed: '18 min', doctor: 'Dr. Verma' }
        ];

        var url = getStatOrdersLoadUrl();
        if (!url || typeof window.fetch !== 'function') {
            renderStatOrders(list, sampleStats);
            return;
        }

        // Fetch dynamic STAT orders from server; fall back to sample on error.
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken() || ''
            }
        })
            .then(function (res) { return res.ok ? res.json() : Promise.reject(res); })
            .then(function (data) {
                // support both plain array and Laravel-style { data: [...] }
                var stats = Array.isArray(data) ? data : (Array.isArray(data.data) ? data.data : null);
                if (!stats) {
                    renderStatOrders(list, sampleStats);
                    return;
                }
                renderStatOrders(list, stats);
            })
            .catch(function () {
                renderStatOrders(list, sampleStats);
            });
    }

    function loadRxValidation() {
        var list = document.getElementById('rxValidateList');
        if (!list) {
            return;
        }
        var rxList = [
            { rx: 'RX-2024-1853', patient: 'Sunita Rawat', drug: 'Tab. Warfarin 5mg', issue: 'HIGH RISK: Warfarin - INR not checked today. Check before dispensing.', type: 'safety' },
            { rx: 'RX-2024-1854', patient: 'Rajesh Sharma', drug: 'Inj. Insulin 20u', issue: 'Dose seems high for body weight (60 kg) - verify with prescribing doctor.', type: 'dose' },
            { rx: 'RX-2024-1855', patient: 'Meena Bisht', drug: 'Tab. Clarithromycin 500mg', issue: 'Drug interaction: Patient on Amlodipine + Clarithromycin (QTc prolongation risk).', type: 'interaction' }
        ];

        list.innerHTML = rxList
            .map(function (r) {
                var label = r.type === 'safety' ? '⚠️ Safety' : r.type === 'dose' ? '💊 Dose' : '🔄 Interaction';
                return '' +
                    '<div style="background:var(--warning-light);border:1.5px solid rgba(245,124,0,.2);border-radius:10px;padding:14px;margin-bottom:10px">' +
                    '<div style="display:flex;justify-content:space-between;align-items:start;gap:12px">' +
                    '<div><div class="d-flex align-center gap-8 mb-4"><span class="badge badge-orange">' + label + '</span><span class="fw-700 fs-13">' + r.patient + ' - ' + r.rx + '</span></div>' +
                    '<div class="fs-12 mb-4"><b>Drug:</b> ' + r.drug + '</div><div class="fs-12 text-muted">' + r.issue + '</div></div>' +
                    '<div style="flex-shrink:0;display:flex;flex-direction:column;gap:4px">' +
                    '<button class="btn btn-success btn-xs" onclick="approveRx(this)">✅ Approve</button>' +
                    '<button class="btn btn-danger btn-xs" onclick="rejectRx(this)">❌ Reject</button>' +
                    '<button class="btn btn-secondary btn-xs" onclick="showToast(\'Escalate\',\'Escalated to senior pharmacist\',\'info\')">↗ Escalate</button>' +
                    '</div></div></div>';
            })
            .join('');
    }

    function getCsrfToken() {
        var tokenMeta = document.querySelector('meta[name="csrf-token"]');
        if (tokenMeta && tokenMeta.getAttribute('content')) {
            return tokenMeta.getAttribute('content');
        }
        if (window.Laravel && window.Laravel.csrfToken) {
            return window.Laravel.csrfToken;
        }
        return '';
    }

    function getStockLoadUrl() {
        if (typeof window.route === 'function') {
            try {
                return window.route('stockLoad');
            } catch (e) {
                return '';
            }
        }
        return '';
    }
    
    function getQuarantineStockLoadUrl() {
        if (typeof window.route === 'function') {
            try {
                return window.route('quarantineStockLoad');
            } catch (e) {
                return '';
            }
        }
        return '';
    }

    function getStockExportUrl() {
        if (typeof window.route === 'function') {
            try {
                return window.route('stockExport');
            } catch (e) {
                return '';
            }
        }
        return '';
    }

    function getShowBadStockFormUrl() {
        if (typeof window.route === 'function') {
            try {
                return window.route('showBadStockForm');
            } catch (e) {
                return '';
            }
        }
        return '';
    }

    function getAdjustBadStockUrl() {
        if (typeof window.route === 'function') {
            try {
                return window.route('adjustBadStock');
            } catch (e) {
                return '';
            }
        }
        return '';
    }
    
    function getQuarantineStockExportUrl() {
        if (typeof window.route === 'function') {
            try {
                return window.route('quarantineStockExport');
            } catch (e) {
                return '';
            }
        }
        return '';
    }

    function getShowBadQuarantineStockFormUrl() {
        if (typeof window.route === 'function') {
            try {
                return window.route('showBadQuarantineStockForm');
            } catch (e) {
                return '';
            }
        }
        return '';
    }

    function getAdjustBadQuarantineStockUrl() {
        if (typeof window.route === 'function') {
            try {
                return window.route('adjustBadQuarantineStock');
            } catch (e) {
                return '';
            }
        }
        return '';
    }

    function getExpiryLoadUrl() {
        if (typeof window.route === 'function') {
            try {
                return window.route('expiryLoad');
            } catch (e) {
                return '';
            }
        }
        return '';
    }

    function getExpiryProcessUrl() {
        if (typeof window.route === 'function') {
            try {
                return window.route('expiryProcess');
            } catch (e) {
                return '';
            }
        }
        return '';
    }

    function getExpiryQuarantineUrl(id) {
        if (typeof window.route === 'function') {
            try {
                try { return window.route('expiryQuarantine').replace('__ID__', String(id)); } catch (e) { return ''; }
            } catch (e) {
                return '';
            }
        }
        return '';
    }

    function getPurchaseLoadUrl() {
        if (typeof window.route === 'function') {
            try {
                return window.route('purchaseLoad');
            } catch (e) {
                return '';
            }
        }
        return '';
    }

    function getPurchasePrintUrl(id) {
        if (typeof window.route === 'function') {
            try {
                return window.route('purchasePrint').replace('__ID__', String(id));
            } catch (e) {
                return '';
            }
        }
        return '';
    }

    function getPurchaseShowformUrl() {
        if (typeof window.route === 'function') {
            try {
                return window.route('purchaseShowform');
            } catch (e) {
                return '';
            }
        }
        return '';
    }

    function escapeHtml(text) {
        return String(text || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function escapeJs(text) {
        return String(text || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
    }

    function clearGrnFormErrors() {
        var form = document.getElementById('grnForm');
        if (!form) { return; }

        form.querySelectorAll('.is-invalid').forEach(function (el) {
            el.classList.remove('is-invalid');
            el.removeAttribute('aria-invalid');
        });
        form.querySelectorAll('.invalid-feedback, .err.text-danger').forEach(function (el) {
            el.parentNode.removeChild(el);
        });
    }

    function toBracketFieldName(name) {
        if (!name) { return name; }
        var parts = name.split('.');
        var bracketed = parts[0];
        for (var i = 1; i < parts.length; i++) {
            bracketed += '[' + parts[i] + ']';
        }
        return bracketed;
    }

    function addGrnFieldError(name, message) {
        if (!name || !message) { return; }
        var selector = toBracketFieldName(name);
        var field = document.querySelector('#grnForm [name="' + selector + '"]');
        if (!field) {
            field = document.querySelector('#grnForm [name="' + name + '"]');
        }
        if (!field) { return; }

        field.classList.add('is-invalid');
        field.setAttribute('aria-invalid', 'true');

        var feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        feedback.style.display = 'block';
        feedback.textContent = message;

        if (field.nextSibling) {
            field.parentNode.insertBefore(feedback, field.nextSibling);
        } else {
            field.parentNode.appendChild(feedback);
        }
    }

    function formatGrnErrorMessages(errors) {
        if (!Array.isArray(errors) || errors.length === 0) { return ''; }
        var messages = errors.map(function (error) {
            return error.message || String(error || 'Invalid input');
        });
        return '<div><strong>Please fix the following errors:</strong><ul class="mb-0 ps-3">' + messages.map(function (m) {
            return '<li>' + escapeHtml(m) + '</li>';
        }).join('') + '</ul></div>';
    }

    function formatCurrency(value) {
        var amount = parseFloat(value || 0);
        if (isNaN(amount)) {
            return '₹0.00';
        }
        return '₹' + amount.toFixed(2);
    }

    function parseIsoDate(isoText) {
        if (!isoText) {
            return null;
        }
        var date = new Date(isoText + 'T00:00:00');
        return isNaN(date.getTime()) ? null : date;
    }

    function toNumber(value) {
        var num = parseFloat(value);
        return isNaN(num) ? 0 : num;
    }

    function currentInventoryFilters() {
        var searchEl = document.getElementById('drugSearch');
        var categoryEl = document.getElementById('drugCategoryFilter');
        var stockEl = document.getElementById('drugStockFilter');
        var expiryEl = document.getElementById('drugExpiryFilter');

        return {
            search: searchEl ? String(searchEl.value || '').trim() : '',
            category_id: categoryEl ? String(categoryEl.value || '') : '',
            stock_filter: stockEl ? String(stockEl.value || 'all') : 'all',
            expiry_filter: expiryEl ? String(expiryEl.value || 'all') : 'all'
        };
    }
    function currentQuarantineInventoryFilters() {
        var searchEl = document.getElementById('quarantineDrugSearch');
        var categoryEl = document.getElementById('quarantineDrugCategoryFilter');
        var expiryEl = document.getElementById('quarantineDrugExpiryFilter');

        return {
            search: searchEl ? String(searchEl.value || '').trim() : '',
            category_id: categoryEl ? String(categoryEl.value || '') : '',
            expiry_filter: expiryEl ? String(expiryEl.value || 'all') : 'all'
        };
    }

    function loadDrugInventory() {
        var tableNode = document.getElementById('drugInventoryTable');
        if (!tableNode) {
            return;
        }

        var stockLoadUrl = getStockLoadUrl();
        if (!stockLoadUrl || typeof window.$ === 'undefined' || typeof window.$.fn.DataTable === 'undefined') {
            return;
        }

        if (inventoryTable) {
            return;
        }

        inventoryTable = window.$('#drugInventoryTable').DataTable({
            processing: true,
            serverSide: true,
            paging: true,
            info: true,
            searching: true,
            ordering: true,
            responsive: true,
            autoWidth: false,
            ajax: {
                url: stockLoadUrl,
                type: 'POST',
                data: function (d) {
                    var filters = currentInventoryFilters();
                    d._token = getCsrfToken();
                    d.category_id = filters.category_id;
                    d.stock_filter = filters.stock_filter;
                    d.expiry_filter = filters.expiry_filter;
                    d.search.value = filters.search;
                }
            },
            columns: [
                { data: 'medicine_name', name: 'medicine.name' },
                { data: 'category_name', name: 'medicine.category.name', orderable: false },
                { data: 'form_name', name: 'medicine.unit', orderable: false },
                { data: 'batch_no', name: 'batch_no' },
                { data: 'expiry_date', name: 'expiry_date' },
                { data: 'available_qty', name: 'available_qty' },
                { data: 'min_level', name: 'medicine.min_level', orderable: false },
                { data: 'unit_mrp', name: 'unit_mrp' },
                { data: 'status', name: 'status' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function (data, type) {
                        if (type !== 'display') {
                            return data;
                        }
                        return '<span class="fw-700 fs-12">' + (data || '-') + '</span>';
                    }
                },
                {
                    targets: 1,
                    render: function (data, type) {
                        if (type !== 'display') {
                            return data;
                        }
                        return '<span class="badge badge-indigo fs-10">' + (data || '-') + '</span>';
                    }
                },
                {
                    targets: 2,
                    render: function (data, type) {
                        if (type !== 'display') {
                            return data;
                        }
                        return '<span class="fs-11">' + (data || '-') + '</span>';
                    }
                },
                {
                    targets: 3,
                    render: function (data, type) {
                        if (type !== 'display') {
                            return data;
                        }
                        return '<span class="fs-11 text-muted">' + (data || '-') + '</span>';
                    }
                },
                {
                    targets: 4,
                    render: function (data, type, row) {
                        if (type !== 'display') {
                            return data;
                        }
                        if (!data || data === '-') {
                            return '-';
                        }

                        var expiryDate = parseIsoDate(row.expiry_iso || '');
                        var style = '';
                        if (expiryDate) {
                            var today = new Date();
                            today.setHours(0, 0, 0, 0);
                            var diff = Math.floor((expiryDate.getTime() - today.getTime()) / (1000 * 60 * 60 * 24));
                            if (diff < 30) {
                                style = 'color:var(--danger);font-weight:700';
                            } else if (diff < 90) {
                                style = 'color:var(--warning)';
                            }
                        }

                        return '<span style="white-space:nowrap;display:inline-block;' + style + '">' + data + '</span>';
                    }
                },
                {
                    targets: 5,
                    render: function (data, type, row) {
                        var stock = toNumber(data);
                        var medicineStock = toNumber(row.medicine_available_qty);
                        if (type !== 'display') {
                            return stock;
                        }

                        var minLevel = toNumber(row.reorder_level || row.min_level);
                        var style = '';
                        if (medicineStock <= 0) {
                            style = 'color:var(--danger);font-weight:700';
                        } else if (minLevel > 0 && medicineStock <= minLevel) {
                            style = 'color:var(--warning);font-weight:600';
                        }

                        var totalHint = '';
                        if (medicineStock !== stock) {
                            totalHint = '<div class="fs-10 text-muted">Total usable: ' + medicineStock + '</div>';
                        }

                        return '<span style="' + style + '">' + stock + '</span>' + totalHint;
                    }
                },
                {
                    targets: 6,
                    render: function (data, type) {
                        var minLevel = toNumber(data);
                        if (type !== 'display') {
                            return minLevel;
                        }
                        return '<span class="text-muted">' + minLevel + '</span>';
                    }
                },
                {
                    targets: 7,
                    render: function (data, type) {
                        var amount = parseFloat(data || 0);
                        if (type !== 'display') {
                            return isNaN(amount) ? 0 : amount;
                        }
                        if (isNaN(amount)) {
                            return '₹0.00';
                        }
                        return '₹' + amount.toFixed(2);
                    }
                },
                {
                    targets: 8,
                    render: function (data, type, row) {
                        if (type !== 'display') {
                            return data;
                        }
                        var stock = toNumber(row.available_qty);
                        var medicineStock = toNumber(row.medicine_available_qty);
                        var reorderLevel = toNumber(row.reorder_level || row.min_level);
                        var label = 'In Stock';
                        var cls = 'green';

                        if (medicineStock > 0 && (stock <= 0 || String(data || '').toLowerCase() === 'expired')) {
                            label = 'Alt Batch Available';
                            cls = 'indigo';
                        } else if (medicineStock <= 0) {
                            label = 'Critical';
                            cls = 'red';
                        } else if (reorderLevel > 0 && medicineStock <= reorderLevel) {
                            label = 'Low';
                            cls = 'orange';
                        }

                        return '<span class="badge badge-' + cls + '">' + label + '</span>';
                    }
                },
                {
                    targets: 9,
                    render: function (data, type, row) {
                        if (type !== 'display') {
                            return data;
                        }

                        var badStockBtn = '';
                        if (row.actions && row.actions !== '-') {
                            badStockBtn = '<button class="btn btn-secondary btn-xs bad-stock-btn" data-id="' + row.id + '">✏️</button>';
                        }

                        return '<div style="display:flex;gap:3px">' +
                            '<button class="btn btn-primary btn-xs" onclick="openModal(\'grnModal\')">+Stock</button>' +
                            badStockBtn +
                            '</div>';
                    }
                }
            ],
            language: {
                search: '',
                searchPlaceholder: 'Search inventory...'
            }
        });

        var reload = function () {
            if (inventoryTable) {
                inventoryTable.ajax.reload();
            }
        };

        var debounceTimer = null;
        var searchBox = document.getElementById('drugSearch');
        if (searchBox) {
            searchBox.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(reload, 300);
            });
        }

        ['drugCategoryFilter', 'drugStockFilter', 'drugExpiryFilter'].forEach(function (id) {
            var el = document.getElementById(id);
            if (el) {
                el.addEventListener('change', reload);
            }
        });
    }

    function loadQuarantineInventory() {
        var tableNode = document.getElementById('quarantineDrugInventoryTable');
        if (!tableNode) {
            return;
        }

        var quarantineStockLoadUrl = getQuarantineStockLoadUrl();
        if (!quarantineStockLoadUrl || typeof window.$ === 'undefined' || typeof window.$.fn.DataTable === 'undefined') {
            return;
        }

        if (quarantineInventoryTable) {
            return;
        }

        quarantineInventoryTable = window.$('#quarantineDrugInventoryTable').DataTable({
            processing: true,
            serverSide: true,
            paging: true,
            info: true,
            searching: true,
            ordering: true,
            responsive: true,
            autoWidth: false,
            ajax: {
                url: quarantineStockLoadUrl,
                type: 'POST',
                data: function (d) {
                    var filters = currentQuarantineInventoryFilters();
                    d._token = getCsrfToken();
                    d.category_id = filters.category_id;
                    d.stock_filter = filters.stock_filter;
                    d.expiry_filter = filters.expiry_filter;
                    d.search.value = filters.search;
                }
            },
            columns: [
                { data: 'medicine_name', name: 'medicine.name' },
                { data: 'category_name', name: 'medicine.category.name', orderable: false },
                { data: 'form_name', name: 'medicine.unit', orderable: false },
                { data: 'batch_no', name: 'batch_no' },
                { data: 'expiry_date', name: 'expiry_date' },
                { data: 'reserved_qty', name: 'reserved_qty' },
                { data: 'min_level', name: 'medicine.min_level', orderable: false },
                { data: 'unit_mrp', name: 'unit_mrp' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function (data, type) {
                        if (type !== 'display') {
                            return data;
                        }
                        return '<span class="fw-700 fs-12">' + (data || '-') + '</span>';
                    }
                },
                {
                    targets: 1,
                    render: function (data, type) {
                        if (type !== 'display') {
                            return data;
                        }
                        return '<span class="badge badge-indigo fs-10">' + (data || '-') + '</span>';
                    }
                },
                {
                    targets: 2,
                    render: function (data, type) {
                        if (type !== 'display') {
                            return data;
                        }
                        return '<span class="fs-11">' + (data || '-') + '</span>';
                    }
                },
                {
                    targets: 3,
                    render: function (data, type) {
                        if (type !== 'display') {
                            return data;
                        }
                        return '<span class="fs-11 text-muted">' + (data || '-') + '</span>';
                    }
                },
                {
                    targets: 4,
                    render: function (data, type, row) {
                        if (type !== 'display') {
                            return data;
                        }
                        if (!data || data === '-') {
                            return '-';
                        }

                        var expiryDate = parseIsoDate(row.expiry_iso || '');
                        var style = '';
                        if (expiryDate) {
                            var today = new Date();
                            today.setHours(0, 0, 0, 0);
                            var diff = Math.floor((expiryDate.getTime() - today.getTime()) / (1000 * 60 * 60 * 24));
                            if (diff < 30) {
                                style = 'color:var(--danger);font-weight:700';
                            } else if (diff < 90) {
                                style = 'color:var(--warning)';
                            }
                        }

                        return '<span style="white-space:nowrap;display:inline-block;' + style + '">' + data + '</span>';
                    }
                },
                {
                    targets: 5,
                    render: function (data, type, row) {
                        var stock = toNumber(data);
                        var medicineStock = toNumber(row.medicine_available_qty);
                        if (type !== 'display') {
                            return stock;
                        }

                        var minLevel = toNumber(row.reorder_level || row.min_level);
                        var style = '';
                        if (medicineStock <= 0) {
                            style = 'color:var(--danger);font-weight:700';
                        } else if (minLevel > 0 && medicineStock <= minLevel) {
                            style = 'color:var(--warning);font-weight:600';
                        }

                        return '<span style="' + style + '">' + stock + '</span>';
                    }
                },
                {
                    targets: 6,
                    render: function (data, type) {
                        var minLevel = toNumber(data);
                        if (type !== 'display') {
                            return minLevel;
                        }
                        return '<span class="text-muted">' + minLevel + '</span>';
                    }
                },
                {
                    targets: 7,
                    render: function (data, type) {
                        var amount = parseFloat(data || 0);
                        if (type !== 'display') {
                            return isNaN(amount) ? 0 : amount;
                        }
                        if (isNaN(amount)) {
                            return '₹0.00';
                        }
                        return '₹' + amount.toFixed(2);
                    }
                },
                {
                    targets: 8,
                    render: function (data, type, row) {
                        if (type !== 'display') {
                            return data;
                        }

                        var badStockBtn = '';
                        if (row.actions && row.actions !== '-') {
                            badStockBtn = '<button class="btn btn-secondary btn-xs bad-quarantine-stock-btn" data-id="' + row.id + '">✏️</button>';
                        }

                        return '<div style="display:flex;gap:3px">' +
                            '' +
                            badStockBtn +
                            '</div>';
                    }
                }
            ],
            language: {
                search: '',
                searchPlaceholder: 'Search inventory...'
            }
        });

        var reload = function () {
            if (quarantineInventoryTable) {
                quarantineInventoryTable.ajax.reload();
            }
        };

        var debounceTimer = null;
        var searchBox = document.getElementById('quarantineDrugSearch');
        if (searchBox) {
            searchBox.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(reload, 300);
            });
        }

        ['quarantineDrugCategoryFilter', 'quarantineDrugExpiryFilter'].forEach(function (id) {
            var el = document.getElementById(id);
            if (el) {
                el.addEventListener('change', reload);
            }
        });
    }

    function loadExpiryAlerts() {
        var tableNode = document.getElementById('expiryAlertsTable');
        if (!tableNode) {
            return;
        }

        var expiryLoadUrl = getExpiryLoadUrl();
        if (!expiryLoadUrl || typeof window.$ === 'undefined' || typeof window.$.fn.DataTable === 'undefined') {
            return;
        }

        if (expiryTable) {
            return;
        }

        expiryTable = window.$('#expiryAlertsTable').DataTable({
            processing: true,
            serverSide: true,
            paging: true,
            info: true,
            searching: true,
            ordering: true,
            responsive: true,
            autoWidth: false,
            ajax: {
                url: expiryLoadUrl,
                type: 'POST',
                data: function (d) {
                    var searchEl = document.getElementById('expirySearch');
                    var rangeEl = document.getElementById('expiryRangeFilter');
                    d._token = getCsrfToken();
                    d.expiry_filter = rangeEl ? rangeEl.value : 'all_alerts';
                    if (searchEl && searchEl.value) {
                        d.search = { value: searchEl.value };
                    }
                }
            },
            columns: [
                { data: 'medicine_name', name: 'medicine.name' },
                { data: 'batch_no', name: 'batch_no' },
                { data: 'expiry_date', name: 'expiry_date' },
                { data: 'days_left', name: 'expiry_date', orderable: true },
                { data: 'available_qty', name: 'available_qty' },
                { data: 'days_left', orderable: false, searchable: false },
                { data: 'id', orderable: false, searchable: false }
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function (data, type) {
                        if (type !== 'display') { return data; }
                        return '<span class="fw-700 fs-12">' + (data || '-') + '</span>';
                    }
                },
                {
                    targets: 1,
                    render: function (data, type) {
                        if (type !== 'display') { return data; }
                        return '<span class="text-muted fs-11">' + (data || '-') + '</span>';
                    }
                },
                {
                    targets: 2,
                    render: function (data, type, row) {
                        if (type !== 'display') { return data; }
                        var days = parseInt(row.days_left, 10);
                        var style = (!isNaN(days) && days < 20) ? 'color:var(--danger);font-weight:700' : 'color:var(--warning);font-weight:700';
                        return '<span style="white-space:nowrap;' + style + '">' + (data || '-') + '</span>';
                    }
                },
                {
                    targets: 3,
                    render: function (data, type) {
                        var days = parseInt(data, 10);
                        if (type !== 'display') { return isNaN(days) ? 9999 : days; }
                        if (isNaN(days)) { return '-'; }
                        var cls = days < 0 ? 'red' : (days < 20 ? 'red' : 'orange');
                        var label = days < 0 ? 'Expired' : days + ' days';
                        return '<span class="badge badge-' + cls + '">' + label + '</span>';
                    }
                },
                {
                    targets: 4,
                    render: function (data, type) {
                        if (type !== 'display') { return toNumber(data); }
                        return String(toNumber(data));
                    }
                },
                {
                    targets: 5,
                    render: function (data, type) {
                        var days = parseInt(data, 10);
                        if (type !== 'display') { return data; }
                        var action;
                        if (isNaN(days) || days < 0) {
                            action = 'Write Off / Return';
                        } else if (days <= 20) {
                            action = 'Return to Supplier';
                        } else if (days <= 30) {
                            action = 'Accelerate Use';
                        } else if (days <= 60) {
                            action = 'Use Priority';
                        } else {
                            action = 'Plan Usage';
                        }
                        return '<span class="fs-11">' + action + '</span>';
                    }
                },
                {
                    targets: 6,
                    render: function (data, type) {
                        if (type !== 'display') { return data; }
                        return '<div style="display:flex;gap:3px">' +
                            '<button class="btn btn-warning btn-xs" onclick="expiryQuarantine(' + data + ', this)">🔒 Quarantine</button>' +
                            '<button class="btn btn-danger btn-xs" onclick="expiryReturn(' + data + ', this)">↩ Return</button>' +
                            '</div>';
                    }
                }
            ],
            language: {
                search: '',
                searchPlaceholder: 'Search expiry...'
            },
            drawCallback: function (settings) {
                var info = this.api().page.info();
                var bannerEl = document.getElementById('expiryAlertMsg');
                if (bannerEl) {
                    bannerEl.innerHTML = '<b>' + info.recordsTotal + ' item(s) found in expiry alerts.</b> Please review and take appropriate action.';
                }
            }
        });

        // Filter events
        var expiryReload = function () {
            if (expiryTable) { expiryTable.ajax.reload(); }
        };

        var expiryDebounce = null;
        var expirySearchEl = document.getElementById('expirySearch');
        if (expirySearchEl) {
            expirySearchEl.addEventListener('input', function () {
                clearTimeout(expiryDebounce);
                expiryDebounce = setTimeout(expiryReload, 300);
            });
        }

        var expiryRangeEl = document.getElementById('expiryRangeFilter');
        if (expiryRangeEl) {
            expiryRangeEl.addEventListener('change', expiryReload);
        }
    }

    function getGrnLoadUrl() {
        if (typeof window.route === 'function') {
            try { return window.route('grnLoad'); } catch (e) { return ''; }
        }
        return '';
    }

    function getGrnStoreUrl() {
        if (typeof window.route === 'function') {
            try { return window.route('grnStore'); } catch (e) { return ''; }
        }
        return '';
    }

    function getGrnApprovedPOsUrl() {
        if (typeof window.route === 'function') {
            try { return window.route('grnApprovedPOs'); } catch (e) { return ''; }
        }
        return '';
    }

    function getGrnViewUrl(id) {
        if (typeof window.route === 'function') {
            try { return window.route('grnView').replace('__ID__', String(id)); } catch (e) { return ''; }
        }
        return '';
    }

    function getGrnPrintUrl(id) {
        if (typeof window.route === 'function') {
            try { return window.route('grnPrint').replace('__ID__', String(id)); } catch (e) { return ''; }
        }
        return '';
    }

    var grnLogTable = null;

    function loadGRNLog() {
        var tableNode = document.getElementById('grnLogTable');
        if (!tableNode) return;
        var url = getGrnLoadUrl();
        if (!url || typeof window.$ === 'undefined' || typeof window.$.fn.DataTable === 'undefined') return;
        if (grnLogTable) return;

        grnLogTable = window.$('#grnLogTable').DataTable({
            processing: true, serverSide: true, paging: true, info: true,
            searching: true, ordering: true, responsive: true, autoWidth: false,
            ajax: { url: url, type: 'POST', data: function (d) { d._token = getCsrfToken(); } },
            columns: [
                { data: null, orderable: false, searchable: false, render: function (d, t, r, m) { return m.row + m.settings._iDisplayStart + 1; } },
                { data: 'grn_no', name: 'grn_no' },
                { data: 'po_no', name: 'po_no', orderable: false },
                { data: 'supplier_name', name: 'supplier_name', orderable: false },
                { data: 'invoice_no', name: 'invoice_no', defaultContent: '—' },
                { data: 'items_count', orderable: false, searchable: false },
                { data: 'total_amount', name: 'total_amount', render: function (v) { return '<span class="fw-700">' + formatCurrency(v) + '</span>'; } },
                { data: 'received_by_name', orderable: false },
                { data: 'received_at', name: 'received_at' },
                { data: 'id', name: 'id', orderable: false, searchable: false }
            ],
            columnDefs: [
                { targets: 1, render: function (d, t) { return t !== 'display' ? d : '<span class="fw-700 text-primary">' + escapeHtml(d || '-') + '</span>'; } },
                { targets: 2, render: function (d, t) { return t !== 'display' ? d : '<span class="text-muted">' + escapeHtml(d || '-') + '</span>'; } },
                {
                    targets: 9,
                    render: function (data, type, row) {
                        if (type !== 'display') { return data; }
                        var printUrl = row.id ? getGrnPrintUrl(row.id) : '';
                        var html = '<div style="display:flex;gap:4px">';
                        html += '<button class="btn btn-secondary btn-xs grn-view-btn" data-id="' + row.id + '">👁️</button>';
                        if (printUrl) {
                            html += '<button class="btn btn-secondary btn-xs grn-print-btn" data-url="' + escapeHtml(printUrl) + '">🖨️</button>';
                        }
                        html += '</div>';
                        return html;
                    }
                }
            ],
            language: { search: '', searchPlaceholder: 'Search GRN log...' }
        });
    }

    function loadPOList() {
        var tableNode = document.getElementById('purchaseOrdersTable');
        if (!tableNode) {
            return;
        }

        var purchaseLoadUrl = getPurchaseLoadUrl();
        if (!purchaseLoadUrl || typeof window.$ === 'undefined' || typeof window.$.fn.DataTable === 'undefined') {
            return;
        }

        if (poTable) {
            return;
        }

        poTable = window.$('#purchaseOrdersTable').DataTable({
            processing: true,
            serverSide: true,
            paging: true,
            info: true,
            searching: true,
            ordering: true,
            responsive: true,
            autoWidth: false,
            ajax: {
                url: purchaseLoadUrl,
                type: 'POST',
                data: function (d) {
                    d._token = getCsrfToken();
                }
            },
            columns: [
                { data: null, orderable: false, searchable: false, render: function (d, t, r, m) { return m.row + m.settings._iDisplayStart + 1; } },
                { data: 'bill_no', name: 'bill_no' },
                { data: 'bill_date', name: 'bill_date' },
                { data: 'supplier_name', name: 'supplier_name', orderable: false },
                { data: 'items_count', name: 'items_count', orderable: false, searchable: false },
                { data: 'created_by_name', name: 'created_by_name', orderable: false },
                { data: 'net_total', name: 'net_total' },
                { data: 'status', name: 'status', orderable: false },
                { data: 'id', name: 'id', orderable: false, searchable: false }
            ],
            columnDefs: [
                {
                    targets: 1,
                    render: function (data, type) {
                        if (type !== 'display') { return data; }
                        return '<span class="fw-700 text-primary">' + escapeHtml(data || '-') + '</span>';
                    }
                },
                {
                    targets: 6,
                    render: function (data, type) {
                        if (type !== 'display') {
                            var amount = parseFloat(data || 0);
                            return isNaN(amount) ? 0 : amount;
                        }
                        return '<span class="fw-700">' + formatCurrency(data) + '</span>';
                    }
                },
                {
                    targets: 7,
                    render: function (data, type) {
                        var st = String(data || 'pending').toLowerCase();
                        if (type !== 'display') { return st; }
                        var clsMap = { approved: 'green', rejected: 'red', received: 'blue', partially_received: 'indigo', pending: 'orange' };
                        var cls = clsMap[st] || 'gray';
                        var label = st.replace('_', ' ');
                        return '<span class="badge badge-' + cls + '">' + escapeHtml(label.charAt(0).toUpperCase() + label.slice(1)) + '</span>';
                    }
                },
                {
                    targets: 8,
                    render: function (data, type, row) {
                        if (type !== 'display') { return data; }
                        var st = String(row.status || 'pending').toLowerCase();
                        var html = '<div style="display:flex;gap:4px">';
                        html += '<button class="btn btn-secondary btn-xs po-view-btn" data-id="' + row.id + '">👁️</button>';
                        if (st === 'pending') {
                            html += '<button class="btn btn-secondary btn-xs po-approve-btn" data-id="' + row.id + '">✅</button>';
                            html += '<button class="btn btn-secondary btn-xs po-reject-btn" data-id="' + row.id + '">❌</button>';
                        } else if (st === 'approved' || st === 'partially_received') {
                            html += '<button class="btn btn-success btn-xs po-create-grn-btn" data-id="' + row.id + '">📥 Create GRN</button>';
                        }
                        var printUrl = row.id ? getPurchasePrintUrl(row.id) : '';
                        if (printUrl && st !== 'pending') {
                            html += '<button class="btn btn-secondary btn-xs po-print-btn" data-url="' + escapeHtml(printUrl) + '">🖨️</button>';
                        }
                        html += '</div>';
                        return html;
                    }
                }
            ],
            language: {
                search: '',
                searchPlaceholder: 'Search purchase orders...'
            }
        });
    }

    function loadMARContent() {
        var el = document.getElementById('marContent');
        if (!el) {
            return;
        }
        var marData = [
            { patient: 'Kavita Sharma', bed: 'A-14', slots: { '08:00': 'yes', '12:00': 'no', '18:00': 'no', '22:00': 'no' }, drugs: ['Amlodipine 5mg', 'Metformin 500mg'] },
            { patient: 'Babita Devi', bed: 'A-11', slots: { '08:00': 'yes', '12:00': 'yes', '18:00': 'no', '22:00': 'no' }, drugs: ['Meropenem 1g IV', 'Paracetamol 1g IV'] },
            { patient: 'Mohan Gupta', bed: 'A-08', slots: { '08:00': 'yes', '12:00': 'no', '18:00': 'no', '22:00': 'no' }, drugs: ['Insulin 8u SC'] }
        ];

        el.innerHTML = '<div class="table-wrap"><table class="hims-table"><thead><tr><th>Patient</th><th>Bed</th><th>Medications</th><th>08:00</th><th>12:00</th><th>18:00</th><th>22:00</th></tr></thead><tbody>' +
            marData.map(function (m) {
                var slots = Object.keys(m.slots).map(function (key) {
                    return m.slots[key] === 'yes'
                        ? '<td style="text-align:center"><span class="badge badge-green">✅</span></td>'
                        : '<td style="text-align:center"><button class="btn btn-outline-primary btn-xs" onclick="showToast(\'Given\',\'Medication administered\',\'success\')">Give</button></td>';
                }).join('');

                return '<tr><td class="fw-700">' + m.patient + '</td><td class="text-muted">' + m.bed + '</td><td class="fs-11">' + m.drugs.join('<br>') + '</td>' + slots + '</tr>';
            }).join('') +
            '</tbody></table></div>';
    }

    var dispenseState = {
        mode: 'walkin',
        patient: null,
        prescription: null,
        items: [],
        medicineSearchResults: []
    };

    function initDispenseModal() {
        var search = document.getElementById('walkInMedicineSearch');
        if (search && !search.dataset.bound) {
            search.dataset.bound = '1';
            var timer = null;
            search.addEventListener('input', function () {
                clearTimeout(timer);
                timer = setTimeout(loadWalkInMedicineOptions, 250);
            });
        }

        ['dispenseDiscount', 'dispensePaid'].forEach(function (id) {
            var el = document.getElementById(id);
            if (el && !el.dataset.bound) {
                el.dataset.bound = '1';
                el.addEventListener('input', recalcDispenseTotals);
            }
        });
    }

    function resetDispenseState(mode) {
        dispenseState = {
            mode: mode || 'walkin',
            patient: null,
            prescription: null,
            items: [],
            medicineSearchResults: dispenseState.medicineSearchResults || []
        };

        var form = document.getElementById('dispenseForm');
        if (form) {
            form.reset();
        }
        setValue('dispensePrescriptionType', '');
        setValue('dispensePrescriptionId', '');
        setValue('dispensePatientId', '');
        setText('dispenseModeBadge', mode === 'prescription' ? 'Prescription' : 'Walk-in');
        setText('dispenseModalTitle', mode === 'prescription' ? '💊 Dispense Prescription' : '💊 Walk-in Dispense');
        renderDispensePatient(null);
        renderDispenseItems();
        recalcDispenseTotals();
    }

    function setValue(id, value) {
        var el = document.getElementById(id);
        if (el) { el.value = value == null ? '' : String(value); }
    }

    function setText(id, value) {
        var el = document.getElementById(id);
        if (el) { el.textContent = value == null ? '' : String(value); }
    }

    function renderDispensePatient(payload) {
        var card = document.getElementById('dispensePatientCard');
        var allergy = document.getElementById('dispenseAllergyAlert');
        if (!card) { return; }

        if (!payload || !payload.patient) {
            card.innerHTML = '<div class="patient-chip"><div class="patient-chip-avatar">W</div><div class="patient-chip-info">' +
                '<div class="patient-chip-name">Walk-in Customer</div><div class="patient-chip-meta">No prescription selected</div></div></div>';
            if (allergy) { allergy.style.display = 'none'; }
            return;
        }

        var p = payload.patient;
        var rx = payload.prescription || {};
        var avatar = String(p.name || 'P').charAt(0).toUpperCase();
        card.innerHTML = '<div class="patient-chip"><div class="patient-chip-avatar">' + escapeHtml(avatar) + '</div><div class="patient-chip-info">' +
            '<div class="patient-chip-name">' + escapeHtml(p.name || '-') + '</div>' +
            '<div class="patient-chip-meta">' + escapeHtml(p.mrn || '-') + ' | ' + escapeHtml(p.age || '-') + ' | ' + escapeHtml(p.gender || '-') + ' | ' + escapeHtml(p.blood_group || '-') + '</div>' +
            '<div class="patient-chip-meta">Rx: ' + escapeHtml(rx.rx_no || '-') + ' | Doctor: ' + escapeHtml(rx.doctor_name || '-') + ' | Valid: ' + escapeHtml(rx.valid_till || 'NA') + '</div>' +
            '</div></div>';

        if (allergy) {
            if (p.known_allergies) {
                allergy.style.display = '';
                allergy.querySelector('div').innerHTML = '<b>Allergy Alert:</b> ' + escapeHtml(p.known_allergies);
            } else {
                allergy.style.display = 'none';
            }
        }
    }

    function batchOptions(item) {
        var batches = item.batches || [];
        if (!batches.length) {
            return '<option value="">No stock</option>';
        }
        return batches.map(function (batch) {
            var selected = String(batch.id) === String(item.batch_id) ? ' selected' : '';
            return '<option value="' + batch.id + '"' + selected +
                ' data-price="' + batch.unit_sale_price + '" data-mrp="' + batch.unit_mrp + '" data-tax="' + batch.tax_percent + '" data-available="' + batch.available_qty + '">' +
                escapeHtml(batch.batch_no || '-') + ' | Exp ' + escapeHtml(batch.expiry_date || 'NA') + ' | ' + batch.available_qty +
                '</option>';
        }).join('');
    }

    function stockBadge(status) {
        if (status === 'out') { return '<span class="badge badge-red">Out</span>'; }
        if (status === 'partial') { return '<span class="badge badge-orange">Partial</span>'; }
        return '<span class="badge badge-green">Available</span>';
    }

    function renderDispenseItems() {
        var body = document.getElementById('dispenseItemsBody');
        if (!body) { return; }

        if (!dispenseState.items.length) {
            body.innerHTML = '<tr><td colspan="10" class="text-muted text-center">Select a prescription or add medicine for walk-in dispense.</td></tr>';
            return;
        }

        body.innerHTML = dispenseState.items.map(function (item, idx) {
            var detail = [item.dosage, item.frequency, item.route, item.instruction].filter(function (v) { return v && v !== '-'; }).join(' / ') || '-';
            var prescribed = item.prescribed_qty || 0;
            return '<tr data-index="' + idx + '">' +
                '<td><input type="hidden" class="disp-medicine-id" value="' + item.medicine_id + '"><div class="fw-700 fs-12">' + escapeHtml(item.medicine_name || '-') + '</div><div class="text-muted fs-10">' + escapeHtml(item.unit || '') + '</div></td>' +
                '<td class="fs-11">' + escapeHtml(detail) + '</td>' +
                '<td class="fw-700">' + prescribed + '</td>' +
                '<td>' + stockBadge(item.stock_status) + '<div class="fs-10 text-muted">' + (item.available_qty || 0) + '</div></td>' +
                '<td><select class="form-control ph-grid-input disp-batch">' + batchOptions(item) + '</select></td>' +
                '<td><input type="number" min="0" step="0.01" class="form-control ph-grid-input ph-grid-input-qty disp-qty" value="' + (item.dispense_qty || 0) + '"></td>' +
                '<td><input type="number" min="0" step="0.01" class="form-control ph-grid-input ph-grid-input-price disp-price" value="' + (item.unit_price || 0) + '"></td>' +
                '<td><input type="number" min="0" max="100" step="0.01" class="form-control ph-grid-input disp-tax" value="' + (item.tax_percent || 0) + '"></td>' +
                '<td><span class="disp-line-total fw-700">' + formatCurrency(lineTotal(item)) + '</span></td>' +
                '<td><button class="btn btn-danger btn-xs" type="button" onclick="removeDispenseItem(' + idx + ')">✕</button></td>' +
                '</tr>';
        }).join('');
        recalcDispenseTotals();
    }

    function lineTotal(item) {
        var qty = toNumber(item.dispense_qty);
        var price = toNumber(item.unit_price);
        var tax = toNumber(item.tax_percent);
        var subtotal = qty * price;
        return subtotal + (subtotal * tax / 100);
    }

    function syncDispenseRowsToState() {
        var body = document.getElementById('dispenseItemsBody');
        if (!body) { return; }
        body.querySelectorAll('tr[data-index]').forEach(function (row) {
            var idx = parseInt(row.dataset.index, 10);
            var item = dispenseState.items[idx];
            if (!item) { return; }
            var batchSel = row.querySelector('.disp-batch');
            var selected = batchSel ? batchSel.options[batchSel.selectedIndex] : null;
            if (selected && selected.value) {
                item.batch_id = selected.value;
                item.unit_price = toNumber(selected.dataset.price);
                item.unit_mrp = toNumber(selected.dataset.mrp);
                item.tax_percent = toNumber(selected.dataset.tax);
                item.available_qty = toNumber(selected.dataset.available);
                row.querySelector('.disp-price').value = item.unit_price;
                row.querySelector('.disp-tax').value = item.tax_percent;
            }
            item.dispense_qty = toNumber(row.querySelector('.disp-qty') && row.querySelector('.disp-qty').value);
            item.unit_price = toNumber(row.querySelector('.disp-price') && row.querySelector('.disp-price').value);
            item.tax_percent = toNumber(row.querySelector('.disp-tax') && row.querySelector('.disp-tax').value);
            var line = row.querySelector('.disp-line-total');
            if (line) { line.textContent = formatCurrency(lineTotal(item)); }
        });
    }

    function recalcDispenseTotals() {
        syncDispenseRowsToState();
        var subtotal = 0;
        var taxTotal = 0;

        dispenseState.items.forEach(function (item) {
            var qty = toNumber(item.dispense_qty);
            var price = toNumber(item.unit_price);
            var tax = toNumber(item.tax_percent);
            var lineSubtotal = qty * price;
            subtotal += lineSubtotal;
            taxTotal += lineSubtotal * tax / 100;
        });

        var discount = toNumber(document.getElementById('dispenseDiscount') && document.getElementById('dispenseDiscount').value);
        var paid = toNumber(document.getElementById('dispensePaid') && document.getElementById('dispensePaid').value);
        var net = Math.max(0, subtotal + taxTotal - discount);
        setText('dispenseSubtotal', formatCurrency(subtotal));
        setText('dispenseTax', formatCurrency(taxTotal));
        setText('dispenseNet', formatCurrency(net));
        setText('dispenseDue', formatCurrency(Math.max(0, net - paid)));
    }

    function loadWalkInMedicineOptions() {
        var url = getDispenseMedicineSearchUrl();
        var search = document.getElementById('walkInMedicineSearch');
        var list = document.getElementById('walkInMedicineOptions');
        if (!url || !search || !list) { return; }
        var q = String(search.value || '').trim();
        if (q.length < 2) {
            list.innerHTML = '';
            return;
        }
        fetch(url + '?q=' + encodeURIComponent(q), {
            method: 'GET',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function (res) { return res.ok ? res.json() : Promise.reject(res); })
            .then(function (data) {
                dispenseState.medicineSearchResults = data.items || [];
                list.innerHTML = dispenseState.medicineSearchResults.map(function (m) {
                    return '<option value="' + escapeHtml(m.name) + '" label="' + escapeHtml(m.name + ' | Stock ' + m.available_qty) + '"></option>';
                }).join('');
            })
            .catch(function () {});
    }

    // Old static updateGRNTotal removed — replaced by updateGRNAcceptedTotal

    window.filterDispenseQueue = function (q) {
        var searchEl = document.getElementById('dispenseSearch');
        if (searchEl && typeof q === 'string' && searchEl.value !== q) {
            searchEl.value = q;
        }
        if (dispenseTable) {
            dispenseTable.ajax.reload();
        }
    };

    window.openWalkInDispense = function () {
        resetDispenseState('walkin');
        window.openModal('dispenseModal');
    };

    window.openPrescriptionDispense = function (type, id) {
        resetDispenseState('prescription');
        window.openModal('dispenseModal');

        var url = getDispensePreviewUrl();
        if (!url || !type || !id) {
            toast('Error', 'Prescription preview route is not available.', 'error');
            return;
        }

        if (typeof window.loader === 'function') { window.loader(); }
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrfToken() || ''
            },
            body: JSON.stringify({ prescription_type: type, prescription_id: id })
        })
            .then(function (res) { return res.ok ? res.json() : res.json().then(function (data) { return Promise.reject(data); }); })
            .then(function (data) {
                if (typeof window.loader === 'function') { window.loader('hide'); }
                dispenseState.mode = 'prescription';
                dispenseState.patient = data.patient;
                dispenseState.prescription = data.prescription;
                dispenseState.items = data.items || [];
                setValue('dispensePrescriptionType', data.prescription && data.prescription.type);
                setValue('dispensePrescriptionId', data.prescription && data.prescription.id);
                setValue('dispensePatientId', data.patient && data.patient.id);
                renderDispensePatient(data);
                renderDispenseItems();
            })
            .catch(function (err) {
                if (typeof window.loader === 'function') { window.loader('hide'); }
                toast('Error', err.message || 'Unable to load prescription.', 'error');
            });
    };

    window.addWalkInMedicine = function () {
        var input = document.getElementById('walkInMedicineSearch');
        if (!input) { return; }
        var value = String(input.value || '').trim().toLowerCase();
        var item = dispenseState.medicineSearchResults.find(function (m) {
            return String(m.name || '').trim().toLowerCase() === value;
        });
        if (!item) {
            toast('Select Medicine', 'Please choose a medicine from search suggestions.', 'warning');
            return;
        }
        dispenseState.items.push({
            medicine_id: item.id,
            medicine_name: item.name,
            unit: item.unit,
            dosage: '-',
            frequency: '-',
            route: '-',
            instruction: '-',
            prescribed_qty: 0,
            available_qty: item.available_qty,
            dispense_qty: item.available_qty > 0 ? 1 : 0,
            stock_status: item.available_qty <= 0 ? 'out' : 'available',
            batch_id: item.batch_id,
            unit_price: item.unit_price,
            unit_mrp: item.unit_mrp,
            tax_percent: item.tax_percent,
            batches: item.batches || []
        });
        input.value = '';
        renderDispenseItems();
    };

    window.removeDispenseItem = function (idx) {
        dispenseState.items.splice(idx, 1);
        renderDispenseItems();
    };

    if (typeof window.$ !== 'undefined') {
        window.$(document).on('input change', '#dispenseItemsBody .disp-qty, #dispenseItemsBody .disp-price, #dispenseItemsBody .disp-tax, #dispenseItemsBody .disp-batch', recalcDispenseTotals);
    }

    window.holdRx = function (btn) {
        var row = btn.closest('tr');
        var status = row ? row.querySelector('td:nth-child(8) .badge') : null;
        if (status) {
            status.textContent = 'on hold';
            status.className = 'badge badge-gray';
        }
        toast('On Hold', 'Prescription placed on hold', 'warning');
    };

    window.refreshQueue = function () {
        if (dispenseTable) {
            dispenseTable.ajax.reload(function () {
                loadDashboardCounts();
            });
        } else {
            loadDispenseQueue();
            loadDashboardCounts();
        }
        toast('Refreshed', 'Queue refreshed', 'success', 2000);
    };

    window.refreshStatOrders = function () {
        loadStatOrders();
        loadDashboardCounts();
        statOrdersLoaded = true;
        toast('Refreshed', 'STAT orders refreshed', 'success', 2000);
    };

    window.processSTAT = function () {
        toast('STAT Raised', 'STAT order raised - Pharmacist notified', 'error');
        window.closeModal('statOrderModal');
    };

    window.dispenseSTAT = function (btn, rx) {
        var card = btn.closest('div[style]');
        if (card) {
            card.style.opacity = '0.5';
        }
        toast('STAT Dispensed', rx + ' - Dispensed. Nurse notified.', 'success');
    };

    window.approveRx = function (btn) {
        var card = btn.closest('div[style]');
        if (card) {
            card.style.opacity = '0.5';
        }
        toast('Approved', 'Prescription approved for dispensing', 'success');
    };

    window.rejectRx = function (btn) {
        var card = btn.closest('div[style]');
        if (card) {
            card.style.opacity = '0.5';
        }
        toast('Rejected', 'Prescription rejected. Doctor notified.', 'error');
    };

    window.filterDrugs = function () {
        if (inventoryTable) {
            inventoryTable.ajax.reload();
        }
    };

    window.expiryQuarantine = function (id, btn) {
        var message = 'This will hold the batch stock and mark it as quarantined.';

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Quarantine This Medicine Batch ?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Quarantine it',
                cancelButtonText: 'Cancel'
            }).then(function (result) {
                if (result.isConfirmed) {
                    doExpiryQuarantine(id, btn);
                }
            });
            return;
        }

        if (!confirm(message)) {
            return;
        }

        doExpiryQuarantine(id, btn);
    };

    function doExpiryQuarantine(id, btn) {
        var url = getExpiryQuarantineUrl(id);
        if (!url || typeof window.$ === 'undefined') {
            toast('Error', 'Quarantine route not available.', 'error');
            if (btn) { btn.disabled = false; }
            return;
        }

        if (btn) {
            btn.disabled = true;
            btn.textContent = 'Quarantining...';
        }
        if (typeof window.loader === 'function') { window.loader(); }

        window.$.ajax({
            url: url,
            type: 'POST',
            data: { _token: getCsrfToken() },
            success: function (response) {
                if (typeof window.loader === 'function') { window.loader('hide'); }
                toast('Quarantined', response.message || 'Batch stock is now on hold.', 'warning');
                if (btn) {
                    btn.textContent = 'Quarantined';
                }
                if (expiryTable) { expiryTable.ajax.reload(null, false); }
                loadDashboardCounts();
            },
            error: function () {
                if (typeof window.loader === 'function') { window.loader('hide'); }
                if (btn) {
                    btn.disabled = false;
                    btn.textContent = '🔒 Quarantine';
                }
                toast('Error', 'Failed to quarantine batch stock.', 'error');
            }
        });
    }

    window.expiryReturn = function (id, btn) {
        var message = 'Return batch #' + id + '? This will initiate the return process for the batch.';

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Return This medicine batch?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, return it',
                cancelButtonText: 'Cancel'
            }).then(function (result) {
                if (result.isConfirmed) {
                    doExpiryReturn(id, btn);
                }
            });
            return;
        }

        if (!confirm(message)) {
            return;
        }

        doExpiryReturn(id, btn);
    };

    function doExpiryReturn(id, btn) {
        if (btn) { btn.disabled = true; }
        toast('Return', 'Return process initiated for batch #' + id + '.', 'info');
    };

    window.processExpiredBatches = function () {
        var url = getExpiryProcessUrl();
        if (!url || typeof window.$ === 'undefined') {
            toast('Error', 'Process route not available.', 'error');
            return;
        }

        var message = 'Expired stock will be deducted from current inventory. Do you want to proceed?';
        var proceed = false;

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Process Expired Stock?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, process it',
                cancelButtonText: 'No, cancel'
            }).then(function (result) {
                if (result.isConfirmed) {
                    doProcessExpiredBatches(url);
                }
            });
            return;
        }

        proceed = confirm(message);
        if (!proceed) {
            return;
        }

        doProcessExpiredBatches(url);
    };

    function doProcessExpiredBatches(url) {
        if (typeof window.loader === 'function') { window.loader(); }

        window.$.ajax({
            url: url,
            type: 'POST',
            data: { _token: getCsrfToken() },
            success: function (response) {
                if (typeof window.loader === 'function') { window.loader('hide'); }
                toast('Done', response.message || 'Expired batches processed.', 'success');
                if (expiryTable) { expiryTable.ajax.reload(null, false); }
                if (inventoryTable) { inventoryTable.ajax.reload(null, false); }
                loadDashboardCounts();
            },
            error: function () {
                if (typeof window.loader === 'function') { window.loader('hide'); }
                toast('Error', 'Failed to process expired batches.', 'error');
            }
        });
    };

    function getPurchaseApproveUrl(id) {
        if (typeof window.route === 'function') {
            try { return window.route('purchaseApprove').replace('__ID__', String(id)); } catch (e) { return ''; }
        }
        return '';
    }

    function getPurchaseRejectUrl(id) {
        if (typeof window.route === 'function') {
            try { return window.route('purchaseReject').replace('__ID__', String(id)); } catch (e) { return ''; }
        }
        return '';
    }

    function getPurchaseStoreUrl() {
        if (typeof window.route === 'function') {
            try { return window.route('purchaseStore'); } catch (e) { return ''; }
        }
        return '';
    }

    function openPOView(id) {
        var url = getPurchaseShowformUrl();
        if (!url || !id) {
            toast('Error', 'PO view route not available.', 'error');
            return;
        }

        if (typeof window.loader === 'function') { window.loader(); }

        window.$.post(url, { _token: getCsrfToken(), id: id, view: 1 }, function (response) {
            if (typeof window.loader === 'function') { window.loader('hide'); }
            window.$('#ajaxdata').html(response);
            window.$('.add-datamodal').modal('show');
            window.$('.add-datamodal .modal-dialog')
                .removeClass('modal-sm modal-lg modal-xl')
                .addClass('modal-fullscreen');
        }).fail(function () {
            if (typeof window.loader === 'function') { window.loader('hide'); }
            toast('Error', 'Unable to load purchase order details.', 'error');
        });
    }

    function openGRNView(id) {
        var url = getGrnViewUrl(id);
        if (!url || !id) {
            toast('Error', 'GRN view route not available.', 'error');
            return;
        }

        if (typeof window.loader === 'function') { window.loader(); }

        window.$.post(url, { _token: getCsrfToken() }, function (response) {
            if (typeof window.loader === 'function') { window.loader('hide'); }
            window.$('#ajaxdata').html(response);
            window.$('.add-datamodal').modal('show');
            window.$('.add-datamodal .modal-dialog')
                .removeClass('modal-sm modal-lg modal-xl')
                .addClass('modal-fullscreen');
        }).fail(function () {
            if (typeof window.loader === 'function') { window.loader('hide'); }
            toast('Error', 'Unable to load GRN details.', 'error');
        });
    }

    /* ─── New PO inline form logic ─── */
    var poRowIdx = 0;
    function poMedicineOptions() {
        return (window.poMedicines || [])
            .map(function (m) { return '<option value="' + m.id + '">' + escapeHtml(m.name) + (m.unit ? ' [' + m.unit + ']' : '') + '</option>'; })
            .join('');
    }

    function buildPORow() {
        var i = poRowIdx++;
        return '<tr data-idx="' + i + '">' +
            '<td><select class="form-control select2 ph-grid-input po-medicine" name="items[' + i + '][medicine_id]"><option value="">Select</option>' + poMedicineOptions() + '</select></td>' +
            '<td><input type="number" min="1" class="form-control ph-grid-input ph-grid-input-qty po-qty" name="items[' + i + '][quantity_purchased]" value="1" placeholder="1"></td>' +
            '<td><input type="number" step="0.01" min="0" class="form-control ph-grid-input ph-grid-input-price po-price" name="items[' + i + '][unit_purchase_price]" value="0" placeholder="0"></td>' +
            '<td><span class="po-line-amt fw-700 fs-12">₹0</span></td>' +
            '<td><button class="btn btn-danger btn-xs" type="button" onclick="this.closest(\'tr\').remove(); recalcPOTotal();">✕</button></td></tr>';
    }

    function initPOMedicineSelect(el) {
        if (typeof window.$ === 'undefined' || !window.$.fn || !window.$.fn.select2) {
            return;
        }
        var $select = window.$(el).find('select.po-medicine.select2');
        if ($select.length) {
            $select.each(function () {
                if (!window.$(this).data('select2')) {
                    window.$(this).select2({
                        placeholder: 'Select medicine',
                        allowClear: true,
                        dropdownParent: window.$(this).closest('.modal').length ? window.$(this).closest('.modal') : window.$('body')
                    });
                }
            });
        }
    }

    function recalcPOTotal() {
        var total = 0;
        window.$('#poItemBody tr').each(function () {
            var qty = parseFloat(window.$(this).find('.po-qty').val()) || 0;
            var price = parseFloat(window.$(this).find('.po-price').val()) || 0;
            var lineAmt = qty * price;
            window.$(this).find('.po-line-amt').text('₹' + lineAmt.toFixed(2));
            total += lineAmt;
        });
        window.$('#poTotal').text('₹' + total.toFixed(2));
    }

    window.recalcPOTotal = recalcPOTotal;

    if (typeof window.$ !== 'undefined') {
        window.$(document).on('click', '#addPOItemRow', function () {
            window.$('#poItemBody').append(buildPORow());
            initPOMedicineSelect(window.$('#poItemBody tr:last'));
            recalcPOTotal();
        });

        window.$(document).on('input', '.po-qty, .po-price', recalcPOTotal);

        window.$(function () {
            initPOMedicineSelect(window.$('#poItemBody'));
        });

        window.$(document).on('click', '#submitNewPO', function () {
            var storeUrl = getPurchaseStoreUrl();
            if (!storeUrl) { toast('Error', 'Store route not available.', 'error'); return; }

            if (typeof window.loader === 'function') { window.loader(); }

            var fd = new FormData(document.getElementById('newPOForm'));
            fd.append('_token', getCsrfToken());


            window.$.ajax({
                url: storeUrl, type: 'POST', data: fd, contentType: false, processData: false,
                success: function (response) {
                    if (typeof window.loader === 'function') { window.loader('hide'); }
                    if (response.status) {
                        toast('PO Created', response.message + (response.bill_no ? ' (' + response.bill_no + ')' : ''), 'success');
                        window.closeModal('newPOModal');
                        document.getElementById('newPOForm').reset();
                        window.$('#poItemBody').html('');
                        poRowIdx = 0;
                        recalcPOTotal();
                        if (poTable) { poTable.ajax.reload(null, false); }
                    } else {
                        toast('Error', response.message || 'Failed to create PO.', 'error');
                    }
                },
                error: function (xhr) {
                    if (typeof window.loader === 'function') { window.loader('hide'); }
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        var msgs = xhr.responseJSON.errors.map(function (e) { return e.message; }).join('\n');
                        toast('Validation Error', msgs, 'error');
                    } else {
                        toast('Error', 'Unable to create purchase order.', 'error');
                    }
                }
            });
        });

        window.$(document).on('click', '.po-view-btn', function () {
            openPOView(this.getAttribute('data-id'));
        });

        /* ─── PO Approve / Reject ─── */
        window.$(document).on('click', '.po-approve-btn', function () {
            var id = this.getAttribute('data-id');
            var url = getPurchaseApproveUrl(id);
            if (!url) { toast('Error', 'Route not found.', 'error'); return; }
            var btn = this;
            if (typeof Swal !== 'undefined') {
                Swal.fire({ title: 'Approve PO?', text: 'This will add stock to inventory.', icon: 'question', showCancelButton: true, confirmButtonText: 'Yes, approve' })
                    .then(function (r) { if (r.isConfirmed) { doApprove(url, btn); } });
            } else if (confirm('Approve this PO? Stock will be added to inventory.')) {
                doApprove(url, btn);
            }
        });

        function doApprove(url, btn) {
            if (typeof window.loader === 'function') { window.loader(); }
            window.$.ajax({
                url: url, type: 'POST', data: { _token: getCsrfToken() },
                success: function (res) {
                    if (typeof window.loader === 'function') { window.loader('hide'); }
                    toast('Approved', res.message || 'PO approved.', 'success');
                    if (poTable) { poTable.ajax.reload(null, false); }
                },
                error: function (xhr) {
                    if (typeof window.loader === 'function') { window.loader('hide'); }
                    toast('Error', (xhr.responseJSON && xhr.responseJSON.message) || 'Approval failed.', 'error');
                }
            });
        }

        window.$(document).on('click', '.po-reject-btn', function () {
            var id = this.getAttribute('data-id');
            var url = getPurchaseRejectUrl(id);
            if (!url) { toast('Error', 'Route not found.', 'error'); return; }

            function submitReject(reason) {
                if (typeof window.loader === 'function') { window.loader(); }
                window.$.ajax({
                    url: url, type: 'POST', data: { _token: getCsrfToken(), reject_reason: reason || '' },
                    success: function (res) {
                        if (typeof window.loader === 'function') { window.loader('hide'); }
                        toast('Rejected', res.message || 'PO rejected.', 'info');
                        if (poTable) { poTable.ajax.reload(null, false); }
                    },
                    error: function (xhr) {
                        if (typeof window.loader === 'function') { window.loader('hide'); }
                        toast('Error', (xhr.responseJSON && xhr.responseJSON.message) || 'Rejection failed.', 'error');
                    }
                });
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Reject PO',
                    input: 'textarea',
                    inputLabel: 'Rejection reason (optional)',
                    inputPlaceholder: 'Enter rejection reason...',
                    inputAttributes: {
                        'aria-label': 'Rejection reason',
                        maxlength: 500,
                        rows: 4
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Reject PO',
                    cancelButtonText: 'Cancel',
                    preConfirm: function (value) {
                        return value || '';
                    }
                }).then(function (result) {
                    if (result.isConfirmed) {
                        submitReject(result.value);
                    }
                });
            } else {
                var reason = prompt('Rejection reason (optional):') || '';
                submitReject(reason);
            }
        });

        window.$(document).on('click', '.po-print-btn', function () {
            var url = this.getAttribute('data-url');
            if (url) {
                window.open(url, '_blank');
            }
        });

        window.$(document).on('click', '.grn-view-btn', function () {
            openGRNView(this.getAttribute('data-id'));
        });

        window.$(document).on('click', '.grn-print-btn', function () {
            var url = this.getAttribute('data-url');
            if (url) {
                window.open(url, '_blank');
            }
        });

        /* ─── PO table → Create GRN button ─── */
        window.$(document).on('click', '.po-create-grn-btn', function () {
            var poId = this.getAttribute('data-id');
            window.openModal('grnModal');
            loadApprovedPOs(function () {
                window.$('#grn_po_select').val(poId).trigger('change');
            });
        });

        /* ─── GRN: PO select change → populate items ─── */
        window.$(document).on('change', '#grn_po_select', function () {
            renderGrnItems(this.value);
        });

        window.$(document).on('input', '.grn-received, .grn-rejected, .grn-price, .grn-tax', updateGRNAcceptedTotal);

        /* ─── GRN: Submit ─── */
        window.$(document).on('click', '#submitGRNBtn', function () {
            var storeUrl = getGrnStoreUrl();
            if (!storeUrl) { toast('Error', 'GRN store route not available.', 'error'); return; }

            var poId = window.$('#grn_po_select').val();
            if (!poId) { toast('Error', 'Please select an approved PO first.', 'error'); return; }

            var doSubmit = function() {
                clearGrnFormErrors();
                if (typeof window.loader === 'function') { window.loader(); }
                var fd = new FormData(document.getElementById('grnForm'));
                fd.append('_token', getCsrfToken());

                // Normalise month expiry fields to full date
                document.querySelectorAll('#grnForm .grn-expiry').forEach(function (el) {
                    if (el.value && el.value.length === 7) {
                        var parts = el.value.split('-');
                        var year = parseInt(parts[0], 10);
                        var month = parseInt(parts[1], 10);
                        if (!isNaN(year) && !isNaN(month)) {
                            var lastDay = new Date(year, month, 0).getDate();
                            fd.set(el.name, el.value + '-' + String(lastDay).padStart(2, '0'));
                        } else {
                            fd.set(el.name, el.value);
                        }
                    }
                });

                window.$.ajax({
                    url: storeUrl, type: 'POST', data: fd, contentType: false, processData: false,
                    success: function (response) {
                        if (typeof window.loader === 'function') { window.loader('hide'); }
                        if (response.status) {
                            toast('GRN Created', response.message + (response.grn_no ? ' (' + response.grn_no + ')' : ''), 'success');
                            window.closeModal('grnModal');
                            document.getElementById('grnForm').reset();
                            window.$('#grnItemBody').html('');
                            document.getElementById('grnItemsWrap').style.display = 'none';
                            document.getElementById('grnNoPoAlert').style.display = '';
                            document.getElementById('grnSubtotal') && (document.getElementById('grnSubtotal').textContent = '₹0.00');
                            document.getElementById('grnTaxTotal') && (document.getElementById('grnTaxTotal').textContent = '₹0.00');
                            window.$('#grnTotal').text('₹0.00');
                            if (grnLogTable) { grnLogTable.ajax.reload(null, false); }
                            if (poTable) { poTable.ajax.reload(null, false); }
                            if (inventoryTable) { inventoryTable.ajax.reload(null, false); }
                            loadDashboardCounts();
                            loadApprovedPOs();
                        } else {
                            toast('Error', response.message || 'Failed to create GRN.', 'error');
                        }
                    },
                    error: function (xhr) {
                        if (typeof window.loader === 'function') { window.loader('hide'); }
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Unable to create GRN.';
                        clearGrnFormErrors();
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            xhr.responseJSON.errors.forEach(function (error) {
                                addGrnFieldError(error.code || error.field || error.name || '', error.message || error);
                            });
                            msg = 'Validation failed. Please correct the highlighted errors.';
                        }
                        toast('Error', msg, 'error');
                    }
                });
            };

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Submit GRN?',
                    text: 'Are you sure you want to submit this GRN and inward the stock?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Submit',
                    cancelButtonText: 'Cancel'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        doSubmit();
                    }
                });
            } else if (confirm('Are you sure you want to submit this GRN and inward the stock?')) {
                doSubmit();
            }
        });

        window.$(document).on('click', '.bad-stock-btn', function () {
            var rowId = window.$(this).data('id');
            var showFormUrl = getShowBadStockFormUrl();
            if (!showFormUrl || !rowId) {
                return;
            }

            if (typeof window.loader === 'function') {
                window.loader();
            }

            window.$.post(showFormUrl, { _token: getCsrfToken(), id: rowId }, function (response) {
                if (typeof window.loader === 'function') {
                    window.loader('hide');
                }
                window.$('#ajaxdata').html(response);
                window.$('.add-datamodal').modal('show');
                window.$('.add-datamodal .modal-dialog').removeClass('modal-xl');
            }).fail(function () {
                if (typeof window.loader === 'function') {
                    window.loader('hide');
                }
                toast('Error', 'Unable to load form.', 'error');
            });
        });

        window.$(document).on('submit', '#bad-stock-form', function (e) {
            e.preventDefault();

            var adjustUrl = getAdjustBadStockUrl();
            if (!adjustUrl) {
                return;
            }

            if (typeof window.loader === 'function') {
                window.loader();
            }

            var fd = new FormData(this);
            fd.append('_token', getCsrfToken());

            window.$.ajax({
                url: adjustUrl,
                type: 'POST',
                data: fd,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (typeof window.loader === 'function') {
                        window.loader('hide');
                    }
                    window.$('.add-datamodal').modal('hide');
                    if (inventoryTable) {
                        inventoryTable.ajax.reload(null, false);
                    }
                    loadDashboardCounts();
                    toast('Success', response.message || 'Bad stock adjusted successfully.', 'success');
                },
                error: function (xhr) {
                    if (typeof window.loader === 'function') {
                        window.loader('hide');
                    }
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        var messages = xhr.responseJSON.errors.map(function (err) {
                            return err.message;
                        }).join('\n');
                        toast('Error', messages, 'error');
                    } else {
                        toast('Error', 'Unable to adjust bad stock.', 'error');
                    }
                }
            });
        });
        
        window.$(document).on('click', '.bad-quarantine-stock-btn', function () {
            var rowId = window.$(this).data('id');
            var showFormUrl = getShowBadQuarantineStockFormUrl();
            if (!showFormUrl || !rowId) {
                return;
            }

            if (typeof window.loader === 'function') {
                window.loader();
            }

            window.$.post(showFormUrl, { _token: getCsrfToken(), id: rowId }, function (response) {
                if (typeof window.loader === 'function') {
                    window.loader('hide');
                }
                window.$('#ajaxdata').html(response);
                window.$('.add-datamodal').modal('show');
                window.$('.add-datamodal .modal-dialog').removeClass('modal-xl');
            }).fail(function () {
                if (typeof window.loader === 'function') {
                    window.loader('hide');
                }
                toast('Error', 'Unable to load form.', 'error');
            });
        });

        window.$(document).on('submit', '#bad-quarantine-stock-form', function (e) {
            e.preventDefault();

            var adjustUrl = getAdjustBadQuarantineStockUrl();
            if (!adjustUrl) {
                return;
            }

            if (typeof window.loader === 'function') {
                window.loader();
            }

            var fd = new FormData(this);
            fd.append('_token', getCsrfToken());

            window.$.ajax({
                url: adjustUrl,
                type: 'POST',
                data: fd,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (typeof window.loader === 'function') {
                        window.loader('hide');
                    }
                    window.$('.add-datamodal').modal('hide');
                    if (quarantineInventoryTable) {
                        quarantineInventoryTable.ajax.reload(null, false);
                    }
                    loadDashboardCounts();
                    toast('Success', response.message || 'Bad quarantine stock adjusted successfully.', 'success');
                },
                error: function (xhr) {
                    if (typeof window.loader === 'function') {
                        window.loader('hide');
                    }
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        var messages = xhr.responseJSON.errors.map(function (err) {
                            return err.message;
                        }).join('\n');
                        toast('Error', messages, 'error');
                    } else {
                        toast('Error', 'Unable to adjust bad quarantine stock.', 'error');
                    }
                }
            });
        });
    }

    window.exportDrugInventory = function () {
        var exportUrl = getStockExportUrl();
        if (!exportUrl) {
            toast('Export', 'Export route is not available.', 'error');
            return;
        }

        var filters = currentInventoryFilters();
        var params = [];

        if (filters.search) {
            params.push('search=' + encodeURIComponent(filters.search));
        }
        if (filters.category_id) {
            params.push('category_id=' + encodeURIComponent(filters.category_id));
        }
        if (filters.stock_filter && filters.stock_filter !== 'all') {
            params.push('stock_filter=' + encodeURIComponent(filters.stock_filter));
        }
        if (filters.expiry_filter && filters.expiry_filter !== 'all') {
            params.push('expiry_filter=' + encodeURIComponent(filters.expiry_filter));
        }

        var finalUrl = exportUrl + (params.length ? ('?' + params.join('&')) : '');
        window.location.href = finalUrl;
    };
    
    window.exportQuarantineDrugInventory = function () {
        var exportUrl = getQuarantineStockExportUrl();
        if (!exportUrl) {
            toast('Export', 'Export route is not available.', 'error');
            return;
        }

        var filters = currentQuarantineInventoryFilters();
        var params = [];

        if (filters.search) {
            params.push('search=' + encodeURIComponent(filters.search));
        }
        if (filters.category_id) {
            params.push('category_id=' + encodeURIComponent(filters.category_id));
        }
        if (filters.stock_filter && filters.stock_filter !== 'all') {
            params.push('stock_filter=' + encodeURIComponent(filters.stock_filter));
        }
        if (filters.expiry_filter && filters.expiry_filter !== 'all') {
            params.push('expiry_filter=' + encodeURIComponent(filters.expiry_filter));
        }

        var finalUrl = exportUrl + (params.length ? ('?' + params.join('&')) : '');
        window.location.href = finalUrl;
    };

    window.switchPhTab = function (paneId, btn) {
        document.querySelectorAll('.tab-btn').forEach(function (b) {
            b.classList.remove('active');
        });
        if (btn) {
            btn.classList.add('active');
        }

        [
            'dispenseQueuePane',
            'statPane',
            'rxValidatePane',
            'inventoryPane',
            'quarantinePane',
            'expiryPane',
            'grnListPane',
            'poPane',
            'marPane'
        ].forEach(function (id) {
            var el = document.getElementById(id);
            if (el) {
                el.style.display = id === paneId ? 'block' : 'none';
            }
        });

        if (paneId === 'inventoryPane') {
            var wasInitialized = !!inventoryTable;
            loadDrugInventory();
            if (wasInitialized && inventoryTable) {
                inventoryTable.ajax.reload();
            }
        }
        
        if (paneId === 'quarantinePane') {
            var wasInitialized = !!quarantineInventoryTable;
            loadQuarantineInventory();
            if (wasInitialized && quarantineInventoryTable) {
                quarantineInventoryTable.ajax.reload();
            }
        }

        if (paneId === 'dispenseQueuePane') {
            var queueWasInit = !!dispenseTable;
            loadDispenseQueue();
            if (queueWasInit && dispenseTable) {
                dispenseTable.ajax.reload();
            }
        }

        if (paneId === 'expiryPane') {
            var expiryWasInit = !!expiryTable;
            loadExpiryAlerts();
            if (expiryWasInit && expiryTable) {
                expiryTable.ajax.reload();
            }
        }

        if (paneId === 'statPane') {
            if (!statOrdersLoaded) {
                loadStatOrders();
                statOrdersLoaded = true;
            }
        }

        if (paneId === 'grnListPane') {
            var grnWasInit = !!grnLogTable;
            loadGRNLog();
            if (grnWasInit && grnLogTable) {
                grnLogTable.ajax.reload();
            }
        }

        if (paneId === 'poPane') {
            var poWasInit = !!poTable;
            loadPOList();
            if (poWasInit && poTable) {
                poTable.ajax.reload();
            }
        }
    };

    window.confirmDispense = function () {
        var url = getDispenseStoreUrl();
        if (!url) {
            toast('Error', 'Dispense store route is not available.', 'error');
            return;
        }

        syncDispenseRowsToState();
        var items = dispenseState.items
            .filter(function (item) { return toNumber(item.dispense_qty) > 0; })
            .map(function (item) {
                return {
                    medicine_id: item.medicine_id,
                    stock_batch_id: item.batch_id || null,
                    quantity: toNumber(item.dispense_qty),
                    unit_price: toNumber(item.unit_price),
                    unit_mrp: toNumber(item.unit_mrp),
                    discount_percent: 0,
                    tax_percent: toNumber(item.tax_percent),
                    is_substituted: false,
                    substitution_note: ''
                };
            });

        if (!items.length) {
            toast('No Items', 'Please enter dispense quantity for at least one medicine.', 'warning');
            return;
        }

        var payload = {
            patient_id: document.getElementById('dispensePatientId') ? document.getElementById('dispensePatientId').value : '',
            prescription_type: document.getElementById('dispensePrescriptionType') ? document.getElementById('dispensePrescriptionType').value : '',
            prescription_id: document.getElementById('dispensePrescriptionId') ? document.getElementById('dispensePrescriptionId').value : '',
            notes: document.getElementById('dispenseNotes') ? document.getElementById('dispenseNotes').value : '',
            discount_amount: toNumber(document.getElementById('dispenseDiscount') && document.getElementById('dispenseDiscount').value),
            paid_amount: toNumber(document.getElementById('dispensePaid') && document.getElementById('dispensePaid').value),
            items: items
        };

        if (typeof window.loader === 'function') { window.loader(); }
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrfToken() || ''
            },
            body: JSON.stringify(payload)
        })
            .then(function (res) { return res.ok ? res.json() : res.json().then(function (data) { return Promise.reject(data); }); })
            .then(function (data) {
                if (typeof window.loader === 'function') { window.loader('hide'); }
                toast('Dispensed', (data.bill_no || 'Bill') + ' created successfully.', 'success');
                window.closeModal('dispenseModal');
                if (dispenseTable) { dispenseTable.ajax.reload(null, false); }
                if (inventoryTable) { inventoryTable.ajax.reload(null, false); }
                loadDashboardCounts();

                // Handle printing trigger
                var autoPrint = document.getElementById('dispenseAutoPrint') && document.getElementById('dispenseAutoPrint').checked;
                if (autoPrint && data.print_url) {
                    window.open(data.print_url, '_blank');
                }
            })
            .catch(function (err) {
                if (typeof window.loader === 'function') { window.loader('hide'); }
                var msg = err.message || 'Unable to dispense medicine.';
                if (err.errors && err.errors.length) {
                    msg = err.errors.map(function (e) { return e.message || e; }).join('\n');
                }
                toast('Error', msg, 'error');
            });
    };

    window.holdDispense = function () {
        toast('On Hold', 'Prescription retained in queue. No stock changed.', 'warning');
        window.closeModal('dispenseModal');
    };

    /* ─── GRN: Dynamic PO-linked flow ─── */
    var grnApprovedPOsCache = [];

    function loadApprovedPOs(callback) {
        var url = getGrnApprovedPOsUrl();
        if (!url) {
            if (typeof callback === 'function') {
                callback();
            }
            return;
        }
        window.$.getJSON(url, function (data) {
            grnApprovedPOsCache = data || [];
            var sel = document.getElementById('grn_po_select');
            if (!sel) {
                if (typeof callback === 'function') {
                    callback();
                }
                return;
            }
            sel.innerHTML = '<option value="">— Select Approved PO —</option>';
            grnApprovedPOsCache.forEach(function (po) {
                sel.innerHTML += '<option value="' + po.id + '">' + escapeHtml(po.bill_no) + ' — ' + escapeHtml(po.supplier) + ' (' + escapeHtml(po.date) + ')</option>';
            });
            if (typeof callback === 'function') {
                callback();
            }
        }).fail(function () {
            if (typeof callback === 'function') {
                callback();
            }
        });
    }

    function renderGrnItems(poId) {
        var body = document.getElementById('grnItemBody');
        var wrap = document.getElementById('grnItemsWrap');
        var alert = document.getElementById('grnNoPoAlert');
        if (!body) return;

        var po = grnApprovedPOsCache.find(function (p) { return p.id === parseInt(poId, 10); });
        if (!po || !po.items || po.items.length === 0) {
            body.innerHTML = '';
            if (wrap) wrap.style.display = 'none';
            if (alert) alert.style.display = '';
            updateGRNAcceptedTotal();
            return;
        }

        if (wrap) wrap.style.display = '';
        if (alert) alert.style.display = 'none';

        var supplierEl = document.getElementById('grn_supplier_display');
        if (supplierEl) supplierEl.value = po.supplier;

        var defaultPackSize = 1;
        var profitPercent = parseFloat(document.getElementById('grn_profit_percent') ? document.getElementById('grn_profit_percent').value : 30) || 0;
        var isSaleIsMrp = document.getElementById('grn_sale_is_mrp') ? document.getElementById('grn_sale_is_mrp').checked : false;

        body.innerHTML = po.items.map(function (item, idx) {
            var estPrice = item.unit_purchase_price || 0;
            var packPurPrice = estPrice * defaultPackSize;
            var packSalePrice = packPurPrice * (1 + profitPercent / 100);
            var packMrp = isSaleIsMrp ? packSalePrice : (packPurPrice * 1.5);
            var medicineVat = item.vat !== undefined ? item.vat : 18;

            return '<tr data-remaining="' + item.remaining_qty + '" data-est-price="' + estPrice + '">' +
                '<input type="hidden" name="items[' + idx + '][purchase_item_id]" value="' + item.purchase_item_id + '">' +
                '<input type="hidden" class="grn-received-packs-hidden" name="items[' + idx + '][quantity_received]" value="' + (item.remaining_qty / defaultPackSize) + '">' +
                '<input type="hidden" class="grn-free-packs-hidden" name="items[' + idx + '][quantity_free]" value="0">' +
                '<input type="hidden" class="grn-rejected-packs-hidden" name="items[' + idx + '][quantity_rejected]" value="0">' +
                '<td class="fw-700 fs-12" style="min-width:120px">' + escapeHtml(item.medicine_name) + '</td>' +
                '<td class="fw-700 text-primary">' + item.remaining_qty + '</td>' +
                '<td><input type="number" min="1" step="1" class="form-control ph-grid-input grn-pack-size" name="items[' + idx + '][pack_size]" value="' + defaultPackSize + '" style="width:60px"></td>' +
                '<td><input type="number" min="0" step="1" class="form-control ph-grid-input grn-received-qty" value="' + item.remaining_qty + '" style="width:70px"></td>' +
                '<td><input type="number" min="0" step="1" class="form-control ph-grid-input grn-free-qty" value="0" style="width:50px"></td>' +
                '<td><input type="number" min="0" step="1" class="form-control ph-grid-input grn-rejected-qty" value="0" style="width:50px"></td>' +
                '<td><input class="form-control ph-grid-input ph-grid-input-batch" name="items[' + idx + '][batch_no]" placeholder="Batch" required style="width:90px"></td>' +
                '<td><input type="month" class="form-control ph-grid-input grn-expiry" name="items[' + idx + '][expiry_date]" style="width:115px"></td>' +
                '<td><input type="number" step="0.01" min="0" class="form-control ph-grid-input grn-pack-price" name="items[' + idx + '][unit_purchase_price]" value="' + packPurPrice.toFixed(2) + '" style="width:80px"></td>' +
                '<td><input type="number" step="0.01" min="0" class="form-control ph-grid-input grn-pack-sale-price" name="items[' + idx + '][unit_sale_price]" value="' + packSalePrice.toFixed(2) + '" style="width:80px"></td>' +
                '<td><input type="number" step="0.01" min="0" class="form-control ph-grid-input grn-pack-mrp" name="items[' + idx + '][unit_mrp]" value="' + packMrp.toFixed(2) + '" style="width:80px"></td>' +
                '<td><input type="number" step="0.01" min="0" max="100" class="form-control ph-grid-input grn-tax" name="items[' + idx + '][tax_percent]" value="' + medicineVat + '" style="width:60px"></td>' +
                '<td><span class="grn-tax-amt fw-700 text-muted">₹0.00</span></td>' +
                '<td><span class="grn-line-total fw-700 text-muted">₹0.00</span></td>' +
                '<td><span class="grn-accepted fw-700 text-success">0</span></td>' +
                '<td><input class="form-control ph-grid-input" name="items[' + idx + '][rejection_reason]" placeholder="Reason" style="width:80px"></td>' +
                '</tr>';
        }).join('');

        updateGRNAcceptedTotal();
    }

    function updateGRNAcceptedTotal() {
        var subTotal = 0;
        var taxTotal = 0;
        var finalTotal = 0;

        window.$('#grnItemBody tr').each(function () {
            var $row = window.$(this);
            var remainingUnits = parseFloat(this.dataset.remaining) || 0;
            var packSize = parseInt($row.find('.grn-pack-size').val(), 10) || 1;
            if (packSize < 1) packSize = 1;

            var recdUnits = parseFloat($row.find('.grn-received-qty').val()) || 0;
            var freeUnits = parseFloat($row.find('.grn-free-qty').val()) || 0;
            var rejUnits = parseFloat($row.find('.grn-rejected-qty').val()) || 0;

            if (recdUnits > remainingUnits) {
                recdUnits = remainingUnits;
                $row.find('.grn-received-qty').val(recdUnits);
            }
            if (rejUnits > recdUnits) {
                rejUnits = recdUnits;
                $row.find('.grn-rejected-qty').val(rejUnits);
            }

            var acceptedUnits = Math.max(0, recdUnits - rejUnits);

            // Sync hidden inputs for backend submission (expects packs)
            $row.find('.grn-received-packs-hidden').val((recdUnits / packSize).toFixed(4));
            $row.find('.grn-free-packs-hidden').val((freeUnits / packSize).toFixed(4));
            $row.find('.grn-rejected-packs-hidden').val((rejUnits / packSize).toFixed(4));

            var packPrice = parseFloat($row.find('.grn-pack-price').val()) || 0;
            var taxPercent = parseFloat($row.find('.grn-tax').val()) || 0;
            var purTaxType = 'inclusive'; // Force purchase tax strictly inclusive

            var lineSub = 0;
            var lineTax = 0;
            var lineTot = 0;

            if (purTaxType === 'inclusive') {
                var totalUnitPur = packPrice / packSize;
                var taxableUnitPur = totalUnitPur / (1 + taxPercent / 100);
                var unitTax = totalUnitPur - taxableUnitPur;

                lineSub = acceptedUnits * taxableUnitPur;
                lineTax = acceptedUnits * unitTax;
                lineTot = acceptedUnits * totalUnitPur;
            } else {
                var taxableUnitPur = packPrice / packSize;
                var unitTax = taxableUnitPur * (taxPercent / 100);

                lineSub = acceptedUnits * taxableUnitPur;
                lineTax = acceptedUnits * unitTax;
                lineTot = lineSub + lineTax;
            }

            $row.find('.grn-accepted').text(acceptedUnits);
            $row.find('.grn-tax-amt').text('₹' + lineTax.toFixed(2));
            $row.find('.grn-line-total').text('₹' + lineTot.toFixed(2));

            subTotal += lineSub;
            taxTotal += lineTax;
            finalTotal += lineTot;
        });

        // Split taxes dynamically in UI based on GST type
        var gstType = window.$('#grn_gst_type').val() || 'local';
        var cgstTotal = 0;
        var sgstTotal = 0;
        var igstTotal = 0;

        if (gstType === 'interstate') {
            igstTotal = taxTotal;
            window.$('#grn_igst_row').show();
            window.$('#grn_cgst_row').hide();
            window.$('#grn_sgst_row').hide();
        } else {
            cgstTotal = taxTotal / 2;
            sgstTotal = taxTotal / 2;
            window.$('#grn_igst_row').hide();
            window.$('#grn_cgst_row').show();
            window.$('#grn_sgst_row').show();
        }

        var subtotalEl = document.getElementById('grnSubtotal');
        var taxEl = document.getElementById('grnTaxTotal');
        var totalEl = document.getElementById('grnTotal');
        var cgstEl = document.getElementById('grnCgstTotal');
        var sgstEl = document.getElementById('grnSgstTotal');
        var igstEl = document.getElementById('grnIgstTotal');

        if (subtotalEl) subtotalEl.textContent = '₹' + subTotal.toFixed(2);
        if (taxEl) taxEl.textContent = '₹' + taxTotal.toFixed(2);
        if (totalEl) totalEl.textContent = '₹' + finalTotal.toFixed(2);
        if (cgstEl) cgstEl.textContent = '₹' + cgstTotal.toFixed(2);
        if (sgstEl) sgstEl.textContent = '₹' + sgstTotal.toFixed(2);
        if (igstEl) igstEl.textContent = '₹' + igstTotal.toFixed(2);
    }

    window.updateGRNTotal = updateGRNAcceptedTotal;

    if (typeof window.$ !== 'undefined') {
        // Universal inputs change recalculation
        window.$(document).on('input change', '.grn-pack-size, .grn-received-qty, .grn-free-qty, .grn-rejected-qty, .grn-pack-price, .grn-pack-sale-price, .grn-pack-mrp, .grn-tax, #grn_gst_type', updateGRNAcceptedTotal);

        // Recalculate purchase pack price, sale pack price, and MRP on pack size change (received units stay same)
        window.$(document).on('change', '.grn-pack-size', function () {
            var $row = window.$(this).closest('tr');
            var packSize = parseInt(window.$(this).val(), 10) || 1;
            if (packSize < 1) packSize = 1;

            var estPrice = parseFloat($row.attr('data-est-price')) || 0;
            var packPurPrice = estPrice * packSize;
            $row.find('.grn-pack-price').val(packPurPrice.toFixed(2));

            var profitPercent = parseFloat(window.$('#grn_profit_percent').val()) || 0;
            var isSaleIsMrp = window.$('#grn_sale_is_mrp').is(':checked');

            var packSale = packPurPrice * (1 + profitPercent / 100);
            $row.find('.grn-pack-sale-price').val(packSale.toFixed(2));

            if (isSaleIsMrp) {
                $row.find('.grn-pack-mrp').val(packSale.toFixed(2));
            } else {
                $row.find('.grn-pack-mrp').val((packPurPrice * 1.5).toFixed(2));
            }

            updateGRNAcceptedTotal();
        });

        // Recalculate sale price and MRP on profit % or purchase price change
        window.$(document).on('input change', '.grn-pack-price, #grn_profit_percent', function () {
            var profitPercent = parseFloat(window.$('#grn_profit_percent').val()) || 0;
            var isSaleIsMrp = window.$('#grn_sale_is_mrp').is(':checked');

            window.$('#grnItemBody tr').each(function () {
                var $row = window.$(this);
                var packPrice = parseFloat($row.find('.grn-pack-price').val()) || 0;
                var packSale = packPrice * (1 + profitPercent / 100);

                $row.find('.grn-pack-sale-price').val(packSale.toFixed(2));
                if (isSaleIsMrp) {
                    $row.find('.grn-pack-mrp').val(packSale.toFixed(2));
                }
            });
            updateGRNAcceptedTotal();
        });

        // Toggle MRP mapping
        window.$(document).on('change', '#grn_sale_is_mrp', function () {
            var isSaleIsMrp = window.$(this).is(':checked');
            if (isSaleIsMrp) {
                window.$('#grnItemBody tr').each(function () {
                    var $row = window.$(this);
                    var packSale = $row.find('.grn-pack-sale-price').val();
                    $row.find('.grn-pack-mrp').val(packSale);
                });
            }
            updateGRNAcceptedTotal();
        });

        // Manual sale price change copies to MRP if checked
        window.$(document).on('input', '.grn-pack-sale-price', function () {
            var isSaleIsMrp = window.$('#grn_sale_is_mrp').is(':checked');
            if (isSaleIsMrp) {
                var $row = window.$(this).closest('tr');
                $row.find('.grn-pack-mrp').val(window.$(this).val());
            }
        });
    }

    window.loadRxPreview = function () {
        return;
    };

    document.addEventListener('DOMContentLoaded', function () {
        loadDashboardCounts();
        loadDispenseQueue();
        loadRxValidation();
        loadMARContent();
    });
})();
