// Coach booking functionality
function loadBookCoachPage() {
    // Load coach booking page
}

function initializeCoachSelection() {
    // Initialize coach selection
}

function handleCoachBooking() {
    // Handle coach booking submission
}

// Export coach booking functions
window.bookCoachPage = {
    load: loadBookCoachPage,
    init: initializeBookCoachPage
};

function initializeBookCoachPage() {
    // Initialize coach booking page
}

// Professional Coach Booking Application
class CoachBookingApp {
    constructor() {
        this.coaches = [];
        this.filteredCoaches = [];
        this.currentView = 'grid';
        this.selectedCoach = null;
        this.init();
    }

    async init() {
        try {
            await this.loadCoaches();
            this.setupEventListeners();
            this.hideLoadingSpinner();
            this.renderCoaches();
        } catch (error) {
            console.error('Failed to initialize coach booking:', error);
            this.showError('Failed to load coaches');
        }
    }

    async loadCoaches() {
        try {
            // First, check if coaches exist in localStorage
            const localCoaches = localStorage.getItem('coaches');
            
            if (!localCoaches) {
                // If no coaches in localStorage, load from JSON file and save to localStorage
                await this.syncCoachesFromJsonToLocalStorage();
            }
            
            // Load coaches from localStorage
            const storedCoaches = JSON.parse(localStorage.getItem('coaches') || '[]');
            this.coaches = storedCoaches;

            // Filter only active coaches
            this.coaches = this.coaches.filter(coach => coach.status === 'Active');
            this.filteredCoaches = [...this.coaches];

            // Populate filter options
            this.populateFilterOptions();
            
        } catch (error) {
            console.error('Error loading coaches:', error);
            // Fallback to empty array if both localStorage and JSON fail
            this.coaches = [];
            this.filteredCoaches = [];
        }
    }

    async syncCoachesFromJsonToLocalStorage() {
        try {
            const response = await fetch('../data/coaches.json');
            if (response.ok) {
                const jsonCoaches = await response.json();
                localStorage.setItem('coaches', JSON.stringify(jsonCoaches));
                console.log('Coaches synced from JSON to localStorage');
            }
        } catch (error) {
            console.error('Failed to sync coaches from JSON:', error);
        }
    }

    populateFilterOptions() {
        // Get unique sports and locations
        const sports = [...new Set(this.coaches.flatMap(coach => coach.specialization))].sort();
        const locations = [...new Set(this.coaches.map(coach => coach.location))].sort();

        // Populate sport filter
        const sportSelect = document.getElementById('sportFilter');
        sports.forEach(sport => {
            const option = document.createElement('option');
            option.value = sport;
            option.textContent = sport;
            sportSelect.appendChild(option);
        });

        // Populate location filter
        const locationSelect = document.getElementById('locationFilter');
        locations.forEach(location => {
            const option = document.createElement('option');
            option.value = location;
            option.textContent = location;
            locationSelect.appendChild(option);
        });
    }

    setupEventListeners() {
        // Filter listeners
        document.getElementById('searchInput').addEventListener('input', () => this.applyFilters());
        document.getElementById('sportFilter').addEventListener('change', () => this.applyFilters());
        document.getElementById('locationFilter').addEventListener('change', () => this.applyFilters());
        document.getElementById('priceFilter').addEventListener('change', () => this.applyFilters());
        document.getElementById('ratingFilter').addEventListener('change', () => this.applyFilters());
        document.getElementById('sortFilter').addEventListener('change', () => this.applyFilters());

        // View toggle listeners
        document.getElementById('gridView').addEventListener('click', () => this.setView('grid'));
        document.getElementById('listView').addEventListener('click', () => this.setView('list'));

        // Modal listeners
        document.getElementById('closeModal').addEventListener('click', () => this.closeBookingModal());
        document.getElementById('cancelBooking').addEventListener('click', () => this.closeBookingModal());
        document.getElementById('bookingForm').addEventListener('submit', (e) => this.handleBookingSubmit(e));

        // Duration change listener for price calculation
        document.getElementById('sessionDuration').addEventListener('change', () => this.updateBookingSummary());

        // Modal backdrop click to close
        document.getElementById('bookingModal').addEventListener('click', (e) => {
            if (e.target.id === 'bookingModal') {
                this.closeBookingModal();
            }
        });
    }

