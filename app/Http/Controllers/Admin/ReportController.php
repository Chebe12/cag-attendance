<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * ReportController handles comprehensive reporting and analytics
 *
 * This controller manages:
 * - Reports dashboard with overview statistics
 * - Attendance reports with advanced filtering
 * - Schedule reports with multi-criteria filtering
 * - Export functionality to Excel and PDF formats
 * - Comprehensive statistics and analytics
 * - IP and location tracking for audit trail
 */
class ReportController extends Controller
{
    /**
     * Display reports dashboard
     *
     * Shows overview of available reports with quick statistics
     * and shortcuts to commonly used report types
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get current month statistics
        $currentMonth = now();

        $stats = [
            // Attendance statistics
            'total_attendance_today' => Attendance::today()->count(),
            'present_today' => Attendance::today()->where('status', 'present')->count(),
            'late_today' => Attendance::today()->where('status', 'late')->count(),
            'absent_today' => Attendance::today()->where('status', 'absent')->count(),

            // Monthly statistics
            'total_attendance_month' => Attendance::thisMonth()->count(),
            'present_month' => Attendance::thisMonth()->where('status', 'present')->count(),
            'late_month' => Attendance::thisMonth()->where('status', 'late')->count(),
            'absent_month' => Attendance::thisMonth()->where('status', 'absent')->count(),

            // Schedule statistics
            'total_schedules_today' => Schedule::today()->count(),
            'completed_schedules_today' => Schedule::today()
                ->whereHas('attendance', function($query) {
                    $query->whereNotNull('check_out');
                })
                ->count(),

            // User statistics
            'total_active_users' => User::active()->count(),
            'total_instructors' => User::active()->instructors()->count(),
            'total_office_staff' => User::active()->officeStaff()->count(),

            // Work hours this month
            'total_work_hours' => Attendance::thisMonth()->sum('work_duration'),
        ];

        // Convert work hours from minutes to hours
        $stats['total_work_hours_formatted'] = round($stats['total_work_hours'] / 60, 2);

        // Get recent attendance for quick overview
        $recentAttendance = Attendance::with(['user', 'schedule.client'])
            ->latest('check_in')
            ->take(10)
            ->get();

        // Get department-wise attendance summary for current month
        $departmentStats = User::select('department', DB::raw('COUNT(DISTINCT attendances.id) as attendance_count'))
            ->leftJoin('attendances', function($join) use ($currentMonth) {
                $join->on('users.id', '=', 'attendances.user_id')
                     ->whereYear('attendances.attendance_date', $currentMonth->year)
                     ->whereMonth('attendances.attendance_date', $currentMonth->month);
            })
            ->where('users.status', 'active')
            ->whereNotNull('users.department')
            ->groupBy('users.department')
            ->get();

        return view('admin.reports.index', compact('stats', 'recentAttendance', 'departmentStats'));
    }

    /**
     * Generate attendance report with advanced filters
     *
     * Filters include: date range, user, department, status, shift
     * Results are paginated and can be exported
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function attendance(Request $request)
    {
        // Validate filter inputs
        $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'user_id' => 'nullable|exists:users,id',
            'department' => 'nullable|string',
            'status' => 'nullable|in:present,late,absent,on_leave',
            'shift_id' => 'nullable|exists:shifts,id',
        ]);

        // Build query with relationships
        $query = Attendance::with(['user', 'schedule.client', 'schedule.shift', 'qrCode']);

        // Apply date range filter (default: current month)
        if ($request->filled('from_date')) {
            $query->whereDate('attendance_date', '>=', $request->from_date);
        } else {
            $query->whereDate('attendance_date', '>=', now()->startOfMonth()->toDateString());
        }

        if ($request->filled('to_date')) {
            $query->whereDate('attendance_date', '<=', $request->to_date);
        } else {
            $query->whereDate('attendance_date', '<=', now()->endOfMonth()->toDateString());
        }

        // Filter by specific user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by department
        if ($request->filled('department')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('department', $request->department);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by shift
        if ($request->filled('shift_id')) {
            $query->where('shift_id', $request->shift_id);
        }

        // Order by latest first
        $attendances = $query->latest('attendance_date')
            ->latest('check_in')
            ->paginate(20)
            ->withQueryString();

        // Calculate summary statistics for filtered results
        $summary = [
            'total_records' => $query->count(),
            'total_present' => (clone $query)->where('status', 'present')->count(),
            'total_late' => (clone $query)->where('status', 'late')->count(),
            'total_absent' => (clone $query)->where('status', 'absent')->count(),
            'total_hours' => (clone $query)->sum('work_duration'),
            'average_hours' => (clone $query)->avg('work_duration'),
        ];

        // Format hours from minutes
        $summary['total_hours_formatted'] = round($summary['total_hours'] / 60, 2);
        $summary['average_hours_formatted'] = round($summary['average_hours'] / 60, 2);

        // Get unique departments for filter dropdown
        $departments = User::distinct('department')
            ->whereNotNull('department')
            ->pluck('department')
            ->sort();

        // Get all active users for filter dropdown
        $users = User::active()
            ->orderBy('firstname')
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->full_name . ' (' . $user->employee_no . ')',
                ];
            });

        return view('admin.reports.attendance', compact(
            'attendances',
            'summary',
            'departments',
            'users'
        ));
    }

    /**
     * Generate schedules report with advanced filters
     *
     * Filters include: date range, user, client, status, shift
     * Shows schedule completion status and attendance correlation
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function schedules(Request $request)
    {
        // Validate filter inputs
        $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'user_id' => 'nullable|exists:users,id',
            'client_id' => 'nullable|exists:clients,id',
            'status' => 'nullable|in:scheduled,completed,cancelled,rescheduled',
            'shift_id' => 'nullable|exists:shifts,id',
        ]);

        // Build query with relationships
        $query = Schedule::with(['user', 'client', 'shift', 'attendance', 'creator']);

        // Apply date range filter (default: current month)
        if ($request->filled('from_date')) {
            $query->whereDate('scheduled_date', '>=', $request->from_date);
        } else {
            $query->whereDate('scheduled_date', '>=', now()->startOfMonth()->toDateString());
        }

        if ($request->filled('to_date')) {
            $query->whereDate('scheduled_date', '<=', $request->to_date);
        } else {
            $query->whereDate('scheduled_date', '<=', now()->endOfMonth()->toDateString());
        }

        // Filter by specific user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by client
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by shift
        if ($request->filled('shift_id')) {
            $query->where('shift_id', $request->shift_id);
        }

        // Order by scheduled date
        $schedules = $query->orderBy('scheduled_date')
            ->orderBy('start_time')
            ->paginate(20)
            ->withQueryString();

        // Calculate summary statistics for filtered results
        $summary = [
            'total_schedules' => $query->count(),
            'scheduled' => (clone $query)->where('status', 'scheduled')->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
            'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
            'with_attendance' => (clone $query)->whereHas('attendance')->count(),
            'without_attendance' => (clone $query)->whereDoesntHave('attendance')->count(),
        ];

        // Get all active users for filter dropdown
        $users = User::active()
            ->orderBy('firstname')
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->full_name . ' (' . $user->employee_no . ')',
                ];
            });

        // Get all active clients for filter dropdown
        $clients = \App\Models\Client::active()
            ->orderBy('name')
            ->get();

        return view('admin.reports.schedules', compact(
            'schedules',
            'summary',
            'users',
            'clients'
        ));
    }

    /**
     * Export reports to Excel or PDF
     *
     * Supports exporting attendance and schedule reports
     * in Excel (.xlsx) or PDF format with applied filters
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'type' => 'required|in:attendance,schedules',
                'format' => 'required|in:excel,pdf',
                'from_date' => 'nullable|date',
                'to_date' => 'nullable|date|after_or_equal:from_date',
                'user_id' => 'nullable|exists:users,id',
                'department' => 'nullable|string',
                'client_id' => 'nullable|exists:clients,id',
                'status' => 'nullable|string',
            ]);

            $type = $validated['type'];
            $format = $validated['format'];

            // Build data query based on type
            if ($type === 'attendance') {
                $data = $this->buildAttendanceExportData($request);
            } else {
                $data = $this->buildSchedulesExportData($request);
            }

            // Generate filename
            $filename = $type . '_report_' . now()->format('Y-m-d_His');

            // Export based on format
            if ($format === 'excel') {
                return $this->exportToExcel($data, $type, $filename);
            } else {
                return $this->exportToPdf($data, $type, $filename);
            }

        } catch (\Exception $e) {
            Log::error('Report Export Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'An error occurred while exporting the report. Please try again.');
        }
    }

    /**
     * Show comprehensive statistics dashboard
     *
     * Displays detailed analytics including:
     * - Attendance trends over time
     * - Department-wise performance
     * - User-wise statistics
     * - Peak attendance hours
     * - Monthly comparisons
     *
     * @return \Illuminate\View\View
     */
    public function statistics()
    {
        $currentMonth = now();
        $previousMonth = now()->subMonth();

        // Overall statistics
        $overallStats = [
            // Current month
            'current_month_attendance' => Attendance::whereYear('attendance_date', $currentMonth->year)
                ->whereMonth('attendance_date', $currentMonth->month)
                ->count(),
            'current_month_present' => Attendance::whereYear('attendance_date', $currentMonth->year)
                ->whereMonth('attendance_date', $currentMonth->month)
                ->where('status', 'present')
                ->count(),
            'current_month_late' => Attendance::whereYear('attendance_date', $currentMonth->year)
                ->whereMonth('attendance_date', $currentMonth->month)
                ->where('status', 'late')
                ->count(),
            'current_month_hours' => Attendance::whereYear('attendance_date', $currentMonth->year)
                ->whereMonth('attendance_date', $currentMonth->month)
                ->sum('work_duration'),

            // Previous month
            'previous_month_attendance' => Attendance::whereYear('attendance_date', $previousMonth->year)
                ->whereMonth('attendance_date', $previousMonth->month)
                ->count(),
            'previous_month_present' => Attendance::whereYear('attendance_date', $previousMonth->year)
                ->whereMonth('attendance_date', $previousMonth->month)
                ->where('status', 'present')
                ->count(),
            'previous_month_late' => Attendance::whereYear('attendance_date', $previousMonth->year)
                ->whereMonth('attendance_date', $previousMonth->month)
                ->where('status', 'late')
                ->count(),
            'previous_month_hours' => Attendance::whereYear('attendance_date', $previousMonth->year)
                ->whereMonth('attendance_date', $previousMonth->month)
                ->sum('work_duration'),
        ];

        // Format hours
        $overallStats['current_month_hours_formatted'] = round($overallStats['current_month_hours'] / 60, 2);
        $overallStats['previous_month_hours_formatted'] = round($overallStats['previous_month_hours'] / 60, 2);

        // Calculate percentage changes
        $overallStats['attendance_change'] = $this->calculatePercentageChange(
            $overallStats['previous_month_attendance'],
            $overallStats['current_month_attendance']
        );
        $overallStats['present_change'] = $this->calculatePercentageChange(
            $overallStats['previous_month_present'],
            $overallStats['current_month_present']
        );

        // Department-wise statistics
        $departmentStats = User::select('department')
            ->selectRaw('COUNT(DISTINCT attendances.user_id) as user_count')
            ->selectRaw('COUNT(attendances.id) as attendance_count')
            ->selectRaw('SUM(CASE WHEN attendances.status = "present" THEN 1 ELSE 0 END) as present_count')
            ->selectRaw('SUM(CASE WHEN attendances.status = "late" THEN 1 ELSE 0 END) as late_count')
            ->selectRaw('SUM(CASE WHEN attendances.status = "absent" THEN 1 ELSE 0 END) as absent_count')
            ->selectRaw('SUM(attendances.work_duration) as total_minutes')
            ->leftJoin('attendances', function($join) use ($currentMonth) {
                $join->on('users.id', '=', 'attendances.user_id')
                     ->whereYear('attendances.attendance_date', $currentMonth->year)
                     ->whereMonth('attendances.attendance_date', $currentMonth->month);
            })
            ->where('users.status', 'active')
            ->whereNotNull('users.department')
            ->groupBy('users.department')
            ->get()
            ->map(function($dept) {
                $dept->total_hours = round(($dept->total_minutes ?? 0) / 60, 2);
                $dept->attendance_rate = $dept->user_count > 0
                    ? round(($dept->present_count / max($dept->attendance_count, 1)) * 100, 1)
                    : 0;
                return $dept;
            });

        // Top performers (users with highest attendance rate this month)
        $topPerformers = User::select('users.*')
            ->selectRaw('COUNT(attendances.id) as attendance_count')
            ->selectRaw('SUM(CASE WHEN attendances.status = "present" THEN 1 ELSE 0 END) as present_count')
            ->selectRaw('SUM(attendances.work_duration) as total_minutes')
            ->leftJoin('attendances', function($join) use ($currentMonth) {
                $join->on('users.id', '=', 'attendances.user_id')
                     ->whereYear('attendances.attendance_date', $currentMonth->year)
                     ->whereMonth('attendances.attendance_date', $currentMonth->month);
            })
            ->where('users.status', 'active')
            ->groupBy('users.id')
            ->having('attendance_count', '>', 0)
            ->orderByDesc('present_count')
            ->take(10)
            ->get()
            ->map(function($user) {
                $user->total_hours = round(($user->total_minutes ?? 0) / 60, 2);
                $user->attendance_rate = $user->attendance_count > 0
                    ? round(($user->present_count / $user->attendance_count) * 100, 1)
                    : 0;
                return $user;
            });

        // Daily attendance trend for current month
        $dailyTrend = Attendance::select(
                DB::raw('DATE(attendance_date) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present'),
                DB::raw('SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late'),
                DB::raw('SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent')
            )
            ->whereYear('attendance_date', $currentMonth->year)
            ->whereMonth('attendance_date', $currentMonth->month)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Peak attendance hours analysis
        $peakHours = Attendance::select(
                DB::raw('HOUR(check_in) as hour'),
                DB::raw('COUNT(*) as count')
            )
            ->whereNotNull('check_in')
            ->whereYear('attendance_date', $currentMonth->year)
            ->whereMonth('attendance_date', $currentMonth->month)
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        return view('admin.reports.statistics', compact(
            'overallStats',
            'departmentStats',
            'topPerformers',
            'dailyTrend',
            'peakHours'
        ));
    }

