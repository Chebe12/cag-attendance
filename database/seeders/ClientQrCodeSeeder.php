<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Client;
use App\Models\QrCode;

class ClientQrCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates a unique QR code for each client.
     */
    public function run(): void
    {
        // Get the admin user ID
        $adminUserId = DB::table('users')
            ->where('user_type', 'admin')
            ->where('email', 'admin@caglobal.com')
            ->value('id') ?? 1;

        // Get all active clients
        $clients = Client::where('status', 'active')->get();

        if ($clients->isEmpty()) {
            $this->command->warn('No active clients found. Please seed clients first.');
            return;
        }

        $this->command->info('Creating QR codes for ' . $clients->count() . ' clients...');

        foreach ($clients as $client) {
            // Check if QR code already exists for this client
            $existingQrCode = QrCode::where('metadata->client_id', $client->id)->first();

            if ($existingQrCode) {
                $this->command->warn("QR code already exists for client: {$client->name}");
                continue;
            }

            // Generate unique code for this client
            $code = 'CLIENT-' . strtoupper($client->code ?? str_replace(' ', '-', $client->name)) . '-' . strtoupper(bin2hex(random_bytes(4)));

            // Create QR code record
            QrCode::create([
                'code' => $code,
                'qr_image_path' => '/qr-codes/client-' . $client->id . '.png',
                'type' => 'permanent',
                'valid_from' => now()->toDateString(),
                'valid_until' => null, // Permanent, no expiration
                'is_active' => true,
                'scan_count' => 0,
                'metadata' => json_encode([
                    'client_id' => $client->id,
                    'client_name' => $client->name,
                    'label' => $client->name . ' QR Code',
                    'description' => 'Attendance QR code for ' . $client->name,
                    'location' => $client->address ?? 'Client Site',
                    'usage' => 'For instructors visiting this client',
                    'type' => 'client_visit',
                ]),
                'created_by' => $adminUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info("✓ Created QR code for: {$client->name}");
        }

        $this->command->info('Client QR codes created successfully!');
    }
}
