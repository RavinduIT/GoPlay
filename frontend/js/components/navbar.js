// Navbar JavaScript
class Navbar {
    constructor() {
        // Try both ID and class selectors for flexibility
        this.mobileMenuBtn = document.getElementById('mobileMenuBtn') || document.querySelector('.mobile-menu-btn');
        this.mobileMenu = document.getElementById('mobileMenu') || document.querySelector('.mobile-menu');
        this.authButtons = document.getElementById('authButtons') || document.querySelector('.nav-actions .auth-buttons');
        this.userMenu = document.getElementById('userMenu') || document.querySelector('.user-menu');
        this.mobileAuth = document.getElementById('mobileAuth') || document.querySelector('.mobile-auth');
        this.mobileUserMenu = document.getElementById('mobileUserMenu') || document.querySelector('.mobile-user-menu');
        this.userName = document.getElementById('userName') || document.querySelector('.user-name');
        this.userEmail = document.getElementById('userEmail') || document.querySelector('.user-email');
        this.userAvatar = document.getElementById('userAvatar') || document.querySelector('.user-avatar');
        
        // Elements to hide when not logged in
        this.cartBtn = document.querySelector('.cart-btn');
        this.providerBtn = document.querySelector('.provider-btn');
        this.adminDropdown = document.querySelector('.admin-dropdown');
        this.mobileCart = document.querySelector('.mobile-cart');
        this.mobileProvider = document.querySelector('.mobile-provider');
        this.mobileAdmin = document.querySelector('.mobile-admin');
        
        // Add timeout for dropdown hover delay
        this.dropdownTimeout = null;
        
        this.init();
    }

    init() {
        this.setupMobileMenu();
        this.setupUserMenu();
        this.checkAuthState();
        this.setActiveNavItem();
    }

    setupMobileMenu() {
        if (this.mobileMenuBtn && this.mobileMenu) {
            this.mobileMenuBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.toggleMobileMenu();
            });

            // Close mobile menu when clicking on nav items
            const mobileNavItems = document.querySelectorAll('.mobile-nav-item, .mobile-auth-btn');
            mobileNavItems.forEach(item => {
                item.addEventListener('click', () => {
                    this.closeMobileMenu();
                });
            });

            // Close mobile menu when clicking outside
            document.addEventListener('click', (e) => {
                if (this.mobileMenu.classList.contains('show') && 
                    !this.mobileMenu.contains(e.target) && 
                    !this.mobileMenuBtn.contains(e.target)) {
                    this.closeMobileMenu();
                }
            });

