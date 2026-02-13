<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Customer;
use App\Models\Role;

class MusteriMigrationSeeder extends Seeder
{
    private $report = [];
    private $stats = [
        'total' => 0,
        'successful' => 0,
        'skipped' => 0,
        'errors' => 0
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Müşteri verilerinden kullanıcı oluşturma işlemi başlatılıyor...');

        // musteri.sql'den verileri parse et
        $musteriData = $this->parseMusteriSql();
        $this->command->info('Toplam ' . count($musteriData) . ' müşteri kaydı bulundu.');

        // Duplicate email'leri filtrele (en yüksek id'yi al)
        $filteredData = $this->filterDuplicateEmails($musteriData);
        $this->command->info('Duplicate filtreleme sonrası: ' . count($filteredData) . ' kayıt işlenecek.');

        // Firma Yöneticisi rolünü kontrol et
        $firmaYoneticisiRole = Role::find(3);
        if (!$firmaYoneticisiRole) {
            $this->command->error('Firma Yöneticisi rolü (id=3) bulunamadı!');
            return;
        }

        $this->stats['total'] = count($filteredData);

        // Her müşteri için işlem yap
        DB::beginTransaction();
        try {
            foreach ($filteredData as $musteri) {
                $this->processMusteri($musteri);
            }
            DB::commit();
            $this->command->info('Tüm işlemler başarıyla tamamlandı!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Hata oluştu, işlemler geri alındı: ' . $e->getMessage());
            $this->report[] = [
                'type' => 'error',
                'message' => 'Kritik hata: ' . $e->getMessage()
            ];
        }

        // Rapor oluştur
        $this->generateReport();
        
        // İstatistikleri göster
        $this->displayStats();
    }

    /**
     * musteri.sql dosyasından verileri parse et
     */
    private function parseMusteriSql(): array
    {
        $sqlFile = database_path('../musteri.sql');
        
        if (!file_exists($sqlFile)) {
            $this->command->error('musteri.sql dosyası bulunamadı!');
            return [];
        }

        $content = file_get_contents($sqlFile);
        
        // INSERT INTO satırını bul
        preg_match('/INSERT INTO `musteri`.*?VALUES\s*(.*?);/s', $content, $matches);
        
        if (empty($matches[1])) {
            $this->command->error('INSERT VALUES bulunamadı!');
            return [];
        }

        $valuesString = $matches[1];
        
        // Her bir kayıt için parse et
        preg_match_all('/\(([^)]+)\)(?=,|\s*$)/s', $valuesString, $records);
        
        $musteriData = [];
        
        foreach ($records[1] as $record) {
            // Değerleri ayır
            $values = $this->parseRecordValues($record);
            
            if (count($values) >= 14) {
                $musteriData[] = [
                    'id' => $values[0],
                    'musteri_id' => $values[1],
                    'ad' => $this->cleanValue($values[2]),
                    'soyad' => $this->cleanValue($values[3]),
                    'unvan' => $this->cleanValue($values[4]),
                    'telefon' => $this->cleanValue($values[5]),
                    'eposta' => $this->cleanValue($values[6]),
                    'hash' => $this->cleanValue($values[7]),
                    'sifre' => $this->cleanValue($values[8]),
                    'durum' => $values[9],
                    'tip' => $values[10],
                    'tc' => $this->cleanValue($values[11]),
                    'tarih_kayit' => $this->cleanValue($values[12]),
                    'tarih_onay' => $this->cleanValue($values[13]),
                ];
            }
        }

        return $musteriData;
    }

    /**
     * Kayıt değerlerini parse et
     */
    private function parseRecordValues($record): array
    {
        $values = [];
        $current = '';
        $inString = false;
        $escaped = false;

        for ($i = 0; $i < strlen($record); $i++) {
            $char = $record[$i];

            if ($escaped) {
                $current .= $char;
                $escaped = false;
                continue;
            }

            if ($char === '\\') {
                $escaped = true;
                continue;
            }

            if ($char === "'") {
                $inString = !$inString;
                continue;
            }

            if ($char === ',' && !$inString) {
                $values[] = trim($current);
                $current = '';
                continue;
            }

            $current .= $char;
        }

        if ($current !== '') {
            $values[] = trim($current);
        }

        return $values;
    }

    /**
     * Değeri temizle
     */
    private function cleanValue($value)
    {
        if ($value === 'NULL' || trim($value) === '') {
            return null;
        }
        return $value;
    }

