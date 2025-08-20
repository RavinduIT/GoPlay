// Mock database data - replace with actual API calls
const coachData = {
    id: 1,
    name: "R.T Rathnayaka",
    title: "Football Coach",
    rating: 4.9,
    totalReviews: 156,
    experience_summary: "8 years experience",
    location: "Colombo",
    hourlyRate: 1500,
    avatar: null, // Will be set when image is uploaded
    about: {
        shortDescription: "Former professional player with 8 years coaching experience. Specialized in youth development and tactical training.",
        fullDescription: [
            "John Martinez is a dedicated football coach with over 8 years of professional coaching experience. He has worked with players of all levels, from youth teams to semi-professional clubs. His coaching philosophy focuses on developing technical skills, tactical awareness, and mental strength.",
            "John's journey in football began as a professional player where he played for several clubs in the regional league. After his playing career, he transitioned into coaching and has since helped hundreds of players improve their game."
        ],
        expertise: [
            "Individual skill development",
            "Team tactical strategies", 
            "Mental preparation and sports psychology",
            "Youth development programs",
            "Goalkeeper training",
            "Fitness and conditioning"
        ]
    },
    specializations: ["Youth Training", "Professional Coaching", "Tactical Analysis"],
    certifications: ["UEFA B License", "Sports Science Diploma", "First Aid Certified"],
    languages: [
        { name: "English", flag: "🇺🇸" },
        { name: "Spanish", flag: "🇪🇸" }
    ],
    achievements: [
        "Led youth team to regional championship (2022)",
        "Developed 15+ players who went on to professional contracts",
        "UEFA B license holder",
        "Sports Science Diploma graduate",
        "Over 500+ successful training sessions"
    ],
    experience: [
        {
            title: "Youth Development Coach",
            company: "City Football Academy",
            dates: "2020-2024",
            description: "Lead coach for U-16 and U-18 teams, focusing on technical development and tactical awareness."
        },
        {
            title: "Assistant Coach",
            company: "Regional Semi-Pro Club",
            dates: "2018-2020",
            description: "Assisted with team training, specialized in set pieces and defensive organization."
        },
        {
            title: "Private Coaching",
            company: "Independent",
            dates: "2016-Present",
            description: "Providing one-on-one coaching sessions for players of all ages and skill levels."
        }
    ],
    gallery: [
        { id: 1, alt: "Coach Training", placeholder: "Training Session" },
        { id: 2, alt: "With Team", placeholder: "Team Photo" },
        { id: 3, alt: "Practice Session", placeholder: "Practice" },
        { id: 4, alt: "Game Day", placeholder: "Game Day" }
    ],
    socialMedia: {
        facebook: "https://www.facebook.com/coachprofile",
        instagram: "https://www.instagram.com/coachprofile",
        whatsapp: "https://wa.me/94771234567"
    },
    booking: {
        sessionDuration: "60 minutes",
        responseTime: "Within 2 hours",
        cancellationPolicy: "24h notice",
        availableTimeSlots: ["09:00", "10:00", "11:00", "14:00", "15:00", "16:00", "17:00", "18:00", "19:00"],
        unavailableSlots: [] // Time slots that are booked
    }
};

// Review System Class
class ReviewSystem {
    constructor() {
        this.reviews = [
            {
                id: 1,
                name: "Sarah Johnson",
                rating: 5,
                text: "Excellent coaching! R.T helped me improve my technical skills tremendously. His training methods are excellent and he really cares about his players development.",
                date: "2024-01-15",
                avatar: "S"
            },
            {
                id: 2,
                name: "Mike Chen",
                rating: 4,
                text: "Great coach! My son's confidence and skills have improved dramatically since training with R.T. He has a great way of connecting with young players.",
                date: "2024-01-10",
                avatar: "M"
            },
            {
                id: 3,
                name: "Emma Rodriguez",
                rating: 5,
                text: "Very professional and knowledgeable coach. R.T helped me work on my positioning and tactical awareness. Great experience overall!",
                date: "2024-01-05",
                avatar: "E"
            },
            {
                id: 4,
                name: "David Thompson",
                rating: 5,
                text: "R.T is an amazing coach who really understands how to motivate players. His training sessions are challenging but fun. Highly recommend!",
                date: "2024-01-01",
                avatar: "D"
            }
        ];
        
        this.currentRating = 0;
        this.init();
    }

