/**
 * GoPlay Provider Application Form Handler
 * Handles multi-step form navigation, validation, and submission.
 */

// ============================================================
// FORM STEP MANAGEMENT
// ============================================================
let currentStep = 1;
const totalSteps = 3;

function nextStep(step) {
    if (!validateStep(step)) return;

    document.querySelector(`.form-step[data-step="${step}"]`).classList.remove('active');
    currentStep = step + 1;
    document.querySelector(`.form-step[data-step="${currentStep}"]`).classList.add('active');
    updateProgress();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function prevStep(step) {
    document.querySelector(`.form-step[data-step="${step}"]`).classList.remove('active');
    currentStep = step - 1;
    document.querySelector(`.form-step[data-step="${currentStep}"]`).classList.add('active');
    updateProgress();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function updateProgress() {
    document.querySelectorAll('.step').forEach((step, index) => {
        const stepNumber = index + 1;
        if (stepNumber < currentStep) {
            step.classList.add('completed');
            step.classList.remove('active');
        } else if (stepNumber === currentStep) {
            step.classList.add('active');
            step.classList.remove('completed');
        } else {
            step.classList.remove('active', 'completed');
        }
    });
}

// ============================================================
// FORM VALIDATION
// ============================================================
function validateStep(step) {
    const currentStepElement = document.querySelector(`.form-step[data-step="${step}"]`);
    if (!currentStepElement) return false;

    const inputs = currentStepElement.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;
    let firstError = null;

    inputs.forEach(input => {
        const formGroup = input.closest('.form-group');
        if (!formGroup) return;

        let fieldValid = true;
        let errorMsg = '';

        if (input.type === 'checkbox') {
            if (input.name === 'terms_agree' && !input.checked) {
                errorMsg = 'You must agree to the terms and conditions';
                fieldValid = false;
            }
        } else if (input.type === 'file') {
            if (input.files.length === 0) {
                errorMsg = 'Please upload the required file';
                fieldValid = false;
            } else {
                const maxSize = 5 * 1024 * 1024;
                for (let i = 0; i < input.files.length; i++) {
                    if (input.files[i].size > maxSize) {
                        errorMsg = 'File size must be less than 5MB';
                        fieldValid = false;
                        break;
                    }
                }
            }
        } else if (input.type === 'email') {
            if (!input.value.trim()) {
                errorMsg = 'This field is required';
                fieldValid = false;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input.value)) {
                errorMsg = 'Please enter a valid email address';
                fieldValid = false;
            }
        } else if (input.type === 'tel') {
            const cleaned = input.value.replace(/\D/g, '');
            if (!cleaned) {
                errorMsg = 'This field is required';
                fieldValid = false;
            } else if (cleaned.length !== 10) {
                errorMsg = 'Please enter a valid 10-digit phone number';
                fieldValid = false;
            }
        } else if (input.type === 'url' && input.value) {
            try { new URL(input.value); } catch {
                errorMsg = 'Please enter a valid URL';
                fieldValid = false;
            }
        } else if (!input.value.trim()) {
            errorMsg = 'This field is required';
            fieldValid = false;
        }

        if (!fieldValid) {
            showError(formGroup, errorMsg);
            isValid = false;
            if (!firstError) firstError = formGroup;
        } else {
            clearError(formGroup);
        }
    });

    if (firstError) {
        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    return isValid;
}

function showError(formGroup, message) {
    formGroup.classList.add('error');
    const el = formGroup.querySelector('.error-message');
    if (el) el.textContent = message;
}

function clearError(formGroup) {
    formGroup.classList.remove('error');
    const el = formGroup.querySelector('.error-message');
    if (el) el.textContent = '';
}

// ============================================================
// REAL-TIME VALIDATION
// ============================================================
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input, select, textarea').forEach(input => {
        input.addEventListener('blur', function() {
            const fg = this.closest('.form-group');
            if (fg && this.hasAttribute('required') && this.value.trim() === '' && this.type !== 'file' && this.type !== 'checkbox') {
                showError(fg, 'This field is required');
            }
        });
        input.addEventListener('input', function() {
            const fg = this.closest('.form-group');
            if (fg && fg.classList.contains('error')) clearError(fg);
        });
    });

    // File upload preview
    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', function() {
            const fg = this.closest('.form-group');
            const placeholder = fg ? fg.querySelector('.file-upload-placeholder') : null;
            if (!placeholder) return;

            if (this.files.length > 0) {
                const p = placeholder.querySelector('p');
                const span = placeholder.querySelector('span');
                if (this.files.length === 1) {
                    if (p) p.textContent = this.files[0].name;
                } else {
                    if (p) p.textContent = `${this.files.length} files selected`;
                }
                if (span) span.innerHTML = '<i class="fas fa-check-circle" style="color:#10b981"></i> File(s) ready';
                placeholder.style.borderColor = '#10b981';
                placeholder.style.background = 'rgba(16,185,129,0.05)';
                if (fg) clearError(fg);
            }
        });
    });
});

