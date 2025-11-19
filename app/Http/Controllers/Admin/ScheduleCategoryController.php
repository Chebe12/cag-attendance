<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScheduleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ScheduleCategoryController extends Controller
{
    /**
     * Display a listing of schedule categories
     */
    public function index(Request $request)
    {
        $query = ScheduleCategory::with(['creator', 'schedules']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by name or code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $categories = $query->latest()->paginate(15);

        return view('admin.schedule-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new schedule category
     */
    public function create()
    {
        return view('admin.schedule-categories.create');
    }

    /**
     * Store a newly created schedule category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:schedule_categories,code',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:draft,active,completed,archived',
        ]);

        // Auto-generate code if not provided
        if (empty($validated['code'])) {
            $validated['code'] = $this->generateCode($validated['name']);
        }

        $validated['created_by'] = Auth::id();

        $category = ScheduleCategory::create($validated);

        return redirect()
            ->route('admin.schedule-categories.show', $category)
            ->with('success', 'Schedule category created successfully!');
    }

    /**
     * Display the specified schedule category
     */
    public function show(ScheduleCategory $scheduleCategory)
    {
        $scheduleCategory->load(['creator', 'schedules.user', 'schedules.client']);

        // Get schedules grouped by day and session
        $groupedSchedules = $scheduleCategory->schedules()
            ->with(['user', 'client'])
            ->get()
            ->groupBy('day_of_week')
            ->map(function($daySchedules) {
                return $daySchedules->groupBy('session_time');
            });

        $stats = [
            'total_schedules' => $scheduleCategory->schedules()->count(),
            'published_schedules' => $scheduleCategory->publishedSchedules()->count(),
            'draft_schedules' => $scheduleCategory->draftSchedules()->count(),
            'unique_clients' => $scheduleCategory->schedules()->distinct('client_id')->count(),
            'unique_instructors' => $scheduleCategory->schedules()->distinct('user_id')->count(),
        ];

        return view('admin.schedule-categories.show', compact('scheduleCategory', 'groupedSchedules', 'stats'));
    }

    /**
     * Show the form for editing the specified schedule category
     */
    public function edit(ScheduleCategory $scheduleCategory)
    {
        return view('admin.schedule-categories.edit', compact('scheduleCategory'));
    }

    /**
     * Update the specified schedule category
     */
    public function update(Request $request, ScheduleCategory $scheduleCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:schedule_categories,code,' . $scheduleCategory->id,
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:draft,active,completed,archived',
        ]);

        $scheduleCategory->update($validated);

        return redirect()
            ->route('admin.schedule-categories.show', $scheduleCategory)
            ->with('success', 'Schedule category updated successfully!');
    }

    /**
     * Remove the specified schedule category
     */
    public function destroy(ScheduleCategory $scheduleCategory)
    {
        // Check if category has any published schedules
        if ($scheduleCategory->publishedSchedules()->count() > 0) {
            return back()->with('error', 'Cannot delete a category with published schedules. Archive it instead.');
        }

        $scheduleCategory->delete();

        return redirect()
            ->route('admin.schedule-categories.index')
            ->with('success', 'Schedule category deleted successfully!');
    }

    /**
     * Activate a schedule category
     */
    public function activate(ScheduleCategory $scheduleCategory)
    {
        $scheduleCategory->update(['status' => 'active']);

        return back()->with('success', 'Category activated successfully!');
    }

    /**
     * Archive a schedule category
     */
    public function archive(ScheduleCategory $scheduleCategory)
    {
        $scheduleCategory->update(['status' => 'archived']);

        return back()->with('success', 'Category archived successfully!');
    }

    /**
     * Generate a unique code from category name
     */
    private function generateCode($name)
    {
        $code = Str::upper(Str::slug(Str::limit($name, 20, ''), '-'));
        $baseCode = $code;
        $counter = 1;

        while (ScheduleCategory::where('code', $code)->exists()) {
            $code = $baseCode . '-' . $counter;
            $counter++;
        }

        return $code;
    }
}
