@php
    $staffCategories = \App\Models\StaffStrength::orderBy('name')->get();
    $savedStrength   = [];
    if ($hospital && $hospital->onboarding_meta) {
        $meta = is_array($hospital->onboarding_meta) ? $hospital->onboarding_meta : [];
        $savedStrength = $meta['staff_strength'] ?? [];
    }
@endphp

<div id="staffStrengthCard">
    <div class="eo-card-hdr">
        <h3 class="eo-card-title"><i class="fas fa-user-md"></i> Medical Staff (Sanctioned vs In Position)</h3>
    </div>
    <div class="eo-card-body">
        @if($staffCategories->isEmpty())
            <p class="text-muted mb-0">No staff strength categories configured</p>
        @else
            <div class="table-responsive">
                <table class="table eo-staff-table mb-0">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Sanctioned</th>
                            <th>In Position</th>
                            <th>Vacancy</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staffCategories as $cat)
                        @php
                            $row       = $savedStrength[$cat->id] ?? [];
                            $sanctioned = $row['sanctioned']   ?? '';
                            $inPosition = $row['in_position']  ?? '';
                        @endphp
                        <tr>
                            <td class="eo-staff-label">{{ $cat->name }}</td>
                            <td>
                                <input type="number" min="0"
                                    class="form-control form-control-sm eo-staff-input sanc"
                                    name="staff_strength[{{ $cat->id }}][sanctioned]"
                                    value="{{ $sanctioned }}"
                                    placeholder="0"
                                    data-row="{{ $cat->id }}" />
                            </td>
                            <td>
                                <input type="number" min="0"
                                    class="form-control form-control-sm eo-staff-input inpos"
                                    name="staff_strength[{{ $cat->id }}][in_position]"
                                    value="{{ $inPosition }}"
                                    placeholder="0"
                                    data-row="{{ $cat->id }}" />
                            </td>
                            <td>
                                <span class="eo-vac-cell" id="vac_{{ $cat->id }}">
                                    @if($sanctioned !== '' && $inPosition !== '')
                                        {{ max(0, (int)$sanctioned - (int)$inPosition) }}
                                    @else
                                        —
                                    @endif
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<script>
(function () {
    function calcVac(input) {
        var rowId  = $(input).data('row');
        var tr     = $(input).closest('tr');
        var sanc   = parseInt(tr.find('.sanc').val())   || 0;
        var inpos  = parseInt(tr.find('.inpos').val())  || 0;
        var vac    = Math.max(0, sanc - inpos);
        var $cell  = $('#vac_' + rowId);

        if ($(input).closest('tr').find('.sanc').val() === '' && $(input).closest('tr').find('.inpos').val() === '') {
            $cell.text('—').removeClass('zero');
        } else {
            $cell.text(vac).toggleClass('zero', vac === 0);
        }
    }

    $(document).on('input', '#staffStrengthCard .sanc, #staffStrengthCard .inpos', function () {
        calcVac(this);
    });

    $(document).on('click', '#saveStaffStrengthBtn', function () {
        ldrshow();
        var formData = new FormData($('#staffStrengthForm')[0]);
        $.ajax({
            url: "{{ route('hospital.empanelmentRegistration.saveStaffStrength', [$uuid]) }}",
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                ldrhide();
                if (response.success) {
                    successMessage(response.message);
                }
            },
            error: function (xhr) {
                ldrhide();
                errorMessage('Something went wrong.');
            }
        });
    });
})();
</script>