    init() {
        this.renderReviews();
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Star rating functionality
        const stars = document.querySelectorAll('.star-rating .star');
        stars.forEach(star => {
            star.addEventListener('click', (e) => {
                this.currentRating = parseInt(e.target.dataset.rating);
                this.updateStarRating();
            });

            star.addEventListener('mouseover', (e) => {
                const rating = parseInt(e.target.dataset.rating);
                this.highlightStars(rating);
            });
        });

        document.getElementById('starRating').addEventListener('mouseleave', () => {
            this.updateStarRating();
        });

        // Submit review
        document.getElementById('submitReview').addEventListener('click', () => {
            this.submitReview();
        });

        // Enter key in textarea
        document.getElementById('reviewText').addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && e.ctrlKey) {
                this.submitReview();
            }
        });
    }

    highlightStars(rating) {
        const stars = document.querySelectorAll('.star-rating .star');
        stars.forEach((star, index) => {
            if (index < rating) {
                star.classList.add('active');
            } else {
                star.classList.remove('active');
            }
        });
    }

    updateStarRating() {
        this.highlightStars(this.currentRating);
    }

    submitReview() {
        const reviewText = document.getElementById('reviewText').value.trim();
        
        if (this.currentRating === 0) {
            alert('Please select a rating!');
            return;
        }

        if (reviewText === '') {
            alert('Please write a review!');
            return;
        }

        const newReview = {
            id: this.reviews.length + 1,
            name: "Anonymous User", // In real app, get from logged-in user
            rating: this.currentRating,
            text: reviewText,
            date: new Date().toISOString().split('T')[0],
            avatar: "A"
        };

        this.reviews.unshift(newReview); // Add to beginning
        this.renderReviews();
        this.resetForm();
        this.showSuccessMessage();

        // Update coach rating and total reviews
        this.updateCoachRating();

        // In real application, send to backend:
        // this.sendReviewToBackend(newReview);
    }

    resetForm() {
        document.getElementById('reviewText').value = '';
        this.currentRating = 0;
        this.updateStarRating();
    }

    showSuccessMessage() {
        const message = document.getElementById('successMessage');
        message.style.display = 'block';
        setTimeout(() => {
            message.style.display = 'none';
        }, 3000);
    }

    updateCoachRating() {
        // Calculate new average rating
        const totalRating = this.reviews.reduce((sum, review) => sum + review.rating, 0);
        const newRating = (totalRating / this.reviews.length).toFixed(1);
        
        // Update coach data
        coachData.rating = parseFloat(newRating);
        coachData.totalReviews = this.reviews.length;
        
        // Update display
        document.getElementById('coachRating').textContent = `${coachData.rating} (${coachData.totalReviews} reviews)`;
    }

    renderReviews() {
        const reviewsList = document.getElementById('reviewsList');
        
        if (this.reviews.length === 0) {
            reviewsList.innerHTML = '<div class="no-reviews">No reviews yet. Be the first to write one!</div>';
            return;
        }

        reviewsList.innerHTML = this.reviews.map(review => `
            <div class="review-item">
                <div class="review-header">
                    <div class="reviewer-avatar">${review.avatar}</div>
                    <div class="reviewer-info">
                        <div class="reviewer-name">${review.name}</div>
                        <div class="review-rating">
                            ${this.generateStars(review.rating)}
                        </div>
                        <div class="review-date">${this.formatDate(review.date)}</div>
                    </div>
                </div>
                <div class="review-text">${review.text}</div>
            </div>
        `).join('');
    }

    generateStars(rating) {
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            stars += `<span class="star ${i <= rating ? 'active' : ''}">★</span>`;
        }
        return stars;
    }

    formatDate(dateString) {
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(dateString).toLocaleDateString('en-US', options);
    }

    // Method to send review to backend (placeholder)
    async sendReviewToBackend(review) {
        try {
            const response = await fetch('/api/reviews', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(review)
            });
            
            if (!response.ok) {
                throw new Error('Failed to submit review');
            }
            
            return await response.json();
        } catch (error) {
            console.error('Error submitting review:', error);
            alert('Failed to submit review. Please try again.');
        }
    }
}

// Initialize the page with dynamic data
function initializePage() {
    populateHeroSection();
    populateBookingSection();
    populateGallery();
    populateAboutSection();
    populateExpertiseSection();
    populateAchievements();
    populateExperience();
    populateSocialMedia();
    setupEventListeners();
    
    // Initialize review system
    new ReviewSystem();
}

function populateHeroSection() {
    document.getElementById('coachName').textContent = coachData.name;
    document.getElementById('coachTitle').textContent = coachData.title;
    document.getElementById('coachRating').textContent = `${coachData.rating} (${coachData.totalReviews} reviews)`;
    document.getElementById('coachExperience').textContent = coachData.experience_summary;
    document.getElementById('coachLocation').textContent = coachData.location;
}

