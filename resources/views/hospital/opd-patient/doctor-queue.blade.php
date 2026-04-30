@extends('layouts.hospital.app')

@section('title', 'Doctor Queue - OPD')

@section('content')
@php
    $isDoctorDashboard = auth()->user()->hasRole('Doctor');
@endphp
<div class="page-content">
    <div class="container-fluid">

        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h3 class="mb-3 mb-md-0 text-white">
                    {{ $isDoctorDashboard ? 'My OPD Queue Desk' : 'OPD Queue Desk' }}
                </h3>
                <a href="{{ route('hospital.opd-patient.token-display') }}" target="_blank" class="btn btn-info btn-sm">
                    <i class="fa-solid fa-display me-1"></i> Open Token Display Screen
                </a>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3 {{ $isDoctorDashboard ? 'd-none' : '' }}">
            <div class="card-body py-2" id="dept-filter-wrap">
                <span class="text-muted small">Loading departments...</span>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4" id="in-room-card">
            <div class="card-header bg-warning text-dark fw-bold">
                <i class="fa-solid fa-door-open me-2"></i>Currently In Room
            </div>
            <div class="card-body" id="in-room-body">
                <div class="text-muted text-center py-3">No patient in room right now.</div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="fa-solid fa-hourglass-half me-2 text-white"></i>Waiting Queue</span>
                <span class="badge bg-secondary text-white" id="waiting-count">0 Waiting</span>
            </div>
            <div class="card-body p-0">
                <div id="queue-table-wrap">
                    <div class="text-center text-muted py-4">Loading...</div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="quickConsultModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen-lg-down modal-fullscreen modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="quickConsultTitle">Quick Consult</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="row g-0 quick-consult-layout">
                            <div class="col-lg-4 border-end bg-light" id="quick-consult-sidebar"></div>
                            <div class="col-lg-8">
                                <div class="p-3" id="quick-consult-content">
                                    <div class="text-muted">Select an action to continue.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('styles')
<style>
.quick-consult-layout {
    min-height: 70vh;
}

.doctor-focus-strip {
    border-left: 4px solid #0d6efd;
}

.doctor-queue-card {
    border: 1px solid #dbeafe;
    border-radius: 12px;
    background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
}

.doctor-token-chip {
    font-size: 1.9rem;
    font-weight: 700;
    line-height: 1;
    color: #0d6efd;
}

.doctor-waiting-list .table td,
.doctor-waiting-list .table th {
    vertical-align: middle;
}

.quick-consult-layout > .col-lg-8 {
    min-width: 0;
}

#quick-consult-content {
    overflow-x: hidden;
}

.quick-consult-nav .btn {
    text-align: left;
}

