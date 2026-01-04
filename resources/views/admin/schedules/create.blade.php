@extends('layouts.app')

@section('title', 'Create Schedule')

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
        <span class="text-gray-500 font-medium">Create</span>
    </li>
@endsection

@section('content')
<div x-data="{
    isRecurring: '{{ old('is_recurring', '0') }}'
}" x-init="$watch('isRecurring', value => {
    // Reset fields when switching types
    if (value === '0') {
        document.getElementById('day_of_week').value = '';
    } else {
        document.getElementById('scheduled_date').value = '';
    }
})">
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <!-- Page header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Create New Schedule</h1>
                <p class="mt-2 text-sm text-gray-700">Assign staff to client visits</p>
            </div>
            <a href="{{ route('admin.schedules.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Schedules
            </a>
        </div>
    </div>

    <!-- Form card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <form action="{{ route('admin.schedules.store') }}" method="POST">
            @csrf

            <div class="p-6 space-y-6">
                <!-- Schedule Type -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Schedule Type
                    </h3>

                    <div class="flex items-center space-x-4">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="is_recurring" value="0" x-model="isRecurring"
                                   class="form-radio text-green-600 focus:ring-green-500"
                                   {{ old('is_recurring', '0') === '0' ? 'checked' : '' }}>
                            <span class="ml-2">One-time Schedule</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="is_recurring" value="1" x-model="isRecurring"
                                   class="form-radio text-green-600 focus:ring-green-500"
                                   {{ old('is_recurring') === '1' ? 'checked' : '' }}>
                            <span class="ml-2">Weekly Recurring</span>
                        </label>
                    </div>
                </div>

                <!-- Schedule Information -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Schedule Details
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Schedule Category (for Weekly Recurring) -->
                        <div x-show="isRecurring === '1'" x-cloak class="md:col-span-2">
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Schedule Category <span class="text-red-500">*</span>
                            </label>
                            <select name="category_id"
                                    id="category_id"
                                    :required="isRecurring === '1'"
                                    class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 @error('category_id') border-red-500 @enderror">
                                <option value="">Select Schedule Category</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }} ({{ $category->start_date->format('M d, Y') }} - {{ $category->end_date->format('M d, Y') }})
                                </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Staff Member -->
                        <div>
                            <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Staff Member <span class="text-red-500">*</span>
                            </label>
                            <select name="user_id"
                                    id="user_id"
                                    required
                                    class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 @error('user_id') border-red-500 @enderror">
                                <option value="">Select Staff Member</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ (old('user_id') ?? request('user_id')) == $user->id ? 'selected' : '' }}>
                                    {{ $user->full_name }} ({{ $user->employee_no }})
                                </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Client -->
                        <div>
                            <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Client <span class="text-red-500">*</span>
                            </label>
                            <select name="client_id"
                                    id="client_id"
                                    required
                                    class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 @error('client_id') border-red-500 @enderror">
                                <option value="">Select Client</option>
                                @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Session -->
                        <div>
                            <label for="session_time" class="block text-sm font-medium text-gray-700 mb-2">
                                Session <span class="text-red-500">*</span>
                            </label>
                            <select name="session_time"
                                    id="session_time"
                                    required
                                    class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 @error('session_time') border-red-500 @enderror">
                                <option value="">Select Session</option>
                                <option value="morning" {{ old('session_time') == 'morning' ? 'selected' : '' }}>
                                    Morning Session (8:30 AM - 11:00 AM)
                                </option>
                                <option value="afternoon" {{ old('session_time') == 'afternoon' ? 'selected' : '' }}>
                                    Afternoon Session (12:00 PM - 2:30 PM)
                                </option>
                            </select>
                            @error('session_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date (One-time) / Day of Week (Recurring) -->
                        <div x-show="isRecurring === '0'" x-cloak>
                            <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Scheduled Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date"
                                   name="scheduled_date"
                                   id="scheduled_date"
                                   value="{{ old('scheduled_date', date('Y-m-d')) }}"
                                   min="{{ date('Y-m-d') }}"
                                   :required="isRecurring === '0'"
                                   class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 @error('scheduled_date') border-red-500 @enderror">
                            @error('scheduled_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-show="isRecurring === '1'" x-cloak>
                            <label for="day_of_week" class="block text-sm font-medium text-gray-700 mb-2">
                                Day of Week <span class="text-red-500">*</span>
                            </label>
                            <select name="day_of_week"
                                    id="day_of_week"
                                    :required="isRecurring === '1'"
                                    class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 @error('day_of_week') border-red-500 @enderror">
                                <option value="">Select Day</option>
                                <option value="monday" {{ old('day_of_week') == 'monday' ? 'selected' : '' }}>Monday</option>
                                <option value="tuesday" {{ old('day_of_week') == 'tuesday' ? 'selected' : '' }}>Tuesday</option>
                                <option value="wednesday" {{ old('day_of_week') == 'wednesday' ? 'selected' : '' }}>Wednesday</option>
                                <option value="thursday" {{ old('day_of_week') == 'thursday' ? 'selected' : '' }}>Thursday</option>
                                <option value="friday" {{ old('day_of_week') == 'friday' ? 'selected' : '' }}>Friday</option>
                                <option value="saturday" {{ old('day_of_week') == 'saturday' ? 'selected' : '' }}>Saturday</option>
                                <option value="sunday" {{ old('day_of_week') == 'sunday' ? 'selected' : '' }}>Sunday</option>
                            </select>
                            @error('day_of_week')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status"
                                    id="status"
                                    required
                                    class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 @error('status') border-red-500 @enderror">
                                <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="scheduled" {{ old('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="canceled" {{ old('status') === 'canceled' ? 'selected' : '' }}>Canceled</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Additional Notes
                    </h3>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Notes
                        </label>
                        <textarea name="notes"
                                  id="notes"
                                  rows="4"
                                  placeholder="Add any special instructions or notes for this schedule"
                                  class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.schedules.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 font-medium transition">
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold rounded-lg shadow-md transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Create Schedule
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
