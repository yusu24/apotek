@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between py-3">
        <!-- Mobile View (Prev/Next buttons only for small screens) -->
        <div class="flex justify-between flex-1 sm:hidden gap-3">
            @if ($paginator->onFirstPage())
                <span class="px-4 py-2 text-sm font-bold text-gray-400 bg-gray-50 border border-gray-200 rounded-lg cursor-not-allowed">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" wire:navigate class="px-4 py-2 text-sm font-bold text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition active:scale-95 shadow-sm">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" wire:navigate class="px-4 py-2 text-sm font-bold text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition active:scale-95 shadow-sm">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span class="px-4 py-2 text-sm font-bold text-gray-400 bg-gray-50 border border-gray-200 rounded-lg cursor-not-allowed">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        <!-- Desktop View -->
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-sm text-gray-500 font-medium">
                    Menampilkan <span class="font-bold text-gray-800">{{ $paginator->firstItem() ?? 0 }}</span> - <span class="font-bold text-gray-800">{{ $paginator->lastItem() ?? 0 }}</span> dari <span class="font-bold text-gray-800">{{ $paginator->total() }}</span> data
                </p>
            </div>

            <div class="flex items-center gap-1.5">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span class="p-2 text-gray-300 border border-transparent cursor-not-allowed">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" wire:navigate class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition active:scale-90" aria-label="{{ __('pagination.previous') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="px-2 text-gray-400 font-bold">{{ $element }}</span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="min-w-[40px] h-10 flex items-center justify-center text-sm font-bold bg-gray-800 text-white rounded-xl shadow-md z-10">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" wire:navigate class="min-w-[40px] h-10 flex items-center justify-center text-sm font-bold text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-xl transition active:scale-95" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" wire:navigate class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition active:scale-90" aria-label="{{ __('pagination.next') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @else
                    <span class="p-2 text-gray-300 border border-transparent cursor-not-allowed">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif
