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
                'start_time' => '08:30:00',
                'end_time' => '11:00:00',
                'color' => '#10B981',
                'description' => 'Morning shift from 8:30 AM to 11:00 AM',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Afternoon Shift',
                'start_time' => '12:00:00',
                'end_time' => '14:30:00',
                'color' => '#3B82F6',
                'description' => 'Afternoon shift from 12:00 PM to 2:30 PM',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
