<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShiftController extends Controller
{
    /**
     * Display a listing of shifts with search and filter functionality.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Build query
        $query = Shift::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Filter by status (active/inactive)
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Order by start time
        $shifts = $query->orderBy('start_time')->paginate(15)->withQueryString();

        return view('admin.shifts.index', compact('shifts'));
    }

    /**
     * Show the form for creating a new shift.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.shifts.create');
    }

    /**
     * Store a newly created shift in storage.
     * Validates shift times and ensures start_time is before end_time.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:shifts,name',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Shift name is required.',
            'name.unique' => 'This shift name already exists.',
            'start_time.required' => 'Start time is required.',
            'start_time.date_format' => 'Start time must be in HH:MM format.',
            'end_time.required' => 'End time is required.',
            'end_time.date_format' => 'End time must be in HH:MM format.',
            'end_time.after' => 'End time must be after start time.',
            'color.regex' => 'Color must be a valid hex color code (e.g., #FF5733).',
        ]);

        // Set default color if not provided
        if (empty($validated['color'])) {
            $validated['color'] = $this->generateRandomColor();
        }

        // Set is_active boolean
        $validated['is_active'] = $request->has('is_active') ? true : false;

        // Create the shift
        $shift = Shift::create($validated);

        return redirect()
            ->route('admin.shifts.show', $shift)
            ->with('success', 'Shift created successfully.');
    }

    /**
     * Display the specified shift.
     *
     * @param  \App\Models\Shift  $shift
     * @return \Illuminate\View\View
     */
    public function show(Shift $shift)
    {
        // Load relationships
        $shift->load(['schedules' => function($query) {
            $query->with(['user', 'client'])->latest()->take(15);
        }]);

        // Get shift statistics
        $shiftStats = [
            'total_schedules' => $shift->schedules()->count(),
            'upcoming_schedules' => $shift->schedules()
                ->where('scheduled_date', '>=', now()->toDateString())
                ->count(),
            'total_attendances' => $shift->attendances()->count(),
        ];

        // Calculate shift duration
        $start = \Carbon\Carbon::createFromFormat('H:i:s', $shift->start_time);
        $end = \Carbon\Carbon::createFromFormat('H:i:s', $shift->end_time);
        $duration = $start->diffInMinutes($end);
        $durationFormatted = floor($duration / 60) . 'h ' . ($duration % 60) . 'm';

        return view('admin.shifts.show', compact('shift', 'shiftStats', 'durationFormatted'));
    }

    /**
     * Show the form for editing the specified shift.
     *
     * @param  \App\Models\Shift  $shift
     * @return \Illuminate\View\View
     */
    public function edit(Shift $shift)
    {
        return view('admin.shifts.edit', compact('shift'));
    }

    /**
     * Update the specified shift in storage.
     * Validates shift times and ensures start_time is before end_time.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Shift  $shift
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Shift $shift)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('shifts', 'name')->ignore($shift->id)
            ],
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Shift name is required.',
            'name.unique' => 'This shift name already exists.',
            'start_time.required' => 'Start time is required.',
            'start_time.date_format' => 'Start time must be in HH:MM format.',
            'end_time.required' => 'End time is required.',
            'end_time.date_format' => 'End time must be in HH:MM format.',
            'end_time.after' => 'End time must be after start time.',
            'color.regex' => 'Color must be a valid hex color code (e.g., #FF5733).',
        ]);

        // Ensure color is set
        if (empty($validated['color'])) {
            $validated['color'] = $shift->color ?? $this->generateRandomColor();
        }

        // Set is_active boolean
        $validated['is_active'] = $request->has('is_active') ? true : false;

        // Update the shift
        $shift->update($validated);

        return redirect()
            ->route('admin.shifts.show', $shift)
            ->with('success', 'Shift updated successfully.');
    }

    /**
     * Remove the specified shift from storage.
     *
     * @param  \App\Models\Shift  $shift
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Shift $shift)
    {
        // Check if shift has schedules or attendances
        $hasSchedules = $shift->schedules()->count() > 0;
        $hasAttendances = $shift->attendances()->count() > 0;

        if ($hasSchedules || $hasAttendances) {
            return redirect()
                ->route('admin.shifts.index')
                ->with('error', 'Cannot delete shift with existing schedules or attendance records. Consider deactivating instead.');
        }

        // Delete the shift
        $shift->delete();

        return redirect()
            ->route('admin.shifts.index')
            ->with('success', 'Shift deleted successfully.');
    }

    /**
     * Toggle the active status of a shift.
     *
     * @param  \App\Models\Shift  $shift
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleStatus(Shift $shift)
    {
        $shift->update([
            'is_active' => !$shift->is_active
        ]);

        $status = $shift->is_active ? 'activated' : 'deactivated';

        return redirect()
            ->back()
            ->with('success', "Shift {$status} successfully.");
    }

    /**
     * Bulk action handler for shifts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|string|in:activate,deactivate,delete',
            'shift_ids' => 'required|array|min:1',
            'shift_ids.*' => 'exists:shifts,id',
        ]);

        $shiftIds = $request->shift_ids;
        $count = 0;

        switch ($request->action) {
            case 'activate':
                $count = Shift::whereIn('id', $shiftIds)->update(['is_active' => true]);
                $message = "{$count} shift(s) activated successfully.";
                break;

            case 'deactivate':
                $count = Shift::whereIn('id', $shiftIds)->update(['is_active' => false]);
                $message = "{$count} shift(s) deactivated successfully.";
                break;

            case 'delete':
                // Only delete shifts without schedules or attendances
                $shifts = Shift::whereIn('id', $shiftIds)
                    ->whereDoesntHave('schedules')
                    ->whereDoesntHave('attendances')
                    ->get();

                foreach ($shifts as $shift) {
                    $shift->delete();
                    $count++;
                }

                $message = "{$count} shift(s) deleted successfully.";

                if ($count < count($shiftIds)) {
                    $message .= ' Some shifts could not be deleted due to existing records.';
                }
                break;
        }

        return redirect()
            ->route('admin.shifts.index')
            ->with('success', $message);
    }

    /**
     * Duplicate an existing shift.
     *
     * @param  \App\Models\Shift  $shift
     * @return \Illuminate\Http\RedirectResponse
     */
    public function duplicate(Shift $shift)
    {
        // Create a new shift with duplicated data
        $newShift = $shift->replicate();
        $newShift->name = $shift->name . ' (Copy)';
        $newShift->save();

        return redirect()
            ->route('admin.shifts.edit', $newShift)
            ->with('success', 'Shift duplicated successfully. You can now modify it.');
    }

    /**
     * Generate a random hex color code.
     *
     * @return string
     */
    private function generateRandomColor()
    {
        // Generate a random color that's not too light or too dark
        $colors = [
            '#3B82F6', // Blue
            '#10B981', // Green
            '#F59E0B', // Amber
            '#EF4444', // Red
            '#8B5CF6', // Violet
            '#EC4899', // Pink
            '#14B8A6', // Teal
            '#F97316', // Orange
            '#6366F1', // Indigo
            '#84CC16', // Lime
        ];

        return $colors[array_rand($colors)];
    }

    /**
     * Validate shift time overlap for a specific date.
     * This can be called via AJAX to check for conflicts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateTimes(Request $request)
    {
        $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'shift_id' => 'nullable|exists:shifts,id',
        ]);

        $start = $request->start_time;
        $end = $request->end_time;

        // Check if start time is before end time
        if ($start >= $end) {
            return response()->json([
                'valid' => false,
                'message' => 'End time must be after start time.'
            ]);
        }

        // Check for overlapping shifts (optional)
        $query = Shift::where(function($q) use ($start, $end) {
            $q->where(function($query) use ($start, $end) {
                $query->where('start_time', '<=', $start)
                      ->where('end_time', '>', $start);
            })->orWhere(function($query) use ($start, $end) {
                $query->where('start_time', '<', $end)
                      ->where('end_time', '>=', $end);
            })->orWhere(function($query) use ($start, $end) {
                $query->where('start_time', '>=', $start)
                      ->where('end_time', '<=', $end);
            });
        });

        // Exclude current shift if updating
        if ($request->filled('shift_id')) {
            $query->where('id', '!=', $request->shift_id);
        }

        $overlappingShifts = $query->get();

        if ($overlappingShifts->count() > 0) {
            return response()->json([
                'valid' => true,
                'warning' => true,
                'message' => 'Warning: This shift overlaps with existing shift(s): ' .
                            $overlappingShifts->pluck('name')->implode(', '),
                'overlapping_shifts' => $overlappingShifts
            ]);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Shift times are valid.'
        ]);
    }
}
