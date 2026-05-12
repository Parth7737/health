<style>
    .add-datamodal .modal-dialog.hrx-recruitment-modal-dialog {
        max-width: 760px;
        width: 92%;
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
    .hrx-recruitment-modal-content .modal-body { padding: 24px; }
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
</style>
