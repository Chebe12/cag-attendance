@extends('layouts.app')

@section('title', 'Admin Dashboard')

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
    <p class="mt-2 text-sm text-gray-600">Here's what's happening with your attendance system today.</p>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
    <!-- Total Users Card -->
    <div class="relative overflow-hidden rounded-xl bg-white shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-blue-600">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-bold text-gray-900">{{ $stats['total_users'] ?? 0 }}</div>
                            <div class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                                <svg class="h-4 w-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ $stats['new_users_this_month'] ?? 0 }}</span>
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-6 py-3">
            <div class="text-sm">
                <a href="{{ route('admin.users.index') }}" class="font-medium text-blue-600 hover:text-blue-500">View all users</a>
            </div>
        </div>
    </div>

    <!-- Today's Attendance Card -->
    <div class="relative overflow-hidden rounded-xl bg-white shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-br from-green-500 to-green-600">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Today's Attendance</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-bold text-gray-900">{{ $stats['today_attendance'] ?? 0 }}</div>
                            <div class="ml-2 text-sm font-medium text-gray-500">/ {{ $stats['total_users'] ?? 0 }}</div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-6 py-3">
            <div class="text-sm">
                <a href="{{ route('admin.attendance.index') }}" class="font-medium text-green-600 hover:text-green-500">View attendance</a>
            </div>
        </div>
    </div>

    <!-- Active Clients Card -->
    <div class="relative overflow-hidden rounded-xl bg-white shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-br from-orange-500 to-orange-600">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Active Clients</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-bold text-gray-900">{{ $stats['total_clients'] ?? 0 }}</div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-6 py-3">
            <div class="text-sm">
                <a href="{{ route('admin.clients.index') }}" class="font-medium text-orange-600 hover:text-orange-500">Manage clients</a>
            </div>
        </div>
    </div>

    <!-- Scheduled Today Card -->
    <div class="relative overflow-hidden rounded-xl bg-white shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-br from-purple-500 to-purple-600">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Scheduled Today</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-bold text-gray-900">{{ $stats['today_schedules'] ?? 0 }}</div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-6 py-3">
            <div class="text-sm">
                <a href="{{ route('admin.schedules.index') }}" class="font-medium text-purple-600 hover:text-purple-500">View schedules</a>
            </div>
        </div>
    </div>
</div>

<!-- Charts and Tables Grid -->
<div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-8">
    <!-- Attendance Trends Chart -->
    <div class="rounded-xl bg-white shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Attendance Trends (Last 7 Days)</h2>
        <div class="relative h-80">
            <canvas id="attendanceChart"></canvas>
        </div>
    </div>

    <!-- Today's Schedules -->
    <div class="rounded-xl bg-white shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Today's Schedules</h2>
            <a href="{{ route('admin.schedules.index') }}" class="text-sm font-medium text-green-600 hover:text-green-500">View all</a>
        </div>
        <div class="flow-root">
            <ul role="list" class="-my-5 divide-y divide-gray-100">
                @forelse($todaySchedules ?? [] as $schedule)
                <li class="py-4">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center text-white font-semibold">
                                {{ substr($schedule->user->name, 0, 1) }}
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $schedule->user->name }}</p>
                            <p class="text-sm text-gray-500 truncate">{{ $schedule->client->name }}</p>
                        </div>
                        <div class="flex-shrink-0 text-right">
                            <p class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i A') }}</p>
                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($schedule->end_time)->format('g:i A') }}</p>
                        </div>
                    </div>
                </li>
                @empty
                <li class="py-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">No schedules for today</p>
                </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

<!-- Schedule Categories Section -->
<div class="rounded-xl bg-white shadow-sm border border-gray-100 p-6 mb-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Schedule Categories</h2>
            <p class="text-sm text-gray-600 mt-1">Manage your schedule terms and categories</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.schedule-categories.index') }}" class="text-sm font-medium text-green-600 hover:text-green-500">View all</a>
            <span class="text-gray-300">|</span>
            <a href="{{ route('admin.schedule-categories.create') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">Create new</a>
        </div>
    </div>

    @php
        $activeCategories = \App\Models\ScheduleCategory::whereIn('status', ['active', 'draft'])
            ->with(['schedules'])
            ->latest()
            ->take(3)
            ->get();
    @endphp

    @if($activeCategories->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($activeCategories as $category)
                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-gray-900 truncate">{{ $category->name }}</h3>
                        @php
                            $statusColors = [
                                'draft' => 'bg-gray-100 text-gray-800',
                                'active' => 'bg-green-100 text-green-800',
                                'completed' => 'bg-blue-100 text-blue-800',
                                'archived' => 'bg-yellow-100 text-yellow-800',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $statusColors[$category->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($category->status) }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-600 mb-3">
                        {{ $category->start_date->format('M d, Y') }} - {{ $category->end_date->format('M d, Y') }}
                    </p>
                    <div class="grid grid-cols-2 gap-2 text-sm mb-3">
                        <div class="bg-gray-50 rounded p-2 text-center">
                            <div class="text-xs text-gray-600">Schedules</div>
                            <div class="font-bold text-gray-900">{{ $category->schedules->count() }}</div>
                        </div>
                        <div class="bg-green-50 rounded p-2 text-center">
                            <div class="text-xs text-gray-600">Published</div>
                            <div class="font-bold text-green-600">{{ $category->publishedSchedules()->count() }}</div>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.schedule-categories.show', $category) }}"
                           class="flex-1 text-center px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-medium rounded transition-colors">
                            View
                        </a>
                        <a href="{{ route('admin.schedules.bulk.create', ['category_id' => $category->id]) }}"
                           class="flex-1 text-center px-3 py-1.5 bg-green-50 hover:bg-green-100 text-green-700 text-xs font-medium rounded transition-colors">
                            Manage
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No categories yet</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating your first schedule category.</p>
            <div class="mt-4">
                <a href="{{ route('admin.schedule-categories.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg">
                    Create Category
                </a>
            </div>
        </div>
    @endif
</div>

<!-- Recent Attendances Table -->
<div class="rounded-xl bg-white shadow-sm border border-gray-100 p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-semibold text-gray-900">Recent Attendances</h2>
        <a href="{{ route('admin.attendance.index') }}" class="text-sm font-medium text-green-600 hover:text-green-500">View all</a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($recentAttendances ?? [] as $attendance)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white font-semibold">
                                    {{ substr($attendance->user->name, 0, 1) }}
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $attendance->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $attendance->user->employee_number }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $attendance->schedule->client->name ?? 'N/A' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('M d, g:i A') : '-' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('M d, g:i A') : '-' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($attendance->check_out)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Completed
                            </span>
                        @elseif($attendance->check_in)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Checked In
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Pending
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">No attendance records yet</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('attendanceChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartData['labels'] ?? ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']) !!},
                datasets: [{
                    label: 'Attendance',
                    data: {!! json_encode($chartData['data'] ?? [12, 19, 15, 17, 14, 16, 18]) !!},
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgb(34, 197, 94)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        borderRadius: 8,
                        titleColor: '#fff',
                        bodyColor: '#fff'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            precision: 0
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
