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

        // Get today's schedule for the logged-in user
        $todaySchedule = Schedule::with(['client', 'shift'])
            ->where('user_id', $user->id)
            ->today()
            ->where('status', 'scheduled')
            ->first();

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

        // Get upcoming schedules (next 7 days)
        $upcomingSchedules = Schedule::with(['client', 'shift'])
            ->where('user_id', $user->id)
            ->upcoming()
            ->whereBetween('scheduled_date', [now()->toDateString(), now()->addDays(7)->toDateString()])
            ->orderBy('scheduled_date')
            ->orderBy('start_time')
            ->get();

        return view('staff.dashboard', compact(
            'user',
            'todaySchedule',
            'todayAttendance',
            'isCheckedInToday',
            'latestAttendance',
            'weekAttendances',
            'upcomingSchedules'
        ));
    }
}
