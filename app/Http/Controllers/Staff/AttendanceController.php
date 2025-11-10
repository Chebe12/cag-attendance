<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\Client;
use App\Models\QrCode;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * AttendanceController handles staff attendance management
 *
 * This controller manages:
 * - Viewing attendance history
 * - QR code scanning for check-in/check-out
 * - Manual check-in and check-out
 * - Viewing personal schedules
 * - Viewing assigned clients (for instructors)
 * - IP and location tracking for all attendance actions
 * - Logging all attendance activities
 */
class AttendanceController extends Controller
{
    /**
     * Display attendance history for logged-in user
     *
     * Shows paginated attendance records with filters for date range and status
     * Includes search functionality and attendance statistics
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Build query for user's attendance records
        $query = Attendance::where('user_id', $user->id)
            ->with(['schedule.client', 'schedule.shift', 'qrCode']);

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('attendance_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('attendance_date', '<=', $request->to_date);
        }

        // Filter by status (present, late, absent, etc.)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by month (for monthly view)
        if ($request->filled('month')) {
            $date = Carbon::parse($request->month);
            $query->whereYear('attendance_date', $date->year)
                  ->whereMonth('attendance_date', $date->month);
        }

        // Order by latest first
        $attendances = $query->latest('attendance_date')
            ->latest('check_in')
            ->paginate(15)
            ->withQueryString();

        // Calculate attendance statistics for current month
        $stats = [
            'total_days' => Attendance::where('user_id', $user->id)
                ->whereMonth('attendance_date', now()->month)
                ->whereYear('attendance_date', now()->year)
                ->count(),
            'present' => Attendance::where('user_id', $user->id)
                ->whereMonth('attendance_date', now()->month)
                ->whereYear('attendance_date', now()->year)
                ->where('status', 'present')
                ->count(),
            'late' => Attendance::where('user_id', $user->id)
                ->whereMonth('attendance_date', now()->month)
                ->whereYear('attendance_date', now()->year)
                ->where('status', 'late')
                ->count(),
            'absent' => Attendance::where('user_id', $user->id)
                ->whereMonth('attendance_date', now()->month)
                ->whereYear('attendance_date', now()->year)
                ->where('status', 'absent')
                ->count(),
            'total_hours' => Attendance::where('user_id', $user->id)
                ->whereMonth('attendance_date', now()->month)
                ->whereYear('attendance_date', now()->year)
                ->sum('work_duration'),
        ];

        // Convert total hours from minutes to hours
        $stats['total_hours_formatted'] = round($stats['total_hours'] / 60, 2);

        return view('staff.attendance.index', compact('attendances', 'stats'));
    }

    /**
     * Show QR scanning page for attendance marking
     *
     * Displays the QR code scanner interface with today's schedule
     * and current attendance status
     *
     * @return \Illuminate\View\View
     */
    public function mark()
    {
        $user = Auth::user();

        // Get today's schedule for the user
        $todaySchedule = Schedule::where('user_id', $user->id)
            ->today()
            ->with(['client', 'shift'])
            ->first();

        // Check if user has already checked in today
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->today()
            ->first();

        // Determine current status (not checked in, checked in, checked out)
        $status = 'not_checked_in';
        if ($todayAttendance) {
            if ($todayAttendance->check_out) {
                $status = 'checked_out';
            } else {
                $status = 'checked_in';
            }
        }

        return view('staff.attendance.mark', compact('todaySchedule', 'todayAttendance', 'status'));
    }

