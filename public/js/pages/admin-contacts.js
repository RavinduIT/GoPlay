/**
 * Admin Contact Messages Management
 */
document.addEventListener('DOMContentLoaded', () => { loadMessages(); });

let currentStatus = 'all';
let currentPage = 1;
let currentSearch = '';
let currentMessageId = null;
let searchTimer = null;

async function loadMessages(page = 1) {
    currentPage = page;
    try {
        const params = new URLSearchParams({ status: currentStatus, page, search: currentSearch });
        const res = await fetch(`${window.BASE_URL || ""}/api/admin/contacts?${params}`);
        const result = await res.json();

        if (result.success) {
            renderMessages(result.data);
            updateStats(result.stats);
            renderPagination(result.pagination);
        }
    } catch (err) {
        console.error('Load error:', err);
        document.getElementById('messagesContainer').innerHTML = '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>Failed to load messages</p></div>';
    }
}

function renderMessages(messages) {
    const container = document.getElementById('messagesContainer');

    if (!messages.length) {
        container.innerHTML = '<div class="empty-state"><i class="fas fa-inbox"></i><p>No messages found</p></div>';
        return;
    }

    container.innerHTML = messages.map(m => {
        const initials = (m.name || 'U').split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase();
        const timeAgo = getTimeAgo(m.created_at);
        const statusClass = m.status;
        const badgeClass = `badge-${m.status}`;
        const preview = (m.message || '').substring(0, 120);

        return `<div class="message-card ${statusClass}" onclick="viewMessage(${m.id})">
            <div class="message-avatar">${initials}</div>
            <div class="message-body">
                <div class="message-header">
                    <span class="message-sender">${esc(m.name)}</span>
                    <span class="message-time">${timeAgo}</span>
                </div>
                <div class="message-subject">${esc(m.subject)}</div>
                <div class="message-preview">${esc(preview)}</div>
                <div class="message-meta">
                    <span class="message-badge ${badgeClass}">${m.status}</span>
                    <span class="message-email"><i class="fas fa-envelope"></i> ${esc(m.email)}</span>
                    ${m.phone ? `<span class="message-email"><i class="fas fa-phone"></i> ${esc(m.phone)}</span>` : ''}
                </div>
            </div>
        </div>`;
    }).join('');
}

function updateStats(stats) {
    document.getElementById('statTotal').textContent = stats.total || 0;
    document.getElementById('statUnread').textContent = stats.unread || 0;
    document.getElementById('statReplied').textContent = stats.replied || 0;
    document.getElementById('statArchived').textContent = stats.archived || 0;
}

function renderPagination(pagination) {
    const container = document.getElementById('pagination');
    if (!pagination || pagination.total_pages <= 1) { container.innerHTML = ''; return; }

    let html = '';
    html += `<button ${pagination.current_page <= 1 ? 'disabled' : ''} onclick="loadMessages(${pagination.current_page - 1})"><i class="fas fa-chevron-left"></i></button>`;
    for (let i = 1; i <= pagination.total_pages; i++) {
        html += `<button class="${i === pagination.current_page ? 'active' : ''}" onclick="loadMessages(${i})">${i}</button>`;
    }
    html += `<button ${pagination.current_page >= pagination.total_pages ? 'disabled' : ''} onclick="loadMessages(${pagination.current_page + 1})"><i class="fas fa-chevron-right"></i></button>`;
    container.innerHTML = html;
}

function filterMessages(status, btn) {
    currentStatus = status;
    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
    if (btn) {
        btn.classList.add('active');
    } else {
        document.querySelectorAll('.filter-tab').forEach(t => {
            if (t.dataset.status === status || t.textContent.trim().toLowerCase() === status) t.classList.add('active');
        });
    }
    loadMessages(1);
}

function debounceSearch() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        currentSearch = document.getElementById('searchInput').value.trim();
        loadMessages(1);
    }, 400);
}

