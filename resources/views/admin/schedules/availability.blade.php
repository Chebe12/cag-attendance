@extends('layouts.app')

@section('title', 'Instructor Availability')

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
        <span class="text-gray-500 font-medium">Instructor Availability</span>
    </li>
@endsection

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Instructor Availability</h1>
                <p class="mt-2 text-sm text-gray-600">View available time slots for each instructor</p>
            </div>
            <a href="{{ route('admin.schedules.index') }}"
               class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
        </div>
    </div>
</div>

<!-- Filters Section -->
<div class="mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <form method="GET" action="{{ route('admin.schedules.availability') }}">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Category Filter -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Filter by Category/Term</label>
                    <select name="category_id" id="category_id"
                            class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Department Filter -->
                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">Filter by Department</label>
                    <select name="department_id" id="department_id"
                            class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ $departmentId == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Submit -->
                <div class="flex items-end">
                    <button type="submit"
                            class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
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

<!-- Legend -->
<div class="mb-6 bg-white rounded-lg shadow-md p-6">
    <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Legend
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg border border-green-200">
            <div class="w-8 h-8 bg-green-500 rounded flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <div class="font-semibold text-sm text-gray-900">Available</div>
                <div class="text-xs text-gray-600">Instructor is free for this slot</div>
            </div>
        </div>
        <div class="flex items-center gap-3 p-3 bg-red-50 rounded-lg border border-red-200">
            <div class="w-8 h-8 bg-red-500 rounded flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <div>
                <div class="font-semibold text-sm text-gray-900">Occupied</div>
                <div class="text-xs text-gray-600">Instructor is assigned to a client</div>
            </div>
        </div>
    </div>
</div>

<!-- Instructor Availability Cards -->
@if(count($instructorAvailability) > 0)
    @foreach($instructorAvailability as $data)
        <div class="mb-6 bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Instructor Header -->
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="bg-white rounded-full p-3">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white">{{ $data['instructor']->name }}</h2>
                            <p class="text-sm text-green-100">{{ $data['instructor']->employee_no }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-white">{{ $data['percentage_full'] }}%</div>
                        <div class="text-sm text-green-100">Capacity Used</div>
                        @if($data['is_full'])
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mt-1">
                                Fully Booked
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-1">
                                Available
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Stats Bar -->
            <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-6">
                        <div>
                            <span class="text-gray-600">Occupied:</span>
                            <span class="font-semibold text-red-600">{{ $data['occupied_slots'] }}/{{ $data['total_slots'] }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Available:</span>
                            <span class="font-semibold text-green-600">{{ $data['available_slots'] }}/{{ $data['total_slots'] }}</span>
                        </div>
                    </div>
                    <div class="w-64 bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full" style="width: {{ $data['percentage_full'] }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Weekly Grid -->
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-gray-300 px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase w-32">Session</th>
                                @foreach($weekDays as $day)
                                <th class="border border-gray-300 px-3 py-2 text-center text-xs font-bold text-gray-700 uppercase">
                                    {{ ucfirst($day) }}
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sessions as $session)
                            <tr>
                                <td class="border border-gray-300 px-3 py-2 font-semibold text-sm
                                    @if($session === 'morning') bg-green-50
                                    @elseif($session === 'mid-morning') bg-blue-50
                                    @else bg-purple-50
                                    @endif">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 flex-shrink-0
                                            @if($session === 'morning') text-green-600
                                            @elseif($session === 'mid-morning') text-blue-600
                                            @else text-purple-600
                                            @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($session === 'afternoon')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                            @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                            @endif
                                        </svg>
                                        <div>
                                            <div>{{ ucfirst(str_replace('-', ' ', $session)) }}</div>
                                            <div class="text-xs text-gray-600">
                                                @php
                                                    $times = \App\Models\Schedule::getSessionTimes($session);
                                                @endphp
                                                {{ date('g:i A', strtotime($times['start'])) }} - {{ date('g:i A', strtotime($times['end'])) }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                @foreach($weekDays as $day)
                                <td class="border border-gray-300 px-3 py-2">
                                    @if($data['slot_details'][$day][$session]['occupied'])
                                        <div class="bg-red-100 border-l-4 border-red-500 rounded p-2">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                <div class="text-xs">
                                                    <div class="font-semibold text-red-900">Occupied</div>
                                                    <div class="text-red-700">{{ $data['slot_details'][$day][$session]['client'] }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="bg-green-100 border-l-4 border-green-500 rounded p-2">
                                            <div class="flex items-center justify-center gap-2">
                                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                <div class="text-xs font-semibold text-green-900">Available</div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
@else
    <div class="bg-white rounded-lg shadow-md p-12 text-center">
        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
        <h3 class="text-xl font-medium text-gray-900 mb-2">No instructors found</h3>
        <p class="text-gray-500">Try adjusting your filters or add instructors to the system.</p>
    </div>
@endif
@endsection
