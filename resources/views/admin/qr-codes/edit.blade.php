@extends('layouts.app')

@section('title', 'Edit QR Code')

@section('content')
<div>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Edit QR Code</h1>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-6">
        <form action="{{ route('admin.qr-codes.update', $qrCode) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">Code</label>
                    <input type="text" name="code" id="code" value="{{ old('code', $qrCode->code) }}" 
                           class="block w-full px-4 py-2 border rounded-lg @error('code') border-red-500 @enderror">
                    @error('code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select name="type" id="type" required class="block w-full px-4 py-2 border rounded-lg">
                        <option value="daily" {{ old('type', $qrCode->type) === 'daily' ? 'selected' : '' }}>Daily</option>
                        <option value="weekly" {{ old('type', $qrCode->type) === 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="permanent" {{ old('type', $qrCode->type) === 'permanent' ? 'selected' : '' }}>Permanent</option>
                    </select>
                </div>

                <div>
                    <label for="valid_from" class="block text-sm font-medium text-gray-700 mb-2">Valid From</label>
                    <input type="date" name="valid_from" id="valid_from" 
                           value="{{ old('valid_from', $qrCode->valid_from ? $qrCode->valid_from->format('Y-m-d') : '') }}" 
                           class="block w-full px-4 py-2 border rounded-lg">
                </div>

                <div>
                    <label for="valid_until" class="block text-sm font-medium text-gray-700 mb-2">Valid Until</label>
                    <input type="date" name="valid_until" id="valid_until" 
                           value="{{ old('valid_until', $qrCode->valid_until ? $qrCode->valid_until->format('Y-m-d') : '') }}" 
                           class="block w-full px-4 py-2 border rounded-lg">
                </div>

                <div>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $qrCode->is_active) ? 'checked' : '' }}
                               class="form-checkbox text-green-600">
                        <span class="ml-2 text-sm">Active</span>
                    </label>
                </div>
            </div>

            <div class="mt-6 flex items-center space-x-3">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Update QR Code
                </button>
                <a href="{{ route('admin.qr-codes.show', $qrCode) }}" 
                   class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
