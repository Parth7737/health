(function (window, $) {
    'use strict';

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function createPrescriptionComposer(options) {
        var settings = $.extend(true, {
            selectors: {
                tbody: '#prescriptionItemsTbody',
                medicine: '#prescription_entry_medicine',
                dosage: '#prescription_entry_dosage',
                instruction: '#prescription_entry_instruction',
                route: '#prescription_entry_route',
                frequency: '#prescription_entry_frequency',
                days: '#prescription_entry_days',
                addButton: '#addPrescriptionItemRow',
                addIcon: '#prescriptionAddIcon',
                cancelButton: '#cancelPrescriptionItemEdit',
                form: '#opdPrescriptionForm'
            },
            select2Parent: null,
            getLoadDosagesUrl: function () {
                return $(settings.selectors.form).data('load-dosages-url') || '';
            },
            getCsrfToken: function () {
                return $('meta[name="csrf-token"]').attr('content') || '';
            }
        }, options || {});

        var dosageCache = {};
        var editRowId = null;
        var rowCounter = 0;

        function $el(key) {
            return $(settings.selectors[key]);
        }

        function getSelectedText($select) {
            return ($select.find('option:selected').text() || '').trim();
        }

        function triggerSelect2Change($select) {
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.trigger('change.select2');
            }
        }

        function setSelectValue($select, value) {
            $select.val(value || '');
            triggerSelect2Change($select);
        }

        function setDosageOptions(dosages, selectedValue) {
            var html = '<option value="">Select Dosage</option>';
            $.each(dosages || [], function (_, dosage) {
                var isSelected = selectedValue && String(selectedValue) === String(dosage.id);
                html += '<option value="' + dosage.id + '"' + (isSelected ? ' selected' : '') + '>' + escapeHtml(dosage.dosage) + '</option>';
            });

            var $dosage = $el('dosage');
            $dosage.html(html);
            if (selectedValue) {
                $dosage.val(String(selectedValue));
            }
            triggerSelect2Change($dosage);
        }

        function ensureEmptyRowState() {
            var $tbody = $el('tbody');
            if (!$tbody.length) {
                return;
            }

            var hasRows = $tbody.find('tr.prescription-item-row').length > 0;
            var $empty = $tbody.find('.prescription-empty-row');
            if (!hasRows) {
                if (!$empty.length) {
                    $tbody.append('<tr class="prescription-empty-row"><td colspan="7" class="text-muted text-center">No medicine added yet.</td></tr>');
                }
                return;
            }

            $empty.remove();
        }

        function clearComposer() {
            editRowId = null;
            setSelectValue($el('medicine'), '');
            setSelectValue($el('instruction'), '');
            setSelectValue($el('route'), '');
            setSelectValue($el('frequency'), '');
            $el('days').val('');
            setDosageOptions([], null);
            $el('addButton').attr('title', 'Add medicine').attr('aria-label', 'Add medicine');
            $el('addIcon').attr('class', 'fa-solid fa-plus');
            $el('cancelButton').addClass('d-none');
        }

        function buildHiddenInputs(item) {
            return '' +
                '<input type="hidden" name="medicine_id[]" value="' + escapeHtml(item.medicineId || '') + '">' +
                '<input type="hidden" name="medicine_dosage_id[]" value="' + escapeHtml(item.dosageId || '') + '">' +
                '<input type="hidden" name="medicine_instruction_id[]" value="' + escapeHtml(item.instructionId || '') + '">' +
                '<input type="hidden" name="medicine_route_id[]" value="' + escapeHtml(item.routeId || '') + '">' +
                '<input type="hidden" name="medicine_frequency_id[]" value="' + escapeHtml(item.frequencyId || '') + '">' +
                '<input type="hidden" name="no_of_day[]" value="' + escapeHtml(item.days || '') + '">';
        }

        function getEntryState() {
            var $medicine = $el('medicine');
            var $selectedMedicine = $medicine.find('option:selected');
            var $dosage = $el('dosage');
            var $instruction = $el('instruction');
            var $route = $el('route');
            var $frequency = $el('frequency');

            return {
                medicineId: $medicine.val() || '',
                categoryId: $selectedMedicine.data('category-id') || '',
                medicineText: ($selectedMedicine.text() || '').trim(),
                dosageId: $dosage.val() || '',
                dosageText: getSelectedText($dosage),
                instructionId: $instruction.val() || '',
                instructionText: getSelectedText($instruction),
                routeId: $route.val() || '',
                routeText: getSelectedText($route),
                frequencyId: $frequency.val() || '',
                frequencyText: getSelectedText($frequency),
                days: $el('days').val() || ''
            };
        }

        function upsertRow(item) {
            var rowId = editRowId || String(++rowCounter);
            var rowHtml = '' +
                '<tr class="prescription-item-row" data-row-id="' + rowId + '" data-medicine-id="' + escapeHtml(item.medicineId) + '" data-category-id="' + escapeHtml(item.categoryId) + '" data-dosage-id="' + escapeHtml(item.dosageId) + '" data-instruction-id="' + escapeHtml(item.instructionId) + '" data-route-id="' + escapeHtml(item.routeId) + '" data-frequency-id="' + escapeHtml(item.frequencyId) + '" data-days="' + escapeHtml(item.days) + '">' +
                    '<td>' + escapeHtml(item.medicineText || '-') + '</td>' +
                    '<td>' + escapeHtml((item.dosageText && item.dosageText != 'Select Dosage') ? item.dosageText : '-') + '</td>' +
                    '<td>' + escapeHtml((item.instructionText && item.instructionText !='Select') ? item.instructionText : '-') + '</td>' +
                    '<td>' + escapeHtml((item.routeText && item.routeText !='Select') ? item.routeText : '-') + '</td>' +
                    '<td>' + escapeHtml((item.frequencyText && item.frequencyText !='Select') ? item.frequencyText : '-') + '</td>' +
                    '<td>' + escapeHtml(item.days || '-') + '</td>' +
                    '<td class="text-end">' +
                        '<span class="prescription-row-actions">' +
                            '<button type="button" class="btn btn-primary btn-xs prescription-icon-btn edit-prescription-item-row mr-2" title="Edit medicine" aria-label="Edit medicine"><i class="fa-solid fa-pen"></i></button>' +
                            '<button type="button" class="btn btn-danger btn-xs prescription-icon-btn remove-prescription-item-row" title="Remove medicine" aria-label="Remove medicine">✕</button>' +
                        '</span>' +
                        buildHiddenInputs(item) +
                    '</td>' +
                '</tr>';

            var $tbody = $el('tbody');
            if (editRowId) {
                $tbody.find('tr.prescription-item-row[data-row-id="' + editRowId + '"]').replaceWith(rowHtml);
            } else {
                $tbody.append(rowHtml);
            }

            clearComposer();
            ensureEmptyRowState();
        }

        function loadFromRow($row) {
            if (!$row || !$row.length) {
                return;
            }

            editRowId = String($row.data('row-id') || '');
            setSelectValue($el('medicine'), String($row.data('medicine-id') || ''));
            setSelectValue($el('instruction'), String($row.data('instruction-id') || ''));
            setSelectValue($el('route'), String($row.data('route-id') || ''));
            setSelectValue($el('frequency'), String($row.data('frequency-id') || ''));
            $el('days').val(String($row.data('days') || ''));

            $el('addButton').attr('title', 'Update medicine').attr('aria-label', 'Update medicine');
            $el('addIcon').attr('class', 'fa-solid fa-check');
            $el('cancelButton').removeClass('d-none');

            loadDosagesByCategory(
                String($row.data('category-id') || ''),
                String($row.data('dosage-id') || ''),
                false,
                String($row.data('medicine-id') || '')
            );
            $el('medicine').trigger('focus');
        }

        function loadDosagesByCategory(categoryId, selectedValue, autoOpenDropdown, medicineId) {
            if (!categoryId && !medicineId) {
                setDosageOptions([], null);
                return $.Deferred().resolve().promise();
            }

            var cacheKey = String(categoryId || ('medicine-' + medicineId));

            function maybeOpenDosage() {
                if (!autoOpenDropdown) {
                    return;
                }

                setTimeout(function () {
                    var $dosage = $el('dosage');
                    if ($dosage.hasClass('select2-hidden-accessible')) {
                        $dosage.select2('open');
                        return;
                    }
                    $dosage.trigger('focus');
                }, 30);
            }

            if (dosageCache[cacheKey]) {
                setDosageOptions(dosageCache[cacheKey], selectedValue);
                if (selectedValue) {
                    setSelectValue($el('dosage'), String(selectedValue));
                }
                maybeOpenDosage();
                return $.Deferred().resolve().promise();
            }

            var loadUrl = settings.getLoadDosagesUrl();
            if (!loadUrl) {
                setDosageOptions([], null);
                return $.Deferred().resolve().promise();
            }

            return $.ajax({
                url: loadUrl,
                method: 'POST',
                dataType: 'json',
                data: {
                medicine_category_id: categoryId,
                medicine_id: medicineId || '',
                _token: settings.getCsrfToken()
                }
            }).done(function (response) {
                dosageCache[cacheKey] = response || [];
                setDosageOptions(dosageCache[cacheKey], selectedValue);
                if (selectedValue) {
                    setSelectValue($el('dosage'), String(selectedValue));
                }
                maybeOpenDosage();
            }).fail(function () {
                setDosageOptions([], null);
                if (typeof window.sendmsg === 'function') {
                    window.sendmsg('error', 'Unable to load dosages for selected medicine.');
                }
            });
        }

        function focusStart() {
            var $medicine = $el('medicine');
            if (!$medicine.length) {
                return;
            }

            setTimeout(function () {
                if ($medicine.hasClass('select2-hidden-accessible')) {
                    $medicine.select2('open');
                    return;
                }
                $medicine.trigger('focus');
            }, 80);
        }

        function focusNextField(currentId) {
            var order = {
                prescription_entry_medicine: 'dosage',
                prescription_entry_dosage: 'instruction',
                prescription_entry_instruction: 'route',
                prescription_entry_route: 'frequency',
                prescription_entry_frequency: 'days'
            };

            var key = order[currentId] || '';
            if (!key) {
                return;
            }

            var $next = $el(key);
            if (!$next.length) {
                return;
            }

            setTimeout(function () {
                if ($next.hasClass('select2-hidden-accessible')) {
                    $next.select2('open');
                    return;
                }
                $next.trigger('focus');
            }, 30);
        }

        function removeRow($row) {
            if (!$row || !$row.length) {
                return;
            }

            if (editRowId && String($row.data('row-id')) === String(editRowId)) {
                clearComposer();
            }
            $row.remove();
            ensureEmptyRowState();
        }

        function initialize() {
            editRowId = null;
            rowCounter = 0;
            dosageCache = {};

            $el('tbody').find('tr.prescription-item-row').each(function () {
                var rowId = parseInt($(this).attr('data-row-id'), 10);
                if (!isNaN(rowId) && rowId > rowCounter) {
                    rowCounter = rowId;
                }
            });

            clearComposer();
            ensureEmptyRowState();
        }

        function addOrUpdateFromComposer(onValidationError) {
            var entry = getEntryState();
            if (!entry.medicineId) {
                if (typeof onValidationError === 'function') {
                    onValidationError('Please select medicine.', settings.selectors.medicine);
                }
                return false;
            }
            if (!entry.frequencyId) {
                if (typeof onValidationError === 'function') {
                    onValidationError('Please select frequency.', settings.selectors.frequency);
                }
                return false;
            }
            if (!entry.days) {
                if (typeof onValidationError === 'function') {
                    onValidationError('Please enter number of days.', settings.selectors.days);
                }
                return false;
            }

            upsertRow(entry);
            return true;
        }

        function onMedicineChanged(autoOpenDropdown) {
            var $medicine = $el('medicine');
            var categoryId = $medicine.find('option:selected').data('category-id') || '';
            var medicineId = $medicine.val() || '';
            return loadDosagesByCategory(categoryId, null, autoOpenDropdown !== false, medicineId);
        }

        return {
            initialize: initialize,
            focusStart: focusStart,
            focusNextField: focusNextField,
            addOrUpdateFromComposer: addOrUpdateFromComposer,
            clearComposer: clearComposer,
            loadFromRow: loadFromRow,
            removeRow: removeRow,
            onMedicineChanged: onMedicineChanged
        };
    }

    function refreshDiagnosticPreview(selectSelector, tbodySelector) {
        var $select = $(selectSelector || '#diagnostic_test_ids');
        var $tbody = $(tbodySelector || '#diagnostic-test-preview-body');

        if (!$select.length || !$tbody.length) {
            return;
        }

        var $selected = $select.find('option:selected');
        if (!$selected.length) {
            $tbody.html('<tr class="empty-diagnostic-test-row"><td colspan="5" class="text-muted text-center">No test selected.</td></tr>');
            return;
        }

        var rows = '';
        $selected.each(function () {
            var $option = $(this);
            rows += '<tr>' +
                '<td>' + escapeHtml($option.text().trim()) + '</td>' +
                '<td>' + escapeHtml($option.data('category') || 'N/A') + '</td>' +
                '<td>' + escapeHtml($option.data('code') || 'N/A') + '</td>' +
                '<td>' + escapeHtml($option.data('parameters') || 'No parameters') + '</td>' +
                '<td>' + escapeHtml($option.data('charge') || '0.00') + '</td>' +
            '</tr>';
        });

        $tbody.html(rows);
    }

    window.OPDCareShared = {
        createPrescriptionComposer: createPrescriptionComposer,
        refreshDiagnosticPreview: refreshDiagnosticPreview,
        escapeHtml: escapeHtml
    };
})(window, window.jQuery);