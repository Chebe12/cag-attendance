<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing data
        $users = DB::table('users')->where('user_type', 'instructor')->pluck('id')->toArray();
        $clients = DB::table('clients')->where('status', 'active')->pluck('id')->toArray();
        $shifts = DB::table('shifts')->where('is_active', true)->pluck('id')->toArray();
        $adminId = DB::table('users')->where('user_type', 'admin')->first()->id ?? 1;

        if (empty($users) || empty($clients) || empty($shifts)) {
            $this->command->error('Please run UserSeeder, ClientSeeder, and ShiftSeeder first!');
            return;
        }

        // Define session times
        $sessions = ['morning', 'afternoon'];
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

        $schedules = [];
        $attendances = [];

        // Create schedules for the past 2 weeks
        $startDate = Carbon::now()->subWeeks(2)->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();

        $scheduleId = 1;

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            // Skip weekends for demo purposes
            if ($date->isWeekend()) {
                continue;
            }

            $dayOfWeek = strtolower($date->format('l'));

            // Create 2-3 schedules per day
            $schedulesPerDay = rand(2, 3);
            for ($i = 0; $i < $schedulesPerDay; $i++) {
                $userId = $users[array_rand($users)];
                $clientId = $clients[array_rand($clients)];
                $shiftId = $shifts[array_rand($shifts)];
                $session = $sessions[array_rand($sessions)];

                // Get session times
                $sessionTimes = [
                    'morning' => ['start' => '08:30:00', 'end' => '11:00:00'],
                    'afternoon' => ['start' => '12:00:00', 'end' => '14:30:00'],
                ];

                $times = $sessionTimes[$session];

                $schedule = [
                    'id' => $scheduleId,
                    'user_id' => $userId,
                    'client_id' => $clientId,
                    'shift_id' => $shiftId,
                    'scheduled_date' => $date->format('Y-m-d'),
                    'start_time' => $times['start'],
                    'end_time' => $times['end'],
                    'day_of_week' => $dayOfWeek,
                    'session_time' => $session,
                    'is_recurring' => false,
                    'status' => 'scheduled',
                    'draft_status' => 'published',
                    'notes' => 'Demo schedule for ' . $date->format('M d, Y'),
                    'created_by' => $adminId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $schedules[] = $schedule;

                // Create attendance for past schedules only
                if ($date->lt(Carbon::now())) {
                    $attendance = $this->createAttendance($scheduleId, $userId, $clientId, $shiftId, $date, $times);
                    if ($attendance) {
                        $attendances[] = $attendance;
                    }
                }

                $scheduleId++;
            }
        }

        // Insert schedules
        DB::table('schedules')->insert($schedules);
        $this->command->info('Created ' . count($schedules) . ' demo schedules');

        // Insert attendances
        DB::table('attendances')->insert($attendances);
        $this->command->info('Created ' . count($attendances) . ' demo attendance records');
    }

    /**
     * Create an attendance record with varied statuses
     */
    private function createAttendance($scheduleId, $userId, $clientId, $shiftId, $date, $times)
    {
        // Random status distribution:
        // 70% present, 15% late, 10% absent, 3% half_day, 2% on_leave
        $rand = rand(1, 100);

        if ($rand <= 70) {
            $status = 'present';
        } elseif ($rand <= 85) {
            $status = 'late';
        } elseif ($rand <= 95) {
            $status = 'absent';
        } elseif ($rand <= 98) {
            $status = 'half_day';
        } else {
            $status = 'on_leave';
        }

        // For absent or on_leave, only create basic record
        if (in_array($status, ['absent', 'on_leave'])) {
            return [
                'user_id' => $userId,
                'schedule_id' => $scheduleId,
                'shift_id' => $shiftId,
                'client_id' => $clientId,
                'attendance_date' => $date->format('Y-m-d'),
                'attendance_type' => 'client_visit',
                'check_in' => null,
                'check_out' => null,
                'check_in_location' => null,
                'check_out_location' => null,
                'check_in_ip' => null,
                'check_out_ip' => null,
                'status' => $status,
                'work_duration' => 0,
                'notes' => $status === 'on_leave' ? 'Approved leave' : 'Did not attend',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // For present/late/half_day, create full attendance
        $startTime = Carbon::parse($date->format('Y-m-d') . ' ' . $times['start']);
        $endTime = Carbon::parse($date->format('Y-m-d') . ' ' . $times['end']);

        // Adjust check-in time based on status
        if ($status === 'late') {
            $checkIn = $startTime->copy()->addMinutes(rand(10, 30)); // 10-30 minutes late
        } else {
            $checkIn = $startTime->copy()->subMinutes(rand(0, 10)); // On time or early
        }

        // Adjust check-out and duration based on status
        if ($status === 'half_day') {
            $checkOut = $checkIn->copy()->addMinutes(rand(30, 60)); // Only worked 30-60 minutes
        } else {
            // Normal full shift with some variation
            $scheduledDuration = $startTime->diffInMinutes($endTime);
            $actualDuration = $scheduledDuration + rand(-15, 15); // +/- 15 minutes variation
            $checkOut = $checkIn->copy()->addMinutes($actualDuration);
        }

        $workDuration = $checkIn->diffInMinutes($checkOut);

        // Sample locations
        $locations = [
            'Main Office - Conference Room A',
            'Client Site - Training Room',
            'Branch Office - 2nd Floor',
            'Remote Location',
            'Client Headquarters',
            'Downtown Office - Meeting Room 3',
        ];

        $ips = [
            '192.168.1.100',
            '192.168.1.101',
            '192.168.1.102',
            '10.0.0.50',
            '10.0.0.51',
            '172.16.0.10',
        ];

        $location = $locations[array_rand($locations)];
        $ip = $ips[array_rand($ips)];

        return [
            'user_id' => $userId,
            'schedule_id' => $scheduleId,
            'shift_id' => $shiftId,
            'client_id' => $clientId,
            'attendance_date' => $date->format('Y-m-d'),
            'attendance_type' => 'client_visit',
            'check_in' => $checkIn->format('Y-m-d H:i:s'),
            'check_out' => $checkOut->format('Y-m-d H:i:s'),
            'check_in_location' => $location,
            'check_out_location' => $location,
            'check_in_ip' => $ip,
            'check_out_ip' => $ip,
            'status' => $status,
            'work_duration' => $workDuration,
            'notes' => $status === 'late' ? 'Arrived ' . $checkIn->diffInMinutes($startTime) . ' minutes late' : null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