// ============================================================
// FORM SUBMISSION
// ============================================================
['groundOwnerForm', 'coachForm', 'shopOwnerForm'].forEach(formId => {
    const form = document.getElementById(formId);
    if (!form) return;

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Validate final step
        if (!validateStep(currentStep)) return;

        const submitBtn = form.querySelector('.btn-submit');
        const originalHTML = submitBtn.innerHTML;

        // Disable and show loading
        submitBtn.disabled = true;
        submitBtn.classList.add('loading');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting your application...';

        // Remove old alerts
        document.querySelectorAll('.alert-toast').forEach(el => el.remove());

        const formData = new FormData(form);
        const baseUrl = window.BASE_URL || '';

        try {
            const response = await fetch(baseUrl + '/provider/submit-application', {
                method: 'POST',
                body: formData
            });

            let data;
            const contentType = response.headers.get('content-type') || '';

            if (contentType.includes('application/json')) {
                data = await response.json();
            } else {
                // Server returned HTML (error page) instead of JSON
                const text = await response.text();
                console.error('Server returned non-JSON response:', text.substring(0, 500));
                data = { success: false, message: 'Server error. Please try again later.' };
            }

            if (data.success) {
                // Show success overlay
                showSuccessOverlay(data.message || 'Application submitted successfully!');
            } else {
                // Show error toast
                showToast('error', data.message || 'Failed to submit application. Please try again.');
                submitBtn.disabled = false;
                submitBtn.classList.remove('loading');
                submitBtn.innerHTML = originalHTML;
            }
        } catch (error) {
            console.error('Submission error:', error);
            showToast('error', 'Network error. Please check your connection and try again.');
            submitBtn.disabled = false;
            submitBtn.classList.remove('loading');
            submitBtn.innerHTML = originalHTML;
        }
    });
});