    /**
     * Build attendance data for export
     *
     * Helper method to prepare attendance data based on filters
     *
     * @param Request $request
     * @return \Illuminate\Support\Collection
     */
    private function buildAttendanceExportData(Request $request)
    {
        $query = Attendance::with(['user', 'schedule.client', 'schedule.shift']);

        // Apply filters
        if ($request->filled('from_date')) {
            $query->whereDate('attendance_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('attendance_date', '<=', $request->to_date);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('department')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('department', $request->department);
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return $query->latest('attendance_date')->get();
    }

    /**
     * Build schedules data for export
     *
     * Helper method to prepare schedules data based on filters
     *
     * @param Request $request
     * @return \Illuminate\Support\Collection
     */
    private function buildSchedulesExportData(Request $request)
    {
        $query = Schedule::with(['user', 'client', 'shift', 'attendance']);

        // Apply filters
        if ($request->filled('from_date')) {
            $query->whereDate('scheduled_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('scheduled_date', '<=', $request->to_date);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return $query->orderBy('scheduled_date')->get();
    }

    /**
     * Export data to Excel format
     *
     * Uses Maatwebsite\Excel to generate Excel file
     *
     * @param \Illuminate\Support\Collection $data
     * @param string $type
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    private function exportToExcel($data, $type, $filename)
    {
        if ($type === 'attendance') {
            return Excel::download(
                new \App\Exports\AttendanceExport($data),
                $filename . '.xlsx'
            );
        } else {
            return Excel::download(
                new \App\Exports\SchedulesExport($data),
                $filename . '.xlsx'
            );
        }
    }

    /**
     * Export data to PDF format
     *
     * Uses DomPDF to generate PDF file with formatted layout
     *
     * @param \Illuminate\Support\Collection $data
     * @param string $type
     * @param string $filename
     * @return \Illuminate\Http\Response
     */
    private function exportToPdf($data, $type, $filename)
    {
        $pdf = Pdf::loadView('admin.reports.pdf.' . $type, [
            'data' => $data,
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ]);

        return $pdf->download($filename . '.pdf');
    }

    /**
     * Calculate percentage change between two values
     *
     * Helper method for statistics calculations
     *
     * @param float $oldValue
     * @param float $newValue
     * @return float
     */
    private function calculatePercentageChange($oldValue, $newValue)
    {
        if ($oldValue == 0) {
            return $newValue > 0 ? 100 : 0;
        }

        return round((($newValue - $oldValue) / $oldValue) * 100, 1);
    }
}
