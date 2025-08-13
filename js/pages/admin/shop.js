// Dashboard JavaScript Functionality

document.addEventListener('DOMContentLoaded', async function() {
    // Initialize localStorage from JSON if needed
    await initializeProductsFromJson();
    
    // Load products from localStorage and display them
    loadProductsFromLocalStorage();
    
    // Initialize dashboard
    initializeDashboard();
});

// Initialize products from JSON file if localStorage is empty
async function initializeProductsFromJson() {
    try {
        const localProducts = localStorage.getItem('products');
        
        if (!localProducts) {
            console.log('No products in localStorage, loading from JSON...');
            
            // Fetch products from JSON file
            const response = await fetch('/data/products.json');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const jsonProducts = await response.json();
            
            // Transform JSON data to match shop system format
            const transformedProducts = jsonProducts.map(product => ({
                id: product.id,
                name: product.name,
                brand: product.brand,
                category: product.category,
                price: product.price,
                rating: product.rating,
                totalReviews: product.totalReviews,
                inStock: product.inStock,
                stock: product.stockQuantity || 0,
                description: product.description,
                features: extractProductFeatures(product),
                images: product.images || ['default_product.jpg'],
                status: product.status
            }));
            
            // Save to localStorage
            localStorage.setItem('products', JSON.stringify(transformedProducts));
            console.log('Products initialized from JSON:', transformedProducts.length, 'products');
        } else {
            console.log('Products already exist in localStorage');
        }
        
    } catch (error) {
        console.error('Error initializing products from JSON:', error);
        // Initialize with minimal fallback data if JSON loading fails
        initializeFallbackProducts();
    }
}

// Extract features from product data
function extractProductFeatures(product) {
    const features = [];
    
    if (product.tags && product.tags.length > 0) {
        // Capitalize first letter of each tag
        features.push(...product.tags.slice(0, 3).map(tag => 
            tag.charAt(0).toUpperCase() + tag.slice(1)
        ));
    }
    
    // Add additional features based on specifications
    if (product.specifications) {
        const specs = product.specifications;
        if (specs.weight) features.push(`Weight: ${specs.weight}`);
        if (specs.size) features.push(`Size: ${specs.size}`);
        if (specs.material) features.push(specs.material);
    }
    
    // Ensure we have at least some features
    if (features.length === 0) {
        features.push('High Quality', 'Durable', product.category);
    }
    
    return features.slice(0, 3); // Limit to 3 features for UI
}

// Fallback products if JSON loading fails
function initializeFallbackProducts() {
    const fallbackProducts = [
        {
            id: 1,
            name: "Professional Tennis Racket",
            brand: "Wilson",
            category: "Tennis",
            price: 199.99,
            rating: 4.8,
            totalReviews: 142,
            inStock: true,
            stock: 25,
            description: "High-performance tennis racket used by professional players",
            features: ["Professional", "Tournament", "Wilson"],
            images: ["tennis_racket_1.jpg"],
            status: "Active"
        }
    ];
    
    localStorage.setItem('products', JSON.stringify(fallbackProducts));
    console.log('Fallback products initialized');
}

// Load products from localStorage and display them
function loadProductsFromLocalStorage() {
    try {
        // Create ProductManager if not already available
        if (typeof window.ProductManager === 'undefined') {
            window.ProductManager = {
                getAllProducts: () => JSON.parse(localStorage.getItem('products') || '[]'),
                addProduct: (newProduct) => {
                    const products = JSON.parse(localStorage.getItem('products') || '[]');
                    if (!newProduct.id) {
                        const maxId = products.reduce((max, product) => Math.max(max, product.id || 0), 0);
                        newProduct.id = maxId + 1;
                    }
                    products.push(newProduct);
                    localStorage.setItem('products', JSON.stringify(products));
                    return newProduct;
                },
                updateProduct: (updatedProduct) => {
                    const products = JSON.parse(localStorage.getItem('products') || '[]');
                    const index = products.findIndex(product => product.id === updatedProduct.id);
                    if (index !== -1) {
                        products[index] = updatedProduct;
                        localStorage.setItem('products', JSON.stringify(products));
                        return true;
                    }
                    return false;
                },
                removeProduct: (productId) => {
                    const products = JSON.parse(localStorage.getItem('products') || '[]');
                    const filteredProducts = products.filter(product => product.id !== productId);
                    localStorage.setItem('products', JSON.stringify(filteredProducts));
                    return true;
                }
            };
        }

        // Get products from localStorage
        const products = window.ProductManager.getAllProducts();
        
        // Clear existing product list and display real data
        displayProducts(products);
        
        // Update stats based on real data
        updateStatsFromProductData(products);
        
    } catch (error) {
        console.error('Error loading products from localStorage:', error);
    }
}