    /**
     * Process QR code scan for check-in/check-out
     *
     * Validates QR code, creates or updates attendance record,
     * tracks IP and location, logs the action
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function scan(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'qr_code' => 'required|string',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'photo' => 'nullable|string', // Base64 encoded photo
            ]);

            $user = Auth::user();
            $qrCodeString = $validated['qr_code'];

            // Find and validate QR code
            $qrCode = QrCode::where('code', $qrCodeString)->first();

            if (!$qrCode) {
                throw ValidationException::withMessages([
                    'qr_code' => ['Invalid QR code. Please scan a valid attendance QR code.']
                ]);
            }

            // Check if QR code is active and valid
            if (!$qrCode->isValid()) {
                throw ValidationException::withMessages([
                    'qr_code' => ['This QR code is expired or inactive.']
                ]);
            }

            // Get IP address and location
            $ipAddress = $request->ip();
            $location = null;
            if (isset($validated['latitude']) && isset($validated['longitude'])) {
                $location = json_encode([
                    'latitude' => $validated['latitude'],
                    'longitude' => $validated['longitude'],
                    'timestamp' => now()->toDateTimeString(),
                ]);
            }

            // Check if user has already checked in today
            $todayAttendance = Attendance::where('user_id', $user->id)
                ->today()
                ->first();

            DB::beginTransaction();

            try {
                if (!$todayAttendance) {
                    // First scan of the day - Check In
                    $todayAttendance = $this->processCheckIn($user, $qrCode, $ipAddress, $location, $validated['photo'] ?? null);
                    $action = 'check_in';
                    $message = 'Successfully checked in!';
                } elseif (!$todayAttendance->check_out) {
                    // Already checked in - Check Out
                    $this->processCheckOut($todayAttendance, $ipAddress, $location, $validated['photo'] ?? null);
                    $action = 'check_out';
                    $message = 'Successfully checked out!';
                } else {
                    // Already checked out today
                    throw ValidationException::withMessages([
                        'qr_code' => ['You have already completed your attendance for today.']
                    ]);
                }

                // Increment QR code scan count
                $qrCode->incrementScanCount();

                // Log the attendance action
                $this->logAttendanceAction(
                    $todayAttendance,
                    $user,
                    $action,
                    $ipAddress,
                    $request->userAgent()
                );

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'attendance' => $todayAttendance->load(['schedule.client', 'schedule.shift']),
                    'action' => $action,
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('QR Scan Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your attendance. Please try again.',
            ], 500);
        }
    }

    /**
     * Manual check-in without QR code
     *
     * Allows staff to check in manually with IP and location tracking
     * Useful when QR scanner is not available
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function checkIn(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'notes' => 'nullable|string|max:500',
            ]);

            $user = Auth::user();

            // Check if already checked in today
            $todayAttendance = Attendance::where('user_id', $user->id)
                ->today()
                ->first();

            if ($todayAttendance) {
                return back()->with('error', 'You have already checked in today.');
            }

            // Get IP address and location
            $ipAddress = $request->ip();
            $location = null;
            if (isset($validated['latitude']) && isset($validated['longitude'])) {
                $location = json_encode([
                    'latitude' => $validated['latitude'],
                    'longitude' => $validated['longitude'],
                    'timestamp' => now()->toDateTimeString(),
                ]);
            }

            DB::beginTransaction();

            try {
                // Create attendance record
                $attendance = $this->processCheckIn($user, null, $ipAddress, $location, null, $validated['notes'] ?? null);

                // Log the attendance action
                $this->logAttendanceAction(
                    $attendance,
                    $user,
                    'manual_check_in',
                    $ipAddress,
                    $request->userAgent()
                );

                DB::commit();

                return redirect()
                    ->route('staff.attendance.index')
                    ->with('success', 'Successfully checked in!');

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Manual Check-in Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'An error occurred during check-in. Please try again.');
        }
    }

    /**
     * Manual check-out
     *
     * Allows staff to check out manually, calculates work duration,
     * records IP and location
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function checkOut(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'notes' => 'nullable|string|max:500',
            ]);

            $user = Auth::user();

            // Find today's attendance record
            $todayAttendance = Attendance::where('user_id', $user->id)
                ->today()
                ->first();

            if (!$todayAttendance) {
                return back()->with('error', 'No check-in record found for today.');
            }

            if ($todayAttendance->check_out) {
                return back()->with('error', 'You have already checked out today.');
            }

            // Get IP address and location
            $ipAddress = $request->ip();
            $location = null;
            if (isset($validated['latitude']) && isset($validated['longitude'])) {
                $location = json_encode([
                    'latitude' => $validated['latitude'],
                    'longitude' => $validated['longitude'],
                    'timestamp' => now()->toDateTimeString(),
                ]);
            }

            DB::beginTransaction();

            try {
                // Update attendance record with check-out
                $this->processCheckOut($todayAttendance, $ipAddress, $location, null, $validated['notes'] ?? null);

                // Log the attendance action
                $this->logAttendanceAction(
                    $todayAttendance,
                    $user,
                    'manual_check_out',
                    $ipAddress,
                    $request->userAgent()
                );

                DB::commit();

                return redirect()
                    ->route('staff.attendance.index')
                    ->with('success', 'Successfully checked out! Total work duration: ' .
                        round($todayAttendance->work_duration / 60, 2) . ' hours');

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Manual Check-out Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'An error occurred during check-out. Please try again.');
        }
    }

    /**
     * Show logged-in user's schedules
     *
     * Displays upcoming and past schedules with filtering options
     * Includes schedule status and client information
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function mySchedule(Request $request)
    {
        $user = Auth::user();

        // Build query
        $query = Schedule::where('user_id', $user->id)
            ->with(['client', 'shift', 'creator']);

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('scheduled_date', '>=', $request->from_date);
        } else {
            // Default: show from today onwards
            $query->whereDate('scheduled_date', '>=', now()->toDateString());
        }

        if ($request->filled('to_date')) {
            $query->whereDate('scheduled_date', '<=', $request->to_date);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by client
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Order by scheduled date
        $schedules = $query->orderBy('scheduled_date')
            ->orderBy('start_time')
            ->paginate(15)
            ->withQueryString();

        // Get user's clients for filter dropdown
        $userClients = Client::whereHas('schedules', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->active()->get();

        // Get upcoming schedules count
        $upcomingCount = Schedule::where('user_id', $user->id)
            ->upcoming()
            ->count();

        // Get today's schedule
        $todaySchedule = Schedule::where('user_id', $user->id)
            ->today()
            ->with(['client', 'shift'])
            ->first();

        return view('staff.attendance.schedule', compact('schedules', 'userClients', 'upcomingCount', 'todaySchedule'));
    }

    /**
     * Show assigned clients for instructors
     *
     * Displays list of clients assigned to the logged-in instructor
     * with schedule information and contact details
     *
     * @return \Illuminate\View\View
     */
    public function myClients()
    {
        $user = Auth::user();

        // Check if user is an instructor
        if (!$user->isInstructor()) {
            return redirect()
                ->route('staff.dashboard')
                ->with('error', 'This feature is only available for instructors.');
        }

        // Get clients assigned to this instructor through schedules
        $clients = Client::whereHas('schedules', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->active()
        ->withCount([
            'schedules' => function($query) use ($user) {
                $query->where('user_id', $user->id);
            }
        ])
        ->with([
            'schedules' => function($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->upcoming()
                    ->with('shift')
                    ->take(5);
            }
        ])
        ->get();

        // Get statistics
        $stats = [
            'total_clients' => $clients->count(),
            'upcoming_schedules' => Schedule::where('user_id', $user->id)
                ->upcoming()
                ->count(),
            'this_week_schedules' => Schedule::where('user_id', $user->id)
                ->whereBetween('scheduled_date', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])
                ->count(),
        ];

        return view('staff.attendance.clients', compact('clients', 'stats'));
    }

