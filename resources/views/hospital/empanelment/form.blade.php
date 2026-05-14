@extends('layouts.hospital.empanelment.app')
@section('title', 'Hospital empanelment | ParaCare+ HIMS')

@section('empanelment_nav_replace')
    @include('hospital.empanelment._partials.onboarding-navbar')
@endsection

@push('css')
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('public/front/assets/css/empanelment-onboarding.css') }}" />
@endpush

@section('content')
    @php
        $wizardSteps = [
            ['num' => 1, 'label' => 'Select facility type', 'icon' => 'fa-hospital', 'hint' => 'Choose how your facility is structured'],
            ['num' => 2, 'label' => 'Basic information', 'icon' => 'fa-info-circle', 'hint' => 'Identity, location & contact'],
            ['num' => 3, 'label' => 'Infrastructure details', 'icon' => 'fa-building', 'hint' => 'Beds, floors & facilities'],
            ['num' => 4, 'label' => 'Staff strength & services', 'icon' => 'fa-users', 'hint' => 'Specialities & capacity'],
            ['num' => 5, 'label' => 'Documents', 'icon' => 'fa-folder-open', 'hint' => 'Licenses & document uploads'],
            ['num' => 6, 'label' => 'Ayushman Bharat empanelment', 'icon' => 'fa-hospital-user', 'hint' => 'Ayushman Bharat details'],
            ['num' => 7, 'label' => 'HMIS & IT setup', 'icon' => 'fa-cogs', 'hint' => 'Systems & credentials'],
            ['num' => 8, 'label' => 'Review & submit', 'icon' => 'fa-clipboard-check', 'hint' => 'Declaration & application submit'],
        ];
        $wizardStepCount = count($wizardSteps);
        $wizardPrev = [];
        foreach ($wizardSteps as $i => $ws) {
            if ($i > 0) {
                $wizardPrev[$ws['num']] = $wizardSteps[$i - 1]['num'];
            }
        }
        $stepHints = [];
        foreach ($wizardSteps as $ws) {
            $stepHints[$ws['num']] = $ws['hint'] ?? $ws['label'];
        }
        $unlockedMaxStep = isset($unlockedMaxStep) ? (int) $unlockedMaxStep : 8;
        $navStep = isset($initialWizardStep) ? (int) $initialWizardStep : 2;
        if ($navStep < 1) {
            $navStep = 1;
        }
        if ($navStep > 8) {
            $navStep = 8;
        }
    @endphp

    <div class="empanelment-onboarding-wrap" id="empanelmentOnboardingRoot" data-total-steps="{{ $wizardStepCount }}"
        data-initial-step="{{ $navStep }}" data-unlocked-max="{{ $unlockedMaxStep }}"
        data-step-hints='@json($stepHints)'>

        <div class="eo-stepper" id="eoStepper" role="navigation" aria-label="Onboarding steps">
            @foreach ($wizardSteps as $idx => $ws)
                @if ($idx > 0)
                    <div class="eo-step-connector eo-latch-conn" data-conn-to="{{ $ws['num'] }}"></div>
                @endif
                <div role="button" tabindex="0"
                    class="eo-step eo-latch-step eo-step-clickable @if ($navStep === $ws['num']) active @endif @if ($navStep > $ws['num']) done @endif"
                    data-step="{{ $ws['num'] }}" id="eo-step-{{ $ws['num'] }}"
                    onclick="if(typeof loadStep==='function')loadStep({{ $ws['num'] }})"
                    onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();if(typeof loadStep==='function')loadStep({{ $ws['num'] }});}">
                    <div class="eo-step-circle"><i class="fas {{ $ws['icon'] }}"></i></div>
                    <div class="eo-step-label">{{ $ws['label'] }}</div>
                </div>
            @endforeach
        </div>

        <div class="eo-progress-wrap">
            <div class="eo-progress-bg">
                <div class="eo-progress-fill" id="eoProgressFill"
                    style="width: {{ $wizardStepCount > 0 ? min(100, round($navStep / $wizardStepCount * 100)) : 0 }}%">
                </div>
            </div>
            <div class="eo-progress-label">
                <span id="eoProgressText">Step {{ $navStep }} of {{ $wizardStepCount }}</span>
                <span id="eoProgressPct">{{ $wizardStepCount > 0 ? round($navStep / $wizardStepCount * 100) : 0 }}%</span>
            </div>
            <div class="small mt-1" id="eoProgressHint" style="color:var(--eo-muted)">
                {{ $stepHints[$navStep] ?? '' }}
            </div>
        </div>

        @if (@$hospital && $hospital->status == 'Rejected' && $hospital->reject_reason)
            <div class="eo-alert-reject" role="alert">
                <h6><i class="fas fa-exclamation-triangle me-2"></i>Application rejected</h6>
                <p class="mb-1"><strong>Reason:</strong> {{ $hospital->reject_reason }}</p>
                <p class="mb-0 small" style="color:var(--eo-muted2)">Update the required sections and save again before final submit.</p>
            </div>
        @endif

        <div class="eo-tab-strip" id="eoNavPills" role="tablist">
            @foreach ($wizardSteps as $ws)
                <button type="button"
                    class="eo-nav-pill eo-nav-pill-btn nav-link navstep{{ $ws['num'] }} eo-latch-pill @if ($navStep === $ws['num']) active @endif"
                    data-step="{{ $ws['num'] }}" data-latch-step="{{ $ws['num'] }}"
                    onclick="loadStep({{ $ws['num'] }})">{{ $ws['label'] }}</button>
            @endforeach
        </div>

        <div class="tab-content eo-tab-panes">
            <div class="tab-pane fade step1" id="tab-facility-type" role="tabpanel"></div>
            <div class="tab-pane fade step2" id="tab-basic-information" role="tabpanel"></div>
            <div class="tab-pane fade step3" id="tab-infrastructure" role="tabpanel"></div>
            <div class="tab-pane fade step4" id="tab-staff-services" role="tabpanel"></div>
            <div class="tab-pane fade step5" id="tab-documents" role="tabpanel"></div>
            <div class="tab-pane fade step6" id="tab-ab-empanelment" role="tabpanel"></div>
            <div class="tab-pane fade step7" id="tab-hmis-setup" role="tabpanel"></div>
            <div class="tab-pane fade step8" id="tab-review-submit" role="tabpanel"></div>
        </div>
    </div>

    <style>
        @media print {
            body {
                visibility: hidden;
            }

            .modal-body {
                visibility: visible;
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                height: auto;
            }

            .table-responsive {
                overflow: visible !important;
                max-height: none !important;
                height: auto !important;
            }

            .table {
                width: 100%;
                border-collapse: collapse;
            }
        }
    </style>
