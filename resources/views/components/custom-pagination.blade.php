{{-- Custom Pagination Component --}}
{{-- Usage: @include('components.custom-pagination', ['items' => $paginatedData]) --}}

@if ($items->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between border-t border-gray-200 pt-4">
        {{-- Mobile view - simpler --}}
        <div class="flex flex-1 justify-between sm:hidden">
            @if ($items->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-not-allowed rounded-md">
                    Previous
                </span>
            @else
                <a href="{{ $items->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition">
                    Previous
                </a>
            @endif

            @if ($items->hasMorePages())
                <a href="{{ $items->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition">
                    Next
                </a>
            @else
                <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-not-allowed rounded-md">
                    Next
                </span>
            @endif
        </div>

        {{-- Desktop view --}}
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Showing
                    <span class="font-medium">{{ $items->firstItem() ?? 0 }}</span>
                    to
                    <span class="font-medium">{{ $items->lastItem() ?? 0 }}</span>
                    of
                    <span class="font-medium">{{ $items->total() }}</span>
                    results
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex shadow-sm rounded-md">
                    {{-- Previous Button --}}
                    @if ($items->onFirstPage())
                        <span class="relative inline-flex items-center px-3 py-2 rounded-l-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $items->previousPageUrl() }}" class="relative inline-flex items-center px-3 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                    @endif

                    {{-- Page Numbers (Max 5) --}}
                    @php
                        $currentPage = $items->currentPage();
                        $lastPage = $items->lastPage();
                        
                        // Calculate the range to display (max 5 pages)
                        $start = max(1, min($currentPage - 2, $lastPage - 4));
                        $end = min($lastPage, $start + 4);
                        
                        // Adjust start if we're near the end
                        if ($end - $start < 4) {
                            $start = max(1, $end - 4);
                        }
                    @endphp

                    @for ($page = $start; $page <= $end; $page++)
                        @if ($page == $currentPage)
                            <span class="relative inline-flex items-center px-4 py-2 -ml-px border border-gray-300 bg-blue-600 text-sm font-bold text-white">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $items->url($page) }}" class="relative inline-flex items-center px-4 py-2 -ml-px border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                                {{ $page }}
                            </a>
                        @endif
                    @endfor

                    {{-- Next Button --}}
                    @if ($items->hasMorePages())
                        <a href="{{ $items->nextPageUrl() }}" class="relative inline-flex items-center px-3 py-2 -ml-px rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @else
                        <span class="relative inline-flex items-center px-3 py-2 -ml-px rounded-r-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