    /**
     * Export attendance records to PDF or Excel
     *
     * Allows staff to export their attendance history in various formats
     * Respects the same filters as the index view
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $format = $request->input('format', 'pdf');

        // Build query for user's attendance records with same filters as index
        $query = Attendance::where('user_id', $user->id)
            ->with(['schedule.client', 'schedule.shift', 'qrCode']);

        // Apply same filters as index method
        if ($request->filled('from_date')) {
            $query->whereDate('attendance_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('attendance_date', '<=', $request->to_date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('month')) {
            $date = Carbon::parse($request->month);
            $query->whereYear('attendance_date', $date->year)
                  ->whereMonth('attendance_date', $date->month);
        }

        $attendances = $query->latest('attendance_date')
            ->latest('check_in')
            ->get();

        // For now, return a simple response
        // TODO: Implement proper PDF/Excel export using libraries like dompdf or maatwebsite/excel
        if ($format === 'pdf') {
            return response()->json([
                'message' => 'PDF export functionality will be implemented soon.',
                'count' => $attendances->count(),
            ]);
        } elseif ($format === 'excel') {
            return response()->json([
                'message' => 'Excel export functionality will be implemented soon.',
                'count' => $attendances->count(),
            ]);
        }

        return back()->with('error', 'Invalid export format.');
    }

    /**
     * Process check-in and create attendance record
     *
     * Helper method to handle check-in logic, determine status (present/late),
     * and create attendance record with all tracking information
     *
     * @param \App\Models\User $user
     * @param \App\Models\QrCode|null $qrCode
     * @param string $ipAddress
     * @param string|null $location
     * @param string|null $photo
     * @param string|null $notes
     * @return \App\Models\Attendance
     */
    private function processCheckIn($user, $qrCode, $ipAddress, $location, $photo = null, $notes = null)
    {
        $now = now();
        $today = $now->toDateString();

        // Get today's schedule for the user
        $schedule = Schedule::where('user_id', $user->id)
            ->whereDate('scheduled_date', $today)
            ->first();

        // Determine status (present or late)
        $status = 'present';
        if ($schedule) {
            $scheduledStartTime = Carbon::parse($today . ' ' . $schedule->start_time);
            // Consider late if check-in is more than 15 minutes after scheduled start time
            if ($now->diffInMinutes($scheduledStartTime, false) < -15) {
                $status = 'late';
            }
        }

        // Handle photo upload if provided (base64)
        $photoPath = null;
        if ($photo) {
            try {
                $photoPath = $this->saveBase64Photo($photo, 'check_in');
            } catch (\Exception $e) {
                Log::warning('Failed to save check-in photo: ' . $e->getMessage());
            }
        }

        // Create attendance record
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'qr_code_id' => $qrCode?->id,
            'schedule_id' => $schedule?->id,
            'shift_id' => $schedule?->shift_id,
            'attendance_date' => $today,
            'check_in' => $now,
            'check_in_location' => $location,
            'check_in_ip' => $ipAddress,
            'status' => $status,
            'check_in_photo' => $photoPath,
            'notes' => $notes,
        ]);

        return $attendance;
    }

    /**
     * Process check-out and update attendance record
     *
     * Helper method to handle check-out logic, calculate work duration,
     * and update attendance record
     *
     * @param \App\Models\Attendance $attendance
     * @param string $ipAddress
     * @param string|null $location
     * @param string|null $photo
     * @param string|null $notes
     * @return void
     */
    private function processCheckOut($attendance, $ipAddress, $location, $photo = null, $notes = null)
    {
        $now = now();

        // Handle photo upload if provided (base64)
        $photoPath = null;
        if ($photo) {
            try {
                $photoPath = $this->saveBase64Photo($photo, 'check_out');
            } catch (\Exception $e) {
                Log::warning('Failed to save check-out photo: ' . $e->getMessage());
            }
        }

        // Update attendance record with check-out information
        $attendance->update([
            'check_out' => $now,
            'check_out_location' => $location,
            'check_out_ip' => $ipAddress,
            'check_out_photo' => $photoPath,
            'notes' => $attendance->notes ? $attendance->notes . "\n" . $notes : $notes,
        ]);

        // Calculate work duration
        $attendance->calculateDuration();
    }

    /**
     * Log attendance action to AttendanceLog
     *
     * Creates a log entry for audit trail with IP address,
     * user agent, and action details
     *
     * @param \App\Models\Attendance $attendance
     * @param \App\Models\User $user
     * @param string $action
     * @param string $ipAddress
     * @param string $userAgent
     * @return void
     */
    private function logAttendanceAction($attendance, $user, $action, $ipAddress, $userAgent)
    {
        AttendanceLog::create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'action' => $action,
            'new_data' => [
                'check_in' => $attendance->check_in?->toDateTimeString(),
                'check_out' => $attendance->check_out?->toDateTimeString(),
                'status' => $attendance->status,
                'work_duration' => $attendance->work_duration,
            ],
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }

    /**
     * Save base64 encoded photo to storage
     *
     * Decodes base64 photo data and saves to public storage
     *
     * @param string $base64Photo
     * @param string $type (check_in or check_out)
     * @return string Path to saved photo
     */
    private function saveBase64Photo($base64Photo, $type = 'check_in')
    {
        // Remove data:image/png;base64, prefix if present
        $image = str_replace('data:image/png;base64,', '', $base64Photo);
        $image = str_replace('data:image/jpeg;base64,', '', $image);
        $image = str_replace('data:image/jpg;base64,', '', $image);
        $image = str_replace(' ', '+', $image);

        // Decode base64
        $imageData = base64_decode($image);

        // Generate unique filename
        $filename = 'attendance/' . $type . '/' . uniqid() . '_' . time() . '.jpg';

        // Save to storage
        \Storage::disk('public')->put($filename, $imageData);

        return $filename;
    }
}
