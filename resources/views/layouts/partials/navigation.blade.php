@php
    $currentRoute = request()->route()->getName();
@endphp

<!-- Admin Navigation -->
@if(auth()->user()->user_type === 'admin')
    <a href="{{ route('admin.dashboard') }}"
       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all {{ str_starts_with($currentRoute, 'admin.dashboard') ? 'bg-green-700 text-white shadow-lg' : 'text-green-100 hover:bg-green-700/50 hover:text-white' }}">
        <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Dashboard
    </a>

    <a href="{{ route('admin.users.index') }}"
       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all {{ str_starts_with($currentRoute, 'admin.users') ? 'bg-green-700 text-white shadow-lg' : 'text-green-100 hover:bg-green-700/50 hover:text-white' }}">
        <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
        Users
    </a>

    <a href="{{ route('admin.clients.index') }}"
       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all {{ str_starts_with($currentRoute, 'admin.clients') ? 'bg-green-700 text-white shadow-lg' : 'text-green-100 hover:bg-green-700/50 hover:text-white' }}">
        <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
        </svg>
        Clients
    </a>

    <a href="{{ route('admin.departments.index') }}"
       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all {{ str_starts_with($currentRoute, 'admin.departments') ? 'bg-green-700 text-white shadow-lg' : 'text-green-100 hover:bg-green-700/50 hover:text-white' }}">
        <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
        </svg>
        Departments
    </a>

    <a href="{{ route('admin.schedules.index') }}"
       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all {{ str_starts_with($currentRoute, 'admin.schedules') ? 'bg-green-700 text-white shadow-lg' : 'text-green-100 hover:bg-green-700/50 hover:text-white' }}">
        <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Schedules
    </a>

    <a href="{{ route('admin.schedule-categories.index') }}"
       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all {{ str_starts_with($currentRoute, 'admin.schedule-categories') ? 'bg-green-700 text-white shadow-lg' : 'text-green-100 hover:bg-green-700/50 hover:text-white' }}">
        <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
        </svg>
        Schedule Categories
    </a>

    <a href="{{ route('admin.schedules.availability') }}"
       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all {{ $currentRoute === 'admin.schedules.availability' ? 'bg-green-700 text-white shadow-lg' : 'text-green-100 hover:bg-green-700/50 hover:text-white' }}">
        <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Instructor Availability
    </a>

    <a href="{{ route('admin.qr-codes.index') }}"
       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all {{ str_starts_with($currentRoute, 'admin.qr-codes') ? 'bg-green-700 text-white shadow-lg' : 'text-green-100 hover:bg-green-700/50 hover:text-white' }}">
        <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
        </svg>
        QR Codes
    </a>

    <a href="{{ route('admin.attendance.index') }}"
       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all {{ str_starts_with($currentRoute, 'admin.attendance') ? 'bg-green-700 text-white shadow-lg' : 'text-green-100 hover:bg-green-700/50 hover:text-white' }}">
        <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
        </svg>
        Attendance
    </a>

    <a href="{{ route('admin.reports.index') }}"
       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all {{ str_starts_with($currentRoute, 'admin.reports') ? 'bg-green-700 text-white shadow-lg' : 'text-green-100 hover:bg-green-700/50 hover:text-white' }}">
        <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Reports
    </a>

<!-- Staff Navigation -->
@else
    <a href="{{ route('staff.dashboard') }}"
       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all {{ str_starts_with($currentRoute, 'staff.dashboard') ? 'bg-green-700 text-white shadow-lg' : 'text-green-100 hover:bg-green-700/50 hover:text-white' }}">
        <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Dashboard
    </a>

    <a href="{{ route('staff.attendance.mark') }}"
       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all {{ str_starts_with($currentRoute, 'staff.attendance.mark') ? 'bg-orange-600 text-white shadow-lg' : 'text-green-100 hover:bg-orange-600/80 hover:text-white' }}">
        <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
        </svg>
        Mark Attendance
    </a>

    <a href="{{ route('staff.attendance.index') }}"
       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all {{ str_starts_with($currentRoute, 'staff.attendance.index') ? 'bg-green-700 text-white shadow-lg' : 'text-green-100 hover:bg-green-700/50 hover:text-white' }}">
        <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
        </svg>
        My Attendance
    </a>

    <a href="{{ route('staff.schedules.index') }}"
       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all {{ str_starts_with($currentRoute, 'staff.schedules') ? 'bg-green-700 text-white shadow-lg' : 'text-green-100 hover:bg-green-700/50 hover:text-white' }}">
        <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        My Schedule
    </a>
@endif
