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

function openEditProductModal(button) {
    const row = button.closest('.product-row');
    currentEditingRow = row;

    // Extract data from row
    const productName = row.querySelector('.product-details h4').textContent;
    const productId = row.querySelector('.product-id').textContent;
    const category = row.querySelector('.category-badge').textContent.toLowerCase();
    const brand = row.querySelector('.brand-tag').textContent;
    const price = getPriceValue(row);
    const stockText = row.querySelector('.stock-count').textContent;
    const stock = parseInt(stockText.match(/\d+/)[0]);
    const status = row.querySelector('.status-badge').classList.contains('active') ? 'active' : 'inactive';

    // Populate form
    document.getElementById('editProductName').value = productName;
    document.getElementById('editProductId').value = productId;
    document.getElementById('editCategory').value = category;
    document.getElementById('editBrand').value = brand;
    document.getElementById('editPrice').value = price;
    document.getElementById('editStock').value = stock;
    document.getElementById('editStatus').value = status;

    // Show modal
    const modal = document.getElementById('editProductModal');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

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
    // Add Product Form
    const addForm = document.getElementById('addProductForm');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            e.preventDefault();
            addNewProduct();
        });
    }

    // Edit Product Form
    const editForm = document.getElementById('editProductForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            updateProduct();
        });
    }
}

function addNewProduct() {
    // Get form data
    const productName = document.getElementById('productName').value;
    const productId = document.getElementById('productId').value;
    const category = document.getElementById('category').value;
    const brand = document.getElementById('brand').value;
    const price = parseFloat(document.getElementById('price').value);
    const stock = parseInt(document.getElementById('stock').value);
    const status = document.getElementById('status').value;

    // Determine stock status
    let stockStatus = 'in-stock';
    if (stock === 0) {
        stockStatus = 'out-of-stock';
    } else if (stock < 10) {
        stockStatus = 'low-stock';
    }

    // Create new row HTML
    const newRow = `
        <tr class="product-row">
            <td>
                <input type="checkbox" class="product-checkbox">
            </td>
            <td>
                <div class="product-info">
                    <img src="/public/assets/images/products/default-product.jpg" alt="${productName}" class="product-image">
                    <div class="product-details">
                        <h4>${productName}</h4>
                        <span class="brand-tag">${brand}</span>
                    </div>
                </div>
            </td>
            <td><span class="product-id">${productId}</span></td>
            <td><span class="category-badge ${category}">${category.charAt(0).toUpperCase() + category.slice(1)}</span></td>
            <td><strong>LKR ${price.toLocaleString()}</strong></td>
            <td>
                <div class="stock-indicator ${stockStatus}">
                    <span class="stock-dot"></span>
                    <span class="stock-count">${stock} units</span>
                </div>
            </td>
            <td><span class="status-badge ${status}">${status.charAt(0).toUpperCase() + status.slice(1)}</span></td>
            <td>
                <div class="rating-stars">
                    <i class="fas fa-star"></i>
                    <span>0.0 (0)</span>
                </div>
            </td>
            <td>Just now</td>
            <td>
                <div class="action-buttons">
                    <button class="btn-action edit" title="Edit Product">
                        <i class="fas fa-edit"></i>
                        Edit
                    </button>
                    <button class="btn-action delete" title="Delete Product">
                        <i class="fas fa-trash-alt"></i>
                        Delete
                    </button>
                </div>
            </td>
        </tr>
    `;

    // Add to table
    const tbody = document.getElementById('productsTableBody');
    tbody.insertAdjacentHTML('afterbegin', newRow);

    // Update stats
    updateProductStats(1, stock);

    // Close modal and show success
    closeAddProductModal();
    showSuccessMessage('Product added successfully!');

    // Re-setup action buttons
    setupActionButtons();
}

function updateProduct() {
    if (!currentEditingRow) return;

    // Get form data
    const productName = document.getElementById('editProductName').value;
    const category = document.getElementById('editCategory').value;
    const brand = document.getElementById('editBrand').value;
    const price = parseFloat(document.getElementById('editPrice').value);
    const stock = parseInt(document.getElementById('editStock').value);
    const status = document.getElementById('editStatus').value;

    // Determine stock status
    let stockStatus = 'in-stock';
    if (stock === 0) {
        stockStatus = 'out-of-stock';
    } else if (stock < 10) {
        stockStatus = 'low-stock';
    }

    // Update row data
    currentEditingRow.querySelector('.product-details h4').textContent = productName;
    currentEditingRow.querySelector('.brand-tag').textContent = brand;
    currentEditingRow.querySelector('.category-badge').textContent = category.charAt(0).toUpperCase() + category.slice(1);
    currentEditingRow.querySelector('.category-badge').className = `category-badge ${category}`;
    currentEditingRow.querySelector('td:nth-child(5) strong').textContent = `LKR ${price.toLocaleString()}`;
    currentEditingRow.querySelector('.stock-count').textContent = `${stock} units`;
    currentEditingRow.querySelector('.stock-indicator').className = `stock-indicator ${stockStatus}`;
    currentEditingRow.querySelector('.status-badge').textContent = status.charAt(0).toUpperCase() + status.slice(1);
    currentEditingRow.querySelector('.status-badge').className = `status-badge ${status}`;
    currentEditingRow.querySelector('td:nth-child(9)').textContent = 'Just now';

    // Close modal and show success
    closeEditProductModal();
    showSuccessMessage('Product updated successfully!');
}

function confirmDeleteProduct() {
    if (!currentDeletingRow) return;

    // Get stock before deletion
    const stockText = currentDeletingRow.querySelector('.stock-count').textContent;
    const stock = parseInt(stockText.match(/\d+/)[0]);

    // Remove row
    currentDeletingRow.remove();

    // Update stats
    updateProductStats(-1, -stock);

    // Close modal and show success
    closeDeleteProductModal();
    showSuccessMessage('Product deleted successfully!');
}

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

// Export for use in HTML
if (typeof window !== 'undefined') {
    window.clearFilters = clearFilters;
    window.openAddProductModal = openAddProductModal;
    window.closeAddProductModal = closeAddProductModal;
    window.closeEditProductModal = closeEditProductModal;
    window.closeDeleteProductModal = closeDeleteProductModal;
    window.confirmDeleteProduct = confirmDeleteProduct;
    window.toggleSidebar = toggleSidebar;
}
