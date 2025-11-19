<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Schedule Overview</title>
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
            @page {
                size: landscape;
                margin: 0.5cm;
            }
            table {
                font-size: 10px;
            }
        }

        .schedule-cell {
            min-height: 80px;
            vertical-align: top;
        }

        .schedule-item {
            margin-bottom: 4px;
            padding: 4px;
            border-radius: 4px;
            font-size: 0.75rem;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Filter Section (No Print) -->
    <div class="no-print max-w-full mx-auto p-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Weekly Schedule Overview</h1>
                    <p class="text-sm text-gray-600">
                        Week of {{ $weekStart->format('M d') }} - {{ $weekEnd->format('M d, Y') }}
                    </p>
                </div>
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

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Week Navigation -->
                <div class="flex items-center space-x-2">
                    <a href="{{ route('admin.schedules.weekly', ['week' => $weekOffset - 1, 'user_id' => $userId]) }}"
                       class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <span class="text-sm font-medium">Week</span>
                    <a href="{{ route('admin.schedules.weekly', ['week' => $weekOffset + 1, 'user_id' => $userId]) }}"
                       class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                    @if($weekOffset != 0)
                    <a href="{{ route('admin.schedules.weekly', ['user_id' => $userId]) }}"
                       class="px-3 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 text-sm rounded-lg">
                        Current Week
                    </a>
                    @endif
                </div>

                <!-- Staff Filter -->
                <form method="GET" action="{{ route('admin.schedules.weekly') }}" class="md:col-span-2">
                    <input type="hidden" name="week" value="{{ $weekOffset }}">
                    <div class="flex items-end space-x-2">
                        <div class="flex-1">
                            <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Filter by Staff</label>
                            <select name="user_id" id="user_id" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                                <option value="">All Instructors</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>
                                    {{ $user->full_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                            Apply
                        </button>
                        @if($userId)
                        <a href="{{ route('admin.schedules.weekly', ['week' => $weekOffset]) }}"
                           class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg">
                            Clear
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Weekly Schedule Table -->
    <div class="max-w-full mx-auto p-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-x-auto">
            <table class="min-w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 px-2 py-2 text-left text-xs font-bold text-gray-700 uppercase" style="width: 15%;">Day/Session</th>
                        <th class="border border-gray-300 px-2 py-2 text-center text-xs font-bold text-blue-700 uppercase bg-blue-50" style="width: 17%;">
                            <div>Monday</div>
                            <div class="text-xs font-normal text-gray-600">{{ $weekSchedule['monday']['date']->format('M d') }}</div>
                        </th>
                        <th class="border border-gray-300 px-2 py-2 text-center text-xs font-bold text-purple-700 uppercase bg-purple-50" style="width: 17%;">
                            <div>Tuesday</div>
                            <div class="text-xs font-normal text-gray-600">{{ $weekSchedule['tuesday']['date']->format('M d') }}</div>
                        </th>
                        <th class="border border-gray-300 px-2 py-2 text-center text-xs font-bold text-green-700 uppercase bg-green-50" style="width: 17%;">
                            <div>Wednesday</div>
                            <div class="text-xs font-normal text-gray-600">{{ $weekSchedule['wednesday']['date']->format('M d') }}</div>
                        </th>
                        <th class="border border-gray-300 px-2 py-2 text-center text-xs font-bold text-orange-700 uppercase bg-orange-50" style="width: 17%;">
                            <div>Thursday</div>
                            <div class="text-xs font-normal text-gray-600">{{ $weekSchedule['thursday']['date']->format('M d') }}</div>
                        </th>
                        <th class="border border-gray-300 px-2 py-2 text-center text-xs font-bold text-red-700 uppercase bg-red-50" style="width: 17%;">
                            <div>Friday</div>
                            <div class="text-xs font-normal text-gray-600">{{ $weekSchedule['friday']['date']->format('M d') }}</div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Morning Session Row -->
                    <tr>
                        <td class="border border-gray-300 px-2 py-2 bg-blue-50 font-semibold text-sm">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                Morning<br>
                                <span class="text-xs text-gray-600">8:30 AM - 10:00 AM</span>
                            </div>
                        </td>
                        @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday'] as $day)
                        <td class="border border-gray-300 px-2 py-2 schedule-cell">
                            @foreach($weekSchedule[$day]['sessions']['morning'] as $schedule)
                                <div class="schedule-item bg-blue-100 border-l-4 border-blue-500">
                                    <div class="font-semibold text-gray-800">{{ $schedule->user->full_name ?? 'N/A' }}</div>
                                    <div class="text-gray-700">{{ $schedule->client->name ?? 'N/A' }}</div>
                                    @if($schedule->client->address)
                                    <div class="text-gray-600 text-xs">📍 {{ Str::limit($schedule->client->address, 30) }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </td>
                        @endforeach
                    </tr>

                    <!-- Mid-Morning Session Row -->
                    <tr>
                        <td class="border border-gray-300 px-2 py-2 bg-purple-50 font-semibold text-sm">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                Mid-Morning<br>
                                <span class="text-xs text-gray-600">10:30 AM - 12:00 PM</span>
                            </div>
                        </td>
                        @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday'] as $day)
                        <td class="border border-gray-300 px-2 py-2 schedule-cell">
                            @foreach($weekSchedule[$day]['sessions']['mid-morning'] as $schedule)
                                <div class="schedule-item bg-purple-100 border-l-4 border-purple-500">
                                    <div class="font-semibold text-gray-800">{{ $schedule->user->full_name ?? 'N/A' }}</div>
                                    <div class="text-gray-700">{{ $schedule->client->name ?? 'N/A' }}</div>
                                    @if($schedule->client->address)
                                    <div class="text-gray-600 text-xs">📍 {{ Str::limit($schedule->client->address, 30) }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </td>
                        @endforeach
                    </tr>

                    <!-- Afternoon Session Row -->
                    <tr>
                        <td class="border border-gray-300 px-2 py-2 bg-orange-50 font-semibold text-sm">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                </svg>
                                Afternoon<br>
                                <span class="text-xs text-gray-600">12:30 PM - 2:00 PM</span>
                            </div>
                        </td>
                        @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday'] as $day)
                        <td class="border border-gray-300 px-2 py-2 schedule-cell">
                            @foreach($weekSchedule[$day]['sessions']['afternoon'] as $schedule)
                                <div class="schedule-item bg-orange-100 border-l-4 border-orange-500">
                                    <div class="font-semibold text-gray-800">{{ $schedule->user->full_name ?? 'N/A' }}</div>
                                    <div class="text-gray-700">{{ $schedule->client->name ?? 'N/A' }}</div>
                                    @if($schedule->client->address)
                                    <div class="text-gray-600 text-xs">📍 {{ Str::limit($schedule->client->address, 30) }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Legend -->
        <div class="mt-4 bg-white rounded-lg shadow-sm border border-gray-200 p-4 no-print">
            <h3 class="font-semibold text-gray-900 mb-2">Legend:</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-2 text-sm">
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-blue-100 border-l-4 border-blue-500 mr-2"></div>
                    <span>Morning Session (8:30 AM - 10:00 AM)</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-purple-100 border-l-4 border-purple-500 mr-2"></div>
                    <span>Mid-Morning Session (10:30 AM - 12:00 PM)</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-orange-100 border-l-4 border-orange-500 mr-2"></div>
                    <span>Afternoon Session (12:30 PM - 2:00 PM)</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
