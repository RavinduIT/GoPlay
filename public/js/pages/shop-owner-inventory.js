// Shop Owner Inventory Management JavaScript

// Modal functions for Add Stock
function openAddStockModal(productId, productName, currentStock) {
    document.getElementById('add_product_id').value = productId;
    document.getElementById('add_product_name').textContent = productName;
    document.getElementById('add_current_stock').textContent = currentStock + ' units';
    document.getElementById('add_quantity').value = '';
    document.getElementById('addStockModal').style.display = 'flex';
}

function closeAddStockModal() {
    document.getElementById('addStockModal').style.display = 'none';
}

// Modal functions for Remove Stock
function openRemoveStockModal(productId, productName, currentStock) {
    document.getElementById('remove_product_id').value = productId;
    document.getElementById('remove_product_name').textContent = productName;
    document.getElementById('remove_current_stock').textContent = currentStock + ' units';
    document.getElementById('remove_quantity').value = '';
    document.getElementById('remove_reason').value = '';
    document.getElementById('removeStockModal').style.display = 'flex';
}

function closeRemoveStockModal() {
    document.getElementById('removeStockModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const addModal = document.getElementById('addStockModal');
    const removeModal = document.getElementById('removeStockModal');
    
    if (event.target === addModal) {
        closeAddStockModal();
    }
    if (event.target === removeModal) {
        closeRemoveStockModal();
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeAddStockModal();
        closeRemoveStockModal();
    }
});

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('inventorySearch');
    
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const inventoryCards = document.querySelectorAll('.inventory-item-card');
            
            inventoryCards.forEach(card => {
                const productName = card.querySelector('.col-product').textContent.toLowerCase();
                const sku = card.querySelector('.col-sku').textContent.toLowerCase();
                
                const matches = productName.includes(searchTerm) || sku.includes(searchTerm);
                
                if (matches) {
                    card.style.display = 'grid';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.3s ease-out';
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });

    // Form validation for Add Stock
    const addStockForm = document.querySelector('#addStockModal form');
    if (addStockForm) {
        addStockForm.addEventListener('submit', function(e) {
            const quantity = parseInt(document.getElementById('add_quantity').value);
            
            if (isNaN(quantity) || quantity < 1) {
                e.preventDefault();
                alert('Please enter a valid quantity (minimum 1).');
                return false;
            }
        });
    }

    // Form validation for Remove Stock
    const removeStockForm = document.querySelector('#removeStockModal form');
    if (removeStockForm) {
        removeStockForm.addEventListener('submit', function(e) {
            const quantity = parseInt(document.getElementById('remove_quantity').value);
            const reason = document.getElementById('remove_reason').value;
            
            if (isNaN(quantity) || quantity < 1) {
                e.preventDefault();
                alert('Please enter a valid quantity (minimum 1).');
                return false;
            }
            
            if (!reason) {
                e.preventDefault();
                alert('Please select a reason for removing stock.');
                return false;
            }

            // Confirm before removing stock
            const productName = document.getElementById('remove_product_name').textContent;
            const confirmed = confirm(`Are you sure you want to remove ${quantity} units of "${productName}"?\nReason: ${reason}`);
            
            if (!confirmed) {
                e.preventDefault();
                return false;
            }
        });
    }
});

// Helper function to format numbers with commas
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// Stock status indicator
function getStockStatusColor(quantity, minLevel) {
    if (quantity === 0) return 'red';
    if (quantity <= minLevel) return 'orange';
    return 'green';
}

// Export functions to global scope
window.openAddStockModal = openAddStockModal;
window.closeAddStockModal = closeAddStockModal;
window.openRemoveStockModal = openRemoveStockModal;
window.closeRemoveStockModal = closeRemoveStockModal;
