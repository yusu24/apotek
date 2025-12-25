<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">User Guides</h2>
            <p class="text-sm text-gray-500 mt-1">Browse documentation, access detailed guides, and download PDF resources.</p>
        </div>
    </div>

    <!-- Main Content -->
    <!-- Main Content -->
    <div>
        @if(count($guides) > 0)
            <!-- Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($guides as $guide)
                    <div class="bg-white border border-gray-200 rounded-2xl p-8 flex flex-col h-full shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                        <!-- Content -->
                        <div class="flex-1 mb-8">
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold uppercase tracking-wider border border-blue-100">
                                    {{ $guide['category'] }}
                                </span>
                            </div>
                            <h3 class="text-gray-900 font-bold text-2xl leading-snug mb-3 group-hover:text-blue-700 transition-colors">{{ $guide['title'] }}</h3>
                            
                            <div class="flex items-center text-xs text-gray-400 mt-4 pt-4 border-t border-gray-100 uppercase tracking-wide font-medium">
                                <svg class="w-1 h-1 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>Updated {{ $guide['updated_at'] }}</span>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="grid grid-cols-2 gap-4">
                            <!-- View Button -->
                            <a href="{{ route('guide.detail', $guide['slug']) }}" 
                               class="h-12 flex items-center justify-center px-6 bg-blue-600 text-white text-sm font-bold rounded-xl hover:bg-blue-700 transition-colors shadow-md shadow-blue-200 gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                View
                            </a>

                            <!-- PDF Button -->
                            <a href="{{ route('guide.detail', $guide['slug']) }}?print=true" 
                               target="_blank"
                               class="h-12 flex items-center justify-center px-6 bg-red-600 text-white text-sm font-bold rounded-xl hover:bg-red-700 transition-colors shadow-md shadow-red-200 gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                PDF
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-gray-50 border border-gray-200 rounded-2xl p-20 text-center text-gray-500">
                <p class="text-sm">No user guides found.</p>
            </div>
        @endif
    </div>

    <!-- Complete Handbook Section (Bottom) -->
    <!-- Complete Handbook Section (Bottom) -->
    <div class="mt-8">
        <div class="bg-white border border-gray-200 rounded-xl p-6 flex flex-col md:flex-row items-center justify-between gap-6 shadow-sm overflow-hidden relative">
            <!-- Background Decoration -->
            <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-gray-100 rounded-full opacity-50 blur-3xl"></div>
            
            <div class="flex items-start gap-4 relative z-10">
                <div class="p-3 bg-blue-50 rounded-lg border border-blue-100 shadow-sm">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800 mb-1">Complete User Handbook</h3>
                    <p class="text-gray-500 text-sm max-w-xl leading-relaxed">Download the comprehensive documentation containing all user guides, procedures, and best practices in a single printable PDF file.</p>
                </div>
            </div>
            <a href="{{ route('guide.handbook') }}?print=true" target="_blank"
               class="h-10 px-6 bg-blue-600 text-white text-sm font-bold rounded-lg flex items-center gap-2 hover:bg-blue-500 shadow-lg shadow-blue-900/50 transition-all hover:-translate-y-1 relative z-10 whitespace-nowrap">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Download All (.PDF)
            </a>
        </div>
    </div>
</div>
