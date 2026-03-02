/* Shop Owner – Reviews Dashboard (IIFE) */
(function () {
    'use strict';

    /* ── state ── */
    let allReviews = [];
    let toastTimer = null;

    /* ── DOM ref helper ── */
    const $ = id => document.getElementById(id);

    /* ================================================================
       BOOT
    ================================================================ */
    document.addEventListener('DOMContentLoaded', () => {
        loadStats();
        loadReviews();
        bindControls();

        // Auto-dismiss PHP session alerts
        document.querySelectorAll('.sr-alert').forEach(el => {
            setTimeout(() => {
                el.style.transition = 'opacity .3s';
                el.style.opacity    = '0';
                setTimeout(() => el.remove(), 300);
            }, 5000);
        });
    });

    /* ================================================================
       API CALLS
    ================================================================ */
    async function loadStats() {
        try {
            const r = await fetch('/api/shop-owner/reviews', { credentials: 'same-origin' });
            if (!r.ok) return;
            const data = await r.json();
            if (data.stats) renderStats(data.stats);
        } catch (e) { /* silent */ }
    }

    async function loadReviews() {
        $('srList').innerHTML = '<div class="sr-loading"><div class="sr-spinner"></div><p>Loading reviews…</p></div>';
        try {
            const r = await fetch('/api/shop-owner/reviews', { credentials: 'same-origin' });
            if (!r.ok) throw new Error('fetch failed');
            const data = await r.json();
            allReviews = Array.isArray(data.reviews) ? data.reviews : [];
            if (data.stats) {
                renderStats(data.stats);
                renderDistribution(data.stats);
            }
            renderList();
        } catch (e) {
            $('srList').innerHTML = '<div class="sr-empty"><i class="fas fa-exclamation-circle"></i><p>Could not load reviews. Please refresh.</p></div>';
        }
    }

    async function sendReply(reviewId, text, btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending…';
        try {
            const r = await fetch(`/api/shop-owner/reviews/${reviewId}/reply`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ reply_text: text }),
            });
            const data = await r.json();
            if (data.success) {
                const rev = allReviews.find(rv => rv.id == reviewId);
                if (rev) {
                    rev.owner_reply    = text;
                    rev.owner_reply_at = new Date().toISOString();
                }
                renderList();
                loadStats();
                toast('Reply saved!', 'success');
            } else {
                toast(data.message || 'Could not save reply.', 'error');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Reply';
            }
        } catch (e) {
            toast('Network error. Please try again.', 'error');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Reply';
        }
    }

    async function deleteReply(reviewId) {
        if (!confirm('Delete your reply?')) return;
        try {
            const r = await fetch(`/api/shop-owner/reviews/${reviewId}/reply`, {
                method: 'DELETE',
                credentials: 'same-origin',
            });
            const data = await r.json();
            if (data.success) {
                const rev = allReviews.find(rv => rv.id == reviewId);
                if (rev) { rev.owner_reply = null; rev.owner_reply_at = null; }
                renderList();
                loadStats();
                toast('Reply deleted.', 'info');
            } else {
                toast(data.message || 'Could not delete reply.', 'error');
            }
        } catch (e) {
            toast('Network error.', 'error');
        }
    }

    /* ================================================================
       RENDER – STATS + DISTRIBUTION
    ================================================================ */
    function renderStats(data) {
        const avg = parseFloat(data.avg_rating) || 0;
        $('srAvgRating').textContent    = avg ? avg.toFixed(1) : '—';
        $('srTotalReviews').textContent  = data.total_reviews  || 0;
        $('srPendingReplies').textContent = data.pending_replies || 0;
        $('srRecentCount').textContent   = data.recent_count   || 0;
    }

    function renderDistribution(data) {
        const avg = parseFloat(data.avg_rating) || 0;
        $('srBigAvg').textContent = avg ? avg.toFixed(1) : '—';

        // star icons
        const starsEl = $('srBigStars');
        starsEl.innerHTML = '';
        for (let i = 1; i <= 5; i++) {
            const ic = document.createElement('i');
            ic.className = i <= Math.round(avg) ? 'fas fa-star' : 'far fa-star';
            starsEl.appendChild(ic);
        }

        const total  = parseInt(data.total_reviews) || 0;
        const counts = {
            5: parseInt(data.five_star)  || 0,
            4: parseInt(data.four_star)  || 0,
            3: parseInt(data.three_star) || 0,
            2: parseInt(data.two_star)   || 0,
            1: parseInt(data.one_star)   || 0,
        };

        document.querySelectorAll('.sr-bar-row').forEach(row => {
            const s   = parseInt(row.dataset.star);
            const c   = counts[s] || 0;
            const pct = total > 0 ? Math.round((c / total) * 100) : 0;
            row.querySelector('.sr-bar-fill').style.width  = pct + '%';
            row.querySelector('.sr-bar-count').textContent = c;
        });
    }

    /* ================================================================
       RENDER – LIST
    ================================================================ */
    function renderList() {
        const list        = $('srList');
        const query       = ($('srSearch').value || '').toLowerCase().trim();
        const rFilter     = $('srFilterRating').value;
        const replyFilter = $('srFilterReply').value;
        const sort        = $('srSort').value;

        let reviews = [...allReviews];

        // filter
        if (query) {
            reviews = reviews.filter(rv =>
                (rv.customer_name  || '').toLowerCase().includes(query) ||
                (rv.product_name   || '').toLowerCase().includes(query) ||
                (rv.review_text    || '').toLowerCase().includes(query)
            );
        }
        if (rFilter) reviews = reviews.filter(rv => String(rv.rating) === rFilter);
        if (replyFilter === 'replied') reviews = reviews.filter(rv => rv.owner_reply && rv.owner_reply.trim() !== '');
        if (replyFilter === 'pending') reviews = reviews.filter(rv => !rv.owner_reply || rv.owner_reply.trim() === '');

        // sort
        reviews.sort((a, b) => {
            if (sort === 'newest')  return new Date(b.created_at) - new Date(a.created_at);
            if (sort === 'oldest')  return new Date(a.created_at) - new Date(b.created_at);
            if (sort === 'highest') return b.rating - a.rating;
            if (sort === 'lowest')  return a.rating - b.rating;
            return 0;
        });

        if (reviews.length === 0) {
            list.innerHTML = '<div class="sr-empty"><i class="fas fa-star-half-alt"></i><p>No reviews match your filters.</p></div>';
            return;
        }

        list.innerHTML = reviews.map(rv => buildCard(rv)).join('');
        attachCardEvents();
    }

    /* ================================================================
       BUILD CARD
    ================================================================ */
    function buildCard(rv) {
        const name     = esc(rv.customer_name || 'Anonymous');
        const initial  = name.charAt(0).toUpperCase();
        const hasReply = rv.owner_reply && rv.owner_reply.trim() !== '';

        const stars = [1,2,3,4,5].map(n =>
            `<i class="fas fa-star${n <= rv.rating ? ' active' : ''}"></i>`
        ).join('');

        const date = rv.created_at ? fmtDate(rv.created_at) : '';

        const productTag = rv.product_name
            ? `<a class="sr-product-tag" href="/product/${rv.product_id}" target="_blank"><i class="fas fa-tag"></i> ${esc(rv.product_name)}</a>`
            : '';

        const verifiedBadge = rv.is_verified_purchase
            ? `<span class="sr-verified-badge"><i class="fas fa-check-circle"></i> Verified</span>`
            : '';

        const repliedBadge = hasReply
            ? `<span class="sr-replied-badge"><i class="fas fa-check-circle"></i> Replied</span>`
            : '';

        const existingReply = hasReply ? `
            <div class="sr-existing-reply" data-rid="${rv.id}">
                <div class="sr-reply-header">
                    <span class="sr-reply-who"><i class="fas fa-reply"></i> Your Reply</span>
                    <div class="sr-reply-actions">
                        <span class="sr-reply-date">${rv.owner_reply_at ? fmtDate(rv.owner_reply_at) : ''}</span>
                        <button class="sr-reply-edit-btn" data-id="${rv.id}" data-text="${esc(rv.owner_reply || '')}">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="sr-reply-delete-btn" data-id="${rv.id}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="sr-reply-body">${esc(rv.owner_reply || '')}</div>
            </div>` : '';

        const triggerLabel = hasReply ? 'Edit Reply' : 'Reply';
        const triggerIcon  = hasReply ? 'fa-edit'   : 'fa-reply';

        return `
        <div class="sr-card" data-id="${rv.id}">
            <div class="sr-card-top">
                <div class="sr-card-meta">
                    <div class="sr-avatar-placeholder">${initial}</div>
                    <div class="sr-card-info">
                        <div class="sr-card-row1">
                            <span class="sr-reviewer-name">${name}</span>
                            <span class="sr-card-date">${date}</span>
                        </div>
                        <div class="sr-stars">${stars}</div>
                        ${productTag}${verifiedBadge}
                        ${repliedBadge}
                    </div>
                    <div class="sr-card-actions">
                        <button class="sr-btn-icon sr-del" title="Delete review" data-delete-id="${rv.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                ${rv.title      ? `<p class="sr-review-title">${esc(rv.title)}</p>` : ''}
                ${rv.review_text ? `<p class="sr-review-text">${esc(rv.review_text)}</p>` : ''}
            </div>
            ${existingReply}
            <!-- inline composer (hidden) -->
            <div class="sr-reply-composer" id="composer-${rv.id}">
                <div class="sr-composer-inner">
                    <div class="sr-composer-label"><i class="fas fa-reply"></i> ${hasReply ? 'Update your reply' : 'Write a reply'}</div>
                    <textarea class="sr-composer-textarea" id="ta-${rv.id}" placeholder="Write your reply…" maxlength="1000">${esc(rv.owner_reply || '')}</textarea>
                    <div class="sr-composer-footer">
                        <button class="sr-btn-cancel" data-cancel-id="${rv.id}">Cancel</button>
                        <button class="sr-btn-send" data-send-id="${rv.id}"><i class="fas fa-paper-plane"></i> Send Reply</button>
                    </div>
                </div>
            </div>
            <div class="sr-card-footer">
                <button class="sr-reply-trigger" data-trigger-id="${rv.id}">
                    <i class="fas ${triggerIcon}"></i> ${triggerLabel}
                </button>
            </div>
        </div>`;
    }

    /* ================================================================
       CARD EVENTS (delegated after each render)
    ================================================================ */
    function attachCardEvents() {
        const list = $('srList');

        list.onclick = function (e) {
            const trigger    = e.target.closest('[data-trigger-id]');
            const cancelBtn  = e.target.closest('[data-cancel-id]');
            const sendBtn    = e.target.closest('[data-send-id]');
            const deleteBtn  = e.target.closest('[data-delete-id]');
            const editBtn    = e.target.closest('.sr-reply-edit-btn');
            const replyDelBtn = e.target.closest('.sr-reply-delete-btn');

            if (trigger)    { toggleComposer(trigger.dataset.triggerId);        return; }
            if (cancelBtn)  { closeComposer(cancelBtn.dataset.cancelId);        return; }
            if (sendBtn) {
                const id   = sendBtn.dataset.sendId;
                const text = ($('ta-' + id)?.value || '').trim();
                if (!text) { toast('Please write a reply first.', 'info'); return; }
                sendReply(id, text, sendBtn);
                return;
            }
            if (deleteBtn)  { deleteReview(deleteBtn.dataset.deleteId);         return; }
            if (editBtn)    { openEditReply(editBtn.dataset.id, editBtn.dataset.text); return; }
            if (replyDelBtn){ deleteReply(replyDelBtn.dataset.id);              return; }
        };
    }

    function toggleComposer(id) {
        const composer = $('composer-' + id);
        const trigger  = document.querySelector(`[data-trigger-id="${id}"]`);
        if (!composer) return;
        const isOpen = composer.classList.contains('open');
        document.querySelectorAll('.sr-reply-composer.open').forEach(el => el.classList.remove('open'));
        document.querySelectorAll('.sr-reply-trigger.active').forEach(el => el.classList.remove('active'));
        if (!isOpen) {
            composer.classList.add('open');
            if (trigger) trigger.classList.add('active');
            const ta = $('ta-' + id);
            if (ta) setTimeout(() => ta.focus(), 50);
        }
    }

    function closeComposer(id) {
        const composer = $('composer-' + id);
        const trigger  = document.querySelector(`[data-trigger-id="${id}"]`);
        if (composer) composer.classList.remove('open');
        if (trigger)  trigger.classList.remove('active');
    }

    function openEditReply(id, text) {
        const composer = $('composer-' + id);
        if (!composer) return;
        document.querySelectorAll('.sr-reply-composer.open').forEach(el => el.classList.remove('open'));
        document.querySelectorAll('.sr-reply-trigger.active').forEach(el => el.classList.remove('active'));
        composer.classList.add('open');
        const trigger = document.querySelector(`[data-trigger-id="${id}"]`);
        if (trigger) trigger.classList.add('active');
        const ta = $('ta-' + id);
        if (ta) {
            ta.value = text || '';
            setTimeout(() => { ta.focus(); ta.setSelectionRange(ta.value.length, ta.value.length); }, 50);
        }
    }

    /* ================================================================
       DELETE REVIEW (traditional form POST)
    ================================================================ */
    function deleteReview(reviewId) {
        if (!confirm('Delete this review permanently? This cannot be undone.')) return;
        const form   = document.createElement('form');
        form.method  = 'POST';
        form.action  = '/shop-owner/reviews/delete';
        const input  = document.createElement('input');
        input.type   = 'hidden';
        input.name   = 'review_id';
        input.value  = reviewId;
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }

    /* ================================================================
       CONTROLS
    ================================================================ */
    function bindControls() {
        let debounce;
        $('srSearch').addEventListener('input', () => {
            clearTimeout(debounce);
            debounce = setTimeout(renderList, 220);
        });
        $('srFilterRating').addEventListener('change', renderList);
        $('srFilterReply').addEventListener('change', renderList);
        $('srSort').addEventListener('change', renderList);
    }

    /* ================================================================
       TOAST
    ================================================================ */
    function toast(msg, type = 'info') {
        const el = $('srToast');
        if (!el) return;
        clearTimeout(toastTimer);
        el.textContent = msg;
        el.className   = `sr-toast ${type} show`;
        toastTimer = setTimeout(() => { el.classList.remove('show'); }, 3200);
    }

    /* ================================================================
       HELPERS
    ================================================================ */
    function fmtDate(iso) {
        if (!iso) return '';
        return new Date(iso).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    }

    function esc(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

})();
