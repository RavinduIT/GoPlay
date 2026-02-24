<?php
$title         = 'Coach Profile - GoPlay Sports Platform';
$additionalCSS = [];
$additionalJS  = [];
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
:root {
    --primary:   #2563eb;
    --primary-dk:#1d4ed8;
    --accent:    #0891b2;
    --warn:      #f59e0b;
    --danger:    #ef4444;
    --success:   #10b981;
    --bg:        #f1f5f9;
    --card:      #ffffff;
    --border:    #e2e8f0;
    --txt:       #1e293b;
    --txt-2:     #64748b;
    --radius:    14px;
    --shadow:    0 4px 20px rgba(0,0,0,.08);
    --transition:all .25s ease;
}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;color:var(--txt);background:var(--bg)}

/* ── Breadcrumb ───────────────────────────── */
.breadcrumb{background:#1e3a8a;color:#fff;padding:.9rem 2rem}
.breadcrumb a{color:rgba(255,255,255,.75);text-decoration:none;font-size:.9rem}
.breadcrumb a:hover{color:#fff}
.breadcrumb span{color:#fff;font-size:.9rem}

/* ── Page Layout ──────────────────────────── */
.profile-page{max-width:1200px;margin:2rem auto;padding:0 1.5rem;display:grid;grid-template-columns:1fr 380px;gap:2rem}
@media(max-width:900px){.profile-page{grid-template-columns:1fr}}

/* ── Coach Info Card ──────────────────────── */
.coach-card{background:var(--card);border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden}
.coach-hero{background:linear-gradient(135deg,#1e3a8a,#2563eb);padding:2.5rem 2rem;text-align:center;color:#fff}
.coach-avatar-wrap{position:relative;display:inline-block;margin-bottom:1rem}
.coach-avatar{width:120px;height:120px;border-radius:50%;object-fit:cover;border:4px solid #fff;box-shadow:0 4px 16px rgba(0,0,0,.3)}
.coach-avatar-placeholder{width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:3rem;border:4px solid #fff;margin:0 auto}
.coach-badge{position:absolute;bottom:4px;right:4px;background:#10b981;color:#fff;border-radius:50%;width:26px;height:26px;display:flex;align-items:center;justify-content:center;font-size:.7rem;border:2px solid #fff}
.coach-name{font-size:1.8rem;font-weight:700;margin-bottom:.4rem}
.sport-chip{display:inline-block;background:rgba(255,255,255,.2);padding:.3rem .9rem;border-radius:2rem;font-size:.85rem;backdrop-filter:blur(4px)}
.rating-row{display:flex;align-items:center;justify-content:center;gap:.5rem;margin-top:.8rem}
.stars{color:var(--warn)}
.review-count{font-size:.85rem;opacity:.8}

.coach-body{padding:1.8rem}
.stat-row{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.8rem}
.stat-box{text-align:center;background:var(--bg);border-radius:10px;padding:1rem .5rem}
.stat-num{font-size:1.4rem;font-weight:700;color:var(--primary)}
.stat-lbl{font-size:.75rem;color:var(--txt-2);margin-top:.2rem}

.section-title{font-weight:700;margin-bottom:.8rem;font-size:.95rem;color:var(--txt)}
.bio-text{color:var(--txt-2);line-height:1.7;font-size:.95rem;margin-bottom:1.5rem}
.tags{display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:1.5rem}
.tag{background:#dbeafe;color:#1d4ed8;padding:.3rem .8rem;border-radius:2rem;font-size:.8rem;font-weight:500}
.tag.cert{background:#d1fae5;color:#065f46}
.info-list{display:grid;grid-template-columns:1fr 1fr;gap:.7rem}
.info-item{display:flex;align-items:center;gap:.5rem;font-size:.9rem;color:var(--txt-2)}
.info-item i{width:16px;color:var(--primary)}

/* ── Reviews ──────────────────────────────── */
.reviews-section{margin-top:1.8rem;border-top:1px solid var(--border);padding-top:1.5rem}
.review-item{padding:1rem 0;border-bottom:1px solid var(--border)}
.review-item:last-child{border-bottom:none}
.reviewer-info{display:flex;align-items:center;gap:.7rem;margin-bottom:.5rem}
.reviewer-avatar{width:36px;height:36px;border-radius:50%;background:var(--bg);display:flex;align-items:center;justify-content:center;font-size:.85rem;font-weight:600;color:var(--primary)}
.reviewer-name{font-weight:600;font-size:.9rem}
.reviewer-date{font-size:.78rem;color:var(--txt-2)}
.review-text{font-size:.88rem;color:var(--txt-2);line-height:1.6}

/* ── Booking Panel ────────────────────────── */
.booking-panel{background:var(--card);border-radius:var(--radius);box-shadow:var(--shadow);padding:1.8rem;position:sticky;top:1.5rem}
.panel-title{font-size:1.2rem;font-weight:700;margin-bottom:1.5rem;display:flex;align-items:center;gap:.5rem;color:var(--txt)}
.price-display{background:linear-gradient(135deg,#eff6ff,#dbeafe);border-radius:10px;padding:1rem;text-align:center;margin-bottom:1.5rem}
.price-big{font-size:1.8rem;font-weight:800;color:var(--primary)}
.price-per{font-size:.85rem;color:var(--txt-2)}

.form-group{margin-bottom:1.2rem}
.form-label{display:block;font-size:.85rem;font-weight:600;color:var(--txt);margin-bottom:.4rem}
.form-control{width:100%;padding:.75rem 1rem;border:2px solid var(--border);border-radius:10px;font-size:.9rem;color:var(--txt);transition:var(--transition);background:#fff}
.form-control:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(37,99,235,.1)}

/* ── Time Slots ──────────────────────────── */
.slots-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:.5rem;margin-top:.5rem}
.slot-btn{padding:.5rem .2rem;border:2px solid var(--border);border-radius:8px;background:#fff;font-size:.78rem;cursor:pointer;transition:var(--transition);text-align:center;color:var(--txt)}
.slot-btn:hover:not(.booked){border-color:var(--primary);background:#eff6ff;color:var(--primary)}
.slot-btn.selected{border-color:var(--primary);background:var(--primary);color:#fff}
.slot-btn.booked{background:#f1f5f9;color:#94a3b8;cursor:not-allowed;border-color:#e2e8f0;text-decoration:line-through}
.no-slots{text-align:center;padding:1.5rem;color:var(--txt-2);font-size:.9rem}
.slots-loading{text-align:center;padding:1rem;color:var(--txt-2)}

.session-summary{background:var(--bg);border-radius:10px;padding:1rem;margin-bottom:1.2rem;font-size:.88rem;color:var(--txt-2);display:none}
.session-summary.show{display:block}
.summary-row{display:flex;justify-content:space-between;padding:.3rem 0}
.summary-row strong{color:var(--txt)}
.summary-total{border-top:1px solid var(--border);padding-top:.5rem;margin-top:.5rem;font-weight:700;font-size:.95rem}

.btn-book{width:100%;padding:1rem;background:var(--primary);color:#fff;border:none;border-radius:10px;font-size:1rem;font-weight:700;cursor:pointer;transition:var(--transition);margin-top:.5rem}
.btn-book:hover{background:var(--primary-dk);transform:translateY(-1px)}
.btn-book:disabled{background:#94a3b8;cursor:not-allowed;transform:none}

/* ── Toast ────────────────────────────────── */
.toast{position:fixed;bottom:2rem;right:2rem;background:#1e293b;color:#fff;padding:1rem 1.5rem;border-radius:10px;box-shadow:0 8px 32px rgba(0,0,0,.2);z-index:9999;transform:translateY(100px);opacity:0;transition:var(--transition);max-width:320px}
.toast.show{transform:translateY(0);opacity:1}
.toast.success{background:#10b981}
.toast.error{background:#ef4444}

/* ── Loading ──────────────────────────────── */
.page-loading{display:flex;align-items:center;justify-content:center;padding:6rem;flex-direction:column;gap:1rem;color:var(--txt-2)}
.spinner{width:40px;height:40px;border:3px solid var(--border);border-top-color:var(--primary);border-radius:50%;animation:spin .7s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
</style>

<!-- Breadcrumb -->
<div class="breadcrumb">
    <a href="/book-coach">Coaches</a>
    <span> &rsaquo; </span>
    <span id="crumb-name">Coach Profile</span>
</div>

<!-- Page loading state -->
<div class="page-loading" id="page-loading">
    <div class="spinner"></div>
    <p>Loading coach profile…</p>
</div>

<!-- Main content (hidden until loaded) -->
<div class="profile-page" id="profile-page" style="display:none">

    <!-- ── Left Column: Coach Info ── -->
    <div>
        <div class="coach-card">
            <div class="coach-hero">
                <div class="coach-avatar-wrap">
                    <div id="avatar-container"></div>
                    <span class="coach-badge"><i class="fas fa-check"></i></span>
                </div>
                <h1 class="coach-name" id="coach-name">—</h1>
                <span class="sport-chip" id="sport-chip">—</span>
                <div class="rating-row">
                    <span class="stars" id="stars-html"></span>
                    <span id="rating-val">0.0</span>
                    <span class="review-count" id="review-count">(0 reviews)</span>
                </div>
            </div>

            <div class="coach-body">
                <!-- Stats -->
                <div class="stat-row">
                    <div class="stat-box">
                        <div class="stat-num" id="stat-exp">—</div>
                        <div class="stat-lbl">Yrs Experience</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-num" id="stat-sessions">—</div>
                        <div class="stat-lbl">Sessions</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-num" id="stat-reviews">—</div>
                        <div class="stat-lbl">Reviews</div>
                    </div>
                </div>

                <!-- Bio -->
                <div class="section-title"><i class="fas fa-user" style="color:var(--primary)"></i> About</div>
                <p class="bio-text" id="bio-text">—</p>

                <!-- Specialties -->
                <div class="section-title"><i class="fas fa-star" style="color:var(--warn)"></i> Specializations</div>
                <div class="tags" id="specialties-tags"></div>

                <!-- Certifications -->
                <div id="cert-section" style="display:none">
                    <div class="section-title"><i class="fas fa-certificate" style="color:var(--success)"></i> Certifications</div>
                    <div class="tags" id="cert-tags"></div>
                </div>

                <!-- Info -->
                <div class="info-list" style="margin-top:1rem">
                    <div class="info-item"><i class="fas fa-map-marker-alt"></i><span id="info-location">—</span></div>
                    <div class="info-item"><i class="fas fa-clock"></i><span id="info-exp">—</span></div>
                </div>

                <!-- Reviews -->
                <div class="reviews-section">
                    <div class="section-title"><i class="fas fa-comments" style="color:var(--accent)"></i> Reviews</div>
                    <div id="reviews-list">
                        <p style="color:var(--txt-2);font-size:.9rem">No reviews yet.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Right Column: Booking Panel ── -->
    <div>
        <div class="booking-panel">
            <div class="panel-title"><i class="fas fa-calendar-check" style="color:var(--primary)"></i> Book a Session</div>

            <div class="price-display">
                <div class="price-big">LKR <span id="panel-price">0</span></div>
                <div class="price-per">per session</div>
            </div>

            <form id="booking-form" onsubmit="return false">
                <input type="hidden" id="coach-id" value="">

                <div class="form-group">
                    <label class="form-label">Session Type</label>
                    <select class="form-control" id="session-type">
                        <option value="individual">Individual (1-on-1)</option>
                        <option value="group">Group Session</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Select Date</label>
                    <input type="date" class="form-control" id="booking-date" min="">
                </div>

                <div class="form-group">
                    <label class="form-label">Available Time Slots</label>
                    <div id="slots-container">
                        <div class="no-slots">Select a date to see available slots</div>
                    </div>
                    <input type="hidden" id="selected-start" value="">
                    <input type="hidden" id="selected-end"   value="">
                </div>

                <div class="form-group">
                    <label class="form-label">Special Requests (optional)</label>
                    <textarea class="form-control" id="special-requests" rows="3"
                              placeholder="Any specific goals or requests…" style="resize:vertical"></textarea>
                </div>

                <!-- Summary -->
                <div class="session-summary" id="session-summary">
                    <div class="summary-row">
                        <span>Date</span>
                        <strong id="sum-date">—</strong>
                    </div>
                    <div class="summary-row">
                        <span>Time</span>
                        <strong id="sum-time">—</strong>
                    </div>
                    <div class="summary-row">
                        <span>Type</span>
                        <strong id="sum-type">—</strong>
                    </div>
                    <div class="summary-row summary-total">
                        <span>Total</span>
                        <strong id="sum-total">LKR 0</strong>
                    </div>
                </div>

                <button type="button" class="btn-book" id="btn-book" disabled onclick="submitBooking()">
                    <i class="fas fa-calendar-check"></i> Confirm Booking
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="toast" id="toast"></div>

<script>
(function () {
    // ── Helpers ────────────────────────────────────────────────
    function getCoachIdFromUrl() {
        const parts = window.location.pathname.split('/');
        return parts[parts.length - 1];
    }

    function showToast(msg, type = 'info') {
        const t = document.getElementById('toast');
        t.textContent = msg;
        t.className   = 'toast show ' + type;
        setTimeout(() => (t.className = 'toast'), 3500);
    }

    function starsHtml(rating) {
        let s = '';
        for (let i = 1; i <= 5; i++) {
            if (i <= Math.floor(rating))      s += '<i class="fas fa-star"></i>';
            else if (i - rating < 1)           s += '<i class="fas fa-star-half-alt"></i>';
            else                               s += '<i class="far fa-star"></i>';
        }
        return s;
    }

    // ── Load Coach ─────────────────────────────────────────────
    let coachData = null;

    async function loadCoach(id) {
        try {
            const res  = await fetch(`/api/coaches/${id}`);
            const data = await res.json();
            if (!data.success) throw new Error(data.error || 'Not found');
            coachData = data.coach;
            renderCoach(data.coach);
        } catch (e) {
            document.getElementById('page-loading').innerHTML =
                '<p style="color:#ef4444">Failed to load coach. <a href="/book-coach">Back</a></p>';
        }
    }

    function renderCoach(c) {
        document.getElementById('page-loading').style.display = 'none';
        document.getElementById('profile-page').style.display = '';
        document.getElementById('crumb-name').textContent = c.name;
        document.title = c.name + ' – GoPlay';

        // Avatar
        const avatarEl = document.getElementById('avatar-container');
        if (c.profile_picture) {
            avatarEl.innerHTML = `<img src="${c.profile_picture}" class="coach-avatar" alt="${c.name}">`;
        } else {
            const initial = c.name.charAt(0).toUpperCase();
            avatarEl.innerHTML = `<div class="coach-avatar-placeholder">${initial}</div>`;
        }

        document.getElementById('coach-name').textContent    = c.name;
        document.getElementById('sport-chip').textContent    = c.sport + ' Coach';
        document.getElementById('stars-html').innerHTML      = starsHtml(c.rating);
        document.getElementById('rating-val').textContent    = parseFloat(c.rating).toFixed(1);
        document.getElementById('review-count').textContent  = `(${c.reviews} reviews)`;
        document.getElementById('stat-exp').textContent      = c.experience ? c.experience.replace(' years','') : '—';
        document.getElementById('stat-sessions').textContent = c.total_sessions ?? '—';
        document.getElementById('stat-reviews').textContent  = c.reviews;
        document.getElementById('bio-text').textContent      = c.bio || 'No bio available.';
        document.getElementById('info-location').textContent = c.location || 'N/A';
        document.getElementById('info-exp').textContent      = c.experience;
        document.getElementById('panel-price').textContent   = parseFloat(c.price).toLocaleString();
        document.getElementById('coach-id').value            = c.id;

        // Specialties
        const spTags = document.getElementById('specialties-tags');
        if (c.specialties && c.specialties.length) {
            spTags.innerHTML = c.specialties.map(s => `<span class="tag">${s}</span>`).join('');
        } else {
            spTags.innerHTML = '<span style="color:var(--txt-2);font-size:.9rem">Not specified</span>';
        }

        // Certifications
        if (c.certifications && c.certifications.length) {
            document.getElementById('cert-section').style.display = '';
            document.getElementById('cert-tags').innerHTML =
                c.certifications.map(s => `<span class="tag cert">${s}</span>`).join('');
        }

        // Reviews
        const rl = document.getElementById('reviews-list');
        if (c.reviews_list && c.reviews_list.length) {
            rl.innerHTML = c.reviews_list.slice(0, 5).map(r => `
                <div class="review-item">
                    <div class="reviewer-info">
                        <div class="reviewer-avatar">${r.reviewer_name.charAt(0)}</div>
                        <div>
                            <div class="reviewer-name">${r.reviewer_name}</div>
                            <div class="reviewer-date">${new Date(r.created_at).toLocaleDateString()}</div>
                        </div>
                        <div class="stars" style="margin-left:auto;font-size:.8rem">${starsHtml(r.rating)}</div>
                    </div>
                    <p class="review-text">${r.review_text || ''}</p>
                </div>`).join('');
        }
    }

    // ── Availability slots ─────────────────────────────────────
    let selectedStart = '', selectedEnd = '';

    async function loadSlots(date) {
        const coachId = document.getElementById('coach-id').value;
        const cont    = document.getElementById('slots-container');
        cont.innerHTML = '<div class="slots-loading"><i class="fas fa-spinner fa-spin"></i> Loading slots…</div>';
        selectedStart = '';
        selectedEnd   = '';
        updateSummary();

        try {
            const res  = await fetch(`/api/coaches/${coachId}/availability?date=${date}`);
            const data = await res.json();

            if (!data.success || !data.slots.length) {
                cont.innerHTML = '<div class="no-slots"><i class="fas fa-calendar-times"></i> No slots available for this date.</div>';
                return;
            }

            const grid = document.createElement('div');
            grid.className = 'slots-grid';
            data.slots.forEach(slot => {
                const btn = document.createElement('button');
                btn.type      = 'button';
                btn.className = 'slot-btn' + (slot.available ? '' : ' booked');
                btn.textContent = slot.label.split('–')[0].trim();
                btn.title       = slot.label;
                if (slot.available) {
                    btn.dataset.start = slot.start_time;
                    btn.dataset.end   = slot.end_time;
                    btn.dataset.label = slot.label;
                    btn.addEventListener('click', () => selectSlot(btn, slot));
                } else {
                    btn.disabled = true;
                }
                grid.appendChild(btn);
            });
            cont.innerHTML = '';
            cont.appendChild(grid);

        } catch (e) {
            cont.innerHTML = '<div class="no-slots" style="color:#ef4444">Failed to load slots.</div>';
        }
    }

    function selectSlot(btn, slot) {
        document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('selected'));
        btn.classList.add('selected');
        selectedStart = slot.start_time;
        selectedEnd   = slot.end_time;
        document.getElementById('selected-start').value = selectedStart;
        document.getElementById('selected-end').value   = selectedEnd;
        updateSummary();
    }

    function updateSummary() {
        const date  = document.getElementById('booking-date').value;
        const start = selectedStart;
        const end   = selectedEnd;
        const type  = document.getElementById('session-type').value;
        const price = coachData ? parseFloat(coachData.price) : 0;
        const sum   = document.getElementById('session-summary');
        const btn   = document.getElementById('btn-book');

        if (date && start && end) {
            document.getElementById('sum-date').textContent  = new Date(date).toLocaleDateString('en-GB',{weekday:'long',day:'numeric',month:'long',year:'numeric'});
            document.getElementById('sum-time').textContent  = formatTime(start) + ' – ' + formatTime(end);
            document.getElementById('sum-type').textContent  = type.charAt(0).toUpperCase() + type.slice(1);
            document.getElementById('sum-total').textContent = 'LKR ' + price.toLocaleString();
            sum.classList.add('show');
            btn.disabled = false;
        } else {
            sum.classList.remove('show');
            btn.disabled = true;
        }
    }

    function formatTime(t) {
        const [h, m] = t.split(':').map(Number);
        const ampm = h >= 12 ? 'PM' : 'AM';
        return ((h % 12) || 12) + ':' + String(m).padStart(2,'0') + ' ' + ampm;
    }

    // ── Submit Booking ──────────────────────────────────────────
    window.submitBooking = async function () {
        const coachId  = document.getElementById('coach-id').value;
        const date     = document.getElementById('booking-date').value;
        const start    = document.getElementById('selected-start').value;
        const end      = document.getElementById('selected-end').value;
        const type     = document.getElementById('session-type').value;
        const requests = document.getElementById('special-requests').value;
        const price    = coachData ? parseFloat(coachData.price) : 0;

        if (!coachId || !date || !start || !end) {
            showToast('Please select a date and time slot.', 'error');
            return;
        }

        const btn = document.getElementById('btn-book');
        btn.disabled   = true;
        btn.innerHTML  = '<i class="fas fa-spinner fa-spin"></i> Booking…';

        try {
            const res = await fetch('/api/coach-bookings', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    coach_id:         coachId,
                    booking_date:     date,
                    start_time:       start,
                    end_time:         end,
                    session_type:     type,
                    total_amount:     price,
                    special_requests: requests,
                }),
            });

            const data = await res.json();

            if (data.success) {
                showToast('Session booked successfully!', 'success');
                setTimeout(() => (window.location.href = '/my-coach-sessions'), 1500);
            } else if (data.error === 'login_required') {
                showToast('Please log in to book a session.', 'error');
                setTimeout(() => (window.location.href = '/login?redirect=' + encodeURIComponent(window.location.href)), 1500);
            } else {
                showToast(data.error || 'Booking failed. Please try again.', 'error');
                btn.disabled  = false;
                btn.innerHTML = '<i class="fas fa-calendar-check"></i> Confirm Booking';
            }
        } catch (e) {
            showToast('Network error. Please try again.', 'error');
            btn.disabled  = false;
            btn.innerHTML = '<i class="fas fa-calendar-check"></i> Confirm Booking';
        }
    };

    // ── Init ────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        const id = getCoachIdFromUrl();
        if (!id || isNaN(id)) {
            window.location.href = '/book-coach';
            return;
        }
        loadCoach(id);

        // Min date = today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('booking-date').min = today;

        document.getElementById('booking-date').addEventListener('change', e => {
            if (e.target.value) loadSlots(e.target.value);
            updateSummary();
        });

        document.getElementById('session-type').addEventListener('change', updateSummary);
    });
})();
</script>
