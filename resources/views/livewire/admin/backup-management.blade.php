<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Pencadangan Data (Backup)</h2>
        <button wire:click="createBackup"
                wire:loading.attr="disabled"
                class="btn btn-primary flex items-center gap-2 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
            <span>Buat Backup Sekarang</span>
        </button>
    </div>

    <!-- Alerts -->

    <!-- Backups Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Nama File</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Ukuran</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Tanggal Dibuat</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm">
                    @forelse ($backups as $backup)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded bg-blue-50 text-blue-600 flex items-center justify-center mr-3 shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                                    </div>
                                    <span class="font-bold text-gray-800 truncate max-w-xs">{{ $backup['name'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center font-medium text-gray-600">
                                {{ $backup['size'] }}
                            </td>
                            <td class="px-6 py-4 text-center text-gray-500">
                                {{ $backup['date'] }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="download('{{ $backup['path'] }}')" 
                                            class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                            title="Unduh">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    </button>
                                    <button onclick="confirm('Apakah Anda yakin ingin menghapus backup ini?') || event.stopImmediatePropagation()"
                                            wire:click="delete('{{ $backup['path'] }}')" 
                                            class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <x-empty-table colspan="4" message="Belum ada pencadangan data." />
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer Info -->
    <div class="mt-6 flex items-start gap-3 p-4 bg-gray-50 border border-gray-100 rounded-xl">
        <svg class="w-5 h-5 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <div class="text-xs text-gray-500 leading-relaxed">
            <p class="font-bold text-gray-700 mb-1">Informasi Penting:</p>
            <ul class="list-disc list-inside space-y-1">
                <li>Pencadangan dilakukan secara otomatis setiap hari pada pukul **01:00 pagi**.</li>
                <li>Sistem menyimpan riwayat selama **30 hari terakhir**.</li>
                <li>Disarankan mengunduh backup secara berkala dan menyimpannya di luar server.</li>
            </ul>
        </div>
    </div>
</div>
