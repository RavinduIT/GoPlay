// User Profile JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initializeProfile();
});

function initializeProfile() {
    // Initialize avatar upload
    initializeAvatarUpload();
    
    // Load recent activity
    loadRecentActivity();
    
    // Initialize password form
    initializePasswordForm();
}

// Avatar Upload Functionality
function initializeAvatarUpload() {
    const avatarInput = document.getElementById('avatar-input');
    const profileAvatar = document.getElementById('profile-avatar');
    
    if (avatarInput) {
        avatarInput.addEventListener('change', handleAvatarUpload);
    }
}

function handleAvatarUpload(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!allowedTypes.includes(file.type)) {
        showAlert('Please select a valid image file (JPEG, PNG, or GIF)', 'error');
        return;
    }
    
    // Validate file size (5MB max)
    if (file.size > 5 * 1024 * 1024) {
        showAlert('File size must be less than 5MB', 'error');
        return;
    }
    
    // Show preview
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('profile-avatar').src = e.target.result;
    };
    reader.readAsDataURL(file);
    
    // Upload file
    uploadAvatar(file);
}

function uploadAvatar(file) {
    const formData = new FormData();
    formData.append('avatar', file);
    
    showLoading('Uploading avatar...');
    
    fetch('/api/user/avatar', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showAlert('Avatar updated successfully!', 'success');
            // Update navbar avatar if it exists
            const navbarAvatar = document.querySelector('.user-avatar');
            if (navbarAvatar) {
                navbarAvatar.src = data.avatar_url;
            }
        } else {
            showAlert(data.error || 'Failed to upload avatar', 'error');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Upload error:', error);
        showAlert('Failed to upload avatar. Please try again.', 'error');
    });
}

// Edit Profile Functionality
function toggleEdit(section) {
    const content = document.getElementById(`${section}-content`);
    const editFields = content.querySelectorAll('[id$="-edit"]');
    const displayFields = content.querySelectorAll('[id$="-display"]');
    const actions = document.getElementById(`${section}-actions`);
    const editBtn = content.parentElement.querySelector('.edit-btn');
    
    const isEditing = editBtn.textContent.includes('Cancel');
    
    if (isEditing) {
        cancelEdit(section);
        return;
    }
    
    // Show edit fields, hide display fields
    editFields.forEach(field => field.style.display = 'block');
    displayFields.forEach(field => field.style.display = 'none');
    actions.style.display = 'flex';
    
    // Update button
    editBtn.innerHTML = '<i class="fas fa-times"></i> Cancel';
}

function cancelEdit(section) {
    const content = document.getElementById(`${section}-content`);
    const editFields = content.querySelectorAll('[id$="-edit"]');
    const displayFields = content.querySelectorAll('[id$="-display"]');
    const actions = document.getElementById(`${section}-actions`);
    const editBtn = content.parentElement.querySelector('.edit-btn');
    
    // Hide edit fields, show display fields
    editFields.forEach(field => field.style.display = 'none');
    displayFields.forEach(field => field.style.display = 'block');
    actions.style.display = 'none';
    
    // Reset form values
    editFields.forEach(field => {
        const displayField = document.getElementById(field.id.replace('-edit', '-display'));
        if (displayField) {
            field.value = displayField.textContent.trim();
        }
    });
    
    // Update button
    editBtn.innerHTML = '<i class="fas fa-edit"></i> Edit';
}

function saveChanges(section) {
    const content = document.getElementById(`${section}-content`);
    const editFields = content.querySelectorAll('[id$="-edit"]');
    
    // Collect form data
    const formData = {};
    editFields.forEach(field => {
        const fieldName = field.id.replace('-edit', '');
        formData[fieldName] = field.value.trim();
    });
    
    // Validate required fields
    if (!formData.first_name || !formData.last_name) {
        showAlert('First name and last name are required', 'error');
        return;
    }
    
    showLoading('Saving changes...');
    
    fetch('/api/user/profile', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showAlert('Profile updated successfully!', 'success');
            
            // Update display fields
            editFields.forEach(field => {
                const fieldName = field.id.replace('-edit', '');
                const displayField = document.getElementById(`${fieldName}-display`);
                if (displayField) {
                    let displayValue = field.value;
                    
                    // Format date of birth
                    if (fieldName === 'date_of_birth' && displayValue) {
                        displayValue = new Date(displayValue).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        });
                    }
                    
                    displayField.textContent = displayValue || 'Not provided';
                }
            });
            
            // Exit edit mode
            cancelEdit(section);
            
            // Update navbar name if first/last name changed
            if (formData.first_name || formData.last_name) {
                const navbarName = document.querySelector('.user-btn span');
                if (navbarName) {
                    navbarName.textContent = `${formData.first_name} ${formData.last_name}`;
                }
            }
            
        } else {
            showAlert(data.error || 'Failed to update profile', 'error');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Update error:', error);
        showAlert('Failed to update profile. Please try again.', 'error');
    });
}

