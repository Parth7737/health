
    <div class="modal fade" id="vitalsSocialModal" tabindex="-1" aria-labelledby="vitalsSocialModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="vitalsSocialModalLabel">Vitals & Social</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-light border d-flex flex-wrap justify-content-between mb-3" role="alert">
                        <span><strong>OPD No:</strong> <span id="selected-opd-case-no">-</span></span>
                        <span><strong>Visit Date:</strong> <span id="selected-opd-date">-</span></span>
                    </div>
                    <div id="vitals-social-alert" class="alert d-none" role="alert"></div>
                    <ul class="nav nav-tabs mb-3" id="vitalsSocialTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="vitals-tab" data-bs-toggle="tab"
                                data-bs-target="#vitalsTab" type="button" role="tab">Vitals</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="social-tab" data-bs-toggle="tab" data-bs-target="#socialTab"
                                type="button" role="tab">Social</button>
                        </li>
                    </ul>
                    <form id="vitalsSocialForm">
                        <div class="tab-content" id="vitalsSocialTabContent">
                            <!-- Vitals Tab -->
                            <div class="tab-pane fade show active" id="vitalsTab" role="tabpanel">
                                <div class="row g-3 align-items-center justify-content-center">
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-3 d-flex flex-column align-items-center">
                                                <div class="vital-box vital-orange mb-4 w-100">
                                                    <div class="d-flex align-items-center mb-2"><i
                                                            class="fa-solid fa-heart me-2"></i>Respiration</div>
                                                    <div class="d-flex align-items-center mb-2">

                                                        <input type="number" class="form-control vital-input" name="respiration" id="respiration-input" />
                                                        <span class="text-dark text-lg fw-normal">Raspiration</span>
                                                    </div>
                                                </div>
                                                <div class="vital-box vital-green w-100">
                                                    <div class="d-flex align-items-center mb-2"><i
                                                            class="fa-solid fa-heart me-2"></i>Diabetes</div>
                                                    <div class="d-flex align-items-center mb-2">
                                                        <input type="number" class="form-control vital-input" name="diabetes" id="diabetes-input" />
                                                        <span class="text-dark text-lg fw-normal">mmol/l</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div
                                                class="col-md-6 d-flex flex-column align-items-center justify-content-center">
                                                <div id="svg-man-placeholder" class="mb-4"
                                                    style="display:flex;align-items:center;justify-content:center;">
                                                    <!-- SVG man goes here -->
                                                    <div class="human-body">
                                                        <svg data-position="head" class="head"
                                                            xmlns="http://www.w3.org/2000/svg" width="56.594"
                                                            height="95.031" viewBox="0 0 56.594 95.031">
                                                            <path
                                                                d="M15.92 68.5l8.8 12.546 3.97 13.984-9.254-7.38-4.622-15.848zm27.1 0l-8.8 12.546-3.976 13.988 9.254-7.38 4.622-15.848zm6.11-27.775l.108-11.775-21.16-14.742L8.123 26.133 8.09 40.19l-3.24.215 1.462 9.732 5.208 1.81 2.36 11.63 9.72 11.018 10.856-.324 9.56-10.37 1.918-11.952 5.207-1.81 1.342-9.517zm-43.085-1.84l-.257-13.82L28.226 11.9l23.618 15.755-.216 10.37 4.976-17.085L42.556 2.376 25.49 0 10.803 3.673.002 24.415z" />
                                                        </svg>
                                                        <svg data-position="shoulder" class="shoulder"
                                                            xmlns="http://www.w3.org/2000/svg" width="109.532"
                                                            height="46.594" viewBox="0 0 109.532 46.594">
                                                            <path
                                                                d="M38.244-.004l1.98 9.232-11.653 2.857-7.474-2.637zm33.032 0l-1.98 9.232 11.653 2.857 7.474-2.637zm21.238 10.54l4.044-2.187 12.656 14 .07 5.33S92.76 10.66 92.515 10.535zm-1.285.58c-.008.28 17.762 18.922 17.762 18.922l.537 16.557-6.157-10.55L91.5 30.988 83.148 15.6zm-74.224-.58L12.962 8.35l-12.656 14-.062 5.325s16.52-17.015 16.764-17.14zm1.285.58C18.3 11.396.528 30.038.528 30.038L-.01 46.595l6.157-10.55 11.87-5.056L26.374 15.6z" />
                                                        </svg>
                                                        <svg data-position="arm" class="arm"
                                                            xmlns="http://www.w3.org/2000/svg" width="156.344"
                                                            height="119.25" viewBox="0 0 156.344 119.25">
                                                            <path
                                                                d="M21.12 56.5a1.678 1.678 0 0 1-.427.33l.935 8.224 12.977-13.89 1.2-8.958A168.2 168.2 0 0 0 21.12 56.5zm1.387 12.522l-18.07 48.91 5.757 1.333 19.125-39.44 3.518-22.047zm-5.278-18.96l2.638 18.74-17.2 46.023L.01 113.05l6.644-35.518zm118.015 6.44a1.678 1.678 0 0 0 .426.33l-.934 8.222-12.977-13.89-1.2-8.958A168.2 168.2 0 0 1 135.24 56.5zm-1.39 12.52l18.073 48.91-5.758 1.333-19.132-39.44-3.52-22.05zm5.28-18.96l-2.64 18.74 17.2 46.023 2.658-1.775-6.643-35.518zm-103.1-12.323a1.78 1.78 0 0 1 .407-.24l3.666-27.345L33.07.015l-7.258 10.58-6.16 37.04.566 4.973a151.447 151.447 0 0 1 15.808-14.87zm84.3 0a1.824 1.824 0 0 0-.407-.24l-3.666-27.345L123.3.015l7.258 10.58 6.16 37.04-.566 4.973a151.447 151.447 0 0 0-15.822-14.87zM22.288 8.832l-3.3 35.276-2.2-26.238zm111.79 0l3.3 35.276 2.2-26.238z" />
                                                        </svg>
                                                        <svg data-position="cheast" class="cheast"
                                                            xmlns="http://www.w3.org/2000/svg" width="86.594"
                                                            height="45.063" viewBox="0 0 86.594 45.063">
                                                            <path
                                                                d="M19.32 0l-9.225 16.488-10.1 5.056 6.15 4.836 4.832 14.07 11.2 4.616 17.85-8.828-4.452-34.7zm47.934 0l9.225 16.488 10.1 5.056-6.15 4.836-4.833 14.07-11.2 4.616-17.844-8.828 4.45-34.7z" />
                                                        </svg>
                                                        <svg data-position="stomach" class="stomach"
                                                            xmlns="http://www.w3.org/2000/svg" width="75.25"
                                                            height="107.594" viewBox="0 0 75.25 107.594">
                                                            <path
                                                                d="M19.25 7.49l16.6-7.5-.5 12.16-14.943 7.662zm-10.322 8.9l6.9 3.848-.8-9.116zm5.617-8.732L1.32 2.15 6.3 15.6zm-8.17 9.267l9.015 5.514 1.54 11.028-8.795-5.735zm15.53 5.89l.332 8.662 12.286-2.665.664-11.826zm14.61 84.783L33.28 76.062l-.08-20.53-11.654-5.736-1.32 37.5zM22.735 35.64L22.57 46.3l11.787 3.166.166-16.657zm-14.16-5.255L16.49 35.9l1.1 11.25-8.8-7.06zm8.79 22.74l-9.673-7.28-.84 9.78L-.006 68.29l10.564 14.594 5.5.883 1.98-20.735zM56 7.488l-16.6-7.5.5 12.16 14.942 7.66zm10.32 8.9l-6.9 3.847.8-9.116zm-5.617-8.733L73.93 2.148l-4.98 13.447zm8.17 9.267l-9.015 5.514-1.54 11.03 8.8-5.736zm-15.53 5.89l-.332 8.662-12.285-2.665-.664-11.827zm-14.61 84.783l3.234-31.536.082-20.532 11.65-5.735 1.32 37.5zm13.78-71.957l.166 10.66-11.786 3.168-.166-16.657zm14.16-5.256l-7.915 5.514-1.1 11.25 8.794-7.06zm-8.79 22.743l9.673-7.28.84 9.78 6.862 12.66-10.564 14.597-5.5.883-1.975-20.74z" />
                                                        </svg>
                                                        <svg data-position="legs" class="legs"
                                                            xmlns="http://www.w3.org/2000/svg" width="93.626"
                                                            height="286.625" viewBox="0 0 93.626 286.625">
                                                            <path
                                                                d="M17.143 138.643l-.664 5.99 4.647 5.77 1.55 9.1 3.1 1.33 2.655-13.755 1.77-4.88-1.55-3.107zm20.582.444l-3.32 9.318-7.082 13.755 1.77 12.647 5.09-14.2 4.205-7.982zm-26.557-12.645l5.09 27.29-3.32-1.777-2.656 8.875zm22.795 42.374l-1.55 4.88-3.32 20.634-.442 27.51 4.65 26.847-.223-34.39 4.87-13.754.663-15.087zM23.34 181.24l1.106 41.267 8.853 33.28-9.628-4.55-16.045-57.8 5.533-36.384zm15.934 80.536l-.664 18.415-1.55 6.435h-4.647l-1.327-4.437-1.55-.222.332 4.437-5.864-1.778-1.55-.887-6.64-1.442-.22-5.214 6.418-10.87 4.426-5.548 10.844-4.437zM13.63 3.076v22.476l15.71 31.073 9.923 30.85L38.23 66.1zm25.49 30.248l.118-.148-.793-2.024L21.9 12.992l-1.242-.44L31.642 40.93zM32.865 44.09l6.812 17.6 2.274-21.596-1.344-3.43zM6.395 61.91l.827 25.34 12.816 35.257-3.928 10.136L3.5 88.133zM30.96 74.69l.345.826 6.47 15.48-4.177 38.342-6.594-3.526 5.715-35.7zm45.5 63.953l.663 5.99-4.647 5.77-1.55 9.1-3.1 1.33-2.655-13.755-1.77-4.88 1.55-3.107zm-20.582.444l3.32 9.318 7.08 13.755-1.77 12.647-5.09-14.2-4.2-7.987zm3.762 29.73l1.55 4.88 3.32 20.633.442 27.51-4.648 26.847.22-34.39-4.867-13.754-.67-15.087zm10.623 12.424l-1.107 41.267-8.852 33.28 9.627-4.55 16.046-57.8-5.533-36.384zM54.33 261.777l.663 18.415 1.546 6.435h4.648l1.328-4.437 1.55-.222-.333 4.437 5.863-1.778 1.55-.887 6.638-1.442.222-5.214-6.418-10.868-4.426-5.547-10.844-4.437zm25.643-258.7v22.476L64.26 56.625l-9.923 30.85L55.37 66.1zM54.48 33.326l-.118-.15.793-2.023L71.7 12.993l1.24-.44L61.96 40.93zm6.255 10.764l-6.812 17.6-2.274-21.595 1.344-3.43zm26.47 17.82l-.827 25.342-12.816 35.256 3.927 10.136 12.61-44.51zM62.64 74.693l-.346.825-6.47 15.48 4.178 38.342 6.594-3.527-5.715-35.7zm19.792 51.75l-5.09 27.29 3.32-1.776 2.655 8.875zM9.495-.007l.827 21.373-7.028 42.308-3.306-34.155zm2.068 27.323L26.24 59.707l3.307 26-6.2 36.58L9.91 85.046l-.827-38.342zM84.103-.01l-.826 21.375 7.03 42.308 3.306-34.155zm-2.066 27.325L67.36 59.707l-3.308 26 6.2 36.58 13.436-37.24.827-38.34z" />
                                                        </svg>
                                                        <svg data-position="hands" class="hands"
                                                            xmlns="http://www.w3.org/2000/svg" width="205" height="38.938"
                                                            viewBox="0 0 205 38.938">
                                                            <path
                                                                d="M21.255-.002l2.88 6.9 8.412 1.335.664 12.458-4.427 17.8-2.878-.22 2.8-11.847-2.99-.084-4.676 12.6-3.544-.446 4.4-12.736-3.072-.584-5.978 13.543-4.428-.445 6.088-14.1-2.1-1.25-7.528 12.012-3.764-.445L12.4 12.9l-1.107-1.78L.665 15.57 0 13.124l8.635-7.786zm162.49 0l-2.88 6.9-8.412 1.335-.664 12.458 4.427 17.8 2.878-.22-2.8-11.847 2.99-.084 4.676 12.6 3.544-.446-4.4-12.736 3.072-.584 5.978 13.543 4.428-.445-6.088-14.1 2.1-1.25 7.528 12.012 3.764-.445L192.6 12.9l1.107-1.78 10.628 4.45.665-2.447-8.635-7.786z" />
                                                        </svg>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="body_area" id="body-area-input" />
                                            </div>
                                            <div class="col-md-3 d-flex flex-column align-items-center">
                                                <div class="vital-box vital-red mb-4 w-100">
                                                    <div class="d-flex align-items-center mb-2"><i
                                                            class="fa-solid fa-heart me-2"></i>Pulse</div>
                                                    <div class="d-flex align-items-center mb-2">
                                                        <input type="number" class="form-control vital-input" name="pluse" id="pluse-input" />
                                                        <span class="text-dark text-lg fw-normal">BPL</span>
                                                    </div>

                                                </div>
                                                <div class="vital-box vital-green mb-4 w-100">
                                                    <div class="d-flex align-items-center mb-2"><i
                                                            class="fa-solid fa-heart me-2"></i>Systolic BP</div>
                                                    <div class="d-flex align-items-center mb-2">
                                                        <input type="number" class="form-control vital-input" name="systolic_bp" id="systolic-bp-input" />
                                                        <span class="text-dark text-lg fw-normal">mmhg</span>
                                                    </div>
                                                </div>
                                                <div class="vital-box vital-green mb-4 w-100">
                                                    <div class="d-flex align-items-center mb-2"><i
                                                            class="fa-solid fa-heart me-2"></i>Diastolic BP</div>
                                                    <div class="d-flex align-items-center mb-2">

                                                        <input type="number" class="form-control vital-input" name="diastolic_bp" id="diastolic-bp-input" />
                                                        <span class="text-dark text-lg fw-normal">BP</span>

                                                    </div>
                                                </div>
                                                <div class="vital-box vital-red w-100">
                                                    <div class="d-flex align-items-center mb-2"><i
                                                            class="fa-solid fa-heart me-2"></i>Temperature</div>
                                                    <div class="d-flex align-items-center mb-2">

                                                        <input type="number" class="form-control vital-input" name="temperature" id="temperature-input" />
                                                        <span class="text-dark text-lg fw-normal">°F</span>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 d-flex flex-column align-items-center">
                                        <h3 class="mb-4">BMI Calculator</h3>
                                        <div class="vital-box vital-blue mb-3 w-100">
                                            <div class="d-flex align-items-center mb-2"><i
                                                    class="fa-solid fa-arrows-up-down me-2"></i>Height</div>
                                            <div class="d-flex align-items-center mb-2">
                                                <input type="number" class="form-control vital-input" name="height" id="height-input" step="0.01" />
                                                <span class="text-dark text-lg fw-normal">Feet</span>
                                            </div>
                                        </div>
                                        <div class="vital-box vital-blue mb-3 w-100">
                                            <div class="d-flex align-items-center mb-2"><i
                                                    class="fa-solid fa-circle-half-stroke me-2"></i>Weight</div>
                                            <div class="d-flex align-items-center mb-2">
                                                <input type="number" class="form-control vital-input" name="weight" id="weight-input" step="0.01" />
                                                <span class="text-dark text-lg fw-normal">Kg</span>
                                            </div>
                                        </div>
                                        <div class="vital-box vital-orange w-100">
                                            <div class="d-flex align-items-center mb-2"><i
                                                    class="fa-solid fa-calculator me-2"></i>BMI</div>
                                            <div class="d-flex align-items-center mb-2">

                                                <input type="number" class="form-control vital-input" name="bmi" id="bmi-input" step="0.01" />
                                                <span class="text-dark text-lg fw-normal">BMI</span>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end mt-3">
                                    <button type="submit" class="btn btn-success save-vitals-social-btn" data-label="Save Vitals">Save Vitals</button>
                                </div>
                            </div>
                            <!-- Social Tab -->
                            <div class="tab-pane fade" id="socialTab" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="shadow rounded mb-3">
                                            <div class="p-4">
                                                <label class="form-label fw-bold text-success d-block mb-4">Any Known
                                                    Allergies</label>
                                                <select class="form-select select2-vitals-social" name="known_allergies[]" id="known-allergies-input" multiple>
                                                    @foreach($allergyOptions as $allergy)
                                                        <option value="{{ $allergy->id }}">{{ $allergy->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="shadow rounded mb-3">
                                            <div class="p-4">
                                                <label class="form-label fw-bold text-success d-block mb-4">Any Allergic
                                                    Reaction To</label>
                                                <select class="form-select select2-vitals-social" name="allergic_reactions[]" id="allergic-reactions-input" multiple>
                                                    @foreach($allergyReactionOptions as $reaction)
                                                        <option value="{{ $reaction->id }}" data-allergy-id="{{ $reaction->allergy_id }}">{{ $reaction->name }}@if($reaction->allergy) ({{ $reaction->allergy->name }})@endif</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="shadow rounded mb-3">
                                            <div class="p-4">
                                                <label
                                                    class="form-label fw-bold text-success d-block mb-4">Habits</label>
                                                <div id="habitsRows">
                                                    <div class="row g-2 mb-2 habit-row">
                                                        <div class="col-md-5">
                                                            <select class="form-select select2" name="habit_name[]">
                                                                <option value="">Select Habit</option>
                                                                @foreach($habitOptions as $habit)
                                                                    <option value="{{ $habit->name }}">{{ $habit->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <select class="form-select select2" name="habit_status[]">
                                                                <option value="">Status</option>
                                                                <option value="Current">Current</option>
                                                                <option value="Past">Past</option>
                                                                <option value="Never">Never</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2 d-flex align-items-center">
                                                            <button type="button"
                                                                class="btn btn-outline-primary btn-sm add-habit me-1"><i
                                                                    class="fa fa-plus"></i></button>
                                                            <button type="button"
                                                                class="btn btn-outline-danger btn-sm remove-habit d-none"><i
                                                                    class="fa fa-minus"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="shadow rounded mb-3">
                                            <div class="p-4">
                                                <label class="form-label fw-bold text-success d-block mb-4">Family
                                                    History</label>
                                                <div id="familyRows">
                                                    <div class="row g-2 mb-2 family-row">
                                                        <div class="col-md-2"><select class="form-select select2"
                                                                name="family_disease[]">
                                                                <option value="">Disease</option>
                                                                @foreach($diseaseOptions as $disease)
                                                                    <option value="{{ $disease->name }}">{{ $disease->name }}</option>
                                                                @endforeach
                                                            </select></div>
                                                        <div class="col-md-2"><select class="form-select select2"
                                                                name="family_relation[]">
                                                                <option value="">Relation</option>
                                                                @foreach($relationOptions as $relation)
                                                                    <option value="{{ $relation }}">{{ $relation }}</option>
                                                                @endforeach
                                                            </select></div>
                                                        <div class="col-md-2"><input type="text" class="form-control"
                                                                name="family_age[]" placeholder="Age" /></div>
                                                        <div class="col-md-4"><input type="text" class="form-control"
                                                                name="family_comments[]" placeholder="Comments" /></div>
                                                        <div class="col-md-2 d-flex align-items-center">
                                                            <button type="button"
                                                                class="btn btn-outline-primary btn-sm add-family me-1"><i
                                                                    class="fa fa-plus"></i></button>
                                                            <button type="button"
                                                                class="btn btn-outline-danger btn-sm remove-family d-none"><i
                                                                    class="fa fa-minus"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="shadow rounded mb-3">
                                            <div class="p-4">
                                                <label class="form-label fw-bold text-success d-block mb-4">Social
                                                    History</label>
                                                <div class="row g-2 mb-2">
                                                        <div class="col-md-4"><input type="text" class="form-control" name="occupation"
                                                            id="occupation-input" placeholder="Occupation" /></div>
                                                        <div class="col-md-4"><select class="form-select" name="social_marital_status" id="social-marital-status-input">
                                                            <option value="">Select Marital Status</option>
                                                            <option value="Single">Single</option>
                                                            <option value="Married">Married</option>
                                                            <option value="Divorced">Divorced</option>
                                                            <option value="Widowed">Widowed</option>
                                                        </select></div>
                                                        <div class="col-md-4"><input type="text" class="form-control" name="place_of_birth"
                                                            id="place-of-birth-input" placeholder="Place of Birth" /></div>
                                                        <div class="col-md-4"><input type="text" class="form-control" name="current_location"
                                                            id="current-location-input" placeholder="Current Location" /></div>
                                                        <div class="col-md-4"><input type="text" class="form-control" name="years_in_current_location"
                                                            id="years-location-input" placeholder="Years in Current Location" /></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-success save-vitals-social-btn" data-label="Save Social">Save Social</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>