{{-- Expense Transaction Detail --}}
<div class="space-y-4">
    {{-- Expense Info --}}
    <div class="grid grid-cols-2 gap-4 pb-4 border-b">
        <div>
            <label class="text-xs font-bold text-gray-500 uppercase">Kategori</label>
            <p class="text-sm font-bold text-gray-900">{{ $expense->category ?? 'Tanpa Kategori' }}</p>
        </div>
        <div>
            <label class="text-xs font-bold text-gray-500 uppercase">Tanggal</label>
            <p class="text-sm text-gray-900">{{ $expense->expense_date }}</p>
        </div>
        <div>
            <label class="text-xs font-bold text-gray-500 uppercase">Metode Pembayaran</label>
            <p class="text-sm text-gray-900">
                @if($expense->payment_method === 'cash')
                    Tunai
                @elseif($expense->payment_method === 'bank_transfer')
                    Transfer Bank
                @elseif($expense->payment_method === 'credit')
                    Kredit
                @else
                    {{ ucfirst($expense->payment_method) }}
                @endif
            </p>
        </div>
        <div>
            <label class="text-xs font-bold text-gray-500 uppercase">Jumlah</label>
            <p class="text-2xl font-bold text-red-600">Rp {{ number_format($expense->amount, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Description --}}
    <div>
        <label class="text-xs font-bold text-gray-600 uppercase block mb-2">Deskripsi</label>
        <p class="text-sm text-gray-900 p-4 bg-gray-50 rounded-lg">{{ $expense->description }}</p>
    </div>

    {{-- Notes --}}
    @if($expense->notes)
    <div>
        <label class="text-xs font-bold text-gray-600 uppercase block mb-2">Catatan</label>
        <p class="text-sm text-gray-700 p-4 bg-yellow-50 border border-yellow-200 rounded-lg italic">{{ $expense->notes }}</p>
    </div>
    @endif

    {{-- Reference Number (if exists) --}}
    @if(isset($expense->reference_number) && $expense->reference_number)
    <div class="p-4 bg-blue-50 rounded-lg">
        <label class="text-xs font-bold text-gray-600 uppercase">No. Referensi</label>
        <p class="text-sm font-mono text-blue-600 mt-1">{{ $expense->reference_number }}</p>
    </div>
    @endif

    {{-- Created By Info --}}
    <div class="pt-4 border-t flex justify-between items-center text-xs text-gray-500">
        <div>
            <span class="font-semibold">Dibuat oleh:</span> {{ $expense->user->name ?? 'System' }}
        </div>
        <div>
            {{ $expense->created_at->format('d/m/Y H:i') }}
        </div>
    </div>
</div>
