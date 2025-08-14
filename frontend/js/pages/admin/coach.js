// Tab Navigation Function
function showTab(tabName) {
    // Hide all tab contents
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => {
        content.classList.remove('active');
    });

    // Remove active class from all tabs
    const tabs = document.querySelectorAll('.nav-tab');
    tabs.forEach(tab => {
        tab.classList.remove('active');
    });

    // Show selected tab content
    document.getElementById(tabName).classList.add('active');

    // Add active class to clicked tab
    event.target.classList.add('active');
}

// DOM Content Loaded Event
document.addEventListener('DOMContentLoaded', function() {
    
    // Animate stats on load
    animateStats();
    
    // Add hover effects to cards
    addCardHoverEffects();
    
    // Setup form validation
    setupFormValidation();
    
    // Setup button interactions
    setupButtonInteractions();
    
    // Add search functionality
    setTimeout(addSearchFunctionality, 1000);
});

// Animate Statistics Cards
function animateStats() {
    const statValues = document.querySelectorAll('.stat-value');
    statValues.forEach((stat, index) => {
        stat.style.transform = 'scale(0.8)';
        stat.style.opacity = '0';
        
        setTimeout(() => {
            stat.style.transition = 'all 0.5s ease';
            stat.style.transform = 'scale(1)';
            stat.style.opacity = '1';
        }, index * 100 + Math.random() * 300);
    });
}

// Add Hover Effects to Cards
function addCardHoverEffects() {
    const cards = document.querySelectorAll('.coach-item, .session-item, .cert-item');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(4px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });
}

// Form Validation Setup
function setupFormValidation() {
    const addButton = document.getElementById('add-coach-btn');
    const inputs = document.querySelectorAll('.form-input');
    
    if (addButton) {
        addButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            let isValid = true;
            const formData = {};
            
            // Validate each input
            inputs.forEach(input => {
                const fieldName = input.id;
                const fieldValue = input.value.trim();
                
                // Check if field is required (name, sport, experience, price are required)
                const isRequired = ['coach-name', 'coach-sport', 'coach-experience', 'coach-price'].includes(fieldName);
                
                if (isRequired && fieldValue === '') {
                    input.style.borderColor = '#dc2626';
                    input.style.boxShadow = '0 0 0 3px rgba(220, 38, 38, 0.1)';
                    isValid = false;
                } else if (fieldValue !== '' || isRequired) {
                    input.style.borderColor = '#d1d5db';
                    input.style.boxShadow = 'none';
                    formData[fieldName] = fieldValue;
                }
            });
            
            if (isValid && formData['coach-name'] && formData['coach-sport']) {
                // Add new coach to the list
                addNewCoach(formData);
                
                // Show success message
                showSuccessMessage(addButton);
                
                // Clear form
                clearForm(inputs);
                
                // Update stats
                updateCoachCount();
            } else {
                // Show error message
                showErrorMessage(addButton);
            }
        });
    }
}

// Add New Coach to List
function addNewCoach(formData) {
    const coachList = document.querySelector('.coach-list');
    if (!coachList) return;
    
    const initials = getInitials(formData['coach-name'] || 'New Coach');
    const coachItem = createCoachItem({
        initials: initials,
        name: formData['coach-name'] || 'New Coach',
        sport: formData['coach-sport'] || 'Unknown',
        experience: formData['coach-experience'] || '0',
        price: formData['coach-price'] || '0',
        bio: formData['coach-bio'] || ''
    });
    
    coachList.appendChild(coachItem);
    
    // Add hover effect to new item
    addSingleCardHoverEffect(coachItem);
}

// Create Coach Item Element
function createCoachItem(coach) {
    const coachItem = document.createElement('div');
    coachItem.className = 'coach-item';
    
    const bioPreview = coach.bio.length > 30 ? coach.bio.substring(0, 30) + '...' : coach.bio;
    const randomRating = (Math.random() * 1 + 4).toFixed(1);
    
    coachItem.innerHTML = `
        <div class="coach-info">
            <div class="coach-avatar">${coach.initials}</div>
            <div class="coach-details">
                <h3>${coach.name}</h3>
                <div class="coach-meta">${coach.sport} • ${coach.experience} years</div>
                <div class="coach-meta" style="margin-top: 4px;">${bioPreview}</div>
            </div>
        </div>
        <div class="coach-stats">
            <span style="font-weight: 600;">$${coach.price}/hour</span>
            <span style="color: #059669; font-weight: 600;">⭐ ${randomRating}</span>
            <span class="coach-badge">active</span>
        </div>
        <div class="coach-actions">
            <button class="btn-icon btn-edit" title="Edit Coach">✏️</button>
            <button class="btn-icon btn-delete" title="Delete Coach">🗑️</button>
        </div>
    `;
    
    return coachItem;
}

// Get Initials from Name
function getInitials(name) {
    return name.split(' ')
        .map(word => word.charAt(0).toUpperCase())
        .slice(0, 2)
        .join('');
}

// Show Success Message
function showSuccessMessage(button) {
    const originalText = button.textContent;
    const originalBackground = button.style.background || '#6366f1';
    
    button.textContent = 'Coach Added Successfully!';
    button.style.background = '#059669';
    button.disabled = true;
    
    setTimeout(() => {
        button.textContent = originalText;
        button.style.background = originalBackground;
        button.disabled = false;
    }, 2000);
}

// Show Error Message
function showErrorMessage(button) {
    const originalText = button.textContent;
    const originalBackground = button.style.background || '#6366f1';
    
    button.textContent = 'Please fill required fields!';
    button.style.background = '#dc2626';
    
    setTimeout(() => {
        button.textContent = originalText;
        button.style.background = originalBackground;
    }, 2000);
}