function populateBookingSection() {
    document.getElementById('coachPrice').innerHTML = `${coachData.hourlyRate}<span style="font-size: 0.8rem; font-weight: 400;">/hour</span>`;
    document.getElementById('sessionDuration').textContent = coachData.booking.sessionDuration;
    document.getElementById('responseTime').textContent = coachData.booking.responseTime;
    document.getElementById('cancellationPolicy').textContent = coachData.booking.cancellationPolicy;
    document.getElementById('responseTimeNote').textContent = coachData.booking.responseTime.toLowerCase().replace('within ', '');

    // Populate time slots
    const timeSlotsContainer = document.getElementById('timeSlots');
    timeSlotsContainer.innerHTML = '';
    
    coachData.booking.availableTimeSlots.forEach(time => {
        const slot = document.createElement('div');
        slot.className = 'time-slot';
        slot.dataset.time = time;
        slot.textContent = time;
        
        if (coachData.booking.unavailableSlots.includes(time)) {
            slot.classList.add('unavailable');
        }
        
        timeSlotsContainer.appendChild(slot);
    });
}

function populateGallery() {
    const galleryGrid = document.getElementById('galleryGrid');
    galleryGrid.innerHTML = '';
    
    coachData.gallery.forEach(item => {
        const img = document.createElement('div');
        img.className = 'gallery-img';
        img.textContent = item.placeholder;
        img.onclick = () => openLightbox(img);
        galleryGrid.appendChild(img);
    });
}

function populateAboutSection() {
    document.getElementById('aboutCoachName').textContent = coachData.name;
    const aboutContent = document.getElementById('aboutContent');
    aboutContent.innerHTML = '';

    // Short description
    const shortDesc = document.createElement('div');
    shortDesc.className = 'about-text';
    shortDesc.textContent = coachData.about.shortDescription;
    aboutContent.appendChild(shortDesc);

    // Full description paragraphs
    coachData.about.fullDescription.forEach(paragraph => {
        const p = document.createElement('div');
        p.className = 'about-text';
        p.textContent = paragraph;
        aboutContent.appendChild(p);
    });

    // Expertise list
    const expertiseSection = document.createElement('div');
    expertiseSection.className = 'specializations';
    expertiseSection.innerHTML = '<strong>His expertise includes:</strong>';
    
    const expertiseList = document.createElement('ul');
    expertiseList.className = 'expertise-list';
    
    coachData.about.expertise.forEach(skill => {
        const li = document.createElement('li');
        li.textContent = skill;
        expertiseList.appendChild(li);
    });
    
    expertiseSection.appendChild(expertiseList);
    aboutContent.appendChild(expertiseSection);
}

function populateExpertiseSection() {
    // Specializations
    const specTags = document.getElementById('specializationTags');
    specTags.innerHTML = '';
    coachData.specializations.forEach(spec => {
        const tag = document.createElement('span');
        tag.className = 'spec-tag';
        tag.textContent = spec;
        specTags.appendChild(tag);
    });

    // Certifications
    const certTags = document.getElementById('certificationTags');
    certTags.innerHTML = '';
    coachData.certifications.forEach(cert => {
        const tag = document.createElement('span');
        tag.className = 'cert-tag';
        tag.textContent = cert;
        certTags.appendChild(tag);
    });

    // Languages
    const langTags = document.getElementById('languageTags');
    langTags.innerHTML = '';
    coachData.languages.forEach(lang => {
        const tag = document.createElement('span');
        tag.className = 'lang-tag';
        tag.innerHTML = `${lang.flag} ${lang.name}`;
        langTags.appendChild(lang);
    });
}

function populateAchievements() {
    const achievementsList = document.getElementById('achievementsList');
    achievementsList.innerHTML = '';
    
    coachData.achievements.forEach(achievement => {
        const li = document.createElement('li');
        li.textContent = achievement;
        achievementsList.appendChild(li);
    });
}

function populateExperience() {
    const experienceContent = document.getElementById('experienceContent');
    experienceContent.innerHTML = '';
    
    coachData.experience.forEach(exp => {
        const expItem = document.createElement('div');
        expItem.className = 'experience-item';
        
        expItem.innerHTML = `
            <div class="experience-header">
                <div class="experience-title">
                    <strong>${exp.title}</strong>
                    <div class="experience-company">${exp.company}</div>
                </div>
                <div class="experience-dates">${exp.dates}</div>
            </div>
            <div class="experience-description">
                ${exp.description}
            </div>
        `;
        
        experienceContent.appendChild(expItem);
    });
}

