@extends('layouts.app')

@section('title', 'Weekly Schedule Overview')

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
        <span class="text-gray-500 font-medium">Weekly Overview</span>
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
        @page {
            size: landscape;
            margin: 0.5cm;
        }
        table {
            font-size: 9px;
        }
        .schedule-item {
            padding: 2px !important;
            margin-bottom: 2px !important;
        }
    }

    .schedule-cell {
        min-height: 100px;
        vertical-align: top;
    }

    .schedule-item {
        margin-bottom: 6px;
        padding: 8px;
        border-radius: 6px;
        font-size: 0.75rem;
        transition: transform 0.2s;
    }

    .schedule-item:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
</style>

<!-- Header Section -->
<div class="mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Weekly Schedule Overview</h1>
                <p class="mt-2 text-sm text-gray-600">
                    Week of {{ $weekStart->format('M d') }} - {{ $weekEnd->format('M d, Y') }}
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.schedules.index') }}"
                   class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors flex items-center gap-2 no-print">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back
                </a>
                <button onclick="window.print()"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors flex items-center gap-2 no-print">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Filters Section -->
<div class="no-print mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Week Navigation -->
            <div class="flex items-center gap-3">
                <span class="text-sm font-medium text-gray-700">Navigate:</span>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.schedules.weekly', ['week' => $weekOffset - 1, 'user_id' => $userId]) }}"
                       class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    @if($weekOffset != 0)
                    <a href="{{ route('admin.schedules.weekly', ['user_id' => $userId]) }}"
                       class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition-colors">
                        Current Week
                    </a>
                    @else
                    <span class="px-3 py-2 bg-green-100 text-green-800 text-sm rounded-lg font-medium">
                        Current Week
                    </span>
                    @endif
                    <a href="{{ route('admin.schedules.weekly', ['week' => $weekOffset + 1, 'user_id' => $userId]) }}"
                       class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Staff Filter -->
            <form method="GET" action="{{ route('admin.schedules.weekly') }}">
                <input type="hidden" name="week" value="{{ $weekOffset }}">
                <div class="flex items-end gap-2">
                    <div class="flex-1">
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">Filter by Staff</label>
                        <select name="user_id" id="user_id"
                                class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            <option value="">All Instructors</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                        Apply
                    </button>
                    @if($userId)
                    <a href="{{ route('admin.schedules.weekly', ['week' => $weekOffset]) }}"
                       class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors">
                        Clear
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Weekly Schedule Grid -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full border-collapse">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 px-4 py-3 text-left text-sm font-bold text-gray-700 uppercase" style="width: 12%;">
                        Session
                    </th>
                    <th class="border border-gray-300 px-4 py-3 text-center text-sm font-bold text-gray-700 uppercase" style="width: 17.6%;">
                        <div class="flex flex-col items-center">
                            <span class="text-blue-700">Monday</span>
                            <span class="text-xs font-normal text-gray-600 mt-1">{{ $weekSchedule['monday']['date']->format('M d') }}</span>
                        </div>
                    </th>
                    <th class="border border-gray-300 px-4 py-3 text-center text-sm font-bold text-gray-700 uppercase" style="width: 17.6%;">
                        <div class="flex flex-col items-center">
                            <span class="text-purple-700">Tuesday</span>
                            <span class="text-xs font-normal text-gray-600 mt-1">{{ $weekSchedule['tuesday']['date']->format('M d') }}</span>
                        </div>
                    </th>
                    <th class="border border-gray-300 px-4 py-3 text-center text-sm font-bold text-gray-700 uppercase" style="width: 17.6%;">
                        <div class="flex flex-col items-center">
                            <span class="text-green-700">Wednesday</span>
                            <span class="text-xs font-normal text-gray-600 mt-1">{{ $weekSchedule['wednesday']['date']->format('M d') }}</span>
                        </div>
                    </th>
                    <th class="border border-gray-300 px-4 py-3 text-center text-sm font-bold text-gray-700 uppercase" style="width: 17.6%;">
                        <div class="flex flex-col items-center">
                            <span class="text-orange-700">Thursday</span>
                            <span class="text-xs font-normal text-gray-600 mt-1">{{ $weekSchedule['thursday']['date']->format('M d') }}</span>
                        </div>
                    </th>
                    <th class="border border-gray-300 px-4 py-3 text-center text-sm font-bold text-gray-700 uppercase" style="width: 17.6%;">
                        <div class="flex flex-col items-center">
                            <span class="text-red-700">Friday</span>
                            <span class="text-xs font-normal text-gray-600 mt-1">{{ $weekSchedule['friday']['date']->format('M d') }}</span>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                <!-- Morning Session Row -->
                <tr>
                    <td class="border border-gray-300 px-4 py-3 bg-green-50">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <div>
                                <div class="font-semibold text-sm text-gray-900">Morning</div>
                                <div class="text-xs text-gray-600">8:30 AM - 10:00 AM</div>
                            </div>
                        </div>
                    </td>
                    @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday'] as $day)
                    <td class="border border-gray-300 px-3 py-3 schedule-cell bg-white">
                        @if($weekSchedule[$day]['sessions']['morning']->count() > 0)
                            @foreach($weekSchedule[$day]['sessions']['morning'] as $schedule)
                                <div class="schedule-item bg-green-100 border-l-4 border-green-500">
                                    <div class="font-semibold text-gray-900 mb-1">{{ $schedule->user->name ?? 'N/A' }}</div>
                                    <div class="text-gray-700 font-medium">{{ $schedule->client->name ?? 'N/A' }}</div>
                                    @if($schedule->client->address)
                                    <div class="text-gray-600 text-xs mt-1 flex items-center gap-1">
                                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ Str::limit($schedule->client->address, 35) }}
                                    </div>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4 text-gray-400 text-sm italic">No schedules</div>
                        @endif
                    </td>
                    @endforeach
                </tr>

                <!-- Mid-Morning Session Row -->
                <tr>
                    <td class="border border-gray-300 px-4 py-3 bg-blue-50">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <div>
                                <div class="font-semibold text-sm text-gray-900">Mid-Morning</div>
                                <div class="text-xs text-gray-600">10:30 AM - 12:00 PM</div>
                            </div>
                        </div>
                    </td>
                    @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday'] as $day)
                    <td class="border border-gray-300 px-3 py-3 schedule-cell bg-white">
                        @if($weekSchedule[$day]['sessions']['mid-morning']->count() > 0)
                            @foreach($weekSchedule[$day]['sessions']['mid-morning'] as $schedule)
                                <div class="schedule-item bg-blue-100 border-l-4 border-blue-500">
                                    <div class="font-semibold text-gray-900 mb-1">{{ $schedule->user->name ?? 'N/A' }}</div>
                                    <div class="text-gray-700 font-medium">{{ $schedule->client->name ?? 'N/A' }}</div>
                                    @if($schedule->client->address)
                                    <div class="text-gray-600 text-xs mt-1 flex items-center gap-1">
                                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ Str::limit($schedule->client->address, 35) }}
                                    </div>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4 text-gray-400 text-sm italic">No schedules</div>
                        @endif
                    </td>
                    @endforeach
                </tr>

                <!-- Afternoon Session Row -->
                <tr>
                    <td class="border border-gray-300 px-4 py-3 bg-purple-50">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-purple-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                            </svg>
                            <div>
                                <div class="font-semibold text-sm text-gray-900">Afternoon</div>
                                <div class="text-xs text-gray-600">12:30 PM - 2:00 PM</div>
                            </div>
                        </div>
                    </td>
                    @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday'] as $day)
                    <td class="border border-gray-300 px-3 py-3 schedule-cell bg-white">
                        @if($weekSchedule[$day]['sessions']['afternoon']->count() > 0)
                            @foreach($weekSchedule[$day]['sessions']['afternoon'] as $schedule)
                                <div class="schedule-item bg-purple-100 border-l-4 border-purple-500">
                                    <div class="font-semibold text-gray-900 mb-1">{{ $schedule->user->name ?? 'N/A' }}</div>
                                    <div class="text-gray-700 font-medium">{{ $schedule->client->name ?? 'N/A' }}</div>
                                    @if($schedule->client->address)
                                    <div class="text-gray-600 text-xs mt-1 flex items-center gap-1">
                                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ Str::limit($schedule->client->address, 35) }}
                                    </div>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4 text-gray-400 text-sm italic">No schedules</div>
                        @endif
                    </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Legend -->
<div class="mt-6 bg-white rounded-lg shadow-md p-6 no-print">
    <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Session Legend
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg border border-green-200">
            <div class="w-1 h-12 bg-green-500 rounded"></div>
            <div>
                <div class="font-semibold text-sm text-gray-900">Morning Session</div>
                <div class="text-xs text-gray-600">8:30 AM - 10:00 AM</div>
            </div>
        </div>
        <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
            <div class="w-1 h-12 bg-blue-500 rounded"></div>
            <div>
                <div class="font-semibold text-sm text-gray-900">Mid-Morning Session</div>
                <div class="text-xs text-gray-600">10:30 AM - 12:00 PM</div>
            </div>
        </div>
        <div class="flex items-center gap-3 p-3 bg-purple-50 rounded-lg border border-purple-200">
            <div class="w-1 h-12 bg-purple-500 rounded"></div>
            <div>
                <div class="font-semibold text-sm text-gray-900">Afternoon Session</div>
                <div class="text-xs text-gray-600">12:30 PM - 2:00 PM</div>
            </div>
        </div>
    </div>
</div>
@endsection
