<style>
.doctor-care-unified-modal {
    background: #ffffff;
    min-height: 100%;
    padding: 0;
}

.doctor-care-chip {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    border: 1px solid #d6e4f3;
    border-radius: 10px;
    background: #f8fbff;
    padding: 10px 12px;
    margin-bottom: 14px;
}

.doctor-care-chip-left {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
}

.doctor-care-chip-avatar {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: linear-gradient(135deg, #1f64b8, #9ec6f2);
    color: #0b355e;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 34px;
    font-size: 13px;
}

.doctor-care-chip-name {
    margin: 0;
    font-size: 15px;
    font-weight: 700;
    color: #0d1b2a;
    line-height: 1.2;
}

.doctor-care-chip-meta {
    margin: 2px 0 0;
    font-size: 11px;
    color: #61809f;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
}

.doctor-care-chip-status {
    border: 1px solid #f8d4a2;
    border-radius: 999px;
    font-size: 10px;
    font-weight: 700;
    color: #8a4b00;
    background: #fff6e9;
    padding: 3px 9px;
    white-space: nowrap;
}

.doctor-care-chip-right {
    display: flex;
    align-items: center;
    gap: 6px;
}

.doctor-care-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    align-items: start;
}

.doctor-care-card {
    border: 1px solid #dbe8f5;
    border-radius: 11px;
    overflow: hidden;
    background: #ffffff;
}

.doctor-care-card-head {
    border-bottom: 1px solid #e3edf8;
    padding: 10px 14px;
    font-size: 14px;
    font-weight: 700;
    color: #0d1b2a;
    background: #f8fbff;
    min-height: 44px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}

.doctor-care-card-body {
    padding: 12px 14px;
}

.doctor-care-col-stack {
    display: grid;
    gap: 14px;
}

.doctor-care-form-group {
    margin-bottom: 10px;
}

.doctor-care-form-group:last-child {
    margin-bottom: 0;
}

.doctor-care-form-group label {
    font-size: 12px;
    color: #2c4460;
    font-weight: 600;
    margin-bottom: 4px;
    display: block;
}

.doctor-care-unified-modal .form-control {
    height: 34px;
    padding: 7px 10px;
    font-size: 12px;
    line-height: 1.4;
    border: 1px solid #cfdceb;
    border-radius: 5px;
    color: #0d1b2a;
    background: #ffffff;
}

.doctor-care-unified-modal select.form-control {
    padding-right: 28px;
}

.doctor-care-unified-modal textarea.form-control {
    height: auto;
    min-height: 58px;
    padding-top: 7px;
    padding-bottom: 7px;
    resize: vertical;
}

.doctor-care-unified-modal .btn-xs {
    height: 24px;
    min-height: 24px;
    padding: 3px 10px;
    font-size: 11px;
    border-radius: 4px;
    line-height: 1;
}

.doctor-care-unified-modal .btn-sm {
    height: 28px;
    min-height: 28px;
    padding: 5px 12px;
    font-size: 11.5px;
    line-height: 1;
}

.doctor-care-unified-modal .doctor-care-actions .btn {
    height: 34px;
    padding: 7px 14px;
    font-size: 12px;
    line-height: 1;
}

.doctor-care-section-title {
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.doctor-care-head-action {
    font-size: 11px;
    font-weight: 700;
    border-radius: 4px;
    line-height: 1;
    padding: 5px 9px;
    height: 24px;
}

.doctor-care-vitals-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
}

.doctor-care-summary {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 8px 12px;
    font-size: 12px;
}

.doctor-care-summary strong {
    color: #113a63;
    margin-right: 4px;
}

.doctor-care-slot-note {
    font-size: 12px;
    color: #5e7d9d;
    margin-bottom: 8px;
}

.doctor-care-test-block {
    border: 1px solid #d6e4f1;
    border-radius: 8px;
    background: #f8fbff;
    padding: 10px;
    margin-bottom: 10px;
    display: none;
}

.doctor-care-test-block.is-open {
    display: block;
}

.doctor-care-test-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 10px;
    align-items: end;
}

.doctor-care-test-grid .doctor-care-form-group {
    margin-bottom: 0;
}

.doctor-care-test-added {
    padding: 8px 10px;
    min-height: 40px;
    font-size: 12px;
    color: #6a84a0;
    border: 1px solid #d6e4f1;
    border-radius: 6px;
    background: #ffffff;
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.doctor-care-test-added .doctor-care-empty-text {
    color: #7a93ad;
}

.doctor-care-test-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border-radius: 999px;
    background: #e7f1ff;
    color: #0f56a5;
    border: 1px solid #bfd7f5;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 10px;
}

.doctor-care-test-remove {
    border: 0;
    background: transparent;
    color: #0f56a5;
    font-size: 11px;
    line-height: 1;
    padding: 0;
    cursor: pointer;
    font-weight: 700;
}

.doctor-care-hidden-slot {
    display: none;
}