@media (max-width: 991.98px) {
    .quick-consult-layout {
        min-height: auto;
    }
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('public/front/assets/js/editor/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('public/modules/sa/opd-care-shared.js') }}"></script>
<script>
const ROUTES = {
    queueStatus: "{{ route('hospital.opd-patient.queue-status') }}",
    callNext: "{{ route('hospital.opd-patient.queue-call-next') }}",
    skipPatient: "{{ route('hospital.opd-patient.queue-skip', '__ID__') }}",
    undoSkip: "{{ route('hospital.opd-patient.queue-undo-skip', '__ID__') }}",
    visitSummaryView: "{{ route('hospital.opd-patient.visit-summary.view', ['opdPatient' => '__ID__']) }}",
    prescriptionForm: "{{ route('hospital.opd-patient.prescription.form', ['opdPatient' => '__ID__']) }}",
    prescriptionStore: "{{ route('hospital.opd-patient.prescription.store', ['opdPatient' => '__ID__']) }}",
    diagnosticShow: "{{ route('hospital.opd-patient.diagnostics.showform', ['opdPatient' => '__ID__']) }}",
    diagnosticStore: "{{ route('hospital.opd-patient.diagnostics.store', ['opdPatient' => '__ID__']) }}",
    visits: "{{ route('hospital.opd-patient.visits', ['patient' => '__ID__']) }}",
    visitSummaryPrint: "{{ route('hospital.opd-patient.visit-summary.print', ['opdPatient' => '__ID__']) }}",
    prescriptionPrint: "{{ route('hospital.opd-patient.prescription.print', ['opdPatient' => '__ID__']) }}",
};

const CSRF = "{{ csrf_token() }}";
const CAN_PATHOLOGY = @json(auth()->user()->can('create-pathology-order'));
const CAN_RADIOLOGY = @json(auth()->user()->can('create-radiology-order'));
const IS_DOCTOR_DASHBOARD = @json($isDoctorDashboard);

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function escapeAttr(value) {
    return escapeHtml(value).replace(/`/g, '&#096;');
}

function routeWithId(template, id) {
    return template.replace('__ID__', id);
}

function notify(type, message) {
    if (typeof sendmsg === 'function') {
        sendmsg(type, message);
        return;
    }
    alert(message);
}

function showLoader() {
    if (typeof loader === 'function') {
        loader('show');
    }
}

function hideLoader() {
    if (typeof loader === 'function') {
        loader('hide');
    }
}

async function postJson(url, body = {}) {
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': CSRF,
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(body),
    });
    return response.json();
}

const QuickConsult = (function () {
    const state = {
        opdPatientId: null,
        patientId: null,
        doctorId: null,
        activeSection: 'summary',
    };

    const editorIds = ['prescription_header_note', 'prescription_footer_note'];
    const modalEl = document.getElementById('quickConsultModal');
    const modal = (window.bootstrap && modalEl) ? new bootstrap.Modal(modalEl) : null;
    let prescriptionComposer = null;

    function getPrescriptionComposer() {
        if (!(window.OPDCareShared && typeof window.OPDCareShared.createPrescriptionComposer === 'function')) {
            return null;
        }

        if (!prescriptionComposer) {
            prescriptionComposer = window.OPDCareShared.createPrescriptionComposer({
                getCsrfToken: function () {
                    return CSRF;
                }
            });
        }

        return prescriptionComposer;
    }

    function initSelect2(scopeSelector) {
        if (!(window.jQuery && jQuery.fn && jQuery.fn.select2)) {
            return;
        }

        const $scope = jQuery(scopeSelector);
        $scope.find('.select2-modal').each(function () {
            const $select = jQuery(this);
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }
            $select.select2({
                dropdownParent: jQuery('#quickConsultModal'),
                width: '100%'
            });
        });
    }

    function initSectionPlugins() {
        if (window.flatpickr) {
            const pickerHost = (modalEl && modalEl.querySelector('.modal-content')) || modalEl || document.body;
            const initPicker = (input) => {
                if (!input) {
                    return;
                }

                if (input._flatpickr) {
                    input._flatpickr.destroy();
                }

                flatpickr(input, {
                    dateFormat: 'd-m-Y',
                    minDate: 'today',
                    appendTo: pickerHost,
                    positionElement: input,
                    static: true,
                    clickOpens: true,
                    allowInput: true,
                    disableMobile: true,
                });
            };

            document.querySelectorAll('#quick-consult-content .prescription-valid-till').forEach((input) => {
                initPicker(input);
                setTimeout(() => {
                    if (!input._flatpickr) {
                        initPicker(input);
                    }
                }, 60);
            });
        }

        initSelect2('#quick-consult-content');

        if (state.activeSection === 'prescription') {
            const composer = getPrescriptionComposer();
            if (composer) {
                composer.initialize();
                composer.focusStart();
            }
        }

        if (state.activeSection === 'pathology' || state.activeSection === 'radiology') {
            refreshDiagnosticPreview();
        }
    }

    function renderSidebar(meta) {
        const sidebar = document.getElementById('quick-consult-sidebar');
        if (!sidebar) {
            return;
        }

        const patientVisitsUrl = state.patientId ? (routeWithId(ROUTES.visits, state.patientId) + '#consolidated') : '#';
        const visitSummaryPrintUrl = state.opdPatientId ? routeWithId(ROUTES.visitSummaryPrint, state.opdPatientId) : '#';
        const prescriptionPrintUrl = state.opdPatientId ? routeWithId(ROUTES.prescriptionPrint, state.opdPatientId) : '#';

        sidebar.innerHTML = `
            <div class="p-3 border-bottom">
                <div class="badge bg-warning text-dark mb-2 fs-6">Token ${escapeHtml(meta.token || '-')}</div>
                <h5 class="mb-1">${escapeHtml(meta.patientName || '-')}</h5>
                <div class="text-muted small">Case: ${escapeHtml(meta.caseNo || '-')}</div>
                <div class="text-muted small">Dr. ${escapeHtml(meta.doctor || '-')} / ${escapeHtml(meta.dept || '-')}</div>
            </div>
            <div class="p-3 d-grid gap-2 quick-consult-nav">
                <button type="button" class="btn btn-outline-primary btn-sm" data-quick-section="summary">
                    <i class="fa-solid fa-file-medical me-1"></i> Visit Summary
                </button>
                <button type="button" class="btn btn-outline-primary btn-sm" data-quick-section="prescription">
                    <i class="fa-solid fa-prescription me-1"></i> Prescription
                </button>
                ${CAN_PATHOLOGY ? `
                    <button type="button" class="btn btn-outline-primary btn-sm" data-quick-section="pathology">
                        <i class="fa-solid fa-vial-circle-check me-1"></i> Pathology Order
                    </button>
                ` : ''}
                ${CAN_RADIOLOGY ? `
                    <button type="button" class="btn btn-outline-primary btn-sm" data-quick-section="radiology">
                        <i class="fa-solid fa-x-ray me-1"></i> Radiology Order
                    </button>
                ` : ''}
                <a href="${escapeAttr(patientVisitsUrl)}" target="_blank" class="btn btn-outline-dark btn-sm ${state.patientId ? '' : 'disabled'}">
                    <i class="fa-solid fa-notes-medical me-1"></i> Previous Consolidated
                </a>
                <a href="${escapeAttr(visitSummaryPrintUrl)}" target="_blank" class="btn btn-outline-secondary btn-sm ${state.opdPatientId ? '' : 'disabled'}">
                    <i class="fa-solid fa-print me-1"></i> Print Visit Summary
                </a>
                <a href="#" data-url="${escapeAttr(prescriptionPrintUrl)}" class="btn btn-outline-secondary btn-sm quick-print-prescription ${state.opdPatientId ? '' : 'disabled'}">
                    <i class="fa-solid fa-file-prescription me-1"></i> Print Prescription
                </a>
                <button type="button" class="btn btn-success btn-sm mt-2" id="quick-complete-next-btn">
                    <i class="fa-solid fa-forward-step me-1"></i> Complete & Call Next
                </button>
            </div>
        `;
    }

    function markActiveSection(section) {
        document.querySelectorAll('[data-quick-section]').forEach((btn) => {
            const active = btn.getAttribute('data-quick-section') === section;
            btn.classList.toggle('btn-primary', active);
            btn.classList.toggle('btn-outline-primary', !active);
        });
    }

    function setLoading(text) {
        const content = document.getElementById('quick-consult-content');
        if (content) {
            content.innerHTML = `<div class="text-center text-muted py-4">${escapeHtml(text || 'Loading...')}</div>`;
        }
    }

    async function fetchHtml(url) {
        const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        if (!response.ok) {
            throw new Error('Unable to load content.');
        }
        return response.text();
    }

    async function postHtml(url, payload) {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
            },
            body: new URLSearchParams(payload),
        });

        if (!response.ok) {
            throw new Error('Unable to load content.');
        }
        return response.text();
    }

    async function loadSection(section) {
        if (!state.opdPatientId) {
            return;
        }

        state.activeSection = section;
        markActiveSection(section);
        setLoading('Loading...');

        try {
            let html = '';

            if (section === 'summary') {
                html = await fetchHtml(routeWithId(ROUTES.visitSummaryView, state.opdPatientId));
            } else if (section === 'prescription') {
                html = await fetchHtml(routeWithId(ROUTES.prescriptionForm, state.opdPatientId));
            } else if (section === 'pathology' || section === 'radiology') {
                html = await postHtml(routeWithId(ROUTES.diagnosticShow, state.opdPatientId), { order_type: section, _token: CSRF });
            }

            const content = document.getElementById('quick-consult-content');
            if (content) {
                content.innerHTML = html || '<div class="text-muted">No content.</div>';
            }

            const diagnosticForm = document.getElementById('saveDiagnosticOrderForm');
            if (diagnosticForm) {
                diagnosticForm.dataset.storeUrl = routeWithId(ROUTES.diagnosticStore, state.opdPatientId);
            }

            initSectionPlugins();
        } catch (error) {
            const content = document.getElementById('quick-consult-content');
            if (content) {
                content.innerHTML = '<div class="alert alert-danger mb-0">Unable to load section. Please try again.</div>';
            }
        }
    }

    function open(meta) {
        state.opdPatientId = meta.opdPatientId;
        state.patientId = meta.patientId;
        state.doctorId = meta.doctorId;
        state.activeSection = 'summary';

        const title = document.getElementById('quickConsultTitle');
        if (title) {
            title.textContent = `Quick Consult - Token ${meta.token || '-'}`;
        }

        renderSidebar(meta);
        if (modal) {
            modal.show();
        }
        loadSection('summary');
    }

    function close() {
        if (modal) {
            modal.hide();
        }
    }

    function reset() {
        if (window.jQuery && jQuery.fn && jQuery.fn.select2) {
            jQuery('#quick-consult-content .select2-hidden-accessible').select2('destroy');
        }

        state.opdPatientId = null;
        state.patientId = null;
        state.doctorId = null;
        state.activeSection = 'summary';
        prescriptionComposer = null;

        const sidebar = document.getElementById('quick-consult-sidebar');
        const content = document.getElementById('quick-consult-content');
        if (sidebar) {
            sidebar.innerHTML = '';
        }
        if (content) {
            content.innerHTML = '<div class="text-muted">Select an action to continue.</div>';
        }
    }

    function refreshDiagnosticPreview() {
        if (!(window.OPDCareShared && typeof window.OPDCareShared.refreshDiagnosticPreview === 'function')) {
            return;
        }

        window.OPDCareShared.refreshDiagnosticPreview('#diagnostic_test_ids', '#diagnostic-test-preview-body');
    }

    async function submitPrescription(form) {
        const submitBtn = document.querySelector('.save-prescription-btn');
        const originalLabel = submitBtn ? submitBtn.textContent : '';

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Saving...';
        }

        showLoader();
        try {
            const storeUrl = form.dataset.storeUrl || routeWithId(ROUTES.prescriptionStore, state.opdPatientId);
            const response = await fetch(storeUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: new FormData(form),
            });

            const data = await response.json();
            hideLoader();

            if (!response.ok || data.status === false || data.errors) {
                notify('error', data?.errors?.[0]?.message || data?.message || 'Unable to save prescription.');
                return;
            }

            notify('success', data.message || 'Prescription saved successfully.');
            await loadSection('summary');
            await QueueDesk.fetchAndRender();
        } catch (error) {
            hideLoader();
            notify('error', 'Unable to save prescription.');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = originalLabel || 'Save Prescription';
            }
        }
    }

    async function submitDiagnostic(form) {
        showLoader();
        try {
            const storeUrl = form.dataset.storeUrl || routeWithId(ROUTES.diagnosticStore, state.opdPatientId);
            const response = await fetch(storeUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: new FormData(form),
            });

            const data = await response.json();
            hideLoader();

            if (!response.ok || data.status === false || data.errors) {
                notify('error', data?.errors?.[0]?.message || data?.message || 'Unable to create diagnostic order.');
                return;
            }

            notify('success', data.message || 'Diagnostic order created successfully.');
            await loadSection(state.activeSection);
            await QueueDesk.fetchAndRender();
        } catch (error) {
            hideLoader();
            notify('error', 'Unable to create diagnostic order.');
        }
    }

    function bindEvents() {
        if (!window.jQuery) {
            return;
        }

        jQuery(document)
            .off('click.quickConsult', '[data-quick-section]')
            .on('click.quickConsult', '[data-quick-section]', function (event) {
                event.preventDefault();
                loadSection(jQuery(this).attr('data-quick-section'));
            })
            .off('click.quickConsult', '#addPrescriptionItemRow')
            .on('click.quickConsult', '#addPrescriptionItemRow', function (event) {
                event.preventDefault();
                const composer = getPrescriptionComposer();
                if (!composer) {
                    return;
                }

                composer.addOrUpdateFromComposer((message, focusSelector) => {
                    notify('error', message);
                    const focusEl = document.querySelector(focusSelector);
                    if (focusEl) {
                        focusEl.focus();
                    }
                });
            })
            .off('click.quickConsult', '#cancelPrescriptionItemEdit')
            .on('click.quickConsult', '#cancelPrescriptionItemEdit', function (event) {
                event.preventDefault();
                const composer = getPrescriptionComposer();
                if (composer) {
                    composer.clearComposer();
                }
            })
            .off('click.quickConsult', '.edit-prescription-item-row')
            .on('click.quickConsult', '.edit-prescription-item-row', function (event) {
                event.preventDefault();
                const composer = getPrescriptionComposer();
                if (composer) {
                    composer.loadFromRow(jQuery(this).closest('tr.prescription-item-row'));
                }
            })
            .off('click.quickConsult', '.remove-prescription-item-row')
            .on('click.quickConsult', '.remove-prescription-item-row', function (event) {
                event.preventDefault();
                const composer = getPrescriptionComposer();
                if (composer) {
                    composer.removeRow(jQuery(this).closest('tr.prescription-item-row'));
                }
            })
            .off('click.quickConsult', '.quick-print-prescription')
            .on('click.quickConsult', '.quick-print-prescription', async function (event) {
                event.preventDefault();

                if (this.classList.contains('disabled')) {
                    return;
                }

                const printUrl = this.getAttribute('data-url') || '';
                if (!printUrl || printUrl === '#') {
                    notify('warning', 'Prescription print is not available yet.');
                    return;
                }

                try {
                    const response = await fetch(printUrl, {
                        method: 'GET',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });

                    if (!response.ok) {
                        notify('warning', 'Prescription not found for print.');
                        return;
                    }

                    window.open(printUrl, '_blank');
                } catch (error) {
                    notify('warning', 'Prescription print is not available yet.');
                }
            })
            .off('change.quickConsult', '#prescription_entry_medicine')
            .on('change.quickConsult', '#prescription_entry_medicine', function () {
                const composer = getPrescriptionComposer();
                if (composer) {
                    composer.onMedicineChanged(true);
                }
            })
            .off('select2:select.quickConsult', '#prescription_entry_dosage, #prescription_entry_instruction, #prescription_entry_frequency')
            .on('select2:select.quickConsult', '#prescription_entry_dosage, #prescription_entry_instruction, #prescription_entry_frequency', function () {
                const composer = getPrescriptionComposer();
                if (composer) {
                    composer.focusNextField(this.id);
                }
            })
            .off('keydown.quickConsult', '#prescription_entry_days')
            .on('keydown.quickConsult', '#prescription_entry_days', function (event) {
                if (event.key !== 'Tab' || event.shiftKey) {
                    return;
                }

                event.preventDefault();
                const addBtn = document.getElementById('addPrescriptionItemRow');
                if (addBtn) {
                    addBtn.focus();
                }
            })
            .off('keydown.quickConsult', '#opdPrescriptionForm')
            .on('keydown.quickConsult', '#opdPrescriptionForm', function (event) {
                const targetId = event.target && event.target.id ? event.target.id : '';
                if (event.key === 'Enter' && /^prescription_entry_/.test(targetId)) {
                    event.preventDefault();
                    const addBtn = document.getElementById('addPrescriptionItemRow');
                    if (addBtn) {
                        addBtn.click();
                    }
                    return;
                }

                if (event.altKey && (event.key === 'n' || event.key === 'N')) {
                    event.preventDefault();
                    const addBtn = document.getElementById('addPrescriptionItemRow');
                    if (addBtn) {
                        addBtn.click();
                    }
                }
            })
            .off('change.quickConsult', '#diagnostic_test_ids')
            .on('change.quickConsult', '#diagnostic_test_ids', function () {
                refreshDiagnosticPreview();
            })
            .off('select2:select.quickConsult select2:unselect.quickConsult', '#diagnostic_test_ids')
            .on('select2:select.quickConsult select2:unselect.quickConsult', '#diagnostic_test_ids', function () {
                refreshDiagnosticPreview();
            })
            .off('submit.quickConsult', '#opdPrescriptionForm')
            .on('submit.quickConsult', '#opdPrescriptionForm', function (event) {
                event.preventDefault();
                submitPrescription(this);
            })
            .off('submit.quickConsult', '#saveDiagnosticOrderForm')
            .on('submit.quickConsult', '#saveDiagnosticOrderForm', function (event) {
                event.preventDefault();
                submitDiagnostic(this);
            });

        if (modalEl) {
            modalEl.addEventListener('hidden.bs.modal', reset);
        }
    }

    function getState() {
        return { ...state };
    }

    bindEvents();

    return {
        open,
        close,
        getState,
    };
})();

const QueueDesk = (function () {
    let selectedDepartment = 'all';
    let lastQueueData = null;

    function normalizeDept(dept) {
        return (dept || 'General').toString().trim() || 'General';
    }

    function renderDeptFilters(deptNames) {
        const wrap = document.getElementById('dept-filter-wrap');
        if (!wrap) {
            return;
        }

        if (IS_DOCTOR_DASHBOARD) {
            wrap.innerHTML = '';
            return;
        }

        const allDepts = ['all', ...Array.from(new Set(deptNames)).sort((a, b) => a.localeCompare(b))];
        const deptCountMap = deptNames.reduce((acc, name) => {
            acc[name] = (acc[name] || 0) + 1;
            return acc;
        }, {});
        const totalCount = deptNames.length;

        if (selectedDepartment !== 'all' && !allDepts.includes(selectedDepartment)) {
            selectedDepartment = 'all';
        }

        wrap.innerHTML = `
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <span class="text-muted small me-1">Department:</span>
                ${allDepts.map((dept) => `
                    <button class="btn btn-sm ${selectedDepartment === dept ? 'btn-primary' : 'btn-outline-primary'}" data-dept-filter="${escapeAttr(dept)}">
                        ${dept === 'all' ? `All (${totalCount})` : `${escapeHtml(dept)} (${deptCountMap[dept] || 0})`}
                    </button>
                `).join('')}
            </div>
        `;
    }

    function findNextCurrentByDoctor(data, doctorId) {
        if (!data || !Array.isArray(data.current_list)) {
            return null;
        }

        const key = String(doctorId || '');
        if (key) {
            const sameDoctor = data.current_list.find((row) => String(row.doctor_id) === key);
            if (sameDoctor) {
                return sameDoctor;
            }
        }

        return data.current_list[0] || null;
    }

    function openQuickFromQueueRow(row) {
        if (!row) {
            return;
        }

        QuickConsult.open({
            opdPatientId: row.id,
            patientId: row.patient_id,
            doctorId: row.doctor_id,
            doctor: row.doctor,
            dept: row.dept,
            token: row.token,
            patientName: row.name,
            caseNo: row.case_no,
        });
    }

    function renderFromData(data) {
        if (!data) {
            return;
        }

        const rawCurrentList = data.current_list || (data.current ? [data.current] : []);
        const rawWaiting = data.queue || [];

        const deptNames = rawCurrentList.map((x) => normalizeDept(x.dept)).concat(rawWaiting.map((x) => normalizeDept(x.dept)));
        renderDeptFilters(deptNames);

        const currentList = selectedDepartment === 'all'
            ? rawCurrentList
            : rawCurrentList.filter((x) => normalizeDept(x.dept) === selectedDepartment);

        const waiting = selectedDepartment === 'all'
            ? rawWaiting
            : rawWaiting.filter((x) => normalizeDept(x.dept) === selectedDepartment);

        const inRoomBody = document.getElementById('in-room-body');
        const waitingWrap = document.getElementById('queue-table-wrap');
        const waitingCount = document.getElementById('waiting-count');

        if (!inRoomBody || !waitingWrap || !waitingCount) {
            return;
        }

        if (IS_DOCTOR_DASHBOARD) {
            const myCurrent = currentList[0] || null;
            const myDoctorId = myCurrent ? myCurrent.doctor_id : (waiting[0] ? waiting[0].doctor_id : null);
            const myDoctorName = myCurrent ? myCurrent.doctor : (waiting[0] ? waiting[0].doctor : 'Doctor');
            const myDept = myCurrent ? myCurrent.dept : (waiting[0] ? waiting[0].dept : '-');

            inRoomBody.innerHTML = myCurrent
                ? `
                    <div class="doctor-queue-card p-3 p-md-4">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                            <div>
                                <div class="text-muted small">In Room</div>
                                <div class="doctor-token-chip mt-1">${escapeHtml(myCurrent.token)}</div>
                                <div class="fw-semibold mt-2">${escapeHtml(myCurrent.name)}</div>
                                <div class="text-muted small">Case: ${escapeHtml(myCurrent.case_no)} | ${escapeHtml(myDept)}</div>
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                <button
                                    class="btn btn-outline-primary btn-sm open-quick-consult"
                                    data-opd-id="${myCurrent.id}"
                                    data-patient-id="${myCurrent.patient_id || ''}"
                                    data-doctor-id="${myCurrent.doctor_id || ''}"
                                    data-doctor="${escapeAttr(myCurrent.doctor || '')}"
                                    data-dept="${escapeAttr(myCurrent.dept || '')}"
                                    data-token="${escapeAttr(myCurrent.token || '')}"
                                    data-name="${escapeAttr(myCurrent.name || '')}"
                                    data-case="${escapeAttr(myCurrent.case_no || '')}"
                                >
                                    <i class="fa-solid fa-bolt me-1"></i>Quick Consult
                                </button>
                                <button class="btn btn-success btn-sm queue-call-next-btn" data-doctor-id="${myCurrent.doctor_id || ''}">
                                    <i class="fa-solid fa-forward-step me-1"></i>Complete & Call Next
                                </button>
                            </div>
                        </div>
                    </div>
                `
                : `
                    <div class="doctor-queue-card p-3 p-md-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <div class="fw-semibold">No patient is currently in room.</div>
                            <div class="text-muted small">Doctor: ${escapeHtml(myDoctorName)}${myDept && myDept !== '-' ? ` | ${escapeHtml(myDept)}` : ''}</div>
                        </div>
                        <button class="btn btn-primary btn-sm queue-call-next-btn" data-doctor-id="${myDoctorId || ''}">
                            <i class="fa-solid fa-forward-step me-1"></i>Call Next Patient
                        </button>
                    </div>
                `;

            waitingCount.textContent = `${waiting.length} Waiting`;

            if (waiting.length === 0) {
                waitingWrap.innerHTML = '<div class="text-center text-muted py-4">No patients in your waiting queue.</div>';
                return;
            }

            waitingWrap.innerHTML = `
                <div class="doctor-waiting-list">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width:90px">Token</th>
                                <th>Patient</th>
                                <th>Case</th>
                                <th class="text-end" style="width:170px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${waiting.map((q) => `
                                <tr class="${q.absent ? 'table-secondary opacity-50' : ''}">
                                    <td><span class="fs-5 fw-bold text-primary">${escapeHtml(q.token)}</span></td>
                                    <td>
                                        <div class="fw-semibold">${escapeHtml(q.name)}</div>
                                        <div class="text-muted small">${escapeHtml(q.age_gender || '')}</div>
                                    </td>
                                    <td class="text-muted small">${escapeHtml(q.case_no)}</td>
                                    <td class="text-end">
                                        ${q.absent
                                            ? `<button class="btn btn-outline-success btn-sm queue-undo-skip-btn" data-id="${q.id}"><i class="fa-solid fa-check me-1"></i>Mark Present</button>`
                                            : `<button class="btn btn-outline-danger btn-sm queue-skip-btn" data-id="${q.id}"><i class="fa-solid fa-person-circle-question me-1"></i>Not Present</button>`
                                        }
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;

            return;
        }

        const doctorLanes = new Map();
        currentList.forEach((item) => {
            doctorLanes.set(String(item.doctor_id), {
                doctor_id: item.doctor_id,
                doctor: item.doctor,
                dept: item.dept,
                current: item,
                waiting_count: 0,
            });
        });

        waiting.forEach((item) => {
            const key = String(item.doctor_id);
            if (!doctorLanes.has(key)) {
                doctorLanes.set(key, {
                    doctor_id: item.doctor_id,
                    doctor: item.doctor,
                    dept: item.dept,
                    current: null,
                    waiting_count: 0,
                });
            }
            doctorLanes.get(key).waiting_count += 1;
        });

        if (doctorLanes.size > 0) {
            inRoomBody.innerHTML = `
                <div class="row g-3">
                    ${Array.from(doctorLanes.values()).map((lane) => `
                        <div class="col-md-6 col-xl-4">
                            <div class="border rounded p-3 h-100 bg-light-subtle">
                                <div class="fw-bold">Dr. ${escapeHtml(lane.doctor)}</div>
                                <div class="text-muted small mb-2">${escapeHtml(lane.dept)}</div>

                                ${lane.current ? `
                                    <div class="d-flex align-items-end gap-3">
                                        <div class="display-5 fw-bolder text-warning lh-1">${escapeHtml(lane.current.token)}</div>
                                        <div>
                                            <div class="fw-semibold">${escapeHtml(lane.current.name)}</div>
                                            <div class="text-muted small">Case: ${escapeHtml(lane.current.case_no)}</div>
                                        </div>
                                    </div>
                                ` : '<div class="text-muted small mb-2">No patient in room</div>'}

                                <div class="d-flex justify-content-between align-items-center mt-3 gap-2">
                                    <span class="badge bg-secondary text-white">${lane.waiting_count} waiting</span>
                                    <div class="d-flex gap-2">
                                        ${lane.current ? `
                                            <button
                                                class="btn btn-outline-primary btn-sm open-quick-consult"
                                                data-opd-id="${lane.current.id}"
                                                data-patient-id="${lane.current.patient_id || ''}"
                                                data-doctor-id="${lane.doctor_id}"
                                                data-doctor="${escapeAttr(lane.doctor)}"
                                                data-dept="${escapeAttr(lane.dept)}"
                                                data-token="${escapeAttr(lane.current.token)}"
                                                data-name="${escapeAttr(lane.current.name)}"
                                                data-case="${escapeAttr(lane.current.case_no)}"
                                            >
                                                <i class="fa-solid fa-bolt me-1"></i>Quick Consult
                                            </button>
                                        ` : ''}
                                        <button class="btn btn-success btn-sm queue-call-next-btn" data-doctor-id="${lane.doctor_id}">
                                            <i class="fa-solid fa-forward-step me-1"></i>Complete & Call Next
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
        } else {
            inRoomBody.innerHTML = `
                <div class="d-flex align-items-center gap-3">
                    <span class="text-muted">No patient in room.</span>
                    <button class="btn btn-primary btn-sm queue-call-next-btn" data-doctor-id="">
                        <i class="fa-solid fa-forward-step me-1"></i>Call Next Patient
                    </button>
                </div>
            `;
        }

        waitingCount.textContent = `${waiting.length} Waiting`;

        if (waiting.length === 0) {
            waitingWrap.innerHTML = '<div class="text-center text-muted py-4">No patients in queue.</div>';
            return;
        }

        waitingWrap.innerHTML = `
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width:80px">Token</th>
                        <th>Patient</th>
                        <th>Doctor / Dept</th>
                        <th>Case No</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    ${waiting.map((q) => `
                        <tr class="${q.absent ? 'table-secondary opacity-50' : ''}">
                            <td><span class="fs-5 fw-bold text-primary">${escapeHtml(q.token)}</span></td>
                            <td>
                                <div class="fw-semibold">${escapeHtml(q.name)}</div>
                                <div class="text-muted small">${escapeHtml(q.age_gender || '')}</div>
                            </td>
                            <td class="text-muted small">Dr. ${escapeHtml(q.doctor)}<br>${escapeHtml(q.dept)}</td>
                            <td class="text-muted small">${escapeHtml(q.case_no)}</td>
                            <td class="text-end">
                                ${q.absent
                                    ? `<button class="btn btn-outline-success btn-sm queue-undo-skip-btn" data-id="${q.id}"><i class="fa-solid fa-check me-1"></i>Mark Present</button>`
                                    : `<button class="btn btn-outline-danger btn-sm queue-skip-btn" data-id="${q.id}"><i class="fa-solid fa-person-circle-question me-1"></i>Not Present</button>`
                                }
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;
    }

    async function fetchAndRender() {
        const response = await fetch(ROUTES.queueStatus, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        lastQueueData = await response.json();
        renderFromData(lastQueueData);
        return lastQueueData;
    }

    async function callNext(doctorId, btnEl, options = {}) {
        const autoOpenQuick = !!options.autoOpenQuick;
        const btn = btnEl || null;
        const originalHtml = btn ? btn.innerHTML : '';

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Wait...';
        }

        try {
            const body = doctorId ? { doctor_id: doctorId } : {};
            const result = await postJson(ROUTES.callNext, body);

            if (!result.status) {
                notify('error', result.message || 'Unable to call next patient.');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
                return;
            }

            notify('success', result.message || 'Next patient called.');
            const latestData = await fetchAndRender();

            if (autoOpenQuick) {
                const nextCurrent = findNextCurrentByDoctor(latestData, doctorId);
                if (nextCurrent) {
                    openQuickFromQueueRow(nextCurrent);
                } else {
                    QuickConsult.close();
                }
            } else {
                QuickConsult.close();
            }
        } catch (error) {
            notify('error', 'Unable to call next patient.');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        }
    }

    async function skipPatient(id) {
        const result = await postJson(routeWithId(ROUTES.skipPatient, id), {});
        if (!result.status) {
            notify('error', result.message || 'Unable to mark absent.');
            return;
        }
        await fetchAndRender();
    }

    async function undoSkip(id) {
        const result = await postJson(routeWithId(ROUTES.undoSkip, id), {});
        if (!result.status) {
            notify('error', result.message || 'Unable to mark present.');
            return;
        }
        await fetchAndRender();
    }

    function bindEvents() {
        if (!window.jQuery) {
            return;
        }

        jQuery(document)
            .off('click.queueDesk', '[data-dept-filter]')
            .on('click.queueDesk', '[data-dept-filter]', function () {
                selectedDepartment = jQuery(this).attr('data-dept-filter') || 'all';
                renderFromData(lastQueueData);
            })
            .off('click.queueDesk', '.open-quick-consult')
            .on('click.queueDesk', '.open-quick-consult', function (event) {
                event.preventDefault();
                QuickConsult.open({
                    opdPatientId: this.dataset.opdId,
                    patientId: this.dataset.patientId,
                    doctorId: this.dataset.doctorId,
                    doctor: this.dataset.doctor,
                    dept: this.dataset.dept,
                    token: this.dataset.token,
                    patientName: this.dataset.name,
                    caseNo: this.dataset.case,
                });
            })
            .off('click.queueDesk', '.queue-call-next-btn')
            .on('click.queueDesk', '.queue-call-next-btn', function (event) {
                event.preventDefault();
                const doctorId = this.dataset.doctorId || null;
                callNext(doctorId, this, { autoOpenQuick: false });
            })
            .off('click.queueDesk', '#quick-complete-next-btn')
            .on('click.queueDesk', '#quick-complete-next-btn', function (event) {
                event.preventDefault();
                const state = QuickConsult.getState();
                callNext(state.doctorId || null, this, { autoOpenQuick: true });
            })
            .off('click.queueDesk', '.queue-skip-btn')
            .on('click.queueDesk', '.queue-skip-btn', function () {
                skipPatient(this.dataset.id);
            })
            .off('click.queueDesk', '.queue-undo-skip-btn')
            .on('click.queueDesk', '.queue-undo-skip-btn', function () {
                undoSkip(this.dataset.id);
            });
    }

    bindEvents();

    return {
        fetchAndRender,
    };
})();

QueueDesk.fetchAndRender();
setInterval(() => QueueDesk.fetchAndRender(), 6000);
</script>
@endpush
