@props([
    'colspan' => 1,
    'message' => 'Data Tidak Ditemukan',
    'subheader' => 'Silakan sesuaikan filter Anda atau tambah data baru.',
    'icon' => 'search' // 'search', 'box', 'document'
])

<tr>
    <td colspan="{{ $colspan }}" class="px-6 py-12 text-center">
        <div class="flex flex-col items-center justify-center">
            @if ($icon === 'search')
                <div class="rounded-full bg-gray-50 dark:bg-gray-800 p-3 mb-3">
                    <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            @elseif ($icon === 'box')
                <div class="rounded-full bg-gray-50 dark:bg-gray-800 p-3 mb-3">
                    <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            @else
                <div class="rounded-full bg-gray-50 dark:bg-gray-800 p-3 mb-3">
                    <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            @endif
            
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $message }}</h3>
            @if($subheader)
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 max-w-xs mx-auto">{{ $subheader }}</p>
            @endif
        </div>
    </td>
</tr>
