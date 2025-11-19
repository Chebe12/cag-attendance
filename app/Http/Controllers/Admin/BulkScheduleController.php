<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Schedule;
use App\Models\ScheduleCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class BulkScheduleController extends Controller
{
    /**
     * Show the bulk schedule creation interface
     */
    public function create(Request $request)
    {
        // Get category or create new if not specified
        $categoryId = $request->get('category_id');
        $category = $categoryId ? ScheduleCategory::findOrFail($categoryId) : null;

        // Get all active categories for selection
        $categories = ScheduleCategory::whereIn('status', ['draft', 'active'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all active clients
        $clients = Client::where('status', 'active')->orderBy('name')->get();

        // Get all active instructors
        $instructors = User::where('status', 'active')
            ->whereIn('user_type', ['instructor', 'office_staff'])
            ->orderBy('firstname')
            ->get();

        // Days of week
        $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        // Sessions
        $sessions = [
            'morning' => '8:30 AM - 10:00 AM',
            'mid-morning' => '10:30 AM - 12:00 PM',
            'afternoon' => '12:30 PM - 2:00 PM',
        ];

        // If editing existing schedules, load them (both published and drafts)
        $existingSchedules = [];
        $editMode = 'create'; // Default mode
        if ($category) {
            // Load both published and draft schedules for editing
            $schedules = $category->schedules()->with(['client', 'user'])->get();
            foreach ($schedules as $schedule) {
                $key = $schedule->client_id . '_' . $schedule->day_of_week . '_' . $schedule->session_time;
                if (!isset($existingSchedules[$key])) {
                    $existingSchedules[$key] = [];
                }
                $existingSchedules[$key][] = $schedule->user_id;
            }
            // Set edit mode if there are published schedules
            if ($category->publishedSchedules()->count() > 0) {
                $editMode = 'edit';
            }
        }

        return view('admin.schedules.bulk-create', compact(
            'category',
            'categories',
            'clients',
            'instructors',
            'daysOfWeek',
            'sessions',
            'existingSchedules',
            'editMode'
        ));
    }

    /**
     * Store or update bulk schedules
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:schedule_categories,id',
            'schedules' => 'required|array',
            'schedules.*.client_id' => 'required|exists:clients,id',
            'schedules.*.day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'schedules.*.session_time' => 'required|in:morning,mid-morning,afternoon',
            'schedules.*.instructor_ids' => 'required|array|min:1',
            'schedules.*.instructor_ids.*' => 'required|exists:users,id',
            'draft_status' => 'required|in:draft,published',
        ]);

        $category = ScheduleCategory::findOrFail($validated['category_id']);
        $errors = [];
        $created = 0;
        $updated = 0;

        DB::beginTransaction();

        try {
            // Build a list of schedule combinations that should exist
            $submittedSchedules = [];
            foreach ($validated['schedules'] as $scheduleData) {
                foreach ($scheduleData['instructor_ids'] as $instructorId) {
                    $submittedSchedules[] = [
                        'client_id' => $scheduleData['client_id'],
                        'user_id' => $instructorId,
                        'day_of_week' => $scheduleData['day_of_week'],
                        'session_time' => $scheduleData['session_time'],
                    ];
                }
            }

            // Delete schedules that were removed (not in submitted list)
            $existingSchedules = Schedule::where('category_id', $category->id)->get();
            $deleted = 0;
            foreach ($existingSchedules as $existing) {
                $found = false;
                foreach ($submittedSchedules as $submitted) {
                    if ($existing->client_id == $submitted['client_id'] &&
                        $existing->user_id == $submitted['user_id'] &&
                        $existing->day_of_week == $submitted['day_of_week'] &&
                        $existing->session_time == $submitted['session_time']) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $existing->delete();
                    $deleted++;
                }
            }

            // Create or update submitted schedules
            foreach ($validated['schedules'] as $index => $scheduleData) {
                $clientId = $scheduleData['client_id'];
                $dayOfWeek = $scheduleData['day_of_week'];
                $sessionTime = $scheduleData['session_time'];
                $instructorIds = $scheduleData['instructor_ids'];

                // Get session times
                $sessionTimes = Schedule::getSessionTimes($sessionTime);

                foreach ($instructorIds as $instructorId) {
                    // Check for conflicts (only for published)
                    if ($validated['draft_status'] === 'published') {
                        $conflict = Schedule::where('category_id', '!=', $category->id)
                            ->where('user_id', $instructorId)
                            ->where('day_of_week', $dayOfWeek)
                            ->where('session_time', $sessionTime)
                            ->where('draft_status', 'published')
                            ->exists();

                        if ($conflict) {
                            $instructor = User::find($instructorId);
                            $errors[] = "Conflict: {$instructor->name} is already assigned on {$dayOfWeek} {$sessionTime} in another category.";
                            continue;
                        }
                    }

                    // Create or update schedule (check regardless of draft_status)
                    $scheduleExists = Schedule::where('category_id', $category->id)
                        ->where('user_id', $instructorId)
                        ->where('client_id', $clientId)
                        ->where('day_of_week', $dayOfWeek)
                        ->where('session_time', $sessionTime)
                        ->first();

                    if ($scheduleExists) {
                        $scheduleExists->update([
                            'start_time' => $sessionTimes['start'],
                            'end_time' => $sessionTimes['end'],
                            'is_recurring' => true,
                            'status' => 'scheduled',
                            'draft_status' => $validated['draft_status'],
                        ]);
                        $updated++;
                    } else {
                        Schedule::create([
                            'category_id' => $category->id,
                            'user_id' => $instructorId,
                            'client_id' => $clientId,
                            'day_of_week' => $dayOfWeek,
                            'session_time' => $sessionTime,
                            'start_time' => $sessionTimes['start'],
                            'end_time' => $sessionTimes['end'],
                            'is_recurring' => true,
                            'status' => 'scheduled',
                            'draft_status' => $validated['draft_status'],
                            'created_by' => Auth::id(),
                        ]);
                        $created++;
                    }
                }
            }

            DB::commit();

            $message = $validated['draft_status'] === 'draft'
                ? "Schedules saved as draft! Created: {$created}, Updated: {$updated}, Deleted: {$deleted}"
                : "Schedules published successfully! Created: {$created}, Updated: {$updated}, Deleted: {$deleted}";

            if (count($errors) > 0) {
                $message .= " However, there were some conflicts: " . implode('; ', array_slice($errors, 0, 3));
                if (count($errors) > 3) {
                    $message .= " and " . (count($errors) - 3) . " more.";
                }
            }

            // Return JSON for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'redirect' => route('admin.schedule-categories.show', $category),
                    'created' => $created,
                    'updated' => $updated,
                    'errors' => $errors
                ]);
            }

            return redirect()
                ->route('admin.schedule-categories.show', $category)
                ->with($errors ? 'warning' : 'success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk schedule creation error: ' . $e->getMessage());

            // Return JSON error for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating schedules: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Error creating schedules: ' . $e->getMessage());
        }
    }

    /**
     * Publish draft schedules
     */
    public function publish(ScheduleCategory $category)
    {
        try {
            $draftCount = $category->draftSchedules()->count();

            if ($draftCount === 0) {
                return back()->with('info', 'No draft schedules to publish.');
            }

            // Check for conflicts before publishing
            $conflicts = $this->checkConflicts($category);

            if (count($conflicts) > 0) {
                return back()->with('error', 'Cannot publish: ' . implode('; ', array_slice($conflicts, 0, 5)));
            }

            // Publish all drafts
            $category->draftSchedules()->update(['draft_status' => 'published']);

            return back()->with('success', "Successfully published {$draftCount} schedules!");

        } catch (\Exception $e) {
            Log::error('Error publishing schedules: ' . $e->getMessage());
            return back()->with('error', 'Error publishing schedules: ' . $e->getMessage());
        }
    }

    /**
     * Delete all draft schedules for a category
     */
    public function deleteDrafts(ScheduleCategory $category)
    {
        $count = $category->draftSchedules()->count();
        $category->draftSchedules()->delete();

        return back()->with('success', "Deleted {$count} draft schedules.");
    }

    /**
     * Check for scheduling conflicts
     */
    private function checkConflicts(ScheduleCategory $category)
    {
        $conflicts = [];
        $schedules = $category->draftSchedules()->with(['user', 'client'])->get();

        foreach ($schedules as $schedule) {
            $conflicting = Schedule::where('category_id', $category->id)
                ->where('user_id', $schedule->user_id)
                ->where('day_of_week', $schedule->day_of_week)
                ->where('session_time', $schedule->session_time)
                ->where('draft_status', 'published')
                ->where('id', '!=', $schedule->id)
                ->with('client')
                ->first();

            if ($conflicting) {
                $conflicts[] = "{$schedule->user->name} is already assigned to {$conflicting->client->name} on {$schedule->day_of_week} {$schedule->session_time}";
            }
        }

        return $conflicts;
    }

    /**
     * Validate schedules via AJAX
     */
    public function validate(Request $request)
    {
        $scheduleData = $request->input('schedule');
        $categoryId = $request->input('category_id');

        $conflicts = [];

        foreach ($scheduleData['instructor_ids'] ?? [] as $instructorId) {
            if (Schedule::hasConflict(
                $instructorId,
                $scheduleData['day_of_week'],
                $scheduleData['session_time'],
                $categoryId
            )) {
                $instructor = User::find($instructorId);
                $conflicts[] = $instructor->name;
            }
        }

        return response()->json([
            'valid' => count($conflicts) === 0,
            'conflicts' => $conflicts,
        ]);
    }
}
