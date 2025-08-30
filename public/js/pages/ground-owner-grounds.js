/**
 * Ground Owner Grounds Management JavaScript
 * Handles ground CRUD operations and UI interactions
 */

let currentGrounds = [];
let editingGroundId = null;

document.addEventListener('DOMContentLoaded', function() {
    initializeGroundsPage();
});

function initializeGroundsPage() {
    loadGrounds();
    setupEventListeners();
    loadSportsCategories();
}

function setupEventListeners() {
    // Search functionality
    const searchInput = document.getElementById('groundSearch');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(filterGrounds, 300));
    }
    
    // Filter dropdowns
    const sportFilter = document.getElementById('sportFilter');
    const statusFilter = document.getElementById('statusFilter');
    
    if (sportFilter) {
        sportFilter.addEventListener('change', filterGrounds);
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', filterGrounds);
    }
    
    // Ground form submission
    const groundForm = document.getElementById('groundForm');
    if (groundForm) {
        groundForm.addEventListener('submit', handleGroundSubmit);
    }
    
    // Modal close events
    const modalClose = document.querySelector('.modal-close');
    if (modalClose) {
        modalClose.addEventListener('click', closeGroundModal);
    }
    
    // Click outside modal to close
    const modal = document.getElementById('groundModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeGroundModal();
            }
        });
    }
}

async function loadGrounds() {
    try {
        showLoadingState();
        
        const response = await fetch('/api/ground-owner/grounds');
        const data = await response.json();
        
        if (data.success) {
            currentGrounds = data.grounds;
            displayGrounds(data.grounds);
            updateSidebarCounts(data.grounds);
        } else {
            showError('Failed to load grounds: ' + data.message);
        }
    } catch (error) {
        console.error('Error loading grounds:', error);
        showError('Failed to load grounds. Please try again.');
    }
}

async function loadSportsCategories() {
    try {
        const response = await fetch('/api/ground-owner/categories');
        const data = await response.json();
        
        if (data.success) {
            populateCategoryDropdowns(data.categories);
        }
    } catch (error) {
        console.error('Error loading categories:', error);
    }
}

function populateCategoryDropdowns(categories) {
    const sportFilter = document.getElementById('sportFilter');
    const sportCategory = document.getElementById('sportCategory');
    
    // Populate filter dropdown
    if (sportFilter) {
        categories.forEach(category => {
            const option = document.createElement('option');
            option.value = category.id;
            option.textContent = category.name;
            sportFilter.appendChild(option);
        });
    }
    
    // Populate modal dropdown
    if (sportCategory) {
        sportCategory.innerHTML = '<option value="">Select Sport</option>';
        categories.forEach(category => {
            const option = document.createElement('option');
            option.value = category.id;
            option.textContent = category.name;
            sportCategory.appendChild(option);
        });
    }
}

function displayGrounds(grounds) {
    const container = document.getElementById('groundsContainer');
    if (!container) return;
    
    if (grounds.length === 0) {
        container.innerHTML = `
            <div class="no-grounds">
                <div class="no-grounds-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <h3>No Grounds Yet</h3>
                <p>Start by adding your first sports ground to begin receiving bookings.</p>
                <button class="btn-primary" onclick="addNewGround()">
                    <i class="fas fa-plus"></i>
                    Add Your First Ground
                </button>
            </div>
        `;
        return;
    }
    
    const groundsHTML = grounds.map(ground => createGroundCard(ground)).join('');
    container.innerHTML = groundsHTML;
}

