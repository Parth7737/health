$(document).ready(function () {
    const state = {
        table: null,
        reportsTable: null,
        snapshotRows: [],
        urgentRows: [],
        resultEntrySelection: null,
        resultEntryLoading: false,
        resultEntryModalOpen: false,
        categoryOptions: [],
        searchTimer: null,
        refreshDebounceTimer: null,
        refreshInFlight: false,
        refreshQueued: false,
        charts: {
            tat: null,
            volume: null,
        },
    };

    function safeText(value, fallback = '-') {
        const raw = String(value ?? '').trim();
        return raw === '' ? fallback : raw;
    }

    function stripHtml(value) {
        return $('<div>').html(value || '').text();
    }

    function escapeHtml(value) {
        return $('<div>').text(value ?? '').html();
    }

    function getCsrfTokenSync() {
        return window?.Laravel?.csrfToken
            || $('meta[name="csrf-token"]').attr('content')
            || $('input[name="_token"]').first().val()
            || '';
    }

    function normalizeReportDateFilter(value) {
        const input = String(value || '').trim();
        if (!input) {
            return '';
        }

        const isoMatch = input.match(/^(\d{4})-(\d{2})-(\d{2})$/);
        if (isoMatch) {
            return `${isoMatch[3]}-${isoMatch[2]}-${isoMatch[1]}`;
        }

        const slashMatch = input.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/);
        if (slashMatch) {
            return `${slashMatch[2].padStart(2, '0')}-${slashMatch[1].padStart(2, '0')}-${slashMatch[3]}`;
        }

        const dashMatch = input.match(/^(\d{1,2})-(\d{1,2})-(\d{4})$/);
        if (dashMatch) {
            return `${dashMatch[1].padStart(2, '0')}-${dashMatch[2].padStart(2, '0')}-${dashMatch[3]}`;
        }

        return input;
    }

    function containsCriticalMarker(row) {
        const source = [
            row?.report_summary,
            row?.report_text,
            Array.isArray(row?.tests_ordered) ? row.tests_ordered.map((item) => item?.test_name).join(' ') : row?.test_name,
            stripHtml(row?.status),
        ].join(' ').toLowerCase();
        return source.includes('critical') || source.includes('panic') || source.includes('flag');
    }

    function priorityClass(priority) {
        const value = String(priority || 'Routine').trim().toLowerCase();
        if (value === 'stat') {
            return 'lab-tag-priority-stat';
        }
        if (value === 'urgent') {
            return 'lab-tag-priority-urgent';
        }
        return 'lab-tag-priority-routine';
    }

    function renderPriorityTag(priority) {
        const label = safeText(priority, 'Routine');
        return `<span class="lab-tag ${priorityClass(label)}">${escapeHtml(label)}</span>`;
    }

    function renderSampleCell(row) {
        return `
            <div class="lab-cell-stack">
                <div class="lab-mini-title">${escapeHtml(safeText(row?.sample_id))}</div>
                <div class="lab-inline-note">Ordered: ${escapeHtml(safeText(row?.ordered_at))}</div>
            </div>
        `;
    }

    function renderPatientCell(row) {
        return `
            <div class="lab-cell-stack">
                <div class="lab-mini-title">${escapeHtml(safeText(row?.patient_name))}</div>
                <div class="lab-inline-note">${escapeHtml(safeText(row?.patient_context, '-'))}</div>
            </div>
        `;
    }

    function renderTestsOrderedCell(items) {
        const list = Array.isArray(items) ? items : [];
        if (!list.length) {
            return '<span class="text-muted">-</span>';
        }

        return `<div class="lab-cell-stack">${list.map((item) => `
            <div class="lab-mini-card">
                <div class="lab-mini-title">${safeText(item?.test_name)}</div>
                <div class="lab-mini-meta">${safeText(item?.category_name, 'Uncategorized')}</div>
            </div>
        `).join('')}</div>`;
    }

    function renderCategoryCell(row) {
        const categories = Array.isArray(row?.category_list) ? row.category_list : [];
        if (!categories.length) {
            return '<span class="lab-tag">Uncategorized</span>';
        }

        return `<div class="lab-tag-row">${categories.map((category) => `<span class="lab-tag">${escapeHtml(safeText(category))}</span>`).join('')}</div>`;
    }

    function renderStatusCell(row) {
        const statuses = Array.isArray(row?.status_breakdown) ? row.status_breakdown : [];
        if (!statuses.length) {
            return row?.status || '<span class="text-muted">-</span>';
        }

        return `<div class="lab-cell-stack">${statuses.map((item) => `
            <div class="lab-mini-card">
                <div class="lab-mini-title">${escapeHtml(safeText(item?.label))}</div>
            </div>
        `).join('')}</div>`;
    }

    const STATUS_STEPS = [
        { key: 'ordered',          label: 'Ordered',    short: '1' },
        { key: 'sample_collected', label: 'Collected',  short: '2' },
        { key: 'in_progress',      label: 'Processing', short: '3' },
        { key: 'completed',        label: 'Completed',  short: '✓' },
    ];

    const STATUS_ACTION_LABEL = {
        ordered:          'Collect Sample',
        sample_collected: 'Start Processing',
        in_progress:      null, // result form instead
    };

    function renderStatusStepper(statusKey) {
        const currentIndex = STATUS_STEPS.findIndex((s) => s.key === statusKey);

        return `
            <div class="lab-stepper">
                ${STATUS_STEPS.map((step, idx) => {
                    const isDone   = idx < currentIndex;
                    const isActive = idx === currentIndex;
                    const dotClass = isDone ? 'done' : isActive ? 'active' : '';
                    const lblClass = isDone ? 'done' : isActive ? 'active' : '';
                    const connClass = isDone ? 'done' : '';
                    const connector = idx < STATUS_STEPS.length - 1
                        ? `<div class="lab-step-connector ${connClass}"></div>`
                        : '';
                    return `
                        <div class="lab-step">
                            <div class="lab-step-wrap">
                                <div class="lab-step-dot ${dotClass}">${isDone ? '✓' : step.short}</div>
                                <div class="lab-step-label ${lblClass}">${step.label}</div>
                            </div>
                            ${connector}
                        </div>
                    `;
                }).join('')}
            </div>
        `;
    }

    function renderActionsCell(items) {
        const list = Array.isArray(items) ? items : [];
        if (!list.length) {
            return '<span class="text-muted">-</span>';
        }

        return `<div class="lab-action-grid">${list.map((item) => {
            const statusKey    = String(item?.status_key || '').toLowerCase();
            const actionLabel  = STATUS_ACTION_LABEL[statusKey];
            const canMove      = !!actionLabel && !['completed', 'cancelled'].includes(statusKey);
            const canEnterResult = statusKey === 'in_progress';

            return `
                <div class="lab-action-row">
                    <div class="lab-action-row-header">
                        <div>
                            <div class="lab-mini-title">${escapeHtml(safeText(item?.test_name))}</div>
                        </div>
                        <div class="lab-action-buttons">
                            ${canMove ? `<button class="btn btn-warning btn-xs update-item-status" data-url="${escapeHtml(item.status_url)}">${escapeHtml(actionLabel)}</button>` : ''}
                            ${canEnterResult ? `<button class="btn btn-primary btn-xs open-report-form" data-id="${Number(item?.item_id || 0)}">📊 Enter Result</button>` : ''}
                            ${item?.report_ready ? `<a href="${escapeHtml(item.print_url)}" target="_blank" class="btn btn-success btn-xs">🖨 Print</a>` : ''}
                        </div>
                    </div>
                    ${renderStatusStepper(statusKey)}
                </div>
            `;
        }).join('')}</div>`;
    }

    function parseDmyHmToDate(value) {
        const input = String(value || '').trim();
        const match = input.match(/^(\d{2})-(\d{2})-(\d{4})\s+(\d{2}):(\d{2})$/);
        if (!match) {
            return null;
        }

        const [, dd, mm, yyyy, hh, min] = match;
        const date = new Date(Number(yyyy), Number(mm) - 1, Number(dd), Number(hh), Number(min));
        return Number.isNaN(date.getTime()) ? null : date;
    }

    function formatMinutesForTat(totalMinutes) {
        const minutes = Math.max(0, Number(totalMinutes) || 0);
        if (minutes < 60) {
            return `${minutes} min`;
        }

        const hours = Math.floor(minutes / 60);
        const remainingMinutes = minutes % 60;
        if (remainingMinutes === 0) {
            return `${hours}h`;
        }

        return `${hours}h ${remainingMinutes}min`;
    }

    function computeUrgentTat(orderedAtText) {
        const ORDER_TAT_MINUTES = 60;
        const orderedAt = parseDmyHmToDate(orderedAtText);
        if (!orderedAt) {
            return { elapsed: '--', remaining: '--' };
        }

        const elapsedMinutes = Math.max(0, Math.floor((Date.now() - orderedAt.getTime()) / 60000));
        const remainingMinutes = Math.max(0, ORDER_TAT_MINUTES - elapsedMinutes);

        return {
            elapsed: formatMinutesForTat(elapsedMinutes),
            remaining: formatMinutesForTat(remainingMinutes),
        };
    }

    function syncCategoryOptions(rows) {
        const filter = document.getElementById('filter-category');
        if (!filter) {
            return;
        }

        const nextOptions = Array.from(new Set((rows || []).flatMap((row) => Array.isArray(row?.category_list) ? row.category_list : [])))
            .map((value) => String(value || '').trim())
            .filter(Boolean)
            .sort((left, right) => left.localeCompare(right));

        if (!$('#filter-category').val() || nextOptions.length > state.categoryOptions.length) {
            state.categoryOptions = nextOptions;
        }

        const selectedValue = $('#filter-category').val() || '';
        filter.innerHTML = '<option value="">All Category</option>' + state.categoryOptions.map((value) => `
            <option value="${escapeHtml(value)}">${escapeHtml(value)}</option>
        `).join('');
        $('#filter-category').val(selectedValue);
    }

    function initializeDatePicker() {
        if (typeof flatpickr !== 'function') {
            return;
        }

        flatpickr('.diagnosis-date', {
            dateFormat: 'd-m-Y',
        });

        const reportInput = document.getElementById('labReportDate');
        if (reportInput && !reportInput._flatpickr) {
            flatpickr(reportInput, {
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'm/d/Y',
                allowInput: true,
            });
        }
    }

    function initializeTooltips() {
        if (typeof $().tooltip !== 'function') {
            return;
        }
        $('[data-bs-toggle="tooltip"]').tooltip('dispose');
        $('[data-bs-toggle="tooltip"]').tooltip({ container: 'body' });
    }

    function switchLabTabInternal(targetPaneId, triggerButton) {
        const panes = [
            'sampleQueuePane',
            'urgentPane',
            'resultEntryPane',
            'criticalPane',
            'reportsPane',
            'tatPane',
            'analyzerPane',
        ];

        panes.forEach((paneId) => {
            const pane = document.getElementById(paneId);
            if (pane) {
                pane.hidden = paneId !== targetPaneId;
            }
        });

        document.querySelectorAll('#labTabBar .tab-btn').forEach((btn) => btn.classList.remove('active'));

        if (triggerButton?.classList) {
            triggerButton.classList.add('active');
        } else {
            const autoBtn = document.querySelector(`#labTabBar .tab-btn[data-lab-tab-target="${targetPaneId}"]`);
            if (autoBtn) {
                autoBtn.classList.add('active');
            }
        }

        if (targetPaneId === 'reportsPane') {
            if (!state.reportsTable) {
                initReportsTable();
            }

            window.requestAnimationFrame(function () {
                if (!state.reportsTable) {
                    return;
                }

                state.reportsTable.columns.adjust();
                if (state.reportsTable.responsive && typeof state.reportsTable.responsive.recalc === 'function') {
                    state.reportsTable.responsive.recalc();
                }
                state.reportsTable.ajax.reload(null, false);
            });
        }

        if (targetPaneId === 'tatPane') {
            window.requestAnimationFrame(function () {
                fetchAndRenderTatAnalytics();
            });
        }

        if (targetPaneId === 'analyzerPane') {
            window.requestAnimationFrame(function () {
                fetchAndRenderAnalyzerConfig();
            });
        }
    }

    window.switchLabTab = function switchLabTab(targetPaneId, triggerButton) {
        switchLabTabInternal(targetPaneId, triggerButton);
    };

    function setKpiLoading(isLoading) {
        ['labKpiTotal', 'labKpiUrgent', 'labKpiCritical', 'labKpiCompleted'].forEach((id) => {
            const node = document.getElementById(id);
            if (!node) return;
            node.classList.toggle('loading', isLoading);
        });
    }

    async function fetchStatusCount(statusValue = '') {
        const token = await csrftoken();
        return new Promise((resolve, reject) => {
            $.post(route('loadtable'), {
                _token: token,
                draw: 1,
                start: 0,
                length: 1,
                item_wise: 1,
                status: statusValue,
                category: $('#filter-category').val() || '',
                search_text: $('#labSearch').val() || '',
                date_from: $('#filter-date-from').val() || '',
                date_to: $('#filter-date-to').val() || '',
            }, function (response) {
                resolve(Number(response?.recordsFiltered || 0));
            }).fail(function (xhr) {
                reject(xhr);
            });
        });
    }

    async function fetchSnapshotRows() {
        const token = await csrftoken();
        return new Promise((resolve, reject) => {
            $.post(route('loadtable'), {
                _token: token,
                draw: 1,
                start: 0,
                length: 200,
                item_wise: 1,
                status: '',
                category: $('#filter-category').val() || '',
                search_text: $('#labSearch').val() || '',
                date_from: $('#filter-date-from').val() || '',
                date_to: $('#filter-date-to').val() || '',
            }, function (response) {
                resolve(Array.isArray(response?.data) ? response.data : []);
            }).fail(function (xhr) {
                reject(xhr);
            });
        });
    }

    async function fetchUrgentRows() {
        const token = await csrftoken();
        return new Promise((resolve, reject) => {
            $.post(route('loadtable'), {
                _token: token,
                draw: 1,
                start: 0,
                length: 200,
                item_wise: 1,
                status: '',
                category: $('#filter-category').val() || '',
                search_text: $('#labSearch').val() || '',
                date_from: $('#filter-date-from').val() || '',
                date_to: $('#filter-date-to').val() || '',
                urgent_only: 1,
            }, function (response) {
                resolve(Array.isArray(response?.data) ? response.data : []);
            }).fail(function (xhr) {
                reject(xhr);
            });
        });
    }

    function updateTabAndKpis() {
        const rows = state.snapshotRows || [];
        const urgentRows = state.urgentRows || [];
        const queueRows = rows.filter((row) => String(row?.status_key || '').toLowerCase() !== 'completed');
        const queueTotal = queueRows.length;
        const overallTotal = rows.length;
        const urgent = urgentRows.length;
        const completed = rows.filter((row) => stripHtml(row?.status).toLowerCase().includes('completed')).length;
        const criticalEntries = getTodayCriticalEntries(rows);
        const groupedCritical = groupCriticalEntries(criticalEntries);
        const critical = groupedCritical.filter((group) => !group.is_acknowledged).length;

        const tatMinutes = queueRows
            .map((row) => parseDmyHmToDate(row?.ordered_at))
            .filter(Boolean)
            .map((orderedAt) => Math.max(0, Math.floor((Date.now() - orderedAt.getTime()) / 60000)));
        const avgTatHours = tatMinutes.length
            ? `${(tatMinutes.reduce((sum, value) => sum + value, 0) / tatMinutes.length / 60).toFixed(1)}h`
            : '--';

        const assign = (id, value) => {
            const node = document.getElementById(id);
            if (node) {
                node.textContent = value;
            }
        };

        assign('labKpiTotal', queueTotal);
        assign('labKpiUrgent', urgent);
        assign('labKpiCompleted', completed);
        assign('labKpiCritical', critical);
        assign('labTabCountQueue', queueTotal);
        assign('labTabCountUrgent', urgent);
        assign('labTabCountCritical', critical);

        const quality = overallTotal > 0 ? `${Math.round((completed / overallTotal) * 100)}%` : '--';
        assign('labKpiQuality', quality);
        assign('labKpiTat', avgTatHours);
    }

    function getTodayCriticalEntries(rows) {
        return (rows || []).flatMap((row) => {
            const entries = Array.isArray(row?.critical_today_entries) ? row.critical_today_entries : [];
            return entries.map((entry) => ({
                ...entry,
                sample_id: row?.sample_id,
                patient_name: row?.patient_name,
                visit_no: row?.visit_no,
                patient_mrn: row?.patient_mrn,
            }));
        });
    }

    function groupCriticalEntries(entries) {
        const grouped = new Map();
        (entries || []).forEach((entry) => {
            const key = `${entry?.item_id || ''}::${entry?.sample_id || ''}`;
            if (!grouped.has(key)) {
                grouped.set(key, {
                    groupKey: key,
                    item_id: entry?.item_id,
                    sample_id: entry?.sample_id,
                    patient_name: entry?.patient_name,
                    visit_no: entry?.visit_no,
                    patient_mrn: entry?.patient_mrn,
                    doctor_name: entry?.doctor_name,
                    print_url: entry?.print_url,
                    entries: [],
                });
            }

            grouped.get(key).entries.push(entry);
        });

        return Array.from(grouped.values()).map((group) => ({
            ...group,
            is_acknowledged: group.entries.every((entry) => !!entry?.is_acknowledged),
            is_doctor_called: group.entries.every((entry) => !!entry?.is_doctor_called),
        }));
    }

    function renderUrgentList() {
        const container = document.getElementById('labUrgentList');
        if (!container) return;

        const heading = document.getElementById('labUrgentHeading');

        const rows = state.urgentRows || [];

        if (heading) {
            heading.textContent = `${rows.length} URGENT/STAT Tests Active — Target TAT: 1 hour. Clinical team awaiting results.`;
        }

        if (!rows.length) {
            container.innerHTML = '<div class="empty-state p-16"><div class="empty-title">No urgent samples right now</div></div>';
            return;
        }

        container.innerHTML = rows.slice(0, 20).map((row) => {
            const sampleId = safeText(row?.sample_id);
            const patient = safeText(row?.patient_name);
            const test = Array.isArray(row?.tests_ordered) ? row.tests_ordered.map((item) => item?.test_name).filter(Boolean).join(', ') : '-';
            const statusText = safeText(row?.status_label);
            const priority = String(row?.priority || 'Urgent').trim().toUpperCase();
            const tat = computeUrgentTat(row?.ordered_at);
            const tests = Array.isArray(row?.tests_ordered) ? row.tests_ordered : [];
            const inProgressItem = tests.find((item) => String(item?.status_key || '').toLowerCase() === 'in_progress');

            let actionHtml = '';
            if (inProgressItem?.item_id) {
                actionHtml = `<button class="btn btn-primary btn-xs open-report-form" data-id="${Number(inProgressItem.item_id)}">📊 Enter Result</button>`;
            }

            return `
                <div class="lab-urgent-card">
                    <div class="lab-urgent-main">
                        <div class="lab-urgent-head">
                            <span class="badge badge-${priority === 'STAT' ? 'red' : 'orange'}">${escapeHtml(priority)}</span>
                            <span class="lab-urgent-patient">${escapeHtml(patient)} — ${escapeHtml(safeText(row?.visit_no, '-'))}</span>
                        </div>
                        <div class="lab-urgent-tests"><b>Tests:</b> ${test}</div>
                        <div class="lab-urgent-meta">Sample ID: ${escapeHtml(sampleId)}</div>
                        <div class="lab-urgent-status">${escapeHtml(statusText)}</div>
                    </div>
                    <div class="lab-urgent-tat">
                        <div class="lab-urgent-elapsed">${escapeHtml(tat.elapsed)}</div>
                        <div class="lab-urgent-tat-note">elapsed</div>
                        <div class="lab-urgent-tat-note">TAT left: ${escapeHtml(tat.remaining)}</div>
                    </div>
                    <div class="lab-urgent-action">
                        ${actionHtml}
                    </div>
                </div>
            `;
        }).join('');
    }

    function renderResultEntryList() {
        // Disabled intentionally: Result Entry now runs in strict single-flow mode (search -> auto-select -> load).
    }

    function getRowPrimaryTest(row) {
        const tests = Array.isArray(row?.tests_ordered) ? row.tests_ordered : [];
        return tests[0] || null;
    }

    function buildResultSearchHaystack(row) {
        const primaryTest = getRowPrimaryTest(row);
        return [
            row?.sample_id,
            row?.order_no,
            row?.patient_name,
            row?.patient_mrn,
            row?.visit_no,
            row?.id,
            row?.item_id,
            primaryTest?.item_id,
            primaryTest?.test_name,
            primaryTest?.status_label,
        ].join(' ').toLowerCase();
    }

    function getResultEntryMatches(query) {
        const q = String(query || '').trim().toLowerCase();
        const rows = state.snapshotRows || [];
        if (!q) {
            return [];
        }

        return rows.filter((row) => buildResultSearchHaystack(row).includes(q)).slice(0, 8);
    }

    function getResultEntryRowsByQuery(query) {
        const q = String(query || '').trim().toLowerCase();
        if (!q) {
            return [];
        }

        const rows = state.snapshotRows || [];

        const exactRows = rows.filter((row) => {
            const primaryTest = getRowPrimaryTest(row);
            const exactCandidates = [
                row?.sample_id,
                row?.order_no,
                row?.patient_mrn,
                row?.id,
                row?.item_id,
                primaryTest?.item_id,
            ].map((v) => String(v ?? '').trim().toLowerCase()).filter(Boolean);

            return exactCandidates.includes(q);
        });

        if (exactRows.length) {
            return exactRows;
        }

        return rows.filter((row) => buildResultSearchHaystack(row).includes(q));
    }

    function getPreferredResultItem(row) {
        return getRowPrimaryTest(row);
    }

    function renderResultEntryMatches() {
        const listNode = document.getElementById('labResultMatchList');
        if (!listNode) return;

        const query = $('#labResultSearch').val() || '';
        const matches = getResultEntryMatches(query);

        if (!matches.length) {
            listNode.innerHTML = '';
            state.resultEntrySelection = null;
            renderSelectedResultEntryCard();
            return;
        }

        const currentSelectionId = Number(state.resultEntrySelection?.id || 0);
        if (!currentSelectionId || !matches.some((row) => Number(row?.id || 0) === currentSelectionId)) {
            state.resultEntrySelection = matches[0];
        }

        const selectedId = Number(state.resultEntrySelection?.id || 0);
        listNode.innerHTML = matches.map((row) => {
            const isActive = Number(row?.id || 0) === selectedId;
            const selectedItem = getPreferredResultItem(row);
            const statusKey = String(selectedItem?.status_key || row?.status_key || '').toLowerCase();
            const statusClass = statusKey === 'completed'
                ? 'lab-result-chip-completed'
                : statusKey === 'in_progress'
                    ? 'lab-result-chip-inprogress'
                    : 'lab-result-chip-pending';
            const statusText = safeText(selectedItem?.status_label || row?.status_label);
            const tests = safeText(selectedItem?.test_name);

            return `
                <div class="lab-result-match-card ${isActive ? 'active' : ''}" data-result-row-id="${Number(row?.id || 0)}">
                    <div>
                        <div class="lab-mini-title">${escapeHtml(safeText(row?.patient_name))} — ${escapeHtml(safeText(row?.visit_no))}</div>
                        <div class="lab-inline-note">Sample: ${escapeHtml(safeText(row?.sample_id))} | MRN: ${escapeHtml(safeText(row?.patient_mrn))}</div>
                        <div class="lab-inline-note" style="margin-top:2px">Tests: ${tests}</div>
                    </div>
                    <span class="lab-result-chip ${statusClass}">${escapeHtml(statusText)}</span>
                </div>
            `;
        }).join('');

        renderSelectedResultEntryCard();
    }

    function renderSelectedResultEntryCard() {
        const node = document.getElementById('labSelectedResultEntry');
        if (!node) return;

        const row = state.resultEntrySelection;
        if (!row) {
            node.innerHTML = '';
            return;
        }

        const selectedItem = getPreferredResultItem(row);
        const tests = safeText(selectedItem?.test_name);
        const statusKey = String(selectedItem?.status_key || row?.status_key || '').toLowerCase();

        let actionHtml = '<span class="text-muted fs-11">No action available</span>';
        if (statusKey === 'completed' && selectedItem?.print_url) {
            actionHtml = `<a href="${escapeHtml(selectedItem.print_url)}" target="_blank" class="btn btn-success btn-sm">🖨 Print Report</a>`;
        } else if (selectedItem?.item_id) {
            actionHtml = `<button type="button" class="btn btn-primary btn-sm" id="labLoadSelectedSampleBtn">📊 Load Result Form</button>`;
        }

        node.innerHTML = `
            <div class="lab-result-selected">
                <div class="lab-result-selected-title">Selected Sample: ${escapeHtml(safeText(row?.sample_id))}</div>
                <div class="lab-result-selected-meta">Patient: ${escapeHtml(safeText(row?.patient_name))} | MRN: ${escapeHtml(safeText(row?.patient_mrn))} | Visit: ${escapeHtml(safeText(row?.visit_no))}</div>
                <div class="lab-result-selected-meta">Tests: ${tests}</div>
                <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                    ${actionHtml}
                </div>
            </div>
        `;
    }

    function openReportFormById(id) {
        const itemId = Number(id || 0);
        if (!itemId) {
            sendmsg('error', 'Unable to identify report item.');
            return;
        }

        if (state.resultEntryLoading || state.resultEntryModalOpen) {
            sendmsg('info', 'Only one report can be loaded at a time. Please close the current form first.');
            return;
        }

        state.resultEntryLoading = true;
        const loadBtn = document.getElementById('labResultSearchBtn');
        if (loadBtn) {
            loadBtn.disabled = true;
            loadBtn.textContent = 'Loading...';
        }

        loader('show');
        csrftoken().then((token) => {
            $.post(route('showform', { item: itemId }), { _token: token }, function (response) {
                loader('hide');
                $('#ajaxdata').html(response);
                $('.add-datamodal .modal-dialog').addClass('modal-xl');
                $('.add-datamodal').modal('show');
                state.resultEntryModalOpen = true;
                state.resultEntryLoading = false;
                if (loadBtn) {
                    loadBtn.disabled = false;
                    loadBtn.textContent = 'Load Sample';
                }
            }).fail(function (xhr) {
                loader('hide');
                sendmsg('error', xhr?.responseJSON?.message || 'Unable to load report form.');
                state.resultEntryLoading = false;
                if (loadBtn) {
                    loadBtn.disabled = false;
                    loadBtn.textContent = 'Load Sample';
                }
            });
        });
    }

    function handleResultEntryMatches(query) {
        const rows = getResultEntryRowsByQuery(query);
        if (!rows.length) {
            state.resultEntrySelection = null;
            renderResultEntryMatches();
            sendmsg('error', 'No report found for the entered Report No / MRN.');
            return;
        }

        const actionableRows = rows.filter((row) => {
            const item = getPreferredResultItem(row);
            const statusKey = String(item?.status_key || row?.status_key || '').toLowerCase();
            return statusKey === 'in_progress';
        });

        if (actionableRows.length === 1) {
            const item = getPreferredResultItem(actionableRows[0]);
            if (item?.item_id) {
                openReportFormById(item.item_id);
                return;
            }
        }

        if (actionableRows.length > 1) {
            state.resultEntrySelection = actionableRows[0];
            renderResultEntryMatches();
            sendmsg('info', `${actionableRows.length} tests found for this order. Select one test card to open result entry.`);
            return;
        }

        const hasCompleted = rows.some((row) => {
            const item = getPreferredResultItem(row);
            return String(item?.status_key || row?.status_key || '').toLowerCase() === 'completed';
        });

        if (hasCompleted) {
            sendmsg('info', 'Selected report item is already completed.');
            return;
        }

        sendmsg('info', 'Report items are not in progress yet. Move status to In Progress first.');
    }

    function loadSelectedResultEntry() {
        const row = state.resultEntrySelection;
        const selectedItem = getPreferredResultItem(row);
        if (!row || !selectedItem) {
            sendmsg('error', 'Please select a report item card first.');
            return;
        }

        const statusKey = String(selectedItem?.status_key || row?.status_key || '').toLowerCase();
        if (statusKey === 'completed') {
            sendmsg('info', 'Report is already complete. Result entry is not allowed.');
            return;
        }

        if (statusKey !== 'in_progress') {
            sendmsg('info', 'Report is not in progress. Please move status to In Progress first.');
            return;
        }

        openReportFormById(selectedItem?.item_id);
    }

    function renderCriticalList() {
        const container = document.getElementById('labCriticalList');
        if (!container) return;

        const heading = document.getElementById('labCriticalHeading');

        const entries = getTodayCriticalEntries(state.snapshotRows || []);
        const grouped = groupCriticalEntries(entries);
        const unacknowledged = grouped.filter((group) => !group.is_acknowledged);

        if (heading) {
            heading.innerHTML = `<b>${unacknowledged.length} Critical Values Unacknowledged!</b> Treating clinicians must acknowledge within 30 minutes as per policy.`;
        }

        if (!grouped.length) {
            container.innerHTML = '<div class="empty-state p-16"><div class="empty-title">No critical values found</div></div>';
            return;
        }

        container.innerHTML = grouped.map((group) => {
            const isAck = !!group.is_acknowledged;
            const isDoctorCalled = !!group.is_doctor_called;
            const itemId = Number(group?.item_id || 0);

            const rowsHtml = group.entries.map((entry) => `
                <div class="lab-critical-grid" style="margin-top:10px">
                    <div><span class="label">Test</span><span class="value">${escapeHtml(safeText(entry?.parameter_name))}</span></div>
                    <div><span class="label">Result</span><span class="value danger">${escapeHtml(safeText(entry?.result_value))}</span></div>
                    <div><span class="label">Normal Range</span><span class="value" style="font-weight:500">${escapeHtml(safeText(entry?.normal_range))}</span></div>
                </div>
                <div class="mt-8"><span class="lab-critical-flag">${escapeHtml(safeText(entry?.flag_label))}</span></div>
            `).join('');

            return `
            <div class="lab-critical-card" style="opacity:${isAck ? 0.62 : 1}">
                <div class="lab-critical-wrap">
                    <div class="lab-critical-main">
                        <div class="lab-critical-head">
                            <span style="font-size:24px">⚠️</span>
                            <div>
                                <div class="lab-critical-name">${escapeHtml(safeText(group?.patient_name))}</div>
                                <div class="lab-critical-sub">${escapeHtml(safeText(group?.sample_id))} | ${escapeHtml(safeText(group?.visit_no))}</div>
                            </div>
                        </div>
                        ${rowsHtml}
                        <div class="fs-12 text-muted mt-6">Assigned Doctor: <b>${escapeHtml(safeText(group?.doctor_name))}</b></div>
                    </div>
                    <div class="lab-critical-actions">
                        <button class="btn btn-danger btn-sm lab-call-doctor" data-item-id="${itemId}" data-doctor="${escapeHtml(safeText(group?.doctor_name))}" ${isDoctorCalled ? 'disabled' : ''}>📞 ${isDoctorCalled ? 'Doctor Notified' : 'Call Doctor'}</button>
                        <button class="btn btn-warning btn-sm lab-ack-critical" data-item-id="${itemId}" ${isAck ? 'disabled' : ''}>✅ ${isAck ? 'Acknowledged' : 'Acknowledge'}</button>
                        <button class="btn btn-secondary btn-xs lab-send-critical" data-item-id="${itemId}" data-print-url="${escapeHtml(safeText(group?.print_url, ''))}">📤 Send Report</button>
                    </div>
                </div>
            </div>
        `;
        }).join('');
    }

    function getCompletedItems(row) {
        const list = Array.isArray(row?.tests_ordered) ? row.tests_ordered : [];
        return list.filter((item) => String(item?.status_key || '').toLowerCase() === 'completed');
    }

    function normalizeReportFlagLabel(value) {
        const text = String(value || '').trim().toLowerCase();
        if (!text) {
            return '';
        }

        if (text.includes('critical') && text.includes('high')) {
            return 'Critical High';
        }

        if (text.includes('critical') && text.includes('low')) {
            return 'Critical Low';
        }

        if (text.includes('high')) {
            return 'High';
        }

        if (text.includes('low')) {
            return 'Low';
        }

        if (text.includes('normal')) {
            return 'Normal';
        }

        return '';
    }

    function getFlagArrow(value) {
        const normalized = normalizeReportFlagLabel(value);
        if (normalized === 'Critical High' || normalized === 'High') {
            return '↑';
        }

        if (normalized === 'Critical Low' || normalized === 'Low') {
            return '↓';
        }

        return '';
    }

    function formatReportValueSnippet(name, value, flagHint) {
        const resultValue = safeText(value, '').trim();
        const parameterName = safeText(name, '').trim();
        const hasArrowInValue = /[↑↓]/.test(resultValue);
        const arrow = hasArrowInValue ? '' : getFlagArrow(flagHint);

        if (parameterName && resultValue) {
            return `${parameterName} ${resultValue}${arrow ? ` ${arrow}` : ''}`;
        }

        if (resultValue) {
            return `${resultValue}${arrow ? ` ${arrow}` : ''}`;
        }

        if (parameterName) {
            return parameterName;
        }

        return '';
    }

    function getReportFlagLabels(row) {
        const labels = [];
        const pushLabel = (value) => {
            const normalized = normalizeReportFlagLabel(value);
            if (normalized && !labels.includes(normalized)) {
                labels.push(normalized);
            }
        };

        const criticalEntries = Array.isArray(row?.critical_today_entries) ? row.critical_today_entries : [];
        criticalEntries.forEach((entry) => {
            pushLabel(entry?.flag_label);
            pushLabel(entry?.result_flag);
        });

        const completedItems = getCompletedItems(row);
        completedItems.forEach((item) => {
            [
                item?.flag_label,
                item?.result_flag,
                item?.abnormal_flag,
                item?.flag,
                item?.interpretation,
                item?.result_summary,
            ].forEach(pushLabel);
        });

        [row?.report_summary, row?.report_text].forEach(pushLabel);

        return labels;
    }

    function getReportKeyResult(row) {
        const criticalEntries = Array.isArray(row?.critical_today_entries) ? row.critical_today_entries : [];
        const criticalValues = criticalEntries
            .map((entry) => formatReportValueSnippet(entry?.parameter_name, entry?.result_value, entry?.flag_label || entry?.result_flag))
            .filter(Boolean);

        if (criticalValues.length) {
            return criticalValues.join(', ');
        }

        const completedItems = getCompletedItems(row);
        const abnormalValues = completedItems
            .filter((item) => {
                const label = normalizeReportFlagLabel([
                    item?.flag_label,
                    item?.result_flag,
                    item?.abnormal_flag,
                    item?.flag,
                    item?.interpretation,
                    item?.result_summary,
                ].find(Boolean));

                return label === 'Critical High' || label === 'Critical Low' || label === 'High' || label === 'Low';
            })
            .map((item) => formatReportValueSnippet(
                item?.parameter_name || item?.test_name,
                item?.result_value || item?.result_summary,
                item?.flag_label || item?.result_flag || item?.abnormal_flag || item?.flag || item?.interpretation,
            ))
            .filter(Boolean);

        if (abnormalValues.length) {
            return abnormalValues.join(', ');
        }

        const summaryLine = [row?.report_summary, row?.report_text]
            .map((value) => String(value || '').trim())
            .filter(Boolean)
            .map((value) => value.split(/\r?\n/)[0])
            .find((value) => /[↑↓]/.test(value));

        if (summaryLine) {
            return summaryLine;
        }

        const labels = getReportFlagLabels(row);
        const basicLabels = labels.filter((label) => label === 'High' || label === 'Low');
        if (basicLabels.length) {
            return basicLabels.join(', ');
        }

        return 'Normal';
    }

    function getReportTime(value) {
        const text = String(value || '').trim();
        if (!text) {
            return '-';
        }

        const parts = text.split(' ');
        return parts[1] || text;
    }

    function getReportDate(value) {
        const text = String(value || '').trim();
        if (!text) {
            return new Date().toLocaleDateString('en-IN');
        }

        const parts = text.split(' ');
        if (parts[0]) {
            return parts[0];
        }

        return new Date().toLocaleDateString('en-IN');
    }

    function formatModalFlag(flag) {
        const normalized = normalizeReportFlagLabel(flag);
        if (normalized === 'Critical High') {
            return '↑↑ High';
        }
        if (normalized === 'Critical Low') {
            return '↓↓ Low';
        }
        if (normalized === 'High') {
            return '↑ High';
        }
        if (normalized === 'Low') {
            return '↓ Low';
        }
        return '—';
    }

    function getFlagColor(flagText) {
        const text = String(flagText || '').toLowerCase();
        if (text.includes('↑↑') || text.includes('↓↓') || text.includes('critical')) {
            return '#c62828';
        }
        if (text.includes('↑') || text.includes('↓') || text.includes('high') || text.includes('low')) {
            return '#ef6c00';
        }
        return '#203142';
    }

    function formatAgeSex(ageValue, sexValue) {
        const age = safeText(ageValue, '-');
        const sex = safeText(sexValue, '-');

        if (age === '-' && sex === '-') {
            return '-';
        }

        if (age === '-') {
            return sex;
        }

        return `${age}Y${sex !== '-' ? `/${sex}` : ''}`;
    }

    function buildReportModalHtml(row) {
        const completedItems = getCompletedItems(row);
        const keyResult = getReportKeyResult(row);
        const rows = completedItems.length ? completedItems : [{}];
        const sampleId = safeText(row?.sample_id || row?.order_no || '-');
        const patientName = safeText(row?.patient_name || '-');
        const patientMrn = safeText(row?.patient_mrn || '-');
        const testName = safeText(completedItems[0]?.test_name || row?.test_name || '-');
        const orderedAt = safeText(row?.ordered_at || '-');
        const ageSex = formatAgeSex(row?.age, row?.sex);
        const hospitalName = safeText(row?.hospital_name || 'District Hospital');
        const pathologistName = safeText(row?.pathologist_name || row?.ordered_by || 'Lab Pathologist');
        const verifiedDate = safeText(row?.verified_date || row?.ordered_at || '-');

        const tableRows = rows.map((item) => {
            const parameter = safeText(item?.parameter_name || item?.test_name || 'Result');
            const resultVal = safeText(item?.result_value || item?.result_summary || (completedItems.length ? '-' : keyResult));
            const unitVal = safeText(item?.unit_name || item?.unit || '-');
            const normalRange = safeText(item?.normal_range || '-');
            const flagText = formatModalFlag(item?.flag_label || item?.result_flag || item?.abnormal_flag || item?.flag || item?.interpretation);
            const resultColor = getFlagColor(flagText);
            const flagColor = getFlagColor(flagText);

            return `
                <tr>
                    <td style="border:1px solid #d4dbe3;padding:5px 7px">${escapeHtml(parameter)}</td>
                    <td style="border:1px solid #d4dbe3;padding:5px 7px;color:${resultColor};font-weight:${resultColor === '#203142' ? 500 : 700}">${escapeHtml(resultVal)}</td>
                    <td style="border:1px solid #d4dbe3;padding:5px 7px">${escapeHtml(unitVal)}</td>
                    <td style="border:1px solid #d4dbe3;padding:5px 7px">${escapeHtml(normalRange)}</td>
                    <td style="border:1px solid #d4dbe3;padding:5px 7px;color:${flagColor}">${escapeHtml(flagText)}</td>
                </tr>
            `;
        }).join('');

        const safeTableRows = tableRows || `
            <tr>
                <td style="border:1px solid #ccc;padding:5px" colspan="5">No parameters found</td>
            </tr>
        `;

        return `
            <div style="font-family:Arial,sans-serif;padding:0;color:#1d2b3a;text-align:left">
                <div style="text-align:center;border-bottom:2px solid #003580;padding-bottom:12px;margin-bottom:12px">
                    <div style="font-size:16px;font-weight:900;color:#003580">${escapeHtml(hospitalName)}</div>
                    <div style="font-size:12px;color:#5a7894">Pathology Department — Laboratory Report</div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px;font-size:12px">
                    <div><b>Patient:</b> ${escapeHtml(patientName)}</div><div><b>Age/Sex:</b> ${escapeHtml(ageSex)}</div>
                    <div><b>MRN:</b> ${escapeHtml(patientMrn)}</div><div><b>Sample ID:</b> ${escapeHtml(sampleId)}</div>
                    <div><b>Test:</b> ${escapeHtml(testName)}</div><div><b>Date:</b> ${escapeHtml(getReportDate(orderedAt))}</div>
                </div>

                <table style="width:100%;border-collapse:collapse;font-size:12px">
                    <thead>
                        <tr style="background:#f5f5f5">
                            <th style="border:1px solid #ccc;padding:6px">Parameter</th>
                            <th style="border:1px solid #ccc;padding:6px">Result</th>
                            <th style="border:1px solid #ccc;padding:6px">Unit</th>
                            <th style="border:1px solid #ccc;padding:6px">Normal Range</th>
                            <th style="border:1px solid #ccc;padding:6px">Flag</th>
                        </tr>
                    </thead>
                    <tbody>${safeTableRows}</tbody>
                </table>

                <div style="margin-top:12px;font-size:11px;color:#5a7894"><b>Pathologist:</b> ${escapeHtml(pathologistName)} | <b>Verified:</b> ${escapeHtml(verifiedDate)}</div>
            </div>
        `;
    }

    function buildReportModalHtmlFromData(data) {
        if (!data) {
            return '<div style="padding:20px;text-align:center;color:#999">No parameters found</div>';
        }

        const parameters = Array.isArray(data.parameters) ? data.parameters : [];
        const testName = safeText(data.test_name || '-');
        const patientName = safeText(data.patient_name || '-');
        const patientMrn = safeText(data.patient_mrn || '-');
        const sampleId = safeText(data.sample_id || '-');
        const orderedAt = safeText(data.ordered_at || '-');
        
        // Dynamic values from backend
        const hospitalName = safeText(data.hospital_name || 'District Hospital Dehradun');
        const pathologistName = safeText(data.pathologist_name || 'Lab Pathologist');
        const verifiedDate = safeText(data.verified_date || new Date().toLocaleDateString('en-IN'));
        
        const ageSexStr = formatAgeSex(data.age, data.sex);

        const tableRows = parameters.map((param) => {
            const paramName = safeText(param?.parameter_name || 'Unknown');
            const resultValue = safeText(param?.result_value || '-');
            const unitName = safeText(param?.unit_name || '-');
            const normalRange = safeText(param?.normal_range || '-');
            const flagLabel = safeText(param?.flag_label || 'Normal');
            
            const flagText = formatModalFlag(flagLabel);
            const resultColor = getFlagColor(flagText);
            const flagColor = getFlagColor(flagText);

            return `
                <tr>
                    <td style="border:1px solid #ccc;padding:5px">${escapeHtml(paramName)}</td>
                    <td style="border:1px solid #ccc;padding:5px;color:${resultColor};font-weight:${resultColor === '#203142' ? 500 : 700}">${escapeHtml(resultValue)}</td>
                    <td style="border:1px solid #ccc;padding:5px">${escapeHtml(unitName)}</td>
                    <td style="border:1px solid #ccc;padding:5px">${escapeHtml(normalRange)}</td>
                    <td style="border:1px solid #ccc;padding:5px;color:${flagColor}">${escapeHtml(flagText)}</td>
                </tr>
            `;
        }).join('');

        const safeTableRows = tableRows || `
            <tr>
                <td style="border:1px solid #ccc;padding:5px" colspan="5">No parameters found</td>
            </tr>
        `;

        return `
            <div style="font-family:Arial,sans-serif;padding:0;color:#1d2b3a;text-align:left">
                <div style="text-align:center;border-bottom:2px solid #003580;padding-bottom:12px;margin-bottom:12px">
                    <div style="font-size:16px;font-weight:900;color:#003580">${escapeHtml(hospitalName)}</div>
                    <div style="font-size:12px;color:#5a7894">Pathology Department — Laboratory Report</div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px;font-size:12px">
                    <div><b>Patient:</b> ${escapeHtml(patientName)}</div><div><b>Age/Sex:</b> ${escapeHtml(ageSexStr)}</div>
                    <div><b>MRN:</b> ${escapeHtml(patientMrn)}</div><div><b>Sample ID:</b> ${escapeHtml(sampleId)}</div>
                    <div><b>Test:</b> ${escapeHtml(testName)}</div><div><b>Date:</b> ${escapeHtml(getReportDate(orderedAt))}</div>
                </div>

                <table style="width:100%;border-collapse:collapse;font-size:12px">
                    <thead>
                        <tr style="background:#f5f5f5">
                            <th style="border:1px solid #ccc;padding:6px">Parameter</th>
                            <th style="border:1px solid #ccc;padding:6px">Result</th>
                            <th style="border:1px solid #ccc;padding:6px">Unit</th>
                            <th style="border:1px solid #ccc;padding:6px">Normal Range</th>
                            <th style="border:1px solid #ccc;padding:6px">Flag</th>
                        </tr>
                    </thead>
                    <tbody>${safeTableRows}</tbody>
                </table>

                <div style="margin-top:12px;font-size:11px;color:#5a7894"><b>Pathologist:</b> ${escapeHtml(pathologistName)} | <b>Verified:</b> ${escapeHtml(verifiedDate)}</div>
            </div>
        `;
    }

    function initReportsTable() {
        const reportsTableNode = $('#lab-reports-table');
        if (!reportsTableNode.length) {
            return;
        }

        if ($.fn.DataTable.isDataTable(reportsTableNode)) {
            state.reportsTable = reportsTableNode.DataTable();
            return;
        }

        state.reportsTable = reportsTableNode.DataTable({
            processing: true,
            serverSide: true,
            paging: true,
            info: true,
            lengthChange: true,
            scrollX: true,
            ajax: {
                url: route('loadtable'),
                type: 'POST',
                data: function (d) {
                    const reportDate = normalizeReportDateFilter($('#labReportDate').val());
                    d._token = getCsrfTokenSync();
                    d.item_wise = 1;
                    d.status = 'completed';
                    d.category = '';
                    d.search_text = $('#labSearch').val() || '';
                    d.date_from = reportDate;
                    d.date_to = reportDate;
                },
            },
            columns: [
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return `<span class="lab-report-sample">${escapeHtml(safeText(row?.sample_id))}</span>`;
                    },
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return `<span class="lab-report-patient">${escapeHtml(safeText(row?.patient_name))}</span>`;
                    },
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        const completedItems = getCompletedItems(row);
                        const tests = completedItems.map((item) => item?.test_name).filter(Boolean).join(', ') || '-';
                        return `<span class="lab-report-tests">${tests}</span>`;
                    },
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return `<span class="lab-report-key">${escapeHtml(getReportKeyResult(row))}</span>`;
                    },
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return `<span class="lab-report-time">${escapeHtml(getReportTime(row?.ordered_at))}</span>`;
                    },
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function () {
                        const label = 'dispatched';
                        return `<span class="lab-report-status lab-report-status-dispatched">${label}</span>`;
                    },
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        const completedItems = getCompletedItems(row);
                        const printUrl = completedItems[0]?.print_url || '';
                        if (!printUrl) {
                            return '-';
                        }
                        // <button type="button" class="btn btn-success btn-xs lab-report-send" data-print-url="${escapeHtml(printUrl)}">📤</button>
                        return `
                            <div class="lab-report-actions">
                                <button type="button" class="btn btn-primary btn-xs lab-report-view" data-print-url="${escapeHtml(printUrl)}" data-row-id="${Number(row?.id || 0)}" data-sample-id="${escapeHtml(safeText(row?.sample_id, ''))}">👁 View</button>
                                <button type="button" class="btn btn-secondary btn-xs lab-report-print" data-print-url="${escapeHtml(printUrl)}">🖨</button>
                               
                            </div>
                        `;
                    },
                },
            ],
            dom: 'rtip',
            searching: false,
            ordering: false,
            responsive: true,
            language: {
                emptyTable: 'No completed reports available',
            },
        });
    }

    function renderReports() {
        if (!state.reportsTable) {
            return;
        }

        state.reportsTable.ajax.reload(null, false);
    }

    function getCategoryFromRow(row) {
        const list = Array.isArray(row?.category_list) ? row.category_list : [];
        const category = String(list[0] || row?.category || '').trim();
        return category || 'Uncategorized';
    }

    function getChartPalette(index) {
        const palette = ['#1565c0', '#00897b', '#c62828', '#7b1fa2', '#f57c00', '#5d4037', '#006064'];
        return palette[index % palette.length];
    }

    async function fetchTatAnalyticsFromBackend() {
        const url = route('tatAnalytics');
        return $.ajax({
            type: 'GET',
            url: url,
            dataType: 'json',
            data: {
                category: $('#filter-category').val() || '',
                search_text: $('#labSearch').val() || '',
                date_from: $('#filter-date-from').val() || '',
                date_to: $('#filter-date-to').val() || '',
            },
            timeout: 15000,
        });
    }

    async function fetchAndRenderTatAnalytics() {
        try {
            const response = await fetchTatAnalyticsFromBackend();
            if (!response?.success) {
                renderCharts({ tat_by_department: [], daily_volume: [], analyzer_performance: [] });
                sendmsg('error', response?.message || 'Unable to load TAT analytics data.');
                return;
            }

            renderCharts(response.data || {});
        } catch (error) {
            console.error('TAT analytics fetch error:', error);
            renderCharts({ tat_by_department: [], daily_volume: [], analyzer_performance: [] });
            sendmsg('error', 'Unable to load TAT analytics data.');
        }
    }

    async function fetchAnalyzerConfigFromBackend() {
        const url = route('analyzerConfig');
        return $.ajax({
            type: 'GET',
            url: url,
            dataType: 'json',
            data: {
                category: $('#filter-category').val() || '',
                search_text: $('#labSearch').val() || '',
                date_from: $('#filter-date-from').val() || '',
                date_to: $('#filter-date-to').val() || '',
            },
            timeout: 15000,
        });
    }

    function renderAnalyzerConfigCards(analyzers) {
        const node = document.getElementById('analyzerList');
        if (!node) {
            return;
        }

        const list = Array.isArray(analyzers) ? analyzers : [];
        if (!list.length) {
            node.innerHTML = '<div class="card"><div class="card-body"><div class="text-muted fs-12">No analyzer configuration data available.</div></div></div>';
            return;
        }

        node.innerHTML = `
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:12px">
              ${list.map((a) => `
                <div style="background:var(--surface);border:1.5px solid ${a.status === 'online' ? 'rgba(46,125,50,.3)' : 'rgba(245,124,0,.3)'};border-radius:12px;padding:14px">
                  <div class="d-flex align-center gap-8 mb-8">
                    <span style="font-size:20px">🔬</span>
                    <div><div class="fw-700 fs-13">${escapeHtml(safeText(a.name, '-'))}</div></div>
                  </div>
                  <div class="fs-12 text-muted mb-4">Tests: ${escapeHtml(safeText(a.tests, '-'))}</div>
                  <div class="fs-12 text-muted mb-4">Last Calibration: <b>${escapeHtml(safeText(a.last_calibration, '-'))}</b></div>
                  <div class="fs-12 text-muted mb-8">Samples Today: <b>${escapeHtml(safeText(a.samples, '0 today'))}</b></div>
                  <span class="badge badge-${a.status === 'online' ? 'green' : 'orange'}">${a.status === 'online' ? '● Online' : '🔧 Maintenance'}</span>
                </div>`).join('')}
            </div>
        `;
    }

    async function fetchAndRenderAnalyzerConfig() {
        try {
            const response = await fetchAnalyzerConfigFromBackend();
            if (!response?.success) {
                renderAnalyzerConfigCards([]);
                sendmsg('error', response?.message || 'Unable to load analyzer config.');
                return;
            }

            renderAnalyzerConfigCards(response?.data?.analyzers || []);
        } catch (error) {
            console.error('Analyzer config fetch error:', error);
            renderAnalyzerConfigCards([]);
            sendmsg('error', 'Unable to load analyzer config.');
        }
    }

    function renderAnalyzerPerformance(entries) {
        const perfNode = document.getElementById('analyzerPerf');
        if (!perfNode) {
            return;
        }

        const rowList = Array.isArray(entries) ? entries : [];
        if (!rowList.length) {
            perfNode.innerHTML = `
                <div class="card-body">
                    <div class="text-muted fs-12">No analyzer performance data available for selected filters.</div>
                </div>
            `;
            return;
        }

        const rowsHtml = rowList
            .map((entry) => {
                return `
                    <tr>
                        <td>${escapeHtml(safeText(entry?.analyzer))}</td>
                        <td>${escapeHtml(safeText(entry?.uptime))}</td>
                        <td>${escapeHtml(safeText(entry?.samples_per_day))}</td>
                        <td>${escapeHtml(safeText(entry?.error_rate))}</td>
                        <td>${escapeHtml(safeText(entry?.calibration))}</td>
                        <td><span class="badge badge-${escapeHtml(safeText(entry?.status_color, 'green'))}">${escapeHtml(safeText(entry?.status, 'OK'))}</span></td>
                    </tr>
                `;
            })
            .join('');

        perfNode.innerHTML = `
            <div class="card-body">
                <div class="table-wrap">
                    <table class="hims-table">
                        <thead>
                            <tr>
                                <th>Analyzer</th>
                                <th>Uptime</th>
                                <th>Samples/Day</th>
                                <th>Error Rate</th>
                                <th>Calibration</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>${rowsHtml}</tbody>
                    </table>
                </div>
            </div>
        `;
    }

    function renderCharts(analyticsData) {
        if (typeof Chart === 'undefined') {
            return;
        }

        const tatEntries = Array.isArray(analyticsData?.tat_by_department) ? analyticsData.tat_by_department : [];
        const dailyVolume = Array.isArray(analyticsData?.daily_volume) ? analyticsData.daily_volume : [];
        const analyzerPerformance = Array.isArray(analyticsData?.analyzer_performance) ? analyticsData.analyzer_performance : [];

        if (!tatEntries.length && !dailyVolume.length) {
            if (state.charts.tat) state.charts.tat.destroy();
            if (state.charts.volume) state.charts.volume.destroy();
            renderAnalyzerPerformance(analyzerPerformance);
            return;
        }

        const dayLabels = dailyVolume.map((entry) => safeText(entry?.label));
        const dayData = dailyVolume.map((entry) => Number(entry?.count || 0));

        const tatCanvas = document.getElementById('labTatChart');
        const volumeCanvas = document.getElementById('labVolumeChart');

        if (state.charts.tat) state.charts.tat.destroy();
        if (state.charts.volume) state.charts.volume.destroy();

        if (tatCanvas) {
            state.charts.tat = new Chart(tatCanvas, {
                type: 'bar',
                data: {
                    labels: tatEntries.map((entry) => safeText(entry?.label)),
                    datasets: [{
                        label: 'Avg TAT (hours)',
                        data: tatEntries.map((entry) => Number(entry?.avg_hours || 0)),
                        backgroundColor: tatEntries.map((_, index) => getChartPalette(index)),
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    return `${value}h`;
                                },
                            },
                        },
                    },
                },
            });
        }

        if (volumeCanvas) {
            state.charts.volume = new Chart(volumeCanvas, {
                type: 'line',
                data: {
                    labels: dayLabels,
                    datasets: [{
                        label: 'Tests Processed',
                        data: dayData,
                        borderColor: '#1565c0',
                        backgroundColor: 'rgba(21,101,192,.08)',
                        fill: true,
                        tension: 0.4,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 },
                        },
                    },
                },
            });
        }

        renderAnalyzerPerformance(analyzerPerformance);
    }

    async function refreshDashboardFromSnapshot() {
        setKpiLoading(true);
        try {
            const [rows, urgentRows] = await Promise.all([
                fetchSnapshotRows(),
                fetchUrgentRows(),
            ]);

            state.snapshotRows = rows;
            state.urgentRows = urgentRows;
            syncCategoryOptions(rows);
            updateTabAndKpis();
            renderUrgentList();
            state.resultEntrySelection = null;
            const matchNode = document.getElementById('labResultMatchList');
            if (matchNode) {
                matchNode.innerHTML = '';
            }
            renderSelectedResultEntryCard();
            renderCriticalList();
            renderReports();
            if (!document.getElementById('tatPane')?.hidden) {
                await fetchAndRenderTatAnalytics();
            }
            if (!document.getElementById('analyzerPane')?.hidden) {
                await fetchAndRenderAnalyzerConfig();
            }
        } catch (xhr) {
            sendmsg('error', xhr?.responseJSON?.message || 'Unable to load lab dashboard data.');
        } finally {
            setKpiLoading(false);
        }
    }

    function scheduleDashboardRefresh(delay = 300) {
        const waitMs = Math.max(0, Number(delay) || 0);

        if (state.refreshDebounceTimer) {
            clearTimeout(state.refreshDebounceTimer);
        }

        state.refreshDebounceTimer = setTimeout(async function () {
            state.refreshDebounceTimer = null;

            if (state.refreshInFlight) {
                state.refreshQueued = true;
                return;
            }

            state.refreshInFlight = true;
            try {
                await refreshDashboardFromSnapshot();
            } finally {
                state.refreshInFlight = false;
                if (state.refreshQueued) {
                    state.refreshQueued = false;
                    scheduleDashboardRefresh(80);
                }
            }
        }, waitMs);
    }

    state.table = $('#xin-table').DataTable({
        processing: true,
        serverSide: true,
        paging: true,
        info: true,
        lengthChange: true,
        scrollX: true,
        ajax: {
            url: route('loadtable'),
            type: 'POST',
            data: function (d) {
                d._token = window.Laravel.csrfToken;
                d.item_wise = 1;
                d.status = $('#filter-status').val();
                d.category = $('#filter-category').val();
                d.search_text = $('#labSearch').val();
                d.date_from = $('#filter-date-from').val();
                d.date_to = $('#filter-date-to').val();
                d.exclude_completed_queue = 1;
            },
        },
        columns: [
            { data: null, orderable: false, searchable: false, render: function (data, type, row) { return renderSampleCell(row); } },
            { data: null, orderable: false, searchable: false, render: function (data, type, row) { return renderPatientCell(row); } },
            { data: 'tests_ordered', orderable: false, searchable: false, render: function (data) { return renderTestsOrderedCell(data); } },
            { data: null, orderable: false, searchable: false, render: function (data, type, row) { return renderCategoryCell(row); } },
            { data: 'priority', orderable: false, searchable: false, render: function (data) { return renderPriorityTag(data); } },
            { data: 'ordered_by', orderable: false, searchable: false, render: function (data) { return `<span class="lab-mini-title">${escapeHtml(safeText(data))}</span>`; } },
            { data: 'collected', orderable: false, searchable: false, render: function (data) { return `<span class="lab-inline-note">${escapeHtml(safeText(data))}</span>`; } },
            { data: null, orderable: false, searchable: false, render: function (data, type, row) { return renderStatusCell(row); } },
            { data: 'tests_ordered', orderable: false, searchable: false, render: function (data) { return renderActionsCell(data); } },
        ],
        dom: 'rtip',
        searching: false,
        ordering: false,
        responsive: true,
    });

    initializeDatePicker();
    initializeTooltips();

    state.table.on('draw.dt responsive-display.dt column-visibility.dt', function () {
        initializeTooltips();
        scheduleDashboardRefresh(200);
    });

    $('#filter-status, #filter-category, #filter-date-from, #filter-date-to').on('change', function () {
        state.table.ajax.reload();
    });

    $('#labSearch').on('input', function () {
        if (state.searchTimer) {
            clearTimeout(state.searchTimer);
        }
        state.searchTimer = setTimeout(function () {
            state.table.ajax.reload();
        }, 250);
    });

    $('#clear-filters').on('click', function () {
        $('#labSearch').val('');
        $('#filter-category').val('');
        $('#filter-status').val('');
        $('#filter-date-from').val('');
        $('#filter-date-to').val('');
        state.table.ajax.reload();
    });

    $('#labResultSearchBtn').on('click', function () {
        handleResultEntryMatches($('#labResultSearch').val());
    });

    $('#labResultSearch').on('input', function () {
        state.resultEntrySelection = null;
        const matchNode = document.getElementById('labResultMatchList');
        if (matchNode) {
            matchNode.innerHTML = '';
        }
        renderSelectedResultEntryCard();
    });

    $('#labResultSearch').on('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            handleResultEntryMatches($('#labResultSearch').val());
        }
    });

    $(document).on('click', '[data-result-row-id]', function () {
        const rowId = Number($(this).data('result-row-id'));
        const found = (state.snapshotRows || []).find((row) => Number(row?.id || 0) === rowId);
        if (!found) return;
        state.resultEntrySelection = found;
        renderResultEntryMatches();
    });

    $(document).on('click', '#labLoadSelectedSampleBtn', function () {
        loadSelectedResultEntry();
    });

    $('#labReportFilterBtn').on('click', function () {
        if (!state.reportsTable) {
            initReportsTable();
        }

        state.reportsTable.ajax.reload();
    });

    $('#labReportDate').on('keydown', function (e) {
        if (e.key !== 'Enter') {
            return;
        }

        e.preventDefault();
        $('#labReportFilterBtn').trigger('click');
    });

    $('#labRefreshReports').on('click', function () {
        renderReports();
    });

    $(document).on('click', '.lab-report-view', async function () {
        const button = $(this);
        const itemId = Number(button.data('row-id') || 0);
        const sampleId = String(button.data('sample-id') || '').trim();

        if (!itemId && !sampleId) {
            sendmsg('error', 'Unable to identify the report.');
            return;
        }

        // Fetch parameters via AJAX using route helper (no loading modal)
        const parameterUrl = route('itemParameters', { item: itemId });
        
        try {
            const response = await $.ajax({
                type: 'GET',
                url: parameterUrl,
                dataType: 'json',
                timeout: 10000,
            });

            if (!response.success) {
                sendmsg('error', response.message || 'Failed to load report parameters.');
                return;
            }

            // Build and display modal directly with fetched data
            const modalHtml = buildReportModalHtmlFromData(response.data);
            
            await Swal.fire({
                title: `📄 Lab Report — ${safeText(response.data?.sample_id || sampleId)}`,
                html: modalHtml,
                width: 900,
                showCloseButton: true,
                showConfirmButton: false,
                customClass: {
                    popup: 'lab-report-preview-popup',
                    title: 'lab-report-preview-title',
                },
            });
        } catch (error) {
            console.error('Error fetching parameters:', error);
            sendmsg('error', 'Failed to load report. Please try again.');
        }
    });

    $(document).on('click', '.lab-report-print', function () {
        const printUrl = String($(this).data('print-url') || '').trim();
        if (!printUrl) {
            sendmsg('error', 'Unable to open this report.');
            return;
        }

        window.open(printUrl, '_blank');
    });

    $(document).on('click', '.lab-report-send', function () {
        const printUrl = String($(this).data('print-url') || '').trim();
        if (!printUrl) {
            sendmsg('error', 'Unable to dispatch this report.');
            return;
        }

        sendmsg('success', 'Report sent to doctor portal successfully.');
    });

    $(document).on('click', '.update-item-status', async function () {
        const url = $(this).data('url');
        const confirm = await Swal.fire({
            title: 'Move to next status?',
            text: 'Are you sure you want to move this item to the next status.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, move',
            cancelButtonText: 'Cancel',
        });

        if (!confirm.isConfirmed) {
            return;
        }

        const token = await csrftoken();

        loader('show');
        $.post(url, { _token: token }, function (response) {
            loader('hide');
            state.table.ajax.reload(null, false);
            scheduleDashboardRefresh(80);
            sendmsg('success', response.message || 'Status updated successfully.');
        }).fail(function (xhr) {
            loader('hide');
            sendmsg('error', xhr?.responseJSON?.message || 'Unable to update status.');
        });
    });

    $(document).on('click', '.open-report-form', async function () {
        const id = $(this).data('id');
        openReportFormById(id);
    });

    $(document).on('click', '.lab-call-doctor', async function () {
        const $btn = $(this);
        const itemId = Number($btn.data('item-id') || 0);
        const doctor = $btn.data('doctor') || 'assigned doctor';

        if (!itemId) {
            sendmsg('error', 'Unable to identify critical report item.');
            return;
        }

        const token = await csrftoken();
        $btn.prop('disabled', true);
        loader('show');

        $.post(route('criticalCall', { item: itemId }), { _token: token }, async function (response) {
            sendmsg('success', response?.message || `Doctor ${doctor} has been notified.`);
            scheduleDashboardRefresh(80);
        }).fail(function (xhr) {
            sendmsg('error', xhr?.responseJSON?.message || 'Unable to notify doctor for this critical report.');
            $btn.prop('disabled', false);
        }).always(function () {
            loader('hide');
        });
    });

    $(document).on('click', '.lab-ack-critical', async function () {
        const $btn = $(this);
        const itemId = Number($btn.data('item-id') || 0);

        if (!itemId) {
            sendmsg('error', 'Unable to acknowledge this critical value.');
            return;
        }

        const token = await csrftoken();
        $btn.prop('disabled', true);
        loader('show');

        $.post(route('criticalAcknowledge', { item: itemId }), { _token: token }, async function (response) {
            sendmsg('success', response?.message || 'Critical report acknowledged.');
            scheduleDashboardRefresh(80);
        }).fail(function (xhr) {
            sendmsg('error', xhr?.responseJSON?.message || 'Unable to acknowledge this critical value.');
            $btn.prop('disabled', false);
        }).always(function () {
            loader('hide');
        });
    });

    $(document).on('click', '.lab-send-critical', function () {
        const printUrl = String($(this).data('print-url') || '').trim();
        if (printUrl) {
            window.open(printUrl, '_blank');
            sendmsg('success', 'Critical report opened for dispatch/print.');
            return;
        }

        const itemId = Number($(this).data('item-id') || 0);
        if (itemId) {
            openReportFormById(itemId);
            return;
        }

        sendmsg('error', 'Unable to send report for this critical entry.');
    });

    $('.add-datamodal').on('hidden.bs.modal', function () {
        state.resultEntryModalOpen = false;
        state.resultEntryLoading = false;
        const loadBtn = document.getElementById('labResultSearchBtn');
        if (loadBtn) {
            loadBtn.disabled = false;
            loadBtn.textContent = 'Load Sample';
        }
    });

    $(document).on('click', '.open-charge-payment-form', async function () {
        const url = $(this).data('url');
        const chargeId = $(this).data('charge-id');
        const title = $(this).data('title');
        const contextNote = $(this).data('context-note');
        const token = await csrftoken();

        loader('show');
        $.post(url, {
            _token: token,
            charge_ids: [chargeId],
            title: title,
            context_note: contextNote,
        }, function (response) {
            loader('hide');
            $('#ajaxdata').html(response);
            $('.add-datamodal .modal-dialog').addClass('modal-xl');
            $('.add-datamodal').modal('show');
        }).fail(function (xhr) {
            loader('hide');
            sendmsg('error', xhr?.responseJSON?.message || 'Unable to load payment form.');
        });
    });

    $(document).on('submit', '#saveReportForm', async function (e) {
        e.preventDefault();
        loader('show');
        $('.err').remove();
        const token = await csrftoken();
        const $form = $(this);
        const id = $form.find('input[name="item_id"]').val();

        $.ajax({
            url: route('save', { item: id }),
            type: 'POST',
            data: $form.serialize() + '&_token=' + encodeURIComponent(token),
            success: function (response) {
                loader('hide');
                $('.add-datamodal').modal('hide');
                state.table.ajax.reload(null, false);
                scheduleDashboardRefresh(80);
                sendmsg('success', response.message || 'Saved successfully.');
            },
            error: function (xhr) {
                loader('hide');
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    const errors = xhr.responseJSON.errors;
                    const errorMessages = [];
                    for (const field in errors) {
                        errorMessages.push(errors[field].message);
                    }
                    if (errorMessages.length) {
                        sendmsg('error', errorMessages.join('<br>'));
                    }
                } else {
                    sendmsg('error', xhr?.responseJSON?.message || 'Unable to save report.');
                }
            },
        });
    });

    $(document).on('submit', '#collectPaymentForm', async function (e) {
        e.preventDefault();
        loader('show');
        $('.err').remove();
        const token = await csrftoken();
        const $form = $(this);
        const submitUrl = $form.data('submit-url');

        $.ajax({
            url: submitUrl,
            type: 'POST',
            data: $form.serialize() + '&_token=' + encodeURIComponent(token),
            success: function (response) {
                loader('hide');
                $('.add-datamodal').modal('hide');
                state.table.ajax.reload(null, false);
                scheduleDashboardRefresh(80);
                sendmsg('success', response.message || 'Payment collected successfully.');
            },
            error: function (xhr) {
                loader('hide');
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    const errors = xhr.responseJSON.errors;
                    const errorMessages = [];
                    for (const field in errors) {
                        errorMessages.push(errors[field].message);
                    }
                    if (errorMessages.length) {
                        sendmsg('error', errorMessages.join('<br>'));
                    }
                } else {
                    sendmsg('error', xhr?.responseJSON?.message || 'Unable to collect payment.');
                }
            },
        });
    });

    $(document).on('change', '#is_advance', function () {
        const $amount = $('#collectPaymentForm').find('input[name="amount"]');
        if (!$amount.length) {
            return;
        }

        if ($(this).is(':checked')) {
            if (typeof $amount.attr('max') !== 'undefined') {
                $amount.data('pending-max', $amount.attr('max'));
            }
            $amount.removeAttr('max');
        } else {
            const pendingMax = $amount.data('pending-max');
            if (pendingMax) {
                $amount.attr('max', pendingMax);
            }
        }
    });

    switchLabTabInternal('sampleQueuePane', document.querySelector('#labTabBar .tab-btn[data-lab-tab-target="sampleQueuePane"]'));
    scheduleDashboardRefresh(0);
});
