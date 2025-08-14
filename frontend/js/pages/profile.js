
class ProfilePage {
    constructor() {
        this.isEditing = false;
        this.currentUser = null;
        this.init();
    }

    async init() {
        try {
            // Wait for auth to be ready
            if (typeof window.authInstance === 'undefined') {
                await new Promise(resolve => {
                    const checkAuth = () => {
                        if (typeof window.authInstance !== 'undefined') {
                            resolve();
                        } else {
                            setTimeout(checkAuth, 100);
                        }
                    };
                    checkAuth();
                });
            }

            // Check if user is authenticated
            if (!window.isAuthenticated()) {
                window.location.href = '../login.html';
                return;
            }

            this.currentUser = window.getCurrentUser();
            this.loadUserData();
            this.setupEventListeners();
        } catch (error) {
            console.error('Error initializing profile page:', error);
            this.showToast('Error', 'Failed to load profile', 'error');
        }
    }

    loadUserData() {
        if (!this.currentUser) return;

        // Update hero section
        document.getElementById('profileName').textContent = this.currentUser.name;
        document.getElementById('profileEmail').textContent = this.currentUser.email;
        document.getElementById('profileLocation').textContent = this.currentUser.location;
        document.getElementById('profileRole').textContent = this.currentUser.role;

        const joinDate = new Date(this.currentUser.joinDate);
        document.getElementById('profileJoinDate').textContent = `Member since ${joinDate.getFullYear()}`;

        // Update profile image
        if (this.currentUser.avatar) {
            document.getElementById('profileImage').src = this.currentUser.avatar;
        }

        // Update form fields
        document.getElementById('inputName').value = this.currentUser.name || '';
        document.getElementById('inputEmail').value = this.currentUser.email || '';
        document.getElementById('inputPhone').value = this.currentUser.phone || '';
        document.getElementById('inputLocation').value = this.currentUser.location || '';
        document.getElementById('inputRole').value = this.currentUser.role || 'Player';
        document.getElementById('inputBio').value = this.currentUser.bio || '';

        // Update sports display
        this.updateSportsDisplay();
    }

    loadStaticStats() {
        // Update stats with static data
        const statsElements = document.querySelectorAll('.stat-value');
        if (statsElements.length >= 4) {
            statsElements[0].textContent = STATIC_STATS.totalBookings;
            statsElements[1].textContent = STATIC_STATS.thisMonth;
            statsElements[2].textContent = `${STATIC_STATS.totalSpent}`;
            statsElements[3].textContent = STATIC_STATS.favoriteSport;
        }
    }

    loadStaticBookings() {
        const bookingsContainer = document.getElementById('recentBookings');
        bookingsContainer.innerHTML = '';

        STATIC_BOOKINGS.forEach(booking => {
            const bookingItem = document.createElement('div');
            bookingItem.className = 'booking-item';

            const statusClass = booking.status === 'Completed' ? 'status-completed' : 'status-upcoming';

            bookingItem.innerHTML = `
                        <div class="booking-header">
                            <div>
                                <div class="booking-name">${booking.name}</div>
                                <div class="booking-type">${booking.type}</div>
                            </div>
                            <span class="booking-status ${statusClass}">${booking.status}</span>
                        </div>
                        <div class="booking-footer">
                            <span>${booking.date} • ${booking.time}</span>
                            <span class="booking-amount">${booking.amount}</span>
                        </div>
                    `;

            bookingsContainer.appendChild(bookingItem);
        });

        // Add "View All Bookings" button
        const viewAllBtn = document.createElement('button');
        viewAllBtn.className = 'btn btn-outline';
        viewAllBtn.style.width = '100%';
        viewAllBtn.style.marginTop = '1rem';
        viewAllBtn.innerHTML = 'View All Bookings';
        bookingsContainer.appendChild(viewAllBtn);
    }

    updateSportsDisplay() {
        const sportsDisplay = document.getElementById('sportsDisplay');
        sportsDisplay.innerHTML = '';

        if (this.currentUser.sports && this.currentUser.sports.length > 0) {
            this.currentUser.sports.forEach(sport => {
                const badge = document.createElement('span');
                badge.className = `sport-badge ${this.isEditing ? 'removable' : ''}`;
                badge.innerHTML = `
                            ${sport}
                            ${this.isEditing ? '<span class="sport-remove">×</span>' : ''}
                        `;

                if (this.isEditing) {
                    badge.addEventListener('click', () => this.removeSport(sport));
                }

                sportsDisplay.appendChild(badge);
            });
        }
    }

