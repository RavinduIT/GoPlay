<?php $_base = defined('BASE_URL') ? BASE_URL : ''; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Reviews – Coach Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $_base ?>/public/css/coach/sidebar.css">
    <link rel="stylesheet" href="<?= $_base ?>/public/css/pages/coach-reviews.css">
</head>
<body>

<div class="coach-dashboard">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="main-content">

        <!--  Page Header  -->
        <div class="page-header">
            <div class="header-left">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h1 class="page-title"><i class="fas fa-star"></i> Client Reviews</h1>
                    <p class="page-subtitle">View and respond to feedback from your clients</p>
                </div>
            </div>
            <div>
                <button class="btn btn-secondary" onclick="doExport()">
                    <i class="fas fa-download"></i> Export CSV
                </button>
            </div>
        </div>

        <div class="reviews-content">

            <!--  Stats Row  -->
            <div class="review-stats-row" id="statsRow">
                <!-- Filled by JS -->
                <div class="overall-card">
                    <div class="skel-card" style="width:80px;height:60px;border-radius:8px"></div>
                </div>
                <div class="breakdown-card">
                    <div class="skel-card" style="height:140px"></div>
                </div>
            </div>

            <!--  Controls  -->
            <div class="controls-row">
                <div class="filter-tabs" id="filterTabs">
                    <button class="ftab active" data-filter="all">All <span class="tab-count" id="cnt-all">—</span></button>
                    <button class="ftab" data-filter="5"> 5 <span class="tab-count" id="cnt-5">—</span></button>
                    <button class="ftab" data-filter="4"> 4 <span class="tab-count" id="cnt-4">—</span></button>
                    <button class="ftab" data-filter="3"> 3 <span class="tab-count" id="cnt-3">—</span></button>
                    <button class="ftab" data-filter="2"> 2 <span class="tab-count" id="cnt-2">—</span></button>
                    <button class="ftab" data-filter="1"> 1 <span class="tab-count" id="cnt-1">—</span></button>
                    <button class="ftab" data-filter="no_reply">No Reply</button>
                </div>
                <div class="ctrl-spacer"></div>
                <div class="search-wrap">
                    <i class="fas fa-search"></i>
                    <input type="text" class="search-input" id="searchInput" placeholder="Search reviews…">
                </div>
                <select class="sort-select" id="sortSelect">
                    <option value="newest">Newest first</option>
                    <option value="oldest">Oldest first</option>
                    <option value="highest">Highest rating</option>
                    <option value="lowest">Lowest rating</option>
                </select>
            </div>

            <!--  Reviews List  -->
            <div id="reviewsList">
                <div class="reviews-list">
                    <?php for ($i = 0; $i < 4; $i++): ?>
                    <div class="skel-card"></div>
                    <?php endfor; ?>
                </div>
            </div>

            <!--  Pagination  -->
            <div class="pagination" id="pagination" style="display:none">
                <button class="pg-btn" id="pgPrev" disabled><i class="fas fa-chevron-left"></i></button>
                <span class="pg-info" id="pgInfo">Page 1 of 1</span>
                <button class="pg-btn" id="pgNext" disabled><i class="fas fa-chevron-right"></i></button>
            </div>

        </div>
    </div>
</div>

<!--  Reply Modal  -->
<div class="modal-backdrop" id="replyBackdrop"></div>
<div class="modal" id="replyModal">
    <div class="modal-head">
        <h3><i class="fas fa-reply"></i> <span id="replyModalTitle">Add Reply</span></h3>
        <button class="modal-close-btn" onclick="closeReplyModal()"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
        <div class="modal-review-preview" id="replyPreview">
            <strong>Client Review</strong>
            <span id="previewText"></span>
        </div>
        <label class="modal-label" for="replyTextarea">Your Reply</label>
        <textarea class="reply-textarea" id="replyTextarea" maxlength="1000" placeholder="Write a professional, helpful reply…"></textarea>
        <div class="char-count"><span id="charCount">0</span> / 1000</div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" onclick="closeReplyModal()">Cancel</button>
        <button class="btn btn-primary" id="submitReplyBtn" onclick="submitReply()">
            <i class="fas fa-paper-plane"></i> Save Reply
        </button>
    </div>
