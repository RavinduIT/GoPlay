// Shop page functionality
function loadShopPage() {
    // Load shop page content
}

function initializeProductGrid() {
    // Initialize product grid
}

function handleProductFilter() {
    // Handle product filtering
}

function handleAddToCart() {
    // Handle add to cart
}

// Export shop page functions
window.shopPage = {
    load: loadShopPage,
    init: initializeShopPage
};

function initializeShopPage() {
    // Initialize shop page
}

    // Professional Shop Application
    class ShopApp {
      constructor() {
        this.products = [];
        this.filteredProducts = [];
        this.cart = JSON.parse(localStorage.getItem('cart')) || [];
        this.currentView = 'grid';
        this.init();
      }

      async init() {
        try {
          await this.loadProducts();
          this.setupEventListeners();
          this.updateCartDisplay();
          this.hideLoadingSpinner();
          this.renderProducts();
        } catch (error) {
          console.error('Failed to initialize shop:', error);
          this.showError('Failed to load products');
        }
      }

      async loadProducts() {
          const response = await fetch('../data/products.json');
          if (!response.ok) throw new Error('Failed to fetch products');
          this.products = await response.json();
          this.filteredProducts = [...this.products];
        
      }

      

      setupEventListeners() {
        // Filter listeners
        document.getElementById('searchInput').addEventListener('input', () => this.applyFilters());
        document.getElementById('categoryFilter').addEventListener('change', () => this.applyFilters());
        document.getElementById('brandFilter').addEventListener('change', () => this.applyFilters());
        document.getElementById('priceFilter').addEventListener('change', () => this.applyFilters());
        document.getElementById('sortFilter').addEventListener('change', () => this.applyFilters());

        // View toggle listeners
        document.getElementById('gridView').addEventListener('click', () => this.setView('grid'));
        document.getElementById('listView').addEventListener('click', () => this.setView('list'));

        // Cart listeners
        document.getElementById('cartButton').addEventListener('click', () => this.toggleCart());
        document.getElementById('closeCart').addEventListener('click', () => this.closeCart());
      }

      applyFilters() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const category = document.getElementById('categoryFilter').value;
        const brand = document.getElementById('brandFilter').value;
        const priceRange = document.getElementById('priceFilter').value;
        const sort = document.getElementById('sortFilter').value;

        this.filteredProducts = this.products.filter(product => {
          const matchesSearch = !searchTerm || 
            product.name.toLowerCase().includes(searchTerm) ||
            product.description.toLowerCase().includes(searchTerm) ||
            product.brand.toLowerCase().includes(searchTerm);

          const matchesCategory = !category || product.category === category;
          const matchesBrand = !brand || product.brand === brand;
          
          let matchesPrice = true;
          if (priceRange) {
            if (priceRange === '200+') {
              matchesPrice = product.price >= 200;
            } else {
              const [min, max] = priceRange.split('-').map(Number);
              matchesPrice = product.price >= min && product.price <= max;
            }
          }

          return matchesSearch && matchesCategory && matchesBrand && matchesPrice && product.status === 'Active';
        });

        // Apply sorting
        this.sortProducts(sort);
        this.renderProducts();
      }

      sortProducts(sortType) {
        switch(sortType) {
          case 'name-asc':
            this.filteredProducts.sort((a, b) => a.name.localeCompare(b.name));
            break;
          case 'name-desc':
            this.filteredProducts.sort((a, b) => b.name.localeCompare(a.name));
            break;
          case 'price-asc':
            this.filteredProducts.sort((a, b) => a.price - b.price);
            break;
          case 'price-desc':
            this.filteredProducts.sort((a, b) => b.price - a.price);
            break;
          case 'rating-desc':
            this.filteredProducts.sort((a, b) => b.rating - a.rating);
            break;
          case 'newest':
            this.filteredProducts.sort((a, b) => new Date(b.listedDate) - new Date(a.listedDate));
            break;
        }
      }

      renderProducts() {
        const grid = document.getElementById('productGrid');
        const noResults = document.getElementById('noResults');
        
        if (this.filteredProducts.length === 0) {
          grid.style.display = 'none';
          noResults.style.display = 'block';
          this.updateProductCount(0);
          return;
        }

        grid.style.display = 'grid';
        noResults.style.display = 'none';
        
        grid.innerHTML = this.filteredProducts.map(product => 
          this.createProductCard(product)
        ).join('');
        
        this.updateProductCount(this.filteredProducts.length);
      }

      createProductCard(product) {
        return `
          <div class="product-card ${!product.inStock ? 'out-of-stock' : ''}">
            <div class="product-image">
              <img src="../assets/images/${product.images[0]}" alt="${product.name}" 
                   onerror="this.src='../assets/images/placeholder.jpg'">
            </div>
            
            <div class="product-info">
              <h3 class="product-name">${product.name}</h3>
              <div class="product-brand">${product.brand}</div>
              
              <div class="product-category">
                <span class="category-tag">${product.category}</span>
              </div>
              
              <div class="product-rating">
                <div class="stars">
                  ${this.generateStars(product.rating)}
                </div>
                <span class="rating-text">${product.rating} (${product.totalReviews})</span>
              </div>
              
              <div class="product-pricing">
                <span class="current-price">Rs.${product.price.toFixed(2)}</span>
              </div>
              
              <p class="product-description">${product.description}</p>
              
              <div class="product-features">
                ${product.features ? product.features.map(feature => `<span class="feature-tag">${feature}</span>`).join('') : ''}
              </div>
              
              <div class="product-buttons">
                <button class="btn-wishlist" onclick="shop.toggleWishlist(${product.id})">
                  <i class="far fa-heart"></i>
                </button>
                <button class="btn-secondary" onclick="shop.viewDetails(${product.id})">
                  View Details
                </button>
                <button class="btn-primary ${!product.inStock ? 'disabled' : ''}" 
                        onclick="shop.addToCart(${product.id})" 
                        ${!product.inStock ? 'disabled' : ''}>
                  ${product.inStock ? 'Buy Now' : 'Out of Stock'}
                </button>
              </div>
            </div>
          </div>
        `;
      }

      generateStars(rating) {
        let stars = '';
        for (let i = 1; i <= 5; i++) {
          if (i <= rating) {
            stars += '<i class="fas fa-star"></i>';
          } else if (i - 0.5 <= rating) {
            stars += '<i class="fas fa-star-half-alt"></i>';
          } else {
            stars += '<i class="far fa-star"></i>';
          }
        }
        return stars;
      }

      setView(viewType) {
        this.currentView = viewType;
        const grid = document.getElementById('productGrid');
        const gridBtn = document.getElementById('gridView');
        const listBtn = document.getElementById('listView');

        if (viewType === 'grid') {
          grid.className = 'product-grid grid-view';
          gridBtn.classList.add('active');
          listBtn.classList.remove('active');
        } else {
          grid.className = 'product-grid list-view';
          listBtn.classList.add('active');
          gridBtn.classList.remove('active');
        }
        
        this.renderProducts();
      }

      addToCart(productId) {
        const product = this.products.find(p => p.id === productId);
        if (!product || !product.inStock) return;

        const existingItem = this.cart.find(item => item.id === productId);
        if (existingItem) {
          existingItem.quantity += 1;
        } else {
          this.cart.push({ ...product, quantity: 1 });
        }

        this.saveCart();
        this.updateCartDisplay();
        this.showCartNotification(product.name);
      }

      removeFromCart(productId) {
        this.cart = this.cart.filter(item => item.id !== productId);
        this.saveCart();
        this.updateCartDisplay();
        this.renderCartItems();
      }

      updateCartQuantity(productId, quantity) {
        const item = this.cart.find(item => item.id === productId);
        if (item) {
          item.quantity = Math.max(0, quantity);
          if (item.quantity === 0) {
            this.removeFromCart(productId);
          } else {
            this.saveCart();
            this.updateCartDisplay();
            this.renderCartItems();
          }
        }
      }
      // remove this--malika
      saveCart() {
        localStorage.setItem('cart', JSON.stringify(this.cart));
      }

      updateCartDisplay() {
        const cartCount = document.getElementById('cartCount');
        const cartTotal = document.getElementById('cartTotal');
        
        const totalItems = this.cart.reduce((sum, item) => sum + item.quantity, 0);
        const totalPrice = this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        
        cartCount.textContent = totalItems;
        cartTotal.textContent = totalPrice.toFixed(2);
      }

      toggleCart() {
        const sidebar = document.getElementById('cartSidebar');
        sidebar.classList.toggle('open');
        this.renderCartItems();
      }

      closeCart() {
        document.getElementById('cartSidebar').classList.remove('open');
      }

      renderCartItems() {
        const cartItems = document.getElementById('cartItems');
        
        if (this.cart.length === 0) {
          cartItems.innerHTML = '<div class="empty-cart"><i class="fas fa-shopping-cart"></i><p>Your cart is empty</p></div>';
          return;
        }

        cartItems.innerHTML = this.cart.map(item => `
          <div class="cart-item">
            <img src="../assets/images/${item.images[0]}" alt="${item.name}" 
                 onerror="this.src='../assets/images/placeholder.jpg'">
            <div class="item-details">
              <h4>${item.name}</h4>
              <p class="item-price">$${item.price.toFixed(2)}</p>
            </div>
            <div class="quantity-controls">
              <button onclick="shop.updateCartQuantity(${item.id}, ${item.quantity - 1})">-</button>
              <span>${item.quantity}</span>
              <button onclick="shop.updateCartQuantity(${item.id}, ${item.quantity + 1})">+</button>
            </div>
            <button class="remove-item" onclick="shop.removeFromCart(${item.id})">
              <i class="fas fa-trash"></i>
            </button>
          </div>
        `).join('');
      }

      showCartNotification(productName) {
        // Simple notification - you can enhance this
        const notification = document.createElement('div');
        notification.className = 'cart-notification';
        notification.innerHTML = `<i class="fas fa-check"></i> ${productName} added to cart!`;
        document.body.appendChild(notification);
        
        setTimeout(() => {
          notification.classList.add('show');
        }, 100);
        
        setTimeout(() => {
          notification.classList.remove('show');
          setTimeout(() => notification.remove(), 300);
        }, 3000);
      }

      updateProductCount(count) {
        document.getElementById('productCount').textContent = 
          `${count} Product${count !== 1 ? 's' : ''} Found`;
      }

      hideLoadingSpinner() {
        document.getElementById('loadingSpinner').style.display = 'none';
      }

      showError(message) {
        const grid = document.getElementById('productGrid');
        grid.innerHTML = `<div class="error-message"><i class="fas fa-exclamation-triangle"></i><p>${message}</p></div>`;
      }

      // Placeholder methods for future features
      toggleWishlist(productId) {
        console.log('Toggle wishlist for product:', productId);
        // Implement wishlist functionality
      }

      quickView(productId) {
        console.log('Quick view for product:', productId);
        // Implement quick view modal
      }

      viewDetails(productId) {
        console.log('View details for product:', productId);
        // Redirect to product details page
        window.location.href = `/pages/product-details.html?id=${productId}`;
      }
    }

    // Initialize shop when DOM is loaded
    let shop;
    document.addEventListener('DOMContentLoaded', () => {
      shop = new ShopApp();
    });

    
  