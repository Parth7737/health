(function () {
  const STEP_ORDER = [1, 2, 3, 4];
  const STEP_CONFIG = {
    1: { paneId: 'editPane1', stepId: 'editStep1', fields: ['edit_name', 'edit_gender'] },
    2: { paneId: 'editPane2', stepId: 'editStep2', fields: ['edit_phone'] },
    3: { paneId: 'editPane3', stepId: 'editStep3', fields: [] },
    4: { paneId: 'editPane4', stepId: 'editStep4', fields: [] },
  };

  class PatientEditProfileFormController {
    constructor() {
      this.currentStep = 1;
      this.routes = {};
      this.bound = false;
      this.modalObserver = null;
      this.elements = {};
      this.flatpickrRetryCount = 0;
      this.dobInstance = null;
      this._shortcutHandler = (event) => this.handleShortcuts(event);
      this._focusTrapHandler = (event) => this.handleModalFocusTrap(event);
      this._documentFocusContainHandler = (event) => this.handleDocumentFocusContain(event);
      this._documentFocusContainBound = false;
    }

    init({ routes } = {}) {
      this.routes = routes || this.routes;
      this.cacheElements();
      if (!this.elements.modal || !this.elements.form) {
        return;
      }
      this.initFlatpickr();
      this.bindEvents();
      this.observeModalVisibility();
      this.resetWizardState();
    }

    cacheElements() {
      this.elements = {
        modal: document.getElementById('editProfileModal'),
        form: document.getElementById('editProfileForm'),
        prevBtn: document.getElementById('editProfilePrevBtn'),
        nextBtn: document.getElementById('editProfileNextBtn'),
        submitBtn: document.getElementById('editProfileSubmitBtn'),
        cancelBtn: document.getElementById('editProfileCancelBtn'),
        alertEl: document.getElementById('editProfileAlert'),
        summaryLeft: document.getElementById('editSummLeft'),
        summaryRight: document.getElementById('editSummRight'),
        stateSelect: document.getElementById('edit_state'),
        districtSelect: document.getElementById('edit_district'),
      };
    }

    bindEvents() {
      if (this.bound) {
        return;
      }
      this.bound = true;

      this.elements.form.addEventListener('submit', (event) => {
        event.preventDefault();
        this.submitProfile();
      });
      this.elements.prevBtn?.addEventListener('click', () => this.moveStep(-1));
      this.elements.nextBtn?.addEventListener('click', () => this.moveStep(1));
      this.elements.submitBtn?.addEventListener('click', () => this.submitProfile());
      this.elements.stateSelect?.addEventListener('change', (event) => {
        this.loadDistricts(this.getSelectedStateId(event.target));
      });

      if (window.jQuery) {
        jQuery(document).on('select2:select select2:clear', '#edit_state', (event) => {
          this.loadDistricts(this.getSelectedStateId(event?.target));
        });
      }

      document.addEventListener('keydown', this._shortcutHandler, true);
      document.addEventListener('keydown', this._focusTrapHandler, true);
      this.elements.form.addEventListener('keydown', (event) => this.handleFormKeydown(event));

      if (!this._documentFocusContainBound) {
        document.addEventListener('focusin', this._documentFocusContainHandler, true);
        this._documentFocusContainBound = true;
      }
    }

    observeModalVisibility() {
      if (!this.elements.modal || this.modalObserver) {
        return;
      }
      this.modalObserver = new MutationObserver(() => {
        if (this.elements.modal.classList.contains('hidden')) {
          return;
        }
        this.handleModalOpened();
      });
      this.modalObserver.observe(this.elements.modal, { attributes: true, attributeFilter: ['class'] });
    }

    handleModalOpened() {
      this.resetWizardState();
      this.initSelect2(this.select2Selectors(), { force: true });
      this.syncVisibleSelect2();
      window.setTimeout(() => {
        document.getElementById('edit_name')?.focus();
      }, 40);
    }

    handleShortcuts(event) {
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

    resetWizardState() {
      this.currentStep = 1;
      this.clearAlert();
      this.setStep(1, { focusFirst: false });
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
      if (this.elements.prevBtn) {
        this.elements.prevBtn.style.display = stepNumber > 1 ? '' : 'none';
      }
      if (this.elements.nextBtn) {
        this.elements.nextBtn.style.display = stepNumber < STEP_ORDER.length ? '' : 'none';
      }
      if (this.elements.submitBtn) {
        this.elements.submitBtn.style.display = stepNumber === STEP_ORDER.length ? '' : 'none';
      }

      this.syncVisibleSelect2();
      if (stepNumber === STEP_ORDER.length) {
        this.buildSummary();
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
      this.clearAlert();
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

      if (stepNumber === 1) {
        const dob = String(document.getElementById('edit_dob')?.value || '').trim();
        const age = String(document.getElementById('edit_age')?.value || '').trim();
        if (!dob && !age) {
          this.setFieldError('edit_dob', 'Date of birth is required (age is calculated automatically).');
          document.getElementById('edit_dob')?.focus();
          return false;
        }
      }

      if (stepNumber === 2) {
        const phone = (document.getElementById('edit_phone')?.value || '').replace(/\D/g, '');
        if (phone.length !== 10) {
          this.setFieldError('edit_phone', 'Mobile number must be 10 digits.');
          document.getElementById('edit_phone')?.focus();
          return false;
        }
      }

      return true;
    }

    validationMessage(fieldId) {
      const map = {
        edit_name: 'Full name is required.',
        edit_dob: 'Date of birth is required.',
        edit_gender: 'Gender is required.',
        edit_phone: 'Mobile number is required.',
      };
      return map[fieldId] || 'This field is required.';
    }

    setFieldError(fieldId, message) {
      const field = document.getElementById(fieldId);
      if (field) {
        field.classList.add('is-invalid');
      }
      this.showAlert(message);
    }

    clearStepErrors(stepNumber) {
      const config = STEP_CONFIG[stepNumber];
      const pane = document.getElementById(config.paneId);
      if (!pane) {
        return;
      }
      pane.querySelectorAll('.is-invalid').forEach((el) => el.classList.remove('is-invalid'));
    }

    clearAlert() {
      if (!this.elements.alertEl) {
        return;
      }
      this.elements.alertEl.classList.add('d-none');
      this.elements.alertEl.innerHTML = '';
    }

    showAlert(message) {
      if (!this.elements.alertEl) {
        return;
      }
      this.elements.alertEl.innerHTML = message;
      this.elements.alertEl.classList.remove('d-none');
    }

    handleFormKeydown(event) {
      if (event.key !== 'Enter' || event.isComposing) {
        return;
      }
      if (!this.elements.modal || this.elements.modal.classList.contains('hidden')) {
        return;
      }
      if (document.querySelector('#editProfileModal .select2-container--open')) {
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
        const summary = document.getElementById('editSummary');
        if (summary && active !== this.elements.submitBtn && (active === summary || summary.contains(active))) {
          event.preventDefault();
          event.stopPropagation();
          this.elements.submitBtn?.focus?.({ preventScroll: false });
        }
        return;
      }

      if (active.closest('.modal-footer')) {
        return;
      }
      if (active.tagName === 'TEXTAREA' || active.tagName === 'BUTTON') {
        return;
      }

      const paneFocusables = this.getPaneFocusables();
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

    isDetachedOverlayFocus() {
      const active = document.activeElement;
      if (!active || !(active instanceof HTMLElement)) {
        return false;
      }
      return !!(active.closest('.select2-dropdown') || active.closest('.flatpickr-calendar'));
    }

    getPaneFocusables() {
      const config = STEP_CONFIG[this.currentStep];
      const pane = config ? document.getElementById(config.paneId) : null;
      if (!pane) {
        return [];
      }
      const list = [];
      const seen = new Set();
      const candidates = pane.querySelectorAll(
        'input:not([type="hidden"]), select, textarea, button, a[href], .select2-selection, #editSummary',
      );
      for (const el of candidates) {
        if (!(el instanceof HTMLElement)) {
          continue;
        }
        if (el.id === 'editSummary') {
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

    getFooterFocusablesInTabOrder() {
      const out = [];
      const pushIf = (btn) => {
        if (!btn || !(btn instanceof HTMLElement)) {
          return;
        }
        if (btn.style.display === 'none' || btn.disabled || !this.isFocusableField(btn)) {
          return;
        }
        out.push(btn);
      };
      pushIf(this.elements.nextBtn);
      pushIf(this.elements.cancelBtn);
      pushIf(this.elements.prevBtn);
      pushIf(this.elements.submitBtn);
      return out;
    }

    getModalFocusables() {
      const modal = this.elements.modal;
      if (!modal || modal.classList.contains('hidden')) {
        return [];
      }
      const list = [];
      const seen = new Set();
      const candidates = modal.querySelectorAll(
        'input:not([type="hidden"]), select, textarea, button, a[href], .select2-selection, #editSummary',
      );
      for (const el of candidates) {
        if (!(el instanceof HTMLElement) || el.closest('[inert]')) {
          continue;
        }
        if (el.id === 'editSummary') {
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
        if (!this.isFocusableField(el) || seen.has(el)) {
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
      if (this.isDetachedOverlayFocus()) {
        return;
      }

      const active = document.activeElement;
      if (active instanceof HTMLElement && this.currentStep < STEP_ORDER.length) {
        const paneList = this.getPaneFocusables();
        const footerList = this.getFooterFocusablesInTabOrder();

        if (!event.shiftKey && paneList.length) {
          const lastPane = paneList[paneList.length - 1];
          if (active === lastPane && this.elements.nextBtn && this.elements.nextBtn.style.display !== 'none') {
            event.preventDefault();
            event.stopPropagation();
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

    handleDocumentFocusContain(event) {
      const modal = this.elements.modal;
      if (!modal || modal.classList.contains('hidden')) {
        return;
      }
      const t = event.target;
      if (!(t instanceof HTMLElement) || modal.contains(t)) {
        return;
      }
      if (t.closest('.select2-dropdown') || t.closest('.flatpickr-calendar')) {
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
        const preferred = list.find((el) => el.id === 'edit_name') || list[0];
        preferred.focus({ preventScroll: true });
      });
    }

    focusFirstInActivePane() {
      if (this.currentStep === STEP_ORDER.length) {
        const summary = document.getElementById('editSummary');
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
        el.focus();
        return;
      }
    }

    select2Selectors() {
      return Array.from(this.elements.form?.querySelectorAll('select.select2[id]') || []).map((el) => `#${el.id}`);
    }

    initSelect2(selectors = this.select2Selectors(), options = {}) {
      if (!(window.jQuery && jQuery.fn && jQuery.fn.select2)) {
        return;
      }
      const force = !!options.force;
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
        $field.select2({ width: '100%', dropdownParent: jQuery('#editProfileModal .modal') });
      });
    }

    syncVisibleSelect2() {
      const config = STEP_CONFIG[this.currentStep];
      const activePane = config ? document.getElementById(config.paneId) : null;
      if (!activePane) {
        this.initSelect2(this.select2Selectors(), { force: false });
        return;
      }
      const selectors = Array.from(activePane.querySelectorAll('select.select2[id]')).map((select) => `#${select.id}`);
      if (selectors.length) {
        this.initSelect2(selectors, { force: false });
      }
    }

    ensureAltInputEditable(instance) {
      const alt = instance ? instance.altInput : null;
      if (!alt) {
        return;
      }
      alt.removeAttribute('readonly');
      alt.setAttribute('placeholder', 'DD-MM-YYYY');
      alt.setAttribute('inputmode', 'numeric');
      alt.setAttribute('autocomplete', 'off');
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
      const dobField = document.getElementById('edit_dob');
      if (!dobField) {
        return;
      }
      if (dobField._flatpickr) {
        return;
      }
      this.dobInstance = window.flatpickr(dobField, {
        altInput: true,
        altFormat: 'd-m-Y',
        dateFormat: 'Y-m-d',
        maxDate: 'today',
        allowInput: true,
        onChange: () => this.calcAge(),
        onReady: (_d, _s, instance) => this.ensureAltInputEditable(instance),
        onOpen: (_d, _s, instance) => this.ensureAltInputEditable(instance),
      });
    }

    calcAge() {
      const dob = document.getElementById('edit_dob')?.value;
      const ageField = document.getElementById('edit_age');
      if (!ageField) {
        return;
      }
      if (!dob) {
        return;
      }
      const birthDate = new Date(dob);
      if (isNaN(birthDate.getTime())) {
        return;
      }
      const calculatedAge = Math.floor((Date.now() - birthDate.getTime()) / (1000 * 60 * 60 * 24 * 365.25));
      if (calculatedAge >= 0) {
        ageField.value = calculatedAge;
      }
    }

    getSelectedStateId(stateSelect) {
      if (!stateSelect) {
        return '';
      }
      const option = stateSelect.options[stateSelect.selectedIndex];
      return option?.getAttribute('data-state-id') || '';
    }

    loadDistricts(stateId, selectedDistrictName) {
      const districtSelect = this.elements.districtSelect;
      const stateSelect = this.elements.stateSelect;
      if (!districtSelect || !stateSelect) {
        return;
      }
      if (!stateId) {
        districtSelect.innerHTML = '<option value="">Select District</option>';
        if (window.jQuery && jQuery(districtSelect).hasClass('select2-hidden-accessible')) {
          jQuery(districtSelect).trigger('change.select2');
        }
        return;
      }

      districtSelect.innerHTML = '<option value="">Loading...</option>';
      const url = `${stateSelect.getAttribute('data-district-url')}?state_id=${encodeURIComponent(stateId)}`;
      fetch(url)
        .then((res) => res.json())
        .then((data) => {
          districtSelect.innerHTML = '<option value="">Select District</option>';
          if (Array.isArray(data)) {
            data.forEach((dist) => {
              const opt = document.createElement('option');
              opt.value = dist.name;
              opt.textContent = dist.name;
              if (selectedDistrictName && dist.name === selectedDistrictName) {
                opt.selected = true;
              }
              districtSelect.appendChild(opt);
            });
          }
          this.initSelect2(['#edit_district'], { force: true });
        })
        .catch(() => {
          districtSelect.innerHTML = '<option value="">Error loading districts</option>';
        });
    }

    getSelectedAllergyNamesText() {
      const select = document.getElementById('edit_allergies');
      if (!select) {
        return '—';
      }
      const names = Array.from(select.selectedOptions)
        .map((option) => String(option.text || '').trim())
        .filter(Boolean);
      return names.length ? names.join(', ') : '—';
    }

    selectedText(selectId) {
      const select = document.getElementById(selectId);
      if (!select) {
        return '';
      }
      return select.options[select.selectedIndex]?.text || '';
    }

    buildSummary() {
      const allergies = this.getSelectedAllergyNamesText();
      const chronic = Array.from(this.elements.form?.querySelectorAll('input[name="chronic_conditions[]"]:checked') || [])
        .map((el) => el.value)
        .join(', ') || '—';

      const left = `
        <div class="fs-13 fw-700 mb-8">Patient Details</div>
        <div class="fs-12 mb-4"><b>Name:</b> ${document.getElementById('edit_name')?.value || '—'}</div>
        <div class="fs-12 mb-4"><b>DOB:</b> ${document.getElementById('edit_dob')?.value || '—'} (${document.getElementById('edit_age')?.value || '—'})</div>
        <div class="fs-12 mb-4"><b>Gender:</b> ${document.getElementById('edit_gender')?.value || '—'}</div>
        <div class="fs-12 mb-4"><b>Phone:</b> ${document.getElementById('edit_phone')?.value || '—'}</div>
        <div class="fs-12 mb-4"><b>Email:</b> ${document.getElementById('edit_email')?.value || '—'}</div>`;

      const right = `
        <div class="fs-13 fw-700 mb-8">Medical &amp; Address</div>
        <div class="fs-12 mb-4"><b>Address:</b> ${document.getElementById('edit_address')?.value || '—'}</div>
        <div class="fs-12 mb-4"><b>State / District:</b> ${this.selectedText('edit_state') || '—'} / ${this.selectedText('edit_district') || '—'}</div>
        <div class="fs-12 mb-4"><b>Allergies:</b> ${allergies}</div>
        <div class="fs-12 mb-4"><b>Chronic Conditions:</b> ${chronic}</div>`;

      if (this.elements.summaryLeft) {
        this.elements.summaryLeft.innerHTML = left;
      }
      if (this.elements.summaryRight) {
        this.elements.summaryRight.innerHTML = right;
      }
    }

    async submitProfile() {
      for (const step of [1, 2]) {
        const valid = await this.validateStep(step);
        if (!valid) {
          this.setStep(step, { focusFirst: true });
          return;
        }
      }

      if (this.elements.submitBtn) {
        this.elements.submitBtn.disabled = true;
      }
      this.clearAlert();

      const formData = new FormData(this.elements.form);
      try {
        const response = await fetch(this.routes.updateProfile, {
          method: 'POST',
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
          body: formData,
        });
        const data = await response.json();
        if (response.ok && data?.status) {
          closeModal('editProfileModal');
          window.location.reload();
          return;
        }

        let errMsg = data?.message || 'Profile update failed. Please check inputs.';
        if (Array.isArray(data?.errors)) {
          const errList = [];
          data.errors.forEach((err) => {
            errList.push(err.message);
            const inputEl = this.elements.form.querySelector(`[name="${err.code}"]`);
            inputEl?.classList?.add('is-invalid');
          });
          errMsg = errList.join('<br>') || errMsg;
        }
        this.showAlert(errMsg);
      } catch (error) {
        console.error(error);
        this.showAlert('An unexpected error occurred. Please try again.');
      } finally {
        if (this.elements.submitBtn) {
          this.elements.submitBtn.disabled = false;
        }
      }
    }
  }

  const controller = new PatientEditProfileFormController();
  window.PatientEditProfileForm = controller;

  document.addEventListener('DOMContentLoaded', () => {
    if (window.PatientEditProfileConfig) {
      controller.init({ routes: window.PatientEditProfileConfig.routes || {} });
    }
  });
})();
