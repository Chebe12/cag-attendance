@extends('layouts.app')

@section('title', 'Reports Dashboard')

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
        <span class="text-gray-500 font-medium">Reports</span>
    </li>
@endsection

@section('content')
<div>
    <!-- Page header -->
    <div class="mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Reports Dashboard</h1>
            <p class="mt-2 text-sm text-gray-700">Generate and view attendance and performance reports</p>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Attendance -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Attendance</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['total_attendance'] ?? 0 }}</p>
                    <p class="mt-1 text-xs text-gray-500">This month</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Staff -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Staff</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['active_staff'] ?? 0 }}</p>
                    <p class="mt-1 text-xs text-gray-500">Currently active</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Clients -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Clients</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['active_clients'] ?? 0 }}</p>
                    <p class="mt-1 text-xs text-gray-500">Currently active</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Avg Hours/Day -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Avg Hours/Day</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['avg_hours'] ?? '0.0' }}</p>
                    <p class="mt-1 text-xs text-gray-500">This month</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Reports -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Available Reports</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Attendance Report -->
                <a href="{{ route('admin.reports.attendance') }}" class="group">
                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-6 hover:shadow-lg transition">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </div>
                            <svg class="w-5 h-5 text-white/60 group-hover:text-white group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                        <h3 class="text-white font-semibold text-lg mb-2">Attendance Report</h3>
                        <p class="text-white/80 text-sm">View detailed attendance records with filters and export options</p>
                    </div>
                </a>

                <!-- Staff Performance Report -->
                <div class="group cursor-pointer">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-6 hover:shadow-lg transition">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <span class="text-xs font-medium px-2 py-1 bg-white/20 text-white rounded">Coming Soon</span>
                        </div>
                        <h3 class="text-white font-semibold text-lg mb-2">Staff Performance</h3>
                        <p class="text-white/80 text-sm">Analyze staff performance metrics and attendance patterns</p>
                    </div>
                </div>

                <!-- Client Report -->
                <div class="group cursor-pointer">
                    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg p-6 hover:shadow-lg transition">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <span class="text-xs font-medium px-2 py-1 bg-white/20 text-white rounded">Coming Soon</span>
                        </div>
                        <h3 class="text-white font-semibold text-lg mb-2">Client Report</h3>
                        <p class="text-white/80 text-sm">View client-specific attendance and staff allocation data</p>
                    </div>
                </div>

                <!-- Shift Coverage Report -->
                <div class="group cursor-pointer">
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-6 hover:shadow-lg transition">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="text-xs font-medium px-2 py-1 bg-white/20 text-white rounded">Coming Soon</span>
                        </div>
                        <h3 class="text-white font-semibold text-lg mb-2">Shift Coverage</h3>
                        <p class="text-white/80 text-sm">Track shift coverage and identify scheduling gaps</p>
                    </div>
                </div>

                <!-- Payroll Summary -->
                <div class="group cursor-pointer">
                    <div class="bg-gradient-to-br from-pink-500 to-pink-600 rounded-lg p-6 hover:shadow-lg transition">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="text-xs font-medium px-2 py-1 bg-white/20 text-white rounded">Coming Soon</span>
                        </div>
                        <h3 class="text-white font-semibold text-lg mb-2">Payroll Summary</h3>
                        <p class="text-white/80 text-sm">Generate payroll reports based on attendance hours</p>
                    </div>
                </div>

                <!-- Custom Report -->
                <div class="group cursor-pointer">
                    <div class="bg-gradient-to-br from-gray-500 to-gray-600 rounded-lg p-6 hover:shadow-lg transition">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <span class="text-xs font-medium px-2 py-1 bg-white/20 text-white rounded">Coming Soon</span>
                        </div>
                        <h3 class="text-white font-semibold text-lg mb-2">Custom Report</h3>
                        <p class="text-white/80 text-sm">Create custom reports with your own filters and metrics</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
