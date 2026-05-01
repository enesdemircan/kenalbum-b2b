<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Paneli') - Ken Albüm</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome & Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <!-- Material Admin CSS -->
    <link href="{{ asset('css/admin-material.css') }}" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #1976d2;
            --secondary-color: #424242;
            --success-color: #388e3c;
            --warning-color: #f57c00;
            --danger-color: #d32f2f;
            --info-color: #0288d1;
            --sidebar-width: 260px;
            --topbar-height: 64px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background: #f5f5f5;
            overflow-x: hidden;
        }
        
        /* Material Card Styles */
        .material-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
        }
        
        .material-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }
        
        .material-card-elevated {
            box-shadow: 0 4px 8px rgba(0,0,0,0.12);
        }
        
        /* Topbar */
        .admin-topbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: var(--topbar-height);
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            z-index: 1000;
            display: flex;
            align-items: center;
            padding: 0 24px;
        }
        
        .topbar-brand {
            font-size: 20px;
            font-weight: 500;
            color: var(--primary-color);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .topbar-brand .material-icons {
            font-size: 28px;
        }
        
        .topbar-menu {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .topbar-icon-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: background 0.2s;
            color: #666;
        }
        
        .topbar-icon-btn:hover {
            background: rgba(0,0,0,0.05);
        }
        
        /* Sidebar */
        .admin-sidebar {
            position: fixed;
            left: 0;
            top: var(--topbar-height);
            width: var(--sidebar-width);
            height: calc(100vh - var(--topbar-height));
            background: #fafafa;
            box-shadow: 2px 0 8px rgba(0,0,0,0.08);
            overflow-y: auto;
            overflow-x: hidden;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 999;
        }
        
        .sidebar-section {
            padding: 8px 0;
        }
        
        .sidebar-section:not(:last-child) {
            border-bottom: 1px solid #e0e0e0;
        }
        
        .sidebar-section-title {
            padding: 12px 16px 8px 16px;
            font-size: 11px;
            font-weight: 600;
            color: #9e9e9e;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .sidebar-menu-item {
            display: flex;
            align-items: center;
            padding: 10px 16px;
            margin: 2px 8px;
            color: #616161;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
            position: relative;
            border-radius: 8px;
            font-size: 14px;
        }
        
        .sidebar-menu-item:hover {
            background: white;
            color: var(--primary-color);
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        }
        
        .sidebar-menu-item.active {
            background: white;
            color: var(--primary-color);
            font-weight: 500;
            box-shadow: 0 2px 6px rgba(25, 118, 210, 0.15);
        }
        
        .sidebar-menu-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 70%;
            background: var(--primary-color);
            border-radius: 0 3px 3px 0;
        }
        
        .sidebar-menu-item .material-icons {
            margin-right: 12px;
            font-size: 22px;
            max-width: 22px;
        }
        
        .sidebar-menu-item span {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .sidebar-menu-item .expand-icon {
            margin-left: auto;
            margin-right: 0;
            font-size: 20px;
            transition: transform 0.3s;
        }
        
        .sidebar-menu-item.expanded .expand-icon {
            transform: rotate(180deg);
        }
        
        .sidebar-submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            background: rgba(0,0,0,0.02);
            border-radius: 8px;
            margin: 0 8px 4px 8px;
        }
        
        .sidebar-submenu.open {
            max-height: 600px;
        }
        
        .sidebar-submenu-item {
            padding: 8px 16px 8px 48px;
            display: flex;
            align-items: center;
            color: #757575;
            text-decoration: none;
            transition: all 0.2s;
            font-size: 13px;
            position: relative;
        }
        
        .sidebar-submenu-item::before {
            content: '';
            position: absolute;
            left: 32px;
            width: 4px;
            height: 4px;
            background: #bdbdbd;
            border-radius: 50%;
        }
        
        .sidebar-submenu-item:hover {
            background: rgba(25, 118, 210, 0.08);
            color: var(--primary-color);
            padding-left: 50px;
        }
        
        .sidebar-submenu-item:hover::before {
            background: var(--primary-color);
        }
        
        .sidebar-submenu-item.active {
            color: var(--primary-color);
            font-weight: 500;
        }
        
        .sidebar-submenu-item.active::before {
            background: var(--primary-color);
            width: 6px;
            height: 6px;
        }
        
        /* Main Content */
        .admin-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--topbar-height);
            padding: 32px;
            min-height: calc(100vh - var(--topbar-height));
        }
        
        /* Page Header */
        .page-header {
            margin-bottom: 32px;
        }
        
        .page-title {
            font-size: 28px;
            font-weight: 400;
            color: #212121;
            margin: 0;
        }
        
        .page-subtitle {
            font-size: 14px;
            color: #757575;
            margin-top: 4px;
        }
        
        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }
        
        .stat-card.primary::before { background: var(--primary-color); }
        .stat-card.success::before { background: var(--success-color); }
        .stat-card.warning::before { background: var(--warning-color); }
        .stat-card.danger::before { background: var(--danger-color); }
        .stat-card.info::before { background: var(--info-color); }
        
        .stat-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateY(-4px);
        }
        
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 16px;
        }
        
        .stat-icon.primary { background: rgba(25, 118, 210, 0.1); color: var(--primary-color); }
        .stat-icon.success { background: rgba(56, 142, 60, 0.1); color: var(--success-color); }
        .stat-icon.warning { background: rgba(245, 124, 0, 0.1); color: var(--warning-color); }
        .stat-icon.danger { background: rgba(211, 47, 47, 0.1); color: var(--danger-color); }
        .stat-icon.info { background: rgba(2, 136, 209, 0.1); color: var(--info-color); }
        
        .stat-value {
            font-size: 32px;
            font-weight: 500;
            color: #212121;
            margin-bottom: 4px;
        }
        
        .stat-label {
            font-size: 14px;
            color: #757575;
            margin-bottom: 8px;
        }
        
        .stat-change {
            font-size: 12px;
            font-weight: 500;
        }
        
        .stat-change.positive { color: var(--success-color); }
        .stat-change.negative { color: var(--danger-color); }
        
        /* Buttons */
        .btn-material {
            padding: 10px 24px;
            border-radius: 4px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.2s;
        }
        
        .btn-material:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }
            
            .admin-sidebar.mobile-open {
                transform: translateX(0);
            }
            
            .admin-content {
                margin-left: 0;
                padding: 16px;
            }
            
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: var(--topbar-height);
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 998;
            }
            
            .sidebar-overlay.show {
                display: block;
            }
        }
        
        /* Scrollbar Styling */
        .admin-sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .admin-sidebar::-webkit-scrollbar-track {
            background: #f5f5f5;
        }
        
        .admin-sidebar::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 3px;
        }
        
        .admin-sidebar::-webkit-scrollbar-thumb:hover {
            background: #999;
        }
    </style>
    
    @yield('styles')
