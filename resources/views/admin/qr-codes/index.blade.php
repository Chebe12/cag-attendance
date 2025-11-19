@extends('layouts.app')

@section('title', 'QR Codes')

@section('content')
<div>
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">QR Code Management</h1>
                <p class="mt-2 text-sm text-gray-700">Generate and manage QR codes for attendance</p>
            </div>
            <a href="{{ route('admin.qr-codes.create') }}"
               class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg">
                Generate New QR Code
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            @if($qrCodes->isEmpty())
                <div class="text-center py-12">
                    <p class="text-gray-500">No QR codes yet. Create one to get started.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($qrCodes as $qrCode)
                        <div class="border rounded-lg p-4">
                            <div class="text-center">
                                <p class="font-medium">{{ $qrCode->code }}</p>
                                <p class="text-sm text-gray-500">{{ $qrCode->type }}</p>
                                <a href="{{ route('admin.qr-codes.show', $qrCode) }}"
                                   class="mt-2 inline-block text-green-600 hover:text-green-700">
                                    View Details
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-6">{{ $qrCodes->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
