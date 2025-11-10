@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Mark Attendance</h1>
        <p class="mt-2 text-sm text-gray-600">Select your location type and scan QR code to check-in or check-out</p>
    </div>

    <!-- Today's Schedules Overview -->
    @if($todaySchedules && $todaySchedules->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Today's Schedule</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($todaySchedules as $schedule)
                    @php
                        $attendance = $todayAttendances->where('schedule_id', $schedule->id)->first();
                        $isCheckedIn = $attendance && $attendance->check_in && !$attendance->check_out;
                        $isCompleted = $attendance && $attendance->check_out;
                    @endphp
                    <div class="border rounded-lg p-4 {{ $isCompleted ? 'bg-green-50 border-green-200' : ($isCheckedIn ? 'bg-blue-50 border-blue-200' : 'border-gray-200') }}">
                        <div class="flex items-start justify-between mb-2">
                            <h4 class="font-semibold text-gray-900">{{ $schedule->client->name }}</h4>
                            @if($isCompleted)
                                <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">Completed</span>
                            @elseif($isCheckedIn)
                                <span class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full">Checked In</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded-full">Pending</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600">
                            {{ date('g:i A', strtotime($schedule->start_time)) }} - {{ date('g:i A', strtotime($schedule->end_time)) }}
                        </p>
                        @if($attendance)
                            <div class="mt-2 text-xs text-gray-500">
                                @if($attendance->check_in)
                                    <p>In: {{ $attendance->check_in->format('g:i A') }}</p>
                                @endif
                                @if($attendance->check_out)
                                    <p>Out: {{ $attendance->check_out->format('g:i A') }}</p>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Scanner Section -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden" x-data="attendanceScanner()">
        <!-- Location Type Selection -->
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Select Attendance Type</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Office Attendance -->
                <button @click="setAttendanceType('office', null)"
                        :class="attendanceType === 'office' ? 'border-green-500 bg-green-50' : 'border-gray-300'"
                        class="border-2 rounded-lg p-4 text-left transition-all hover:border-green-400">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="font-semibold text-gray-900">Office Day</h4>
                            <p class="text-sm text-gray-600">Working from office today</p>
                        </div>
                    </div>
                </button>

                <!-- Client Visit -->
                <button @click="showClientSelection = !showClientSelection"
                        :class="attendanceType === 'client_visit' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'"
                        class="border-2 rounded-lg p-4 text-left transition-all hover:border-blue-400">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="font-semibold text-gray-900">Client Visit</h4>
                            <p class="text-sm text-gray-600">Visiting a client location</p>
                        </div>
                    </div>
                </button>
            </div>

            <!-- Client Selection (shown when client_visit is selected) -->
            <div x-show="showClientSelection" x-transition class="mt-4" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Client</label>
                <select x-model="selectedScheduleId" @change="setAttendanceType('client_visit', $event.target.value)"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    <option value="">-- Select Client --</option>
                    @if($todaySchedules && $todaySchedules->count() > 0)
                        @foreach($todaySchedules as $schedule)
                            <option value="{{ $schedule->id }}">
                                {{ $schedule->client->name }} ({{ date('g:i A', strtotime($schedule->start_time)) }})
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>

        <!-- Status Message -->
        <div x-show="message" x-transition class="p-4" :class="messageType === 'success' ? 'bg-green-50' : 'bg-red-50'" style="display: none;">
            <p :class="messageType === 'success' ? 'text-green-800' : 'text-red-800'" x-text="message"></p>
        </div>

        <!-- QR Scanner -->
        <div class="p-6">
            <div x-show="attendanceType" style="display: none;">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Scan QR Code</h3>
                <div class="bg-gray-900 rounded-lg overflow-hidden" style="aspect-ratio: 4/3;">
                    <div id="qr-reader" class="w-full h-full"></div>
                </div>
                <div class="mt-4 text-center">
                    <button @click="toggleScanner()"
                            :class="isScanning ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'"
                            class="px-6 py-2 text-white rounded-lg transition-colors">
                        <span x-text="isScanning ? 'Stop Scanner' : 'Start Scanner'"></span>
                    </button>
                </div>
            </div>

            <div x-show="!attendanceType" class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
                <p class="mt-2 text-gray-600">Please select attendance type above to continue</p>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
function attendanceScanner() {
    return {
        attendanceType: '',
        selectedScheduleId: null,
        showClientSelection: false,
        html5QrCode: null,
        isScanning: false,
        message: '',
        messageType: 'success',
        processing: false,

        setAttendanceType(type, scheduleId) {
            this.attendanceType = type;
            this.selectedScheduleId = scheduleId;

            if (type === 'client_visit' && !scheduleId) {
                this.showClientSelection = true;
            } else {
                this.showClientSelection = false;
                if (this.isScanning) {
                    this.stopScanner();
                    setTimeout(() => this.startScanner(), 100);
                }
            }
        },

        async startScanner() {
            if (!this.attendanceType) return;
            if (this.attendanceType === 'client_visit' && !this.selectedScheduleId) {
                this.showMessage('Please select a client first', 'error');
                return;
            }

            try {
                this.html5QrCode = new Html5Qrcode("qr-reader");

                await this.html5QrCode.start(
                    { facingMode: "environment" },
                    { fps: 10, qrbox: { width: 250, height: 250 } },
                    (decodedText) => this.onScanSuccess(decodedText),
                    () => {}
                );

                this.isScanning = true;
            } catch (err) {
                console.error('Scanner error:', err);
                this.showMessage('Unable to access camera', 'error');
            }
        },

        async stopScanner() {
            if (this.html5QrCode && this.isScanning) {
                try {
                    await this.html5QrCode.stop();
                    this.isScanning = false;
                } catch (err) {
                    console.error('Stop error:', err);
                }
            }
        },

        async toggleScanner() {
            if (this.isScanning) {
                await this.stopScanner();
            } else {
                await this.startScanner();
            }
        },

        async onScanSuccess(qrCode) {
            if (this.processing) return;

            this.processing = true;
            await this.stopScanner();

            try {
                const response = await fetch('{{ route('staff.attendance.scan') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        qr_code: qrCode,
                        attendance_type: this.attendanceType,
                        schedule_id: this.selectedScheduleId
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    this.showMessage(data.message, 'success');
                    setTimeout(() => window.location.href = '{{ route('staff.dashboard') }}', 2000);
                } else {
                    this.showMessage(data.message || 'Failed to process QR code', 'error');
                    setTimeout(() => {
                        this.processing = false;
                        this.startScanner();
                    }, 2000);
                }
            } catch (err) {
                console.error('Process error:', err);
                this.showMessage('Network error. Please try again.', 'error');
                setTimeout(() => {
                    this.processing = false;
                    this.startScanner();
                }, 2000);
            }
        },

        showMessage(msg, type) {
            this.message = msg;
            this.messageType = type;
            setTimeout(() => this.message = '', 5000);
        }
    }
}
</script>
@endsection