    /**
     * Duplicate email'leri filtrele (en yüksek id'yi al)
     */
    private function filterDuplicateEmails(array $musteriData): array
    {
        $grouped = [];
        
        foreach ($musteriData as $musteri) {
            $email = strtolower($musteri['eposta']);
            
            if (!isset($grouped[$email])) {
                $grouped[$email] = $musteri;
            } else {
                // En yüksek id'yi tut
                if ($musteri['id'] > $grouped[$email]['id']) {
                    $this->report[] = [
                        'type' => 'skipped',
                        'reason' => 'Duplicate email (daha düşük id)',
                        'data' => $grouped[$email]
                    ];
                    $grouped[$email] = $musteri;
                } else {
                    $this->report[] = [
                        'type' => 'skipped',
                        'reason' => 'Duplicate email (daha düşük id)',
                        'data' => $musteri
                    ];
                }
            }
        }
        
        return array_values($grouped);
    }

    /**
     * Müşteri kaydını işle
     */
    private function processMusteri(array $musteri): void
    {
        // Email kontrolü - sistemde var mı?
        if (User::where('email', $musteri['eposta'])->exists()) {
            $this->report[] = [
                'type' => 'skipped',
                'reason' => 'Email sistemde mevcut',
                'data' => $musteri
            ];
            $this->stats['skipped']++;
            return;
        }

        try {
            // Unvan işleme
            $unvan = $musteri['unvan'] ?: 'İsimsiz Firma';
            
            // Firma ID oluştur
            $firmaId = $this->generateFirmaId($unvan);
            
            // Şifre oluştur
            $password = $this->generatePassword($musteri['telefon']);
            
            // Customer oluştur
            $customer = Customer::create([
                'firma_id' => $firmaId,
                'unvan' => $unvan,
                'phone' => $musteri['telefon'] ?: 'Belirtilmemiş',
                'email' => $musteri['eposta'],
                'adres' => 'Belirtilmemiş',
                'vergi_dairesi' => 'Belirtilmemiş',
                'vergi_numarasi' => '0000000000',
                'balance' => 0.00,
            ]);

            // User oluştur
            $userName = trim($musteri['ad'] . ' ' . $musteri['soyad']);
            $user = User::create([
                'name' => $userName,
                'email' => $musteri['eposta'],
                'password' => Hash::make($password),
                'customer_id' => $customer->id,
                'status' => 1, // Onaylanmış
            ]);

            // Firma Yöneticisi rolünü ata (id=3)
            $user->roles()->attach(3);

            $this->report[] = [
                'type' => 'success',
                'data' => [
                    'musteri_id' => $musteri['id'],
                    'name' => $userName,
                    'email' => $musteri['eposta'],
                    'password' => $password,
                    'firma_id' => $firmaId,
                    'unvan' => $unvan,
                    'customer_id' => $customer->id,
                    'user_id' => $user->id,
                ]
            ];
            
            $this->stats['successful']++;
            
            $this->command->info("✓ {$userName} ({$musteri['eposta']}) başarıyla oluşturuldu.");
            
        } catch (\Exception $e) {
            $this->report[] = [
                'type' => 'error',
                'reason' => $e->getMessage(),
                'data' => $musteri
            ];
            $this->stats['errors']++;
            $this->command->error("✗ Hata: {$musteri['ad']} {$musteri['soyad']} - " . $e->getMessage());
        }
    }

    /**
     * Ünvandan firma_id oluştur
     */
    private function generateFirmaId(string $unvan): string
    {
        // Türkçe karakterleri İngilizce'ye çevir
        $replacements = [
            'ş' => 's', 'Ş' => 'S',
            'ğ' => 'g', 'Ğ' => 'G',
            'ü' => 'u', 'Ü' => 'U',
            'ı' => 'i', 'İ' => 'I',
            'ö' => 'o', 'Ö' => 'O',
            'ç' => 'c', 'Ç' => 'C',
        ];
        
        $normalized = str_replace(array_keys($replacements), array_values($replacements), $unvan);
        
        // Sadece harfleri al
        $letters = preg_replace('/[^a-zA-Z]/', '', $normalized);
        
        // İlk 4 harfi al, büyük harfe çevir
        $prefix = strtoupper(substr($letters, 0, 4));
        
        // Eğer 4 karakterden az ise, 'X' ile doldur
        $prefix = str_pad($prefix, 4, 'X');
        
        // Unique firma_id bul
        $counter = 1;
        do {
            $firmaId = $prefix . str_pad($counter, 3, '0', STR_PAD_LEFT);
            $counter++;
        } while (Customer::where('firma_id', $firmaId)->exists());
        
        return $firmaId;
    }

