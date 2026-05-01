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

  const ADMIT_FIELD_MAP = {
    patient_id: 'admit_patient_search',
    hr_department_id: 'admit_dept',
    bed_id: 'admit_bed',
    doctor_id: 'admit_doctor',
    admission_reason: 'admit_reason',
    payment_mode: 'admit_payment',
    advance_deposit: 'admit_advance',
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
      this.token.searchResults?.addEventListener('click', (event) => this.handleTokenResultClick(event));
      document.addEventListener('click', (event) => {
        if (!this.token.searchResults || !this.token.patientSearch) {
          return;
        }
        if (event.target === this.token.patientSearch || this.token.searchResults.contains(event.target)) {
          return;
        }
        this.token.searchResults.innerHTML = '';
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
      this.admit.searchResults?.addEventListener('click', (event) => this.handleAdmitResultClick(event));
      this.admit.preview?.addEventListener('click', (event) => this.handleBedPreviewClick(event));
      this.admit.form?.querySelectorAll('input, select, textarea').forEach((field) => {
        field.addEventListener('input', () => this.clearFieldError(field.id));
        field.addEventListener('change', () => this.clearFieldError(field.id));
      });

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
    }

    observeModalState() {
      if (this.token.modal && !this.tokenModalObserver) {
        this.tokenModalObserver = new MutationObserver(() => {
          if (this.token.modal.classList.contains('hidden')) {
            this.resetTokenForm(true);
          } else {
            this.resetTokenForm(false);
            this.initFlatpickr();
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
      if (this.token.searchResults) this.token.searchResults.innerHTML = '';
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
        allowInput: false,
        onChange: async () => {
          await this.loadTokenSlots();
          await this.loadSlotWiseTokenPreview();
        },
      });
    }

    setupFlatpickrField(field, config) {
      if (!field) {
        return;
      }

      if (field._flatpickr) {
        field._flatpickr.destroy();
      }

      window.flatpickr(field, config);
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
      if (this.admit.patientChip) {
        this.admit.patientChip.style.display = 'none';
        this.admit.patientChip.innerHTML = '';
      }
      this.admit.doctor.innerHTML = '<option value="">Select Doctor</option>';
      this.renderAvailableBeds();
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

    async handleTokenPatientSearch() {
      const q = this.token.patientSearch?.value.trim() || '';
      if (this.token.patientId?.value) {
        this.token.patientId.value = '';
        if (this.token.patientName) this.token.patientName.value = '';
        if (this.token.patientAge) this.token.patientAge.value = '';
        if (this.token.patientGender) this.token.patientGender.value = '';
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
          <button type="button" class="tok-search-item" data-token-patient="1" data-id="${patient.id}" data-mrn="${encodeURIComponent(patient.mrn || '')}" data-name="${encodeURIComponent(patient.name || '')}" data-phone="${encodeURIComponent(patient.phone || '')}" data-age-sex="${encodeURIComponent(patient.age_sex || '')}">
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
      this.tokenPatient = {
        id: item.dataset.id,
        mrn: decodeURIComponent(item.dataset.mrn || ''),
        name: decodeURIComponent(item.dataset.name || ''),
        phone: decodeURIComponent(item.dataset.phone || ''),
        ageSex: decodeURIComponent(item.dataset.ageSex || ''),
      };
      this.token.patientId.value = this.tokenPatient.id;
      this.token.patientName.value = this.tokenPatient.name;
      this.token.patientSearch.value = `${this.tokenPatient.mrn} - ${this.tokenPatient.name}`;
      this.fillTokenAgeSex(this.tokenPatient.ageSex);
      this.token.searchResults.innerHTML = '';
      this.clearFieldError('tok_patient_search');
    }

    fillTokenAgeSex(ageSexText) {
      const [age, sex] = String(ageSexText || '').split('/');
      if (this.token.patientAge) {
        this.token.patientAge.value = age && age !== '-' ? age : '';
      }
      if (this.token.patientGender) {
        const normalizedSex = String(sex || '').trim();
        this.token.patientGender.value = ['Male', 'Female', 'Other'].includes(normalizedSex) ? normalizedSex : '';
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

    escapeHtml(value) {
      return String(value || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
    }

    async handleAdmitPatientSearch() {
      const q = this.admit.patientSearch?.value.trim() || '';
      if (q.length < 2) {
        this.admit.searchResults.innerHTML = '';
        return;
      }
      const data = await window.pmFetch(`${this.routes.searchPatients}?q=${encodeURIComponent(q)}`);
      this.admit.searchResults.innerHTML = (data || []).map((patient) => `
        <div class="patient-chip mt-8" style="cursor:pointer" data-admit-patient="1" data-id="${patient.id}" data-mrn="${encodeURIComponent(patient.mrn || '')}" data-name="${encodeURIComponent(patient.name || '')}" data-phone="${encodeURIComponent(patient.phone || '')}" data-age-sex="${encodeURIComponent(patient.age_sex || '')}">
          <div class="patient-chip-info">
            <div class="patient-chip-name">${patient.name}</div>
            <div class="patient-chip-meta">${patient.mrn} | ${patient.phone || '-'} | ${patient.age_sex}</div>
          </div>
        </div>`).join('');
    }

    handleAdmitResultClick(event) {
      const item = event.target.closest('[data-admit-patient]');
      if (!item) {
        return;
      }
      this.admitPatient = {
        id: item.dataset.id,
        mrn: decodeURIComponent(item.dataset.mrn || ''),
        name: decodeURIComponent(item.dataset.name || ''),
        phone: decodeURIComponent(item.dataset.phone || ''),
        ageSex: decodeURIComponent(item.dataset.ageSex || ''),
      };
      this.admit.patientId.value = this.admitPatient.id;
      this.admit.patientSearch.value = `${this.admitPatient.mrn} - ${this.admitPatient.name}`;
      this.admit.searchResults.innerHTML = '';
      this.admit.patientChip.style.display = '';
      this.admit.patientChip.innerHTML = `<div class="patient-chip-info"><div class="patient-chip-name">${this.admitPatient.name}</div><div class="patient-chip-meta">${this.admitPatient.mrn} | ${this.admitPatient.phone || '-'} | ${this.admitPatient.ageSex}</div></div>`;
      this.clearFieldError('admit_patient_search');
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
        window.PatientRegistrationForm?.initSelect2?.(['#reg_bed']);
        window.PatientRegistrationForm?.displayBedDetails?.();
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
          body: {
            patient_id: this.admit.patientId.value,
            hr_department_id: this.admit.dept.value,
            doctor_id: this.admit.doctor.value || null,
            bed_id: this.admit.bed.value,
            admission_reason: this.admit.reason.value || null,
            payment_mode: this.admit.payment.value || null,
            advance_deposit: this.admit.advance.value || 0,
          }
        });
        sendmsg('success', `Admission ${data.admission_no} created. Bed ${data.bed_no}.`);
        closeModal('ipdAdmitModal');
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
