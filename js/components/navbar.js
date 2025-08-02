// Navbar JavaScript
class NavbarManager {
    constructor() {
        this.currentUser = null;
        this.init();
    }

    init() {
        // Initialize Lucide icons
        lucide.createIcons();
        
        // Load user data from localStorage
        this.loadUserData();
        
        // Update UI based on authentication status
        this.updateAuthUI();
        
        // Set active nav item based on current path
        this.setActiveNavItem();
        
        // Add event listeners
        this.addEventListeners();
    }

    loadUserData() {
        const storedUser = localStorage.getItem('currentUser');
        if (storedUser) {
            this.currentUser = JSON.parse(storedUser);
        }
    }

    updateAuthUI() {
        const authButtons = document.getElementById('authButtons');
        const userMenu = document.getElementById('userMenu');
        const mobileAuthButtons = document.getElementById('mobileAuthButtons');
        const mobileUserMenu = document.getElementById('mobileUserMenu');
        
        if (this.currentUser) {
            // User is authenticated
            if (authButtons) authButtons.style.display = 'none';
            if (userMenu) userMenu.style.display = 'flex';
            if (mobileAuthButtons) mobileAuthButtons.style.display = 'none';
            if (mobileUserMenu) mobileUserMenu.style.display = 'flex';
            
            // Update user info
            const userName = document.getElementById('userName');
            const userEmail = document.getElementById('userEmail');
            if (userName) userName.textContent = this.currentUser.name;
            if (userEmail) userEmail.textContent = this.currentUser.email;
        } else {
            // User is not authenticated
            if (authButtons) authButtons.style.display = 'flex';
            if (userMenu) userMenu.style.display = 'none';
            if (mobileAuthButtons) mobileAuthButtons.style.display = 'flex';
            if (mobileUserMenu) mobileUserMenu.style.display = 'none';
        }
    }

    setActiveNavItem() {
        const currentPath = window.location.pathname;
        const navItems = document.querySelectorAll('.nav-item, .mobile-nav-item');
        
        navItems.forEach(item => {
            item.classList.remove('active');
            if (item.getAttribute('href') === currentPath) {
                item.classList.add('active');
            }
        });
    }

    addEventListeners() {
        // Mobile menu toggle
        const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', this.toggleMobileMenu.bind(this));
        }

        // Close mobile menu when clicking nav items
        const mobileNavItems = document.querySelectorAll('.mobile-nav-item');
        mobileNavItems.forEach(item => {
            item.addEventListener('click', this.closeMobileMenu.bind(this));
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', this.handleOutsideClick.bind(this));
    }

    toggleMobileMenu() {
        const mobileNav = document.getElementById('mobileNav');
        const menuIcon = document.querySelector('.menu-icon');
        const closeIcon = document.querySelector('.close-icon');
        
        if (mobileNav.classList.contains('active')) {
            mobileNav.classList.remove('active');
            mobileNav.style.display = 'none';
            menuIcon.style.display = 'block';
            closeIcon.style.display = 'none';
        } else {
            mobileNav.classList.add('active');
            mobileNav.style.display = 'block';
            menuIcon.style.display = 'none';
            closeIcon.style.display = 'block';
        }
    }

    closeMobileMenu() {
        const mobileNav = document.getElementById('mobileNav');
        const menuIcon = document.querySelector('.menu-icon');
        const closeIcon = document.querySelector('.close-icon');
        
        mobileNav.classList.remove('active');
        mobileNav.style.display = 'none';
        menuIcon.style.display = 'block';
        closeIcon.style.display = 'none';
    }

    toggleAdminDropdown() {
        const dropdown = document.querySelector('.admin-dropdown').parentElement;
        this.toggleDropdown(dropdown);
    }

    toggleUserDropdown() {
        const dropdown = document.querySelector('.user-avatar').parentElement;
        this.toggleDropdown(dropdown);
    }

    toggleDropdown(dropdown) {
        const isActive = dropdown.classList.contains('active');
        
        // Close all dropdowns first
        document.querySelectorAll('.dropdown').forEach(d => {
            d.classList.remove('active');
        });
        
        // Toggle current dropdown
        if (!isActive) {
            dropdown.classList.add('active');
        }
    }

    handleOutsideClick(event) {
        const dropdowns = document.querySelectorAll('.dropdown');
        dropdowns.forEach(dropdown => {
            if (!dropdown.contains(event.target)) {
                dropdown.classList.remove('active');
            }
        });
    }

    logout() {
        // Clear user data
        this.currentUser = null;
        localStorage.removeItem('currentUser');
        
        // Update UI
        this.updateAuthUI();
        
        // Redirect to home page
        window.location.href = '/';
    }
}

// Global functions for onclick handlers
function toggleMobileMenu() {
    navbarManager.toggleMobileMenu();
}

function closeMobileMenu() {
    navbarManager.closeMobileMenu();
}

function toggleAdminDropdown() {
    navbarManager.toggleAdminDropdown();
}

function toggleUserDropdown() {
    navbarManager.toggleUserDropdown();
}

function logout() {
    navbarManager.logout();
}

// Initialize navbar when DOM is loaded
let navbarManager;
document.addEventListener('DOMContentLoaded', function() {
    navbarManager = new NavbarManager();
});