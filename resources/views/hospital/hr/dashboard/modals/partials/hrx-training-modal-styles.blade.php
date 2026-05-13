<style>
    .hrx-training-modal-content .modal-header {
        border-bottom: 1px solid #e8e0f0;
        padding: 18px 22px 14px;
        background: linear-gradient(135deg, #faf7ff 0%, #fff 55%);
    }
    .hrx-training-modal-content .modal-header h2 {
        font-size: 20px;
        font-weight: 800;
        color: #1a0d2e;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
        letter-spacing: -0.02em;
    }
    .hrx-training-modal-content .modal-body { padding: 20px 22px 24px; }
    .hrx-training-modal-content .modal-footer {
        border-top: 1px solid #e8e0f0;
        padding: 14px 22px;
        background: #fafbfd;
    }
    .hrx-training-modal-content .form-label {
        font-size: 12px;
        font-weight: 700;
        color: #5a7894;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: 6px;
    }
    .hrx-training-modal-content .form-control {
        border-radius: 10px;
        border: 1.5px solid #d8dee9;
    }
    .hrx-training-modal-content .form-control:focus {
        border-color: #4a148c;
        box-shadow: 0 0 0 3px rgba(74, 20, 140, 0.12);
    }
    .hrx-training-modal-content .btn-training-submit {
        background: linear-gradient(135deg, #4a148c, #6a1b9a);
        border: none;
        color: #fff;
        font-weight: 700;
        padding: 10px 22px;
        border-radius: 10px;
        letter-spacing: .02em;
    }
    .hrx-training-modal-content .btn-training-submit:hover { filter: brightness(1.06); color: #fff; }
    .hrx-training-hero {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 18px;
        padding: 16px 18px;
        border-radius: 14px;
        border: 1px solid #e8e0f0;
        background: linear-gradient(120deg, #f3e8ff 0%, #fff 40%, #f8fafc 100%);
    }
    .hrx-training-hero-title { font-size: 18px; font-weight: 800; color: #1a0d2e; margin: 0 0 6px; max-width: 720px; }
    .hrx-training-hero-meta { font-size: 13px; color: #5a7894; font-weight: 600; }
    .hrx-training-summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 12px;
        margin-bottom: 20px;
    }
    .hrx-training-summary-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 14px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }
    .hrx-training-summary-card .lbl {
        font-size: 10px;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: .06em;
    }
    .hrx-training-summary-card .val { font-size: 14px; font-weight: 700; color: #0f172a; margin-top: 6px; line-height: 1.35; }
    .hrx-training-section {
        margin-top: 22px;
        padding-top: 18px;
        border-top: 1px solid #eef2f7;
    }
    .hrx-training-section-title {
        font-size: 13px;
        font-weight: 800;
        color: #4a148c;
        margin: 0 0 14px;
        display: flex;
        align-items: center;
        gap: 10px;
        text-transform: uppercase;
        letter-spacing: .05em;
    }
    .hrx-training-section-title i { font-size: 15px; opacity: 0.9; }
    .hrx-training-table { width: 100%; font-size: 13px; border-collapse: separate; border-spacing: 0; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; }
    .hrx-training-table th {
        background: linear-gradient(180deg, #f0f4fa 0%, #e8edf5 100%);
        color: #334155;
        font-weight: 800;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .05em;
        padding: 12px 14px;
        border-bottom: 1px solid #d8dee9;
        text-align: left;
    }
    .hrx-training-table td { padding: 12px 14px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; background: #fff; }
    .hrx-training-table tbody tr:last-child td { border-bottom: none; }
    .hrx-training-add-row { display: flex; flex-wrap: wrap; gap: 10px; align-items: flex-end; margin-bottom: 14px; }
    .hrx-training-add-row select { min-width: 260px; border-radius: 10px; }
    .hrx-training-status-row { display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end; margin-top: 6px; }
    .hrx-training-status-row select { min-width: 200px; border-radius: 10px; }
    .hrx-training-log-list { margin: 0; padding: 0; list-style: none; }
    .hrx-training-log-item {
        position: relative;
        padding: 14px 16px 14px 20px;
        margin-bottom: 12px;
        border-radius: 12px;
        border: 1px solid #e8e0f0;
        background: #fff;
        border-left: 4px solid #4a148c;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
    }
    .hrx-training-log-item.ev-status { border-left-color: #1565c0; }
    .hrx-training-log-item.ev-participant { border-left-color: #2e7d32; }
    .hrx-training-log-item.ev-cert { border-left-color: #6a1b9a; }
    .hrx-training-log-item.ev-cancel { border-left-color: #c62828; }
    .hrx-training-log-badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .06em;
        background: #f3e8ff;
        color: #4a148c;
        margin-bottom: 8px;
    }
    .hrx-training-log-when { font-size: 12px; font-weight: 800; color: #0f172a; }
    .hrx-training-log-msg { font-size: 14px; color: #334155; margin-top: 6px; line-height: 1.5; }
    .hrx-training-log-note { font-size: 13px; color: #475569; margin-top: 8px; white-space: pre-wrap; padding: 10px 12px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; }
    .hrx-training-log-by { font-size: 11px; color: #94a3b8; margin-top: 10px; font-weight: 600; }
    .hrx-training-cert-actions { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; }
    .hrx-training-cert-actions .hrx-btn-lite { font-size: 11px; padding: 5px 10px; border-radius: 8px; }
</style>
