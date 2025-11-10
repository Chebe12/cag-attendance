<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users with search and filter functionality.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Build query
        $query = User::query();

        // Search functionality - search by employee_no, names, email, department
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('employee_no', 'like', '%' . $search . '%')
                  ->orWhere('firstname', 'like', '%' . $search . '%')
                  ->orWhere('middlename', 'like', '%' . $search . '%')
                  ->orWhere('lastname', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('department', 'like', '%' . $search . '%')
                  ->orWhere('position', 'like', '%' . $search . '%');
            });
        }

        // Filter by user type
        if ($request->filled('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        // Filter by department
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Order by latest first
        $users = $query->latest()->paginate(15)->withQueryString();

        // Get unique departments for filter dropdown
        $departments = User::distinct('department')
            ->whereNotNull('department')
            ->pluck('department')
            ->sort();

        return view('admin.users.index', compact('users', 'departments'));
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $departments = \App\Models\Department::active()->orderBy('name')->get();
        return view('admin.users.create', compact('departments'));
    }

    /**
     * Store a newly created user in storage.
     * Validates all fields and hashes the password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'employee_no' => 'required|string|max:50|unique:users,employee_no',
            'firstname' => 'required|string|max:100',
            'middlename' => 'nullable|string|max:100',
            'lastname' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'department_id' => 'nullable|exists:departments,id',
            'department' => 'nullable|string|max:100', // Legacy field
            'position' => 'required|string|max:100',
            'user_type' => 'required|string|in:admin,instructor,office_staff',
            'status' => 'required|string|in:active,inactive,suspended',
        ], [
            'employee_no.required' => 'Employee number is required.',
            'employee_no.unique' => 'This employee number is already in use.',
            'email.unique' => 'This email address is already registered.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min' => 'Password must be at least 8 characters.',
            'user_type.in' => 'Invalid user type selected.',
            'status.in' => 'Invalid status selected.',
            'department_id.exists' => 'Selected department does not exist.',
        ]);

        // Hash the password
        $validated['password'] = Hash::make($validated['password']);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        }

        // Create the user
        $user = User::create($validated);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function show(User $user)
    {
        // Load relationships for better insights
        $user->load([
            'schedules' => function($query) {
                $query->latest()->take(10);
            },
            'attendances' => function($query) {
                $query->latest()->take(10);
            },
            'leaveRequests' => function($query) {
                $query->latest()->take(5);
            }
        ]);

        // Get attendance statistics
        $attendanceStats = [
            'total' => $user->attendances()->count(),
            'present' => $user->attendances()->where('status', 'present')->count(),
            'late' => $user->attendances()->where('status', 'late')->count(),
            'absent' => $user->attendances()->where('status', 'absent')->count(),
        ];

        return view('admin.users.show', compact('user', 'attendanceStats'));
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        $departments = \App\Models\Department::active()->orderBy('name')->get();
        return view('admin.users.edit', compact('user', 'departments'));
    }

    /**
     * Update the specified user in storage.
     * Hashes password only if provided.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        // Validate the request
        $validated = $request->validate([
            'employee_no' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'employee_no')->ignore($user->id)
            ],
            'firstname' => 'required|string|max:100',
            'middlename' => 'nullable|string|max:100',
            'lastname' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id)
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'department_id' => 'nullable|exists:departments,id',
            'department' => 'nullable|string|max:100', // Legacy field
            'position' => 'required|string|max:100',
            'user_type' => 'required|string|in:admin,instructor,office_staff',
            'status' => 'required|string|in:active,inactive,suspended',
        ], [
            'employee_no.required' => 'Employee number is required.',
            'employee_no.unique' => 'This employee number is already in use.',
            'email.unique' => 'This email address is already registered.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min' => 'Password must be at least 8 characters.',
            'user_type.in' => 'Invalid user type selected.',
            'status.in' => 'Invalid status selected.',
            'department_id.exists' => 'Selected department does not exist.',
        ]);

        // Hash the password only if provided
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        }

        // Update the user
        $user->update($validated);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        // Check if user has related records
        $hasSchedules = $user->schedules()->count() > 0;
        $hasAttendances = $user->attendances()->count() > 0;

        if ($hasSchedules || $hasAttendances) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Cannot delete user with existing schedules or attendance records. Consider deactivating instead.');
        }

        // Delete avatar if exists
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Delete the user
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Bulk action handler for users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|string|in:activate,deactivate,suspend,delete',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
        ]);

        $userIds = $request->user_ids;

        // Prevent action on current user
        if (in_array(auth()->id(), $userIds)) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'You cannot perform bulk actions on your own account.');
        }

        $count = 0;

        switch ($request->action) {
            case 'activate':
                $count = User::whereIn('id', $userIds)->update(['status' => 'active']);
                $message = "{$count} user(s) activated successfully.";
                break;

            case 'deactivate':
                $count = User::whereIn('id', $userIds)->update(['status' => 'inactive']);
                $message = "{$count} user(s) deactivated successfully.";
                break;

            case 'suspend':
                $count = User::whereIn('id', $userIds)->update(['status' => 'suspended']);
                $message = "{$count} user(s) suspended successfully.";
                break;

            case 'delete':
                // Only delete users without schedules or attendances
                $usersToDelete = User::whereIn('id', $userIds)
                    ->whereDoesntHave('schedules')
                    ->whereDoesntHave('attendances')
                    ->get();

                foreach ($usersToDelete as $user) {
                    if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                        Storage::disk('public')->delete($user->avatar);
                    }
                    $user->delete();
                    $count++;
                }

                $message = "{$count} user(s) deleted successfully.";

                if ($count < count($userIds)) {
                    $message .= ' Some users could not be deleted due to existing records.';
                }
                break;
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', $message);
    }
}