            // Handle escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.mobileMenu.classList.contains('show')) {
                    this.closeMobileMenu();
                }
            });
        } else {
            console.warn('Mobile menu elements not found:', {
                mobileMenuBtn: !!this.mobileMenuBtn,
                mobileMenu: !!this.mobileUserMenu
            });
        }
    }

    setupUserMenu() {
        // Setup dropdown hover effects with improved hover handling
        const userMenuElement = document.querySelector('.user-menu');
        if (userMenuElement) {
            const dropdown = userMenuElement.querySelector('.user-dropdown');
            if (dropdown) {
                // Show dropdown on hover
                const showDropdown = () => {
                    // Clear any pending hide timeout
                    if (this.dropdownTimeout) {
                        clearTimeout(this.dropdownTimeout);
                        this.dropdownTimeout = null;
                    }
                    
                    dropdown.style.opacity = '1';
                    dropdown.style.visibility = 'visible';
                    dropdown.style.transform = 'translateY(0)';
                };

                // Hide dropdown with delay
                const hideDropdown = () => {
                    this.dropdownTimeout = setTimeout(() => {
                        dropdown.style.opacity = '0';
                        dropdown.style.visibility = 'hidden';
                        dropdown.style.transform = 'translateY(-10px)';
                    }, 150); // Small delay to prevent flickering
                };

                // Add hover events to both the trigger and dropdown
                userMenuElement.addEventListener('mouseenter', showDropdown);
                userMenuElement.addEventListener('mouseleave', hideDropdown);
                
                // Also add events to the dropdown itself
                dropdown.addEventListener('mouseenter', showDropdown);
                dropdown.addEventListener('mouseleave', hideDropdown);
            }
        }

        // Setup admin dropdown with same improved logic
        const adminDropdown = document.querySelector('.admin-dropdown');
        if (adminDropdown) {
            const adminMenu = adminDropdown.querySelector('.admin-menu');
            if (adminMenu) {
                let adminTimeout = null;

                const showAdminMenu = () => {
                    if (adminTimeout) {
                        clearTimeout(adminTimeout);
                        adminTimeout = null;
                    }
                    
                    adminMenu.style.opacity = '1';
                    adminMenu.style.visibility = 'visible';
                    adminMenu.style.transform = 'translateY(0)';
                };

                const hideAdminMenu = () => {
                    adminTimeout = setTimeout(() => {
                        adminMenu.style.opacity = '0';
                        adminMenu.style.visibility = 'hidden';
                        adminMenu.style.transform = 'translateY(-10px)';
                    }, 150);
                };

                adminDropdown.addEventListener('mouseenter', showAdminMenu);
                adminDropdown.addEventListener('mouseleave', hideAdminMenu);
                
                adminMenu.addEventListener('mouseenter', showAdminMenu);
                adminMenu.addEventListener('mouseleave', hideAdminMenu);
            }
        }
    }

    toggleMobileMenu() {
        const isOpen = this.mobileMenu.classList.contains('show');
        
        if (isOpen) {
            this.closeMobileMenu();
        } else {
            this.openMobileMenu();
        }
    }

    openMobileMenu() {
        this.mobileMenu.classList.add('show');
        this.mobileMenu.classList.remove('hide');
        this.mobileMenuBtn.innerHTML = '<i class="fas fa-times"></i>';
        this.mobileMenuBtn.setAttribute('aria-expanded', 'true');
        this.mobileMenuBtn.setAttribute('aria-label', 'Close menu');
        document.body.style.overflow = 'hidden';
    }

    closeMobileMenu() {
        this.mobileMenu.classList.add('hide');
        this.mobileMenu.classList.remove('show');
        this.mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>';
        this.mobileMenuBtn.setAttribute('aria-expanded', 'false');
        this.mobileMenuBtn.setAttribute('aria-label', 'Open menu');
        document.body.style.overflow = '';
        
        // Remove hide class after animation completes
        setTimeout(() => {
            this.mobileMenu.classList.remove('hide');
        }, 300);
    }

    checkAuthState() {
        const currentUser = this.getCurrentUser();
        
        if (currentUser) {
            this.showUserMenu(currentUser);
            this.showLoggedInElements();
        } else {
            this.showAuthButtons();
            this.hideLoggedInElements();
        }
    }

    getCurrentUser() {
        try {
            const userData = localStorage.getItem('currentUser');
            return userData ? JSON.parse(userData) : null;
        } catch (error) {
            console.error('Error getting current user:', error);
            return null;
        }
    }

    showUserMenu(user) {
        // Hide auth buttons
        if (this.authButtons) {
            this.authButtons.style.display = 'none';
        }
        if (this.mobileAuth) {
            this.mobileAuth.style.display = 'none';
        }

        // Show user menu
        if (this.userMenu) {
            this.userMenu.style.display = 'block';
        }
        if (this.mobileUserMenu) {
            this.mobileUserMenu.style.display = 'block';
        }

        // Update user info for desktop
        if (this.userName) {
            this.userName.textContent = user.name;
        }
        if (this.userEmail) {
            this.userEmail.textContent = user.email;
        }
        if (this.userAvatar) {
            this.userAvatar.textContent = user.avatar || '👤';
        }

        // Update user info for mobile
        const mobileUserName = document.getElementById('mobileUserName');
        const mobileUserEmail = document.getElementById('mobileUserEmail');
        const mobileUserAvatar = document.getElementById('mobileUserAvatar');
        
        if (mobileUserName) {
            mobileUserName.textContent = user.name;
        }
        if (mobileUserEmail) {
            mobileUserEmail.textContent = user.email;
        }
        if (mobileUserAvatar) {
            mobileUserAvatar.textContent = user.avatar || '👤';
        }
    }

    showAuthButtons() {
        // Show auth buttons
        if (this.authButtons) {
            this.authButtons.style.display = 'flex';
        }
        if (this.mobileAuth) {
            this.mobileAuth.style.display = 'flex';
        }

        // Hide user menu
        if (this.userMenu) {
            this.userMenu.style.display = 'none';
        }
        if (this.mobileUserMenu) {
            this.mobileUserMenu.style.display = 'none';
        }
    }

    showLoggedInElements() {
        // Show elements that should only be visible when logged in
        if (this.cartBtn) {
            this.cartBtn.style.display = 'flex';
        }
        if (this.providerBtn) {
            this.providerBtn.style.display = 'block';
        }
        if (this.adminDropdown) {
            this.adminDropdown.style.display = 'block';
        }
        if (this.mobileCart) {
            this.mobileCart.style.display = 'block';
        }
        if (this.mobileProvider) {
            this.mobileProvider.style.display = 'block';
        }
        if (this.mobileAdmin) {
            this.mobileAdmin.style.display = 'block';
        }
    }

    hideLoggedInElements() {
        // Hide elements that should only be visible when logged in
        if (this.cartBtn) {
            this.cartBtn.style.display = 'none';
        }
        if (this.providerBtn) {
            this.providerBtn.style.display = 'none';
        }
        if (this.adminDropdown) {
            this.adminDropdown.style.display = 'none';
        }
        if (this.mobileCart) {
            this.mobileCart.style.display = 'none';
        }
        if (this.mobileProvider) {
            this.mobileProvider.style.display = 'none';
        }
        if (this.mobileAdmin) {
            this.mobileAdmin.style.display = 'none';
        }
    }

    setActiveNavItem() {
        const currentPath = window.location.pathname;
        const navItems = document.querySelectorAll('.nav-item, .mobile-nav-item');
        
        navItems.forEach(item => {
            const href = item.getAttribute('href');
            if (href === currentPath || (currentPath === '/' && href === '/')) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
    }

    updateCartCount(count) {
        const cartCounts = document.querySelectorAll('.cart-count');
        cartCounts.forEach(element => {
            element.textContent = count;
        });
    }
}

// Global logout function
function logout() {
    localStorage.removeItem('currentUser');
    window.location.href = '/';
}

// Initialize navbar when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.navbar = new Navbar();
});

// Update auth state when storage changes
window.addEventListener('storage', (e) => {
    if (e.key === 'currentUser') {
        if (window.navbar) {
            window.navbar.checkAuthState();
        }
    }
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = Navbar;
}