// ============================================================
// SUCCESS OVERLAY
// ============================================================
function showSuccessOverlay(message) {
    const overlay = document.createElement('div');
    overlay.className = 'success-overlay';
    overlay.innerHTML = `
        <div class="success-overlay-content">
            <div class="success-checkmark">
                <div class="check-icon">
                    <span class="icon-line line-tip"></span>
                    <span class="icon-line line-long"></span>
                    <div class="icon-circle"></div>
                    <div class="icon-fix"></div>
                </div>
            </div>
            <h2>Application Submitted!</h2>
            <p>${message}</p>
            <p class="redirect-text">Redirecting to homepage in <span id="countdown">5</span> seconds...</p>
            <a href="${window.BASE_URL || ''}/" class="btn-home">
                <i class="fas fa-home"></i> Go to Homepage Now
            </a>
        </div>
    `;

    // Inject overlay styles
    if (!document.getElementById('success-overlay-styles')) {
        const style = document.createElement('style');
        style.id = 'success-overlay-styles';
        style.textContent = `
            .success-overlay {
                position: fixed; top: 0; left: 0; right: 0; bottom: 0;
                background: rgba(0,0,0,0.7); backdrop-filter: blur(8px);
                display: flex; align-items: center; justify-content: center;
                z-index: 99999; animation: fadeIn 0.3s ease;
            }
            .success-overlay-content {
                background: #fff; border-radius: 20px; padding: 50px 40px;
                text-align: center; max-width: 500px; width: 90%;
                box-shadow: 0 25px 60px rgba(0,0,0,0.3);
                animation: slideUp 0.5s ease;
            }
            .success-overlay-content h2 {
                font-size: 28px; color: #1a1a2e; margin: 20px 0 10px;
            }
            .success-overlay-content p {
                color: #666; font-size: 15px; line-height: 1.6;
            }
            .redirect-text {
                margin-top: 20px; font-size: 13px !important; color: #999 !important;
            }
            .redirect-text span { font-weight: 700; color: #3b82f6; }
            .btn-home {
                display: inline-block; margin-top: 20px; padding: 12px 30px;
                background: linear-gradient(135deg, #3b82f6, #2563eb);
                color: #fff !important; text-decoration: none; border-radius: 10px;
                font-weight: 600; transition: transform 0.2s, box-shadow 0.2s;
            }
            .btn-home:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(59,130,246,0.4);
            }
            /* Animated checkmark */
            .success-checkmark { width: 80px; height: 80px; margin: 0 auto; }
            .check-icon {
                width: 80px; height: 80px; position: relative;
                border-radius: 50%; box-sizing: content-box;
                border: 4px solid #10b981;
            }
            .check-icon::before {
                content: ''; position: absolute; top: 3px; left: 3px;
                width: 30px; height: 80px;
                transform: rotate(-45deg); transform-origin: 100% 100%;
                border-radius: 100px 0 0 100px;
            }
            .check-icon::after {
                content: ''; position: absolute; top: 0; left: 30px;
                width: 60px; height: 80px;
                transform: rotate(-45deg); transform-origin: 0 100%;
                border-radius: 0 100px 100px 0;
                animation: none;
            }
            .icon-line {
                position: absolute; display: block; height: 5px;
                background-color: #10b981; border-radius: 2px;
                z-index: 10;
            }
            .icon-line.line-tip {
                top: 46px; left: 14px; width: 25px;
                transform: rotate(45deg);
                animation: iconLineTip 0.75s ease forwards;
            }
            .icon-line.line-long {
                top: 38px; right: 8px; width: 47px;
                transform: rotate(-45deg);
                animation: iconLineLong 0.75s ease forwards;
            }
            .icon-circle {
                position: absolute; top: -4px; left: -4px;
                z-index: 10; width: 80px; height: 80px;
                border-radius: 50%; box-sizing: content-box;
                border: 4px solid rgba(16,185,129,0.5);
                animation: iconPulse 1.2s ease infinite;
            }
            @keyframes iconLineTip {
                0% { width: 0; left: 1px; top: 19px; }
                54% { width: 0; left: 1px; top: 19px; }
                70% { width: 50px; left: -8px; top: 37px; }
                84% { width: 17px; left: 21px; top: 48px; }
                100% { width: 25px; left: 14px; top: 46px; }
            }
            @keyframes iconLineLong {
                0% { width: 0; right: 46px; top: 54px; }
                65% { width: 0; right: 46px; top: 54px; }
                84% { width: 55px; right: 0; top: 35px; }
                100% { width: 47px; right: 8px; top: 38px; }
            }
            @keyframes iconPulse {
                0%, 100% { opacity: 1; transform: scale(1); }
                50% { opacity: 0.6; transform: scale(1.05); }
            }
            @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
            @keyframes slideUp {
                from { opacity: 0; transform: translateY(30px); }
                to { opacity: 1; transform: translateY(0); }
            }
        `;
        document.head.appendChild(style);
    }

    document.body.appendChild(overlay);

    // Countdown redirect
    let seconds = 5;
    const countdownEl = document.getElementById('countdown');
    const timer = setInterval(() => {
        seconds--;
        if (countdownEl) countdownEl.textContent = seconds;
        if (seconds <= 0) {
            clearInterval(timer);
            window.location.href = (window.BASE_URL || '') + '/';
        }
    }, 1000);
}

