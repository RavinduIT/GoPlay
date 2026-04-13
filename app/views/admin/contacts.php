<?php $_base = defined('BASE_URL') ? BASE_URL : ''; ?>
<?php
/**
 * Admin Contact Messages Management View
 */
$activePage = $activePage ?? 'contacts';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages - GoPlay Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= $_base ?>/public/css/pages/admin-contacts.css">
</head>
<body>
    <div class="admin-dashboard">
        <?php include APP_PATH . '/views/components/admin-sidebar.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <div class="header-left">
                    <button class="sidebar-toggle" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
                    <div>
                        <h1>Contact Messages</h1>
                        <p class="header-subtitle">View and respond to user inquiries</p>
                    </div>
                </div>
                <div class="header-right">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" placeholder="Search messages..." oninput="debounceSearch()">
                    </div>
                </div>
            </header>

            <div class="admin-content">
                <!-- Stats -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(59,130,246,0.1); color: #3b82f6;"><i class="fas fa-envelope"></i></div>
                        <div class="stat-info"><span class="stat-number" id="statTotal">0</span><span class="stat-label">Total Messages</span></div>
                    </div>
                    <div class="stat-card stat-clickable" onclick="filterMessages('unread')">
                        <div class="stat-icon" style="background: rgba(245,158,11,0.1); color: #f59e0b;"><i class="fas fa-envelope-open"></i></div>
                        <div class="stat-info"><span class="stat-number" id="statUnread">0</span><span class="stat-label">Unread</span></div>
                    </div>
                    <div class="stat-card stat-clickable" onclick="filterMessages('replied')">
                        <div class="stat-icon" style="background: rgba(16,185,129,0.1); color: #10b981;"><i class="fas fa-reply"></i></div>
                        <div class="stat-info"><span class="stat-number" id="statReplied">0</span><span class="stat-label">Replied</span></div>
                    </div>
                    <div class="stat-card stat-clickable" onclick="filterMessages('archived')">
                        <div class="stat-icon" style="background: rgba(100,116,139,0.1); color: #64748b;"><i class="fas fa-archive"></i></div>
                        <div class="stat-info"><span class="stat-number" id="statArchived">0</span><span class="stat-label">Archived</span></div>
                    </div>
                </div>

                <!-- Filter Tabs -->
                <div class="filter-tabs">
                    <button class="filter-tab active" data-status="all" onclick="filterMessages('all', this)">All</button>
                    <button class="filter-tab" data-status="unread" onclick="filterMessages('unread', this)">Unread</button>
                    <button class="filter-tab" data-status="read" onclick="filterMessages('read', this)">Read</button>
                    <button class="filter-tab" data-status="replied" onclick="filterMessages('replied', this)">Replied</button>
                    <button class="filter-tab" data-status="archived" onclick="filterMessages('archived', this)">Archived</button>
                </div>

                <!-- Messages List -->
                <div class="messages-container" id="messagesContainer">
                    <div class="loading-state"><i class="fas fa-spinner fa-spin"></i><p>Loading messages...</p></div>
                </div>

                <!-- Pagination -->
                <div class="pagination" id="pagination"></div>
            </div>
        </main>
    </div>

    <!-- View/Reply Modal -->
    <div class="modal-overlay" id="messageModal">
        <div class="modal">
            <div class="modal-header">
                <h2 id="msgModalTitle">Message Details</h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body" id="messageDetailBody">
                <!-- Filled by JS -->
            </div>
            <div class="modal-footer" id="messageModalFooter">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Close</button>
                <button type="button" class="btn btn-warning" id="archiveBtn" onclick="archiveMessage()"><i class="fas fa-archive"></i> Archive</button>
                <button type="button" class="btn btn-danger" id="deleteBtn" onclick="deleteMessage()"><i class="fas fa-trash"></i> Delete</button>
            </div>
        </div>
    </div>

    <script src="<?= $_base ?>/public/js/pages/admin-contacts.js"></script>
</body>
</html>
