// Dashboard functionality with localStorage integration using 'sportsGrounds' key
document.addEventListener('DOMContentLoaded', function() {
    // Initialize grounds from localStorage
    loadGroundsFromStorage();
    
    // Tab switching functionality
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const targetTab = button.getAttribute('data-tab');
            
            // Remove active class from all tabs and panes
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabPanes.forEach(pane => pane.classList.remove('active'));
            
            // Add active class to clicked tab and corresponding pane
            button.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
        });
    });

    // Add Ground form handling
    const addGroundForm = document.querySelector('.add-ground-form');
    if (addGroundForm) {
        addGroundForm.addEventListener('submit', handleAddGround);
    }

    // Ground card actions
    attachGroundCardEvents();

    // Animate stats on page load
    animateStats();

    // Animate popularity bars
    animatePopularityBars();
});

// Ground Management Functions
function getGroundsFromStorage() {
    try {
        const storedData = localStorage.getItem('sportsGrounds');
        if (!storedData) {
            return [];
        }
        
        const parsedData = JSON.parse(storedData);
        
        // Handle both array format and object format with 'grounds' property
        if (Array.isArray(parsedData)) {
            return parsedData;
        } else if (parsedData && parsedData.grounds && Array.isArray(parsedData.grounds)) {
            return parsedData.grounds;
        }
        
        return [];
    } catch (error) {
        console.error('Error loading grounds from localStorage:', error);
        return [];
    }
}

function saveGroundsToStorage(grounds) {
    try {
        // Store as array format for consistency with the booking page
        localStorage.setItem('sportsGrounds', JSON.stringify(grounds));
        
        // Also create a downloadable JSON structure for reference
        const groundsData = {
            lastUpdated: new Date().toISOString(),
            totalGrounds: grounds.length,
            grounds: grounds
        };
        
        // Create a blob URL for the JSON file (for download purposes)
        const jsonString = JSON.stringify(groundsData, null, 2);
        const blob = new Blob([jsonString], { type: 'application/json' });
        
        // Store the blob URL for potential download
        window.groundsJsonUrl = URL.createObjectURL(blob);
        
        console.log('Grounds data saved to localStorage (sportsGrounds) and prepared for JSON export');
        
        // Trigger custom event to notify other parts of the application
        window.dispatchEvent(new CustomEvent('sportsGroundsUpdated', { detail: grounds }));
        
    } catch (error) {
        console.error('Error saving grounds to localStorage:', error);
        showNotification('Error saving ground data', 'error');
    }
}

function loadGroundsFromStorage() {
    const grounds = getGroundsFromStorage();
    const groundsList = document.querySelector('.grounds-list');
    
    if (groundsList) {
        // Clear existing grounds
        groundsList.innerHTML = '';
        
        // Add each ground to the list
        grounds.forEach(ground => {
            const groundCard = createGroundCard(ground);
            groundsList.appendChild(groundCard);
        });
        
        // Add a subtle entrance animation
        const groundCards = groundsList.querySelectorAll('.ground-card');
        groundCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.3s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    }
    
    // Update stats after loading
    updateStats();
    
    console.log(`Loaded ${grounds.length} grounds from sportsGrounds localStorage`);
}

// Refresh a single ground card without reloading all
function refreshGroundCard(groundId) {
    const grounds = getGroundsFromStorage();
    const ground = grounds.find(g => g.id === groundId);
    
    if (!ground) {
        console.warn(`Ground with ID ${groundId} not found`);
        return;
    }
    
    updateGroundCardDisplay(groundId, ground);
}

function generateGroundId() {
    return 'ground_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
}

