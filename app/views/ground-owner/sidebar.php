<?php
/**
 * Ground Owner Sidebar - Dark Theme (matching Admin Sidebar exactly)
 */
$_base = defined('BASE_URL') ? BASE_URL : '';
$currentPath = $_SERVER['REQUEST_URI'] ?? '';
$activePage = '';
if (strpos($currentPath, '/ground-owner/grounds') !== false) $activePage = 'grounds';
elseif (strpos($currentPath, '/ground-owner/bookings') !== false) $activePage = 'bookings';
elseif (strpos($currentPath, '/ground-owner/earnings') !== false) $activePage = 'earnings';
elseif (strpos($currentPath, '/ground-owner/reviews') !== false) $activePage = 'reviews';
elseif (strpos($currentPath, '/ground-owner/schedule') !== false) $activePage = 'schedule';
elseif (strpos($currentPath, '/ground-owner/availability') !== false) $activePage = 'availability';
elseif (strpos($currentPath, '/ground-owner/maintenance') !== false) $activePage = 'maintenance';
elseif (strpos($currentPath, '/ground-owner/coaches') !== false) $activePage = 'coaches';
elseif (strpos($currentPath, '/ground-owner/commission') !== false) $activePage = 'commission';
elseif (strpos($currentPath, '/ground-owner/profile') !== false) $activePage = 'profile';
elseif (strpos($currentPath, '/ground-owner/dashboard') !== false) $activePage = 'dashboard';

$menuItems = [
    ['id'=>'dashboard','url'=>$_base.'/ground-owner/dashboard','icon'=>'fas fa-home','label'=>'Dashboard','badge'=>null],
    ['id'=>'grounds','url'=>$_base.'/ground-owner/grounds','icon'=>'fas fa-map-marker-alt','label'=>'My Grounds','badge'=>null],
    ['id'=>'bookings','url'=>$_base.'/ground-owner/bookings','icon'=>'fas fa-calendar-alt','label'=>'Bookings','badge'=>null],
    ['id'=>'earnings','url'=>$_base.'/ground-owner/earnings','icon'=>'fas fa-chart-line','label'=>'Earnings','badge'=>null],
    ['id'=>'reviews','url'=>$_base.'/ground-owner/reviews','icon'=>'fas fa-star','label'=>'Reviews','badge'=>null],
    ['id'=>'schedule','url'=>$_base.'/ground-owner/schedule','icon'=>'fas fa-clock','label'=>'Schedule','badge'=>null],
    ['id'=>'availability','url'=>$_base.'/ground-owner/availability','icon'=>'fas fa-calendar-check','label'=>'Availability','badge'=>null],
    ['id'=>'maintenance','url'=>$_base.'/ground-owner/maintenance','icon'=>'fas fa-tools','label'=>'Maintenance','badge'=>null],
    ['id'=>'coaches','url'=>$_base.'/ground-owner/coaches','icon'=>'fas fa-user-tie','label'=>'Coaches','badge'=>null],
    ['id'=>'commission','url'=>$_base.'/ground-owner/commission','icon'=>'fas fa-file-invoice-dollar','label'=>'Commission Invoices','badge'=>null],
    'divider',
    ['id'=>'profile','url'=>$_base.'/ground-owner/profile','icon'=>'fas fa-user','label'=>'Profile','badge'=>null],
    ['id'=>'logout','url'=>$_base.'/logout','icon'=>'fas fa-sign-out-alt','label'=>'Logout','badge'=>null,'class'=>'logout-link']
];
?>

