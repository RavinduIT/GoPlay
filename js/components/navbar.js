// Navbar JavaScript
class Navbar {
    constructor() {
        this.mobileMenuBtn = document.getElementById('mobileMenuBtn');
        this.mobileMenu = document.getElementById('mobileMenu');
        this.authButtons = document.getElementById('authButtons');
        this.userMenu = document.getElementById('userMenu');
        this.mobileAuth = document.getElementById('mobileAuth');
        this.mobileUserMenu = document.getElementById('mobileUserMenu');
        this.userName = document.getElementById('userName');
        this.userEmail = document.getElementById('userEmail');
        this.userAvatar = document.getElementById('userAvatar');
        
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
            this.mobileMenuBtn.addEventListener('click', () => {
                this.toggleMobileMenu();
            });

            // Close mobile menu when clicking on nav items
            const mobileNavItems = document.querySelectorAll('.mobile-nav-item');
            mobileNavItems.forEach(item => {
                item.addEventListener('click', () => {
                    this.closeMobileMenu();
                });
            });

            // Close mobile menu when clicking outside
            document.addEventListener('click', (e) => {
                if (!this.mobileMenu.contains(e.target) && !this.mobileMenuBtn.contains(e.target)) {
                    this.closeMobileMenu();
                }
            });
        }
    }

    setupUserMenu() {
        // Setup dropdown hover effects
        const userMenuElement = document.querySelector('.user-menu');
        if (userMenuElement) {
            const dropdown = userMenuElement.querySelector('.user-dropdown');
            
            userMenuElement.addEventListener('mouseenter', () => {
                dropdown.style.opacity = '1';
                dropdown.style.visibility = 'visible';
                dropdown.style.transform = 'translateY(0)';
            });

            userMenuElement.addEventListener('mouseleave', () => {
                dropdown.style.opacity = '0';
                dropdown.style.visibility = 'hidden';
                dropdown.style.transform = 'translateY(-10px)';
            });
        }

        // Setup admin dropdown
        const adminDropdown = document.querySelector('.admin-dropdown');
        if (adminDropdown) {
            const adminMenu = adminDropdown.querySelector('.admin-menu');
            
            adminDropdown.addEventListener('mouseenter', () => {
                adminMenu.style.opacity = '1';
                adminMenu.style.visibility = 'visible';
                adminMenu.style.transform = 'translateY(0)';
            });

            adminDropdown.addEventListener('mouseleave', () => {
                adminMenu.style.opacity = '0';
                adminMenu.style.visibility = 'hidden';
                adminMenu.style.transform = 'translateY(-10px)';
            });
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
        this.mobileMenu.style.display = 'block';
        this.mobileMenuBtn.innerHTML = '<i class="fas fa-times"></i>';
        document.body.style.overflow = 'hidden';
    }

    closeMobileMenu() {
        this.mobileMenu.classList.remove('show');
        this.mobileMenu.style.display = 'none';
        this.mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>';
        document.body.style.overflow = '';
    }

    checkAuthState() {
        const currentUser = this.getCurrentUser();
        
        if (currentUser) {
            this.showUserMenu(currentUser);
        } else {
            this.showAuthButtons();
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

        // Update user info
        if (this.userName) {
            this.userName.textContent = user.name;
        }
        if (this.userEmail) {
            this.userEmail.textContent = user.email;
        }
        if (this.userAvatar) {
            this.userAvatar.textContent = user.avatar || '👤';
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