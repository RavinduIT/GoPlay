// User Profile JavaScript functionality
class UserProfile {
    constructor() {
        this.isEditing = {};
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadRecentActivity();
    }

    setupEventListeners() {
        // Avatar upload
        const avatarInput = document.getElementById('avatar-input');
        if (avatarInput) {
            avatarInput.addEventListener('change', (e) => this.handleAvatarUpload(e));
        }

        // Password form
        const passwordForm = document.getElementById('password-form');
        if (passwordForm) {
            passwordForm.addEventListener('submit', (e) => this.handlePasswordChange(e));
        }

        // Close modal when clicking outside
        window.addEventListener('click', (e) => {
            const modal = document.getElementById('password-modal');
            if (e.target === modal) {
                this.closePasswordModal();
            }
        });

        // Confirm password validation
        const confirmPassword = document.getElementById('confirm_password');
        const newPassword = document.getElementById('new_password');
        if (confirmPassword && newPassword) {
            confirmPassword.addEventListener('input', () => {
                if (confirmPassword.value !== newPassword.value) {
                    confirmPassword.setCustomValidity('Passwords do not match');
                } else {
                    confirmPassword.setCustomValidity('');
                }
            });
        }
    }

    // Toggle edit mode for a section
    toggleEdit(section) {
        const isCurrentlyEditing = this.isEditing[section];
        
        if (isCurrentlyEditing) {
            this.cancelEdit(section);
        } else {
            this.enterEditMode(section);
        }
    }

    enterEditMode(section) {
        this.isEditing[section] = true;
        
        // Hide display spans and show input fields
        const fields = this.getSectionFields(section);
        fields.forEach(field => {
            const display = document.getElementById(`${field}-display`);
            const edit = document.getElementById(`${field}-edit`);
            
            if (display && edit) {
                display.style.display = 'none';
                edit.style.display = 'block';
                edit.focus();
            }
        });

        // Show action buttons
        const actions = document.getElementById(`${section}-actions`);
        if (actions) {
            actions.style.display = 'flex';
        }

        // Update edit button
        const editBtn = document.querySelector(`[onclick="toggleEdit('${section}')"]`);
        if (editBtn) {
            editBtn.innerHTML = '<i class="fas fa-times"></i> Cancel';
            editBtn.className = 'edit-btn btn-secondary';
        }
    }

    cancelEdit(section) {
        this.isEditing[section] = false;
        
        // Show display spans and hide input fields, reset values
        const fields = this.getSectionFields(section);
        fields.forEach(field => {
            const display = document.getElementById(`${field}-display`);
            const edit = document.getElementById(`${field}-edit`);
            
            if (display && edit) {
                // Reset input value to original
                const originalValue = display.textContent.trim();
                if (originalValue !== 'Not provided') {
                    edit.value = originalValue;
                }
                
                display.style.display = 'block';
                edit.style.display = 'none';
            }
        });

        // Hide action buttons
        const actions = document.getElementById(`${section}-actions`);
        if (actions) {
            actions.style.display = 'none';
        }

        // Update edit button
        const editBtn = document.querySelector(`[onclick="toggleEdit('${section}')"]`);
        if (editBtn) {
            editBtn.innerHTML = '<i class="fas fa-edit"></i> Edit';
            editBtn.className = 'edit-btn';
        }
    }

    getSectionFields(section) {
        const fieldMap = {
            'personal': ['first_name', 'last_name', 'phone', 'date_of_birth']
        };
        return fieldMap[section] || [];
    }

