<?php
// OPcache temizleme dosyası

echo "=== OPCACHE TEMİZLEME ===\n\n";

// 1. OPcache aktif mi?
if (function_exists('opcache_get_status')) {
    $status = opcache_get_status();
    if ($status !== false) {
        echo "✅ OPcache aktif\n";
        echo "   Kullanılan bellek: " . round($status['memory_usage']['used_memory'] / 1024 / 1024, 2) . " MB\n";
        echo "   Boş bellek: " . round($status['memory_usage']['free_memory'] / 1024 / 1024, 2) . " MB\n";
        echo "   Cache'lenen dosya sayısı: " . $status['opcache_statistics']['num_cached_scripts'] . "\n\n";
        
        // OPcache'i sıfırla
        if (opcache_reset()) {
            echo "✅ OPcache başarıyla temizlendi!\n\n";
            
            // Tekrar kontrol
            $newStatus = opcache_get_status();
            echo "Yeni durum:\n";
            echo "   Cache'lenen dosya sayısı: " . $newStatus['opcache_statistics']['num_cached_scripts'] . "\n";
        } else {
            echo "❌ OPcache temizlenemedi\n";
        }
    } else {
        echo "⚠️ OPcache devre dışı\n";
    }
} else {
    echo "⚠️ OPcache yüklü değil\n";
}

echo "\n=== İŞLEM BİTTİ ===\n";
echo "\nŞimdi test-sanctum.php dosyasını tekrar çalıştırın:\n";
echo "https://b2b.kenalbum.com.tr/test-sanctum.php\n";

