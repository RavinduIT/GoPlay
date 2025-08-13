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

// Load coaches from localStorage and display them
function loadCoachesFromLocalStorage() {
    try {
        // Import book-coach script to access CoachManager if not already available
        if (typeof window.CoachManager === 'undefined') {
            // Try to get coaches directly from localStorage
            window.CoachManager = {
                getAllCoaches: () => JSON.parse(localStorage.getItem('coaches') || '[]'),
                addCoach: (newCoach) => {
                    const coaches = JSON.parse(localStorage.getItem('coaches') || '[]');
                    if (!newCoach.id) {
                        const maxId = coaches.reduce((max, coach) => Math.max(max, coach.id || 0), 0);
                        newCoach.id = maxId + 1;
                    }
                    coaches.push(newCoach);
                    localStorage.setItem('coaches', JSON.stringify(coaches));
                    return newCoach;
                },
                updateCoach: (updatedCoach) => {
                    const coaches = JSON.parse(localStorage.getItem('coaches') || '[]');
                    const index = coaches.findIndex(coach => coach.id === updatedCoach.id);
                    if (index !== -1) {
                        coaches[index] = updatedCoach;
                        localStorage.setItem('coaches', JSON.stringify(coaches));
                        return true;
                    }
                    return false;
                },
                removeCoach: (coachId) => {
                    const coaches = JSON.parse(localStorage.getItem('coaches') || '[]');
                    const filteredCoaches = coaches.filter(coach => coach.id !== coachId);
                    localStorage.setItem('coaches', JSON.stringify(filteredCoaches));
                    return true;
                }
            };
        }

        // Get coaches from localStorage
        const coaches = window.CoachManager.getAllCoaches();
        
        // Clear existing coach list and display real data
        displayCoaches(coaches);
        
        // Update stats based on real data
        updateStatsFromCoachData(coaches);
        
    } catch (error) {
        console.error('Error loading coaches from localStorage:', error);
    }
}

// Display coaches in the admin dashboard
function displayCoaches(coaches) {
    const coachList = document.querySelector('.coach-list');
    if (!coachList) return;
    
    // Clear existing hardcoded coaches
    coachList.innerHTML = '';
    
    // Display each coach from localStorage
    coaches.forEach(coach => {
        const coachItem = createCoachItemFromData(coach);
        coachList.appendChild(coachItem);
        addSingleCardHoverEffect(coachItem);
    });
}

// Create coach item from localStorage data
function createCoachItemFromData(coach) {
    const coachItem = document.createElement('div');
    coachItem.className = 'coach-item';
    coachItem.dataset.coachId = coach.id; // Store coach ID for edit/delete operations
    
    const initials = getInitials(coach.name || 'Unknown Coach');
    const experience = typeof coach.experience === 'string' ? coach.experience : `${coach.experience || 0} years`;
    const specializations = Array.isArray(coach.specialization) ? coach.specialization.join(', ') : (coach.specialization || 'General');
    const bio = coach.bio || 'No biography available';
    const bioPreview = bio.length > 50 ? bio.substring(0, 50) + '...' : bio;
    
    coachItem.innerHTML = `
        <div class="coach-info">
            <div class="coach-avatar">${initials}</div>
            <div class="coach-details">
                <h3>${coach.name || 'Unknown Coach'}</h3>
                <div class="coach-meta">${specializations} • ${experience}</div>
                <div class="coach-meta" style="margin-top: 4px;">${bioPreview}</div>
            </div>
        </div>
        <div class="coach-stats">
            <span style="font-weight: 600;">$${coach.hourlyRate || 0}/hour</span>
            <span style="color: #059669; font-weight: 600;">⭐ ${coach.rating || 'N/A'}</span>
            <span class="coach-badge ${coach.status === 'Active' ? 'active' : 'inactive'}">${coach.status || 'active'}</span>
        </div>
        <div class="coach-actions">
            <button class="btn-icon btn-edit" title="Edit Coach" data-coach-id="${coach.id}">✏️</button>
            <button class="btn-icon btn-delete" title="Delete Coach" data-coach-id="${coach.id}">🗑️</button>
        </div>
    `;
    
    return coachItem;
}

// Update stats based on real coach data
function updateStatsFromCoachData(coaches) {
    const activeCoaches = coaches.filter(coach => coach.status === 'Active');
    const totalCoaches = activeCoaches.length;
    
    // Calculate average rating
    const avgRating = activeCoaches.length > 0 
        ? (activeCoaches.reduce((sum, coach) => sum + (coach.rating || 0), 0) / activeCoaches.length).toFixed(1)
        : '0.0';
    
    // Update the stats display
    const statElements = {
        coaches: document.querySelector('.stats-grid .stat-card:nth-child(1) .stat-value'),
        rating: document.querySelector('.stats-grid .stat-card:nth-child(4) .stat-value')
    };
    
    if (statElements.coaches) {
        statElements.coaches.textContent = totalCoaches;
    }
    
    if (statElements.rating) {
        statElements.rating.textContent = avgRating;
    }
}