    applyFilters() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const sport = document.getElementById('sportFilter').value;
        const location = document.getElementById('locationFilter').value;
        const priceRange = document.getElementById('priceFilter').value;
        const ratingFilter = document.getElementById('ratingFilter').value;
        const sort = document.getElementById('sortFilter').value;

        this.filteredCoaches = this.coaches.filter(coach => {
            const matchesSearch = !searchTerm ||
                coach.name.toLowerCase().includes(searchTerm) ||
                coach.bio.toLowerCase().includes(searchTerm) ||
                coach.specialization.some(spec => spec.toLowerCase().includes(searchTerm)) ||
                coach.location.toLowerCase().includes(searchTerm);

            const matchesSport = !sport || coach.specialization.includes(sport);
            const matchesLocation = !location || coach.location === location;

            let matchesPrice = true;
            if (priceRange) {
                if (priceRange === '70+') {
                    matchesPrice = coach.hourlyRate >= 70;
                } else {
                    const [min, max] = priceRange.split('-').map(Number);
                    matchesPrice = coach.hourlyRate >= min && coach.hourlyRate <= max;
                }
            }

            const matchesRating = !ratingFilter || coach.rating >= parseFloat(ratingFilter);

            return matchesSearch && matchesSport && matchesLocation && matchesPrice && matchesRating;
        });

