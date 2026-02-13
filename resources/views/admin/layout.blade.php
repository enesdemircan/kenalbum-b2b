<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Paneli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    @yield('styles')
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="/admin">Ken Albüm</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
      
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.products.index') }}"><i class="bi bi-box"></i> Ürünler</a></li>
               
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.users.index') }}"><i class="bi bi-people"></i> Kullanıcılar</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.customers.index') }}"><i class="bi bi-building"></i> Firmalar</a></li>
       
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.discount-groups.index') }}"><i class="bi bi-percent"></i> İndirim Grupları</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.orders.index') }}"><i class="bi bi-cart"></i> Siparişler</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.barcode.search') }}"><i class="bi bi-upc-scan"></i> Barcode Arama</a></li>
              
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-gear"></i> Sabitler
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item"  href="{{ route('admin.main-categories.index') }}"><i class="bi bi-tags"></i> Ana Ürün Kategorileri</a></li>
                      
                        <li><a class="dropdown-item" href="{{ route('admin.customization-categories.index') }}"><i class="bi bi-list-check"></i> Özelleştirme Kategorileri</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.order-statuses.index') }}"><i class="bi bi-flag"></i> Sipariş Durumları</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.roles.index') }}"><i class="bi bi-shield"></i> Kullanıcı Rolleri</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.routes.index') }}"><i class="bi bi-diagram-3"></i> Route Yönetimi</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-sliders"></i> Ayarlar
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('admin.pages.index') }}"><i class="bi bi-file-text"></i> Sayfalar</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.site-settings.index') }}"><i class="bi bi-gear-wide-connected"></i> Site Ayarları</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.sliders.index') }}"><i class="bi bi-images"></i> Slider</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.bank-accounts.index') }}"><i class="bi bi-bank"></i> IBAN Hesapları</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.s3-files.index') }}"><i class="bi bi-cloud"></i> S3 Dosyaları</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.cart-files.index') }}"><i class="bi bi-file-earmark"></i> Cart Dosyaları</a></li>
                    </ul>
                </li>
                
                <!-- Kullanıcı menüsü -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i> {{ Auth::user()->name ?? 'Admin' }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right"></i> Çıkış Yap
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container-fluid">
    @yield('content')
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html> 