// Handle add ground form submission
function handleAddGround(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const groundData = {
        id: generateGroundId(),
        name: e.target.querySelector('input[type="text"]').value.trim(),
        sports: [e.target.querySelector('select').value], // Store as array to match booking page format
        type: e.target.querySelector('select').value,
        location: e.target.querySelectorAll('input[type="text"]')[1].value.trim(),
        pricePerHour: parseFloat(e.target.querySelector('input[type="number"]').value),
        price: parseFloat(e.target.querySelector('input[type="number"]').value), // Keep both for compatibility
        description: e.target.querySelector('textarea').value.trim(),
        status: 'active',
        rating: 0, // Default rating
        amenities: [], // Default empty amenities
        availableSlots: ["9:00 AM", "11:00 AM", "2:00 PM", "4:00 PM", "6:00 PM"], // Default slots
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString()
    };

    // Validate form data
    if (!groundData.name || !groundData.type || !groundData.location || isNaN(groundData.pricePerHour) || groundData.pricePerHour < 0) {
        showNotification('Please fill in all required fields correctly', 'error');
        return;
    }

    // Show loading state
    showNotification('Adding ground...', 'info');
    
    setTimeout(() => {
        // Get existing grounds
        const grounds = getGroundsFromStorage();
        
        // Add new ground
        grounds.push(groundData);
        
        // Save to localStorage
        saveGroundsToStorage(grounds);
        
        // Add new ground to the list
        addGroundToList(groundData);
        
        // Reset form
        e.target.reset();
        
        // Show success message
        showNotification('Ground added successfully!', 'success');
        
        // Update stats
        updateStats();
    }, 1000);
}

// Add ground to the grounds list
function addGroundToList(groundData) {
    const groundsList = document.querySelector('.grounds-list');
    if (groundsList) {
        const groundCard = createGroundCard(groundData);
        groundsList.appendChild(groundCard);
    }
}

// Create ground card element
function createGroundCard(groundData) {
    const card = document.createElement('div');
    card.className = 'ground-card';
    card.setAttribute('data-ground-id', groundData.id);
    
    const sportIcon = getSportIcon(groundData.type || (groundData.sports && groundData.sports[0]));
    const price = groundData.pricePerHour || groundData.price || 0;
    
    card.innerHTML = `
        <div class="ground-image">
            <div class="ground-placeholder">${sportIcon}</div>
        </div>
        <div class="ground-info">
            <h3>${groundData.name}</h3>
            <p>${groundData.type || (groundData.sports && groundData.sports[0]) || 'Sports'} • ${groundData.location}</p>
            <div class="price">Rs ${price}/hour</div>
            <div class="status ${groundData.status}">${groundData.status}</div>
            <div class="sports-tags">
                <span class="tag">${groundData.type || (groundData.sports && groundData.sports[0]) || 'Sports'}</span>
            </div>
        </div>
        <div class="ground-actions">
            <button class="btn-icon edit-btn" data-tooltip="Edit Ground">✏️</button>
            <button class="btn-icon delete-btn" data-tooltip="Delete Ground">🗑️</button>
        </div>
    `;

    // Attach event listeners
    const editBtn = card.querySelector('.edit-btn');
    const deleteBtn = card.querySelector('.delete-btn');
    
    editBtn.addEventListener('click', (e) => handleEditGround(e, groundData.id));
    deleteBtn.addEventListener('click', (e) => handleDeleteGround(e, groundData.id));

    return card;
}

// Attach events to all ground cards
function attachGroundCardEvents() {
    const editButtons = document.querySelectorAll('.btn-icon.edit-btn');
    const deleteButtons = document.querySelectorAll('.btn-icon.delete-btn');

    editButtons.forEach(button => {
        const groundCard = button.closest('.ground-card');
        const groundId = groundCard.getAttribute('data-ground-id');
        button.addEventListener('click', (e) => handleEditGround(e, groundId));
    });

    deleteButtons.forEach(button => {
        const groundCard = button.closest('.ground-card');
        const groundId = groundCard.getAttribute('data-ground-id');
        button.addEventListener('click', (e) => handleDeleteGround(e, groundId));
    });
}

// Get sport icon based on type
function getSportIcon(type) {
    const icons = {
        'Football': '⚽',
        'Basketball': '🏀',
        'Tennis': '🎾',
        'Cricket': '🏏',
        'Badminton': '🏸',
        'Multi-Sport': '🏟️',
        'Swimming': '🏊',
        'Volleyball': '🏐'
    };
    return icons[type] || '🏟️';
}