    /**
     * Telefon numarasından şifre oluştur
     */
    private function generatePassword(?string $telefon): string
    {
        if (!$telefon) {
            return '12345678';
        }
        
        // Sadece rakamları al
        $digits = preg_replace('/[^0-9]/', '', $telefon);
        
        // İlk 8 rakamı al
        $password = substr($digits, 0, 8);
        
        // 8 karakterden az ise varsayılan şifreyi kullan
        if (strlen($password) < 8) {
            return '12345678';
        }
        
        return $password;
    }

    /**
     * Rapor dosyası oluştur
     */
    private function generateReport(): void
    {
        $reportPath = storage_path('logs/musteri-migration-report.txt');
        
        // Klasör yoksa oluştur
        if (!file_exists(dirname($reportPath))) {
            mkdir(dirname($reportPath), 0755, true);
        }
        
        $content = "=================================================\n";
        $content .= "MÜŞTERİ VERİLERİNDEN KULLANICI OLUŞTURMA RAPORU\n";
        $content .= "=================================================\n";
        $content .= "Tarih: " . date('Y-m-d H:i:s') . "\n\n";
        
        // İstatistikler
        $content .= "İSTATİSTİKLER\n";
        $content .= "-------------\n";
        $content .= "Toplam İşlenen: {$this->stats['total']}\n";
        $content .= "Başarılı: {$this->stats['successful']}\n";
        $content .= "Atlanan: {$this->stats['skipped']}\n";
        $content .= "Hatalı: {$this->stats['errors']}\n\n";
        
        // Başarılı kayıtlar
        $content .= "=================================================\n";
        $content .= "BAŞARILI KAYITLAR\n";
        $content .= "=================================================\n\n";
        
        foreach ($this->report as $item) {
            if ($item['type'] === 'success') {
                $data = $item['data'];
                $content .= "Müşteri ID: {$data['musteri_id']}\n";
                $content .= "Ad Soyad: {$data['name']}\n";
                $content .= "Email: {$data['email']}\n";
                $content .= "Şifre: {$data['password']}\n";
                $content .= "Firma ID: {$data['firma_id']}\n";
                $content .= "Ünvan: {$data['unvan']}\n";
                $content .= "Customer ID: {$data['customer_id']}\n";
                $content .= "User ID: {$data['user_id']}\n";
                $content .= "Rol: Firma Yöneticisi (id=3)\n";
                $content .= "-------------------------------------------------\n\n";
            }
        }
        
        // Atlanan kayıtlar
        $content .= "=================================================\n";
        $content .= "ATLANAN KAYITLAR\n";
        $content .= "=================================================\n\n";
        
        foreach ($this->report as $item) {
            if ($item['type'] === 'skipped') {
                $data = $item['data'];
                $content .= "Müşteri ID: {$data['id']}\n";
                $content .= "Ad Soyad: {$data['ad']} {$data['soyad']}\n";
                $content .= "Email: {$data['eposta']}\n";
                $content .= "Sebep: {$item['reason']}\n";
                $content .= "-------------------------------------------------\n\n";
            }
        }
        
        // Hatalı kayıtlar
        if ($this->stats['errors'] > 0) {
            $content .= "=================================================\n";
            $content .= "HATALI KAYITLAR\n";
            $content .= "=================================================\n\n";
            
            foreach ($this->report as $item) {
                if ($item['type'] === 'error') {
                    if (isset($item['data'])) {
                        $data = $item['data'];
                        $content .= "Müşteri ID: {$data['id']}\n";
                        $content .= "Ad Soyad: {$data['ad']} {$data['soyad']}\n";
                        $content .= "Email: {$data['eposta']}\n";
                    }
                    $content .= "Hata: {$item['reason']}\n";
                    if (isset($item['message'])) {
                        $content .= "Detay: {$item['message']}\n";
                    }
                    $content .= "-------------------------------------------------\n\n";
                }
            }
        }
        
        file_put_contents($reportPath, $content);
        
        $this->command->info("\nDetaylı rapor oluşturuldu: {$reportPath}");
    }

    /**
     * İstatistikleri göster
     */
    private function displayStats(): void
    {
        $this->command->info("\n=================================================");
        $this->command->info("İŞLEM TAMAMLANDI");
        $this->command->info("=================================================");
        $this->command->info("Toplam İşlenen: {$this->stats['total']}");
        $this->command->info("Başarılı: {$this->stats['successful']}");
        $this->command->info("Atlanan: {$this->stats['skipped']}");
        $this->command->info("Hatalı: {$this->stats['errors']}");
        $this->command->info("=================================================\n");
    }
}
