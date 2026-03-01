/* Ground Owner – Reviews Dashboard (IIFE) */
(function () {
    'use strict';

    /* ── state ── */
    let allReviews = [];
    let reportTargetId = null;
    let toastTimer = null;

    /* ── DOM refs (resolved after DOMContentLoaded) ── */
    const $ = id => document.getElementById(id);

    /* ================================================================
       BOOT
    ================================================================ */
    document.addEventListener('DOMContentLoaded', () => {
        loadStats();
        loadReviews();
        bindControls();
    });

    /* ================================================================
       API CALLS
    ================================================================ */
    async function loadStats() {
        try {
            const r = await fetch('/api/ground-owner/reviews/stats');
            if (!r.ok) return;
            const data = await r.json();
            const stats = data.stats || data; // support both flat and wrapped
            renderStats(stats);
            renderDistribution(stats);
        } catch (e) {
            console.error('stats error', e);
        }
    }

    async function loadReviews() {
        const list = $('rvList');
        list.innerHTML = '<div class="rv-loading"><div class="rv-spinner"></div><p>Loading reviews…</p></div>';

        try {
            const r = await fetch('/api/ground-owner/reviews');
            if (!r.ok) throw new Error('fetch failed');
            const data = await r.json();
            allReviews = Array.isArray(data.reviews) ? data.reviews : [];
            renderList();
        } catch (e) {
            $('rvList').innerHTML = '<div class="rv-empty"><i class="fas fa-exclamation-circle"></i><p>Could not load reviews. Please refresh.</p></div>';
        }
    }

    async function sendReply(reviewId, text, btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending…';
        try {
            const r = await fetch(`/api/ground-owner/reviews/${reviewId}/reply`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ reply_text: text })
            });
            const data = await r.json();
            if (data.success) {
                // update local copy
                const rev = allReviews.find(rv => rv.id == reviewId);
                if (rev) {
                    rev.reply_id   = 1; // truthy placeholder
                    rev.reply_text = text;
                    rev.replied_at = new Date().toISOString();
                }
                renderList();
                toast('Reply saved!', 'success');
                loadStats(); // refresh counts
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
            const r = await fetch(`/api/ground-owner/reviews/${reviewId}/reply`, { method: 'DELETE' });
            const data = await r.json();
            if (data.success) {
                const rev = allReviews.find(rv => rv.id == reviewId);
                if (rev) { rev.reply_id = null; rev.reply_text = null; rev.replied_at = null; }
                renderList();
                toast('Reply deleted.', 'info');
                loadStats();
            } else {
                toast(data.message || 'Could not delete reply.', 'error');
            }
        } catch (e) {
            toast('Network error.', 'error');
        }
    }

    async function submitReport(reviewId, reason, description) {
        try {
            const r = await fetch(`/api/ground-owner/reviews/${reviewId}/report`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ reason, description })
            });
            const data = await r.json();
            return data;
        } catch (e) {
            return { success: false, message: 'Network error.' };
        }
    }

    /* ================================================================
       RENDER – STATS
    ================================================================ */
    function renderStats(data) {
        const avg = parseFloat(data.average_rating) || 0;
        $('rvAvgRating').textContent = avg ? avg.toFixed(1) : '—';
        $('rvTotalReviews').textContent = data.total_reviews || 0;
        $('rvPendingReplies').textContent = data.pending_replies || 0;
        $('rvRecentCount').textContent = data.recent_count || 0;
    }

    function renderDistribution(data) {
        const avg = parseFloat(data.average_rating) || 0;
        $('rvBigAvg').textContent = avg ? avg.toFixed(1) : '—';

        // star icons
        const starsEl = $('rvBigStars');
        starsEl.innerHTML = '';
        for (let i = 1; i <= 5; i++) {
            const ic = document.createElement('i');
            ic.className = i <= Math.round(avg) ? 'fas fa-star' : 'far fa-star';
            starsEl.appendChild(ic);
        }

        const total = parseInt(data.total_reviews) || 0;
        const counts = {
            5: parseInt(data.five_star)  || 0,
            4: parseInt(data.four_star)  || 0,
            3: parseInt(data.three_star) || 0,
            2: parseInt(data.two_star)   || 0,
            1: parseInt(data.one_star)   || 0
        };

        document.querySelectorAll('.rv-bar-row').forEach(row => {
            const s = parseInt(row.dataset.star);
            const c = counts[s] || 0;
            const pct = total > 0 ? Math.round((c / total) * 100) : 0;
            row.querySelector('.rv-bar-fill').style.width  = pct + '%';
            row.querySelector('.rv-bar-count').textContent = c;
        });
    }

    /* ================================================================
       RENDER – LIST
    ================================================================ */
    function renderList() {
        const list  = $('rvList');
        const query = ($('rvSearch').value || '').toLowerCase().trim();
        const rFilter = $('rvFilterRating').value;
        const replyFilter = $('rvFilterReply').value;
        const sort  = $('rvSort').value;

        let reviews = [...allReviews];

        // filter
        if (query) {
            reviews = reviews.filter(rv =>
                (rv.first_name + ' ' + rv.last_name).toLowerCase().includes(query) ||
                (rv.review_text || '').toLowerCase().includes(query) ||
                (rv.facility_name || '').toLowerCase().includes(query)
            );
        }
        if (rFilter) reviews = reviews.filter(rv => String(rv.rating) === rFilter);
        if (replyFilter === 'replied')  reviews = reviews.filter(rv => rv.reply_id);
        if (replyFilter === 'pending')  reviews = reviews.filter(rv => !rv.reply_id);

        // sort
        reviews.sort((a, b) => {
            if (sort === 'newest')  return new Date(b.created_at) - new Date(a.created_at);
            if (sort === 'oldest')  return new Date(a.created_at) - new Date(b.created_at);
            if (sort === 'highest') return b.rating - a.rating;
            if (sort === 'lowest')  return a.rating - b.rating;
            return 0;
        });

        if (reviews.length === 0) {
            list.innerHTML = '<div class="rv-empty"><i class="fas fa-star-half-alt"></i><p>No reviews match your filters.</p></div>';
            return;
        }

        list.innerHTML = reviews.map(rv => buildCard(rv)).join('');
        attachCardEvents();
    }

    function buildCard(rv) {
        const name    = (rv.first_name || '') + ' ' + (rv.last_name || '');
        const initials = name.trim() ? name.trim().split(' ').map(w => w[0]).join('').toUpperCase().slice(0,2) : '?';
        const avatar  = rv.profile_picture
            ? `<img class="rv-avatar" src="${esc(rv.profile_picture)}" alt="${esc(name)}">`
            : `<div class="rv-avatar-placeholder">${initials}</div>`;

        const stars = [1,2,3,4,5].map(n =>
            `<i class="fas fa-star${n <= rv.rating ? '' : ' inactive'}" style="color:${n <= rv.rating ? '#f59e0b' : '#d1d5db'}"></i>`
        ).join('');

        const date = rv.created_at ? fmtDate(rv.created_at) : '';

        const repliedBadge = rv.reply_id
            ? `<span class="rv-replied-badge"><i class="fas fa-check-circle"></i> Replied</span>`
            : '';

        const existingReply = rv.reply_id ? `
            <div class="rv-existing-reply" data-rid="${rv.id}">
                <div class="rv-reply-header">
                    <span class="rv-reply-who"><i class="fas fa-reply"></i> Your Reply</span>
                    <div class="rv-reply-actions">
                        <span class="rv-reply-date">${rv.replied_at ? fmtDate(rv.replied_at) : ''}</span>
                        <button class="rv-reply-edit-btn" data-id="${rv.id}" data-text="${esc(rv.reply_text || '')}">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="rv-reply-delete-btn" data-id="${rv.id}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="rv-reply-body">${esc(rv.reply_text || '')}</div>
            </div>` : '';

        const triggerLabel = rv.reply_id ? 'Edit Reply' : 'Reply';
        const triggerIcon  = rv.reply_id ? 'fa-edit' : 'fa-reply';

        return `
        <div class="rv-card" data-id="${rv.id}">
            <div class="rv-card-top">
                <div class="rv-card-meta">
                    ${avatar}
                    <div class="rv-card-info">
                        <div class="rv-card-row1">
                            <span class="rv-reviewer-name">${esc(name.trim())}</span>
                            <span class="rv-card-date">${date}</span>
                        </div>
                        <div class="rv-stars">${stars}</div>
                        ${rv.facility_name ? `<span class="rv-facility-tag"><i class="fas fa-map-marker-alt"></i> ${esc(rv.facility_name)}</span>` : ''}
                        ${repliedBadge}
                    </div>
                    <div class="rv-card-actions">
                        <button class="rv-btn-icon rv-flag" title="Report review" data-report-id="${rv.id}">
                            <i class="fas fa-flag"></i>
                        </button>
                    </div>
                </div>
                ${rv.review_text ? `<p class="rv-review-text">${esc(rv.review_text)}</p>` : ''}
            </div>
            ${existingReply}
            <!-- inline composer (hidden) -->
            <div class="rv-reply-composer" id="composer-${rv.id}">
                <div class="rv-composer-inner">
                    <div class="rv-composer-label"><i class="fas fa-reply"></i> ${rv.reply_id ? 'Update your reply' : 'Write a reply'}</div>
                    <textarea class="rv-composer-textarea" id="ta-${rv.id}" placeholder="Write your reply…">${esc(rv.reply_text || '')}</textarea>
                    <div class="rv-composer-footer">
                        <button class="rv-btn-cancel" data-cancel-id="${rv.id}">Cancel</button>
                        <button class="rv-btn-send" data-send-id="${rv.id}"><i class="fas fa-paper-plane"></i> Send Reply</button>
                    </div>
                </div>
            </div>
            <div class="rv-card-footer">
                <button class="rv-reply-trigger" data-trigger-id="${rv.id}">
                    <i class="fas ${triggerIcon}"></i> ${triggerLabel}
                </button>
            </div>
        </div>`;
    }

    /* ================================================================
       CARD EVENTS (delegated after each render)
    ================================================================ */
    function attachCardEvents() {
        const list = $('rvList');

        // single listener using event delegation
        list.onclick = function (e) {
            const btn = e.target.closest('[data-trigger-id]');
            const cancelBtn = e.target.closest('[data-cancel-id]');
            const sendBtn   = e.target.closest('[data-send-id]');
            const reportBtn = e.target.closest('[data-report-id]');
            const editBtn   = e.target.closest('.rv-reply-edit-btn');
            const deleteBtn = e.target.closest('.rv-reply-delete-btn');

            if (btn)    { toggleComposer(btn.dataset.triggerId); return; }
            if (cancelBtn) { closeComposer(cancelBtn.dataset.cancelId); return; }
            if (sendBtn) {
                const id   = sendBtn.dataset.sendId;
                const text = ($('ta-' + id).value || '').trim();
                if (!text) { toast('Please write a reply first.', 'info'); return; }
                sendReply(id, text, sendBtn);
                return;
            }
            if (reportBtn) { openReportModal(reportBtn.dataset.reportId); return; }
            if (editBtn)   { openEditReply(editBtn.dataset.id, editBtn.dataset.text); return; }
            if (deleteBtn) { deleteReply(deleteBtn.dataset.id); return; }
        };
    }

    function toggleComposer(id) {
        const composer = $('composer-' + id);
        const trigger  = document.querySelector(`[data-trigger-id="${id}"]`);
        if (!composer) return;
        const isOpen = composer.classList.contains('open');
        // close all others
        document.querySelectorAll('.rv-reply-composer.open').forEach(el => el.classList.remove('open'));
        document.querySelectorAll('.rv-reply-trigger.active').forEach(el => el.classList.remove('active'));

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
        // pop open composer and pre-fill
        const composer = $('composer-' + id);
        if (!composer) return;
        document.querySelectorAll('.rv-reply-composer.open').forEach(el => el.classList.remove('open'));
        document.querySelectorAll('.rv-reply-trigger.active').forEach(el => el.classList.remove('active'));
        composer.classList.add('open');
        const trigger = document.querySelector(`[data-trigger-id="${id}"]`);
        if (trigger) trigger.classList.add('active');
        const ta = $('ta-' + id);
        if (ta) { ta.value = text || ''; setTimeout(() => { ta.focus(); ta.setSelectionRange(ta.value.length, ta.value.length); }, 50); }
    }

    /* ================================================================
       REPORT MODAL
    ================================================================ */
    function openReportModal(id) {
        reportTargetId = id;
        // remove old modal if any
        const old = document.querySelector('.rv-modal-backdrop');
        if (old) old.remove();

        const backdrop = document.createElement('div');
        backdrop.className = 'rv-modal-backdrop';
        backdrop.innerHTML = `
            <div class="rv-modal" role="dialog" aria-modal="true">
                <div class="rv-modal-header">
                    <h3><i class="fas fa-flag"></i> Report Review</h3>
                    <button class="rv-modal-close" id="rvModalClose"><i class="fas fa-times"></i></button>
                </div>
                <div class="rv-modal-body">
                    <label for="rvReportReason">Reason</label>
                    <select id="rvReportReason">
                        <option value="spam">Spam</option>
                        <option value="offensive">Offensive</option>
                        <option value="inappropriate">Inappropriate</option>
                        <option value="fake">Fake review</option>
                        <option value="other">Other</option>
                    </select>
                    <label for="rvReportDesc">Description (optional)</label>
                    <textarea id="rvReportDesc" placeholder="Describe the issue…"></textarea>
                </div>
                <div class="rv-modal-footer">
                    <button class="rv-btn-cancel" id="rvModalCancelBtn">Cancel</button>
                    <button class="rv-btn-report" id="rvModalSubmitBtn"><i class="fas fa-flag"></i> Submit Report</button>
                </div>
            </div>`;

        document.body.appendChild(backdrop);

        const close = () => backdrop.remove();
        $('rvModalClose').onclick      = close;
        $('rvModalCancelBtn').onclick  = close;
        backdrop.onclick = e => { if (e.target === backdrop) close(); };

        $('rvModalSubmitBtn').onclick = async function () {
            const reason = $('rvReportReason').value;
            const desc   = ($('rvReportDesc').value || '').trim();
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting…';
            const result = await submitReport(reportTargetId, reason, desc);
            if (result.success) {
                close();
                toast('Report submitted. Thank you.', 'success');
            } else {
                toast(result.message || 'Could not submit report.', 'error');
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-flag"></i> Submit Report';
            }
        };
    }

    /* ================================================================
       CONTROLS
    ================================================================ */
    function bindControls() {
        let debounce;
        $('rvSearch').addEventListener('input', () => {
            clearTimeout(debounce);
            debounce = setTimeout(renderList, 220);
        });
        $('rvFilterRating').addEventListener('change', renderList);
        $('rvFilterReply').addEventListener('change', renderList);
        $('rvSort').addEventListener('change', renderList);
    }

    /* ================================================================
       TOAST
    ================================================================ */
    function toast(msg, type = 'info') {
        const el = $('rvToast');
        if (!el) return;
        clearTimeout(toastTimer);
        el.textContent = msg;
        el.className   = `rv-toast ${type} show`;
        toastTimer = setTimeout(() => { el.classList.remove('show'); }, 3200);
    }

    /* ================================================================
       HELPERS
    ================================================================ */
    function fmtDate(iso) {
        if (!iso) return '';
        const d = new Date(iso);
        return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
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