        // Apply sorting
        this.sortCoaches(sort);
        this.renderCoaches();
    }

    sortCoaches(sortType) {
        switch (sortType) {
            case 'name-asc':
                this.filteredCoaches.sort((a, b) => a.name.localeCompare(b.name));
                break;
            case 'price-asc':
                this.filteredCoaches.sort((a, b) => a.hourlyRate - b.hourlyRate);
                break;
            case 'price-desc':
                this.filteredCoaches.sort((a, b) => b.hourlyRate - a.hourlyRate);
                break;
            case 'rating-desc':
                this.filteredCoaches.sort((a, b) => b.rating - a.rating);
                break;
            case 'experience-desc':
                this.filteredCoaches.sort((a, b) => {
                    const aExp = parseInt(a.experience);
                    const bExp = parseInt(b.experience);
                    return bExp - aExp;
                });
                break;
        }
    }

    renderCoaches() {
        const grid = document.getElementById('coachGrid');
        const noResults = document.getElementById('noResults');

        if (this.filteredCoaches.length === 0) {
            grid.style.display = 'none';
            noResults.style.display = 'block';
            this.updateCoachCount(0);
            return;
        }

        grid.style.display = 'grid';
        noResults.style.display = 'none';

        grid.innerHTML = this.filteredCoaches.map(coach =>
            this.createCoachCard(coach)
        ).join('');

        this.updateCoachCount(this.filteredCoaches.length);
    }

    createCoachCard(coach) {
        const experienceYears = parseInt(coach.experience) || 0;

        return `
                    <div class="coach-card">
                        <div class="coach-image">
                            <img src="../assets/images/${coach.profileImage}" alt="${coach.name}" 
                                 onerror="this.src='https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=300&h=300&fit=crop&crop=face'">
                            <div class="coach-actions">
                                <button class="action-btn profile-btn" onclick="coachApp.viewProfile(${coach.id})">
                                    <i class="fas fa-user"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="coach-info">
                            <div class="coach-header">
                                <h3 class="coach-name">${coach.name}</h3>
                                <div class="coach-specialization">${coach.specialization.join(' • ')}</div>
                            </div>
                            
                            <div class="coach-meta">
                                <div class="coach-rating">
                                    <div class="stars">
                                        ${this.generateStars(coach.rating)}
                                    </div>
                                    <span class="rating-text">${coach.rating} (${coach.totalStudents} students)</span>
                                </div>
                                <div class="coach-details">
                                    <span class="experience"><i class="fas fa-medal"></i> ${coach.experience} experience</span>
                                    <span class="location"><i class="fas fa-map-marker-alt"></i> ${coach.location}</span>
                                </div>
                            </div>
                            
                            <p class="coach-bio">${coach.bio}</p>
                            
                            <div class="coach-certifications">
                                <h4>Certifications</h4>
                                <div class="cert-tags">
                                    ${coach.certifications.map(cert => `<span class="cert-tag">${cert}</span>`).join('')}
                                </div>
                            </div>
                            
                            <div class="coach-availability">
                                <h4>Available</h4>
                                <div class="availability-tags">
                                    ${coach.availability.map(day => `<span class="availability-tag">${day}</span>`).join('')}
                                </div>
                            </div>
                            
                            <div class="coach-pricing">
                                <span class="hourly-rate">$${coach.hourlyRate}/hour</span>
                            </div>
                            
                            <div class="coach-buttons">
                                <button class="btn-secondary" onclick="coachApp.viewProfile(${coach.id})">
                                    <i class="fas fa-user"></i> View Profile
                                </button>
                                <button class="btn-primary" onclick="coachApp.openBookingModal(${coach.id})">
                                    <i class="fas fa-calendar-plus"></i> Book Session
                                </button>
                            </div>
                        </div>
                    </div>
                `;
    }

    generateStars(rating) {
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            if (i <= rating) {
                stars += '<i class="fas fa-star"></i>';
            } else if (i - 0.5 <= rating) {
                stars += '<i class="fas fa-star-half-alt"></i>';
            } else {
                stars += '<i class="far fa-star"></i>';
            }
        }
        return stars;
    }

    setView(viewType) {
        this.currentView = viewType;
        const grid = document.getElementById('coachGrid');
        const gridBtn = document.getElementById('gridView');
        const listBtn = document.getElementById('listView');

        if (viewType === 'grid') {
            grid.className = 'coach-grid grid-view';
            gridBtn.classList.add('active');
            listBtn.classList.remove('active');
        } else {
            grid.className = 'coach-grid list-view';
            listBtn.classList.add('active');
            gridBtn.classList.remove('active');
        }

        this.renderCoaches();
    }


    openBookingModal(coachId) {
        const coach = this.coaches.find(c => c.id === coachId);
        if (!coach) return;

        this.selectedCoach = coach;

        // Populate modal with coach info
        document.getElementById('modalCoachImage').src = `../assets/images/${coach.profileImage}`;
        document.getElementById('modalCoachImage').onerror = function () {
            this.src = 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=200&fit=crop&crop=face';
        };
        document.getElementById('modalCoachName').textContent = coach.name;
        document.getElementById('modalCoachSpecialization').textContent = coach.specialization.join(' • ');
        document.getElementById('modalCoachRate').textContent = coach.hourlyRate;

        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('sessionDate').min = today;

        // Reset form
        document.getElementById('bookingForm').reset();
        this.updateBookingSummary();

        // Show modal
        document.getElementById('bookingModal').style.display = 'block';
    }

    closeBookingModal() {
        document.getElementById('bookingModal').style.display = 'none';
        this.selectedCoach = null;
    }

    updateBookingSummary() {
        if (!this.selectedCoach) return;

        const duration = parseInt(document.getElementById('sessionDuration').value) || 1;
        const rate = this.selectedCoach.hourlyRate;
        const total = duration * rate;

        document.getElementById('summaryDuration').textContent = `${duration} hour${duration > 1 ? 's' : ''}`;
        document.getElementById('summaryRate').textContent = rate;
        document.getElementById('summaryTotal').textContent = total;
    }

    async handleBookingSubmit(e) {
        e.preventDefault();

        if (!this.selectedCoach) return;

        const formData = new FormData(e.target);
        const bookingData = {
            coachId: this.selectedCoach.id,
            coachName: this.selectedCoach.name,
            date: document.getElementById('sessionDate').value,
            time: document.getElementById('sessionTime').value,
            duration: parseInt(document.getElementById('sessionDuration').value),
            sessionType: document.getElementById('sessionType').value,
            specialRequests: document.getElementById('specialRequests').value,
            totalCost: parseInt(document.getElementById('summaryTotal').textContent),
            bookingDate: new Date().toISOString()
        };

        try {
            // Show loading state
            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            submitBtn.disabled = true;

            // Simulate API call
            await new Promise(resolve => setTimeout(resolve, 2000));

            // Store booking in localStorage (in production, send to API)
            const existingBookings = JSON.parse(localStorage.getItem('coachBookings') || '[]');
            existingBookings.push({ ...bookingData, id: Date.now() });
            localStorage.setItem('coachBookings', JSON.stringify(existingBookings));

            // Show success message
            this.showBookingConfirmation(bookingData);
            this.closeBookingModal();

        } catch (error) {
            console.error('Booking failed:', error);
            alert('Failed to book session. Please try again.');
        } finally {
            // Reset button
            const submitBtn = e.target.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-credit-card"></i> Confirm Booking';
            submitBtn.disabled = false;
        }
    }

    showBookingConfirmation(bookingData) {
        const notification = document.createElement('div');
        notification.className = 'booking-notification';
        notification.innerHTML = `
                    <div class="notification-content">
                        <i class="fas fa-check-circle"></i>
                        <h4>Booking Confirmed!</h4>
                        <p>Your session with ${bookingData.coachName} has been booked for ${bookingData.date} at ${bookingData.time}</p>
                        <small>Total: $${bookingData.totalCost}</small>
                    </div>
                `;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.classList.add('show');
        }, 100);

        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }

    viewProfile(coachId) {
        console.log('View profile for coach:', coachId);
        // In production, navigate to coach profile page
        window.location.href = `/pages/coach-profile.html?id=${coachId}`;
    }


    updateCoachCount(count) {
        document.getElementById('coachCount').textContent =
            `${count} Coach${count !== 1 ? 'es' : ''} Available`;
    }

    hideLoadingSpinner() {
        document.getElementById('loadingSpinner').style.display = 'none';
    }

    showError(message) {
        const grid = document.getElementById('coachGrid');
        grid.innerHTML = `<div class="error-message"><i class="fas fa-exclamation-triangle"></i><p>${message}</p></div>`;
    }

    // Helper functions for admin dashboard to manage coaches in localStorage
    static addCoachToLocalStorage(newCoach) {
        const coaches = JSON.parse(localStorage.getItem('coaches') || '[]');
        // Generate new ID if not provided
        if (!newCoach.id) {
            const maxId = coaches.reduce((max, coach) => Math.max(max, coach.id || 0), 0);
            newCoach.id = maxId + 1;
        }
        coaches.push(newCoach);
        localStorage.setItem('coaches', JSON.stringify(coaches));
        console.log('Coach added to localStorage:', newCoach);
        return newCoach;
    }

    static updateCoachInLocalStorage(updatedCoach) {
        const coaches = JSON.parse(localStorage.getItem('coaches') || '[]');
        const index = coaches.findIndex(coach => coach.id === updatedCoach.id);
        if (index !== -1) {
            coaches[index] = updatedCoach;
            localStorage.setItem('coaches', JSON.stringify(coaches));
            console.log('Coach updated in localStorage:', updatedCoach);
            return true;
        }
        return false;
    }

    static removeCoachFromLocalStorage(coachId) {
        const coaches = JSON.parse(localStorage.getItem('coaches') || '[]');
        const filteredCoaches = coaches.filter(coach => coach.id !== coachId);
        localStorage.setItem('coaches', JSON.stringify(filteredCoaches));
        console.log('Coach removed from localStorage:', coachId);
        return true;
    }

    static getAllCoachesFromLocalStorage() {
        return JSON.parse(localStorage.getItem('coaches') || '[]');
    }

    // Method to refresh the current coach list (useful after admin changes)
    refreshCoachList() {
        this.loadCoaches().then(() => {
            this.renderCoaches();
        });
    }
}

// Initialize coach booking app when DOM is loaded
let coachApp;
document.addEventListener('DOMContentLoaded', () => {
    coachApp = new CoachBookingApp();
});

// Global functions for admin dashboard to manage coaches
window.CoachManager = {
    addCoach: (newCoach) => CoachBookingApp.addCoachToLocalStorage(newCoach),
    updateCoach: (updatedCoach) => CoachBookingApp.updateCoachInLocalStorage(updatedCoach),
    removeCoach: (coachId) => CoachBookingApp.removeCoachFromLocalStorage(coachId),
    getAllCoaches: () => CoachBookingApp.getAllCoachesFromLocalStorage(),
    refreshUI: () => {
        if (window.coachApp) {
            window.coachApp.refreshCoachList();
        }
    }
};

