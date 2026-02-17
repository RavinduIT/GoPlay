/**
 * Shop Owner Sidebar - Shared JavaScript
 * Handles sidebar toggle functionality across all shop owner pages
 */

// Initialize sidebar when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initShopOwnerSidebar);
} else {
    initShopOwnerSidebar();
}

function initShopOwnerSidebar() {
    const sidebar = document.getElementById('dashboardSidebar');
    if (!sidebar) return;
    
    const toggleButtons = document.querySelectorAll('.sidebar-toggle');
    
    toggleButtons.forEach(button => {
        button.addEventListener('click', toggleSidebar);
    });
}

function toggleSidebar() {
    const sidebar = document.getElementById('dashboardSidebar');
    const main = document.querySelector('.dashboard-main');
    
    if (sidebar) {
        sidebar.classList.toggle('collapsed');
    }
    
    if (main) {
        main.classList.toggle('sidebar-collapsed');
    }
}

// Export for use in inline handlers
window.toggleSidebar = toggleSidebar;