    async saveChanges(section) {
        const fields = this.getSectionFields(section);
        const data = {};

        // Collect data from input fields
        fields.forEach(field => {
            const input = document.getElementById(`${field}-edit`);
            if (input) {
                data[field] = input.value.trim();
            }
        });

        // Validate data
        if (!this.validateData(data)) {
            return;
        }

        try {
            // Show loading state
            this.showLoading(section);

            // Make API request
            const response = await fetch((window.BASE_URL||'')+'/api/user/profile', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                // Update display values
                fields.forEach(field => {
                    const display = document.getElementById(`${field}-display`);
                    const input = document.getElementById(`${field}-edit`);
                    
                    if (display && input) {
                        let displayValue = input.value || 'Not provided';
                        
                        // Format date for display
                        if (field === 'date_of_birth' && input.value) {
                            const date = new Date(input.value);
                            displayValue = date.toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            });
                        }
                        
                        display.textContent = displayValue;
                    }
                });

                this.cancelEdit(section);
                this.showToast('Profile updated successfully!', 'success');
            } else {
                throw new Error(result.error || 'Failed to update profile');
            }
        } catch (error) {
            console.error('Error updating profile:', error);
            this.showToast(error.message || 'Failed to update profile', 'error');
        } finally {
            this.hideLoading(section);
        }
    }

    validateData(data) {
        // Basic validation
        if (data.first_name && data.first_name.length < 2) {
            this.showToast('First name must be at least 2 characters long', 'warning');
            return false;
        }

        if (data.last_name && data.last_name.length < 2) {
            this.showToast('Last name must be at least 2 characters long', 'warning');
            return false;
        }

        if (data.phone && data.phone.length > 0) {
            const phoneRegex = /^[\+]?[\d\s\-\(\)]+$/;
            if (!phoneRegex.test(data.phone)) {
                this.showToast('Please enter a valid phone number', 'warning');
                return false;
            }
        }

        if (data.date_of_birth && data.date_of_birth.length > 0) {
            const date = new Date(data.date_of_birth + 'T00:00:00');
            const today = new Date();
            
            // Check if the date is valid
            if (isNaN(date.getTime())) {
                this.showToast('Please enter a valid date', 'warning');
                return false;
            }
            
            // Calculate age properly
            let age = today.getFullYear() - date.getFullYear();
            const monthDiff = today.getMonth() - date.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < date.getDate())) {
                age--;
            }
            
            // Date must not be in the future
            if (date > today) {
                this.showToast('Date of birth cannot be in the future', 'warning');
                return false;
            }
            
            // Age must be reasonable (5-120)
            if (age < 5 || age > 120) {
                this.showToast('Please enter a valid date of birth (age must be between 5 and 120)', 'warning');
                return false;
            }
        }

        return true;
    }

    async handleAvatarUpload(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Validate file
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        const maxSize = 5 * 1024 * 1024; // 5MB

        if (!allowedTypes.includes(file.type)) {
            this.showToast('Please select a valid image file (JPEG, PNG, or GIF)', 'warning');
            return;
        }

        if (file.size > maxSize) {
            this.showToast('Image size must be less than 5MB', 'warning');
            return;
        }

        try {
            // Show loading state
            const uploadBtn = document.querySelector('.avatar-upload-btn');
            const originalContent = uploadBtn.innerHTML;
            uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            uploadBtn.disabled = true;

            // Create form data
            const formData = new FormData();
            formData.append('avatar', file);

            // Upload avatar
            const response = await fetch((window.BASE_URL||'')+'/api/user/avatar', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // Update avatar image
                const avatar = document.getElementById('profile-avatar');
                if (avatar) {
                    avatar.src = (window.BASE_URL||'') + result.avatar_url + '?t=' + Date.now(); // Cache busting
                }

                this.showToast('Profile picture updated successfully!', 'success');
            } else {
                throw new Error(result.error || 'Failed to upload avatar');
            }
        } catch (error) {
            console.error('Error uploading avatar:', error);
            this.showToast(error.message || 'Failed to upload profile picture', 'error');
        } finally {
            // Reset button
            const uploadBtn = document.querySelector('.avatar-upload-btn');
            uploadBtn.innerHTML = '<i class="fas fa-camera"></i>';
            uploadBtn.disabled = false;
        }
    }

    async handlePasswordChange(event) {
        event.preventDefault();
        
        const currentPassword = document.getElementById('current_password').value;
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        // Validate passwords
        if (newPassword !== confirmPassword) {
            this.showToast('New passwords do not match', 'warning');
            return;
        }

        if (newPassword.length < 8) {
            this.showToast('New password must be at least 8 characters long', 'warning');
            return;
        }

        try {
            // Show loading state
            const submitBtn = event.target.querySelector('button[type="submit"]');
            const originalContent = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Changing...';
            submitBtn.disabled = true;

            // Make API request
            const response = await fetch((window.BASE_URL||'')+'/api/user/change-password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    current_password: currentPassword,
                    new_password: newPassword
                })
            });

            const result = await response.json();

            if (result.success) {
                this.closePasswordModal();
                document.getElementById('password-form').reset();
                this.showToast('Password changed successfully!', 'success');
            } else {
                throw new Error(result.error || 'Failed to change password');
            }
        } catch (error) {
            console.error('Error changing password:', error);
            this.showToast(error.message || 'Failed to change password', 'error');
        } finally {
            // Reset button
            const submitBtn = event.target.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-save"></i> Change Password';
            submitBtn.disabled = false;
        }
    }

    async loadRecentActivity() {
        try {
            const activityList = document.getElementById('activity-list');
            if (!activityList) return;

            // Fetch real data from API
            const activities = [];

            try {
                const bookingsRes = await fetch((window.BASE_URL||'')+'/api/user/ground-bookings');
                const bookingsData = await bookingsRes.json();
                if (bookingsData.success && bookingsData.bookings) {
                    bookingsData.bookings.slice(0, 3).forEach(b => {
                        activities.push({
                            icon: 'fas fa-calendar-check',
                            iconColor: b.status === 'confirmed' ? '#28a745' : b.status === 'pending' ? '#ffc107' : '#6c757d',
                            title: `Ground Booking ${b.status ? b.status.charAt(0).toUpperCase() + b.status.slice(1) : ''}`,
                            description: `Booking at ${b.facility_name || b.ground_name || 'Sports Ground'} on ${b.booking_date || ''}`,
                            time: this.timeAgo(b.created_at)
                        });
                    });
                }
            } catch(e) { /* bookings API may not exist */ }

            try {
                const ordersRes = await fetch((window.BASE_URL||'')+'/api/user/orders');
                const ordersData = await ordersRes.json();
                if (ordersData.success && ordersData.orders) {
                    ordersData.orders.slice(0, 3).forEach(o => {
                        activities.push({
                            icon: 'fas fa-shopping-cart',
                            iconColor: o.status === 'delivered' ? '#28a745' : o.status === 'processing' ? '#17a2b8' : '#ffc107',
                            title: `Order ${o.order_number || '#' + o.id}`,
                            description: `Order ${o.status || 'placed'} - LKR ${parseFloat(o.total_amount || 0).toFixed(2)}`,
                            time: this.timeAgo(o.created_at)
                        });
                    });
                }
            } catch(e) { /* orders API may not exist */ }

            // Sort by most recent
            activities.sort((a, b) => (b._ts || 0) - (a._ts || 0));

            if (activities.length === 0) {
                activityList.innerHTML = `
                    <div class="activity-empty" style="text-align:center;padding:2rem;color:#94a3b8;">
                        <i class="fas fa-inbox" style="font-size:2rem;margin-bottom:12px;display:block;opacity:0.5;"></i>
                        <p>No recent activity yet</p>
                        <small>Your bookings and orders will appear here</small>
                    </div>`;
                return;
            }

            const activityHTML = activities.slice(0, 5).map(activity => `
                <div class="activity-item">
                    <div class="activity-icon" style="background-color: ${activity.iconColor}">
                        <i class="${activity.icon}"></i>
                    </div>
                    <div class="activity-details">
                        <h4>${activity.title}</h4>
                        <p>${activity.description}</p>
                        <small>${activity.time}</small>
                    </div>
                </div>
            `).join('');

            activityList.innerHTML = activityHTML;
        } catch (error) {
            console.error('Error loading activity:', error);
            const activityList = document.getElementById('activity-list');
            if (activityList) {
                activityList.innerHTML = `
                    <div class="activity-empty" style="text-align:center;padding:2rem;color:#94a3b8;">
                        <i class="fas fa-inbox" style="font-size:2rem;margin-bottom:12px;display:block;opacity:0.5;"></i>
                        <p>No recent activity yet</p>
                        <small>Your bookings and orders will appear here</small>
                    </div>`;
            }
        }
    }

    timeAgo(dateStr) {
        if (!dateStr) return '';
        const date = new Date(dateStr);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);
        if (seconds < 60) return 'Just now';
        if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
        if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
        if (seconds < 604800) return Math.floor(seconds / 86400) + ' days ago';
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    showLoading(section) {
        const actions = document.getElementById(`${section}-actions`);
        if (actions) {
            const saveBtn = actions.querySelector('.btn-primary');
            if (saveBtn) {
                saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                saveBtn.disabled = true;
            }
        }
    }

    hideLoading(section) {
        const actions = document.getElementById(`${section}-actions`);
        if (actions) {
            const saveBtn = actions.querySelector('.btn-primary');
            if (saveBtn) {
                saveBtn.innerHTML = '<i class="fas fa-save"></i> Save Changes';
                saveBtn.disabled = false;
            }
        }
    }

    showToast(message, type = 'info') {
        // Remove existing toast
        const existingToast = document.querySelector('.toast');
        if (existingToast) {
            existingToast.remove();
        }

        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.textContent = message;

        // Add to page
        document.body.appendChild(toast);

        // Show toast
        setTimeout(() => toast.classList.add('show'), 100);

        // Hide and remove toast
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    changePassword() {
        const modal = document.getElementById('password-modal');
        if (modal) {
            modal.style.display = 'block';
            document.getElementById('current_password').focus();
        }
    }

    closePasswordModal() {
        const modal = document.getElementById('password-modal');
        if (modal) {
            modal.style.display = 'none';
            document.getElementById('password-form').reset();
        }
    }
}

// Global functions for onclick handlers
function toggleEdit(section) {
    window.userProfile.toggleEdit(section);
}

function saveChanges(section) {
    window.userProfile.saveChanges(section);
}

function cancelEdit(section) {
    window.userProfile.cancelEdit(section);
}

function changePassword() {
    window.userProfile.changePassword();
}

function closePasswordModal() {
    window.userProfile.closePasswordModal();
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.userProfile = new UserProfile();
});