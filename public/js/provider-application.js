// Form Step Management
let currentStep = 1;
const totalSteps = 3;

function nextStep(step) {
    // Validate current step
    if (!validateStep(step)) {
        return;
    }

    // Hide current step
    document.querySelector(`.form-step[data-step="${step}"]`).classList.remove('active');

    // Show next step
    currentStep = step + 1;
    document.querySelector(`.form-step[data-step="${currentStep}"]`).classList.add('active');

    // Update progress
    updateProgress();

    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function prevStep(step) {
    // Hide current step
    document.querySelector(`.form-step[data-step="${step}"]`).classList.remove('active');

    // Show previous step
    currentStep = step - 1;
    document.querySelector(`.form-step[data-step="${currentStep}"]`).classList.add('active');

    // Update progress
    updateProgress();

    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function updateProgress() {
    // Update step indicators
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

// Form Validation
function validateStep(step) {
    const currentStepElement = document.querySelector(`.form-step[data-step="${step}"]`);
    const inputs = currentStepElement.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;

    inputs.forEach(input => {
        const formGroup = input.closest('.form-group');

        // Check if input is empty
        if (!input.value.trim() && input.type !== 'checkbox' && input.type !== 'file') {
            showError(formGroup, 'This field is required');
            isValid = false;
        }
        // Check checkboxes
        else if (input.type === 'checkbox') {
            const checkboxGroup = input.closest('.form-group');
            const checkboxes = checkboxGroup.querySelectorAll('input[type="checkbox"]');
            const isAnyChecked = Array.from(checkboxes).some(cb => cb.checked);

            if (input.hasAttribute('required') && !input.checked && input.name !== 'terms_agree') {
                // For checkbox groups
                if (!isAnyChecked) {
                    showError(checkboxGroup, 'Please select at least one option');
                    isValid = false;
                } else {
                    clearError(checkboxGroup);
                }
            } else if (input.name === 'terms_agree' && !input.checked) {
                showError(formGroup, 'You must agree to the terms and conditions');
                isValid = false;
            } else {
                clearError(formGroup);
            }
        }
        // Check file inputs
        else if (input.type === 'file') {
            if (input.files.length === 0) {
                showError(formGroup, 'Please upload the required file');
                isValid = false;
            } else {
                // Check file size (5MB max)
                const maxSize = 5 * 1024 * 1024; // 5MB
                let hasLargeFile = false;

                Array.from(input.files).forEach(file => {
                    if (file.size > maxSize) {
                        hasLargeFile = true;
                    }
                });

                if (hasLargeFile) {
                    showError(formGroup, 'File size must be less than 5MB');
                    isValid = false;
                } else {
                    clearError(formGroup);
                }
            }
        }
        // Email validation
        else if (input.type === 'email') {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(input.value)) {
                showError(formGroup, 'Please enter a valid email address');
                isValid = false;
            } else {
                clearError(formGroup);
            }
        }
        // Phone validation
        else if (input.type === 'tel') {
            const phoneRegex = /^[0-9]{10}$/;
            const cleanedPhone = input.value.replace(/\D/g, '');
            if (!phoneRegex.test(cleanedPhone)) {
                showError(formGroup, 'Please enter a valid 10-digit phone number');
                isValid = false;
            } else {
                clearError(formGroup);
            }
        }
        // URL validation
        else if (input.type === 'url' && input.value) {
            try {
                new URL(input.value);
                clearError(formGroup);
            } catch {
                showError(formGroup, 'Please enter a valid URL');
                isValid = false;
            }
        }
        else {
            clearError(formGroup);
        }
    });

    return isValid;
}

function showError(formGroup, message) {
    formGroup.classList.add('error');
    const errorElement = formGroup.querySelector('.error-message');
    if (errorElement) {
        errorElement.textContent = message;
    }
}

function clearError(formGroup) {
    formGroup.classList.remove('error');
    const errorElement = formGroup.querySelector('.error-message');
    if (errorElement) {
        errorElement.textContent = '';
    }
}

// Real-time validation
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners for real-time validation
    const inputs = document.querySelectorAll('input, select, textarea');

    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            const formGroup = this.closest('.form-group');
            if (this.hasAttribute('required') && this.value.trim() === '') {
                showError(formGroup, 'This field is required');
            } else {
                clearError(formGroup);
            }
        });

        input.addEventListener('input', function() {
            const formGroup = this.closest('.form-group');
            if (formGroup.classList.contains('error')) {
                clearError(formGroup);
            }
        });
    });

    // File upload preview
    const fileInputs = document.querySelectorAll('input[type="file"]');

    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const formGroup = this.closest('.form-group');
            const placeholder = formGroup.querySelector('.file-upload-placeholder');

            if (this.files.length > 0) {
                const fileNames = Array.from(this.files).map(f => f.name).join(', ');
                const fileCount = this.files.length;

                if (fileCount === 1) {
                    placeholder.querySelector('p').textContent = this.files[0].name;
                } else {
                    placeholder.querySelector('p').textContent = `${fileCount} files selected`;
                }

                placeholder.querySelector('span').textContent = '<i class="fas fa-check"></i> Uploaded';
                placeholder.style.color = 'var(--success-color)';
                clearError(formGroup);
            }
        });
    });
});

