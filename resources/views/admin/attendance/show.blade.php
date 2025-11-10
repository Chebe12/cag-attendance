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
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Employee Information -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Employee Information</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $attendance->user->firstname }} {{ $attendance->user->lastname }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Employee Number</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $attendance->user->employee_no }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $attendance->user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">User Type</dt>
                        <dd class="mt-1 text-sm text-gray-900 capitalize">{{ str_replace('_', ' ', $attendance->user->user_type) }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Attendance Information -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Attendance Information</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Check In Time</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $attendance->check_in_time ? $attendance->check_in_time->format('F j, Y - g:i A') : 'Not checked in' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Check Out Time</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $attendance->check_out_time ? $attendance->check_out_time->format('F j, Y - g:i A') : 'Not checked out' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Duration</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if($attendance->check_in_time && $attendance->check_out_time)
                                {{ $attendance->check_in_time->diffForHumans($attendance->check_out_time, true) }}
                            @else
                                -
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            @if($attendance->check_out_time)
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                    Completed
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                    In Progress
                                </span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">QR Code Used</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $attendance->qrCode->code ?? 'N/A' }}</dd>
                    </div>
                    @if($attendance->schedule)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Assigned Schedule</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $attendance->schedule->shift->name ?? 'N/A' }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-6 pt-6 border-t border-gray-200 flex gap-3">
            <form method="POST" action="{{ route('admin.attendance.destroy', $attendance) }}" onsubmit="return confirm('Are you sure you want to delete this attendance record? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Delete Record
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