// Display products in the admin dashboard
function displayProducts(products) {
    const productList = document.getElementById('product-list');
    if (!productList) return;
    
    // Clear existing hardcoded products
    productList.innerHTML = '';
    
    // Display each product from localStorage
    products.forEach(product => {
        const productItem = createProductItemFromData(product);
        productList.appendChild(productItem);
    });
}

// Create product item from localStorage data
function createProductItemFromData(product) {
    const productItem = document.createElement('div');
    productItem.className = 'product-item';
    productItem.dataset.productId = product.id; // Store product ID for edit/delete operations
    
    const stockClass = getStockClass(product.stock || 0);
    
    productItem.innerHTML = `
        <div class="product-info">
            <h3>${product.name || 'Unknown Product'}</h3>
            <p class="product-meta">${product.brand || 'Unknown'} • ${product.category || 'General'}</p>
            <div class="product-details">
                <span class="price">Rs.${(product.price || 0).toFixed(2)}</span>
                <span class="stock-badge ${stockClass}">Stock: ${product.stock || 0}</span>
            </div>
        </div>
        <div class="product-actions">
            <button class="edit-btn" data-product-id="${product.id}"><i class="fas fa-edit"></i></button>
            <button class="delete-btn" data-product-id="${product.id}"><i class="fas fa-trash"></i></button>
        </div>
    `;
    
    return productItem;
}

// Update stats based on real product data
function updateStatsFromProductData(products) {
    const activeProducts = products.filter(product => product.status === 'Active');
    const totalProducts = activeProducts.length;
    
    // Calculate total revenue (estimate based on product prices)
    const totalRevenue = activeProducts.reduce((sum, product) => sum + (product.price || 0), 0);
    
    // Update the stats display
    const statElements = {
        products: document.getElementById('total-products'),
        revenue: document.getElementById('total-revenue')
    };
    
    if (statElements.products) {
        statElements.products.textContent = totalProducts;
    }
    
    if (statElements.revenue) {
        statElements.revenue.textContent = `Rs.${totalRevenue.toFixed(2)}`;
    }
}

function initializeDashboard() {
    // Initialize tab switching
    initializeTabSwitching();
    
    // Initialize product form
    initializeProductForm();
    
    // Initialize product actions
    initializeProductActions();
    
    // Update stats periodically
    updateStats();
    
    // Initialize search
    initializeSearch();
    
    // Initialize analytics
    initializeAnalytics();
}

// Tab Switching Functionality
function initializeTabSwitching() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const targetTab = button.dataset.tab;
            
            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked button and corresponding content
            button.classList.add('active');
            document.getElementById(`${targetTab}-tab`).classList.add('active');
        });
    });
}

// Product Form Functionality
function initializeProductForm() {
    const productForm = document.getElementById('product-form');
    
    productForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const productData = {
            name: document.getElementById('product-name').value,
            category: document.getElementById('category').value,
            brand: document.getElementById('brand').value,
            price: parseFloat(document.getElementById('price').value),
            stock: parseInt(document.getElementById('stock').value)
        };
        
        // Validate form data
        if (validateProductData(productData)) {
            addNewProduct(productData);
            resetForm();
        }
    });
}

// Validate product data
function validateProductData(data) {
    return data.name && 
           data.category && 
           data.brand && 
           data.price > 0 && 
           data.stock >= 0;
}

