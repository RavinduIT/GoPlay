/* Ground Owner – Profile Page (IIFE) */
(function () {
    'use strict';

    const $ = id => document.getElementById(id);
    let profileData = null;   // { user, owner_profile, stats }
    let toastTimer  = null;

    /* ================================================================
       BOOT
    ================================================================ */
    document.addEventListener('DOMContentLoaded', () => {
        loadProfile();
        bindUI();
    });

    /* ================================================================
       LOAD PROFILE
    ================================================================ */
    async function loadProfile() {
        try {
            const r    = await fetch((window.BASE_URL||'')+'/api/ground-owner/profile');
            const data = await r.json();
            if (!data.success) { toast('Could not load profile.', 'error'); return; }

            profileData = data.profile;
            render(profileData);
        } catch (e) {
            toast('Network error loading profile.', 'error');
        }
    }

    /* ================================================================
       RENDER
    ================================================================ */
    function render(p) {
        const u  = p.user          || {};
        const op = p.owner_profile || {};
        const s  = p.stats         || {};

        // Hero
        const name = [u.first_name, u.last_name].filter(Boolean).join(' ') || u.username || 'Ground Owner';
        $('gpHeroName').textContent = name;
        $('gpHeroBiz').textContent  = op.business_name || '';

        // Always show an image — use profile picture or default avatar
        const av = $('gpAvatarDisplay');
        const src = u.profile_picture || (window.BASE_URL||'')+'/public/assets/images/default-avatar.svg';
        av.innerHTML = `<img src="${esc(src)}" alt="${esc(name)}" onerror="this.src=(window.BASE_URL||'')+'/public/assets/images/default-avatar.svg'">`;

        // Stats
        $('gpStatGrounds').textContent  = s.total_facilities ?? '0';
        $('gpStatBookings').textContent = s.total_bookings   ?? '0';
        $('gpStatRating').textContent   = s.average_rating   ? Number(s.average_rating).toFixed(1) : '—';
        $('gpStatYears').textContent    = op.years_in_business || '0';

        // Contact sidebar
        setRow('gpPhone',   u.phone,              u.phone);
        setRow('gpEmail',   u.email,              u.email);
        setRow('gpBizEmail',op.business_email,    op.business_email);
        setRow('gpWebsite', op.website,           op.website, true);
        setRow('gpAddress', op.business_address,  op.business_address);

        // Social
        const social = [];
        if (op.facebook)  social.push({ url: op.facebook,  icon: 'fab fa-facebook',  cls: 'gp-si-fb', label: 'Facebook' });
        if (op.instagram) social.push({ url: op.instagram, icon: 'fab fa-instagram', cls: 'gp-si-ig', label: 'Instagram' });
        if (op.twitter)   social.push({ url: op.twitter,   icon: 'fab fa-twitter',   cls: 'gp-si-tw', label: 'Twitter / X' });

        const socialEl = $('gpSocialLinks');
        if (social.length) {
            socialEl.innerHTML = social.map(s =>
                `<a href="${esc(s.url)}" target="_blank" rel="noopener" class="gp-social-link">
                    <span class="gp-social-icon ${s.cls}"><i class="${s.icon}"></i></span>
                    ${s.label}
                </a>`
            ).join('');
        } else {
            socialEl.innerHTML = '<p class="gp-empty-hint">No social links added yet.</p>';
        }

        // Payment methods
        let payments = op.payment_methods;
        if (typeof payments === 'string') {
            try { payments = JSON.parse(payments); } catch (e) { payments = []; }
        }
        const pmEl = $('gpPayments');
        if (payments && payments.length) {
            const icons = { cash: 'fa-money-bill-wave', card: 'fa-credit-card', bank_transfer: 'fa-university', payhere: 'fa-mobile-alt', dialog_genie: 'fa-wallet' };
            pmEl.innerHTML = `<div class="gp-payment-tags">` +
                payments.map(m => `<span class="gp-payment-tag"><i class="fas ${icons[m] || 'fa-check'}"></i> ${fmtPayment(m)}</span>`).join('') +
                `</div>`;
        } else {
            pmEl.innerHTML = '<p class="gp-empty-hint">No payment methods added yet.</p>';
        }

        // Business info
        $('gpBizName').textContent  = op.business_name                 || '—';
        $('gpRegNo').textContent    = op.business_registration_number  || '—';
        $('gpYears').textContent    = op.years_in_business ? op.years_in_business + ' yrs' : '—';
        $('gpFacCount').textContent = s.total_facilities ?? '—';

        // Bio
        $('gpBio').textContent = op.bio || 'No bio added yet.';

        // Banking (mask account number)
        $('gpBankName').textContent   = op.bank_name            || '—';
        $('gpBankHolder').textContent = op.bank_account_holder  || '—';
        $('gpBankAcc').textContent    = maskAccount(op.bank_account_number);
    }

    function setRow(id, value, display, isLink = false) {
        const el = $(id);
        if (!el) return;
        const span = el.querySelector('span');
        if (!value) {
            el.style.display = 'none';
            return;
        }
        el.style.display = '';
        if (isLink) {
            span.innerHTML = `<a href="${esc(value)}" target="_blank" rel="noopener">${esc(display)}</a>`;
        } else {
            span.textContent = display;
        }
    }

    /* ================================================================
       EDIT MODAL
    ================================================================ */
    function openModal() {
        if (!profileData) return;
        const u  = profileData.user          || {};
        const op = profileData.owner_profile || {};

        // Personal
        $('gpFFirstName').value = u.first_name  || '';
        $('gpFLastName').value  = u.last_name   || '';
        $('gpFPhone').value     = u.phone        || '';
        $('gpFEmail').value     = u.email        || '';

        // Business
        $('gpFBizName').value   = op.business_name                 || '';
        $('gpFRegNo').value     = op.business_registration_number  || '';
        $('gpFYears').value     = op.years_in_business             || '';
        $('gpFBizAddr').value   = op.business_address              || '';
        $('gpFBizPhone').value  = op.business_phone                || '';
        $('gpFBizEmail').value  = op.business_email                || '';
        $('gpFWebsite').value   = op.website                       || '';
        $('gpFBio').value       = op.bio                           || '';

        // Payment checkboxes
        let pm = op.payment_methods;
        if (typeof pm === 'string') { try { pm = JSON.parse(pm); } catch (e) { pm = []; } }
        document.querySelectorAll('.gp-checks input[type="checkbox"]').forEach(cb => {
            cb.checked = Array.isArray(pm) && pm.includes(cb.value);
        });

        // Social
        $('gpFFacebook').value  = op.facebook   || '';
        $('gpFInstagram').value = op.instagram  || '';
        $('gpFTwitter').value   = op.twitter    || '';

        // Banking
        $('gpFBankName').value   = op.bank_name            || '';
        $('gpFBankHolder').value = op.bank_account_holder  || '';
        $('gpFBankAcc').value    = op.bank_account_number  || '';

        // Reset to first tab
        activateTab('personal');
        $('gpModal').style.display = 'flex';
    }

    function closeModal() {
        $('gpModal').style.display = 'none';
    }

    async function saveProfile() {
        const btn = $('gpModalSave');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving…';

        // Gather payment methods
        const pm = [];
        document.querySelectorAll('.gp-checks input[type="checkbox"]:checked').forEach(cb => pm.push(cb.value));

        const payload = {
            // user fields
            first_name: $('gpFFirstName').value.trim(),
            last_name:  $('gpFLastName').value.trim(),
            phone:      $('gpFPhone').value.trim(),
            // business
            business_name:                 $('gpFBizName').value.trim(),
            business_registration_number:  $('gpFRegNo').value.trim(),
            years_in_business:             parseInt($('gpFYears').value) || 0,
            business_address:              $('gpFBizAddr').value.trim(),
            business_phone:                $('gpFBizPhone').value.trim(),
            business_email:                $('gpFBizEmail').value.trim(),
            website:                       $('gpFWebsite').value.trim(),
            bio:                           $('gpFBio').value.trim(),
            payment_methods:               pm,
            // social
            facebook:  $('gpFFacebook').value.trim(),
            instagram: $('gpFInstagram').value.trim(),
            twitter:   $('gpFTwitter').value.trim(),
            // banking
            bank_name:            $('gpFBankName').value.trim(),
            bank_account_holder:  $('gpFBankHolder').value.trim(),
            bank_account_number:  $('gpFBankAcc').value.trim()
        };

        try {
            const r    = await fetch((window.BASE_URL||'')+'/api/ground-owner/profile', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await r.json();
            if (data.success) {
                toast('Profile saved!', 'success');
                closeModal();
                loadProfile();   // refresh display
            } else {
                toast(data.message || 'Could not save profile.', 'error');
            }
        } catch (e) {
            toast('Network error.', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save"></i> Save Changes';
        }
    }

    /* ================================================================
       AVATAR UPLOAD
    ================================================================ */
    function bindAvatarUpload() {
        const input = $('gpAvatarInput');
        if (!input) return;
        input.addEventListener('change', async () => {
            const file = input.files[0];
            if (!file) return;

            const fd = new FormData();
            fd.append('avatar', file);

            try {
                const r    = await fetch((window.BASE_URL||'')+'/api/ground-owner/upload-avatar', { method: 'POST', body: fd });
                const data = await r.json();
                if (data.success) {
                    toast('Photo updated!', 'success');
                    // Update displayed avatar immediately
                    const av = $('gpAvatarDisplay');
                    av.innerHTML = `<img src="${esc(data.avatar_url)}" alt="Avatar">`;
                    // keep profileData in sync
                    if (profileData && profileData.user) profileData.user.profile_picture = data.avatar_url;
                } else {
                    toast(data.message || 'Upload failed.', 'error');
                }
            } catch (e) {
                toast('Upload error.', 'error');
            }
            input.value = '';
        });
    }

    /* ================================================================
       TABS
    ================================================================ */
    function activateTab(tabId) {
        document.querySelectorAll('.gp-tab').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.tab === tabId);
        });
        document.querySelectorAll('.gp-tab-panel').forEach(panel => {
            panel.classList.toggle('active', panel.id === 'gpTab' + cap(tabId));
        });
    }

    /* ================================================================
       BIND UI
    ================================================================ */
    function bindUI() {
        // Edit button
        $('gpEditBtn').addEventListener('click', openModal);

        // Modal close
        $('gpModalClose').addEventListener('click', closeModal);
        $('gpModalCancel').addEventListener('click', closeModal);
        $('gpModal').addEventListener('click', e => { if (e.target === $('gpModal')) closeModal(); });

        // Save
        $('gpModalSave').addEventListener('click', saveProfile);

        // Tab switching
        document.querySelectorAll('.gp-tab').forEach(btn => {
            btn.addEventListener('click', () => activateTab(btn.dataset.tab));
        });

        // Avatar upload
        bindAvatarUpload();
    }

    /* ================================================================
       HELPERS
    ================================================================ */
    function initials(name) {
        return name.trim().split(/\s+/).map(w => w[0]).join('').toUpperCase().slice(0, 2);
    }

    function maskAccount(acc) {
        if (!acc) return '—';
        const s = String(acc);
        if (s.length <= 4) return s;
        return '•'.repeat(s.length - 4) + s.slice(-4);
    }

    function fmtPayment(key) {
        const map = { cash: 'Cash', card: 'Card', bank_transfer: 'Bank Transfer', payhere: 'PayHere', dialog_genie: 'Dialog Genie' };
        return map[key] || key;
    }

    function cap(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function esc(str) {
        if (str == null) return '';
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    }

    function toast(msg, type = 'info') {
        const el = $('gpToast');
        if (!el) return;
        clearTimeout(toastTimer);
        el.textContent = msg;
        el.className   = `gp-toast ${type} show`;
        toastTimer = setTimeout(() => el.classList.remove('show'), 3200);
    }

})();
