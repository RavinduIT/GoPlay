// Shop Owner Products Management JavaScript

let currentEditingRow = null;
let currentDeletingRow = null;

document.addEventListener('DOMContentLoaded', function() {
    initializeProductsPage();
    setupActionButtons();
});

function initializeProductsPage() {
    setupSearchFilter();
    setupCheckboxes();
    setupFilters();
    setupFormSubmissions();
}

// ============================================
// SEARCH AND FILTER FUNCTIONALITY
// ============================================

function setupSearchFilter() {
    const searchInput = document.getElementById('productSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            filterProducts(searchTerm);
        });
    }
}

function filterProducts(searchTerm) {
    const rows = document.querySelectorAll('.product-row');

    rows.forEach(row => {
        const productName = row.querySelector('.product-details h4').textContent.toLowerCase();
        const productId = row.querySelector('.product-id').textContent.toLowerCase();
        const brand = row.querySelector('.brand-tag').textContent.toLowerCase();

        const matches = productName.includes(searchTerm) ||
                       productId.includes(searchTerm) ||
                       brand.includes(searchTerm);

        row.style.display = matches ? '' : 'none';
    });
}

function setupCheckboxes() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const productCheckboxes = document.querySelectorAll('.product-checkbox');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            productCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }

    productCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = Array.from(productCheckboxes).every(cb => cb.checked);
            const someChecked = Array.from(productCheckboxes).some(cb => cb.checked);

            if (selectAllCheckbox) {
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = someChecked && !allChecked;
            }
        });
    });
}

function setupFilters() {
    const categoryFilter = document.getElementById('categoryFilter');
    const stockFilter = document.getElementById('stockFilter');
    const sortFilter = document.getElementById('sortFilter');

    if (categoryFilter) {
        categoryFilter.addEventListener('change', applyFilters);
    }

    if (stockFilter) {
        stockFilter.addEventListener('change', applyFilters);
    }

    if (sortFilter) {
        sortFilter.addEventListener('change', applySorting);
    }
}

function applyFilters() {
    const categoryFilter = document.getElementById('categoryFilter').value;
    const stockFilter = document.getElementById('stockFilter').value;
    const rows = document.querySelectorAll('.product-row');

    rows.forEach(row => {
        let showRow = true;

        // Category filter
        if (categoryFilter) {
            const category = row.querySelector('.category-badge').textContent.toLowerCase();
            if (!category.includes(categoryFilter)) {
                showRow = false;
            }
        }

        // Stock filter
        if (stockFilter) {
            const stockIndicator = row.querySelector('.stock-indicator');
            if (stockFilter === 'in-stock' && !stockIndicator.classList.contains('in-stock')) {
                showRow = false;
            } else if (stockFilter === 'low-stock' && !stockIndicator.classList.contains('low-stock')) {
                showRow = false;
            } else if (stockFilter === 'out-of-stock' && !stockIndicator.classList.contains('out-of-stock')) {
                showRow = false;
            }
        }

        row.style.display = showRow ? '' : 'none';
    });
}

function applySorting() {
    const sortValue = document.getElementById('sortFilter').value;
    const tbody = document.getElementById('productsTableBody');
    const rows = Array.from(tbody.querySelectorAll('.product-row'));

    rows.sort((a, b) => {
        switch(sortValue) {
            case 'name-asc':
                return a.querySelector('.product-details h4').textContent.localeCompare(
                    b.querySelector('.product-details h4').textContent
                );
            case 'name-desc':
                return b.querySelector('.product-details h4').textContent.localeCompare(
                    a.querySelector('.product-details h4').textContent
                );
            case 'price-low':
                return getPriceValue(a) - getPriceValue(b);
            case 'price-high':
                return getPriceValue(b) - getPriceValue(a);
            default:
                return 0;
        }
    });

    rows.forEach(row => tbody.appendChild(row));
}

function getPriceValue(row) {
    const priceText = row.querySelector('td:nth-child(5)').textContent;
    return parseFloat(priceText.replace(/[^0-9.-]+/g, ''));
}

