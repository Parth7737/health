<style>
    .add-datamodal .modal-dialog.hrx-recruitment-modal-dialog {
        max-width: 760px;
        width: 92%;
    }
    .add-datamodal .modal-dialog.hrx-recruitment-modal-dialog.hrx-recruitment-modal-dialog-lg {
        max-width: 1140px;
        width: 96%;
    }
    .add-datamodal .modal-content.hrx-recruitment-modal-content {
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #ccd8e8;
        box-shadow: 0 20px 60px rgba(0,0,0,.2);
    }
    .hrx-recruitment-modal-content .modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid #ccd8e8;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #fff;
    }
    .hrx-recruitment-modal-content .modal-header h2 {
        font-size: 16px;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
        color: #0d1b2a;
    }
    .hrx-recruitment-modal-content .btn-close {
        background: none;
        border: none;
        color: #5a7894;
        opacity: 1;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        box-shadow: none;
    }
    .hrx-recruitment-modal-content .btn-close:hover {
        background: #e8f5e9;
        color: #2e7d32;
    }
    .hrx-recruitment-modal-content .modal-body { padding: 24px; max-height: min(78vh, 900px); overflow-y: auto; }
    .hrx-recruitment-modal-content .modal-footer {
        padding: 16px 24px;
        border-top: 1px solid #ccd8e8;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        background: #fafbfd;
    }
    .hrx-recruitment-modal-content .form-label {
        font-size: 12px;
        font-weight: 600;
        color: #5a7894;
        margin-bottom: 4px;
    }
    .hrx-recruitment-modal-content .form-control,
    .hrx-recruitment-modal-content .form-select {
        padding: 9px 12px;
        border: 1.5px solid #ccd8e8;
        border-radius: 8px;
        font-size: 13px;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
    }
    .hrx-recruitment-modal-content .form-control:focus,
    .hrx-recruitment-modal-content .form-select:focus {
        border-color: #2e7d32;
        box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.12);
    }
    .hrx-recruitment-modal-content .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all .22s cubic-bezier(.4,0,.2,1);
    }
    .hrx-recruitment-modal-content .btn-outline {
        background: #fff;
        color: #4a148c;
        border: 1.5px solid #4a148c;
    }
    .hrx-recruitment-modal-content .btn-outline:hover {
        background: #f3e5f5;
        color: #3b0070;
    }
    .hrx-recruitment-modal-content .btn-recruit-submit {
        background: linear-gradient(135deg, #2e7d32, #43a047);
        color: #fff;
        border: 1px solid #1b5e20;
        box-shadow: 0 4px 12px rgba(46, 125, 50, 0.35);
    }
    .hrx-recruitment-modal-content .btn-recruit-submit:hover {
        background: linear-gradient(135deg, #1b5e20, #2e7d32);
        color: #fff;
        transform: translateY(-1px);
    }
    .hrx-recruitment-modal-content .btn-recruit-submit:active {
        transform: translateY(0);
    }
    .hrx-recruitment-modal-content .btn-sm.btn-recruit-save {
        background: linear-gradient(135deg, #2e7d32, #43a047);
        color: #fff;
        border: none;
        font-weight: 600;
        border-radius: 6px;
    }
    .hrx-recruitment-modal-content .btn-sm.btn-recruit-save:hover {
        background: linear-gradient(135deg, #1b5e20, #2e7d32);
        color: #fff;
    }

    /* Vacancy view — summary strip */
    .hrx-rv-summary {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 12px;
        margin-bottom: 20px;
    }
    .hrx-rv-summary-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 14px;
    }
    .hrx-rv-summary-card .hrx-rv-label {
        display: block;
        font-size: 11px;
        font-weight: 600;
        color: #5a7894;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: 4px;
    }
    .hrx-rv-summary-card .hrx-rv-value {
        font-size: 14px;
        font-weight: 700;
        color: #0d1b2a;
    }

    /* Applicants table */
    .hrx-recruitment-modal-content .hrx-recruitment-applicants-wrap {
        border: 1px solid #ccd8e8;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
    }
    .hrx-recruitment-modal-content .hrx-recruitment-applicants-table {
        width: 100%;
        margin: 0;
        font-size: 13px;
        border-collapse: collapse;
    }
    .hrx-recruitment-modal-content .hrx-recruitment-applicants-table thead th {
        background: #f0f4fa;
        color: #3d5a73;
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .03em;
        padding: 12px 14px;
        border-bottom: 1px solid #ccd8e8;
        white-space: nowrap;
    }
    .hrx-recruitment-modal-content .hrx-recruitment-applicants-table tbody tr.hrx-app-main-row td {
        border-bottom: 1px solid #eef3f8;
    }
    .hrx-recruitment-applicant-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
    }
    .hrx-recruitment-modal-content .hrx-recruitment-applicant-view-icon-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        padding: 0;
        border-radius: 999px;
        font-size: 15px;
        border: 1.5px solid #94a3b8;
        background: #fff;
        color: #475569;
        cursor: pointer;
        transition: background .2s, border-color .2s, color .2s;
    }
    .hrx-recruitment-modal-content .hrx-recruitment-applicant-view-icon-btn:hover {
        background: #f1f5f9;
        border-color: #4a148c;
        color: #4a148c;
    }
    .hrx-app-timeline-heading {
        font-size: 11px;
        font-weight: 700;
        color: #5a7894;
        text-transform: uppercase;
        letter-spacing: .05em;
        margin: 12px 0 10px;
    }
    .hrx-app-timeline {
        margin: 0;
        padding: 0 0 4px 0;
        list-style: none;
        border-left: 2px solid #ccd8e8;
        margin-left: 8px;
    }
    .hrx-app-timeline-item {
        position: relative;
        padding-left: 18px;
        margin-bottom: 14px;
    }
    .hrx-app-timeline-item:last-child { margin-bottom: 0; }
    .hrx-app-timeline-item::before {
        content: '';
        position: absolute;
        left: -7px;
        top: 4px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #4a148c;
        border: 2px solid #fff;
        box-shadow: 0 0 0 1px #ccd8e8;
    }
    .hrx-app-timeline-when {
        font-size: 12px;
        font-weight: 700;
        color: #0d1b2a;
        margin-bottom: 4px;
    }
    .hrx-app-timeline-change {
        font-size: 13px;
        color: #334155;
        margin-bottom: 4px;
    }
    .hrx-app-timeline-change strong { color: #4a148c; }
    .hrx-app-timeline-note {
        font-size: 13px;
        color: #1e293b;
        white-space: pre-wrap;
        line-height: 1.45;
        padding: 8px 10px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        margin-top: 6px;
    }
    .hrx-app-timeline-by {
        font-size: 11px;
        color: #64748b;
        margin-top: 6px;
    }
    .hrx-recruitment-modal-content .hrx-recruitment-applicants-table .hrx-badge {
        display: inline-flex;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
    }
    .hrx-recruitment-modal-content .hrx-recruitment-applicants-table .hrx-badge.app-applied { background: #e3f2fd; color: #1565c0; }
    .hrx-recruitment-modal-content .hrx-recruitment-applicants-table .hrx-badge.app-screening { background: #fff8e1; color: #f57f17; }
    .hrx-recruitment-modal-content .hrx-recruitment-applicants-table .hrx-badge.app-shortlisted { background: #e8f5e9; color: #2e7d32; }
    .hrx-recruitment-modal-content .hrx-recruitment-applicants-table .hrx-badge.app-interview { background: #f3e5f5; color: #6a1b9a; }
    .hrx-recruitment-modal-content .hrx-recruitment-applicants-table .hrx-badge.app-selected { background: #e0f7fa; color: #00838f; }
    .hrx-recruitment-modal-content .hrx-recruitment-applicants-table .hrx-badge.app-rejected { background: #ffebee; color: #c62828; }
    .hrx-recruitment-modal-content .hrx-recruitment-applicants-table .hrx-badge.app-hired { background: #e8f5e9; color: #1b5e20; }

    .hrx-recruitment-modal-content .hrx-recruitment-resume-link {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid #bfdbfe;
        background: #eff6ff;
        color: #1d4ed8;
        text-decoration: none;
    }
    .hrx-recruitment-modal-content .hrx-recruitment-resume-link:hover {
        background: #dbeafe;
        color: #1e3a8a;
    }

    .hrx-recruitment-modal-content .hrx-recruitment-status-popup-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        border: 1.5px solid #4a148c;
        background: #fff;
        color: #4a148c;
        cursor: pointer;
        transition: background .2s, border-color .2s, color .2s;
    }
    .hrx-recruitment-modal-content .hrx-recruitment-status-popup-btn:hover {
        background: #f3e8ff;
        border-color: #3b0070;
        color: #3b0070;
    }

    .hrx-recruitment-applicants-heading {
        font-size: 14px;
        font-weight: 700;
        color: #0d1b2a;
        margin: 0 0 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .hrx-recruitment-applicants-heading i { color: #2e7d32; }

    /* SweetAlert2 — applicant status popup */
    .hrx-recruitment-status-popup .swal2-html-container { text-align: left !important; padding-top: 0.5rem !important; }
    .hrx-recruitment-status-popup .hrx-swal-field { margin-bottom: 14px; }
    .hrx-recruitment-status-popup .hrx-swal-field label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #5a7894;
        margin-bottom: 6px;
    }
    .hrx-recruitment-status-popup .hrx-swal-field select,
    .hrx-recruitment-status-popup .hrx-swal-field textarea {
        width: 100%;
        border: 1.5px solid #ccd8e8;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 13px;
        font-family: inherit;
    }
    .hrx-recruitment-status-popup .hrx-swal-field select:focus,
    .hrx-recruitment-status-popup .hrx-swal-field textarea:focus {
        outline: none;
        border-color: #4a148c;
        box-shadow: 0 0 0 3px rgba(74, 20, 140, 0.12);
    }

    /* SweetAlert2 — applicant detail & history */
    .hrx-recruitment-applicant-view-popup.swal2-popup {
        max-width: 520px;
    }
    .hrx-recruitment-applicant-view-popup .swal2-html-container {
        text-align: left !important;
        padding-top: 0.35rem !important;
        max-height: min(70vh, 520px);
        overflow-y: auto;
    }
    .hrx-recruitment-applicant-view-popup .hrx-app-detail-summary {
        font-size: 13px;
        color: #334155;
        line-height: 1.55;
        margin-bottom: 14px;
        padding: 12px 14px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
    }
    .hrx-recruitment-applicant-view-popup .hrx-app-detail-summary a {
        color: #1d4ed8;
        font-weight: 600;
    }
    .hrx-recruitment-applicant-view-popup .hrx-app-detail-row { margin-bottom: 6px; }
    .hrx-recruitment-applicant-view-popup .hrx-app-detail-label {
        font-size: 11px;
        font-weight: 700;
        color: #5a7894;
        text-transform: uppercase;
        letter-spacing: .04em;
        display: block;
        margin-bottom: 2px;
    }
</style>
