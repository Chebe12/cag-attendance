@extends('layouts.app')

@section('title', 'Mark Attendance')

@section('breadcrumbs')
<li class="flex items-center">
    <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
    </svg>
    <span class="ml-2 text-sm text-gray-500">Dashboard</span>
</li>
<li class="flex items-center">
    <svg class="h-5 w-5 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
    </svg>
    <span class="text-sm font-medium text-gray-900">Mark Attendance</span>
</li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Page header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Scan QR Code</h1>
        <p class="mt-2 text-sm text-gray-600">Position the QR code within the camera frame to mark your attendance</p>
    </div>

    <!-- Scanner Section -->
    <div class="rounded-xl bg-white shadow-lg border border-gray-100 overflow-hidden">
        <div class="p-8">
            <!-- Status Messages -->
            <div x-data="{ show: false, message: '', type: 'success' }"
                 @show-message.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => show = false, 5000)"
                 x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="mb-6"
                 style="display: none;">
                <div :class="{
                    'bg-green-50 border-green-200': type === 'success',
                    'bg-red-50 border-red-200': type === 'error',
                    'bg-blue-50 border-blue-200': type === 'info'
                }" class="rounded-lg p-4 border">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg x-show="type === 'success'" class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <svg x-show="type === 'error'" class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <svg x-show="type === 'info'" class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p :class="{
                                'text-green-800': type === 'success',
                                'text-red-800': type === 'error',
                                'text-blue-800': type === 'info'
                            }" class="text-sm font-medium" x-text="message"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Camera Scanner -->
            <div x-data="qrScanner()" x-init="init()">
                <!-- Scanner Container -->
                <div class="relative bg-gray-900 rounded-lg overflow-hidden" style="aspect-ratio: 4/3;">
                    <div id="qr-reader" class="w-full h-full"></div>

                    <!-- Scanning Overlay -->
                    <div x-show="isScanning" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <div class="w-64 h-64 border-4 border-green-500 rounded-lg relative">
                            <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-green-500"></div>
                            <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-green-500"></div>
                            <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-green-500"></div>
                            <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-green-500"></div>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div x-show="!isScanning && !error" class="absolute inset-0 flex items-center justify-center bg-gray-900">
                        <div class="text-center">
                            <svg class="animate-spin h-12 w-12 text-green-500 mx-auto" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p class="mt-4 text-white text-sm">Initializing camera...</p>
                        </div>
                    </div>

                    <!-- Error State -->
                    <div x-show="error" class="absolute inset-0 flex items-center justify-center bg-gray-900">
                        <div class="text-center px-6">
                            <svg class="h-16 w-16 text-red-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="mt-4 text-white text-sm" x-text="error"></p>
                            <button @click="retry()" class="mt-4 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                Retry
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Scanner Controls -->
                <div class="mt-6 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <div class="flex h-3 w-3">
                            <span x-show="isScanning" class="animate-ping absolute inline-flex h-3 w-3 rounded-full bg-green-400 opacity-75"></span>
                            <span :class="isScanning ? 'bg-green-500' : 'bg-gray-400'" class="relative inline-flex rounded-full h-3 w-3"></span>
                        </div>
                        <span class="text-sm text-gray-600" x-text="isScanning ? 'Scanning...' : 'Not scanning'"></span>
                    </div>

                    <div class="flex items-center space-x-3">
                        <button @click="toggleCamera()"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span x-text="isScanning ? 'Stop Camera' : 'Start Camera'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="relative">
            <div class="absolute inset-0 flex items-center px-8">
                <div class="w-full border-t border-gray-200"></div>
            </div>
            <div class="relative flex justify-center">
                <span class="px-4 bg-white text-sm text-gray-500">Or</span>
            </div>
        </div>

        <!-- Manual Entry -->
        <div class="p-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Manual Entry</h3>
            <form action="{{ route('staff.attendance.manual') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="qr_code" class="block text-sm font-medium text-gray-700 mb-2">
                        Enter QR Code
                    </label>
                    <div class="flex space-x-3">
                        <input type="text"
                               name="qr_code"
                               id="qr_code"
                               required
                               class="flex-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500"
                               placeholder="Enter QR code manually">
                        <button type="submit"
                                class="inline-flex items-center px-6 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 transition-all">
                            Submit
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Instructions -->
    <div class="mt-8 rounded-xl bg-blue-50 border border-blue-200 p-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Instructions</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Allow camera access when prompted</li>
                        <li>Position the QR code within the frame</li>
                        <li>Hold steady until the code is scanned</li>
                        <li>The system will automatically process your attendance</li>
                        <li>If scanning fails, you can enter the code manually below</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
function qrScanner() {
    return {
        html5QrCode: null,
        isScanning: false,
        error: null,
        processing: false,

        init() {
            this.startScanner();
        },

        async startScanner() {
            try {
                this.error = null;
                this.html5QrCode = new Html5Qrcode("qr-reader");

                const config = {
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.333334
                };

                await this.html5QrCode.start(
                    { facingMode: "environment" },
                    config,
                    (decodedText, decodedResult) => {
                        this.onScanSuccess(decodedText);
                    },
                    (errorMessage) => {
                        // Ignore scan errors (when no QR code is detected)
                    }
                );

                this.isScanning = true;
            } catch (err) {
                console.error('Error starting scanner:', err);
                this.error = 'Unable to access camera. Please check permissions.';
                this.isScanning = false;
            }
        },

        async stopScanner() {
            if (this.html5QrCode) {
                try {
                    await this.html5QrCode.stop();
                    this.isScanning = false;
                } catch (err) {
                    console.error('Error stopping scanner:', err);
                }
            }
        },

        async toggleCamera() {
            if (this.isScanning) {
                await this.stopScanner();
            } else {
                await this.startScanner();
            }
        },

        async retry() {
            this.error = null;
            await this.startScanner();
        },

        async onScanSuccess(qrCode) {
            if (this.processing) return;

            this.processing = true;
            await this.stopScanner();

            try {
                const response = await fetch('{{ route('staff.attendance.process') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ qr_code: qrCode })
                });

                const data = await response.json();

                if (response.ok) {
                    window.dispatchEvent(new CustomEvent('show-message', {
                        detail: { message: data.message, type: 'success' }
                    }));

                    // Redirect after 2 seconds
                    setTimeout(() => {
                        window.location.href = '{{ route('staff.dashboard') }}';
                    }, 2000);
                } else {
                    window.dispatchEvent(new CustomEvent('show-message', {
                        detail: { message: data.message || 'Failed to process QR code', type: 'error' }
                    }));

                    // Restart scanner after 2 seconds
                    setTimeout(() => {
                        this.processing = false;
                        this.startScanner();
                    }, 2000);
                }
            } catch (error) {
                console.error('Error processing QR code:', error);
                window.dispatchEvent(new CustomEvent('show-message', {
                    detail: { message: 'An error occurred. Please try again.', type: 'error' }
                }));

                // Restart scanner after 2 seconds
                setTimeout(() => {
                    this.processing = false;
                    this.startScanner();
                }, 2000);
            }
        }
    };
}
</script>
@endpush
