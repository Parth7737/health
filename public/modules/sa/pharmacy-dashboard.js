(function () {
    'use strict';

    function toast(title, message, type, duration) {
        if (typeof window.showToast === 'function') {
            window.showToast(title, message, type, duration);
            return;
        }
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

    var dispenseTable = null;
    var inventoryTable = null;
    var expiryTable = null;
    var poTable = null;
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
                        var cls = priority === 'stat' ? 'red' : (priority === 'urgent' ? 'orange' : 'gray');
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
                        return '<span class="badge badge-' + cls + '">' + escapeHtml(status) + '</span>';
                    }
                },
                {
                    targets: 8,
                    render: function (data, type, row) {
                        if (type !== 'display') { return data; }

                        var rx = escapeHtml(row.rx_no || '-');
                        var priority = String(row.priority || '').toLowerCase();
                        return '<div style="display:flex;gap:3px">' +
                            '<button class="btn btn-success btn-xs" onclick="dispenseRx(\'' + rx + '\', this)">✅ Dispense</button>' +
                            '<button class="btn btn-secondary btn-xs" onclick="openModal(\'dispenseModal\')">👁️ View</button>' +
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

    function loadStatOrders() {
        var list = document.getElementById('statOrdersList');
        if (!list) {
            return;
        }
        var stats = [
            { rx: 'RX-STAT-001', patient: 'Mohan Lal Gupta - ICU Bed 3', drug: 'Inj. Noradrenaline 4mg + Dopamine 200mg', time: '10:05', elapsed: '8 min', doctor: 'Dr. Negi' },
            { rx: 'RX-STAT-002', patient: 'Deepak Rawat - HDU Bed 1', drug: 'Inj. Furosemide 80mg IV Push + O2 Supply', time: '10:12', elapsed: '1 min', doctor: 'Dr. Bisht' },
            { rx: 'RX-STAT-003', patient: 'Baby Renu - NICU Bed 2', drug: 'Inj. Ampicillin 250mg + Gentamicin 20mg', time: '09:55', elapsed: '18 min', doctor: 'Dr. Verma' }
        ];

        list.innerHTML = stats
            .map(function (s) {
                var elapsed = parseInt(s.elapsed, 10);
                return '' +
                    '<div style="background:#fff5f5;border:1.5px solid rgba(198,40,40,.2);border-radius:10px;padding:14px;margin-bottom:10px">' +
                    '<div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px">' +
                    '<div><div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">' +
                    '<span class="badge badge-red">🚨 STAT</span><span class="fw-700 fs-13">' + s.patient + '</span></div>' +
                    '<div class="fs-12 text-muted mb-4"><b>Drug:</b> ' + s.drug + '</div>' +
                    '<div class="fs-12 text-muted"><b>Ordered by:</b> ' + s.doctor + ' | <b>Time:</b> ' + s.time + '</div></div>' +
                    '<div style="text-align:right;flex-shrink:0"><div style="font-size:20px;font-weight:900;color:' + (elapsed > 15 ? 'var(--danger)' : 'var(--warning)') + '">' + s.elapsed + '</div>' +
                    '<div class="fs-10 text-muted">elapsed</div>' +
                    '<button class="btn btn-danger btn-xs mt-8" onclick="dispenseSTAT(this, \'' + s.rx + '\')">🚨 Dispense NOW</button></div></div></div>';
            })
            .join('');
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

    function escapeHtml(text) {
        return String(text || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
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
                        if (type !== 'display') {
                            return stock;
                        }

                        var minLevel = toNumber(row.min_level);
                        var style = '';
                        if (stock < minLevel * 0.5) {
                            style = 'color:var(--danger);font-weight:700';
                        } else if (stock < minLevel) {
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
                        var stock = toNumber(row.available_qty);
                        var minLevel = toNumber(row.min_level);
                        var label = 'In Stock';
                        var cls = 'green';

                        if (String(data || '').toLowerCase() === 'expired') {
                            label = 'Critical';
                            cls = 'red';
                        } else if (stock < minLevel * 0.5) {
                            label = 'Critical';
                            cls = 'red';
                        } else if (stock < minLevel || String(data || '').toLowerCase() === 'out_of_stock') {
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
                        var cls = days < 20 ? 'red' : 'orange';
                        var label = days < 0 ? Math.abs(days) + ' days ago' : days + ' days';
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

    function loadGRNLog() {
        var body = document.getElementById('grnBody');
        if (!body) {
            return;
        }
        var grns = [
            { grn: 'GRN-2024-0458', supplier: 'Cipla Ltd.', inv: 'INV-C-8821', items: 24, value: '₹84,500', by: 'Pharmacist Suresh', date: 'Today', status: 'verified' },
            { grn: 'GRN-2024-0457', supplier: 'Sun Pharma', inv: 'INV-SP-3392', items: 18, value: '₹62,000', by: 'Pharmacist Suresh', date: 'Yesterday', status: 'verified' },
            { grn: 'GRN-2024-0456', supplier: 'Dr. Reddy\'s', inv: 'INV-DR-1123', items: 31, value: '₹1,15,800', by: 'Pharmacist Suresh', date: '3 days ago', status: 'partial' }
        ];

        body.innerHTML = grns
            .map(function (g) {
                return '<tr><td class="fw-700 text-primary">' + g.grn + '</td><td>' + g.supplier + '</td><td class="text-muted">' + g.inv + '</td><td>' + g.items + '</td><td class="fw-700">' + g.value + '</td><td class="text-muted">' + g.by + '</td><td class="text-muted">' + g.date + '</td><td><span class="badge badge-' + (g.status === 'verified' ? 'green' : 'orange') + '">' + g.status + '</span></td></tr>';
            })
            .join('');
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
                { data: 'bill_no', name: 'bill_no' },
                { data: 'supplier_name', name: 'supplier_name', orderable: false },
                { data: 'items_count', name: 'items_count', orderable: false, searchable: false },
                { data: 'net_total', name: 'net_total' },
                { data: 'created_by_name', name: 'created_by_name', orderable: false },
                { data: 'bill_date', name: 'bill_date' },
                { data: 'payment_status', name: 'payment_status', orderable: false },
                { data: 'id', name: 'id', orderable: false, searchable: false }
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function (data, type) {
                        if (type !== 'display') { return data; }
                        return '<span class="fw-700 text-primary">' + escapeHtml(data || '-') + '</span>';
                    }
                },
                {
                    targets: 1,
                    render: function (data, type) {
                        if (type !== 'display') { return data; }
                        return '<span>' + escapeHtml(data || '—') + '</span>';
                    }
                },
                {
                    targets: 2,
                    render: function (data, type) {
                        var items = parseInt(data, 10);
                        if (isNaN(items)) { items = 0; }
                        if (type !== 'display') { return items; }
                        return String(items);
                    }
                },
                {
                    targets: 3,
                    render: function (data, type) {
                        if (type !== 'display') {
                            var amount = parseFloat(data || 0);
                            return isNaN(amount) ? 0 : amount;
                        }
                        return '<span class="fw-700">' + formatCurrency(data) + '</span>';
                    }
                },
                {
                    targets: 4,
                    render: function (data, type) {
                        if (type !== 'display') { return data; }
                        return '<span class="text-muted">' + escapeHtml(data || '—') + '</span>';
                    }
                },
                {
                    targets: 5,
                    render: function (data, type) {
                        if (type !== 'display') { return data; }
                        return '<span class="text-muted">' + escapeHtml(data || '-') + '</span>';
                    }
                },
                {
                    targets: 6,
                    render: function (data, type) {
                        var paymentStatus = String(data || 'pending').toLowerCase();
                        if (type !== 'display') { return paymentStatus; }

                        var statusText = paymentStatus === 'paid' ? 'delivered' : (paymentStatus === 'partial' ? 'approved' : 'pending approval');
                        var statusClass = paymentStatus === 'paid' ? 'green' : (paymentStatus === 'partial' ? 'blue' : 'orange');

                        return '<span class="badge badge-' + statusClass + '">' + statusText + '</span>';
                    }
                },
                {
                    targets: 7,
                    render: function (data, type, row) {
                        if (type !== 'display') { return data; }

                        var billNo = escapeHtml(row.bill_no || 'PO');
                        var paymentStatus = String(row.payment_status || 'pending').toLowerCase();
                        if (paymentStatus === 'pending') {
                            return '<button class="btn btn-success btn-xs po-approve-btn" data-po="' + billNo + '" data-id="' + row.id + '">✅ Approve</button>';
                        }

                        var printUrl = row.id ? getPurchasePrintUrl(row.id) : '';
                        return printUrl
                            ? '<button class="btn btn-secondary btn-xs po-print-btn" data-url="' + escapeHtml(printUrl) + '">🖨️ Print</button>'
                            : '<span class="text-muted fs-11">—</span>';
                    }
                }
            ],
            language: {
                search: '',
                searchPlaceholder: 'Search purchase orders...'
            },
            error: function () {
                toast('Error', 'Unable to load purchase orders.', 'error');
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

    function loadRxPreviewBody() {
        var body = document.getElementById('rxPreviewBody');
        if (!body) {
            return;
        }
        var items = [
            { drug: 'Tab. Amlodipine 5mg', dose: '1 tab', freq: '1-0-0', days: 30, qty: '30 tabs' },
            { drug: 'Tab. Metformin 500mg', dose: '1 tab', freq: '1-0-1', days: 30, qty: '60 tabs' },
            { drug: 'Tab. Aspirin 75mg', dose: '1 tab', freq: '0-1-0', days: 30, qty: '30 tabs' }
        ];

        body.innerHTML = items
            .map(function (i) {
                return '<tr><td>' + i.drug + '</td><td>' + i.dose + '</td><td>' + i.freq + '</td><td>' + i.days + 'd</td><td>' + i.qty + '</td><td><input type="checkbox" checked style="accent-color:var(--success)"></td></tr>';
            })
            .join('');
    }

    function updateGRNTotal() {
        var amountNodes = document.querySelectorAll('[id^="grnAmt"]');
        var total = 0;
        amountNodes.forEach(function (node) {
            var value = parseFloat(String(node.textContent || '0').replace('₹', '').replace(/,/g, ''));
            if (!isNaN(value)) {
                total += value;
            }
        });
        var totalNode = document.getElementById('grnTotal');
        if (totalNode) {
            totalNode.textContent = '₹' + total.toFixed(2);
        }
    }

    window.filterDispenseQueue = function (q) {
        var searchEl = document.getElementById('dispenseSearch');
        if (searchEl && typeof q === 'string' && searchEl.value !== q) {
            searchEl.value = q;
        }
        if (dispenseTable) {
            dispenseTable.ajax.reload();
        }
    };

    window.dispenseRx = function (rx, btn) {
        var row = btn.closest('tr');
        if (row) {
            row.style.opacity = '0.6';
        }
        btn.outerHTML = '<span class="badge badge-green">✅ Dispensed</span>';
        toast('Dispensed', rx + ' - Dispensed successfully. Label printed.', 'success');
    };

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
            dispenseTable.ajax.reload();
        } else {
            loadDispenseQueue();
        }
        toast('Refreshed', 'Queue refreshed', 'success', 2000);
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
        if (btn) { btn.disabled = true; }
        toast('Quarantine', 'Batch #' + id + ' marked for quarantine.', 'warning');
    };

    window.expiryReturn = function (id, btn) {
        if (btn) { btn.disabled = true; }
        toast('Return', 'Return process initiated for batch #' + id + '.', 'info');
    };

    window.processExpiredBatches = function () {
        var url = getExpiryProcessUrl();
        if (!url || typeof window.$ === 'undefined') {
            toast('Error', 'Process route not available.', 'error');
            return;
        }

        if (typeof window.loader === 'function') { window.loader(); }

        window.$.ajax({
            url: url,
            type: 'POST',
            data: { _token: getCsrfToken() },
            success: function (response) {
                if (typeof window.loader === 'function') { window.loader('hide'); }
                toast('Done', response.message || 'Expired batches processed.', 'success');
                if (expiryTable) { expiryTable.ajax.reload(null, false); }
            },
            error: function () {
                if (typeof window.loader === 'function') { window.loader('hide'); }
                toast('Error', 'Failed to process expired batches.', 'error');
            }
        });
    };

    if (typeof window.$ !== 'undefined') {
        window.$(document).on('click', '.po-approve-btn', function () {
            var row = this.closest('tr');
            if (!row) {
                return;
            }

            var po = this.getAttribute('data-po') || 'PO';
            var statusBadge = row.querySelector('td:nth-child(7) .badge');
            if (statusBadge) {
                statusBadge.className = 'badge badge-blue';
                statusBadge.textContent = 'approved';
            }
            this.outerHTML = '<button class="btn btn-secondary btn-xs po-print-btn" data-url="' + escapeHtml(getPurchasePrintUrl(this.getAttribute('data-id'))) + '">🖨️ Print</button>';
            toast('PO Approved', po + ' approved', 'success');
        });

        window.$(document).on('click', '.po-print-btn', function () {
            var url = this.getAttribute('data-url');
            if (url) {
                window.open(url, '_blank');
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

        if (paneId === 'poPane') {
            var poWasInit = !!poTable;
            loadPOList();
            if (poWasInit && poTable) {
                poTable.ajax.reload();
            }
        }
    };

    window.confirmDispense = function () {
        toast('Dispensed', 'Prescription dispensed. Drug label printed.', 'success');
        window.closeModal('dispenseModal');
    };

    window.holdDispense = function () {
        toast('On Hold', 'Prescription placed on hold', 'warning');
        window.closeModal('dispenseModal');
    };

    window.submitGRN = function () {
        toast('GRN Submitted', 'GRN submitted. Stock levels updated.', 'success');
        window.closeModal('grnModal');
    };

    window.loadRxPreview = function () {
        return;
    };

    window.calcGRNRow = function (idx) {
        var qty = parseFloat((document.getElementById('grnQty' + idx) || {}).value || 0);
        var rate = parseFloat((document.getElementById('grnRate' + idx) || {}).value || 0);
        var el = document.getElementById('grnAmt' + idx);
        if (el) {
            el.textContent = '₹' + (qty * rate).toFixed(2);
        }
        updateGRNTotal();
    };

    window.updateGRNTotal = updateGRNTotal;

    window.addGRNRow = function () {
        var idx = grnRowCount++;
        var tr = document.createElement('tr');
        tr.id = 'grnRow' + idx;
        tr.innerHTML = '' +
            '<td><input class="form-control ph-grid-input" placeholder="Drug name"></td>' +
            '<td><input class="form-control ph-grid-input ph-grid-input-batch" placeholder="Batch"></td>' +
            '<td><input type="date" class="form-control ph-grid-input"></td>' +
            '<td><input type="date" class="form-control ph-grid-input"></td>' +
            '<td><input type="number" class="form-control ph-grid-input ph-grid-input-qty" id="grnQty' + idx + '" oninput="calcGRNRow(' + idx + ')"></td>' +
            '<td><input type="number" class="form-control ph-grid-input ph-grid-input-free"></td>' +
            '<td><input type="number" class="form-control ph-grid-input ph-grid-input-price" id="grnMRP' + idx + '"></td>' +
            '<td><input type="number" class="form-control ph-grid-input ph-grid-input-price" id="grnRate' + idx + '" oninput="calcGRNRow(' + idx + ')"></td>' +
            '<td><span id="grnAmt' + idx + '" class="fw-700 fs-12">₹0</span></td>' +
            '<td><button class="btn btn-danger btn-xs" type="button" onclick="this.closest(\'tr\').remove(); updateGRNTotal();">✕</button></td>';

        var body = document.getElementById('grnItemBody');
        if (body) {
            body.appendChild(tr);
        }
    };

    document.addEventListener('DOMContentLoaded', function () {
        loadDispenseQueue();
        loadStatOrders();
        loadRxValidation();
        loadGRNLog();
        loadMARContent();
        loadRxPreviewBody();
        updateGRNTotal();
    });
})();