// ============================================================
// TOAST NOTIFICATIONS
// ============================================================
function showToast(type, message) {
    document.querySelectorAll('.alert-toast').forEach(el => el.remove());

    // Inject toast styles if missing
    if (!document.getElementById('toast-styles')) {
        const style = document.createElement('style');
        style.id = 'toast-styles';
        style.textContent = `
            .alert-toast {
                position: fixed; top: 20px; right: 20px; z-index: 99998;
                max-width: 420px; width: calc(100% - 40px);
                padding: 18px 22px; border-radius: 12px;
                display: flex; align-items: flex-start; gap: 12px;
                font-size: 14px; line-height: 1.5;
                box-shadow: 0 12px 40px rgba(0,0,0,0.15);
                animation: toastSlideIn 0.4s ease;
                color: #fff;
            }
            .alert-toast.alert-error {
                background: linear-gradient(135deg, #ef4444, #dc2626);
            }
            .alert-toast.alert-success {
                background: linear-gradient(135deg, #10b981, #059669);
            }
            .alert-toast .toast-icon {
                font-size: 20px; flex-shrink: 0; margin-top: 1px;
            }
            .alert-toast .toast-body { flex: 1; }
            .alert-toast .toast-title {
                font-weight: 700; font-size: 15px; margin-bottom: 4px;
            }
            .alert-toast .toast-close {
                background: none; border: none; color: rgba(255,255,255,0.7);
                cursor: pointer; font-size: 18px; padding: 0; flex-shrink: 0;
            }
            .alert-toast .toast-close:hover { color: #fff; }
            @keyframes toastSlideIn {
                from { opacity: 0; transform: translateX(60px); }
                to { opacity: 1; transform: translateX(0); }
            }
            @keyframes toastSlideOut {
                from { opacity: 1; transform: translateX(0); }
                to { opacity: 0; transform: translateX(60px); }
            }
        `;
        document.head.appendChild(style);
    }

    const toast = document.createElement('div');
    toast.className = `alert-toast alert-${type}`;

    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
    const title = type === 'success' ? 'Success' : 'Submission Failed';

    toast.innerHTML = `
        <div class="toast-icon"><i class="fas ${icon}"></i></div>
        <div class="toast-body">
            <div class="toast-title">${title}</div>
            <div>${message}</div>
        </div>
        <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
    `;

    document.body.appendChild(toast);

    // Auto-dismiss after 8 seconds
    setTimeout(() => {
        if (toast.parentElement) {
            toast.style.animation = 'toastSlideOut 0.3s ease forwards';
            setTimeout(() => toast.remove(), 300);
        }
    }, 8000);
}

// Keep old showAlert for backward compatibility
function showAlert(type, message) {
    if (type === 'success') {
        showSuccessOverlay(message);
    } else {
        showToast(type, message);
    }
}

// ============================================================
// PHONE NUMBER FORMATTING
// ============================================================
document.querySelectorAll('input[type="tel"]').forEach(input => {
    input.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 10) value = value.slice(0, 10);
        e.target.value = value;
    });
});

// ============================================================
// DATE CONSTRAINTS
// ============================================================
const dobInput = document.getElementById('dateOfBirth');
if (dobInput) {
    dobInput.setAttribute('max', new Date().toISOString().split('T')[0]);
}

const yearInput = document.getElementById('yearEstablished');
if (yearInput) {
    yearInput.setAttribute('max', new Date().getFullYear());
}

// Initialize progress indicators
updateProgress();
