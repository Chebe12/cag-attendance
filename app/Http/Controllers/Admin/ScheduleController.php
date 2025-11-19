<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Client;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * Display a listing of schedules with relationships.
     * Loads user, client, and shift relationships.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Build query with relationships
        $query = Schedule::with(['user', 'client', 'shift', 'creator']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($query) use ($search) {
                    $query->where('firstname', 'like', '%' . $search . '%')
                          ->orWhere('lastname', 'like', '%' . $search . '%')
                          ->orWhere('employee_no', 'like', '%' . $search . '%');
                })->orWhereHas('client', function($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        // Filter by user
        if ($request->filled('user')) {
            $query->where('user_id', $request->user);
        }

        // Filter by client
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Filter by session
        if ($request->filled('session')) {
            $query->where('session_time', $request->session);
        }

        // Filter by shift
        if ($request->filled('shift_id')) {
            $query->where('shift_id', $request->shift_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by single date (from the view filter)
        if ($request->filled('date')) {
            $query->whereDate('scheduled_date', $request->date);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('scheduled_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('scheduled_date', '<=', $request->date_to);
        }

        // Filter by day of week
        if ($request->filled('day_of_week')) {
            $query->where('day_of_week', $request->day_of_week);
        }

        // Default to upcoming schedules if no date filter (removed to show all schedules)
        // Comment this out to show all schedules by default
        // if (!$request->filled('date_from') && !$request->filled('date_to') && !$request->filled('date') && !$request->filled('show_all')) {
        //     $query->where('scheduled_date', '>=', now()->toDateString());
        // }

        // Order by scheduled date and start time
        $schedules = $query->orderBy('scheduled_date', 'desc')
                          ->orderBy('start_time')
                          ->paginate(20)
                          ->withQueryString();

        // Get data for filters
        $users = User::active()->orderBy('firstname')->get();
        $clients = Client::active()->orderBy('name')->get();
        $shifts = Shift::active()->orderBy('name')->get();

        return view('admin.schedules.index', compact('schedules', 'users', 'clients', 'shifts'));
    }

    /**
     * Show the form for creating a new schedule.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Get active users and clients for dropdowns
        $users = User::active()->orderBy('firstname')->get();
        $clients = Client::active()->orderBy('name')->get();

        return view('admin.schedules.create', compact('users', 'clients'));
    }

    /**
     * Store a newly created schedule in storage.
     * Validates dates, times, foreign keys, and sets created_by to authenticated admin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'client_id' => 'required|exists:clients,id',
            'session_time' => 'required|in:morning,mid-morning,afternoon',
            'shift_id' => 'nullable|exists:shifts,id',
            'is_recurring' => 'boolean',
            'scheduled_date' => 'nullable|date|after_or_equal:today|required_if:is_recurring,false',
            'day_of_week' => 'nullable|string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday|required_if:is_recurring,true',
            'status' => 'required|string|in:draft,scheduled,pending,canceled',
            'notes' => 'nullable|string|max:1000',
        ], [
            'user_id.required' => 'Please select a user.',
            'user_id.exists' => 'The selected user does not exist.',
            'client_id.required' => 'Please select a client.',
            'client_id.exists' => 'The selected client does not exist.',
            'session_time.required' => 'Please select a session.',
            'session_time.in' => 'Invalid session selected.',
            'shift_id.exists' => 'The selected shift does not exist.',
            'scheduled_date.required_if' => 'Scheduled date is required for non-recurring schedules.',
            'scheduled_date.after_or_equal' => 'Scheduled date must be today or in the future.',
            'day_of_week.required_if' => 'Day of week is required for recurring schedules.',
            'day_of_week.in' => 'Invalid day of week selected.',
            'status.in' => 'Invalid status selected.',
        ]);

        // Calculate day of week from scheduled_date if not recurring
        if (!$request->is_recurring && !empty($validated['scheduled_date'])) {
            $validated['day_of_week'] = strtolower(Carbon::parse($validated['scheduled_date'])->format('l'));
        }

        // Set created_by to authenticated admin
        $validated['created_by'] = auth()->id();
        $validated['is_recurring'] = $request->input('is_recurring', 0) == 1;

        // Get session times based on session_time
        $sessionTimes = Schedule::getSessionTimes($validated['session_time']);
        $validated['start_time'] = $sessionTimes['start'];
        $validated['end_time'] = $sessionTimes['end'];

        // Check for scheduling conflicts (only for non-recurring schedules)
        if (!$validated['is_recurring'] && !empty($validated['scheduled_date'])) {
            $conflict = $this->checkSchedulingConflict(
                $validated['user_id'],
                $validated['scheduled_date'],
                $validated['start_time'],
                $validated['end_time']
            );

            if ($conflict) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('warning', 'Warning: This user already has a schedule at this time: ' . $conflict->client->name);
            }
        }

        // Create the schedule
        $schedule = Schedule::create($validated);

        return redirect()
            ->route('admin.schedules.show', $schedule)
            ->with('success', 'Schedule created successfully.');
    }

    /**
     * Display the specified schedule.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\View\View
     */
    public function show(Schedule $schedule)
    {
        // Load all relationships
        $schedule->load(['user', 'client', 'shift', 'creator', 'attendance']);

        return view('admin.schedules.show', compact('schedule'));
    }

    /**
     * Show the form for editing the specified schedule.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\View\View
     */
    public function edit(Schedule $schedule)
    {
        // Get active users and clients for dropdowns
        $users = User::active()->orderBy('firstname')->get();
        $clients = Client::active()->orderBy('name')->get();

        return view('admin.schedules.edit', compact('schedule', 'users', 'clients'));
    }

    /**
     * Update the specified schedule in storage.
     * Validates dates, times, and foreign keys.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Schedule $schedule)
    {
        // Validate the request
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'client_id' => 'required|exists:clients,id',
            'session_time' => 'required|in:morning,mid-morning,afternoon',
            'shift_id' => 'nullable|exists:shifts,id',
            'is_recurring' => 'boolean',
            'scheduled_date' => 'nullable|date|required_if:is_recurring,false',
            'day_of_week' => 'nullable|string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday|required_if:is_recurring,true',
            'status' => 'required|string|in:draft,scheduled,pending,canceled',
            'notes' => 'nullable|string|max:1000',
        ], [
            'user_id.required' => 'Please select a user.',
            'user_id.exists' => 'The selected user does not exist.',
            'client_id.required' => 'Please select a client.',
            'client_id.exists' => 'The selected client does not exist.',
            'session_time.required' => 'Please select a session.',
            'session_time.in' => 'Invalid session selected.',
            'shift_id.exists' => 'The selected shift does not exist.',
            'scheduled_date.required_if' => 'Scheduled date is required for non-recurring schedules.',
            'day_of_week.required_if' => 'Day of week is required for recurring schedules.',
            'day_of_week.in' => 'Invalid day of week selected.',
            'status.in' => 'Invalid status selected.',
        ]);

        // Calculate day of week from scheduled_date if not recurring
        if (!$request->is_recurring && !empty($validated['scheduled_date'])) {
            $validated['day_of_week'] = strtolower(Carbon::parse($validated['scheduled_date'])->format('l'));
        }

        $validated['is_recurring'] = $request->input('is_recurring', 0) == 1;

        // Get session times based on session_time
        $sessionTimes = Schedule::getSessionTimes($validated['session_time']);
        $validated['start_time'] = $sessionTimes['start'];
        $validated['end_time'] = $sessionTimes['end'];

        // Check for scheduling conflicts (only for non-recurring schedules, excluding current schedule)
        if (!$validated['is_recurring'] && !empty($validated['scheduled_date'])) {
            $conflict = $this->checkSchedulingConflict(
                $validated['user_id'],
                $validated['scheduled_date'],
                $validated['start_time'],
                $validated['end_time'],
                $schedule->id
            );

            if ($conflict) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('warning', 'Warning: This user already has a schedule at this time: ' . $conflict->client->name);
            }
        }

        // Update the schedule
        $schedule->update($validated);

        return redirect()
            ->route('admin.schedules.show', $schedule)
            ->with('success', 'Schedule updated successfully.');
    }

    /**
     * Remove the specified schedule from storage (soft delete).
     *
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Schedule $schedule)
    {
        // Check if schedule has attendance
        if ($schedule->attendance) {
            return redirect()
                ->route('admin.schedules.index')
                ->with('error', 'Cannot delete schedule with attendance record. Consider canceling instead.');
        }

        // Soft delete the schedule
        $schedule->delete();

        return redirect()
            ->route('admin.schedules.index')
            ->with('success', 'Schedule deleted successfully.');
    }

    /**
     * Display calendar view with schedule data.
     * Provides data formatted for calendar display.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function calendar(Request $request)
    {
        // Get month and year from request or use current
        $month = $request->filled('month') ? $request->month : now()->month;
        $year = $request->filled('year') ? $request->year : now()->year;

        // Get schedules for the month
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        $schedules = Schedule::with(['user', 'client', 'shift'])
            ->whereBetween('scheduled_date', [$startDate, $endDate])
            ->orderBy('scheduled_date')
            ->orderBy('start_time')
            ->get();

        // Format schedules for calendar
        $calendarData = $schedules->map(function($schedule) {
            return [
                'id' => $schedule->id,
                'title' => $schedule->user->full_name . ' - ' . $schedule->client->name,
                'start' => $schedule->scheduled_date . 'T' . $schedule->start_time,
                'end' => $schedule->scheduled_date . 'T' . $schedule->end_time,
                'backgroundColor' => $schedule->shift->color ?? '#3B82F6',
                'borderColor' => $schedule->shift->color ?? '#3B82F6',
                'extendedProps' => [
                    'user' => $schedule->user->full_name,
                    'client' => $schedule->client->name,
                    'shift' => $schedule->shift->name,
                    'status' => $schedule->status,
                ],
                'url' => route('admin.schedules.show', $schedule),
            ];
        });

        // Get statistics for the month
        $stats = [
            'total' => $schedules->count(),
            'scheduled' => $schedules->where('status', 'scheduled')->count(),
            'completed' => $schedules->where('status', 'completed')->count(),
            'cancelled' => $schedules->where('status', 'cancelled')->count(),
        ];

        // Get users and clients for quick filters
        $users = User::active()->orderBy('firstname')->get();
        $clients = Client::active()->orderBy('name')->get();

        return view('admin.schedules.calendar', compact('calendarData', 'stats', 'month', 'year', 'users', 'clients'));
    }

    /**
     * Get schedule data as JSON for AJAX calendar.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCalendarData(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');

        $schedules = Schedule::with(['user', 'client', 'shift'])
            ->whereBetween('scheduled_date', [$start, $end])
            ->orderBy('scheduled_date')
            ->orderBy('start_time')
            ->get();

        $events = $schedules->map(function($schedule) {
            return [
                'id' => $schedule->id,
                'title' => $schedule->user->full_name . ' - ' . $schedule->client->name,
                'start' => $schedule->scheduled_date . 'T' . $schedule->start_time,
                'end' => $schedule->scheduled_date . 'T' . $schedule->end_time,
                'backgroundColor' => $schedule->shift->color ?? '#3B82F6',
                'borderColor' => $schedule->shift->color ?? '#3B82F6',
                'extendedProps' => [
                    'user' => $schedule->user->full_name,
                    'client' => $schedule->client->name,
                    'shift' => $schedule->shift->name,
                    'status' => $schedule->status,
                ],
                'url' => route('admin.schedules.show', $schedule),
            ];
        });

        return response()->json($events);
    }

    /**
     * Bulk action handler for schedules.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|string|in:complete,cancel,reschedule,delete',
            'schedule_ids' => 'required|array|min:1',
            'schedule_ids.*' => 'exists:schedules,id',
        ]);

        $scheduleIds = $request->schedule_ids;
        $count = 0;

        switch ($request->action) {
            case 'complete':
                $count = Schedule::whereIn('id', $scheduleIds)->update(['status' => 'completed']);
                $message = "{$count} schedule(s) marked as completed.";
                break;

            case 'cancel':
                $count = Schedule::whereIn('id', $scheduleIds)->update(['status' => 'cancelled']);
                $message = "{$count} schedule(s) cancelled.";
                break;

            case 'reschedule':
                $count = Schedule::whereIn('id', $scheduleIds)->update(['status' => 'rescheduled']);
                $message = "{$count} schedule(s) marked for rescheduling.";
                break;

            case 'delete':
                // Only delete schedules without attendance
                $schedules = Schedule::whereIn('id', $scheduleIds)
                    ->whereDoesntHave('attendance')
                    ->get();

                foreach ($schedules as $schedule) {
                    $schedule->delete();
                    $count++;
                }

                $message = "{$count} schedule(s) deleted.";

                if ($count < count($scheduleIds)) {
                    $message .= ' Some schedules could not be deleted due to existing attendance records.';
                }
                break;
        }

        return redirect()
            ->back()
            ->with('success', $message);
    }

    /**
     * Duplicate an existing schedule.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\View\View
     */
    public function duplicate(Schedule $schedule)
    {
        // Get active users, clients, and shifts for dropdowns
        $users = User::active()->orderBy('firstname')->get();
        $clients = Client::active()->orderBy('name')->get();
        $shifts = Shift::active()->orderBy('start_time')->get();

        // Clone the schedule data
        $scheduleData = $schedule->replicate()->toArray();

        return view('admin.schedules.create', compact('users', 'clients', 'shifts', 'scheduleData'));
    }

    /**
     * Check for scheduling conflicts.
     *
     * @param  int  $userId
     * @param  string  $date
     * @param  string  $startTime
     * @param  string  $endTime
     * @param  int|null  $excludeScheduleId
     * @return \App\Models\Schedule|null
     */
    private function checkSchedulingConflict($userId, $date, $startTime, $endTime, $excludeScheduleId = null)
    {
        $query = Schedule::where('user_id', $userId)
            ->where('scheduled_date', $date)
            ->where('status', '!=', 'cancelled')
            ->where(function($q) use ($startTime, $endTime) {
                $q->where(function($query) use ($startTime, $endTime) {
                    $query->where('start_time', '<=', $startTime)
                          ->where('end_time', '>', $startTime);
                })->orWhere(function($query) use ($startTime, $endTime) {
                    $query->where('start_time', '<', $endTime)
                          ->where('end_time', '>=', $endTime);
                })->orWhere(function($query) use ($startTime, $endTime) {
                    $query->where('start_time', '>=', $startTime)
                          ->where('end_time', '<=', $endTime);
                });
            });

        if ($excludeScheduleId) {
            $query->where('id', '!=', $excludeScheduleId);
        }

        return $query->with(['client'])->first();
    }

    /**
     * Get user's schedules for a specific date.
     * Can be used for AJAX requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserSchedules(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
        ]);

        $schedules = Schedule::with(['client', 'shift'])
            ->where('user_id', $request->user_id)
            ->where('scheduled_date', $request->date)
            ->where('status', '!=', 'cancelled')
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'schedules' => $schedules,
            'count' => $schedules->count(),
        ]);
    }

    /**
     * Display the printable schedules page with filters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function printView(Request $request)
    {
        // Build query with relationships
        $query = Schedule::with(['user', 'client', 'shift']);

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by client
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('scheduled_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('scheduled_date', '<=', $request->date_to);
        }

        // Default to current week if no date filter
        if (!$request->filled('date_from') && !$request->filled('date_to')) {
            $query->whereBetween('scheduled_date', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ]);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default to scheduled only
            $query->where('status', 'scheduled');
        }

        // Order by date and time
        $schedules = $query->orderBy('scheduled_date', 'asc')
                          ->orderBy('start_time', 'asc')
                          ->get();

        // Get data for filters
        $users = User::active()->orderBy('firstname')->get();
        $clients = Client::active()->orderBy('name')->get();

        // Group schedules by date for better display
        $groupedSchedules = $schedules->groupBy(function($schedule) {
            return $schedule->scheduled_date ? $schedule->scheduled_date->format('Y-m-d') : 'No Date';
        });

        return view('admin.schedules.print', compact('schedules', 'groupedSchedules', 'users', 'clients'));
    }

    /**
     * Display the weekly schedule overview with sessions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function weeklyOverview(Request $request)
    {
        // Get filter parameters
        $userId = $request->input('user_id');
        $weekOffset = $request->input('week', 0);

        // Calculate week start and end dates
        $weekStart = now()->startOfWeek()->addWeeks($weekOffset);
        $weekEnd = now()->startOfWeek()->addWeeks($weekOffset)->endOfWeek();

        // Build query
        $query = Schedule::with(['user', 'client'])
            ->whereBetween('scheduled_date', [$weekStart, $weekEnd])
            ->where('status', '!=', 'cancelled');

        // Filter by user if specified
        if ($userId) {
            $query->where('user_id', $userId);
        }

        // Get all schedules for the week
        $schedules = $query->get();

        // Get all instructors
        $users = User::active()->orderBy('firstname')->get();

        // Structure data by day and session
        $weekDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $sessions = ['morning', 'mid-morning', 'afternoon'];

        // Map day names to Carbon day constants (0=Sunday, 1=Monday, 2=Tuesday, etc.)
        $dayMapping = [
            'monday' => Carbon::MONDAY,
            'tuesday' => Carbon::TUESDAY,
            'wednesday' => Carbon::WEDNESDAY,
            'thursday' => Carbon::THURSDAY,
            'friday' => Carbon::FRIDAY,
        ];

        $weekSchedule = [];
        foreach ($weekDays as $day) {
            // Calculate the date for this day of the week
            $dayOfWeek = $dayMapping[$day];
            $date = $weekStart->copy()->startOfWeek()->addDays($dayOfWeek - 1);

            $weekSchedule[$day] = [
                'date' => $date,
                'sessions' => []
            ];

            foreach ($sessions as $session) {
                $daySchedules = $schedules->filter(function($schedule) use ($date, $session) {
                    return $schedule->scheduled_date &&
                           $schedule->scheduled_date->isSameDay($date) &&
                           $schedule->session_time === $session;
                });

                $weekSchedule[$day]['sessions'][$session] = $daySchedules;
            }
        }

        return view('admin.schedules.weekly', compact('weekSchedule', 'users', 'weekStart', 'weekEnd', 'weekOffset', 'userId'));
    }
}