// Clear Form
function clearForm(inputs) {
    inputs.forEach(input => {
        input.value = '';
        input.style.borderColor = '#d1d5db';
        input.style.boxShadow = 'none';
    });
}

// Add Single Card Hover Effect
function addSingleCardHoverEffect(card) {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateX(4px)';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateX(0)';
    });
}

// Setup Button Interactions
function setupButtonInteractions() {
    // Edit and Delete button handlers
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-edit')) {
            handleEditCoach(e.target);
        } else if (e.target.classList.contains('btn-delete')) {
            handleDeleteCoach(e.target);
        }
    });
}

// Handle Edit Coach
function handleEditCoach(button) {
    const coachItem = button.closest('.coach-item');
    const coachName = coachItem.querySelector('h3').textContent;
    
    // Simple edit functionality - you can expand this
    const newName = prompt('Edit coach name:', coachName);
    if (newName && newName.trim() !== '' && newName.trim() !== coachName) {
        coachItem.querySelector('h3').textContent = newName.trim();
        
        // Update initials
        const avatar = coachItem.querySelector('.coach-avatar');
        avatar.textContent = getInitials(newName.trim());
        
        // Show success feedback
        const originalBg = button.style.background;
        button.style.background = '#059669';
        button.style.color = 'white';
        
        setTimeout(() => {
            button.style.background = originalBg;
            button.style.color = '#6366f1';
        }, 1000);
    }
}

// Handle Delete Coach
function handleDeleteCoach(button) {
    const coachItem = button.closest('.coach-item');
    const coachName = coachItem.querySelector('h3').textContent;
    
    if (confirm(`Are you sure you want to delete ${coachName}?`)) {
        // Add fade out animation
        coachItem.style.transition = 'all 0.3s ease';
        coachItem.style.opacity = '0';
        coachItem.style.transform = 'translateX(-20px)';
        
        setTimeout(() => {
            coachItem.remove();
            updateCoachCount();
        }, 300);
    }
}

// Update Coach Count
function updateCoachCount() {
    const coachItems = document.querySelectorAll('.coach-item');
    const coachCountElement = document.querySelector('.stats-grid .stat-card:nth-child(1) .stat-value');
    
    if (coachCountElement) {
        const newCount = coachItems.length;
        animateValue(coachCountElement, newCount);
    }
}

// Update Stats (you can call this function when data changes)
function updateStats(stats) {
    const statElements = {
        coaches: document.querySelector('.stats-grid .stat-card:nth-child(1) .stat-value'),
        earnings: document.querySelector('.stats-grid .stat-card:nth-child(2) .stat-value'),
        sessions: document.querySelector('.stats-grid .stat-card:nth-child(3) .stat-value'),
        rating: document.querySelector('.stats-grid .stat-card:nth-child(4) .stat-value')
    };
    
    Object.keys(stats).forEach(key => {
        if (statElements[key]) {
            animateValue(statElements[key], stats[key]);
        }
    });
}

// Animate Value Change
function animateValue(element, newValue) {
    if (!element) return;
    
    const currentColor = element.style.color;
    element.style.transform = 'scale(1.1)';
    element.style.color = '#6366f1';
    
    setTimeout(() => {
        element.textContent = newValue;
        element.style.transform = 'scale(1)';
        element.style.color = currentColor || '#1e293b';
    }, 200);
}

// Search Functionality
function addSearchFunctionality() {
    const coachesTab = document.querySelector('#coaches .section-card');
    if (!coachesTab) return;
    
    const searchContainer = document.createElement('div');
    searchContainer.style.marginBottom = '1rem';
    
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.placeholder = 'Search coaches by name or sport...';
    searchInput.className = 'form-input';
    searchInput.id = 'coach-search';
    
    searchContainer.appendChild(searchInput);
    
    const coachListTitle = coachesTab.querySelector('h2:last-of-type');
    if (coachListTitle) {
        coachListTitle.parentNode.insertBefore(searchContainer, coachListTitle.nextSibling);
        
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const coachItems = document.querySelectorAll('.coach-item');
            
            coachItems.forEach(item => {
                const coachName = item.querySelector('h3').textContent.toLowerCase();
                const coachMeta = item.querySelector('.coach-meta').textContent.toLowerCase();
                
                if (searchTerm === '' || coachName.includes(searchTerm) || coachMeta.includes(searchTerm)) {
                    item.style.display = 'flex';
                    item.style.opacity = '1';
                    item.style.transform = 'translateX(0)';
                } else {
                    item.style.display = 'none';
                    item.style.opacity = '0';
                    item.style.transform = 'translateX(-10px)';
                }
            });
        });
    }
}

// Utility function to format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

// Utility function to validate form fields
function validateField(field, value) {
    switch(field) {
        case 'coach-name':
            return value.length >= 2;
        case 'coach-sport':
            return value !== 'Select sport' && value.length > 0;
        case 'coach-experience':
            return !isNaN(value) && parseInt(value) >= 0;
        case 'coach-price':
            return !isNaN(value) && parseFloat(value) >= 0;
        default:
            return true;
    }
}

// Initialize tooltips
function initializeTooltips() {
    const buttons = document.querySelectorAll('[title]');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.position = 'relative';
        });
    });
}

// Export functions for external use (if needed)
if (typeof window !== 'undefined') {
    window.CoachDashboard = {
        showTab,
        updateStats,
        addNewCoach,
        formatCurrency,
        validateField
    };
}