@endsection

@push('scripts')
    <script>
        window.eoWizardPrev = @json($wizardPrev);

        (function() {
            const root = document.getElementById('empanelmentOnboardingRoot');
            if (!root) return;

            const hints = (function() {
                try {
                    return JSON.parse(root.dataset.stepHints || '{}');
                } catch (e) {
                    return {};
                }
            })();

            const unlockedFloor = parseInt(root.dataset.unlockedMax, 10) || 8;

            window.eoSetUnlockedMax = function(max) {
                max = Math.max(parseInt(max, 10) || 1, unlockedFloor);
                root.dataset.unlockedMax = String(max);
                root.querySelectorAll('.eo-latch-step').forEach(function(el) {
                    var n = parseInt(el.dataset.step, 10);
                    el.classList.toggle('d-none', n > max);
                });
                root.querySelectorAll('.eo-latch-pill').forEach(function(el) {
                    var n = parseInt(el.dataset.latchStep, 10);
                    el.classList.toggle('d-none', n > max);
                });
                root.querySelectorAll('.eo-latch-conn').forEach(function(el) {
                    var to = parseInt(el.dataset.connTo, 10);
                    el.classList.toggle('d-none', to > max);
                });
            };

            window.eoUpdateStepper = function(currentStep) {
                currentStep = parseInt(currentStep, 10) || 1;
                const total = parseInt(root.dataset.totalSteps, 10) || 1;
                document.querySelectorAll('#eoStepper .eo-step').forEach(function(el) {
                    if (el.classList.contains('d-none')) return;
                    const n = parseInt(el.dataset.step, 10);
                    el.classList.remove('active', 'done');
                    if (n < currentStep) el.classList.add('done');
                    if (n === currentStep) el.classList.add('active');
                });
                const pct = Math.min(100, Math.round(currentStep / total * 100));
                const fill = document.getElementById('eoProgressFill');
                const txt = document.getElementById('eoProgressText');
                const pc = document.getElementById('eoProgressPct');
                const hintEl = document.getElementById('eoProgressHint');
                if (fill) fill.style.width = pct + '%';
                if (txt) txt.textContent = 'Step ' + currentStep + ' of ' + total;
                if (pc) pc.textContent = pct + '%';
                if (hintEl && hints[currentStep]) hintEl.textContent = hints[currentStep];
                document.querySelectorAll('.eo-nav-pill-btn').forEach(function(btn) {
                    if (btn.classList.contains('d-none')) return;
                    const n = parseInt(btn.dataset.step, 10);
                    btn.classList.toggle('active', n === currentStep);
                });
            };

            window.eoUpdateStepper(root.dataset.initialStep || 1);
        })();

        @if ($user)
            loadStep('{{ $navStep }}');
        @endif

        function loadStep(step) {
            const root = document.getElementById('empanelmentOnboardingRoot');
            const unlocked = root ? (parseInt(root.dataset.unlockedMax, 10) || 8) : 8;
            step = parseInt(step, 10) || 1;
            if (step > unlocked) {
                if (typeof errorMessage === 'function') {
                    errorMessage('Complete and save the previous section first.');
                } else {
                    alert('Complete and save the previous section first.');
                }
                return;
            }

            ldrshow();
            $('.eo-nav-pill-btn').removeClass('active');
            $('.eo-nav-pill-btn[data-step="' + step + '"]').addClass('active');
            $('.empanelment-onboarding-wrap .tab-pane').removeClass('show active');
            $('.step' + step).addClass('show active');
            window.eoUpdateStepper(step);

            $.ajax({
                url: "{{ route('hospital.empanelmentRegistration.stepLoad', [$uuid]) }}",
                type: 'POST',
                dataType: 'html',
                data: {
                    _token: '{{ csrf_token() }}',
                    step: step
                },
                success: function(html) {
                    ldrhide();
                    $('.step' + step).html(html);
                    loadSelect2();
                    if (typeof window.eoUpdateStepper === 'function') {
                        window.eoUpdateStepper(step);
                    }
                },
                error: function(xhr, status, error) {
                    ldrhide();
                    if (xhr.status === 403 && xhr.responseText) {
                        $('.step' + step).html(xhr.responseText);
                        return;
                    }
                    console.error('Error loading step:', error);
                    alert('Failed to load this step. Please try again.');
                }
            });
        }
    </script>
@endpush
