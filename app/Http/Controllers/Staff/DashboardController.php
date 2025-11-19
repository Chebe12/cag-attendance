<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Staff DashboardController handles staff member dashboard display
 *
 * This controller provides personalized information:
 * - Today's schedule for logged-in user
 * - Check-in status (if already checked in today)
 * - Latest attendance record
 * - This week's attendance history
 * - Upcoming schedules (next 7 days)
 */
class DashboardController extends Controller
{
    /**
     * Display the staff dashboard
     *
     * Gathers personalized metrics and passes them to the view
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get the authenticated user
        $user = Auth::user();

        // Get active schedule category
        $activeCategory = \App\Models\ScheduleCategory::where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        // Get today's day of the week (lowercase)
        $todayDayOfWeek = strtolower(now()->format('l'));

        // Get today's schedule for the logged-in user based on day of week
        $todaySchedule = null;
        if ($activeCategory) {
            $todaySchedule = Schedule::with(['client', 'shift', 'category'])
                ->where('user_id', $user->id)
                ->where('category_id', $activeCategory->id)
                ->where('is_recurring', true)
                ->where('day_of_week', $todayDayOfWeek)
                ->where('draft_status', 'published')
                ->whereIn('status', ['scheduled', 'draft', 'pending'])
                ->first();
        }

        // Get today's attendance record (if exists)
        $todayAttendance = Attendance::with(['schedule', 'shift'])
            ->where('user_id', $user->id)
            ->today()
            ->first();

        // Check if user has already checked in today
        $isCheckedInToday = $todayAttendance && $todayAttendance->check_in !== null;

        // Get the latest attendance record (for quick reference)
        $latestAttendance = Attendance::with(['schedule', 'shift'])
            ->where('user_id', $user->id)
            ->latest('attendance_date')
            ->first();

        // Get this week's attendance history
        $weekAttendances = Attendance::with(['schedule', 'shift'])
            ->where('user_id', $user->id)
            ->thisWeek()
            ->orderBy('attendance_date', 'desc')
            ->get();

        // Get upcoming schedules (next 6 days based on day of week)
        $upcomingSchedules = collect();
        if ($activeCategory) {
            $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            $todayIndex = array_search($todayDayOfWeek, $daysOfWeek);

            // Get schedules for the next 6 days
            for ($i = 1; $i <= 6; $i++) {
                $nextDayIndex = ($todayIndex + $i) % 7;
                $nextDay = $daysOfWeek[$nextDayIndex];

                $daySchedules = Schedule::with(['client', 'shift', 'category'])
                    ->where('user_id', $user->id)
                    ->where('category_id', $activeCategory->id)
                    ->where('is_recurring', true)
                    ->where('day_of_week', $nextDay)
                    ->where('draft_status', 'published')
                    ->whereIn('status', ['scheduled', 'draft', 'pending'])
                    ->orderBy('start_time')
                    ->get();

                // Add day info to each schedule
                foreach ($daySchedules as $schedule) {
                    $schedule->display_day = ucfirst($nextDay);
                    $schedule->days_until = $i;
                    $upcomingSchedules->push($schedule);
                }
            }
        }

        // Calculate stats
        $stats = [
            'month_attendance' => Attendance::where('user_id', $user->id)
                ->whereMonth('attendance_date', now()->month)
                ->count(),
            'on_time_percentage' => 95, // Placeholder
            'total_hours' => Attendance::where('user_id', $user->id)
                ->whereMonth('attendance_date', now()->month)
                ->get()
                ->sum(function($a) {
                    if ($a->check_in && $a->check_out) {
                        return \Carbon\Carbon::parse($a->check_in)->diffInHours(\Carbon\Carbon::parse($a->check_out));
                    }
                    return 0;
                })
        ];

        return view('staff.dashboard', compact(
            'user',
            'todaySchedule',
            'todayAttendance',
            'isCheckedInToday',
            'latestAttendance',
            'weekAttendances',
            'upcomingSchedules',
            'stats'
        ));
    }
}
