/* global Chart, Swal, flatpickr, route, loader, csrftoken, sendmsg */
(function () {
    'use strict';

    let worklistTable = null;
    let completedTable = null;
    let radRisReportStatusNorm = '';
    let modalityChart = null;
    let examsChart = null;
    let tatChart = null;
    let sourceChart = null;
    let aiPieChart = null;
    let weekOffset = 0;
    let worklistSearchTimer = null;
    let risFilterToolbarLayoutTimer = null;
    let risFilterToolbarResizeTimer = null;
    let radRisReportingCtx = { itemId: null, orderNo: '', patientId: '', patient: '', study: '' };

    function getPacsViewerTemplate() {
        const scope = document.querySelector('.rad-ris-scope');
        return scope ? String(scope.getAttribute('data-pacs-viewer-template') || '').trim() : '';
    }

    /** POST URL for radiology worklist save (RIS + legacy modal). */
    function getRisSaveUrl(itemId) {
        const id = String(itemId ?? '').trim();
        if (!id) {
            return '';
        }
        if (typeof window.route === 'function' && window.Routes && Object.prototype.hasOwnProperty.call(window.Routes, 'save') && window.Routes.save) {
            let u = window.route('save', { item: id });
            u = u ? String(u) : '';
            if (u.indexOf('__ITEM__') !== -1) {
                u = u.split('__ITEM__').join(encodeURIComponent(id));
            }
            if (u) {
                return u;
            }
        }
        const scope = document.querySelector('.rad-ris-scope');
        const tpl = scope ? String(scope.getAttribute('data-worklist-save-url') || '').trim() : '';
        if (tpl) {
            return tpl.split('__ITEM__').join(encodeURIComponent(id));
        }
        return '';
    }

    function buildPacsViewerUrl(tpl, ctx) {
        const orderNo = ctx.orderNo || '';
        const patientId = ctx.patientId || '';
        return tpl
            .replace(/\{accession\}/g, encodeURIComponent(orderNo))
            .replace(/\{order_no\}/g, encodeURIComponent(orderNo))
            .replace(/\{patient_id\}/g, encodeURIComponent(patientId));
    }

    function applyRadRisReportingSelection(itemId, meta) {
        radRisReportingCtx = {
            itemId: itemId || null,
            orderNo: meta && meta.orderNo ? String(meta.orderNo) : '',
            patientId: meta && meta.patientId ? String(meta.patientId) : '',
            patient: meta && meta.patient ? String(meta.patient) : '',
            study: meta && meta.study ? String(meta.study) : ''
        };
        const set = function (id, text) {
            const n = document.getElementById(id);
            if (n) {
                n.textContent = text;
            }
        };
        set('rad-ris-rpt-patient', radRisReportingCtx.patient || '—');
        set('rad-ris-rpt-accession', radRisReportingCtx.orderNo || '—');
        set('rad-ris-rpt-study', radRisReportingCtx.study || '—');
        const pill = document.getElementById('rad-ris-reporting-status-pill');
        if (pill) {
            pill.textContent = itemId ? 'Selected' : 'None';
            pill.className = 'rad-ris-badge ' + (itemId ? 'rad-ris-badge-blue' : 'rad-ris-badge-gray');
        }
        const openSel = document.getElementById('rad-ris-reporting-open-selected');
        if (openSel) {
            openSel.disabled = !itemId;
        }
        const dicomBtn = document.getElementById('rad-ris-dicom-open');
        if (dicomBtn) {
            dicomBtn.disabled = !itemId || !getPacsViewerTemplate();
        }
        const printA = document.getElementById('rad-ris-reporting-print-selected');
        if (printA) {
            if (itemId) {
                printA.href = route('print', { item: itemId });
                printA.style.pointerEvents = '';
                printA.style.opacity = '1';
                printA.setAttribute('aria-disabled', 'false');
                printA.classList.remove('rad-ris-is-disabled');
            } else {
                printA.href = 'javascript:void(0)';
                printA.style.pointerEvents = 'none';
                printA.style.opacity = '0.5';
                printA.setAttribute('aria-disabled', 'true');
                printA.classList.add('rad-ris-is-disabled');
            }
        }
    }

    function syncRadRisCkEditorsToTextareas() {
        if (typeof CKEDITOR === 'undefined') {
            return;
        }
        ['rad-ris-rpt-clinical', 'rad-ris-rpt-findings', 'rad-ris-rpt-impression'].forEach(function (id) {
            const inst = CKEDITOR.instances[id];
            if (inst) {
                inst.updateElement();
            }
        });
    }

    function ensureRadRisCkEditors() {
        if (typeof CKEDITOR === 'undefined') {
            return false;
        }
        const ids = ['rad-ris-rpt-clinical', 'rad-ris-rpt-findings', 'rad-ris-rpt-impression'];
        let created = false;
        ids.forEach(function (id) {
            if (!document.getElementById(id)) {
                return;
            }
            if (!CKEDITOR.instances[id]) {
                CKEDITOR.replace(id, {
                    height: 150,
                    resize_enabled: true,
                    removePlugins: 'elementspath',
                    toolbar: [
                        { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', '-', 'RemoveFormat'] },
                        { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent'] },
                        { name: 'styles', items: ['Format'] },
                        { name: 'insert', items: ['Table', 'HorizontalRule'] },
                        { name: 'tools', items: ['Maximize', 'Source'] }
                    ],
                    format_tags: 'p;h2;h3;h4',
                    entities: false,
                    enterMode: CKEDITOR.ENTER_P,
                    shiftEnterMode: CKEDITOR.ENTER_BR
                });
                created = true;
            }
        });
        return created;
    }

    function setRadRisRichField(id, html) {
        ensureRadRisCkEditors();
        const v = html || '';
        if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances[id]) {
            CKEDITOR.instances[id].setData(v);
        } else {
            const el = document.getElementById(id);
            if (el) {
                el.value = v;
            }
        }
    }

    function fillRadRisReportForm(data) {
        radRisReportStatusNorm = data.status_norm || '';
        document.getElementById('rad-ris-rpt-item-id').value = data.id || '';
        document.getElementById('rad-ris-rpt-patient').textContent = data.patient || '—';
        document.getElementById('rad-ris-rpt-accession').textContent = data.accession || '—';
        document.getElementById('rad-ris-rpt-study').textContent = data.study || '—';
        const ref = document.getElementById('rad-ris-rpt-referred');
        if (ref) {
            ref.textContent = data.referred_by || '—';
        }
        const badge = document.getElementById('rad-ris-rpt-status-badge');
        if (badge) {
            badge.textContent = data.status_label || '—';
            badge.className =
                'rad-ris-badge ' + (radRisReportStatusNorm === 'completed' ? 'rad-ris-badge-green' : 'rad-ris-badge-orange');
        }
        setRadRisRichField('rad-ris-rpt-clinical', data.clinical_indication || '');
        document.getElementById('rad-ris-rpt-technique').value = data.report_technique || '';
        setRadRisRichField('rad-ris-rpt-findings', data.report_text || '');
        setRadRisRichField('rad-ris-rpt-impression', data.report_impression || '');
        document.getElementById('rad-ris-rpt-summary').value = data.report_summary || '';
        document.getElementById('rad-ris-rpt-category').value = data.report_category || 'Normal';
        const radSel = document.getElementById('rad-ris-rpt-radiologist');
        if (radSel) {
            const cur = data.report_radiologist_id;
            radSel.innerHTML = '<option value="">— Select —</option>';
            (data.radiologists || []).forEach(function (r) {
                const o = document.createElement('option');
                o.value = r.id;
                o.textContent = r.name;
                radSel.appendChild(o);
            });
            if (cur) {
                radSel.value = String(cur);
            }
        }
        const addWrap = document.getElementById('rad-ris-rpt-addendum-wrap');
        const addBtn = document.getElementById('radRisRptAddendumBtn');
        if (radRisReportStatusNorm === 'completed') {
            addWrap?.classList.remove('d-none');
            addBtn?.classList.remove('d-none');
        } else {
            addWrap?.classList.add('d-none');
            addBtn?.classList.add('d-none');
        }
        document.getElementById('rad-ris-rpt-addendum').value = '';
        document.getElementById('radRisRptSaveAction').value = 'save';
    }

    function loadRadRisReportPanel(itemId, meta) {
        if (!itemId) {
            return;
        }
        switchTab('reporting');
        if (meta) {
            applyRadRisReportingSelection(itemId, meta);
        }
        const url = route('reportItem', { item: itemId });
        fetch(url, { headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) {
                return r.json();
            })
            .then(function (data) {
                fillRadRisReportForm(data);
                const dbtn = document.getElementById('rad-ris-dicom-open');
                if (dbtn) {
                    dbtn.disabled = !getPacsViewerTemplate();
                }
            })
            .catch(function () {
                radRisToast('Unable to load report data.', 'warning');
            });
    }

    function bindReportingPanel() {
        document.getElementById('rad-ris-dicom-open')?.addEventListener('click', function () {
            const tpl = getPacsViewerTemplate();
            if (!tpl || !radRisReportingCtx.itemId) {
                radRisToast('Configure PACS URL in .env or select a study from the queue.', 'warning');
                return;
            }
            const url = buildPacsViewerUrl(tpl, radRisReportingCtx);
            if (!url || url === tpl) {
                radRisToast('PACS URL template is invalid.', 'warning');
                return;
            }
            window.open(url, '_blank', 'noopener,noreferrer');
        });
    }

    /**
     * Select2 sets a pixel width on .select2-container after init; DataTables / layout
     * shifts then widen the widgets and break the one-line filter grid. Re-apply fluid width.
     */
    function syncRisWorklistFilterToolbar() {
        const row = document.querySelector('#rad-ris-panel-worklist .rad-ris-worklist-filters-row--controls');
        if (!row) {
            return;
        }
        row.querySelectorAll('.select2-container').forEach(function (el) {
            el.style.setProperty('width', '100%', 'important');
            el.style.setProperty('min-width', '0', 'important');
            el.style.setProperty('max-width', '100%', 'important');
        });
    }

    function scheduleRisWorklistFilterToolbarSync() {
        clearTimeout(risFilterToolbarLayoutTimer);
        risFilterToolbarLayoutTimer = setTimeout(function () {
            syncRisWorklistFilterToolbar();
        }, 0);
    }

    function radRisToast(msg, type) {
        type = type || 'info';
        const c = document.getElementById('rad-ris-toast-container');
        if (!c) {
            return;
        }
        const t = document.createElement('div');
        t.className = 'rad-ris-toast ' + type;
        const icons = { success: 'fa-circle-check', info: 'fa-circle-info', warning: 'fa-triangle-exclamation' };
        t.innerHTML = '<i class="fa-solid ' + (icons[type] || icons.info) + '"></i> ' + msg;
        c.appendChild(t);
        setTimeout(function () {
            t.remove();
        }, 3500);
    }

    function openModal(id) {
        const el = document.getElementById(id);
        if (el) {
            el.classList.add('open');
            el.setAttribute('aria-hidden', 'false');
        }
    }

    function closeModal(id) {
        const el = document.getElementById(id);
        if (el) {
            el.classList.remove('open');
            el.setAttribute('aria-hidden', 'true');
        }
    }

    function switchTab(tab) {
        document.querySelectorAll('.rad-ris-panel').forEach(function (p) {
            p.classList.remove('active');
        });
        document.querySelectorAll('.rad-ris-tab-btn').forEach(function (b) {
            b.classList.remove('active');
        });
        const panel = document.getElementById('rad-ris-panel-' + tab);
        if (panel) {
            panel.classList.add('active');
        }
        document.querySelectorAll('.rad-ris-tab-btn[data-rad-tab="' + tab + '"]').forEach(function (b) {
            b.classList.add('active');
        });

        if (tab === 'modalities') {
            loadModalitiesBoard();
        } else if (tab === 'reports') {
            loadAnalyticsCharts();
        } else if (tab === 'schedule') {
            loadScheduleGrid();
        } else if (tab === 'protocols') {
            loadProtocols();
        } else if (tab === 'reporting') {
            loadPendingQueue();
            requestAnimationFrame(function () {
                ensureRadRisCkEditors();
            });
        } else if (tab === 'completed') {
            initCompletedTableOnce();
        } else if (tab === 'ai') {
            initAiPieIfNeeded();
        } else if (tab === 'worklist') {
            scheduleRisWorklistFilterToolbarSync();
        }
    }

    function loadSummary() {
        const today = new Date().toISOString().slice(0, 10);
        const url = route('risSummary') + '?date=' + encodeURIComponent(today);
        fetch(url, { headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) {
                return r.json();
            })
            .then(function (data) {
                const el = function (id) {
                    return document.getElementById(id);
                };
                if (el('rad-ris-stat-total')) {
                    el('rad-ris-stat-total').textContent = data.total_orders ?? '0';
                }
                if (el('rad-ris-stat-completed')) {
                    el('rad-ris-stat-completed').textContent = data.completed ?? '0';
                }
                if (el('rad-ris-stat-urgent')) {
                    el('rad-ris-stat-urgent').textContent = data.urgent_stat_open ?? '0';
                }
                if (el('rad-ris-stat-report-pending')) {
                    el('rad-ris-stat-report-pending').textContent = data.in_progress ?? '0';
                }
                if (el('rad-ris-stat-ai')) {
                    el('rad-ris-stat-ai').textContent = data.ai_flagged ?? '0';
                }
                const pend = (data.ordered ?? 0) + (data.in_progress ?? 0);
                if (el('rad-ris-stat-pending-meta')) {
                    el('rad-ris-stat-pending-meta').innerHTML =
                        '<i class="fa-solid fa-arrow-trend-up" style="color:#2e7d32"></i> ' + pend + ' open in workflow';
                }
                if (el('rad-ris-stat-completion')) {
                    el('rad-ris-stat-completion').innerHTML =
                        '<span style="color:#2e7d32;font-weight:600">' + (data.completion_pct ?? 0) + '%</span> completion';
                }
            })
            .catch(function () {
                radRisToast('Unable to load summary', 'warning');
            });
    }

    function loadModalitiesBoard() {
        const today = new Date().toISOString().slice(0, 10);
        fetch(route('risModalitiesBoard') + '?date=' + encodeURIComponent(today), {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function (r) {
                return r.json();
            })
            .then(function (res) {
                const g = document.getElementById('rad-ris-modality-grid');
                if (!g) {
                    return;
                }
                const list = res.data || [];
                g.innerHTML = list
                    .map(function (m) {
                        return (
                            '<div class="rad-ris-modality-card">' +
                            '<div class="rad-ris-mod-icon" style="background:' +
                            m.color +
                            '"><i class="fa-solid ' +
                            m.icon +
                            '"></i></div>' +
                            '<div class="rad-ris-mod-name">' +
                            escapeHtml(m.name) +
                            '</div>' +
                            '<div class="rad-ris-mod-location"><i class="fa-solid fa-location-dot" style="margin-right:4px"></i>' +
                            escapeHtml(m.location) +
                            '</div>' +
                            '<div class="rad-ris-mod-stats">' +
                            '<div class="rad-ris-mod-stat"><div class="val" style="color:' +
                            m.color +
                            '">' +
                            m.today +
                            '</div><div class="lbl">Today</div></div>' +
                            '<div class="rad-ris-mod-stat"><div class="val" style="color:#e65100">' +
                            m.pending +
                            '</div><div class="lbl">Pending</div></div>' +
                            '<div class="rad-ris-mod-stat"><div class="val" style="color:#2e7d32">' +
                            m.util +
                            '%</div><div class="lbl">Util.</div></div>' +
                            '</div>' +
                            '<div class="rad-ris-mod-status-bar"><div class="rad-ris-mod-status-bar-fill" style="width:' +
                            m.util +
                            '%;background:' +
                            m.color +
                            '"></div></div>' +
                            '</div>'
                        );
                    })
                    .join('');
                renderModalityUtilChart(list);
            })
            .catch(function () {
                radRisToast('Unable to load modalities', 'warning');
            });
    }

    function destroyChart(ch) {
        if (ch && typeof ch.destroy === 'function') {
            ch.destroy();
        }
    }

    function renderModalityUtilChart(list) {
        const canvas = document.getElementById('rad-ris-modality-util-chart');
        if (!canvas || typeof Chart === 'undefined') {
            return;
        }
        destroyChart(modalityChart);
        modalityChart = new Chart(canvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: list.map(function (m) {
                    return m.name.length > 18 ? m.name.slice(0, 18) + '…' : m.name;
                }),
                datasets: [
                    {
                        label: 'Completed',
                        data: list.map(function (m) {
                            return Math.max(0, m.today - m.pending);
                        }),
                        backgroundColor: '#1565c0'
                    },
                    {
                        label: 'Pending',
                        data: list.map(function (m) {
                            return m.pending;
                        }),
                        backgroundColor: '#e65100'
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'top' } },
                scales: {
                    x: { stacked: true },
                    y: { stacked: true, beginAtZero: true, grid: { color: '#f0f3f8' } }
                }
            }
        });
    }

    function loadAnalyticsCharts() {
        fetch(route('risAnalytics'), { headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) {
                return r.json();
            })
            .then(function (data) {
                const daily = data.daily_volume || [];
                const byMod = data.by_modality || [];
                const tatWeekly = data.tat_weekly || [];
                let sources = data.sources || [];
                if (!sources.length || sources.every(function (s) {
                    return !s.count;
                })) {
                    sources = [{ label: 'No orders', count: 1 }];
                }
                const monthly = data.monthly_summary || [];
                const chartLabels = data.chart_labels || daily;
                const modalitySeries = data.modality_series || [];
                const monthLabel = data.monthly_period || '';

                const elMonth = document.getElementById('rad-ris-analytics-month-label');
                if (elMonth) {
                    elMonth.textContent = monthLabel ? 'Calendar month: ' + monthLabel : '';
                }

                const modColors = ['#1565c0', '#2e7d32', '#c62828', '#6a1b9a', '#e65100', '#00695c', '#4527a0', '#880e4f'];
                const ex = document.getElementById('rad-ris-exams-chart');
                if (ex && typeof Chart !== 'undefined') {
                    destroyChart(examsChart);
                    const labels = chartLabels.map(function (d) {
                        return d.label || d;
                    });
                    let datasets = modalitySeries.map(function (s, idx) {
                        return {
                            label: s.label.length > 16 ? s.label.slice(0, 16) + '…' : s.label,
                            data: s.data || [],
                            backgroundColor: modColors[idx % modColors.length]
                        };
                    });
                    if (!datasets.length) {
                        datasets = [
                            {
                                label: 'Orders',
                                data: daily.map(function (d) {
                                    return d.count;
                                }),
                                backgroundColor: '#1565c0'
                            }
                        ];
                    }
                    examsChart = new Chart(ex.getContext('2d'), {
                        type: 'bar',
                        data: { labels: labels, datasets: datasets },
                        options: {
                            plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } } },
                            scales: {
                                x: { stacked: true, grid: { display: false } },
                                y: { stacked: true, beginAtZero: true, grid: { color: '#f0f3f8' } }
                            },
                            maintainAspectRatio: false
                        }
                    });
                }

                const tc = document.getElementById('rad-ris-tat-chart');
                if (tc && typeof Chart !== 'undefined') {
                    destroyChart(tatChart);
                    tatChart = new Chart(tc.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: tatWeekly.map(function (t) {
                                return t.label;
                            }),
                            datasets: [
                                {
                                    label: 'Avg turnaround (hrs)',
                                    data: tatWeekly.map(function (t) {
                                        return t.avg_hours;
                                    }),
                                    backgroundColor: '#00695c'
                                }
                            ]
                        },
                        options: {
                            plugins: { legend: { display: false } },
                            scales: { y: { beginAtZero: true, grid: { color: '#f0f3f8' } } },
                            maintainAspectRatio: false
                        }
                    });
                }

                const sc = document.getElementById('rad-ris-source-chart');
                if (sc && typeof Chart !== 'undefined') {
                    destroyChart(sourceChart);
                    sourceChart = new Chart(sc.getContext('2d'), {
                        type: 'pie',
                        data: {
                            labels: sources.map(function (s) {
                                return s.label;
                            }),
                            datasets: [
                                {
                                    data: sources.map(function (s) {
                                        return s.count;
                                    }),
                                    backgroundColor: ['#1565c0', '#2e7d32', '#c62828', '#6a1b9a', '#e65100', '#00695c'],
                                    borderWidth: 2
                                }
                            ]
                        },
                        options: { plugins: { legend: { position: 'bottom' } }, maintainAspectRatio: false }
                    });
                }

                const tbody = document.getElementById('rad-ris-monthly-summary-body');
                if (tbody) {
                    tbody.innerHTML = monthly
                        .map(function (r) {
                            return (
                                '<tr><td class="px-3 py-2 rad-ris-fw-700">' +
                                escapeHtml(r.modality) +
                                '</td><td class="px-3 py-2">' +
                                r.orders +
                                '</td><td class="px-3 py-2" style="color:#2e7d32;font-weight:700">' +
                                r.completed +
                                '</td><td class="px-3 py-2" style="color:#e65100;font-weight:700">' +
                                r.pending +
                                '</td><td class="px-3 py-2">' +
                                escapeHtml(r.tat) +
                                '</td><td class="px-3 py-2 rad-ris-fw-700">' +
                                escapeHtml(r.revenue) +
                                '</td></tr>'
                            );
                        })
                        .join('');
                    if (!monthly.length) {
                        tbody.innerHTML =
                            '<tr><td colspan="6" class="px-3 py-3 rad-ris-text-muted rad-ris-text-sm">No data in range.</td></tr>';
                    }
                }

                if (byMod.length === 0 && daily.every(function (d) {
                    return d.count === 0;
                })) {
                    radRisToast('Last 7 days have no radiology orders yet.', 'info');
                }
            })
            .catch(function () {
                radRisToast('Unable to load analytics', 'warning');
            });
    }

    function initAiPieIfNeeded() {
        const canvas = document.getElementById('rad-ris-ai-pie-chart');
        if (!canvas || typeof Chart === 'undefined' || aiPieChart) {
            return;
        }
        aiPieChart = new Chart(canvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['No engine', 'Pending', 'Normal', 'Critical'],
                datasets: [{ data: [1, 0, 0, 0], backgroundColor: ['#5a7894', '#1565c0', '#2e7d32', '#c62828'], borderWidth: 2 }]
            },
            options: { plugins: { legend: { position: 'bottom' } }, maintainAspectRatio: false }
        });
    }

    function loadPendingQueue() {
        fetch(route('risPendingQueue'), { headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) {
                return r.json();
            })
            .then(function (res) {
                const tbody = document.getElementById('rad-ris-pending-report-body');
                if (!tbody) {
                    return;
                }
                const rows = res.data || [];
                tbody.innerHTML = rows
                    .map(function (r) {
                        const cls = r.priority === 'STAT' ? 'rad-ris-badge-red' : 'rad-ris-badge-orange';
                        return (
                            '<tr><td class="px-3 py-2 rad-ris-fw-700">' +
                            escapeHtml(r.patient) +
                            '</td><td class="px-3 py-2">' +
                            escapeHtml(r.study) +
                            '</td><td class="px-3 py-2"><span class="rad-ris-badge ' +
                            cls +
                            '">' +
                            escapeHtml(r.priority) +
                            '</span></td><td class="px-3 py-2 rad-ris-text-muted rad-ris-text-sm">' +
                            escapeHtml(r.age) +
                            '</td><td class="px-3 py-2"><button type="button" class="rad-ris-btn rad-ris-btn-primary rad-ris-btn-sm rad-ris-open-report" data-id="' +
                            r.item_id +
                            '" data-order-no="' +
                            escapeHtml(r.order_no || '') +
                            '" data-patient-id="' +
                            escapeHtml(r.patient_id || '') +
                            '" data-patient="' +
                            escapeHtml(r.patient || '') +
                            '" data-study="' +
                            escapeHtml(r.study || '') +
                            '"><i class="fa-solid fa-pen"></i> Report</button></td></tr>'
                        );
                    })
                    .join('');
                if (!rows.length) {
                    tbody.innerHTML =
                        '<tr><td colspan="5" class="px-3 py-3 rad-ris-text-muted rad-ris-text-sm">Queue is clear.</td></tr>';
                }
            })
            .catch(function () {});
    }

    function loadProtocols() {
        const q = (document.getElementById('rad-ris-protocol-search') || {}).value || '';
        fetch(route('risProtocols') + '?q=' + encodeURIComponent(q), {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function (r) {
                return r.json();
            })
            .then(function (res) {
                const g = document.getElementById('rad-ris-protocol-grid');
                if (!g) {
                    return;
                }
                const palette = ['#6a1b9a', '#c62828', '#1565c0', '#e65100', '#00695c', '#2e7d32', '#880e4f', '#4527a0'];
                const icons = [
                    'fa-lungs',
                    'fa-head-side-virus',
                    'fa-brain',
                    'fa-wave-square',
                    'fa-x-ray',
                    'fa-atom',
                    'fa-spine',
                    'fa-notes-medical'
                ];
                const list = res.data || [];
                g.innerHTML = list
                    .map(function (p, i) {
                        const color = palette[i % palette.length];
                        const icon = icons[i % icons.length];
                        const desc = p.desc || ('Modality: ' + p.modality);
                        return (
                            '<div class="rad-ris-protocol-card" title="' +
                            escapeHtml(p.name) +
                            '">' +
                            '<div class="rad-ris-pc-icon" style="background:' +
                            color +
                            '"><i class="fa-solid ' +
                            icon +
                            '"></i></div>' +
                            '<div class="rad-ris-fw-700" style="font-size:13px">' +
                            escapeHtml(p.name) +
                            '</div>' +
                            '<div class="rad-ris-text-muted rad-ris-text-sm" style="line-height:1.45">' +
                            escapeHtml(desc) +
                            '</div>' +
                            '<div class="rad-ris-text-sm mt-1 d-flex flex-wrap gap-1 align-items-center"><span class="rad-ris-badge rad-ris-badge-purple">' +
                            escapeHtml(p.modality) +
                            '</span>' +
                            (p.code
                                ? '<span class="rad-ris-badge rad-ris-badge-gray">' + escapeHtml(p.code) + '</span>'
                                : '') +
                            '</div>' +
                            '</div>'
                        );
                    })
                    .join('');
                if (!list.length) {
                    g.innerHTML =
                        '<div class="rad-ris-text-muted rad-ris-text-sm p-2">No radiology tests found. Configure tests under Settings.</div>';
                }
            })
            .catch(function () {});
    }

    function loadScheduleGrid() {
        const monday = getMonday(new Date());
        monday.setDate(monday.getDate() + weekOffset * 7);
        const weekStart = monday.toISOString().slice(0, 10);
        fetch(route('risSchedule') + '?week_start=' + encodeURIComponent(weekStart), {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function (r) {
                return r.json();
            })
            .then(function (data) {
                const label = document.getElementById('rad-ris-sch-week-label');
                if (label) {
                    label.textContent = data.week_label || '—';
                }
                const grid = document.getElementById('rad-ris-schedule-grid');
                if (!grid) {
                    return;
                }
                const days = data.days || [];
                const slots = data.slots || [];
                const cells = data.cells || {};
                let html = '<div class="rad-ris-sch-header">Time</div>';
                days.forEach(function (d) {
                    html += '<div class="rad-ris-sch-header">' + escapeHtml(d.label) + '</div>';
                });
                slots.forEach(function (slot, si) {
                    html += '<div class="rad-ris-sch-time">' + escapeHtml(slot) + '</div>';
                    days.forEach(function (d) {
                        const list = (cells[d.key] && cells[d.key][si]) || [];
                        let inner = '';
                        list.forEach(function (ap) {
                            inner +=
                                '<div class="rad-ris-sch-appt ' +
                                (ap.class || '') +
                                '">' +
                                escapeHtml(ap.text) +
                                '</div>';
                        });
                        html += '<div class="rad-ris-sch-slot">' + inner + '</div>';
                    });
                });
                grid.innerHTML = html;
            })
            .catch(function () {});
    }

    function getMonday(d) {
        const x = new Date(d);
        const day = x.getDay();
        const diff = (day === 0 ? -6 : 1) - day;
        x.setDate(x.getDate() + diff);
        x.setHours(0, 0, 0, 0);
        return x;
    }

    function escapeHtml(s) {
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function initRadRisWorklistTooltips() {
        $('[data-bs-toggle="tooltip"]').tooltip('dispose');
        $('[data-bs-toggle="tooltip"]').tooltip({ container: 'body' });
    }

    function initWorklistTable() {
        if (!$('#rad-ris-worklist-table').length) {
            return;
        }
        worklistTable = $('#rad-ris-worklist-table').DataTable({
            processing: true,
            serverSide: true,
            paging: true,
            info: true,
            lengthChange: true,
            scrollX: true,
            order: [[1, 'asc']],
            ajax: {
                url: route('loadtable'),
                type: 'POST',
                data: function (d) {
                    d._token = window.Laravel.csrfToken;
                    d.status = $('#rad-ris-filter-status').val();
                    d.category = $('#rad-ris-filter-modality').val();
                    d.priority = $('#rad-ris-filter-priority').val();
                    d.date_from = $('#rad-ris-filter-date-from').val();
                    d.date_to = $('#rad-ris-filter-date-to').val();
                }
            },
            columns: [
                {
                    data: null,
                    name: 'serial_no',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                { data: 'id', name: 'id', visible: false, searchable: false, orderable: true },
                { data: 'accession', name: 'accession', orderable: false, searchable: false },
                { data: 'patient_name', name: 'patient_name', orderable: false, searchable: false },
                { data: 'patient_age_sex', name: 'patient_age_sex', orderable: false, searchable: false },
                { data: 'modality', name: 'modality', orderable: false, searchable: false },
                { data: 'examination', name: 'examination', orderable: false, searchable: false },
                { data: 'ordered_by', name: 'ordered_by', orderable: false, searchable: false },
                { data: 'ward_opd', name: 'ward_opd', orderable: false, searchable: false },
                { data: 'priority', name: 'priority', orderable: false, searchable: false },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'time_slot', name: 'time_slot', orderable: false, searchable: false },
                { data: 'workflow', name: 'workflow', orderable: false, searchable: false }
            ],
            dom: 'Blrtip',
            responsive: true,
            autoWidth: true,
            buttons: [
                {
                    text: '<i class="fa fa-sync"></i>',
                    className: 'btn btn-secondary',
                    titleAttr: 'Reload Table',
                    action: function (e, dt) {
                        dt.ajax.reload();
                    }
                },
                { extend: 'copy', className: 'buttons-copy btn btn-light', text: '<i class="fa fa-copy"></i>', titleAttr: 'Copy' },
                { extend: 'csv', className: 'buttons-csv btn btn-info', text: '<i class="fa fa-file-csv"></i>', titleAttr: 'Export as CSV' },
                { extend: 'excel', className: 'buttons-excel btn btn-success', text: '<i class="fa fa-file-excel"></i>', titleAttr: 'Export as Excel' },
                { extend: 'pdf', className: 'buttons-pdf btn btn-danger', text: '<i class="fa fa-file-pdf"></i>', titleAttr: 'Export as PDF' },
                { extend: 'print', className: 'buttons-print btn btn-primary', text: '<i class="fa fa-print"></i>', titleAttr: 'Print Table' },
                { extend: 'colvis', className: 'buttons-colvis btn btn-dark', text: '<i class="fa fa-columns"></i>', titleAttr: 'Column Visibility' }
            ],
            initComplete: function () {
                scheduleRisWorklistFilterToolbarSync();
            },
            drawCallback: function () {
                scheduleRisWorklistFilterToolbarSync();
                initRadRisWorklistTooltips();
            }
        });

        initRadRisWorklistTooltips();
        worklistTable.on('draw.dt responsive-display.dt column-visibility.dt', function () {
            scheduleRisWorklistFilterToolbarSync();
            initRadRisWorklistTooltips();
        });
        worklistTable.on('draw.dt responsive-display.dt', initRadRisWorklistTooltips);

        bindWorklistSearchControls();
    }

    function bindWorklistSearchControls() {
        const $input = $('#rad-ris-worklist-search');
        if (!$input.length || !worklistTable) {
            return;
        }

        $input.off('input.radRisSearch').on('input.radRisSearch', function () {
            clearTimeout(worklistSearchTimer);
            const val = $(this).val();
            worklistSearchTimer = setTimeout(function () {
                worklistTable.search(val).draw();
            }, 350);
        });

        $('#rad-ris-clear-search')
            .off('click.radRisSearch')
            .on('click.radRisSearch', function () {
                clearTimeout(worklistSearchTimer);
                $input.val('');
                worklistTable.search('').draw();
            });
    }

    function reloadWorklist() {
        if (worklistTable) {
            worklistTable.ajax.reload(null, false);
        }
        if (completedTable) {
            completedTable.ajax.reload(null, false);
        }
        loadSummary();
        loadPendingQueue();
    }

    function initCompletedTableOnce() {
        if (completedTable) {
            return;
        }
        if (!document.getElementById('rad-ris-completed-table')) {
            return;
        }
        initCompletedTable();
    }

    function initCompletedTable() {
        completedTable = $('#rad-ris-completed-table').DataTable({
            processing: true,
            serverSide: true,
            paging: true,
            info: true,
            lengthChange: true,
            scrollX: true,
            order: [[1, 'desc']],
            ajax: {
                url: route('loadtable'),
                type: 'POST',
                data: function (d) {
                    d._token = window.Laravel.csrfToken;
                    d.completed_only = 1;
                    d.status = $('#rad-ris-c-filter-status').val();
                    d.category = $('#rad-ris-c-filter-modality').val();
                    d.priority = $('#rad-ris-c-filter-priority').val();
                    d.date_from = $('#rad-ris-c-filter-date-from').val();
                    d.date_to = $('#rad-ris-c-filter-date-to').val();
                }
            },
            columns: [
                {
                    data: null,
                    name: 'serial_no',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                { data: 'id', name: 'id', visible: false, searchable: false, orderable: true },
                { data: 'accession', name: 'accession', orderable: false, searchable: false },
                { data: 'patient_name', name: 'patient_name', orderable: false, searchable: false },
                { data: 'patient_age_sex', name: 'patient_age_sex', orderable: false, searchable: false },
                { data: 'modality', name: 'modality', orderable: false, searchable: false },
                { data: 'examination', name: 'examination', orderable: false, searchable: false },
                { data: 'ordered_by', name: 'ordered_by', orderable: false, searchable: false },
                { data: 'ward_opd', name: 'ward_opd', orderable: false, searchable: false },
                { data: 'priority', name: 'priority', orderable: false, searchable: false },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'time_slot', name: 'time_slot', orderable: false, searchable: false },
                { data: 'workflow', name: 'workflow', orderable: false, searchable: false }
            ],
            dom: 'Blrtip',
            responsive: true,
            autoWidth: true,
            buttons: [
                {
                    text: '<i class="fa fa-sync"></i>',
                    className: 'btn btn-secondary',
                    titleAttr: 'Reload',
                    action: function (e, dt) {
                        dt.ajax.reload();
                    }
                },
                { extend: 'copy', className: 'buttons-copy btn btn-light', text: '<i class="fa fa-copy"></i>', titleAttr: 'Copy' },
                { extend: 'csv', className: 'buttons-csv btn btn-info', text: '<i class="fa fa-file-csv"></i>', titleAttr: 'CSV' },
                { extend: 'excel', className: 'buttons-excel btn btn-success', text: '<i class="fa fa-file-excel"></i>', titleAttr: 'Excel' },
                { extend: 'pdf', className: 'buttons-pdf btn btn-danger', text: '<i class="fa fa-file-pdf"></i>', titleAttr: 'PDF' },
                { extend: 'print', className: 'buttons-print btn btn-primary', text: '<i class="fa fa-print"></i>', titleAttr: 'Print' },
                { extend: 'colvis', className: 'buttons-colvis btn btn-dark', text: '<i class="fa fa-columns"></i>', titleAttr: 'Columns' }
            ]
        });
        const $cin = $('#rad-ris-completed-search');
        let cTimer = null;
        $cin.off('input.radRisCompleted').on('input.radRisCompleted', function () {
            clearTimeout(cTimer);
            const val = $(this).val();
            cTimer = setTimeout(function () {
                completedTable.search(val).draw();
            }, 350);
        });
        $('#rad-ris-c-clear-search')
            .off('click.radRisCompleted')
            .on('click.radRisCompleted', function () {
                clearTimeout(cTimer);
                $cin.val('');
                completedTable.search('').draw();
            });
        $('#rad-ris-c-filter-status, #rad-ris-c-filter-modality, #rad-ris-c-filter-priority').on('change', function () {
            completedTable.ajax.reload();
        });
        $('#rad-ris-c-filter-date-from, #rad-ris-c-filter-date-to').on('change', function () {
            completedTable.ajax.reload();
        });
        $('#rad-ris-c-clear-filters').on('click', function () {
            $('#rad-ris-c-filter-status').val('completed').trigger('change');
            $('#rad-ris-c-filter-modality').val('').trigger('change');
            $('#rad-ris-c-filter-priority').val('').trigger('change');
            $('#rad-ris-c-filter-date-from').val('');
            $('#rad-ris-c-filter-date-to').val('');
            $('#rad-ris-completed-search').val('');
            completedTable.search('').draw();
            completedTable.ajax.reload();
        });
    }

    function bindWorklistFilters() {
        $('#rad-ris-filter-status, #rad-ris-filter-modality, #rad-ris-filter-priority').on('change', function () {
            reloadWorklist();
        });
        $('#rad-ris-filter-date-from, #rad-ris-filter-date-to').on('change', function () {
            reloadWorklist();
        });
        $('#rad-ris-clear-filters').on('click', function () {
            clearTimeout(worklistSearchTimer);
            $('#rad-ris-filter-status').val('').trigger('change');
            $('#rad-ris-filter-modality').val('').trigger('change');
            $('#rad-ris-filter-priority').val('').trigger('change');
            $('#rad-ris-filter-date-from').val('');
            $('#rad-ris-filter-date-to').val('');
            $('#rad-ris-worklist-search').val('');
            if (worklistTable) {
                worklistTable.search('');
            }
            reloadWorklist();
        });
    }

    function bindModalActions() {
        document.querySelectorAll('.rad-ris-close-modal').forEach(function (btn) {
            btn.addEventListener('click', function () {
                closeModal(btn.getAttribute('data-target'));
            });
        });
        [['rad-ris-btn-new-order', 'rad-ris-order-modal'], ['rad-ris-toolbar-new-order', 'rad-ris-order-modal'], ['rad-ris-btn-book-slot', 'rad-ris-schedule-modal']].forEach(function (pair) {
            const b = document.getElementById(pair[0]);
            if (b) {
                b.addEventListener('click', function () {
                    openModal(pair[1]);
                });
            }
        });
        document.getElementById('rad-ris-toolbar-refresh')?.addEventListener('click', function () {
            reloadWorklist();
            radRisToast('Lists refreshed', 'success');
        });
        document.getElementById('rad-ris-refresh-modalities')?.addEventListener('click', function () {
            loadModalitiesBoard();
            radRisToast('Modality board refreshed', 'info');
        });
        document.getElementById('rad-ris-sch-prev')?.addEventListener('click', function () {
            weekOffset -= 1;
            loadScheduleGrid();
        });
        document.getElementById('rad-ris-sch-next')?.addEventListener('click', function () {
            weekOffset += 1;
            loadScheduleGrid();
        });
        document.getElementById('rad-ris-btn-export')?.addEventListener('click', function () {
            if (worklistTable && worklistTable.button) {
                try {
                    worklistTable.button('.buttons-excel').trigger();
                } catch (e) {
                    try {
                        worklistTable.button('.buttons-csv').trigger();
                    } catch (e2) {
                        radRisToast('Use the DataTable toolbar buttons (copy / export) above the grid.', 'info');
                    }
                }
            } else {
                radRisToast('Table is not ready yet.', 'warning');
            }
        });
        document.querySelectorAll('.rad-ris-dicom-tool').forEach(function (b) {
            b.addEventListener('click', function () {
                radRisToast(b.getAttribute('data-msg') || 'Tool', 'info');
            });
        });
        document.getElementById('rad-ris-ai-run-demo')?.addEventListener('click', function () {
            radRisToast('AI engine not connected — demo only.', 'info');
        });
        document.getElementById('rad-ris-protocol-settings')?.addEventListener('click', function () {
            const scope = document.querySelector('.rad-ris-scope');
            const u = scope && scope.getAttribute('data-settings-tests-url');
            if (u) {
                window.location.href = u;
            } else {
                radRisToast('Radiology test settings route not available.', 'warning');
            }
        });
        let protoTimer = null;
        document.getElementById('rad-ris-protocol-search')?.addEventListener('input', function () {
            clearTimeout(protoTimer);
            protoTimer = setTimeout(loadProtocols, 350);
        });
    }

    function bindTabs() {
        document.querySelectorAll('.rad-ris-tab-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                switchTab(btn.getAttribute('data-rad-tab'));
            });
        });
    }

    function bindReportingOpenReport() {
        $(document).on('click', '.rad-ris-open-report', function () {
            const $btn = $(this);
            const id = $btn.data('id');
            loadRadRisReportPanel(id, {
                orderNo: $btn.attr('data-order-no') || '',
                patientId: $btn.attr('data-patient-id') || '',
                patient: $btn.attr('data-patient') || '',
                study: $btn.attr('data-study') || ''
            });
        });
        $(document).on('click', '.rad-ris-wf-open-reporting', function () {
            const $btn = $(this);
            loadRadRisReportPanel($btn.data('item-id'), {
                orderNo: $btn.attr('data-order-no') || '',
                patientId: $btn.attr('data-patient-id') || '',
                patient: $btn.attr('data-patient') || '',
                study: $btn.attr('data-study') || ''
            });
        });
        $(document).on('click', '.rad-ris-wf-to-exam', async function () {
            const id = $(this).data('item-id');
            const token = await csrftoken();
            $.post(route('workflowAdvance', { item: id }), { _token: token }, function (res) {
                reloadWorklist();
                sendmsg('success', res.message || 'Updated.');
            }).fail(function (xhr) {
                sendmsg('error', xhr?.responseJSON?.message || 'Unable to update status.');
            });
        });
    }

    function bindLegacyRadiologyHandlers() {
        $(document).on('click', '.update-item-status', async function () {
            const url = $(this).data('url');
            const confirm = await Swal.fire({
                title: 'Move to next status?',
                text: 'Are you sure you want to move this item to the next status.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, move',
                cancelButtonText: 'Cancel'
            });
            if (!confirm.isConfirmed) {
                return;
            }
            const token = await csrftoken();
            loader('show');
            $.post(url, { _token: token }, function (response) {
                loader('hide');
                reloadWorklist();
                sendmsg('success', response.message || 'Status updated successfully.');
            }).fail(function (xhr) {
                loader('hide');
                sendmsg('error', xhr?.responseJSON?.message || 'Unable to update status.');
            });
        });

        $(document).on('click', '.open-report-form', async function () {
            const id = $(this).data('id');
            loader('show');
            const token = await csrftoken();
            $.post(route('showform', { item: id }), { _token: token }, function (response) {
                loader('hide');
                $('#ajaxdata').html(response);
                $('.add-datamodal .modal-dialog').addClass('modal-xl');
                $('.add-datamodal').modal('show');
            }).fail(function (xhr) {
                loader('hide');
                sendmsg('error', xhr?.responseJSON?.message || 'Unable to load report form.');
            });
        });

        $(document).on('click', '.open-charge-payment-form', async function () {
            const url = $(this).data('url');
            const chargeId = $(this).data('charge-id');
            const title = $(this).data('title');
            const contextNote = $(this).data('context-note');
            const token = await csrftoken();
            loader('show');
            $.post(
                url,
                {
                    _token: token,
                    charge_ids: [chargeId],
                    title: title,
                    context_note: contextNote
                },
                function (response) {
                    loader('hide');
                    $('#ajaxdata').html(response);
                    $('.add-datamodal .modal-dialog').addClass('modal-xl');
                    $('.add-datamodal').modal('show');
                }
            ).fail(function (xhr) {
                loader('hide');
                sendmsg('error', xhr?.responseJSON?.message || 'Unable to load payment form.');
            });
        });

        $(document).on('click', '#radRisReportDraftBtn', function (e) {
            e.preventDefault();
            $('#radRisSaveAction').val('draft');
            $('#saveReportForm').trigger('submit');
        });
        $(document).on('click', '#radRisReportFinalizeBtn', function (e) {
            e.preventDefault();
            $('#radRisSaveAction').val('finalize');
            $('#saveReportForm').trigger('submit');
        });
        $(document).on('click', '#radRisReportAddendumBtn', function (e) {
            e.preventDefault();
            $('#radRisSaveAction').val('addendum');
            $('#saveReportForm').trigger('submit');
        });

        $(document).on('submit', '#saveReportForm', async function (e) {
            e.preventDefault();
            loader('show');
            $('.err').remove();
            const token = await csrftoken();
            const $form = $(this);
            const id = $form.find('input[name="item_id"]').val();
            const saveUrlLegacy = getRisSaveUrl(id);
            if (!saveUrlLegacy) {
                loader('hide');
                sendmsg('error', 'Save URL is not configured. Reload the page or contact support.');
                return;
            }
            $.ajax({
                url: saveUrlLegacy,
                type: 'POST',
                data: $form.serialize() + '&_token=' + encodeURIComponent(token),
                success: function (response) {
                    loader('hide');
                    $('.add-datamodal').modal('hide');
                    reloadWorklist();
                    loadPendingQueue();
                    applyRadRisReportingSelection(null, {});
                    sendmsg('success', response.message || 'Saved successfully.');
                },
                error: function (xhr) {
                    loader('hide');
                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        const errors = xhr.responseJSON.errors;
                        const msgs = [];
                        for (const field in errors) {
                            msgs.push(errors[field].message);
                        }
                        if (msgs.length) {
                            sendmsg('error', msgs.join('<br>'));
                        }
                    } else {
                        sendmsg('error', xhr?.responseJSON?.message || 'Unable to save report.');
                    }
                }
            });
        });

        $(document).on('submit', '#collectPaymentForm', async function (e) {
            e.preventDefault();
            loader('show');
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
                    reloadWorklist();
                    sendmsg('success', response.message || 'Payment collected successfully.');
                },
                error: function (xhr) {
                    loader('hide');
                    sendmsg('error', xhr?.responseJSON?.message || 'Unable to collect payment.');
                }
            });
        });

        $(document).on('click', '#radRisRptDraftBtn', function (e) {
            e.preventDefault();
            $('#radRisRptSaveAction').val('draft');
            $('#radRisRptForm').trigger('submit');
        });
        $(document).on('click', '#radRisRptFinalizeBtn', function (e) {
            e.preventDefault();
            $('#radRisRptSaveAction').val('finalize');
            $('#radRisRptForm').trigger('submit');
        });
        $(document).on('click', '#radRisRptAddendumBtn', function (e) {
            e.preventDefault();
            $('#radRisRptSaveAction').val('addendum');
            $('#radRisRptForm').trigger('submit');
        });
        $(document).on('click', '#radRisRptPrintBtn', function () {
            const id = document.getElementById('rad-ris-rpt-item-id').value;
            if (!id) {
                radRisToast('Open a study first.', 'warning');
                return;
            }
            if (radRisReportStatusNorm !== 'completed') {
                radRisToast('Finalize the report before PDF download.', 'warning');
                return;
            }
            window.open(route('completedPdf', { item: id }), '_blank', 'noopener');
        });

        $(document).on('submit', '#radRisRptForm', async function (e) {
            e.preventDefault();
            syncRadRisCkEditorsToTextareas();
            loader('show');
            const token = await csrftoken();
            const $form = $(this);
            const id = $form.find('#rad-ris-rpt-item-id').val();
            const saveUrl = getRisSaveUrl(id);
            if (!saveUrl || !id) {
                loader('hide');
                sendmsg('error', 'Open a study from the pending queue first, then save.');
                return;
            }
            $.ajax({
                url: saveUrl,
                type: 'POST',
                data: $form.serialize() + '&_token=' + encodeURIComponent(token),
                success: function (response) {
                    loader('hide');
                    reloadWorklist();
                    sendmsg('success', response.message || 'Saved successfully.');
                    fetch(route('reportItem', { item: id }), { headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(function (r) {
                            return r.json();
                        })
                        .then(function (data) {
                            fillRadRisReportForm(data);
                        })
                        .catch(function () {});
                },
                error: function (xhr) {
                    loader('hide');
                    if (xhr.status === 422 && xhr.responseJSON?.message) {
                        sendmsg('error', xhr.responseJSON.message);
                        return;
                    }
                    sendmsg('error', xhr?.responseJSON?.message || 'Unable to save report.');
                }
            });
        });
    }

    $(document).ready(function () {
        if (typeof flatpickr === 'function') {
            flatpickr('.rad-ris-flatpickr', { dateFormat: 'd-m-Y', allowInput: true });
        }
        if ($.fn.select2) {
            $('#rad-ris-filter-status, #rad-ris-filter-modality, #rad-ris-filter-priority').select2({
                width: '100%',
                dropdownParent: $('#rad-ris-panel-worklist')
            });
            if (document.getElementById('rad-ris-c-filter-modality')) {
                $('#rad-ris-c-filter-status, #rad-ris-c-filter-modality, #rad-ris-c-filter-priority').select2({
                    width: '100%',
                    dropdownParent: $('#rad-ris-panel-completed')
                });
            }
            syncRisWorklistFilterToolbar();
            requestAnimationFrame(function () {
                requestAnimationFrame(syncRisWorklistFilterToolbar);
            });
        }
        bindTabs();
        bindModalActions();
        bindWorklistFilters();
        initWorklistTable();
        initCompletedTableOnce();
        $(window).on('resize.radRisFilters', function () {
            clearTimeout(risFilterToolbarResizeTimer);
            risFilterToolbarResizeTimer = setTimeout(syncRisWorklistFilterToolbar, 120);
        });
        bindLegacyRadiologyHandlers();
        bindReportingOpenReport();
        bindReportingPanel();
        loadSummary();
    });
})();