    setupEventListeners() {
        // Edit profile button
        document.getElementById('editProfileBtn').addEventListener('click', () => {
            this.toggleEditMode();
        });

        // Save profile button
        document.getElementById('saveProfileBtn').addEventListener('click', () => {
            this.saveProfile();
        });

        // Cancel edit button
        document.getElementById('cancelEditBtn').addEventListener('click', () => {
            this.cancelEdit();
        });

        // Avatar upload
        document.getElementById('avatarUploadBtn').addEventListener('click', () => {
            document.getElementById('avatarInput').click();
        });

        document.getElementById('avatarInput').addEventListener('change', (e) => {
            this.handleImageUpload(e);
        });

        // Sports management
        document.getElementById('showAddSportBtn').addEventListener('click', () => {
            this.showAddSportSection();
        });

        document.getElementById('addSportBtn').addEventListener('click', () => {
            this.addSport();
        });

        document.getElementById('cancelSportBtn').addEventListener('click', () => {
            this.hideAddSportSection();
        });

        document.getElementById('newSportInput').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.addSport();
            }
        });

        // Form submission prevention
        document.getElementById('profileForm').addEventListener('submit', (e) => {
            e.preventDefault();
        });
    }

    toggleEditMode() {
        this.isEditing = !this.isEditing;

        const inputs = document.querySelectorAll('#profileForm input, #profileForm select, #profileForm textarea');
        const editBtn = document.getElementById('editProfileBtn');
        const formActions = document.getElementById('formActions');
        const showAddSportBtn = document.getElementById('showAddSportBtn');

        inputs.forEach(input => {
            input.disabled = !this.isEditing;
        });

        if (this.isEditing) {
            editBtn.innerHTML = '<i class="fas fa-times"></i> Cancel';
            editBtn.className = 'btn btn-outline';
            formActions.style.display = 'flex';
            showAddSportBtn.style.display = 'inline-flex';
        } else {
            editBtn.innerHTML = '<i class="fas fa-edit"></i> Edit Profile';
            editBtn.className = 'btn btn-outline';
            formActions.style.display = 'none';
            showAddSportBtn.style.display = 'none';
            this.hideAddSportSection();
        }

        this.updateSportsDisplay();
    }

    saveProfile() {
        const formData = {
            name: document.getElementById('inputName').value.trim(),
            email: document.getElementById('inputEmail').value.trim(),
            phone: document.getElementById('inputPhone').value.trim(),
            location: document.getElementById('inputLocation').value.trim(),
            role: document.getElementById('inputRole').value,
            bio: document.getElementById('inputBio').value.trim()
        };

        // Validate required fields
        if (!formData.name || !formData.email || !formData.phone || !formData.location) {
            this.showToast('Validation Error', 'Please fill in all required fields', 'error');
            return;
        }

        // Validate email format
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(formData.email)) {
            this.showToast('Validation Error', 'Please enter a valid email address', 'error');
            return;
        }

        try {
            const result = window.updateUser(formData);
            if (result.success) {
                this.currentUser = result.user;
                this.toggleEditMode();
                this.loadUserData();
                this.showToast('Success', 'Profile updated successfully!');
            } else {
                this.showToast('Error', result.error || 'Failed to update profile', 'error');
            }
        } catch (error) {
            console.error('Error saving profile:', error);
            this.showToast('Error', 'Failed to update profile', 'error');
        }
    }

    cancelEdit() {
        this.toggleEditMode();
        this.loadUserData(); // Reset form data
    }

    handleImageUpload(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Validate file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            this.showToast('Error', 'Please select an image smaller than 5MB', 'error');
            return;
        }

        // Validate file type
        if (!file.type.startsWith('image/')) {
            this.showToast('Error', 'Please select an image file', 'error');
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            const imageUrl = e.target.result;
            document.getElementById('profileImage').src = imageUrl;

            // Update user avatar
            const result = window.updateUser({ avatar: imageUrl });
            if (result.success) {
                this.currentUser = result.user;
                this.showToast('Success', 'Profile picture updated successfully!');
            } else {
                this.showToast('Error', 'Failed to update profile picture', 'error');
            }
        };
        reader.readAsDataURL(file);
    }

    showAddSportSection() {
        document.getElementById('addSportSection').style.display = 'flex';
        document.getElementById('showAddSportBtn').style.display = 'none';
        document.getElementById('newSportInput').focus();
    }

    hideAddSportSection() {
        document.getElementById('addSportSection').style.display = 'none';
        document.getElementById('showAddSportBtn').style.display = 'inline-flex';
        document.getElementById('newSportInput').value = '';
    }

    addSport() {
        const newSport = document.getElementById('newSportInput').value.trim();

        if (!newSport) {
            this.showToast('Error', 'Please enter a sport name', 'error');
            return;
        }

        if (this.currentUser.sports && this.currentUser.sports.includes(newSport)) {
            this.showToast('Error', 'This sport is already in your list', 'error');
            return;
        }

        const updatedSports = [...(this.currentUser.sports || []), newSport];
        const result = window.updateUser({ sports: updatedSports });

        if (result.success) {
            this.currentUser = result.user;
            this.updateSportsDisplay();
            this.hideAddSportSection();
            this.showToast('Success', `${newSport} added to your favorite sports!`);
        } else {
            this.showToast('Error', 'Failed to add sport', 'error');
        }
    }

    removeSport(sportToRemove) {
        const updatedSports = this.currentUser.sports.filter(sport => sport !== sportToRemove);
        const result = window.updateUser({ sports: updatedSports });

        if (result.success) {
            this.currentUser = result.user;
            this.updateSportsDisplay();
            this.showToast('Success', `${sportToRemove} removed from your favorite sports`);
        } else {
            this.showToast('Error', 'Failed to remove sport', 'error');
        }
    }

    showToast(title, message, type = 'success') {
        // Remove existing toasts
        const existingToasts = document.querySelectorAll('.toast');
        existingToasts.forEach(toast => toast.remove());

        const toast = document.createElement('div');
        toast.className = `toast ${type === 'error' ? 'error' : ''}`;

        toast.innerHTML = `
                    <div class="toast-title">${this.escapeHtml(title)}</div>
                    <div class="toast-description">${this.escapeHtml(message)}</div>
                `;

        document.body.appendChild(toast);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (toast.parentElement) {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.remove();
                    }
                }, 300);
            }
        }, 5000);
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize profile page when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new ProfilePage();
});

// Also initialize if DOM is already loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        new ProfilePage();
    });
} else {
    new ProfilePage();
}