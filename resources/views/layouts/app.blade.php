<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <style>
            [x-cloak] { display: none !important; }
        </style>
    <script>
        // On page load or when changing themes, best to add inline in `head` to avoid FOUC
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    </head>
    <body class="font-sans antialiased">
        <div x-data="{}" class="h-screen bg-milky-white dark:bg-gray-900 overflow-hidden overflow-x-hidden flex flex-col">
            <livewire:layout.navigation />

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col overflow-y-auto xl:ml-64 relative pt-16 xl:pt-0">
                @if(session()->has('impersonator_id'))
                    <div class="bg-amber-500 text-white px-6 py-2 flex justify-between items-center shadow-md relative z-[100] animate-pulse">
                        <div class="flex items-center gap-3 text-sm font-bold">
                            <svg class="w-5 h-5 font-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <span>Mode Impersonate: Sedang masuk sebagai <u>{{ auth()->user()->name }}</u></span>
                        </div>
                        <a href="{{ route('admin.leave-impersonation') }}" class="bg-white text-amber-600 px-4 py-1 rounded-full text-xs font-black uppercase hover:bg-amber-50 shadow-sm transition active:scale-95">
                            Kembali ke Akun Saya
                        </a>
                    </div>
                @endif
                
                <!-- Page Heading -->
                @if (isset($header))
                    <header>
                        <div class="max-w-screen-2xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                <!-- Page Content -->
                <main class="flex-1">
                    {{ $slot }}
                </main>
            </div>
        </div>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('theme', {
                    on: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),

                    toggle() {
                        this.on = !this.on;
                        if (this.on) {
                            document.documentElement.classList.add('dark');
                            localStorage.setItem('theme', 'dark');
                        } else {
                            document.documentElement.classList.remove('dark');
                            localStorage.setItem('theme', 'light');
                        }
                    }
                });

                Alpine.store('mobileNav', {
                    open: false,
                    toggle() {
                        this.open = !this.open;
                    },
                    close() {
                        this.open = false;
                    }
                });
            });
        </script>
    </body>
</html>
