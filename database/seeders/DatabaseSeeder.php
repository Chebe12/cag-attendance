<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * The order of seeders is important as some tables have foreign key constraints.
     */
    public function run(): void
    {
        // Seed shifts first (no dependencies)
        $this->call(ShiftSeeder::class);

        // Seed users (admin, instructors, and office staff)
        // Must be before QrCodeSeeder since qr_codes has created_by foreign key to users
        $this->call(UserSeeder::class);

        // Seed clients (no dependencies)
        $this->call(ClientSeeder::class);

        // Seed QR codes (depends on users being seeded)
        $this->call(QrCodeSeeder::class);
    }
}
