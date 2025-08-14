// Dashboard JavaScript Functionality

document.addEventListener('DOMContentLoaded', function() {
    // Initialize dashboard
    initializeDashboard();
});

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

// Add new product to inventory
function addNewProduct(productData) {
    const productList = document.getElementById('product-list');
    const productItem = createProductItem(productData);
    productList.appendChild(productItem);
    
    // Update total products count
    updateTotalProducts();
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
    // Edit functionality - no popup
    console.log('Edit product:', productItem.querySelector('h3').textContent);
}

// Delete product functionality
function deleteProduct(productItem) {
    // Delete without confirmation
    productItem.remove();
    updateTotalProducts();
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