let currentFacility = null;
        let facilityMap = null;
        let selectedDate = null;
        let selectedTime = null;

        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            loadComponents();
            initializePage();
        });

        

        // Initialize page with facility data
        async function initializePage() {
            try {
                const urlParams = new URLSearchParams(window.location.search);
                const facilityId = urlParams.get('id');

                if (!facilityId) {
                    showError('No facility ID provided');
                    return;
                }

                // Load facility data
                const data = await window.dataLoader.loadFacilities();
                const facility = window.dataLoader.getFacilityById(data.facilities, facilityId);

                if (!facility) {
                    showError('Facility not found');
                    return;
                }

                currentFacility = facility;
                populateFacilityData(facility);
                initializeMap(facility);
                setupBookingForm();
                hideLoading();

            } catch (error) {
                console.error('Error loading facility data:', error);
                showError('Unable to load facility data');
            }
        }

        // Populate facility data
        function populateFacilityData(facility) {
            // Update page title
            document.title = `${facility.name} - GoPlay`;

            // Breadcrumb
            document.getElementById('breadcrumb-facility').textContent = facility.name;

            // Header
            document.getElementById('facility-name').textContent = facility.name;
            document.getElementById('facility-location').innerHTML = `
                <i class="fas fa-map-marker-alt"></i>
                <span>${facility.address}</span>
            `;

            // Rating
            updateRatingDisplay(facility.rating);

            // Description
            document.getElementById('facility-description').innerHTML = `<p>${facility.description}</p>`;

            // Sports
            const sportsGrid = document.getElementById('sports-grid');
            sportsGrid.innerHTML = facility.sports.map(sport => `
                <div class="sport-item">
                    <div class="sport-icon">
                        <i class="fas fa-dumbbell"></i>
                    </div>
                    <span class="sport-name">${sport}</span>
                </div>
            `).join('');

            // Features
            const featuresGrid = document.getElementById('features-grid');
            featuresGrid.innerHTML = facility.features.map(feature => `
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>${feature}</span>
                </div>
            `).join('');

            // Amenities
            const amenitiesGrid = document.getElementById('amenities-grid');
            const amenityIcons = {
                parking: 'fa-car',
                restrooms: 'fa-restroom',
                cafeteria: 'fa-utensils',
                airConditioning: 'fa-snowflake',
                wifi: 'fa-wifi',
                lockers: 'fa-lock'
            };

            amenitiesGrid.innerHTML = Object.entries(facility.amenities).map(([key, available]) => {
                if (!available) return '';
                const icon = amenityIcons[key] || 'fa-check';
                const name = key.replace(/([A-Z])/g, ' $1').replace(/^./, str => str.toUpperCase());
                return `
                    <div class="amenity-item">
                        <i class="fas ${icon}"></i>
                        <span>${name}</span>
                    </div>
                `;
            }).join('');

            // Operating Hours
            const hoursGrid = document.getElementById('hours-grid');
            const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            const dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

            hoursGrid.innerHTML = days.map((day, index) => `
                <div class="hours-row">
                    <span class="day-name">${dayNames[index]}</span>
                    <span class="hours-time">${facility.operatingHours[day]}</span>
                </div>
            `).join('');

            // Address and Contact
            document.getElementById('full-address').textContent = facility.address;
            document.getElementById('contact-info').innerHTML = `
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <span>${facility.contact.phone}</span>
                </div>
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <span>${facility.contact.email}</span>
                </div>
                <div class="contact-item">
                    <i class="fas fa-user-tie"></i>
                    <span>Manager: ${facility.contact.manager}</span>
                </div>
            `;

            // Booking panel
            document.getElementById('price-amount').textContent = `$${facility.pricePerHour}`;
            document.querySelector('.booking-rating-score').textContent = facility.rating;

            // Time slots
            populateTimeSlots(facility.availableSlots);
        }

        // Update rating display
        function updateRatingDisplay(rating) {
            const ratingElements = document.querySelectorAll('.stars, .rating-stars, .review-rating');
            ratingElements.forEach(element => {
                const stars = element.querySelectorAll('i');
                stars.forEach((star, index) => {
                    if (index < Math.floor(rating)) {
                        star.classList.remove('far');
                        star.classList.add('fas');
                    } else {
                        star.classList.remove('fas');
                        star.classList.add('far');
                    }
                });
            });

            document.querySelectorAll('.rating-number, .rating-score').forEach(el => {
                el.textContent = rating.toFixed(1);
            });
        }

        // Initialize Google Maps
        function initializeMap(facility) {
            if (MAPS_CONFIG.apiKey === 'YOUR_GOOGLE_MAPS_API_KEY') {
                document.getElementById('facility-map').innerHTML = `
                    <div class="map-placeholder">
                        <i class="fas fa-map"></i>
                        <p>Map integration ready!</p>
                        <p>Using your configured API key</p>
                    </div>
                `;
                return;
            }

            loadGoogleMapsAPI(MAPS_CONFIG.apiKey, function() {
                facilityMap = new GoogleMap('facility-map', {
                    center: { lat: facility.lat, lng: facility.lng },
                    zoom: 16
                });

                // Add marker for facility
                facilityMap.addMarker({
                    position: { lat: facility.lat, lng: facility.lng },
                    title: facility.name,
                    infoWindow: `
                        <div style="max-width: 200px;">
                            <h4>${facility.name}</h4>
                            <p>${facility.address}</p>
                        </div>
                    `
                });
            });
        }

        // Populate time slots
        function populateTimeSlots(slots) {
            const container = document.getElementById('time-slots-container');
            container.innerHTML = slots.map(slot => `
                <button class="time-slot" data-time="${slot}" onclick="selectTimeSlot('${slot}')">
                    ${slot}
                </button>
            `).join('');
        }

        // Setup booking form
        function setupBookingForm() {
            const dateInput = document.getElementById('booking-date');
            const today = new Date().toISOString().split('T')[0];
            dateInput.setAttribute('min', today);
            dateInput.value = today;
            selectedDate = today;
            updateBookingSummary();

            dateInput.addEventListener('change', function() {
                selectedDate = this.value;
                updateBookingSummary();
            });
        }

        // Select time slot
        function selectTimeSlot(time) {
            document.querySelectorAll('.time-slot').forEach(slot => {
                slot.classList.remove('selected');
            });
            
            event.target.classList.add('selected');
            selectedTime = time;
            updateBookingSummary();
        }

        // Update booking summary
        function updateBookingSummary() {
            if (selectedDate) {
                const date = new Date(selectedDate);
                const formattedDate = date.toLocaleDateString('en-US', { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                });
                document.getElementById('summary-date').textContent = formattedDate;
            }

            if (selectedTime) {
                document.getElementById('summary-time').textContent = selectedTime;
            }

            if (currentFacility && selectedDate && selectedTime) {
                document.getElementById('summary-total').textContent = `$${currentFacility.pricePerHour}`;
                document.getElementById('book-now-btn').disabled = false;
            }
        }

        // Show loading
        function showLoading() {
            document.getElementById('loading-overlay').classList.remove('hidden');
            document.getElementById('facility-content').classList.add('hidden');
            document.getElementById('error-overlay').classList.add('hidden');
        }

        // Hide loading
        function hideLoading() {
            document.getElementById('loading-overlay').classList.add('hidden');
            document.getElementById('facility-content').classList.remove('hidden');
        }

        // Show error
        function showError(message) {
            document.getElementById('loading-overlay').classList.add('hidden');
            document.getElementById('facility-content').classList.add('hidden');
            document.getElementById('error-overlay').classList.remove('hidden');
            document.querySelector('.error-content p').textContent = message;
        }

        // Action functions
        function goBack() {
            window.history.back();
        }

        function shareFacility() {
            if (navigator.share) {
                navigator.share({
                    title: currentFacility.name,
                    text: currentFacility.description,
                    url: window.location.href
                });
            } else {
                navigator.clipboard.writeText(window.location.href);
                alert('Link copied to clipboard!');
            }
        }

        function addToWishlist() {
            alert('Added to wishlist! Feature coming soon.');
        }

        function openGallery() {
            alert('Photo gallery feature coming soon!');
        }

        function getDirections() {
            const address = encodeURIComponent(currentFacility.address);
            window.open(`https://www.google.com/maps/dir/?api=1&destination=${address}`, '_blank');
        }

        function centerMap() {
            if (facilityMap) {
                facilityMap.setCenter(currentFacility.lat, currentFacility.lng);
                facilityMap.setZoom(16);
            }
        }

        function openFullMap() {
            window.open(`map-example.html`, '_blank');
        }

        function writeReview() {
            alert('Review feature coming soon!');
        }

        function proceedToBooking() {
            if (!selectedDate || !selectedTime) {
                alert('Please select date and time');
                return;
            }

            const bookingData = {
                facilityId: currentFacility.id,
                facilityName: currentFacility.name,
                date: selectedDate,
                time: selectedTime,
                price: currentFacility.pricePerHour
            };

            localStorage.setItem('bookingData', JSON.stringify(bookingData));
            window.location.href = 'payment.html';
        }

        function contactFacility() {
            window.location.href = `tel:${currentFacility.contact.phone}`;
        }

        function viewAvailability() {
            alert('Availability calendar coming soon!');
        }

        function reportIssue() {
            alert('Issue reporting feature coming soon!');
        }

        