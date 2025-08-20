<?php 
$title = 'Book Ground - GoPlay Sports Platform';
$additionalCSS = ['/public/css/pages/book-ground.css'];
$additionalJS = ['/public/js/pages/book-ground.js'];
?>

<div class="page-header">
    <div class="container">
        <h1>Book Sports Ground</h1>
        <p>Find and reserve the perfect sports facility for your needs</p>
    </div>
</div>

<!-- Filters Section -->
<section class="filters-section">
    <div class="container">
        <div class="filters-container">
            <div class="filter-group">
                <label for="location">Location</label>
                <select id="location" class="filter-select">
                    <option value="">All Locations</option>
                    <option value="colombo">Colombo</option>
                    <option value="kandy">Kandy</option>
                    <option value="galle">Galle</option>
                    <option value="negombo">Negombo</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="sport">Sport Type</label>
                <select id="sport" class="filter-select">
                    <option value="">All Sports</option>
                    <option value="football">Football</option>
                    <option value="cricket">Cricket</option>
                    <option value="tennis">Tennis</option>
                    <option value="basketball">Basketball</option>
                    <option value="badminton">Badminton</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="date">Date</label>
                <input type="date" id="date" class="filter-input" min="<?= date('Y-m-d') ?>">
            </div>

            <div class="filter-group">
                <label for="time">Time</label>
                <select id="time" class="filter-select">
                    <option value="">Any Time</option>
                    <option value="morning">Morning (6:00 - 12:00)</option>
                    <option value="afternoon">Afternoon (12:00 - 18:00)</option>
                    <option value="evening">Evening (18:00 - 22:00)</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="price">Max Price (per hour)</label>
                <input type="number" id="price" class="filter-input" placeholder="2000" min="0">
            </div>

            <button id="searchBtn" class="btn btn-primary">Search Grounds</button>
            <button id="clearBtn" class="btn btn-outline">Clear Filters</button>
        </div>
    </div>
</section>

<!-- Results Section -->
<section class="results-section">
    <div class="container">
        <div class="results-header">
            <h2>Available Grounds</h2>
            <div class="view-toggle">
                <button class="view-btn active" data-view="grid">Grid</button>
                <button class="view-btn" data-view="list">List</button>
            </div>
        </div>

        <div id="groundsContainer" class="grounds-grid">
            <!-- Ground cards will be loaded here -->
            <div class="ground-card">
                <div class="ground-image">
                    <img src="/public/assets/images/placeholder.jpg" alt="Football Ground">
                    <div class="ground-badge">Featured</div>
                </div>
                <div class="ground-info">
                    <h3 class="ground-name">City Football Complex</h3>
                    <div class="ground-location">📍 Colombo 05</div>
                    <div class="ground-type">⚽ Football Ground</div>
                    <div class="ground-rating">
                        <span class="stars">⭐⭐⭐⭐⭐</span>
                        <span class="rating-text">4.8 (120 reviews)</span>
                    </div>
                    <div class="ground-features">
                        <span class="feature">Floodlights</span>
                        <span class="feature">Parking</span>
                        <span class="feature">Changing Rooms</span>
                    </div>
                    <div class="ground-price">
                        <span class="price">Rs. 1,500</span>
                        <span class="unit">per hour</span>
                    </div>
                    <div class="ground-actions">
                        <button class="btn btn-outline btn-sm">View Details</button>
                        <button class="btn btn-primary btn-sm">Book Now</button>
                    </div>
                </div>
            </div>

            <div class="ground-card">
                <div class="ground-image">
                    <img src="/public/assets/images/placeholder.jpg" alt="Tennis Court">
                </div>
                <div class="ground-info">
                    <h3 class="ground-name">Elite Tennis Courts</h3>
                    <div class="ground-location">📍 Kandy</div>
                    <div class="ground-type">🎾 Tennis Court</div>
                    <div class="ground-rating">
                        <span class="stars">⭐⭐⭐⭐</span>
                        <span class="rating-text">4.5 (89 reviews)</span>
                    </div>
                    <div class="ground-features">
                        <span class="feature">Clay Court</span>
                        <span class="feature">Equipment Rental</span>
                        <span class="feature">Coaching</span>
                    </div>
                    <div class="ground-price">
                        <span class="price">Rs. 2,000</span>
                        <span class="unit">per hour</span>
                    </div>
                    <div class="ground-actions">
                        <button class="btn btn-outline btn-sm">View Details</button>
                        <button class="btn btn-primary btn-sm">Book Now</button>
                    </div>
                </div>
            </div>

            <div class="ground-card">
                <div class="ground-image">
                    <img src="/public/assets/images/placeholder.jpg" alt="Basketball Court">
                </div>
                <div class="ground-info">
                    <h3 class="ground-name">Urban Basketball Arena</h3>
                    <div class="ground-location">📍 Galle</div>
                    <div class="ground-type">🏀 Basketball Court</div>
                    <div class="ground-rating">
                        <span class="stars">⭐⭐⭐⭐⭐</span>
                        <span class="rating-text">4.9 (156 reviews)</span>
                    </div>
                    <div class="ground-features">
                        <span class="feature">Indoor</span>
                        <span class="feature">Air Conditioning</span>
                        <span class="feature">Spectator Seating</span>
                    </div>
                    <div class="ground-price">
                        <span class="price">Rs. 1,200</span>
                        <span class="unit">per hour</span>
                    </div>
                    <div class="ground-actions">
                        <button class="btn btn-outline btn-sm">View Details</button>
                        <button class="btn btn-primary btn-sm">Book Now</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <button class="pagination-btn" disabled>Previous</button>
            <span class="pagination-info">Page 1 of 5</span>
            <button class="pagination-btn">Next</button>
        </div>
    </div>
</section>

<script>
// Filter functionality
document.getElementById('searchBtn').addEventListener('click', function() {
    // Get filter values
    const location = document.getElementById('location').value;
    const sport = document.getElementById('sport').value;
    const date = document.getElementById('date').value;
    const time = document.getElementById('time').value;
    const price = document.getElementById('price').value;
    
    console.log('Searching with filters:', {location, sport, date, time, price});
    
    // Here you would typically make an AJAX request to search for grounds
    // For now, we'll just show a loading state
    const container = document.getElementById('groundsContainer');
    container.innerHTML = '<div class="loading">Searching for grounds...</div>';
    
    // Simulate API call
    setTimeout(() => {
        // Reload the container with filtered results
        loadGrounds({location, sport, date, time, price});
    }, 1000);
});

// Clear filters
document.getElementById('clearBtn').addEventListener('click', function() {
    document.querySelectorAll('.filter-select, .filter-input').forEach(input => {
        input.value = '';
    });
    loadGrounds();
});

// View toggle
document.querySelectorAll('.view-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        const container = document.getElementById('groundsContainer');
        if (this.dataset.view === 'list') {
            container.classList.add('grounds-list');
            container.classList.remove('grounds-grid');
        } else {
            container.classList.add('grounds-grid');
            container.classList.remove('grounds-list');
        }
    });
});

function loadGrounds(filters = {}) {
    // This function would load grounds based on filters
    console.log('Loading grounds with filters:', filters);
    
    // For now, just reload the default content
    // In a real app, this would make an API call
}

// Initialize with today's date
document.getElementById('date').value = new Date().toISOString().split('T')[0];
</script>