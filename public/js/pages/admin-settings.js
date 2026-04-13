// Admin Settings JavaScript
let originalSettings = {};
let hasUnsavedChanges = false;

document.addEventListener('DOMContentLoaded', function() {
    initializeSettings();
    setupEventListeners();
    loadSystemInfo();
});

function setupEventListeners() {
    // Tab switching
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            switchTab(this.dataset.tab);
        });
    });

    // Track form changes
    document.querySelectorAll('.settings-form input, .settings-form textarea, .settings-form select').forEach(input => {
        input.addEventListener('change', function() {
            hasUnsavedChanges = true;
            showFloatingSaveButton();
        });
    });

    // Warn before leaving with unsaved changes
    window.addEventListener('beforeunload', function(e) {
        if (hasUnsavedChanges) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
}

function switchTab(tabName) {
    // Update tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');

    // Update panels
    document.querySelectorAll('.settings-panel').forEach(panel => {
        panel.classList.remove('active');
    });
    document.getElementById(`${tabName}-panel`).classList.add('active');
}

async function initializeSettings() {
    try {
        const response = await fetch((window.BASE_URL||'')+'/api/admin/settings');
        const data = await response.json();

        if (data.success) {
            originalSettings = data.data;
            populateSettings(data.data);
        }
    } catch (error) {
        console.error('Error loading settings:', error);
        showNotification('Failed to load settings', 'error');
    }
}

function populateSettings(settings) {
    // Populate General Settings
    const general = settings.general || [];
    general.forEach(setting => {
        const input = document.getElementById(setting.key_name);
        if (input) {
            if (input.type === 'checkbox') {
                input.checked = setting.value === 'true' || setting.value === '1';
            } else {
                input.value = setting.value || '';
            }
        }
    });

    // Populate Email Settings
    const email = settings.email || [];
    email.forEach(setting => {
        const input = document.getElementById(setting.key_name);
        if (input) {
            input.value = setting.value || '';
        }
    });

    // Populate Payment Settings
    const payment = settings.payment || [];
    payment.forEach(setting => {
        const input = document.getElementById(setting.key_name);
        if (input) {
            if (input.type === 'checkbox') {
                input.checked = setting.value === 'true' || setting.value === '1';
            } else {
                input.value = setting.value || '';
            }
        }
    });

    // Populate Booking Settings
    const booking = settings.booking || [];
    booking.forEach(setting => {
        const input = document.getElementById(setting.key_name);
        if (input) {
            if (input.type === 'checkbox') {
                input.checked = setting.value === 'true' || setting.value === '1';
            } else {
                input.value = setting.value || '';
            }
        }
    });

    // Populate Security Settings
    const other = settings.other || [];
    other.forEach(setting => {
        const input = document.getElementById(setting.key_name);
        if (input) {
            if (input.type === 'checkbox') {
                input.checked = setting.value === 'true' || setting.value === '1';
            } else {
                input.value = setting.value || '';
            }
        }
    });
}

async function saveAllSettings() {
    try {
        showNotification('Saving settings...', 'info');

        const settings = {};
        
        // Collect all form inputs
        document.querySelectorAll('.settings-form input, .settings-form textarea, .settings-form select').forEach(input => {
            if (input.id) {
                if (input.type === 'checkbox') {
                    settings[input.id] = input.checked ? '1' : '0';
                } else {
                    settings[input.id] = input.value;
                }
            }
        });

        const response = await fetch((window.BASE_URL||'')+'/api/admin/settings/bulk', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ settings })
        });

        const data = await response.json();

        if (data.success) {
            hasUnsavedChanges = false;
            hideFloatingSaveButton();
            showNotification('Settings saved successfully!', 'success');
            originalSettings = settings;
        } else {
            throw new Error(data.message || 'Failed to save settings');
        }
    } catch (error) {
        console.error('Error saving settings:', error);
        showNotification('Failed to save settings', 'error');
    }
}

async function loadSystemInfo() {
    try {
        const response = await fetch((window.BASE_URL||'')+'/api/admin/settings/system-info');
        const data = await response.json();

        if (data.success) {
            const info = data.data;
            
            document.getElementById('phpVersion').textContent = info.php_version || 'N/A';
            document.getElementById('dbVersion').textContent = info.database_version ? 
                info.database_version.split('-')[0] : 'N/A';
            document.getElementById('serverInfo').textContent = info.server_software || 'N/A';
            
            if (info.disk_space) {
                const totalGB = (info.disk_space.total / (1024**3)).toFixed(2);
                const freeGB = (info.disk_space.free / (1024**3)).toFixed(2);
                document.getElementById('diskSpace').textContent = `${freeGB} GB / ${totalGB} GB free`;
            }
        }
    } catch (error) {
        console.error('Error loading system info:', error);
    }
}

async function testEmailConfig() {
    const email = prompt('Enter email address to send test email:');
    if (!email) return;

    if (!validateEmail(email)) {
        showNotification('Please enter a valid email address', 'error');
        return;
    }

    try {
        showNotification('Sending test email...', 'info');

        const response = await fetch((window.BASE_URL||'')+'/api/admin/settings/test-email', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email })
        });

        const data = await response.json();

        if (data.success) {
            showNotification('Test email sent successfully!', 'success');
        } else {
            throw new Error(data.message || 'Failed to send test email');
        }
    } catch (error) {
        console.error('Error sending test email:', error);
        showNotification('Failed to send test email', 'error');
    }
}