</head>
<body>

<!-- Topbar -->
<div class="admin-topbar">
    <button class="topbar-icon-btn d-md-none" id="sidebarToggle">
        <span class="material-icons">menu</span>
    </button>
    
    <a href="{{ route('admin.dashboard') }}" class="topbar-brand">

        <span>KenPanel v1.0.0</span>
    </a>
    
    <div class="topbar-menu">
        <button class="topbar-icon-btn">
            <span class="material-icons">notifications</span>
        </button>
        
        <div class="dropdown">
            <button class="topbar-icon-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <span class="material-icons">account_circle</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="material-icons" style="font-size:18px;vertical-align:middle">dashboard</i> Dashboard</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="material-icons" style="font-size:18px;vertical-align:middle">logout</i> Çıkış Yap
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Sidebar Overlay for Mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-section">
        <div class="sidebar-section-title">ANA MENÜ</div>
        <a href="{{ route('admin.dashboard') }}" class="sidebar-menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span class="material-icons">dashboard</span>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('admin.orders.index') }}" class="sidebar-menu-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
            <span class="material-icons">shopping_cart</span>
            <span>Siparişler</span>
        </a>
        <a href="{{ route('admin.barcode.search') }}" class="sidebar-menu-item {{ request()->routeIs('admin.barcode.*') ? 'active' : '' }}">
            <span class="material-icons">qr_code_scanner</span>
            <span>Barcode</span>
        </a>
    </div>
    
    <div class="sidebar-section">
        <div class="sidebar-section-title">ÜRÜN & MÜŞTERİ</div>
        <a href="{{ route('admin.products.index') }}" class="sidebar-menu-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
            <span class="material-icons">inventory_2</span>
            <span>Ürünler</span>
        </a>
        <a href="{{ route('admin.main-categories.index') }}" class="sidebar-menu-item {{ request()->routeIs('admin.main-categories.*') ? 'active' : '' }}">
            <span class="material-icons">category</span>
            <span>Kategoriler</span>
        </a>
        <a href="{{ route('admin.customers.index') }}" class="sidebar-menu-item {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
            <span class="material-icons">business</span>
            <span>Firmalar</span>
        </a>
        <a href="{{ route('admin.users.index') }}" class="sidebar-menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <span class="material-icons">people</span>
            <span>Kullanıcılar</span>
        </a>
        <a href="{{ route('admin.discount-groups.index') }}" class="sidebar-menu-item {{ request()->routeIs('admin.discount-groups.*') ? 'active' : '' }}">
            <span class="material-icons">local_offer</span>
            <span>İndirim Grupları</span>
        </a>
    </div>
    
    <div class="sidebar-section">
        <a href="#" class="sidebar-menu-item" data-toggle="submenu" onclick="toggleSubmenu(event, 'sabitlerSubmenu')">
            <span class="material-icons">settings_applications</span>
            <span>Sabitler</span>
            <span class="material-icons expand-icon">expand_more</span>
        </a>
        <div class="sidebar-submenu" id="sabitlerSubmenu">
            <a href="{{ route('admin.customization-categories.index') }}" class="sidebar-submenu-item {{ request()->routeIs('admin.customization-categories.*') ? 'active' : '' }}">Özelleştirme Kategorileri</a>
            <a href="{{ route('admin.order-statuses.index') }}" class="sidebar-submenu-item {{ request()->routeIs('admin.order-statuses.*') ? 'active' : '' }}">Sipariş Durumları</a>
            <a href="{{ route('admin.shipping-methods.index') }}" class="sidebar-submenu-item {{ request()->routeIs('admin.shipping-methods.*') ? 'active' : '' }}">Kargo Metotları</a>
            <a href="{{ route('admin.roles.index') }}" class="sidebar-submenu-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">Kullanıcı Rolleri</a>
            <a href="{{ route('admin.routes.index') }}" class="sidebar-submenu-item {{ request()->routeIs('admin.routes.*') ? 'active' : '' }}">Route Yönetimi</a>
        </div>
    </div>
    
    <div class="sidebar-section">
        <a href="#" class="sidebar-menu-item" data-toggle="submenu" onclick="toggleSubmenu(event, 'ayarlarSubmenu')">
            <span class="material-icons">tune</span>
            <span>Ayarlar</span>
            <span class="material-icons expand-icon">expand_more</span>
        </a>
        <div class="sidebar-submenu" id="ayarlarSubmenu">
            <a href="{{ route('admin.site-settings.index') }}" class="sidebar-submenu-item {{ request()->routeIs('admin.site-settings.*') ? 'active' : '' }}">Site Ayarları</a>
            <a href="{{ route('admin.pages.index') }}" class="sidebar-submenu-item {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">Sayfalar</a>
            <a href="{{ route('admin.sliders.index') }}" class="sidebar-submenu-item {{ request()->routeIs('admin.sliders.*') ? 'active' : '' }}">Slider</a>
            <a href="{{ route('admin.bank-accounts.index') }}" class="sidebar-submenu-item {{ request()->routeIs('admin.bank-accounts.*') ? 'active' : '' }}">IBAN Hesapları</a>
            <a href="{{ route('admin.s3-files.index') }}" class="sidebar-submenu-item {{ request()->routeIs('admin.s3-files.*') ? 'active' : '' }}">S3 Dosyaları</a>
            <a href="{{ route('admin.cart-files.index') }}" class="sidebar-submenu-item {{ request()->routeIs('admin.cart-files.*') ? 'active' : '' }}">Cart Dosyaları</a>
        </div>
    </div>
