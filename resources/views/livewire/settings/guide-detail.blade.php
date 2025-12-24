<div class="min-h-screen bg-gray-50 py-6">
    <!-- Simple Header -->
    <div class="bg-white border-b border-gray-200 mb-6">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('guide.index') }}" class="text-gray-600 hover:text-gray-900">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <h1 class="text-xl font-bold text-gray-900">{{ $guide['title'] }}</h1>
                </div>
                <button onclick="window.print()" 
                        class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Cetak PDF
                </button>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-12 gap-6">
            
            <!-- Sidebar Navigation -->
            <div class="col-span-3">
                <div class="bg-white border border-gray-200 rounded-lg p-4 sticky top-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Navigasi</h3>
                    <nav class="space-y-1">
                        <a href="#overview" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">Ringkasan</a>
                        <a href="#procedures" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">Prosedur</a>
                        <a href="#forms" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">Formulir</a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-span-6 space-y-6">
                
                <!-- Summary -->
                <section id="overview">
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-2">{{ $guide['title'] }}</h2>
                        <p class="text-gray-600">{{ $guide['description'] }}</p>
                    </div>
                </section>

                <!-- Golden Rules -->
                @if(!empty($guide['golden_rules']))
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-5">
                    <h3 class="text-sm font-semibold text-blue-900 mb-3">✓ Aturan Penting</h3>
                    <ol class="space-y-2">
                        @foreach($guide['golden_rules'] as $index => $rule)
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold">{{ $index + 1 }}</span>
                            <span class="text-sm text-gray-700 leading-relaxed">{!! str_replace(['**', '##'], ['<strong>', '</strong>'], $rule) !!}</span>
                        </li>
                        @endforeach
                    </ol>
                </div>
                @endif

                <!-- Procedures -->
                @if(!empty($guide['procedures']))
                <section id="procedures">
                    <h3 class="text-base font-bold text-gray-900 mb-4">Prosedur</h3>
                    <div class="space-y-3">
                        @foreach($guide['procedures'] as $index => $proc)
                        <div class="bg-white border border-gray-200 rounded-lg p-5">
                            <div class="flex gap-4">
                                <span class="flex-shrink-0 w-8 h-8 bg-gray-100 text-gray-700 rounded-full flex items-center justify-center text-sm font-bold">{{ $index + 1 }}</span>
                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-1">{{ $proc['title'] }}</h4>
                                    <p class="text-sm text-gray-600">{!! str_replace(['**', '##'], ['<strong>', '</strong>'], $proc['desc']) !!}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </section>
                @endif

                <!-- Form Fields -->
                @if(!empty($guide['form_fields']))
                <section id="forms">
                    <h3 class="text-base font-bold text-gray-900 mb-4">Formulir</h3>
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Field</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Keterangan</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($guide['form_fields'] as $field)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $field['name'] }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $field['description'] }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @if($field['required'])
                                            <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-medium rounded">Wajib</span>
                                        @else
                                            <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded">Opsional</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
                @endif
            </div>

            <!-- Right Sidebar -->
            <div class="col-span-3 space-y-4">
                
                <!-- Preview -->
                @if(file_exists(public_path('images/guide/' . $guide['image'])))
                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                    <img src="{{ asset('images/guide/' . $guide['image']) }}" class="w-full">
                </div>
                @endif

                <!-- Buttons -->
                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">Tombol</h4>
                    <div class="space-y-2">
                        @foreach($guide['buttons'] as $btn)
                        <div class="text-sm">
                            <div class="font-medium text-gray-900">{{ $btn['label'] }}</div>
                            <div class="text-gray-600 text-xs">{{ $btn['func'] }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Sub Menus -->
                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">Sub-Menu</h4>
                    <div class="space-y-2">
                        @foreach($guide['sub_menus'] as $sub)
                        <div class="text-sm">
                            <div class="font-medium text-gray-900">{{ $sub['name'] }}</div>
                            <div class="text-gray-600 text-xs">{{ $sub['func'] }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        @media print {
            .sticky { position: relative !important; }
            button { display: none !important; }
        }
    </style>
    @endpush
</div>
