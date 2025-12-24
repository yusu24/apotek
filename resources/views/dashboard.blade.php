<x-app-layout>
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
</x-app-layout>
