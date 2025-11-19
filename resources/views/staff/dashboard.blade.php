@extends('layouts.app')

@section('title', 'Staff Dashboard')

@section('breadcrumbs')
<li class="flex items-center">
    <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
    </svg>
    <span class="ml-2 text-sm font-medium text-gray-900">Dashboard</span>
</li>
@endsection

@section('content')
<!-- Page header -->
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }}</h1>
    <p class="mt-2 text-sm text-gray-600">{{ now()->format('l, F j, Y') }}</p>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-8">
    <!-- Check In/Out Card -->
    <div class="rounded-xl bg-gradient-to-br from-green-500 to-green-600 shadow-lg p-8 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 h-32 w-32 rounded-full bg-white opacity-10"></div>
        <div class="absolute bottom-0 left-0 -mb-8 -ml-8 h-40 w-40 rounded-full bg-white opacity-10"></div>

        <div class="relative z-10">
            <div class="flex items-center mb-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white bg-opacity-20">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-2xl font-bold">Quick Attendance</h2>
                    <p class="text-green-100 text-sm">Scan QR code to mark attendance</p>
                </div>
            </div>

            @if($todayAttendance ?? null)
                @if($todayAttendance->check_in && !$todayAttendance->check_out)
                    <!-- Checked In - Show Check Out -->
                    <div class="mt-6">
                        <div class="bg-white bg-opacity-20 rounded-lg p-4 mb-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-green-100">Checked in at</p>
                                    <p class="text-xl font-bold">{{ \Carbon\Carbon::parse($todayAttendance->check_in)->format('g:i A') }}</p>
                                </div>
                                <div class="flex h-3 w-3">
                                    <span class="animate-ping absolute inline-flex h-3 w-3 rounded-full bg-white opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-white"></span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('staff.attendance.mark') }}"
                           class="block w-full text-center bg-white text-green-600 rounded-lg px-6 py-3 text-base font-semibold hover:bg-green-50 transition-colors shadow-lg transform hover:scale-105 transition-transform duration-200">
                            Scan QR to Check Out
                        </a>
                    </div>
                @elseif($todayAttendance->check_out)
                    <!-- Already Checked Out -->
                    <div class="mt-6">
                        <div class="bg-white bg-opacity-20 rounded-lg p-4 mb-4">
                            <div class="flex items-center justify-center">
                                <svg class="h-12 w-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <p class="text-center mt-2 font-semibold">Attendance Completed</p>
                            <p class="text-center text-sm text-green-100">You've checked out for today</p>
                        </div>
                    </div>
                @endif
            @else
                <!-- Not Checked In Yet -->
                <div class="mt-6">
                    <a href="{{ route('staff.attendance.mark') }}"
                       class="block w-full text-center bg-white text-green-600 rounded-lg px-6 py-4 text-lg font-semibold hover:bg-green-50 transition-colors shadow-lg transform hover:scale-105 transition-transform duration-200">
                        <div class="flex items-center justify-center">
                            <svg class="h-6 w-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                            Scan QR to Check In
                        </div>
                    </a>
                    <p class="text-center text-sm text-green-100 mt-3">You haven't checked in today</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Today's Schedule Card -->
    <div class="rounded-xl bg-white shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Today's Schedule</h2>
            <a href="{{ route('staff.schedules.index') }}" class="text-sm font-medium text-green-600 hover:text-green-500">View all</a>
        </div>

        @if($todaySchedule ?? null)
            <div class="space-y-4">
                <div class="flex items-start space-x-4 p-4 bg-gradient-to-r from-orange-50 to-orange-100 rounded-lg border border-orange-200">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-semibold text-gray-900">{{ $todaySchedule->client->name }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ $todaySchedule->client->address }}</p>
                        <div class="mt-3 flex items-center space-x-4">
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="h-4 w-4 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ \Carbon\Carbon::parse($todaySchedule->start_time)->format('g:i A') }}
                            </div>
                            <span class="text-gray-400">-</span>
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="h-4 w-4 mr-1 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ \Carbon\Carbon::parse($todaySchedule->end_time)->format('g:i A') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="mt-2 text-sm text-gray-500">No schedule for today</p>
                <p class="text-xs text-gray-400 mt-1">Enjoy your day off!</p>
            </div>
        @endif
    </div>
</div>

<!-- This Week's Schedule (Recurring) -->
@php
    $activeCategory = \App\Models\ScheduleCategory::where('status', 'active')
        ->where('start_date', '<=', now())
        ->where('end_date', '>=', now())
        ->first();

    $weeklySchedules = [];
    if ($activeCategory) {
        $weeklySchedules = \App\Models\Schedule::where('user_id', auth()->id())
            ->where('category_id', $activeCategory->id)
            ->where('draft_status', 'published')
            ->with(['client', 'category'])
            ->orderByRaw("FIELD(day_of_week, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')")
            ->orderByRaw("FIELD(session_time, 'morning', 'mid-morning', 'afternoon')")
            ->get()
            ->groupBy('day_of_week');
    }

    $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    $today = strtolower(now()->format('l'));
@endphp

