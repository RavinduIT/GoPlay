<?php $_base = defined('BASE_URL') ? BASE_URL : ''; ?>
<link rel="stylesheet" href="<?= $_base ?>/public/css/pages/admin-dashboard.css">
<div class="admin-dashboard">
    <?php
    $activePage = 'profile';
    include __DIR__ . '/../components/admin-sidebar.php';
    ?>

    <main class="admin-main">
        <header class="admin-header">
            <div class="header-left">
                <h1 class="page-title">My Profile</h1>
            </div>
        </header>

        <div class="dashboard-content" style="padding: 24px;">
            <div id="profile-message" style="display:none; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-weight: 500;"></div>

            <!-- Profile Card -->
            <div style="background: white; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 24px;">
                <div style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); padding: 40px 32px; display: flex; align-items: center; gap: 24px;">
                    <div style="position: relative;">
                        <img id="admin-avatar" src="<?= $_base ?>/public/assets/images/default-avatar.png"
                             alt="Admin" style="width: 100px; height: 100px; border-radius: 50%; border: 4px solid rgba(255,255,255,0.3); object-fit: cover;">
                        <label style="position: absolute; bottom: 0; right: 0; background: #3b82f6; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.3);">
                            <i class="fas fa-camera" style="font-size: 14px;"></i>
                            <input type="file" id="avatar-input" accept="image/*" hidden>
                        </label>
                    </div>
                    <div>
                        <h2 id="admin-name" style="color: white; font-size: 1.5rem; font-weight: 700; margin: 0 0 4px;">Loading...</h2>
                        <p id="admin-role" style="color: rgba(255,255,255,0.7); font-size: 0.95rem; margin: 0;">Super Admin</p>
                        <p id="admin-email" style="color: rgba(255,255,255,0.5); font-size: 0.85rem; margin: 4px 0 0;">loading...</p>
                    </div>
                </div>
            </div>

            <!-- Profile Details -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                <!-- Personal Info -->
                <div style="background: white; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); border: 1px solid #e5e7eb; padding: 28px;">
                    <h3 style="font-size: 1.1rem; font-weight: 600; color: #1f2937; margin: 0 0 20px; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-user" style="color: #3b82f6;"></i> Personal Information
                    </h3>
                    <form id="profile-form">
                        <div style="display: grid; gap: 16px;">
                            <div>
                                <label style="display: block; font-weight: 500; color: #374151; font-size: 0.875rem; margin-bottom: 6px;">First Name</label>
                                <input type="text" id="first_name" name="first_name" style="width: 100%; padding: 10px 14px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 0.95rem; box-sizing: border-box;" required>
                            </div>
                            <div>
                                <label style="display: block; font-weight: 500; color: #374151; font-size: 0.875rem; margin-bottom: 6px;">Last Name</label>
                                <input type="text" id="last_name" name="last_name" style="width: 100%; padding: 10px 14px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 0.95rem; box-sizing: border-box;" required>
                            </div>
                            <div>
                                <label style="display: block; font-weight: 500; color: #374151; font-size: 0.875rem; margin-bottom: 6px;">Email</label>
                                <input type="email" id="email" name="email" style="width: 100%; padding: 10px 14px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 0.95rem; background: #f9fafb; color: #6b7280; box-sizing: border-box;" readonly>
                            </div>
                            <div>
                                <label style="display: block; font-weight: 500; color: #374151; font-size: 0.875rem; margin-bottom: 6px;">Phone</label>
                                <input type="tel" id="phone" name="phone" pattern="[0-9+\s\-()]{7,15}" style="width: 100%; padding: 10px 14px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 0.95rem; box-sizing: border-box;">
                            </div>
                        </div>
                        <button type="submit" style="margin-top: 20px; padding: 10px 24px; background: #3b82f6; color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 0.95rem; cursor: pointer;">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </form>
                </div>

                <!-- Change Password -->
                <div style="background: white; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); border: 1px solid #e5e7eb; padding: 28px;">
                    <h3 style="font-size: 1.1rem; font-weight: 600; color: #1f2937; margin: 0 0 20px; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-lock" style="color: #3b82f6;"></i> Change Password
                    </h3>
                    <form id="password-form">
                        <div style="display: grid; gap: 16px;">
                            <div>
                                <label style="display: block; font-weight: 500; color: #374151; font-size: 0.875rem; margin-bottom: 6px;">Current Password</label>
                                <input type="password" id="current_password" name="current_password" style="width: 100%; padding: 10px 14px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 0.95rem; box-sizing: border-box;" required>
                            </div>
                            <div>
                                <label style="display: block; font-weight: 500; color: #374151; font-size: 0.875rem; margin-bottom: 6px;">New Password</label>
                                <input type="password" id="new_password" name="new_password" minlength="8" style="width: 100%; padding: 10px 14px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 0.95rem; box-sizing: border-box;" required>
                            </div>
                            <div>
                                <label style="display: block; font-weight: 500; color: #374151; font-size: 0.875rem; margin-bottom: 6px;">Confirm Password</label>
                                <input type="password" id="confirm_password" name="confirm_password" minlength="8" style="width: 100%; padding: 10px 14px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 0.95rem; box-sizing: border-box;" required>
                            </div>
                        </div>
                        <button type="submit" style="margin-top: 20px; padding: 10px 24px; background: #3b82f6; color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 0.95rem; cursor: pointer;">
                            <i class="fas fa-key"></i> Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<style>
