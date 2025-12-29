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

    <!-- Statistics Cards (Forced Style V2) -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
        <!-- Card 1: Blue -->
        <div style="background-color: #1d4ed8; color: white; padding: 1rem; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
            <div class="flex items-center gap-3 mb-2">
                <div style="background-color: rgba(255, 255, 255, 0.25); padding: 0.5rem; border-radius: 0.5rem;">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p style="color: white; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">HARI INI</p>
                    <p style="font-size: 1.5rem; font-weight: 700;">{{ number_format($stats['total_today']) }}</p>
                </div>
            </div>
        </div>

        <!-- Card 2: Green -->
        <div style="background-color: #15803d; color: white; padding: 1rem; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
            <div class="flex items-center gap-3 mb-2">
                <div style="background-color: rgba(255, 255, 255, 0.25); padding: 0.5rem; border-radius: 0.5rem;">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <p style="color: white; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">MINGGU INI</p>
                    <p style="font-size: 1.5rem; font-weight: 700;">{{ number_format($stats['total_week']) }}</p>
                </div>
            </div>
        </div>

        <!-- Card 3: Purple -->
        <div style="background-color: #7e22ce; color: white; padding: 1rem; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
            <div class="flex items-center gap-3 mb-2">
                <div style="background-color: rgba(255, 255, 255, 0.25); padding: 0.5rem; border-radius: 0.5rem;">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
                <div>
                    <p style="color: white; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">BULAN INI</p>
                    <p style="font-size: 1.5rem; font-weight: 700;">{{ number_format($stats['total_month']) }}</p>
                </div>
            </div>
        </div>

        <!-- Card 4: Red -->
        <div style="background-color: #b91c1c; color: white; padding: 1rem; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
            <div class="flex items-center gap-3 mb-2">
                <div style="background-color: rgba(255, 255, 255, 0.25); padding: 0.5rem; border-radius: 0.5rem;">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <div>
                    <p style="color: white; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">USER AKTIF</p>
                    <p style="font-size: 1.5rem; font-weight: 700;">{{ number_format($stats['unique_users_today']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search" 
                        placeholder="Cari deskripsi atau modul..." 
                        class="w-full pl-10 pr-4 py-2 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Filter User -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
                <select wire:model.live="filterUser" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">Semua User</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Module -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Modul</label>
                <select wire:model.live="filterModule" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">Semua Modul</option>
                    @foreach($modules as $module)
                        <option value="{{ $module }}">{{ ucfirst($module) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Action -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Aksi</label>
                <select wire:model.live="filterAction" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">Semua Aksi</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}">{{ ucfirst($action) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Clear Filters -->
            <div class="flex items-end">
                <button wire:click="clearFilters" 
                    class="w-full px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition text-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Reset
                </button>
            </div>
        </div>

        <!-- Date Range -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                <input type="date" wire:model.live="filterDateFrom" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                <input type="date" wire:model.live="filterDateTo" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
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
                                        {{ substr($log->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 text-sm">{{ $log->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $log->user->email }}</p>
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
            @include('components.custom-pagination', ['items' => $logs])
        </div>
    </div>
</div>
