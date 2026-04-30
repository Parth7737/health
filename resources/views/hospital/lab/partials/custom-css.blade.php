<style>
    .lab-pane {
        padding: 16px;
    }

    .lab-pane[hidden] {
        display: none !important;
    }

    .lab-stat-note {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 2px;
    }

    .lab-kpi-value.loading {
        opacity: 0.6;
    }

    .lab-cell-stack {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .lab-mini-card {
        padding: 8px 10px;
        border: 1px solid #e3edf8;
        border-radius: 10px;
        background: #f8fbff;
    }

    .lab-mini-title {
        font-size: 12px;
        font-weight: 700;
        color: #153a5b;
    }

    .lab-mini-meta {
        margin-top: 4px;
        font-size: 11px;
        color: #607d94;
    }

    .lab-tag-row {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .lab-tag {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 4px 8px;
        font-size: 11px;
        font-weight: 600;
        line-height: 1;
        background: #eef5fb;
        color: #275274;
    }

    .lab-tag-priority-routine {
        background: #ecf5ec;
        color: #256b2e;
    }

    .lab-tag-priority-urgent {
        background: #fff2e2;
        color: #a55a00;
    }

    .lab-tag-priority-stat {
        background: #fdeaea;
        color: #b3261e;
    }

    .lab-action-grid {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .lab-action-row {
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding: 10px 12px;
        border: 1px solid #edf2f7;
        border-radius: 10px;
        background: #fff;
    }

    .lab-action-row-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .lab-action-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .lab-inline-note {
        font-size: 11px;
        color: #607d94;
    }

    /* Status Stepper */
    .lab-stepper {
        display: flex;
        align-items: center;
        gap: 0;
        width: 100%;
        margin-top: 4px;
    }

    .lab-step {
        display: flex;
        align-items: center;
        flex: 1;
        gap: 0;
    }

    .lab-step-dot {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        border: 2px solid #ccc;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 9px;
        font-weight: 700;
        color: #999;
        flex-shrink: 0;
        position: relative;
        z-index: 1;
    }

    .lab-step-dot.done {
        background: #22c55e;
        border-color: #22c55e;
        color: #fff;
    }

    .lab-step-dot.active {
        background: #3b82f6;
        border-color: #3b82f6;
        color: #fff;
        box-shadow: 0 0 0 3px rgba(59,130,246,.2);
    }

    .lab-step-connector {
        flex: 1;
        height: 2px;
        background: #e2e8f0;
    }

    .lab-step-connector.done {
        background: #22c55e;
    }

    .lab-step-label {
        font-size: 9px;
        color: #94a3b8;
        text-align: center;
        margin-top: 3px;
        white-space: nowrap;
    }

    .lab-step-label.active { color: #3b82f6; font-weight: 700; }
    .lab-step-label.done   { color: #22c55e; font-weight: 600; }

    .lab-step-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex-shrink: 0;
    }

    /* Urgent pane: screenshot-like card micro-tuning */
    .lab-urgent-card {
        background: var(--surface);
        border: 1.5px solid rgba(198,40,40,.2);
        border-radius: 10px;
        padding: 14px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .lab-urgent-main {
        flex: 1;
        min-width: 0;
    }

    .lab-urgent-head {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 4px;
    }

    .lab-urgent-patient {
        font-size: 13px;
        font-weight: 700;
        color: #14263d;
        line-height: 1.25;
    }

    .lab-urgent-tests {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 2px;
    }

    .lab-urgent-meta,
    .lab-urgent-status {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 4px;
        line-height: 1.25;
    }

    .lab-urgent-tat {
        text-align: center;
        flex-shrink: 0;
        min-width: 82px;
    }

    .lab-urgent-elapsed {
        font-size: 32px;
        font-weight: 900;
        line-height: 1;
        color: var(--danger);
        letter-spacing: -0.5px;
    }

    .lab-urgent-tat-note {
        font-size: 10px;
        color: var(--text-muted);
        line-height: 1.2;
        margin-top: 2px;
    }

    .lab-urgent-action {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
    }

    /* Result entry pane: screenshot-style search and load flow */
    .lab-result-match-card {
        border: 1px solid #dfe8f2;
        border-radius: 10px;
        background: #fff;
        padding: 10px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        cursor: pointer;
    }

    .lab-result-match-card:hover {
        border-color: #b9d0e8;
        background: #f9fcff;
    }

    .lab-result-match-card.active {
        border-color: #1565c0;
        background: #f1f7ff;
        box-shadow: 0 0 0 2px rgba(21,101,192,.1);
    }

    .lab-result-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 3px 9px;
        font-size: 10px;
        font-weight: 700;
        line-height: 1;
    }

    .lab-result-chip-inprogress { background: #e3f2fd; color: #1565c0; }
    .lab-result-chip-completed { background: #e8f5e9; color: #2e7d32; }
    .lab-result-chip-pending { background: #fff3e0; color: #ef6c00; }

    .lab-result-selected {
        border: 1.5px solid #d5e4f3;
        border-radius: 12px;
        background: #fff;
        padding: 14px;
        margin-bottom: 12px;
    }

    .lab-result-selected-title {
        font-size: 13px;
        font-weight: 700;
        color: #14324d;
        margin-bottom: 6px;
    }

    .lab-result-selected-meta {
        font-size: 11px;
        color: #5f7a91;
        margin-bottom: 8px;
    }

    /* Critical values card styling (screenshot parity) */
    .lab-critical-card {
        background: #fff5f5;
        border: 2px solid var(--danger);
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 12px;
    }

    .lab-critical-wrap {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
    }

    .lab-critical-main { flex: 1; min-width: 0; }

    .lab-critical-head {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 6px;
    }

    .lab-critical-name {
        font-size: 14px;
        font-weight: 700;
        color: var(--danger);
    }

    .lab-critical-sub {
        font-size: 12px;
        color: var(--text-muted);
    }

    .lab-critical-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 12px;
    }

    .lab-critical-grid .label {
        font-size: 11px;
        color: var(--text-muted);
        display: block;
    }

    .lab-critical-grid .value {
        font-size: 13px;
        font-weight: 700;
    }

    .lab-critical-grid .value.danger {
        color: var(--danger);
    }

    .lab-critical-flag {
        background: var(--danger);
        color: #fff;
        padding: 3px 10px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 700;
        display: inline-block;
    }

    .lab-critical-actions {
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .lab-report-sample {
        font-size: 12px;
        font-weight: 700;
        color: #1b4f87;
    }

    .lab-report-pane-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
        flex-wrap: wrap;
    }

    .lab-report-pane-title {
        font-size: 14px;
        font-weight: 700;
        color: #14263d;
        line-height: 1.2;
    }

    .lab-report-filter-bar {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-left: auto;
    }

    .lab-report-date-input {
        width: 140px;
        min-width: 140px;
        background: #fff;
    }

    .lab-report-filter-btn {
        min-width: 84px;
        justify-content: center;
    }

    .lab-report-table-shell {
        border: 1px solid #dbe6f2;
        border-radius: 14px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 2px 10px rgba(15, 42, 69, 0.04);
    }

    #reportsPane .dataTables_wrapper {
        padding: 0;
    }

    #reportsPane .dataTables_scrollHead,
    #reportsPane .dataTables_scrollHeadInner,
    #reportsPane .dataTables_scrollHeadInner table,
    #reportsPane .dataTables_scrollBody table {
        width: 100% !important;
    }

    #reportsPane table.dataTable {
        margin-top: 0 !important;
        margin-bottom: 0 !important;
        border-collapse: separate;
        border-spacing: 0;
    }

    #reportsPane table.dataTable thead th {
        background: #edf3f9;
        color: #6c87a0;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.02em;
        border-bottom: 1px solid #d9e5f2;
        padding: 11px 12px;
        white-space: nowrap;
    }

    #reportsPane table.dataTable tbody td {
        padding: 9px 12px;
        border-bottom: 1px solid #eef3f8;
        vertical-align: middle;
        background: #fff;
    }

    #reportsPane table.dataTable tbody tr:nth-child(even) td {
        background: #f8fbff;
    }

    #reportsPane table.dataTable tbody tr:hover td {
        background: #f3f8fd;
    }

    #reportsPane .dataTables_paginate,
    #reportsPane .dataTables_info {
        padding: 10px 12px 0;
    }

    .lab-report-patient {
        font-size: 12px;
        font-weight: 700;
        color: #12293d;
    }

    .lab-report-tests,
    .lab-report-key {
        font-size: 12px;
        color: #2f4d68;
    }

    .lab-report-time {
        font-size: 12px;
        color: #607d94;
    }

    .lab-report-status {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 3px 9px;
        font-size: 10px;
        font-weight: 700;
        background: #e8f5e9;
        color: #2e7d32;
        text-transform: lowercase;
    }

    .lab-report-status-printed {
        background: #e7f0ff;
        color: #215da8;
    }

    .lab-report-status-dispatched {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .lab-report-actions {
        display: flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
    }

    .lab-report-actions .btn {
        min-width: 32px;
        padding-inline: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    @media (max-width: 767.98px) {
        .lab-report-filter-bar {
            width: 100%;
            margin-left: 0;
        }

        .lab-report-date-input,
        .lab-report-filter-btn {
            width: 100%;
            min-width: 0;
        }
    }
    
</style>