// Handle edit ground
function handleEditGround(e, groundId) {
    const grounds = getGroundsFromStorage();
    const ground = grounds.find(g => g.id === groundId);
    
    if (!ground) {
        showNotification('Ground not found', 'error');
        return;
    }
    
    showNotification(`Editing ${ground.name}...`, 'info');
    
    // Create edit modal or form
    createEditModal(ground);
}

// Create edit modal
function createEditModal(ground) {
    // Remove existing modal
    const existingModal = document.querySelector('.edit-modal');
    if (existingModal) {
        existingModal.remove();
    }
    
    const currentType = ground.type || (ground.sports && ground.sports[0]) || '';
    const currentPrice = ground.pricePerHour || ground.price || 0;
    
    const modal = document.createElement('div');
    modal.className = 'edit-modal';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Ground</h3>
                <button class="close-modal">&times;</button>
            </div>
            <form class="edit-ground-form">
                <div class="form-group">
                    <label>Ground Name *</label>
                    <input type="text" value="${ground.name}" required>
                </div>
                <div class="form-group">
                    <label>Sport Type *</label>
                    <select required>
                        <option value="Football" ${currentType === 'Football' ? 'selected' : ''}>Football</option>
                        <option value="Basketball" ${currentType === 'Basketball' ? 'selected' : ''}>Basketball</option>
                        <option value="Tennis" ${currentType === 'Tennis' ? 'selected' : ''}>Tennis</option>
                        <option value="Cricket" ${currentType === 'Cricket' ? 'selected' : ''}>Cricket</option>
                        <option value="Badminton" ${currentType === 'Badminton' ? 'selected' : ''}>Badminton</option>
                        <option value="Multi-Sport" ${currentType === 'Multi-Sport' ? 'selected' : ''}>Multi-Sport</option>
                        <option value="Swimming" ${currentType === 'Swimming' ? 'selected' : ''}>Swimming</option>
                        <option value="Volleyball" ${currentType === 'Volleyball' ? 'selected' : ''}>Volleyball</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Location *</label>
                    <input type="text" value="${ground.location}" required>
                </div>
                <div class="form-group">
                    <label>Price per Hour *</label>
                    <input type="number" value="${currentPrice}" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea>${ground.description || ''}</textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel">Cancel</button>
                    <button type="submit" class="btn-save">Save Changes</button>
                </div>
            </form>
        </div>
    `;
    
    // Style the modal
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    `;
    
    // Add modal styles
    const modalStyles = `
        .modal-content {
            background: white;
            border-radius: 12px;
            padding: 24px;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .close-modal {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
        }
        .form-group {
            margin-bottom: 16px;
        }
        .form-group label {
            display: block;
            margin-bottom: 4px;
            font-weight: 500;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 8px 12px;
            border: 2px solid #e5e7eb;
            border-radius: 6px;
            font-size: 14px;
        }
        .form-group textarea {
            min-height: 80px;
            resize: vertical;
        }
        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 24px;
        }
        .btn-cancel, .btn-save {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
        }
        .btn-cancel {
            background: #f3f4f6;
            color: #374151;
        }
        .btn-save {
            background: #3b82f6;
            color: white;
        }
    `;
    
    const styleSheet = document.createElement('style');
    styleSheet.textContent = modalStyles;
    document.head.appendChild(styleSheet);
    
    document.body.appendChild(modal);
    
    // Add event listeners
    modal.querySelector('.close-modal').addEventListener('click', () => modal.remove());
    modal.querySelector('.btn-cancel').addEventListener('click', () => modal.remove());
    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.remove();
    });
    
    modal.querySelector('.edit-ground-form').addEventListener('submit', (e) => {
        e.preventDefault();
        
        // Validate before updating
        const form = e.target;
        const name = form.querySelector('input[type="text"]').value.trim();
        const type = form.querySelector('select').value;
        const location = form.querySelectorAll('input[type="text"]')[1].value.trim();
        const price = parseFloat(form.querySelector('input[type="number"]').value);
        
        if (!name || !type || !location || isNaN(price) || price < 0) {
            showNotification('Please fill in all required fields correctly', 'error');
            return;
        }
        
        // Show updating notification
        showNotification('Updating ground...', 'info');
        
        // Update with a slight delay for better UX
        setTimeout(() => {
            updateGround(ground.id, form);
            modal.remove();
        }, 500);
    });
}

