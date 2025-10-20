<?php
// ---- Shared components (navbar) ----
$VIEWS = dirname(__DIR__); // -> .../app/views
require_once $VIEWS . '/components/navbar.php';
?>

<!-- Font Awesome (icons). Remove if already loaded in your layout -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" referrerpolicy="no-referrer" />

<style>
  :root{
    /* your navbar height */
    --site-header-height: 70px;

    /* theme + layout */
    --primary-color:#7c3aed; --primary-light:#8b5cf6;
    --success-color:#10b981; --warning-color:#f59e0b; --danger-color:#ef4444;
    --sidebar-width:280px;
    --border-radius:12px;
    --shadow-md:0 4px 6px -1px rgba(0,0,0,.1);
    --shadow-lg:0 10px 15px -3px rgba(0,0,0,.1);
    --shadow-xl:0 20px 25px -5px rgba(0,0,0,.1);
    --transition:all .3s cubic-bezier(.4,0,.2,1);
  }

  /* layout wrapper under the navbar */
  .shop-owner-dashboard{
    display:flex;
    min-height:calc(100vh - var(--site-header-height));
  }

  /* SIDEBAR sits below navbar */
  .dashboard-sidebar{
    width:var(--sidebar-width);
    position:fixed; left:0;
    top:var(--site-header-height);
    height:calc(100vh - var(--site-header-height));
    background:linear-gradient(180deg,#1e293b 0%,#0f172a 100%);
    color:#fff; overflow-y:auto;
    z-index:900; /* keep below navbar if navbar has higher z-index */
    box-shadow:var(--shadow-xl);
    transition:var(--transition);
    transform:translateX(0);
  }
  body.sidebar-collapsed .dashboard-sidebar{ transform:translateX(-100%); }

  .sidebar-header{padding:1.25rem 1.5rem;border-bottom:1px solid rgba(255,255,255,.1);display:flex;align-items:center;justify-content:space-between;}
  .logo{display:flex;align-items:center;gap:.75rem;font-weight:700}
  .logo i{background:var(--primary-light);padding:.5rem;border-radius:var(--border-radius);box-shadow:var(--shadow-md);}

  .sidebar-nav ul{list-style:none;padding:1rem 0;margin:0}
  .sidebar-nav li{margin:.25rem 0}
  .sidebar-nav a{display:flex;align-items:center;gap:.75rem;padding:.875rem 1.5rem;color:rgba(255,255,255,.85);text-decoration:none;transition:var(--transition)}
  .sidebar-nav a:hover{background:rgba(255,255,255,.1);color:#fff;padding-left:2rem}
  .sidebar-nav li.active a{background:linear-gradient(90deg,var(--primary-color),var(--primary-light));border-radius:0 25px 25px 0;margin-right:1rem;box-shadow:var(--shadow-md)}
  .sidebar-nav a i{width:20px;text-align:center}

  .badge{margin-left:auto;background:var(--danger-color);color:#fff;font-size:.75rem;font-weight:600;padding:.25rem .5rem;border-radius:12px;min-width:20px;text-align:center}
  .badge.new{background:var(--success-color);animation:pulse 2s infinite}
  .badge.warning{background:var(--warning-color)}
  @keyframes pulse{0%,100%{opacity:1}50%{opacity:.7}}
  .nav-divider{height:1px;background:rgba(255,255,255,.1);margin:1rem 0}
  .logout-link{color:rgba(255,255,255,.6)!important}
  .logout-link:hover{color:#ef4444!important;background:rgba(239,68,68,.12)!important}

  /* MAIN AREA sits next to (or full-width when collapsed) */
  
  
    .dashboard-main{
    flex:1;
    margin-left:var(--sidebar-width);
    padding:1rem; /* Add normal spacing */
    transition:var(--transition);
}


  body.sidebar-collapsed .dashboard-main{ margin-left:0; }

  /* Floating reopen button; appears below navbar when collapsed */
  .sidebar-fab{
    position:fixed; top:calc(var(--site-header-height) + 12px); left:16px;
    width:44px;height:44px;border-radius:50%;display:grid;place-items:center;border:none;
    background:#fff;color:#1e293b; box-shadow:var(--shadow-lg);
    z-index:950; transition:var(--transition);
    opacity:0; pointer-events:none;
  }
  body.sidebar-collapsed .sidebar-fab{opacity:1;pointer-events:auto}
  .sidebar-fab:hover{transform:translateY(-1px)}
</style>

<!-- Floating button to open when collapsed -->
<button class="sidebar-fab" id="sidebarFab" aria-label="Open sidebar" aria-expanded="false">
  <i class="fas fa-bars"></i>
</button>

<div class="shop-owner-dashboard">
  <!-- SIDEBAR -->
  <aside class="dashboard-sidebar" id="dashboardSidebar" aria-label="Shop owner sidebar">
    <div class="sidebar-header">
      <div class="logo">
        <i class="fas fa-store"></i><span>Shop Manager</span>
      </div>
      <button class="sidebar-toggle" id="closeSidebarBtn" aria-label="Close sidebar" style="background:none;border:none;color:#fff;font-size:1.2rem;cursor:pointer;">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <nav class="sidebar-nav">
      <ul>
        <li class="active">
          <a href="/shop-owner/dashboard"><i class="fas fa-home"></i><span>Dashboard</span></a>
        </li>
        <li>
          <a href="/shop-owner/products"><i class="fas fa-box"></i><span>Products</span><span class="badge">156</span></a>
        </li>
        <li>
          <a href="/shop-owner/orders"><i class="fas fa-shopping-cart"></i><span>Orders</span><span class="badge new">12</span></a>
        </li>
        <li>
          <a href="/shop-owner/inventory"><i class="fas fa-warehouse"></i><span>Inventory</span><span class="badge warning">5</span></a>
        </li>
        <li><a href="/shop-owner/sales"><i class="fas fa-chart-line"></i><span>Sales</span></a></li>
        <li><a href="/shop-owner/customers"><i class="fas fa-users"></i><span>Customers</span><span class="badge">234</span></a></li>
        <li><a href="/shop-owner/reviews"><i class="fas fa-star"></i><span>Reviews</span><span class="badge">45</span></a></li>
        <li class="nav-divider"></li>
        <li><a href="/shop-owner/profile"><i class="fas fa-user"></i><span>Profile</span></a></li>
        <li><a href="/logout" class="logout-link"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
      </ul>
    </nav>
  </aside>

  <!-- MAIN CONTENT -->
  
</div>

<script>
  (function(){
    const body = document.body;
    const fab = document.getElementById('sidebarFab');
    const closeBtn = document.getElementById('closeSidebarBtn');
    const STORAGE_KEY = 'goplay.sidebar.collapsed';

    function setCollapsed(v){
      body.classList.toggle('sidebar-collapsed', v);
      fab?.setAttribute('aria-expanded', v ? 'false' : 'true');
      try{ localStorage.setItem(STORAGE_KEY, v ? '1' : '0'); }catch(e){}
    }
    function toggle(){ setCollapsed(!body.classList.contains('sidebar-collapsed')); }

    fab?.addEventListener('click', toggle);
    closeBtn?.addEventListener('click', toggle);

    // restore preference
    try{
      if(localStorage.getItem(STORAGE_KEY) === '1'){ setCollapsed(true); }
    }catch(e){}

    // esc closes
    document.addEventListener('keydown', e=>{
      if(e.key === 'Escape' && !body.classList.contains('sidebar-collapsed')) setCollapsed(true);
    });

    // expose for legacy calls
    window.toggleSidebar = toggle;
  })();
</script>

<?php
// ---- Shared components (footer) ----
require_once $VIEWS . '/components/footer.php';
?>
