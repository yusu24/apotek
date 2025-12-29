<x-app-layout>
    @can('view dashboard')
    <div class="p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">
                {{ __('Dashboard') }}
            </h2>
        </div>

        <div class="space-y-8">
            <!-- Dashboard Welcome Card -->
            <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl shadow-xl overflow-hidden text-white relative">
                {{-- SVG Decoration Removed --}}
                <div class="p-6 md:p-10 flex flex-col gap-4">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-extrabold mb-2 tracking-tight">Halo, {{ auth()->user()->name }}!</h1>
                        <p class="text-blue-100 text-sm md:text-lg opacity-90">Selamat datang kembali di <span class="font-bold border-b-2 border-blue-400">Apotek.POS</span>. Pantau performa stok Anda hari ini.</p>
                    </div>
                    <div class="flex gap-4">
                        {{-- Buka Kasir Button Removed as per User Request --}}
                    </div>
                </div>
            </div>

            <!-- Performance Components -->
            <livewire:dashboard.product-performance />
        </div>
    </div>
    @else
    <div class="min-h-[60vh] flex items-center justify-center">
        <div class="text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Akses Terbatas</h3>
            <p class="mt-1 text-sm text-gray-500">Anda tidak memiliki izin untuk melihat dashboard.</p>
        </div>
    </div>
    @endcan
</x-app-layout>
