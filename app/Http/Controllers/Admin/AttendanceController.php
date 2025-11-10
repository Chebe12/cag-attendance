<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Admin AttendanceController
 *
 * Manages attendance viewing and monitoring for administrators
 */
class AttendanceController extends Controller
{
    /**
     * Display a listing of all attendance records
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Attendance::with(['user', 'qrCode'])
            ->latest('check_in_time');

        // Filter by user if specified
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date if specified
        if ($request->has('date') && $request->date) {
            $query->whereDate('check_in_time', $request->date);
        }

        // Filter by date range if specified
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('check_in_time', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('check_in_time', '<=', $request->end_date);
        }

        $attendances = $query->paginate(20);

        // Get all users for filter dropdown
        $users = User::where('user_type', '!=', 'admin')
            ->orderBy('firstname')
            ->get();

        return view('admin.attendance.index', compact('attendances', 'users'));
    }

    /**
     * Display the specified attendance record
     *
     * @param Attendance $attendance
     * @return \Illuminate\View\View
     */
    public function show(Attendance $attendance)
    {
        $attendance->load(['user', 'qrCode', 'schedule']);

        return view('admin.attendance.show', compact('attendance'));
    }

    /**
     * Delete an attendance record
     *
     * @param Attendance $attendance
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Attendance $attendance)
    {
        $userName = $attendance->user->firstname . ' ' . $attendance->user->lastname;

        $attendance->delete();

        return redirect()->route('admin.attendance.index')
            ->with('success', "Attendance record for {$userName} deleted successfully.");
    }
}
