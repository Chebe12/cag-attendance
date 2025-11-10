<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QrCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the admin user ID (should be 1)
        $adminUserId = DB::table('users')
            ->where('user_type', 'admin')
            ->where('email', 'admin@caglobal.com')
            ->value('id') ?? 1;

        DB::table('qr_codes')->insert([
            [
                'code' => 'DAILY-2025-11-10-' . strtoupper(bin2hex(random_bytes(4))),
                'qr_image_path' => '/qr-codes/daily-2025-11-10.png',
                'type' => 'daily',
                'valid_from' => now()->toDateString(),
                'valid_until' => now()->toDateString(),
                'is_active' => true,
                'scan_count' => 0,
                'metadata' => json_encode([
                    'description' => 'Daily attendance QR code for November 10, 2025',
                    'location' => 'Main Office',
                    'batch' => 'DAILY-BATCH-001',
                ]),
                'created_by' => $adminUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'WEEKLY-2025-W45-' . strtoupper(bin2hex(random_bytes(4))),
                'qr_image_path' => '/qr-codes/weekly-2025-w45.png',
                'type' => 'weekly',
                'valid_from' => now()->startOfWeek()->toDateString(),
                'valid_until' => now()->endOfWeek()->toDateString(),
                'is_active' => true,
                'scan_count' => 0,
                'metadata' => json_encode([
                    'description' => 'Weekly attendance QR code for Week 45 of 2025',
                    'location' => 'All Locations',
                    'batch' => 'WEEKLY-BATCH-001',
                ]),
                'created_by' => $adminUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'OFFICE-GENERAL-' . strtoupper(bin2hex(random_bytes(6))),
                'qr_image_path' => '/qr-codes/office-general.png',
                'type' => 'permanent',
                'valid_from' => now()->toDateString(),
                'valid_until' => null,
                'is_active' => true,
                'scan_count' => 0,
                'metadata' => json_encode([
                    'label' => 'Office General QR Code',
                    'description' => 'General office attendance QR code for all staff',
                    'location' => 'Main Office - Reception',
                    'batch' => 'OFFICE-GENERAL-001',
                    'notes' => 'This QR code is for office days - staff working from office should scan this',
                    'usage' => 'For all staff members on office days (not client visits)',
                ]),
                'created_by' => $adminUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
