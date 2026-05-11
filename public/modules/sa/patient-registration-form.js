(function () {
  const STEP_ORDER = [1, 2, 3, 4, 5];
  const STEP_CONFIG = {
    1: { paneId: 'regPane1', stepId: 'step1', fields: ['reg_name', 'reg_dob', 'reg_gender'] },
    2: { paneId: 'regPane2', stepId: 'step2', fields: ['reg_phone', 'reg_address'] },
    3: { paneId: 'regPane3', stepId: 'step3', fields: [] },
    4: { paneId: 'regPane4', stepId: 'step4', fields: ['reg_dept'] },
    5: { paneId: 'regPane5', stepId: 'step5', fields: [] },
  };

  const FIELD_TO_STEP = {
    reg_name: 1,
    reg_dob: 1,
    reg_gender: 1,
    reg_category: 1,
    reg_phone: 2,
    reg_address: 2,
    reg_state: 2,
    reg_district: 2,
    reg_nationality: 2,
    reg_dept: 4,
    reg_doctor: 4,
    reg_slot: 4,
    reg_bed: 4,
    reg_advance_deposit: 4,
    reg_complaint: 4,
    reg_visit_type: 4,
    reg_chronic_conditions: 3,
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
  };

  class PatientRegistrationFormController {
    constructor() {
      this.currentStep = 1;
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
      }
      this.ensureAppointmentDate();
      this.resetSummary();
      this.resetWizardState();
      this.updateVisitType(this.getVisitType(), { syncSlots: false });
      this.syncVisibleSelect2();
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

      const focusables = this.getModalFocusables();
      if (!focusables.length) {
        return;
      }

      const active = document.activeElement;
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
      return Array.from(this.elements.form?.querySelectorAll('select[id]') || []).map((el) => `#${el.id}`);
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
      const selectors = Array.from(activePane.querySelectorAll('select[id]')).map((select) => `#${select.id}`);
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
      this.elements.summaryRight.innerHTML = `
        <div class="fs-13 fw-700 mb-8">Visit Details</div>
        <div class="fs-12 mb-4"><b>Visit Type:</b> ${summary.visitType}</div>
        <div class="fs-12 mb-4"><b>Department:</b> ${summary.department}</div>
        <div class="fs-12 mb-4"><b>Doctor:</b> ${summary.doctor}</div>
        <div class="fs-12 mb-4"><b>Slot:</b> ${summary.slot}</div>
        <div class="fs-12 mb-4"><b>Payment Mode:</b> ${summary.paymentMode}</div>
        ${chargeLines.join('')}
        ${bedLines}
        <div class="fs-12 mb-4"><b>Registration Date:</b> ${new Date().toLocaleDateString('en-IN')}</div>`;
    }

    stepForField(fieldId) {
      return FIELD_TO_STEP[fieldId] || 1;
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
        this.setStep(this.stepForField(firstFieldId), { focusFirst: false });
        const target = document.getElementById(firstFieldId);
        if (target?.classList?.contains('select2-hidden-accessible')) {
          target.nextElementSibling?.querySelector?.('.select2-selection')?.focus?.();
        } else {
          target?.focus?.();
        }
        return true;
      }
      return false;
    }

    collectPayload() {
      const visitType = this.getVisitType();
      return {
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
    }

    async submitRegistration() {
      const validations = await Promise.all(STEP_ORDER.slice(0, 4).map((stepNumber) => this.validateStep(stepNumber)));
      if (validations.includes(false)) {
        this.setStep(STEP_ORDER[validations.findIndex((value) => value === false)], { focusFirst: false });
        return;
      }

      const payload = this.collectPayload();
      this.elements.submitBtn.disabled = true;
      try {
        const data = await window.pmFetch(this.routes.register, { method: 'POST', body: payload });
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