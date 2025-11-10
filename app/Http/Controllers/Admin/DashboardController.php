<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Client;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Admin DashboardController handles admin dashboard display
 *
 * This controller provides comprehensive statistics and analytics:
 * - User count (total staff/instructors)
 * - Today's attendance metrics
 * - Client count
 * - Today's schedules
 * - Recent attendances
 * - 7-day attendance chart data
 * - Staff statistics by type
 */
class DashboardController extends Controller
{
    /**
     * Display the admin dashboard
     *
     * Gathers all dashboard metrics and passes them to the view
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get total number of active users (excluding admins)
        $totalUsers = User::where('status', 'active')
            ->whereIn('user_type', ['instructor', 'office_staff'])
            ->count();

        // Get today's attendance count
        $todayAttendanceCount = Attendance::today()->count();

        // Get total number of active clients
        $totalClients = Client::where('status', 'active')->count();

        // Get total schedules for today
        $todaySchedulesCount = Schedule::today()
            ->where('status', 'scheduled')
            ->count();

        // Get recent attendances (last 10 records with user and schedule info)
        $recentAttendances = Attendance::with(['user', 'schedule', 'shift'])
            ->latest('attendance_date')
            ->take(10)
            ->get();

        // Get today's schedules with user and client information
        $todaySchedules = Schedule::with(['user', 'client', 'shift'])
            ->today()
            ->where('status', 'scheduled')
            ->orderBy('start_time')
            ->get();

        // Get attendance chart data for the last 7 days
        $attendanceChartData = $this->getAttendanceChartData();

        // Get staff statistics by user type
        $staffStatistics = $this->getStaffStatistics();

        return view('admin.dashboard', compact(
            'totalUsers',
            'todayAttendanceCount',
            'totalClients',
            'todaySchedulesCount',
            'recentAttendances',
            'todaySchedules',
            'attendanceChartData',
            'staffStatistics'
        ));
    }

    /**
     * Get attendance chart data for the last 7 days
     *
     * Returns an array with dates and corresponding attendance counts
     * for displaying on a line or bar chart
     *
     * @return array
     */
    private function getAttendanceChartData()
    {
        $data = [];
        $dates = [];
        $counts = [];

        // Generate data for the last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $dates[] = now()->subDays($i)->format('D, M d');

            // Count attendances for this date
            $count = Attendance::whereDate('attendance_date', $date)->count();
            $counts[] = $count;
        }

        $data['dates'] = $dates;
        $data['counts'] = $counts;

        return $data;
    }

    /**
     * Get staff statistics grouped by user type
     *
     * Returns counts of instructors, office staff, and their active/inactive status
     *
     * @return array
     */
    private function getStaffStatistics()
    {
        return [
            // Total instructors (active and inactive)
            'total_instructors' => User::where('user_type', 'instructor')->count(),

            // Active instructors
            'active_instructors' => User::where('user_type', 'instructor')
                ->where('status', 'active')
                ->count(),

            // Inactive instructors
            'inactive_instructors' => User::where('user_type', 'instructor')
                ->where('status', 'inactive')
                ->count(),

            // Total office staff
            'total_office_staff' => User::where('user_type', 'office_staff')->count(),

            // Active office staff
            'active_office_staff' => User::where('user_type', 'office_staff')
                ->where('status', 'active')
                ->count(),

            // Inactive office staff
            'inactive_office_staff' => User::where('user_type', 'office_staff')
                ->where('status', 'inactive')
                ->count(),

            // Total staff (instructors + office staff)
            'total_staff' => User::whereIn('user_type', ['instructor', 'office_staff'])->count(),

            // Attendance rate today (percentage of scheduled staff who checked in)
            'attendance_rate_today' => $this->calculateTodayAttendanceRate(),
        ];
    }

    /**
     * Calculate today's attendance rate
     *
     * Calculates the percentage of scheduled staff who have checked in today
     *
     * @return float
     */
    private function calculateTodayAttendanceRate()
    {
        $scheduledToday = Schedule::today()
            ->where('status', 'scheduled')
            ->count();

        if ($scheduledToday === 0) {
            return 0;
        }

        $attendedToday = Attendance::today()
            ->whereNotNull('check_in')
            ->count();

        return round(($attendedToday / $scheduledToday) * 100, 2);
    }
}