// Add new product to inventory and localStorage
function addNewProduct(productData) {
    try {
        // Create product object in the format expected by the shop system
        const newProduct = {
            name: productData.name || 'New Product',
            brand: productData.brand || 'Unknown Brand',
            category: productData.category || 'General',
            price: parseFloat(productData.price) || 0,
            stock: parseInt(productData.stock) || 0,
            rating: 4.0, // Default rating for new products
            totalReviews: 0, // New product starts with 0 reviews
            inStock: productData.stock > 0,
            description: `High-quality ${productData.category || 'sports'} equipment from ${productData.brand || 'premium brand'}.`,
            features: [`${productData.category || 'Sports'} Equipment`, 'High Quality', 'Durable'],
            images: ['default_product.jpg'],
            status: 'Active'
        };
        
        // Add to localStorage using ProductManager
        const addedProduct = window.ProductManager.addProduct(newProduct);
        
        // Refresh the product list display
        loadProductsFromLocalStorage();
        
        console.log('Product added successfully:', addedProduct);
        
    } catch (error) {
        console.error('Error adding new product:', error);
        alert('Error adding product. Please try again.');
    }
}

// Create product item HTML
function createProductItem(data) {
    const productItem = document.createElement('div');
    productItem.className = 'product-item';
    
    const stockClass = getStockClass(data.stock);
    
    productItem.innerHTML = `
        <div class="product-info">
            <h3>${data.name}</h3>
            <p class="product-meta">${data.brand} • ${data.category}</p>
            <div class="product-details">
                <span class="price">$${data.price.toFixed(2)}</span>
                <span class="stock-badge ${stockClass}">Stock: ${data.stock}</span>
            </div>
        </div>
        <div class="product-actions">
            <button class="edit-btn"><i class="fas fa-edit"></i></button>
            <button class="delete-btn"><i class="fas fa-trash"></i></button>
        </div>
    `;
    
    // Add event listeners to action buttons
    const editBtn = productItem.querySelector('.edit-btn');
    const deleteBtn = productItem.querySelector('.delete-btn');
    
    editBtn.addEventListener('click', () => editProduct(productItem));
    deleteBtn.addEventListener('click', () => deleteProduct(productItem));
    
    return productItem;
}

// Get stock class based on quantity
function getStockClass(stock) {
    if (stock >= 25) return 'stock-high';
    if (stock >= 10) return 'stock-medium';
    return 'stock-low';
}

// Initialize product actions
function initializeProductActions() {
    const editButtons = document.querySelectorAll('.edit-btn');
    const deleteButtons = document.querySelectorAll('.delete-btn');
    
    editButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            const productItem = e.target.closest('.product-item');
            editProduct(productItem);
        });
    });
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            const productItem = e.target.closest('.product-item');
            deleteProduct(productItem);
        });
    });
}

// Edit product functionality
function editProduct(productItem) {
    const productId = parseInt(productItem.dataset.productId);
    const products = window.ProductManager.getAllProducts();
    const product = products.find(p => p.id === productId);
    
    if (!product) {
        alert('Product not found!');
        return;
    }
    
    // Create a simple edit form
    const editForm = createProductEditForm(product);
    
    // Show the edit form in a modal-like overlay
    showProductEditModal(editForm, product);
}

