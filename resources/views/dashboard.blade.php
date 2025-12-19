<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto space-y-8">
        <!-- Dashboard Welcome Card -->
        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl shadow-xl overflow-hidden text-white relative">
            <div class="absolute top-0 right-0 p-8 py-10 opacity-10">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.82v-1.91c-.5-.07-1.01-.21-1.48-.41L9.67 17.1c.52.23 1.07.36 1.63.39v-2.09c-.77-.07-1.52-.31-2.18-.7l.56-1.1c.54.32 1.14.51 1.62.58V12.1c-.88-.13-1.74-.47-2.48-1.02l.66-1.09c.63.46 1.35.75 1.82.85V8.85c-.4-.05-.82-.14-1.22-.27L9 7.41c.45.18.91.29 1.38.33V6h2.82v1.76c.49.06.98.17 1.45.34l-.53 1.1c-.43-.16-.88-.26-1.34-.31L13 11c.91.13 1.8.49 2.56 1.06l-.72 1.07c-.63-.48-1.35-.79-1.84-.88v2.1c.5.06 1.01.18 1.48.37l-.41 1.15c-.4-.17-.83-.28-1.38-.34v1.56z"></path></svg>
            </div>
            <div class="p-8 md:p-10 flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <h1 class="text-3xl font-extrabold mb-2 tracking-tight">Halo, {{ auth()->user()->name }}!</h1>
                    <p class="text-blue-100 text-lg opacity-90">Selamat datang kembali di <span class="font-bold border-b-2 border-blue-400">Apotek.POS</span>. Pantau performa stok Anda hari ini.</p>
                </div>
                <div class="flex gap-4">
                    {{-- Buka Kasir Button Removed as per User Request --}}
                </div>
            </div>
        </div>

        <!-- Performance Components -->
        <livewire:dashboard.product-performance />
    </div>
</x-app-layout>