// Form Submission
const forms = ['groundOwnerForm', 'coachForm', 'shopOwnerForm'];

forms.forEach(formId => {
    const form = document.getElementById(formId);

    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Validate final step
            if (!validateStep(currentStep)) {
                return;
            }

            // Get submit button
            const submitBtn = form.querySelector('.btn-submit');
            const originalText = submitBtn.innerHTML;

            // Disable button and show loading
            submitBtn.disabled = true;
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

            // Prepare form data
            const formData = new FormData(form);

            try {
                // Submit form
                const response = await fetch((window.BASE_URL||'')+'/provider/submit-application', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // Show success message
                    showAlert('success', 'Application submitted successfully! You will receive an email once your application is reviewed.');

                    // Redirect after 3 seconds
                    setTimeout(() => {
                        window.location.href=(window.BASE_URL||'')+'/';
                    }, 3000);
                } else {
                    // Show error message
                    showAlert('error', data.message || 'Failed to submit application. Please try again.');

                    // Re-enable button
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('loading');
                    submitBtn.innerHTML = originalText;
                }
            } catch (error) {
                console.error('Error submitting form:', error);
                showAlert('error', 'An error occurred. Please try again later.');

                // Re-enable button
                submitBtn.disabled = false;
                submitBtn.classList.remove('loading');
                submitBtn.innerHTML = originalText;
            }
        });
    }
});

// Show Alert Function
function showAlert(type, message) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());

    // Create new alert
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;

    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    alert.innerHTML = `
        <i class="fas ${icon}"></i>
        <span>${message}</span>
    `;

    // Insert at top of form
    const form = document.querySelector('.application-form');
    form.insertBefore(alert, form.firstChild);

    // Scroll to alert
    alert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

// Phone number formatting
const phoneInputs = document.querySelectorAll('input[type="tel"]');
phoneInputs.forEach(input => {
    input.addEventListener('input', function(e) {
        // Remove non-numeric characters
        let value = e.target.value.replace(/\D/g, '');

        // Limit to 10 digits
        if (value.length > 10) {
            value = value.slice(0, 10);
        }

        e.target.value = value;
    });
});

// Prevent future dates for date of birth
const dobInput = document.getElementById('dateOfBirth');
if (dobInput) {
    const today = new Date().toISOString().split('T')[0];
    dobInput.setAttribute('max', today);
}

// Set max year for year established
const yearInput = document.getElementById('yearEstablished');
if (yearInput) {
    const currentYear = new Date().getFullYear();
    yearInput.setAttribute('max', currentYear);
}

// Initialize progress
updateProgress();