function createGroundCard(ground) {
    const amenitiesList = ground.amenities ? ground.amenities.slice(0, 3).join(', ') : '';
    const moreAmenities = ground.amenities && ground.amenities.length > 3 ? `+${ground.amenities.length - 3} more` : '';
    
    return `
        <div class="ground-card" data-ground-id="${ground.id}">
            <div class="ground-image">
                ${ground.images && ground.images.length > 0 
                    ? `<img src="/public/assets/images/grounds/${ground.images[0]}" alt="${ground.name}">` 
                    : `<div class="ground-placeholder"><i class="fas fa-image"></i></div>`
                }
                <div class="ground-status status-${ground.status}">
                    ${ground.status.charAt(0).toUpperCase() + ground.status.slice(1)}
                </div>
            </div>
            
            <div class="ground-info">
                <div class="ground-header">
                    <h3 class="ground-name">${ground.name}</h3>
                    <div class="ground-rating">
                        <i class="fas fa-star"></i>
                        <span>${ground.rating || '0.0'}</span>
                        <small>(${ground.total_reviews || 0})</small>
                    </div>
                </div>
                
                <div class="ground-meta">
                    <div class="ground-sport">
                        <i class="${ground.category_icon || 'fas fa-circle'}"></i>
                        <span>${ground.category_name}</span>
                    </div>
                    <div class="ground-location">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>${ground.city}</span>
                    </div>
                </div>
                
                <div class="ground-description">
                    ${ground.description ? ground.description.substring(0, 100) + '...' : 'No description available'}
                </div>
                
                <div class="ground-amenities">
                    <small>${amenitiesList} ${moreAmenities}</small>
                </div>
                
                <div class="ground-footer">
                    <div class="ground-rate">
                        <span class="rate-amount">LKR ${parseFloat(ground.hourly_rate).toLocaleString()}</span>
                        <small>/hour</small>
                    </div>
                    
                    <div class="ground-actions">
                        <button class="btn-action btn-edit" onclick="editGround(${ground.id})">
                            <i class="fas fa-edit"></i>
                            <span>Edit</span>
                        </button>
                        <button class="btn-action btn-view" onclick="viewGroundDetails(${ground.id})">
                            <i class="fas fa-eye"></i>
                            <span>View</span>
                        </button>
                        <button class="btn-action btn-delete" onclick="deleteGround(${ground.id})">
                            <i class="fas fa-trash"></i>
                            <span>Delete</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function filterGrounds() {
    const searchTerm = document.getElementById('groundSearch')?.value.toLowerCase() || '';
    const sportFilter = document.getElementById('sportFilter')?.value || '';
    const statusFilter = document.getElementById('statusFilter')?.value || '';
    
    const filteredGrounds = currentGrounds.filter(ground => {
        const matchesSearch = !searchTerm || 
            ground.name.toLowerCase().includes(searchTerm) ||
            ground.city.toLowerCase().includes(searchTerm) ||
            (ground.description && ground.description.toLowerCase().includes(searchTerm));
            
        const matchesSport = !sportFilter || ground.sport_category_id == sportFilter;
        const matchesStatus = !statusFilter || ground.status === statusFilter;
        
        return matchesSearch && matchesSport && matchesStatus;
    });
    
    displayGrounds(filteredGrounds);
}

function addNewGround() {
    editingGroundId = null;
    document.getElementById('modalTitle').textContent = 'Add New Ground';
    document.getElementById('groundForm').reset();
    document.getElementById('groundId').value = '';
    
    // Clear all amenity checkboxes
    document.querySelectorAll('input[name="amenities[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    document.getElementById('groundModal').style.display = 'block';
}

async function editGround(groundId) {
    try {
        const response = await fetch(`/api/ground-owner/grounds/${groundId}`);
        const data = await response.json();
        
        if (data.success) {
            populateGroundForm(data.ground);
            editingGroundId = groundId;
            document.getElementById('modalTitle').textContent = 'Edit Ground';
            document.getElementById('groundModal').style.display = 'block';
        } else {
            showError('Failed to load ground details');
        }
    } catch (error) {
        console.error('Error loading ground:', error);
        showError('Failed to load ground details');
    }
}

function populateGroundForm(ground) {
    document.getElementById('groundId').value = ground.id;
    document.getElementById('groundName').value = ground.name;
    document.getElementById('sportCategory').value = ground.sport_category_id;
    document.getElementById('description').value = ground.description || '';
    document.getElementById('address').value = ground.address;
    document.getElementById('city').value = ground.city;
    document.getElementById('postalCode').value = ground.postal_code || '';
    document.getElementById('hourlyRate').value = ground.hourly_rate;
    document.getElementById('capacity').value = ground.capacity || '';
    document.getElementById('rules').value = ground.rules || '';
    
    // Set amenities checkboxes
    const amenities = ground.amenities || [];
    document.querySelectorAll('input[name="amenities[]"]').forEach(checkbox => {
        checkbox.checked = amenities.includes(checkbox.value);
    });
}

async function handleGroundSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const groundData = {};
    
    // Convert FormData to object
    for (let [key, value] of formData.entries()) {
        if (key === 'amenities[]') {
            if (!groundData.amenities) groundData.amenities = [];
            groundData.amenities.push(value);
        } else {
            groundData[key] = value;
        }
    }
    
    try {
        const isEditing = editingGroundId !== null;
        const url = isEditing ? `/api/ground-owner/grounds/${editingGroundId}` : '/api/ground-owner/grounds';
        const method = isEditing ? 'PUT' : 'POST';
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(groundData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            showSuccess(data.message);
            closeGroundModal();
            loadGrounds(); // Reload the grounds list
        } else {
            showError(data.message);
        }
    } catch (error) {
        console.error('Error saving ground:', error);
        showError('Failed to save ground. Please try again.');
    }
}

async function deleteGround(groundId) {
    if (!confirm('Are you sure you want to delete this ground? This action cannot be undone.')) {
        return;
    }
    
    try {
        const response = await fetch(`/api/ground-owner/grounds/${groundId}`, {
            method: 'DELETE'
        });
        
        const data = await response.json();
        
        if (data.success) {
            showSuccess('Ground deleted successfully');
            loadGrounds(); // Reload the grounds list
        } else {
            showError(data.message);
        }
    } catch (error) {
        console.error('Error deleting ground:', error);
        showError('Failed to delete ground. Please try again.');
    }
}

function viewGroundDetails(groundId) {
    // Open ground details in new tab or modal
    window.open(`/ground-details?id=${groundId}`, '_blank');
}

function closeGroundModal() {
    document.getElementById('groundModal').style.display = 'none';
    editingGroundId = null;
}

function showLoadingState() {
    const container = document.getElementById('groundsContainer');
    if (container) {
        container.innerHTML = `
            <div class="ground-card loading-placeholder">
                <div class="loading-shimmer"></div>
            </div>
            <div class="ground-card loading-placeholder">
                <div class="loading-shimmer"></div>
            </div>
            <div class="ground-card loading-placeholder">
                <div class="loading-shimmer"></div>
            </div>
        `;
    }
}

function updateSidebarCounts(grounds) {
    const groundsCount = document.getElementById('groundsCount');
    if (groundsCount) {
        groundsCount.textContent = grounds.length;
    }
}

function showError(message) {
    // Simple error display - you can enhance this with a proper notification system
    alert('Error: ' + message);
}

function showSuccess(message) {
    // Simple success display - you can enhance this with a proper notification system
    alert('Success: ' + message);
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Export functions for global use
window.addNewGround = addNewGround;
window.editGround = editGround;
window.deleteGround = deleteGround;
window.viewGroundDetails = viewGroundDetails;
window.closeGroundModal = closeGroundModal;