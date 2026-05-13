@php
    $preIds = ($data && $data->mandatory_documents_pre_auth)
        ? array_filter(explode(',', $data->mandatory_documents_pre_auth))
        : [];
    $claimIds = ($data && $data->mandatory_documents_claim_processing)
        ? array_filter(explode(',', $data->mandatory_documents_claim_processing))
        : [];
    $sghsId = (int) ($sghsSchemeTypeId ?? 1);
    $schemeTypeVal = (int) old('scheme_type_id', @$data->scheme_type_id);
    $sghsShow = $schemeTypeVal === $sghsId;
@endphp
<div class="modal-header bg-primary ">
    <h5 class="modal-title text-white">{{ !$id ? 'Add' : 'Edit'}} procedure</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="POST" id="savedata" enctype="multipart/form-data">
    <div class="modal-body">
        <input type="hidden" id="id" name="id" value="{{ $id }}">

        <div class="accordion" id="procFormAcc">
            <div class="accordion-item">
                <h2 class="accordion-header"><button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#procSec1">Classification &amp; codes</button></h2>
                <div id="procSec1" class="accordion-collapse collapse show" data-bs-parent="#procFormAcc">
                    <div class="accordion-body row g-2">
                        <div class="col-md-4">
                            <label class="form-label">Scheme type</label>
                            <select name="scheme_type_id" id="scheme_type_id" class="form-select select2">
                                <option value="">—</option>
                                @foreach($schemeTypes as $stid => $stname)
                                    <option value="{{ $stid }}" @selected(old('scheme_type_id', @$data->scheme_type_id) == $stid)>{{ $stname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 sghs-field {{ $sghsShow ? '' : 'd-none' }}">
                            <label class="form-label">Procedure category</label>
                            <select name="procedure_category_id" id="procedure_category_id" class="form-select select2">
                                <option value="">—</option>
                                @foreach($categories as $cid => $cname)
                                    <option value="{{ $cid }}" @selected(@$data->procedure_category_id == $cid)>{{ $cname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Speciality</label>
                            <select name="speciality_id" id="speciality_id" class="form-select select2">
                                <option value="">—</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Package</label>
                            <select name="package_id" class="form-select select2">
                                <option value="">—</option>
                                @foreach($packages as $pid => $pname)
                                    <option value="{{ $pid }}" @selected(@$data->package_id == $pid)>{{ $pname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Procedure code 1</label>
                            <input type="text" name="procedure_code_1" class="form-control" value="{{ old('procedure_code_1', @$data->procedure_code_1) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Procedure code 2</label>
                            <input type="text" name="procedure_code_2" class="form-control" value="{{ old('procedure_code_2', @$data->procedure_code_2) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Is multiple procedures</label>
                            <select name="is_multiple_procedure" class="form-select">
                                <option value="">—</option>
                                <option value="Yes" @selected(@$data->is_multiple_procedure === 'Yes')>Yes</option>
                                <option value="No" @selected(@$data->is_multiple_procedure === 'No')>No</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ICD / ICHI code</label>
                            <input type="text" name="icd_code" class="form-control" value="{{ old('icd_code', @$data->icd_code) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Procedure type</label>
                            <input type="text" name="procedure_type" class="form-control" value="{{ old('procedure_type', @$data->procedure_type) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Procedure label</label>
                            <select name="procedure_label" class="form-select">
                                <option value="">—</option>
                                <option value="Regular Procedure" @selected(@$data->procedure_label === 'Regular Procedure')>Regular Procedure</option>
                                <option value="Add-on Procedure" @selected(@$data->procedure_label === 'Add-on Procedure')>Add-on Procedure</option>
                                <option value="Follow-up Procedure" @selected(@$data->procedure_label === 'Follow-up Procedure')>Follow-up Procedure</option>
                                <option value="Stand Alone Procedure" @selected(@$data->procedure_label === 'Stand Alone Procedure')>Stand Alone Procedure</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Procedure name</label>
                            <textarea name="procedure_name" class="form-control" rows="2">{{ old('procedure_name', @$data->procedure_name) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#procSec2">Pricing &amp; stratification</button></h2>
                <div id="procSec2" class="accordion-collapse collapse" data-bs-parent="#procFormAcc">
                    <div class="accordion-body row g-2">
                        <div class="col-md-4">
                            <label class="form-label">Price</label>
                            <input type="number" step="0.01" min="0" name="price" class="form-control" value="{{ old('price', @$data->price ?? 0) }}">
                        </div>
                        <div class="col-md-4 sghs-field {{ $sghsShow ? '' : 'd-none' }}">
                            <label class="form-label">Non NABH price</label>
                            <input type="number" step="0.01" min="0" name="non_nabh_price" id="non_nabh_price" class="form-control" value="{{ old('non_nabh_price', @$data->non_nabh_price) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <input type="text" name="status" class="form-control" value="{{ old('status', @$data->status ?? 'active') }}" placeholder="active">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Stratification criteria</label>
                            <select name="stratification_criteria" class="form-select" id="stratification_criteria">
                                <option value="">—</option>
                                <option value="Yes" @selected(@$data->stratification_criteria === 'Yes')>Yes</option>
                                <option value="No" @selected(@$data->stratification_criteria === 'No')>No</option>
                            </select>
                        </div>
                        <div class="col-md-4 stratification-field {{ (@$data->stratification_criteria ?? '') === 'Yes' ? '' : 'd-none' }}">
                            <label class="form-label">No. of stratification</label>
                            <input type="text" name="no_of_stratification" id="no_of_stratification" class="form-control" value="{{ old('no_of_stratification', @$data->no_of_stratification) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Implants / high-end consumables</label>
                            <select name="implants_high_end_consumables" class="form-select" id="implants_high_end_consumables">
                                <option value="">—</option>
                                <option value="Yes" @selected(@$data->implants_high_end_consumables === 'Yes')>Yes</option>
                                <option value="No" @selected(@$data->implants_high_end_consumables === 'No')>No</option>
                            </select>
                        </div>
                        <div class="col-md-4 implant-field {{ (@$data->implants_high_end_consumables ?? '') === 'Yes' ? '' : 'd-none' }}">
                            <label class="form-label">More than one implant</label>
                            <input type="text" name="more_than_one_implant" id="more_than_one_implant" class="form-control" value="{{ old('more_than_one_implant', @$data->more_than_one_implant) }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#procSec3">Hospitals, LOS, documents</button></h2>
                <div id="procSec3" class="accordion-collapse collapse" data-bs-parent="#procFormAcc">
                    <div class="accordion-body row g-2">
                        <div class="col-md-4">
                            <label class="form-label">Special conditions</label>
                            <select name="special_conditions" class="form-select">
                                <option value="">—</option>
                                <option value="Yes" @selected(@$data->special_conditions === 'Yes')>Yes</option>
                                <option value="No" @selected(@$data->special_conditions === 'No')>No</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Reservation public hospitals</label>
                            <select name="reservation_public_hospitals" class="form-select">
                                <option value="">—</option>
                                <option value="Yes" @selected(@$data->reservation_public_hospitals === 'Yes')>Yes</option>
                                <option value="No" @selected(@$data->reservation_public_hospitals === 'No')>No</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Reservation tertiary hospitals</label>
                            <select name="reservation_tertiary_hospitals" class="form-select">
                                <option value="">—</option>
                                <option value="Yes" @selected(@$data->reservation_tertiary_hospitals === 'Yes')>Yes</option>
                                <option value="No" @selected(@$data->reservation_tertiary_hospitals === 'No')>No</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Level of care</label>
                            <select name="level_of_care" class="form-select">
                                <option value="NA" @selected(@$data->level_of_care === 'NA')>NA</option>
                                <option value="Secondary" @selected(@$data->level_of_care === 'Secondary')>Secondary</option>
                                <option value="Tertiary" @selected(@$data->level_of_care === 'Tertiary')>Tertiary</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">LOS</label>
                            <input type="text" name="los" class="form-control" value="{{ old('los', @$data->los) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Auto approved</label>
                            <select name="auto_approved" class="form-select">
                                <option value="No" @selected((@$data->auto_approved ?? 'No') === 'No')>No</option>
                                <option value="Yes" @selected(@$data->auto_approved === 'Yes')>Yes</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Mandatory documents — pre auth (investigations)</label>
                            <select name="mandatory_documents_pre_auth[]" class="form-select select2" multiple data-placeholder="Select investigations">
                                @foreach($investigations as $inv)
                                    <option value="{{ $inv->id }}" @selected(in_array((string) $inv->id, $preIds, true))>{{ $inv->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Mandatory documents — claim processing</label>
                            <select name="mandatory_documents_claim_processing[]" class="form-select select2" multiple>
                                @foreach($investigations as $inv)
                                    <option value="{{ $inv->id }}" @selected(in_array((string) $inv->id, $claimIds, true))>{{ $inv->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#procSec4">Rules &amp; flags</button></h2>
                <div id="procSec4" class="accordion-collapse collapse" data-bs-parent="#procFormAcc">
                    <div class="accordion-body row g-2">
                        <div class="col-md-4">
                            <label class="form-label">Special condition pop-up</label>
                            <select name="special_condition_pop_up" class="form-select" id="special_condition_pop_up">
                                <option value="No" @selected((@$data->special_condition_pop_up ?? 'No') === 'No')>No</option>
                                <option value="Yes" @selected(@$data->special_condition_pop_up === 'Yes')>Yes</option>
                            </select>
                        </div>
                        <div class="col-md-8 popup-field {{ (@$data->special_condition_pop_up ?? 'No') === 'Yes' ? '' : 'd-none' }}">
                            <label class="form-label">Pop-up message</label>
                            <textarea name="special_condition_pop_up_message" class="form-control" rows="2">{{ old('special_condition_pop_up_message', @$data->special_condition_pop_up_message) }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Special conditions rule</label>
                            <select name="special_conditions_rule" class="form-select" id="special_conditions_rule">
                                <option value="No" @selected((@$data->special_conditions_rule ?? 'No') === 'No')>No</option>
                                <option value="Yes" @selected(@$data->special_conditions_rule === 'Yes')>Yes</option>
                            </select>
                        </div>
                        <div class="col-md-8 rule-field {{ (@$data->special_conditions_rule ?? 'No') === 'Yes' ? '' : 'd-none' }}">
                            <label class="form-label">Rule message</label>
                            <textarea name="special_conditions_rule_message" class="form-control" rows="2">{{ old('special_conditions_rule_message', @$data->special_conditions_rule_message) }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Enhancement applicable</label>
                            <select name="enhancement_applicable" class="form-select">
                                <option value="No" @selected((@$data->enhancement_applicable ?? 'No') === 'No')>No</option>
                                <option value="Yes" @selected(@$data->enhancement_applicable === 'Yes')>Yes</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Medical or surgical</label>
                            <select name="medical_or_surgical" class="form-select">
                                <option value="Medical" @selected((@$data->medical_or_surgical ?? 'Medical') === 'Medical')>Medical</option>
                                <option value="Surgical" @selected(@$data->medical_or_surgical === 'Surgical')>Surgical</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Day care procedure</label>
                            <select name="day_care_procedure" class="form-select">
                                <option value="No" @selected((@$data->day_care_procedure ?? 'No') === 'No')>No</option>
                                <option value="Yes" @selected(@$data->day_care_procedure === 'Yes')>Yes</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
<script>
(function ($) {
    const SGHS_ID = {{ (int) $sghsSchemeTypeId }};
    const getSpecUrl = @json(route('admin.procedures.get-specialities'));
    const csrf = $('meta[name="csrf-token"]').attr('content');
    var tpSpecFirstLoad = true;

    function esc(s) {
        return $('<div/>').text(s == null ? '' : String(s)).html();
    }
    function toggleSghs() {
        var show = String($('#scheme_type_id').val()) === String(SGHS_ID);
        $('.sghs-field').toggleClass('d-none', !show);
    }
    function toggleStrat() {
        var v = $('#stratification_criteria').val();
        $('.stratification-field').toggleClass('d-none', v !== 'Yes');
    }
    function toggleImpl() {
        var v = $('#implants_high_end_consumables').val();
        $('.implant-field').toggleClass('d-none', v !== 'Yes');
    }
    function togglePop() {
        var v = $('#special_condition_pop_up').val();
        $('.popup-field').toggleClass('d-none', v !== 'Yes');
    }
    function toggleRule() {
        var v = $('#special_conditions_rule').val();
        $('.rule-field').toggleClass('d-none', v !== 'Yes');
    }
    function refreshSpecialities() {
        var schemeTypeId = $('#scheme_type_id').val();
        var prev = $('#speciality_id').val();
        if (tpSpecFirstLoad) {
            var init = @json(old('speciality_id', optional($data)->speciality_id));
            if ((!prev || prev === '') && init !== null && init !== '') {
                prev = String(init);
            }
            tpSpecFirstLoad = false;
        }
        $.ajax({
            url: getSpecUrl,
            method: 'POST',
            data: { _token: csrf, scheme_type_id: schemeTypeId },
            success: function (res) {
                var html = '<option value="">—</option>';
                $.each(res.specialities || [], function (_, s) {
                    var sel = (String(prev) === String(s.id)) ? ' selected' : '';
                    html += '<option value="' + s.id + '"' + sel + '>' + esc(s.name) + '</option>';
                });
                var $el = $('#speciality_id');
                if ($el.data('select2')) {
                    $el.select2('destroy');
                }
                $el.html(html);
                if (typeof loadSelect2 === 'function') {
                    loadSelect2();
                }
            }
        });
    }

    $('#stratification_criteria').on('change', toggleStrat);
    $('#implants_high_end_consumables').on('change', toggleImpl);
    $('#special_condition_pop_up').on('change', togglePop);
    $('#special_conditions_rule').on('change', toggleRule);
    $('#scheme_type_id').on('change', function () {
        toggleSghs();
        refreshSpecialities();
    });

    toggleStrat();
    toggleImpl();
    togglePop();
    toggleRule();
    toggleSghs();
    $('#scheme_type_id').trigger('change');
})(jQuery);
</script>
