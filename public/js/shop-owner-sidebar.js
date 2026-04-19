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
    const overlay = document.getElementById('sidebarOverlay');

    if (!sidebar) return;

    if (window.innerWidth <= 768) {
        sidebar.classList.toggle('active');
        if (overlay) overlay.classList.toggle('active');
    } else {
        sidebar.classList.toggle('collapsed');
        if (main) main.classList.toggle('sidebar-collapsed');
    }
}

function closeSidebar() {
    const sidebar = document.getElementById('dashboardSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (sidebar) sidebar.classList.remove('active');
    if (overlay) overlay.classList.remove('active');
}

// Export for use in inline handlers
window.toggleSidebar = toggleSidebar;