function clearFilters() {
    document.getElementById('productSearch').value = '';
    document.getElementById('categoryFilter').value = '';
    document.getElementById('stockFilter').value = '';
    document.getElementById('sortFilter').value = 'newest';

    const rows = document.querySelectorAll('.product-row');
    rows.forEach(row => row.style.display = '');
}

// ============================================
// MODAL MANAGEMENT
// ============================================

function openAddProductModal() {
    const modal = document.getElementById('addProductModal');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';

    // Reset form
    document.getElementById('addProductForm').reset();
}

function closeAddProductModal() {
    const modal = document.getElementById('addProductModal');
    modal.classList.remove('active');
    document.body.style.overflow = '';
}

// Removed old openEditProductModal function - now using editProduct(productId) instead

function closeEditProductModal() {
    const modal = document.getElementById('editProductModal');
    modal.classList.remove('active');
    document.body.style.overflow = '';
    currentEditingRow = null;
}

function openDeleteProductModal(button) {
    const row = button.closest('.product-row');
    currentDeletingRow = row;

    const productName = row.querySelector('.product-details h4').textContent;
    document.getElementById('deleteProductName').textContent = productName;

    const modal = document.getElementById('deleteProductModal');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeDeleteProductModal() {
    const modal = document.getElementById('deleteProductModal');
    modal.classList.remove('active');
    document.body.style.overflow = '';
    currentDeletingRow = null;
}

// ============================================
// FORM SUBMISSIONS
// ============================================

function setupFormSubmissions() {
    // Forms will submit normally to the server
    // No preventDefault needed since we're using traditional form submission
    // Just add validation if needed
    
    const addForm = document.getElementById('addProductForm');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            // Optional: Add client-side validation here
            const name = document.getElementById('productName');
            const category = document.getElementById('category');
            const brand = document.getElementById('brand');
            const price = document.getElementById('price');
            const stock = document.getElementById('stock');
            
            if (!name.value.trim()) {
                alert('Please enter product name');
                e.preventDefault();
                return false;
            }
            
            // Let the form submit normally to /shop-owner/products/create
        });
    }

    const editForm = document.getElementById('editProductForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            // Form submits normally to /shop-owner/products/update
        });
    }
}

// Product forms are now handled by server-side POST requests
// No client-side manipulation needed

// Delete is now handled by server-side POST request via deleteProduct() function


// ============================================
// ACTION BUTTONS SETUP
// ============================================

function setupActionButtons() {
    // Edit buttons
    document.querySelectorAll('.btn-action.edit').forEach(button => {
        button.addEventListener('click', function() {
            openEditProductModal(this);
        });
    });

    // Delete buttons
    document.querySelectorAll('.btn-action.delete').forEach(button => {
        button.addEventListener('click', function() {
            openDeleteProductModal(this);
        });
    });
}

// ============================================
// UTILITY FUNCTIONS
// ============================================

function updateProductStats(productChange, stockChange) {
    // Update total products
    const totalProductsCard = document.querySelector('.stat-card.blue .stat-value');
    if (totalProductsCard) {
        const currentTotal = parseInt(totalProductsCard.textContent);
        totalProductsCard.textContent = currentTotal + productChange;
    }

    // Update in stock count
    const inStockCard = document.querySelector('.stat-card.green .stat-value');
    if (inStockCard && stockChange > 0) {
        const currentInStock = parseInt(inStockCard.textContent);
        inStockCard.textContent = currentInStock + productChange;
    }
}

function showSuccessMessage(message) {
    const successDiv = document.createElement('div');
    successDiv.className = 'success-message';
    successDiv.innerHTML = `
        <i class="fas fa-check-circle"></i>
        <span>${message}</span>
    `;

    document.body.appendChild(successDiv);

    setTimeout(() => {
        successDiv.remove();
    }, 3000);
}

function toggleSidebar() {
    const sidebar = document.getElementById('dashboardSidebar');
    if (sidebar) {
        sidebar.classList.toggle('collapsed');
    }
}

// ============================================
// IMAGE PREVIEW FUNCTIONALITY
// ============================================

