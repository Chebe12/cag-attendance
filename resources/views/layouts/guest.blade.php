<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CA Global Attendance') }} - @yield('title', 'Login')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                        },
                        secondary: {
                            500: '#f59e0b',
                            600: '#d97706',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Custom animations -->
    <style>
        @keyframes blob {
            0% {
                transform: translate(0px, 0px) scale(1);
            }

            33% {
                transform: translate(30px, -50px) scale(1.1);
            }

            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }

            100% {
                transform: translate(0px, 0px) scale(1);
            }
        }

        .animate-blob {
            animation: blob 7s infinite;
        }

        .animation-delay-2000 {
            animation-delay: 2s;
        }

        .animation-delay-4000 {
            animation-delay: 4s;
        }
    </style>

    @stack('styles')
</head>

<body class="h-full">
    <div class="min-h-full flex">
        <!-- Left side - Branding -->
        <div
            class="hidden lg:flex lg:flex-1 lg:flex-col lg:justify-center lg:px-8 bg-gradient-to-br from-green-900 via-green-800 to-green-900 relative overflow-hidden">
            <!-- Animated background elements -->
            <div class="absolute inset-0 opacity-10">
                <div
                    class="absolute top-0 left-0 w-96 h-96 bg-orange-500 rounded-full mix-blend-multiply filter blur-3xl animate-blob">
                </div>
                <div
                    class="absolute top-0 right-0 w-96 h-96 bg-green-500 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-2000">
                </div>
                <div
                    class="absolute bottom-0 left-1/2 w-96 h-96 bg-orange-400 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-4000">
                </div>
            </div>

            <div class="relative z-10 max-w-md mx-auto text-center">
                <!-- Logo -->
                <div class="flex justify-center mb-8">
                    <div
                        class="w-24 h-24 bg-gradient-to-br from-green-400 to-orange-500 rounded-2xl flex items-center justify-center shadow-2xl transform hover:scale-105 transition-transform duration-300">
                        <span class="text-white font-bold text-4xl">CA</span>
                    </div>
                </div>

                <!-- Company name -->
                <h1 class="text-4xl font-bold text-white mb-4">CA Global</h1>
                <p class="text-xl text-green-100 mb-8">Attendance Management System</p>

                <!-- Features list -->
                <div class="space-y-4 text-left">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-700 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-200" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-white font-semibold">QR Code Check-in</h3>
                            <p class="text-green-200 text-sm">Quick and contactless attendance marking</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-700 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-200" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-white font-semibold">Real-time Tracking</h3>
                            <p class="text-green-200 text-sm">Monitor attendance in real-time</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-700 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-200" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-white font-semibold">Detailed Reports</h3>
                            <p class="text-green-200 text-sm">Comprehensive attendance analytics</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right side - Login form -->
        <div class="flex flex-1 flex-col justify-center py-12 px-4 sm:px-6 lg:px-20 xl:px-24 bg-gray-50">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                <!-- Mobile logo -->
                <div class="lg:hidden flex justify-center mb-8">
                    <div
                        class="w-20 h-20 bg-gradient-to-br from-green-400 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg">
                        <span class="text-white font-bold text-3xl">CA</span>
                    </div>
                </div>

                <div>
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900">
                        @yield('heading', 'Sign in to your account')
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        @yield('subheading', 'Enter your credentials to continue')
                    </p>
                </div>

                <!-- Flash messages -->
                @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)"
                    class="mt-6 rounded-lg bg-green-50 p-4 border border-green-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
                @endif

                @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)"
                    class="mt-6 rounded-lg bg-red-50 p-4 border border-red-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <div class="mt-8">
                    @yield('content')
                </div>

                <!-- Footer -->
                <div class="mt-8 text-center">
                    <p class="text-xs text-gray-500">
                        &copy; {{ date('Y') }} CA Global. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>

    @stack('scripts')

    <style>
        @keyframes blob {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            33% {
                transform: translate(30px, -50px) scale(1.1);
            }

            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }
        }

        .animate-blob {
            animation: blob 7s infinite;
        }

        .animation-delay-2000 {
            animation-delay: 2s;
        }

        .animation-delay-4000 {
            animation-delay: 4s;
        }
    </style>
</body>

</html>