// Password Change Functionality
function changePassword() {
    document.getElementById('password-modal').classList.add('show');
    document.getElementById('current_password').focus();
}

function closePasswordModal() {
    document.getElementById('password-modal').classList.remove('show');
    document.getElementById('password-form').reset();
    clearFormErrors();
}

function initializePasswordForm() {
    const passwordForm = document.getElementById('password-form');
    if (passwordForm) {
        passwordForm.addEventListener('submit', handlePasswordChange);
    }
    
    // Close modal when clicking outside
    const modal = document.getElementById('password-modal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closePasswordModal();
            }
        });
    }
}

function handlePasswordChange(event) {
    event.preventDefault();
    
    const currentPassword = document.getElementById('current_password').value;
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    // Clear previous errors
    clearFormErrors();
    
    // Validate passwords match
    if (newPassword !== confirmPassword) {
        showFormError('confirm_password', 'Passwords do not match');
        return;
    }
    
    // Validate password strength
    if (newPassword.length < 8) {
        showFormError('new_password', 'Password must be at least 8 characters long');
        return;
    }
    
    const formData = {
        current_password: currentPassword,
        new_password: newPassword
    };
    
    showLoading('Changing password...');
    
    fetch('/api/user/change-password', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showAlert('Password changed successfully!', 'success');
            closePasswordModal();
        } else {
            if (data.error.includes('Current password')) {
                showFormError('current_password', data.error);
            } else {
                showAlert(data.error || 'Failed to change password', 'error');
            }
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Password change error:', error);
        showAlert('Failed to change password. Please try again.', 'error');
    });
}

// Activity Loading
function loadRecentActivity() {
    const activityList = document.getElementById('activity-list');
    if (!activityList) return;
    
    fetch('/api/user/activity')
    .then(response => response.json())
    .then(data => {
        if (data.success && data.activities) {
            displayActivity(data.activities);
        } else {
            activityList.innerHTML = '<p class="text-secondary">No recent activity found.</p>';
        }
    })
    .catch(error => {
        console.error('Failed to load activity:', error);
        activityList.innerHTML = '<p class="text-secondary">Failed to load recent activity.</p>';
    });
}

function displayActivity(activities) {
    const activityList = document.getElementById('activity-list');
    
    if (activities.length === 0) {
        activityList.innerHTML = '<p class="text-secondary">No recent activity found.</p>';
        return;
    }
    
    const activityHTML = activities.map(activity => `
        <div class="activity-item">
            <div class="activity-icon">
                <i class="fas fa-${getActivityIcon(activity.type)}"></i>
            </div>
            <div class="activity-details">
                <h4>${activity.title}</h4>
                <p>${activity.description}</p>
                <span class="activity-time">${formatTimeAgo(activity.created_at)}</span>
            </div>
        </div>
    `).join('');
    
    activityList.innerHTML = activityHTML;
}

function getActivityIcon(type) {
    const icons = {
        'booking': 'calendar-check',
        'order': 'shopping-cart',
        'payment': 'credit-card',
        'profile': 'user',
        'default': 'bell'
    };
    return icons[type] || icons.default;
}

function formatTimeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);
    
    if (diffInSeconds < 60) return 'Just now';
    if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} minutes ago`;
    if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} hours ago`;
    if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)} days ago`;
    
    return date.toLocaleDateString();
}

// Utility Functions
function showAlert(message, type = 'info') {
    // Remove existing alerts
    const existingAlert = document.querySelector('.alert');
    if (existingAlert) {
        existingAlert.remove();
    }
    
    // Create new alert
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} fade-in-up`;
    alert.textContent = message;
    
    // Insert at top of profile container
    const container = document.querySelector('.profile-container');
    container.insertBefore(alert, container.firstChild);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}

function showLoading(message = 'Loading...') {
    const existingLoading = document.getElementById('loading-overlay');
    if (existingLoading) return;
    
    const overlay = document.createElement('div');
    overlay.id = 'loading-overlay';
    overlay.innerHTML = `
        <div class="loading-content">
            <div class="spinner"></div>
            <span>${message}</span>
        </div>
    `;
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
    `;
    
    const loadingContent = overlay.querySelector('.loading-content');
    loadingContent.style.cssText = `
        background: white;
        padding: 2rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 1rem;
        font-weight: 600;
    `;
    
    document.body.appendChild(overlay);
}

function hideLoading() {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) {
        overlay.remove();
    }
}

function showFormError(fieldId, message) {
    const field = document.getElementById(fieldId);
    if (!field) return;
    
    const formGroup = field.closest('.form-group');
    formGroup.classList.add('error');
    
    // Remove existing error message
    const existingError = formGroup.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }
    
    // Add new error message
    const errorElement = document.createElement('span');
    errorElement.className = 'error-message';
    errorElement.textContent = message;
    formGroup.appendChild(errorElement);
}

function clearFormErrors() {
    const errorGroups = document.querySelectorAll('.form-group.error');
    errorGroups.forEach(group => {
        group.classList.remove('error');
        const errorMessage = group.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.remove();
        }
    });
}