async function viewMessage(id) {
    currentMessageId = id;
    try {
        const res = await fetch(`${window.BASE_URL || ""}/api/admin/contacts/${id}`);
        const result = await res.json();

        if (!result.success) { showToast(result.message, 'error'); return; }

        const m = result.data;
        const body = document.getElementById('messageDetailBody');

        body.innerHTML = `
            <div class="detail-row">
                <div class="detail-label">From</div>
                <div class="detail-value"><strong>${esc(m.name)}</strong> &lt;${esc(m.email)}&gt; ${m.phone ? `| ${esc(m.phone)}` : ''}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Subject</div>
                <div class="detail-value"><strong>${esc(m.subject)}</strong></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Received</div>
                <div class="detail-value">${new Date(m.created_at).toLocaleString()}</div>
            </div>
            <div class="detail-message">
                <div class="detail-label">Message</div>
                <div class="detail-value">${esc(m.message).replace(/\n/g, '<br>')}</div>
            </div>
            ${m.admin_reply ? `
                <div class="detail-reply">
                    <div class="detail-label"><i class="fas fa-reply"></i> Admin Reply (${m.replied_at ? new Date(m.replied_at).toLocaleString() : ''})</div>
                    <div class="detail-value">${esc(m.admin_reply).replace(/\n/g, '<br>')}</div>
                </div>
            ` : `
                <div class="reply-form">
                    <label for="replyText"><i class="fas fa-reply"></i> Reply to ${esc(m.name)}</label>
                    <textarea id="replyText" placeholder="Type your reply here..."></textarea>
                    <button class="btn btn-primary" onclick="sendReply(${m.id})"><i class="fas fa-paper-plane"></i> Send Reply</button>
                </div>
            `}`;

        document.getElementById('msgModalTitle').textContent = m.subject;
        document.getElementById('messageModal').classList.add('active');

        // Refresh list to update read status
        loadMessages(currentPage);
    } catch (err) {
        showToast('Failed to load message', 'error');
    }
}

async function sendReply(id) {
    const reply = document.getElementById('replyText')?.value?.trim();
    if (!reply) { showToast('Please enter a reply', 'error'); return; }

    try {
        const res = await fetch(`${window.BASE_URL || ""}/api/admin/contacts/${id}/reply`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ reply })
        });
        const result = await res.json();
        if (result.success) {
            showToast('Reply sent successfully', 'success');
            closeModal();
            loadMessages(currentPage);
        } else {
            showToast(result.message, 'error');
        }
    } catch (err) {
        showToast('Failed to send reply', 'error');
    }
}

async function archiveMessage() {
    if (!currentMessageId) return;
    try {
        const res = await fetch(`${window.BASE_URL || ""}/api/admin/contacts/${currentMessageId}/status`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ status: 'archived' })
        });
        const result = await res.json();
        if (result.success) { showToast('Archived', 'success'); closeModal(); loadMessages(currentPage); }
    } catch (err) { showToast('Error', 'error'); }
}

async function deleteMessage() {
    if (!currentMessageId || !confirm('Delete this message permanently?')) return;
    try {
        const res = await fetch(`${window.BASE_URL || ""}/api/admin/contacts/${currentMessageId}`, { method: 'DELETE' });
        const result = await res.json();
        if (result.success) { showToast('Deleted', 'success'); closeModal(); loadMessages(currentPage); }
    } catch (err) { showToast('Error', 'error'); }
}

function closeModal() {
    document.getElementById('messageModal').classList.remove('active');
    currentMessageId = null;
}

function getTimeAgo(dateStr) {
    const diff = Math.floor((Date.now() - new Date(dateStr).getTime()) / 1000);
    if (diff < 60) return 'Just now';
    if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
    if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
    if (diff < 604800) return Math.floor(diff / 86400) + 'd ago';
    return new Date(dateStr).toLocaleDateString();
}

function esc(t) { if (!t) return ''; const d = document.createElement('div'); d.textContent = t; return d.innerHTML; }

function showToast(msg, type) {
    let c = document.querySelector('.toast-container');
    if (!c) { c = document.createElement('div'); c.className = 'toast-container'; document.body.appendChild(c); }
    const icons = { success: 'fa-check-circle', error: 'fa-exclamation-circle' };
    const colors = { success: '#10b981', error: '#ef4444' };
    const t = document.createElement('div'); t.className = `toast ${type}`;
    t.innerHTML = `<i class="fas ${icons[type]||icons.success}" style="color:${colors[type]}"></i><span>${esc(msg)}</span>`;
    c.appendChild(t);
    setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.remove(), 300); }, 4000);
}

document.getElementById('messageModal')?.addEventListener('click', e => { if (e.target.id === 'messageModal') closeModal(); });
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
