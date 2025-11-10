<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('shifts')->insert([
            [
                'name' => 'Morning Shift',
                'start_time' => '07:00:00',
                'end_time' => '12:00:00',
                'color' => '#10B981',
                'description' => 'Early morning shift from 7:00 AM to 12:00 PM',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mid-Morning Shift',
                'start_time' => '10:00:00',
                'end_time' => '14:00:00',
                'color' => '#F59E0B',
                'description' => 'Mid-morning shift from 10:00 AM to 2:00 PM',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Afternoon Shift',
                'start_time' => '13:00:00',
                'end_time' => '18:00:00',
                'color' => '#3B82F6',
                'description' => 'Afternoon shift from 1:00 PM to 6:00 PM',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
