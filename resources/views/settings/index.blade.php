@extends('layouts.app')

@section('title', 'Settings')

@section('breadcrumbs')
    <li class="flex items-center">
        <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-500">
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
            </svg>
        </a>
        <svg class="h-5 w-5 text-gray-300 mx-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
        <span class="text-gray-500 font-medium">Settings</span>
    </li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Page header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Settings</h1>
        <p class="mt-2 text-sm text-gray-700">Manage your preferences and notification settings</p>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded">
        <div class="flex">
            <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p class="ml-3 text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <!-- Notification Settings -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Notification Preferences</h2>
            <form action="{{ route('settings.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Email Notifications</h3>
                            <p class="text-sm text-gray-500">Receive notifications via email</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="notification_email" value="1" class="sr-only peer"
                                   {{ session('user_settings.notification_email', true) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Push Notifications</h3>
                            <p class="text-sm text-gray-500">Receive in-app push notifications</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="notification_push" value="1" class="sr-only peer"
                                   {{ session('user_settings.notification_push', true) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit"
                            class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Account Information -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h2>
            <div class="space-y-3">
                <div class="flex justify-between items-center py-3 border-b border-gray-200">
                    <span class="text-sm font-medium text-gray-700">Name</span>
                    <span class="text-sm text-gray-900">{{ $user->full_name }}</span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gray-200">
                    <span class="text-sm font-medium text-gray-700">Email</span>
                    <span class="text-sm text-gray-900">{{ $user->email }}</span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gray-200">
                    <span class="text-sm font-medium text-gray-700">Employee No</span>
                    <span class="text-sm text-gray-900">{{ $user->employee_no ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between items-center py-3">
                    <span class="text-sm font-medium text-gray-700">User Type</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        {{ ucfirst($user->user_type ?? 'staff') }}
                    </span>
                </div>
            </div>

            <div class="mt-6">
                <a href="{{ route('profile.edit') }}"
                   class="text-green-600 hover:text-green-700 text-sm font-medium">
                    Edit Profile ’
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
