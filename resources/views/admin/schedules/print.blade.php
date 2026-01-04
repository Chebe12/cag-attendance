@extends('layouts.app')

@section('title', 'Printable Schedules')

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
        <a href="{{ route('admin.schedules.index') }}" class="text-gray-400 hover:text-gray-500">Schedules</a>
        <svg class="h-5 w-5 text-gray-300 mx-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
        <span class="text-gray-500 font-medium">Print View</span>
    </li>
@endsection

@section('content')
<style>
    @media print {
        .no-print {
            display: none !important;
        }
        body {
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }
        .page-break {
            page-break-after: always;
        }
        @page {
            size: A4;
            margin: 1cm;
        }
        .print-content {
            padding: 0 !important;
        }
    }
</style>

<!-- Filter Section -->
<div class="no-print mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Printable Schedules</h1>
                <p class="mt-2 text-sm text-gray-600">Generate and print schedule reports</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.schedules.index') }}"
                   class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back
                </a>
                <button onclick="window.print()"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print
                </button>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('admin.schedules.print') }}">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- Staff Filter -->
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">Staff Member</label>
                    <select name="user_id" id="user_id"
                            class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        <option value="">All Staff</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Client Filter -->
                <div>
                    <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">Client</label>
                    <select name="client_id" id="client_id"
                            class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        <option value="">All Clients</option>
                        @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date From -->
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                    <input type="date" name="date_from" id="date_from"
                           value="{{ request('date_from', now()->startOfWeek()->format('Y-m-d')) }}"
                           class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                </div>

                <!-- Date To -->
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                    <input type="date" name="date_to" id="date_to"
                           value="{{ request('date_to', now()->endOfWeek()->format('Y-m-d')) }}"
                           class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                </div>

                <!-- Submit -->
                <div class="flex items-end">
                    <button type="submit"
                            class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Apply Filters
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Printable Content -->
<div class="print-content">
    <!-- Print Header -->
    <div class="text-center mb-6 print-only" style="display: none;">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Staff Schedule Report</h1>
        <p class="text-gray-600 text-lg">
            @if(request('date_from') && request('date_to'))
                {{ \Carbon\Carbon::parse(request('date_from'))->format('M d, Y') }} - {{ \Carbon\Carbon::parse(request('date_to'))->format('M d, Y') }}
            @else
                {{ now()->startOfWeek()->format('M d, Y') }} - {{ now()->endOfWeek()->format('M d, Y') }}
            @endif
        </p>
        @if(request('user_id'))
            <p class="text-sm text-gray-500 mt-1">
                Staff: {{ $users->find(request('user_id'))->name ?? 'N/A' }}
            </p>
        @endif
        @if(request('client_id'))
            <p class="text-sm text-gray-500">
                Client: {{ $clients->find(request('client_id'))->name ?? 'N/A' }}
            </p>
        @endif
        <p class="text-xs text-gray-400 mt-2">Generated: {{ now()->format('M d, Y h:i A') }}</p>
    </div>

    @if($groupedSchedules->isEmpty())
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <h3 class="text-xl font-medium text-gray-900 mb-2">No schedules found</h3>
            <p class="text-gray-500">Try adjusting your filters to see schedules.</p>
        </div>
    @else
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 no-print">
            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-gray-500">
                <div class="text-sm text-gray-600">Total Schedules</div>
                <div class="text-3xl font-bold text-gray-900">{{ $schedules->count() }}</div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-blue-500">
                <div class="text-sm text-gray-600">Scheduled</div>
                <div class="text-3xl font-bold text-blue-600">{{ $schedules->where('status', 'scheduled')->count() }}</div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-green-500">
                <div class="text-sm text-gray-600">Completed</div>
                <div class="text-3xl font-bold text-green-600">{{ $schedules->where('status', 'completed')->count() }}</div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-red-500">
                <div class="text-sm text-gray-600">Cancelled</div>
                <div class="text-3xl font-bold text-red-600">{{ $schedules->where('status', 'cancelled')->count() }}</div>
            </div>
        </div>

        <!-- Schedules by Day -->
        @foreach($groupedSchedules as $date => $daySchedules)
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6 {{ !$loop->last ? 'page-break' : '' }}">
                <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ \Carbon\Carbon::parse($date)->format('l, F d, Y') }}
                    </h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff Member</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Session</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($daySchedules as $schedule)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $schedule->user->name ?? 'N/A' }}</div>
                                            <div class="text-xs text-gray-500">{{ $schedule->user->employee_no ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $schedule->client->name ?? 'N/A' }}</div>
                                    @if($schedule->client->address)
                                    <div class="text-xs text-gray-500 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ Str::limit($schedule->client->address, 40) }}
                                    </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($schedule->session_time === 'morning') bg-green-100 text-green-800
                                        @else bg-purple-100 text-purple-800
                                        @endif">
                                        {{ ucfirst(str_replace('-', ' ', $schedule->session_time ?? 'N/A')) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $schedule->session_time_range ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($schedule->status === 'scheduled') bg-blue-100 text-blue-800
                                        @elseif($schedule->status === 'completed') bg-green-100 text-green-800
                                        @elseif($schedule->status === 'canceled' || $schedule->status === 'cancelled') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($schedule->status ?? 'scheduled') }}
                                    </span>
                                </td>
                            </tr>
                            @if($schedule->notes)
                            <tr class="bg-yellow-50">
                                <td colspan="5" class="px-6 py-3">
                                    <div class="flex items-start gap-2">
                                        <svg class="w-4 h-4 text-yellow-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                        </svg>
                                        <div>
                                            <span class="text-xs font-semibold text-yellow-800">Notes: </span>
                                            <span class="text-xs text-yellow-700">{{ $schedule->notes }}</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                    <p class="text-sm text-gray-600">
                        <span class="font-semibold">Total for this day:</span> {{ $daySchedules->count() }} schedule(s)
                    </p>
                </div>
            </div>
        @endforeach
    @endif
</div>

<style>
    @media print {
        .print-only {
            display: block !important;
        }
    }
</style>
@endsection
