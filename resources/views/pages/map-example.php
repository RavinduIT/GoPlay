<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sports Facilities Map - GoPlay</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/components/navbar.css">
    <link rel="stylesheet" href="../css/components/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .map-container {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .map-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .map-wrapper {
            position: relative;
            height: 500px;
            width: 100%;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        #sports-map {
            height: 100%;
            width: 100%;
        }
        
        .map-controls {
            margin: 1rem 0;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .map-btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            background: #007bff;
            color: white;
            cursor: pointer;
            font-size: 0.9rem;
        }
        
        .map-btn:hover {
            background: #0056b3;
        }
        
        .map-btn.secondary {
            background: #6c757d;
        }
        
        .map-btn.secondary:hover {
            background: #545b62;
        }
        
        .loading {
            text-align: center;
            padding: 2rem;
            color: #666;
        }
        
        .error {
            text-align: center;
            padding: 2rem;
            color: #dc3545;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            margin: 1rem 0;
        }

        @media (max-width: 768px) {
            .map-container {
                padding: 1rem;
            }
            
            .map-wrapper {
                height: 400px;
            }
            
            .map-controls {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar Component -->
    <div id="navbar-container"></div>

    <!-- Main Content -->
    <main class="map-container">
        <div class="map-header">
            <h1>Find Sports Facilities Near You</h1>
            <p>Discover and book amazing sports facilities in your area</p>
        </div>

        <div class="map-controls">
            <button id="find-location-btn" class="map-btn">
                <i class="fas fa-location-arrow"></i> Find My Location
            </button>
            <button id="show-all-btn" class="map-btn secondary">
                <i class="fas fa-map"></i> Show All Facilities
            </button>
            <button id="clear-markers-btn" class="map-btn secondary">
                <i class="fas fa-times"></i> Clear Markers
            </button>
        </div>

        <div class="map-wrapper">
            <div id="sports-map"></div>
        </div>

        <div id="map-status" class="loading">
            Loading map...
        </div>
    </main>

    <!-- Footer Component -->
    <div id="footer-container"></div>

    <!-- Load configuration first -->
    <script src="../js/config/maps-config.js"></script>
    
    <!-- Load Google Maps component -->
    <script src="../js/components/google-map.js"></script>
    
    <!-- Load navbar -->
    <script src="../js/components/navbar.js"></script>
    
    <!-- Load data loader -->
    <script src="../js/data-loader.js"></script>

    <script>
        let sportsMap;
        
        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            loadComponents();
            initializeMap();
        });

        // Load navbar and footer components
        function loadComponents() {
            // Load navbar
            fetch('components/navbar.html')
                .then(res => res.text())
                .then(data => {
                    document.getElementById('navbar-container').innerHTML = data;
                    if (typeof Navbar !== 'undefined') {
                        new Navbar();
                    }
                });

            // Load footer
            fetch('components/footer.html')
                .then(res => res.text())
                .then(data => {
                    document.getElementById('footer-container').innerHTML = data;
                });
        }

        // Initialize Google Maps with data from JSON
        async function initializeMap() {
            const statusElement = document.getElementById('map-status');
            
            // Check if API key is configured
            if (MAPS_CONFIG.apiKey === 'YOUR_GOOGLE_MAPS_API_KEY') {
                statusElement.innerHTML = `
                    <div class="error">
                        <h3>Google Maps API Key Required</h3>
                        <p>Please configure your Google Maps API key in <code>js/config/maps-config.js</code></p>
                        <p>Get your API key from <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a></p>
                    </div>
                `;
                return;
            }

            try {
                // Load facilities data
                const data = await window.dataLoader.loadFacilities();
                const facilities = data.facilities || [];

                // Load Google Maps API
                loadGoogleMapsAPI(MAPS_CONFIG.apiKey, function() {
                    try {
                        // Create the map
                        sportsMap = new GoogleMap('sports-map', {
                            center: MAPS_CONFIG.defaultCenter,
                            zoom: MAPS_CONFIG.defaultZoom
                        });

                        // Add facilities from JSON data
                        sportsMap.addSportsFacilities(facilities);
                        
                        // Fit map to show all markers
                        sportsMap.fitToMarkers();

                        statusElement.innerHTML = `<p style="color: #28a745;">✓ Map loaded successfully with ${facilities.length} facilities! Click on markers to view facility details.</p>`;
                        
                        // Initialize button events
                        initializeMapControls(facilities);

                    } catch (error) {
                        console.error('Error initializing map:', error);
                        statusElement.innerHTML = `
                            <div class="error">
                                Error loading map: ${error.message}
                            </div>
                        `;
                    }
                });

            } catch (error) {
                console.error('Error loading facility data:', error);
                statusElement.innerHTML = `
                    <div class="error">
                        Error loading facility data: ${error.message}
                    </div>
                `;
            }
        }

        // Initialize map control buttons
        function initializeMapControls(facilities) {
            // Find user location
            document.getElementById('find-location-btn').addEventListener('click', function() {
                const btn = this;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Finding location...';
                
                sportsMap.addUserLocation()
                    .then(() => {
                        btn.innerHTML = '<i class="fas fa-check"></i> Location found!';
                        setTimeout(() => {
                            btn.innerHTML = '<i class="fas fa-location-arrow"></i> Find My Location';
                            btn.disabled = false;
                        }, 2000);
                    })
                    .catch((error) => {
                        btn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Location unavailable';
                        console.error('Error getting location:', error);
                        setTimeout(() => {
                            btn.innerHTML = '<i class="fas fa-location-arrow"></i> Find My Location';
                            btn.disabled = false;
                        }, 3000);
                    });
            });

            // Show all facilities
            document.getElementById('show-all-btn').addEventListener('click', function() {
                sportsMap.clearMarkers();
                sportsMap.addSportsFacilities(facilities);
                sportsMap.fitToMarkers();
            });

            // Clear markers
            document.getElementById('clear-markers-btn').addEventListener('click', function() {
                sportsMap.clearMarkers();
            });
        }

        // Global logout function
        function logout() {
            localStorage.removeItem('currentUser');
            localStorage.removeItem('authToken');
            window.location.href = '../index.html';
        }
    </script>
</body>
</html>