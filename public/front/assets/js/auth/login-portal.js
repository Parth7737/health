(function () {
    const config = window.PARACARE_LOGIN || {};
    const state = {
        selectedRole: config.defaultRole || "",
        challengeToken: "",
        loginPayload: null,
        otpSeconds: 0,
        otpTimer: null,
    };

    const roleInput = document.getElementById("selectedRole");
    const loginPanel = document.getElementById("panelLogin");
    const otpPanel = document.getElementById("panelOtp");
    const btnSendOtp = document.getElementById("btnSendOtp");
    const btnVerifyOtp = document.getElementById("btnVerifyOtp");
    const btnResendOtp = document.getElementById("btnResendOtp");

    bindRoleSelection();
    bindForms();
    initOtpInputs();
    startClock();

    function bindRoleSelection() {
        const options = document.querySelectorAll(".role-option");

        options.forEach((option) => {
            option.addEventListener("click", function () {
                options.forEach((item) => item.classList.remove("active"));
                option.classList.add("active");
                state.selectedRole = option.dataset.role || "";
                roleInput.value = state.selectedRole;
            });
        });

        if (state.selectedRole) {
            const selected = document.querySelector('.role-option[data-role="' + state.selectedRole + '"]');
            if (selected) {
                selected.click();
            }
        }
    }

    function bindForms() {
        const loginForm = document.getElementById("loginForm");
        const otpForm = document.getElementById("otpForm");
        const btnBack = document.getElementById("btnBackToLogin");

        loginForm.addEventListener("submit", async function (event) {
            event.preventDefault();
            clearErrors();

            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value;
            const role = roleInput.value;

            if (!email) {
                showFieldError("email", "Username or email is required.");
                return;
            }

            if (!password) {
                showFieldError("password", "Password is required.");
                return;
            }

            if (!role) {
                showToast("Please select role.", "warning");
                return;
            }

            btnSendOtp.disabled = true;
            btnSendOtp.textContent = "Sending OTP...";

            try {
                const payload = { email: email, password: password, role: role };
                state.loginPayload = payload;

                const response = await postJson(config.routes.sendOtp, payload);

                state.challengeToken = response.challenge_token;
                showPanel("otp");
                startOtpCountdown(Number(response.expires_in || 300));

                const mask = response.masked_mobile && response.masked_mobile !== "N/A"
                    ? "Mobile ending " + response.masked_mobile
                    : "your registered contact";

                const message = "OTP sent successfully to " + mask + ".";
                document.getElementById("otpHint").textContent = message;
                showToast(message, "success");

                if (response.debug_otp) {
                    showToast("Debug OTP: " + response.debug_otp, "info", 5000);
                }
            } catch (error) {
                applyBackendErrors(error);
            } finally {
                btnSendOtp.disabled = false;
                btnSendOtp.textContent = "Send OTP";
            }
        });

        otpForm.addEventListener("submit", async function (event) {
            event.preventDefault();
            const otp = readOtp();

            if (!state.challengeToken) {
                showToast("Session expired. Please login again.", "warning");
                showPanel("login");
                return;
            }

            if (!/^\d{6}$/.test(otp)) {
                showToast("Please enter valid 6 digit OTP.", "warning");
                return;
            }

            btnVerifyOtp.disabled = true;
            btnVerifyOtp.textContent = "Verifying...";

            try {
                const response = await postJson(config.routes.verifyOtp, {
                    challenge_token: state.challengeToken,
                    otp: otp,
                });

                showToast(response.message || "Login successful.", "success");
                window.location.href = response.url;
            } catch (error) {
                applyBackendErrors(error);
            } finally {
                btnVerifyOtp.disabled = false;
                btnVerifyOtp.textContent = "Verify OTP & Login";
            }
        });

        btnResendOtp.addEventListener("click", async function () {
            if (!state.loginPayload) {
                showToast("Please login again to resend OTP.", "warning");
                return;
            }

            btnResendOtp.disabled = true;

            try {
                const response = await postJson(config.routes.sendOtp, state.loginPayload);
                state.challengeToken = response.challenge_token;
                startOtpCountdown(Number(response.expires_in || 300));
                showToast("OTP resent successfully.", "info");

                if (response.debug_otp) {
                    showToast("Debug OTP: " + response.debug_otp, "info", 5000);
                }

                clearOtpInputs();
            } catch (error) {
                applyBackendErrors(error);
            }
        });

        btnBack.addEventListener("click", function () {
            showPanel("login");
            clearOtpInputs();
        });
    }

    function showPanel(panel) {
        if (panel === "otp") {
            loginPanel.classList.remove("active");
            otpPanel.classList.add("active");
            focusFirstOtp();
            return;
        }

        otpPanel.classList.remove("active");
        loginPanel.classList.add("active");
        stopOtpCountdown();
        state.challengeToken = "";
    }

    function initOtpInputs() {
        const otpInputs = document.querySelectorAll(".otp-box");

        otpInputs.forEach((input, index) => {
            input.addEventListener("input", function () {
                input.value = input.value.replace(/\D/g, "").slice(0, 1);
                if (input.value && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
            });

            input.addEventListener("keydown", function (event) {
                if (event.key === "Backspace" && !input.value && index > 0) {
                    otpInputs[index - 1].focus();
                }
            });

            input.addEventListener("paste", function (event) {
                const text = (event.clipboardData || window.clipboardData).getData("text");
                const digits = text.replace(/\D/g, "").slice(0, 6);

                if (!digits) {
                    return;
                }

                otpInputs.forEach((item, i) => {
                    item.value = digits[i] || "";
                });

                event.preventDefault();
            });
        });
    }

    function readOtp() {
        const otpInputs = document.querySelectorAll(".otp-box");
        return Array.from(otpInputs)
            .map((item) => item.value)
            .join("");
    }

    function clearOtpInputs() {
        const otpInputs = document.querySelectorAll(".otp-box");
        otpInputs.forEach((item) => {
            item.value = "";
        });
        focusFirstOtp();
    }

    function focusFirstOtp() {
        const firstOtp = document.querySelector(".otp-box");
        if (firstOtp) {
            firstOtp.focus();
        }
    }

    function showFieldError(fieldId, message) {
        const field = document.getElementById(fieldId);
        const errorNode = document.getElementById(fieldId + "Error");

        if (field) {
            field.classList.add("input-error");
        }

        if (errorNode) {
            errorNode.textContent = message;
        }
    }

    function clearErrors() {
        ["email", "password"].forEach((fieldId) => {
            const field = document.getElementById(fieldId);
            const errorNode = document.getElementById(fieldId + "Error");

            if (field) {
                field.classList.remove("input-error");
            }

            if (errorNode) {
                errorNode.textContent = "";
            }
        });
    }

    function applyBackendErrors(error) {
        const errors = (error && error.errors) || {};
        const messages = [];

        Object.keys(errors).forEach((key) => {
            if (Array.isArray(errors[key]) && errors[key].length) {
                messages.push(errors[key][0]);
            }
        });

        if (errors.email) {
            showFieldError("email", errors.email[0]);
        }

        if (errors.password) {
            showFieldError("password", errors.password[0]);
        }

        if (messages.length) {
            showToast(messages[0], "error");
            return;
        }

        const fallback = (error && error.message) || "Something went wrong. Please try again.";
        showToast(fallback, "error");
    }

    async function postJson(url, body) {
        const response = await fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": config.csrfToken,
            },
            body: JSON.stringify(body),
        });

        const json = await response.json();

        if (!response.ok) {
            throw json;
        }

        return json;
    }

    function showToast(message, type, duration) {
        const stack = document.getElementById("toastStack");
        const item = document.createElement("div");

        item.className = "toast-item " + (type || "info");
        item.textContent = message;
        stack.appendChild(item);

        setTimeout(function () {
            item.remove();
        }, duration || 3000);
    }

    function startOtpCountdown(totalSeconds) {
        state.otpSeconds = totalSeconds;
        stopOtpCountdown();
        updateOtpTimerText();

        btnResendOtp.disabled = true;

        state.otpTimer = setInterval(function () {
            state.otpSeconds -= 1;
            updateOtpTimerText();

            if (state.otpSeconds <= 0) {
                stopOtpCountdown();
                btnResendOtp.disabled = false;
            }
        }, 1000);
    }

    function stopOtpCountdown() {
        if (state.otpTimer) {
            clearInterval(state.otpTimer);
            state.otpTimer = null;
        }
    }

    function updateOtpTimerText() {
        const minute = Math.floor(Math.max(state.otpSeconds, 0) / 60);
        const second = Math.max(state.otpSeconds, 0) % 60;
        const timer = document.getElementById("otpTimer");

        timer.textContent = String(minute).padStart(2, "0") + ":" + String(second).padStart(2, "0");
    }

    function startClock() {
        const node = document.getElementById("portalClock");

        if (!node) {
            return;
        }

        const render = function () {
            const now = new Date();
            node.textContent = now.toLocaleString("en-IN", {
                weekday: "short",
                day: "2-digit",
                month: "short",
                year: "numeric",
                hour: "2-digit",
                minute: "2-digit",
            });
        };

        render();
        setInterval(render, 30000);
    }
})();
