// Dashboard functionality
document.addEventListener('DOMContentLoaded', function() {
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
    const editButtons = document.querySelectorAll('.btn-icon:not(.delete)');
    const deleteButtons = document.querySelectorAll('.btn-icon.delete');

    editButtons.forEach(button => {
        button.addEventListener('click', handleEditGround);
    });

    deleteButtons.forEach(button => {
        button.addEventListener('click', handleDeleteGround);
    });

    // Animate stats on page load
    animateStats();

    // Animate popularity bars
    animatePopularityBars();
});

// Handle add ground form submission
function handleAddGround(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const groundData = {
        name: e.target.querySelector('input[type="text"]').value,
        type: e.target.querySelector('select').value,
        location: e.target.querySelectorAll('input[type="text"]')[1].value,
        price: e.target.querySelector('input[type="number"]').value,
        description: e.target.querySelector('textarea').value
    };

    // Validate form data
    if (!groundData.name || !groundData.type || !groundData.location || !groundData.price) {
        showNotification('Please fill in all required fields', 'error');
        return;
    }

    // Simulate API call
    showNotification('Adding ground...', 'info');
    
    setTimeout(() => {
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
    const groundCard = createGroundCard(groundData);
    groundsList.appendChild(groundCard);
}

// Create ground card element
function createGroundCard(groundData) {
    const card = document.createElement('div');
    card.className = 'ground-card';
    
    const sportIcon = getSportIcon(groundData.type);
    
    card.innerHTML = `
        <div class="ground-image">
            <div class="ground-placeholder">${sportIcon}</div>
        </div>
        <div class="ground-info">
            <h3>${groundData.name}</h3>
            <p>${groundData.type} • ${groundData.location}</p>
            <div class="price">$${groundData.price}/hour</div>
            <div class="status active">active</div>
            <div class="sports-tags">
                <span class="tag">${groundData.type}</span>
            </div>
        </div>
        <div class="ground-actions">
            <button class="btn-icon">✏️</button>
            <button class="btn-icon delete">🗑️</button>
        </div>
    `;

    // Add event listeners to new buttons
    card.querySelector('.btn-icon:not(.delete)').addEventListener('click', handleEditGround);
    card.querySelector('.btn-icon.delete').addEventListener('click', handleDeleteGround);

    return card;
}

// Get sport icon based on type
function getSportIcon(type) {
    const icons = {
        'Football': '⚽',
        'Basketball': '🏀',
        'Tennis': '🎾',
        'Multi-Sport': '⚽'
    };
    return icons[type] || '🏟️';
}

// Handle edit ground
function handleEditGround(e) {
    const groundCard = e.target.closest('.ground-card');
    const groundName = groundCard.querySelector('h3').textContent;
    
    showNotification(`Editing ${groundName}...`, 'info');
    
    // Here you would typically open an edit modal or redirect to edit page
    setTimeout(() => {
        showNotification('Edit functionality would open here', 'success');
    }, 1000);
}

// Handle delete ground
function handleDeleteGround(e) {
    const groundCard = e.target.closest('.ground-card');
    const groundName = groundCard.querySelector('h3').textContent;
    
    if (confirm(`Are you sure you want to delete ${groundName}?`)) {
        showNotification('Deleting ground...', 'info');
        
        // Animate out
        groundCard.style.transform = 'translateX(-100%)';
        groundCard.style.opacity = '0';
        
        setTimeout(() => {
            groundCard.remove();
            showNotification('Ground deleted successfully!', 'success');
            updateStats();
        }, 300);
    }
}

// Update dashboard stats
function updateStats() {
    const totalGrounds = document.querySelectorAll('.ground-card').length;
    const statsValue = document.querySelector('.stat-value');
    
    if (statsValue) {
        animateNumber(statsValue, parseInt(statsValue.textContent), totalGrounds);
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
    }, 3000);
}

// Handle responsive navigation
function toggleMobileNav() {
    const nav = document.querySelector('.nav');
    nav.classList.toggle('mobile-open');
}

// Add scroll effect to header
window.addEventListener('scroll', () => {
    const header = document.querySelector('.header');
    if (window.scrollY > 10) {
        header.style.boxShadow = '0 4px 20px rgba(0,0,0,0.1)';
    } else {
        header.style.boxShadow = '0 2px 4px rgba(0,0,0,0.1)';
    }
});

// Keyboard navigation for tabs
document.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
        const activeTab = document.querySelector('.tab-btn.active');
        const tabs = Array.from(document.querySelectorAll('.tab-btn'));
        const currentIndex = tabs.indexOf(activeTab);
        
        let nextIndex;
        if (e.key === 'ArrowLeft') {
            nextIndex = currentIndex > 0 ? currentIndex - 1 : tabs.length - 1;
        } else {
            nextIndex = currentIndex < tabs.length - 1 ? currentIndex + 1 : 0;
        }
        
        tabs[nextIndex].click();
        tabs[nextIndex].focus();
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
    if (field.type === 'number' && field.placeholder === '0.00') {
        if (value !== '' && (isNaN(value) || parseFloat(value) < 0)) {
            field.classList.add('field-invalid');
        } else if (value !== '') {
            field.classList.add('field-valid');
        }
    }
}

// Add CSS for field validation
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
    }
`;
document.head.appendChild(style);

// Initialize tooltips (simple implementation)
function initTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', showTooltip);
        element.addEventListener('mouseleave', hideTooltip);
    });
}

function showTooltip(e) {
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

// Initialize on load
window.addEventListener('load', () => {
    initTooltips();
});