{{-- Modern Pagination Component --}}
{{-- Usage: @include('components.modern-pagination', ['items' => $paginatedData]) --}}

@php
    $showPerPage = $showPerPage ?? isset($perPage);
@endphp

@if ($items->hasPages() || ($showPerPage && $items->total() > 0))
    <div class="w-full flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pt-4">

        {{-- Result Info --}}
        <p class="text-xs text-gray-400 shrink-0">
            @if($items->hasPages())
                Menampilkan <span class="font-semibold text-gray-600">{{ $items->firstItem() }}</span>&ndash;<span class="font-semibold text-gray-600">{{ $items->lastItem() }}</span> dari <span class="font-semibold text-gray-600">{{ number_format($items->total()) }}</span> data
            @else
                Menampilkan <span class="font-semibold text-gray-600">{{ number_format($items->total()) }}</span> data
            @endif
        </p>

        <div class="flex items-center gap-2">

            {{-- PerPage Select --}}
            @if($showPerPage)
                <select wire:model.live="perPage"
                    class="h-9 border-0 bg-gray-100 rounded-xl py-0 pl-3 pr-8 text-xs text-gray-600 font-medium cursor-pointer focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            @endif

            {{-- Pagination Buttons --}}
            @if($items->hasPages())
            <div class="flex items-center gap-1">

                {{-- Previous Button --}}
                @if ($items->onFirstPage())
                    <span class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-100 text-gray-300 cursor-not-allowed select-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </span>
                @else
                    <a href="{{ $items->previousPageUrl() }}"
                        wire:click.prevent="previousPage('{{ $pageName ?? 'page' }}')"
                        wire:loading.attr="disabled"
                        class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-800 transition-all duration-150 cursor-pointer group">
                        <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform duration-150" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                @endif

                {{-- Page Numbers (Max 5 visible) --}}
                @php
                    $currentPage = $items->currentPage();
                    $lastPage    = $items->lastPage();

                    // Compute a window of up to 5 pages centred on current
                    $start = max(1, min($currentPage - 2, $lastPage - 4));
                    $end   = min($lastPage, $start + 4);

                    if (($end - $start) < 4) {
                        $start = max(1, $end - 4);
                    }
                @endphp

                @for ($page = $start; $page <= $end; $page++)
                    @if ($page == $currentPage)
                        <span class="w-9 h-9 flex items-center justify-center rounded-xl bg-blue-600 text-white text-sm font-bold shadow-md shadow-blue-200 select-none">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $items->url($page) }}"
                            wire:click.prevent="gotoPage({{ $page }}, '{{ $pageName ?? 'page' }}')"
                            wire:loading.attr="disabled"
                            class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-900 text-sm font-medium transition-all duration-150 cursor-pointer">
                            {{ $page }}
                        </a>
                    @endif
                @endfor

                {{-- Next Button --}}
                @if ($items->hasMorePages())
                    <a href="{{ $items->nextPageUrl() }}"
                        wire:click.prevent="nextPage('{{ $pageName ?? 'page' }}')"
                        wire:loading.attr="disabled"
                        class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-800 transition-all duration-150 cursor-pointer group">
                        <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform duration-150" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @else
                    <span class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-100 text-gray-300 cursor-not-allowed select-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                @endif

            </div>
            @endif
        </div>
    </div>
@endif
