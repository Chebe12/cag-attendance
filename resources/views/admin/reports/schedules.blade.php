@extends('layouts.app')

@section('title', 'Schedules Report')

@section('breadcrumbs')
    <li class="flex items-center">
        <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-500">
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
            </svg>
        </a>
        <svg class="h-5 w-5 text-gray-300 mx-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
        <a href="{{ route('admin.reports.index') }}" class="text-gray-400 hover:text-gray-500">Reports</a>
        <svg class="h-5 w-5 text-gray-300 mx-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
        <span class="text-gray-500 font-medium">Schedules</span>
    </li>
@endsection

@section('content')
<div x-data="{
    showFilters: false,
    showExport: false,
    exportReport(format) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('admin.reports.export') }}';

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        const typeInput = document.createElement('input');
        typeInput.type = 'hidden';
        typeInput.name = 'type';
        typeInput.value = 'schedules';
        form.appendChild(typeInput);

        const formatInput = document.createElement('input');
        formatInput.type = 'hidden';
        formatInput.name = 'format';
        formatInput.value = format;
        form.appendChild(formatInput);

        @if(request('from_date'))
        const fromDate = document.createElement('input');
        fromDate.type = 'hidden';
        fromDate.name = 'from_date';
        fromDate.value = '{{ request('from_date') }}';
        form.appendChild(fromDate);
        @endif

        @if(request('to_date'))
        const toDate = document.createElement('input');
        toDate.type = 'hidden';
        toDate.name = 'to_date';
        toDate.value = '{{ request('to_date') }}';
        form.appendChild(toDate);
        @endif

        @if(request('user_id'))
        const userId = document.createElement('input');
        userId.type = 'hidden';
        userId.name = 'user_id';
        userId.value = '{{ request('user_id') }}';
        form.appendChild(userId);
        @endif

        @if(request('client_id'))
        const clientId = document.createElement('input');
        clientId.type = 'hidden';
        clientId.name = 'client_id';
        clientId.value = '{{ request('client_id') }}';
        form.appendChild(clientId);
        @endif

        @if(request('status'))
        const status = document.createElement('input');
        status.type = 'hidden';
        status.name = 'status';
        status.value = '{{ request('status') }}';
        form.appendChild(status);
        @endif

        document.body.appendChild(form);
        form.submit();
    }
}">
    <!-- Page header -->
    <div class="mb-8">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Schedules Report</h1>
                <p class="mt-2 text-sm text-gray-700">View and export schedule records</p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                <button @click="showFilters = !showFilters"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filters
                </button>

                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold rounded-lg shadow-md transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export Report
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open"
                         @click.away="open = false"
                         x-transition
                         class="absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10"
                         style="display: none;">
                        <div class="py-1">
                            <button @click="exportReport('pdf'); open = false"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    Export as PDF
                                </div>
                            </button>
                            <button @click="exportReport('excel'); open = false"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Export as Excel
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Schedules</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $summary['total_schedules'] }}</p>
                </div>
                <div class="p-3 rounded-full bg-blue-100">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Scheduled</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $summary['scheduled'] }}</p>
                </div>
                <div class="p-3 rounded-full bg-yellow-100">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Completed</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $summary['completed'] }}</p>
                </div>
                <div class="p-3 rounded-full bg-green-100">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">With Attendance</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $summary['with_attendance'] }}</p>
                </div>
                <div class="p-3 rounded-full bg-purple-100">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Panel -->
    <div x-show="showFilters"
         x-transition
         class="bg-white rounded-lg shadow-md p-6 mb-6"
         style="display: none;">
        <form method="GET" action="{{ route('admin.reports.schedules') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label for="from_date" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                    <input type="date"
                           id="from_date"
                           name="from_date"
                           value="{{ request('from_date', now()->startOfMonth()->toDateString()) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>

                <div>
                    <label for="to_date" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                    <input type="date"
                           id="to_date"
                           name="to_date"
                           value="{{ request('to_date', now()->endOfMonth()->toDateString()) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>

                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Instructor</label>
                    <select id="user_id"
                            name="user_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">All Instructors</option>
                        @foreach($users as $user)
                            <option value="{{ $user['id'] }}" {{ request('user_id') == $user['id'] ? 'selected' : '' }}>
                                {{ $user['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="client_id" class="block text-sm font-medium text-gray-700 mb-1">Client</label>
                    <select id="client_id"
                            name="client_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">All Clients</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                {{ $client->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="status"
                            name="status"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">All Statuses</option>
                        <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="rescheduled" {{ request('status') == 'rescheduled' ? 'selected' : '' }}>Rescheduled</option>
                    </select>
                </div>

                <div>
                    <label for="shift_id" class="block text-sm font-medium text-gray-700 mb-1">Shift</label>
                    <select id="shift_id"
                            name="shift_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">All Shifts</option>
                        @foreach(\App\Models\Shift::orderBy('start_time')->get() as $shift)
                            <option value="{{ $shift->id }}" {{ request('shift_id') == $shift->id ? 'selected' : '' }}>
                                {{ $shift->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.reports.schedules') }}"
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Clear Filters
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Schedules Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instructor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendance</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($schedules as $schedule)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                            <span class="text-green-600 font-semibold text-sm">
                                                {{ substr($schedule->user->firstname, 0, 1) }}{{ substr($schedule->user->lastname, 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $schedule->user->full_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $schedule->user->employee_no }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $schedule->client->name }}</div>
                                <div class="text-sm text-gray-500">{{ $schedule->client->location ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($schedule->scheduled_date)->format('M d, Y') }}</div>
                                <div class="text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }} -
                                    {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ $schedule->shift->name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($schedule->status == 'scheduled')
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Scheduled
                                    </span>
                                @elseif($schedule->status == 'completed')
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Completed
                                    </span>
                                @elseif($schedule->status == 'cancelled')
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Cancelled
                                    </span>
                                @else
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ ucfirst($schedule->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($schedule->attendance)
                                    <div class="text-sm text-gray-900">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ ucfirst($schedule->attendance->status) }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $schedule->attendance->check_in ? \Carbon\Carbon::parse($schedule->attendance->check_in)->format('h:i A') : '-' }}
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">No attendance</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="mt-4 text-lg">No schedules found</p>
                                <p class="mt-2 text-sm">Try adjusting your filters</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($schedules->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $schedules->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
