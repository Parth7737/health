<!-- ═══════════════════════════════════════════════════════════════
     RESULT ENTRY MODAL - Pixel Perfect Design
     ═══════════════════════════════════════════════════════════════ -->

<div class="modal-header border-0" style="background: white; padding: 16px 20px; border-bottom: 1px solid #e0e0e0;">
    <h5 class="modal-title" style="font-size: 16px; font-weight: 700; color: #333;">📋 Result Entry</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="saveReportForm">
    @csrf
    <input type="hidden" name="item_id" value="{{ $item->id }}">
    
    <div class="modal-body" style="max-height: 70vh; overflow-y: auto; background: #f9f9f9; padding: 18px;">
       
        <div class="patient-chip mb-16">
            <div class="patient-chip-avatar">{{ strtoupper(substr($item->order->patient->name ?? 'P', 0, 1)) }}</div>
            <div class="patient-chip-info"><div class="patient-chip-name">{{ $item->order->patient->name ?? '—' }}</div><div class="patient-chip-meta">Sample: {{ $item->order->order_no ?? '—' }} | {{ $item->order->diagnosticTest?->name ?? 'CBC' }} | Collected: {{ $item->created_at?->format('H:i') ?? '—' }}</div></div>
            <span class="badge badge-orange">{{ ucwords(str_replace('_', ' ', $item->status)) }}</span>
        </div>

        <div class="table-wrap">
            <table class="hims-table" id="resultTable">
                <thead><tr><th>Parameter</th><th>Result</th><th>Unit</th><th>Normal Range</th><th>Flag</th></tr></thead>
                @forelse($item->parameters as $key => $parameter)
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
                <tr id="resultRow_{{ $key }}">
                    <td>{{ $parameter->parameter_name }}
                        <input type="hidden" class="param-min" value="{{ $minVal }}">
                        <input type="hidden" class="param-max" value="{{ $maxVal }}">
                        <input type="hidden" class="param-crit-low" value="{{ $critLow }}">
                        <input type="hidden" class="param-crit-high" value="{{ $critHigh }}">
                    </td>
                    <td><input type="number" step="0.01" name="result_value[{{ $parameter->id }}]" value="{{ $parameter->result_value }}" class="form-control form-control-sm result-value" data-param-id="{{ $parameter->id }}" style="font-size:12px;padding:5px;width:100px">
                    </td>
                    <td>{{ $unitName }}</td>
                    <td>{{ $rangeText }}</td>
                    <td style="padding: 10px 14px; text-align: center;">
                        <div class="result-flag-display" data-param-id="{{ $parameter->id }}" style="min-height: 24px; display: flex; align-items: center; justify-content: center;">
                            @if($parameter->result_flag)
                                @php
                                    $flagConfig = [
                                        'normal' => ['Normal', 'pc-flag-badge pc-flag-green'],
                                        'low' => ['↓ Low', 'pc-flag-badge pc-flag-orange'],
                                        'high' => ['↑ High', 'pc-flag-badge pc-flag-orange'],
                                        'critical_low' => ['↓↓ Critical Low', 'pc-flag-badge pc-flag-red'],
                                        'critical_high' => ['↑↑ Critical High', 'pc-flag-badge pc-flag-red'],
                                    ];
                                    $config = $flagConfig[$parameter->result_flag] ?? ['—', 'text-muted fs-12'];
                                @endphp
                                <span class="{{ $config[1] }}">{{ $config[0] }}</span>
                            @else
                                <span class="text-muted fs-12">—</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align: center; padding: 20px; color: #999;">No parameters configured for this test</td></tr>    
                @endforelse                
            </table>
        </div>
        <div class="form-row cols-2 mt-12">
            <div class="form-group">
                <label class="form-label">Technician Remarks</label>
                <textarea class="form-control" name="technician_remarks" rows="2" placeholder="Any remarks on sample quality or test…">{{ $item->technician_remarks }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Pathologist Comment</label>
                <textarea class="form-control" name="pathologist_comment" rows="2" placeholder="Interpretive comments…">{{ $item->pathologist_comment }}</textarea>
            </div>
        </div>
    </div>

    <!-- FOOTER ACTION BAR -->
    <div class="modal-footer border-top" style="background: white; padding: 12px 16px; display: flex; gap: 8px; justify-content: space-between; align-items: center;">
        <!-- Left: print link (shown only when already completed) -->
        <div id="pcPrintArea">
            @if($item->status === 'completed')
                <a href="{{ route('hospital.pathology.worklist.print', $item->id) }}" target="_blank"
                   style="background:#1565c0; color:white; font-size:13px; font-weight:600; border:none; padding:7px 16px; border-radius:4px; cursor:pointer; text-decoration:none; display:inline-block;">
                    🖨 Print / View Report
                </a>
            @endif
        </div>
        <!-- Right: action buttons -->
        <div style="display:flex; gap:8px;">
            <button type="button" class="btn" data-bs-dismiss="modal" style="background: #e8e8e8; color: #333; font-size: 13px; font-weight: 600; border: none; padding: 7px 16px; border-radius: 4px; cursor: pointer;">
                Cancel
            </button>
            <button type="button" class="btn" id="pcDraftBtn" style="background: #ff9800; color: white; font-size: 13px; font-weight: 600; border: none; padding: 7px 16px; border-radius: 4px; cursor: pointer;">
                💾 Save Draft
            </button>
            <button type="button" class="btn" id="pcFinalBtn" style="background: #4caf50; color: white; font-size: 13px; font-weight: 600; border: none; padding: 7px 16px; border-radius: 4px; cursor: pointer;">
                ✓ Finalize & Dispatch
            </button>
        </div>
    </div>
</form>

<style>
    .pc-flag-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 3px 8px;
        border-radius: 999px;
        font-size: 10px;
        line-height: 1.2;
        font-weight: 700;
        border: 0;
        white-space: nowrap;
    }
    .pc-flag-green {
        background: #e8f5e9;
        color: #2e7d32;
    }
    .pc-flag-orange {
        background: #fff3e0;
        color: #ef6c00;
    }
    .pc-flag-red {
        background: #ffebee;
        color: #c62828;
    }
    .modal-body::-webkit-scrollbar {
        width: 6px;
    }
    .modal-body::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .modal-body::-webkit-scrollbar-thumb {
        background: #1565c0;
        border-radius: 10px;
    }
    .modal-body::-webkit-scrollbar-thumb:hover {
        background: #1145a0;
    }
    .result-value:focus {
        border-color: #1565c0 !important;
        box-shadow: 0 0 0 0.2rem rgba(21, 101, 192, 0.25) !important;
    }
    .form-control:focus {
        border-color: #1565c0 !important;
        box-shadow: 0 0 0 0.2rem rgba(21, 101, 192, 0.25) !important;
    }
    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    /* ── toast ── */
    #pc-toast-wrap { position:fixed; top:18px; right:18px; z-index:99999; display:flex; flex-direction:column; gap:8px; pointer-events:none; }
    .pc-toast { display:flex; align-items:flex-start; gap:10px; min-width:280px; max-width:380px; padding:12px 16px; border-radius:8px; font-size:13px; font-weight:500; color:#fff; box-shadow:0 4px 18px rgba(0,0,0,.18); opacity:0; transform:translateX(40px); transition:opacity .25s,transform .25s; pointer-events:auto; }
    .pc-toast.show { opacity:1; transform:translateX(0); }
    .pc-toast-success { background:#388e3c; }
    .pc-toast-error   { background:#c62828; }
    .pc-toast-warn    { background:#e65100; }
    .pc-toast-info    { background:#1565c0; }
    .pc-toast-icon { font-size:16px; flex-shrink:0; margin-top:1px; }
    .pc-toast-close { margin-left:auto; background:none; border:none; color:inherit; font-size:15px; cursor:pointer; opacity:.8; padding:0 0 0 8px; }
</style>

<div id="pc-toast-wrap"></div>

<script>
    (function initResultEntry() {
        const form = document.getElementById('saveReportForm');
        if (!form) return;

        const SAVE_URL = '{{ route("hospital.pathology.worklist.save", $item->id) }}';
        const PRINT_URL = '{{ route("hospital.pathology.worklist.print", $item->id) }}';
        const CSRF = '{{ csrf_token() }}';

        function pcToast(msg, type = 'info', duration = 4000) {
            const wrap = document.getElementById('pc-toast-wrap');
            const icons = { success:'✓', error:'✕', warn:'⚠', info:'ℹ' };
            const t = document.createElement('div');
            t.className = `pc-toast pc-toast-${type}`;
            t.innerHTML = `<span class="pc-toast-icon">${icons[type] || 'ℹ'}</span><span style="flex:1">${msg}</span><button class="pc-toast-close" onclick="this.closest('.pc-toast').remove()">✕</button>`;
            wrap.appendChild(t);
            requestAnimationFrame(() => { requestAnimationFrame(() => t.classList.add('show')); });
            setTimeout(() => { t.classList.remove('show'); setTimeout(() => t.remove(), 300); }, duration);
        }

        function generateFlag(value, minVal, maxVal, critLow, critHigh) {
            if (!value || isNaN(value)) return null;
            const val = parseFloat(value);
            const parsedMin    = (minVal    !== null && !isNaN(minVal))    ? parseFloat(minVal)    : null;
            const parsedMax    = (maxVal    !== null && !isNaN(maxVal))    ? parseFloat(maxVal)    : null;
            const parsedCritLow  = (critLow  !== null && !isNaN(critLow))  ? parseFloat(critLow)  : null;
            const parsedCritHigh = (critHigh !== null && !isNaN(critHigh)) ? parseFloat(critHigh) : null;

            if (parsedCritLow  !== null && val < parsedCritLow)  return { flag:'critical_low',  label:'↓↓ Critical Low',  badgeClass:'pc-flag-badge pc-flag-red',    rowBg:'#fff5f5' };
            if (parsedCritHigh !== null && val > parsedCritHigh) return { flag:'critical_high', label:'↑↑ Critical High', badgeClass:'pc-flag-badge pc-flag-red',    rowBg:'#fff5f5' };
            if (parsedMin !== null && parsedMax !== null) {
                if (parsedCritLow  === null && val < parsedMin * 0.7)  return { flag:'critical_low',  label:'↓↓ Critical Low',  badgeClass:'pc-flag-badge pc-flag-red',    rowBg:'#fff5f5' };
                if (parsedCritHigh === null && val > parsedMax * 1.3)  return { flag:'critical_high', label:'↑↑ Critical High', badgeClass:'pc-flag-badge pc-flag-red',    rowBg:'#fff5f5' };
                if (val < parsedMin || val > parsedMax)                return { flag: val < parsedMin ? 'low' : 'high', label: val < parsedMin ? '↓ Low' : '↑ High', badgeClass:'pc-flag-badge pc-flag-orange', rowBg:'#fffbf0' };
            }
            return { flag:'normal', label:'Normal', badgeClass:'pc-flag-badge pc-flag-green', rowBg:'' };
        }

        form.querySelectorAll('.result-value').forEach(input => {
            input.addEventListener('input', function() {
                const row        = this.closest('tr');
                const flagInfo   = generateFlag(
                    this.value,
                    row.querySelector('.param-min').value  || null,
                    row.querySelector('.param-max').value  || null,
                    row.querySelector('.param-crit-low').value  || null,
                    row.querySelector('.param-crit-high').value || null
                );
                const flagDisplay = row.querySelector('.result-flag-display');
                if (flagInfo) {
                    flagDisplay.innerHTML    = `<span class="${flagInfo.badgeClass}">${flagInfo.label}</span>`;
                    row.style.background = flagInfo.rowBg;
                } else {
                    flagDisplay.innerHTML    = '<span class="text-muted fs-12">—</span>';
                    row.style.background = '';
                }
            });
            if (input.value !== '') input.dispatchEvent(new Event('input', { bubbles: true }));
        });

        function doSave(saveAs) {
            // Validate all result values are filled before finalizing
            if (saveAs === 'final') {
                const emptyInputs = Array.from(form.querySelectorAll('.result-value')).filter(i => i.value.trim() === '');
                if (emptyInputs.length > 0) {
                    emptyInputs.forEach(i => {
                        i.style.borderColor = '#e53935';
                        i.style.background  = '#fff8f8';
                    });
                    // Remove highlight after user types
                    emptyInputs.forEach(i => i.addEventListener('input', function onFix() {
                        i.style.borderColor = '';
                        i.style.background  = '';
                        i.removeEventListener('input', onFix);
                    }));
                    emptyInputs[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                    emptyInputs[0].focus();
                    pcToast(`${emptyInputs.length} parameter(s) have no result value. Please fill all values before finalizing.`, 'warn', 5000);
                    return;
                }
            }

            const formData = new FormData(form);
            formData.set('save_as', saveAs);

            const draftBtn = document.getElementById('pcDraftBtn');
            const finalBtn = document.getElementById('pcFinalBtn');
            const activeBtn = saveAs === 'draft' ? draftBtn : finalBtn;
            const origText  = activeBtn.textContent;
            activeBtn.disabled = true;
            activeBtn.textContent = 'Saving…';

            fetch(SAVE_URL, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                activeBtn.disabled = false;
                if (data.status) {
                    if (saveAs === 'draft') {
                        activeBtn.textContent = '✓ Draft Saved';
                        activeBtn.style.background = '#4caf50';
                        setTimeout(() => { activeBtn.textContent = origText; activeBtn.style.background = '#ff9800'; }, 2500);
                    } else {
                        // 1. Toast
                        pcToast(data.message || 'Pathology report finalized.', 'success');

                        // 2. Hide modal
                        const modalEl = form.closest('.modal');
                        if (modalEl) {
                            const bsModal = bootstrap.Modal.getInstance(modalEl);
                            if (bsModal) bsModal.hide();
                        }

                        // 3. Refresh worklist table (DataTable or plain reload)
                        setTimeout(() => {
                            if (window.LaravelDataTables) {
                                Object.values(window.LaravelDataTables).forEach(dt => { try { dt.ajax.reload(null, false); } catch(e) {} });
                            } else if (typeof $.fn !== 'undefined') {
                                $('table.dataTable').each(function() { try { $(this).DataTable().ajax.reload(null, false); } catch(e) {} });
                            } else {
                                location.reload();
                            }
                        }, 300);

                        // 4. Open report in new tab
                        window.open(data.print_url || PRINT_URL, '_blank');
                    }
                } else {
                    activeBtn.textContent = origText;
                    pcToast(data.message || 'Unable to save. Please try again.', 'error');
                }
            })
            .catch(() => {
                activeBtn.disabled = false;
                activeBtn.textContent = origText;
                pcToast('Network error. Please check your connection.', 'error');
            });
        }

        document.getElementById('pcDraftBtn').addEventListener('click', () => doSave('draft'));
        document.getElementById('pcFinalBtn').addEventListener('click', () => doSave('final'));
    })();
</script>
