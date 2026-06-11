(function () {
    'use strict';

    var marState = {
        date: '',
        filtersBound: false,
        canManage: false
    };

    function getCsrfToken() {
        var tokenMeta = document.querySelector('meta[name="csrf-token"]');
        if (tokenMeta && tokenMeta.getAttribute('content')) {
            return tokenMeta.getAttribute('content');
        }
        if (window.Laravel && window.Laravel.csrfToken) {
            return window.Laravel.csrfToken;
        }
        return '';
    }

    function formatTime12(time24) {
        if (!time24) {
            return '-';
        }

        var match = String(time24).trim().match(/^(\d{1,2}):(\d{2})$/);
        if (!match) {
            return String(time24);
        }

        var hour = parseInt(match[1], 10);
        var minute = match[2];
        var period = hour >= 12 ? 'PM' : 'AM';
        hour = hour % 12;
        if (hour === 0) {
            hour = 12;
        }

        return hour + ':' + minute + ' ' + period;
    }

    function escapeHtml(value) {
        return String(value == null ? '' : value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function toast(title, message, type) {
        if (typeof window.showToast === 'function') {
            window.showToast(title, message, type || 'info');
            return;
        }
        if (typeof window.toast === 'function') {
            window.toast(title, message, type || 'info');
        }
    }

    function timeSourceBadge(source) {
        var map = {
            frequency: { cls: 'badge-green', label: 'Frequency Times' },
            meal: { cls: 'badge-orange', label: 'Meal-Based' },
            auto: { cls: 'badge-gray', label: 'Auto Schedule' }
        };
        var meta = map[source] || map.auto;
        return '<span class="badge ' + meta.cls + ' mar-meal-badge">' + meta.label + '</span>';
    }

    function statusBadge(status) {
        var map = {
            pending: 'badge-orange',
            given: 'badge-green',
            missed: 'badge-red',
            held: 'badge-gray',
            refused: 'badge-red'
        };
        var label = {
            pending: 'Pending',
            given: 'Given',
            missed: 'Missed',
            held: 'Held',
            refused: 'Refused'
        };
        return '<span class="badge ' + (map[status] || 'badge-gray') + '">' + escapeHtml(label[status] || status) + '</span>';
    }

    function renderSlotActions(patient, medication, slot) {
        if (slot.status !== 'pending') {
            var meta = [];
            if (slot.administered_by) {
                meta.push('By <b>' + escapeHtml(slot.administered_by) + '</b>');
            }
            if (slot.administered_at) {
                meta.push('at ' + escapeHtml(slot.administered_at));
            }
            var note = slot.notes ? '<div class="fs-10 text-muted mt-4">' + escapeHtml(slot.notes) + '</div>' : '';
            return statusBadge(slot.status) + (meta.length ? '<div class="fs-10 text-muted mt-4">' + meta.join(' ') + '</div>' : '') + note;
        }

        if (!slot.can_manage) {
            return statusBadge('pending') + '<div class="fs-10 text-muted mt-4">View only</div>';
        }

        var payload = JSON.stringify({
            bed_allocation_id: patient.bed_allocation_id,
            ipd_prescription_id: slot.prescription_id,
            ipd_prescription_item_id: slot.prescription_item_id,
            medicine_id: slot.medicine_id,
            scheduled_date: marState.date,
            scheduled_time: slot.scheduled_time,
            meal_relation: slot.meal_relation
        }).replace(/"/g, '&quot;');
        return '';
        // return '<div class="mar-actions">' +
        //     '<button class="btn btn-success btn-xs" data-mar-action="given" data-mar-payload="' + payload + '">✅ Give</button>' +
        //     '<button class="btn btn-warning btn-xs" data-mar-action="held" data-mar-payload="' + payload + '">⏸ Hold</button>' +
        //     '<button class="btn btn-danger btn-xs" data-mar-action="missed" data-mar-payload="' + payload + '">✖ Missed</button>' +
        //     '<button class="btn btn-outline-danger btn-xs" data-mar-action="refused" data-mar-payload="' + payload + '">🚫 Refused</button>' +
        //     '</div>';
    }

    function renderPatients(patients) {
        if (!patients.length) {
            return '<div class="text-success text-center" style="padding: 40px;">No active IPD medication schedules for selected filters.</div>';
        }

        return patients.map(function (patient) {
            var doseRows = [];

            (patient.medications || []).forEach(function (medication) {
                (medication.slots || []).forEach(function (slot) {
                    doseRows.push(
                        '<div class="mar-dose-row">' +
                        '<div><span class="fw-700 fs-13">' + escapeHtml(formatTime12(slot.scheduled_time)) + '</span></div>' +
                        '<div>' +
                        '  <div class="fw-700 fs-12">' + escapeHtml(medication.medicine_name) + '</div>' +
                        '  <div class="fs-11 text-muted">' + escapeHtml(medication.dosage) + ' · ' + escapeHtml(medication.route) + ' · Day ' + medication.day + '/' + medication.total_days + '</div>' +
                        '</div>' +
                        '<div class="fs-11">' +
                        '  <span class="badge badge-gray mar-meal-badge">' + escapeHtml(slot.meal_label || medication.meal_label) + '</span> ' +
                        '  <div class="text-muted mt-4">' + escapeHtml(medication.instruction || '-') + '</div>' +
                        '</div>' +
                        '<div class="fs-11 text-muted">' + escapeHtml(medication.frequency) + '</div>' +
                        '<div>' + renderSlotActions(patient, medication, slot) + '</div>' +
                        '</div>'
                    );
                });
            });

            // timeSourceBadge(slot.time_source) +
            return '' +
                '<div class="mar-patient-card">' +
                '  <div class="mar-patient-head">' +
                '    <div>' +
                '      <div class="fw-700 fs-14">' + escapeHtml(patient.patient_name) + ' <span class="text-muted fs-12">(' + escapeHtml(patient.patient_uhid) + ')</span></div>' +
                '      <div class="fs-11 text-muted">' + escapeHtml(patient.location) + '</div>' +
                '    </div>' +
                '    <div class="fs-11 text-muted">' + doseRows.length + ' dose(s) scheduled</div>' +
                '  </div>' +
                doseRows.join('') +
                '</div>';
        }).join('');
    }

    function updateSummary(summary) {
        var total = document.getElementById('marSummaryTotal');
        var pending = document.getElementById('marSummaryPending');
        var given = document.getElementById('marSummaryGiven');
        var other = document.getElementById('marSummaryOther');

        if (total) total.textContent = summary.total || 0;
        if (pending) pending.textContent = summary.pending || 0;
        if (given) given.textContent = summary.given || 0;
        if (other) other.textContent = summary.other || 0;
    }

    function updateMealTimes(mealTimes) {
        var breakfast = document.getElementById('marBreakfastTime');
        var lunch = document.getElementById('marLunchTime');
        var dinner = document.getElementById('marDinnerTime');

        if (breakfast && mealTimes && mealTimes.breakfast) breakfast.textContent = formatTime12(mealTimes.breakfast);
        if (lunch && mealTimes && mealTimes.lunch) lunch.textContent = formatTime12(mealTimes.lunch);
        if (dinner && mealTimes && mealTimes.dinner) dinner.textContent = formatTime12(mealTimes.dinner);
    }

    window.loadNursingMar = function () {
        var content = document.getElementById('marContent');
        if (!content) return;

        var url = typeof window.route === 'function' ? window.route('marLoad') : '';
        if (!url) {
            content.innerHTML = '<div class="text-danger text-center py-20">MAR load route is not available.</div>';
            return;
        }

        var searchEl = document.getElementById('marSearch');
        var wardEl = document.getElementById('marWardFilter');
        var dateEl = document.getElementById('marDate');

        content.innerHTML = '<div class="text-muted text-center" style="padding: 40px;">Loading medication administration records...</div>';

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrfToken() || ''
            },
            body: JSON.stringify({
                date: dateEl ? dateEl.value : marState.date,
                ward_id: wardEl ? wardEl.value : '',
                search: searchEl ? String(searchEl.value || '').trim() : ''
            })
        })
            .then(function (res) { return res.ok ? res.json() : Promise.reject(res); })
            .then(function (payload) {
                marState.date = payload.date || marState.date;
                updateSummary(payload.summary || {});
                updateMealTimes(payload.meal_times || {});
                content.innerHTML = renderPatients(payload.patients || []);
            })
            .catch(function () {
                content.innerHTML = '<div class="text-danger text-center py-20">Failed to load MAR data.</div>';
            });
    };

    function loadWards() {
        var wardEl = document.getElementById('marWardFilter');
        if (!wardEl) return;

        var url = typeof window.route === 'function' ? window.route('marWards') : '';
        if (!url) return;

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(function (res) { return res.ok ? res.json() : Promise.reject(res); })
            .then(function (payload) {
                var items = Array.isArray(payload.items) ? payload.items : [];
                wardEl.innerHTML = '<option value="">All Wards</option>' + items.map(function (ward) {
                    return '<option value="' + ward.id + '">' + escapeHtml(ward.ward_name) + '</option>';
                }).join('');
            })
            .catch(function () { });
    }

    function administerDose(status, payload, note) {
        var url = typeof window.route === 'function' ? window.route('marAdminister') : '';
        if (!url) {
            toast('Error', 'MAR action route is not available.', 'error');
            return;
        }

        payload.status = status;
        payload.notes = note || '';

        if (typeof window.loader === 'function') window.loader();

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrfToken() || ''
            },
            body: JSON.stringify(payload)
        })
            .then(function (res) { return res.ok ? res.json() : res.json().then(function (data) { return Promise.reject(data); }); })
            .then(function (data) {
                if (typeof window.loader === 'function') window.loader('hide');
                toast('Success', data.message || 'Updated successfully.', 'success');
                loadNursingMar();
            })
            .catch(function (err) {
                if (typeof window.loader === 'function') window.loader('hide');
                toast('Error', err.message || 'Unable to update MAR entry.', 'error');
            });
    }

    function bindFilters() {
        if (marState.filtersBound) return;

        var searchEl = document.getElementById('marSearch');
        var wardEl = document.getElementById('marWardFilter');
        var dateEl = document.getElementById('marDate');
        var debounceTimer = null;

        if (dateEl && typeof flatpickr !== 'undefined') {
            flatpickr(dateEl, {
                dateFormat: 'Y-m-d',
                defaultDate: new Date(),
                onChange: function (_selectedDates, dateStr) {
                    marState.date = dateStr;
                    loadNursingMar();
                }
            });
            marState.date = dateEl.value || new Date().toISOString().slice(0, 10);
        }

        if (searchEl) {
            searchEl.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(loadNursingMar, 350);
            });
        }

        if (wardEl) {
            wardEl.addEventListener('change', loadNursingMar);
        }

        document.addEventListener('click', function (event) {
            var btn = event.target.closest('[data-mar-action]');
            if (!btn) return;

            var action = btn.getAttribute('data-mar-action');
            var payloadRaw = btn.getAttribute('data-mar-payload');
            if (!payloadRaw) return;

            var payload;
            try {
                payload = JSON.parse(payloadRaw.replace(/&quot;/g, '"'));
            } catch (e) {
                toast('Error', 'Invalid dose payload.', 'error');
                return;
            }

            var note = '';
            if (action !== 'given') {
                note = window.prompt('Enter remark / reason (optional):', '') || '';
            }

            administerDose(action, payload, note);
        });

        marState.filtersBound = true;
    }

    document.addEventListener('DOMContentLoaded', function () {
        bindFilters();
        loadWards();
        loadNursingMar();
    });
})();