// DOM Content Loaded Event
document.addEventListener('DOMContentLoaded', function() {
    
    // Load coaches from localStorage and display them
    loadCoachesFromLocalStorage();
    
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

// Add New Coach to List and localStorage
function addNewCoach(formData) {
    try {
        // Create coach object in the format expected by the booking system
        const newCoach = {
            name: formData['coach-name'] || 'New Coach',
            email: `${formData['coach-name'].toLowerCase().replace(' ', '.')}@example.com`,
            phone: '+1234567890', // Default phone
            specialization: [formData['coach-sport'] || 'General'], // Array format
            experience: `${formData['coach-experience'] || 0} years`,
            location: 'Not specified', // Default location
            bio: formData['coach-bio'] || 'Professional coach with expertise in sports training.',
            certifications: ['Certified Coach'], // Default certification
            hourlyRate: parseFloat(formData['coach-price']) || 50,
            availability: ['Monday', 'Wednesday', 'Friday'], // Default availability
            rating: 4.5, // Default rating
            totalStudents: 0, // New coach starts with 0 students
            joinDate: new Date().toISOString().split('T')[0],
            status: 'Active',
            profileImage: 'default_coach.jpg'
        };
        
        // Add to localStorage using CoachManager
        const addedCoach = window.CoachManager.addCoach(newCoach);
        
        // Refresh the coach list display
        loadCoachesFromLocalStorage();
        
        console.log('Coach added successfully:', addedCoach);
        
    } catch (error) {
        console.error('Error adding new coach:', error);
        alert('Error adding coach. Please try again.');
    }
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
    const coachId = parseInt(button.dataset.coachId);
    const coaches = window.CoachManager.getAllCoaches();
    const coach = coaches.find(c => c.id === coachId);
    
    if (!coach) {
        alert('Coach not found!');
        return;
    }
    
    // Create a simple edit form
    const editForm = createEditForm(coach);
    
    // Show the edit form in a modal-like overlay
    showEditModal(editForm, coach);
}

// Create edit form for coach
function createEditForm(coach) {
    const specialization = Array.isArray(coach.specialization) ? coach.specialization[0] : coach.specialization;
    const experience = typeof coach.experience === 'string' ? coach.experience.replace(' years', '') : coach.experience;
    
    return `
        <div class="edit-modal-overlay" id="editModalOverlay">
            <div class="edit-modal">
                <h3>Edit Coach</h3>
                <form id="editCoachForm">
                    <div class="form-group">
                        <label>Coach Name</label>
                        <input type="text" id="edit-name" value="${coach.name}" required>
                    </div>
                    <div class="form-group">
                        <label>Sport</label>
                        <select id="edit-sport" required>
                            <option value="Basketball" ${specialization === 'Basketball' ? 'selected' : ''}>Basketball</option>
                            <option value="Tennis" ${specialization === 'Tennis' ? 'selected' : ''}>Tennis</option>
                            <option value="Football" ${specialization === 'Football' ? 'selected' : ''}>Football</option>
                            <option value="Soccer" ${specialization === 'Soccer' ? 'selected' : ''}>Soccer</option>
                            <option value="Cricket" ${specialization === 'Cricket' ? 'selected' : ''}>Cricket</option>
                            <option value="Swimming" ${specialization === 'Swimming' ? 'selected' : ''}>Swimming</option>
                            <option value="Badminton" ${specialization === 'Badminton' ? 'selected' : ''}>Badminton</option>
                            <option value="Volleyball" ${specialization === 'Volleyball' ? 'selected' : ''}>Volleyball</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Experience (years)</label>
                        <input type="number" id="edit-experience" value="${experience}" required min="0">
                    </div>
                    <div class="form-group">
                        <label>Price per Hour ($)</label>
                        <input type="number" id="edit-price" value="${coach.hourlyRate}" required min="0" step="0.01">
                    </div>
                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" id="edit-location" value="${coach.location || ''}">
                    </div>
                    <div class="form-group">
                        <label>Biography</label>
                        <textarea id="edit-bio" rows="3">${coach.bio || ''}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select id="edit-status">
                            <option value="Active" ${coach.status === 'Active' ? 'selected' : ''}>Active</option>
                            <option value="Inactive" ${coach.status === 'Inactive' ? 'selected' : ''}>Inactive</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="button" onclick="closeEditModal()" class="btn-secondary">Cancel</button>
                        <button type="submit" class="btn-primary">Update Coach</button>
                    </div>
                </form>
            </div>
        </div>
    `;
}

// Show edit modal
function showEditModal(formHTML, coach) {
    // Remove existing modal if any
    const existingModal = document.getElementById('editModalOverlay');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', formHTML);
    
    // Add event listener for form submission
    const form = document.getElementById('editCoachForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        saveCoachEdit(coach.id);
    });
    
    // Add CSS for modal if not already added
    if (!document.getElementById('editModalStyles')) {
        const styles = document.createElement('style');
        styles.id = 'editModalStyles';
        styles.textContent = `
            .edit-modal-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 1000;
            }
            .edit-modal {
                background: white;
                padding: 2rem;
                border-radius: 12px;
                width: 90%;
                max-width: 500px;
                max-height: 80vh;
                overflow-y: auto;
            }
            .edit-modal h3 {
                margin-bottom: 1.5rem;
                color: #1e293b;
            }
            .edit-modal .form-group {
                margin-bottom: 1rem;
            }
            .edit-modal label {
                display: block;
                margin-bottom: 0.5rem;
                font-weight: 500;
                color: #374151;
            }
            .edit-modal input, .edit-modal select, .edit-modal textarea {
                width: 100%;
                padding: 0.75rem;
                border: 2px solid #d1d5db;
                border-radius: 8px;
                font-size: 0.875rem;
            }
            .edit-modal .form-actions {
                display: flex;
                gap: 1rem;
                justify-content: flex-end;
                margin-top: 1.5rem;
            }
            .edit-modal .btn-secondary, .edit-modal .btn-primary {
                padding: 0.75rem 1.5rem;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                font-weight: 500;
            }
            .edit-modal .btn-secondary {
                background: #f3f4f6;
                color: #374151;
            }
            .edit-modal .btn-primary {
                background: #6366f1;
                color: white;
            }
        `;
        document.head.appendChild(styles);
    }
}

// Close edit modal
function closeEditModal() {
    const modal = document.getElementById('editModalOverlay');
    if (modal) {
        modal.remove();
    }
}

// Save coach edit
function saveCoachEdit(coachId) {
    try {
        const updatedCoach = {
            id: coachId,
            name: document.getElementById('edit-name').value,
            specialization: [document.getElementById('edit-sport').value],
            experience: `${document.getElementById('edit-experience').value} years`,
            hourlyRate: parseFloat(document.getElementById('edit-price').value),
            location: document.getElementById('edit-location').value,
            bio: document.getElementById('edit-bio').value,
            status: document.getElementById('edit-status').value
        };
        
        // Get existing coach data to preserve other fields
        const coaches = window.CoachManager.getAllCoaches();
        const existingCoach = coaches.find(c => c.id === coachId);
        
        if (existingCoach) {
            // Merge with existing data
            const finalCoach = { ...existingCoach, ...updatedCoach };
            
            // Update in localStorage
            const success = window.CoachManager.updateCoach(finalCoach);
            
            if (success) {
                // Refresh the display
                loadCoachesFromLocalStorage();
                closeEditModal();
                
                // Show success message
                alert('Coach updated successfully!');
            } else {
                alert('Failed to update coach. Please try again.');
            }
        }
        
    } catch (error) {
        console.error('Error updating coach:', error);
        alert('Error updating coach. Please try again.');
    }
}

// Handle Delete Coach
function handleDeleteCoach(button) {
    const coachId = parseInt(button.dataset.coachId);
    const coaches = window.CoachManager.getAllCoaches();
    const coach = coaches.find(c => c.id === coachId);
    
    if (!coach) {
        alert('Coach not found!');
        return;
    }
    
    if (confirm(`Are you sure you want to delete ${coach.name}?\n\nThis action cannot be undone and will remove the coach from both the admin dashboard and the booking system.`)) {
        try {
            // Remove from localStorage
            const success = window.CoachManager.removeCoach(coachId);
            
            if (success) {
                // Add fade out animation
                const coachItem = button.closest('.coach-item');
                coachItem.style.transition = 'all 0.3s ease';
                coachItem.style.opacity = '0';
                coachItem.style.transform = 'translateX(-20px)';
                
                setTimeout(() => {
                    // Refresh the entire coach list to ensure consistency
                    loadCoachesFromLocalStorage();
                    
                    // Show success message
                    alert('Coach deleted successfully!');
                }, 300);
            } else {
                alert('Failed to delete coach. Please try again.');
            }
            
        } catch (error) {
            console.error('Error deleting coach:', error);
            alert('Error deleting coach. Please try again.');
        }
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