// Update ground data
function updateGround(groundId, form) {
    const grounds = getGroundsFromStorage();
    const groundIndex = grounds.findIndex(g => g.id === groundId);
    
    if (groundIndex === -1) {
        showNotification('Ground not found', 'error');
        return;
    }
    
    // Validate form data
    const name = form.querySelector('input[type="text"]').value.trim();
    const type = form.querySelector('select').value;
    const location = form.querySelectorAll('input[type="text"]')[1].value.trim();
    const price = parseFloat(form.querySelector('input[type="number"]').value);
    const description = form.querySelector('textarea').value.trim();
    
    if (!name || !type || !location || isNaN(price) || price < 0) {
        showNotification('Please fill in all required fields correctly', 'error');
        return;
    }
    
    // Update ground data
    const updatedGround = {
        ...grounds[groundIndex],
        name: name,
        type: type,
        sports: [type], // Update sports array too
        location: location,
        price: price,
        pricePerHour: price, // Keep both for compatibility
        description: description,
        updatedAt: new Date().toISOString()
    };
    
    grounds[groundIndex] = updatedGround;
    
    // Save to localStorage
    saveGroundsToStorage(grounds);
    
    // Update the specific ground card in the DOM
    updateGroundCardDisplay(groundId, updatedGround);
    
    // Update stats
    updateStats();
    
    showNotification('Ground updated successfully!', 'success');
}

// Update specific ground card display
function updateGroundCardDisplay(groundId, groundData) {
    const groundCard = document.querySelector(`[data-ground-id="${groundId}"]`);
    
    if (!groundCard) {
        console.warn(`Ground card with ID ${groundId} not found in DOM`);
        return;
    }
    
    const sportIcon = getSportIcon(groundData.type || (groundData.sports && groundData.sports[0]));
    const price = groundData.pricePerHour || groundData.price || 0;
    const sportType = groundData.type || (groundData.sports && groundData.sports[0]) || 'Sports';
    
    // Update the card content
    groundCard.innerHTML = `
        <div class="ground-image">
            <div class="ground-placeholder">${sportIcon}</div>
        </div>
        <div class="ground-info">
            <h3>${groundData.name}</h3>
            <p>${sportType} • ${groundData.location}</p>
            <div class="price">Rs ${price}/hour</div>
            <div class="status ${groundData.status}">${groundData.status}</div>
            <div class="sports-tags">
                <span class="tag">${sportType}</span>
            </div>
        </div>
        <div class="ground-actions">
            <button class="btn-icon edit-btn" data-tooltip="Edit Ground">✏️</button>
            <button class="btn-icon delete-btn" data-tooltip="Delete Ground">🗑️</button>
        </div>
    `;
    
    // Re-attach event listeners to the updated card
    const editBtn = groundCard.querySelector('.edit-btn');
    const deleteBtn = groundCard.querySelector('.delete-btn');
    
    editBtn.addEventListener('click', (e) => handleEditGround(e, groundData.id));
    deleteBtn.addEventListener('click', (e) => handleDeleteGround(e, groundData.id));
    
    // Add a subtle animation to show the update
    groundCard.style.transform = 'scale(1.02)';
    groundCard.style.transition = 'transform 0.2s ease';
    
    setTimeout(() => {
        groundCard.style.transform = 'scale(1)';
    }, 200);
}

