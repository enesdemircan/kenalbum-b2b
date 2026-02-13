<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use App\Models\Route as RouteModel;
use Illuminate\Support\Str;

class ImportRoutesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'route:import {--group=admin : Route group to import} {--force : Force update existing routes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import routes from web.php to routes table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Route import işlemi başlatılıyor...');
        
        $group = $this->option('group');
        $force = $this->option('force');
        
        $routes = $this->getRoutesByGroup($group);
        
        if (empty($routes)) {
            $this->warn("'{$group}' grubunda route bulunamadı.");
            return 1;
        }
        
        $this->info(count($routes) . " adet route bulundu.");
        
        $imported = 0;
        $updated = 0;
        $skipped = 0;
        
        foreach ($routes as $route) {
            $result = $this->importRoute($route, $force);
            
            switch ($result) {
                case 'imported':
                    $imported++;
                    break;
                case 'updated':
                    $updated++;
                    break;
                case 'skipped':
                    $skipped++;
                    break;
            }
        }
        
        $this->info("İşlem tamamlandı!");
        $this->info("- Yeni eklenen: {$imported}");
        $this->info("- Güncellenen: {$updated}");
        $this->info("- Atlanan: {$skipped}");
        
        return 0;
    }
    
    /**
     * Belirli gruba ait route'ları getir
     */
    private function getRoutesByGroup($group)
    {
        $routes = [];
        $routeCollection = Route::getRoutes();
        
        foreach ($routeCollection as $route) {
            $uri = $route->uri();
            $methods = $route->methods();
            $name = $route->getName();
            
            // Admin route'larını filtrele
            if ($group === 'admin' && Str::startsWith($uri, 'admin')) {
                foreach ($methods as $method) {
                    if ($method !== 'HEAD') {
                        $routes[] = [
                            'name' => $name,
                            'uri' => $uri,
                            'method' => $method,
                            'group' => $group,
                            'description' => $this->generateDescription($name, $uri, $method)
                        ];
                    }
                }
            }
            
            // Customer route'larını filtrele
            if ($group === 'customer' && (Str::startsWith($uri, 'profile') || Str::startsWith($uri, 'cart') || Str::startsWith($uri, 'panel'))) {
                foreach ($methods as $method) {
                    if ($method !== 'HEAD') {
                        $routes[] = [
                            'name' => $name,
                            'uri' => $uri,
                            'method' => $method,
                            'group' => $group,
                            'description' => $this->generateDescription($name, $uri, $method)
                        ];
                    }
                }
            }
            
            // Frontend route'larını filtrele
            if ($group === 'frontend' && !Str::startsWith($uri, 'admin') && !Str::startsWith($uri, 'profile') && !Str::startsWith($uri, 'cart') && !Str::startsWith($uri, 'panel')) {
                foreach ($methods as $method) {
                    if ($method !== 'HEAD') {
                        $routes[] = [
                            'name' => $name,
                            'uri' => $uri,
                            'method' => $method,
                            'group' => $group,
                            'description' => $this->generateDescription($name, $uri, $method)
                        ];
                    }
                }
            }
        }
        
        return $routes;
    }
    
    /**
     * Route açıklaması oluştur
     */
    private function generateDescription($name, $uri, $method)
    {
        if (!$name) {
            return "{$method} {$uri}";
        }
        
        // Route name'den açıklama oluştur
        $parts = explode('.', $name);
        $action = end($parts);
        
        $descriptions = [
            'index' => 'Listeleme',
            'create' => 'Oluşturma Formu',
            'store' => 'Kaydetme',
            'show' => 'Görüntüleme',
            'edit' => 'Düzenleme Formu',
            'update' => 'Güncelleme',
            'destroy' => 'Silme',
            'import' => 'İçe Aktarma',
            'by-group' => 'Grupa Göre Filtreleme',
            'toggle-status' => 'Durum Değiştirme',
            'assign-role' => 'Rol Atama',
            'remove-role' => 'Rol Kaldırma',
            'update-role-permissions' => 'Rol İzinlerini Güncelleme',
            'update-status' => 'Durum Güncelleme',
            'update-cart-status' => 'Sepet Durumu Güncelleme',
            'customization' => 'Özelleştirme',
            'delete-image' => 'Resim Silme',
            'upload-image' => 'Resim Yükleme',
            'hierarchical' => 'Hiyerarşik Görünüm',
            'children' => 'Alt Parametreler',
            'details' => 'Detaylar',
            'update-hierarchy' => 'Hiyerarşi Güncelleme',
            'get-params' => 'Parametreleri Getir',
            'get-parents' => 'Üst Parametreler',
            'list' => 'Liste',
            'add' => 'Ekleme',
            'existing' => 'Mevcut Olanlar',
            'personels' => 'Personel Yönetimi',
            'addresses' => 'Adres Yönetimi',
            'detail' => 'Detay Görüntüleme',
            'ordercreate' => 'Sipariş Oluşturma',
            'extra-sales' => 'Ek Satış',
            'count' => 'Sayım',
            'checkout' => 'Ödeme',
            'complete' => 'Tamamlama',
            'order' => 'Sipariş',
            'add-extra' => 'Ek Ürün Ekleme',
            'quantity' => 'Miktar Güncelleme',
            'remove' => 'Kaldırma',
            'clear' => 'Temizleme',
            'download' => 'İndirme',
            'download-customization' => 'Özelleştirme İndirme',
            'customization-params' => 'Özelleştirme Parametreleri',
            'customization-children' => 'Özelleştirme Alt Parametreleri',
            'customization-file' => 'Özelleştirme Dosyası',
            'dashboard' => 'Dashboard',
            'main-categories' => 'Ana Kategoriler',
            'products' => 'Ürünler',
            'customization-categories' => 'Özelleştirme Kategorileri',
            'customization-params' => 'Özelleştirme Parametreleri',
            'users' => 'Kullanıcılar',
            'roles' => 'Roller',
            'routes' => 'Route\'lar',
            'discount-groups' => 'İndirim Grupları',
            'order-statuses' => 'Sipariş Durumları',
            'site-settings' => 'Site Ayarları',
            'sliders' => 'Slider\'lar',
            'bank-accounts' => 'Banka Hesapları',
            'customers' => 'Müşteriler',
            'pages' => 'Sayfalar',
            'orders' => 'Siparişler',
            'product-customization-params' => 'Ürün Özelleştirme Parametreleri',
            'product-details' => 'Ürün Detayları',
            'customization-params-customers' => 'Özelleştirme Parametreleri Müşterileri',
            'panel' => 'Panel',
            'home' => 'Ana Sayfa',
            'category' => 'Kategori',
            'page' => 'Sayfa',
            'cart' => 'Sepet',
            'profile' => 'Profil',
            'login' => 'Giriş',
            'register' => 'Kayıt',
            'logout' => 'Çıkış',
            'approval' => 'Onay',
        ];
        
        $description = '';
        foreach ($parts as $part) {
            if (isset($descriptions[$part])) {
                $description = $descriptions[$part];
                break;
            }
        }
        
        if (!$description) {
            $description = ucfirst(str_replace(['-', '_'], ' ', $action));
        }
        
        return $description;
    }
    
    /**
     * Route'u veritabanına import et
     */
    private function importRoute($routeData, $force = false)
    {
        $existingRoute = RouteModel::where('name', $routeData['name'])
                                 ->where('method', $routeData['method'])
                                 ->first();
        
        if ($existingRoute) {
            if ($force) {
                $existingRoute->update([
                    'uri' => $routeData['uri'],
                    'group' => $routeData['group'],
                    'description' => $routeData['description'],
                    'is_active' => true
                ]);
                
                $this->line("✓ Güncellendi: {$routeData['name']} ({$routeData['method']})");
                return 'updated';
            } else {
                $this->line("- Atlanan: {$routeData['name']} ({$routeData['method']}) - Zaten mevcut");
                return 'skipped';
            }
        } else {
            RouteModel::create([
                'name' => $routeData['name'],
                'uri' => $routeData['uri'],
                'method' => $routeData['method'],
                'group' => $routeData['group'],
                'description' => $routeData['description'],
                'is_active' => true
            ]);
            
            $this->line("✓ Eklendi: {$routeData['name']} ({$routeData['method']})");
            return 'imported';
        }
    }
} 