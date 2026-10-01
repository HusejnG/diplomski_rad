<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(EquipmentSeeder::class);

        // Demo nalozi za sve tri uloge (korisno za odbranu/demonstraciju rada).
        User::updateOrCreate(['email' => 'admin@solar.test'], [
            'name' => 'Administrator',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        User::updateOrCreate(['email' => 'projektant@solar.test'], [
            'name' => 'Amina Projektant',
            'password' => bcrypt('password'),
            'role' => 'designer',
            'email_verified_at' => now(),
        ]);

        User::updateOrCreate(['email' => 'korisnik@solar.test'], [
            'name' => 'Test Korisnik',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);
    }
}
