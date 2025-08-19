// Google Maps configuration
// Replace 'YOUR_GOOGLE_MAPS_API_KEY' with your actual API key

const MAPS_CONFIG = {
    // Replace with your Google Maps API key
    apiKey: 'AIzaSyB3qKhJG9ulG0vgu9KxaG0NPXADLGxMr7k',
    
    // Default map settings
    defaultCenter: { lat: 6.9271, lng: 79.8612 }, // Colombo, Sri Lanka
    defaultZoom: 13,
    
    // Map libraries to load
    libraries: ['places', 'geometry']
};

// Export configuration
window.MAPS_CONFIG = MAPS_CONFIG;