</div>

<!-- Toast -->
<div class="toast-wrap" id="toastWrap"></div>

<script>
// 
//  State
// 
let reviewStats   = {};
let currentFilter = 'all';
let currentSort   = 'newest';
let currentSearch = '';
let currentPage   = 1;
let totalPages    = 1;
let replyTarget   = null;   // { reviewId, hasReply }

// 
//  Init
// 
document.addEventListener('DOMContentLoaded', () => {
    loadStats();
    loadReviews();

    // Sidebar toggle
    const st = document.getElementById('sidebarToggle');
    const sb = document.querySelector('.dashboard-sidebar');
    if (st && sb) st.addEventListener('click', () => sb.classList.toggle('collapsed'));

    // Filter tabs
    document.getElementById('filterTabs').addEventListener('click', e => {
        const btn = e.target.closest('.ftab');
        if (!btn) return;
        document.querySelectorAll('.ftab').forEach(t => t.classList.remove('active'));
        btn.classList.add('active');
        currentFilter = btn.dataset.filter;
        currentPage   = 1;
        loadReviews();
    });

    // Sort
    document.getElementById('sortSelect').addEventListener('change', e => {
        currentSort = e.target.value;
        currentPage = 1;
        loadReviews();
    });

    // Search (debounced)
    let searchTimer;
    document.getElementById('searchInput').addEventListener('input', e => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            currentSearch = e.target.value.trim();
            currentPage   = 1;
            loadReviews();
        }, 350);
    });

    // Pagination
    document.getElementById('pgPrev').addEventListener('click', () => { currentPage--; loadReviews(); });
    document.getElementById('pgNext').addEventListener('click', () => { currentPage++; loadReviews(); });

    // Reply textarea char count
    document.getElementById('replyTextarea').addEventListener('input', e => {
        document.getElementById('charCount').textContent = e.target.value.length;
    });

    // Close modal on backdrop click
    document.getElementById('replyBackdrop').addEventListener('click', closeReplyModal);
});

// 
//  Load stats
// 
async function loadStats() {
    try {
        const res  = await fetch((window.BASE_URL||'')+'/api/coach/reviews/stats');
        const data = await res.json();
        if (!data.success) return;
        reviewStats = data.stats;
        renderStats(data.stats);
    } catch (err) {
        console.error(err);
    }
}

function renderStats(s) {
    const total = parseInt(s.total) || 0;
    const avg   = parseFloat(s.avg_rating) || 0;

    // Fill filter counts
    document.getElementById('cnt-all').textContent = total;
    document.getElementById('cnt-5').textContent   = s.r5 || 0;
    document.getElementById('cnt-4').textContent   = s.r4 || 0;
    document.getElementById('cnt-3').textContent   = s.r3 || 0;
    document.getElementById('cnt-2').textContent   = s.r2 || 0;
    document.getElementById('cnt-1').textContent   = s.r1 || 0;

    const monthDiff = (parseInt(s.this_month) || 0) - (parseInt(s.last_month) || 0);

    document.getElementById('statsRow').innerHTML = `
    <div class="overall-card">
        <div class="overall-score">${avg.toFixed(1)}</div>
        <div class="overall-stars">${starsHtml(avg, 22)}</div>
        <div class="overall-total">${total} review${total !== 1 ? 's' : ''}</div>
        ${monthDiff >= 0
            ? `<div class="overall-month"><i class="fas fa-arrow-trend-up"></i>+${monthDiff} this month</div>`
            : `<div class="overall-month" style="color:var(--danger);background:rgba(239,68,68,.1)"><i class="fas fa-arrow-trend-down"></i>${monthDiff} this month</div>`
        }
    </div>
    <div class="breakdown-card">
        <h3><i class="fas fa-chart-bar"></i> Rating Breakdown</h3>
        ${[5,4,3,2,1].map(n => {
            const cnt  = parseInt(s['r' + n]) || 0;
            const pct  = total > 0 ? Math.round(cnt / total * 100) : 0;
            return `
            <div class="rating-bar">
                <div class="bar-label">${n} <i class="fas fa-star"></i></div>
                <div class="bar-track"><div class="bar-fill" style="width:${pct}%"></div></div>
                <div class="bar-count">${cnt}</div>
            </div>`;
        }).join('')}
    </div>`;
}

