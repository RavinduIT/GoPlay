class CoachProfile {
    constructor() {
        this.profileData = {};
        this.isEditing = false;
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadProfileData();
    }

    bindEvents() {
        // Edit profile button
        const editBtn = document.getElementById('editProfileBtn');
        if (editBtn) {
            editBtn.addEventListener('click', () => {
                this.openModal('editProfileModal');
            });
        }

        // Avatar change
        const avatarInput = document.getElementById('avatarInput');
        if (avatarInput) {
            avatarInput.addEventListener('change', (e) => {
                this.handleAvatarChange(e);
            });
        }

        // Section edit buttons
        document.querySelectorAll('.edit-section-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const section = btn.getAttribute('onclick')?.match(/'([^']+)'/)?.[1];
                if (section) {
                    this.editSection(section);
                }
            });
        });

        // Modal close events
        document.querySelectorAll('.modal-close').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const modal = e.target.closest('.modal');
                if (modal) this.closeModal(modal.id);
            });
        });

        // Form submissions
        document.addEventListener('submit', (e) => {
            if (e.target.id === 'editProfileForm') {
                e.preventDefault();
                this.saveProfile();
            }
        });

        // Click outside modal to close
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal')) {
                this.closeModal(e.target.id);
            }
        });

        // ESC key to close modal
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const openModal = document.querySelector('.modal[style*="flex"]');
                if (openModal) {
                    this.closeModal(openModal.id);
                }
            }
        });
    }

    async loadProfileData() {
        try {
            const response = await fetch('/api/coach/profile');
            this.profileData = await response.json();
            this.updateProfileDisplay();
        } catch (error) {
            console.error('Error loading profile data:', error);
            this.// showToast('Error loading profile data', 'error');
            // Use mock data as fallback
            this.profileData = this.getMockProfileData();
            this.updateProfileDisplay();
        }
    }

    getMockProfileData() {
        return {
            fullName: 'Lasith Malinga',
            email: 'lasith.malinga@email.com',
            phone: '+94 71 123 4567',
            location: 'Colombo, Sri Lanka',
            dateOfBirth: 'August 28, 1983',
            gender: 'Male',
            specialization: 'Cricket Coach & Former International Player',
            experience: '5',
            license: 'Level 3 Certified',
            languages: 'English, Sinhala, Tamil',
            hourlyRate: '¹800 - ¹1,500',
            bio: 'Former international cricket player with over 15 years of professional experience. Specialized in fast bowling techniques and youth development. I have coached players at various levels, from beginners to professional cricketers.',
            avatar: '/public/assets/images/coach-avatar.jpg',
            stats: {
                rating: 4.9,
                students: 45,
                years: 5,
                sessions: 230
            },
            specializations: ['Fast Bowling', 'Cricket Fundamentals', 'Match Strategy', 'Youth Development'],
            achievements: [
                {
                    type: 'certificate',
                    title: 'Level 3 Cricket Coaching Certificate',
                    organization: 'Sri Lanka Cricket Board',
                    year: '2019'
                },
                {
                    type: 'award',
                    title: 'Best Youth Coach Award',
                    organization: 'Provincial Cricket Association',
                    year: '2021'
                },
                {
                    type: 'medal',
                    title: 'International Playing Career',
                    organization: 'Sri Lankan National Team',
                    year: '2004-2019'
                }
            ],
            availability: {
                monday: { available: true, startTime: '9:00 AM', endTime: '6:00 PM' },
                tuesday: { available: true, startTime: '9:00 AM', endTime: '6:00 PM' },
                wednesday: { available: true, startTime: '9:00 AM', endTime: '6:00 PM' },
                thursday: { available: true, startTime: '9:00 AM', endTime: '6:00 PM' },
                friday: { available: true, startTime: '9:00 AM', endTime: '6:00 PM' },
                saturday: { available: false },
                sunday: { available: false }
            }
        };
    }

    updateProfileDisplay() {
        // Update profile header
        const coachName = document.querySelector('.coach-name');
        const coachSpecialization = document.querySelector('.coach-specialization');
        
        if (coachName) coachName.textContent = this.profileData.fullName || 'Coach Name';
        if (coachSpecialization) coachSpecialization.textContent = this.profileData.specialization || 'Sports Coach';

        // Update stats
        if (this.profileData.stats) {
            this.updateStats(this.profileData.stats);
        }

        // Update personal information
        this.updateInfoSection();

        // Update specializations
        this.updateSpecializations(this.profileData.specializations || []);

        // Update bio
        const bioText = document.querySelector('.bio-text');
        if (bioText) {
            bioText.textContent = this.profileData.bio || 'No bio available';
        }

        // Update achievements
        this.updateAchievements(this.profileData.achievements || []);

        // Update weekly availability
        this.updateWeeklySchedule(this.profileData.availability || {});

        // Update avatar
        const profileAvatar = document.getElementById('profileAvatar');
        if (profileAvatar && this.profileData.avatar) {
            profileAvatar.src = this.profileData.avatar;
        }
    }

    updateStats(stats) {
        // Update stat numbers in profile header
        const statElements = document.querySelectorAll('.stat-number');
        if (statElements.length >= 4) {
            statElements[0].textContent = stats.rating || '4.9';
            statElements[1].textContent = stats.students || '45';
            statElements[2].textContent = stats.years || '5';
            statElements[3].textContent = stats.sessions || '230';
        }
    }

    updateInfoSection() {
        // Update personal info section
        const personalInfoItems = document.querySelectorAll('.profile-card:first-of-type .info-item span');
        if (personalInfoItems.length >= 6) {
            personalInfoItems[0].textContent = this.profileData.fullName || 'Not specified';
            personalInfoItems[1].textContent = this.profileData.email || 'Not specified';
            personalInfoItems[2].textContent = this.profileData.phone || 'Not specified';
            personalInfoItems[3].textContent = this.profileData.location || 'Not specified';
            personalInfoItems[4].textContent = this.profileData.dateOfBirth || 'Not specified';
            personalInfoItems[5].textContent = this.profileData.gender || 'Not specified';
        }

        // Update professional info section
        const professionalInfoItems = document.querySelectorAll('.profile-card:nth-of-type(2) .info-item span');
        if (professionalInfoItems.length >= 4) {
            professionalInfoItems[0].textContent = (this.profileData.experience || '5') + '+ Years';
            professionalInfoItems[1].textContent = this.profileData.license || 'Level 3 Certified';
            professionalInfoItems[2].textContent = this.profileData.languages || 'English';
            professionalInfoItems[3].textContent = this.profileData.hourlyRate || '¹800 - ¹1,500';
        }
    }

    updateSpecializations(specializations) {
        const container = document.querySelector('.specialization-tags');
        if (container) {
            container.innerHTML = specializations.map(spec => 
                `<span class="tag">${spec}</span>`
            ).join('');
        }
    }

    updateAchievements(achievements) {
        const container = document.querySelector('.achievements-list');
        if (container) {
            container.innerHTML = achievements.map(achievement => `
                <div class="achievement-item">
                    <div class="achievement-icon">
                        <i class="fas ${this.getAchievementIcon(achievement.type)}"></i>
                    </div>
                    <div class="achievement-details">
                        <h4>${achievement.title}</h4>
                        <p>${achievement.organization} - ${achievement.year}</p>
                    </div>
                </div>
            `).join('');
        }
    }

    getAchievementIcon(type) {
        const icons = {
            'certificate': 'fa-certificate',
            'award': 'fa-trophy',
            'medal': 'fa-medal',
            'license': 'fa-id-card',
            'experience': 'fa-star'
        };
        return icons[type] || 'fa-award';
    }

    updateWeeklySchedule(availability) {
        const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        
        days.forEach((day, index) => {
            const dayElement = document.querySelector(`.schedule-day:nth-child(${index + 1})`);
            if (dayElement) {
                const timeSlot = dayElement.querySelector('.time-slot');
                const daySchedule = availability[day];
                
                if (daySchedule && daySchedule.available) {
                    timeSlot.textContent = `${daySchedule.startTime} - ${daySchedule.endTime}`;
                    dayElement.classList.remove('unavailable');
                } else {
                    timeSlot.textContent = 'Unavailable';
                    dayElement.classList.add('unavailable');
                }
            }
        });
    }

    // Avatar Management
    changeAvatar() {
        document.getElementById('avatarInput').click();
    }

    async handleAvatarChange(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Validate file type and size
        if (!file.type.startsWith('image/')) {
            this.// showToast('Please select an image file', 'error');
            return;
        }

        if (file.size > 5 * 1024 * 1024) { // 5MB limit
            this.// showToast('Image size must be less than 5MB', 'error');
            return;
        }

        try {
            // For demo purposes, just show the selected image
            const reader = new FileReader();
            reader.onload = (e) => {
                document.getElementById('profileAvatar').src = e.target.result;
                this.// showToast('Avatar updated successfully', 'success');
            };
            reader.readAsDataURL(file);

            // In a real application, you would upload to the server:
            // const formData = new FormData();
            // formData.append('avatar', file);
            // const response = await fetch('/api/coach/profile/avatar', {
            //     method: 'POST',
            //     body: formData
            // });

        } catch (error) {
            console.error('Error uploading avatar:', error);
            this.// showToast('Error uploading avatar', 'error');
        }
    }

    // Section Editing
    editSection(section) {
        switch (section) {
            case 'personal':
            case 'professional':
            case 'bio':
                this.populateEditForm();
                this.openModal('editProfileModal');
                break;
            case 'certifications':
                this.// showToast('Certifications management coming soon', 'info');
                break;
            default:
                this.openModal('editProfileModal');
        }
    }

    // Form Management
    populateEditForm() {
        const form = document.getElementById('editProfileForm');
        if (!form) return;

        // Populate form fields with current data
        const fields = {
            'fullName': this.profileData.fullName || '',
            'email': this.profileData.email || '',
            'phone': this.profileData.phone || '',
            'location': this.profileData.location || '',
            'specialization': this.profileData.specialization || '',
            'experience': this.profileData.experience || '',
            'hourlyRate': this.profileData.hourlyRate || '',
            'languages': this.profileData.languages || '',
            'bio': this.profileData.bio || ''
        };

        Object.entries(fields).forEach(([name, value]) => {
            const field = form.querySelector(`[name="${name}"]`);
            if (field) {
                field.value = value;
            }
        });
    }

    async saveProfile() {
        const form = document.getElementById('editProfileForm');
        const formData = new FormData(form);
        const profileData = Object.fromEntries(formData.entries());

        if (!this.validateProfileForm(profileData)) {
            return;
        }

        try {
            // For demo purposes, just update local data
            this.profileData = { ...this.profileData, ...profileData };
            this.updateProfileDisplay();
            this.closeModal('editProfileModal');
            this.// showToast('Profile updated successfully', 'success');

            // In a real application, you would save to the server:
            // const response = await fetch('/api/coach/profile', {
            //     method: 'PUT',
            //     headers: { 'Content-Type': 'application/json' },
            //     body: JSON.stringify(profileData)
            // });

        } catch (error) {
            console.error('Error updating profile:', error);
            this.// showToast('Error updating profile: ' + error.message, 'error');
        }
    }

    validateProfileForm(data) {
        const required = ['fullName', 'email', 'phone'];
        
        for (let field of required) {
            if (!data[field] || data[field].trim() === '') {
                this.// showToast(`Please fill in the ${field.replace(/([A-Z])/g, ' $1').toLowerCase()}`, 'error');
                return false;
            }
        }

        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(data.email)) {
            this.// showToast('Please enter a valid email address', 'error');
            return false;
        }

        // Phone validation
        const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
        if (!phoneRegex.test(data.phone.replace(/[\s\-\(\)]/g, ''))) {
            this.// showToast('Please enter a valid phone number', 'error');
            return false;
        }

        return true;
    }

    // Modal Management
    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            
            // Add fade-in animation
            setTimeout(() => {
                modal.classList.add('fade-in');
            }, 10);
        }
    }

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('fade-in');
            
            setTimeout(() => {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
                
                // Reset forms
                const forms = modal.querySelectorAll('form');
                forms.forEach(form => form.reset());
            }, 300);
        }
    }

    // Utility Methods
    animateValue(element, start, end, duration) {
        const startTime = performance.now();
        
        const step = (currentTime) => {
            const progress = Math.min((currentTime - startTime) / duration, 1);
            const current = start + (end - start) * this.easeOutQuart(progress);
            
            element.textContent = Math.floor(current);
            
            if (progress < 1) {
                requestAnimationFrame(step);
            }
        };
        
        requestAnimationFrame(step);
    }

    easeOutQuart(t) {
        return 1 - (--t) * t * t * t;
    }

    // Toast Notifications
    // showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation-triangle' : 'info'}"></i>
            <span>${message}</span>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.add('show');
        }, 100);
        
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
}

// Initialize profile manager when DOM is loaded
let coachProfile;
document.addEventListener('DOMContentLoaded', () => {
    coachProfile = new CoachProfile();
});