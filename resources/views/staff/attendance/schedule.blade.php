@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">My Schedule</h1>
        <p class="mt-2 text-sm text-gray-600">View your weekly recurring schedule</p>
        @if($activeCategory ?? null)
            <p class="mt-1 text-xs text-gray-500">
                Current Term: <span class="font-semibold">{{ $activeCategory->name }}</span>
                ({{ $activeCategory->start_date->format('M d, Y') }} - {{ $activeCategory->end_date->format('M d, Y') }})
            </p>
        @endif
    </div>

    @if($todaySchedules && $todaySchedules->count() > 0)
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-blue-800">Today's Schedule ({{ $todaySchedules->count() }} {{ $todaySchedules->count() === 1 ? 'visit' : 'visits' }})</h3>
                    <div class="mt-2 space-y-3">
                        @foreach($todaySchedules as $schedule)
                            <div class="text-sm text-blue-700 border-l-2 border-blue-300 pl-3">
                                <p><strong>Client:</strong> {{ $schedule->client->name }}</p>
                                <p><strong>Time:</strong> {{ date('g:i A', strtotime($schedule->start_time)) }} - {{ date('g:i A', strtotime($schedule->end_time)) }}</p>
                                @if($schedule->client->address)
                                    <p><strong>Location:</strong> {{ $schedule->client->address }}, {{ $schedule->client->city }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Day of Week</label>
                <select name="day_of_week" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    <option value="">All Days</option>
                    <option value="monday" {{ request('day_of_week') == 'monday' ? 'selected' : '' }}>Monday</option>
                    <option value="tuesday" {{ request('day_of_week') == 'tuesday' ? 'selected' : '' }}>Tuesday</option>
                    <option value="wednesday" {{ request('day_of_week') == 'wednesday' ? 'selected' : '' }}>Wednesday</option>
                    <option value="thursday" {{ request('day_of_week') == 'thursday' ? 'selected' : '' }}>Thursday</option>
                    <option value="friday" {{ request('day_of_week') == 'friday' ? 'selected' : '' }}>Friday</option>
                    <option value="saturday" {{ request('day_of_week') == 'saturday' ? 'selected' : '' }}>Saturday</option>
                    <option value="sunday" {{ request('day_of_week') == 'sunday' ? 'selected' : '' }}>Sunday</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Client</label>
                <select name="client_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    <option value="">All Clients</option>
                    @foreach($userClients as $client)
                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    <option value="">All Statuses</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="missed" {{ request('status') == 'missed' ? 'selected' : '' }}>Missed</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">
                    Apply Filters
                </button>
            </div>
            <div class="md:col-span-4">
                <a href="{{ route('staff.schedules.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg inline-block">
                    Clear Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Schedule List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($schedules->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Day</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Session</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($schedules as $schedule)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                    {{ ucfirst($schedule->day_of_week) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $sessionColors = [
                                            'morning' => 'bg-blue-100 text-blue-800',
                                            'afternoon' => 'bg-orange-100 text-orange-800',
                                        ];
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $sessionColors[$schedule->session_time] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst(str_replace('-', ' ', $schedule->session_time)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium">
                                    {{ $schedule->client->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $times = \App\Models\Schedule::getSessionTimes($schedule->session_time);
                                    @endphp
                                    {{ date('g:i A', strtotime($times['start'])) }} - {{ date('g:i A', strtotime($times['end'])) }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $schedule->client->address ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $schedule->client->city }}, {{ $schedule->client->state }}</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $schedules->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No schedules found</h3>
                <p class="mt-1 text-sm text-gray-500">You don't have any upcoming schedules.</p>
            </div>
        @endif
    </div>
</div>
@endsection
