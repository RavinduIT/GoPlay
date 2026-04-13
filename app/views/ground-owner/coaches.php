<?php
$_base = defined('BASE_URL') ? BASE_URL : '';
$title = 'Coaches - GoPlay';
$additionalCSS = ['/public/css/pages/ground-owner-dashboard.css'];
$additionalJS  = [];
include __DIR__ . '/layout-head.php';
?>
<style>
/*  Page variables  */
:root {
    --co-pending:  #f59e0b;
    --co-approved: #10b981;
    --co-rejected: #ef4444;
}

/*  Stat cards  */
.co-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 32px;
}
.co-stat {
    background: #fff; border-radius: 14px; padding: 22px 24px;
    display: flex; align-items: center; gap: 18px;
    box-shadow: 0 1px 3px rgba(0,0,0,.07); border: 1px solid #e2e8f0;
    transition: transform .2s, box-shadow .2s;
}
.co-stat:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(0,0,0,.09); }
.co-stat-icon {
    width: 50px; height: 50px; border-radius: 12px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: 1.25rem;
}
.co-stat-icon.pending  { background: #fef3c7; color: var(--co-pending); }
.co-stat-icon.approved { background: #d1fae5; color: var(--co-approved); }
.co-stat-icon.rejected { background: #fee2e2; color: var(--co-rejected); }
.co-stat-num { font-size: 2rem; font-weight: 800; color: #0f172a; line-height: 1; }
.co-stat-lbl { font-size: .8rem; color: #64748b; margin-top: 4px; font-weight: 500; }

/*  Panel card  */
.co-panel-card {
    background: #fff; border: 1px solid #e2e8f0; border-radius: 14px;
    box-shadow: 0 1px 3px rgba(0,0,0,.07); overflow: hidden;
}

/*  Tab bar  */
.co-tabs {
    display: flex; padding: 6px; background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}
.co-tab {
    flex: 1; padding: 10px 16px; border: none; background: none; cursor: pointer;
    border-radius: 8px; font-size: .875rem; font-weight: 600; color: #64748b;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    transition: background .18s, color .18s;
}
.co-tab:hover { background: #f1f5f9; color: #334155; }
.co-tab.active { background: #fff; color: #2563eb; box-shadow: 0 1px 4px rgba(0,0,0,.1); }
.co-tab-count {
    min-width: 22px; height: 22px; padding: 0 7px; border-radius: 999px;
    font-size: .72rem; font-weight: 700; display: inline-flex; align-items: center; justify-content: center;
}
.co-tab-count.p-c  { background: #fef3c7; color: #b45309; }
.co-tab-count.a-c  { background: #d1fae5; color: #065f46; }
.co-tab-count.r-c  { background: #fee2e2; color: #991b1b; }

/*  Tab panels  */
.co-panel { display: none; }
.co-panel.active { display: block; }

/*  Toolbar  */
.co-toolbar {
    padding: 14px 20px; border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; gap: 12px;
}
.co-search {
    flex: 1; display: flex; align-items: center; gap: 10px;
    background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 9px;
    padding: 9px 14px; transition: border-color .2s;
}
.co-search:focus-within { border-color: #93c5fd; background: #fff; }
.co-search i { color: #94a3b8; font-size: .9rem; }
.co-search input { border: none; background: none; outline: none; font-size: .875rem; color: #1e293b; width: 100%; }
.co-search input::placeholder { color: #94a3b8; }
.co-count-lbl { font-size: .8rem; color: #94a3b8; white-space: nowrap; }

/*  Coach list  */
.co-list { padding: 12px 20px 20px; display: flex; flex-direction: column; gap: 10px; }

.co-card {
    border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px 18px;
    display: grid; grid-template-columns: 52px 1fr auto;
    align-items: center; gap: 16px; background: #fff;
    cursor: pointer; transition: box-shadow .2s, border-color .2s;
}
.co-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.08); border-color: #cbd5e1; }
.co-card.status-rejected { opacity: .65; }

.co-avatar { width: 52px; height: 52px; border-radius: 50%; object-fit: cover; flex-shrink: 0; }
.co-avatar-init {
    width: 52px; height: 52px; border-radius: 50%; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; font-weight: 700; color: #fff;
}
.co-card-top { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 5px; }
.co-card-name { font-size: .95rem; font-weight: 700; color: #0f172a; }
.co-chip {
    padding: 2px 9px; border-radius: 999px; font-size: .72rem; font-weight: 600;
    display: inline-flex; align-items: center; gap: 5px;
}
.co-chip.sport    { background: #eff6ff; color: #2563eb; }
.co-chip.rating   { background: #fffbeb; color: #b45309; }
.co-chip.pending  { background: #fef3c7; color: #b45309; }
.co-chip.approved { background: #d1fae5; color: #065f46; }
.co-chip.rejected { background: #fee2e2; color: #991b1b; }
.co-card-meta { display: flex; flex-wrap: wrap; gap: 14px; font-size: .8rem; color: #64748b; }
.co-card-meta span { display: flex; align-items: center; gap: 5px; }
.co-card-meta i { color: #94a3b8; font-size: .78rem; }
.co-fac-row { margin-top: 5px; font-size: .8rem; color: #475569; display: flex; align-items: center; gap: 6px; }
.co-fac-row i { color: #3b82f6; }

/* card right side */
.co-card-right { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
.co-view-btn {
    padding: 7px 14px; border-radius: 8px; border: 1px solid #e2e8f0;
    background: #f8fafc; color: #475569; font-size: .8rem; font-weight: 600;
    cursor: pointer; display: flex; align-items: center; gap: 6px;
    transition: background .15s, border-color .15s;
}
.co-view-btn:hover { background: #f1f5f9; border-color: #cbd5e1; color: #1e293b; }
.co-act-btn {
    width: 34px; height: 34px; border-radius: 8px; border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center; font-size: .87rem;
    transition: opacity .15s;
}
.co-act-btn:hover { opacity: .8; }
.co-act-approve { background: #10b981; color: #fff; }
.co-act-reject  { background: #fee2e2; color: #ef4444; border: 1px solid #fecaca; }
.co-act-remove  { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }

/*  Empty  */
.co-empty { padding: 52px 24px; text-align: center; color: #94a3b8; }
.co-empty-icon {
    width: 64px; height: 64px; border-radius: 50%;
    background: #f8fafc; border: 2px dashed #e2e8f0;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem; margin: 0 auto 16px;
}
.co-empty h4 { font-size: .95rem; color: #475569; font-weight: 600; margin-bottom: 6px; }
.co-empty p  { font-size: .82rem; }

/*  Skeleton  */
.co-skeleton { padding: 20px; display: flex; flex-direction: column; gap: 10px; }
.co-skel-card {
    border-radius: 12px; border: 1px solid #f1f5f9; padding: 18px 20px;
    display: grid; grid-template-columns: 52px 1fr; gap: 16px; align-items: center;
}
.skel {
    background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
    background-size: 200% 100%; border-radius: 6px;
    animation: shimmer 1.4s ease-in-out infinite;
}
.skel-circle { border-radius: 50%; width: 52px; height: 52px; }
@keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

/* 
   DETAIL DRAWER (right side-panel)
    */
.co-drawer-overlay {
    position: fixed; inset: 0; background: rgba(15,23,42,.4);
    z-index: 8000; opacity: 0; pointer-events: none; transition: opacity .25s;
}
.co-drawer-overlay.open { opacity: 1; pointer-events: auto; }

.co-drawer {
    position: fixed; top: 0; right: 0; bottom: 0;
    width: min(480px, 100vw);
    background: #fff; z-index: 8001;
    display: flex; flex-direction: column;
    transform: translateX(100%); transition: transform .3s cubic-bezier(.4,0,.2,1);
    box-shadow: -8px 0 32px rgba(0,0,0,.12);
}
.co-drawer-overlay.open .co-drawer { transform: translateX(0); }

/* drawer header */
.co-drawer-head {
    padding: 20px 24px;
    border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; justify-content: space-between;
    flex-shrink: 0;
}
.co-drawer-head h2 { font-size: 1.05rem; font-weight: 700; color: #0f172a; }
.co-drawer-close {
    width: 34px; height: 34px; border-radius: 8px; border: 1px solid #e2e8f0;
    background: #f8fafc; cursor: pointer; display: flex; align-items: center; justify-content: center;
    font-size: .9rem; color: #64748b; transition: background .15s;
}
.co-drawer-close:hover { background: #f1f5f9; color: #1e293b; }

/* drawer body — scrollable */
.co-drawer-body { flex: 1; overflow-y: auto; padding: 0 0 24px; }

/*  Profile banner  */
.co-profile-banner {
    padding: 28px 24px 20px;
    display: flex; gap: 20px; align-items: flex-start;
    border-bottom: 1px solid #f1f5f9;
}
.co-profile-avatar {
    width: 72px; height: 72px; border-radius: 50%; object-fit: cover; flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(0,0,0,.1);
}
.co-profile-avatar-init {
    width: 72px; height: 72px; border-radius: 50%; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem; font-weight: 700; color: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,.1);
}
.co-profile-name { font-size: 1.15rem; font-weight: 800; color: #0f172a; margin-bottom: 6px; }
.co-profile-chips { display: flex; flex-wrap: wrap; gap: 6px; }

/*  Quick stats  */
.co-quick-stats {
    display: grid; grid-template-columns: repeat(4, 1fr);
    gap: 1px; background: #f1f5f9;
    border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9;
}
.co-qs-item {
    background: #fff; padding: 14px 12px; text-align: center;
}
.co-qs-val { font-size: 1.25rem; font-weight: 800; color: #0f172a; line-height: 1; }
.co-qs-lbl { font-size: .72rem; color: #94a3b8; margin-top: 4px; font-weight: 500; }
.co-qs-stars { color: #f59e0b; font-size: .7rem; margin-top: 2px; }

/*  Sections  */
.co-section { padding: 20px 24px; border-bottom: 1px solid #f8fafc; }
.co-section:last-child { border-bottom: none; }
.co-section-title {
    font-size: .78rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .06em; color: #94a3b8; margin-bottom: 12px;
}
.co-bio { font-size: .875rem; color: #475569; line-height: 1.7; }
.co-spec-list { display: flex; flex-wrap: wrap; gap: 8px; }
.co-spec-tag {
    padding: 4px 12px; border-radius: 999px; background: #f0f9ff;
    color: #0369a1; font-size: .8rem; font-weight: 500; border: 1px solid #bae6fd;
}
.co-contact-row {
    display: flex; align-items: center; gap: 10px;
    font-size: .875rem; color: #475569; padding: 5px 0;
}
.co-contact-row i { width: 16px; color: #94a3b8; text-align: center; }

/*  Credentials  */
.co-cred-item {
    display: flex; gap: 12px; align-items: flex-start; padding: 10px 0;
    border-bottom: 1px solid #f8fafc;
}
.co-cred-item:last-child { border-bottom: none; }
.co-cred-icon {
    width: 34px; height: 34px; border-radius: 8px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: .85rem;
}
.co-cred-icon.cert { background: #eff6ff; color: #2563eb; }
.co-cred-icon.ach  { background: #fef9c3; color: #ca8a04; }
.co-cred-body { flex: 1; min-width: 0; }
.co-cred-title { font-size: .875rem; font-weight: 600; color: #1e293b; }
.co-cred-org   { font-size: .78rem; color: #64748b; margin-top: 2px; }
.co-cred-date  { font-size: .75rem; color: #94a3b8; margin-top: 3px; }
.co-no-cred    { font-size: .82rem; color: #94a3b8; padding: 8px 0; }

/*  Facility info  */
.co-fac-info {
    display: flex; align-items: center; gap: 10px; padding: 10px 14px;
    background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0;
}
.co-fac-info i { color: #3b82f6; font-size: .95rem; }
.co-fac-info span { font-size: .875rem; color: #1e293b; font-weight: 500; }

/*  drawer footer (action bar)  */
.co-drawer-footer {
    border-top: 1px solid #e2e8f0; padding: 16px 24px;
    display: flex; gap: 10px; flex-shrink: 0;
    background: #fff;
}
.co-footer-btn {
    flex: 1; padding: 11px 16px; border-radius: 10px; border: none;
    font-size: .875rem; font-weight: 600; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    transition: opacity .15s, box-shadow .15s;
}
.co-footer-btn:hover  { opacity: .88; }
.co-footer-btn:active { opacity: .75; }
.co-footer-btn.approve { background: #10b981; color: #fff; }
.co-footer-btn.reject  { background: #fff; color: #ef4444; border: 1px solid #fecaca; }
.co-footer-btn.remove  { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }

/* drawer loading state */
.co-drawer-loading {
    display: flex; flex-direction: column; align-items: center;
    justify-content: center; padding: 60px 24px; gap: 14px; color: #94a3b8;
}
.co-spinner {
    width: 36px; height: 36px; border-radius: 50%;
    border: 3px solid #e2e8f0; border-top-color: #3b82f6;
    animation: spin .7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/*  Confirm modal  */
.co-overlay {
    position: fixed; inset: 0; background: rgba(15,23,42,.5);
    display: flex; align-items: center; justify-content: center;
    z-index: 9000; opacity: 0; pointer-events: none; transition: opacity .2s;
}
.co-overlay.open { opacity: 1; pointer-events: auto; }
.co-modal {
    background: #fff; border-radius: 16px; padding: 28px 28px 22px;
    max-width: 400px; width: 90%;
    transform: translateY(12px) scale(.97); transition: transform .2s;
    box-shadow: 0 20px 48px rgba(0,0,0,.18);
}
.co-overlay.open .co-modal { transform: translateY(0) scale(1); }
.co-modal-icon {
    width: 48px; height: 48px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.25rem; margin-bottom: 14px;
}
.co-modal-icon.approve { background: #d1fae5; color: #10b981; }
.co-modal-icon.reject  { background: #fee2e2; color: #ef4444; }
.co-modal-icon.remove  { background: #f1f5f9; color: #64748b; }
.co-modal h3 { font-size: 1.05rem; font-weight: 700; color: #0f172a; margin-bottom: 8px; }
.co-modal p  { font-size: .875rem; color: #64748b; line-height: 1.6; margin-bottom: 22px; }
.co-modal strong { color: #1e293b; }
.co-modal-btns { display: flex; gap: 10px; justify-content: flex-end; }
.co-modal-cancel {
    padding: 9px 20px; border: 1px solid #e2e8f0; background: #fff;
    border-radius: 9px; font-size: .875rem; font-weight: 600; cursor: pointer; color: #64748b;
}
.co-modal-cancel:hover { background: #f8fafc; }
.co-modal-confirm {
    padding: 9px 20px; border: none; border-radius: 9px;
    font-size: .875rem; font-weight: 600; cursor: pointer; color: #fff;
}
.co-modal-confirm.approve { background: #10b981; }
.co-modal-confirm.reject  { background: #ef4444; }
.co-modal-confirm.remove  { background: #64748b; }

/*  Toast  */
.co-toast {
    position: fixed; bottom: 28px; right: 28px; z-index: 9999;
    display: flex; align-items: center; gap: 10px;
    padding: 13px 18px; border-radius: 10px; font-size: .875rem; font-weight: 500;
    box-shadow: 0 8px 24px rgba(0,0,0,.15); color: #fff;
    opacity: 0; transform: translateY(10px);
    transition: opacity .25s, transform .25s; pointer-events: none;
}
.co-toast.show { opacity: 1; transform: translateY(0); }
.co-toast.success { background: #10b981; }
.co-toast.error   { background: #ef4444; }
.co-toast.info    { background: #3b82f6; }

/*  Responsive  */
@media (max-width: 900px) { .co-stats { grid-template-columns: repeat(2,1fr); } }
@media (max-width: 640px) {
    .co-stats { grid-template-columns: 1fr; }
    .co-card  { grid-template-columns: 44px 1fr; }
    .co-card-right { grid-column: 1/-1; justify-content: flex-end; border-top: 1px solid #f1f5f9; padding-top: 10px; }
    .co-quick-stats { grid-template-columns: repeat(2,1fr); }
}
</style>

<div class="ground-owner-dashboard">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <main class="dashboard-main">
        <header class="dashboard-header">
            <div class="header-left">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title">Coaches</h1>
            </div>
            <div class="header-right"></div>
        </header>

        <div class="dashboard-content">

            <!-- Stats -->
            <div class="co-stats">
                <div class="co-stat">
                    <div class="co-stat-icon pending"><i class="fas fa-hourglass-half"></i></div>
                    <div>
                        <div class="co-stat-num" id="coPendingCount">—</div>
                        <div class="co-stat-lbl">Pending Requests</div>
                    </div>
                </div>
                <div class="co-stat">
                    <div class="co-stat-icon approved"><i class="fas fa-user-check"></i></div>
                    <div>
                        <div class="co-stat-num" id="coApprovedCount">—</div>
                        <div class="co-stat-lbl">Active Coaches</div>
                    </div>
                </div>
                <div class="co-stat">
                    <div class="co-stat-icon rejected"><i class="fas fa-user-times"></i></div>
                    <div>
                        <div class="co-stat-num" id="coRejectedCount">—</div>
                        <div class="co-stat-lbl">Rejected</div>
                    </div>
                </div>
            </div>

            <!-- Panel -->
            <div class="co-panel-card">
                <div class="co-tabs" role="tablist">
                    <button class="co-tab active" data-tab="pending" role="tab">
                        <i class="fas fa-hourglass-half"></i> Pending
                        <span class="co-tab-count p-c" id="tabPendingBadge">0</span>
                    </button>
                    <button class="co-tab" data-tab="approved" role="tab">
                        <i class="fas fa-user-check"></i> Active
                        <span class="co-tab-count a-c" id="tabApprovedBadge">0</span>
                    </button>
                    <button class="co-tab" data-tab="rejected" role="tab">
                        <i class="fas fa-user-times"></i> Rejected
                        <span class="co-tab-count r-c" id="tabRejectedBadge">0</span>
                    </button>
                </div>

                <!-- Pending -->
                <div class="co-panel active" id="panel-pending">
                    <div class="co-toolbar">
                        <div class="co-search">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Search pending requests…" oninput="filterList('pending',this.value)">
                        </div>
                        <span class="co-count-lbl" id="countPending">0 requests</span>
                    </div>
                    <div class="co-list" id="list-pending"><?php echo skeletonCards(3); ?></div>
                </div>

                <!-- Approved -->
                <div class="co-panel" id="panel-approved">
                    <div class="co-toolbar">
                        <div class="co-search">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Search active coaches…" oninput="filterList('approved',this.value)">
                        </div>
                        <span class="co-count-lbl" id="countApproved">0 coaches</span>
                    </div>
                    <div class="co-list" id="list-approved"><?php echo skeletonCards(3); ?></div>
                </div>

                <!-- Rejected -->
                <div class="co-panel" id="panel-rejected">
                    <div class="co-toolbar">
                        <div class="co-search">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Search rejected…" oninput="filterList('rejected',this.value)">
                        </div>
                        <span class="co-count-lbl" id="countRejected">0 coaches</span>
                    </div>
                    <div class="co-list" id="list-rejected"><?php echo skeletonCards(2); ?></div>
                </div>
            </div>

        </div>
    </main>
</div>

<!--  DETAIL DRAWER  -->
<div class="co-drawer-overlay" id="drawerOverlay">
    <div class="co-drawer" id="coDrawer">

        <div class="co-drawer-head">
            <h2 id="drawerTitle">Coach Details</h2>
            <button class="co-drawer-close" onclick="closeDrawer()" title="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="co-drawer-body" id="drawerBody">
            <div class="co-drawer-loading">
                <div class="co-spinner"></div>
                <span>Loading…</span>
            </div>
        </div>

        <div class="co-drawer-footer" id="drawerFooter"></div>
    </div>
</div>

<!--  CONFIRM MODAL  -->
<div class="co-overlay" id="coOverlay">
    <div class="co-modal">
        <div class="co-modal-icon" id="modalIcon"></div>
        <h3 id="modalTitle"></h3>
        <p id="modalBody"></p>
        <div class="co-modal-btns">
            <button class="co-modal-cancel" onclick="closeModal()">Cancel</button>
            <button class="co-modal-confirm" id="modalConfirm">Confirm</button>
        </div>
    </div>
</div>

<!--  TOAST  -->
<div class="co-toast" id="coToast">
    <i id="toastIcon"></i>
    <span id="toastMsg"></span>
</div>

<?php
function skeletonCards(int $n): string {
    $h = '';
    for ($i = 0; $i < $n; $i++) {
        $h .= '<div class="co-skel-card">
            <div class="skel skel-circle"></div>
            <div><div class="skel" style="height:14px;width:55%;margin-bottom:10px;"></div>
            <div class="skel" style="height:11px;width:80%;"></div></div>
        </div>';
    }
    return '<div class="co-skeleton">' . $h . '</div>';
}
?>

<script>
(function () {
    'use strict';

    /*  state  */
    const state = { pending: [], approved: [], rejected: [] };
    let currentLinkId = null;

    /*  helpers  */
    const esc = s => String(s ?? '').replace(/[&<>"']/g, c =>
        ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

    const avatarColors = ['#3b82f6','#8b5cf6','#ec4899','#f97316','#14b8a6','#64748b'];
    function colorFor(name) {
        let n = 0; for (const c of name) n += c.charCodeAt(0);
        return avatarColors[n % avatarColors.length];
    }
    function initials(name) {
        return name.split(' ').map(p => p[0] || '').join('').slice(0, 2).toUpperCase();
    }
    function avatarSmall(url, name) {
        const ini = initials(name), col = colorFor(name);
        if (url) return `<img class="co-avatar" src="${esc(url)}" alt="${esc(name)}"
            onerror="this.outerHTML='<div class=co-avatar-init style=background:${col}>${ini}</div>'">`;
        return `<div class="co-avatar-init" style="background:${col}">${ini}</div>`;
    }
    function avatarLarge(url, name) {
        const ini = initials(name), col = colorFor(name);
        if (url) return `<img class="co-profile-avatar" src="${esc(url)}" alt="${esc(name)}"
            onerror="this.outerHTML='<div class=co-profile-avatar-init style=background:${col}>${ini}</div>'">`;
        return `<div class="co-profile-avatar-init" style="background:${col}">${ini}</div>`;
    }
    function stars(rating) {
        const r = Math.round(Number(rating) * 2) / 2;
        let s = '';
        for (let i = 1; i <= 5; i++) {
            s += i <= r ? '<i class="fas fa-star"></i>' :
                 (i - 0.5 === r ? '<i class="fas fa-star-half-alt"></i>' : '<i class="far fa-star"></i>');
        }
        return s;
    }

    /*  toast  */
    let toastTimer;
    function toast(msg, type = 'success') {
        const el = document.getElementById('coToast');
        const icons = { success: 'fa-check-circle', error: 'fa-exclamation-circle', info: 'fa-info-circle' };
        el.className = `co-toast ${type}`;
        document.getElementById('toastIcon').className = `fas ${icons[type] || icons.info}`;
        document.getElementById('toastMsg').textContent = msg;
        el.classList.add('show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => el.classList.remove('show'), 3200);
    }

    /*  confirm modal  */
    let pendingAction = null;
    function openModal({ icon, iconClass, title, body, confirmLabel, confirmClass, onConfirm }) {
        document.getElementById('modalIcon').innerHTML = `<i class="fas ${icon}"></i>`;
        document.getElementById('modalIcon').className = `co-modal-icon ${iconClass}`;
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('modalBody').innerHTML = body;
        const btn = document.getElementById('modalConfirm');
        btn.textContent = confirmLabel;
        btn.className = `co-modal-confirm ${confirmClass}`;
        pendingAction = onConfirm;
        document.getElementById('coOverlay').classList.add('open');
        btn.focus();
    }
    window.closeModal = () => {
        document.getElementById('coOverlay').classList.remove('open');
        pendingAction = null;
    };
    document.getElementById('modalConfirm').addEventListener('click', () => {
        if (pendingAction) pendingAction();
        closeModal();
    });
    document.getElementById('coOverlay').addEventListener('click', e => {
        if (e.target === e.currentTarget) closeModal();
    });

    /*  load list  */
    async function load() {
        try {
            const r = await fetch((window.BASE_URL||'')+'/api/ground-owner/coaches');
            const d = await r.json();
            if (!d.success) throw new Error(d.message);
            state.pending  = d.pending  || [];
            state.approved = d.approved || [];
            state.rejected = d.rejected || [];
            render();
        } catch (e) {
            toast('Could not load coaches: ' + e.message, 'error');
            ['pending','approved','rejected'].forEach(s =>
                document.getElementById('list-' + s).innerHTML = emptyHtml(s));
        }
    }

    /*  render list  */
    function render() {
        const pc = state.pending.length, ac = state.approved.length, rc = state.rejected.length;
        setText('coPendingCount',  pc);
        setText('coApprovedCount', ac);
        setText('coRejectedCount', rc);
        setText('tabPendingBadge',  pc);
        setText('tabApprovedBadge', ac);
        setText('tabRejectedBadge', rc);
        setCountLbl('countPending',  pc, 'request');
        setCountLbl('countApproved', ac, 'coach');
        setCountLbl('countRejected', rc, 'coach');
        const sb = document.getElementById('coachesPendingCount');
        if (sb) { sb.textContent = pc; sb.style.display = pc > 0 ? '' : 'none'; }
        renderList('pending',  state.pending);
        renderList('approved', state.approved);
        renderList('rejected', state.rejected);
    }
    function setText(id, v) { const el = document.getElementById(id); if (el) el.textContent = v; }
    function setCountLbl(id, n, noun) {
        const el = document.getElementById(id);
        if (el) el.textContent = `${n} ${noun}${n !== 1 ? 's' : ''}`;
    }
    function renderList(status, list) {
        document.getElementById('list-' + status).innerHTML =
            list.length ? list.map(c => coachCard(c, status)).join('') : emptyHtml(status);
    }
    function emptyHtml(status) {
        const cfg = {
            pending:  { icon: 'fa-hourglass-half', h: 'No pending requests', p: 'Coach requests will appear here.' },
            approved: { icon: 'fa-user-check',     h: 'No active coaches',    p: 'Approved coaches will show on your facility page.' },
            rejected: { icon: 'fa-user-times',     h: 'No rejected coaches',  p: 'Rejected requests will appear here.' },
        }[status];
        return `<div class="co-empty">
            <div class="co-empty-icon"><i class="fas ${cfg.icon}"></i></div>
            <h4>${cfg.h}</h4><p>${cfg.p}</p>
        </div>`;
    }

    function coachCard(c, status) {
        const name   = `${c.first_name||''} ${c.last_name||''}`.trim() || 'Unknown Coach';
        const sport  = c.sport_name ? `<span class="co-chip sport"><i class="fas fa-futbol"></i>${esc(c.sport_name)}</span>` : '';
        const rating = c.rating     ? `<span class="co-chip rating"><i class="fas fa-star"></i>${Number(c.rating).toFixed(1)}</span>` : '';
        const sChip  = `<span class="co-chip ${status}">${
            {pending:'<i class="fas fa-hourglass-half"></i>Pending',
             approved:'<i class="fas fa-check-circle"></i>Active',
             rejected:'<i class="fas fa-times-circle"></i>Rejected'}[status]
        }</span>`;
        const meta = [
            c.hourly_rate      ? `<span><i class="fas fa-tag"></i>Rs. ${Number(c.hourly_rate).toLocaleString()}/hr</span>` : '',
            c.experience_years ? `<span><i class="fas fa-briefcase"></i>${esc(c.experience_years)} yrs exp</span>` : '',
            c.email            ? `<span><i class="fas fa-envelope"></i>${esc(c.email)}</span>` : '',
        ].join('');
        const fac = c.facility_name ? `<div class="co-fac-row"><i class="fas fa-map-marker-alt"></i>${esc(c.facility_name)}</div>` : '';

        let actions = '';
        if (status === 'pending') {
            actions = `
                <button class="co-act-btn co-act-approve" onclick="event.stopPropagation();doApprove(${c.link_id},'${esc(name)}')" title="Approve"><i class="fas fa-check"></i></button>
                <button class="co-act-btn co-act-reject"  onclick="event.stopPropagation();doReject(${c.link_id},'${esc(name)}')"  title="Reject"><i class="fas fa-times"></i></button>`;
        } else if (status === 'approved') {
            actions = `<button class="co-act-btn co-act-remove" onclick="event.stopPropagation();doRemove(${c.link_id},'${esc(name)}')" title="Remove"><i class="fas fa-user-minus"></i></button>`;
        } else {
            actions = `<button class="co-act-btn co-act-remove" onclick="event.stopPropagation();doRemove(${c.link_id},'${esc(name)}')" title="Delete"><i class="fas fa-trash"></i></button>`;
        }

        return `<div class="co-card status-${status}" id="co-card-${c.link_id}" data-name="${esc(name.toLowerCase())}"
                     onclick="openDrawer(${c.link_id},'${esc(name)}')">
            ${avatarSmall(c.profile_picture, name)}
            <div>
                <div class="co-card-top">${`<span class="co-card-name">${esc(name)}</span>`}${sport}${rating}${sChip}</div>
                <div class="co-card-meta">${meta}</div>
                ${fac}
            </div>
            <div class="co-card-right">
                <button class="co-view-btn" onclick="event.stopPropagation();openDrawer(${c.link_id},'${esc(name)}')">
                    <i class="fas fa-eye"></i> View
                </button>
                ${actions}
            </div>
        </div>`;
    }

    /*  search  */
    window.filterList = function(status, query) {
        const q = query.toLowerCase();
        let visible = 0;
        document.querySelectorAll(`#list-${status} .co-card`).forEach(el => {
            const show = !q || el.dataset.name.includes(q);
            el.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        setCountLbl('count' + status.charAt(0).toUpperCase() + status.slice(1),
                    q ? visible : state[status].length,
                    status === 'pending' ? 'request' : 'coach');
    };

    /* 
       DETAIL DRAWER
     */
    window.openDrawer = async function(linkId, name) {
        currentLinkId = linkId;
        document.getElementById('drawerTitle').textContent = name;
        document.getElementById('drawerBody').innerHTML = `
            <div class="co-drawer-loading">
                <div class="co-spinner"></div><span>Loading details…</span>
            </div>`;
        document.getElementById('drawerFooter').innerHTML = '';
        document.getElementById('drawerOverlay').classList.add('open');
        document.body.style.overflow = 'hidden';

        try {
            const r = await fetch(`${window.BASE_URL||""}/api/ground-owner/coaches/${linkId}`);
            const d = await r.json();
            if (!d.success) throw new Error(d.message);
            renderDrawer(d.coach);
        } catch (e) {
            document.getElementById('drawerBody').innerHTML = `
                <div class="co-drawer-loading" style="color:#ef4444;">
                    <i class="fas fa-exclamation-circle" style="font-size:2rem;"></i>
                    <span>${esc(e.message)}</span>
                </div>`;
        }
    };

    window.closeDrawer = function() {
        document.getElementById('drawerOverlay').classList.remove('open');
        document.body.style.overflow = '';
        currentLinkId = null;
    };

    document.getElementById('drawerOverlay').addEventListener('click', e => {
        if (e.target === e.currentTarget) closeDrawer();
    });

    function renderDrawer(c) {
        const name   = `${c.first_name||''} ${c.last_name||''}`.trim() || 'Unknown Coach';
        const status = c.link_status || 'pending';
        const sChip  = `<span class="co-chip ${status}">${
            {pending:'<i class="fas fa-hourglass-half"></i>Pending approval',
             approved:'<i class="fas fa-check-circle"></i>Active',
             rejected:'<i class="fas fa-times-circle"></i>Rejected'}[status]
        }</span>`;
        const sport = c.sport_name ? `<span class="co-chip sport"><i class="fas fa-futbol"></i>${esc(c.sport_name)}</span>` : '';

        /*  Quick stats  */
        const ratingVal  = c.rating ? Number(c.rating).toFixed(1) : '—';
        const starsHtml  = c.rating ? `<div class="co-qs-stars">${stars(c.rating)}</div>` : '';
        const sessions   = c.completed_sessions ?? c.total_sessions ?? 0;

        /*  Bio  */
        const bio = c.bio
            ? `<div class="co-section">
                   <div class="co-section-title">About</div>
                   <div class="co-bio">${esc(c.bio)}</div>
               </div>`
            : '';

        /*  Specializations  */
        let specHtml = '';
        if (c.specializations) {
            const tags = c.specializations.split(',').map(s => s.trim()).filter(Boolean);
            if (tags.length) {
                specHtml = `<div class="co-section">
                    <div class="co-section-title">Specializations</div>
                    <div class="co-spec-list">${tags.map(t => `<span class="co-spec-tag">${esc(t)}</span>`).join('')}</div>
                </div>`;
            }
        }

        /*  Contact  */
        const contactRows = [
            c.email    ? `<div class="co-contact-row"><i class="fas fa-envelope"></i><span>${esc(c.email)}</span></div>` : '',
            c.phone    ? `<div class="co-contact-row"><i class="fas fa-phone"></i><span>${esc(c.phone)}</span></div>` : '',
            c.location ? `<div class="co-contact-row"><i class="fas fa-map-marker-alt"></i><span>${esc(c.location)}</span></div>` : '',
        ].join('');
        const contact = contactRows ? `<div class="co-section">
            <div class="co-section-title">Contact</div>${contactRows}
        </div>` : '';

        /*  Facility  */
        const fac = c.facility_name ? `<div class="co-section">
            <div class="co-section-title">Linked Facility</div>
            <div class="co-fac-info"><i class="fas fa-map-marker-alt"></i><span>${esc(c.facility_name)}</span></div>
        </div>` : '';

        /*  Certificates  */
        const certs = c.certificates && c.certificates.length
            ? c.certificates.map(cert => `
                <div class="co-cred-item">
                    <div class="co-cred-icon cert"><i class="fas fa-certificate"></i></div>
                    <div class="co-cred-body">
                        <div class="co-cred-title">${esc(cert.title)}</div>
                        ${cert.issuing_organization ? `<div class="co-cred-org">${esc(cert.issuing_organization)}</div>` : ''}
                        ${cert.issue_date ? `<div class="co-cred-date"><i class="fas fa-calendar-alt" style="margin-right:4px;"></i>${formatDate(cert.issue_date)}${cert.expiry_date ? ' – ' + formatDate(cert.expiry_date) : ''}</div>` : ''}
                    </div>
                </div>`).join('')
            : '<div class="co-no-cred">No certificates listed.</div>';

        /*  Achievements  */
        const achs = c.achievements && c.achievements.length
            ? c.achievements.map(a => `
                <div class="co-cred-item">
                    <div class="co-cred-icon ach"><i class="fas fa-trophy"></i></div>
                    <div class="co-cred-body">
                        <div class="co-cred-title">${esc(a.title)}</div>
                        ${a.description ? `<div class="co-cred-org">${esc(a.description)}</div>` : ''}
                        ${a.date_achieved ? `<div class="co-cred-date"><i class="fas fa-calendar-alt" style="margin-right:4px;"></i>${formatDate(a.date_achieved)}</div>` : ''}
                    </div>
                </div>`).join('')
            : '<div class="co-no-cred">No achievements listed.</div>';

        document.getElementById('drawerBody').innerHTML = `
            <!-- Profile banner -->
            <div class="co-profile-banner">
                ${avatarLarge(c.profile_picture, name)}
                <div>
                    <div class="co-profile-name">${esc(name)}</div>
                    <div class="co-profile-chips">${sport}${sChip}</div>
                </div>
            </div>

            <!-- Quick stats -->
            <div class="co-quick-stats">
                <div class="co-qs-item">
                    <div class="co-qs-val">${ratingVal}</div>
                    ${starsHtml}
                    <div class="co-qs-lbl">Rating</div>
                </div>
                <div class="co-qs-item">
                    <div class="co-qs-val">${sessions}</div>
                    <div class="co-qs-lbl">Sessions</div>
                </div>
                <div class="co-qs-item">
                    <div class="co-qs-val">${c.experience_years ?? '—'}</div>
                    <div class="co-qs-lbl">Yrs Exp</div>
                </div>
                <div class="co-qs-item">
                    <div class="co-qs-val" style="font-size:1rem;">Rs.${c.hourly_rate ? Number(c.hourly_rate).toLocaleString() : '—'}</div>
                    <div class="co-qs-lbl">/ hour</div>
                </div>
            </div>

            ${bio}
            ${specHtml}
            ${contact}
            ${fac}

            <!-- Certificates -->
            <div class="co-section">
                <div class="co-section-title">Certificates</div>
                ${certs}
            </div>

            <!-- Achievements -->
            <div class="co-section">
                <div class="co-section-title">Achievements</div>
                ${achs}
            </div>
        `;

        /* Footer actions */
        const footer = document.getElementById('drawerFooter');
        if (status === 'pending') {
            footer.innerHTML = `
                <button class="co-footer-btn reject" onclick="doReject(${c.link_id},'${esc(name)}')">
                    <i class="fas fa-times"></i> Reject
                </button>
                <button class="co-footer-btn approve" onclick="doApprove(${c.link_id},'${esc(name)}')">
                    <i class="fas fa-check"></i> Approve
                </button>`;
        } else if (status === 'approved') {
            footer.innerHTML = `
                <button class="co-footer-btn remove" onclick="doRemove(${c.link_id},'${esc(name)}')">
                    <i class="fas fa-user-minus"></i> Remove Coach
                </button>`;
        } else {
            footer.innerHTML = `
                <button class="co-footer-btn remove" onclick="doRemove(${c.link_id},'${esc(name)}')">
                    <i class="fas fa-trash"></i> Delete
                </button>`;
        }
    }

    function formatDate(str) {
        if (!str) return '';
        const d = new Date(str);
        if (isNaN(d)) return str;
        return d.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
    }

    /*  actions  */
    window.doApprove = function(linkId, name) {
        openModal({
            icon: 'fa-user-check', iconClass: 'approve', title: 'Approve Coach',
            body: `Allow <strong>${esc(name)}</strong> to train at your facility? They will appear on your public page.`,
            confirmLabel: 'Approve', confirmClass: 'approve',
            onConfirm: async () => {
                try {
                    const d = await (await fetch(`${window.BASE_URL||""}/api/ground-owner/coaches/${linkId}/approve`, { method: 'PUT' })).json();
                    if (!d.success) throw new Error(d.message);
                    toast(`${name} approved!`);
                    closeDrawer();
                    await load();
                } catch (e) { toast('Error: ' + e.message, 'error'); }
            }
        });
    };

    window.doReject = function(linkId, name) {
        openModal({
            icon: 'fa-user-times', iconClass: 'reject', title: 'Reject Request',
            body: `Reject the request from <strong>${esc(name)}</strong>? They will be notified.`,
            confirmLabel: 'Reject', confirmClass: 'reject',
            onConfirm: async () => {
                try {
                    const d = await (await fetch(`${window.BASE_URL||""}/api/ground-owner/coaches/${linkId}/reject`, { method: 'PUT' })).json();
                    if (!d.success) throw new Error(d.message);
                    toast(`${name} rejected.`, 'info');
                    closeDrawer();
                    await load();
                } catch (e) { toast('Error: ' + e.message, 'error'); }
            }
        });
    };

    window.doRemove = function(linkId, name) {
        openModal({
            icon: 'fa-user-minus', iconClass: 'remove', title: 'Remove Coach',
            body: `Remove <strong>${esc(name)}</strong> from your facility? They will no longer appear publicly.`,
            confirmLabel: 'Remove', confirmClass: 'remove',
            onConfirm: async () => {
                try {
                    const d = await (await fetch(`${window.BASE_URL||""}/api/ground-owner/coaches/${linkId}`, { method: 'DELETE' })).json();
                    if (!d.success) throw new Error(d.message);
                    toast(`${name} removed.`, 'info');
                    closeDrawer();
                    await load();
                } catch (e) { toast('Error: ' + e.message, 'error'); }
            }
        });
    };

    /* keep old names used from inline onclick */
    window.approveCoach = window.doApprove;
    window.rejectCoach  = window.doReject;
    window.removeCoach  = window.doRemove;

    /*  tabs  */
    document.querySelectorAll('.co-tab').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.co-tab').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.co-panel').forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById('panel-' + btn.dataset.tab).classList.add('active');
        });
    });

    window.toggleSidebar = window.toggleSidebar || function () {
        document.getElementById('dashboardSidebar')?.classList.toggle('open');
    };

    load();
})();
</script>

<?php include __DIR__ . '/layout-foot.php'; ?>