// Handle delete ground
function handleDeleteGround(e, groundId) {
    const grounds = getGroundsFromStorage();
    const ground = grounds.find(g => g.id === groundId);
    
    if (!ground) {
        showNotification('Ground not found', 'error');
        return;
    }
    
    if (confirm(`Are you sure you want to delete ${ground.name}?`)) {
        showNotification('Deleting ground...', 'info');
        
        const groundCard = document.querySelector(`[data-ground-id="${groundId}"]`);
        
        // Animate out
        if (groundCard) {
            groundCard.style.transform = 'translateX(-100%)';
            groundCard.style.opacity = '0';
        }
        
        setTimeout(() => {
            // Remove from localStorage
            const updatedGrounds = grounds.filter(g => g.id !== groundId);
            saveGroundsToStorage(updatedGrounds);
            
            // Remove from DOM
            if (groundCard) {
                groundCard.remove();
            }
            
            showNotification('Ground deleted successfully!', 'success');
            updateStats();
        }, 300);
    }
}

// Export grounds data as JSON file
function exportGroundsJson() {
    const grounds = getGroundsFromStorage();
    const groundsData = {
        exportedAt: new Date().toISOString(),
        totalGrounds: grounds.length,
        grounds: grounds
    };
    
    const jsonString = JSON.stringify(groundsData, null, 2);
    const blob = new Blob([jsonString], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    
    const a = document.createElement('a');
    a.href = url;
    a.download = `sportsgrounds-${new Date().toISOString().split('T')[0]}.json`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    
    showNotification('Sports grounds data exported successfully!', 'success');
}

// Clear all grounds data (with confirmation)
function clearAllGrounds() {
    if (confirm('Are you sure you want to delete ALL sports grounds? This action cannot be undone.')) {
        localStorage.removeItem('sportsGrounds');
        loadGroundsFromStorage();
        showNotification('All sports grounds data cleared!', 'success');
    }
}

// Sync with external updates to sportsGrounds
function syncWithExternalUpdates() {
    window.addEventListener('storage', (e) => {
        if (e.key === 'sportsGrounds') {
            console.log('sportsGrounds localStorage updated externally, refreshing...');
            loadGroundsFromStorage();
        }
    });
    
    // Listen for custom events
    window.addEventListener('sportsGroundsUpdated', (e) => {
        console.log('sportsGrounds updated via custom event');
        loadGroundsFromStorage();
    });
}

// Update dashboard stats
function updateStats() {
    const grounds = getGroundsFromStorage();
    const totalGrounds = grounds.length;
    const statsValue = document.querySelector('.stat-value');
    
    if (statsValue) {
        animateNumber(statsValue, parseInt(statsValue.textContent) || 0, totalGrounds);
    }
    
    // Update other stats if they exist
    const activeGrounds = grounds.filter(g => g.status === 'active').length;
    const avgPrice = grounds.length > 0 ? (grounds.reduce((sum, g) => sum + (g.pricePerHour || g.price || 0), 0) / grounds.length).toFixed(2) : 0;
    
    // Update additional stats if elements exist
    const activeGroundsElement = document.querySelector('[data-stat="active-grounds"]');
    const avgPriceElement = document.querySelector('[data-stat="avg-price"]');
    
    if (activeGroundsElement) {
        animateNumber(activeGroundsElement, parseInt(activeGroundsElement.textContent) || 0, activeGrounds);
    }
    
    if (avgPriceElement) {
        avgPriceElement.textContent = `Rs ${avgPrice}`;
    }
}

// Animate number changes
function animateNumber(element, from, to) {
    const duration = 1000;
    const startTime = performance.now();
    
    function update(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        const current = Math.round(from + (to - from) * progress);
        element.textContent = current;
        
        if (progress < 1) {
            requestAnimationFrame(update);
        }
    }
    
    requestAnimationFrame(update);
}

// Animate stats on page load
function animateStats() {
    const statCards = document.querySelectorAll('.stat-card');
    
    statCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

// Animate popularity bars
function animatePopularityBars() {
    const popularityFills = document.querySelectorAll('.popularity-fill');
    
    setTimeout(() => {
        popularityFills.forEach((fill, index) => {
            const width = fill.style.width;
            fill.style.width = '0%';
            
            setTimeout(() => {
                fill.style.width = width;
            }, index * 200);
        });
    }, 500);
}

// Show notification
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    // Style the notification
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 1000;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        max-width: 300px;
        word-wrap: break-word;
    `;
    
    // Set background color based on type
    const colors = {
        success: '#22c55e',
        error: '#ef4444',
        info: '#3b82f6',
        warning: '#f59e0b'
    };
    
    notification.style.backgroundColor = colors[type] || colors.info;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 10);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    }, 300);
}

// Handle responsive navigation
function toggleMobileNav() {
    const nav = document.querySelector('.nav');
    nav.classList.toggle('mobile-open');
}

// Add scroll effect to header
window.addEventListener('scroll', () => {
    const header = document.querySelector('.header');
    if (header) {
        if (window.scrollY > 10) {
            header.style.boxShadow = '0 4px 20px rgba(0,0,0,0.1)';
        } else {
            header.style.boxShadow = '0 2px 4px rgba(0,0,0,0.1)';
        }
    }
});

// Keyboard navigation for tabs
document.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
        const activeTab = document.querySelector('.tab-btn.active');
        const tabs = Array.from(document.querySelectorAll('.tab-btn'));
        const currentIndex = tabs.indexOf(activeTab);
        
        if (currentIndex !== -1) {
            let nextIndex;
            if (e.key === 'ArrowLeft') {
                nextIndex = currentIndex > 0 ? currentIndex - 1 : tabs.length - 1;
            } else {
                nextIndex = currentIndex < tabs.length - 1 ? currentIndex + 1 : 0;
            }
            
            tabs[nextIndex].click();
            tabs[nextIndex].focus();
        }
    }
});

// Handle form input validation
document.addEventListener('input', (e) => {
    if (e.target.matches('input, select, textarea')) {
        validateField(e.target);
    }
});

function validateField(field) {
    const value = field.value.trim();
    
    // Remove existing validation classes
    field.classList.remove('field-valid', 'field-invalid');
    
    if (field.hasAttribute('required')) {
        if (value === '') {
            field.classList.add('field-invalid');
        } else {
            field.classList.add('field-valid');
        }
    }
    
    // Specific validation for price field
    if (field.type === 'number' && (field.placeholder === '0.00' || field.step === '0.01')) {
        if (value !== '' && (isNaN(value) || parseFloat(value) < 0)) {
            field.classList.add('field-invalid');
        } else if (value !== '') {
            field.classList.add('field-valid');
        }
    }
}

// Add CSS for field validation and modal
const style = document.createElement('style');
style.textContent = `
    .field-valid {
        border-color: #22c55e !important;
    }
    
    .field-invalid {
        border-color: #ef4444 !important;
    }
    
    @media (max-width: 768px) {
        .nav.mobile-open {
            display: flex;
            flex-direction: column;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            padding: 20px;
            gap: 15px;
        }
        
        .modal-content {
            width: 95% !important;
            max-height: 90vh !important;
        }
    }
`;
document.head.appendChild(style);

// Initialize tooltips (simple implementation)
function initTooltips() {
    document.addEventListener('mouseenter', (e) => {
        if (e.target.hasAttribute('data-tooltip')) {
            showTooltip(e);
        }
    }, true);
    
    document.addEventListener('mouseleave', (e) => {
        if (e.target.hasAttribute('data-tooltip')) {
            hideTooltip();
        }
    }, true);
}

function showTooltip(e) {
    hideTooltip(); // Remove existing tooltip
    
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip';
    tooltip.textContent = e.target.getAttribute('data-tooltip');
    tooltip.style.cssText = `
        position: absolute;
        background: #1e293b;
        color: white;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 12px;
        pointer-events: none;
        z-index: 1000;
        white-space: nowrap;
    `;
    
    document.body.appendChild(tooltip);
    
    const rect = e.target.getBoundingClientRect();
    tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
    tooltip.style.top = rect.top - tooltip.offsetHeight - 10 + 'px';
}

function hideTooltip() {
    const tooltip = document.querySelector('.tooltip');
    if (tooltip) {
        tooltip.remove();
    }
}

// Function to import grounds data from external source
function importGroundsData(newGroundsData) {
    try {
        let groundsToImport = [];
        
        // Handle different data formats
        if (Array.isArray(newGroundsData)) {
            groundsToImport = newGroundsData;
        } else if (newGroundsData && Array.isArray(newGroundsData.grounds)) {
            groundsToImport = newGroundsData.grounds;
        } else {
            throw new Error('Invalid data format');
        }
        
        // Validate each ground object
        const validatedGrounds = groundsToImport.map(ground => {
            return {
                ...ground,
                id: ground.id || generateGroundId(),
                sports: ground.sports || [ground.type || 'Sports'],
                pricePerHour: ground.pricePerHour || ground.price || 0,
                price: ground.price || ground.pricePerHour || 0,
                status: ground.status || 'active',
                amenities: ground.amenities || [],
                availableSlots: ground.availableSlots || ["9:00 AM", "11:00 AM", "2:00 PM", "4:00 PM", "6:00 PM"],
                rating: ground.rating || 0,
                createdAt: ground.createdAt || new Date().toISOString(),
                updatedAt: new Date().toISOString()
            };
        });
        
        // Save imported data
        saveGroundsToStorage(validatedGrounds);
        loadGroundsFromStorage();
        
        showNotification(`Successfully imported ${validatedGrounds.length} sports grounds!`, 'success');
        return true;
        
    } catch (error) {
        console.error('Error importing grounds data:', error);
        showNotification('Error importing grounds data. Please check the format.', 'error');
        return false;
    }
}

// Function to merge new data with existing data
function mergeGroundsData(newGroundsData, overwriteExisting = false) {
    try {
        const existingGrounds = getGroundsFromStorage();
        let groundsToMerge = Array.isArray(newGroundsData) ? newGroundsData : newGroundsData.grounds;
        
        if (overwriteExisting) {
            // Replace all data
            return importGroundsData(newGroundsData);
        } else {
            // Merge with existing data
            const existingIds = new Set(existingGrounds.map(g => g.id));
            const newGrounds = groundsToMerge.filter(g => !existingIds.has(g.id));
            
            const mergedGrounds = [...existingGrounds, ...newGrounds.map(ground => ({
                ...ground,
                id: ground.id || generateGroundId(),
                sports: ground.sports || [ground.type || 'Sports'],
                pricePerHour: ground.pricePerHour || ground.price || 0,
                price: ground.price || ground.pricePerHour || 0,
                status: ground.status || 'active',
                amenities: ground.amenities || [],
                availableSlots: ground.availableSlots || ["9:00 AM", "11:00 AM", "2:00 PM", "4:00 PM", "6:00 PM"],
                rating: ground.rating || 0,
                createdAt: ground.createdAt || new Date().toISOString(),
                updatedAt: new Date().toISOString()
            }))];
            
            saveGroundsToStorage(mergedGrounds);
            loadGroundsFromStorage();
            
            showNotification(`Merged ${newGrounds.length} new sports grounds with existing data!`, 'success');
            return true;
        }
    } catch (error) {
        console.error('Error merging grounds data:', error);
        showNotification('Error merging grounds data.', 'error');
        return false;
    }
}

// Utility functions for external use
window.sportsGroundsManager = {
    exportData: exportGroundsJson,
    clearAllData: clearAllGrounds,
    getGrounds: getGroundsFromStorage,
    saveGrounds: saveGroundsToStorage,
    importData: importGroundsData,
    mergeData: mergeGroundsData,
    refreshDisplay: loadGroundsFromStorage
};

// Initialize on load
window.addEventListener('load', () => {
    initTooltips();
    syncWithExternalUpdates();
    console.log('Sports Grounds Management System initialized');
    console.log('Available commands:', Object.keys(window.sportsGroundsManager));
    console.log('Use sportsGroundsManager.exportData() to export, sportsGroundsManager.clearAllData() to clear all');
});