<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call(RoleSeeder::class);
        $this->call(RouteSeeder::class);

        User::firstOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Test User',
            'password' => bcrypt('password'),
        ]);

        // Test kullanıcısına admin rolü ata
        $testUser = User::where('email', 'test@example.com')->first();
        $adminRole = \App\Models\Role::where('name', 'administrator')->first();
        if ($testUser && $adminRole) {
            $testUser->roles()->sync([$adminRole->id]);
        }

        // Ana kategorileri ekle
        $this->call([
            MainCategorySeeder::class,
            CustomizationCategorySeeder::class,
            CustomizationParamSeeder::class,
            ProductSeeder::class,
            CustomizationPivotParamSeeder::class,
            OrderStatusSeeder::class,
        ]);
    }
}
