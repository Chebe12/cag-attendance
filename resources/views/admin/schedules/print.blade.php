<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Schedules</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
        }

        .schedule-table {
            border-collapse: collapse;
            width: 100%;
        }

        .schedule-table th,
        .schedule-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .schedule-table th {
            background-color: #f3f4f6;
            font-weight: 600;
        }
    </style>
</head>
<body class="bg-gray-50 p-4">
    <!-- Filter Section (No Print) -->
    <div class="no-print max-w-7xl mx-auto mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-bold text-gray-900">Printable Schedules</h1>
                <div class="flex items-center space-x-3">
                    <button onclick="window.print()"
                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Print
                    </button>
                    <a href="{{ route('admin.schedules.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back
                    </a>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.schedules.print') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- Staff Filter -->
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">Staff</label>
                    <select name="user_id" id="user_id" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        <option value="">All Staff</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->full_name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Client Filter -->
                <div>
                    <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">Client</label>
                    <select name="client_id" id="client_id" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
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
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from', now()->startOfWeek()->format('Y-m-d')) }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                </div>

                <!-- Date To -->
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to', now()->endOfWeek()->format('Y-m-d')) }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                </div>

                <!-- Submit -->
                <div class="flex items-end">
                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Printable Content -->
    <div class="max-w-7xl mx-auto bg-white p-8">
        <!-- Header for Print -->
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Staff Schedule</h1>
            <p class="text-gray-600">
                @if(request('date_from') && request('date_to'))
                    {{ \Carbon\Carbon::parse(request('date_from'))->format('M d, Y') }} - {{ \Carbon\Carbon::parse(request('date_to'))->format('M d, Y') }}
                @else
                    {{ now()->startOfWeek()->format('M d, Y') }} - {{ now()->endOfWeek()->format('M d, Y') }}
                @endif
            </p>
            @if(request('user_id'))
                <p class="text-sm text-gray-500">
                    Staff: {{ $users->find(request('user_id'))->full_name ?? 'N/A' }}
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
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900">No schedules found</h3>
                <p class="mt-1 text-sm text-gray-500">Try adjusting your filters to see schedules.</p>
            </div>
        @else
            @foreach($groupedSchedules as $date => $daySchedules)
                <div class="mb-8 {{ !$loop->last ? 'page-break' : '' }}">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 border-b-2 border-gray-300 pb-2">
                        {{ \Carbon\Carbon::parse($date)->format('l, F d, Y') }}
                    </h2>

                    <table class="schedule-table">
                        <thead>
                            <tr>
                                <th class="w-1/5">Staff Member</th>
                                <th class="w-1/5">Client</th>
                                <th class="w-1/6">Shift</th>
                                <th class="w-1/6">Time</th>
                                <th class="w-1/5">Location</th>
                                <th class="w-1/12">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($daySchedules as $schedule)
                            <tr>
                                <td>
                                    <div class="font-medium text-gray-900">{{ $schedule->user->full_name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $schedule->user->employee_no ?? 'N/A' }}</div>
                                </td>
                                <td>
                                    <div class="font-medium text-gray-900">{{ $schedule->client->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $schedule->client->code ?? '' }}</div>
                                </td>
                                <td>
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                        {{ $schedule->shift->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="text-sm">
                                    {{ $schedule->shift->start_time ?? 'N/A' }} - {{ $schedule->shift->end_time ?? 'N/A' }}
                                </td>
                                <td class="text-sm">
                                    {{ $schedule->client->address ?? 'N/A' }}
                                </td>
                                <td>
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                        @if($schedule->status === 'scheduled') bg-blue-100 text-blue-800
                                        @elseif($schedule->status === 'completed') bg-green-100 text-green-800
                                        @elseif($schedule->status === 'cancelled') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($schedule->status ?? 'scheduled') }}
                                    </span>
                                </td>
                            </tr>
                            @if($schedule->notes)
                            <tr>
                                <td colspan="6" class="text-xs text-gray-600 bg-gray-50">
                                    <strong>Notes:</strong> {{ $schedule->notes }}
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Summary for the day -->
                    <div class="mt-2 text-sm text-gray-600">
                        Total: {{ $daySchedules->count() }} schedule(s)
                    </div>
                </div>
            @endforeach

            <!-- Overall Summary -->
            <div class="mt-8 pt-6 border-t-2 border-gray-300">
                <h3 class="text-lg font-bold text-gray-900 mb-3">Summary</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div class="bg-gray-50 p-3 rounded">
                        <div class="text-gray-500 text-xs font-medium">Total Schedules</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $schedules->count() }}</div>
                    </div>
                    <div class="bg-blue-50 p-3 rounded">
                        <div class="text-blue-600 text-xs font-medium">Scheduled</div>
                        <div class="text-2xl font-bold text-blue-900">{{ $schedules->where('status', 'scheduled')->count() }}</div>
                    </div>
                    <div class="bg-green-50 p-3 rounded">
                        <div class="text-green-600 text-xs font-medium">Completed</div>
                        <div class="text-2xl font-bold text-green-900">{{ $schedules->where('status', 'completed')->count() }}</div>
                    </div>
                    <div class="bg-red-50 p-3 rounded">
                        <div class="text-red-600 text-xs font-medium">Cancelled</div>
                        <div class="text-2xl font-bold text-red-900">{{ $schedules->where('status', 'cancelled')->count() }}</div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</body>
</html>