// Create edit form for product
function createProductEditForm(product) {
    return `
        <div class="edit-modal-overlay" id="editProductModalOverlay">
            <div class="edit-modal">
                <h3>Edit Product</h3>
                <form id="editProductForm">
                    <div class="form-group">
                        <label>Product Name</label>
                        <input type="text" id="edit-product-name" value="${product.name}" required>
                    </div>
                    <div class="form-group">
                        <label>Brand</label>
                        <input type="text" id="edit-brand" value="${product.brand}" required>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select id="edit-category" required>
                            <option value="Basketball" ${product.category === 'Basketball' ? 'selected' : ''}>Basketball</option>
                            <option value="Tennis" ${product.category === 'Tennis' ? 'selected' : ''}>Tennis</option>
                            <option value="Football" ${product.category === 'Football' ? 'selected' : ''}>Football</option>
                            <option value="Soccer" ${product.category === 'Soccer' ? 'selected' : ''}>Soccer</option>
                            <option value="Cricket" ${product.category === 'Cricket' ? 'selected' : ''}>Cricket</option>
                            <option value="Swimming" ${product.category === 'Swimming' ? 'selected' : ''}>Swimming</option>
                            <option value="Badminton" ${product.category === 'Badminton' ? 'selected' : ''}>Badminton</option>
                            <option value="Volleyball" ${product.category === 'Volleyball' ? 'selected' : ''}>Volleyball</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Price (Rs.)</label>
                        <input type="number" id="edit-price" value="${product.price}" required min="0" step="0.01">
                    </div>
                    <div class="form-group">
                        <label>Stock</label>
                        <input type="number" id="edit-stock" value="${product.stock || 0}" required min="0">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea id="edit-description" rows="3">${product.description || ''}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select id="edit-status">
                            <option value="Active" ${product.status === 'Active' ? 'selected' : ''}>Active</option>
                            <option value="Inactive" ${product.status === 'Inactive' ? 'selected' : ''}>Inactive</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="button" onclick="closeProductEditModal()" class="btn-secondary">Cancel</button>
                        <button type="submit" class="btn-primary">Update Product</button>
                    </div>
                </form>
            </div>
        </div>
    `;
}

