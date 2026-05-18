(function () {
  const STEP_ORDER = [1, 2, 3, 4, 5];
  const STEP_CONFIG = {
    1: { paneId: 'regPane1', stepId: 'step1', fields: ['reg_name', 'reg_dob', 'reg_gender'] },
    2: { paneId: 'regPane2', stepId: 'step2', fields: ['reg_phone', 'reg_address'] },
    3: { paneId: 'regPane3', stepId: 'step3', fields: [] },
    4: { paneId: 'regPane4', stepId: 'step4', fields: ['reg_dept'] },
    5: { paneId: 'regPane5', stepId: 'step5', fields: [] },
  };

  const GOVERNMENT_PAYMENT_LABELS = new Set([
    'State Health Scheme / AB-PMJAY (Ayushman Bharat)',
    'AB-PMJAY (Ayushman Bharat)',
    'CGHS',
    'ECHS',
    'State Health Scheme',
    'ESI',
  ]);

  const FIELD_TO_STEP = {
    reg_name: 1,
    reg_dob: 1,
    reg_gender: 1,
    reg_category: 1,
    reg_phone: 2,
    reg_address: 2,
    reg_pin: 2,
    reg_state: 2,
    reg_district: 2,
    reg_nationality: 2,
    reg_ab: 1,
    reg_dept: 4,
    reg_doctor: 4,
    reg_slot: 4,
    reg_bed: 4,
    reg_advance_deposit: 4,
    reg_complaint: 4,
    reg_visit_type: 4,
    reg_chronic_conditions: 3,
    reg_gov_scheme_id: 4,
    reg_gov_card_search: 4,
    reg_gov_otp: 4,
    reg_gov_kyc_group: 4,
    reg_scheme_newborn: 1,
  };

  const SERVER_FIELD_MAP = {
    title: 'reg_title',
    name: 'reg_name',
    date_of_birth: 'reg_dob',
    age_years: 'reg_age',
    gender: 'reg_gender',
    blood_group: 'reg_blood',
    aadhar_no: 'reg_aadhaar',
    ayushman_bharat_id: 'reg_ab',
    marital_status: 'reg_marital_status',
    religion_id: 'reg_religion',
    occupation: 'reg_occupation',
    patient_category_id: 'reg_category',
    category: 'reg_category',
    phone: 'reg_phone',
    alternate_phone: 'reg_alt_phone',
    email: 'reg_email',
    address: 'reg_address',
    pin_code: 'reg_pin',
    district: 'reg_district',
    state: 'reg_state',
    nationality: 'reg_nationality',
    emergency_contact_name: 'reg_emergency_name',
    emergency_contact_relation: 'reg_emergency_relation',
    emergency_contact_phone: 'reg_emergency_phone',
    known_allergies: 'allergyInput',
    chronic_conditions: 'reg_chronic_conditions',
    past_surgical_history: 'reg_past_surgery',
    current_medications: 'reg_current_medications',
    family_history: 'reg_family_history',
    smoking_status: 'reg_smoking',
    alcohol_status: 'reg_alcohol',
    vaccination_status: 'reg_vaccination',
    visit_type: 'reg_visit_type',
    hr_department_id: 'reg_dept',
    doctor_id: 'reg_doctor',
    appointment_date: 'reg_appointment_date',
    appointment_time: 'reg_slot',
    slot: 'reg_slot',
    chief_complaint: 'reg_complaint',
    payment_mode: 'reg_payment',
    applied_charge: 'reg_fee',
    advance_deposit: 'reg_advance_deposit',
    bed_id: 'reg_bed',
    admission_reason: 'reg_admission_reason',
    casualty: 'reg_visit_type',
    scheme_beneficiary_card_id: 'reg_gov_card_search',
    scheme_type_id: 'reg_gov_scheme_id',
    pin_code: 'reg_pin',
    state: 'reg_state',
    district: 'reg_district',
    ayushman_bharat_id: 'reg_ab',
  };

  class PatientRegistrationFormController {
    constructor() {
      this.currentStep = 1;
      this.lastErrorFieldId = null;
      this.routes = {};
      this.boot = {};
      this.bound = false;
      this.modalObserver = null;
      this.elements = {};
      this.flatpickrRetryCount = 0;
      this._shortcutHandler = (event) => this.handleRegistrationShortcuts(event);
      this._focusTrapHandler = (event) => this.handleModalFocusTrap(event);
      this._documentFocusContainHandler = (event) => this.handleDocumentFocusContain(event);
      this._documentFocusContainBound = false;
      this.govBeneficiaryLocked = false;
      /** After Send OTP succeeds, show the OTP input (hidden until then for Aadhaar OTP flow). */
      this.govOtpInputRevealed = false;
    }

    init({ routes, boot }) {
      this.routes = routes || this.routes;
      this.boot = boot || this.boot;
      this.cacheElements();
      if (!this.elements.modal || !this.elements.form) {
        return;
      }

      this.renderStaticOptions();
      this.ensureAppointmentDate();
      this.initFlatpickr();
      this.bindEvents();
      this.observeModalVisibility();
      this.resetFormState({ preserveValues: true });
    }

    cacheElements() {
      this.elements = {
        modal: document.getElementById('newPatientModal'),
        form: document.getElementById('patientRegistrationForm'),
        prevBtn: document.getElementById('regPrevBtn'),
        nextBtn: document.getElementById('regNextBtn'),
        submitBtn: document.getElementById('regSubmitBtn'),
        summaryLeft: document.getElementById('summLeft'),
        summaryRight: document.getElementById('summRight'),
        genMrn: document.getElementById('genMRN'),
        genToken: document.getElementById('genToken'),
        genFee: document.getElementById('genFee'),
        feeGroup: document.getElementById('regFeeGroup'),
        allergyInput: document.getElementById('allergyInput'),
        allergyChips: document.getElementById('allergyChips'),
        allergyAddBtn: document.getElementById('allergyAddBtn'),
        appointmentDate: document.getElementById('reg_appointment_date'),
      };
    }

    bindEvents() {
      if (this.bound) {
        return;
      }

      this.elements.form.addEventListener('submit', (event) => {
        event.preventDefault();
        this.submitRegistration();
      });
      this.elements.prevBtn?.addEventListener('click', () => this.moveStep(-1));
      this.elements.nextBtn?.addEventListener('click', () => this.moveStep(1));
      this.elements.allergyAddBtn?.addEventListener('click', () => this.addAllergy());
      this.elements.allergyChips?.addEventListener('click', (event) => {
        const chip = event.target.closest('[data-allergy-chip]');
        if (chip) {
          chip.remove();
        }
      });
      this.elements.allergyInput?.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter') {
          return;
        }
        event.preventDefault();
        this.addAllergy();
      });

      document.getElementById('reg_dob')?.addEventListener('change', () => this.calcAge());
      document.getElementById('reg_scheme_newborn')?.addEventListener('change', () => this.syncSchemeNewbornDetailsVisibility());
      document.getElementById('reg_state')?.addEventListener('change', (event) => this.loadRegistrationDistricts(event.target.value));
      document.getElementById('reg_dept')?.addEventListener('change', async () => {
        await this.loadRegistrationDoctors();
        await this.loadRegistrationCharge();
      });
      document.getElementById('reg_doctor')?.addEventListener('change', async () => {
        await this.loadRegistrationSlots();
        await this.loadRegistrationCharge();
      });
      document.getElementById('reg_appointment_date')?.addEventListener('change', () => this.loadRegistrationSlots());

      document.getElementById('reg_bed')?.addEventListener('change', () => {
        this.displayBedDetails();
        this.applyVisitDateVisibility(this.getVisitType());
        this.updateGovPaymentAvailability();
      });

      if (window.jQuery) {
        jQuery(document).on('select2:select select2:clear', '#reg_state', (event) => {
          this.loadRegistrationDistricts(event?.target?.value || '');
        });
        jQuery(document).on('select2:select select2:clear', '#reg_dept', async () => {
          await this.loadRegistrationDoctors();
          await this.loadRegistrationCharge();
        });
        jQuery(document).on('select2:select select2:clear', '#reg_doctor', async () => {
          await this.loadRegistrationSlots();
          await this.loadRegistrationCharge();
        });
        jQuery(document).on('select2:select select2:clear', '#reg_bed', () => {
          this.displayBedDetails();
          this.applyVisitDateVisibility(this.getVisitType());
          this.updateGovPaymentAvailability();
        });
        jQuery(document).on('select2:select select2:clear', '#reg_payment', () => {
          this.clearFieldError('reg_payment');
          if (!this.isGovSchemePaymentLabel(document.getElementById('reg_payment')?.value)) {
            this.resetGovSchemeState();
          }
          this.updateGovPaymentAvailability();
        });

        /* Select2 + dropdownParent(modal) often leaves focus on <body> after picking an option; restore for keyboard UX.
           Skip fields whose handlers already move focus (district / doctor / slot). */
        const skipSelect2FocusRestore = new Set(['reg_state', 'reg_dept', 'reg_doctor']);
        jQuery(document).on('select2:select select2:clear', '#newPatientModal select.select2-hidden-accessible', function () {
          const id = this.id;
          if (!id || skipSelect2FocusRestore.has(id)) {
            return;
          }
          const selectEl = this;
          window.requestAnimationFrame(() => {
            window.requestAnimationFrame(() => {
              const modal = document.getElementById('newPatientModal');
              if (!modal || modal.classList.contains('hidden')) {
                return;
              }
              const selection = selectEl.nextElementSibling?.querySelector?.('.select2-selection');
              if (!selection) {
                return;
              }
              if (document.activeElement === selection) {
                return;
              }
              selection.focus({ preventScroll: true });
            });
          });
        });
      }

      this.elements.form.querySelectorAll('input, select, textarea').forEach((field) => {
        field.addEventListener('input', () => this.clearFieldError(field.id));
        field.addEventListener('change', () => this.clearFieldError(field.id));
      });

      document.querySelectorAll('#newPatientModal input[name="visitType"]').forEach((radio) => {
        radio.addEventListener('change', () => {
          this.clearFieldError('reg_visit_type');
          this.updateVisitType(radio.value);
        });
      });

      document.getElementById('reg_payment')?.addEventListener('change', () => {
        this.clearFieldError('reg_payment');
        if (!this.isGovSchemePaymentLabel(document.getElementById('reg_payment')?.value)) {
          this.resetGovSchemeState();
        }
        this.updateGovPaymentAvailability();
      });
      document.getElementById('reg_gov_scheme_id')?.addEventListener('change', () => {
        if (this.govBeneficiaryLocked) {
          return;
        }
        this.clearFieldError('reg_gov_scheme_id');
        const lt = document.getElementById('reg_scheme_lookup_token');
        const at = document.getElementById('reg_scheme_auth_token');
        if (lt) lt.value = '';
        if (at) at.value = '';
        this.preloadGovBeneficiarySearchFromStep1();
        this.syncGovOtpSendButton();
      });
      document.getElementById('reg_gov_search_btn')?.addEventListener('click', () => void this.runGovBeneficiarySearch());
      document.getElementById('reg_gov_confirm_auth_btn')?.addEventListener('click', () => void this.runGovConfirmAuth());
      document.getElementById('reg_gov_send_otp_btn')?.addEventListener('click', () => void this.sendGovSchemeOtp());
      document.getElementById('reg_gov_clear_btn')?.addEventListener('click', () => {
        this.resetGovSchemeState();
        sendmsg('info', 'Beneficiary cleared — you can search again.');
      });
      document.getElementById('reg_gov_card_search')?.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
          event.preventDefault();
          void this.runGovBeneficiarySearch();
        }
      });
      document.getElementById('reg_gov_otp')?.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
          event.preventDefault();
          void this.runGovConfirmAuth();
        }
      });
      document.querySelectorAll('#regGovSchemeBlock input[name="reg_gov_kyc"]').forEach((r) => {
        r.addEventListener('change', () => this.handleGovKycChange());
      });
      document.getElementById('reg_ab')?.addEventListener('input', () => {
        if (this.isGovSchemePanelVisible()) {
          this.preloadGovBeneficiarySearchFromStep1();
        }
      });
      document.getElementById('reg_aadhaar')?.addEventListener('input', () => {
        if (this.isGovSchemePanelVisible()) {
          this.preloadGovBeneficiarySearchFromStep1();
        }
      });

      this.elements.submitBtn?.addEventListener('click', (event) => {
        event.preventDefault();
        this.submitRegistration();
      });

      this.elements.modal?.addEventListener('keydown', this._shortcutHandler, true);
      this.elements.modal?.addEventListener('keydown', this._focusTrapHandler, true);

      this.elements.form.addEventListener('keydown', (event) => this.handleRegistrationFormKeydown(event), true);

      if (!this._documentFocusContainBound) {
        this._documentFocusContainBound = true;
        document.addEventListener('focusin', this._documentFocusContainHandler, true);
      }

      this.bound = true;
    }

    observeModalVisibility() {
      if (this.modalObserver || !this.elements.modal) {
        return;
      }

      this.modalObserver = new MutationObserver(() => {
        if (this.elements.modal.classList.contains('hidden')) {
          this.resetFormState({ preserveValues: false });
          return;
        }
        this.handleModalOpened();
      });

      this.modalObserver.observe(this.elements.modal, { attributes: true, attributeFilter: ['class'] });
    }

    handleRegistrationShortcuts(event) {
      if (!this.elements.modal || this.elements.modal.classList.contains('hidden')) {
        return;
      }
      if (!event.altKey || event.repeat) {
        return;
      }
      const key = String(event.key || '').toLowerCase();
      if (key === 'n') {
        event.preventDefault();
        event.stopPropagation();
        void this.moveStep(1);
      } else if (key === 'b') {
        event.preventDefault();
        event.stopPropagation();
        void this.moveStep(-1);
      }
    }

    handleModalOpened() {
      this.stripStaleRegistrationTabIndex();
      this.ensureAppointmentDate();
      this.initFlatpickr();
      this.resetWizardState();
      this.syncVisibleSelect2();
      this.updateVisitType(this.getVisitType(), { syncSlots: false });
      this.updateGovPaymentAvailability();
      this.focusRegistrationName();
    }

    focusRegistrationName() {
      const nameField = document.getElementById('reg_name');
      if (!nameField) {
        return;
      }

      window.setTimeout(() => {
        nameField.focus();
      }, 40);
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
      this.setupFlatpickrField('reg_dob', {
        altInput: true,
        altFormat: 'd-m-Y',
        dateFormat: 'Y-m-d',
        maxDate: 'today',
        allowInput: true,
      });
      this.setupFlatpickrField('reg_appointment_date', {
        altInput: true,
        altFormat: 'd-m-Y',
        dateFormat: 'Y-m-d',
        minDate: 'today',
        allowInput: true,
      });
    }

    setupFlatpickrField(fieldId, config) {
      const field = document.getElementById(fieldId);
      if (!field) {
        return;
      }

      const onChange = [];
      if (typeof config.onChange === 'function') {
        onChange.push(config.onChange);
      }

      if (fieldId === 'reg_appointment_date') {
        onChange.push(() => {
          this.loadRegistrationSlots();
        });
      }

      if (fieldId === 'reg_dob') {
        onChange.push(() => this.calcAge());
      }

      if (field._flatpickr) {
        field._flatpickr.destroy();
      }

      const ensureAltInputEditable = (instance) => {
        const alt = instance?.altInput;
        if (!alt) {
          return;
        }
        alt.removeAttribute('readonly');
        alt.setAttribute('placeholder', 'DD-MM-YYYY');
        alt.setAttribute('inputmode', 'numeric');
        alt.setAttribute('autocomplete', 'off');
      };

      window.flatpickr(field, {
        ...config,
        clickOpens: true,
        onChange,
        onReady(selectedDates, dateStr, instance) {
          ensureAltInputEditable(instance);
          if (typeof config.onReady === 'function') {
            config.onReady(selectedDates, dateStr, instance);
          }
        },
        onOpen(selectedDates, dateStr, instance) {
          ensureAltInputEditable(instance);
          if (typeof config.onOpen === 'function') {
            config.onOpen(selectedDates, dateStr, instance);
          }
        },
      });
    }

    resetFormState({ preserveValues }) {
      this.clearAllErrors();
      if (!preserveValues) {
        this.elements.form.reset();
        if (this.elements.allergyChips) {
          this.elements.allergyChips.innerHTML = '';
        }
        this.renderStaticOptions();
        this.resetGovSchemeState();
      }
      this.ensureAppointmentDate();
      this.resetSummary();
      this.resetWizardState();
      this.updateVisitType(this.getVisitType(), { syncSlots: false });
      this.syncVisibleSelect2();
      this.updateGovPaymentAvailability();
      this.syncSchemeNewbornDetailsVisibility();
    }

    renderStaticOptions() {
      window.pmRenderOptions?.(document.getElementById('reg_state'), this.boot.states || [], { placeholder: 'Select State' });
      window.pmRenderOptions?.(document.getElementById('reg_dept'), this.boot.departments || [], { placeholder: 'Select Department' });
      this.resetDoctorOptions();
      this.resetSlotOptions();
    }

    ensureAppointmentDate() {
      if (this.elements.appointmentDate && !this.elements.appointmentDate.value) {
        this.elements.appointmentDate.value = new Date().toISOString().slice(0, 10);
      }
    }

    resetDoctorOptions() {
      const field = document.getElementById('reg_doctor');
      if (field) {
        field.innerHTML = '<option value="">Select Doctor</option>';
      }
    }

    resetSlotOptions() {
      const field = document.getElementById('reg_slot');
      if (field) {
        field.innerHTML = '<option value="">Select Slot</option>';
      }
    }

    resetSummary() {
      if (this.elements.summaryLeft) this.elements.summaryLeft.innerHTML = '';
      if (this.elements.summaryRight) this.elements.summaryRight.innerHTML = '';
      if (this.elements.genMrn) this.elements.genMrn.textContent = '—';
      if (this.elements.genToken) this.elements.genToken.textContent = '—';
      if (this.elements.genFee) this.elements.genFee.textContent = '₹0';
    }

    resetWizardState() {
      this.currentStep = 1;
      this.setStep(this.currentStep, { focusFirst: false });
    }

    setStep(stepNumber, options = {}) {
      const focusFirst = options.focusFirst !== false;
      STEP_ORDER.forEach((number) => {
        const config = STEP_CONFIG[number];
        const pane = document.getElementById(config.paneId);
        const step = document.getElementById(config.stepId);
        if (pane) {
          const isActive = number === stepNumber;
          pane.style.display = isActive ? 'block' : 'none';
          if (isActive) {
            pane.removeAttribute('inert');
          } else {
            pane.setAttribute('inert', '');
          }
        }
        if (step) {
          step.classList.toggle('active', number === stepNumber);
          step.classList.toggle('done', number < stepNumber);
        }
      });
      this.currentStep = stepNumber;
      this.elements.prevBtn.style.display = stepNumber > 1 ? '' : 'none';
      this.elements.nextBtn.style.display = stepNumber < STEP_ORDER.length ? '' : 'none';
      this.elements.submitBtn.style.display = stepNumber === STEP_ORDER.length ? '' : 'none';
      this.syncVisibleSelect2();
      if (stepNumber === 4) {
        this.updateGovPaymentAvailability();
      }
      if (stepNumber === STEP_ORDER.length) {
        this.prepareStepFivePreview();
      }
      if (focusFirst) {
        window.requestAnimationFrame(() => this.focusFirstInActivePane());
      }
    }

    async moveStep(direction) {
      if (direction > 0) {
        const valid = await this.validateStep(this.currentStep);
        if (!valid) {
          return;
        }
      }

      const nextStep = Math.max(1, Math.min(STEP_ORDER.length, this.currentStep + direction));
      this.setStep(nextStep, { focusFirst: true });
    }

    async validateStep(stepNumber) {
      if (stepNumber === this.currentStep) {
        this.lastErrorFieldId = null;
      }
      this.clearStepErrors(stepNumber);
      const config = STEP_CONFIG[stepNumber];
      for (const fieldId of config.fields) {
        const field = document.getElementById(fieldId);
        if (!field || String(field.value || '').trim()) {
          continue;
        }
        this.setFieldError(fieldId, this.validationMessage(fieldId));
        field.focus();
        return false;
      }

      if (stepNumber === 2) {
        const phone = (document.getElementById('reg_phone')?.value || '').replace(/\D/g, '');
        if (phone.length !== 10) {
          this.setFieldError('reg_phone', 'Mobile number must be 10 digits.');
          document.getElementById('reg_phone')?.focus();
          return false;
        }
      }

      if (stepNumber === 1 && !this.validateSchemeNewbornFields()) {
        return false;
      }

      if (stepNumber === 4) {
        const visitType = this.getVisitType();
        const selectedBed = document.getElementById('reg_bed')?.value || '';
        const isEmergencyBedAdmission = visitType === 'Emergency' && !!selectedBed;
        if (!visitType) {
          this.setFieldError('reg_visit_type', 'Visit type required.');
          return false;
        }
        if (['OPD', 'Daycare'].includes(visitType) || (visitType === 'Emergency' && !isEmergencyBedAdmission)) {
          const doctor = document.getElementById('reg_doctor')?.value;
          const slot = document.getElementById('reg_slot')?.value;
          if (!doctor) {
            this.setFieldError('reg_doctor', 'Doctor required.');
            document.getElementById('reg_doctor')?.focus();
            return false;
          }
          if (!slot) {
            this.setFieldError('reg_slot', 'Slot required.');
            document.getElementById('reg_slot')?.focus();
            return false;
          }
        }
        if (visitType === 'IPD' && !document.getElementById('reg_bed')?.value) {
          this.setFieldError('reg_bed', 'Bed required.');
          document.getElementById('reg_bed')?.focus();
          return false;
        }
        if (this.isGovSchemePanelVisible()) {
          if (!this.validateSchemeAddressFields()) {
            return false;
          }
          if (!this.validateSchemeNewbornFields()) {
            return false;
          }
          if (!document.getElementById('reg_gov_scheme_id')?.value) {
            this.setFieldError('reg_gov_scheme_id', 'Select a scheme.');
            document.getElementById('reg_gov_scheme_id')?.focus();
            return false;
          }
          const cardSearch = String(document.getElementById('reg_gov_card_search')?.value || '').trim();
          if (cardSearch.length < 4) {
            this.setFieldError('reg_gov_card_search', 'Enter at least 4 characters to search the beneficiary.');
            document.getElementById('reg_gov_card_search')?.focus();
            return false;
          }
          if (!document.getElementById('reg_scheme_lookup_token')?.value) {
            this.setFieldError('reg_gov_card_search', 'Search and load the beneficiary before continuing.');
            document.getElementById('reg_gov_card_search')?.focus();
            return false;
          }
          if (!document.getElementById('reg_scheme_auth_token')?.value) {
            this.setFieldError('reg_gov_kyc_group', 'Complete beneficiary authentication (Confirm authentication).');
            document.getElementById('reg_gov_confirm_auth_btn')?.focus();
            return false;
          }
          const kyc = document.querySelector('#regGovSchemeBlock input[name="reg_gov_kyc"]:checked')?.value;
          if (kyc === 'aadhar_otp') {
            const otp = String(document.getElementById('reg_gov_otp')?.value || '').trim();
            if (!/^\d{6}$/.test(otp)) {
              this.setFieldError('reg_gov_otp', 'Enter the 6-digit OTP used for authentication.');
              document.getElementById('reg_gov_otp')?.focus();
              return false;
            }
          }
        }
      }

      return true;
    }

    validationMessage(fieldId) {
      const messages = {
        reg_name: 'Patient name required.',
        reg_dob: 'Date of birth required.',
        reg_gender: 'Gender required.',
        reg_phone: 'Mobile number required.',
        reg_address: 'Address required.',
        reg_dept: 'Department required.',
        reg_gov_scheme_id: 'Select a government scheme.',
        reg_gov_card_search: 'Enter beneficiary ID and search.',
        reg_gov_otp: 'Enter the 6-digit OTP.',
      };
      return messages[fieldId] || 'Required field missing.';
    }

    clearAllErrors() {
      this.elements.form.querySelectorAll('.has-error').forEach((group) => group.classList.remove('has-error'));
      this.elements.form.querySelectorAll('.form-control, .form-check-input').forEach((field) => field.classList.remove('error'));
      this.elements.form.querySelectorAll('.field-error-message').forEach((node) => node.remove());
      this.elements.form.querySelectorAll('.select2-selection').forEach((node) => node.classList.remove('error'));
    }

    clearStepErrors(stepNumber) {
      const pane = document.getElementById(STEP_CONFIG[stepNumber]?.paneId);
      if (!pane) {
        return;
      }
      pane.querySelectorAll('.has-error').forEach((group) => group.classList.remove('has-error'));
      pane.querySelectorAll('.form-control, .form-check-input').forEach((field) => field.classList.remove('error'));
      pane.querySelectorAll('.field-error-message').forEach((node) => node.remove());
      pane.querySelectorAll('.select2-selection').forEach((node) => node.classList.remove('error'));
    }

    clearFieldError(fieldId) {
      if (!fieldId) {
        return;
      }
      const field = document.getElementById(fieldId);
      const group = this.resolveErrorGroup(fieldId, field);
      if (!group) {
        return;
      }
      group.classList.remove('has-error');
      group.querySelectorAll('.field-error-message').forEach((node) => node.remove());
      if (field) field.classList.remove('error');
      const select2Selection = group.querySelector('.select2-selection');
      if (select2Selection) select2Selection.classList.remove('error');
    }

    resolveErrorGroup(fieldId, field) {
      if (fieldId === 'reg_visit_type') return document.getElementById('reg_visit_type');
      if (fieldId === 'reg_chronic_conditions') return document.getElementById('reg_chronic_conditions');
      if (fieldId === 'reg_gov_kyc_group') return document.getElementById('reg_gov_kyc_group');
      if (fieldId === 'reg_gov_otp') return document.getElementById('reg_gov_otp')?.closest('.form-group') || null;
      return field?.closest('.form-group') || null;
    }

    setFieldError(fieldId, message) {
      const field = document.getElementById(fieldId);
      const group = this.resolveErrorGroup(fieldId, field);
      if (!group) {
        sendmsg('error', message);
        return;
      }
      group.classList.add('has-error');
      if (field) field.classList.add('error');
      const select2Selection = group.querySelector('.select2-selection');
      if (select2Selection) select2Selection.classList.add('error');
      group.querySelectorAll('.field-error-message').forEach((node) => node.remove());
      const errorNode = document.createElement('div');
      errorNode.className = 'field-error-message';
      errorNode.textContent = message;
      group.appendChild(errorNode);
    }

    getVisitType() {
      return document.querySelector('#newPatientModal input[name="visitType"]:checked')?.value || 'OPD';
    }

    selectedText(selectId) {
      const el = document.getElementById(selectId);
      return el && el.selectedIndex >= 0 ? el.options[el.selectedIndex].text : '';
    }

    selectedMeaningfulText(selectId) {
      const text = String(this.selectedText(selectId) || '').trim();
      if (!text) return null;
      const normalized = text.toLowerCase();
      if (normalized === 'select state' || normalized === 'select district' || normalized === 'select nationality') {
        return null;
      }
      return text;
    }

    isGovSchemePaymentLabel(paymentLabel) {
      return GOVERNMENT_PAYMENT_LABELS.has(String(paymentLabel || '').trim());
    }

    isGovSchemeBedContext() {
      const visitType = this.getVisitType();
      const bed = String(document.getElementById('reg_bed')?.value || '').trim();
      if (visitType === 'IPD') {
        return !!bed;
      }
      if (visitType === 'Emergency') {
        return !!bed;
      }
      return false;
    }

    isGovSchemePanelVisible() {
      const pay = document.getElementById('reg_payment')?.value || '';
      return this.isGovSchemePaymentLabel(pay) && this.isGovSchemeBedContext();
    }

    syncSchemeNewbornDetailsVisibility() {
      const checked = !!document.getElementById('reg_scheme_newborn')?.checked;
      const panel = document.getElementById('reg_scheme_newborn_details');
      if (panel) {
        panel.style.display = checked ? '' : 'none';
      }
      if (!checked) {
        const cert = document.getElementById('reg_scheme_born_certificate');
        if (cert) {
          cert.value = '';
        }
      }
    }

    validateSchemeNewbornFields() {
      if (!document.getElementById('reg_scheme_newborn')?.checked) {
        return true;
      }
      const name = String(document.getElementById('reg_name')?.value || '').trim();
      if (!name) {
        return this.failField('reg_name', 'Enter the baby name in patient details (step 1).');
      }
      const dob = String(document.getElementById('reg_dob')?.value || '').trim();
      if (!dob) {
        return this.failField('reg_dob', 'Enter date of birth in patient details (step 1).');
      }
      const gender = String(document.getElementById('reg_gender')?.value || '').trim();
      if (!gender) {
        return this.failField('reg_gender', 'Select gender in patient details (step 1).');
      }
      return true;
    }

    validateSchemeAddressFields() {
      const address = String(document.getElementById('reg_address')?.value || '').trim();
      if (address.length < 3) {
        return this.failField('reg_address', 'Address is required for scheme registration.');
      }
      const pin = String(document.getElementById('reg_pin')?.value || '').trim();
      if (pin.length < 4) {
        return this.failField('reg_pin', 'PIN code is required for scheme registration.');
      }
      if (!this.selectedMeaningfulText('reg_state')) {
        return this.failField('reg_state', 'State is required for scheme registration.');
      }
      if (!this.selectedMeaningfulText('reg_district')) {
        return this.failField('reg_district', 'District is required for scheme registration.');
      }
      const abId = String(document.getElementById('reg_ab')?.value || '').trim();
      if (abId.length < 4) {
        return this.failField('reg_ab', 'Ayushman Bharat ID is required for scheme registration.');
      }
      return true;
    }

    resetGovSchemeState() {
      this.govBeneficiaryLocked = false;
      const lookup = document.getElementById('reg_scheme_lookup_token');
      const auth = document.getElementById('reg_scheme_auth_token');
      if (lookup) lookup.value = '';
      if (auth) auth.value = '';
      const res = document.getElementById('reg_gov_result');
      if (res) {
        res.style.display = 'none';
        res.innerHTML = '';
      }
      const kycGroup = document.getElementById('reg_gov_kyc_group');
      if (kycGroup) kycGroup.style.display = 'none';
      this.govOtpInputRevealed = false;
      this.syncGovOtpRowVisibility();
      const otp = document.getElementById('reg_gov_otp');
      if (otp) otp.value = '';
      const without = document.querySelector('#regGovSchemeBlock input[name="reg_gov_kyc"][value="without_auth"]');
      if (without) without.checked = true;
      this.applyGovBeneficiaryLockedUi();
      this.syncGovOtpSendButton();
    }

    setGovBeneficiaryLocked(locked) {
      this.govBeneficiaryLocked = !!locked;
      this.applyGovBeneficiaryLockedUi();
    }

    applyGovBeneficiaryLockedUi() {
      const locked = this.govBeneficiaryLocked;
      const search = document.getElementById('reg_gov_card_search');
      const btnSearch = document.getElementById('reg_gov_search_btn');
      const scheme = document.getElementById('reg_gov_scheme_id');
      const radios = document.querySelectorAll('#regGovSchemeBlock input[name="reg_gov_kyc"]');
      const confirmB = document.getElementById('reg_gov_confirm_auth_btn');
      const clearB = document.getElementById('reg_gov_clear_btn');
      if (search) {
        search.readOnly = locked;
      }
      if (btnSearch) btnSearch.disabled = locked;
      if (scheme) scheme.disabled = locked;
      radios.forEach((r) => {
        r.disabled = locked;
      });
      if (confirmB) confirmB.disabled = locked;
      if (clearB) clearB.style.display = locked ? '' : 'none';
      const otpField = document.getElementById('reg_gov_otp');
      if (otpField) otpField.readOnly = locked;
      this.syncGovOtpSendButton();
    }

    syncGovOtpSendButton() {
      const sendOtp = document.getElementById('reg_gov_send_otp_btn');
      if (!sendOtp) {
        return;
      }
      const kyc = document.querySelector('#regGovSchemeBlock input[name="reg_gov_kyc"]:checked')?.value;
      const hasLookup = !!document.getElementById('reg_scheme_lookup_token')?.value;
      const show = kyc === 'aadhar_otp' && hasLookup && this.isGovSchemePanelVisible();
      sendOtp.style.display = show ? '' : 'none';
      sendOtp.disabled = this.govBeneficiaryLocked || !show;
      this.syncGovOtpRowVisibility();
    }

    syncGovOtpRowVisibility() {
      const row = document.getElementById('reg_gov_otp_row');
      if (!row) {
        return;
      }
      const kyc = document.querySelector('#regGovSchemeBlock input[name="reg_gov_kyc"]:checked')?.value;
      row.style.display = kyc === 'aadhar_otp' && this.govOtpInputRevealed ? '' : 'none';
    }

    relocatePaymentControls() {
      const opd = document.getElementById('regPaymentMountOpd');
      const ipd = document.getElementById('regPaymentMountIpd');
      if (!opd || !ipd) {
        return;
      }
      const type = this.getVisitType();
      const toIpd = type === 'IPD' || type === 'Emergency';
      const target = toIpd ? ipd : opd;
      const source = toIpd ? opd : ipd;
      while (source.firstChild) {
        target.appendChild(source.firstChild);
      }
    }

    getRegistrationFooterFocusablesInTabOrder() {
      const out = [];
      const pushIf = (btn) => {
        if (!btn || !(btn instanceof HTMLElement)) {
          return;
        }
        if (btn.style.display === 'none') {
          return;
        }
        if (btn.disabled) {
          return;
        }
        if (!this.isFocusableField(btn)) {
          return;
        }
        out.push(btn);
      };
      pushIf(this.elements.nextBtn);
      pushIf(document.getElementById('regModalCancelBtn'));
      pushIf(this.elements.prevBtn);
      return out;
    }

    async sendGovSchemeOtp() {
      if (this.govBeneficiaryLocked) {
        return;
      }
      const schemeId = document.getElementById('reg_gov_scheme_id')?.value;
      const search = String(document.getElementById('reg_gov_card_search')?.value || '').trim();
      const lookupToken = document.getElementById('reg_scheme_lookup_token')?.value;
      const kyc = document.querySelector('#regGovSchemeBlock input[name="reg_gov_kyc"]:checked')?.value;
      if (kyc !== 'aadhar_otp') {
        return;
      }
      if (!lookupToken) {
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
        this.govOtpInputRevealed = true;
        this.syncGovOtpRowVisibility();
        const otpEl = document.getElementById('reg_gov_otp');
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

    preloadGovBeneficiarySearchFromStep1() {
      if (this.govBeneficiaryLocked) {
        return;
      }
      const searchEl = document.getElementById('reg_gov_card_search');
      if (!searchEl || !this.isGovSchemePanelVisible()) {
        return;
      }
      const ab = String(document.getElementById('reg_ab')?.value || '').trim();
      if (ab) {
        searchEl.value = ab;
        return;
      }
      const raw = String(document.getElementById('reg_aadhaar')?.value || '').replace(/\D/g, '');
      if (raw.length >= 4) {
        searchEl.value = raw;
      }
    }

    updateGovPaymentAvailability() {
      const paymentSelect = document.getElementById('reg_payment');
      if (!paymentSelect) {
        return;
      }
      const allowGov = this.isGovSchemeBedContext();
      const previous = paymentSelect.value;
      Array.from(paymentSelect.options).forEach((opt) => {
        const label = String(opt.text || '').trim();
        if (!GOVERNMENT_PAYMENT_LABELS.has(label)) {
          opt.disabled = false;
          return;
        }
        opt.disabled = !allowGov;
      });
      if (!allowGov && GOVERNMENT_PAYMENT_LABELS.has(String(previous || '').trim())) {
        paymentSelect.value = 'Cash';
        this.resetGovSchemeState();
      }
      this.toggleGovSchemePanel();
    }

    toggleGovSchemePanel() {
      const block = document.getElementById('regGovSchemeBlock');
      if (!block) {
        return;
      }
      const show = this.isGovSchemePanelVisible();
      block.style.display = show ? 'block' : 'none';
      if (show) {
        this.preloadGovBeneficiarySearchFromStep1();
      } else if (!this.isGovSchemePaymentLabel(document.getElementById('reg_payment')?.value || '')) {
        this.resetGovSchemeState();
      }
      this.syncGovOtpSendButton();
    }

    handleGovKycChange() {
      if (this.govBeneficiaryLocked) {
        return;
      }
      this.clearFieldError('reg_gov_kyc_group');
      const auth = document.getElementById('reg_scheme_auth_token');
      if (auth) auth.value = '';
      this.govOtpInputRevealed = false;
      const otpEl = document.getElementById('reg_gov_otp');
      if (otpEl) {
        otpEl.value = '';
      }
      this.syncGovOtpRowVisibility();
      this.syncGovOtpSendButton();
    }

    escapeHtml(str) {
      return String(str ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
    }

    renderGovBeneficiarySummary(b) {
      return `<div><b>Name:</b> ${this.escapeHtml(b.name)}</div>
        <div><b>Card / ID:</b> ${this.escapeHtml(b.card_id)}</div>
        <div><b>Scheme:</b> ${this.escapeHtml(b.care_plan)}</div>`;
    }

    async runGovBeneficiarySearch() {
      if (this.govBeneficiaryLocked) {
        return;
      }
      this.clearFieldError('reg_gov_card_search');
      this.clearFieldError('reg_gov_scheme_id');
      const authEl = document.getElementById('reg_scheme_auth_token');
      if (authEl) authEl.value = '';
      const schemeId = document.getElementById('reg_gov_scheme_id')?.value;
      const search = String(document.getElementById('reg_gov_card_search')?.value || '').trim();
      if (!schemeId) {
        this.setFieldError('reg_gov_scheme_id', 'Select a scheme first.');
        return;
      }
      if (search.length < 4) {
        this.setFieldError('reg_gov_card_search', 'Enter at least 4 characters.');
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
        const lt = document.getElementById('reg_scheme_lookup_token');
        if (lt) lt.value = data.lookup_token || '';
        this.govOtpInputRevealed = false;
        const res = document.getElementById('reg_gov_result');
        const kycGroup = document.getElementById('reg_gov_kyc_group');
        if (res) {
          res.style.display = 'block';
          res.innerHTML = this.renderGovBeneficiarySummary(data.beneficiary || {});
        }
        if (kycGroup) kycGroup.style.display = 'block';
        this.handleGovKycChange();
        this.syncGovOtpSendButton();
        sendmsg('success', 'Beneficiary loaded. Complete authentication below.');
      } catch (error) {
        const fromBody =
          error.responseData && typeof window.pmExtractErrorMessage === 'function'
            ? window.pmExtractErrorMessage(error.responseData)
            : '';
        sendmsg('error', fromBody || error.message || 'Search failed.');
      }
    }

    async runGovConfirmAuth() {
      if (this.govBeneficiaryLocked) {
        return;
      }
      this.clearFieldError('reg_gov_kyc_group');
      this.clearFieldError('reg_gov_otp');
      const schemeId = document.getElementById('reg_gov_scheme_id')?.value;
      const search = String(document.getElementById('reg_gov_card_search')?.value || '').trim();
      const lookupToken = document.getElementById('reg_scheme_lookup_token')?.value;
      const kyc = document.querySelector('#regGovSchemeBlock input[name="reg_gov_kyc"]:checked')?.value || 'without_auth';
      if (!lookupToken) {
        this.setFieldError('reg_gov_card_search', 'Search the beneficiary first.');
        return;
      }
      let otp = '';
      if (kyc === 'aadhar_otp') {
        if (!this.govOtpInputRevealed) {
          sendmsg('error', 'Send OTP first, then enter the 6-digit code.');
          return;
        }
        otp = String(document.getElementById('reg_gov_otp')?.value || '').trim();
        if (!/^\d{6}$/.test(otp)) {
          this.setFieldError('reg_gov_otp', 'Enter the 6-digit OTP.');
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
        const authEl = document.getElementById('reg_scheme_auth_token');
        if (authEl) authEl.value = data.auth_token || '';
        this.setGovBeneficiaryLocked(true);
        sendmsg('success', 'Beneficiary authentication recorded. You can continue to the next step.');
      } catch (error) {
        const fromBody =
          error.responseData && typeof window.pmExtractErrorMessage === 'function'
            ? window.pmExtractErrorMessage(error.responseData)
            : '';
        sendmsg('error', fromBody || error.message || 'Authentication failed.');
      }
    }

    shouldPersistSchemePayload(payload) {
      const vt = String(payload.visit_type || '');
      const bed = String(payload.bed_id || '').trim();
      const admit = vt === 'IPD' || (vt === 'Emergency' && bed);
      return admit && this.isGovSchemePaymentLabel(payload.payment_mode);
    }

    stripStaleRegistrationTabIndex() {
      if (!this.elements.modal) {
        return;
      }
      this.elements.modal.querySelectorAll('[tabindex]').forEach((node) => {
        if (!(node instanceof HTMLElement)) {
          return;
        }
        if (node.matches('select.select2-hidden-accessible')) {
          return;
        }
        /* Select2 moves focus to .select2-selection; stripping tabindex makes it untabbable so Tab skips e.g. Gender/Blood. */
        if (node.matches('.select2-selection, .select2-search__field')) {
          return;
        }
        if (node.matches('[data-reg-keeps-tabindex]')) {
          return;
        }
        node.removeAttribute('tabindex');
      });
    }

    isFocusableField(el) {
      if (!el || !(el instanceof HTMLElement) || el.disabled) {
        return false;
      }
      if (el.getAttribute('aria-hidden') === 'true') {
        return false;
      }
      if (el.type === 'hidden') {
        return false;
      }
      if (typeof el.checkVisibility === 'function') {
        return el.checkVisibility({ checkOpacity: true, checkVisibilityCSS: true });
      }
      return !!(el.offsetParent || el.getClientRects().length);
    }

    /** Select2 / Flatpickr often render under <body>; don't fight their Tab while focus is there. */
    isRegistrationDetachedOverlayFocus() {
      const active = document.activeElement;
      if (!active || !(active instanceof HTMLElement)) {
        return false;
      }
      return !!(active.closest('.select2-dropdown') || active.closest('.flatpickr-calendar'));
    }

    /** All tabbable controls inside the registration modal (respects inert hidden steps). */
    getModalFocusables() {
      const modal = this.elements.modal;
      if (!modal || modal.classList.contains('hidden')) {
        return [];
      }
      const list = [];
      const seen = new Set();
      const candidates = modal.querySelectorAll(
        'input:not([type="hidden"]), select, textarea, button, a[href], .select2-selection, #regSummary',
      );
      for (const el of candidates) {
        if (!(el instanceof HTMLElement)) {
          continue;
        }
        if (el.closest('[inert]')) {
          continue;
        }
        if (el.id === 'regSummary') {
          if (el.getAttribute('tabindex') === '0' && this.isFocusableField(el) && !seen.has(el)) {
            seen.add(el);
            list.push(el);
          }
          continue;
        }
        if (el.tagName === 'SELECT' && el.classList.contains('select2-hidden-accessible')) {
          const selection = el.nextElementSibling?.querySelector?.('.select2-selection');
          if (selection && this.isFocusableField(selection) && !seen.has(selection)) {
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
        if (!this.isFocusableField(el)) {
          continue;
        }
        if (seen.has(el)) {
          continue;
        }
        seen.add(el);
        list.push(el);
      }
      return list;
    }

    handleModalFocusTrap(event) {
      if (event.key !== 'Tab' || !this.elements.modal || this.elements.modal.classList.contains('hidden')) {
        return;
      }
      if (this.isRegistrationDetachedOverlayFocus()) {
        return;
      }

      const active = document.activeElement;
      if (active instanceof HTMLElement && this.currentStep < STEP_ORDER.length) {
        const paneList = this.getRegistrationPaneFocusables();
        const footerList = this.getRegistrationFooterFocusablesInTabOrder();

        if (!event.shiftKey && paneList.length) {
          const lastPane = paneList[paneList.length - 1];
          if (active === lastPane && this.elements.nextBtn && this.elements.nextBtn.style.display !== 'none') {
            event.preventDefault();
            event.stopPropagation();
            /* allow scroll: footer must stay in view; faint global :focus-visible may not apply to programmatic focus */
            this.elements.nextBtn.focus({ preventScroll: false });
            return;
          }
        }

        if (footerList.length) {
          const fIdx = footerList.indexOf(active);
          if (fIdx !== -1) {
            event.preventDefault();
            event.stopPropagation();
            const nextIdx = event.shiftKey
              ? (fIdx - 1 + footerList.length) % footerList.length
              : (fIdx + 1) % footerList.length;
            footerList[nextIdx]?.focus({ preventScroll: false });
            return;
          }
        }
      }

      const focusables = this.getModalFocusables();
      if (!focusables.length) {
        return;
      }

      const idx = active instanceof HTMLElement ? focusables.indexOf(active) : -1;

      if (event.shiftKey) {
        if (idx <= 0) {
          event.preventDefault();
          event.stopPropagation();
          focusables[focusables.length - 1].focus({ preventScroll: true });
        }
      } else if (idx === focusables.length - 1 || idx === -1) {
        event.preventDefault();
        event.stopPropagation();
        focusables[0].focus({ preventScroll: true });
      }
    }

    /** If focus lands outside the open registration modal (e.g. page header), pull it back inside. */
    handleDocumentFocusContain(event) {
      const modal = this.elements.modal;
      if (!modal || modal.classList.contains('hidden')) {
        return;
      }
      const t = event.target;
      if (!(t instanceof HTMLElement)) {
        return;
      }
      if (modal.contains(t)) {
        return;
      }
      if (t.closest('.select2-dropdown')) {
        return;
      }
      if (t.closest('.flatpickr-calendar')) {
        return;
      }
      const otherOverlay = t.closest('.modal-overlay');
      if (otherOverlay && otherOverlay !== modal) {
        return;
      }

      const list = this.getModalFocusables();
      if (!list.length) {
        return;
      }
      window.requestAnimationFrame(() => {
        if (modal.classList.contains('hidden') || modal.contains(document.activeElement)) {
          return;
        }
        const preferred = list.find((el) => el.id === 'reg_name') || list[0];
        preferred.focus({ preventScroll: true });
      });
    }

    /** Focusables inside the active step pane only (excludes modal footer). Tree order ≈ Tab order. */
    getRegistrationPaneFocusables() {
      const config = STEP_CONFIG[this.currentStep];
      const pane = config ? document.getElementById(config.paneId) : null;
      if (!pane) {
        return [];
      }
      const list = [];
      const seen = new Set();
      const candidates = pane.querySelectorAll(
        'input:not([type="hidden"]), select, textarea, button, a[href], .select2-selection, #regSummary',
      );
      for (const el of candidates) {
        if (!(el instanceof HTMLElement)) {
          continue;
        }
        if (el.id === 'regSummary') {
          if (el.getAttribute('tabindex') === '0' && this.isFocusableField(el) && !seen.has(el)) {
            seen.add(el);
            list.push(el);
          }
          continue;
        }
        if (el.tagName === 'SELECT' && el.classList.contains('select2-hidden-accessible')) {
          const selection = el.nextElementSibling?.querySelector?.('.select2-selection');
          if (selection && this.isFocusableField(selection) && !seen.has(selection)) {
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
        if (!this.isFocusableField(el)) {
          continue;
        }
        if (seen.has(el)) {
          continue;
        }
        seen.add(el);
        list.push(el);
      }
      return list;
    }

    handleRegistrationFormKeydown(event) {
      if (event.key !== 'Enter' || event.isComposing) {
        return;
      }
      if (!this.elements.modal || this.elements.modal.classList.contains('hidden')) {
        return;
      }
      if (document.querySelector('#newPatientModal .select2-container--open')) {
        return;
      }
      if (document.querySelector('.flatpickr-calendar.open')) {
        return;
      }

      const active = document.activeElement;
      if (!active || !(active instanceof HTMLElement)) {
        return;
      }

      if (this.currentStep === STEP_ORDER.length) {
        const summary = document.getElementById('regSummary');
        if (
          summary
          && active !== this.elements.submitBtn
          && (active === summary || summary.contains(active))
        ) {
          event.preventDefault();
          event.stopPropagation();
          this.elements.submitBtn?.focus?.({ preventScroll: false });
        }
        return;
      }

      if (active.closest('.modal-footer')) {
        return;
      }

      const aid = active.id;
      if (aid === 'allergyInput' || aid === 'allergyAddBtn') {
        return;
      }

      const paneFocusables = this.getRegistrationPaneFocusables();
      if (!paneFocusables.length) {
        return;
      }
      const last = paneFocusables[paneFocusables.length - 1];
      if (active !== last) {
        return;
      }

      event.preventDefault();
      event.stopPropagation();
      void this.moveStep(1);
    }

    focusFirstInActivePane() {
      if (this.currentStep === STEP_ORDER.length) {
        const summary = document.getElementById('regSummary');
        if (summary) {
          summary.focus({ preventScroll: false });
          summary.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
        } else {
          this.elements.submitBtn?.focus?.();
        }
        return;
      }
      const config = STEP_CONFIG[this.currentStep];
      const pane = config ? document.getElementById(config.paneId) : null;
      if (!pane) {
        return;
      }
      const candidates = pane.querySelectorAll(
        'input:not([type="hidden"]), select, textarea, button, [href], .select2-selection',
      );
      for (const el of candidates) {
        if (el.tagName === 'SELECT' && el.classList.contains('select2-hidden-accessible')) {
          const selection = el.nextElementSibling?.querySelector?.('.select2-selection');
          if (selection) {
            selection.focus();
            return;
          }
          continue;
        }
        if (!this.isFocusableField(el)) {
          continue;
        }
        if (el.classList.contains('select2-selection')) {
          el.focus();
          return;
        }
        el.focus();
        return;
      }
    }

    select2Selectors() {
      return Array.from(this.elements.form?.querySelectorAll('select[id]') || [])
        .filter((el) => el.getAttribute('data-no-select2') !== '1')
        .map((el) => `#${el.id}`);
    }

    focusSelect2Selection(selectId) {
      const id = String(selectId || '').replace(/^#/, '');
      const select = document.getElementById(id);
      const selection = select?.nextElementSibling?.querySelector?.('.select2-selection');
      selection?.focus?.();
    }

    /**
     * @param {string[]} selectors
     * @param {{ force?: boolean, focusAfter?: string }} [options] force: destroy+rebind (e.g. after options HTML replaced). focusAfter: select id to focus after init.
     */
    initSelect2(selectors = this.select2Selectors(), options = {}) {
      if (!(window.jQuery && jQuery.fn && jQuery.fn.select2)) {
        return;
      }

      const force = !!options.force;
      const focusAfter = options.focusAfter;

      selectors.forEach((selector) => {
        const $field = jQuery(selector);
        if (!$field.length) {
          return;
        }
        const el = $field[0];
        if (el?.getAttribute('data-no-select2') === '1') {
          if ($field.hasClass('select2-hidden-accessible')) {
            $field.select2('destroy');
          }
          return;
        }
        if ($field.hasClass('select2-hidden-accessible')) {
          if (!force) {
            return;
          }
          $field.select2('destroy');
        }
        $field.select2({ width: '100%', dropdownParent: jQuery('#newPatientModal .modal') });
      });

      if (focusAfter) {
        window.requestAnimationFrame(() => this.focusSelect2Selection(focusAfter));
      }
    }

    syncVisibleSelect2() {
      const config = STEP_CONFIG[this.currentStep];
      const activePane = config ? document.getElementById(config.paneId) : null;
      if (!activePane) {
        this.initSelect2(this.select2Selectors(), { force: false });
        return;
      }
      const selectors = Array.from(activePane.querySelectorAll('select[id]'))
        .filter((select) => select.getAttribute('data-no-select2') !== '1')
        .map((select) => `#${select.id}`);
      if (selectors.length) {
        this.initSelect2(selectors, { force: false });
      }
    }

    calcAge() {
      const dob = document.getElementById('reg_dob')?.value;
      const ageField = document.getElementById('reg_age');
      if (!ageField) {
        return;
      }
      if (!dob) {
        ageField.value = '';
        return;
      }
      const diff = Date.now() - new Date(dob).getTime();
      ageField.value = `${Math.floor(diff / (1000 * 60 * 60 * 24 * 365.25))} yrs`;
    }

    addAllergy() {
      const input = this.elements.allergyInput;
      if (!input) {
        return;
      }
      const value = input.value.trim();
      if (!value) {
        return;
      }
      this.elements.allergyChips.insertAdjacentHTML('beforeend', `<span class="badge badge-red" style="cursor:pointer" data-allergy-chip="1">${value} ✕</span>`);
      input.value = '';
      this.clearFieldError('allergyInput');
    }

    removeAllergy(element) {
      element?.remove();
    }

    getAllergiesText() {
      return Array.from(document.querySelectorAll('#allergyChips .badge')).map((el) => el.textContent.replace(' ✕', '').trim()).join(', ');
    }

    getChronicConditions() {
      return Array.from(document.querySelectorAll('#regPane3 input[name="diseases[]"]:checked')).map((el) => el.value);
    }

    async loadRegistrationDistricts(stateId) {
      const districtSelect = document.getElementById('reg_district');
      const districtUrl = document.getElementById('reg_state')?.dataset?.districtUrl || this.routes.loadDistricts;
      if (!districtSelect) {
        return;
      }
      if (!stateId) {
        districtSelect.innerHTML = '<option value="">Select District</option>';
        this.initSelect2(['#reg_district'], { force: true });
        return;
      }
      try {
        const data = await window.pmFetch(`${districtUrl}?state_id=${encodeURIComponent(stateId)}`);
        window.pmRenderOptions?.(districtSelect, data || [], { placeholder: 'Select District' });
        this.initSelect2(['#reg_district'], { force: true, focusAfter: 'reg_district' });
      } catch (error) {
        sendmsg('error', `District Load Failed: ${error.message}`);
      }
    }

    async loadRegistrationDoctors() {
      const deptId = document.getElementById('reg_dept')?.value;
      const doctorSelect = document.getElementById('reg_doctor');
      if (!doctorSelect) {
        return;
      }
      this.clearFieldError('reg_dept');
      const doctors = deptId ? await window.pmFetch(`${this.routes.loadDoctors}?dept_id=${encodeURIComponent(deptId)}`) : [];
      window.pmRenderOptions?.(doctorSelect, doctors || [], { placeholder: 'Select Doctor' });
      this.resetSlotOptions();
      this.initSelect2(['#reg_doctor', '#reg_slot'], { force: true, focusAfter: deptId ? 'reg_doctor' : undefined });
    }

    async loadRegistrationCharge() {
      const feeField = document.getElementById('reg_fee');
      if (!feeField || !this.routes.getOpdCharge) {
        return;
      }

      const visitType = this.getVisitType();
      if (visitType === 'IPD') {
        feeField.value = '0';
        return;
      }

      const deptId = document.getElementById('reg_dept')?.value;
      if (!deptId) {
        return;
      }

      try {
        const data = await window.pmFetch(this.routes.getOpdCharge, {
          method: 'POST',
          body: {
            hr_department_id: deptId,
            doctor_id: document.getElementById('reg_doctor')?.value || null,
            visit_type: visitType,
            tpa_id: null,
          },
        });

        if (typeof data?.charge === 'number' && Number.isFinite(data.charge)) {
          feeField.value = Number(data.charge).toFixed(2);
        }
      } catch (error) {
        // Keep user-entered fee if dynamic charge API fails.
      }
    }

    displayBedDetails() {
      const bedSelect = document.getElementById('reg_bed');
      const bedSummary = document.getElementById('reg-bed-summary');
      if (!bedSelect || !bedSummary) {
        return;
      }

      const selectedOption = bedSelect.options[bedSelect.selectedIndex];
      if (!selectedOption.value) {
        bedSummary.innerHTML = 'Select a bed to see its location, type and standard base charge.';
        return;
      }

      const ward = selectedOption.dataset.ward || '—';
      const room = selectedOption.dataset.room || '—';
      const bedType = selectedOption.dataset.type || '—';
      const charge = selectedOption.dataset.charge || '0.00';

      bedSummary.innerHTML = `
        <div style="background:var(--surface-2);border:1px solid var(--border-light);border-radius:8px;padding:12px;font-size:12px">
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            <div><b>Ward:</b> ${ward}</div>
            <div><b>Room:</b> ${room}</div>
            <div><b>Bed Type:</b> ${bedType}</div>
            <div><b>Base Charge:</b> <span style="color:var(--primary);font-weight:700">₹${charge}</span></div>
          </div>
        </div>
      `;
    }

    async loadRegistrationSlots() {
      const doctorId = document.getElementById('reg_doctor')?.value;
      const appointmentDate = document.getElementById('reg_appointment_date')?.value;
      const slotSelect = document.getElementById('reg_slot');
      if (!slotSelect) {
        return;
      }
      if (!doctorId || !appointmentDate) {
        this.resetSlotOptions();
        this.initSelect2(['#reg_slot'], { force: true });
        return;
      }
      const slots = await window.pmFetch(`${this.routes.loadDoctorSlots}?doctor_id=${encodeURIComponent(doctorId)}&date=${encodeURIComponent(appointmentDate)}`);
      slotSelect.innerHTML = '<option value="">Select Slot</option>' + (slots || []).map((slot) => `<option value="${slot.label}">${slot.label}</option>`).join('');
      if (slots && slots.length) {
        slotSelect.value = slots[0].label;
      }
      this.initSelect2(['#reg_slot'], { force: true, focusAfter: 'reg_slot' });
      if (window.jQuery && slotSelect.value) {
        jQuery(slotSelect).trigger('change.select2');
      }
    }

    updateVisitType(type, options = {}) {
      const syncSlots = options.syncSlots !== false;
      document.getElementById('vtOPD').style.borderColor = type === 'OPD' ? 'var(--primary)' : 'var(--border)';
      document.getElementById('vtIPD').style.borderColor = type === 'IPD' ? 'var(--primary)' : 'var(--border)';
      document.getElementById('vtEM').style.borderColor = type === 'Emergency' ? 'var(--danger)' : 'var(--border)';
      document.getElementById('vtDaycare').style.borderColor = type === 'Daycare' ? 'var(--primary)' : 'var(--border)';
      const ipdFields = document.getElementById('regIpdFields');
      const slotGroup = document.getElementById('reg_slot')?.closest('.form-group');
      const feeGroup = this.elements.feeGroup;
      if (ipdFields) ipdFields.style.display = (type === 'IPD' || type === 'Emergency') ? '' : 'none';
      this.relocatePaymentControls();
      if (slotGroup) slotGroup.style.display = this.shouldHideAppointmentDate(type) ? 'none' : '';
      if (feeGroup) feeGroup.style.display = type === 'IPD' ? 'none' : '';
      this.applyVisitDateVisibility(type);
      /* Same bed list as IPD — Emergency also shows regIpdFields and needs beds loaded. */
      if (type === 'IPD' || type === 'Emergency') {
        window.pmLoadBedOptions?.();
      }
      if (type !== 'IPD' && syncSlots) {
        this.loadRegistrationSlots();
      }
      this.loadRegistrationCharge();
      this.updateGovPaymentAvailability();
    }

    getSelectedBedMeta() {
      const bedSelect = document.getElementById('reg_bed');
      const selectedOption = bedSelect?.options?.[bedSelect.selectedIndex];
      if (!selectedOption || !selectedOption.value) {
        return null;
      }

      return {
        bedLabel: selectedOption.text || '—',
        ward: selectedOption.dataset.ward || '—',
        room: selectedOption.dataset.room || '—',
        bedType: selectedOption.dataset.type || '—',
        baseCharge: selectedOption.dataset.charge || '0.00',
      };
    }

    formatCurrency(value) {
      const amount = Number(value || 0);
      return `₹${Number.isFinite(amount) ? amount.toFixed(2) : '0.00'}`;
    }

    shouldHideAppointmentDate(type) {
      if (type === 'IPD') {
        return true;
      }

      if (type === 'Emergency') {
        return !!document.getElementById('reg_bed')?.value;
      }

      return false;
    }

    applyVisitDateVisibility(type) {
      const hide = this.shouldHideAppointmentDate(type);
      const dateGroup = document.getElementById('reg_appointment_date')?.closest('.form-group');
      const slotGroup = document.getElementById('reg_slot')?.closest('.form-group');
      if (dateGroup) dateGroup.style.display = hide ? 'none' : '';
      if (slotGroup) slotGroup.style.display = hide ? 'none' : '';
    }

    async loadMrnPreview() {
      if (!this.routes.mrnPreview || !this.elements.genMrn) {
        return;
      }

      try {
        const data = await window.pmFetch(this.routes.mrnPreview);
        this.elements.genMrn.textContent = data?.mrn || '—';
      } catch (error) {
        this.elements.genMrn.textContent = '—';
      }
    }

    prepareStepFivePreview() {
      this.buildSummary();
      this.loadMrnPreview();
    }

    buildSummary() {
      const visitType = this.getVisitType();
      const feeValue = document.getElementById('reg_fee')?.value || 0;
      const advanceDeposit = document.getElementById('reg_advance_deposit')?.value || 0;
      const bedMeta = this.getSelectedBedMeta();
      const summary = {
        name: document.getElementById('reg_name')?.value || 'Not entered',
        dob: document.getElementById('reg_dob')?.value || '—',
        age: document.getElementById('reg_age')?.value || '—',
        gender: document.getElementById('reg_gender')?.value || '—',
        phone: document.getElementById('reg_phone')?.value || '—',
        department: this.selectedText('reg_dept') || '—',
        doctor: this.selectedText('reg_doctor') || '—',
        slot: document.getElementById('reg_slot')?.value || '—',
        visitType,
        fee: feeValue,
        advanceDeposit,
        paymentMode: document.getElementById('reg_payment')?.value || '—',
        admissionReason: document.getElementById('reg_admission_reason')?.value || '—',
      };
      this.elements.genFee.textContent = this.formatCurrency(visitType === 'IPD' ? advanceDeposit : feeValue);
      this.elements.summaryLeft.innerHTML = `
        <div class="fs-13 fw-700 mb-8">Patient Details</div>
        <div class="fs-12 mb-4"><b>Name:</b> ${summary.name}</div>
        <div class="fs-12 mb-4"><b>DOB:</b> ${summary.dob} (${summary.age})</div>
        <div class="fs-12 mb-4"><b>Gender:</b> ${summary.gender}</div>
        <div class="fs-12 mb-4"><b>Phone:</b> ${summary.phone}</div>`;
      const chargeLines = [];
      if (visitType !== 'IPD') {
        chargeLines.push(`<div class="fs-12 mb-4"><b>Registration Fee:</b> ${this.formatCurrency(summary.fee)}</div>`);
      }
      if (visitType === 'IPD' || visitType === 'Emergency') {
        chargeLines.push(`<div class="fs-12 mb-4"><b>Advance Deposit:</b> ${this.formatCurrency(summary.advanceDeposit)}</div>`);
      }
      const bedLines = bedMeta
        ? `
        <div class="fs-12 mb-4"><b>Bed:</b> ${bedMeta.bedLabel}</div>
        <div class="fs-12 mb-4"><b>Ward / Room:</b> ${bedMeta.ward} / ${bedMeta.room}</div>
        <div class="fs-12 mb-4"><b>Bed Type:</b> ${bedMeta.bedType}</div>
        <div class="fs-12 mb-4"><b>Bed Base Charge:</b> ${this.formatCurrency(bedMeta.baseCharge)}</div>
        <div class="fs-12 mb-4"><b>Admission Reason:</b> ${summary.admissionReason}</div>`
        : '';
      const schemeSummaryLines = [];
      if (this.isGovSchemePanelVisible()) {
        schemeSummaryLines.push(
          `<div class="fs-12 mb-4"><b>Scheme:</b> ${this.escapeHtml(this.selectedText('reg_gov_scheme_id'))}</div>`,
        );
        schemeSummaryLines.push(
          `<div class="fs-12 mb-4"><b>Beneficiary search ID:</b> ${this.escapeHtml(String(document.getElementById('reg_gov_card_search')?.value || '').trim() || '—')}</div>`,
        );
        schemeSummaryLines.push(
          `<div class="fs-12 mb-4"><b>Authentication:</b> ${this.escapeHtml(document.querySelector('#regGovSchemeBlock input[name="reg_gov_kyc"]:checked')?.value || '—')}</div>`,
        );
      }
      if (document.getElementById('reg_scheme_newborn')?.checked) {
        schemeSummaryLines.push('<div class="fs-12 mb-4"><b>Newborn (scheme flag):</b> Yes</div>');
      }
      this.elements.summaryRight.innerHTML = `
        <div class="fs-13 fw-700 mb-8">Visit Details</div>
        <div class="fs-12 mb-4"><b>Visit Type:</b> ${summary.visitType}</div>
        <div class="fs-12 mb-4"><b>Department:</b> ${summary.department}</div>
        <div class="fs-12 mb-4"><b>Doctor:</b> ${summary.doctor}</div>
        <div class="fs-12 mb-4"><b>Slot:</b> ${summary.slot}</div>
        <div class="fs-12 mb-4"><b>Payment Mode:</b> ${summary.paymentMode}</div>
        ${schemeSummaryLines.join('')}
        ${chargeLines.join('')}
        ${bedLines}
        <div class="fs-12 mb-4"><b>Registration Date:</b> ${new Date().toLocaleDateString('en-IN')}</div>`;
    }

    stepForField(fieldId) {
      return FIELD_TO_STEP[fieldId] || 1;
    }

    goToField(fieldId) {
      if (!fieldId) {
        return;
      }
      const step = this.stepForField(fieldId);
      if (step !== this.currentStep) {
        this.setStep(step, { focusFirst: false });
      }
      window.requestAnimationFrame(() => {
        const target = document.getElementById(fieldId);
        if (target?.classList?.contains('select2-hidden-accessible')) {
          target.nextElementSibling?.querySelector?.('.select2-selection')?.focus?.();
        } else {
          target?.focus?.();
        }
      });
    }

    failField(fieldId, message) {
      this.lastErrorFieldId = fieldId;
      this.setFieldError(fieldId, message);
      this.goToField(fieldId);
      return false;
    }

    fieldIdForServerCode(code) {
      if (SERVER_FIELD_MAP[code]) return SERVER_FIELD_MAP[code];
      const normalized = String(code || '').replace(/\.\d+$/, '');
      return SERVER_FIELD_MAP[normalized] || null;
    }

    applyServerErrors(errors) {
      if (!Array.isArray(errors) || !errors.length) {
        return false;
      }
      this.clearAllErrors();
      let firstFieldId = null;
      errors.forEach((error) => {
        const fieldId = this.fieldIdForServerCode(error.code);
        if (!fieldId) {
          return;
        }
        if (!firstFieldId) {
          firstFieldId = fieldId;
        }
        this.setFieldError(fieldId, error.message || 'Invalid value');
      });
      if (firstFieldId) {
        this.goToField(firstFieldId);
        return true;
      }
      return false;
    }

    collectPayload() {
      const visitType = this.getVisitType();
      const payload = {
        title: document.getElementById('reg_title')?.value || null,
        name: document.getElementById('reg_name')?.value || null,
        date_of_birth: document.getElementById('reg_dob')?.value || null,
        age_years: parseInt((document.getElementById('reg_age')?.value || '0').replace(/\D/g, ''), 10) || 0,
        gender: document.getElementById('reg_gender')?.value || null,
        blood_group: document.getElementById('reg_blood')?.value || null,
        aadhar_no: document.getElementById('reg_aadhaar')?.value || null,
        ayushman_bharat_id: document.getElementById('reg_ab')?.value || null,
        marital_status: document.getElementById('reg_marital_status')?.value || null,
        religion_id: document.getElementById('reg_religion')?.value || null,
        occupation: document.getElementById('reg_occupation')?.value || null,
        patient_category_id: document.getElementById('reg_category')?.value || null,
        phone: document.getElementById('reg_phone')?.value || null,
        alternate_phone: document.getElementById('reg_alt_phone')?.value || null,
        email: document.getElementById('reg_email')?.value || null,
        address: document.getElementById('reg_address')?.value || null,
        pin_code: document.getElementById('reg_pin')?.value || null,
        district: this.selectedMeaningfulText('reg_district'),
        state: this.selectedMeaningfulText('reg_state'),
        nationality: this.selectedMeaningfulText('reg_nationality'),
        emergency_contact_name: document.getElementById('reg_emergency_name')?.value || null,
        emergency_contact_relation: document.getElementById('reg_emergency_relation')?.value || null,
        emergency_contact_phone: document.getElementById('reg_emergency_phone')?.value || null,
        known_allergies: this.getAllergiesText() || null,
        chronic_conditions: this.getChronicConditions(),
        past_surgical_history: document.getElementById('reg_past_surgery')?.value || null,
        current_medications: document.getElementById('reg_current_medications')?.value || null,
        family_history: document.getElementById('reg_family_history')?.value || null,
        smoking_status: document.getElementById('reg_smoking')?.value || null,
        alcohol_status: document.getElementById('reg_alcohol')?.value || null,
        vaccination_status: document.getElementById('reg_vaccination')?.value || null,
        visit_type: visitType,
        hr_department_id: document.getElementById('reg_dept')?.value || null,
        doctor_id: document.getElementById('reg_doctor')?.value || null,
        appointment_date: document.getElementById('reg_appointment_date')?.value || null,
        appointment_time: document.getElementById('reg_slot')?.value ? window.pmConvertSlotTo24Hour(document.getElementById('reg_slot').value) : null,
        slot: document.getElementById('reg_slot')?.value || null,
        chief_complaint: document.getElementById('reg_complaint')?.value || null,
        payment_mode: document.getElementById('reg_payment')?.value || null,
        applied_charge: document.getElementById('reg_fee')?.value || 0,
        advance_deposit: document.getElementById('reg_advance_deposit')?.value || 0,
        bed_id: document.getElementById('reg_bed')?.value || null,
        admission_reason: document.getElementById('reg_admission_reason')?.value || null,
        casualty: visitType === 'Emergency' ? 'Yes' : 'No',
      };
      if (this.shouldPersistSchemePayload(payload)) {
        const kyc = document.querySelector('#regGovSchemeBlock input[name="reg_gov_kyc"]:checked')?.value || 'without_auth';
        payload.scheme_type_id = document.getElementById('reg_gov_scheme_id')?.value || null;
        payload.scheme_beneficiary_card_id = String(document.getElementById('reg_gov_card_search')?.value || '').trim() || null;
        payload.scheme_kyc_type = kyc;
        payload.scheme_lookup_token = document.getElementById('reg_scheme_lookup_token')?.value || null;
        payload.scheme_auth_token = document.getElementById('reg_scheme_auth_token')?.value || null;
        payload.scheme_aadhar_otp = kyc === 'aadhar_otp' ? String(document.getElementById('reg_gov_otp')?.value || '').trim() || null : null;
        payload.scheme_is_newborn = !!document.getElementById('reg_scheme_newborn')?.checked;
        if (payload.scheme_is_newborn) {
          payload.scheme_born_baby_dob = document.getElementById('reg_dob')?.value || null;
          payload.scheme_born_baby_name = document.getElementById('reg_name')?.value || null;
          payload.scheme_born_baby_gender = document.getElementById('reg_gender')?.value || null;
        }
      }
      return payload;
    }

    buildRegistrationRequestBody(payload) {
      const certFile = document.getElementById('reg_scheme_born_certificate')?.files?.[0];
      if (!certFile) {
        return payload;
      }
      const formData = new FormData();
      Object.entries(payload).forEach(([key, value]) => {
        if (value === null || value === undefined) {
          return;
        }
        if (Array.isArray(value)) {
          value.forEach((item) => formData.append(`${key}[]`, item));
          return;
        }
        formData.append(key, value);
      });
      formData.append('scheme_born_baby_birth_certificate', certFile);
      return formData;
    }

    async submitRegistration() {
      this.lastErrorFieldId = null;
      const validations = [];
      for (const stepNumber of STEP_ORDER.slice(0, 4)) {
        validations.push(await this.validateStep(stepNumber));
      }
      if (validations.includes(false)) {
        const step = this.lastErrorFieldId
          ? this.stepForField(this.lastErrorFieldId)
          : STEP_ORDER[validations.findIndex((value) => value === false)];
        this.setStep(step, { focusFirst: false });
        if (this.lastErrorFieldId) {
          this.goToField(this.lastErrorFieldId);
        }
        return;
      }

      const payload = this.collectPayload();
      if (this.shouldPersistSchemePayload(payload)) {
        if (!this.validateSchemeAddressFields()) {
          return;
        }
        if (!this.validateSchemeNewbornFields()) {
          return;
        }
      }
      const requestBody = this.buildRegistrationRequestBody(payload);
      this.elements.submitBtn.disabled = true;
      try {
        const data = await window.pmFetch(this.routes.register, { method: 'POST', body: requestBody });
        const successMessage = `${data?.patient_name || payload.name} registered successfully.`;
        this.elements.genMrn.textContent = data?.mrn || '—';
        this.elements.genToken.textContent = data?.token || data?.admission_no || '—';
        this.buildSummary();
        closeModal('newPatientModal');
        this.resetFormState({ preserveValues: false });
        window.setTimeout(() => {
          sendmsg('success',successMessage);
        }, 120);

        try {
          await window.pmRefreshPatientDashboard?.();
        } catch (refreshError) {
          sendmsg('warning', refreshError.message || 'Something went wrong while refreshing patient dashboard. Please refresh manually to see the new registration.');
        }
      } catch (error) {
        const handled = this.applyServerErrors(error.responseData?.errors || []);
        if (!handled) {
          sendmsg('error', error.message);

        }
      } finally {
        this.elements.submitBtn.disabled = false;
      }
    }
  }

  const controller = new PatientRegistrationFormController();
  window.PatientRegistrationForm = controller;
  window.regStep = (direction) => controller.moveStep(direction);
  window.submitRegistration = () => controller.submitRegistration();
  window.calcAge = () => controller.calcAge();
  window.addAllergy = () => controller.addAllergy();
  window.removeAllergy = (element) => controller.removeAllergy(element);
  window.loadRegistrationDistricts = (stateId) => controller.loadRegistrationDistricts(stateId);
  window.toggleVisitType = (type) => controller.updateVisitType(type);
})();