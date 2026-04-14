<table>
    <thead>
        <tr>
            <th colspan="{{ $reportMode === 'realized' ? '9' : '8' }}" style="text-align: center; font-size: 16px; font-weight: bold;">
                LAPORAN {{ $reportMode === 'realized' ? 'REALISASI' : '' }} MARGIN PRODUK
            </th>
        </tr>
        <tr>
            <th colspan="{{ $reportMode === 'realized' ? '9' : '8' }}" style="text-align: center; font-size: 12px;">
                @if($reportMode === 'realized')
                    Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                @else
                    Tipe: Potensi Margin (Stok Saat Ini) - Dicetak: {{ date('d/m/Y H:i') }}
                @endif
            </th>
        </tr>
        <tr></tr>
        <tr>
            <th style="font-weight: bold; background-color: #f3f4f6;">No</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">Nama Produk</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">Barcode</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">Kategori</th>
            @if($reportMode === 'realized')
                <th style="font-weight: bold; background-color: #f3f4f6; text-align: center;">Qty Terjual</th>
            @endif
            <th style="font-weight: bold; background-color: #f3f4f6;">{{ $reportMode === 'realized' ? 'Harga Beli Rerata' : 'Harga Beli' }}</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">{{ $reportMode === 'realized' ? 'Harga Jual Rerata' : 'Harga Jual' }}</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">{{ $reportMode === 'realized' ? 'Total Margin' : 'Margin' }} (Rp)</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">Margin (%)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $index => $product)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $product->name }}</td>
            <td>{{ $product->barcode }}</td>
            <td>{{ $product->category->name ?? '-' }}</td>
            @if($reportMode === 'realized')
                <td style="text-align: center;">{{ $product->total_sold }}</td>
            @endif
            <td style="text-align: right;">{{ $product->avg_buy_price ?? $product->last_buy_price }}</td>
            <td style="text-align: right;">{{ $product->avg_sell_price ?? $product->sell_price }}</td>
            <td style="text-align: right; font-weight: bold;">{{ $product->margin_amount }}</td>
            <td style="text-align: right;">{{ round($product->margin_percentage, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="{{ $reportMode === 'realized' ? '9' : '8' }}"></td>
        </tr>
        <tr>
            <td colspan="4" style="font-weight: bold;">Tipe Laporan:</td>
            <td colspan="4">{{ $reportMode === 'realized' ? 'Realisasi Penjualan' : 'Potensi Stok' }}</td>
        </tr>
        <tr>
            <td colspan="4" style="font-weight: bold;">Total Produk:</td>
            <td colspan="4">{{ $products->count() }}</td>
        </tr>
        <tr>
            <td colspan="4" style="font-weight: bold;">Total {{ $reportMode === 'realized' ? 'Keuntungan' : 'Margin' }}:</td>
            <td colspan="4" style="font-weight: bold;">{{ $products->sum('margin_amount') }}</td>
        </tr>
        <tr>
            <td colspan="4" style="font-weight: bold;">Rata-rata Margin (%):</td>
            <td colspan="4">{{ round($products->count() > 0 ? $products->avg('margin_percentage') : 0, 2) }}</td>
        </tr>
    </tfoot>
</table>
