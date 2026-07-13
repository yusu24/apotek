<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ \Illuminate\Support\Str::title(\App\Models\Setting::get('store_name') ?? config('app.name', 'Apotek')) }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css2?family=Figtree:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 dark:text-slate-100 antialiased selection:bg-blue-500 selection:text-white">
        <x-loading-bar />
        <x-toast />
        <div class="min-h-screen flex flex-col justify-center items-center px-4 sm:px-6 lg:px-8 py-12 bg-gradient-to-tr from-slate-100 via-white to-blue-50 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 transition-colors duration-300">
            <div class="w-full sm:max-w-md">
                <!-- Logo Container -->
                <div class="flex justify-center mb-6">
                    <div class="focus:outline-none select-none">
                        @if($logoPath = \App\Models\Setting::get('store_login_logo_path'))
                            <img src="{{ asset('storage/' . $logoPath) }}" class="h-32 sm:h-40 md:h-48 w-auto object-contain mx-auto filter drop-shadow-md" alt="Logo">
                        @else
                            <x-application-logo class="w-28 h-28 sm:w-36 sm:h-36 md:w-44 md:h-44 fill-current text-blue-600 dark:text-blue-500 filter drop-shadow-lg" />
                        @endif
                    </div>
                </div>

                <!-- Main Card Slot Container -->
                <div class="w-full bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200/50 dark:border-slate-800/50 shadow-2xl rounded-2xl px-6 py-8 sm:px-10 sm:py-10 transition-all duration-300">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