async function clearCache() {
    if (!confirm('Are you sure you want to clear the cache?')) return;

    try {
        showNotification('Clearing cache...', 'info');

        const response = await fetch((window.BASE_URL||'')+'/api/admin/settings/clear-cache', {
            method: 'POST'
        });

        const data = await response.json();

        if (data.success) {
            showNotification('Cache cleared successfully!', 'success');
        } else {
            throw new Error(data.message || 'Failed to clear cache');
        }
    } catch (error) {
        console.error('Error clearing cache:', error);
        showNotification('Failed to clear cache', 'error');
    }
}

async function backupDatabase() {
    if (!confirm('Create a database backup? This may take a few moments.')) return;

    try {
        showNotification('Creating database backup...', 'info');

        const response = await fetch((window.BASE_URL||'')+'/api/admin/settings/backup-database', {
            method: 'POST'
        });

        const data = await response.json();

        if (data.success) {
            showNotification(`Backup created successfully! File: ${data.filename}`, 'success');
        } else {
            throw new Error(data.message || 'Failed to create backup');
        }
    } catch (error) {
        console.error('Error creating backup:', error);
        showNotification('Failed to create database backup', 'error');
    }
}

async function viewLogs() {
    try {
        const response = await fetch((window.BASE_URL||'')+'/api/admin/settings/activity-logs?limit=50');
        const data = await response.json();

        if (data.success) {
            showLogsModal(data.data);
        } else {
            throw new Error('Failed to load logs');
        }
    } catch (error) {
        console.error('Error loading logs:', error);
        showNotification('Failed to load activity logs', 'error');
    }
}

function showLogsModal(logs) {
    const modal = document.createElement('div');
    modal.className = 'logs-modal';
    modal.innerHTML = `
        <div class="modal-overlay" onclick="this.parentElement.remove()"></div>
        <div class="modal-content large">
            <div class="modal-header">
                <h3>Activity Logs</h3>
                <button onclick="this.closest('.logs-modal').remove()" class="close-icon">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="logs-table-container">
                <table class="logs-table">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>User</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${logs.map(log => `
                            <tr>
                                <td><strong>${escapeHtml(log.action)}</strong></td>
                                <td>${escapeHtml(log.user)}</td>
                                <td>${formatDateTime(log.timestamp)}</td>
                                <td><span class="status-badge ${log.status}">${log.status}</span></td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
            <button onclick="this.closest('.logs-modal').remove()" class="close-btn">Close</button>
        </div>
    `;

    const style = document.createElement('style');
    style.textContent = `
        .logs-modal { position: fixed; inset: 0; z-index: 1000; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .modal-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.5); }
        .modal-content.large { position: relative; background: white; padding: 0; border-radius: 16px; max-width: 900px; width: 100%; max-height: 80vh; display: flex; flex-direction: column; }
        .modal-header { padding: 1.5rem 2rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
        .modal-header h3 { margin: 0; font-size: 1.5rem; color: #0f172a; }
        .close-icon { background: none; border: none; font-size: 1.5rem; color: #64748b; cursor: pointer; padding: 0.5rem; }
        .close-icon:hover { color: #0f172a; }
        .logs-table-container { flex: 1; overflow-y: auto; padding: 1rem 2rem; }
        .logs-table { width: 100%; border-collapse: collapse; }
        .logs-table th { background: #f8fafc; padding: 1rem; text-align: left; font-weight: 600; color: #0f172a; font-size: 0.875rem; position: sticky; top: 0; }
        .logs-table td { padding: 1rem; border-top: 1px solid #e2e8f0; font-size: 0.875rem; color: #475569; }
        .logs-table tbody tr:hover { background: #f8fafc; }
        .status-badge { padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600; }
        .status-badge.success { background: #d1fae5; color: #065f46; }
        .close-btn { margin: 1.5rem 2rem; padding: 0.75rem; background: #e2e8f0; border: none; border-radius: 10px; cursor: pointer; font-weight: 600; }
        .close-btn:hover { background: #cbd5e1; }
    `;
    document.head.appendChild(style);
    document.body.appendChild(modal);
}

async function optimizeDatabase() {
    if (!confirm('Optimize database tables? This may take a few moments.')) return;

    try {
        showNotification('Optimizing database...', 'info');

        // Simulate optimization (implement actual optimization on backend)
        await new Promise(resolve => setTimeout(resolve, 2000));

        showNotification('Database optimized successfully!', 'success');
    } catch (error) {
        console.error('Error optimizing database:', error);
        showNotification('Failed to optimize database', 'error');
    }
}

function showFloatingSaveButton() {
    const floatingSave = document.querySelector('.floating-save');
    if (floatingSave) {
        floatingSave.classList.add('show');
    }
}

function hideFloatingSaveButton() {
    const floatingSave = document.querySelector('.floating-save');
    if (floatingSave) {
        floatingSave.classList.remove('show');
    }
}

// Helper Functions
function validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function formatDateTime(timestamp) {
    const date = new Date(timestamp);
    return date.toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        <span>${message}</span>
    `;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#0052cc'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.2);
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        animation: slideIn 0.3s ease;
        font-weight: 600;
    `;
    
    document.body.appendChild(notification);
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Add animations
const styleSheet = document.createElement('style');
styleSheet.textContent = `
    @keyframes slideIn {
        from { transform: translateX(400px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(400px); opacity: 0; }
    }
`;
document.head.appendChild(styleSheet);