function populateSocialMedia() {
    const socialButtons = document.getElementById('socialButtons');
    socialButtons.innerHTML = '';
    
    const socialPlatforms = [
        { name: 'Facebook', icon: 'fab fa-facebook-f', url: coachData.socialMedia.facebook, class: 'facebook' },
        { name: 'Instagram', icon: 'fab fa-instagram', url: coachData.socialMedia.instagram, class: 'instagram' },
        { name: 'WhatsApp', icon: 'fab fa-whatsapp', url: coachData.socialMedia.whatsapp, class: 'whatsapp' }
    ];
    
    socialPlatforms.forEach(platform => {
        if (platform.url) {
            const link = document.createElement('a');
            link.href = platform.url;
            link.target = '_blank';
            link.className = `social-btn ${platform.class}`;
            link.innerHTML = `<i class="${platform.icon}"></i> ${platform.name}`;
            socialButtons.appendChild(link);
        }
    });
}

function setupEventListeners() {
    // Time slot selection
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('time-slot') && !e.target.classList.contains('unavailable')) {
            document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
            e.target.classList.add('selected');
        }
    });

    // Back button
    document.querySelector('.back-button').addEventListener('click', function() {
        window.history.back();
    });

    // Form submission
    document.getElementById('bookingForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const selectedDate = document.getElementById('selectedDate').value;
        const selectedTime = document.querySelector('.time-slot.selected');
        const venue = document.getElementById('venueDetails').value;
        
        if (!selectedDate || !selectedTime || !venue.trim()) {
            alert('Please fill in all required fields and select a time slot.');
            return;
        }
        
        // In a real application, this would send data to the backend
        const bookingData = {
            coachId: coachData.id,
            date: selectedDate,
            time: selectedTime.dataset.time,
            venue: venue,
            hourlyRate: coachData.hourlyRate
        };
        
        console.log('Booking request data:', bookingData);
        alert(`Booking request sent!\n\nCoach: ${coachData.name}\nDate: ${selectedDate}\nTime: ${selectedTime.textContent}\nVenue: ${venue.substring(0, 50)}...`);
    });

    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('selectedDate').min = today;

    // Avatar upload
    document.getElementById('avatarUpload').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const placeholder = document.getElementById('avatarPlaceholder');
                placeholder.style.backgroundImage = `url(${e.target.result})`;
                placeholder.style.backgroundSize = 'cover';
                placeholder.style.backgroundPosition = 'center';
                placeholder.textContent = '';
                
                // In a real application, upload to server
                coachData.avatar = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
}

// Lightbox functions
function openLightbox(element) {
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    
    if (element.style.backgroundImage) {
        lightboxImg.src = element.style.backgroundImage.slice(5, -2);
    } else {
        // For placeholder images, you could show a default image or handle differently
        return;
    }
    
    lightbox.style.display = 'flex';
}

function closeLightbox() {
    document.getElementById('lightbox').style.display = 'none';
}

// API functions that would connect to your backend
async function fetchCoachData(coachId) {
    // This would be replaced with actual API call
    // const response = await fetch(`/api/coaches/${coachId}`);
    // return await response.json();
    return coachData;
}

async function submitBookingRequest(bookingData) {
    // This would be replaced with actual API call
    // const response = await fetch('/api/bookings', {
    //     method: 'POST',
    //     headers: { 'Content-Type': 'application/json' },
    //     body: JSON.stringify(bookingData)
    // });
    // return await response.json();
    console.log('Booking submitted:', bookingData);
    return { success: true, message: 'Booking request sent successfully' };
}

async function updateCoachProfile(coachId, updates) {
    // This would be replaced with actual API call
    // const response = await fetch(`/api/coaches/${coachId}`, {
    //     method: 'PUT',
    //     headers: { 'Content-Type': 'application/json' },
    //     body: JSON.stringify(updates)
    // });
    // return await response.json();
    console.log('Profile updated:', updates);
    return { success: true };
}

// Initialize the page when DOM is loaded
document.addEventListener('DOMContentLoaded', initializePage);

// Example of how to update data dynamically (simulating real-time updates)
function simulateDataUpdate() {
    // Simulate receiving updated data from the server
    coachData.totalReviews = 157;
    coachData.rating = 4.95;
    
    // Update the display
    document.getElementById('coachRating').textContent = `${coachData.rating} (${coachData.totalReviews} reviews)`;
}