@media (max-width: 900px) {
    .dashboard-content > div:last-child {
        grid-template-columns: 1fr !important;
    }
}
</style>

<script>
(function() {
    const B = window.BASE_URL || '';

    // Load profile data
    async function loadProfile() {
        try {
            const res = await fetch(B + '/admin/api/profile');
            const data = await res.json();
            if (!data.success) return;
            const p = data.profile;
            document.getElementById('admin-name').textContent = p.name || '';
            document.getElementById('admin-role').textContent = p.role || 'Admin';
            document.getElementById('admin-email').textContent = p.email || '';
            if (p.avatar) document.getElementById('admin-avatar').src = p.avatar;
            // Fill form
            document.getElementById('first_name').value = p.first_name || '';
            document.getElementById('last_name').value = p.last_name || '';
            document.getElementById('email').value = p.email || '';
            document.getElementById('phone').value = p.phone || '';
        } catch (e) {
            console.error('Failed to load profile:', e);
        }
    }

    // Avatar upload
    document.getElementById('avatar-input').addEventListener('change', async function(e) {
        const file = e.target.files[0];
        if (!file) return;
        if (file.size > 2 * 1024 * 1024) { showMsg('File must be under 2MB', 'error'); return; }
        const fd = new FormData();
        fd.append('avatar', file);
        try {
            const res = await fetch(B + '/api/admin/avatar', { method: 'POST', body: fd });
            const data = await res.json();
            if (data.success) {
                document.getElementById('admin-avatar').src = data.avatar_url;
                showMsg('Avatar updated!', 'success');
            } else {
                showMsg(data.message || 'Upload failed', 'error');
            }
        } catch (e) { showMsg('Upload error', 'error'); }
    });

    // Profile form
    document.getElementById('profile-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const body = JSON.stringify({
            first_name: document.getElementById('first_name').value.trim(),
            last_name: document.getElementById('last_name').value.trim(),
            phone: document.getElementById('phone').value.trim()
        });
        try {
            const res = await fetch(B + '/api/admin/profile', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body
            });
            const data = await res.json();
            if (data.success) {
                showMsg('Profile updated!', 'success');
                loadProfile();
            } else {
                showMsg(data.message || 'Update failed', 'error');
            }
        } catch (e) { showMsg('Update error', 'error'); }
    });

    // Password form
    document.getElementById('password-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const np = document.getElementById('new_password').value;
        const cp = document.getElementById('confirm_password').value;
        if (np !== cp) { showMsg('Passwords do not match', 'error'); return; }
        if (np.length < 8) { showMsg('Password must be at least 8 characters', 'error'); return; }
        const body = JSON.stringify({
            current_password: document.getElementById('current_password').value,
            new_password: np
        });
        try {
            const res = await fetch(B + '/api/admin/change-password', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body
            });
            const data = await res.json();
            if (data.success) {
                showMsg('Password updated!', 'success');
                document.getElementById('password-form').reset();
            } else {
                showMsg(data.message || 'Failed to update password', 'error');
            }
        } catch (e) { showMsg('Error updating password', 'error'); }
    });

    function showMsg(text, type) {
        const el = document.getElementById('profile-message');
        el.textContent = text;
        el.style.display = 'block';
        el.style.background = type === 'success' ? '#d1fae5' : '#fee2e2';
        el.style.color = type === 'success' ? '#065f46' : '#991b1b';
        setTimeout(() => { el.style.display = 'none'; }, 4000);
    }

    loadProfile();
})();
</script>
