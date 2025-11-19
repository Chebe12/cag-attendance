<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of clients with search and filter functionality.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Build query - include trashed if requested
        $query = Client::query();

        // Include soft deleted records if requested
        if ($request->filled('show_deleted') && $request->show_deleted === 'true') {
            $query->withTrashed();
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('contact_person', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%')
                  ->orWhere('city', 'like', '%' . $search . '%');
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by city
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        // Order by latest first
        $clients = $query->latest()->paginate(15)->withQueryString();

        // Get unique cities for filter dropdown
        $cities = Client::distinct('city')
            ->whereNotNull('city')
            ->pluck('city')
            ->sort();

        return view('admin.clients.index', compact('clients', 'cities'));
    }

    /**
     * Show the form for creating a new client.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.clients.create');
    }

    /**
     * Store a newly created client in storage.
     * Validates all client fields.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:clients,code',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:clients,email',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'status' => 'required|string|in:active,inactive',
            'notes' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Client name is required.',
            'code.unique' => 'This client code is already in use.',
            'contact_person.required' => 'Contact person name is required.',
            'email.required' => 'Email address is required.',
            'email.unique' => 'This email address is already registered.',
            'phone.required' => 'Phone number is required.',
            'status.in' => 'Invalid status selected.',
        ]);

        // Auto-generate code if not provided
        if (empty($validated['code'])) {
            $validated['code'] = Client::generateCode($validated['name']);
        }

        // Create the client
        $client = Client::create($validated);

        return redirect()
            ->route('admin.clients.show', $client)
            ->with('success', 'Client created successfully.');
    }

    /**
     * Display the specified client.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        // Find client including soft deleted
        $client = Client::withTrashed()->findOrFail($id);

        // Load relationships and counts
        $client->load(['schedules' => function($query) {
            $query->with(['user', 'shift'])->latest()->take(15);
        }]);

        $client->loadCount(['shifts', 'schedules']);

        // Get unique staff assigned to this client's shifts
        $staffCount = \App\Models\User::whereHas('schedules', function($query) use ($client) {
            $query->where('client_id', $client->id);
        })->distinct()->count();

        // Get schedule statistics
        $scheduleStats = [
            'total' => $client->schedules()->count(),
            'upcoming' => $client->schedules()->where('scheduled_date', '>=', now()->toDateString())->count(),
            'completed' => $client->schedules()->where('scheduled_date', '<', now()->toDateString())->count(),
        ];

        return view('admin.clients.show', compact('client', 'scheduleStats', 'staffCount'));
    }

    /**
     * Show the form for editing the specified client.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\View\View
     */
    public function edit(Client $client)
    {
        return view('admin.clients.edit', compact('client'));
    }

    /**
     * Update the specified client in storage.
     * Validates all client fields with proper unique rule.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Client $client)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:clients,code,' . $client->id,
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:clients,email,' . $client->id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'status' => 'required|string|in:active,inactive',
            'notes' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Client name is required.',
            'code.unique' => 'This client code is already in use.',
            'contact_person.required' => 'Contact person name is required.',
            'email.required' => 'Email address is required.',
            'email.unique' => 'This email address is already registered.',
            'phone.required' => 'Phone number is required.',
            'status.in' => 'Invalid status selected.',
        ]);

        // Update the client
        $client->update($validated);

        return redirect()
            ->route('admin.clients.show', $client)
            ->with('success', 'Client updated successfully.');
    }

    /**
     * Soft delete the specified client from storage.
     * Uses Laravel's soft delete feature.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Client $client)
    {
        // Check if client has active schedules
        $hasActiveSchedules = $client->schedules()
            ->where('scheduled_date', '>=', now()->toDateString())
            ->count() > 0;

        if ($hasActiveSchedules) {
            return redirect()
                ->route('admin.clients.index')
                ->with('warning', 'Cannot delete client with active schedules. Consider deactivating instead.');
        }

        // Soft delete the client
        $client->delete();

        return redirect()
            ->route('admin.clients.index')
            ->with('success', 'Client deleted successfully.');
    }

    /**
     * Restore a soft deleted client.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        // Find the soft deleted client
        $client = Client::withTrashed()->findOrFail($id);

        // Check if client is actually deleted
        if (!$client->trashed()) {
            return redirect()
                ->route('admin.clients.index')
                ->with('info', 'Client is not deleted.');
        }

        // Restore the client
        $client->restore();

        return redirect()
            ->route('admin.clients.show', $client)
            ->with('success', 'Client restored successfully.');
    }

    /**
     * Permanently delete a soft deleted client.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete($id)
    {
        // Find the soft deleted client
        $client = Client::withTrashed()->findOrFail($id);

        // Check if client has any schedules
        if ($client->schedules()->count() > 0) {
            return redirect()
                ->route('admin.clients.index')
                ->with('error', 'Cannot permanently delete client with existing schedules.');
        }

        // Permanently delete the client
        $client->forceDelete();

        return redirect()
            ->route('admin.clients.index')
            ->with('success', 'Client permanently deleted.');
    }

    /**
     * Display a listing of only soft deleted clients.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function trashed(Request $request)
    {
        // Get only soft deleted clients
        $query = Client::onlyTrashed();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('contact_person', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Order by latest deleted first
        $clients = $query->latest('deleted_at')->paginate(15)->withQueryString();

        return view('admin.clients.trashed', compact('clients'));
    }

    /**
     * Bulk action handler for clients.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|string|in:activate,deactivate,delete,restore,force_delete',
            'client_ids' => 'required|array|min:1',
            'client_ids.*' => 'exists:clients,id',
        ]);

        $clientIds = $request->client_ids;
        $count = 0;

        switch ($request->action) {
            case 'activate':
                $count = Client::whereIn('id', $clientIds)->update(['status' => 'active']);
                $message = "{$count} client(s) activated successfully.";
                break;

            case 'deactivate':
                $count = Client::whereIn('id', $clientIds)->update(['status' => 'inactive']);
                $message = "{$count} client(s) deactivated successfully.";
                break;

            case 'delete':
                // Only delete clients without active schedules
                $clients = Client::whereIn('id', $clientIds)->get();

                foreach ($clients as $client) {
                    $hasActiveSchedules = $client->schedules()
                        ->where('scheduled_date', '>=', now()->toDateString())
                        ->count() > 0;

                    if (!$hasActiveSchedules) {
                        $client->delete();
                        $count++;
                    }
                }

                $message = "{$count} client(s) deleted successfully.";

                if ($count < count($clientIds)) {
                    $message .= ' Some clients could not be deleted due to active schedules.';
                }
                break;

            case 'restore':
                $count = Client::withTrashed()
                    ->whereIn('id', $clientIds)
                    ->restore();
                $message = "{$count} client(s) restored successfully.";
                break;

            case 'force_delete':
                // Only permanently delete clients without any schedules
                $clients = Client::withTrashed()
                    ->whereIn('id', $clientIds)
                    ->get();

                foreach ($clients as $client) {
                    if ($client->schedules()->count() === 0) {
                        $client->forceDelete();
                        $count++;
                    }
                }

                $message = "{$count} client(s) permanently deleted.";

                if ($count < count($clientIds)) {
                    $message .= ' Some clients could not be deleted due to existing schedules.';
                }
                break;
        }

        return redirect()
            ->back()
            ->with('success', $message);
    }
}
