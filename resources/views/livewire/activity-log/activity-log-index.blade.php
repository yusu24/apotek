<div class="p-6" wire:poll.2s.visible>
    <!-- Header -->
    <div class="mb-6 flex justify-between items-start">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Riwayat Aktivitas User
            </h2>
            <p class="text-sm text-gray-500 mt-1">Tracking semua aktivitas user di sistem</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 px-3 py-1 bg-green-50 text-green-700 rounded-full text-xs font-semibold border border-green-200">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                </span>
                Live Updates
            </div>
            <span class="text-xs text-gray-400">Terakhir diperbarui: {{ $lastUpdated }}</span>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Card 1: Blue -->
        <div class="bg-blue-700 text-white p-4 rounded-lg shadow-md">
            <div class="flex items-center gap-3 mb-2">
                <div class="bg-white/25 p-2 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-white text-xs font-semibold uppercase">HARI INI</p>
                    <p class="text-2xl font-bold">{{ number_format($stats['total_today']) }}</p>
                </div>
            </div>
        </div>

        <!-- Card 2: Green -->
        <div class="bg-green-700 text-white p-4 rounded-lg shadow-md">
            <div class="flex items-center gap-3 mb-2">
                <div class="bg-white/25 p-2 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <p class="text-white text-xs font-semibold uppercase">MINGGU INI</p>
                    <p class="text-2xl font-bold">{{ number_format($stats['total_week']) }}</p>
                </div>
            </div>
        </div>

        <!-- Card 3: Purple -->
        <div class="bg-purple-700 text-white p-4 rounded-lg shadow-md">
            <div class="flex items-center gap-3 mb-2">
                <div class="bg-white/25 p-2 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
                <div>
                    <p class="text-white text-xs font-semibold uppercase">BULAN INI</p>
                    <p class="text-2xl font-bold">{{ number_format($stats['total_month']) }}</p>
                </div>
            </div>
        </div>

        <!-- Card 4: Red -->
        <div class="bg-red-700 text-white p-4 rounded-lg shadow-md">
            <div class="flex items-center gap-3 mb-2">
                <div class="bg-white/25 p-2 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <div>
                    <p class="text-white text-xs font-semibold uppercase">USER AKTIF</p>
                    <p class="text-2xl font-bold">{{ number_format($stats['unique_users_today']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 no-print mb-6">
        <!-- Row 1: Main Filters -->
        <div class="flex flex-wrap md:flex-nowrap gap-4 md:gap-6 items-center">
            <!-- Search (Order 1) -->
            <div class="flex items-center gap-2 flex-1 md:flex-none order-1">
                <label class="hidden md:inline text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Cari</label>
                <input type="text" wire:model.live.debounce.300ms="search" 
                    placeholder="Deskripsi/modul..." 
                    class="w-full md:w-64 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm py-2 px-3 focus:ring-2 focus:ring-blue-500 transition-all">
            </div>

            <!-- Filter User (Order 2) -->
            <div class="flex items-center gap-2 flex-1 md:flex-none order-2">
                <label class="hidden md:inline text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">User</label>
                <select wire:model.live="filterUser" class="w-full md:w-40 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm py-2 pl-3 !pr-12 focus:ring-2 focus:ring-blue-500 transition-all">
                    <option value="">Semua User</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Module (Order 3) -->
            <div class="flex items-center gap-2 flex-1 md:flex-none order-3">
                <label class="hidden md:inline text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Modul</label>
                <select wire:model.live="filterModule" class="w-full md:w-40 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm py-2 pl-3 !pr-12 focus:ring-2 focus:ring-blue-500 transition-all">
                    <option value="">Semua Modul</option>
                    @foreach($modules as $module)
                        <option value="{{ $module }}">{{ ucfirst($module) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Action (Order 4) -->
            <div class="flex items-center gap-2 flex-1 md:flex-none order-4">
                <label class="hidden md:inline text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Aksi</label>
                <select wire:model.live="filterAction" class="w-full md:w-40 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm py-2 pl-3 !pr-12 focus:ring-2 focus:ring-blue-500 transition-all">
                    <option value="">Semua Aksi</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}">{{ ucfirst($action) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Reset (Order 5) -->
            <div class="order-5 md:ml-auto">
                <button wire:click="clearFilters" 
                    class="px-3 md:px-5 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 shadow-sm font-bold text-sm flex items-center justify-center gap-2 transition duration-200" title="Reset semua filter">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span class="hidden md:inline">Reset Filter</span>
                </button>
            </div>
        </div>

        <!-- Row 2: Date Filters -->
        <div class="flex flex-wrap md:flex-nowrap gap-4 md:gap-6 items-center mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-2 w-full md:w-auto">
                <label class="hidden md:inline text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Periode</label>
                <div class="flex items-center gap-1.5 flex-1 md:flex-none">
                    <x-date-picker wire:model.live="filterDateFrom" class="flex-1 md:w-40 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm py-2 px-3 uppercase focus:ring-2 focus:ring-blue-500 transition-all"></x-date-picker>
                    <span class="text-gray-400 font-bold">-</span>
                    <x-date-picker wire:model.live="filterDateTo" class="flex-1 md:w-40 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm py-2 px-3 uppercase focus:ring-2 focus:ring-blue-500 transition-all"></x-date-picker>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Timeline -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Timeline Aktivitas
        </h3>

        <div class="space-y-4">
            @forelse($logs as $log)
                <div wire:key="log-{{ $log->id }}" class="relative pl-8 pb-4 border-l-2 
                    @if($log->action_color === 'green') border-green-500
                    @elseif($log->action_color === 'yellow') border-yellow-500
                    @elseif($log->action_color === 'red') border-red-500
                    @elseif($log->action_color === 'blue') border-blue-500
                    @elseif($log->action_color === 'purple') border-purple-500
                    @else border-gray-500
                    @endif
                    last:border-0 last:pb-0">
                    
                    <!-- Timeline Dot -->
                    <div class="absolute -left-3 top-0 w-6 h-6 rounded-full flex items-center justify-center text-xs
                        @if($log->action_color === 'green') bg-green-500
                        @elseif($log->action_color === 'yellow') bg-yellow-500
                        @elseif($log->action_color === 'red') bg-red-500
                        @elseif($log->action_color === 'blue') bg-blue-500
                        @elseif($log->action_color === 'purple') bg-purple-500
                        @else bg-gray-500
                        @endif
                        text-white shadow-lg">
                        {{ $log->action_icon }}
                    </div>

                    <!-- Activity Card -->
                    <div class="bg-gray-50 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <!-- Header -->
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="px-2 py-1 rounded-full text-xs font-bold
                                        @if($log->action_color === 'green') bg-green-100 text-green-800
                                        @elseif($log->action_color === 'yellow') bg-yellow-100 text-yellow-800
                                        @elseif($log->action_color === 'red') bg-red-100 text-red-800
                                        @elseif($log->action_color === 'blue') bg-blue-100 text-blue-800
                                        @elseif($log->action_color === 'purple') bg-purple-100 text-purple-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ strtoupper($log->action) }}
                                    </span>
                                    <span class="px-2 py-1 bg-gray-200 text-gray-700 rounded-full text-xs font-semibold">
                                        {{ ucfirst($log->module) }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ $log->created_at->diffForHumans() }}
                                    </span>
                                </div>

                                <!-- User Info -->
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                        {{ substr(optional($log->user)->name ?? 'S', 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 text-sm">{{ optional($log->user)->name ?? 'System / Deleted User' }}</p>
                                        <p class="text-xs text-gray-500">{{ optional($log->user)->email ?? '-' }}</p>
                                    </div>
                                </div>

                                <!-- Description -->
                                <p class="text-sm text-gray-700 mb-2">{{ $log->description }}</p>

                                <!-- Meta Info -->
                                <div class="flex flex-wrap gap-3 text-xs text-gray-500">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                                    </span>
                                    @if($log->ip_address)
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                                            {{ $log->ip_address }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
@empty
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <p class="text-gray-500 font-medium">Tidak ada aktivitas ditemukan</p>
                    <p class="text-sm text-gray-400 mt-1">Coba ubah filter pencarian Anda</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $logs->links('components.custom-pagination', ['items' => $logs]) }}
        </div>
    </div>
</div>
