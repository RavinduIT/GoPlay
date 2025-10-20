let facilitiesMap;
        let currentFacilities = [];
        let allFacilities = [];
        let facilityLocations = [];
        let availableSports = [];

        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            loadComponents();
            initializePage();
        });

        // Load components
        function loadComponents() {
            // Load navbar
            fetch('/app/views/components/navbar.php')
                .then(res => res.text())
                .then(data => {
                    document.getElementById('navbar-container').innerHTML = data;
                    if (typeof Navbar !== 'undefined') {
                        new Navbar();
                    }
                });

            // Load footer
            fetch('/app/views/components/footer.php/footer.html')
                .then(res => res.text())
                .then(data => {
                    document.getElementById('footer-container').innerHTML = data;
                });
        }

        // Initialize page with data from JSON
        async function initializePage() {
            try {
                // Show loading state
                showLoadingState();

                // Load data from JSON file
                const data = await window.dataLoader.loadFacilities();
                
                // Store data globally
                allFacilities = data.facilities;
                facilityLocations = data.locations;
                availableSports = data.sports;
                currentFacilities = [...allFacilities];

                // Populate dropdowns
                populateLocationDropdown(facilityLocations);
                populateSportsDropdown(availableSports);

                // Render facilities and initialize map
                renderFacilities();
                initializeMap();

                // Hide loading state
                hideLoadingState();
                
            } catch (error) {
                console.error('Error initializing page:', error);
                showErrorState(error.message);
            }
        }

        // Show loading state
        function showLoadingState() {
            const container = document.getElementById('facilities-grid');
            container.innerHTML = `
                <div class="loading-state">
                    <div class="loading-spinner">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                    <p>Loading premium facilities...</p>
                </div>
            `;
        }

        // Hide loading state
        function hideLoadingState() {
            // Loading will be replaced by rendered facilities
        }

        // Show error state
        function showErrorState(message) {
            const container = document.getElementById('facilities-grid');
            container.innerHTML = `
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3>Unable to Load Facilities</h3>
                    <p>${message}</p>
                    <button class="btn-secondary" onclick="location.reload()">Try Again</button>
                </div>
            `;
        }

        // Populate location dropdown
        function populateLocationDropdown(locations) {
            const select = document.getElementById('location');
            const currentOptions = select.querySelectorAll('option:not([value=""])');
            currentOptions.forEach(option => option.remove());

            locations.forEach(location => {
                const option = document.createElement('option');
                option.value = location.code;
                option.textContent = `${location.name} (${location.area})`;
                select.appendChild(option);
            });
        }

        // Populate sports dropdown  
        function populateSportsDropdown(sports) {
            const select = document.getElementById('sport-type');
            const currentOptions = select.querySelectorAll('option:not([value=""])');
            currentOptions.forEach(option => option.remove());

            sports.forEach(sport => {
                const option = document.createElement('option');
                option.value = sport.code;
                option.textContent = sport.name;
                select.appendChild(option);
            });
        }

       

        // Render facilities list with professional design
        function renderFacilities() {
            const container = document.getElementById('facilities-grid');
            
            if (currentFacilities.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3>No facilities found</h3>
                        <p>Try adjusting your search criteria or browse all facilities</p>
                        <button class="btn-secondary" onclick="clearFilters()">Clear Filters</button>
                    </div>
                `;
                return;
            }

            container.innerHTML = currentFacilities.map(facility => `
                <div class="facility-card" onclick="highlightFacility(${facility.id})">
                    <div class="facility-image">
                        <div class="facility-badge">
                            <i class="fas fa-star"></i>
                            <span>${facility.rating}</span>
                        </div>
                        <div class="facility-icon">
                            <i class="fas fa-dumbbell"></i>
                        </div>
                    </div>
                    
                    <div class="facility-content">
                        <div class="facility-header">
                            <h3 class="facility-name">${facility.name}</h3>
                            <div class="facility-price">
                                <span class="price-amount">$${facility.pricePerHour}</span>
                                <span class="price-period">/hour</span>
                            </div>
                        </div>
                        
                        <div class="facility-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>${facility.address}</span>
                        </div>
                        
                        <div class="facility-sports">
                            ${facility.sports.map(sport => `<span class="sport-tag">${sport}</span>`).join('')}
                        </div>
                        
                        <div class="facility-features">
                            ${facility.features.slice(0, 3).map(feature => `
                                <span class="feature-item">
                                    <i class="fas fa-check"></i>
                                    ${feature}
                                </span>
                            `).join('')}
                            ${facility.features.length > 3 ? `<span class="more-features">+${facility.features.length - 3} more</span>` : ''}
                        </div>
                        
                        <div class="facility-footer">
                            <div class="facility-rating">
                                <div class="stars">
                                    ${Array(5).fill().map((_, i) => `
                                        <i class="fas fa-star ${i < Math.floor(facility.rating) ? 'filled' : ''}"></i>
                                    `).join('')}
                                </div>
                                <span class="rating-text">${facility.rating} rating</span>
                            </div>
                            <div class="facility-actions">
                                <button class="btn-outline" onclick="viewDetails(${facility.id}); event.stopPropagation();">
                                    Details
                                </button>
                                <button class="btn-primary" onclick="bookFacility(${facility.id}); event.stopPropagation();">
                                    <i class="fas fa-calendar-plus"></i>
                                    Book Now
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');

            // Update results count
            const count = currentFacilities.length;
            document.getElementById('results-count').textContent = `${count} Premium Facilities Available`;
        }

        // Professional map toggle functionality
                }

        // Sort facilities using data loader
        function sortFacilities() {
            const sortBy = document.getElementById('sort-by').value;
            currentFacilities = window.dataLoader.sortFacilities(currentFacilities, sortBy);
            renderFacilities();
        }

        // Clear filters and reset to all facilities
        function clearFilters() {
            document.getElementById('search-grounds').value = '';
            document.getElementById('location').value = '';
            document.getElementById('sport-type').value = '';
            document.getElementById('price-range').value = '';
            document.getElementById('sort-by').value = 'rating';
            
            currentFacilities = [...allFacilities];
            currentFacilities = window.dataLoader.sortFacilities(currentFacilities, 'rating');
            
            renderFacilities();
            updateMapMarkers();
        }

        // View facility details
        function viewDetails(facilityId) {
            window.location.href = `ground-details.html?id=${facilityId}`;
        }

        // Enhanced search with price filtering using data loader
        function searchFacilities() {
            const filters = {
                searchTerm: document.getElementById('search-grounds').value,
                location: document.getElementById('location').value,
                sportType: document.getElementById('sport-type').value,
                priceRange: document.getElementById('price-range').value
            };

            // Use data loader to filter facilities
            currentFacilities = window.dataLoader.filterFacilities(allFacilities, filters);
            
            // Apply current sort
            const sortBy = document.getElementById('sort-by').value;
            currentFacilities = window.dataLoader.sortFacilities(currentFacilities, sortBy);

            renderFacilities();
            updateMapMarkers();
        }

        // Get current location
        function getCurrentLocation() {
            // Location functionality disabled
        }

        // Highlight facility on map
        function highlightFacility(facilityId) {
            // Map highlight disabled
        }

        // Fit map to show all markers
        function fitMapToMarkers() {
            // Map functionality disabled
        }

        // Book facility
        function bookFacility(facilityId) {
            const facility = currentFacilities.find(f => f.id === facilityId);
            if (facility) {
                window.location.href = `ground-details.html?id=${facilityId}`;
            }
        }

        // Global logout function
        function logout() {
            localStorage.removeItem('currentUser');
            localStorage.removeItem('authToken');
            window.location.href = '../index.html';
        }

        // Update map markers
        function updateMapMarkers() {
            // Map update disabled
        }

        // Add event listeners for search inputs
        function addEventListeners() {
            const searchInput = document.getElementById('search-grounds');
            const locationSelect = document.getElementById('location');
            const sportSelect = document.getElementById('sport-type');
            const priceSelect = document.getElementById('price-range');

            if (searchInput) {
                searchInput.addEventListener('input', searchFacilities);
            }
            if (locationSelect) {
                locationSelect.addEventListener('change', searchFacilities);
            }
            if (sportSelect) {
                sportSelect.addEventListener('change', searchFacilities);
            }
            if (priceSelect) {
                priceSelect.addEventListener('change', searchFacilities);
            }
        }

        // Initialize event listeners after DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Add a delay to ensure elements are created
            setTimeout(addEventListeners, 500);
        });