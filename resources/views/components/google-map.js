// Google Maps component for GoPlay application
class GoogleMap {
    constructor(containerId, options = {}) {
        this.containerId = containerId;
        this.map = null;
        this.markers = [];
        this.infoWindows = [];
        
        // Default options
        this.options = {
            center: { lat: 6.9271, lng: 79.8612 }, // Colombo, Sri Lanka
            zoom: 13,
            mapTypeId: 'roadmap',
            styles: this.getMapStyles(),
            ...options
        };
        
        // Wait for Google Maps to load
        if (window.google && window.google.maps) {
            this.initMap();
        } else {
            window.initGoogleMap = () => this.initMap();
        }
    }

    // Custom map styles for better appearance
    getMapStyles() {
        return [
            {
                featureType: 'poi.business',
                stylers: [{ visibility: 'off' }]
            },
            {
                featureType: 'poi.park',
                elementType: 'labels.text',
                stylers: [{ visibility: 'off' }]
            }
        ];
    }

    // Initialize the map
    initMap() {
        const container = document.getElementById(this.containerId);
        if (!container) {
            console.error('Map container not found:', this.containerId);
            return;
        }

        this.map = new google.maps.Map(container, this.options);
        
        // Add click listener
        this.map.addListener('click', (event) => {
            this.onMapClick(event);
        });
    }

    // Add a marker to the map
    addMarker(options) {
        if (!this.map) {
            console.error('Map not initialized');
            return null;
        }

        const marker = new google.maps.Marker({
            position: options.position,
            map: this.map,
            title: options.title || '',
            icon: options.icon || null,
            animation: options.animation || null
        });

        // Add info window if content provided
        if (options.infoWindow) {
            const infoWindow = new google.maps.InfoWindow({
                content: options.infoWindow
            });

            marker.addListener('click', () => {
                // Close other info windows
                this.infoWindows.forEach(window => window.close());
                infoWindow.open(this.map, marker);
            });

            this.infoWindows.push(infoWindow);
        }

        this.markers.push(marker);
        return marker;
    }

    // Add multiple markers for sports facilities
    addSportsFacilities(facilities) {
        facilities.forEach(facility => {
            const marker = this.addMarker({
                position: { lat: facility.lat, lng: facility.lng },
                title: facility.name,
                infoWindow: this.createFacilityInfoWindow(facility)
            });

            // Store facility data with marker
            marker.facilityData = facility;
        });
    }

    // Create info window content for sports facility
    createFacilityInfoWindow(facility) {
        return `
            <div class="facility-info-window" style="max-width: 300px; font-family: Arial, sans-serif;">
                <h3 style="margin: 0 0 10px 0; color: #333;">${facility.name}</h3>
                <div class="facility-details">
                    <p style="margin: 5px 0; color: #666;"><i class="fas fa-map-marker-alt"></i> ${facility.address}</p>
                    <p style="margin: 5px 0; color: #666;"><i class="fas fa-star"></i> Rating: ${facility.rating}/5</p>
                    <p style="margin: 5px 0; color: #666;"><i class="fas fa-dollar-sign"></i> From $${facility.pricePerHour}/hour</p>
                    <div class="facility-sports" style="margin: 10px 0;">
                        ${facility.sports.map(sport => `<span style="background: #007bff; color: white; padding: 2px 6px; border-radius: 3px; font-size: 12px; margin: 2px;">${sport}</span>`).join('')}
                    </div>
                </div>
                <div class="facility-actions" style="margin-top: 15px;">
                    <button onclick="viewFacilityDetails(${facility.id})" style="background: #007bff; color: white; border: none; padding: 8px 12px; border-radius: 4px; margin-right: 5px; cursor: pointer;">
                        View Details
                    </button>
                    <button onclick="bookFacility(${facility.id})" style="background: #28a745; color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer;">
                        Book Now
                    </button>
                </div>
            </div>
        `;
    }

    // Get user's current location
    getCurrentLocation() {
        return new Promise((resolve, reject) => {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const location = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };
                        resolve(location);
                    },
                    (error) => reject(error),
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 }
                );
            } else {
                reject(new Error('Geolocation is not supported'));
            }
        });
    }

    // Add user location marker
    async addUserLocation() {
        try {
            const location = await this.getCurrentLocation();
            const marker = this.addMarker({
                position: location,
                title: 'Your Location',
                infoWindow: '<div><strong>Your Current Location</strong></div>'
            });
            
            this.setCenter(location.lat, location.lng);
            return location;
        } catch (error) {
            console.error('Error getting location:', error);
            throw error;
        }
    }

    // Set map center
    setCenter(lat, lng) {
        if (this.map) {
            this.map.setCenter({ lat, lng });
        }
    }

    // Set map zoom
    setZoom(zoom) {
        if (this.map) {
            this.map.setZoom(zoom);
        }
    }

    // Clear all markers
    clearMarkers() {
        this.markers.forEach(marker => marker.setMap(null));
        this.markers = [];
        this.infoWindows = [];
    }

    // Fit map to show all markers
    fitToMarkers() {
        if (this.markers.length > 0) {
            const bounds = new google.maps.LatLngBounds();
            this.markers.forEach(marker => bounds.extend(marker.getPosition()));
            this.map.fitBounds(bounds);
        }
    }

    // Handle map click events
    onMapClick(event) {
        console.log('Map clicked at:', event.latLng.toJSON());
    }

    // Resize map (call when container size changes)
    resize() {
        if (this.map) {
            google.maps.event.trigger(this.map, 'resize');
        }
    }

    // Destroy the map
    destroy() {
        this.clearMarkers();
        this.map = null;
    }
}

// Utility function to load Google Maps API
window.loadGoogleMapsAPI = function(apiKey, callback) {
    if (window.google && window.google.maps) {
        callback();
        return;
    }

    const script = document.createElement('script');
    script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&callback=initGoogleMap&libraries=places`;
    script.async = true;
    script.defer = true;
    
    window.initGoogleMap = callback;
    
    script.onerror = () => {
        console.error('Failed to load Google Maps API');
    };
    
    document.head.appendChild(script);
};

// Global functions for info window actions
window.viewFacilityDetails = function(facilityId) {
    window.location.href = `ground-details.html?id=${facilityId}`;
};

window.bookFacility = function(facilityId) {
    window.location.href = `book-ground.html?facility=${facilityId}`;
};

// Export the class
window.GoogleMap = GoogleMap;