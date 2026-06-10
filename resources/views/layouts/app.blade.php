<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Toko Meliana Jaya') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- PWA Setup -->
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#22c55e">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js').then(registration => {
                        console.log('SW registered: ', registration);
                    }).catch(registrationError => {
                        console.log('SW registration failed: ', registrationError);
                    });
                });
            }
        </script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="pb-20 md:pb-0">
                {{ $slot }}
            </main>

            <!-- Bottom Navigation for Mobile (PWA) -->
            <div class="md:hidden fixed bottom-0 left-0 z-50 w-full h-16 bg-white border-t border-gray-200 dark:bg-gray-700 dark:border-gray-600">
                <div class="grid h-full {{ auth()->user()->isAdmin() ? 'max-w-lg grid-cols-5' : 'max-w-sm grid-cols-2' }} mx-auto font-medium">
                    <a href="{{ route('dashboard') }}" class="inline-flex flex-col items-center justify-center px-2 hover:bg-gray-50 dark:hover:bg-gray-800 group {{ request()->routeIs('dashboard') ? 'text-brand-600' : 'text-gray-500' }}">
                        <svg class="w-6 h-6 mb-1 {{ request()->routeIs('dashboard') ? 'text-brand-600' : 'text-gray-500' }} group-hover:text-brand-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                        <span class="text-[10px] sm:text-xs text-center {{ request()->routeIs('dashboard') ? 'text-brand-600' : 'text-gray-500' }} group-hover:text-brand-600">Home</span>
                    </a>
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('products.index') }}" class="inline-flex flex-col items-center justify-center px-2 hover:bg-gray-50 dark:hover:bg-gray-800 group {{ request()->routeIs('products.*') ? 'text-brand-600' : 'text-gray-500' }}">
                        <svg class="w-6 h-6 mb-1 {{ request()->routeIs('products.*') ? 'text-brand-600' : 'text-gray-500' }} group-hover:text-brand-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path></svg>
                        <span class="text-[10px] sm:text-xs text-center {{ request()->routeIs('products.*') ? 'text-brand-600' : 'text-gray-500' }} group-hover:text-brand-600">Katalog</span>
                    </a>
                    @endif
                    <a href="{{ route('sales.create') }}" class="inline-flex flex-col items-center justify-center px-2 hover:bg-gray-50 dark:hover:bg-gray-800 group {{ request()->routeIs('sales.*') ? 'text-brand-600' : 'text-gray-500' }}">
                        <svg class="w-6 h-6 mb-1 {{ request()->routeIs('sales.*') ? 'text-brand-600' : 'text-gray-500' }} group-hover:text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <span class="text-[10px] sm:text-xs text-center {{ request()->routeIs('sales.*') ? 'text-brand-600' : 'text-gray-500' }} group-hover:text-brand-600">Jual</span>
                    </a>
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('stock.index') }}" class="inline-flex flex-col items-center justify-center px-2 hover:bg-gray-50 dark:hover:bg-gray-800 group {{ request()->routeIs('stock.*') ? 'text-brand-600' : 'text-gray-500' }}">
                        <svg class="w-6 h-6 mb-1 {{ request()->routeIs('stock.*') ? 'text-brand-600' : 'text-gray-500' }} group-hover:text-brand-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5 3a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2H5zm0 2h10v7h-2l-1 2H8l-1-2H5V5z" clip-rule="evenodd"></path></svg>
                        <span class="text-[10px] sm:text-xs text-center {{ request()->routeIs('stock.*') ? 'text-brand-600' : 'text-gray-500' }} group-hover:text-brand-600">Stok</span>
                    </a>
                    <a href="{{ route('reports.index') }}" class="inline-flex flex-col items-center justify-center px-2 hover:bg-gray-50 dark:hover:bg-gray-800 group {{ request()->routeIs('reports.*') ? 'text-brand-600' : 'text-gray-500' }}">
                        <svg class="w-6 h-6 mb-1 {{ request()->routeIs('reports.*') ? 'text-brand-600' : 'text-gray-500' }} group-hover:text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        <span class="text-[10px] sm:text-xs text-center {{ request()->routeIs('reports.*') ? 'text-brand-600' : 'text-gray-500' }} group-hover:text-brand-600">Laporan</span>
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @stack('scripts')
    </body>
</html>