@if($activeCategory)
<div class="rounded-xl bg-white shadow-sm border border-gray-100 p-6 mb-8">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">This Week's Schedule</h2>
            <p class="text-xs text-gray-600 mt-1">{{ $activeCategory->name }} - Recurring Weekly</p>
        </div>
        <a href="{{ route('staff.schedules.index') }}" class="text-sm font-medium text-green-600 hover:text-green-500">View all</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-7 gap-2">
        @foreach($daysOfWeek as $day)
            @php
                $isToday = $day === $today;
                $daySchedules = $weeklySchedules[$day] ?? collect();
            @endphp
            <div class="border rounded-lg p-3 {{ $isToday ? 'bg-green-50 border-green-300 ring-2 ring-green-200' : 'bg-gray-50' }}">
                <h3 class="text-xs font-bold {{ $isToday ? 'text-green-800' : 'text-gray-700' }} uppercase mb-2">
                    {{ substr($day, 0, 3) }}
                    @if($isToday)
                        <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-600 text-white">
                            Today
                        </span>
                    @endif
                </h3>
                @if($daySchedules->count() > 0)
                    <div class="space-y-1.5">
                        @foreach($daySchedules as $schedule)
                            @php
                                $sessionColors = [
                                    'morning' => 'bg-green-100 text-green-800 border-green-300',
                                    'mid-morning' => 'bg-blue-100 text-blue-800 border-blue-300',
                                    'afternoon' => 'bg-purple-100 text-purple-800 border-purple-300',
                                ];
                            @endphp
                            <div class="text-xs p-2 rounded border {{ $sessionColors[$schedule->session_time] ?? 'bg-gray-100 text-gray-800 border-gray-300' }}">
                                <div class="font-semibold">{{ $schedule->client->name }}</div>
                                <div class="text-xs opacity-75 mt-0.5">
                                    {{ ucfirst(str_replace('-', ' ', $schedule->session_time)) }}
                                </div>
                                @php
                                    $times = \App\Models\Schedule::getSessionTimes($schedule->session_time);
                                @endphp
                                <div class="text-xs opacity-75">
                                    {{ date('g:i A', strtotime($times['start'])) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-gray-400 italic text-center py-2">No schedule</p>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- Upcoming Schedules and This Week's Attendance -->
<div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-8">
    <!-- Upcoming Schedules -->
    <div class="rounded-xl bg-white shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Upcoming Schedules</h2>
            <a href="{{ route('staff.schedules.index') }}" class="text-sm font-medium text-green-600 hover:text-green-500">View all</a>
        </div>

        <div class="space-y-3">
            @forelse($upcomingSchedules ?? [] as $schedule)
                <div class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center text-white font-bold text-xs">
                            {{ substr($schedule->display_day ?? ucfirst($schedule->day_of_week), 0, 3) }}
                        </div>
                    </div>
                    <div class="ml-4 flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $schedule->client->name }}</p>
                        <p class="text-xs text-gray-500">
                            {{ $schedule->display_day ?? ucfirst($schedule->day_of_week) }} •
                            @php
                                $times = \App\Models\Schedule::getSessionTimes($schedule->session_time);
                            @endphp
                            {{ date('g:i A', strtotime($times['start'])) }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">No upcoming schedules</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- This Week's Attendance -->
    <div class="rounded-xl bg-white shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">This Week's Attendance</h2>
            <a href="{{ route('staff.attendance.index') }}" class="text-sm font-medium text-green-600 hover:text-green-500">View all</a>
        </div>

        <div class="space-y-3">
            @forelse($weekAttendances ?? [] as $attendance)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="text-sm font-medium text-gray-900">
                            {{ \Carbon\Carbon::parse($attendance->check_in)->format('M j') }}
                        </div>
                        <div class="flex items-center space-x-2 text-xs text-gray-500">
                            <span>{{ \Carbon\Carbon::parse($attendance->check_in)->format('g:i A') }}</span>
                            @if($attendance->check_out)
                                <span>-</span>
                                <span>{{ \Carbon\Carbon::parse($attendance->check_out)->format('g:i A') }}</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        @if($attendance->check_out)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Complete
                            </span>
                        @elseif($attendance->check_in)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <div class="mr-1 flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-blue-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                                </div>
                                Active
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">No attendance this week</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
    <div class="rounded-xl bg-white shadow-sm border border-gray-100 p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <div class="ml-5">
                <dl>
                    <dt class="text-sm font-medium text-gray-500">This Month</dt>
                    <dd class="text-2xl font-bold text-gray-900">{{ $stats['month_attendance'] ?? 0 }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-100 p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="ml-5">
                <dl>
                    <dt class="text-sm font-medium text-gray-500">On Time</dt>
                    <dd class="text-2xl font-bold text-gray-900">{{ $stats['on_time_percentage'] ?? 0 }}%</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-100 p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100">
                    <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="ml-5">
                <dl>
                    <dt class="text-sm font-medium text-gray-500">Total Hours</dt>
                    <dd class="text-2xl font-bold text-gray-900">{{ $stats['total_hours'] ?? 0 }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