<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-header">
        <button class="sidebar-collapse-btn desktop-only" onclick="toggleSidebarCollapse()" title="Toggle Sidebar">
            <i class="fa-solid fa-bars"></i>
        </button>
        <a href="<?= $_base ?>/" class="logo">
            <img src="<?= $_base ?>/public/assets/images/logo.jpeg" alt="GoPlay" class="logo-img">
            <div class="logo-text-wrap">
                <span class="logo-text">GoPlay</span>
                <span class="logo-badge">Ground Owner</span>
            </div>
        </a>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <?php foreach ($menuItems as $item): ?>
                <?php if ($item === 'divider'): ?>
                    <li class="nav-divider"><span class="divider-line"></span></li>
                <?php else: ?>
                    <li class="nav-item <?= ($activePage === $item['id']) ? 'active' : '' ?>">
                        <a href="<?= htmlspecialchars($item['url']) ?>" 
                           <?= isset($item['class']) ? 'class="'.htmlspecialchars($item['class']).'"' : '' ?>
                           title="<?= htmlspecialchars($item['label']) ?>">
                            <i class="<?= htmlspecialchars($item['icon']) ?> nav-icon"></i>
                            <span class="nav-label"><?= htmlspecialchars($item['label']) ?></span>
                            <?php if ($item['badge'] !== null): ?>
                                <span class="nav-badge"><?= htmlspecialchars($item['badge']) ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </nav>
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar"><i class="fas fa-map-marker-alt"></i></div>
            <div class="user-details">
                <span class="user-name"><?= htmlspecialchars($_SESSION['user_name'] ?? $_SESSION['first_name'] ?? 'Ground Owner') ?></span>
                <span class="user-role">Ground Owner</span>
            </div>
        </div>
    </div>
</aside>

