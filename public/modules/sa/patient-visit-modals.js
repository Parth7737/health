(function () {
  const debounce = window.pmDebounce || ((fn, delay = 300) => {
    let timer;
    return (...args) => {
      clearTimeout(timer);
      timer = setTimeout(() => fn(...args), delay);
    };
  });

  const TOKEN_FIELD_MAP = {
    patient_id: 'tok_patient_search',
    hr_department_id: 'tok_dept',
    doctor_id: 'tok_doctor',
    visit_type: 'tok_visit_type',
    appointment_date: 'tok_appointment_date',
    appointment_time: 'tok_slot',
    slot: 'tok_slot',
    chief_complaint: 'tok_complaint',
    payment_mode: 'tok_payment',
  };

  const GOVERNMENT_PAYMENT_LABELS = new Set([
    'State Health Scheme / AB-PMJAY (Ayushman Bharat)',
    'AB-PMJAY (Ayushman Bharat)',
    'CGHS',
    'ECHS',
    'State Health Scheme',
    'ESI',
  ]);

  const ADMIT_FIELD_MAP = {
    patient_id: 'admit_patient_search',
    hr_department_id: 'admit_dept',
    bed_id: 'admit_bed',
    doctor_id: 'admit_doctor',
    admission_reason: 'admit_reason',
    payment_mode: 'admit_payment',
    advance_deposit: 'admit_advance',
    scheme_beneficiary_card_id: 'admit_gov_card_search',
    scheme_type_id: 'admit_gov_scheme_id',
    scheme_aadhar_otp: 'admit_gov_otp',
    address: 'admit_profile_address',
    pin_code: 'admit_profile_pin',
    state: 'admit_profile_state',
    district: 'admit_profile_district',
    ayushman_bharat_id: 'admit_profile_ab',
    aadhar_no: 'admit_profile_aadhaar',
  };

  const ADMIT_PROFILE_LABELS = {
    address: 'Address',
    pin_code: 'PIN code',
    state: 'State',
    district: 'District',
    ayushman_bharat_id: 'Ayushman Bharat ID',
  };

  class PatientVisitModalsController {
    constructor() {
      this.routes = {};
      this.boot = {};
      this.bound = false;
      this.availableBeds = [];
      this.tokenPatient = null;
      this.admitPatient = null;
      this.tokenModalObserver = null;
      this.admitModalObserver = null;
      this.tokenAppliedCharge = 0;
      this.flatpickrRetryCount = 0;
      this._visitOverlayKeydown = (e) => this.handleVisitOverlayKeydown(e);
      this._visitFormEnterKeydown = (e) => this.handleVisitFormEnterKeydown(e);
      this._visitDocumentFocusContain = (e) => this.handleVisitDocumentFocusContain(e);
      this._visitKeyboardA11yBound = false;
      /** Last Select2 `<select>` the user interacted with — used to refocus after destroy/re-init. */
      this._visitLastSelect2InteractionId = null;
      /** Highlighted row index in #tok_search_results (-1 = none). */
      this._tokSearchActiveIndex = -1;
      /** Highlighted row index in #admit_search_results (-1 = none). */
      this._admitSearchActiveIndex = -1;
      this.admitGovBeneficiaryLocked = false;
      this.admitGovOtpInputRevealed = false;
      this.admitSchemeProfileComplete = true;
      this.admitSchemeProfileMissing = [];
    }

    init({ routes, boot }) {
      this.routes = routes || this.routes;
      this.boot = boot || this.boot;
      this.cacheElements();
      this.bindEvents();
      this.renderStaticOptions();
      this.observeModalState();
      this.resetTokenForm(true);
      this.resetAdmitForm(true);
      this.initFlatpickr();
      this.initModalSelect2(this.token.form, this.token.modal);
      this.initModalSelect2(this.admit.form, this.admit.modal);
      window.pmLoadBedOptions = () => this.loadAvailableBeds();
      window.loadBedOptions = () => this.loadAvailableBeds();
      window.issueToken = () => this.submitTokenForm();
      window.admitPatient = () => this.submitAdmitForm();
    }

    cacheElements() {
      this.token = {
        modal: document.getElementById('opdTokenModal'),
        form: document.getElementById('opdTokenForm'),
        patientSearch: document.getElementById('tok_patient_search'),
        patientId: document.getElementById('tok_patient_id'),
        patientName: document.getElementById('tok_name'),
        patientAge: document.getElementById('tok_age'),
        patientGender: document.getElementById('tok_gender'),
        dept: document.getElementById('tok_dept'),
        doctor: document.getElementById('tok_doctor'),
        appointmentDate: document.getElementById('tok_appointment_date'),
        slot: document.getElementById('tok_slot'),
        complaint: document.getElementById('tok_complaint'),
        visitType: document.getElementById('tok_visit_type'),
        payment: document.getElementById('tok_payment'),
        charge: document.getElementById('tok_charge'),
        searchResults: document.getElementById('tok_search_results'),
        submitBtn: document.getElementById('tokSubmitBtn'),
        previewNo: document.getElementById('tokenDisplayNo'),
        previewDept: document.getElementById('tokenDisplayDept'),
        previewTime: document.getElementById('tokenDisplayTime'),
      };

      this.admit = {
        modal: document.getElementById('ipdAdmitModal'),
        form: document.getElementById('ipdAdmitForm'),
        patientSearch: document.getElementById('admit_patient_search'),
        patientId: document.getElementById('admit_patient_id'),
        searchResults: document.getElementById('admit_search_results'),
        patientChip: document.getElementById('admitPatientChip'),
        dept: document.getElementById('admit_dept'),
        doctor: document.getElementById('admit_doctor'),
        ward: document.getElementById('admit_ward'),
        bed: document.getElementById('admit_bed'),
        reason: document.getElementById('admit_reason'),
        payment: document.getElementById('admit_payment'),
        advance: document.getElementById('admit_advance'),
        preview: document.getElementById('bedPreview'),
        submitBtn: document.getElementById('admitSubmitBtn'),
      };
    }

    bindEvents() {
      if (this.bound) {
        return;
      }

      this.token.form?.addEventListener('submit', (event) => {
        event.preventDefault();
        this.submitTokenForm();
      });
      this.token.dept?.addEventListener('change', async () => {
        this.clearFormErrors(this.token.form);
        await this.loadTokenDoctorsAndSlots();
        await this.loadTokenCharge();
        await this.loadSlotWiseTokenPreview();
      });
      this.token.doctor?.addEventListener('change', async () => {
        await this.loadTokenSlots();
        await this.loadTokenCharge();
        await this.loadSlotWiseTokenPreview();
      });
      this.token.appointmentDate?.addEventListener('change', async () => {
        await this.loadTokenSlots();
        await this.loadSlotWiseTokenPreview();
      });
      this.token.slot?.addEventListener('change', async () => {
        this.updateTokenPreview();
        await this.loadSlotWiseTokenPreview();
      });
      this.token.visitType?.addEventListener('change', async () => {
        await this.loadTokenCharge();
      });

      if (window.jQuery) {
        jQuery(document).on('select2:select select2:clear', '#tok_dept', async () => {
          this.clearFormErrors(this.token.form);
          await this.loadTokenDoctorsAndSlots();
          await this.loadTokenCharge();
          await this.loadSlotWiseTokenPreview();
        });
        jQuery(document).on('select2:select select2:clear', '#tok_doctor', async () => {
          await this.loadTokenSlots();
          await this.loadTokenCharge();
          await this.loadSlotWiseTokenPreview();
        });
        jQuery(document).on('select2:select select2:clear', '#tok_appointment_date', async () => {
          await this.loadTokenSlots();
          await this.loadSlotWiseTokenPreview();
        });
        jQuery(document).on('select2:select select2:clear', '#tok_slot', async () => {
          this.updateTokenPreview();
          await this.loadSlotWiseTokenPreview();
        });
        jQuery(document).on('select2:select select2:clear', '#tok_visit_type', async () => {
          await this.loadTokenCharge();
        });
        jQuery(document).on('select2:select select2:clear', '#admit_dept', async () => {
          this.clearFormErrors(this.admit.form);
          await this.loadAdmitDoctors();
        });
        jQuery(document).on('select2:select select2:clear', '#admit_ward', () => {
          this.renderAvailableBeds();
        });
        jQuery(document).on('select2:select select2:clear', '#admit_bed', () => {
          this.syncPreviewSelection();
        });
      }
      this.token.patientSearch?.addEventListener('input', debounce(() => this.handleTokenPatientSearch(), 300));
      this.token.patientSearch?.addEventListener('keydown', (event) => this.handleTokenPatientSearchKeydown(event));
      this.token.searchResults?.addEventListener('click', (event) => this.handleTokenResultClick(event));
      document.addEventListener('click', (event) => {
        const t = event.target;
        if (this.token.searchResults && this.token.patientSearch) {
          if (t !== this.token.patientSearch && !this.token.searchResults.contains(t)) {
            this.token.searchResults.innerHTML = '';
            this._tokSearchActiveIndex = -1;
          }
        }
        if (this.admit.searchResults && this.admit.patientSearch) {
          if (t !== this.admit.patientSearch && !this.admit.searchResults.contains(t)) {
            this.admit.searchResults.innerHTML = '';
            this._admitSearchActiveIndex = -1;
          }
        }
      });
      this.token.form?.querySelectorAll('input, select, textarea').forEach((field) => {
        field.addEventListener('input', () => this.clearFieldError(field.id));
        field.addEventListener('change', () => this.clearFieldError(field.id));
      });

      this.admit.form?.addEventListener('submit', (event) => {
        event.preventDefault();
        this.submitAdmitForm();
      });
      this.admit.dept?.addEventListener('change', async () => {
        this.clearFormErrors(this.admit.form);
        await this.loadAdmitDoctors();
      });
      this.admit.ward?.addEventListener('change', () => this.renderAvailableBeds());
      this.admit.bed?.addEventListener('change', () => this.syncPreviewSelection());
      this.admit.patientSearch?.addEventListener('input', debounce(() => this.handleAdmitPatientSearch(), 300));
      this.admit.patientSearch?.addEventListener('keydown', (event) => this.handleAdmitPatientSearchKeydown(event));
      this.admit.searchResults?.addEventListener('click', (event) => this.handleAdmitResultClick(event));
      this.admit.preview?.addEventListener('click', (event) => this.handleBedPreviewClick(event));
      this.admit.form?.querySelectorAll('input, select, textarea').forEach((field) => {
        field.addEventListener('input', () => this.clearFieldError(field.id));
        field.addEventListener('change', () => this.clearFieldError(field.id));
      });
      this.admit.payment?.addEventListener('change', () => {
        this.clearFieldError('admit_payment');
        if (!this.isAdmitGovSchemePaymentLabel(this.admit.payment?.value)) {
          this.resetAdmitGovSchemeState();
          this.hideAdmitSchemeProfilePanel();
        } else {
          void this.refreshAdmitSchemeProfilePanel();
        }
        this.toggleAdmitGovSchemePanel();
      });
      document.getElementById('admit_scheme_profile_save')?.addEventListener('click', () => void this.saveAdmitSchemeProfile());
      document.getElementById('admit_profile_state')?.addEventListener('change', (event) => {
        void this.loadAdmitProfileDistricts(event.target.value, null);
      });
      document.getElementById('admit_gov_scheme_id')?.addEventListener('change', () => {
        if (this.admitGovBeneficiaryLocked) {
          return;
        }
        this.clearFieldError('admit_gov_scheme_id');
        const lt = document.getElementById('admit_scheme_lookup_token');
        const at = document.getElementById('admit_scheme_auth_token');
        if (lt) lt.value = '';
        if (at) at.value = '';
        this.preloadAdmitGovBeneficiarySearch();
        this.syncAdmitGovOtpSendButton();
      });
      document.getElementById('admit_gov_search_btn')?.addEventListener('click', () => void this.runAdmitGovBeneficiarySearch());
      document.getElementById('admit_gov_confirm_auth_btn')?.addEventListener('click', () => void this.runAdmitGovConfirmAuth());
      document.getElementById('admit_gov_send_otp_btn')?.addEventListener('click', () => void this.sendAdmitGovSchemeOtp());
      document.getElementById('admit_gov_clear_btn')?.addEventListener('click', () => {
        this.resetAdmitGovSchemeState();
        sendmsg('info', 'Beneficiary cleared — you can search again.');
      });
      document.getElementById('admit_gov_card_search')?.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
          event.preventDefault();
          void this.runAdmitGovBeneficiarySearch();
        }
      });
      document.getElementById('admit_gov_otp')?.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
          event.preventDefault();
          void this.runAdmitGovConfirmAuth();
        }
      });
      document.querySelectorAll('#admitGovSchemeBlock input[name="admit_gov_kyc"]').forEach((r) => {
        r.addEventListener('change', () => this.handleAdmitGovKycChange());
      });

      this.bindVisitModalKeyboardA11y();

      this.bound = true;
    }

    renderStaticOptions() {
      window.pmRenderOptions?.(this.token.dept, this.boot.departments || [], { placeholder: 'Select Department' });
      window.pmRenderOptions?.(this.admit.dept, this.boot.departments || [], { placeholder: 'Select Department' });
      this.initModalSelect2(this.token.form, this.token.modal);
      this.initModalSelect2(this.admit.form, this.admit.modal);
    }

    initModalSelect2(form, modal) {
      if (!(window.jQuery && jQuery.fn && jQuery.fn.select2) || !form || !modal) {
        return;
      }
      const $modal = jQuery(modal).find('.modal').first();
      jQuery(form).find('select.form-control').each((_, el) => {
        if (el.dataset && el.dataset.noSelect2 === '1') {
          return;
        }
        const $el = jQuery(el);
        if ($el.hasClass('select2-hidden-accessible')) {
          $el.select2('destroy');
        }
        $el.select2({ width: '100%', dropdownParent: $modal });
      });

      if (form.id === 'opdTokenForm' || form.id === 'ipdAdmitForm') {
        this.clearVisitModalTabindex(form);
      } else {
        this.applyModalTabIndexOrder(form);
      }
      this.restoreVisitSelect2FocusAfterReinit(form, modal);
    }

    clearVisitModalTabindex(form) {
      if (!form) {
        return;
      }
      const modalEl = form.closest('.modal');
      const scope = modalEl || form;
      scope.querySelectorAll('[tabindex]').forEach((node) => {
        node.removeAttribute('tabindex');
      });
      scope.querySelectorAll('[data-modal-tabindex]').forEach((node) => {
        node.removeAttribute('data-modal-tabindex');
      });
      form.querySelectorAll('select.select2-hidden-accessible').forEach((sel) => {
        sel.setAttribute('tabindex', '-1');
        const box = sel.nextElementSibling?.querySelector('.select2-selection');
        if (box) {
          box.removeAttribute('tabindex');
        }
      });
    }

    getSelect2FocusTarget(selectEl) {
      if (!(selectEl instanceof HTMLElement) || selectEl.disabled) {
        return null;
      }
      if (!selectEl.classList.contains('select2-hidden-accessible')) {
        return selectEl;
      }
      const selection = selectEl.nextElementSibling?.querySelector('.select2-selection');
      if (selection && this.isFocusableVisitField(selection)) {
        return selection;
      }
      return null;
    }

    getAppointmentDateFocusTarget() {
      const inp = document.getElementById('tok_appointment_date');
      if (!inp) {
        return null;
      }
      if (inp._flatpickr?.altInput) {
        return inp._flatpickr.altInput;
      }
      return inp;
    }

    pushIfVisitModalFocusable(list, el) {
      if (!(el instanceof HTMLElement)) {
        return;
      }
      if (el.disabled) {
        return;
      }
      if (el.classList.contains('select2-selection')) {
        const native = el.closest('.select2-container')?.previousElementSibling;
        if (native && native.matches('select') && native.disabled) {
          return;
        }
      }
      if (!this.isFocusableVisitField(el)) {
        return;
      }
      list.push(el);
    }

    getOrderedOpdTokenFocusables(overlay) {
      if (!(overlay instanceof HTMLElement) || overlay.id !== 'opdTokenModal') {
        return [];
      }
      const list = [];
      const selectChain = (id) => {
        const node = document.getElementById(id);
        if (!node || node.tagName !== 'SELECT') {
          return;
        }
        const t = this.getSelect2FocusTarget(node);
        if (t) {
          this.pushIfVisitModalFocusable(list, t);
        }
      };
      [
        'tok_patient_search',
        'tok_name',
        'tok_age',
        'tok_gender',
      ].forEach((id) => {
        const node = document.getElementById(id);
        if (node) {
          this.pushIfVisitModalFocusable(list, node);
        }
      });
      selectChain('tok_dept');
      selectChain('tok_doctor');
      this.pushIfVisitModalFocusable(list, this.getAppointmentDateFocusTarget());
      selectChain('tok_slot');
      ['tok_complaint', 'tok_visit_type', 'tok_payment'].forEach((id) => {
        const node = document.getElementById(id);
        if (!node) {
          return;
        }
        if (node.tagName === 'SELECT') {
          selectChain(id);
        } else {
          this.pushIfVisitModalFocusable(list, node);
        }
      });
      const charge = document.getElementById('tok_charge');
      if (charge) {
        this.pushIfVisitModalFocusable(list, charge);
      }
      /* After Applied Charge: Tab goes straight to Issue Token (skip Cancel in forward order). */
      const footer = overlay.querySelector('.modal-footer');
      if (footer) {
        const submitBtn = document.getElementById('tokSubmitBtn');
        const cancelBtn = footer.querySelector('.btn-secondary');
        if (submitBtn) {
          this.pushIfVisitModalFocusable(list, submitBtn);
        }
        if (cancelBtn && cancelBtn !== submitBtn) {
          this.pushIfVisitModalFocusable(list, cancelBtn);
        }
      }
      const closeBtn = overlay.querySelector('.modal-header .modal-close');
      this.pushIfVisitModalFocusable(list, closeBtn);
      return list;
    }

    getOrderedIpdAdmitFocusables(overlay) {
      if (!(overlay instanceof HTMLElement) || overlay.id !== 'ipdAdmitModal') {
        return [];
      }
      const list = [];
      const selectChain = (id) => {
        const node = document.getElementById(id);
        if (!node || node.tagName !== 'SELECT') {
          return;
        }
        const t = this.getSelect2FocusTarget(node);
        if (t) {
          this.pushIfVisitModalFocusable(list, t);
        }
      };
      this.pushIfVisitModalFocusable(list, document.getElementById('admit_patient_search'));
      selectChain('admit_dept');
      this.pushIfVisitModalFocusable(list, document.getElementById('admit_reason'));
      selectChain('admit_ward');
      selectChain('admit_bed');
      selectChain('admit_doctor');
      selectChain('admit_duration');
      const pay = document.getElementById('admit_payment');
      if (pay) {
        this.pushIfVisitModalFocusable(list, pay);
      }
      if (this.isAdmitGovSchemePanelVisible()) {
        this.pushIfVisitModalFocusable(list, document.getElementById('admit_gov_scheme_id'));
        this.pushIfVisitModalFocusable(list, document.getElementById('admit_gov_card_search'));
        const kycGroup = document.getElementById('admit_gov_kyc_group');
        if (kycGroup && kycGroup.style.display !== 'none') {
          document.querySelectorAll('#admitGovSchemeBlock input[name="admit_gov_kyc"]').forEach((r) => {
            this.pushIfVisitModalFocusable(list, r);
          });
        }
        const otpRow = document.getElementById('admit_gov_otp_row');
        if (otpRow && otpRow.style.display !== 'none') {
          this.pushIfVisitModalFocusable(list, document.getElementById('admit_gov_otp'));
        }
        ['admit_gov_send_otp_btn', 'admit_gov_confirm_auth_btn', 'admit_gov_clear_btn'].forEach((id) => {
          const btn = document.getElementById(id);
          if (btn && btn.style.display !== 'none' && !btn.disabled) {
            this.pushIfVisitModalFocusable(list, btn);
          }
        });
      }
      this.pushIfVisitModalFocusable(list, document.getElementById('admit_advance'));
      this.pushIfVisitModalFocusable(list, document.getElementById('admit_special_instructions'));
      /* Bed preview chips: mouse-only — Tab skips to footer actions (see .bed-preview-chip tabindex). */
      /* After last body field: Tab goes straight to Admit (skip Cancel in forward order). */
      const footer = overlay.querySelector('.modal-footer');
      if (footer) {
        const submitBtn = document.getElementById('admitSubmitBtn');
        const cancelBtn = footer.querySelector('.btn-secondary');
        if (submitBtn) {
          this.pushIfVisitModalFocusable(list, submitBtn);
        }
        if (cancelBtn && cancelBtn !== submitBtn) {
          this.pushIfVisitModalFocusable(list, cancelBtn);
        }
      }
      const closeBtn = overlay.querySelector('.modal-header .modal-close');
      this.pushIfVisitModalFocusable(list, closeBtn);
      return list;
    }

    indexInVisitOrderedFocusables(ordered, active) {
      if (!active || !ordered.length) {
        return -1;
      }
      let idx = ordered.indexOf(active);
      if (idx >= 0) {
        return idx;
      }
      const sc = active.closest('.select2-selection');
      if (sc) {
        idx = ordered.indexOf(sc);
        if (idx >= 0) {
          return idx;
        }
      }
      const apptAlt = document.getElementById('tok_appointment_date')?._flatpickr?.altInput;
      if (apptAlt && active === apptAlt) {
        idx = ordered.indexOf(apptAlt);
        if (idx >= 0) {
          return idx;
        }
      }
      return -1;
    }

    applyModalTabIndexOrder(form) {
      if (!form) {
        return;
      }

      const controls = Array.from(form.querySelectorAll('input, select, textarea, button'))
        .filter((node) => this.isTabbableControl(node));

      let index = 1;
      controls.forEach((node) => {
        if (node.tagName === 'SELECT' && node.classList.contains('select2-hidden-accessible')) {
          node.dataset.modalTabindex = String(index);
          node.setAttribute('tabindex', '-1');
        } else {
          node.setAttribute('tabindex', String(index));
        }

        if (node.tagName === 'SELECT') {
          node.dataset.modalTabindex = String(index);
        }

        index += 1;
      });

      this.syncModalSelect2TabOrder(form);
    }

    syncModalSelect2TabOrder(form) {
      if (!form || !(window.jQuery && jQuery.fn && jQuery.fn.select2)) {
        return;
      }

      form.querySelectorAll('select[id]').forEach((selectNode) => {
        const tabIndex = selectNode.dataset.modalTabindex || selectNode.getAttribute('tabindex');
        if (!tabIndex) {
          return;
        }

        const select2Selection = selectNode.nextElementSibling?.querySelector('.select2-selection');
        if (select2Selection) {
          select2Selection.setAttribute('tabindex', String(tabIndex));
        }
      });
    }

    isTabbableControl(node) {
      if (!node || node.disabled) return false;
      if (node.type === 'hidden') return false;
      /* Select2 hides the real <select> (often no offsetParent); tab order uses .select2-selection instead. */
      if (node.tagName === 'SELECT' && node.classList.contains('select2-hidden-accessible')) {
        return true;
      }
      /* Flatpickr: only the alt input is tabbed; primary stays for programmatic value. */
      if (node.tagName === 'INPUT' && node.classList.contains('flatpickr-input')
        && !node.classList.contains('flatpickr-alt-input') && node._flatpickr?.altInput) {
        return false;
      }
      if (!this.isVisibleElement(node)) return false;
      return true;
    }

    isVisibleElement(node) {
      if (!node || !(node instanceof HTMLElement)) return false;
      return !!(node.offsetParent || node.getClientRects().length);
    }

    observeModalState() {
      if (this.token.modal && !this.tokenModalObserver) {
        this.tokenModalObserver = new MutationObserver(() => {
          if (this.token.modal.classList.contains('hidden')) {
            this.resetTokenForm(true);
          } else {
            this.resetTokenForm(false);
            this.initFlatpickr();
            window.setTimeout(() => this.focusVisitModalStart(this.token), 50);
          }
        });
        this.tokenModalObserver.observe(this.token.modal, { attributes: true, attributeFilter: ['class'] });
      }

      if (this.admit.modal && !this.admitModalObserver) {
        this.admitModalObserver = new MutationObserver(() => {
          if (this.admit.modal.classList.contains('hidden')) {
            this.resetAdmitForm(true);
          } else {
            this.resetAdmitForm(false);
            this.loadAvailableBeds();
            window.setTimeout(() => this.focusVisitModalStart(this.admit), 50);
          }
        });
        this.admitModalObserver.observe(this.admit.modal, { attributes: true, attributeFilter: ['class'] });
      }
    }

    resetTokenForm(clearValues) {
      this.clearFormErrors(this.token.form);
      if (clearValues) {
        this.token.form?.reset();
        this.renderStaticOptions();
      }
      this.ensureTokenAppointmentDate();
      this.tokenPatient = null;
      this.tokenAppliedCharge = 0;
      if (this.token.patientId) this.token.patientId.value = '';
      if (this.token.patientName) this.token.patientName.value = '';
      if (this.token.patientAge) this.token.patientAge.value = '';
      if (this.token.patientGender) this.token.patientGender.value = '';
      this.setTokenGenderLocked(false);
      if (this.token.searchResults) this.token.searchResults.innerHTML = '';
      this._tokSearchActiveIndex = -1;
      if (this.token.doctor) this.token.doctor.innerHTML = '<option value="">Select Doctor</option>';
      if (this.token.slot) this.token.slot.innerHTML = '<option value="">Select Slot</option>';
      if (this.token.visitType) this.token.visitType.value = 'OPD';
      this.setTokenCharge(0);
      this.initModalSelect2(this.token.form, this.token.modal);
      this.updateTokenPreview();
    }

    ensureTokenAppointmentDate() {
      if (this.token.appointmentDate && !this.token.appointmentDate.value) {
        this.token.appointmentDate.value = new Date().toISOString().slice(0, 10);
      }
    }

    initFlatpickr() {
      if (typeof window.flatpickr !== 'function') {
        if (this.flatpickrRetryCount >= 20) {
          return;
        }
        this.flatpickrRetryCount += 1;
        window.setTimeout(() => this.initFlatpickr(), 150);
        return;
      }

      this.flatpickrRetryCount = 0;
      this.ensureTokenAppointmentDate();
      this.setupFlatpickrField(this.token.appointmentDate, {
        altInput: true,
        altFormat: 'd-m-Y',
        dateFormat: 'Y-m-d',
        minDate: 'today',
        allowInput: true,
        onChange: async () => {
          await this.loadTokenSlots();
          await this.loadSlotWiseTokenPreview();
        },
      });
    }

    setupFlatpickrField(field, config = {}) {
      if (!field) {
        return;
      }

      if (field._flatpickr) {
        field._flatpickr.destroy();
      }

      const userOnReady = config.onReady;
      const userOnOpen = config.onOpen;
      const merged = {
        ...config,
        clickOpens: true,
        onReady(selectedDates, dateStr, instance) {
          const alt = instance?.altInput;
          if (alt) {
            alt.removeAttribute('readonly');
            alt.setAttribute('placeholder', 'DD-MM-YYYY');
            alt.setAttribute('inputmode', 'numeric');
            alt.setAttribute('autocomplete', 'off');
          }
          if (typeof userOnReady === 'function') {
            userOnReady(selectedDates, dateStr, instance);
          }
        },
        onOpen(selectedDates, dateStr, instance) {
          const alt = instance?.altInput;
          if (alt) {
            alt.removeAttribute('readonly');
          }
          if (typeof userOnOpen === 'function') {
            userOnOpen(selectedDates, dateStr, instance);
          }
        },
      };

      window.flatpickr(field, merged);

      window.queueMicrotask(() => {
        if (field._flatpickr?.altInput) {
          field.setAttribute('tabindex', '-1');
        }
        const form = field.closest('form');
        if (form && (form.id === 'opdTokenForm' || form.id === 'ipdAdmitForm')) {
          this.clearVisitModalTabindex(form);
        }
      });
    }

    resetAdmitForm(clearValues) {
      this.clearFormErrors(this.admit.form);
      if (clearValues) {
        this.admit.form?.reset();
        this.renderStaticOptions();
      }
      this.admitPatient = null;
      if (this.admit.patientId) this.admit.patientId.value = '';
      if (this.admit.searchResults) this.admit.searchResults.innerHTML = '';
      this._admitSearchActiveIndex = -1;
      if (this.admit.patientChip) {
        this.admit.patientChip.style.display = 'none';
        this.admit.patientChip.innerHTML = '';
      }
      this.admit.doctor.innerHTML = '<option value="">Select Doctor</option>';
      this.renderAvailableBeds();
      this.resetAdmitGovSchemeState();
      this.admitSchemeProfileComplete = true;
      this.admitSchemeProfileMissing = [];
      this.hideAdmitSchemeProfilePanel();
      this.toggleAdmitGovSchemePanel();
      this.initModalSelect2(this.admit.form, this.admit.modal);
    }

    updateTokenPreview() {
      const deptText = this.token.dept?.selectedIndex >= 0 ? this.token.dept.options[this.token.dept.selectedIndex].text : 'Select Department';
      this.token.previewDept.textContent = deptText || 'Select Department';
      const slotText = this.token.slot?.value || '-';
      const dateLabel = this.token.appointmentDate?.value || '';
      this.token.previewTime.textContent = dateLabel ? `${slotText} | ${dateLabel}` : slotText;
      if (!this.token.previewNo?.textContent || !this.token.previewNo.textContent.trim()) {
        this.token.previewNo.textContent = '---';
      }
    }

    async loadTokenDoctorsAndSlots() {
      const deptId = this.token.dept?.value;
      if (!deptId) {
        window.pmRenderOptions?.(this.token.doctor, [], { placeholder: 'Select Doctor' });
        window.pmRenderOptions?.(this.token.slot, [], { placeholder: 'Select Slot' });
        if (this.token.previewNo) this.token.previewNo.textContent = '---';
        this.initModalSelect2(this.token.form, this.token.modal);
        this.updateTokenPreview();
        return;
      }
      const doctors = deptId ? await window.pmFetch(`${this.routes.loadDoctors}?dept_id=${encodeURIComponent(deptId)}`) : [];
      window.pmRenderOptions?.(this.token.doctor, doctors || [], { placeholder: 'Select Doctor' });
      this.initModalSelect2(this.token.form, this.token.modal);
      await this.loadTokenSlots();
    }

    async loadTokenCharge() {
      if (!this.routes.getOpdCharge || !this.token.dept?.value) {
        this.setTokenCharge(0);
        return;
      }
      try {
        const data = await window.pmFetch(this.routes.getOpdCharge, {
          method: 'POST',
          body: {
            hr_department_id: this.token.dept.value,
            doctor_id: this.token.doctor?.value || null,
            visit_type: this.token.visitType?.value || 'OPD',
            tpa_id: null,
          },
        });
        const charge = Number(data?.charge ?? data?.standard_charge ?? 0);
        if (Number.isFinite(charge)) {
          this.setTokenCharge(charge);
          return;
        }
        this.setTokenCharge(0);
      } catch (error) {
        this.setTokenCharge(0);
      }
    }

    async loadTokenSlots() {
      const doctorId = this.token.doctor?.value;
      const date = this.token.appointmentDate?.value;
      if (!doctorId || !date) {
        this.token.slot.innerHTML = '<option value="">Select Slot</option>';
        if (this.token.previewNo) this.token.previewNo.textContent = '---';
        this.initModalSelect2(this.token.form, this.token.modal);
        this.updateTokenPreview();
        return;
      }
      const currentSlot = this.token.slot?.value || '';
      const slots = await window.pmFetch(`${this.routes.loadDoctorSlots}?doctor_id=${encodeURIComponent(doctorId)}&date=${encodeURIComponent(date)}`);
      this.token.slot.innerHTML = '<option value="">Select Slot</option>' + (slots || []).map((slot) => {
        const label = slot?.label || slot?.slot || slot?.value || '';
        const safe = this.escapeHtml(label);
        return `<option value="${safe}">${safe}</option>`;
      }).join('');
      if (currentSlot && Array.from(this.token.slot.options).some((option) => option.value === currentSlot)) {
        this.token.slot.value = currentSlot;
      } else if (slots && slots.length) {
        this.token.slot.value = slots[0]?.label || slots[0]?.slot || slots[0]?.value || '';
      }
      this.initModalSelect2(this.token.form, this.token.modal);
      if (window.jQuery && this.token.slot.value) {
        jQuery(this.token.slot).trigger('change.select2');
      }
      this.updateTokenPreview();
    }

    async loadAdmitDoctors() {
      const deptId = this.admit.dept?.value;
      const currentDoctorId = this.admit.doctor?.value || '';
      const doctors = deptId ? await window.pmFetch(`${this.routes.loadDoctors}?dept_id=${encodeURIComponent(deptId)}`) : [];
      window.pmRenderOptions?.(this.admit.doctor, doctors || [], { placeholder: 'Select Doctor' });
      if (currentDoctorId && Array.isArray(doctors) && doctors.some((doctor) => String(doctor.id) === String(currentDoctorId))) {
        this.admit.doctor.value = currentDoctorId;
      }
      this.initModalSelect2(this.admit.form, this.admit.modal);
      if (window.jQuery && this.admit.doctor?.value) {
        jQuery(this.admit.doctor).trigger('change.select2');
      }
    }

    getTokSearchItemButtons() {
      if (!this.token.searchResults) {
        return [];
      }
      return Array.from(this.token.searchResults.querySelectorAll('.tok-search-item[data-token-patient]'));
    }

    refreshTokSearchActiveClass() {
      const items = this.getTokSearchItemButtons();
      items.forEach((btn, i) => {
        btn.classList.toggle('tok-search-item--active', i === this._tokSearchActiveIndex);
      });
      const active = items[this._tokSearchActiveIndex];
      if (active && typeof active.scrollIntoView === 'function') {
        active.scrollIntoView({ block: 'nearest' });
      }
    }

    handleTokenPatientSearchKeydown(event) {
      const items = this.getTokSearchItemButtons();
      const key = event.key;
      if (key === 'ArrowDown' && items.length) {
        event.preventDefault();
        if (this._tokSearchActiveIndex < items.length - 1) {
          this._tokSearchActiveIndex += 1;
        } else {
          this._tokSearchActiveIndex = 0;
        }
        if (this._tokSearchActiveIndex < 0) {
          this._tokSearchActiveIndex = 0;
        }
        this.refreshTokSearchActiveClass();
        return;
      }
      if (key === 'ArrowUp' && items.length) {
        event.preventDefault();
        if (this._tokSearchActiveIndex > 0) {
          this._tokSearchActiveIndex -= 1;
        } else {
          this._tokSearchActiveIndex = -1;
        }
        this.refreshTokSearchActiveClass();
        return;
      }
      if (key === 'Enter' && this._tokSearchActiveIndex >= 0 && items[this._tokSearchActiveIndex]) {
        event.preventDefault();
        this.selectTokenPatientFromItem(items[this._tokSearchActiveIndex]);
        return;
      }
      if (key === 'Escape' && items.length) {
        event.preventDefault();
        this._tokSearchActiveIndex = -1;
        this.refreshTokSearchActiveClass();
      }
    }

    getAdmitSearchItemButtons() {
      if (!this.admit.searchResults) {
        return [];
      }
      return Array.from(this.admit.searchResults.querySelectorAll('.tok-search-item[data-admit-patient]'));
    }

    refreshAdmitSearchActiveClass() {
      const items = this.getAdmitSearchItemButtons();
      items.forEach((btn, i) => {
        btn.classList.toggle('tok-search-item--active', i === this._admitSearchActiveIndex);
      });
      const active = items[this._admitSearchActiveIndex];
      if (active && typeof active.scrollIntoView === 'function') {
        active.scrollIntoView({ block: 'nearest' });
      }
    }

    handleAdmitPatientSearchKeydown(event) {
      const items = this.getAdmitSearchItemButtons();
      const key = event.key;
      if (key === 'ArrowDown' && items.length) {
        event.preventDefault();
        if (this._admitSearchActiveIndex < items.length - 1) {
          this._admitSearchActiveIndex += 1;
        } else {
          this._admitSearchActiveIndex = 0;
        }
        if (this._admitSearchActiveIndex < 0) {
          this._admitSearchActiveIndex = 0;
        }
        this.refreshAdmitSearchActiveClass();
        return;
      }
      if (key === 'ArrowUp' && items.length) {
        event.preventDefault();
        if (this._admitSearchActiveIndex > 0) {
          this._admitSearchActiveIndex -= 1;
        } else {
          this._admitSearchActiveIndex = -1;
        }
        this.refreshAdmitSearchActiveClass();
        return;
      }
      if (key === 'Enter' && this._admitSearchActiveIndex >= 0 && items[this._admitSearchActiveIndex]) {
        event.preventDefault();
        this.selectAdmitPatientFromItem(items[this._admitSearchActiveIndex]);
        return;
      }
      if (key === 'Escape' && items.length) {
        event.preventDefault();
        this._admitSearchActiveIndex = -1;
        this.refreshAdmitSearchActiveClass();
      }
    }

    setTokenGenderLocked(locked) {
      if (!this.token.patientGender) {
        return;
      }
      this.token.patientGender.disabled = !!locked;
      this.token.patientGender.setAttribute('aria-readonly', locked ? 'true' : 'false');
    }

    /** Map API / age_sex suffix to select values Male | Female | Other. */
    normalizeTokenGenderValue(raw) {
      const s = String(raw || '').trim();
      if (!s || s === '-') {
        return '';
      }
      const lower = s.toLowerCase();
      if (lower === 'male' || lower === 'm') {
        return 'Male';
      }
      if (lower === 'female' || lower === 'f') {
        return 'Female';
      }
      if (lower === 'other' || lower === 'o') {
        return 'Other';
      }
      if (['Male', 'Female', 'Other'].includes(s)) {
        return s;
      }
      return '';
    }

    parseGenderFromAgeSex(ageSexText) {
      const parts = String(ageSexText || '').split('/');
      if (parts.length < 2) {
        return '';
      }
      return this.normalizeTokenGenderValue(parts[1]);
    }

    focusTokenDepartmentSelect() {
      window.setTimeout(() => {
        const el = this.token.dept;
        if (!el) {
          return;
        }
        const $ = window.jQuery;
        if ($ && $.fn && $.fn.select2 && $(el).hasClass('select2-hidden-accessible')) {
          const selection = el.nextElementSibling?.querySelector?.('.select2-selection');
          if (selection instanceof HTMLElement) {
            selection.focus();
            return;
          }
        }
        el.focus();
      }, 50);
    }

    focusAdmitDepartmentSelect() {
      window.setTimeout(() => {
        const el = this.admit.dept;
        if (!el) {
          return;
        }
        const $ = window.jQuery;
        if ($ && $.fn && $.fn.select2 && $(el).hasClass('select2-hidden-accessible')) {
          const selection = el.nextElementSibling?.querySelector?.('.select2-selection');
          if (selection instanceof HTMLElement) {
            selection.focus();
            return;
          }
        }
        el.focus();
      }, 50);
    }

    selectTokenPatientFromItem(item) {
      if (!item) {
        return;
      }
      const genderRaw = item.dataset.gender ? decodeURIComponent(item.dataset.gender) : '';
      const ageSex = item.dataset.ageSex ? decodeURIComponent(item.dataset.ageSex || '') : '';
      this.tokenPatient = {
        id: item.dataset.id,
        mrn: decodeURIComponent(item.dataset.mrn || ''),
        name: decodeURIComponent(item.dataset.name || ''),
        phone: decodeURIComponent(item.dataset.phone || ''),
        ageSex,
        gender: genderRaw,
      };
      if (this.token.patientId) {
        this.token.patientId.value = this.tokenPatient.id;
      }
      if (this.token.patientName) {
        this.token.patientName.value = this.tokenPatient.name;
      }
      if (this.token.patientSearch) {
        this.token.patientSearch.value = `${this.tokenPatient.mrn} - ${this.tokenPatient.name}`;
      }
      this.fillTokenAgeSex(this.tokenPatient.ageSex);
      if (this.token.patientGender) {
        const g = this.normalizeTokenGenderValue(this.tokenPatient.gender)
          || this.parseGenderFromAgeSex(this.tokenPatient.ageSex);
        this.token.patientGender.value = g;
      }
      this.setTokenGenderLocked(true);
      this._tokSearchActiveIndex = -1;
      if (this.token.searchResults) {
        this.token.searchResults.innerHTML = '';
      }
      this.clearFieldError('tok_patient_search');
      this.focusTokenDepartmentSelect();
    }

    async handleTokenPatientSearch() {
      const q = this.token.patientSearch?.value.trim() || '';
      this._tokSearchActiveIndex = -1;
      if (this.token.patientId?.value) {
        this.token.patientId.value = '';
        if (this.token.patientName) this.token.patientName.value = '';
        if (this.token.patientAge) this.token.patientAge.value = '';
        if (this.token.patientGender) this.token.patientGender.value = '';
        this.setTokenGenderLocked(false);
      }
      if (q.length < 2) {
        this.token.searchResults.innerHTML = '';
        return;
      }
      const data = await window.pmFetch(`${this.routes.searchPatients}?q=${encodeURIComponent(q)}`);
      if (!Array.isArray(data) || data.length === 0) {
        this.token.searchResults.innerHTML = '<div class="tok-search-empty">No patient found</div>';
        return;
      }
      this.token.searchResults.innerHTML = data.slice(0, 10).map((patient) => {
        const mrn = this.escapeHtml(patient.mrn || '-');
        const name = this.escapeHtml(patient.name || '-');
        const phone = this.escapeHtml(patient.phone || '-');
        const ageSex = this.escapeHtml(patient.age_sex || '-');
        return `
          <button type="button" class="tok-search-item" data-token-patient="1" data-id="${patient.id}" data-mrn="${encodeURIComponent(patient.mrn || '')}" data-name="${encodeURIComponent(patient.name || '')}" data-phone="${encodeURIComponent(patient.phone || '')}" data-age-sex="${encodeURIComponent(patient.age_sex || '')}" data-gender="${encodeURIComponent(patient.gender || '')}">
            <div class="tok-search-name">${name}</div>
            <div class="tok-search-meta">${mrn} | ${phone} | ${ageSex}</div>
          </button>`;
      }).join('');
    }

    handleTokenResultClick(event) {
      const item = event.target.closest('[data-token-patient]');
      if (!item) {
        return;
      }
      this.selectTokenPatientFromItem(item);
    }

    fillTokenAgeSex(ageSexText) {
      const [age] = String(ageSexText || '').split('/');
      if (this.token.patientAge) {
        this.token.patientAge.value = age && age !== '-' ? String(age).trim() : '';
      }
    }

    setTokenCharge(charge) {
      const numeric = Number(charge);
      this.tokenAppliedCharge = Number.isFinite(numeric) ? Number(numeric.toFixed(2)) : 0;
      if (this.token.charge) {
        this.token.charge.value = String(this.tokenAppliedCharge);
      }
    }

    async loadSlotWiseTokenPreview() {
      if (!this.token.previewNo) {
        return;
      }

      const selectedDate = this.token.appointmentDate?.value || '';
      const today = new Date().toISOString().slice(0, 10);
      if (selectedDate && selectedDate !== today) {
        this.token.previewNo.textContent = '001';
        this.updateTokenPreview();
        return;
      }

      if (!this.routes.opdQueue) {
        this.token.previewNo.textContent = '---';
        this.updateTokenPreview();
        return;
      }

      try {
        const rows = await window.pmFetch(this.routes.opdQueue);
        const maxToken = (rows || [])
          .reduce((max, row) => Math.max(max, this.extractTokenNumber(row?.token)), 0);

        const nextToken = maxToken + 1;
        this.token.previewNo.textContent = String(nextToken).padStart(3, '0');
      } catch (error) {
        this.token.previewNo.textContent = '---';
      }

      this.updateTokenPreview();
    }

    extractTokenNumber(tokenValue) {
      const match = String(tokenValue || '').match(/\d+/);
      return match ? Number(match[0]) : 0;
    }

    bindVisitModalKeyboardA11y() {
      if (this._visitKeyboardA11yBound) {
        return;
      }
      this._visitKeyboardA11yBound = true;

      this.token.modal?.addEventListener('keydown', this._visitOverlayKeydown, true);
      this.admit.modal?.addEventListener('keydown', this._visitOverlayKeydown, true);
      this.token.form?.addEventListener('keydown', this._visitFormEnterKeydown, true);
      this.admit.form?.addEventListener('keydown', this._visitFormEnterKeydown, true);
      document.addEventListener('focusin', this._visitDocumentFocusContain, true);

      this.token.submitBtn?.addEventListener('click', (event) => {
        event.preventDefault();
        void this.submitTokenForm();
      });
      this.token.form?.addEventListener('submit', (event) => {
        event.preventDefault();
      });
      this.admit.submitBtn?.addEventListener('click', (event) => {
        event.preventDefault();
        void this.submitAdmitForm();
      });
      this.admit.form?.addEventListener('submit', (event) => {
        event.preventDefault();
      });

      if (window.jQuery) {
        jQuery(document).on(
          'select2:select select2:clear',
          '#opdTokenModal select.select2-hidden-accessible, #ipdAdmitModal select.select2-hidden-accessible',
          (event) => {
            const selectEl = event.currentTarget || event.target;
            if (!selectEl || selectEl.dataset?.noSelect2 === '1') {
              return;
            }
            this._visitLastSelect2InteractionId = selectEl.id || null;
          },
        );
        jQuery(document).on(
          'select2:close',
          '#opdTokenModal select.select2-hidden-accessible, #ipdAdmitModal select.select2-hidden-accessible',
          (event) => {
            const selectEl = event.currentTarget || event.target;
            if (!selectEl || selectEl.dataset?.noSelect2 === '1') {
              return;
            }
            const overlay = selectEl.closest('#opdTokenModal, #ipdAdmitModal');
            if (!overlay || overlay.classList.contains('hidden')) {
              return;
            }
            window.setTimeout(() => {
              if (this.visitModalSelect2DropdownOpen()) {
                return;
              }
              const selection = selectEl.nextElementSibling?.querySelector?.('.select2-selection');
              if (selection && document.activeElement !== selection) {
                this.focusVisitModalField(selection);
              }
            }, 0);
          },
        );
      }
    }

    visitModalSelect2DropdownOpen() {
      return !!document.querySelector(
        '#opdTokenModal .select2-container--open, #ipdAdmitModal .select2-container--open',
      );
    }

    /**
     * After Select2 destroy+init (e.g. dept → reload doctors), focus often lands on &lt;body&gt;.
     * Re-focus the last select the user used when focus is no longer inside the modal.
     */
    restoreVisitSelect2FocusAfterReinit(form, modal) {
      if (!(form instanceof HTMLElement) || !(modal instanceof HTMLElement) || modal.classList.contains('hidden')) {
        return;
      }
      const lastId = this._visitLastSelect2InteractionId;
      if (!lastId) {
        return;
      }
      const node = document.getElementById(lastId);
      if (!node || !form.contains(node) || !node.classList.contains('select2-hidden-accessible')) {
        return;
      }

      const attempt = () => {
        if (modal.classList.contains('hidden') || this.visitModalSelect2DropdownOpen()) {
          return;
        }
        const ae = document.activeElement;
        if (ae instanceof HTMLElement && ae.closest('.flatpickr-calendar')) {
          return;
        }
        if (ae instanceof HTMLElement && modal.contains(ae) && ae !== document.body && ae !== document.documentElement) {
          return;
        }
        const selection = node.nextElementSibling?.querySelector?.('.select2-selection');
        if (!selection || document.activeElement === selection) {
          return;
        }
        this.focusVisitModalField(selection);
      };

      window.requestAnimationFrame(() => window.requestAnimationFrame(attempt));
      window.setTimeout(attempt, 40);
      window.setTimeout(attempt, 120);
    }

    /**
     * Focus inside Issue Token / IPD Admit modals and keep the target in view (modal-body scroll).
     */
    focusVisitModalField(el) {
      if (!(el instanceof HTMLElement)) {
        return;
      }
      el.focus({ preventScroll: false });
      window.requestAnimationFrame(() => {
        try {
          el.scrollIntoView({ block: 'nearest', inline: 'nearest' });
        } catch (e) {
          /* ignore */
        }
      });
    }

    focusVisitModalStart(ctx) {
      const search = ctx === this.token ? this.token.patientSearch : this.admit?.patientSearch;
      if (search && !search.disabled) {
        this.focusVisitModalField(search);
      }
    }

    getActiveVisitModalOverlay() {
      if (this.token.modal && !this.token.modal.classList.contains('hidden')) {
        return this.token.modal;
      }
      if (this.admit.modal && !this.admit.modal.classList.contains('hidden')) {
        return this.admit.modal;
      }
      return null;
    }

    isVisitDetachedOverlayFocus() {
      const active = document.activeElement;
      if (!active || !(active instanceof HTMLElement)) {
        return false;
      }
      return !!(active.closest('.select2-dropdown') || active.closest('.flatpickr-calendar'));
    }

    isFocusableVisitField(el) {
      if (!el || !(el instanceof HTMLElement) || el.disabled) {
        return false;
      }
      if (el.getAttribute('aria-hidden') === 'true') {
        return false;
      }
      if (el.type === 'hidden') {
        return false;
      }
      if (el.tagName === 'INPUT' && el.classList.contains('flatpickr-input')
        && !el.classList.contains('flatpickr-alt-input') && el._flatpickr?.altInput) {
        return false;
      }
      if (typeof el.checkVisibility === 'function') {
        return el.checkVisibility({ checkOpacity: true, checkVisibilityCSS: true });
      }
      return !!(el.offsetParent || el.getClientRects().length);
    }

    collectVisitFocusables(root, { bodyOnly }) {
      if (!root) {
        return [];
      }
      const modalEl = root.querySelector('.modal') || root;
      const body = modalEl.querySelector('.modal-body');
      const list = [];
      const seen = new Set();

      const pushCandidates = (nodes) => {
        for (const el of nodes) {
          if (!(el instanceof HTMLElement)) {
            continue;
          }
          if (el.tagName === 'SELECT' && el.classList.contains('select2-hidden-accessible')) {
            const selection = el.nextElementSibling?.querySelector?.('.select2-selection');
            if (selection && this.isFocusableVisitField(selection) && !seen.has(selection)) {
              seen.add(selection);
              seen.add(el);
              list.push(selection);
            }
            continue;
          }
          if (el.classList.contains('select2-selection')) {
            const prev = el.closest('.select2-container')?.previousElementSibling;
            if (prev?.matches?.('select.select2-hidden-accessible')) {
              continue;
            }
          }
          if (!this.isFocusableVisitField(el)) {
            continue;
          }
          if (seen.has(el)) {
            continue;
          }
          seen.add(el);
          list.push(el);
        }
      };

      if (!bodyOnly) {
        const closeBtn = modalEl.querySelector('.modal-header .modal-close');
        if (closeBtn) {
          pushCandidates([closeBtn]);
        }
      }

      if (body) {
        pushCandidates(
          body.querySelectorAll(
            'input:not([type="hidden"]), select, textarea, button, a[href], .select2-selection, [data-token-patient], [data-admit-patient], .bed-preview-chip',
          ),
        );
      }

      if (!bodyOnly) {
        const footer = modalEl.querySelector('.modal-footer');
        if (footer) {
          pushCandidates(footer.querySelectorAll('button'));
        }
      }

      return list;
    }

    getVisitModalFocusables(overlay) {
      if (overlay?.id === 'opdTokenModal') {
        return this.getOrderedOpdTokenFocusables(overlay);
      }
      if (overlay?.id === 'ipdAdmitModal') {
        return this.getOrderedIpdAdmitFocusables(overlay);
      }
      return this.collectVisitFocusables(overlay, { bodyOnly: false });
    }

    getVisitModalBodyFocusables(overlay) {
      if (overlay?.id === 'opdTokenModal' || overlay?.id === 'ipdAdmitModal') {
        return this.getVisitModalFocusables(overlay).filter((el) => el.closest && el.closest('.modal-body'));
      }
      return this.collectVisitFocusables(overlay, { bodyOnly: true });
    }

    handleVisitOverlayKeydown(event) {
      const overlay = event.currentTarget;
      if (!(overlay instanceof HTMLElement) || overlay.classList.contains('hidden')) {
        return;
      }

      if (event.altKey && !event.repeat) {
        const key = String(event.key || '').toLowerCase();
        if (key === 'n') {
          event.preventDefault();
          event.stopPropagation();
          const primary = overlay.querySelector('#tokSubmitBtn, #admitSubmitBtn');
          if (primary) {
            this.focusVisitModalField(primary);
          }
          return;
        }
        if (key === 'b') {
          event.preventDefault();
          event.stopPropagation();
          const cancel = overlay.querySelector('.modal-footer .btn-secondary');
          if (cancel) {
            this.focusVisitModalField(cancel);
          }
          return;
        }
      }

      if (event.key === 'Tab') {
        this.handleVisitTabCycle(event, overlay);
      }
    }

    handleVisitTabCycle(event, overlay) {
      if (event.key !== 'Tab' || overlay.classList.contains('hidden')) {
        return;
      }
      if (this.isVisitDetachedOverlayFocus()) {
        return;
      }
      const ordered = overlay.id === 'opdTokenModal'
        ? this.getOrderedOpdTokenFocusables(overlay)
        : this.getOrderedIpdAdmitFocusables(overlay);
      if (!ordered.length) {
        return;
      }
      const active = document.activeElement;
      const idx = this.indexInVisitOrderedFocusables(ordered, active);
      if (idx === -1) {
        return;
      }
      /* Issue Token: forward Tab stays on the button; Shift+Tab jumps back to Applied Charge (skip Cancel). */
      if (overlay.id === 'opdTokenModal' && active.id === 'tokSubmitBtn') {
        if (!event.shiftKey) {
          event.preventDefault();
          event.stopPropagation();
          return;
        }
        const ch = document.getElementById('tok_charge');
        if (ch) {
          event.preventDefault();
          event.stopPropagation();
          this.focusVisitModalField(ch);
          return;
        }
      }
      /* IPD Admit: same — forward Tab stays on Admit; Shift+Tab to last body field (skip Cancel). */
      if (overlay.id === 'ipdAdmitModal' && active.id === 'admitSubmitBtn') {
        if (!event.shiftKey) {
          event.preventDefault();
          event.stopPropagation();
          return;
        }
        const lastBody = document.getElementById('admit_special_instructions');
        if (lastBody) {
          event.preventDefault();
          event.stopPropagation();
          this.focusVisitModalField(lastBody);
          return;
        }
      }
      event.preventDefault();
      event.stopPropagation();
      const len = ordered.length;
      const nextIdx = event.shiftKey ? (idx - 1 + len) % len : (idx + 1) % len;
      this.focusVisitModalField(ordered[nextIdx]);
    }

    handleVisitFormEnterKeydown(event) {
      if (event.key !== 'Enter' || event.isComposing) {
        return;
      }
      const form = event.currentTarget;
      if (!(form instanceof HTMLFormElement)) {
        return;
      }
      const overlay = form.closest('.modal-overlay');
      if (!overlay || overlay.classList.contains('hidden')) {
        return;
      }
      if (document.querySelector('#opdTokenModal .select2-container--open, #ipdAdmitModal .select2-container--open')) {
        return;
      }
      if (document.querySelector('.flatpickr-calendar.open')) {
        return;
      }
      const active = document.activeElement;
      if (!active || !(active instanceof HTMLElement)) {
        return;
      }

      const submitBtn = form.querySelector('#tokSubmitBtn, #admitSubmitBtn');
      if (!submitBtn) {
        return;
      }

      if (active.id === 'tokSubmitBtn' || active.id === 'admitSubmitBtn') {
        event.preventDefault();
        event.stopPropagation();
        if (active.id === 'tokSubmitBtn') {
          void this.submitTokenForm();
        } else {
          void this.submitAdmitForm();
        }
        return;
      }

      if (active.closest('.modal-footer')) {
        return;
      }

      /* Chief complaint: Enter issues token (Shift+Enter = newline). */
      if (active.id === 'tok_complaint' && !event.shiftKey) {
        event.preventDefault();
        event.stopPropagation();
        void this.submitTokenForm();
        return;
      }

      if (active.tagName === 'TEXTAREA' && event.shiftKey) {
        return;
      }

      const bodyFocusables = this.getVisitModalBodyFocusables(overlay);
      if (!bodyFocusables.length) {
        return;
      }
      const last = bodyFocusables[bodyFocusables.length - 1];

      if (form.id === 'ipdAdmitForm') {
        if (active !== last) {
          return;
        }
        event.preventDefault();
        event.stopPropagation();
        if (submitBtn instanceof HTMLElement) {
          this.focusVisitModalField(submitBtn);
        }
        return;
      }

      if (form.id === 'opdTokenForm') {
        /* Last body control or charge: Enter submits (implicit form submit was closing / doing nothing useful). */
        if (active !== last && active.id !== 'tok_charge') {
          return;
        }
        event.preventDefault();
        event.stopPropagation();
        void this.submitTokenForm();
      }
    }

    handleVisitDocumentFocusContain(event) {
      const overlay = this.getActiveVisitModalOverlay();
      if (!overlay) {
        return;
      }
      if (this.visitModalSelect2DropdownOpen()) {
        return;
      }
      const t = event.target;
      if (!(t instanceof HTMLElement)) {
        return;
      }
      if (overlay.contains(t)) {
        return;
      }
      if (t.closest('.select2-dropdown')) {
        return;
      }
      if (t.closest('.flatpickr-calendar')) {
        return;
      }
      const otherOverlay = t.closest('.modal-overlay');
      if (otherOverlay && otherOverlay !== overlay) {
        return;
      }

      const list = this.getVisitModalFocusables(overlay);
      if (!list.length) {
        return;
      }
      /* Let Select2 / post-reinit focus settle — immediate pull-back was racing focus restore. */
      window.requestAnimationFrame(() => {
        window.setTimeout(() => {
          if (overlay.classList.contains('hidden') || this.visitModalSelect2DropdownOpen()) {
            return;
          }
          if (overlay.contains(document.activeElement)) {
            return;
          }
          const preferred = overlay.querySelector('#tok_patient_search, #admit_patient_search') || list[0];
          if (preferred) {
            this.focusVisitModalField(preferred);
          }
        }, 60);
      });
    }

    escapeHtml(value) {
      return String(value || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
    }

    selectAdmitPatientFromItem(item) {
      if (!item) {
        return;
      }
      this.admitPatient = {
        id: item.dataset.id,
        mrn: decodeURIComponent(item.dataset.mrn || ''),
        name: decodeURIComponent(item.dataset.name || ''),
        phone: decodeURIComponent(item.dataset.phone || ''),
        ageSex: decodeURIComponent(item.dataset.ageSex || ''),
        aadharNo: decodeURIComponent(item.dataset.aadhar || ''),
        ayushmanBharatId: decodeURIComponent(item.dataset.ayushman || ''),
      };
      if (this.admit.patientId) {
        this.admit.patientId.value = this.admitPatient.id;
      }
      if (this.admit.patientSearch) {
        this.admit.patientSearch.value = `${this.admitPatient.mrn} - ${this.admitPatient.name}`;
      }
      this._admitSearchActiveIndex = -1;
      if (this.admit.searchResults) {
        this.admit.searchResults.innerHTML = '';
      }
      if (this.admit.patientChip) {
        this.admit.patientChip.style.display = '';
        this.admit.patientChip.innerHTML = `<div class="patient-chip-info"><div class="patient-chip-name">${this.escapeHtml(this.admitPatient.name)}</div><div class="patient-chip-meta">${this.escapeHtml(this.admitPatient.mrn)} | ${this.escapeHtml(this.admitPatient.phone || '-')} | ${this.escapeHtml(this.admitPatient.ageSex)}</div></div>`;
      }
      this.clearFieldError('admit_patient_search');
      if (this.isAdmitGovSchemePaymentLabel(this.admit.payment?.value)) {
        void this.refreshAdmitSchemeProfilePanel();
      }
      this.focusAdmitDepartmentSelect();
    }

    async handleAdmitPatientSearch() {
      const q = this.admit.patientSearch?.value.trim() || '';
      this._admitSearchActiveIndex = -1;
      if (this.admit.patientId?.value) {
        this.admit.patientId.value = '';
        if (this.admit.patientChip) {
          this.admit.patientChip.style.display = 'none';
          this.admit.patientChip.innerHTML = '';
        }
      }
      if (q.length < 2) {
        if (this.admit.searchResults) {
          this.admit.searchResults.innerHTML = '';
        }
        if (!this.admit.patientId?.value) {
          this.admitSchemeProfileComplete = true;
          this.hideAdmitSchemeProfilePanel();
          this.toggleAdmitGovSchemePanel();
        }
        return;
      }
      const data = await window.pmFetch(`${this.routes.searchPatients}?q=${encodeURIComponent(q)}`);
      if (!Array.isArray(data) || data.length === 0) {
        if (this.admit.searchResults) {
          this.admit.searchResults.innerHTML = '<div class="tok-search-empty">No patient found</div>';
        }
        return;
      }
      if (!this.admit.searchResults) {
        return;
      }
      this.admit.searchResults.innerHTML = data.slice(0, 10).map((patient) => {
        const mrn = this.escapeHtml(patient.mrn || '-');
        const name = this.escapeHtml(patient.name || '-');
        const phone = this.escapeHtml(patient.phone || '-');
        const ageSex = this.escapeHtml(patient.age_sex || '-');
        return `
          <button type="button" class="tok-search-item" data-admit-patient="1" data-id="${patient.id}" data-mrn="${encodeURIComponent(patient.mrn || '')}" data-name="${encodeURIComponent(patient.name || '')}" data-phone="${encodeURIComponent(patient.phone || '')}" data-age-sex="${encodeURIComponent(patient.age_sex || '')}" data-gender="${encodeURIComponent(patient.gender || '')}" data-aadhar="${encodeURIComponent(patient.aadhar_no || '')}" data-ayushman="${encodeURIComponent(patient.ayushman_bharat_id || '')}">
            <div class="tok-search-name">${name}</div>
            <div class="tok-search-meta">${mrn} | ${phone} | ${ageSex}</div>
          </button>`;
      }).join('');
    }

    handleAdmitResultClick(event) {
      const item = event.target.closest('[data-admit-patient]');
      if (!item) {
        return;
      }
      this.selectAdmitPatientFromItem(item);
    }

    async loadAvailableBeds() {
      try {
        this.availableBeds = await window.pmFetch(this.routes.availableBeds);
        this.renderAvailableBeds();
      } catch (error) {
        sendmsg('error', error.message);
      }
    }

    renderAvailableBeds() {
      const currentWard = this.admit.ward?.value || '';
      const currentBed = this.admit.bed?.value || '';
      const normalizedBeds = (this.availableBeds || []).map((bed) => ({
        ...bed,
        ward: bed?.ward || '-',
        room_no: bed?.room_no || '-',
        bed_type: bed?.bed_type || '-',
        rate: Number(bed?.rate || 0),
      }));
      const wards = [...new Set(normalizedBeds.map((bed) => bed.ward).filter(Boolean))];
      this.admit.ward.innerHTML = '<option value="">All Wards</option>' + wards.map((ward) => `<option value="${this.escapeHtml(ward)}">${this.escapeHtml(ward)}</option>`).join('');
      if (currentWard && wards.includes(currentWard)) {
        this.admit.ward.value = currentWard;
      }
      const selectedWard = this.admit.ward.value;
      const beds = selectedWard ? normalizedBeds.filter((bed) => bed.ward === selectedWard) : normalizedBeds;
      this.admit.bed.innerHTML = '<option value="">Select Bed</option>' + beds.map((bed) => `
        <option
          value="${this.escapeHtml(bed.id)}"
          data-ward="${this.escapeHtml(bed.ward)}"
          data-room="${this.escapeHtml(bed.room_no)}"
          data-type="${this.escapeHtml(bed.bed_type)}"
          data-charge="${bed.rate.toFixed(2)}"
        >
          ${this.escapeHtml(bed.bed_no)} | ${this.escapeHtml(bed.ward)} / ${this.escapeHtml(bed.room_no)} | ${this.escapeHtml(bed.bed_type)}
        </option>`).join('');
      if (currentBed && beds.some((bed) => String(bed.id) === String(currentBed))) {
        this.admit.bed.value = currentBed;
      }
      this.admit.preview.innerHTML = this.renderBedPreviewGroups(beds, selectedWard);
      this.initModalSelect2(this.admit.form, this.admit.modal);
      this.syncPreviewSelection();
      const regBed = document.getElementById('reg_bed');
      if (regBed) {
        regBed.innerHTML = '<option value="">Select Bed</option>' + normalizedBeds.map((bed) => `
          <option
            value="${bed.id}"
            data-ward="${bed.ward || '-'}"
            data-room="${bed.room_no || '-'}"
            data-type="${bed.bed_type || '-'}"
            data-charge="${bed.rate.toFixed(2)}"
          >
            ${bed.bed_no} | ${bed.ward || '-'} / ${bed.room_no || '-'} | ${bed.bed_type || '-'}
          </option>`).join('');
        /* Do not focus bed after reload — user may be Tabbing through visit-type radios (IPD → Emergency → Daycare). */
        window.PatientRegistrationForm?.initSelect2?.(['#reg_bed'], { force: true });
        window.PatientRegistrationForm?.displayBedDetails?.();
        window.PatientRegistrationForm?.relocatePaymentControls?.();
        window.PatientRegistrationForm?.updateGovPaymentAvailability?.();
      }
    }

    renderBedPreviewGroups(beds, selectedWard) {
      if (!beds.length) {
        return '<span style="font-size:11px;color:var(--text-muted)">No beds available</span>';
      }

      const groupedBeds = beds.reduce((groups, bed) => {
        const wardName = bed.ward || '-';
        if (!groups[wardName]) {
          groups[wardName] = [];
        }
        groups[wardName].push(bed);
        return groups;
      }, {});

      const wardNames = selectedWard ? [selectedWard] : Object.keys(groupedBeds);
      return `<div class="bed-preview-groups">${wardNames.map((wardName) => {
        const wardBeds = groupedBeds[wardName] || [];
        return `
          <div class="bed-preview-group">
            <div class="bed-preview-group-head">
              <span>${this.escapeHtml(wardName)}</span>
              <span class="bed-preview-count">${wardBeds.length} beds</span>
            </div>
            <div class="bed-preview-list">
              ${wardBeds.map((bed) => `
                <button
                  class="bed-preview-chip"
                  type="button"
                  tabindex="-1"
                  data-bed-id="${this.escapeHtml(bed.id)}"
                  title="${this.escapeHtml(`${bed.bed_no} | ${bed.room_no} | ${bed.bed_type}`)}"
                >
                  ${this.escapeHtml(bed.bed_no)}
                  <span class="bed-preview-meta">${this.escapeHtml(`${bed.room_no} • ${bed.bed_type}`)}</span>
                </button>`).join('')}
            </div>
          </div>`;
      }).join('')}</div>`;
    }

    syncPreviewSelection() {
      const selectedBedId = String(this.admit.bed?.value || '');
      if (!this.admit.preview) {
        return;
      }
      this.admit.preview.querySelectorAll('[data-bed-id]').forEach((node) => {
        node.classList.toggle('active', selectedBedId && String(node.dataset.bedId) === selectedBedId);
      });
    }

    handleBedPreviewClick(event) {
      const chip = event.target.closest('[data-bed-id]');
      if (!chip) {
        return;
      }
      this.admit.bed.value = chip.dataset.bedId;
      if (window.jQuery) {
        jQuery(this.admit.bed).trigger('change.select2');
      }
      this.syncPreviewSelection();
      this.clearFieldError('admit_bed');
    }

    clearFormErrors(form) {
      if (!form) {
        return;
      }
      form.querySelectorAll('.has-error').forEach((group) => group.classList.remove('has-error'));
      form.querySelectorAll('.error').forEach((field) => field.classList.remove('error'));
      form.querySelectorAll('.field-error-message').forEach((node) => node.remove());
    }

    clearFieldError(fieldId) {
      if (!fieldId) {
        return;
      }
      const field = document.getElementById(fieldId);
      const group = field?.closest('.form-group');
      if (!group) {
        return;
      }
      group.classList.remove('has-error');
      field.classList.remove('error');
      group.querySelectorAll('.field-error-message').forEach((node) => node.remove());
    }

    setFieldError(fieldId, message) {
      const field = document.getElementById(fieldId);
      const group = field?.closest('.form-group');
      if (!group) {
        sendmsg('error', message);
        return;
      }
      group.classList.add('has-error');
      field.classList.add('error');
      group.querySelectorAll('.field-error-message').forEach((node) => node.remove());
      const errorNode = document.createElement('div');
      errorNode.className = 'field-error-message';
      errorNode.textContent = message;
      group.appendChild(errorNode);
    }

    applyErrors(fieldMap, errors) {
      if (!Array.isArray(errors) || !errors.length) {
        return false;
      }
      let firstFieldId = null;
      errors.forEach((error) => {
        const normalizedCode = String(error.code || '').replace(/\.\d+$/, '');
        const fieldId = fieldMap[error.code] || fieldMap[normalizedCode];
        if (!fieldId) {
          return;
        }
        if (!firstFieldId) {
          firstFieldId = fieldId;
        }
        this.setFieldError(fieldId, error.message || 'Invalid value');
      });
      if (firstFieldId) {
        document.getElementById(firstFieldId)?.focus?.();
        return true;
      }
      return false;
    }

    validateTokenForm() {
      this.clearFormErrors(this.token.form);
      if (!this.token.patientId.value) {
        this.setFieldError('tok_patient_search', 'Please select a patient.');
        this.token.patientSearch.focus();
        return false;
      }
      if (!this.token.dept.value) {
        this.setFieldError('tok_dept', 'Please select a department.');
        this.token.dept.focus();
        return false;
      }
      if (!this.token.doctor.value) {
        this.setFieldError('tok_doctor', 'Please select a doctor.');
        this.token.doctor.focus();
        return false;
      }
      if (!this.token.slot.value) {
        this.setFieldError('tok_slot', 'Please select a slot.');
        this.token.slot.focus();
        return false;
      }
      return true;
    }

    async submitTokenForm() {
      if (!this.validateTokenForm()) {
        return;
      }
      this.token.submitBtn.disabled = true;
      try {
        const data = await window.pmFetch(this.routes.issueToken, {
          method: 'POST',
          body: {
            patient_id: this.token.patientId.value,
            hr_department_id: this.token.dept.value,
            doctor_id: this.token.doctor.value || null,
            appointment_date: this.token.appointmentDate.value || null,
            appointment_time: this.token.slot.value ? window.pmConvertSlotTo24Hour(this.token.slot.value) : null,
            slot: this.token.slot.value || null,
            chief_complaint: this.token.complaint.value || null,
            visit_type: this.token.visitType?.value || 'OPD',
            payment_mode: this.token.payment.value || null,
            applied_charge: this.tokenAppliedCharge,
            priority: 'Normal',
          }
        });
        sendmsg('success', `Token ${data.token} issued successfully.`);
        closeModal('opdTokenModal');
        this.resetTokenForm(true);
        await window.pmRefreshPatientDashboard?.();
      } catch (error) {
        const handled = this.applyErrors(TOKEN_FIELD_MAP, error.responseData?.errors || []);
        if (!handled) {
          sendmsg('error', error.message);
        }
      } finally {
        this.token.submitBtn.disabled = false;
      }
    }

    isAdmitGovSchemePaymentLabel(paymentLabel) {
      return GOVERNMENT_PAYMENT_LABELS.has(String(paymentLabel || '').trim());
    }

    isAdmitGovSchemePanelVisible() {
      return this.isAdmitGovSchemePaymentLabel(this.admit.payment?.value || '');
    }

    patientSchemeProfileUrl(patientId, forUpdate = false) {
      const template = forUpdate
        ? this.routes.patientSchemeProfileUpdate
        : this.routes.patientSchemeProfile;
      if (!template || !patientId) {
        return null;
      }
      return String(template).replace('__ID__', String(patientId));
    }

    hideAdmitSchemeProfilePanel() {
      const block = document.getElementById('admitSchemeProfileBlock');
      if (block) {
        block.style.display = 'none';
      }
    }

    async refreshAdmitSchemeProfilePanel() {
      if (!this.isAdmitGovSchemePaymentLabel(this.admit.payment?.value || '') || !this.admit.patientId?.value) {
        this.admitSchemeProfileComplete = true;
        this.admitSchemeProfileMissing = [];
        this.hideAdmitSchemeProfilePanel();
        return;
      }
      const url = this.patientSchemeProfileUrl(this.admit.patientId.value, false);
      if (!url) {
        return;
      }
      try {
        const data = await window.pmFetch(url);
        this.applyAdmitSchemeProfilePayload(data);
      } catch (error) {
        sendmsg('error', error.message || 'Could not load patient profile for scheme admission.');
      }
    }

    applyAdmitSchemeProfilePayload(data) {
      const missing = Array.isArray(data?.missing) ? data.missing : [];
      const profile = data?.profile || {};
      this.admitSchemeProfileMissing = missing;
      this.admitSchemeProfileComplete = !!data?.complete || missing.length === 0;

      const block = document.getElementById('admitSchemeProfileBlock');
      const list = document.getElementById('admitSchemeProfileMissingList');
      const showProfile = this.isAdmitGovSchemePaymentLabel(this.admit.payment?.value || '')
        && !!this.admit.patientId?.value
        && !this.admitSchemeProfileComplete;

      if (block) {
        block.style.display = showProfile ? 'block' : 'none';
      }
      if (list) {
        list.innerHTML = missing
          .filter((key) => ADMIT_PROFILE_LABELS[key])
          .map((key) => `<li>${this.escapeHtml(ADMIT_PROFILE_LABELS[key])}</li>`)
          .join('');
      }

      const wraps = {
        address: 'admit_profile_address_wrap',
        pin_code: 'admit_profile_pin_wrap',
        state: 'admit_profile_state_wrap',
        district: 'admit_profile_district_wrap',
        ayushman_bharat_id: 'admit_profile_ab_wrap',
      };
      Object.entries(wraps).forEach(([key, wrapId]) => {
        const wrap = document.getElementById(wrapId);
        if (wrap) {
          wrap.style.display = missing.includes(key) ? '' : 'none';
        }
      });
      const aadhaarWrap = document.getElementById('admit_profile_aadhaar_wrap');
      if (aadhaarWrap) {
        aadhaarWrap.style.display = !String(profile.aadhar_no || '').trim() ? '' : 'none';
      }

      const addressEl = document.getElementById('admit_profile_address');
      if (addressEl && missing.includes('address')) {
        addressEl.value = profile.address || '';
      }
      const pinEl = document.getElementById('admit_profile_pin');
      if (pinEl && missing.includes('pin_code')) {
        pinEl.value = profile.pin_code || '';
      }
      const abEl = document.getElementById('admit_profile_ab');
      if (abEl && missing.includes('ayushman_bharat_id')) {
        abEl.value = profile.ayushman_bharat_id || '';
      }
      const aadhaarEl = document.getElementById('admit_profile_aadhaar');
      if (aadhaarEl && !String(profile.aadhar_no || '').trim()) {
        aadhaarEl.value = profile.aadhar_no || '';
      }
      const stateEl = document.getElementById('admit_profile_state');
      if (stateEl && missing.includes('state')) {
        stateEl.value = data?.state_id ? String(data.state_id) : '';
        if (missing.includes('district')) {
          void this.loadAdmitProfileDistricts(stateEl.value, data?.district_id || null, profile.district || '');
        }
      } else if (missing.includes('district')) {
        const stateForDistrict = document.getElementById('admit_profile_state')?.value || (data?.state_id ? String(data.state_id) : '');
        void this.loadAdmitProfileDistricts(stateForDistrict, data?.district_id || null, profile.district || '');
      }

      if (this.admitPatient) {
        this.admitPatient.ayushmanBharatId = profile.ayushman_bharat_id || this.admitPatient.ayushmanBharatId || '';
        this.admitPatient.aadharNo = profile.aadhar_no || this.admitPatient.aadharNo || '';
      }
    }

    async loadAdmitProfileDistricts(stateId, districtId = null, districtName = '') {
      const districtEl = document.getElementById('admit_profile_district');
      if (!districtEl) {
        return;
      }
      if (!stateId) {
        districtEl.innerHTML = '<option value="">Select District</option>';
        return;
      }
      const districts = await window.pmFetch(`${this.routes.loadDistricts}?state_id=${encodeURIComponent(stateId)}`);
      window.pmRenderOptions?.(districtEl, districts || [], { placeholder: 'Select District', valueKey: 'id', labelKey: 'name' });
      if (districtId) {
        districtEl.value = String(districtId);
      } else if (districtName) {
        const match = Array.from(districtEl.options).find((opt) => opt.text === districtName);
        if (match) {
          districtEl.value = match.value;
        }
      }
    }

    selectedAdmitProfileText(selectEl) {
      if (!selectEl || selectEl.tagName !== 'SELECT') {
        return null;
      }
      const text = selectEl.options[selectEl.selectedIndex]?.text?.trim() || '';
      if (!text || /^select\s/i.test(text)) {
        return null;
      }
      return text;
    }

    collectAdmitSchemeProfilePayload() {
      const body = {};
      const missing = this.admitSchemeProfileMissing || [];
      if (missing.includes('address')) {
        body.address = String(document.getElementById('admit_profile_address')?.value || '').trim();
      }
      if (missing.includes('pin_code')) {
        body.pin_code = String(document.getElementById('admit_profile_pin')?.value || '').trim();
      }
      if (missing.includes('state')) {
        body.state = this.selectedAdmitProfileText(document.getElementById('admit_profile_state'));
      }
      if (missing.includes('district')) {
        body.district = this.selectedAdmitProfileText(document.getElementById('admit_profile_district'));
      }
      if (missing.includes('ayushman_bharat_id')) {
        body.ayushman_bharat_id = String(document.getElementById('admit_profile_ab')?.value || '').trim();
      }
      const aadhaarWrap = document.getElementById('admit_profile_aadhaar_wrap');
      if (aadhaarWrap && aadhaarWrap.style.display !== 'none') {
        body.aadhar_no = String(document.getElementById('admit_profile_aadhaar')?.value || '').trim();
      }
      return body;
    }

    validateAdmitSchemeProfileFields(requireSaved = true) {
      if (this.admitSchemeProfileComplete) {
        return true;
      }
      const missing = this.admitSchemeProfileMissing || [];
      const payload = this.collectAdmitSchemeProfilePayload();
      if (missing.includes('address') && String(payload.address || '').length < 3) {
        this.setFieldError('admit_profile_address', 'Address is required for scheme admission.');
        document.getElementById('admit_profile_address')?.focus();
        return false;
      }
      if (missing.includes('pin_code') && String(payload.pin_code || '').length < 4) {
        this.setFieldError('admit_profile_pin', 'PIN code is required for scheme admission.');
        document.getElementById('admit_profile_pin')?.focus();
        return false;
      }
      if (missing.includes('state') && !payload.state) {
        this.setFieldError('admit_profile_state', 'State is required for scheme admission.');
        document.getElementById('admit_profile_state')?.focus();
        return false;
      }
      if (missing.includes('district') && !payload.district) {
        this.setFieldError('admit_profile_district', 'District is required for scheme admission.');
        document.getElementById('admit_profile_district')?.focus();
        return false;
      }
      if (missing.includes('ayushman_bharat_id') && String(payload.ayushman_bharat_id || '').length < 4) {
        this.setFieldError('admit_profile_ab', 'Ayushman Bharat ID is required for scheme admission.');
        document.getElementById('admit_profile_ab')?.focus();
        return false;
      }
      if (!requireSaved) {
        return true;
      }
      sendmsg('error', 'Save patient details in the highlighted section before scheme beneficiary verification.');
      document.getElementById('admit_scheme_profile_save')?.focus();
      return false;
    }

    async saveAdmitSchemeProfile() {
      if (!this.admit.patientId?.value) {
        sendmsg('error', 'Select a patient first.');
        return;
      }
      if (!this.validateAdmitSchemeProfileFields(false)) {
        return;
      }
      const url = this.patientSchemeProfileUrl(this.admit.patientId.value, true);
      if (!url) {
        sendmsg('error', 'Patient profile update is not configured.');
        return;
      }
      const saveBtn = document.getElementById('admit_scheme_profile_save');
      if (saveBtn) saveBtn.disabled = true;
      try {
        const data = await window.pmFetch(url, {
          method: 'POST',
          body: this.collectAdmitSchemeProfilePayload(),
        });
        this.applyAdmitSchemeProfilePayload(data);
        sendmsg('success', data?.message || 'Patient details saved.');
        if (this.admitSchemeProfileComplete) {
          this.toggleAdmitGovSchemePanel();
          this.preloadAdmitGovBeneficiarySearch();
        }
      } catch (error) {
        const handled = this.applyErrors(ADMIT_FIELD_MAP, error.responseData?.errors || []);
        if (!handled) {
          sendmsg('error', error.message || 'Could not save patient details.');
        }
      } finally {
        if (saveBtn) saveBtn.disabled = false;
      }
    }

    toggleAdmitGovSchemePanel() {
      const govBlock = document.getElementById('admitGovSchemeBlock');
      if (!govBlock) {
        return;
      }
      const showGov = this.isAdmitGovSchemePanelVisible() && this.admitSchemeProfileComplete;
      govBlock.style.display = showGov ? 'block' : 'none';
      if (showGov) {
        this.preloadAdmitGovBeneficiarySearch();
      } else if (!this.isAdmitGovSchemePaymentLabel(this.admit.payment?.value || '')) {
        this.resetAdmitGovSchemeState();
        this.hideAdmitSchemeProfilePanel();
      }
      this.syncAdmitGovOtpSendButton();
    }

    resetAdmitGovSchemeState() {
      this.admitGovBeneficiaryLocked = false;
      const lookup = document.getElementById('admit_scheme_lookup_token');
      const auth = document.getElementById('admit_scheme_auth_token');
      if (lookup) lookup.value = '';
      if (auth) auth.value = '';
      const res = document.getElementById('admit_gov_result');
      if (res) {
        res.style.display = 'none';
        res.innerHTML = '';
      }
      const kycGroup = document.getElementById('admit_gov_kyc_group');
      if (kycGroup) kycGroup.style.display = 'none';
      this.admitGovOtpInputRevealed = false;
      this.syncAdmitGovOtpRowVisibility();
      const otp = document.getElementById('admit_gov_otp');
      if (otp) otp.value = '';
      const without = document.querySelector('#admitGovSchemeBlock input[name="admit_gov_kyc"][value="without_auth"]');
      if (without) without.checked = true;
      this.applyAdmitGovBeneficiaryLockedUi();
      this.syncAdmitGovOtpSendButton();
    }

    setAdmitGovBeneficiaryLocked(locked) {
      this.admitGovBeneficiaryLocked = !!locked;
      this.applyAdmitGovBeneficiaryLockedUi();
    }

    applyAdmitGovBeneficiaryLockedUi() {
      const locked = this.admitGovBeneficiaryLocked;
      const search = document.getElementById('admit_gov_card_search');
      const btnSearch = document.getElementById('admit_gov_search_btn');
      const scheme = document.getElementById('admit_gov_scheme_id');
      const radios = document.querySelectorAll('#admitGovSchemeBlock input[name="admit_gov_kyc"]');
      const confirmB = document.getElementById('admit_gov_confirm_auth_btn');
      const clearB = document.getElementById('admit_gov_clear_btn');
      if (search) search.readOnly = locked;
      if (btnSearch) btnSearch.disabled = locked;
      if (scheme) scheme.disabled = locked;
      radios.forEach((r) => {
        r.disabled = locked;
      });
      if (confirmB) confirmB.disabled = locked;
      if (clearB) clearB.style.display = locked ? '' : 'none';
      const otpField = document.getElementById('admit_gov_otp');
      if (otpField) otpField.readOnly = locked;
      this.syncAdmitGovOtpSendButton();
    }

    syncAdmitGovOtpSendButton() {
      const sendOtp = document.getElementById('admit_gov_send_otp_btn');
      if (!sendOtp) {
        return;
      }
      const kyc = document.querySelector('#admitGovSchemeBlock input[name="admit_gov_kyc"]:checked')?.value;
      const hasLookup = !!document.getElementById('admit_scheme_lookup_token')?.value;
      const show = kyc === 'aadhar_otp' && hasLookup && this.isAdmitGovSchemePanelVisible();
      sendOtp.style.display = show ? '' : 'none';
      sendOtp.disabled = this.admitGovBeneficiaryLocked || !show;
      this.syncAdmitGovOtpRowVisibility();
    }

    syncAdmitGovOtpRowVisibility() {
      const row = document.getElementById('admit_gov_otp_row');
      if (!row) {
        return;
      }
      const kyc = document.querySelector('#admitGovSchemeBlock input[name="admit_gov_kyc"]:checked')?.value;
      row.style.display = kyc === 'aadhar_otp' && this.admitGovOtpInputRevealed ? '' : 'none';
    }

    handleAdmitGovKycChange() {
      if (this.admitGovBeneficiaryLocked) {
        return;
      }
      this.clearFieldError('admit_gov_kyc_group');
      const auth = document.getElementById('admit_scheme_auth_token');
      if (auth) auth.value = '';
      this.admitGovOtpInputRevealed = false;
      const otpEl = document.getElementById('admit_gov_otp');
      if (otpEl) otpEl.value = '';
      this.syncAdmitGovOtpRowVisibility();
      this.syncAdmitGovOtpSendButton();
    }

    renderAdmitGovBeneficiarySummary(b) {
      return `<div><b>Name:</b> ${this.escapeHtml(b.name)}</div>
        <div><b>Card / ID:</b> ${this.escapeHtml(b.card_id)}</div>
        <div><b>Scheme:</b> ${this.escapeHtml(b.care_plan)}</div>`;
    }

    preloadAdmitGovBeneficiarySearch() {
      if (this.admitGovBeneficiaryLocked) {
        return;
      }
      const searchEl = document.getElementById('admit_gov_card_search');
      if (!searchEl || !this.isAdmitGovSchemePanelVisible()) {
        return;
      }
      const ab = String(this.admitPatient?.ayushmanBharatId || '').trim();
      if (ab) {
        searchEl.value = ab;
        return;
      }
      const raw = String(this.admitPatient?.aadharNo || '').replace(/\D/g, '');
      if (raw.length >= 4) {
        searchEl.value = raw;
      }
    }

    async runAdmitGovBeneficiarySearch() {
      if (this.admitGovBeneficiaryLocked) {
        return;
      }
      this.clearFieldError('admit_gov_card_search');
      this.clearFieldError('admit_gov_scheme_id');
      const authEl = document.getElementById('admit_scheme_auth_token');
      if (authEl) authEl.value = '';
      const schemeId = document.getElementById('admit_gov_scheme_id')?.value;
      const search = String(document.getElementById('admit_gov_card_search')?.value || '').trim();
      if (!schemeId) {
        this.setFieldError('admit_gov_scheme_id', 'Select a scheme first.');
        return;
      }
      if (search.length < 4) {
        this.setFieldError('admit_gov_card_search', 'Enter at least 4 characters.');
        return;
      }
      if (!this.routes.schemeBeneficiaryLookup) {
        sendmsg('error', 'Beneficiary search is not configured.');
        return;
      }
      try {
        const data = await window.pmFetch(this.routes.schemeBeneficiaryLookup, {
          method: 'POST',
          body: {
            scheme_type_id: schemeId,
            beneficiary_search: search,
          },
        });
        if (!data?.success) {
          sendmsg('error', data?.msg || 'Beneficiary not found.');
          return;
        }
        const lt = document.getElementById('admit_scheme_lookup_token');
        if (lt) lt.value = data.lookup_token || '';
        this.admitGovOtpInputRevealed = false;
        const res = document.getElementById('admit_gov_result');
        const kycGroup = document.getElementById('admit_gov_kyc_group');
        if (res) {
          res.style.display = 'block';
          res.innerHTML = this.renderAdmitGovBeneficiarySummary(data.beneficiary || {});
        }
        if (kycGroup) kycGroup.style.display = 'block';
        this.handleAdmitGovKycChange();
        this.syncAdmitGovOtpSendButton();
        sendmsg('success', 'Beneficiary loaded. Complete authentication below.');
      } catch (error) {
        const fromBody =
          error.responseData && typeof window.pmExtractErrorMessage === 'function'
            ? window.pmExtractErrorMessage(error.responseData)
            : '';
        sendmsg('error', fromBody || error.message || 'Search failed.');
      }
    }

    async sendAdmitGovSchemeOtp() {
      if (this.admitGovBeneficiaryLocked) {
        return;
      }
      const schemeId = document.getElementById('admit_gov_scheme_id')?.value;
      const search = String(document.getElementById('admit_gov_card_search')?.value || '').trim();
      const lookupToken = document.getElementById('admit_scheme_lookup_token')?.value;
      const kyc = document.querySelector('#admitGovSchemeBlock input[name="admit_gov_kyc"]:checked')?.value;
      if (kyc !== 'aadhar_otp' || !lookupToken) {
        sendmsg('error', 'Search the beneficiary before sending OTP.');
        return;
      }
      if (!this.routes.schemeBeneficiarySendOtp) {
        sendmsg('error', 'Send OTP is not configured.');
        return;
      }
      try {
        const data = await window.pmFetch(this.routes.schemeBeneficiarySendOtp, {
          method: 'POST',
          body: {
            scheme_type_id: schemeId,
            beneficiary_search: search,
            lookup_token: lookupToken,
            kyc_type: 'aadhar_otp',
          },
        });
        if (!data?.success) {
          sendmsg('error', data?.msg || 'Could not send OTP.');
          return;
        }
        this.admitGovOtpInputRevealed = true;
        this.syncAdmitGovOtpRowVisibility();
        const otpEl = document.getElementById('admit_gov_otp');
        if (otpEl) {
          otpEl.value = '';
          window.requestAnimationFrame(() => otpEl.focus());
        }
        const stub = data.test_otp != null && data.test_otp !== '' ? String(data.test_otp) : '';
        const base = data.message || 'OTP sent.';
        sendmsg('success', stub ? `${base} Test OTP: ${stub}` : base);
      } catch (error) {
        const fromBody =
          error.responseData && typeof window.pmExtractErrorMessage === 'function'
            ? window.pmExtractErrorMessage(error.responseData)
            : '';
        sendmsg('error', fromBody || error.message || 'Send OTP failed.');
      }
    }

    async runAdmitGovConfirmAuth() {
      if (this.admitGovBeneficiaryLocked) {
        return;
      }
      this.clearFieldError('admit_gov_kyc_group');
      this.clearFieldError('admit_gov_otp');
      const schemeId = document.getElementById('admit_gov_scheme_id')?.value;
      const search = String(document.getElementById('admit_gov_card_search')?.value || '').trim();
      const lookupToken = document.getElementById('admit_scheme_lookup_token')?.value;
      const kyc = document.querySelector('#admitGovSchemeBlock input[name="admit_gov_kyc"]:checked')?.value || 'without_auth';
      if (!lookupToken) {
        this.setFieldError('admit_gov_card_search', 'Search the beneficiary first.');
        return;
      }
      let otp = '';
      if (kyc === 'aadhar_otp') {
        if (!this.admitGovOtpInputRevealed) {
          sendmsg('error', 'Send OTP first, then enter the 6-digit code.');
          return;
        }
        otp = String(document.getElementById('admit_gov_otp')?.value || '').trim();
        if (!/^\d{6}$/.test(otp)) {
          this.setFieldError('admit_gov_otp', 'Enter the 6-digit OTP.');
          return;
        }
      }
      if (!this.routes.schemeBeneficiaryConfirmAuth) {
        sendmsg('error', 'Authentication endpoint is not configured.');
        return;
      }
      try {
        const data = await window.pmFetch(this.routes.schemeBeneficiaryConfirmAuth, {
          method: 'POST',
          body: {
            scheme_type_id: schemeId,
            beneficiary_search: search,
            lookup_token: lookupToken,
            kyc_type: kyc,
            otp: otp || null,
          },
        });
        if (!data?.success) {
          sendmsg('error', data?.msg || 'Authentication failed.');
          return;
        }
        const authEl = document.getElementById('admit_scheme_auth_token');
        if (authEl) authEl.value = data.auth_token || '';
        this.setAdmitGovBeneficiaryLocked(true);
        sendmsg('success', 'Beneficiary authentication recorded. You can admit the patient.');
      } catch (error) {
        const fromBody =
          error.responseData && typeof window.pmExtractErrorMessage === 'function'
            ? window.pmExtractErrorMessage(error.responseData)
            : '';
        sendmsg('error', fromBody || error.message || 'Authentication failed.');
      }
    }

    validateAdmitGovSchemeFields() {
      if (!this.isAdmitGovSchemePanelVisible() || !this.admitSchemeProfileComplete) {
        return true;
      }
      if (!document.getElementById('admit_gov_scheme_id')?.value) {
        this.setFieldError('admit_gov_scheme_id', 'Select a scheme.');
        document.getElementById('admit_gov_scheme_id')?.focus();
        return false;
      }
      const cardSearch = String(document.getElementById('admit_gov_card_search')?.value || '').trim();
      if (cardSearch.length < 4) {
        this.setFieldError('admit_gov_card_search', 'Enter at least 4 characters to search the beneficiary.');
        document.getElementById('admit_gov_card_search')?.focus();
        return false;
      }
      if (!document.getElementById('admit_scheme_lookup_token')?.value) {
        this.setFieldError('admit_gov_card_search', 'Search and load the beneficiary before admitting.');
        document.getElementById('admit_gov_card_search')?.focus();
        return false;
      }
      if (!document.getElementById('admit_scheme_auth_token')?.value) {
        this.setFieldError('admit_gov_kyc_group', 'Complete beneficiary authentication (Confirm authentication).');
        document.getElementById('admit_gov_confirm_auth_btn')?.focus();
        return false;
      }
      const kyc = document.querySelector('#admitGovSchemeBlock input[name="admit_gov_kyc"]:checked')?.value;
      if (kyc === 'aadhar_otp') {
        const otp = String(document.getElementById('admit_gov_otp')?.value || '').trim();
        if (!/^\d{6}$/.test(otp)) {
          this.setFieldError('admit_gov_otp', 'Enter the 6-digit OTP used for authentication.');
          document.getElementById('admit_gov_otp')?.focus();
          return false;
        }
      }
      return true;
    }

    collectAdmitPayload() {
      const body = {
        patient_id: this.admit.patientId.value,
        hr_department_id: this.admit.dept.value,
        doctor_id: this.admit.doctor.value || null,
        bed_id: this.admit.bed.value,
        admission_reason: this.admit.reason.value || null,
        payment_mode: this.admit.payment.value || null,
        advance_deposit: this.admit.advance.value || 0,
      };
      if (this.isAdmitGovSchemePaymentLabel(this.admit.payment?.value) && this.admitSchemeProfileComplete) {
        const kyc = document.querySelector('#admitGovSchemeBlock input[name="admit_gov_kyc"]:checked')?.value || 'without_auth';
        body.scheme_type_id = document.getElementById('admit_gov_scheme_id')?.value || null;
        body.scheme_beneficiary_card_id = String(document.getElementById('admit_gov_card_search')?.value || '').trim() || null;
        body.scheme_kyc_type = kyc;
        body.scheme_lookup_token = document.getElementById('admit_scheme_lookup_token')?.value || null;
        body.scheme_auth_token = document.getElementById('admit_scheme_auth_token')?.value || null;
        body.scheme_aadhar_otp = kyc === 'aadhar_otp' ? String(document.getElementById('admit_gov_otp')?.value || '').trim() || null : null;
      }
      return body;
    }

    validateAdmitForm() {
      this.clearFormErrors(this.admit.form);
      if (!this.admit.patientId.value) {
        this.setFieldError('admit_patient_search', 'Please select a patient.');
        this.admit.patientSearch.focus();
        return false;
      }
      if (!this.admit.dept.value) {
        this.setFieldError('admit_dept', 'Please select a department.');
        this.admit.dept.focus();
        return false;
      }
      if (!this.admit.bed.value) {
        this.setFieldError('admit_bed', 'Please select a bed.');
        this.admit.bed.focus();
        return false;
      }
      if (!this.validateAdmitSchemeProfileFields()) {
        return false;
      }
      if (!this.validateAdmitGovSchemeFields()) {
        return false;
      }
      return true;
    }

    async submitAdmitForm() {
      if (!this.validateAdmitForm()) {
        return;
      }
      this.admit.submitBtn.disabled = true;
      try {
        const data = await window.pmFetch(this.routes.ipdAdmit, {
          method: 'POST',
          body: this.collectAdmitPayload(),
        });
        let successMsg = `Admission ${data.admission_no} created. Bed ${data.bed_no}.`;
        if (data.scheme_preauth_url) {
          successMsg += ' Scheme preauth draft created.';
        }
        sendmsg('success', successMsg);
        closeModal('ipdAdmitModal');
        if (data.scheme_preauth_url) {
          window.location.href = data.scheme_preauth_url;
          return;
        }
        this.resetAdmitForm(true);
        await window.pmRefreshPatientDashboard?.();
        await this.loadAvailableBeds();
      } catch (error) {
        const handled = this.applyErrors(ADMIT_FIELD_MAP, error.responseData?.errors || []);
        if (!handled) {
          sendmsg('error', error.message);
        }
      } finally {
        this.admit.submitBtn.disabled = false;
      }
    }
  }

  window.PatientVisitModals = new PatientVisitModalsController();
})();