function previewImages(input, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    container.innerHTML = '';
    
    if (input.files && input.files.length > 0) {
        // Limit to 5 images
        const maxFiles = Math.min(input.files.length, 5);
        
        for (let i = 0; i < maxFiles; i++) {
            const file = input.files[i];
            
            // Validate file type
            if (!file.type.match('image.*')) {
                continue;
            }
            
            // Validate file size (5MB max)
            if (file.size > 5 * 1024 * 1024) {
                alert(`${file.name} is too large. Max size is 5MB.`);
                continue;
            }
            
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const previewDiv = document.createElement('div');
                previewDiv.className = 'image-preview-item';
                previewDiv.innerHTML = `
                    <img src="${e.target.result}" alt="Preview">
                    <div class="image-preview-overlay">
                        <span class="image-name">${file.name}</span>
                        <span class="image-size">${formatFileSize(file.size)}</span>
                    </div>
                `;
                container.appendChild(previewDiv);
            };
            
            reader.readAsDataURL(file);
        }
    }
}

function formatFileSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
}

// ============================================
// EDIT PRODUCT FUNCTIONALITY
// ============================================

function editProduct(productId) {
    // Extract product data from the table row using data attributes
    const row = document.querySelector(`tr[data-product-id="${productId}"]`);
    if (!row) {
        console.error('Product row not found for ID:', productId);
        alert('Error: Product not found. Please refresh the page.');
        return;
    }
    
    // Get data from attributes
    const productName = row.dataset.productName || '';
    const productSku = row.dataset.productSku || '';
    const categoryId = row.dataset.productCategoryId || '';
    const brand = row.dataset.productBrand || '';
    const price = row.dataset.productPrice || '0';
    const stock = row.dataset.productStock || '0';
    const status = row.dataset.productStatus || 'active';
    const description = row.dataset.productDescription || '';
    const availableSizes = row.dataset.productSizes || '';
    const availableColors = row.dataset.productColors || '';
    
    // Populate edit form
    document.getElementById('editProductId').value = productId;
    document.getElementById('editProductName').value = productName;
    document.getElementById('editProductSku').value = productSku;
    document.getElementById('editBrand').value = brand;
    document.getElementById('editPrice').value = price;
    document.getElementById('editStock').value = stock;
    document.getElementById('editStatus').value = status;
    document.getElementById('editDescription').value = description;
    document.getElementById('editProductSizes').value = availableSizes;
    document.getElementById('editProductColors').value = availableColors;
    
    // Select category by ID
    const categorySelect = document.getElementById('editCategory');
    if (categoryId) {
        categorySelect.value = categoryId;
    }
    
    // Debug: Log to verify product ID is set
    console.log('Editing product ID:', productId);
    console.log('Product ID field value:', document.getElementById('editProductId').value);
    console.log('Product data:', { productName, productSku, categoryId, brand, price, stock, status });
    
    // Clear image preview
    document.getElementById('imagePreviewEdit').innerHTML = '';
    
    // Open edit modal
    const modal = document.getElementById('editProductModal');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

// ============================================
// DELETE PRODUCT FUNCTIONALITY
// ============================================

let productToDelete = null;

function deleteProduct(productId, productName) {
    productToDelete = productId;
    document.getElementById('deleteProductId').value = productId;
    document.getElementById('deleteProductName').textContent = productName;
    openDeleteProductModal();
}

function confirmDeleteProduct() {
    if (productToDelete) {
        document.getElementById('deleteProductForm').submit();
    }
}

// ============================================
// AUTO-HIDE ALERTS
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.3s ease';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});

// Export for use in HTML
if (typeof window !== 'undefined') {
    window.clearFilters = clearFilters;
    window.openAddProductModal = openAddProductModal;
    window.closeAddProductModal = closeAddProductModal;
    window.closeEditProductModal = closeEditProductModal;
    window.closeDeleteProductModal = closeDeleteProductModal;
    window.editProduct = editProduct;
    window.deleteProduct = deleteProduct;
    window.previewImages = previewImages;
}
