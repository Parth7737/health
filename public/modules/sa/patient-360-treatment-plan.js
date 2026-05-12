/**
 * Patient 360 — Treatment plan modal (SHA preauth-style: load procedures, procedure detail, implant/stratification toggles).
 * Requires window.Patient360Config.treatmentPlan + Patient360Config.csrf
 */
;(function (win, doc) {
    'use strict';

    var cfg = win.Patient360Config;
    if (!cfg || !cfg.treatmentPlan || !win.jQuery) {
        return;
    }

    var $ = win.jQuery;
    var tp = cfg.treatmentPlan;
    var csrf = cfg.csrf;
    var PROC_PREVIEW_LEN = 72;

    var $modal = $('#p360TreatmentPlanModal');
    var $spec = $('#p360TpSpeciality');
    var $proc = $('#p360TpProcedure');
    var $strat = $('#p360TpStratificationId');
    var $impl = $('#p360TpImplantId');
    var $qty = $('#p360TpImplantQty');
    var $units = $('#p360TpUnits');
    var $u100 = $('#p360TpU100Amount');
    var $ichi = $('#p360TpIchi');
    var $add = $('#p360TreatmentPlanAddBtn');
    var $qtyErr = $('#p360TpImplantQtyError');

    var lastDetail = null;

    var $dropdownParent = $modal.find('.modal-content').length ? $modal.find('.modal-content') : $modal;

    function tpDestroySelect2($el) {
        if (!$el || !$el.length || !$.fn.select2) {
            return;
        }
        if ($el.hasClass('select2-hidden-accessible')) {
            try {
                $el.select2('destroy');
            } catch (e) { /* ignore */ }
        }
    }

    function tpBindSelect2($el) {
        if (!$el || !$el.length || !$.fn.select2) {
            return;
        }
        tpDestroySelect2($el);
        if (!$el.parent().hasClass('position-relative')) {
            $el.wrap('<div class="position-relative"></div>');
        }
        $el.select2({
            dropdownParent: $dropdownParent,
            width: '100%',
            minimumResultsForSearch: 0,
        });
    }

    function tpBindAllSelects() {
        tpBindSelect2($spec);
        tpBindSelect2($proc);
        tpBindSelect2($strat);
        tpBindSelect2($impl);
    }

    function escHtml(s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function formatInr(val) {
        var n = parseFloat(String(val).replace(/[^\d.-]/g, ''));
        if (isNaN(n)) {
            n = 0;
        }
        try {
            return '₹' + n.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        } catch (e) {
            return '₹' + n.toFixed(2);
        }
    }

    function procedureCellHtml(full) {
        var t = String(full || '');
        if (t.length <= PROC_PREVIEW_LEN) {
            return '<div class="p360-tp-proc-cell">' + escHtml(t) + '</div>';
        }
        var vis = escHtml(t.slice(0, PROC_PREVIEW_LEN)) + '…';
        return (
            '<div class="p360-tp-proc-cell p360-tp-proc-collapsed" data-full-text="' + escHtml(t) + '">' +
            '<span class="p360-tp-proc-preview">' + vis + '</span> ' +
            '<button type="button" class="btn btn-link btn-sm p-0 align-baseline p360-tp-show-more">Show more</button>' +
            '</div>'
        );
    }

    function notifyTp(msg, type) {
        type = type || 'error';
        if (typeof win.sendmsg === 'function') {
            win.sendmsg(type, msg);
        } else if (win.alert) {
            win.alert(msg);
        }
    }

    function renumberTreatmentPlanRows() {
        var body = doc.getElementById('p360TreatmentPlanTableBody');
        if (!body) {
            return;
        }
        var rows = body.querySelectorAll('tr');
        rows.forEach(function (tr, i) {
            var noCell = tr.querySelector('td.p360-tp-col-no');
            if (noCell) {
                noCell.textContent = String(i + 1);
            }
        });
        var hint = doc.getElementById('p360TreatmentPlanEmptyHint');
        if (hint) {
            hint.style.display = rows.length ? 'none' : '';
        }
    }

    function bindTreatmentPlanTable(body) {
        if (!body || body.dataset.p360TpBound) {
            return;
        }
        body.dataset.p360TpBound = '1';
        body.addEventListener('click', function (ev) {
            var t = ev.target;
            if (t && t.classList && t.classList.contains('p360-tp-show-more')) {
                var wrap = t.closest('.p360-tp-proc-cell');
                if (!wrap) {
                    return;
                }
                var full = wrap.getAttribute('data-full-text') || '';
                wrap.removeAttribute('data-full-text');
                wrap.classList.remove('p360-tp-proc-collapsed');
                wrap.innerHTML = escHtml(full);
                return;
            }
            if (t && t.classList && t.classList.contains('p360-tp-remove-row')) {
                var tr = t.closest('tr');
                if (tr && tr.parentNode) {
                    tr.parentNode.removeChild(tr);
                    renumberTreatmentPlanRows();
                }
            }
        });
    }

    function showLoader() {
        if (typeof win.loader === 'function') {
            win.loader('show');
        }
    }

    function hideLoader() {
        if (typeof win.loader === 'function') {
            win.loader('hide');
        }
    }

    function updateAddButtonState() {
        if (!$add.length) {
            return;
        }
        if (!$proc.val()) {
            $add.prop('disabled', true);
            return;
        }
        if (!lastDetail) {
            $add.prop('disabled', true);
            return;
        }
        var priceOk = (Number(lastDetail.price) !== 0) || lastDetail.usp === true;
        if (!priceOk) {
            $add.prop('disabled', true);
            return;
        }
        if (lastDetail.is_stratification && $strat.find('option').length > 1) {
            var $stCol = $strat.closest('.p360-tp-stratification-field').first();
            if (!$stCol.hasClass('d-none') && !$strat.val()) {
                $add.prop('disabled', true);
                return;
            }
        }
        if (lastDetail.is_implant) {
            var $impCol = $impl.closest('.p360-tp-implant-field').first();
            if (!$impCol.hasClass('d-none') && !$impl.val()) {
                $add.prop('disabled', true);
                return;
            }
            if (!$impCol.hasClass('d-none') && !$qty.prop('readonly') && String($qty.val()).trim() === '') {
                $add.prop('disabled', true);
                return;
            }
        }
        if (lastDetail.usp === true) {
            var u = parseFloat(String($u100.val() || '').replace(/,/g, ''));
            if (isNaN(u) || u <= 0) {
                $add.prop('disabled', true);
                return;
            }
        }
        $add.prop('disabled', false);
    }

    function resetProcedureDependents() {
        lastDetail = null;
        $proc.html('<option value="">Select procedure</option>');
        $strat.html('<option value="">Select stratification</option>');
        $impl.html('<option value="">Select implant</option>');
        $qty.val('').prop('readonly', true).removeAttr('max');
        $qtyErr.empty();
        $units.val('').prop('readonly', false);
        $u100.val('');
        $ichi.val('');
        $('.p360-tp-stratification-field').addClass('d-none');
        $('.p360-tp-implant-field').addClass('d-none');
        $('.p360-tp-u100-field').addClass('d-none');
        $add.prop('disabled', true);
        tpBindSelect2($proc);
        tpBindSelect2($strat);
        tpBindSelect2($impl);
    }

    $spec.on('change', function () {
        var id = $(this).val();
        resetProcedureDependents();
        if (!id) {
            return;
        }
        showLoader();
        $.ajax({
            url: tp.procedures,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf },
            data: { id: id, _token: csrf },
            success: function (response) {
                hideLoader();
                if (response && response.html) {
                    $proc.html(response.html);
                    tpBindSelect2($proc);
                }
                $add.prop('disabled', true);
            },
            error: function () {
                hideLoader();
                notifyTp('Unable to load procedures.');
            }
        });
    });

    $proc.on('change', function () {
        var id = $(this).val();
        $strat.html('<option value="">Select stratification</option>');
        $impl.html('<option value="">Select implant</option>');
        tpBindSelect2($strat);
        tpBindSelect2($impl);
        $qty.val('').prop('readonly', true).removeAttr('max');
        $qtyErr.empty();
        $units.val('').prop('readonly', false);
        $u100.val('');
        $ichi.val('');
        $('.p360-tp-stratification-field').addClass('d-none');
        $('.p360-tp-implant-field').addClass('d-none');
        $('.p360-tp-u100-field').addClass('d-none');
        lastDetail = null;
        $add.prop('disabled', true);

        if (!id) {
            return;
        }

        showLoader();
        $.ajax({
            url: tp.procedureDetail,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf },
            data: { id: id, _token: csrf },
            success: function (response) {
                hideLoader();
                if (!response || response.success !== true) {
                    notifyTp((response && response.message) || 'Procedure not found.');
                    return;
                }
                lastDetail = response;
                $ichi.val(response.icd_code || '');
                $units.val(response.no_of_days != null ? String(response.no_of_days) : '');
                if (response.is_read_only === true) {
                    $units.prop('readonly', true);
                } else {
                    $units.prop('readonly', false);
                }

                if (response.usp === true) {
                    $('.p360-tp-u100-field').removeClass('d-none');
                } else {
                    $('.p360-tp-u100-field').addClass('d-none');
                }

                if (response.is_stratification) {
                    $strat.html(response.stratification_options || '<option value="">Select stratification</option>');
                    $('.p360-tp-stratification-field').removeClass('d-none');
                } else {
                    $strat.html('<option value="">Select stratification</option>');
                    $('.p360-tp-stratification-field').addClass('d-none');
                }

                if (response.is_implant) {
                    $impl.html(response.implants_options || '<option value="">Select implant</option>');
                    $('.p360-tp-implant-field').removeClass('d-none');
                } else {
                    $impl.html('<option value="">Select implant</option>');
                    $('.p360-tp-implant-field').addClass('d-none');
                }

                tpBindSelect2($strat);
                tpBindSelect2($impl);

                updateAddButtonState();
            },
            error: function () {
                hideLoader();
                notifyTp('Unable to load procedure details.');
            }
        });
    });

    $impl.on('change', function () {
        var id = $(this).val();
        $qtyErr.empty();
        if (!id) {
            $qty.val('').prop('readonly', true).removeAttr('max');
            updateAddButtonState();
            return;
        }
        showLoader();
        $.ajax({
            url: tp.implantDetail,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf },
            data: { id: id, _token: csrf },
            success: function (response) {
                hideLoader();
                if (!response || response.success !== true) {
                    notifyTp((response && response.message) || 'Implant not found.');
                    return;
                }
                $qty.val(response.qty != null ? String(response.qty) : '1');
                if (response.max != null) {
                    $qty.attr('max', String(response.max));
                } else {
                    $qty.removeAttr('max');
                }
                if (response.is_read_only === true) {
                    $qty.prop('readonly', true);
                } else {
                    $qty.prop('readonly', false);
                }
                updateAddButtonState();
            },
            error: function () {
                hideLoader();
                notifyTp('Unable to load implant details.');
            }
        });
    });

    $qty.on('input', function () {
        var q = $(this).val();
        var max = $(this).attr('max');
        $qtyErr.empty();
        if (!isNaN(parseFloat(q)) && !isNaN(parseFloat(max)) && parseFloat(q) > parseFloat(max)) {
            $qtyErr.html('<span>You cannot add more than ' + max + ' qty.</span>');
            $add.prop('disabled', true);
            return;
        }
        updateAddButtonState();
    });

    $strat.on('change', function () {
        var id = $(this).val();
        if (!id) {
            updateAddButtonState();
            return;
        }
        showLoader();
        $.ajax({
            url: tp.stratificationDetail,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf },
            data: { id: id, _token: csrf },
            success: function (response) {
                hideLoader();
                if (!response || response.success !== true) {
                    notifyTp((response && response.message) || 'Stratification not found.');
                    return;
                }
                updateAddButtonState();
            },
            error: function () {
                hideLoader();
                notifyTp('Unable to load stratification details.');
            }
        });
    });

    $u100.on('input change', function () {
        updateAddButtonState();
    });

    $units.on('input change', function () {
        updateAddButtonState();
    });

    $add.on('click', function () {
        if ($add.prop('disabled')) {
            return;
        }
        var specV = ($spec.find('option:selected').text() || '').trim();
        var procV = ($proc.find('option:selected').text() || '').trim();
        var impV = '';
        var $impCol = $impl.closest('.p360-tp-implant-field').first();
        if ($impCol.length && !$impCol.hasClass('d-none') && $impl.val()) {
            impV = $impl.find('option:selected').text();
        }
        var qtyV = '';
        if ($impCol.length && !$impCol.hasClass('d-none') && $impl.val()) {
            qtyV = String($qty.val() || '');
        }
        var stratV = '';
        var $stCol = $strat.closest('.p360-tp-stratification-field').first();
        if ($stCol.length && !$stCol.hasClass('d-none') && $strat.val()) {
            stratV = $strat.find('option:selected').text();
        }
        var unitsV = String($units.val() || '');
        var amtVal = '';
        if ($u100.closest('.p360-tp-u100-field').length && !$u100.closest('.p360-tp-u100-field').hasClass('d-none')) {
            amtVal = $u100.val();
        } else if (lastDetail && lastDetail.price != null) {
            amtVal = String(lastDetail.price);
        }
        var ichiV = String($ichi.val() || '');

        var body = doc.getElementById('p360TreatmentPlanTableBody');
        if (!body) {
            return;
        }
        var tr = doc.createElement('tr');
        tr.innerHTML =
            '<td class="p360-tp-col-no"></td>' +
            '<td>' + escHtml(specV) + '</td>' +
            '<td>' + procedureCellHtml(procV) + '</td>' +
            '<td>' + escHtml(impV) + '</td>' +
            '<td>' + escHtml(qtyV) + '</td>' +
            '<td>' + escHtml(stratV) + '</td>' +
            '<td>' + escHtml(unitsV) + '</td>' +
            '<td>' + escHtml(formatInr(amtVal)) + '</td>' +
            '<td>' + escHtml(ichiV) + '</td>' +
            '<td class="text-center">' +
            '<button type="button" class="p360-tp-remove-row" title="Remove line" aria-label="Remove line">⋮</button>' +
            '</td>';
        body.appendChild(tr);
        renumberTreatmentPlanRows();
    });

    $modal.on('hidden.bs.modal', function () {
        tpDestroySelect2($spec);
        tpDestroySelect2($proc);
        tpDestroySelect2($strat);
        tpDestroySelect2($impl);
    });

    $modal.on('shown.bs.modal', function () {
        tpBindAllSelects();
        var opts = $spec.find('option[value!=""]');
        if (opts.length === 1 && !$spec.val()) {
            $spec.val(opts.first().val()).trigger('change');
        } else if ($spec.val()) {
            $spec.trigger('change');
        }
    });

    $(function () {
        if ($modal.length) {
            tpBindAllSelects();
        }
        var body = doc.getElementById('p360TreatmentPlanTableBody');
        if (body) {
            bindTreatmentPlanTable(body);
            renumberTreatmentPlanRows();
        }
    });
}(window, document));
