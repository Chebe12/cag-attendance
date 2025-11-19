@extends('layouts.app')

@section('title', 'QR Code Details')

@section('content')
<div>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">QR Code: {{ $qrCode->code }}</h1>
        <p class="mt-2 text-sm text-gray-700">Type: {{ ucfirst($qrCode->type) }}</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="text-center mb-6">
            <div class="inline-block mx-auto" style="max-width: 400px;">
                {!! $qrCodeSvg !!}
            </div>
        </div>

        <div class="space-y-4">
            <div>
                <strong>Code:</strong> {{ $qrCode->code }}
            </div>
            <div>
                <strong>Type:</strong> {{ ucfirst($qrCode->type) }}
            </div>
            <div>
                <strong>Status:</strong> 
                <span class="{{ $qrCode->is_active ? 'text-green-600' : 'text-gray-600' }}">
                    {{ $qrCode->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <div>
                <strong>Scan Count:</strong> {{ $qrCode->scan_count }}
            </div>
            @if($qrCode->valid_from)
                <div><strong>Valid From:</strong> {{ $qrCode->valid_from->format('M d, Y') }}</div>
            @endif
            @if($qrCode->valid_until)
                <div><strong>Valid Until:</strong> {{ $qrCode->valid_until->format('M d, Y') }}</div>
            @endif
        </div>

        <div class="mt-6 flex items-center space-x-3">
            <a href="{{ route('admin.qr-codes.edit', $qrCode) }}"
               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                Edit
            </a>
            <a href="{{ route('admin.qr-codes.download', $qrCode) }}"
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Download
            </a>
            <a href="{{ route('admin.qr-codes.print', $qrCode) }}" target="_blank"
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Print
            </a>
            <form action="{{ route('admin.qr-codes.destroy', $qrCode) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Delete
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