// Show product edit modal
function showProductEditModal(formHTML, product) {
    // Remove existing modal if any
    const existingModal = document.getElementById('editProductModalOverlay');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', formHTML);
    
    // Add event listener for form submission
    const form = document.getElementById('editProductForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        saveProductEdit(product.id);
    });
    
    // Add CSS for modal if not already added
    if (!document.getElementById('editProductModalStyles')) {
        const styles = document.createElement('style');
        styles.id = 'editProductModalStyles';
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

// Close product edit modal
function closeProductEditModal() {
    const modal = document.getElementById('editProductModalOverlay');
    if (modal) {
        modal.remove();
    }
}

// Save product edit
function saveProductEdit(productId) {
    try {
        const updatedProduct = {
            id: productId,
            name: document.getElementById('edit-product-name').value,
            brand: document.getElementById('edit-brand').value,
            category: document.getElementById('edit-category').value,
            price: parseFloat(document.getElementById('edit-price').value),
            stock: parseInt(document.getElementById('edit-stock').value),
            description: document.getElementById('edit-description').value,
            status: document.getElementById('edit-status').value
        };
        
        // Get existing product data to preserve other fields
        const products = window.ProductManager.getAllProducts();
        const existingProduct = products.find(p => p.id === productId);
        
        if (existingProduct) {
            // Merge with existing data and update inStock based on stock
            const finalProduct = { 
                ...existingProduct, 
                ...updatedProduct,
                inStock: updatedProduct.stock > 0
            };
            
            // Update in localStorage
            const success = window.ProductManager.updateProduct(finalProduct);
            
            if (success) {
                // Refresh the display
                loadProductsFromLocalStorage();
                closeProductEditModal();
                
                // Show success message
                alert('Product updated successfully!');
            } else {
                alert('Failed to update product. Please try again.');
            }
        }
        
    } catch (error) {
        console.error('Error updating product:', error);
        alert('Error updating product. Please try again.');
    }
}

// Delete product functionality
function deleteProduct(productItem) {
    const productId = parseInt(productItem.dataset.productId);
    const products = window.ProductManager.getAllProducts();
    const product = products.find(p => p.id === productId);
    
    if (!product) {
        alert('Product not found!');
        return;
    }
    
    if (confirm(`Are you sure you want to delete ${product.name}?`)) {
        try {
            // Remove from localStorage
            window.ProductManager.removeProduct(productId);
            
            // Refresh the display
            loadProductsFromLocalStorage();
            
            console.log('Product deleted successfully:', product.name);
        } catch (error) {
            console.error('Error deleting product:', error);
            alert('Error deleting product. Please try again.');
        }
    }
}

// Reset form after submission
function resetForm() {
    document.getElementById('product-form').reset();
}

// Update total products count
function updateTotalProducts() {
    const totalProducts = document.querySelectorAll('.product-item').length;
    document.getElementById('total-products').textContent = totalProducts;
}

// Update dashboard stats
function updateStats() {
    // Simulate real-time data updates
    const stats = {
        revenue: generateRandomRevenue(),
        ordersToday: generateRandomOrders(),
        activeCustomers: generateRandomCustomers()
    };
    
    // Update stats with animation
    animateStatUpdate('total-revenue', `$${stats.revenue.toLocaleString()}`);
    animateStatUpdate('orders-today', stats.ordersToday);
    animateStatUpdate('active-customers', stats.activeCustomers.toLocaleString());
}

// Generate random revenue for demo
function generateRandomRevenue() {
    return Math.floor(Math.random() * 5000) + 10000;
}

// Generate random orders for demo
function generateRandomOrders() {
    return Math.floor(Math.random() * 20) + 15;
}

// Generate random customers for demo
function generateRandomCustomers() {
    return Math.floor(Math.random() * 500) + 1000;
}

// Animate stat updates
function animateStatUpdate(elementId, newValue) {
    const element = document.getElementById(elementId);
    
    // Add animation class
    element.style.transform = 'scale(1.05)';
    element.style.transition = 'transform 0.3s ease';
    
    // Update value after brief delay
    setTimeout(() => {
        element.textContent = newValue;
        element.style.transform = 'scale(1)';
    }, 150);
}

// Search functionality
function initializeSearch() {
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.placeholder = 'Search products...';
    searchInput.className = 'search-input';
    searchInput.style.cssText = `
        width: 100%;
        padding: 12px 16px;
        margin-bottom: 20px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.875rem;
    `;
    
    const inventorySection = document.querySelector('.inventory-section');
    if (inventorySection) {
        const inventoryTitle = inventorySection.querySelector('h2');
        inventoryTitle.insertAdjacentElement('afterend', searchInput);
        
        searchInput.addEventListener('input', (e) => {
            filterProducts(e.target.value);
        });
    }
}

// Filter products based on search
function filterProducts(searchTerm) {
    const productItems = document.querySelectorAll('.product-item');
    const term = searchTerm.toLowerCase();
    
    productItems.forEach(item => {
        const productName = item.querySelector('h3').textContent.toLowerCase();
        const productMeta = item.querySelector('.product-meta').textContent.toLowerCase();
        
        if (productName.includes(term) || productMeta.includes(term)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
}

// Analytics chart initialization
function initializeAnalytics() {
    // Simulate updating progress bars with animation
    const progressBars = document.querySelectorAll('.progress-fill');
    
    progressBars.forEach(bar => {
        const targetWidth = bar.style.width;
        bar.style.width = '0%';
        
        setTimeout(() => {
            bar.style.width = targetWidth;
        }, 500);
    });
}

// Export data functionality (silent)
function exportData(type) {
    const data = [];
    
    if (type === 'products') {
        const productItems = document.querySelectorAll('.product-item');
        productItems.forEach(item => {
            const name = item.querySelector('h3').textContent;
            const meta = item.querySelector('.product-meta').textContent;
            const price = item.querySelector('.price').textContent;
            const stock = item.querySelector('.stock-badge').textContent;
            
            data.push({ name, meta, price, stock });
        });
    }
    
    // Create and download CSV
    const csv = convertToCSV(data);
    downloadCSV(csv, `${type}_export_${new Date().toISOString().split('T')[0]}.csv`);
}

// Convert data to CSV format
function convertToCSV(data) {
    if (data.length === 0) return '';
    
    const headers = Object.keys(data[0]).join(',');
    const rows = data.map(row => Object.values(row).join(',')).join('\n');
    
    return headers + '\n' + rows;
}

// Download CSV file
function downloadCSV(csv, filename) {
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    
    a.setAttribute('hidden', '');
    a.setAttribute('href', url);
    a.setAttribute('download', filename);
    
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}