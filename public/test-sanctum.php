<?php
// Sanctum test dosyası
require __DIR__ . '/../vendor/autoload.php';

echo "=== SANCTUM TEST ===\n\n";

// 1. HasApiTokens trait var mı?
echo "1. HasApiTokens trait kontrolü:\n";
if (trait_exists('Laravel\Sanctum\HasApiTokens')) {
    echo "   ✅ Laravel\Sanctum\HasApiTokens VAR\n";
} else {
    echo "   ❌ Laravel\Sanctum\HasApiTokens YOK\n";
}

// 2. Laravel bootstrap
echo "\n2. Laravel bootstrap:\n";
try {
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    echo "   ✅ Laravel bootstrap tamamlandı\n";
} catch (Exception $e) {
    echo "   ❌ Laravel bootstrap başarısız: " . $e->getMessage() . "\n";
} 
  
// 3. createToken metodu var mı?
echo "\n3. createToken metodu kontrolü:\n";
$reflection = new ReflectionClass('Laravel\Sanctum\HasApiTokens');
$methods = $reflection->getMethods();
$hasCreateToken = false;
foreach ($methods as $method) {
    if ($method->getName() === 'createToken') {
        $hasCreateToken = true;
        break;
    }
}

if ($hasCreateToken) {
    echo "   ✅ createToken metodu VAR\n";
} else {
    echo "   ❌ createToken metodu YOK\n";
}

// 4. User class'ı trait'i kullanıyor mu?
echo "\n4. User class trait kullanımı:\n";
try {
    $userReflection = new ReflectionClass('App\Models\User');
    $traits = class_uses_recursive('App\Models\User');
    echo "   Kullanılan tüm trait'ler (recursive): " . implode(', ', array_keys($traits)) . "\n";
    if (in_array('Laravel\Sanctum\HasApiTokens', array_keys($traits))) {
        echo "   ✅ HasApiTokens trait kullanılıyor\n";
    } else {
        echo "   ❌ HasApiTokens trait kullanılmıyor\n";
    }
} catch (Exception $e) {
    echo "   ❌ Hata: " . $e->getMessage() . "\n";
}

// 5. Composer autoload güncel mi?
echo "\n5. Composer autoload kontrolü:\n";
$composerLockTime = filemtime(__DIR__ . '/../composer.lock');
$autoloadTime = filemtime(__DIR__ . '/../vendor/autoload.php');
echo "   composer.lock: " . date('Y-m-d H:i:s', $composerLockTime) . "\n";
echo "   autoload.php: " . date('Y-m-d H:i:s', $autoloadTime) . "\n";
if ($autoloadTime >= $composerLockTime) {
    echo "   ✅ Autoload güncel\n";
} else {
    echo "   ⚠️ Autoload eski olabilir, 'composer dump-autoload' çalıştırın\n";
}

echo "\n=== TEST BİTTİ ===\n";

