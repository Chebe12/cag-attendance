@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $scheduleCategory->name }}</h1>
            <p class="mt-2 text-sm text-gray-600">
                {{ $scheduleCategory->start_date->format('F d, Y') }} - {{ $scheduleCategory->end_date->format('F d, Y') }}
                <span class="mx-2">•</span>
                {{ $scheduleCategory->duration }} days
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.schedule-categories.index') }}"
               class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors">
                Back
            </a>
            <a href="{{ route('admin.schedules.bulk.create', ['category_id' => $scheduleCategory->id]) }}"
               class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    @if($stats['published_schedules'] > 0)
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    @endif
                </svg>
                @if($stats['published_schedules'] > 0)
                    Edit Schedules
                @else
                    Create Schedules
                @endif
            </a>
            <a href="{{ route('admin.schedule-categories.edit', $scheduleCategory) }}"
               class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                Edit Category
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Total Schedules</div>
            <div class="text-2xl font-bold text-gray-900">{{ $stats['total_schedules'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Published</div>
            <div class="text-2xl font-bold text-green-600">{{ $stats['published_schedules'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Drafts</div>
            <div class="text-2xl font-bold text-yellow-600">{{ $stats['draft_schedules'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Clients</div>
            <div class="text-2xl font-bold text-blue-600">{{ $stats['unique_clients'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Instructors</div>
            <div class="text-2xl font-bold text-purple-600">{{ $stats['unique_instructors'] }}</div>
        </div>
    </div>

    <!-- Actions Bar -->
    @if($stats['draft_schedules'] > 0)
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-sm font-medium text-yellow-800">Draft Schedules Pending</h3>
                <p class="text-sm text-yellow-700 mt-1">
                    You have {{ $stats['draft_schedules'] }} draft schedule(s) that haven't been published yet.
                </p>
            </div>
            <div class="flex gap-2">
                <form method="POST" action="{{ route('admin.schedules.bulk.delete-drafts', $scheduleCategory) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Delete all draft schedules?')"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                        Delete Drafts
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.schedules.bulk.publish', $scheduleCategory) }}" class="inline">
                    @csrf
                    <button type="submit" onclick="return confirm('Publish all draft schedules?')"
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                        Publish All Drafts
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Category Info -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Category Details</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-600">Code</label>
                <p class="mt-1 text-sm text-gray-900">{{ $scheduleCategory->code }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Status</label>
                <p class="mt-1">
                    @php
                        $statusColors = [
                            'draft' => 'bg-gray-100 text-gray-800',
                            'active' => 'bg-green-100 text-green-800',
                            'completed' => 'bg-blue-100 text-blue-800',
                            'archived' => 'bg-yellow-100 text-yellow-800',
                        ];
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$scheduleCategory->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($scheduleCategory->status) }}
                    </span>
                </p>
            </div>
            @if($scheduleCategory->description)
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-600">Description</label>
                <p class="mt-1 text-sm text-gray-900">{{ $scheduleCategory->description }}</p>
            </div>
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-600">Created By</label>
                <p class="mt-1 text-sm text-gray-900">{{ $scheduleCategory->creator->name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Created On</label>
                <p class="mt-1 text-sm text-gray-900">{{ $scheduleCategory->created_at->format('M d, Y g:i A') }}</p>
            </div>
        </div>
    </div>

    <!-- Weekly Schedule Overview -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900">Weekly Schedule Overview</h2>
            <p class="text-sm text-gray-600 mt-1">All schedules for this category grouped by day and session</p>
        </div>

        @if($groupedSchedules->count() > 0)
            <div class="overflow-x-auto">
                @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                    @if(isset($groupedSchedules[$day]))
                        <div class="border-b border-gray-200">
                            <div class="bg-gray-50 px-6 py-3">
                                <h3 class="text-sm font-bold text-gray-900 uppercase">{{ $day }}</h3>
                            </div>
                            <div class="px-6 py-4">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    @foreach(['morning', 'mid-morning', 'afternoon'] as $session)
                                        <div class="border rounded-lg p-4 {{ $session === 'morning' ? 'bg-green-50' : ($session === 'mid-morning' ? 'bg-blue-50' : 'bg-purple-50') }}">
                                            <h4 class="text-sm font-semibold text-gray-900 mb-2">
                                                {{ ucfirst(str_replace('-', ' ', $session)) }}
                                                @php
                                                    $times = \App\Models\Schedule::getSessionTimes($session);
                                                @endphp
                                                <span class="text-xs text-gray-600 font-normal">
                                                    ({{ date('g:i A', strtotime($times['start'])) }} - {{ date('g:i A', strtotime($times['end'])) }})
                                                </span>
                                            </h4>
                                            @if(isset($groupedSchedules[$day][$session]))
                                                <div class="space-y-2">
                                                    @foreach($groupedSchedules[$day][$session] as $schedule)
                                                        <div class="text-sm bg-white rounded p-2 border border-gray-200">
                                                            <div class="font-medium text-gray-900">{{ $schedule->client->name }}</div>
                                                            <div class="text-xs text-gray-600">{{ $schedule->user->name }}</div>
                                                            @if($schedule->draft_status === 'draft')
                                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 mt-1">
                                                                    Draft
                                                                </span>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-xs text-gray-500 italic">No schedules</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No schedules yet</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating schedules for this category.</p>
                <div class="mt-6">
                    <a href="{{ route('admin.schedules.bulk.create', ['category_id' => $scheduleCategory->id]) }}"
                       class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg">
                        Create Schedules
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