.doctor-care-unified-modal #doctorUnifiedPrescriptionSlot .prescription-workspace,
.doctor-care-unified-modal #doctorUnifiedPathologySlot .prescription-workspace,
.doctor-care-unified-modal #doctorUnifiedRadiologySlot .prescription-workspace {
    background: transparent;
    border-radius: 0;
    padding: 0;
}

.doctor-care-unified-modal #doctorUnifiedPrescriptionSlot .prescription-meta-card,
.doctor-care-unified-modal #doctorUnifiedPathologySlot .prescription-meta-card,
.doctor-care-unified-modal #doctorUnifiedRadiologySlot .prescription-meta-card {
    border: 0;
    border-radius: 0;
    padding: 0;
    background: transparent;
}

.doctor-care-unified-modal #doctorUnifiedPrescriptionSlot .prescription-items-shell {
    display: grid;
    gap: 12px;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.doctor-care-unified-modal #doctorUnifiedPrescriptionSlot .prescription-entry-grid {
    display: none !important;
    gap: 10px !important;
    margin-bottom: 8px;
    grid-auto-flow: row;
}

.doctor-care-unified-modal #doctorUnifiedPrescriptionSlot .prescription-entry-grid.is-open {
    display: grid !important;
    grid-template-columns: 3fr 1.6fr 2fr 2fr 2fr 1fr auto !important;
    grid-auto-flow: row;
    gap: 10px !important;
    align-items: start;
    margin-bottom: 12px;
}

.doctor-care-unified-modal #doctorUnifiedPrescriptionSlot .prescription-entry-grid>div {
    min-width: 0 !important;
    grid-column: auto !important;
}

.doctor-care-unified-modal #doctorUnifiedPrescriptionSlot .prescription-entry-grid .form-label {
    font-size: 11px;
    margin-bottom: 3px !important;
    min-height: 16px;
}

.doctor-care-unified-modal #doctorUnifiedPrescriptionSlot .prescription-entry-grid .form-control,
.doctor-care-unified-modal #doctorUnifiedPrescriptionSlot .prescription-entry-grid .form-select {
    height: 32px;
    font-size: 11.5px;
    border-color: #cfdceb;
}

.doctor-care-unified-modal #doctorUnifiedPrescriptionSlot .prescription-entry-actions .d-flex {
    height: 32px;
    align-items: center;
}

.doctor-care-unified-modal #doctorUnifiedPrescriptionSlot .prescription-entry-actions .prescription-icon-btn {
    width: 32px;
    height: 32px;
}

.doctor-care-unified-modal #doctorUnifiedPrescriptionSlot #prescriptionItemsTable {
    border: 1px solid #d6e4f1;
    border-radius: 7px;
    overflow: hidden;
    font-size: 11px;
}

.doctor-care-unified-modal #doctorUnifiedPrescriptionSlot #prescriptionItemsTable thead th {
    background: #edf3f9;
    color: #56718d;
    border-color: #d6e4f1;
    text-transform: uppercase;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .02em;
    padding: 7px 8px;
}

.doctor-care-unified-modal #doctorUnifiedPrescriptionSlot #prescriptionItemsTable tbody td {
    border-color: #e4edf6;
    padding: 7px 8px;
    font-size: 11px;
}

.doctor-care-unified-modal #doctorUnifiedPrescriptionSlot .prescription-row-actions .btn {
    width: 24px;
    height: 24px;
    padding: 0;
    border-radius: 4px;
    font-size: 11px;
}

.doctor-care-unified-modal #doctorUnifiedPrescriptionSlot .prescription-notes-grid {
    display: none;
}

.doctor-care-entry-flash {
    animation: doctor-care-entry-flash .6s ease;
}

@keyframes doctor-care-entry-flash {
    0% {
        box-shadow: 0 0 0 0 rgba(44, 109, 182, 0.35);
    }

    100% {
        box-shadow: 0 0 0 10px rgba(44, 109, 182, 0);
    }
}

.doctor-care-unified-modal .doctor-unified-diagnostic-form .form-label {
    font-size: 11px;
    margin-bottom: 4px;
    color: #2c4460;
    font-weight: 600;
}

.doctor-care-unified-modal .doctor-unified-diagnostic-form .table th,
.doctor-care-unified-modal .doctor-unified-diagnostic-form .table td {
    font-size: 11px;
    padding: 6px 8px;
}

.doctor-care-actions {
    margin-top: 16px;
    display: flex;
    gap: 8px;
    justify-content: flex-end;
    border-top: 1px solid #e3edf8;
    padding-top: 10px;
    background: #ffffff;
}

@media (max-width: 991.98px) {
    .doctor-care-grid {
        grid-template-columns: 1fr;
    }

    .doctor-care-unified-modal #doctorUnifiedPrescriptionSlot .prescription-entry-grid,
    .doctor-care-unified-modal #doctorUnifiedPrescriptionSlot .prescription-entry-grid.is-open {
        min-width: 0;
        grid-template-columns: 1fr !important;
    }

    .doctor-care-vitals-grid,
    .doctor-care-summary,
    .doctor-care-test-grid {
        grid-template-columns: 1fr;
    }
}
</style>