// 
//  Load & render reviews
// 
async function loadReviews() {
    document.getElementById('reviewsList').innerHTML =
        `<div class="reviews-list">${'<div class="skel-card"></div>'.repeat(3)}</div>`;
    document.getElementById('pagination').style.display = 'none';

    const params = new URLSearchParams({
        sort:  currentSort,
        page:  currentPage,
        limit: 10,
    });
    if (currentFilter !== 'all' && currentFilter !== 'no_reply') {
        params.set('rating', currentFilter);
    }
    if (currentFilter === 'no_reply') {
        params.set('no_reply', '1');
    }
    if (currentSearch) params.set('search', currentSearch);

    try {
        const res  = await fetch((window.BASE_URL||'')+'/api/coach/reviews?' + params);
        const data = await res.json();
        if (!data.success) throw new Error(data.message);

        totalPages = data.totalPages || 1;
        renderReviews(data.data || [], data.total || 0);
        updatePagination(data.page, totalPages);
    } catch (err) {
        console.error(err);
        document.getElementById('reviewsList').innerHTML =
            `<div class="empty-state"><i class="fas fa-exclamation-circle"></i><h3>Failed to load</h3><p>Please refresh and try again.</p></div>`;
    }
}

function renderReviews(reviews, total) {
    const container = document.getElementById('reviewsList');

    if (!reviews.length) {
        container.innerHTML = `
        <div class="empty-state">
            <i class="fas fa-star"></i>
            <h3>No Reviews Found</h3>
            <p>No reviews match your current filters. Try adjusting your search or filter.</p>
        </div>`;
        return;
    }

    container.innerHTML = `<div class="reviews-list">${reviews.map(buildCard).join('')}</div>`;
}

function buildCard(r) {
    const initials  = (r.first_name[0] + r.last_name[0]).toUpperCase();
    const hasReply  = !!r.reply_id;
    const sessionPill = r.session_type
        ? `<span class="rev-session-pill">${cap(r.session_type)}</span>` : '';

    const replySection = hasReply ? `
    <div class="reply-block" id="replyBlock-${r.id}">
        <div class="reply-block-header">
            <div class="reply-by"><i class="fas fa-user-shield"></i> Your Reply
                ${r.reply_updated_at && r.reply_updated_at !== r.reply_at
                    ? '<span style="color:var(--text-3);font-weight:400">(edited)</span>' : ''}
            </div>
            <div style="display:flex;align-items:center;gap:12px">
                <span class="reply-date">${fmtDate(r.reply_at)}</span>
                <div class="reply-actions">
                    <button class="reply-act-btn reply-edit-btn" onclick="openReplyModal(${r.id}, true, ${JSON.stringify(r.review_text).replace(/"/g,'&quot;')}, ${JSON.stringify(r.reply_text).replace(/"/g,'&quot;')})">
                        <i class="fas fa-pen"></i> Edit
                    </button>
                    <button class="reply-act-btn reply-delete-btn" onclick="confirmDeleteReply(${r.id})">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </div>
        </div>
        <div class="reply-text" id="replyText-${r.id}">${esc(r.reply_text)}</div>
    </div>` : '';

    return `
    <div class="review-card" id="rev-${r.id}">
        <div class="rev-header">
            <div class="rev-avatar">${initials}</div>
            <div class="rev-meta">
                <div class="rev-name">
                    ${esc(r.first_name + ' ' + r.last_name)}
                    <i class="fas fa-circle-check verified" title="Verified client"></i>
                </div>
                <div class="rev-sub">
                    <div class="rev-stars">${starsHtml(r.rating, 13)}</div>
                    <span class="rev-rating-num">${r.rating}/5</span>
                    <span class="rev-date">${fmtDate(r.created_at)}</span>
                    ${sessionPill}
                </div>
            </div>
        </div>

        <p class="rev-text">${esc(r.review_text || '')}</p>

        ${replySection}

        <div class="rev-footer">
            <span style="font-size:12px;color:var(--text-3)">Review #${r.id}</span>
            <div class="rev-actions">
                ${hasReply
                    ? ''
                    : `<button class="btn btn-outline btn-sm" onclick="openReplyModal(${r.id}, false, ${JSON.stringify(r.review_text).replace(/"/g,'&quot;')}, '')">
                        <i class="fas fa-reply"></i> Reply
                       </button>`
                }
            </div>
        </div>
    </div>`;
}

