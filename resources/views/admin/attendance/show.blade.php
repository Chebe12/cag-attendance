@extends('layouts.app')

@section('title', 'Attendance Details')

@section('content')
<div class="py-6">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Attendance Details</h1>
            <p class="mt-1 text-sm text-gray-600">View detailed attendance information</p>
        </div>
        <a href="{{ route('admin.attendance.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
            Back to List
        </a>
    </div>

    <!-- Attendance Details -->
    <div class="space-y-6">
        <!-- Employee Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Employee Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $attendance->user->full_name ?? ($attendance->user->firstname . ' ' . $attendance->user->lastname) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Employee Number</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $attendance->user->employee_no ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $attendance->user->email }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Department</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $attendance->user->department ?? 'N/A' }}</dd>
                </div>
            </div>
        </div>

        <!-- Schedule & Assignment Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Schedule & Assignment
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Client</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $attendance->client->name ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Shift</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $attendance->shift->name ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Schedule</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($attendance->schedule)
                            {{ ucfirst($attendance->schedule->day_of_week) }} - {{ ucfirst($attendance->schedule->session_time) }}
                        @else
                            N/A
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">QR Code</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $attendance->qrCode->code ?? 'N/A' }}</dd>
                </div>
            </div>
        </div>

        <!-- Attendance Timing -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Attendance Timing
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Date</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-semibold">
                        {{ $attendance->attendance_date ? \Carbon\Carbon::parse($attendance->attendance_date)->format('F j, Y') : 'N/A' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Check In Time</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('g:i A') : 'Not checked in' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Check Out Time</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('g:i A') : 'Not checked out' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Work Duration</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-semibold">
                        @if($attendance->work_duration)
                            {{ floor($attendance->work_duration / 60) }}h {{ $attendance->work_duration % 60 }}m
                        @else
                            -
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1">
                        @if($attendance->status === 'present')
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                Present
                            </span>
                        @elseif($attendance->status === 'absent')
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                Absent
                            </span>
                        @elseif($attendance->status === 'late')
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                Late
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                {{ ucfirst($attendance->status) }}
                            </span>
                        @endif
                    </dd>
                </div>
            </div>
        </div>

        <!-- Location & IP Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Location & IP Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Check In Details -->
                <div class="border border-gray-200 rounded-lg p-4 bg-green-50">
                    <h4 class="text-sm font-semibold text-green-900 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Check In Details
                    </h4>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-xs font-medium text-gray-600">Location</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $attendance->check_in_location ?? 'Not recorded' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-600">IP Address</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">
                                {{ $attendance->check_in_ip ?? 'Not recorded' }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Check Out Details -->
                <div class="border border-gray-200 rounded-lg p-4 bg-red-50">
                    <h4 class="text-sm font-semibold text-red-900 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Check Out Details
                    </h4>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-xs font-medium text-gray-600">Location</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $attendance->check_out_location ?? 'Not recorded' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-600">IP Address</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">
                                {{ $attendance->check_out_ip ?? 'Not recorded' }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        @if($attendance->notes)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Additional Notes
            </h3>
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $attendance->notes }}</p>
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div class="text-xs text-gray-500">
                    <p>Created: {{ $attendance->created_at->format('F j, Y g:i A') }}</p>
                    <p>Last Updated: {{ $attendance->updated_at->format('F j, Y g:i A') }}</p>
                </div>
                <form method="POST" action="{{ route('admin.attendance.destroy', $attendance) }}" onsubmit="return confirm('Are you sure you want to delete this attendance record? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete Record
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
