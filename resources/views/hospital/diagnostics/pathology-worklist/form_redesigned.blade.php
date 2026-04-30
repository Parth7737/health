<!-- ═══════════════════════════════════════════════════════════════
     RESULT ENTRY MODAL - Pixel Perfect Design
     ═══════════════════════════════════════════════════════════════ -->

<div class="modal-header border-0 pb-3" style="background: linear-gradient(135deg, #1565c0 0%, #1848b8 100%); color: white;">
    <div style="flex: 1;">
        <h5 class="modal-title" style="font-size: 16px; font-weight: 700; margin-bottom: 2px;">📋 Result Entry</h5>
        <small style="opacity: 0.85;">Complete pathology test results for patient report</small>
    </div>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="saveReportForm">
    @csrf
    <input type="hidden" name="item_id" value="{{ $item->id }}">
    
    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
        <!-- PATIENT INFO STRIP -->
        <div style="background: #f5f7fa; border-radius: 8px; padding: 14px; margin-bottom: 18px; border-left: 4px solid #1565c0;">
            <div class="row g-3" style="font-size: 12.5px;">
                <div class="col-auto">
                    <span style="color: #666; font-weight: 600;">Order No:</span>
                    <strong style="color: #1565c0;">{{ $item->order->order_no ?? '—' }}</strong>
                </div>
                <div class="col-auto" style="border-left: 1px solid #ddd; padding-left: 14px;">
                    <span style="color: #666; font-weight: 600;">Patient:</span>
                    <strong>{{ $item->order->patient->name ?? '—' }}</strong>
                </div>
                <div class="col-auto" style="border-left: 1px solid #ddd; padding-left: 14px;">
                    <span style="color: #666; font-weight: 600;">Age/Gender:</span>
                    <strong>{{ $item->order->patient->age ?? '—' }}/{{ strtoupper(substr($item->order->patient->gender ?? '-', 0, 1)) }}</strong>
                </div>
                <div class="col-auto ms-auto" style="border-left: 1px solid #ddd; padding-left: 14px; text-align: right;">
                    <span style="color: #666; font-weight: 600;">Status:</span>
                    <span class="badge" style="background: #4caf50; color: white; padding: 4px 10px; font-size: 10px; font-weight: 600;">{{ ucwords(str_replace('_', ' ', $item->status)) }}</span>
                </div>
            </div>
        </div>

        <!-- PARAMETERS TABLE -->
        <div style="margin-bottom: 18px;">
            <div style="display: flex; align-items: center; margin-bottom: 12px; gap: 8px;">
                <span style="font-size: 13px; font-weight: 700; color: #1565c0;">🧪 Test Parameters & Results</span>
                <span style="font-size: 11px; color: #999; background: #f0f0f0; padding: 3px 8px; border-radius: 4px;">{{ $item->parameters->count() }} items</span>
            </div>

            <div class="table-responsive" style="border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;">
                <table class="table mb-0" style="font-size: 12px;">
                    <thead>
                        <tr style="background: linear-gradient(180deg, #f5f7fa 0%, #eef1f5 100%); border-bottom: 2px solid #1565c0;">
                            <th style="padding: 10px 12px; font-weight: 700; color: #1565c0; text-align: left;">Parameter</th>
                            <th style="padding: 10px 12px; font-weight: 700; color: #1565c0; text-align: center; width: 70px;">Unit</th>
                            <th style="padding: 10px 12px; font-weight: 700; color: #1565c0; text-align: center; width: 120px;">Normal Range</th>
                            <th style="padding: 10px 12px; font-weight: 700; color: #1565c0; text-align: center; width: 100px;">Result</th>
                            <th style="padding: 10px 12px; font-weight: 700; color: #1565c0; text-align: center; width: 110px;">Flag</th>
                            <th style="padding: 10px 12px; font-weight: 700; color: #1565c0; text-align: left;">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($item->parameters as $parameter)
                            @php
                                $paramDef = $parameter->parameterable;
                                $minVal = $paramDef?->min_value ?? null;
                                $maxVal = $paramDef?->max_value ?? null;
                                $critLow = $paramDef?->critical_low ?? null;
                                $critHigh = $paramDef?->critical_high ?? null;
                                $unitName = $paramDef?->unit?->name ?? '-';
                                $rangeText = $parameter->normal_range ?? '-';
                                if ($minVal !== null && $maxVal !== null) {
                                    $rangeText = number_format($minVal, 2) . ' - ' . number_format($maxVal, 2);
                                }
                            @endphp
                            <tr style="border-bottom: 1px solid #e8e8e8; transition: background 0.2s;">
                                <td style="padding: 11px 12px; font-weight: 600; color: #333;">
                                    {{ $parameter->parameter_name }}
                                    <input type="hidden" class="param-min" value="{{ $minVal }}">
                                    <input type="hidden" class="param-max" value="{{ $maxVal }}">
                                    <input type="hidden" class="param-crit-low" value="{{ $critLow }}">
                                    <input type="hidden" class="param-crit-high" value="{{ $critHigh }}">
                                </td>
                                <td style="padding: 11px 12px; text-align: center; color: #666;">
                                    <span style="background: #f0f0f0; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">{{ $unitName }}</span>
                                </td>
                                <td style="padding: 11px 12px; text-align: center; color: #666; font-size: 11px;">
                                    <span style="background: #fafafa; padding: 6px 8px; border-radius: 4px; display: inline-block;">{{ $rangeText }}</span>
                                </td>
                                <td style="padding: 11px 12px; text-align: center;">
                                    <input 
                                        type="number" 
                                        class="form-control form-control-sm result-value" 
                                        name="result_value[{{ $parameter->id }}]" 
                                        value="{{ $parameter->result_value }}"
                                        step="0.01"
                                        placeholder="0.00"
                                        data-param-id="{{ $parameter->id }}"
                                        style="border: 1.5px solid #ddd; border-radius: 4px; padding: 6px 8px; font-size: 11px; font-weight: 600; text-align: center;">
                                </td>
                                <td style="padding: 11px 12px; text-align: center;">
                                    <div class="result-flag-display" data-param-id="{{ $parameter->id }}" style="min-height: 26px; display: flex; align-items: center; justify-content: center;">
                                        @if($parameter->result_flag)
                                            @php
                                                $flagConfig = [
                                                    'normal' => ['✓ Normal', '#4caf50', '#e8f5e9'],
                                                    'low' => ['↓ Low', '#ff9800', '#fff3e0'],
                                                    'high' => ['↑ High', '#ff9800', '#fff3e0'],
                                                    'critical_low' => ['↓↓ Critical Low', '#f44336', '#ffebee'],
                                                    'critical_high' => ['↑↑ Critical High', '#f44336', '#ffebee'],
                                                ];
                                                $config = $flagConfig[$parameter->result_flag] ?? ['—', '#999', '#f5f5f5'];
                                            @endphp
                                            <span style="background: {{ $config[2] }}; color: {{ $config[1] }}; padding: 5px 10px; border-radius: 4px; font-weight: 700; font-size: 10.5px; border: 1px solid {{ $config[1] }};">{{ $config[0] }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td style="padding: 11px 12px;">
                                    <input 
                                        type="text" 
                                        class="form-control form-control-sm" 
                                        name="remarks[{{ $parameter->id }}]" 
                                        value="{{ $parameter->remarks }}" 
                                        placeholder="Add remarks..."
                                        style="border: 1.5px solid #e0e0e0; border-radius: 4px; padding: 6px 8px; font-size: 11px;">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 30px; text-align: center; color: #999;">
                                    <div style="font-size: 14px; font-weight: 600;">No parameters configured</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- COMMENTS SECTION -->
        <div class="row g-3">
            <div class="col-md-6">
                <label style="font-size: 12px; font-weight: 700; color: #333; margin-bottom: 8px; display: block;">Technician Remarks</label>
                <textarea 
                    class="form-control" 
                    name="technician_remarks" 
                    rows="3" 
                    placeholder="Any remarks on sample quality or test..."
                    style="border: 1.5px solid #e0e0e0; border-radius: 4px; padding: 10px; font-size: 11.5px; font-family: 'Inter', sans-serif; resize: vertical;"></textarea>
            </div>
            <div class="col-md-6">
                <label style="font-size: 12px; font-weight: 700; color: #333; margin-bottom: 8px; display: block;">Pathologist Comment</label>
                <textarea 
                    class="form-control" 
                    name="pathologist_comment" 
                    rows="3" 
                    placeholder="Clinical interpretation, findings..."
                    style="border: 1.5px solid #e0e0e0; border-radius: 4px; padding: 10px; font-size: 11.5px; font-family: 'Inter', sans-serif; resize: vertical;"></textarea>
            </div>
        </div>
    </div>

    <!-- FOOTER ACTIONS -->
    <div class="modal-footer" style="background: #f9fafb; border-top: 1px solid #e0e0e0; gap: 8px; padding: 12px 18px;">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border: 1.5px solid #ddd; font-size: 11.5px; font-weight: 600; padding: 7px 18px; border-radius: 4px; background: white; color: #666;">
            ❌ Cancel
        </button>
        <button type="submit" class="btn" style="border: 1.5px solid #ff9800; background: #ff9800; color: white; font-size: 11.5px; font-weight: 600; padding: 7px 18px; border-radius: 4px;">
            💾 Save Draft
        </button>
        <button type="button" class="btn" style="border: 1.5px solid #2196f3; background: #2196f3; color: white; font-size: 11.5px; font-weight: 600; padding: 7px 18px; border-radius: 4px;">
            ✅ Send for Verification
        </button>
        <button type="button" class="btn" style="border: 1.5px solid #4caf50; background: #4caf50; color: white; font-size: 11.5px; font-weight: 600; padding: 7px 18px; border-radius: 4px;">
            🖨️ Finalize & Dispatch Report
        </button>
    </div>
</form>

<style>
    .modal-body {
        background: white;
        padding: 18px;
    }
    
    .result-value:focus {
        border-color: #1565c0 !important;
        box-shadow: 0 0 0 3px rgba(21, 101, 192, 0.1) !important;
    }
    
    .form-control-sm:focus {
        border-color: #1565c0 !important;
        box-shadow: 0 0 0 2px rgba(21, 101, 192, 0.1) !important;
    }
    
    table tbody tr:hover {
        background: #f9fafb !important;
    }
</style>

<script>
(function initResultEntry() {
    const form = document.getElementById('saveReportForm');
    if (!form) return;

    const resultInputs = form.querySelectorAll('.result-value');

    function generateFlag(value, minVal, maxVal, critLow, critHigh) {
        if (!value || value === '') return null;
        const val = parseFloat(value);

        const flagConfig = {
            'normal': { label: '✓ Normal', color: '#4caf50', bg: '#e8f5e9' },
            'low': { label: '↓ Low', color: '#ff9800', bg: '#fff3e0' },
            'high': { label: '↑ High', color: '#ff9800', bg: '#fff3e0' },
            'critical_low': { label: '↓↓ Critical Low', color: '#f44336', bg: '#ffebee' },
            'critical_high': { label: '↑↑ Critical High', color: '#f44336', bg: '#ffebee' },
        };

        if (critLow !== null && val < parseFloat(critLow)) {
            return { flag: 'critical_low', ...flagConfig.critical_low };
        }
        if (critHigh !== null && val > parseFloat(critHigh)) {
            return { flag: 'critical_high', ...flagConfig.critical_high };
        }

        if (minVal !== null && maxVal !== null) {
            const min = parseFloat(minVal);
            const max = parseFloat(maxVal);
            if (val < min) return { flag: 'low', ...flagConfig.low };
            if (val > max) return { flag: 'high', ...flagConfig.high };
            return { flag: 'normal', ...flagConfig.normal };
        }

        if (minVal !== null && val < parseFloat(minVal)) {
            return { flag: 'low', ...flagConfig.low };
        }
        if (maxVal !== null && val > parseFloat(maxVal)) {
            return { flag: 'high', ...flagConfig.high };
        }

        return { flag: 'normal', ...flagConfig.normal };
    }

    resultInputs.forEach(input => {
        input.addEventListener('input', function() {
            const paramId = this.dataset.paramId;
            const value = this.value;
            const row = this.closest('tr');
            const minVal = row.querySelector('.param-min')?.value;
            const maxVal = row.querySelector('.param-max')?.value;
            const critLow = row.querySelector('.param-crit-low')?.value;
            const critHigh = row.querySelector('.param-crit-high')?.value;

            const flagDisplay = form.querySelector(`.result-flag-display[data-param-id="${paramId}"]`);
            if (!flagDisplay) return;

            const flagInfo = generateFlag(value, minVal, maxVal, critLow, critHigh);
            if (flagInfo) {
                flagDisplay.innerHTML = `<span style="background: ${flagInfo.bg}; color: ${flagInfo.color}; padding: 5px 10px; border-radius: 4px; font-weight: 700; font-size: 10.5px; border: 1px solid ${flagInfo.color};">${flagInfo.label}</span>`;
            } else {
                flagDisplay.innerHTML = '';
            }
        });
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Form submitted with values');
        // Backend integration here
    });
})();
</script>
