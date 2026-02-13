<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserAddress;

class UserAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        
        if ($user) {
            UserAddress::create([
                'user_id' => $user->id,
                'title' => 'Ev Adresi',
                'ad' => 'Ahmet',
                'soyad' => 'Yılmaz',
                'adres' => 'Atatürk Mahallesi, Cumhuriyet Caddesi No:123, Ankara',
                'telefon' => '0555 123 45 67'
            ]);

            UserAddress::create([
                'user_id' => $user->id,
                'title' => 'İş Adresi',
                'ad' => 'Ahmet',
                'soyad' => 'Yılmaz',
                'adres' => 'Kızılay Mahallesi, İstiklal Caddesi No:456, Ankara',
                'telefon' => '0555 987 65 43'
            ]);
        }
    }
}