// 
//  Pagination
// 
function updatePagination(page, total) {
    const pg = document.getElementById('pagination');
    if (total <= 1) { pg.style.display = 'none'; return; }
    pg.style.display = 'flex';
    document.getElementById('pgInfo').textContent = `Page ${page} of ${total}`;
    document.getElementById('pgPrev').disabled = page <= 1;
    document.getElementById('pgNext').disabled = page >= total;
}

// 
//  Reply modal
// 
function openReplyModal(reviewId, isEdit, reviewText, existingReply) {
    replyTarget = { reviewId, hasReply: isEdit };

    document.getElementById('replyModalTitle').textContent = isEdit ? 'Edit Reply' : 'Add Reply';
    document.getElementById('previewText').textContent     = reviewText;
    document.getElementById('replyTextarea').value         = existingReply || '';
    document.getElementById('charCount').textContent       = (existingReply || '').length;

    document.getElementById('replyBackdrop').classList.add('open');
    document.getElementById('replyModal').classList.add('open');
    setTimeout(() => document.getElementById('replyTextarea').focus(), 150);
}

function closeReplyModal() {
    document.getElementById('replyBackdrop').classList.remove('open');
    document.getElementById('replyModal').classList.remove('open');
    replyTarget = null;
}

async function submitReply() {
    if (!replyTarget) return;
    const text = document.getElementById('replyTextarea').value.trim();
    if (!text) { toast('Please enter a reply.', 'error'); return; }

    const btn = document.getElementById('submitReplyBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving…';

    try {
        const res  = await fetch(`${window.BASE_URL||""}/api/coach/reviews/${replyTarget.reviewId}/reply`, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ reply_text: text }),
        });
        const data = await res.json();
        if (data.success) {
            toast('Reply saved!', 'success');
            closeReplyModal();
            loadReviews();
        } else {
            toast(data.message || 'Failed to save.', 'error');
        }
    } catch (err) {
        toast('Network error.', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Save Reply';
    }
}

async function confirmDeleteReply(reviewId) {
    if (!confirm('Delete your reply to this review?')) return;
    try {
        const res  = await fetch(`${window.BASE_URL||""}/api/coach/reviews/${reviewId}/reply`, { method: 'DELETE' });
        const data = await res.json();
        if (data.success) {
            toast('Reply deleted.', 'success');
            loadReviews();
        } else {
            toast(data.message || 'Failed to delete.', 'error');
        }
    } catch (err) {
        toast('Network error.', 'error');
    }
}

// 
//  Export
// 
function doExport() {
    window.location.href=(window.BASE_URL||'')+'/api/coach/reviews/export';
}

// 
//  Helpers
// 
function starsHtml(rating, size) {
    const full  = Math.floor(rating);
    const half  = rating - full >= 0.5;
    let html = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= full)          html += `<i class="fas fa-star" style="font-size:${size}px;color:var(--gold)"></i>`;
        else if (i === full + 1 && half) html += `<i class="fas fa-star-half-stroke" style="font-size:${size}px;color:var(--gold)"></i>`;
        else                    html += `<i class="fas fa-star empty" style="font-size:${size}px;color:var(--border)"></i>`;
    }
    return html;
}

function fmtDate(dt) {
    if (!dt) return '';
    return new Date(dt).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

function cap(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }

function esc(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
                    .replace(/"/g,'&quot;').replace(/'/g,'&#039;');
}

function toast(msg, type = 'success') {
    const wrap = document.getElementById('toastWrap');
    const el   = document.createElement('div');
    el.className = `toast toast-${type}`;
    el.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${msg}`;
    wrap.appendChild(el);
    setTimeout(() => el.remove(), 3500);
}
</script>

</body>
</html>
