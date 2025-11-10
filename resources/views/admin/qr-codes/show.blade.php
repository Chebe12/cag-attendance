@extends('layouts.app')

@section('title', 'QR Code Details')

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
    <a href="{{ route('admin.qr-codes.index') }}" class="text-sm text-gray-500 hover:text-gray-700">QR Codes</a>
</li>
<li class="flex items-center">
    <svg class="h-5 w-5 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
    </svg>
    <span class="text-sm font-medium text-gray-900">{{ $qrCode->code }}</span>
</li>
@endsection

@section('content')
<!-- Page header -->
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">QR Code Details</h1>
        <p class="mt-2 text-sm text-gray-600">View and manage QR code information</p>
    </div>
    <div class="mt-4 sm:mt-0 flex items-center space-x-3">
        <a href="{{ route('admin.qr-codes.index') }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to List
        </a>
        <a href="{{ route('admin.qr-codes.download', $qrCode) }}"
           class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 transition-all">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Download QR Code
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- QR Code Display -->
    <div class="lg:col-span-1">
        <div class="rounded-xl bg-white shadow-sm border border-gray-100 overflow-hidden sticky top-6">
            <!-- QR Code Image -->
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-8 flex items-center justify-center">
                <div class="bg-white p-6 rounded-xl shadow-lg">
                    <img src="{{ $qrCode->image_url }}"
                         alt="QR Code"
                         class="h-64 w-64 object-contain">
                </div>
            </div>

            <!-- Status Badge -->
            <div class="p-6 border-t border-gray-200">
                <div class="text-center">
                    @if($qrCode->is_active && !$qrCode->is_expired)
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <div class="mr-2 flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                            </div>
                            Active
                        </span>
                    @elseif($qrCode->is_expired)
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Expired
                        </span>
                    @else
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            Inactive
                        </span>
                    @endif
                </div>

                <div class="mt-4 text-center">
                    <p class="text-2xl font-bold text-gray-900">{{ $qrCode->code }}</p>
                    <p class="text-sm text-gray-500 mt-1">QR Code</p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="p-6 bg-gray-50 border-t border-gray-200 space-y-2">
                <button onclick="window.print()"
                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print QR Code
                </button>

                @if($qrCode->is_active)
                    <form action="{{ route('admin.qr-codes.deactivate', $qrCode) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-red-600 hover:bg-red-700 transition-colors">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                            Deactivate
                        </button>
                    </form>
                @else
                    <form action="{{ route('admin.qr-codes.activate', $qrCode) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-green-600 hover:bg-green-700 transition-colors">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Activate
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- QR Code Information -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Basic Information -->
        <div class="rounded-xl bg-white shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Basic Information</h2>
            <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">QR Code</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono bg-gray-50 px-3 py-2 rounded-lg">{{ $qrCode->code }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Client</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $qrCode->client->name ?? 'No Client Assigned' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Created Date</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($qrCode->created_at)->format('M d, Y g:i A') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Valid Until</dt>
                    <dd class="mt-1">
                        <span class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($qrCode->valid_until)->format('M d, Y g:i A') }}</span>
                        <span class="ml-2 text-xs text-gray-500">({{ \Carbon\Carbon::parse($qrCode->valid_until)->diffForHumans() }})</span>
                    </dd>
                </div>
                @if($qrCode->client)
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Client Address</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $qrCode->client->address }}</dd>
                </div>
                @endif
            </dl>
        </div>

        <!-- Usage Statistics -->
        <div class="rounded-xl bg-white shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Usage Statistics</h2>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3 mb-6">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-500">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-blue-900">Total Scans</p>
                            <p class="text-2xl font-bold text-blue-900">{{ $qrCode->usage_count ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-500">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-green-900">Unique Users</p>
                            <p class="text-2xl font-bold text-green-900">{{ $qrCode->unique_users_count ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg p-4 border border-orange-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-orange-500">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-orange-900">Last Used</p>
                            <p class="text-sm font-bold text-orange-900">
                                {{ $qrCode->last_used_at ? \Carbon\Carbon::parse($qrCode->last_used_at)->diffForHumans() : 'Never' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usage Chart Placeholder -->
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-900 mb-4">Usage Over Time</h3>
                <div class="h-48 bg-gray-50 rounded-lg border border-gray-200 flex items-center justify-center">
                    <canvas id="usageChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="rounded-xl bg-white shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Recent Activity</h2>

            <div class="flow-root">
                <ul role="list" class="-mb-8">
                    @forelse($recentActivity ?? [] as $index => $activity)
                    <li>
                        <div class="relative pb-8">
                            @if(!$loop->last)
                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                            @endif
                            <div class="relative flex space-x-3">
                                <div>
                                    <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </span>
                                </div>
                                <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                    <div>
                                        <p class="text-sm text-gray-900">
                                            <span class="font-medium">{{ $activity->user->name }}</span>
                                            scanned QR code for
                                            <span class="font-medium">{{ $activity->type }}</span>
                                        </p>
                                        <p class="mt-0.5 text-xs text-gray-500">{{ $activity->client->name ?? 'N/A' }}</p>
                                    </div>
                                    <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                        <time datetime="{{ $activity->created_at }}">
                                            {{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}
                                        </time>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">No activity recorded yet</p>
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('usageChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($usageData['labels'] ?? ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']) !!},
                datasets: [{
                    label: 'Scans',
                    data: {!! json_encode($usageData['data'] ?? [2, 5, 3, 7, 4, 6, 8]) !!},
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgb(34, 197, 94)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        borderRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            precision: 0
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
});
</script>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    .lg\:col-span-1, .lg\:col-span-1 * {
        visibility: visible;
    }
    .lg\:col-span-1 {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
    }
}
</style>
@endpush
