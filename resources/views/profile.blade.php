<x-app-layout>
    <div class="p-6">
        <!-- Header Section -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800">
                {{ __('Pengaturan Profil') }}
            </h2>
            <p class="text-sm text-gray-500 mt-1">Kelola informasi identitas akun dan keamanan kata sandi Anda.</p>
        </div>

        <div class="space-y-8">
            <!-- Profile Information Card -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="p-6 md:p-8">
                    <div class="flex items-center gap-3 mb-8 border-b border-gray-100 pb-4">
                        <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 uppercase tracking-tight">Informasi Profil</h3>
                            <p class="text-xs text-gray-500 italic">Perbarui nama lengkap dan alamat email aktif Anda.</p>
                        </div>
                    </div>
                    
                    <div class="max-w-2xl">
                        <livewire:profile.update-profile-information-form />
                    </div>
                </div>
            </div>

            <!-- Password Management Card -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="p-6 md:p-8">
                    <div class="flex items-center gap-3 mb-8 border-b border-gray-100 pb-4">
                        <div class="w-10 h-10 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 uppercase tracking-tight">Keamanan Akun</h3>
                            <p class="text-xs text-gray-500 italic">Gunakan kata sandi yang kuat untuk menjaga keamanan akses akun.</p>
                        </div>
                    </div>
                    
                    <div class="max-w-2xl">
                        <livewire:profile.update-password-form />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
