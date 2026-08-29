@php
    $showPerPage = $showPerPage ?? isset($perPage);
@endphp

@if ($items->hasPages() || ($showPerPage && $items->total() > 0))
    <div class="w-full flex items-center justify-between gap-2 pt-3 flex-nowrap overflow-x-auto">

        {{-- Sisi Kiri: Info Data Sangat Ringkas --}}
        <p class="text-xs text-gray-500 whitespace-nowrap shrink-0">
            @if($items->hasPages())
                <span class="font-semibold text-gray-700">{{ $items->firstItem() }}&ndash;{{ $items->lastItem() }}</span> / <span class="font-semibold text-gray-700">{{ number_format($items->total()) }}</span> data
            @else
                <span class="font-semibold text-gray-700">{{ number_format($items->total()) }}</span> data
            @endif
        </p>

        {{-- Sisi Kanan: Dropdown PerPage & Tombol Navigasi (1 Baris) --}}
        <div class="flex items-center gap-1.5 shrink-0 flex-nowrap">

            {{-- Dropdown PerPage (Hanya tampil di tablet/desktop, disembunyikan di HP) --}}
            @if($showPerPage)
                <select wire:model.live="perPage"
                    class="hidden sm:block h-7 sm:h-8 border-0 bg-gray-100 rounded-lg py-0 pl-2 pr-6 text-xs text-gray-600 font-medium cursor-pointer focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all shrink-0">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            @endif

            {{-- Tombol Navigasi Halaman --}}
            @if($items->hasPages())
            <div class="flex items-center gap-1 shrink-0 flex-nowrap">

                {{-- Previous Button --}}
                @if ($items->onFirstPage())
                    <span class="w-7 h-7 sm:w-8 sm:h-8 md:w-9 md:h-9 flex items-center justify-center rounded-lg bg-gray-100 text-gray-300 cursor-not-allowed select-none shrink-0">
                        <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </span>
                @else
                    <a href="{{ $items->previousPageUrl() }}"
                        wire:click.prevent="previousPage('{{ $pageName ?? 'page' }}')"
                        wire:loading.attr="disabled"
                        class="w-7 h-7 sm:w-8 sm:h-8 md:w-9 md:h-9 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-800 transition-all duration-150 cursor-pointer group shrink-0">
                        <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 group-hover:-translate-x-0.5 transition-transform duration-150" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                @endif

                {{-- Page Numbers (Max 5 di Desktop, Max 3 di Mobile) --}}
                @php
                    $currentPage = $items->currentPage();
                    $lastPage    = $items->lastPage();

                    // Desktop window: maksimal 5 halaman
                    $startDesk = max(1, min($currentPage - 2, $lastPage - 4));
                    $endDesk   = min($lastPage, $startDesk + 4);
                    if (($endDesk - $startDesk) < 4) {
                        $startDesk = max(1, $endDesk - 4);
                    }

                    // Mobile window: maksimal 3 halaman
                    $startMob = max(1, min($currentPage - 1, $lastPage - 2));
                    $endMob   = min($lastPage, $startMob + 2);
                    if (($endMob - $startMob) < 2) {
                        $startMob = max(1, $endMob - 2);
                    }
                @endphp

                @for ($page = $startDesk; $page <= $endDesk; $page++)
                    @php
                        $isMobileVisible = ($page >= $startMob && $page <= $endMob);
                        $displayClass = $isMobileVisible ? 'flex' : 'hidden sm:flex';
                    @endphp

                    @if ($page == $currentPage)
                        <span class="w-7 h-7 sm:w-8 sm:h-8 md:w-9 md:h-9 {{ $displayClass }} items-center justify-center rounded-lg bg-blue-600 text-white text-xs sm:text-sm font-bold shadow-sm select-none shrink-0">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $items->url($page) }}"
                            wire:click.prevent="gotoPage({{ $page }}, '{{ $pageName ?? 'page' }}')"
                            wire:loading.attr="disabled"
                            class="w-7 h-7 sm:w-8 sm:h-8 md:w-9 md:h-9 {{ $displayClass }} items-center justify-center rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-900 text-xs sm:text-sm font-medium transition-all duration-150 cursor-pointer shrink-0">
                            {{ $page }}
                        </a>
                    @endif
                @endfor

                {{-- Next Button --}}
                @if ($items->hasMorePages())
                    <a href="{{ $items->nextPageUrl() }}"
                        wire:click.prevent="nextPage('{{ $pageName ?? 'page' }}')"
                        wire:loading.attr="disabled"
                        class="w-7 h-7 sm:w-8 sm:h-8 md:w-9 md:h-9 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-800 transition-all duration-150 cursor-pointer group shrink-0">
                        <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 group-hover:translate-x-0.5 transition-transform duration-150" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @else
                    <span class="w-7 h-7 sm:w-8 sm:h-8 md:w-9 md:h-9 flex items-center justify-center rounded-lg bg-gray-100 text-gray-300 cursor-not-allowed select-none shrink-0">
                        <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                @endif

            </div>
            @endif
        </div>
    </div>
@endif
