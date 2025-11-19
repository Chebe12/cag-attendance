@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Schedule Category</h1>
        <p class="mt-2 text-sm text-gray-600">Update category: {{ $scheduleCategory->name }}</p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form method="POST" action="{{ route('admin.schedule-categories.update', $scheduleCategory) }}">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Category Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name', $scheduleCategory->name) }}" required
                       placeholder="e.g., First Term 2024/2025, Marketing Q1 2024"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Code -->
            <div class="mb-6">
                <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                    Code <span class="text-red-500">*</span>
                </label>
                <input type="text" name="code" id="code" value="{{ old('code', $scheduleCategory->code) }}" required
                       placeholder="e.g., FIRST-TERM-2024-2025"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('code') border-red-500 @enderror">
                <p class="mt-1 text-xs text-gray-500">A unique code for easy reference.</p>
                @error('code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description
                </label>
                <textarea name="description" id="description" rows="3"
                          placeholder="Optional description for this category..."
                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('description') border-red-500 @enderror">{{ old('description', $scheduleCategory->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Date Range -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Start Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="start_date" id="start_date"
                           value="{{ old('start_date', $scheduleCategory->start_date->format('Y-m-d')) }}" required
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('start_date') border-red-500 @enderror">
                    @error('start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                        End Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="end_date" id="end_date"
                           value="{{ old('end_date', $scheduleCategory->end_date->format('Y-m-d')) }}" required
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('end_date') border-red-500 @enderror">
                    @error('end_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Status -->
            <div class="mb-6">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                    Status <span class="text-red-500">*</span>
                </label>
                <select name="status" id="status" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('status') border-red-500 @enderror">
                    <option value="draft" {{ old('status', $scheduleCategory->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="active" {{ old('status', $scheduleCategory->status) == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="completed" {{ old('status', $scheduleCategory->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="archived" {{ old('status', $scheduleCategory->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <button type="button" onclick="if(confirm('Are you sure you want to delete this category?')) document.getElementById('delete-form').submit();"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                    Delete Category
                </button>
                <div class="flex gap-3">
                    <a href="{{ route('admin.schedule-categories.show', $scheduleCategory) }}"
                       class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors">
                        Update Category
                    </button>
                </div>
            </div>
        </form>

        <!-- Delete Form -->
        <form id="delete-form" method="POST" action="{{ route('admin.schedule-categories.destroy', $scheduleCategory) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
@endsection
