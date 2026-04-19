<?php
/**
 * Coach Sidebar - Dark Theme (matching Admin Sidebar exactly)
 */
$_base = defined('BASE_URL') ? BASE_URL : '';
$currentPath = $_SERVER['REQUEST_URI'] ?? '';
$activePage = '';
if (strpos($currentPath, '/coach/profile') !== false) $activePage = 'profile';
elseif (strpos($currentPath, '/coach/sessions') !== false) $activePage = 'sessions';
elseif (strpos($currentPath, '/coach/clients') !== false) $activePage = 'clients';
elseif (strpos($currentPath, '/coach/earnings') !== false) $activePage = 'earnings';
elseif (strpos($currentPath, '/coach/availability') !== false) $activePage = 'availability';
elseif (strpos($currentPath, '/coach/reviews') !== false) $activePage = 'reviews';
elseif (strpos($currentPath, '/coach/dashboard') !== false) $activePage = 'dashboard';

$menuItems = [
    ['id'=>'dashboard','url'=>$_base.'/coach/dashboard','icon'=>'fas fa-home','label'=>'Dashboard','badge'=>null],
    ['id'=>'profile','url'=>$_base.'/coach/profile','icon'=>'fas fa-user-edit','label'=>'My Profile','badge'=>null],
    ['id'=>'sessions','url'=>$_base.'/coach/sessions','icon'=>'fas fa-dumbbell','label'=>'Training Sessions','badge'=>null],
    ['id'=>'clients','url'=>$_base.'/coach/clients','icon'=>'fas fa-users','label'=>'My Clients','badge'=>null],
    ['id'=>'earnings','url'=>$_base.'/coach/earnings','icon'=>'fas fa-wallet','label'=>'Earnings','badge'=>null],
    ['id'=>'availability','url'=>$_base.'/coach/availability','icon'=>'fas fa-clock','label'=>'Availability','badge'=>null],
    ['id'=>'reviews','url'=>$_base.'/coach/reviews','icon'=>'fas fa-star','label'=>'Reviews','badge'=>null],
    'divider',
    ['id'=>'logout','url'=>$_base.'/logout','icon'=>'fas fa-sign-out-alt','label'=>'Logout','badge'=>null,'class'=>'logout-link']
];
?>

<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-header">
        <button class="sidebar-collapse-btn desktop-only" onclick="toggleSidebarCollapse()" title="Toggle Sidebar">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div class="logo"><span class="logo-text">Coach Panel</span></div>
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
            <div class="user-avatar"><i class="fas fa-running"></i></div>
            <div class="user-details">
                <span class="user-name"><?= htmlspecialchars($_SESSION['user_name'] ?? $_SESSION['first_name'] ?? 'Coach') ?></span>
                <span class="user-role">Coach</span>
            </div>
        </div>
    </div>
</aside>

<style>
.admin-sidebar{position:fixed;left:0;top:0;height:100vh;width:260px;background:linear-gradient(180deg,#1e293b 0%,#0f172a 100%);color:#e2e8f0;display:flex;flex-direction:column;transition:width .3s cubic-bezier(.4,0,.2,1);z-index:1000;box-shadow:4px 0 24px rgba(0,0,0,.12);overflow:hidden}
.admin-sidebar.collapsed{width:80px}
.sidebar-header{padding:24px 20px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid rgba(255,255,255,.1);min-height:80px}
.sidebar-header .logo{display:flex;align-items:center;gap:12px;transition:all .3s ease}
.logo-text{font-size:24px;font-weight:700;color:#fff;white-space:nowrap;opacity:1;transition:opacity .2s ease}
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
.admin-main,.dashboard-main,.cd-main{margin-left:260px;transition:margin-left .3s cubic-bezier(.4,0,.2,1)}
.admin-sidebar.collapsed~.admin-main,.admin-sidebar.collapsed~.dashboard-main,.admin-sidebar.collapsed~.cd-main{margin-left:80px}
@media(max-width:768px){.desktop-only{display:none}.admin-sidebar{transform:translateX(-100%);width:260px!important}.admin-sidebar.active{transform:translateX(0)}.admin-sidebar.collapsed{transform:translateX(-100%)}.admin-main,.dashboard-main,.cd-main{margin-left:0!important}}
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