<style>
.admin-sidebar{position:fixed;left:0;top:0;height:100vh;width:260px;background:linear-gradient(180deg,#1e293b 0%,#0f172a 100%);color:#e2e8f0;display:flex;flex-direction:column;transition:width .3s cubic-bezier(.4,0,.2,1);z-index:1000;box-shadow:4px 0 24px rgba(0,0,0,.12);overflow:hidden}
.admin-sidebar.collapsed{width:80px}
.sidebar-header{padding:24px 20px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid rgba(255,255,255,.1);min-height:80px}
.sidebar-header .logo{display:flex;align-items:center;gap:10px;transition:all .3s ease;text-decoration:none}
.logo-img{width:40px;height:40px;border-radius:8px;object-fit:contain;flex-shrink:0;background:#fff;padding:2px}
.logo-text-wrap{display:flex;flex-direction:column;opacity:1;transition:opacity .2s ease;overflow:hidden}
.admin-sidebar.collapsed .logo-text-wrap{opacity:0;width:0}
.logo-text{font-size:18px;font-weight:800;color:#fff;white-space:nowrap;line-height:1.1;letter-spacing:.5px}
.logo-badge{font-size:10px;font-weight:600;color:#94a3b8;white-space:nowrap;text-transform:uppercase;letter-spacing:.8px;margin-top:1px}
.admin-sidebar.collapsed .logo-text{opacity:0;width:0}
.sidebar-collapse-btn{background:rgba(255,255,255,.1);border:none;width:32px;height:32px;border-radius:8px;color:#e2e8f0;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .3s ease;flex-shrink:0}
.sidebar-collapse-btn:hover{background:rgba(255,255,255,.2);color:#fff;transform:scale(1.05)}
.sidebar-nav{flex:1;overflow-y:auto;overflow-x:hidden;padding:16px 0}
.sidebar-nav::-webkit-scrollbar{width:6px}
.sidebar-nav::-webkit-scrollbar-track{background:rgba(255,255,255,.05)}
.sidebar-nav::-webkit-scrollbar-thumb{background:rgba(255,255,255,.2);border-radius:3px}
.sidebar-nav ul{list-style:none;padding:0;margin:0}
.nav-item{margin:4px 12px}
.nav-item a{display:flex;align-items:center;padding:12px 16px;color:#cbd5e1;text-decoration:none;border-radius:10px;transition:all .3s ease;position:relative;overflow:hidden}
.nav-item a::before{content:'';position:absolute;left:0;top:0;width:4px;height:100%;background:#3b82f6;transform:scaleY(0);transition:transform .3s ease}
.nav-item a:hover{background:rgba(59,130,246,.1);color:#fff;transform:translateX(2px)}
.nav-item.active a{background:linear-gradient(135deg,rgba(59,130,246,.2) 0%,rgba(59,130,246,.05) 100%);color:#fff;font-weight:600}
.nav-item.active a::before{transform:scaleY(1)}
.nav-icon{font-size:18px;min-width:24px;width:24px;text-align:center;margin-right:12px;transition:all .3s ease}
.admin-sidebar.collapsed .nav-icon{margin-right:0;font-size:20px}
.nav-label{flex:1;white-space:nowrap;opacity:1;transition:opacity .2s ease}
.admin-sidebar.collapsed .nav-label{opacity:0;width:0}
.nav-badge{background:#3b82f6;color:#fff;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;min-width:20px;text-align:center}
.admin-sidebar.collapsed .nav-badge{opacity:0;width:0;padding:0}
.nav-divider{margin:16px 20px;position:relative}
.divider-line{display:block;height:1px;background:linear-gradient(90deg,transparent 0%,rgba(255,255,255,.1) 50%,transparent 100%)}
.logout-link{color:#f87171!important}
.logout-link:hover{background:rgba(248,113,113,.1)!important;color:#fca5a5!important}
.sidebar-footer{border-top:1px solid rgba(255,255,255,.1);padding:16px}
.user-info{display:flex;align-items:center;gap:12px;padding:8px;border-radius:10px;transition:all .3s ease}
.user-info:hover{background:rgba(255,255,255,.05)}
.user-avatar{width:40px;height:40px;background:linear-gradient(135deg,#3b82f6 0%,#1e40af 100%);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;color:#fff;flex-shrink:0}
.user-details{display:flex;flex-direction:column;opacity:1;transition:opacity .2s ease}
.admin-sidebar.collapsed .user-details{opacity:0;width:0}
.user-name{font-size:14px;font-weight:600;color:#fff;white-space:nowrap}
.user-role{font-size:12px;color:#94a3b8;white-space:nowrap}
.admin-main,.dashboard-main{margin-left:260px;transition:margin-left .3s cubic-bezier(.4,0,.2,1)}
.admin-sidebar.collapsed~.admin-main,.admin-sidebar.collapsed~.dashboard-main{margin-left:80px}
@media(max-width:768px){.desktop-only{display:none}.admin-sidebar{transform:translateX(-100%);width:260px!important}.admin-sidebar.active{transform:translateX(0)}.admin-sidebar.collapsed{transform:translateX(-100%)}.admin-main,.dashboard-main{margin-left:0!important}}
.admin-sidebar.collapsed .nav-item a:hover::after{content:attr(title);position:absolute;left:100%;top:50%;transform:translateY(-50%);background:#1e293b;color:#fff;padding:8px 12px;border-radius:8px;white-space:nowrap;margin-left:16px;font-size:14px;box-shadow:0 4px 12px rgba(0,0,0,.3);z-index:1001;animation:tooltipFade .2s ease}
@keyframes tooltipFade{from{opacity:0;transform:translateY(-50%) translateX(-8px)}to{opacity:1;transform:translateY(-50%) translateX(0)}}
</style>

<script>
window.BASE_URL='<?= defined("BASE_URL")?BASE_URL:"" ?>';
function toggleSidebarCollapse(){const s=document.getElementById('adminSidebar');const c=s.classList.toggle('collapsed');localStorage.setItem('sidebarCollapsed',c);window.dispatchEvent(new CustomEvent('sidebarToggle',{detail:{collapsed:c}}))}
function toggleSidebar(){document.getElementById('adminSidebar').classList.toggle('active')}
document.addEventListener('DOMContentLoaded',function(){const s=document.getElementById('adminSidebar');if(localStorage.getItem('sidebarCollapsed')==='true')s.classList.add('collapsed')});
document.addEventListener('click',function(e){const s=document.getElementById('adminSidebar');const t=document.querySelector('.sidebar-toggle');if(window.innerWidth<=768&&s&&!s.contains(e.target)&&t&&!t.contains(e.target))s.classList.remove('active')});
let resizeTimer;window.addEventListener('resize',function(){clearTimeout(resizeTimer);resizeTimer=setTimeout(function(){if(window.innerWidth>768)document.getElementById('adminSidebar').classList.remove('active')},250)});
</script>