<table>
    <thead>
        <tr>
            <th colspan="7" style="text-align: center; font-size: 16px; font-weight: bold;">
                LAPORAN MARGIN PRODUK
            </th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: center; font-size: 12px;">
                Dicetak: {{ date('d F Y H:i') }}
            </th>
        </tr>
        <tr></tr>
        <tr>
            <th style="font-weight: bold; background-color: #f3f4f6;">No</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">Nama Produk</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">Barcode</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">Kategori</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">Harga Beli</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">Harga Jual</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">Margin (Rp)</th>
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
            <td style="text-align: right;">{{ $product->last_buy_price ? number_format($product->last_buy_price, 0, ',', '.') : '-' }}</td>
            <td style="text-align: right;">{{ number_format($product->sell_price, 0, ',', '.') }}</td>
            <td style="text-align: right;">{{ $product->last_buy_price ? number_format($product->margin_amount, 0, ',', '.') : '-' }}</td>
            <td style="text-align: right;">{{ $product->last_buy_price ? number_format($product->margin_percentage, 2, ',', '.') . '%' : '-' }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="8"></td>
        </tr>
        <tr>
            <td colspan="4" style="font-weight: bold;">Total Produk:</td>
            <td colspan="4">{{ $products->count() }}</td>
        </tr>
        <tr>
            <td colspan="4" style="font-weight: bold;">Produk dengan Margin Positif:</td>
            <td colspan="4">{{ $products->where('margin_amount', '>', 0)->count() }}</td>
        </tr>
        <tr>
            <td colspan="4" style="font-weight: bold;">Produk dengan Margin Negatif:</td>
            <td colspan="4">{{ $products->where('margin_amount', '<', 0)->count() }}</td>
        </tr>
        <tr>
            <td colspan="4" style="font-weight: bold;">Rata-rata Margin (%):</td>
            <td colspan="4">{{ number_format($products->where('last_buy_price', '>', 0)->avg('margin_percentage'), 2, ',', '.') }}%</td>
        </tr>
    </tfoot>
</table>