</aside>

<!-- Main Content -->
<main class="admin-content">
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Sidebar Toggle for Mobile
    const sidebarToggle = document.getElementById('sidebarToggle');
    const adminSidebar = document.getElementById('adminSidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            adminSidebar.classList.toggle('mobile-open');
            sidebarOverlay.classList.toggle('show');
        });
    }
    
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            adminSidebar.classList.remove('mobile-open');
            sidebarOverlay.classList.remove('show');
        });
    }
    
    // Submenu Toggle Function
    function toggleSubmenu(event, submenuId) {
        event.preventDefault();
        const menuItem = event.currentTarget;
        const submenu = document.getElementById(submenuId);
        
        if (submenu) {
            const isOpen = submenu.classList.contains('open');
            
            // Close all other submenus
            document.querySelectorAll('.sidebar-submenu').forEach(item => {
                item.classList.remove('open');
            });
            document.querySelectorAll('.sidebar-menu-item[data-toggle="submenu"]').forEach(item => {
                item.classList.remove('expanded');
            });
            
            // Toggle current submenu
            if (!isOpen) {
                submenu.classList.add('open');
                menuItem.classList.add('expanded');
            }
        }
    }
    
    // Auto-expand submenu if current page is in submenu
    document.addEventListener('DOMContentLoaded', function() {
        const activeSubmenuItems = document.querySelectorAll('.sidebar-submenu-item.active');
        activeSubmenuItems.forEach(item => {
            const submenu = item.closest('.sidebar-submenu');
            if (submenu) {
                submenu.classList.add('open');
                const parentMenuItem = submenu.previousElementSibling;
                if (parentMenuItem && parentMenuItem.hasAttribute('data-toggle')) {
                    parentMenuItem.classList.add('expanded');
                }
            }
        });
    });
</script>
@stack('scripts')
</body>
</html> 